<?php
function crm_connector_send_to_api($data_to_send) {
    $options = get_option('crm_connector_options');
    $crm_api_url = $options['api_url'] ?? '';
    if (empty($crm_api_url)) return false;
    
    $domain = preg_replace('/^www\./', '', wp_parse_url(site_url(), PHP_URL_HOST));
    $data_to_send['origin_domain'] = $domain;

    $args = [
        'method'  => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($data_to_send),
        'timeout' => 15,
    ];
    
    $response = wp_remote_post($crm_api_url, $args);

    if (is_wp_error($response)) return false;

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code === 401 || $response_code === 403) {
        // این تابع دیگر پلاگین را غیرفعال نمی‌کند، فقط وضعیت را تغییر می‌دهد
        delete_option('crm_connection_status');
        return false;
    }
    return ($response_code >= 200 && $response_code < 300);
}

function crm_handle_ajax_test_connection() {
    check_ajax_referer('crm_test_connection_nonce');

    $api_url = isset($_POST['api_url']) ? esc_url_raw($_POST['api_url']) : '';
    if (empty($api_url)) {
        wp_send_json_error(['message' => 'آدرس API نمی‌تواند خالی باشد.']);
    }

    $domain = preg_replace('/^www\./', '', wp_parse_url(site_url(), PHP_URL_HOST));
    $response = wp_remote_post($api_url, [
        'method' => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['origin_domain' => $domain, 'action' => 'verify']),
        'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'خطا در اتصال: ' . $response->get_error_message()]);
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code >= 200 && $response_code < 300) {
        update_option('crm_connection_status', 'verified');
        wp_send_json_success(['message' => 'اتصال با موفقیت برقرار شد.']);
    } elseif ($response_code === 401 || $response_code === 403) {
        delete_option('crm_connection_status');
        wp_send_json_error(['message' => 'دامنه شما توسط سرور تایید نشد. دسترسی غیرمجاز.']);
    } else {
        delete_option('crm_connection_status');
        wp_send_json_error(['message' => 'سرور پاسخ نامعتبر داد. کد خطا: ' . $response_code]);
    }
}
add_action('wp_ajax_test_crm_connection', 'crm_handle_ajax_test_connection');