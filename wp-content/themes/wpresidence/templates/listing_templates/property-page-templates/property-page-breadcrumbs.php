<?php
/** MILLDONE
 * WpResidence Property Breadcrumbs
 * src: templates\listing_templates\property-page-templates\property-page-breadcrumbs.php
 * This template file is responsible for rendering the breadcrumbs on a property page
 * in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @version 1.0
 * 
 * @uses wpresidence_get_option()
 * @uses get_the_term_list()
 * @uses get_the_title()
 * 
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific functions and variables
 * 
 * Expected global variables:
 * - $post
 * - $property_id (optional, from custom page builder)
 */

// Fetch breadcrumb settings
$enable_show_breadcrumbs = wpresidence_get_option('wp_estate_show_breadcrumbs');
$wpestate_property_breadcrumbs = wpresidence_get_option('wpestate_property_breadcrumbs');

// Determine the post ID
$postID = isset($property_id) ? $property_id : $post->ID;

// Force showing breadcrumbs for Elementor blocks if $property_id is set
if (isset($property_id)) {
    $enable_show_breadcrumbs = 'yes';
}

// Only proceed if breadcrumbs are enabled
if ($enable_show_breadcrumbs !== 'yes') {
    return;
}

$referrer = wp_get_referer();
$referrerID = 0;
if ( $referrer )    {
    $referrerID = url_to_postid( $referrer );
}
$isSearch = false;
if ( (get_option( 'page_on_front' ) == $referrerID && strpos( $referrer, '?s=' ) !== false) || strpos( $referrer, 'search' ) !== false || wpestate_is_search_page($referrerID) )  {
    $isSearch = true;
}
$referrerHTML = '';
// Add Back to search if referrer is search results
if ( $isSearch ) {
    $referrerHTML .= sprintf(
        '<li class=""><a href="%s">%s</a></li>',
        esc_url(wp_get_referer()),
        esc_html__('Back to Search', 'wpresidence')
    );
}

// Generate custom breadcrumb items
$item_custom = '';
if (is_array($wpestate_property_breadcrumbs) && !empty($wpestate_property_breadcrumbs)) {
    foreach ($wpestate_property_breadcrumbs as $taxonomy) {
        $terms = get_the_term_list($postID, $taxonomy, '', ', ', '');
        if ($terms) {
            $item_custom .= sprintf('<li>%s</li>', $terms);
        }
    }
}
?>

<div class="col-xs-12 col-md-12 breadcrumb_container">
    <ol class="breadcrumb">
        <?php 
        if ( $referrerHTML )  {
            echo wp_kses_post( $referrerHTML );
        }
        ?>
        <li>
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'wpresidence'); ?></a>
        </li>
        <?php 
        if ($item_custom) {
            echo wp_kses_post($item_custom);
        }
        ?>
        <li class="active">
            <?php echo esc_html(get_the_title($postID)); ?>
        </li>
    </ol>
</div>