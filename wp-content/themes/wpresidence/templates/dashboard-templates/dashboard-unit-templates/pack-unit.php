<?php
/** MILLDONE
 * Template part for displaying a single package item in the user dashboard
 * src: templates/dashboard-templates/dashboard-unit-templates/pack-unit.php
 * @package WpResidence
 * @subpackage UserDashboard
 * @since 1.0.0
 */




// Retrieve package details
$postid = get_the_ID();
$pack_list = get_post_meta($postid, 'pack_listings', true);
$pack_featured = get_post_meta($postid, 'pack_featured_listings', true);
$pack_image_included = get_post_meta($postid, 'pack_image_included', true);
$pack_price = get_post_meta($postid, 'pack_price', true);
$unlimited_lists = get_post_meta($postid, 'mem_list_unl', true);
$biling_period = get_post_meta($postid, 'biling_period', true);
$billing_freq = get_post_meta($postid, 'billing_freq', true);
$pack_time = get_post_meta($postid, 'pack_time', true);

// Prepare display information
if ($billing_freq > 1) {
    $biling_period .= 's';
}
$price = $where_currency == 'before' ? $wpestate_currency  . $pack_price : $pack_price .  $wpestate_currency;
$pack_image_included = intval($pack_image_included) == 0 ? esc_html__('Unlimited', 'wpresidence') : $pack_image_included;
$title = get_the_title();
?>

<div class="pack-listing" data-id="<?php echo esc_attr($postid); ?>">
    <div class="pack-listing-title" 
         data-stripetitle2="<?php echo esc_attr($title); ?>"  
         data-stripetitle="<?php echo esc_attr($title) . ' ' . esc_attr__('Package Payment', 'wpresidence'); ?>" 
         data-stripepay="<?php echo esc_attr($pack_price * 100); ?>" 
         data-packprice="<?php echo esc_attr($pack_price); ?>" 
         data-packid="<?php echo esc_attr($postid); ?>">
        <?php echo esc_html($title); ?>
    </div>
    <div class="submit-price"><?php echo esc_html($price); ?> / <?php echo esc_html($billing_freq); ?> <?php echo wpestate_show_bill_period($biling_period); ?></div>
    <div class="pack-listing-period">
        <?php
        if ($unlimited_lists == 1) {
            echo esc_html__('Unlimited', 'wpresidence') . ' ' . esc_html__('listings ', 'wpresidence');
        } else {
            echo esc_html($pack_list) . ' ' . esc_html__('Listings', 'wpresidence');
        }
        ?>
    </div>
    <div class="pack-listing-period"><?php echo esc_html($pack_featured) . ' ' . esc_html__('Featured', 'wpresidence'); ?></div>
    <div class="pack-listing-period"><?php echo esc_html($pack_image_included) . ' ' . esc_html__('Images / per listing', 'wpresidence'); ?></div>
    <div class="buypackage">
        <input type="checkbox" class="input_pak_check" name="packagebox" value="1" style="display:block;" />
        <?php echo esc_html__('Select package', 'wpresidence'); ?>
    </div>
</div>