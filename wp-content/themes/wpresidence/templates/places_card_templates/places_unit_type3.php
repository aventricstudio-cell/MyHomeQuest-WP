<?php
/** MILLDONE
 * WPResidence Theme - Places Unit Type 3 INLINE Template
 * src: templates\places_card_templates\places_unit_type3.php
 * This template is responsible for rendering individual place listings
 * of type 3 inline in the WPResidence theme. It displays place information including
 * name, image, and listing count in a square design.
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0.0
 */


// Ensure $place_id is an integer
$place_id = intval($place_id);
$term = get_term($place_id);

// Initialize category data array
$category_data = array(
    'attach_id'          => '',
    'featured_image'     => '',
    'featured_image_url' => '',
    'tagline'            => '',
    'name'               => $term->name,
    'count'              => $term->count,
    'description'        => $term->description,
    'tax'                => '',
    
);

// Get term link
$term_link = get_term_link($term); 
$term_link = !is_wp_error($term_link) ? $term_link : '';

// Retrieve term meta data
$term_meta = get_option("taxonomy_$place_id");

// Process featured image
if (!empty($term_meta['category_featured_image'])) {
    $category_data['featured_image'] = $term_meta['category_featured_image'];
}

if (!empty($term_meta['category_attach_id'])) {
    $category_data['attach_id'] = $term_meta['category_attach_id'];
    $featured_image = wp_get_attachment_image_src($category_data['attach_id'], 'property_full');
    $category_data['featured_image_url'] = $featured_image[0] ?? '';
}



// Get category tagline
$category_data['tagline'] = isset($term_meta['category_tagline']) ? wp_kses_post(stripslashes($term_meta['category_tagline'])) : '';



// Set default featured image if not available
if (empty($category_data['featured_image_url'])) {
    $category_data['featured_image_url'] = get_theme_file_uri('/img/defaults/default_property_listings.jpg');
}

// Prepare inline styles
$inline_style = sprintf('background-image: url(%s);', esc_url($category_data['featured_image_url']));

// Prepare listing count text
$listing_count_text = sprintf(
    _n('%d listing', '%d listings', $category_data['count'], 'wpresidence'),
    $category_data['count']
);

?>
<div class="<?php echo esc_attr($places_class['col_class']); ?> listing_wrapper  places_wrapper_type_3" 
     data-org="<?php echo esc_attr($places_class['col_org']); ?>"
     <?php echo !empty($item_height_style) ? esc_attr($item_height_style) : ''; ?>>
    <div class="places_square_backgorund_image" 
         data-link="<?php echo esc_url($term_link); ?>"
         style="<?php echo esc_attr(trim($inline_style)); ?>">
        <div class="places_cover" data-link="<?php echo esc_url($term_link); ?>"></div>
    </div>
   
    <div class="property_listing_square_details">
        <h4>
            <a href="<?php echo esc_url($term_link); ?>">
                <?php echo esc_html(wp_trim_words($category_data['name'], 7, '...')); ?>
            </a>
        </h4>
        <div class="property_location_type_3">
            <?php echo esc_html($listing_count_text); ?>
        </div>
    </div>    
</div>