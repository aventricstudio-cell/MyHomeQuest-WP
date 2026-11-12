<?php
/**
 * WpResidence License tab.
 * Displays registration and deregistration forms for the theme license.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output the License tab content.
 */
function wpresidence_ptc_render_license_tab() {
    $license = WpestateFunk::get_instance();
    echo '<div class="wpresidence-settings">';
    $license->show_deregister_license_form();
    echo '</div>';
}
