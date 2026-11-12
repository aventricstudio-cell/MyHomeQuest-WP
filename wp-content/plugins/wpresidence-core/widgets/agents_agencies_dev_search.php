<?php
/** MILLDONE
 * Agents Agencies Developers Search Widget for WpResidence Theme
 * src: widgets\agents_agencies_dev_search.php
 * This file contains the Agents_Agencies_Dev_Search_widget class, which extends WP_Widget
 * to create a custom search widget for agents, agencies, and developers in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Widgets
 * @since 1.0.0
 */

class Agents_Agencies_Dev_Search_widget extends WP_Widget {
    /**
     * Constructor for the Agents_Agencies_Dev_Search_widget class
     */
    public function __construct() {
        parent::__construct(
            'ag_ag_dev_search_widget', // Base ID
            esc_html__('WpEstate: Agents Agencies Developers Search', 'wpresidence-core'), // Name
            array(
                'description' => esc_html__('A search widget for agents, agencies, and developers', 'wpresidence-core'),
                'classname' => 'advanced_search_sidebar ag_ag_dev_search_widget boxed_widget',
            ) // Args
        );
    }
    
    /**
     * Front-end display of the widget
     *
     * @param array $args     Widget arguments
     * @param array $instance Saved values from database
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = ! empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        $search_link = wpestate_get_template_link('page-templates/aag_search_results.php');
        $select_args = wpestate_get_select_arguments();

        // Widget content
        $this->render_search_form($search_link, $select_args);

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     *
     * @param array $instance Previously saved values from database
     */
    public function form($instance) {
        $title = ! empty($instance['title']) ? $instance['title'] : esc_html__('Agents Search', 'wpresidence-core');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'wpresidence-core'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     *
     * @param array $new_instance Values just sent to be saved
     * @param array $old_instance Previously saved values from database
     * @return array Updated safe values to be saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }

    /**
     * Render the search form
     *
     * @param string $search_link The URL for the search results page
     * @param array $select_args Arguments for taxonomy queries
     */
    private function render_search_form($search_link, $select_args) {
        $widget_taxonomies_filter = $this->get_widget_taxonomies_filter();
        ?>
        <form role="search" method="get" action="<?php echo esc_url($search_link); ?>">
            <?php
            $this->render_keyword_search_field();
            $this->render_search_type_selector($widget_taxonomies_filter);
            $this->render_dynamic_dropdowns($widget_taxonomies_filter, $select_args);

            // Add WPML language form field if applicable
            if (function_exists('icl_translate')) {
                do_action('wpml_add_language_form_field');
            }
            ?>
            <button class="wpresidence_button" id="advanced_submit_widget"><?php esc_html_e('Search', 'wpresidence-core'); ?></button>
        </form>
        <?php
    }

    /**
     * Get the widget taxonomies filter structure
     *
     * @return array The widget taxonomies filter structure
     */
    private function get_widget_taxonomies_filter() {
        return array(
            array(
                'post_type' => 'estate_agent',
                'selector_name' => esc_html__('Agent', 'wpresidence-core'),
                'tax_to_query' => array(
                    'property_city_agent' => esc_html__('Select City', 'wpresidence-core'),
                    'property_area_agent' => esc_html__('Select Area', 'wpresidence-core'),
                    'property_category_agent' => esc_html__('Select Category', 'wpresidence-core'),
                    'property_action_category_agent' => esc_html__('Select Action Category', 'wpresidence-core'),
                ),
            ),
            array(
                'post_type' => 'estate_agency',
                'selector_name' => esc_html__('Agency', 'wpresidence-core'),
                'tax_to_query' => array(
                    'city_agency' => esc_html__('Select City', 'wpresidence-core'),
                    'area_agency' => esc_html__('Select Area', 'wpresidence-core'),
                    'category_agency' => esc_html__('Select Agency Category', 'wpresidence-core'),
                    'action_category_agency' => esc_html__('Select Action Category', 'wpresidence-core'),
                ),
            ),
            array(
                'post_type' => 'estate_developer',
                'selector_name' => esc_html__('Developer', 'wpresidence-core'),
                'tax_to_query' => array(
                    'property_city_developer' => esc_html__('Select City', 'wpresidence-core'),
                    'property_area_developer' => esc_html__('Select Area', 'wpresidence-core'),
                    'property_category_developer' => esc_html__('Select Category', 'wpresidence-core'),
                    'property_action_developer' => esc_html__('Select Action Category', 'wpresidence-core'),
                ),
            ),  
        );
    }

    /**
     * Render the keyword search field
     */
    private function render_keyword_search_field() {
        $keyword_search = isset($_GET['_keyword_search']) ? sanitize_text_field(wp_unslash($_GET['_keyword_search'])) : '';
        ?>
        <input type="text" id="keyword_search" class="form-control" name="_keyword_search" placeholder="<?php esc_attr_e('Name', 'wpresidence-core'); ?>" value="<?php echo esc_attr($keyword_search); ?>">
        <?php
    }

    /**
     * Render the search type selector
     *
     * @param array $widget_taxonomies_filter The widget taxonomies filter structure
     */
    private function render_search_type_selector($widget_taxonomies_filter) {
        $search_type_selector = '';
        foreach ($widget_taxonomies_filter as $single_post_type) {
            $search_type_selector .= sprintf(
                '<li role="presentation" data-value="%s">%s</li>',
                esc_attr($single_post_type['post_type']),
                esc_html($single_post_type['selector_name'])
            );
        }

        $search_type_title = $widget_taxonomies_filter[0]['selector_name'];
        $search_type_value = $widget_taxonomies_filter[0]['post_type'];

        if (isset($_GET['_search_post_type'])) {
            $requested_post_type = sanitize_text_field(wp_unslash($_GET['_search_post_type']));
            foreach ($widget_taxonomies_filter as $single_post_type) {
                if ($requested_post_type === $single_post_type['post_type']) {
                    $search_type_title = $single_post_type['selector_name'];
                    $search_type_value = $single_post_type['post_type'];
                    break;
                }
            }
        }

        ?>
        <div class="dropdown wpresidence_dropdown">
            <button data-toggle="dropdown" id="sidebar-search_post_type" 
                class="btn dropdown-toggle"
                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                data-value="<?php echo esc_attr(strtolower(rawurlencode($search_type_value))); ?>"> 
                <?php echo esc_html($search_type_title); ?>               
            </button>           
            <input type="hidden" name="_search_post_type" value="<?php echo esc_attr(strtolower(rawurlencode($search_type_value))); ?>">
            <ul class="dropdown-menu filter_menu aag_picker"
                role="menu" aria-labelledby="sidebar-search_post_type">
                <?php echo $search_type_selector; ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Render dynamic dropdowns
     *
     * @param array $widget_taxonomies_filter The widget taxonomies filter structure
     * @param array $args Arguments for taxonomy queries
     */
    private function render_dynamic_dropdowns($widget_taxonomies_filter, $args) {
		$search_type_value   =   $widget_taxonomies_filter[0]['post_type'];
		
        foreach ($widget_taxonomies_filter as $single_post_type) {
            foreach ($single_post_type['tax_to_query'] as $key => $value) {
                $taxonomy_values_to_process = wpestate_get_taxonomy_select_list($args, $key, $value);
                $initial_tax_value = 'all';
                
                if (isset($_GET['_' . $key])) {
                    $initial_tax_value = sanitize_text_field(wp_unslash($_GET['_' . $key]));
                    foreach ($taxonomy_values_to_process['values'] as $single_value) {
                        if ($single_value['slug'] === $initial_tax_value) {
                            $value = $single_value['text'];
                            break;
                        }
                    }
                }

                $display_style = ($search_type_value !== $single_post_type['post_type']) ? ' style="display:none;" ' : '';
                $hidden_input_name='_'.$key;
                $button_id ='sidebar-'. $key;
                $ul_list_class = $key . '_ul_list';
                $wrapper_class = 'ag_ag_dev_search_selector selector_for_'.$single_post_type['post_type'];
				if (function_exists('wpresidence_render_single_dropdown')) {
                    wpresidence_render_single_dropdown(
                        $wrapper_class,
                        $button_id,
                        $value,
                        $initial_tax_value,
                        $display_style,
                        $hidden_input_name,
                        $ul_list_class,
                        $taxonomy_values_to_process['text']
                    );
                }

                ?>
               
                <?php
            }
        }
    }
}