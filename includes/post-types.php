<?php
function crm_connector_register_post_types() {
    register_post_type('crm_form', [
        'labels'       => ['name' => 'فرم‌ها', 'singular_name' => 'فرم', 'add_new_item' => 'افزودن فرم جدید'],
        'public'       => false, 'show_ui'      => true, 'show_in_menu' => false, 'supports'     => ['title'],
    ]);
    register_post_type('crm_submission', [
        'labels'         => ['name' => 'ورودی‌ها', 'singular_name' => 'ورودی'],
        'public'         => false, 'show_ui'        => true, 'show_in_menu'   => false, 'capability_type'=> 'post',
        'capabilities' => ['create_posts' => false], 'map_meta_cap'   => true, 'supports'       => ['title'],
    ]);
}
add_action('init', 'crm_connector_register_post_types');