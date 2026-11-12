<?php
/**
 * Taxonomy Header Image Display
 * 
 * @package     WPResidence
 * @subpackage  Taxonomy
 * @since       2.0.0
 * 
 * Description: This file handles the display of featured images and metadata 
 * for taxonomy terms (categories/locations) in WP Residence theme.
 * It retrieves term data, featured images, and displays a parallax header
 * with taxonomy information.
 * 
 * @global object $term_data WordPress term object
 */

// Initialize required variables with default empty values
$taxonomy_header_data = [
    'place_id'                    => 0,
    'category_attach_id'          => '',
    'category_tax'                => '',
    'category_featured_image'     => '',
    'category_name'               => '',
    'category_featured_image_url' => '',
    'category_tagline'            => '',
    'page_tax'                    => ''
];

// Get term data if not already set globally
global $term_data;
if (empty($term_data)) {
    $taxonomy   = get_query_var('taxonomy');
    $term       = get_query_var('term');
    $term_data  = get_term_by('slug', $term, $taxonomy);
}

// Get term ID and metadata
$place_id  = $term_data->term_id;
$term_meta = get_option("taxonomy_$place_id");

// Process featured image data
if (isset($term_meta['category_featured_image'])) {
    $taxonomy_header_data['category_featured_image'] = $term_meta['category_featured_image'];
}

// Handle attachment image processing
if (isset($term_meta['category_attach_id'])) {
    $taxonomy_header_data['category_attach_id'] = $term_meta['category_attach_id'];
    $category_featured_image = wp_get_attachment_image_src(
        $taxonomy_header_data['category_attach_id'], 
        'full'
    );
    $taxonomy_header_data['category_featured_image_url'] = $category_featured_image[0];
}

// Process taxonomy category data
if (isset($term_meta['category_tax'])) {
    $taxonomy_header_data['category_tax'] = $term_meta['category_tax'];
    $term = get_term($place_id, $taxonomy_header_data['category_tax']);
    
    if (isset($term->name)) {
        $taxonomy_header_data['category_name'] = $term->name;
    }
}

// Get additional metadata
if (isset($term_meta['category_tagline'])) {
    $taxonomy_header_data['category_tagline'] = stripslashes($term_meta['category_tagline']);
}

if (isset($term_meta['page_tax'])) {
    $taxonomy_header_data['page_tax'] = $term_meta['page_tax'];
}

// Get parallax header setting
$parallax_header = wpresidence_get_option('wp_estate_paralax_header', '');

// Fallback for empty featured image
if (empty($taxonomy_header_data['category_featured_image_url'])) {
    $taxonomy_header_data['category_featured_image_url'] = wpresidence_get_option(
        'wp_estate_header_taxonomy_image', 
        'url'
    );
}

// Display the header section if featured image exists
if (!empty($taxonomy_header_data['category_featured_image_url'])) {
    ?>
    <div class="listing_main_image parallax_effect_<?php echo esc_attr($parallax_header); ?>" 
         id="listing_main_image_photo" 
         style="background-image: url(<?php echo esc_attr($taxonomy_header_data['category_featured_image_url']); ?>)">
        <h1 class="entry-title entry-tax"><?php echo esc_html($term_data->name); ?></h1>
        <div class="tax_tagline"><?php echo esc_html($taxonomy_header_data['category_tagline']); ?></div>
        <div class="img-overlay"></div>
    </div>
    <?php
}