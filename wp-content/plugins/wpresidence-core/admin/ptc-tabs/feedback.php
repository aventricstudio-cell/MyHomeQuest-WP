<?php
/**
 * Feedback tab renderer.
 *
 * Displays a form so site admins can send feedback
 * directly to the theme authors.
 *
 * @package WpResidence Core
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the feedback form tab.
 *
 * Outputs a small form that sends an email to the
 * theme authors when submitted.
 */
function wpresidence_ptc_render_feedback_tab() {
    if ( isset( $_GET['sent'] ) ) {
        echo '<div class="notice notice-success"><p>' . esc_html__( 'Feedback sent. Thank you!', 'wpresidence-core' ) . '</p></div>';
    }
    ?>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'wpresidence_feedback_nonce_action', 'wpresidence_feedback_nonce' ); ?>
        <div class="wpresidence-form wpresidence-third">
            <div class="wpresidence-row wpresidence-column">
                <label class="wpresidence-label-full" for="wpresidence_feedback_name"><?php echo esc_html__( 'Name', 'wpresidence-core' ); ?></label>
                <input type="text" name="wpresidence_feedback_name" id="wpresidence_feedback_name" class="wpresidence-2025-input" />
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label class="wpresidence-label-full" for="wpresidence_feedback_email"><?php echo esc_html__( 'Email', 'wpresidence-core' ); ?></label>
                <input type="email" name="wpresidence_feedback_email" id="wpresidence_feedback_email" class="wpresidence-2025-input" />
            </div>
            <div class="wpresidence-row wpresidence-column">
                <label class="wpresidence-label-full" for="wpresidence_feedback_message"><?php echo esc_html__( 'Message', 'wpresidence-core' ); ?></label>
                <textarea name="wpresidence_feedback_message" id="wpresidence_feedback_message" class="wpresidence-2025-input" rows="5"></textarea>
            </div>
        </div>
        <input type="hidden" name="action" value="wpresidence_feedback_submit">
        <?php 
        submit_button( 
            esc_html__( 'Send Feedback', 'wpresidence-core' ), 
            'primary wpresidence_button' 
        ); 
        ?>
    </form>
    <?php
}

/**
 * Handle submission of the feedback form.
 *
 * Validates the request and emails the provided
 * details to the theme author.
 */
function wpresidence_ptc_handle_feedback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have permission to perform this action.', 'wpresidence-core' ) );
    }

    check_admin_referer( 'wpresidence_feedback_nonce_action', 'wpresidence_feedback_nonce' );

    $name    = isset( $_POST['wpresidence_feedback_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wpresidence_feedback_name'] ) ) : '';
    $email   = isset( $_POST['wpresidence_feedback_email'] ) ? sanitize_email( wp_unslash( $_POST['wpresidence_feedback_email'] ) ) : '';
    $message = isset( $_POST['wpresidence_feedback_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wpresidence_feedback_message'] ) ) : '';

    $subject = 'WPResidence Feedback';

    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    if ( $email ) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    }

    wp_mail( 'office@wpestate.org', $subject, nl2br( $message ), $headers );

    wp_redirect( add_query_arg( array(
        'page' => 'wpresidence-post-type-control-feedback',
        'sent' => '1',
    ), admin_url( 'admin.php' ) ) );
    exit;
}
