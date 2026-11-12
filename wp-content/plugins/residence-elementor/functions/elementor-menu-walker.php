<?php
namespace ElementorWpResidence\Widgets;

class Wpestate_Elementor_Menu_Custom_Walker extends \Walker_Nav_Menu {        var $columns        = 0;
    var $max_columns    = 0;
    var $rows           = 1;
    var $rowsCounter    = array();
    var $mega_active    = 0;

    
    // starts at the begining of the level
    function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent         =   str_repeat("\t", $depth);
        $style          =   '';

        
        if($depth === 0 && $this->active_megamenu) 
        {
        
            if( !empty($this->megamenu_background)) {
                $style .= 'Background-image:url('.esc_url($this->megamenu_background).');';
            }
            
            if (!empty($this->megamenu_styles) ){
                $style .=  $this->megamenu_styles;
            }
            
          
        }

        $output .= "\n$indent<ul style=\"$style\" class=\"  sub-menu {to_replace_class}\">\n";

        

    }

    // shows at the end of the level
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent     = str_repeat("\t", $depth);
        $output    .= "$indent</ul>\n";
        
        if($depth === 0) 
        {
            if($this->active_megamenu)
            {

                $output = str_replace("{to_replace_class}", "wpestate_megamenu_class wpestate_megamenu_column", $output);
                
                foreach($this->rowsCounter as $row => $columns)
                {
                    $output = str_replace("{current_row_".$row."}", "wpestate_megamenu_column", $output);
                }
                
                $this->columns = 0;
                $this->max_columns = 0;
                $this->rowsCounter = array();
                
            }
            else
            {
                $output = str_replace("{to_replace_class}", "", $output);
            }
        }
    }    


    // shows ar start of the element
    function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
        global $wp_query;
        $item_output            = '';
        $li_text_block_class    = '';
        $column_class           = '';

        $this->megamenu_widgetarea  = get_post_meta( $item->ID, '_menu_item_megamenu_widgetarea', true);
        $this->megamenu_background  = get_post_meta( $item->ID, '_menu_item_megamenu_background', true );
        $this->megamenu_styles      = get_post_meta( $item->ID, '_menu_item_megamenu_styles', true );
        $this->megamenu_border      = get_post_meta( $item->ID, '_menu-item-megamenu-border', true );
        

        if($depth === 0)
        {   
            $this->active_megamenu = get_post_meta( $item->ID, '_menu_item_megamenu', true);
            if($this->active_megamenu) {
                $column_class .= " with-megamenu";
            } else {
                $column_class .= " no-megamenu";
            }

        }


        
        if($depth === 1 && $this->active_megamenu)
        {
            $this->columns ++;
            
            $this->rowsCounter[$this->rows] = $this->columns;
            
            if($this->max_columns < $this->columns) {
                $this->max_columns = $this->columns; 
            }
            
            $column_class  = ' {current_row_'.$this->rows.'}';
            
            if($this->columns == 1){
                $column_class  .= " wpestate_megamenu_first_element";
            }

            if($this->megamenu_widgetarea == false) {
            
                $title = apply_filters( 'the_title', $item->title, $item->ID );

                if($title != "&#8211;" && $title != '"&#8211;"')
                {
                    $menu_icon_tag  = ! empty( $item->menu_icon ) ? '<i class="'.esc_attr( $item->menu_icon ).'"></i>' : '';
                    $menu_label_tag = ! empty( $item->menu_label ) ? '<div class="menu_label menu_label_'.str_replace(" ","_",esc_attr( $item->menu_label )).'">'.esc_attr( $item->menu_label ).'</div>' : '';
                    $attributes = ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn) .'"' : '';      

                    $item_output .= $args->before;
                    $item_output .= '<div class="megamenu-title"'. $attributes .'>';
                    $item_output .= '<a href="'.esc_url($item->url).'">';
                    $item_output .= $menu_icon_tag;
                    $item_output .= $args->link_before . $title . $args->link_after;
                    $item_output .= $menu_label_tag;
                    $item_output .= '</a>';
                    $item_output .= '</div>';
                    $item_output .= $args->after;
                }

            } else {
                 if( is_active_sidebar( $this->megamenu_widgetarea )) {
                    $item_output .= '<div class="megamenu-widgets-container">';
                    ob_start();
                    if ( is_active_sidebar( $this->megamenu_widgetarea  ) ) {
                        dynamic_sidebar( $this->megamenu_widgetarea );
                    }
                    $temp=ob_get_contents();
                    ob_end_clean(); 
                    $item_output .= '<ul>'.$temp. '</ul></div>';
                   
                }
            }
           

           
        } else {

            if($depth === 2 && $this->megamenu_widgetarea && $this->active_megamenu) {

            if( is_active_sidebar( $this->megamenu_widgetarea ) ) {
                $item_output .= '<div class="megamenu-widgets-container">';
                ob_start();
                if ( is_active_sidebar( $this->megamenu_widgetarea   ) ) {
                    dynamic_sidebar( $this->megamenu_widgetarea );
                }
                $temp=ob_get_contents();
                ob_end_clean(); 
                $item_output .= '<ul>'.$temp. '</ul></div>';
        }
        } else {

                $menu_icon_tag  = ! empty( $item->menu_icon ) ? '<i class="'.esc_attr( $item->menu_icon ).'"></i>' : '';
                $menu_label_tag = ! empty( $item->menu_label ) ? '<div class="menu_label menu_label_'.str_replace(" ","_",esc_attr( $item->menu_label )).'">'.esc_attr( $item->menu_label ).'</div>' : '';
                  
                $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
                $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
                $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
                $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : ' href="#"';            

                if(isset($args->before)){
                    $item_output .= $args->before;
                }
                $item_output .= '<a class="menu-item-link" '. $attributes .'>';
                $item_output .= $menu_icon_tag;
                
                if( isset($args->link_before)){
                    $item_output .= $args->link_before;
                }
                
                $item_output .=  apply_filters( 'the_title', $item->title, $item->ID );
                
                if(isset($args->link_after)){
                   $item_output .=   $args->link_after;
                }
                $item_output.= $menu_label_tag;
                $item_output .= '</a>';
                
                if(isset($args->after)){
                    $item_output .= $args->after;
                }
            }
        }
        
        
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

         $border_class='';
        if (!empty($this->megamenu_border) ){
            $border_class.=  'mega_menu_border';
        }
        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="'.$li_text_block_class. esc_attr( $class_names ) . $column_class.'  '.$border_class.' "';
        
       
            
        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
        
        
        
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}

