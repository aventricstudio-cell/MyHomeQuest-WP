<?php
/** MILLDONE
 * WPResidence Theme - Places Unit Type 3 Template for Elementor
 * src: templates\places_card_templates\places_unit_type3_elementor.php
 * This template is responsible for rendering individual place listings
 * of type 3 in the WPResidence theme, specifically for the Elementor grid widget.
 * It displays place information including name, image, and listing count.
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
    $category_data['tagline'] = $term_meta['category_tagline'] ?? '';
    
    $featured_image = wp_get_attachment_image_src($category_data['attach_id'], 'property_full');
    $category_data['featured_image_url'] = $featured_image[0] ?? '';
}





// Prepare inline styles
if (empty($category_data['featured_image_url'])) {
    $category_data['featured_image_url'] = get_theme_file_uri('/img/defaults/default_property_listings.jpg');
}

$inline_style = 'background-image: url(' . esc_url($category_data['featured_image_url']) . ');';
    
// Prepare listing count text
$listing_count_text = sprintf(
    _n('%d listing', '%d listings', $category_data['count'], 'wpresidence'),
    $category_data['count']
);

?>
<div class=" places_wrapper_type_4 places_unit_type4  elementor_places_wrapper <?php echo esc_attr($places_class['col_class']); ?>">
    <div class="places_background_image" style="<?php echo esc_attr(trim($inline_style)); ?>">
        <div class="places_cover" data-link="<?php echo esc_url($term_link); ?>"></div>
    </div>
    <div class="places_type_4_content">
        <h4>
            <a href="<?php echo esc_url($term_link); ?>">
                <?php echo esc_html(wp_trim_words($category_data['name'], 7, '...')); ?>
            </a>
        </h4>
        <div class="places_type_4_tagline">
            <?php // Uncomment the line below if you want to display the tagline
            // echo esc_html($category_data['tagline']);
            ?>
        </div>
        <div class="places_type_4_listings_no">
            <?php echo esc_html($listing_count_text); ?>
        </div>
    </div>
</div>