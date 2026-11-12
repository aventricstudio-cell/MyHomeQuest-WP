<?php

class Woocommerce_Pay_Per_Post_Tools {

	public static function reset_data(){
		global $wpdb;
		$delete_post_meta = $wpdb->query( "DELETE FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` LIKE 'wc_pay_per_post_%'" );
		$delete_transients = self::delete_transients();
		$delete_page_views = $wpdb->query( "DELETE FROM `{$wpdb->prefix}woocommerce_pay_per_post_pageviews`" );
	}


	public static function reset_page_views(){
		global $wpdb;
		$delete_page_views = $wpdb->query( "DELETE FROM `{$wpdb->prefix}woocommerce_pay_per_post_pageviews`" );
	}

	public static function get_transients(): array {
		global $wpdb;

		return (array) $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '_transient_wc_pay_per_post_%'" );
	}

	public static function get_transient_timeout( $transient ) {
		global $wpdb;

		// Use prepared statement to prevent SQL injection
		$transient_timeout = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_value FROM $wpdb->options WHERE option_name LIKE %s",
				'%_transient_timeout_' . $wpdb->esc_like( $transient ) . '%'
			)
		);

		return $transient_timeout[0];
	}


	public static function delete_transients() {
		global $wpdb;

		return $wpdb->query( "DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '_transient_wc_pay_per_post_%'" );

	}

	public static function delete_log(){
		$log = new Woocommerce_Pay_Per_Post_Logger();
		$log->delete_log_file();
	}


}