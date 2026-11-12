<?php
/**
 * Property Categories Widget
 *
 * Displays property taxonomy terms in a hierarchical list with performance optimizations
 * Uses the WP Estate caching system for improved database efficiency
 * 
 * @package WpResidence
 * @subpackage Widgets
 * @since 4.0.0
 */

/**
 * Property_Categories widget class
 * 
 * Displays real estate categories in a hierarchical list with caching for better performance
 */
class Property_Categories extends WP_Widget {
    
    /**
     * Widget constructor
     * 
     * Sets up the widget name, description, and control options
     */
    function __construct() {
        $widget_ops = array('classname' => 'property_categories', 'description' => 'List Properties by Categories');
        $control_ops = array('id_base' => 'property_categories');
        parent::__construct('property_categories', 'Wp Estate: List Properties by Categories', $widget_ops, $control_ops);
    }
    
    /**
     * Widget admin form
     * 
     * Outputs the widget settings form in the admin area
     * 
     * @param array $instance Current widget instance settings
     */
    function form($instance) {
        // Default widget settings
        $defaults = array(
            'title'       => 'Our Listings',
            'taxonony'    => 'property_category',
            'show_count'  => 'yes',
            'show_child'  => 'yes',
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        
        // Available property taxonomies
        $taxonomies = array(
            'property_category'         => esc_html__('Property Category','wpresidence-core'),
            'property_action_category'  => esc_html__('Property Action','wpresidence-core'),
            'property_city'             => esc_html__('Property City','wpresidence-core'),
            'property_area'             => esc_html__('Property Area','wpresidence-core'),
            'property_county_state'     => esc_html__('Property County/State','wpresidence-core')
        );
        
        $show_cont = array('yes','no');
        
        // Build the form HTML
        $display = '
        <p>
            <label for="'.$this->get_field_id('title').'">Title:</label>
        </p>
        <p>
            <input id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.$instance['title'].'" />
        </p>
        
        <p>
            <label for="'.$this->get_field_id('taxonony').'">Category / Taxonomy:</label>
        </p>
        <p>        
            <select id="'.$this->get_field_id('taxonony').'" name="'.$this->get_field_name('taxonony').'">';
                foreach($taxonomies as $tax=>$name){
                    $display .='<option value ="'.$tax.'"';
                        if($instance['taxonony']=== $tax){
                           $display .=' selected '; 
                        }
                    $display .='>'.$name.'</option>';
                }
            $display .='</select>
        </p>
        
        <p>
            <label for="'.$this->get_field_id('show_count').'">Show Categories Count</label>
        </p>
        
        <p>
            <select id="'.$this->get_field_id('show_count').'" name="'.$this->get_field_name('show_count').'">';
            foreach($show_cont as $tax=>$name){
                $display .='<option value ="'.$name.'"';
                    if($instance['show_count']=== $name){
                       $display .=' selected '; 
                    }
                $display .='>'.$name.'</option>';
            }
            $display .='</select>
        </p>
        
        <p>
            <label for="'.$this->get_field_id('show_child').'">Show Child Categories:</label>
        </p>
        
        <p>
            <select id="'.$this->get_field_id('show_child').'" name="'.$this->get_field_name('show_child').'">';
            foreach($show_cont as $tax=>$name){
                $display .='<option value ="'.$name.'"';
                    if($instance['show_child']=== $name){
                       $display .=' selected '; 
                    }
                $display .='>'.$name.'</option>';
            }
            $display .='</select>
        </p>';
        print $display;
    }

    /**
     * Save widget options
     * 
     * Process widget options on save
     * 
     * @param array $new_instance New settings for this instance as submitted by the user
     * @param array $old_instance Old settings for this instance
     * @return array Updated settings to save
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title']      = $new_instance['title'];
        $instance['taxonony']   = $new_instance['taxonony'];
        $instance['show_count'] = $new_instance['show_count'];
        $instance['show_child'] = $new_instance['show_child'];
        
        return $instance;
    }

    /**
     * Frontend display of widget
     * 
     * Handles the front-end display with caching optimizations
     * 
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance The settings for the particular instance of the widget.
     */
    function widget($args, $instance) {
        extract($args);
        
        $title      = apply_filters('widget_title', $instance['title']);
        $show_count = $instance['show_count'];
        $show_child = $instance['show_child'];
        $taxonomy   = $instance['taxonony'];
        
        // Generate a unique cache key based on widget settings
        // This helps avoid redundant processing for identical widgets
        $cache_key = 'wpestate_category_widget_' . md5(serialize($instance));
        
        // Add language code to cache key for multilingual sites
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $cache_key = wpestate_set_transient_name_multilang( $cache_key . '_' );
        } else {
            $cache_key .= '_';
        }
        
        // Try to get cached widget output
        $output = get_transient($cache_key);
        
        // If no cache exists, generate the HTML
        if ($output === false) {
            $output = '';
            $output .= $before_widget;
            if ($title) {
                $output .= $before_title . $title . $after_title;
            }
            
            $output .= '<div class="category_list_widget">';
            
            // Retrieve parent terms using the cached terms function
            // This leverages the existing WP Estate caching system
            $items = wpestate_get_cached_terms(
                $taxonomy,
                array('parent' => 0)
            );
            
            // Only process if we have valid terms
            if (!empty($items) && !is_wp_error($items)) {
                $output .= $this->build_category_list($items, $taxonomy, $show_child, $show_count);
            }
            
            $output .= '</div>';
            $output .= $after_widget;
            
            // Cache the final HTML output for 6 hours
            // This avoids regenerating the entire tree structure on each page load
            set_transient($cache_key, $output, 6 * HOUR_IN_SECONDS);
        }
        
        // Output the widget content (either from cache or freshly generated)
        echo $output;
    }
    
    /**
     * Builds a hierarchical category list
     * 
     * Recursively generates HTML for the category tree with support for child categories
     * Uses the WP Estate term caching system for performance
     * 
     * @param array  $items      Array of term objects
     * @param string $taxonomy   Taxonomy name
     * @param string $show_child Whether to show child categories ('yes' or 'no')
     * @param string $show_count Whether to show term counts ('yes' or 'no')
     * @return string HTML output for the category list
     */
    private function build_category_list($items, $taxonomy, $show_child, $show_count) {
        // Return empty string for empty term arrays
        if (empty($items)) {
            return '';
        }
        
        // Start the list with appropriate class based on hierarchy level
        $return_string = '';
        if ($show_child == 'yes') {
            $return_string .= '<ul>';
        } else {
            $return_string .= '<ul class="child_category">';
        }
        
        // Process each term
        foreach ($items as $item) {
            // Add the term link
            $return_string .= '<li><a href="' . esc_url(get_term_link($item->slug, $item->taxonomy)) . '">' . esc_attr($item->name) . '</a>';
            
            // Add the term count if enabled
            if ($show_count == 'yes') {
                $return_string .= '<span class="category_no">(' . esc_attr($item->count) . ')</span>';
            }
            
            // Process child terms if enabled
            if ($show_child == 'yes') {
                // Get child terms using the cached terms function
                $child_categories = wpestate_get_cached_terms($taxonomy, array('parent' => $item->term_id));
                
                // Recursively add child terms if they exist
                if (!empty($child_categories) && !is_wp_error($child_categories)) {
                    $return_string .= $this->build_category_list($child_categories, $taxonomy, false, $show_count);
                }
            }
            
            $return_string .= '</li>';
        }
        
        $return_string .= '</ul>';
        return $return_string;
    }
}
?>
