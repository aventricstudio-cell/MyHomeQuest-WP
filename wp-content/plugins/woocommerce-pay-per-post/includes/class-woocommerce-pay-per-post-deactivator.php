<?php

class Woocommerce_Pay_Per_Post_Deactivator {

	/**
	 * Plugin deactivation hook.
	 *
	 * Note: Cleanup should NOT happen here - only on uninstall via Freemius.
	 * This method is intentionally empty to preserve data when temporarily deactivating.
	 *
	 * @since 3.2.0
	 */
	public static function deactivate() {
		// Intentionally empty - cleanup happens in uninstall() only
		// This prevents accidental data loss when temporarily deactivating the plugin
	}

	/**
	 * Plugin uninstall - deletes all plugin data if user opted in.
	 *
	 * This is called via Freemius 'after_uninstall' hook.
	 * WordPress doesn't support multiple uninstall methods, so Freemius SDK uses its own.
	 *
	 * @since 3.2.0
	 */
	public static function uninstall() {
		// Check to see if option is set to remove all settings.
		$delete_settings = get_option( WC_PPP_SLUG . '_delete_settings', false );

		if ( $delete_settings ) {
			Woocommerce_Pay_Per_Post_Helper::logger( 'Delete all settings - UNINSTALL initiated' );
			self::delete_all_settings();
		}
	}

	/**
	 * Deletes all plugin data from the database.
	 *
	 * Fixed in v3.2.0 to use prepared statements instead of RLIKE (SQL injection fix).
	 *
	 * @since 3.2.0
	 */
	private static function delete_all_settings() {
		global $wpdb;

		// Delete custom pageviews table
		$sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}woocommerce_pay_per_post_pageviews`";
		$wpdb->query( $sql );
		Woocommerce_Pay_Per_Post_Helper::logger( 'woocommerce_pay_per_post_pageviews table DELETED' );

		// Delete all plugin options (use LIKE with proper escaping instead of RLIKE)
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE %s",
			$wpdb->esc_like( WC_PPP_SLUG . '_' ) . '%'
		) );
		Woocommerce_Pay_Per_Post_Helper::logger( 'Plugin options DELETED' );

		// Delete all plugin post meta (use LIKE with proper escaping instead of RLIKE)
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` LIKE %s",
			$wpdb->esc_like( WC_PPP_SLUG . '_' ) . '%'
		) );
		Woocommerce_Pay_Per_Post_Helper::logger( 'Plugin post meta DELETED' );

		// Delete all plugin transients
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE %s OR `option_name` LIKE %s",
			'_transient_' . $wpdb->esc_like( WC_PPP_SLUG ) . '%',
			'_transient_timeout_' . $wpdb->esc_like( WC_PPP_SLUG ) . '%'
		) );
		Woocommerce_Pay_Per_Post_Helper::logger( 'Plugin transients DELETED' );

		// Delete log file
		$log = new Woocommerce_Pay_Per_Post_Logger();
		$log->delete_log_file();
		Woocommerce_Pay_Per_Post_Helper::logger( 'Log file DELETED' );
	}

}