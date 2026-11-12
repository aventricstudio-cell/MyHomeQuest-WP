<?php  /** @noinspection PhpUndefinedVariableInspection */ ?>
<div class="wrap about-wrap debug wc-ppp-debug full-width-layout">
    <h1><?php use Pramadillo\PayForPost\Carbon\Carbon;

	    _e( 'Pay For Post with WooCommerce Debug' ); ?></h1>
    <div class="pramadillo-badge"><img alt="Logo" src="<?php echo plugin_dir_url( __DIR__ ) . 'img/icon.png'; ?>"/></div>
    <p class="about-text"><?php _e( 'This page can show you every detail about the configuration of your WordPress website. This information is incredibly helpful when debugging any issues related to the Pay For Post with WooCommerce plugin.' ); ?></p>

    <div class="wc-ppp-settings-wrap">

        <div>

            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">

                    <div class="postbox">
                        <h2 class="hndle"><?php esc_attr_e( 'Common Issues', 'wc_pay_per_post' ); ?></h2>

                        <div class="inside">
                            <h4><?php _e( 'Enabling Guest Checkout' ); ?></h4>
                            <p><?php _e( 'You need to make sure that you do <strong>NOT</strong> have guest checkout enabled on your WooCommerce store.  User accounts are necessary to keep track of who purchased what and when. <br><br>If you are not sure, check your WooCommerce settings <a href="/wp-admin/admin.php?page=wc-settings&tab=account">here</a>' ); ?></p>
                            <h4><?php _e( 'Content Still Displaying to Public' ); ?></h4>
                            <p><?php _e( 'Out of the box this plugin works with the standard POSTS and PAGES using the_content filter from Wordpress.  Your theme may not be utilizing the_content filter or may be a different custom post type.  No worries though, you can still utilize this plugin, you just need to look at the <a href="https://pramadillo.com/plugins/woocommerce-pay-per-post/#template-functions" target="_blank">Template Functions</a>.' ); ?></p>

                        </div>

                    </div>

	                <?php if ( $enable_debugging ): ?>
                    <br><br>

                        <div class="postbox">
                            <h2 class="hndle"><?php esc_attr_e( 'Debug Log', 'wc_pay_per_post' ); ?></h2>

                            <div class="inside">
                                <label><strong><?php esc_attr_e( 'Log File Location', 'wc_pay_per_post' ); ?></strong>: <a href="<?php echo Woocommerce_Pay_Per_Post_Helper::logger_url(); ?>" target="_blank"><?php echo Woocommerce_Pay_Per_Post_Helper::logger_uri(); ?></a> | <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=' . WC_PPP_SLUG . '-settings' ), 'delete_log', 'delete_log_nonce' ); ?>" title="Delete Log"><span class="dashicons dashicons-dismiss error-message"></span></a>
                                <?php if(file_exists(Woocommerce_Pay_Per_Post_Helper::logger_uri())): ?>
                                <textarea style="width:100%; height:500px;"><?php include(Woocommerce_Pay_Per_Post_Helper::logger_uri()); ?></textarea>
                                <?php endif; ?>
                                </label>
                            </div>

                        </div>
	                <?php endif; ?>
                    <br><br>

                    <div class="postbox">
                        <h2 class="hndle"><?php esc_attr_e( 'Transients', 'wc_pay_per_post' ); ?></h2>

                        <div class="inside">
                            <?php _e('<strong>The default setting for when transients expire is 1 hour.</strong><br>  You can change that buy using the filter <code>wc_pay_per_post_posts_transient_time</code>  You need to specify how many SECONDS before the transient expires. <br><br> Example:<br> <code>add_filter(&#x27;wc_pay_per_post_posts_transient_time&#x27;, function(){ return 10800; });</code>', 'wc_pay_per_post'); ?>
                            <form id="wc-ppp-create-table" action="" method="post" style="margin:20px 0 ;">
                                <?php wp_nonce_field("wc_ppp_delete_transients", "wc_ppp_delete_transients_nonce"); ?>
                                <input type="submit" name="wc-ppp-delete-btn" class="wc-ppp-delete-transients-btn button action" value="Clear/Refresh Transients">
                            </form>
                            <table class="wp-list-table widefat fixed striped table-view-list">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Expires</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($transients as $transient):
                                        ?>
                                    <tr>
                                        <td><?= $transient->option_id; ?></td>
                                        <td><?= $transient->option_name; ?></td>
                                        <td><?= wp_trim_words($transient->option_value); ?></td>
                                        <td><?= Carbon::createFromTimestamp(Woocommerce_Pay_Per_Post_Debug::get_transient_timeout(str_replace('_transient_', '', $transient->option_name)))->diffForHumans();?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
				<?php require_once plugin_dir_path(__FILE__ ).'settings-sidebar.php'; ?>
            </div>

        </div>
    </div>
</div>