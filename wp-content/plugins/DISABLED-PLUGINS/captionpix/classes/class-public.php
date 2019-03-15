<?php
/*
DESCRIPTION OF PARAMETERS for captionpix short code

theme                - the name of captioning theme

align                - align the image either left, right, center or let it be
frameborder          - full frame border definition 
framebordercolor     - color of the frame's border
framebordersize      - width of the frame's border
framecolor           - colour of the frame
framesize            - width of the frame of the image
marginside           - margin for the side of the image when floated left or right
margintop            - margin for the top of the frame which can be positive or negative
marginbottom         - margin for the bottom of the frame which can be positive or negative
width                - total width of the image plus frame

imgalt               - image alt text
imgbordercolor       - colour of the border around the image
imgbordersize        - size of the border around the image
imgborder            - full image border specification 
imglink              - the link to visit when the image is clicked
imgmargin            - image margin
imgpadding           - image padding
imgsrc               - the url to the image to be used (REQUIRED)
imgtitle             - image title text

captionalign         - align the caption text either left, right or center
captionclass         - CSS class used to define the caption (all other params apart from captiontext are ignored)
captionfontcolor     - colour of the caption font
captionfontsize      - size in pixels of the caption font
captionfontfamily    - font family for caption text - either inherit or a font name
captionfontstyle     - font style for caption text - either normal or italic
captionpaddingbottom - padding at the bottom of the caption text in pixels
captionpaddingleft   - padding at the left of the caption text in pixels
captionpaddingright  - padding at the right of the caption text in pixels
captionpaddingtop    - padding at the top of the caption text in pixels
captiontext          - the caption text to be displayed
*/

class CaptionPix {

	static function init() {
		add_filter('widget_text', 'do_shortcode', 11);
		add_shortcode('captionpix', array(__CLASS__,'display'));
		//add_filter('the_content', array('captionpix','autocaption'), 10); //autocaptioning coming in a later release
	}

	static function autocaption($content) {
    	if (is_home() || is_single() || is_page()) {
			$options = captionpix_get_options();
			$auto = array_key_exists('autocaption',$options) ? $options['autocaption'] : 'none';
			if ($auto != 'none') 
				$content = preg_replace_callback(
    			    '/<img\s[^>]*>/i',
    			    create_function(
    			        '$matches',
    			        'return self::autocaption_image($matches[0],$auto);'
    	    		),
    	    		$content
    			);
    	}
		return $content;
	}

	static function autocaption_image($img, $autocaption) {
  		$class=preg_match('/class="[^"]*"/i', $img, $matches) ?  $matches[1] :'';
   		if (strpos($class,'caption-pix-outer') !== FALSE) return $img;

		$src= preg_match('/src="(.*)"/i', $img, $matches) ? $matches[1] : '';
		$title = preg_match('/title="([^"]*)"/i', $img, $matches) ? $matches[1] : '';
		$alt=preg_match('/alt="([^"]*)"/i', $img, $matches) ?  $matches[1] :'';
    	switch ($autocaption) {
    		case "post": {
    			global $post;
    			$caption = $post->post_title;
				break;
				}
    		case "alt": { $caption = $alt; break; }
    		case "title":
    		default:
    			{ $caption = $title; break;}
    	}
    	$params = array();
    	$params['imgurl']= $src;
   	 	$params['imgtitle']= $title;
		$params['imgalt'] = $alt;
		$params['captiontext'] = $caption;
		return self::display ($params);
	}

	static function display ($attr) {
		$errors = self::validate($attr);
  		if (count($errors) > 0 ) return implode('<br/>',$errors); //exit if errors
  		$mytheme = array_key_exists('theme', $attr)? $attr['theme'] : Captionpix_Options::get_option('theme'); //get the chosen theme name
  		$theme_defaults = Captionpix_Theme_Factory::get_theme($mytheme);   //get theme defaults
  		$defaults = array_merge(Captionpix_Options::get_options(), $theme_defaults); //get combined list of defaults
		$nooverrides = $defaults['nooverrides']=='theme' ? array_keys($theme_defaults) : explode(",",$defaults['nooverrides']);
		if (count($nooverrides) > 0) foreach ($nooverrides as $key) if (array_key_exists($key,$attr)) unset($attr[$key]); //suppress unwanted overrides
  		$params = shortcode_atts($defaults , $attr ); //get any user overrides
  		$theme_builder = array('captionpix','build_theme_'.str_replace('-','_',$mytheme));
  		return is_callable($theme_builder) ? call_user_func($theme_builder,$params) : self::build_html($params);
	}

	static function error(&$errors, $message) {
    	$errors[] = '<span style="color:#5B5B5B; border:1px #CE0053 solid; padding:5px; font-weight:bold; font-size:12px;">'.$message.'</span>';
	}

	static function validate(&$attr){
  		foreach ( $attr as $k => $v )
    		$attr[$k] = (($k == 'framecolor') ||($k == 'imgbordercolor') ||($k == 'captionfontcolor') ||
    			($k == 'captionfontstyle') || ($k == 'captionalign') || ($k == 'float')) ? strtolower(trim($v)) : trim($v);
  		extract($attr);
  		$e = array();
  		if (isset($float)) self::validate_in_set($e, 'float', $float, array('left','right','center','none'));
  		if (isset($framecolor)) self::validate_color($e, 'framecolor', $framecolor);
  		if (isset($marginbottom)) self::check_number_range($e, 'marginbottom', $marginbottom, -250, 250);
  		if (isset($margintop)) self::check_number_range($e, 'margintop', $margintop, -250, 250);
  		if (isset($marginside)) self::check_number_range($e, 'marginside', $marginside, 0, 50);
  		if (isset($padding)) self::check_number_range($e, 'padding', $padding, 0, 50);
  		if (isset($width)) self::check_number_range($e, 'width', $width, 0, 1280);
		if (isset($captionfontcolor)) self::validate_color($e, 'captionfontcolor', $captionfontcolor);
  		if (isset($captionalign)) self::validate_in_set($e, 'captionalign', $captionalign, array('left','right','center','justify','initial','inherit'));
  		if (isset($captionfontsize)) self::check_number_range($e, 'captionfontsize', $captionfontsize, 4, 72);
  		return $e;
	}

	static function check_number_range(&$e,$key,$value,$min,$max) {
		if (self::check_number($e,$key,$value)) 
			if (($value >= $min) && ($value <= $max))
				return true;
			else 
         		self::error($e, sprintf(__('%1$s must be between %2$d and %3$d'),$key,$min,$max));
		return false;
	}
	
	static function check_number(&$e,$key,$value) {
        $pattern = '/^-?[0-9]{1,4}$/';
		if (preg_match($pattern, $value, $matches) && ($matches[0]==trim($value))) 
			return $matches[0];
		else {
			self::error($e,sprintf(__('%1$s must be a valid number; %2$s is not valid.'),$key,$value));
			return false;
		}
	}	

	static function validate_in_set(&$e, $key, $value, $set) {
		if (empty($value)) return true;
		foreach ($set as $option) if ($value==$option) return true;
		self::error($e,sprintf(__('%1$s must be one of the following: %2$s'),$key,implode(",",$set)));
		return false;
	}
	
	static function validate_color(&$e, $key, $value) {
		if (empty($value)) return true;		
		$pattern = '/(#([0-9A-Fa-f]{3,6})\b)|(aqua)|(black)|(blue)|(fuchsia)|(gray)|(green)|(lime)|(maroon)|(navy)|(olive)|(orange)|(purple)|(red)|(silver)|(teal)|(white)|(yellow)|(rgb\(\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*\))|(rgb\(\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*\))/';
		if (preg_match($pattern, $value, $matches))
			return true;
		else {
			self::error($e,sprintf(__('%1$s must be a valid color;  %2$s is not valid.'),$key,$value));
			return false;
			}
	}
	
	static private function build_caption($caption_params) {
        $width='';
        $class='';
  		extract($caption_params);  
   		if (empty($text)) return '';
   		if (!empty($class)) 
   			$style = 'class="'.$class.'"';   //style using only CSS class
   		else {
   			$padding = sprintf(';padding : %1$spx %2$spx %3$spx %4$spx', $paddingtop,$paddingright, $paddingbottom, $paddingleft);
			$width = empty($width) ? '' : sprintf('; width: %1$s', (($width-$paddingleft-$paddingright).'px'));
   			if (!empty($maxwidth)) $maxwidth = '; max-width:'.$maxwidth.'px';   			
   			if (!empty($align)) $align = '; text-align:'.$align;
   			if (!empty($fontfamily)) $fontfamily = ';font-family:'.$fontfamily;
			if (!empty($fontstyle)) $fontstyle = '; font-style:'.$fontstyle;
   			if (!empty($fontcolor)) $fontcolor = '; color:'.$fontcolor;
   			if (isset($fontsize)) { 
   				$lineheight = '; line-height:'.$fontsize.'px';
   				$fontsize = '; font-size:'.$fontsize.'px';
   			}
   			$style = 'style="margin: 0 auto'.$padding.
   				$width.$maxwidth.$align.$fontfamily.$fontstyle.$fontcolor.$fontsize.$lineheight.'"';
   		}
   		return sprintf('<div %1$s>%2$s</div>',$style, $text);
 	}
 
 	static private function build_image($img_params) {
	$width=''; $border='';
   	extract($img_params);
    
		$badchars = array('&',  '*',   '\'',   '?', '!', '"', '`', '_');
		$title =  str_replace($badchars, "", $title);
		$alt = empty($alt) ? $title : htmlspecialchars($alt, ENT_QUOTES) ;
		$padding = 'padding:'.$padding;
        $margin = ';margin:'.$margin;	
		$width = empty($width) ? ';width:100%' : (';width:'.$width.'px;max-width:100%');        	
        if (!empty($linkrel)) $linkrel = ' rel="'.$linkrel.'"'; 
        if (!empty($linkclass)) $linkclass = ' class="'.$linkclass.'"'; 
        if (!empty($bordercolor)) 
        	$border = ';border:'.(empty($bordersize) ? '1' : $bordersize).'px solid '.$bordercolor;
        elseif (!empty($border) )
            $border  = ';border:'.$border;
   		$img = sprintf('<img src="%1$s" style="%2$s" title="%3$s" alt="%4$s" /></a>',    
				$src, $padding.$margin.$width.$border, $title, $alt);    
		if ($link == 'none')
			return $img;
		else
    		return sprintf('<a%1$s href="%2$s" style="display:block" %3$s>%4$s</a>',    
				$linkrel, $link ? $link : $src, $linkclass, $img);    
 	}

 	static private function build_frame($frame_params,$img_params,$caption_params) {
		$width = ''; $align='';$margintop='';$marginbottom='';$padding='';$framecolor='';$framebackground='';
    	extract($frame_params);
    	if ($nostyle) { 	
    		$style1='';
    		$style2='';
		} else {    	
    		$width = empty($width) ? '' : (';width:'.$width.'px;');
    		$display = 'display: ' . (empty($width) ? 'inline-block' : 'block'); //shrink to fit if responsive
			switch($align){
    			case "right": $align = ';float:right; margin-right: 5px; margin-left:'.$marginside.'px'; break;
    			case "left": $align = ';float:left; margin-left: 5px; margin-right:'.$marginside.'px'; break;
    			case "center": $align = ';float:none; margin:0 auto; text-align:center; width:100%; overflow:hidden;position: relative; clear:both'; break;
  				default: $align=';float:none';
  			}
			$margintop = ';margin-top:'.$margintop.'px';
			$marginbottom = ';margin-bottom:'.$marginbottom.'px';
			if (!empty($framesize)) $padding = ';padding:'.$framesize.'px';
			if (!empty($framecolor)) $framecolor = ';background-color:'.$framecolor; 	
			if (!empty($framebackground)) $framebackground = ';background-image:url('.$framebackground.')'; 
			if (!empty($frameborder)) 
				$frameborder = ';'.$frameborder; 
			elseif (!empty($framebordercolor)) 
				$frameborder = ';border:'.(empty($framebordersize) ? '1' : $framebordersize).'px solid '.$framebordercolor;
 			$style1 = ' style="'.$display.$width.$align.$margintop.$marginbottom.'"';
 			$style2 = ' style="display:inline-block'.$padding.$framecolor.$framebackground.$frameborder.'"';
 		}
		return '<div class="captionpix-outer '.'"'.$style1.'>'.
			   '<div class="captionpix-frame '.$theme.'"'.$style2.'>'.
	    	   '<div class="captionpix-inner">'.self::build_image($img_params).self::build_caption($caption_params).'</div></div></div>';
 	}

	static private function build_html($params) {
		$frame_params = array();
		$img_params = array();
		$caption_params = array();
		foreach ($params as $key => $value) {
			if (substr($key,0,7)=='caption') {
				$ckey = substr($key,7);
				$caption_params[$ckey] = $value;
			}
			elseif (substr($key,0,3)=='img'){
				$ikey = substr($key,3);
				$img_params[$ikey] = $value;
			} elseif ('width'==$key){
				$img_params[$key] = $value;
			} else {
				$frame_params[$key] = $value;
			}
		}
		if (!empty($img_params['width'])) {
			$caption_params['width'] = $img_params['width'];
			$frame_params['width']= $img_params['width'] + $img_params['padding'] + 
				empty($img_params['bordersize'])?0  : (2*$img_params['bordersize']);
		}
		return self::build_frame($frame_params,$img_params,$caption_params);
	}

}
