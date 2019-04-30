<?php
if(!class_exists('WPAlchemy_MetaBox')){
    include_once (WP_CONTENT_DIR.'/wpalchemy/MetaBox.php');
}

global $wpalchemy_media_access;
if(!class_exists('WPAlchemy_MediaAccess')){
    include_once (WP_CONTENT_DIR.'/wpalchemy/MediaAccess.php');
}
$wpalchemy_media_access = new WPAlchemy_MediaAccess();

if (!class_exists('MSDLab_Sidebar_Content_Support')) {
    class MSDLab_Sidebar_Content_Support {
        //Properties
        private $options;

        //Methods
        function __construct(){
            global $current_screen;
            //"Constants" setup
            //Actions
            add_action( 'init', array(&$this,'register_metaboxes') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'footer_hook') );
            add_action('genesis_header',array(&$this,'maybe_remove_sidebar_widgets') );
            add_action('genesis_sidebar',array(&$this,'msdlab_do_sidebar_content') );

            //Filters
        }

        function register_metaboxes(){
            global $sidebar_content_metabox;
            $sidebar_content_metabox = new WPAlchemy_MetaBox(array
            (
                'id' => '_sidebar_content',
                'title' => 'Sidebar Content Area',
                'types' => array('page'),
                'context' => 'normal', // same as above, defaults to "normal"
                'priority' => 'high', // same as above, defaults to "high"
                'template' => get_stylesheet_directory().'/lib/template/metabox-sidebar_content.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_msdlab_' // defaults to NULL
            ));
        }

        function add_admin_styles() {
            //wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'css/meta.css');
        }

        function footer_hook()
        {
            ?><script type="text/javascript">
            jQuery('#titlediv').after(jQuery('#_sidebar_content_metabox'));
        </script><?php
        }

        function maybe_remove_sidebar_widgets(){
            if($this->has_sidebar_content()){
                remove_all_actions('genesis_sidebar');
                add_action('genesis_sidebar',array(&$this,'msdlab_do_sidebar_content') );
            }
        }

        function has_sidebar_content(){
            $ret = 'false';
            if(is_page()){
                global $post, $sidebar_content_metabox;
                $sidebar_content_metabox->the_meta();
                $sidebarbool = $sidebar_content_metabox->get_the_value('sidebarbool');
                if($sidebarbool == 'true'){
                    $ret = true;
                }
            }
            return $ret;
        }

        function msdlab_do_sidebar_content(){
            if(is_page()){
                global $post, $sidebar_content_metabox;
                $sidebar_content_metabox->the_meta();
                $sidebarbool = $sidebar_content_metabox->get_the_value('sidebarbool');
                if($sidebarbool != 'true'){
                    return;
                }
                $sidebarclass = $sidebar_content_metabox->get_the_value('sidebarclass');
                $sidebarintro = apply_filters('the_content',$sidebar_content_metabox->get_the_value('sidebarintro'));
                $sidebarcontent = apply_filters('the_content',$sidebar_content_metabox->get_the_value('sidebarcontent'));
                global $post;
                if(count($sidebarintro > 0)){
                    $sidebarintro = sprintf('<div class="sidebarintro">%s</div>',$sidebarintro);
                }
                if(count($sidebarcontent > 0)){
                    $sidebarcontent = sprintf('<div class="sidebarcontent">%s</div>',$sidebarcontent);
                }
                print '<div class="manual-sidebar '.$sidebarclass.'">'.$sidebarintro.$sidebarcontent.'</div>';
            }
        }

    } //End Class
} //End if class exists statement