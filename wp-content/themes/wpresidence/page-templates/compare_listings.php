<?php
/** MILLDONE
 * Template Name: Compare Listings
 * src: page-templates/compare_listings.php
 * This template handles the display of property comparison in the WpResidence theme.
 * It processes selected properties and displays their details side by side for comparison.
 *
 * @package WpResidence
 * @subpackage CompareListings
 * @since 1.0.0
 */

// Check for WpResidence Core Plugin
if (!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin, Please activate it from the plugins menu!', 'wpresidence');
    exit();
}

get_header();
$wpestate_options = get_query_var('wpestate_options');

// Check if properties are selected for comparison
if (!isset($_POST['selected_id'])) {
    echo '<div class="nocomapare">' . esc_html__('Page should be accessible only via the compare button', 'wpresidence') . '</div>';
    exit();
}

// Sanitize and validate property IDs
$list_prop = array_map('intval', $_POST['selected_id']);
$list_prop = array_filter($list_prop, 'is_numeric');

if (empty($list_prop)) {
    echo '<div class="nocomapare">' . esc_html__('No valid properties selected for comparison', 'wpresidence') . '</div>';
    exit();
}

// Initialize variables
$unit = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));
$wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
$measure_sys = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));
$properties = array();
$id_array = array();

// Query properties
$args = array(
    'post_type'     => 'estate_property',
    'post_status'   => 'publish',
    'post__in'      => $list_prop
);

$prop_selection = new WP_Query($args);

// Process properties
while ($prop_selection->have_posts()): $prop_selection->the_post();
    $property = array();
    $id_array[] = get_the_ID();
    
    // Gather property details
    $property['link'] = esc_url(get_permalink());
    $property['title'] = get_the_title();
    $property['image'] = get_the_post_thumbnail(get_the_ID(), 'property_listings', array('class' => 'lazyload img-responsive'));
    $property['type'] = get_the_term_list(get_the_ID(), 'property_category', '', ', ', '');
    $property['property_city'] = get_the_term_list(get_the_ID(), 'property_city', '', ', ', '');
    $property['property_area'] = get_the_term_list(get_the_ID(), 'property_area', '', ', ', '');
    $property['property_zip'] = esc_html(get_post_meta(get_the_ID(), 'property_zip', true));
    $property['property_size'] = wpestate_get_converted_measure(get_the_ID(), 'property_size');
    $property['property_lot_size'] = wpestate_get_converted_measure(get_the_ID(), 'property_lot_size');
    $property['property_rooms'] = floatval(get_post_meta(get_the_ID(), 'property_rooms', true));
    $property['property_bedrooms'] = floatval(get_post_meta(get_the_ID(), 'property_bedrooms', true));
    $property['property_bathrooms'] = floatval(get_post_meta(get_the_ID(), 'property_bathrooms', true));
    $property['energy_index'] = get_post_meta(get_the_ID(), 'energy_index', true);
    $property['energy_class'] = get_post_meta(get_the_ID(), 'energy_class', true);
    $property['property_id'] = get_the_ID();
    
    // Get property price
    $property['price'] = (floatval(get_post_meta(get_the_ID(), 'property_price', true)) != 0) 
        ? wpestate_show_price(get_the_ID(), $wpestate_currency, $where_currency, 1) 
        : '';
    
    // Get property features
    $terms = wpestate_get_cached_terms('property_features');
    foreach ($terms as $term) {
        $property[$term->name] = has_term($term->name, 'property_features', get_the_ID()) ? 1 : 0;
    }
    
    $properties[] = $property;
endwhile;

wp_reset_query();
wp_reset_postdata();
?>

<div class="row wpresidence_page_content_wrapper">
    <?php get_template_part('templates/breadcrumbs'); ?>
    <?php get_template_part('templates/ajax_container'); ?>
    
    <h1 class="entry-title compare_title"><?php esc_html_e('Compare Listings', 'wpresidence'); ?></h1>
    <div class="p-0 p04mobile compare_wrapper row">
        
        <div class="compare_legend_head_wrapper flex-column flex-md-row ">
            <div class="compare_legend_head"></div>
        
            <?php
            // Display property headers
            foreach ($properties as $property) {
                ?>
                <div class="compare_item_head"> 
                    <a href="<?php echo esc_url($property['link']); ?>"><?php echo $property['image']; ?></a>
                    <h4><a href="<?php echo esc_url($property['link']); ?>"><?php echo esc_html($property['title']); ?></a></h4>
                    <div class="property_price"><?php echo $property['price']; ?></div>
                    <div class="article_property_type"><?php echo esc_html__('Type: ', 'wpresidence') . wp_kses_post($property['type']); ?></div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <?php
        // Define attributes to show
        $show_att = array(
            'property_city'     => esc_html__('City', 'wpresidence'),
            'property_area'     => esc_html__('Area', 'wpresidence'),
            'property_zip'      => esc_html__('Zip', 'wpresidence'),
            'property_size'     => esc_html__('Size', 'wpresidence'),
            'property_lot_size' => esc_html__('Lot Size', 'wpresidence'),
            'property_rooms'    => esc_html__('Rooms', 'wpresidence'),
            'property_bedrooms' => esc_html__('Bedrooms', 'wpresidence'),
            'property_bathrooms'=> esc_html__('Bathrooms', 'wpresidence'),
            'energy_index'      => esc_html__('Energy Index', 'wpresidence'),
            'energy_class'      => esc_html__('Energy Class', 'wpresidence'),
            'property_id'       => esc_html__('Property ID', 'wpresidence'),
        );
        
        // Display property attributes
        foreach ($show_att as $key => $value) {
            echo '<div class="compare_item flex-column flex-md-row ' . esc_attr($key) . '">';
            echo '<div class="compare_legend_head">' . esc_html($value) . '</div>';
            foreach ($properties as $property) {
                echo '<div class="prop_value">' . wp_kses_post($property[$key]) . '</div>';
            }
            echo '</div>';
        }
        
        // Display custom fields
        $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $field) {
                $name = $field[0];
                $label = $field[1];
                $slug = wpestate_limit45(sanitize_title($name));
                $slug = sanitize_key($slug);
                
                if (function_exists('icl_translate')) {
                    $label = icl_translate('wpestate', 'wp_estate_property_custom_' . esc_html($label), $label);
                }
                
                echo '<div class="compare_item flex-column flex-md-row  ' . esc_attr($slug) . '">';
                echo '<div class="compare_legend_head">' . wp_kses_post(stripslashes($label)) . '</div>';
                foreach ($id_array as $prop_id) {
                    echo '<div class="prop_value">' . esc_html(get_post_meta($prop_id, $slug, true)) . '</div>';
                }
                echo '</div>';
            }
        }
        
        // Display property features
        foreach ($terms as $term) {
            echo '<div class="compare_item flex-column flex-md-row  ' . esc_attr($term->name) . '">';
            echo '<div class="compare_legend_head">' . esc_html($term->name) . '</div>';
            foreach ($properties as $property) {
                echo '<div class="prop_value">';
                echo ($property[$term->name] == 1) 
                    ? '<i class="fas fa-check compare_yes"></i>'
                    : '<i class="fas fa-times compare_no"></i>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div><!-- end compare wrapper-->
</div>   

<?php get_footer(); ?>