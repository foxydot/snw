<?php
if(!class_exists('WPAlchemy_MetaBox')){
    include_once (WP_CONTENT_DIR.'/wpalchemy/MetaBox.php');
}

global $wpalchemy_media_access;
if(!class_exists('WPAlchemy_MediaAccess')){
    include_once (WP_CONTENT_DIR.'/wpalchemy/MediaAccess.php');
}
$wpalchemy_media_access = new WPAlchemy_MediaAccess();

if (!class_exists('MSDLab_Custom_Class_Support')) {
    class MSDLab_Custom_Class_Support {
        //Properties
        private $options;

        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDLab_Custom_Class_Support(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct(){
            global $current_screen;
            //"Constants" setup
            //Actions
            add_action( 'init', array(&$this,'register_metaboxes') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'footer_hook') );

            //Filters
            add_filter('body_class',array(&$this,'do_custom_body_classes'));

        }

        function register_metaboxes(){
            global $custom_class_metabox;
            $custom_class_metabox = new WPAlchemy_MetaBox(array
            (
                'id' => '_custom_class',
                'title' => 'Body Classes',
                'types' => array('page'),
                'context' => 'side', // same as above, defaults to "normal"
                'priority' => 'high', // same as above, defaults to "high"
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-custom_class.php',
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
            jQuery('#pageparentdiv').after(jQuery('#_custom_class_metabox'));
        </script><?php
        }

        function do_custom_body_classes($classes)
        {
            global $post;
            if (is_page()) {

                global $custom_class_metabox;
                $custom_class_metabox->the_meta();
                $new_classes = explode(' ',$custom_class_metabox->get_the_value('bodyclass'));
                $classes = array_merge($classes,$new_classes);
            }
            return $classes;
        }

    } //End Class
} //End if class exists statement