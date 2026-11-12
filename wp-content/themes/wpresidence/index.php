<?php
/** MILLDONE
 * Index Template
 * src: index.php
 * This template serves as the default template for displaying blog posts
 * in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

get_header();

$wpestate_options = get_query_var('wpestate_options');
?>

<div class="row wpresidence_page_content_wrapper">
    <?php 
    // Include the breadcrumbs template
    get_template_part('templates/breadcrumbs'); 
    ?>
    
    <div class="<?php echo esc_attr(isset($wpestate_options['content_class']) ? $wpestate_options['content_class'] : ''); ?>">
        
        
       
        <h1 class="entry-title title_prop"><?php the_title(); ?></h1>
       


        <div class="row">
            <?php
            // Set up the query for blog posts
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 0;
            $args = array(
                'post_type' => 'post',
                'paged'     => $paged,
                'post_status'    => 'publish'
            );

            // Allow developers to modify the query arguments
            $args = apply_filters('wpestate_blog_list_query_args', $args);

            // Create a new WP_Query instance for blog posts
            $blog_selection = new WP_Query($args);
            wpresidence_display_blog_list_as_html($blog_selection, $wpestate_options, $context = 'blog_list'); 
            
            ?>
        </div>
   


        <?php 
        // Display pagination if there are posts
        if ($blog_selection->have_posts()) :
            wpestate_pagination($blog_selection->max_num_pages, 2); 
        endif;
        ?>    
    </div><!-- end main content container -->
    
    <?php  
    // Include the sidebar
    include get_theme_file_path('sidebar.php'); 
    ?>
</div>   

<?php 
// Include the footer
get_footer(); 
?>
