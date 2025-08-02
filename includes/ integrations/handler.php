<?php
// File: includes/integrations/elementor/handler.php

if (!defined('ABSPATH')) exit;

class CRM_Elementor_Integration {

    public function __construct() {
        // فقط زمانی که المنتور بارگذاری شده باشد، ادامه بده
        add_action('elementor/loaded', [$this, 'init']);
    }

    public function init() {
        // ثبت ویجت‌ها
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }

    public function register_widgets($widgets_manager) {
        // فایل کلاس ویجت را فراخوانی کن
        require_once CRM_CONNECTOR_PATH . 'includes/integrations/elementor/widget.php';

        // ویجت جدید را در المنتور ثبت کن
        $widgets_manager->register(new \CRM_Form_Elementor_Widget());
    }
}

// اجرای کلاس
new CRM_Elementor_Integration();