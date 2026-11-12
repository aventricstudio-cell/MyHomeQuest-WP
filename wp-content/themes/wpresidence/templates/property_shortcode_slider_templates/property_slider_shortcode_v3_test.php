<?php
$slides             =   '';
$property_request=array(
    'id'=>$prop_id,
    'image_size'=>'property_featured_sidebar',
);
$property_data  =   wpestate_api_return_property_fields_base($property_request);
$author_id      =   $property_data['post_data']['post_author'];
$active         =   '';


$agent_id           =   intval  ( $property_data['default-fields']['property_agent'] );
$thumb_id           =   get_post_thumbnail_id($agent_id);
$agent_face         =   wp_get_attachment_image_src($thumb_id, 'agent_picture_thumb');
$agent_face_image   = '';

if($agent_face ){
    $agent_face_image=$agent_face[0];
}


if ($agent_id==0 || ( $agent_face && $agent_face[0]=='') ){
    $agent_face[0]=$agent_face_image=get_the_author_meta( 'custom_picture',$author_id);
    if( $agent_face[0]==''){
        $agent_face[0]= $agent_face_image=get_theme_file_uri('/img/default-user_1.png');
    }
}
if($agent_face_image==''){
    $agent_face_image=get_theme_file_uri('/img/default-user_1.png');
}








?>
<div class="item <?php echo esc_attr($active);?>" data-number="<?php echo esc_attr($counter);?>">
        
        <div class="property_slider_carousel_elementor_v3_image_wrapper">
        
            <div class="tag-wrapper">
                <?php
                    if($property_data['default-fields']['prop_featured']==1){
                        print '<div class="featured_div">'.esc_html__('Featured','wpresidence').'</div>';
                    }
                ?>      
                
                <div class="status-wrapper">
                    <?php
                         $property_action            =   get_the_terms($prop_id, 'property_action_category');  
                        if(isset($property_action[0])){
                            $property_action_term = $property_action[0]->name;
                            print '<div class="action_tag_wrapper '.esc_attr($property_action_term).' ">'.wp_kses_post($property_action_term).'</div>';
                        }                      
                        print wpestate_return_property_status($prop_id,'unit');      
                    ?> 
                </div>
            </div>


            <div class="places_cover"></div>
            
            <div class="property_slider_carousel_elementor_v3_image_container" style="background-image:url('<?php echo esc_url($property_data['media']['thumb']);?>')">
            </div> 
        </div>

        <div class="property_slider_carousel_elementor_v3_content_wrapper">
            <div class="property_slider_carousel_elementor_v3_price">
                <?php echo trim($property_data['default-fields']['property_price_formated']);?>
            </div>
            
            <a href="<?php echo esc_url($property_data['post_data']['permalink']);?>" class="property_slider_carousel_elementor_v3_title">
                <?php echo esc_html($property_data['post_data']['post_title']);?>
            </a>
          
            <div class="property_slider_carousel_elementor_v3_address">
                <?php echo esc_html($property_data['default-fields']['property_address']);?>
            </div>


            <div class="property_slider_carousel_elementor_v3_excerpt">   
                <?php 
                    echo  wpestate_strip_excerpt_by_char(get_the_excerpt(),210,$prop_id);
                ?>
            </div>


            <div class="property_listing_details">
 
                <?php if($property_data['default-fields']['property_bedrooms']!='' &&  $property_data['default-fields']['property_bedrooms']!=0){
                    print '<span class="inforoom">';
                    include (locate_template('templates/svg_icons/'));
         
                    print esc_html($property_data['default-fields']['property_bedrooms']).'</span>';
                }

                if($property_data['default-fields']['property_bathrooms']!='' && $property_data['default-fields']['property_bathrooms']!=0){
                    print '<span class="infobath">';
                    include (locate_template('templates/svg_icons/single_bath.svg'));
                    print  esc_html($property_data['default-fields']['property_bathrooms']).'</span>';
                }

                if($property_data['default-fields']['property_size']!=''){
                    print ' <span class="infosize">';
                    include (locate_template('templates/svg_icons/single_floor_plan.svg'));
                    print trim( $property_data['default-fields']['property_size'] ).'</span>';
                }

                echo '<a href="'.esc_url($property_data['post_data']['permalink']).'" target="'.esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page','')).'"  class="unit_details_x">'.esc_html__('details','wpresidence').'</a>';
                ?>
            </div>











            <div class="property_agent_wrapper">
                <div class="property_agent_image" style="background-image:url('<?php print esc_attr($agent_face_image); ?>')"></div> 
                <div class="property_agent_image_sign"><i class="far fa-user-circle"></i></div>
                    <?php if($agent_id!=0){
                            echo '<a href="'.esc_url( get_permalink($agent_id) ) .'">'.get_the_title($agent_id).'</a>';
                        }else{
                            echo get_the_author_meta( 'first_name',$author_id).' '.get_the_author_meta( 'last_name',$author_id);
                        }
                    ?>
                </div>

        </div>
        
   
    
</div>
   

