<?php
function crm_connector_add_all_form_meta_boxes() {
    add_meta_box('crm_form_builder', 'فیلدهای فرم', 'crm_connector_render_form_builder_callback', 'crm_form', 'normal', 'high');
    add_meta_box('crm_form_shortcode', 'شورت‌کد فرم', 'crm_connector_render_shortcode_metabox_callback', 'crm_form', 'side', 'high');
}
add_action('add_meta_boxes', 'crm_connector_add_all_form_meta_boxes');

function crm_connector_render_shortcode_metabox_callback($post) {
    $shortcode = '[my_crm_form id="' . $post->ID . '"]';
    echo '<p>این شورت‌کد را کپی کنید:</p><input type="text" readonly="readonly" value="' . esc_attr($shortcode) . '" onclick="this.select();" style="width:100%; text-align:center;">';
}

function crm_connector_render_form_builder_callback($post) {
    wp_nonce_field('save_form_fields', '_form_fields_nonce');
    $fields = get_post_meta($post->ID, '_form_fields', true);
    $fields = is_array($fields) ? $fields : [];
    echo '<div id="field-repeater-container"></div><button type="button" id="add-field-btn" class="button button-primary" style="margin-top: 15px;">افزودن فیلد</button>';
    echo '<script>window.crm_form_fields = ' . json_encode($fields) . ';</script>';
}

function crm_connector_save_form_fields($post_id) {
    if (!isset($_POST['_form_fields_nonce']) || !wp_verify_nonce($_POST['_form_fields_nonce'], 'save_form_fields')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) !== 'crm_form' || !current_user_can('edit_post', $post_id)) return;

    $new_fields = isset($_POST['_form_fields']) ? (array) $_POST['_form_fields'] : [];
    $sanitized_fields = [];
    foreach ($new_fields as $field) {
        if (empty($field['label'])) continue;
        $sanitized_fields[] = ['label' => sanitize_text_field($field['label']), 'type'  => sanitize_text_field($field['type']), 'required' => isset($field['required']) ? 'true' : 'false'];
    }
    update_post_meta($post_id, '_form_fields', $sanitized_fields);
}
add_action('save_post', 'crm_connector_save_form_fields');