<?php
/*MILLDONE
* src: libs/property_page_functions/property_floor_plan_sections.php
*/

/**
 * Property Floor Plans Function
 *
 * This function generates and displays the floor plans for a property in the WpResidence theme.
 * It can output the content either as a tab or as an accordion item.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Whether to display as a tab ('yes') or accordion (default '').
 * @param string $tab_active_class The CSS class for active tabs (optional).
 *
 * @return string|void Returns the HTML content if it's a tab, otherwise echoes the content.
 */

if ( ! function_exists( 'wpestate_property_floor_plans_v2' ) ) :
    function wpestate_property_floor_plans_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Retrieve floor plans data and labels
        $data  = wpestate_return_all_labels_data( 'floor_plans' );
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Generate floor plan content
        ob_start();
        estate_floor_plan( $postID, 0 );
        $content = ob_get_clean();

        // Display content based on the $is_tab parameter
        if ( 'yes' === $is_tab ) {
            // Return content as a tab item
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        } else {
            // Echo content as an accordion item
            echo (
                wpestate_property_page_create_acc(
                    $content,
                    $label,
                    $data['accordion_id'],
                    $data['accordion_id'] . '_collapse'
                )
            );
        }
    }
endif;


/**
 * Floor Plans Display Function
 *
 * This function generates and displays the floor plans for a property in the WpResidence theme.
 * It handles both regular display and print view, and can work with pre-fetched property details.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param int    $post_id                  The ID of the property post.
 * @param int    $is_print                 Whether this is for print view (1) or regular view (0).
 * @param array  $wpestate_prop_all_details Pre-fetched property details (optional).
 */

 if ( ! function_exists( 'estate_floor_plan' ) ) :
    function estate_floor_plan( $post_id, $is_print = 0, $wpestate_prop_all_details = '' ) {
        // Set up print class if needed
        $is_print_class = $is_print ? ' floor_print_class ' : '';

        // Get measurement unit
        $unit = wpestate_get_meaurement_unit_formated();

        // Fetch floor plan data
        $plan_data = estate_get_floor_plan_data( $post_id, $wpestate_prop_all_details );

        // Set up currency details
        $wpestate_currency  = esc_html( wpresidence_get_option( 'wp_estate_currency_symbol', '' ) );
        $where_currency     = esc_html( wpresidence_get_option( 'wp_estate_where_currency_symbol', '' ) );

        // Initialize lightbox content and counter
        $lightbox = '';
        $counter  = 0;
        $show     = ' style="display:block"; ';

        // Display floor plans
        if ( is_array( $plan_data['titles'] ) ) {
            print '<div class="row '.esc_attr($is_print_class).'">';
            foreach ( $plan_data['titles'] as $key => $plan_name ) {
                $counter++;
                estate_display_single_floor_plan( $key, $plan_data, $unit, $wpestate_currency, $where_currency, $is_print_class, $show, $counter );
                $lightbox .= estate_generate_lightbox_content( $plan_data['titles'][$key], $plan_data['descriptions'][$key], $plan_data['image_attachs'][$key], estate_get_plan_details( $key, $plan_data, $unit, $wpestate_currency, $where_currency ), $counter );
                $show = '';
            }

            // Enqueue necessary scripts 
            wp_enqueue_script('owl_carousel');

            // Include floor plans gallery template
            include( locate_template( 'templates/listing_templates/floorplans_gallery.php' ) );

            // Add inline script to initialize lightbox
            wp_add_inline_script('owl_carousel', '
                jQuery(document).ready(function($){
                    estate_start_lightbox_floorplans();
                });
            ');
            print '</div>';
        }
    }
endif;

/**
 * Fetches floor plan data for a property
 *
 * @param int   $post_id                  The ID of the property post.
 * @param array $wpestate_prop_all_details Pre-fetched property details (optional).
 * @return array Associative array of floor plan data
 */
function estate_get_floor_plan_data( $post_id, $wpestate_prop_all_details ) {
    $data_fields = [
        'titles'        => 'plan_title',
        'descriptions'  => 'plan_description',
        'images'        => 'plan_image',
        'sizes'         => 'plan_size',
        'image_attachs' => 'plan_image_attach',
        'rooms'         => 'plan_rooms',
        'baths'         => 'plan_bath',
        'prices'        => 'plan_price',
    ];

    $plan_data = [];

    foreach ( $data_fields as $key => $field ) {
        if ( $wpestate_prop_all_details === '' ) {
            $plan_data[$key] = get_post_meta( $post_id, $field, true );
        } else {
            $plan_data[$key] = unserialize( wpestate_return_custom_field( $wpestate_prop_all_details, $field ) );
        }
    }

    return $plan_data;
}

/**
 * Displays a single floor plan and returns lightbox content
 *
 * @param int    $key              The current floor plan index.
 * @param array  $plan_data        The floor plan data.
 * @param string $unit             The measurement unit.
 * @param string $wpestate_currency The currency symbol.
 * @param string $where_currency   The currency position.
 * @param string $is_print_class   The print class if applicable.
 * @param string $show             The display style.
 * @param int    $counter          The floor plan counter.
 * @return string The lightbox content for this floor plan
 */
function estate_display_single_floor_plan( $key, $plan_data, $unit, $wpestate_currency, $where_currency, $is_print_class, $show, $counter ) {
    $plan_details = estate_get_plan_details( $key, $plan_data, $unit, $wpestate_currency, $where_currency );
    $full_img     = wp_get_attachment_image_src( $plan_data['image_attachs'][$key], 'full' );
    $full_img_path = $full_img ? $full_img[0] : '';

    // Display floor plan row
    estate_display_floor_plan_row( $plan_data['titles'][$key], $plan_details, $is_print_class );

    // Display floor plan image and description
    estate_display_floor_plan_image( $plan_data['titles'][$key], $plan_data['descriptions'][$key], $full_img_path, $is_print_class, $show, $counter );

    // Generate and return lightbox content
    return estate_generate_lightbox_content( $plan_data['titles'][$key], $plan_data['descriptions'][$key], $full_img_path, $plan_details, $counter );
}

/**
 * Gets the details for a single floor plan
 *
 * @param int    $key              The current floor plan index.
 * @param array  $plan_data        The floor plan data.
 * @param string $unit             The measurement unit.
 * @param string $wpestate_currency The currency symbol.
 * @param string $where_currency   The currency position.
 * @return array Associative array of floor plan details
 */
function estate_get_plan_details( $key, $plan_data, $unit, $wpestate_currency, $where_currency ) {
    $details = [];

    if ( isset( $plan_data['sizes'][$key] ) && $plan_data['sizes'][$key] !== '' ) {
        $details['size'] = '<span class="bold_detail">' . esc_html__( 'size:', 'wpresidence' ) . '</span> ' . wpestate_property_size_number_format( wpestate_convert_measure( $plan_data['sizes'][$key] ) ) . ' ' . $unit;
    }

    if ( isset( $plan_data['rooms'][$key] ) && $plan_data['rooms'][$key] !== '' ) {
        $details['rooms'] = '<span class="bold_detail">' . esc_html__( 'rooms: ', 'wpresidence' ) . '</span> ' . $plan_data['rooms'][$key];
    }

    if ( isset( $plan_data['baths'][$key] ) && $plan_data['baths'][$key] !== '' ) {
        $details['baths'] = '<span class="bold_detail">' . esc_html__( 'baths:', 'wpresidence' ) . '</span> ' . $plan_data['baths'][$key];
    }

    if ( isset( $plan_data['prices'][$key] ) && $plan_data['prices'][$key] !== '' ) {
        $details['price'] = '<span class="bold_detail">' . esc_html__( 'price: ', 'wpresidence' ) . '</span> ' . wpestate_show_price_floor( $plan_data['prices'][$key], $wpestate_currency, $where_currency, 1 );
    }

    return $details;
}

/**
 * Displays the floor plan row with details
 *
 * @param string $plan_name      The name of the floor plan.
 * @param array  $plan_details   The details of the floor plan.
 * @param string $is_print_class The print class if applicable.
 */
function estate_display_floor_plan_row( $plan_name, $plan_details, $is_print_class ) {
    ?>
    <div class="front_plan_row <?php echo esc_attr( $is_print_class ); ?>">
        <div class="floor_title"><?php echo esc_html( $plan_name ); ?></div>
        <?php foreach ( $plan_details as $detail_key => $detail ) : ?>
            <div class="floor_details <?php echo $detail_key === 'price' ? 'floor_price_details' : ''; ?>">
                <?php echo wp_kses_post( $detail ); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Displays the floor plan image and description
 *
 * @param string $plan_name      The name of the floor plan.
 * @param string $plan_desc      The description of the floor plan.
 * @param string $full_img_path  The path to the full-size image.
 * @param string $is_print_class The print class if applicable.
 * @param string $show           The display style.
 * @param int    $counter        The floor plan counter.
 */
function estate_display_floor_plan_image( $plan_name, $plan_desc, $full_img_path, $is_print_class, $show, $counter ) {

    ?>
    <div class="front_plan_row_image <?php echo esc_attr( $is_print_class ); ?>" <?php echo trim( $show ); ?>>
        <div class="floor_image">
            <a href="<?php echo esc_url( $full_img_path ); ?>"  title="<?php echo esc_attr( $plan_desc ); ?>">
                <img class="lightbox_trigger_floor" data-slider-no="<?php echo esc_attr( $counter ); ?>" src="<?php echo esc_url( $full_img_path ); ?>" alt="<?php echo esc_attr( $plan_name ); ?>">
            </a>
        </div>
        <div class="floor_description"><?php echo esc_html( $plan_desc ); ?></div>
    </div>
    <?php
}

/**
 * Generates the lightbox content for a floor plan
 *
 * @param string $plan_name     The name of the floor plan.
 * @param string $plan_desc     The description of the floor plan.
 * @param string $full_img_path The path to the full-size image.
 * @param array  $plan_details  The details of the floor plan.
 * @param int    $counter       The floor plan counter.
 * @return string The HTML content for the lightbox
 */
function estate_generate_lightbox_content( $plan_name, $plan_desc, $image_attach, $plan_details, $counter ) {
    $full_img     = wp_get_attachment_image_src( $image_attach, 'full' );
    $full_img_path = $full_img ? $full_img[0] : '';

    ob_start();
    ?>
    <div class="item" href="#<?php echo esc_attr( $counter ); ?>">
        <div class="itemimage">
            <img src="<?php echo esc_url( $full_img_path ); ?>" alt="<?php echo esc_attr( $plan_name ); ?>">
        </div>
        <div class="lightbox_floor_details">
            <div class="floor_title"><?php echo esc_html( $plan_name ); ?></div>
            <div class="floor_light_desc"><?php echo esc_html( $plan_desc ); ?></div>
            <?php foreach ( $plan_details as $detail ) : ?>
                <div class="floor_details"><?php echo wp_kses_post( $detail ); ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}