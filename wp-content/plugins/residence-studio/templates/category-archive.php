<?php
/**
 * Minimal wrapper template used when a Design Studio category template is
 * available.
 *
 * The `template_include` filter requires a PHP file path to load. This file is
 * therefore returned so we can output the selected Elementor template between
 * the theme header and footer without loading the theme's default taxonomy
 * layout.
 */
get_header();
wpestate_render_current_category_template();
get_footer();
