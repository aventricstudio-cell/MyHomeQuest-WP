<?php
/** MILLDONE
 * Template Name: User Dashboard Stats
 * src: page-templates\user_dashboard_stats.php
 * Displays analytics and statistics for properties in the WPResidence dashboard.
 * Includes property views tracking and graphical representation of statistics.
 * 
 * @package WPResidence
 * @subpackage Dashboard
 * @since 1.0.0
 * 
 * File: page-templates/user_dashboard_analytics.php
 */

// Security check
wpestate_dashboard_header_permissions();

// Initialize user data
$current_user = wp_get_current_user();
$userID                         =   $current_user->ID;
$user_data = array(
    'id'         => $current_user->ID,
    'login'      => $current_user->user_login,
    'agent_id'   => intval(get_user_meta($current_user->ID, 'user_agent_id', true))
);

// Get and validate agent list
$agent_list = (array)get_user_meta($user_data['id'], 'current_agent_list', true);
$agent_list[] = $user_data['id'];

// Check agent status
$agent_status = get_post_status($user_data['agent_id']);
if ($agent_status === 'pending' || $agent_status === 'disabled') {
    wp_safe_redirect(esc_url(home_url('/dashboard-main/')));
    exit;
}

// Get and validate analytics ID
$analytics_id = isset($_GET['analytics_id']) ? intval($_GET['analytics_id']) : 0;
$author_id = wpsestate_get_author($analytics_id);

// Verify author permissions
if (!in_array($author_id, $agent_list)) {
    wp_safe_redirect(esc_url(home_url('/')));
    exit;
}

get_header();
?>

<div class="row row_user_dashboard">
    <?php 
    // Include dashboard sidebar
    include(locate_template('templates/dashboard-templates/dashboard-left-col.php')); 
    ?>

    <div class="col-md-9 dashboard-margin row">
        <?php
        // Include membership profile
        include(locate_template('templates/dashboard-templates/user_memebership_profile.php'));
        
        // Display dashboard title
        wpestate_show_dashboard_title(
            esc_html__('Analytics', 'wpresidence'),
            '',
            ''
        );
        ?>
        <div class="dashboard-wrapper-form row">
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper">
                    <?php
                    if ($analytics_id > 0) {
                        // Get total views
                        $total_views = intval(get_post_meta($analytics_id, 'wpestate_total_views', true));
                        ?>
                        <div class="statistics_wrapper_dashboard">
                            <!-- Back to properties link -->
                            <a href="<?php echo esc_url(wpestate_get_template_link('page-templates/user_dashboard.php')); ?>" 
                            class="back_prop_list">
                                <?php esc_html_e('Back to properties list', 'wpresidence'); ?>
                            </a>

                            <!-- Total views display -->
                            <div class="statistics_wrapper_total_views">
                                <?php 
                                printf(
                                    '%s %d',
                                    esc_html__('Total number of views:', 'wpresidence'),
                                    $total_views
                                );
                                ?>
                            </div>

                            <!-- Statistics chart -->
                            <canvas class="my_chart_dash" 
                                    id="myChart_<?php echo esc_attr($analytics_id); ?>">
                            </canvas>
                        </div>
                        <?php 
                    } 
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Initialize chart
$chart_init = sprintf(
    'jQuery(document).ready(function(){ wpestate_load_stats(%d); });',
    intval($analytics_id)
);
?>

<script type="text/javascript">
    //<![CDATA[
    <?php echo $chart_init; ?>
    //]]>
</script>

<?php
// Create security nonces
$nonces = array(
    'tab_stats'          => wp_create_nonce('wpestate_tab_stats'),
    'property_actions'   => wp_create_nonce('wpestate_property_actions')
);

// Output hidden nonce fields
foreach ($nonces as $id => $nonce) {
    printf(
        '<input type="hidden" id="wpestate_%s" value="%s" />',
        esc_attr($id),
        esc_attr($nonce)
    );
}

get_footer();
?>