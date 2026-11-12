<?php
//MILLDONE
// src: templates/header_media/header_media.php
// Early determination of page types


//"Media Header" type for Property Page?  wp_estate_header_type_property_page
//Media Header Type?  wp_estate_header_type
//Media Header Type for taxonomy pages?    wp_estate_header_type_taxonomy
//Media Header Type for Blog Posts?   wp_estate_header_type_blog_post



// Exit if in Elementor edit mode for wpestate-studio posts
if (is_singular('wpestate-studio') 
    //&& 
    //    \Elementor\Plugin::$instance->preview->is_preview_mode()
) {
    return;
}

// Check if Elementor theme location is not handling the single template
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {
    // Only proceed if Elementor is loaded
    if (did_action('elementor/loaded')) {

     

        // Array of WP Estate single template functions to check
        $enabled_functions = [
            'wpestate_single_agent_enabled',
            'wpestate_single_developer_enabled',
            'wpestate_single_agency_enabled',
            'wpestate_single_post_enabled',
           //  'wpestate_single_property_enabled', - disabled per issue 210
            'wpestate_category_template_enabled',
        ];
        
        // Check each function - if any returns true, exit early
        foreach ($enabled_functions as $function) {
            if (function_exists($function) && $function()) {
                return; // Template is enabled, don't render default content
            }
        }
    }
}




$is_archive             = is_category() || is_tax() || is_archive() || is_search();
$is_404                 = is_404();
$is_singular_property   = is_singular('estate_property');
$is_singular_post       = is_singular('post');
$is_splash_page         = is_page_template('page-templates/splash_page.php');
$is_property_list_half  = is_page_template('page-templates/property_list_half.php');
$is_advanced_search     = is_page_template('page-templates/advanced_search_results.php');
$adv_search_type        =   wpresidence_get_option('wp_estate_adv_search_type','');

$postID                 = isset($post->ID) ? $post->ID : '';
$page_template          = $postID ? get_post_meta($postID, '_wp_page_template', true) : '';

// Filter: Allows developers to modify or add custom singular header types
$singular_header_types = apply_filters('wpestate_singular_header_types', [
    'estate_agency' => 21,
    'estate_developer' => 22,
]);

$search_on_start                =    wpresidence_get_option('wp_estate_search_on_start', '');
$mobile_header_media_sticky     =   "mobile_header_media_sticky_".esc_attr( wpresidence_get_option('wp_estate_mobile_sticky_header') );

// archive situation 
if( $is_archive ){ 


    // Filter: Allows customization of the header type for archive pages
    $global_header_type = apply_filters('wpestate_archive_header_type', wpresidence_get_option('wp_estate_header_type_taxonomy',''));

    // no media header if we have half map tempplate for archive
    if(   intval( wpresidence_get_option('wp_estate_property_list_type','') ) == 2)   {
        $global_header_type =0;

    }

}else{
 
    // Filter: Allows customization of the global header type for non-archive pages
    $global_header_type = apply_filters('wpestate_global_header_type', wpresidence_get_option('wp_estate_header_type', ''));
    
    if($is_singular_post){
        // Filter: Allows customization of the global header type for post pages
        $global_header_type = apply_filters('wpestate_global_header_type_for_post', wpresidence_get_option('wp_estate_header_type_blog_post', ''));
    }else if($is_singular_property){
        // Filter: Allows customization of the global header type for property pages
        $global_header_type = apply_filters('wpestate_global_header_type_for_property', wpresidence_get_option('wp_estate_header_type_property_page', ''));
    }
}

// get per page header type if any
$header_type = intval( get_post_meta($postID, 'header_type', true) );

// Filter: Allows modification of the header type for any page
$header_type = apply_filters('wpestate_header_type', $header_type, $postID);




// splash page situation
if ($is_splash_page) {
    $header_type = 20;
}else{
    $show_adv_search_status = wpresidence_get_option('wp_estate_show_adv_search', '');
    $adv_search_type        = wpresidence_get_option('wp_estate_adv_search_type', '');
}

// Action: Executed before the header media section begins
do_action('wpestate_before_header_media');

// if is the case display advanced search before header media 
if ($search_on_start === 'yes' && !$is_splash_page  ) {
    // Action: Executed before the advanced search is displayed
    do_action('wpestate_before_advanced_search');
    wpestate_show_advanced_search($postID);
    // Action: Executed after the advanced search is displayed
    do_action('wpestate_after_advanced_search');
}




// if we load header for taxonomy
if(  ($is_404 || $is_archive)  &&  wpestate_check_google_map_tax() ){
    // Filter: Customizes header type for 404 and archive pages with Google Map taxonomy
    $header_type = apply_filters('wpestate_404_archive_header_type', 7);
} 


// header for single post estate developers or agency
foreach ($singular_header_types as $post_type => $type) {
    if (is_singular($post_type)) {
        // Filter: Allows customization of header type for specific singular post types
        $header_type = apply_filters("wpestate_{$post_type}_header_type", $type);
        break;
    }
}





// when to display google/open street  maps on half map like pages
if ($is_property_list_half || 
    (is_tax() && intval(wpresidence_get_option('wp_estate_property_list_type','')) == 2) || 
    (is_page_template('page-templates/advanced_search_results.php') && intval(wpresidence_get_option('wp_estate_property_list_type_adv','')) == 2)) {
    // Filter: Customizes header type for property list pages
    $header_type = apply_filters('wpestate_property_list_header_type', 5);
}








//
// Start the html markup here
// Filter: Allows modification of the header media CSS class
//
//

$header_media_class = apply_filters('wpestate_header_media_class', "header_media d-flex  w-100 {$mobile_header_media_sticky} header_mediatype_{$header_type} with_search_{$adv_search_type} {$args['elementor_class']}");
?>

<div class="<?php echo esc_attr($header_media_class); ?>">
    <?php
    // Action: Executed before the main header media content
    do_action('wpestate_before_header_media_content');

    if (!$header_type==0 && !$is_archive){  
      
        // is not global settings
        // Action: Executed before displaying singular header media
        do_action('wpestate_before_singular_header_media');
        wpresidence_header_media_type_for_singular($header_type,$postID);
        // Action: Executed after displaying singular header media
        do_action('wpestate_after_singular_header_media');
    }else{    
        // we don't have particular settings - apply global header
        // Action: Executed before displaying global header media
        do_action('wpestate_before_global_header_media');
        wpresidence_header_media_global_header($is_archive,$is_singular_post,$is_singular_property,$global_header_type);
        // Action: Executed after displaying global header media
        do_action('wpestate_after_global_header_media');
    }

    $show_adv_search_slider     =   wpresidence_get_option('wp_estate_show_adv_search_slider','');
    $show_mobile                =   0;

    if ( is_404() ||  $is_archive ){
        $header_type=0;
    }
    if( wpestate_float_search_placement_new() || $is_splash_page    ){
        if($header_type==1 || ( $global_header_type==0 && $header_type==0) ){
            //nothing
        }else{
        
           // Action: Executed before including the advanced search template
           include( locate_template( 'templates/advanced_search/advanced_search.php') );
           // Action: Executed after including the advanced search template
           do_action('wpestate_after_advanced_search_template');
        }
    }

    if( $is_splash_page   ){
        if( wp_is_mobile() ){
            // Action: Executed before displaying mobile advanced search on splash page
            do_action('wpestate_before_mobile_advanced_search');
            include( locate_template( 'templates/advanced_search/adv_search_mobile.php') );
            // Action: Executed after displaying mobile advanced search on splash page
            do_action('wpestate_after_mobile_advanced_search');
        }
    }

    if($is_splash_page   ){
        // Action: Executed before displaying the splash page menu
        do_action('wpestate_before_splash_page_menu');
        include( locate_template( 'templates/splash-page/splash-page-menu.php') );
        // Action: Executed after displaying the splash page menu
        do_action('wpestate_after_splash_page_menu');
    }
    ?>
</div>

<?php
// Action: Executed after the main header media content
// Usage: add_action('wpestate_after_header_media_content', function() { echo '</div>'; });
do_action('wpestate_after_header_media_content');

// show advanced search after media header 
if ($search_on_start === 'no' && !$is_splash_page  ) {
   
    $use_float_search = wpresidence_get_option('wp_estate_use_float_search_form', '') === 'yes';  
   
    if ( !$use_float_search ) {

        // Action: Executed before displaying advanced search after header media
        do_action('wpestate_before_advanced_search');
        wpestate_show_advanced_search($postID);
        // Action: Executed after displaying advanced search after header media
        do_action('wpestate_after_advanced_search');
    }
}

// show mobile search
$show_mobile_search = !wpestate_half_map_conditions('') && 
                      !wpestate_is_user_dashboard() && 
                      !is_page_template('page-templates/splash_page.php') &&
                      wp_is_mobile();

if ($show_mobile_search) {
    // Action: Executed before displaying mobile search
    do_action('wpestate_before_mobile_search');
    include(locate_template('templates/advanced_search/adv_search_mobile.php'));
    // Action: Executed after displaying mobile search
    do_action('wpestate_after_mobile_search');
}
?>
