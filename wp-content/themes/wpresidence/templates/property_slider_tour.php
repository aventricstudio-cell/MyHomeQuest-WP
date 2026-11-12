<?php
global $post;
?>
<div class="container_tour">
<?php
if( get_post_meta( $post->ID, 'embed_virtual_tour', true ) != '' ){
    $virtual_tour= get_post_meta( $post->ID, 'embed_virtual_tour', true );
    $virtual_tour = do_shortcode( $virtual_tour );
    echo $virtual_tour;
}
?>
</div>
 