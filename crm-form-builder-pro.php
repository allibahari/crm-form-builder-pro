<?php
/**
 * Plugin Name: CRM Form Builder Pro
 * Description: یک پلاگین کامل برای ساخت فرم‌های متعدد، مدیریت ورودی‌ها و اتصال امن به CRM با طراحی Tailwind CSS.
 * Version: 5.0
 * Author: ALi Bahari
 */

if (!defined('ABSPATH')) exit;

define('CRM_CONNECTOR_PATH', plugin_dir_path(__FILE__));
define('CRM_CONNECTOR_URL', plugin_dir_url(__FILE__));
define('CRM_CONNECTOR_BASENAME', plugin_basename(__FILE__));

// ۱. فعال‌سازی و غیرفعال‌سازی
require_once CRM_CONNECTOR_PATH . 'includes/class-activator.php';
register_activation_hook(__FILE__, ['CRM_Connector_Activator', 'activate']);

// ۲. ثبت Post Types
require_once CRM_CONNECTOR_PATH . 'includes/post-types.php';

// ۳. توابع مربوط به API و AJAX
require_once CRM_CONNECTOR_PATH . 'includes/api-functions.php';

// ۴. بخش پیشخوان (Admin)
if (is_admin()) {
    require_once CRM_CONNECTOR_PATH . 'includes/admin/menu.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/settings-page.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/form-builder-metabox.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/custom-columns.php';
    require_once CRM_CONNECTOR_PATH . 'includes/admin/submissions-metabox.php';
}

// ۵. بخش کاربری (Frontend)
require_once CRM_CONNECTOR_PATH . 'includes/frontend/enqueue.php';
require_once CRM_CONNECTOR_PATH . 'includes/frontend/shortcode.php';
require_once CRM_CONNECTOR_PATH . 'includes/frontend/form-handler.php';
// ۶. بارگذاری ماژول‌های یکپارچه‌سازی (Integrations)
function crm_connector_load_integrations() {
    // بررسی می‌کند که آیا افزونه المنتور نصب و فعال است یا خیر
    if (did_action('elementor/loaded')) {
        require_once CRM_CONNECTOR_PATH . 'includes/integrations/elementor/handler.php';
    }
}
add_action('plugins_loaded', 'crm_connector_load_integrations');