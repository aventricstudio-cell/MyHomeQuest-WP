<?php
/**MILLODONE
 * Floor Plans Gallery Template
 * src: templates/listing_templates/floorplans_gallery.php
 * This template is responsible for displaying the lightbox gallery of floor plans
 * for a property in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 */

// Ensure that $lightbox variable is available and contains the floor plan content
if (!isset($lightbox) || empty($lightbox)) {
    return; // Exit if no floor plan data is available
}
?>

<div class="lightbox_property_wrapper_floorplans">
    <!-- Main wrapper for the floor plans lightbox -->
    
    <div class="lightbox_property_wrapper_level2">
        <!-- Secondary wrapper for positioning and styling -->
        
        <div class="lightbox_property_content row">
            <!-- Content container with Bootstrap row class -->
            
            <div class="lightbox_property_slider col-md-12">
                <!-- Slider container taking full width on medium screens and up -->
                
                <div id="owl-demo-floor" class="owl-carousel owl-theme">
                    <!-- Owl Carousel container for floor plan slides -->
                    <?php echo trim($lightbox); // Output the floor plan content ?>
                </div>
            </div>
        </div>
        
        <div class="lighbox-image-close-floor">
            <!-- Close button for the lightbox -->
            <i class="fas fa-times" aria-hidden="true"></i>
        </div>
    
    </div>
   
    <div class="lighbox_overlay"></div>
    <!-- Overlay div for dimming the background when lightbox is active -->
    
</div>