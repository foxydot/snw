<?php
/*
 *  shortcodes
 */
add_shortcode('latest','msdlab_latest_shortcode_handler');

function msdlab_latest_shortcode_handler($atts){
    $args = (shortcode_atts( array(
        'post_type' => 'post',
        'posts_per_page' => '1',
    ), $atts ));
    global $post;
    $my_query = new WP_Query($args);
    ob_start();
    while ( $my_query->have_posts() ) : $my_query->the_post();
        print '<article>';
        printf( '<a href="%s" title="%s" class="latest_image_wrapper alignleft">%s</a>', get_permalink(), the_title_attribute('echo=0'), genesis_get_image(array('size' => 'thumbnail')) );
        print '<div>';
        printf( '<a href="%s" title="%s" class="latest-title"><h3>%s</h3></a>', get_permalink(), the_title_attribute('echo=0'), get_the_title() );
        genesis_post_info();
        print '</div>';
        print '</article>';
    endwhile;
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}