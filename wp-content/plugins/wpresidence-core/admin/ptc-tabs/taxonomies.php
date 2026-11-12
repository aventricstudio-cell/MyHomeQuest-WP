<?php
/**
 * Taxonomies tab renderer.
 *
 * Displays toggle controls for each taxonomy grouped by
 * post type.
 *
 * @package WpResidence Core
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the Taxonomies tab content.
 *
 * @param array $taxonomies Taxonomies organized by post type.
 * @param array $post_types Post type labels.
 * @param array $options    Saved options.
 */
function wpresidence_ptc_render_taxonomies_tab( $taxonomies, $post_types, $options ) {
    ?>
    <div class="wpresidence-settings">
    <?php
    $taxonomies_filtered = array_filter(
        $taxonomies,
        function ( $taxes, $ptype ) use ( $options ) {
            return ! ( isset( $options['post_types'][ $ptype ] ) && ! $options['post_types'][ $ptype ] );
        },
        ARRAY_FILTER_USE_BOTH
    );

    if ( empty( $taxonomies_filtered ) ) {
        echo '<p class="wpresidence-row">' . esc_html__( 'There are no taxonomies to display because no post types with taxonomies are enabled.', 'wpresidence-core' ) . '</p>';
        echo '</div>';
        return;
    }

    $last_ptype = array_key_last( $taxonomies_filtered );

    foreach ( $taxonomies_filtered as $ptype => $taxes ) :
        ?>
        <div class="wpresidence-heading"><strong><?php echo esc_html( $post_types[ $ptype ] ); ?></strong></div>
        <?php foreach ( $taxes as $tax_slug => $tax_label ) :
            $enabled = isset( $options['taxonomies'][ $tax_slug ] ) ? (bool) $options['taxonomies'][ $tax_slug ] : true;
        ?>
        <div class="wpresidence-row">
            <div class="wpresidence-label">
                <?php echo esc_html( $tax_label ); ?>
            </div>
            <div class="wpresidence-field">
                <div class="wpresidence-toggle-wrapper">
                    <input type="checkbox" id="taxonomy_<?php echo esc_attr( $tax_slug ); ?>" name="taxonomies[<?php echo esc_attr( $tax_slug ); ?>]" value="1" class="wpresidence-toggle-input" <?php checked( $enabled, true ); ?>>
                    <label for="taxonomy_<?php echo esc_attr( $tax_slug ); ?>" class="wpresidence-toggle-label">
                        <span class="wpresidence-toggle-slider"></span>
                    </label>
                    <span class="wpresidence-toggle-text">
                        <?php echo $enabled ? esc_html__( 'Enabled', 'wpresidence-core' ) : esc_html__( 'Disabled', 'wpresidence-core' ); ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if ( $ptype !== $last_ptype ) : ?>
        <hr>
    <?php endif; ?>

<?php endforeach; ?>

    </div>
    <?php
}
