<?php
/** 
 * Property Card Title Template
 * src: templates\property_cards_templates\property_card_details_templates\property_card_title.php
 * This template is responsible for displaying the title of a property
 * on property cards in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since 1.0
 *
 * @param string$wpresidence_property_cards_context['new_page_option'] The option for opening links in a new page.
 *                                This should be passed to the template to avoid repeated DB queries.
 */
/**
 * Define allowed HTML tags for the title
 * This array specifies which HTML tags are allowed in the property title
 * for security reasons when using wp_kses().
 */
$allowed_html = array(
    'br'     => array(),
    'em'     => array(),
    'strong' => array(),
    'b'      => array(),
);

// Get property ID from cached data or fallback to post ID
$prop_id = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'ID');

/**
 * Retrieve the property title
 * This function likely formats the title specifically for property cards
 */
$title = wpestate_return_property_card_title_from_cache($property_unit_cached_data,$postID);

/**
 * Get the permalink for the current property using the utility function
 */
$link = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink');

/**
 * Set the target attribute for the title link
 * If the new page option is not '_self', set the target attribute to open in a new tab/window
 */
$target = $wpresidence_property_cards_context['new_page_option'] === '_self' ? '' : sprintf('target="%s"', esc_attr($wpresidence_property_cards_context['new_page_option']));
?>
<h4>
  <a href="<?php echo esc_url($link); ?>" <?php echo $target; ?>><?php echo wp_kses($title, $allowed_html); ?></a>
</h4>