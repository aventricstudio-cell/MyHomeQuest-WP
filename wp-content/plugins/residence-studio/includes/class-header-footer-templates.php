<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WpResidence_Elementor_Header_Footer_Templates {

    public $header_footer_templates = [];

    public function __construct() {
        add_action('wp', [$this, 'initialize_header_footer_templates']);
    }

    /**
     * Initialize header and footer templates
     */
    public function initialize_header_footer_templates() {
        $this->header_footer_templates = $this->display_head_foot();
    }

    /**
     * Display custom Elementor header
     */
    public function display_custom_elementor_header() {
        $first_before_header_key = array_search('wpestate_template_before_header', $this->header_footer_templates);
        $wpestate_template_header = array_search('wpestate_template_header', $this->header_footer_templates);
        $wpestate_template_after_header = array_search('wpestate_template_after_header', $this->header_footer_templates);

        echo '<div class="wpestate_elementor_header_custom">';
        if ($first_before_header_key !== false) {
            $this->render_elementor_post($first_before_header_key);
        }
        if ($wpestate_template_header !== false) {
            $this->render_elementor_post($wpestate_template_header);
        }
        if ($wpestate_template_after_header !== false) {
            $this->render_elementor_post($wpestate_template_after_header);
        }
        echo '</div>';
    }
    
    
    /**
     * Display custom Elementor footer
     */
    public function display_custom_elementor_footer() {
        $first_before_footer_key        = array_search('wpestate_template_before_footer', $this->header_footer_templates);
        $wpestate_template_footer       = array_search('wpestate_template_footer', $this->header_footer_templates);
        $wpestate_template_after_footer = array_search('wpestate_template_after_footer', $this->header_footer_templates);

        echo '<div class="wpestate_elementor_footer_custom">';
            if ($first_before_footer_key !== false) {
                $this->render_elementor_post($first_before_footer_key);
            }
            if ($wpestate_template_footer !== false) {
                $this->render_elementor_post($wpestate_template_footer);
            }
            if ($wpestate_template_after_footer !== false) {
                $this->render_elementor_post($wpestate_template_after_footer);
            }
        echo '</div>';
    }
    
    
    
    
    
    
    
    
    
    /**
     * Render Elementor post content
     *
     * @param int $post_id The ID of the post to render.
     */
    public function render_elementor_post($post_id) {
        // Use the generic template renderer which also supports WPBakery
        echo WpResidence_Render_Template::get_elementor_template( $post_id );
    }

    /**
     * Display header and footer based on conditions
     *
     * @return array The array of header and footer templates.
     */
    public function display_head_foot() {
        $conditions = $this->build_conditions_for_post();
       
        $args = array(
            'post_type' => 'wpestate-studio',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        // wpml fix requested
        if (function_exists('icl_translate') ){
            $args [ 'suppress_filters'] = false;
        }

        $posts = get_posts($args);

        if (empty($posts)) {
            return [];
        }

        $return = array();
        foreach ($posts as $post) {
         
            $positions = get_post_meta($post->ID, 'wpestate_head_foot_positions', true);
            if (empty($positions)) {
                $single = get_post_meta($post->ID, 'wpestate_head_foot_position', true);
                $positions = $single ? array($single) : array();
            }
            if (!is_array($positions)) {
                $positions = array($positions);
            }

            $exclude_positions = get_post_meta($post->ID, 'wpestate_head_foot_exclude_positions', true);
            if (empty($exclude_positions)) {
                $exclude_positions = array();
            }
            if (!is_array($exclude_positions)) {
                $exclude_positions = array($exclude_positions);
            }

            $tax_terms            = get_post_meta($post->ID, 'wpestate_head_foot_tax_terms', true);
            $exclude_tax_terms    = get_post_meta($post->ID, 'wpestate_head_foot_exclude_tax_terms', true);
            if (!is_array($tax_terms)) {
                $tax_terms = array();
            }
            if (!is_array($exclude_tax_terms)) {
                $exclude_tax_terms = array();
            }

            $exclude_found = false;
            foreach ($exclude_positions as $idx => $exclude_position) {
                $ex_term = isset($exclude_tax_terms[$idx]) ? $exclude_tax_terms[$idx] : '';
                if ($this->wpestate_match_position($exclude_position, $ex_term, $conditions)) {
                    $exclude_found = true;
                    break;
                }
            }

            if ($exclude_found) {
                continue;
            }

            $found = false;
            foreach ($positions as $idx => $position) {
                $term = isset($tax_terms[$idx]) ? $tax_terms[$idx] : '';
                if ($this->wpestate_match_position($position, $term, $conditions)) {
                    $return[$post->ID] = get_post_meta($post->ID, 'wpestate_head_foot_template', true);
                    $found = true;
                    break;
                }
            }

        }
  
        return array_unique($return);
    }

    /**
     * Check if a position matches current conditions considering taxonomy terms.
     *
     * @param string $position Selected location (post type, taxonomy, etc).
     * @param string $term     Optional taxonomy term in format "taxonomy:term".
     * @param array  $conditions Conditions built for current request.
     *
     * @return bool True if the position matches.
     */
    private function wpestate_match_position($position, $term, $conditions) {
         foreach ($conditions as $condition) {
            $condition_parts = explode(':', $condition['value']);
            $condition_base  = $condition_parts[0];

            if ($position === 'standard-singulars' && is_singular()) {
              
                return true;
            }
            if ($position === 'standard-archives' && is_archive()) {
              
                return true;
            }
            if ($position === 'standard-global') {
              
                return true;
            }

            if ($condition_base === $position) {
                if ($term) {
                    if ($condition['value'] === $term) {
                       
                        return true;
                    }
                } else {

                    return true;
                }
            }
        }

     
        return false;
    }

    /**
     * Build conditions for the current post or page
     *
     * @return array Array of conditions.
     */
    public function build_conditions_for_post() {
        $conditions = [];
        if (is_front_page()) {
            $conditions[] = ['type' => 'special', 'value' => 'front_page'];
        } elseif (is_home()) {
            $conditions[] = ['type' => 'special', 'value' => 'blog'];
        } elseif (is_404()) {
            $conditions[] = ['type' => 'special', 'value' => '404'];
        } elseif (is_search()) {
            $conditions[] = ['type' => 'special', 'value' => 'search'];
        } elseif (is_date()) {
            $conditions[] = ['type' => 'special', 'value' => 'date'];
        } elseif (is_author()) {
            $conditions[] = ['type' => 'special', 'value' => 'author'];
        } elseif (function_exists('is_shop') && is_shop()) {
            $conditions[] = ['type' => 'special', 'value' => 'woocommerce_shop'];
        }
        if (is_archive()) {
            $conditions[] = ['type' => 'archive', 'value' => 'archive'];
        }
        if (is_tax() || is_category() || is_tag()) {
            $taxonomy = get_queried_object();
            if ($taxonomy && !is_wp_error($taxonomy)) {
                $conditions[] = ['type' => 'taxonomy', 'value' => $taxonomy->taxonomy . ':' . $taxonomy->term_id];
            }
        }
        if (is_singular()) {
            $post = get_post();
            if (!empty($post->post_type)) {
                $conditions[] = ['type' => 'post_type', 'value' => $post->post_type];
            }
            $conditions[] = ['type' => 'specific', 'value' => $post->ID];
            $taxonomies = get_object_taxonomies( $post->post_type );
            foreach ( $taxonomies as $tax_slug ) {
                $term_ids = wp_get_object_terms( $post->ID, $tax_slug, array( 'fields' => 'ids' ) );
                if ( ! is_wp_error( $term_ids ) ) {
                    foreach ( $term_ids as $t_id ) {
                        $conditions[] = ['type' => 'taxonomy', 'value' => $tax_slug . ':' . $t_id];
                    }
                }
            }
        }
     
        return $conditions;
    }

    /**
     * Helper function to remove and display information
     */
    public function wpestate_helper_remove() {
    
        global $post;    return;
        $post_id = isset($post->ID) ? $post->ID : null;
        $conditions = $this->build_conditions_for_post($post_id);

        echo '<pre style="margin-top:100px;"> for ' . $post_id;
        print_r($conditions);
        echo '</pre>';
        echo '<pre style="margin-top:100px;"> for id: ' . $post_id . '  ';
        print_r($this->header_footer_templates);

        foreach ($this->header_footer_templates as $key => $value) {
            print 'we show ' . get_the_title($key) . '</br>';
        }
        echo '</pre>';
    }
}
?>
