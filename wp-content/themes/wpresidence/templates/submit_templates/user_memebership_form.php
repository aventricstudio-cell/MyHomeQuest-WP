<?php
/** MILLDONE
 * WpResidence Theme - User Membership and Submission Status Widget
 * src: templates/submit_templates/user_memebership_profile.php
 * This widget displays user membership information or paid submission details
 * based on the theme's settings. It's part of the WpResidence theme's dashboard functionality.
 *
 * @package WpResidence
 * @subpackage Widgets
 * @since 1.0.0
 */

if ( ! function_exists( 'wpresidence_user_membership_widget' ) ) :
/**
 * Displays user membership information or paid submission details.
 *
 * This function checks the submission status set in the theme options and displays
 * either membership details or paid submission information accordingly.
 *
 * @since 1.0.0
 *
 * @global WP_User $current_user Current logged-in WordPress user object.
 */
function wpresidence_user_membership_widget() {
    // Retrieve current user information
    $current_user = wp_get_current_user();
    $user_id      = $current_user->ID;
    $parent_user_id = wpestate_check_for_agency( $user_id );
    $user_login   = $current_user->user_login;

    // Retrieve user package information
    $user_pack              = get_the_author_meta( 'package_id', $parent_user_id );
    $user_registered        = get_the_author_meta( 'user_registered', $parent_user_id );
    $user_package_activation = get_the_author_meta( 'package_activation', $parent_user_id );

    // Get theme options
    $unit                    = esc_html( wpresidence_get_option( 'wp_estate_measure_sys', '' ) );
    $paid_submission_status  = esc_html( wpresidence_get_option( 'wp_estate_paid_submission', '' ) );

    // Display appropriate content based on submission status
    if ( 'membership' === $paid_submission_status ) {
        // Display membership information
        ?>
        <div class="submit_container">
            <div class="submit_container_header"><?php esc_html_e( 'Membership', 'wpresidence' ); ?></div>
            <?php wpestate_get_pack_data_for_user( $parent_user_id, $user_pack, $user_registered, $user_package_activation ); ?>
        </div>
        <?php
    } elseif ( 'per listing' === $paid_submission_status ) {
        // Retrieve paid submission details
        $price_submission          = floatval( wpresidence_get_option( 'wp_estate_price_submission', '' ) );
        $price_featured_submission = floatval( wpresidence_get_option( 'wp_estate_price_featured_submission', '' ) );
        $submission_currency_status = esc_html( wpresidence_get_option( 'wp_estate_submission_curency', '' ) );

        // Display paid submission information
        ?>
        <div class="submit_container">
            <div class="submit_container_header"><?php esc_html_e( 'Paid submission', 'wpresidence' ); ?></div>
            <div class="user_dashboard_box">
                <p class="full_form-nob"><?php esc_html_e( 'This is a paid submission.', 'wpresidence' ); ?></p>
                <p class="full_form-nob">
                    <?php 
                    esc_html_e( 'Price: ', 'wpresidence' );
                    echo '<span class="submit-price">' . esc_html( $price_submission . ' ' . $submission_currency_status ) . '</span>';
                    ?>
                </p>
                <p class="full_form-nob">
                    <?php 
                    esc_html_e( 'Featured (extra): ', 'wpresidence' );
                    echo '<span class="submit-price">' . esc_html( $price_featured_submission . ' ' . $submission_currency_status ) . '</span>';
                    ?>
                </p>
            </div>
        </div>
        <?php
    }
}
endif;

// Execute the widget function
wpresidence_user_membership_widget();
?>