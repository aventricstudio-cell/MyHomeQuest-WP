<?php /** @noinspection PhpUnused */

/**
	 * Class Woocommerce_PayPerPost
	 * This is for all deprecated functions
	 */
	class Woocommerce_PayPerPost {

		/**
		 * @return bool
		 */
		public static function has_access(): bool {
			_deprecated_function( __FUNCTION__, '2.0.0', 'Woocommerce_Pay_Per_Post_Helper::has_access()' );

			return Woocommerce_Pay_Per_Post_Helper::has_access( null, false );
		}

		/**
		 * @return string
		 * @deprecated 2.0.0 Use Woocommerce_Pay_Per_Post_Helper::get_no_access_content()
		 */
		public static function get_no_access_content(): string {
			_deprecated_function( __FUNCTION__, '2.0.0', 'Woocommerce_Pay_Per_Post_Helper::get_no_access_content()' );

			return Woocommerce_Pay_Per_Post_Helper::get_no_access_content();
		}

	}
