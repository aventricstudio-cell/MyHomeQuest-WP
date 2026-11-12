<?php

/** @noinspection PhpUndefinedVariableInspection */
//Sort by last purchase date
//usort($purchased, fn($a, $b) => strcmp($a->last_purchase_date, $b->last_purchase_date));
?>
<div class="wc-ppp-purchased-container">
	<?php 
if ( count( $purchased ) > 0 ) {
    ?>
        <ul id="what-ever-you-want">
			<?php 
    foreach ( $purchased as $post ) {
        ?>
                <li>
                    <a href="<?php 
        echo esc_url( get_permalink( $post->ID ) );
        ?>"><?php 
        echo esc_html( $post->post_title );
        ?></a>
                    <?php 
        //echo $post->last_purchase_date;
        ?>
                </li>
			<?php 
    }
    ?>
        </ul>
	<?php 
} else {
    ?>
        <p><?php 
    /** @noinspection PhpVoidFunctionResultUsedInspection */
    apply_filters( 'wc_pay_per_post_shortcode_purchased_no_posts', _e( 'You have not purchased any protected posts.', 'wc_pay_per_post' ) );
    ?></p>
	<?php 
}
?>
</div>