<?php
function crm_connector_enqueue_scripts() {
    wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com', [], null, false);
}
add_action('wp_enqueue_scripts', 'crm_connector_enqueue_scripts');

function crm_connector_admin_enqueue_scripts($hook) {
    global $post_type;
    if (('post.php' == $hook || 'post-new.php' == $hook) && 'crm_form' == $post_type) {
        wp_enqueue_script('crm-form-builder', CRM_CONNECTOR_URL . 'assets/js/form-builder-admin.js', [], '1.0', true);
        wp_enqueue_style('crm-admin-style', CRM_CONNECTOR_URL . 'assets/css/admin-style.css');
    }
    if ($hook === 'toplevel_page_crm-connector-settings' || $hook === 'فرم-ساز-crm_page_crm-connector-settings') {
        wp_enqueue_script('crm-settings-admin', CRM_CONNECTOR_URL . 'assets/js/settings-admin.js', [], '1.0', true);
        wp_localize_script('crm-settings-admin', 'crm_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('crm_test_connection_nonce')
        ]);
    }
}
add_action('admin_enqueue_scripts', 'crm_connector_admin_enqueue_scripts');