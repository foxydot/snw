<?php
class MSDSectionedPage{
        /**
         * A reference to an instance of this class.
         */
        private static $instance;


        /**
         * Returns an instance of this class. 
         */
        public static function get_instance() {

                if( null == self::$instance ) {
                        self::$instance = new MSDSectionedPage();
                } 

                return self::$instance;

        } 
        
        /**
         * Initializes the plugin by setting filters and administration functions.
         */
   function __construct() {
       add_action('wp_enqueue_scripts', array(&$this,'enqueue_scripts'));
       add_action('genesis_after_content_sidebar_wrap',array(&$this,'sectioned_page_output'),30);
       add_action('wp_print_footer_scripts',array(&$this,'sectioned_page_footer_js'));
       add_filter( 'wp_post_revision_meta_keys', array(&$this,'add_meta_keys_to_revision') );

   }
        
    function add_metaboxes(){
        global $post,$sectioned_page_metabox,$wpalchemy_media_access;
        $sectioned_page_metabox = new WPAlchemy_MetaBox(array
        (
            'id' => '_sectioned_page',
            'title' => 'Page Sections',
            'types' => array('page'),
            'context' => 'normal', // same as above, defaults to "normal"
            'priority' => 'high', // same as above, defaults to "high"
            'template' => plugin_dir_path(__DIR__).'template/metabox-sectioned-page.php',
            'autosave' => TRUE,
            'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
            'prefix' => '_msdlab_', // defaults to NULL
            //'include_template' => 'sectioned-page.php',
        ));
    }

    function default_output($section,$i){
        //ts_data($section);
        global $parallax_ids;
        $eo = ($i+1)%2==0?'even':'odd';
        $title = apply_filters('the_title',$section['content-area-title']);
        $section_name = $section['section-name']!=''?$section['section-name']:$title;
        $slug = sanitize_title_with_dashes(str_replace('/', '-', $section_name));
        $background = '';
        if($section['background-color'] || $section['background-image']){
            if($section['background-color'] && $section['background-image']){
                $background = 'style="background-image: url('.$section['background-image'].');background-color: '.$section['background-color'].';"';
            } elseif($section['background-image']){
                $background = 'style="background-image: url('.$section['background-image'].');"';
            } else{
                $background = 'style="background-color: '.$section['background-color'].';"';
            }
            if($section['background-image'] && $section['background-image-parallax']){
                $parallax_ids[] = $slug;
            }
        }
        $subtitle = $section['content-area-subtitle'] !=''?apply_filters('msdlab_sectioned_page_output_subtitle','<h4 class="section-subtitle">'.$section['content-area-subtitle'].'</h4>'):'';
        $wrapped_title = trim($title) != ''?apply_filters('msdlab_sectioned_page_output_title','<div class="section-title">
            <h3>
                '.$title.'
            </h3>
            '.$subtitle.'                
        </div>'):'';
        $header = apply_filters('the_content',$section['header-area-content']);
        $content = apply_filters('the_content',$section['content-area-content']);
        $footer = apply_filters('the_content',$section['footer-area-content']);
        $float = $section['feature-image-float']!='none'?' class="align'.$section['feature-image-float'].'"':'';
        $featured_image = $section['content-area-image'] !=''?'<img src="'.$section['content-area-image'].'"'.$float.' />':'';
        $section_classes = isset($section['css-classes'])?implode(" ",$section['css-classes']):'';
        $classes = apply_filters('msdlab_sectioned_page_output_classes',array(
            'section',
            'section-'.$slug,
            $section_classes,
	        $section['custom-css-classes'],
	        'one-col',
            'section-'.$eo,
            'clearfix',
        ));
        //think about filtering the classes here
        $ret = '
        <div id="'.$slug.'" class="'.implode(' ', $classes).'"'.$background.'>
        
                <div class="container">
                '.$wrapped_title.'
            <div class="section-body">
                    '.$featured_image.'
                    '.$header.'
                    <div class="row column-holder">'.$content.'</div>
                    '.$footer.'
                </div>
            </div>
        </div>
        ';
        return $ret;
    }


    function column_output($section,$i){
        //ts_data($section);
        global $parallax_ids;
        $eo = ($i+1)%2==0?'even':'odd';
        $breakpoint = apply_filters('msdlab_column_breakpoint', 'sm');
        switch($breakpoint){
            case 'md':
                $next = 'lg';
                break;
            case 'sm':
                $next = 'md';
                break;
            case 'xs':
                $next = 'sm';
                break;
            default:
                $next = 'md';
                break;
        }
        $title = apply_filters('the_title',$section['content-area-title']);
        $section_name = $section['section-name']!=''?$section['section-name']:$title;
        $slug = sanitize_title_with_dashes(str_replace('/', '-', $section_name));
        $background = '';
        if($section['background-color'] || $section['background-image']){
            if($section['background-color'] && $section['background-image']){
                $background = 'style="background-image: url('.$section['background-image'].');background-color: '.$section['background-color'].';"';
            } elseif($section['background-image']){
                $background = 'style="background-image: url('.$section['background-image'].');"';
            } else{
                $background = 'style="background-color: '.$section['background-color'].';"';
            }
            if($section['background-image'] && $section['background-image-parallax']){
                $parallax_ids[] = $slug;
            }
        }
        $subtitle = $section['content-area-subtitle'] !=''?apply_filters('msdlab_sectioned_page_output_subtitle','<h4 class="section-subtitle wrap container">'.$section['content-area-subtitle'].'</h4>'):'';
        $wrapped_title = trim($title) != ''?apply_filters('msdlab_sectioned_page_output_title','<div class="section-title">
            <h3 class="container">
                '.$title.'
            </h3>
            '.$subtitle.'                
        </div>'):'';
        $header = $section['header-area-bool']?'<div class="section-header-area">'.apply_filters('the_content',$section['header-area-content']).'</div>':'';
        if(!is_numeric($section['content-area-width'])) {
	        $content = '<div class="section-content column-1 ' . $section['content-area-width'] . '">' . apply_filters( 'the_content', $section['content-area-content'] ) . '</div>';
        } else {
	        $content = '<div class="section-content column-1 col-' . $breakpoint . '-12 col-' . $next . '-' . $section['content-area-width'] . '">' . apply_filters( 'the_content', $section['content-area-content'] ) . '</div>';
        }
	    if(!is_numeric($section['column-2-area-width'])) {
		    $content2 = '<div class="section-content column-2 ' . $section['column-2-area-width'] . '">' . apply_filters( 'the_content', $section['column-2-area-content'] ) . '</div>';
	    } else {
		    $content2 = '<div class="section-content column-2 col-' . $breakpoint . '-12 col-' . $next . '-' . $section['column-2-area-width'] . '">' . apply_filters( 'the_content', $section['column-2-area-content'] ) . '</div>';
	    }
	    if(!is_numeric($section['column-3-area-width'])) {
		    $content3 = '<div class="section-content column-3 ' . $section['column-3-area-width'] . '">' . apply_filters( 'the_content', $section['column-3-area-content'] ) . '</div>';
	    } else {
		    $content3 = '<div class="section-content column-3 col-' . $breakpoint . '-12 col-' . $next . '-' . $section['column-3-area-width'] . '">' . apply_filters( 'the_content', $section['column-3-area-content'] ) . '</div>';
	    }
	    if(!is_numeric($section['column-4-area-width'])) {
		    $content4 = '<div class="section-content column-4 ' . $section['column-4-area-width'] . '">' . apply_filters( 'the_content', $section['column-4-area-content'] ) . '</div>';
	    } else {
		    $content4 = '<div class="section-content column-4 col-' . $breakpoint . '-12 col-' . $next . '-' . $section['column-4-area-width'] . '">' . apply_filters( 'the_content', $section['column-4-area-content'] ) . '</div>';
	    }
	   $footer = $section['footer-area-bool']?'<div class="section-footer-area">'.apply_filters('the_content',$section['footer-area-content']).'</div>':'';
        $float = $section['feature-image-float']!='none'?' class="align'.$section['feature-image-float'].'"':'';
        $featured_image = $section['content-area-image'] !=''?'<img src="'.$section['content-area-image'].'"'.$float.' />':'';
        $classes = apply_filters('msdlab_sectioned_page_output_classes',array(
            'section',
            'section-'.$slug,
            implode(" ",$section['css-classes']),
	        $section['custom-css-classes'],
	        $section['layout'],
            'section-'.$eo,
            'clearfix'
        ));
        switch($section['layout']){
            case 'four-col':
                $central_content = $content4;
            case 'three-col':
                $central_content = $content3.$central_content;
            case 'two-col':
                $central_content = $content2.$central_content;
            default:
                $central_content = $content.$central_content;
                break;
        }

        $central_content = '<div class="row column-holder">'.$central_content.'</div>';
        //think about filtering the classes here
        $ret = '
        <div id="'.$slug.'" class="'.implode(' ', $classes).'"'.$background.'>
        
                '.$wrapped_title.'
            <div class="section-body">
                <div class="container">
                    '.$featured_image.'
                    '.$header.'
                    '.$central_content.'
                    '.$footer.'
                </div>
            </div>
        </div>
        ';
        return $ret;
    }

    function sectioned_page_output(){
        wp_enqueue_script('sticky',plugin_dir_url(__FILE__). '../js/jquery.sticky.js',array('jquery'),FALSE,TRUE);
        global $post,$sectioned_page_metabox;
        $i = 0;
        $meta = $sectioned_page_metabox->the_meta();
        if(is_object($sectioned_page_metabox)){
            while($sectioned_page_metabox->have_fields('sections')){
                $hide = $sectioned_page_metabox->get_the_value('section-hidden-bool');
                if($hide){$i++;continue;}
                $layout = $sectioned_page_metabox->get_the_value('layout');
                switch($layout){
                    case "four-col":
                        $sections[] = self::column_output($meta['sections'][$i],$i);
                        break;
                    case "three-col":
                        $sections[] = self::column_output($meta['sections'][$i],$i);
                        break;
                    case "two-col":
                        $sections[] = self::column_output($meta['sections'][$i],$i);
                        break;
                    default:
                        $sections[] = self::default_output($meta['sections'][$i],$i);
                        break;
                }
                $i++;
            }//close while
            if(is_array($sections)) {
                print '<div class="sectioned-page-wrapper">';
                print implode("\n", $sections);
                print '</div>';
            } //close if
        }//clsoe if
    }

    function sectioned_page_footer_js(){
        global $nav_ids,$parallax_ids; //http://julian.com/research/velocity/ llook at this to speed up animations
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
        //do some little stuff for parralaxing
        // init controller
        var section_controller = new ScrollMagic({globalSceneOptions: {triggerHook: "onEnter", duration: $(window).height()*4}});
    
        // build scenes
        <?php
            $i = 0;
            if($parallax_ids):
            foreach($parallax_ids AS $p_id):
        ?>
        new ScrollScene({options:{triggerElement:"#<?php print $p_id; ?>"}})
            .setTween(TweenMax.fromTo("#<?php print $p_id; ?>", 1, {css:{'background-position':"50% 100%"}, ease: Linear.easeNone}, {css:{'background-position':"50% 0%"}, ease: Linear.easeNone}))
            .addTo(section_controller);
        <?php 
            $i++;
            endforeach;
            endif;
        ?>
            $("#floating_nav").sticky({ topSpacing: 0 });
        });
        </script>
        <?php
    }
        function info_footer_hook()
        {
            $postid = is_admin()?$_GET['post']:$post->ID;
            $template_file = get_post_meta($postid,'_wp_page_template',TRUE);
            if($template_file == 'page-sectioned.php'){
            ?><script type="text/javascript">
                
                </script><?php
            }
        }

        function enqueue_scripts(){
            wp_deregister_script('greensock');
            wp_enqueue_script('tweenlite',plugin_dir_url(__DIR__). 'js/greensock/TweenLite.min.js');
            wp_enqueue_script('tweenmax',plugin_dir_url(__DIR__). 'js//greensock/TweenMax.min.js');
            wp_enqueue_script('timelinelite',plugin_dir_url(__DIR__). 'js//greensock/TimelineLite.min.js');
            wp_enqueue_script('greensock-easepack',plugin_dir_url(__DIR__). 'js/greensock/easing/EasePack.min.js');
            wp_enqueue_script('greensock-css',plugin_dir_url(__DIR__). 'js/greensock/plugins/CSSPlugin.min.js');
            wp_enqueue_script('tweenmax-jquery',plugin_dir_url(__DIR__). 'js/greensock/jquery.gsap.min.js',array('jquery','tweenmax'));
            wp_enqueue_script('scroll-magic',plugin_dir_url(__DIR__). 'js/jquery.scrollmagic.min.js',array('jquery','tweenmax'));
        }
        
        function enqueue_admin(){
                //js
                wp_enqueue_script('spectrum',plugin_dir_url(__DIR__). 'js/spectrum.js',array('jquery'));
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('jquery-ui-accordion');
                wp_enqueue_script('sectioned-admin',plugin_dir_url(__DIR__). 'js/sectioned-input.js',array('jquery','jquery-ui-sortable','jquery-ui-accordion'));
             //css
                wp_enqueue_style('spectrum',plugin_dir_url(__DIR__). 'css/spectrum.css');
                wp_enqueue_style('sectioned-admin',plugin_dir_url(__DIR__). 'css/sectioned.css');
        }

    function add_meta_keys_to_revision( $keys ) {
        $keys[] = '_msdlab_sections';
        $keys[] = '_sectioned_page_fields';
        return $keys;
    }
}
