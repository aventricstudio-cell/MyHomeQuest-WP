<?php 

$show_email_footer                  =  wpresidence_get_option('wpestate_display_footer_email');
$wpestate_show_footer_email_address =  wpresidence_get_option('wpestate_show_footer_email_address');
$wp_estate_email_footer_content     =  wpresidence_get_option('wp_estate_email_footer_content');

$wp_estate_email_footer_social1     =  wpresidence_get_option('wp_estate_email_footer_social1', 'url');
$wp_estate_email_footer_social_link1=  wpresidence_get_option('wp_estate_email_footer_social_link1');
$wp_estate_email_footer_social2     =  wpresidence_get_option('wp_estate_email_footer_social2', 'url');
$wp_estate_email_footer_social_link2=  wpresidence_get_option('wp_estate_email_footer_social_link2');
$wp_estate_email_footer_social3     =  wpresidence_get_option('wp_estate_email_footer_social3', 'url');
$wp_estate_email_footer_social_link3=  wpresidence_get_option('wp_estate_email_footer_social_link3');

$wp_estate_co_address   =   esc_html ( wpresidence_get_option('wp_estate_co_address','') );  
$wp_estate_company_name =   esc_html ( wpresidence_get_option('wp_estate_company_name','') ); 

?>

    <!-- section : Bottom Section -->
              
                <!-- End Section -->
                
<!-- section : Footer Section -->

    <?php if($show_email_footer=='yes') { ?>
                <div class="wpestate_social_icons" style="padding: 25px 0px 0px 0px; text-align: center;">
           
                <?php if(trim($wp_estate_email_footer_social_link1)!='') {?>      
                  <a href="<?php echo esc_url($wp_estate_email_footer_social_link1);?>"  style="text-decoration: none;  display: inline-block; width: 36px; height: 36px; text-align: center; vertical-align: middle; line-height: 42px!important; margin: 0px 5px;" >
                    <img  src="<?php print esc_attr($wp_estate_email_footer_social1); ?>" alt="social" >
                  </a>
                <?php } ?>
                
                <?php if(trim($wp_estate_email_footer_social_link2)!='') {?>      
                  <a href="<?php echo esc_url($wp_estate_email_footer_social_link2);?>"  style="text-decoration: none;  display: inline-block; width: 36px; height: 36px; text-align: center; vertical-align: middle; line-height: 42px!important; margin: 0px 5px;" >
                    <img  src="<?php print esc_attr($wp_estate_email_footer_social2); ?>" alt="social" >
                  </a>
                <?php } ?>
                
                
                <?php if(trim($wp_estate_email_footer_social_link3)!='') {?>      
                  <a href="<?php echo esc_url($wp_estate_email_footer_social_link3);?>"  style="text-decoration: none;  display: inline-block; width: 36px; height: 36px; text-align: center; vertical-align: middle; line-height: 42px!important; margin: 0px 5px;" >
                    <img  src="<?php print esc_attr($wp_estate_email_footer_social3); ?>" alt="social" >
                  </a>
                <?php } ?>
          
                

                </div>
                <table class="wpestate_footer" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                  <tr>
                    <td class="px-sm-16" style="padding:0px;" >
                      <table cellpadding="0" cellspacing="0" role="presentation" width="100%">
                        <tr>
                          <td class="col" align="center" width="352" style="padding: 24px 24px;" >
                            
                            <?php if($wpestate_show_footer_email_address=='yes'){  ?>
                              <p mc:edit='Footer text 1' class="source" style="color: #A0AEC0;font-size: 14px; font-weight: 400; line-height: 12px;margin-bottom: 5px;">
                                <?php echo esc_html($wp_estate_company_name.' '.$wp_estate_co_address);?>
                              </p>
                            <?php } ?>
                            
                            <p mc:edit='Footer text 1' class="source" style="color: #A0AEC0;font-size: 13px; font-weight: 400; line-height: 12px;margin-bottom: 5px;" >
                              <?php 
                               echo trim($wp_estate_email_footer_content);
                              ?>
                            </p>


                <?php } ?>

                <!-- End Section -->
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <!-- End Section -->


  </body>
</html>
<br> <br>
<br><br>      