<?php
/** MILLDONE
 * Featured Agent Template for WpResidence Theme
 * src: templates\agent_card_templates\agent_unit_featured.php
 * This template displays a featured agent card with their details and listings.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core
 * - WpResidence theme functions and templates
 */

// Retrieve all agent details at once
$agent_details = wpestate_return_agent_details('', $postID);


// Check if agent details were successfully retrieved
if (!is_array($agent_details) || empty($agent_details)) {
    // Handle the error case, maybe log it or display a message
    return;
}

// Extract commonly used details with fallbacks
$name = isset($agent_details['realtor_name']) ? $agent_details['realtor_name'] : '';
$link = isset($agent_details['link']) ? $agent_details['link'] : '';

// Prepare agent thumbnail
$thumb_prop = '';
if (isset($agent_details['realtor_image'])) {
    $thumb_id = get_post_thumbnail_id($postID);
    if ($thumb_id) {
        $thumb_prop = wp_get_attachment_image(
            $thumb_id,
            'property_listings',
            false,
            array(
                'data-original' => $agent_details['realtor_image'],
                'class'         => 'lazyload img-responsive',
            )
        );
    }
}

if (empty($thumb_prop)) {
    $thumb_prop = '<img src="' . esc_url(get_theme_file_uri('/img/default_user.png')) . '" alt="' . esc_attr__('user image', 'wpresidence') . '">';
}

// Get agent notes
if (empty($notes)) {
    $notes = wpestate_strip_excerpt_by_char(get_the_excerpt($postID), 120, $postID, '...');
}

// Determine column class
$col_class = '';
if (isset($item_length) || isset($col_org)) {
    $col_class = isset($col_org) ? "col-md-$col_org" : $item_length;
    echo '<div class="' . esc_attr($col_class) . ' listing_wrapper">';
}
?>

<div class="wpresidence_agent_unit_wrapper">
    <div class="agent_unit agent_unit_featured" data-link="<?php echo esc_attr($link); ?>">
        <div class="agent-unit-img-wrapper">
            <div class="prop_new_details_back"></div>
            <?php echo $thumb_prop; ?>
        </div>
        <div class="agent_unit_content">
            <h4><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($name); ?></a></h4>
            <?php if (isset($agent_details['realtor_position'])): ?>
                <div class="agent_position"><?php echo esc_html($agent_details['realtor_position']); ?></div>
            <?php endif; ?>
            <div class="agent_featured_details">

                        <div class="featured_agent_notes "><?php echo wp_kses_post($notes); ?></div>

                <?php
                $contact_details = array(
                    'phone'  => array('icon' => 'fas fa-phone', 'key' => 'realtor_phone'),
                    'mobile' => array('icon' => 'fas fa-mobile-alt', 'key' => 'realtor_mobile'),
                    'email'  => array('icon' => 'far fa-envelope', 'key' => 'email')
                );
                foreach ($contact_details as $type => $detail) {
                    if (isset($agent_details[$detail['key']]) && !empty($agent_details[$detail['key']])) {
                        echo '<div class="agent_detail"><i class="' . esc_attr($detail['icon']) . '"></i>' . esc_html($agent_details[$detail['key']]) . '</div>';
                    }
                }
                ?>
            </div>
            <div class="agent_unit_social">
                <?php 
                if (function_exists('wpestate_return_agent_share_social_icons')) {
                    echo wpestate_return_agent_share_social_icons($agent_details, 'social-wrapper featured_agent_social', '');
                }
                ?>
            </div>

            <a class="see_my_list_featured" href="<?php echo esc_url($link); ?>" target="_blank">
                <span class="featured_agent_listings wpresidence_button"><?php esc_html_e('My Listings', 'wpresidence'); ?></span>
            </a>
        </div>
    </div>
</div>

<?php
if (isset($item_length) || isset($col_org)) {
    echo '</div>';
}
?>