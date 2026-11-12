<?php  /** @noinspection PhpUndefinedVariableInspection */ ?>

<div class="wrap about-wrap full-width-layout">
    <h1><?php esc_html_e( 'Pay For Post with WooCommerce', 'wc_pay_per_post' ); ?> (<a href="#changelog"><?php echo WC_PPP_VERSION; ?></a>)</h1>
    <div class="pramadillo-badge"><img alt="Logo" src="<?php echo plugin_dir_url( __DIR__ ) . 'img/icon.png'; ?>"/></div>

    <div class="about-text">
		<?php _e( 'If you have any questions or want to suggest a feature request please reach out to me at <a href="https://pramadillo.com" target="_blank">pramadillo.com</a>.  If you really dig this plugin consider leaving me a review!', 'wc_pay_per_post' ); ?>
    </div>

	<?php if ( wcppp_freemius()->is_not_paying() && ! wcppp_freemius()->is_trial() ) : ?>
        <a href="<?php echo wcppp_freemius()->get_upgrade_url(); ?>"><img alt="Upgrade" src="<?php echo plugin_dir_url( __DIR__ ) . 'img/upgrade.png'; ?>" style="position:fixed; right:0; bottom:0; margin-right:20px; margin-top:20px; max-width:250px;"/></a>
	<?php endif; ?>

    <nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
        <a href="#" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
        <!--suppress HtmlUnknownTarget -->
        <a href="admin.php?page=wc_pay_per_post-getting-started" class="nav-tab"><?php _e( 'Getting Started' ); ?></a>
        <a href="#changelog" class="nav-tab"><?php _e( 'Changelog' ); ?></a>
        <!--suppress HtmlUnknownTarget -->
        <a href="admin.php?page=wc_pay_per_post-help" class="nav-tab"><?php _e( 'Help' ); ?></a>
    </nav>

    <div class="changelog point-releases">
        <h3><?php _e( 'Latest release changelog' ); ?></h3>
        <p>
            <strong><?php _e( 'Version', 'wc_pay_per_post' ); ?> <?php echo WC_PPP_VERSION; ?> </strong>
			<?php echo str_replace( '*', '<br>', $last_change ); ?>
            <br><br>
            <strong><?php _e( 'For more information, see <a href="#changelog">the changelog</a>.', 'wc_pay_per_post' ); ?></strong>
        </p>

    </div>


	<?php if ( wcppp_freemius()->is_not_paying() && ! wcppp_freemius()->is_trial() ) : ?>

    <div class="headline-feature">
        <h2><?php _e( 'Upgrade today for these awesome Premium features!', 'wc_pay_per_post' ); ?></h2>
        <ul>
            <li><?php _e( 'Ability to override Restricted Content Message on a per page basis', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'Delay Restriction – This allows you to delay the paywall from appearing for a set amount of time.', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'Page View Restriction – This allows you to limit the number of page views the user who purchased this product has before the paywall reappears. Options to specify over a set amount of time or forever.', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'Expiry Restriction – This allows you to specify an expiration on this post which would require the user to repurchase the product associated with this post.', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'Custom WooCommerce tab on the My Account page to list out all purchased content.', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'Listing of purchased content on order receipt and order confirmation page.', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'PolyLang Multiple Language support.', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'Priority Support.', 'wc_pay_per_post' ); ?></li>
            <li><?php _e( 'And more!', 'wc_pay_per_post' ); ?></li>
        </ul>
    </div>

    <hr>
    <?php endif; ?>
    <div class="wc-ppp-changelog" id="changelog">
        <h2><?php esc_html_e( 'Changelog', 'wc_pay_per_post' ); ?></h2>

		<?php
		echo '<pre>';
		print_r( $full_change_log );
		echo '</pre>';
		?>

    </div>
</div>
