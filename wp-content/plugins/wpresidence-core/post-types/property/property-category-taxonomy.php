<?php
/**
 * Property Category Taxonomy Functions
 *
 * Handles the creation and management of custom fields for the property category taxonomy.
 * Includes functions for displaying edit/add forms and saving custom field data.
 *
 * Custom fields include:
 * - Page ID association
 * - Featured image
 * - Category tagline
 * 
 * @package WpResidence
 * @subpackage Taxonomies
 * @since 1.0
 */

// Register callbacks for category term form display and data saving
add_action('property_category_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_category_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_category', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_category', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

if (!function_exists('wpestate_property_category_callback_function')):
    /**
     * Displays custom fields on the property category term edit form in tabs.
     *
     * @param object $tag The term object being edited.
     */
    function wpestate_property_category_callback_function($tag) {
        if (is_object($tag)) {
            $t_id    = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $term_meta_array     = wpestate_parse_category_term_array($term_meta);
            $pagetax             = $term_meta_array['pagetax'];
            $category_featured_image = $term_meta_array['category_featured_image'];
            $category_featured_image_icon = $term_meta_array['category_featured_image_icon'];
            $category_tagline    = $term_meta_array['category_tagline'];
            $category_attach_id  = $term_meta_array['category_attach_id'];
            $category_gallery    = $term_meta_array['category_gallery'];
            $category_documents  = isset( $term_meta_array['category_documents'] ) ? $term_meta_array['category_documents'] : '';
            $term_address        = $term_meta_array['term_address'];
            $term_zip            = $term_meta_array['term_zip'];
            $term_country        = $term_meta_array['term_country'];
            $term_latitude       = $term_meta_array['term_latitude'];
            $term_longitude      = $term_meta_array['term_longitude'];
            $page_custom_zoom    = $term_meta_array['page_custom_zoom'];
            $google_camera_angle = $term_meta_array['google_camera_angle'];
            $term_google_view    = $term_meta_array['term_google_view'];
            $term_geojson        = isset( $term_meta_array['term_geojson'] ) ? $term_meta_array['term_geojson'] : '';
        } else {
            $pagetax = '';
            $category_featured_image = '';
            $category_featured_image_icon = '';
            $category_tagline = '';
            $category_attach_id = '';
            $category_gallery = '';
            $category_documents = '';
            $term_address = '';
            $term_zip = '';
            $term_country = '';
            $term_latitude = '';
            $term_longitude = '';
            $page_custom_zoom = 16;
            $google_camera_angle = '';
            $term_google_view = '';
            $term_geojson     = '';
        }

        $taxonomy_slug = is_object($tag) && isset($tag->taxonomy) ? $tag->taxonomy : 'property_category';

        $stateparent = '';
        if ( 'property_city' === $taxonomy_slug ) {
            if ( is_object( $tag ) ) {
                $selected    = isset( $term_meta['stateparent'] ) ? $term_meta['stateparent'] : '';
                $stateparent = wpestate_get_all_states( $selected );
            } else {
                $stateparent = wpestate_get_all_states();
            }
        }

        $cityparent = '';
        if ( 'property_area' === $taxonomy_slug ) {
            if ( is_object( $tag ) ) {
                $selected   = isset( $term_meta['cityparent'] ) ? $term_meta['cityparent'] : '';
                $cityparent = wpestate_get_all_cities( $selected );
            } else {
                $cityparent = wpestate_get_all_cities();
            }
        }
        ?>
        
        <table class="form-table">
            <tbody>
                <tr class="form-field">
                    <th colspan="2">
                        <div id="property_category_options_wrapper" class="property_options_wrapper meta-options">
                            <div class="property_options_wrapper_list">
                                <div class="property_tab_item active_tab" data-content="term_details"><?php echo esc_html__('Details', 'wpresidence-core'); ?></div>
                                <div class="property_tab_item" data-content="term_media"><?php echo esc_html__('Media', 'wpresidence-core'); ?></div>
                                <div class="property_tab_item" data-content="term_documents"><?php echo esc_html__('Documents', 'wpresidence-core'); ?></div>
                                <div class="property_tab_item" data-content="term_maps"><?php echo esc_html__('Maps', 'wpresidence-core'); ?></div>
                                <div class="property_tab_item" data-content="term_custom_data"><?php echo esc_html__('Custom Data', 'wpresidence-core'); ?></div>
                            </div>
                            
                            <div class="property_options_content_wrapper">
                                <!-- Details Tab -->
                                <div class="property_tab_item_content active_tab" id="term_details">
                                    <div class="prop_full">
                                        <label for="term_meta[pagetax]"><?php echo esc_html__('Page id for this term(deprecated - will be removed)', 'wpresidence-core'); ?></label>
                                        <input type="text" name="term_meta[pagetax]" class="postform wpresidence-2025-input" value="<?php echo esc_attr($pagetax); ?>">
                                      
                                    </div>
                                    
                                    <div class="prop_full">
                                        <label for="term_meta[category_tagline]"><?php echo esc_html__('Category Tagline', 'wpresidence-core'); ?></label>
                                        <input id="category_tagline" type="text" size="36" class="wpresidence-2025-input" name="term_meta[category_tagline]" value="<?php echo esc_attr($category_tagline); ?>" />
                                    </div>
                                    
                                    <?php if ( 'property_city' === $taxonomy_slug ) : ?>
                                        <div class="prop_full">
                                            <label for="term_meta[stateparent]"><?php echo esc_html__('Which county / state has this city', 'wpresidence-core'); ?></label>
                                                  
                                            <select name="term_meta[stateparent]" class="postform wpresidence-2025-select">
                                                <?php echo $stateparent; ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ( 'property_area' === $taxonomy_slug ) : ?>
                                        <div class="prop_full">
                                                <label for="term_meta[cityparent]"><?php echo esc_html__('Which city has this area', 'wpresidence-core'); ?></label>
                                                <select name="term_meta[cityparent]" class="postform wpresidence-2025-select">
                                                    <?php echo $cityparent; ?>
                                                </select>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ( 'property_features' === $taxonomy_slug ) : ?>
                                        <div class="prop_full">
                                            <label for="category_featured_image_icon">
                                                <?php echo esc_html__('SVG ICON - SVG ONLY!', 'wpresidence-core'); ?> -
                                                <a target="_blank" href="https://help.wpresidence.net/article/listings-features-and-amenities-listings-labels/">
                                                    <?php echo esc_html__('Video Tutorial', 'wpresidence-core'); ?>
                                                </a>
                                            </label>

                                            <input id="category_featured_image_icon" type="text" class="postform wpestate_landing_upload wpresidence-2025-input" size="36" name="term_meta[category_featured_image_icon]" value="<?php echo esc_attr($category_featured_image_icon); ?>" />
                                            <input id="category_featured_image_icon_button" type="button" class="upload_button button media_upload_button category_featured_image_button" value="<?php echo esc_html__('Upload SVG', 'wpresidence-core'); ?>" />
                                            <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="<?php echo esc_attr($category_attach_id); ?>" />
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Media Tab -->
                                <div class="property_tab_item_content" id="term_media">
                                    <div class="property_prop_half prop_full ">
                                        <label for="category_featured_image"><?php echo esc_html__('Featured Image', 'wpresidence-core'); ?></label>
                                        <input id="category_featured_image" type="text" class="postform wpestate_landing_upload wpresidence-2025-input" size="36" name="term_meta[category_featured_image]" value="<?php echo esc_attr($category_featured_image); ?>" />
                                        <button  type="button" id="category_featured_image_button" class="upload_button button media_upload_button category_featured_image_button"><?php echo esc_html__('Upload Image', 'wpresidence-core'); ?></button>
                                        <?php if ( 'property_features' !== $taxonomy_slug ) : ?>
                                            <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="<?php echo esc_attr($category_attach_id); ?>" />
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="property_prop_half prop_full">
                                        <label><?php echo esc_html__('Gallery Images', 'wpresidence-core'); ?></label>
                                        <div class="property_uploaded_thumb_wrapepr">
                                            <?php foreach ( array_filter( explode( ',', $category_gallery ) ) as $img_id ) : ?>
                                                <?php $preview = wp_get_attachment_image_src( $img_id, 'thumbnail' ); ?>
                                                <?php if ( $preview ) : ?>
                                                    <div class="uploaded_thumb" data-imageid="<?php echo esc_attr( $img_id ); ?>">
                                                        <img src="<?php echo esc_url( $preview[0] ); ?>" alt="">
                                                        <span class="wpresidence_term_attach_delete dashicons dashicons-trash"></span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="hidden" class="wpestate_term_gallery_ids" name="term_meta[category_gallery]" value="<?php echo esc_attr($category_gallery); ?>" />
                                       <button type="button" class="button  media_upload_button term-gallery-upload-button"><?php echo esc_html__('Upload/Add Images', 'wpresidence-core'); ?></button>
                                   </div>
                               </div>

                                <!-- Documents Tab -->
                                <div class="property_tab_item_content" id="term_documents">
                                    <div class="property_prop_half prop_full">
                                        <label><?php echo esc_html__('PDF Documents', 'wpresidence-core'); ?></label>
                                        <div class="property_uploaded_thumb_wrapepr">
                                            <?php foreach ( array_filter( explode( ',', $category_documents ) ) as $doc_id ) : ?>
                                                <?php
                                                $preview = wp_get_attachment_image_src( $doc_id, 'thumbnail' );
                                                $src     = $preview ? $preview[0] : get_theme_file_uri( '/img/pdf.png' );
                                                ?>
                                                <div class="uploaded_thumb" data-imageid="<?php echo esc_attr( $doc_id ); ?>">
                                                    <img src="<?php echo esc_url( $src ); ?>" alt="">
                                                    <span class="wpresidence_term_attach_delete dashicons dashicons-trash"></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="hidden" class="wpestate_term_document_ids" name="term_meta[category_documents]" value="<?php echo esc_attr($category_documents); ?>" />
                                        <button type="button" class="button  media_upload_button term-document-upload-button"><?php echo esc_html__('Upload/Add Files', 'wpresidence-core'); ?></button>
                                    </div>
                                </div>

                                <!-- Maps Tab -->
                                <div class="property_tab_item_content" id="term_maps">
                                    <div id="googleMap" style="width:100%;height:380px;"></div>
                                    
                                    <div>
                                        <a class="button" href="#" id="admin_place_pin"><?php echo esc_html__('Place Pin base on location', 'wpresidence-core'); ?></a>
                                    </div>
                                    <div class="property_prop_term_geojson">
                                        <label><?php echo esc_html__('GeoJson file(draw on map): ', 'wpresidence-core'); ?></label>
                                        <input id="term_geojson" type="text" size="40" class="wpestate_landing_upload wpresidence-2025-input" name="term_meta[term_geojson]" value="<?php echo esc_attr($term_geojson); ?>" />
                                        <button type="button" id="term_geojson_button" class="button media_upload_button term-geojson-upload-button"><?php echo esc_html__('Upload GeoJSON', 'wpresidence-core'); ?></button>
                                    </div>
                                    
                                    <div class="property_prop">
                                        <label for="term_meta[term_address]"><?php echo esc_html__('Location (*if it is needed): ', 'wpresidence-core'); ?></label><br />
                                        <input type="text" id="term_address" size="40" name="term_meta[term_address]" value="<?php echo esc_attr($term_address); ?>">
                                    </div>
                                    <div class="property_category_map_flex">
                                        <div class="property_prop_half">
                                            <label for="term_meta[term_zip]"><?php echo esc_html__('Zip: ', 'wpresidence-core'); ?></label><br />
                                            <input type="text" id="term_zip" size="40" name="term_meta[term_zip]" value="<?php echo esc_attr($term_zip); ?>">
                                        </div>
                                        
                                        <div class="property_prop_half">
                                            <label for="property_country"><?php echo esc_html__('Country: ', 'wpresidence-core'); ?></label><br />
                                            <?php echo wpestate_country_list( esc_attr($term_country), '', 'term_meta[term_country]' ); ?>
                                        </div>
                                        
                                        <div class="property_prop_half">
                                            <label for="term_meta[term_latitude]"><?php echo esc_html__('Latitude:', 'wpresidence-core'); ?></label><br />
                                            <input type="text" id="term_latitude" style="margin-right:20px;" size="40" name="term_meta[term_latitude]" value="<?php echo esc_attr($term_latitude); ?>">
                                        </div>
                                        
                                        <div class="property_prop_half">
                                            <label for="term_meta[term_longitude]"><?php echo esc_html__('Longitude:', 'wpresidence-core'); ?></label><br />
                                            <input type="text" id="term_longitude" style="margin-right:20px;" size="40" name="term_meta[term_longitude]" value="<?php echo esc_attr($term_longitude); ?>">
                                        </div>
                                        
                                        <div class="property_prop_half">
                                            <label for="term_meta[page_custom_zoom]"><?php echo esc_html__('Zoom Level for map (1-20)', 'wpresidence-core'); ?></label><br />
                                            <select name="term_meta[page_custom_zoom]" id="page_custom_zoom">
                                                <?php for ($i=1; $i<21; $i++) : ?>
                                                    <option value="<?php echo $i; ?>" <?php selected($page_custom_zoom, $i, true); ?>><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="property_prop_half">
                                            <label for="term_meta[google_camera_angle]"><?php echo esc_html__('Google View Camera Angle', 'wpresidence-core'); ?></label>
                                            <input type="text" id="google_camera_angle" style="margin-right:0px;" size="5" name="term_meta[google_camera_angle]" value="<?php echo esc_attr($google_camera_angle); ?>">
                                        </div>
                                        
                                        <div class="property_prop_half" style="padding-top:20px;">
                                            <input type="hidden" name="term_meta[term_google_view]" value="">
                                            <input type="checkbox" class="wpresidence-admin-checkbox" id="term_google_view" name="term_meta[term_google_view]" value="1" <?php checked($term_google_view, 1, true); ?> />
                                            <label class="checklabel" style="display:inline;" for="term_google_view"><?php echo esc_html__('Enable Google Street View', 'wpresidence-core'); ?></label>
                                        </div>
                                    </div>    
                                </div>

                                <!-- Custom Data Tab -->
                                <div class="property_tab_item_content" id="term_custom_data">
                                    <?php wpestate_term_custom_fields_render( $tag ); ?>
                                </div>
                            </div>
                        </div>
                        
                        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="<?php echo esc_attr($taxonomy_slug); ?>" />
                    </th>
                </tr>
            </tbody>
        </table>
        
        <?php
    }
endif;

if (!function_exists('wpestate_property_category_callback_add_function')):
    /**
     * Displays custom fields on the property category term add form
     * 
     * Adds form fields for creating new property category terms including:
     * - Page ID association
     * - Featured image upload 
     * - Category tagline
     *
     * @param object $tag The term object (empty for new terms)
     * @return void Outputs HTML form fields
     */
    function wpestate_property_category_callback_add_function($tag) {
      print '<div class="wpresidence_edit_term_note">'.esc_html__('For extra details like media, documents, maps. custom info, etc - you need to click edit after the term was added.','wpresidence-core').'</div>';
    }

endif;



