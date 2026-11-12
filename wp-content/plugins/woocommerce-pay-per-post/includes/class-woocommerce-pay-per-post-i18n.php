<?php

	class Woocommerce_Pay_Per_Post_i18n {

		/** @noinspection PhpUnused */
		public function load_plugin_textdomain() {

			load_plugin_textdomain(
				'wc_pay_per_post',
				false,
				dirname( plugin_basename( __FILE__ ), 2 ) . '/languages/'
			);

		}

	}
