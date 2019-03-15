<?php
class Captionpix_Options {

	const OPTIONS_NAME = 'captionpix_options';

	private static $defaults = array(
	    'theme'=> 'crystal',
	    'align' => 'left',
	    'framebackground' => '',
	    'frameborder' => '',
	    'framebordercolor' => '',    	
	    'framebordersize' => '',
	    'framecolor' => '',
	    'framesize'=> '',
	    'marginbottom' => '10',	
	    'marginside' => '15',
	    'margintop' => '7',
	    'nostyle' => '',
	    'width' => '300',
	    'imgsrc' => '',
	    'imglink' => '',	
	    'imglinkrel' => '',
	    'imglinkclass' => '',
	    'imgtitle' => '',
	    'imgalt' => '',
	    'imgborder' => 'none',	
	    'imgbordercolor' => '',
	    'imgbordersize' => '',    
	    'imgmargin' => '0',	
	    'imgpadding' => '0',
	    'captionalign' => 'center',
	    'captionclass' => '',
	    'captionfontcolor' => '#FFFFFF',	
	    'captionfontfamily' => 'inherit',
	    'captionfontsize' => '12',
	    'captionfontstyle' => 'normal',
	    'captionfontweight' => 'normal',
	    'captionpaddingleft' => '10',
	    'captionpaddingright' => '10',
	    'captionpaddingtop' => '10',
	    'captionpaddingbottom' => '5',
	    'captionmaxwidth' => '',
	    'captiontext' => '',
	    'autocaption' => 'none'
	    );

    protected static $options = null;	

	private static $autocaptioning = array (
		'none' => 'None',
		'title' => 'Use Image Title as Caption',
		'alt' => 'Use Image Alt as Caption',
		'post' => 'Use Post Title as Caption'
		);

    public static function get_autocaptioning() {
		return self::$autocaptioning;
    }

    private static function get_defaults() {
		return self::$defaults;
    }


    public static function init($more = array()) {
        if (self::$options === null) self::$options = new Captionpix_DIY_Options(self::OPTIONS_NAME, self::$defaults);
		if (count($more) > 0) self::$options->add_defaults($more);
    }

	public static function get_options ($cache = true) {
		return self::$options->get_options($cache = true); 
	}

	public static function get_option($option_name, $cache = true) {
	    return self::$options->get_option($option_name, $cache); 
	}

	public static function save_options ($options) {
		return self::$options->save_options($options);
	}

	public static function validate_options ($defaults, $options) {
		return self::$options->validate_options((array)$defaults, (array)$options);
	}	

	public static function upgrade_options () {
		return self::$options->upgrade_options();
	}	
}
?>