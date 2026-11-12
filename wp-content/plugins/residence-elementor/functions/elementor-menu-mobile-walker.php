<?php
namespace ElementorWpResidence\Widgets;

class Wpestate_Elementor_Menu_Mobile_Custom_Walker extends \Walker_Nav_Menu {
    var $columns = 0;
    var $max_columns = 0;
    var $rows = 1;
    var $rowsCounter = array();
    var $html_block = '';
    var $design = '';
    var $width = '';
    var $icon_type = '';
    var $icon_id = '';
    var $icon_width = '';
    var $icon_height = '';
    var $icon_html = '';

    /**
     * Starts the list before the elements are added.
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int $depth Depth of the item.
     * @param array $args Additional arguments.
     */
    function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul  class=\"sub-menu {to_replace_class}\">\n";
       
    }

    /**
     * Ends the list after the elements are added.
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int $depth Depth of the item.
     * @param array $args Additional arguments.
     */
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";

        if ($depth === 0) {
            $output = str_replace("{to_replace_class}", "", $output);
        }
        
    }

    /**
     * Starts the element output.
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param object $item Menu item data object.
     * @param int $depth Depth of the item.
     * @param array $args Additional arguments.
     * @param int $current_object_id ID of the current item.
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
        
        global $wp_query;
        $item_output = '';


      
        $this->html_block = get_post_meta($item->ID, '_menu_item_html_block', true);
        $this->design = get_post_meta($item->ID, '_menu_item_design', true);
        $this->width = get_post_meta($item->ID, '_menu_item_width', true);
        $this->icon_type = get_post_meta($item->ID, '_menu_item_icon_type', true);
        $this->icon_id = get_post_meta($item->ID, '_menu_item_icon_id', true);
        $this->icon_width = get_post_meta($item->ID, '_menu_item_icon_width', true);
        $this->icon_height = get_post_meta($item->ID, '_menu_item_icon_height', true);
        $this->icon_html = get_post_meta($item->ID, '_menu_item_icon_html', true);


      
        $menu_icon_tag = !empty($item->menu_icon) ? '<i class="'.esc_attr($item->menu_icon).'"></i>' : '';
        $menu_label_tag = !empty($item->menu_label) ? '<div class="menu_label menu_label_'.str_replace(" ","_",esc_attr($item->menu_label)).'">'.esc_attr($item->menu_label).'</div>' : '';

        $attributes  = !empty($item->attr_title) ? ' title="'.esc_attr($item->attr_title).'"' : '';
        $attributes .= !empty($item->target) ? ' target="'.esc_attr($item->target).'"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="'.esc_attr($item->xfn).'"' : '';
        $attributes .= !empty($item->url) ? ' href="'.esc_attr($item->url).'"' : ' href="#"';

        $item_output .= $args->before;
        $item_output .= '<a class="menu-item-link"'. $attributes .'>';
        $item_output .= $menu_icon_tag;
        $item_output .= $args->link_before;
        $item_output .= apply_filters('the_title', $item->title, $item->ID);
        $item_output .= $args->link_after;
        $item_output .= $menu_label_tag;
        $item_output .= '</a>';
        $item_output .= $args->after;

    

        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

     

        // Add current menu item classes
        if (in_array('current-menu-item', $classes) || in_array('current-menu-parent', $classes) || in_array('current-menu-ancestor', $classes)) {
            $classes[] = 'current-menu-item';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="'.esc_attr($class_names).' " ';

        $output .= $indent . '<li id="menu-item-'. $item->ID .'"' . $value . $class_names .'>';
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }


    function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {
        if ($depth == "") {
            $depth = 0;
        }
        $id_field = $this->db_fields['id'];
        $id = $element->$id_field;

        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }

        if (get_post_meta($id, '_menu_item_html_block', true)) {
            $this->unset_children($element, $children_elements);
        }

        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    
}