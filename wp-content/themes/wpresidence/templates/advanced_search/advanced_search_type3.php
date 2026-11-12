<?php
/**
 * Advanced Search Type 3
 * 
 * This template handles the display of the advanced search form (type 3).
 * It assumes that necessary variables and select lists are already available
 * from the parent file that includes this template.
 */

/**
 * Initialize advanced search parameters
 * 
 * @var array  $adv_search_what        Advanced search fields
 * @var string $show_adv_search_visible Visibility of advanced search
 * @var string $close_class            CSS class for closed search
 * @var string $extended_search        Extended search option
 * @var string $extended_class         CSS class for extended search
 */
$adv_search_what         = wpresidence_get_option('wp_estate_adv_search_what', '');
$show_adv_search_visible = wpresidence_get_option('wp_estate_show_adv_search_visible', '');
$extended_search         = wpresidence_get_option('wp_estate_show_adv_search_extended', '');

$close_class   = ($show_adv_search_visible == 'no') ? 'adv-search-1-close' : '';
$extended_class = ($extended_search == 'yes') ? 'adv_extended_class' : '';

if ($show_adv_search_visible == 'no' && $extended_search == 'yes') {
    $close_class = 'adv-search-1-close-extended';
}

/**
 * Advanced Search Type 3 Form
 */
?>
<div class="adv-search-3" id="adv-search-3" data-postid="<?php echo intval($post_id); ?>">
    <div id="adv-search-header-3"><?php esc_html_e('Advanced Search', 'wpresidence'); ?></div>
    <div class="adv3-holder">
        <form role="search" method="get" class="row gx-2 gy-2" action="<?php echo esc_url($adv_submit); ?>">
            <?php
            // Add WPML language field if WPML is active
            if (function_exists('icl_translate')) {
                do_action('wpml_add_language_form_field');
            }

            // Render advanced search fields
            // the select lists passed from the parent file
            wpestate_render_advanced_search_fields($adv_search_what, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $select_county_state_list,'type3');
            ?>

            <div class="col-md-12">
                <input name="submit" type="submit" class="wpresidence_button" id="advanced_submit_3" value="<?php esc_html_e('Search', 'wpresidence'); ?>">
            </div>

            <?php include(locate_template('templates/preview_template.php')); ?>
        </form>
    </div>
</div>