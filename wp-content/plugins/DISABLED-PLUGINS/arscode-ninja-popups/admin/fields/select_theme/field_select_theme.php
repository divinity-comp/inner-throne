<?php

class SNP_NHP_Options_select_theme extends SNP_NHP_Options
{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function __construct($field = array(), $value ='', $parent)
	{

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
	}

//function

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function render()
	{
		global $SNP_THEMES_DIR;
		$class = (isset($this->field['class']))?$this->field['class']:'';
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][theme]" class="'.$class.' nhp-opts-select-theme" >';
		foreach ($this->field['options'] as $k => $v)
		{
			echo '<option value="' . $k . '" ' . selected((isset($this->value['theme']) ? $this->value['theme'] : ''), $k, false) . ' data-preview="'.plugins_url('', realpath($v['DIR'])).'">' . $v['NAME'] . '</option>';
		}//foreach

		echo '</select>';
		
		echo '<select id="'.$this->field['id'].'-color" name="'.$this->args['opt_name'].'['.$this->field['id'].'][color]" class="'.$class.' nhp-opts-select-theme-color" >';
		echo '</select>';
		
		echo '<select id="'.$this->field['id'].'-type" name="'.$this->args['opt_name'].'['.$this->field['id'].'][type]" class="'.$class.' nhp-opts-select-theme-type">';
		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>' : '';
		
		echo '<input type="hidden" id="SNP_URL" value="'.SNP_URL.'" />';
		if(isset($this->value['color']))
		{
		    echo '<input type="hidden" id="nhp-opts-select-theme-color-org-val" value="'.$this->value['color'].'" />';
		}
		if(isset($this->value['type']))
		{
		    echo '<input type="hidden" id="nhp-opts-select-theme-type-org-val" value="'.$this->value['type'].'" />';
		}
	}
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since SNP_NHP_Options 1.0.1
	*/
	function enqueue(){
		
		wp_enqueue_script(
			'nhp-opts-select-theme-js', 
			SNP_NHP_OPTIONS_URL.'fields/select_theme/field_select_theme.js', 
			array('jquery'),
			time(),
			true
		);
		
	}//function

//function
}

//class
?>