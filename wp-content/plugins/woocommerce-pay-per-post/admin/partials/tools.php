<div class="wrap about-wrap full-width-layout">
<h1><?php 
_e( 'Pay For Post with WooCommerce Tools' );
?></h1>
    <div class="wc-ppp-settings-wrap">
        <div id="poststuff">

            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">

                    <div class="postbox">
                        <h2 class="hndle"><?php 
esc_attr_e( 'Delete Debug Log', 'wc_pay_per_post' );
?></h2>
                        <div class="inside">
                            <p><?php 
_e( 'If you were debugging the log file could be very large.  You can delete it from here. ' );
?></p>
                            <form id="wc-ppp-create-table" action="" method="post" style="margin:20px 0 ;">
				                <?php 
wp_nonce_field( "wc_ppp_delete_log", "wc_ppp_delete_log_nonce" );
?>
                                <input type="submit" name="wc-ppp-delete-log-btn" class="wc-ppp-delete-log-btn button action" value="Delete Debug Log">
                            </form>
                        </div>
                    </div>

                    <div class="postbox">
                        <h2 class="hndle"><?php 
esc_attr_e( 'Clear Transients', 'wc_pay_per_post' );
?></h2>
                        <div class="inside">
                            <p><?php 
_e( 'Transients are used to store data for a temporary period.  Sometimes you may want to clear them manually before they expire.' );
?></p>
                            <form id="wc-ppp-create-table" action="" method="post" style="margin:20px 0 ;">
		                        <?php 
wp_nonce_field( "wc_ppp_delete_transients", "wc_ppp_delete_transients_nonce" );
?>
                                <input type="submit" name="wc-ppp-delete-btn" class="wc-ppp-delete-transients-btn button action" value="Clear/Refresh Transients">
                            </form>
                        </div>
                    </div>
                <?php 
?>
                    <div class="postbox">
                        <h2 class="hndle"><?php 
esc_attr_e( 'Reset Product, Posts and User Data', 'wc_pay_per_post' );
?></h2>
                        <div class="inside">
                            <p><?php 
_e( 'If you want to remove all product associations with posts, user pageviews, and all data related to protecting content.' );
?></p>
                            <form id="wc-ppp-create-table" action="" method="post" style="margin:20px 0 ;">
				                <?php 
wp_nonce_field( "wc_ppp_reset_data", "wc_ppp_reset_data_nonce" );
?>
                                <input type="submit" name="wc-ppp-reset-btn" class="wc-ppp-reset-data-btn button action" value="Reset Data" onclick="return confirm('Are you sure you want to reset data?  This can not be undone.')">
                            </form>
                            <strong style="color:red; font-weight:bold;">This can not be undone!  Please make a backup.</strong>
                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>
</div>