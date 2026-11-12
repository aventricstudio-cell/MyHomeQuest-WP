<?php 
$contact_single_class='';
if(wpresidence_get_option('wp_estate_show_back_to_top','') =='yes'){ ?>
    <a href="#" class="backtop"  aria-label="up" ><i class="fas fa-chevron-up"></i></a>
<?php }else{
    $contact_single_class='wpsestate_is_contact_single';
}

if(wpresidence_get_option('wp_estate_show_footer_contact','') =='yes'){ ?>
    <a href="#" class="contact-box <?php echo esc_attr($contact_single_class);?>"  aria-label="contact" ><i class="fas fa-envelope"></i></a>
<?php } ?>


 
<div class="contactformwrapper <?php echo esc_attr($contact_single_class);?> hidden"> 

        <div id="footer-contact-form">
        <div class="contact_close_button">
            <i class="fas fa-times" aria-hidden="true"></i>
        </div>
        <h4><?php esc_html_e('Contact Us','wpresidence')?></h4>
        <p><?php esc_html_e('Use the form below to contact us!','wpresidence');?></p>
        <div class="alert-box error">
            <div class="alert-message" id="footer_alert-agent-contact"></div>
        </div> 

        
        <input type="text" placeholder="<?php esc_html_e('Name','wpresidence');?>" required="required"   id="foot_contact_name"  name="contact_name" class="form-control" value="" tabindex="373"> 
        <input type="email" required="required" placeholder="<?php esc_html_e('Email','wpresidence')?>"  id="foot_contact_email" name="contact_email" class="form-control" value="" tabindex="374">
        <input type="email" required="required" placeholder="<?php esc_html_e('Phone','wpresidence')?>"  id="foot_contact_phone" name="contact_phone" class="form-control" value="" tabindex="374">
        <textarea placeholder="<?php esc_html_e('Type your message...','wpresidence')?>" required="required" id="foot_contact_content" name="contact_content" class="form-control" tabindex="375"></textarea>
        <input type="hidden" name="contact_ajax_nonce" id="agent_property_ajax_nonce"  value="<?php echo wp_create_nonce('ajax-property-contact');?>" />

        <?php print wpestate_check_gdpr_case('footer'); ?>
        <div class="btn-cont">
            <button type="submit" id="btn-cont-submit" class="wpresidence_button"><?php esc_html_e('Send','wpresidence');?></button>
         
            <input type="hidden" value="" name="contact_to">
            <div class="bottom-arrow"></div>
        </div>  
    </div>
    
</div>
