<?php
global $post;
global $adv_search_type;
$adv_search_what            =   wpresidence_get_option('wp_estate_adv_search_what','');
$show_adv_search_visible    =   wpresidence_get_option('wp_estate_show_adv_search_visible','');
$close_class                =   '';
$allowed_html               =   array();
if($show_adv_search_visible=='no'){
    $close_class='adv-search-1-close';
}

$extended_search    =   wpresidence_get_option('wp_estate_show_adv_search_extended','');
$extended_class     =   '';
if ($adv_search_type==2){
    $extended_class='adv_extended_class2';
}

if ( $extended_search =='yes' ){
    $extended_class='adv_extended_class';
    if($show_adv_search_visible=='no'){
        $close_class='adv-search-1-close-extended';
    }
}
?>

<div class="adv-search-1 wpresidence-adv-search-11 <?php echo esc_html($close_class.' '.$extended_class);?>" id="adv-search-1" >

    <form role="search" method="get" class="row gx-2"    action="<?php print esc_url($adv_submit); ?>" >
        <?php
        if (function_exists('icl_translate') ){
            print do_action( 'wpml_add_language_form_field' );
        }
        ?>

        <div class="col-md-6">
            <input type="text" id="keyword_search" class="form-control" name="keyword_search"  placeholder="<?php esc_html_e('Type Keyword','wpresidence');?>" value="<?php

            if(isset($_GET['keyword_search'])){
                echo esc_attr( wp_kses($_GET['keyword_search'], $allowed_html) );
            }
            ?>">
        </div>

        <?php
       if( isset($_GET['property_category'][0]) && $_GET['property_category'][0]!=''&& $_GET['property_category'][0]!='all'  ){
            $full_name = get_term_by('slug', esc_html( wp_kses( $_GET['property_category'][0],$allowed_html) ),'property_category');
            $adv_categ_value= $adv_categ_value1=$full_name->name;
            $adv_categ_value1 = mb_strtolower ( str_replace(' ', '-', $adv_categ_value1));
        }else{
          //  $adv_categ_value    = esc_html__('Categories','wpresidence');
            $adv_categ_value=wpresidence_return_custom_dropdown_label_type10('categories');
            $adv_categ_value1   ='all';
        }

        print '<div class="col-md-2">'
            .wpestate_show_dropdown_taxonomy_v21('categories',$adv_categ_value , '').
        '</div>';

        ?>

        <?php
        if(isset($_GET['property_action_category'][0]) && $_GET['property_action_category'][0]!='' && $_GET['property_action_category'][0]!='all'){
            $full_name = get_term_by('slug', esc_html( wp_kses( $_GET['property_action_category'][0],$allowed_html) ),'property_action_category');
            $adv_actions_value=$adv_actions_value1= $full_name->name;
            $adv_actions_value1 = mb_strtolower ( str_replace(' ', '-', $adv_actions_value1) );
        }else{

            //$adv_actions_value=esc_html__('Types','wpresidence');
            $adv_actions_value=wpresidence_return_custom_dropdown_label_type10('types');
            $adv_actions_value1='all';
        }

        print'
        <div class="col-md-2">'
            .wpestate_show_dropdown_taxonomy_v21('types',$adv_actions_value , '').
        '</div>';


        ?>

        <div class="col-md-2">
            <div class="adv_handler"><i class="fas fa-sliders-h" aria-hidden="true"></i></div>
            <input name="submit" type="submit" class="wpresidence_button" id="advanced_submit_11" value="<?php esc_html_e('Search','wpresidence');?>">
        </div>

        <input type="hidden" name="is11" value="11">

        <div class="adv_search_hidden_fields row gx-2 gy-2 ">

            <?php
            $adv_search_label       =   wpresidence_get_option('wp_estate_adv_search_label','');
        
            $adv_search_fields_no_per_row   =   ( floatval( wpresidence_get_option('wp_estate_search_fields_no_per_row') ) );
           
                foreach($adv_search_what as $key=>$search_field){
                    $search_col         =   3;
                    $search_col_price   =   6;
                    if($adv_search_fields_no_per_row==2){
                        $search_col         =   6;
                        $search_col_price   =   12;
                    }else  if($adv_search_fields_no_per_row==3){
                        $search_col         =   4;
                        $search_col_price   =   8;
                    }
                    if($search_field=='property price' &&  wpresidence_get_option('wp_estate_show_slider_price','')=='yes'){
                        $search_col=$search_col_price;
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
           

            if($extended_search=='yes'){
               show_extended_search('adv');
            }
            ?>

       </div>


     <?php include( locate_template('templates/preview_template.php') ); ?>
    </form>
       <div style="clear:both;"></div>
</div>
