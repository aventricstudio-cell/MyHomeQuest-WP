<?php
/** MILLDONE
 * Invoice Listing Unit Template
 * src: templates\dashboard-templates\invoice_listing_unit.php
 * Displays individual invoice details in the dashboard invoice list.
 * Shows invoice title, date, type, billing type, status, price, and print option.
 *
 * @package WpResidence
 * @subpackage Dashboard/Invoices
 * @since 1.0
 * 
 * Required variables:
 * @param int    $invoiceID          The invoice post ID
 * @param string $wpestate_currency  Currency symbol
 * @param string $where_currency     Currency position
 */

// Get invoice details
$invoice_type = esc_html(get_post_meta($invoiceID, 'invoice_type', true));
$bill_type = esc_html(get_post_meta($invoiceID, 'biling_type', true));
$payment_status = esc_html(get_post_meta($invoiceID, 'pay_status', true));
$price = get_post_meta($invoiceID, 'item_price', true);

// Format payment status
$status_text = ($payment_status == 0) 
    ? esc_html__('Not Paid', 'wpresidence')
    : esc_html__('Paid', 'wpresidence');

$status_class = ($payment_status == 0) 
    ? 'notpaid'
    : 'paid';

// Map invoice types to translatable strings
$invoice_types = [
    'Listing' => esc_html__('Listing', 'wpresidence'),
    'Upgrade to Featured' => esc_html__('Upgrade to Featured', 'wpresidence'),
    'Publish Listing with Featured' => esc_html__('Publish Listing with Feature', 'wpresidence'),
    'Package' => esc_html__('Package', 'wpresidence')
];

// Map billing types to translatable strings
$billing_types = [
    'One Time' => esc_html__('One Time', 'wpresidence'),
    'Recurring' => esc_html__('Recurring', 'wpresidence')
];
?>

<div class="flex-md-row flex-column align-items-start align-items-md-center  gap-3 gap-md-1  invoice_unit" 
     data-booking-confirmed="<?php echo esc_attr(get_post_meta($invoiceID, 'item_id', true)); ?>"
     data-invoice-confirmed="<?php echo intval($invoiceID); ?>">
    
    <!-- Invoice Title -->
    <div class="col-md-2 invoice_unit_title">
        <?php echo esc_html(get_the_title()); ?>
    </div>

    <!-- Invoice Date -->
    <div class="col-md-2">
        <?php echo esc_html(get_the_date()); ?>
    </div>

    <!-- Invoice Type -->
    <div class="col-md-2">
        <?php 
        if (isset($invoice_types[$invoice_type])) {
            echo $invoice_types[$invoice_type];
        }
        ?>
    </div>

    <!-- Billing Type -->
    <div class="col-md-2">
        <?php 
        if (isset($billing_types[$bill_type])) {
            echo $billing_types[$bill_type];
        }
        ?>
    </div>

    <!-- Payment Status -->
    <div class="col-md-2">
        <div class="property_list_status_label <?php echo sanitize_key($status_class); ?>">
            <?php echo esc_html($status_text); ?>
        </div>
    </div>

    <!-- Price -->
    <div class="col-md-1 price_invoice_wrap">
        <?php 
        echo wpestate_show_price_booking_for_invoice(
            $price,
            $wpestate_currency,
            $where_currency,
            0,
            1
        ); 
        ?>
    </div>

    <!-- Print Button -->
    <div class="col-md-1 print_invoice_wrap">
        <div class="print_invoice" data-post-id="<?php echo intval($invoiceID); ?>">
            <?php esc_html_e('Print', 'wpresidence'); ?>
        </div>
    </div>
</div>