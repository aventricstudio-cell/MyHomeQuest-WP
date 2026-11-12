<div class="postbox wc-ppp-help-tab" id="wc-ppp-help-filters-tab" style="display:none;">
    <h2 class="hndle"><?php esc_attr_e( 'Available Filters', 'wc_pay_per_post' ); ?></h2>

    <div class="inside">
        <ul>
            <li><h4>wc_pay_per_post_args</h4>
                <ul>
                    <li><?php esc_attr_e( 'This filter allows you to override the WP Query arguments for the shortcodes.', 'wc_pay_per_post' ); ?></li>
                    <li><strong><?php esc_attr_e( 'Example', 'wc_pay_per_post' ); ?>:</strong>
                        <ul>
                            <li><pre>
<code>add_filter('wc_pay_per_post_args', 'my_theme_wc_ppp_args');

function my_theme_wc_ppp_args($args){
    $args['orderby'] = 'menu_order';
    return $args;
}
</code></pre>

                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
        <p><?php _e('This plugin has several different filters available for modifying various functions in the plugin. It is strongly recommended that you view all filters, attributes and descriptions by visiting <a href="https://pramadillo.com/documentation/filters-for-woocommerce-pay-per-post/" target="_blank">our filters documentation.</a></p>','wc_pay_per_post'); ?>

    </div>
</div>