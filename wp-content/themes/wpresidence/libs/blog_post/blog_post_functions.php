<?php

// MILLDONE

/**
 * Displays blog posts list in HTML format
 * 
 * This function generates the HTML output for blog posts based on various display settings
 * and configurations. It supports both grid and list layouts, with customizable column classes
 * and unit templates.
 *
 * @param WP_Query $blog_selection   The WordPress query object containing the posts to display
 * @param array    $wpestate_options Array of display options for the blog list
 * @param string   $context          Context in which the function is being called (e.g., 'shortcode')
 * @param array    $shortcode_attributes Array of attributes passed from shortcode
 * @param array    $pagination       Pagination settings and data
 *
 * @uses wpestate_blog_unit_selector()     Gets the appropriate blog unit template
 * @uses wpresidence_get_option()          Retrieves WPResidence theme options
 * @uses wpestate_blog_unit_column_selector() Determines column classes for blog units
 * @uses locate_template()                 Locates the appropriate template file
 *
 * Key Features:
 * - Supports grid layout through 'display_grid' attribute
 * - Handles 'open in new page' functionality
 * - Customizable column layouts
 * - Displays "No posts found" message when no posts are available
 * - Properly resets WordPress query data after execution
 *
 * Example Usage:
 * $blog_query = new WP_Query($args);
 * wpresidence_display_blog_list_as_html($blog_query, array(), 'shortcode', array('display_grid' => 'yes'));
 */
if(!function_exists('wpresidence_display_blog_list_as_html')):
    function wpresidence_display_blog_list_as_html($blog_selection, $wpestate_options = array(), $context = '', $shortcode_attributes = array(), $pagination = array()){


        $blog_unit              = wpestate_blog_unit_selector($shortcode_attributes);
        $no_posts_message       = esc_html__('No posts found', 'wpresidence');

        // Determine if grid display is enabled
        $display_grid = isset($shortcode_attributes['display_grid']) && $shortcode_attributes['display_grid'] === 'yes' ? 'yes' : 'no';

        //open in new page option
        $new_page_option                     = wpresidence_get_option('wp_estate_unit_card_new_page', '');

        
        //Determine which column class we will use
        $blog_unit_class_request = wpestate_blog_unit_column_selector($wpestate_options,$context,$shortcode_attributes);
        $blog_unit_class = $blog_unit_class_request['col_class'];

        // reset blog unit card class if display is grid 
        if (    $display_grid  === 'yes' ){
            $blog_unit_class='';
   
        }else{
            // add class for responsive when non grid
            $blog_unit_class.=' col-12 col-sm-6 col-md-6  ';
        }


        if ($blog_selection->have_posts()) {
            while ($blog_selection->have_posts()) : $blog_selection->the_post();
                $postID = get_the_ID();

                if ($display_grid === 'yes') {
                    echo '<div class="shortcode_wrapper_grid_item">';
                }


                include(locate_template($blog_unit));

                if ($display_grid === 'yes') {
                    echo '</div>';
                }

                
            endwhile;



         


        } else {
  
   
                echo '<span class="no_results">' . esc_html($no_posts_message) . '</span>';
                if($context !== 'shortcode'){ }
        }

        wp_reset_postdata();
        wp_reset_query();


    }
endif;















/**
*
*
* Function return the no of columns for blog unit 
*
*/

if(!function_exists('wpestate_blog_unit_column_selector')):
    function wpestate_blog_unit_column_selector($wpestate_options='',$context='',$shortcode_params=''){
     
        // Get the number of blog listings per row from theme options
        // The second parameter '' provides a default value if the option is not set
        $wpestate_no_listins_per_row = intval(wpresidence_get_option('wp_estate_blog_listings_per_row', ''));

        if($context=='shortcode'){
            if(isset($shortcode_params['rownumber'])){
                $wpestate_no_listins_per_row=$shortcode_params['rownumber'];
            }
        }

        // if we have similar posts
        if($context=='similar'){
            // Get the option for similar blog post count (default to 3 if not set)
            $wpestate_no_listins_per_row = intval(wpresidence_get_option('wp_estate_similar_blog_per_row', '',3));
        }


        // Further adjustment if content class is 'col-lg-12' or full width
        if(isset($wpestate_options['content_class']) && !str_contains($wpestate_options['content_class'], 'col-lg-12') ){
            $wpestate_no_listins_per_row--;
        }
    
        // force maxim 6 columns
        if ($wpestate_no_listins_per_row > 4) {
            $wpestate_no_listins_per_row = 4;
        }   

        $options = array(
            '1'=> array(
                'col_class' =>  'col-lg-12 col-md-12',
                'col_org'   =>  12
            ) ,
            
            '2'=> array(
                    'col_class' =>  'col-lg-6',
                    'col_org'   =>  6
            ),   

            '3'=> array(
                    'col_class' =>  'col-lg-4',
                    'col_org'   =>  4
            ), 
                
            '4'=> array(
                    'col_class' =>  'col-lg-3',
                    'col_org'   =>  3
            ), 
            '5'=> array(
                'col_class' =>  'col-lg-3',
                'col_org'   =>  3
             ), 
            '6'=> array(
                'col_class' =>  'col-lg-2',
                'col_org'   =>  2
            ), 

           
        );

        if(isset( $options[$wpestate_no_listins_per_row] )){
            return $options[$wpestate_no_listins_per_row];
        }else{
              return $options[3];
        }
    }

endif;




/**
 * Restrict access to specific post types
 *
 * This function checks if the current post is of type 'wpestate_message' or 'wpestate_invoice'
 * and redirects to the home page if so.
 */
function wpestate_restrict_post_type_access() {
    if (is_singular(['wpestate_message', 'wpestate_invoice'])) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'wpestate_restrict_post_type_access');


/**
*
*
* Function waht will return the right temaplte for blog cards*
*
*/

if(!function_exists('wpestate_blog_unit_selector')):
    function wpestate_blog_unit_selector($version_from_shortcode_array = '') {
        $template= 'blog_unit2.php'; // Default fallback value

        if (is_array($version_from_shortcode_array) && isset($version_from_shortcode_array['card_version']) && $version_from_shortcode_array['card_version'] !== '') {
            $version_from_shortcode = intval($version_from_shortcode_array['card_version']);

            if ($version_from_shortcode === 1) {
                $template= 'blog_unit.php';
            } elseif ($version_from_shortcode === 2) {
                $template= 'blog_unit2.php';
            } elseif ($version_from_shortcode === 3) {
                $template= 'blog_unit3.php';
            } elseif ($version_from_shortcode === 4) {
                $template= 'blog_unit4.php';
            }
        } else {
              $blog_unit = intval(wpresidence_get_option('wp_estate_blog_unit_card', ''));

            if ($blog_unit === 'list') {
                $template= 'blog_unit.php';
            } elseif ($blog_unit === 'grid2') {
                $template= 'blog_unit3.php';
            }elseif ($blog_unit === 1) {
                $template= 'blog_unit.php';
            } elseif ($blog_unit === 2) {
                $template= 'blog_unit2.php';
            } elseif ($blog_unit === 3) {
                $template= 'blog_unit3.php';
            } elseif ($blog_unit === 4) {
                $template= 'blog_unit4.php';
            }
        }

     
    return 'templates/blog_card_templates/'.$template;
}   
endif;