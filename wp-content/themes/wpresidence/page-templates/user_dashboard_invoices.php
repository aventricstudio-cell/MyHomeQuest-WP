<?php
/**
 * Template Name: User Dashboard Invoices
 * 
 * Displays user invoices and payment history in the WPResidence dashboard.
 * Handles paid submissions, currency settings, and invoice management.
 * 
 * @package WPResidence
 * @subpackage Dashboard
 * @since 1.0.0
 * 
 * File: page-templates/user_dashboard_invoices.php
 */

// Security check
wpestate_dashboard_header_permissions();

/**
 * Initialize required settings and configurations
 */
$current_user = wp_get_current_user();
$userID = $current_user->ID;

// Payment and submission settings
$payment_settings = array(
    'status'   => esc_html(wpresidence_get_option('wp_estate_paid_submission', '')),
    'price'    => floatval(wpresidence_get_option('wp_estate_price_submission', '')),
    'currency' => esc_html(wpresidence_get_option('wp_estate_submission_curency', ''))
);

// Currency display settings
$currency_settings = array(
    'symbol'    => esc_html(wpresidence_get_option('wp_estate_currency_symbol', '')),
    'position'  => esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''))
);

// User favorites settings
$favorites_settings = array(
    'listings'      => wpestate_return_favorite_listings_per_user(),
    'show_remove'   => 1,
    'show_compare'  => 1,
    'compare_only'  => 'no'
);

// Get theme options
$wpestate_options = get_query_var('wpestate_options');

// Load header
get_header();
?>

<div class="row row_user_dashboard">
    <?php 
    // Include dashboard sidebar
    include(locate_template('templates/dashboard-templates/dashboard-left-col.php')); 
    ?>

    <div class="col-md-9 dashboard-margin row">
        <?php
        // Include membership profile section
        include(locate_template('templates/dashboard-templates/user_memebership_profile.php'));
        
        // Display dashboard title
        wpestate_show_dashboard_title(get_the_title());
        ?>

        <!-- Main Content Area -->
        <div class="dashboard-wrapper-form row">
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper">
                    <?php
                    // Display invoice list
                    wpestate_dashboard_invoice_list();
                    ?>
                </div>
            </div> 
        </div>
    </div>
</div>

<?php
// Create and output security nonce for invoice actions
$invoice_nonce = wp_create_nonce("wpestate_invoices_actions");
?>
<input type="hidden" 
       id="wpestate_invoices_actions" 
       value="<?php echo esc_attr($invoice_nonce); ?>" />

<?php
// Load footer
get_footer();
?>