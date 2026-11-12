<?php

$property_fields_raw=array(

  'post_title' =>array(
                  'section'             => 'description',
                  'label'               =>  esc_html__('*Title (mandatory)','wpresidence'),
                  'type'                =>  'wpestate_title',
                  'meta_name'           =>  'wpestate_title',
                  'inputype'            =>  'input',
                  'bootstral_length'    =>  '12'
                ),

  'post_description' =>array(
                  'section'             => 'description',
                  'label'               =>  esc_html__('Description','wpresidence'),
                  'type'                =>  'wpestate_description',
                  'meta_name'           =>  'wpestate_description',
                  'inputype'            =>  'wp_editor',
                  'bootstral_length'    =>  '12'
                ),

  'property_price' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__('Price in ','wpresidence').esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') ).esc_html__('(only numbers)','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_price',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '12'
                  ),

  'property_year_tax' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__('Yearly Tax Rate','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_year_tax',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
                  ),
  'property_hoa' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__('Homeowners Association Fee(monthly)','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_hoa',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
                  ),
  'property_label' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__('After Price Label (ex: "/month")','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_label',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
                  ),
  'property_label_before' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__('Before Price Label (ex: "from ")','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_label_before',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
                  ),
    'property_second_price' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__('Additional Price Info','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_second_price',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
    ),
    
    'property_second_price_label' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__(' After Label for Additional Price info ','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_second_price_label',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
    ),
    'property_label_before_second_price' =>array(
                    'section'             => 'description',
                    'label'               =>  esc_html__('Before Label for Additional Price Info','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'property_label_before_second_price',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
    ),

    'property_category' =>array(
                      'section'             => 'category',
                      'label'               =>  esc_html__('Select Categorie','wpresidence'),
                      'type'                =>  'taxonomy',
                      'meta_name'           =>  'prop_category',
                      'tax_name'           =>  'property_category',
                      'inputype'            =>  'wp_dropdown',
                      'bootstral_length'    =>  '6'
                    ),
    'property_action_category' =>array(
                      'section'             => 'category',
                      'label'               =>  esc_html__('Listed In','wpresidence'),
                      'type'                =>  'taxonomy',
                      'meta_name'           =>  'prop_action_category',
                      'tax_name'            =>  'property_action_category',
                      'inputype'            =>  'wp_dropdown',
                      'bootstral_length'    =>  '6'
                    ),
      // 'property_city' =>array(
      //                   'section'             => 'location',
      //                   'label'               =>  esc_html__('City','wpresidence'),
      //                   'type'                =>  'taxonomy',
      //                   'meta_name'           =>  'property_city',
      //                   'tax_name'            =>  'property_city',
      //                   'inputype'            =>  'wp_dropdown',
      //                   'bootstral_length'    =>  '6'
      //                 ),
      // 'property_area' =>array(
      //                   'section'             => 'location',
      //                   'label'               =>  esc_html__('Listed In','wpresidence'),
      //                   'type'                =>  'taxonomy',
      //                   'meta_name'           =>  'property_area',
      //                   'tax_name'            =>  'property_area',
      //                   'inputype'            =>  'wp_dropdown',
      //                   'bootstral_length'    =>  '6'
      //                 ),
      //     'property_county_state' =>array(
      //                       'section'             => 'category',
      //                       'label'               =>  esc_html__('County State','wpresidence'),
      //                       'type'                =>  'taxonomy',
      //                       'meta_name'           =>  'property_county_state',
      //                       'tax_name'            =>  'property_county_state',
      //                       'inputype'            =>  'wp_dropdown',
      //                       'bootstral_length'    =>  '6'
      //                     ),



      'property_address' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Property Address','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'property_address',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),

      'property_zip' =>array(
                          'section'             => 'location',
                          'label'               =>  esc_html__('Property Zip','wpresidence'),
                          'type'                =>  'post_meta',
                          'meta_name'           =>  'property_zip',
                          'inputype'            =>  'input',
                          'bootstral_length'    =>  '6'
                        ),
      'property_country' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Country','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'property_country',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),

      'energy_class' =>array(
                          'section'             => 'location',
                          'label'               =>  esc_html__('Energy Class','wpresidence'),
                          'type'                =>  'post_meta',
                          'meta_name'           =>  'energy_class',
                          'inputype'            =>  'input',
                          'bootstral_length'    =>  '6'
                        ),
      'energy_index' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Energy Index','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'energy_index',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),
        'co2_index' =>array(
                    'section'             => 'location',
                    'label'               =>  esc_html__('Greenhouse gas emissions kgCO2/m2a','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'co2_index',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
                ),   
        'co2_class' =>array(
                    'section'             => 'location',
                    'label'               =>  esc_html__('Greenhouse gas emissions index class','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'co2_class',
                    'inputype'            =>  'input',
                    'bootstral_length'    =>  '6'
                ),
        'renew_energy_index' =>array(
                    'section'             => 'location',
                    'label'               =>  esc_html__('Renewable energy performance index','wpresidence'),
                    'type'                =>  'post_meta',
                    'meta_name'           =>  'renew_energy_index',
                    'inputype'            =>  'input',
                '   bootstral_length'    =>  '6'
                ),
        'building_energy_index' =>array(
                'section'             => 'location',
                'label'               =>  esc_html__('Energy performance of the building','wpresidence'),
                'type'                =>  'post_meta',
                'meta_name'           =>  'building_energy_index',
                'inputype'            =>  'input',
            '   bootstral_length'    =>  '6'
        ),
        'epc_current_rating' =>array(
            'section'             => 'location',
            'label'               =>  esc_html__('EPC current rating','wpresidence'),
            'type'                =>  'post_meta',
            'meta_name'           =>  'epc_current_rating',
            'inputype'            =>  'input',
        '   bootstral_length'    =>  '6'
        ),
        'epc_potential_rating' =>array(
            'section'             => 'location',
            'label'               =>  esc_html__('EPC Potential Rating','wpresidence'),
            'type'                =>  'post_meta',
            'meta_name'           =>  'epc_potential_rating',
            'inputype'            =>  'input',
        '   bootstral_length'    =>  '6'
        ),
                           
      'property_size' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Property Size','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'property_size',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),
      'owner_notes' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Owner Notes','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'owner_notes',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),
      'property_lot_size' =>array(
                          'section'             => 'location',
                          'label'               =>  esc_html__('Property Lot Size','wpresidence'),
                          'type'                =>  'post_meta',
                          'meta_name'           =>  'property_lot_size',
                          'inputype'            =>  'input',
                          'bootstral_length'    =>  '6'
                        ),
       'property_rooms' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Property Rooms','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'property_rooms',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),
        'property_bedrooms' =>array(
                         'section'             => 'location',
                         'label'               =>  esc_html__('Property Bedrooms','wpresidence'),
                         'type'                =>  'post_meta',
                         'meta_name'           =>  'property_bedrooms',
                         'inputype'            =>  'input',
                         'bootstral_length'    =>  '6'
                       ),
         'property_bathrooms' =>array(
                            'section'             => 'location',
                            'label'               =>  esc_html__('Property Bathrooms','wpresidence'),
                            'type'                =>  'post_meta',
                            'meta_name'           =>  'property_bathrooms',
                            'inputype'            =>  'input',
                            'bootstral_length'    =>  '6'
                          ),
          'embed_video_type' =>array(
                           'section'             => 'location',
                           'label'               =>  esc_html__('Video Type','wpresidence'),
                           'type'                =>  'post_meta',
                           'meta_name'           =>  'embed_video_type',
                           'inputype'            =>  'input',
                           'bootstral_length'    =>  '6'
                         ),
           'embed_video_id' =>array(
                            'section'             => 'location',
                            'label'               =>  esc_html__('Video Type','wpresidence'),
                            'type'                =>  'post_meta',
                            'meta_name'           =>  'embed_video_id',
                            'inputype'            =>  'input',
                            'bootstral_length'    =>  '6'
                          ),
            'embed_virtual_tour' =>array(
                             'section'             => 'location',
                             'label'               =>  esc_html__('Virtual Tour','wpresidence'),
                             'type'                =>  'post_meta',
                             'meta_name'           =>  'embed_virtual_tour',
                             'inputype'            =>  'input',
                             'bootstral_length'    =>  '6'
                           ),
     'property_latitude' =>array(
                      'section'             => 'location',
                      'label'               =>  esc_html__('Property Latitude','wpresidence'),
                      'type'                =>  'post_meta',
                      'meta_name'           =>  'property_latitude',
                      'inputype'            =>  'input',
                      'bootstral_length'    =>  '6'
                    ),
    'property_longitude' =>array(
                       'section'             => 'location',
                       'label'               =>  esc_html__('Property Longitudine','wpresidence'),
                       'type'                =>  'post_meta',
                       'meta_name'           =>  'property_longitude',
                       'inputype'            =>  'input',
                       'bootstral_length'    =>  '6'
                     ),
    'property_google_view' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Google View','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'property_google_view',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),
    'property_hide_map_marker' =>array(
                        'section'             => 'location',
                        'label'               =>  esc_html__('Hide Map Marker','wpresidence'),
                        'type'                =>  'post_meta',
                        'meta_name'           =>  'property_hide_map_marker',
                        'inputype'            =>  'input',
                        'bootstral_length'    =>  '6'
                      ),

      'google_camera_angle' =>array(
                 'section'             => 'location',
                 'label'               =>  esc_html__('Google Camera Angle','wpresidence'),
                 'type'                =>  'post_meta',
                 'meta_name'           =>  'google_camera_angle',
                 'inputype'            =>  'input',
                 'bootstral_length'    =>  '6'
               ),

       'property_subunits_list' =>array(
                  'section'             => 'location',
                  'label'               =>  esc_html__('Property subunits list','wpresidence'),
                  'type'                =>  'post_meta',
                  'meta_name'           =>  'property_subunits_list',
                  'inputype'            =>  'input',
                  'bootstral_length'    =>  '6'
                ),
        'property_has_subunits' =>array(
                   'section'             => 'location',
                   'label'               =>  esc_html__('Property has subunits','wpresidence'),
                   'type'                =>  'post_meta',
                   'meta_name'           =>  'property_has_subunits',
                   'inputype'            =>  'input',
                   'bootstral_length'    =>  '6'
                 ),
);







        //
        // 'property_city' =>array(
        //                   'section'             => 'location',
        //                   'label'               =>  esc_html__('City','wpresidence'),
        //                   'type'                =>  'taxonomy',
        //                   'meta_name'           =>  'property_city',
        //                   'tax_name'            =>  'property_city',
        //                   'inputype'            =>  'wp_dropdown',
        //                   'bootstral_length'    =>  '6'
        //                 ),
        // 'property_area' =>array(
        //                   'section'             => 'location',
        //                   'label'               =>  esc_html__('Area','wpresidence'),
        //                   'type'                =>  'taxonomy',
        //                   'meta_name'           =>  'property_area',
        //                   'tax_name'            =>  'property_area',
        //                   'inputype'            =>  'wp_dropdown',
        //                   'bootstral_length'    =>  '6'
        //                 ),
        //   'property_county' =>array(
        //                     'section'             => 'location',
        //                     'label'               =>  esc_html__('County/State','wpresidence'),
        //                     'type'                =>  'taxonomy',
        //                     'meta_name'           =>  'property_county',
        //                     'tax_name'            =>  'property_county',
        //                     'inputype'            =>  'wp_dropdown',
        //                     'bootstral_length'    =>  '6'
        //                   ),






  //'post_content' => array(),
  // property_category
  // property_action_category
  // property_city
  // property_area
  // property_county_state
  // property_address
  // property_county
  // property_zip
  // property_country
  // energy_class
  // energy_index
  // property_status
  // property_price
  // property_label
  // property_label_before
  // property_year_tax
  // property_hoa
  // property_size
  // owner_notes
  // property_lot_size
  // property_rooms
  // property_bedrooms
  // property_bathrooms
  // property_has_subunits
  // property_subunits_list
  // property_roofing
  // embed_video_type
  // google_camera_angle
  //
  // embed_video_id
  // embed_virtual_tour
  // property_latitude
  // property_longitude
  // property_google_view
  // google_camera_angle



// plan_title
// plan_description
// plan_image
// plan_size
// plan_rooms
// plan_bath
// plan_price
//


/*
* Change unit status
*
*/
add_action( 'wp_ajax_wpestate_disable_listing', 'wpestate_disable_listing' );

if( !function_exists('wpestate_disable_listing') ):
    function wpestate_disable_listing(){
        if( isset($_POST['is_agent']) && intval($_POST['is_agent'])==1 ){
            check_ajax_referer( 'wpestate_agent_actions', 'security' );
        }else{
            check_ajax_referer( 'wpestate_property_actions', 'security' );
        }
        $current_user       =   wp_get_current_user();
        $userID             =   $current_user->ID;
        $user_login         =   $current_user->user_login;

        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        $prop_id=intval($_POST['prop_id']);
        if(!is_numeric($prop_id)) {
            exit();
        }
        $agent_list                     =   (array)get_user_meta($userID,'current_agent_list',true);
        $the_post= get_post( $prop_id);

        if( $current_user->ID != $the_post->post_author   &&  !in_array($the_post->post_author , $agent_list)  ) {
            exit('you don\'t have the right to delete this');
        }

        if($the_post->post_status=='disabled'){
            $new_status='publish';
        }else{
            $new_status='disabled';
        }
        $my_post = array(
            'ID'           => $prop_id,
            'post_status'   => $new_status
        );

        wp_update_post( $my_post );
        die();
    }
endif;


/**
 * Dashboard Invoice List Function
 *
 * Displays a list of invoices in the user dashboard with filtering options.
 * Shows invoice details, totals, and provides filtering by date, type, and status.
 * 
 * @package WpResidence
 * @subpackage Dashboard/Invoices
 * @since 1.0
 */

if (!function_exists('wpestate_dashboard_invoice_list')):
    function wpestate_dashboard_invoice_list() {
        // Get current user
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;

        // Setup WP_Query arguments
        $args = array(
            'post_type'      => 'wpestate_invoice',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'author'         => $userID,
        );

        // Initialize variables
        $total_confirmed = 0;
        $templates = '';
        
        // Get currency settings
        $wpestate_currency       =   esc_html ( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency =   esc_html ( wpresidence_get_option('wp_estate_where_currency_symbol', '') );

        // Query invoices
        $prop_selection = new WP_Query($args);
        
        // Process invoices if found
        if ($prop_selection->have_posts()) {
            ob_start();
            while ($prop_selection->have_posts()): 
                $prop_selection->the_post();
                $invoiceID = get_the_ID();
                include(locate_template('templates/dashboard-templates/invoice_listing_unit.php'));
                
                // Calculate totals
                $price = floatval(get_post_meta($invoiceID, 'item_price', true));
                $total_confirmed += $price;
            endwhile;
            $templates = ob_get_contents();
            ob_end_clean();
            wp_reset_postdata();
        }

        // Display filter section
        ?>
        <div class="wpestate_dashboard_list_header flex-md-row flex-column">
            <!-- Date Filters -->
            <input type="text" 
                   id="invoice_start_date" 
                   class="wpestate_dashboard_input_type form-control" 
                   name="invoice_start_date" 
                        autocomplete="off"
                   placeholder="<?php esc_attr_e('from date', 'wpresidence'); ?>">
            
            <input type="text" 
                   id="invoice_end_date" 
                   class="wpestate_dashboard_input_type form-control" 
                   name="invoice_end_date" 
                    autocomplete="off"
                   placeholder="<?php esc_attr_e('to date', 'wpresidence'); ?>">

            <!-- Invoice Type Filter -->
            <select id="invoice_type" name="invoice_type" class="wpestate-dashboard-select">
                <option value=""><?php esc_html_e('Any', 'wpresidence'); ?></option>
                <option value="Upgrade to Featured"><?php esc_html_e('Upgrade to Featured', 'wpresidence'); ?></option>
                <option value="Package"><?php esc_html_e('Package', 'wpresidence'); ?></option>
                <option value="Listing"><?php esc_html_e('Listing', 'wpresidence'); ?></option>
            </select>

            <!-- Status Filter -->
            <select id="invoice_status" name="invoice_status" class="wpestate-dashboard-select">
                <option value=""><?php esc_html_e('Any', 'wpresidence'); ?></option>
                <option value="1"><?php esc_html_e('Paid', 'wpresidence'); ?></option>
                <option value="0"><?php esc_html_e('Not Paid', 'wpresidence'); ?></option>
            </select>

            <!-- Totals Display -->
            <div class="col-md-12 invoice_totals">
                <strong><?php esc_html_e('Total Invoices: ', 'wpresidence'); ?></strong>
                <span id="invoice_confirmed">
                    <?php echo wpestate_show_price_custom_invoice($total_confirmed); ?>
                </span>
            </div>
        </div>

        <!-- Table Headers -->
        <div class="wpestate_dashboard_table_list_header d-none d-md-flex row">
            <div class="col-md-2"><?php esc_html_e('Title', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Date', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Invoice Type', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Billing Type', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Status', 'wpresidence'); ?></div>
            <div class="col-md-1"><?php esc_html_e('Price', 'wpresidence'); ?></div>
            <div class="col-md-1"></div>
        </div>

        <!-- Invoice Listings -->

            <?php echo trim($templates); ?>
    
        <?php
    }
endif;



/**
 * Displays and manages the agent list in the admin dashboard.
 * 
 * This function handles the display of agents associated with the current user (agency/developer)
 * in the WordPress admin dashboard. It includes search functionality, pagination, and 
 * displays agent details in a table format.
 * 
 * @package WpResidence
 * @subpackage DashboardFunctions
 * @since 1.0.0
 * 
 * @param int $status_value Optional. Filter agents by status.
 * @return void
 * 
 * @uses WP_Query
 * @uses wpresidence_get_option()
 * @uses locate_template()
 * @uses wpestate_pagination()
 */
if (!function_exists('wpestate_dashboard_agent_list')):
    function wpestate_dashboard_agent_list($status_value = '') {
        // Get current user and pagination settings
        $current_user = wp_get_current_user();
        $prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));
        $paged = max(1, get_query_var('paged'));

        // Base query arguments
        $args = array(
            'post_type'      => 'estate_agent',
            'author'         => $current_user->ID,
            'paged'          => $paged,
            'posts_per_page' => $prop_no,
            'post_status'    => array('any')
        );

        // Add search parameter if provided
        if (isset($_POST['prop_name']) && !empty($_POST['prop_name'])) {
            $args['s'] = sanitize_text_field($_POST['prop_name']);
        }

        // Initialize agent query
        $prop_selection = new WP_Query($args);

        // Display search form
        ?>
        <div class="wpestate_dashboard_list_header flex-md-row flex-column">
            <form action="" id="search_dashboard_auto" method="POST">
                <input type="text" 
                       id="prop_name" 
                       name="prop_name" 
                       value="" 
                       placeholder="<?php esc_attr_e('Search for an Agent', 'wpresidence'); ?>">
                <input type="submit" 
                       class="wpresidence_button" 
                       id="search_form_submit_1" 
                       value="<?php esc_attr_e('Search', 'wpresidence'); ?>">
                <?php wp_nonce_field('dashboard_agent_search', 'dashboard_agent_search_nonce'); ?>
            </form>
        </div>

        <!-- Table Header -->
        <div class="wpestate_dashboard_table_list_header d-none d-md-flex row">
            <div class="col-md-4"><?php esc_html_e('Agent', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Phone/Mobile', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Email', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Status', 'wpresidence'); ?></div>
            <div class="col-md-2 property_dashboard_action"><?php esc_html_e('Actions', 'wpresidence'); ?></div>
        </div>

        <?php
        // Display agent list or no results message
        if (!$prop_selection->have_posts()) {
            ?>
            <div class="col-md-12 row_dasboard-prop-listing">
                <h4><?php esc_html_e('You don\'t have any agents added!', 'wpresidence'); ?></h4>
            </div>
            <?php
        } else {
            while ($prop_selection->have_posts()): 
                $prop_selection->the_post();
                $agentID=get_the_ID();
                include(locate_template('templates/dashboard-templates/dashboard_agent_unit.php'));
            endwhile;
        }

        // Reset post data
        wp_reset_postdata();

        // Setup autocomplete functionality
        $autofill_args = array(
            'post_type'                 => 'estate_agent',
            'author'                    => $current_user->ID,
            'posts_per_page'            => -1,
            'post_status'               => array('any'),
            'cache_results'             => false,
            'update_post_meta_cache'    => false,
            'update_post_term_cache'    => false,
        );

        $agent_titles = array();
        $prop_selection2 = new WP_Query($autofill_args);
        
        while ($prop_selection2->have_posts()): 
            $prop_selection2->the_post();
            $agent_titles[] = get_the_title();
        endwhile;
        
        wp_reset_postdata();

        // Enqueue autocomplete script
        $autofill_json = wp_json_encode($agent_titles);
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                var autofill = <?php echo $autofill_json; ?>;
                $("#prop_name").autocomplete({
                    source: autofill
                });
            });
        </script>
        <?php

        // Display pagination
        wpestate_pagination($prop_selection->max_num_pages, 2);
    }
endif;







/*
* Property list admin dashboard
* $agent_list is a list of possible author - the real author id + the agency and developer that owns that user
*/
if( !function_exists('wpestate_dashboard_property_list') ):
  function wpestate_dashboard_property_list($agent_list,$order_by='0',$status_value='0'){
      $prop_no      =   intval( wpresidence_get_option('wp_estate_prop_no', '') );
      $paged        =   (get_query_var('paged')) ? get_query_var('paged') : 1;
      $autofill     =   '';
      $order_by     =   intval($order_by);
      $order_data   =   wpestate_set_order_parameter_property($order_by);
      $agent_list   =   array_filter($agent_list);

      $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $user_login = $current_user->user_login;
      $user_pack = get_the_author_meta('package_id', $userID);

      $edit_link = wpestate_get_template_link('page-templates/user_dashboard_add.php');
      $args = array(
              'post_type'         =>  'estate_property',
              'author__in'        =>  $agent_list,
              'paged'             =>  $paged,
              'posts_per_page'    =>  $prop_no,
              'post_status'       =>  wpestate_set_status_parameter_property($status_value)

              );


        $args ['meta_key']        =   $order_data ['meta_key'];
        $args ['orderby']         =   $order_data ['orderby'] ;
        $args ['order']           =   $order_data ['order'] ;


        if(isset($_POST['prop_name'])){
            $prop_name = esc_html( $_POST['prop_name'] );
            $args['s']  = $prop_name ;
        }



        $prop_selection = new WP_Query($args);

        if($order_by!=0) {
          $prop_selection = new WP_Query($args);
        }else{
          $prop_selection = wpestate_return_filtered_by_order($args);
        }




      print '<div class="wpestate_dashboard_list_header flex-md-row flex-column">';
            get_template_part('templates/dashboard-templates/dashboard-list-filter-actions');
            get_template_part('templates/dashboard-templates/dashboard-list-filter-categories');
            print '<form action="" id="search_dashboard_auto" method="POST">
                    <input type="text" id="prop_name" name="prop_name" value="" placeholder="'.esc_html__('Search a listing','wpresidence').'">
                    <input type="submit" class="wpresidence_button" id="search_form_submit_1" value="'.esc_html__('Search','wpresidence').'">';
                    wp_nonce_field( 'dashboard_searches', 'dashboard_searches_nonce');
            print'</form> ';
      print '</div>';


      print '<div class="wpestate_dashboard_table_list_header d-none d-md-flex row">';



        $paid_submission_status         =   esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
        if ($paid_submission_status=='per listing'){
              print '<div class="col-md-3">'.esc_html__('Property','wpresidence').'</div>';
              print '<div class="col-md-2">'.esc_html__('Category','wpresidence').'</div>';
              print '<div class="col-md-2">'.esc_html__('Status','wpresidence').'</div>';
              print '<div class="col-md-2">'.esc_html__('Pay Status','wpresidence').'</div>';
              print '<div class="col-md-1">'.esc_html__('Price','wpresidence').'</div>';
        }else{
              print '<div class="col-md-4">'.esc_html__('Property','wpresidence').'</div>';
              print '<div class="col-md-2">'.esc_html__('Category','wpresidence').'</div>';
              print '<div class="col-md-2">'.esc_html__('Status','wpresidence').'</div>';
              print '<div class="col-md-2">'.esc_html__('Price','wpresidence').'</div>';
        }


        print '<div class="col-md-2 property_dashboard_action">'.esc_html__('Actions','wpresidence').'</div>';
      print'</div>';

      if( !$prop_selection->have_posts() ){
          print '<h4 style="margin-top:30px">'.esc_html__('You don\'t have any properties!','wpresidence').'</h4>';
      }else{

          while ($prop_selection->have_posts()): $prop_selection->the_post();
                include( locate_template('templates/dashboard-templates/dashboard_listing_unit.php'));
          endwhile;


          $args2= array(
                  'post_type'                 =>  'estate_property',
                  'author__in'                =>  $agent_list,
                  'posts_per_page'            => '-1' ,
                  'post_status'               =>  array( 'any' ),
                  'cache_results'             =>  false,
                  'update_post_meta_cache'    =>  false,
                  'update_post_term_cache'    =>  false,

                  );
          $prop_selection2 = new WP_Query($args2);
          while ($prop_selection2->have_posts()): $prop_selection2->the_post();
                  $autofill.= '"'.get_the_title().'",';
          endwhile;

          print '<script type="text/javascript">
             //<![CDATA[
                   jQuery(document).ready(function(){
                       var autofill=['.$autofill.']
                       jQuery( "#prop_name" ).autocomplete({
                       source: autofill
                   });
             });
             //]]>
             </script>';
          wpestate_pagination($prop_selection->max_num_pages, $range =2);

          
    }

  }
endif;











/*
* Delete listing
*
*/
if( !function_exists('wpestate_delete_listing_dashboard') ):
function wpestate_delete_listing_dashboard($delete_get_id,$current_user,$agent_list){


    if( !is_numeric( $delete_get_id) ){
        exit('you don\'t have the right to delete this!');
    }else{
        $delete_id  =   intval( $delete_get_id);
        $the_post   =   get_post( $delete_id);


        if( $current_user->ID != $the_post->post_author   &&  !in_array($the_post->post_author , $agent_list)  ) {
            exit('you don\'t have the right to delete this');
        }else{

            // delete attchaments
            $arguments = array(
                'numberposts'   =>  -1,
                'post_type'     =>  'attachment',
                'post_parent'   =>  $delete_id,
                'post_status'   =>  null,
                'exclude'       =>  get_post_thumbnail_id(),
                'orderby'       =>  'menu_order',
                'order'         =>  'ASC'
            );
            $post_attachments = get_posts($arguments);

            foreach ($post_attachments as $attachment) {
               wp_delete_post($attachment->ID);
            }

            wp_delete_post( $delete_id );
            wp_redirect( wpestate_get_template_link('page-templates/user_dashboard.php') );  exit;
        }

    }
}
endif;



////////////////////////////////////////////////////////////////////////////////
/// Ajax  register agent
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_register_agent', 'wpestate_ajax_register_agent' );
if( !function_exists('wpestate_ajax_register_agent') ):

  function wpestate_ajax_register_agent(){
         check_ajax_referer( 'wpestate_register_agent_nonce', 'security' );

         $allowed_html       =   array();
         $current_user           =   wp_get_current_user();
         $userID                 =   $current_user->ID;
         $user_login             =   $current_user->user_login;
       //  check_ajax_referer( 'profile_ajax_nonce', 'security-profile' );
         if ( !is_user_logged_in() ) {
             exit('ko');
         }
         if($userID === 0 ){
             exit('out pls');
         }
         $user_role = get_user_meta( $current_user->ID, 'user_estate_role', true) ;
         if($user_role!=3 && $user_role !=4){
             exit;
         }

         $user_email                 =   trim( sanitize_text_field(wp_kses( $_POST['useremail'] ,$allowed_html) ) );
         $user_name                  =   trim( sanitize_text_field(wp_kses( $_POST['agent_username'] ,$allowed_html) ) );
         $firstname                  =   sanitize_text_field ( wp_kses( $_POST['firstname'] ,$allowed_html) );
         $secondname                 =   sanitize_text_field ( wp_kses( $_POST['secondname'] ,$allowed_html) );
         $new_user_type              =   2;//agent
         $errors                     =   array();
         $is_agent_edit              =   intval($_POST['agentedit']);
         $user_id                    =   intval($_POST['userid']);
         $agent_id                   =   intval($_POST['agentid']);

         if( $is_agent_edit!=1){

             if (preg_match("/^[0-9A-Za-z_]+$/", $user_name) == 0) {
                 $errors[]= esc_html__('Invalid username (do not use special characters or spaces)!','wpresidence');
             }

             if ($user_email=='' || $user_name=='' ){
                 $errors[]= esc_html__('Username and/or Email field is empty!','wpresidence');
             }

             $user_id     =   username_exists( $user_name );
             if ($user_id){
                 $errors[]= esc_html__('Username already exists.  Please choose a new one.','wpresidence');
             }

             $user_pass              =   trim( sanitize_text_field(wp_kses( $_POST['agent_password'] ,$allowed_html) ) );
             $user_pass_retype       =   trim( sanitize_text_field(wp_kses( $_POST['agent_repassword'] ,$allowed_html) ) );

             if ($user_pass=='' || $user_pass_retype=='' ){
                 $errors[]= esc_html__('One of the password field is empty!','wpresidence');
             }

             if ($user_pass !== $user_pass_retype ){
                 $errors[]= esc_html__('Passwords do not match','wpresidence');
             }

             if( email_exists($user_email) ){
                 $errors[]= esc_html__('Email already exists.  Please choose a new one.','wpresidence');
             }
         }

         if( $is_agent_edit==1){
             if ($user_email==''  ){
                 $errors[]= esc_html__('Username and/or Email field is empty!','wpresidence');
             }
             if( $user_email != get_post_meta($agent_id, 'agent_email', true) ){
                 if( email_exists($user_email) ){
                     $errors[]= esc_html__('Email already exists.  Please choose a new one.','wpresidence');
                 }
             }
         }

         if($firstname=='' && $secondname==''){
             $errors[]= esc_html__('Agent need a first & last name','wpresidence');
         }


         if(filter_var($user_email,FILTER_VALIDATE_EMAIL) === false) {
             $errors[]= esc_html__('The email doesn\'t look right !','wpresidence');
         }

         $domain = mb_substr(strrchr($user_email, "@"), 1);
         if( $domain!='' && !checkdnsrr ($domain)  ){
             $errors[]=  esc_html__('The email\'s domain doesn\'t look right.','wpresidence');
         }




         if( !empty($errors) && is_array($errors) ){
             $erros_mess =   '';
             foreach($errors as $key=>$text){
                 $erros_mess .= $text.'</br>';
             }
             print json_encode(array ('added'=>false, 'mesaj'=>$erros_mess ));
             die();
         }

         if($is_agent_edit!=1){
             $new_user_id    =   wp_create_user( $user_name, $user_pass, $user_email );
         }else{
             $new_user_id    =   $user_id;
         }





         $allowed_html               =   array('</br>');
         $firstname                  =   sanitize_text_field ( wp_kses( $_POST['firstname'] ,$allowed_html) );
         $secondname                 =   sanitize_text_field ( wp_kses( $_POST['secondname'] ,$allowed_html) );
         $useremail                  =   sanitize_text_field ( wp_kses( $_POST['useremail'] ,$allowed_html) );
         $userphone                  =   sanitize_text_field ( wp_kses( $_POST['userphone'] ,$allowed_html) );
         $usermobile                 =   sanitize_text_field ( wp_kses( $_POST['usermobile'] ,$allowed_html) );
         $userskype                  =   sanitize_text_field ( wp_kses( $_POST['userskype'] ,$allowed_html) );
         $usertitle                  =   sanitize_text_field ( wp_kses( $_POST['usertitle'] ,$allowed_html) );
         $about_me                   =   wp_kses( $_POST['description'],$allowed_html );
         $profile_image_url_small    =   sanitize_text_field ( wp_kses($_POST['profile_image_url_small'],$allowed_html) );
         $profile_image_url          =   sanitize_text_field ( wp_kses($_POST['profile_image_url'],$allowed_html) );
         $userfacebook               =   sanitize_text_field ( wp_kses( $_POST['userfacebook'],$allowed_html) );
         $usertwitter                =   sanitize_text_field ( wp_kses( $_POST['usertwitter'],$allowed_html) );
         $userlinkedin               =   sanitize_text_field ( wp_kses( $_POST['userlinkedin'],$allowed_html) );
         $userpinterest              =   sanitize_text_field ( wp_kses( $_POST['userpinterest'],$allowed_html ) );
         $userinstagram              =   sanitize_text_field ( wp_kses( $_POST['userinstagram'],$allowed_html ) );


         $agent_custom_label          =   $_POST['agent_custom_label'];
         $agent_custom_value          =   $_POST['agent_custom_value'];

         // prcess fields data
         $agent_fields_array = array();
         if(is_array($agent_custom_label) && is_array($agent_custom_value)){
             for( $i=0; $i<count( $agent_custom_label  ); $i++ ){
                     $agent_fields_array[] = array( 'label' => sanitize_text_field( $agent_custom_label[$i] ), 'value' => sanitize_text_field( $agent_custom_value[$i] ) );
             }
         }

         $userurl                    =   sanitize_text_field ( wp_kses( $_POST['userurl'],$allowed_html ) );
         $agent_category_submit      =   sanitize_text_field ( wp_kses( $_POST['agent_category_submit'],$allowed_html ) );
         $agent_action_submit        =   sanitize_text_field ( wp_kses( $_POST['agent_action_submit'],$allowed_html ) );
         $agent_city                 =   sanitize_text_field ( wp_kses( $_POST['agent_city'],$allowed_html ) );
         $agent_county               =   sanitize_text_field ( wp_kses( $_POST['agent_county'],$allowed_html ) );
         $agent_area                 =   sanitize_text_field ( wp_kses( $_POST['agent_area'],$allowed_html ) );
         $agent_member               =   sanitize_text_field ( wp_kses( $_POST['agent_member'],$allowed_html ) );
         $agent_address              =   sanitize_text_field ( wp_kses( $_POST['agent_address'],$allowed_html ) );

         $agent_youtube              =   sanitize_text_field ( wp_kses( $_POST['agent_youtube '],$allowed_html ) );
         $agent_tiktok               =   sanitize_text_field ( wp_kses( $_POST['agent_tiktok '],$allowed_html ) );
         $agent_telegram             =   sanitize_text_field ( wp_kses( $_POST['agent_telegram '],$allowed_html ) );
         $agent_vimeo                =   sanitize_text_field ( wp_kses( $_POST['agent_vimeo '],$allowed_html ) );
         $agent_private_notes        =   sanitize_text_field ( wp_kses( $_POST['agent_private_notes '],$allowed_html ) );
         
         update_user_meta( $new_user_id, 'first_name', $firstname ) ;
         update_user_meta( $new_user_id, 'last_name',  $secondname) ;
         update_user_meta( $new_user_id, 'phone' , $userphone) ;
         update_user_meta( $new_user_id, 'skype' , $userskype) ;
         update_user_meta( $new_user_id, 'title', $usertitle) ;
         update_user_meta( $new_user_id, 'custom_picture',$profile_image_url);
         update_user_meta( $new_user_id, 'small_custom_picture',$profile_image_url_small);
         update_user_meta( $new_user_id, 'mobile' , $usermobile) ;
         update_user_meta( $new_user_id, 'facebook' , $userfacebook) ;
         update_user_meta( $new_user_id, 'twitter' , $usertwitter) ;
         update_user_meta( $new_user_id, 'agent_custom_data' , $agent_fields_array) ;
         update_user_meta( $new_user_id, 'linkedin' , $userlinkedin) ;
         update_user_meta( $new_user_id, 'pinterest' , $userpinterest) ;
         update_user_meta( $new_user_id, 'instagram' , $userinstagram) ;
         update_user_meta( $new_user_id, 'website' , $userurl) ;
         update_user_meta( $new_user_id, 'user_estate_role', 2) ;
         update_user_meta( $new_user_id, 'agent_member' , $agent_member) ;
         update_user_meta( $new_user_id, 'agent_address' , $agent_address) ;

         update_user_meta( $new_user_id, 'agent_youtube' , $agent_youtube) ;
         update_user_meta( $new_user_id, 'agent_tiktok' , $agent_tiktok) ;
         update_user_meta( $new_user_id, 'agent_telegram' , $agent_telegram) ;
         update_user_meta( $new_user_id, 'agent_vimeo' , $agent_vimeo) ;
         update_user_meta( $new_user_id, 'agent_private_notes' , $agent_private_notes) ;


         if($is_agent_edit!=1){
             $post = array(
                 'post_title'        => $firstname.' '.$secondname,
                 'post_status'       => 'publish',
                 'post_type'         => 'estate_agent' ,
                 'author'            => $userID ,
                 'post_content'      =>  $about_me
             );

             $new_agent_id =  wp_insert_post($post );
             update_post_meta( $new_agent_id, 'user_meda_id', $new_user_id);
             update_user_meta( $new_user_id, 'user_agent_id', $new_agent_id);

             $current_agent_list = (array)get_user_meta($userID,'current_agent_list',true);
             if(!is_array($current_agent_list)){
                 $current_agent_list = array();
             }
             $current_agent_list[]=$new_user_id;
             update_user_meta( $userID, 'current_agent_list',array_unique( $current_agent_list) );
         }else{
             $post = array(
                 'ID'                =>  $agent_id,
                 'post_title'        =>  $firstname.' '.$secondname,
                 'post_content'      =>  $about_me
             );
             wp_update_post( $post );
             $new_agent_id   =   $agent_id;
         }

 		      update_post_meta($new_agent_id, 'agent_custom_data',   $agent_fields_array);

         update_post_meta($new_agent_id, 'first_name',   $firstname);
         update_post_meta($new_agent_id, 'last_name',   $secondname);
         update_post_meta($new_agent_id, 'agent_email',   $useremail);
         update_post_meta($new_agent_id, 'agent_phone',   $userphone);
         update_post_meta($new_agent_id, 'agent_mobile',  $usermobile);
         update_post_meta($new_agent_id, 'agent_skype',   $userskype);
         update_post_meta($new_agent_id, 'agent_position',  $usertitle);
         update_post_meta($new_agent_id, 'agent_facebook',   $userfacebook);
         update_post_meta($new_agent_id, 'agent_twitter',   $usertwitter);
         update_post_meta($new_agent_id, 'agent_linkedin',   $userlinkedin);
         update_post_meta($new_agent_id, 'agent_pinterest',   $userpinterest);
         update_post_meta($new_agent_id, 'agent_instagram',   $userinstagram);
         update_post_meta($new_agent_id, 'agent_website',   $userurl);
         update_post_meta($new_agent_id, 'agent_member',   $agent_member);
         update_post_meta($new_agent_id, 'agent_address',   $agent_address);
         set_post_thumbnail($new_agent_id, $profile_image_url_small );


         $agent_category           =   get_term( $agent_category_submit, 'property_category_agent');
         if(isset($agent_category->term_id)){
             $agent_category_submit  =   $agent_category->name;
         }else{
             $agent_category_submit=-1;
         }

         if( isset($agent_category_submit) && $agent_category_submit!='none' ){
             wp_set_object_terms($new_agent_id,$agent_category_submit,'property_category_agent');
         }

         //---

         $agent_category           =   get_term( $agent_action_submit, 'property_action_category_agent');
         if(isset($agent_category->term_id)){
             $agent_action_submit  =   $agent_category->name;
         }else{
             $agent_action_submit=-1;
         }

         if( isset($agent_action_submit) && $agent_action_submit!='none' ){
             wp_set_object_terms($new_agent_id,$agent_action_submit,'property_action_category_agent');
         }


         if( isset($agent_city) && $agent_city!='none' ){
             wp_set_object_terms($new_agent_id,$agent_city,'property_city_agent');
         }

         if( isset($agent_county) && $agent_county!='none' ){
             wp_set_object_terms($new_agent_id,$agent_county,'property_county_state_agent');
         }

         if( isset($agent_area) && $agent_area!='none' ){
             wp_set_object_terms($new_agent_id,$agent_area,'property_area_agent');
         }

         if( empty($errors) && $is_agent_edit!=1 ){
             /// to do
             $arguments=array(
                 'user_profile'      =>  $user_name,
             );
             wpestate_select_email_type(get_option('admin_email'),'agent_added',$arguments);
             echo json_encode(array('added'=>true, 'mesaj'=> esc_html__('Agent was added... You will be redirected to your agent list...','wpresidence') ));
         }else{
             $arguments=array(
                 'user_profile'      =>  $user_name,
             );
             wpestate_select_email_type(get_option('admin_email'),'agent_update_profile',$arguments);
             echo json_encode(array('added'=>true, 'mesaj'=> esc_html__('Agent profile edited','wpresidence') ));
         }





         die();


    }
endif; // end   wpestate_ajax_update_profile



/*
*
* Filter invoices
*
*/



/**
 * Invoice AJAX Filter Handler
 *
 * Handles AJAX requests to filter invoice listings based on date range,
 * invoice type, and payment status.
 *
 * @package WpResidence
 * @subpackage Dashboard/Invoices
 * @since 1.0
 */

add_action('wp_ajax_wpestate_ajax_filter_invoices', 'wpestate_ajax_filter_invoices');

if (!function_exists('wpestate_ajax_filter_invoices')):
    /**
     * Filter invoices based on AJAX parameters
     *
     * @return void Outputs JSON with filtered results
     */
    function wpestate_ajax_filter_invoices() {
        // Verify nonce and user authentication
        check_ajax_referer('wpestate_invoices_actions', 'security');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('unauthorized_access');
        }
        
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        
        if ($userID === 0) {
            wp_send_json_error('invalid_user');
        }

        // Sanitize input parameters
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        // Build meta query
        $meta_query = array();
        
        // Add invoice type filter
        if (!empty($type)) {
            $meta_query[] = array(
                'key'     => 'invoice_type',
                'value'   => $type,
                'type'    => 'char',
                'compare' => 'LIKE'
            );
        }
        
        // Add payment status filter
        if (!empty($status)) {
            $meta_query[] = array(
                'key'     => 'pay_status',
                'value'   => $status,
                'type'    => 'numeric',
                'compare' => '='
            );
        }

        // Build date query

        
        if (!empty($start_date)) {
            $date_query['after'] = $start_date;
        }
        
        if (!empty($end_date)) {
            $date_query['before'] = $end_date;
        }

        // Setup WP_Query arguments
        $args = array(
            'post_type'      => 'wpestate_invoice',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'author'         => $userID,
            'meta_query'     => $meta_query,
            'date_query'     => $date_query
        );

        // Get currency settings
        $wpestate_currency       =   esc_html ( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency =   esc_html ( wpresidence_get_option('wp_estate_where_currency_symbol', '') );

        // Execute query and gather results
        $prop_selection = new WP_Query($args);
        $total_confirmed = 0;
        
        ob_start();
        
        while ($prop_selection->have_posts()): 
            $prop_selection->the_post();
            
            $invoiceID = get_the_ID();
            
            // Include invoice template
            include(locate_template('templates/dashboard-templates/invoice_listing_unit.php'));
            
            // Calculate totals
            $price = floatval(get_post_meta($invoiceID, 'item_price', true));
            $total_confirmed += $price;
        endwhile;
        
        $templates = ob_get_contents();
        ob_end_clean();
        
        wp_reset_postdata();

        // Prepare and send response
        $response = array(
            'args '=>  $args ,
            'results' => $templates,
            'invoice_confirmed' => wpestate_show_price_custom_invoice($total_confirmed)
        );

        wp_send_json_success($response);
    }
endif;



/**
 * Displays the messages list in the user dashboard inbox
 * 
 * Retrieves and displays messages where the current user is either the sender
 * or recipient, showing only the first message of each conversation thread.
 * Messages marked as deleted by the user are excluded.
 *
 * @package WpResidence
 * @subpackage Dashboard
 * @param int $userID The ID of the current user
 * @return string HTML output of the messages list
 * @since WpResidence 1.0
 */

if (!function_exists('wpestate_dashboard_inbox_list')):
    function wpestate_dashboard_inbox_list($userID) {
        // Sanitize input
        $userID = absint($userID);
        
        $current_user = wp_get_current_user();
        // Define query arguments for message retrieval
        $args = array(
            'post_type'      => 'wpestate_message',
            'post_status'    => 'publish',
            'paged'          => 1,
            'posts_per_page' => 80,
            'order'          => 'DESC',
            'meta_query'     => array(
                'relation' => 'AND',
                // Check if user is sender or recipient
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'message_to_user',
                        'value'   => $userID,
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'message_from_user',
                        'value'   => $userID,
                        'compare' => '='
                    ),
                ),
                // Only get first message in thread
                array(
                    'key'     => 'first_content',
                    'value'   => 1,
                    'compare' => '='
                ),
                // Exclude deleted messages
                array(
                    'key'     => 'delete_destination' . $userID,
                    'value'   => 1,
                    'compare' => '!='
                ),
            )
        );

        // Execute query to get messages
        $message_selection = new WP_Query($args);

        // Start output buffering for better performance
        ob_start();
        
        // Output table header
        ?>
        <div class="wpestate_dashboard_table_list_header d-none d-md-flex row">
            <div class="col-md-3"><?php esc_html_e('Started by', 'wpresidence'); ?></div>
            <div class="col-md-4"><?php esc_html_e('Subject', 'wpresidence'); ?></div>
            <div class="col-md-2"><?php esc_html_e('Date', 'wpresidence'); ?></div>
            <div class="col-md-3"><?php esc_html_e('Action', 'wpresidence'); ?></div>
        </div>
        <?php

        // Loop through messages and display each one
        while ($message_selection->have_posts()):
            $message_selection->the_post();
            $messageID = get_the_ID();
            include(locate_template('templates/dashboard-templates/message-listing-unit.php'));
        endwhile;

        // Reset WordPress query
        wp_reset_postdata();

        // Return the buffered output
        return ob_get_clean();
    }
endif;
?>