<?php
/**
 * Footer Latest Listings Widget
 *
 * Displays the latest property listings in the footer with advanced filtering options
 * Implements transient caching for improved performance
 * 
 * @package WpResidence
 * @subpackage Widgets
 * @since 4.0.0
 */

class Footer_latest_widget extends WP_Widget {
    
    /**
     * Widget constructor
     */
    function __construct(){
        $widget_ops = array('classname' => 'latest_listings', 'description' => 'Show latest listings.');
        $control_ops = array('id_base' => 'footer_latest_widget');
        parent::__construct('footer_latest_widget', 'Wp Estate: Latest Listing ', $widget_ops, $control_ops);
    }

    /**
     * Widget admin form
     * 
     * @param array $instance Current widget instance settings
     */
    function form($instance){
        $defaults = array('title'                       =>  'Latest Listing',
                          'listing_no'                  =>  3,
                           'adv_filter_search_action'   =>  '',
                           'adv_filter_search_category' =>  '',
                           'current_adv_filter_city'    =>  '',
                           'current_adv_filter_area'    =>  '',
                           'show_featured_only'         =>  '',
                           'show_as_slider'             =>  'list',
            );
        $instance = wp_parse_args((array) $instance, $defaults);

        // Get taxonomy terms with caching
        $args = array(
            'hide_empty'    => false
        );

        // Build action category dropdown with cached terms
        $actions_select     =   '';
        $taxonomy           =   'property_action_category';
        $tax_terms          =   wpestate_get_cached_terms($taxonomy, $args);

        $current_adv_filter_search_action = $instance['adv_filter_search_action'];
        if($current_adv_filter_search_action==''){
            $current_adv_filter_search_action=array();
        }

        $all_selected='';
        if(!empty($current_adv_filter_search_action) &&  in_array  (esc_html__('all','wpresidence-core'), $current_adv_filter_search_action)  ){
          $all_selected=' selected="selected" ';
        }

        $actions_select.='<option value="all" '.$all_selected.'>'.esc_html__('all','wpresidence-core').'</option>';
        if( !empty( $tax_terms ) ){
            foreach ($tax_terms as $tax_term) {
                $actions_select .= '<option value="'.$tax_term->name.'" ';
                if( in_array  ( $tax_term->name, $current_adv_filter_search_action) ){
                  $actions_select .= ' selected="selected" ';
                }
                $actions_select .=' >'.$tax_term->name.'</option>';
            }
        }

        // Build property category dropdown with cached terms
        $taxonomy           =   'property_category';
        $tax_terms          =   wpestate_get_cached_terms($taxonomy, $args);

        $current_adv_filter_search_category = $instance['adv_filter_search_category'];
        if($current_adv_filter_search_category==''){
            $current_adv_filter_search_category=array();
        }

        $all_selected='';
        if( !empty($current_adv_filter_search_category) && $current_adv_filter_search_category[0]=='all'){
          $all_selected=' selected="selected" ';
        }

        $categ_select = $this->build_taxonomy_dropdown($tax_terms, $current_adv_filter_search_category);

        // Build city dropdown with cached terms
        $taxonomy = 'property_city';
        $tax_terms_city = wpestate_get_cached_terms($taxonomy, $args);
        $current_adv_filter_city = $instance['current_adv_filter_city'];
        if($current_adv_filter_city==''){
            $current_adv_filter_city=array();
        }

        $select_city = $this->build_taxonomy_dropdown($tax_terms_city, $current_adv_filter_city);

        // Build area dropdown with cached terms
        $taxonomy = 'property_area';
        $tax_terms_area = wpestate_get_cached_terms($taxonomy, $args);
        $current_adv_filter_area = $instance['current_adv_filter_area'];
        if($current_adv_filter_area==''){
            $current_adv_filter_area=array();
        }

        $select_area = $this->build_taxonomy_dropdown($tax_terms_area, $current_adv_filter_area);

        // Build featured only dropdown
        $cache_array = array('yes','no');
        $show_featured_only_select = '';
        $show_featured_only = $instance['show_featured_only'];
        foreach($cache_array as $value){
            $show_featured_only_select.='<option value="'.$value.'" ';
            if ( $show_featured_only == $value ){
                $show_featured_only_select.=' selected="selected" ';
            }
            $show_featured_only_select.='>'.$value.'</option>';
        }

        // Output the form HTML
        $display='
        <p>
            <label for="'.$this->get_field_id('title').'">Title:</label> </br>
            <input id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.$instance['title'].'" />
        </p>

        <p>
           <label for="'.$this->get_field_id('listing_no').'">How many Listings:</label> </br>
           <input id="'.$this->get_field_id('listing_no').'" name="'.$this->get_field_name('listing_no').'" value="'.$instance['listing_no'].'" />
        </p>

        <p>
            <label for="'.$this->get_field_id('adv_filter_search_action').'">Pick actions</label> </br>
            <select id="'.$this->get_field_id('adv_filter_search_action').'" name="'.$this->get_field_name('adv_filter_search_action').'[]" multiple="multiple" style="width:250px;" >
                '.$actions_select.'
            </select>
        </p>

        <p>
            <label for="'.$this->get_field_id('adv_filter_search_category').'">Pick category</label> </br>
            <select id="'.$this->get_field_id('adv_filter_search_category').'" name="'.$this->get_field_name('adv_filter_search_category').'[]" multiple="multiple" style="width:250px;" >
                '.$categ_select.'
            </select>
        </p>

        <p>
            <label for="'.$this->get_field_id('current_adv_filter_city').'">Pick City</label> </br>
            <select id="'.$this->get_field_id('current_adv_filter_city').'" name="'.$this->get_field_name('current_adv_filter_city').'[]" multiple="multiple" style="width:250px;" >
                '.$select_city.'
            </select>
        </p>

         <p>
            <label for="'.$this->get_field_id('current_adv_filter_area').'">Pick Area</label> </br>
            <select id="'.$this->get_field_id('current_adv_filter_area').'" name="'.$this->get_field_name('current_adv_filter_area').'[]" multiple="multiple" style="width:250px;" >
                '.$select_area.'
            </select>
        </p>

        <p>
            <label for="'.$this->get_field_id('show_featured_only').'">Show featured only </label><br />
            <select id="'.$this->get_field_id('show_featured_only').'" name="'.$this->get_field_name('show_featured_only').'" style="width:250px;" >
                '.$show_featured_only_select.'
            </select>
        </p>

        <p>
            <label for="'.$this->get_field_id('show_as_slider').'">Show as List or Slider ? </label><br />
            <input type="radio" name="'.$this->get_field_name('show_as_slider').'" value="list"';
            if( $instance['show_as_slider'] == 'list'){
                $display.= ' checked ';
            }
            $display.='>List<br>
            <input type="radio" name="'.$this->get_field_name('show_as_slider').'" value="slider"';
            if( $instance['show_as_slider'] == 'slider'){
                $display.= ' checked ';
            }
            $display.='>Slider<br>
        </p>';

        print $display;
    }

    /**
     * Helper method to build taxonomy dropdown options
     * 
     * @param array $tax_terms Array of taxonomy terms
     * @param array $selected_terms Array of selected term names
     * @return string HTML output for dropdown options
     */
    private function build_taxonomy_dropdown($tax_terms, $selected_terms) {
        $dropdown = '<option value="all" ';
        if(!empty($selected_terms) && $selected_terms[0]=='all'){
            $dropdown .= ' selected="selected" ';
        }
        $dropdown .= '>'.esc_html__('all','wpresidence-core').'</option>';
        
        if(!empty($tax_terms)){
            foreach ($tax_terms as $tax_term) {
                $dropdown .= '<option value="' . $tax_term->name . '" ';
                if(in_array($tax_term->name, $selected_terms)){
                    $dropdown .= ' selected="selected" ';
                }
                $dropdown .= '>' . $tax_term->name . '</option>';
            }
        }
        
        return $dropdown;
    }

    /**
     * Save widget options
     * 
     * @param array $new_instance New settings for this instance as submitted by the user
     * @param array $old_instance Old settings for this instance
     * @return array Updated settings to save
     */
    function update($new_instance, $old_instance){
        $instance                               =   $old_instance;
        $instance['title']                      =   $new_instance['title'];
        $instance['listing_no']                 =   $new_instance['listing_no'];
        $instance['adv_filter_search_action']   =   $new_instance['adv_filter_search_action'];
        $instance['adv_filter_search_category'] =   $new_instance['adv_filter_search_category'];
        $instance['current_adv_filter_city']    =   $new_instance['current_adv_filter_city'];
        $instance['current_adv_filter_area']    =   $new_instance['current_adv_filter_area'];
        $instance['show_featured_only']         =   $new_instance['show_featured_only'];
        $instance['show_as_slider']             =   $new_instance['show_as_slider'];
        return $instance;
    }

    /**
     * Frontend display of widget
     * 
     * @param array $args Display arguments including 'before_title', 'after_title',
     *                    'before_widget', and 'after_widget'.
     * @param array $instance The settings for the particular instance of the widget.
     */
    function widget($args, $instance){
        extract($args);

        $wpestate_currency       =   wpresidence_get_option('wp_estate_currency_symbol', '');
        $where_currency =   wpresidence_get_option('wp_estate_where_currency_symbol', '');
        $title          =   apply_filters('widget_title', $instance['title']);

        // Build a unique cache key based on widget settings and user preferences
        $transient_name = $this->get_cached_widget_key($instance);
        
        print $before_widget;
        if($title) {
            print $before_title.$title.$after_title;
        }

        // Try to get cached widget output
        $display = get_transient($transient_name);

        // If no cache exists, generate the widget output
        if($display === false){
            $display = $this->generate_widget_content($instance, $transient_name);
        }

        // Add script for slider functionality
        print '<script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
           estate_sidebar_slider_carousel();
        });
        //]]>
        </script>';

        print $display;
        print $after_widget;
    }

    /**
     * Generate widget content
     * 
     * @param array $instance Widget settings
     * @param string $transient_name Cache key for storing results
     * @return string HTML output
     */
    private function generate_widget_content($instance, $transient_name) {
        $wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
        $where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');
        $show_as_slider = isset($instance['show_as_slider']) ? $instance['show_as_slider'] : 'list';
        
        // Set up query arguments based on widget settings
        $args = $this->build_property_query_args($instance);
        
        $show_as_slider_class = '';
        if($show_as_slider == 'list'){
            $show_as_slider_class = 'list_type';
        }

        $display = '<div class="latest_listings '.$show_as_slider_class.'">';

        // Execute the query with proper ordering
        add_filter('posts_orderby', 'wpestate_my_order');
        $the_query = new WP_Query($args);
        remove_filter('posts_orderby', 'wpestate_my_order');

        if($show_as_slider == 'slider'){
            wp_enqueue_script('owl_carousel');
            $display .= '<div class="owl-featured-slider owl-carousel owl-theme">';
        }

        // Loop through properties
        while($the_query->have_posts()):
            $the_query->the_post();
            
            $price = wpestate_show_price(get_the_ID(), $wpestate_currency, $where_currency, 1);
            $link = esc_url(get_permalink());
            $title = get_the_title();
            
            if($show_as_slider == 'list'){
                $display .= $this->render_list_item($price, $link, $title);
            } else {
                $display .= $this->render_slider_item($link, $title);
            }
        endwhile;

        if($show_as_slider == 'slider'){
            $display .= '</div>';
        }

        wp_reset_query();
        $display .= '</div>';
        
        // Cache the generated output
        if(function_exists('wpestate_html_compress')){
            set_transient($transient_name, wpestate_html_compress($display), 4*60*60);
        } else {
            set_transient($transient_name, $display, 4*60*60);
        }
        
        return $display;
    }

    /**
     * Render list view item
     * 
     * @param string $price Formatted property price
     * @param string $link Property permalink
     * @param string $title Property title
     * @return string HTML for list item
     */
    private function render_list_item($price, $link, $title) {
        $output = '<div class="widget_latest_internal" data-link="'.$link.'">';
        
        $preview = wp_get_attachment_image_src(get_post_thumbnail_id(), 'widget_thumb');
        if(!isset($preview[0]) || $preview[0] == ''){
            $preview = array();
            $preview[0] = get_theme_file_uri('/img/defaults/default_widget_thumb.jpg');
        }
        
        $output .= '<div class="widget_latest_listing_image">
                      <a href="'.$link.'"><img src="'.$preview[0].'" alt="slider-thumb" data-original="'.$preview[0].'" class="lazyload img_responsive" height="70" width="105" /></a>
                    </div>';
        
        $output .= '<div class="listing_name">';
        $output .= '<span class=widget_latest_title><a href="'.$link.'">';
        
        $output .= mb_substr($title, 0, 35);
        if(mb_strlen($title) > 35){
            $output .= '...';
        }
        
        $output .= '</a></span>
                   <span class=widget_latest_price>'.$price.'</span>
                 </div>';
        $output .= '</div>';
        
        return $output;
    }

    /**
     * Render slider view item
     * 
     * @param string $link Property permalink
     * @param string $title Property title
     * @return string HTML for slider item
     */
    private function render_slider_item($link, $title) {
        $thumb_id = get_post_thumbnail_id();
        $preview = wp_get_attachment_image_src($thumb_id, 'property_listings');
        if($preview[0] == ''){
            $preview[0] = get_theme_file_uri('/img/defaults/default_property_listings.jpg');
        }
        
        return '<div class="item">
                  <div class="featured_widget_image" data-link="'.$link.'">
                    <div class="prop_new_details_back"></div>
                    <a href="'.$link.'"><img src="'.$preview[0].'" class="img-responsive" alt="slider-thumb" /></a>
                  </div>
                  <div class="featured_title"><a href="'.$link.'" class="featured_title_link">'.$title.'</a></div>
                </div>';
    }

    /**
     * Build property query arguments
     * 
     * @param array $instance Widget settings
     * @return array Query arguments
     */
    private function build_property_query_args($instance) {
        $current_adv_filter_search_action = $instance['adv_filter_search_action'];
        $current_adv_filter_search_category = $instance['adv_filter_search_category'];
        $current_adv_filter_area = $instance['current_adv_filter_area'];
        $current_adv_filter_city = $instance['current_adv_filter_city'];
        $show_featured_only = $instance['show_featured_only'];
        
        // Initialize tax query arrays
        $area_array = $city_array = $action_array = $categ_array = '';
        
        // Build action category query
        if(!empty($current_adv_filter_search_action) && $current_adv_filter_search_action[0] != 'all'){
            $taxcateg_include = array();
            
            foreach($current_adv_filter_search_action as $value){
                $taxcateg_include[] = sanitize_title($value);
            }
            
            $categ_array = array(
                'taxonomy' => 'property_action_category',
                'field' => 'slug',
                'terms' => $taxcateg_include
            );
        }
        
        // Build property category query
        if(!empty($current_adv_filter_search_category) && $current_adv_filter_search_category[0] != 'all'){
            $taxaction_include = array();
            
            foreach($current_adv_filter_search_category as $value){
                $taxaction_include[] = sanitize_title($value);
            }
            
            $action_array = array(
                'taxonomy' => 'property_category',
                'field' => 'slug',
                'terms' => $taxaction_include
            );
        }
        
        // Build city query
        if(!empty($current_adv_filter_city) && $current_adv_filter_city[0] != 'all'){
            $taxaction_include = array();
            
            foreach($current_adv_filter_city as $value){
                $taxaction_include[] = sanitize_title($value);
            }
            
            $city_array = array(
                'taxonomy' => 'property_city',
                'field' => 'slug',
                'terms' => $taxaction_include
            );
        }
        
        // Build area query
        if(!empty($current_adv_filter_area) && $current_adv_filter_area[0] != 'all'){
            $taxaction_include = array();
            
            foreach($current_adv_filter_area as $value){
                $taxaction_include[] = sanitize_title($value);
            }
            
            $area_array = array(
                'taxonomy' => 'property_area',
                'field' => 'slug',
                'terms' => $taxaction_include
            );
        }
        
        // Build meta query for featured properties
        $meta_query = array();
        if($show_featured_only == 'yes'){
            $meta_query[] = array(
                'key' => 'prop_featured',
                'value' => 1,
                'type' => 'numeric',
                'compare' => '='
            );
        }
        
        // Construct the final query args
        return array(
            'post_type' => 'estate_property',
            'post_status' => 'publish',
            'paged' => 1,
            'posts_per_page' => $instance['listing_no'],
            'orderby' => 'id',
            'meta_key' => 'prop_featured',
            'order' => 'DESC',
            'meta_query' => $meta_query,
            'tax_query' => array(
                'relation' => 'AND',
                $categ_array,
                $action_array,
                $city_array,
                $area_array
            )
        );
    }

    /**
     * Generate a unique cache key for the widget
     * 
     * @param array $instance Widget settings
     * @return string Cache key
     */
    private function get_cached_widget_key($instance) {
        $transient_name = 'wpestate_widget_recent_query_output_';
        
        // Add taxonomy filters to key
        if(!empty($instance['adv_filter_search_action']) && $instance['adv_filter_search_action'][0] != 'all'){
            foreach($instance['adv_filter_search_action'] as $value){
                $transient_name .= '_'.sanitize_title($value);
            }
        }
        
        if(!empty($instance['adv_filter_search_category']) && $instance['adv_filter_search_category'][0] != 'all'){
            foreach($instance['adv_filter_search_category'] as $value){
                $transient_name .= '_'.sanitize_title($value);
            }
        }
        
        if(!empty($instance['current_adv_filter_city']) && $instance['current_adv_filter_city'][0] != 'all'){
            foreach($instance['current_adv_filter_city'] as $value){
                $transient_name .= '_'.sanitize_title($value);
            }
        }
        
        if(!empty($instance['current_adv_filter_area']) && $instance['current_adv_filter_area'][0] != 'all'){
            foreach($instance['current_adv_filter_area'] as $value){
                $transient_name .= '_'.sanitize_title($value);
            }
        }
        
        // Add other widget settings to key
        $transient_name .= '_'.$instance['show_featured_only'].'_'.$instance['listing_no'].'_DESC_prop_featured';
        
        // Add language code for multilingual sites
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_name = wpestate_set_transient_name_multilang( $transient_name . '_' );
        } else {
            $transient_name .= '_';
        }

        // Add currency preferences
        if(isset($_COOKIE['my_custom_curr_symbol'])){
            $transient_name .= '_'.$_COOKIE['my_custom_curr_symbol'];
        }
        
        if(isset($_COOKIE['my_measure_unit'])){
            $transient_name .= $_COOKIE['my_measure_unit'];
        }
        
        return $transient_name;
    }
}

?>
