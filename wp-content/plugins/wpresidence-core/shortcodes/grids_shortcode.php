<?php
/**
 * WpResidence Content Grids Function
 *
 * This function generates a grid layout for displaying content items in the WpResidence theme.
 * It's used as part of a widget to showcase properties, agents, and blog posts.
 *
 * @package WpResidence
 * @subpackage Widgets
 * @since 1.0.0
 */

if ( ! function_exists( 'wpresidence_content_grids' ) ) :
    /**
     * Generate content grids for WpResidence theme
     *
     * @param array $settings Widget settings containing item IDs.
     */
    function wpresidence_content_grids( $settings ) {
        // Extract item IDs from settings
        $items_id   = $settings['item_ids'];
        $item_array = explode( ',', $items_id );

        // Define permitted post types
        $permited_posts_types = array( 'estate_property', 'estate_agent', 'post' );

        // Start the main wrapper
        print '<div class="wpestate_content_grid_wrapper flex-column flex-lg-row">';

        // First column
        print '<div class="wpestate_content_grid_wrapper_first_col col-12 col-lg-6">';
        $itemID    = $item_array[0];
        $post_type = get_post_type( $itemID );
        if ( in_array( $post_type, $permited_posts_types ) ) {
            include( locate_template( 'templates/shortcode_templates/content_grid_big_' . $post_type . '_type_1.php' ) );
        }
        print '</div>';

        // Remove the first item as it's already displayed
        unset( $item_array[0] );

        // Second column
        print '<div class="wpestate_content_grid_wrapper_second_col col-12 col-lg-6">';
        foreach ( $item_array as $key => $value ) {
            $itemID    = $value;
            $post_type = get_post_type( $itemID );
            if ( in_array( $post_type, $permited_posts_types ) ) {
                include( locate_template( 'templates/shortcode_templates/content_grid_small_' . $post_type . '_type_1.php' ) );
            }
        }
        print '</div>';

        // Close the main wrapper
        print '</div>';
    }
endif;





/**
 * WpEstate Sliding Box Shortcode
 *
 * This file contains the function to generate a sliding box shortcode for the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * @uses ob_start() For output buffering
 * @uses wp_kses_post() For sanitizing HTML content
 * @uses esc_attr() For escaping attribute values
 * @uses esc_html() For escaping HTML content
 * @uses esc_url() For escaping URLs
 *
 * @param array $settings An array of settings for the sliding box
 * @return string The HTML output for the sliding box shortcode
 */

if ( ! function_exists( 'wpestate_sliding_box_shortcode' ) ) :
    /**
     * Generate HTML for the sliding box shortcode
     *
     * @param array $settings Shortcode settings
     * @return string HTML output
     */
    function wpestate_sliding_box_shortcode( $settings ) {
        // Start output buffering
        ob_start();

        // Calculate the number of items
        $items = count( $settings['form_fields'] )+1;

        // Open the wrapper div
        echo '<div class="wpestate_sliding_box_wrapper">';

        // Loop through each box in the settings
        foreach ( $settings['form_fields'] as $box ) {
            // Extract and sanitize box data
            $image_url    = isset( $box['image']['url'] ) ? esc_url( $box['image']['url'] ) : '';
            $title        = isset( $box['title'] ) ? esc_html( $box['title'] ) : '';
            $content      = isset( $box['content'] ) ? wp_kses_post( $box['content'] ) : '';
            $read_me      = isset( $box['read_me'] ) ? esc_html( $box['read_me'] ) : '';
            $read_me_link = isset( $box['read_me_link'] ) ? $box['read_me_link'] : '';
            $read_me_icon = isset( $box['read_me_icon'] ) ? $box['read_me_icon'] : '';

            // var_dump( $read_me_link );
            // var_dump( $read_me_icon );

            // Determine if the box should be shown open
            $class_box = isset( $box['show_open'] ) && 'yes' === $box['show_open'] ? 'active-element' : '';
            $class_box .= ' slider_box_size_' . $items;

            // Output the sliding box HTML
            ?>
            <div class="wpestate_sliding_box <?php echo esc_attr( $class_box ); ?>">
                <div class="sliding-image" style="background-image:url(<?php echo $image_url; ?>)"></div>
                <div class="sliding-content-wrapper">
                    <h4><?php echo $title; ?></h4>
                    <p><?php echo $content; ?></p>
                    <div class="sliding-content-action">
                        <a class="wpresidence_button" href="<?php echo $read_me_link['url']; ?>" <?php echo (! $read_me_link['is_external'] ) ? 'target="_blank"' : '' ?> <?php echo ($read_me_link['nofollow'] ) ? 'rel="nofollow"' : '' ?>><span class="<?php echo $read_me_icon['value'] . ' ' . $read_me_icon['library'] ?>"></span> <?php echo $read_me; ?></a>
                    </div>
                </div>
            </div>
            <?php
        }

        // Close the wrapper div
        echo '</div>';

        // Get the buffered content and clean the buffer
        $output = ob_get_clean();

        // Return the generated HTML
        return $output;
    }
endif;



/**
 * WPResidence Theme - Grid Display Functions
 *
 * This file contains functions for displaying grids and querying taxonomies in the WPResidence theme.
 * It includes two main functions:
 * 1. wpresidence_display_grids() - Generates grid layout for taxonomies
 * 2. wpresidence_query_taxonomies() - Queries taxonomies based on provided arguments
 *
 * @package WPResidence
 * @subpackage Widgets
 * @since 1.0.0
 * 
 * @dependencies WPResidence core functions, WordPress core functions
 */

 if (!function_exists('wpresidence_display_grids')) :
  /**
   * Display WPResidence Grids
   *
   * This function generates a grid layout for displaying taxonomies.
   *
   * @param array $args Arguments for grid display
   * @return string HTML markup for the grid
   */
  function wpresidence_display_grids($args) {
      // Set up grid display and query taxonomies
      $display_grids = wpresidence_display_grids_setup();
      $taxonomies = wpresidence_query_taxonomies($args);
      
      // Extract and sanitize arguments
      $type = intval($args['type']);
      $place_type = intval($args['wpresidence_design_type']);
      $use_grid = $display_grids[$type];
      $category_tax = $args['grid_taxonomy'];

      // reset classes for columns
      $places_class['col_class']='';
      $places_class['col_org']='';


      // Determine grid pattern size
      $grid_pattern_size = is_array($use_grid['position']) ? count($use_grid['position']) : 1;

      // Initialize container
      $container = '<div class="row elementor_wpresidece_grid">';

      foreach ($taxonomies as $key => $taxonomy) {
          // Calculate position in grid pattern
          $key_position = ($key >= $grid_pattern_size) ? (($key % $grid_pattern_size) + 1) : (intval($key) + 1);
          $item_length = $use_grid['position'][$key_position];

          if (isset($taxonomies[$key])) {
              $container .= sprintf('<div class="%s col-sm-12 elementor_residence_grid">', esc_attr($item_length));
              
              ob_start();
              $place_id = $taxonomy->term_id;
              $category_name = $taxonomy->name;
              $category_count = $taxonomy->count;
              
              $card_type = wpestate_places_card_selector($place_type, 1);
              include(locate_template($card_type));
              $container .= ob_get_clean();
              
              $container .= '</div>';
          }
      }

      $container .= '</div>';
      return $container;
  }
endif;





if (!function_exists('wpresidence_query_taxonomies')) :
    /**
     * Query Taxonomies
     *
     * This function queries taxonomies based on provided arguments.
     *
     * @param array $args Arguments for taxonomy query
     * @return array Array of taxonomy terms
     */
    function wpresidence_query_taxonomies($args) {
        $requested_tax = $args['grid_taxonomy'];
        $arguments = array(
        'hide_empty' => $args['hide_empty_taxonomy'] ?? false,
        'number'     => $args['items_no'] ?? 0,
        'orderby'    => $args['orderby'] ?? 'name',
        'order'      => $args['order'] ?? 'ASC'
        );

        if (!empty($args[$requested_tax])) {
            $arguments['slug'] = $args[$requested_tax];
        }

        $terms = wpestate_get_cached_terms( $args['grid_taxonomy'],$arguments);
        return !is_wp_error($terms) ? $terms : array();
    }
endif;





/*
* Default values for ELementor wpresidence grids
*
*
*/

if( !function_exists('wpresidence_display_grids_setup') ):
function wpresidence_display_grids_setup(){
  $setup=array(
    1 =>  array(
              'position' => array(
                              1=> 'col-md-8',
                              2=> 'col-md-4',
                              3=> 'col-md-4',
                              4=> 'col-md-4',
                              5=> 'col-md-4',

                            )
          ),
      2 =>  array(
                'position' => array(
                                1=> 'col-md-6',
                                2=> 'col-md-3',
                                3=> 'col-md-3',
                                4=> 'col-md-3',
                                5=> 'col-md-3',
                                6=> 'col-md-6',
                              )
            ),
      3 =>  array(
                'position' => array(
                                  1=> 'col-md-4',
                                  2=> 'col-md-4',
                                  3=> 'col-md-4',
                                  4=> 'col-md-4',
                                  5=> 'col-md-4',
                                  6=> 'col-md-4',
                              )
            ),
        4 =>  array(
                  'position' => array(
                                  1=> 'col-md-4',
                                  2=> 'col-md-4',
                                  3=> 'col-md-4',
                                  4=> 'col-md-6',
                                  5=> 'col-md-6',
                                )
              ),
        5 =>  array(
                  'position' => array(
                                  1=> 'col-md-4',
                                  2=> 'col-md-8',
                                  3=> 'col-md-8',
                                  4=> 'col-md-4',
                                )
              ),
        6 =>  array(
                  'position' => array(
                                  1=> 'col-md-3',
                                  2=> 'col-md-3',
                                  3=> 'col-md-3',
                                  4=> 'col-md-3',
                                  5=> 'col-md-3',
                                  6=> 'col-md-3',
                                  7=> 'col-md-3',
                                  8=> 'col-md-3',
                                )
              ),
  );
  return $setup;
}
endif;
