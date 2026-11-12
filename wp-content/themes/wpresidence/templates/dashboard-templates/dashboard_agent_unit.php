<?php
/** MILLDONE
 * Template for displaying individual agent units in the dashboard
 * src: templates\dashboard-templates\dashboard_agent_unit.php
 * This template is responsible for rendering individual agent cards/rows
 * in the WPResidence dashboard. It displays agent information including
 * their photo, contact details, and status.
 * 
 * @package WpResidence
 * @subpackage DashboardTemplates
 * @since 1.0.0
 */



// Fetch agent details
$agent_details = array(
    'phone'     => get_post_meta($agentID, 'agent_phone', true),
    'mobile'    => get_post_meta($agentID, 'agent_mobile', true),
    'email'     => get_post_meta($agentID, 'agent_email', true),
    'position'  => get_post_meta($agentID, 'agent_position', true),
    'user_id'   => get_post_meta($agentID, 'user_meda_id', true)
);

// Sanitize all agent details
array_walk($agent_details, 'esc_html');

// Get agent name and permalink
$agent_name = get_the_title();
$agent_link = esc_url(get_permalink());

// Get agent thumbnail
$thumb_props = array(
    'class' => 'lazyload img-responsive'
);
$agent_thumbnail = get_the_post_thumbnail($agentID, 'widget_thumb', $thumb_props);

// Set default thumbnail if none exists
if (empty($agent_thumbnail)) {
    $default_image_url = get_theme_file_uri('/img/default_user.png');
    $agent_thumbnail = sprintf(
        '<img src="%s" alt="%s">',
        esc_url($default_image_url),
        esc_attr__('user image', 'wpresidence')
    );
}

// Get and process agent status
$post_status = get_post_status($agentID);
$status_map = array(
    'expired'  => esc_html__('Expired', 'wpresidence'),
    'publish'  => esc_html__('Published', 'wpresidence'),
    'disabled' => esc_html__('Disabled', 'wpresidence'),
    'draft'    => esc_html__('Draft', 'wpresidence'),
    'default'  => esc_html__('Waiting for approval', 'wpresidence')
);
$status = isset($status_map[$post_status]) ? $status_map[$post_status] : $status_map['default'];
$status_class = sanitize_key(array_search($status, $status_map));
?>

<div class="row property_wrapper_dash flex-md-row flex-column">
    <!-- Agent Image and Basic Info -->
    <div class="blog_listing_image col-12 col-md-12 col-lg-12 col-xl-4 ">
        <div class="dashboard_agent_listing_image">
            <?php echo wp_kses_post($agent_thumbnail); ?>
        </div>
        
        <div class="property_dashboard_location_wrapper">
            <a class="dashbard_unit_title" href="<?php echo esc_url($agent_link); ?>">
                <?php echo esc_html($agent_name); ?>
            </a>
            <div class="property_dashboard_location">
                <?php 
                if (!empty($agent_details['position'])) {
                    echo esc_html($agent_details['position']) . '<br>';
                }
                printf(
                    '%s %s',
                    esc_html__('user id:', 'wpresidence'),
                    esc_html($agent_details['user_id'])
                );
                ?>
            </div>
        </div>
    </div>

    <!-- Agent Contact Information -->
    <div class="col-md-2 property_dashboard_types"><?php
        $contact_info = array_filter(array(
            $agent_details['phone'],
            $agent_details['mobile']
        ));
        echo esc_html(implode(' / ', $contact_info));
        ?>
    </div>

    <!-- Agent Email -->
    <div class="col-md-2 property_dashboard_types"><?php 
      echo esc_html($agent_details['email']); ?>
    </div>

    <!-- Agent Status -->
    <div class="col-md-2 property_dashboard_status">
        <div class="property_list_status_label <?php echo esc_attr($status_class); ?>">
            <?php echo esc_html($status); ?>
        </div>
    </div>

    <!-- Agent Actions -->
    <div class="col-md-2 property_dashboard_action">
        <?php 
          include(locate_template('templates/dashboard-templates/dashboard-unit-templates/dashboard-agent-unit-actions.php'));
        ?>
    </div>
</div>