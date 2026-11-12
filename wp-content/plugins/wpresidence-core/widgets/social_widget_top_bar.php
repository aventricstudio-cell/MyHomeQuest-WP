<?php
class Social_widget_top extends WP_Widget {
        function __construct(){
		$widget_ops = array('classname' => 'social_sidebar', 'description' => 'Wp Estate: Social Links for Header / Footer');
		$control_ops = array('id_base' => 'social_widget_top');
		parent::__construct('Social_widget_top', 'Wp Estate:  Social Links for Header / Footer', $widget_ops, $control_ops);
	}
	
 function form($instance){
		$defaults = array(  
                                    'facebook'      => esc_html__('Facebook Link:','wpresidence_core'),
                                    'whatsup'       => esc_html__('WhatsApp Link:','wpresidence_core'),
                                    'telegram'      => esc_html__('Telegram Link:','wpresidence_core'),
                                    'tiktok'        => esc_html__('TikTok Link:','wpresidence_core'),
                                    'rss'           => esc_html__('Rss Link:','wpresidence_core'),
                                    'twitter'       => esc_html__('x - Twitter Link:','wpresidence_core'),
                                    'dribbble'      => esc_html__('Dribble Link:','wpresidence_core'),
                                    'google'        => esc_html__('Google+ Link:','wpresidence_core'),
                                    'linkedIn'      => esc_html__('LinkedIn Link:','wpresidence_core'),                            
                                    'tumblr'        => esc_html__('Tumblr Link:','wpresidence_core'),
                                    'pinterest'     => esc_html__('Pinterest Link:','wpresidence_core'),                               
                                    'youtube'       => esc_html__('Youtube Link:','wpresidence_core'),
                                    'vimeo'         => esc_html__('Vimeo Link:','wpresidence_core'),
                                    'instagram'     => esc_html__('Instagram Link:','wpresidence_core'),
                                    'foursquare'    => esc_html__('Foursquare Link:','wpresidence_core'),                  
                                    'line'          => esc_html__('Line Link:','wpresidence_core'),
                                    'wechat'        => esc_html__('WeChat Link:','wpresidence_core'),
                                    );
		
                
                $display='';
                foreach($defaults as $key=>$value):
                    $display.='<p><label for="'.$this->get_field_id($key).'">'.$value.'</label></p>
                        <p><input id="'.$this->get_field_id($key).'" name="'.$this->get_field_name($key).'" value="';
                        
                        if( isset($instance[$key]) ) {                        
                            $display.= $instance[$key];
                        }
                        
                        $display.='" /></p>';
                endforeach;
                
                
               
		print $display;
		
	}

	function update($new_instance, $old_instance){
                $instance = $old_instance;
		
		$instance['rss']        = $new_instance['rss'];
		$instance['facebook']   = $new_instance['facebook'];
                $instance['whatsup']    = $new_instance['whatsup'];
                $instance['telegram']   = $new_instance['telegram'];
                $instance['tiktok']     = $new_instance['tiktok'];
		$instance['twitter']    = $new_instance['twitter'];	
		$instance['dribbble']   = $new_instance['dribbble'];
		$instance['google']     = $new_instance['google'];
		$instance['linkedIn']   = $new_instance['linkedIn'];
		$instance['tumblr']     = $new_instance['tumblr'];
		$instance['pinterest']  = $new_instance['pinterest'];
		$instance['youtube']    = $new_instance['youtube'];
		$instance['vimeo']      = $new_instance['vimeo'];
                $instance['instagram']  = $new_instance['instagram'];
		$instance['foursquare'] = $new_instance['foursquare'];       
                $instance['line']       = $new_instance['line'];
                $instance['wechat']     = $new_instance['wechat'];
                
		return $instance;
	}


        
        function widget($args, $instance){
		extract($args);
	         
		print $before_widget;
		print wpestate_get_social_icons_widgets_elementor($instance);
		
                print $after_widget;
	}
}


?>