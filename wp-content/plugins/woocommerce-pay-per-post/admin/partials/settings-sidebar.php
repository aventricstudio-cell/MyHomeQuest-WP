<div id="postbox-container-1" class="postbox-container">
    <?php 
if ( wcppp_freemius()->is_not_paying() && !wcppp_freemius()->is_trial() ) {
    ?>
		<?php 
    require_once plugin_dir_path( __FILE__ ) . 'upsell-sidebar.php';
    ?>
	<?php 
}
?>

	<?php 
if ( isset( $_GET['page'] ) && $_GET['page'] === 'wc_pay_per_post-debug' ) {
    ?>
	<?php 
    require_once plugin_dir_path( __FILE__ ) . 'debug-sidebar.php';
    ?>
    <?php 
}
?>

    <div class="postbox">
        <h2 class="hndle"><?php 
esc_attr_e( 'Have Questions? Request a Feature?', 'wc_pay_per_post' );
?></h2>

        <div class="inside">
            <p><?php 
_e( 'If you have any questions or want to suggest a feature request please reach out to me at <a href="https://pramadillo.com" target="_blank">pramadillo.com</a>.  If you really dig this plugin consider leaving me a review!', 'wc_pay_per_post' );
?></p>
            <ul>
                <li><a href="https://pramadillo.com/feature-requests" target="_blank"><?php 
esc_attr_e( 'Request a Feature', 'wc_pay_per_post' );
?></a></li>

                <?php 
?>

            </ul>

            <?php 
if ( isset( $_GET['page'] ) && $_GET['page'] === 'wc_pay_per_post-settings' ) {
    ?>
            <h3><?php 
    esc_attr_e( 'Additional Help', 'wc_pay_per_post' );
    ?></h3>
            <ul>
                <li><a href="<?php 
    echo get_admin_url();
    ?>admin.php?page=wc_pay_per_post-help#wc-ppp-help-getting-started-tab"><?php 
    esc_attr_e( 'Getting Started', 'wc_pay_per_post' );
    ?></a></li>
                <li><a href="<?php 
    echo get_admin_url();
    ?>admin.php?page=wc_pay_per_post-help#wc-ppp-help-shortcode-tab"><?php 
    esc_attr_e( 'Shortcodes', 'wc_pay_per_post' );
    ?></a></li>
                <li><a href="<?php 
    echo get_admin_url();
    ?>admin.php?page=wc_pay_per_post-help#wc-ppp-help-shortcode-templates-tab"><?php 
    esc_attr_e( 'Shortcode Templates', 'wc_pay_per_post' );
    ?></a></li>
                <li><a href="<?php 
    echo get_admin_url();
    ?>admin.php?page=wc_pay_per_post-help#wc-ppp-help-template-tags-tab"><?php 
    esc_attr_e( 'Template Tags', 'wc_pay_per_post' );
    ?></a></li>
                <li><a href="<?php 
    echo get_admin_url();
    ?>admin.php?page=wc_pay_per_post-help#wc-ppp-help-filters-tab"><?php 
    esc_attr_e( 'Filters', 'wc_pay_per_post' );
    ?></a></li>
                <li><a href="<?php 
    echo get_admin_url();
    ?>admin.php?page=wc_pay_per_post-help#wc-ppp-help-hooks-tab"><?php 
    esc_attr_e( 'Hooks', 'wc_pay_per_post' );
    ?></a></li>
            </ul>
            <?php 
}
?>

        </div>

    </div>


</div>