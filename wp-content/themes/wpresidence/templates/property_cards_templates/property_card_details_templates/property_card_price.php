<?php
/** MILLDONE
 * Property Card Price Template
 * src: templates\property_cards_templates\property_card_details_templates\property_card_price.php
 * This template is responsible for displaying the price of a property
 * on property cards in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since 1.0
 */


/**
 * Retrieve currency symbol and position
 */
$currency_symbol = wpresidence_get_option('wp_estate_currency_symbol', '');
$currency_position = wpresidence_get_option('wp_estate_where_currency_symbol', '');

/**
 * Display the property price
 */
?>
<div class="listing_unit_price_wrapper">
    <?php
    /**
     * Use the wpestate_show_price() function to display the formatted price
     * 
     * @param int    $postID          The ID of the current property post
     * @param string $currency_symbol   The currency symbol (e.g., '$', '€')
     * @param string $currency_position Where to display the currency symbol (before or after the price)
     */
    wpestate_show_price_from_cache($property_unit_cached_data, $currency_symbol, $currency_position);
    ?>
</div>