<?php
/**
 * Centralized HTML renderers for dropdown components.
 *
 * This file stores the canonical dropdown HTML builders used by
 * theme search forms, filters, and widget controls.
 *
 * @package WPResidence
 */
if (!function_exists('wpestate_build_dropdown_adv_new')):
    /**
      * Build advanced-search dropdown markup with data-search metadata.
      *
      * This renderer is used by advanced search templates for taxonomy and
      * post-meta driven dropdown fields. It also attaches `data-search-*`
      * attributes used by AJAX collection on the frontend.
      *
      * @param string $appendix    Prefix for CSS classes and IDs (for example '', 'sidebar-', 'shortcode-', 'mobile-', 'half-').
      * @param string $ul_id       Dropdown list `<ul>` id.
      * @param string $toogle_id   Dropdown toggle button id.
      * @param string $values      Default/current label text.
      * @param string $values1     Default/current machine value.
      * @param string $get_var     Field key used for request value and mapping.
      * @param string $select_list Prebuilt `<li>` list markup.
      * @param string $active      Active state class marker.
      * @param string $search_compare_override Optional explicit compare operator (for example equal, smaller, greater).
      *
      * @return string Dropdown HTML markup.
      */
    function wpestate_build_dropdown_adv_new($appendix, $ul_id, $toogle_id, $values, $values1, $get_var, $select_list, $active = '', $search_compare_override = '') {

        // Step 1: Initialize local render flags and data-search defaults.
        $extraclass     = '';
        $wrapper_class  = '';
        $return_string  = '';
        $is_half        = 0;
        $search_compare   = 'equal';
        $search_taxonomy  = '';
        $search_post_meta = '';

        // Step 1.1: Resolve compare operator.
        // Priority:
        // 1) explicit override provided by caller,
        // 2) field match inside Theme Options "Advanced Search How",
        // 3) fallback to "equal".
        if ( is_string( $search_compare_override ) && trim( $search_compare_override ) !== '' ) {
            $search_compare = trim( $search_compare_override );
        } else {
            $field_compare_key = '';
            if ( is_string( $get_var ) ) {
                $field_compare_key = trim( $get_var );
            }
            if ( function_exists( 'wpestate_map_adv_search_field_to_meta_key' ) ) {
                $field_compare_key = wpestate_map_adv_search_field_to_meta_key( $field_compare_key );
            }

            $adv_search_what = wpresidence_get_option( 'wp_estate_adv_search_what', '' );
            $adv_search_how  = wpresidence_get_option( 'wp_estate_adv_search_how', '' );

            if ( $field_compare_key !== '' && is_array( $adv_search_what ) && is_array( $adv_search_how ) ) {
                foreach ( $adv_search_what as $field_index => $field_label ) {
                    if ( ! is_string( $field_label ) || trim( $field_label ) === '' ) {
                        continue;
                    }

                    $mapped_field_label = trim( $field_label );
                    if ( function_exists( 'wpestate_map_adv_search_field_to_meta_key' ) ) {
                        $mapped_field_label = wpestate_map_adv_search_field_to_meta_key( $mapped_field_label );
                    }

                    if ( $mapped_field_label === $field_compare_key ) {
                        if ( isset( $adv_search_how[ $field_index ] ) && is_string( $adv_search_how[ $field_index ] ) && trim( $adv_search_how[ $field_index ] ) !== '' ) {
                            $search_compare = trim( $adv_search_how[ $field_index ] );
                        }
                        break;
                    }
                }
            }
        }

        // Step 2: Map dropdown keys to taxonomy/post-meta fields for AJAX extraction.
        $taxonomy_map = array(
            'property_category'   => 'property_category',
            'property_action_category' => 'property_action_category',
            'property_city'        => 'property_city',
            'property_area'        => 'property_area',
            'property_county_state' => 'property_county_state',
            'property_status'      => 'property_status',
        );
        $post_meta_map = array(
            'advanced_country'   => 'property_country',
            'rooms'              => 'property_rooms',
            'bedrooms'           => 'property_bedrooms',
            'bathrooms'          => 'property_bathrooms',
            'property_rooms'     => 'property_rooms',
            'property_bedrooms'  => 'property_bedrooms',
            'property_bathrooms' => 'property_bathrooms',
        );

        // Step 3: Resolve current field as taxonomy or post-meta.
        if ( isset( $taxonomy_map[ $get_var ] ) ) {
            $search_taxonomy = $taxonomy_map[ $get_var ];
        } elseif ( isset( $post_meta_map[ $get_var ] ) ) {
            $search_post_meta = $post_meta_map[ $get_var ];
        } elseif ( is_string( $get_var ) && trim( $get_var ) !== '' ) {
            // For custom fields, treat get_var as the exact post-meta key.
            $search_post_meta = trim( $get_var );
        }

        // Step 4: Determine extra wrapper/button classes by rendering context.
        switch ( $appendix ) {
            case '':
                $extraclass = ' filter_menu_trigger  ';
                break;
            case 'sidebar-':
                $extraclass = ' sidebar_filter_menu  ';
                break;
            case 'shortcode-':
                $extraclass    = ' filter_menu_trigger  ';
                $wrapper_class = 'listing_filter_select';
                break;
            case 'mobile-':
                $extraclass = ' filter_menu_trigger  ';
                break;
            case 'half-':
                $extraclass    = ' filter_menu_trigger  ';
                $return_string = '<div class="col-md-3">';
                $appendix      = '';
                $is_half       = 1;
                break;
        }

        // Step 5: Adjust wrapper output for advanced search type 6.
        $adv_search_type = wpresidence_get_option( 'wp_estate_adv_search_type', '' );
        if ( $adv_search_type == 6 ) {
            $return_string = '';
        }

        // Step 6: Resolve current selected value and display label.
        $selected_term_id = 0;
        $is_array_field   = ( $get_var == 'property_category' || $get_var == 'property_action_category' );

        // Sanitize get_var for non-array fields
        if ( ! $is_array_field ) {
            $get_var = sanitize_key( $get_var );
        }

        // Taxonomy dropdown flag — used for button text rendering (Step 8)
        $is_taxonomy_dropdown = $is_array_field
            || $get_var == 'property_city'
            || $get_var == 'property_area'
            || $get_var == 'property_county_state';

        // Extract raw GET value — array fields use [0], scalar fields use direct value
        $raw_get_value = '';
        if ( $is_array_field && isset( $_GET[ $get_var ][0] ) ) {
            $raw_get_value = trim( $_GET[ $get_var ][0] );
        } elseif ( ! $is_array_field && isset( $_GET[ $get_var ] ) ) {
            $raw_get_value = trim( $_GET[ $get_var ] );
        }

        if ( $raw_get_value !== '' && $active === 'active' ) {
            // Sanitize the value — array fields skip sanitize_text_field
            $getval = $is_array_field
                ? ucwords( esc_html( $raw_get_value ) )
                : ucwords( esc_html( sanitize_text_field( $raw_get_value ) ) );

            // Resolve term name and ID from slug
            $term_data             = wpestate_return_title_from_slug( $get_var, $getval );
            $selected_term_id      = $term_data['term_id'];
            $button_label          = $term_data['name'];
            $button_data_value     = $term_data['name'];
            $selected_slug_display = str_replace( '-', ' ', $getval );
        } else {
            $selected_slug_display = $values;
            $button_label          = $values;
            $button_data_value     = $values1;
        }

        // Step 7: Build shared data-search attributes injected on button/input/ul.
        $data_search_attrs = 'data-term-id-selected="' . intval( $selected_term_id ) . '"'
            . ' data-search-taxonomy="'  . esc_attr( $search_taxonomy )  . '"'
            . ' data-search-compare="'   . esc_attr( $search_compare )   . '"'
            . ' data-search-post-meta="' . esc_attr( $search_post_meta ) . '"';

        // Step 8: Render button text — taxonomy fields show term name, custom fields apply WPML.
        if ( $is_taxonomy_dropdown ) {
            $button_text = ( $button_label == 'All' ) ? $values : $button_label;
        } else {
            if ( function_exists( 'icl_translate' ) ) {
                $button_label = apply_filters( 'wpml_translate_single_string', trim( $button_label ), 'custom field value', 'custom_field_value' . $button_label );
            }
            $button_text = ( $button_label == 'all' || $button_label == 'All' ) ? $values : $button_label;
        }

        // Step 9: Build hidden input name from taxonomy/post-meta mapping.
        // Priority:
        // 1) taxonomy field name,
        // 2) post-meta field name,
        // 3) fallback to legacy get_var key.
        $field_name_base = '';
        if ( $search_taxonomy !== '' ) {
            $field_name_base = $search_taxonomy;
        } elseif ( $search_post_meta !== '' ) {
            $field_name_base = $search_post_meta;
        } else {
            $field_name_base = trim( (string) $get_var );
        }
        $input_name  = $is_array_field ? $field_name_base . '[]' : $field_name_base;
        $input_value = '';
        if ( $is_array_field && isset( $_GET[ $get_var ][0] ) ) {
            $input_value = strtolower( esc_attr( $_GET[ $get_var ][0] ) );
        } elseif ( ! $is_array_field && isset( $_GET[ $field_name_base ] ) ) {
            $input_value = strtolower( esc_attr( $_GET[ $field_name_base ] ) );
        } elseif ( ! $is_array_field && isset( $_GET[ $get_var ] ) ) {
            $input_value = strtolower( esc_attr( $_GET[ $get_var ] ) );
        }

        // Step 10: Assemble final dropdown HTML.
        $return_string .= '
        <div class="dropdown ' . $active . ' bbrb-wpestate_build_dropdown_adv_new ' . $button_label . ' wpresidence_dropdown ' . $wrapper_class . '">
            <button data-toggle="dropdown" id="' . sanitize_key( $appendix . $toogle_id ) . '"
                class="btn dropdown-toggle ' . $extraclass . '"
                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                data-value="' . esc_attr( $button_data_value ) . '" ' . $data_search_attrs . '>'
                . $button_text .
            '</button>
            <input type="hidden" name="' . $input_name . '" ' . $data_search_attrs . ' value="' . $input_value . '">
            <ul id="' . $appendix . $ul_id . '" class="dropdown-menu filter_menu" role="menu" aria-labelledby="' . $appendix . $toogle_id . '" ' . $data_search_attrs . '>
                ' . $select_list . '
            </ul>
        </div>';

        // Step 11: Close half-width wrapper when applicable.
        if ( $is_half == 1 && $adv_search_type != 6 ) {
            $return_string .= '</div>';
        }

        return $return_string;
    }
endif;
















if (!function_exists('wpestate_build_dropdown_multiple')):
    /**
     * Build multi-select dropdown markup for advanced search.
     *
     * @param string $appendix    Prefix for IDs/layout context.
     * @param string $ul_id       Legacy parameter kept for compatibility.
     * @param string $toogle_id   Toggle id used for select id.
     * @param string $values      Title/label shown by selectpicker.
     * @param string $values1     Legacy parameter kept for compatibility.
     * @param string $get_var     Field key used for input name.
     * @param string $select_list Prebuilt `<option>` markup.
     * @param string $active      Legacy parameter kept for compatibility.
     *
     * @return string Select HTML markup.
     */
    function wpestate_build_dropdown_multiple($appendix, $ul_id, $toogle_id, $values, $values1, $get_var, $select_list, $active = '') {
        // Step 1: Initialize output and layout flags.
        $return_string = '';
        $is_half = 0;

        // Step 2: Apply context-based layout behavior.
        switch ($appendix) {
            case 'half-':
                $appendix = '';
                $is_half = 1;
                $return_string = '<div class="col-md-3">';
                break;
        }

        // Step 3: Resolve field key and metadata attributes used by AJAX search collectors.
        $get_var_sanitized = sanitize_key($get_var);
        $search_compare = 'equal';
        $search_taxonomy = '';
        $search_post_meta = '';
        $taxonomy_map = array(
            'property_category'   => 'property_category',
            'property_action_category' => 'property_action_category',
            'property_city'        => 'property_city',
            'property_area'        => 'property_area',
            'property_county_state' => 'property_county_state',
            'property_status'      => 'property_status'
        );
        $post_meta_map = array(
            'advanced_country'   => 'property_country',
            'rooms'              => 'property_rooms',
            'bedrooms'           => 'property_bedrooms',
            'bathrooms'          => 'property_bathrooms',
            'property_rooms'     => 'property_rooms',
            'property_bedrooms'  => 'property_bedrooms',
            'property_bathrooms' => 'property_bathrooms',
        );
        if (isset($taxonomy_map[$get_var_sanitized])) {
            $search_taxonomy = $taxonomy_map[$get_var_sanitized];
        } elseif (isset($post_meta_map[$get_var_sanitized])) {
            $search_post_meta = $post_meta_map[$get_var_sanitized];
        } elseif (is_string($get_var) && trim($get_var) !== '') {
            // For custom fields, treat get_var as the exact post-meta key.
            $search_post_meta = trim($get_var);
        }
        $data_search_attrs = 'data-term-id-selected="" data-search-taxonomy="' . esc_attr($search_taxonomy) . '" data-search-compare="' . esc_attr($search_compare) . '" data-search-post-meta="' . esc_attr($search_post_meta) . '"';

        // Step 3.1: Resolve form field name from taxonomy/post-meta mapping.
        // Priority:
        // 1) taxonomy field name,
        // 2) post-meta field name,
        // 3) fallback to legacy get_var key.
        $field_name_base = trim((string) $get_var);
        if ($search_taxonomy !== '') {
            $field_name_base = $search_taxonomy;
        } elseif ($search_post_meta !== '') {
            $field_name_base = $search_post_meta;
        } elseif ($field_name_base === '') {
            $field_name_base = $get_var_sanitized;
        }

        // Step 5: Handle search type specific output behavior.
        $adv_search_type = wpresidence_get_option('wp_estate_adv_search_type', '');
        if ($adv_search_type == 6) {
            $return_string = '';
        }

        // Step 6: Enable optional live search for selectpicker.
        $live_search = '';
        if ('yes' === wpresidence_get_option('wp_estate_select_list_multiple_show_search', '')) {
            $live_search = 'data-live-search="true" ';
        }

        // Step 7: Render multi-select markup with search metadata attributes.
        $return_string .= '
        <select class="form-control  wpestatemultiselectselectpicker" multiple xx3
            name="' . esc_attr($field_name_base) . '[]"
            id="' . esc_attr($appendix . $toogle_id) . '"
            title="' . esc_attr($values) . '"
            ' . esc_html($live_search) . '
            ' . $data_search_attrs . '
            data-selected-text-format="count"
            data-count-Selected-Text="{0} ' . esc_html__('items selected', 'wpresidence') . '"
            data-select-all-text="' . esc_html__('Select All', 'wpresidence') . '"
            data-deselect-all-text="' . esc_html__('Select None', 'wpresidence') . '"
            data-actions-box="true"
            aria-labelledby="' . esc_attr($appendix . $toogle_id) . '">
            ' . $select_list . '
        </select>';

        if ($is_half == 1 && $adv_search_type != 6) {
            $return_string .= '</div>';
        }

        return $return_string;
    }
endif;

if (!function_exists('wpestate_build_dropdown_for_filters')):
    /**
     * Builds a dropdown for listing/order filters.
     *
     * @param string $dropdown_id    Unique identifier for the dropdown.
     * @param string $selected_value Currently selected value.
     * @param string $label          Visible text label for button.
     * @param string $select_list    Pre-formatted `<li>` options markup.
     *
     * @return string Dropdown HTML markup.
     */
    function wpestate_build_dropdown_for_filters($dropdown_id, $selected_value, $label, $select_list) {
        // Step 1: Map known filter dropdown IDs to their taxonomy names.
        $taxonomy_map = array(
            'a_filter_action'       => 'property_action_category',
            'sidebar-adv_actions'   => 'property_action_category',
            'a_filter_categ'        => 'property_category',
            'sidebar-adv_categ'     => 'property_category',
            'a_filter_county'       => 'property_county_state',
            'sidebar-adv_conty'     => 'property_county_state',
            'a_filter_cities'       => 'property_city',
            'sidebar-property_city' => 'property_city',
            'a_filter_areas'        => 'property_area',
            'sidebar-adv_area'      => 'property_area',
            'a_filter_status'       => 'property_status',
            'a_filter_features'     => 'property_features'
        );

        // Step 2: Resolve taxonomy for this dropdown ID (empty when not taxonomy-based).
        $search_taxonomy = '';
        if (isset($taxonomy_map[$dropdown_id])) {
            $search_taxonomy = $taxonomy_map[$dropdown_id];
        }

        // Step 3: Resolve the selected term id for initial render metadata.
        $selected_term_id = '';
        if ($search_taxonomy !== '') {
            $selected_value_string = trim((string) $selected_value);
            $selected_value_slug = sanitize_title(rawurldecode($selected_value_string));

            if ($selected_value_slug !== '' && strtolower($selected_value_slug) !== 'all') {
                if (ctype_digit($selected_value_string)) {
                    $selected_term_id = (string) intval($selected_value_string);
                } else {
                    $term = get_term_by('slug', $selected_value_slug, $search_taxonomy);
                    if ($term && !is_wp_error($term)) {
                        $selected_term_id = (string) intval($term->term_id);
                    }
                }
            }
        }

        // Step 4: Build the shared metadata attributes.
        $data_search_attrs = 'data-term-id-selected="' . esc_attr($selected_term_id) . '" data-search-taxonomy="' . esc_attr($search_taxonomy) . '"';

        // Step 5: Render filter dropdown with metadata attributes on button and list.
        $return_string = '
        <div class="dropdown listing_filter_select wpresidence_dropdown wpresidence_filters_dropdown wpresidence_wrap_' . esc_attr($dropdown_id) . '">
            <button data-toggle="dropdown" id="' . esc_attr($dropdown_id) . '"
                class="btn dropdown-toggle filter_menu_trigger"
                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                data-value="' . esc_attr($selected_value) . '"
                ' . $data_search_attrs . '>
               ' . esc_html($label) . '
            </button>
            <ul class="dropdown-menu filter_menu" role="menu" aria-labelledby="' . esc_attr($dropdown_id) . '" ' . $data_search_attrs . '>
                ' . trim($select_list) . '
            </ul>
        </div>';

        return $return_string;
    }
endif;

if (!function_exists('wpresidence_render_single_dropdown')):
    /**
     * Render a generic single dropdown.
     *
     * @param string $wrapper_class     Additional wrapper class.
     * @param string $button_id         Toggle button id.
     * @param string $dropdown_label    Visible dropdown label.
     * @param string $initial_value     Initial hidden/select value.
     * @param string $display_style     Optional style attribute content.
     * @param string $hidden_input_name Hidden input name.
     * @param string $ul_list_class     Dropdown list class suffix.
     * @param string $dropdown_options  Dropdown `<li>` options markup.
     */
    function wpresidence_render_single_dropdown($wrapper_class, $button_id, $dropdown_label, $initial_value, $display_style, $hidden_input_name, $ul_list_class, $dropdown_options) {
        ?>
        <div class="dropdown wpresidence_dropdown  <?php echo esc_attr($wrapper_class); ?>" <?php echo $display_style; ?>>
            <button  data-bs-toggle="dropdown" id="<?php echo esc_attr($button_id); ?>"
                class="btn dropdown-toggle"
                type="button"
                aria-expanded="false"
                data-value="<?php echo esc_attr(strtolower(rawurlencode($initial_value))); ?>">
                <?php echo wp_kses_post($dropdown_label); ?>
            </button>
            <input type="hidden" name="<?php echo esc_attr($hidden_input_name); ?>" value="<?php echo esc_attr(strtolower(rawurlencode($initial_value))); ?>">
            <ul  class="dropdown-menu filter_menu <?php echo ($ul_list_class); ?> " role="menu" aria-labelledby="<?php echo esc_attr($button_id); ?>">
                <?php echo $dropdown_options; ?>
            </ul>
        </div>
        <?php
    }
endif;
