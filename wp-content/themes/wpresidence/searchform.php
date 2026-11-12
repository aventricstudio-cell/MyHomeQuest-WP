<?php
/**MILLDONE
 * Search Form Template
 * src: searchform.php
 * This template displays the search form used throughout the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */
?>

<form method="get" id="searchform" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="s" class="screen-reader-text"><?php esc_html_e('Search for:', 'wpresidence'); ?></label>
    <input type="text" 
           class="form-control" 
           name="s" 
           id="s" 
           placeholder="<?php esc_attr_e('Type Keyword', 'wpresidence'); ?>" 
           value="<?php echo get_search_query(); ?>" 
    />
    <button type="submit" class="wpresidence_button" id="submit-form">
        <?php esc_html_e('Search', 'wpresidence'); ?>
    </button>
    <?php wp_nonce_field('wpestate_default_search', 'wpestate_default_search_nonce'); ?>
</form>