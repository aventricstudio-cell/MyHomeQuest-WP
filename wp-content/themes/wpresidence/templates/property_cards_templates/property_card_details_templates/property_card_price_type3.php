<?php
/** MILLDONE
 * Template for displaying Property Card Price (Type 3)
 * src: templates\property_cards_templates\property_card_details_templates\property_card_price_type3.php
 * This file is part of the WpResidence theme and is used to render
 * the price section of a property card for Type 3 layout.
 */

// Set up necessary variables
$wpestate_currency  = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency     = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
$link               = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink');

// Determine the target attribute for the link
$target = (wpresidence_get_option('wp_estate_unit_card_new_page', '') == '_self') ? '' : ' target="' . esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page', '')) . '"';
?>

<div class="listing_unit_price_wrapper">
    <a href="<?php echo esc_url($link); ?>"<?php echo $target; ?>>
        <?php
        /**
         * Display the property price
         * 
         * The wpestate_show_price() function is responsible for formatting and outputting the price.
         * It takes into account the currency symbol and its position as defined in the theme options.
         */
      
        wpestate_show_price_from_cache($property_unit_cached_data, $wpestate_currency, $where_currency);
        ?>
    </a>
</div>