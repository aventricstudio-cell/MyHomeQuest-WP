<?php
/** MILLDONE
 * Directory Template
 * src: property_list_directory.php
 * This template is part of the WpResidence theme and displays a directory of property listings
 * with filtering options and pagination.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0
 */

// Retrieve query variables and initialize filters
$taxonomy = get_query_var('taxonomy');
$term = get_query_var('term');

$selected_order = esc_html__('Sort by', 'wpresidence');
$listing_filter = '';
$listings_list = '';

if (isset($post->ID)) {
    $listing_filter = get_post_meta($post->ID, 'listing_filter', true);
}

$listing_filter_array = wpestate_listings_sort_options_array();

$args = wpestate_get_select_arguments();

$selected_order_num = '';
foreach ($listing_filter_array as $key => $value) {
    $listings_list .= '<li role="presentation" data-value="' . esc_attr($key) . '">' . esc_html($value) . '</li>';

    if ($key == $listing_filter) {
        $selected_order = $value;
        $selected_order_num = $key;
    }
}

$order_class = '';

// Start of HTML output
?>

<div class="wpresidence-content-container-wrapper wpestate_column_contentcol-12 d-flex flex-wrap">
    <?php get_template_part('templates/breadcrumbs'); ?>
    <div class="order-2 wpestate_column_content <?php echo esc_attr($wpestate_options['content_class']); ?>">
        
        <div class="single-content" style="margin-bottom:20px;">
            <?php 
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) == 'yes') {
                echo '<h1 class="entry-title title_prop">' . get_the_title() . '</h1>';
            }
            ?>
            <div class="directory_content_wrapper">       
                <?php
                $page_object = get_page($post->ID);
             
                if (!is_null($page_object->post_content)) {
                    $content = apply_filters('the_content', $page_object->post_content);
                    echo do_shortcode($content);
                }
                
                ?>
            </div>     
        </div>

        <!-- Filters Section -->
        <div class="listing_filters_head_directory"> 
            <input type="hidden" id="page_idx" value="<?php echo (!is_tax() && !is_category()) ? intval($post->ID) : ''; ?>">

         

            <?php
  echo wpestate_build_dropdown_for_filters('a_filter_order_directory', $selected_order_num, $selected_order, $listings_list);
       




            $prop_unit_list_class = '';
            $prop_unit_grid_class = 'icon_selected';
            if ($wpestate_prop_unit == 'list') {
                $prop_unit_grid_class = "";
                $prop_unit_list_class = "icon_selected";
            }
            ?>
            <?php wpestate_render_list_grid_toggle($prop_unit_grid_class, $prop_unit_list_class); ?>
        </div>
        <!-- End of Filters Section -->
  
        <!-- Listings Section -->
        <div id="listing_ajax_container" class="row "> 
            <input type="hidden" id="tax_categ_picked" value="<?php echo esc_attr($tax_categ_picked); ?>">
            <input type="hidden" id="tax_action_picked" value="<?php echo esc_attr($tax_action_picked); ?>">
            <input type="hidden" id="tax_city_picked" value="<?php echo esc_attr($tax_city_picked); ?>">
            <input type="hidden" id="taxa_area_picked" value="<?php echo esc_attr($taxa_area_picked); ?>">
            
            <?php
            $show_compare_only = 'yes';
            $counter = 0;
            
            wpresidence_display_property_list_as_html($prop_selection, $wpestate_options, 'directory_list');
            ?>
        </div>
        <?php get_template_part('templates/spiner'); ?> 
            
        <?php if ($prop_selection->have_posts()) : ?>
            <div id="directory_load_more" class="wpresidence_button"><?php esc_html_e('Load More Listings', 'wpresidence') ?></div>  
        <?php endif; ?>
        <!-- End of Listings Section -->
        
        <?php   
        if (!is_tax()) {
            echo '<div class="single-content">';
            $property_list_second_content = get_post_meta($post->ID, 'property_list_second_content', true);
            echo do_shortcode($property_list_second_content);
            echo '</div>';
        }
        ?>
           
        <?php 
        if (wp_is_mobile()) {
            ?>
            <div class="col-xs-12 <?php echo esc_attr($wpestate_options['sidebar_class']); ?> order-1 widget-area-sidebar" id="primary">
                <?php if (is_active_sidebar($wpestate_options['sidebar_name'])) : ?>
                    <ul class="xoxo">
                        <?php dynamic_sidebar($wpestate_options['sidebar_name']); ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php 
        } 
        ?>    
    </div><!-- end 9col container-->

<?php include(locate_template('templates/directory_page_templates/directory_filters.php')); ?>
</div>