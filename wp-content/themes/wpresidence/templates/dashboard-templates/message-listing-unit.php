<?php
/** MILLDONE
 * Template for displaying individual message units in the dashboard inbox
 * src: templates/dashboard-templates/message-listing-unit.php
 * This template displays a single message with its replies and reply form.
 * It's used within the dashboard inbox list to show message details,
 * including sender information, subject, content, and reply functionality.
 *
 * @package WpResidence
 * @subpackage Dashboard
 * @since WpResidence 1.0
 */

// Fetch and sanitize message metadata
$message_from_user      = intval(get_post_meta($messageID, 'message_from_user', true));
$user                   = get_user_by('id', $message_from_user);
$message_from_user_name = esc_html($user->user_login);
$message_status         = get_post_meta($messageID, 'message_status' . $userID, true);
$message_title          = get_the_title($messageID);
$message_content        = get_the_content();

?>
<div class="row property_wrapper_dash flex-md-row flex-column inbox_row">
    <div class="message_listing d-flex flex-column " data-messid="<?php echo esc_attr($messageID); ?>">
        
        <!-- Message Header Section -->
        <div class="message_header flex-md-row flex-column ">
            <div class="col-12 col-md-3">
                <?php
                // Display unread message indicator
                if ($message_status === 'unread') {
                    printf(
                        '<span class="mess_unread mess_tooltip" data-bs-toggle="tooltip" title="%s"><i class="fas fa-exclamation-circle"></i></span>',
                        esc_attr__('new message', 'wpresidence')
                    );
                }

                // Display message sender information
                if ($current_user->user_login === $message_from_user_name) {
                    echo '<span class="mess_from"><strong>' . 
                         esc_html__('Conversation started by you ', 'wpresidence') . 
                         '</strong></span>';
                } else {
                    printf(
                        '<span class="mess_from"><strong>%s: </strong>%s</span>',
                        esc_html__('From', 'wpresidence'),
                        esc_html($message_from_user_name)
                    );
                }
                ?>
            </div>

            <div class="col-12 col-md-4">
                <span class="mess_subject">
                    <strong><?php esc_html_e('Subject', 'wpresidence'); ?>: </strong>
                    <?php echo esc_html($message_title); ?>
                </span>
            </div>

            <div class=" col-12 col-md-2">
                <span class="mess_date"><?php echo esc_html(get_the_date()); ?></span>
            </div>

            <div class="col-12 col-md-3 message-action">
                <span class="mess_reply mess_tooltip" 
                    data-bs-toggle="tooltip" title="<?php esc_attr_e('reply to message', 'wpresidence'); ?>">
                    <i class="fas fa-reply"></i>
                </span>
                <div class="delete_wrapper">
                    <span class="mess_delete mess_tooltip" 
                        data-bs-toggle="tooltip" title="<?php esc_attr_e('delete message', 'wpresidence'); ?>">
                        <i class="fas fa-times deleteprop"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Message Content Section -->
        <div class="mess_content">
            <h4><?php echo esc_html($message_title); ?></h4>
            <div class="message_content">
                <?php
                // Handle split message content
                $pieces = explode('||', $message_content);
                echo wp_kses_post(nl2br($pieces[0]));
                
                if (isset($pieces[1])) {
                    echo '<br>';
                    echo wp_kses_post(nl2br($pieces[1]));
                }

                // Display message replies
                echo '<div class="mess_content-list-replies">';
                
                $args_child = array(
                    'post_type'         => 'wpestate_message',
                    'post_status'       => 'publish',
                    'posts_per_page'    => -1,
                    'order'             => 'ASC',
                    'post_parent'       => $messageID,
                );

                $message_selection_child = new WP_Query($args_child);
                
                while ($message_selection_child->have_posts()): 
                    $message_selection_child->the_post();
                    
                    // Get the post author's ID using get_the_ID()
                    $author_id = get_post_field('post_author', get_the_ID());
  
                    // Retrieve the user information using the author ID
                    $reply_user = get_user_by('id', $author_id);

                
                    ?>
                    <div class="mess_content-list-replies_unit">
                        <h4>
                            <strong><?php esc_html_e('From: ', 'wpresidence'); ?></strong>
                            <?php echo esc_html($reply_user->user_login); ?> - 
                            <?php echo esc_html(get_the_title($messageID)); ?>
                        </h4>
                        <?php echo wp_kses_post(nl2br(get_the_content())); ?>
                    </div>
                    <?php
                endwhile;
                
                wp_reset_postdata();
                echo '</div>';
                ?>

                <span class="wpresidence_button mess_send_reply_button2">
                    <?php esc_html_e('Reply', 'wpresidence'); ?>
                </span>
            </div>
        </div>

        <!-- Reply Form Section -->
        <div class="mess_reply_form">
            <h4><?php esc_html_e('Reply', 'wpresidence'); ?></h4>
            <input type="text" 
                   class="subject_reply" 
                   value="<?php echo esc_attr('Re: ' . $message_title); ?>">
            <textarea name="message_reply_content" 
                      class="message_reply_content"></textarea>
            <br>
            <span class="wpresidence_button mess_send_reply_button" 
                  data-messid="<?php echo esc_attr($messageID); ?>">
                <?php esc_html_e('Send Reply', 'wpresidence'); ?>
            </span>
        </div>
    </div>
</div>