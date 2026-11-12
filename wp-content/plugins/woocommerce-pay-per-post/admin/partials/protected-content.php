<?php
/** @noinspection PhpUndefinedVariableInspection */

// Sanitize and validate product_id if present
$product_id = null;
if ( isset( $_GET['product_id'] ) ) {
    $product_id = absint( $_GET['product_id'] );
    // Validate product_id exists in data array
    if ( ! isset( $data[ $product_id ] ) ) {
        $product_id = null;
    }
}
?>

<div class="wrap about-wrap full-width-layout">
    <h1><?php _e( 'Pay For Post with WooCommerce Protected Content' ); ?></h1>
    <p class="about-text"><?php _e( 'If you have any suggestions on what you would like to see here please reach out!', 'wc_pay_per_post' ); ?></p>
    <div class="pramadillo-badge"><img alt="Logo" src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'img/icon.png' ); ?>"/></div>

    <div class="wc-ppp-settings-wrap">
        <div id="poststuff">

            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <?php if( null === $product_id ): ?>
                    <table class="purchased-content-table" style="width:100%">
                        <thead>
                        <tr>
                            <th style="width:40px;">ID</th>
                            <th>Post Name</th>
                            <th>Protection Type</th>
                            <th>Origin Type</th>
                            <th>Products</th>
                            <th>Users</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $id => $post): ?>
                        <tr>
                            <td><a href="<?php echo esc_url( get_edit_post_link($id) ); ?>"><?php echo absint( $id ); ?></a></td>
                            <td><a href="<?php echo esc_url( get_edit_post_link($id) ); ?>"><?php echo esc_html( $post['title'] ); ?></a></td>
                            <td><?php echo esc_html( $post['protection_type'] ); ?></td>
                            <td><?php echo esc_html( $post['origin_type'] ); ?></td>
                            <td>
                                <?php foreach($post['products'] as $product): ?>
                                <?php if($product): ?>
                                    <a href="<?php echo esc_url( get_edit_post_link($product) ); ?>" class="pramadillo-tag"><?php echo esc_html( get_the_title($product) ); ?></a>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
                            <td><a href="<?php echo esc_url( add_query_arg( 'product_id', absint( $id ) ) ); ?>" class="show_user_table" data-post-id="<?php echo absint( $id ); ?>"><?php echo absint( count($post['users']) ); ?></a></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <a href="javascript: history.go(-1);" class="button button-primary">Go Back</a>
                    <h3>Showing users that purchased <?php echo esc_html( $data[ $product_id ]['title'] ); ?></h3>
                        <table style="width:100%;" class="purchased-content-table">
                            <thead>
                            <tr>
                                <th style="width:50px;!important">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach( $data[ $product_id ]['users'] as $key => $email ): $user = get_user_by( 'email', $email ); ?>
	                            <?php if( $user ): ?>
                                    <tr>
                                        <td><a href="<?php echo esc_url( get_edit_user_link( $user->ID ) );?>"><?php echo absint( $user->ID ); ?></a></td>
                                        <td><a href="<?php echo esc_url( get_edit_user_link( $user->ID ) );?>"><?php echo esc_html( $user->display_name ); ?></a></td>
                                        <td><a href="<?php echo esc_url( get_edit_user_link( $user->ID ) );?>"><?php echo esc_html( $user->user_email ); ?></a></td>
                                    </tr>
	                            <?php else: ?>
                                    <tr>
                                        <td></td>
                                        <td>Not a WordPress user... Guest Checkout is probably enabled when it should not be.</td>
                                        <td><?php echo esc_html( $email ); ?></td>
                                    </tr>
	                            <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>