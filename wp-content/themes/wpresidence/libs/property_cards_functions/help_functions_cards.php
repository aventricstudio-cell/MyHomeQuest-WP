<?php


/**
*
*
* Return sorting options for listings
*
*
*/
if(!function_exists('wpestate_listings_sort_options_array')):
function wpestate_listings_sort_options_array(){

    $listing_filter_array=array(
        "1"=>esc_html__('Price High to Low','wpresidence'),
        "2"=>esc_html__('Price Low to High','wpresidence'),
        "3"=>esc_html__('Newest first','wpresidence'),
        "4"=>esc_html__('Oldest first','wpresidence'),
        "11"=>esc_html__('Newest Edited','wpresidence'),
        "12"=>esc_html__('Oldest Edited ','wpresidence'),
        "5"=>esc_html__('Bedrooms High to Low','wpresidence'),
        "6"=>esc_html__('Bedrooms Low to high','wpresidence'),
        "7"=>esc_html__('Bathrooms High to Low','wpresidence'),
        "8"=>esc_html__('Bathrooms Low to high','wpresidence'),
        "0"=>esc_html__('Default','wpresidence')
    );
    return $listing_filter_array;
}
endif;








if( !function_exists('wpestate_interior_classes') ):
function wpestate_interior_classes($wpestate_uset_unit){
    $return='';
    if($wpestate_uset_unit==1) {
        $return= 'property_listing_custom_design';
    }
    return $return;
}
endif;


if (!function_exists('wpestate_render_list_grid_toggle')):
    /**
     * Render the grid/list view toggle used in property filters.
     *
     * @param string $grid_class             CSS class to apply on the grid toggle.
     * @param string $list_class             CSS class to apply on the list toggle.
     * @param string $county_value           Optional county value attached as data attribute.
     * @param bool   $list_first             Whether to render the list toggle before the grid toggle.
     * @param string $list_container_class   Extra class for the list toggle container.
     * @param string $grid_container_class   Extra class for the grid toggle container.
     */
    function wpestate_render_list_grid_toggle($grid_class, $list_class, $county_value = '', $list_first = false, $list_container_class = '', $grid_container_class = '')
    {
        $list_container_classes = trim('listing_filter_select listing_filter_views list_filter_wiew ' . $list_container_class);
        $grid_container_classes = trim('listing_filter_select listing_filter_views grid_filter_wiew ' . $grid_container_class);

        ?>
        <div class="wpestate_list_grid_filter_wiew_wrapper">
            <?php if ($list_first) { ?>
                <div class="<?php echo esc_attr($list_container_classes); ?>">
                    <div id="list_view" class="<?php echo esc_attr($list_class); ?>">
                        <?php include(locate_template('templates/svg_icons/list-icon.svg')); ?>
                    </div>
                </div>

                <div class="<?php echo esc_attr($grid_container_classes); ?>">
                    <div id="grid_view" class="<?php echo esc_attr($grid_class); ?>">
                        <?php include(locate_template('templates/svg_icons/grid-icon.svg')); ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="<?php echo esc_attr($grid_container_classes); ?>">
                    <div id="grid_view" class="<?php echo esc_attr($grid_class); ?>">
                        <?php include(locate_template('templates/svg_icons/grid-icon.svg')); ?>
                    </div>
                </div>

                <div class="<?php echo esc_attr($list_container_classes); ?>">
                    <div id="list_view" class="<?php echo esc_attr($list_class); ?>">
                        <?php include(locate_template('templates/svg_icons/list-icon.svg')); ?>
                    </div>
                </div>
            <?php } ?>

            <div data-toggle="dropdown" id="a_filter_county" class="" data-value="<?php echo esc_attr($county_value); ?>"></div>
        </div>
        <?php
    }
endif;






?>
