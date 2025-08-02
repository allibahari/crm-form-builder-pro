<?php
function crm_connector_settings_init() {
    register_setting('crm_connector_options', 'crm_connector_options');
    add_settings_section('crm_connector_main_section', 'اتصال به سرور CRM', null, 'crm-connector-settings');
    add_settings_field('api_url', 'آدرس API تایید', 'crm_connector_api_url_render', 'crm-connector-settings', 'crm_connector_main_section');
}
add_action('admin_init', 'crm_connector_settings_init');

function crm_connector_api_url_render() { 
    $options = get_option('crm_connector_options'); 
    printf('<input type="url" name="crm_connector_options[api_url]" id="crm_api_url_field" value="%s" class="regular-text" style="direction: ltr;" placeholder="https://api.your-crm.com/v1/verify">', esc_attr($options['api_url'] ?? '')); 
}

function crm_connector_render_settings_page() {
    $status = get_option('crm_connection_status');
    ?>
    <style>.connection-status-box{padding:20px;border-width:2px;border-style:solid;border-radius:5px;margin:20px 0;max-width:600px;}.status-connected{border-color:#28a745;background-color:#d4edda;color:#155724}.status-disconnected{border-color:#dc3545;background-color:#f8d7da;color:#721c24}#connection-result{margin-top:15px;font-weight:700}</style>
    <div class="wrap">
        <h1>اتصال به CRM</h1>
        <div id="connection-status-display" class="connection-status-box <?php echo ($status === 'verified') ? 'status-connected' : 'status-disconnected'; ?>">
            <?php if ($status === 'verified') : ?>
                <strong>وضعیت: <span style="color: green;">✔</span> متصل</strong>
            <?php else : ?>
                <strong>وضعیت: <span style="color: red;">✖</span> قطع</strong><p>لطفاً آدرس API را وارد کرده و اتصال را تست کنید.</p>
            <?php endif; ?>
        </div>
        <form action='options.php' method='post'>
            <?php settings_fields('crm_connector_settings_group'); do_settings_fields('crm-connector-settings', 'crm_connector_main_section'); submit_button('ذخیره آدرس'); ?>
        </form>
        <hr>
        <h2>بررسی اتصال</h2>
        <button type="button" id="test-connection-btn" class="button button-primary button-large">تست و تایید اتصال</button>
        <div id="connection-result"></div>
    </div>
    <?php
}