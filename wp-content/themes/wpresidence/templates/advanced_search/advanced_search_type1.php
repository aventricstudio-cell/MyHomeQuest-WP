<?php
/**
 * Initialize advanced search parameters
 * 
 * @var string $adv_search_what         Advanced search fields
 * @var string $show_adv_search_visible Visibility of advanced search
 * @var string $close_class             CSS class for closed search
 * @var string $extended_search         Extended search option
 * @var string $extended_class          CSS class for extended search
 */
$adv_search_what         = wpresidence_get_option('wp_estate_adv_search_what', '');
$show_adv_search_visible = wpresidence_get_option('wp_estate_show_adv_search_visible', '');
$extended_search         = wpresidence_get_option('wp_estate_show_adv_search_extended', '');




$close_class   = ($show_adv_search_visible == 'no') ? ' float_search_closed ' : '';
$extended_class = '';

if ($extended_search == 'yes') {
    $extended_class = 'adv_extended_class';
    if ($show_adv_search_visible == 'no') {
        $close_class = 'adv-search-1-close-extended';
    }
}


/**
 * Advanced Search Type 1 Form
 * 
 * This form allows users to perform advanced property searches.
 */
?>
<div class="adv-search-1 container <?php echo esc_attr($close_class . ' ' . $extended_class); ?>" id="adv-search-1">
    <h3 class="adv-search-header-1"><?php esc_html_e('Advanced Search', 'wpresidence'); ?></h3>
    <form role="search" method="get" id="adv_search_form" class="row" action="<?php echo esc_url($adv_submit); ?>">
        <?php
        // Add WPML language field if WPML is active
        if (function_exists('icl_translate')) {
            do_action('wpml_add_language_form_field');
        }
        ?>
        
        <div class="adv1-holder row gx-2 gy-2">
            <?php
            // Advanced search fields will be populated here
            wpestate_render_advanced_search_fields($adv_search_what, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $select_county_state_list);
            ?>
        </div>

        <button type="submit" class="wpresidence_button residence_advanced_submit_button_search_1">
            <?php esc_html_e('SEARCH PROPERTIES', 'wpresidence'); ?>
        </button>

        <?php include(locate_template('templates/preview_template.php')); ?>
    </form>
</div>