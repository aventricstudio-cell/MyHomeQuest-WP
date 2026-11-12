<?php

class Woocommerce_Pay_Per_Post_Debug {
    /**
     * Returns a table which contains all the checks, their statuses and actionable items
     * @return string
     */
    public static function output_debug_stats() : string {
        $check_results = [];
        foreach ( self::checks() as $check ) {
            $check_results[$check] = self::$check();
        }
        $output = '<table class="table">';
        foreach ( $check_results as $function => $value ) {
            $output_function = $function . '_output';
            $output .= self::$output_function( $value );
        }
        $output .= '</table>';
        return $output;
    }

    /**
     * We loop through this array in the output_debug_stats function and run the function with the same array key name
     * @return array
     */
    private static function checks() : array {
        $checks = ['ppp_version', 'ppp_post_count', 'wc_product_count'];
        return $checks;
    }

    //Check Functions that correlate to the keys in the checks function
    /** @noinspection PhpUnused */
    protected static function page_view_table() : bool {
        return self::does_page_view_table_exist();
    }

    /** @noinspection PhpUnused */
    protected static function ppp_version() : string {
        return WC_PPP_VERSION;
    }

    /** @noinspection PhpUnused */
    protected static function ppp_post_count() : array {
        return self::get_number_of_ppp_posts();
    }

    /** @noinspection PhpUnused */
    protected static function wc_product_count() : array {
        return self::get_number_of_wc_products();
    }

    //Output Function that correlate to the keys in the checks function
    /** @noinspection PhpUnused */
    protected static function ppp_post_count_output( $data ) : string {
        $status = false;
        $post_count = 0;
        $output = '<tr>';
        $output .= '<td>Number of Protected Posts</td>';
        foreach ( $data as $key => $value ) {
            $status = $key;
            $post_count = $value;
        }
        if ( $status ) {
            $output .= '<td class="success">' . $post_count . '</td>';
        } else {
            $output .= '<td class="error">ZERO</td>';
        }
        $output .= '</tr>';
        return $output;
    }

    /** @noinspection PhpUnused */
    protected static function ppp_version_output( $data ) : string {
        $output = '<tr>';
        $output .= '<td>Version</td>';
        $output .= '<td>' . $data . '</td>';
        $output .= '</tr>';
        return $output;
    }

    /** @noinspection PhpUnused */
    protected static function wc_product_count_output( $data ) : string {
        $output = '<tr>';
        $output .= '<td>Number of Products</td>';
        if ( !isset( $data[0] ) ) {
            $output .= '<td class="success">' . $data[1] . '</td>';
        } else {
            $output .= '<td class="error">' . $data[1] . '</td>';
        }
        $output .= '</tr>';
        return $output;
    }

    /** @noinspection PhpUnused */
    protected static function page_view_table_output( $status ) : string {
        $output = '<tr>';
        $output .= '<td>Page View Database Present?</td>';
        if ( $status ) {
            $output .= '<td class="success">YES</td>';
        } else {
            $output .= '<td class="error">NO!</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td colspan="2">
							<form id="wc-ppp-create-table" action="" method="post">
							' . wp_nonce_field( "wc_ppp_create_table", "wc_ppp_create_table_nonce" ) . '
			                <input type="submit" name="wc-ppp-upgrade-btn" class="wc-ppp-create-table-btn button action" value="Create Table">
            				</form>
						<br></td>';
        }
        $output .= '</tr>';
        return $output;
    }

    //Actual logic functions
    private static function get_number_of_ppp_posts() : array {
        $post_count = count( Woocommerce_Pay_Per_Post_Helper::get_protected_posts() );
        if ( $post_count > 0 ) {
            return [
                true => $post_count,
            ];
        }
        return [
            false => 0,
        ];
    }

    private static function get_number_of_wc_products() : array {
        $args = [
            'nopaging'    => true,
            'post_status' => 'publish',
            'post_type'   => 'product',
        ];
        $posts = count( get_posts( $args ) );
        if ( $posts > 0 ) {
            return [
                true => $posts,
            ];
        }
        return [
            false => 0,
        ];
    }

    private static function does_page_view_table_exist() : bool {
        global $wpdb;
        $exists = (array) $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_pay_per_post_pageviews'", ARRAY_A );
        if ( count( $exists ) > 0 ) {
            return true;
        }
        return false;
    }

    public static function get_transients() : array {
        global $wpdb;
        return (array) $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '_transient_wc_pay_per_post_%'" );
    }

    public static function get_transient_timeout( $transient ) {
        global $wpdb;
        // Use prepared statement to prevent SQL injection
        $transient_timeout = $wpdb->get_col( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE %s", '%_transient_timeout_' . $wpdb->esc_like( $transient ) . '%' ) );
        return $transient_timeout[0];
    }

    public static function delete_transients() {
        global $wpdb;
        return $wpdb->query( "DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '_transient_wc_pay_per_post_%'" );
    }

}
