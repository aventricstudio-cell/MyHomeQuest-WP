<?php
// File: admin-sections/wpresidence-api.php



$custom_fields =   Redux::get_option($opt_name, 'wpestate_custom_fields_list');


$custom_fields_string='';
$i=0;
if (!empty($custom_fields)) {
	while ($i< count($custom_fields['add_field_name'])) {
		$name =   $custom_fields['add_field_name'][$i];
		$label=   $custom_fields['add_field_label'][$i];

		//    $slug =   sanitize_key ( str_replace(' ','_',$name) );
		if (function_exists('wpestate_limit45')) {
			$slug = wpestate_limit45(sanitize_title($name));
		} else {
			$slug = sanitize_title($name);
		}
		$slug         =   sanitize_key($slug);

		
		$label = stripslashes($label);

		if ($label!='' && $slug!='') {
			$custom_fields_string.= esc_html__('For Custom Field with label: ','wpresidence-core').$label.esc_html__(' use the following slug in API: ','wpresidence-core').$slug.'</br>';
		}
		$i++;
	}
}
if(!$custom_fields_string){
    $custom_fields_string = esc_html__('No custom fields defined yet. Please go to General Settings and add custom fields.','wpresidence-core');
} 

Redux::setSection($opt_name, array(
    'title'      => __('WpResidence API', 'wpresidence-core'),
    'id'         => 'developers_custom_tab',
    'subsection' => false,
    'fields'     => array(
        array(
			'id'       => 'wp_estate_display_cache_metabox',
			'type'     => 'button_set',
			'title'    => __( 'Display Cached data as metabox? ', 'wpresidence-core' ),
			'subtitle' => __( 'The option "Yes" will display cached data for a estate_property post type in metabox format.', 'wpresidence-core' ),
			'options'  => array( 
						'no'  => 'no',
						'yes' => 'yes'
						),
			'default'  => 'no',
        ),
        array(
			'id'       => 'wp_estate_enable_api',
			'type'     => 'button_set',
			'title'    => __( 'Enable WpResidence Theme API', 'wpresidence-core' ),
			'subtitle' => __( 'The option "Yes" will enable the wpresidence api. Please note you will need to install the "JWT Authentication for WP-API" plugin.', 'wpresidence-core' ),
			'options'  => array(
						'no'  => 'no',
						'yes' => 'yes'
						),
			'default'  => 'no',
		),
		array(
            'id'     => 'jwt-info-normal-api-link',
            'type'   => 'info',
            'required' => array('wp_estate_enable_api','=','yes'),
            'notice' => false,
            'desc'   => 'API Documentation: <a href="https://www.postman.com/universal-eclipse-339362/wpresidence/documentation/3j1fc0t/wpresidence-api" target="_blank">Postman Api Documentation</a>', 
		      ),
        array(
            'id'     => 'jwt-info-normal',
            'type'   => 'info',
            'required' => array('wp_estate_enable_api','=','yes'),
            'notice' => false,
            'desc'   => defined('JWT_AUTH_SECRET_KEY')
                ? 'JWT_AUTH_SECRET_KEY is defined with the value: ' . JWT_AUTH_SECRET_KEY
                : 'JWT_AUTH_SECRET_KEY is not defined in wp-config.php - The method of authentication for the API is not set up. Please follow the instructions in the help file to set up the API.',
        ),
        array(
            'id'     => 'jwt-info-slugs',
            'required' => array('wp_estate_enable_api','=','yes'),
            'type'   => 'content',
            'content' => $custom_fields_string
        ),
     
    ),
));