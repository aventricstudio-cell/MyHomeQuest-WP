<?php
/** MILLDONE
 * Property Payment Status Template
 * src: templates/dashboard-templates/dashboard-unit-templates/dashboard-unit-paystatus.php
 * Displays the payment status for properties under the 'per listing' payment model.
 * Shows either 'Paid' or 'Not Paid' status with appropriate styling.
 *
 * @package WpResidence
 * @subpackage Dashboard/Templates
 * @since 1.0
 * 
 * Required variables:
 * @param int    $post_id               The property post ID
 * @param string $paid_submission_status The type of submission payment system
 */

// Only process for per listing payment model
if ($paid_submission_status == 'per listing') {
    // Get and sanitize payment status
    $pay_status = get_post_meta($post_id, 'pay_status', true);
    
    // Set appropriate status text
    $pay_status_text = ($pay_status == 'paid')
        ? esc_html__('Paid', 'wpresidence')
        : esc_html__('Not Paid', 'wpresidence');

    $pay_status_class = ($pay_status == 'paid')
        ? 'paid'
        : 'notpaid';

    // Display payment status with appropriate CSS class
    printf(
        '<div class="property_list_status_label %1$s">%2$s</div>',
        sanitize_key($pay_status_class),
        esc_html($pay_status_text)
    );
}
?>