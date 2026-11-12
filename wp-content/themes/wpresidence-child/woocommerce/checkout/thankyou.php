<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order">
    <?php

    if ( isset( $_SESSION['draft_property_id'] ) ) {
        $property_id = $_SESSION['draft_property_id'];
        $property    = get_post( $property_id );

        if ( $property && $property->post_status == 'draft' ) {
            ?>
            <div>
                <div style="gap:10px">
                    <!-- Hidden "Publish Property" Form -->
                    <!-- <form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" id="publish_form" style="display: none;">
                        <input type="hidden" name="publish_property" value="<?php echo esc_attr( $property_id ); ?>" />
                        <input type="submit" id="publish_button" />
                    </form> -->

                    <!-- Visible "Return to Dashboard" Button -->
                    <!-- <a href="<?php echo esc_url( home_url('/dashboard') ); ?>" class="button button-primary" id="return_dashboard">
                        Got to Dashboard
                    </a> -->
                </div>
            </div>

            <!-- <script>
            document.getElementById("return_dashboard").addEventListener("click", function(event) {
                event.preventDefault(); // Stop default link behavior
                document.getElementById("publish_button").click(); // Auto-click hidden button
            });
            </script> -->

            <?php
        }
    }

    if ( $order ) :
        do_action( 'woocommerce_before_thankyou', $order->get_id() );
        ?>

        <?php if ( $order->has_status( 'failed' ) ) : ?>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
                <?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?>
            </p>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay">
                    <?php esc_html_e( 'Pay', 'woocommerce' ); ?>
                </a>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay">
                        <?php esc_html_e( 'My account', 'woocommerce' ); ?>
                    </a>
                <?php endif; ?>
            </p>

        <?php else : ?>

            <?php wc_get_template( 'checkout/order-received.php', array( 'order' => $order ) ); ?>

            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
                <li class="woocommerce-order-overview__order order">
                    <?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
                    <strong><?php echo $order->get_order_number(); ?></strong>
                </li>

                <li class="woocommerce-order-overview__date date">
                    <?php esc_html_e( 'Date:', 'woocommerce' ); ?>
                    <strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
                </li>

                <?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
                    <li class="woocommerce-order-overview__email email">
                        <?php esc_html_e( 'Email:', 'woocommerce' ); ?>
                        <strong><?php echo $order->get_billing_email(); ?></strong>
                    </li>
                <?php endif; ?>

                <li class="woocommerce-order-overview__total total">
                    <?php esc_html_e( 'Total:', 'woocommerce' ); ?>
                    <strong><?php echo $order->get_formatted_order_total(); ?></strong>
                </li>

                <?php if ( $order->get_payment_method_title() ) : ?>
                    <li class="woocommerce-order-overview__payment-method method">
                        <?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
                        <strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
                    </li>
                <?php endif; ?>

            </ul>

        <?php endif; ?>

        <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
        <?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

    <?php else : ?>
        <?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>
    <?php endif; ?>

</div>
