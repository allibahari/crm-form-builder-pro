<?php
// File: includes/admin/menu.php

if (!defined('ABSPATH')) exit;

/**
 * Handles the redirect after plugin activation.
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
 * Creates the admin menus based on license status.
 */
function crm_connector_create_menus() {
    $is_verified = get_option('crm_connection_status') === 'verified';

    if ($is_verified) {
        // اگر لایسنس فعال باشد، منوهای کامل ساخته می‌شوند
        add_menu_page('فرم ساز CRM', 'فرم ساز CRM', 'manage_options', 'edit.php?post_type=crm_form', null, 'dashicons-database-add', 30);
        add_submenu_page('edit.php?post_type=crm_form', 'همه فرم‌ها', 'همه فرم‌ها', 'manage_options', 'edit.php?post_type=crm_form');
        add_submenu_page('edit.php?post_type=crm_form', 'افزودن فرم', 'افزودن فرم', 'manage_options', 'post-new.php?post_type=crm_form');
        add_submenu_page('edit.php?post_type=crm_form', 'ورودی‌ها', 'ورودی‌ها', 'manage_options', 'edit.php?post_type=crm_submission');
        add_submenu_page('edit.php?post_type=crm_form', 'تنظیمات و لایسنس', 'تنظیمات و لایسنس', 'manage_options', 'crm-connector-settings', 'crm_connector_render_settings_page');
    
    } else {
        // اگر لایسنس فعال نباشد، فقط منوی تنظیمات ساخته می‌شود
        add_menu_page('فرم ساز CRM', 'فرم ساز CRM', 'manage_options', 'crm-connector-settings', 'crm_connector_render_settings_page', 'dashicons-database-add', 30);
    }
}
add_action('admin_menu', 'crm_connector_create_menus');

/**
 * پیغام خطا را با استایل Tailwind نمایش می‌دهد.
 */
function crm_connector_show_admin_notices() {
    // فقط زمانی که لایسنس تایید نشده و در صفحه تنظیمات نیستیم، پیغام را نمایش بده
    if (get_option('crm_connection_status') !== 'verified' && (!isset($_GET['page']) || $_GET['page'] !== 'crm-connector-settings')) {
        // *** تغییر اصلی در اینجا است ***
        $settings_url = admin_url('admin.php?page=crm-connector-settings');
        ?>
        <div class="m-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-lg shadow-md" role="alert">
            <p>
                <strong class="font-bold">[فرم ساز CRM]:</strong>
                برای استفاده از امکانات، لطفاً ابتدا لایسنس خود را از 
                <a href="<?php echo esc_url($settings_url); ?>" class="font-semibold text-red-900 underline hover:text-red-700 transition-colors">
                    صفحه تنظیمات
                </a>
                فعال کنید.
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'crm_connector_show_admin_notices');