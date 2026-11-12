 <?php

// Retrieve the secondary agents for the current property
if(isset($post->ID)){
    $postID=$post->ID;
}


$agents_secondary   =   get_post_meta($postID, 'property_agent_secondary', true);

if( is_array($agents_secondary) && !empty($agents_secondary) && $agents_secondary[0]!=''  ){


    $agents_sec_list = implode(',',$agents_secondary);
    $args_other_agents = array(
        'post_type'         => 'estate_agent',
        'posts_per_page'    => -1 ,
        'post__in'         =>  $agents_secondary
        );

    
    $agent_selection = new WP_Query($args_other_agents);
 
                            
    while ($agent_selection->have_posts()): $agent_selection->the_post();
        $agent_id   =   get_the_ID();
        $realtor_details    = wpestate_return_agent_details('',$agent_id);


        print '<div class="agent_unit_widget_sidebar_wrapper_unit">';
        print   '<div class="agent_unit_widget_sidebar" style="background-image: url('. esc_url($realtor_details['agent_face_img']).')"></div>';
        
        print ' <div class="agent_unit_widget_sidebar_details_wrapper">
                    <h4> <a href="'.esc_url($realtor_details['link']).'">'.esc_html($realtor_details['realtor_name']).'</a></h4>
                    <div class="agent_position">'.esc_html($realtor_details['realtor_position']).'</div>
                </div>';
        print '</div>';
    endwhile;

        

    
    wp_reset_postdata();
    wp_reset_query();
}        
?>