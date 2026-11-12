<?php
/** MILLDONE
 * Dashboard Property Unit Template
 * src: templates\dashboard-templates\dashboard_listing_unit.php
 * This template displays individual property listings in the user dashboard of WpResidence theme.
 * It shows property details including featured status, image, location, type, status and actions.
 *
 * @package WpResidence
 * @subpackage Dashboard
 * @since 1.0
 * 
 * Dependencies:
 * - WpResidence Theme Options
 * - WordPress Core Functions
 * - Property Post Type
 * - Property Taxonomies (property_category, property_action_category, property_city, property_area)
 *
 * Template Variables Required:
 * - $edit_link: URL for editing the property
 * - $floor_link: URL for editing floor plans (optional)
 * - $is_dashboard_fav: Boolean indicating if this is the favorites dashboard
 */

// Fetch and sanitize property data
$post_id = get_the_ID();
$title = get_the_title();
$featured = intval(get_post_meta($post_id, 'prop_featured', true));
$preview = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'widget_thumb');
$edit_link = esc_url_raw(add_query_arg('listing_edit', $post_id, $edit_link));

// Handle floor link if it exists
if (isset($floor_link)) {
    $floor_link = esc_url_raw(add_query_arg('floor_edit', $post_id, $floor_link));
}

// Get property status and permalink
$post_status = get_post_status($post_id);
$link = get_permalink();

// Initialize payment related variables
$price_submission = floatval(wpresidence_get_option('wp_estate_price_submission', ''));
$price_featured_submission = floatval(wpresidence_get_option('wp_estate_price_featured_submission', ''));
$th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));

// Get property statistics and metadata
$no_views = intval(get_post_meta($post_id, 'wpestate_total_views', true));
$free_feat_list_expiration = intval(wpresidence_get_option('wp_estate_free_feat_list_expiration', ''));
$pfx_date = strtotime(get_the_date("Y-m-d", $post_id));
$expiration_date = $pfx_date + ($free_feat_list_expiration * 24 * 60 * 60);

// Set image column width based on submission status
$image_class = ($paid_submission_status == 'per listing') ? 3 : 4;

// Set default preview image if none exists
if (!isset($preview[0])) {
    $preview = array(get_theme_file_uri('/img/default_listing_105.png'));
}

$blog_listing_image_class= 'col-xl-4';
if ($paid_submission_status=='per listing'){
    $blog_listing_image_class= 'col-xl-3';
}
?>

<div class="row property_wrapper_dash flex-md-row flex-column">
    <!-- Property Image and Basic Info Section -->
    <div class="blog_listing_image col-12 col-md-12 col-lg-12 <?php echo esc_attr($blog_listing_image_class);?> col-md-<?php echo esc_attr($image_class); ?>">
        <?php if ($featured == 1) : ?>
            <div class="featured_div"><?php esc_html_e('Featured', 'wpresidence'); ?></div>
        <?php endif; ?>

        <a class="dashbard_unit_image" href="<?php echo esc_url($link); ?>">
            <img src="<?php echo esc_url($preview[0]); ?>" alt="<?php echo esc_attr($title); ?>" />
        </a>

        <div class="property_dashboard_location_wrapper">
            <a class="dashbard_unit_title" href="<?php echo esc_url($link); ?>">
                <?php echo esc_html($title); ?>
            </a>

            <div class="property_dashboard_location">
                <?php
                $property_location = get_the_term_list($post_id, 'property_city', '', ', ', '') . ', ' . 
                                   get_the_term_list($post_id, 'property_area', '', ', ', '');
                echo wp_kses_post(trim($property_location, ','));
                ?>
            </div>

            <div class="property_dashboard_status_unit">
                <?php
                if (!isset($is_dasboard_fav)) {
                    if ($paid_submission_status == 'membership' && isset($user_pack) && $user_pack == '') {
                        if (!is_wpresidence_developer_or_agency(get_the_author_meta('ID'))) {
                            $date_format = get_option('date_format');
                            esc_html_e('Expires on ', 'wpresidence');
                            echo esc_html(date($date_format, $expiration_date));
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Property Types Section -->
    <div class="col-md-2 property_dashboard_types">
        <?php
        $property_types = get_the_term_list($post_id, 'property_category', '', ', ', '') . ', ' . 
                         get_the_term_list($post_id, 'property_action_category', '', ', ', '');
        echo wp_kses_post(trim($property_types, ','));
        ?>
    </div>

    <!-- Status, Payment Status, and Price Sections -->
    <?php if ($paid_submission_status == 'per listing') : ?>
        <div class="col-md-2 property_dashboard_status">
            <?php include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard-unit-status.php')); ?>
        </div>
        <div class="col-md-2 property_dashboard_status">
            <?php include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard-unit-paystatus.php')); ?>
        </div>
        <div class="col-md-1 property_dashboard_price">
            <?php include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard-unit-price.php')); ?>
        </div>
    <?php else : ?>
        <div class="col-md-2 property_dashboard_status">
            <?php include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard-unit-status.php')); ?>
        </div>
        <div class="col-md-2 property_dashboard_price">
            <?php include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard-unit-price.php')); ?>
        </div>
    <?php endif; ?>

    <!-- Actions Section -->
    <div class="col-md-2 property_dashboard_action">
        <?php
        if (!isset($is_dasboard_fav)) {
            include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard-unit-actions.php'));
        } else {
            printf(
                '<div class="remove_fav_dash wpresidence_button" data-postid="%d">%s</div>',
                intval($post->ID),
                esc_html__('Remove From Favorites', 'wpresidence')
            );
        }
        ?>
    </div>

    <?php include(locate_template('templates/dashboard-templates/dashboard-unit-templates/per_listing_pay.php')); ?>
</div>