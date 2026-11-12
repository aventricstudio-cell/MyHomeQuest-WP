<?php

namespace PRAMADILLO\INTEGRATIONS;

use Woocommerce_Pay_Per_Post_Helper;
class WooCommerce {
    private $available_templates;

    public function __construct() {
        $this->available_templates = [
            'woocommerce-order-receipt' => 'woocommerce-order-receipt.php',
        ];
    }

    /**
     * @param $formatted_meta
     * @param $item
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function get_purchased_content_by_product_id( $formatted_meta, $item ) : string {
        Woocommerce_Pay_Per_Post_Helper::logger( 'WooCommerce/get_purchased_content_by_product_id() called' );
        if ( $item->get_variation_id() > 0 ) {
            $purchased = Woocommerce_Pay_Per_Post_Helper::get_posts_associated_with_product_id( $item->get_variation_id() );
        } else {
            $purchased = Woocommerce_Pay_Per_Post_Helper::get_posts_associated_with_product_id( $item->get_product_id() );
        }
        $template = 'woocommerce-order-receipt';
        if ( !empty( $purchased ) ) {
            ob_start();
            require Woocommerce_Pay_Per_Post_Helper::locate_template( $this->available_templates[$template], '', WC_PPP_PATH . 'public/partials/' );
            return ob_get_clean();
        }
        return $formatted_meta;
    }

    public static function hide_item_meta_from_email( $css ) : string {
        return $css . '.ppp-purchased-content {display:none;}';
    }

}
