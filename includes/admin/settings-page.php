<?php
// File: includes/admin/settings-page.php

if (!defined('ABSPATH')) exit;

/**
 * Registers all settings for the plugin.
 */
function crm_connector_settings_init() {
    register_setting('crm_connector_settings_group', 'crm_connector_options');
    
    // Section 1: License and API Connection
    add_settings_section(
        'crm_connector_license_section', 
        'اتصال و فعال‌سازی لایسنس', 
        null, 
        'crm-connector-settings'
    );
    add_settings_field('license_key', 'کلید لایسنس', 'crm_connector_license_key_render', 'crm-connector-settings', 'crm_connector_license_section');
    add_settings_field('api_url', 'آدرس API تایید', 'crm_connector_api_url_render', 'crm-connector-settings', 'crm_connector_license_section');

    // Section 2: Google reCAPTCHA Settings
    add_settings_section(
        'crm_connector_recaptcha_section', 
        'تنظیمات Google reCAPTCHA (v2)', 
        'crm_connector_recaptcha_section_callback', // A helper text for the section
        'crm-connector-settings'
    );
    add_settings_field('recaptcha_site_key', 'Site Key', 'crm_connector_recaptcha_site_key_render', 'crm-connector-settings', 'crm_connector_recaptcha_section');
    add_settings_field('recaptcha_secret_key', 'Secret Key', 'crm_connector_recaptcha_secret_key_render', 'crm-connector-settings', 'crm_connector_recaptcha_section');
}
add_action('admin_init', 'crm_connector_settings_init');

// --- Render functions for License Section ---
function crm_connector_license_key_render() { 
    $options = get_option('crm_connector_options'); 
    printf('<input type="text" name="crm_connector_options[license_key]" id="crm_license_key_field" value="%s" class="regular-text" style="direction: ltr;" placeholder="XXXX-XXXX-XXXX-XXXX">', esc_attr($options['license_key'] ?? '')); 
}
function crm_connector_api_url_render() { 
    $options = get_option('crm_connector_options'); 
    printf('<input type="url" name="crm_connector_options[api_url]" id="crm_api_url_field" value="%s" class="regular-text" style="direction: ltr;" placeholder="https://api.your-crm.com/v1/verify">', esc_attr($options['api_url'] ?? '')); 
}

// --- Render functions for reCAPTCHA Section ---
function crm_connector_recaptcha_section_callback() {
    echo '<p>کلیدهای سایت و کلید مخفی خود را برای reCAPTCHA v2 ("I\'m not a robot") از <a href="https://www.google.com/recaptcha/admin" target="_blank">پنل مدیریت گوگل</a> دریافت کرده و در اینجا وارد کنید.</p>';
}
function crm_connector_recaptcha_site_key_render() { 
    $options = get_option('crm_connector_options'); 
    printf('<input type="text" name="crm_connector_options[recaptcha_site_key]" value="%s" class="regular-text" style="direction: ltr;">', esc_attr($options['recaptcha_site_key'] ?? '')); 
}
function crm_connector_recaptcha_secret_key_render() { 
    $options = get_option('crm_connector_options'); 
    printf('<input type="text" name="crm_connector_options[recaptcha_secret_key]" value="%s" class="regular-text" style="direction: ltr;">', esc_attr($options['recaptcha_secret_key'] ?? '')); 
}


/**
 * Renders the complete settings page HTML.
 */
function crm_connector_render_settings_page() {
    $status = get_option('crm_connection_status');
    ?>
    <style>/* Styles from previous steps */</style>
    <div class="wrap">
        <h1>تنظیمات پلاگین فرم ساز CRM</h1>
        
        <form action='options.php' method='post'>
            <?php 
            settings_fields('crm_connector_settings_group'); 
            // This function will now render both the license and reCAPTCHA sections
            do_settings_sections('crm-connector-settings'); 
            submit_button('ذخیره تنظیمات'); 
            ?>
        </form>
        
        <hr>
        <h2>بررسی و فعال‌سازی لایسنس</h2>
        <div id="connection-status-display" class="connection-status-box <?php echo ($status === 'verified') ? 'status-connected' : 'status-disconnected'; ?>">
            <strong>وضعیت: 
            <?php if ($status === 'verified') : ?>
                <span style="color: green;">✔</span> فعال و متصل
            <?php else : ?>
                <span style="color: red;">✖</span> غیرفعال</strong><p>لطفاً کلید لایسنس و آدرس API را وارد کرده و اتصال را تایید کنید.</p>
            <?php endif; ?>
        </div>
        <button type="button" id="test-connection-btn" class="button button-primary button-large">تست و فعال‌سازی لایسنس</button>
        <div id="connection-result"></div>
    </div>
    <?php
}