<?php
require_once get_theme_file_path('/libs/plugins/class-tgm-plugin-activation.php');

///////////////////////////////////////////////////////////////////////////////////////////
/////// Required Plugins
///////////////////////////////////////////////////////////////////////////////////////////


function wpestate_return_requred_plugins(){
        $plugins = array(
                array(
                    'name'     			=> 'WPResidence Core functionality',
                    'slug'     			=> 'wpresidence-core',
                    'source'   			=> get_theme_file_uri ('/libs/plugins/wpresidence-core.zip'),
                    'required' 			=> true,
                    'version' 			=> '5.5.0',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                    'description'               =>  esc_html('Core Functionality for WPResidence Theme','wpresidence'),
                     'image'                    =>  esc_url(get_theme_file_uri('/img/residence-icon_settings.png')) 
		),
             

                array(
                    'name'     			=> 'WPResidence Elementor Addon',
                    'slug'     			=> 'residence-elementor',
                    'source'   			=> get_theme_file_uri ('/libs/plugins/residence-elementor.zip'),
                    'required' 			=> true,
                    'version' 			=> '5.5.0',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                     'description'               =>  esc_html('Elementor Blocks for WPResidence Theme','wpresidence'),
                       'image'                    =>  esc_url(get_theme_file_uri('/img/residence-icon_settings.png')) 
		),
                array(
                        'name'     			=> 'WPResidence Elementor Design Studio',
                        'slug'     			=> 'residence-studio',
                        'source'   			=> get_theme_file_uri ('/libs/plugins/residence-studio.zip'),
                        'required' 			=> true,
                        'version' 			=> '5.5.0',
                        'force_activation' 		=> false,
                        'force_deactivation' 	=> false,
                        'external_url' 		=> '',
                         'description'               =>  esc_html('Build your own templates for WPresidence with Elementor','wpresidence'),
                           'image'                    =>  esc_url(get_theme_file_uri('/img/residence-icon_settings.png')) 
                ),
                   array(
                    'name'     			=> 'WPResidence Gutenberg Blocks functionality',
                    'slug'     			=> 'residence-gutenberg',
                    'source'   			=> get_theme_file_uri ('/libs/plugins/residence-gutenberg.zip'),
                    'required' 			=> false,
                    'version' 			=> '1.50.1',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                     'description'               =>  esc_html('Gutenberg Blocks for WPResidence Theme','wpresidence'),
                       'image'                    =>  esc_url(get_theme_file_uri('/img/residence-icon_settings.png')) 
		),
                array(
                    'name'     			=> 'WPEstate CRM',
                    'slug'     			=> 'wpestate-crm',
                    'source'   			=> get_theme_file_uri ('/libs/plugins/wpestate-crm.zip'),
                    'required' 			=> true,
                    'version' 			=> '5.2.1',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                     'description'               =>  esc_html('CRM plugin for WpResidence Theme','wpresidence'),
                          'image'                    =>  esc_url(get_theme_file_uri('/img/residence-icon_settings.png')) 
		),
		array(
                    'name'     			=> 'Revolution Slider',
                    'slug'     			=> 'revslider',
                    'source'   			=> 'https://plugins.wpestate.org/sliderrevolution-wordpress.zip',
                    'required' 			=> false,
                    'version' 			=> '6.7.41',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                      'description'             =>  esc_html('Slider Plugin','wpresidence'),
                'image'                    =>  esc_url(get_theme_file_uri('/img/revolution-slider.png')) 

                    
		),
                array(
                    'name'     			=> 'WPBakery Visual Composer',
                    'slug'     			=> 'js_composer',
                    'source'   			=> 'https://plugins.wpestate.org/js_composer.zip',
                    'required' 			=> false,
                    'version' 			=> '8.7.2',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                    'description'             =>  esc_html('WordpPress Builder','wpresidence'),
                    'image'                    =>  esc_url(get_theme_file_uri('/img/bakery.png')) 
		),
         
                array(
                    'name'     			=> 'Elementor',
                    'slug'     			=> 'elementor',
                    'source'   			=> 'https://downloads.wordpress.org/plugin/elementor.3.35.6.zip',
                    'required' 			=> false,
                    'version' 			=> '3.35.6',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                    'description'             =>  esc_html('WordPress Builder','wpresidence'),
                    'image'                    =>  esc_url(get_theme_file_uri('/img/elementor-logo.png')) 
		),
                array(
                    'name'     			=> 'One Click Demo Import',
                    'slug'     			=> 'one-click-demo-import',
                    'source'   			=> "https://downloads.wordpress.org/plugin/one-click-demo-import.3.4.0.zip",
                    'required' 			=> true,
                    'version' 			=> '3.4.0',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                        'description'             =>  esc_html('For Importing the WPResidence Demos','wpresidence')
		),
                array(
                    'name'     			=> 'Envato Market',
                    'slug'     			=> 'envato-market',
                    'source'   			=> 'https://goo.gl/pkJS33',
                    'required' 			=> true,
                    'version' 			=> '2.0.13',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                        'description'             =>  esc_html('Use this plugin in order to updated to the latest version','wpresidence'),
                        'image'                    =>  esc_url(get_theme_file_uri('/img/envato.png')) 
		),      
                
                array(
                    'name'     			=> 'MLS Import - Import MLS listings in your website',
                    'slug'     			=> 'mlsimport',
                    'source'   			=> 'https://downloads.wordpress.org/plugin/mlsimport.6.1.10.zip',
                    'required' 			=> false,
                    'version' 			=> '6.1.10',
                    'force_activation' 		=> false,
                    'force_deactivation' 	=> false,
                    'external_url' 		=> '',
                        'description'             =>  esc_html('Import MLS listings in your website - USA, Canada and Mexico','wpresidence')
            )   , array(
                    'name'     			=> 'SVG Support for Demo Import',
                    'slug'     			=> 'svg-support',
                    'source'   			=> 'https://downloads.wordpress.org/plugin/svg-support.2.5.14.zip',
                    'required' 			=> false,
                    'version' 			=> '2.5.14',
                    'force_activation'  => false,
                    'force_deactivation'=> false,
                    'external_url' 		=> '',
                'description'             =>  esc_html('It is used during demo import to get the svg icos used by WpResidence demos','wpresidence')
                )

	);
        return $plugins;
}


if( !function_exists('wpestate_required_plugins') ):
function wpestate_required_plugins() {


        $plugins= wpestate_return_requred_plugins();


		$config = array(
		'domain'       		=> 'wpresidence',
		'default_path' 		=> '',
		'parent_slug'           => 'themes.php',
		'menu'         		=> 'install-required-plugins',
		'has_notices'      	=> true,
		'is_automatic'    	=> false,
		'message' 			=> '',
		'strings'      		=> array(
			'page_title'                       			=> esc_html__( 'Install Required Plugins', 'wpresidence' ),
			'menu_title'                       			=> esc_html__( 'Install Plugins' , 'wpresidence' ),
			'installing'                       			=> esc_html__( 'Installing Plugin: %s', 'wpresidence' ),
			'oops'                             			=> esc_html__( 'Something went wrong with the plugin API.', 'wpresidence' ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'wpresidence'  ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'wpresidence'  ), // %1$s = plugin name(s)
			'notice_cannot_install'  				=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'wpresidence'  ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' , 'wpresidence' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' , 'wpresidence' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 				=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' , 'wpresidence' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 					=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'wpresidence'  ), // %1$s = plugin name(s)
			'notice_cannot_update' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' , 'wpresidence' ), // %1$s = plugin name(s)
			'install_link' 					 	=> _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'wpresidence'  ),
			'activate_link' 				  	=> _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'wpresidence'  ),
			'return'                           			=> esc_html__( 'Return to Required Plugins Installer', 'wpresidence' ),
			'plugin_activated'                 			=> esc_html__( 'Plugin activated successfully.', 'wpresidence' ),
			'complete' 						=> esc_html__( 'All plugins installed and activated successfully. %s', 'wpresidence' ),
			'nag_type'						=> 'updated'
		)
	);
tgmpa($plugins, $config);
}
endif; // end   wpestate_required_plugins
?>
