<?php 
$banner= get_stylesheet_directory_uri().'/email_templates/images/event_not_recorded.png';
include(locate_template('templates/email_templates/structure/email_header.php'));
$site_url               =   get_site_url();
$wp_estate_email_content_background=  wpresidence_get_option('wp_estate_email_content_background', '');
if($wp_estate_email_content_background==''){
  $wp_estate_email_content_background='#ffffff';
}
?>



                <!-- section : User Welcoming -->
                <table cellpadding="0" cellspacing="0" role="presentation" width="100%">
                  <tr>
                    <td style="padding: 0px;">
                      <table cellpadding="0" cellspacing="0" role="presentation" width="100%">
                        <tr>
                          <td class="col px-sm-16 " align="left" width="352" style="background-color:<?php echo esc_html($wp_estate_email_content_background);?>; padding: 48px 24px 32px 24px!important; border-radius: 2px!important; ">

                            <h1 mc:edit='Heading' class="quicksand" style="color: #222;font-size: 24px!important; font-weight: 600!important;line-height: 30px ; margin-bottom: 7px;">
                              {subject}
                            </h1>
                          
                            <p mc:edit='Text 1' class="source wpestate_email_paragraph" style="color: #718096;font-size: 15px!important; font-weight: 300; line-height: 1.6em!important; margin-bottom: 15px;"> 
                               
                            </p>
                            <p mc:edit='Text 1' class="source wpestate_email_paragraph" style="color: #718096;font-size: 15px!important; font-weight: 300; line-height: 1.6em!important ; margin-bottom: 15px;"> 
                                {content}
                            </p>
                           


                       
                        
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
                <!-- End User Welcoming -->


                

                  <!--  ----------------------------------------------------------------------
                  Copy the Template Above
                ------------------------------------------------------------------------- -->  

               
               
 <?php include(locate_template('templates/email_templates/structure/email_footer.php')); ?>               