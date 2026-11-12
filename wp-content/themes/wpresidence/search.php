<?php
/** MILLDONE
 * Search Results Template
 * src: search.php
 * This template handles the display of search results
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
        
        
        <?php
        if (have_posts()){
        ?>
        <h1 class="entry-title-search entry-title title_prop">
            <?php esc_html_e( 'Search Results for : ','wpresidence');print '"' . get_search_query() . '"';?>
        </h1>
        <?php
        }
        ?>

     


        <div class="row" style="min-height:500px;">
            <?php
            if (have_posts()){
                // Create a new WP_Query instance for blog posts
                global $wp_query;
                wpresidence_display_blog_list_as_html($wp_query, $wpestate_options, $context = 'blog_list'); 
            }else{
                echo '<h3>'.esc_html__('No results found','wpresidence').'</h3>';
            }
            ?>
        </div>
   


        <?php 
        // Display pagination if there are posts
        if ($wp_query->have_posts()) :
            wpestate_pagination($wp_query->max_num_pages, 2); 
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
