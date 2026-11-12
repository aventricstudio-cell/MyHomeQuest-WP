<?php
/**
 * WPResidence Theme - Places Unit Type 1 Template for Elementor Grid
 *
 * This template is responsible for rendering individual place listings
 * of type 1 in the WPResidence theme, specifically for the Elementor grid widget.
 * It displays place information including name, image, and listing count.
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0.0
 */

// Ensure $place_id is an integer
$place_id = intval($place_id);

// Initialize category data array
$category_data = array(
    'attach_id'          => '',
    'tax'                => '',
    'featured_image'     => '',
    'featured_image_url' => '',
    'name'               => '',
    'count'              => 0,
    'description'        => ''
);

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

// Process category data
if (!empty($term_meta['category_tax'])) {
    $category_data['tax'] = $term_meta['category_tax'];
    $term = get_term($place_id, $category_data['tax']);
    if ($term && !is_wp_error($term)) {
        $category_data['name'] = $term->name;
        $category_data['count'] = $term->count;
        $category_data['description'] = $term->description;
    }
}

// Get term link
$term_link = get_term_link($place_id, $category_data['tax']);
$term_link = !is_wp_error($term_link) ? $term_link : '';

// Prepare inline styles
$inline_style = !empty($category_data['featured_image_url']) 
    ? sprintf('background-image: url(%s);', esc_url($category_data['featured_image_url']))
    : 'background-color: #ddd;';

// Prepare listing count text
$listing_count_text = sprintf(
    _n('%d listing', '%d listings', $category_data['count'], 'wpresidence'),
    $category_data['count']
);



?>
<div class="listing_wrapper elementor_places_wrapper places_wrapper_type_1_grid" <?php echo !empty($item_height_style) ? esc_attr($item_height_style) : ''; ?>>
    <div class="property_listing places_wrapper_type_1 places_listing places_background_image" 
         data-link="<?php echo esc_url($term_link); ?>"
         style="<?php echo esc_attr(trim($inline_style)); ?>">
        <div class="places_cover" data-link="<?php echo esc_url($term_link); ?>"></div>
        <h4>
            <a href="<?php echo esc_url($term_link); ?>">
                <?php echo esc_html(wp_trim_words($category_data['name'], 7, '...')); ?>
            </a>
        </h4>
        <div class="property_location">
            <?php echo esc_html($listing_count_text); ?>
        </div>
    </div>
</div>