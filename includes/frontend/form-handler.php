<?php
// File: includes/frontend/form-handler.php

if (!defined('ABSPATH')) exit;

function crm_connector_handle_form_submission() {
    // ... (بخش بررسی امنیتی و اولیه بدون تغییر) ...
    if (!isset($_POST['form_id'], $_POST['crm_nonce']) || !wp_verify_nonce($_POST['crm_nonce'], 'submit_crm_form_' . $_POST['form_id'])) {
        wp_die('بررسی امنیتی ناموفق بود!');
    }

    $form_id = intval($_POST['form_id']);
    $submitted_fields = isset($_POST['form_fields']) ? (array) $_POST['form_fields'] : [];
    $redirect_url = wp_get_referer() ? remove_query_arg(['submission', 'submission_form_id'], wp_get_referer()) : home_url();

    // شروع فرآیند اعتبارسنجی
    $form_rules = get_post_meta($form_id, '_form_fields', true);
    $errors = [];
    $uploaded_file_data = []; // برای ذخیره اطلاعات فایل آپلود شده

    // این توابع برای آپلود فایل لازم هستند
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    foreach ($form_rules as $rule) {
        $label = $rule['label'];
        $is_required = isset($rule['required']) && $rule['required'] === 'true';

        // اعتبارسنجی فیلدهای عادی
        if ($rule['type'] !== 'file') {
            $submitted_value = isset($submitted_fields[$label]) ? trim($submitted_fields[$label]) : '';
            if ($is_required && empty($submitted_value)) {
                $errors[] = 'فیلد "' . esc_html($label) . '" ضروری است.';
            }
            if ($rule['type'] === 'email' && !empty($submitted_value) && !is_email($submitted_value)) {
                $errors[] = 'فرمت ایمیل در فیلد "' . esc_html($label) . '" صحیح نیست.';
            }
        }
        // اعتبارسنجی فیلد فایل
        else {
            if (isset($_FILES[$label]) && !empty($_FILES[$label]['name'])) {
                $file = $_FILES[$label];
                
                // بررسی حجم فایل (مثلاً حداکثر 2MB)
                $max_size = 2 * 1024 * 1024; // 2 مگابایت
                if ($file['size'] > $max_size) {
                    $errors[] = 'حجم فایل "' . esc_html($label) . '" بیش از حد مجاز (2MB) است.';
                }

                // بررسی نوع فایل (فقط عکس)
                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_info = wp_check_filetype(basename($file['name']));
                if (!in_array($file_info['type'], $allowed_mime_types)) {
                    $errors[] = 'فرمت فایل "' . esc_html($label) . '" غیرمجاز است. فقط JPG, PNG, GIF قبول می‌شود.';
                }
                
                if (empty($errors)) {
                    $uploaded_file_data[$label] = $file;
                }

            } elseif ($is_required) {
                $errors[] = 'آپلود فایل در فیلد "' . esc_html($label) . '" ضروری است.';
            }
        }
    }
    
    // ... (بخش تصمیم‌گیری بر اساس خطاها بدون تغییر) ...
    if (!empty($errors)) {
        set_transient('crm_form_errors_' . $form_id, $errors, 60);
        set_transient('crm_form_old_data_' . $form_id, $submitted_fields, 60);
        wp_safe_redirect(add_query_arg(['submission' => 'validation_error', 'submission_form_id' => $form_id], $redirect_url));
        exit;
    }
    
    // اگر همه چیز درست بود، فایل‌ها را آپلود کن
    $sanitized_data = [];
    foreach ($submitted_fields as $label => $value) {
        $sanitized_data[sanitize_text_field($label)] = sanitize_textarea_field($value);
    }
    
    foreach ($uploaded_file_data as $label => $file) {
        $upload_overrides = ['test_form' => false];
        $movefile = wp_handle_upload($file, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            $sanitized_data[sanitize_text_field($label)] = $movefile['url']; // ذخیره آدرس URL فایل آپلود شده
        } else {
            $errors[] = 'خطا در آپلود فایل: ' . $movefile['error'];
        }
    }
    
    // اگر در حین آپلود خطا رخ داد، دوباره به فرم برگردان
    if (!empty($errors)) {
        set_transient('crm_form_errors_' . $form_id, $errors, 60);
        // ... (کد مشابه بالا)
        exit;
    }

    // ادامه فرآیند ذخیره‌سازی و ارسال به API
    // ... (بقیه کد مانند قبل است، فقط داده‌های $sanitized_data را استفاده می‌کند) ...
    $first_name = 'ورودی جدید'; $email = '';
    // ... (بقیه کد ذخیره سازی)
}
// ...