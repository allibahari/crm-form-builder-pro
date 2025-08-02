<?php
class CRM_Connector_Activator {
    public static function activate() {
        if (get_option('crm_connector_activated')) {
            return;
        }

        $default_form_id = wp_insert_post([
            'post_type'    => 'crm_form',
            'post_title'   => 'فرم تماس پیش‌فرض',
            'post_status'  => 'publish',
        ]);

        if ($default_form_id && !is_wp_error($default_form_id)) {
            $default_fields = [
                ['label' => 'نام شما', 'type' => 'text', 'required' => 'true'],
                ['label' => 'ایمیل شما', 'type' => 'email', 'required' => 'true'],
                ['label' => 'پیام شما', 'type' => 'textarea', 'required' => 'false'],
            ];
            update_post_meta($default_form_id, '_form_fields', $default_fields);

            $page_content = '<p>از این فرم برای تماس با ما استفاده کنید.</p>';
            $page_content .= '[my_crm_form id="' . $default_form_id . '"]';

            wp_insert_post([
                'post_type'    => 'page',
                'post_title'   => 'تماس با ما',
                'post_content' => $page_content,
                'post_status'  => 'publish',
            ]);
        }
        update_option('crm_connector_activated', true);
        set_transient('crm_activation_redirect', true, 30);
    }
}