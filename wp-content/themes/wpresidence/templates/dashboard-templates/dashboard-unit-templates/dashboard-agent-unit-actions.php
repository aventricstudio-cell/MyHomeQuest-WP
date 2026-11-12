<?php
/** MILLDONE
 * Template for displaying agent action buttons in the dashboard
 * src: templates\dashboard-templates\dashboard-unit-templates\dashboard-agent-unit-actions.php
 * This template renders the action dropdown menu for each agent listing
 * in the WPResidence dashboard. Actions include edit, delete, and enable/disable.
 * 
 * @package WpResidence
 * @subpackage DashboardTemplates
 * @since 1.0.0
 */

// Initialize required variables

$post_status = get_post_status($agentID);

// Get edit link
$edit_link = wpestate_get_template_link('page-templates/user_dashboard_add_agent.php');
$edit_link = esc_url_raw(add_query_arg('listing_edit', $agentID, $edit_link));

// Get delete link
$delete_link = wpestate_get_template_link('page-templates/user_dashboard_agent_list.php');
$delete_link = esc_url_raw(add_query_arg('delete_id', $agentID, $delete_link));

// Setup title attribute options
$title_args = array(
    'echo' => false,
);

// Prepare enable/disable button properties
$enable_disable_props = array(
    'publish' => array(
        'class' => 'dashboad-tooltip disable_listing disable_agent disabledx',
        'text' => esc_html__('Disable Agent', 'wpresidence')
    ),
    'disabled' => array(
        'class' => 'dashboad-tooltip disable_listing disable_agent',
        'text' => esc_html__('Enable Agent', 'wpresidence')
    )
);
?>

<div class="btn-group">
    <!-- Action Button -->
    <button type="button" 
            class="btn btn-default dropdown-toggle property_dashboard_actions_button" 
            data-bs-toggle="dropdown" 
            aria-haspopup="true" 
            aria-expanded="false">
        <?php esc_html_e('Actions', 'wpresidence'); ?> 
        <span class="caret"></span>
    </button>

    <!-- Dropdown Menu -->
    <ul class="dropdown-menu">
        <!-- Edit Action -->
        <li>
            <a href="<?php echo esc_url($edit_link); ?>">
                <?php esc_html_e('Edit Agent', 'wpresidence'); ?>
            </a>
        </li>

        <!-- Delete Action -->
        <li>
            <a class="dashboad-tooltip" 
               href="<?php echo esc_url($delete_link); ?>"
               onclick="return confirm('<?php 
                   printf(
                       '%s %s?',
                       esc_html__('Are you sure you wish to delete', 'wpresidence'),
                       esc_html(the_title_attribute($title_args))
                   ); 
               ?>')">
                <?php esc_html_e('Delete Agent', 'wpresidence'); ?>
            </a>
        </li>

        <!-- Enable/Disable Action -->
        <li>
            <?php 
            if (isset($enable_disable_props[$post_status])) {
                $button = $enable_disable_props[$post_status];
                printf(
                    '<a href="" class="%s" data-postid="%d">%s</a>',
                    esc_attr($button['class']),
                    intval($agentID),
                    esc_html($button['text'])
                );
            }
            ?>
        </li>
    </ul>
</div>