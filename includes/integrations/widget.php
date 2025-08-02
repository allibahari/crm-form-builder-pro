<?php
// File: includes/integrations/elementor/widget.php

if (!defined('ABSPATH')) exit;

class CRM_Form_Elementor_Widget extends \Elementor\Widget_Base {

    // نام منحصر به فرد ویجت
    public function get_name() {
        return 'crm_form_selector';
    }

    // عنوانی که در پنل المنتور نمایش داده می‌شود
    public function get_title() {
        return __('فرم ساز CRM', 'crm-form-builder-pro');
    }

    // آیکون ویجت
    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    // دسته‌بندی ویجت
    public function get_categories() {
        return ['general'];
    }

    // تابع کمکی برای گرفتن لیست فرم‌ها
    private function get_forms_list() {
        $forms = get_posts([
            'post_type' => 'crm_form',
            'numberposts' => -1,
            'post_status' => 'publish',
        ]);

        if (empty($forms)) {
            return ['0' => __('هیچ فرمی یافت نشد', 'crm-form-builder-pro')];
        }

        return wp_list_pluck($forms, 'post_title', 'ID');
    }

    // تعریف کنترل‌ها (تنظیمات ویجت در پنل المنتور)
    protected function register_controls() {
        // تب محتوا
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('محتوا', 'crm-form-builder-pro'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'form_id',
            [
                'label' => __('یک فرم انتخاب کنید', 'crm-form-builder-pro'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_forms_list(),
                'default' => '0',
            ]
        );

        $this->end_controls_section();

        // تب استایل
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('استایل دکمه', 'crm-form-builder-pro'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'button_background_color',
			[
				'label' => __( 'رنگ پس‌زمینه دکمه', 'crm-form-builder-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form button' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'button_text_color',
			[
				'label' => __( 'رنگ متن دکمه', 'crm-form-builder-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form button' => 'color: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_section();
    }

    // رندر کردن خروجی ویجت در سایت (بخش کاربری)
    protected function render() {
        $settings = $this->get_settings_for_display();
        $form_id = $settings['form_id'];

        if (empty($form_id) || $form_id === '0') {
            echo '<div style="padding: 20px; text-align: center; background-color: #fff8e1; border: 1px solid #ffecb3;">';
            echo __('لطفاً یک فرم را از تنظیمات ویجت انتخاب کنید.', 'crm-form-builder-pro');
            echo '</div>';
            return;
        }

        // استفاده از شورت‌کد موجود برای نمایش فرم
        echo do_shortcode('[my_crm_form id="' . esc_attr($form_id) . '"]');
    }
}