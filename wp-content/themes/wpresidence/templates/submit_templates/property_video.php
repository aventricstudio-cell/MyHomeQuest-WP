<?php
/** MILLDONE
 * WpResidence Video Options Section
 * src: templates/submit_templates/property_video.php
 * This file is part of the WpResidence theme and handles the display and selection
 * of video options in the property submission or edit form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions (get_post_meta)
 * - WpResidence theme functions (wpestate_submit_return_value)
 * - Global variables: $wpestate_submission_page_fields, $allowed_html, $get_listing_edit
 *
 * Usage:
 * This file should be included in the property submission or edit form within the WpResidence theme.
 * It displays options for selecting video type and entering video ID.
 */

// Initialize video type
$video_type = '';

// Handle property edit scenario
if (isset($get_listing_edit) && is_numeric($get_listing_edit)) {
    $edit_id = intval($get_listing_edit);
    $video_type = esc_html(get_post_meta($edit_id, 'embed_video_type', true));
}

// Handle form submission
if (isset($_POST['embed_video_type'])) {
    $video_type = wp_kses(esc_html($_POST['embed_video_type']), $allowed_html);
}

// Generate video type options
// Allow Vimeo, YouTube, and TikTok sources
$video_values = array('vimeo', 'youtube', 'tiktok');
$option_video = '';
foreach ($video_values as $value) {
    $option_video .= sprintf(
        '<option value="%1$s" %2$s>%3$s</option>',
        esc_attr($value),
        selected($value, $video_type, false),
        esc_html(ucfirst($value))
    );
}

// Check if video options should be displayed
$display_video_options = is_array($wpestate_submission_page_fields) && 
    (in_array('embed_video_type', $wpestate_submission_page_fields, true) || 
     in_array('embed_video_id', $wpestate_submission_page_fields, true));

if ($display_video_options) :
?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Video Option', 'wpresidence'); ?></div>
        
        <?php if (is_array($wpestate_submission_page_fields) && in_array('embed_video_type', $wpestate_submission_page_fields, true)) : ?>
            <div class="col-md-12">
                <label for="embed_video_type"><?php esc_html_e('Video from', 'wpresidence'); ?></label>
                <select id="embed_video_type" name="embed_video_type" class="select-submit2">
                    <?php echo $option_video; ?>
                </select>
            </div>
        <?php endif; ?>
        
        <?php if (is_array($wpestate_submission_page_fields) && in_array('embed_video_id', $wpestate_submission_page_fields, true)) : ?>
            <div class="col-md-12">
                <label for="embed_video_id"><?php esc_html_e('Embed Video ID:', 'wpresidence'); ?></label>
                <input type="text" id="embed_video_id" class="form-control" name="embed_video_id" size="40"
                    value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('embed_video_id', $get_listing_edit, ''))); ?>">
            </div>
        <?php endif; ?>
    </div>
<?php
endif;
?>