<?php
// File: includes/admin/menu.php

if (!defined('ABSPATH')) exit;

/**
 * ریدایرکت پس از فعال‌سازی را مدیریت می‌کند
 */
function crm_connector_admin_init_redirect() {
    if (get_transient('crm_activation_redirect')) {
        delete_transient('crm_activation_redirect');
        if (!isset($_GET['activate-multi'])) {
            wp_safe_redirect(admin_url('admin.php?page=crm-connector-settings'));
            exit;
        }
    }
}
add_action('admin_init', 'crm_connector_admin_init_redirect');

/**
 * منوهای پلاگین را بدون هیچ محدودیتی ایجاد می‌کند.
 */
function crm_connector_create_menus() {
    // ایجاد منوی اصلی که به صفحه "همه فرم‌ها" لینک می‌دهد
    add_menu_page(
        'فرم ساز CRM',
        'فرم ساز CRM',
        'manage_options',
        'edit.php?post_type=crm_form', // لینک مستقیم به لیست فرم‌ها
        null,
        'dashicons-database-add',
        30
    );
    
    // ایجاد زیرمنوها
    add_submenu_page(
        'edit.php?post_type=crm_form', // والد: لیست فرم‌ها
        'همه فرم‌ها',
        'همه فرم‌ها',
        'manage_options',
        'edit.php?post_type=crm_form'
    );
    add_submenu_page(
        'edit.php?post_type=crm_form', // والد: لیست فرم‌ها
        'افزودن فرم',
        'افزودن فرم',
        'manage_options',
        'post-new.php?post_type=crm_form'
    );
    add_submenu_page(
        'edit.php?post_type=crm_form', // والد: لیست فرم‌ها
        'ورودی‌ها',
        'ورودی‌ها',
        'manage_options',
        'edit.php?post_type=crm_submission'
    );
    add_submenu_page(
        'edit.php?post_type=crm_form', // والد: لیست فرم‌ها
        'تنظیمات',
        'تنظیمات',
        'manage_options',
        'crm-connector-settings',
        'crm_connector_render_settings_page'
    );
}
add_action('admin_menu', 'crm_connector_create_menus');