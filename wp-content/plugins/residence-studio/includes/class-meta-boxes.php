<?php
class WpResidence_Meta_Boxes {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_head_foot_metabox']);
        add_action('save_post', [$this, 'save_head_foot_metabox']);
    }

    public function add_head_foot_metabox() {
        add_meta_box(
            'wpestate_head_foot_metabox',
            esc_html__('Template Settings', 'wpresidence-core'),
            [$this, 'render_head_foot_metabox'],
            'wpestate-studio',
            'normal',
            'high'
        );
    }




   
/**
 * Simple Meta Box Update
 * Just modify the existing render_head_foot_metabox method
 */

public function render_head_foot_metabox($post) {
    // Retrieve the current values
    $template = get_post_meta($post->ID, 'wpestate_head_foot_template', true);
    $positions = get_post_meta($post->ID, 'wpestate_head_foot_positions', true);
    if (empty($positions)) {
        $single = get_post_meta($post->ID, 'wpestate_head_foot_position', true);
        if (!empty($single)) {
            $positions = array($single);
        }
    }
    if (!is_array($positions)) {
        $positions = array('');
    }

    $exclude_positions = get_post_meta($post->ID, 'wpestate_head_foot_exclude_positions', true);
    if (empty($exclude_positions)) {
        $exclude_positions = array('');
    }
    if (!is_array($exclude_positions)) {
        $exclude_positions = array($exclude_positions);
    }

    $tax_terms_selected = get_post_meta($post->ID, 'wpestate_head_foot_tax_terms', true);
    if (!is_array($tax_terms_selected)) {
        $tax_terms_selected = array();
    }
    $tax_terms_exclude_selected = get_post_meta($post->ID, 'wpestate_head_foot_exclude_tax_terms', true);
    if (!is_array($tax_terms_exclude_selected)) {
        $tax_terms_exclude_selected = array();
    }

    $full_width = get_post_meta($post->ID, 'wpestate_custom_full_width', true);
    if ($full_width === '') {
        $full_width = 'no';
    }

    // Nonce field for security
    wp_nonce_field('wpestate_save_head_foot_metabox', 'wpestate_head_foot_nonce');

    // Get the template types and FILTERED location options
    $types = wpestate_desing_template_types();
    $location = wpestate_templates_selection_options(); // <-- PASS THE TEMPLATE TYPE
    $tax_terms = wpestate_get_all_taxonomy_terms();
    ?>

    <p>
    <label class="post-attributes-label">
        <?php esc_html_e('Template Type', 'wpresidence-core');?>
    </label>
    </p>
    <select name="wpestate_head_foot_template" id="template-type-select" class="wpresidence-2025-select">
        <?php foreach ($types as $value => $label): ?>
            <?php 
            // Add data attributes
            $data_template = '';
            if ($value == 'wpestate_single_property_page') $data_template = 'data-template="property"';
            if ($value == 'wpestate_single_agent') $data_template = 'data-template="agent"';
            if ($value == 'wpestate_single_agency') $data_template = 'data-template="agency"';
            if ($value == 'wpestate_single_developer') $data_template = 'data-template="developer"';
            if ($value == 'wpestate_single_post') $data_template = 'data-template="post"';
            if ($value == 'wpestate_category_page') $data_template = 'data-template="category"';
            ?>
            <option value="<?php echo esc_attr($value); ?>" <?php echo $data_template; ?> <?php selected($template, $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p>
        <label class="post-attributes-label" for="wpestate_custom_full_width">
            <?php esc_html_e('Custom template should be full width ?', 'wpresidence-core');?>
        </label>
    </p>
    <select name="wpestate_custom_full_width" id="wpestate_custom_full_width" class="wpresidence-2025-select">
        <option value="no" <?php selected($full_width, 'no');?>><?php esc_html_e('No', 'wpresidence-core');?></option>
        <option value="yes" <?php selected($full_width, 'yes');?>><?php esc_html_e('Yes', 'wpresidence-core');?></option>
    </select>


    <div id="location-section" style="<?php echo empty($template) ? 'display:none;' : ''; ?>">
        <p>
            <label class="post-attributes-label">
                <?php esc_html_e('Display Location', 'wpresidence-core');?>
            </label>
        </p>
        <div id="wpestate-location-container">
            <?php
            foreach ($positions as $idx => $pos) {
                $term_val = isset($tax_terms_selected[$idx]) ? $tax_terms_selected[$idx] : '';
                echo wpestate_create_nested_select_field(
                    'wpestate_head_foot_positions[]',
                    $location,
                    $pos,
                    'wpestate_head_foot_tax_terms[]',
                    $tax_terms,
                    $term_val
                );
            }
            ?>
        </div>
        <p>
            <button type="button" class="button wpresidence_button button-primary" id="wpestate-add-location"><?php esc_html_e('Add Location', 'wpresidence-core');?></button>
        </p>

        <p>
            <label class="post-attributes-label">
                <?php esc_html_e('Exclude Location', 'wpresidence-core');?>
            </label>
        </p>
        <div id="wpestate-exclude-location-container">
            <?php
            foreach ($exclude_positions as $idx => $ex_pos) {
                $term_val = isset($tax_terms_exclude_selected[$idx]) ? $tax_terms_exclude_selected[$idx] : '';
                echo wpestate_create_nested_select_field(
                    'wpestate_head_foot_exclude_positions[]',
                    $location,
                    $ex_pos,
                    'wpestate_head_foot_exclude_tax_terms[]',
                    $tax_terms,
                    $term_val
                );
            }
            ?>
        </div>
        <p>
            <button type="button" class="button wpresidence_button button-primary" id="wpestate-add-exclude-location"><?php esc_html_e('Add Exclude Location', 'wpresidence-core');?></button>
        </p>
    </div>

    <?php
}



























    public function save_head_foot_metabox($post_id) {
        // Verify nonce
        if (!isset($_POST['wpestate_head_foot_nonce']) || !wp_verify_nonce($_POST['wpestate_head_foot_nonce'], 'wpestate_save_head_foot_metabox')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sanitize and save the fields
        if (isset($_POST['wpestate_head_foot_template'])) {
            update_post_meta($post_id, 'wpestate_head_foot_template', sanitize_text_field($_POST['wpestate_head_foot_template']));
        }

        if (isset($_POST['wpestate_custom_full_width'])) {
            update_post_meta($post_id, 'wpestate_custom_full_width', sanitize_text_field($_POST['wpestate_custom_full_width']));
        } else {
            delete_post_meta($post_id, 'wpestate_custom_full_width');
        }


        if (isset($_POST['wpestate_head_foot_tax_terms'])) {
            $tax_terms = array_map('sanitize_text_field', (array) $_POST['wpestate_head_foot_tax_terms']);
            update_post_meta($post_id, 'wpestate_head_foot_tax_terms', $tax_terms);
        } else {
            delete_post_meta($post_id, 'wpestate_head_foot_tax_terms');
        }

        if (isset($_POST['wpestate_head_foot_exclude_tax_terms'])) {
            $ex_terms = array_map('sanitize_text_field', (array) $_POST['wpestate_head_foot_exclude_tax_terms']);
            update_post_meta($post_id, 'wpestate_head_foot_exclude_tax_terms', $ex_terms);
        } else {
            delete_post_meta($post_id, 'wpestate_head_foot_exclude_tax_terms');
        }

        if (isset($_POST['wpestate_head_foot_positions'])) {
            $positions = array_map('sanitize_text_field', (array) $_POST['wpestate_head_foot_positions']);
            $positions = array_filter($positions);
            update_post_meta($post_id, 'wpestate_head_foot_positions', $positions);
            delete_post_meta($post_id, 'wpestate_head_foot_position');
        } elseif (isset($_POST['wpestate_head_foot_position'])) {
            // Fallback for old single position field
            update_post_meta($post_id, 'wpestate_head_foot_positions', array(sanitize_text_field($_POST['wpestate_head_foot_position'])));
            delete_post_meta($post_id, 'wpestate_head_foot_position');
        } else {
            delete_post_meta($post_id, 'wpestate_head_foot_positions');
            delete_post_meta($post_id, 'wpestate_head_foot_position');
        }

        if (isset($_POST['wpestate_head_foot_exclude_positions'])) {
            $exclude_positions = array_map('sanitize_text_field', (array) $_POST['wpestate_head_foot_exclude_positions']);
            $exclude_positions = array_filter($exclude_positions);
            update_post_meta($post_id, 'wpestate_head_foot_exclude_positions', $exclude_positions);
        } else {
            delete_post_meta($post_id, 'wpestate_head_foot_exclude_positions');
        }
    }
}
