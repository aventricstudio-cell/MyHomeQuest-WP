<!DOCTYPE html>
<html <?php language_attributes(); ?>   dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>" >
<head>

    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

    <?php
    if( !has_site_icon() ){
        print '<link rel="shortcut icon" href="'.get_theme_file_uri('/img/favicon.gif').'" type="image/x-icon" />';
    }
    wp_head();
    ?>
</head>

<body <?php body_class(); ?>>
<?php
// This section ensures compatibility with WordPress 5.2+ body open hook.
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
}
global $wpestate_studio;
// These lines determine the header type and classes.
// Notice how theme options are retrieved and used to set up the page structure.
$logo_header_type   =   wpresidence_get_option('wp_estate_logo_header_type','');
$header_classes     =   wpestate_header_classes();


// This condition adjusts the header type for the user dashboard or half map.
if(  $logo_header_type=='type3' ||  $logo_header_type=='type4' ) {
  if( wpestate_is_user_dashboard() || wpestate_half_map_conditions($post->ID) ){
    $logo_header_type='type1';
  }
}




// Apply the filter to insert code before the mobile menu
do_action('wpresidence_before_mobile_menu', '');

// Load the mobile menu template part
get_template_part('templates/menus/mobile_menu');

// Apply the filter to insert code after the mobile menu
do_action('wpresidence_after_mobile_menu', '');
?>


<div class="website-wrapper wpresidence_wrapper_for_header_<?php echo esc_attr($logo_header_type).' '.esc_attr( $header_classes['wide_class'] ); ?>" id="all_wrapper" >

  <?php  
  // Special handling for header type 4.
  // This is an example of conditional template inclusion.
  if (  $logo_header_type=='type4'){
        // Apply the filter to insert code before header 4 sidebar section
        do_action('wpresidence_before_header4_sidebar_section', '');
        
        include(locate_template('templates/headers/header4_sidebar_section.php'));

        // Apply the filter to insert code before header 4 sidebar section
        do_action('wpresidence_after_header4_sidebar_section', '');
      ?>
      <div class="wpresidence_wrapper_for_header_4_colum">
  <?php        
  }
  ?>

  <div class="container-fluid px-0 wpresidence_main_wrapper_class <?php echo esc_attr($header_classes['main_wrapper_class']) ;?>">

    <?php
      // display the header in dasboard pages only if the option is set to yes
      if( wpestate_is_user_dashboard() &&  wpresidence_get_option('wp_estate_show_header_dashboard', '')=='no'){
        return;
      }
   
    ?>



    <?php
    // This section handles the header display, including Elementor integration.
    // Notice the checks for Elementor and custom header displays.
    $is_elementor_in_use='header_media_elementor';
    if(!wpestate_display_studio_header()){
      if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
          wpresidence_show_header_wrapper($header_classes,$logo_header_type);
          $is_elementor_in_use='header_media_non_elementor';
      }
    }else{
        $wpestate_studio->header_footer_instance->display_custom_elementor_header();
    }
  
    if (isset($wpestate_studio) && is_object($wpestate_studio)) {
        $wpestate_studio->header_footer_instance->wpestate_helper_remove();
    }
    

    // Apply the filter to insert code before header media section
    do_action('wpresidence_before_header_media_section', '');

    // This includes the header media template.
    // The template parts are included with additional parameters and if we are not on dashboard page.
    if(!wpestate_is_user_dashboard()):
      get_template_part( 'templates/header_media/header_media','', array(
            'elementor_class'   => $is_elementor_in_use,
      ) );
    endif;

    // Apply the filter to insert code before header media section
    do_action('wpresidence_after_header_media_section', '');
    ?>

 

      <?php 
      // This section handles different layouts for various page types.
      // Pay attention to the conditional checks and how they affect the page structure.
      if( ! is_singular('estate_property')){
  
          $agent_page_template        = intval ( wpresidence_get_option('wp_estate_agent_layouts','') ) ;
          if( is_singular ('estate_agent') &&   $agent_page_template === 2 ){ ?>
            <div class="wpestate_agent_header2">
                <div class="wpestate_agent_header2_breadcrumbs row">
                  <?php get_template_part('templates/breadcrumbs'); ?>
                </div>
            </div>

          <?php
          } 
        
          $wpestate_wide_elememtor_page_class='';
          if ( class_exists( '\Elementor\Plugin' ) ) {
            if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
              $wpestate_wide_elememtor_page_class=' wpresidence_elmentor_edit_page ';
              if( wpresidence_get_option('wpestate_wide_elememtor_page') === 'yes'){
                $wpestate_wide_elememtor_page_class.= " wpestate_wide_elememtor_page ";
              }  
            } 
          }
      ?>

      <main class="content_wrapper container-fluid  <?php echo esc_attr($wpestate_wide_elememtor_page_class);?>">

      <?php
      }
      ?>