<?php
class MSDLAB_SettingControls{

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;
	public $javascript;

	public function __construct() {
		if(class_exists('MSDLAB_Queries')){
			$this->queries = new MSDLAB_Queries();
		}
	}

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {

		if( null == self::$instance ) {
			self::$instance = new MSDLAB_SettingControls();
		}

		return self::$instance;

	}

	//form fields
	public function settings_textfield($title = "",$id = "text", $class = array('medium'), $value = null){
		if(is_null($value)){
			$value = $_POST[$id.'_input'];
		}
		$label = apply_filters('msd_'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
		$form_field = apply_filters('msd_'.$id.'_field','<input id="'.$id.'_input" name="'.$id.'_input" type="text" value="'.$value.'" placeholder="'.$title.'" />');
		$class = implode(" ",apply_filters('msd_'.$id.'_class', $class));
		$ret = '<div id="'.$id.'" class="'.$class.'">'.$label.$form_field.'</div>';
		return apply_filters('msd_'.$id.'', $ret);
	}

	public function settings_date($title = "Date",$id = "date", $class = array('datepicker'), $value = null){
		if(is_null($value)){
			$value = $_POST[$id.'_input'];
		}
		$label = apply_filters('msd_'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
		$form_field = apply_filters('msd_'.$id.'_field','<input id="'.$id.'_input" name="'.$id.'_input" type="date" value="'.$value.'" placeholder="'.$title.'" />');
		$class = implode(" ",apply_filters('msd_'.$id.'_class', $class));
		$ret = '<div id="'.$id.'" class="'.$class.'">'.$label.$form_field.'</div>';
		return apply_filters('msd_'.$id.'', $ret);
	}
	
	public function settings_textarea($title = "Text to display out of season",$id = "text", $class = array('textarea'), $value = null){
		if(is_null($value)){
			$value = $_POST[$id.'_input'];
		}
		$label = apply_filters('msd_'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
		ob_start();
		wp_editor( stripcslashes($value), $id.'_input', array('media_buttons' => false,'teeny' => true,'textarea_rows' => 5) );
		$form_field = ob_get_clean();
		$class = implode(" ",apply_filters('msd_'.$id.'_class', $class));
		$ret = '<div id="'.$id.'" class="'.$class.'">'.$label.$form_field.'</div>';
		return apply_filters('msd_'.$id.'', $ret);
	}

	public function settings_button($title = "Save",$id = "submit", $class = array('submit'), $type = "submit"){
		$form_field = apply_filters('msd_'.$id.'_button','<input id="'.$id.'_button" type="'.$type.'" value="'.$title.'" class="button button-primary button-large" />');
		$class = implode(" ",apply_filters('msd_'.$id.'_class', $class));
		$ret = '<div id="'.$id.'" class="'.$class.'">'.$form_field.'</div>';
		return apply_filters('msd_'.$id.'', $ret);
	}

	public function build_javascript($id = "csf_form"){
		$ret = '
        <script>
  jQuery(function($){
    '.implode(" ",apply_filters('msd_'.$id.'_javascript', $this->javascript)).'
  });
  </script>';
		return $ret;
	}

	public function settings_hidden($id, $value = null, $title = "", $validation = null, $class = array('hidden')){
		if(is_null($value)){
			$value = $_POST[$id.'_input'];
		}
		$form_field = apply_filters('msd_'.$id.'_field','<input id="'.$id.'_input" name="'.$id.'_input" type="hidden" value="'.$value.'" />');
		$class = implode(" ",apply_filters('msd_'.$id.'_class', $class));
		$ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$form_field.'</div>';
		return apply_filters('msd_'.$id.'', $ret);
	}

	public function settings_select($id, $value = null, $title = "", $null_option = null, $options = array(), $validation = null, $class = array('select')){
		if(is_null($value)  || empty($value)){
			$value = $_POST[$id.'_input'];
		}
		if($null_option == null){$null_option = 'Select';}
		$label = apply_filters('msd_'.$id.'_label','<label for="'.$id.'_input">'.$title.'</label>');
		//iterate through $options
		$options_str = implode("\n\r",$this->build_options($options,$value,$null_option));
		$select = '<select id="'.$id.'_input" name="'.$id.'_input">'.$options_str.'</select>';
		$form_field = apply_filters('msd_'.$id.'_field', $select );
		$class = implode(" ",apply_filters('msd_'.$id.'_class', $class));
		$ret = '<div id="'.$id.'_wrapper" class="'.$class.'">'.$label.$form_field.'</div>';
		return apply_filters('msd_'.$id.'', $ret);
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

	public function delete_button($title = "Delete",$id = "delete",$class = array('submit'), $type = "submit"){
		$form_field = apply_filters('msd_'.$id.'_button','<input id="'.$id.'_input" name="'.$id.'_input" type="hidden" value="1" /><input id="'.$id.'_button" type="'.$type.'" value="'.$title.'" class="button btn-danger button-large" />');
		$class = implode(" ",apply_filters('msd_'.$id.'_class', $class));
		$script = '<script>
(function($) {
	$("#'.$id.'_button").click(function(e){
	    e.preventDefault();
	    $("#'.$id.'_input").val("0");
	    var c = confirm("Are you sure? This action can not be reversed!");
        if (c == true) {
            $(this).parents("form").submit();
        } else {
	        $("#'.$id.'_input").val("1");
        }
	});
})( jQuery );
</script>';
		$ret = '<div id="'.$id.'" class="'.$class.'">'.$form_field.'</div>'.$script;
		return apply_filters('msd_'.$id.'', $ret);
	}

	//create form


	public function form_header($id = "csf_form", $class = array()){
		$class = implode(" ",apply_filters('msd_'.$id.'_header_class', $class));
		$ret = '<form id="'.$id.'" class="'.$class.'" method="post">';
		return apply_filters('msd_'.$id.'_header', $ret);
	}

	public function form_footer(){
		$ret = '</form>';
		return apply_filters('msd_manage_form_footer', $ret);
	}

	public function get_form($options){
		extract($options);
		if(!$form_id){ return false; }
		switch ($form_id){
			case 'pressrelease':
				$ret['hdr'] = $this->form_header($form_id);
				$ret['info1'] = '<h3>Footer</h3>';
				$ret['footer'] = $this->settings_textarea('Footer Content','press_release_footer','',$data['footer'],null);

				$ret['info2'] = '<h3>CTA</h3>';
				$ret['cta_title'] = $this->settings_textfield('CTA Title','press_release_cta_title',array('large','setting-field'),$data['cta']['title']);
				$ret['cta_content'] = $this->settings_textarea('CTA Content','press_release_cta_content','',$data['cta']['content'],null);
				$ret['submit'] = $this->settings_button();
				$ret['nonce'] = wp_nonce_field( $form_id );
				$ret['ftr'] = $this->form_footer();
				break;
		}
		return implode("\n",$ret);
	}
}