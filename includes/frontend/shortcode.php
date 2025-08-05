<?php
// File: includes/frontend/shortcode.php

if (!defined('ABSPATH')) exit;

function crm_connector_register_shortcode() {
    add_shortcode('my_crm_form', 'crm_connector_render_form');
}
add_action('init', 'crm_connector_register_shortcode');

function crm_connector_render_form($atts) {
    $atts = shortcode_atts(['id' => 0], $atts, 'my_crm_form');
    $form_id = intval($atts['id']);

    if (!$form_id || get_post_type($form_id) !== 'crm_form' || get_option('crm_connection_status') !== 'verified') {
        return '';
    }

    $fields = get_post_meta($form_id, '_form_fields', true);
    // Get captcha setting for this form
    $captcha_type = get_post_meta($form_id, '_captcha_type', true);
    $options = get_option('crm_connector_options');

    // If Google reCAPTCHA is selected, enqueue its script
    if ($captcha_type === 'recaptcha' && !empty($options['recaptcha_site_key'])) {
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, true);
    }
    
    // ... (rest of the initial code, getting errors, old data, etc.) ...
    
    ob_start();
    ?>
    <div class="w-full max-w-lg mx-auto p-8 space-y-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800"><?php echo get_the_title($form_id); ?></h2>
        
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
            <?php if ($captcha_type === 'math') :
                if (!session_id()) { session_start(); }
                $num1 = rand(1, 9);
                $num2 = rand(1, 9);
                $_SESSION['crm_math_captcha_answer'] = $num1 + $num2;
                ?>
                <div class="crm-form-field mb-5">
                    <label for="math_captcha" class="block mb-2 text-sm font-medium text-gray-700">Please solve: <?php echo "$num1 + $num2"; ?> = ? <span class="text-red-500">*</span></label>
                    <input type="number" name="math_captcha" id="math_captcha" class="block w-full p-2.5 text-gray-900 bg-gray-50 rounded-lg border border-gray-300" required>
                </div>
            <?php elseif ($captcha_type === 'recaptcha' && !empty($options['recaptcha_site_key'])) : ?>
                <div class="crm-form-field mb-5">
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($options['recaptcha_site_key']); ?>"></div>
                </div>
            <?php endif; ?>
            
            <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Submit</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}