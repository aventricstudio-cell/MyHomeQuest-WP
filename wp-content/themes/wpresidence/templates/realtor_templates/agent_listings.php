<?php
/** MILLDONE
 * Agent Listings Display
 * src: templates\realtor_templates\agent_listings.php
 * This file is responsible for displaying the listings of an agent
 * in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Retrieve the number of properties to display
$prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));



// Get the agent ID
$agent_id = get_post_meta($post->ID, 'user_meda_id', true) ?: -1;

// Check if property slider is enabled
$wpestate_property_unit_slider = wpresidence_get_option('wp_estate_prop_list_slider', '');

// Determine the current page
$paged = isset($_GET['pagelist']) ? intval($_GET['pagelist']) : 1;

// Prepare query arguments based on agent type
$args = [
    'post_type'         => 'estate_property',
    'paged'             => $paged,
    'posts_per_page'    => $prop_no ,
    'post_status'       => 'publish',
    'meta_key'          => 'prop_featured',
    'orderby'           => 'meta_value',
    'order'             => 'DESC',
];

$mapargs = [
    'post_type'         => 'estate_property',
    'post_status'       => 'publish',
    'posts_per_page'    => -1,
];

if ($agent_id === -1) {
    $args['meta_query'] = $mapargs['meta_query'] = [
        [
            'key'     => 'property_agent',
            'value'   => $post->ID,
        ],
    ];
} else {
    $args['author'] = $mapargs['author'] = $agent_id;
}

// Get filtered properties
$prop_selection = wpestate_return_filtered_by_order($args);

// Get property categories and prepare tab terms
$tab_terms = [];
$transient_agent_id = $agent_id === -1 ? "meta_property_agent_{$post->ID}" : "custom_post_{$agent_id}";

$terms = wpestate_get_cached_terms('property_category');

foreach ($terms as $term) {
    $term_args = [
        'post_type'         => 'estate_property',
        'posts_per_page'    => -1,
        'post_status'       => 'publish',
        'fields'            => 'ids',
        // Allow language filters so translated term counts stay accurate.
        'suppress_filters'  => false,
        'tax_query'         => [
            [
                'taxonomy' => 'property_category',
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ],
        ],
    ];

    if ($agent_id === -1) {
        $term_args['meta_query'] = $args['meta_query'];
    } else {
        $term_args['author'] = $agent_id;
    }

    $all_posts = get_posts($term_args);

    if (!empty($all_posts)) {
        $tab_terms[$term->term_id] = [
            'name'  => $term->name,
            'slug'  => $term->slug,
            'count' => count($all_posts)
        ];
    }
}

// Create nonce for AJAX
$ajax_nonce = wp_create_nonce("wpestate_developer_listing_nonce");
?>
<input type="hidden" id="wpestate_developer_listing_nonce" value="<?php echo esc_attr($ajax_nonce); ?>" />
<?php
// Display agent listings
if ($prop_selection->have_posts()):
    ?>
    <div class="wpresidence_realtor_listings_wrapper single_listing_block">
        <h3 class="agent_listings_title"><?php esc_html_e('My Listings', 'wpresidence'); ?></h3>
        
        <?php
        // Include the term bar template
        get_template_part('templates/realtor_templates/agent-term-bar', null, [
            'prop_selection' => $prop_selection,
            'tab_terms' => $tab_terms
        ]);
        ?>
        
        <div class="agency_listings_wrapper row">
            <?php wpresidence_display_property_list_as_html($prop_selection, $wpestate_options, 'agent_listings'); ?>
        </div>
        
        <div class="spinner" id="listing_loader">
            <div class="new_prelader"></div>
        </div>
        
        <div class="load_more_ajax_cont">
            <input type="button" class="wpresidence_button listing_load_more" value="<?php esc_attr_e('Load More Properties', 'wpresidence'); ?>">
        </div>
    </div>
    <?php
endif;





// Handle Google Maps integration
if (wp_script_is('googlecode_regular', 'enqueued')) {
    $max_pins = intval(wpresidence_get_option('wp_estate_map_max_pins'));
    $mapargs['posts_per_page'] = $max_pins;
    $mapargs['offset'] = ($paged - 1) * 9;
    $mapargs['fields'] = 'ids';
    
    $transient_appendix = "_agent_listings_{$transient_agent_id}";
    $selected_pins = wpestate_listing_pins($transient_appendix, 1, $mapargs, 1);
    
    wp_localize_script('googlecode_regular', 'googlecode_regular_vars2', [
        'markers2' => $selected_pins,
        'agent_id' => $agent_id
    ]);
}
?>
