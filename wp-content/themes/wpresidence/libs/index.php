<?php
//MILLDONE
require_once  get_theme_file_path('/libs/css_js_include.php');
require_once  get_theme_file_path('/libs/plugins.php');
require_once  get_theme_file_path('/libs/help_functions.php');
require_once  get_theme_file_path('/templates/html_render_functions.php');

require_once get_theme_file_path( 'libs/property-admin.php' );
require_once get_theme_file_path( 'libs/theme-admin.php' );

// functions for  layout
require_once  get_theme_file_path('/libs/layout/layout_functions.php');
require_once  get_theme_file_path('libs/template_functions/template_functions.php');
require_once  get_theme_file_path('libs/taxonomy_functions/taxonomy_functions.php');
require_once  get_theme_file_path('libs/taxonomy_functions/taxonomy_map_functions.php');

// functions for  headers
require_once  get_theme_file_path( '/libs/headers/header-help-functions.php' );
require_once  get_theme_file_path('/libs/headers/header_filter_functions.php');
require_once  get_theme_file_path('/libs/headers/megamenu.php');
require_once  get_theme_file_path('/libs/headers/header_actions.php');

// functions for header media
require_once  get_theme_file_path('/libs/header_media/header-media-functions.php');
require_once  get_theme_file_path('/libs/header_media/header-media-theme-sliders.php');

// functions for splash page
require_once  get_theme_file_path('/libs/splash-page-functions/splash-page-functions.php');

//functions for advanced search results
require_once  get_theme_file_path('/libs/advanced_search/searchfunctions.php');
require_once  get_theme_file_path('/libs/advanced_search/searchfunctions2.php');
require_once  get_theme_file_path('/libs/advanced_search/display_seach_form_functions.php');


// unified property query layer (classic + index dispatcher)
require_once  get_theme_file_path('/libs/query/query-functions.php');

// 2026 query layer — estate_property only
require_once get_theme_file_path('/libs/query/query2026/wpestate_query_2026_property_placeholders.php');
require_once get_theme_file_path('/libs/query/query2026/wpestate_query_2026_property_runtime.php');
require_once get_theme_file_path('/libs/query/query2026/wpestate_query_2026_property.php');
require_once get_theme_file_path('/libs/query/query2026/wpestate_query_2026_prepare_arguments.php');
require_once get_theme_file_path('/libs/query/query2026/wpestate_query_2026_prepare_context_arguments.php');
require_once get_theme_file_path('/libs/query/query2026/wpestate_query_2026_property_list_cache.php');
require_once get_theme_file_path('/libs/query/query2026/wpestate_query_2026_prepare_search_arguments.php');



//functions for blog post page
require_once  get_theme_file_path('libs/blog_post/blog_post_functions.php');


//functions for login and register
require_once  get_theme_file_path('libs/classes/WpEstate_Custom_Auth.php');



//property cards functions
require_once  get_theme_file_path('/libs/property_cards_functions/help_functions_cards.php');
require_once  get_theme_file_path('/libs/property_cards_functions/property_cards_functions.php');
require_once  get_theme_file_path('/libs/places_categories_cards/places_categories_cards.php');



require_once  get_theme_file_path('/libs/widget_functions.php');
require_once  get_theme_file_path('/libs/pin_management.php');
require_once  get_theme_file_path('/libs/ajax_functions.php');
require_once  get_theme_file_path('/libs/ajax_functions_property_query.php');
require_once  get_theme_file_path('/libs/ajax_upload.php');
require_once  get_theme_file_path('/libs/3rdparty.php');
require_once  get_theme_file_path('/libs/theme-setup.php');
require_once  get_theme_file_path('/libs/general-settings.php');

// property page functions
require_once  get_theme_file_path('/libs/property_page_functions/property_page_functions.php');
require_once  get_theme_file_path('/libs/property_page_functions/property_page_functions_2.php');
require_once  get_theme_file_path('/libs/property_page_functions/property_layout_functions.php');
require_once  get_theme_file_path('/libs/property_page_functions/gallery_functions.php');


require_once get_template_directory() . '/libs/property_page_functions/property_overview_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_description_and_documents_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_booking_shortcode_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_multi_units_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_energy_savings_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_address_section_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_details_section_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_features_ammenities_section_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_video_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_map_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_virtual_tour_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_walkscore_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_neary_by_yelp_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_mortgage_calculator_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_floor_plan_section_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_views_section_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_schedule_tour_section_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_agent_sections_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_reviews_section_functions.php';
require_once get_template_directory() . '/libs/property_page_functions/property_similar_listings_functions.php';

// agent, agency, developer functions
require_once  get_theme_file_path('/libs/agents/agent_functions.php');
require_once  get_theme_file_path('/libs/agency_developers_functions/agency_developers_functions.php');




require_once  get_theme_file_path('/libs/stats.php');

require_once  get_theme_file_path('/libs/design_functions.php');
require_once  get_theme_file_path('/libs/update.php');

require_once  get_theme_file_path('/libs/wp_admin_functions/wp_admin_functions.php');
require_once  get_theme_file_path('/libs/wp_admin_functions/check_conditions_functions.php');

require_once  get_theme_file_path('/libs/base_functions/styling_functions.php');
require_once  get_theme_file_path('libs/theme-cache.php');
require_once  get_theme_file_path('/libs/multiple_sidebars.php');
require_once  get_theme_file_path('/libs/dashboard_functions/dashboard_functions.php');
require_once  get_theme_file_path('/libs/dashboard_functions/dashboard_actions_functions.php');
require_once  get_theme_file_path('/templates/modals/login_register_modal.php');
require_once  get_theme_file_path('/libs/wpestate_query_functions.php');
require_once  get_theme_file_path('/libs/footer_functions/footer_filter_functions.php');


require_once  get_theme_file_path('/libs/elementor_help_files.php');
require_once  get_theme_file_path('/libs/elementor_help_files2.php');
require_once  get_theme_file_path('/libs/contact_functions.php');

// directory functions
require_once  get_theme_file_path('libs/directory_functions/directory_functions.php');

require_once  get_theme_file_path('/libs/wp_admin_functions/wellcome_screen.php');




