<?php
if (!class_exists('MSDCustomPages')) {
class MSDCustomPages {
//Properites

//Methods

/**
* PHP 5 Constructor
*/
function __construct(){
//check requirements
register_activation_hook(__FILE__, array(&$this,'check_requirements'));
//get sub-packages
    require_once(plugin_dir_path(__FILE__) . '/page-sectioned-functions.php');
    if(class_exists('MSDSectionedPage')){
        add_action('admin_print_footer_scripts',array('MSDSectionedPage','info_footer_hook') ,100);
        add_action('admin_enqueue_scripts',array('MSDSectionedPage','enqueue_admin'));
        add_action( 'init', array( 'MSDSectionedPage', 'add_metaboxes' ) );
    }
}
/**
* @desc Check to see if requirements are met
*/
function check_requirements(){

}
/***************************/
} //End Class
} //End if class exists statement

//instantiate
$msd_custom_pages = new MSDCustomPages();