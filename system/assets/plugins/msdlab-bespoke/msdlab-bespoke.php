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
        require_once(plugin_dir_path(__FILE__) . 'lib/inc/news_cpt.php');
        if(class_exists('MSDNewsCPT')){
            $this->news_class = new MSDNewsCPT();
        }
        require_once(plugin_dir_path(__FILE__) . 'lib/inc/team_cpt.php');
        if(class_exists('MSDTeamCPT')){
            $this->team_class = new MSDTeamCPT();
        }
        require_once(plugin_dir_path(__FILE__) . 'lib/inc/sectioned-pages.php');
        if(class_exists('MSDSectionedPage')){
            $this->section_class = new MSDSectionedPage();
        }


        register_activation_hook( __FILE__, create_function('','flush_rewrite_rules();') );
        register_deactivation_hook( __FILE__, create_function('','flush_rewrite_rules();') );
    }

}
//instantiate
$msd_custom = new MSDLabClientCustom();