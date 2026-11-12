<?php





/*

 * 
 * 
 * 
 * 
 * 
 * 
 * 
 **/    
    

function wpestate_render_elementor_search($settings,$elementor_this,$post_id){

    ob_start();
    ?>
   
    <div class="search_wrapper search_wr_elementor search_wr_elementor_shadow_<?php echo esC_attr($settings['form_field_use_tabs']);?> "> 
        <div class="search_wrapper_color"></div>
        
        <?php
        if($settings['form_field_show_section_title']=='true'){
            print ' <div class="adv-search-header">'. $settings['form_field_section_title_text'].'</div>';
        }
        ?>
       
        <div class="wpestate-adv-holder">
            <?php
                wpestate_render_elementor_search_tab_wrapper($settings,$elementor_this,$post_id);
            ?>
        </div>
        
    </div>
        
    <?php
    $return = ob_get_contents();
    ob_end_clean();
     
    return $return;

    
}


/*

 * 
 * 
 * 
 * 
 * 
 * 
 * 
 **/    
if(!function_exists('wpestate_return_taxonomy_terms_elementor')):    
    function wpestate_return_taxonomy_terms_elementor($taxonomy){

           
            $transient_key = wpestate_set_transient_name_multilang('wpestate_elementor_tax_');
        
        
            $return_array=get_transient($transient_key);
             
    
            if($return_array==false || $return_array=='' ){

                $terms = get_terms( array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false,
                    'orderby'   =>'name',
                    'order'     =>'ASC'
                ) );
                
        

                $return_array=array();
        
                if($terms){
                    
                    foreach($terms as $key=>$term){
                        
                        $return_array[$term->term_id]=$term->name;
                    }
                }
                set_transient($transient_key,$return_array,60*60*6);
            }
            return $return_array;
            
    }
endif;





/*

 * 
 * 
 * 
 * 
 * 
 * 
 * 
 **/    
    
    
function wpestate_elemnentor_get_type_data($settings){
    $possible_values    =   array('action_data','category_data','city_data','area_data','county_data','status_data');
    $type_data          =   '';
    foreach($possible_values as $type){
        if(isset($settings[$type])){
            $type_data  =   $settings[$type];
        }
    }
    return $type_data;
}

    
    
    
    
    
 
/*

 * 
 *  Reorder datbs
 * 
 * 
 * 
 * 
 * 
 **/    
       
function wpestate_reorder_tab_items( $tabs_order_by, $taxonomy_tab_array, $type_data){
    $tabs_order_by = intval($tabs_order_by);
   
    if( !is_array($type_data) ){
         $type_data=array();
    }
    
    if($tabs_order_by==1){
        asort($type_data);
        return $type_data;
    }else if($tabs_order_by==2){
        arsort($type_data);
        return $type_data;
    }else  if($tabs_order_by==3){
        
        asort($taxonomy_tab_array,SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL  ); 
        
        $ordered_array=array();
        foreach($taxonomy_tab_array as $key=>$term_name){
            if(in_array($key, $type_data)){
                $ordered_array[]=$key;
            }
        }
        return $ordered_array;
       
       
    }else  if($tabs_order_by==4){
     
    
        arsort($taxonomy_tab_array,SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL  ); 
        $ordered_array=array();
        foreach($taxonomy_tab_array as $key=>$term_name){
            if(in_array($key, $type_data)){
                $ordered_array[]=$key;
            }
        }
         return $ordered_array;
    }
  
    
    
}    


/*

 * 
 * 
 * 
 * 
 * 
 * 
 * 
 **/ 

function wpestate_render_elementor_search_tab_wrapper($settings,$elementor_this,$post_id){
    
   
    $type_data= wpestate_elemnentor_get_type_data($settings);
    
    $position   =   '_elementor_search';
    $adv_submit =   wpestate_get_template_link('page-templates/advanced_search_results.php');
    $return     =   '';
       
    $taxonomy_tab = $settings['tabs_field'];
    $term_counter   =   0;
    

    
    $taxonomy_tab_array =   wpestate_return_taxonomy_terms_elementor($taxonomy_tab);
    $type_data          =   wpestate_reorder_tab_items( $settings['tabs_order_by'], $taxonomy_tab_array, $type_data);
     

    if( is_array($type_data) && count($type_data)>1 ){
        $tab_items      =   '';
        $tab_content    =   '';
     
        $active         =   'active';
        
        if(isset($_GET['adv_search_tab_elementor']) && $_GET['adv_search_tab_elementor']!=''){
            $active         =   '';
        }
        $tmp='';

        foreach ($type_data as $key=>$term_id){
               
               
                $term_slug='';
                $term_name='';
                $term = get_term_by('id',$term_id,$taxonomy_tab);
               
              
                if($term){
                    $term_slug=$term->slug;
                    $term_name=$term->name;
                }
                $use_name=$term_slug;
                
                
                if(isset($_GET['adv_search_tab_elementor']) && $_GET['adv_search_tab_elementor']==$term_slug){
                    $active         =   'active';
                }

                $aria_selected = "aria-selected=false";
                if($active=='active'){
                    $aria_selected = "aria-selected=true";
                }
              

   

                
                $tab_items.= '<div data-aria-controls="'.urldecode($term_slug.$position).'" 
                                class="adv_search_tab_item item_underline_active_'.esc_attr($settings['wpresidence_tab_item_underline_active']).' '.esc_attr($active).' '.esc_attr($term_slug).'" data-term="'.esc_attr($term_slug).'" data-termid="'.esc_attr($tmp).'" data-tax="'.esc_attr($taxonomy_tab).'">
                                    <a href="#'.urldecode($term_slug.$position).'" 
                                        aria-controls="'.urldecode($term_slug.$position).'-pane" 
                                        role="tab" 
                                        class="elementor_search_tab_head '.esc_attr($active).'" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#'.urldecode($term_slug.$position).'-pane"
                                        '.esc_attr($aria_selected).'>'
                                            .urldecode (str_replace("-"," ", $term_name))
                                    .'</a>
                            </div>';
                
                $tab_class='';
                if(isset($_GET['adv_search_tab_elementor'])){
                    $tab_class=esc_html($_GET['adv_search_tab_elementor']);
                }
                
          
                if($active=='active'){
                 
                    $active.=' show ';
                }
 
                $tab_content.='<div role="tabpanel" 
                                    class="tab-pane  container  '.esc_attr($tab_class).' '.esc_attr($active).'" 
                                    id="'.urldecode($term_slug.$position).'-pane"
                                    aria-labelledby="'.urldecode($term_slug.$position).'"
                          
                                    >';
            
                $tab_content.='<form role="search" method="get" action="'.esc_url($adv_submit).'" >';
                
                if($taxonomy_tab=='property_category'){
                    $tab_content.='<input type="hidden" name="property_category[]" value="'.esc_html($use_name).'" >';
                }else if($taxonomy_tab=='property_action_category'){
                    $tab_content.='<input type="hidden" name="property_action_category[]" value="'.esc_html($use_name).'" >';
                }else if($taxonomy_tab=='property_city'){
                    $tab_content.='<input type="hidden" name="property_city" value="'.esc_html($use_name).'" >';
                }else if($taxonomy_tab=='property_area'){
                    $tab_content.='<input type="hidden" name="property_area" value="'.esc_html($use_name).'" >';
                }else if($taxonomy_tab=='property_county_state'){
                    $tab_content.='<input type="hidden" name="property_county_state" value="'.esc_html($use_name).'" >';
                }
                
                $tab_content.=' <input type="hidden" name="adv6_search_tab" value="'.esc_html($use_name).'">
                                <input type="hidden" name="term_id" class="term_id_class" value="'.esc_html($term_id).'">
                                <input type="hidden" name="term_counter" class="term_counter" value="'.intval($term_counter).'">';
                $tab_content.=  wpestate_search_form_render_tab($settings,$elementor_this,$term_counter,$taxonomy_tab,$term_slug,$term_id);                
                $tab_content.=  wpestate_elementor_show_button($settings);
                
                //$tab_content.=' TAB CONTNETE '.esc_attr($term_slug);
                $extended_search= wpestate_elementor_search_show_ammenities($settings,$position,$use_name);
              
                $tab_content.= $extended_search; 
                $tab_content.='<input type="hidden" name="elementor_form_id" value="'.intval($post_id).'">';
                  $tab_content.= '</div>';//end wpresidene-elenmento-help-file
                $tab_content.= '</form>';
                $active='';
                $term_counter++;
        }
           
            $return.=   '<div role="tabpanel" class="adv_search_tab" >';
            $return.=   '<div class="nav nav-tabs" role="tablist">'.$tab_items.'</div>';   //escaped above
            $return.=   '<div class="tab-content ">'.$tab_content.'</div>';//escaped above
           
          //  $return.=   '</div>';
            $return.=   '</div>';



            print trim($return);
                
    ?>

            
    <?php
    }else{
        // one or no tabs
        print   '<form role="search" method="get" action="'.esc_url($adv_submit).'" >';
        print   wpestate_search_form_render_tab($settings,$elementor_this,$term_counter,$taxonomy_tab,'','');
        print   wpestate_elementor_show_button($settings);
        print   wpestate_elementor_search_show_ammenities($settings,$position,'');
        print   '<input type="hidden" name="elementor_form_id" value="'.intval($post_id).'">';
        print   '</form>';
    }
    
    
    
}

/*
 * show search buttons
 * 
 * 
 * 
 * 
 * 
 * */



function wpestate_elementor_show_button($settings){
    $form_field_show_labels_class='';
    if($settings['form_field_show_labels']=='true'){
        $form_field_show_labels_class=" form_field_show_labels_true ";
    }
    
  
    
    $return_string=  '<div class="elemenentor_submit_wrapper elementor-field-group form-group '.esc_attr($form_field_show_labels_class).' elementor-column elementor-col-'.esc_attr($settings['submit_button_width']).'" >';
    $return_string.= '<button name="submit" type="submit" class="wpresidence_button  search_button_use_hover_effect_no'.esc_attr($settings['search_button_use_hover_effect']).'"  value="'.esc_html($settings['submit_button_text']).'">';

    
   
        ob_start();
        \Elementor\Icons_Manager::render_icon( $settings['search_icon_button'], [ 'aria-hidden' => 'true' ] ); 
        $icon = ob_get_contents();
        ob_end_clean();      
        $return_string.='<div class="elementor-icon">'.$icon.'</div>';
  
    
    $return_string.= esc_html($settings['submit_button_text']).'</button>';
    $return_string.= '</div>';
              
    return $return_string;
}




/*
 * show ammenities
 * 
 * 
 * 
 * 
 * 
 * */

function wpestate_elementor_search_show_ammenities($settings,$position,$use_name){
    $extended_search='';
    if( $settings['form_field_show_exra_details']){
        ob_start();
        print '<div class="elementor-field-group elementor-column form-group elementor-col-100">';
            show_extended_search($position,$use_name);
        print '</div>';
        $extended_search = ob_get_contents();
        ob_end_clean();
    }
    
    return $extended_search;
}

/*
 * check tab placement 
 * 
 * 
 * 
 * 
 * 
 * */

function wpestate_elementor_check_tab_placement($settings,$item,$term_id){
    if($item==$term_id){
        return true;
    }
    
    if(!$settings['form_field_use_tabs'] || $settings['form_field_use_tabs']=='false'){
        return true;
    }
    return false;
    
}

/*
 * render tab
 * 
 * 
 * 
 * 
 * 
 * */
function wpestate_search_form_render_tab($settings,$elementor_this,$term_counter,$taxonomy_tab,$term_slug,$term_id){

    $args                       =   wpestate_get_select_arguments();

    $action_select_list         =  '';
    $categ_select_list          =   '';
    $select_city_list           =   '';
    $select_area_list           =   '';
    $select_county_state_list   =   '';



    $show_slider_price          =   wpresidence_get_option('wp_estate_show_slider_price','');
    $return = '';

    foreach ($settings['form_fields'] as $key => $item) :
        
        if( wpestate_elementor_check_tab_placement($settings,$item['tab_holder'],$term_id) ){
   
           
            $elementor_this->residence_render_attributes($key, $item, $settings);
            $return.= '<div '.$elementor_this->get_render_attribute_string('field-group' . $key).'> ';

            
            if ( $item['field_label'] ) {
                if( 
                    
                    $item['field_type']!=='geolocation_radius' && 
                    ( $item['field_type']!=='property price' || ( $item['field_type']=='property price') && $show_slider_price =='no' )) {
                     
                    $return.= '<label '.$elementor_this->get_render_attribute_string('label' . $key) .'>' . $item['field_label'].'</label>';
                   
                }
            }

            ob_start();
            $price_array_data=array();
            if( $item['field_type']=='property price' || $item['field_type']=='property-price-v2'  ){
                $price_array_data['term_counter']   =   $term_counter;
                $price_array_data['taxonomy_tab']   =   $taxonomy_tab;
                $price_array_data['term_slug']      =   $term_slug;
                $price_array_data['term_id']        =   $term_id;
                $price_array_data['min_price']      =   $item['min_price'];
                $price_array_data['max_price']      =   $item['max_price'];
            }
            if(  $item['field_type']=='property-price-v3'  ){
                $price_array_data['term_counter']   =   $term_counter;
                $price_array_data['taxonomy_tab']   =   $taxonomy_tab;
                $price_array_data['term_slug']      =   $term_slug;
                $price_array_data['term_id']        =   $term_id;
                $price_array_data['min_price_values']      =   $item['min_price_values'];
                $price_array_data['max_price_values']      =   $item['max_price_values'];
            }



            if( $item['field_type']=='categories' ){
                $categ_select_list  =  '<li role="presentation" data-value="all">'. $item['placeholder'].'</li>'.  $categ_select_list ;
            }
            if( $item['field_type']=='types' ){
                $action_select_list  =  '<li role="presentation" data-value="all">'. $item['placeholder'].'</li>'.  $action_select_list ;
            }
            if( $item['field_type']=='cities' ){
                $select_city_list  =  '<li role="presentation" data-value="all">'. $item['placeholder'].'</li>'.  $select_city_list ;
            }
            if( $item['field_type']=='areas' ){
                $select_area_list  =  '<li role="presentation" data-value="all">'. $item['placeholder'].'</li>'.  $select_area_list ;
            }
             if( $item['field_type']=='county / state' ){
                $select_county_state_list  =  '<li role="presentation" data-value="all">'. $item['placeholder'].'</li>'.  $select_county_state_list ;
            }





            wpestate_show_search_field($item['placeholder'],'elementor',$item['field_type'],$action_select_list,$categ_select_list,$select_city_list,$select_area_list,$key,$select_county_state_list,$term_counter,$item['placeholder'],$item['field_label'], $item['field_how'],$price_array_data);

            $return_content_fields= ob_get_contents();
            ob_end_clean();

            $return.= $return_content_fields.'</div>';
        }
    endforeach;
    
    return $return;
}
