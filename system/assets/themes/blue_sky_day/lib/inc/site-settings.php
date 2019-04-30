<?php
class CustomAddress extends MSDAddress  {

function widget( $args, $instance ) {
    extract($args);
    $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
    echo $before_widget;
    if ( !empty( $title ) ) { print $before_title.$title.$after_title; }
    ?>
    <div id="address" class="address">
        <?php print (get_option('msdsocial_mailing_street')!='' && get_option('msdsocial_mailing_city')!='' && get_option('msdsocial_mailing_state')!='')?'<strong>MAILING ADDRESS:</strong> '.get_option('msdsocial_mailing_street').' '.get_option('msdsocial_mailing_street2').' | '.get_option('msdsocial_mailing_city').', '.get_option('msdsocial_mailing_state').' '.get_option('msdsocial_mailing_zip').'<br /> ':''; ?>
        <?php print (get_option('msdsocial_street')!='' && get_option('msdsocial_city')!='' && get_option('msdsocial_state')!='')?get_option('msdsocial_street').' '.get_option('msdsocial_city').', '.get_option('msdsocial_state').' '.get_option('msdsocial_zip').'<br /> ':''; ?>
    </div>
    <?php
    echo $after_widget;
}

}

add_action('widgets_init', create_function('', 'return register_widget("CustomAddress");'));


class CustomVisit extends MSDVisit {
    /** constructor */

    function widget( $args, $instance ) {
        extract($args);
        global $msd_social;
        $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
        $text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
        echo $before_widget;
        if ( !empty( $title ) ) { print $before_title.$title.$after_title; }
        echo '<div class="business-hours">'.$msd_social->get_hours().'</div>';
        if ( !empty( $text )){ print '<div class="business-hours-text">'.$text.'</div>'; }
        echo $after_widget;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("CustomVisit");'));