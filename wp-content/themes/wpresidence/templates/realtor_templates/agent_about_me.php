<?php
/** MILLDONE
 * Agent Content Display
 * src: templates\realtor_templates\agent_about_me.php
 * This file is responsible for displaying the content of an agent's profile
 * in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Check if the current post type is 'estate_agent'
if ( 'estate_agent' === get_post_type( $post->ID ) ) {
    ?>
    <div class="agent_content row">
        <h4><?php esc_html_e( 'About Me', 'wpresidence' ); ?></h4>    
        <div>
        <?php 
        // Display the content of the agent's profile
        $content = get_the_content();
      
        if (!empty($content)) {
          echo apply_filters('the_content', $content);
        } 
        ?>
        </div>
    </div>
    <?php
}
?>