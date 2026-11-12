<?php
// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;
 
if ( !function_exists( 'wpestate_chld_thm_cfg_parent_css' ) ):
    function wpestate_chld_thm_cfg_parent_css() {
        $parent_style = 'wpestate_style'; 
    
        wp_enqueue_style( 'wpestate-child-style',
            get_stylesheet_directory_uri() . '/style.css',
                array( $parent_style ),
                wp_get_theme()->get('Version')
        );
        
    }
endif;
function enqueue_custom_js() {
    wp_enqueue_script(
        'custom-js', // Handle
        get_stylesheet_directory_uri() . '/js/custom.js', // Path to custom.js
        array('jquery'), // Dependencies (optional)
        null, // Version (null or a specific version)
        true // Load in footer
    );
}
add_action('wp_enqueue_scripts', 'enqueue_custom_js');



add_action('after_setup_theme', function() {
    $domain = 'wpresidence';
    $locale = get_locale();

    // 1. Load parent theme translations from WP language directory
    load_theme_textdomain($domain, WP_LANG_DIR . '/themes');
    
    // 2. Load child theme translations
    load_child_theme_textdomain($domain, WP_LANG_DIR . '/themes');
    
    // 3. Fallback to child theme languages directory
    $child_mofile = get_stylesheet_directory() . "/languages/{$locale}.mo";
    if (file_exists($child_mofile)) {
        load_textdomain($domain, $child_mofile);
    }
    
    
});

add_action( 'wp_enqueue_scripts', 'wpestate_chld_thm_cfg_parent_css' );

// Function to add custom user roles
function add_custom_user_roles() {
    // Add User role
    add_role(
        'user',
        __('User'),
        array(
            'read'         => true,  // Allows reading posts
            'edit_posts'   => false, // Disallows editing posts
            'upload_files' => false, // Disallows uploading files
        )
    );

    // Add Agent role
    add_role(
        'agent',
        __('Agent'),
        array(
            'read'           => true,
            'edit_posts'     => true,  // Allows editing their own posts
            'publish_posts'  => true,  // Allows publishing their own posts
            'upload_files'   => true,  // Allows uploading files
            'edit_others_posts' => false, // Disallows editing others' posts
        )
    );
       // Add Interior designer role
       add_role(
        'interior_designer',
        __('Interior Designer'),
        array(
            'read'           => true,
            'edit_posts'     => true,  // Allows editing their own posts
            'publish_posts'  => true,  // Allows publishing their own posts
            'upload_files'   => true,  // Allows uploading files
            'edit_others_posts' => false, // Disallows editing others' posts
        )
    );

    // Add Agency role
    add_role(
        'agency',
        __('Agency'),
        array(
            'read'           => true,
            'edit_posts'     => true,
            'edit_others_posts' => true,  // Allows editing others' posts
            'publish_posts'  => true,
            'manage_options' => true,  // Allows managing options
            'upload_files'   => true,
        )
    );
}
//  add_action('init', 'add_custom_user_roles');



//  role rename agency
add_filter('editable_roles', 'rename_agency_to_interior_designer');
function rename_agency_to_interior_designer($roles) {
    if (isset($roles['agency'])) {
        $roles['agency']['name'] = __('Interior Designer');
    }
    return $roles;
}

add_action('init', 'update_agency_role_display_name_to_interior_designer');
function update_agency_role_display_name_to_interior_designer() {
    global $wp_roles;
    if (isset($wp_roles->roles['agency'])) {
        $wp_roles->roles['agency']['name'] = __('Interior Designer');
        $wp_roles->role_names['agency'] = __('Interior Designer');
    }
}
//  role rename agency end
 


// interior designer admin access restrict
// function restrict_wp_admin_access_for_interior_designer() {
//     // Check if user is logged in and has 'interior_designer' role
//     if ( is_user_logged_in() && current_user_can('interior_designer') ) {
//         // Check if the user is trying to access wp-admin
//         $current_url = $_SERVER['REQUEST_URI'];
//         if ( strpos($current_url, 'wp-admin') !== false ) {
//             // Redirect the user to the homepage or any other page you prefer
//             wp_redirect(home_url());
//             exit;
//         }
//     }
// }
// add_action('admin_init', 'restrict_wp_admin_access_for_interior_designer');

// Block wp-admin for non-admin users
function restrict_admin_area_for_non_admins() {
    // Allow admins and AJAX requests
    if ( is_admin() && ! current_user_can( 'administrator' ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url( '/user-dashboard/' ) ); // change to your front-end dashboard URL
        exit;
    }
}
add_action( 'admin_init', 'restrict_admin_area_for_non_admins' );

// Redirect non-admin users after login
function custom_login_redirect_based_on_role( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        // If user is admin, go to wp-admin
        if ( in_array( 'administrator', $user->roles ) ) {
            return admin_url();
        } else {
            return home_url( '/user-dashboard/' ); // redirect all other roles here
        }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'custom_login_redirect_based_on_role', 10, 3 );



function custom_authentication_errors($user, $username, $password) {
if (is_wp_error($user)) {
    return $user; // Return early if there's already an error.
}

// Get user data
$user_data = get_user_by('login', $username);
if (!$user_data) {
    $user_data = get_user_by('email', $username);
}

// If user exists, check role
if ($user_data) {
    if (in_array('administrator', (array) $user_data->roles)) {
        return new WP_Error('authentication_failed', __('Invalid credentials.', 'wpresidence'));
    }
}

return $user;
}
// add_filter('authenticate', 'custom_authentication_errors', 30, 3);

function redirect_non_logged_in_users() {
if (!is_user_logged_in()) {
    ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                var userMenu = document.getElementById("user_menu_u");
                console.log("User menu found:", userMenu);
                if (userMenu) {
                    userMenu.addEventListener("click", function(event) {
                        event.preventDefault(); // Prevent default action if it's a link
                        event.stopPropagation(); // Stop event bubbling
                        window.location.href = "<?php echo esc_url(home_url('/register/')); ?>";
                    });
                }
            }, 500); // Delay to ensure elements are fully loaded
        });
    </script>
    <?php
}
}
//  add_action('wp_footer', 'redirect_non_logged_in_users');

function hide_menu_for_non_agents() {
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $allowed_roles = array('agency', 'agent', 'interior_designer');

    if (!array_intersect($allowed_roles, (array) $user->roles)) {
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                var userMenu = document.getElementById("user_menu_u");
                if (userMenu) {
                    userMenu.style.display = "none";
                }
            });
        </script>
        <?php
    }
} else {
    ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var userMenu = document.getElementById("user_menu_u");
            if (userMenu) {
                userMenu.style.display = "none";
            }
        });
    </script>
    <?php
}
}
add_action('wp_footer', 'hide_menu_for_non_agents');

function restrict_property_submit_page() {
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $restricted_role = 'user'; // Role that should be restricted
    $restricted_page_slug = 'property-submit-front'; // Page slug to restrict

    if (in_array($restricted_role, (array) $user->roles) && is_page($restricted_page_slug)) {
        wp_redirect(home_url('/')); // Redirect to home or another page
        exit;
    }
}
}
add_action('template_redirect', 'restrict_property_submit_page');


function hide_menu_items_for_roles() {
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $restricted_roles = ['user']; // Change role as needed

    if (array_intersect($restricted_roles, (array) $user->roles)) {
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                let menuClasses = [
                    ".page-templates\\/user_dashboard", // My Properties List
                    ".page-templates\\/user_dashboard_add", // Add New Property
                    ".page-templates\\/user_dashboard_invoices" // My Invoices
                ];

                menuClasses.forEach(menuClass => {
                    document.querySelectorAll("li[class*='" + menuClass + "']").forEach(menuItem => {
                        menuItem.style.display = "none"; // Hide the menu item
                    });
                });
            });
        </script>
        <style>
            .page-templates\/user_dashboard,
            .page-templates\/user_dashboard_add,
            .page-templates\/user_dashboard_invoices {
                display: none !important;
            }
        </style>
        <?php
    }
}
}
add_action('wp_footer', 'hide_menu_items_for_roles');

function restrict_dashboard_pages() {
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $restricted_roles = ['user']; // Change role as needed
    $restricted_pages = [
        'dashboard-property-list',
        'dashboard-add-property',
        'dashboard-invoices'
    ];

    $current_url = $_SERVER['REQUEST_URI'];
    foreach ($restricted_pages as $page) {
        if (strpos($current_url, $page) !== false && array_intersect($restricted_roles, (array) $user->roles)) {
            wp_redirect(home_url('/dashboard-main')); // Redirect to homepage or another page
            exit;
        }
    }
}
}
add_action('template_redirect', 'restrict_dashboard_pages');


function hide_menu_items_for_agency() {
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $restricted_roles = ['agency']; // Change this to the actual agency role slug

    if (array_intersect($restricted_roles, (array) $user->roles)) {
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                let menuClasses = [
                    ".page-templates\\/user_dashboard_add_agent", // Add New Agent
                    ".page-templates\\/user_dashboard_agent_list" // Agent List
                ];
                if(menuClasses.length > 0) {
                    menuClasses.forEach(menuClass => {
                        document.querySelectorAll("li[class*='" + menuClass + "']").forEach(menuItem => {
                            menuItem.style.display = "none"; // Hide the menu item
                        });
                    });
                }
                
            });
        </script>
        <style>
            .page-templates\/user_dashboard_add_agent,
            .page-templates\/user_dashboard_agent_list {
                display: none !important;
            }
        </style>
        <?php
    }
}
}
add_action('wp_footer', 'hide_menu_items_for_agency');

// Add service to cart
function add_service_to_cart() {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        WC()->cart->add_to_cart($product_id);
        // You can return any additional data you need here
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action('wp_ajax_woocommerce_add_to_cart', 'add_service_to_cart');
add_action('wp_ajax_nopriv_woocommerce_add_to_cart', 'add_service_to_cart');
// Shortcode to Display Custom Service Table


function custom_service_table_shortcode() {
    if (!class_exists('WooCommerce')) {
        return '<p>WooCommerce is not installed.</p>';
    }

    ob_start();
    global $post;
    $current_slug = get_post_field('post_name', $post->ID);

    $is_service_page = ($current_slug === 'service');
    $is_update_service_page = ($current_slug === 'update-service');

    $purchased_product_ids = array();
    $current_property_id = isset($_GET['property_id']) ? intval($_GET['property_id']) : 0;

    //  On both service & update-service page, check previous purchases per property
    if ($current_property_id) {
        $orders = wc_get_orders(array(
            'customer_id' => get_current_user_id(),
            'limit'       => -1,
            'status'      => array('wc-completed', 'wc-processing'),
        ));

        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $linked_property_id = $item->get_meta('linked_property_id');

                if ($linked_property_id == $current_property_id) {
                    $purchased_product_ids[] = $product_id;
                }
            }
        }
    }

    $has_required_product_in_cart = false;
    $required_names = array('Rentals', 'Sales');

    if ($is_service_page) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_name = get_the_title($cart_item['product_id']);
            if (in_array($product_name, $required_names)) {
                $has_required_product_in_cart = true;
                break;
            }
        }
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => 'services',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="pricing-container">';

        while ($query->have_posts()) {
            $query->the_post();
            global $product;
            $product_id = $product->get_id();
            $product_name = get_the_title();

            echo '<div class="pricing-card">';
            echo '<h2>' . esc_html($product_name) . '</h2>';
            echo '<p class="price">£' . esc_html($product->get_price()) . '</p>';

            $description = apply_filters('the_content', get_the_content());
            if (!empty($description)) {
                echo '<div class="product-description">' . $description . '</div>';
            }

            //  Button logic starts here
            $is_in_cart = false;
            $cart_item_key = '';
            $cart_items = WC()->cart !== null ? WC()->cart->get_cart() : array();
            foreach ($cart_items as $key => $cart_item) {
                if ( (int) $cart_item['product_id'] !== $product_id ) continue;
                // Support both key names: linked_property_id (from filter) and property_id (from handle_pay_now_action)
                $pid_in_cart = isset( $cart_item['linked_property_id'] ) ? intval( $cart_item['linked_property_id'] )
                             : ( isset( $cart_item['property_id'] ) ? intval( $cart_item['property_id'] ) : 0 );
                if ( $pid_in_cart === $current_property_id ) {
                    $is_in_cart    = true;
                    $cart_item_key = $key;
                    break;
                }
            }

            //  Show buttons conditionally
            if ($is_update_service_page || $is_service_page) {
                if (in_array($product_id, $purchased_product_ids)) {
                    echo '<button class="select-button remove" disabled>Already Purchased</button>';
                } elseif ($is_in_cart) {
                    echo '<a href="' . esc_url(wc_get_cart_remove_url($cart_item_key)) . '" class="select-button remove">Selected</a>';
                } elseif ($is_service_page && !$has_required_product_in_cart) {
                    echo '<button class="select-button disabled" disabled>Select Service</button>';
                } else {
                    $add_to_cart_url = $product->add_to_cart_url();
                    if ($current_property_id) {
                        $add_to_cart_url .= '&property_id=' . $current_property_id;
                    }
                    echo '<a href="' . esc_url($add_to_cart_url) . '" class="select-button">Select Service</a>';
                }
            }

            echo '</div>'; // .pricing-card
        }

        echo '</div>'; // .pricing-container
        echo '<div class="next-button"><a href="' . esc_url(site_url('/payment/')) . '">Proceed to Payment</a></div>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('service_table', 'custom_service_table_shortcode');


//  Store property_id in cart item
add_filter('woocommerce_add_cart_item_data', 'add_property_id_to_cart_item', 10, 2);
function add_property_id_to_cart_item($cart_item_data, $product_id) {
    if (isset($_GET['property_id'])) {
        $cart_item_data['linked_property_id'] = intval($_GET['property_id']);
    }
    return $cart_item_data;
}

//  Save property_id in order item meta
add_action('woocommerce_checkout_create_order_line_item', 'save_property_id_to_order_items', 10, 4);
function save_property_id_to_order_items($item, $cart_item_key, $values, $order) {
    if (isset($values['linked_property_id'])) {
        $item->add_meta_data('linked_property_id', $values['linked_property_id']);
    }
}



//  Save the Property as Draft 
// Add this code to your theme's functions.php or a custom plugin

// Custom handler for the Submit Property button
function custom_property_submission_handler() {
    // Check if form is submitted
    if (isset($_POST['submit_property'])) {
        // Basic validation
        if (empty($_POST['wpestate_title'])) {
            if (!session_id()) {
                session_start();
            }
            $_SESSION['property_error'] = '';
            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;
        }
        if (!empty($_POST['attachid'])) {
            $_POST['attachid'] = ltrim($_POST['attachid'], ',');
        }      
        
        // Property data
        $property_title = sanitize_text_field($_POST['wpestate_title']);
        $property_data = array(
            'post_title'   => $property_title,
            'post_status'  => 'draft', // Explicitly set as draft
            'post_type'    => 'estate_property',
            'post_content' => isset($_POST['wpestate_description']) ? wp_kses_post($_POST['wpestate_description']) : ''
        );
        
        // Insert property as draft
        $property_id = wp_insert_post($property_data);
        
        if ($property_id) {
            // Save metadata from the form
            foreach ($_POST as $key => $value) {
                if (!in_array($key, ['submit_property', 'action', 'submit_property_front_nonce'])) {
                    if (is_array($value)) {
                        update_post_meta($property_id, $key, $value);
                    } else {
                        update_post_meta($property_id, $key, sanitize_text_field($value));
                    }
                }
            }
            
            // Handle taxonomies
            if (!empty($_POST['prop_action_category'])) {
                $category_id = intval($_POST['prop_action_category']);
                wp_set_object_terms($property_id, $category_id, 'property_action_category', false);
                
                // Save the listed in value explicitly
                update_post_meta($property_id, 'listed_in', $category_id);
                update_post_meta($property_id, '_draft_category', $category_id);
            }
            
            if (!empty($_POST['prop_category'])) {
                $category_id = intval($_POST['prop_category']);
                wp_set_object_terms($property_id, $category_id, 'property_category', false);
            }
                 
           
            if (!empty($_POST['property_city'])) {
                $city_name = sanitize_text_field($_POST['property_city']);
            
                // Check if city term exists
                $term = term_exists($city_name, 'property_city');
                if ($term !== 0 && $term !== null) {
                    // City already exists, set by ID
                    wp_set_object_terms($property_id, intval($term['term_id']), 'property_city', false);
                } else {
                    // City doesn't exist, create new term
                    $new_term = wp_insert_term($city_name, 'property_city');
                    if (!is_wp_error($new_term)) {
                        wp_set_object_terms($property_id, intval($new_term['term_id']), 'property_city', false);
                    }
                }
            }
            if (!empty($_POST['property_county'])) {
                $county_name = sanitize_text_field($_POST['property_county']);
            
                // Check if county term exists
                $term = term_exists($county_name, 'property_county');
                if ($term !== 0 && $term !== null) {
                    // County already exists, assign it
                    wp_set_object_terms($property_id, intval($term['term_id']), 'property_county', false);
                } else {
                    // County doesn't exist, create and assign
                    $new_term = wp_insert_term($county_name, 'property_county');
                    if (!is_wp_error($new_term)) {
                        wp_set_object_terms($property_id, intval($new_term['term_id']), 'property_county', false);
                    }
                }
            }
            if (!empty($_POST['property_area'])) {
                $area_name = sanitize_text_field($_POST['property_area']);
            
                // Check if area term exists
                $term = term_exists($area_name, 'property_area');
                if ($term !== 0 && $term !== null) {
                    // Area already exists, assign it
                    wp_set_object_terms($property_id, intval($term['term_id']), 'property_area', false);
                } else {
                    // Area doesn't exist, create and assign
                    $new_term = wp_insert_term($area_name, 'property_area');
                    if (!is_wp_error($new_term)) {
                        wp_set_object_terms($property_id, intval($new_term['term_id']), 'property_area', false);
                    }
                }
            }
                        
           
            if ( ! empty( $_POST ) ) {
                $property_features = array();
                $feature_mapping = array(
                    'equipped-kitchen' => 122,
                    'gym' => 126,
                    'laundry' => 65,
                    'media-room' => 71,
                    'back-yard' => 117,
                    'balcony' => 118,
                    'basketball-court' => 119,
                    'front-yard' => 123,
                    'garage-attached' => 124,
                    'garden' => 125,
                    'hot-bath' => 128,
                    'pool' => 90,
                    'central-air' => 120,
                    'electricity' => 121,
                    'heating' => 127,
                    'natural-gas' => 129,
                    'ventilation' => 112,
                    'water' => 114,
                    'chair-accessible' => 47,
                    'smoke-detector' => 105,
                    'wifi' => 116,
                );
            
                foreach ( $feature_mapping as $field_name => $term_id ) {
                    if ( isset( $_POST[ $field_name ] ) && $_POST[ $field_name ] == '1' ) {
                        $property_features[] = $term_id;
                    }
                }
            
                if ( ! empty( $property_features ) ) {
                    wp_set_object_terms( $property_id, $property_features, 'property_features' );
                }
            }
            
            if (!empty($_POST['property_status'])) {
                $status_name = sanitize_text_field($_POST['property_status']);
            
                // Check if status term exists
                $term = term_exists($status_name, 'property_status');
                if ($term !== 0 && $term !== null) {
                    // Term exists, assign by ID
                    wp_set_object_terms($property_id, intval($term['term_id']), 'property_status', false);
                } else {
                    // Term does not exist, create it
                    $new_term = wp_insert_term($status_name, 'property_status');
                    if (!is_wp_error($new_term)) {
                        wp_set_object_terms($property_id, intval($new_term['term_id']), 'property_status', false);
                    }
                }
            }
            
            if (!empty($_POST['listing_location'])) {
              wp_set_object_terms($property_id, intval($_POST['listing_location']), 'property_location', false);
            }
            
            // Set pending payment flag
            update_post_meta($property_id, '_pending_payment', 'yes');
            if (!empty($_POST['attachid'])) {
                update_post_meta($property_id, '_thumbnail_id', $_POST['attachid']);
            }
            
            
            // Store property ID in session
            if (!session_id()) {
                session_start();
            }
            $_SESSION['draft_property_id'] = $property_id;
            
            // Store listed_in value in session too
            $listed_in = isset($_POST['prop_action_category']) ? intval($_POST['prop_action_category']) : '';
            $_SESSION['property_listed_in'] = $listed_in;
            
         
                     if (class_exists('WooCommerce') && isset($_POST['add_to_cart']) && $_POST['add_to_cart'] == '1') {
                // Determine which product to add based on category
                
                // Get Product IDs by Name instead of hardcoded IDs
                $rentals = get_page_by_title('Rentals', OBJECT, 'product'); 
                $sales = get_page_by_title('Sales', OBJECT, 'product'); 
                $rentals_product_id = $rentals ? $rentals->ID : 0;
                $sales_product_id = $sales ? $sales->ID : 0;

                // Choose product based on listing
                $product_id = ($listed_in == 97) ? $rentals_product_id : $sales_product_id;

                if ($product_id) {
                    // Clear cart and add the product
                    WC()->cart->empty_cart();
                    WC()->cart->add_to_cart($product_id, 1, '', '', array('property_id' => $property_id)); 
                }
            }
            // Redirect to service page
            $pay_now_url = add_query_arg(
                array(
                    'pay_now' => '1',
                    'property_id' => $property_id
                ),
                get_permalink(31699)
            );
            wp_redirect($pay_now_url);
            exit;
        }
    }
}
add_action('template_redirect', 'custom_property_submission_handler');

// Modify the Submit Property button to redirect to the service page
function modify_submit_property_button() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Replace the default submit action
        $('#submit_property').on('click', function(e) {
            e.preventDefault();
            
            // Add hidden fields to indicate submission
            if ($('#front_submit_form input[name="submit_property"]').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'submit_property',
                    value: '1'
                }).appendTo('#front_submit_form');
            }
            
            // Optional: Add to cart flag
            $('<input>').attr({
                type: 'hidden',
                name: 'add_to_cart',
                value: '0' // Don't add to cart immediately
            }).appendTo('#front_submit_form');
            
            // Submit the form
            $('#front_submit_form').submit();
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'modify_submit_property_button');

// Add AJAX handler for the front-end submission form
function wpestate_ajax_front_property_submit() {
    // Verify nonce
    if (!isset($_POST['submit_property_front_nonce']) || 
        !wp_verify_nonce($_POST['submit_property_front_nonce'], 'submit_property_front_action')) {
        echo json_encode(array('success' => false, 'message' => 'Security check failed'));
        die();
    }
    
    // Set post to draft explicitly
    $_POST['post_status'] = 'draft';
    
    // Call the main submission handler
    custom_property_submission_handler();
    
    // If we get here, something went wrong
    echo json_encode(array('success' => false, 'message' => 'Submission failed'));
    die();
}
add_action('wp_ajax_wpestate_ajax_front_property_submit', 'wpestate_ajax_front_property_submit');
add_action('wp_ajax_nopriv_wpestate_ajax_front_property_submit', 'wpestate_ajax_front_property_submit');

// Ensure properties are always saved as draft
function ensure_property_draft_status($postarr) {
    // Check if this is a property submission from front-end
    if (isset($postarr['post_type']) && 
        $postarr['post_type'] == 'estate_property' && 
        (isset($_POST['submit_property']) || isset($_POST['action']) && $_POST['action'] == 'front_submit')) {
        
        // Force draft status
        $postarr['post_status'] = 'draft';
    }
    
    return $postarr;
}
add_filter('wp_insert_post_data', 'ensure_property_draft_status', 10, 1);


// Handle the Pay Now button action
function handle_pay_now_action() {
    if ( ! isset( $_GET['pay_now'] ) || $_GET['pay_now'] != '1' || ! isset( $_GET['property_id'] ) ) {
        return;
    }

    // WooCommerce is processing an add-to-cart GET request right now.
    // Do NOT touch the cart — clearing it here would discard the item the user just selected.
    if ( isset( $_GET['add-to-cart'] ) ) {
        return;
    }

    $property_id = intval( $_GET['property_id'] );
    if ( ! $property_id ) return;

    // Only proceed if the property exists
    if ( ! get_post_status( $property_id ) ) return;

    // Resolve the required base product (Rentals or Sales)
    $listed_in = get_post_meta( $property_id, 'listed_in', true );
    if ( empty( $listed_in ) ) {
        $listed_in = get_post_meta( $property_id, '_draft_category', true );
    }

    if ( $listed_in == 97 ) {
        $base_product = get_page_by_title( 'Rentals', OBJECT, 'product' );
    } else {
        $base_product = get_page_by_title( 'Sales', OBJECT, 'product' );
    }
    $base_product_id = $base_product ? $base_product->ID : 0;
    if ( ! $base_product_id ) return;

    // If the required base product is already in the cart for this property,
    // do NOT clear the cart — the user may have already selected additional services.
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( (int) $cart_item['product_id'] !== $base_product_id ) continue;
        $pid_in_cart = isset( $cart_item['linked_property_id'] ) ? intval( $cart_item['linked_property_id'] )
                     : ( isset( $cart_item['property_id'] ) ? intval( $cart_item['property_id'] ) : 0 );
        if ( $pid_in_cart === $property_id ) {
            // Already in cart for this property — leave the cart alone.
            if ( session_id() ) {
                $_SESSION['selected_property_id'] = $property_id;
                $_SESSION['service_property_id']  = $property_id;
            }
            return;
        }
    }

    // Base product not yet in cart — clear and add it.
    WC()->cart->empty_cart();
    WC()->cart->add_to_cart( $base_product_id, 1, '', '', array(
        'linked_property_id' => $property_id,
        'property_id'        => $property_id,
    ) );

    if ( session_id() ) {
        $_SESSION['selected_property_id'] = $property_id;
        $_SESSION['service_property_id']  = $property_id;
    }
}
add_action('wp', 'handle_pay_now_action');


// Save property as draft with proper metadata
function save_property_as_draft($post_id) {
    if (isset($_POST['listed_in'])) {
        $listed_in = sanitize_text_field($_POST['listed_in']);
        $encoded_listed_in = base64_encode($listed_in); // Encode value
        
        update_post_meta($post_id, '_listed_in_encoded', $encoded_listed_in);
    }
}
add_action('save_post_estate_property', 'save_property_as_draft');

// Save property category for draft properties
function save_draft_property_category($post_id, $post) {
    if ($post->post_type !== 'estate_property' || $post->post_status !== 'draft') {
        return; 
    }

    // Get selected category
    $categories = wp_get_post_terms($post_id, 'property_action_category', array('fields' => 'ids'));

    if (!empty($categories)) {
        update_post_meta($post_id, '_draft_category', $categories[0]); // Store only first category
    }
}
add_action('save_post', 'save_draft_property_category', 10, 2);

// Show property name on cart & checkout pages
add_filter('woocommerce_get_item_data', 'show_property_name_on_cart_and_checkout', 10, 2);
function show_property_name_on_cart_and_checkout($item_data, $cart_item) {
    if (!empty($cart_item['property_id'])) {
        $property_id = $cart_item['property_id'];
        $property_title = get_the_title($property_id);

        if ($property_title) {
            $item_data[] = array(
                'key'   => __('Property'),
                'value' => esc_html($property_title),
            );
        }
    }
    return $item_data;
}

// Attach property ID to order item at checkout
add_action('woocommerce_checkout_create_order_line_item', 'add_property_id_to_order_items', 10, 4);
function add_property_id_to_order_items($item, $cart_item_key, $values, $order) {
    if (!empty($values['property_id'])) {
        $item->add_meta_data('property_id', $values['property_id'], true);
    }
}

// Publish property after payment
add_action('woocommerce_order_status_completed', 'publish_property_after_payment');
add_action('woocommerce_order_status_processing', 'publish_property_after_payment');
function publish_property_after_payment($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;

    foreach ($order->get_items() as $item) {
        $property_id = $item->get_meta('property_id');

        if ($property_id && get_post_status($property_id) === 'draft') {
            // Publish the property
            wp_update_post([
                'ID' => $property_id,
                'post_status' => 'publish',
            ]);

            // Mark it as paid
            update_post_meta($property_id, 'payment_completed', 'yes');
        }
    }
}

// Utility: Check property payment status
function check_property_payment_status($property_id) {
    $payment_completed = get_post_meta($property_id, 'payment_completed', true);
    if ($payment_completed === 'yes') return true;

    global $wpdb;
    $has_payment = $wpdb->get_var($wpdb->prepare(
        "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta oim
         JOIN {$wpdb->prefix}woocommerce_order_items oi ON oim.order_item_id = oi.order_item_id
         JOIN {$wpdb->posts} p ON oi.order_id = p.ID
         WHERE oim.meta_key = 'property_id' 
         AND oim.meta_value = %s
         AND p.post_status IN ('wc-completed', 'wc-processing')
         LIMIT 1",
        $property_id
    ));

    return !empty($has_payment);
}

// Add styles for the property submission table and service cards

        // Full name validetion remove
        function override_wpestate_registration_validation() {
            ?>
            <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                if (typeof wpestate_validateRegistrationData === "function") {
                    wpestate_validateRegistrationData = function ($formWrapper, formData) {
                        const $messageArea = $formWrapper.find('.wpestate_register_message_area');
        
                        // Removed full name check
        
                        // Terms and conditions check
                        if (!$formWrapper.find('.wpestate_register_form_agree_terms').is(":checked")) {
                            $messageArea.html(`<div class="login-alert">${control_vars.terms_cond}</div>`);
                            return false;
                        }
        
                        // User type check
                        if (formData.new_user_type === 0 && $formWrapper.find('.wpestate_register_form_user_type').length > 0) {
                            $messageArea.html(`<div class="login-alert">${control_vars.user_type_warn}</div>`);
                            return false;
                        }
        
                        return true;
                    };
                }
            });
            jQuery(document).ready(function() {
                jQuery('.register_submit_modal').on( 'click', function(event) {
                    jQuery('.login-links').show();
                    jQuery('#modal_login_wrapper').show();
                    jQuery('#modal_login_wrapper').find('[autofocus]').focus();
                });
            });
            </script>
            <?php
        }
        add_action('wp_footer', 'override_wpestate_registration_validation');

        function custom_add_text_to_specific_menu_item($item_output, $item, $depth, $args) {
            if ($item->ID == 321800) { // Your Menu Item ID
                $item_output .= '<span class="custom-menu-text">Your Text</span><div class="submit_action">Register</div>';
            }
            return $item_output;
        }
        
        add_filter('walker_nav_menu_start_el', 'custom_add_text_to_specific_menu_item', 10, 4);

// Validation wizard form
add_action('wp_footer', 'custom_media_tab_validation_script');
function custom_media_tab_validation_script() {
    if (is_page()) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            function validateMediaTab() {
                var uploadedImages = $('#imagelist .uploaded_images').length;
                $('.media-validation-error').remove();

                if (uploadedImages === 0) {
                    $('#drag-and-drop').after('<p class="media-validation-error" style="color:red; margin-top:10px;">Please upload at least one image before continuing.</p>');
                    return false;
                }

                return true;
            }

            $('#front_submit_next_step').on('click', function(e) {
                if ($('#drag-and-drop').is(':visible')) {
                    if (!validateMediaTab()) {
                        e.preventDefault();
                        e.stopImmediatePropagation(); 
                        return false;
                    }
                }
            });
        });
        
        </script>
        <?php
    }
}


add_action('wp_footer', 'custom_location_tab_validation_script');
function custom_location_tab_validation_script() {
    if (is_page()) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            function validateLocationTab() {
                let isValid = true;

                // Remove old errors
                $('.location-validation-error').remove();

                const address = $('#property_address');
                const city = $('#property_city_submit');
                const zip = $('#property_zip');
                const country = $('#property_country');

                // Validation
                if ($.trim(address.val()) === '') {
                    address.before('<p class="location-validation-error" style="color:red; margin-bottom:5px;">Address is required.</p>');
                    isValid = false;
                }

                if ($.trim(city.val()) === '') {
                    city.before('<p class="location-validation-error" style="color:red; margin-bottom:5px;">City is required.</p>');
                    isValid = false;
                }

                if ($.trim(zip.val()) === '') {
                    zip.before('<p class="location-validation-error" style="color:red; margin-bottom:5px;">Postal code is required.</p>');
                    isValid = false;
                }

                if (!country.val()) {
                    country.before('<p class="location-validation-error" style="color:red; margin-bottom:5px;">Please select a country.</p>');
                    isValid = false;
                }

                return isValid;
            }

            $('#front_submit_next_step').on('click', function(e) {
                // Only validate if location tab is visible
                if ($('#property_address').is(':visible')) {
                    if (!validateLocationTab()) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                }
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'custom_listing_details_validation_script');
function custom_listing_details_validation_script() {
    if (is_page()) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            function validateListingDetailsTab() {
                let isValid = true;

                // Remove old errors
                $('.listing-details-validation-error').remove();

                function checkField(id, message) {
                    const input = $('#' + id);
                    if ($.trim(input.val()) === '') {
                        input.before('<p class="listing-details-validation-error" style="color:red; margin-bottom:5px;">' + message + '</p>');
                        isValid = false;
                    }
                }

                checkField('property_size', 'Size is required.');
                checkField('property_lot_size', 'Lot size is required.');
                checkField('property_rooms', 'Number of rooms is required.');
                checkField('property_bedrooms', 'Number of bedrooms is required.');
                checkField('property_bathrooms', 'Number of bathrooms is required.');
                return isValid;
            }

            $('#front_submit_next_step').on('click', function(e) {
                // Only validate if this tab is visible
                if ($('#property_size').is(':visible')) {
                    if (!validateListingDetailsTab()) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                }
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'validate_step_1_fields_script');
function validate_step_1_fields_script() {
    if (is_page()) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            function showError(input, message) {
                removeError(input);
                const error = $('<div class="inline-error-message" style="color:red; margin-bottom:5px; font-size:13px;"></div>').text(message);
                $(input).before(error);
            }

            function removeError(input) {
                $(input).siblings('.inline-error-message').remove();
            }

            $('#front_submit_next_step').on('click', function(e) {
                const currentStep = $('#current_step').val();
                if (currentStep !== '1') return;

                let hasError = false;

                // Title validation
                const title = $('input[name="wpestate_title"]');
                if ($.trim(title.val()) === '') {
                    showError(title, "Title is required.");
                    hasError = true;
                } else {
                    removeError(title);
                }

                // Description validation
                const description = $('#description');
                if ($.trim(description.val()) === '') {
                    showError(description, "Description is required.");
                    hasError = true;
                } else {
                    removeError(description);
                }

                // Property Price validation
                const price = $('input[name="property_price"]');
                if ($.trim(price.val()) === '') {
                    showError(price, "Property Price is required.");
                    hasError = true;
                } else if (isNaN(price.val())) {
                    showError(price, "Property Price must be a number.");
                    hasError = true;
                } else if (parseFloat(price.val()) < 0) {
                    showError(price, "Property Price cannot be negative.");
                    hasError = true;
                } else {
                    removeError(price);
                }


                // Category validation
                const category = $('#prop_category_submit');
                if (category.val() === '-1') {
                    showError(category, "Please select a Category.");
                    hasError = true;
                } else {
                    removeError(category);
                }

                // Listed In validation
                const listedIn = $('#prop_action_category_submit');
                if (listedIn.val() === '-1') {
                    showError(listedIn, "Please select 'Listed In'.");
                    hasError = true;
                } else {
                    removeError(listedIn);
                }


                if (hasError) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            });
        });
        jQuery(document).ready(function($) {
    $('#register_button').on('click', function(e) {
        e.preventDefault();

        var registerForm = $('#register_form'); // Make sure this ID matches your form
        var formData = registerForm.serialize();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'wpestate_to_register_user',
                data: formData
            },
            success: function(response) {
                // Assuming your success message is in response.success
                if (response.success || response.indexOf('success') !== -1) {
                    registerForm[0].reset(); // Clear the form fields
                }

                // Optional: Show a success message
                $('#register_response').html(response);
            },
            error: function() {
                $('#register_response').html('Something went wrong. Please try again.');
            }
        });
    });
});

        </script>
        <style>
            /* .inline-error-message {
                font-size: 13px;
            } */
        </style>
        <?php
    }
}

add_action('template_redirect', 'redirect_to_404_on_cart_shop_and_my_account');

function redirect_to_404_on_cart_shop_and_my_account() {
    if ( is_page('cart') || is_page('shop') || is_page('my-account') ) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include( get_query_template( '404' ) );
        exit;
    }
}



// change order details to property details
add_filter( 'gettext', 'change_order_details_based_on_product', 20, 3 );
function change_order_details_based_on_product( $translated_text, $text, $domain ) {
    if ( $text === 'Order details' && $domain === 'woocommerce' ) {

        // Get order ID from "Thank You" or "View Order" pages
        $order_id = get_query_var( 'order-received' );

        if ( ! $order_id && isset( $_GET['order_id'] ) ) {
            $order_id = absint( $_GET['order_id'] );
        }

        if ( ! $order_id && isset( $_GET['order'] ) ) {
            $order_id = absint( $_GET['order'] );
        }

        // Now check the order items
        if ( $order_id ) {
            $order = wc_get_order( $order_id );

            if ( $order && is_a( $order, 'WC_Order' ) ) {
                foreach ( $order->get_items() as $item ) {
                    $product_name = strtolower( $item->get_name() );

                    if ( strpos( $product_name, 'portfolio' ) !== false ) {
                        return 'Portfolio Details';
                    } elseif ( strpos( $product_name, 'property' ) !== false ) {
                        return 'Property Details';
                    }
                }
            }
        }

        // If no matching product found, default to Property Details
        return 'Property Details';
    }

    return $translated_text;
}

// login menu hide
function hide_menu_item_for_logged_in_users($items, $args) {
    if (is_user_logged_in()) {
        $items = preg_replace('/<li id="menu-item-32180".*?<\/li>/', '', $items);
    }
    return $items;
}
// whatsapp button
add_filter('wp_nav_menu_items', 'hide_menu_item_for_logged_in_users', 10, 2);
function disable_whatsapp_button_click() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const whatsappBtn = document.querySelector('.realtor_whatsapp');
            if (whatsappBtn) {
                whatsappBtn.addEventListener('click', function (e) {
                    e.preventDefault(); // Prevent the default click behavior
                });
            }
        });
    </script>
    <?php
}
// add_action('wp_footer', 'disable_whatsapp_button_click');

// add tabel property details
function custom_property_grid_on_service_page() {
    if (!session_id()) {
        session_start(); // Start session if not already started
    }

    ob_start();

    // Default values
    $title = 'Waiting for property...';
    $price = '---';
    $categories = '---';
    $listed_in = '---';
    $total_pay = '---';
    $image_url = 'https://via.placeholder.com/600x400'; // default image

    // Handle GET and Session
    if (isset($_GET['pay_now']) && $_GET['pay_now'] == '1' && isset($_GET['property_id']) && is_numeric($_GET['property_id'])) {
        $_SESSION['service_property_id'] = intval($_GET['property_id']);
    }

    if (isset($_SESSION['service_property_id'])) {
        $property_id = $_SESSION['service_property_id'];
        $post = get_post($property_id);

        if ($post && $post->post_type === 'estate_property') {
            $title = get_the_title($property_id);
            $price_val = get_post_meta($property_id, 'property_price', true);
            $price = '£' . $price_val;

            $categories = wp_get_post_terms($property_id, 'property_category', ['fields' => 'names']);
            $listed_in = wp_get_post_terms($property_id, 'property_action_category', ['fields' => 'names']);

            $categories = !empty($categories) ? implode(', ', $categories) : '---';
            $listed_in = !empty($listed_in) ? implode(', ', $listed_in) : '---';

            // Get featured image
            if (has_post_thumbnail($property_id)) {
                $image_url = get_the_post_thumbnail_url($property_id, 'large');
            }

            // Total Pay based on listing type
            $listing_type = strtolower(trim($listed_in));
            if ($listing_type == 'rentals') {
                $total_pay = '£100';
            } elseif ($listing_type == 'sales') {
                $total_pay = '£100';
            }
        } else {
            $title = 'Property not found';
        }
    }

    // New card-style layout
    echo '<div class="property-card-wrapper">';
    echo '  <img src="' . esc_url($image_url) . '" class="property-card-image" />';
    echo '  <div class="property-card-content">';
    echo '    <div class="property-card-title">' . esc_html($title) . '</div>';
    echo '    <div class="property-info-grid">';
    echo '      <div><span>Property Price</span><span>' . esc_html($price) . '</span></div>';
    echo '      <div><span>Category</span><span>' . esc_html($categories) . '</span></div>';
    echo '      <div><span>Listed In</span><span>' . esc_html($listed_in) . '</span></div>';   
    echo '    </div>';
    echo '      <div style="width: 100% !important;
    margin-top: 5px;
    text-align: center;
    padding: 10px 18px;
    font-weight: normal;
    color: #333;
    font-size: 16px;
    border-top: 1px solid #fff;
}"><span>Total Pay</span> <span style=" font-weight: bold;">' . esc_html($total_pay) . '</span> </div>';
    echo '  </div>';
    echo '</div>';

    return ob_get_clean();
}

add_shortcode('service_property_grid', 'custom_property_grid_on_service_page');




//Edit status not chnaged 
add_action('wp', 'property_submission_action');

function property_submission_action() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Save Changes'])) {

        // Check nonce for security
        if (isset($_POST['property_submission_nonce']) && !wp_verify_nonce($_POST['property_submission_nonce'], 'property_submission_action')) {
            die('Invalid nonce');
        }

        if (!empty($_POST['editing_property_id'])) {
            $property_id = absint($_POST['editing_property_id']); // sanitize ID input
            $existing_post = get_post($property_id);

            if ($existing_post && $existing_post->post_type === 'estate_property') {
                // Toggle post status
                $new_status = ($existing_post->post_status === 'publish') ? 'draft' : 'publish';

                $property_data = array(
                    'ID'          => $property_id,
                    'post_status' => $new_status,
                );

                $result = wp_update_post($property_data);

                if (is_wp_error($result)) {
                    // Handle error
                    $error_message = $result->get_error_message();
                    // Log or display error
                }
            }
        }
    }
}

// url 404 
add_action('template_redirect', 'custom_service_page_restrictions');
function custom_service_page_restrictions() {
    // Check if we are on the "service" page
    if ( is_page('service') ) {

        // Redirect non-logged-in users to login page
        if ( !is_user_logged_in() ) {
            wp_redirect( wp_login_url( get_permalink() ) );
            exit;
        }

        // Check for pay_now and validate property_id
        if ( isset($_GET['pay_now']) && $_GET['pay_now'] == '1' ) {
            $property_id = isset($_GET['property_id']) ? absint($_GET['property_id']) : 0;

            // If property_id is invalid or not an estate_property, show 404
            if ( $property_id <= 0 || get_post_type($property_id) !== 'estate_property' ) {
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
                nocache_headers();
                include( get_query_template('404') );
                exit;
            }
        }
    }
}

// wizard tab disable
function disable_property_tabs_script() {
    if (is_page('property-submit-front')) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const navLinks = document.querySelectorAll('.navigation_container .inner_navigation');
                console.log("Disabling tabs on property-submit-front:", navLinks.length);
                
                navLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                    link.style.pointerEvents = 'none';
                    link.style.opacity = '0.5';
                    link.style.cursor = 'not-allowed';
                });
            }, 300);
        });
        </script>
        <?php
    }
}
 add_action('wp_footer', 'disable_property_tabs_script');


// disable edit listed value and Category
add_action('wp_footer', 'disable_fields_on_edit_page');
function disable_fields_on_edit_page() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            var currentUrl = window.location.href;
            if (currentUrl.indexOf("listing_edit=") !== -1) {
                $('#prop_action_category_submit').prop('disabled', true);
                $('#prop_category_submit').prop('disabled', true);
                $('#prop_action_category_submit, #prop_category_submit').css({
                    'pointer-events': 'none',
                    'background-color': '#f1f1f1',
                    'color': '#777'
                });
            }
        });
    // (*text remove in edit dahboard and wizard property)
    document.addEventListener("DOMContentLoaded", function() {
  const elements = document.querySelectorAll(".col-md-6");
  elements.forEach(function(el) {
    if (el.textContent.includes("(*text)")) {
      el.innerHTML = el.innerHTML.replace("(*text)", "");
    }
    if (el.textContent.includes("(*date)")) {
      el.innerHTML = el.innerHTML.replace("(*date)", "");
    }
  });
});




    </script>
    <?php
}

// Country dropdown disable 
function disable_property_country_dropdown_js() {
    ?>
    <script>
        

        document.addEventListener('DOMContentLoaded', function() {
            let propertyCountryElement = document.getElementById("property_country");
            if (propertyCountryElement) {
                propertyCountryElement.disabled = true;
            }
        });

    </script>
    <?php
}
add_action('wp_footer', 'disable_property_country_dropdown_js');


// don't page redirect press enter key

function prevent_enter_key_submission() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get the form element
            var form = document.getElementById('front_submit_form');

            // Add event listener for keydown event
            form.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Prevent form submission
                }
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'prevent_enter_key_submission');


function custom_zoom_script() {
    if (is_page('service')) { // Slug 'service' hai
        ?>
        <script type="text/javascript">
            // document.addEventListener("DOMContentLoaded", function() {
            //     document.body.style.zoom = "80%";
            // });
            // Function to remove validation from WooCommerce checkout form
        
        </script>
        <?php
    }
    ?>
    <script type="text/javascript">
            
            jQuery(document).ready(function($) {
               
                $('.wc-block-components-text-input input[required]').each(function () {
                    $(this).removeAttr('required');
                });
            });

        </script>
    <?php    
}
add_action('wp_footer', 'custom_zoom_script');

// wp-admin validation

function hide_admin_bar_for_non_allowed_roles() {
    if (!current_user_can('agent') ||  !current_user_can('interior_designer') ||  !current_user_can('agency')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'hide_admin_bar_for_non_allowed_roles');

add_filter('wp_authenticate_user', 'restrict_login_to_admin_only', 10, 2);
function restrict_login_to_admin_only($user, $password) {
// Check if the user is logging in via the specific redirect URL
if (isset($_REQUEST['redirect_to']) && strpos($_REQUEST['redirect_to'], 'wp-admin') !== false) {
if (!in_array('administrator', (array) $user->roles)) {
return new WP_Error('not_admin', __('Only administrators can access this area.'));
}
}
return $user;
}

// interior_designer dahbaord add new property hide
function hide_user_dashboard_add_for_developer_role() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $restricted_roles = ['agency'];

        if (array_intersect($restricted_roles, (array) $user->roles)) {
            
            ?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {

                    let menuClass = ".page-templates\\/user_dashboard_add";
             
                    document.querySelectorAll("li[class*='" + menuClass + "']").forEach(menuItem => {
                        menuItem.style.display = "none"; // Hide the menu item
                    });
                });
                
            </script>
            <style>
                .page-templates\/user_dashboard_add {
                    display: none !important;
                }
            </style>
            <?php
        }
    }
}
 add_action('wp_footer', 'hide_user_dashboard_add_for_developer_role');
 
function hide_create_portfolio_for_agent_role_js() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $user_role = $user->roles[0]; // Get the first role of the user
        
        if ($user_role == 'agent') {
            ?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    // Target the menu item by its class or ID (inspect the actual HTML to find the correct selector)
                    var portfolioButton = document.querySelector('.page-templates\\/portfolio'); // Update the selector as needed
                    
                    if (portfolioButton) {
                        portfolioButton.style.display = 'none'; // Hide the portfolio button
                    }
                });
            </script>
            
            <style>
                /* Use this to force hiding the button using CSS */
                .page-templates\/portfolio {
                    display: none !important;
                }
            </style>
            <?php
        }
    }
}

//   add_action('wp_footer', 'hide_create_portfolio_for_agent_role_js');


// remove checkout name in pay pay option
 function change_express_checkout_heading_text() {
    if (is_page(31591)) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var interval = setInterval(function () {
                    var heading = document.querySelector('.wc-block-components-express-payment__title');
                    if (heading && heading.textContent.includes('Checkout')) {
                        heading.textContent = heading.textContent.replace('Checkout', '').trim();
                        clearInterval(interval); // Stop checking once replaced
                    }
                }, 300); // check every 300ms
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'change_express_checkout_heading_text');



// page validation  interior designer
function restrict_interior_profile_page() {
    if ( is_page('interior-profile') && !is_user_logged_in() ) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include( get_query_template( '404' ) );
        exit;
    }
}
add_action( 'template_redirect', 'restrict_interior_profile_page' );


// portfolio steps
function custom_portfolio_form_shortcode() {
    ob_start();

    $checkout_url = wc_get_checkout_url();

    // Use product name instead of ID
    $product = get_page_by_path('portfolio', OBJECT, 'product');
    $product_id = $product ? $product->ID : 0;
    $current_user = wp_get_current_user();

    $user_id      = $current_user->ID;
    
    $user_role    = intval(get_user_meta($user_id, 'user_estate_role', true));

   
    $agent_id = get_user_meta($user_id, 'user_agent_id', true);

    
    $portfolio = get_posts([
        'post_type'   => 'portfolio',
        'post_status' => ['draft', 'publish'], // check both draft and publish
        'author'      => $user_id,
        'numberposts' => 1
    ]);
  
    $portfolio = !empty($portfolio) ? $portfolio[0] : '';

    $portfolio_id = $portfolio->ID ?? 0;   
    $author_id = get_current_user_id();
    
    $title = $portfolio->post_title ?? '';

    $bio = $portfolio->post_content ?? '';
    $portfolio_data = [
        'full_name' => get_post_meta($portfolio_id, 'full_name', true),
        'email'     => get_post_meta($portfolio_id, 'email', true),
        'phone'     => get_post_meta($portfolio_id, 'phone', true),
        'date_of_birth' => get_post_meta($portfolio_id, 'date_of_birth', true),
        'gender' => get_post_meta($portfolio_id, 'gender', true),
        'address' => get_post_meta($portfolio_id, 'address', true),
        'city' => get_post_meta($portfolio_id, 'city', true),
        'portfolio_description' => get_post_meta($portfolio_id, 'portfolio_description', true),
        'bio' => get_post_meta($portfolio_id, 'bio', true),
        'linkedin' => get_post_meta($portfolio_id, 'linkedin', true),
        'website' => get_post_meta($portfolio_id, 'website', true),
        'facebook' => get_post_meta($portfolio_id, 'facebook', true),
        'instagram' => get_post_meta($portfolio_id, 'instagram', true),
        'tiktok' => get_post_meta($portfolio_id, 'tiktok', true),
        'twitter' => get_post_meta($portfolio_id, 'twitter', true),
        'youtube' => get_post_meta($portfolio_id, 'youtube', true),
        'order_id' => get_post_meta($portfolio_id, 'order_id', true),
        'site' => get_post_meta($portfolio_id, 'site', true),
    ];  
    
    $projects = get_post_meta($portfolio_id, 'portfolio_projects', true);  
    $agency_name     =  $user_email  = $agency_address  = $agency_facebook = $agency_instagram= $agency_twitter  = $agency_linkedin = $agency_youtube  = $agency_tiktok   = $agency_website  = '';
    $user_bio  = $profile_image_url = '';
    if ($agent_id) {                                    
        $agency_name     = get_the_title($agent_id); 
        $user_email      = $current_user->user_email;
        $agency_mobile   = get_post_meta($agent_id, 'agency_mobile', true);
        $agency_address  = get_post_meta($agent_id, 'agency_address', true);
        $agency_facebook = get_post_meta($agent_id, 'agency_facebook', true);
        $agency_instagram= get_post_meta($agent_id, 'agency_instagram', true);
        $agency_twitter  = get_post_meta($agent_id, 'agency_twitter', true);
        $agency_linkedin = get_post_meta($agent_id, 'agency_linkedin', true);
        $agency_youtube  = get_post_meta($agent_id, 'agency_youtube', true);
        $agency_tiktok   = get_post_meta($agent_id, 'agency_tiktok', true);
        $agency_website  = get_post_meta($agent_id, 'agency_website', true);
        $user_bio        = get_post_field('post_content', $agent_id);
        $profile_image_url = get_user_meta($user_id, 'custom_picture', true);
    }       

    ?>

<div id="portfolio-form" class="bg-white p-6 mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">My Portfolio</h2>    
    <!-- Form -->
    <form id="multiStepPortfolioForm" enctype="multipart/form-data">
        <!-- Row 1: Name and Email in one line (both required) -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1; position: relative;">
                <lable>Name <span class="required-indicator" style="color: red;"> *</span></lable>
                <input type="text" maxlength="50"  name="full_name" value="<?php echo esc_attr(!empty($title) ? $title : $agency_name); ?>" id="fullName" placeholder="Full Name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
             
                <div id="name-error" style="color: red; font-size: 12px; margin-top: 5px; display: none;">Please enter a valid name</div>
            </div>
            <div style="flex: 1; position: relative;">
                 <lable>Email <span class="required-indicator" style="color: red;"> *</span></lable>
                <input type="email" name="email" value="<?php echo esc_attr(!empty($portfolio_data['email']) ? $portfolio_data['email'] : $user_email); ?>" id="email" placeholder="Email Address" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            
                <div id="email-error" style="color: red; font-size: 12px; margin-top: 5px; display: none;">Please enter a valid email address</div>
            </div>
        </div>
   
        
        <!-- Row 3: Phone Number and County -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px;     flex-wrap: wrap;">
            <div style="flex: 1; position: relative;">
                 <lable>Phone Number <span class="required-indicator" style="color: red;"> *</span></lable>
                <input type="tel" maxlength="15" name="phone" value="<?php echo esc_attr($portfolio_data['phone'] ? $portfolio_data['phone'] : $agency_mobile ?? ''); ?>" id="phone" placeholder="Phone Number" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
               <div id="phone-error" style="color: red; font-size: 12px; margin-top: 5px; display: none;">Please enter a valid phone number</div>
            </div>
            <div style="flex: 1; position: relative;">
                 <lable>Country <span class="required-indicator" style="color: red;"> *</span></lable>
                <select name="county" id="county" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">-- Select Country --</option>
                    <?php
                    global $wpdb;
                    $counties = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}counties ORDER BY county_name");
                    foreach ($counties as $county): ?>
                        <option value="<?php echo esc_attr($county->id); ?>" <?php selected(get_post_meta($portfolio_id, 'county', true), $county->id); ?>>
                            <?php echo esc_html($county->county_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
       
        <!-- Row 4: City and Bio -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px;     flex-wrap: wrap;">
            <div style="flex: 1; position: relative;">
                 <lable>City <span class="required-indicator" style="color: red;"> *</span></lable>
                <select name="city" id="city" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">-- Select City --</option>
                    <?php
                    $selected_city = get_post_meta($portfolio_id, 'city', true);
                    if ($selected_city) {
                        $city_data = $wpdb->get_row($wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}cities WHERE id = %d",
                            $selected_city
                        ));
                        if ($city_data) {
                            echo '<option value="' . esc_attr($city_data->id) . '" selected>' . esc_html($city_data->city_name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1; position: relative;">
                 <lable>Professional Bio <span class="required-indicator" style="color: red;"> *</span></lable>
                <textarea name="bio" id="bio" placeholder="Professional Bio..." required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; height: 80px;"><?php echo esc_textarea($portfolio_data['bio'] ?? $user_bio ?? ''); ?></textarea>
                
            </div>

              <div style="flex: 1; position: relative;">
                <lable>Describe your work</lable>
                <textarea name="portfolio_description" id="portfolioDesc" placeholder="Describe your work..." 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; height: 80px;"><?php echo esc_textarea($portfolio_data['portfolio_description'] ?? ''); ?></textarea>
            </div>

        </div>
 
        <!-- Row 5: Upload Profile Image and Portfolio Description -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px;     flex-wrap: wrap;">
            <div style="flex: 1; position: relative;">
                <button type="button" id="upload-button" style="background-color: #4a90e2; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; position: relative;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
                        <path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                        <path d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2zM14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1zM2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1h-10z"/>
                    </svg>
                    Profile Image
                    <span class="required-indicator" style="color: red; margin-left: 5px;"></span>
                </button>
                <input type="file" name="profile_image" id="profile_image" accept="image/*" required style="display: none;">
                <div id="profile-image-preview" style="margin-top: 10px; position: relative; width: 10%;">
                    <?php if (!empty($profile_image_url)): ?>
                        <img src="<?php echo esc_url($profile_image_url); ?>" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php endif; ?>
                </div>
                <div id="profile-image-error" style="color: red; margin-top: 5px; display: none;">Profile image is required</div>
            </div>
          
        </div>
        
        <!-- Row 6: Social Media Links -->
        <div style="margin-bottom: 20px; border: 1px solid #eee; padding: 15px; border-radius: 4px;">
            <h3 class="text-xl font-bold mb-4">Social Media Profiles</h3>
            
            <div style="display: flex; flex-direction: column; gap: 15px;     flex-wrap: wrap;">
                <!-- Row 1: Facebook and Instagram -->
                <div style="display: flex; gap: 15px;     flex-wrap: wrap;">
                    <!-- Facebook -->
                    <div style="flex: 1; display: flex; gap: 10px; align-items: center;     flex-wrap: wrap;">
                        <div style="width: 30px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#1877f2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        <input type="url" name="facebook" value="<?php echo esc_attr($portfolio_data['facebook']) ? $portfolio_data['facebook'] : $agency_facebook; ?>" id="facebook" placeholder="Facebook Profile URL" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <!-- Instagram -->
                    <div style="flex: 1; display: flex; gap: 10px; align-items: center;">
                        <div style="width: 30px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#e4405f">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
                        </div>
                        <input type="url" name="instagram" value="<?php echo esc_attr($portfolio_data['instagram']) ? $portfolio_data['instagram'] : $agency_instagram; ?>" id="instagram" placeholder="Instagram Profile URL" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                
                <!-- Row 2: Twitter and LinkedIn -->
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <!-- Twitter -->
                    <div style="flex: 1; display: flex; gap: 10px; align-items: center;">
                        <div style="width: 30px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#000000">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231L21.75 2.25z"/>
                            </svg>
                        </div>
                        <input type="url" name="twitter" value="<?php echo esc_attr($portfolio_data['twitter']) ? $portfolio_data['twitter'] : $agency_twitter; ?>" id="twitter" placeholder="Twitter Profile URL" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <!-- LinkedIn -->
                    <div style="flex: 1; display: flex; gap: 10px; align-items: center;">
                        <div style="width: 30px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#0077b5">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </div>
                        <input type="url" name="linkedin" value="<?php echo esc_attr($portfolio_data['linkedin']) ? $portfolio_data['linkedin'] : $agency_linkedin; ?>" id="linkedin" placeholder="LinkedIn Profile URL" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                
                <!-- Row 3: YouTube and TikTok -->
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <!-- YouTube -->
                    <div style="flex: 1; display: flex; gap: 10px; align-items: center;">
                        <div style="width: 30px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#ff0000">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </div>
                        <input type="url" name="youtube" value="<?php echo esc_attr($portfolio_data['youtube']) ? $portfolio_data['youtube'] : $agency_youtube; ?>" id="youtube" placeholder="YouTube Channel URL" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <!-- TikTok -->
                    <div style="flex: 1; display: flex; gap: 10px; align-items: center;">
                        <div style="width: 30px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#000000">
                                <path d="M12 2a10 10 0 1 0 10 10A10.004 10.004 0 0 0 12 2zm0 17c-1.21 0-2.4-.15-3.53-.44-.41-.1-.79-.22-1.13-.37-1.48-.64-2.51-1.9-2.51-3.44 0-.38.05-.75.14-1.1.1-.35.24-.68.42-.98a6.3 6.3 0 0 1 3.3-.83c.18 0 .36.02.53.05a4.4 4.4 0 0 1 3.13 3.13c.03.17.05.35.05.53a6.3 6.3 0 0 1-.83 3.3c-.3.18-.63.32-.98.42-.35.09-.72.14-1.1.14-1.54 0-2.8-1.03-3.44-2.51-.15-.34-.25-.72-.37-1.13-.29-1.13-.44-2.32-.44-3.53 0-1.21.15-2.4.44-3.53.1-.41.22-.79.37-1.13.64-1.48 1.9-2.51 3.44-2.51.38 0 .75.05 1.1.14.35.1.68.24.98.42a6.3 6.3 0 0 1 .83 3.3c0 .18-.02.36-.05.53a4.4 4.4 0 0 1-3.13 3.13c-.17.03-.35.05-.53.05a6.3 6.3 0 0 1-3.3-.83c-.18-.3-.32-.63-.42-.98-.09-.35-.14-.72-.14-1.1 0-1.54 1.03-2.8 2.51-3.44.34-.15.72-.25 1.13-.37 1.13-.29 2.32-.44 3.53-.44 1.21 0 2.4.15 3.53.44.41.1.79.22 1.13.37 1.48.64 2.51 1.9 2.51 3.44 0 .38-.05.75-.14 1.1-.1.35-.24.68-.42.98a6.3 6.3 0 0 1-3.3.83c-.18 0-.36-.02-.53-.05a4.4 4.4 0 0 1-3.13-3.13c-.03-.17-.05-.35-.05-.53a6.3 6.3 0 0 1 .83-3.3c.3-.18.63-.32.98-.42.35-.09.72-.14 1.1-.14 1.54 0 2.8 1.03 3.44 2.51.15.34.25.72.37 1.13.29 1.13.44 2.32.44 3.53z"/>
                            </svg>
                        </div>
                        <input type="url" name="tiktok" value="<?php echo esc_attr($portfolio_data['tiktok']) ? $portfolio_data['tiktok'] : $agency_tiktok; ?>" id="tiktok" placeholder="TikTok Profile URL" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                
                <!-- Row 4: Personal Website (single row) -->
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <!-- Website (half width) -->
                    <div style="flex: 1; max-width: 50%; display: flex; gap: 10px; align-items: center;">
                        <div style="width: 30px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#333333">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm1 16.057v-3.057h2.994c-.059 1.143-.212 2.24-.456 3.279-.823-.12-1.674-.188-2.538-.222zm1.957 2.162c-.499 1.33-1.159 2.497-1.957 3.456v-3.62c.666.028 1.319.081 1.957.164zm-1.957-7.219v-3.015c.868-.034 1.721-.103 2.548-.224.238 1.027.389 2.111.446 3.239h-2.994zm0-5.014v-3.661c.806.969 1.471 2.15 1.971 3.496-.642.084-1.3.137-1.971.165zm2.703-3.267c1.237.496 2.354 1.228 3.29 2.146-.642.234-1.311.442-2.019.607-.344-.992-.775-1.91-1.271-2.753zm-7.241 13.56c-.244-1.039-.398-2.136-.456-3.279h2.994v3.057c-.865.034-1.714.102-2.538.222zm2.538 1.776v3.62c-.798-.959-1.458-2.126-1.957-3.456.638-.083 1.291-.136 1.957-.164zm-2.994-7.055c.057-1.128.207-2.212.446-3.239.827.121 1.68.19 2.548.224v3.015h-2.994zm1.024-5.179c.5-1.346 1.165-2.527 1.97-3.496v3.661c-.671-.028-1.329-.081-1.97-.165zm-2.005-.35c-.708-.165-1.377-.373-2.018-.607.937-.918 2.053-1.65 3.29-2.146-.496.844-.927 1.762-1.272 2.753zm-.549 1.918c-.264 1.151-.434 2.36-.492 3.611h-3.933c.165-1.658.739-3.197 1.617-4.518.88.361 1.816.67 2.808.907zm.009 9.262c-.988.236-1.92.542-2.797.9-.89-1.328-1.471-2.879-1.637-4.551h3.934c.058 1.265.231 2.488.5 3.651zm.553 1.917c.342.976.768 1.881 1.257 2.712-1.223-.49-2.326-1.211-3.256-2.115.636-.229 1.299-.435 1.999-.597zm9.924 0c.7.163 1.362.367 1.999.597-.931.903-2.034 1.625-3.257 2.116.489-.832.915-1.737 1.258-2.713zm.553-1.917c.27-1.163.442-2.386.501-3.651h3.934c-.167 1.672-.748 3.223-1.638 4.551-.877-.358-1.81-.664-2.797-.9zm.501-5.651c-.058-1.251-.229-2.46-.492-3.611.992-.237 1.929-.546 2.809-.907.877 1.321 1.451 2.86 1.616 4.518h-3.933z"/>
                            </svg>
                        </div>
                        <input type="url" name="website" value="<?php echo esc_attr($portfolio_data['website']) ? $portfolio_data['website'] : $agency_website; ?>" id="website" placeholder="Personal Website URL" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="flex: 1; max-width: 50%;"></div>
                </div>
            </div>

            <div id="address-sections-container" style="margin-bottom: 20px; border: 1px solid #eee; padding: 15px; border-radius: 4px; margin-top: 15px;">
                <h3 class="text-xl font-bold mb-4">Project Details</h3>

                <?php
                $projects = get_post_meta($portfolio_id, 'portfolio_projects', true);
                if (empty($projects)) {
                    $projects = [['title' => '', 'site' => '', 'images' => []]];
                }
				
                foreach ($projects as $index => $project): ?>
                    <div class="address-section" data-index="<?php echo $index; ?>" style="margin-bottom: 10px;">                      

                        <div style="display: flex; gap: 10px; align-items: center;     flex-wrap: wrap;">
                            
                            <input type="text" maxlength="50" name="title[]" value="<?php echo esc_attr($project['title'] ?? ''); ?>" placeholder="Project Title" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            
                            
                            <input type="text" maxlength="150" name="site[]" value="<?php echo esc_attr($project['site'] ?? ''); ?>" placeholder="Site Address" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            
                            <button type="button" class="upload-address-img" style="background-color: #4a90e2; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; white-space: nowrap;">
                                Upload Images
                            </button>
                            <input type="file" name="address_images[<?php echo $index; ?>][]" class="address-image-input" accept="image/*" multiple style="display: none;">
                            
                            <?php if ($index === 0): ?>
                               <button type="button" class="add-more-btn" style="background-color: #4caf50; color: white; border: none; padding: 10px 15px; border-radius: 4px;">Add More</button>
                                
                            <?php else: ?>                                
                                <button type="button" class="delete-btn" style="background-color: #f44336; color: white; border: none; padding: 10px 15px !important; border-radius: 4px; cursor: pointer; white-space: nowrap; height: auto;">
                                    Delete
                                </button>
                            <?php endif; ?>
                        </div>


                        <div class="address-img-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;     flex-wrap: wrap;"></div>
                    

                        <div class="address-image-preview" style="margin-top: 10px; display: flex; gap: 10px;     flex-wrap: wrap;">
                            <?php if (!empty($project['images'])): ?>
                                <?php foreach ($project['images'] as $image): ?>
                                    <div class="address-image-wrapper">
                                        <span class="remove-preview-image" >×</span>
                                        <img src="<?php echo esc_url($image); ?>" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;"/>
                                        <input type="hidden" name="images[<?php echo $index; ?>][]" value="<?php echo esc_url($image); ?>" />
                                    </div>
                            
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>  
        
        

        <!-- Submit Button -->
        <?php             
            if($portfolio_data['order_id']){ ?>
            <button type="button" id="submitProfileBtn" style="background-color: #4a90e2; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    Update Portfolio
                </button>
        <?php }else{ ?>
                <button type="button" id="submitProfileBtn" style="background-color: #4a90e2; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    List Your Profile
                </button>
            <?php

            }
            ?>
    </div>

    <!-- ===================================================================================================================================== -->  


            <script>
           
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('address-sections-container');
                let sectionIndex = 1;

                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('add-more-btn')) {
                        console.log("===== add more button clicked 2=====");
                        return false;
                        e.preventDefault();

                        const firstSection = container.querySelector('.address-section');
                        const newSection = firstSection.cloneNode(true);

                        newSection.setAttribute('data-index', sectionIndex);

                        // Update names and clear values
                        newSection.querySelectorAll('input').forEach(input => {
                            if (input.type === 'text') {
                                input.value = '';
                            } else if (input.type === 'file') {
                                input.name = `address_images[${sectionIndex}][]`;
                                input.value = '';
                            }
                        });

                        // Clear preview
                        newSection.querySelector('.address-img-preview').innerHTML = '';

                        // Replace Add More with Delete
                        const btn = newSection.querySelector('.add-more-btn');
                        btn.textContent = 'Delete';
                        btn.classList.remove('add-more-btn');
                        btn.classList.add('delete-btn');
                        btn.style.backgroundColor = '#f44336';

                        // Append to container
                        container.appendChild(newSection);
                        sectionIndex++;
                    }

                    // Handle Delete
                    if (e.target.classList.contains('delete-btn')) {
                        e.preventDefault();
                        const section = e.target.closest('.address-section');
                        section.remove();
                    }

                    // Handle Upload Button
                    if (e.target.classList.contains('upload-address-img')) {
                        const section = e.target.closest('.address-section');
                        const fileInput = section.querySelector('.address-image-input');
                        console.log('after save image change--', fileInput);
                        fileInput.click();
                    }
                });

                // Live event delegation for file preview
                container.addEventListener('change', function (e) {
                    if (e.target.classList.contains('address-image-input')) {
                        const files = e.target.files;
                        let allValid = [...files].every(file => file.type.startsWith('image/'));

                        if (!allValid) {
                            alert('Please select only image files.');
                            e.target.value = ''; 
                            return;
                        }

                        const previewArea = e.target.closest('.address-section').querySelector('.address-img-preview');
                        const currentImageCount = previewArea.querySelectorAll('img').length;

                        if (currentImageCount + files.length > 5) {
                            alert('You can upload a maximum of 5 images per project.');
                            e.target.value = '';
                            return;
                        }


                        previewArea.innerHTML = '';

                        [...files].forEach(file => {
                            const reader = new FileReader();
                            reader.onload = function (event) {
                              

                                const imgWrapper = document.createElement('div');
                                imgWrapper.style.position = 'relative';
                                imgWrapper.style.display = 'inline-block';

                                const img = document.createElement('img');
                                img.src = event.target.result;
                                img.style.height = '60px';
                                img.style.borderRadius = '4px';
                                img.style.border = '1px solid #ccc';
                                img.style.display = 'block';

                                const removeBtn = document.createElement('span');
                                removeBtn.textContent = '×';
                                removeBtn.style.position = 'absolute';
                                removeBtn.style.top = '-5px';
                                removeBtn.style.right = '-5px';
                                removeBtn.style.background = 'red';
                                removeBtn.style.color = 'white';
                                removeBtn.style.borderRadius = '50%';
                                removeBtn.style.cursor = 'pointer';
                                removeBtn.style.width = '18px';
                                removeBtn.style.height = '18px';
                                removeBtn.style.display = 'flex';
                                removeBtn.style.alignItems = 'center';
                                removeBtn.style.justifyContent = 'center';
                                removeBtn.style.fontSize = '12px';
                                removeBtn.classList.add('remove-preview-image');

                                imgWrapper.appendChild(img);
                                imgWrapper.appendChild(removeBtn);
                                previewArea.appendChild(imgWrapper);


                            };
                            reader.readAsDataURL(file);
                        });
                    }
                });

                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-preview-image')) {
                        e.target.parentElement.remove();
                    }
                });


            });

            </script>

           
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Validation patterns
            const patterns = {
                name: /^[a-zA-Z\s]{2,50}$/,
                email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
                phone: /^[0-9]{10,15}$/,
                url: /^(https?:\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/
            };

            // Input validation functions
            function validateInput(element, pattern, errorElement) {
                if (element.value === '') {
                    if (element.required) {
                        element.style.border = '1px solid red';
                        if (errorElement) errorElement.style.display = 'block';
                        return false;
                    }
                    return true; // Empty but not required is OK
                }
                
                const isValid = pattern.test(element.value);
                element.style.border = isValid ? '1px solid #ddd' : '1px solid red';
                if (errorElement) errorElement.style.display = isValid ? 'none' : 'block';
                return isValid;
            }

            // Add input validation event listeners
            document.getElementById('fullName').addEventListener('input', function() {
                validateInput(this, patterns.name, document.getElementById('name-error'));
            });

            document.getElementById('email').addEventListener('input', function() {
                validateInput(this, patterns.email, document.getElementById('email-error'));
            });

            document.getElementById('phone').addEventListener('input', function(e) {
                // Allow only numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                validateInput(this, patterns.phone, document.getElementById('phone-error'));
            });

            // Social media URL validations
            ['linkedin', 'website', 'facebook', 'instagram', 'youtube'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', function() {
                        if (this.value !== '') {
                            validateInput(this, patterns.url);
                        } else {
                            this.style.border = '1px solid #ddd';
                        }
                    });
                }
            });

            // Profile image upload handler (single image)
            document.getElementById("upload-button").addEventListener("click", function () {
                document.getElementById("profile_image").click();
            });

            document.getElementById("profile_image").addEventListener("change", function (event) {
                const previewArea = document.getElementById("profile-image-preview");
                previewArea.innerHTML = ""; // Clear previous preview
                
                if (event.target.files.length > 0) {
                    const file = event.target.files[0];
                    
                    // Check if file is an image
                    if (!file.type.match('image.*')) {
                        document.getElementById("profile-image-error").textContent = "Please select an image file";
                        document.getElementById("profile-image-error").style.display = "block";
                        this.value = ''; // Clear the file input
                        return;
                    }
                    
                    const reader = new FileReader();
                    
                    reader.onload = function (e) {
                        const imgDiv = document.createElement("div");
                        imgDiv.style.position = 'relative';
                        imgDiv.style.border = '1px solid #ddd';
                        imgDiv.style.borderRadius = '4px';
                        imgDiv.style.overflow = 'hidden';
                        
                        imgDiv.innerHTML = `
                            <img src="${e.target.result}" style="width: 150px; height: 150px; object-fit: cover;">
                            <button type="button" class="delete-profile-image" style="position: absolute; top: 2px; right: 2px; background: rgba(255,0,0,0.7); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer;">×</button>
                        `;
                        
                        previewArea.appendChild(imgDiv);
                        document.getElementById("profile-image-error").style.display = "none";
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Delete profile image handler
            document.addEventListener("click", function (e) {
                if (e.target.classList.contains("delete-profile-image")) {
                    e.target.parentElement.remove();
                    document.getElementById("profile_image").value = "";
                    document.getElementById("profile-image-error").style.display = "block";
                }
                
                // Delete portfolio image handler has been removed
                
                // Add more button handler
                if (e.target.classList.contains("add-more-btn")) {

                    console.log("===== add more button clicked 1=====");
                    addNewAddressSection();
                }
                
                // Delete section button handler
                if (e.target.classList.contains("delete-btn")) {
                    e.target.closest('.address-section').remove();
                }
                
              
            });

           

            // Submit button handler
            document.getElementById('submitProfileBtn').addEventListener('click', function () {
                console.log('submitProfileBtn');
                // Change button text to "Processing..." and disable it
                const submitBtn = document.getElementById('submitProfileBtn');
                submitBtn.textContent = 'Processing...';
                submitBtn.disabled = true;
                submitBtn.style.backgroundColor = '#7ba7e8'; // Lighter blue to indicate processing
                
                // Validate all required fields
                let isValid = true;
               
                // Validate name
                const fullName = document.getElementById('fullName');
                isValid = validateInput(fullName, patterns.name, document.getElementById('name-error')) && isValid;
                
                // Validate email
                const email = document.getElementById('email');
                isValid = validateInput(email, patterns.email, document.getElementById('email-error')) && isValid;
                
                // Validate phone
                const phone = document.getElementById('phone');
                isValid = validateInput(phone, patterns.phone, document.getElementById('phone-error')) && isValid;
       
                // Validate city
                const city = document.getElementById('city');
                if (!city.value && city.required) {
                    city.style.border = '1px solid red';
                    isValid = false;
                } else {
                    city.style.border = '1px solid #ddd';
                }
                
                // Validate bio
                const bio = document.getElementById('bio');
                if (!bio.value && bio.required) {
                    bio.style.border = '1px solid red';
                    isValid = false;
                } else {
                    bio.style.border = '1px solid #ddd';
                }
   
              // Check profile image
                function validateProfileImage() {
                    const profileImageInput = document.getElementById('profile_image');
                    const profileImagePreview = document.getElementById('profile-image-preview');
                    const profileImageError = document.getElementById('profile-image-error');

                    if (profileImagePreview.querySelector('img')) {
                        profileImageError.style.display = 'none';
                        return true; // Image is already present
                    }

                    if (profileImageInput.files.length === 0) {
                        profileImageError.style.display = 'block';
                        return false; // No new image selected
                    } else {
                        profileImageError.style.display = 'none';
                        return true; // New image selected
                    }
                }
                console.log('submitProfileBtn 88888888888888');
                // Validate social media URLs if provided
                ['linkedin', 'website', 'facebook', 'instagram', 'youtube'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element && element.value !== '') {
                        //isValid = validateInput(element, patterns.url) && isValid;
                    }
                });
                
                if (!isValid) {
                    // Reset button if validation fails
                    submitBtn.textContent = 'List Your Profile';
                    submitBtn.disabled = false;
                    submitBtn.style.backgroundColor = '#4a90e2';
                    return; // Stop submission if validation fails
                }

               
                // Get form element
                    const form = document.getElementById('multiStepPortfolioForm');

                    // Create FormData object
                    const formData = new FormData(form);

                    // Optional: disable submit button and show loader if needed
                    submitBtn.textContent = 'Processing...';
                    submitBtn.disabled = true;
                    submitBtn.style.backgroundColor = '#ccc';

                    // Step 1: Add Portfolio product to cart (Product ID from PHP)
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=add_to_cart&product_id=<?php echo $product_id; ?>')
                        .then(() => {
                            // Step 2: Send form data via AJAX POST to store it
                            return fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=store_portfolio_data', {
                                method: 'POST',
                                body: formData
                            });
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Optionally check response and then redirect
                            console.log('-----Response:', data);
                            
                            if (data.success) {
                                 console.log('-----data :', data.data);
                                var order_id = data.data.order_id;
                                console.log('-----order_id :', order_id);
                                if (order_id != '' && order_id != 0  && order_id != null && order_id != undefined) {
                                    window.location.reload();
                                } else {
                                    window.location.href = '<?php echo esc_url($checkout_url); ?>';
                                }
                            } else {
                                throw new Error(data.message || 'Failed to save portfolio data');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            submitBtn.textContent = 'List Your Profile';
                            submitBtn.disabled = false;
                            submitBtn.style.backgroundColor = '#4a90e2';
                        });



            });

        });

        function addNewAddressSection() {
            const container = document.getElementById('address-sections-container');
            const index = container.querySelectorAll('.address-section').length; 
            const newSection = document.createElement('div');
            newSection.className = 'address-section';
             newSection.setAttribute('data-index', index);
            newSection.style.marginTop = '15px';
            newSection.style.paddingTop = '15px';
            newSection.style.borderTop = '1px solid #eee';
            
            newSection.innerHTML = `
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" maxlength="50" name="title[]" placeholder="Project Title" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <input type="text" maxlength="150" name="site[]" placeholder="Site Address" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <button type="button" class="upload-address-img" style="background-color: #4a90e2; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; white-space: nowrap;">
                        Upload Images
                    </button>
                    <input type="file" name="address_images[${index}][]" class="address-image-input" accept="image/*" multiple style="display: none;">
                    <button type="button" class="delete-btn" style="background-color: #f44336; color: white; border: none; padding: 10px 15px !important; border-radius: 4px; cursor: pointer; white-space: nowrap; height: auto;">
                        Delete
                    </button>
                </div>
                <div class="address-img-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;"></div>
            `;
            
            container.appendChild(newSection);
            
            // Add event listener for the new file input

            
            const fileInput = newSection.querySelector('.address-image-input');
            fileInput.addEventListener('change', function(event) {
                const section = event.target.closest('.address-section');
                const fileInput = section.querySelector('.address-image-input');
                console.log('before on add image change--', fileInput);
                fileInput.click();



                return false;
                // Validate file types
                const files = event.target.files;
                let allFilesValid = true;
                
                for (let i = 0; i < files.length; i++) {
                    if (!files[i].type.match('image.*')) {
                        allFilesValid = false;
                        break;
                    }
                }
                
                if (!allFilesValid) {
                    alert('Please select only image files');
                    return;
                }
                
                const previewArea = this.closest('.address-section').querySelector('.address-img-preview');
                handleImageUpload(event, previewArea);
            });
        }

        function handleImageUpload(event, previewAreaEl) {
            const files = event.target.files;
            const previewArea = typeof previewAreaEl === 'string' 
                ? document.getElementById(previewAreaEl)
                : previewAreaEl;
            console.log('handleImageUpload ----- ', previewArea);
             console.log('file ----', previewArea);
            for (let file of files) {
                const reader = new FileReader();
                
                reader.onload = function (e) {
                    const imgDiv = document.createElement("div");
                    imgDiv.style.position = 'relative';
                    imgDiv.style.border = '1px solid #ddd';
                    imgDiv.style.borderRadius = '4px';
                    imgDiv.style.overflow = 'hidden';
                    
                    imgDiv.innerHTML = `
                        <img src="${e.target.result}" style="width: 80px; height: 80px; object-fit: cover;">
                        <button type="button" class="delete-image" style="position: absolute; top: 2px; right: 2px; background: rgba(255,0,0,0.7); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer;">×</button>
                    `;
                    
                    previewArea.appendChild(imgDiv);
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    <?php

    return ob_get_clean();
}

add_shortcode('portfolio_form', 'custom_portfolio_form_shortcode');

// Add product to cart for both logged-in and non-logged-in users
add_action('wp_ajax_add_to_cart', 'add_portfolio_to_cart');
add_action('wp_ajax_nopriv_add_to_cart', 'add_portfolio_to_cart');

function add_portfolio_to_cart() {
    // Get the product ID (hardcoded for the portfolio)
    $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 32962;

    // Ensure the cart is emptied before adding the required product
    WC()->cart->empty_cart();

    // Add the portfolio product to the cart
    WC()->cart->add_to_cart($product_id);

    wp_die(); // Terminate AJAX request
}

// Store portfolio data temporarily
add_action('wp_ajax_store_portfolio_data', 'store_portfolio_form_data');
add_action('wp_ajax_nopriv_store_portfolio_data', 'store_portfolio_form_data');

function store_portfolio_form_data() {
    if (empty($_POST)) {
        wp_send_json_error(['message' => 'No form data received']);
    }

    $form_data = $_POST;
    $author_id = get_current_user_id();

    if (!$author_id) {
        wp_send_json_error(['message' => 'User not logged in']);
    }

    $existing = get_posts([
        'post_type'   => 'portfolio',
        'post_status' => ['draft', 'publish'], // check both draft and publish
        'author'      => $author_id,
        'numberposts' => 1,
        'fields'      => 'ids'
    ]);
   

    $post_args = [
        'post_title'   => sanitize_text_field($form_data['full_name'] ?? ''),
        'post_content' => wp_kses_post($form_data['bio'] ?? ''),
        //'post_status'  => 'draft',
        'post_type'    => 'portfolio',
        'post_author'  => $author_id,
    ];

    $meta_input = [
        'email' => sanitize_email($form_data['email'] ?? ''),
        'phone' => sanitize_text_field($form_data['phone'] ?? ''),
        'date_of_birth' => sanitize_text_field($form_data['date_of_birth'] ?? ''),
        'gender' => sanitize_text_field($form_data['gender'] ?? ''),
        'address' => sanitize_text_field($form_data['address'] ?? ''),        
        'county' => sanitize_text_field($form_data['county'] ?? ''),
        'city' => sanitize_text_field($form_data['city'] ?? ''),
        'portfolio_description' => wp_kses_post($form_data['portfolio_description'] ?? ''),
        'bio' => wp_kses_post($form_data['bio'] ?? ''),
        'linkedin' => esc_url_raw($form_data['linkedin'] ?? ''),
        'website' => esc_url_raw($form_data['website'] ?? ''),
        'facebook' => esc_url_raw($form_data['facebook'] ?? ''),
        'instagram' => esc_url_raw($form_data['instagram'] ?? ''),
        'twitter' => esc_url_raw($form_data['twitter'] ?? ''),
        'tiktok' => esc_url_raw($form_data['tiktok'] ?? ''),        
        'youtube' => esc_url_raw($form_data['youtube'] ?? ''),
        //'order_id' => sanitize_text_field($form_data['order_id'] ?? ''),
        'site' => isset($form_data['site']) && is_array($form_data['site']) 
            ? array_map('sanitize_text_field', $form_data['site']) : [],
    ];
   
    $portfolio_id = 0;
    if (!empty($existing)) {
        $post_args['ID'] = $existing[0];
        $portfolio_id = wp_update_post($post_args);
        foreach ($meta_input as $key => $value) {
            update_post_meta($portfolio_id, $key, $value);
        }
    } else {
        $post_args['post_status'] = 'draft';
        $post_args['meta_input'] = $meta_input;
        $portfolio_id = wp_insert_post($post_args);

        if (is_wp_error($portfolio_id) || !$portfolio_id) {
            wp_send_json_error(['message' => 'Failed to create portfolio']);
        }
    }
    $agent_id = get_user_meta($author_id, 'user_agent_id', true);
    // ================= Handle Portfolio Profile Image =====================
    if (!empty($_FILES['profile_image']['tmp_name'])) {
        
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Upload and attach the file
        $attach_id = media_handle_upload('profile_image', $portfolio_id);   


        // Check for upload error
        if (!is_wp_error($attach_id)) { 
            
            // Get the URL of the uploaded image
            $profile_image_url = wp_get_attachment_url($attach_id);

            // Set as featured image for both portfolio and agent post (if needed)
            set_post_thumbnail($portfolio_id, $attach_id);
            set_post_thumbnail($agent_id, $attach_id);

            // Update user meta with image URL and attachment ID
            update_user_meta($author_id, 'custom_picture', $profile_image_url);
            update_user_meta($author_id, 'small_custom_picture', $attach_id);
            
        } else {
            // Optionally log or handle upload error
            error_log('Image upload failed: ' . $attach_id->get_error_message());
        }
    }


    // ================= Handle Portfolio Projects =====================
    $projects = [];
    $titles = isset($_POST['title']) ? $_POST['title'] : [];
    $sites = isset($_POST['site']) ? $_POST['site'] : [];
    $portfolio_images = isset($_POST['images']) ? $_POST['images'] : [];
    $images = $_FILES['address_images'] ?? [];

    foreach ($titles as $index => $title) {
        $uploaded_image_urls = [];

        // Handle image upload for each project (indexed by $index)
        if (!empty($images['name'][$index]) && is_array($images['name'][$index])) {
            foreach ($images['name'][$index] as $img_idx => $img_name) {
                if (!empty($img_name)) {
                    $tmp_name = $images['tmp_name'][$index][$img_idx];
                    $upload_dir = wp_upload_dir();
                    $filename = sanitize_file_name($img_name);
                    $new_file_path = $upload_dir['path'] . '/' . $filename;

                    if (move_uploaded_file($tmp_name, $new_file_path)) {
                        $filetype = wp_check_filetype($filename, null);
                        $attachment = [
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => $filename,
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        ];
                        $attach_id = wp_insert_attachment($attachment, $new_file_path, $portfolio_id);
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        $image_url = wp_get_attachment_url($attach_id);
                        $uploaded_image_urls[] = esc_url_raw($image_url);
                    }
                }
            }
        }           
        
        if(isset($portfolio_images[$index])){
            $uploaded_image_urls = array_merge($uploaded_image_urls, $portfolio_images[$index]);
        }
       
        $title =    isset($titles[$index]) ? sanitize_text_field($title) : '';
        $site_address = isset($sites[$index]) ? sanitize_text_field($sites[$index]) : '';

        if($title || $site_address || !empty($uploaded_image_urls)){
            $projects[] = [
                'title' => sanitize_text_field($title),
                'site' => sanitize_text_field($sites[$index] ?? ''),
                'images' => $uploaded_image_urls
            ];
        }

        
    }
  
    update_post_meta($portfolio_id, 'portfolio_projects', $projects);
    $portfolio_order_id = get_post_meta($portfolio_id, 'order_id', true);
    $portfolio_order_id = $portfolio_order_id ? $portfolio_order_id : 0;
    $result = [
        'message' => 'Portfolio saved successfully',
        'portfolio_id' => $portfolio_id,
        'order_id' => $portfolio_order_id,
        
    ];
    
    wp_send_json_success($result);
    //wp_send_json_success(['message' => 'Portfolio saved successfully', 'portfolio_id' => $portfolio_id]);
}


// Store portfolio form data in session

function old_store_portfolio_form_data() {


    if (empty($_POST)) {
        wp_die('No form data received');
    }

    // Store form data in session
    WC()->session->set('portfolio_form_data', $_POST);
    
    // Handle profile image upload
    if (!empty($_FILES['profile_image'])) {
        $upload_dir = wp_upload_dir();
        $portfolio_upload_dir = $upload_dir['basedir'] . '/portfolio-temp/' . session_id();
        
        // Create directory if it doesn't exist
        if (!file_exists($portfolio_upload_dir)) {
            wp_mkdir_p($portfolio_upload_dir);
        }
        
        // Store profile image path in session
        $tmp_name = $_FILES['profile_image']['tmp_name'];
        $name = sanitize_file_name($_FILES['profile_image']['name']);
        $file_path = $portfolio_upload_dir . '/profile_' . $name;
        
        // Move uploaded file
        echo "File Path: " . move_uploaded_file($tmp_name, $file_path);
        WC()->session->set('portfolio_profile_image', $file_path);
    }

    // Group project details
    $projects = [];
    $titles = isset($_POST['title']) ? $_POST['title'] : [];
    $sites = isset($_POST['site']) ? $_POST['site'] : [];

    foreach ($titles as $index => $title) {
        $projects[$index] = [
            'title' => sanitize_text_field($title),
            'site' => sanitize_text_field($sites[$index]),
            'images' => []
        ];
    }

    // Process address_images[] grouped by section
    if (!empty($_FILES['address_images']['name'])) {
        foreach ($_FILES['address_images']['name'] as $section => $file_names) {
            foreach ($file_names as $i => $filename) {
                if ($_FILES['address_images']['error'][$section][$i] === 0) {
                    $tmp_name = $_FILES['address_images']['tmp_name'][$section][$i];
                    $name = sanitize_file_name($filename);
                    $portfolio_upload_dir = wp_upload_dir()['basedir'] . '/portfolio-temp/' . session_id();
                    wp_mkdir_p($portfolio_upload_dir);
                    $file_path = $portfolio_upload_dir . "/project_{$section}_{$name}";

                    move_uploaded_file($tmp_name, $file_path);
                    $projects[$section]['images'][] = $file_path;
                }
            }
        }
    }

    WC()->session->set('portfolio_projects', $projects);


    wp_die('Form data stored successfully');
}

// Process portfolio creation after successful payment
add_action('woocommerce_payment_complete', 'process_portfolio_after_payment');
add_action('woocommerce_thankyou', 'process_portfolio_after_payment');

function process_portfolio_after_payment($order_id) {
    // Get the order
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    // Get product ID for portfolio product
    $product = get_page_by_path('portfolio', OBJECT, 'product');
    $portfolio_product_id = $product ? $product->ID : 0;

    // Check if the order contains the portfolio product
    $contains_portfolio = false;
    foreach ($order->get_items() as $item) {
        if ($item->get_product_id() == $portfolio_product_id) {
            $contains_portfolio = true;
            break;
        }
    }

    if (!$contains_portfolio) {
        return; // Exit if the portfolio product isn't purchased
    }

    // Get the user from the order
    $author_id = $order->get_user_id();
    if (!$author_id) {
        return;
    }

    // Get the user's portfolio post
    $existing = get_posts([
        'post_type'   => 'portfolio',
        'post_status' => array('draft', 'pending', 'publish'), // In case it's already published or pending
        'author'      => $author_id,
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);

    if (!empty($existing)) {
        $portfolio_id = $existing[0];

        // Update the post status to 'publish'
        wp_update_post([
            'ID' => $portfolio_id,
            'post_status' => 'publish',
        ]);

        // Optionally update the order_id in meta
        update_post_meta($portfolio_id, 'order_id', $order_id);

        // Add portfolio ID to order meta
        update_post_meta($order_id, '_portfolio_id', $portfolio_id);
    }
}


function old_process_portfolio_after_payment($order_id) {
    // Get the order
    $order = wc_get_order($order_id);
    
    // Get product ID for portfolio product
    $product = get_page_by_path('portfolio', OBJECT, 'product');
    $portfolio_product_id = $product ? $product->ID : 0;
    
    // Check if the order is paid and contains our portfolio product
    $contains_portfolio = false;
    foreach ($order->get_items() as $item) {
        if ($item->get_product_id() == $portfolio_product_id) {
            $contains_portfolio = true;
            break;
        }
    }
    
    if (!$contains_portfolio) {
        return;  // Exit if the order does not contain the portfolio product
    }
    
    // Get form data from session
    $form_data = WC()->session->get('portfolio_form_data');


    $portfolio_profile_image = WC()->session->get('portfolio_profile_image');

    if (empty($form_data)) {
        return;
    }
    
    // Create Portfolio post
    $portfolio_post = array(
        'post_title'    => sanitize_text_field($form_data['full_name']),
        'post_content'  => wp_kses_post($form_data['bio']),
        'post_status'   => 'publish',
        'post_type'     => 'portfolio',
        'meta_input'    => array(
            'email' => sanitize_email($form_data['email']),
            'phone' => sanitize_text_field($form_data['phone']),
            'date_of_birth' => sanitize_text_field($form_data['date_of_birth']),
            'gender' => sanitize_text_field($form_data['gender']),
            'address' => sanitize_text_field($form_data['address']),
            'city' => sanitize_text_field($form_data['city']),
            'portfolio_description' => wp_kses_post($form_data['portfolio_description']),
            'bio' => wp_kses_post($form_data['bio']),
            'linkedin' => esc_url_raw($form_data['linkedin']),
            'website' => esc_url_raw($form_data['website']),
            'facebook' => esc_url_raw($form_data['facebook']),
            'instagram' => esc_url_raw($form_data['instagram']),
            'youtube' => esc_url_raw($form_data['youtube']),
            'order_id' => $order_id,
            'site' => array_map('sanitize_text_field', $form_data['site'])
        )
    );
    
    // Insert the portfolio post
    $portfolio_id = wp_insert_post($portfolio_post);
    
    if (!$portfolio_id) {
        return;  // Exit if portfolio post creation fails
    }

    $portfolio_projects = WC()->session->get('portfolio_projects');
    if (!empty($portfolio_projects)) {
        foreach ($portfolio_projects as &$project) {

            $uploaded_image_urls = array(); 

            if (!empty($project['images']) && is_array($project['images'])) {
                foreach ($project['images'] as $image_path) {
                    if (!empty($image_path) && file_exists($image_path)) {
                        $upload_dir = wp_upload_dir();
                        $filename = basename($image_path);
                        $new_file_path = $upload_dir['path'] . '/' . $filename;

                        if (@copy($image_path, $new_file_path)) {
                            $filetype = wp_check_filetype($filename, null);
                            $attachment = array(
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => sanitize_file_name($filename),
                                'post_content'   => '',
                                'post_status'    => 'inherit'
                            );

                            $attach_id = wp_insert_attachment($attachment, $new_file_path, $portfolio_id);

                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
                            wp_update_attachment_metadata($attach_id, $attach_data);

                            // Get the image URL and store it
                            $image_url = wp_get_attachment_url($attach_id);
                            $uploaded_image_urls[] = esc_url_raw($image_url);
                        }
                    }
                }
            }




            $project['title'] = sanitize_text_field($project['title']);
            $project['site'] = sanitize_text_field($project['site']);
            $project['images'] = $uploaded_image_urls;
        }
        update_post_meta($portfolio_id, 'portfolio_projects', $portfolio_projects);
    }


    // Move and attach profile image
    if (!empty($portfolio_profile_image) && file_exists($portfolio_profile_image)) {
        $upload_dir = wp_upload_dir();
        $filename = basename($portfolio_profile_image);
        
        // Define new path in uploads directory
        $new_file_path = $upload_dir['path'] . '/' . $filename;

        // Move the file from temp to main uploads folder
        if (@copy($portfolio_profile_image, $new_file_path)) {
            // Create attachment
            $filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title'     => sanitize_file_name($filename),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            
            // Insert attachment into media library
            $attach_id = wp_insert_attachment($attachment, $new_file_path, $portfolio_id);

            // Include image functions
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate metadata and update
            $attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Set as featured image
            set_post_thumbnail($portfolio_id, $attach_id);
        }
    }

    // Clear session data
    WC()->session->__unset('portfolio_form_data');
    
    // Add portfolio ID to order meta
    update_post_meta($order_id, '_portfolio_id', $portfolio_id);
}

// Register custom post type for Portfolio
function register_portfolio_post_type() {
    $labels = array(
        'name'                  => _x('Portfolios', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Portfolio', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Portfolios', 'Admin Menu text', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'add_new_item'          => __('Add New Portfolio', 'textdomain'),
        'edit_item'             => __('Edit Portfolio', 'textdomain'),
        'new_item'              => __('New Portfolio', 'textdomain'),
        'view_item'             => __('View Portfolio', 'textdomain'),
        'search_items'          => __('Search Portfolios', 'textdomain'),
        'not_found'             => __('No portfolios found', 'textdomain'),
        'all_items'             => __('All Portfolios', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'portfolio'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail'),
        'menu_icon'          => 'dashicons-portfolio',
    );

    register_post_type('portfolio', $args);
}
add_action('init', 'register_portfolio_post_type');

// listing shortcode
function portfolio_business_listing_shortcode($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 9, // Changed default to 5 posts per page for pagination
        'post_type' => 'portfolio', // Allow overriding post type through shortcode
        'debug' => 'no', // Add debug parameter
    ), $atts);
    
    ob_start();
    
    // Check if debug mode is on
    $debug = ($atts['debug'] === 'yes');
    
    // Get the current page number
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    if (isset($_GET['portfolio_page'])) {
        $paged = intval($_GET['portfolio_page']);
    }
    
    // Get filter values
    $city_filter = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
    $gender_filter = isset($_GET['gender']) ? sanitize_text_field($_GET['gender']) : '';

    $cities_data = get_cities_data_by_county(1);
    $country_data = get_countries_list();    
   
    // Add filter form at the top
    ?>
    <div class="portfolio-filter" style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; border: 1px solid #e0e0e0;">
       

        <form method="get" id="portfolio-filter-form" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <?php
            // Preserve existing query parameters
            foreach ($_GET as $key => $value) {
                if ($key !== 'city' && $key !== 'portfolio_page' && $key !== 'gender') {
                    echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                }
            }
            ?>            
             <div class="portfolio-filter-fields">
                <label for="city" style="display: block; margin-bottom: 5px; font-weight: 500;">Filter by City</label>
                <?php echo get_city_dropdown(1, esc_attr($city_filter)); ?>   
            </div>
           
            <div>
                <button type="submit" style="padding: 10px 20px; background-color: #0073e1; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">Filter</button>
                <?php if (!empty($city_filter) || isset($_GET['gender'])): ?>
                    <a href="<?php echo esc_url(remove_query_arg(array('city', 'portfolio_page', 'gender'))); ?>" style="display: inline-block; padding: 10px 20px; background-color: #f0f0f0; color: #333; text-decoration: none; border-radius: 4px; margin-left: 10px;">Clear Filter</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php
    
    if ($debug) {
        echo '<div style="background: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">';
        echo '<h3>Debug Information:</h3>';
        echo '<p>Looking for post_type: <strong>' . esc_html($atts['post_type']) . '</strong></p>';
        echo '<p>Current page: <strong>' . esc_html($paged) . '</strong></p>';
        echo '<p>City filter: <strong>' . esc_html($city_filter) . '</strong></p>';
        echo '<p>Gender filter: <strong>' . esc_html($gender_filter) . '</strong></p>';
    }
    
    $args = array(
        'post_type'      => $atts['post_type'],
        'posts_per_page' => $atts['posts_per_page'],
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
    );
    
    // Add meta query for filters if provided
    $meta_query = array('relation' => 'AND');
    
    if (!empty($city_filter)) {
        $meta_query[] = array(
            'key'     => 'city',
            'value'   => $city_filter,
            'compare' => '=',
        );
    }
    
    if (!empty($gender_filter)) {
        $meta_query[] = array(
            'key'     => 'gender',
            'value'   => $gender_filter,
            'compare' => '=',
        );
    }
    
    if (count($meta_query) > 1) { // If we have actual queries (not just the relation)
        $args['meta_query'] = $meta_query;
    }
    
    $query = new WP_Query($args);
    
    if ($debug) {
        echo '<p>Found posts: <strong>' . $query->found_posts . '</strong></p>';
        echo '</div>';
    }
    ?>
    <div class="portfolio-listings-section">
        <!-- <h1 class="entry-title title_prop">Interior Designer & Architects List</h1> -->
        <h1 class="entry-title title_prop">Interior Designers, Architects & Surveyors List</h1>
        <?php
        if ($query->have_posts()) : ?>
            <div class="portfolio-listings">
                <?php 
                $i = 0;
                while ($query->have_posts()) : $query->the_post();
                    global $post;
                    // Get post meta data with fallbacks
                    $location = get_post_meta(get_the_ID(), 'address', true);
                    if (empty($location)) $location = 'Location:';

                    $location = '';
                    
                    $phone = get_post_meta(get_the_ID(), 'business_phone', true);
                    if (empty($phone)) $phone = '';
                    
                    $rating = get_post_meta(get_the_ID(), 'business_rating', true);
                    if (empty($rating)) $rating = '5.0/5';
                    
                    $reviews = get_post_meta(get_the_ID(), 'business_reviews', true);
                    if (empty($reviews)) $reviews = '1';
                    
                    $response_time = get_post_meta(get_the_ID(), 'response_time', true);
                    if (empty($response_time)) $response_time = 'More than 60 Mins';
                    
                    $sulekha_score = get_post_meta(get_the_ID(), 'Real Estate', true);
                    if (empty($sulekha_score)) $sulekha_score = '6.6';
                    
                    // Get service categories or use default ones
                    $service_categories = get_post_meta(get_the_ID(), 'service_categories', true);
                    if (empty($service_categories)) {
                        // Check if we have categories or tags assigned
                        $terms = wp_get_post_terms(get_the_ID(), 'category');
                        if (empty($terms)) {
                            $terms = wp_get_post_terms(get_the_ID(), 'post_tag');
                        }
                        
                        if (!empty($terms)) {
                            $service_categories = array();
                            foreach ($terms as $term) {
                                $service_categories[] = $term->name;
                            }
                        } else {
                            $service_categories = array('Interior Designers', 'Restaurant Interiors', 'Shop Interiors');
                            $service_categories = array();
                        }
                    } elseif (!is_array($service_categories)) {
                        $service_categories = explode(',', $service_categories);
                    }
                    
                    // Fetch profile image
                    $profile_image = get_post_meta(get_the_ID(), 'profile_image', true); 
                    
                    // Fetch additional fields
                    $city = get_post_meta(get_the_ID(), 'city', true);
                    if (!empty($city)){
                        $city = $cities_data[ $city]['city_name'];
                    }else{
                        $city = '';
                    }
                    
                    $country = get_post_meta(get_the_ID(), 'county', true);
                    if (!empty($country)){
                        $country = $country_data[$country]['county_name'];
                    }else{
                        $country = '';
                    }

                    $address = get_post_meta(get_the_ID(), 'address', true);
                
                    
                    // Get date of birth
                    $date_of_birth = get_post_meta(get_the_ID(), 'date_of_birth', true);
                    
                    // Get gender
                    $gender = get_post_meta(get_the_ID(), 'gender', true);
                    if (!empty($gender)) {
                        if ($gender == 'male') $gender_display = 'Male';
                        elseif ($gender == 'female') $gender_display = 'Female';
                        elseif ($gender == 'other') $gender_display = 'Other';
                        elseif ($gender == 'prefer_not_to_say') $gender_display = 'Prefer not to say';
                        else $gender_display = $gender; // Use as is if it's custom
                    } else {
                        $gender_display = '';
                    }
                    
                    // Get bio
                    $bio = get_post_meta(get_the_ID(), 'bio', true);

                    //==============0505-===== dashboard profile image get in portfolio
                    $user_small_picture_id = get_the_author_meta('small_custom_picture', $post->post_author);

                    // Set default profile picture if custom picture is not set
                    if (empty($user_small_picture_id)) {
                        $user_small_picture = [get_theme_file_uri('/img/default-user_1.png')];
                    } else {
                        //$user_small_picture = wp_get_attachment_image_src($user_small_picture_id, 'agent_picture_thumb');
                        $user_small_picture = wp_get_attachment_image_src($user_small_picture_id, 'full');
                    }

                    // Ensure $user_small_picture is an array with at least one element
                    $profile_image_url = is_array($user_small_picture) && !empty($user_small_picture) ? $user_small_picture[0] : '';

                    //===============
                    if($i== 0 ||  $i % 2 == 0){
                    //echo '<div class="portfolio-business-section" style="display: flex;gap: 10px;margin-bottom: 15px;width: 100%;">';
                    }
                ?>
            
                    <div class="portfolio-business-card">
                        <!-- Business Image -->
                        <div class="business-image">
                            <img src="<?php echo $profile_image_url; ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 100%; object-fit: cover;">
             
                        </div>
                        
                        <!-- Business Details -->
                        <div class="business-details" style="flex: 1; padding: 15px; position: relative;">
                            <!-- Title and Rating -->
                            <div style="margin-bottom: 8px;">
                                <h2 style="margin: 0 0 5px 0; font-size: 18px; font-weight: 600; color: #333;"><?php the_title(); ?></h2>
                                <p style="margin: 0; font-size: 14px; color: #666;"><i class="fas fa-map-marker-alt"></i><?php echo esc_html($location); ?> <?php echo esc_html($city); ?>, <?php echo esc_html($country); ?></p>
                           
                            </div>
                            
                  
                            
                            <!-- Phone -->
                            <?php if (!empty($phone)) : ?>
                            <div style="margin-bottom: 12px;">
                                <span style="display: flex; align-items: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px;">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    <?php echo esc_html($phone); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Service Categories -->
                            <div class="service-categories" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px;">
                                <?php 
                                $categories_shown = 0;
                                $max_categories = 3; // Show max 3 categories
                                

                                foreach($service_categories as $index => $category) : 
                                    if ($categories_shown < $max_categories) :
                                        $categories_shown++;
                                ?>
                                    <span style="padding: 4px 10px; background: #f0f0f0; border-radius: 4px; font-size: 12px; color: #555;"><?php echo esc_html(trim($category)); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                
                                $remaining = count($service_categories) - $max_categories;
                                if ($remaining > 0) : 
                                ?>
                                    <span style="padding: 4px 10px; background: #f0f0f0; border-radius: 4px; font-size: 12px; color: #555;">+<?php echo $remaining; ?> more</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Response Time & Score
                            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                <div style="background: #f0f0f0; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                    Response Time: <?php echo esc_html($response_time); ?>
                                </div>
                                <div style="background: #f0f0f0; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                Real Esate Score: <?php echo esc_html($sulekha_score); ?>
                                </div>
                            </div> -->
                            
                            <!-- Business Description -->
                            <div class="business-description">
                                <?php 
                                // Use bio if available, otherwise fall back to excerpt/content
                                if (!empty($bio)) {
                                    echo wp_trim_words($bio, 10, '...');
                                } else {
                                    $excerpt = get_the_excerpt();
                                    if (empty($excerpt)) {
                                        $excerpt = wp_trim_words(get_the_content(), 10, '...');
                                    }
                                    echo $excerpt;
                                }
                                ?>
                            </div>
                            
                            <!-- Enquire Now Button -->
                            <div class="portfolio_button" style="">
                                <a href="<?php echo get_the_permalink(); ?> " style="">View Details</a>
                            </div>
                        </div>
                    </div>              
                <?php 
                //===============
                if ($i % 2 == 1) {
                    //echo '</div>'; // Close after the second item
                }
                $i++;
            endwhile; 
            // Close the last open div if the total count is odd
            if ($i % 2 != 0) {
            // echo '</div>';
            }
            ?>
            </div>
        </div>
        <?php 
        // Add pagination
        $total_pages = $query->max_num_pages;
        
        if ($total_pages > 1) : 
        ?>
        <div class="portfolio-pagination" style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
            <div class="pagination-numbers" style="display: inline-flex; gap: 5px;">
                <?php 
                // Previous page link
                if ($paged > 1) : 
                    $prev_link = add_query_arg('portfolio_page', $paged - 1);
                ?>
                    <a href="<?php echo esc_url($prev_link); ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #555;">&laquo;</a>
                <?php endif; ?>
                
                <?php 
                // Calculate page links to show
                $start_page = max(1, $paged - 2);
                $end_page = min($total_pages, $paged + 2);
                
                // Always show first page
                if ($start_page > 1) : 
                    $first_link = add_query_arg('portfolio_page', 1);
                ?>
                    <a href="<?php echo esc_url($first_link); ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #555;">1</a>
                    <?php if ($start_page > 2) : ?>
                        <span style="display: inline-block; padding: 8px 12px; color: #555;">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php 
                // Page numbers
                for ($i = $start_page; $i <= $end_page; $i++) :
                    $page_link = add_query_arg('portfolio_page', $i);
                    $active_class = ($i == $paged) ? 'background-color: #0073e1; color: white;' : 'color: #555;';
                ?>
                    <a href="<?php echo esc_url($page_link); ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; <?php echo $active_class; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php 
                // Always show last page
                if ($end_page < $total_pages) : 
                    $last_link = add_query_arg('portfolio_page', $total_pages);
                ?>
                    <?php if ($end_page < $total_pages - 1) : ?>
                        <span style="display: inline-block; padding: 8px 12px; color: #555;">...</span>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($last_link); ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #555;"><?php echo $total_pages; ?></a>
                <?php endif; ?>
                
                <?php 
                // Next page link
                if ($paged < $total_pages) : 
                    $next_link = add_query_arg('portfolio_page', $paged + 1);
                ?>
                    <a href="<?php echo esc_url($next_link); ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #555;">&raquo;</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p>No business listings found.</p>
    <?php endif;
    
    return ob_get_clean();
}
add_shortcode('portfolio_business', 'portfolio_business_listing_shortcode');

// Portfolio data fatch
function wpestate_populate_portfolio_form() {
    $current_user = wp_get_current_user();
 
    $user_id      = $current_user->ID;
    $user_role    = intval(get_user_meta($user_id, 'user_estate_role', true));

    if ($user_role == 3) { // Check if the user is an agency
        $agent_id = get_user_meta($user_id, 'user_agent_id', true);

        if ($agent_id) {                                    
            $agency_name     = get_the_title($agent_id); 
            $user_email      = $current_user->user_email;
            $agency_mobile   = get_post_meta($agent_id, 'agency_mobile', true);
            $agency_address  = get_post_meta($agent_id, 'agency_address', true);
            $agency_facebook = get_post_meta($agent_id, 'agency_facebook', true);
            $agency_instagram= get_post_meta($agent_id, 'agency_instagram', true);
            $agency_twitter  = get_post_meta($agent_id, 'agency_twitter', true);
            $agency_linkedin = get_post_meta($agent_id, 'agency_linkedin', true);
            $agency_youtube  = get_post_meta($agent_id, 'agency_youtube', true);
            $agency_tiktok   = get_post_meta($agent_id, 'agency_tiktok', true);
            $agency_website  = get_post_meta($agent_id, 'agency_website', true);
            $user_bio        = get_post_field('post_content', $agent_id);
            $profile_image_url = get_user_meta($user_id, 'custom_picture', true);

            ?>
            <script type="text/javascript">
                document.addEventListener('DOMContentLoaded', function() {
                    const fullNameInput = document.getElementById('fullName');
                    const emailInput = document.getElementById('email');
                    const phoneInput = document.getElementById('phone');
                    const addressInput = document.getElementById('address');
                    const bioTextarea = document.getElementById('bio');
                    const facebookInput = document.getElementById('facebook');
                    const instagramInput = document.getElementById('instagram');
                    const twitterInput = document.getElementById('twitter');
                    const linkedinInput = document.getElementById('linkedin');
                    const youtubeInput = document.getElementById('youtube');
                    const tiktokInput = document.getElementById('tiktok');
                    const websiteInput = document.getElementById('website');
                    const profileImagePreview = document.getElementById('profile-image-preview');
                    const uploadButton = document.getElementById('upload-button');

                    if (fullNameInput && '<?php echo esc_js($agency_name); ?>') {
                        fullNameInput.value = '<?php echo esc_js($agency_name); ?>';
                    }
                    if (emailInput && '<?php echo esc_js($user_email); ?>') {
                        emailInput.value = '<?php echo esc_js($user_email); ?>';
                    }
                    if (phoneInput && '<?php echo esc_js($agency_mobile); ?>') {
                        phoneInput.value = '<?php echo esc_js($agency_mobile); ?>';
                    }
                    if (addressInput && '<?php echo esc_js($agency_address); ?>') {
                        addressInput.value = '<?php echo esc_js($agency_address); ?>';
                    }
                    if (bioTextarea && '<?php echo esc_js($user_bio); ?>') {
                        bioTextarea.value = '<?php echo esc_js($user_bio); ?>';
                    }
                    if (facebookInput && '<?php echo esc_js($agency_facebook); ?>') {
                        facebookInput.value = '<?php echo esc_js($agency_facebook); ?>';
                    }
                    if (instagramInput && '<?php echo esc_js($agency_instagram); ?>') {
                        instagramInput.value = '<?php echo esc_js($agency_instagram); ?>';
                    }
                    if (twitterInput && '<?php echo esc_js($agency_twitter); ?>') {
                        twitterInput.value = '<?php echo esc_js($agency_twitter); ?>';
                    }
                    if (linkedinInput && '<?php echo esc_js($agency_linkedin); ?>') {
                        linkedinInput.value = '<?php echo esc_js($agency_linkedin); ?>';
                    }
                    if (youtubeInput && '<?php echo esc_js($agency_youtube); ?>') {
                        youtubeInput.value = '<?php echo esc_js($agency_youtube); ?>';
                    }
                    if (tiktokInput && '<?php echo esc_js($agency_tiktok); ?>') {
                        tiktokInput.value = '<?php echo esc_js($agency_tiktok); ?>';
                    }
                    if (websiteInput && '<?php echo esc_js($agency_website); ?>') {
                        websiteInput.value = '<?php echo esc_js($agency_website); ?>';
                    }
                    if (profileImagePreview && '<?php echo esc_url($profile_image_url); ?>') {
                        profileImagePreview.innerHTML = '<img src="<?php echo esc_url($profile_image_url); ?>" alt="Profile Image" style="max-width: 100px; height: auto;">';
                        // Optionally, you might want to disable the upload button or indicate that an image is already present.
                        if (uploadButton) {
                            uploadButton.textContent = 'Image Uploaded';
                            uploadButton.disabled = true;
                        }
                    }
                });
            </script>
            <?php
        }
    }
}
//add_action('wp_footer', 'wpestate_populate_portfolio_form');



//_______________________city & country
/**
 * County and City Manager functionality
 * Add this code to your theme's functions.php file
 */

// Step 1: Create custom table on theme activation
function create_county_city_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Table for counties
    $table_counties = $wpdb->prefix . 'counties';
    $sql_counties = "CREATE TABLE IF NOT EXISTS $table_counties (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        county_name varchar(100) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    // Table for cities with county relationship
    $table_cities = $wpdb->prefix . 'cities';
    $sql_cities = "CREATE TABLE IF NOT EXISTS $table_cities (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        city_name varchar(100) NOT NULL,
        county_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_counties);
    dbDelta($sql_cities);
}
// Run this function once to create tables
add_action('after_switch_theme', 'create_county_city_tables');
// Also add this line to run it immediately when the code is added to functions.php
// Comment it out after the tables are created
create_county_city_tables();

// Step 2: Add admin menu for managing counties and cities
function county_city_admin_menu() {
    add_menu_page(
        'County & City Manager',
        'Counties & Cities',
        'manage_options',
        'county-city-manager',
        'county_city_manager_page',
        'dashicons-location',
        30
    );
}
add_action('admin_menu', 'county_city_admin_menu');

// Admin page content
function county_city_manager_page() {
    global $wpdb;
    $counties_table = $wpdb->prefix . 'counties';
    $cities_table = $wpdb->prefix . 'cities';
    
    // Process form submissions
    if (isset($_POST['add_county']) && !empty($_POST['county_name'])) {
        $wpdb->insert(
            $counties_table,
            ['county_name' => sanitize_text_field($_POST['county_name'])]
        );
        echo '<div class="notice notice-success"><p>County added successfully!</p></div>';
    }
    
    if (isset($_POST['add_city']) && !empty($_POST['city_name']) && !empty($_POST['county_id'])) {
        $wpdb->insert(
            $cities_table,
            [
                'city_name' => sanitize_text_field($_POST['city_name']),
                'county_id' => intval($_POST['county_id'])
            ]
        );
        echo '<div class="notice notice-success"><p>City added successfully!</p></div>';
    }
    
    // Delete operations
    if (isset($_GET['delete_county']) && !empty($_GET['delete_county'])) {
        $county_id = intval($_GET['delete_county']);
        $wpdb->delete($counties_table, ['id' => $county_id]);
        $wpdb->delete($cities_table, ['county_id' => $county_id]); // Delete related cities
        echo '<div class="notice notice-success"><p>County and its cities deleted successfully!</p></div>';
    }
    
    if (isset($_GET['delete_city']) && !empty($_GET['delete_city'])) {
        $city_id = intval($_GET['delete_city']);
        $wpdb->delete($cities_table, ['id' => $city_id]);
        echo '<div class="notice notice-success"><p>City deleted successfully!</p></div>';
    }
    
    // Get existing counties and cities
    $counties = $wpdb->get_results("SELECT * FROM $counties_table ORDER BY county_name");
    ?>
    <div class="wrap">
        <h1>County & City Manager</h1>
        
        <div style="display: flex; gap: 20px;">
            <!-- County Management -->
            <div style="flex: 1; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2>Manage Counties</h2>
                <form method="post">
                    <div style="margin-bottom: 15px;">
                        <input type="text" name="county_name" placeholder="County Name" required style="width: 100%;">
                    </div>
                    <button type="submit" name="add_county" class="button button-primary">Add County</button>
                </form>
                
                <h3 style="margin-top: 20px;">Existing Counties</h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>County Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($counties as $county): ?>
                        <tr>
                            <td><?php echo $county->id; ?></td>
                            <td><?php echo esc_html($county->county_name); ?></td>
                            <td>
                                <a href="?page=county-city-manager&delete_county=<?php echo $county->id; ?>" 
                                   onclick="return confirm('Are you sure? This will also delete all cities in this county!');"
                                   class="button button-small">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($counties)): ?>
                            <tr><td colspan="3">No counties found. Please add some.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- City Management -->
            <div style="flex: 1; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2>Manage Cities</h2>
                <form method="post">
                    <div style="margin-bottom: 15px;">
                        <select name="county_id" required style="width: 100%;">
                            <option value="">-- Select County --</option>
                            <?php foreach ($counties as $county): ?>
                                <option value="<?php echo $county->id; ?>"><?php echo esc_html($county->county_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <input type="text" name="city_name" placeholder="City Name" required style="width: 100%;">
                    </div>
                    <button type="submit" name="add_city" class="button button-primary">Add City</button>
                </form>
                
                <h3 style="margin-top: 20px;">Existing Cities</h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>City Name</th>
                            <th>County</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cities = $wpdb->get_results("
                            SELECT c.*, co.county_name 
                            FROM $cities_table c
                            JOIN $counties_table co ON c.county_id = co.id
                            ORDER BY co.county_name, c.city_name
                        ");
                        foreach ($cities as $city):
                        ?>
                        <tr>
                            <td><?php echo $city->id; ?></td>
                            <td><?php echo esc_html($city->city_name); ?></td>
                            <td><?php echo esc_html($city->county_name); ?></td>
                            <td>
                                <a href="?page=county-city-manager&delete_city=<?php echo $city->id; ?>" 
                                   onclick="return confirm('Are you sure?');"
                                   class="button button-small">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($cities)): ?>
                            <tr><td colspan="4">No cities found. Please add some.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

// Step 3: Front-end form modifications - Enqueue scripts for AJAX
function county_city_enqueue_scripts() {
    wp_enqueue_script('jquery');
    
    // Add custom JavaScript for dependent dropdowns
    wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            // When county dropdown changes
            $(document).on("change", "#county", function() {
                var county_id = $(this).val();
                var selected_city = $("#multiStepPortfolioForm").find("#city").val();
                // AJAX request to get cities for selected county
                $.ajax({
                    url: "' . admin_url('admin-ajax.php') . '",
                    type: "POST",
                    data: {
                        action: "get_cities_by_county",
                        county_id: county_id,
                        selected_city: selected_city
                    },
                    success: function(response) {
                        $("#city").html(response);
                    }
                });
            });
            $("#multiStepPortfolioForm").find("#county").trigger("change");            
        });
    ');
}
add_action('wp_enqueue_scripts', 'county_city_enqueue_scripts');

// AJAX handler for getting cities by county
function get_cities_by_county() {
    global $wpdb;
    $county_id = isset($_POST['county_id']) ? intval($_POST['county_id']) : 0;
    
    if ($county_id > 0) {
        $cities_table = $wpdb->prefix . 'cities';
        $cities = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $cities_table WHERE county_id = %d ORDER BY city_name",
            $county_id
        ));
        $selected_city = isset($_POST['selected_city']) ? intval($_POST['selected_city']) : 0;
        echo '<option value="">-- Select City --</option>';
        foreach ($cities as $city) {
            // Check if the city is selected
            $selected = ($selected_city == $city->id) ? 'selected' : '';
            echo '<option value="' . esc_attr($city->id) . '" '.$selected.'>' . esc_html($city->city_name) . '</option>';
        }
    } else {
        echo '<option value="">-- Select City --</option>';
    }
    
    wp_die();
}
add_action('wp_ajax_get_cities_by_county', 'get_cities_by_county');
add_action('wp_ajax_nopriv_get_cities_by_county', 'get_cities_by_county');

function get_cities_data_by_county($county_id = 1) {
    global $wpdb;

    $county_id = intval($county_id);
    if ($county_id <= 0) {
        $county_id = 1;
    }

    $cities_table = $wpdb->prefix . 'cities';

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$cities_table} WHERE county_id = %d ORDER BY city_name ASC",
            $county_id
        ),
        ARRAY_A
    );

    // Restructure the array to use city ID as key
    $cities = [];
    foreach ($results as $row) {
        if (isset($row['id'])) {
            $cities[$row['id']] = $row;
        }
    }

    return $cities;
}

function get_countries_list() {
    global $wpdb;
    $countries_table = $wpdb->prefix . 'countries';
    
    // Check if the table exists
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}counties ORDER BY county_name", ARRAY_A);
    
    //$results = $wpdb->get_results("SELECT * FROM {$countries_table} WHERE 1=1", ARRAY_A);   
    // Restructure the array to use country ID as key
    $countries = [];
    foreach ($results as $row) {
        if (isset($row['id'])) {
            $countries[$row['id']] = $row;
        }
    }
    return $countries;
}
   


/**
 * Helper function to get county dropdown HTML
 * You can use this in your form template
 */
function get_county_dropdown($selected_id = '') {
    global $wpdb;
    $counties_table = $wpdb->prefix . 'counties';
    $counties = $wpdb->get_results("SELECT * FROM $counties_table ORDER BY county_name");
    
    ob_start();
    ?>
    <select name="county" id="county" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        <option value="">-- Select County --</option>
        <?php foreach ($counties as $county): ?>
            <option value="<?php echo esc_attr($county->id); ?>" <?php selected($selected_id, $county->id); ?>>
                <?php echo esc_html($county->county_name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
    return ob_get_clean();
}

/**
 * Helper function to get city dropdown HTML
 * You can use this in your form template
 */
function get_city_dropdown($county_id = '', $selected_id = '') {
    global $wpdb;
    $cities_table = $wpdb->prefix . 'cities';
    
    ob_start();
    ?>
    <select name="city" id="city" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        <option value="">-- Select City --</option>
        <?php
        if (!empty($county_id)) {
            $cities = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $cities_table WHERE county_id = %d ORDER BY city_name",
                $county_id
            ));
            
            foreach ($cities as $city): ?>
                <option value="<?php echo esc_attr($city->id); ?>" <?php selected($selected_id, $city->id); ?>>
                    <?php echo esc_html($city->city_name); ?>
                </option>
            <?php endforeach;
        }
        ?>
    </select>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode to display county and city dropdowns
 * You can use [county_city_form] shortcode in your pages or posts
 */
function county_city_form_shortcode($atts) {
    ob_start();
    ?>
    <!-- County Dropdown -->
    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
        <div style="flex: 1; position: relative;">
            <?php echo get_county_dropdown(); ?>
            <span class="required-indicator" style="color: red; position: absolute; top: 10px; right: 10px;">*</span>
        </div>
    </div>
            
    <!-- City Dropdown -->
    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
        <div style="flex: 1; position: relative;">
            <?php echo get_city_dropdown(); ?>
            <span class="required-indicator" style="color: red; position: absolute; top: 10px; right: 10px;">*</span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('county_city_form', 'county_city_form_shortcode');

/**
 * Example of how to modify your existing form
 * Replace this with code that fits your specific theme/form
 */
function modify_your_custom_form() {

    echo do_shortcode('[county_city_form]');
    
    // OR use the helper functions directly in your template
    ?>
    <!-- County Field (replacing address) -->
    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
        <div style="flex: 1; position: relative;">
            <?php echo get_county_dropdown(); ?>
            <span class="required-indicator" style="color: red; position: absolute; top: 10px; right: 10px;">*</span>
        </div>
    </div>
            
    <!-- City Field -->
    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
        <div style="flex: 1; position: relative;">
            <?php echo get_city_dropdown(); ?>
            <span class="required-indicator" style="color: red; position: absolute; top: 10px; right: 10px;">*</span>
        </div>
    </div>
    <?php
}
// Uncomment and modify this to hook into your form
// add_action('your_form_hook_name', 'modify_your_custom_form');


add_filter( 'woocommerce_thankyou_order_received_text', 'custom_thankyou_message_based_on_user_role', 20, 2 );

function custom_thankyou_message_based_on_user_role( $thank_you_msg, $order_id ) {
    // Get order
    $order = wc_get_order( $order_id );

    // Get current user
    $current_user = wp_get_current_user();

    // Check if user is logged in
    if ( $current_user->exists() ) {

        $user_roles = $current_user->roles;        

        if ( in_array( 'agency', $user_roles ) ) {
            $url = home_url('dashboard-profile-page'); // Redirect to homepage
            $thank_you_msg = '</br><a href="' . esc_url($url) . '" class="return_woo_button">' . esc_html__('Return to Dashboard', 'wpresidence-core') . '</a>';
        }        
    }

    return $thank_you_msg;
}


add_action('created_property_area', 'save_area_city_meta', 10, 2);
add_action('edited_property_area', 'save_area_city_meta', 10, 2);

function save_area_city_meta($term_id) {
    if (isset($_POST['cityparent'])) {
        update_term_meta($term_id, 'cityparent', sanitize_text_field($_POST['cityparent']));
    }
}

// checkout page coupon field show
add_action('woocommerce_before_checkout_form', 'always_show_coupon_field', 5);
function always_show_coupon_field() {
    if (wc_coupons_enabled()) {
        echo '<div class="custom-coupon">';
        woocommerce_checkout_coupon_form();
        echo '</div>';
    }
}
add_action('woocommerce_review_order_before_payment', function() {
    ?>
    <form class="checkout_coupon woocommerce-form-coupon" method="post">
        <input type="text" name="coupon_code" placeholder="Enter coupon code" />
        <button type="submit" name="apply_coupon">Apply</button>
    </form>
    <?php
});

add_filter('woocommerce_cart_needs_payment', function($needs_payment) {

    if (WC()->cart && WC()->cart->total == 0) {
        return false;
    }

    return $needs_payment;
});


add_action('woocommerce_review_order_after_submit', function() {

    if (WC()->cart && WC()->cart->total == 0) {
        echo '<button type="submit" class="button alt" style="width:100%;margin-top:10px;">Place Order</button>';
    }

});


// Pluign Hide update hide update notifications

function remove_core_updates(){

    global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);

}

add_filter('pre_site_transient_update_core','remove_core_updates'); //hide updates for WordPress itself

add_filter('pre_site_transient_update_plugins','remove_core_updates'); //hide updates for all plugins

add_filter('pre_site_transient_update_themes','remove_core_updates'); //hide updates for all themes
