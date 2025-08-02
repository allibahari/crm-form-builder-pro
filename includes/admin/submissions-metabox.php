<?php
function crm_connector_add_submission_details_metabox() {
    add_meta_box('crm_submission_details', 'جزئیات اطلاعات ارسال شده', 'crm_connector_render_submission_details_callback', 'crm_submission', 'normal', 'high');
}
add_action('add_meta_boxes', 'crm_connector_add_submission_details_metabox');

function crm_connector_render_submission_details_callback($post) {
    $form_id = get_post_meta($post->ID, '_form_id', true);
    $submission_data = get_post_meta($post->ID, '_submission_data', true);
    $submission_date = get_post_meta($post->ID, '_submission_date', true);
    $api_status = get_post_meta($post->ID, '_api_sent_status', true);
    ?>
    <style>.submission-table{width:100%;border-collapse:collapse;}.submission-table th,.submission-table td{text-align:right;padding:12px;border:1px solid #ddd}.submission-table th{background-color:#f9f9f9;width:180px;font-weight:700}.submission-table tr:nth-child(even){background-color:#f2f2f2}</style>
    <div class="wrap">
        <h2>اطلاعات کلی</h2>
        <table class="submission-table">
            <?php if ($form_id) echo '<tr><th>فرم ارسالی</th><td><a href="' . get_edit_post_link($form_id) . '">' . esc_html(get_the_title($form_id)) . '</a></td></tr>'; ?>
            <?php if ($submission_date) echo '<tr><th>تاریخ ارسال</th><td>' . esc_html($submission_date) . '</td></tr>'; ?>
            <?php if ($api_status) echo '<tr><th>وضعیت ارسال به CRM</th><td><strong style="color:' . ($api_status === 'موفق' ? 'green' : 'red') . ';">' . esc_html($api_status) . '</strong></td></tr>'; ?>
        </table>
        <h2 style="margin-top:30px;">فیلدهای پر شده</h2>
        <?php if (!empty($submission_data) && is_array($submission_data)) : ?>
            <table class="submission-table">
                <?php foreach ($submission_data as $label => $value) : ?>
                    <tr><th><?php echo esc_html($label); ?></th><td><?php echo nl2br(esc_html($value)); ?></td></tr>
                <?php endforeach; ?>
            </table>
        <?php else : echo '<p>هیچ اطلاعاتی یافت نشد.</p>'; endif; ?>
    </div>
    <?php
}