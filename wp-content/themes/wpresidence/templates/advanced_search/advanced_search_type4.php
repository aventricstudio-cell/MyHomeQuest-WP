<?php

$adv_search_what            =   wpresidence_get_option('wp_estate_adv_search_what','');
$show_adv_search_visible    =   wpresidence_get_option('wp_estate_show_adv_search_visible','');
$close_class                =   '';

if($show_adv_search_visible=='no'){
    $close_class='adv-search-1-close';
}

$extended_search    =   wpresidence_get_option('wp_estate_show_adv_search_extended','');
$extended_class     =   '';

if ( $extended_search =='yes' ){
    $extended_class='adv_extended_class';
    if($show_adv_search_visible=='no'){
        $close_class='adv-search-1-close-extended';
    }

}
?>

<div class="adv-search-1 container" id="adv-search-1" >
    <div class="adv-search-header-1"> <?php esc_html_e('Advanced Search ','wpresidence');?></div>
    <form role="search" method="get"   action="<?php print esc_url($adv_submit); ?>" >
        <?php
        if (function_exists('icl_translate') ){
            print do_action( 'wpml_add_language_form_field' );
        }
        ?>

        <div class="adv4-holder row  gx-2 gy-2">
            <?php
       
          
            $adv_search_fields_no_per_row   =   ( floatval( wpresidence_get_option('wp_estate_search_fields_no_per_row') ) );
            
      
            
            $adv_search_label       =   wpresidence_get_option('wp_estate_adv_search_label','');
      
                foreach($adv_search_what as $key=>$search_field){
                 
                    // Determine correct column width for this field
                    $search_col =$search_col_submit=  wpresidence_get_search_form_col($search_field, $adv_search_fields_no_per_row);

                     if (
                        $search_field == 'property price' &&
                        wpresidence_get_option('wp_estate_show_slider_price','') == 'yes'
                    ) {
                        if(is_numeric( $search_col_submit )){
                            $search_col_submit=$search_col_submit/2;
                        }
                    }


                    if($search_field=='property-price-v2'){
                        $price_array_data='';
                    }else{
                        $price_array_data=array();
                        $price_array_data['min_price_values']      =   wpresidence_get_option('wp_estate_min_price_dropdown_values','');
                        $price_array_data['max_price_values']      =    wpresidence_get_option('wp_estate_max_price_dropdown_values','');
                    }

                    if($search_field!=='none'){
                        print '<div class="col-md-'.esc_attr($search_col).' '.str_replace(" ","_",$search_field).'">';
                            wpestate_show_search_field($adv_search_label[$key],'mainform',$search_field,$action_select_list,$categ_select_list,$select_city_list,$select_area_list,$key,$select_county_state_list,'','','','',$price_array_data);
                        print '</div>';
                    }

                }


                print '<div class="col-md-'.esc_attr($search_col_submit).' '.str_replace(" ","_",$search_field).'">';
                print '<input name="submit" type="submit" class="wpresidence_button advanced_submit_4"  value="'.esc_html__('Search Properties','wpresidence').'">';
                print '</div>';
          

            if($extended_search=='yes'){
               show_extended_search('adv');
            }
            ?>
        </div>


          <?php include( locate_template('templates/preview_template.php') ); ?>

    </form>
    <div style="clear:both;"></div>
</div>
