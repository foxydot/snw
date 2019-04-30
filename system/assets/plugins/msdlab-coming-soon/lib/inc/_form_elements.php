<?php
class MSDLAB_FormControls{

    /**
     * A reference to an instance of this class.
     */
    private static $instance;
    public $javascript;

    public function __construct() {

        add_action( 'wp_ajax_remove_pdf', array(&$this,'delete_file') );
        add_action( 'wp_ajax_nopriv_remove_pdf', array(&$this,'delete_file') );

        if(class_exists('MSDLAB_Queries')){
            $this->queries = new MSDLAB_Queries();
        }
    }

    /**
     * Returns an instance of this class.
     */
    public static function get_instance() {

        if( null == self::$instance ) {
            self::$instance = new MSDLAB_FormControls();
        }

        return self::$instance;

    }

    public function form_header($id = "csf_form", $class = array()){
        $class = implode(" ",apply_filters('msdlab_'.$id.'_header_class', $class));
        $ret = '<form id="'.$id.'" class="'.$class.'" method="post" enctype="multipart/form-data">';
        return apply_filters('msdlab_'.$id.'_header', $ret);
    }

    public function form_close(){
        $ret = '</form>';
        return apply_filters('msdlab_form__manage_form_footer', $ret);
    }

    public function form_footer($id, $content, $class = array()){
        $class = implode(" ",apply_filters('msdlab_'.$id.'_footer_class', $class));
        $ret = '<div id="'.$id.'" class="'.$class.'">'.$content.'</div>';
        return apply_filters('msdlab_'.$id.'_footer', $ret);
    }


    public function build_jquery($id,$jquery){
        $ret = '
        <script>
  jQuery(function($){
    '.implode("\n\r",apply_filters('msdlab_'.$id.'_javascript', $jquery)).'
  });
  </script>';
        return $ret;
    }

    //FIELD LOGIC

    //TODO: Refactor for redundancies

    public function section_header($id, $value = null, $class = array('section-header')){
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div class="row"><h3 id="'.$id.'_wrapper" class="col-sm-12 '.$class.'">'.$value.'</h3></div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_utility($id, $value = null, $title = "", $validation = null, $class = array('hidden')){
        if(is_null($value)){
            $value = $_POST[$id];
        }
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<input id="'.$id.'" name="'.$id.'" type="hidden" value="'.$value.'" />');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_hidden($id, $value = null, $title = "", $validation = null, $class = array('hidden')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<input id="'.$id.'_input" name="'.$id.'_input" type="hidden" value="'.$value.'" />');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_boolean($id, $value = 0, $title = "", $validation = null, $class = array('bool'), $settings = array()){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $default_settings = array(
            'true' => 'YES',
            'false' => 'NO'
        );
        $settings = array_merge($default_settings,$settings);
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        $bkp_field = apply_filters('msdlab_form__'.$id.'_bkp_field','<input id="'.$id.'_input" name="'.$id.'_input" type="hidden" value="0" />');
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<div class="ui-toggle-btn">
        <input id="'.$id.'_input" name="'.$id.'_input" type="checkbox" value="1"'.checked($value,1,false).' '.$this->build_validation($validation).' />
        <div class="handle" data-on="'.$settings['true'].'" data-off="'.$settings['false'].'"></div></div>');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$bkp_field.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function build_validation($validation_array){
        if(empty($validation_array)){return;}
        foreach($validation_array AS $k => $v){
            $validation_str[] = $k . ' = "' . $v .'"';
        }
        if($validation_str)
            return implode(' ',$validation_str);
    }

    public function field_date($id, $value = null, $title = "Date", $validation = null, $class = array('datepicker')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<input id="'.$id.'_input" name="'.$id.'_input" type="date" value="'.$value.'" placeholder="'.$title.'" '.$this->build_validation($validation).' />');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_textinfo($id, $value, $title = "", $placeholder = null, $validation = null, $class = array('medium')){
        if(is_null($value)){
            $value = 'n/a';
        }
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_info">'.$title.'</label>');
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<span class="symbol"><span id="'.$id.'_info" name="'.$id.'_info" class="info">'.$value.'</span></span>');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_textfield($id, $value, $title = "", $placeholder = null, $validation = null, $class = array('medium')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $type = isset($validation['type'])?$validation['type']:'text';
        if($placeholder == null){$placeholder = $title;}
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<span class="symbol"><input id="'.$id.'_input" name="'.$id.'_input" type="'.$type.'" value="'.$value.'" placeholder="'.$placeholder.'" '.$this->build_validation($validation).' /></span>');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_texteditor($id, $value = null, $title = "", $validation = null, $class = array('textarea')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input" class="textarea-label">'.$title.'</label>');
        ob_start();
        wp_editor( stripcslashes($value), $id.'_input', array('media_buttons' => false,'teeny' => true,'textarea_rows' => 5,) );
        $form_field = ob_get_clean();
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }


    public function field_textarea($id, $value = null, $title = "", $validation = null, $class = array('textarea')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        $form_field = '<textarea id="'.$id.'_input" name="'.$id.'_input" '.$this->build_validation($validation).'>'.stripcslashes($value).'</textarea>';
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_select($id, $value = null, $title = "", $null_option = null, $options = array(), $validation = null, $class = array('select')){
        if(is_null($value)  || empty($value)){
            $value = $_POST[$id.'_input'];
        }
        if($null_option == null){$null_option = 'Select';}
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        //iterate through $options
        $options_str = implode("\n\r",$this->build_options($options,$value,$null_option));
        $select = '<select id="'.$id.'_input" name="'.$id.'_input" '.$this->build_validation($validation).'>'.$options_str.'</select>';
        $form_field = apply_filters('msdlab_form__'.$id.'_field', $select );
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_pageselect($title = "Select Page",$id = "pageselect", $class = array('select'), $value = null){
        if(is_null($value)  || empty($value)){
            $value = $_POST[$id.'_input'];
        }
        $label = apply_filters('msdlab_form_'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        $options = array();
        //iterate through available pages
        $form_field = apply_filters('msdlab_form__'.$id.'_field',wp_dropdown_pages( array( 'name' => $id.'_input', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => get_option( $id ) ) ) );
        $class = implode(" ",apply_filters('msdlab_form__'.$id.'_class', $class));
        $ret = '<div id="'.$id.'" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function build_options($options,$value,$null_option){
        $ret = array();
        $cur = $options[$value];
        $options = array_unique($options);
        if(!empty($cur)) {
            $options[$value] = $cur;
        }
        if(is_array($null_option)){
            $ret[] = '<option value="'.$null_option['value'].'">'.$null_option['option'].'</option>';
        } else {
            $ret[] = '<option>'.$null_option.'</option>';
        }
        foreach ($options AS $k => $v){
            $ret[] = '<option value="'.$k.'"'.selected($value,$k,false).'>'.$v.'</option>';
        }
        return $ret;
    }

    public function field_radio($id, $value = null, $title = "", $options = array(), $validation = null, $class = array('radio')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        //iterate through $options
        foreach ($options AS $k => $v){
            $options_array[] = '<div class="'.$id.'_'.$k.'_wrapper option-wrapper"><input id="'.$id.'_'.$k.'_input" name="'.$id.'_input" type="radio" value="'.$k.'"'.checked($value,$k,false).' /> <label class="option-label">'.$v.'</label></div>';
        }

        $options_str = '<div class="radio-wrapper">'.implode("\n\r",$options_array).'</div>';
        $form_field = apply_filters('msdlab_form__'.$id.'_field', $options_str );
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_checkbox_array($id, $value = null, $title = "", $options = array(), $validation = null, $class = array('checkbox')){
        $vals = array();
        $id_array = explode('_',$id);
        $col = $id_array[1];
        foreach ($value AS $k => $v){
            $vals[] = $v->{$col};
        }
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
        //iterate through $options
        foreach ($options AS $k => $v){
            $options_array[] = '<div class="'.$id.'_'.$k.'_wrapper checkbox-wrapper"><input id="'.$id.'_'.$k.'" name="'.$id.'_input[]" type="checkbox" value="'.$k.'"'.$this->checked_in_array($vals,$k,false).' /> '.$v.'</div>';
        }
        $options_str = '<div class="checkbox-array-options-wrapper"><div class="inner-wrap">'.implode("\n\r",$options_array).'</div></div>';
        $form_field = apply_filters('msdlab_form__'.$id.'_field', $options_str );
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function checked_in_array($array,$current,$echo = true){
        if(!is_array($array)){return false;}
        if(in_array($current,$array)){
            if($echo){
                print "checked";
            } else {
                return "checked";
            }
        } else {
            return false;
        }
    }

    public function field_checkbox($id, $value = 0, $title = "", $validation = null, $class = array('checkbox','col-md-12')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<input id="'.$id.'_input" name="'.$id.'_input"  value="1" type="checkbox" '.checked(1,$_POST[$id.'_input'],0).' />');
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_input">'.$form_field.$title.'</label>');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_button($id,$title = "Save", $class = array('submit'), $type = "submit", $validate = true){
        if($validate == false){$atts = ' formnovalidate=formnovalidate ';}
        $form_field = apply_filters('msdlab_form__'.$id.'_button','<input id="'.$id.'_button" type="'.$type.'" value="'.$title.'"'.$atts.'/>');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    public function field_result($id, $value, $title = "", $class = array('medium')){
        if(is_null($value)){
            $value = $_POST[$id.'_input'];
        }
        if($placeholder == null){$placeholder = $title;}
        $label = apply_filters('msdlab_form__'.$id.'_label','<label for="'.$id.'_result">'.$title.'</label>');
        $form_field = apply_filters('msdlab_form__'.$id.'_field','<span class="result">'.$value.'</span>');
        if(is_array($class)) {
            $class = implode(" ", apply_filters('msdlab_form__' . $id . '_class', $class));
        }
        $ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
        return apply_filters('msdlab_form__'.$id.'', $ret);
    }

    
}