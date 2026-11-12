<?php 
/**
 * WP Estate Membership Package Administration Functions
 *
 * This file contains functions for creating, displaying, and managing 
 * membership packages in the WP Estate real estate platform. It handles
 * the admin interface for package creation and the user-facing display
 * of available packages.
 *
 * Functions include:
 * - Adding metaboxes for package details in admin
 * - Rendering package option fields in admin
 * - Displaying available packages to users
 *
 * @package WP Estate
 * @subpackage Membership
 * @since 1.0.0
 */

/////////////////////////////////////////////////////////////////////////////////////
// custom options for property
/////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_add_pack_metaboxes') ):
    /**
     * Registers the metabox for membership package details
     *
     * Adds a custom metabox to the membership_package post type
     * that allows administrators to configure package settings.
     *
     * @return void
     */
    function wpestate_add_pack_metaboxes() {
      add_meta_box(
          'Forestate_membership-sectionid',
          esc_html__( 'Package Details', 'wpresidence-core' ),
          'membership_package',
          'membership_package',
          'normal',
          'default'
        );
    }
    endif; // end   wpestate_add_pack_metaboxes
    
    
    if( !function_exists('membership_package') ):
    /**
     * Renders the membership package metabox content
     *
     * Creates the form fields for configuring a membership package,
     * including billing period, frequency, listing allowances, pricing,
     * and visibility settings.
     *
     * @param WP_Post $post The current post object (membership package)
     * @return void Outputs HTML directly
     */
    function membership_package( $post ) {
        wp_nonce_field( plugin_basename( __FILE__ ), 'estate_pack_noncename' );
        global $post;

        $unlimited_days  = esc_html( get_post_meta( $post->ID, 'mem_days_unl', true ) );
        $unlimited_lists = esc_html( get_post_meta( $post->ID, 'mem_list_unl', true ) );
        $billing_periods = array( 'Day', 'Week', 'Month', 'Year' );

        $billng_saved   = esc_html( get_post_meta( $post->ID, 'biling_period', true ) );
        $billing_select = '<select name="biling_period" id="billing_period">';
        foreach ( $billing_periods as $period ) {
            $billing_select .= '<option value="' . $period . '" ';
            if ( $billng_saved == $period ) {
                $billing_select .= ' selected="selected" ';
            }
            $billing_select .= '>' . $period . '</option>';
        }
        $billing_select .= '</select>';

        $check_unlimited_lists = '';
        if ( $unlimited_lists == 1 ) {
            $check_unlimited_lists = ' checked="checked"  ';
        }

        $visible_array  = array( 'yes', 'no' );
        $visible_saved  = get_post_meta( $post->ID, 'pack_visible', true );
        $visible_select = '<select id="pack_visible" name="pack_visible">';
        foreach ( $visible_array as $option ) {
            $visible_select .= '<option value="' . $option . '" ';
            if ( $visible_saved == $option ) {
                $visible_select .= ' selected="selected" ';
            }
            $visible_select .= '>' . $option . '</option>';
        }
        $visible_select .= '</select>';

        $visible_pack_array  = wpresidence_rolemap();
        $visible_pack_saved  = get_post_meta( $post->ID, 'pack_visible_user_role', true );
        $visible_pack_select = '<select id="pack_visible_user_role" name="pack_visible_user_role[]" multiple="multiple">';
        foreach ( $visible_pack_array as $role => $option ) {
            $visible_pack_select .= '<option value="' . $role . '" ';
            if ( is_array( $visible_pack_saved ) && in_array( $role, $visible_pack_saved ) ) {
                $visible_pack_select .= ' selected="selected" ';
            }
            $visible_pack_select .= '>' . $option . '</option>';
        }
        $visible_pack_select .= '</select>';

        $available_tabs = array( 'pack_price_period', 'pack_listings', 'pack_display' );
        $active_tab     = 'pack_price_period';
        if ( isset( $_GET['membership_tab'] ) && in_array( $_GET['membership_tab'], $available_tabs, true ) ) {
            $active_tab = sanitize_key( $_GET['membership_tab'] );
        }

        print '<div class="property_options_wrapper meta-options">'
            .'<div class="property_options_wrapper_list">';
                print '<div class="property_tab_item'.( $active_tab === 'pack_price_period' ? ' active_tab' : '' ).'" data-content="pack_price_period">'.esc_html__('Billing Price and Period','wpresidence-core').'</div>';
                print '<div class="property_tab_item'.( $active_tab === 'pack_listings' ? ' active_tab' : '' ).'" data-content="pack_listings">'.esc_html__('Listings Included','wpresidence-core').'</div>';
                print '<div class="property_tab_item'.( $active_tab === 'pack_display' ? ' active_tab' : '' ).'" data-content="pack_display">'.esc_html__('Display','wpresidence-core').'</div>';
        print '</div><div class="property_options_content_wrapper">';

                print '<div class="property_tab_item_content'.( $active_tab === 'pack_price_period' ? ' active_tab' : '' ).'" id="pack_price_period">';
                print '    <div class="property_prop_half">
                            <label for="pack_price">'.esc_html__('Package Price in ','wpresidence-core').' '.wpresidence_get_option('wp_estate_submission_curency').'</label><br />
                            <input type="text" id="pack_price" name="pack_price" value="'.esc_html( get_post_meta($post->ID,'pack_price',true) ).'">
                        </div>';
                print '    <div class="property_prop_half">
                            <label for="biling_period">'.esc_html__('Billing Period:','wpresidence-core').'</label><br />
                            '.$billing_select.'
                        </div>';
                print '    <div class="property_prop_half">
                            <label for="billing_freq">'.esc_html__('Billing Frequency','wpresidence-core').'</label><br />
                            <input type="text" id="billing_freq" name="billing_freq" value="'.intval(get_post_meta($post->ID,'billing_freq',true)).'">
                        </div>';
                print '    <div class="property_prop_half">
                            <label for="pack_stripe_id">Package Stripe ID (enter the ID from Stripe Account)</label><br />
                            <input type="text" id="pack_stripe_id" name="pack_stripe_id" value="'.esc_html( get_post_meta($post->ID,'pack_stripe_id',true) ).'">
                        </div>';
                print '</div>';

                print '<div class="property_tab_item_content'.( $active_tab === 'pack_listings' ? ' active_tab' : '' ).'" id="pack_listings">';
                print '    <div class="property_prop_half">
                            <label for="pack_listings">'.esc_html__('How many listings are included?','wpresidence-core').'</label><br />
                            <input type="text" id="pack_listings" name="pack_listings" value="'.esc_html( get_post_meta($post->ID,'pack_listings',true) ).'">
                            <br/>
                            <div class="wpresidence_check_list_wrapper">
                                <input type="hidden" name="mem_list_unl" value=""/>
                                <input type="checkbox" class="wpresidence-admin-checkbox" id="mem_list_unl" name="mem_list_unl" value="1" '.$check_unlimited_lists.' />
                                <label for="mem_list_unl" class="regular-label">'.esc_html__('Unlimited listings','wpresidence-core').'</label>
                            </div>
                        </div>';
                print '    <div class="property_prop_half">
                            <label for="pack_featured_listings">'.esc_html__('How many Featured listings are included?','wpresidence-core').'</label><br />
                            <input type="text" id="pack_featured_listings" name="pack_featured_listings" value="'.esc_html( get_post_meta($post->ID,'pack_featured_listings',true) ).'">
                        </div>';
                print '    <div class="property_prop_half">
                            <label for="pack_image_included">'.esc_html__('How many images are included per listing?','wpresidence-core').'</label><br />
                            <input type="text" id="pack_image_included" name="pack_image_included" value="'.intval( get_post_meta($post->ID,'pack_image_included',true) ).'">
                        </div>';
                print '</div>';

                print '<div class="property_tab_item_content'.( $active_tab === 'pack_display' ? ' active_tab' : '' ).'" id="pack_display">';
                print '    <div class="property_prop_half">
                            <label for="pack_visible_user_role">'.esc_html__('Display package for? *Hold CTRL for multiple selection.','wpresidence-core').'</label><br />
                            '.$visible_pack_select.'
                        </div>';
                print '    <div class="property_prop_half">
                            <label for="pack_visible">'.esc_html__('Is it visible?','wpresidence-core').'</label><br />
                            '.$visible_select.'
                        </div>';
                print '</div>';

        print '</div></div>';
    }
    endif; // end   membership_package



   
///////////////////////////////////////////////////////////////////////////////////////////
// update user profile on register
///////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_display_packages') ):
    /**
     * Displays available membership packages for user selection
     *
     * Creates a dropdown select element with all visible membership packages.
     * Used in various frontend interfaces where users need to select a package.
     * Each option contains data attributes for price and a sanitized title
     * that can be used for JavaScript interactions.
     *
     * @return void Outputs HTML directly
     */
    function wpestate_display_packages(){
        global $post;
        // Query for visible packages only
        $args = array(
                        'post_type'     => 'membership_package',
                        'posts_per_page'=> -1,
                        'meta_query'    => array(
                                                array(
                                                    'key' => 'pack_visible',
                                                    'value' => 'yes',
                                                    'compare' => '=',
                                                )
                                            )
        );
        $pack_selection = new WP_Query($args);
    
        // Build the select dropdown with default option
        $return='<select name="pack_select" id="pack_select" class="select-submit2"><option value="">'.esc_html__('Select package','wpresidence-core').'</option>';
        while($pack_selection->have_posts() ){
    
            $pack_selection->the_post();
            $title=get_the_title();
            // Add data attributes for price and sanitized title for JS usage
            $return.='<option value="'.$post->ID.'"  data-price="'.get_post_meta(get_the_id(),'pack_price',true).'" data-pick="'.sanitize_title($title).'" >'.$title.'</option>';
        }
        $return.='</select>';
    
        print $return;
    
    }
    endif; // end   wpestate_display_packages