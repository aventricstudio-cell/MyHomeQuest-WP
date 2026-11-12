<div class="wrap about-wrap full-width-layout">
    <h1><?php esc_html_e( 'Getting Started with Pay For Post with WooCommerce', 'wc_pay_per_post' ); ?></h1>
    <div class="pramadillo-badge"><img alt="Logo" src="<?php echo plugin_dir_url( __DIR__ ) . 'img/icon.png'; ?>"/></div>

    <div class="about-text">
		<?php _e( 'The whole purpose of this plugin was to make it super simple to put a page/post/cpt behind a pay wall. You can protect your content in 3 simple steps.  If you are having issues after following the steps below, please feel free to reach out!', 'wc_pay_per_post' ); ?>
    </div>

	<?php if ( wcppp_freemius()->is_not_paying() && ! wcppp_freemius()->is_trial() ) : ?>
        <a href="<?php echo wcppp_freemius()->get_upgrade_url(); ?>"><img alt="Upgrade" src="<?php echo plugin_dir_url( __DIR__ ) . 'img/upgrade.png'; ?>" style="position:fixed; right:0; bottom:0; margin-right:20px; margin-top:20px; max-width:250px;"/></a>
	<?php endif; ?>

    <div class="headline-feature">
        <h2><?php _e( 'Protecting a Post' ); ?></h2>
        <p class="lead-description"><?php _e( 'Getting started is super simple.  All you need to do is associate a product with a page or a post and that\'s it!' ); ?></p>
        <div class="inline-svg aligncenter">
            <img alt="Select Product" src="<?php echo plugin_dir_url( __DIR__ ) . 'img/videos/select-product.gif'; ?>" width="100%">
        </div>
    </div>

    <div class="feature-section is-wide has-2-columns is-wider-left">
        <div class="column is-vertically-aligned-center">
            <h3><?php _e( 'Step 1: Create a Product' ); ?></h3>
            <p>
	            <?php _e( 'The first step of the process is to create a WooCommerce product which you would like your users to purchase before being able to access your content.' ); ?>
            </p>
            <a href="<?php echo get_admin_url(); ?>post-new.php?post_type=product" class="button button-small button-primary">Create a Product</a>

        </div>
        <div class="column">
            <div class="inline-svg aligncenter">
                <!--suppress HtmlUnknownTarget -->
                <img src="<?php echo WC_PPP_URL; ?>/admin/img/woocommerce_logo.svg" alt="WooCommerce Logo" >
            </div>
        </div>
    </div>
    <hr />

    <div class="feature-section is-wide has-2-columns is-wider-right">
        <div class="column">
            <div class="inline-svg aligncenter">
                <!--suppress HtmlUnknownTarget -->
                <img src="<?php echo WC_PPP_URL; ?>/admin/img/wordpress-logo.svg" alt="Wordpress Logo" style="width:120px; height:120px;">
            </div>
        </div>
        <div class="column is-vertically-aligned-center">
            <h3><?php _e( 'Step 2: Create a Page/Post' ); ?></h3>
            <p><?php _e( 'Create a page or a post in Wordpress like you normally would do.  All of the content you have in the editor will be hidden from users.' ); ?></p>
            <a href="<?php echo get_admin_url(); ?>post-new.php?post_type=post" class="button button-small button-primary">Create a Post</a>

        </div>
    </div>
    <hr />

    <div class="feature-section is-wide has-2-columns is-wider-left">
        <div class="column is-vertically-aligned-center">
            <h3><?php _e( 'Step 3: Associate your Product with Page/Post' ); ?></h3>
            <p>
				<?php _e( 'In your page or post edit screen, you will see a Pay For Post with WooCommerce section which will allow you to search for your product that you want to have users purchase to view the content of the page.' ); ?>
            </p>
        </div>
        <div class="column">
            <div class="inline-svg aligncenter">
                <!--suppress HtmlUnknownTarget -->
                <img src="<?php echo WC_PPP_URL; ?>/admin/img/icon.png" alt="" style="width:120px; height:120px;">
            </div>
        </div>
    </div>
    <hr />


    <h3 class="aligncenter"><?php _e( 'Video Library' ); ?></h3>
    <p class="aligncenter"><?php _e( 'New videos will be added shortly with how-to videos on various features of the plugin.' ); ?></p>
    <div class="aligncenter">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/ZepEicA3yeA" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <br><br>
    <iframe width="560" height="315" src="https://www.youtube.com/embed/UEjs8JCknFU" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <hr />
    <h3 class="aligncenter"><?php _e( 'Common Issues' ); ?></h3>

    <div class="has-2-columns">
        <div class="column aligncenter">
            <h4><?php _e( 'Enabling Guest Checkout' ); ?></h4>
            <p><?php _e( 'You need to make sure that you do <strong>NOT</strong> have guest checkout enabled on your WooCommerce store.  User accounts are necessary to keep track of who purchased what and when. <br><br>If you are not sure, check your WooCommerce settings <a href="/wp-admin/admin.php?page=wc-settings&tab=account">here</a>' ); ?></p>
        </div>
        <div class="column aligncenter">
            <h4><?php _e( 'Content Still Displaying to Public' ); ?></h4>
            <p><?php _e( 'Out of the box this plugin works with the standard POSTS and PAGES using the_content filter from Wordpress.  Your theme may not be utilizing the_content filter or may be a different custom post type.  No worries though, you can still utilize this plugin, you just need to look at the <a href="https://pramadillo.com/plugins/woocommerce-pay-per-post/#template-functions" target="_blank">Template Functions</a>.' ); ?></p>
        </div>
    </div>


</div>