<?php
/**
 * Import Locations tab renderer.
 *
 * Provides a simple interface to import state, city and
 * area terms from a CSV file.
 *
 * @package WpResidence Core
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the Import Locations tab.
 *
 * Includes a simple upload field for the CSV file and
 * a button to trigger the import via AJAX.
 */
function wpresidence_ptc_render_import_locations_tab() {
    ?>
    <form id="wpresidence-import-locations-form" method="post" action="">
        <?php wp_nonce_field( 'wpresidence-import-locations', 'wpresidence-import-locations-nonce' ); ?>
        
        <div class="wpresidence-explanations">
            <?php echo esc_html__( 'To Import use a csv file with his header: State, City, Area. Or use the sample file from wpresidence-core/samples/import-locations-sample.csv', 'wpresidence-core' ); ?>
        </div>
            
        <div class="wpresidence-row">
            <input type="text" id="wpresidence-import-file" class="wpresidence-2025-input" />            
            <button id="wpresidence-upload-csv" class="button wpresidence_button secondary"><?php echo esc_html__( 'Choose CSV File', 'wpresidence-core' ); ?></button>
        </div>

        
        <div class="wpresidence-row">
            <button id="wpresidence-run-import" class="button wpresidence_button button-primary"><?php echo esc_html__( 'Import', 'wpresidence-core' ); ?></button>
        </div>
        <p id="wpresidence-import-status"></p>
    </form>
    <?php
}
