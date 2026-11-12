<?php
global $post;
global $adv_search_type;
$position='';
$adv_search_what            =   wpresidence_get_option('wp_estate_adv_search_what','');
$show_adv_search_visible    =   wpresidence_get_option('wp_estate_show_adv_search_visible','');
$close_class                =   '';

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
$adv6_taxonomy          =   wpresidence_get_option('wp_estate_adv6_taxonomy');
$adv6_taxonomy_terms    =   wpresidence_get_option('wp_estate_adv6_taxonomy_terms');
$adv6_max_price         =   wpresidence_get_option('wp_estate_adv6_max_price');
$adv6_min_price         =   wpresidence_get_option('wp_estate_adv6_min_price');
$allowed_html           =   array();
?>
<div class="adv-search-1 <?php echo esc_attr($close_class.' '.$extended_class);?>" id="adv-search-8" >

        <?php
        if (function_exists('icl_translate') ){
            print do_action( 'wpml_add_language_form_field' );
        }
        ?>

        <div class="adv8-holder">
            <?php
    
            $adv_search_fields_no_per_row   =   ( floatval( wpresidence_get_option('wp_estate_search_fields_no_per_row') ) );
                        print '<div role="tabpanel" class="adv_search_tab '.wpestate_search_tab_align().' advanced_search_type8_file" >';

                        $tab_items      =   '';
                        $tab_content    =   '';
                        $active         =   'active';
                        if(isset($_GET['adv6_search_tab']) && $_GET['adv6_search_tab']!=''){
                            $active         =   '';
                        }
                        if(is_array($adv6_taxonomy_terms)){
                            foreach ($adv6_taxonomy_terms as $term_id){
                                $term               =   get_term( $term_id, $adv6_taxonomy);
                                $use_name           =   sanitize_title($term->name);
                                $use_title_name     =   $term->name;


                                if(isset($_GET['adv6_search_tab']) && $_GET['adv6_search_tab']==$use_name){
                                    $active         =   'active';
                                }

                             
                                if($active=='active'){
                                    $active.=' show ';
                                    $aria_selected_tag='aria-selected=true';    
                                }else{
                                    $aria_selected_tag='aria-selected=false';  
                                }
            
                                                
                                $tab_items .= '<div 
                                    class="nav-item" 
                                    role="presentation">
                                    <button 
                                        class="nav-link adv_search_tab_item ' . esc_attr($active) . ' ' . esc_attr($use_name) . '" 
                                        id="' . urldecode($use_name . $position) . '-tab"
                                        data-bs-toggle="tab" 
                                        data-bs-target="#' . urldecode($use_name . $position) . '-pane"
                                        type="button"
                                        role="tab" 
                                        aria-controls="' . urldecode($use_name . $position) . '-pane"
                                        aria-selected="' . ($active === 'active' ? 'true' : 'false') . '"
                                        data-term="' . esc_attr($use_name) . '" 
                                        data-termid="' . esc_attr($term_id) . '" 
                                        data-tax="' . esc_attr($adv6_taxonomy) . '">
                                        ' . urldecode(str_replace("-", " ", $use_title_name)) . '
                                    </button>
                                </div>';



                                $tab_content .= '<div 
                                class="tab-pane fade '   . esc_attr($active) . '" 
                                id="' . urldecode($use_name . $position) . '-pane"
                                role="tabpanel"
                                aria-labelledby="' . urldecode($use_name . $position) . '-tab"
                                tabindex="0">';

                                $tab_content.='
                                    <form  role="search"  method="get" action="'.esc_url($adv_submit).'" ><div class="d-flex"> ' ;
                                        
                                        if($adv6_taxonomy=='property_category'){
                                            $tab_content.='<input type="hidden" class="picked_tax" name="property_category[]" value="'.esc_attr($use_name).'" >';
                                        }else if($adv6_taxonomy=='property_action_category'){
                                            $tab_content.='<input type="hidden" class="picked_tax" name="property_action_category[]" value="'.esc_attr($use_name).'" >';
                                        }else if($adv6_taxonomy=='property_city'){
                                            $tab_content.='<input type="hidden" class="picked_tax" name="property_city" value="'.esc_attr($use_name).'" >';
                                        }else if($adv6_taxonomy=='property_area'){
                                            $tab_content.='<input type="hidden" class="picked_tax" name="property_area" value="'.esc_attr($use_name).'" >';
                                        }else if($adv6_taxonomy=='property_county_state'){
                                            $tab_content.='<input type="hidden" class="picked_tax" name="property_county_state" value="'.esc_attr($use_name).'" >';
                                        }


                                        $tab_content.='<input type="hidden" name="adv6_search_tab" value="'.esc_attr($use_name).'">
                                        <input type="hidden" name="term_id" value="'.esc_html($term_id).'">';


                                        if (function_exists('icl_translate') ){
                                            $tab_content.= do_action( 'wpml_add_language_form_field' );
                                        }

                                        $tab_content.='
                                            <div class="flex-fill" >
                                                <input type="text" id="adv_location" class="form-control adv_locations_search" name="location"  placeholder="'. esc_html__('Search State, City or Area','wpresidence').'" value="">
                                            </div>

                                            <div class="d-flex flex-fill">';

                                                if($adv6_taxonomy!=='property_category'){
                                                    $label= esc_html__('Categories','wpresidence');
                                                    $tab_content.='<div class="col-md-6">
                                                        '.wpestate_show_dropdown_taxonomy_v21('categories',$label , '').'
                                                    </div>';
                                                }else if($adv6_taxonomy!=='property_action_category'){

                                                    $label= esc_html__('Types','wpresidence');
                                                    $tab_content.='<div class="col-md-6">
                                                        '.wpestate_show_dropdown_taxonomy_v21('types',$label , '').'
                                                    </div>';
                                                    
                                                }

                                                $tab_content.='
                                                <input type="hidden" name="is2" value="1">

                                                    <input name="submit" type="submit" class="wpresidence_button" id="advanced_submit_22" value="'.esc_html__('Search Properties','wpresidence').'">
                                               
                                            </div></div>';

                                        if($extended_search=='yes'){
                                            ob_start();
                                            show_extended_search('adv');
                                            $tab_content.=ob_get_contents();
                                            ob_end_clean();
                                        }

                                    $tab_content.='<input type="hidden" name="adv6_search_tab" value="'.esc_attr($use_name).'">
                                    <input type="hidden" name="term_id" value="'.esc_attr($term_id).'">';

                                    $tab_content.='</form>
                                </div>  ';
                                $active='';
                            }
                        }

                    print '<div class="nav nav-tabs" role="tablist">'.$tab_items.'</div>'; //escaped above
                    print '<div class="tab-content">'.$tab_content.'</div>'; // escpaed above
                    print'</div>';

            ?>

            <?php include( locate_template('templates/preview_template.php') ); ?>
        </div>
       <div style="clear:both;"></div>
</div>


<?php

$availableTags  =  wpestate_return_data_for_location_autocomplete();

print '<script type="text/javascript">    //<![CDATA[
    jQuery(document).ready(function(){
        var availableTagsData = ' . $availableTags . ';
        wpresidenceInitializeAutocomplete(availableTagsData,"adv_location");
    });
//]]>
</script>';



?>
