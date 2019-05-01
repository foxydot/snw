<?php
/*
Plugin Name: MSDLab Bespoke Client Functions
Description: Custom functions for this site.
Version: 0.1
Author: MSDLab
Author URI: http://msdlab.com/
License: GPL v2
*/

if(!class_exists('WPAlchemy_MetaBox')){
    if(!include_once (WP_CONTENT_DIR.'/wpalchemy/MetaBox.php'))
    include_once (plugin_dir_path(__FILE__).'/lib/wpalchemy/MetaBox.php');
}
global $wpalchemy_media_access;
if(!class_exists('WPAlchemy_MediaAccess')){
    if(!include_once (WP_CONTENT_DIR.'/wpalchemy/MediaAccess.php'))
    include_once (plugin_dir_path(__FILE__).'/lib/wpalchemy/MediaAccess.php');
}
$wpalchemy_media_access = new WPAlchemy_MediaAccess();
global $msd_custom;

class MSDLabClientCustom
{
    private $ver;

    function MSDLabClientCustom()
    {
        $this->__construct();
    }

    /**
     * MSDLabClientCustom constructor.
     */
    function __construct()
    {
        $this->ver = '0.1';
        /*
         * Pull in and define supports
         */
        require_once(plugin_dir_path(__FILE__) . 'lib/inc/_media.php');
        require_once(plugin_dir_path(__FILE__) . 'lib/inc/_shortcodes.php');
        require_once(plugin_dir_path(__FILE__) . 'lib/inc/_utility.php');
	    require_once(plugin_dir_path(__FILE__) . 'lib/inc/_widgets.php');

        require_once(plugin_dir_path(__FILE__) . 'lib/inc/sectioned-pages.php');
        if(class_exists('MSDSectionedPage')){
            $this->section_class = new MSDSectionedPage();
        }
	    add_action('admin_menu', array(&$this,'move_admin_menu_items'));
        }

	function move_admin_menu_items() {
		global $menu;
		foreach ( $menu as $key => $value ) {
			if ( 'edit-comments.php' == $value[2] ) {
				$oldkey = $key;
			}
			if ( 'edit.php?post_type=cookielawinfo' == $value[2] ) {
				$oldkey = $key;
			}
		}
		$newkey = 50; // use whatever index gets you the position you want
		// if this key is in use you will write over a menu item!
		$menu[$newkey]=$menu[$oldkey];
		$menu[$oldkey]=array();
	}
}
//instantiate
$msd_custom = new MSDLabClientCustom();