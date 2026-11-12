<?php
/** MILLDONE
 * Template for displaying a property unit in saved searches
 * src: templates\property_cards_templates\property_unit_saved_search.php
 * This template is part of the WpResidence theme and is used to show
 * a compact view of a property, typically in email notifications or
 * saved search results.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since WpResidence 1.0
 */

// Get the property ID
$prop_id = get_the_ID();

// Retrieve property details
$property_size = wpestate_get_converted_measure($prop_id, 'property_size');
$property_bedrooms = get_post_meta($prop_id, 'property_bedrooms', true);
$property_bathrooms = get_post_meta($prop_id, 'property_bathrooms', true);

// Get currency settings
$wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

// Get property title and link
$title = get_the_title();
$link = esc_url(get_permalink());

// Get property thumbnail
$image = wpestate_return_property_card_thumb_email($prop_id, 'slider_thumb');

// Define common styles
$text_style = 'color: #718096;font-size:14px;';
?>

<div style="display:flex;margin-bottom:10px;gap:10px;">
    <div style="max-width:143px;">
        <a href="<?php echo $link; ?>" target="_blank">
            <img style="max-width:143px;" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
        </a>
    </div>
    <div style="width:100%;padding-left:10px;">
        <div style="width:100%;<?php echo $text_style; ?>font-size:15px;">
            <?php wpestate_show_price($prop_id, $wpestate_currency, $where_currency); ?>
        </div>
        <div style="width:100%;">
            <a href="<?php echo $link; ?>" target="_blank" style="font-size:15px;font-weight:600;color:#211465!important;text-decoration:none;">
                <?php echo esc_html($title); ?>
            </a>
        </div>
        <div style="width:100%;display:flex;line-height:1em;gap:10px;">
            <?php
            // Display bedrooms if available
            if (!empty($property_bedrooms) && $property_bedrooms != 0) {
                printf(
                    '<div style="margin-right:10px;%s">%s %s</div>',
                    $text_style,
                    esc_html($property_bedrooms),
                    esc_html__('BD', 'wpresidence')
                );
            }
            
            // Display bathrooms if available
            if (!empty($property_bathrooms) && $property_bathrooms != 0) {
                printf(
                    '<div style="margin-right:10px;%s">%s %s</div>',
                    $text_style,
                    esc_html($property_bathrooms),
                    esc_html__('BA', 'wpresidence')
                );
            }
            ?>
        </div>
    </div>
</div>
