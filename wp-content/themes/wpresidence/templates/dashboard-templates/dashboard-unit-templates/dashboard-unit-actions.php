<?php
/** MILLDONE
 * Dashboard Property Actions Template
 * src: templates\dashboard-templates\dashboard-unit-templates\dashboard-unit-actions.php
 * Displays the actions dropdown menu for properties in the user dashboard.
 * Includes options for editing, deleting, duplicating, featuring properties and managing listings.
 *
 * @package WpResidence
 * @subpackage Dashboard/Templates
 * @since 1.0
 * 
 * Required variables:
 * @param int $post_id The property post ID
 * @param string $floor_link Optional floor plan edit link
 */

// Generate action URLs
$edit_link = wpestate_get_template_link('page-templates/user_dashboard_add.php');
$list_link = wpestate_get_template_link('page-templates/user_dashboard.php');
$analytics_link = wpestate_get_template_link('page-templates/user_dashboard_analytics.php');

// Add query parameters to URLs
$analytics_link = add_query_arg('analytics_id', $post_id, $analytics_link);
$featured_link = add_query_arg('featured_edit', $post_id, $list_link);
$duplicate_link = add_query_arg('duplicate', $post_id, $list_link);
$edit_link = esc_url_raw(add_query_arg('listing_edit', $post_id, $edit_link));

// Handle floor link if available
if (isset($floor_link)) {
    $floor_link = esc_url_raw(add_query_arg('floor_edit', $post_id, $floor_link));
}

// Get property and user data
$post_status = get_post_status($post_id);
$no_views = intval(get_post_meta($post_id, 'wpestate_total_views', true));
$pay_status_simple = get_post_meta($post_id, 'pay_status', true);
$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));

// Get user information
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$parent_userID = wpestate_check_for_agency($userID);
$user_pack = get_the_author_meta('package_id', $parent_userID);
$remaining_lists = wpestate_get_remain_listing_user($parent_userID, $user_pack);

// Setup defaults for title attribute
$defaults = array('echo' => false);
?>

<div class="btn-group">
    <!-- Action Button -->
    <button type="button" class="btn btn-default dropdown-toggle property_dashboard_actions_button" 
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php esc_html_e('Actions', 'wpresidence'); ?>
    </button>

    <!-- Dropdown Menu -->
    <ul class="dropdown-menu">
        <!-- Edit Property -->
        <li>
            <a class="dashboad-tooltip" href="<?php echo esc_url($edit_link); ?>">
                <?php esc_html_e('Edit property', 'wpresidence'); ?>
            </a>
        </li>

        <!-- Delete Property -->
        <li>
            <a class="dashboad-tooltip" 
               onclick="return confirm('<?php echo esc_js(sprintf(esc_html__('Are you sure you wish to delete %s?', 'wpresidence'), the_title_attribute($defaults))); ?>')" 
               href="<?php echo esc_url_raw(add_query_arg('delete_id', $post_id, wpestate_get_template_link('page-templates/user_dashboard.php'))); ?>">
                <?php esc_html_e('Delete property', 'wpresidence'); ?>
            </a>
        </li>

        <!-- Duplicate Property -->
        <?php if ($paid_submission_status != 'membership' || 
                  ($paid_submission_status == 'membership' && ($remaining_lists > 0 || $remaining_lists == -1))) : ?>
            <li>
                <a class="dashboad-tooltip" href="<?php echo esc_url($duplicate_link); ?>">
                    <?php esc_html_e('Duplicate Property', 'wpresidence'); ?>
                </a>
            </li>
        <?php else : ?>
            <li>
                <a class="dashboad-tooltip not_" href="#">
                    <?php esc_html_e('Not enough listings to duplicate', 'wpresidence'); ?>
                </a>
            </li>
        <?php endif; ?>

        <!-- Analytics -->
        <li>
            <a class="dashboad-tooltip" href="<?php echo esc_url($analytics_link); ?>">
                <?php esc_html_e('Views Stats', 'wpresidence'); ?>
            </a>
        </li>

        <!-- Expired Property Actions -->
        <?php if ($post_status == 'expired') : ?>
            <li>
                <a class="dashboad-tooltip resend_pending" data-listingid="<?php echo intval($post_id); ?>">
                    <?php esc_html_e('Resend for approval', 'wpresidence'); ?>
                </a>
            </li>
        <?php endif; ?>

        <!-- Payment Status Actions -->
        <?php if ($paid_submission_status == 'per listing') : ?>
            <?php if (strtolower($pay_status_simple) == 'paid') : ?>
                <?php if (intval(get_post_meta($post_id, 'prop_featured', true)) == 1) : ?>
                    <li><a><?php esc_html_e('Paid & Featured', 'wpresidence'); ?></a></li>
                <?php else : ?>
                    <li>
                        <a href="#" class="per_listing_payment" data-listingid="<?php echo intval($post_id); ?>">
                            <?php esc_html_e('Upgrade to Featured', 'wpresidence'); ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php else : ?>
                <li>
                    <a href="#" class="per_listing_payment" data-listingid="<?php echo intval($post_id); ?>">
                        <?php esc_html_e('Pay', 'wpresidence'); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Enable/Disable Listing -->
        <?php if ($post_status == 'publish') : ?>
            <li>
                <a href="#" class="dashboad-tooltip disable_listing disabledx" 
                   data-postid="<?php echo intval($post_id); ?>">
                    <?php esc_html_e('Disable Listing', 'wpresidence'); ?>
                </a>
            </li>
        <?php elseif ($post_status == 'disabled') : ?>
            <li>
                <a href="#" class="dashboad-tooltip disable_listing" 
                   data-postid="<?php echo intval($post_id); ?>">
                    <?php esc_html_e('Enable Listing', 'wpresidence'); ?>
                </a>
            </li>
        <?php endif; ?>

        <!-- Membership Features -->
        <?php if ($paid_submission_status == 'membership') : ?>
            <?php if (intval(get_post_meta($post_id, 'prop_featured', true)) != 1) : ?>
                <li>
                    <a class="dashboad-tooltip make_featured" 
                       href="<?php echo esc_url($featured_link); ?>" 
                       data-bs-toggle="tooltip" 
                       title="<?php esc_attr_e('Set as featured, *Listings set as featured are subtracted from your package', 'wpresidence'); ?>">
                        <?php esc_html_e('Set as featured', 'wpresidence'); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>