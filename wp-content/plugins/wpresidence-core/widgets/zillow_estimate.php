<?php

class Zillow_Estimate_Widget extends WP_Widget {	
	function __construct(){
        //function Zillow_Estimate_Widget()	{
		$widget_ops = array('classname' => 'zillow_widget boxed_widget', 'description' => 'estimate your property');
		$control_ops = array('id_base' => 'zillow_estimate_widget');
		//$this->WP_Widget('zillow_estimate_widget', 'Wp Estate Zillow Estimate Widget', $widget_ops, $control_ops);
                parent::__construct('zillow_estimate_widget', 'Wp Estate Zillow Estimate Widget', $widget_ops, $control_ops);
	}

	function form($instance)
	{
		$defaults = array('title' => 'Estimate your home');
		$instance = wp_parse_args((array) $instance, $defaults);
		$display='
                <p><label for="'.$this->get_field_id('title').'">Title:</label></p>
                <p><input id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.$instance['title'].'" /></p>
                ';
		print $display;
	}


	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['zillow_api_key'] = $new_instance['zillow_api_key'];
		return $instance;
	}


	function widget($args, $instance)
	{       
                $display='';
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);

		print $before_widget;
		if($title) {
			print $before_title.$title.$after_title;
		}
		
          $zillow_api_key             =   esc_html ( wpresidence_get_option('wp_estate_zillow_api_key','') );
    

              
                    if( $zillow_api_key!=''){
                        
                       print '
                        <div class="zillow-wrapper">    
                    
                       
                        <div class="zill_estimate_adr1-wrapper">    
                            <input type="text" class="form-control" id="zill_estimate_adr1"   name="zill_estimate_adr"    placeholder="'.esc_html__('Your Address','wpresidence-core').'">
                        </div>
                        
                        <div class="zill_estimate_city1-wrapper">
                            <input type="text" class="form-control" id="zill_estimate_city1"  name="zill_estimate_city"   placeholder="'.esc_html__('Your City','wpresidence-core').'">
                        </div>
                        
                        <div class="zill_estimate_state1-wrapper">                       
                            <input type="text" class="form-control" id="zill_estimate_state1" name="zill_estimate_state"  placeholder="'.esc_html__('Your State Code (ex CA)','wpresidence-core').'">
                        </div>
                        
                        <button class="wpresidence_button" id="zill_submit_estimate">'.esc_html__('Get Estimation','wpresidence-core').'</button>
                   
                        
                        <div class="wpestate_zillow_answer"></div>


                        </div>
                        ';     
                        $ajax_nonce = wp_create_nonce( "wpestate_zillow_nonce" );
                        print '<input type="hidden" id="wpestate_zillow_nonce" value="'.esc_html($ajax_nonce).'" />    ';       
                
                    }
                    else{
                        $display.='<p>Please add Zillow Api Key in Theme Options </p>';
                    }
                
                print $display;
		print $after_widget;
	}

}

?>