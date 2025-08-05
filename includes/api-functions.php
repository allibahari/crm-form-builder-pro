<?php
// File: includes/api-functions.php

if (!defined('ABSPATH')) exit;

/**
 * داده‌های نهایی فرم را به همراه کلید لایسنس به صورت JSON به API ارسال می‌کند.
 */
function crm_connector_send_to_api($data_to_send) {
    $options = get_option('crm_connector_options');
    $crm_api_url = $options['api_url'] ?? '';
    $license_key = $options['license_key'] ?? '';

    if (empty($crm_api_url) || empty($license_key)) {
        return false;
    }
    
    $domain = preg_replace('/^www\./', '', wp_parse_url(site_url(), PHP_URL_HOST));
    $data_to_send['origin_domain'] = $domain;
    $data_to_send['license_key'] = $license_key;

    $args = [
        'method'  => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode($data_to_send),
        'timeout' => 15,
    ];
    
    $response = wp_remote_post($crm_api_url, $args);

    if (is_wp_error($response)) {
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code === 401 || $response_code === 403) {
        delete_option('crm_connection_status');
        return false;
    }
    return ($response_code >= 200 && $response_code < 300);
}

/**
 * درخواست AJAX برای تست اتصال و فعال‌سازی لایسنس را مدیریت می‌کند.
 */
function crm_handle_ajax_test_connection() {
    check_ajax_referer('crm_test_connection_nonce');

    $api_url = isset($_POST['api_url']) ? esc_url_raw($_POST['api_url']) : '';
    $license_key = isset($_POST['license_key']) ? sanitize_text_field(trim($_POST['license_key'])) : '';

    // *** تغییر اصلی و رفع باگ در این بخش است ***
    // بررسی می‌کند که آیا فیلدها خالی هستند یا خیر
    if (empty($api_url) || empty($license_key)) {
        wp_send_json_error(['message' => 'آدرس API و کلید لایسنس نمی‌توانند خالی باشند.']);
        // از ادامه اجرای تابع جلوگیری می‌کند
        die();
    }

    $domain = preg_replace('/^www\./', '', wp_parse_url(site_url(), PHP_URL_HOST));
    
    $response = wp_remote_post($api_url, [
        'method' => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
            'origin_domain' => $domain,
            'license_key'   => $license_key,
            'action'        => 'verify'
        ]),
        'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'خطا در اتصال: ' . $response->get_error_message()]);
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code >= 200 && $response_code < 300) {
        update_option('crm_connection_status', 'verified');
        wp_send_json_success(['message' => 'لایسنس با موفقیت تایید و فعال شد.']);
    } elseif ($response_code === 401 || $response_code === 403) {
        delete_option('crm_connection_status');
        wp_send_json_error(['message' => 'لایسنس نامعتبر است یا با این دامنه مطابقت ندارد.']);
    } else {
        delete_option('crm_connection_status');
        wp_send_json_error(['message' => 'سرور پاسخ نامعتبر داد. کد خطا: ' . $response_code]);
    }
}
add_action('wp_ajax_test_crm_connection', 'crm_handle_ajax_test_connection');


// =========================================================================
//  شبیه‌ساز API موقت برای تست
// =========================================================================
add_action('init', 'crm_connector_mock_api_handler');

function crm_connector_mock_api_handler() {
    if (!isset($_GET['mock_api'])) {
        return;
    }
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);
    if (isset($data['action']) && $data['action'] === 'verify') {
        $license_key = isset($data['license_key']) ? $data['license_key'] : '';
        $valid_license = 'VALID-LICENSE-KEY-12345';
        if ($license_key === $valid_license) {
            wp_send_json(['status' => 'success', 'message' => 'لایسنس توسط سرور تست تایید شد.'], 200);
        } else {
            wp_send_json(['status' => 'error', 'message' => 'لایسنس نامعتبر است.'], 403);
        }
    }
    if (isset($data['license_key'])) {
         wp_send_json(['status' => 'success', 'message' => 'داده توسط سرور تست دریافت شد.'], 200);
    }
    wp_send_json(['status' => 'error', 'message' => 'درخواست نامعتبر به سرور تست.'], 400);
    die();
}