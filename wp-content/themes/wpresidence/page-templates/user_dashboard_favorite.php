<?php
/** MILLDONE
 * Template Name: User Dashboard Favorite
 * src: page-templates\user_dashboard_favorite.php
 * This file is part of the WpResidence theme and handles the display of the user's
 * favorite properties in their dashboard.
 *
 * @package WpResidence
 * @subpackage UserDashboard
 * @since 1.0.0
 *
 * Dependencies: 
 * - WordPress core functions
 * - WpResidence theme functions (wpestate_dashboard_header_permissions, wpestate_return_favorite_listings_per_user, wpestate_show_dashboard_title)
 * - WpResidence template parts
 *
 * Usage:
 * This template is used to display the user's favorite properties in their dashboard within the WpResidence theme.
 */

// Ensure user has necessary permissions
wpestate_dashboard_header_permissions();

// Get current user information
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;
$curent_fav = wpestate_return_favorite_listings_per_user();
$is_dasboard_fav = true;
$edit_link = wpestate_get_template_link('page-templates/user_dashboard_favorite.php');
get_header();

$wpestate_options = get_query_var('wpestate_options');
?>

<div class="row row_user_dashboard">
    <?php include(locate_template('templates/dashboard-templates/dashboard-left-col.php')); ?>
    
    <div class="col-md-9 dashboard-margin row">
        <?php
        include(locate_template('templates/dashboard-templates/user_memebership_profile.php'));
        wpestate_show_dashboard_title(get_the_title());
        $agent_list[] = $current_user->ID;
        ?>
        <div class="dashboard-wrapper-form row">
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper">
                    <?php
                    $order_by = isset($_GET['orderby']) ? intval($_GET['orderby']) : '';
                    $status_value = isset($_GET['status']) ? intval($_GET['status']) : '';
                    
                    // Display table header
                    ?>
                    <div class="wpestate_dashboard_table_list_header d-none d-md-flex row">
                     
                        <?php
                        $paid_submission_status         =   esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
                        if ($paid_submission_status=='per listing'){
                            print '<div class="col-md-3">'.esc_html__('Property','wpresidence').'</div>';
                            print '<div class="col-md-2">'.esc_html__('Category','wpresidence').'</div>';
                            print '<div class="col-md-2">'.esc_html__('Status','wpresidence').'</div>';
                            print '<div class="col-md-2">'.esc_html__('Pay Status','wpresidence').'</div>';
                            print '<div class="col-md-1">'.esc_html__('Price','wpresidence').'</div>';
                        }else{
                            print '<div class="col-md-4">'.esc_html__('Property','wpresidence').'</div>';
                            print '<div class="col-md-2">'.esc_html__('Category','wpresidence').'</div>';
                            print '<div class="col-md-2">'.esc_html__('Status','wpresidence').'</div>';
                            print '<div class="col-md-2">'.esc_html__('Price','wpresidence').'</div>';
                        }
                    ?>
                    </div>
                    
                    <?php
                    if (!empty($curent_fav)) {
                        $args = array(
                            'post_type'        => 'estate_property',
                            'post_status'      => 'publish',
                            'posts_per_page'   => -1,
                            'post__in'         => $curent_fav
                        );
                        $prop_selection = new WP_Query($args);
                        
                        while ($prop_selection->have_posts()) : $prop_selection->the_post();
                            include(locate_template('templates/dashboard-templates/dashboard_listing_unit.php'));
                        endwhile;
                        
                        wp_reset_postdata();
                    } else {
                        echo '<h4>' . esc_html__('You don\'t have any favorite properties yet!', 'wpresidence') . '</h4>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>