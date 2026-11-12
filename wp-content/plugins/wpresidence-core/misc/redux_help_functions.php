<?php


/**
 * Retrieves an array of page IDs and titles that use the 'page_property_design.php' template.
 *
 * This function uses transients for persistent caching regardless of object cache availability.
 * Avoids the unnecessary FOUND_ROWS() query by using no_found_rows parameter.
 *
 * @return array An associative array of page IDs and titles.
 */
function wpestate_property_page_template_function() {
    // Use transients for reliable caching even without object cache
    $cache_key = 'wpestate_property_page_templates';
    if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
        $cache_key = wpestate_set_transient_name_multilang( 'wpestate_property_page_templates' );
    }
    $return_array = get_transient($cache_key);

    if (false === $return_array) {
        $return_array = array();

        $args = array(
            'post_type'      => 'wpestate-studio',
            'meta_key'       => 'wpestate_head_foot_template',
            'meta_value'     => 'wpestate_single_property_page',
            'posts_per_page' => 100, // Limit to 100 pages for performance
            'no_found_rows'  => true, // Prevents the duplicate COUNT query
            'fields'         => 'ids', // Only get IDs initially for better performance
        );
        
        $pages = new WP_Query($args);
        
        if (!empty($pages->posts)) {
            foreach ($pages->posts as $page_id) {
                // Get title directly - more efficient than the loop with the_post()
                $return_array[$page_id] = get_the_title($page_id);
            }
        }
        
        // Store in transient
        set_transient($cache_key, $return_array, HOUR_IN_SECONDS);
    }
    
    return $return_array;
}



/**
 * Process and save Redux framework options for WP Residence Theme
 * 
 * This function is triggered when Redux options are saved, handling various
 * specialized processing tasks including currency exchange settings, search options,
 * user membership scheduling, and more.
 * 
 * @param array $value  The values being saved in the Redux options panel
 * @param array $value2 Additional data provided by Redux
 * @return array The processed values to be saved
 */
add_action('redux/options/wpresidence_admin/saved', 'wprentals_redux_on_save',10,2);
function wprentals_redux_on_save($value,$value2){

    // Update WordPress site icon with the favicon image from Redux
    if( isset($value['wp_estate_favicon_image']) && isset( $value['wp_estate_favicon_image']['id'])  ){
       update_option( 'site_icon', $value['wp_estate_favicon_image']['id'] );
    }

    // Save custom AJAX handler setting
    if( isset( $value['wp_estate_use_custom_ajaxhandler']) ){
          update_option('wp_estate_use_custom_ajaxhandler',$value['wp_estate_use_custom_ajaxhandler']);
    }

    // Update property page content and structure options from WordPress options
    Redux::setOption('wpresidence_admin','wpestate_property_page_content', get_option('wpestate_property_page_content',true) );//admin
    Redux::setOption('wpresidence_admin','wpestate_property_unit_structure',  get_option('wpestate_property_unit_structure',true)  );//front

    // Process advanced search settings
    if(isset(  $value['wpestate_set_search']['adv_search_what'])){
        Redux::setOption('wpresidence_admin','wp_estate_adv_search_what',  $value['wpestate_set_search']['adv_search_what'] );
    }
    if(isset(  $value['wpestate_set_search']['adv_search_how'])){
        Redux::setOption('wpresidence_admin','wp_estate_adv_search_how',  $value['wpestate_set_search']['adv_search_how']);
    }
    if(isset(  $value['wpestate_set_search']['adv_search_label'])){
        Redux::setOption('wpresidence_admin','wp_estate_adv_search_label', $value['wpestate_set_search']['adv_search_label']);
    }
    if(isset(  $value['wpestate_set_search']['search_field_label'])){
        Redux::setOption('wpresidence_admin','wp_estate_search_field_label',  $value['wpestate_set_search']['search_field_label']);
    }

    // Update URL rewrites option
    if($value['wp_estate_url_rewrites']){
        update_option('wp_estate_url_rewrites',$value['wp_estate_url_rewrites']);
    }

    // Setup default advanced search fields when search type is changed to new or old type
    if( isset( $value['wp_estate_adv_search_type'] ) && ( $value['wp_estate_adv_search_type'] =='newtype' || $value['wp_estate_adv_search_type'] =='oldtype') ){
            // Set default search fields for classic search
            $adv_search_what    =   array('Location','check_in','check_out','guest_no');
            $adv_search_how     =   array('like','like','like','greater');

            Redux::setOption('wpresidence_admin','wp_estate_adv_search_what_classic',$adv_search_what);
            Redux::setOption('wpresidence_admin','wp_estate_adv_search_how_classic',$adv_search_how);

            // Set default search fields for half map search
            $adv_search_what_classic_half    =   array('Location','check_in','check_out','guest_no','property_rooms','property_category','property_action_category','property_bedrooms','property_bathrooms','property_price');
            $adv_search_how_classic_half     =   array('like','like','like','greater','greater','like','like','greater','greater','between');
            Redux::setOption('wpresidence_admin','wp_estate_adv_search_what_half',$adv_search_what_classic_half);
            Redux::setOption('wpresidence_admin','wp_estate_adv_search_how_half',$adv_search_how_classic_half);
    }

    // Schedule search alert emails if save search option is enabled
    if ( isset( $value['wp_estate_show_save_search'] ) ){
        $show_save_search   =   $value['wp_estate_show_save_search'];
        $search_alert       =   '';
        if(isset($value['wp_estate_search_alert'])){
            $search_alert       = $value['wp_estate_search_alert'];
        }

        wp_estate_schedule_email_events( $show_save_search,$search_alert);
    }

    // Handle paid membership scheduling
    if ( isset( $value['wp_estate_paid_submission'] ) ){
        if( $value['wp_estate_paid_submission']=='membership'){
            // Schedule regular checks for user membership status
            wp_estate_schedule_user_check();
        }else{
            // Clear scheduled checks if membership is disabled
            wp_clear_scheduled_hook('wpestate_check_for_users_event');
        }
    }

    // Handle automatic currency exchange rate updates
    if ( isset($value['wp_estate_auto_curency']) ){
        if( $value['wp_estate_auto_curency']=='yes' ){
            // Schedule daily currency exchange rate updates
            wp_estate_enable_load_exchange();
        }else{
            // Clear scheduled currency updates if auto-update is disabled
            wp_clear_scheduled_hook('wpestate_load_exchange_action');
        }
    }

    // Flush rewrite rules if URL rewrites have changed
    if(isset($value['wp_estate_url_rewrites'])){
        flush_rewrite_rules();
    }

    // Process theme slider manual settings
    if( isset($value['wp_estate_theme_slider_manual']) && $value['wp_estate_theme_slider_manual']!=''){
        $theme_slider = array();
        // Convert comma-separated IDs to array
        $new_ids = explode(',', $value['wp_estate_theme_slider_manual']);

        foreach($new_ids as $key=>$value){
            $theme_slider[]=$value;
        }
        Redux::setOption('wpresidence_admin','wp_estate_theme_slider',$theme_slider);
    }

    // Update custom fields list
    if( isset($value['wpestate_custom_fields_list']) && $value['wpestate_custom_fields_list']!=''){
        update_option('wpestate_custom_fields_list',$value['wpestate_custom_fields_list']);
    }

    // Browser caching configuration for .htaccess
    $value_httaces='<IfModule mod_deflate.c>
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

    // Save browser caching settings if enabled
    if( isset($value['info_warning_enable_browser']) && $value['info_warning_enable_browser']!=''){
         Redux::setOption('wpresidence_admin','info_warning_enable_browser',$value_httaces);
    }

    // Return the processed values
    return $value;
}



/**
 * Get property features for Redux options
 * 
 * Uses cached taxonomy terms for better performance
 * Handles WPML translations appropriately
 * 
 * @return array Array of property features as slug => name
 */
function wpresidence_redux_advanced_exteded() {
    $return_array = array();
    
    // Get cached terms instead of querying the database
    $terms = wpestate_get_cached_terms('property_features');
    
    // Handle WPML language switching if needed
    if (defined('ICL_SITEPRESS_VERSION') && !is_admin()) {
        $current_language = apply_filters('wpml_current_language', null);
        $default_language = apply_filters('wpml_default_language', null);
        
        if ($current_language != $default_language) {
            $original_terms = null;
            
            // Switch to default language to get original terms
            do_action('wpml_switch_language', $default_language);
            $original_terms = wpestate_get_cached_terms('property_features');
            
            // Switch back to current language
            do_action('wpml_switch_language', $current_language);
            
            // Process terms with proper language handling
            foreach ($terms as $key => $term) {
                $term_name = $term->name;
                $term_slug = $term->slug;
                $original_term_id = apply_filters('wpml_object_id', $term->term_id, 'property_features', true, $default_language);
                
                // Find the original term by ID
                $original_term_key = array_search($original_term_id, array_column($original_terms, 'term_id'));
                
                if ($original_term_key !== false) {
                    $original_term = $original_terms[$original_term_key];
                    $term_slug = $original_term->slug;
                    $return_array[$term_slug] = $term_name;
                }
            }
            
            return $return_array;
        }
    }
    
    // Standard processing when not using WPML or in default language
    foreach ($terms as $checker => $term) {
        $return_array[$term->slug] = $term->name;
    }
    
    return $return_array;
}











function wpresidence_prepare_non_latin($key,$label){

    $label  =  stripslashes( $label);

    $slug   =   stripslashes($key);
    $slug   =   str_replace(' ','-',$key);
    $slug   =   htmlspecialchars ( $slug ,ENT_QUOTES);
    $slug   =   wpestate_limit45(sanitize_title( $slug ));
    $slug   =   sanitize_key($slug);


    $return=array();
    $return['key']=trim($slug);
    $return['label']=trim($label);
    return $return;
}


function wpresidence_core_redux_yelp(){
     $yelp_terms_array =
            array (
                'active'            =>  array( 'category' => esc_html__('Active Life','wpresidence-core'),
                                                'category_sign' => 'fa fa-bicycle'),
                'arts'              =>  array( 'category' => esc_html__('Arts & Entertainment','wpresidence-core'),
                                               'category_sign' => 'fa fa-music') ,
                'auto'              =>  array( 'category' => esc_html__('Automotive','wpresidence-core'),
                                                'category_sign' => 'fa fa-car' ),
                'beautysvc'         =>  array( 'category' => esc_html__('Beauty & Spas','wpresidence-core'),
                                                'category_sign' => 'fa fa-female' ),
                'education'         => array(  'category' => esc_html__('Education','wpresidence-core'),
                                                'category_sign' => 'fa fa-graduation-cap' ),
                'eventservices'     => array(  'category' => esc_html__('Event Planning & Services','wpresidence-core'),
                                                'category_sign' => 'fa fa-birthday-cake' ),
                'financialservices' => array(  'category' => esc_html__('Financial Services','wpresidence-core'),
                                                'category_sign' => 'fa fa-money' ),
                'food'              => array(  'category' => esc_html__('Food','wpresidence-core'),
                                                'category_sign' => 'fa fa fa-cutlery' ),
                'health'            => array(  'category' => esc_html__('Health & Medical','wpresidence-core'),
                                                'category_sign' => 'fa fa-medkit' ),
                'homeservices'      => array(  'category' =>esc_html__('Home Services ','wpresidence-core'),
                                                'category_sign' => 'fa fa-wrench' ),
                'hotelstravel'      => array(  'category' => esc_html__('Hotels & Travel','wpresidence-core'),
                                                'category_sign' => 'fa fa-bed' ),
                'localflavor'       => array(  'category' => esc_html__('Local Flavor','wpresidence-core'),
                                                'category_sign' => 'fa fa-coffee' ),
                'localservices'     => array(  'category' => esc_html__('Local Services','wpresidence-core'),
                                                'category_sign' => 'fa fa-dot-circle-o' ),
                'massmedia'         => array(  'category' => esc_html__('Mass Media','wpresidence-core'),
                                                'category_sign' => 'fa fa-television' ),
                'nightlife'         => array(  'category' => esc_html__('Nightlife','wpresidence-core'),
                                                'category_sign' => 'fa fa-glass' ),
                'pets'              => array(  'category' => esc_html__('Pets','wpresidence-core'),
                                                'category_sign' => 'fa fa-paw' ),
                'professional'      => array(  'category' => esc_html__('Professional Services','wpresidence-core'),
                                                'category_sign' => 'fa fa-suitcase' ),
                'publicservicesgovt'=> array(  'category' => esc_html__('Public Services & Government','wpresidence-core'),
                                                'category_sign' => 'fa fa-university' ),
                'realestate'        => array(  'category' => esc_html__('Real Estate','wpresidence-core'),
                                                'category_sign' => 'fa fa-building-o' ),
                'religiousorgs'     => array(  'category' => esc_html__('Religious Organizations','wpresidence-core'),
                                                'category_sign' => 'fa fa-cloud' ),
                'restaurants'       => array(  'category' => esc_html__('Restaurants','wpresidence-core'),
                                                'category_sign' => 'fa fa-cutlery' ),
                'shopping'          => array(  'category' => esc_html__('Shopping','wpresidence-core'),
                                                'category_sign' => 'fa fa-shopping-bag' ),
                'transport'         => array(  'category' => esc_html__('Transportation','wpresidence-core'),
                                                'category_sign' => 'fa fa-bus' )
    );

    $to_return=array();
    foreach($yelp_terms_array as $key=>$term){
            $to_return[$key]=$term['category'];
    }
    return $to_return;
}



function wpresidence_return_google_fonts(){
    $google_fonts_array = array(
                'ABeeZee',
                'Abel',
                'Abril+Fatface',
                'Aclonica',
                'Acme',
                'Actor',
                'Adamina',
                'Advent+Pro',
                'Aguafina+Script',
                'Akronim',
                'Aladin',
                'Aldrich',
                'Alef',
                'Alegreya',
                'Alegreya+Sans',
                'Alegreya+Sans+SC',
                'Alegreya+SC',
                'Alex+Brush',
                'Alfa+Slab+One',
                'Alice',
                'Alike',
                'Alike+Angular',
                'Allan',
                'Allerta',
                'Allerta+Stencil',
                'Allura',
                'Almendra',
                'Almendra+Display',
                'Almendra+SC',
                'Amarante',
                'Amaranth',
                'Amatic+SC',
                'Amethysta',
                'Amiri',
                'Amita',
                'Anaheim',
                'Andada',
                'Andika',
                'Angkor',
                'Annie+Use+Your+Telescope',
                'Anonymous+Pro',
                'Antic',
                'Antic+Didone',
                'Antic+Slab',
                'Anton',
                'Arapey',
                'Arbutus',
                'Arbutus+Slab',
                'Architects+Daughter',
                'Archivo+Black',
                'Archivo+Narrow',
                'Arimo',
                'Arizonia',
                'Armata',
                'Artifika',
                'Arvo',
                'Arya',
                'Asap',
                'Asar',
                'Asset',
                'Astloch',
                'Asul',
                'Atomic+Age',
                'Aubrey',
                'Audiowide',
                'Autour+One',
                'Average',
                'Average+Sans',
                'Averia+Gruesa+Libre',
                'Averia+Libre',
                'Averia+Sans+Libre',
                'Averia+Serif+Libre',
                'Bad+Script',
                'Balthazar',
                'Bangers',
                'Basic',
                'Battambang',
                'Baumans',
                'Bayon',
                'Belgrano',
                'Belleza',
                'BenchNine',
                'Bentham',
                'Berkshire+Swash',
                'Bevan',
                'Bigelow+Rules',
                'Bigshot+One',
                'Bilbo',
                'Bilbo+Swash+Caps',
                'Biryani',
                'Bitter',
                'Black+Ops+One',
                'Bokor',
                'Bonbon',
                'Boogaloo',
                'Bowlby+One',
                'Bowlby+One+SC',
                'Brawler',
                'Bree+Serif',
                'Bubblegum+Sans',
                'Bubbler+One',
                'Buda',
                'Buenard',
                'Butcherman',
                'Butterfly+Kids',
                'Cabin',
                'Cabin+Condensed',
                'Cabin+Sketch',
                'Caesar+Dressing',
                'Cagliostro',
                'Calligraffitti',
                'Cambay',
                'Cambo',
                'Candal',
                'Cantarell',
                'Cantata+One',
                'Cantora+One',
                'Capriola',
                'Cardo',
                'Carme',
                'Carrois+Gothic',
                'Carrois+Gothic+SC',
                'Carter+One',
                'Catamaran',
                'Caudex',
                'Cedarville+Cursive',
                'Ceviche+One',
                'Changa+One',
                'Chango',
                'Chau+Philomene+One',
                'Chela+One',
                'Chelsea+Market',
                'Chenla',
                'Cherry+Cream+Soda',
                'Cherry+Swash',
                'Chewy',
                'Chicle',
                'Chivo',
                'Chonburi',
                'Cinzel',
                'Cinzel+Decorative',
                'Clicker+Script',
                'Coda',
                'Coda+Caption',
                'Codystar',
                'Combo',
                'Comfortaa',
                'Coming+Soon',
                'Concert+One',
                'Condiment',
                'Content',
                'Contrail+One',
                'Convergence',
                'Cookie',
                'Copse',
                'Corben',
                'Courgette',
                'Cousine',
                'Coustard',
                'Covered+By+Your+Grace',
                'Crafty+Girls',
                'Creepster',
                'Crete+Round',
                'Crimson+Text',
                'Croissant+One',
                'Crushed',
                'Cuprum',
                'Cutive',
                'Cutive+Mono',
                'Damion',
                'Dancing+Script',
                'Dangrek',
                'Dawning+of+a+New+Day',
                'Days+One',
                'Dekko',
                'Delius',
                'Delius+Swash+Caps',
                'Delius+Unicase',
                'Della+Respira',
                'Denk+One',
                'Devonshire',
                'Dhurjati',
                'Didact+Gothic',
                'Diplomata',
                'Diplomata+SC',
                'Domine',
                'Donegal+One',
                'Doppio+One',
                'Dorsa',
                'Dosis',
                'Dr+Sugiyama',
                'Droid+Sans',
                'Droid+Sans+Mono',
                'Droid+Serif',
                'Duru+Sans',
                'Dynalight',
                'Eagle+Lake',
                'Eater',
                'EB+Garamond',
                'Economica',
                'Eczar',
                'Ek+Mukta',
                'Electrolize',
                'Elsie',
                'Elsie+Swash+Caps',
                'Emblema+One',
                'Emilys+Candy',
                'Engagement',
                'Englebert',
                'Enriqueta',
                'Erica+One',
                'Esteban',
                'Euphoria+Script',
                'Ewert',
                'Exo',
                'Exo+2',
                'Expletus+Sans',
                'Fanwood+Text',
                'Fascinate',
                'Fascinate+Inline',
                'Faster+One',
                'Fasthand',
                'Fauna+One',
                'Federant',
                'Federo',
                'Felipa',
                'Fenix',
                'Finger+Paint',
                'Fira+Mono',
                'Fira+Sans',
                'Fjalla+One',
                'Fjord+One',
                'Flamenco',
                'Flavors',
                'Fondamento',
                'Fontdiner+Swanky',
                'Forum',
                'Francois+One',
                'Freckle+Face',
                'Fredericka+the+Great',
                'Fredoka+One',
                'Freehand',
                'Fresca',
                'Frijole',
                'Fruktur',
                'Fugaz+One',
                'Gabriela',
                'Gafata',
                'Galdeano',
                'Galindo',
                'Gentium+Basic',
                'Gentium+Book+Basic',
                'Geo',
                'Geostar',
                'Geostar+Fill',
                'Germania+One',
                'GFS+Didot',
                'GFS+Neohellenic',
                'Gidugu',
                'Gilda+Display',
                'Give+You+Glory',
                'Glass+Antiqua',
                'Glegoo',
                'Gloria+Hallelujah',
                'Goblin+One',
                'Gochi+Hand',
                'Gorditas',
                'Goudy+Bookletter+1911',
                'Graduate',
                'Grand+Hotel',
                'Gravitas+One',
                'Great+Vibes',
                'Griffy',
                'Gruppo',
                'Gudea',
                'Gurajada',
                'Habibi',
                'Halant',
                'Hammersmith+One',
                'Hanalei',
                'Hanalei+Fill',
                'Handlee',
                'Hanuman',
                'Happy+Monkey',
                'Headland+One',
                'Henny+Penny',
                'Herr+Von+Muellerhoff',
                'Hind',
                'Holtwood+One+SC',
                'Homemade+Apple',
                'Homenaje',
                'Iceberg',
                'Iceland',
                'IM+Fell+Double+Pica',
                'IM+Fell+Double+Pica+SC',
                'IM+Fell+DW+Pica',
                'IM+Fell+DW+Pica+SC',
                'IM+Fell+English',
                'IM+Fell+English+SC',
                'IM+Fell+French+Canon',
                'IM+Fell+French+Canon+SC',
                'IM+Fell+Great+Primer',
                'IM+Fell+Great+Primer+SC',
                'Imprima',
                'Inconsolata',
                'Inder',
                'Indie+Flower',
                'Inika',
                'Inknut+Antiqua',
                'Inter',
                'Irish+Grover',
                'Istok+Web',
                'Italiana',
                'Italianno',
                'Itim',
                'Jacques+Francois',
                'Jacques+Francois+Shadow',
                'Jaldi',
                'Jim+Nightshade',
                'Jockey+One',
                'Jolly+Lodger',
                'Josefin+Sans',
                'Josefin+Slab',
                'Joti+One',
                'Judson',
                'Julee',
                'Julius+Sans+One',
                'Junge',
                'Jura',
                'Just+Another+Hand',
                'Just+Me+Again+Down+Here',
                'Kadwa',
                'Kalam',
                'Kameron',
                'Kantumruy',
                'Karla',
                'Karma',
                'Kaushan+Script',
                'Kavoon',
                'Kdam+Thmor',
                'Keania+One',
                'Kelly+Slab',
                'Kenia',
                'Khand',
                'Khmer',
                'Khula',
                'Kite+One',
                'Knewave',
                'Kotta+One',
                'Koulen',
                'Kranky',
                'Kreon',
                'Kristi',
                'Krona+One',
                'Kurale',
                'La+Belle+Aurore',
                'Laila',
                'Lakki+Reddy',
                'Lancelot',
                'Lateef',
                'Lato',
                'League+Script',
                'Leckerli+One',
                'Ledger',
                'Lekton',
                'Lemon',
                'Libre+Baskerville',
                'Life+Savers',
                'Lilita+One',
                'Lily+Script+One',
                'Limelight',
                'Linden+Hill',
                'Lobster',
                'Lobster+Two',
                'Londrina+Outline',
                'Londrina+Shadow',
                'Londrina+Sketch',
                'Londrina+Solid',
                'Lora',
                'Love+Ya+Like+A+Sister',
                'Loved+by+the+King',
                'Lovers+Quarrel',
                'Luckiest+Guy',
                'Lusitana',
                'Lustria',
                'Macondo',
                'Macondo+Swash+Caps',
                'Magra',
                'Maiden+Orange',
                'Mako',
                'Mallanna',
                'Mandali',
                'Marcellus',
                'Marcellus+SC',
                'Marck+Script',
                'Margarine',
                'Marko+One',
                'Marmelad',
                'Martel',
                'Martel+Sans',
                'Marvel',
                'Mate',
                'Mate+SC',
                'Maven+Pro',
                'McLaren',
                'Meddon',
                'MedievalSharp',
                'Medula+One',
                'Megrim',
                'Meie+Script',
                'Merienda',
                'Merienda+One',
                'Merriweather',
                'Merriweather+Sans',
                'Metal',
                'Metal+Mania',
                'Metamorphous',
                'Metrophobic',
                'Michroma',
                'Milonga',
                'Miltonian',
                'Miltonian+Tattoo',
                'Miniver',
                'Miss+Fajardose',
                'Modak',
                'Modern+Antiqua',
                'Molengo',
                'Molle',
                'Monda',
                'Monofett',
                'Monoton',
                'Monsieur+La+Doulaise',
                'Montaga',
                'Montez',
                'Montserrat',
                'Montserrat+Alternates',
                'Montserrat+Subrayada',
                'Moul',
                'Moulpali',
                'Mountains+of+Christmas',
                'Mouse+Memoirs',
                'Mr+Bedfort',
                'Mr+Dafoe',
                'Mr+De+Haviland',
                'Mrs+Saint+Delafield',
                'Mrs+Sheppards',
                'Muli',
                'Mystery+Quest',
                'Neucha',
                'Neuton',
                'New+Rocker',
                'News+Cycle',
                'Niconne',
                'Nixie+One',
                'Nobile',
                'Nokora',
                'Norican',
                'Nosifer',
                'Nothing+You+Could+Do',
                'Noticia+Text',
                'Noto+Sans',
                'Noto+Serif',
                'Nova+Cut',
                'Nova+Flat',
                'Nova+Mono',
                'Nova+Oval',
                'Nova+Round',
                'Nova+Script',
                'Nova+Slim',
                'Nova+Square',
                'NTR',
                'Numans',
                'Nunito',
                'Odor+Mean+Chey',
                'Offside',
                'Old+Standard+TT',
                'Oldenburg',
                'Oleo+Script',
                'Oleo+Script+Swash+Caps',
                'Open+Sans',
                'Open+Sans+Condensed',
                'Oranienbaum',
                'Orbitron',
                'Oregano',
                'Orienta',
                'Original+Surfer',
                'Oswald',
                'Over+the+Rainbow',
                'Overlock',
                'Overlock+SC',
                'Ovo',
                'Oxygen',
                'Oxygen+Mono',
                'Pacifico',
                'Palanquin',
                'Palanquin+Dark',
                'Paprika',
                'Parisienne',
                'Passero+One',
                'Passion+One',
                'Pathway+Gothic+One',
                'Patrick+Hand',
                'Patrick+Hand+SC',
                'Patua+One',
                'Paytone+One',
                'Peddana',
                'Peralta',
                'Permanent+Marker',
                'Petit+Formal+Script',
                'Petrona',
                'Philosopher',
                'Piedra',
                'Pinyon+Script',
                'Pirata+One',
                'Plaster',
                'Play',
                'Playball',
                'Playfair+Display',
                'Playfair+Display+SC',
                'Podkova',
                'Poiret+One',
                'Poller+One',
                'Poly',
                'Pompiere',
                'Pontano+Sans',
                'Poppins',
                'Port+Lligat+Sans',
                'Port+Lligat+Slab',
                'Pragati+Narrow',
                'Prata',
                'Preahvihear',
                'Press+Start+2P',
                'Princess+Sofia',
                'Prociono',
                'Prosto+One',
                'PT+Mono',
                'PT+Sans',
                'PT+Sans+Caption',
                'PT+Sans+Narrow',
                'PT+Serif',
                'PT+Serif+Caption',
                'Puritan',
                'Purple+Purse',
                'Quando',
                'Quantico',
                'Quattrocento',
                'Quattrocento+Sans',
                'Questrial',
                'Quicksand',
                'Quintessential',
                'Qwigley',
                'Racing+Sans+One',
                'Radley',
                'Rajdhani',
                'Raleway',
                'Raleway+Dots',
                'Ramabhadra',
                'Ramaraja',
                'Rambla',
                'Rammetto+One',
                'Ranchers',
                'Rancho',
                'Ranga',
                'Rationale',
                'Ravi+Prakash',
                'Redressed',
                'Reenie+Beanie',
                'Revalia',
                'Rhodium+Libre',
                'Ribeye',
                'Ribeye+Marrow',
                'Righteous',
                'Risque',
                'Roboto',
                'Roboto+Condensed',
                'Roboto+Mono',
                'Roboto+Slab',
                'Rochester',
                'Rock+Salt',
                'Rokkitt',
                'Romanesco',
                'Ropa+Sans',
                'Rosario',
                'Rosarivo',
                'Rouge+Script',
                'Rozha+One',
                'Rubik',
                'Rubik+Mono+One',
                'Rubik+One',
                'Ruda',
                'Rufina',
                'Ruge+Boogie',
                'Ruluko',
                'Rum+Raisin',
                'Ruslan+Display',
                'Russo+One',
                'Ruthie',
                'Rye',
                'Sacramento',
                'Sahitya',
                'Sail',
                'Salsa',
                'Sanchez',
                'Sancreek',
                'Sansita+One',
                'Sarala',
                'Sarina',
                'Sarpanch',
                'Satisfy',
                'Scada',
                'Scheherazade',
                'Schoolbell',
                'Seaweed+Script',
                'Sevillana',
                'Seymour+One',
                'Shadows+Into+Light',
                'Shadows+Into+Light+Two',
                'Shanti',
                'Share',
                'Share+Tech',
                'Share+Tech+Mono',
                'Shojumaru',
                'Short+Stack',
                'Siemreap',
                'Sigmar+One',
                'Signika',
                'Signika+Negative',
                'Simonetta',
                'Sintony',
                'Sirin+Stencil',
                'Six+Caps',
                'Skranji',
                'Slabo+13px',
                'Slabo+27px',
                'Slackey',
                'Smokum',
                'Smythe',
                'Sniglet',
                'Snippet',
                'Snowburst+One',
                'Sofadi+One',
                'Sofia',
                'Sofia+Sans',
                'Sonsie+One',
                'Sorts+Mill+Goudy',
                'Source+Code+Pro',
                'Source+Sans+Pro',
                'Source+Serif+Pro',
                'Special+Elite',
                'Spicy+Rice',
                'Spinnaker',
                'Spirax',
                'Squada+One',
                'Sree+Krushnadevaraya',
                'Stalemate',
                'Stalinist+One',
                'Stardos+Stencil',
                'Stint+Ultra+Condensed',
                'Stint+Ultra+Expanded',
                'Stoke',
                'Strait',
                'Sue+Ellen+Francisco',
                'Sumana',
                'Sunshiney',
                'Supermercado+One',
                'Sura',
                'Suranna',
                'Suravaram',
                'Suwannaphum',
                'Swanky+and+Moo+Moo',
                'Syncopate',
                'Tangerine',
                'Taprom',
                'Tauri',
                'Teko',
                'Telex',
                'Tenali+Ramakrishna',
                'Tenor+Sans',
                'Text+Me+One',
                'The+Girl+Next+Door',
                'Tienne',
                'Tillana',
                'Timmana',
                'Tinos',
                'Titan+One',
                'Titillium+Web',
                'Trade+Winds',
                'Trocchi',
                'Trochut',
                'Trykker',
                'Tulpen+One',
                'Ubuntu',
                'Ubuntu+Condensed',
                'Ubuntu+Mono',
                'Ultra',
                'Uncial+Antiqua',
                'Underdog',
                'Unica+One',
                'UnifrakturCook',
                'UnifrakturMaguntia',
                'Unkempt',
                'Unlock',
                'Unna',
                'Vampiro+One',
                'Varela',
                'Varela+Round',
                'Vast+Shadow',
                'Vesper+Libre',
                'Vibur',
                'Vidaloka',
                'Viga',
                'Voces',
                'Volkhov',
                'Vollkorn',
                'Voltaire',
                'VT323',
                'Waiting+for+the+Sunrise',
                'Wallpoet',
                'Walter+Turncoat',
                'Warnes',
                'Wellfleet',
                'Wendy+One',
                'Wire+One',
                'Work+Sans',
                'Yanone+Kaffeesatz',
                'Yantramanav',
                'Yellowtail',
                'Yeseva+One',
                'Yesteryear',
                'Zeyada'
            );
    $font_select='';
    /*foreach($google_fonts_array as $key=>$value){
        $font_select.='<option value="'.$key.'">'.$value.'</option>';
    }
    */
    $return_array=array();
    foreach($google_fonts_array as $value){
        $return_array[$value] = str_replace('+',' ',$value);
    }
    return $return_array;
}
