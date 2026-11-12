<?php
/** MILLDONE
 * WpResidence Theme - User Dashboard Template
 * src: templates\dashboard-templates\user_memebership_profile.php
 * This file is part of the WpResidence theme and handles the user dashboard display,
 * including package information, available packages, and payment options.
 *
 * @package WpResidence
 * @subpackage UserDashboard
 * @since 1.0.0
 *
 * Dependencies:
 * - WpResidence theme functions
 * - WordPress core functions
 *
 * Usage:
 * This template is typically included in the user dashboard page of the WpResidence theme.
 * It displays user package information, available packages, and payment options.
 */

// Retrieve user information and dashboard links
$parent_userID          = wpestate_check_for_agency($userID);
$user_login             = $current_user->user_login;
$add_link               = wpestate_get_template_link('page-templates/user_dashboard_add.php');
$dash_profile           = wpestate_get_template_link('page-templates/user_dashboard_profile.php');
$dash_favorite          = wpestate_get_template_link('page-templates/user_dashboard_favorite.php');
$dash_link              = wpestate_get_template_link('page-templates/user_dashboard.php');
$activeprofile          = '';
$activedash             = '';
$activeadd              = '';
$activefav              = '';

// Retrieve user package and payment information
$user_pack              = get_the_author_meta('package_id', $parent_userID);
$clientId               = esc_html(wpresidence_get_option('wp_estate_paypal_client_id', ''));
$clientSecret           = esc_html(wpresidence_get_option('wp_estate_paypal_client_secret', ''));
$user_registered        = get_the_author_meta('user_registered', $userID);
$user_package_activation = get_the_author_meta('package_activation', $parent_userID);
$is_membership          = 0;
$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
$user_role              = get_user_meta($current_user->ID, 'user_estate_role', true);

?>

<div class="col-md-12 top_dahsboard_wrapper dashboard_package_row">
    <?php
    // Display membership package information if applicable
    if ($paid_submission_status == 'membership') {
        wpestate_get_pack_data_for_user_top($parent_userID, $user_pack, $user_registered, $user_package_activation);
        $is_membership = 1;
    }

    // Display Stripe cancellation option if applicable
    if ($is_membership == 1) {
        $stripe_profile_user    = get_user_meta($userID, 'stripe', true);
        $subscription_id        = get_user_meta($userID, 'stripe_subscription_id', true);
        $enable_stripe_status   = esc_html(wpresidence_get_option('wp_estate_enable_stripe', ''));

            if ($stripe_profile_user != '' && $subscription_id != '' && $enable_stripe_status === 'yes') {  
                echo '<div id="stripe_cancel" data-bs-toggle="tooltip" title="' . esc_attr__('subscription will be cancelled at the end of current period', 'wpresidence') . '" data-stripeid="' . esc_attr($userID) . '">' . esc_html__('cancel stripe subscription', 'wpresidence') . '</div>';
                $ajax_nonce = wp_create_nonce("wpestate_stripe_cancel_nonce");
                echo '<input type="hidden" id="wpestate_stripe_cancel_nonce" value="' . esc_attr($ajax_nonce) . '" />';
            }
   

        // Display package information for the main user
        if ($userID == $parent_userID) {
        ?>
            <div class="pack_description">
                <div id="open_packages" class="wrapper_packages">
                    <?php echo esc_html__('See Available Packages and Payment Methods', 'wpresidence'); ?>
                    <i class="fas fa-angle-up" aria-hidden="true"></i>
                </div>
            </div>

            <div class="pack_description_row">
                <div class="add-estate profile-page profile-onprofile row">
                    <div class="pack-unit">
                        <div class="pack_description_unit_head">
                            <h4><?php echo esc_html__('Packages Available', 'wpresidence'); ?></h4>
                        </div>

                        <?php
                        // Prepare user role information for package query
                        $user_roles = $current_user->roles;

                        // Define role labels
                        $roles_map = wpresidence_rolemap();
                       // Get the first matching user role label
                        $user_role_label = '';
                        foreach ( $user_roles as $role_slug ) {
                            if ( isset($roles_map[$role_slug]) ) {
                                $user_role_label = $role_slug;
                      
                            }
                        }

                        // Set up user role meta query if we found a matching label
                        $user_role_meta_query = array();
                        if ( $user_role_label !== '' ) {
                            $user_role_meta_query = array(
                                'key'     => 'pack_visible_user_role',
                                'value'   => $user_role_label,
                                'compare' => 'LIKE',
                            );
                        }
                        // Set up currency information
                        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_submission_curency', ''));
                        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

                        // Query for available packages
                        $args = array(
                            'post_type'      => 'membership_package',
                            'posts_per_page' => -1,
                            'meta_query'     => array(
                                array(
                                    'key'     => 'pack_visible',
                                    'value'   => 'yes',
                                    'compare' => '=',
                                ),
                            )
                        );

                        if ($user_role_meta_query != '') {
                            $args['meta_query'][] = $user_role_meta_query;
                        }

                        $pack_selection = new WP_Query($args);

                        // Display available packages
                        while ($pack_selection->have_posts()) {
                            $pack_selection->the_post();
                        
                            include(locate_template('templates/dashboard-templates/dashboard-unit-templates/pack-unit.php'));
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>

            <div class="pack_description_row">
                <div class="add-estate profile-page profile-onprofile row">
                    <div class="pack-unit">
                        <?php
                        global $wpestate_global_payments;
                        if ($wpestate_global_payments->is_woo != 'yes') {
                        ?>
                            <div class="pack_description_unit_head">
                                <h4><?php echo esc_html__('Payment Method', 'wpresidence'); ?></h4>
                            </div>
                        <?php } ?>

                        <div id="package_pick">
                            <?php
                            if ($wpestate_global_payments->is_woo == 'yes') {
                                $wpestate_global_payments->show_button_pay('', '', '', 0, 5);
                            } else {
                            ?>
                                <div class="recuring_wrapper">
                                    <input type="checkbox" name="pack_recuring" id="pack_recuring" value="1" style="display:block;" />
                                    <label for="pack_recurring"><?php esc_html_e('make payment recurring ', 'wpresidence'); ?></label>
                                </div>

                                <?php
                                $enable_paypal_status = esc_html(wpresidence_get_option('wp_estate_enable_paypal', ''));
                                $enable_stripe_status = esc_html(wpresidence_get_option('wp_estate_enable_stripe', ''));
                                $enable_direct_status = esc_html(wpresidence_get_option('wp_estate_enable_direct_pay', ''));

                                if ($enable_paypal_status === 'yes') {
                                    echo '<div id="pick_pack"></div>';
                                }
                                if ($enable_stripe_status === 'yes') {
                                    wpestate_show_stripe_form_membership();
                                }
                                if ($enable_direct_status === 'yes') {
                                    echo '<div id="direct_pay" class="">' . esc_html__('Wire Transfer', 'wpresidence') . '</div>';
                                }
                            }

                            $ajax_nonce = wp_create_nonce("wpresidence_simple_pay_actions_nonce");
                            echo '<input type="hidden" id="wpresidence_simple_pay_actions_nonce" value="' . esc_attr($ajax_nonce) . '" />';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } 
    } ?>
</div>