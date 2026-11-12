<?php


/**
 * Determines and displays the appropriate header media type for singular pages.
 *
 * It takes the header type and post ID as parameters and renders the corresponding header media based on the specified type.
 * The function supports various header types including images, sliders, Google Maps, and custom headers
 * for specific post types like agencies and developers.
 *
 * @param int $header_type The type of header to display, determined by theme settings or post meta.
 * @param int $postID The ID of the current post or page.
 */
if(!function_exists('wpresidence_header_media_type_for_singular')):
    function wpresidence_header_media_type_for_singular($header_type,$postID){
        switch ($header_type) {
                case 1://none
                    // No header media is displayed
                    break;
                case 2://image
                    // Displays a custom image header
                    $custom_image       =   esc_html(get_post_meta($postID, 'page_custom_image', true));
                    wpestate_header_image($custom_image);
                    break;
                case 3://theme slider
                    // Displays the theme's built-in slider
                    wpestate_present_theme_slider();
                    break;
                case 4://revolutin slider
                    // Displays a Revolution Slider if the plugin is active
                    if(function_exists('putRevSlider')){
                     
                        $rev_slider         =   esc_html( esc_html(get_post_meta($postID, 'rev_slider', true)) );
                        putRevSlider($rev_slider);
                    }
                    break;
                case 5://google maps
                    // Displays Google Maps, but only if not on a half map page
                   if(  !wpestate_half_map_conditions ($postID) ){       
                        include( locate_template('templates/google_maps_base.php' ) );
                    }
                    break;
                case 6:
                    // Displays a video header
                    wpestate_video_header();
                    break;
                case 7://google maps
                    // Displays a header specific to taxonomy pages
                    include( locate_template('templates/header_taxonomy.php') );
                    break;
                case 20:
                    // Displays a header for splash pages
                    wpestate_splash_page_header();
                    break;
                case 21;
                    // Displays a header specific to agency pages
                    include( locate_template ('templates/agency_templates/header_agency.php') );
                    break;
                case 22;
                    // Displays a header specific to developer pages
                    include( locate_template ('templates/developer_templates/header_developer.php') );
                    break;        
                case 11;
                    // Displays a property slider tour
                    include( locate_template ('templates/property_slider_tour.php') );
                    break;
            }
    }
endif;



/**
 * Renders the global header media based on the specified header type and context.
 *
 * This function is responsible for displaying different types of headers in the WP Residence theme.
 * It supports various header types including no header, image header, theme slider, revolution slider,
 * and Google Maps integration. The function adapts its behavior based on whether the current page
 * is an archive page or not.
 *
 * @param int $global_header_type The type of header to be displayed. Possible values are:
 *                                0 - No header
 *                                1 - Image header
 *                                2 - Theme slider
 *                                3 - Revolution slider
 *                                4 - Google Maps
 * @param string $global_header   (Optional) URL of the global header image. This parameter is not used
 *                                within the function and can be omitted in future refactors.
 * @param bool $is_archive        Indicates whether the current page is an archive page.
 *
 * @return void This function does not return a value; it outputs the header directly.
 */
if(!function_exists('wpresidence_header_media_global_header')):
    function wpresidence_header_media_global_header($is_archive,$is_singular_post,$is_singular_property,$global_header_type) {
        switch ($global_header_type) {
            case 0:
                // No header is displayed
                // This case might be used when you want to completely disable the header
                break;

            case 1:
                // Image header
                // Different image sources are used for archive and non-archive pages
                
                $global_header_image =   wpresidence_get_option('wp_estate_global_header', 'url');

                if ($is_archive) {
            
                    include( locate_template('templates/header_taxonomy.php') );

                } else {
                    // For non-archive pages
                    $global_header_image = wpresidence_get_option('wp_estate_global_header', 'url');
                    if($is_singular_post){
                        $global_header_image = wpresidence_get_option('wp_estate_header_blog_post_image', 'url');
                    }else if($is_singular_property){
                        $global_header_image = wpresidence_get_option('wp_estate_header_property_page_image', 'url');                  
                    }

                    // Display the header image using a theme-specific function
                    wpestate_header_image($global_header_image);
                }

              
                break;

            case 2:
                // Theme slider
                // This case uses a custom theme slider, likely defined elsewhere in the theme
                wpestate_present_theme_slider();
                break;

            case 3:
                // Revolution slider
                // This case integrates with the Revolution Slider plugin, if available
                if (function_exists('putRevSlider')) {
                    // Different slider IDs are used for archive and non-archive pages
                    if ($is_archive) {
                        // For archive pages, use the taxonomy-specific revolution slider
                        $global_revolution_slider = wpresidence_get_option('wp_estate_header_taxonomy_revolution_slider', '');
                    } else {
                        // For non-archive pages, use the global revolution slider
                        $global_revolution_slider = wpresidence_get_option('wp_estate_global_revolution_slider', '');
                        if($is_singular_post){
                            $global_revolution_slider = wpresidence_get_option('wp_estate_header_blog_post_revolution_slider', '');
                        }else if($is_singular_property){
                            $global_revolution_slider = wpresidence_get_option('wp_estate_header_property_page_revolution_slider', '');
                        }

                    }
                    
                    // Display the Revolution Slider
                    putRevSlider($global_revolution_slider);
                }
                // Note: There's no else condition here. If putRevSlider function doesn't exist,
                // no action is taken and no header will be displayed.
                break;

            case 4:
                // Google Maps
                // This case integrates a Google Maps view as the header
                // The actual map rendering logic is contained in a separate template file
                include(locate_template('templates/google_maps_base.php'));
                break;

            // Note: There's no default case. If an unexpected $global_header_type is provided,
            // the function will silently do nothing. Consider adding error logging for unexpected types.
        }
    }
endif;








/**
 * Main function to generate and display the header image for WPEstate theme.
 * 
 * This function determines the type of page being displayed and generates
 * the appropriate header image HTML. It handles different scenarios such as
 * category/taxonomy pages, splash pages, and regular pages with custom headers.
 *
 * @param string $image The URL of the header image.
 * @return void Echoes the generated HTML.
 */
if (!function_exists('wpestate_header_image')):
    function wpestate_header_image($image) {
        global $post;
        $paralax_header = wpresidence_get_option('wp_estate_paralax_header', '');

        if (is_category() || is_tax() || is_archive()) {
            echo wpestate_get_simple_header_image($image);
        } elseif (isset($post->ID)) {
            $header_data = wpestate_get_header_data($post->ID);
            echo wpestate_get_custom_header_image($image, $header_data, $paralax_header);
        } else {
            echo wpestate_get_simple_header_image($image);
        }
    }

endif;



/**
 * Generates a simple header image HTML.
 * 
 * This function is used for category, taxonomy, or archive pages where
 * a basic header image is sufficient.
 *
 * @param string $image The URL of the header image.
 * @return string The HTML for a simple header image.
 */
if (!function_exists('wpestate_get_simple_header_image')):
    function wpestate_get_simple_header_image($image) {
        return '<div class="wpestate_header_image" style="background-image:url(' . esc_url($image) . ')"></div>';
    }
endif;



/**
 * Retrieves header data based on the post ID.
 * 
 * This function determines whether the current page is a splash page or a regular page
 * and calls the appropriate function to get the header data.
 *
 * @param int $post_id The ID of the current post/page.
 * @return array An array containing header data.
 */
if (!function_exists('wpestate_get_header_data')):
    function wpestate_get_header_data($post_id) {
        if (is_page_template('page-templates/splash_page.php')) {
            return wpestate_get_splash_page_data();
        } else {
            return wpestate_get_regular_page_data($post_id);
        }
    }

endif;



/**
 * Retrieves header data for splash pages.
 * 
 * Splash pages have specific settings that are different from regular pages.
 * This function retrieves those settings from the theme options.
 *
 * @return array An array of header data specific to splash pages.
 */
if (!function_exists('wpestate_get_splash_page_data')):
    function wpestate_get_splash_page_data() {
        return array(
            'header_type' => 20,
            'image' => esc_html(wpresidence_get_option('wp_estate_splash_image', 'url')),
            'img_full_screen' => 'no',
            'img_full_back_type' => '',
            'page_header_title' => stripslashes(esc_html(wpresidence_get_option('wp_estate_splash_page_title', ''))),
            'page_header_subtitle' => stripslashes(esc_html(wpresidence_get_option('wp_estate_splash_page_subtitle', ''))),
            'page_header_image_height' => 600,
            'page_header_overlay_val' => esc_html(wpresidence_get_option('wp_estate_splash_overlay_opacity', '')),
            'page_header_overlay_color' => esc_html(wpresidence_get_option('wp_estate_splash_overlay_color', '')),
            'wp_estate_splash_overlay_image' => esc_html(wpresidence_get_option('wp_estate_splash_overlay_image', 'url')),
        );
    }
endif;


/**
 * Retrieves header data for regular pages.
 * 
 * This function checks the header type of the page and calls the appropriate
 * function to get either global or custom header data.
 *
 * @param int $post_id The ID of the current post/page.
 * @return array An array of header data for regular pages.
 */
if (!function_exists('wpestate_get_regular_page_data')):
    function wpestate_get_regular_page_data($post_id) {
        $header_type = get_post_meta($post_id, 'header_type', true);

        if ($header_type == 0) {
            return wpestate_get_global_header_data();
        } else {
            return wpestate_get_custom_header_data($post_id);
        }
    }
endif;






/**
 * Retrieves global header data.
 * 
 * When a page uses the global header type, this function retrieves
 * the default header settings from the theme options.
 *
 * @return array An array of global header data.
 */
if (!function_exists('wpestate_get_global_header_data')):    
    function wpestate_get_global_header_data() {
        return array(
            'img_full_screen' => esc_html(wpresidence_get_option('wp_estate_global_header', 'url')),
            'img_full_back_type' => '',
            'page_header_title' => '',
            'page_header_subtitle' => '',
            'page_header_image_height' => '',
            'page_header_overlay_val' => '',
            'page_header_overlay_color' => '',
            'wp_estate_splash_overlay_image' => '',
        );
    }
endif;


/**
 * Retrieves custom header data for a specific page.
 * 
 * When a page has custom header settings, this function retrieves
 * those settings from the page's meta data.
 *
 * @param int $post_id The ID of the current post/page.
 * @return array An array of custom header data for the specified page.
 */
if (!function_exists('wpestate_get_custom_header_data')): 
    function wpestate_get_custom_header_data($post_id) {
        return array(
            'img_full_screen' => esc_html(get_post_meta($post_id, 'page_header_image_full_screen', true)),
            'img_full_back_type' => esc_html(get_post_meta($post_id, 'page_header_image_back_type', true)),
            'page_header_title' => stripslashes(esc_html(get_post_meta($post_id, 'page_header_title_over_image', true))),
            'page_header_subtitle' => stripslashes(esc_html(get_post_meta($post_id, 'page_header_subtitle_over_image', true))),
            'page_header_image_height' => floatval(get_post_meta($post_id, 'page_header_image_height', true)),
            'page_header_overlay_val' => esc_html(get_post_meta($post_id, 'page_header_overlay_val', true)),
            'page_header_overlay_color' => esc_html(get_post_meta($post_id, 'page_header_overlay_color', true)),
            'wp_estate_splash_overlay_image' => '',
        );
    }
endif;




/**
 * Generates the HTML for a custom header image.
 * 
 * This function combines various components (style, class, overlay, heading)
 * to create the complete HTML structure for a custom header image.
 *
 * @param string $image The URL of the header image.
 * @param array $header_data An array containing header settings.
 * @param string $paralax_header The parallax effect setting.
 * @return string The complete HTML for the custom header image.
 */
if (!function_exists('wpestate_get_custom_header_image')): 
    function wpestate_get_custom_header_image($image, $header_data, $paralax_header) {
        $header_data = wpestate_apply_default_values($header_data);
        $style = wpestate_get_header_style($image, $header_data);
        $class = wpestate_get_header_class($header_data, $paralax_header);

        $output = "<div class=\"" . esc_attr($class) . "\" style=\"" . esc_attr($style) . "\">";
        $output .= wpestate_get_overlay_html($header_data);
        $output .= wpestate_get_heading_html($header_data);
        $output .= '</div>';

        return $output;
    }

endif;





/**
 * Applies default values to header data.
 * 
 * This function ensures that certain header settings have default values
 * if they are not set, preventing potential errors.
 *
 * @param array $header_data The original header data.
 * @return array The header data with default values applied where necessary.
 */
if (!function_exists('wpestate_apply_default_values')): 
    function wpestate_apply_default_values($header_data) {
        $header_data['page_header_overlay_val'] = $header_data['page_header_overlay_val'] ?: 1;
        $header_data['page_header_image_height'] = $header_data['page_header_image_height'] ?: 580;
        return $header_data;
    }
endif;




/**
 * Generates the style attribute for the header image div.
 * 
 * This function creates the CSS styles for the header image,
 * including the background image, height, and size settings.
 *
 * @param string $image The URL of the header image.
 * @param array $header_data An array containing header settings.
 * @return string The complete style attribute for the header div.
 */
if (!function_exists('wpestate_get_header_style')): 
    function wpestate_get_header_style($image, $header_data) {
        $style = "background-image:url(" . esc_url($image) . ");";
        if ($header_data['page_header_image_height'] != 0) {
            $style .= " height:{$header_data['page_header_image_height']}px;";
        }
        if ($header_data['img_full_back_type'] == 'contain') {
            $style .= " background-size: contain;";
        }
        return $style;
    }
endif;



/**
 * Generates the class attribute for the header image div.
 * 
 * This function creates the CSS classes for the header image,
 * including full-screen and parallax effect settings.
 *
 * @param array $header_data An array containing header settings.
 * @param string $paralax_header The parallax effect setting.
 * @return string The complete class attribute for the header div.
 */
if (!function_exists('wpestate_get_header_class')): 
    function wpestate_get_header_class($header_data, $paralax_header) {
        $full_screen = isset($header_data['img_full_screen']) ? $header_data['img_full_screen'] : '';
        
        return esc_attr("wpestate_header_image full_screen_" . sanitize_html_class($full_screen) . " parallax_effect_" . sanitize_html_class($paralax_header));
 
    }
endif;





/**
 * Generates the HTML for the header overlay.
 * 
 * If an overlay color or image is specified in the header data,
 * this function creates the HTML for that overlay.
 *
 * @param array $header_data An array containing header settings.
 * @return string The HTML for the header overlay, or an empty string if no overlay is needed.
 */
if (!function_exists('wpestate_get_overlay_html')): 
    function wpestate_get_overlay_html($header_data) {
        $overlay_color = isset($header_data['page_header_overlay_color']) ? $header_data['page_header_overlay_color'] : '';
        $overlay_image = isset($header_data['wp_estate_splash_overlay_image']) ? $header_data['wp_estate_splash_overlay_image'] : '';
        $overlay_opacity = isset($header_data['page_header_overlay_val']) ? $header_data['page_header_overlay_val'] : '';

        if ($overlay_color !== '' || $overlay_image !== '') {
            $style = '';
            if ($overlay_color !== '') {
                $style .= 'background-color:#' . esc_attr($overlay_color) . ';';
            }
            if ($overlay_opacity !== '') {
                $style .= 'opacity:' . esc_attr($overlay_opacity) . ';';
            }
            if ($overlay_image !== '') {
                $style .= 'background-image:url(' . esc_url($overlay_image) . ');';
            }

            return '<div class="wpestate_header_image_overlay" style="' . esc_attr($style) . '"></div>';
        }
        return '';
    }
endif;    





/**
 * Generates the HTML for the header title and subtitle.
 * 
 * If a header title is specified in the header data, this function
 * creates the HTML for the title and (if present) the subtitle.
 *
 * @param array $header_data An array containing header settings.
 * @return string The HTML for the header title and subtitle, or an empty string if no title is set.
 */

if (!function_exists('wpestate_get_heading_html')): 
    function wpestate_get_heading_html($header_data) {
        $title     = isset($header_data['page_header_title']) ? $header_data['page_header_title'] : '';
        $subtitle  = isset($header_data['page_header_subtitle']) ? $header_data['page_header_subtitle'] : '';

        if ($title !== '') {
            $output = '<div class="heading_over_image_wrapper">';
            $output .= '<h1 class="heading_over_image">' . esc_html($title) . '</h1>';
            
            if ($subtitle !== '') {
                $output .= '<div class="subheading_over_image exclude-rtl">' . esc_html($subtitle) . '</div>';
            }
            
            $output .= '</div>';
            return $output;
        }
        return '';
    }
endif;




if (!function_exists('wpestate_check_google_map_tax')):
    /**
     * Determines whether to display a Google Map for taxonomy pages in the WPResidence theme.
     * 
     * This function is crucial for the theme's property listing display logic. It checks various
     * conditions to decide if a Google Map should be shown on taxonomy archive pages.
     * 
     * The function considers:
     * 1. The taxonomy header type setting
     * 2. Whether it's a taxonomy page
     * 3. The property list type setting
     * 4. If the current taxonomy is a valid property-related taxonomy
     * 5. Whether a featured image is set for the term
     *
     * @return bool False if a Google Map should be displayed, true if it should not be displayed.
     */
    function wpestate_check_google_map_tax() {
        // Retrieve the header type setting for taxonomy pages
        // This setting determines how the header of taxonomy pages should be displayed
        $tax_header = wpresidence_get_option('wp_estate_header_type_taxonomy', '');
       
        // Check if we're on a taxonomy page and if the header type is set to 4
        // If true, we don't display the Google Map
        if (is_tax() && $tax_header == 4) {
            return false;
        }

        // Define an array of valid property-related taxonomies
        // This optimization improves readability and makes it easier to manage the list of taxonomies
        $valid_taxonomies = [
            'property_category',
            'property_action_category',
            'property_city',
            'property_area',
            'property_county_state'
        ];
       
        // Check if we're on a taxonomy page and the property list type is set to 1
        // The property list type setting affects how properties are displayed
        if (is_tax() && intval(wpresidence_get_option('wp_estate_property_list_type', '')) == 1) {
            // Get the current taxonomy
            $taxonmy = get_query_var('taxonomy');

            // Check if the current taxonomy is in our list of valid property taxonomies
            // This uses in_array() for efficient checking, especially if the list grows
            if (in_array($taxonmy, $valid_taxonomies, true)) {
                // Retrieve the current term (category, city, etc.)
                $term = get_query_var('term');
                
                // Get the term data object
                $term_data = get_term_by('slug', $term, $taxonmy);
                
                // Get the term ID
                $place_id = $term_data->term_id;
                
                // Retrieve the term meta data
                // This uses get_option() which might be for backward compatibility or specific to WPResidence
                $term_meta = get_option("taxonomy_$place_id");
             
                // Check if a featured image is set for this term
                if (isset($term_meta['category_featured_image']) && $term_meta['category_featured_image'] != '') {
                    // If a featured image is set, we don't display the Google Map
                    return true; // no google map
                }
            }
        }

        // If none of the above conditions are met, we display the Google Map
        return false;
    }
endif;