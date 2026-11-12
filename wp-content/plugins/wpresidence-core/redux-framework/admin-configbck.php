<?php
/**
 * ReduxFramework Admin Config File
 * For full documentation, please visit: https://devs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux' ) ) {
	return;
}

// This is your option name where all the Redux data is stored.
$opt_name = 'wprentals_admin';  // YOU MUST CHANGE THIS.  DO NOT USE 'redux_demo' IN YOUR PROJECT!!!

// Uncomment to disable demo mode.
/* Redux::disable_demo(); */  // phpcs:ignore Squiz.PHP.CommentedOutCode

$dir = __DIR__ . DIRECTORY_SEPARATOR;

/*
 * --> Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
 */

// Background Patterns Reader.
$sample_patterns_path = Redux_Core::$dir . '../sample/patterns/';
$sample_patterns_url  = Redux_Core::$url . '../sample/patterns/';
$sample_patterns      = array();

if ( is_dir( $sample_patterns_path ) ) {
	$sample_patterns_dir = opendir( $sample_patterns_path );

	if ( $sample_patterns_dir ) {

		// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
		while ( false !== ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) ) {
			if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
				$name              = explode( '.', $sample_patterns_file );
				$name              = str_replace( '.' . end( $name ), '', $sample_patterns_file );
				$sample_patterns[] = array(
					'alt' => $name,
					'img' => $sample_patterns_url . $sample_patterns_file,
				);
			}
		}
	}
}

// Used to except HTML tags in description arguments where esc_html would remove.
$kses_exceptions = array(
	'a'      => array(
		'href' => array(),
	),
	'strong' => array(),
	'br'     => array(),
	'code'   => array(),
);

/*
 * ---> BEGIN ARGUMENTS
 */

/**
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://devs.redux.io/core/arguments/
 */
$theme = wp_get_theme(); // For use with some settings. Not necessary.

// TYPICAL → Change these values as you need/desire.
$args = array(
	// This is where your data is stored in the database and also becomes your global variable name.
	'opt_name'                  => $opt_name,

	// Name that appears at the top of your panel.
	'display_name'              => $theme->get( 'Name' ),

	// Version that appears at the top of your panel.
	'display_version'           => $theme->get( 'Version' ),

	// Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
	'menu_type'                 => 'menu',

	// Show the sections below the admin menu item or not.
	'allow_sub_menu'            => true,

    'menu_title'           => __( 'WpRentals Options', 'wprentals-core' ),
    'page_title'           => __( 'WpRentals Options', 'wprentals-core' ),

	// Disable to create your own Google fonts loader.
	'disable_google_fonts_link' => false,

	// Show the panel pages on the admin bar.
	'admin_bar'                 => true,

	// Icon for the admin bar menu.
    'admin_bar_icon'       => '',

	// Priority for the admin bar menu.
	'admin_bar_priority'        => 50,

	// Sets a different name for your global variable other than the opt_name.
	'global_variable'           => $opt_name,

	// Show the time the page took to load, etc. (forced on while on localhost or when WP_DEBUG is enabled).
	'dev_mode'                  => false,

	// Enable basic customizer support.
	'customizer'                => true,

	// Allow the panel to open expanded.
	'open_expanded'             => false,

	// Disable the save warning when a user changes a field.
    'disable_save_warn' => true,  

	// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
	'page_priority'             => 1,

	// For a full list of options, visit: https://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters.
	'page_parent'               => 'themes.php',

	// Permissions needed to access the options panel.
	'page_permissions'          => 'manage_options',

	// Specify a custom URL to an icon.
    'menu_icon'            => get_template_directory_uri() . '/img/rentals_icon.png',

	// Force your panel to always open to a specific tab (by id).
	'last_tab'                  => '',

	// Icon displayed in the admin panel next to your menu_title.
	'page_icon'                 => 'icon-themes',

	// Page slug used to denote the panel, will be based off page title, then menu title, then opt_name if not provided.
	'page_slug'                 => $opt_name,

	// On load save the defaults to DB before user clicks save.
	'save_defaults'             => true,

	// Display the default value next to each field when not set to the default value.
	'default_show'              => false,

	// What to print by the field's title if the value shown is default.
	'default_mark'              => '*',

	// Shows the Import/Export panel when not used as a field.
	'show_import_export'        => true,

	// Shows the Options Object for debugging purposes. Show be set to false before deploying.
	'show_options_object'       => true,

	// The time transients will expire when the 'database' arg is set.
	'transient_time'            => 60 * MINUTE_IN_SECONDS,

	// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output.
	'output'                    => true,

	// Allows dynamic CSS to be generated for customizer and google fonts,
	// but stops the dynamic CSS from going to the page head.
	'output_tag'                => true,

	// Disable the footer credit of Redux. Please leave if you can help it.
	'footer_credit'             => '',

	// If you prefer not to use the CDN for ACE Editor.
	// You may download the Redux Vendor Support plugin to run locally or embed it in your code.
	'use_cdn'                   => true,

	// Set the theme of the option panel.  Use 'wp' to use a more modern style, default is classic.
	'admin_theme'               => 'classic',

	// Enable or disable flyout menus when hovering over a menu with submenus.
	'flyout_submenus'           => true,

	// Mode to display fonts (auto|block|swap|fallback|optional)
	// See: https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display.
	'font_display'              => 'swap',

	// HINTS.
	'hints'                     => array(
		'icon'          => 'el el-question-sign',
		'icon_position' => 'right',
		'icon_color'    => 'lightgray',
		'icon_size'     => 'normal',
		'tip_style'     => array(
			'color'   => 'red',
			'shadow'  => true,
			'rounded' => false,
			'style'   => '',
		),
		'tip_position'  => array(
			'my' => 'top left',
			'at' => 'bottom right',
		),
		'tip_effect'    => array(
			'show' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'mouseover',
			),
			'hide' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'click mouseleave',
			),
		),
	),

	// FUTURE → Not in use yet, but reserved or partially implemented.
	// Use at your own risk.
	// Possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
	'database'                  => '',
	'network_admin'             => true,
	'search'                    => true,
);


// ADMIN BAR LINKS → Set up custom links in the admin bar menu as external items.
// PLEASE CHANGE THESE SETTINGS IN YOUR THEME BEFORE RELEASING YOUR PRODUCT!!
// If these are left unchanged, they will not display in your panel!
/*
$args['admin_bar_links'][] = array(
	'id'    => 'redux-docs',
	'href'  => '//devs.redux.io/',
	'title' => __( 'Documentation', 'your-textdomain-here' ),
);

$args['admin_bar_links'][] = array(
	'id'    => 'redux-support',
	'href'  => '//github.com/ReduxFramework/redux-framework/issues',
	'title' => __( 'Support', 'your-textdomain-here' ),
);
*/

// SOCIAL ICONS → Set up custom links in the footer for quick links in your panel footer icons.
// PLEASE CHANGE THESE SETTINGS IN YOUR THEME BEFORE RELEASING YOUR PRODUCT!!
// If these are left unchanged, they will not display in your panel!
$args['share_icons'][] = array(
    'url'   => 'https://www.facebook.com/wpestate/',
    'title' => 'Like us on Facebook',
    'icon'  => 'el el-facebook'
);
$args['share_icons'][] = array(
    'url'   => 'https://twitter.com/wpestate',
    'title' => 'Follow us on X - Twitter',
    'icon'  => 'el el-twitter'
);
$args['share_icons'][] = array(
    'url'   => 'https://www.youtube.com/channel/UC4OAel8_RSDjNgAibtBEDsg',
    'title' => 'Find us on Youtube',
    'icon'  => 'el el-youtube'
);

// Panel Intro text → before the form.
if ( ! isset( $args['global_variable'] ) || false !== $args['global_variable'] ) {
	if ( ! empty( $args['global_variable'] ) ) {
		$v = $args['global_variable'];
	} else {
		$v = str_replace( '-', '_', $args['opt_name'] );
	}

	// translators:  Panel opt_name.
	$args['intro_text'] = '';
} else {
	$args['intro_text'] = '';
}

// Add content after the form.
$args['footer_text'] = '';
Redux::set_args( $opt_name, $args );

/*
 * ---> END ARGUMENTS
 */

/*
 * ---> START HELP TABS
 */
$help_tabs = array(
    array(
        'id'      => 'redux-help-tab-1',
        'title'   => __( 'Theme Information 1', 'wprentals-core' ),
        'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'wprentals-core' )
    ),
    array(
        'id'      => 'redux-help-tab-2',
        'title'   => __( 'Theme Information 2', 'wprentals-core' ),
        'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'wprentals-core' )
    )
);
Redux::set_help_tab( $opt_name, $help_tabs );

// Set the help sidebar.
$content = '<p>' . esc_html__( 'This is the sidebar content, HTML is allowed.', 'your-textdomain-here' ) . '</p>';

Redux::set_help_sidebar( $opt_name, $content );

/*
 * <--- END HELP TABS
 */


/*
 * ---> START Custom WpRentals Fields
 */


 $WpestateFunk = WpestateFunk::get_instance();
    
 $listing_filter_array = array(
    "0" => esc_html__('Default', 'wprentals-core'),
    "1" => esc_html__('Price High to Low', 'wprentals-core'),
    "2" => esc_html__('Price Low to High', 'wprentals-core'),
    "3" => esc_html__('Newest first', 'wprentals-core'),
    "4" => esc_html__('Oldest first', 'wprentals-core'),
    "11" => esc_html__('Newest Edited', 'wprentals-core'),
    "12" => esc_html__('Oldest Edited ', 'wprentals-core'),
    "5" => esc_html__('Bedrooms High to Low', 'wprentals-core'),
    "6" => esc_html__('Bedrooms Low to high', 'wprentals-core'),
    "7" => esc_html__('Bathrooms High to Low', 'wprentals-core'),
    "8" => esc_html__('Bathrooms Low to high', 'wprentals-core'),
);


$default_custom_field   =   array();
$def_add_field_name     =   array('Check-in hour','Check-Out hour','Late Check-in','Optional services','Outdoor facilities','Extra People','Cancellation');
$def_add_field_label        =   array('Check-in hour','Check-Out hour','Late Check-in','Optional services','Outdoor facilities','Extra People','Cancellation');
$def_add_field_order        =   array(1,2,3,4,5,6,7);
$def_add_field_type         =   array('short text','short text','short text','short text','short text','short text','short text');

$default_custom_field['add_field_name']=$def_add_field_name;
$default_custom_field['add_field_label']=$def_add_field_label;
$default_custom_field['add_field_order']=$def_add_field_order;
$default_custom_field['add_field_type']=$def_add_field_type;


$default_feature_list='Kitchen,Internet,Smoking Allowed,TV,Wheelchair Accessible,Elevator in Building,Indoor Fireplace,Heating,Essentials,Doorman,Pool,Washer,Hot Tub,Dryer,Gym,Free Parking on Premises,Wireless Internet,Pets Allowed,Family/Kid Friendly,Suitable for Events,Non Smoking,Phone (booth/lines),Projector(s),Bar / Restaurant,Air Conditioner,Scanner / Printer,Fax';



$pin_fields=array();

$pin_fields[]=array(
			'id'       => 'wp_estate_use_price_pins',
			'type'     => 'button_set',
			'title'    => __( 'Use price Pins?', 'wprentals-core' ),
			'subtitle' => __( 'Use price Pins? (The css class for price pins is "wpestate_marker". Each pin has also a class with the name of the category or action. For example "wpestate_marker apartments sales")', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		);

 $pin_fields[]=array(
			'id'       => 'wp_estate_use_price_pins_full_price',
			'type'     => 'button_set',
			'title'    => __( 'Use Full Price Pins?', 'wprentals-core' ),
			'subtitle' => __( 'If not we will show prices without before and after label and in this format: 5,23m or 6.83k', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		);


$pin_fields[]=array(
			'id'       => 'wp_estate_use_single_image_pin',
			'type'     => 'button_set',
			'title'    => __( 'Use single Image Pin ?', 'wprentals-core' ),
			'subtitle' => __( 'We will use 1 single pins for all markers. This option will decrease the loading time for maps.', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		);

$pin_fields[]=array(
			'id'       => 'wp_estate_single_pin',
			'type'     => 'media',
			'title'    => __( 'Single Pin Marker / Contact page marker', 'wprentals-core' ),
			'subtitle' => __( 'Image size must be 44px x 50px.', 'wprentals-core' ),
		);

 $pin_fields[]=array(
			'id'       => 'wp_estate_cloud_pin',
			'type'     => 'media',
			'title'    => __( 'Cloud Marker Image', 'wprentals-core' ),
			'subtitle' => __( 'Image must be 70px x 70px', 'wprentals-core' ),
		);



if(function_exists('wprentals_add_pins_icons')){
	$pin_fields = wprentals_add_pins_icons(  $pin_fields );
}else{
	$pin_fields=array();
}


$wpestate_listings_sort_options_array=array();
if(function_exists('wpestate_listings_sort_options_array')){
	$wpestate_listings_sort_options_array= wpestate_listings_sort_options_array();
}



if(function_exists('wprentals_redux_font_google')){
    $wprentals_redux_font_google = wprentals_redux_font_google();
}else{
	$wprentals_redux_font_google=array();
}
    
if(function_exists('wprentals_redux_yelp')){
	$wprentals_redux_yelp=wprentals_redux_yelp();
}else{
	$wprentals_redux_yelp=array();
}


$siteurl = 'noreply@'. parse_url( get_site_url(), PHP_URL_HOST );

/*
 * <--- END Custom WpRentals Fields
 */





/*
 * ---> START SECTIONS
 */
if( ! $WpestateFunk->is_registered() ){

	Redux::setSection( $opt_name, array(
		'title' => __( 'General', 'wprentals-core' ),
		'id'    => 'general_settings_sidebar',
		'icon'  => 'el el-adjust-alt'
	) );

	Redux::setSection( $opt_name, array(

		'title'      => __( 'General Settings', 'wprentals-core' ),
		'id'         => 'global_settings_tab',
		'subsection' => true,
		'fields'     => array(
			array(
				  'id'       => 'noticelicense',
				'type'  => 'info',
				'title' => __('You cannot save theme options until you activate the theme. See this link  <a href="https://help.wprentals.org/article/where-is-my-purchase-code/" target="_blank" >link</a> if you don\'t know how to get your license key. Thank you!',  'wprentals_core'),
				'style' => 'warning',
			),
			)
	));

	return;
}


//-> Start General  Section

Redux::setSection( $opt_name, array(
	'title' => __( 'General', 'wprentals-core' ),
	'id'    => 'wp_estate_general_settings_sidebar',
	'icon'  => 'el el-adjust-alt'
) );


Redux::setSection( $opt_name, array(
	'title'      => __( 'General Settings', 'wprentals-core' ),
	'id'         => 'wp_estate_general_settings_sidebar_tab',
	'subsection' => true,
	'fields'     => array(
		  array(
			'id'       => 'wp_estate_general_country',
			'type'     => 'select',
			'title'    => __( 'Country', 'wprentals-core' ),
			'subtitle' => __( 'Select default country', 'wprentals-core' ),
			'options'  =>  wprentals_return_country_array(),
			'default'  => 'United States'

		),

			array(
				'id'       => 'wp_estate_measure_sys',
				'type'     => 'select',
				'title'    => __( 'Measurement Unit', 'wprentals-core' ),
				'subtitle' => __( 'Select the measurement unit you will use on the website', 'wprentals-core' ),
				'options'  => array(
					'ft' => esc_html__( 'square feet - ft²', 'wprentals-core' ),
					'm'  => esc_html__( 'square meters - m²', 'wprentals-core' ),
				),
				'default' => 'ft',
			),
		

		 array(
			'id'       => 'wp_estate_date_lang',
			'type'     => 'select',
			'title'    => __( 'Language for datepicker', 'wprentals-core' ),
			'subtitle' => __( 'Select the language for booking form datepicker and search by date datepicker', 'wprentals-core' ),
			'options'  => array(
						'xx'=> 'default',
						'af'=>'Afrikaans',
						'ar'=>'Arabic',
						'ar-DZ' =>'Algerian',
						'az'=>'Azerbaijani',
						'be'=>'Belarusian',
						'bg'=>'Bulgarian',
						'bs'=>'Bosnian',
						'ca'=>'Catalan',
						'cs'=>'Czech',
						'cy-GB'=>'Welsh/UK',
						'da'=>'Danish',
						'de'=>'German',
						'el'=>'Greek',
						'en-AU'=>'English/Australia',
						'en-GB'=>'English/UK',
						'en-NZ'=>'English/New Zealand',
						'eo'=>'Esperanto',
						'es'=>'Spanish',
						'et'=>'Estonian',
						'eu'=>'Karrikas-ek',
						'fa'=>'Persian',
						'fi'=>'Finnish',
						'fo'=>'Faroese',
						'fr'=>'French',
						'fr-CA'=>'Canadian-French',
						'fr-CH'=>'Swiss-French',
						'gl'=>'Galician',
						'he'=>'Hebrew',
						'hi'=>'Hindi',
						'hr'=>'Croatian',
						'hu'=>'Hungarian',
						'hy'=>'Armenian',
						'id'=>'Indonesian',
						'ic'=>'Icelandic',
						'it'=>'Italian',
						'it-CH'=>'Italian-CH',
						'ja'=>'Japanese',
						'ka'=>'Georgian',
						'kk'=>'Kazakh',
						'km'=>'Khmer',
						'ko'=>'Korean',
						'ky'=>'Kyrgyz',
						'lb'=>'Luxembourgish',
						'lt'=>'Lithuanian',
						'lv'=>'Latvian',
						'mk'=>'Macedonian',
						'ml'=>'Malayalam',
						'ms'=>'Malaysian',
						'nb'=>'Norwegian',
						'nl'=>'Dutch',
						'nl-BE'=>'Dutch-Belgium',
						'nn'=>'Norwegian-Nynorsk',
						'no'=>'Norwegian',
						'pl'=>'Polish',
						'pt'=>'Portuguese',
						'pt-BR'=>'Brazilian',
						'rm'=>'Romansh',
						'ro'=>'Romanian',
						'ru'=>'Russian',
						'sk'=>'Slovak',
						'sl'=>'Slovenian',
						'sq'=>'Albanian',
						'sr'=>'Serbian',
						'sr-SR'=>'Serbian-i18n',
						'sv'=>'Swedish',
						'ta'=>'Tamil',
						'th'=>'Thai',
						'tj'=>'Tajiki',
						'tr'=>'Turkish',
						'uk'=>'Ukrainian',
						'vi'=>'Vietnamese',
						'zh-CN'=>'Chinese',
						'zh-HK'=>'Chinese-Hong-Kong',
						'zh-TW'=>'Chinese Taiwan',
			),
			 'default'   => 'en-GB'
		),
		

		array(
			'id'       => 'wp_estate_delete_orphan',
			'type'     => 'button_set',
			'title'    => __( 'Auto delete orphan listings', 'wprentals-core' ),
			'subtitle' => __( 'Listings that users started to submit but did not complete - cron will run 1 time per day', 'wprentals-core' ),
			'options'  => array(
					'yes'  => 'yes',
					'no' => 'no'
					),
			'default' => 'no'
		),
		
		array(
			'id'       => 'wp_estate_disable_theme_cache',
			'type'     => 'button_set',
			'title'    => __( 'Disable Theme Cache (Keep theme cache on when your site is in production)', 'wprentals-core' ),
			'subtitle' => __( 'Theme Cache will cache only the heavy database queries. Use this feature along classic cache plugins like WpRocket!', 'wprentals-core' ),
			'options'  => array(
							'yes'  => 'yes',
							'no' => 'no'
						),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_google_analytics_code',
			'type'     => 'text',
			'title'    => __( 'Google Analytics Tracking id (ex UA-41924406-1', 'wprentals-core' ),
			'subtitle' => __( 'Google Analytics Tracking id (ex UA-41924406-1)', 'wprentals-core' ),
		),

	),
) );

Redux::setSection( $opt_name, array(
'title'      => __( 'Appearance', 'wprentals-core' ),
'id'         => 'appearance_options_tab',
'subsection' => true,
'fields'     => array(
		array(
			'id'       => 'wp_estate_wide_status',
			'type'     => 'button_set',
			'title'    => __( 'Wide or Boxed?', 'wprentals-core' ),
			'subtitle' => __( 'Choose the theme layout: wide or boxed.', 'wprentals-core' ),
			'options'  => array(
				'1' =>  __( 'wide','wprentals-core'),
				'2' =>  __( 'boxed','wprentals-core')
			),
			'default'  => '1'
		),
		array(
			'id'       => 'wp_estate_prop_no',
			'type'     => 'text',
			'title'    => __( 'Properties List - Properties number per page', 'wprentals-core' ),
			'subtitle' => __( 'Set how many properties to show per page in lists.', 'wprentals-core' ),
			'default'  => '12'
		),
		array(
			'id'       => 'wp_estate_blog_sidebar',
			'type'     => 'button_set',
			'title'    => __( 'Listing Page/Blog Category/Archive Sidebar Position', 'wprentals-core' ),
			'subtitle' => __( 'Where to show the sidebar for blog category/archive list.', 'wprentals-core' ),
			'options'  => array('no sidebar' => 'no sidebar','right' => 'right','left'=>'left'),
			'default'  =>'right'
		),
		array(
			'id'       => 'wp_estate_blog_sidebar_name',
			'type'     => 'select',
			'title'    => __( 'Blog Category/Archive Sidebar', 'wprentals-core' ),
			'subtitle' => __( 'What sidebar to show for blog category/archive list.', 'wprentals-core' ),

			'data'  =>  'sidebars',
			'default'  => 'primary-widget-area'

		),
	
		array(
			'id'       => 'wp_estate_blog_unit',
			'type'     => 'select',
			'title'    => __( 'Blog Card Design', 'wprentals-core' ),
			'subtitle' => __( 'Type Blog Card Design', 'wprentals-core' ),
			'options'  => array(
				1 =>    'type 1 - full row',
				2 =>    'type 2',
				3 =>    'type 3'),
			'data'  =>  'sidebars',
			'default'  => 2

		),
	
	
	
		array(
			'id'       => 'wp_estate_property_list_type',
			'type'     => 'button_set',
			'title'    => __( 'Listing List Type for Taxonomy pages', 'wprentals-core' ),
			'subtitle' => __( 'Select standard or half map style for property taxonomies pages.', 'wprentals-core' ),
			'options'  => array(
				'1' =>  __( 'standard','wprentals-core'),
				'2' =>  __( 'half map','wprentals-core')
			),
			'default'  => '1'
		),

		array(
			'id'       => 'wp_estate_align_style_half',
			'type'     => 'button_set',
			'title'    => __( 'Map position in Half Map template', 'wprentals-core' ),
			'subtitle' => __( 'Select map position in Half Map template', 'wprentals-core' ),
			'options'  =>array(
							'1' => esc_html__( 'Map on the Left','wprentals-core'),
							'2' => esc_html__( 'Map on the Right','wprentals-core')
							),
			'default'  => '1',
		),
		array(
			'id' => 'wp_estate_global_sticky_sidebar',
			'type' => 'button_set',
			'title' => __('Enable Sticky Sidebar for all pages', 'wprentals-core'),
			'subtitle' => __('Enable Sticky Sidebar for all pages', 'wprentals-core'),

			'options' => array(
				'no'=>'no',
				'yes'=>'yes'
			),
			'default' => 'no',
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Logos & Favicon', 'wprentals-core' ),
	'id'         => 'logos_favicon_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_favicon_image',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Your Favicon', 'wprentals-core' ),
			'subtitle' => __( 'Upload site favicon in .ico, .png, .jpg or .gif format', 'wprentals-core' ),
		),

		array(
			'id'       => 'wp_estate_logo_image',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Your Logo', 'wprentals-core' ),
			'subtitle' => __( 'Use the "Upload" button and "Insert into Post" button from the pop up window.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_transparent_logo_image',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Your Transparent Header Logo', 'wprentals-core' ),
			'subtitle' => __( 'Use the "Upload" button and "Insert into Post" button from the pop up window.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_mobile_logo_image',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Mobile/Tablets Logo', 'wprentals-core' ),
			'subtitle' => __( 'Upload mobile logo in jpg or png format.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_logo_image_retina',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Your Retina Logo', 'wprentals-core' ),
			'subtitle' => __( 'To create retina logo, add _2x at the end of name of the original file (for ex logo_2x.jpg)', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_transparent_logo_image_retina',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Your Transparent Retina Logo', 'wprentals-core' ),
			'subtitle' => __( 'To create retina logo, add _2x at the end of name of the original file (for ex logo_2x.jpg)', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_mobile_logo_image_retina',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Your Mobile Retina Logo', 'wprentals-core' ),
			'subtitle' => __( 'To create retina logo, add _2x at the end of name of the original file (for ex logo_2x.jpg)', 'wprentals-core' ),
		),
		
		array(
			'id'       => 'wp_estate_logo_max_height',
			'type'     => 'text',
			'title'    => __( 'Maximum height for the logo in px', 'wprentals-core' ),
			'subtitle' => __( 'Change the maximum height of the logo. Add only a number (ex: 60). Change Header height and sticky header height in Design -> Header Design.', 'wprentals-core' ),
		),

		 array(
			'id'       => 'wp_estate_logo_max_width',
			'type'     => 'text',
			'title'    => __( 'Maximum width for the logo in px', 'wprentals-core' ),
			'subtitle' => __( 'Change the maximum width of the logo. Add only a number (ex: 200).', 'wprentals-core' ),
		),
	),
) );

Redux::setSection($opt_name, array(
	'title' => __('Header', 'wprentals-core'),
	'id' => 'header_settings_tab',
	'subsection' => true,
	'fields' => array(
		 array(
			'id'       => 'wp_estate_show_submit',
			'type'     => 'button_set',
			'title'    => __( 'Show submit listing button in header?', 'wprentals-core' ),
			'subtitle' => __( 'Submit listing will only work with theme register/login.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'yes',
		),
                array(
                       'id'       => 'wp_estate_allow_user_registration',
                       'type'     => 'button_set',
                       'title'    => __( 'Allow user registration?', 'wprentals-core' ),
                       'subtitle' => __( 'Enable or disable user registration.', 'wprentals-core' ),
                       'options'  => array(
                               'yes' => 'yes',
                               'no'  => 'no'
                               ),
                       'default'  => 'yes',
               ),
                array(
                       'id'       => 'wp_estate_show_top_bar_user_login',
                       'type'     => 'button_set',
                       'title'    => __( 'Show user login menu in header?', 'wprentals-core' ),
                       'subtitle' => __( 'Enable or disable the user login / register in header.', 'wprentals-core' ),
                       'options'  => array(
                               'yes' => 'yes',
                               'no'  => 'no'
                               ),
                       'default'  => 'yes',
               ),

               array(
                       'id'       => 'wp_estate_modal_image',
                       'type'     => 'media',
                       'url'      => true,
			'title'    => __( 'Modal Image', 'wprentals-core' ),
			'subtitle' => __( 'Modal Image', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_show_top_bar_user_menu',
			'type'     => 'button_set',
			'title'    => __( 'Show top bar widget menu ?', 'wprentals-core' ),
			'subtitle' => __( 'Enable or disable top bar widget area. If enabled, see this help article to add widgets: ', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/header-widgets/" target = "_blank"> https://help.wprentals.org/article/header-widgets/ </a>',
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'yes',
		),
		array(
			'id'       => 'wp_estate_show_top_bar_mobile_menu',
			'type'     => 'button_set',
			'required' => array('wp_estate_show_top_bar_user_menu','=','yes'),
			'title'    => __( 'Show top bar on mobile devices?', 'wprentals-core' ),
			'subtitle' => __( 'Enable or disable top bar on mobile devices', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
	   


		array(
			'id'       => 'wp_estate_show_menu_dashboard',
			'type'     => 'button_set',
			'title'    => __( 'Show the header menu in user dashboard pages?', 'wprentals-core' ),
			'subtitle' => __( 'Show the header menu in user dashboard pages?', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'yes',
		),
		array(
			'id'       => 'wp_estate_logo_header_type',
			'type'     => 'button_set',
			'title'    => __( 'Header Type?', 'wprentals-core' ),
			'subtitle' => __( 'Select header type.', 'wprentals-core' ),
			'options'  => array(
				'type1' =>  __( 'type1','wprentals-core'),
				'type2' =>  __( 'type2','wprentals-core')
			),
			'default' => 'type1'
		),
		array(
			'id'       => 'wp_estate_logo_header_align',
			'type'     => 'button_set',
			'title'    => __( 'Header Align(Logo Position)?', 'wprentals-core' ),
			'subtitle' => __( 'Select header alignment.', 'wprentals-core' ),
			'options'  => array(
				'left' =>  __( 'left','wprentals-core'),
				'center' =>  __( 'center','wprentals-core'),
				'right' =>  __( 'right','wprentals-core')
			),
			'default'  => 'left'
		),
		array(
			'id'       => 'wp_estate_header_type',
			'type'     => 'button_set',
			'title'    => __( 'Media Header Type?', 'wprentals-core' ),
			'subtitle' => __( 'Select what media header to use globally.', 'wprentals-core' ),
			'options'  => array(
						'0' => 'none',
						'1' =>'image',
						'2' =>'theme slider',
						'3' =>'revolution slider',
						'4' =>'google map'
						),
			'default' => '4'
		),

		array(
			'id'       => 'wp_estate_global_revolution_slider',
			'required'  => array('wp_estate_header_type','=','3'),
			'type'     => 'text',
			'title'    => __( 'Global Revolution Slider', 'wprentals-core' ),
			'subtitle' => __( 'If media header is set to Revolution Slider, type the slider name and save.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_global_header',
			'required'  => array('wp_estate_header_type','=','1'),
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Global Header Static Image', 'wprentals-core' ),
			'subtitle' => __( 'If media header is set to image, add the image below.', 'wprentals-core' ),
		),




		array(
			'id'       => 'wp_estate_user_header_type',
			'type'     => 'button_set',
			'title'    => __( 'Media Header Type for Owners page?', 'wprentals-core' ),
			'subtitle' => __( 'Overwrites the global header type option', 'wprentals-core' ),
			'options'  => array(
						'0' => 'none',
						'1' =>'image',
						'2' =>'theme slider',
						'3' =>'revolution slider',
						'4' =>'google map'
			),
			'default' => '0'
		),

		array(
			'id'       => 'wp_estate_global_revolution_slider_user',
			'required'  => array('wp_estate_user_header_type','=','3'),
			'type'     => 'text',
			'title'    => __( 'Global Revolution Slider', 'wprentals-core' ),
			'subtitle' => __( 'If media header is set to Revolution Slider, type the slider name and save.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_global_header_image_user',
			'required'  => array('wp_estate_user_header_type','=','1'),
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Global Header Static Image', 'wprentals-core' ),
			'subtitle' => __( 'If media header is set to image, add the image below.', 'wprentals-core' ),
		),




		array(
			'id'       => 'wp_estate_header_type_taxonomy',
			'type'     => 'button_set',
			'title'    => __( 'Media Header Type for Taxonomy?', 'wprentals-core' ),
			'subtitle' => __( 'Select what media header to use globally for taxonomies/categories.', 'wprentals-core' ),
			'options'  => array(
				'none',
				'image',
				'theme slider',
				'revolution slider',
				'google map'
				),
			'default'  => 4,
		),

	
		
		
		
		array(
				'id'       => 'wp_estate_header_taxonomy_revolution_slider',
				'type'     => 'text',
				'required'  => array('wp_estate_header_type_taxonomy','=','3'),
				'title'    => __( 'Taxonomy Header -  Revolution Slider', 'wprentals-core' ),
				'subtitle' => __( 'If media header is set to Revolution Slider, type the slider name and save.', 'wprentals-core' ),
		),

		array(
			'id'       => 'wp_estate_header_taxonomy_image',
			'type'     => 'media',
			'url'      => true,
			'required'  => array('wp_estate_header_type_taxonomy','=','1'),
			'title'    => __( 'Taxonomy Header Static Image', 'wprentals-core' ),
			'subtitle' => __( 'If media header is set to image, and no image is added we will use the taxonomy featured image', 'wprentals-core' ),
		),



		array(
			'id'       => 'wp_estate_header_type_blog_post',
			'type'     => 'button_set',
			'title'    => __( 'Media Header Type for Blog Post?', 'wprentals-core' ),
			'subtitle' => __( 'Select what media header to use globally for blog posts', 'wprentals-core' ),
			'options'  => array(
				'none',
				'image',
				'theme slider',
				'revolution slider',
				'google map'
				),
			'default'  => 4,
		),
		
		
		array(
				'id'       => 'wp_estate_header_single_post_revolution_slider',
				'type'     => 'text',
				'required'  => array('wp_estate_header_type_blog_post','=','3'),
				'title'    => __( 'Single Post Header -  Revolution Slider', 'wprentals-core' ),
				'subtitle' => __( 'If media header is set to Revolution Slider, type the slider name and save.', 'wprentals-core' ),
		),

		array(
			'id'       => 'wp_estate_header_single_post_image',
			'type'     => 'media',
			'url'      => true,
			'required'  => array('wp_estate_header_type_blog_post','=','1'),
			'title'    => __( 'Single Post Header Static Image', 'wprentals-core' ),
			'subtitle' => __( 'If media header is set to image, and no image is added we will use the taxonomy featured image', 'wprentals-core' ),
		),



		array(
			'id'       => 'wp_estate_paralax_header',
			'type'     => 'button_set',
			'title'    => __( 'Parallax efect for image/video header media ?', 'wprentals-core' ),
			'subtitle' => __( 'Enable parallax efect for image/video media header.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no' => 'no'
						),
			'default'  => 'no'
		),
		array(
			'id'       => 'wp_estate_use_upload_tax_page',
			'type'     => 'button_set',
			'title'    => __( 'Use uploaded Image for City and Area taxonomy page Header?', 'wprentals-core' ),
			'subtitle' => __( 'Works with Taxonomy set to Standard type', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no' => 'no'
						),
			'default'  => 'no'
		),
		array(
			'id'       => 'wp_estate_wide_header',
			'type'     => 'button_set',
			'title'    => __( 'Wide Header?', 'wprentals-core' ),
			'subtitle' => __( 'Shows the header 100% wide.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default'  => 'no'
		),
		array(
			'id'       => 'wp_estate_transparent_menu',
			'type'     => 'button_set',
			'title'    => __( 'Transparent Menu over Header?', 'wprentals-core' ),
			'subtitle' => __( 'Don\'t use this option with header media none or google maps', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no' => 'no'
						),
			'default'  => 'no'
		),
		array(
			'id'       => 'wp_estate_transparent_menu_listing',
			'type'     => 'button_set',
			'title'    => __( 'For Properties page: Use Transparent Menu over Header?', 'wprentals-core' ),
			'subtitle' => __( 'Overwrites the option for Transparent Menu over Header', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no' => 'no'
						),
			'default'  => 'no'
		),
		array(
			'id'       => 'wp_estate_mobile_sticky_header',
			'type'     => 'button_set',
			'title'    => __( 'Use Sticky mobile header?', 'wprentals-core' ),
			'subtitle' => __( 'Do not use this option with Show top bar on mobile devices? set to YES', 'wprentals-core' ),
			'options'  => array(
				'yes'  => 'yes',
				'no'   => 'no',
				),
			'default'  => 'no',
		),
	),
));

Redux::setSection( $opt_name, array(
	'title'      => __( 'Footer', 'wprentals-core' ),
	'id'         => 'footer_settings_tab',
	'subsection' => true,
	'fields'     => array(
		  array(
			'id'       => 'wp_estate_show_footer_copy',
			'type'     => 'button_set',
			'title'    => __( 'Show Footer Copyright Area?', 'wprentals-core' ),
			'subtitle' => __( 'Show Footer Copyright Area?', 'wprentals-core' ),
			'options'  => array(
				'yes'  => 'yes',
				'no'   => 'no',
				),
			'default'  => 'yes',
		),
		 array(
			'id'       => 'wp_estate_copyright_message',
			'type'     => 'textarea',
			'required'  => array('wp_estate_show_footer_copy','=','yes'),
		  
			'title'		=> __( 'Copyright Message', 'wprentals-core' ),
			'subtitle' => __('Type here the copyright message that will appear in footer. Add only text.', 'wprentals-core'),
			'default'	=> 'Copyright All Rights Reserved &copy; 2019',
		),
		
		array(
			'id'       => 'wp_estate_footer_background',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Background for Footer', 'wprentals-core' ),
			'subtitle' => __( 'Insert background footer image below.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_repeat_footer_back',
			'type'     => 'button_set',
			'title'    => __( 'Repeat Footer background ?', 'wprentals-core' ),
			'subtitle' => __( 'Set repeat options for background footer image.', 'wprentals-core' ),
			'options'  => array(
					'repeat'   => 'repeat',
					'repeat x' => 'repeat x',
					'repeat y' => 'repeat y',
					'no repeat'=> 'no repeat'
					),
			'default'  => 'no repeat'
		),
		array(
			'id'       => 'wp_estate_wide_footer',
			'type'     => 'button_set',
			'title'    => __( 'Wide Footer?', 'wprentals-core' ),
			'subtitle' => __( 'Makes the footer show 100% screen wide.', 'wprentals-core' ),
			'options'  => array(
					'yes'=> 'yes',
					'no' => 'no'
				),
			'default'  => 'no'
		),
		
		 array(
			'id'       => 'wp_estate_footer_type',
			'type'     => 'button_set',
			'title'    => __( 'Footer Type', 'wprentals-core' ),
			'subtitle' => __( 'Footer Type', 'wprentals-core' ),
			'options'  => array(
				'1'  =>  __('4 equal columns','wprentals-core'),
				'2'  =>  __('3 equal columns','wprentals-core'),
				'3'  =>  __('2 equal columns','wprentals-core'),
				'4'  =>  __('100% width column','wprentals-core'),
				'5'  =>  __('3 columns: 1/2 + 1/4 + 1/4','wprentals-core'),
				'6'  =>  __('3 columns: 1/4 + 1/2 + 1/4','wprentals-core'),
				'7'  =>  __('3 columns: 1/4 + 1/4 + 1/2','wprentals-core'),
				'8'  =>  __('2 columns: 2/3 + 1/3','wprentals-core'),
				'9'  =>  __('2 columns: 1/3 + 2/3','wprentals-core'),
				),
			'default'  => '2',
		),
	   
	),
) );




Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_currency/' );
Redux::setSection( $opt_name, array(
	'title'      => __( 'Price & Currency', 'wprentals-core' ),
	'id'         => 'price_curency_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_prices_th_separator',
			'type'     => 'text',
			'title'    => __( 'Price - thousands separator', 'wprentals-core' ),
			'subtitle' => __( 'Set the thousand separator for price numbers.', 'wprentals-core' ),
			'default'  => '.',
		),
		array(
			'id'       => 'wp_estate_currency_label_main',
			'type'     => 'text',
			'title'    => __( 'Currency Symbol', 'wprentals-core' ),
			'subtitle' => __( 'This is used for default listing price currency symbol and default currency symbol in multi currency dropdown', 'wprentals-core' ),
			'default'  =>'USD',
			),
		array(
			'id'       => 'wp_estate_where_currency_symbol',
			'type'     => 'button_set',
			'title'    => __( 'Where to show the currency symbol?', 'wprentals-core' ),
			'subtitle' => __( 'Where to show the currency symbol?', 'wprentals-core' ),
			'options'  =>  array(
					'before' => 'before',
					'after'  => 'after'
				),
			'default'  => 'before'
		),
		array(
			'id'       => 'wp_estate_currency_symbol',
			'type'     => 'text',
			'title'    => __( 'Currency code', 'wprentals-core' ),
			'subtitle' => __( 'Currency code is used for syncing the multi-currency options with Google Exchange, if enabled.', 'wprentals-core' ),
			'default'  => '$'
		),
		array(
			'id'       => 'wp_estate_auto_curency',
			'type'     => 'button_set',
			'title'    => __( 'Enable auto loading of exchange rates from free.currencyconverterapi.com (1 time per day)?', 'wprentals-core' ),
			'subtitle' => __( 'Currency code must be set according to international standards. Complete list is here', 'wprentals-core' ).'<a href="http://www.xe.com/iso4217.php" target="_blank"> http://www.xe.com/iso4217.php </a>',
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				  ),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_currencyconverterapi_api',
			'type'     => 'text',
			'title'    => __( 'Currencyconverterapi.com Api Key', 'wprentals-core' ),
			'subtitle' => __( 'Get the free api key from here https://free.currencyconverterapi.com/free-api-key', 'wprentals-core' ),
			'default'  => '',
		),

		array(
		   'id'       => 'wpestate_currency',
		   'type'     => 'wpestate_currency',
		   'title'    => __( 'Add Currencies for Multi Currency Widget.', 'wprentals-core' ),
		   'class'    => 'class_wpestate_currency',
		   'full_width' => true,

	   ),
	),
) );


Redux::setSection( $opt_name, array(
	'title'      => __( 'Booking Settings', 'wprentals-core' ),
	'id'         => 'booking_settings_tab',
	'subsection' => true,
	'fields'     => array(
		array(
		   'id'       => 'wp_estate_booking_type',
		   'type'     => 'button_set',
		   'title'    => __( 'Select Booking Type', 'wprentals-core' ),
		   'subtitle' => __( 'Select Global Booking Type', 'wprentals-core' ),
		   'options'  => array(
						'1' => __('Per Day for all Listings','wprentals-core'),
						'2' => __('Per Hour for all Listings','wprentals-core'),
						'3' => __('Mixt - Owner chooses price by hour or by day','wprentals-core')
						),
		   'default'  => '1',
		),
		array(
		   'id'       => 'wp_estate_setup_weekend',
		   'type'     => 'button_set',
		   'title'    => __( 'Select Weekend days', 'wprentals-core' ),
		   'subtitle' => __( 'Users can set a different price per day for weekend days', 'wprentals-core' ),
		   'options'  => array(
						'0' => __('Sunday and Saturday','wprentals-core'),
						'1' => __('Friday and Saturday','wprentals-core'),
						'2' => __('Friday, Saturday and Sunday','wprentals-core')
						),
		   'default'  => '0',
		),
		array(
		   'id'       => 'wp_estate_date_format',
		   'type'     => 'button_set',
		   'title'    => __( 'Select Date Format for datepickers', 'wprentals-core' ),
		   'subtitle' => __( 'You can set a dateformat that will be applied for all your datepickers', 'wprentals-core' ),
		   'options'  => array(
						'0' =>'yy-mm-dd',
						'1' =>'yy-dd-mm',
						'2' =>'dd-mm-yy',
						'3' =>'mm-dd-yy',
						'4' =>'dd-yy-mm',
						'5' =>'mm-yy-dd',
						),
		   'default'  => '0',
		),
		   
		
		array(
			'id'       => 'wp_estate_custom_guest_control',
			'type'     => 'button_set',
			'title'    => __( 'Enable advanced guest control?', 'wprentals-core' ),
			'subtitle' => __( 'The advanced guest picker will let you select the number of adults, children or infants ?', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				  ),
			'default'  => 'no',
		),
		
		
		array(
			'id'       => 'wp_estate_guest_dropdown_no',
			'type'     => 'text',
			'title'    => __( 'Maximum Guest number', 'wprentals-core' ),
			'subtitle' => __( 'Set maximum number of guests in guest dropdowns.', 'wprentals-core' ),
			'default'  => '15',
		),
		array(
			'id'       => 'wp_estate_month_no_show',
			'type'     => 'text',
			'title'    => __( 'Maximum Month number(per day booking)', 'wprentals-core' ),
			'subtitle' => __( 'Set maximum number of months to be shown on listing page. 12 is the recommended number. A higher number may result in page slowness.', 'wprentals-core' ),
			'default'  => '12',
		),
		array(
			'id'       => 'wp_estate_week_days',
			'type'     => 'text',
			'title'    => __( 'Your number or nights / hours you wish to use instead of 7days (7hours)', 'wprentals-core' ),
			'subtitle' => __( 'It allows owner to set a difference price per night / hour for longer periods. Changes apply to NEW bookings only.', 'wprentals-core' ),
			'default'  => '7',
		),
		array(
			'id'       => 'wp_estate_month_days',
			'type'     => 'text',
			'title'    => __( 'Your number or nights / hours you wish to use instead of 30days (30hours)', 'wprentals-core' ),
			'subtitle' => __( 'It allows owner to set a difference price per night / hour for longer periods. Changes apply to NEW bookings only.', 'wprentals-core' ),
			'default'  => '30',
		),
	),
) );


$default_custom_field   =   array();
$def_add_field_name     =   array('Check-in hour','Check-Out hour','Late Check-in','Optional services','Outdoor facilities','Extra People','Cancellation');
$def_add_field_label        =   array('Check-in hour','Check-Out hour','Late Check-in','Optional services','Outdoor facilities','Extra People','Cancellation');
$def_add_field_order        =   array(1,2,3,4,5,6,7);
$def_add_field_type         =   array('short text','short text','short text','short text','short text','short text','short text');

$default_custom_field['add_field_name']=$def_add_field_name;
$default_custom_field['add_field_label']=$def_add_field_label;
$default_custom_field['add_field_order']=$def_add_field_order;
$default_custom_field['add_field_type']=$def_add_field_type;




Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_custom_fields_list/' );
Redux::setSection( $opt_name, array(
	'title'      => __( 'Custom Fields', 'wprentals-core' ),
	'id'         => 'custom_fields_tab',
	'subsection' => true,
	'fields'     => array(
		array(
		   'id'       => 'wpestate_custom_fields_list',
		   'type'     => 'wpestate_custom_fields_list',
		   'full_width' => true,
		   'title'    => __( 'Add, edit or delete property custom fields.', 'wprentals-core' ),
		   'default'  => $default_custom_field
	   ),
	),
) );


$default_feature_list='Kitchen,Internet,Smoking Allowed,TV,Wheelchair Accessible,Elevator in Building,Indoor Fireplace,Heating,Essentials,Doorman,Pool,Washer,Hot Tub,Dryer,Gym,Free Parking on Premises,Wireless Internet,Pets Allowed,Family/Kid Friendly,Suitable for Events,Non Smoking,Phone (booth/lines),Projector(s),Bar / Restaurant,Air Conditioner,Scanner / Printer,Fax';

Redux::setSection( $opt_name, array(
	'title'      => __( 'Features & Amenities', 'wprentals-core' ),
	'id'         => 'ammenities_features_tab',
	'subsection' => true,
	'fields'     => array(
		array(
		   'id'       => 'wp_estate_feature_list',
		   'type'     => 'info',
			'desc'   =>  __( 'Starting with v2.6 all features & amenities are converted to taxonomy (category) terms. Manage Features & Amenities from the left sidebar, Listings -> Features & Amenities menu or from Edit Property in wp-admin.', 'wprentals-core' ),


	   ),
		array(
			'id'       => 'wp_estate_show_no_features',
			'type'     => 'button_set',
			'title'    => __( 'Show the Features and Amenities that are not available', 'wprentals-core' ),
			'subtitle' => __( 'Show on property page the features and amenities that are not selected?', 'wprentals-core' ),
			'options'  => array(
						'yes'  => 'yes',
						'no'   => 'no'
				),
			'default'  => 'yes',
		),

	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Listings Labels', 'wprentals-core' ),
	'id'         => 'listing_labels_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_property_adr_text',
			'type'     => 'text',
			'title'    => __( 'Property Address Label', 'wprentals-core' ),
			'subtitle' => __( 'Custom title instead of Property Address Label.', 'wprentals-core' ),
			'default'  => 'Property Address',
		),
		array(
			'id'       => 'wp_estate_property_features_text',
			'type'     => 'text',
			'title'    => __( 'Property Features Label', 'wprentals-core' ),
			'subtitle' => __( 'Custom title instead of Property Features Label.', 'wprentals-core' ),
			'default'  => 'Property Features',
		),
		array(
			'id'       => 'wp_estate_property_description_text',
			'type'     => 'text',
			'title'    => __( 'Property Description Label', 'wprentals-core' ),
			'subtitle' => __( 'Custom title instead of Property Description Label.', 'wprentals-core' ),
			'default'  => 'Property Description',
		),
		array(
			'id'       => 'wp_estate_property_details_text',
			'type'     => 'text',
			'title'    => __( 'Property Details Label', 'wprentals-core' ),
			'subtitle' => __( 'Custom title instead of Property Details Label.', 'wprentals-core' ),
			'default'  => 'Property Details',
		),
		array(
			'id'       => 'wp_estate_property_price_text',
			'type'     => 'text',
			'title'    => __( 'Property Price Label', 'wprentals-core' ),
			'subtitle' => __( 'Custom title instead of Property Price label.', 'wprentals-core' ),
			'default'  => 'Property Price',
		),
		array(
			'id'       => 'wp_estate_sleeping_text',
			'type'     => 'text',
			'title'    => __( 'Sleeping Situation Label', 'wprentals-core' ),
			'subtitle' => __( 'Custom title instead of Sleeping Situation label.', 'wprentals-core' ),
			'default'  => 'Sleeping Situation',
		),
		array(
			'id'       => 'wp_estate_terms_text',
			'type'     => 'text',
			'title'    => __( 'Terms and Conditions Label', 'wprentals-core' ),
			'subtitle' => __( 'Custom title instead of STerms and Conditions label.', 'wprentals-core' ),
			'default'  => 'Terms and Conditions',
		),
		 array(
			'id'       => 'wp_estate_bed_list',
			'type'     => 'text',
			'title'    => __( 'Types of Beds', 'wprentals-core' ),
			'subtitle' => __( 'List of bed types separated by comma', 'wprentals-core' ),
			'default'  => 'King Bed,Queen Bed,Double,Single,Couch',
		),
	),
) );


Redux::setSection( $opt_name, array(
	'title'      => __( 'Theme Slider', 'wprentals-core' ),
	'id'         => 'theme_slider_tab',
	'subsection' => true,
	'fields'     => array(


		array(
			'id'       => 'wp_estate_theme_slider',
			'type'     => 'select',
			'multi'    => true,
			'title'    => __( 'Select Properties ', 'wprentals-core' ),
			'subtitle' => __( 'Select properties for header theme listing slider. For speed reason we show only the first 50 listings. If you want to add other listings, use the field below to select properties by ID. ', 'wprentals-core' ),
			'data'  => 'posts',
						'args'  => array(
							'post_type'         =>  'estate_property',
							 'post_status'       =>  'publish',
							 'posts_per_page'    =>  50,

						),
			// 'options'  => wprentals_return_theme_slider_list(),
		),


		array(
			'id'       => 'wp_estate_theme_slider_manual',
			'type'     => 'text',
			'title'    => __( 'Add Properties in theme slider by ID. Place here the Listings Id separated by comma.', 'wprentals-core' ),
			'subtitle' => __( 'Place here the Listings Id separated by comma. Will Overwrite the above selection!', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_slider_cycle',
			'type'     => 'text',
			'title'    => __( 'Number of milisecons before auto cycling an item', 'wprentals-core' ),
			'subtitle' => __( 'Number of milisecons before auto cycling an item (5000=5sec).Put 0 if you don\'t want to autoslide.', 'wprentals-core' ),
			'default'  => '5000'
		),
		array(
			'id'       => 'wp_estate_theme_slider_type',
			'type'     => 'button_set',
			'title'    => __( 'Design Type?', 'wprentals-core' ),
			'subtitle' => __( 'Select the design type.', 'wprentals-core' ),
			'options'  => array(
						 'type1' => 'type1',
						 'type2' => 'type2'
				),
			'default'  => 'type1',
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Splash Page', 'wprentals-core' ),
	'id'         => 'splash_page_page_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_spash_header_type',
			'type'     => 'select',
			'title'    => __( 'Select the splash page type.', 'wprentals-core' ),
			'subtitle' => __( 'Important: Create also a page with template "Splash Page" to see how your splash settings apply', 'wprentals-core' ),
			'options'  => array(
					'image'       => 'image' ,
					'video'       => 'video',
					'image slider' => 'image slider'
				),
			'default' =>  'image'

		),


		array(
			'id'       => 'wp_estate_splash_slider_gallery',
			'type'     => 'gallery',
			'class'    => 'slider_splash',
			'required' => array('wp_estate_spash_header_type', '=', 'image slider'),
			'title'    => __( 'Slider Images', 'wprentals-core' ),
			'subtitle' => __( 'Slider Images, .png, .jpg or .gif format', 'wprentals-core' ),

		),


		 array(
			'id'       => 'wp_estate_splash_slider_transition',
			'type'     => 'text',
			'class'    => 'slider_splash',
			'required' => array('wp_estate_spash_header_type', '=', 'image slider'),
			'title'    => __( 'Slider Transition', 'wprentals-core' ),
			'subtitle' => __( 'Number of milisecons before auto cycling an item (5000=5sec).Put 0 if you don\'t want to autoslide.', 'wprentals-core' ),

		),




		array(
			'id'       => 'wp_estate_splash_image',
			'type'     => 'media',
			'class'    => 'image_splash',
			'required' => array('wp_estate_spash_header_type', '=', 'image'),
			'title'    => __( 'Splash Image', 'wprentals-core' ),
			'subtitle' => __( 'Splash Image, .png, .jpg or .gif format', 'wprentals-core' ),

		),



		  array(
			'id'       => 'wp_estate_splash_video_mp4',
			'type'     => 'media',
			'class'    => 'video_splash',
			'url'      => true,
			'preview'  => false,
			'mode'     => false,
			'required' => array('wp_estate_spash_header_type', '=', 'video'),
			'title'    => __( 'Splash Video in mp4 format', 'wprentals-core' ),
			'subtitle' => __( 'Splash Video in mp4 format', 'wprentals-core' ),
		),





		array(
			'id'       => 'wp_estate_splash_video_webm',
			'type'     => 'media',
			'class'    => 'video_splash',
			'url'      => true,
			'preview'  => false,
			'mode'     => false,
			'required' => array('wp_estate_spash_header_type', '=', 'video'),
			'title'    => __( 'Splash Video in webm format', 'wprentals-core' ),
			'subtitle' => __( 'Splash Video in webm format', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_splash_video_ogv',
			'type'     => 'media',
			'class'    => 'video_splash',
			'url'      => true,
			'preview'  => false,
			'mode'     => false,
			'required' => array('wp_estate_spash_header_type', '=', 'video'),
			'title'    => __( 'Splash Video in ogv format', 'wprentals-core' ),
			'subtitle' => __( 'Splash Video in ogv format', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_splash_video_cover_img',
			'type'     => 'media',
			'class'    => 'video_splash',
			'required' => array('wp_estate_spash_header_type', '=', 'video'),
			'title'    => __( 'Cover Image for video', 'wprentals-core' ),
			'subtitle' => __( 'Cover Image for videot', 'wprentals-core' ),
		),



		array(
			'id'       => 'wp_estate_splash_overlay_image',
			'type'     => 'media',
			'title'    => __( 'Overlay Image', 'wprentals-core' ),
			'subtitle' => __( 'Overlay Image, .png, .jpg or .gif format', 'wprentals-core' ),
		),

		array(
			'id'       => 'wp_estate_splash_overlay_color',
			'type'     => 'color',
			'title'    => __( 'Overlay Color', 'wprentals-core' ),
			'subtitle' => __( 'Overlay Color', 'wprentals-core' ),
			'transparent' => false,

		),
		array(
			'id'       => 'wp_estate_splash_overlay_opacity',
			'type'     => 'text',
			'title'    => __( 'Overlay Opacity', 'wprentals-core' ),
			'subtitle' => __( 'Overlay Opacity- values from 0 to 1 , Ex: 0.4', 'wprentals-core' ),

		),
		array(
			'id'       => 'wp_estate_splash_page_title',
			'type'     => 'text',
			'title'    => __( 'Splash Page Title', 'wprentals-core' ),
			'subtitle' => __( 'Splash Page Title', 'wprentals-core' ),

		),
		array(
			'id'       => 'wp_estate_splash_page_subtitle',
			'type'     => 'text',
			'title'    => __( 'Splash Page Subtitle', 'wprentals-core' ),
			'subtitle' => __( 'Splash Page Subtitle', 'wprentals-core' ),

		),
		array(
			'id'       => 'wp_estate_splash_page_logo_link',
			'type'     => 'text',
			 'preview'  => false,
			'title'    => __( 'Logo Link', 'wprentals-core' ),
			'subtitle' => __( 'In case you want to send users to another page', 'wprentals-core' ),
		),



	),
) );



Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_custom_url_rewrite/' );
Redux::setSection( $opt_name, array(
	'title'      => __( 'Listing and Owner Links', 'wprentals-core' ),
	'id'         => 'property_rewrite_page_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'     => 'opt-info_links',
			'type'   => 'info',
			'notice' => false,
			'title'   => __( 'You cannot use special characters like "&". After changing the url you may need to wait for a few minutes until WordPress changes all the urls. In case your new names do not update automatically, go to Settings - Permalinks and Save again the "Permalinks Settings" - option "Post name"', 'wprentals-core' )
		),
		 array(
			'id'     => 'opt-info_links2',
			'type'   => 'info',
			'notice' => false,
			'title'   => __( ' DO NOT USE "type" as this name is reserved by WordPress ', 'wprentals-core' ).'<a href="https://codex.wordpress.org/Reserved_Terms" target="_blank">https://codex.wordpress.org/Reserved_Terms</a>'
		),
		array(
			'id'     => 'wp_estate_url_rewrites',
			'type'   => 'wpestate_custom_url_rewrite',
			'notice' => false,
			'full_width'    => true,

		),

	),
) );


Redux::setSection( $opt_name, array(
	'title'      => __( 'Login & Register', 'wprentals-core' ),
	'id'         => 'property_login_register_page_tab',
	'subsection' => true,
	'fields'     => array(
	 
		array(
			'id'       => 'wp_estate_enable_user_pass',
			'type'     => 'button_set',
			'title'    => __( 'Users can type the password on registration form', 'wprentals-core' ),
			'subtitle' => __( 'If no, users will get the auto generated password via email', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_enable_user_phone',
			'type'     => 'button_set',
			'title'    => __( 'Enable mobile phone number in registration form ?', 'wprentals-core' ),
			'subtitle' => __( 'Enable mobile phone number in registration form ?', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
		
		
		array(
			'id'       => 'wp_estate_redirect_users',
			'type'     => 'button_set',
			'title'    => __( 'Redirect users to same page after login?', 'wprentals-core' ),
			'subtitle' => __( 'Login on property page will be redirect on the same page. It is NOT working for social login feature.', 'wprentals-core' ),
			'options'  => array(
				'yes'   => 'yes',
				'no'    => 'no'
			),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_redirect_custom_link',
			'type'     => 'text',
			'required' => array('wp_estate_redirect_users', '=', 'no'),
			'title'    => __( 'Link where users will be redirected after login', 'wprentals-core' ),
			'subtitle' => __( 'If blank users will be redirected to profile page.Login on property page will be redirect on the same page. It is NOT working for social login feature.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_separate_users',
			'type'     => 'button_set',
			'title'    => __( 'Separate users on registration?', 'wprentals-core' ),
			'subtitle' => __( 'If Yes, there will be 2 user types: who can only book and who can rent & book.', 'wprentals-core' ),
			'options'  => array(
				'yes'  => 'yes',
				'no' => 'no'
				),
			'default'  => 'no',
		),
		 array(
			'id'       => 'wp_estate_publish_only',
			'type'     => 'textarea',
			'required' => array('wp_estate_separate_users','=','no'),
			'title'    => __( 'Only these users can publish (separate SUBCRIBERS usernames with ,).', 'wprentals-core' ),
			'subtitle' => __( 'Don\'t use spaces between comma and username. Correct Ex: paula,victoria ', 'wprentals-core' ),
		),
	),
) );










//->STRAT Social & Contact
Redux::setSection( $opt_name, array(
	'title' => __( 'Social & Contact', 'wprentals-core' ),
	'id'    => 'social_contact_sidebar',
	'icon'  => 'el el-address-book'
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Contact Page Details', 'wprentals-core' ),
	'id'         => 'contact_details_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_use_gdpr',
			'type'     => 'button_set',
			'title'    => __( 'Use GDPR Checkbox ?', 'wprentals-core' ),
			'subtitle' => __( 'Use GDPR Checkbox on contact forms?', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
		
		array(
			'id'       => 'wp_estate_show_phone_no_in_contact',
			'type'     => 'button_set',
			'title'    => __( 'Show phone number in contact form?', 'wprentals-core' ),
			'subtitle' => __( 'Show phone number in contact form?', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
		
		
		array(
			'id'       => 'wp_estate_company_name',
			'type'     => 'text',
			'title'    => __( 'Company Name', 'wprentals-core' ),
			'subtitle' => __( 'Company name for contact page', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_co_address',
			'type'     => 'textarea',
			'title'    => __( 'Company Address', 'wprentals-core' ),
			'subtitle' => __( 'Type company address', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_email_adr',
			'type'     => 'text',
			'title'    => __( 'Email', 'wprentals-core' ),
			'subtitle' => __( 'Company email', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_duplicate_email_adr',
			'type'     => 'text',
			'title'    => __( 'Duplicate Email', 'wprentals-core' ),
			'subtitle' => __( 'Send all contact emails to', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_telephone_no',
			'type'     => 'text',
			'title'    => __( 'Telephone', 'wprentals-core' ),
			'subtitle' => __( 'Company phone number.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_mobile_no',
			'type'     => 'text',
			'title'    => __( 'Mobile', 'wprentals-core' ),
			'subtitle' => __( 'Company mobile', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_fax_ac',
			'type'     => 'text',
			'title'    => __( 'Fax', 'wprentals-core' ),
			'subtitle' => __( 'Company fax', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_skype_ac',
			'type'     => 'text',
			'title'    => __( 'Skype', 'wprentals-core' ),
			'subtitle' => __( 'Company Skype', 'wprentals-core' ),
		),

		array(
			'id'       => 'wp_estate_hq_latitude',
			'type'     => 'text',
			'title'    => __( 'Contact Page - Company HQ Latitude', 'wprentals-core' ),
			'subtitle' => __( 'Set company pin location for contact page template. Latitude must be a number (ex: 40.577906).', 'wprentals-core' ),
			'default'  => '40.781711'
		),
		array(
			'id'       => 'wp_estate_hq_longitude',
			'type'     => 'text',
			'title'    => __( 'Contact Page - Company HQ Longitude', 'wprentals-core' ),
			'subtitle' => __( 'Set company pin location for contact page template. Longitude must be a number (ex: -74.155058).', 'wprentals-core' ),
			'default'  => '-73.955927'
		),
	),
) );


Redux::setSection( $opt_name, array(
	'title'      => __( 'Social Accounts', 'wprentals-core' ),
	'id'         => 'social_accounts_tab',
	'subsection' => true,
	'fields'     => array(
		 array(
			'id'       => 'wp_estate_whatsup_link',
			'type'     => 'text',
			'title'    => __( 'WhatsApp phone number', 'wprentals-core' ),
			'subtitle' => __( 'WhatsApp phone number without spaces or signs.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_facebook_link',
			'type'     => 'text',
			'title'    => __( 'Facebook Link', 'wprentals-core' ),
			'subtitle' => __( 'Facebook page url, with https://', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_twitter_link',
			'type'     => 'text',
			'title'    => __( 'Twitter page link', 'wprentals-core' ),
			'subtitle' => __( 'Twitter page link, with https://', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_google_link',
			'type'     => 'text',
			'title'    => __( 'Google+ Link', 'wprentals-core' ),
			'subtitle' => __( 'Google+ page link, with https://', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_linkedin_link',
			'type'     => 'text',
			'title'    => __( 'Linkedin Link', 'wprentals-core' ),
			'subtitle' => __( 'Linkedin page link, with https://', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_pinterest_link',
			'type'     => 'text',
			'title'    => __( 'Pinterest Link', 'wprentals-core' ),
			'subtitle' => __( 'Pinterest page link, with https://', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_instagram_ac',
			'type'     => 'text',
			'title'    => __( 'Instagram Link', 'wprentals-core' ),
			'subtitle' => __( 'Company Instagram', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_youtube_ac',
			'type'     => 'text',
			'title'    => __( 'Youtube Link', 'wprentals-core' ),
			'subtitle' => __( 'Company Youtube', 'wprentals-core' ),
		),
		 array(
			'id'       => 'wp_estate_telegram_link',
			'type'     => 'text',
			'title'    => __( 'Telegram Link', 'wprentals-core' ),
			'subtitle' => __( 'Company Telegram', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_tiktoklink',
			'type'     => 'text',
			'title'    => __( 'TikTok Link', 'wprentals-core' ),
			'subtitle' => __( 'Company TikTok', 'wprentals-core' ),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Social Login', 'wprentals-core' ),
	'id'         => 'social_login_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_social_register_on',
			'type'     => 'button_set',
			'title'    => __( 'Display social login also on register modal window?', 'wprentals-core' ),
			'subtitle' => __( 'Enable or disable social login also on register modal window', 'wprentals-core' ),
			'options'  => array(
						 'yes' => 'yes',
						 'no'  => 'no' 
						),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_facebook_login',
			'type'     => 'button_set',

			'title'    => __( 'Allow login via Facebook?', 'wprentals-core' ),
			'subtitle' => __( 'Enable or disable Facebook login.', 'wprentals-core' ),
			'options'  => array(
						 'yes' => 'yes',
						 'no'  => 'no'
						),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_facebook_api',
			'required' => array('wp_estate_facebook_login','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Facebook Api key', 'wprentals-core' ),
			'subtitle' => __( 'Facebook Api key is required for Facebook login. See this help article before: ', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/facebook-login/" target="_blank">https://help.wprentals.org/article/facebook-login/</a>',
		),
		array(
			'id'       => 'wp_estate_facebook_secret',
			'required' => array('wp_estate_facebook_login','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Facebook Secret', 'wprentals-core' ),
			'subtitle' => __( 'Facebook Secret is required for Facebook login.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_google_login',
			'type'     => 'button_set',
			'title'    => __( 'Allow login via Google?', 'wprentals-core' ),
			'subtitle' => __( 'Enable or disable Google login.', 'wprentals-core' ),
			'options'  => array(
						 'yes' => 'yes',
						 'no'  => 'no'
						),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_google_oauth_api',
			'required' => array('wp_estate_google_login','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Google Oauth Api', 'wprentals-core' ),
			'subtitle' => __( 'Google Oauth Api is required for Google Login. See this help article before: ', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/enable-gmail-google-login/" target="_blank">https://help.wprentals.org/article/enable-gmail-google-login/</a>',
		),
		array(
			'id'       => 'wp_estate_google_oauth_client_secret',
			'required' => array('wp_estate_google_login','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Google Oauth Client Secret', 'wprentals-core' ),
			'subtitle' => __( 'Google Oauth Client Secret is required for Google Login.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_google_api_key',
			'required' => array('wp_estate_google_login','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Google api key', 'wprentals-core' ),
			'subtitle' => __( 'Google api key is required for Google Login.', 'wprentals-core' ),
		),

	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Twitter Login & Widget ', 'wprentals-core' ),
	'id'         => 'twitter_widget_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_twiter_login',
			'type'     => 'button_set',
			'title'    => __( 'Allow login via Twitter?', 'wprentals-core' ),
			'subtitle' => __( 'Allow login via Twitter? (works only over https)', 'wprentals-core' ),
			'options'  => array(
						 'yes' => 'yes',
						 'no'  => 'no'
						),
			'default'  => 'no',
		),
		
		array(
			'id'       => 'wp_estate_twitter_consumer_key',
			'type'     => 'text',
			'title'    => __( 'Twitter consumer_key.', 'wprentals-core' ),
			'subtitle' => __( 'Twitter consumer_key is required for theme Twitter widget or Twitter login. See this help article before: ', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/wp-estate-twitter-widget/" target="_blank">https://help.wprentals.org/article/wp-estate-twitter-widget/</a>',
		),
		
		array(
			'id'       => 'wp_estate_twitter_consumer_secret',
			'type'     => 'text',
			'title'    => __( 'Twitter Consumer Secret', 'wprentals-core' ),
			'subtitle' => __( 'Twitter Consumer Secret is required for theme Twitter widget or Twitter login.', 'wprentals-core' ),
		),
		
		array(
			'id'       => 'wp_estate_twitter_access_token',
			'type'     => 'text',
			'title'    => __( 'Twitter Access Token', 'wprentals-core' ),
			'subtitle' => __( 'Twitter Access Token is required for theme Twitter widget or Twitter login.', 'wprentals-core' ),
		),
		
		array(
			'id'       => 'wp_estate_twitter_access_secret',
			'type'     => 'text',
			'title'    => __( 'Twitter Access Token Secret', 'wprentals-core' ),
			'subtitle' => __( 'Twitter Access Token Secret is required for theme Twitter widget or Twitter login.', 'wprentals-core' ),
		),
		
		array(
			'id'       => 'wp_estate_twitter_cache_time',
			'type'     => 'text',
			'title'    => __( 'Twitter Cache Time', 'wprentals-core' ),
			'subtitle' => __( 'Twitter Cache Time', 'wprentals-core' ),
		),

	),
) );

// -> START Map options
Redux::setSection( $opt_name, array(
	'title' => __( 'Map', 'wprentals-core' ),
	'id'    => 'map_settings_sidebar',
	'icon'  => 'el el-map-marker'
) );

 Redux::setSection( $opt_name, array(
	'title'      => __( 'Map Settings', 'wprentals-core' ),
	'id'         => 'general_map_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'swithc_info',
			'type'     => 'info',
			'title'    => __( 'VERY IMPORTANT: For already published properties, switching from Google Places to OpenStreet Places (or from Openstreet to Google Places) may require adding properties City & address again. The 2 systems can have different names for city, area and country and search by location may not work.', 'wprentals-core' ),


		),

		 array(
			'id'       => 'wp_estate_kind_of_map',
			'type'     => 'button_set',
			'title'    => __( 'What Map System you want to use?', 'wprentals-core' ),
			'subtitle' => __( 'What map system you want to use', 'wprentals-core' ),
			'options'  => array(
						2 => 'open street',
						1  => 'google maps'
						),
			'default'  => 1,
		),



		array(
			'id'       => 'wp_estate_kind_of_places',
			'type'     => 'button_set',
			'title'    => __( 'What Places Api you want to use?', 'wprentals-core' ),
			'subtitle' => __( 'Google Places work only with Google Maps activated.', 'wprentals-core' ),
			'options'  => array(
						3 => 'open street',
						1  => 'google places'
						),
			'default'  => 1,
		),

//            array(
//                'id'       => 'wp_estate_google_lang',
//                'type'     => 'select',
//                'title'    => __( 'Select language for Google Maps and Places', 'wprentals-core' ),
//                'subtitle' => '<p style="color:red;">'.__( 'Google Places work only with Google Maps activated.', 'wprentals-core' ).'</p>',
//                'options'  => array(
//                            'af'=>	'Afrikaans',
//                            'sq'=>	'Albanian',
//                            'am'=>	'Amharic',
//                            'ar'=>	'Arabic',
//                            'hy'=>	'Armenian',
//                            'az'=>	'Azerbaijani',
//                            'eu'=>	'Basque',
//                            'be'=>	'Belarusian',
//                            'bn'=>	'Bengali',
//                            'bs'=>	'Bosnian',
//                            'bg'=>	'Bulgarian',
//                            'my'=>	'Burmese',
//                            'ca'=>	'Catalan',
//                            'zh'=>	'Chinese',
//                            'zh-CN'=>	'Chinese (Simplified)',
//                            'zh-HK'=>	'Chinese (Hong Kong)',
//                            'zh-TW'=>	'Chinese (Traditional)',
//                            'hr'=>	'Croatian',
//                            'cs'=>	'Czech',
//                            'da'=>	'Danish',
//                            'nl'=>	'Dutch',
//                            'en'=>	'English',
//                            'en-AU'=>	'English (Australian)',
//                            'en-GB'=>	'English (Great Britain)',
//                            'et'=>	'Estonian',
//                            'fa'=>	'Farsi',
//                            'fi'=>	'Finnish',
//                            'fil'=>	'Filipino',
//                            'fr'=>	'French',
//                            'fr-CA'=>	'French (Canada)',
//                            'gl'=>	'Galician',
//                            'ka'=>	'Georgian',
//                            'de'=>	'German',
//                            'el'=>	'Greek',
//                            'gu'=>	'Gujarati',
//                            'iw'=>	'Hebrew',
//                            'hi'=>	'Hindi',
//                            'hu'=>	'Hungarian',
//                            'is'=>	'Icelandic',
//                            'id'=>	'Indonesian',
//                            'it'=>	'Italian',
//                            'ja'=>	'Japanese',
//                            'kn'=>	'Kannada',
//                            'kk'=>	'Kazakh',
//                            'km'=>	'Khmer',
//                            'ko'=>	'Korean',
//                            'ky'=>	'Kyrgyz',
//                            'lo'=>	'Lao',
//                            'lv'=>	'Latvian',
//                            'lt'=>	'Lithuanian',
//                            'mk'=>	'Macedonian',
//                            'ms'=>	'Malay',
//                            'ml'=>	'Malayalam',
//                            'mr'=>	'Marathi',
//                            'mn'=>	'Mongolian',
//                            'ne'=>	'Nepali',
//                            'no'=>	'Norwegian',
//                            'pl'=>	'Polish',
//                            'pt'=>	'Portuguese',
//                            'pt-BR'=>	'Portuguese (Brazil)',
//                            'pt-PT'=>	'Portuguese (Portugal)',
//                            'pa'=>	'Punjabi',
//                            'ro'=>	'Romanian',
//                            'ru'=>	'Russian',
//                            'sr'=>	'Serbian',
//                            'si'=>	'Sinhalese',
//                            'sk'=>	'Slovak',
//                            'sl'=>	'Slovenian',
//                            'es'=>	'Spanish',
//                            'es-419'=>	'Spanish (Latin America)',
//                            'sw'=>	'Swahili',
//                            'sv'=>	'Swedish',
//                            'ta'=>	'Tamil',
//                            'te'=>	'Telugu',
//                            'th'=>	'Thai',
//                            'tr'=>	'Turkish',
//                            'uk'=>	'Ukrainian',
//                            'ur'=>	'Urdu',
//                            'uz'=>	'Uzbek',
//                            'vi'=>	'Vietnamese',
//                            'zu'=>	'Zulu',
//
//
//                            ),
//                'default'  => 'en',
//            ),

		array(
			'id'       => 'wp_estate_readsys',
			'type'     => 'button_set',
			'title'    => __( 'Use file reading for pins?', 'wprentals-core' ),
			'subtitle' => __( 'Use file reading for pins? (*recommended for over 200 listings. File reading is faster than mysql reading and improves page speed)', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_map_max_pins',
			'type'     => 'text',
			'title'    => __( 'Maximum number of pins to show on the map.', 'wprentals-core' ),
			'subtitle' => __( 'A high number will increase the response time and server load. Use a number that works for your current hosting situation. Put -1 for all pins.', 'wprentals-core' ),
			'default'  => '25'
		),
		array(
			'id'       => 'wp_estate_api_key',
			'type'     => 'text',
			'title'    => __( 'Google Maps API KEY', 'wprentals-core' ),
			'subtitle' => __( 'The Google Maps JavaScript API v3 REQUIRES an API key to function correctly. Get an APIs Console key and post the code below. You can get it from here: https://developers.google.com/maps/documentation/javascript/tutorial#api_key', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_mapbox_api_key',
			'type'     => 'text',
			'title'    => __( 'MapBox API KEY -  used for tile server', 'wprentals-core' ),
			'subtitle' => __( 'You can get it from here: https://www.mapbox.com/. If you leave it blank we will use the default openstreet server which can be slow', 'wprentals-core' ),
		),
	 

		array(
			'id'       => 'wp_estate_general_latitude',
			'type'     => 'text',
			'title'    => __( 'Starting Point Latitude', 'wprentals-core' ),
			'subtitle' => __( 'Applies for global header media with map. Add only numbers (ex: 40.577906).', 'wprentals-core' ),
			'default'  => '40.781711'
		),
		array(
			'id'       => 'wp_estate_general_longitude',
			'type'     => 'text',
			'title'    => __( 'Starting Point Longitude', 'wprentals-core' ),
			'subtitle' => __( 'Applies for global header media with map. Add only numbers (ex: -74.155058).', 'wprentals-core' ),
			'default'  => '-73.955927'
		),
		array(
			'id'       => 'wp_estate_default_map_zoom',
			'type'     => 'text',
			'title'    => __( 'Default Map zoom (1 to 20)', 'wprentals-core' ),
			'subtitle' => __( 'Applies for global header media with map , except advanced search results, properties list and taxonomies pages.', 'wprentals-core' ),
			'default'  => '15'
		),
		array(
			'id'       => 'wp_estate_pin_cluster',
			'type'     => 'button_set',
			'title'    => __( 'Use Pin Cluster on map', 'wprentals-core' ),
			'subtitle' => __( 'If yes, it groups nearby pins in cluster.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'yes',
		),
		array(
			'id'       => 'wp_estate_zoom_cluster',
			'type'     => 'text',
			'required' => array('wp_estate_pin_cluster', '=', 'yes'),
			'title'    => __( 'Maximum zoom level for Cloud Cluster to appear - Only for Google Maps', 'wprentals-core' ),
			'subtitle' => __( 'Pin cluster disappears when map zoom is less than the value set in here.', 'wprentals-core' ),
			'default'  => '10'
		),
		array(
			'id'       => 'wp_estate_geolocation_radius',
			'type'     => 'text',
			'title'    => __( 'Geolocation Circle over map (in meters)', 'wprentals-core' ),
			'subtitle' => __( 'Controls circle radius value for user geolocation pin. Type only numbers (ex: 400).', 'wprentals-core' ),
			'default'  => '1000'
		),
		array(
			'id'       => 'wp_estate_min_height',
			'type'     => 'text',
			'title'    => __( 'Height of the Google Map when closed', 'wprentals-core' ),
			'subtitle' => __( 'Applies for header google maps when set as global header media type.', 'wprentals-core' ),
			'default'  => '550'
		),
		array(
			'id'       => 'wp_estate_max_height',
			'type'     => 'text',
			'title'    => __( 'Height of Google Map when open', 'wprentals-core' ),
			'subtitle' => __( 'Applies for header google maps when set as global header media type.', 'wprentals-core' ),
			'default'  => '650'
		),
		array(
			'id'       => 'wp_estate_keep_min',
			'type'     => 'button_set',
			'title'    => __( 'Force Google Map at the "closed" size?', 'wprentals-core' ),
			'subtitle' => __( 'Applies for header google maps when set as global header media type, except listing page.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_map_style',
			'type'     => 'textarea',
			'title'    => __( 'Style for Google Map. Use <strong> https://snazzymaps.com/ </strong> to create styles', 'wprentals-core' ),
			'subtitle' => __( 'Copy/paste below the custom map style code.', 'wprentals-core' ),
		),
	),
) );





$pin_fields=array();

$pin_fields[]=array(
			'id'       => 'wp_estate_use_price_pins',
			'type'     => 'button_set',
			'title'    => __( 'Use price Pins?', 'wprentals-core' ),
			'subtitle' => __( 'Use price Pins? (The css class for price pins is "wpestate_marker". Each pin has also a class with the name of the category or action. For example "wpestate_marker apartments sales")', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		);

 $pin_fields[]=array(
			'id'       => 'wp_estate_use_price_pins_full_price',
			'type'     => 'button_set',
			'title'    => __( 'Use Full Price Pins?', 'wprentals-core' ),
			'subtitle' => __( 'If not we will show prices without before and after label and in this format: 5,23m or 6.83k', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		);


$pin_fields[]=array(
			'id'       => 'wp_estate_use_single_image_pin',
			'type'     => 'button_set',
			'title'    => __( 'Use single Image Pin ?', 'wprentals-core' ),
			'subtitle' => __( 'We will use 1 single pins for all markers. This option will decrease the loading time for maps.', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		);

$pin_fields[]=array(
			'id'       => 'wp_estate_single_pin',
			'type'     => 'media',
			'title'    => __( 'Single Pin Marker / Contact page marker', 'wprentals-core' ),
			'subtitle' => __( 'Image size must be 44px x 50px.', 'wprentals-core' ),
		);

 $pin_fields[]=array(
			'id'       => 'wp_estate_cloud_pin',
			'type'     => 'media',
			'title'    => __( 'Cloud Marker Image', 'wprentals-core' ),
			'subtitle' => __( 'Image must be 70px x 70px', 'wprentals-core' ),
		);



if(function_exists('wprentals_add_pins_icons')){
	$pin_fields = wprentals_add_pins_icons(  $pin_fields );
}else{
	$pin_fields=array();
}
Redux::setSection( $opt_name, array(
	'title'      =>     __( 'Pin Management', 'wprentals-core' ),
	'id'         =>     'pin_management_tab',
	'class'      =>     'wprentals_pin_fields',
	'desc'       =>     __( 'Add new Google Maps pins for single actions / single categories. For speed reason, you MUST add pins if you change categories and actions names.'
			. '</br>Use the "Upload" button and "Insert into Post" button from the pop up window.'
			. '</br> Pins retina version must be uploaded at the same time (same folder) as the original pin and the name of the retina file should be with_2x at the end. Help here', 'wprentals-core' ) . '<a href="https://help.wprentals.org/article/retina-pins/" target="_blank">https://help.wprentals.org/article/retina-pins/</a>',
	'subsection' => true,
	'fields'     => $pin_fields,
) );







Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_generate_pins/' );
Redux::setSection( $opt_name, array(
	'title'      => __( 'Generate Data & Pins', 'wprentals-core' ),
	'id'         => 'generare_pins_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_generate_pins',
			'type'     => 'wpestate_generate_pins',

			'title'    => __( 'Generate Pins and Autocomplete data', 'wprentals-core' ),
			'subtitle' => __( 'Generate Pins for Google Map and Autocomplete data for Advanced Search with theme auto-complete enabled', 'wprentals-core' ),
		),
	),
) );



// -> START Design Selection
Redux::setSection( $opt_name, array(
	'title' => __( 'Listing Page', 'wprentals-core' ),
	'id'    => 'listing_page_settings_sidebar',
	'icon'  => 'el el-brush'
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Listing Page Settings', 'wprentals-core' ),
	'id'         => 'property_page_settings_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_listing_page_type',
			'type'     => 'button_set',
			'title'    => __( 'Listing Page Design Type', 'wprentals-core' ),
			'subtitle' => __( 'Select design type for Listing Page .', 'wprentals-core' ),
			'options'  => array(
						'1' => esc_html__( 'Type 1','wprentals-core'),
						'2' => esc_html__( 'Type 2','wprentals-core'),
						'3' => esc_html__( 'Type 3','wprentals-core'),
						'4' => esc_html__( 'Type 4','wprentals-core'),
						'5' => esc_html__( 'Type 5','wprentals-core'),
					),
			'default'  => '1',
		),


		array(
			'id'       => 'wp_estate_replace_booking_form',
			'type'     => 'button_set',
			'title'    => __( 'Show Contact form instead of Booking Form ?', 'wprentals-core' ),
			'subtitle' => __( 'Show Contact form instead of Booking Form ?', 'wprentals-core' ),
			'options'  => array(
						'yes' => esc_html__( 'Yes','wprentals-core'),
						'no' => esc_html__( 'No','wprentals-core')
					),
			'default'  => 'no',
		),


		
		 array(
			'id'       => 'wp_estate_show_booking_terms',
			'type'     => 'button_set',
			  'required'  => array('wp_estate_replace_booking_form','=','no'),
			'title'    => __( 'Show Booking Terms checkbox ?', 'wprentals-core' ),
			'subtitle' => __( 'Guest has to agree with booking terms before doing the booking request', 'wprentals-core' ),
			'options'  => array(
						'yes' => esc_html__( 'Yes','wprentals-core'),
						'no' => esc_html__( 'No','wprentals-core')
					),
			'default'  => 'no',
		),

		array(
			'id'       => 'wp_estate_show_booking_terms_link',
			'required'  => array('wp_estate_replace_booking_form','=','no'),
			'type'     => 'text',
			'title'    => __( 'Url for the Booking Terms page - applies to all listings on site', 'wprentals-core' ),
			'subtitle' => __( 'Url for the Booking Terms page - applies to all listings on site', 'wprentals-core' ),

			'default'  => '',
		),
		
		array(
			'id'       => 'wp_estate_show_booking_terms_label',
			'required'  => array('wp_estate_replace_booking_form','=','no'),
			'type'     => 'text',
			'title'    => __( 'Label for the Booking Terms page - applies to all listings on site', 'wprentals-core' ),
			'subtitle' => __( 'Label for the Booking Terms page - applies to all listings on site', 'wprentals-core' ),

			'default'  => 'I agree to the terms and condiitons',
		),
		


		array(
			'id'       => 'wp_estate_show_map_location',
			'type'     => 'button_set',
			'title'    => __( 'Hide map location and address for unbooked properties?', 'wprentals-core' ),
			'subtitle' => __( 'If "yes" we will not show the address or exact location on property page map.', 'wprentals-core' ),
			'options'  => array(
						'yes' => esc_html__( 'Yes','wprentals-core'),
						'no' => esc_html__( 'No','wprentals-core')
					),
			'default'  => 'no',
		),

	   
	
		array(
			'id'       => 'wp_estate_show_min_nights_calendar',
			'type'     => 'button_set',
			'title'    => __( 'Show Minimum nights in availability calendar?', 'wprentals-core' ),
			'subtitle' => __( 'Show Minimum nights in availability calendar?(the calendar in content , not the booking calendar)', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Listing Page Layout Manager', 'wprentals-core' ),
	'id'         => 'property_page_layout_manager_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wprentals_layout_manager',
			'type'     => 'button_set',
			'title'    => __( 'Enable Layout Manager ?', 'wprentals-core' ),
			'subtitle' => __( 'If yes, you will have the option re-arrange the sections on property page.', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
		
		array(
			'id'       => 'wprentals_hide_description',
			'type'     => 'button_set',
			'title'    => __( 'Hide Default Description Section?', 'wprentals-core' ),
			'subtitle' => __( 'Hide Default Description Section?', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
		
		array(
			'id'       => 'wprentals_hide_default_owner',
			'type'     => 'button_set',
			'title'    => __( 'Hide Default Owner section?', 'wprentals-core' ),
			'subtitle' => __( 'Hide Default Owner section?', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
		  array(
			'id'       => 'wprentals_hide_default_map',
			'type'     => 'button_set',
			'title'    => __( 'Hide Default Map section?', 'wprentals-core' ),
			'subtitle' => __( 'Hide Default Map section?', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
		
		array(
			'id'       => 'wprentals_hide_similar_listing',
			'type'     => 'button_set',
			'title'    => __( 'Hide Default Similar Listing Section?', 'wprentals-core' ),
			'subtitle' => __( 'Hide Default Similar Listing Section?', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'
				),
			'default'  => 'no',
		),
		
	  
		
		array(
			'id'      => 'wprentals_property_layout_tabs',
			'type'    => 'sorter',
			'title'   => 'Property Page Layout Manager',
			'desc'    => 'Drag and drop sections and organize your property page design.',
			'options' => array(
				'enabled'  => array(
					'gallery'               => esc_html__('Gallery', 'wprentals-core'),
					'description'           => esc_html__('Description', 'wprentals-core'),
					'price_details'         => esc_html__('Price Details', 'wprentals-core'),
					'sleeping'              => esc_html__('Sleeping Arrangements', 'wprentals-core'),
					'address'               => esc_html__('Address', 'wprentals-core'),
					'listing_details'       => esc_html__('Listing Details', 'wprentals-core'),
					'features'              => esc_html__('Features', 'wprentals-core'),
					'terms'                 => esc_html__('Terms and Conditions', 'wprentals-core'),
					'nearby'                => esc_html__('What\'s Nearby', 'wprentals-core'),
					'availability'          => esc_html__('Availability', 'wprentals-core'),
					'reviews'               => esc_html__('Reviews', 'wprentals-core'),
					'virtual_tour'          => esc_html__('Virtual Tour', 'wprentals-core'),
					'map'                   => esc_html__('Map', 'wprentals-core'),
					'owner'                 => esc_html__('Owner Section', 'wprentals-core'),
					'similar'               => esc_html__('Similar Listings', 'wprentals-core'),
				),
				'disabled' => array(
				  
				)
			),
		),
		
		array(
			'id'       => 'wp_estate_property_sidebar_sitcky',
			'type'     => 'button_set',
			'title'    => __( 'Use Sticky Sidebar on Listing page', 'wprentals-core' ),
			'subtitle' => __( 'Use Sticky Sidebar on Listing page.', 'wprentals-core' ),
			'options'  => array(
				'yes' => 'yes',
				'no'  => 'no'

				),
			'default'  => 'no',
		),

	   
		)
	));


	
Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_property_page_header/' );

Redux::setSection($opt_name, array(
'title' => __('Listing Icon Area Design ', 'wprentals-core'),
'id' => 'listing_icon_area_design_composer_extra_details_tab',
'subsection' => true,
'fields' => array(
	array(
			'id'       => 'wp_estate_use_custom_icon_area',
			'type'     => 'button_set',
			'title'    => __( 'Use Custom Icon Area?', 'wprentals-core' ),
			'subtitle' => __( 'Use Custom Icon Area?', 'wprentals-core' ),
			'options'  =>  array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),

	array(
		'id'       => 'wp_estate_property_page_header',
		'type'     => 'wpestate_property_page_header',
		'full_width' => true,
		'required' => array('wp_estate_use_custom_icon_area','=','yes'),
		'class'    =>'wpestate_property_page_header',
		'title'    => __( 'Listing Icon Area Design', 'wprentals-core' ),
		'subtitle' => __( 'Add, edit or delete listing custom fields.', 'wprentals-core' ),
		'default'  => ''// 1 = on | 0 = off
	),
	
 
	  
	array(
			'id' => 'wp_estate_property_page_icon_area_font_size',
			'required' => array('wp_estate_use_custom_icon_area','=','yes'),
			'type' => 'typography',
			'title' => __(' Font Control', 'wprentals-core'),
			'subtitle' => __('Set font size,weight or color', 'wprentals-core'),
		   
			'default'     => array(
			'font-weight'  => '500',
			'font-family' => 'Roboto',
			'google'      => true,
			'font-size'   => '14px',
			),
			'color' => true,
			'text-align' => false,
			'units' => 'px',
			'font-style'=>true,
			 'font-family' => true,
			'font-weight'=>true,           
	),
	
	array(
		'id' => 'wp_estate_property_page_icon_area_icon_size',
		'type' => 'text',
		'required' => array('wp_estate_use_custom_icon_area','=','yes'),
		'title' => __(' Image or Icons Size', 'wprentals-core'),
		'subtitle' => __('Image or Icons Size max-height in px', 'wprentals-core'),
		'default' => '17',
	),
	array(
		'id' => 'wp_estate_property_page_icon_area_color',
		'required' => array('wp_estate_use_custom_icon_area','=','yes'),
		'type' => 'color',
		'title' => __('Icon Color- (it is not applicable to images)', 'wprentals-core'),
		'subtitle' => __('For Images, upload the image in the color you wish', 'wprentals-core'),
		'transparent' => false,
	),
	
	array(
		'id' => 'wp_estate_property_page_icon_area_alignment',
		'type' => 'button_set',
		'required' => array('wp_estate_use_custom_icon_area','=','yes'),
		'title' => __('Align options', 'wprentals-core'),
		'subtitle' => __('Manage how to align the fields in the details row.', 'wprentals-core'),
		'options' => array(
			'flex-start' => 'left',
			'flex-end' => 'right',
			'space-between' => 'fill'
		),
		'default' => 'fill',
	),
	
	
	
	
	array(
		'id' => 'wp_estate_property_page_icon_area_image_position',
		'type' => 'button_set',
		'required' => array('wp_estate_use_custom_icon_area','=','yes'),
		'title' => __('Image/Icon position', 'wprentals-core'),
		'subtitle' => __('Manage how to align the icon', 'wprentals-core'),
		'options' => array(
			'row' => 'left',
			'row-reverse' => 'right',
			'column' => 'top',
			'column-reverse'=>'bottom'
		),
		'default' => 'left',
	),
	
	array(
		'id' => 'wp_estate_property_page_icon_area_text_position',
		'type' => 'button_set',
		'required' => array('wp_estate_use_custom_icon_area','=','yes'),
		'title' => __('Text position', 'wprentals-core'),
		'subtitle' => __('Where to place the text?', 'wprentals-core'),
		'options' => array(
			'start' => 'start',
			'end' => 'end',
	   
		),
		'default' => 'start',
	),
	array(
		'id' => 'wp_estate_property_page_icon_area_gap',
		'type' => 'text',
		'required' => array('wp_estate_use_custom_icon_area','=','yes'),
		'title' => __('Gap between details in px', 'wprentals-core'),
		'subtitle' => __('Space between details', 'wprentals-core'),
		
	),
  
)));
	
   





Redux::setSection( $opt_name, array(
	'title'      => __( 'Disclaimer', 'wprentals-core' ),
	'id'         => 'disclaimer_section_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_disclaiment_text',
	 
			'type'     => 'editor',
			'title'    => __( 'Disclaimer Text', 'wprentals-core' ),
			'subtitle' => __( 'Disclaimer Text. You can use the strings %property_address and %propery_id and the theme will replace those with the property address and id. ', 'wprentals-core' ),
		),
		 
	
)));
   
$wpestate_listings_sort_options_array=array();
if(function_exists('wpestate_listings_sort_options_array')){
	$wpestate_listings_sort_options_array= wpestate_listings_sort_options_array();
}

Redux::setSection( $opt_name, array(
	'title'      => __( 'Similar Listings', 'wprentals-core' ),
	'id'         => 'similar_listings_section_tab',
	'subsection' => true,
	'fields'     => array(
			

		array(
				'id'       => 'wp_estate_similar_prop_no',
				'type'     => 'text',
				'title'    => __( 'No of similar properties in property page', 'wprentals-core' ),
				'subtitle' => __( 'Similar listings show when there are other properties from the same area, city, type and category.', 'wprentals-core' ),
				'default'  => '4'
		),

		array(
			'id'       => 'wp_estate_simialar_taxes',
			'type'     => 'select',
			'multi'    => true,
			'title'    => __( 'Select taxonomies for similar listings', 'wprentals-core' ),
			'subtitle' => __( 'Select taxonomies for similar listings( if none is selected we will use property category, property action category and property city)', 'wprentals-core' ),
		
			
			 'options'  => array(
					'property_category' =>'category',
					'property_action_category'=>'action category',
					'property_city'=>'city',
					'property_area'=>'area'
				),

		),
		 array(
			'id'       => 'wp_estate_similar_listins_order',
			'type'     => 'select',
			'title'    => __( 'Select Similar Listings Order', 'wprentals-core' ),
			'subtitle' => __( 'Select Similar Listings Order', 'wprentals-core' ),
			'options'  => $wpestate_listings_sort_options_array,
		),
	
)));











// -> START Design Selection
Redux::setSection( $opt_name, array(
	'title' => __( 'Design', 'wprentals-core' ),
	'id'    => 'design_settings_sidebar',
	'icon'  => 'el el-brush'
) );
Redux::setSection( $opt_name, array(
	'title'      => __( 'General Design Settings', 'wprentals-core' ),
	'id'         => 'general_design_settings_tab',
	'subsection' => true,
	'fields'     => array(
		 array(
		'id' => 'wp_estate_main_grid_content_width',
		'type' => 'text',
		'title' => __('Main Grid Width in px', 'wprentals-core'),
		'subtitle' => __('This option defines the main content width. Default value is 1200px', 'wprentals-core'),
		),
		array(
			'id' => 'wp_estate_main_content_width',
			'type' => 'text',
			'title' => __('Content Width (In Percent)', 'wprentals-core'),
			'subtitle' => __('Using this option you can define the width of the content in percent.Sidebar will occupy the rest of the main content space.', 'wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_elements_border_radius',
			'type'     => 'text',
			'title'    => __( 'Border Corner Radius', 'wprentals-core' ),
			'subtitle' => __( 'Border Corner Radius for unit elements like listing unit or blog unit. Only numbers', 'wprentals-core' ),
		),
		
		array(
			'id'       => 'wp_estate_header_height',
			'type'     => 'text',
			'title'    => __( 'Header Height', 'wprentals-core' ),
			'subtitle' => __( 'Header Height in px', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_sticky_header_height',
			'type'     => 'text',
			'title'    => __( 'Sticky Header Height', 'wprentals-core' ),
			'subtitle' => __( 'Sticky Header Height in px', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_border_bottom_header',
			'type'     => 'text',
			'title'    => __( 'Border Bottom Header Height', 'wprentals-core' ),
			'subtitle' => __( 'Header Border Bottom Height', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_border_bottom_header_color',
			'type'     => 'color',
			'title'    => __( 'Header Border Bottom Color', 'wprentals-core' ),
			'subtitle' => __( 'Header Border Bottom Color', 'wprentals-core' ),
			'transparent'  => false,
		),

	),
) );




Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_custom_field_type3/' );


Redux::setSection( $opt_name, array(
	'title'      => __( 'Listing Card Design', 'wprentals-core' ),
	'id'         => 'listing_card_design_tab',
	'subsection' => true,
	'fields'     => array(
	   array(
			'id'       => 'wp_estate_listing_unit_type',
			'type'     => 'button_set',
			'title'    => __( 'Listing Unit Type', 'wprentals-core' ),
			'subtitle' => __( 'Select Listing Unit Type.</br>Unit type 3 works only with custom fields.', 'wprentals-core' ),
			'options'  =>array(
							'1' => __( 'Type 1','wprentals-core'),
							'2' => __( 'Type 2','wprentals-core'),
							'3' => __( 'Type 3','wprentals-core'),
							'4' => __( 'Type 4','wprentals-core')
						),
			'default'  => '2',
		),

		array(
			'id'       => 'wp_estate_prop_page_new_tab',
			'type'     => 'button_set',
			'title'    => __( 'Open property page in new tab', 'wprentals-core' ),
			'subtitle' => __( 'Open property page in new tab', 'wprentals-core' ),
			'options'  =>array(
						'_blank' =>'yes',
						'_self'  => 'no'
						),
			'default'  => '_self',
		),

		array(
			'id'       => 'wp_estate_prop_list_slider',
			'type'     => 'button_set',
			'title'    => __( 'Use Slider in Listing Unit? (*doesn\'t apply for featured listing unit and listing shortcode list with no space between units)', 'wprentals-core' ),
			'subtitle' => __( 'Enable / Disable the image slider in listing unit (used in lists)', 'wprentals-core' ),
			'options'  =>array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
		
		 array(
			'id'       => 'wp_estate_image_no_slider',
			'type'     => 'text',
			'required'  => array('wp_estate_prop_list_slider','=','yes'),
			'title'    => __( 'Number of images in card unit slider', 'wprentals-core' ),
			'subtitle' => __( 'Set Number of images in card unit slider', 'wprentals-core' ),
		  
			'default'  => '3',
		),
		
		
		array(
			'id'       => 'wp_estate_listing_unit_style_half',
			'type'     => 'button_set',
			'title'    => __( 'Listing Unit Style for Half Map', 'wprentals-core' ),
			'subtitle' => __( 'Select Listing Unit Style for Half Map', 'wprentals-core' ),
			'options'  =>array(
							'1' => esc_html__( 'List','wprentals-core'),
							'2' => esc_html__( 'Grid','wprentals-core')
							),
			'default'  => '1',
		),


		array(
			'id'       => 'wp_estate_custom_listing_fields',
			'type'     => 'wpestate_custom_field_type3',
			'full_width' => true,
			'title'    => __( 'Custom Fields for Unit Type 3', 'wprentals-core' ),
			'subtitle' => __( 'Add, edit or delete listing custom fields.', 'wprentals-core' ),

		),


	),
) );







Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_custom_fields_infobox/' );
Redux::setSection( $opt_name, array(
	'title'      => __( 'Map Marker Infobox Design', 'wprentals-core' ),
	'id'         => 'infobox_design_tab',
	'subsection' => true,
	'fields'     => array(
			array(
			'id'       => 'wp_estate_custom_icons_infobox',
			'type'     => 'button_set',
			'title'    => __( 'Use custom icons on Infobox ?', 'wprentals-core' ),
			'subtitle' => __( 'Use custom icons on Infobox ?', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
		array(
			'id'       =>   'wp_estate_custom_infobox_fields',
			'type'     =>   'wpestate_custom_fields_infobox',
			'title'    => __( 'Custom Fields for Infobox', 'wprentals-core' ),
			'subtitle' => __( 'Add, edit or delete listing custom fields.', 'wprentals-core' ),
			'full_width' => true,

		),
	),
) );









Redux::setSection( $opt_name, array(
	'title'      => __( 'Custom Colors Settings', 'wprentals-core' ),
	'id'         => 'custom_colors_tab',
	'desc'       => __( '***Please understand that we cannot add here color controls for all theme elements & details. Doing that will result in a overcrowded and useless interface. These small details need to be addressed via custom css code', 'wprentals-core' ),
	'subsection' => true,
	'fields'     => array(

		array(
			'id'       => 'wp_estate_on_child_theme',
			'type'     => 'checkbox',
			'title'    => __( 'On save, give me the css code to save in child theme style.css ?', 'wprentals-core' ),
			'subtitle' => __( '*Recommended option', 'wprentals-core' ),
			'desc'     => __( 'If you use this option, you will need to copy / paste the code below and use it in child theme style.css. The colors will NOT change otherwise!', 'wprentals-core' ),
			'default'  => '1'// 1 = on | 0 = off
		),
		 array(
			'id'       => 'wp_estate_on_child_theme_customcss',
			'type'     => 'callback',
			'required'     => array('wp_estate_on_child_theme','=','1'),
			'title'    => __( 'Css Code for Child Theme', 'redux-framework-demo' ),
			'subtitle' =>    __('Copy the above code and add it into your child theme style.css','wprentals-core'),
			'callback' => 'wp_estate_redux_on_child_theme_customcss',
			'class'    => 'wp_estate_redux_on_child_theme_customcss'
		),

		array(
			'id'       => 'wp_estate_main_color',
			'type'     => 'color',
			'title'    => __( 'Main Color', 'wprentals-core' ),
			'subtitle' => __( 'Main Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_background_color',
			'type'     => 'color',
			'title'    => __( 'Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_breadcrumbs_font_color',
			'type'     => 'color',
			'title'    => __( 'Breadcrumbs, Meta and Listing Info Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Breadcrumbs, Meta and Listing Info Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_font_color',
			'type'     => 'color',
			'title'    => __( 'Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_link_color',
			'type'     => 'color',
			'title'    => __( 'Link Color', 'wprentals-core' ),
			'subtitle' => __( 'Link Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_headings_color',
			'type'     => 'color',
			'title'    => __( 'Headings Color', 'wprentals-core' ),
			'subtitle' => __( 'Headings Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_footer_back_color',
			'type'     => 'color',
			'title'    => __( 'Footer Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Footer Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_footer_font_color',
			'type'     => 'color',
			'title'    => __( 'Footer Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Footer Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		  array(
			'id'       => 'wp_estate_widget_title_footer_font_color',
			'type'     => 'color',
			'title'    => __( 'Footer Widget Title Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Footer Widget Title Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_footer_copy_color',
			'type'     => 'color',
			'title'    => __( 'Footer Copyright Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Footer Copyright Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_footer_copy_back_color',
			'type'     => 'color',
			'title'    => __( 'Footer Copyright Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Footer Copyright Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_sidebar_widget_color',
			'type'     => 'color',
			'title'    => __( 'Sidebar Widget Background Color( for "boxed" widgets)', 'wprentals-core' ),
			'subtitle' => __( 'Sidebar Widget Background Color( for "boxed" widgets)', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_sidebar_heading_color',
			'type'     => 'color',
			'title'    => __( 'Sidebar Heading Color', 'wprentals-core' ),
			'subtitle' => __( 'Sidebar Heading Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_sidebar2_font_color',
			'type'     => 'color',
			'title'    => __( 'Sidebar Font color', 'wprentals-core' ),
			'subtitle' => __( 'Sidebar Font color', 'wprentals-core' ),
			'transparent'  => false,
		),

		array(
			'id'       => 'wp_estate_box_content_back_color',
			'type'     => 'color',
			'title'    => __( 'Boxed Content Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Boxed Content Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_box_content_border_color',
			'type'     => 'color',
			'title'    => __( 'Border Color', 'wprentals-core' ),
			'subtitle' => __( 'Border Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_hover_button_color',
			'type'     => 'color',
			'title'    => __( 'Hover Button Color', 'wprentals-core' ),
			'subtitle' => __( 'Hover Button Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		
		array(
			'id'       => 'wp_estate_calendar_back_color',
			'type'     => 'color',
			'title'    => __( 'Calendar- Background Color for day', 'wprentals-core' ),
			'subtitle' => __( 'Calendar- Background Color for day', 'wprentals-core' ),
			'transparent'  => false,
		),
		
		
		array(
			'id'       => 'wp_estate_calendar_font_color',
			'type'     => 'color',
			'title'    => __( 'Calendar- Font Color for day', 'wprentals-core' ),
			'subtitle' => __( 'Calendar- Font Color for day', 'wprentals-core' ),
			'transparent'  => false,
		),
		
		
		 array(
			'id'       => 'wp_estate_calendar_internal_color',
			'type'     => 'color',
			'title'    => __( 'Calendar- Internal booking color', 'wprentals-core' ),
			'subtitle' => __( 'Calendar- Internal booking color', 'wprentals-core' ),
			'transparent'  => false,
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Main Menu Design', 'wprentals-core' ),
	'id'         => 'mainmenu_design_elements_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_header_color',
			'type'     => 'color',
			'title'    => __( 'Header Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Header Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_top_bar_back',
			'type'     => 'color',
			'title'    => __( 'Top Bar Background Color (Header Widget Menu)', 'wprentals-core' ),
			'subtitle' => __( 'Top Bar Background Color (Header Widget Menu)', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_top_bar_font',
			'type'     => 'color',
			'title'    => __( 'Top Bar Font Color (Header Widget Menu)', 'wprentals-core' ),
			'subtitle' => __( 'Top Bar Font Color (Header Widget Menu)', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_menu_font_color',
			'type'     => 'color',
			'title'    => __( 'Top Menu Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Top Menu Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_top_menu_hover_font_color',
			'type'     => 'color',
			'title'    => __( 'Top Menu Hover Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Top Menu Hover Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_active_menu_font_color',
			'type'     => 'color',
			'title'    => __( 'Active Menu Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Active Menu Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_transparent_menu_font_color',
			'type'     => 'color',
			'title'    => __( 'Transparent Header - Top Menu Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Transparent Header - Top Menu Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_transparent_menu_hover_font_color',
			'type'     => 'color',
			'title'    => __( 'Transparent Header - Top Menu Hover Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Transparent Header - Top Menu Hover Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_sticky_menu_font_color',
			'type'     => 'color',
			'title'    => __( 'Sticky Menu Font Color', 'wprentals-core' ),
			'subtitle' => __( 'Sticky Menu Font Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_menu_items_color',
			'type'     => 'color',
			'title'    => __( 'Menu Item Color', 'wprentals-core' ),
			'subtitle' => __( 'Menu Item Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_menu_item_back_color',
			'type'     => 'color',
			'title'    => __( 'Menu Item Back Color', 'wprentals-core' ),
			'subtitle' => __( 'Menu Item Back Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_menu_hover_font_color',
			'type'     => 'color',
			'title'    => __( 'Menu Item hover font color', 'wprentals-core' ),
			'subtitle' => __( 'Menu Item hover font color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_top_menu_font_size',
			'type'     => 'text',
			'title'    => __( 'Top Menu Font Size', 'wprentals-core' ),
			'subtitle' => __( 'Top Menu Font Size', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_menu_item_font_size',
			'type'     => 'text',
			'title'    => __( 'Menu Item Font Size', 'wprentals-core' ),
			'subtitle' => __( 'Menu Item Font Size', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_top_menu_hover_back_font_color',
			'type'     => 'color',
			'transparent'  => false,
			'title'    => __( 'Top Menu Hover Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Top Menu Hover Background Color (*applies on some hover types)', 'wprentals-core' ),
		),
		array(
			'id'     => 'opt-info_hover',
			'type'   => 'info',
			'notice' => false,
			'desc'   => __( ' <img  style="border:1px solid #FFE7E7;margin-bottom:10px;" src="'. get_template_directory_uri().'/img/menu_types.png" alt="logo"/>', 'wprentals-core' )
		),
		array(
			'id'       => 'wp_estate_top_menu_hover_type',
			'type'     => 'button_set',
			'title'    => __( 'Top Menu Hover Type', 'wprentals-core' ),
			'subtitle' => __( 'Top Menu Hover Type', 'wprentals-core' ),
			'options'  =>   array(
						'1'=>'1',
						'2'=>'2',
						'3'=>'3',
						'4'=>'4',
						'5'=>'5',
						'6'=>'6'),
			'default'  => '1',
		),
	),
) );

Redux::setSection( $opt_name, array(
'title'      => __( 'Mobile Menu Colors', 'wprentals-core' ),
'id'         => 'mobile_design_elements_tab',
'subsection' => true,
'fields'     => array(
		array(
			'id'       => 'wp_estate_mobile_header_background_color',
			'type'     => 'color',
			'title'    => __( 'Mobile header background color', 'wprentals-core' ),
			'subtitle' => __( 'Mobile header background color', 'wprentals-core' ),
			'transparent' => false,
		),

		array(
			'id'       => 'wp_estate_mobile_header_icon_color',
			'type'     => 'color',
			'title'    => __( 'Mobile header icon color', 'wprentals-core' ),
			'subtitle' => __( 'Mobile header icon color', 'wprentals-core' ),
			'transparent' => false,
		),

		array(
			'id'       => 'wp_estate_mobile_menu_font_color',
			'type'     => 'color',
			'title'    => __( 'Mobile menu font color', 'wprentals-core' ),
			'subtitle' => __( 'Mobile menu font color', 'wprentals-core' ),
			'transparent' => false,
		),

		array(
			'id'       => 'wp_estate_mobile_menu_hover_font_color',
			'type'     => 'color',
			'title'    => __( 'Mobile menu hover font color', 'wprentals-core' ),
			'subtitle' => __( 'Mobile menu hover font color', 'wprentals-core' ),
			'transparent' => false,
		),

		array(
			'id'       => 'wp_estate_mobile_item_hover_back_color',
			'type'     => 'color',
			'title'    => __( 'Mobile menu item hover background color', 'wprentals-core' ),
			'subtitle' => __( 'Mobile menu item hover background color', 'wprentals-core' ),
			'transparent' => false,
		),

		array(
			'id'       => 'wp_estate_mobile_menu_backgound_color',
			'type'     => 'color',
			'title'    => __( 'Mobile menu background color', 'wprentals-core' ),
			'subtitle' => __( 'Mobile menu background color', 'wprentals-core' ),
			'transparent' => false,
		),

		array(
			'id'       => 'wp_estate_mobile_menu_border_color',
			'type'     => 'color',
			'title'    => __( 'Mobile menu item border color', 'wprentals-core' ),
			'subtitle' => __( 'Mobile menu item border color', 'wprentals-core' ),
			'transparent' => false,
		),

	   ),
) );



Redux::setSection( $opt_name, array(
	'title'      => __( 'Custom CSS', 'wprentals-core' ),
	'id'         => 'custom_css_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_custom_css',
			'type'     => 'ace_editor',
			'title'    => __( 'Custom Css', 'wprentals-core' ),
			'subtitle' => __( 'Overwrite theme css using custom css.', 'wprentals-core' ),
			'mode'     => 'css',
			'theme'    => 'monokai',
			),
	),
) );

if(function_exists('wprentals_redux_font_google')){
$wprentals_redux_font_google = wprentals_redux_font_google();
}else{
	$wprentals_redux_font_google=array();
}

Redux::setSection( $opt_name, array(
	'title'      => __( 'Fonts', 'wprentals-core' ),
	'id'         => 'custom_fonts_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_general_font',
			'type'     => 'typography',
			'title'    => __( 'Main Font', 'wprentals-core' ),
			'subtitle' => __( 'Select Main Font', 'wprentals-core' ),
			'options'  => $wprentals_redux_font_google,
			'default'  =>''
		),
		array(
			'id'       => 'wp_estate_headings_font_subset',
			'type'     => 'text',
			'title'    => __( 'Main Font subset', 'wprentals-core' ),
			'subtitle' => __( 'Select Main Font subset( like greek,cyrillic, etc..)', 'wprentals-core' ),
		),
		array(
		   'id'          => 'h1_typo',
		   'type'        => 'typography',
		   'title'       => esc_html__('H1 Font', 'wprentals-core'),

		   'google'      => true,
		   'font-family' => true,
		   'subsets'     => true,
		   'line-height'=> true,
		   'font-weight'=> true,
		   'font-backup' => false,
		   'text-align'  => false,
		   'text-transform' => false,
		   'font-style' => false,
		   'color'      => false,
		   'units'       =>'px',
		   'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
		   'all_styles'  => true
	   ),

	array(
			'id'          => 'h2_typo',
			'type'        => 'typography',
			'title'       => esc_html__('H2 Font', 'wprentals-core'),
			'google'      => true,
			'font-family' => true,
			'subsets'     => true,
			'line-height'=> true,
			'font-weight'=> true,
			'font-backup' => false,
			'text-align'  => false,
			'text-transform' => false,
			'font-style' => false,
			'color'      => false,
			'units'       =>'px',
			'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
			'all_styles'  => true
		),

	array(
			'id'          => 'h3_typo',
			'type'        => 'typography',
			'title'       => esc_html__('H3 Font', 'wprentals-core'),
			'google'      => true,
			'font-family' => true,
			'subsets'     => true,
			'line-height'=> true,
			'font-weight'=> true,
			'font-backup' => false,
			'text-align'  => false,
			'text-transform' => false,
			'font-style' => false,
			'color'      => false,
			'units'       =>'px',
			'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
			'all_styles'  => true
		),

	array(
		   'id'          => 'h4_typo',
		   'type'        => 'typography',
		   'title'       => esc_html__('H4 Font', 'wprentals-core'),
		   'google'      => true,
		   'font-family' => true,
		   'subsets'     => true,
		   'line-height'=> true,
		   'font-weight'=> true,
		   'font-backup' => false,
		   'text-align'  => false,
		   'text-transform' => false,
		   'font-style' => false,
		   'color'      => false,
		   'units'       =>'px',
		   'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
		   'all_styles'  => true
	   ),

	array(
			'id'          => 'h5_typo',
			'type'        => 'typography',
			'title'       => esc_html__('H5 Font', 'wprentals-core'),
			'google'      => true,
			'font-family' => true,
			'subsets'     => true,
			'line-height'=> true,
			'font-weight'=> true,
			'font-backup' => false,
			'text-align'  => false,
			'text-transform' => false,
			'font-style' => false,
			'color'      => false,
			'units'       =>'px',
			'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
			'all_styles'  => true
		),

	 array(
			'id'          => 'h6_typo',
			'type'        => 'typography',
			'title'       => esc_html__('H6 Font', 'wprentals-core'),
			'google'      => true,
			'font-family' => true,
			'subsets'     => true,
			'line-height'=> true,
			'font-weight'=> true,
			'font-backup' => false,
			'text-align'  => false,
			'text-transform' => false,
			'font-style' => false,
			'color'      => false,
			'units'       =>'px',
			'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
			'all_styles'  => true
		),

	 array(
			'id'          => 'paragraph_typo',
			'type'        => 'typography',
			'title'       => esc_html__('Paragraph Font', 'wprentals-core'),
			'google'      => true,
			'font-family' => true,
			'subsets'     => true,
			'line-height'=> true,
			'font-weight'=> true,
			'font-backup' => false,
			'text-align'  => false,
			'text-transform' => false,
			'font-style' => false,
			'color'      => false,
			'units'       =>'px',
			'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
			'all_styles'  => true
		),

	 array(
			'id'          => 'menu_typo',
			'type'        => 'typography',
			'title'       => esc_html__('Menu Font', 'wprentals-core'),
			'google'      => true,
			'font-family' => true,
			'subsets'     => true,
			'line-height' => true,
			'font-weight' => true,
			'font-backup' => false,
			'text-align'  => false,
			'text-transform' => false,
			'font-style' => false,
			'color'      => false,
			'units'       =>'px',
			'subtitle'    => esc_html__('Select your custom font options.', 'wprentals-core'),
			'all_styles'  => true
		),
	),
) );


	
	// -> START Advanced Selection
Redux::setSection( $opt_name, array(
	'title'      => __( 'Email Management', 'wprentals-core' ),
	'id'         => 'advanced_email_settings_sidebar',
	'icon'  => 'el el-envelope el el-small'
) );




Redux::setSection( $opt_name, array(
	'title'      => __( 'Email Content', 'wprentals-core' ),
	'id'         => 'email_management_tab',
	'desc'       => __( 'Leave "Subject" blank for the email notifications you don\'t wish to send. Global variables: %website_url as website url,%website_name as website name, %user_email as user_email, %username as username', 'wprentals-core' ),
	'subsection' => true,
	'fields'     => array(
	   array(
			'id'       => 'wp_estate_subject_new_user',
			'type'     => 'text',
			'title'    => __( 'Subject for New user notification', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for New user notification', 'wprentals-core' ),
			'default'  => __( 'Your username and password on %website_url', 'wprentals-core' )
		),
		array(
			'id'       => 'wp_estate_new_user',
			'type'     => 'editor',
			'title'    => __( 'Content for New user notification', 'wprentals-core' ),
			'subtitle' => __( 'Email content for New user notification', 'wprentals-core' ),
			'default'  => __('Hi there,
							Welcome to %website_url ! You can login now using the below credentials:
							Username:%user_login_register
							Password: %user_pass_register
							If you have any problems, please contact me.
							Thank you!', 'wprentals-core'),
			'desc'     => esc_html__('%user_login_register as new username, %user_pass_register as user password, %user_email_register as new user email.', 'wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_admin_new_user',
			'type'     => 'text',
			'title'    => __( 'Subject for New user admin notification', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for New user admin notification', 'wprentals-core' ),
			'default'  => __('New User Registration' , 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_admin_new_user',
			'type'     => 'editor',
			'title'    => __( 'Content for New user admin notification', 'wprentals-core' ),
			'subtitle' => __( 'Email content for New user admin notification', 'wprentals-core' ),
			'default'  => __('New user registration on %website_url.
							Username: %user_login_register,
							E-mail: %user_email_register', 'wprentals-core'),
			'desc'     =>esc_html__( '%user_login_register as new username and %user_email_register as new user email.', 'wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_purchase_activated',
			'type'     => 'text',
			'title'    => __( 'Subject for Purchase Activated', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Purchase Activated', 'wprentals-core' ),
			'default'  =>__('Your purchase was activated', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_purchase_activated',
			'type'     => 'editor',
			'title'    => __( 'Content for Purchase Activated', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Purchase Activated', 'wprentals-core' ),
			'default'  => __('Hi there,
						   Your purchase on  %website_url is activated! You should go check it out.', 'wprentals-core'),
			'desc'     => esc_html__('','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_password_reset_request',
			'type'     => 'text',
			'title'    => __( 'Subject for Password Reset Request', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Password Reset Request', 'wprentals-core' ),
			'default'  => __('Password Reset Request', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_password_reset_request',
			'type'     => 'editor',
			'title'    => __( 'Content for Password Reset Request', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Password Reset Request', 'wprentals-core' ),
			'default'  => __('Someone requested that the password be reset for the following account:
							%website_url
							Username: %forgot_username .
							If this was a mistake, just ignore this email and nothing will happen. To reset your password, visit the following address:%reset_link,
							Thank You!', 'wprentals-core'),
			'desc'     => esc_html__('Use %reset_link as reset link, %forgot_username as user name and %forgot_email as user email.','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_password_reseted',
			'type'     => 'text',
			'title'    => __( 'Subject for Password Reseted', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Password Reseted', 'wprentals-core' ),
			'default'  => __('Your Password was Reset', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_password_reseted',
			'type'     => 'editor',
			'title'    => __( 'Content for Password Reseted', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Password Reseted', 'wprentals-core' ),
			'default'  => __('Your new password for the account at: %website_url:
							Username:%user_login,
							Password:%user_pass
							You can now login with your new password at: %website_url', 'wprentals-core'),
			'desc'     => esc_html__('Use %reset_link as reset link, %forgot_username as username and %forgot_email as user email.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_approved_listing',
			'type'     => 'text',
			'title'    => __( 'Subject for Approved Listings', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Approved Listings', 'wprentals-core' ),
			'default'  => __('Your Password was Reset', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_approved_listing',
			'type'     => 'editor',
			'title'    => __( 'Content for Approved Listings', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Approved Listings', 'wprentals-core' ),
			'default'  => __('Hi there,
							Your listing, %property_title was approved on  %website_url ! The listing is: %property_url.
							You should go check it out.', 'wprentals-core'),
			'desc'     => esc_html__('You can use %listing_author as owner name, %post_id as listing id, %property_url as property url and %property_title as property name.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_admin_expired_listing',
			'type'     => 'text',
			'title'    => __( 'Subject for Admin - Expired Listing', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Admin - Expired Listing', 'wprentals-core' ),
			'default'  => __('Expired Listing sent for approval on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_admin_expired_listing',
			'type'     => 'editor',
			'title'    => __( 'Content for Admin - Expired Listing', 'wprentals-core' ),
			'subtitle' => __( 'Email Email content for Admin - Expired Listing', 'wprentals-core' ),
			'default'  => __('Hi there,
							A user has re-submited a new property on %website_url ! You should go check it out.
							This is the property title: %submission_title.', 'wprentals-core'),
			'desc'     => esc_html__('You can use %submission_title as property title number, %submission_url as property submission url.','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_paid_submissions',
			'type'     => 'text',
			'title'    => __( 'Subject for Paid Submission' ),
			'subtitle' => __( 'Email subject for Paid Submission', 'wprentals-core' ),
			'default'  => __('New Paid Submission on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_paid_submissions',
			'type'     => 'editor',
			'title'    => __( 'Content for Paid Submission', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Paid Submission', 'wprentals-core' ),
			'default'  => __('Hi there,
							You have a new paid submission on  %website_url ! You should go check it out.', 'wprentals-core'),
			'desc'     => esc_html__('', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_featured_submission',
			'type'     => 'text',
			'title'    => __( 'Subject for Featured Submission', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Featured Submission', 'wprentals-core' ),
			'default'  => __('New Feature Upgrade on  %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_featured_submission',
			'type'     => 'editor',
			'title'    => __( 'Content for Featured Submission', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Featured Submission', 'wprentals-core' ),
			'default'  => __('Hi there,
							You have a new featured submission on  %website_url ! You should go check it out.', 'wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_account_downgraded',
			'type'     => 'text',
			'title'    => __( 'Subject for Account Downgraded', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Account Downgraded', 'wprentals-core' ),
			'default'  => __('Account Downgraded on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_account_downgraded',
			'type'     => 'editor',
			'title'    => __( 'Content for Account Downgraded', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Account Downgraded', 'wprentals-core' ),
			'default'  => __('Hi there,
							You downgraded your subscription on %website_url. Because your listings number was greater than what the actual package offers, we set the status of all your listings to expired. You will need to choose which listings you want live and send them again for approval.
							Thank you!', 'wprentals-core'),
			'desc'     => esc_html__('','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_membership_cancelled',
			'type'     => 'text',
			'title'    => __( 'Subject for Membership Cancelled', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Membership Cancelled', 'wprentals-core' ),
			'default'  => __('Membership Cancelled on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_membership_cancelled',
			'type'     => 'editor',
			'title'    => __( 'Content for Membership Cancelled', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Membership Cancelled', 'wprentals-core' ),
			'default'  => __('Hi there,
							Your subscription on %website_url was cancelled because it expired or the recurring payment from the merchant was not processed. All your listings are no longer visible for our visitors but remain in your account.
							Thank you.', 'wprentals-core'),
			'desc'     => esc_html__('', 'wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_free_listing_expired',
			'type'     => 'text',
			'title'    => __( 'Subject for Free Listing Expired' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Free Listing Expired', 'wprentals-core' ),
			'default'  => __('Free Listing expired on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_free_listing_expired',
			'type'     => 'editor',
			'title'    => __( 'Content for Free Listing Expired', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Free Listing Expired', 'wprentals-core' ),
			'default'  => __('Hi there,
							One of your free listings on  %website_url has expired. The listing is %expired_listing_url.
							Thank you!', 'wprentals-core'),
			'desc'     => esc_html__('You can use %expired_listing_url as expired listing url and %expired_listing_name as expired listing name.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_new_listing_submission',
			'type'     => 'text',
			'title'    => __( 'Subject for New Listing Submission', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for New Listing Submission', 'wprentals-core' ),
			'default'  => __('New Listing Submission on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_new_listing_submission',
			'type'     => 'editor',
			'title'    => __( 'Content for New Listing Submission', 'wprentals-core' ),
			'subtitle' => __( 'Email content for New Listing Submission', 'wprentals-core' ),
			'default'  => __('Hi there,
						   A user has submited a new property on %website_url ! You should go check it out.This is the property title %new_listing_title!', 'wprentals-core'),
			'desc'     => esc_html__('You can use %new_listing_title as new listing title and %new_listing_url as new listing url.','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_recurring_payment',
			'type'     => 'text',
			'title'    => __( 'Subject for Recurring Payment', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Recurring Payment', 'wprentals-core' ),
			'default'  => __('Recurring Payment on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_recurring_payment',
			'type'     => 'editor',
			'title'    => __( 'Content for Recurring Payment', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Recurring Payment', 'wprentals-core' ),
			'default'  => __('Hi there,
						   We charged your account on %merchant for a subscription on %website_url ! You should go check it out.', 'wprentals-core'),
			'desc'     => esc_html__('You can use %recurring_pack_name as recurring packacge name and %merchant as merchant name.','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_membership_activated',
			'type'     => 'text',
			'title'    => __( 'Subject for Membership Activated' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Membership Activated', 'wprentals-core' ),
			'default'  => __('Membership Activated on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_membership_activated',
			'type'     => 'editor',
			'title'    => __( 'Content for Membership Activated', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Membership Activated', 'wprentals-core' ),
			'default'  => __('Hi there,
						   Your new membership on %website_url is activated! You should go check it out.', 'wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_agent_update_profile',
			'type'     => 'text',
			'title'    => __( 'Subject for Update Profile', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Update Profile', 'wprentals-core' ),
			'default'  => __('Profile Update', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_agent_update_profile',
			'type'     => 'editor',
			'title'    => __( 'Content for Update Profile', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Update Profile', 'wprentals-core' ),
			'default'  => __('A user updated his profile on %website_url.
							Username: %user_login', 'wprentals-core'),
			'desc'     => esc_html__('Use %user_login as username, %user_email_profile as user email, %user_id as user_id.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_bookingconfirmeduser',
			'type'     => 'text',
			'title'    => __( 'Subject for Booking Confirmed - User', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Booking Confirmed - User', 'wprentals-core' ),
			'default'  => __('Booking Confirmed on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_bookingconfirmeduser',
			'type'     => 'editor',
			'title'    => __( 'Content for Booking Confirmed - User', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Booking Confirmed - User', 'wprentals-core' ),
			'default'  => __('Hi there,
							Your booking made on %website_url was confirmed! You can see all your reservations by logging in your account and visiting My Reservations page.','wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_bookingconfirmed',
			'type'     => 'text',
			'title'    => __( 'Subject for Booking Confirmed', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Booking Confirmed', 'wprentals-core' ),
			'default'  =>'Booking Confirmed on %website_url'
		),
		array(
			'id'       => 'wp_estate_bookingconfirmed',
			'type'     => 'editor',
			'title'    => __( 'Content for Booking Confirmed', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Booking Confirmed', 'wprentals-core' ),
			'default'  => __('Hi there,
							Somebody confirmed a booking on %website_url! You should go and check it out!Please remember that the confirmation is made based on the payment confirmation of a non-refundable fee of the total invoice cost, processed through %website_url and sent to website administrator. ','wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_bookingconfirmed_nodeposit',
			'type'     => 'text',
			'title'    => __( 'Subject for Booking Confirmed - no deposit', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Booking Confirmed - no deposit', 'wprentals-core' ),
			'default'  => __('Booking Confirmed on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_bookingconfirmed_nodeposit',
			'type'     => 'editor',
			'title'    => __( 'Content for Booking Confirmed - no deposit', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Booking Confirmed - no deposit', 'wprentals-core' ),
			'default'  => __('Hi there,
							You confirmed a booking on %website_url! The booking was confirmed with no deposit!','wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_inbox',
			'type'     => 'text',
			'title'    => __( 'Subject for Inbox- New Message', 'wprentals-core' ),
			'subtitle' => __( 'Email subject for Inbox- New Message', 'wprentals-core' ),
			'default'  => __('New Message on %website_url.', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_inbox',
			'type'     => 'editor',
			'title'    => __( 'Content for Inbox- New Message', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Inbox- New Message', 'wprentals-core' ),
			'default'  => __('Hi there,
							You have a new message on %website_url! You should go and check it out!
							The message is:
							%content','wprentals-core'),
			'desc'     => esc_html__('You can use %content as message content.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_newbook',
			'type'     => 'text',
			'title'    => __( 'Subject for New Booking Request' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for New Booking Request', 'wprentals-core' ),
			'default'  => __('New Booking Request on %website_url.', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_newbook',
			'type'     => 'editor',
			'title'    => __( 'Content for New Booking Request', 'wprentals-core' ),
			'subtitle' => __( 'Email content for New Booking Request', 'wprentals-core' ),
			'default'  => __('Hi there,
							You have received a new booking request on %website_url !  Go to your account in Bookings page to see the request, issue the invoice or reject it!
							The property is: %booking_property_link','wprentals-core'),
			'desc'     => esc_html__('You can use %booking_property_link as property url,%booking_id as booking id.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_mynewbook',
			'type'     => 'text',
			'title'    => __( 'Subject for Owner - New Booking Request', 'wprentals-core'),
			'subtitle' => __( 'Email subject for Owner - New Booking Request', 'wprentals-core' ),
			'default'  => __('You booked a period on %website_url.', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_mynewbook',
			'type'     => 'editor',
			'title'    => __( 'Content for Owner - New Booking Request', 'wprentals-core' ),
			'subtitle' => __( 'Email content for User - New Booking Request', 'wprentals-core' ),
			'default'  => __('Hi there,
							You have booked a period for your own listing on %website_url !  The reservation will appear in your account, under My Bookings.
							The property is: %booking_property_link','wprentals-core'),
			'desc'     => esc_html__('You can use %booking_property_link as property url.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_newinvoice',
			'type'     => 'text',
			'title'    => __( 'Subject for Invoice generation' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Invoice generation', 'wprentals-core' ),
			'default'  => __('New Invoice on %website_url.', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_newinvoice',
			'type'     => 'editor',
			'title'    => __( 'Content for Invoice generation', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Invoice generation', 'wprentals-core' ),
			'default'  => __('Hi there,
							An invoice was generated for your booking request on %website_url !  A deposit will be required for booking to be confirmed. For more details check out your account, My Reservations page.','wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_deletebooking',
			'type'     => 'text',
			'title'    => __( 'Subject for Booking request rejected','wprentals-core' ),
			'subtitle' => __( 'Email subject for Booking request rejected', 'wprentals-core' ),
			'default'  => 'Booking Request Rejected on %website_url'
		),
		array(
			'id'       => 'wp_estate_deletebooking',
			'type'     => 'editor',
			'title'    => __( 'Content for Booking request rejected', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Booking request rejected', 'wprentals-core' ),
			'default'  => __('Hi there,
							One of your booking requests sent on %website_url was rejected by the owner. The rejected reservation is automatically removed from your account. ','wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_subject_deletebookinguser',
			'type'     => 'text',
			'title'    => __( 'Subject for Booking Request Cancelled' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Booking Request Cancelled', 'wprentals-core' ),
			'default'  => __('Booking Request Cancelled on %website_url', 'wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_deletebookinguser',
			'type'     => 'editor',
			'title'    => __( 'Content for Booking Request Cancelled', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Booking Request Cancelled', 'wprentals-core' ),
			'default'  => __('Hi there,
							One of the unconfirmed booking requests you received on %website_url  was cancelled! The request is automatically deleted from your account!','wprentals-core'),
			'desc'     => esc_html__('You can use %receiver_email as email of the person who cancel ,%receiver_name as the username of person who cancel.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_deletebookingconfirmed',
			'type'     => 'text',
			'title'    => __( 'Subject for Booking Period Cancelled' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Booking Period Cancelled', 'wprentals-core' ),
			'default'  => __('Booking Request Cancelled on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_deletebookingconfirmed',
			'type'     => 'editor',
			'title'    => __( 'Content for Booking Period Cancelled', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Booking Period Cancelled', 'wprentals-core' ),
			'default'  => __('Hi there,
							One of your confirmed bookings on %website_url  was cancelled by property owner. ','wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		 array(
			'id'       => 'wp_estate_subject_new_wire_transfer',
			'type'     => 'text',
			'title'    => __( 'Subject for New wire Transfer', 'wprentals-core'),
			'subtitle' => __( 'Email subject for New wire Transfer', 'wprentals-core' ),
			'default'  =>  __('You ordered a new Wire Transfer', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_new_wire_transfer',
			'type'     => 'editor',
			'title'    => __( 'Content for New wire Transfer', 'wprentals-core' ),
			'subtitle' => __( 'Email content for New wire Transfer', 'wprentals-core' ),
			'default'  => __('We received your Wire Transfer payment request on  %website_url !
							Please follow the instructions below in order to start submitting properties as soon as possible.
							The invoice number is: %invoice_no, Amount: %total_price.
							Instructions:  %payment_details.','wprentals-core'),
			'desc'     => esc_html__('You can use %invoice_no as invoice number, %total_price as $totalprice and %payment_details as $payment_details.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_admin_new_wire_transfer',
			'type'     => 'text',
			'title'    => __( 'Subject for Admin - New wire Transfer' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Admin - New wire Transfer', 'wprentals-core' ),
			'default'  => __('Somebody ordered a new Wire Transfer', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_admin_new_wire_transfer',
			'type'     => 'editor',
			'title'    => __( 'Content for Admin - New wire Transfer', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Admin - New wire Transfer', 'wprentals-core' ),
			'default'  => __('Hi there,
							You received a new Wire Transfer payment request on %website_url.
							The invoice number is:  %invoice_no,  Amount: %total_price.
							Please wait until the payment is made to activate the user purchase.','wprentals-core'),
			'desc'     => esc_html__('You can use %invoice_no as invoice number, %total_price as $totalprice and %payment_details as $payment_details.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_full_invoice_reminder',
			'type'     => 'text',
			'title'    => __( 'Subject for Invoice Payment Reminder' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Invoice Payment Reminder', 'wprentals-core' ),
			'default'  => __('Invoice payment reminder', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_full_invoice_reminder',
			'type'     => 'editor',
			'title'    => __( 'Content for Invoice Payment Reminder', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Invoice Payment Reminder', 'wprentals-core' ),
			'default'  => __('Hi there,
							We remind you that you need to fully pay the invoice no %invoice_id until  %until_date. This invoice is for booking no %booking_id on property %property_title with the url %property_url.
							Thank you.','wprentals-core'),
			'desc'     => esc_html__('* you can use %invoice_id as invoice id, %property_url as property url and %property_title as property name, %booking_id as booking id, %until_date as the last day.','wprentals-core'),
		),
		array(
			'id'       => 'wp_estate_subject_new_user_id_verification',
			'type'     => 'text',
			'title'    => __( 'Subject for New User ID verification' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for New User ID verification', 'wprentals-core' ),
			'default'  => __('New User ID verification', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_new_user_id_verification',
			'type'     => 'editor',
			'title'    => __( 'Content for New User ID verification', 'wprentals-core' ),
			'subtitle' => __( 'Email content for New User ID verification', 'wprentals-core' ),
			'default'  => __('A user added his User ID verification image on %website_url.
							Username: %user_login.
							','wprentals-core'),
			'desc'     => esc_html__('you can use %user_login as username.','wprentals-core'),
		),
		
			array(
			'id'       => 'wp_estate_subject_payment_action_required',
			'type'     => 'text',
			'title'    => __( 'Subject for Payment Action Required' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for  Payment Action Required (on 3ds recurring payments via Stripe) ', 'wprentals-core' ),
			'default'  => __('Payment Action Required on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_payment_action_required',
			'type'     => 'editor',
			'title'    => __( 'Content for Payment Action Required', 'wprentals-core' ),
			'subtitle' => __( 'Email content for  Payment Action Required (on 3ds recurring payments via Stripe) ', 'wprentals-core' ),
			'default'  => __('Hi there,
							One of your subscription payments on %website_url  requires manual confirmation. Please go to your dashboard and approve the payment. ','wprentals-core'),
			'desc'     => esc_html__('','wprentals-core' ),
		),
		
		array(
			'id'       => 'wp_estate_subject_new_review',
			'type'     => 'text',
			'title'    => __( 'Subject for New Review email' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for New Review', 'wprentals-core' ),
			'default'  => __('New Review received on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_new_review',
			'type'     => 'editor',
			'title'    => __( 'Content for New Review Email', 'wprentals-core' ),
			'subtitle' => __( 'Email content for when a new review', 'wprentals-core' ),
			'default'  => __('Hi there,
						   You Received a new review for %property_name . User %user% posted a %stars stars review : 
						   %content.','wprentals-core'),
			'desc'     => esc_html__('You can use %stars for stars no, %user for reviewer login name, %property_name for property name, %content for review content.','wprentals-core' ),
		),
		 
		array(
			'id'       => 'wp_estate_subject_review_reply',
			'type'     => 'text',
			'title'    => __( 'Subject for Review Reply Email' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Review Reply Email', 'wprentals-core' ),
			'default'  => __('A reply was posted to your review on %website_url', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_review_reply',
			'type'     => 'editor',
			'title'    => __( 'Content for  Review Reply Email', 'wprentals-core' ),
			'subtitle' => __( 'Email content for when a review reply', 'wprentals-core' ),
			'default'  => __('Hi there,
						   You Received a reply to your review for %property_name.
						   %reply_content','wprentals-core'),
			'desc'     => esc_html__('You can use %property_name for property name, %reply_content for reply content.','wprentals-core' ),
		),
		
		
		
		array(
			'id'       => 'wp_estate_subject_post_review_reminder',
			'type'     => 'text',
			'title'    => __( 'Subject for Post Peview Reminder' , 'wprentals-core'),
			'subtitle' => __( 'Email subject for Post Peview Reminder', 'wprentals-core' ),
			'default'  => __('Rate your stay at %property_title', 'wprentals-core')
		),
		array(
			'id'       => 'wp_estate_post_review_reminder',
			'type'     => 'editor',
			'title'    => __( 'Content for Post Peview Reminder', 'wprentals-core' ),
			'subtitle' => __( 'Email content for Post Peview Reminder', 'wprentals-core' ),
			'default'  => __('Hi there,
							You just checked out of %property_title. Take a few minutes to rate your stay and let your host know how they did.
							 Thank you.','wprentals-core'),
			'desc'     => esc_html__('* you can use %invoice_id as invoice id, %property_url as property url and %property_title as property name, %booking_from_date as booking start date, %booking_to_date as booking end date','wprentals-core'),
		),
  
	),
) );




Redux::setSection( $opt_name, array(
	'title'      => __( 'Emails Settings', 'wprentals-core' ),
	'id'         => 'advanced_email_settings_section',
	'subsection' => true,
	'fields'     => array(
			
		array(
			'id'       => 'wpestate_email_type',
			'type'     =>  'button_set',
			'title'    => __( 'Send emails as Html or text', 'wprentals-core' ),
			'subtitle' => __( 'Send emails as Html or text','wprentals-core'),
			'options'  => array(
							'html' => 'html',
							'text'  => 'text'
						),
			'default'  => 'html'
		),
		
		
		array(
			'id'       => 'wp_estate_send_name_email_from',
			 'type'     => 'text',
			'title'    => __( 'Emails will be sent from name?', 'wprentals-core' ),
			'subtitle' => __( 'Emails will use the from name set here','wprentals-core'),
			
			'default'  => 'noreply',
		),
		array(
			'id'       => 'wp_estate_send_email_from',
			 'type'     => 'text',
			'title'    => __( 'Emails will be sent from email', 'wprentals-core' ),
			'subtitle' => __( 'Emails will use as sender email this address. If left blank, emails are sent from an address like noreply@yourdomain.com','wprentals-core'),
			
			'default'  => $siteurl,
		),
		
		
		
		array(
			'id'       => 'wpestate_display_header_email',
			'type'     =>  'button_set',
			'title'    => __( 'Display Email Header ?', 'wprentals-core' ),
			'subtitle' => __( 'Display email header - the default header contains only the logo ','wprentals-core'),
			'options'  => array(
							'yes' => 'yes',
							'no'  => 'no'
						),
			'default'  => 'yes'
		),
		
		array(
			'id'       => 'wp_estate_email_logo_image',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Email Logo', 'wprentals-core' ),
			'subtitle' => __( 'Use the "Upload" button and "Insert into Post" button from the pop up window. Add a small logo. ', 'wprentals-core' ),
			 'default'  => array(
				'url' =>get_template_directory_uri().'/img/logo.png'
			)
			
		  
		),
		  array(
			'id'       => 'wpestate_display_footer_email',
			'type'     =>  'button_set',
			'title'    => __( 'Display Email footer?', 'wprentals-core' ),
			'subtitle' => __( 'Display email footer','wprentals-core'),
			'options'  => array(
							'yes' => 'yes',
							'no'  => 'no'
						),
			'default'  => 'yes'
		),
		
	   array(
			'id'       => 'wpestate_show_footer_email_address',
			'type'     =>  'button_set',
			'title'    => __( 'Show Address in  email footer?', 'wprentals-core' ),
			'subtitle' => __( 'Show Address in  email footer?','wprentals-core'),
			'options'  => array(
							'yes' => 'yes',
							'no'  => 'no'
						),
			'default'  => 'yes'
		),
		
		array(
			'id'       => 'wp_estate_email_footer_content',
			'type'     => 'editor',
			'title'    => __( 'Footer Content', 'wprentals-core' ),
			'subtitle' => __( 'Footer Content for email', 'wprentals-core' ),
			'default'  => __('Please do not reply directly to this email. If you believe this is an error or require further assistance, please contact us', 'wprentals-core'),
		   
		),
		
		array(
			'id'       => 'wp_estate_email_footer_social1',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Social icon no 1', 'wprentals-core' ),
			'subtitle' => __( 'Upload social icon image', 'wprentals-core' ),
			'default'  => array(
					'url' =>get_template_directory_uri().'/templates/email_templates/images/facebook_email.png'
				)
			

		),
		array(
			'id'       => 'wp_estate_email_footer_social_link1',
			'type'     => 'text',
			'title'    => __( 'Link social accont no 1 ?', 'wprentals-core' ),
			'subtitle' => __( 'Link for social accont no 1 ','wprentals-core'),
			 'default'   =>'#'
	
		),
		
		
		array(
			'id'       => 'wp_estate_email_footer_social2',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Social icon no 2', 'wprentals-core' ),
			'subtitle' => __( 'Upload social icon image', 'wprentals-core' ),
			'default'  => array(
					'url' =>get_template_directory_uri().'/templates/email_templates/images/twitter-email.png'
				)
		),
		
		array(
			'id'       => 'wp_estate_email_footer_social_link2',
			'type'     => 'text',
			'title'    => __( 'Link social accont no 2 ?', 'wprentals-core' ),
			'subtitle' => __( 'Link for social accont no 2 ','wprentals-core'),
			 'default'   =>'#'
		   
		),
		
		
		array(
			'id'       => 'wp_estate_email_footer_social3',
			'type'     => 'media',
			'url'      => true,
			'title'    => __( 'Social icon no 3', 'wprentals-core' ),
			'subtitle' => __( 'Upload social icon image', 'wprentals-core' ),
			'default'  => array(
				'url' =>get_template_directory_uri().'/templates/email_templates/images/linkedin-email.png'
			)
		),
		array(
			'id'       => 'wp_estate_email_footer_social_link3',
			 'type'     => 'text',
			'title'    => __( 'Link social accont no 3 ?', 'wprentals-core' ),
			'subtitle' => __( 'Link for social accont no 3 ','wprentals-core'),
			'default'   =>'#'
		),
		
		array(
			'id'       => 'wp_estate_email_background',
			'type'     => 'color',
			'title'    => __( 'Email Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Email Background Color', 'wprentals-core' ),
			'transparent' => false,
		),
		 array(
			'id'       => 'wp_estate_email_content_background',
			'type'     => 'color',
			'title'    => __( 'Email Content Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Email Content Background Color', 'wprentals-core' ),
			'transparent' => false,
		),
		
		
		
		
	),
) );

	Redux::setSection( $opt_name, array(
	'title'      => __( 'Trip Details Email', 'wprentals-core' ),
	'id'         => 'advanced_trip_detail_email_section',
	'subsection' => true,
	'fields'     => array(
			
		array(
			'id'       => 'wpestate_your_trip_show_email',
			'type'     =>  'button_set',
			'title'    => __( 'Show owner email in trip details email?', 'wprentals-core' ),
			'subtitle' => __( 'Show owner email in trip details email?','wprentals-core'),
			'options'  => array(
							'yes' => 'yes',
							'no'  => 'no'
						),
			'default'  => 'yes'
		),
		array(
			'id'       => 'wpestate_send_your_trip_show_email',
			'type'     =>  'button_set',
			'title'    => __( 'Send trip details email?', 'wprentals-core' ),
			'subtitle' => __( 'Send trip details email?','wprentals-core'),
			'options'  => array(
							'yes' => 'yes',
							'no'  => 'no'
						),
			'default'  => 'yes'
		),
		
		
)));

Redux::setSection( $opt_name, array(
	'title' => __( 'Advanced', 'wprentals-core' ),
	'id'    => 'advanced_settings_sidebar',
	'icon'  => 'el el-cogs'
) );






 Redux::setSection( $opt_name, array(
	'title'      => __( 'Import & Export', 'wprentals-core' ),
	'id'         => 'import_export_ab',
	'subsection' => true,
	'fields'     => array(
		array(
			  'id'            => 'opt-import-export',
			  'type'          => 'import_export',
			  'title'         => 'Import & Export',
			//  'subtitle'      => '',
			  'full_width'    => false,
		  ),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'reCaptcha settings', 'wprentals-core' ),
	'id'         => 'recaptcha_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_use_captcha',
			'type'     => 'button_set',
			'title'    => __( 'Use reCaptcha on register ?', 'wprentals-core' ),
			'subtitle' => __( 'This helps preventing registration spam.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
						),
			'default'  => 'no',
		),
		array(
			'id'       => 'wp_estate_recaptha_sitekey',
			'type'     => 'text',
			'required' =>   array('wp_estate_use_captcha','=','yes'),
			'title'    => __( 'reCaptha site key' , 'wprentals-core' ),
			'subtitle' => __( 'Get this detail after you signup here: ', 'wprentals-core' ).'<a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">https://www.google.com/recaptcha/intro/index.html</a>',
		),
		array(
			'id'       => 'wp_estate_recaptha_secretkey',
			'type'     => 'text',
			'required' =>   array('wp_estate_use_captcha','=','yes'),
			'title'    => __( 'reCaptha secret key', 'wprentals-core' ),
			'subtitle' => __( 'Get this detail after you signup here: ', 'wprentals-core' ).'<a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">https://www.google.com/recaptcha/intro/index.html</a>',
		),
	),
) );

if(function_exists('wprentals_redux_yelp')){
	$wprentals_redux_yelp=wprentals_redux_yelp();
}else{
	$wprentals_redux_yelp=array();
}

Redux::setSection( $opt_name, array(
	'title'      => __( 'Yelp settings', 'wprentals-core' ),
	'id'         => 'yelp_tab',
	'desc'       => __( 'Please note that Yelp is not working for all countries. See here https://www.yelp.com/factsheet the list of countries where Yelp is available.', 'wprentals-core' ),
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_yelp_client_id',
			'type'     => 'text',
			'title'    => __( 'Yelp Api Client ID' , 'wprentals-core'),
			'subtitle' => __( 'Get this detail after you signup here: ', 'wprentals-core' ).'<a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">https://www.yelp.com/developers/v3/manage_app</a>',
		),
		array(
			'id'       => 'wp_estate_yelp_client_secret',
			'type'     => 'text',
			'title'    => __( 'Yelp Api Key' , 'wprentals-core'),
			'subtitle' => __( 'Get this detail after you signup here: ', 'wprentals-core' ).'<a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">https://www.yelp.com/developers/v3/manage_app</a>',
		),
		array(
			'id'       => 'wp_estate_yelp_categories',
			'type'     => 'select',
			'multi'    =>   true,
			'title'    => __( 'Yelp Categories', 'wprentals-core' ),
			'subtitle' => __( 'Yelp Categories to show on front page', 'wprentals-core' ),
			'options'  => $wprentals_redux_yelp,
		),
		array(
			'id'       => 'wp_estate_yelp_results_no',
			'type'     => 'text',
			'title'    => __( 'Yelp - no of results', 'wprentals-core' ),
			'subtitle' => __( 'Yelp - no of results', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_yelp_dist_measure',
			'type'     => 'button_set',
			'title'    => __( 'Yelp Distance Measurement Unit', 'wprentals-core' ),
			'subtitle' => __( 'Yelp Distance Measurement Unit', 'wprentals-core' ),
			'options'  => array('miles'=>'miles','kilometers'=>'kilometers'),
			'default'  => 'miles',
		),
	),
) );


Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_image_size/' );
$default_image_size = wpestate_return_default_image_size();
$redux_image_size=array();
foreach ($default_image_size as $key=>$image_size){
$redux_image_size[]=array(
		'id' => 'wp_estate_'.$key,
		'full_width' => true,
		'type' => 'wpestate_image_size',
		'title' => $image_size['name'],
		'subtitle'=> esc_html('Orignal size:').' '.$image_size['width'].'px x '.$image_size['height'].' px. '.esc_html('The new values apply only for the images you upload after saving your change. For older images you need to use "Regenerate Thumbnails" plugin in order to recreate thumbs.','wprentals-core')
	   
);
}


Redux::setSection($opt_name, array(
'title' => __('Image settings', 'wprentals-core'),
'desc' => '<div class="wpsestate_admin_notice">' . __('VERY IMPORTANT!'
		. '</strong></br>- Make a backup of your site before modifying thumbs. You can revert the backup if you do not like how your changes influence your site design.'
		. '</br>- Your changes will influence design (thumbs height / width could look different than in default design) and speed (the bigger width and height values are, the slower the site could be)'
		. '</br>- After you change a image size you need to regenerate the thumbs for the already uploaded images (thre are free plugins for that) '
		. '</br>- Learn about WordPress thumbs and how they work before making changes -  https://developer.wordpress.org/reference/functions/add_image_size/ ', 'wprentals-core').'</div>',
'id' => 'Image_settings_tab',
'subsection' => true,
'fields' =>  $redux_image_size
));





// -> START Payments & Submit Selection
Redux::setSection( $opt_name, array(
	'title' => __( 'Payments & Submit', 'wprentals-core' ),
	'id'    => 'membership_settings',
	'icon'  => 'el el-group'
) );

if(function_exists('wpestate_return_all_fields')){
	$wpestate_return_all_fields=wpestate_return_all_fields();
}else{
	$wpestate_return_all_fields=array();
}



Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_select/' );
Redux::setSection( $opt_name, array(
	'title'      => __( 'Listing Submit Page', 'wprentals-core' ),
	'id'         => 'submit_page_settings_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_item_rental_type',
			'type'     => 'button_set',
			'title'    => __( 'What do you Rent?', 'wprentals-core' ),
			'subtitle' => __( 'Object Rentals doesn\'t show the guest field on property booking form and changes the label "night" into "day".', 'wprentals-core' ),
			'options'  => array(
						'0' => __('Vacation Rental', 'wprentals-core'),
						'1' => __('Object Rental', 'wprentals-core')
					),
			'default'  => '0',
		),
		array(
			'id'       => 'wp_estate_show_guest_number',
			'type'     => 'button_set',

			'title'    => __( 'Show the Guest dropdown?', 'wprentals-core' ),
			'subtitle' => __( 'Show the Guest dropdown in submit listing page? '
						 . 'Only for Object Rental set this to No for guest dropdown to not show in submit form.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
					),
			'default'  => 'yes',
		),
		array(
			'id'       => 'wp_estate_show_city_drop_submit',
			'type'     => 'button_set',
			'title'    => __( 'Show cities and areas as dropdowns?', 'wprentals-core' ),
			'subtitle' => __( 'Show cities and areas as dropdowns - populated with existing items in database. Cities and Areas are independent dropdowns.'
						 . 'This option doesn\'t apply when "Use Google Places autocomplete for Search?" from Advanced Search Settings is enabled.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no',

					),
			'default'  => 'no',
		),


		array(
			'id'       => 'wp_estate_submission_page_fields',
			'type'     => 'wpestate_select',
			'multi'     =>  true,

			'title'    =>   __( 'Select the Fields for listing submission.', 'wprentals-core' ),
			'subtitle' =>   __( 'Use CTRL to select multiple fields for listing submission page.', 'wprentals-core' ),
			'options'   =>   $wpestate_return_all_fields,
			'default'   =>   $wpestate_return_all_fields,
		),
		array(
			'id'       => 'wp_estate_mandatory_page_fields',
			'type'     => 'wpestate_select',
			'multi'     =>  true,
			'args'      => 'xxxx',
			'title'    =>   __( 'Select the Mandatory Fields for listing submission.', 'wprentals-core' ),
			'subtitle' =>   __( 'Make sure the mandatory fields for listing submission page are part of submit form (managed from the above setting). Use CTRL for multiple fields select.', 'wprentals-core' ),
			'options'   =>  array(),
		),


		array(
			'id'       => 'wp_estate_category_main',
			'type'     => 'text',
			'title'    => __( 'Main Category Label', 'wprentals-core' ),
			'subtitle' => __( 'Main Category Label', 'wprentals-core' ),
		),


		array(
			'id'       => 'wp_estate_category_main',
			'type'     => 'text',
			'title'    => __( 'Main Category Label', 'wprentals-core' ),
			'subtitle' => __( 'Main Category Label', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_category_main_dropdown',
			'type'     => 'text',
			'title'    => __( 'Main Category Label for dropdowns', 'wprentals-core' ),
			'subtitle' => __( 'Main Category Label for dropdowns', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_category_second',
			'type'     => 'text',
			'title'    => __( 'Secondary Category Label', 'wprentals-core' ),
			'subtitle' => __( 'Secondary Category Label', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_category_second_dropdown',
			'type'     => 'text',
			'title'    => __( 'Secondary Category Label for dropdowns', 'wprentals-core' ),
			'subtitle' => __( 'Secondary Category Label for dropdowns', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_item_description_label',
			'type'     => 'text',
			'title'    => __( 'Item Description Label', 'wprentals-core' ),
			'subtitle' => __( 'Item Description Label', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_prop_image_number',
			'type'     => 'text',
			'title'    => __( 'Maximum no of images per listing (only front-end upload)', 'wprentals-core' ),
			'subtitle' => __( 'Maximum no of images per listing (only front-end upload)', 'wprentals-core' ),
			'default'  => '12'
		),
		array(
			  'id'       => 'wp_estate_submit_redirect',
			  'type'     => 'text',
			  'title'    => __( 'Url where the user will be redirected after property submit.', 'wprentals-core' ),
			  'subtitle' => __( 'Leave blank if you want to remain on the same page.', 'wprentals-core' ),
			  'default'  => ''
		  ),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Submission Payment Settings', 'wprentals-core' ),
	'id'         => 'membership_settings_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_paid_submission',
			'type'     => 'button_set',
			'title'    => __( 'Enable Paid Submission?', 'wprentals-core' ),
			'subtitle' => __( 'No = submission is free. Paid listing = submission requires user to pay a fee for each listing. Membership = submission is based on user membership package.', 'wprentals-core' ),
			'options'  => array(
						'no'         => 'no',
						'per listing'=> 'per listing',
						'membership' => 'membership'
					),
			'default'  => 'no',
		),




		 array(
			'id'       => 'wp_estate_free_mem_list',
			'type'     => 'text',
			'required'  => array('wp_estate_paid_submission','=','membership'),
			'title'    => __( ' Free Membership - no of free listings for new users', 'wprentals-core' ),
			'subtitle' => __( 'If you change this value, the new value applies for new registered users. Old value applies for older registered accounts.', 'wprentals-core' ),
			'default'  => '0'
		 ),


		array(
			'id'       => 'wp_estate_free_mem_list_unl',
			'required'  => array('wp_estate_paid_submission','=','membership'),
			'type'     => 'checkbox',
			'title'    => __( 'Free Membership - Offer unlimited listings for new users', 'wprentals-core' ),
			'default'  => '1'// 1 = on | 0 = off
		),
		array(
			'id'       => 'wp_estate_free_feat_list',
			'required'  => array('wp_estate_paid_submission','=','membership'),
			'type'     => 'text',
			'title'    => __( 'Free Membership - no of featured listings (for "membership" mode)', 'wprentals-core' ),
			'subtitle' => __( 'If you change this value, the new value applies for new registered users. Old value applies for older registered accounts.', 'wprentals-core' ),
			'default'  => '0'
		),
		array(
			'id'       => 'wp_estate_free_feat_list_expiration',
			'required'  => array('wp_estate_paid_submission','=','membership'),
			'type'     => 'text',
			'title'    => __( 'Free Days for Each Free Listing - no of days until a free listing will expire. *Starts from the moment the listing is published on the website. (for "membership" mode only)', 'wprentals-core' ),
			'subtitle' => __( 'Option applies for each free published listing.', 'wprentals-core' ),
			'default'  => '0'
		),




		array(
			'id'       => 'wp_estate_price_submission',
			'type'     => 'text',
			'required'  => array('wp_estate_paid_submission','=','per listing'),
			'title'    => __( 'Price Per Submission (for "per listing" mode)', 'wprentals-core' ),
			'subtitle' => __( 'Use .00 format for decimals (ex: 5.50). Do not set price as 0!', 'wprentals-core' ),
			'default'  => '0'
		),
		array(
			'id'       => 'wp_estate_price_featured_submission',
			'type'     => 'text',
			'required'  => array('wp_estate_paid_submission','=','per listing'),
			'title'    => __( 'Price to make the listing featured (for "per listing" mode)', 'wprentals-core' ),
			'subtitle' => __( 'Use .00 format for decimals (ex: 1.50). Do not set price as 0!', 'wprentals-core' ),
			'default'  => '0'
		),


		array(
			'id'       => 'wp_estate_paypal_api',
			'type'     => 'button_set',
			'title'    => __( 'Paypal & Stripe Api - SSL is mandatory for live payments', 'wprentals-core' ),
			'subtitle' => __( 'Sandbox = test API. LIVE = real payments API. Update PayPal and Stripe settings according to API type selection.', 'wprentals-core' ),
			'options'  => array(
					'sandbox' => 'sandbox',
					 'live'   =>  'live'
					),
			'default'  => 'sandbox',
		),
		array(
			'id'       => 'wp_estate_admin_submission',
			'type'     => 'button_set',
			'title'    => __( 'Submited Listings should be approved by admin?', 'wprentals-core' ),
			'subtitle' => __( 'If yes, admin publishes each listing submitted in front end manually.', 'wprentals-core' ),
			'options'  => array(
						'yes'  => 'yes',
						'no'   => 'no'
					),
			'default'  => 'yes',
		),

		array(
			'id'       => 'wp_estate_submission_curency',
			'type'     => 'button_set',
			'title'    => __( 'Currency For Paid Submission', 'wprentals-core' ),
			'subtitle' => __( 'The currency in which payments are processed.', 'wprentals-core' ),
			'options'  => array('USD' => 'USD',
								'EUR' => 'EUR',
								'AED' => 'AED',
								'AUD' => 'AUD',
								'BRL' => 'BRL',
								'CAD' => 'CAD',
								'COP' => 'COP',
								'CZK' => 'CZK',
								'DKK' => 'DKK',
								'HKD' => 'HKD',
								'HUF' => 'HUF',
								'ILS' => 'ILS',
								'JPY' => 'JPY',
								'JOD' => 'JOD',
								'MAD' => 'MAD', 
								'MXN' => 'MXN',
								'MYR' => 'MYR',
								'NOK' => 'NOK',
								'NZD' => 'NZD',
								'PHP' => 'PHP',
								'PLN' => 'PLN',
								'RON' => 'RON',
								'GBP' => 'GBP',
								'SGD' => 'SGD',
								'SEK' => 'SEK',
								'CHF' => 'CHF',
								'TWD' => 'TWD',
								'THB' => 'THB',
								'TRY' => 'TRY',
								'RUB' => 'RUB',
								'INR' => 'INR',
								'ZAR' => 'ZAR',
				),
			'default'  => 'USD',
		),
		array(
			'id'       => 'wp_estate_enable_direct_pay',
			'type'     => 'button_set',
			'title'    => __( 'Enable Direct Payment / Wire Payment?', 'wprentals-core' ),
			'subtitle' => __( 'Enable or disable the wire payment option.', 'wprentals-core' ),
			'options'  => array(
						'yes'  => 'yes',
						'no'   => 'no'
					),
			'default'  => 'no',
		),
		array(
		'id' => 'wp_estate_direct_payment_details',
		'type' => 'textarea',
		'required'=>array('wp_estate_enable_direct_pay','=','yes'),
		'title' => __('Wire instructions for direct payment', 'wprentals-core'),
		'subtitle' => __('If wire payment is enabled, type the instructions below(Allowed htmls tags : a,br,em and strong).', 'wprentals-core'),
		'validate' => 'html_custom',
		'allowed_html' => array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br'=>array(),
			'em' => array(),
			'strong' => array()
		)
	),
	array(
		'id'       => 'wp_estate_submission_curency_custom',
		'type'     => 'text',
		'title'    => __( 'Custom Currency Symbol', 'wprentals-core' ),
		'subtitle' => __( 'Add and save your own currency for Wire payments.', 'wprentals-core' ),
	),


),
) );

  Redux::setSection( $opt_name, array(
	'title'      => __( 'WooCommerce Settings', 'wprentals-core' ),
	'id'         => 'woo_settings_tab',
	'subsection' => true,
	'fields'     => array(
	array(
		   'id'       => 'wp_estate_enable_woo_mes',
		   'type'     => 'info',
			'desc'   =>  __( 'You need WooCommerce Plugin Installed and Active & and a WooCommerce Merchant Enabled. <a href="https://help.wprentals.org/article/install-woocommerce-and-activate-woocommerce-payments/" target="_blank">See help page.</a> </br>Payments are considerd complete once the Order for a particular items has the status "Processing or Complete " . </br> WooCommerce does not suport recurring payments, and so submission membership packages cannot be bought via WooCommerce.', 'wprentals-core' ),


	   ),
		 array(
			'id'       => 'wp_estate_enable_woo',
			'type'     => 'button_set',
			'title'    => __( 'Enable WooCommerce payments?', 'wprentals-core' ),
			'subtitle' => __( '', 'wprentals-core' ),
			'options'  => array(
						'yes'       => 'yes',
						'no'        => 'no',

					),
			'default'  => 'no',
		),
		)

	));

Redux::setSection( $opt_name, array(
	'title'      => __( 'Booking Payment Options', 'wprentals-core' ),
	'id'         => 'booking_payment_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_include_expenses',
			'type'     => 'button_set',
			'title'    => __( 'Include expenses when calculating deposit?', 'wprentals-core' ),
			'subtitle' => __( 'Include expenses when calculating deposit. The expenses are city fee and cleaning fee.', 'wprentals-core' ),
			'default'   =>'no',
			'options'  => array(
					'yes' => 'yes',
					'no'  => 'no',

				),
		),
		array(
			'id'       => 'wp_estate_book_down',
			'type'     => 'text',
			'title'    => __( 'Deposit Fee - % booking fee', 'wprentals-core' ),
			'subtitle' => __( 'Expenses are included or not in the deposit amount according to the above option. If the value is set to 100 (100%) the "Include expenses when calculating deposit?" option will be auto set to "yes"!', 'wprentals-core' ),
			'default'  => '0'
		),
		array(
			'id'       => 'wp_estate_book_down_fixed_fee',
			'type'     => 'text',
			'title'    => __( 'Deposit Fee - fixed value booking fee', 'wprentals-core' ),
			'subtitle' => __( 'Add the fixed fee as a number. If you use this option, leave blank Deposit Fee - % booking fee', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_service_fee',
			'type'     => 'text',
			'title'    => __( 'Service Fee - % booking fee', 'wprentals-core' ),
			'subtitle' => __( 'Service Fee. Is the commision that goes to the admin and is deducted from the total booking value.', 'wprentals-core' ),
			'default'  => '0'
		),
		array(
			'id'       => 'wp_estate_service_fee_fixed_fee',
			'type'     => 'text',
			'title'    => __( 'Service Fee - fixed value service fee', 'wprentals-core' ),
			'subtitle' => __( 'Service Fee - fixed value service fee. If you use this option, leave blank Service Fee - % booking fee', 'wprentals-core' ),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'PayPal Settings', 'wprentals-core' ),
	'id'         => 'paypal_settings_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_enable_paypal',
			'type'     => 'button_set',
			'title'    => __( 'Enable Paypal', 'wprentals-core' ),
			'subtitle' => __( 'You can enable or disable PayPal buttons.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default' => 'no'
		),
		array(
			'id'       => 'wp_estate_paypal_client_id',
			'type'     => 'text',
			'required' => array('wp_estate_enable_paypal','=','yes'),
			'title'    => __( 'Paypal Client id', 'wprentals-core' ),
			'subtitle' => __( 'PayPal business account is required. Info is taken from https://developer.paypal.com/. See help:', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/paypal-set-up/" target="_blank">https://help.wprentals.org/article/paypal-set-up/</a>',
		),
		array(
			'id'       => 'wp_estate_paypal_client_secret',
			'type'     => 'text',
			 'required' => array('wp_estate_enable_paypal','=','yes'),
			'title'    => __( 'Paypal Client Secret Key', 'wprentals-core' ),
			'subtitle' => __( 'Info is taken from https://developer.paypal.com/ See help:', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/paypal-set-up/" target="_blank">https://help.wprentals.org/article/paypal-set-up/</a>',
		),
		array(
			'id'       => 'wp_estate_paypal_rec_email',
			'type'     => 'text',
			 'required' => array('wp_estate_enable_paypal','=','yes'),
			'title'    => __( 'Paypal receiving email', 'wprentals-core' ),
			'subtitle' => __( 'Info is taken from https://www.paypal.com/ or http://sandbox.paypal.com/ See help:', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/paypal-set-up/" target="_blank">https://help.wprentals.org/article/paypal-set-up/</a>',
		),

	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Stripe Settings', 'wprentals-core' ),
	'id'         => 'stripe_settings_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_enable_stripe',
			'type'     => 'button_set',
			'title'    => __( 'Enable Stripe', 'wprentals-core' ),
			'subtitle' => __( 'You can enable or disable Stripe buttons.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default' => 'no'
		),
		array(
			'id'       => 'wp_estate_stripe_secret_key',
			'required' => array('wp_estate_enable_stripe','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Stripe Secret Key', 'wprentals-core' ),
			'subtitle' => __( 'Info is taken from your account at https://dashboard.stripe.com/login See help: ', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/stripe-set-up/" target="_blank">https://help.wprentals.org/article/stripe-set-up/</a>',
		),
		array(
			'id'       => 'wp_estate_stripe_publishable_key',
			'required' => array('wp_estate_enable_stripe','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Stripe Publishable Key', 'wprentals-core' ),
			'subtitle' => __( 'Info is taken from your account at https://dashboard.stripe.com/login See help: ', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/stripe-set-up/" target="_blank">https://help.wprentals.org/article/stripe-set-up/</a>',
		),
		array(
			'id'       => 'wp_estate_stripe_webhook',
			'required' => array('wp_estate_enable_stripe','=','yes'),
			'type'     => 'text',
			'title'    => __( 'Stripe Webhook Secret Key', 'wprentals-core' ),
			'subtitle' => __( 'Info is taken from your account at https://dashboard.stripe.com/login See help: ', 'wprentals-core' ).'<a href="https://help.wprentals.org/article/stripe-set-up/" target="_blank">https://help.wprentals.org/article/stripe-set-up/</a>',
		),
	),
) );

// -> START Search Selection
Redux::setSection( $opt_name, array(
	'title' => __( 'Search', 'wprentals-core' ),
	'id'    => 'advanced_search_settings',
	'icon'  => 'el el-search'
) );


Redux::setSection($opt_name, array(
	'title' => __('Advanced Search Results Page', 'wpresidence-core'),
	'id' => 'advanced_search_results',
	'subsection' => true,
	'fields' => array(
		array(
			'id'       => 'wp_estate_property_list_type_adv',
			'type'     => 'button_set',
			'title'    => __( 'Page List Type for Advanced Search Results', 'wprentals-core' ),
			'subtitle' => __( 'Select standard or half map style for advanced search results page.', 'wprentals-core' ),
			'options'  => array(
				'1' =>  __( 'standard','wprentals-core'),
				'2' =>  __( 'half map','wprentals-core')
			),
			'default'  => '1'
		),
		array(
			'id' => 'wp_estate_property_list_type_adv_order',
			'type' => 'button_set',
			'title' => __('Properties default order in advanced search results page', 'wprentals-core'),
			'subtitle' => __('Select the default order for properties in advanced search results page', 'wprentals-core'),
			'options' => $listing_filter_array,
			'default' => "0",
			),

	),
));



Redux::setSection( $opt_name, array(
	'title'      => __( 'Advanced Search Settings', 'wprentals-core' ),
	'id'         => 'advanced_search_settings_tab',
	'subsection' => true,
	'fields'     => array(
	   array(
			'id'       => 'wp_estate_show_adv_search_general',
			'type'     => 'button_set',
			'title'    => __( 'Show Advanced Search?', 'wprentals-core' ),
			'subtitle' => __( 'Disables or enables the display of advanced search over header media (Google Maps, Revolution Slider, theme slider or image).', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default' => 'no'
		),

		
		array(
			'id'       => 'wp_estate_show_adv_search_slider',
			'required' => array('wp_estate_show_adv_search_general', '=', 'yes'),
			'type'     => 'button_set',
			'title'    => __( 'Show Advanced Search over sliders or images?', 'wprentals-core' ),
			'subtitle' => __( 'Disables or enables the display of advanced search over header type: revolution slider, image and theme slider.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default'  => 'yes'
		),
		array(
			'id'       => 'wp_estate_ondemandmap',
			'type'     => 'button_set',
			'title'    => __( 'Use on demand pins when moving the map, in Properties list half map and Advanced search results half map pages', 'wprentals-core' ),
			'subtitle' => __( 'See this help article before: ', 'wprentals-core' ).'<a href=" https://help.wprentals.org/article/google-maps-settings/" target="_blank"> https://help.wprentals.org/article/google-maps-settings/</a>',
			'default' => 'no',
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),

		),
		array(
			'id'       => 'wp_estate_wpestate_autocomplete',
			'type'     => 'button_set',
			'title'    => __( 'Use Google Places for Search?', 'wprentals-core' ),
			'subtitle' => __( 'If you select NO, the autocomplete will be done with data from properties already saved.', 'wprentals-core' ),
			'desc'     => __( 'Due to speed reasons the data for NON-Google autocomplete is generated 1 time per day. If you want to manually generate the data, go to map section - Generate Data&Pins', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default' => 'no'
		),
		array(
			'id'       => 'wp_estate_wpestate_autocomplete_use_list',
			'required' => array('wp_estate_wpestate_autocomplete', '=', 'no'),
			'type'     => 'button_set',
			'title'    => __( 'Use Dropdown List instead of autocomplete for Non Google Location fields?', 'wprentals-core' ),
			'subtitle' => __( 'Works only with the option "Use Google Places autocomplete for Search?" - NO. If you select YES, you will have a dropdown instead of autocomplete.', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default' => 'no'
		),
		array(
			'id'       => 'wp_estate_show_empty_city',
			'type'     => 'button_set',
			'title'    => __( 'Show Cities and Areas with 0 listings in dropdowns?', 'wprentals-core' ),
			'subtitle' => __( 'Enable or disable empty city or area categories in dropdowns', 'wprentals-core' ),
			'options'  => array(
						'yes' => 'yes',
						'no'  => 'no'
				),
			'default' => 'no'
		),
		

	  
		
	),
) );

Redux::setSection($opt_name, array(
'title' => __('Taxonomies Multi Selections', 'wprentals-core' ),
'id' => 'advanced_search_multi_select_items',
'subsection' => true,
'fields' => array(
	 array(
		'id' => 'wp_estate_categ_select_list_multiple',
		'type' => 'button_set',
		'title' => __('Multiselect for the Category taxonomy', 'wprentals-core' ),
		'subtitle' => __('If yes, users can select multiple items in the Category dropdown in Advanced Search.', 'wprentals-core' ),
		'options' => array(
			'yes' => 'yes',
			'no' => 'no'
		),
		'default' => 'no',
	),
	array(
		'id' => 'wp_estate_action_select_list_multiple',
		'type' => 'button_set',
		'title' => __('Multiselect for the Type taxonomy', 'wprentals-core' ),
		'subtitle' => __('If yes, users can select multiple items in the Type dropdown in Advanced Search.', 'wprentals-core' ),
		'options' => array(
			'yes' => 'yes',
			'no' => 'no'
		),
		'default' => 'no',
	),
	array(
		'id' => 'wp_estate_city_select_list_multiple',
		'type' => 'button_set',
		'title' => __('Multiselect for the City taxonomy', 'wprentals-core' ),
		'subtitle' => __('If yes, users cand select multiple items in the City dropdown in Advanced Search.', 'wprentals-core' ),
		'options' => array(
			'yes' => 'yes',
			'no' => 'no'
		),
		'default' => 'no',
	),
	array(
		'id' => 'wp_estate_area_select_list_multiple',
		'type' => 'button_set',
		'title' => __('Multiselect for the Area taxonomy', 'wprentals-core' ),
		'subtitle' => __('If yes, users can select multiple items in the Area dropdown in Advanced Search.', 'wprentals-core' ),
		'options' => array(
			'yes' => 'yes',
			'no' => 'no'
		),
		'default' => 'no',
	),
  
	array(
		'id' => 'wp_estate_select_list_multiple_show_search',
		'type' => 'button_set',
		'title' => __('Show Search in the multiselect component', 'wprentals-core' ),
		'subtitle' => __('Show or hide the search in the multiselect component', 'wprentals-core' ),
		'options' => array(
			'yes' => 'yes',
			'no' => 'no'
		),
		'default' => 'yes',
	),
	
),
));

if(function_exists('wprentals_redux_advanced_exteded')){
	$wprentals_redux_advanced_exteded=wprentals_redux_advanced_exteded();
}else{
	$wprentals_redux_advanced_exteded=array();
}
Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_set_search/' );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Advanced Search Form', 'wprentals-core' ),
	'id'         => 'advanced_search_form_tab',
	'subsection' => true,
	'fields'     => array(
		 array(
			'id'       => 'wp_estate_adv_search_type',
			'type'     => 'button_set',
			'title'    => __( 'Select search type.', 'wprentals-core' ),
			'subtitle' => __( 'Type 1 - vertical design - hardcoded search type
							   </br>Type 2 - horizontal design - hardcoded search type
							   </br>Type 3, 4, 5 - work with search custom fields only.', 'wprentals-core' ),
			'options'  =>array(
						'newtype'   => esc_html__( 'Type 1','wprentals-core'),
						'oldtype'   => esc_html__( 'Type 2','wprentals-core'),
						'type3'     => esc_html__( 'Type 3','wprentals-core'),
						'type4'     => esc_html__( 'Type 4','wprentals-core'),
						'type5'     => esc_html__( 'Type 5','wprentals-core')
						),
			'default' => 'newtype'
		),
		array(
			'id'       => 'wp_estate_show_slider_min_price',
			'type'     => 'text',
			'title'    => __( 'Minimum value for Price Slider', 'wprentals-core' ),
			'subtitle' => __( 'Type only numbers!', 'wprentals-core' ),
			'default'  => '0'
		),
		array(
			'id'       => 'wp_estate_show_slider_max_price',
			'type'     => 'text',
			'title'    => __( 'Maximum value for Price Slider', 'wprentals-core' ),
			'subtitle' => __( 'Type only numbers!', 'wprentals-core' ),
			'default'  => '2500'
		),
		array(
			'id'       => 'wp_estate_show_adv_search_extended',
			'type'     => 'button_set',
			'title'    => __( 'Show Amenities and Features fields?', 'wprentals-core' ),
			'subtitle' => __( 'Displayed Only on: header search type 3, 4 and 5 & half map filters.', 'wprentals-core' ),
			'options'  =>array(
						'yes'   => 'yes',
						'no'   => 'no',
						),
			'default' => 'yes'
		),
		array(
			'id'       => 'wp_estate_advanced_exteded',
			'type'     => 'wpestate_select',
			'required' => array('wp_estate_show_adv_search_extended','=','yes'),
			'multi'    => true,
			'title'    => __( 'Amenities and Features for Advanced Search', 'wprentals-core' ),
			'subtitle' => __( 'Select which features and amenities show in search.', 'wprentals-core' ),
			'options'  => $wprentals_redux_advanced_exteded,
		),


		array(
			'id'       => 'wp_estate_show_dropdowns',
			'type'     => 'button_set',
			'title'    => __( 'Show Dropdowns for Guests, beds, bathrooms or rooms?', 'wprentals-core' ),
			'subtitle' => __( 'Works with SEARCH Types 3, 4, 5 and Half Map Form. Guests, Rooms, Bedrooms or Bathrooms must be added to Search Custom Fields for the option to apply.', 'wprentals-core' ),
			'options'  =>array(
						'yes'   => 'yes',
						'no'   => 'no',
						),
			'default' => 'yes'
		),

		array(
			'id' => 'wp_estate_beds_component_values',
			'type' => 'text',

			'title' => __('Possible dropdown values for beds', 'wprentals-core' ),
			'subtitle' => __('Type only numbers with/without + !', 'wprentals-core' ),
			'default' => '1,2,3,4,5,6+'
		),

		array(
			'id' => 'wp_estate_baths_component_values',
			'type' => 'text',

			'title' => __('Possible dropdown values for baths', 'wprentals-core' ),
			'subtitle' => __('Type only numbers with/without + !', 'wprentals-core' ),
			'default' => '1,2,3,4,5,6+'
		),

		array(
			'id'       => 'wp_estate_adv_search_label_for_form',
			'type'     => 'text',
			'title'    => __( 'Advanced Search Label for type 3', 'wprentals-core' ),
			'subtitle' => __( 'Advanced Search Label for type 3', 'wprentals-core' ),
		),


		array(
			'id'       => 'wp_estate_adv_search_fields_no',
			'type'     => 'text',
			'title'    => __( 'No of Search fields', 'wprentals-core' ),
			'subtitle' => __( 'No of Search fields for type 3, 4 and 5.', 'wprentals-core' ),
			'default'  => '3'
		),
		array(
			'id'       => 'wp_estate_search_fields_no_per_row',
			'type'     => 'text',
			'title'    => __( 'No of Search fields per row', 'wprentals-core' ),
			'subtitle' => __( 'No of Search fields per row (Possible values: 2,3,4). Only for types 3, 4, 5', 'wprentals-core' ),
			'default'  => '3'
		),
		array(
		   'id'       => 'wpestate_set_search',
		   'type'     => 'wpestate_set_search',
		   'title'    => __( 'Type 3, Type 4 and Type 5 custom search fields setup', 'wprentals-core' ),
		   'subtitle' => __( '*Do not duplicate fields and make sure search fields do not contradict themselves.
						</br>*<strong>Greater, Smaller and Equal</strong> must be used only for numeric fields.
						</br>*<strong>Like</strong> MUST be used for all text fields (including dropdowns)
						</br>*<strong>Date Greater / Date Smaller</strong> can be used for all date format fields.
						</br>*Labels will not apply for taxonomy dropdowns fields. These sync with the names added in Listing Submit Settings</br>', 'wprentals-core' ),
		   'full_width' => true,
		),

	),
) );


 Redux::setSection( $opt_name, array(
	'title'      => __( 'Half Map Search Form', 'wprentals-core' ),
	'id'         => 'advanced_search_half_map_form_tab',
	'subsection' => true,
	'fields'     => array(

		 array(
			'id'       => 'wp_estate_adv_search_fields_no_half_map',
			'type'     => 'text',
			'title'    => __( 'No of Search fields', 'wprentals-core' ),
			'subtitle' => __( 'Total number of search fields.', 'wprentals-core' ),
			'default'  => '3'
		),
		array(
			'id'       => 'wp_estate_search_fields_no_per_row_half_map',
			'type'     => 'text',
			'title'    => __( 'No of Search fields per row', 'wprentals-core' ),
			'subtitle' => __( 'No of Search fields per row (Possible values: 2,3,4)', 'wprentals-core' ),
			'default'  => '3'
		),
		array(
		   'id'       => 'wpestate_set_search_half_map',
		   'type'     => 'wpestate_set_search',
		   'title'    => __( 'Half Map custom search fields setup', 'wprentals-core' ),
		   'subtitle' => __( '*Do not duplicate fields and make sure search fields do not contradict themselves.
						</br>*<strong>Greater, Smaller and Equal</strong> must be used only for numeric fields.
						</br>*<strong>Like</strong> MUST be used for all text fields (including dropdowns)
						</br>*<strong>Date Greater / Date Smaller</strong> can be used for all date format fields.
						</br>*Labels will not apply for taxonomy dropdowns fields. These sync with the names added in Listing Submit Settings</br>', 'wprentals-core' ),
		   'full_width' => true,
		),



	),
	));

Redux::setSection( $opt_name, array(
	'title'      => __( 'Geo Location Search', 'wprentals-core' ),
	'id'         => 'geo_location_search_tab',
	'subsection' => true,
	'fields'     => array(
	   array(
			'id'       => 'wp_estate_use_geo_location',
			'type'     => 'button_set',
			'title'    => __( 'Use Geo Location Search in Half Map?', 'wprentals-core'),
			'subtitle' => __( 'If YES, the Geo Location search show in half map properties list and half map advanced search results, above the search fields.', 'wprentals-core' ),
			'default'  => 'no',
			'options'  =>array(
						'yes'   => 'yes',
						'no'   => 'no'
						),
		),
		array(
			'id'       => 'wp_estate_geo_radius_measure',
			'type'     => 'button_set',
			'title'    => __( 'Show Geo Location Search in:', 'wprentals-core' ),
			'subtitle' => __( 'Select between miles and kilometers.', 'wprentals-core' ),
			'default'  => 'miles',
			'options'  =>array (
					   'miles' =>  esc_html__('miles','wprentals-core'),
					   'km'    =>  esc_html__('km','wprentals-core')
						),
		),
		array(
			'id'       => 'wp_estate_initial_radius',
			'type'     => 'text',
			'title'    => __( 'Initial area radius', 'wprentals-core' ),
			'subtitle' => __( 'Initial area radius. Use only numbers.', 'wprentals-core' ),
			'default' => '3'
		),
		array(
			'id'       => 'wp_estate_min_geo_radius',
			'type'     => 'text',
			'title'    => __( 'Minimum radius value', 'wprentals-core' ),
			'subtitle' => __( 'Minimum radius value. Use only numbers.', 'wprentals-core' ),
			'default' => '1'
		),
		array(
			'id'       => 'wp_estate_max_geo_radius',
			'type'     => 'text',
			'title'    => __( 'Maximum radius value', 'wprentals-core' ),
			'subtitle' => __( 'Maximum radius value. Use only numbers.', 'wprentals-core' ),
			'default' => '10'
		),
		array(
		'id' => 'wp_estate_use_geo_location_limit_country',
		'type' => 'button_set',
		'title' => __('Limit to a specific country?', 'wprentals-core'),
		'subtitle' => __('If YES, the geo location search will be limited to a specific country', 'wprentals-core'),
		'default' => 'no',
		'options' => array(
			'yes' => 'yes',
			'no' => 'no'
		),
	),
	array(
		'id' => 'wp_estate_use_geo_location_limit_country_selected',
		'type' => 'select',
		'required' => array('wp_estate_use_geo_location_limit_country', '=', 'yes'),
		'title' => __('Select the country', 'wprentals-core'),
		'subtitle' => __('If YES, the geo location search will be limited to a specific country', 'wprentals-core'),
		'options' => wpestate_country_list_code(),
		'default' => ''
	),
	),
) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Advanced Search Form Position', 'wprentals-core' ),
	'id'         => 'advanced_search_form_position_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_sticky_search',
			'type'     => 'button_set',
			'title'    => __( 'Use sticky search ?', 'wprentals-core' ),
			'subtitle' => __( 'This will replace the sticky header. Doesn\'t apply to search type 1', 'wprentals-core' ),
			'options'  =>array(
						'yes'   => 'yes',
						'no'   => 'no'
						),
			'default' => 'no'
		),

		array(
			'id'       => 'wp_estate_use_float_search_form',
			'type'     => 'button_set',
			'title'    => __( 'Use Float Search Form ?', 'wprentals-core' ),
			'subtitle' => __( 'The search form is "floating" over the media header and you set the distance between search and browser\'s margin bottom below.', 'wprentals-core' ),
			'options'  =>array(
						'yes'   => 'yes',
						'no'   => 'no'
						),
			'default'  => 'yes'
		),
		array(
			'id'       => 'wp_estate_float_form_top',
			'type'     => 'text',
			'required' =>  array('wp_estate_use_float_search_form','=','yes'),
			'title'    => __( 'Distance between search form and the browser margin bottom: Ex 200px or 20%.', 'wprentals-core' ),
			'subtitle' => __( 'Distance between search form and the browser margin bottom: Ex 200px or 20%.', 'wprentals-core' ),
			'default'  => '20%'
		),

		array(
			'id'       => 'wp_estate_float_form_top_tax',
			'type'     => 'text',
			'required' =>  array('wp_estate_use_float_search_form','=','yes'),
			'title'    => __( 'Distance between search form and the browser margin bottom in px Ex 200px or 20% - for taxonomy, category and archives pages.', 'wprentals-core' ),
			'subtitle' => __( 'Distance between search form and the browser margin bottom in px Ex 200px or 20% - for taxonomy, category and archives pages.', 'wprentals-core' ),
			'default'  => '15%'
		),

		array(
			'id'       => 'wp_estate_search_on_start',
			'required' =>  array('wp_estate_use_float_search_form','=','no'),
			'type'     => 'button_set',
			'title'    => __( 'Put Search form before the header media ?', 'wprentals-core' ),
			'subtitle' => __( 'Works with "Use FLoat Form" options set to no. Doesn\'t apply to search type 1', 'wprentals-core' ),
			'options'  =>array(
						'no'   => 'no',
						'yes'   => 'yes',
						),
			'default'  => 'no'
		),


	),
) );

 Redux::setSection( $opt_name, array(
	'title'      => __( 'Advanced Search Colors', 'wprentals-core' ),
	'id'         => 'advanced_search_colors_tab',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'wp_estate_adv_back_color',
			'type'     => 'color',
			'title'    => __( 'Advanced Search Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Advanced Search Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_adv_back_color_opacity',
			'type'     => 'text',
			'title'    => __( 'Advanced Search Background color Opacity', 'wprentals-core' ),
			'subtitle' => __( 'Values between 0 -invisible and 1 - fully visible. Applies only when search form position "Use Float Search Form?" - is YES.', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_adv_search_back_button',
			'type'     => 'color',
			'title'    => __( 'Advanced Search Button Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Advanced Search Button Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
		array(
			'id'       => 'wp_estate_adv_search_back_hover_button',
			'type'     => 'color',
			'title'    => __( 'Advanced Search Button Hover Background Color', 'wprentals-core' ),
			'subtitle' => __( 'Advanced Search Button Hover Background Color', 'wprentals-core' ),
			'transparent'  => false,
		),
	),
) );


$sms_data_array=array(
		array(
			'id'       => 'wp_estate_sms_verification',
			'type'     => 'button_set',
			'title'    => __( 'Enable SMS service', 'wprentals-core' ),
			'subtitle' => __( 'Enable SMS service', 'wprentals-core' ),
			'options'  =>array(
						'yes'   => 'yes',
						'no'   => 'no'
						),
			'default' => 'no'
		),
		array(
			'id'       => 'wp_estate_twilio_phone_no',
			'type'     => 'text',
			'title'    => __( 'Twilio phone number', 'wprentals-core' ),
			'subtitle' => __( 'Twilio phone number(ex +1256973878)', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_twilio_api_key',
			'type'     => 'text',
			'title'    => __( 'Twilio Account Sid', 'wprentals-core' ),
			'subtitle' => __( 'Twilio Account Sid', 'wprentals-core' ),
		),
		array(
			'id'       => 'wp_estate_twilio_auth_token',
			'type'     => 'text',
			'title'    => __( 'Twilio Auth Token', 'wprentals-core' ),
			'subtitle' => __( 'Twilio Auth Token', 'wprentals-core' ),
		),
		 array(
			'id'    => 'sms_info',
			'type'  => 'info',
			'style' => 'info',
			'title' => __( 'Leave "content" blank for the sms notifications you don\'t wish to send. Global variables: %website_url as website url,%website_name as website name, %user_email as user_email, %username as username', 'wprentals-core' ),
		),

	);







	$sms_array=array(
		'validation'                =>  __('Phone Number Validation','wprentals'),
		'admin_new_user'            =>  __('New user admin notification','wprentals'),
		'password_reset_request'    =>  __('Password Reset Request','wprentals'),
		'password_reseted'          =>  __('Password Reseted','wprentals'),
		'approved_listing'          =>  __('Approved Listings','wprentals'),
		'admin_expired_listing'     =>  __('Admin - Expired Listing','wprentals'),
		'paid_submissions'          =>  __('Paid Submission','wprentals'),
		'featured_submission'       =>  __('Featured Submission','wprentals'),
		'account_downgraded'        =>  __('Account Downgraded','wprentals'),
		'membership_cancelled'      =>  __('Membership Cancelled','wprentals'),
		'free_listing_expired'      =>  __('Free Listing Expired','wprentals'),
		'new_listing_submission'    =>  __('New Listing Submission','wprentals'),
		'recurring_payment'         =>  __('Recurring Payment','wprentals'),
		'membership_activated'      =>  __('Membership Activated','wprentals'),
		'agent_update_profile'      =>  __('Update Profile','wprentals'),
		'bookingconfirmeduser'      =>  __('Booking Confirmed - User','wprentals'),
		'bookingconfirmed'          =>  __('Booking Confirmed','wprentals'),
		'bookingconfirmed_nodeposit'=>  __('Booking Confirmed - no deposit','wprentals'),
		'inbox'                     =>  __('Inbox- New Message','wprentals'),
		'newbook'                   =>  __('New Booking Request','wprentals'),
		'mynewbook'                 =>  __('User - New Booking Request','wprentals'),
		'newinvoice'                =>  __('Invoice generation','wprentals'),
		'deletebooking'             =>  __('Booking request rejected','wprentals'),
		'deletebookinguser'         =>  __('Booking Request Cancelled','wprentals'),
		'deletebookingconfirmed'    =>  __('Booking Period Cancelled ','wprentals'),
		'new_wire_transfer'         =>  __('New wire Transfer','wprentals'),
		'admin_new_wire_transfer'   =>  __('Admin - New wire Transfer','wprentals'),
		'full_invoice_reminder'     =>  __('Invoice Payment Reminder','wprentals'),
	);


 foreach ($sms_array as $key=>$label ){

   // $value          = stripslashes( wprentals_get_option('wp_estate_'.$key,'') );

	$temp_array = array(
			'id'       => 'wp_estate_sms_'.$key,
			'type'     => 'text',
			'title'    => __( 'SMS for', 'wprentals-core' ).' '.$label,
			'subtitle' => wpestate_emails_extra_details($key,1),
		);

	$sms_data_array[]=$temp_array;
 }














Redux::setSection( $opt_name, array(
	'title'      => __( 'SMS Management', 'wprentals-core' ),
	'id'         => 'sms_notice_tab',
	'desc'       => __( 'SMS Management is offered through Twilio API <a href="https://www.twilio.com">https://www.twilio.com</a>. You will need an active account with them to use their SMS service and you may need to buy extra SMS as well. Your account info will have to be added below. ', 'wprentals-core' ),
	'subsection' => false,
	'fields'     => $sms_data_array,
) );








// -> START help Selection
Redux::setSection( $opt_name, array(
	'title' => __( 'Help & Custom', 'wprentals-core' ),
	'id'    => 'help_custom_sidebar',
	'icon'  => 'el el-question',

) );

Redux::setSection( $opt_name, array(
	'title'      => __( 'Help & Custom', 'wprentals-core' ),
	'id'         => 'help_custom_tab',
	'subsection' => true,
	   'fields'     => array(
		array(
			'id'     => 'opt-info-normal',
			'type'   => 'info',
			'notice' => false,
			'desc'   => __( 'For support please go to ', 'wprentals-core' ).'< a href="http://support.wpestate.org/" target="_blank"> http://support.wpestate.org/ </a>'.__( 'create an account and post a ticket. The registration is simple and as soon as you post we are notified. We usually answer in the next 24h (except weekends). Please use this system and not the email. It will help us answer much faster. Thank you! ', 'wprentals-core' )
			.'</br></br>'.__( 'For custom work on this theme please go to ', 'wprentals-core' ) .'< a href="http://support.wpestate.org/" target="_blank"> http://support.wpestate.org/ </a>'.__( ', create a ticket with your request and we will offer a free quote. ', 'wprentals-core' )
			.'</br></br>'.__( 'For help files please go to ', 'wprentals-core' ) .'< a href="https://help.wprentals.org/" target="_blank"> https://help.wprentals.org/</a>'
			.'</br></br>'.__( 'Subscribe to our mailing list in order to receive news about new features and theme upgrades ', 'wprentals-core' ) .'< a href="http://eepurl.com/CP5U5" target="_blank"> Subscribe Here!</a>'
		),
		array(
			'id'       => 'wp_estate_support',
			'type'     => 'button_set',
			'title'    => __( 'WpEstate Fan', 'wprentals-core' ),
			'subtitle' => __( 'The option "Yes" places a discrete link to wpestate.org in the footer.', 'wprentals-core' ),
			'options'  => array(
						'no'  => 'no',
						'yes' => 'yes'
						),
			'default'  => 'no',
		),
	),
) );


/*
 * <--- END SECTIONS
 */

/*
 * YOU MUST PREFIX THE FUNCTIONS BELOW AND ACTION FUNCTION CALLS OR OTHER CONFIGS MAY OVERRIDE YOUR CODE.
 */

/*
 * --> Action hook examples.
 */

// Function to test the compiler hook and demo CSS output.
// Above 10 is a priority, but 2 is necessary to include the dynamically generated CSS to be sent to the function.
// add_filter('redux/options/' . $opt_name . '/compiler', 'compiler_action', 10, 3);
//
// Change the arguments after they've been declared, but before the panel is created.
// add_filter('redux/options/' . $opt_name . '/args', 'change_arguments' );
//
// Change the default value of a field after it's been set, but before it's been used.
// add_filter('redux/options/' . $opt_name . '/defaults', 'change_defaults' );
//
// Dynamically add a section.
// It can be also used to modify sections/fields.
// add_filter('redux/options/' . $opt_name . '/sections', 'dynamic_section');
// .
if ( ! function_exists( 'compiler_action' ) ) {
	/**
	 * This is a test function that will let you see when the compiler hook occurs.
	 * It only runs if a field's value has changed and compiler => true is set.
	 *
	 * @param array  $options        Options values.
	 * @param string $css            Compiler selector CSS values  compiler => array( CSS SELECTORS ).
	 * @param array  $changed_values Any values that have changed since last save.
	 */
	function compiler_action( array $options, string $css, array $changed_values ) {
		echo '<h1>The compiler hook has run!</h1>';
		echo '<pre>';
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions
		print_r( $changed_values ); // Values that have changed since the last save.
		// echo '<br/>';
		// print_r($options); //Option values.
		// echo '<br/>';
		// print_r($css); // Compiler selector CSS values compiler => array( CSS SELECTORS ).
		echo '</pre>';
	}
}

if ( ! function_exists( 'redux_validate_callback_function' ) ) {
	/**
	 * Custom function for the callback validation referenced above
	 *
	 * @param array $field          Field array.
	 * @param mixed $value          New value.
	 * @param mixed $existing_value Existing value.
	 *
	 * @return array
	 */
	function redux_validate_callback_function( array $field, $value, $existing_value ): array {
		$error   = false;
		$warning = false;

		// Do your validation.
		if ( 1 === (int) $value ) {
			$error = true;
			$value = $existing_value;
		} elseif ( 2 === (int) $value ) {
			$warning = true;
			$value   = $existing_value;
		}

		$return['value'] = $value;

		if ( true === $error ) {
			$field['msg']    = 'your custom error message';
			$return['error'] = $field;
		}

		if ( true === $warning ) {
			$field['msg']      = 'your custom warning message';
			$return['warning'] = $field;
		}

		return $return;
	}
}


if ( ! function_exists( 'dynamic_section' ) ) {
	/**
	 * Custom function for filtering the section array.
	 * Good for child themes to override or add to the sections.
	 * Simply include this function in the child themes functions.php file.
	 * NOTE: the defined constants for URLs and directories will NOT be available at this point in a child theme,
	 * so you must use get_template_directory_uri() if you want to use any of the built-in icons.
	 *
	 * @param array $sections Section array.
	 *
	 * @return array
	 */
	function dynamic_section( array $sections ): array {
		$sections[] = array(
			'title'  => esc_html__( 'Section via hook', 'your-textdomain-here' ),
			'desc'   => '<p class="description">' . esc_html__( 'This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.', 'your-textdomain-here' ) . '</p>',
			'icon'   => 'el el-paper-clip',

			// Leave this as a blank section, no options just some intro text set above.
			'fields' => array(),
		);

		return $sections;
	}
}

if ( ! function_exists( 'change_arguments' ) ) {
	/**
	 * Filter hook for filtering the args.
	 * Good for child themes to override or add to the args array.
	 * It can also be used in other functions.
	 *
	 * @param array $args Global arguments array.
	 *
	 * @return array
	 */
	function change_arguments( array $args ): array {
		$args['dev_mode'] = true;

		return $args;
	}
}

if ( ! function_exists( 'change_defaults' ) ) {
	/**
	 * Filter hook for filtering the default value of any given field. Very useful in development mode.
	 *
	 * @param array $defaults Default value array.
	 *
	 * @return array
	 */
	function change_defaults( array $defaults ): array {
		$defaults['str_replace'] = esc_html__( 'Testing filter hook!', 'your-textdomain-here' );

		return $defaults;
	}
}

if ( ! function_exists( 'redux_custom_sanitize' ) ) {
	/**
	 * Function to be used if the field sanitizes argument.
	 * Return value MUST be formatted or cleaned text to display.
	 *
	 * @param string $value Value to evaluate or clean.  Required.
	 *
	 * @return string
	 */
	function redux_custom_sanitize( string $value ): string {
		$return = '';

		foreach ( explode( ' ', $value ) as $w ) {
			foreach ( str_split( $w ) as $k => $v ) {
				if ( ( $k + 1 ) % 2 !== 0 && ctype_alpha( $v ) ) {
					$return .= mb_strtoupper( $v );
				} else {
					$return .= $v;
				}
			}

			$return .= ' ';
		}

		return $return;
	}
}
