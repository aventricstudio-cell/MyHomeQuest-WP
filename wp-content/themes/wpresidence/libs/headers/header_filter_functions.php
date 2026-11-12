<?php
//MILLDONE
/*
* This block of code adds an action hook in WordPress.
* The action 'wp_body_open' is triggered after the opening <body> tag.
* This hook is useful for inserting content or executing functions
* right after the opening <body> tag.
 * Hook into 'wp_body_open' to add custom functionality or content
 * right after the opening <body> tag.
 */
add_action( 'wp_body_open', 'wpresidence_wp_body_open' );

/**
 * Check if the function 'wpresidence_wp_body_open' is not already defined.
 * This prevents function redeclaration errors.
 */
if(!function_exists('wpresidence_wp_body_open')):
    
    /**
     * Define the function 'wpresidence_wp_body_open'.
     * This function will be executed when the 'wp_body_open' action is triggered.
     * Currently, the function is empty and does not perform any actions.
     */
    function wpresidence_wp_body_open(){
        // Add your custom functionality here.
    }
    
endif;









/*
* This function outputs the HTML for the site logo, including a link to the home page or splash page.
* It also adds optional CSS classes and margin styles to the logo.
*/

if (!function_exists('wpestate_display_logo')):

    /**
     * Displays the site logo with optional custom classes and margin.
     *
     * @param string $logo URL of the logo image.
     * @param string $classes Optional CSS classes to add to the logo container.
     * @return string HTML for the logo.
     */
    function wpestate_display_logo($logo, $classes = '') {
        global $post;
        $page_template = '';

        // Check if the global $post object is set and get the page template if it exists.
        if (isset($post->ID)) {
            $page_template = get_post_meta($post->ID, '_wp_page_template', true);
            $page_template = ($page_template);
        }

        // Trigger an action before logo display starts
        do_action('wpresidence_before_logo_display', $logo, $classes);

        // Allow modification of the logo URL and classes through filters
        $logo = apply_filters('wpresidence_logo_url', $logo);
        $classes = apply_filters('wpresidence_logo_classes', $classes);

        // Initialize the return variable with the opening div and anchor tags.
        $return = '<div class="logo ' . esc_attr($classes) . '" >
            <a href="';

        // Check if the current page template is 'page-templates/splash_page.php' and if a splash page logo link is set.
        $splash_page_logo_link = wpresidence_get_option('wp_estate_splash_page_logo_link', '');
        if ($page_template == 'page-templates/splash_page.php' && $splash_page_logo_link != '') {
            // If on splash page and splash page logo link is set, use it as the href attribute.
            $return .= esc_url($splash_page_logo_link);
        } else {
            // Otherwise, use the home URL with login scheme as the href attribute.
            $return .= esc_url(home_url('', 'login'));
        }
        $return .= '">';

        // Check if a logo URL is provided.
        if ($logo != '') {
            // If logo URL is provided, include it in the img tag with custom margin.
            $return .= '<img id="logo_image" src="' . esc_url($logo) . '" class="img-responsive retina_ready" alt="' . esc_html__('company logo', 'wpresidence') . '"/>';
        } else {
            // If no logo URL is provided, use a default logo image from the theme directory.
            $return .= '<img id="logo_image" class="img-responsive retina_ready" src="' . get_theme_file_uri('/img/logo.png') . '" alt="' . esc_html__('company logo', 'wpresidence') . '"/>';
        }

        // Close the anchor and div tags.
        $return .= '</a></div>';

        // Allow modification of the complete logo HTML through a filter
        $return = apply_filters('wpresidence_logo_html', $return);

        // Trigger an action after logo display ends
        do_action('wpresidence_after_logo_display', $return);

        // Return the complete HTML for the logo.
        return $return;
    }


endif;






/*
*
* Load the header section
*
*/

if(!function_exists('wpresidence_show_header_wrapper')):
    // Function to display the header wrapper with custom classes and logo header type
    function wpresidence_show_header_wrapper($header_classes, $logo_header_type) {
    ?>

      

        <?php       
        // Check if the top bar user menu is enabled in theme options
        // and if the current page template is not 'page-templates/splash_page.php'
        if (esc_html(wpresidence_get_option('wp_estate_show_top_bar_user_menu', '')) == "yes" && !is_page_template('page-templates/splash_page.php')) {


            // Apply the filter to insert code before top bar
            do_action('wpresidence_before_top_bar', '');
           
            // Include the top bar template part
            get_template_part('templates/headers/top_bar');

            // Apply the filter to insert code after top bar
            do_action('wpresidence_after_top_bar', '');

        }
        ?> 
        
        <?php
        // Apply the filter to insert code before mobile menu header
        do_action('wpresidence_before_mobile_menu_header', '');

        // Include the mobile menu header template part
        get_template_part('templates/headers/mobile_menu_header');

        
        // Apply the filter to insert code after mobile menu header
        do_action('wpresidence_after_mobile_menu_header', '');
        ?>



        <?php
        // Apply the filter to insert code before master header wrapper
        do_action('wpresidence_before_master_header', '');        
        ?>
        
        <!-- Master header div with dynamic classes -->
        <div class="master_header d-none d-xl-block d-flex <?php echo esc_attr($header_classes['master_header_class']); ?>">
         
  
            <?php
            // Apply the filter to insert code before  header wrapper
            do_action('wpresidence_before_header_wrapper', '');        
            ?>

            <!-- Header wrapper with dynamic classes and Bootstrap flex classes -->
            <header class="header_wrapper d-flex w-100 <?php echo esc_attr($header_classes['header_wrapper_class']); ?> ">
                
                <?php
                // Apply the filter to insert code before display the header
                do_action('wpresidence_before_display_header', '');        
                ?>
                
                <?php
                // Check if the current page is not the user dashboard
                if (!wpestate_is_user_dashboard()) {
                    // Switch statement to include different header templates based on the logo header type
                    switch ($logo_header_type) {
                        case 'type1':
                            // Include header template for type1
                            include(locate_template('templates/headers/header1.php'));
                            break;
                        case 'type2':
                            // Include header template for type2
                            include(locate_template('templates/headers/header2.php'));
                            break;
                        case 'type3':
                            // Include header template for type3
                            include(locate_template('templates/headers/header3.php'));
                            break;
                        case 'type4':
                            // Include header template for type4
                            include(locate_template('templates/headers/header4_top_section.php'));
                            break;
                        case 'type5':
                            // Include header template for type5
                            include(locate_template('templates/headers/header5.php'));
                            break;
                        case 'type6':
                            // Include header template for type6
                            include(locate_template('templates/headers/header6.php'));
                            break;
                    }
                } else {
                    // If the current page is the user dashboard, include header1 template by default
                    include(locate_template('templates/headers/header1.php'));
                }
                ?>   
                
                <?php
                // Apply the filter to insert code after display the header
                do_action('wpresidence_before_display_header', '');        
                ?>
            </header>
            
            <?php
            // Apply the filter to insert code after header wrapper
            do_action('wpresidence_after_header_wrapper', '');        
            ?>
       </div>
  
        <?php 
        // Apply the filter to insert code after fore master header wrapper
        do_action('wpresidence_after_master_header', '');
        ?>
        
        <?php
        global $post;
        // Check if the current page is a single 'estate_property' and if the post ID is set
        if (is_singular('estate_property') && isset($post->ID)) {
            // Get the local content type status from the post meta
            $local_pgpr_content_type_status = get_post_meta($post->ID, 'local_pgpr_content_type', true);
            // Get the global content type status from theme options
            $global_prpg_content_type_status = esc_html(wpresidence_get_option('wp_estate_global_prpg_content_type', ''));
            // Determine the content type to be used on the property page
            $content_type = wpestate_property_page_load_content($local_pgpr_content_type_status, $global_prpg_content_type_status); 

            // If the content type is not 'tabs', build and display the sticky top bar for the property
            if ($content_type != 'tabs') {
                // Print the sticky top bar HTML for the property
                print wpresidence_property_build_sticky_top_bar($post->ID);
                // Print the JavaScript to make the top bar sticky on scroll
                print '<script type="text/javascript">
                //<![CDATA[
                    jQuery(document).ready(function(){
                        wpestate_property_sticky();
                    });
                //]]>
                </script>';
            }
        }
    }
endif;




/*
*
* Function to return all label data for property sections
*
*/

if(!function_exists('wpestate_return_all_labels_data')):
    /**
     * Function to return all label data for property sections or a specific section.
     *
     * @param string $item Optional. The specific section item to return data for.
     * @return array The array containing label data for all sections or a specific section.
     */
    function wpestate_return_all_labels_data($item=''){
        // Array containing data for various property sections
        $section_ids = array(
            'overview' => array(
                "accordion_id"          => 'single-overview-section',
                "tab_id"                => 'tab_property_overview',
                "label_default"         => esc_html__('Overview', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_overview_text'
            ),
            'description' => array(
                "accordion_id"          => 'wpestate_property_description_section',
                "tab_id"                => 'tab_property_description',
                "label_default"         => esc_html__('Description', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_description_text'
            ),
            'booking_shortcode' => array(
                "accordion_id"          => 'accordion_property_booking_shortcode',
                "tab_id"                => 'tab_property_booking_shortcode',
                "label_default"         => esc_html__('Booking', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_booking_shortcode_text'
            ),
            'documents' => array(
                "accordion_id"          => 'accordion_property_documents',
                "tab_id"                => 'tab_property_documens',
                "label_default"         => esc_html__('Documents', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_documents_text'
            ),
            'multi-units' => array(
                "accordion_id"          => 'accordion_property_multi_units',
                "tab_id"                => 'tab_property_multi_units',
                "label_default"         => esc_html__('Available Units', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_multi_text'
            ),
            'energy-savings' => array(
                "accordion_id"          => 'accordion_property_energy_savings',
                "tab_id"                => 'tab_property_energy_savings',
                "label_default"         => esc_html__('Energy Savings', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_energy_savings_text'
            ),
            'address' => array(
                "accordion_id"          => 'accordion_property_address',
                "tab_id"                => 'tab_property_address',
                "label_default"         => esc_html__('Property Address', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_adr_text'
            ),
            'listing_details' => array(
                "accordion_id"          => 'accordion_property_details',
                "tab_id"                => 'tab_property_listing_details',
                "label_default"         => esc_html__('Property Details', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_details_text'
            ),
            'features' => array(
                "accordion_id"          => 'accordion_features_details',
                "tab_id"                => 'tab_property_features',
                "label_default"         => esc_html__('Amenities and Features', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_features_text'
            ),
            'video' => array(
                "accordion_id"          => 'accordion_property_video',
                "tab_id"                => 'tab_property_video',
                "label_default"         => esc_html__('Video', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_video_text'
            ),
            'map' => array(
                "accordion_id"          => 'accordion_property_details_map',
                "tab_id"                => 'tab_property_map',
                "label_default"         => esc_html__('Map', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_map_text'
            ),
            'virtual_tour' => array(
                "accordion_id"          => 'accordion_property_virtual_tour',
                "tab_id"                => 'tab_property_virtual_tour',
                "label_default"         => esc_html__('Virtual Tour/Meta Reels', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_virtual_tour_text'
            ),
            'walkscore' => array(
                "accordion_id"          => 'accordion_property_walkscore',
                "tab_id"                => 'tab_property_walkscore',
                "label_default"         => esc_html__('WalkScore', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_walkscorer_text'
            ),
            'nearby' => array(
                "accordion_id"          => 'accordion_property_near_by',
                "tab_id"                => 'tab_property_near_by',
                "label_default"         => esc_html__('What\'s Nearby', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_near_by_text'
            ),
            'payment_calculator' => array(
                "accordion_id"          => 'accordion_property_payment_calculator',
                "tab_id"                => 'tab_property_calculator',
                "label_default"         => esc_html__('Mortgage Calculator', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_calculator_text'
            ),
            'floor_plans' => array(
                "accordion_id"          => 'accordion_property_floor_plans',
                "tab_id"                => 'tab_property_floor_plan',
                "label_default"         => esc_html__('Floor Plans', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_floor_plan_text'
            ),
            'page_views' => array(
                "accordion_id"          => 'accordion_property_page_views',
                "tab_id"                => 'tab_property_page_views',
                "label_default"         => esc_html__('Page Views Statistics', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_page_views_text'
            ),
            'schedule_tour' => array(
                "accordion_id"          => 'accordion_property_schedule_tour',
                "tab_id"                => 'tab_property_schedule',
                "label_default"         => esc_html__('Schedule a Tour', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_schedule_tour_text'
            ),
            'agent_area' => array(
                "accordion_id"          => 'wpestate_single_agent_details_wrapper',
                "tab_id"                => 'tab_property_agent_area',
                "label_default"         => esc_html__('Agent', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_sitcky_agent_text'
            ),
            'other_agents' => array(
                "accordion_id"          => 'property_other_agents',
                "tab_id"                => 'tab_property_other_agents',
                "label_default"         => esc_html__('Other Agents', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_other_agents_text'
            ),
            'reviews' => array(
                "accordion_id"          => 'property_reviews_area',
                "tab_id"                => 'tab_property_reviews',
                "label_default"         => esc_html__('Property Reviews', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_reviewstext'
            ),
            'similar' => array(
                "accordion_id"          => 'property_similar_listings',
                "tab_id"                => 'tab_property_similar_listings',
                "label_default"         => esc_html__('Similar Listings', 'wpresidence'),
                "label_theme_option"    => 'wp_estate_property_similart_listings_text'
            ),
        );

        // If a specific section item is requested and it exists in the array, return that section's data
        if ($item != '' && isset($section_ids[$item])) {
            return $section_ids[$item];
        } else {
            // Otherwise, return the entire array of section data
            return $section_ids;
        }
    }
endif;




/**
 * WpResidence Theme - Sticky Top Bar Builder for Property Pages
 *
 * This file contains the wpresidence_property_build_sticky_top_bar function, which
 * generates the sticky top bar navigation for property pages in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - wpresidence_get_option()
 * - wpestate_return_all_labels_data()
 * - wpestate_property_page_prepare_label()
 * - wpestate_check_category_for_morgage()
 * - wpestare_return_documents()
 *
 * Usage:
 * $sticky_top_bar = wpresidence_property_build_sticky_top_bar($post_id);
 */

 if ( ! function_exists( 'wpresidence_property_build_sticky_top_bar' ) ) :
    /**
     * Build the sticky top bar for property pages.
     *
     * This function generates the HTML for the sticky top bar navigation
     * on property pages, based on various theme options and property metadata.
     *
     * @param int $postID The ID of the property post.
     * @return string HTML for the sticky top bar or an empty string if not applicable.
     */
    function wpresidence_property_build_sticky_top_bar( $postID ) {
        // Check if the sticky top bar should be shown
        if ( 'yes' !== wpresidence_get_option( 'wp_estate_show_property_sticky_top_bar' ) ) {
            return '';
        }

        // Check global and local property page templates
        $global_template = intval( wpresidence_get_option( 'wp_estate_global_property_page_template' ) );
        $local_template  = intval( get_post_meta( $postID, 'property_page_design_local', true ) );
        if ( 0 === $local_template ) {
            $local_template  = intval( get_post_meta( $postID, 'property_page_desing_local', true ) );
        }

        if ( 0 !== $global_template || 0 !== $local_template ) {
            return '';
        }

        // Determine the layout order based on the property layout option
        $property_layouts = intval( wpresidence_get_option( 'wp_estate_property_layouts' ) );
        $layout_option    = ( 6 === $property_layouts || 7 === $property_layouts ) 
            ? 'wp_estate_property_page_acc_lay6_order' 
            : 'wp_estate_property_page_acc_order';

        $layout   = wpresidence_get_option( $layout_option );
        $to_parse = $layout['enabled'];

        if ( 6 === $property_layouts || 7 === $property_layouts ) {
            if ( is_array( $layout['after'] ) ) {
                $to_parse = array_merge( $to_parse, $layout['after'] );
            }
            if ( is_array( $layout['after_content'] ) ) {
                $to_parse = array_merge( $to_parse, $layout['after_content'] );
            }
        }

        $data     = wpestate_return_all_labels_data();
        $all_data = get_post_meta( $postID, '', true );

        $navigation_links = array();

        // Build navigation links
        foreach ( $to_parse as $key => $label ) {
            if ( isset( $data[ $key ] ) ) {
                $label = wpestate_property_page_prepare_label( $data[ $key ]['label_theme_option'], $data[ $key ]['label_default'] );

                if ( wpresidence_should_show_section_on_property_sticky_bar( $key, $all_data, $postID ) ) {
                    $navigation_links[] = sprintf(
                        '<a class="wpestate_top_property_navigation_link" href="#%s">%s</a>',
                        esc_attr( $data[ $key ]['accordion_id'] ),
                        esc_html( $label )
                    );
                }
            }
        }

        // Return the complete HTML for the sticky top bar
        return ! empty( $navigation_links ) 
            ? '<div class="wpestate_top_property_navigation">' . implode( '', $navigation_links ) . '</div>'
            : '';
    }
endif;

/**
 * Helper function to determine if a section should be shown in property sticky bar.
 *
 * @param string $key The key of the section.
 * @param array $all_data All post meta data for the property.
 * @param int $postID The ID of the property post.
 * @return boolean True if the section should be shown, false otherwise.
 */
if ( ! function_exists( 'wpresidence_should_show_section_on_property_sticky_bar' ) ) :
 function wpresidence_should_show_section_on_property_sticky_bar( $key, $all_data, $postID ) {
    switch ( $key ) {
        case 'video':
            return isset( $all_data['embed_video_id'] ) && '' !== $all_data['embed_video_id'][0];
        case 'virtual_tour':
            return isset( $all_data['embed_virtual_tour'] ) && '' !== $all_data['embed_virtual_tour'][0];
        case 'floor_plans':
            return isset( $all_data['use_floor_plans'][0] ) && 1 == $all_data['use_floor_plans'][0];
        case 'multi-units':
            return isset( $all_data['property_has_subunits'][0] ) && 1 == $all_data['property_has_subunits'][0];
        case 'energy-savings':
            return isset( $all_data['energy_index'][0] ) && '' !== $all_data['energy_index'][0]
                || isset( $all_data['energy_class'][0] ) && '' !== $all_data['energy_class'][0]
                || isset( $all_data['co2_index'][0] ) && '' !== $all_data['co2_index'][0]
                || isset( $all_data['co2_class'][0] ) && '' !== $all_data['co2_class'][0]
                || isset( $all_data['epc_current_rating'][0] ) && '' !== $all_data['epc_current_rating'][0]
                || isset( $all_data['epc_potential_rating'][0] ) && '' !== $all_data['epc_potential_rating'][0];
        case 'payment_calculator':
            return 'yes' === wpestate_check_category_for_morgage( $postID );
        case 'features':
            return is_array( get_the_terms( $postID, 'property_features' ) );
        case 'documents':
            return '' !== wpestare_return_documents( $postID );
        case 'booking_shortcode':
            return isset( $all_data['property_booking_shortcode'][0] ) && '' !== $all_data['property_booking_shortcode'][0];
        default:
            return true;
    }
}
endif;











/*
* Check if we display wpestate studio header
*
*
*/

if(!function_exists('wpestate_display_studio_header')):
    /**
     * Function to check if the wpestate studio header should be displayed.
     *
     * @return bool True if the studio header should be displayed, false otherwise.
     */
    function wpestate_display_studio_header(){
        // Access the global $wpestate_studio object
        global $wpestate_studio;
        
        // Array of header template values to check against
        $header_values = array(
            'wpestate_template_header',  
            'wpestate_template_before_header',   
            'wpestate_template_after_header'
        );

        // Check if $wpestate_studio is set and is an object
        if (isset($wpestate_studio) && is_object($wpestate_studio)) {
            // Check if header_footer_templates is an array within the $wpestate_studio object
            if (is_array($wpestate_studio->header_footer_instance->header_footer_templates)) {
                // Loop through each header template value
                foreach ($header_values as $template) {
                    // Check if the template is in the header_footer_templates array
                    if (in_array($template, $wpestate_studio->header_footer_instance->header_footer_templates)) {
                        // If found, return true to indicate the studio header should be displayed
                        return true;
                    }
                }
            }
        }

        // Return false if none of the conditions are met to display the studio header
        return false;
    }
endif;

