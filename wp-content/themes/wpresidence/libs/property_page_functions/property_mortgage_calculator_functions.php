<?php
/* MILLDONE
*  src: libs\property_page_functions\property_mortgage_calculator_functions.php
*/

/**
 * Display property payment calculator.
 *
 * This function generates the HTML for displaying a property payment calculator.
 * It can output the content either as a tab or as an accordion item.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab. Default ''.
 * @param string $tab_active_class Optional. CSS class for active tab. Default ''.
 * @return array|void Array of tab content if $is_tab is 'yes', otherwise echoes the HTML.
 */

 if( !function_exists('wpestate_property_payment_calculator_v2') ):
    function wpestate_property_payment_calculator_v2($postID,$is_tab='',$tab_active_class=''){

        $show_morgage_calculator= wpestate_check_category_for_morgage($postID);
        if(  $show_morgage_calculator!='yes' )return;
      
        $data       =   wpestate_return_all_labels_data('payment_calculator');
        $label      =   wpestate_property_page_prepare_label( $data['label_theme_option'],$data['label_default'] );


        ob_start();
        wpestate_morgage_calculator($postID); 
        $content    =  ob_get_contents();
        ob_end_clean();
    
        if($is_tab=='yes'){
            $to_return =  wpestate_property_page_create_tab_item($content,$label,$data['tab_id'],$tab_active_class);
            $to_return['tab_panel'].= ' <script type="text/javascript">
            //<![CDATA[
                jQuery(document).ready(function(){
                    wpestate_show_morg_pie();
                });
        
            //]]>
            </script>';
            return  $to_return;
        }else{
            print wpestate_property_page_create_acc($content,$label,$data['accordion_id'],$data['accordion_id'].'_collapse');
            print ' <script type="text/javascript">
            //<![CDATA[
                jQuery(document).ready(function(){
                    wpestate_show_morg_pie();
                });
        
            //]]>
            </script>';
        }
    }

endif;


/**
 * Generate mortgage calculator content.
 *
 * @param int $postID The ID of the property post.
 * @return string The HTML content for the mortgage calculator.
 */
if ( ! function_exists( 'wpresidence_get_mortgage_calculator_content' ) ) :
    function wpresidence_get_mortgage_calculator_content( $postID ) {
        ob_start();
        wpestate_morgage_calculator( $postID );
        return ob_get_clean();
    }
endif;


/**
 * Generate mortgage calculator HTML for a property.
 *
 * This function calculates mortgage details and generates the HTML
 * for displaying a mortgage calculator on a property page.
 *
 * @since 1.0.0
 *
 * @param int   $post_id                  The ID of the property post.
 * @param array $wpestate_prop_all_details Optional. An array of all property details.
 */
if ( ! function_exists( 'wpestate_morgage_calculator' ) ) :
    function wpestate_morgage_calculator( $post_id, $wpestate_prop_all_details = '' ) {
        // Get currency data
        $currency      = esc_html( wpresidence_get_option( 'wp_estate_currency_symbol', '' ) );
        $currency_pos  = esc_html( wpresidence_get_option( 'wp_estate_where_currency_symbol', '' ) );
        $label_before  = $currency_pos === 'before' ? $currency : '';
        $label_after   = $currency_pos === 'after' ?  $currency : '';

        // Get property data
        if ( empty( $wpestate_prop_all_details ) ) {
            $price = floatval( get_post_meta( $post_id, 'property_price', true ) );
            $property_tax_percent = floatval( get_post_meta( $post_id, 'property_year_tax', true ) );
            $hoo_fees = floatval( get_post_meta( $post_id, 'property_hoa', true ) );
        } else {
            $price = floatval( wpestate_return_custom_field( $wpestate_prop_all_details, 'property_price' ) );
            $property_tax_percent = floatval( wpestate_return_custom_field( $wpestate_prop_all_details, 'property_year_tax' ) );
            $hoo_fees = floatval( wpestate_return_custom_field( $wpestate_prop_all_details, 'property_hoa' ) );
        }

        // Set default values if necessary
        $property_tax_percent = $property_tax_percent ?: floatval( wpresidence_get_option( 'wp_estate_morg_default_tax', '' ) );
        $price_down_percent = floatval( wpresidence_get_option( 'wp_estate_morg_default_price_down_per', '' ) );
        $price_down = $price * $price_down_percent / 100;
        $morgage_interest = floatval( wpresidence_get_option( 'wp_estate_morg_default_morg_interest', '' ) );
        $morgage_term = floatval( wpresidence_get_option( 'wp_estate_morg_default_morg_term', '' ) );
        $pmi_rate = floatval( wpresidence_get_option( 'wp_estate_morg_default_pmi', '' ) );

        // Calculate mortgage details
        $principal = $price - $price_down;
        $monthly_interest_rate = $morgage_interest / 12 / 100;
        $no_monthly_payments = 12 * $morgage_term;

        $pmt = ( $monthly_interest_rate * $principal ) / ( 1 - pow( 1 + $monthly_interest_rate, -$no_monthly_payments ) );
        $monthly_property_tax = $price * $property_tax_percent / 100 / 12;
        $monthly_pmi = $principal * $pmi_rate / 100 / 12;
        $total_monthly = $pmt + $monthly_property_tax + $hoo_fees + $monthly_pmi;

        // Avoid division by zero
        $total_monthly = max( $total_monthly, 1 );

        $percent_principal = $pmt * 100 / $total_monthly;
        $percent_hoa = $hoo_fees * 100 / $total_monthly;
        $percent_tax = $monthly_property_tax * 100 / $total_monthly;
        $percent_pmi = $monthly_pmi * 100 / $total_monthly;

        // Start HTML output
        ?>
        <style>
        .currency-wrapper::after { content: '<?php echo esc_html($currency); ?>'; }
        </style>
        
        <div class="row">
            <div class="morgage_chart_wrapper onfirst">
                <div id="canvas-holder">
                    <canvas id="morgage_chart"></canvas>
                    <div class="morg_momth_pay">
                        <div class="morg_month_wrap"><?php echo esc_html($label_before); ?><span id="morg_month_total"><?php echo number_format( $total_monthly, 2 ); ?></span><?php echo $label_after; ?></div>
                        <span id="morg_per_month"><?php esc_html_e( 'per month', 'wpresidence' ); ?></span>
                    </div>
                </div>
                <ul class="morgage_legend">
                    <li><?php esc_html_e( 'Principal and Interest', 'wpresidence' ); ?></li>
                    <li><?php esc_html_e( 'Property Tax', 'wpresidence' ); ?></li>
                    <li><?php esc_html_e( 'HOA fee', 'wpresidence' ); ?></li>
                    <li><?php esc_html_e( 'Private Mortgage Insurance', 'wpresidence' ); ?></li>
                </ul>
            </div>

            <div class="morgage_chart_wrapper">
                <label><?php esc_html_e( 'Principal and Interest', 'wpresidence' ); ?></label>
                <?php echo  esc_html($label_before); ?> <span data-per="<?php echo esc_attr( $percent_principal ); ?>" id="morg_principal"><?php echo number_format( $pmt, 2 ); ?></span><?php echo $label_after; ?>

                
                <label><?php esc_html_e( 'Property Tax', 'wpresidence' ); ?></label>
                <div class="currency-wrapper">
                    <input type="text" id="monthly_property_tax" class="form-control" data-per="<?php echo esc_attr( $percent_tax ); ?>" value="<?php echo esc_attr( $monthly_property_tax ); ?>">
                </div>

                <label><?php esc_html_e( 'Private Mortgage Insurance (PMI)', 'wpresidence' ); ?></label>
                <div class="wpresidence_private_mortgate_insurance_wrapper">
                    <div class="percentage-wrapper">
                        <input type="text" id="morgage_pmi_percent" class="morgage_inputdata form-control" value="<?php echo esc_attr( $pmi_rate ); ?>">
                    </div>
                    <div class="currency-wrapper">
                        <input type="text" id="morgage_pmi" class="form-control" data-per="<?php echo esc_attr( $percent_pmi ); ?>" value="<?php echo esc_attr( $monthly_pmi ); ?>" readonly>
                    </div>
                </div>

                

                <label><?php esc_html_e( 'Monthly HOA Fees', 'wpresidence' ); ?></label>
                <div class="currency-wrapper">
                    <input type="text" id="hoo_fees" class="form-control" data-per="<?php echo esc_attr( $percent_hoa ); ?>" value="<?php echo esc_attr( $hoo_fees ); ?>">
                </div>
            </div>

            <div class="morgage_data_wrapper onfirst">
                <label><?php esc_html_e( 'Total Amount', 'wpresidence' ); ?></label>
                <div class="currency-wrapper">
                    <input type="text" name="morgage_home_price" class="morgage_inputdata form-control" data-price="<?php echo esc_attr( $price ); ?>" id="morgage_home_price" value="<?php echo esc_attr( $price ); ?>">
                </div>

                <label><?php esc_html_e( 'Down Payment', 'wpresidence' ); ?></label>
                <div class="currency-wrapper" style="width: 75%;float:left;">
                    <input type="text" name="morgage_down_payment"  class="morgage_inputdata form-control" id="morgage_down_payment" data-price="<?php echo esc_attr( $price_down ); ?>" value="<?php echo esc_attr( $price_down ); ?>">
                </div>
                <div class="percentage-wrapper">
                    <input type="text" name="morgage_down_payment_percent" class="morgage_inputdata form-control" id="morgage_down_payment_percent" data-down-pay="<?php echo esc_attr( $price_down_percent ); ?>" value="<?php echo esc_attr( $price_down_percent ); ?>">
                </div>
            </div>

            <div class="morgage_data_wrapper">
                <label><?php esc_html_e( ' Loan Terms (Years)', 'wpresidence' ); ?></label>
                <input type="text" name="morgage_term" class="morgage_inputdata form-control" id="morgage_term" value="<?php echo esc_attr( $morgage_term ); ?>">

                <label><?php esc_html_e( 'Interest', 'wpresidence' ); ?></label>
                <div class="percentage-wrapper" style="width:100%;">
                    <input type="text" name="morgage_interest" class="morgage_inputdata form-control" id="morgage_interest" value="<?php echo esc_attr( $morgage_interest ); ?>">
                </div>
            </div>
        </div>
        <?php
    }
endif;



/**
 * Check if Category Should Show Mortgage Calculator
 *
 * This function determines whether a mortgage calculator should be displayed
 * for a given property, based on its categories and theme settings.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param int $post_id The ID of the property post.
 * @return string 'yes' if mortgage calculator should be shown, 'no' otherwise.
 */

if ( ! function_exists( 'wpestate_check_category_for_morgage' ) ) :
    function wpestate_check_category_for_morgage( $post_id ) {
        // Get theme options for mortgage calculator display
        $show_morgage_calculator = wpresidence_get_option( 'wp_estate_show_morg_calculator', '' );
        $exclude_categories = wpresidence_get_option( 'wp_estate_excludeshow_morg_calculator', '' );

        // If mortgage calculator is enabled globally
        if ( 'yes' === $show_morgage_calculator ) {
            // Check if there are categories to exclude
            if ( is_array( $exclude_categories ) && ! empty( $exclude_categories ) ) {


                // Get property categories and action categories
                $terms_category = get_the_terms( $post_id, 'property_category' );
                $terms_action_category = get_the_terms( $post_id, 'property_action_category' );

                // Combine all terms
                $all_terms = array();
                if ( $terms_category ) {
                    $all_terms = array_merge( $all_terms, $terms_category );
                }
                if ( $terms_action_category ) {
                    $all_terms = array_merge( $all_terms, $terms_action_category );
                }



                // Check if any of the property's terms are in the exclude list
                foreach ( $all_terms as $term ) {
                 
                    if ( in_array( (int) $term->term_id, array_map('intval', $exclude_categories), true ) ) {
                        return 'no';
                    }
                }
            }
        }

        // Return the global setting if no exclusions apply
        return $show_morgage_calculator;
    }
endif;