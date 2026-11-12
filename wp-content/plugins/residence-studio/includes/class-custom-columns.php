<?php
class WpResidence_Custom_Columns {

    public function __construct() {
        add_filter('manage_edit-wpestate_head_foot_columns', [$this, 'customize_columns']);
        add_action('manage_posts_custom_column', [$this, 'populate_columns']);
        add_filter('manage_edit-wpestate_head_foot_sortable_columns', [$this, 'sortable_columns']);

        // Columns for wpestate-studio templates
        add_filter('manage_wpestate-studio_posts_columns', [$this, 'studio_columns']);
        add_action('manage_wpestate-studio_posts_custom_column', [$this, 'studio_column_content'], 10, 2);
    }

    public function customize_columns($columns) {
        $slice = array_slice($columns, 2, 2);
        unset($columns['comments']);
        unset($slice['comments']);
        $splice = array_splice($columns, 2);
        $columns['head_foot_price'] = esc_html__('Price', 'wpresidence-core');
        $columns['head_foot_for'] = esc_html__('Billing For', 'wpresidence-core');
        $columns['head_foot_type'] = esc_html__('Headers & Footers Type', 'wpresidence-core');
        $columns['head_foot_user'] = esc_html__('Purchased by User', 'wpresidence-core');
        $columns['head_foot_status'] = esc_html__('Status', 'wpresidence-core');
        return array_merge($columns, array_reverse($slice));
    }

    public function populate_columns($column) {
        $the_id = get_the_ID();
        if ('head_foot_price' == $column) {
            echo get_post_meta($the_id, 'item_price', true);
        }

        if ('head_foot_for' == $column) {
            echo get_post_meta($the_id, 'head_foot_type', true);
        }

        if ('head_foot_type' == $column) {
            echo get_post_meta($the_id, 'biling_type', true);
        }

        if ('head_foot_user' == $column) {
            $user_id = get_post_meta($the_id, 'buyer_id', true);
            $user_info = get_userdata($user_id);
            if (isset($user_info->user_login)) {
                echo esc_html($user_info->user_login);
            }
        }
        if ('head_foot_status' == $column) {
            $stat = get_post_meta($the_id, 'pay_status', 1);
            if ($stat == 0) {
                esc_html_e('Not Paid', 'wpresidence-core');
            } else {
                esc_html_e('Paid', 'wpresidence-core');
            }
        }
    }

    public function sortable_columns($columns) {
        $columns['head_foot_price'] = 'head_foot_price';
        $columns['head_foot_user'] = 'head_foot_user';
        $columns['head_foot_for'] = 'head_foot_for';
        $columns['head_foot_type'] = 'head_foot_type';
        $columns['head_foot_status'] = 'head_foot_status';
        return $columns;
    }

    /**
     * Add columns to the wpestate-studio list table.
     */
    public function studio_columns($columns) {
        $new = array();
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ('title' === $key) {
                $new['wpestate_type'] = esc_html__('Type', 'wpresidence-core');
                $new['wpestate_display_rules'] = esc_html__('Display Rules', 'wpresidence-core');
            }
        }
        return $new;
    }

    /**
     * Populate custom columns for wpestate-studio.
     */
    public function studio_column_content($column, $post_id) {
        if ('wpestate_type' === $column) {
            $type = get_post_meta($post_id, 'wpestate_head_foot_template', true);
            $types = wpestate_desing_template_types();
            echo isset($types[$type]) ? esc_html($types[$type]) : esc_html($type);
        }

        if ('wpestate_display_rules' === $column) {
            $locations = get_post_meta($post_id, 'wpestate_head_foot_positions', true);
            if (empty($locations)) {
                $single = get_post_meta($post_id, 'wpestate_head_foot_position', true);
                $locations = $single ? array($single) : array();
            }
            if (!is_array($locations)) {
                $locations = array($locations);
            }

            $exclude = get_post_meta($post_id, 'wpestate_head_foot_exclude_positions', true);
            if (!is_array($exclude)) {
                $exclude = !empty($exclude) ? array($exclude) : array();
            }

            $options = wpestate_templates_selection_options();
            $display_labels = $this->labels_from_options($locations, $options);
            $exclude_labels = $this->labels_from_options($exclude, $options);

            $output = '';
            if (!empty($display_labels)) {
                $output .= esc_html__('Display:', 'wpresidence-core') . ' ' . implode(', ', $display_labels);
            }
            if (!empty($exclude_labels)) {
                if ($output !== '') {
                    $output .= '<br />';
                }
                $output .= esc_html__('Exclude:', 'wpresidence-core') . ' ' . implode(', ', $exclude_labels);
            }
            echo $output;
        }
    }

    /**
     * Helper to map stored values to labels.
     */
    private function labels_from_options($values, $options) {
        $labels = array();
        foreach ($values as $value) {
            foreach ($options as $group) {
                if (isset($group['value'][$value])) {
                    $labels[] = $group['value'][$value];
                    continue 2;
                }
            }
        }
        return $labels;
    }
}
