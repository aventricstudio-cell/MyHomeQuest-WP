<?php

namespace ElementorWpResidence;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.2.0
 */
class Plugin {

    /**
     * Instance
     *
     * @since 1.2.0
     * @access private
     * @static
     *
     * @var Plugin The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.2.0
     * @access public
     *
     * @return Plugin An instance of the class.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * widget_scripts
     *
     * Load required plugin core files.
     *
     * @since 1.2.0
     * @access public
     */
    public function widget_scripts() {
        
    }

    /**
     * Include Widgets files
     *
     * Load widgets files
     *
     * @since 1.2.0
     * @access private
     */
    private function include_widgets_files() {

        // Load Helper functions/classes
        require_once( __DIR__ . '/functions/elementor-menu-mobile-walker.php' );
        require_once( __DIR__ . '/functions/elementor-menu-walker.php' );

        // Load Widgets files
        require_once( __DIR__ . '/widgets/helper.php' );
        require_once( __DIR__ . '/widgets/recent-items.php' );
        require_once( __DIR__ . '/widgets/recent-properties-top-bar.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider.php' );
        require_once( __DIR__ . '/widgets/list-items-by-id.php' );
        require_once( __DIR__ . '/widgets/places-slider.php' );
        require_once( __DIR__ . '/widgets/membership-package.php' );
        require_once( __DIR__ . '/widgets/featured-agency-developer.php' );
        require_once( __DIR__ . '/widgets/testimonial.php' );
        require_once( __DIR__ . '/widgets/google_map_property.php' );
        require_once( __DIR__ . '/widgets/list-items-agent.php' );
        require_once( __DIR__ . '/widgets/display_categories.php' );
        require_once( __DIR__ . '/widgets/list-agents.php' );
        require_once( __DIR__ . '/widgets/featured-agent.php' );
        require_once( __DIR__ . '/widgets/featured-article.php' );
        require_once( __DIR__ . '/widgets/featured-property.php' );
        require_once( __DIR__ . '/widgets/login-form.php' );
        require_once( __DIR__ . '/widgets/advanced-search.php' );
        require_once( __DIR__ . '/widgets/contact_us.php' );
        require_once( __DIR__ . '/widgets/contact_form_builder.php' );
        require_once( __DIR__ . '/widgets/properties_slider.php' );
        require_once( __DIR__ . '/widgets/full_map.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties.php' );
        require_once( __DIR__ . '/widgets/wpestate_tabs.php' );
        require_once( __DIR__ . '/widgets/wpestate_accordions.php' );
        require_once( __DIR__ . '/widgets/sliding-box.php' );
        require_once( __DIR__ . '/widgets/properties_slider_v2.php' );
        require_once( __DIR__ . '/widgets/properties_slider_v3.php' );
        require_once( __DIR__ . '/widgets/testimonial_slider.php' );
        require_once( __DIR__ . '/widgets/video_player.php' );
        require_once( __DIR__ . '/widgets/hotspot.php' );      
        require_once( __DIR__ . '/widgets/blog-list.php' );
        require_once( __DIR__ . '/widgets/favorites-list.php' );     
        require_once( __DIR__ . '/widgets/display_categories_as_tabs.php' );   
           
        require_once( __DIR__ . '/widgets/property_page_tab_details.php' );
        require_once( __DIR__ . '/widgets/property_page_accordion_details.php' );
 
        require_once( __DIR__ . '/widgets/property_page_simple_detail_section.php' );
        require_once( __DIR__ . '/widgets/property_page_slider_section.php' );
        require_once( __DIR__ . '/widgets/property_page_agent_card.php' );
        require_once( __DIR__ . '/widgets/property_page_agent_contact.php' );
        require_once( __DIR__ . '/widgets/property_page_related_listings.php' );
        require_once( __DIR__ . '/widgets/property_page_intext_details.php' );
        require_once( __DIR__ . '/widgets/property_page_design_gallery.php' );
        require_once( __DIR__ . '/widgets/property_page_agent_details_intext_details.php' );
    
        require_once( __DIR__ . '/widgets/propert_page_other_agents.php' );
        require_once( __DIR__ . '/widgets/taxonomy_list.php' );
     
        require_once( __DIR__ . '/widgets/wpresidence-grids.php' );
        require_once( __DIR__ . '/widgets/search_form_builder.php' );   
        require_once( __DIR__ . '/widgets/recent-items_card_v1.php' );
        require_once( __DIR__ . '/widgets/recent-items_card_v2.php' );
        require_once( __DIR__ . '/widgets/recent-items_card_v3.php' );
        require_once( __DIR__ . '/widgets/recent-items_card_v4.php' );
        require_once( __DIR__ . '/widgets/recent-items_card_v5.php' );
        require_once( __DIR__ . '/widgets/recent-items_card_v6.php' );
        require_once( __DIR__ . '/widgets/recent-items_card_v7.php' );
        require_once( __DIR__ . '/widgets/recent-items_card_v8.php' );

        require_once( __DIR__ . '/widgets/recent-items-slider_v1.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider_v2.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider_v3.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider_v4.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider_v5.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider_v6.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider_v7.php' );
        require_once( __DIR__ . '/widgets/recent-items-slider_v8.php' );



        require_once( __DIR__ . '/widgets/filter_list_properties_v1.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties_v2.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties_v3.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties_v4.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties_v5.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties_v6.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties_v7.php' );
        require_once( __DIR__ . '/widgets/filter_list_properties_v8.php' );
       
        
        require_once( __DIR__ . '/widgets/wpresidence-agent-grids.php' );
        require_once( __DIR__ . '/widgets/wpresidence-content-grid.php' );
        
        
        require_once( __DIR__ . '/widgets/wpresidence-scroll-spy.php' );
        require_once( __DIR__ . '/widgets/wpresidence-call-to-action.php' );
        // require_once( __DIR__ . '/widgets/wpresidence-properties-by-category.php' );
       // require_once( __DIR__ . '/widgets/advanced_filter_list_properties.php' );

        // Header Footer Widgets
        require_once( __DIR__ . '/widgets/header-footer/site-create-listing.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-currency-changer.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-language.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-language-changer.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-login.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-logo.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-measurement-unit-changer.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-navigation.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-phone.php' );
        require_once( __DIR__ . '/widgets/header-footer/site-social.php' );
        require_once( __DIR__ . '/widgets/header-footer/wpresidence-properties-by-category.php' );


       // single agent widgets
        require_once( __DIR__ . '/widgets/agent/single-agent-meta.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-name.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-description.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-image.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-address.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-title.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-member-of.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-call-button.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-sendmail-button.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-whatsapp-button.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-star-reviews.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-social.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-custom-fields.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-specialities.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-reviews.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-contact-details.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-listings.php' );
        require_once( __DIR__ . '/widgets/agent/single-agent-contact-form.php' );



        // single agency widgets 
        require_once( __DIR__ . '/widgets/agency/single-agency-meta.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-name.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-description.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-image.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-excerpt.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-call-button.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-whatsapp-button.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-star-reviews.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-social.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-opening-hours.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-taxes.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-website.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-license.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-languages.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-specialities.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-reviews.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-map.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-contact-details.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-listings.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-agents-list.php' );
        require_once( __DIR__ . '/widgets/agency/single-agency-contact-form.php' );



        // single developers widgets 
        require_once( __DIR__ . '/widgets/developer/single-developer-meta.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-name.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-description.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-image.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-excerpt.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-call-button.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-whatsapp-button.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-star-reviews.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-social.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-opening-hours.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-taxes.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-website.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-license.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-languages.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-specialities.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-reviews.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-map.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-contact-details.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-listings.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-agents-list.php' );
        require_once( __DIR__ . '/widgets/developer/single-developer-contact-form.php' );



        // single post widgets 
        require_once( __DIR__ . '/widgets/single-post/single-post-title.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-content.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-excerpt.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-featured-image.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-author-box.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-comments.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-navigation.php' );

        require_once( __DIR__ . '/widgets/single-post/single-post-breadcrumbs.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-social.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-meta-info.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-related-posts.php' );
        require_once( __DIR__ . '/widgets/single-post/single-post-slider.php' );




        // single proprty widgets 
        require_once( __DIR__ . '/widgets/single-property-widgets/property_page_simple_detail.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-breadcrumbs.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-title.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-price.php' );     
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-price-info.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-address.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-addto-favorite.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-status.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-content.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-excerpt.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-featured_image.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property_masonary_gallery1.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property_masonary_gallery2.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property_classic_slider.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property_horizontal_slider.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property_vertical_slider.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property_three_items_slider.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property_full_width_slider.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-header-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-overview-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-description-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-address-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-details-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-features-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-video-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-map-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-virtual-tour-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-walkscore-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-calculator-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-floorplans-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-statistics-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-reviews-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-yelp-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-similar-listings-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-subunits-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-agec_form_v2-section.php' );
        require_once( __DIR__ . '/widgets/single-property-widgets/property-page-agec_form_v2-sidebar-section.php' );
        
        
        require_once( __DIR__ . '/widgets/single-property-widgets/property_page_schedule_tour.php' );



        require_once( __DIR__ . '/widgets/category_widgets/term-title.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-description.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-parent.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-city-location.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-featured-image.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-gallery.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-tagline.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term_custom_field_widget.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-maps.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-breadcrumbs.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term-header.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term_listings_number.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term_documents.php' );
        require_once( __DIR__ . '/widgets/category_widgets/term_listings.php' );

    }

    /**
     * Register Widgets
     *
     * Register new Elementor widgets.
     *
     * @since 1.2.0
     * @access public
     */
    public function register_widgets() {
        // Its is now safe to include Widgets files
        $this->include_widgets_files();
        
        
    //    \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_advanced());
        // Register Widgets
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Blog_Post_List());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Search_Form_Builder());
      
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items());
         \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Properties_Top_Bar());
         
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_ListItems_ByID());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Properties_Slider());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Places_Slider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Display_Categories());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Display_Categories_As_Tabs());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Grids());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Taxonomy_List());
  

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_ListItems_Agent());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_List_Agents());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Featured_Agent());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Featured_Agency_Developer());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Featured_Article());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Featured_Property());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Advanced_Search());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Contact_Us());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Contact_Form_Builder());


        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Full_Map());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Google_Map_Property());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties());


        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Login_Form());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Membership_Package());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Testimonial());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Tabs());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Accordions());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Sliding_Box());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Properties_Slider_v2());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Properties_Slider_v3());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Testimonial_Slider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Video_Player()); 
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_HotSpots());    
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agent_Grids());  
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Favorite_List());  
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Content_grid());  
            
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V1());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V2());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V3());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V4());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V5());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V6());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V7());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_Card_V8());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v1());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v2());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v3());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v4());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v5());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v6());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v7());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Recent_Items_SLider_v8());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v1());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v2());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v3());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v4());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v5());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v6());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v7());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Filter_List_Properties_v8());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\WPresidence_Scroll_Spy_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Call_To_Action());




        // Header Footer Widgets
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Logo());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Navigation_Menu());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Login());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Create_Listing());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Currency_Changer());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Measurement_Unit_Changer());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Social());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Phone());       
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Language());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Footer_Properties_By_Category());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Language_Changer());


        // single agent widgets 
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agent_Single_Detail());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agent_Single_Detail());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Address());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Member_Of());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Name());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Send_Mail());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Image());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Social());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Star_Reviews());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Title_Position());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Whatsapp_Button());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Description());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Call_Button());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Listings());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agent_Custom_Fields());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agent_Specialities());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agent_Contact_Details());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agent_Reviews());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agent_Contact_Form());


        // single agency widgets 

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Single_Detail());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Single_Website());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Single_License());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Single_Opening_Hours());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Single_Taxes());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Single_Languages());
        
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Name());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Image());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Description());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Excerpt());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Social());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Whatsapp_Button());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Call_Button());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Star_Reviews());
        
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Specialities());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Contact_Details());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Reviews());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Agency_Map());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Listings());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Agents());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Agency_Contact_Form());


        // single developers widgets 

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Single_Detail());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Single_Website());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Single_License());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Single_Opening_Hours());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Single_Taxes());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Single_Languages());
        
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Name());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Image());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Description());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Excerpt());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Social());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Whatsapp_Button());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Call_Button());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Star_Reviews());
        
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Specialities());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Contact_Details());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Reviews());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Developer_Map());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Listings());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Agents());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Developer_Contact_Form());



        // single post widgets

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Title());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Content());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Excerpt());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Featured_Image());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Author_Box());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Navigation());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Comments());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Breadcrumbs());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Social());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Meta_Info());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Related_Posts());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Slider());


        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Title());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Breadcrumbs());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Price());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Additional_Price_Info());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Address());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Add_To_Favorites());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Status());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Content());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Excerpt());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Featured_Image());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Masonary_Gallery1());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Masonary_Gallery2());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Classic_Slider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Horizontal_Slider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Vertical_Slider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Three_Items_Slider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Full_Width_Slider());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Header_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Overview_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Description_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Address_Section());





        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Details_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Features_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Video_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Map_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Virtual_Tour_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Walkscore_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Calculator_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_FloorPlan_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Statistics_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Reviews_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Yelp_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Similar_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Subunits_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Agent_Form_Section());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Agent_Form2_Section());






    
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Tab_Details());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Accordion_Details());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Simple_Detail());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Detail_Section());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Agent_Card());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Agent_Contact());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Related_Listings());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Intext_Details());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Design_Gallery());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Agent_Details_Intext());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Other_Agents());
        
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Property_Page_Schedule_Tour());


        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Title());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Description());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Parent());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_City_Location());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Featured_Image());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Gallery());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Tagline());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpestate_Term_Custom_Field_Widget());

        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Map());


        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Page_Breadcrumbs());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Header());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Property_Count());
        \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Documents());

            \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Term_Listings());
    }

    /**
     * Adds custom Elementor widget categories and conditionally prioritizes specific categories
     * based on the post type and template being edited.
     * 
     * For 'wpestate-studio' post types, this method will move the relevant category to the top
     * of the Elementor widget panel based on the 'wpestate_head_foot_template' meta value:
     * - wpestate_single_property_page → wpresidence_property (first)
     * - wpestate_single_agent → wpestate_single_agent_category (first)
     * - wpestate_single_agency → wpestate_single_agency_category (first)
     * - wpestate_single_developer → wpestate_single_developer_category (first)
     * - wpestate_single_post → wpestate_single_post_category (first)
     * 
     * All other categories remain in their default positions.
     * 
     * @since 1.0.0
     * @param \Elementor\Elements_Manager $elements_manager The Elementor elements manager instance
     * @return void
     */
    public function add_elementor_widget_categories($elements_manager) {
        // ========================================
        // GET CURRENT POST INFORMATION
        // ========================================
        
        // Try to get the current post ID using multiple fallback methods
        // This ensures we can detect the post ID in various contexts (frontend, admin, AJAX)
        $post_id = get_the_ID();
        
        if (!$post_id) {
            // Fallback 1: Check $_GET for post ID (common in admin edit screens)
            if (isset($_GET['post'])) {
                $post_id = intval($_GET['post']);
            }
            
            // Fallback 2: Check $_POST for editor post ID (AJAX requests)
            if (!$post_id && isset($_POST['editor_post_id'])) {
                $post_id = intval($_POST['editor_post_id']);
            }
            
            // Fallback 3: Check for Elementor specific post ID
            if (!$post_id && isset($_POST['post_id'])) {
                $post_id = intval($_POST['post_id']);
            }
            
            // Fallback 4: Use global $post object
            if (!$post_id) {
                global $post;
                if ($post && isset($post->ID)) {
                    $post_id = $post->ID;
                }
            }
        }
        
        // Get the post type for the current post
        $post_type = '';
        if ($post_id) {
            $post_type = get_post_type($post_id);
        }
        
        // Get the template meta value that determines which widgets should be prioritized
        $template = '';
        if ($post_id) {
            $template = get_post_meta($post_id, 'wpestate_head_foot_template', true);
        }
        
        // ========================================
        // DETERMINE PRIORITY CATEGORY
        // ========================================
        
        // Check if we should prioritize a specific category and determine which one
        $priority_category = false;
        
        if ($post_type === 'wpestate-studio' && $post_id) {
            // Define mappings between template values and their corresponding widget categories
            // This allows each template type to have its most relevant widgets appear first
            $template_category_map = [
                'wpestate_single_property_page' => 'wpresidence_property',
                'wpestate_single_agent' => 'wpestate_single_agent_category',
                'wpestate_single_agency' => 'wpestate_single_agency_category',
                'wpestate_single_developer' => 'wpestate_single_developer_category',
                'wpestate_single_post' => 'wpestate_single_post_category',
                'wpestate_category_page' => 'category_widgets',
                
                'wpestate_template_header' => 'wpresidence_header',
                'wpestate_template_before_header' => 'wpresidence_header',
                'wpestate_template_after_header' => 'wpresidence_header',
                'wpestate_template_footer' => 'wpresidence_header',
                'wpestate_template_after_footer' => 'wpresidence_header',

                'wpestate_template_before_footer' => 'wpresidence_header',







            ];
            
            // Check if the current template has a corresponding priority category
            if (isset($template_category_map[$template])) {
                $priority_category = $template_category_map[$template];
            }
        }
        
        // ========================================
        // REGISTER WIDGET CATEGORIES
        // ========================================
        
        if ($priority_category) {
            // PRIORITY MODE: Register all categories and then reorder to put the priority one first
            
            // Register core WpResidence widget categories
            $elements_manager->add_category(
                'wpresidence', [
                    'title' => __('WpResidence Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-home',
                ]
            );
            
            $elements_manager->add_category(
                'wpresidence_property', [
                    'title' => __('WpResidence Property Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-home',
                ]
            );
            
            $elements_manager->add_category(
                'wpresidence_extented', [
                    'title' => __('WpResidence Property List Widgets with Card Variations', 'residence-elementor'),
                    'icon' => 'fa fa-home',
                ]
            );

            $elements_manager->add_category(
                'category_widgets', [
                    'title' => __('Category Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-folder',
                ]
            );
            
            // Register template-specific widget categories
            // Each category is designed for a specific page template type
            
            $elements_manager->add_category(
                'wpestate_single_agent_category', [
                    'title' => __('WpResidence Agent Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-user', // User icon for agent-related widgets
                ]
            );
            
            $elements_manager->add_category(
                'wpestate_single_agency_category', [
                    'title' => __('WpResidence Agency Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-building', // Building icon for agency-related widgets
                ]
            );
            
            $elements_manager->add_category(
                'wpestate_single_developer_category', [
                    'title' => __('WpResidence Developer Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-hammer', // Hammer icon for developer-related widgets
                ]
            );
            
            $elements_manager->add_category(
                'wpestate_single_post_category', [
                    'title' => __('WpResidence Post Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-newspaper', // Newspaper icon for post-related widgets
                ]
            );
               $elements_manager->add_category(
            'wpresidence_header', [
            'title' => __('WpResidence Header & Footer Widgets', 'residence-elementor'),
            'icon' => 'fa fa-home',
            ]
        );
            // Reorder categories to move the priority category to the top
            // This ensures the most relevant widgets appear first in the Elementor panel
            $this->reorder_categories_to_top($elements_manager, $priority_category);
            
        } else {
            // DEFAULT MODE: Register all categories in their natural order
            // This happens when not editing a wpestate-studio post or when no template priority is set
            
            // Register core WpResidence widget categories in default order
            $elements_manager->add_category(
                'wpresidence', [
                    'title' => __('WpResidence Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-home',
                ]
            );
            
            $elements_manager->add_category(
                'wpresidence_property', [
                    'title' => __('WpResidence Property Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-home',
                ]
            );
            
            $elements_manager->add_category(
                'wpresidence_extented', [
                    'title' => __('WpResidence Property List Widgets with Card Variations', 'residence-elementor'),
                    'icon' => 'fa fa-home',
                ]
            );

            $elements_manager->add_category(
                'category_widgets', [
                    'title' => __('Category Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-folder',
                ]
            );

            // Register template-specific categories in default order
            // These will appear at the bottom of the category list
            $elements_manager->add_category(
                'wpestate_single_agent_category', [
                    'title' => __('WpResidence Agent Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-user',
                ]
            );
            
            $elements_manager->add_category(
                'wpestate_single_agency_category', [
                    'title' => __('WpResidence Agency Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-building',
                ]
            );
            
            $elements_manager->add_category(
                'wpestate_single_developer_category', [
                    'title' => __('WpResidence Developer Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-hammer',
                ]
            );
            
            $elements_manager->add_category(
                'wpestate_single_post_category', [
                    'title' => __('WpResidence Post Page Widgets', 'residence-elementor'),
                    'icon' => 'fa fa-newspaper',
                ]
            );
        }
    }

    /**
     * Reorders Elementor widget categories to place a specific category at the top
     * 
     * This method uses a closure to access the private $categories property of the 
     * Elements_Manager and reorders it using uksort. Only the specified priority 
     * category is moved to the top - all other categories maintain their relative positions.
     * 
     * @since 1.0.0
     * @param \Elementor\Elements_Manager $elements_manager The Elementor elements manager instance
     * @param string $priority_category The category slug that should be moved to the top
     * @return void
     */
    private function reorder_categories_to_top($elements_manager, $priority_category) {
        // Create a closure that can access the private $categories property
        // This is necessary because Elementor doesn't provide a public method to reorder categories
        $reorder_cats = function() use($priority_category) {
            // Use uksort to reorder the categories array by key (category slug)
            uksort($this->categories, function($keyOne, $keyTwo) use($priority_category) {
                // Move the priority category to the top of the list
                if ($keyOne === $priority_category) {
                    return -1; // $keyOne comes before $keyTwo
                }
                if ($keyTwo === $priority_category) {
                    return 1; // $keyTwo comes before $keyOne
                }
                
                // For all other categories (including other wpresidence categories), 
                // maintain their original relative order by returning 0 (equal)
                return 0;
            });
        };
        
        // Execute the closure in the context of the $elements_manager object
        // This allows us to modify the private $categories property
        $reorder_cats->call($elements_manager);
    }







    function wpresidence_elementor_widgets_dependencies() {

        /* Scripts */
        wp_register_script( 'wpresidence-scroll-spy-script', plugins_url( 'assets/js/scroll-spy.js', __FILE__ ) );

        /* Styles */
        wp_register_style( 'wpresidence-call-to-action-style', plugins_url( 'assets/css/call-to-action.css', __FILE__ ) );
        wp_register_style( 'wpresidence-scroll-spy-style', plugins_url( 'assets/css/scroll-spy.css', __FILE__ ) );
        wp_register_style( 'wpresidence-author-box-style', plugins_url( 'assets/css/author-box.css', __FILE__ ) );
        wp_register_style( 'wpresidence-post-navigation-style', plugins_url( 'assets/css/post-navigation.css', __FILE__ ) );

    }

    public function __construct() {

        // Register widget scripts
        add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);

        // Register widgets
        add_action('elementor/widgets/register', [$this, 'register_widgets']);

        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);

        add_action( 'wp_enqueue_scripts', [$this, 'wpresidence_elementor_widgets_dependencies'] );
    }

}

// Instantiate Plugin Class
Plugin::instance();

function wpestate_prop_page_return_id() {
    return 26113;
}
