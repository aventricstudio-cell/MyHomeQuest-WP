<?php
/**
 * White Label tab renderer.
 *
 * Provides an interface to configure branding options
 * directly from the admin area.
 *
 * @package WpResidence Core
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the White Label settings tab.
 *
 * Allows users to change theme name, author and logo
 * directly from the admin dashboard.
 */
function wpresidence_ptc_render_white_label_tab() {
    if ( defined( 'HIDE_WHITE_LABEL_ACCESS' ) && HIDE_WHITE_LABEL_ACCESS === true ) {
        echo '<div class="notice notice-warning"><p>' . esc_html__( 'White Label settings are currently hidden via wp-config.php', 'wpresidence-core' ) . '</p></div>';
        return;
    }

    $branding = wpresidence_white_label_get_settings();
    ?>
    <form method="post" action="options.php" class="wpresidence-half">
        <?php settings_fields( 'wpresidence_white_label' ); ?>

        <div class="wpresidence-explanations">
            <?php echo esc_html__( "Use define('HIDE_WHITE_LABEL_ACCESS', true); in wp-config.php if you want to hide this section", 'wpresidence-core' ); ?>
        </div>

        <div class="wpresidence-form">
            <div class="wpresidence-row wpresidence-column">
                <label for="wl-branding" class="wpresidence-label-full" ><?php echo esc_html__( 'Theme Branding', 'wpresidence-core' ); ?></label>
                <input type="text" class="wpresidence-2025-input" id="wl-branding" name="wpresidence_white_label[branding]" value="<?php echo esc_attr( $branding['branding'] ); ?>">
                <p class="description"><?php echo esc_html__( 'Replaces "WpResidence" text in the admin area.', 'wpresidence-core' ); ?></p>
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label for="wl-name" class="wpresidence-label-full"><?php echo esc_html__( 'Theme Name', 'wpresidence-core' ); ?></label>
                <input type="text" class="wpresidence-2025-input" id="wl-name" name="wpresidence_white_label[name]" value="<?php echo esc_attr( $branding['name'] ); ?>">
                <p class="description"><?php echo esc_html__( 'Change the text.', 'wpresidence-core' ); ?></p>
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label for="wl-author" class="wpresidence-label-full"><?php echo esc_html__( 'Theme Author', 'wpresidence-core' ); ?></label>
                <input type="text" class="wpresidence-2025-input" id="wl-author" name="wpresidence_white_label[author]" value="<?php echo esc_attr( $branding['author'] ); ?>">
                <p class="description"><?php echo esc_html__( 'Change the text.', 'wpresidence-core' ); ?></p>
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label for="wl-author-url" class="wpresidence-label-full"><?php echo esc_html__( 'Author URL', 'wpresidence-core' ); ?></label>
                <input type="text" class="wpresidence-2025-input" id="wl-author-url" name="wpresidence_white_label[author_url]" value="<?php echo esc_url( $branding['author_url'] ); ?>">
                <p class="description"><?php echo esc_html__( 'Change the text.', 'wpresidence-core' ); ?></p>
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label for="wl-description" class="wpresidence-label-full" ><?php echo esc_html__( 'Theme Description', 'wpresidence-core' ); ?></label>
                <textarea class="wpresidence-2025-input " rows="3" id="wl-description" name="wpresidence_white_label[description]"><?php echo esc_textarea( $branding['description'] ); ?></textarea>
                <p class="description"><?php echo esc_html__( 'Change the text.', 'wpresidence-core' ); ?></p>
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label for="wl-screenshot" class="wpresidence-label-full"><?php echo esc_html__( 'Screenshot URL', 'wpresidence-core' ); ?></label>
                <input type="text" class="wpresidence-2025-input wl-media-field" id="wl-screenshot" name="wpresidence_white_label[screenshot]" value="<?php echo esc_url( $branding['screenshot'] ); ?>">
                <button type="button" class="button wl-screenshot-upload wpresidence_button small_button "><?php echo esc_html__( 'Upload', 'wpresidence-core' ); ?></button>
                <p class="description"><?php echo esc_html__( 'Change the text.', 'wpresidence-core' ); ?></p>
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label for="wl-logo" class="wpresidence-label-full"><?php echo esc_html__( 'Branding Logo URL', 'wpresidence-core' ); ?></label>
                <input type="text" class="wpresidence-2025-input wl-media-field" id="wl-logo" name="wpresidence_white_label[branding_logo]" value="<?php echo esc_url( $branding['branding_logo'] ); ?>">
                <button type="button" class="button wl-logo-upload wpresidence_button small_button"><?php echo esc_html__( 'Upload', 'wpresidence-core' ); ?></button>
                <p class="description"><?php echo esc_html__( 'Change the text.', 'wpresidence-core' ); ?></p>
            </div>
            <div class="wpresidence-row wpresidence_check_row">
                 <input type="checkbox" class="wpresidence_checkbox" id="wpresidence_hide_themes_customizer" name="wpresidence_white_label[hide_themes_customizer]" value="1" <?php checked( $branding['hide_themes_customizer'], true ); ?>>
                 <label class="wpresidence-label-full" for="wpresidence_hide_themes_customizer" ><?php echo esc_html__( 'Hide "Themes" section from Customizer', 'wpresidence-core' ); ?></label>
            
            </div>
        </div>
        <?php submit_button( 'Save', 'primary wpresidence_button' ); ?>
    </form>




    <?php
}

