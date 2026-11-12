<?php
/**
 * Property Card Second Price Template
 * Similar to property_card_price.php, but shows the secondary price
 * using: property_second_price, property_label_before_second_price, property_second_price_label
 */

// Currency symbol and position
$currency_symbol   = wpresidence_get_option('wp_estate_currency_symbol', '');
$currency_position = wpresidence_get_option('wp_estate_where_currency_symbol', '');

// Secondary price and labels
$second_price  = floatval( get_post_meta( $postID, 'property_second_price', true ) );
$label         = esc_html( get_post_meta( $postID, 'property_second_price_label', true ) );
$label_before  = esc_html( get_post_meta( $postID, 'property_label_before_second_price', true ) );
?>
<div class="listing_unit_price_wrapper ">
    <?php
    // If a numeric second price exists, use theme formatter for the second price
    if ( $second_price != 0 ) {
        // Leverage theme formatter for secondary price (5th param "yes"), same as in title section
        echo wp_kses_post( wpestate_show_price( $postID, $currency_symbol, $currency_position, 1, 'yes' ) );
    } else {
        // Fallback to labels if numeric value is not set
        echo '<span class="price_label price_label_before">' . esc_html( $label_before ) . '</span>'
            . '<span class="price_label">' . esc_html( $label ) . '</span>';
    }
    ?>
</div>

