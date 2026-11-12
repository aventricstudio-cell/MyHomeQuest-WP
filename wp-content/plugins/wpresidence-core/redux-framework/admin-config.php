<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: https://devs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux' ) ) {
	return;
}

// This is your option name where all the Redux data is stored.
$opt_name = 'wpresidence_admin';  // YOU MUST CHANGE THIS.  DO NOT USE 'redux_demo' IN YOUR PROJECT!!!

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
  $menu_icon = wpresidence_get_theme_branding_logo_url(); // Just returns the URL
// TYPICAL → Change these values as you need/desire.
$args = array(
	// This is where your data is stored in the database and also becomes your global variable name.
	'opt_name'                  => $opt_name,

	// Name that appears at the top of your panel.
	'display_name'              => wpresidence_theme_branding() ,
    //$theme->get( 'Name' ),

	// Version that appears at the top of your panel.
	'display_version'           => $theme->get( 'Version' ),

	// Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
	'menu_type'                 => 'menu',

	// Show the sections below the admin menu item or not.
	'allow_sub_menu'            => true,

	// The text to appear in the admin menu.
   'menu_title' => wpresidence_theme_branding() . ' ' . __('Theme Options', 'wpresidence-core'),
'page_title' => wpresidence_theme_branding() . ' ' . __('Theme Options', 'wpresidence-core'),
	// Disable to create your own Google fonts loader.
	'disable_google_fonts_link' => false,

	// Show the panel pages on the admin bar.
	'admin_bar'                 => true,

	// Icon for the admin bar menu.
	'admin_bar_icon'            => 'dashicons-portfolio',

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
	'disable_save_warn'         => true,

	// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_priority'             => 3,

	// For a full list of options, visit: https://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters.
	'page_parent'               => 'themes.php',

	// Permissions needed to access the options panel.
	'page_permissions'          => 'manage_options',

	// Specify a custom URL to an icon.
    'menu_icon' =>   $menu_icon ,

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
	'default_mark'              => '',

	// Shows the Import/Export panel when not used as a field.
	'show_import_export'        => true,

	// Shows the Options Object for debugging purposes. Show be set to false before deploying.
	'show_options_object'       => false,

	// The time transients will expire when the 'database' arg is set.
	'transient_time'            => 60 * MINUTE_IN_SECONDS,

	// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output.
	'output'                    => true,

	// Allows dynamic CSS to be generated for customizer and google fonts,
	// but stops the dynamic CSS from going to the page head.
	'output_tag'                => true,

	// Disable the footer credit of Redux. Please leave if you can help it.
	'footer_credit'             => false,

	// If you prefer not to use the CDN for ACE Editor.
	// You may download the Redux Vendor Support plugin to run locally or embed it in your code.
	'use_cdn'                   => true,

	// Set the theme of the option panel.  Use 'wp' to use a more modern style, default is classic.
	'admin_theme'               => 'wp',

	// Enable or disable flyout menus when hovering over a menu with submenus.
	'flyout_submenus'           => false,

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
/*$args['admin_bar_links'][] = array(
	'id'    => 'redux-docs',
	'href'  => '//devs.redux.io/',
	'title' => __( 'Documentation', 'your-textdomain-here' ),
);

$args['admin_bar_links'][] = array(
	'id'    => 'redux-support',
	'href'  => '//github.com/ReduxFramework/redux-framework/issues',
	'title' => __( 'Support', 'your-textdomain-here' ),
);*/

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
}else {
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
		'title'   => esc_html__( 'Theme Information 1', 'your-textdomain-here' ),
		'content' => '<p>' . esc_html__( 'This is the tab content, HTML is allowed.', 'your-textdomain-here' ) . '</p>',
	),
	array(
		'id'      => 'redux-help-tab-2',
		'title'   => esc_html__( 'Theme Information 2', 'your-textdomain-here' ),
		'content' => '<p>' . esc_html__( 'This is the tab content, HTML is allowed.', 'your-textdomain-here' ) . '</p>',
	),
);
Redux::set_help_tab( $opt_name, $help_tabs );

// Set the help sidebar.
$content = '<p>' . esc_html__( 'This is the sidebar content, HTML is allowed.', 'your-textdomain-here' ) . '</p>';

Redux::set_help_sidebar( $opt_name, $content );

/*
 * <--- END HELP TABS
 */

/*
 * ---> START SECTIONS
 */

 $WpestateFunk = WpestateFunk::get_instance();
 $measure_array = array(
     esc_html__('ft', 'wpresidence-core') => esc_html__('square feet - ft2', 'wpresidence-core'),
     esc_html__('m', 'wpresidence-core') => esc_html__('square meters - m2', 'wpresidence-core'),
     esc_html__('ac', 'wpresidence-core') => esc_html__('acres - ac', 'wpresidence-core'),
     esc_html__('yd', 'wpresidence-core') => esc_html__('square yards - yd2', 'wpresidence-core'),
     esc_html__('ha', 'wpresidence-core') => esc_html__('hectares - ha', 'wpresidence-core'),
 );
 
 $measure_array_lot_size = array(
     ''=>esc_html__('Not Set - Property measurement size will be used','wpresidence-core'),
     esc_html__('ft', 'wpresidence-core') => esc_html__('square feet - ft2', 'wpresidence-core'),
     esc_html__('m', 'wpresidence-core') => esc_html__('square meters - m2', 'wpresidence-core'),
     esc_html__('ac', 'wpresidence-core') => esc_html__('acres - ac', 'wpresidence-core'),
     esc_html__('yd', 'wpresidence-core') => esc_html__('square yards - yd2', 'wpresidence-core'),
     esc_html__('ha', 'wpresidence-core') => esc_html__('hectares - ha', 'wpresidence-core'),
 );
 
 $plugin_img_path = plugins_url('/img/', dirname(__FILE__));
 
 if( ! $WpestateFunk->is_registered() ){
 
     Redux::setSection($opt_name, array(
         'title' => __('General', 'wpresidence-core'),
         'id' => 'general_settings_sidebar',
         'icon' => 'el el-adjust-alt'
     ));
 
     Redux::setSection($opt_name, array(
         'title' => __('General Settings', 'wpresidence-core'),
         'id' => 'global_settings_tab',
         'subsection' => true,
         'fields' => array(
             array(
                 'id' => 'noticelicense',
                 'type' => 'info',
                 'title' => __('Access to full theme options is limited until your purchase is actived. See this link  <a href="http://help.wpresidence.net/article/how-to-get-your-buyer-license-code/" target="_blank" >link</a> if you don\'t know how to get your license key. Thank you!', 'wpresidence-core'),
                 'style' => 'warning',
             ),
         )
     ));
 
     return;
 }
 
 Redux::setSection($opt_name, array(
     'title' => __('General', 'wpresidence-core'),
     'id' => 'general_settings_sidebar',
     'icon' => 'el el-adjust-alt'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('General Settings', 'wpresidence-core'),
     'id' => 'global_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_general_country',
             'type' => 'select',
             'title' => __('Country', 'wpresidence-core'),
             'subtitle' => __('Select default country', 'wpresidence-core'),
             'options' => wpestate_country_list_redux(),
             'default' => esc_html__("United States", 'wpresidence-core')
         ),
  
         array(
             'id' => 'wp_estate_date_lang',
             'type' => 'select',
             'title' => __('Language for datepicker', 'wpresidence-core'),
             'subtitle' => __('This applies for the calendar field type available for properties.', 'wpresidence-core'),
             'options' => array(
                 'xx' => 'default',
                 'af' => 'Afrikaans',
                 'ar' => 'Arabic',
                 'ar-DZ' => 'Algerian',
                 'az' => 'Azerbaijani',
                 'be' => 'Belarusian',
                 'bg' => 'Bulgarian',
                 'bs' => 'Bosnian',
                 'ca' => 'Catalan',
                 'cs' => 'Czech',
                 'cy-GB' => 'Welsh/UK',
                 'da' => 'Danish',
                 'de' => 'German',
                 'el' => 'Greek',
                 'en-AU' => 'English/Australia',
                 'en-GB' => 'English/UK',
                 'en-NZ' => 'English/New Zealand',
                 'eo' => 'Esperanto',
                 'es' => 'Spanish',
                 'et' => 'Estonian',
                 'eu' => 'Karrikas-ek',
                 'fa' => 'Persian',
                 'fi' => 'Finnish',
                 'fo' => 'Faroese',
                 'fr' => 'French',
                 'fr-CA' => 'Canadian-French',
                 'fr-CH' => 'Swiss-French',
                 'gl' => 'Galician',
                 'he' => 'Hebrew',
                 'hi' => 'Hindi',
                 'hr' => 'Croatian',
                 'hu' => 'Hungarian',
                 'hy' => 'Armenian',
                 'id' => 'Indonesian',
                 'ic' => 'Icelandic',
                 'it' => 'Italian',
                 'it-CH' => 'Italian-CH',
                 'ja' => 'Japanese',
                 'ka' => 'Georgian',
                 'kk' => 'Kazakh',
                 'km' => 'Khmer',
                 'ko' => 'Korean',
                 'ky' => 'Kyrgyz',
                 'lb' => 'Luxembourgish',
                 'lt' => 'Lithuanian',
                 'lv' => 'Latvian',
                 'mk' => 'Macedonian',
                 'ml' => 'Malayalam',
                 'ms' => 'Malaysian',
                 'nb' => 'Norwegian',
                 'nl' => 'Dutch',
                 'nl-BE' => 'Dutch-Belgium',
                 'nn' => 'Norwegian-Nynorsk',
                 'no' => 'Norwegian',
                 'pl' => 'Polish',
                 'pt' => 'Portuguese',
                 'pt-BR' => 'Brazilian',
                 'rm' => 'Romansh',
                 'ro' => 'Romanian',
                 'ru' => 'Russian',
                 'sk' => 'Slovak',
                 'sl' => 'Slovenian',
                 'sq' => 'Albanian',
                 'sr' => 'Serbian',
                 'sr-SR' => 'Serbian-i18n',
                 'sv' => 'Swedish',
                 'ta' => 'Tamil',
                 'th' => 'Thai',
                 'tj' => 'Tajiki',
                 'tr' => 'Turkish',
                 'uk' => 'Ukrainian',
                 'vi' => 'Vietnamese',
                 'zh-CN' => 'Chinese',
                 'zh-HK' => 'Chinese-Hong-Kong',
                 'zh-TW' => 'Chinese Taiwan',
             ),
             'default' => 'en-GB'
         ),
         array(
             'id' => 'wp_estate_google_analytics_code',
             'type' => 'text',
             'title' => __('Google Analytics Tracking id', 'wpresidence-core'),
             'subtitle' => __('Google Analytics Tracking id (ex UA-41924406-1)', 'wpresidence-core'),
         ),
     ),
 ));
 
 $all_pages_dashboard = array(
     'user_dashboard' => __('Property List', 'wpresidence-core'),
     'user_dashboard_main' => __('Dashboard Overview', 'wpresidence-core'),
     'user_dashboard_add' => __('Add New Property', 'wpresidence-core'),
     'user_dashboard_favorite' => __('Favorite Properties', 'wpresidence-core'),
     'user_dashboard_inbox' => __('Inbox', 'wpresidence-core'),
     'user_dashboard_invoices' => __('Invoices', 'wpresidence-core'),
     'user_dashboard_search_result' => __('Property Search Results Page', 'wpresidence-core'),
     'user_dashboard_searches' => __('Saved Searches', 'wpresidence-core'),
     'user_dashboard_analytics' => __('Property Analytics', 'wpresidence-core'),
     'wpestate-crm-dashboard' => __('Crm List', 'wpresidence-core'),
     'wpestate-crm-dashboard_contacts' => __('Crm Contacts', 'wpresidence-core'),
     'wpestate-crm-dashboard_leads' => __('Crm Leads', 'wpresidence-core'),
 );
 
 Redux::setSection($opt_name, array(
     'title' => __('User Types Settings', 'wpresidence-core'),
     'id' => 'user_role_options_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_visible_user_role_dropdown',
             'type' => 'button_set',
             'title' => __('Display a dropdown with user, agent, agency, developer in register form?', 'wpresidence-core'),
             'subtitle' => __('This option applies to all register forms.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_visible_user_role',
             'type' => 'select',
             'multi' => true,
            // 'required' => array('wp_estate_visible_user_role_dropdown', '=', 'yes'),
             'class' => 'class_visible_user_role',
             'title' => __('Select the user roles to display in the registration form dropdown. If the dropdown is not shown, the first option will be used by default.', 'wpresidence-core'),
             'subtitle' => __('This option applies to all register forms.', 'wpresidence-core'),
             'options' => wpresidence_rolemap(),
             'default' => 'subscriber',
         ),
         array(
             'id' => 'wp_estate_admin_submission_user_role',
             'type' => 'select',
             'multi' => true,
             'class' => 'class_visible_user_role',
             'required' => array('wp_estate_visible_user_role_dropdown', '=', 'yes'),
             'title' => __('Select the user roles that the admin must approve manually before users can access the User Dashboard.', 'wpresidence-core'),
             'subtitle' => __('Roles not selected will be approved automatically.', 'wpresidence-core'),
             'options' => array(
                 'wpresidence_agent_role' => __('Agent', 'wpresidence-core'),
                 'wpresidence_agency_role' => __('Agency', 'wpresidence-core'),
                 'wpresidence_developer_role' => __('Developer', 'wpresidence-core')
             ),
             'default' => '',
         ),
       
         array(
             'id' => 'wp_estate_user_page_permission',
             'type' => 'select',
             'multi' => true,
             'class' => 'class_visible_user_role',
             'title' => __('Select the pages that regular users can access in the User Dashboard.', 'wpresidence-core'),
             'subtitle' => __('Only these pages will be visible to users after they log in.', 'wpresidence-core'),
             'options' => $all_pages_dashboard,
             'default' => '',
         ),
 
 
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Logos & Favicon', 'wpresidence-core'),
     'id' => 'logos_favicon_tab',
     'class' => 'logos_fav_class',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_favicon_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Your Favicon', 'wpresidence-core'),
             'subtitle' => __('Upload site favicon in .ico, .png, .jpg or .gif format', 'wpresidence-core'),
         ),
         array(
             'id' => 'opt-info_retina',
             'type' => 'info',
             'notice' => false,
             'title' => __('Upload logos with RETINA version. For RETINA version create first retina logo. Add _2x at the end of name of the original file (for ex logo_2x.jpg for retina and logo.jpg for non retina). Upload the retina logos from Media - Add New. Help - ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/how-to-add-retina-logos/">http://help.wpresidence.net/article/how-to-add-retina-logos/</a>'
         ),
         array(
             'id' => 'wp_estate_logo_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Your Logo', 'wpresidence-core'),
             'subtitle' => __('Use the "Upload" button and "Insert into Post" button from the pop up window.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_stikcy_logo_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Your Sticky Header Logo', 'wpresidence-core'),
             'subtitle' => __('Use the "Upload" button and "Insert into Post" button from the pop up window.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_transparent_logo_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Your Transparent Header Logo', 'wpresidence-core'),
             'subtitle' => __('Use the "Upload" button and "Insert into Post" button from the pop up window.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_mobile_logo_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Mobile/Tablets Logo', 'wpresidence-core'),
             'subtitle' => __('Upload mobile logo in jpg or png format.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_print_logo_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Print Page Logo', 'wpresidence-core'),
             'subtitle' => __('Upload a different logo in jpg or png format for the Print PDF template.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_logo_max_height',
             'type' => 'text',
             'title' => __('Maximum height for the logo in px', 'wpresidence-core'),
             'subtitle' => __('Change the maximum height of the logo. Add only a number (ex: 60). Change Header height and sticky header height in Design -> Header Design.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_logo_max_width',
             'type' => 'text',
             'title' => __('Maximum width for the logo in px', 'wpresidence-core'),
             'subtitle' => __('Change the maximum width of the logo. Add only a number (ex: 200).', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_logo_margin',
             'type' => 'text',
             'title' => __('Margin top for logo in px', 'wpresidence-core'),
             'subtitle' => __('Add logo top margin top as a number (ex: 10)', 'wpresidence-core'),
             'default' => '0'
         ),
     ),
 ));
 
 
 $listing_filter_array = array(
     "0" => esc_html__('Default', 'wpresidence-core'),
     "1" => esc_html__('Price High to Low', 'wpresidence-core'),
     "2" => esc_html__('Price Low to High', 'wpresidence-core'),
     "3" => esc_html__('Newest first', 'wpresidence-core'),
     "4" => esc_html__('Oldest first', 'wpresidence-core'),
     "11" => esc_html__('Newest Edited', 'wpresidence-core'),
     "12" => esc_html__('Oldest Edited ', 'wpresidence-core'),
     "5" => esc_html__('Bedrooms High to Low', 'wpresidence-core'),
     "6" => esc_html__('Bedrooms Low to high', 'wpresidence-core'),
     "7" => esc_html__('Bathrooms High to Low', 'wpresidence-core'),
     "8" => esc_html__('Bathrooms Low to high', 'wpresidence-core'),
 );
 
 Redux::setSection($opt_name, array(
     'title' => __('Lists Layout & Sidebar', 'wpresidence-core'),
     'id' => 'appearance_options_tab',
     'subsection' => true,
     'fields' => array(
 
         array(
             'id' => 'wp_estate_prop_no',
             'type' => 'text',
             'title' => __('No of Properties per Page in Property List Templates and Category/Taxonomy Lists', 'wpresidence-core'),
             'subtitle' => __('Set the Same Value as in WordPress - Settings - Reading - Pages show at most x posts', 'wpresidence-core'),
             'default' => '12'
         ),
 
         array(
             'id' => 'wp_estate_property_list_type_tax_order',
             'type' => 'button_set',
             'title' => __('Properties Default Order in Category/Taxonomy List Pages', 'wpresidence-core'),
             'subtitle' => __('Select the default order for properties in taxonomy list pages', 'wpresidence-core'),
             'options' => $listing_filter_array,
             'default' => "0",
         ),
         
         array(
             'id' => 'wp_estate_property_list_type',
             'type' => 'button_set',
             'title' => __('Layout Type for Property Category/Taxonomy List Pages', 'wpresidence-core'),
             'subtitle' => __('Half map does not support sidebar. Applies for all property categories / taxonomies pages.', 'wpresidence-core'),
             'options' => array(
                 '1' => __('standard', 'wpresidence-core'),
                 '2' => __('half map', 'wpresidence-core')
             ),
             'default' => '1',
         ),

 
         array(
             'id' => 'wp_estate_blog_sidebar',
             'type' => 'button_set',
             'title' => __('Select Sidebar Position for Property Category/Taxonomy List with Standard Layout and Blog Category/Archive List', 'wpresidence-core'),
             'subtitle' => __('Sidebar Position: Right, Left, or None', 'wpresidence-core'),
             'options' => array(
                 'no sidebar' => __('no sidebar', 'wpresidence-core'),
                 'right' => __('right', 'wpresidence-core'),
                 'left' => __('left', 'wpresidence-core')
             ),
             'default' => 'right',
         ),
         
         array(
             'id' => 'wp_estate_blog_sidebar_name',
             'type' => 'select',
             'title' => __('Property Category/Taxonomy and Blog Category/Archive - Select the Sidebar', 'wpresidence-core'),
             'subtitle' => __('Which sidebar to show for blog category/archive list. Create new Sidebars from Appearance -≥ Sidebars.', 'wpresidence-core'),
             'data' => 'sidebars',
             'default' => 'primary-widget-area'
         ),
 
 
 
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Sticky Sidebar', 'wpresidence-core'),
     'id' => 'appearance_stikcyoptions_tab',
     'subsection' => true,
     'fields' => array(
 
         array(
             'id' => 'wp_estate_global_sticky_sidebar',
             'type' => 'button_set',
             'title' => __('Enable Sticky Sidebar for all pages', 'wpresidence-core'),
             'subtitle' => __('Enable Sticky Sidebar for all pages', 'wpresidence-core'),
            
             'options' => array(
                 'no'=>'no',
                 'yes'=>'yes'
             ),
             'default' => 'no',
         ),
     ),
 ));
 

 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_currency/' );
 Redux::setSection($opt_name, array(
     'title' => __('Price & Currency', 'wpresidence-core'),
     'id' => 'price_curency_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_use_short_like_price',
             'type' => 'button_set',
             'title' => __('Display short style price? ', 'wpresidence-core'),
             'subtitle' => __('Show prices in this format : 5,23m or 6.83k. Price Style for maps pins are controlled in Map > Pin management', 'wpresidence-core'),
             'options' => array(
                 'no' => 'no',
                 'yes' => 'yes'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_show_hide_price',
             'type' => 'button_set',
             'title' => __('Hide property price', 'wpresidence-core'),
             'subtitle' => __('Hide property price', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_property_hide_price_text',
             'type' => 'text',
             'title' => __('Property Hide Price replacements text', 'wpresidence-core'),
             'subtitle' => __('The label that appears in place of Price section.', 'wpresidence-core'),
             'default' => 'Price on application'
         ),
         array(
             'id' => 'wp_estate_prices_th_separator',
             'type' => 'text',
             'title' => __('Price - thousands separator', 'wpresidence-core'),
             'subtitle' => __('Set the thousand separator for price numbers.', 'wpresidence-core'),
             'default' => '.',
         ),
         array(
             'id' => 'wp_estate_prices_decimal_poins',
             'type' => 'text',
             'title' => __('Number of decimal points', 'wpresidence-core'),
             'subtitle' => __('Number of decimal points.', 'wpresidence-core'),
             'default' => '2',
         ),
         array(
             'id' => 'wp_estate_prices_decimal_poins_separator',
             'type' => 'text',
             'title' => __('Decimal points separator', 'wpresidence-core'),
             'subtitle' => __('Decimal points separator.', 'wpresidence-core'),
             'default' => '.',
         ),
         array(
             'id' => 'wp_estate_currency_symbol',
             'type' => 'text',
             'title' => __('Currency Symbol', 'wpresidence-core'),
             'subtitle' => __('Enter the currency symbol as you want it to appear with the price. If you need a space between the symbol and the amount (for example, € 100 instead of €100), type the space manually from your keyboard and save.', 'wpresidence-core'),
             'default' => '$',
         ),
         array(
             'id' => 'wp_estate_currency_label_main',
             'type' => 'text',
             'title' => __('Currency Label', 'wpresidence-core'),
             'subtitle' => __('Set the Currency Label for the Multi-Currency Widget dropdown. It must follow ISO 4217 standards. Full list: https://www.xe.com/iso4217.php', 'wpresidence-core'),
             'default' => 'USD',
         ),
         array(
             'id' => 'wp_estate_where_currency_symbol',
             'type' => 'button_set',
             'title' => __('Where to show the currency symbol?', 'wpresidence-core'),
             'subtitle' => __('Set the position for the currency symbol.', 'wpresidence-core'),
             'options' => array(
                 'before' => 'before',
                 'after' => 'after'
             ),
             'default' => 'before',
         ),
         array(
             'id' => 'wp_estate_price_indian_format',
             'type' => 'button_set',
             'title' => __('Use indian format for price?', 'wpresidence-core'),
             'subtitle' => __('Use indian format for price?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_auto_curency',
             'type' => 'button_set',
             'title' => __('Enable Automatic Daily Exchange Rate Updates?', 'wpresidence-core'),
             'subtitle' => __('Currency LABEL must follow ISO 4217 standards. Full list: https://www.xe.com/iso4217.php', 'wpresidence-core'),
                 'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
        array(
             'id' => 'wp_estate_currencylayer_api',
             'type' => 'text',
             'title' => __('Currencylayer.com API Key', 'wpresidence-core'),
             'subtitle' => __('Free plan allows up to 100 API calls per day. Get your key at https://currencylayer.com/#pricing_plan', 'wpresidence-core'),
              'default' => '',
         ),
       /*  array(
             'id' => 'wp_estate_currencyconverterapi_api',
             'type' => 'text',
             'title' => __('Currencylayer.com API Key', 'wpresidence-core'),
             'subtitle' => __('Free plan allows up to 100 API calls per day. Get your key at https://currencylayer.com/#pricing_plan', 'wpresidence-core'),
             'default' => '',
         ),
*/
         array(
             'id' => 'wpestate_currency',
             'type' => 'wpestate_currency',
             'title' => __('Add Currencies for the Multi-Currency Widget', 'wpresidence-core'),
             'class' => 'class_wpestate_currency',
             'full_width' => true,
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Measurement Unit', 'wpresidence-core'),
     'id' => 'measurement_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_measure_sys',
             'type' => 'select',
             'title' => __('Measurement Unit for Property Size', 'wpresidence-core'),
             'subtitle' => __('Select the measurement unit that will be used across the website for property sizes.', 'wpresidence-core'),
             'options' => $measure_array,
             'default' => 'ft'
         ),
         
         array(
             'id' => 'wp_estate_measure_sys_lot_size',
             'type' => 'select',
             'title' => __('Measurement Unit for Lot Size', 'wpresidence-core'),
             'subtitle' => __('Select the measurement unit for Property Lot Size field. If left blank, Measurement Unit for Property Size applies.', 'wpresidence-core'),
             'options' => $measure_array_lot_size,
             'default' => ''
         ),
         
         array(
             'id' => 'wp_estate_size_decimals',
             'type' => 'text',
             'title' => __('Properties Size & Lot Size - no of decimals', 'wpresidence-core'),
             'subtitle' => __('Properties Size & Lot Size - no of decimals', 'wpresidence-core'),
             'default' => '2'
         ),
         array(
             'id' => 'wp_estate_size_decimal_separator',
             'type' => 'text',
             'title' => __('Properties Size & Lot Size - decimal separator', 'wpresidence-core'),
             'subtitle' => __('Properties Size & Lot Size - decimal separator', 'wpresidence-core'),
             'default' => '.'
         ),
         array(
             'id' => 'wp_estate_size_thousand_separator',
             'type' => 'text',
             'title' => __('Properties Size & Lot Size - thousand separator', 'wpresidence-core'),
             'subtitle' => __('Properties Size & Lot Size - thousand separator', 'wpresidence-core'),
             'default' => ','
         ),
 
     ),
 ));
 
 $default_custom_field = array();
 $def_add_field_name = array('property year', 'property garage', 'property garage size', 'property date', 'property basement', 'property external construction', 'property roofing');
 $def_add_field_label = array('Year Built', 'Garages', 'Garage Size', 'Available from', 'Basement', 'external construction', 'short text');
 $def_add_field_order = array(1, 2, 3, 4, 5, 6, 7);
 $def_add_field_type = array('date', 'short text', 'short text', 'short text', 'short text', 'short text', 'short text');
 
 $default_custom_field['add_field_name'] = $def_add_field_name;
 $default_custom_field['add_field_label'] = $def_add_field_label;
 $default_custom_field['add_field_order'] = $def_add_field_order;
 $default_custom_field['add_field_type'] = $def_add_field_type;
 
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_custom_fields_list/' );
 Redux::setSection($opt_name, array(
     'title' => __('Property Custom Fields', 'wpresidence-core'),
     'id' => 'custom_fields_tab',
     'subsection' => true,
     'fields' => array(
        array(
            'id' => 'wpestate_show_acf_fields',
            'type' => 'switch',
            'title' => __('Show ACF Fields in Property Custom Fields', 'wpresidence-core'),
            'subtitle' => __('Enable or disable Advanced Custom Fields integration for property custom fields.', 'wpresidence-core'),
            'default' => 1,
        ),
         array(
             'id' => 'wpestate_custom_fields_list',
             'type' => 'wpestate_custom_fields_list',
             'full_width' => true,
             'title' => __('Add, edit or delete property custom fields.', 'wpresidence-core'),
         // 'default'  => $default_custom_field
         ),
     ),
 ));
 
 $default_feature_list = 'attic, gas heat, ocean view, wine cellar, basketball court, gym,pound, fireplace, lake view, pool, back yard, front yard, fenced yard, sprinklers, washer and dryer, deck, balcony, laundry, concierge, doorman, private space, storage, recreation, roof deck';
 
 
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_custom_url_rewrite/' );
 Redux::setSection($opt_name, array(
     'title' => __('Property, Agent, Agency, Developer Links Names', 'wpresidence-core'),
     'id' => 'property_rewrite_page_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'opt-info_links',
             'type' => 'info',
             'notice' => false,
             'title' => __('You cannot use special characters like "&". After changing the url you may need to wait for a few minutes until WordPress changes all the urls. In case your new names do not update automatically, go to Settings - Permalinks and Save again the "Permalinks Settings" - option "Post name"', 'wpresidence-core')
         ),
         array(
             'id' => 'opt-info_links2',
             'type' => 'info',
             'notice' => false,
             'title' => __(' DO NOT USE "type" as this name is reserved by WordPress ', 'wpresidence-core') . '<a href="https://codex.wordpress.org/Reserved_Terms" target="_blank">https://codex.wordpress.org/Reserved_Terms</a>'
         ),
         array(
             'id' => 'wp_estate_url_rewrites',
             'type' => 'wpestate_custom_url_rewrite',
             'notice' => false,
             'full_width' => true,
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Splash Page', 'wpresidence-core'),
     'id' => 'splash_page_page_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_spash_header_type',
             'type' => 'select',
             'title' => __('Select the splash page type.', 'wpresidence-core'),
             'subtitle' => __('Important: Create also a page with template "Splash Page" to see how your splash settings apply', 'wpresidence-core'),
             'options' => array(
                 'image' => 'image',
                 'video' => 'video',
                 'image slider' => 'image slider'
             ),
             'default' => 'image'
         ),
         array(
             'id' => 'wp_estate_splash_slider_gallery',
             'type' => 'gallery',
             'class' => 'slider_splash',
             'required' => array('wp_estate_spash_header_type', '=', 'image slider'),
             'title' => __('Slider Images', 'wpresidence-core'),
             'subtitle' => __('Slider Images, .png, .jpg or .gif format', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_slider_transition',
             'type' => 'text',
             'class' => 'slider_splash',
             'required' => array('wp_estate_spash_header_type', '=', 'image slider'),
             'title' => __('Slider Transition', 'wpresidence-core'),
             'subtitle' => __('Number of milisecons before auto cycling an item (5000=5sec).Put 0 if you don\'t want to autoslide.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_image',
             'type' => 'media',
             'class' => 'image_splash',
             'required' => array('wp_estate_spash_header_type', '=', 'image'),
             'title' => __('Splash Image', 'wpresidence-core'),
             'subtitle' => __('Splash Image, .png, .jpg or .gif format', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_video_mp4',
             'type' => 'media',
             'class' => 'video_splash',
             'url' => true,
             'preview' => false,
             'mode' => false,
             'required' => array('wp_estate_spash_header_type', '=', 'video'),
             'title' => __('Splash Video in mp4 format', 'wpresidence-core'),
             'subtitle' => __('Splash Video in mp4 format', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_video_webm',
             'type' => 'media',
             'class' => 'video_splash',
             'url' => true,
             'preview' => false,
             'mode' => false,
             'required' => array('wp_estate_spash_header_type', '=', 'video'),
             'title' => __('Splash Video in webm format', 'wpresidence-core'),
             'subtitle' => __('Splash Video in webm format', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_video_ogv',
             'type' => 'media',
             'class' => 'video_splash',
             'url' => true,
             'preview' => false,
             'mode' => false,
             'required' => array('wp_estate_spash_header_type', '=', 'video'),
             'title' => __('Splash Video in ogv format', 'wpresidence-core'),
             'subtitle' => __('Splash Video in ogv format', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_video_cover_img',
             'type' => 'media',
             'class' => 'video_splash',
             'required' => array('wp_estate_spash_header_type', '=', 'video'),
             'title' => __('Cover Image for video', 'wpresidence-core'),
             'subtitle' => __('Cover Image for videot', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_overlay_image',
             'preview' => true,
             'type' => 'media',
             'title' => __('Overlay Image', 'wpresidence-core'),
             'subtitle' => __('Overlay Image, .png, .jpg or .gif format', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_overlay_color',
             'type' => 'color',
             'title' => __('Overlay Color', 'wpresidence-core'),
             'subtitle' => __('Overlay Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_splash_overlay_opacity',
             'type' => 'text',
             'title' => __('Overlay Opacity', 'wpresidence-core'),
             'subtitle' => __('Overlay Opacity- values from 0 to 1 , Ex: 0.4', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_page_title',
             'type' => 'text',
             'title' => __('Splash Page Title', 'wpresidence-core'),
             'subtitle' => __('Splash Page Title', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_page_subtitle',
             'type' => 'text',
             'title' => __('Splash Page Subtitle', 'wpresidence-core'),
             'subtitle' => __('Splash Page Subtitle', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_splash_page_logo_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Logo Link', 'wpresidence-core'),
             'subtitle' => __('In case you want to send users to another page', 'wpresidence-core'),
         ),
     ),
 ));
 
 //->STRAT Social & Contact
 Redux::setSection($opt_name, array(
     'title' => __('Social & Contact', 'wpresidence-core'),
     'id' => 'social_contact_sidebar',
     'icon' => 'el el-address-book'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Contact Page Details', 'wpresidence-core'),
     'id' => 'social_contact_sidebar_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_company_contact_image',
             'type' => 'media',
             'title' => __('Image for Contact Page', 'wpresidence-core'),
             'subtitle' => __('Add the image for the contact page contact area. Minim 350px wide for a nice design.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_company_name',
             'type' => 'text',
             'preview' => false,
             'title' => __('Company Name', 'wpresidence-core'),
             'subtitle' => __('Company name for contact page', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_email_adr',
             'type' => 'text',
             'preview' => false,
             'title' => __('Email', 'wpresidence-core'),
             'subtitle' => __('Company email', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_duplicate_email_adr',
             'type' => 'text',
             'preview' => false,
             'title' => __('Duplicate Email', 'wpresidence-core'),
             'subtitle' => __('Send all contact emails to', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_telephone_no',
             'type' => 'text',
             'preview' => false,
             'title' => __('Telephone', 'wpresidence-core'),
             'subtitle' => __('Company phone number.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_mobile_no',
             'type' => 'text',
             'preview' => false,
             'title' => __('Mobile', 'wpresidence-core'),
             'subtitle' => __('Company mobile', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_fax_ac',
             'type' => 'text',
             'preview' => false,
             'title' => __('Fax', 'wpresidence-core'),
             'subtitle' => __('Company fax', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_skype_ac',
             'type' => 'text',
             'preview' => false,
             'title' => __('Skype', 'wpresidence-core'),
             'subtitle' => __('Company skype', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_co_address',
             'type' => 'text',
             'preview' => false,
             'title' => __('Company Address', 'wpresidence-core'),
             'subtitle' => __('Type company address', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_opening_hours_1',
             'type' => 'text',
             'preview' => false,
             'title' => __('Opening Hours 1', 'wpresidence-core'),
             'subtitle' => __('Opening Hours ', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_opening_hours_value_1',
             'type' => 'text',
             'preview' => false,
             'title' => __('Opening Hours Value 1', 'wpresidence-core'),
             'subtitle' => __('Opening Hours Value 1', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_opening_hours_2',
             'type' => 'text',
             'preview' => false,
             'title' => __('Opening Hours 1', 'wpresidence-core'),
             'subtitle' => __('Opening Hours ', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_opening_hours_value_2',
             'type' => 'text',
             'preview' => false,
             'title' => __('Opening Hours Value 1', 'wpresidence-core'),
             'subtitle' => __('Opening Hours Value 1', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_opening_hours_3',
             'type' => 'text',
             'preview' => false,
             'title' => __('Opening Hours 3', 'wpresidence-core'),
             'subtitle' => __('Opening Hours 3', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_opening_hours_value_3',
             'type' => 'text',
             'preview' => false,
             'title' => __('Opening Hours Value 3', 'wpresidence-core'),
             'subtitle' => __('Opening Hours Value 3', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_hq_latitude',
             'type' => 'text',
             'preview' => false,
             'title' => __('Contact Page - Company HQ Latitude', 'wpresidence-core'),
             'subtitle' => __('Set company pin location for contact page template. Latitude must be a number (ex: 40.577906).', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_hq_longitude',
             'type' => 'text',
             'preview' => false,
             'title' => __('Contact Page - Company HQ Longitude', 'wpresidence-core'),
             'subtitle' => __('Set company pin location for contact page template. Longitude must be a number (ex: -74.155058).', 'wpresidence-core'),
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Social Accounts', 'wpresidence-core'),
     'id' => 'social_accounts_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_facebook_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Facebook Link', 'wpresidence-core'),
             'subtitle' => __('Facebook page url, with https://', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_twitter_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('X - Twitter page link', 'wpresidence-core'),
             'subtitle' => __('X - Twitter page link, with https://', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_google_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Google+ Link', 'wpresidence-core'),
             'subtitle' => __('Google+ page link, with https://', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_linkedin_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Linkedin Link', 'wpresidence-core'),
             'subtitle' => __('Linkedin Link', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_pinterest_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Pinterest Link', 'wpresidence-core'),
             'subtitle' => __('Pinterest page link, with https://', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_instagram_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Instagram Link', 'wpresidence-core'),
             'subtitle' => __('Instagram page link, with https://', 'wpresidence-core'),
         ),
         
         array(
             'id' => 'wp_estate_whatsapp_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('WhatsApp Link', 'wpresidence-core'),
             'subtitle' => __('WhatsApp page link, with https://', 'wpresidence-core'),
         ),
         
         
         array(
             'id' => 'wp_estate_tiktok_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Tiktok Link', 'wpresidence-core'),
             'subtitle' => __('Tiktok page link, with https://', 'wpresidence-core'),
         ),
         
         array(
             'id' => 'wp_estate_line_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Line Link', 'wpresidence-core'),
             'subtitle' => __('Line page link, with https://', 'wpresidence-core'),
         ),
         
         array(
             'id' => 'wp_estate_telegram_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Telegram Link', 'wpresidence-core'),
             'subtitle' => __('Telegram page link, with https://', 'wpresidence-core'),
         ),
         
         array(
             'id' => 'wp_estate_wechat_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Wechat Link', 'wpresidence-core'),
             'subtitle' => __('Wechat page link, with https://', 'wpresidence-core'),
         ),
         
         array(
             'id' => 'wp_estate_foursquare_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Foursquare Link', 'wpresidence-core'),
             'subtitle' => __('Foursquare page link, with https://', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_vimeo_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Vimeo Link', 'wpresidence-core'),
             'subtitle' => __('Vimeo page link, with https://', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_youtube_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Youtube Link', 'wpresidence-core'),
             'subtitle' => __('Youtube page link, with https://', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_dribbble_link',
             'type' => 'text',
             'preview' => false,
             'title' => __('Dribbble Link', 'wpresidence-core'),
             'subtitle' => __('Dribbble page link, with https://', 'wpresidence-core'),
         )
         , array(
                 'id'       => 'wp_estate_zillow_api_key',
                 'type'     => 'text',
                 'preview'  => false,
                 'title'    => __( 'Zillow api key', 'wpresidence-core' ),
                 'subtitle' => __( 'Zillow api key is required for Zillow Widget.', 'wpresidence-core' ),
             ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Social Login', 'wpresidence-core'),
     'id' => 'social_login_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_facebook_login',
             'type' => 'button_set',
             'title' => __('Allow login via Facebook?', 'wpresidence-core'),
             'subtitle' => __('Allow login via Facebook?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_facebook_api',
             'required' => array('wp_estate_facebook_login', '=', 'yes'),
             'type' => 'text',
             'title' => __('Facebook Api key', 'wpresidence-core'),
             'subtitle' => __('Facebook Api key is required for Facebook login. See this help article before: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/facebook-login/" target="_blank">http://help.wpresidence.net/article/facebook-login/</a>',
         ),
         array(
             'id' => 'wp_estate_facebook_secret',
             'required' => array('wp_estate_facebook_login', '=', 'yes'),
             'type' => 'text',
             'title' => __('Facebook Secret', 'wpresidence-core'),
             'subtitle' => __('Facebook Secret is required for Facebook login.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_google_login',
             'type' => 'button_set',
             'title' => __('Allow login via Google?', 'wpresidence-core'),
             'subtitle' => __('Enable or disable Google login.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_google_oauth_api',
             'required' => array('wp_estate_google_login', '=', 'yes'),
             'type' => 'text',
             'title' => __('Google Oauth Api', 'wpresidence-core'),
             'subtitle' => __('Google Oauth Api is required for Google Login. See this help article before: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/enable-gmail-google-login/" target="_blank">http://help.wpresidence.net/article/enable-gmail-google-login/</a>',
         ),
         array(
             'id' => 'wp_estate_google_oauth_client_secret',
             'required' => array('wp_estate_google_login', '=', 'yes'),
             'type' => 'text',
             'title' => __('Google Oauth Client Secret', 'wpresidence-core'),
             'subtitle' => __('Google Oauth Client Secret is required for Google Login.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_google_api_key',
             'required' => array('wp_estate_google_login', '=', 'yes'),
             'type' => 'text',
             'title' => __('Google api key', 'wpresidence-core'),
             'subtitle' => __('Google api key is required for Google Login.', 'wpresidence-core'),
         ),
     ),
 ));
 
 $siteurl = 'noreply@' . parse_url(get_site_url(), PHP_URL_HOST);
 
 Redux::setSection($opt_name, array(
     'title' => __('Contact Form Settings', 'wpresidence-core'),
     'id' => 'contact_form_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_use_gdpr',
             'type' => 'button_set',
             'title' => __('Use GDPR / Agree with Terms Checkbox ?', 'wpresidence-core'),
             'subtitle' => __('Help: ', 'wpresidence-core') . '<a href ="http://help.wpresidence.net/article/contact-form-settings/">http://help.wpresidence.net/article/contact-form-settings/</a>',
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
           array(
             'id' => 'wp_estate_use_gdpr_text',
             'type' => 'text',
            'required'  => array('wp_estate_use_gdpr', '=', 'yes'),
             'title'    => __('Text for the checkbox label', 'wpresidence-core'),
             'subtitle' => __('Text for the checkbox label', 'wpresidence-core'),
            'default'   =>esc_html__('I consent to the ','wpresidence_core')
         ),
           array(
             'id' => 'wp_estate_use_gdpr_text_terms',
             'type' => 'text',
            'required' => array('wp_estate_use_gdpr', '=', 'yes'),
             'title' => __('Text for terms link', 'wpresidence-core'),
             'subtitle' =>  __('Text for terms link', 'wpresidence-core'),
             'default'  =>  esc_html__('GDPR Terms','wpresidence_core')
         ),

     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Contact 7 Settings', 'wpresidence-core'),
     'id' => 'contact7_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_contact_form_7_agent',
             'type' => 'text',
             'title' => __('Contact form 7 code for agent', 'wpresidence-core'),
             'subtitle' => __('Contact form 7 code for agent (ex: [contact-form-7 id="2725" title="contact me"])', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_contact_form_7_contact',
             'type' => 'text',
             'title' => __('Contact form 7 code for contact page', 'wpresidence-core'),
             'subtitle' => __('Contact form 7 code for contact page template (ex: [contact-form-7 id="2725" title="contact me"])', 'wpresidence-core'),
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('X - Twitter Login & Widget', 'wpresidence-core'),
     'id' => 'twitter_widget_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_twiter_login',
             'type' => 'button_set',
             'title' => __('Allow login via X - Twitter?', 'wpresidence-core'),
             'subtitle' => __('Allow login via X - Twitter? (works only over https)', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_twitter_consumer_key',
             'type' => 'text',
             'title' => __('X - Twitter consumer_key.', 'wpresidence-core'),
             'subtitle' => __('X - Twitter consumer_key is required for theme Twitter widget. See this help article before: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/wp-estate-twitter-widget/" target="_blank">http://help.wpresidence.net/article/wp-estate-twitter-widget/</a>',
         ),
         array(
             'id' => 'wp_estate_twitter_consumer_secret',
             'type' => 'text',
             'title' => __('X - Twitter Consumer Secret', 'wpresidence-core'),
             'subtitle' => __('X - Twitter Consumer Secret is required for theme Twitter widget.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_twitter_access_token',
             'type' => 'text',
             'title' => __('X - Twitter Access Token', 'wpresidence-core'),
             'subtitle' => __('X - Twitter Access Token is required for theme Twitter widget.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_twitter_access_secret',
             'type' => 'text',
             'title' => __('X - Twitter Access Token Secret', 'wpresidence-core'),
             'subtitle' => __('X - Twitter Access Token Secret is required for theme Twitter widget.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_twitter_cache_time',
             'type' => 'text',
             'title' => __('X- Twitter Cache Time', 'wpresidence-core'),
             'subtitle' => __('X - Twitter Cache Time', 'wpresidence-core'),
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Header', 'wpresidence-core'),
     'id' => 'headers_settings_sidebar',
     'icon' => 'el el el-photo'
 ));
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Header Settings', 'wpresidence-core'),
     'id' => 'header_style_tab',
     'subsection' => true,
     'fields' => array(
 
         array(
             'id' => 'wp_estate_wide_header',
             'type' => 'button_set',
             'title' => __('Wide Header?', 'wpresidence-core'),
             'subtitle' => __('Makes the header width 100%.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
 
         
         array(
             'id' => 'wp_estate_header_phone_no',
             'type' => 'text',
             'title' => __('Phone Number. Leave blank to show none', 'wpresidence-core'),
             'subtitle' => __('Show your phone number in header next to Login/Register icon.', 'wpresidence-core'),
             'default' => ''
         ),
 
         array(
             'id' => 'wp_estate_logo_header_type',
             'type' => 'button_set',
             'title' => __('Header Layout', 'wpresidence-core'),
             'subtitle' => __('Header Layout: Sets the placement of the logo, menu, login, and submit button in the header. Note: Header Type 3 doesn’t work with the half-map property list template.', 'wpresidence-core'),
             'options' => array(
                 'type1' => 'type 1',
                 'type2' => 'type 2',
                 'type4' => 'type 3',
                 'type5' => 'type 4',
                 'type6' => 'type 5'
             ),
             'default' => 'type1',
         ),
 
         array(
             'id' => 'wp_estate_menu_header_align',
             'type' => 'button_set',
             'required' => array('wp_estate_logo_header_type', '=', 'type1'),
             'title' => __('Main Menu Alignment for Header Type 1?', 'wpresidence-core'),
             'subtitle' => __('Select the menu align position for Header Type 1.', 'wpresidence-core'),
             'options' => array(
                 'left' => 'left',
                 'center' => 'center - equal spaces between elements',
                 'abscenter' => ' row center',
                 'right' => 'right'
             ),
             'default' => 'center',
         ),
 
                   
         array(
             'id' => 'wp_estate_menu_header_align_abs_centers',
             'type' => 'text',
             'required' => array('wp_estate_menu_header_align', '=', 'abscenter'),
             'title' => __('Width of the logo and user menu area.', 'wpresidence-core'),
             'subtitle' => __('To center the menu absolutely, the logo and user menu area need to have the same width in pixels.', 'wpresidence-core'),
             'default' => '290'
         ),
     
         
         array(
             'id' => 'wp_estate_text_header_align',
             'type' => 'button_set',
             'title' => __('Header type 3 Menu Text Alignment?', 'wpresidence-core'),
             'subtitle' => __('Select the text alignment for header type 3.', 'wpresidence-core'),
             'required' => array('wp_estate_logo_header_type', '=', 'type4'),
             'options' => array(
                 'left' => 'left',
                 'center' => 'center',
                 'right' => 'right'
             ),
             'default' => 'left',
         ),
 
         array(
             'id' => 'wp_estate_header5_info_widget1_icon',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'type' => 'text',
             'title' => __('Header 4 - Info widget 1 - icon', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 1 - icon. Ex: fa fa-phone', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget1_text1',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 1 - First line of text', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 1 - First line of text', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget1_text2',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 1 - Second line of text', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 1 - Second line of text', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget2_icon',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 2 - icon', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 2 - icon. Ex: fa fa-phone', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget2_text1',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 2 - First line of text', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 2 - First line of text', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget2_text2',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 2 - Second line of text', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 2 - Second line of text', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget3_icon',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 3 - icon', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 3 - icon. Ex: fa fa-phone', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget3_text1',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 3 - First line of text', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 3 - First line of text', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header5_info_widget3_text2',
             'type' => 'text',
             'required' => array('wp_estate_logo_header_type', '=', 'type5'),
             'title' => __('Header 4 - Info widget 3 - Second line of text', 'wpresidence-core'),
             'subtitle' => __('Header 4 - Info widget 3 - Second line of text', 'wpresidence-core'),
         ),
 
         
         array(
             'id' => 'wp_estate_header3_sidebar_header_align',
             'type' => 'button_set',
             'required' => array(
                array('wp_estate_logo_header_type', '=', 'type4'),
            ),
             'title' => __('Header type 3 vertical postion', 'wpresidence-core'),
             'subtitle' => __('Header type 3 vertical postion', 'wpresidence-core'),
             'options' => array(
                 'left' => 'left',
                 'right' => 'right'
             ),
             'default' => 'left',
         ),
         array(
             'id' => 'wp_estate_logo_header_align',
             'type' => 'button_set',
             'required' => array(   
                array('wp_estate_logo_header_type', '!=', array('type4') ),
            ),
             'title' => __('Logo position in header?', 'wpresidence-core'),
             'subtitle' => __('Important: The "Center" option is not applicable to headers of types 1, 4, and 5.', 'wpresidence-core'),
             'options' => array(
                 'left' => 'left',
                 'center' => 'center',
                 'right' => 'right'
             ),
             'default' => 'left',
         ),
 
         array(
             'id' => 'wp_estate_header_height',
             'type' => 'text',
             'title' => __('Header Height', 'wpresidence-core'),
             'subtitle' => __('Header Height in pixels. Use numbers only', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_sticky_header_height',
             'type' => 'text',
             'title' => __('Sticky Header Height', 'wpresidence-core'),
             'subtitle' => __('Sticky Header Height in pixels. Use numbers only', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_top_menu_font_size',
             'type' => 'text',
             'title' => __('Top Menu Font Size', 'wpresidence-core'),
             'subtitle' => __('Top Menu Font Size. Use numbers only', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_menu_item_font_size',
             'type' => 'text',
             'title' => __('Menu Item Font Size', 'wpresidence-core'),
             'subtitle' => __('Menu Item Font Size', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_border_bottom_header',
             'type' => 'text',
             'title' => __('Height of the border below header (in pixels)', 'wpresidence-core'),
             'subtitle' => __('Adds a colored border below header', 'wpresidence-core'),
         ),
 
         array(
             'id' => 'wp_estate_sticky_border_bottom_header',
             'type' => 'text',
             'title' => __('Height of the border below sticky header (in pixels)', 'wpresidence-core'),
             'subtitle' => __('Adds a colored border below sticky header', 'wpresidence-core'),
         ),
 
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Login / Register & Submit', 'wpresidence-core'),
     'id' => 'login_style_tab',
     'subsection' => true,
    'fields' => array(

        array(
            'id' => 'wp_estate_allow_user_registration',
            'type' => 'button_set',
            'title' => __('Allow user registration?', 'wpresidence-core'),
            'subtitle' => __('Enable or disable user registration.', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'yes',
        ),

        array(
            'id' => 'wp_estate_show_top_bar_user_login',
            'type' => 'button_set',
            'title' => __('Show user login menu in header?', 'wpresidence-core'),
            'subtitle' => __('Enable or disable user login menu in header.', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
        ),

        array(
            'id' => 'wp_estate_show_submit',
             'type' => 'button_set',
             'title' => __('Show submit property button in header?', 'wpresidence-core'),
             'subtitle' => __('Submit property will show only with theme register/login for header enabled.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
 
         array(
             'id' => 'wp_estate_enable_user_pass',
             'type' => 'button_set',
             'title' => __('Users can type the password on registration form?', 'wpresidence-core'),
             'subtitle' => __('If no, users will get the auto generated password via email', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
         array(
             'id' => 'wp_estate_login_modal_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Login/Register Modal Image', 'wpresidence-core'),
             'subtitle' => __('Login/Register Modal Image', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_login_modal_message',
             'type' => 'text',
             'default' => 'Some Nice Welcome Message',
             'title' => __('Login/Register Modal Message', 'wpresidence-core'),
             'subtitle' => __('Login/Register Modal Message', 'wpresidence-core'),
         ),
 
         array(
             'id' => 'wp_estate_favorites_login',
             'type' => 'button_set',
             'title' => __('Login is mandatory to save favorite properties? ', 'wpresidence-core'),
             'subtitle' => __('If yes, users must login to save and view favorite properties', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
  
         array(
             'id' => 'wp_estate_login_redirect',
             'type' => 'text',
             'title' => __('Url where the user will be redirected after login.', 'wpresidence-core'),
             'subtitle' => __('If left blank, user redirects to the dashboard profile page .', 'wpresidence-core'),
             'default' => ''
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Register reCaptcha Settings', 'wpresidence-core'),
     'id' => 'recaptcha_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_use_captcha',
             'type' => 'button_set',
             'title' => __('Add reCaptcha to register form?', 'wpresidence-core'),
             'subtitle' => __('This helps preventing registration spam with ReCaptcha system. All theme forms are already secured. ReCaptcha is Optional.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_recaptha_sitekey',
             'type' => 'text',
             'required' => array('wp_estate_use_captcha', '=', 'yes'),
             'title' => __('reCaptha site key', 'wpresidence-core'),
             'subtitle' => __('Get this detail after you signup here: ', 'wpresidence-core') . '<a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">https://www.google.com/recaptcha/intro/index.html</a>',
         ),
         array(
             'id' => 'wp_estate_recaptha_secretkey',
             'type' => 'text',
             'required' => array('wp_estate_use_captcha', '=', 'yes'),
             'title' => __('reCaptha secret key', 'wpresidence-core'),
             'subtitle' => __('Get this detail after you signup here: ', 'wpresidence-core') . '<a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">https://www.google.com/recaptcha/intro/index.html</a>',
         ),
     ),
 ));
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Top Bar', 'wpresidence-core'),
     'id' => 'header_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_top_bar_user_menu',
             'type' => 'button_set',
             'title' => __('Show top bar widgets menu?', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the top bar widgets area. If enabled, see this help article to add widgets: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/widgets-2/#a_wpestate_id" target = "_blank"> http://help.wpresidence.net/article/widgets-2/ </a>',
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_show_top_bar_user_menu_mobile',
             'type' => 'button_set',
             'required' => array('wp_estate_show_top_bar_user_menu', '=', 'yes'),
             'title' => __('Show top bar on mobile devices?', 'wpresidence-core'),
             'subtitle' => __('Top Bar will not display on Mobile devices when Sticky mobile header is set YES!', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
         array(
             'id' => 'wp_estate_topbar_transparent',
             'type' => 'button_set',
             'title' => __('Global transparent top bar widgets menu?', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the use of transparent top bar widgets menu globally.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_topbar_border',
             'type' => 'button_set',
             'title' => __('Separation border for top bar widgets?', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the use of borders for top bar widgets globally.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
 
     ),
 ));
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Hero Media Header', 'wpresidence-core'),
     'id' => 'hero_header_tab',
     'subsection' => true,
     'fields' => array(
 
         array(
             'id' => 'wp_estate_header_transparent',
             'type' => 'button_set',
             'title' => __('Global Transparent Header', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the use of transparent header for all pages. Does not work well with Hero Media set to Maps.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
         array(
             'id' => 'wp_estate_header_type',
             'type' => 'button_set',
             'title' => __('Global Hero Media Header Type', 'wpresidence-core'),
             'subtitle' => __('Media Header is the first section below header. Select what media header to use globally.', 'wpresidence-core'),
             'options' => array(
                 'none',
                 'image',
                 'theme slider',
                 'revolution slider',
                 'maps'
             ),
             'default' => 4,
         ),
         array(
             'id' => 'wp_estate_global_revolution_slider',
             'type' => 'text',
             'required' => array('wp_estate_header_type', '=', '3'),
             'title' => __('Hero Media Header - Revolution Slider', 'wpresidence-core'),
             'subtitle' => __('If media header is set to Revolution Slider, type the slider name and save.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_global_header',
             'type' => 'media',
             'url' => true,
             'required' => array('wp_estate_header_type', '=', '1'),
             'title' => __('Hero Media Header - Static Image', 'wpresidence-core'),
             'subtitle' => __('If media header is set to image, add the image below.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header_type_taxonomy',
             'type' => 'button_set',
             'title' => __('Hero Media Header Type for Taxonomy Pages', 'wpresidence-core'),
             'subtitle' => __('Media Header is the first section below header. Select what media header to use globally for taxonomies/categories. Maps selection is mandatory for Half Map layout.', 'wpresidence-core'),
             'options' => array(
                 'none',
                 'image',
                 'theme slider',
                 'revolution slider',
                 'maps'
             ),
             'default' => 4,
         ),
         array(
             'id' => 'wp_estate_header_taxonomy_revolution_slider',
             'type' => 'text',
             'required' => array('wp_estate_header_type_taxonomy', '=', '3'),
             'title' => __('Taxonomy Hero Media Header Type - Revolution Slider', 'wpresidence-core'),
             'subtitle' => __('If media header is set to Revolution Slider, type the slider name and save.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header_taxonomy_image',
             'type' => 'media',
             'url' => true,
             'required' => array('wp_estate_header_type_taxonomy', '=', '1'),
             'title' => __('Taxonomy Hero Media Header Type - Static Image', 'wpresidence-core'),
             'subtitle' => __('If media header is set to image, and no image is added we will use the taxonomy featured image', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header_type_blog_post',
             'type' => 'button_set',
             'title' => __('Hero Media Header Type for Blog Posts', 'wpresidence-core'),
             'subtitle' => __('Select what media header to use for blog posts.', 'wpresidence-core'),
             'options' => array(
                 'none',
                 'image',
                 'theme slider',
                 'revolution slider',
                 'maps'
             ),
             'default' => 4,
         ),
         array(
             'id' => 'wp_estate_header_blog_post_revolution_slider',
             'type' => 'text',
             'required' => array('wp_estate_header_type_blog_post', '=', '3'),
             'title' => __('Blog Post Hero Media Header Type -  Revolution Slider', 'wpresidence-core'),
             'subtitle' => __('If media header is set to Revolution Slider, type the slider name and save.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header_blog_post_image',
             'type' => 'media',
             'url' => true,
             'required' => array('wp_estate_header_type_blog_post', '=', '1'),
             'title' => __('Blog Post Hero Media Header Type - Static Image', 'wpresidence-core'),
             'subtitle' => __('Blog Post header image', 'wpresidence-core'),
         ),
 
         array(
             'id' => 'wp_estate_paralax_header',
             'type' => 'button_set',
             'title' => __('Parallax efect for image/video hero header media?', 'wpresidence-core'),
             'subtitle' => __('Enable parallax efect for image/video hero media header globally.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'no',
         ),
 
         array(
             'id' => 'wp_estate_location_animation',
             'type' => 'button_set',
             'title' => __('Change text over header with user location on home?', 'wpresidence-core'),
             'subtitle' => __('Works only for Hero Header Image and only for homepage', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_location_animation_text',
             'type' => 'text',
             'title' => __('Heading with user location', 'wpresidence-core'),
             'subtitle' => __('%city% will be replaced by user city', 'wpresidence-core'),
             'default' => 'Find a home in %city%',
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Hero Media - Theme Slider', 'wpresidence-core'),
     'id' => 'theme_slider_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_theme_slider',
             'type' => 'select',
             'multi' => true,
             'data' => 'posts',
             'args' => array(
                 'post_type' => 'estate_property',
                 'post_status' => 'publish',
                 'posts_per_page' => 50,
             ),
             'title' => __('Select Properties ', 'wpresidence-core'),
             'subtitle' => __('Select properties for hero header theme slider - *hold CTRL for multiple select
 Due to speed reason we only show here the first 50 listings. If you want to add other listings into the theme slider please go and edit the property (in wordpress admin) and select "Property in theme Slider" in Property Details tab.', 'wpresidence-core'),
         //'options'  => wpresidence_return_theme_slider_list(),
         ),
         array(
             'id' => 'wp_estate_theme_slider_manual',
             'type' => 'text',
             'title' => __('Add Properties in theme slider by ID. Place here the Listings Id separated by comma.', 'wpresidence-core'),
             'subtitle' => __('Place here the Listings Id separated by comma. Will Overwrite the above selection!', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_slider_cycle',
             'type' => 'text',
             'title' => __('Number of milisecons before auto cycling an item', 'wpresidence-core'),
             'subtitle' => __('Number of milisecons before auto cycling an item (5000=5sec).Put 0 if you don\'t want to autoslide.', 'wpresidence-core'),
             'default' => '5000'
         ),
         array(
             'id' => 'wp_estate_theme_slider_type',
             'type' => 'button_set',
             'title' => __('Design Type?', 'wpresidence-core'),
             'subtitle' => __('Select the design type.', 'wpresidence-core'),
             'options' => array(
                 'type1' => 'type1',
                 'type2' => 'type2',
                 'type3' => 'type3'
             ),
             'default' => 'type1',
         ),
         array(
             'id' => 'wp_estate_theme_slider_height',
             'type' => 'text',
             'title' => __('Height in px (put 0 for full screen)', 'wpresidence-core'),
             'subtitle' => __('Height in px(put 0 for full screen, Default :580px)', 'wpresidence-core'),
             'default' => '580'
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Mobile Menu', 'wpresidence-core'),
     'id' => 'mobile_menu_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_mobile_sticky_header',
             'type' => 'button_set',
             'title' => __('Use Sticky mobile header?', 'wpresidence-core'),
             'subtitle' => __('If yes the top bar will be hidden on moble devices', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'no',
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Header Colors', 'wpresidence-core'),
     'id' => 'mainmenu_design_elements_tab',
     'subsection' => true,
     'fields' => array(
       
         array(
             'id' => 'wp_estate_header_color',
             'type' => 'color',
             'title' => __('Header Background Color', 'wpresidence-core'),
             'subtitle' => __('Applies for standard & sticky menus background color.', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_menu_font_color',
             'type' => 'color',
             'title' => __('Header Font Color', 'wpresidence-core'),
             'subtitle' => __('Text Menu Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_sticky_menu_font_color',
             'type' => 'color',
             'title' => __('Sticky Menu Font Color', 'wpresidence-core'),
             'subtitle' => __('Sticky Menu Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_top_menu_hover_font_color',
             'type' => 'color',
             'title' => __('Header Hover Font Color', 'wpresidence-core'),
             'subtitle' => __('Header Hover Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_active_menu_font_color',
             'type' => 'color',
             'title' => __('Active Menu Font Color', 'wpresidence-core'),
             'subtitle' => __('Active Menu Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_top_menu_hover_back_font_color',
             'type' => 'color',
             'title' => __('Header Hover Background Color', 'wpresidence-core'),
             'subtitle' => __('Top Menu Hover Background Color (*applies only for some hover types)', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'opt-info_hover_type',
             'type' => 'raw',
             'title' => '',
             'full_width' => true,
             'content' => '<img src="' . plugins_url('/img/menu_types.png', dirname(__FILE__)) . '" alt="Menu Types" />'
         ),
         array(
             'id' => 'wp_estate_top_menu_hover_type',
             'type' => 'button_set',
             'title' => __('Top Menu Hover Type', 'wpresidence-core'),
             'subtitle' => __('For Hover Type 1, 2, 5, 6 - setup Top Menu Hover Font Color option', 'wpresidence-core') . '</br>' . __('For Hover Type 3, 4 - setup Top Menu Hover Background Color option', 'wpresidence-core'),
             'options' => array(
                 '1' => '1',
                 '2' => '2',
                 '3' => '3',
                 '4' => '4',
                 '5' => '5',
                 '6' => '6'),
             'default' => '1',
         ),
             
         array(
             'id' => 'wp_estate_transparent_menu_font_color',
             'type' => 'color',
             'title' => __('Transparent Menu Font Color', 'wpresidence-core'),
             'subtitle' => __('Transparent Menu Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_transparent_menu_hover_font_color',
             'type' => 'color',
             'title' => __('Transparent Menu Hover Font Color', 'wpresidence-core'),
             'subtitle' => __('Transparent Menu Hover Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_menu_item_back_color',
             'type' => 'color',
             'title' => __('Dropdown Menu Background Color', 'wpresidence-core'),
             'subtitle' => __('Dropdown Menu Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_menu_items_color',
             'type' => 'color',
             'title' => __('Dropdown Menu Item Font Color', 'wpresidence-core'),
             'subtitle' => __('Text color for dropdown menu item', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_menu_hover_font_color',
             'type' => 'color',
             'title' => __('Dropdown Menu Item Hover Font Color', 'wpresidence-core'),
             'subtitle' => __('Hover text color for dropdown menu item', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_menu_hover_back_color',
             'type' => 'color',
             'title' => __('Dropdown Menu Item Hover Background Color', 'wpresidence-core'),
             'subtitle' => __('Hover background color for dropdown menu item', 'wpresidence-core'),
             'transparent' => false,
         ),
 
 
 
         array(
             'id' => 'wp_estate_menu_border_color',
             'type' => 'color',
             'title' => __('Dropdown Menu Item Border Color', 'wpresidence-core'),
             'subtitle' => __('Dropdown menu item border bottom color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
        
         array(
             'id' => 'wp_estate_border_bottom_header_color',
             'type' => 'color',
             'title' => __('Color of the border below header. ', 'wpresidence-core'),
             'subtitle' => __('Add the border in Header Settings.', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_border_bottom_header_sticky_color',
             'type' => 'color',
             'title' => __('Color of the border below sticky header', 'wpresidence-core'),
             'subtitle' => __('Add the border in Header Settings.', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         
         array(
             'id' => 'wp_estate_header4_back_color',
             'type' => 'color',
             'title' => __('Header type 4 - background color for second row', 'wpresidence-core'),
             'subtitle' => __('Header type 4 - background color for second row.', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_header4_font_color',
             'type' => 'color',
             'title' => __('Header type 4 - font color for second row', 'wpresidence-core'),
             'subtitle' => __('Header type 4 - font color for second row.', 'wpresidence-core'),
             'transparent' => false,
         ),
 
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Top Bar Colors', 'wpresidence-core'),
     'id' => 'topbar_design_elements_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_top_bar_back',
             'type' => 'color',
             'title' => __('Top Bar Background Color (Header Widget Area)', 'wpresidence-core'),
             'subtitle' => __('Applies for the top bar area when enabled', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_top_bar_font',
             'type' => 'color',
             'title' => __('Top Bar Font Color (Header Widget Area)', 'wpresidence-core'),
             'subtitle' => __('Applies for header widgets added to the top bar area', 'wpresidence-core'),
             'transparent' => false,
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Mobile Menu Colors', 'wpresidence-core'),
     'id' => 'mobile_design_elements_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_mobile_header_background_color',
             'type' => 'color',
             'title' => __('Mobile header background color', 'wpresidence-core'),
             'subtitle' => __('Mobile header background color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_mobile_header_icon_color',
             'type' => 'color',
             'title' => __('Mobile header icon color', 'wpresidence-core'),
             'subtitle' => __('Mobile header icon color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_mobile_menu_font_color',
             'type' => 'color',
             'title' => __('Mobile menu font color', 'wpresidence-core'),
             'subtitle' => __('Mobile menu font color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_mobile_menu_hover_font_color',
             'type' => 'color',
             'title' => __('Mobile menu hover font color', 'wpresidence-core'),
             'subtitle' => __('Mobile menu hover font color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_mobile_item_hover_back_color',
             'type' => 'color',
             'title' => __('Mobile menu item hover background color', 'wpresidence-core'),
             'subtitle' => __('Mobile menu item hover background color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_mobile_menu_backgound_color',
             'type' => 'color',
             'title' => __('Mobile menu background color', 'wpresidence-core'),
             'subtitle' => __('Mobile menu background color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_mobile_menu_border_color',
             'type' => 'color',
             'title' => __('Mobile menu item border color', 'wpresidence-core'),
             'subtitle' => __('Mobile menu item border color', 'wpresidence-core'),
             'transparent' => false,
         ),
     ),
 ));
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Footer', 'wpresidence-core'),
     'id' => 'footers_settings_sidebar',
     'icon' => 'el el-screen'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Footer Elements ', 'wpresidence-core'),
     'id' => 'footer_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_footer',
             'type' => 'button_set',
             'title' => __('Show Footer?', 'wpresidence-core'),
             'subtitle' => __('Show Footer?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_show_footer_copy',
             'type' => 'button_set',
             'title' => __('Show Footer Copyright Area?', 'wpresidence-core'),
             'subtitle' => __('Show Footer Copyright Area?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_show_back_to_top',
             'type' => 'button_set',
             'title' => __('Show Back to Top Button ?', 'wpresidence-core'),
             'subtitle' => __('Show Back to Top Button ?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'yes',
         ),
         
         array(
             'id' => 'wp_estate_show_footer_contact',
             'type' => 'button_set',
             'title' => __('Show Footer Contact button?', 'wpresidence-core'),
             'subtitle' => __('Show Footer Contact button?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'yes',
         ),
         
         
         
         array(
             'id' => 'wp_estate_copyright_message',
             'type' => 'textarea',
             'required' => array('wp_estate_show_footer_copy', '=', 'yes'),
             'title' => __('Copyright Message', 'wpresidence-core'),
             'subtitle' => __('Type here the copyright message that will appear in footer. Add only text.', 'wpresidence-core'),
             'default' => 'Copyright All Rights Reserved 2019',
         ),
         
         array(
             'id' => 'wp_estate_footer_background',
             'type' => 'media',
             'url' => true,
             'title' => __('Background for Footer', 'wpresidence-core'),
             'subtitle' => __('Insert background footer image.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_repeat_footer_back',
             'type' => 'button_set',
             'title' => __('Repeat Footer background ?', 'wpresidence-core'),
             'subtitle' => __('Set repeat options for background footer image.', 'wpresidence-core'),
             'options' => array(
                 'repeat' => 'repeat',
                 'repeat x' => 'repeat x',
                 'repeat y' => 'repeat y',
                 'no repeat' => 'no repeat'
             ),
             'default' => 'repeat',
         ),
         array(
             'id' => 'wp_estate_wide_footer',
             'type' => 'button_set',
             'title' => __('Wide Footer?', 'wpresidence-core'),
             'subtitle' => __('Makes the footer width 100%.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_footer_type',
             'type' => 'button_set',
             'title' => __('Footer Type', 'wpresidence-core'),
             'subtitle' => __('Footer Type', 'wpresidence-core'),
             'options' => array(
                 '1' => __('4 equal columns', 'wpresidence-core'),
                 '2' => __('3 equal columns', 'wpresidence-core'),
                 '3' => __('2 equal columns', 'wpresidence-core'),
                 '4' => __('100% width column', 'wpresidence-core'),
                 '5' => __('3 columns: 1/2 + 1/4 + 1/4', 'wpresidence-core'),
                 '6' => __('3 columns: 1/4 + 1/2 + 1/4', 'wpresidence-core'),
                 '7' => __('3 columns: 1/4 + 1/4 + 1/2', 'wpresidence-core'),
                 '8' => __('2 columns: 2/3 + 1/3', 'wpresidence-core'),
                 '9' => __('2 columns: 1/3 + 2/3', 'wpresidence-core'),
             ),
             'default' => '1',
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Footer Layout', 'wpresidence-core'),
     'id' => 'footer_style_tab',
     'subsection' => true,
     'fields' => array(
 
         array(
             'id' => 'wp_estate_wide_footer',
             'type' => 'button_set',
             'title' => __('Wide Footer?', 'wpresidence-core'),
             'subtitle' => __('Makes the footer width 100%.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
         array(
             'id' => 'wp_estate_footer_type',
             'type' => 'button_set',
             'title' => __('Footer Type', 'wpresidence-core'),
             'subtitle' => __('Footer Type', 'wpresidence-core'),
             'options' => array(
                 '1' => __('4 equal columns', 'wpresidence-core'),
                 '2' => __('3 equal columns', 'wpresidence-core'),
                 '3' => __('2 equal columns', 'wpresidence-core'),
                 '4' => __('100% width column', 'wpresidence-core'),
                 '5' => __('3 columns: 1/2 + 1/4 + 1/4', 'wpresidence-core'),
                 '6' => __('3 columns: 1/4 + 1/2 + 1/4', 'wpresidence-core'),
                 '7' => __('3 columns: 1/4 + 1/4 + 1/2', 'wpresidence-core'),
                 '8' => __('2 columns: 2/3 + 1/3', 'wpresidence-core'),
                 '9' => __('2 columns: 1/3 + 2/3', 'wpresidence-core'),
             ),
             'default' => '1',
         ),
        
 
         array(
             'id' => 'wp_estate_footer_background',
             'type' => 'media',
             'url' => true,
             'title' => __('Background for Footer', 'wpresidence-core'),
             'subtitle' => __('Insert background footer image.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_repeat_footer_back',
             'type' => 'button_set',
             'title' => __('Repeat Footer background?', 'wpresidence-core'),
             'subtitle' => __('Set repeat options for background footer image.', 'wpresidence-core'),
             'options' => array(
                 'repeat' => 'repeat',
                 'repeat x' => 'repeat x',
                 'repeat y' => 'repeat y',
                 'no repeat' => 'no repeat'
             ),
             'default' => 'repeat',
         ),

         

 
         array(
             'id' => 'wp_estate_show_sticky_footer',
             'type' => 'button_set',
             'title' => __('Use Sticky Footer?', 'wpresidence-core'),
             'subtitle' => __('"Show Footer when using Half Map Layout" option cannot work if "Enable Sticky Footer" is also set to Yes.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Footer Colors', 'wpresidence-core'),
     'id' => 'footer_colors_tab',
     'desc' => __('***Please understand that we cannot add here color controls for all theme elements & details. Doing that will result in a overcrowded and useless interface. These small details need to be addressed via custom css code', 'wpresidence-core'),
     'subsection' => true,
     'fields' => array(
      
         array(
             'id' => 'wp_estate_footer_back_color',
             'type' => 'color',
             'title' => __('Footer Background Color', 'wpresidence-core'),
             'subtitle' => __('Footer Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_footer_font_color',
             'type' => 'color',
             'title' => __('Footer Font Color', 'wpresidence-core'),
             'subtitle' => __('Footer Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_footer_heading_color',
             'type' => 'color',
             'title' => __('Footer Heading Font Color', 'wpresidence-core'),
             'subtitle' => __('Footer Heading Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_footer_copy_color',
             'type' => 'color',
             'title' => __('Footer Copyright Font Color', 'wpresidence-core'),
             'subtitle' => __('Footer Copyright Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_footer_copy_back_color',
             'type' => 'color',
             'title' => __('Footer Copyright Area Background Font Color', 'wpresidence-core'),
             'subtitle' => __('Footer Copyright Area Background Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
       
         array(
             'id' => 'wp_estate_footer_social_widget_back_color',
             'type' => 'color',
             'title' => __('Footer Social Widget Icon Background Color', 'wpresidence-core'),
             'subtitle' => __('Footer Social Widget Icon Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         
         array(
             'id' => 'wp_estate_footer_social_widget_color',
             'type' => 'color',
             'title' => __('Footer Social Widget Icon Color', 'wpresidence-core'),
             'subtitle' => __('Footer Social Widget Icon Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         
     ),
 ));
 
 /* disabled
 if (function_exists('is_plugin_active')):
     if (is_plugin_active('residence-studio/wpresidence-elementor-design-studio.php')) {
     // -> START Header Footer options
     Redux::setSection($opt_name, array(
         'title' => __('Headers & Footers with Elementor', 'wpresidence-core'),
         'id' => 'head_foot_settings_sidebar',
         'icon' => 'el el-website'
     ));
     Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_header_footer/' );
     Redux::setSection($opt_name, array(
         'title' => __('Add / Edit Headers and Footers', 'wpresidence-core'),
         'id' => 'head_foot_settings_tab',
         'subsection' => true,
         'fields' => array(
             array(
                 'id' => 'wp_estate_header_footer_item',
                 'type' => 'wpestate_header_footer',
                 'title' => __('Header and Footer Templates', 'wpresidence-core'),
                 'subtitle' => __('Create Headers and Footers with Elementor and Theme Widgets. The templates can also be managed / deleted via WpEstate Studio Templates menu. <a href="https://help.wpresidence.net/article/how-to-create-custom-header-footer-with-header-footer-builder/" target="_blank">Check this help with more details.</a>
 
 ', 'wpresidence-core'),
               'class' => 'class_head_foot_settings_tab',
             ),
 
 
         ),
     ));
 }
 endif;
 
 */


 // -> START Map options
 Redux::setSection($opt_name, array(
     'title' => __('Maps', 'wpresidence-core'),
     'id' => 'map_settings_sidebar',
     'icon' => 'el el-map-marker'
 ));
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Maps General Settings', 'wpresidence-core'),
     'id' => 'general_map_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_kind_of_map',
             'type' => 'button_set',
             'title' => __('What Maps System do you want to use?', 'wpresidence-core'),
             'subtitle' => __('Google Maps requires an API KEY. Open Street can be used without a key.', 'wpresidence-core'),
             'options' => array(
                 2 => 'open street maps',
                 1 => 'google maps'
             ),
             'default' => 1,
         ),
 
 //            array(
 //                'id'       => 'wp_estate_ssl_map',
 //                'type'     => 'button_set',
 //                'title'    => __( 'Use Google maps with SSL ?', 'wpresidence-core' ),
 //                'subtitle' => __( 'Set to Yes if you use SSL.', 'wpresidence-core' ),
 //                'options'  => array(
 //                            'yes' => 'yes',
 //                            'no'  => 'no'
 //                            ),
 //                'default'  => 'no',
 //            ),
 //
         array(
             'id' => 'wp_estate_api_key',
             'type' => 'text',
             'title' => __('Google Maps API KEY', 'wpresidence-core'),
             'subtitle' => __('The Google Maps JavaScript API v3 REQUIRES an API key to work. Login in your Google Account, and follow Google Instructions to get the API key from this url: https://developers.google.com/maps/documentation/javascript/tutorial#api_key', 'wpresidence-core') . '<br>' . __('Help: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/google-api-key/">http://help.wpresidence.net/article/google-api-key/<a/>',
         ),
 //             array(
 //                'id'       => 'wp_estate_reverse_geolocation',
 //                'type'     => 'button_set',
 //                'title'    => __( 'What system do you want to use for geolocation?', 'wpresidence-core' ),
 //                'subtitle' => __( 'Your option is considered if you activate the option "Enable Autocomplete in Front End Submission Form" from Membership & submit Settings. Google Places is a paid system while Open Street is free. ', 'wpresidence-core' ),
 //                'options'  => array(
 //                            '1' => 'google places',
 //                            '2'  => 'open street'
 //                            ),
 //                'default'  => '1',
 //            ),
         array(
             'id' => 'wp_estate_mapbox_api_key',
             'type' => 'text',
             'title' => __('MapBox API KEY - used for tiles (maps images) server when Open Street Maps is enabled', 'wpresidence-core'),
             'subtitle' => __('You can get the key from https://www.mapbox.com/. If blank, it uses the default OpenStreet Maps Server which can be slow', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_general_latitude',
             'type' => 'text',
             'title' => __('Starting Point Latitude', 'wpresidence-core'),
             'subtitle' => __('Applies when Maps are enabled in Header - Hero Media. Add only numbers (ex: 40.577906).', 'wpresidence-core'),
             'default' => '40.781711'
         ),
         array(
             'id' => 'wp_estate_general_longitude',
             'type' => 'text',
             'title' => __('Starting Point Longitude', 'wpresidence-core'),
             'subtitle' => __('Applies when Maps are enabled in Header - Hero Media. Add only numbers (ex: -74.155058).', 'wpresidence-core'),
             'default' => '-73.955927'
         ),
         array(
             'id' => 'wp_estate_default_map_zoom',
             'type' => 'text',
             'title' => __('Default Maps zoom (1 to 20)', 'wpresidence-core'),
             'subtitle' => __('Applies when Maps are enabled in Header - Hero Media. Exceptions: advanced search results, properties list and taxonomies pages.', 'wpresidence-core'),
             'default' => '15'
         ),
 
         array(
             'id' => 'wp_estate_pin_cluster',
             'type' => 'button_set',
             'title' => __('Use the Pin Cluster on the maps', 'wpresidence-core'),
             'subtitle' => __('If yes, it groups nearby pins in a cluster.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_zoom_cluster',
             'type' => 'text',
             'required' => array('wp_estate_pin_cluster', '=', 'yes'),
             'title' => __('Maximum zoom level for Cloud Cluster to appear', 'wpresidence-core'),
             'subtitle' => __('Pin cluster disappears when maps zoom is less than the value set in here.', 'wpresidence-core'),
             'default' => '10'
         ),
         array(
             'id' => 'wp_estate_geolocation_radius',
             'type' => 'text',
             'title' => __('Geolocation Circle over maps (in meters)', 'wpresidence-core'),
             'subtitle' => __('Controls circle radius value for user geolocation pin. Type only numbers (ex: 400).', 'wpresidence-core'),
             'default' => '1000'
         ),
 
         array(
             'id' => 'wp_estate_min_height',
             'type' => 'text',
             'title' => __('Header Height when maps appear in "closed" view', 'wpresidence-core'),
             'subtitle' => __('Applies when Maps are enabled in Header - Hero Media.', 'wpresidence-core'),
             'default' => '300'
         ),
         array(
             'id' => 'wp_estate_max_height',
             'type' => 'text',
             'title' => __('Header Height when maps appear in "open" view', 'wpresidence-core'),
             'subtitle' => __('Applies when Maps are enabled in Header - Hero Media.', 'wpresidence-core'),
             'default' => '450'
         ),
         array(
             'id' => 'wp_estate_keep_min',
             'type' => 'button_set',
             'title' => __('Always show Maps in "closed" view?', 'wpresidence-core'),
             'subtitle' => __('Applies when Maps are enabled in Header - Hero Media. Exception: property page.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_keep_max',
             'type' => 'button_set',
             'title' => __('Force Maps height to be full screen size?', 'wpresidence-core'),
             'subtitle' => __('Applies when Maps are enabled in Header - Hero Media. Exception: property page.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
     ),
 ));
 
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Google Maps Extra Settings', 'wpresidence-core'),
     'id' => 'google_map_tab',
     'subsection' => true,
     'fields' => array(
 
         array(
             'id' => 'wp_estate_default_map_type',
             'type' => 'button_set',
             'title' => __('Default Google Maps View', 'wpresidence-core'),
             'subtitle' => __('The type selected applies only for Google Maps.', 'wpresidence-core'),
             'options' => array(
                 'SATELLITE' => 'SATELLITE',
                 'HYBRID' => 'HYBRID',
                 'TERRAIN' => 'TERRAIN',
                 'ROADMAP' => 'ROADMAP'
             ),
             'default' => 'ROADMAP',
         ),
         array(
             'id' => 'wp_estate_cache',
             'type' => 'button_set',
             'title' => __('Use Cache for Google maps? (*cache will renew itself every 3h)', 'wpresidence-core'),
             'subtitle' => __('If set to yes, new property pins will update on the map every 3 hours.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 
                 array(
             'id' => 'wp_estate_show_g_search',
             'type' => 'button_set',
             'title' => __('Show Google Maps Search over Map?', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the Google Maps search bar.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_map_style',
             'type' => 'textarea',
             'title' => __('Style for Google Maps. Use <strong> https://snazzymaps.com/ </strong> to create styles', 'wpresidence-core'),
             'subtitle' => __('Copy/paste below the custom map style code.', 'wpresidence-core'),
             'full_width' => true,
         ),
     ),
 ));
 
 
 $pin_fields = array();
 
 $pin_fields[] = array(
     'id' => 'wp_estate_use_price_pins',
     'type' => 'button_set',
     'title' => __('Use price Pins?', 'wpresidence-core'),
     'subtitle' => __('Use price Pins? (The css class for price pins is "wpestate_marker". Each pin receives a class with the name of the category or action: For example "wpestate_marker apartments sales")', 'wpresidence-core'),
     'options' => array(
         'yes' => 'yes',
         'no' => 'no'
     ),
     'default' => 'no',
 );
 
 $pin_fields[] = array(
     'id' => 'wp_estate_use_price_pins_full_price',
     'type' => 'button_set',
     'title' => __('Use Full Price Pins?', 'wpresidence-core'),
     'subtitle' => __('If not we will show prices without before and after label and in this format : 5,23m or 6.83k', 'wpresidence-core'),
     'options' => array(
         'yes' => 'yes',
         'no' => 'no'
     ),
     'default' => 'no',
 );
 
 $pin_fields[] = array(
     'id' => 'wp_estate_use_single_image_pin',
     'type' => 'button_set',
     'title' => __('Use single Image Pin?', 'wpresidence-core'),
     'subtitle' => __('We will use 1 single pin for all markers. This option will decrease the loading time on you maps.', 'wpresidence-core'),
     'options' => array(
         'yes' => 'yes',
         'no' => 'no'
     ),
     'default' => 'no',
 );
 
 $pin_fields[] = array(
     'id' => 'wp_estate_single_pin',
     'type' => 'media',
     'title' => __('Single Pin Marker / Contact marker / Agency or Developer marker image', 'wpresidence-core'),
     'subtitle' => __('Image size must be 44px x 50px.', 'wpresidence-core'),
 );
 
 $pin_fields[] = array(
     'id' => 'wp_estate_cloud_pin',
     'type' => 'media',
     'title' => __('Pin Cluster - Cloud Marker Image', 'wpresidence-core'),
     'subtitle' => __('Image must be 70px x 70px', 'wpresidence-core'),
 );
 
 $pin_fields = wpresidence_core_add_pins_icons($pin_fields);
 
 Redux::setSection($opt_name, array(
     'title' => __('Maps Pins Management', 'wpresidence-core'),
     'id' => 'pin_management_tab',
     'class' => 'wpresidence-core_pin_fields',
     'desc' => __('Add new pins for single actions / single categories.'
             . '</br>For speed reason, you MUST add pins if you change categories and actions names. '
             . '</br>The Pins retina version must be uploaded at the same time (same folder) as the original pin, and with the same name and an additional _2x at the end. Help here: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/wpresidence-options-pin-management/" target="_blank">http://help.wpresidence.net/article/wpresidence-options-pin-management/</a>',
     'subsection' => true,
     'fields' => $pin_fields,
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Number of Maps Pins Settings', 'wpresidence-core'),
     'id' => 'maps_pins_number',
     'subsection' => true,
     'fields' => array(
 
 
         array(
             'id' => 'wp_estate_readsys',
             'type' => 'button_set',
             'title' => __('Use file reading for maps pins?', 'wpresidence-core'),
             'subtitle' => __('Use file reading for maps pins? (*recommended for over 200 listings. Read the manual for differences between file and mysql reading)', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_map_max_pins',
             'type' => 'text',
             'title' => __('Maximum number of pins to show on the map.', 'wpresidence-core'),
             'subtitle' => __('A high number will increase the response time and server load. Use a number that works for your current hosting situation. Put -1 for all pins.', 'wpresidence-core'),
             'default' => '100'
         ),
     ),
 ));

 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_generate_pins/' );
 Redux::setSection($opt_name, array(
     'title' => __('Generate Data & Maps Pins', 'wpresidence-core'),
     'id' => 'generare_pins_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_generate_pins',
             'type' => 'wpestate_generate_pins',
             'full_width' => true,
             'title' => __('Generates Maps Pins and Location field Autocomplete data', 'wpresidence-core'),
         //'subtitle' => __( 'Click "Save Changes" to generate file with map pins for the read from file map option set to YES.', 'wpresidence-core' ),
         ),
     ),
 ));
 
 // -> START Property page
 Redux::setSection($opt_name, array(
     'title' => __('Property Page', 'wpresidence-core'),
     'id' => 'property_page_settings_sidebar',
     'icon' => 'el el-file'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Property Page Design', 'wpresidence-core'),
     'id' => 'property_page_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_property_layouts',
             'type' => 'image_select',
             'title' => esc_html__('Property Page Layout', 'wpresidence-core'),
             'subtitle' => __('Pick your Property Page Layout', 'wpresidence-core'),
             'options' => array(
                 1 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-layouts/layout-1.png'
                 ),
                 2 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-layouts/layout-2.png'
                 ),
                 3 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-layouts/layout-3.png'
                 ),
                 4 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-layouts/layout-4.png'
                 ),
                 5 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-layouts/layout-5.png'
                 ),
                 6 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-layouts/layout-6.png'
                 ),
                 7 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-layouts/layout-7.png'
                 ),
             ),
             'default' => 1,
         ),
         array(
             'id' => 'wp_estate_col_layout',
             'required' => array('wp_estate_property_layouts', '>', 5),
             'type' => 'button_set',
             'title' => __('Content Columns width ', 'wpresidence-core'),
             'subtitle' => __('Layouts 6 & 7 have two content columns and no sidebar. Choose the columns width here. Manage the sections for each column in "Selection for Accordion - Layouts 6 & 7"', 'wpresidence-core'),
             'options' => array(
                 6 => ' 50% / 50%',
                 8 => '66% / 33%',
                 4 => '33% / 66%',
                 9 => '75% / 25%',
                 3 => '25% / 75%',
             ),
             'default' => 6,
         ),
         /*
          * Media Options
          */
         array(
             'id' => 'wp_estate_header_type_property_page',
             'type' => 'button_set',
             'title' => __('Hero Media Header for Property Page', 'wpresidence-core'),
             'subtitle' => __('Hero Media Header is the first section below header. The option you select will be visible on all property pages.', 'wpresidence-core'),
             'options' => array(
                 'none',
                 'image',
                 'theme slider',
                 'revolution slider',
                 'maps'
             ),
             'default' => 4,
         ),
         array(
             'id' => 'wp_estate_header_property_page_revolution_slider',
             'type' => 'text',
             'required' => array('wp_estate_header_type_property_page', '=', '3'),
             'title' => __('Property Hero Media Header -  Revolution Slider', 'wpresidence-core'),
             'subtitle' => __('If media header is set to Revolution Slider, type the slider name and save.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_header_property_page_image',
             'type' => 'media',
             'url' => true,
             'required' => array('wp_estate_header_type_property_page', '=', '1'),
             'title' => __('Property Hero Media Header - Static Image', 'wpresidence-core'),
             'subtitle' => __('If media header is set to image, and no image is added we will use the taxonomy featured image', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_global_prpg_slider_type',
             'type' => 'button_set',
             'title' => __('"Media Section" Type (property images & video) ', 'wpresidence-core'),
             'subtitle' => __('Choose how to display the listing images or video', 'wpresidence-core'),
             'options' => array(
                 'classic' => 'classic slider',
                 'vertical' => 'vertical slider',
                 'horizontal' => 'horizontal slider',
                 'full width header' => 'slider v4 ',
                 'multi image slider' => 'multi image slider',
                 'gallery' => 'masonry gallery v1',
                 'header masonry gallery' => 'masonry gallery v2',
             ),
             'default' => 'horizontal',
         ),
         array(
             'id' => 'wp_estate_media_buttons_order_items',
             'type' => 'sorter',
             'title' => __('Media Section Order - Buttons Selection', 'wpresidence-core'),
             'subtitle' => __('Select the media types buttons for media section', 'wpresidence-core'),
             'options' => array(
                 'enabled' => array(
                     'image' => esc_html__('Image Gallery', 'wpresidence-core'),
                     'map' => esc_html__('Map View', 'wpresidence-core'),
                     'street' => esc_html__('Street View', 'wpresidence-core'),
                     'video' => esc_html__('Video', 'wpresidence-core'),
                     'virtual_tour' => esc_html__('Virtual Tour', 'wpresidence-core'),
                 ),
                 'disabled' => array(
                 )
             ),
         ),
         /*
          * Content Options
          */
         array(
             'id' => 'wp_estate_global_prpg_content_type',
             'type' => 'button_set',
             'title' => __('Show Content as', 'wpresidence-core'),
             'subtitle' => __('Tabs does not apply to Property Layouts 6 & 7. Select the sections you wish to show in each layout from "Selection for Accordion Layout" or "Selection for Tab Layout"', 'wpresidence-core'),
             'options' => array(
                 'accordion' => 'accordion',
                 'tabs' => 'tabs'
             ),
             'default' => 'accordion',
         ),
         array(
             'id' => 'wp_estate_show_property_sticky_top_bar',
             'type' => 'button_set',
             'required' => array('wp_estate_global_prpg_content_type', '=', 'accordion'),
             'title' => __('Property Navigation Sticky Bar (works only for accordion content)', 'wpresidence-core'),
             'subtitle' => __('Property Navigation Sticky Bar (works only for accordion content)', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_details_colum',
             'type' => 'button_set',
             'title' => __('Show property details on 1,2 or 3 columns', 'wpresidence-core'),
             'subtitle' => __('Property details will display on 1, 2 or 3 columns', 'wpresidence-core'),
             'options' => array(
                 '12' => '1 Column',
                 '6' => '2 Columns',
                 '4' => '3 Columns',
             ),
             'default' => '4',
         ),
         array(
             'id' => 'wp_estate_address_column',
             'type' => 'button_set',
             'title' => __('Show property address on 1,2 or 3 columns', 'wpresidence-core'),
             'subtitle' => __('Address will display on 1,2 or 3 columns', 'wpresidence-core'),
             'options' => array(
                 '12' => '1 Column',
                 '6' => '2 Columns',
                 '4' => '3 Columns',
             ),
             'default' => '4',
         ),
         array(
             'id' => 'wp_estate_features_colum',
             'type' => 'button_set',
             'title' => __('Show property Features & Amenities on 1,2 or 3 columns', 'wpresidence-core'),
             'subtitle' => __('Features & Amenities will display on 1, 2 or 3 columns', 'wpresidence-core'),
             'options' => array(
                 '12' => '1 Column',
                 '6' => '2 Columns',
                 '4' => '3 Columns',
             ),
             'default' => '4',
         ),
         /*
          * SIdebar Options
          */
         array(
             'id' => 'wp_estate_property_sidebar',
             'type' => 'button_set',
             'title' => __('Property - Select Sidebar Position', 'wpresidence-core'),
             'subtitle' => __('Where to show the sidebar in property page.', 'wpresidence-core'),
             'options' => array(
                 'no sidebar' => __('no sidebar', 'wpresidence-core'),
                 'right' => __('right', 'wpresidence-core'),
                 'left' => __('left', 'wpresidence-core')
             ),
             'default' => 'right',
         ),
         array(
             'id' => 'wp_estate_property_sidebar_sitcky',
             'type' => 'button_set',
             'title' => __('Use Sticky Sidebar on Property page', 'wpresidence-core'),
             'subtitle' => __('*Sticky sidebar will go over sticky footer, if enabled.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_property_sidebar_name',
             'type' => 'select',
             'title' => __('Property page - Select what Sidebar', 'wpresidence-core'),
             'subtitle' => __('Select which sidebar to show in property page. Create new Sidebars from Appearance -> Sidebars', 'wpresidence-core'),
             'data' => 'sidebars',
             'default' => 'primary-widget-area'
         ),
         array(
             'id' => 'wp_estate_show_prev_next',
             'type' => 'button_set',
             'title' => __('Show Prev/Next butons on property page ?', 'wpresidence-core'),
             'subtitle' => __('This option will show or hide the prev/next buttons on property page', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Custom Template (for Advanced Users)', 'wpresidence-core'),
     'desc' => __('Custom templates have evolved into a more advanced feature called Design Studio. This new functionality and all of your templates can be found here:', 'wpresidence-core') 
    . ' <a href="' . admin_url( 'edit.php?post_type=wpestate-studio' ) . '" target="_blank">Design Studio</a>',

     
     'id' => 'property_page_custom_templates_sec_tab',
     'subsection' => true,
     'fields' => array(
       /*  array(
             'id' => 'wp_estate_global_property_page_template',
             'type' => 'select',
             'title' => __('Use a custom property page template', 'wpresidence-core'),
             'subtitle' => __('Pick a custom property page template you made.', 'wpresidence-core'),
             'options' => wpestate_property_page_template_function()
         ),
         array(
             'id' => 'wpestate_wide_elememtor_page',
             'type' => 'button_set',
             'title' => __('Custom template should be full width ?', 'wpresidence-core'),
             'subtitle' => __('Custom template should be full width ?', 'wpresidence-core'),
             'options' => array(
                 'yes'=>'yes',
                 'no'=>'no',
             ),
             'default' =>'no',
         ),
         array(
             'id' => 'wp_estate_elementor_id',
             'type' => 'text',
             'title' => __('Elementor Only -  ID for sample property', 'wpresidence-core'),
             'subtitle' => __('We will use this property data as sample info for Elementor Preview for all properties. If blank we will use the data from the last property published.', 'wpresidence-core'),
         ),
         */
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Selection for Accordion Layout', 'wpresidence-core'),
     'id' => 'property_page_simple_acc_sec_tab',
     'subsection' => true,

     'fields' => array(
         array(
             'id' => 'wp_estate_property_page_acc_order',
             'type' => 'sorter',
             'title' => 'Arrange Sections',
             'desc' => 'Drag and drop sections and organize your property page design.',
             'options' => array(
                'enabled' => array(
                    'overview' => esc_html__('Overview', 'wpresidence-core'),
                    'description' => esc_html__('Description', 'wpresidence-core'),
                    'documents' => esc_html__('Documents', 'wpresidence-core'),
                     'multi-units' => esc_html__('Multi Units', 'wpresidence-core'),
                     'energy-savings' => esc_html__('Energy Savings', 'wpresidence-core'),
                     'address' => esc_html__('Address', 'wpresidence-core'),
                     'listing_details' => esc_html__('Listing Details', 'wpresidence-core'),
                     'features' => esc_html__('Amenities and Features', 'wpresidence-core'),
                     'video' => esc_html__('Video', 'wpresidence-core'),
                     'map' => esc_html__('Map', 'wpresidence-core'),
                     'virtual_tour' => esc_html__('Virtual Tour', 'wpresidence-core'),
                     'walkscore' => esc_html__('WalkScore', 'wpresidence-core'),
                     'nearby' => esc_html__('What\'s Nearby', 'wpresidence-core'),
                     'payment_calculator' => esc_html__('Payment Calculator', 'wpresidence-core'),
                     'floor_plans' => esc_html__('Floor Plans', 'wpresidence-core'),
                     'page_views' => esc_html__('Page Views', 'wpresidence-core'),
                     'schedule_tour' => esc_html__('Schedule Tour', 'wpresidence-core'),
                     'agent_area' => esc_html__('Agent', 'wpresidence-core'),
                    'other_agents' => esc_html__('Other Agents', 'wpresidence-core'),
                    'reviews' => esc_html__('Reviews', 'wpresidence-core'),
                    'similar' => esc_html__('Similar Listings', 'wpresidence-core'),
                ),
                'disabled' => array(
                    'booking_shortcode' => esc_html__('Booking Shortcode', 'wpresidence-core'),
                )
            ),
            'save_always' => true,
        ),
 )));
 


 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_sortable/' );
 Redux::setSection($opt_name, array(
     'title' => __('Selection for Tab Layout', 'wpresidence-core'),
     'id' => 'property_page_simple_tab_sec_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_property_page_tab_order',
             'type' => 'wpestate_sortable',
             'title' => 'Arrange Sections',
             'desc' => 'Drag and drop sections and organize your property page design.',
             'options' => array(
                'enabled' => array(
                    'overview' => esc_html__('Overview', 'wpresidence-core'),
                    'description' => esc_html__('Description', 'wpresidence-core'),
                    'documents' => esc_html__('Documents', 'wpresidence-core'),
                     'multi-units' => esc_html__('Multi Units', 'wpresidence-core'),
                     'energy-savings' => esc_html__('Energy Savings', 'wpresidence-core'),
                     'address' => esc_html__('Address', 'wpresidence-core'),
                     'listing_details' => esc_html__('Listing Details', 'wpresidence-core'),
                     'features' => esc_html__('Amenities and Features', 'wpresidence-core'),
                     'video' => esc_html__('Video', 'wpresidence-core'),
                     'map' => esc_html__('Map', 'wpresidence-core'),
                     'virtual_tour' => esc_html__('Virtual Tour', 'wpresidence-core'),
                     'walkscore' => esc_html__('WalkScore', 'wpresidence-core'),
                     'nearby' => esc_html__('What\'s Nearby', 'wpresidence-core'),
                     'payment_calculator' => esc_html__('Payment Calculator', 'wpresidence-core'),
                     'floor_plans' => esc_html__('Floor Plans', 'wpresidence-core'),
                     'page_views' => esc_html__('Page Views', 'wpresidence-core'),
                     'schedule_tour' => esc_html__('Schedule Tour', 'wpresidence-core'),
                     'agent_area' => esc_html__('Agent', 'wpresidence-core'),
                    'other_agents' => esc_html__('Other Agents', 'wpresidence-core'),
                    'reviews' => esc_html__('Reviews', 'wpresidence-core'),
                    'similar' => esc_html__('Similar Listings', 'wpresidence-core'),
                ),
                'disabled' => array(
                    'booking_shortcode' => esc_html__('Booking Shortcode', 'wpresidence-core'),
                )
            ),
        ),
)));
  Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_sortable/' );
 Redux::setSection($opt_name, array(
     'title' => __('Selection for Accordion - Layouts 6 & 7', 'wpresidence-core'),
     'id' => 'property_page_simple_acc_lay6',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_property_page_acc_lay6_order',
             'type' => 'wpestate_sortable',
             'title' => 'Arrange Sections',
             'desc' => 'Drag and drop sections and organize your property page design.',
             'options' => array(
                'enabled' => array(
                    'overview' => esc_html__('Overview', 'wpresidence-core'),
                    'description' => esc_html__('Description', 'wpresidence-core'),
                    'documents' => esc_html__('Documents', 'wpresidence-core'),
                     'multi-units' => esc_html__('Multi Units', 'wpresidence-core'),
                     'energy-savings' => esc_html__('Energy Savings', 'wpresidence-core'),
                     'address' => esc_html__('Address', 'wpresidence-core'),
                     'listing_details' => esc_html__('Listing Details', 'wpresidence-core'),
                     'features' => esc_html__('Amenities and Features', 'wpresidence-core'),
                     'video' => esc_html__('Video', 'wpresidence-core'),
                     'map' => esc_html__('Map', 'wpresidence-core'),
                     'virtual_tour' => esc_html__('Virtual Tour', 'wpresidence-core'),
                     'walkscore' => esc_html__('WalkScore', 'wpresidence-core'),
                     'nearby' => esc_html__('What\'s Nearby', 'wpresidence-core'),
                     'payment_calculator' => esc_html__('Payment Calculator', 'wpresidence-core'),
                     'floor_plans' => esc_html__('Floor Plans', 'wpresidence-core'),
                     'page_views' => esc_html__('Page Views', 'wpresidence-core'),
                     'schedule_tour' => esc_html__('Schedule Tour', 'wpresidence-core'),
                     'agent_area' => esc_html__('Agent', 'wpresidence-core'),
                    'other_agents' => esc_html__('Other Agents', 'wpresidence-core'),
                    'reviews' => esc_html__('Reviews', 'wpresidence-core'),
                    'similar' => esc_html__('Similar Listings', 'wpresidence-core'),
                ),
                'disabled' => array(
                    'booking_shortcode' => esc_html__('Booking Shortcode', 'wpresidence-core'),
                )
            ),
        ),
)));
 
 Redux::setSection($opt_name, array(
     'title' => __('Overview section', 'wpresidence-core'),
     'id' => 'overview_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_overview_elements_design',
             'type' => 'button_set',
             'title' => __('Design Type for Overview elements', 'wpresidence-core'),
             'subtitle' => __('Design Type for Overview elements', 'wpresidence-core'),
             'options' => array(
                 'type1'=>'type 1',
                 'type2'=>'type 2',
                 'type3'=>'type 3',
                 'type4'=>'type 4',
             ),
             'default' =>'type1',
         ),
         
         array(
             'id' => 'wp_estate_show_overview_title',
             'type' => 'button_set',
             'title' => __('Show Overview Title', 'wpresidence-core'),
             'subtitle' => __('Show Overview Title - the text can be changed in listings label section', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         
         
         array(
             'id' => 'wp_estate_property_overview_order',
             'type' => 'sorter',
             'title' => 'Arrange Overview Details',
             'desc' => 'Drag and drop sections and organize your property overview section.',
             'options' => array(
                 'enabled' => array(
                     'updated_on' => esc_html__('Updated On', 'wpresidence-core'),
                     'bedrooms' => esc_html__('Bedrooms', 'wpresidence-core'),
                     'bathrooms' => esc_html__('Bathrooms', 'wpresidence-core'),
                     'garages' => esc_html__('Garages', 'wpresidence-core'),
                     'size' => esc_html__('Property Size', 'wpresidence-core'),
                     'year_built' => esc_html__('Year Buit', 'wpresidence-core'),
                 ),
                 'disabled' => array(
                     'property_category' => esc_html__('Property Category', 'wpresidence-core'),
                     'property_id' => esc_html__('Property Id', 'wpresidence-core'),
                     'property_internal_id'=> esc_html__('Listing Id', 'wpresidence-core'),
                     'rooms' => esc_html__('Rooms', 'wpresidence-core'),
                     'lot_size' => esc_html__('Lot Size', 'wpresidence-core'),
                     'map' => esc_html__('Map', 'wpresidence-core'),
                 )
             ),
         ),
     
         array(
             'id' => 'wpestate_overview_map_width',
             'type' => 'text',
             'title' => __('Overview Small Map width', 'wpresidence-core'),
             'subtitle' => __('Put only numners', 'wpresidence-core'),
             'default' => 120,
         ),
          array(
             'id' => 'wpestate_overview_map_height',
             'type' => 'text',
             'title' => __('Overview Small Map Height', 'wpresidence-core'),
             'subtitle' => __('Put only numners', 'wpresidence-core'),
             'default' => 120,
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Map section', 'wpresidence-core'),
     'id' => 'prop_map_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_map_prop_page2',
             'type' => 'button_set',
             'title' => __('Show Map on Property Page?', 'wpresidence-core'),
             'subtitle' => __('Obsolete option. Will be removed in the next theme update. The section is now managed from "Selection for Layouts" settings.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_hide_marker_pin',
             'type' => 'button_set',
             'title' => __('Hide map location?', 'wpresidence-core'),
             'subtitle' => __('if "yes" we will not show the address or exact location on property page map.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Energy Efficiency section', 'wpresidence-core'),
     'id' => 'energy_eff_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wpestate_energy_section_possible_grades',
             'type' => 'text',
             'title' => __('Energy Classes separated by comma', 'wpresidence-core'),
             'subtitle' => __('Energy Classes separated by comma: Ex:A+,A,B,c', 'wpresidence-core'),
             'default' => "A+,A,B,C,D,E,F,G,H",
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_1',
             'type' => 'color',
             'title' => __('Energy Class 1 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 1 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#6d9e00'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_2',
             'type' => 'color',
             'title' => __('Energy Class 2 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 2 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#7fb800'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_3',
             'type' => 'color',
             'title' => __('Energy Class 3 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 3 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#91d100'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_4',
             'type' => 'color',
             'title' => __('Energy Class 4 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 4 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#ebc400'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_5',
             'type' => 'color',
             'title' => __('Energy Class 5 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 5 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#eb9d00'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_6',
             'type' => 'color',
             'title' => __('Energy Class 6 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 6 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#e67300'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_7',
             'type' => 'color',
             'title' => __('Energy Class 7 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 7 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#d22300'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_8',
             'type' => 'color',
             'title' => __('Energy Class 8 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 8 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#b80000'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_9',
             'type' => 'color',
             'title' => __('Energy Class 9 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 9 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#790000'
         ),
         array(
             'id' => 'wp_estate_energy_class_colorb_10',
             'type' => 'color',
             'title' => __('Energy Class 10 Background Color', 'wpresidence-core'),
             'subtitle' => __('Energy Class 10 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#6d9e00'
         ),
         array(
             'id' => 'wpestate_co2_section_possible_grades',
             'type' => 'text',
             'title' => __('Greenhouse Gas Emissions classes separated by comma', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions classes separated by comma: Ex:A+,A,B,c', 'wpresidence-core'),
             'default' => "A+,A,B,C,D,E,F,G",
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_1',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 1 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 1 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#f7ecfd'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_2',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 2 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 2 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#e3c2f9'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_3',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 3 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 3 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#d5a9f4'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_4',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 4 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 4 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#cc95f3'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_5',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 5 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 5 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#c17df2'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_6',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 6 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 6 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#b971ee'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_7',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 7 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 7 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#a84ced'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_8',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 8 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 8 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#8818de'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_9',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 9 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 9 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#8818de'
         ),
         array(
             'id' => 'wp_estate_co2_class_colorb_10',
             'type' => 'color',
             'title' => __('Greenhouse Gas Emissions Class 10 Background Color', 'wpresidence-core'),
             'subtitle' => __('Greenhouse Gas Emissions Class 10 Background Color', 'wpresidence-core'),
             'transparent' => false,
             'default' => '#8818de'
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Mortgage Calculator', 'wpresidence-core'),
     'id' => 'mortgage_calculator_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_morg_calculator',
             'type' => 'button_set',
             'title' => __('Show Mortgage Calculator', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the mortgage calculator on property pages.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_excludeshow_morg_calculator',
             'type' => 'select',
             'multi' => true,
             'title' => __('Exclude Mortgage Calculator from Categories', 'wpresidence-core'),
             'subtitle' => __('Select the categories where the mortgage calculator should not appear.', 'wpresidence-core'),
             'data' => 'terms',
             'args' => array(
                 'taxonomies' => array('property_category', 'property_action_category'),
             ),
         ),
         array(
             'id' => 'wp_estate_morg_default_price_down_per',
             'required' => array('wp_estate_show_morg_calculator', '=', 'yes'),
             'type' => 'text',
             'title' => __('Default Down Payment (%)', 'wpresidence-core'),
             'subtitle' => __('Enter a numeric value. This represents the down payment percentage.', 'wpresidence-core'),
             'default' => 20,
         ),
         array(
             'id' => 'wp_estate_morg_default_morg_interest',
             'required' => array('wp_estate_show_morg_calculator', '=', 'yes'),
             'type' => 'text',
             'title' => __('Default Mortgage Interest Rate (%)', 'wpresidence-core'),
             'subtitle' => __('Enter a numeric value. This represents the mortgage interest rate as a percentage.', 'wpresidence-core'),
             'default' => 4.125,
         ),
         array(
             'id' => 'wp_estate_morg_default_morg_term',
             'required' => array('wp_estate_show_morg_calculator', '=', 'yes'),
             'type' => 'text',
             'title' => __('Default Mortgage Term (Years)', 'wpresidence-core'),
             'subtitle' => __('Set the default mortgage term in years.', 'wpresidence-core'),
             'default' => 30,
         ),
         array(
             'id' => 'wp_estate_morg_default_tax',
             'required' => array('wp_estate_show_morg_calculator', '=', 'yes'),
             'type' => 'text',
             'title' => __('Default Property Tax Percentage', 'wpresidence-core'),
             'subtitle' => __('Set the default property tax as a percentage. Each property will display this as a monthly fixed amount calculated as: property price × tax percent ÷ 12.', 'wpresidence-core'),
             'default' => 1.5,
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Contact & Schedule Tour ', 'wpresidence-core'),
     'id' => 'contact_calculator_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_global_property_page_agent_sidebar',
             'type' => 'button_set',
             'title' => __('Show Agent Contact form on Sidebar', 'wpresidence-core'),
             'subtitle' => __('Show agent contact form on sidebar.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_enable_direct_mess',
             'type' => 'button_set',
             'title' => __('Enable Direct Message?', 'wpresidence-core'),
             'subtitle' => __('If set to no, you will need to delete Inbox page template.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_use_classic_schedule',
             'type' => 'button_set',
             'title' => __('Use Classic tour schedule?', 'wpresidence-core'),
             'subtitle' => __('Use Classic tour schedule?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_show_schedule_tour',
             'type' => 'button_set',
             'title' => __('Display Schedule tour section', 'wpresidence-core'),
             'subtitle' => __('Select yes if you want to display schedule tour section.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         
         array(
             'id' => 'wp_estate_show_schedule_tour_type',
             'type' => 'button_set',
             'title' => __('Schedule Tour design type', 'wpresidence-core'),
             'required' => array('wp_estate_show_schedule_tour', '=', 'yes'),
             'subtitle' => __('Select the design version for  schedule tour section.', 'wpresidence-core'),
             'options' => array(
                 0 => 'type 1',
                 1 => 'type 2'
             ),
             'default' => 0,
         ),
         
         
         array(
             'id' => 'wp_estate_sidebar_contact_group',
             'type' => 'button_set',
             'title' => __('On sidebar - Display Contact Form and Schedule a tour sections as tabs ?', 'wpresidence-core'),
             'subtitle' => __('On sidebar - Display Contact Form and Schedule a tour sections as tabs ?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_schedule_tour_timeslots',
             'type' => 'textarea',
             'title' => __('Time Slots for Scheduled Tour', 'wpresidence-core'),
             'subtitle' => __('Use a comma to separate the time slots(Ex : 12:00 am, 12:30am)', 'wpresidence-core'),
             'default' => '10:00 am, 10:30 am, 11:00 am, 11:30 am, 12:00 pm, 12:30 pm,  01:00 pm,  01:30 pm,  02:00 pm, 02:30 pm',
         ),
         array(
             'id' => 'wp_estate_exclude_show_schedule_tour',
             'type' => 'select',
             'multi' => true,
             'title' => __('Exclude  Schedule tour from categories', 'wpresidence-core'),
             'subtitle' => __('Exclude  Schedule tour from categories', 'wpresidence-core'),
             'data' => 'terms',
             'args' => array(
                 'taxonomies' => array('property_category', 'property_action_category'),
             ),
         ),
 )));
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Show/Hide Details', 'wpresidence-core'),
     'id' => 'show_hide_details_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_hide_print_button',
             'type' => 'button_set',
             'title' => __('Show/Hide "Print" Button', 'wpresidence-core'),
             'subtitle' => __('Show/Hide "Print" Button', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_show_hide_fav_button',
             'type' => 'button_set',
             'title' => __('Show/Hide "Add to Favorite" Button', 'wpresidence-core'),
             'subtitle' => __('Show/Hide "Add to Favorite" Button', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_show_hide_share_button',
             'type' => 'button_set',
             'title' => __('Show/Hide "Share" Button', 'wpresidence-core'),
             'subtitle' => __('Show/Hide "Share" Button', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_show_hide_address_details',
             'type' => 'button_set',
             'title' => __('Show/Hide "Address Details " under title', 'wpresidence-core'),
             'subtitle' => __('Show/Hide "Address Details " under title', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
 )));
 
 
 
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Listings Labels', 'wpresidence-core'),
     'id' => 'listing_labels_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_property_overview_text',
             'type' => 'text',
             'title' => __('Overview', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Overview label.', 'wpresidence-core'),
             'default' => 'Overview'
         ),
         array(
             'id' => 'wp_estate_property_multi_text',
             'type' => 'text',
             'title' => __('Multi Unit Label', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Multi Unit label.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_property_multi_child_text',
             'type' => 'text',
             'title' => __('Multi Unit Label (*for sub unit)', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Multi Unit label(*for sub unit).', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_property_adr_text',
             'type' => 'text',
             'title' => __('Property Address Label', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Property Address label.', 'wpresidence-core'),
             'default' => 'Property Address'
         ),
         array(
             'id' => 'wp_estate_property_features_text',
             'type' => 'text',
             'title' => __('Property Features Label', 'wpresidence-core'),
             'subtitle' => __('Update; Custom title instead of Features and Amenities label.', 'wpresidence-core'),
             'default' => 'Property Features'
         ),
         array(
             'id' => 'wp_estate_property_description_text',
             'type' => 'text',
             'title' => __('Property Description Label', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Description label.', 'wpresidence-core'),
             'default' => 'Property Description'
         ),
         array(
             'id' => 'wp_estate_property_documents_text',
             'type' => 'text',
             'title' => __('Property Documents Label', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Documents label.', 'wpresidence-core'),
             'default' => 'Property Documents'
         ),
         array(
             'id' => 'wp_estate_property_energy_savings_text',
             'type' => 'text',
             'title' => __('Property Energy Savings Label', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Energy Savings label.', 'wpresidence-core'),
             'default' => 'Energy Savings'
         ),
         array(
             'id' => 'wp_estate_property_details_text',
             'type' => 'text',
             'title' => __('Property Details Label', 'wpresidence-core'),
             'subtitle' => __('Custom title instead of Property Details label.', 'wpresidence-core'),
             'default' => 'Property Details'
         ),
         array(
             'id' => 'wp_estate_property_map_text',
             'type' => 'text',
             'title' => __('Property Map Label', 'wpresidence-core'),
             'subtitle' => __('The label for map section.', 'wpresidence-core'),
             'default' => 'Map'
         ),
         array(
             'id' => 'wp_estate_property_video_text',
             'type' => 'text',
             'title' => __('Property Video Label', 'wpresidence-core'),
             'subtitle' => __('The label for video section.', 'wpresidence-core'),
             'default' => 'Video'
         ),
        array(
            'id' => 'wp_estate_property_virtual_tour_text',
            'type' => 'text',
            'title' => __('Property Virtual Tour Label', 'wpresidence-core'),
            'subtitle' => __('The label for Virtual Tour section.', 'wpresidence-core'),
            'default' => 'Virtual Tour'
        ),
        array(
            'id' => 'wp_estate_property_booking_shortcode_text',
            'type' => 'text',
            'title' => __('Property Booking Shortcode Label', 'wpresidence-core'),
            'subtitle' => __('The label for the Booking Shortcode section.', 'wpresidence-core'),
            'default' => 'Booking Shortcode'
        ),
        array(
            'id' => 'wp_estate_property_walkscorer_text',
            'type' => 'text',
            'title' => __('Property Walkscore Label', 'wpresidence-core'),
            'subtitle' => __('The label for Walkscore section.', 'wpresidence-core'),
             'default' => 'Walkscore'
         ),
         array(
             'id' => 'wp_estate_property_near_by_text',
             'type' => 'text',
             'title' => __('Property What\'s Nearby Label', 'wpresidence-core'),
             'subtitle' => __('The label for What\'s Nearby section.', 'wpresidence-core'),
             'default' => 'What\'s Nearby'
         ),
         array(
             'id' => 'wp_estate_property_calculator_text',
             'type' => 'text',
             'title' => __('Property Payment Calculator Label', 'wpresidence-core'),
             'subtitle' => __('The label for Payment Calculator section.', 'wpresidence-core'),
             'default' => 'Payment Calculator'
         ),
         array(
             'id' => 'wp_estate_property_page_views_text',
             'type' => 'text',
             'title' => __('Property Page Views Label', 'wpresidence-core'),
             'subtitle' => __('The label for Page Views section.', 'wpresidence-core'),
             'default' => 'Page Views Statistics'
         ),
         array(
             'id' => 'wp_estate_property_floor_plan_text',
             'type' => 'text',
             'title' => __('Property Floor Plans Label', 'wpresidence-core'),
             'subtitle' => __('The label for Floor Plans section.', 'wpresidence-core'),
             'default' => 'Floor Plans'
         ),
         array(
             'id' => 'wp_estate_property_reviewstext',
             'type' => 'text',
             'title' => __('Property Reviews Label', 'wpresidence-core'),
             'subtitle' => __('The label for reviews section.', 'wpresidence-core'),
             'default' => 'Property Reviews'
         ),
         array(
             'id' => 'wp_estate_property_schedule_tour_text',
             'type' => 'text',
             'title' => __('Property Schedule Tour Label', 'wpresidence-core'),
             'subtitle' => __('The label for schedule tour section.', 'wpresidence-core'),
             'default' => 'Schedule a tour'
         ),
         array(
             'id' => 'wp_estate_property_similart_listings_text',
             'type' => 'text',
             'title' => __('Property Similar Listings Label', 'wpresidence-core'),
             'subtitle' => __('The label for similar listings section.', 'wpresidence-core'),
             'default' => 'Similar Listings'
         ),
         array(
             'id' => 'wp_estate_property_other_agents_text',
             'type' => 'text',
             'title' => __('Property "Other Agents" Label', 'wpresidence-core'),
             'subtitle' => __('The label for Other Agents section.', 'wpresidence-core'),
             'default' => 'Other Agents'
         ),
         array(
             'id' => 'wp_estate_property_sitcky_agent_text',
             'type' => 'text',
             'title' => __('Property Agent Label - only navigation bar', 'wpresidence-core'),
             'subtitle' => __('The label for Agents section.', 'wpresidence-core'),
             'default' => 'Agent'
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('LightBox', 'wpresidence-core'),
     'id' => 'lightbox_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_lightbox_contact',
             'type' => 'button_set',
             'title' => __('Show Contact Form on lightbox', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the contact form on lightbox.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_crop_images_lightbox',
             'type' => 'button_set',
             'title' => __('Crop Images on lightbox', 'wpresidence-core'),
             'subtitle' => __('Images will have the same size. If set to no you will need to make sure that images are about the same size', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         
          array(
             'id' => 'wp_estate_lightbox_slider',
             'type' => 'button_set',
             'title' => __('Slider for lightbox', 'wpresidence-core'),
             'subtitle' => __('what kind of slider to use on property page lightbox', 'wpresidence-core'),
             'options' => array(
                 'owl' => 'owl carousel',
                 'slick' => 'slick slider'
             ),
             'default' => 'owl',
         ),
         
         
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Features & Amenities', 'wpresidence-core'),
     'id' => 'ammenities_features_tab',
     'subsection' => true,
     'fields' => array(
       
         array(
             'id' => 'wp_estate_show_no_features',
             'type' => 'button_set',
             'title' => __('Show the Features and Amenities that are not available', 'wpresidence-core'),
             'subtitle' => __('Show on property page the features and amenities that are not selected?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
     ),
 ));

 Redux::setSection($opt_name, array(
     'title' => __('Print Page Design', 'wpresidence-core'),
     'id' => 'property_page_simple_print_tab',
     'subsection' => true,

     'fields' => array(
         array(
             'id' => 'wp_estate_property_page_print_order',
             'type' => 'sorter',
             'title' => 'Arrange Sections',
             'desc' => 'Drag and drop sections and organize your property page design.',
             'options' => array(
                 'enabled' => array(
                     'overview' => esc_html__('Overview', 'wpresidence-core'),
                     'images' => esc_html__('Images', 'wpresidence-core'),
                     'description' => esc_html__('Description', 'wpresidence-core'),
                    //  'documents' => esc_html__('Documents', 'wpresidence-core'),
                     'multi-units' => esc_html__('Multi Units', 'wpresidence-core'),
                     'energy-savings' => esc_html__('Energy Savings', 'wpresidence-core'),
                     'address' => esc_html__('Address', 'wpresidence-core'),
                     'listing_details' => esc_html__('Listing Details', 'wpresidence-core'),
                     'features' => esc_html__('Amenities and Features', 'wpresidence-core'),
                    //  'video' => esc_html__('Video', 'wpresidence-core'),
                     'map' => esc_html__('Map', 'wpresidence-core'),
                    //  'virtual_tour' => esc_html__('Virtual Tour', 'wpresidence-core'),
                    //  'walkscore' => esc_html__('WalkScore', 'wpresidence-core'),
                    //  'nearby' => esc_html__('What\'s Nearby', 'wpresidence-core'),
                    //  'payment_calculator' => esc_html__('Payment Calculator', 'wpresidence-core'),
                     'floor_plans' => esc_html__('Floor Plans', 'wpresidence-core'),
                    //  'page_views' => esc_html__('Page Views', 'wpresidence-core'),
                    //  'schedule_tour' => esc_html__('Schedule Tour', 'wpresidence-core'),
                     'agent_area' => esc_html__('Agent', 'wpresidence-core'),
                     'other_agents' => esc_html__('Other Agents', 'wpresidence-core'),
                     'reviews' => esc_html__('Reviews', 'wpresidence-core'),
                    //  'similar' => esc_html__('Similar Listings', 'wpresidence-core'),
                 ),
                 'disabled' => array(
                 )
             ),
             'save_always' => true,
         ),
 )));
 
//  Redux::setSection($opt_name, array(
//      'title' => __('Print Page Design', 'wpresidence-core'),
//      'id' => 'print_page_tab',
//      'subsection' => true,
//      'fields' => array(
//          array(
//              'id' => 'wp_estate_print_show_subunits',
//              'type' => 'button_set',
//              'title' => __('Show subunits section', 'wpresidence-core'),
//              'subtitle' => __('Show subunits section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//          array(
//              'id' => 'wp_estate_print_show_agent',
//              'type' => 'button_set',
//              'title' => __('Show agent details section', 'wpresidence-core'),
//              'subtitle' => __('Show agent details section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//          array(
//              'id' => 'wp_estate_print_show_description',
//              'type' => 'button_set',
//              'title' => __('Show description section', 'wpresidence-core'),
//              'subtitle' => __('Show description section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//          array(
//              'id' => 'wp_estate_print_show_adress',
//              'type' => 'button_set',
//              'title' => __('Show address section', 'wpresidence-core'),
//              'subtitle' => __('Show address section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//          array(
//              'id' => 'wp_estate_print_show_details',
//              'type' => 'button_set',
//              'title' => __('Show details section', 'wpresidence-core'),
//              'subtitle' => __('Show details section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//          array(
//              'id' => 'wp_estate_print_show_features',
//              'type' => 'button_set',
//              'title' => __('Show features & amenities section', 'wpresidence-core'),
//              'subtitle' => __('Show features & amenities section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//          array(
//              'id' => 'wp_estate_print_show_floor_plans',
//              'type' => 'button_set',
//              'title' => __('Show floor plans section', 'wpresidence-core'),
//              'subtitle' => __('Show floor plans section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//          array(
//              'id' => 'wp_estate_print_show_images',
//              'type' => 'button_set',
//              'title' => __('Show gallery section', 'wpresidence-core'),
//              'subtitle' => __('Show gallery section in print page?', 'wpresidence-core'),
//              'options' => array(
//                  'yes' => 'yes',
//                  'no' => 'no'
//              ),
//              'default' => 'yes',
//          ),
//      ),
//  ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Yelp settings', 'wpresidence-core'),
     'id' => 'yelp_tab',
     'desc' => __('Please note that Yelp is not working for all countries. See here https://www.yelp.com/factsheet the list of countries where Yelp is available.', 'wpresidence-core'),
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_yelp_client_id',
             'type' => 'text',
             'title' => __('Yelp Api Client ID', 'wpresidence-core'),
             'subtitle' => __('Get this detail after you signup here: ', 'wpresidence-core') . '<a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">https://www.yelp.com/developers/v3/manage_app</a>',
         ),
         array(
             'id' => 'wp_estate_yelp_client_api_key_2018',
             'type' => 'text',
             'title' => __('Yelp Api Key', 'wpresidence-core'),
             'subtitle' => __('Get this detail after you signup here: ', 'wpresidence-core') . '<a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">https://www.yelp.com/developers/v3/manage_app</a>',
         ),
         array(
             'id' => 'wp_estate_yelp_categories',
             'type' => 'select',
             'multi' => true,
             'title' => __('Yelp Categories', 'wpresidence-core'),
             'subtitle' => __('Yelp Categories to show on front page', 'wpresidence-core'),
             'options' => wpresidence_core_redux_yelp(),
         ),
         array(
             'id' => 'wp_estate_yelp_results_no',
             'type' => 'text',
             'title' => __('Yelp - no of results', 'wpresidence-core'),
             'subtitle' => __('*Numeric field. Type no of results wish to show on listing page for each category.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_yelp_dist_measure',
             'type' => 'button_set',
             'title' => __('Yelp Distance Measurement Unit', 'wpresidence-core'),
             'subtitle' => __('Yelp Distance Measurement Unit', 'wpresidence-core'),
             'options' => array('miles' => 'miles', 'kilometers' => 'kilometers'),
             'default' => 'miles',
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Walkscore section', 'wpresidence-core'),
     'id' => 'walscore_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_walkscore_api',
             'type' => 'text',
             'title' => __('Walkscore APi Key', 'wpresidence-core'),
             'subtitle' => __('Walkscore info doesn\'t show if you don\'t add the API. Score is available for any address in the United States, Canada, and Australia. Register for an API (free) here https://www.walkscore.com/professional/api-sign-up.php', 'wpresidence-core'),
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('BreadCrumbs', 'wpresidence-core'),
     'id' => 'breadcrumbs_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wpestate_property_breadcrumbs',
             'type' => 'select',
             'multi' => true,
             'title' => __('Select taxonomies that will appear in the breadcrumbs', 'wpresidence-core'),
             'subtitle' => __('Select taxonomies that will appear in the breadcrumbs', 'wpresidence-core'),
             'options' => array(
                 'property_category' => 'category',
                 'property_action_category' => 'action category',
                 'property_city' => 'city',
                 'property_area' => 'area',
                 'property_status' => 'status',
                 'property_county_state'=>'county/state'
             ),
             'default' => 'property_category',
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Disclaimer', 'wpresidence-core'),
     'id' => 'disclaimer_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_disclaiment_text',
             'type' => 'textarea',
             'title' => __('Disclaimer Text', 'wpresidence-core'),
             'subtitle' => __('Disclaimer Text. You can use the strings %property_address and %propery_id and the theme will replace those with the property address and id.', 'wpresidence-core'),
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Reviews', 'wpresidence-core'),
     'id' => 'reviews_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_no_of_reviews',
             'type' => 'text',
             'title' => __('Display only the first X reviews.', 'wpresidence-core'),
             'subtitle' => __('Add a number or leave blank to show all reviews', 'wpresidence-core'),
             'default' => 10
         ),
 )));
 
 if(function_exists('wpestate_listings_sort_options_array')){
     $wpestate_listings_sort_options_array=wpestate_listings_sort_options_array();
 }else{
     $wpestate_listings_sort_options_array=array();
 }
 
 Redux::setSection($opt_name, array(
     'title' => __('Similar Listings', 'wpresidence-core'),
     'id' => 'similar_listings_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_similar_prop_no',
             'type' => 'text',
             'title' => __('No of similar properties in property page', 'wpresidence-core'),
             'subtitle' => __('Similar listings show when there are other properties from the same area, city, type and category.', 'wpresidence-core'),
             'default' => '4'
         ),
         array(
             'id' => 'wp_estate_similar_prop_per_row',
             'type' => 'button_set',
             'title' => __('No of similar property listings per row when the page is without sidebar', 'wpresidence-core'),
             'subtitle' => __('When the page is with sidebar the no of listings per row will be 2 or 3 - depending on your selection', 'wpresidence-core'),
             'options' => array(
                 '3' => '3',
                 '4' => '4'
             ),
             'default' => '3',
         ),
         array(
             'id' => 'wp_estate_simialar_taxes',
             'type' => 'select',
             'multi' => true,
             'title' => __('Select taxonomies for similar listings', 'wpresidence-core'),
             'subtitle' => __('Select taxonomies for similar listings( if none is selected we will use property category, property action category and property city)', 'wpresidence-core'),
             'options' => array(
                 'property_category' => 'category',
                 'property_action_category' => 'action category',
                 'property_city' => 'city',
                 'property_area' => 'area'
             ),
         ),
         array(
             'id' => 'wp_estate_similar_listins_order',
             'type' => 'select',
             'title' => __('Select Similar Listings Order', 'wpresidence-core'),
             'subtitle' => __('Select Similar Listings Order', 'wpresidence-core'),
             'options' => $wpestate_listings_sort_options_array,
         ),
         
            array(
             'id' => 'wp_estate_unit_md_similar',
             'type' => 'button_set',
             'title' => __('Similar listing display format', 'wpresidence-core'),
             'subtitle' => __('Chose the similar listing display format - grid or list ', 'wpresidence-core'),
             'options' => array(
                 'grid' => 'grid',
                 'list' => 'list'
             ),
             'default' => 'grid',
         ),
         
         
         
 )));
 
 // -> START Property card Desgin
 Redux::setSection($opt_name, array(
     'title' => __('Property Card Design (used in lists)', 'wpresidence-core'),
     'id' => 'property_page_card_settings_sidebar',
     'icon' => 'el el-lines'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Property Card General Settings', 'wpresidence-core'),
     'id' => 'property_card_design_tab',
     'subsection' => true,
     'fields' => array(
        /* array(
             'id' => 'wp_estate_unit_card_type',
             'type' => 'button_set',
             'title' => __('Unit Card Type', 'wpresidence-core'),
             'subtitle' => __('Unit Card Type', 'wpresidence-core'),
             'options' => array(
                 '0' => __('default', 'wpresidence-core'),
                 '1' => __('type 1', 'wpresidence-core'),
                 '2' => __('type 2', 'wpresidence-core'),
                 '3' => __('type 3', 'wpresidence-core'),
                 '4' => __('type 4', 'wpresidence-core'),
                 '5' => __('type 5', 'wpresidence-core'),
                 '6' => __('type 6', 'wpresidence-core'),
                 '7' => __('type 7', 'wpresidence-core'),
             ),
             'default' => '0',
         ),*/
         
         array(
             'id' => 'wp_estate_unit_card_type',
             'type' => 'image_select',
             'title' => __('Unit Card Type', 'wpresidence-core'),
             'subtitle' => __('Unit Card Type', 'wpresidence-core'),
             'options' => array(
                  0 => array(
                     'title'=>'default card unit',
                     'alt' => 'default card unit',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-o.png'
                 ),
                 1 => array(
                     'title'=>'type 1',
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-o-1.png'
                 ),
                 2 => array(
                      'title'=>'type 2',
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-o-2.png'
                 ),
                 3 => array(
                      'title'=>'type 3',
                     'alt' => '',
                    'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-o-3.png'
                 ),
                 4 => array(
                      'title'=>'type 4',
                     'alt' => '',
                      'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-o-4.png'
                 ),
                 5 => array(
                      'title'=>'type 5',
                     'alt' => '',
                       'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-o-5.png'
                 ),
                 6 => array(
                      'title'=>'type 6',
                     'alt' => '',
                      'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-o-6.png'
                 ),
                 7 => array(
                      'title'=>'type 7',
                     'alt' => '',
                      'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-7.png'
                 ),
                  8 => array(
                      'title'=>'type 8',
                     'alt' => '',
                      'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/property-cards/card-unit-8.png'
                 ),
             ),
             'default' => 0,
          ),
         
         
         
         array(
             'id' => 'wp_estate_prop_unit',
             'type' => 'button_set',
             'title' => __('Property List display (*global option)', 'wpresidence-core'),
             'subtitle' => __('Select grid or list style for properties list pages.', 'wpresidence-core'),
             'options' => array(
                 'grid' => __('grid', 'wpresidence-core'),
                 'list' => __('list', 'wpresidence-core')
             ),
             'default' => 'grid',
         ),
               array(
             'id' => 'wp_estate_unit_card_title',
             'type' => 'text',
             'title' => __('No of chars to show in Property Title', 'wpresidence-core'),
             'subtitle' => __('Set how many characters to display from the property title - leave blank for full title', 'wpresidence-core'),
             'default' => '44'
         ),
      
          array(
             'id' => 'wp_estate_unit_card_excerpt_grid',
             'type' => 'text',
             'title' => __('No of chars to show in Property card excerpt - grid style', 'wpresidence-core'),
             'subtitle' => __('Set how many characters to display from property excerpt for grid style.', 'wpresidence-core'),
             'default' => '90'
         ),
         array(
             'id' => 'wp_estate_unit_card_excerpt_list',
             'type' => 'text',
             'title' => __('No of chars to show in Property card excerpt - list style', 'wpresidence-core'),
             'subtitle' => __('Set how many characters to display from property excerpt for list style.', 'wpresidence-core'),
             'default' => '160'
         ),
         
         array(
             'id' => 'wp_estate_use_property_modal',
             'type' => 'button_set',
             'title' => __('Click and open Property Modal Window - Zillow style', 'wpresidence-core'),
             'subtitle' => __('Click and open Property Modal Window - Zillow style', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
        
         array(
             'id' => 'wp_estate_unit_card_new_page',
             'type' => 'button_set',
             'title' => __('Open title link to property page in new window', 'wpresidence-core'),
             'subtitle' => __('Open tile link to property page in new window', 'wpresidence-core'),
             'options' => array(
                 '_blank' => 'yes',
                 '_self' => 'no'
             ),
             'default' => '_self',
         ),
         array(
             'id' => 'wp_estate_listings_per_row',
             'type' => 'button_set',
             'title' => __('No of property listings per row when the page is without sidebar', 'wpresidence-core'),
             'subtitle' => __('When the page is with sidebar the no of listings per row will be 2 or 3 - depending on your selection', 'wpresidence-core'),
             'options' => array(
                 '3' => '3',
                 '4' => '4'
             ),
             'default' => '3',
         ),
         array(
             'id' => 'wp_estate_prop_unit_min_height',
             'type' => 'text',
             'title' => __('Property Unit/Card min height', 'wpresidence-core'),
             'subtitle' => __('Property Unit/Card min height', 'wpresidence-core'),
         ),
         
         array(
             'id' => 'wp_estate_call_show_modal_unit7',
             'type' => 'button_set',
             'title' => __('Show modal for the "Call" action in card type 7', 'wpresidence-core'),
             'subtitle' => __('Show modal for the "Call" action in card type 7', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         
         array(
             'id' => 'wp_estate_call_text_unit7',
            'type' => 'editor',
             'title' => __('Text for the "Call" modal in card type 7', 'wpresidence-core'),
             'subtitle' => __('Text for the "Call" modal in card type 7', 'wpresidence-core'),
             'default' => 'You can contact  %realtor_name via
              phone: %realtor_phone
              mobile: %realtor_mobile
              Please use the Property ID %property_id to identify the property "%title"',
             'desc' => esc_html__('%property_id as  property ID, %title as property title, %realtor_name as realtor name, %realtor_phone as realtor phone,, %realtor_mobile as realtor mobile,use text mode and <br class=""> tag for new line, <p class=""> for a new paragraph,
 <span class=""> for styling.', 'wpresidence-core'),
         ),
         
          
 
 )));
 
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Property Card Image Settings', 'wpresidence-core'),
     'id' => 'property_card_image_settings',
     'subsection' => true,
     'fields' => array(
          array(
             'id' => 'wp_estate_prop_list_slider',
             'type' => 'button_set',
             'title' => __('Use Slider in Property Unit', 'wpresidence-core'),
             'subtitle' => __('Enable / Disable the image slider in property unit (used in lists).', 'wpresidence-core'),
             'options' => array(
                 '1' => __('yes', 'wpresidence-core'),
                 '0' => __('no', 'wpresidence-core')
             ),
             'default' => '0',
         ),
         array(
             'id' => 'wp_estate_prop_list_slider_image_number',
             'type' => 'text',
             'required' => array('wp_estate_prop_list_slider', '=', '1'),
             'default' => 3,
             'title' => __('No of images in slider in property card', 'wpresidence-core'),
             'subtitle' => __('More images means longer load time', 'wpresidence-core'),
         ),
       
         
         array(
             'id' => 'wp_estate_prop_list_slider_image_palceholder',
             'type' => 'media',
             'url' => true,
             'title' => __('Add placeholder image', 'wpresidence-core'),
             'subtitle' => __('Replace the theme default property card unit placeholder image with a custom one.', 'wpresidence-core'),
         ),
        
         
         
 )));
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_property_card_details_customizer/' );
 Redux::setSection($opt_name, array(
     'title' => __('Property Card Content Composer', 'wpresidence-core'),
     'id' => 'property_card_composer_tab',
     'subsection' => true,
     'fields' => array(
          array(
             'id' => 'wp_estate_use_composer_details',
             'type' => 'button_set',
  
             'title' => __('Use Composer Settings', 'wpresidence-core'),
             'subtitle' => __('Note: Composer does not apply to card type 5.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_property_card_rows',
             'type' => 'sorter',
             'title' => __('Property Card - rows order', 'wpresidence-core'),
             'subtitle' => __('Order for the content rows in card', 'wpresidence-core'),
             'options' => array(
                 'enabled' => array(
                    // 'image' => esc_html__('Image Section from selected unit', 'wpresidence-core'),
                     'title' => esc_html__('Title row', 'wpresidence-core'),
                     'price' => esc_html__('Price row', 'wpresidence-core'),
                     'second_price' => esc_html__('Additional Price row', 'wpresidence-core'),
                     'excerpt' => esc_html__('Excerpt row', 'wpresidence-core'),
                     'details' => esc_html__('Details row', 'wpresidence-core'),
                     'address' => esc_html__('Address row', 'wpresidence-core'),
                     'categories' => esc_html__('Categories row', 'wpresidence-core'),
                     'contact' => esc_html__('Contact row', 'wpresidence-core'),
                     
                    // 'agent' => esc_html__('Agent', 'wpresidence-core')
                 ),
                 'disabled' => array(
                     'mlsdata' => esc_html__('MLS Info', 'wpresidence-core'),
                 )
             ),
         ),

         array(
             'id' => 'wp_estate_property_card_rows_details',
             'type'     => 'wpestate_property_card_details_customizer',
             'full_width' => true,
             'title' => __('Property Card Details Row', 'wpresidence-core'),
             'subtitle' => __('Choose what fields to show in the details row', 'wpresidence-core'),
      
         ),
       
         array(
             'id' => 'wp_estate_property_card_rows_address',
             'type' => 'sorter',
              'title' => __('Property Card Adress Row', 'wpresidence-core'),
             'subtitle' => __('Compose the address row in the card unit', 'wpresidence-core'),
             'options' => array(
                 'enabled' => array(
                     'property_address' => esc_html__('Adress', 'wpresidence-core'),
                     'property_zip' => esc_html__('Zip', 'wpresidence-core'),
                     'property_county_state' => esc_html__('County/State', 'wpresidence-core'),
                     'property_city' => esc_html__('City', 'wpresidence-core'),
                     'property_area' => esc_html__('Area', 'wpresidence-core'),
                 ),
                 'disabled' => array(
                 )
             ),
         ),
         
         array(
             'id' => 'wp_estate_property_card_rows_categories',
             'type' => 'sorter',
             'title' => __('Property Card Categories Row', 'wpresidence-core'),
             'subtitle' => __('Compose the categories row in the card unit', 'wpresidence-core'),
             'options' => array(
                 'enabled' => array(
                     'property_category' => esc_html__('Category', 'wpresidence-core'),
                     'property_action_category' => esc_html__('Type', 'wpresidence-core'),
                     'property_status' => esc_html__('Status', 'wpresidence-core'),
                     
                 ),
                 'disabled' => array(
                 )
             ),
         ),
         
         array(
             'id' => 'wp_estate_property_card_rows_mls_logo',
             'type' => 'media',
             'url' => true,
             'title' => __('MLS or Broker Logo', 'wpresidence-core'),
             'subtitle' => __('MLS or Broker Logo - will be used in Composer, the MLS Info section.', 'wpresidence-core'),
            
         ),
            array(
             'id' => 'wp_estate_property_card_rows_mls_name',
             'type' => 'text',
             'title' => __('MLS or Broker Name', 'wpresidence-core'),
             'subtitle' => __('MLS or Broker Name - will be used in Composer, the MLS Info section.', 'wpresidence-core'),
             'default' => '',
         ),
         
    /*     
         array(
             'id' => 'wp_estate_property_card_rows_agent',
             'type' => 'sorter',
             'title' => __('Property Card Agent Row', 'wpresidence-core'),
             'subtitle' => __('Order of content rows in card', 'wpresidence-core'),
             'options' => array(
                 'enabled' => array(
                     'bedrooms'  => esc_html__('Agent Thumb & Name', 'wpresidence-core'),
                     'bathrooms' => esc_html__('User Icon & Name', 'wpresidence-core'),
                     'bedrooms1' => esc_html__('Agent Thumb', 'wpresidence-core'),
                     'name'      => esc_html__('Name', 'wpresidence-core'),
                     'share'     => esc_html__('Share', 'wpresidence-core'),
                     'favorite'  => esc_html__('Favorite', 'wpresidence-core'),
                     'compare'   => esc_html__('Compare', 'wpresidence-core'),
                     
                 ),
                 'disabled' => array(
                 )
             ),
         ),
      */   
         
     ))
 );
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Property Card Agent Section Settings', 'wpresidence-core'),
     'id' => 'property_card_agent_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
                'id' => 'property_card_agent_show_row',
                'type' => 'button_set',
 
                'title' => __('Display the agent row? ', 'wpresidence-core'),
                'subtitle' => __('Enable or disable the full agent row.', 'wpresidence-core'),
                'options' => array(
                    'yes' => 'yes',
                    'no' => 'no'
                ),
                'default' => 'yes',
            ),
            array(
             'id' => 'property_card_agent_section_tab_show_agent_image',
             'type' => 'button_set',
  
             'title' => __('Show Agent Image', 'wpresidence-core'),
             'subtitle' => __('Enable or disable agent image.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         
            array(
             'id' => 'property_card_agent_section_tab_show_agent_name',
             'type' => 'button_set',
  
             'title' => __('Show agent name ', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the agent name.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
 )));
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Property Card Composer - Extra Settings', 'wpresidence-core'),
     'id' => 'property_card_composer_extra_details_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_card_details_text_align',
             'type' => 'button_set',

             'title' => __('Card Text Align', 'wpresidence-core'),
             'options' => array(
                 'left' => __('left', 'wpresidence-core'),
                 'center' => __('center', 'wpresidence-core'),
                 'right' => __('right', 'wpresidence-core'),
 
             ),
             'default' => 'left',
         ),
         array(
             'id' => 'wp_estate_card_details_font_awsome_size',
             'type' => 'text',
             'title' => __('Details Row - Font Awsesome Icons Size', 'wpresidence-core'),
             'subtitle' => __('Details Row - Font Awsesome Icons Size in px', 'wpresidence-core'),
             'default' => '13',
         ),
         array(
             'id' => 'wp_estate_card_details_image_icon_size',
             'type' => 'text',
  
             'title' => __('Details Row - Image Icons Size', 'wpresidence-core'),
             'subtitle' => __('Details Row - Image Icons Size max-height in px', 'wpresidence-core'),
             
             'default' => '17',
         ),
           
             array(
                 'id' => 'wp_estate_card_details_font_size',
                 'type' => 'typography',
                 'title' => __('Details Row - Font Control', 'wpresidence-core'),
                 'subtitle' => __('Details Row - select field value text font size,weight or color', 'wpresidence-core'),
                
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
             'id' => 'wp_estate_card_details_alignment',
             'type' => 'button_set',
  
             'title' => __('Details Row Align options', 'wpresidence-core'),
             'subtitle' => __('Manage how to align the fields in the details row.', 'wpresidence-core'),
             'options' => array(
                'flex-start' => 'left',
                'center'=> 'center',
                'flex-end' => 'right',
                'space-between' => 'fill'
             ),
             'default' => 'fill',
         ),
         array(
             'id' => 'wp_estate_card_details_image_position',
             'type' => 'button_set',
  
             'title' => __('Details Image/Icon position', 'wpresidence-core'),
             'subtitle' => __('Manage how to align the icon in the details row', 'wpresidence-core'),
             'options' => array(
                 'row' => 'left',
                 'row-reverse' => 'right',
                 'column' => 'top',
                 'column-reverse'=>'bottom'
             ),
             'default' => 'left',
         ),
         array(
             'id' => 'wp_estate_card_details_image_color',
             'type' => 'color',
             'title' => __('Details Font Awesome Icon Color- (it is not applicable to images)', 'wpresidence-core'),
             'subtitle' => __('For Images, upload the image in the color you wish', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_card_details_gap',
             'type' => 'text',
             'title' => __('Gap between details in px', 'wpresidence-core'),
             'subtitle' => __('Space between details', 'wpresidence-core'),
             
         ),
         
 )));
         
         
         
         
         
 Redux::setSection($opt_name, array(
     'title' => __('Show/Hide Details', 'wpresidence-core'),
     'id' => 'property_card_show_hide_details',
     'subsection' => true,
     'fields' => array(
         
            array(
             'id' => 'property_card_agent_show_compare',
             'type' => 'button_set',
  
             'title' => __('Show compare button ', 'wpresidence-core'),
             'subtitle' => __('Show compare button', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'property_card_agent_show_share',
             'type' => 'button_set',
  
             'title' => __('Show share button ', 'wpresidence-core'),
             'subtitle' => __('Show share button', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
          array(
             'id' => 'property_card_agent_show_favorite',
             'type' => 'button_set',
  
             'title' => __('Show favorite button ', 'wpresidence-core'),
             'subtitle' => __('Show favorite button', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'property_card_agent_show_status',
             'type' => 'button_set',
  
             'title' => __('Show property status labels ', 'wpresidence-core'),
             'subtitle' => __('Show property status labels', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'property_card_agent_show_featured',
             'type' => 'button_set',
  
             'title' => __('Show property featured labels ', 'wpresidence-core'),
             'subtitle' => __('Show property featured labels', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
 )));
 
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_custom_unit_design/' );
 Redux::setSection($opt_name, array(
     'title' => __('Property Card Design - Beta', 'wpresidence-core'),
     'id' => 'property_page__design_tab',
     'subsection' => true,
     'desc'  => __('Please use the "Save Design" button in order to save your design','wpresidence-core'), 
     'fields' => array(
         array(
             'id' => 'wpestate_uset_unit',
             'type' => 'button_set',
             'title' => __('Use this unit/card?', 'wpresidence-core'),
             'options' => array(
                 '1' => 'yes',
                 '0' => 'no'
             ),
             'default' => '0'// 1 = on | 0 = off
         ),
         array(
             'id' => 'wpestate_property_page_content1',
             'type' => 'wpestate_custom_unit_design',
             'title' => __('Card Designer', 'wpresidence-core'),
             'subtitle' => __('This property unit builder is a very complex feature, with a lot of options, and because of that it may not work for all design idees. We will continue to improve it, but please be aware that css problems may appear and those will have to be solved by manually adding css rules in the code.', 'wpresidence-core'),
             'full_width' => true
         ),
     ),
 ));
 
 
 
 
 
 // -> START Design Selection
 Redux::setSection($opt_name, array(
     'title' => __(' Agents, Agencies, Developers', 'wpresidence-core'),
     'id' => 'agent_agencies_developers_settings_sidebar',
     'icon' => 'el el-group'
 ));
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Agent Page', 'wpresidence-core'),
     'id' => 'general_design_settingsddd_tab',
     'subsection' => true,
     'fields' => array(
          array(
             'id' => 'wp_estate_agent_layouts',
             'type' => 'image_select',
             'title' => esc_html__('Agent Page Layout', 'wpresidence-core'),
             'subtitle' => __('Pick your Agent Page Layout', 'wpresidence-core'),
             'options' => array(
                 1 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/agent-page/type1.png'
                 ),
                 2 => array(
                     'alt' => '',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/agent-page/type2.png'
                 ),
                 ),
             
              'default' => 1,
         
             ),
         
         
         array(
             'id' => 'wp_estate_agent_sidebar',
             'type' => 'button_set',
             'title' => __('Agent Page - Select Sidebar Position', 'wpresidence-core'),
             'subtitle' => __('Where to show the sidebar on the agent page type 1. Applies when agent registers through theme register form.', 'wpresidence-core'),
             'options' => array(
                 'no sidebar' => __('no sidebar', 'wpresidence-core'),
                 'right' => __('right', 'wpresidence-core'),
                 'left' => __('left', 'wpresidence-core')
             ),
             'default' => 'right',
         ),
         array(
             'id' => 'wp_estate_agent_sidebar_name',
             'type' => 'select',
             'title' => __('Agent Page - Select the Sidebar', 'wpresidence-core'),
             'subtitle' => __('Which sidebar to show in agent page type 1? Create new Sidebars from Appearance -≥ Sidebars.', 'wpresidence-core'),
             'data' => 'sidebars',
             'default' => 'primary-widget-area'
         ),
         
         array(
             'id' => 'wp_estate_agent_page_show_speciality_service',
             'type' => 'button_set',
             'title' => __('Show Specialties & Service Areas', 'wpresidence-core'),
             'subtitle' => __('Show/Hide Specialties & Service Areas', 'wpresidence-core'),
             'options' => array(
                 'yes' => __('yes', 'wpresidence-core'),
                 'no' => __('no', 'wpresidence-core')
             ),
             'default' => 'no',
         ),
         
         array(
             'id' => 'wp_estate_agent_page_show_speciality_service',
             'type' => 'button_set',
             'title' => __('Show Specialties & Service Areas', 'wpresidence-core'),
             'subtitle' => __('Show/Hide Specialties & Service Areas', 'wpresidence-core'),
             'options' => array(
                 'yes' => __('yes', 'wpresidence-core'),
                 'no' => __('no', 'wpresidence-core')
             ),
             'default' => 'yes',
         ),
         
         array(
             'id' => 'wp_estate_agent_page_show_my_listings',
             'type' => 'button_set',
             'title' => __('Show/Hide  My listings Areas', 'wpresidence-core'),
             'subtitle' => __('Show/Hide Specialties & Service Areas', 'wpresidence-core'),
             'options' => array(
                 'yes' => __('yes', 'wpresidence-core'),
                 'no' => __('no', 'wpresidence-core')
             ),
             'default' => 'yes',
         ),
         
        
     )
     ));
 
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Agent Card Settings', 'wpresidence-core'),
     'id' => 'agent_card_settingsddd_tab',
     'subsection' => true,
     'fields' => array(
        array(
             'id' => 'wp_estate_agent_unit_card',
             'type' => 'button_set',
             'title' => __('Agent Unit card', 'wpresidence-core'),
             'subtitle' => __('Agent Unit Card.', 'wpresidence-core'),
             'options' => array(
                 '1' => __('Type 1' ,'wpresidence-core'),
                 '2' => __('Type 2' ,'wpresidence-core'),
                 '3' => __('Type 3' ,'wpresidence-core'),
                 '4' => __('Type 4', 'wpresidence-core'),
             ),
             'default' => '1',
         ),
         
         
         array(
             'id' => 'wp_estate_agent_listings_per_row',
             'type' => 'button_set',
             'title' => __('No of agent listings per row when the page is without sidebar', 'wpresidence-core'),
             'subtitle' => __('When the page is with sidebar the no of listings per row will be 2 or 3 - depending on your selection', 'wpresidence-core'),
             'options' => array(
                 '3' => '3',
                 '4' => '4'
             ),
             'default' => '4',
         ),
 
 
         
     )));
 
     
 Redux::setSection($opt_name, array(
     'title' => __('Reviews Settings', 'wpresidence-core'),
     'id' => 'agent_reviews_settingsddd_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_reviews_block',
             'type' => 'select',
             'multi' => true,
             'class' => 'class_visible_user_role',
             'title' => __('Select User Types, where you want to show review block', 'wpresidence-core'),
             'subtitle' => __('*Hold CTRL for multiple selection.', 'wpresidence-core'),
             'options' => array(
                 'agent' => __('Agent', 'wpresidence-core'),
                 'agency' => __('Agency', 'wpresidence-core'),
                 'developer' => __('Developer', 'wpresidence-core')
             ),
             'default' => '',
         ),
 
         array(
             'id' => 'wp_estate_admin_approves_reviews',
             'type' => 'button_set',
             'title' => __('Admin Should approve the reviews', 'wpresidence-core'),
             'subtitle' => __('If yes, the reviews can be found in the comments section', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
 
     )));
 
  // -> START dashboard Selection
 
  Redux::setSection($opt_name, array(
     'title' => __(' User Dashboard', 'wpresidence-core'),
     'id' => 'user_settings_sidebar',
     'icon' => 'el el-user'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('User Dashboard Header', 'wpresidence-core'),
     'id' => 'wpestate_user_dashboard_header',
     'subsection' => true,
     'fields' => array(
 
 array(
     'id' => 'wp_estate_show_header_dashboard',
     'type' => 'button_set',
     'title' => __('Show Header in Dashboard?', 'wpresidence-core'),
     'subtitle' => __('Enable or disable header in dashboard. The header will always be wide & with Type1 as design', 'wpresidence-core'),
     'options' => array(
         'yes' => 'yes',
         'no' => 'no'
     ),
     'default' => 'yes',
 ),
 
 ),
 )); 
 
 Redux::setSection($opt_name, array(
     'title' => __('User Dashboard Colors', 'wpresidence-core'),
     'id' => 'wpestate_user_dashboard_design_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_user_dashboard_menu_color',
             'type' => 'color',
             'title' => __('User Dashboard Menu Color', 'wpresidence-core'),
             'subtitle' => __('User Dashboard Menu Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_user_dashboard_menu_hover_color',
             'type' => 'color',
             'title' => __('User Dashboard Menu Hover Color', 'wpresidence-core'),
             'subtitle' => __('User Dashboard Menu Hover Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_user_dashboard_menu_color_hover',
             'type' => 'color',
             'title' => __('User Dashboard Menu Item Background Color', 'wpresidence-core'),
             'subtitle' => __('User Dashboard Menu Item Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_user_dashboard_menu_back',
             'type' => 'color',
             'title' => __('User Dashboard Menu Background', 'wpresidence-core'),
             'subtitle' => __('User Dashboard Menu Background', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_user_dashboard_content_back',
             'type' => 'color',
             'title' => __('Content Background Color', 'wpresidence-core'),
             'subtitle' => __('Content Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_user_dashboard_content_color',
             'type' => 'color',
             'title' => __('Content Text Color', 'wpresidence-core'),
             'subtitle' => __('Content Text Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_user_dashboard_package_back',
             'type' => 'color',
             'title' => __('User Dashboard - Package Row Background', 'wpresidence-core'),
             'subtitle' => __('User Dashboard Package Row Background', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_user_dashboard_package_color',
             'type' => 'color',
             'title' => __('User Dashboard Package Color', 'wpresidence-core'),
             'subtitle' => __('User Dashboard Package Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_user_dashboard_buy_package',
             'type' => 'color',
             'title' => __('Dashboard Buy Package Select Background', 'wpresidence-core'),
             'subtitle' => __('Dashboard Package Selected', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_user_dashboard_package_select',
             'type' => 'color',
             'title' => __('Dashboard Package - Select Color', 'wpresidence-core'),
             'subtitle' => __('Dashboard Package Select Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_user_dashboard_content_button_back',
             'type' => 'color',
             'title' => __('Button Background Color', 'wpresidence-core'),
             'subtitle' => __('Button Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_hover_button_color',
             'type' => 'color',
             'title' => __('Hover Button Background Color', 'wpresidence-core'),
             'subtitle' => __('Hover Button Color', 'wpresidence-core'),
             'transparent' => false,
         ),
 
 
     ),
 ));    
 
 // -> START Blog Selection
 Redux::setSection($opt_name, array(
     'title' => __(' Blog', 'wpresidence-core'),
     'id' => 'blog_settings_sidebar',
     'icon' => 'el el-adjust'
 ));
 Redux::setSection($opt_name, array(
     'title' => __('Blog Card Settings', 'wpresidence-core'),
     'id' => 'blog_card_tab',
     'subsection' => true,
     'fields' => array(
 
         array(
             'id' => 'wp_estate_blog_unit_card',
             'type' => 'button_set',
             'title' => __('Blog List and Category/Archive - Select Card Design Type', 'wpresidence-core'),
             'subtitle' => __('The selection applies also to Recent Articles Slider and Recent Articles shortcodes!', 'wpresidence-core'),
             'options' => array(
                 '1' => __('Type 1' ,'wpresidence-core'),
                 '2' => __('Type 2' ,'wpresidence-core'),
                 '3' => __('Type 3' ,'wpresidence-core'),
                 '4' => __('Type 4', 'wpresidence-core'),
             ),
             'default' => '2',
         ),
         
         array(
             'id' => 'wp_estate_blog_listings_per_row',
             'type' => 'button_set',
             'title' => __('No of blog listings per row when the page is without sidebar', 'wpresidence-core'),
             'subtitle' => __('When the page is with sidebar the no of listings per row will be 2 or 3 - depending on your selection', 'wpresidence-core'),
             'options' => array(
                 '3' => '3',
                 '4' => '4'
             ),
             'default' => '4',
         ),
         
        //  array(
        //      'id' => 'wp_estate_similar_blog_post',
        //      'type' => 'button_set',
        //      'title' => __('No of similar blog posts', 'wpresidence-core'),
        //      'subtitle' => __('When the page is with sidebar the no of posts per row will be 2 or 3 - depending on your selection', 'wpresidence-core'),
        //      'options' => array(
        //          '3' => '3',
        //          '4' => '4'
        //      ),
        //      'default' => '3',
        //  ),
         
         
  
 )));

 Redux::setSection($opt_name, array(
     'title' => __('Similar Blog Posts', 'wpresidence-core'),
     'id' => 'similar_blog_posts_section_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_similar_blog_no',
             'type' => 'text',
             'title' => __('No of similar blog posts', 'wpresidence-core'),
            //  'subtitle' => __('Similar listings show when there are other properties from the same area, city, type and category.', 'wpresidence-core'),
             'default' => '4'
         ),
         array(
             'id' => 'wp_estate_similar_blog_per_row',
             'type' => 'button_set',
             'title' => __('No of similar blog posts per row when the page is without sidebar', 'wpresidence-core'),
             'subtitle' => __('When the page is with sidebar the no of blog posts per row will be 2 or 3 - depending on your selection', 'wpresidence-core'),
             'options' => array(
                 '3' => '3',
                 '4' => '4'
             ),
             'default' => '3',
         ),
         array(
             'id' => 'wp_estate_simialar_blog_taxes',
             'type' => 'select',
             'multi' => true,
             'title' => __('Select taxonomies for similar blog posts', 'wpresidence-core'),
             'subtitle' => __('Select taxonomies for similar blog posts', 'wpresidence-core'),
             'options' => array(
                 'category' => 'Categories',
                 'post_tag' => 'Tags',
             ),
             'default' => array('post_tag'),
         ),
         array(
             'id' => 'wp_estate_similar_blog_listins_order_by',
             'type' => 'select',
             'title' => __('Select Similar Blog Posts Order By', 'wpresidence-core'),
             'subtitle' => __('Select Similar Blog Posts Order Bt', 'wpresidence-core'),
             'options' => array(
                    'date' => __('Date', 'wpresidence-core'),
                    'title' => __('Title', 'wpresidence-core'),
                    'rand' => __('Random', 'wpresidence-core'),
             ),
             'default' => 'rand',
         ),
         array(
             'id' => 'wp_estate_similar_blog_listins_order',
             'type' => 'select',
             'title' => __('Select Similar Blog Posts Order By', 'wpresidence-core'),
             'subtitle' => __('Select Similar Blog Posts Order Bt', 'wpresidence-core'),
             'options' => array(
                    'asc' => __('Ascending', 'wpresidence-core'),
                    'desc' => __('Descending', 'wpresidence-core'),
             ),
             'default' => 'asc',
         ),
         
        // array(
        //      'id' => 'wp_estate_unit_md_similar',
        //      'type' => 'button_set',
        //      'title' => __('Similar blog posts display format', 'wpresidence-core'),
        //      'subtitle' => __('Chose the similar blog posts display format - grid or list ', 'wpresidence-core'),
        //      'options' => array(
        //          'grid' => 'grid',
        //          'list' => 'list'
        //      ),
        //      'default' => 'grid',
        //  ),
         
         
         
 )));
 
 
 
 
     // -> START Design Selection
 Redux::setSection($opt_name, array(
     'title' => __('Design', 'wpresidence-core'),
     'id' => 'design_settings_sidebar',
     'icon' => 'el el-brush'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('General Design Settings', 'wpresidence-core'),
     'id' => 'general_design_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_wide_status',
             'type' => 'button_set',
             'title' => __('Wide or Boxed?', 'wpresidence-core'),
             'subtitle' => __('Choose the theme layout: wide or boxed.', 'wpresidence-core'),
             'options' => array(
                 '1' => __('wide', 'wpresidence-core'),
                 '2' => __('boxed', 'wpresidence-core')
             ),
             'default' => '1'
         ),
         array(
             'id' => 'wp_estate_main_grid_content_width',
             'type' => 'text',
             'title' => __('Main Grid Width in px', 'wpresidence-core'),
             'subtitle' => __('This option defines the main content width. Default value is 1200px', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_main_content_width',
             'type' => 'text',
             'title' => __('Content Width (In Percent)', 'wpresidence-core'),
             'subtitle' => __('Using this option you can define the width of the content in percent.Sidebar will occupy the rest of the main content space.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_contentarea_internal_padding_top',
             'type' => 'text',
             'title' => __('Content Area Internal Padding Top', 'wpresidence-core'),
             'subtitle' => __('Content Area Internal Padding Top', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_contentarea_internal_padding_left',
             'type' => 'text',
             'title' => __('Content Area Internal Padding Left', 'wpresidence-core'),
             'subtitle' => __('Content Area Internal Padding Left', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_contentarea_internal_padding_bottom',
             'type' => 'text',
             'title' => __('Content Area Internal Padding Bottom', 'wpresidence-core'),
             'subtitle' => __('Content Area Internal Padding Bottom', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_contentarea_internal_padding_right',
             'type' => 'text',
             'title' => __('Content Area Internal Padding Right', 'wpresidence-core'),
             'subtitle' => __('Content Area Internal Padding Right', 'wpresidence-core'),
         ),
 
         array(
             'id' => 'wp_estate_show_breadcrumbs',
             'type' => 'button_set',
             'title' => __('Show Breadcrumbs', 'wpresidence-core'),
             'subtitle' => __('Show Breadcrumbs', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_border_radius_corner',
             'type' => 'text',
             'title' => __('Border Corner Radius', 'wpresidence-core'),
             'subtitle' => __('Border Corner Radius for unit elements, like property unit, agent unit or blog unit etc', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_cssbox_shadow',
             'type' => 'text',
             'title' => __('Box Shadow on elements like property unit', 'wpresidence-core'),
             'subtitle' => __('Box Shadow on elements like property unit. Type none for no shadow or put the css values like  0px 2px 0px 0px rgba(227, 228, 231, 1).', 'wpresidence-core'),
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Custom Colors', 'wpresidence-core'),
     'id' => 'custom_colors_tab',
     'desc' => __('***Please understand that we cannot add here color controls for all theme elements & details. Doing that will result in a overcrowded and useless interface. These small details need to be addressed via custom css code', 'wpresidence-core'),
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_main_color',
             'type' => 'color',
             'title' => __('Main Color', 'wpresidence-core'),
             'subtitle' => __('Main Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_second_color',
             'type' => 'color',
             'title' => __('Second Color', 'wpresidence-core'),
             'subtitle' => __('Second Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_background_color',
             'type' => 'color',
             'title' => __('Page Background Color', 'wpresidence-core'),
             'subtitle' => __('Changes the background color (default color is light grey).', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_content_back_color',
             'type' => 'color',
             'title' => __('Page Content Background Color', 'wpresidence-core'),
             'subtitle' => __('Changes the page content background color (default color is light grey)', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_content_area_back_color',
             'type' => 'color',
             'title' => __('Content Area Background Color', 'wpresidence-core'),
             'subtitle' => __('Changes the text sections background color (default color is white)', 'wpresidence-core'),
             'transparent' => false,
         ),
 
         array(
             'id' => 'wp_estate_breadcrumbs_font_color',
             'type' => 'color',
             'title' => __('Breadcrumbs Color', 'wpresidence-core'),
             'subtitle' => __('Breadcrumbs Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_font_color',
             'type' => 'color',
             'title' => __('Text Color', 'wpresidence-core'),
             'subtitle' => __('Text Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_link_color',
             'type' => 'color',
             'title' => __('Link Color', 'wpresidence-core'),
             'subtitle' => __('Link Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_headings_color',
             'type' => 'color',
             'title' => __('Headings Color', 'wpresidence-core'),
             'subtitle' => __('Headings Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_map_controls_back',
             'type' => 'color',
             'title' => __('Map Controls Background Color', 'wpresidence-core'),
             'subtitle' => __('Map Controls Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_map_controls_font_color',
             'type' => 'color',
             'title' => __('Map Controls Font Color', 'wpresidence-core'),
             'subtitle' => __('Map Controls Font Color', 'wpresidence-core'),
             'transparent' => false,
         ),
     ),
 ));
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Agent, Blog, Property Card Design & Colors', 'wpresidence-core'),
     'id' => 'property_list_design_tab',
     'subsection' => true,
     'fields' => array(
         
        
         array(
             'id' => 'wp_estate_propertyunit_internal_padding_top',
             'type' => 'text',
             'title' => __('Property, Agent and Blog Unit/Card Internal Padding Top', 'wpresidence-core'),
             'subtitle' => __('Property, Agent and Blog Unit/Card Internal Padding Top', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_propertyunit_internal_padding_left',
             'type' => 'text',
             'title' => __('Property, Agent and Blog Unit/Card Internal Padding Left', 'wpresidence-core'),
             'subtitle' => __('Property, Agent and Blog Unit/Card Internal Padding Left', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_propertyunit_internal_padding_bottom',
             'type' => 'text',
             'title' => __('Property, Agent and Blog Unit/Card Internal Padding Bottom', 'wpresidence-core'),
             'subtitle' => __('Property, Agent and Blog Unit/Card Internal Padding Bottom', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_propertyunit_internal_padding_right',
             'type' => 'text',
             'title' => __('Property, Agent and Blog Unit/Card Internal Padding Right', 'wpresidence-core'),
             'subtitle' => __('Property, Agent and Blog Unit/Card Internal Padding Right', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_property_unit_color',
             'type' => 'color',
             'title' => __('Property, Agent and Blog Unit/Card Backgrond Color', 'wpresidence-core'),
             'subtitle' => __('Property, Agent and Blog Unit/Card Backgrond Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_unit_border_size',
             'type' => 'text',
             'title' => __('Unit border size', 'wpresidence-core'),
             'subtitle' => __('Unit border size', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_unit_border_color',
             'type' => 'color',
             'title' => __('Unit/Card border color', 'wpresidence-core'),
             'subtitle' => __('Unit/Card border color', 'wpresidence-core'),
             'transparent' => false,
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Sidebar Widgets Design & Colors', 'wpresidence-core'),
     'id' => 'widget_design_elements_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_sidebarwidget_internal_padding_top',
             'type' => 'text',
             'title' => __('Sidebar Widget Internal Padding - Top', 'wpresidence-core'),
             'subtitle' => __('Add only numbers. Default value is 30.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_sidebarwidget_internal_padding_left',
             'type' => 'text',
             'title' => __('Sidebar Widget Internal Padding - Left', 'wpresidence-core'),
             'subtitle' => __('Add only numbers. Default value is 30.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_sidebarwidget_internal_padding_bottom',
             'type' => 'text',
             'title' => __('Sidebar Widget Internal Padding - Bottom', 'wpresidence-core'),
             'subtitle' => __('Add only numbers. Default value is 30.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_sidebarwidget_internal_padding_right',
             'type' => 'text',
             'title' => __('Sidebar Widget Internal Padding - Right', 'wpresidence-core'),
             'subtitle' => __('Add only numbers. Default value is 30.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_sidebar_widget_color',
             'type' => 'color',
             'title' => __('Sidebar Widget Background Color', 'wpresidence-core'),
             'subtitle' => __('Default color is white.', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_widget_sidebar_border_size',
             'type' => 'text',
             'title' => __('Sidebar Widget Border Size', 'wpresidence-core'),
             'subtitle' => __('Default border size is 1px.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_widget_sidebar_border_color',
             'type' => 'color',
             'title' => __('Sidebar Widget Border Color', 'wpresidence-core'),
             'subtitle' => __('Default border color is white.', 'wpresidence-core'),
             'transparent' => false,
         ),
     ),
 ));
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Fonts', 'wpresidence-core'),
     'id' => 'custom_fonts_tab',
     'subsection' => true,
     'class' => 'custom_fonts_tab',
     'fields' => array(
         array(
             'id' => 'h1_typo',
             'type' => 'typography',
             'title' => esc_html__('H1 Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
         array(
             'id' => 'h2_typo',
             'type' => 'typography',
             'title' => esc_html__('H2 Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
         array(
             'id' => 'h3_typo',
             'type' => 'typography',
             'title' => esc_html__('H3 Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
         array(
             'id' => 'h4_typo',
             'type' => 'typography',
             'title' => esc_html__('H4 Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
         array(
             'id' => 'h5_typo',
             'type' => 'typography',
             'title' => esc_html__('H5 Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
         array(
             'id' => 'h6_typo',
             'type' => 'typography',
             'title' => esc_html__('H6 Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
         array(
             'id' => 'paragraph_typo',
             'type' => 'typography',
             'title' => esc_html__('Paragraph Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
         array(
             'id' => 'menu_typo',
             'type' => 'typography',
             'title' => esc_html__('Menu Font', 'wpresidence-core'),
             'google' => true,
             'font-family' => true,
             'subsets' => true,
             'line-height' => true,
             'font-weight' => true,
             'font-backup' => false,
             'text-align' => false,
             'text-transform' => false,
             'font-style' => false,
             'color' => false,
             'units' => 'px',
             'subtitle' => esc_html__('Select your custom font options.', 'wpresidence-core'),
             'all_styles' => true
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Custom CSS', 'wpresidence-core'),
     'id' => 'custom_css_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_custom_css',
             'type' => 'ace_editor',
             'title' => __('Custom Css', 'wpresidence-core'),
             'subtitle' => __('Overwrite theme css using custom css.', 'wpresidence-core'),
             'mode' => 'css',
             'theme' => 'monokai',
         ),
     ),
 ));
 
 
 
 // -> START Advanced Selection
 Redux::setSection($opt_name, array(
     'title' => __('Email Management', 'wpresidence-core'),
     'id' => 'advanced_email_settings_sidebar',
     'icon' => 'el el-envelope el el-small'
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Emails Settings', 'wpresidence-core'),
     'id' => 'advanced_email_settings_section',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wpestate_email_type',
             'type' => 'button_set',
             'title' => __('Send emails as Html or text', 'wpresidence-core'),
             'subtitle' => __('Send emails as Html or text', 'wpresidence-core'),
             'options' => array(
                 'html' => 'html',
                 'text' => 'text'
             ),
             'default' => 'html'
         ),
         array(
             'id' => 'wp_estate_send_name_email_from',
             'type' => 'text',
             'title' => __('Emails will be sent from name?', 'wpresidence-core'),
             'subtitle' => __('Emails will use the from name set here', 'wpresidence-core'),
             'default' => 'noreply',
         ),
         array(
             'id' => 'wp_estate_send_email_from',
             'type' => 'text',
             'title' => __('Emails will be sent from email', 'wpresidence-core'),
             'subtitle' => __('Emails will use as sender email this address. If left blank, emails are sent from an address like noreply@yourdomain.com', 'wpresidence-core'),
             'default' => $siteurl,
         ),
         array(
             'id' => 'wpestate_display_header_email',
             'type' => 'button_set',
             'title' => __('Display Email Header ?', 'wpresidence-core'),
             'subtitle' => __('Display email header - the default header contains only the logo ', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         array(
             'id' => 'wp_estate_email_logo_image',
             'type' => 'media',
             'url' => true,
             'title' => __('Email Logo', 'wpresidence-core'),
             'subtitle' => __('Use the "Upload" button and "Insert into Post" button from the pop up window. Add a small logo. ', 'wpresidence-core'),
             'default' => array(
                 'url' => get_template_directory_uri() . '/img/logo.png'
             )
         ),
         array(
             'id' => 'wpestate_display_footer_email',
             'type' => 'button_set',
             'title' => __('Display Email footer?', 'wpresidence-core'),
             'subtitle' => __('Display email footer', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         array(
             'id' => 'wpestate_show_footer_email_address',
             'type' => 'button_set',
             'title' => __('Show Address in  email footer?', 'wpresidence-core'),
             'subtitle' => __('Show Address in  email footer?', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         array(
             'id' => 'wp_estate_email_footer_content',
             'type' => 'editor',
             'title' => __('Footer Content', 'wpresidence-core'),
             'subtitle' => __('Footer Content for email', 'wpresidence-core'),
             'default' => __('Please do not reply directly to this email. If you believe this is an error or require further assistance, please contact us', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_email_footer_social1',
             'type' => 'media',
             'url' => true,
             'title' => __('Social icon no 1', 'wpresidence-core'),
             'subtitle' => __('Upload social icon image', 'wpresidence-core'),
             'default' => array(
                 'url' => get_template_directory_uri() . '/templates/email_templates/images/facebook_email.png'
             )
         ),
         array(
             'id' => 'wp_estate_email_footer_social_link1',
             'type' => 'text',
             'title' => __('Link social accont no 1 ?', 'wpresidence-core'),
             'subtitle' => __('Link for social accont no 1 ', 'wpresidence-core'),
             'default' => '#'
         ),
         array(
             'id' => 'wp_estate_email_footer_social2',
             'type' => 'media',
             'url' => true,
             'title' => __('Social icon no 2', 'wpresidence-core'),
             'subtitle' => __('Upload social icon image', 'wpresidence-core'),
             'default' => array(
                 'url' => get_template_directory_uri() . '/templates/email_templates/images/twitter-email.png'
             )
         ),
         array(
             'id' => 'wp_estate_email_footer_social_link2',
             'type' => 'text',
             'title' => __('Link social accont no 2 ?', 'wpresidence-core'),
             'subtitle' => __('Link for social accont no 2 ', 'wpresidence-core'),
             'default' => '#'
         ),
         array(
             'id' => 'wp_estate_email_footer_social3',
             'type' => 'media',
             'url' => true,
             'title' => __('Social icon no 3', 'wpresidence-core'),
             'subtitle' => __('Upload social icon image', 'wpresidence-core'),
             'default' => array(
                 'url' => get_template_directory_uri() . '/templates/email_templates/images/linkedin-email.png'
             )
         ),
         array(
             'id' => 'wp_estate_email_footer_social_link3',
             'type' => 'text',
             'title' => __('Link social accont no 3 ?', 'wpresidence-core'),
             'subtitle' => __('Link for social accont no 3 ', 'wpresidence-core'),
             'default' => '#'
         ),
         array(
             'id' => 'wp_estate_email_background',
             'type' => 'color',
             'title' => __('Email Background Color', 'wpresidence-core'),
             'subtitle' => __('Email Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_email_content_background',
             'type' => 'color',
             'title' => __('Email Content Background Color', 'wpresidence-core'),
             'subtitle' => __('Email Content Background Color', 'wpresidence-core'),
             'transparent' => false,
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Emails Content', 'wpresidence-core'),
     'id' => 'email_management_tab',
     'desc' => __('Leave "Subject" blank for the email notifications you don\'t wish to send. Global variables: %website_url as website url,%website_name as website name, %user_email as user_email, %username as username', 'wpresidence-core'),
     'subsection' => true,
     'class' => 'email_management_class',
     'fields' => array(
         array(
             'id' => 'wp_estate_subject_new_user',
             'type' => 'text',
             'title' => __('Subject for New user notification', 'wpresidence-core'),
             'subtitle' => __('Email subject for New user notification', 'wpresidence-core'),
             'default' => __('Your username and password on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_new_user',
             'type' => 'editor',
             'title' => __('Content for New user notification', 'wpresidence-core'),
             'subtitle' => __('Email content for New user notification', 'wpresidence-core'),
             'default' => __('Hi there,
 Welcome to %website_url! You can login now using the below credentials:
 Username:%user_login_register
 Password: %user_pass_register
 If you have any problems, please contact me.
 Thank you!', 'wpresidence-core'),
             'desc' => esc_html__('%user_login_register as new username, %user_pass_register as user password, %user_email_register as new user email,use text mode and <br class=""> tag for new line, <p class=""> for a new paragraph,
 <span class=""> for styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_admin_new_user',
             'type' => 'text',
             'title' => __('Subject for New user admin notification', 'wpresidence-core'),
             'subtitle' => __('Email subject for New user admin notification', 'wpresidence-core'),
             'default' => __('New User Registration', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_admin_new_user',
             'type' => 'editor',
             'title' => __('Content for New user admin notification', 'wpresidence-core'),
             'subtitle' => __('Email content for New user admin notification', 'wpresidence-core'),
             'default' => __('New user registration on %website_url.
 Username: %user_login_register,
 E-mail: %user_email_register', 'wpresidence-core'),
             'desc' => esc_html__('%user_login_register as new username and %user_email_register as new user email, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_purchase_activated',
             'type' => 'text',
             'title' => __('Subject for Purchase Activated', 'wpresidence-core'),
             'subtitle' => __('Email subject for Purchase Activated', 'wpresidence-core'),
             'default' => __('Your purchase was activated', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_purchase_activated',
             'type' => 'editor',
             'title' => __('Content for Purchase Activated', 'wpresidence-core'),
             'subtitle' => __('Email content for Purchase Activated', 'wpresidence-core'),
             'default' => __('Hi there,
 Your purchase on  %website_url is activated! You should go check it out.', 'wpresidence-core'),
             'desc' => esc_html__('%user_login_register as new username and %user_email_register as new user email, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_password_reset_request',
             'type' => 'text',
             'title' => __('Subject for Password Reset Request', 'wpresidence-core'),
             'subtitle' => __('Email subject for Password Reset Request', 'wpresidence-core'),
             'default' => __('Password Reset Request', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_password_reset_request',
             'type' => 'editor',
             'title' => __('Content for Password Reset Request', 'wpresidence-core'),
             'subtitle' => __('Email content for Password Reset Request', 'wpresidence-core'),
             'default' => __('Someone requested that the password be reset for the following account:
 %website_url
 Username: %username.
 If this was a mistake, just ignore this email and nothing will happen. To reset your password, visit the following address:%reset_link,
 Thank You!', 'wpresidence-core'),
             'desc' => esc_html__('%reset_link as reset link, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_password_reseted',
             'type' => 'text',
             'title' => __('Subject for Password Reseted', 'wpresidence-core'),
             'subtitle' => __('Email subject for Password Reset Request', 'wpresidence-core'),
             'default' => __('Your Password was Reset', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_password_reseted',
             'type' => 'editor',
             'title' => __('Content for Password Reseted', 'wpresidence-core'),
             'subtitle' => __('Email content for Password Reseted', 'wpresidence-core'),
             'default' => __('Your new password for the account at: %website_url:
 Username:%username,
 Password:%user_pass.
 You can now login with your new password at: %website_url', 'wpresidence-core'),
             'desc' => esc_html__('%user_pass as user password, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_agent_review',
             'type' => 'text',
             'title' => __('Subject for Agent Review', 'wpresidence-core'),
             'subtitle' => __('Email subject for Agent Review Posted', 'wpresidence-core'),
             'default' => __('A new agent review was received.', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_agent_review',
             'type' => 'editor',
             'title' => __('Content for Agent Review Received', 'wpresidence-core'),
             'subtitle' => __('Email content for Agent Review Received', 'wpresidence-core'),
             'default' => __('You received a new agent review on : %website_url:
 The review is for agent %agent_name and was posted by %user_post.', 'wpresidence-core'),
             'desc' => esc_html__('%agent_name as agent name,%user_post for the username (the one who post), use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_purchase_activated',
             'type' => 'text',
             'title' => __('Subject for Purchase Activated', 'wpresidence-core'),
             'subtitle' => __('Email subject for Purchase Activated', 'wpresidence-core'),
             'default' => __('Your purchase was activated', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_purchase_activated',
             'type' => 'editor',
             'title' => __('Content for Purchase Activated', 'wpresidence-core'),
             'subtitle' => __('Email content for Purchase Activated', 'wpresidence-core'),
             'default' => __('Hi there,
 Your purchase on  %website_url is activated! You should go check it out.', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_approved_listing',
             'type' => 'text',
             'title' => __('Subject for Approved Listings', 'wpresidence-core'),
             'subtitle' => __('Email subject for Approved Listings', 'wpresidence-core'),
             'default' => __('Your listing was approved', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_approved_listing',
             'type' => 'editor',
             'title' => __('Content for Approved Listings', 'wpresidence-core'),
             'subtitle' => __('Email content for Approved Listings', 'wpresidence-core'),
             'default' => __('Hi there,
 Your listing, %property_title was approved on  %website_url! The listing is: %property_url.
 You should go check it out.', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %post_id as listing id, %property_url as property url and %property_title as property name, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_approved_user',
             'type' => 'text',
             'title' => __('Subject for Approved User', 'wpresidence-core'),
             'subtitle' => __('Email subject for Approved User', 'wpresidence-core'),
             'default' => __('Your account was approved', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_approved_user',
             'type' => 'editor',
             'title' => __('Content for Approved User', 'wpresidence-core'),
             'subtitle' => __('Email content for Approved User', 'wpresidence-core'),
             'default' => __('Hi there,
 Your account was approved on  %website_url! You can now log in.', 'wpresidence-core'),
            //  'desc' => esc_html__('* you can use %post_id as listing id, %property_url as property url and %property_title as property name, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_new_wire_transfer',
             'type' => 'text',
             'title' => __('Subject for New wire Transfer', 'wpresidence-core'),
             'subtitle' => __('Email subject for New wire Transfer', 'wpresidence-core'),
             'default' => __('You ordered a new Wire Transfer', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_new_wire_transfer',
             'type' => 'editor',
             'title' => __('Content for New wire Transfer', 'wpresidence-core'),
             'subtitle' => __('Email content for New wire Transfer', 'wpresidence-core'),
             'default' => __('We received your Wire Transfer payment request on  %website_url !
 Please follow the instructions below in order to start submitting properties as soon as possible.
 The invoice number is: %invoice_no, Amount: %total_price.
 Instructions:  %payment_details.', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %invoice_no as invoice number, %total_price as $totalprice and %payment_details as $payment_details, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_admin_new_wire_transfer',
             'type' => 'text',
             'title' => __('Subject for Admin - New wire Transfer', 'wpresidence-core'),
             'subtitle' => __('Email subject for Admin - New wire Transfer', 'wpresidence-core'),
             'default' => __('Somebody ordered a new Wire Transfer', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_admin_new_wire_transfer',
             'type' => 'editor',
             'title' => __('Content for Admin - New wire Transfer', 'wpresidence-core'),
             'subtitle' => __('Email content for Admin - New wire Transfer', 'wpresidence-core'),
             'default' => __('Hi there,
 You received a new Wire Transfer payment request on %website_url.
 The invoice number is:  %invoice_no,  Amount: %total_price.
 Please wait until the payment is made to activate the user purchase.', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %invoice_no as invoice number, %total_price as $totalprice and %payment_details as $payment_details, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_admin_expired_listing',
             'type' => 'text',
             'title' => __('Subject for Admin - Expired Listing', 'wpresidence-core'),
             'subtitle' => __('Email subject for Admin - Expired Listing', 'wpresidence-core'),
             'default' => __('Expired Listing sent for approval on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_admin_expired_listing',
             'type' => 'editor',
             'title' => __('Content for Admin - Expired Listing', 'wpresidence-core'),
             'subtitle' => __('Email content for Admin - Expired Listing', 'wpresidence-core'),
             'default' => __('Hi there,
 A user has re-submited a new property on %website_url! You should go check it out.
 This is the property title: %submission_title.', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %submission_title as property title number, %submission_url as property submission url, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_matching_submissions',
             'type' => 'text',
             'title' => __('Subject for Matching Submissions', 'wpresidence-core'),
             'subtitle' => __('Email subject for Matching Submissions', 'wpresidence-core'),
             'default' => __('Matching Submissions on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_matching_submissions',
             'type' => 'editor',
             'title' => __('Content for Matching Submissions', 'wpresidence-core'),
             'subtitle' => __('Email content for Matching Submissions', 'wpresidence-core'),
             'default' => __('Hi there,
 A new submission matching your chosen criteria has been published at %website_url.
 These are the new submissions:
 %matching_submissions
 If you do not wish to be notified anymore please enter your account and delete the saved search.Thank you!', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %matching_submissions as matching submissions list, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_paid_submissions',
             'type' => 'text',
             'title' => __('Subject for Paid Submission', 'wpresidence-core'),
             'subtitle' => __('Email subject for Paid Submission', 'wpresidence-core'),
             'default' => __('New Paid Submission on %website_url', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_paid_submissions',
             'type' => 'editor',
             'title' => __('Content for Paid Submission', 'wpresidence-core'),
             'subtitle' => __('Email content for Paid Submission', 'wpresidence-core'),
             'default' => __('Hi there,
 You have a new paid submission on  %website_url! You should go check it out.', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_featured_submission',
             'type' => 'text',
             'title' => __('Subject for Featured Submission', 'wpresidence-core'),
             'subtitle' => __('Email subject for Featured Submission', 'wpresidence-core'),
             'default' => __('New Feature Upgrade on  %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_featured_submission',
             'type' => 'editor',
             'title' => __('Content for Featured Submission', 'wpresidence-core'),
             'subtitle' => __('Email content for Featured Submission', 'wpresidence-core'),
             'default' => __('Hi there,
 You have a new featured submission on  %website_url! You should go check it out.', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_account_downgraded',
             'type' => 'text',
             'title' => __('Subject for Account Downgraded', 'wpresidence-core'),
             'subtitle' => __('Email subject for Account Downgraded', 'wpresidence-core'),
             'default' => __('Account Downgraded on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_account_downgraded',
             'type' => 'editor',
             'title' => __('Content for Account Downgraded', 'wpresidence-core'),
             'subtitle' => __('Email content for Account Downgraded', 'wpresidence-core'),
             'default' => __('Hi there,
 You downgraded your subscription on %website_url. Because your listings number was greater than what the actual package offers, we set the status of all your listings to "expired". You will need to choose which listings you want live and send them again for approval.
 Thank you!', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_membership_cancelled',
             'type' => 'text',
             'title' => __('Subject for Membership Cancelled', 'wpresidence-core'),
             'subtitle' => __('Email subject for Membership Cancelled', 'wpresidence-core'),
             'default' => __('Membership Cancelled on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_membership_cancelled',
             'type' => 'editor',
             'title' => __('Content for Membership Cancelled', 'wpresidence-core'),
             'subtitle' => __('Email content for Membership Cancelled', 'wpresidence-core'),
             'default' => __('Hi there,
 Your subscription on %website_url was cancelled because it expired or the recurring payment from the merchant was not processed. All your listings are no longer visible for our visitors but remain in your account.
 Thank you.', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_downgrade_warning',
             'type' => 'text',
             'title' => __('Subject for Membership Expiration Warning', 'wpresidence-core'),
             'subtitle' => __('Email subject for Membership Expiration Warning', 'wpresidence-core'),
             'default' => __('Membership Expiration Warning on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_downgrade_warning',
             'type' => 'editor',
             'title' => __('Content for Membership Expiration Warning', 'wpresidence-core'),
             'subtitle' => __('Email content for Membership Expiration Warning', 'wpresidence-core'),
             'default' => __('Hi there,
 Your subscription on %website_url will expire in 3 days.Please make sure you renew your subscription or you have enough funds for an auto renew.', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_free_listing_expired',
             'type' => 'text',
             'title' => __('Subject for Free Listing Expired', 'wpresidence-core'),
             'subtitle' => __('Email subject for Free Listing Expired', 'wpresidence-core'),
             'default' => __('Free Listing expired on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_free_listing_expired',
             'type' => 'editor',
             'title' => __('Content for Free Listing Expired', 'wpresidence-core'),
             'subtitle' => __('Email content for Free Listing Expired', 'wpresidence-core'),
             'default' => __('Hi there,
 One of your free listings on  %website_url has "expired". The listing is %expired_listing_url.
 Thank you!', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %expired_listing_url as expired listing url and %expired_listing_name as expired listing name, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_new_listing_submission',
             'type' => 'text',
             'title' => __('Subject for New Listing Submission', 'wpresidence-core'),
             'subtitle' => __('Email subject for New Listing Submission', 'wpresidence-core'),
             'default' => __('New Listing Submission on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_new_listing_submission',
             'type' => 'editor',
             'title' => __('Content for New Listing Submission', 'wpresidence-core'),
             'subtitle' => __('Email content for New Listing Submission', 'wpresidence-core'),
             'default' => __('Hi there,
 A user has submited a new property on %website_url! You should go check it out.This is the property title %new_listing_title!', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %new_listing_title as new listing title and %new_listing_url as new listing url, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_listing_edit',
             'type' => 'text',
             'title' => __('Subject for Listing Edit', 'wpresidence-core'),
             'subtitle' => __('Email subject for Listing Edit', 'wpresidence-core'),
             'default' => __('Listing Edited on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_listing_edit',
             'type' => 'editor',
             'title' => __('Content for Listing Edit', 'wpresidence-core'),
             'subtitle' => __('Email content for Listing Edit', 'wpresidence-core'),
             'default' => __('Hi there,
 A user has edited one of his listings  on %website_url ! You should go check it out. The property name is : %editing_listing_title!', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %editing_listing_title as editing listing title and %editing_listing_url as editing listing url, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_recurring_payment',
             'type' => 'text',
             'title' => __('Subject for Recurring Payment', 'wpresidence-core'),
             'subtitle' => __('Email subject for Recurring Payment', 'wpresidence-core'),
             'default' => __('Recurring Payment on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_recurring_payment',
             'type' => 'editor',
             'title' => __('Content for Recurring Payment', 'wpresidence-core'),
             'subtitle' => __('Email content for Recurring Payment', 'wpresidence-core'),
             'default' => __('Hi there,
 We charged your account on %merchant for a subscription on %website_url ! You should go check it out.', 'wpresidence-core'),
             'desc' => esc_html__('* you can use %recurring_pack_name as recurring packacge name and %merchant as merchant name, use text mode and <p style=""> or <span style=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_membership_activated',
             'type' => 'text',
             'title' => __('Subject for Membership Activated', 'wpresidence-core'),
             'subtitle' => __('Email subject for Membership Activated', 'wpresidence-core'),
             'default' => __('Membership Activated on %website_url', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_membership_activated',
             'type' => 'editor',
             'title' => __('Content for Membership Activated', 'wpresidence-core'),
             'subtitle' => __('Email content for Membership Activated', 'wpresidence-core'),
             'default' => __('Hi there,
 Your new membership on %website_url is activated! You should go check it out.', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_subject_agent_update_profile',
             'type' => 'text',
             'title' => __('Subject for Update Profil', 'wpresidence-core'),
             'subtitle' => __('Email subject for Update Profile', 'wpresidence-core'),
             'default' => __('Profile Update', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_agent_update_profile',
             'type' => 'editor',
             'title' => __('Content for Update Profile', 'wpresidence-core'),
             'subtitle' => __('Email content for Update Profile', 'wpresidence-core'),
             'default' => __('A user updated his profile on %website_url.Username: %user_profile', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),    
         array(
             'id' => 'wp_estate_subject_agent_added',
             'type' => 'text',
             'title' => __('Subject for New Agent', 'wpresidence-core'),
             'subtitle' => __('Email subject for New Agent', 'wpresidence-core'),
             'default' => __('New Agent Added', 'wpresidence-core')
         ),
         array(
             'id' => 'wp_estate_agent_added',
             'type' => 'editor',
             'title' => __('Content for New Agent', 'wpresidence-core'),
             'subtitle' => __('Email content for New Agent', 'wpresidence-core'),
             'default' => __('A new agent was added on %website_url.
 Username: %user_profile', 'wpresidence-core'),
             'desc' => esc_html__('use text mode and <p style=""> or <span class=""> for a new paragraph and styling.', 'wpresidence-core'),
         ),
         array(
                 'id'       => 'wp_estate_subject_payment_action_required',
                 'type'     => 'text',
                 'title'    => __( 'Subject for Payment Action Required' , 'wpresidence-core'),
                 'subtitle' => __( 'Email subject for  Payment Action Required (on 3ds recurring payments via Stripe) ', 'wpresidence-core' ),
                 'default'  => __('Payment Action Required on %website_url', 'wpresidence-core')
         ),
             array(
                 'id'       => 'wp_estate_payment_action_required',
                 'type'     => 'editor',
                 'title'    => __( 'Content for Payment Action Required', 'wpresidence-core' ),
                 'subtitle' => __( 'Email content for  Payment Action Required (on 3ds recurring payments via Stripe) ', 'wpresidence-core' ),
                 'default'  => __('Hi there,
                                 One of your subscription payments on %website_url  requires manual confirmation. Please go to your dashboard and approve the payment. ','wpresidence-core'),
                 'desc'     => esc_html__('','wpresidence-core' ),
             ),
         
     ),
 ));
 
 
 
 
 
 
 
 
 
 
 
 
 // -> START Membership Selection
 Redux::setSection($opt_name, array(
     'title' => __('Submission & Membership', 'wpresidence-core'),
     'id' => 'membership_settings_sidebar',
     'icon' => 'el el-upload'
 ));
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_select/' );
 Redux::setSection($opt_name, array(
     'title' => __('Property Submission Page', 'wpresidence-core'),
     'id' => 'property_submission_page_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_enable_autocomplete',
             'type' => 'button_set',
             'title' => __('Enable Location Autocomplete in Front End Submission Form', 'wpresidence-core'),
             'subtitle' => __('If yes, the address field in front end submission form uses Google Places or Open Street autocomplete, a choice you make in Map Settings. ', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_submission_page_fields',
             'type' => 'wpestate_select',
             'multi' => true,
             'title' => __('Select the Fields for listing submission.', 'wpresidence-core'),
             'subtitle' => __('Use CTRL to select multiple fields for listing submission page.', 'wpresidence-core'),
             'options' => wpestate_return_all_fields(),
             'default' => wpestate_return_all_fields(),
         ),
         array(
             'id' => 'wp_estate_mandatory_page_fields',
             'type' => 'wpestate_select',
             'multi' => true,
             'title' => __('Select the Mandatory Fields for listing submission.', 'wpresidence-core'),
             'subtitle' => __('Make sure the mandatory fields for listing submission page are part of submit form (managed from the above setting). Use CTRL for multiple fields select. *Title is always mandatory!', 'wpresidence-core'),
             'options' => array(),
         ),
 
         
         array(
             'id' => 'wp_estate_prop_image_number',
             'type' => 'text',
             'title' => __('Maximum no of images per property (only front-end upload). Works when submission is free or requires user to pay a fee for each listing', 'wpresidence-core'),
             'subtitle' => __('The maximum no of images a user can upload with the front end submit form. Use 0 for unlimited. This value is NOT used in case of membership mode (each package will have its own max image no set)', 'wpresidence-core'),
             'default' => '12'
         ),
 
 
         array(
             'id' => 'wp_estate_admin_submission',
             'type' => 'button_set',
             'title' => __('Submitted Listings should be approved by admin?', 'wpresidence-core'),
             'subtitle' => __('If yes, admin publishes each property submitted in front end manually.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'yes',
         ),
     ),
 ));
 
 $list_currency = array(
     'USD' => 'USD',
     'EUR' => 'EUR',
     'AUD' => 'AUD',
     'BRL' => 'BRL',
     'CAD' => 'CAD',
     'CZK' => 'CZK',
     'DKK' => 'DKK',
     'GTQ' => 'GTQ',
     'HKD' => 'HKD',
     'HUF' => 'HUF',
     'ILS' => 'ILS',
     'INR' => 'INR',
     'JPY' => 'JPY',
     'KRW'=>  'KRW',
     'MYR' => 'MYR',
     'MXN' => 'MXN',
     'NOK' => 'NOK',
     'NZD' => 'NZD',
     'NGN' => 'NGN',
     'PHP' => 'PHP',
     'PLN' => 'PLN',
     'GBP' => 'GBP',
     'SGD' => 'SGD',
     'SEK' => 'SEK',
     'CHF' => 'CHF',
     'TWD' => 'TWD',
     'THB' => 'THB',
     'TRY' => 'TRY',
     'EGP' => 'EGP',
     'XOF' => 'XOF'
 );
 $custom_field = Redux::get_option($opt_name, 'wp_estate_submission_curency_custom');
 if ($custom_field != '') {
     $list_currency[$custom_field] = $custom_field;
 }
 
 Redux::setSection($opt_name, array(
     'title' => __('Membership Settings', 'wpresidence-core'),
     'id' => 'membership_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_paid_submission',
             'type' => 'button_set',
             'title' => __('Enable Paid Submission?', 'wpresidence-core'),
             'subtitle' => __('No = submission is free. Paid listing = submission requires user to pay a fee for each listing. Membership = submission is based on user membership package.', 'wpresidence-core'),
             'options' => array(
                 'no' => 'no',
                 'per listing' => 'per listing',
                 'membership' => 'membership'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_free_mem_list',
             'type' => 'text',
             'required' => array('wp_estate_paid_submission', '=', 'membership'),
             'title' => __(' Free Membership - no of free listings for new users', 'wpresidence-core'),
             'subtitle' => __('If you change this value, the new value applies for new registered users. Old value applies for older registered accounts.', 'wpresidence-core'),
             'default' => '0'
         ),
         array(
             'id' => 'wp_estate_free_mem_list_unl',
             'required' => array('wp_estate_paid_submission', '=', 'membership'),
             'type' => 'checkbox',
             'title' => __('Free Membership - Offer unlimited listings for new users', 'wpresidence-core'),
             'default' => '1'// 1 = on | 0 = off
         ),
         array(
             'id' => 'wp_estate_free_feat_list',
             'required' => array('wp_estate_paid_submission', '=', 'membership'),
             'type' => 'text',
             'title' => __('Free Membership - no of featured listings (for "membership" mode)', 'wpresidence-core'),
             'subtitle' => __('If you change this value, the new value applies for new registered users. Old value applies for older registered accounts.', 'wpresidence-core'),
             'default' => '0'
         ),
         array(
             'id' => 'wp_estate_free_feat_list_expiration',
             'required' => array('wp_estate_paid_submission', '=', 'membership'),
             'type' => 'text',
             'title' => __('Free Days for Each Free Listing - no of days until a free listing will expire. *Starts from the moment the listing is published on the website. (for "membership" mode only)', 'wpresidence-core'),
             'subtitle' => __('Option applies for each free published listing.', 'wpresidence-core'),
             'default' => '0'
         ),
         array(
             'id' => 'wp_estate_free_pack_image_included',
             'required' => array('wp_estate_paid_submission', '=', 'membership'),
             'type' => 'text',
             'title' => __('Free Membership Images - no of images per listing', 'wpresidence-core'),
             'subtitle' => __('Option applies for each free published listing.', 'wpresidence-core'),
             'default' => '0'
         ),
         array(
             'id' => 'wp_estate_downgraded_to_free_values',
             'type' => 'button_set',
             'required' => array('wp_estate_paid_submission', '=', 'membership'),
             'title' => __('Downgraded to the free package values ', 'wpresidence-core'),
             'subtitle' => __('When downgraded to free - how many listings should be available', 'wpresidence-core'),
             'options' => array(
                 'free' => 'free package no of listings',
                 'none' => 'none',
             ),
             'default' => 'sandbox',
         ),
         
         
         array(
             'id' => 'wp_estate_price_submission',
             'type' => 'text',
             'required' => array('wp_estate_paid_submission', '=', 'per listing'),
             'title' => __('Price Per Submission (for "per listing" mode)', 'wpresidence-core'),
             'subtitle' => __('Use .00 format for decimals (ex: 5.50). Do not set price as 0!', 'wpresidence-core'),
             'default' => '0'
         ),
         array(
             'id' => 'wp_estate_price_featured_submission',
             'type' => 'text',
             'required' => array('wp_estate_paid_submission', '=', 'per listing'),
             'title' => __('Price to make the listing featured (for "per listing" mode)', 'wpresidence-core'),
             'subtitle' => __('Use .00 format for decimals (ex: 1.50). Do not set price as 0!', 'wpresidence-core'),
             'default' => '0'
         ),
 
         array(
             'id' => 'wp_estate_paypal_api',
             'type' => 'button_set',
             'required' => array('wp_estate_paid_submission', '!=', 'no'),
             'title' => __('Paypal & Stripe Api Type', 'wpresidence-core'),
             'subtitle' => __('Sandbox = test API. LIVE = real payments API. Update PayPal and Stripe settings according to API type selection.', 'wpresidence-core'),
             'options' => array(
                 'sandbox' => 'sandbox',
                 'live' => 'live',
             ),
             'default' => 'sandbox',
         ),
         array(
             'id' => 'wp_estate_submission_curency',
             'type' => 'button_set',
             'required' => array('wp_estate_paid_submission', '!=', 'no'),
             'title' => __('Currency For Paid Submission', 'wpresidence-core'),
             'subtitle' => __('The currency in which payments are processed.', 'wpresidence-core'),
             'options' => $list_currency,
             'default' => 'USD',
         ),
 
         array(
             'id' => 'wp_estate_submission_curency_custom',
             'type' => 'text',
             'required' => array('wp_estate_enable_direct_pay', '=', 'yes'),
             'title' => __('Custom Currency Symbol - *select it from the list above after you add it.', 'wpresidence-core'),
             'subtitle' => __('Add your own currency for Wire payments.', 'wpresidence-core'),
         ),
 
         array(
             'id' => 'wp_estate_invoice_extra_details_print',
             'type' => 'editor',
             'title' => __('Extra details to add on printed invoice.', 'wpresidence-core'),
             'subtitle' => __('Extra details to add on printed invoice.', 'wpresidence-core'),
             'validate' => 'html_custom',
             'allowed_html' => array(
                 'a' => array(
                     'href' => array(),
                     'title' => array()
                 ),
                 'br' => array(),
                 'em' => array(),
                 'strong' => array()
             )
         ),
     ),
 ));
 
 
 Redux::setSection($opt_name, array(
     'title' => __('PayPal API', 'wpresidence-core'),
     'id' => 'paypal_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_enable_paypal',
             'type' => 'button_set',
             'title' => __('Enable Paypal', 'wpresidence-core'),
             'subtitle' => __('You can enable or disable PayPal buttons.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
         array(
             'id' => 'wp_estate_paypal_client_id',
             'type' => 'text',
             'required' => array('wp_estate_enable_paypal', '=', 'yes'),
             'title' => __('Paypal Client id', 'wpresidence-core'),
             'subtitle' => __('PayPal business account is required. Info is taken from https://developer.paypal.com/. See help: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/paypal-set-up/" target="_blank">http://help.wpresidence.net/article/paypal-set-up/</a>',
         ),
         array(
             'id' => 'wp_estate_paypal_client_secret',
             'type' => 'text',
             'required' => array('wp_estate_enable_paypal', '=', 'yes'),
             'title' => __('Paypal Client Secret Key', 'wpresidence-core'),
             'subtitle' => __('Info is taken from https://developer.paypal.com/', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_paypal_rec_email',
             'type' => 'text',
             'required' => array('wp_estate_enable_paypal', '=', 'yes'),
             'title' => __('Paypal receiving email', 'wpresidence-core'),
             'subtitle' => __('Info is taken from https://www.paypal.com/ or http://sandbox.paypal.com/', 'wpresidence-core'),
         ),
     ),
 ));
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Stripe API', 'wpresidence-core'),
     'id' => 'stripe_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_enable_stripe',
             'type' => 'button_set',
             'title' => __('Enable Stripe', 'wpresidence-core'),
             'subtitle' => __('You can enable or disable Stripe button.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
         array(
             'id' => 'wp_estate_stripe_secret_key',
             'required' => array('wp_estate_enable_stripe', '=', 'yes'),
             'type' => 'text',
             'title' => __('Stripe Secret Key', 'wpresidence-core'),
             'subtitle' => __('Info is taken from your account at https://dashboard.stripe.com/login See help: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/stripe-set-up/" target="_blank">http://help.wpresidence.net/article/stripe-set-up/</a>',
         ),
         array(
             'id' => 'wp_estate_stripe_publishable_key',
             'required' => array('wp_estate_enable_stripe', '=', 'yes'),
             'type' => 'text',
             'title' => __('Stripe Publishable Key', 'wpresidence-core'),
             'subtitle' => __('Info is taken from your account at https://dashboard.stripe.com/login See help: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/stripe-set-up/" target="_blank">http://help.wpresidence.net/article/stripe-set-up/</a>',
         ),
         array(
             'id' => 'wp_estate_stripe_webhook',
             'required' => array('wp_estate_enable_stripe', '=', 'yes'),
             'type' => 'text',
             'title' => __('Stripe Webhook Secret Key', 'wpresidence-core'),
             'subtitle' => __('Info is taken from your account at https://dashboard.stripe.com/login See help: ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/stripe-set-up/" target="_blank">http://help.wpresidence.net/article/stripe-set-up/</a>',
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Wire Transfer', 'wpresidence-core'),
     'id' => 'wire_transfer_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_enable_direct_pay',
             'type' => 'button_set',
             'required' => array('wp_estate_paid_submission', '!=', 'no'),
             'title' => __('Enable Direct Payment / Wire Payment?', 'wpresidence-core'),
             'subtitle' => __('Enable or disable the wire payment option.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'no',
         ),
 
         array(
             'id' => 'wp_estate_direct_payment_details',
             'type' => 'editor',
             'required' => array('wp_estate_enable_direct_pay', '=', 'yes'),
             'title' => __('Wire instructions for direct payment', 'wpresidence-core'),
             'subtitle' => __('If wire payment is enabled, type the instructions below.', 'wpresidence-core'),
             'validate' => 'html_custom',
             'allowed_html' => array(
                 'a' => array(
                     'href' => array(),
                     'title' => array()
                 ),
                 'br' => array(),
                 'em' => array(),
                 'strong' => array()
             ),
         ),
     ),
     ));    
 
 Redux::setSection($opt_name, array(
     'title' => __('WooCommerce Settings', 'wpresidence-core'),
     'id' => 'woo_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_enable_woo_mes',
             'type' => 'info',
             'desc' => __('You need WooCommerce Plugin Installed and Active & and a WooCommerce Merchant Enabled. <a href="http://help.wpresidence.net/article/install-woocommerce-and-use-woocommerce-payments/" target="_blank">See help page</a>.
 Payments are considerd complete once the Order for a particular items has the status "Processing or Complete " .</br>
 WooCommerce does not suport recurring payments for Membership Submission.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_enable_woo',
             'type' => 'button_set',
             'title' => __('Enable WooCommerce payments?', 'wpresidence-core'),
             'subtitle' => __('', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no',
             ),
             'default' => 'no',
         ),
     )
 ));
 

  // -> START Search Selection
 Redux::setSection($opt_name, array(
     'title' => __('Half Map Template', 'wpresidence-core'),
     'id' => 'half_map_settings_sidebar',
     'icon' => 'el el-search'
  ));


  Redux::setSection($opt_name, array(
     'title' => __('Half Map Template', 'wpresidence-core'),
     'id' => 'half_map_search_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_half_map_search',
             'type' => 'button_set',
             'title' => __('Show Search', 'wpresidence-core'),
             'subtitle' => __('Show or hide the search form on Half Map templates.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
            'id' => 'wp_estate_half_map_search_version',
            'type' => 'image_select',
            'title' => __('Select Search Style', 'wpresidence-core'),
            'subtitle' => __('Choose the Half Map search form style.', 'wpresidence-core'),
            'options' => array(
                '1' => array(
                    'alt' => '',
                    'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/half-map1.png'
                ),
                '2' => array(
                    'alt' => '',
                    'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/half-map2.png'
                ),
             
            ),
            'default' => '1'
        ),
        array(
            'id' => 'wp_estate_half_map_search_fields_no',
            'type' => 'select',
            'title' => __('Search Fields per Row', 'wpresidence-core'),
            'subtitle' => __('Choose how many search fields appear per row in the Half Map search.', 'wpresidence-core'),
            'options' => array(
                '1' => __('One', 'wpresidence-core'),
                '2' => __('Two', 'wpresidence-core'),
                '3' => __('Three', 'wpresidence-core'),
                '4' => __('Four', 'wpresidence-core'),
                '5' => __('Five', 'wpresidence-core'),
                '6' => __('Six', 'wpresidence-core'),
            ),
            'default' => '3',
        ),
 
        array(
            'id' => 'wp_estate_half_map_search_map_type',
            'type' => 'button_set',
            'title' => __('Map View / List view', 'wpresidence-core'),
            'subtitle' => __('Choose which option the toggle shows by default: Map View or List View.', 'wpresidence-core'),
            'options' => array(
                'map' => __('Map view', 'wpresidence-core'),
                'list' => __('List view', 'wpresidence-core'),
            ),
            'default' => 'map',
        ),

         array(
         'id' => 'wp_estate_half_map_position',
         'type' => 'button_set',
         'title' => __('Half Map Layout – Map Position', 'wpresidence-core'),
         'subtitle' => __('Select where the map should appear on all Half Map pages, including Search Results, Category/Taxonomy Half Map pages, and Properties List with Half Map.', 'wpresidence-core'),
         'options' => array(
             'left' => __('left', 'wpresidence-core'),
             'right' => __('right', 'wpresidence-core')
         ),
         'default' => 'left',
        ),


        array(
        'id' => 'wp_estate_property_list_type_footer',
        'type' => 'button_set',
        'title' => __('Show Footer when using Half Map Layout', 'wpresidence-core'),
        'subtitle' => __('Enable footer when using Half Map Layout type. Show Footer when using Half Map Layout" option cannot work if "Enable Sticky Footer" is also set to Yes. ', 'wpresidence-core'),
        'options' => array(
            'yes' => __('yes', 'wpresidence-core'),
            'no' => __('no', 'wpresidence-core')
        ),
        'default' => 'no',
    ),
    
),
));
 
 // -> START Search Selection
 Redux::setSection($opt_name, array(
     'title' => __('Search', 'wpresidence-core'),
     'id' => 'advanced_search_settings_sidebar',
     'icon' => 'el el-search'
  ));

 Redux::setSection($opt_name, array(
     'title' => __('Advanced Search Results Page', 'wpresidence-core'),
     'id' => 'advanced_search_results',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_property_list_type_adv',
             'type' => 'button_set',
             'title' => __('Properties list layout type for advanced search results page', 'wpresidence-core'),
             'subtitle' => __('Select between standard or half map layout style for advanced search results page.', 'wpresidence-core'),
             'options' => array(
                 '1' => __('standard', 'wpresidence-core'),
                 '2' => __('half map', 'wpresidence-core')
             ),
             'default' => '2',
         ),
         array(
             'id' => 'wp_estate_property_list_type_adv_order',
             'type' => 'button_set',
             'title' => __('Properties default order in advanced search results page', 'wpresidence-core'),
             'subtitle' => __('Select the default order for properties in advanced search results page', 'wpresidence-core'),
             'options' => $listing_filter_array,
             'default' => "0",
         ),
 
         array(
             'id' => 'wp_estate_prop_no_adv_search',
             'type' => 'text',
             'title' => __('Properties number per page in advanced search results page', 'wpresidence-core'),
             'subtitle' => __('Set how many properties properties to show per page in advanced search results page', 'wpresidence-core'),
             'default' => '12'
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Advanced Search Display', 'wpresidence-core'),
     'id' => 'advanced_search_settings_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_adv_search_general',
             'type' => 'button_set',
             'title' => __('Show Advanced Search?', 'wpresidence-core'),
             'subtitle' => __('Disables or enables the display of advanced search globally.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         array(
             'id' => 'wp_estate_show_adv_search_property_page',
             'type' => 'button_set',
             'title' => __('Show Advanced Search in Property Page?', 'wpresidence-core'),
             'subtitle' => __('Disables or enables the display of advanced search in property pages.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         array(
             'id' => 'wp_estate_show_adv_search_tax',
             'type' => 'button_set',
             'title' => __('Show Advanced Search in Taxonomies, Categories or Archives?', 'wpresidence-core'),
             'subtitle' => __('Disables or enables the display of advanced search in taxonomies, categories and blog archives', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         
         array(
             'id' => 'wp_estate_show_preview_results',
             'type' => 'button_set',
             'title' => __('Show Preview results box?', 'wpresidence-core'),
             'subtitle' => __('This will enable the search preview in the theme options search form ', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         
         
         array(
             'id' => 'wp_estate_sticky_search',
             'type' => 'button_set',
             'title' => __('Use sticky search?', 'wpresidence-core'),
             'subtitle' => __('This will replace the sticky header. Doesn\'t apply to search type 3', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
         array(
             'id' => 'wp_estate_search_on_start',
             'type' => 'button_set',
             'title' => __('Put Search form before the header media?', 'wpresidence-core'),
             'subtitle' => __('Works with "Use Float Form" options set to no! Doesn\'t apply to search type 3.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
         array(
             'id' => 'wp_estate_use_float_search_form',
             'type' => 'button_set',
             'title' => __('Use Float Search Form?', 'wpresidence-core'),
             'subtitle' => __('The search form is "floating" over the media header and you set the position.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
         array(
             'id' => 'wp_estate_float_form_top',
             'required' => array('wp_estate_use_float_search_form', '=', 'yes'),
             'type' => 'text',
             'title' => __('Distance betwen search form and the top margin of the browser: Ex 200px or 20%', 'wpresidence-core'),
             'subtitle' => __('Distance betwen search form and the top margin of the browser: Ex 200px or 20%.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_float_form_top_tax',
             'required' => array('wp_estate_use_float_search_form', '=', 'yes'),
             'type' => 'text',
             'title' => __('Distance betwen search form and the top margin of the browser in px Ex 200px or 20% - for taxonomy, category and archives pages', 'wpresidence-core'),
             'subtitle' => __('Distance betwen search form and the top margin of the browser in px Ex 200px or 20% - for taxonomy, category and archives pages.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_show_adv_search_visible',
             'type' => 'button_set',
             'title' => __('Keep Advanced Search visible? (*only for Type 1,3 and 4)', 'wpresidence-core'),
             'subtitle' => __('If no, advanced search over header will display in closed position by default.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         array(
             'id' => 'wp_estate_show_empty_city',
             'type' => 'button_set',
             'title' => __('Show Property Categories with 0 properties in advanced search and properties list filters.', 'wpresidence-core'),
             'subtitle' => __('Applies for Categories, Types, States, Cities and Areas dropdowns.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
     ),
 ));




 $tax_array = array('none' => esc_html__('none', 'wpresidence-core'),
     'property_category' => esc_html__('Categories', 'wpresidence-core'),
     'property_action_category' => esc_html__('Action Categories', 'wpresidence-core'),
     'property_city' => esc_html__('City', 'wpresidence-core'),
     'property_area' => esc_html__('Area', 'wpresidence-core'),
     'property_county_state' => esc_html__('County State', 'wpresidence-core'),
 );

 

 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_select/' );
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_set_search/' );
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_taxonomy_tabs/' );
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_taxonomy_tabs_price/' );


 Redux::setSection($opt_name, array(
     'title' => __('Advanced Search Form', 'wpresidence-core'),
     'id' => 'advanced_search_form_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_adv_search_type',
             'type' => 'image_select',
             'title' => __('Advanced Search Type?', 'wpresidence-core'),
             'subtitle' => __('Applies for the search over header type. IMPORTANT! Enable Advanced Search Custom Fields - YES, for Type 5, Type 6, Type 8, Type 10 and Type 11.', 'wpresidence-core'),
             /*'options' => array(
                 '1' => 'Type 1',
                // '2' => 'Type 2',
                 '3' => 'Type 3',
                 '4' => 'Type 4',
                 '5' => 'Type 5',
                 '6' => 'Type 6',
              //   '7-obsolete' => 'Type 7-obsolete',
             //    '8' => 'Type 8',
             //    '9-obsolete' => 'Type 9-obsolete',
                 '10' => 'Type 10',
                 '11' => 'Type 11'
             ),
             */
              'options' => array(
                 '1' => array(
                     'title'=>'Type 1',
                    'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type1.png'
                 ),
                 '3' => array(
                        'title'=>'Type 3',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type3.png'
                 ),
                 '4' => array(
                        'title'=>'Type 4',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type4.png'
                 ),
                 '5' => array(
                        'title'=>'Type 5',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type5.png'
                 ),
                 '6' => array(
                        'title'=>'Type 6',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type6.png'
                 ),
                 '8' => array(
                        'title'=>'Type 8',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type8.png'
                 ),
                 '10' => array(
                      'title'=>'Type 10',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type10.png'
                 ),
                 '11' => array(
                        'title'=>'Type 11',
                     'img' => WPESTATE_PLUGIN_URL . '/wpresidence-core/img/search-types/type11.png'
                 ),
                  
                  
             ),   
             'default' => '1'
         ),
 
                 
         array(
             'id' => 'wp_estate_custom_advanced_search',
             'type' => 'button_set',
             'title' => __('Use Custom Fields For Advanced Search?', 'wpresidence-core'),
             'subtitle' => __(' It is mandatory to be YES for Type 5, Type 6, Type 8, Type 10 and Type 11. Set the custom fields as shown in this help ', 'wpresidence-core') . '<a href="http://help.wpresidence.net/article/adv-search-custom-fields-setup/" target=_blank>http://help.wpresidence.net/article/adv-search-custom-fields-setup/</a>',
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
         
 
         array(
             'id' => 'wp_estate_show_slider_price',
             'type' => 'button_set',
          
             'title' => __('Show Slider for Price?', 'wpresidence-core'),
             'subtitle' => __('If no, price field can still be used in search and it will be input type.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         array(
             'id' => 'wp_estate_show_slider_min_price',
             'type' => 'text',
             'required' => array('wp_estate_show_slider_price', '=', 'yes'),
             'title' => __('Minimum value for Price Slider', 'wpresidence-core'),
             'subtitle' => __('Type only numbers!', 'wpresidence-core'),
             'default' => '0'
         ),
         array(
             'id' => 'wp_estate_show_slider_max_price',
             'type' => 'text',
             'required' => array('wp_estate_show_slider_price', '=', 'yes'),
             'title' => __('Maximum value for Price Slider', 'wpresidence-core'),
             'subtitle' => __('Type only numbers!', 'wpresidence-core'),
             'default' => '1500000'
         ),
         
          array(
             'id' => 'wp_estate_min_price_dropdown_values',
             'type' => 'text',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '6'),
             ),
             'title' => __('Values for Minimum Price Dropdown', 'wpresidence-core'),
             'subtitle' => __('Applies to Price V3 field. Type only numbers, separated by commna', 'wpresidence-core'),
             'default' => '1000,5000,10000,50000,100000,200000,300000,400000,500000,600000,700000,800000,900000,1000000,1500000,2000000,2500000,5000000'
         ),
         array(
             'id' => 'wp_estate_max_price_dropdown_values',
             'type' => 'text',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '6'),
             ),
             'title' => __('Values for Maximum Price Dropdown', 'wpresidence-core'),
             'subtitle' => __('Applies to Price V3 field. Type only numbers, separated by commna', 'wpresidence-core'),
             'default' => '5000,10000,50000,100000,200000,300000,400000,500000,600000,700000,800000,900000,1000000,1500000,2000000,2500000,5000000,10000000'
         ),
 
         array(
             'id' => 'wp_estate_show_dropdowns',
             'type' => 'button_set',
             'title' => __('Show Dropdowns for beds, bathrooms or rooms? (*only works with the options for Custom Fields -> YES)', 'wpresidence-core'),
             'subtitle' => __('Add the Bedrooms, Bathrooms or Rooms fields in the Advanced Search Form.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes'
         ),
         
         
          array(
             'id' => 'wp_estate_beds_component_values',
             'type' => 'text',
           
             'title' => __('Possible dropdown values for beds', 'wpresidence-core'),
             'subtitle' => __('Type only numbers with/without + !', 'wpresidence-core'),
             'default' => '1,2,3,4,5,6+'
         ),
         
         array(
             'id' => 'wp_estate_baths_component_values',
             'type' => 'text',
           
             'title' => __('Possible dropdown values for baths', 'wpresidence-core'),
             'subtitle' => __('Type only numbers with/without + !', 'wpresidence-core'),
             'default' => '1,2,3,4,5,6+'
         ),
         
         array(
             'id' => 'wp_estate_rooms_component_values',
             'type' => 'text',
           
             'title' => __('Possible dropdown values for rooms', 'wpresidence-core'),
             'subtitle' => __('Type only numbers with/without + !', 'wpresidence-core'),
             'default' => '1,2,3,4,5,6+'
         ),
         
         
         array(
             'id' => 'wp_estate_show_adv_search_extended',
             'type' => 'button_set',
             'title' => __('Show Amenities and Features?', 'wpresidence-core'),
             'subtitle' => __('Features selected will show in all Advanced Search Forms.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no'
         ),
         array(
             'id' => 'wp_estate_advanced_exteded',
             'type' => 'wpestate_select',
             'required' => array('wp_estate_show_adv_search_extended', '=', 'yes'),
             'multi' => true,
             'title' => __('Amenities and Features for Advanced Search', 'wpresidence-core'),
             'subtitle' => __('Select which features and amenities to show in search forms.', 'wpresidence-core'),
             'options' => wpresidence_redux_advanced_exteded(),
         ),
         
         array(
             'id' => 'wp_estate_adv6_taxonomy',
             'type' => 'select',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '1'),
                 array('wp_estate_adv_search_type', '!=', '2'),
                 array('wp_estate_adv_search_type', '!=', '3'),
                 array('wp_estate_adv_search_type', '!=', '4'),
                 array('wp_estate_adv_search_type', '!=', '5'),
                 array('wp_estate_adv_search_type', '!=', '10'),
                 array('wp_estate_adv_search_type', '!=', '11'),
                 array('wp_estate_adv_search_type', '!=', '7-obsolete'),
                 array('wp_estate_adv_search_type', '!=', '9-obsolete'),
             ),
             'title' => __('Select the Property Taxonomy for tabs options in Advanced Search Type 6 and Type 8', 'wpresidence-core'),
             'subtitle' => __('Select from the dropdown on the right.', 'wpresidence-core'),
             'options' => $tax_array
         ),
 
         array(
             'id' => 'wp_estate_search_tab_align',
             'type' => 'button_set',
             'title' => __('Tabs alignment', 'wpresidence-core'),
             'subtitle' => __('Tabs alignment', 'wpresidence-core'),
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '1'),
                 array('wp_estate_adv_search_type', '!=', '2'),
                 array('wp_estate_adv_search_type', '!=', '3'),
                 array('wp_estate_adv_search_type', '!=', '4'),
                 array('wp_estate_adv_search_type', '!=', '5'),
                 array('wp_estate_adv_search_type', '!=', '10'),
                 array('wp_estate_adv_search_type', '!=', '11'),
                 array('wp_estate_adv_search_type', '!=', '7-obsolete'),
                 array('wp_estate_adv_search_type', '!=', '9-obsolete'),
             ),
             'options' => array(
                 'left' => 'left',
                 'center' => 'center'
             ),
             'default' => 'left'
         ),
         array(
             'id' => 'wp_estate_adv6_taxonomy_terms',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '1'),
                 array('wp_estate_adv_search_type', '!=', '2'),
                 array('wp_estate_adv_search_type', '!=', '3'),
                 array('wp_estate_adv_search_type', '!=', '4'),
                 array('wp_estate_adv_search_type', '!=', '5'),
                 array('wp_estate_adv_search_type', '!=', '10'),
                 array('wp_estate_adv_search_type', '!=', '11'),
                 array('wp_estate_adv_search_type', '!=', '7-obsolete'),
                 array('wp_estate_adv_search_type', '!=', '9-obsolete'),
             ),
             'type' => 'wpestate_taxonomy_tabs',
             'title' => __('Select the Property Taxonomy Terms for tabs options in Advanced Search Type 6 and Type 8', 'wpresidence-core'),
             'subtitle' => __('You must select at least 1 category for search to display.', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_adv6_min_price',
             'type' => 'wpestate_taxonomy_tabs_price',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '1'),
                 array('wp_estate_adv_search_type', '!=', '2'),
                 array('wp_estate_adv_search_type', '!=', '3'),
                 array('wp_estate_adv_search_type', '!=', '4'),
                 array('wp_estate_adv_search_type', '!=', '5'),
                 array('wp_estate_adv_search_type', '!=', '10'),
                 array('wp_estate_adv_search_type', '!=', '11'),
                 array('wp_estate_adv_search_type', '!=', '7-obsolete'),
                 array('wp_estate_adv_search_type', '!=', '9-obsolete'),
             ),
             'full_width' => true,
             'title' => __('Price slider Minimum values for advanced search with tabs', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_adv6_max_price',
             'type' => 'wpestate_taxonomy_tabs_price',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '1'),
                 array('wp_estate_adv_search_type', '!=', '2'),
                 array('wp_estate_adv_search_type', '!=', '3'),
                 array('wp_estate_adv_search_type', '!=', '4'),
                 array('wp_estate_adv_search_type', '!=', '5'),
                 array('wp_estate_adv_search_type', '!=', '10'),
                 array('wp_estate_adv_search_type', '!=', '11'),
                 array('wp_estate_adv_search_type', '!=', '7-obsolete'),
                 array('wp_estate_adv_search_type', '!=', '9-obsolete'),
             ),
             'full_width' => true,
             'title' => __('Price slider Maximum values for advanced search with tabs', 'wpresidence-core'),
         ),
         
         
          array(
             'id' => 'wp_estate_adv6_min_price_dropdown_values',
             'type' => 'wpestate_taxonomy_tabs_price',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '1'),
                 array('wp_estate_adv_search_type', '!=', '2'),
                 array('wp_estate_adv_search_type', '!=', '3'),
                 array('wp_estate_adv_search_type', '!=', '4'),
                 array('wp_estate_adv_search_type', '!=', '5'),
                 array('wp_estate_adv_search_type', '!=', '10'),
                 array('wp_estate_adv_search_type', '!=', '11'),
                 array('wp_estate_adv_search_type', '!=', '7-obsolete'),
                 array('wp_estate_adv_search_type', '!=', '9-obsolete'),
             ),
             'full_width' => true,
             'title' => __('Price dropdown minimum values for Price V3 field, separated by comma', 'wpresidence-core'),
         ),
         
         
          array(
             'id' => 'wp_estate_adv6_max_price_dropdown_values',
             'type' => 'wpestate_taxonomy_tabs_price',
             'required' => array(
                 array('wp_estate_adv_search_type', '!=', '1'),
                 array('wp_estate_adv_search_type', '!=', '2'),
                 array('wp_estate_adv_search_type', '!=', '3'),
                 array('wp_estate_adv_search_type', '!=', '4'),
                 array('wp_estate_adv_search_type', '!=', '5'),
                 array('wp_estate_adv_search_type', '!=', '10'),
                 array('wp_estate_adv_search_type', '!=', '11'),
                 array('wp_estate_adv_search_type', '!=', '7-obsolete'),
                 array('wp_estate_adv_search_type', '!=', '9-obsolete'),
             ),
             'full_width' => true,
             'title' => __('Price dropdown maximum values for Price V3 field, separated by comma', 'wpresidence-core'),
         ),
         
         
         array(
             'id' => 'wp_estate_adv_search_fields_no',
             'type' => 'text',
             'title' => __('No of Search fields', 'wpresidence-core'),
             'subtitle' => __('No of Search fields. Click on "Save Changes" and reload the page to see the fields!', 'wpresidence-core'),
             'default' => '8'
         ),
         array(
             'id' => 'wp_estate_search_fields_no_per_row',
             'type' => 'text',
             'title' => __('No of Search fields per row', 'wpresidence-core'),
             'subtitle' => __('No of Search fields per row (Possible values: 1,2,3,4).', 'wpresidence-core'),
             'default' => '4'
         ),
         array(
             'id' => 'wpestate_set_search',
             'type' => 'wpestate_set_search',
             'title' => __('Advanced Search Custom Fields Setup', 'wpresidence-core'),
             'full_width' => true,
         ),
     ),
 ));

 
 Redux::setSection($opt_name, array(
     'title' => __('Geo Location Search', 'wpresidence-core'),
     'id' => 'geo_location_search_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_use_geo_location',
             'type' => 'button_set',
             'title' => __('Use Geo Location Search', 'wpresidence-core'),
             'subtitle' => __('If YES, the geo location search will appear above half map search fields', 'wpresidence-core'),
             'default' => 'no',
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
         ),
         array(
             'id' => 'wp_estate_initial_radius',
             'type' => 'text',
             'title' => __('Initial area radius', 'wpresidence-core'),
             'subtitle' => __('Initial area radius. Use only numbers.', 'wpresidence-core'),
             'default' => '12'
         ),
         array(
             'id' => 'wp_estate_min_geo_radius',
             'type' => 'text',
             'title' => __('Minimum radius value', 'wpresidence-core'),
             'subtitle' => __('Minimum radius value. Use only numbers.', 'wpresidence-core'),
             'default' => '1'
         ),
         array(
             'id' => 'wp_estate_max_geo_radius',
             'type' => 'text',
             'title' => __('Maximum radius value', 'wpresidence-core'),
             'subtitle' => __('Maximum radius value. Use only numbers.', 'wpresidence-core'),
             'default' => '50'
         ),
         array(
             'id' => 'wp_estate_geo_radius_measure',
             'type' => 'button_set',
             'title' => __('Show Geo Location Search in:', 'wpresidence-core'),
             'subtitle' => __('Select between miles and kilometers.', 'wpresidence-core'),
             'default' => 'miles',
             'options' => array(
                 'miles' => __('miles', 'wpresidence-core'),
                 'km' => __('km', 'wpresidence-core')
             ),
         ),
         array(
             'id' => 'wp_estate_use_geo_location_limit_country',
             'type' => 'button_set',
             'title' => __('Limit to a specific country?', 'wpresidence-core'),
             'subtitle' => __('If YES, the geo location search will be limited to a specific country', 'wpresidence-core'),
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
             'title' => __('Select the country', 'wpresidence-core'),
             'subtitle' => __('If YES, the geo location search will be limited to a specific country', 'wpresidence-core'),
             'options' => wpestate_country_list_code(),
             'default' => ''
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Save Search Settings', 'wpresidence-core'),
     'id' => 'save_search_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_save_search',
             'type' => 'button_set',
             'title' => __('Use Saved Search Feature?', 'wpresidence-core'),
             'subtitle' => __('If YES, user can save his searchs from Advanced Search Results, if he is logged in with a registered account.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_search_alert',
             'type' => 'button_set',
             'title' => __('Send emails', 'wpresidence-core'),
             'subtitle' => __('Send weekly or daily an email alert with new published properties matching user saved search', 'wpresidence-core'),
             'options' => array(
                 0 => 'daily',
                 1 => 'weekly'
             ),
             'default' => 1,
         ),
     ),
 ));
 
 Redux::setSection($opt_name, array(
     'title' => __('Sold Listings', 'wpresidence-core'),
     'id' => 'advanced_search_Sold_items',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_show_sold_items',
             'type' => 'button_set',
             'title' => __('Display Sold Listings', 'wpresidence-core'),
             'subtitle' => __('Choose whether to show or hide listings marked as "Sold" in search results and property category/taxonomy pages.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wpestate_mark_sold_status',
             'type' => 'select',
             'title' => esc_html__('Sold Property Status', 'wpresidece-core'),
             'subtitle' => esc_html__('Select the status that will be used to mark properties as "Sold".', 'wpresidece-core'),
             'desc' => '',
             'data' => 'terms',
             'args' => array(
                 'taxonomies' => array('property_status'),
                 'hide_empty' => false,
             )
         ),
     ),
 ));
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Taxonomies Multi Selections', 'wpresidence-core'),
     'id' => 'advanced_search_multi_select_items',
     'subsection' => true,
     'fields' => array(
          array(
             'id' => 'wp_estate_categ_select_list_multiple',
             'type' => 'button_set',
             'title' => __('Multiselect for the Category taxonomy', 'wpresidence-core'),
             'subtitle' => __('If yes, users can select multiple items in the Category dropdown in Advanced Search.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_action_select_list_multiple',
             'type' => 'button_set',
             'title' => __('Multiselect for the Type taxonomy', 'wpresidence-core'),
             'subtitle' => __('If yes, users can select multiple items in the Type dropdown in Advanced Search.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_city_select_list_multiple',
             'type' => 'button_set',
             'title' => __('Multiselect for the City taxonomy', 'wpresidence-core'),
             'subtitle' => __('If yes, users cand select multiple items in the City dropdown in Advanced Search.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_area_select_list_multiple',
             'type' => 'button_set',
             'title' => __('Multiselect for the Area taxonomy', 'wpresidence-core'),
             'subtitle' => __('If yes, users can select multiple items in the Area dropdown in Advanced Search.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_county_select_state_list_multiple',
             'type' => 'button_set',
             'title' => __('Multiselect for the County/State taxonomy', 'wpresidence-core'),
             'subtitle' => __('If yes, users can select multiple items in the County/State dropdown in Advanced Search.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_status_select_list_multiple',
             'type' => 'button_set',
             'title' => __('Multiselect for the Property Status taxonomy', 'wpresidence-core'),
             'subtitle' => __('If yes, users can select multiple items in the Property Status dropdown in Advanced Search.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_select_list_multiple_show_search',
             'type' => 'button_set',
             'title' => __('Show Search in the multiselect component', 'wpresidence-core'),
             'subtitle' => __('Show or hide the search in the multiselect component', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),  
     ),
 ));
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Advanced Search Colors', 'wpresidence-core'),
     'id' => 'advanced_search_colors_tab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_adv_back_color',
             'type' => 'color',
             'title' => __('Advanced Search Background Color', 'wpresidence-core'),
             'subtitle' => __('Applies when the search form display is set to float mode over the hero header, except for Search Type 3', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_adv_back_color_opacity',
             'type' => 'text',
             'title' => __('Advanced Search Background color Opacity', 'wpresidence-core'),
             'subtitle' => __('Values between 0 -invisible and 1 - fully visible', 'wpresidence-core'),
         ),
         array(
             'id' => 'wp_estate_adv_font_color',
             'type' => 'color',
             'title' => __('Advanced Search Font Color', 'wpresidence-core'),
             'subtitle' => __('Applies when search form display is set to float mode over the hero header for Features and Amenities', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_adv_search_back_color',
             'type' => 'color',
             'title' => __('Advanced Search Button Background Color', 'wpresidence-core'),
             'subtitle' => __('Applies only for Search Type 1', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_adv_search_tab_font_color',
             'type' => 'color',
             'title' => __('Search Type 6 - Categories Tabs Color', 'wpresidence-core'),
             'subtitle' => __('Default color is blue (used for tab background and font color)', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_adv_search_font_color',
             'type' => 'color',
             'title' => __('Search Form Dropdown Fields Hover Font Color', 'wpresidence-core'),
             'subtitle' => __('Applies to all forms, dropdowns and input search fields. Default color is grey.', 'wpresidence-core'),
             'transparent' => false,
         ),
         array(
             'id' => 'wp_estate_adv_search_background_color',
             'type' => 'color',
             'title' => __('Search Form Dropdown Fields Hover Background Color', 'wpresidence-core'),
             'subtitle' => __('Applies to all forms, dropdowns and input search fields. Default color is light grey', 'wpresidence-core'),
             'transparent' => false,
         ),
     ),
 ));
 
 
 // -> START Advanced Selection
 Redux::setSection($opt_name, array(
     'title' => __('Advanced Settings', 'wpresidence-core'),
     'id' => 'advanced_settings_sidebar',
     'icon' => 'el el-cog'
 ));
 
 $value_httaces = '<IfModule mod_deflate.c>
 # Insert filters
 AddOutputFilterByType DEFLATE text/plain
 AddOutputFilterByType DEFLATE text/html
 AddOutputFilterByType DEFLATE text/xml
 AddOutputFilterByType DEFLATE text/css
 AddOutputFilterByType DEFLATE application/xml
 AddOutputFilterByType DEFLATE application/xhtml+xml
 AddOutputFilterByType DEFLATE application/rss+xml
 AddOutputFilterByType DEFLATE application/javascript
 AddOutputFilterByType DEFLATE application/x-javascript
 AddOutputFilterByType DEFLATE application/x-httpd-php
 AddOutputFilterByType DEFLATE application/x-httpd-fastphp
 AddOutputFilterByType DEFLATE image/svg+xml
 # Drop problematic browsers
 BrowserMatch ^Mozilla/4 gzip-only-text/html
 BrowserMatch ^Mozilla/4\.0[678] no-gzip
 BrowserMatch \bMSI[E] !no-gzip !gzip-only-text/html
 # Make sure proxies dont deliver the wrong content
 Header append Vary User-Agent env=!dont-vary
 </IfModule>
 ## EXPIRES CACHING ##
 <IfModule mod_expires.c>
 ExpiresActive On
 ExpiresByType image/jpg "access 1 year"
 ExpiresByType image/jpeg "access 1 year"
 ExpiresByType image/gif "access 1 year"
 ExpiresByType image/png "access 1 year"
 ExpiresByType text/css "access 1 month"
 ExpiresByType text/html "access 1 month"
 ExpiresByType application/pdf "access 1 month"
 ExpiresByType text/x-javascript "access 1 month"
 ExpiresByType application/x-shockwave-flash "access 1 month"
 ExpiresByType image/x-icon "access 1 year"
 ExpiresDefault "access 1 month"
 </IfModule>
 ## EXPIRES CACHING ##';
 
 Redux::setSection($opt_name, array(
     'title' => __('Theme Speed Settings', 'wpresidence-core'),
     'id' => 'speed_management_tab',
     'desc' => '<strong>' . __('Speed Advice'
             . '</strong></br>1. If you are NOT using "Ultimate Addons for Visual Composer" please disable it or just disable the modules you don\'t use. It will reduce the size of javascript files you are loading and increase the site speed!.'
             . '</br>2. Use the EWWW Image Optimizer (or WP Smush IT) plugin to optimise images- optimised images increase the site speed.'
             . '</br>3. Create a free account on Cloudflare (https://www.cloudflare.com/) and use this CDN.'
             . '</br>4. If you are using custom categories make sure you are adding the custom pins images on Theme Options -> Map -> Pin Management. The site will get slow if it needs to look for images that don\'t exist. ', 'wpresidence-core'),
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'wp_estate_disable_theme_cache',
             'type' => 'button_set',
             'title' => __('Disable Theme Cache (Keep theme cache on when your site is in production)', 'wpresidence-core'),
             'subtitle' => __('Theme Cache will cache only the heavy database queries. Use this feature along classic cache plugins like WpRocket!', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_use_mimify',
             'type' => 'button_set',
             'title' => __('Minify css and js files', 'wpresidence-core'),
             'subtitle' => __('The system will use the minified versions of the css and js files', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_use_custom_ajaxhandler',
             'type' => 'button_set',
             'title' => __('Use custom ajax handler for searches?', 'wpresidence-core'),
             'subtitle' => __('Some hosting companies will not allow the use of custom ajax handlers. In this case, you need to set this option to no.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'yes',
         ),
         array(
             'id' => 'wp_estate_remove_script_version',
             'type' => 'button_set',
             'title' => __('Remove script version - Following Envato guidelines we removed this feature.Please use a cache plugin in order to remove the script version.', 'wpresidence-core'),
             'subtitle' => __('The system will remove the script version when it is included. This doest not actually improve speed, but improves test score on speed tools pages.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'info_warning_enable_browser',
             'type' => 'textarea',
             // 'style' => 'warning',
             'title' => __('Enable Browser Cache', 'wpresidence-core'),
             'subtitle' => __('Add this code in your .httaces file(copy paste at the end). It will activate the browser cache and speed up your site.', 'wpresidence-core'),
             //  'desc'   => $value_httaces,
             'default' => $value_httaces,
         ),
 )));
 
 Redux::setSection($opt_name, array(
     'title' => __('Import & Export', 'wpresidence-core'),
     'id' => 'import_export_ab',
     'subsection' => true,
     'fields' => array(
         array(
             'id' => 'opt-import-export',
             'type' => 'import_export',
             'title' => 'Import & Export',
             //  'subtitle'      => '',
             'full_width' => false,
         ),
     ),
 ));
 
 Redux::set_extensions( $opt_name, WPESTATE_PLUGIN_PATH.'redux-framework/extensions/wpestate_image_size/' );
 $default_image_size = wpestate_return_default_image_size();
 $redux_image_size=array();
 foreach ($default_image_size as $key=>$image_size){
     $redux_image_size[]=array(
             'id' => 'wp_estate_'.$key,
             'full_width' => true,
             'type' => 'wpestate_image_size',
             'title' => $image_size['name'],
             'subtitle'=> esc_html('Orignal size:').' '.$image_size['width'].'px x '.$image_size['height'].' px. '.esc_html('The new values apply only for the images you upload after saving your change. For older images you need to use "Regenerate Thumbnails" plugin in order to recreate thumbs.','wpresidence-core')
            
     );
 }
 
 
 
 
 
 Redux::setSection($opt_name, array(
     'title' => __('Image settings', 'wpresidence-core'),
     'desc' => '<div class="wpsestate_admin_notice">' . __('VERY IMPORTANT!'
             . '</strong></br>- Make a backup of your site before modifying thumbs. You can revert the backup if you do not like how your changes influence your site design.'
             . '</br>- Your changes will influence design (thumbs height / width could look different than in default design) and speed (the bigger width and height values are, the slower the site could be)'
             . '</br>- Learn about WordPress thumbs and how they work before making changes -  https://developer.wordpress.org/reference/functions/add_image_size/ ', 'wpresidence-core').'</div>',
     'id' => 'Image_settings_tab',
     'subsection' => true,
     'fields' =>  $redux_image_size
     ));
 

Redux::setSection($opt_name, array(
    'title' => __('Webhook URL', 'wpresidence-core'),
    'id' => 'webhook_url_tab',
    'subsection' => true,
    'fields' =>  array(
        array(
            'id' => 'wp_estate_webhook_url',
            'type' => 'text',
            'title' => __('Webhook URL', 'wpresidence-core'),
            'subtitle' => __('Enter the integration URL (Ex: https://example.com/webhook)', 'wpresidence-core'),
            'default' => 'https://example.com/webhook'
        ),
        array(
            'id' => 'wp_estate_contact_form',
            'type' => 'button_set',
            'title' => __('Property Agent Form', 'wpresidence-core'),
            'subtitle' => __('Enable data sending for property agent form', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'wp_estate_property_form',
            'type' => 'button_set',
            'title' => __('Property Agent Form', 'wpresidence-core'),
            'subtitle' => __('Enable data sending for property agent form', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'wp_estate_schedule_tour_form',
            'type' => 'button_set',
            'title' => __('Property Schedule Tour Form', 'wpresidence-core'),
            'subtitle' => __('Enable data sending for property schedule tour form', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'wp_estate_agent_contact_form',
            'type' => 'button_set',
            'title' => __('Property Agent Form', 'wpresidence-core'),
            'subtitle' => __('Enable data sending for property agent form', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'wp_estate_agency_contact_form',
            'type' => 'button_set',
            'title' => __('Property Agency Form', 'wpresidence-core'),
            'subtitle' => __('Enable data sending for property agency form', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'wp_estate_developer_contact_form',
            'type' => 'button_set',
            'title' => __('Property Developer Form', 'wpresidence-core'),
            'subtitle' => __('Enable data sending for property developer form', 'wpresidence-core'),
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
        ),
    )
));
 
 
 
 
 
 
 // -> START help Selection
 Redux::setSection($opt_name, array(
     'title' => __('MLS - IDX - RESO', 'wpresidence-core'),
     'id' => 'mls_sidebar',
     'icon' => 'el el-cloud',
     'fields' => array(
         array(
             'id' => 'opt-info-mlsimport',
             'type' => 'info',
             'notice' => false,
             'desc' => 'WpResidence is MLSImport.com ready, a 3rd party plugin which can import IDX listings directly into WpResidence as if you would add them manually. Check out a demo with WpResidence & MLS Imported listings here: https://orlando.wpresidence.net/ and https://demo3.wpresidence.net/ '
         ),
         array(
             'id' => 'wp_estate_use_optima',
             'type' => 'button_set',
             'title' => __('Use Optima Express plugin (idx plugin by ihomefinder) - you will need to enable the plugin ?', 'wpresidence-core'),
             'subtitle' => __('Set to YES only if you have an IDX plugin installed and activated in your site', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_idx_enable',
             'type' => 'button_set',
             'title' => __('Enable dsIDXpress to use the map', 'wpresidence-core'),
             'subtitle' => __('Enable only if you activate the dsIDXpres optional plugin. Works ONLY with Google Maps enabled (not Open Street Map)', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
     ),
 ));
 
 // -> START help Selection
 Redux::setSection($opt_name, array(
     'title' => __('CRM', 'wpresidence-core'),
     'id' => 'CRM_sidebar',
     'icon' => 'el el-group',
     'fields' => array(
         array(
             'id' => 'wp_estate_enable_hubspot_integration',
             'type' => 'button_set',
             'title' => __('Enable HubSpot Crm Integration? ', 'wpresidence-core'),
             'subtitle' => __('You will need a hubspot account and an api key.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_enable_hubspot_integration_for_all',
             'required' => array('wp_estate_enable_hubspot_integration', '=', 'yes'),
             'type' => 'button_set',
             'title' => __('Enable HubSpot Crm Integration for agents, agency and developers? ', 'wpresidence-core'),
             'subtitle' => __('They will need a hubspot account and an api key.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
         array(
             'id' => 'wp_estate_hubspot_api',
             'type' => 'text',
             'title' => __('HubSpot Private Application Token ( Api Key Authentication is now obsolete)', 'wpresidence-core'),
             'subtitle' => __('HubSpot Private Application Token ', 'wpresidence-core'),
             'default' => ''
         ),
         array(
             'id' => 'wp_estate_hubspot_first_stage',
             'type' => 'text',
             'title' => __('HubSpot FIrst Deal Stage', 'wpresidence-core'),
             'subtitle' => __('HubSpot FIrst Deal Stage', 'wpresidence-core'),
             'default' => 'appointmentscheduled'
         ),
     ))
 );
 
 require_once WPESTATE_PLUGIN_PATH . 'redux-framework/theme-admin-sections/wpresidence-api.php';  

 // -> START help Selection
 Redux::setSection($opt_name, array(
     'title' => __('Help & Custom', 'wpresidence-core'),
     'id' => 'help_custom_sidebar',
     'icon' => 'el el-question',
     'fields' => array(
         array(
             'id' => 'opt-info-normal',
             'type' => 'info',
             'notice' => false,
             'desc' => __('For support please go to ', 'wpresidence-core') . '< a href="http://support.wpestate.org/" target="_blank"> http://support.wpestate.org/ </a>' . __('create an account and post a ticket. The registration is simple and as soon as you post we are notified. We usually answer in the next 24h (except weekends). Please use this system and not the email. It will help us answer much faster. Thank you! ', 'wpresidence-core')
             . '<br /><br />' . __('For custom work on this theme please go to ', 'wpresidence-core') . '< a href="http://support.wpestate.org/" target="_blank"> http://support.wpestate.org/ </a>' . __(', create a ticket with your request and we will offer a free quote. ', 'wpresidence-core')
             . '<br /><br />' . __('For help files please go to ', 'wpresidence-core') . '< a href="http://help.wpresidence.net/" target="_blank"> http://help.wpresidence.net/</a>'
             . '<br /><br />' . __('Subscribe to our mailing list in order to receive news about new features and theme upgrades ', 'wpresidence-core') . '< a href="http://eepurl.com/CP5U5" target="_blank"> Subscribe Here!</a>'
         ),
         array(
             'id' => 'wp_estate_support',
             'type' => 'button_set',
             'title' => __('WpEstate Fan', 'wpresidence-core'),
             'subtitle' => __('The option "Yes" places a discrete link to wpestate.org in the footer.', 'wpresidence-core'),
             'options' => array(
                 'yes' => 'yes',
                 'no' => 'no'
             ),
             'default' => 'no',
         ),
     ),
 ));
 

 
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
