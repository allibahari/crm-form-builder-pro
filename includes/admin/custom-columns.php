<?php
function crm_connector_add_shortcode_column_to_forms_list($columns) {
    $columns['shortcode'] = 'شورت‌کد';
    return $columns;
}
add_filter('manage_crm_form_posts_columns', 'crm_connector_add_shortcode_column_to_forms_list');

function crm_connector_render_shortcode_column($column, $post_id) {
    if ($column === 'shortcode') {
        echo '<input type="text" readonly="readonly" value="[my_crm_form id=\'' . $post_id . '\']" onclick="this.select();" style="width:100%;">';
    }
}
add_action('manage_crm_form_posts_custom_column', 'crm_connector_render_shortcode_column', 10, 2);