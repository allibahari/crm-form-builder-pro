<?php
// File: includes/frontend/enqueue.php

if (!defined('ABSPATH')) exit;

/**
 * اسکریپت‌ها و استایل‌های بخش کاربری (سایت اصلی) را فراخوانی می‌کند.
 */
function crm_connector_enqueue_scripts() {
    // فراخوانی Tailwind CSS از CDN برای استایل فرم‌ها
    wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com', [], null, false);
}
add_action('wp_enqueue_scripts', 'crm_connector_enqueue_scripts');

/**
 * اسکریپت‌ها و استایل‌های بخش مدیریت (پیشخوان) را فراخوانی می‌کند.
 */
function crm_connector_admin_enqueue_scripts($hook) {
    // بارگذاری Tailwind در تمام صفحات پیشخوان برای نمایش صحیح پیغام‌ها
    wp_enqueue_script('tailwindcss-admin', 'https://cdn.tailwindcss.com', [], null, false);

    global $post_type;

    // فقط در صفحه "افزودن فرم" یا "ویرایش فرم"، فایل‌های مربوط به فرم‌ساز را بارگذاری کن
    if (('post.php' == $hook || 'post-new.php' == $hook) && isset($post_type) && $post_type === 'crm_form') {
        // بارگذاری فایل جاوااسکریپت فرم‌ساز
        wp_enqueue_script('crm-form-builder', CRM_CONNECTOR_URL . 'assets/js/form-builder-admin.js', [], '1.0', true);
        
        // بارگذاری فایل استایل فرم‌ساز
        wp_enqueue_style('crm-admin-style', CRM_CONNECTOR_URL . 'assets/css/admin-style.css');
    }

    // فقط در صفحه تنظیمات پلاگین، اسکریپت مربوط به آن را بارگذاری کن
    if (isset($_GET['page']) && $_GET['page'] === 'crm-connector-settings') {
        wp_enqueue_script('crm-settings-admin', CRM_CONNECTOR_URL . 'assets/js/settings-admin.js', [], '1.o', true);
        
        // ارسال اطلاعات لازم از PHP به جاوااسکریپت
        wp_localize_script('crm-settings-admin', 'crm_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('crm_test_connection_nonce')
        ]);
    }
}
add_action('admin_enqueue_scripts', 'crm_connector_admin_enqueue_scripts');