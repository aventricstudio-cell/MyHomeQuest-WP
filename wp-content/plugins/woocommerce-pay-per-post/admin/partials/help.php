<div class="wrap about-wrap full-width-layout">
<h1><?php _e('Pay For Post with WooCommerce Documentation' ); ?></h1>
    <p class="about-text">		<?php _e( 'For more in-depth documentation, please visit <a href="https://pramadillo.com/documentation/category/woocommerce-pay-per-post/" target="_blank">pramadillo.com</a> <br><br>If you have any questions or want to suggest a feature request please reach out to me at <a href="https://pramadillo.com/support/" target="_blank">here</a>.  If you really dig this plugin consider leaving me a review!', 'wc_pay_per_post' ); ?></p>
    <div class="pramadillo-badge"><img alt="Logo" src="<?php echo plugin_dir_url( __DIR__ ) . 'img/icon.png'; ?>"/></div>

    <div class="wc-ppp-settings-wrap">
        <h2 class="nav-tab-wrapper" id="wc-ppp-help-nav-tabs">
            <!--suppress HtmlUnknownAnchorTarget -->
            <a class="nav-tab nav-tab-active" href="#wc-ppp-help-getting-started-tab">Getting Started</a>
            <!--suppress HtmlUnknownAnchorTarget -->
            <a class="nav-tab" href="#wc-ppp-help-shortcode-tab">Shortcodes</a>
            <!--suppress HtmlUnknownAnchorTarget -->
            <a class="nav-tab" href="#wc-ppp-help-shortcode-templates-tab">Shortcode Templates</a>
            <!--suppress HtmlUnknownAnchorTarget -->
            <a class="nav-tab" href="#wc-ppp-help-template-tags-tab">Template Tags</a>
            <!--suppress HtmlUnknownAnchorTarget -->
            <a class="nav-tab" href="#wc-ppp-help-filters-tab">Filters</a>
            <!--suppress HtmlUnknownAnchorTarget -->
            <a class="nav-tab" href="#wc-ppp-help-hooks-tab">Hooks</a>
        </h2>
        <div id="poststuff">

            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
					<?php require_once plugin_dir_path(__FILE__ ).'help-getting-started.php'; ?>
					<?php require_once plugin_dir_path(__FILE__ ).'help-shortcodes.php'; ?>
					<?php require_once plugin_dir_path(__FILE__ ).'help-shortcodes-templates.php'; ?>
					<?php require_once plugin_dir_path(__FILE__ ).'help-template-tags.php'; ?>
					<?php require_once plugin_dir_path(__FILE__ ).'help-filters.php'; ?>
					<?php require_once plugin_dir_path(__FILE__ ).'help-hooks.php'; ?>
                </div>
				<?php require_once plugin_dir_path(__FILE__ ).'settings-sidebar.php'; ?>
            </div>

        </div>
    </div>
</div>