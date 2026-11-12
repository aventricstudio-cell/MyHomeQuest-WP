<?php

	namespace PRAMADILLO\INTEGRATIONS;

	use WC_Subscriptions_Product;
	use Woocommerce_Pay_Per_Post_Helper;

	class WooCommerceSubscriptions {

		/** @noinspection PhpUnused */
		public function generate_subscriptions_dropdown( $subscription_products, $selected = [] ): string {
			$drop_down = '<optgroup label="Subscription Products">';
			foreach ( $subscription_products as $product ) {
				$drop_down .= '<option value="' . $product['ID'] . '"';

				if ( in_array( (string) $product['ID'], (array) $selected, true ) ) {
					$drop_down .= ' selected="selected"';
				}

				$drop_down .= '>' . $product['post_title'] . ' - [#' . $product['ID'] . ']</option>';
			}
			$drop_down .= '</optgroup>';

			return $drop_down;

		}

		/**
		 * @param $product_id
		 *
		 * @return bool
		 */
		public function is_subscription_product( $product_id ): bool {
			if ( ! class_exists( 'WC_Subscriptions_Product' ) ) {
				return false;
			}
			if ( WC_Subscriptions_Product::is_subscription( $product_id ) ) {
				return true;
			}

			return false;
		}


		public function post_contains_subscription_products( $post_id, $product_ids = null ): bool {
			if(is_null( $product_ids ) ) {
				$product_ids = (array) get_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', true );
			}

			foreach ( $product_ids as $product_id ) {
				if ( $this->is_subscription_product( $product_id ) ) {
					return true;
				}
			}

			return false;

		}

		/** @noinspection PhpUnused */
		public function filter_subscription_products( $products ): array {
			if ( ! class_exists( 'WC_Subscriptions_Product' ) ) {
				return $products;
			}

			$return = [];
			foreach ( $products as $product ) {
				if ( ! WC_Subscriptions_Product::is_subscription( $product['ID'] ) ) {
					$return[] = $product;
				}
			}

			return $return;
		}

		/**
		 * @param null $post_id
		 *
		 * @return bool
		 */
		public function is_subscriber( $post_id = null, $product_ids = null ): bool {
			if ( ! function_exists( 'wcs_user_has_subscription' ) ) {
				return false;
			}

			Woocommerce_Pay_Per_Post_Helper::logger( 'Looking to see if user has any active subscriptions' );

			if ( is_null( $post_id ) ) {
				$post_id = get_the_ID();
			}

			if(is_null($product_ids)){
				$product_ids  = get_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', true );
			}
			$current_user = wp_get_current_user();

			if ( ! empty( $product_ids ) ):
				foreach ( (array) $product_ids as $id ) {
					if ( wcs_user_has_subscription( $current_user->ID, $id, [ 'active', 'pending-cancel' ] ) ) {
						Woocommerce_Pay_Per_Post_Helper::logger( 'IS AN ACTIVE SUBSCRIBER OF  - ' . trim( $id ) );

						return true;
					}
					Woocommerce_Pay_Per_Post_Helper::logger( 'Is NOT a subscriber of  - ' . trim( $id ) );

				}
			endif;

			Woocommerce_Pay_Per_Post_Helper::logger( 'Has no active subscriptions' );

			return false;
		}


	}