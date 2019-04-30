<?php
/*
Plugin Name: MSDLab Coming Soon
Description: Select a coming soon page for anonymous users.
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
global $msd_coming_soon;

class MSDLabComingSoon
{
    private $ver;

    /**
     * MSDLabClientCustom constructor.
     */
    function __construct()
    {
        $this->ver = '0.1';

        require_once(plugin_dir_path(__FILE__).'/lib/inc/_form_elements.php');
        if(class_exists('MSDLAB_FormControls')){
            $this->form = new MSDLAB_FormControls();
        }
        require_once(plugin_dir_path(__FILE__).'/lib/inc/_queries.php');
        if(class_exists('MSDLAB_Queries')){
            $this->queries = new MSDLAB_Queries();
        }
        add_action('admin_menu', array(&$this,'settings_page'));
        add_action('template_redirect', array(&$this,'redirect_guests'));

    }

    /*
     * Settings page for coming soon
     */
    function settings_page(){
        add_submenu_page('options-general.php',__('Coming Soon'),__('Coming Soon Settings'),'administrator','options-coming-soon', array(&$this,'settings_page_content'));
    }



    function settings_page_content(){
        $form_id = 'coming_soon_settings_form';
        $msg = $this->queries->set_option_data($form_id);
        if(is_string($msg)){
            print '<div class="updated notice notice-success is-dismissible">'.$msg.'</div>';
        } else {
            ts_data($msg);
        }
        $ret = array();
        $ret['header'] = $this->form->form_header($form_id);
        $ret['coming_soon_page'] = $this->form->field_pageselect("Select Coming Soon Page","coming_soon_page");
        $ret['button'] = $this->form->field_button('save_coming_soon');
        $ret['nonce'] = wp_nonce_field( $form_id );
        $ret['footer'] = $this->form->form_footer($form_id,'');

        print implode("\n",$ret);
    }

    /*
     * Redirect for coming soon
     */
    function redirect_guests(){
        global $post;
        $coming_soon_page = get_option('coming_soon_page');
        if(!is_user_logged_in() && $post->ID != $coming_soon_page){
            wp_redirect( get_permalink($coming_soon_page), 302 );
            exit;
        }
    }

}
//instantiate
$msd_coming_soon = new MSDLabComingSoon();