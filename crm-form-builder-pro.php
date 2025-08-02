<?php
/**
 * Plugin Name:       CRM Form Builder Pro
 * Description:       یک پلاگین کامل برای ساخت فرم‌های متعدد، مدیریت ورودی‌ها و اتصال امن به CRM با طراحی Tailwind CSS.
 * Version:           5.1
 * Author:            Your Name
 * Requires at least: 5.2
 * Requires PHP:      7.4
 */

if (!defined('ABSPATH')) exit;

// =========================================================================
//  ۱. بررسی نسخه PHP (بخش جدید)
// =========================================================================
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>خطا در پلاگین فرم ساز CRM:</strong> این پلاگین برای اجرا به نسخه PHP 7.4 یا بالاتر نیاز دارد. نسخه PHP سرور شما ' . esc_html(PHP_VERSION) . ' است. لطفاً نسخه PHP خود را ارتقا دهید.';
        echo '</p></div>';
    });
    // از بارگذاری بقیه پلاگین جلوگیری کن
    return;
}

// =========================================================================
//  ۲. تعریف ثابت‌های پلاگین
// =========================================================================
define('CRM_CONNECTOR_PATH', plugin_dir_path(__FILE__));
define('CRM_CONNECTOR_URL', plugin_dir_url(__FILE__));
define('CRM_CONNECTOR_BASENAME', plugin_basename(__FILE__));

// =========================================================================
//  ۳. فراخوانی فایل‌های پلاگین
// =========================================================================

// فعال‌سازی و غیرفعال‌سازی
require_once CRM_CONNECTOR_PATH . 'includes/class-activator.php';
register_activation_hook(__FILE__, ['CRM_Connector_Activator', 'activate']);

// ثبت Post Types
require_once CRM_CONNECTOR_PATH . 'includes/post-types.php';

// توابع مربوط به API و AJAX
require_once CRM_CONNECTOR_PATH . 'includes/api-functions.php';

// بخش پیشخوان (Admin)
if (is_admin()) {
    require_once CRM_CONNECTOR_PATH . 'includes/admin/menu.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/settings-page.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/form-builder-metabox.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/custom-columns.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/submissions-metabox.php';
}

// بخش کاربری (Frontend)
require_once CRM_CONNECTOR_PATH . 'includes/frontend/enqueue.php';
require_once CRM_CONNECTOR_PATH . 'includes/frontend/shortcode.php';
require_once CRM_CONNECTOR_PATH . 'includes/frontend/form-handler.php';

// ماژول‌های یکپارچه‌سازی (Integrations)
function crm_connector_load_integrations() {
    if (did_action('elementor/loaded')) {
        require_once CRM_CONNECTOR_PATH . 'includes/integrations/elementor/handler.php';
    }
}
add_action('plugins_loaded', 'crm_connector_load_integrations');