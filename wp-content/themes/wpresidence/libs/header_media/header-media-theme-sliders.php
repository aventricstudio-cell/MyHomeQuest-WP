<?php
//MILLDONE
if( !function_exists('wpestate_present_theme_slider') ):
    /**
     * Generates and displays the theme slider for the WPResidence theme.
     *
     * This function is responsible for creating the main slider on the homepage or other
     * designated pages in the WPResidence real estate WordPress theme. It handles different
     * types of sliders and dynamically generates the necessary HTML and JavaScript.
     *
     * Global Dependencies:
     * - Assumes the existence of global $wpresidence_admin for theme options.
     * - Relies on several theme-specific functions like wpresidence_get_option and wpestate_return_ids_theme_slider.
     *
     * @return void This function echoes HTML output directly.
     */
    function wpestate_present_theme_slider(){

        // Retrieve the slider type from theme options
        $theme_slider = wpresidence_get_option( 'wp_estate_theme_slider_type');
        
        // Check for specific slider types and call appropriate functions
        if($theme_slider=='type2'){
            // If slider type is 'type2', call the corresponding function and exit
            wpestate_present_theme_slider_type2();
            return;
        }else if($theme_slider=='type3'){
            // If slider type is 'type3', call the corresponding function and exit
            wpestate_present_theme_slider_type3();
            return;
        }
        
        // For default slider type, get the IDs of properties to be displayed
        $theme_slider = wpestate_return_ids_theme_slider();
        if(empty($theme_slider)){
            return; // Exit if no properties are set for the slider
        }
        
        // Retrieve various theme options
        $wpestate_currency      = wpresidence_get_option('wp_estate_currency_symbol', '');
        $where_currency         = wpresidence_get_option('wp_estate_where_currency_symbol', '');
        $slider_cycle           = wpresidence_get_option('wp_estate_slider_cycle', '');
        $extended_search        = wpresidence_get_option('wp_estate_show_adv_search_extended', '');
        $theme_slider_height    = wpresidence_get_option('wp_estate_theme_slider_height', '');
       
        // Initialize variables for slider construction
        $counter        = 0;
        $indicators     = '';
        $data_bs_ride   = "carousel";
     
        // Determine if the slider should auto-cycle
        if($slider_cycle == 0){
            $data_bs_ride = "false" ;
        }
        
        // Set up classes for extended search and full-screen options
        $extended_class = $extended_search == 'yes' ? 'theme_slider_extended' : '';
        if ($theme_slider_height == 0) {
            $theme_slider_height = 900;
            $extended_class .= " full_screen_yes";
        }
   
        // Set up query arguments to retrieve slider properties
        $args = array(
            'post_type'        => 'estate_property',
            'post_status'      => 'publish',
            'post__in'         => $theme_slider,
            'posts_per_page'   => -1,
            'suppress_filters' => false
        );
       
        // Fetch the properties for the slider
        $recent_posts = get_posts($args);
        
        // Begin outputting the slider HTML structure
        ?>
        <div class="theme_slider_wrapper <?php echo esc_attr($extended_class); ?> carousel theme_slider_1 slide" data-bs-ride="<?php echo esc_attr($data_bs_ride); ?>"
            data-bs-interval="<?php echo esc_attr($slider_cycle); ?>" id="estate-carousel"  style="height:<?php echo esc_attr($theme_slider_height); ?>px;">
            <?php
                // Loop through each property and include the slider template
                foreach ($recent_posts as $post) {            
                    $postID = $post->ID;
                    setup_postdata($post);
                    include( locate_template( 'templates/header_media/header_media_slider_template_1.php') );
                    // Build the indicators for the slider
                    $indicators .= '<li data-bs-target="#estate-carousel" data-bs-slide-to="'.esc_attr($counter).'" class="'.esc_attr($active).'"></li>';
                    $counter++;
                }
            wp_reset_query();        
            wp_reset_postdata();
            ?>
           
            <!-- Output the slider indicators -->
            <ol class="carousel-indicators">
                <?php echo $indicators; ?>
            </ol>
        </div>
        <?php
    }
endif;




if( !function_exists('wpestate_present_theme_slider_type2') ):
    /**
     * Generates and displays the theme slider type 2 for the WPResidence theme.
     *
     * This function is responsible for creating a specific type of slider (type 2) on the homepage 
     * or other designated pages in the WPResidence real estate WordPress theme. It uses the Slick 
     * slider library and dynamically generates the necessary HTML and JavaScript.
     *
     * Dependencies:
     * - Requires the Slick slider JavaScript library to be available.
     * - Relies on several WPResidence theme-specific functions and options.
     * - Assumes the existence of a template file: 'templates/header_media/header_media_slider_template_2.php'
     *
     * Global Dependencies:
     * - Assumes the existence of global $wpresidence_admin for theme options.
     * - Uses theme-specific functions like wpresidence_get_option and wpestate_return_ids_theme_slider.
     *
     * @return void This function echoes HTML and JavaScript output directly.
     */
    function wpestate_present_theme_slider_type2(){
        // Enqueue the Slick slider script
        wp_enqueue_script('slick.min');
     
        // Retrieve the IDs of properties to be displayed in the slider
        $theme_slider = wpestate_return_ids_theme_slider();
        if(empty($theme_slider)){
            return; // Exit if no properties are set for the slider
        }

        // Retrieve various theme options
        $wpestate_currency  = esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency     = esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $extended_search    = wpresidence_get_option('wp_estate_show_adv_search_extended','');
        $slider_cycle       = wpresidence_get_option( 'wp_estate_slider_cycle', '');
        
        // Initialize counter and extended class variables
        $counter = 0;
        $extended_class = '';
        
        // Add class for extended search if option is set
        if ( $extended_search == 'yes' ){
            $extended_class = 'theme_slider_extended';
        }
        
        // Set slider height based on theme option
        $theme_slider_height = wpresidence_get_option( 'wp_estate_theme_slider_height', '');
        if($theme_slider_height == 0){
            $theme_slider_height = 900;
            $extended_class .= " full_screen_yes ";
        }
        
        // Set up query arguments to retrieve slider properties
        $args = array(
            'post_type'        => 'estate_property',
            'post_status'      => 'publish',
            'post__in'         => $theme_slider,
            'posts_per_page'   => -1,
            'suppress_filters' => false
        );
       
        // Fetch the properties for the slider
        $recent_posts = get_posts($args);
        
        // Begin outputting the slider HTML structure
        ?>
        <div class="theme_slider_wrapper theme_slider_2 <?php echo esc_attr($extended_class);?> "
            data-auto="<?php echo esc_attr($slider_cycle); ?>"
            style="height:<?php echo esc_attr($theme_slider_height);?>px;">
            <?php
                // Loop through each property and include the slider template
                foreach ($recent_posts as $post) {
                    $postID = $post->ID;
                    setup_postdata($post);
                    include( locate_template( 'templates/header_media/header_media_slider_template_2.php') );
                    $counter++;
                }
                // Reset WordPress query and postdata
                wp_reset_query();        
                wp_reset_postdata();
            ?>
        </div>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready(function(){
                // Initialize the Slick slider with 3 slides visible
                wpestate_enable_slick_theme_slider(3);
            });
            //]]>
        </script>
    <?php
    }
endif;






if( !function_exists('wpestate_present_theme_slider_type3') ):
    /**
     * Generates and displays the theme slider type 3 for the WPResidence theme.
     *
     * This function creates a specific type of slider (type 3) on the homepage or other 
     * designated pages in the WPResidence real estate WordPress theme. It utilizes the 
     * Owl Carousel library and dynamically generates the necessary HTML and JavaScript.
     *
     * Dependencies:
     * - Requires the Owl Carousel JavaScript library to be available.
     * - Relies on several WPResidence theme-specific functions and options.
     * - Assumes the existence of a template file: 'templates/header_media/header_media_slider_template_3.php'
     *
     * Global Dependencies:
     * - Assumes the existence of global $wpresidence_admin for theme options.
     * - Uses theme-specific functions like wpresidence_get_option and wpestate_return_ids_theme_slider.
     *
     * @return void This function echoes HTML and JavaScript output directly.
     */
    function wpestate_present_theme_slider_type3(){
        // Enqueue the Owl Carousel script
        wp_enqueue_script('owl_carousel');
        
        // Initialize counters and container variables
        $counter    = 0;
        $slides     = '';
        $indicators = '';
     
        // Retrieve the IDs of properties to be displayed in the slider
        $theme_slider = wpestate_return_ids_theme_slider();
        if(empty($theme_slider)){
            return; // Exit if no properties are set for the slider
        }

        // Retrieve various theme options
        $wpestate_currency  = esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency     = esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $slider_cycle       = wpresidence_get_option( 'wp_estate_slider_cycle', '');
        $extended_search    = wpresidence_get_option('wp_estate_show_adv_search_extended','');
        
        // Set up extended class for search options
        $extended_class = '';
        if ( $extended_search == 'yes' ){
            $extended_class = 'theme_slider_extended';
        }
        
        // Set slider height based on theme option
        $theme_slider_height = wpresidence_get_option( 'wp_estate_theme_slider_height', '');
        if($theme_slider_height == 0){
            $theme_slider_height = 900;
            $extended_class .= " full_screen_yes ";
        }

        // Set up query arguments to retrieve slider properties
        $args = array(
            'post_type'        => 'estate_property',
            'post_status'      => 'publish',
            'post__in'         => $theme_slider,
            'posts_per_page'   => -1,
            'suppress_filters' => false
        );
       
        // Fetch the properties for the slider
        $recent_posts = get_posts($args);
        ?>
        <div class="theme_slider_wrapper owl-carousel owl-theme <?php echo esc_attr($extended_class);?> theme_slider_3 slider_type_3  slide"  
            data-auto="<?php echo esc_attr($slider_cycle);?>" id="estate-carousel" >
           
            <?php
            $class_counter = 1;
            foreach ($recent_posts as $post) {
                $postID = $post->ID;
                setup_postdata($post);
                // Include the slider template for each property
                include( locate_template( 'templates/header_media/header_media_slider_template_3.php') );
           
                // Generate indicators for each slide
                $indicators .= '
                <a data-target="#estate-carousel" href="#item'.esc_attr($counter).'" class="button secondary url '. esc_attr($active).'">
                    '.$ex_cont.'
                </a>';
                $counter++;
                $class_counter++;
                if( $class_counter > 3 ){
                    $class_counter = 1;
                }
            }
            // Reset WordPress query and postdata
            wp_reset_query();        
            wp_reset_postdata();
            ?>
        </div>
        <ol class="theme_slider_3_carousel-indicators">
            <?php echo trim($indicators);?>
        </ol>
       
        <script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function(){
                   // Initialize the type 3 theme slider
                   wpestate_theme_slider_3();
                });
                //]]>
        </script>
   
    <?php    
    }
endif;










if (!function_exists('wpestate_theme_slider_contact')) {
    /**
     * Generates the contact section HTML for the theme slider.
     *
     * This function creates a contact section for a specific property in the theme slider,
     * including the agent's details and a contact form. It uses output buffering to capture
     * the generated HTML.
     *
     * @param int $propid The ID of the property for which to generate the contact section.
     * @return string The HTML content of the contact section.
     */
    function wpestate_theme_slider_contact($propid) {
        // Start output buffering to capture HTML output
        ob_start();

        // Store the property ID for use in the template
        $propertyID = $propid;

        // Retrieve agent details for the given property
        $realtor_details = wpestate_return_agent_details($propid);
        $realtor_link = esc_url($realtor_details['link']);
        $realtor_image = esc_url($realtor_details['realtor_image']);
        $realtor_name = esc_html($realtor_details['realtor_name']);

        // Set the text for the contact button
        $contact_agent_text = esc_html__('Contact Agent', 'wpresidence');
        ?>
        <!-- Wrapper for the agent details and contact button -->
        <div class="theme_slider_contact_wrapper">
            <?php if ($realtor_link != ''): ?>
                <!-- Display agent image with link if available -->
                <a href="<?php echo $realtor_link; ?>">
                    <div class="theme_slider_agent" style="background-image:url('<?php echo $realtor_image; ?>');"></div>
                </a>
            <?php else: ?>
                <!-- Display agent image without link -->
                <div class="theme_slider_agent" style="background-image:url('<?php echo $realtor_image; ?>');"></div>
            <?php endif; ?>

            <!-- Display agent name, linked if a link is available -->
            <div class="theme_slider_agent_name">
                <?php if ($realtor_link != ''): ?>
                    <a href="<?php echo $realtor_link; ?>"><?php echo $realtor_name; ?></a>
                <?php else: ?>
                    <?php echo $realtor_name; ?>
                <?php endif; ?>
            </div>

            <!-- Contact agent button -->
            <div class="wpestate_theme_slider_contact_agent"><?php echo $contact_agent_text; ?></div>
        </div>

        <!-- Wrapper for the contact form -->
        <div class="theme_slider_contact_form_wrapper">
            <!-- Close button for the contact form modal -->
            <div class="theme_slider_details_modal_close">
                <?php  
               
                    include(locate_template('/templates/svg_icons/close_icon.svg'));
                ?>
            </div>

            <!-- Contact form container -->
            <div class="agent_contanct_form wpestate_contact_form_parent">
                <?php
                    // Set the context for the contact form
                    $context = 'theme_slider';
                    // Include the contact form template
                    include locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php');
                ?>
            </div>
        </div>
        <?php
        // Get the buffered content
        $contact_forms = ob_get_contents();
        // Clear the output buffer
        ob_end_clean();
        // Return the generated HTML
        return $contact_forms;
    }
}









/**
 * Retrieves and processes the property IDs for the theme slider.
 *
 * This function checks for manually set property IDs for the theme slider.
 * If manual IDs are set, it processes and returns them. Otherwise, it falls
 * back to the default theme slider option.
 *
 * @package WPResidence
 * @subpackage ThemeSlider
 * @return array|string An array of property IDs if manually set, or a string of IDs if using default option
 */

if (!function_exists('wpestate_return_ids_theme_slider')):

function wpestate_return_ids_theme_slider() {
    // Retrieve the manually set theme slider option
    $theme_slider_manual = wpresidence_get_option('wp_estate_theme_slider_manual', '');

    // Check if manual slider IDs are set
    if ($theme_slider_manual != '') {
        // Split the comma-separated string of IDs into an array
        $theme_slider_manual_array = explode(',', $theme_slider_manual);
        
        // Return the array of manually set property IDs
        return $theme_slider_manual_array;
    } else {
        // If no manual IDs are set, retrieve the default theme slider option
        $theme_slider = wpresidence_get_option('wp_estate_theme_slider', '');
        
        // Return the default theme slider option (could be a string or an array)
        return $theme_slider;
    }
}

endif;

?>