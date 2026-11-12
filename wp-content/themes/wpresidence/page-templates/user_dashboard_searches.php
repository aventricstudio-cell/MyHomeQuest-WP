<?php
/** MILLDONE
 * Template Name: User Dashboard Saved Searches
 * src: page-templates\user_dashboard_searches.php
 * This file is part of the WpResidence theme and handles the display of the user's
 * saved searches in their dashboard.
 *
 * @package WpResidence
 * @subpackage UserDashboard
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme functions (wpestate_dashboard_header_permissions, wpresidence_get_option, wpestate_return_favorite_listings_per_user, wpestate_show_dashboard_title)
 * - WpResidence template parts
 *
 * Usage:
 * This template is used to display the user's saved searches in their dashboard within the WpResidence theme.
 */

// Ensure user has necessary permissions
wpestate_dashboard_header_permissions();

// Get current user and theme options
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$wpestate_options = get_query_var('wpestate_options');

// Theme options
$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
$price_submission = floatval(wpresidence_get_option('wp_estate_price_submission', ''));
$submission_curency_status = esc_html(wpresidence_get_option('wp_estate_submission_curency', ''));
$wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
$custom_advanced_search = 'yes';
$adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
$adv_search_how = wpresidence_get_option('wp_estate_adv_search_how', '');
$adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');

// User specific data
$curent_fav = wpestate_return_favorite_listings_per_user();
$show_remove_fav = 1;
$show_compare = 1;
$show_compare_only = 'no';

get_header();
?>

<div class="row row_user_dashboard">
    <?php include(locate_template('templates/dashboard-templates/dashboard-left-col.php')); ?>
    
    <div class="col-md-9 dashboard-margin row">
        <?php
        include(locate_template('templates/dashboard-templates/user_memebership_profile.php'));
        wpestate_show_dashboard_title(get_the_title());
        ?>
        <div class="dashboard-wrapper-form row">
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper">
                    <?php
                    $args = array(
                        'post_type'      => 'wpestate_search',
                        'post_status'    => 'any',
                        'posts_per_page' => -1,
                        'author'         => $userID
                    );
                    $prop_selection = new WP_Query($args);
                    
                    if ($prop_selection->have_posts()) {
                        while ($prop_selection->have_posts()) : $prop_selection->the_post();
                            $searchID=get_the_ID();
                            include(locate_template('templates/dashboard-templates/search_unit.php'));
                        endwhile;
                        wp_reset_postdata();
                    } else {
                        echo '<div class="col-md-12 row_dasboard-prop-listing">';
                        echo '<h4>' . esc_html__('You don\'t have any saved searches yet!', 'wpresidence') . '</h4>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$ajax_nonce = wp_create_nonce("wpestate_searches_actions");
echo '<input type="hidden" id="wpestate_searches_actions" value="' . esc_attr($ajax_nonce) . '" />';

get_footer();
?>