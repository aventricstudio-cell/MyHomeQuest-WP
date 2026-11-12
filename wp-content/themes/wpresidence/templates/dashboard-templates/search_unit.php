<?php
/** MILLDONE
 * Saved Search Display Template
 * src: templates\dashboard-templates\search_unit.php
 * Displays saved property search parameters and provides deletion option.
 * Shows search criteria and arguments in a formatted display.
 *
 * @package WpResidence
 * @subpackage Dashboard/SavedSearches
 * @since 1.0
 * 
 * Required variables:
 * @param int    $searchID                 The saved search post ID
 * @param array  $custom_advanced_search   Custom search fields
 * @param array  $adv_search_what         Search field types
 * @param array  $adv_search_how          Search comparison operators
 * @param array  $adv_search_label        Search field labels
 */
?>

<div class=" row property_wrapper_dash flex-md-row flex-column search_unit_wrapper">
    <div class="search-title  col-md-8 p-0">
        <h4><?php the_title(); ?></h4>
  
    
        <?php
        // Retrieve and decode search parameters
        $search_arguments = get_post_meta($searchID, 'search_arguments', true);
        $search_arguments_decoded = (array)json_decode($search_arguments, true);
    
        // Retrieve and decode meta arguments
        $meta_arguments = get_post_meta($searchID, 'meta_arguments', true);
        $meta_arguments = (array)json_decode($meta_arguments, true);
        ?>
        
        <!-- Search Parameters Display -->
        <div class="col-md search_param">
            <strong><?php esc_html_e('Search Parameters: ', 'wpresidence'); ?></strong>
            <?php
            echo wpestate_show_search_params_new(
                $meta_arguments,
                $search_arguments_decoded,
                $custom_advanced_search,
                $adv_search_what,
                $adv_search_how,
                $adv_search_label
            );
            ?>
        </div>
    </div>
    
    <!-- Delete Search Button -->
   
        <a class="delete_search col-md-4 wpresidence_button"
           data-searchid="<?php echo intval($searchID); ?>">
            <?php esc_html_e('delete search', 'wpresidence'); ?>
        </a>
  
</div>