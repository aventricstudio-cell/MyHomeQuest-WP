<?php
class WpResidence_Custom_Post_Type {
    private $tabs = array(
        'all'       => 'All',
        'header'    => 'Header',
        'footer'    => 'Footer',
        'property'  => 'Property',
        'agent'     => 'Agent',
        'agency'    => 'Agency',
        'developer' => 'Developer',
        'taxonomies'=> 'Taxonomies',
        'post'      => 'Single Post',
        'block'     => 'Block',
    );

    public function __construct() {

        add_action('init', [$this, 'register_custom_post_type']);
        add_filter('views_edit-wpestate-studio', [$this, 'template_tabs']);
        add_action('pre_get_posts', [$this, 'apply_tab_filter']);
        add_action('init', [$this, 'enable_wpbakery']);

    }





public function enable_wpbakery() {
      if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
          vc_set_default_editor_post_types( array( 'page', 'post', 'wpestate-studio' ) );
      }
}





public function register_custom_post_type() {
    // Check if the branding function exists, otherwise use default
    $branding = function_exists('wpresidence_theme_branding') ? wpresidence_theme_branding() : 'WpResidence';
    // Check if branding logo function exists and get icon
    $icon = 'dashicons-welcome-widgets-menus'; // Default fallback
    if (function_exists('wpresidence_get_theme_branding_logo_url')) {
        $branding_logo = wpresidence_get_theme_branding_logo_url();
        if (!empty($branding_logo)) {
            $icon = $branding_logo;
        }
    }

    
    register_post_type('wpestate-studio', array(
        'labels' => array(
            'name' => sprintf(esc_html__('%s Studio Templates', 'wpestate-studio-templates'), $branding),
            'singular_name' => sprintf(esc_html__('%s Studio Templates', 'wpestate-studio-templates'), $branding),
            'add_new' => sprintf(esc_html__('Add New %s Studio Template', 'wpestate-studio-templates'), $branding),
            'add_new_item' => sprintf(esc_html__('Add %s Studio Template', 'wpestate-studio-templates'), $branding),
            'edit' => sprintf(esc_html__('Edit %s Studio Templates', 'wpestate-studio-templates'), $branding),
            'edit_item' => sprintf(esc_html__('Edit %s Studio Template', 'wpestate-studio-templates'), $branding),
            'new_item' => sprintf(esc_html__('New %s Studio Template', 'wpestate-studio-templates'), $branding),
            'view' => sprintf(esc_html__('View %s Studio Templates', 'wpestate-studio-templates'), $branding),
            'view_item' => sprintf(esc_html__('View %s Studio Template', 'wpestate-studio-templates'), $branding),
            'search_items' => sprintf(esc_html__('Search %s Studio Templates', 'wpestate-studio-templates'), $branding),
            'not_found' => sprintf(esc_html__('No %s Studio Templates found', 'wpestate-studio-templates'), $branding),
            'not_found_in_trash' => sprintf(esc_html__('No %s Studio Templates found in trash', 'wpestate-studio-templates'), $branding),
            'parent' => sprintf(esc_html__('Parent %s Studio Templates', 'wpestate-studio-templates'), $branding)
        ),
        'public' => true,
        'has_archive' => false,
        'hierarchical' => false,
        'can_export' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'rewrite' => array('slug' => 'wpestate-studio-templates'),
        'supports' => array('title', 'thumbnail', 'page-attributes', 'editor'),
        'can_export' => true,
        'show_in_rest' => true,
        'rest_base' => 'wpestate-studio-templates',
    
        'exclude_from_search' => true,
        'menu_icon' => $icon,
        'menu_position' => 4,
    ));
    add_post_type_support('wpestate-studio', 'elementor');
}









    public function template_tabs($views) {
        $current = isset($_GET['template_tab']) ? sanitize_key($_GET['template_tab']) : 'all';
        $base = admin_url('edit.php?post_type=wpestate-studio');
        foreach ($this->tabs as $slug => $label) {
            $url = $slug === 'all' ? $base : add_query_arg('template_tab', $slug, $base);
            $class = $current === $slug ? 'class="current"' : '';
            $count = $this->count_tab($slug);
            $views[$slug] = '<a href="' . esc_url($url) . '" ' . $class . '>' . esc_html($label) . ' <span class="count">(' . intval($count) . ')</span></a>';
        }
        return $views;
    }

    public function apply_tab_filter($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        if ($query->get('post_type') !== 'wpestate-studio') {
            return;
        }
        $tab = isset($_GET['template_tab']) ? sanitize_key($_GET['template_tab']) : 'all';
        if ($tab === 'all') {
            return;
        }
        $args = $this->get_tab_query_args($tab);
        if (!empty($args['meta_query'])) {
            $query->set('meta_query', $args['meta_query']);
        }
    }

    private function count_tab($tab) {
        $args = $this->get_tab_query_args($tab);
        $query = new WP_Query($args);
        return $query->found_posts;
    }

    private function get_tab_query_args($tab) {
        $args = array(
            'post_type'      => 'wpestate-studio',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'post_status'    => 'any',
        );

        switch ($tab) {
            case 'header':
                $args['meta_query'][] = array(
                    'key'     => 'wpestate_head_foot_template',
                    'value'   => array('wpestate_template_header', 'wpestate_template_before_header', 'wpestate_template_after_header'),
                    'compare' => 'IN',
                );
                break;
            case 'footer':
                $args['meta_query'][] = array(
                    'key'     => 'wpestate_head_foot_template',
                    'value'   => array('wpestate_template_footer', 'wpestate_template_before_footer', 'wpestate_template_after_footer'),
                    'compare' => 'IN',
                );
                break;
            case 'property':
                $args['meta_query'][] = array(
                    'key'   => 'wpestate_head_foot_template',
                    'value' => 'wpestate_single_property_page',
                );
                break;
            case 'agent':
                $args['meta_query'][] = array(
                    'key'   => 'wpestate_head_foot_template',
                    'value' => 'wpestate_single_agent',
                );
                break;
            case 'agency':
                $args['meta_query'][] = array(
                    'key'   => 'wpestate_head_foot_template',
                    'value' => 'wpestate_single_agency',
                );
                break;
            case 'developer':
                $args['meta_query'][] = array(
                    'key'   => 'wpestate_head_foot_template',
                    'value' => 'wpestate_single_developer',
                );
                break;
            case 'block':
                $args['meta_query'][] = array(
                    'key'   => 'wpestate_head_foot_template',
                    'value' => 'wpestate_template_custom_block',
                );
                break;
            case 'taxonomies':
                $loc = wpestate_templates_selection_options();
                $tax = array_keys($loc['taxonomies']['value']);
                $args['meta_query'][] = array(
                     'key'   => 'wpestate_head_foot_template',
                    'value' => 'wpestate_category_page',
                );
                break;
            case 'special':
                $special = array_keys(wpestate_templates_special_pages());
                $args['meta_query'][] = array(
                    'key'     => 'wpestate_head_foot_positions',
                    'value'   => $special,
                    'compare' => 'IN',
                );
                break;
            case 'post':
                $args['meta_query'][] = array(
                    'key'   => 'wpestate_head_foot_template',
                    'value' => 'wpestate_single_post',
                );
                break;
        }
        return $args;
    }
}

new WpResidence_Custom_Post_Type();
