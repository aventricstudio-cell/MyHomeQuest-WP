<?php
/** MILLDONE
 * WpResidence Dashboard List Filter Actions Template
 * src: templates/dashboard-templates/dashboard-list-filter-actions.php
 * This template displays the order by dropdown for the property listing in the user dashboard.
 *
 * @package WpResidence
 * @subpackage Dashboard
 * @since 1.0.0
 */

// Get the property list link
$property_list_link = wpestate_get_template_link('page-templates/user_dashboard.php');

// Set the default order label
$order_label = esc_html__('Order By', 'wpresidence');

// Define the dropdown values
$values_dropdown = array(
    0 => array('label' => esc_html__('Default Order', 'wpresidence'), 'value' => 0),
    1 => array('label' => esc_html__('Price - High to Low', 'wpresidence'), 'value' => 1),
    2 => array('label' => esc_html__('Price - Low to High', 'wpresidence'), 'value' => 2),
    7 => array('label' => esc_html__('Bathrooms - High to Low', 'wpresidence'), 'value' => 7),
    8 => array('label' => esc_html__('Bathrooms - Low to High', 'wpresidence'), 'value' => 8),
    4 => array('label' => esc_html__('Date - Old to New', 'wpresidence'), 'value' => 4),
    3 => array('label' => esc_html__('Date - New to Old', 'wpresidence'), 'value' => 3),
);

// Add status to property list link if set
if (isset($_GET['status']) && intval($_GET['status']) != 0) {
    $property_list_link = add_query_arg('status', intval($_GET['status']), $property_list_link);
}

// Set order label if orderby is set
if (isset($_GET['orderby']) && intval($_GET['orderby']) != 0) {
    $order_label = $values_dropdown[intval($_GET['orderby'])]['label'];
}
?>

<div class="dropdown wpresidence_dropdown wpestate_dashhboard_filter">
    <button type="button" class="btn dropdown-toggle filter_menu_trigger property_dashboard_actions_button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php echo esc_html($order_label); ?>
    </button>
    <ul class="dropdown-menu">
        <?php foreach ($values_dropdown as $item): ?>
            <li>
                <a class="dashboad-tooltip" href="<?php echo esc_url(add_query_arg('orderby', $item['value'], $property_list_link)); ?>">
                    <?php echo esc_html($item['label']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>