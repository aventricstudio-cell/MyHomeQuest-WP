<?php
class Social_widget extends WP_Widget {
        function __construct(){
	//function Social_widget(){
		$widget_ops = array('classname' => 'social_sidebar', 'description' => 'Wp Estate: Social Links for Sidebar.');
		$control_ops = array('id_base' => 'social_widget');
		//$this->WP_Widget('social_widget', 'Wp Estate: Social Links', $widget_ops, $control_ops);
		parent::__construct('social_widget', 'Wp Estate: Social Links for Sidebar', $widget_ops, $control_ops);
	}
	
        function form($instance){
		$defaults = array(  'title'         => esc_html__('Social Links:','wpresidence_core'),
                                    'facebook'      => esc_html__('Facebook Link:','wpresidence_core'),
                                    'whatsup'       => esc_html__('WhatsUp Link:','wpresidence_core'),
                                    'telegram'      => esc_html__('Telegram Link:','wpresidence_core'),
                                    'tiktok'        => esc_html__('TikTok Link:','wpresidence_core'),
                                    'rss'           => esc_html__('Rss Link:','wpresidence_core'),
                                    'twitter'       => esc_html__('X - Twitter Link:','wpresidence_core'),
                                    'dribbble'      => esc_html__('Dribble Link:','wpresidence_core'),
                                    'google'        => esc_html__('Google+ Link:','wpresidence_core'),
                                    'linkedIn'      => esc_html__('Linkdin Link:','wpresidence_core'),
                                    'tumblr'        => esc_html__('Tumblr Link:','wpresidence_core'),
                                    'pinterest'     => esc_html__('Pinterest Link:','wpresidence_core'),
                                    'yahoo'         => esc_html__('Yahoo Link:','wpresidence_core'),
                                    'youtube'       => esc_html__('Youtube Link:','wpresidence_core'),
                                    'vimeo'         => esc_html__('Vimeo Link:','wpresidence_core'),
                                    'instagram'     => esc_html__('Instagram Link:','wpresidence_core'),
                                    'foursquare'    => esc_html__('FourthSquare Link:','wpresidence_core'),
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
		$instance['title']      = $new_instance['title'];
		$instance['rss']        = $new_instance['rss'];
		$instance['facebook']   = $new_instance['facebook'];
                $instance['whatsup']    = $new_instance['whatsup'];
                $instance['telegram']   = $new_instance['telegram'];
                $instance['tiktok']     = $new_instance['tiktok'];
		$instance['twitter']    = $new_instance['twitter'];
		$instance['email']      = $new_instance['email'];
		$instance['dribbble']   = $new_instance['dribbble'];
		$instance['google']     = $new_instance['google'];
		$instance['linkedIn']   = $new_instance['linkedIn'];
		$instance['phone_no']   = $new_instance['phone_no'];
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
		$title = apply_filters('widget_title', $instance['title']);
                
                $defaults = array( 
                    'facebook'      => '<i class="fab fa-facebook-f"></i>',
                    'whatsup'       => '<i class="fab fa-whatsapp"></i>',
                    'telegram'      => '<i class="fab fa-telegram-plane"></i>',
                    'tiktok'        => '<i class="fab fa-tiktok"></i>',
                    'rss'           => '<i class="fas fa-rss fa-fw"></i>',
                    'twitter'       => '<i class="fa-brands fa-x-twitter"></i>',
                    'dribbble'      => '<i class="fab fa-dribbble  fa-fw"></i>',
                    'google'        => '<i class="fab fa-google"></i>',
                    'linkedIn'      => '<i class="fab fa-linkedin-in"></i>',
                    'tumblr'        => '<i class="fab fa-tumblr  fa-fw"></i>',
                    'pinterest'     => '<i class="fab fa-pinterest-p  fa-fw"></i>',
                    'youtube'       => '<i class="fab fa-youtube  fa-fw"></i>',
                    'vimeo'         => '<i class="fab fa-vimeo-v  fa-fw"></i>',
                    'instagram'     => '<i class="fab fa-instagram  fa-fw"></i>',
                    'foursquare'    => '<i class="fab  fa-foursquare  fa-fw"></i>',
                    'line'          => '<i class="fab fa-line"></i>',
                
                    'wechat'        => '<i class="fab fa-weixin"></i>',
                );
                
                
                $display='';
		print $before_widget;

		if($title) {
			print $before_title.$title.$after_title;
		}
		$display.='<div class="social_sidebar_internal">';
		
                foreach ($defaults as $key=>$value):
                    if(isset($instance[$key]) && $instance[$key]){
			$display.='<a href="'.esc_url($instance[$key]).'" target="_blank" aria-label="'.esc_html($key).'" >'.trim($value).'</a>';
                    }
                endforeach;

		$display.='</div>';
		print $display;
		print $after_widget;
	}
}
















?>