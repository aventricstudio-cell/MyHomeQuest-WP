<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wpestate_get_header_id() {
    $header_id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_template_header');
    return $header_id !== '' ? $header_id : false;
}

function wpestate_header_enabled() {
    return apply_filters('wpestate_header_enabled', wpestate_get_header_id() !== false);
}

function wpestate_header_template_id() {
    return apply_filters('wpestate_header_template_id', wpestate_get_header_id());
}

function wpestate_get_header_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_header_template_id());
}

function wpestate_render_header() {
    if (!wpestate_header_enabled()) {
        return;
    }
    echo '<header itemscope="itemscope" itemtype="http://schema.org/WPHeader">';
    wpestate_get_header_template();
    echo '</header>';
}

function wpestate_get_before_header_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_template_before_header');
   return $id !== '' ? $id : false;
}

function wpestate_before_header_enabled() {
    return apply_filters('wpestate_before_header_enabled', wpestate_get_before_header_id() !== false);
}

function wpestate_before_header_template_id() {
    return apply_filters('wpestate_before_header_template_id', wpestate_get_before_header_id());
}

function wpestate_get_before_header_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_before_header_template_id());
}

function wpestate_render_before_header() {
    if (!wpestate_before_header_enabled()) {
        return;
    }
    wpestate_get_before_header_template();
}

function wpestate_get_after_header_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_template_after_header');
    return $id !== '' ? $id : false;
}

function wpestate_after_header_enabled() {
    return apply_filters('wpestate_after_header_enabled', wpestate_get_after_header_id() !== false);
}

function wpestate_after_header_template_id() {
    return apply_filters('wpestate_after_header_template_id', wpestate_get_after_header_id());
}

function wpestate_get_after_header_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_after_header_template_id());
}

function wpestate_render_after_header() {
    if (!wpestate_after_header_enabled()) {
        return;
    }
    wpestate_get_after_header_template();
}

function wpestate_get_footer_id() {
    $footer_id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_template_footer');
    return $footer_id !== '' ? $footer_id : false;
}

function wpestate_footer_enabled() {
    return apply_filters('wpestate_footer_enabled', wpestate_get_footer_id() !== false);
}

function wpestate_footer_template_id() {
    return apply_filters('wpestate_footer_template_id', wpestate_get_footer_id());
}

function wpestate_get_footer_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_footer_template_id());
}

function wpestate_render_footer() {
    if (!wpestate_footer_enabled()) {
        return;
    }
    echo '<footer itemscope="itemscope" itemtype="http://schema.org/WPFooter">';
    wpestate_get_footer_template();
    echo '</footer>';
}

function wpestate_get_before_footer_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_template_before_footer');
   return $id !== '' ? $id : false;
}

function wpestate_before_footer_enabled() {
    return apply_filters('wpestate_before_footer_enabled', wpestate_get_before_footer_id() !== false);
}

function wpestate_before_footer_template_id() {
    return apply_filters('wpestate_before_footer_template_id', wpestate_get_before_footer_id());
}

function wpestate_get_before_footer_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_before_footer_template_id());
}

function wpestate_render_before_footer() {
    if (!wpestate_before_footer_enabled()) {
        return;
    }
    wpestate_get_before_footer_template();
}

function wpestate_get_after_footer_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_template_after_footer');
    return $id !== '' ? $id : false;
}

function wpestate_after_footer_enabled() {
    return apply_filters('wpestate_after_footer_enabled', wpestate_get_after_footer_id() !== false);
}

function wpestate_after_footer_template_id() {
    return apply_filters('wpestate_after_footer_template_id', wpestate_get_after_footer_id());
}

function wpestate_get_after_footer_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_after_footer_template_id());
}

function wpestate_render_after_footer() {
    if (!wpestate_after_footer_enabled()) {
        return;
    }
    wpestate_get_after_footer_template();
}

function wpestate_get_single_property_id() {
    global $post;

    $local_id = false;
    if ( isset( $post->ID ) ) {
        $local_id = get_post_meta( $post->ID, 'property_page_desing_local', true );
    }

    if ( ! empty( $local_id ) ) {
        $id = $local_id;
    } else {
        $id = WpResidence_Render_Template::instance()->fetch_plugin_settings( 'wpestate_single_property_page' );
    }

    return $id !== '' ? $id : false;
}

function wpestate_single_property_enabled() {
    return apply_filters('wpestate_single_property_enabled', wpestate_get_single_property_id() !== false);
}

function wpestate_single_property_template_id() {
    return apply_filters('wpestate_single_property_template_id', wpestate_get_single_property_id());
}

function wpestate_get_single_property_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_single_property_template_id());
}

function wpestate_render_single_property() {
    if (!wpestate_single_property_enabled()) {
        return;
    }

    $wpestate_wide_elementor_page_class = '';
    $template_id = wpestate_single_property_template_id();
    if ($template_id) {
        $full_width = get_post_meta($template_id, 'wpestate_custom_full_width', true);
        if ($full_width === 'yes') {
            $wpestate_wide_elementor_page_class = 'wpestate_wide_elementor_page';
        }
    }

    ?>

        <div class="container content_wrapper wpestate_content_wrapper_custom_template <?php echo esc_attr($wpestate_wide_elementor_page_class); ?>">
            <div class="wpestate_content_wrapper_custom_template_wrapper">
                <?php wpestate_get_single_property_template(); ?>
            </div>
        </div>
    <?php
}

function wpestate_get_single_agent_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_single_agent');
    return $id !== '' ? $id : false;
}

function wpestate_single_agent_enabled() {
    return apply_filters('wpestate_single_agent_enabled', wpestate_get_single_agent_id() !== false);
}

function wpestate_single_agent_template_id() {
    return apply_filters('wpestate_single_agent_template_id', wpestate_get_single_agent_id());
}

function wpestate_get_single_agent_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_single_agent_template_id());
}

function wpestate_render_single_agent() {
    if (!wpestate_single_agent_enabled()) {
        return;
    }
    echo '<div class="wpestate-single-agent">';
    wpestate_get_single_agent_template();
    echo '</div>';
}

function wpestate_get_single_agency_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_single_agency');
    return $id !== '' ? $id : false;
}

function wpestate_single_agency_enabled() {
    return apply_filters('wpestate_single_agency_enabled', wpestate_get_single_agency_id() !== false);
}

function wpestate_single_agency_template_id() {
    return apply_filters('wpestate_single_agency_template_id', wpestate_get_single_agency_id());
}

function wpestate_get_single_agency_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_single_agency_template_id());
}

function wpestate_render_single_agency() {
    if (!wpestate_single_agency_enabled()) {
        return;
    }
    echo '<div class="wpestate-single-agency">';
    wpestate_get_single_agency_template();
    echo '</div>';
}

function wpestate_get_single_developer_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_single_developer');
     return $id !== '' ? $id : false;
}

function wpestate_single_developer_enabled() {
    return apply_filters('wpestate_single_developer_enabled', wpestate_get_single_developer_id() !== false);
}

function wpestate_single_developer_template_id() {
    return apply_filters('wpestate_single_developer_template_id', wpestate_get_single_developer_id());
}

function wpestate_get_single_developer_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_single_developer_template_id());
}

function wpestate_render_single_developer() {
    if (!wpestate_single_developer_enabled()) {
        return;
    }
    echo '<div class="wpestate-single-developer">';
    wpestate_get_single_developer_template();
    echo '</div>';
}

function wpestate_get_single_post_id() {
    $id = WpResidence_Render_Template::instance()->fetch_plugin_settings('wpestate_single_post');
    return $id !== '' ? $id : false;
}

function wpestate_single_post_enabled() {
    return apply_filters('wpestate_single_post_enabled', wpestate_get_single_post_id() !== false);
}

function wpestate_single_post_template_id() {
    return apply_filters('wpestate_single_post_template_id', wpestate_get_single_post_id());
}

function wpestate_get_single_post_template() {
    echo WpResidence_Render_Template::get_elementor_template(wpestate_single_post_template_id());
}

function wpestate_render_single_post() {
    if (!wpestate_single_post_enabled()) {
        return;
    }
    echo '<div class="wpestate-single-post-wpresidence-studio">';
    wpestate_get_single_post_template();
    echo '</div>';
}

/**
 * Check if a taxonomy/term pair matches a template rule.
 *
 * @param string $position Taxonomy slug stored in the location field.
 * @param string $term_val Optional term value in format "taxonomy:term".
 * @param string $tax      Current taxonomy slug.
 * @param int    $term_id  Current term ID.
 * @return bool True on match.
 */
function wpestate_category_match( $position, $term_val, $tax, $term_id ) {
    if ( $term_val ) {
        return $term_val === $tax . ':' . $term_id;
    }

    return $position === $tax;
}

/**
 * Locate a design studio template for a taxonomy archive.
 *
 * @param string $tax     Taxonomy slug.
 * @param int    $term_id Term ID.
 * @return int Template post ID or 0 if none found.
 */
function wpestate_get_category_template_id( $tax, $term_id = 0 ) {
    $args = array(
        'post_type'      => 'wpestate-studio',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => 'wpestate_head_foot_template',
        'meta_value'     => 'wpestate_category_page',
    );

    $posts = get_posts( $args );
    if ( empty( $posts ) ) {
        return 0;
    }

    foreach ( $posts as $post ) {
        $positions         = get_post_meta( $post->ID, 'wpestate_head_foot_positions', true );
        $exclude_positions = get_post_meta( $post->ID, 'wpestate_head_foot_exclude_positions', true );
        $tax_terms         = get_post_meta( $post->ID, 'wpestate_head_foot_tax_terms', true );
        $exclude_terms     = get_post_meta( $post->ID, 'wpestate_head_foot_exclude_tax_terms', true );

        $positions         = is_array( $positions ) ? $positions : ( $positions ? array( $positions ) : array() );
        $exclude_positions = is_array( $exclude_positions ) ? $exclude_positions : ( $exclude_positions ? array( $exclude_positions ) : array() );
        $tax_terms         = is_array( $tax_terms ) ? $tax_terms : array();
        $exclude_terms     = is_array( $exclude_terms ) ? $exclude_terms : array();

        // Skip if any exclude rule matches.
        foreach ( $exclude_positions as $idx => $ex_pos ) {
            $ex_term = isset( $exclude_terms[ $idx ] ) ? $exclude_terms[ $idx ] : '';
            if ( wpestate_category_match( $ex_pos, $ex_term, $tax, $term_id ) ) {
                continue 2;
            }
        }

        foreach ( $positions as $idx => $pos ) {
            $term_val = isset( $tax_terms[ $idx ] ) ? $tax_terms[ $idx ] : '';
            if ( wpestate_category_match( $pos, $term_val, $tax, $term_id ) ) {
                return (int) $post->ID;
            }
        }
    }

    return 0;
}

/**
 * Get the category template ID for the current archive.
 *
 * @return int Template ID or 0.
 */
function wpestate_current_category_template_id() {
    if ( ! ( is_tax() || is_category() || is_tag() ) ) {
        return 0;
    }

    $obj = get_queried_object();
    if ( empty( $obj ) || is_wp_error( $obj ) ) {
        return 0;
    }

    return wpestate_get_category_template_id( $obj->taxonomy, $obj->term_id );
}

/**
 * Output the category template for the current archive if available.
 */
function wpestate_render_current_category_template() {
    $id = wpestate_current_category_template_id();
    if ( $id ) {
        echo WpResidence_Render_Template::get_elementor_template( $id );
    }
}



add_action('save_post_wpestate-studio', 'wpestate_clear_property_template_cache');
add_action('deleted_post', 'wpestate_clear_property_template_cache_on_delete');

/**
 * Clear cache when a wpestate-studio post is added or updated
 */
function wpestate_clear_property_template_cache( $post_id ) {
    // Prevent autosave or revision triggers
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    if ( get_post_type( $post_id ) === 'wpestate-studio' ) {
        delete_transient( 'wpestate_property_page_templates' );
     }
}

/**
 * Clear cache when a wpestate-studio post is deleted
 */
function wpestate_clear_property_template_cache_on_delete( $post_id ) {
    if ( get_post_type( $post_id ) === 'wpestate-studio' ) {
        delete_transient( 'wpestate_property_page_templates' );
    }
}


/**
 * Get the latest wpestate-studio post ID.
 *
 * @return int Post ID or 0 if none found.
 */
function wpestate_last_post_id() {
    $query = new WP_Query([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'ID',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ]);
    return !empty($query->posts) ? (int) $query->posts[0] : 0;

}
function wpestate_enqueue_template_css() {
    $template_functions = array(
        'wpestate_header_template_id',
        'wpestate_before_header_template_id',
        'wpestate_after_header_template_id',
        'wpestate_footer_template_id',
        'wpestate_before_footer_template_id',
        'wpestate_after_footer_template_id',
        'wpestate_single_property_template_id',
        'wpestate_single_agent_template_id',
        'wpestate_single_agency_template_id',
        'wpestate_single_developer_template_id',
        'wpestate_single_post_template_id',
        'wpestate_current_category_template_id',
    );

    foreach ( $template_functions as $func ) {
        if ( function_exists( $func ) ) {
            $id = call_user_func( $func );
            if ( $id && did_action( 'elementor/loaded' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $id );
                $css_file->enqueue();
            }
        }
    }
}

add_action( 'wp_enqueue_scripts', 'wpestate_enqueue_template_css', 5 );
function wpestate_category_template_enabled() {
    return wpestate_current_category_template_id() !== 0;
}