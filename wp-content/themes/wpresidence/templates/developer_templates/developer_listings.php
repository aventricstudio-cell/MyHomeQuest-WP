<?php
/** MILLDONE
 * Developer Listings Query and Display
 * src: templates\developer_templates\developer_listings.php
 * This file handles the query and display of property listings for a developer in the WpResidence theme.
 * It includes filtering by property categories and pagination.
 *
 * @package WpResidence
 * @subpackage DeveloperListings
 * @since 1.0.0
 */

// Get developer/agency information
$user_agency = get_post_meta($post->ID, 'user_meda_id', true);
$agent_list = (array)get_user_meta($user_agency, 'current_agent_list', true);
$agent_list[] = $user_agency;
$agent_list=array_filter($agent_list);
$prop_no        =   intval( wpresidence_get_option('wp_estate_prop_no', '') );

// Handle pagination
$paged = isset($_GET['pagelist']) ? max(1, intval($_GET['pagelist'])) : get_query_var('page', 1);

// Set up the base query arguments
$base_args = array(
    'post_type'      => 'estate_property',
    'posts_per_page' => $prop_no,
    'post_status'    => 'publish',
    'meta_key'       => 'prop_featured',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'paged'          => $paged,
);

// Adjust query based on agent list
if (count($agent_list) == 0 || $user_agency == 0) {
    $args = array_merge($base_args, array(
        'meta_query' => array(
            array(
                'key'   => 'property_agent',
                'value' => $post->ID,
            ),
        ),
    ));
} else {
    $args = array_merge($base_args, array(
        'author__in' => $agent_list,
    ));
}

// Get properties
$prop_selection = wpestate_return_filtered_by_order($args);



// Get property categories
$terms = wpestate_get_cached_terms('property_category');

$tab_terms = array();
foreach ($terms as $single_term) {
    $term_args = array_merge($args, array(
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'property_category',
                'field'    => 'term_id',
                'terms'    => $single_term->term_id,
            ),
        ),
        'fields' => 'ids'
    ));
    
    $all_posts = get_posts($term_args);
    
    if (count($all_posts) > 0) {
        $tab_terms[$single_term->term_id] = array(
            'name'  => $single_term->name,
            'slug'  => $single_term->slug,
            'count' => count($all_posts)
        );
    }
}

// Build term bar
$term_bar = '<div class="term_bar_item active_term" data-term_id="0" data-term_name="all">' . 
            esc_html__('All', 'wpresidence') . ' (' . esc_html($prop_selection->found_posts) . ')</div>';

foreach ($tab_terms as $key => $value) {
    $term_bar .= sprintf(
        '<div class="term_bar_item" data-term_id="%s" data-term_name="%s">%s (%s)</div>',
        esc_attr($key),
        esc_attr($value['slug']),
        esc_html($value['name']),
        esc_html($value['count'])
    );
}

// Create nonce for AJAX
$ajax_nonce = wp_create_nonce("wpestate_developer_listing_nonce");

// Output starts here
?>

<input type="hidden" id="wpestate_developer_listing_nonce" value="<?php echo esc_attr($ajax_nonce); ?>" />

<?php if ($prop_selection->have_posts()) : ?>
    <div class="wpresidence_realtor_listings_wrapper developer_listing agency_listings_title single_listing_block">
        <div class="term_bar_wrapper developer_listings  mx-0 d-flex flex-column flex-md-row" data-agency_id="<?php echo esc_attr($user_agency); ?>" data-post_id="<?php echo esc_attr($post->ID); ?>">
            <?php echo $term_bar; // XSS OK - escaped above ?>
        </div>
        <div class="agency_listings_wrapper row">
            <?php wpresidence_display_property_list_as_html($prop_selection, $wpestate_options, 'directory_list'); ?>
        </div>
        <div class="spinner" id="listing_loader">
            <div class="new_prelader"></div>
        </div>
        <div class="load_more_ajax_cont">
            <input type="button" class="wpresidence_button listing_load_more" value="<?php esc_attr_e('Load More Properties', 'wpresidence'); ?>">
        </div>
    </div>
<?php endif; ?>