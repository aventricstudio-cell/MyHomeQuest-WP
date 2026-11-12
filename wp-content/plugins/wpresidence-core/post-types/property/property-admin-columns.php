<?php

use function GuzzleHttp\json_encode;
/**
 * Property Admin Columns Management
 *
 * This file handles the customization of WordPress admin columns for properties,
 * property cities, and property areas. It defines column layouts, populates column
 * content, and manages sorting functionality for the property listing interface
 * in the WordPress admin panel.
 *
 * @package WPResidence
 * @subpackage Core
 * @since 1.0
 */

///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Manage property lists
///////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('manage_edit-estate_property_columns', 'wpestate_my_columns');

if (!function_exists('wpestate_my_columns')):
    /**
     * Defines custom columns for property listings in admin
     * 
     * Customizes the columns shown in the properties list table including:
     * ID, thumbnail, location info, property details, pricing, and featured status
     *
     * @param array $columns The default WordPress columns
     * @return array Modified array of columns
     */
    function wpestate_my_columns($columns) {
    unset($columns['comments']);
    unset($columns['author']);
    unset($columns['date']);
    unset($columns['revealid_id']);
    
    $custom_columns = array(
        'estate_thumb' => esc_html__('Image', 'wpresidence-core'),
        'title' => esc_html__('Title', 'wpresidence-core'),
        'estate_info' => esc_html__('Info', 'wpresidence-core'),
        'estate_price' => esc_html__('Price', 'wpresidence-core'),
        'estate_status' => esc_html__('Status', 'wpresidence-core'),
        'estate_featured' => esc_html__('Featured', 'wpresidence-core'),
        'estate_user_date' => esc_html__('User / Date', 'wpresidence-core'),
        'estate_ID' => esc_html__('ID', 'wpresidence-core'),
        'estate_actions' => esc_html__('Actions', 'wpresidence-core'),
    );
    
    $ordered = array();
    $ordered['estate_thumb'] = $custom_columns['estate_thumb'];
    $ordered['title'] = $custom_columns['title'];
    
    foreach ($custom_columns as $key => $label) {
        if ($key !== 'estate_thumb' && $key !== 'title') {
            $ordered[$key] = $label;
        }
    }
    
    $columns = array_merge($columns, $ordered);
   
 
    // Switch title and estate_thumb
    $reordered = array();
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $reordered['estate_thumb'] = $columns['estate_thumb'];
            $reordered['title'] = $value;
            unset($columns['estate_thumb']);
        } elseif ($key !== 'estate_thumb') {
            $reordered[$key] = $value;
        }
    }
    
    return $reordered;
    }

endif;


add_action('manage_posts_custom_column', 'wpestate_populate_columns', 10, 2);
if (!function_exists('wpestate_populate_columns')):
    /**
     * Populates content for custom columns in property listings
     * 
     * Handles the display of data in each custom column including contact information,
     * property details, taxonomies, pricing, and featured status.
     *
     * @param string $column The name of the column being populated
     * @return void
     */
    function wpestate_populate_columns($column, $the_id) {
        // global $post;
        // $the_id = $post->ID;

        if ('estate_ID' == $column) {
            echo $the_id;
        } else if ('estate_agent_email' == $column) {
            $agent_email = get_post_meta($the_id, 'agent_email', true);
            echo $agent_email;
        } else if ('estate_agency_email' == $column) {
            $agent_email = get_post_meta($the_id, 'agency_email', true);
            echo $agent_email;
        } else if ('estate_agency_email' == $column) {
            $agent_email = get_post_meta($the_id, 'agency_email', true);
            echo $agent_email;
        } else if ('estate_developer_email' == $column) {
            $agent_email = get_post_meta($the_id, 'developer_email', true);
            echo $agent_email;
        } else if ('estate_agency_phone' == $column) {
            $agent_phone = get_post_meta($the_id, 'agency_phone', true);
            $agent_mobile = get_post_meta($the_id, 'agency_mobile', true);
            echo $agent_phone . ' / ' . $agent_mobile;
        } else if ('estate_developer_phone' == $column) {
            $agent_phone = get_post_meta($the_id, 'developer_phone', true);
            $agent_mobile = get_post_meta($the_id, 'developer_mobile', true);
            echo $agent_phone . ' / ' . $agent_mobile;
        } else if ('estate_agent_phone' == $column) {
            $agent_phone = get_post_meta($the_id, 'agent_phone', true);
            $agent_mobile = get_post_meta($the_id, 'agent_mobile', true);
            echo $agent_phone . ' / ' . $agent_mobile;
        } else if ('estate_agent_city' == $column) {
            if (taxonomy_exists('property_city_agent')) {
                $estate_action = get_the_term_list($the_id, 'property_city_agent', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    echo $estate_action;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_agent_action' == $column) {
            if (taxonomy_exists('property_action_category_agent')) {
                $estate_action = get_the_term_list($the_id, 'property_action_category_agent', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    echo $estate_action;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_agent_category' == $column) {
            if (taxonomy_exists('property_category_agent')) {
                $estate_category = get_the_term_list($the_id, 'property_category_agent', '', ', ', '');
                if (!is_wp_error($estate_category)) {
                    echo $estate_category;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_agency_city' == $column) {
            if (taxonomy_exists('city_agency')) {
                $estate_action = get_the_term_list($the_id, 'city_agency', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    echo $estate_action;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_agency_action' == $column) {
            if (taxonomy_exists('action_category_agency')) {
                $estate_action = get_the_term_list($the_id, 'action_category_agency', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    echo $estate_action;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_agency_category' == $column) {
            if (taxonomy_exists('category_agency')) {
                $estate_category = get_the_term_list($the_id, 'category_agency', '', ', ', '');
                if (!is_wp_error($estate_category)) {
                    echo $estate_category;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_developer_city' == $column) {
            if (taxonomy_exists('property_city_developer')) {
                $estate_action = get_the_term_list($the_id, 'property_city_developer', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    echo $estate_action;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_developer_action' == $column) {
            if (taxonomy_exists('property_action_developer')) {
                $estate_action = get_the_term_list($the_id, 'property_action_developer', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    echo $estate_action;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_developer_category' == $column) {
            if (taxonomy_exists('property_category_developer')) {
                $estate_category = get_the_term_list($the_id, 'property_category_developer', '', ', ', '');
                if (!is_wp_error($estate_category)) {
                    echo $estate_category;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_status' == $column) {

            $post_status = get_post_status($the_id);
            $soldTermID = intval(wpresidence_get_option('wpestate_mark_sold_status', ''));
            $sold = false;
            $soldTermName = '';

            if ($soldTermID > 0 && has_term($soldTermID, 'property_status', $the_id)) {
                $soldTerm = get_term($soldTermID, 'property_status');
                if (!is_wp_error($soldTerm) && $soldTerm) {
                    $sold = true;
                    $soldTermName = $soldTerm->name;
                }
            }

            $status_map = array(
                'expired'  => esc_html__('Expired', 'wpresidence'),
                'publish'  => esc_html__('Published', 'wpresidence'),
                'disabled' => esc_html__('Disabled', 'wpresidence'),
                'draft'    => esc_html__('Draft', 'wpresidence'),
                'default'  => esc_html__('Waiting for approval', 'wpresidence')
            );

            $status = isset($status_map[$post_status]) ? $status_map[$post_status] : $status_map['default'];
            $status_class = sanitize_key(strtolower($status));
            echo '<span class="status_label ' . esc_attr($status_class) . '">' . esc_html($status) . '</span>';

            if ($sold) {
                $sold_class = sanitize_key(strtolower($soldTermName));
                echo '<span class="status_label status_label_mark_as_sold ' . esc_attr($sold_class) . '">' . esc_html($soldTermName) . '</span>';
            }

        } else if ('estate_user_date' == $column) {
            $user_id      = wpsestate_get_author($the_id);
            $estate_autor = get_the_author_meta('display_name', $user_id);
            echo esc_html__('Published by', 'wpresidence-core') . ' <a href="' . esc_url(get_edit_user_link($user_id)) . '">' . esc_html($estate_autor) . '</a><br>';

            echo esc_html(get_the_date('', $the_id));

        } else if ('estate_thumb' == $column || 'estate_agent_thumb' == $column || 'estate_agency_thumb' == $column || 'estate_developer_thumb' == $column) {
            $thumb_id = get_post_thumbnail_id($the_id);
            $post_thumbnail_url = wp_get_attachment_image_src($thumb_id, 'slider_thumb');
            if (isset($post_thumbnail_url[0])) {
                echo '<img class="wpresidence_admin_thumb" src="' .esc_url( $post_thumbnail_url[0]) . '" >';
            } else {
                echo '<img class="wpresidence_admin_thumb" src="' . esc_url(get_theme_file_uri('/img/default_listing_105.png')) . '" >';
            }
        } else if ('estate_info' == $column) {

            $returnCity = 'City: ';
            if (taxonomy_exists('property_city')) {
                $estate_city = get_the_term_list($the_id, 'property_city', '', ', ', '');
                if (!is_wp_error($estate_city)) {
                    $returnCity .= $estate_city;
                } else {
                    $returnCity .= esc_html__('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                $returnCity .= esc_html__('Taxonomy not enabled', 'wpresidence-core');
            }
            echo $returnCity;

            $returnAction = 'Action: ';
            if (taxonomy_exists('property_action_category')) {
                $estate_action = get_the_term_list($the_id, 'property_action_category', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    $returnAction .= $estate_action;
                } else {
                    $returnAction .= esc_html__('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                $returnAction .= esc_html__('Taxonomy not enabled', 'wpresidence-core');
            }
            echo '<br>' . $returnAction;
            $returnCategory = 'Category: ';
            if (taxonomy_exists('property_category')) {
                $estate_category = get_the_term_list($the_id, 'property_category', '', ', ', '');
                if (!is_wp_error($estate_category)) {
                    $returnCategory .= $estate_category;
                } else {
                    $returnCategory .= esc_html__('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                $returnCategory .= esc_html__('Taxonomy not enabled', 'wpresidence-core');
            }
            echo '<br>' . $returnCategory;
            $propertyID = get_post_meta($the_id, 'property_internal_id', true);
            echo '<br>Listing ID: ' . (!empty($propertyID) ? esc_html($propertyID) : 'N/A');
        } else if ('estate_city' == $column) {
            if (taxonomy_exists('property_city')) {
                $estate_city = get_the_term_list($the_id, 'property_city', '', ', ', '');
                if (!is_wp_error($estate_city)) {
                    echo $estate_city;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_action' == $column) {
            if (taxonomy_exists('property_action_category')) {
                $estate_action = get_the_term_list($the_id, 'property_action_category', '', ', ', '');
                if (!is_wp_error($estate_action)) {
                    echo $estate_action;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } elseif ('estate_category' == $column) {
            if (taxonomy_exists('property_category')) {
                $estate_category = get_the_term_list($the_id, 'property_category', '', ', ', '');
                if (!is_wp_error($estate_category)) {
                    echo $estate_category;
                } else {
                    esc_html_e('Taxonomy not enabled', 'wpresidence-core');
                }
            } else {
                esc_html_e('Taxonomy not enabled', 'wpresidence-core');
            }
        } else if ('estate_price' == $column) {
            $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
            $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
            $price = floatval(get_post_meta($the_id, 'property_price', true));
            $second_price = floatval(get_post_meta($the_id, 'property_second_price', true));
            if ($price != 0) {
                $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
                $price = wpestate_format_number_price($price, $th_separator);
                if ($where_currency == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }

            if ($second_price != 0) {
                $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
                $second_price = wpestate_format_number_price($second_price, $th_separator);

                if ($where_currency == 'before') {
                    $second_price = $wpestate_currency . $second_price;
                } else {
                    $second_price = $second_price . $wpestate_currency;
                }
            } else {
                $second_price = '';
            }

            echo '<div class="wpestate_admin_price_wrapper">' . get_post_meta($the_id, 'property_label_before', true) . ' ' . $price . ' ' . get_post_meta($the_id, 'property_label', true) . '</div>';

            echo '<div class="wpestate_admin_second_price_wrapper">' . get_post_meta($the_id, 'property_label_before_second_price', true) . ' ' . $second_price . ' ' . get_post_meta($the_id, 'property_second_price_label', true) . '</div>';
        } else if ('estate_featured' == $column) {
            $estate_featured = get_post_meta($the_id, 'prop_featured', true);
            if ($estate_featured == 1) {
                $estate_featured = esc_html__('Yes', 'wpresidence-core');
            } else {
                $estate_featured = esc_html__('No', 'wpresidence-core');
            }
            echo esc_html($estate_featured);
        } else if ('estate_actions' == $column) {

            echo '<div class="wpestate_admin_actions_wrapper">';
            echo wp_estate_display_action_buttons($the_id);
            echo '</div>';

        }
    }
endif;

add_filter('manage_edit-estate_property_sortable_columns', 'wpestate_sort_me');
if (!function_exists('wpestate_sort_me')):
    /**
     * Makes certain columns sortable in the admin interface
     *
     * @param array $columns Current sortable columns
     * @return array Modified array of sortable columns
     */
    function wpestate_sort_me($columns) {
        $columns['estate_featured'] = 'estate_featured';
        $columns['estate_price']    = 'estate_price';
        $columns['estate_status']   = 'estate_status';
        $columns['estate_ID']       = 'estate_ID';

        return $columns;
    }
endif;

add_filter('request', 'bs_event_date_column_orderby_core');
/**
 * Handles the sorting functionality for custom columns
 *
 * @param array $vars The query variables
 * @return array Modified query variables
 */
function bs_event_date_column_orderby_core($vars) {
    if (isset($vars['orderby']) && 'estate_featured' == $vars['orderby']) {
        $vars = array_merge($vars, array(
            'meta_key' => 'prop_featured',
            'orderby' => 'meta_value_num'
                ));
    }
    if (isset($vars['orderby']) && 'estate_price' == $vars['orderby']) {
        $vars = array_merge($vars, array(
            'meta_key' => 'property_price',
            'orderby' => 'meta_value_num'
                ));
    }

    if (isset($vars['orderby']) && 'estate_status' == $vars['orderby']) {
        $vars = array_merge($vars, array(
            'orderby' => 'post_status'
                ));
    }
    if (isset($vars['orderby']) && 'estate_ID' == $vars['orderby']) {
        $vars = array_merge($vars, array(
            'orderby' => 'ID'
                ));
    }
    return $vars;
}

add_action('admin_head-edit.php', 'wpestate_property_title_width');
if (!function_exists('wpestate_property_title_width')):
    /**
     * Adjusts admin column widths for properties.
     *
     * Widens the Title column and constrains the Featured column to 100px.
     *
     * @return void
     */
    function wpestate_property_title_width() {
        $screen = get_current_screen();
        if ('edit-estate_property' !== $screen->id) {
            return;
        }
        echo '<style>
            .wp-list-table th.column-title,
            .wp-list-table td.column-title {
                width: 30%;
            }
            .wp-list-table th.column-estate_featured,
            .wp-list-table td.column-estate_featured {
                width: 100px;
                max-width: 100px;
            }
        </style>';
    }
endif;


add_filter('manage_edit-property_city_columns', 'ST4_city_columns_head');
add_filter('manage_property_city_custom_column', 'ST4_city_columns_content_taxonomy', 10, 3);

if (!function_exists('ST4_city_columns_head')):
    /**
     * Defines columns for the property city taxonomy admin interface
     *
     * @param array $new_columns The default columns
     * @return array Modified columns array
     */
    function ST4_city_columns_head($new_columns) {
        $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => esc_html__('Name', 'wpresidence-core'),
            'county' => esc_html__('County / State', 'wpresidence-core'),
            'id' => esc_html__('ID', 'wpresidence-core'),
            'header_icon' => '',
            'slug' => esc_html__('Slug', 'wpresidence-core'),
            'posts' => esc_html__('Posts', 'wpresidence-core')
        );
        return $new_columns;
    }
endif;

if (!function_exists('ST4_city_columns_content_taxonomy')):
    /**
     * Populates content for the custom city taxonomy columns
     *
     * @param string $out The output string
     * @param string $column_name The name of the column
     * @param int $term_id The term ID
     * @return void
     */
    function ST4_city_columns_content_taxonomy($out, $column_name, $term_id) {
        if ($column_name == 'county') {
            $term_meta = get_option("taxonomy_$term_id");
            if (isset($term_meta['stateparent'])) {
                print stripslashes($term_meta['stateparent']);
            }
        }
        if ($column_name == 'id') {
            echo $term_id;
        }
    }
endif;



add_filter('manage_edit-property_area_columns', 'ST4_columns_head');
add_filter('manage_property_area_custom_column', 'ST4_columns_content_taxonomy', 10, 3);

if (!function_exists('ST4_columns_head')):
    /**
     * Defines columns for the property area taxonomy admin interface
     * Sets up the columns shown in the property area listing table
     *
     * @param array $new_columns The default WordPress columns
     * @return array Modified array of columns
     */
    function ST4_columns_head($new_columns) {
        $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => esc_html__('Name', 'wpresidence-core'),
            'city' => esc_html__('City', 'wpresidence-core'),
            'id' => esc_html__('ID', 'wpresidence-core'),
            'header_icon' => '',
            'slug' => esc_html__('Slug', 'wpresidence-core'),
            'posts' => esc_html__('Posts', 'wpresidence-core')
        );

        return $new_columns;
    }
endif;

if (!function_exists('ST4_columns_content_taxonomy')):
    /**
     * Populates content for the property area taxonomy columns
     * Displays the parent city and term ID for each property area
     *
     * @param string $out The output string
     * @param string $column_name The name of the column
     * @param int $term_id The term ID
     * @return void
     */
    function ST4_columns_content_taxonomy($out, $column_name, $term_id) {
        if ($column_name == 'city') {
            $term_meta = get_option("taxonomy_$term_id");
            if (isset($term_meta['cityparent'])) {
                print stripslashes($term_meta['cityparent']);
            }
        }
        if ($column_name == 'id') {
            echo $term_id;
        }
    }
endif;


/**
 * Add ID Column to WordPress Admin Tables
 *
 * This set of filters and actions adds an ID column to various admin tables in WordPress,
 * including posts, pages, media, categories, and custom WP Estate taxonomies.
 * Displaying IDs in admin tables makes it easier for administrators to reference specific items
 * when working with templates, shortcodes, or custom code.
 *
 * @package WP Estate
 * @subpackage Admin UI
 */

// Add ID column to standard WordPress post types
add_filter('manage_posts_columns', 'wpestate_add_id_column', 5);
add_action('manage_posts_custom_column', 'wpestate_id_column_content', 5, 2);
add_filter('manage_pages_columns', 'wpestate_add_id_column', 5);
add_action('manage_pages_custom_column', 'wpestate_id_column_content', 5, 2);
add_filter('manage_media_columns', 'wpestate_add_id_column', 5);
add_action('manage_media_custom_column', 'wpestate_id_column_content', 5, 2);

// Add ID column to standard WordPress categories
add_action('manage_edit-category_columns', 'wpestate_add_id_column', 5);
add_filter('manage_category_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);

// Add ID column to agent-related taxonomies
add_action('manage_edit-property_category_agent_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_category_agent_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);
add_action('manage_edit-property_action_category_agent_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_action_category_agent_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);
add_action('manage_edit-property_city_agent_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_city_agent_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);
add_action('manage_edit-property_area_agent_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_area_agent_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);
add_action('manage_edit-property_county_state_agent_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_county_state_agent_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);

// Add ID column to property-related taxonomies
add_action('manage_edit-property_category_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_category_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);
add_action('manage_edit-property_action_category_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_action_category_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);
add_action('manage_edit-property_city_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_city_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);
add_action('manage_edit-property_county_state_columns', 'wpestate_add_id_column', 5);
add_filter('manage_property_county_state_custom_column', 'wpestate_categoriesColumnsRow', 10, 3);

/**
 * Add ID Column to Admin Tables
 * 
 * This function adds a new column called 'ID' to WordPress admin tables.
 * The column is registered with the internal name 'revealid_id'.
 *
 * @param array $columns Existing columns array
 * @return array Modified columns array with ID column added
 */
function wpestate_add_id_column($columns) {
   $columns['revealid_id'] = 'ID';
   return $columns;
}

/**
 * Display ID in Column for Posts, Pages, and Media
 * 
 * This function outputs the ID value in the custom column for post types.
 * It's used by the standard WordPress post types (posts, pages, media).
 *
 * @param string $column The column name
 * @param int $id The ID of the current item
 * @return void
 */
function wpestate_id_column_content($column, $id) {
    if('revealid_id' == $column) {
        print intval($id);
    }
}

/**
 * Display ID in Column for Categories and Taxonomies
 * 
 * This function returns the ID value for the custom column in taxonomy tables.
 * It's used by categories and custom taxonomies to display their term IDs.
 *
 * @param string $argument Default output for the column
 * @param string $columnName The name of the column
 * @param int $categoryID The ID of the current taxonomy term
 * @return int|string The category ID if in the ID column, otherwise original argument
 */
function wpestate_categoriesColumnsRow($argument, $columnName, $categoryID) {
    if($columnName == 'revealid_id') {
        return $categoryID;
    }
}