<?php
/** MILLDONE
 * Template for displaying property listings with a half map layout
 * src: templates\properties_list_templates\property_list_page_half_map_core.php
 * This template is part of the WpResidence theme and handles the display of
 * property listings alongside a map view.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since WpResidence 1.0
 */

// Initialize variables
$wpestate_custom_unit_structure = wpresidence_get_option('wpestate_property_unit_structure');
$wpestate_uset_unit             = intval(wpresidence_get_option('wpestate_uset_unit', ''));
$wpestate_no_listins_per_row    = intval(wpresidence_get_option('wp_estate_listings_per_row', ''));
$wpestate_property_unit_slider  = wpresidence_get_option('wp_estate_prop_list_slider', '');
$top_bar_style                  = "";

if (esc_html(wpresidence_get_option('wp_estate_show_top_bar_user_menu', '')) == "no") {
    $top_bar_style = ' half_no_top_bar ';
}

$logo_header_type = wpresidence_get_option('wp_estate_logo_header_type', '');
include(locate_template('templates/properties_list_templates/filters_templates/property_list_ajax_tax_hidden_filters.php'));

$property_card_type = intval(wpresidence_get_option('wp_estate_unit_card_type'));
$property_card_type_string = ($property_card_type == 0) ? '' : '_type' . $property_card_type;

// Determine which column class we will use
$property_unit_class = wpestate_return_unit_class($wpestate_options);

// Open in new page option
$new_page_option = wpresidence_get_option('wp_estate_unit_card_new_page', '');


$map_orientation_class = wpresidence_map_map_orientation_class();
$show_mobile = 1;
$show_compare_only = 'yes';
$show_half_map_search = wpresidence_get_option('wp_estate_show_half_map_search', 'yes');
$half_map_search_version = wpresidence_get_option('wp_estate_half_map_search_version', '1');
$half_map_default_view = wpresidence_get_option('wp_estate_half_map_search_map_type', 'grid');
$is_half_map_list_view = ($half_map_default_view === 'list');

if ($half_map_search_version === '1') {
    $half_map_search_template = get_theme_file_path('templates/advanced_search/advanced_search_type_half.php');
} else {
    $half_map_search_template = get_theme_file_path('templates/advanced_search/advanced_search_type_half_' . intval($half_map_search_version) . '.php');
}

$half_map_search_template = file_exists($half_map_search_template) ? $half_map_search_template : '';
?>

<div class="d-flex flex-column flex-md-row align-self-start w-100" style="flex:0!important">
  <?php
    if ($show_half_map_search !== 'no' && $half_map_search_version !== '1' && $half_map_search_template !== '') {
        include $half_map_search_template;
    }
    ?>
</div>


<div class="d-flex flex-column flex-md-row wpestate_overlfow wpresidence_property_list_half_map_core <?php echo esc_attr($map_orientation_class ); ?> ">
   
    <div id="google_map_prop_list_wrapper"
         class="col-12 col-md-6 half_mobile_hide google_map_prop_list <?php echo esc_attr($top_bar_style . ' half_' . $logo_header_type); ?> "
         <?php echo $is_half_map_list_view ? 'style="display:none;"' : ''; ?>>
        <?php include(locate_template('templates/google_maps_base.php')); ?>
    </div>

    <div id="google_map_prop_list_sidebar"
         class="col-12 <?php echo esc_attr($is_half_map_list_view ? 'half_type1 col-md-12' : 'col-md-6'); ?> <?php echo esc_attr($top_bar_style . ' half_' . $logo_header_type); ?> ">
        <?php if ($half_map_search_template !== '' && $show_half_map_search !== 'no' && $half_map_search_version === '1') : ?>
            <div class="search_wrapper">
                <?php
                include $half_map_search_template;
                include(get_theme_file_path('templates/advanced_search/property_list_filter_half.php'));
                ?>
            </div>
        <?php else : ?>

                <?php include(get_theme_file_path('templates/advanced_search/property_list_filter_half.php')); ?>
         
        <?php endif; ?>

        <?php
        while (have_posts()) : the_post(); ?>
            <div class="single-content">
            
            <?php
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) == 'yes') {
                echo '<h1 class="entry-title title_prop">' . get_the_title() . '</h1>';
            } 
            ?>
            </div>
        <?php    
        endwhile;
        ?>  

        <?php get_template_part('templates/spiner'); ?> 
        
        <div id="listing_ajax_container" class="ajax-map row"> 
            <?php
            $counter = 0;
           
            wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'property_list');

         
            ?>
        </div>
        <!-- Listings Ends  here --> 
        
        <div class="half-pagination">
            <?php wpestate_pagination($prop_selection->max_num_pages, $range = 2); ?>       
        </div>    
    </div><!-- end 8col container-->
</div>

<div class="half_map_controllers_wrapper d-md-none d-block">
    <div class="half_mobile_toggle_listings half_control_visible"><i class="fas fa-bars"></i><?php esc_html_e('Listings', 'wpresidence'); ?></div>
    <div class="half_mobile_toggle_map"><i class="far fa-map"></i> <?php esc_html_e('Map View', 'wpresidence'); ?></div>
</div>