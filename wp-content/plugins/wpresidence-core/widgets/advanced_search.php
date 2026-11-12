<?php
class Advanced_Search_widget extends WP_Widget {
	function __construct(){
	//function Advanced_Search_widget(){
		$widget_ops = array('classname' => 'advanced_search_sidebar boxed_widget', 'description' => 'Advanced Search Widget');
		$control_ops = array('id_base' => 'advanced_search_widget');
		//$this->WP_Widget('advanced_search_widget', 'Wp Estate: Advanced Search', $widget_ops, $control_ops);
                parent::__construct('advanced_search_widget', 'Wp Estate: Advanced Search', $widget_ops, $control_ops);

	}

	function form($instance){
		$defaults = array('title' => 'Advanced Search' );
		$instance = wp_parse_args((array) $instance, $defaults);
		$display='
                <p>
                    <label for="'.$this->get_field_id('title').'">Title:</label>
		</p><p>
                    <input id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.$instance['title'].'" />
		</p>';
		print $display;
	}


	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];

		return $instance;
	}



	function widget($args, $instance){
		extract($args);
                $display='';
                $select_tax_action_terms='';
                $select_tax_category_terms='';

		$title = apply_filters('widget_title', $instance['title']);

		print $before_widget;

		if($title) {
                    print $before_title.$title.$after_title;
		}else{
                    print '<div class="widget-title-sidebar_blank"></div>';
                }

                $adv_submit=wpestate_get_template_link('page-templates/advanced_search_results.php');

                //  show cities or areas that are empty ?
                $args = wpestate_get_select_arguments();
                $action_select_list =   wpestate_get_action_select_list($args);
                $categ_select_list  =   wpestate_get_category_select_list($args);
                $select_city_list   =   wpestate_get_city_select_list($args);
                $select_area_list   =   wpestate_get_area_select_list($args);
                $select_county_state_list   =   wpestate_get_county_state_select_list($args);


                $adv_search_what        =   wpresidence_get_option('wp_estate_adv_search_what','');
                $adv_search_label       =   wpresidence_get_option('wp_estate_adv_search_label','');
                $adv_search_how         =   wpresidence_get_option('wp_estate_adv_search_how','');

               
                $adv_search_type            =   wpresidence_get_option('wp_estate_adv_search_type','');

                print '<form role="search" method="get"  class="d-flex flex-column flex-nowrap align-items-stretch"  action="'.$adv_submit.'" >';
                    wp_nonce_field( 'wpestate_regular_search', 'wpestate_regular_search_nonce' );
                    if ( $adv_search_type==10){
                        $adv_actions_value  =   esc_html__('Types','wpresidence-core');
                        $adv_actions_value1 =   'all';

                        print '
                            <input type="text" id="adv_location" class="form-control" name="adv_location"  placeholder="'.esc_html__('Type address, state, city or area','wpresidence-core').'" value="">
                        ';

                        print wpestate_show_dropdown_taxonomy_v21('types',$adv_actions_value , '');
                       print '<input type="hidden" name="is10" value="10">';
                    }


                    if ( $adv_search_type==11){
                        $adv_actions_value  =   esc_html__('Types','wpresidence-core');
                        $adv_actions_value1 =   'all';
                        $adv_categ_value    =   esc_html__('Categories','wpresidence-core');
                        $adv_categ_value1   =   'all';

                        print'<input type="text" id="keyword_search" class="form-control" name="keyword_search"  placeholder="'. esc_html__('Type Keyword','wpresidence-core').'" value="">';
                        print wpestate_show_dropdown_taxonomy_v21('categories',$adv_categ_value , '');
                        print wpestate_show_dropdown_taxonomy_v21('types',$adv_actions_value , '');
                        print ' <input type="hidden" name="is11" value="11">';
                    }


                
                        $this->custom_fields_widget($adv_search_what,$action_select_list,$categ_select_list,$select_city_list,$select_area_list,$adv_search_how,$adv_search_label,$select_county_state_list);
                 



                if ( $adv_search_type!=6 ){
                    $extended_search = wpresidence_get_option('wp_estate_show_adv_search_extended','');
                    if($extended_search=='yes'){
                        show_extended_search('widget');
                    }
                }


                if (function_exists('icl_translate') ){
                    print do_action( 'wpml_add_language_form_field' );
                }


                if ( $adv_search_type!=6 ){

                    print'<button class="wpresidence_button wpresidence_advanced_submit_widget">'.esc_html__('Search','wpresidence-core').'</button>';
                }
                print '</form>';
		print $after_widget;

	}








        function custom_fields_widget($adv_search_what,$action_select_list,$categ_select_list,$select_city_list,$select_area_list,$adv_search_how,$adv_search_label,$select_county_state_list){
            $adv_search_type            =   wpresidence_get_option('wp_estate_adv_search_type','');
            $adv_search_label       =   wpresidence_get_option('wp_estate_adv_search_label','');

            if ( $adv_search_type==6 ){
                $adv_submit=wpestate_get_template_link('page-templates/advanced_search_results.php');
                print  wpestate_show_advanced_search_tabs($adv_submit,'sidebar');
                return;
            }
          

   

            if ( $adv_search_type==7 || $adv_search_type==8 || $adv_search_type==9 ){
                    $adv6_taxonomy          =   wpresidence_get_option('wp_estate_adv6_taxonomy');

                    if ($adv6_taxonomy=='property_category'){
                        $search_field="categories";
                    }else if ($adv6_taxonomy=='property_action_category'){
                        $search_field="types";
                    }else if ($adv6_taxonomy=='property_city'){
                        $search_field="cities";
                    }else if ($adv6_taxonomy=='property_area'){
                        $search_field="areas";
                    }else if ($adv6_taxonomy=='property_county_state'){
                        $search_field="county / state";
                    }

                wpestate_show_search_field_tab_inject('','sidebar',$search_field,$action_select_list,$categ_select_list,$select_city_list,$select_area_list,'',$select_county_state_list);
            }




            foreach($adv_search_what as $key=>$search_field){

                if($search_field=='property-price-v2'){
                    $price_array_data='';
                }else{
                    $price_array_data=array();
                    $price_array_data['min_price_values']      =   wpresidence_get_option('wp_estate_min_price_dropdown_values','');
                    $price_array_data['max_price_values']      =    wpresidence_get_option('wp_estate_max_price_dropdown_values','');
                }

                wpestate_show_search_field($adv_search_label[$key],'sidebar',$search_field,$action_select_list,$categ_select_list,$select_city_list,$select_area_list,$key,$select_county_state_list,'','','','',$price_array_data);
            }
        }//end custom fields function



}// end class
?>
