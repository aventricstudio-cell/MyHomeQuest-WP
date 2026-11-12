<?php
$args2                      =   wpestate_get_select_arguments();
$action_select_list         =   wpestate_get_action_select_list($args2);
$categ_select_list          =   wpestate_get_category_select_list($args2);
$select_city_list           =   wpestate_get_city_select_list($args2);
$select_area_list           =   wpestate_get_area_select_list($args2);
$select_county_state_list   =   wpestate_get_county_state_select_list($args2);

if( is_page_template('page-templates/property_list_half.php')  ){
    if(isset( $current_adv_filter_search_action[0]) && $current_adv_filter_search_action[0]!='' ){
        $_GET['property_action_category'][0]=$current_adv_filter_search_action[0];
    }
    if( isset($current_adv_filter_search_category[0]) && $current_adv_filter_search_category[0]!='' ){
        $_GET['property_category'][0]=$current_adv_filter_search_category[0];
    }
    if( isset($current_adv_filter_area[0]) && $current_adv_filter_area[0]!='' ){
        $_GET['property_area']=$current_adv_filter_area[0];
    }
    if( isset($current_adv_filter_city[0])&& $current_adv_filter_city[0]!='' ){
        $_GET['property_city']=$current_adv_filter_city[0];
    }
    if( isset($current_adv_filter_county_state[0])&& $current_adv_filter_county_state[0]!='' ){
        $_GET['property_county_state']=$current_adv_filter_county_state[0];
    }
}


$adv_submit                 =   wpestate_get_template_link('page-templates/advanced_search_results.php');
$adv_search_what            =   wpresidence_get_option('wp_estate_adv_search_what','');
$show_adv_search_visible    =   wpresidence_get_option('wp_estate_show_adv_search_visible','');
$adv_search_type            =   wpresidence_get_option('wp_estate_adv_search_type','');
$close_class                =   '';
$allowed_html               =   array();

if($show_adv_search_visible=='no'){
    $close_class='adv-search-1-close';
}

if(isset( $post->ID)){
    $post_id = $post->ID;
}else{
    $post_id = '';
}

$extended_search    =   wpresidence_get_option('wp_estate_show_adv_search_extended','');
$extended_class     =   '';

if ( $extended_search =='yes' ){
    $extended_class='adv_extended_class';
    if($show_adv_search_visible=='no'){
        $close_class='adv-search-1-close-extended';
    }
}
$adv6_taxonomy          =   wpresidence_get_option('wp_estate_adv6_taxonomy');
?>



<div class=" wpresidence_half_search_type_2 w-100 p-2 <?php print esc_attr($close_class.' '.$extended_class);?>" id="adv-search-1" data-postid="<?php print intval($post_id); ?>" data-tax="<?php print esc_attr($adv6_taxonomy);?>">

<!--    <form role="search" method="get"   action="<?php print esc_url($adv_submit); ?>" >-->
        <?php
        if (function_exists('icl_translate') ){
            print do_action( 'wpml_add_language_form_field' );
        }
        ?>

        <div class=" wpresidence_note_half_search row search_wrapper  ">

            <?php
                $type        = wpresidence_get_option('wp_estate_adv_search_type', '');
                
                // Include the appropriate search form based on the search type
                if($type<=5) $type=4;
                
                $search_template = 'templates/advanced_search/advanced_search_type' . $type . '.php';
                
                if (file_exists(get_theme_file_path($search_template))) {
                    include(get_theme_file_path($search_template));
                
                } else {
                        // Fallback to default search form if specific type doesn't exist
                        include(get_theme_file_path('templates/advanced_search/advanced_search_type1.php'));
                }
            ?>
        </div>
   
</div>
