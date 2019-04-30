<?php
class MSDLAB_Queries{

    /**
     * A reference to an instance of this class.
     */
    private static $instance;
    private $post_vars;


    public function __construct() {
        global $wpdb;
        if ( ! empty( $_POST ) ) { //add nonce
            $this->post_vars = $_POST;
        }
    }

    /**
     * Returns an instance of this class.
     */
    public static function get_instance() {

        if( null == self::$instance ) {
            self::$instance = new MSDLAB_Queries();
        }

        return self::$instance;

    }



    /*
     * Setting Queries
     */

    /**
     * Save any updated data
     *
     * @return true on success, error message on failure.
     */
    public function set_option_data($form_id){
        if(empty($this->post_vars)){
            return false;
        }
        $nonce = $_POST['_wpnonce'];
        if(!wp_verify_nonce( $nonce, $form_id )) {
            return new WP_Error( 'nononce', __( '<div class="message error">Invalid entry</div>', "msdlab" ) );
        }
        foreach ($this->post_vars AS $k => $v){
            if(stripos($k,'_input')){
                $option = str_replace('_input','',$k);
                $orig = get_option($option);
                if($v !== $orig) {
                    if (!update_option($option, $v)) {
                        return new WP_Error( 'update', __( '<div class="message error">Error updating ' . $option .'</div>', "msdlab" ) );
                    }
                }
            }
        }
        return '<div class="message success">Data Updated</div>';
    }

    /*
     * END Setting Queries
     */

    /**
     * Getting Queries
     */



    /**
     * Form population queries
     */
    function get_select_array_from_db($table,$id_field,$field,$orderby = false,$publishflag = 0){
        global $wpdb;
        $where = '';
        if(!$orderby){
            $orderby = $id_field;
        }
        if($publishflag){
            $where = ' WHERE '.strtolower($table).'.Publish = 1 ';
        }
        $sql = 'SELECT `'.$id_field.'`,`'.$field.'` FROM `'.strtolower($table).'` '.$where.'ORDER BY `'.$orderby.'` ASC;';
        $result = $wpdb->get_results( $sql, ARRAY_A );
        foreach ($result AS $k=>$v){
            $array[$v[$id_field]] = $v[$field];
        }
        return $array;
    }



    /*
    *  Resource Queries
    */

    function get_next_id($table,$id_field){
        global $wpdb;
        $sql = 'SELECT '.$id_field.' FROM '.$table.' ORDER BY '.$id_field.' DESC LIMIT 1';
        $results = $wpdb->get_results($sql);
        return $results[0]->{$id_field}+1;
    }

}