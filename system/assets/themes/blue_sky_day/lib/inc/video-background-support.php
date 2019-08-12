<?php
if (!class_exists('MSDLab_Video_Background_Support')) {
    class MSDLab_Video_Background_Support
    {
        //Properties
        private $options;

        //Methods
        function __construct($options)
        {
            global $current_screen;
            //"Constants" setup
            //Actions
            add_action('genesis_before_header', array(&$this, 'do_video_background'),10);
            add_action( 'customize_register', array(&$this,'video_src_customize_register') );

            //Filters
        }

        function video_src_customize_register( $wp_customize ) {
            //All our sections, settings, and controls will be added here
            $wp_customize->add_setting( 'video_src' , array(
                'default'   => '',
                'transport' => 'refresh',
            ) );
            $wp_customize->add_section( 'video_src_section' , array(
                'title'      => __( 'Background', 'genesis' ),
                'priority'   => 30,
            ) );
            $wp_customize->add_control( new WP_Customize_Background_Image_Control( $wp_customize, 'video_src', array(
                'label'      => __( 'Video Source', 'genesis' ),
                'section'    => 'video_src_section',
                'settings'   => 'video_src',
            ) ) );
        }


        function do_video_background(){
            if(is_admin()){return;}
            if(wp_is_mobile()){return;}
            $videosrc = get_stylesheet_directory_uri().'/lib/images/Day-Loop-Final.mp4';
            print '<!-- The video -->
<video autoplay muted loop id="bkgVideo">
  <source src="'.$videosrc.'" type="video/mp4">
</video>
<style>
#bkgVideo {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    min-width: 120%; 
    min-height: 120%;
    z-index: -1000;
    background-color: rgb(50, 95, 165);
}
$bkgVideo source{
        position: absolute;
        top:0;
        height: 100%;
    }

</style>';
        }
    }
}