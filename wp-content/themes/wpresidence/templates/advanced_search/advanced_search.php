<?php
/**
 * MLLDONE
 * Advanced Search Template
 *
 * This file handles the display of the advanced search form in WPResidence.
 * It determines when and how to show the search form based on various conditions
 * and user settings.
 *
 */

/**
 * Advanced Search Configuration
 * 
 * This section initializes key variables and detects the current page template.
 * It's crucial for determining how and where to display the advanced search form.
 *
 * @global int $adv_search_type The type of advanced search to be displayed
 * @global WP_Post $post The global post object
 */
global $adv_search_type;
global $post;

// Detect the current page template
$current_page_template = '';
if (isset($post->ID)) {
    $page_template = get_post_meta($post->ID, '_wp_page_template', true);
    $current_page_template = ($page_template);
}

/**
 * Determine Advanced Search Visibility
 *
 * This section decides whether to display the advanced search form based on
 * various conditions such as global settings, page-specific settings, and
 * the current context (e.g., taxonomy pages, singular property pages).
 */

// Get global setting for advanced search visibility
$show_adv_search = wpresidence_get_option('wp_estate_show_adv_search_general', '');
$post_id = '';

// Check for page-specific advanced search settings
if (isset($post->ID)) {
    $post_id = $post->ID;
    $show_adv_search_local = get_post_meta($post_id, 'page_show_adv_search', true);
    
    // Use global setting if no local setting is specified
    $show_adv_search_local = ($show_adv_search_local === '') ? 'global' : $show_adv_search_local;
    
    // Override global setting with local setting if not set to 'global'
    if ($show_adv_search_local !== 'global') {
        $show_adv_search = $show_adv_search_local;
    }
}

// Special handling for taxonomy, category, archive, and tag pages
if (is_tax() || is_category() || is_archive() || is_tag()) {
    $show_adv_search = wpresidence_get_option('wp_estate_show_adv_search_tax', '');
}

// Special handling for single property pages
if (is_singular('estate_property')) {
    $show_adv_search = wpresidence_get_option('wp_estate_show_adv_search_property_page', '');
}

/**
 * Initialize Advanced Search Parameters
 *
 * This section prepares all necessary variables and settings for the advanced search form.
 * It's executed only if the advanced search is set to be displayed ($show_adv_search !== 'no').
 */
if ($show_adv_search !== 'no') {
    // Retrieve search results page link
    $adv_submit = wpestate_get_template_link('page-templates/advanced_search_results.php');
    
    // Prepare arguments for select dropdowns
    $args = wpestate_get_select_arguments();
    
    // Initialize select lists for various property attributes
    $action_select_list        = wpestate_get_action_select_list($args);
    $categ_select_list         = wpestate_get_category_select_list($args);
    $select_city_list          = wpestate_get_city_select_list($args);
    $select_area_list          = wpestate_get_area_select_list($args);
    $select_county_state_list  = wpestate_get_county_state_select_list($args);
    
    // Retrieve advanced search settings
    $adv_search_settings = array(
        'type'          => wpresidence_get_option('wp_estate_adv_search_type', ''),
        'visibility'    => wpresidence_get_option('wp_estate_show_adv_search_visible', ''),
        'on_start'      => wpresidence_get_option('wp_estate_search_on_start', ''),
        'float_form'    => wpestate_retrive_float_search_placement($post_id),
        'float_top'     => wpresidence_get_option('wp_estate_float_form_top', '')
    );
    
    // Special handling for taxonomy pages
    if (is_tax()) {
        $adv_search_settings['float_top'] = esc_html(wpresidence_get_option('wp_estate_float_form_top_tax'));
    }
    
    // Initialize wrapper class
    $close_class_wr = ' ';

    // Use extracted variables

    extract($adv_search_settings);

    /**
     * Setup Additional Search Parameters and CSS Classes
     *
     * This section prepares various CSS classes and additional search parameters
     * based on theme options, page templates, and other conditions.
     */

    // Initialize search wrapper classes
    $search_wrapper_classes = array();

    // Handle sticky search
    $sticky_search = wpresidence_get_option('wp_estate_sticky_search');
    if (wpestate_is_user_dashboard()) {
        $sticky_search = 'no';
    }
    if ($sticky_search === 'yes') {
        $search_wrapper_classes[] = 'search_wrapper_sticky_search';
    }

    // Handle search placement
    $search_wrapper_classes[] = ($on_start == 'yes') ? 'with_search_on_start' : 'with_search_on_end';

    // Handle float search form
    if ($float_form == "yes" || is_page_template('page-templates/splash_page.php')) {
        $search_wrapper_classes[] = 'with_search_form_float';
    } else {
        $search_wrapper_classes[] = 'without_search_form_float';
    }

    // Handle advanced search visibility for specific search types
    if (in_array($type, array(1, 3, 4))) {
        $show_adv_search_visible = wpresidence_get_option('wp_estate_show_adv_search_visible', '');
        if ($show_adv_search_visible == 'no') {
            $close_class_wr .= " float_search_closed ";
        }
    }

    // Handle pages without search
    $page_without_search = '';
    if (isset($post->ID) && is_page($post->ID) && 
        ($current_page_template == 'page-templates/contact_page.php' || is_singular('estate_agency') || is_singular('estate_developer'))) {
        $page_without_search = 'page_without_search';
    }

    // Combine all classes
    $search_wrapper_classes[] = $page_without_search;
    $search_wrapper_classes[] = $close_class_wr;
    $search_wrapper_classes[] = "search_wr_{$type}";

    $search_wrapper_class = implode(' ', array_filter($search_wrapper_classes));

    /**
     * Display Advanced Search Form
     *
     * This section handles the actual display of the advanced search form
     * based on the settings and classes prepared above.
     */
    $prpg_slider_type_status = esc_html(wpresidence_get_option('wp_estate_global_prpg_slider_type', ''));
    $show_adv_search_general = wpresidence_get_option('wp_estate_show_adv_search_general', '');

    if (!(is_singular('estate_property') && 
          ((get_post_meta($post->ID, 'local_pgpr_slider_type', true) == 'global' && $prpg_slider_type_status === 'full width header') ||
           (get_post_meta($post->ID, 'local_pgpr_slider_type', true) == 'full width header')))) {
        
        if ($show_adv_search_general != 'no'  || $show_adv_search_local != 'no') {
            ?>
            <div id="search_wrapper" class="search_wrapper d-none d-xl-block <?php echo esc_attr($search_wrapper_class); ?>" 
                 <?php echo wpestate_search_float_position($post_id); ?> 
                 data-postid="<?php echo intval($post_id); ?>">
                
                <?php
                if (!(isset($post->ID) && is_page($post->ID) && 
                    ($current_page_template == 'page-templates/contact_page.php' || is_singular('estate_agency') || is_singular('estate_developer')))) {
                    echo '<div class="search_wrapper_color"></div>';
                    
                    // Include the appropriate search form based on the search type
                    $search_template = 'templates/advanced_search/advanced_search_type' . $type . '.php';
                    if (file_exists(get_theme_file_path($search_template))) {
                        include(get_theme_file_path($search_template));
                    } else {
                        // Fallback to default search form if specific type doesn't exist
                        include(get_theme_file_path('templates/advanced_search/advanced_search_type1.php'));
                    }
                }
                ?>
            </div><!-- end search_wrapper -->
            <?php
        }
    }
}
?>