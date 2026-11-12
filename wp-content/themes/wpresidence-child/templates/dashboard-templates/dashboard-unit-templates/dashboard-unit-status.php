<?php
/** MILLDONE
 * Property Status Display Template
 * src: templates\dashboard-templates\dashboard-unit-templates\dashboard-unit-status.php
 * Displays the current status of a property listing including its publication
 * and payment status. Handles different states: published, expired, disabled,
 * draft, and pending approval.
 *
 * Required variables:
 * @param int    $post_id               The property post ID
 * @param string $paid_submission_status The type of submission payment system
 */

// Get current post status
$post_status = get_post_status($post_id);
$status = '';
$link = '';

// Determine status label and link based on post status
if ($post_status == 'expired') {
    $status = esc_html__('Expired', 'wpresidence');
} elseif ($post_status == 'publish') {
    $link = get_permalink();
    $status = esc_html__('Published', 'wpresidence');
} elseif ($post_status == 'disabled') {
    $status = esc_html__('Disabled', 'wpresidence');
} elseif ($post_status == 'draft') {
    $status = esc_html__('Draft', 'wpresidence');
} else {
    $status = esc_html__('Waiting for approval', 'wpresidence');
}

// Handle payment status for per listing submissions
$is_pay_status = '';
if ($paid_submission_status == 'per listing') {
    $pay_status = get_post_meta($post_id, 'pay_status', true);
    if ($pay_status == 'paid') {
        $is_pay_status = esc_html__('Paid', 'wpresidence');
    } else {
        $is_pay_status = esc_html__('Not Paid', 'wpresidence');
    }
}

// Display status with appropriate CSS class
?>
<div class="property_list_status_label <?php echo sanitize_key($post_status); ?>">
    <?php echo esc_html($status); ?>
</div>