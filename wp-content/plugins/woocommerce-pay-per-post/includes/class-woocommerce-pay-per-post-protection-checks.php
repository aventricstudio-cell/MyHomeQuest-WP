<?php

class Woocommerce_Pay_Per_Post_Protection_Checks extends Woocommerce_Pay_Per_Post {
    public static function check_if_admin_call( $post_id, $product_ids ) : bool {
        $post_id_str = 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_admin_call  - IS ';
        $log_str = $post_id_str . (( is_admin() ? 'an Admin Call' : 'NOT an Admin Call' ));
        Woocommerce_Pay_Per_Post_Helper::logger( $log_str );
        return is_admin();
    }

    public static function check_if_admin_user_have_access( $post_id, $product_ids ) : bool {
        $admins_allowed_access = (bool) get_option( WC_PPP_SLUG . '_allow_admins_access_to_protected_posts', false );
        // Check and see if admins are allowed to view protected content.
        if ( $admins_allowed_access && is_super_admin() ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_admin_user_have_access  - Administrators HAVE access to all protected posts via settings' );
            return true;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_admin_user_have_access  - Administrators DO NOT HAVE access to all protected posts via settings' );
        return false;
    }

    public static function check_if_user_role_has_access( $post_id, $product_ids ) : bool {
        $allowed_user_roles = [];
        foreach ( wp_get_current_user()->roles as $role ) {
            if ( in_array( $role, $allowed_user_roles ) ) {
                return true;
            }
        }
        return false;
    }

    public static function check_if_purchased( $post_id, $product_ids ) : bool {
        $current_user = wp_get_current_user();
        foreach ( $product_ids as $id ) {
            if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() && class_exists( '\\PRAMADILLO\\INTEGRATIONS\\WooCommerceSubscriptions' ) ) {
                $subscriptions = new \PRAMADILLO\INTEGRATIONS\WooCommerceSubscriptions();
                if ( Woocommerce_Pay_Per_Post_Helper::customer_has_purchased_product( $current_user->user_email, $current_user->ID, trim( $id ) ) && !$subscriptions->is_subscription_product( $id ) ) {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_purchased  - WooSubscriptions Enabled and User has purchased product id #' . trim( $id ) . ' that is NOT a subscription product' );
                    return true;
                }
            } else {
                if ( Woocommerce_Pay_Per_Post_Helper::customer_has_purchased_product( $current_user->user_email, $current_user->ID, trim( $id ) ) ) {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_purchased  - User has purchased product id #' . trim( $id ) );
                    return true;
                }
            }
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_purchased  - User has NOT purchased product id #' . trim( $id ) );
        return false;
    }

    public static function check_if_logged_in( $post_id, $product_ids ) : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_logged_in  - ' . (( is_user_logged_in() ? 'true' : 'false' )) );
        return is_user_logged_in();
    }

    public static function check_if_has_access() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_has_access  - Has been called' );
        //
        //            switch ($this->protection_type) {
        //                case 'standard': // Since we already check to see if they purchased the product standard protection returns true all the time.
        //                case 'delay': // Delay protection is same protection as standard, just difference in when to display pay wall, we already checked to see if they purchased product we return true.
        //                    Woocommerce_Pay_Per_Post_Helper::logger('Protection Type is Standard or Delayed');
        //
        //                    return $this->check_if_purchased();
        //                case 'page-view':
        //                    Woocommerce_Pay_Per_Post_Helper::logger('Protection Type is Page View Protection');
        //
        //                    return $this->has_access_page_view_protection__premium_only();
        //                case 'expire':
        //                    Woocommerce_Pay_Per_Post_Helper::logger('Protection Type is Expiration Protection');
        //
        //                    return $this->has_access_expiry_protection__premium_only();
        //            }
        return false;
    }

    public static function check_if_is_paid_memberships_pro_member( $post_id, $product_ids ) : bool {
        //Is user a Paid Memberships Pro Member?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_paid_membership_pro() && class_exists( '\\PRAMADILLO\\INTEGRATIONS\\PaidMembershipsPro' ) ) {
            $pmp = new \PRAMADILLO\INTEGRATIONS\PaidMembershipsPro();
            return $pmp->is_member( $post_id, $product_ids );
        }
        return false;
    }

    public static function check_if_is_member( $post_id = null, $product_ids = null ) : bool {
        //Is user a WooCommerce Memberships Member?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_memberships() && class_exists( '\\PRAMADILLO\\INTEGRATIONS\\WooCommerceMemberships' ) ) {
            $memberships = new \PRAMADILLO\INTEGRATIONS\WooCommerceMemberships();
            return $memberships->is_member( $post_id, $product_ids );
        }
        return false;
    }

    public static function check_if_is_subscriber( $post_id = null, $product_ids = null ) : bool {
        //Is user a WooCommerce Subscriptions Subscriber?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() && class_exists( '\\PRAMADILLO\\INTEGRATIONS\\WooCommerceSubscriptions' ) ) {
            $subscriptions = new \PRAMADILLO\INTEGRATIONS\WooCommerceSubscriptions();
            return $subscriptions->is_subscriber( $post_id, $product_ids );
        }
        return false;
    }

    public static function check_if_post_contains_subscription_products( $post_id, $product_ids ) : bool {
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() && class_exists( '\\PRAMADILLO\\INTEGRATIONS\\WooCommerceSubscriptions' ) ) {
            $subscriptions = new \PRAMADILLO\INTEGRATIONS\WooCommerceSubscriptions();
            return $subscriptions->post_contains_subscription_products( $post_id, $product_ids );
        }
        return false;
    }

    public static function check_if_product_is_a_subscription_product( $id ) : bool {
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() ) {
            return WC_Subscriptions_Product::is_subscription( $id );
        }
        return false;
    }

    public static function check_if_product_is_a_membership_product( $id ) : bool {
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_memberships() ) {
            return array_key_exists( (int) $id, wc_memberships_get_membership_plans() );
        }
        return false;
    }

    public static function check_if_post_contains_membership_products( $id, $product_ids ) : bool {
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_memberships() && class_exists( '\\PRAMADILLO\\INTEGRATIONS\\WooCommerceMemberships' ) ) {
            $memberships = new \PRAMADILLO\INTEGRATIONS\WooCommerceMemberships();
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_post_contains_membership_products  - Does Post Contain Membership Products? - ' . (( $memberships->post_contains_membership_products( $id ) ? 'true' : 'false' )) );
            return $memberships->post_contains_membership_products( $id, $product_ids );
        }
        return false;
    }

    public static function check_if_post_contains_paid_memberships_pro_membership_products( $post_id, $product_ids ) : bool {
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_paid_membership_pro() && class_exists( '\\PRAMADILLO\\INTEGRATIONS\\PaidMembershipsPro' ) ) {
            $pmp = new \PRAMADILLO\INTEGRATIONS\PaidMembershipsPro();
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Protection_Checks/check_if_post_contains_paid_memberships_pro_membership_products  - Does Post Contain Paid Membership Pro Membership Products? - ' . (( $pmp->post_contains_membership_products( $id ) ? 'true' : 'false' )) );
            return $pmp->post_contains_membership_products( $post_id, $product_ids );
        }
        return false;
    }

}
