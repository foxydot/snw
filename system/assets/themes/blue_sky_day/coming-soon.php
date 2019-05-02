<?php
/*
Template Name: Coming Soon
*/

//force full-width
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
remove_action('genesis_header','msdlab_do_nav');

genesis();