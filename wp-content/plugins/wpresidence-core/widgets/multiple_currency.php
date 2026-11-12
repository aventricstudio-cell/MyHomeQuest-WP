<?php
/** MILLDONE
 * WpResidence Theme - Multiple Currency Widget
 * src: widgets\multiple_currency.php
 * This file contains the Multiple_currency_widget class, which extends WP_Widget
 * to create a custom widget for displaying and changing currencies in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Widgets
 * @since 1.0.0
 *
 * @uses WP_Widget
 * @uses wpestate_generate_currency_dropdown() Function to generate the currency dropdown
 * @uses icl_register_string() WPML function for string translation (if available)
 */

class Multiple_currency_widget extends WP_Widget {

    /**
     * Set up the widget's unique name, ID, class, description, and other options.
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'multiple_currency_widget',
            'description' => esc_html__('Display a dropdown to change the currency', 'wpresidence-core')
        );
        
        parent::__construct(
            'multiple_currency_widget', // Base ID
            esc_html__('WpEstate: Multiple Currency Widget', 'wpresidence-core'), // Name
            $widget_ops
        );
    }

    /**
     * Output the settings update form in wp-admin.
     *
     * @param array $instance Current settings
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : esc_html__('Change Your Currency', 'wpresidence-core');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'wpresidence-core'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    /**
     * Save widget settings.
     *
     * @param array $new_instance New settings
     * @param array $old_instance Old settings
     * @return array Updated settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';

        // Register the title for WPML translation if the function exists
        if (function_exists('icl_register_string')) {
            icl_register_string('wpestate_Multiple_currency_widget', 'Multiple_currency_widget_title', $instance['title']);
        }

        return $instance;
    }

    /**
     * Output the widget content on the front-end.
     *
     * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'
     * @param array $instance The settings for this instance of the widget
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        if (function_exists('wpestate_generate_currency_dropdown')) {
            echo wpestate_generate_currency_dropdown();
        }

        echo $args['after_widget'];
    }
}