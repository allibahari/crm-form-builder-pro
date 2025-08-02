<?php
// File: includes/frontend/shortcode.php

function crm_connector_register_shortcode() {
    add_shortcode('my_crm_form', 'crm_connector_render_form');
}
add_action('init', 'crm_connector_register_shortcode');

function crm_connector_render_form($atts) {
    $atts = shortcode_atts(['id' => 0], $atts, 'my_crm_form');
    $form_id = intval($atts['id']);

    if (!$form_id || get_post_type($form_id) !== 'crm_form') {
        return '<p>خطا: فرم یافت نشد.</p>';
    }

    $fields = get_post_meta($form_id, '_form_fields', true);
    if (empty($fields) || !is_array($fields)) return '<p>این فرم فیلدی ندارد.</p>';

    $errors = get_transient('crm_form_errors_' . $form_id);
    $old_data = get_transient('crm_form_old_data_' . $form_id);
    delete_transient('crm_form_errors_' . $form_id);
    delete_transient('crm_form_old_data_' . $form_id);

    // بررسی وجود فیلد فایل برای افزودن enctype
    $has_file_field = false;
    foreach ($fields as $field) {
        if ($field['type'] === 'file') {
            $has_file_field = true;
            break;
        }
    }

    ob_start();
    
    // ... (بخش نمایش خطاها بدون تغییر باقی می‌ماند) ...
    if (!empty($errors) && is_array($errors)) {
        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">';
        echo '<p class="font-bold">لطفاً خطاهای زیر را برطرف کنید:</p><ul class="list-disc list-inside mt-2">';
        foreach ($errors as $error) { echo '<li>' . esc_html($error) . '</li>'; }
        echo '</ul></div>';
    }
    ?>
    <div class="w-full max-w-lg mx-auto p-8 space-y-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800"><?php echo get_the_title($form_id); ?></h2>
        
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" <?php if ($has_file_field) echo 'enctype="multipart/form-data"'; ?>>
            <input type="hidden" name="action" value="submit_crm_form">
            <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
            <?php wp_nonce_field('submit_crm_form_' . $form_id, 'crm_nonce'); ?>
            
            <?php foreach ($fields as $key => $field) :
                $field_id = 'field_' . $form_id . '_' . $key;
                $is_required = isset($field['required']) && $field['required'] === 'true';
                $field_label = $field['label'];
                $old_value = isset($old_data[$field_label]) ? $old_data[$field_label] : '';
                ?>
                <div class="mb-5">
                    <label for="<?php echo esc_attr($field_id); ?>" class="block mb-2 text-sm font-medium text-gray-700"><?php echo esc_html($field_label); if ($is_required) echo '<span class="text-red-500">*</span>'; ?></label>
                    
                    <?php if ($field['type'] === 'file') : ?>
                        <input type="file" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($field_label); ?>" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" <?php if ($is_required) echo 'required'; ?>>
                        <p class="mt-1 text-sm text-gray-500">فرمت‌های مجاز: JPG, PNG (حداکثر حجم: 2MB)</p>
                    
                    <?php elseif ($field['type'] === 'textarea') : ?>
                        <textarea id="<?php echo esc_attr($field_id); ?>" name="form_fields[<?php echo esc_attr($field_label); ?>]" class="block w-full p-2.5 text-gray-900 bg-gray-50 rounded-lg border border-gray-300" rows="4" <?php if ($is_required) echo 'required'; ?>><?php echo esc_textarea($old_value); ?></textarea>
                    
                    <?php else : ?>
                        <input type="<?php echo esc_attr($field['type']); ?>" id="<?php echo esc_attr($field_id); ?>" name="form_fields[<?php echo esc_attr($field_label); ?>]" value="<?php echo esc_attr($old_value); ?>" class="block w-full p-2.5 text-gray-900 bg-gray-50 rounded-lg border border-gray-300" <?php if ($is_required) echo 'required'; ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center">ارسال</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}