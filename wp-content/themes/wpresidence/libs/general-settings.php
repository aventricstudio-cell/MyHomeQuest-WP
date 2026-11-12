<?php

/**
 * WPEstate Image Size Configuration
 * 
 * Registers custom image sizes for the theme based on default configurations
 * or user-defined settings from the admin panel.
 * 
 * @return void
 */
if( !function_exists('wpestate_image_size') ):
    function wpestate_image_size(){
        // Get default image size configurations
        $default_image_size = wpestate_return_default_image_size_theme();

        // Loop through each default image size
        foreach($default_image_size as $key=>$value ){
            $option_name = 'wp_estate_'.$key;
            // Get admin options
            $wpresidence_admin =  get_option('wpresidence_admin','') ;
    
           
            if(isset($wpresidence_admin[$option_name])){
                // Use custom user-defined dimensions if available
                $saved_option =$wpresidence_admin[$option_name];
                $crop=true;
                if( isset($saved_option['add_field_width']) && intval($saved_option['add_field_width'])> 0 &&
                    isset($saved_option['add_field_height']) && intval($saved_option['add_field_height'])> 0 ){  
                        // Set custom width and height from admin settings
                        $width = intval($saved_option['add_field_width']);
                        $height = intval($saved_option['add_field_height']);
                }else{
                    // Fall back to default dimensions
                    $width  =   $value['width'];
                    $height =   $value['height'];
                }

                // Check if cropping is disabled in settings
                if( isset($saved_option['add_field_width']) && $saved_option['add_field_crop']=='no' ){
                    $crop=false;
                }
            }else{
                // Use default dimensions if no custom settings exist
                $width  =   $value['width'];
                $height =   $value['height'];
                $crop   =   true;
            }
          //  print '</br> fac'.$key.' '.$width.' '.$height.' '.$crop;

            // Register the image size with WordPress
            add_image_size($key, $width, $height , $crop);
        }

       // Set the default post thumbnail size
       set_post_thumbnail_size(  250, 220, true);
    }
endif;


/**
 * WPEstate Default Image Size Definitions
 * 
 * Defines the default image sizes used throughout the theme with their
 * dimensions and cropping settings.
 * 
 * @return array Array of image size configurations
 */
function wpestate_return_default_image_size_theme(){
    $default_image_size = array(
     'user_picture_profile' => array(
         'name' => esc_html__('User profile picture', 'wpesidence-core'),
         'width' => 255,
         'height' => 143,
         'crop' => true,
     ),
     'agent_picture_thumb' => array(
         'name' => esc_html__('Agent picture thumb', 'wpesidence-core'),
         'width' => 120,
         'height' => 120,
         'crop' => true,
     ),
     'blog_thumb' => array(
         'name' => esc_html__('Blog thumb', 'wpesidence-core'),
         'width' => 272,
         'height' => 189,
         'crop' => true,
     ),
     'blog_unit' => array(
         'name' => esc_html__('Blog unit', 'wpesidence-core'),
         'width' => 1170,
         'height' => 405,
         'crop' => true,
     ),
     'slider_thumb' => array(
         'name' => esc_html__('Slider thumb', 'wpesidence-core'),
         'width' => 143,
         'height' => 83,
         'crop' => true,
     ),
     'property_featured_sidebar' => array(
         'name' => esc_html__('Property featured sidebar', 'wpesidence-core'),
         'width' => 768,
         'height' => 662,
         'crop' => true,
     ),
     'property_listings' => array(
         'name' => esc_html__('Property listings', 'wpesidence-core'),
         'width' => 525,
         'height' => 328,
         'crop' => true,
     ),
     'property_full' => array(
         'name' => esc_html__('Property full', 'wpesidence-core'),
         'width' => 980,
         'height' => 777,
         'crop' => true,
     ),
     'listing_full_slider' => array(
         'name' => esc_html__('Listing full slider', 'wpesidence-core'),
         'width' => 835,
         'height' => 467,
         'crop' => true,
     ),
     'listing_full_slider_1' => array(
         'name' => esc_html__('Listing full slider 1', 'wpesidence-core'),
         'width' => 1170,
         'height' => 656,
         'crop' => true,
     ),
     'property_featured' => array(
         'name' => esc_html__('Property featured', 'wpesidence-core'),
         'width' => 940,
         'height' => 390,
         'crop' => true,
     ),
     'property_full_map' => array(
         'name' => esc_html__('Property full map', 'wpesidence-core'),
         'width' => 1920,
         'height' => 790,
         'crop' => true,
     ),
     'widget_thumb' => array(
         'name' => esc_html__('Widget thumb', 'wpesidence-core'),
         'width' => 105,
         'height' => 70,
         'crop' => true,
     ),
     'user_thumb' => array(
         'name' => esc_html__('User thumb', 'wpesidence-core'),
         'width' => 45,
         'height' => 45,
         'crop' => true,
     ),
     'custom_slider_thumb' => array(
         'name' => esc_html__('Custom slider thumb', 'wpesidence-core'),
         'width' => 36,
         'height' => 36,
         'crop' => true,
     ),
     'post_thumbnail_size' => array(
         'name' => esc_html__('Post thumbnail size', 'wpesidence-core'),
         'width' => 250,
         'height' => 220,
         'crop' => true,
     ),
 );
 
    // Return the array of default image sizes
    return $default_image_size;
    
 }




 
/**
 * Custom excerpt length for WPResidence theme
 *
 * Sets the default excerpt length to 64 words.
 * 
 * @param int $length The default WordPress excerpt length
 * @return int Modified excerpt length (64 words)
 * @since 1.0.0
 * @hooks into excerpt_length filter
 */
if( !function_exists('wp_estate_excerpt_length') ):
    function wp_estate_excerpt_length($length) {
        return 64; // Return fixed length of 64 words for excerpts
    }
endif; // end   wp_estate_excerpt_length


/**
 * Custom excerpt "more" text for WPResidence theme
 *
 * Replaces the default WordPress [...] with a space followed by three dots.
 * 
 * @param string $more The default WordPress excerpt more string
 * @return string Modified excerpt more string (' ...')
 * @since 1.0.0
 * @hooks into excerpt_more filter
 */
if( !function_exists('wpestate_new_excerpt_more') ):
    function wpestate_new_excerpt_more( $more ) {
        return ' ...'; // Return space plus three dots instead of default [...]
    }
endif; // end   wpestate_new_excerpt_more




/**
 * Truncates text to a specific number of words
 *
 * Takes a text string and limits it to the specified number of words.
 * Unlike WordPress excerpt functions, this doesn't add any trailing characters.
 * 
 * @param string $text The text to truncate
 * @param int $words_no The maximum number of words to return
 * @return string The truncated text
 * @since 1.0.0
 */
if( !function_exists('wpestate_strip_words') ):
    function wpestate_strip_words($text, $words_no) {

        // Split text into array limited to $words_no + 1 elements
        $temp = explode(' ', $text, ($words_no + 1));
        // If we have more words than limit, remove the last element
        if (count($temp) > $words_no) {
            array_pop($temp);
        }
        return implode(' ', $temp); // Recombine words with spaces
          }
endif; // end   wpestate_strip_words




/**
 * Truncates text to a specific number of characters with a "read more" link
 *
 * Takes a text string and limits it to a specified number of characters,
 * then adds a "read more" link to the post if the text was truncated.
 * 
 * @param string $text The text to truncate
 * @param int $chars_no The maximum number of characters to display
 * @param int $post_id The ID of the post to link to
 * @param string $more Optional custom "read more" text
 * @return string The truncated text with a "read more" link if needed
 * @since 1.0.0
 */
if( !function_exists('wpestate_strip_excerpt_by_char') ):
    function wpestate_strip_excerpt_by_char($text, $chars_no,$post_id,$more='') {
    
        // Get substring limited to $chars_no characters
        $return_string  =mb_substr( $text,0,$chars_no);
            // If the text was truncated (original longer than limit)
            if(mb_strlen($text)>$chars_no){
                // Add default read more text if none provided
                if($more==''){
                    $return_string.= ' <a href="'.esc_url ( get_permalink($post_id)).'" class="unit_more_x">'.esc_html__(' ...','wpresidence').'</a>';
                }else{
                    // Add custom read more text if provided
                    $return_string.= ' <a href="'.esc_url(get_permalink($post_id)).'" class="unit_more_x">'.$more.'</a>';
                }

            }
        return $return_string;
        }

endif; // end   wpestate_strip_words



/**
 * Truncates text to a specific number of characters with a "read more" link - Cache compatible version
 *
 * Similar to wpestate_strip_excerpt_by_char but uses cached property data instead of querying the database
 * for the permalink. Improves performance when displaying multiple property cards.
 * 
 * @param string $text The text to truncate
 * @param int $chars_no The maximum number of characters to display
 * @param array $property_unit_cached_data Cached property data array including permalink
 * @param string $more Optional custom "read more" text
 * @return string The truncated text with a "read more" link if needed
 * @since 4.0.0
 */
if( !function_exists('wpestate_strip_excerpt_by_char_from_cache') ):
    function wpestate_strip_excerpt_by_char_from_cache($text, $chars_no, $property_unit_cached_data, $propID, $more='') {
   
        // Get substring limited to $chars_no characters
        $return_string = mb_substr($text, 0, $chars_no);
        
        // If the text was truncated (original longer than limit)
        if(mb_strlen($text) > $chars_no){
            // Get the permalink from cache or database fallback
            $permalink = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $propID, '', 'permalink');
            
            // Add default read more text if none provided
            if($more == ''){
                $return_string .= ' <a href="'.esc_url($permalink).'" class="unit_more_x">'.esc_html__(' ...','wpresidence').'</a>';
            } else {
                // Add custom read more text if provided
                $return_string .= ' <a href="'.esc_url($permalink).'" class="unit_more_x">'.$more.'</a>';
            }
        }
        
        return $return_string;
    }
endif; // end   wpestate_strip_words




/**
 * Truncates text to a specific number of characters with a "read more" link for place listings
 *
 * Special version of the excerpt truncation function specifically for place listings 
 * that requires a direct link URL rather than a post ID.
 * 
 * @param string $text The text to truncate
 * @param int $chars_no The maximum number of characters to display
 * @param string $link The full URL to link to
 * @return string The truncated text with a "read more" link if needed
 * @since 1.0.0
 */
if( !function_exists('wpestate_strip_excerpt_by_char_places') ):
    function wpestate_strip_excerpt_by_char_places($text, $chars_no,$link) {
        $return_string  = '';
        // Get substring limited to $chars_no characters
        $return_string  =  mb_substr( $text,0,$chars_no);
            // If the text was truncated (original longer than limit)
            if(mb_strlen($text)>$chars_no){
                $return_string.= ' <a href="'.esc_url($link).'" class="unit_more_x">'.esc_html__(' ...','wpresidence').'</a>';
            }
        return $return_string;
        }

endif; // end   wpestate_strip_words




/**
 * Adds responsive container divs around embedded content
 *
 * Wraps embedded content (like YouTube videos) in a responsive container
 * to ensure proper display on all screen sizes. Uses a special container
 * for Twitter embeds.
 * 
 * @param string $html The HTML embed code to wrap
 * @return string The wrapped HTML with responsive container divs
 * @since 1.0.0
 * @hooks into embed_oembed_html and video_embed_html filters
 */
if( !function_exists('wpestate_embed_html') ):
    function wpestate_embed_html( $html ) {
        // Special container for Twitter embeds
        if (strpos($html,'twitter') !== false) {
            return '<div class="video-container-tw">' . $html . '</div>';
        }else{
            // Standard container for all other embeds (like YouTube)
            return '<div class="video-container">' . $html . '</div>';
        }

    }
endif;
add_filter( 'embed_oembed_html', 'wpestate_embed_html', 10, 3 );
add_filter( 'video_embed_html', 'wpestate_embed_html' ); // Jetpack

/////////////////////////////////////////////////////////////////////////////////////////
///// html in conmment
/////////////////////////////////////////////////////////////////////////////////////////
//add_action('init', 'wpestate_html_tags_code', 10);

/**
 * Defines allowed HTML tags for comments and posts
 *
 * Sets up whitelists of allowed HTML tags and attributes for
 * comments and post content to enhance security.
 * Note: This function is currently commented out in the init hook.
 * 
 * @return void
 * @since 1.0.0
 */
if( !function_exists('wpestate_html_tags_code') ):
    function wpestate_html_tags_code() {

      global $allowedposttags, $allowedtags;
      // Define allowed tags for posts
      $allowedposttags = array(
          'strong' => array(), // Allow <strong> tags without attributes
          'em' => array(),     // Allow <em> tags without attributes
          'pre' => array(),    // Allow <pre> tags without attributes
          'code' => array(),   // Allow <code> tags without attributes
          'a' => array(        // Allow <a> tags with specific attributes
            'href' => array (), // Allow href attribute
            'title' => array (), // Allow title attribute
            'class'=>array(),    // Allow class attribute
            )
      );

      // Define allowed tags for comments (same as posts in this case)
      $allowedtags = array(
          'strong' => array(), // Allow <strong> tags without attributes
          'em' => array(),     // Allow <em> tags without attributes
          'pre' => array(),    // Allow <pre> tags without attributes
          'code' => array(),   // Allow <code> tags without attributes
          'a' => array(        // Allow <a> tags with specific attributes  
            'href' => array (), // Allow href attribute
            'title' => array (), // Allow title attribute
            'class'=>array(),    // Allow class attribute
          )
      );
    }
endif;




/**
 * Clears default widgets on theme activation
 * 
 * This function removes all widgets from specified sidebars when the theme is activated.
 * It prevents the theme from inheriting widgets from previously active themes.
 * 
 * @since 1.0.0
 * @hooks into after_switch_theme action
 * @return void
 */
add_action('after_switch_theme', 'wpestate_clear_default_widgets');
function wpestate_clear_default_widgets() {
    // Check if this is a child theme activation
    $current_theme = wp_get_theme();
    $parent_theme = $current_theme->parent();
      // Get the previously active theme
    $previous_theme = get_option('theme_switched');
    // Only proceed if this is not a child theme activation
    // or if it's the initial activation of the parent theme
    if (
        (!$parent_theme || $current_theme->get_template() != 'wpresidence') && 
        // Add this condition to prevent clearing when switching from child theme
        $previous_theme != 'wpresidence-child'
    ){
        // Get current sidebars and their widgets
        $sidebars_widgets = get_option('sidebars_widgets');
       
        // Clear specific sidebars
        $sidebars_to_clear = array(
            'primary-widget-area',
            'secondary-widget-area',
            'first-footer-widget-area',
            'second-footer-widget-area',
            'third-footer-widget-area',
            'fourth-footer-widget-area',
            'top-bar-left-widget-area',
            'top-bar-right-widget-area',
            'header4-widget-area',
            'dashboard-top-bar-left-widget-area',
            'dashboard-top-bar-right-widget-area',
            'splash-page_bottom-right-widget-area',
            'splash-page_bottom-left-widget-area'
        );
       
        // Loop through sidebars and clear each one
        foreach ($sidebars_to_clear as $sidebar) {
            if (isset($sidebars_widgets[$sidebar])) {
                $sidebars_widgets[$sidebar] = array();
            }
        }
       
        // Save updated sidebar settings
        update_option('sidebars_widgets', $sidebars_widgets);
       
        // Also clear widget settings for common widgets
        delete_option('widget_block');
        delete_option('widget_custom_html');
        delete_option('widget_media_image');
        delete_option('widget_text');
        delete_option('widget_search');
        delete_option('widget_ag_ag_dev_search_widget');
    }
}












/**
 * Registers navigation menus and sidebar widget areas
 * 
 * This function creates all the widget areas and navigation menus used
 * throughout the WPResidence theme. It registers the primary menu, mobile menu,
 * footer menu, and multiple sidebar and footer widget areas.
 * 
 * @since 1.0.0
 * @hooks into widgets_init action
 * @return void
 */
add_action( 'widgets_init', 'wpestate_widgets_init' );
if( !function_exists('wpestate_widgets_init') ):
function wpestate_widgets_init() {
    // Register navigation menus
    register_nav_menu( 'primary', esc_html__( 'Primary Menu', 'wpresidence' ) );
    register_nav_menu( 'mobile', esc_html__( 'Mobile Menu', 'wpresidence' ) );
    register_nav_menu( 'footer_menu', esc_html__( 'Footer Menu', 'wpresidence' ) );
    register_nav_menu( 'header_6_second_menu', esc_html__( 'Header 5 Second Menu', 'wpresidence' ) );
    
    // Register Primary Widget Area (main sidebar)
    register_sidebar(array(
        'name' => esc_html__('Primary Widget Area', 'wpresidence'),
        'id' => 'primary-widget-area',
        'description' => esc_html__('The primary widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-sidebar">',
        'after_title' => '</h3>',
    ));


    // Register Secondary Widget Area (secondary sidebar)
    register_sidebar(array(
        'name' => esc_html__('Secondary Widget Area', 'wpresidence'),
        'id' => 'secondary-widget-area',
        'description' => esc_html__('The secondary widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-sidebar">',
        'after_title' => '</h3>',
    ));


    // Register First Footer Widget Area
    register_sidebar(array(
        'name' => esc_html__('First Footer Widget Area', 'wpresidence'),
        'id' => 'first-footer-widget-area',
        'description' => esc_html__('The first footer widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h4 class="widget-title-footer">',
        'after_title' => '</h4>',
    ));


    // Register Second Footer Widget Area
    register_sidebar(array(
        'name' => esc_html__('Second Footer Widget Area', 'wpresidence'),
        'id' => 'second-footer-widget-area',
        'description' => esc_html__('The second footer widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h4 class="widget-title-footer">',
        'after_title' => '</h4>',
    ));


    // Register Third Footer Widget Area
    register_sidebar(array(
        'name' => esc_html__('Third Footer Widget Area', 'wpresidence'),
        'id' => 'third-footer-widget-area',
        'description' => esc_html__('The third footer widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h4 class="widget-title-footer">',
        'after_title' => '</h4>',
    ));


    // Register Fourth Footer Widget Area
    register_sidebar(array(
        'name' => esc_html__('Fourth Footer Widget Area', 'wpresidence'),
        'id' => 'fourth-footer-widget-area',
        'description' => esc_html__('The fourth footer widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h4 class="widget-title-footer">',
        'after_title' => '</h4>',
    ));


    // Register Top Bar Left Widget Area
    register_sidebar(array(
        'name' => esc_html__('Top Bar Left Widget Area', 'wpresidence'),
        'id' => 'top-bar-left-widget-area',
        'description' => esc_html__('The top bar left widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-topbar">',
        'after_title' => '</h3>',
    ));

    // Register Top Bar Right Widget Area
    register_sidebar(array(
        'name' => esc_html__('Top Bar Right Widget Area', 'wpresidence'),
        'id' => 'top-bar-right-widget-area',
        'description' => esc_html__('The top bar right widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-topbar">',
        'after_title' => '</h3>',
    ));
  

    // Register Header 3 Widget Area
    register_sidebar(array(
        'name' => esc_html__('Header 3 Widget Area', 'wpresidence'),
        'id' => 'header4-widget-area',
        'description' => esc_html__('Header 3 widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-header4">',
        'after_title' => '</h3>',
    ));


     // Register Dashboard Top Bar Left Widget Area
     register_sidebar(array(
        'name' => esc_html__('Dashboard Top Bar Left Widget Area', 'wpresidence'),
        'id' => 'dashboard-top-bar-left-widget-area',
        'description' => esc_html__('User Dashboard - The top bar left widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-topbar">',
        'after_title' => '</h3>',
    ));

    // Register Dashboard Top Bar Right Widget Area
    register_sidebar(array(
        'name' => esc_html__('Dashboard Top Bar Right Widget Area', 'wpresidence'),
        'id' => 'dashboard-top-bar-right-widget-area',
        'description' => esc_html__('User Dashboard - The top bar right widget area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-topbar">',
        'after_title' => '</h3>',
    ));

    // Register Splash Page Bottom Right Widget Area
    register_sidebar(array(
        'name' => esc_html__('Splash Page Bottom Right Widget Area', 'wpresidence'),
        'id' => 'splash-page_bottom-right-widget-area',
        'description' => esc_html__('Splash Page - Bottom right area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="splash_page_widget widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-topbar">',
        'after_title' => '</h3>',
    ));

    // Register Splash Page Bottom Left Widget Area
    register_sidebar(array(
        'name' => esc_html__('Splash Page Bottom Left Widget Area', 'wpresidence'),
        'id' => 'splash-page_bottom-left-widget-area',
        'description' => esc_html__('Splash Page - Bottom left area', 'wpresidence'),
        'before_widget' => '<li id="%1$s" class="splash_page_widget widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title-topbar">',
        'after_title' => '</h3>',
    ));
}
endif; // end   wpestate_widgets_init


/**
 * Clears default widgets from the "top-bar-right-widget-area" sidebar.
 *
 * This function hooks into the 'default_option_sidebars_widgets' filter
 * to prevent WordPress from auto-populating the "top-bar-right-widget-area"
 * with default widgets when the theme is activated or when the sidebar
 * has no widgets assigned.
 *
 * @param array $sidebars_widgets An associative array of all sidebars and their widgets.
 * @return array Modified sidebars widget array with the "top-bar-right-widget-area" cleared.
 */
function clear_default_widgets($sidebars_widgets) {
    // Clear default widgets for the specified sidebar
    if (isset($sidebars_widgets['top-bar-right-widget-area'])) {
        $sidebars_widgets['top-bar-right-widget-area'] = array();
    }
    return $sidebars_widgets;
}
add_filter('default_option_sidebars_widgets', 'clear_default_widgets');

?>
