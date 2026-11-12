<?php 
/**
 * Dashboard Payment Modal Template
 * templates\dashboard-templates\dashboard-unit-templates\per_listing_pay.php
 * Displays the payment modal for property submissions and featured upgrades.
 * Handles different payment methods including PayPal, Stripe, and Wire Transfer.
 *
 * @package WpResidence
 * @subpackage Dashboard/Payments
 * @since 1.0
 * 
 * Required globals:
 * @global object $wpestate_global_payments Payment processing object
 */


global $wpestate_global_payments;

?>

<div class="paymentmodal " id="paymodal_<?php echo intval($post_id);?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="wpestate_dashboard_content_wrapper">
          <button type="button" class="btn-close paymentmodal_close" data-bs-dismiss="modal" aria-label="Close"></button>
    

        <?php
        $post_id                    =   get_the_ID();
        $price_submission           =   floatval(wpresidence_get_option('wp_estate_price_submission', ''));
        $where_currency         =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $wpestate_currency      =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );

        $price_featured_submission  =   floatval(wpresidence_get_option('wp_estate_price_featured_submission', ''));
        if ($paid_submission_status=='per listing') {
            $enable_paypal_status= esc_html(wpresidence_get_option('wp_estate_enable_paypal', ''));
            $enable_stripe_status= esc_html(wpresidence_get_option('wp_estate_enable_stripe', ''));
            $enable_direct_status= esc_html(wpresidence_get_option('wp_estate_enable_direct_pay', ''));


            if (mb_strtolower($pay_status)!='paid') {
                print'<div class="listing_submit">'.esc_html__('Submission Fee', 'wpresidence').': ';
                
                if ($where_currency == 'before') {
                    print '<span class="submit-price">'.esc_html($wpestate_currency).'</span><span class="submit-price submit-price-no">'. esc_html($price_submission).'</span>'; 
                }else{
                    print '<span class="submit-price submit-price-no">'. esc_html($price_submission).'</span><span class="submit-price">'.esc_html($wpestate_currency).'</span>'; 
                }
                
                print'</br></div>';

                global $wpestate_global_payments;
                if ($wpestate_global_payments->is_woo=='yes') {
                    $wpestate_global_payments->show_button_pay($post_id, '', '', $price_submission, 2);
                } else {
                    $stripe_class='';
                    if ($enable_paypal_status==='yes') {
                        $stripe_class=' stripe_paypal ';
                        print ' <div class="listing_submit_normal " data-listingid="'.intval($post_id).'"></div>';
                    }

                    if ($enable_stripe_status==='yes') {
                        wpestate_show_stripe_form_per_listing($stripe_class, $post_id, $price_submission, $price_featured_submission);
                    }
                    if ($enable_direct_status=='yes') {
                        print '<div data-listing="'.intval($post_id).'" data-price-submission="'.floatval(wpresidence_get_option('wp_estate_price_submission','')).'" class="perpack">'.esc_html__('Wire Transfer', 'wpresidence').'</div>';
                    }
                }
            } else {
                if (intval(get_post_meta($post_id, 'prop_featured', true))==1) {
                    print '<span class="label label-success featured_label">'.esc_html__('Property is featured', 'wpresidence').'</span>';
                } else {
                    print'
                     <div class="listing_submit upgrade_post">
                    '.esc_html__('Featured  Fee', 'wpresidence').':
                      <span class="submit-price submit-price-total">'.esc_html($price_featured_submission).'</span>
                      <span class="submit-price">'.esc_html($wpestate_currency).'</span>';
                    print  '</div>';

                    if ($wpestate_global_payments->is_woo=='yes') {
                        $wpestate_global_payments->show_button_pay($post_id, '', '', $price_featured_submission, 3);
                    } else {
                        $stripe_class='';
                        if ($enable_paypal_status==='yes') {
                            print'<span class="listing_upgrade" data-listingid="'.intval($post_id).'"></span>';
                        }
                        if ($enable_stripe_status==='yes') {
                            wpestate_show_stripe_form_upgrade($stripe_class, $post_id, $price_submission, $price_featured_submission);
                        }
                        if ($enable_direct_status=='yes') {
                            print '<div data-listing="'.intval($post_id).'" data-isupgrade="1" data-price-submission="'.floatval(wpresidence_get_option('wp_estate_price_featured_submission','')).'" class="perpack">'.esc_html__('Upgrade to featured', 'wpresidence').'</div>';
                        }
                    }
                }
            }
        }

        ?>


  </div>
</div>
