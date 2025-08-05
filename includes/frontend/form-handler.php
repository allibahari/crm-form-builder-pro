<?php
// File: includes/frontend/form-handler.php

if (!defined('ABSPATH')) exit;

function crm_connector_handle_form_submission() {
    // ... (initial security checks) ...
    $form_id = intval($_POST['form_id']);
    $errors = [];

    // --- New Captcha Validation Block ---
    $captcha_type = get_post_meta($form_id, '_captcha_type', true);

    if ($captcha_type === 'math') {
        if (!session_id()) { session_start(); }
        $user_answer = isset($_POST['math_captcha']) ? intval($_POST['math_captcha']) : '';
        $correct_answer = isset($_SESSION['crm_math_captcha_answer']) ? $_SESSION['crm_math_captcha_answer'] : null;
        
        if ($user_answer !== $correct_answer) {
            $errors[] = 'The math problem was answered incorrectly.';
        }
        unset($_SESSION['crm_math_captcha_answer']); // Unset after checking

    } elseif ($captcha_type === 'recaptcha') {
        $options = get_option('crm_connector_options');
        $secret_key = $options['recaptcha_secret_key'] ?? '';
        $g_response = $_POST['g-recaptcha-response'] ?? '';

        if (empty($g_response)) {
            $errors[] = 'Please complete the reCAPTCHA challenge.';
        } elseif (!empty($secret_key)) {
            $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
            $response = wp_remote_post($verify_url, [
                'body' => [
                    'secret'   => $secret_key,
                    'response' => $g_response,
                    'remoteip' => $_SERVER['REMOTE_ADDR'],
                ],
            ]);
            $output = json_decode(wp_remote_retrieve_body($response));

            if (!$output->success) {
                $errors[] = 'Google reCAPTCHA verification failed. Please try again.';
            }
        }
    }
    // --- End of Captcha Validation Block ---

    // ... (rest of the validation for other fields) ...
    $form_rules = get_post_meta($form_id, '_form_fields', true);
    // ... loop through form_rules and add to $errors array ...

    // If there are any errors (from captcha or fields), redirect back.
    if (!empty($errors)) {
        set_transient('crm_form_errors_' . $form_id, $errors, 60);
        set_transient('crm_form_old_data_' . $form_id, $_POST['form_fields'] ?? [], 60);
        wp_safe_redirect(wp_get_referer());
        exit;
    }

    // ... If no errors, proceed with saving and sending data ...
}
add_action('admin_post_nopriv_submit_crm_form', 'crm_connector_handle_form_submission');
add_action('admin_post_submit_crm_form', 'crm_connector_handle_form_submission');