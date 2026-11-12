<?php
/** MILLDONE
 * Agency/Developer Map Display
 * src: templates\agency_templates\agency_map.php
 * This file handles the display of a map for an agency or developer in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Map
 * @since 1.0.0
 */

global $post;

// Determine if we're on a developer page
$is_developer = is_singular('estate_developer');

// Get the appropriate latitude and longitude
$lat_key = $is_developer ? 'developer_lat' : 'agency_lat';
$long_key = $is_developer ? 'developer_long' : 'agency_long';

$latitude = get_post_meta($post->ID, $lat_key, true);
$longitude = get_post_meta($post->ID, $long_key, true);

// Ensure we have valid coordinates
$has_coordinates = !empty($latitude) && !empty($longitude);

if ($has_coordinates) :
?>
<div class="agency_developer_map_wrapper">
    <div id="agency_map"
        data-cur_lat="<?php echo esc_attr($latitude); ?>"
        data-cur_long="<?php echo esc_attr($longitude); ?>">
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
    wpestate_agency_map_function();
});
</script>
<?php
endif;
?>