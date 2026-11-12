<?php

/**
 * Display the billing period in a human-readable format.
 *
 * @param string $biling_period The billing period to format.
 * @return string The formatted billing period.
 */

if(!function_exists('wpestate_show_bill_period')):
    function wpestate_show_bill_period($biling_period) {
        $periods = array(
            'Day' => esc_html__('days', 'wpresidence'),
            'Days' => esc_html__('days', 'wpresidence'),
            'Week' => esc_html__('weeks', 'wpresidence'),
            'Weeks' => esc_html__('weeks', 'wpresidence'),
            'Month' => esc_html__('months', 'wpresidence'),
            'Months' => esc_html__('months', 'wpresidence'),
            'Year' => esc_html__('year', 'wpresidence'),
            'Years' => esc_html__('years', 'wpresidence')
        );

        return isset($periods[$biling_period]) ? $periods[$biling_period] : $biling_period;
    }
endif;


/**
 * Check User Permission on Dashboard
 * 
 * Verifies if the current user has permission to access specific dashboard pages.
 * Access is granted if any of these conditions are met:
 * - User is an administrator
 * - User has an estate role > 1
 * - No specific permissions are set
 * - The requested page is user_dashboard_profile
 * - The page is in the allowed permissions array
 *
 * @since 4.7.0
 * 
 * @param string $current_page Optional. The page template to check permissions against.
 *                            If empty, the current page template will be used.
 *                            Template path can be with or without 'page-templates/' prefix.
 * 
 * @return boolean True if user has permission, false otherwise. 
 */

if (!function_exists('wpestate_check_user_permission_on_dashboard')):
    /**
     * Check User Permission On Dashboard.
     *
     * @param mixed $current_page
     * @return mixed
     */
    function wpestate_check_user_permission_on_dashboard($current_page = '') {
        // Check if user is logged in
        $current_user = wp_get_current_user();
        if (!$current_user->ID) {
            return false;
        }

        // Administrator has full access
        if (current_user_can('administrator')) {
            return true;
        }

        if(  current_user_can('wpresidence_agent_role') || 
             current_user_can('wpresidence_developer_role') ||
             current_user_can('wpresidence_agency_role'))
                {
            return true;
        }


        // Get permissions array
        $permissions = wpresidence_get_option('wp_estate_user_page_permission', '');
        
        // If no permissions are set, allow access
        if (empty($permissions)) {
            return true;
        }

        // Get current page template if not provided
        if (empty($current_page)) {
            global $post;
            $current_page = '';
            if (isset($post->ID)) {
                $current_page = get_post_meta($post->ID, '_wp_page_template', true);
            }
        }

        // Clean up the page template path
        $current_page = str_replace(['.php', 'page-templates/'], '', $current_page);

        // Profile page is always accessible
        if ($current_page === 'user_dashboard_profile') {
            return true;
        }

        // Clean up permissions array and check access
        $cleaned_permissions = array_map(function($permission) {
            return str_replace('page-templates/', '', $permission);
        }, $permissions);

        return in_array($current_page, $cleaned_permissions, true);
    }
endif;


add_action('delete_post', 'wpestate_delete_history');

/**
 * Delete History.
 *
 * @param mixed $postId
 * @return mixed
 */
function wpestate_delete_history($postId) {
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $recording_types = array(
        'estate_property',
        'estate_agent',
        'estate_agency',
        'estate_developer',
        'wpestate_invoice',
        'wpestate_message',
        'wpestate_search',
    );

    $post_type = get_post_type($postId);
    $history_array = array();

    $history_array = get_user_meta($userID, 'wpestate_delete_history', true);
    if ($history_array == '') {
        $history_array = array();
    }

    if (in_array($post_type, $recording_types)) {
        $current_unix_timestamp = time();
        if (is_array($history_array)) {
            foreach ($history_array as $key => $item) {
                if (($key + 60 * 60 * 24 * 7) <= $current_unix_timestamp) {
                    unset($history_array[$key]);
                }
            }
        }

        $entry_date_label = date_i18n('F j, Y, g:i a', $current_unix_timestamp);
        $history_array[$current_unix_timestamp] = array(
            'date' => $entry_date_label,
            'label' => wpestate_compose_history_entry($postId, 'delete')
        );
        update_user_meta($userID, 'wpestate_delete_history', $history_array);
    }
}

/*
 * return dashboard widget history
 *
 *
 *
 *
 */

if (!function_exists('wpestate_dashboard_widget_history')):

    /**
     * Dashboard Widget History.
     *
     * @param mixed $agent_list
     * @return mixed
     */
    function wpestate_dashboard_widget_history($agent_list) {
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $history_array = get_user_meta($userID, 'wpestate_delete_history', true);
        if ($history_array == '') {
            $history_array = array();
        }
        $agent_list = array_filter($agent_list);

        $args = array(
            'post_type' => array(
                'estate_property',
                'estate_agent',
                'estate_agency',
                'estate_developer',
                'wpestate_invoice',
                'wpestate_message',
                'wpestate_search',
            ),
            'author__in' => $agent_list,
            'paged' => 1,
            'posts_per_page' => 40,
            'orderby' => array(
                'modified' => 'DESC',
                'date' => 'DESC'
            ),
            'order' => 'desc',
            'post_status' => array('any'),
            'date_query' => array(
                array(
                    'column' => 'post_modified_gmt',
                    'after' => '1 week ago',
                ),
            ),
        );
        //post_date
        $prop_selection = new WP_Query($args);
        while ($prop_selection->have_posts()): $prop_selection->the_post();
            $item_id = get_the_ID();
            $publish_date = get_post_timestamp($item_id, 'date');
            $modified_date = get_post_timestamp($item_id, 'modified');

            $entry_date = $publish_date;
            $date_format  = get_option('date_format');
            $date_format  = $date_format.' g:i a';

            $entry_date_label = get_the_date($date_format);

            $action = 'add';
            if ($modified_date > $publish_date) {
                $entry_date = $modified_date;
                $entry_date_label = get_the_modified_date($date_format);
                $action = 'edit';
            }

            $history_array[$entry_date] = array(
                'date' => $entry_date_label,
                'label' => wpestate_compose_history_entry($item_id, $action)
            );
        endwhile;
        wp_reset_query();
        wp_reset_postdata();


        krsort($history_array, SORT_NUMERIC);
        array_slice($history_array, 0, 21);


        $return = '<div class="col-md-12 col-lg-12 wpestate_widget_flex"><div class="wpestate_dashboard_content_wrapper wpestate_widget_wrapper">';
        $return .= '<h3>' . esc_html__('Account History (last 7 days)', 'wpresidence') . '</h3>';
        $return .= '<div class="dashboard_history_wrapper">';
        foreach ($history_array as $key => $entry) {
            $return .= '<div class="wpestate_dash_history_unit">';
            $return .= '<div class="wpestat_dash_history_date">' . $entry['date'] . '</div>';
            $return .= '<div class="wpestat_dash_history_label">' . $entry['label'] . '</div>';
            $return .= '</div>';
        }

        $return .= '</div></div></div>';


        return $return;
    }

endif;

/**
 * Compose History Entry.
 *
 * @param mixed $item_id
 * @param mixed $action
 * @return mixed
 */
function wpestate_compose_history_entry($item_id, $action = 'add') {
    $post_type = get_post_type($item_id);
    $return = '';

    $action_string = esc_html__('Edited', 'wpresidence');
    if ($action == 'add') {
        $action_string = esc_html__('Added', 'wpresidence');
    } else if ($action == 'delete') {
        $action_string = esc_html__('Deleted', 'wpresidence');
    }

    switch ($post_type) {
        case 'estate_property':
            $return = sprintf(esc_html__('%s property %s', 'wpresidence'), $action_string, get_the_title($item_id));
            break;
        case 'estate_agent':
            $return = sprintf(esc_html__('%s agent %s', 'wpresidence'), $action_string, get_the_title($item_id));
            break;
        case 'estate_agency':
            $return = sprintf(esc_html__('%s agency %s', 'wpresidence'), $action_string, get_the_title($item_id));
            break;
        case 'estate_agency':
            $return = sprintf(esc_html__('%s developer %s', 'wpresidence'), $action_string, get_the_title($item_id));
            break;
        case 'wpestate_invoice':
            $return = sprintf(esc_html__('Generated Invoice %s', 'wpresidence'), get_the_title($item_id));
            if ($action == 'delete') {
                $return = sprintf(esc_html__('Deleted Invoice %s', 'wpresidence'), get_the_title($item_id));
            }
            break;
        case 'wpestate_message':
            $return = sprintf(esc_html__('Write Message  %s', 'wpresidence'), get_the_title($item_id));
            if ($action == 'delete') {
                $return = sprintf(esc_html__('Deleted Message  %s', 'wpresidence'), get_the_title($item_id));
            }
            break;
        case 'wpestate_search':
            $return = sprintf(esc_html__('Saved Search  %s', 'wpresidence'), get_the_title($item_id));
            if ($action == 'delete') {
                $return = sprintf(esc_html__('Deleted Search  %s', 'wpresidence'), get_the_title($item_id));
            }
            break;
    }


    return $return;
}

/*
 * return dashboard widget to 10
 *
 *
 *
 *
 */
if (!function_exists('wpestate_dashboard_widget_top_ten_contacted')):

    /**
     * Dashboard Widget Top Ten Contacted.
     *
     * @param mixed $agent_list
     * @return mixed
     */
    function wpestate_dashboard_widget_top_ten_contacted($agent_list) {

        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $template = get_transient('wpestate_dashboard_widget_top_ten_contacted_' . $userID);
        $agent_list = array_filter($agent_list);

        if ($template === false) {
            $args = array(
                'post_type' => 'estate_property',
                'author__in' => $agent_list,
                'paged' => 1,
                'posts_per_page' => 5,
                'orderby' => 'meta_value_num',
                'meta_key' => 'wpestate_total_contact',
                'order' => 'desc',
                'post_status' => array('any')
            );
            $prop_selection = new WP_Query($args);
            ob_start();

            if ($prop_selection->have_posts()) {
                while ($prop_selection->have_posts()): $prop_selection->the_post();
                    $action_status = get_post_meta(get_the_ID(), 'wpestate_total_contact', true) . ' ' . esc_html__('Inquiries', 'wpresidence');
                    include( locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard_unit_widget.php'));
                endwhile;
            }else {
                print '<div class="wpestate_dashboard_no_listings">' . esc_html__('You don\'t have any listings or enough data!', 'wpresidence') . '</div>';
            }
            $template = ob_get_contents();
            ob_end_clean();
            wp_reset_query();
            wp_reset_postdata();
            set_transient('wpestate_dashboard_widget_top_ten_contacted_' . $userID, $template, 60 * 60 * 24);
        }

        $return = '<div class="col-12 col-md-12 col-lg-6 wpestate_widget_flex"><div class="wpestate_dashboard_content_wrapper wpestate_widget_wrapper">';
        $return .= '<h3>' . esc_html__('Your most Popular Listings', 'wpresidence') . '</h3>';
        $return .= $template;
        $return .= '</div></div>';

        return $return;
    }

endif;




/*
 * return dashboard widget - global details
 *
 *
 *
 *
 */
if (!function_exists('wpestate_dashboard_account_summary')):

    /**
     * Dashboard Account Summary.
     *
     * @param mixed $userID
     * @param mixed $agent_list
     * @return mixed
     */
    function wpestate_dashboard_account_summary($userID, $agent_list) {



        $details = '';

        if(!function_exists('is_wpresidence_developer_or_agency')){
            return '';
        }


        $details .= '<div class="dasboard_widget_row">' . esc_html__('Total Properties', 'wpresidence') . ': ' . wpestate_count_user_posts_by_status_query($agent_list) . '</div>';
        $details .= '<div class="dasboard_widget_row">' . esc_html__('Published Properties', 'wpresidence') . ': ' . count_user_posts($userID, 'estate_property', true) . '</div>';



        if (is_wpresidence_developer_or_agency() ) {
            $details .= '<div class="dasboard_widget_row">' . esc_html__('Total Agents', 'wpresidence') . ': ' . count_user_posts($userID, 'estate_agent', false) . '</div>';
        }

        $details .= '<div class="dasboard_widget_row">' . esc_html__('Saved Searches', 'wpresidence') . ': ' . wpestate_count_user_posts_by_status('draft', 'wpestate_search', $userID) . '</div>';

        $curent_fav = wpestate_return_favorite_listings_per_user();
        if (is_array($curent_fav)) {
            $curent_fav_no = count($curent_fav);

            if( isset($curent_fav[0]) && $curent_fav[0]=='null' ){
                $curent_fav_no=0;
            }
        } else {
            $curent_fav_no = intval($curent_fav);
        }
        $details .= '<div class="dasboard_widget_row">' . esc_html__('Favorite Properties', 'wpresidence') . ': ' . intval($curent_fav_no) . '</div>';


        $return = '<div class="col-md-12 wpestate_dashboard_account_summary"><div class="wpestate_dashboard_content_wrapper wpestate_widget_wrapper">';
        $return .= '<h3>' . esc_html__('Account Summary', 'wpresidence') . '</h3>';

        $return .= $details;
        $return .= '</div></div>';

        return $return;
    }

endif;

/**
 * Count all estate_property posts for a list of agents, any status.
 *
 * Uses the 2026 query layer with IDs-only mode for minimal memory.
 *
 * @param array $agent_list  Array of user/agent IDs.
 * @return int               Total post count across all statuses.
 */
function wpestate_count_user_posts_by_status_query( $agent_list ) {
    // Step 1: build params — IDs-only, no pagination, skip row count.
    $params = array(
        'author__in'    => $agent_list,
        'nopaging'      => true,
        'fields'        => 'ids',
        'post_status'   => 'any',
        'no_found_rows' => true,
    );

    // Step 2: execute via 2026 query layer.
    $result = wpestate_query_2026_property( 'dashboard', $params );

    // Step 3: clean up global post data and return the count.
    wp_reset_postdata();
    return $result['query']->post_count;
}

/**
 * Count User Posts By Status.
 *
 * @param mixed $post_status
 * @param mixed $post_type
 * @param mixed $user_id
 * @return mixed
 */
function wpestate_count_user_posts_by_status($post_status = 'publish', $post_type = 'estate_property', $user_id = 0) {
    global $wpdb;
    $count = $wpdb->get_var(
            $wpdb->prepare(
                    "
        SELECT COUNT(ID) FROM $wpdb->posts
        WHERE post_status = %s
        AND post_type = %s
        AND post_author = %d", $post_status, $post_type, $user_id
            )
    );
    return ($count) ? $count : 0;
}

/*
 * generate data for total vistis graph
 *
 *
 *
 *
 */
if (!function_exists('wpestate_display_total_visits_listings')):

    /**
     * Display Total Visits Listings.
     *
     * @return mixed
     */
    function wpestate_display_total_visits_listings() {
        $data = wpestate_total_visits_listings_initial_offload();


        $labels = array_keys($data);
        $values = array_values($data);

        $return = '<div class="col-md-12 col-lg-12 visits_per_listing wpestate_widget_flex"><div class="wpestate_dashboard_content_wrapper wpestate_widget_wrapper">';
        $return .= '<h3>' . esc_html__('Listings Views', 'wpresidence') . '</h3>';
        $return .= '<canvas id="myChart_widget_total"></canvas>';
        $return .= '</div></div>';

        $return .= '<script type="text/javascript">
        //<![CDATA[
              jQuery(document).ready(function(){
                 setTimeout(function(){
                    wpestate_chart_total_listings_widget(' . json_encode($values) . ', ' . json_encode($labels) . ', "' . esc_html__('Total views for your listings', 'wpresidence') . '" );
                 }, 200);
                });

        //]]>
        </script>';


        return $return;
    }

endif;


/**
 * Generate daily visit totals for the dashboard "Total Visits" graph.
 *
 * Builds an associative array of date => total_views for the last 30 days
 * across all properties owned by the current user and their linked agents.
 * Results are cached as a user-level transient (24 h) and persisted in
 * user meta so subsequent days only need to compute yesterday's delta.
 *
 * NOT migrated to 2026 query layer — the WP_Query here orders by
 * meta_value_num (wpestate_total_views) and iterates post meta
 * (wpestate_detailed_views) per property. This is a stats aggregation
 * pattern that doesn't benefit from the 2026 taxonomy/filter pipeline.
 *
 * @return array  Associative array keyed by formatted date string,
 *                values are integer view counts, ordered oldest-first.
 */
if (!function_exists('wpestate_total_visits_listings_initial_offload')):

    /**
     * Total Visits Listings Initial Offload.
     *
     * @return mixed
     */
    function wpestate_total_visits_listings_initial_offload() {
        // Step 1: identify the current user and build the agent list.
        $current_user   = wp_get_current_user();
        $userID         = $current_user->ID;
        $agent_list     = (array) get_user_meta($userID, 'current_agent_list', true);
        $agent_list[]   = $current_user->ID;

        // Step 2: prepare date references for today and yesterday.
        $date_format    = get_option('date_format');
        $today          = date_i18n($date_format, current_time('timestamp'));
        $yesterday      = date_i18n($date_format, strtotime('yesterday'));

        // Step 3: check for previously offloaded totals (user meta) and transient cache.
        $agent_list     = array_filter($agent_list);
        $if_offloaded   = get_user_meta($userID, 'wpestate_show_initial_offload', true);
        $totals_generarated = get_transient('wpestate_show_initial_offload_' . $userID);
        $date_format = get_option('date_format');

        // Step 4: rebuild totals if the transient has expired.
        if ($totals_generarated === false) {
            // Step 4a: seed the totals array — blank 30-day window or previous offload.
            if ($if_offloaded == '') {
                $totals = array();
                for ($i = 1; $i <= 30; $i++) {
                    $date = date_i18n($date_format, strtotime("-$i days"));
                    $totals[$date] = 0;
                }
            } else {
                $totals = $if_offloaded;
            }

            // Step 4b: query all properties for this agent list, IDs only, sorted by total views.
            $args = array(
                'post_type'         => 'estate_property',
                'author__in'        => $agent_list,
                'posts_per_page'    => -1,
                'orderby'           => 'meta_value_num',
                'meta_key'          => 'wpestate_total_views',
                'order'             => 'desc',
                'post_status'       => array('any'),
                'fields'            => 'ids',
            );
            $prop_selection = new WP_Query($args);

            // Step 4c: aggregate daily views from each property's detailed_views meta.
            foreach ($prop_selection->posts as $prop_id) {
                $detailed_views = get_post_meta($prop_id, 'wpestate_detailed_views', true);
                if ($detailed_views == '' || !is_array($detailed_views)) {
                    $detailed_views = array();
                }

                if ($if_offloaded == '') {
                    // First run: sum all days within the 30-day window.
                    foreach ($detailed_views as $key => $value) {
                        if (isset($totals[$key])) {
                            $totals[$key] = $totals[$key] + $value;
                        }
                    }
                } else {
                    // Incremental update: only add yesterday's views.
                    if( isset($totals[$yesterday]) ){
                        if (isset($detailed_views[$yesterday])) {
                            $totals[$yesterday] = $totals[$yesterday] + $detailed_views[$yesterday];
                        }
                    }else{
                        $totals[$yesterday]=0;
                    }


                }
            }

            // Step 4d: remove today (incomplete) and persist the updated totals.
            unset($totals[$today]);
            update_user_meta($userID, 'wpestate_show_initial_offload', $totals);
            set_transient('wpestate_show_initial_offload_' . $userID, $totals, 60 * 60 * 24);
        } else {
            // Step 5: transient still valid — use the offloaded snapshot.
            $totals = $if_offloaded;
        }

        // Step 6: reverse so oldest date is first (for chart rendering).
        $totals = array_reverse($totals);

        return $totals;
    }

endif;




/**
 * Render the "Your most visited Listings" dashboard widget.
 *
 * Shows up to 5 properties ordered by wpestate_total_views (descending).
 * Output is cached as a per-user transient (24 h) to avoid repeated queries.
 *
 * NOT migrated to 2026 query layer — this query orders by a specific
 * meta_value_num (wpestate_total_views) and renders full post objects
 * through a template loop. The 2026 layer's dashboard context doesn't
 * support meta-based ordering with template rendering.
 *
 * @param array $agent_list  Array of user/agent IDs whose properties to show.
 * @return string            HTML markup for the widget.
 */
if (!function_exists('wpestate_dashboard_widget_top_ten')):

    /**
     * Dashboard Widget Top Ten.
     *
     * @param mixed $agent_list
     * @return mixed
     */
    function wpestate_dashboard_widget_top_ten($agent_list) {

        // Step 1: identify current user and check for cached output.
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $template = get_transient('wpestate_dashboard_widget_top_ten_' . $userID);
        $agent_list = array_filter($agent_list);

        // Step 2: rebuild the widget HTML if the transient has expired.
        if ($template === false) {
            // Step 2a: query top 5 properties by total views, any status.
            $args = array(
                'post_type' => 'estate_property',
                'author__in' => $agent_list,
                'paged' => 1,
                'posts_per_page' => 5,
                'orderby' => 'meta_value_num',
                'meta_key' => 'wpestate_total_views',
                'order' => 'desc',
                'post_status' => array('any')
            );
            $prop_selection = new WP_Query($args);

            // Step 2b: render each property through the dashboard widget template.
            ob_start();
            if ($prop_selection->have_posts()) {
                while ($prop_selection->have_posts()): $prop_selection->the_post();
                    $action_status = get_post_meta(get_the_ID(), 'wpestate_total_views', true) . ' ' . esc_html__('Views', 'wpresidence');
                    include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard_unit_widget.php'));
                endwhile;
            }else {
                print '<div class="wpestate_dashboard_no_listings">' . esc_html__('You don\'t have any listings or enough data!', 'wpresidence') . '</div>';
            }

            // Step 2c: capture output buffer and cache for 24 hours.
            $template = ob_get_contents();
            ob_end_clean();
            wp_reset_query();
            wp_reset_postdata();
            set_transient('wpestate_dashboard_widget_top_ten_' . $userID, $template, 60 * 60 * 24);
        }

        // Step 3: wrap the cached/generated HTML in the widget container.
        $return = '<div class="col-12 col-md-12 col-lg-6 wpestate_widget_flex"><div class="wpestate_dashboard_content_wrapper wpestate_widget_wrapper">';
        $return .= '<h3>' . esc_html__('Your most visited Listings', 'wpresidence') . '</h3>';
        $return .= $template;
        $return .= '</div></div>';

        return $return;
    }

endif;




/*
 * return input data on property submit
 *
 *
 *
 *
 */

if (!function_exists('wpestate_submit_return_value')):

    /**
     * Submit Return Value.
     *
     * @param mixed $label
     * @param mixed $edit_id
     * @param mixed $extra
     * @return mixed
     */
    function wpestate_submit_return_value($label, $edit_id, $extra = '') {
        $allowed_html = array();
        $allowed_html_desc = array(
            'a' => array(
                'href' => array(),
                'title' => array()
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'ul' => array('li'),
            'li' => array(),
            'code' => array(),
            'ol' => array('li'),
            'del' => array(
                'datetime' => array()
            ),
            'blockquote' => array(),
            'ins' => array(),
        );
        $value = '';




        if (isset($_GET['listing_edit']) && is_numeric($_GET['listing_edit'])) {
            $edit_id = intval($_GET['listing_edit']);

            if ($label == 'wpestate_title') {
                $value = get_the_title($edit_id);
            } elseif ($label == 'wpestate_description') {
                $value = get_post_field('post_content', $edit_id);
            } else {
                if ($extra == 'numeric') {
                    $value = floatval(get_post_meta($edit_id, $label, true));
                } else {
                    $value = esc_html(get_post_meta($edit_id, $label, true));
                }
            }
        }

        if (isset($_POST[$label])) {
            if ($label == 'wpestate_title') {
                $value = wp_kses($_POST['wpestate_title'], $allowed_html);
            } elseif ($label == 'wpestate_description') {
                $value = wp_kses($_POST['wpestate_description'], $allowed_html_desc);
            } else {
                $value = wp_kses(esc_html($_POST[$label]), $allowed_html);
            }
        }


        return $value;
    }

endif;









/*
 * return status data for wp_query properties
 *
 *
 *
 *
 */


if (!function_exists('wpestate_duplicate_listing')):

    /**
     * Duplicate Listing.
     *
     * @param mixed $duplicate_id
     * @return mixed
     */
    function wpestate_duplicate_listing($duplicate_id) {

        //include_once( ABSPATH . 'wp-admin/includes/image.php' );
        $current_user = wp_get_current_user();
        $original_id = intval($duplicate_id);
        $paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
        $parent_userID = wpestate_check_for_agency($current_user->ID);

        if ($paid_submission_status != 'membership' || ($paid_submission_status == 'membership' || wpestate_get_current_user_listings($parent_userID) > 0)) {
            if (get_post_type($original_id) == 'estate_property') {
                $new_author = $current_user->ID;
                $post = get_post($original_id);
                if (isset($post) && $post != null) {
                    $args = array('post_type' => $post->post_type,
                        'post_status' => 'draft',
                        // Leave slug empty so WordPress can generate a unique value for the clone.
                        'post_name' => '',
                        'post_parent' => $post->post_parent,
                        'post_author' => $new_author,
                        'comment_status' => $post->comment_status,
                        'ping_status' => $post->ping_status,
                        'post_content' => $post->post_content,
                        'post_excerpt' => $post->post_excerpt,
                        'post_parent' => $post->post_parent,
                        'post_password' => $post->post_password,
                        'post_title' => esc_html__('Clone: ', 'wpresidence') . ' ' . $post->post_title,
                        'to_ping' => $post->to_ping,
                        'menu_order' => $post->menu_order
                    );
                    $post_id = wp_insert_post($args);

                    $taxonomies = get_object_taxonomies($post->post_type);
                    foreach ($taxonomies as $taxonomy) {
                        $post_terms = wp_get_object_terms($original_id, $taxonomy, array('fields' => 'slugs'));
                        wp_set_object_terms($post_id, $post_terms, $taxonomy, false);
                    }


                    // duplicate meta_key
                    $floor_plans = array(
                        'plan_title',
                        'plan_description',
                        'plan_image',
                        'plan_size',
                        'plan_rooms',
                        'plan_bath',
                        'plan_price',
                        'plan_image_attach');

                    $data = get_post_custom($original_id);
                    foreach ($data as $key => $values) :
                        if (in_array($key, $floor_plans)) {
                            $value = get_post_meta($original_id, $key, true);
                            update_post_meta($post_id, $key, $value);
                        } else {
                            foreach ($values as $value) {
                                update_post_meta($post_id, $key, $value);
                            }
                        }

                    endforeach;


         


                    // update memberhip package
                    if ($paid_submission_status == 'membership') { // update pack status
                        wpestate_update_listing_no($parent_userID);
                    }

                    //defaults
                    update_post_meta($post_id, 'sidebar_agent_option', 'global');
                    update_post_meta($post_id, 'local_pgpr_slider_type', 'global');
                    update_post_meta($post_id, 'local_pgpr_content_type', 'global');
                    // Initialize featured flag only when missing, so existing featured listings stay featured.
                    if ( ! metadata_exists( 'post', $post_id, 'prop_featured' ) ) {
                        update_post_meta($post_id, 'prop_featured', 0);
                    }
                    update_post_meta($post_id, 'pay_status', 'not paid');
                    update_post_meta($post_id, 'page_custom_zoom', 16);
                    wp_reset_query();







                    $redirect = wpestate_get_template_link('page-templates/user_dashboard.php');
                    wp_redirect($redirect);
                    exit;
                }
            }
        } else {
            // do nothing
        }
    }

endif;










/*
 *  processing floor plan data
 *
 */
if (!function_exists('wpestate_save_floor_plan_data')):

    /**
     * Save Floor Plan Data.
     *
     * @param mixed $edit_id
     * @param mixed $post_data
     * @return mixed
     */
    function wpestate_save_floor_plan_data($edit_id, $post_data) {
        if (isset($post_data['use_floor_plans'])) {
            update_post_meta($edit_id, 'use_floor_plans', intval($_POST['use_floor_plans']));
        }

        if (isset($_POST['plan_title'])) {
            update_post_meta($edit_id, 'plan_title', wpestate_sanitize_array($_POST['plan_title']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_title', '');
            }
        }

        if (isset($_POST['plan_description'])) {
            update_post_meta($edit_id, 'plan_description', wpestate_sanitize_array($_POST['plan_description']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_description', '');
            }
        }




        if (isset($_POST['floor_plan_image'])) {
            update_post_meta($edit_id, 'plan_image', wpestate_sanitize_array($_POST['floor_plan_image']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_image', '');
            }
        }

        if (isset($_POST['plan_image_attach'])) {
            update_post_meta($edit_id, 'plan_image_attach', wpestate_sanitize_array($_POST['plan_image_attach']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_image_attach', '');
            }
        }

        if (isset($_POST['plan_size'])) {
            update_post_meta($edit_id, 'plan_size', wpestate_sanitize_array($_POST['plan_size']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_size', '');
            }
        }


        if (isset($_POST['plan_rooms'])) {
            update_post_meta($edit_id, 'plan_rooms', wpestate_sanitize_array($_POST['plan_rooms']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_rooms', '');
            }
        }

        if (isset($_POST['plan_bath'])) {
            update_post_meta($edit_id, 'plan_bath', wpestate_sanitize_array($_POST['plan_bath']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_bath', '');
            }
        }

        if (isset($_POST['plan_price'])) {
            update_post_meta($edit_id, 'plan_price', wpestate_sanitize_array($_POST['plan_price']));
        } else {
            if (isset($edit_id)) {
                update_post_meta($edit_id, 'plan_price', '');
            }
        }
    }

endif;



/*
 *  processing data on edit dashboard
 *
 */
if (!function_exists('wpestate_save_property_dashboard_data')):

    /**
     * Save Property Dashboard Data.
     *
     * @param mixed $edit_id
     * @param mixed $post_data
     * @param mixed $property_fields_raw
     * @return mixed
     */
    function wpestate_save_property_dashboard_data($edit_id, $post_data, $property_fields_raw) {


 

        foreach ($property_fields_raw as $key => $item):

            if ($item['type'] == 'taxonomy') {
                wpestate_update_property_taxonomy($edit_id, $item, $post_data);
            } elseif ($item['type'] == 'post_meta') {
                if($key=='embed_virtual_tour'){
                    $search             =   "'";
                    $replace            =   '"';
                    $post_data[$key]    =   str_replace($search,$replace,$post_data[$key]);
                }
                
                wpestate_update_property_meta($edit_id, $item, $post_data);
            }


        endforeach;

        // Step 1: Detect dashboard edit submissions only.
        $is_edit_submission = isset($post_data['action']) && $post_data['action'] === 'edit';

        // Step 2: Check if the subunits section was part of the submitted form.
        $has_subunits_toggle = isset($post_data['property_has_subunits']);

        // Step 3: Detect the "deselect all" case where the multi-select key is absent.
        $has_subunits_list = isset($post_data['property_subunits_list']);

        // Step 4: Clear all subunits explicitly when edit form omits the list key.
        if ($is_edit_submission && $has_subunits_toggle && !$has_subunits_list) {
            wpestate_clear_subnits_dashboard($edit_id, array());
        }
    }

endif;






/*
 *  Saving custom fields dashboard
 *
 */
if (!function_exists('wpestate_save_custom_fields_dasboard')):

    /**
     * Save Custom Fields Dasboard.
     *
     * @param mixed $post_id
     * @param mixed $post_data
     * @return mixed
     */
    function wpestate_save_custom_fields_dasboard($post_id, $post_data) {
        $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
        $allowed_html = array();
        $i = 0;
        if (!empty($custom_fields)) {
            while ($i < count($custom_fields)) {
                $name = $custom_fields[$i][0];
                $type = $custom_fields[$i][2];
                $slug = str_replace(' ', '_', $name);
                $slug = wpestate_limit45(sanitize_title($name));
                $slug = sanitize_key($slug);
                if (isset($post_data[$slug])) {
                    if ($type == 'numeric') {
                        $value_custom = intval(wp_kses($post_data[$slug], $allowed_html));

                        if ($value_custom == 0) {
                            $value_custom = '';
                        }

                        update_post_meta($post_id, $slug, $value_custom);
                    } else {
                        $value_custom = esc_html(wp_kses($post_data[$slug], $allowed_html));
                        update_post_meta($post_id, $slug, $value_custom);
                    }
                    $custom_fields_array[$slug] = wp_kses(esc_html($post_data[$slug]), $allowed_html);
                }

                $i++;
            }
        }
    }

endif;




/*
 *  Saving property features dashboard
 *
 */
if (!function_exists('wpestate_save_property_features_dasboard')):

    /**
     * Save Property Features Dasboard.
     *
     * @param mixed $post_id
     * @param mixed $post_data
     * @return mixed
     */
    function wpestate_save_property_features_dasboard($post_id, $post_data) {
        wp_delete_object_term_relationships($post_id, 'property_features');
        $property_features = wpestate_get_cached_terms( 'property_features' );

        if (is_array($property_features)) {
            foreach ($property_features as $key => $term) {
                $feature_name = $term->slug;
                
                $post_var_name_with_id = 'wpresidence_feature_'.$term->term_id;

                if (isset($post_data[$post_var_name_with_id]) && $post_data[$post_var_name_with_id] == 1) {
                    wp_set_object_terms($post_id, $term->term_id, 'property_features', true);
                }
            }
        }
    }

endif;

/*
 *  Update property meta
 *
 */
if (!function_exists('wpestate_update_property_meta')):

    /**
     * Update Property Meta.
     *
     * @param mixed $edit_id
     * @param mixed $item
     * @param mixed $post_data
     * @return mixed
     */
    function wpestate_update_property_meta($edit_id, $item, $post_data) {
        $iframe = array('iframe' => array(
                'src' => array(),
                'width' => array(),
                'name' => array(),
                'height' => array(),
                'frameborder' => array(),
                'style' => array(),
                'allowFullScreen' => array() // add any other attributes you wish to allow
            ));




        if (isset($post_data[$item['meta_name']])) {

            $value = sanitize_text_field($post_data[$item['meta_name']]);

            if ($item['meta_name'] == 'embed_virtual_tour') {
                $value = wp_kses(strval($post_data['embed_virtual_tour']), $iframe);
            }
            
          

          //  print 'saving '. $item['meta_name']. ' with '.$value;

            update_post_meta($edit_id, $item['meta_name'], $value);

            if ($item['meta_name'] == 'property_subunits_list') {
                wpestate_clear_subnits_dashboard($edit_id, $post_data[$item['meta_name']]);
            }
        }
        
    }

endif;




/*
 *  Processing subunits
 *
 */
if (!function_exists('wpestate_clear_subnits_dashboard')):

    /**
     * Clear Subnits Dashboard.
     *
     * @param mixed $post_id
     * @param mixed $property_subunits_list
     * @return mixed
     */
    function wpestate_clear_subnits_dashboard($post_id, $property_subunits_list) {


        $old_property_subunits_list = get_post_meta($post_id, 'property_subunits_list', true);
        if (is_array($old_property_subunits_list)) {
            foreach ($old_property_subunits_list as $key) {
                delete_post_meta(intval($key), 'property_subunits_master');
            }
        } else {
            delete_post_meta(intval($old_property_subunits_list), 'property_subunits_master');
        }

        if (is_array($property_subunits_list)) {
            foreach ($property_subunits_list as $key) {
                update_post_meta(intval($key), 'property_subunits_master', $post_id);
            }
        } else {
            update_post_meta(intval($property_subunits_list), 'property_subunits_master', $post_id);
        }



        update_post_meta($post_id, 'property_subunits_list', $property_subunits_list);
    }

endif;





/*
 *  wpestate_update_city_area_county_dashboard
 *
 */
if (!function_exists('wpestate_update_city_area_county_dashboard')):

    /**
     * Update City Area County Dashboard.
     *
     * @param mixed $post_id
     * @param mixed $post_data
     * @return mixed
     */
    function wpestate_update_city_area_county_dashboard($post_id, $post_data) {
        if (!isset($post_data['property_city'])) {
            $property_city = 0;
        } else {
            $property_city = sanitize_text_field($post_data['property_city']);
        }

        if (!isset($post_data['property_area'])) {
            $property_area = 0;
        } else {
            $property_area = sanitize_text_field($post_data['property_area']);
        }


        if (!isset($post_data['property_county'])) {
            $property_county_state = 0;
        } else {
            $property_county_state = sanitize_text_field($post_data['property_county']);
        }


        if (isset($property_city) && $property_city != 'none') {
            if ($property_city == -1) {
                $property_city = '';
            }
            wp_set_object_terms($post_id, $property_city, 'property_city');
        }

        if (isset($property_area) && $property_area != 'none') {
            wp_set_object_terms($post_id, $property_area, 'property_area');
        }

        if (isset($property_county_state) && $property_county_state != 'none') {
            if ($property_county_state == -1) {
                $property_county_state = '';
            }
            wp_set_object_terms($post_id, $property_county_state, 'property_county_state');
        }


        if ($property_area != '') {
            $terms = get_term_by('name', $property_area, 'property_area');

            if (isset($terms->term_id)) {
                $t_id = $terms->term_id;
                $term_meta = array('cityparent' => $property_city);
                add_option("taxonomy_$t_id", $term_meta);
            }
        }


        if ($property_city != '') {
            $terms = get_term_by('name', $property_city, 'property_city');
            if ($terms != '') {
                $t_id = $terms->term_id;
                $term_meta = array('stateparent' => $property_county_state);
                add_option("taxonomy_$t_id", $term_meta);
            }
        }
    }

endif;










/*
 *  wpestate_update_property_taxonomy
 *
 */
if (!function_exists('wpestate_update_property_taxonomy')):

    /**
     * Update Property Taxonomy.
     *
     * @param mixed $edit_id
     * @param mixed $item
     * @param mixed $post_data
     * @return mixed
     */
    function wpestate_update_property_taxonomy($edit_id, $item, $post_data) {
        if (!isset($post_data[$item['meta_name']])) {
            $prop_category = 0;
        } else {
            $prop_category = sanitize_text_field($post_data[$item['meta_name']]);
        }

        if ($prop_category == -1 && $edit_id != '') {
            wp_delete_object_term_relationships($edit_id, $item['tax_name']);
        }

        if ($prop_category != 'none') {
            $prop_category = get_term($prop_category, $item['tax_name']);
            if ($prop_category && !is_wp_error($prop_category) && isset($prop_category->name)) {
                wp_set_object_terms($edit_id, $prop_category->name, $item['tax_name']);
            }
        }
    }

endif;


if (!function_exists('wpestate_upload_images_dashboard')):
    /**
     * Updates property gallery and featured image from dashboard submission.
     *
     * @param mixed $post_id
     * @param mixed $images_ids_string
     * @param mixed $property_thumb
     * @param mixed $is_edit
     * @return mixed
     */
    function wpestate_upload_images_dashboard($post_id, $images_ids_string = '', $property_thumb = '', $is_edit = '') {
        
 
        // Convert comma separated image IDs to array and clean empty values
        $images_id_array = array_filter(explode(',', $images_ids_string));
        $property_thumb = intval($property_thumb);

        if ( !$is_edit && !empty($images_id_array) ) {
            // If this is not an edit action and images are provided, attach images to post
            foreach ($images_id_array as $image_id) {
                $image_id = intval($image_id);
                if ($image_id > 0) {
                    // Attach each image to the post
                    wp_update_post(array(
                        'ID' => $image_id,
                        'post_parent' => $post_id
                    ));
                }
            }
        }
       
        // Save gallery images array to post meta
        update_post_meta($post_id, 'wpestate_property_gallery', $images_id_array);
 
        // Set thumbnail
        if ($property_thumb !== 0 && is_numeric($property_thumb)) {
            // If specific thumbnail was selected, use it
            set_post_thumbnail($post_id, $property_thumb);
        } else {
            // Otherwise use first image from gallery as thumbnail
            $last_image_id = reset($images_id_array);
            set_post_thumbnail($post_id, $last_image_id);
        }
    }
 endif;




/*
 *  Delete images on dashboard property edit
 *
 */
if (!function_exists('wpestat_delete_images_onedit')):

    /**
     * Wpestat Delete Images Onedit.
     *
     * @param mixed $images_todelete_post
     * @param mixed $current_user
     * @param mixed $agent_list
     * @return mixed
     */
    function wpestat_delete_images_onedit($images_todelete_post, $current_user, $agent_list) {
        $allowed_html = array();
        $images_todelete = wp_kses(esc_html($images_todelete_post), $allowed_html);

        $images_delete_arr = explode(',', $images_todelete);
        foreach ($images_delete_arr as $key => $value) {
            $img = get_post($value);
            $author_id = $img->post_author;
            if ($current_user->ID != $author_id && !in_array($the_post->post_author, $agent_list)) {
                exit('you don\'t have the rights to delete images');
            } else {
                wp_delete_post($value);
            }
        }
    }

endif;











/*
 * Load styles and js files for dashboard
 *
 */

if (!function_exists('wpestate_enqueue_for_dashboard')):

    /**
     * Enqueue For Dashboard.
     *
     * @param mixed $mimify_prefix
     * @param mixed $theme_object
     * @return mixed
     */
    function wpestate_enqueue_for_dashboard($mimify_prefix, $theme_object) {
        wp_enqueue_style('wpestate-poppins', "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap");
        wp_enqueue_style('wpestate-inter', "https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap");


        wp_enqueue_style('wpestate_dashboard_style', get_template_directory_uri() . '/public/css/dashboard.css', array(), $theme_object->Version, 'all');
     
        wp_enqueue_script('wpestate_dashboard', get_theme_file_uri('/js/dashboard/wpestate_dashboard_js' . $mimify_prefix . '.js'), array('jquery'), $theme_object->Version, true);
        wp_localize_script(
                'wpestate_dashboard', 'wpestate_dashboard_js_vars', array(
            'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
            'disabled' => esc_html__('Disabled', 'wpresidence'),
            'published' => esc_html__('Published', 'wpresidence'),
            'disablelisting' => esc_html__('Disable Listing', 'wpresidence'),
            'enablelisting' => esc_html__('Enable Listing', 'wpresidence'),
            'disableagent' => esc_html__('Disable Agent', 'wpresidence'),
            'enableagent' => esc_html__('Enable Agent', 'wpresidence'),
            'allow_file_type' => esc_html__('Valid file formats', 'wpresidence'),
            'plan_title' => esc_html__('Plan Title', 'wpresidence'),
            'are_you_sure' => esc_html__('Are you sure you wish to delete ?', 'wpresidence'),
            'delete_plan' => esc_html__('Delete Plan', 'wpresidence'),
            'plan_description' => esc_html__('Plan Description', 'wpresidence'),
            'plan_size' => esc_html__('Plan Size', 'wpresidence'),
            'plan_rooms' => esc_html__('Plan Rooms', 'wpresidence'),
            'plan_bath' => esc_html__('Plan Bathrooms', 'wpresidence'),
            'plan_price' => esc_html__('Plan Price in', 'wpresidence') . ' ' . esc_html(wpresidence_get_option('wp_estate_currency_symbol', '')),
            'uploadnew' => esc_html__('Upload Plan Image', 'wpresidence'),
            'processing' => esc_html__('processing...', 'wpresidence'),
                )
        );
    }

endif;


/*
 * Check dashboard permission
 *
 */

if (!function_exists('wpestate_dashboard_header_permissions')):

    /**
     * Dashboard Header Permissions.
     *
     * @return mixed
     */
    function wpestate_dashboard_header_permissions() {
        // check if plugin is activated
        if (!function_exists('wpestate_residence_functionality')) {
            esc_html_e('This page will not work without WpResidence Core Plugin, Please activate it from the plugins menu!', 'wpresidence');
            exit();
        }

        global $post;
        $current_page='';
        if(isset($post->ID)){
            $current_page = get_post_meta( $post->ID, '_wp_page_template', true );
        }
        
        // check if user is logged
        if ($current_page !== 'page-templates/user_dashboard_profile.php') {
            if (!is_user_logged_in()) {
                wp_redirect(esc_url(home_url('/')));
                exit;
            }

            // check if user permissions are set.
            if (function_exists('wpestate_check_user_permission_on_dashboard')) {
                if (!wpestate_check_user_permission_on_dashboard()) {
                    wp_redirect(esc_url(home_url('/')));
                    exit('1035');
                }
            }
        }

        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $user_login = $current_user->user_login;
        // wpestate_check_agent_permission($userID);
    }

endif;


/*
 * Check if the page loaded belongs to dashboard
 *
 */
if (!function_exists('wpestate_check_agent_permission')):

    /**
     * Check Agent Permission.
     *
     * @param mixed $userID
     * @return mixed
     */
    function wpestate_check_agent_permission($userID) {
        $user_agent_id = intval(get_user_meta($userID, 'user_agent_id', true));
        $status = get_post_status($user_agent_id);

        if ($status === 'pending' || $status === 'disabled') {
            // wp_redirect(esc_url(home_url('/')));
            exit('1060');
        }
    }

endif;






/*
 * Show Dashboard title
 *
 */
if (!function_exists('wpestate_show_dashboard_title')):

    /**
     * Show Dashboard Title.
     *
     * @param mixed $title
     * @param mixed $second_title
     * @param mixed $description
     * @return mixed
     */
    function wpestate_show_dashboard_title($title = '', $second_title = '', $description = '') {
        global $post;
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $user_data = get_userdata($userID);
        $name = $user_data->first_name . ' ' . $user_data->last_name;
        $message = esc_html__('Welcome', 'wpresidence');
        if (trim($name) != '') {
            $message .= ', ' . $name;
        }

        if ( is_wpresidence_developer_or_agency () )  {
            $developer_id = get_the_author_meta('user_agent_id', $userID);
            $message .= ', ' . get_the_title($developer_id);
        }
        print '<div class="col-md-12 dashboard_hello_section">';
        print '<div class="dashboard_hi_text">' . $message . '</div>';
        print '<h2>' . esc_html($title) . '</h2>';


        $no_unread = intval(get_user_meta($userID, 'unread_mess', true));
        print '<div class="wpestate_bell_note position-absolute d-none d-md-flex "><a href="' . esc_url(wpestate_get_template_link('page-templates/user_dashboard_inbox.php')) . '">';
        include(locate_template('templates/dashboard-templates/dashboard-icons/bell.svg'));
        print '<div class="wpestate_bell_note_unread">' . intval($no_unread) . '</div></a>';
        print '</div>';
        print '</div>';
    }

endif;














/*
 * Generate Dasboard menu
 *
 */

if (!function_exists('wpestate_generate_user_menu')):

    /**
     * Generate User Menu.
     *
     * @param mixed $place
     * @return mixed
     */
    function wpestate_generate_user_menu($place = '') {
        $current_user           = wp_get_current_user();
        $userID                 = $current_user->ID;
        $user_login             = $current_user->user_login;
        $user_agent_id          = intval(get_user_meta($userID, 'user_agent_id', true));
        $home_url               = esc_url(home_url('/'));
        $no_unread              = intval(get_user_meta($userID, 'unread_mess', true));
      
        global $post;
        $current_page_template='';
        if(isset($post->ID)){
            $current_page_template = get_post_meta( $post->ID, '_wp_page_template', true );
        }

        $values_dropdown_property_status = array(
            0 => array(
                'label' => esc_html__('All', 'wpresidence'),
                'value' => 0
            ),
            1 => array(
                'label' => esc_html__('Published', 'wpresidence'),
                'value' => 1
            ),
            2 => array(
                'label' => esc_html__('Disabled', 'wpresidence'),
                'value' => 2
            ),
            3 => array(
                'label' => esc_html__('Expired', 'wpresidence'),
                'value' => 3
            ),
            4 => array(
                'label' => esc_html__('Draft', 'wpresidence'),
                'value' => 4
            ),
            5 => array(
                'label' => esc_html__('Waiting for approval', 'wpresidence'),
                'value' => 5
            ),
        );


        $values_crm_dropdown = array(
            0 => array(
                'label' => esc_html__('Leads', 'wpresidence'),
                'value' => 0,
                'link' => 'wpestate-crm-dashboard_leads.php'
            ),
            1 => array(
                'label' => esc_html__('Contacts', 'wpresidence'),
                'value' => 1,
                'link' => 'wpestate-crm-dashboard_contacts.php'
            ),
        );

        $dashboard_pages = array(
            'page-templates/user_dashboard_main.php' => array(
                'icon' => 'dashboard.svg',
                'label' => esc_html__('Dashboard', 'wpresidence')
            ),
            'page-templates/user_dashboard_profile.php' => array(
                'icon' => 'profile.svg',
                'label' => esc_html__('My Profile', 'wpresidence')
            ),
            'page-templates/user_dashboard.php' => array(
                'icon' => 'listings.svg',
                'label' => esc_html__('My Properties List', 'wpresidence')
            ),
            'page-templates/user_dashboard_add.php' => array(
                'icon' => 'plus.svg',
                'label' => esc_html__('Add New Property', 'wpresidence')
            ),
            'page-templates/user_dashboard_favorite.php' => array(
                'icon' => 'heart.svg',
                'label' => esc_html__('Favorites', 'wpresidence')
            ),
            'page-templates/user_dashboard_searches.php' => array(
                'icon' => 'search.svg',
                'label' => esc_html__('Saved Searches', 'wpresidence')
            ),
            'page-templates/user_dashboard_invoices.php' => array(
                'icon' => 'invoices.svg',
                'label' => esc_html__('My Invoices', 'wpresidence')
            ),
            'page-templates/user_dashboard_add_agent.php' => array(
                'icon' => 'addagent.svg',
                'label' => esc_html__('Add New Agent', 'wpresidence'),
            ),
            'page-templates/user_dashboard_agent_list.php' => array(
                'icon' => 'agents.svg',
                'label' => esc_html__('Agent List', 'wpresidence'),
            ),
            'page-templates/user_dashboard_inbox.php' => array(
                'icon' => 'message.svg',
                'label' => esc_html__('Inbox', 'wpresidence') . '<div class="unread_mess">' . intval($no_unread) . '</div>',
            ),
            'user_dashboard_showing.php' => array(
                'icon' => 'dashboard.svg',
                'label' => esc_html__('Dashboard', 'wpresidence')
            ),
            'wpestate-crm-dashboard.php' => array(
                'icon' => 'crm.svg',
                'label' => esc_html__('CRM', 'wpresidence')
            ),
        );


        if(!function_exists('wpestate_crm_top_level_menu')){
            unset($dashboard_pages['wpestate-crm-dashboard.php']);
        }


        $no_unread = intval(get_user_meta($userID, 'unread_mess', true));
        $user_role = intval(get_user_meta($userID, 'user_estate_role', true));
        
        

        if (
            !current_user_can('wpresidence_developer_role') &&
            !current_user_can('wpresidence_agency_role')
        ) {
         
            unset($dashboard_pages['page-templates/user_dashboard_agent_list.php']);
            unset($dashboard_pages['page-templates/user_dashboard_add_agent.php']);
        }
       
        



        foreach ($dashboard_pages as $page => $details):
            $active_class = '';
            $template_link = wpestate_get_template_link($page);
            if ( $current_page_template == $page ) {
                $active_class = 'user_tab_active';
            }
            if ($page == 'wpestate-crm-dashboard.php' && 
                ( $current_page_template == 'wpestate-crm-dashboard_contacts.php' || $current_page_template == 'wpestate-crm-dashboard_leads.php')) {
                $active_class = 'user_tab_active';
            }



            if ($template_link != $home_url && 
                $template_link != '' && wpestate_check_user_permission_on_dashboard(str_replace('.php', '', $page))) {
            
                    if (wpestate_check_user_agent_id($user_agent_id)) {
                    if (wpestate_check_user_role_menu($user_role, $user_agent_id)) {
                        if ($place == 'top' && $details['label'] === esc_html__('Logout', 'wpresidence')) {
                            print '<li role="presentation" class="divider"></li>';
                        }
                        ?>


                        <li role="presentation" class="<?php print esc_attr($active_class) . '_list';
                        print ' ' . esc_attr(str_replace('.php', '', $page)) . ' ' . 'user_role_' . esc_attr($user_role); ?>">
                            
                            <?php 
                            wpresidence_render_conditional_dashboard_link($template_link, $active_class, $details,$page); 
                            ?>
                            
                            
                            
                            <?php
                            if ($page == 'page-templates/user_dashboard.php') {

                                $status = '';
                                if (isset($_GET['status'])) {
                                    $status = intval($_GET['status']);
                                }
                                ?>
                                <ul class="secondary_menu_sidebar ">
                                    <?php
                                    foreach ($values_dropdown_property_status as $key => $item) {
                                        $selected_class = '';
                                        if ($status == $item['value']) {
                                            $selected_class = " secondary_select ";
                                        }
                                        ?>
                                        <li>
                                            <a class="dashboad-tooltip <?php echo esc_attr($selected_class); ?>" href="<?php print esc_url_raw(add_query_arg('status', $item['value'], '')); ?>">
                                    <?php echo ' - ' . esc_html($item['label']); ?>
                                            </a>
                                        </li>
                                <?php } ?>
                                </ul>

                            <?php
                            } else if ( $page == 'wpestate-crm-dashboard.php' || 
                                        $current_page_template == 'wpestate-crm-dashboard_contacts.php' || 
                                        $current_page_template == 'wpestate-crm-dashboard_leads.php') {


                                $status = '';
                                if (isset($_GET['actions'])) {
                                    $status = intval($_GET['actions']);
                                }
                                ?>

                                <ul class="secondary_menu_sidebar ">
                                    <?php
                                    foreach ($values_crm_dropdown as $key => $item) {
                                        $selected_class = '';
                                        if ($status == $item['value']) {
                                            $selected_class = " secondary_select ";
                                        }
                                        $base_crm = wpestate_get_template_link('wpestate-crm-dashboard.php');
                                        ?>
                                        <li>
                                            <a class="dashboad-tooltip <?php echo esc_attr($selected_class); ?>" href="<?php print esc_url_raw(add_query_arg('actions', $item['value'], $base_crm)); ?>">
                                    <?php echo ' - ' . esc_html($item['label']); ?>
                                            </a>
                                        </li>
                            <?php } ?>
                                </ul>

                        <?php } ?>

                        </li>
                        <?php
                    }
                }
            }
        endforeach;
        ?>

        <li role="presentation">
            <a href="<?php print esc_url(wp_logout_url(esc_url($home_url))); ?>" class="" >
            <?php
            include(locate_template('templates/dashboard-templates/dashboard-icons/logout.svg'));
            esc_html_e('Logout', 'wpresidence');
            ?>
            </a>
            <?php
        }


endif;

/**
 * Renders a dashboard link that can be disabled based on user type and status
 * 
 * @param string $template_link The URL for the link
 * @param string $active_class CSS classes for the link
 * @param array $details Icon and label details
 * @param string $status Current status value
 * @return void Outputs the HTML directly
 */
function wpresidence_render_conditional_dashboard_link($template_link, $active_class, $details,$page) {
    
    if(!function_exists('is_wpresidence_developer_or_agency')){
        return '';
    }

    $is_inactive = false;
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $user_login = $current_user->user_login;

    // Get user role and agent information
    $user_role = get_user_meta($userID, 'user_estate_role', true);
    $user_agent_id = intval(get_user_meta($userID, 'user_agent_id', true));
    $status = get_post_status($user_agent_id);

  
    
    $should_disable=array(
        'page-templates/user_dashboard_add.php',
        'page-templates/user_dashboard_add_agent.php',
        'page-templates/user_dashboard_agent_list.php'
    );

    // Check if user is developer, agency or agent AND status is pending or disabled
    if (is_wpresidence_developer_agency_or_agent() && 
        ($status === 'pending' || $status === 'disabled') &&
        in_array($page,$should_disable)
        ) {
        $is_inactive = true;
    }
    
    if ($is_inactive) {
        ?>
        <a class="<?php echo esc_attr($active_class); ?> wpresidence_dashboard_disabled_link"  
                data-bs-toggle="tooltip" title="<?php esc_attr_e('Your account is not approved','wpresidence');?>" >
                    <?php
                    include(locate_template('templates/dashboard-templates/dashboard-icons/' . $details['icon']));
                    echo trim($details['label']);
                    ?>
        </a>
        <?php
    } else {
        ?>
        <a href="<?php echo esc_url($template_link); ?>" class="<?php echo esc_attr($active_class); ?>">
            <?php
            include(locate_template('templates/dashboard-templates/dashboard-icons/' . $details['icon']));
            echo trim($details['label']);
            ?>
        </a>
        <?php
    }
}


    /*
     * check agent status
     *
     */

    if (!function_exists('wpestate_check_user_agent_id')):

        /**
         * Check User Agent Id.
         *
         * @param mixed $user_agent_id
         * @return mixed
         */
        function wpestate_check_user_agent_id($user_agent_id) {
            return true;
            if ($user_agent_id == 0 || ($user_agent_id != 0 && get_post_status($user_agent_id) != 'pending' && get_post_status($user_agent_id) != 'disabled')) {
                return true;
            }
            return false;
        }

    endif;



    /*
     * Check agent or developer role
     *
     */

    if (!function_exists('wpestate_check_user_role_menu')):

        /**
         * Check User Role Menu.
         *
         * @param mixed $user_role
         * @param mixed $user_agent_id
         * @return mixed
         */
        function wpestate_check_user_role_menu($user_role, $user_agent_id) {
            return true;
            if (  is_wpresidence_developer_or_agency() ) {
                
                if ($user_agent_id != 0 && get_post_status($user_agent_id) != 'pending') {
                    return true;
                }
                
            }
            return false;
        }

    endif;



    /*
     * Fitler submission fields - use only what admin set in theme option
     *
     */

    if (!function_exists('wpestate_unset_nonsubmit_fields')):

        /**
         * Unset Nonsubmit Fields.
         *
         * @param mixed $property_fields
         * @return mixed
         */
        function wpestate_unset_nonsubmit_fields($property_fields) {
            $wpestate_submission_page_fields = wpresidence_get_option('wp_estate_submission_page_fields', '');
            if (is_array($wpestate_submission_page_fields)) {
                foreach ($property_fields as $key => $details):
                    if ($details['meta_name'] != 'wpestate_title' && $details['meta_name'] != 'wpestate_description') {
                        if (!in_array($details['meta_name'], $wpestate_submission_page_fields)) {
                            unset($property_fields[$key]);
                        }
                    }
                endforeach;
            }

            return $property_fields;
        }

    endif;












    /*
     * Display form fields
     *
     */
    if (!function_exists('wpestate_dashnoard_display_form_fields')):

        /**
         * Dashnoard Display Form Fields.
         *
         * @param mixed $items
         * @param mixed $itemID
         * @param mixed $type_submission
         * @return mixed
         */
        function wpestate_dashnoard_display_form_fields($items, $itemID, $type_submission = '') {
            $return = '';

            foreach ($items as $key => $details):
                $bootstral_length = 6;
                if (isset($details['bootstral_length'])) {
                    $bootstral_length = $details['bootstral_length'];
                }
                $return .= '<div class="col-md-' . $bootstral_length . ' ' . $key . '_wrapper ">';
                $return .= '<label for="' . $key . '">' . $details['label'] . '</label>';
                $return .= wpestate_dashnoard_display_form_fields_return_input($key, $details, $itemID);
                $return .= '</div>';
            endforeach;

            return $return;
        }

    endif;







    /*
     * Display form fields Input
     *
     */
    if (!function_exists('wpestate_dashnoard_display_form_fields_return_input')):

        /**
         * Dashnoard Display Form Fields Return Input.
         *
         * @param mixed $key
         * @param mixed $details
         * @param mixed $itemID
         * @return mixed
         */
        function wpestate_dashnoard_display_form_fields_return_input($key, $details, $itemID) {
            $value = wpestate_get_saved_input_value($details, $itemID);
            $return = '';

            if (isset($details['inputype']) && $details['inputype'] != 'input') {
                if ($details['inputype'] == 'wp_editor') {
                    $return .= wpestate_dasboard_display_wp_editor($details, $value);
                } elseif ($details['inputype'] == 'wp_dropdown') {
                    $return .= wpestate_dasboard_display_wp_dropdown($details, $value);
                } elseif ($details['inputype'] == 'textarea') {
                    $return .= '<textarea  id="' . $key . '" class="form-control"   name="' . $key . '">' . esc_html($value) . '</textarea>';
                }
            } else {
                $return .= '<input type="text" id="' . $key . '" class="form-control" value="' . esc_html($value) . '"  name="' . $key . '">';
            }

            return $return;
        }

    endif;



    /*
     * Display form fields Input
     *
     */
    if (!function_exists('wpestate_dasboard_display_wp_dropdown')):

        /**
         * Dasboard Display Wp Dropdown.
         *
         * @param mixed $details
         * @param mixed $value
         * @return mixed
         */
        function wpestate_dasboard_display_wp_dropdown($details, $value) {
            $return = '';
            if (!taxonomy_exists($details['tax_name'])) {
                return $return;
            }
            $terms_check = get_terms(array(
                'taxonomy' => $details['tax_name'],
                'number' => 1,
                'hide_empty' => false
            ));
            if (is_wp_error($terms_check)) {
                return $return;
            }
            ob_start();
            $args = array(
                'class' => 'select-submit2',
                'hide_empty' => false,
                'selected' => $value,
                'name' => $details['meta_name'],
                'id' => $details['meta_name'] . '_submit',
                'orderby' => 'NAME',
                'order' => 'ASC',
                'show_option_none' => esc_html__('None', 'wpresidence'),
                'taxonomy' => $details['tax_name'],
                'hierarchical' => true
            );
            wp_dropdown_categories($args);
            $return = ob_get_contents();
            ob_end_clean();

            return $return;
        }

    endif;





    /*
     * Display form fields Input
     *
     */
    if (!function_exists('wpestate_dasboard_display_wp_editor')):

        /**
         * Dasboard Display Wp Editor.
         *
         * @param mixed $details
         * @param mixed $value
         * @return mixed
         */
        function wpestate_dasboard_display_wp_editor($details, $value) {
            $return = '';
            ob_start();
            wp_editor(
                    stripslashes($value), $details['meta_name'], array(
                'textarea_rows' => 6,
                'textarea_name' => $details['meta_name'],
                'wpautop' => true, // use wpautop?
                'media_buttons' => false, // show insert/upload button(s)
                'tabindex' => '',
                'editor_css' => '',
                'editor_class' => '',
                'teeny' => false,
                'dfw' => false,
                'tinymce' => false,
                'quicktags' => array("buttons" => "strong,em,block,ins,ul,li,ol,close"),
                    )
            );
            $return = ob_get_contents();
            ob_end_clean();

            return $return;
        }

    endif;




    /*
     * Display form textarea
     *
     */
    if (!function_exists('wpestate_dashnoard_display_form_fields_return_textarea')):

        /**
         * Dashnoard Display Form Fields Return Textarea.
         *
         * @return mixed
         */
        function wpestate_dashnoard_display_form_fields_return_textarea() {
            $value = wpestate_get_saved_input_value($details);
            $return .= '<input type="text" id="' . $key . '" class="form-control" value="' . esc_html($value) . '"  name="' . $key . '">';

            return $return;
        }

    endif;








    /*
     * Display form textarea
     *
     */
    if (!function_exists('wpestate_get_saved_input_value')):

        /**
         * Get Saved Input Value.
         *
         * @param mixed $details
         * @param mixed $itemID
         * @return mixed
         */
        function wpestate_get_saved_input_value($details, $itemID) {
            $return = '';
            if ($details['type'] == 'wpestate_title') {
                $return = get_the_title($itemID);
            } elseif ($details['type'] == 'wpestate_description') {
                $return = get_post_field('post_content', $itemID);
            } elseif ($details['type'] == 'taxonomy') {
                $tax_list = get_the_terms($itemID, $details['tax_name']);
                if (!is_wp_error($tax_list) && !empty($tax_list) && isset($tax_list[0]->term_id)) {
                    $return = $tax_list[0]->term_id;
                }
            } elseif ($details['type'] == 'taxonomy_name') {
                $tax_list = get_the_terms($itemID, $details['tax_name']);
                if (!is_wp_error($tax_list) && !empty($tax_list) && isset($tax_list[0]->name)) {
                    $return = $tax_list[0]->name;
                }
            } elseif ($details['type'] == 'user_meta') {
                $return = get_the_author_meta($details['meta'], $itemID);
            } elseif ($details['type'] == 'post_meta') {
             
                $return = get_post_meta($itemID, $details['meta_name'], true);
            }


            if (isset($details['bypass']) && intval($details['bypass']) > 0) {
                if ($details['type'] == 'user_meta') {
                    $return = get_the_author_meta($details['meta'], $details['bypass']);
                } elseif ($details['type'] == 'post_meta') {
                    $return = get_post_meta($details['bypass'], $details['meta_name'], true);
                }
            }



            return $return;
        }

    endif;




    if (!function_exists('wpestate_make_featured_dashboard')):

        /**
         * Make Featured Dashboard.
         *
         * @param mixed $featured_id
         * @return mixed
         */
        function wpestate_make_featured_dashboard($featured_id) {
            $current_user = wp_get_current_user();
            $userID = $current_user->ID;
            $parent_userID = wpestate_check_for_agency($userID);
            $post = get_post($featured_id);

            $agent_list = (array) get_user_meta($parent_userID, 'current_agent_list', true);

            if (!is_user_logged_in()) {
                exit('ko');
            }
            if ($userID === 0) {
                exit('out pls');
            }

            if ($post->post_author != $userID && !in_array($post->post_author, $agent_list)) {
                exit('get out of my cloud');
            } else {
                if (wpestate_get_remain_featured_listing_user($parent_userID) > 0) {
                    wpestate_update_featured_listing_no($parent_userID);
                    update_post_meta($featured_id, 'prop_featured', 1);
                }
            }
            $list_link = wpestate_get_template_link('page-templates/user_dashboard.php');
            wp_redirect($list_link);
            exit;
        }


endif;

if (!function_exists('wpestate_display_acf_frontend_form')):
/**
 * Displays an ACF frontend form for editing a post
 *
 * @param int $post_id The ID of the post to edit
 * @param string $post_type The post type of the post
 * @return string HTML output of the form
 */
function wpestate_display_acf_frontend_form($post_id, $post_type) {
    
    if(!function_exists('acf_get_field_groups')){
        return;
    }

    // Get ACF field groups for this post type
    $field_groups = acf_get_field_groups(array(
        'post_type' => $post_type
    ));
    
    if (empty($field_groups)) {
        return '';
    }

    $field_value = '';
    
    ob_start();
    ?>  
        <?php foreach ($field_groups as $field_group): ?>
            <?php $fields = acf_get_fields($field_group['key']); ?>
            
            <div class="field-group">
                
                <?php foreach ($fields as $field): ?>
                    <?php 
                    $field_value = '';
                    if ( !empty( $post_id ) )   {
                        $field_value = get_field($field['name'], $post_id); 
                    }
                    if ( is_null($field_value) ) {
                        $field_value = '';
                    }
                    ?>
                    
                    <div class="acf-field">
                        <label for="<?php echo $field['name']; ?>">
                            <?php echo $field['label']; ?>
                            <?php if ($field['required']): ?><span class="required">*</span><?php endif; ?>
                        </label>
                        
                        <?php
                        // Render different field types
                        switch ($field['type']) {
                            case 'text':
                            case 'email':
                            case 'url':
                                ?>
                                <input type="<?php echo $field['type']; ?>" 
                                       name="acf_fields[<?php echo $field['name']; ?>]" 
                                       id="<?php echo $field['name']; ?>" 
                                       value="<?php echo esc_attr($field_value); ?>"
                                       <?php echo $field['required'] ? 'required' : ''; ?>>
                                <?php
                                break;
                                
                            case 'textarea':
                                ?>
                                <textarea name="acf_fields[<?php echo $field['name']; ?>]" 
                                         id="<?php echo $field['name']; ?>"
                                         <?php echo $field['required'] ? 'required' : ''; ?>><?php echo esc_textarea($field_value); ?></textarea>
                                <?php
                                break;
                                
                            case 'number':
                                ?>
                                <input type="number" 
                                       name="acf_fields[<?php echo $field['name']; ?>]" 
                                       id="<?php echo $field['name']; ?>" 
                                       value="<?php echo esc_attr($field_value); ?>"
                                       <?php echo $field['required'] ? 'required' : ''; ?>>
                                <?php
                                break;
                                
                            case 'select':
                                ?>
                                <select name="acf_fields[<?php echo $field['name']; ?>]" 
                                        id="<?php echo $field['name']; ?>"
                                        <?php echo $field['required'] ? 'required' : ''; ?>>
                                    <?php if (!$field['required']): ?>
                                        <option value="">Select...</option>
                                    <?php endif; ?>
                                    <?php foreach ($field['choices'] as $value => $label): ?>
                                        <option value="<?php echo esc_attr($value); ?>" 
                                                <?php selected($field_value, $value); ?>>
                                            <?php echo esc_html($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php
                                break;
                                
                            case 'checkbox':
                                ?>
                                <div class="checkbox-group">
                                    <?php foreach ($field['choices'] as $value => $label): ?>
                                        <label>
                                            <input type="checkbox" 
                                                   name="acf_fields[<?php echo $field['name']; ?>][]" 
                                                   value="<?php echo esc_attr($value); ?>"
                                                   <?php echo (is_array($field_value) && in_array($value, $field_value)) ? 'checked' : ''; ?>>
                                            <?php echo esc_html($label); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <?php
                                break;
                                
                            case 'radio':
                                ?>
                                <div class="radio-group">
                                    <?php foreach ($field['choices'] as $value => $label): ?>
                                        <label>
                                            <input type="radio" 
                                                   name="acf_fields[<?php echo $field['name']; ?>]" 
                                                   value="<?php echo esc_attr($value); ?>"
                                                   <?php checked($field_value, $value); ?>>
                                            <?php echo esc_html($label); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <?php
                                break;
                                
                            case 'true_false':
                                ?>
                                <label>
                                    <input type="checkbox" 
                                           name="acf_fields[<?php echo $field['name']; ?>]" 
                                           value="1"
                                           <?php checked($field_value, 1); ?>>
                                    <?php echo $field['message'] ?: 'Yes'; ?>
                                </label>
                                <?php
                                break;
                                
                            default:
                                ?>
                                <input type="text" 
                                       name="acf_fields[<?php echo $field['name']; ?>]" 
                                       id="<?php echo $field['name']; ?>" 
                                       value="<?php echo esc_attr($field_value); ?>"
                                       <?php echo $field['required'] ? 'required' : ''; ?>>
                                <?php
                                break;
                        }
                        ?>
                        
                        <?php if ($field['instructions']): ?>
                            <p class="field-instructions"><?php echo $field['instructions']; ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php
    
    return ob_get_clean();
}
endif;
