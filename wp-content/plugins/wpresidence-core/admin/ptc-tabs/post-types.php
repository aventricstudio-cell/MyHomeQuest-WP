<?php
/**
 * Post Types tab renderer.
 *
 * Displays toggle controls for enabling or disabling
 * custom post types in the admin area.
 *
 * @package WpResidence Core
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the Post Types tab content.
 *
 * @param array $post_types List of post types.
 * @param array $options    Saved options.
 */
function wpresidence_ptc_render_post_types_tab( $post_types, $options ) {
    ?>
    <div class="wpresidence-settings">
        <?php foreach ( $post_types as $slug => $label ) :
            $enabled = isset( $options['post_types'][ $slug ] ) ? (bool) $options['post_types'][ $slug ] : true;
        ?>
            <div class="wpresidence-row">
                <div class="wpresidence-label">
                    <?php echo esc_html( $label ); ?>
                </div>
                <div class="wpresidence-field">
                    <div class="wpresidence-toggle-wrapper">
                        <input type="checkbox" id="post_type_<?php echo esc_attr( $slug ); ?>" name="post_types[<?php echo esc_attr( $slug ); ?>]" value="1" class="wpresidence-toggle-input" <?php checked( $enabled, true ); ?>>
                        <label for="post_type_<?php echo esc_attr( $slug ); ?>" class="wpresidence-toggle-label">
                            <span class="wpresidence-toggle-slider"></span>
                        </label>
                        <span class="wpresidence-toggle-text">
                            <?php echo $enabled ? esc_html__( 'Enabled', 'wpresidence-core' ) : esc_html__( 'Disabled', 'wpresidence-core' ); ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}
