<?php

/*
Plugin Name: [RYIT] Journey to the Great Self
Description: Adds the JGS code to the RYIT website
Version:     0.1
Author:      Eivind Figenschau Skjellum
*/

add_action('wp_head', 'jgs_initialize');

function jgs_initialize() {
	if (is_jgs_page()) {
		add_jgs_stylesheet();
		jgs_enqueue_jplayer();
	}
}

function jgs_enqueue_jplayer() {
	$echo = '<link rel="stylesheet" href="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/skin/circle.player.css">';
	$echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/jquery.jplayer.min.js"></script>';
	$echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/jquery.transform2d.js"></script>';
	$echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/jquery.grab.js"></script>';
	$echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/mod.csstransforms.min.js"></script>';
	$echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/circle.player.js"></script>';
	echo $echo;
}

function add_jgs_stylesheet() {
	global $post;
	if (!is_object($post)) return;
	$parent = wp_get_post_parent_id($post->ID);
	if ($parent == 52269) {
		wp_enqueue_style('greatself-style', get_stylesheet_directory_uri() . "/css/greatself-homestudy.css");
		wp_enqueue_style('rpg-style', get_stylesheet_directory_uri() . "/css/rpg-awesome.css");
	}
}

function jgs_embed_jplayer() {
?>
	<!-- The container for the interface can go where you want to display it. Show and hide it as you need. -->
	<li id="label">Menu</li>
	<li id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
   <div class="jp-type-single">
	  <div class="jp-gui jp-interface">
		 <button class="jp-play" role="button" tabindex="0"><span class="fa fa-play"></span><span class="fa fa-pause"></span></button>
		 <!--
		 <div class="jp-volume-controls">
			<button class="jp-mute" role="button" tabindex="0">mute</button>
			<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
			<div class="jp-volume-bar">
			    <div class="jp-volume-bar-value"></div>
			</div>
		 </div>
		 <div class="jp-controls-holder">
			<div class="jp-controls">
			    <button class="jp-play" role="button" tabindex="0">play</button>
			    <button class="jp-stop" role="button" tabindex="0">stop</button>
			</div>
			<div class="jp-progress">
			    <div class="jp-seek-bar">
				   <div class="jp-play-bar"></div>
			    </div>
			</div>
			<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
			<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
			<div class="jp-toggles">
			    <button class="jp-repeat" role="button" tabindex="0">repeat</button>
			</div>
		 </div>
		 -->
	  </div>
	  <!--
	  <div class="jp-details">
		 <div class="jp-title" aria-label="title">&nbsp;</div>
	  </div>
	  <div class="jp-no-solution">
		 <span>Update Required</span>
		 To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
	  </div>
	  -->
   </div>
 </li>
<?php
}

//Ajax update of Fragments form
add_action('wp_ajax_display_echo_system', 'jgs_display_echo_system');
add_action('wp_ajax_nopriv_display_echo_system', 'jgs_display_echo_system');

function jgs_display_echo_system() {
	if (isset($_REQUEST['post_id'])) {
		$post_id = $_REQUEST['post_id'];
	}
	else {
		die();
	}

	$response = array();
	$response[] = jgs_echo_archives(array(
		"post_id" => $post_id
	));
	echo json_encode($response);
	die();
}

function jgs_user_has_answered() {
	global $post;
	//Check for existing comment
	$args = array(
		'post_id' => $post->ID,
		'user_id' => get_current_user_id() ,
	);
	/*
	   print_r($args);
	   echo "post id: " . $post->ID;
	   echo "user id: " . get_current_user_id(); */
	$comments = get_comments($args);
	if (count($comments) > 0):
		return true;
	else:
		return false;
	endif;
}

add_shortcode('jgs_user_has_answered', 'jgs_user_has_answered');

//Simple Comment Editing
add_filter('sce_comment_time', 'edit_sce_comment_time');

function edit_sce_comment_time($time_in_minutes) {
	return 60;
}

//Register AJAX functions
add_action('wp_ajax_dialogue_request', 'dialogue_request');
add_action('wp_ajax_nopriv_dialogue_request', 'dialogue_request');

function dialogue_request() {
	if (isset($_REQUEST['post_id'])) {
		$post_id = $_REQUEST['post_id'];
	}
	else {
		die();
	}

	$response = get_field('jgs_character', $post_id);
	$response = $response['response'];
	echo json_encode($response);
	die();
}

//AJAX functions for progress
add_action('wp_ajax_update_progress', 'jgs_update_progress');
add_action('wp_ajax_nopriv_update_progress', 'jgs_update_progress');

function jgs_update_progress() {
	if (isset($_REQUEST['week_id'])) {
		$week_id = $_REQUEST['week_id'];
	}
	if (isset($_REQUEST['step'])) {
		$step = $_REQUEST['step'];
	}

	$user_id = get_current_user_id();
	$progress = $week_id . "," . $step;

	//progress field id = field_5a58d033aeb25
	update_field('jgs_user_data_progress', $progress, 'user_' . $user_id);

	die();
}

//Ajax update of Advanced Custom Fields form
add_action('wp_ajax_save_my_data', 'acf_form_head');
add_action('wp_ajax_nopriv_save_my_data', 'acf_form_head');

function jgs_fragments_collected($user_id) {
	if (empty($user_id)) {
		$user_id = get_current_user_id();
	}

	$fragments_count = 4;
	$fragments = get_field('jgs_fragments', 'user_' . $user_id);

	for ($i = 1;$i <= $fragments_count;$i++) {
		$field = $fragments['fragment_' . $i];
		if (empty($field)) {
			return false;
		}
		else {
			return true;
		}
	}
}

add_action('comment_post', 'ajaxify_comments', 20, 2);

function ajaxify_comments($comment_ID, $comment_status) {
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		//If AJAX Request Then
		switch ($comment_status) {
			case '0':
				//notify moderator of unapproved comment
				wp_notify_moderator($comment_ID);
			case '1': //Approved comment
				echo "success";
				$commentdata = & get_comment($comment_ID, ARRAY_A);
				$post = & get_post($commentdata['comment_post_ID']);
				wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
			break;
			default:
				echo "error";
		}
		exit;
	}
}

function jgs_user_profile() {
	global $post;
	$user_id = get_current_user_id();

	$fields = array(
		'field_5a576ed8b86eb',
		'field_59e7447aceaf8',
		'field_5a4be6cf193be',
		'field_59e748b89988c',
		'field_5a58cff4aeb23',
		'field_5a5a254d8bae5'
	);

	if (jgs_fragments_collected($user_id)) {
		$fields[] = 'field_5a6518b25fd3d';
	}

	$settings = array(
		'id' => 'acf_user_profile_form',
		'fields' => $fields,
		'post_id' => 'user_' . $user_id
	);

?>

    <div id="account" class="closed hide">
	   <div class="toggle"><i class="fa fa-cog" aria-hidden="true"></i></div>
	   <div class="wrap">
		  <div class="wrap">
			 <h3>Account settings</h3>
			 <?php
	$user = get_currentuserinfo();
	$username = $user->user_firstname . " " . $user->user_lastname;
?>
			 <p class='welcome'>For <?php echo $username; ?>
			 <?php acf_form($settings); ?>
		  </div>
	   </div>
    </div>

<?php
}

function jgs_embed_videos() {
	global $post;
	$calls = get_field('jgs_videos', $post->ID);
	if ($calls) {
?>
<div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_1  fusion-one-full fusion-column-first fusion-column-last maxwidth-1000 1_1" id="coaching-call">
<?php
		foreach ($calls as $call):
			$title = $call['title'];
			$text = $call['text'];
			$src = $call['src'];
?>
<?php if ($src && $title): ?>
    <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
    <div class="fusion-builder-row fusion-builder-row-inner fusion-row "><div class="fusion-layout-column fusion_builder_column fusion_builder_column_2_3  fusion-two-third fusion-column-first 2_3" style="margin-top: 0px;margin-bottom: 20px;width:66.66%;width:calc(66.66% - ( ( 4% ) * 0.6666 ) );margin-right:4%;">
    <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
    <div class="fusion-video fusion-vimeo fusion-alignleft" style="max-width:700px;max-height:394px; width:100%"><div class="video-shortcode"><div class="fluid-width-video-wrapper" style="padding-top: 56.2857%;"><iframe src="https://player.vimeo.com/video/<?php echo $src; ?>?autoplay=0&amp;api=1&amp;player_id=player_3&amp;wmode=opaque" allowfullscreen="" title="vimeo251266841" id="player_3" name="fitvid0" kwframeid="3"></iframe></div></div></div>

    </div>
    </div><div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_3  fusion-one-third fusion-column-last 1_3" style="margin-top: 0px;margin-bottom: 20px;width:33.33%;width:calc(33.33% - ( ( 4% ) * 0.3333 ) );">
    <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
    <div class="fusion-title title fusion-sep-none fusion-title-size-two" style="margin-top:0px;margin-bottom:15px;"><h2 class="title-heading-left"><?php echo $title; ?></h2></div><div class="fusion-text"><?php echo $text; ?></p>
    </div>

    </div>
    </div></div><div class="fusion-clearfix"></div>
    </div>

<?php
			endif;
		endforeach; ?>
</div>
<?php
	}
}


/* ******************** */
/*       JGS CODE       */
/* ******************** */

function jgs_user_interface() {
	$user_id = get_current_user_id();
	echo "<ul id='jgs_interface' class='hide'>";
	jgs_embed_jplayer();
	echo '<li id="jgs_challenge_box_menuitem"><img src="' . get_site_url() . '/wp-content/uploads/2018/01/jgs-magic-box-150x100.png"></li>';
	//echo '<li id="jgs_inventory_system"><img src="' . get_stylesheet_directory_uri() . '/images/burlap-bag.png">';
	$week_id = get_field('jgs_week_id');
	$progress_week = get_field('jgs_user_data', 'user_' . $user_id);
	$progress_week = $progress_week['progress'];
	$progress_week = explode(",", $progress_week);
	$progress_week = $progress_week[0];
	if ($progress_week >= 3 && $week_id > 2) {
		echo "<li id='jgs_echo_system'><img src='" . get_stylesheet_directory_uri() . "/images/echo-system-icon.png' /></li>";
	}

	$draco_count = ryit_get_reward_balance();
	if (!empty($draco_count)) {
		$draco_echo = "<div><span>" . $draco_count . "</span></div>";
	}
	echo "<li id='jgs_draco'>" . $draco_echo . "<img src='" . get_stylesheet_directory_uri() . "/images/coin-stack.png' /></li>";
	echo "<li id='jgs_forum'><a href='/forums/forum/jgs-beta/' target='_blank'><img src='" . get_stylesheet_directory_uri() . "/images/forum-icon.png' /></a></li>";
	echo "</ul>";
}

function jgs_character_interview() {
	global $post;
	$character = get_field('jgs_character', $post->ID);
	if ($character) {
		$name = $character['name'];
		$description = $character['description'];
		$portrait_url = $character['portrait'];
		$greeting = $character['greeting'];
		$response = $character['response'];
		$output = "<div class='jgs_character_interview'>";
		$output .= "<div class='portrait'><img src='" . $portrait_url . "' width='300' height='300' /></div>";
		$output .= "<h2>" . $name . "</h2>";
		$output .= "<h4>" . $description . "</h4>";
		if (jgs_user_has_answered()) {
			$output .= "<div class='greeting'>" . $response . "</div>";
		}
		else {
			$output .= "<div class='greeting'>" . $greeting . "</div>";
		}

		$response_none = get_field('jgs_character_length_responses_response_none', $post->ID);
		$response_short = get_field('jgs_character_length_responses_response_short');
		$response_medium = get_field('jgs_character_length_responses_response_medium');
		$response_long = get_field('jgs_character_length_responses_response_long');
		$output .= "<input id='echo_length_responses' type='hidden' response_none='" . $response_none . "' response_short='" . $response_short . "' response_medium='" . $response_medium . "' response_long='" . $response_long . "'>";
		$output .= "</div>";
		return $output;
	}
}

add_shortcode('jgs_character_interview', 'jgs_character_interview');

function jgs_variable_text($atts = [], $content = null) {
	$args = shortcode_atts(array(
		'type' => 'gender',
		'value' => 'default',
		'ne' => false, //show if NOT equal
		'fallback' => ''
	) , $atts);

	$type = $args['type'];
	$value = $args['value'];
	$display_if_not_equal = $args['ne'] == "true" ? true : false;
	$user_id = get_current_user_id();

	if ($type == 'gender') {
		$type_val = get_field('ryit_user_profile_gender', 'user_' . $user_id);
		if (($type_val == $value && !$display_if_not_equal) || ($type_val != $value && $display_if_not_equal)) {
			$content = do_shortcode($content);
			return $content;
		}
	}
	if ($type == 'last_id') {
		$last_id = get_field('jgs_user_data_last_choice_id', 'user_' . $user_id);
		if (strpos($type, ",")) {
			$vals = explode(",", $type);
			if ((in_array($last_id, $vals) && !$display_if_not_equal) || (!in_array($last_id, $vals) && $display_if_not_equal)) {
				$content = do_shortcode($content);
				return $content;
			}
		}
		else {
			if (($last_id == $value && !$display_if_not_equal) || ($last_id != $value && $display_if_not_equal)) {
				$content = do_shortcode($content);
				return $content;
			}
		}
	}

	return false;
}

add_shortcode('jgs_var_text', 'jgs_variable_text');
add_shortcode('jgs_var_text_nested', 'jgs_variable_text');

function jgs_retrieve_fragment($atts) {
	extract(shortcode_atts(array(
		'value' => '1'
	) , $atts));

	$user_id = get_current_user_id();

	$fragments = get_field('jgs_fragments', 'user_' . $user_id);

	if ($value) {
		$fragment = $fragments['fragment_' . $value];
		if ($fragment) {
			return $fragment;
		}
	}

	return false;
}

add_shortcode('jgs_retrieve_fragment', 'jgs_retrieve_fragment');

//Journey continues button
function jgs_continue_btn_func($atts) {
	extract(shortcode_atts(array(
		'step' => '1',
		'label' => 'Continue your Journey',
		'finalize_week' => false,
	) , $atts));

	if ($finalize_week) {
		$finalize = " finalize='true'";
	}

	//HTML
	$echo = "<div class='continue-button' step='" . $step . "'" . $finalize . ">";
	$echo .= "<a href='#'>";
	$echo .= "<span>" . $label . "</span>";
	$echo .= "</a>";
	$echo .= "</div>";

	return $echo;
}

add_shortcode('jgs_continue_btn', 'jgs_continue_btn_func');

function jgs_echo_archives($params) {

	// compensating for inability to send booleans with shortcode
	switch ($params['display']) {
		case 'input':
			$hide_input = false;
			$hide_output = true;
		break;
		case 'all':
			$hide_input = false;
			$hide_output = false;
		case 'comments':
			$hide_input = true;
			$hide_output = false;
		break;
		default:
			$hide_input = true;
			$hide_output = false;
		break;
	}

	if ($params['post_id']) {
		$post_id = $params['post_id'];
	}
	else {
		global $post;
		$post_id = $post->ID;
	}

	//end compensating
	/* Show comments */
	if (get_comments_number($post_id) > 0 && !$hide_output) {
		$args = array(
			'post_id' => $post_id
		);
		$comments = get_comments($args);
		$echo = "";
		$echo .= "<div id='comments'>";

		foreach ($comments as $comment) {
			$author_ID = $comment->user_id;
			//echo "author id: " . $author_ID . "|";
			$gender = get_field('ryit_user_profile_gender', 'user_' . $author_ID);

			//Check ECHO-archives visibility settings
			$visibility = get_field('jgs_user_profile', 'user_' . $author_ID);
			$visibility = $visibility['echo_visibility'];
			if (empty($visibility) || $visibility == "") $visibility = "show";

			//Determine gender settings
			if ($visibility != anonymous) {
				$avatar = get_field('ryit_user_profile_image', 'user_' . $author_ID);
				if ($gender == 'man') {
					$icon = "<span class='gender'><i class='fa fa-mars'></i></span>";
				}
				else if ($gender == 'woman') {
					$icon = "<span class='gender'><i class='fa fa-venus'></i></span>";
				}
				else {
					$icon = "";
				}
			}

			//Determine name settings
			if ($visibility == "show") {
				$name = $comment->comment_author;
				$city = get_field('ryit_user_profile_city', 'user_' . $author_ID);
				$country = get_field('ryit_user_profile_country', 'user_' . $author_ID);
				if ($city) {
					$location = $city;
					if ($country) {
						$location .= ", " . $country;
					}
				}
				else {
					if ($country) {
						$location = $country;
					}
				}
				$location = "<span class='location'>" . $location . "</span>";
			}
			if ($visibility == "anonymous") {
				$name = get_field('ryit_user_profile_nickname', 'user_' . $author_ID);
				if (!$name) {
					$name = "Anonymous";
				}
				$location = "";
			}

			if ($visibility == "show" || $visibility == "anonymous") {
				$echo .= "<div class='comment'>";
				$placeholder = "";
				if (empty($avatar)) {
					if ($gender == "man") {
						$placeholder = " placeholder-man";
					}
					else if ($gender == "woman") {
						$placeholder = " placeholder-woman";
					}
					else {
						$placeholder = " placeholder-default";
					}
				}
				$echo .= "<div class='avatar" . $placeholder . "'><img src='" . $avatar . "' /><span class='date'> Posted on" . get_comment_date('M j, Y', $comment->comment_ID) . "</span></div>";
				$echo .= "<div class='text'>";
				$echo .= "<h3>" . $name . "</h3>";
				$echo .= "<div class='meta'>" . $icon . $location . "</div>";
				$echo .= "<div class='body'>" . apply_filters('the_content', $comment->comment_content) . "</div>";
				$echo .= "</div>";
				$echo .= "</div>";
			}

			unset($avatar, $name, $gender, $city, $country);
		}

		$echo .= "</div>";
	}
	else {
		if ($hide_input) {
			$echo .= "<div><p>No comments in the ECHO-archives for this week.</p></div>";
		}
	}

	/* Show comment form, if no comment has already been added */

	//no answer has been given, print out input field
	if (!jgs_user_has_answered() && !$hide_input) {
		$label_submit = get_field('jgs_character', $post->ID);
		$label_submit = "Respond to " . $label_submit['name'];

		ob_start();
		$args = array(
			'label_submit' => $label_submit,
			'title_reply' => '',
			'logged_in_as' => '',
			'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun') . '</label><textarea id="jgs_echo_comment" name="comment" cols="45" rows="8" aria-required="true">The ECHO-system awaits you...</textarea></p>'
		);
		comment_form($args, $post->ID);
		$echo .= ob_get_clean();
	}

	if (!comments_open() && get_comments_number() && post_type_supports(get_post_type() , 'comments')):
		$echo .= '<p class="no-comments">' . esc_html_e("Comments are closed.", "Avada") . '</p>';
	endif;

	return $echo;
}

add_shortcode('jgs_echo_archives', 'jgs_echo_archives');

//Fragments of the past form
function jgs_fragments() {
	$user_id = get_current_user_id();
	$fragments_count = 6;
	$fragments = get_field('jgs_fragments', 'user_' . $user_id);

	for ($i = 1;$i <= $fragments_count;$i++) {
		$field = $fragments['fragment_' . $i];
		if (empty($field)) {
			break;
			$form_complete = false;
		}
		else {
			$form_complete = true;
		}
	}
	$settings = array(
		'fields' => array(
			'field_5a6518b25fd3d'
		) ,
		'id' => 'acf_fragments_form',
		'post_id' => 'user_' . $user_id
	);

	if ($form_complete) {
		return "<div class='form_complete'>You have already shared your fragments. Edit them in the profile settings.</div>";
	}
	else {
		ob_start();
		acf_form($settings);
		return ob_get_clean();
	}
}

add_shortcode("jgs_fragments", "jgs_fragments");

//Fragments of the past form
function jgs_acf_input_form($atts) {
	acf_form_head();
	$atts = shortcode_atts(array(
		'fields' => '',
		'return_msg' => 'Thank you',
		'require' => false,
		'submit_message' => "",
		'style' => 'default'
	) , $atts, 'jgs_input_form');

	$user_id = get_current_user_id();
	$fields = explode(",", $atts['fields']);
	$msg = $atts['submit_message'];
	$require = $atts['require'];
	$style = $atts['style'];

	$form_complete = true; //set as default and if one field is not filled in, change to false
	$submit_value = "Update form";
	foreach ($fields as $field) {
		if (empty(get_field($field, 'user_' . $user_id))) {
			$form_complete = false;
			$submit_value = "Save form";
		}
	}

	$form_attr = array();
	$classes = array();
	$classes[] = "style-" . $style;
	$classes[] = "acf-form";
	$classes[] = $form_complete ? "complete" : "incomplete";

	$form_attr['class'] = implode(" ", $classes);

	if ($require == "all") {
		$form_attr['require'] = 'all';
	}

	$settings = array(
		'fields' => $fields,
		'id' => 'acf_input_form',
		'post_id' => 'user_' . $user_id,
		'html_updated_message' => $msg,
		'form_attributes' => $form_attr,
		'submit_value' => $submit_value
	);

	ob_start();
	acf_form($settings);
	return ob_get_clean();
}

add_shortcode('jgs_input_form', 'jgs_acf_input_form');

//Configure buttons for multiple choice sequences
function jgs_init_multichoice_buttons($atts) {
	$args = shortcode_atts(array(
		'setup' => '',
		'mode' => "respond"
	) , $atts);

	$mode = $args['mode']; //are the questions proactive or in response to a character? (respond/proactive) - implemented in the future
	$buttons_settings = $args['setup'];
	$buttons_settings = explode("|", $buttons_settings);
	$buttons_echo = "";

	foreach ($buttons_settings as $button) {
		$vars = explode(";", $button);
		$button_label = $vars[0];
		$button_target = $vars[1];
		if (empty($vars[2])) {
			$button_js_callback = false;
		}
		else {
			$button_js_callback = $vars[2];
		}
		$buttons_echo .= "<input type='hidden' class='button-settings' data-target='" . $button_target . "' data-js-callback='" . $button_js_callback . "' data-label='" . $button_label . "' style='display: none;'></input>";
	}
	$buttons_echo = "<form class='buttons-settings'>" . $buttons_echo . "</form>";

	return $buttons_echo;
}

add_shortcode('jgs_init_multichoice_buttons', 'jgs_init_multichoice_buttons');

function jgs_button_init($atts) {
	$args = shortcode_atts(array(
		'value' => 'Continue your Journey',
		'callback_func' => '',
	) , $atts);

	$value = $args['value'];
	$callback_func = $args['callback_func'];
	//  call_user_func($call_func);
	if (!empty($callback_func)) {
		$callback_func = " data-function='" . $callback_func . "'";
	}

	return "<span class='button_label_placeholder'" . $callback_func . " style='display:none;'>" . $value . "</span>";
}

add_shortcode("jgs_btn_label", "jgs_button_init");

add_action('wp_ajax_update_lastid', 'jgs_update_lastid');
add_action('wp_ajax_nopriv_update_lastid', 'jgs_update_lastid');

function jgs_update_lastid() {
	$last_id = $_GET['last_id'];
	$user_id = get_current_user_id();
	update_field('jgs_user_data_last_choice_id', $last_id, 'user_' . $user_id);
	die();
}

add_action('wp_ajax_defeat_dragon', 'jgs_defeat_dragon');
add_action('wp_ajax_nopriv_defeat_dragon', 'jgs_defeat_dragon');

function jgs_defeat_dragon() {
	$dragon = $_GET['dragon'];
	$user_id = get_current_user_id();
	if ($dragon == "redfang") {
		update_field('jgs_user_data_redfang_defeated', '1', 'user_' . $user_id);
	}
	else {
		update_field('jgs_user_data_beira_defeated', '1', 'user_' . $user_id);
	}
	die();
}

add_action('wp_ajax_update_heatindex', 'jgs_update_heatindex');
add_action('wp_ajax_nopriv_update_heatindex', 'jgs_update_heatindex');

function jgs_update_heatindex() {
	$type = $_GET['type'];
	$amount = $_GET['amount'];
	$user_id = $_GET['user_id'];
	if ($type === "fire" || $type === "ice") {
		$curr_amount = get_field('jgs_user_data_heat_index_' . $type, 'user_' . $user_id);
		if (!empty($amount)) {
			if (strpos($amount, "+") !== false) { //increase amount relatively
				$amount = str_replace("+", "", $amount); //remove operator
				if ($curr_amount + intval($amount) > 10) {
					$new_amount = 10;
				}
				else {
					$new_amount = $curr_amount + $amount;
				}
				//Update totals for this heat variable
				$total = get_field('jgs_user_data_heat_index_' . $type . '_total', 'user_' . $user_id);
				if (empty($total)) {
					$total = 0;
				}
				update_field('jgs_user_data_heat_index_' . $type . '_total', $total + $amount, 'user_' . $user_id);
			}
			elseif (strpos($amount, "-") !== false) { //reduce amount relatively
				$amount = str_replace("-", "", $amount); //remove operator
				if ($curr_amount - intval($amount) <= 0) {
					$new_amount = 0;
				}
				else {
					$new_amount = $curr_amount - $amount;
				}
			}
			else {
				$new_amount = $amount; //set amount absolutely
				
			}
		}
		update_field('jgs_user_data_heat_index_' . $type, $new_amount, 'user_' . $user_id);
	}
	$echo['type'] = $type;
	$echo['amount'] = $new_amount;
	wp_send_json_success($echo);
	die();
}

/*
function jgs_draco_collection_test() {
    $user_id = get_current_user_id();
    $draco_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);

    $echo = "<p><span class='fusion-dropcap dropcap dropcap-boxed' style='background-color:#1a80b6;'>Y</span>ou reach into your pocket  simple creature like yourself cannot speak with someone as magnificent as Redfang without paying a price course.</p><p>&quot;Since I like you, I will only charge you 10 draco. MY money. <em>Give it to me</em>!&quot; ";
    $echo .= "You fumble through your pockets and come up with " . $draco_balance . " draco. ";

    if($draco_balance < 10 && $draco_balance > 0) {
	   $echo .= "You put the money on the altar.</p>";
	   $echo .= "<p>&quot;I said TEN draco, " . do_shortcode('[ryit_name]') . "! <strong>Give me my money!</strong>&quot;</p>";
    }
    else if($draco_balance > 10) {
	   $echo .= "You put 10 draco on the altar</p>";
    }
    else {
	   $echo .= "</p><p>&quot;I have nothing,&quot; you think to yourself. &quot;Oh shit...&quot;</p>";
	   $echo .= "<p>&quot;So you have nothing? You have NOTHING?!&quot; The last word ripples through the air as a shockwave, knocking the air out of your lungs.";
	   $echo .= "<p>&quot;So you must think you are very special, facing off with Redfang without having followed the instructions of your little friends. You like to do things your own way I see. Yes, you're so grand. So wonderful. Oh yes, I see it! You're so powerful and special, just like me. And you know better than everyone. EVERYONE!</p><p>Why don't people just love you, worship you? Yes, it's a good question, is it not? Why don't they bow at your feet, as they do to mine - for surely no-one is as wonderful as " . do_shortcode('[ryit_name]') . ", the Chosen One who would prevent the Dragon Wars by doing NOTHING AT ALL!&quot;</p><p>Redfang roars with scornful laughter.</p>";
	   $echo .= "<p>The massive dragon puts his giant feet towards the remaining wobbly pillars and pushes. &quot;You had a chance " . do_shortcode('[ryit_name]') . ". You had potential. The Order, the Clan of the Dragon Heart, even my wretched brother believed in you! But I see your true nature. You are one of us. Yes, me and my sister will....recruit you.</p>";
	   $echo .= "<p>Redfang pushes the pillars to the ground and enters the temple through the opening. He looks at you, heat fills your whole body, and then everything goes black.</p>";
    }
    
    return $echo;
}

add_shortcode("jgs_draco_test", "jgs_draco_collection_test");
*/

function jgs_multiple_choice_sequence() {
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		global $post;
		$post_id = $post->ID;
		$user_id = get_current_user_id();
		$ajax = false;
		//echo "AJAX FALSE";
		
	}
	else {
		$is_ajax = true;
		$post_id = $_GET['post_id'];
		$user_id = $_GET['user_id'];
		//echo "AJAX TRUE";
		
	}

	//Determine if AJAX
	if (!$is_ajax) {
		$sequence_ID = get_field('jgs_user_data_dragon_progress', 'user_' . $user_id);
		if (empty($sequence_ID)) {
			$sequence_ID = "redfang-start";
		}
	}
	else {
		$sequence_ID = $_GET['sequence_ID'];
	}

	//Determine which dragon is active
	$active_dragon = "";
	$heatmap_type = "";
	if (strpos($sequence_ID, 'redfang') !== false) {
		$active_dragon = "redfang";
		$heatmap_type = "fire";
	}
	else if (strpos($sequence_ID, 'beira') !== false) {
		$active_dragon = "beira";
		$heatmap_type = "ice";
	}

	$sequence = jgs_init_sequence_contents($post_id, $user_id, $sequence_ID);
	//var_dump($sequence);
	$sequence_content = $sequence['sequence_content'];
	$buttons_html = get_string_between($sequence_content, "<form class='buttons-settings'>", "</form>");
	//var_dump($sequence_content);
	//Set up buttons
	$buttons_array = array();
	while (!empty(strpos($buttons_html, "</input>"))) {
		$needle = "</input>";
		$cut_index = strpos($buttons_html, $needle);
		$buttons_array[] = substr($buttons_html, 0, $cut_index + strlen($needle));
		$buttons_html = substr($buttons_html, $cut_index + strlen($needle));
	}

	$buttons_html = "";
	$buttons_count = count($buttons_array);
	if ($buttons_count > 1) { //Multiple choice response
		foreach ($buttons_array as $button) {
			$button_target = get_string_between($button, "data-target='", "'");
			$button_js_callback = get_string_between($button, "data-js-callback='", "'");
			$button_label = get_string_between($button, "data-label='", "'");
			$buttons_html .= "<option data-target='" . $button_target . "' data-js-callback='" . $button_js_callback . "'>&ndash; " . $button_label . "</option>";
		}
		$buttons_html = "<form id='choice-respond'><select><option>Select an option:</option>" . $buttons_html . "</select></form>";
		$buttons_html .= '<div class="sequence-buttons"></div>'; //placeholder
		
	}
	else { //Single choice
		foreach ($buttons_array as $button) {
			$button_target = get_string_between($button, "data-target='", "'");
			$button_js_callback = get_string_between($button, "data-js-callback='", "'");
			$button_label = get_string_between($button, "data-label='", "'");
			$buttons_html .= "<div class='sequence-button'><a class='button-setting' href='#' data-target='" . $button_target . "' data-js-callback='" . $button_js_callback . "'>" . $button_label . "</a></div>";
		}
		$buttons_html = '<div class="sequence-buttons">' . $buttons_html . '</div>';
		$buttons_html .= '<form id="choice-respond"></form>'; //placeholder
		
	}

	//Retrieve dragon heat map variables
	$ice_level = get_field('jgs_user_data_heat_index_ice', 'user_' . $user_id);
	$ice_level_echo = (isset($ice_level) && $ice_level > 0) ? " level-" . $ice_level : " level-0";

	$fire_level = get_field('jgs_user_data_heat_index_fire', 'user_' . $user_id);
	$fire_level_echo = (isset($fire_level) && $fire_level > 0) ? " level-" . $fire_level : " level-0";

	$dragon_loader = '<div id="dragon-loader" style="display: none;"><div class="dragon-pendant"><div class="dragon-pendant-glow"></div><div class="dragon-pendant-object"></div></div></div>';
	$close_btn = '<div id="choice-sequence-close"><span class="close fa fa-times-circle"></span></div>';
	$redfang_is_defeated = get_field("jgs_user_data_redfang_defeated", "user_" . $user_id) == '1' ? true : false;
	$beira_is_defeated = get_field("jgs_user_data_beira_defeated", "user_" . $user_id) == '1' ? true : false;

	$heatmap_redfang = '<div id="heatmap-redfang" class="heatmap' . $fire_level_echo . ' status-' . ($redfang_is_defeated ? "defeated" : "alive") . '"><img src="' . get_site_url() . '/wp-content/uploads/2018/03/jgs-red-dragon-1.jpg" /><div class="heatbar"><span class="marker"></span><span class="fill"></span></div></div>';

	$heatmap_beira = '<div id="heatmap-beira" class="heatmap' . $ice_level_echo . ' status-' . ($beira_is_defeated ? "defeated" : "alive") . '"><img src="' . get_site_url() . '/wp-content/uploads/2018/03/beira-white-dragon.jpg" /><div class="heatbar"><span class="marker"></span><span class="fill"></span></div></div>';
	$heatmap = '<div id="heatmaps" class="' . $active_dragon . '">' . $heatmap_beira . $heatmap_redfang . '</div>';

	if (!$is_ajax) { //will run the first time the shortcode is displayed
		return $dragon_loader . "<div id='choice-sequence-bg' class='hide'></div><div id='choice-sequence' class='hide'>" . $close_btn . "<div class='wrap'><div class='sequence-element' id='" . $sequence_ID . "' active_dragon='" . $active_dragon . "'>" . $sequence_content . "</div></div>" . $buttons_html . $heatmap . "</div></div>";
	}
	else {
		update_field('jgs_user_data_dragon_progress', $sequence_ID, 'user_' . $user_id);
		$ajax_echo['content'] = "<div class='sequence-element' id='" . $sequence_ID . "' active_dragon='" . $active_dragon . "'>" . $sequence_content . "</div>";
		$ajax_echo['buttons'] = $buttons_html;
		$ajax_echo['dragon'] = $active_dragon;
		//$ajax_echo['ice'] = $heatmap_beira; */
		// . $buttons_html . $heatmap . "</div>";
		wp_send_json_success($ajax_echo);
	}
}

add_shortcode("jgs_multiple_choice_sequence", "jgs_multiple_choice_sequence");

add_action('wp_ajax_update_sequence', 'jgs_multiple_choice_sequence');
add_action('wp_ajax_nopriv_update_sequence', 'jgs_multiple_choice_sequence');

function jgs_init_sequence_contents($post_id, $user_id, $sequence_ID) {
	//retrieve multiple choice sequences
	$sequences = get_field('jgs_choice_sequence', $post_id);

	//extract the correct sequence
	foreach ($sequences as $sequence) {
		if ($sequence['sequence_ID'] == $sequence_ID) {
			$sequence_content = $sequence['sequence_content'];

			$advanced = $sequence['sequence_enable_advanced'];
			if (!empty($advanced)) {
				$settings = $sequence['advanced_controls'];
				$php_function_calls = $settings['php_function_call'];
				//var_dump($php_function_calls);
				foreach ($php_function_calls as $php_call) {
					$php_call = $php_call['php_function'];
					$arg = get_string_between($php_call, "(", ")");
					$func = substr($php_call, 0, strpos($php_call, "("));
					//call_user_func_array($func,array($arg,$user_id));
					
				}
			}
			break;
		}
	}
	return array(
		'sequence_content' => $sequence_content,
		'image_echo' => $image_echo
	);
}

function get_string_between($string, $start, $end, $inclusive = false) {
	$string = " " . $string;
	$ini = strpos($string, $start);
	if ($ini == 0) return "";
	if (!$inclusive) $ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
	if ($inclusive) $len += strlen($end);
	return substr($string, $ini, $len);
}

function jgs_init_dragon() {
	$user_id = get_current_user_id();
	$progress = get_field('jgs_user_data_dragon_progress', 'user_' . $user_id);
	if (!empty($progress)) {
		$label = "Return to the Dragons";
	}
	else {
		$label = "Face the Dragons";
	}

	return "<div class='fusion-button-wrapper fusion-aligncenter'><a href='#' data-callback='init_dragon' class='fusion-button button-3d fusion-button-round button-medium button-default button-1 simple'>" . $label . "</a></div>";
}

add_shortcode("jgs_init_dragon", "jgs_init_dragon");

function jgs_get_user_var($atts, $content = null) {
	$user_id = get_current_user_id();
	$args = shortcode_atts(array(
		'field' => '',
	) , $atts);

	$field = $args['field'];

	return get_field($field, 'user_' . $user_id);
}

add_shortcode("jgs_get_user_var", "jgs_get_user_var");

function jgs_dragon_conclusion() {
	$user_id = get_current_user_id();

	$ice_total = get_field('jgs_user_data_heat_index_ice_total', 'user_' . $user_id);
	$fire_total = get_field('jgs_user_data_heat_index_fire_total', 'user_' . $user_id);

	$echo = "<h3>Redfang battle summary</h3>";
	$echo .= "<p>Your total fire index is " . $fire_total . ".</p>";
	if ($fire_total < 3) {
		$echo .= "<p>This is a very low score and if you have been honest, you are not in any danger of being possessed by Redfang in the future. However, watch out so you don't become a pushover, for Redfang has his way of taking hold in even the sweetest of people.</p>";
	}
	else if ($fire_total >= 3 && $fire_total <= 5) {
		$echo .= "<p>This is a medium score and if you have been honest, you are not in any danger of being possessed by Redfang in the future.</p>";
	}
	else if ($fire_total > 6 && $fire_total <= 9) {
		$echo .= "<p>This is a fairly high score, and you may need to watch out for feelings of specialness, judgment, arrogance and grandiosity. Also watch out for your tendency to repress your vulnerability and sensitivity.</p>";
	}
	else if ($fire_total > 9) {
		$echo .= "<p>This is a high score, and you will likely be prone to judgmental thoughts, feeling special, better than others. You likely also repress your vulnerability and sensitivity a lot. Keep doing the practices of regulating archetypal energy, and you will do fine. Good luck!</p>";
	}
	$echo .= "<h3>Beira battle summary</h3>";
	$echo .= "<p>Your total ice index is " . $ice_total . ".</p>";

	if ($ice_total < 3) {
		$echo .= "<p>Beira's magic does not have much power over you and you are safe (or in denial).</p>";
	}
	else if ($ice_total >= 3 && $ice_total <= 5) {
		$echo .= "<p>This is a medium score and if you have been honest, there is no immediate danger of you being possessed by Beira in the future, but you should be mindful not to buy into the stories of your wounds and victimhood too much (by using practices in Marion's Box that build your power and fortitude).</p>";
	}
	else if ($ice_total > 6 && $ice_total <= 9) {
		$echo .= "<p>This is a fairly high score, so watch out for the pull of unconsciousness, of sinking into apathy and sleep, and waiting to be saved by caretakers. Make sure to use practices to build your power and resilience.</p>";
	}
	else if ($ice_total > 9) {
		$echo .= "<p>This is a very high score, and Beira (as the Devouring Mother) really has her teeth in you. You must be careful to do practices that build your vitality, your sense of order and direction, and that you take responsibility for your life. Also: Start forgiving people. Good luck, you will do fine.</p>";
	}

	return $echo;
}

add_shortcode("jgs_dragon_conclusion", "jgs_dragon_conclusion");

function jgs_dragon_pendant() {
	$user_id = get_current_user_id();

	$redfang_defeated = get_field('jgs_user_data_redfang_defeated', 'user_' . $user_id);
	$beira_defeated = get_field('jgs_user_data_beira_defeated', 'user_' . $user_id);

	$echo = "";
	if (!$redfang_defeated || !$beira_defeated) {
		$echo .= '<div class="dragon-pendant">';
		$echo .= '<div class="dragon-pendant-glow"></div>';
		$echo .= '<div class="dragon-pendant-object"></div>';
		$echo .= '</div>';
		$echo .= '<div class="fusion-title title fusion-sep-none fusion-title-center fusion-title-size-three fusion-border-below-title" style="margin-top:20px;margin-bottom:5px;"><h3 class="title-heading-center"><p style="text-align: center;">Master Cirrus’s dragon pendant glows</p></h3></div>';
		$echo .= '<p style="text-align: center;"><span style="color: #ffffff;"><em>They have arrived...</em></span></p>';
	}
	else {
		$echo .= '<div class="dragon-pendant">';
		$echo .= '<div class="dragon-pendant-object"></div>';
		$echo .= '</div>';
		$echo .= '<div class="fusion-title title fusion-sep-none fusion-title-center fusion-title-size-three fusion-border-below-title" style="margin-top:20px;margin-bottom:5px;"><h3 class="title-heading-center"><p style="text-align: center;">Master Cirrus’s dragon pendant lays dormant</p></h3></div>';
		$echo .= '<p style="text-align: center;"><span style="color: #ffffff;"><em>Redfang and Beira have fled</em></span></p>';
	}

	return $echo;
}

add_shortcode('jgs_dragon_pendant', 'jgs_dragon_pendant');

/*
function jgs_exit_dragon_sequence() {

}

add_shortcode("jgs_exit_dragon_sequence", "jgs_exit_dragon_sequence");
*/

function jgs_numeric_user_var_compare($atts, $content = null) {
	$user_id = get_current_user_id();
	$args = shortcode_atts(array(
		'val' => 0,
		'field' => '',
		'op' => false,
	) , $atts);

	$op = $args['op'];
	$val = $args['val'];
	$val = strpos($val, "|") ? $val : intval($val); //treat values without pipe as integers
	$stored_val = get_field($args['field'], 'user_' . $user_id);

	if (empty($stored_val)) $stored_val = 0;

	if (!$op) { //break if no operator is set
		return false;
	}
	else {
		if ($op == "lt") {
			if ($stored_val < $val) {
				$content = do_shortcode($content);
			}
			else {
				return false;
			}
		}
		elseif ($op == "lte") {
			if ($stored_val <= $val) {
				$content = do_shortcode($content);
			}
			else {
				return false;
			}
		}
		elseif ($op == "gt") {
			if ($stored_val > $val) {
				$content = do_shortcode($content);
			}
			else {
				return false;
			}
		}
		elseif ($op == "gte") {
			if ($stored_val >= $val) {
				$content = do_shortcode($content);
			}
			else {
				return false;
			}
		}
		elseif ($op == "eq") {
			if ($stored_val == $val) {
				$content = do_shortcode($content);
			}
			else {
				return false;
			}
		}
		elseif ($op == "ne") {
			if ($stored_val != $val) {
				$content = do_shortcode($content);
			}
			else {
				return false;
			}
		}
		elseif ($op == "between") {
			if (!empty($val)) {
				$values = explode("|", $val);
				if ($stored_val >= intval($values[0]) && $stored_val <= intval($values[1])) {
					$content = do_shortcode($content);
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	return $content;
}

add_shortcode('jgs_usr_var_comp', 'jgs_numeric_user_var_compare');
add_shortcode('jgs_usr_var_innercomp', 'jgs_numeric_user_var_compare');

/* ************************** */
/*      CHALLENGE SYSTEM      */
/* ************************** */

function jgs_open_challenge_system($atts) {
	$atts = shortcode_atts(array(
		'filter' => '',
	) , $atts, 'jgs_open_challenge_system');

	$filter = $atts['filter'];

	if (empty($filter)) {
		return "<a href='#' class='jgs_challenge_box'><img src='/wp-content/uploads/2018/01/jgs-magic-box-150x100.png' /></a>";
	}
	else {
		return "<a href='#' class='jgs_challenge_box' filter_name='" . $filter . "'><img src='/wp-content/uploads/2018/01/jgs-magic-box-150x100.png' /></a>";
	}

}

add_shortcode('jgs_marions_box', 'jgs_open_challenge_system');

function ryit_archetype_challenge_post_type() {

	$supports = array(
		'title'
	);

	$labels = array(
		'name' => 'Challenge',
		'singular_name' => 'Challenge',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Challenge',
		'edit_item' => 'Edit Challenge',
		'new_item' => 'New Challenge',
		'view_item' => 'View Challenge',
		'search_items' => 'Search Challenges',
		'not_found' => 'No challenges found',
		'not_found_in_trash' => 'No challenges found in Trash',
		'parent_item_colon' => '',
		'menu_name' => 'Challenges'
	);

	$args = array(
		'label' => 'Challenges',
		'public' => true,
		'show_ui' => true,
		'hierarchial' => false,
		'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
		'show_in_menu' => true,
		'menu_position' => 5,
		'rewrite' => array(
			'slug' => 'challenge'
		) ,
		'supports' => $supports,
		'labels' => $labels
	);

	register_post_type('kwml_challenge', $args);
}

add_action('init', 'ryit_archetype_challenge_post_type');

function ryit_archetype_challenge_taxonomy() {

	$labels = array(
		'name' => _x('Challenge type', 'taxonomy general name', 'textdomain') ,
		'singular_name' => _x('Challenge type', 'taxonomy singular name', 'textdomain') ,
		'search_items' => __('Search types', 'textdomain') ,
		'all_items' => __('All Challenge Types', 'textdomain') ,
		'edit_item' => __('Edit Type', 'textdomain') ,
		'update_item' => __('Update Type', 'textdomain') ,
		'add_new_item' => __('Add New Type', 'textdomain') ,
		'new_item_name' => __('New Type Name', 'textdomain') ,
		'menu_name' => __('Challenge Type', 'textdomain') ,
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array(
			'slug' => 'challenge_type'
		) ,
	);

	register_taxonomy('kwml_challenge_type', array(
		'kwml_challenge'
	) , $args);
}

add_action('init', 'ryit_archetype_challenge_taxonomy');

function ryit_archetype_challenge_benefit_taxonomy() {

	$labels = array(
		'name' => _x('Challenge Benefit', 'taxonomy general name', 'textdomain') ,
		'singular_name' => _x('Challenge Benefit', 'taxonomy singular name', 'textdomain') ,
		'search_items' => __('Search Benefits', 'textdomain') ,
		'all_items' => __('All Benefit Types', 'textdomain') ,
		'edit_item' => __('Edit Benefit', 'textdomain') ,
		'update_item' => __('Update Benefit', 'textdomain') ,
		'add_new_item' => __('Add New Benefit', 'textdomain') ,
		'new_item_name' => __('New Benefit Name', 'textdomain') ,
		'menu_name' => __('Benefit Type', 'textdomain') ,
	);

	$args = array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array(
			'slug' => 'benefit'
		) ,
	);

	register_taxonomy('kwml_benefit', array(
		'kwml_challenge'
	) , $args);
}

add_action('init', 'ryit_archetype_challenge_benefit_taxonomy');

add_action('init', 'get_kwml_terms');

function get_kwml_terms() {
	$terms = get_terms('kwml_challenge_type', array(
		'hide_empty' => true
	));
	//return "<pre>" . print_r($terms) . "</pre>";
	$kwml_terms = array();
	foreach ($terms as $term) {
		if ($term->parent != 0) {
			$kwml_terms[] = array(
				'slug' => $term->slug,
				'parent' => $term->parent
			);
		}
	}
	return $kwml_terms;
}

function ryit_list_kwml_challenges($user_id, $filter) {

	// Get the categories for post and product post types
	$terms = get_kwml_terms();

	//Get challenge posts filtered on terms
	foreach ($terms as $term) {
		$args = array(
			'posts_per_page' => - 1,
			'orderby' => 'title',
			'order' => 'ASC',
			'post_type' => 'kwml_challenge',
			'tax_query' => array(
				array(
					'taxonomy' => 'kwml_challenge_type',
					'terms' => $term['slug'],
					'field' => 'slug',
				)
			)
		);
		$kwml_challenges[$term['slug']] = array(
			'parent' => $term['parent'],
			'posts' => get_posts($args)
		);
	}
	$user_id = get_current_user_id();
	$reward_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
	$reward_balance = empty($reward_balance) ? 0 : $reward_balance;

	$output = '<div id="challenges_overlay"><div class="close_wrap"><a class="close"><span class="fa fa-times"></a></div>';
	if ($filter == "fight-redfang") {
		$output .= '<span id="reward_balance">Dragon Power: <strong>' . get_field('jgs_user_data_heat_index_fire', 'user_' . $user_id) . '</strong></span>';
	}
	if ($filter == "fight-beira") {
		$output .= '<span id="reward_balance">Dragon Power: <strong>' . get_field('jgs_user_data_heat_index_ice', 'user_' . $user_id) . '</strong></span>';
	}
	$output .= '<div id="challenges"' . (empty($filter) ? '' : ' class="' . $filter . '"') . '><div class="scrollwrap">';

	//Loop through challenge objects
	foreach ($kwml_challenges as $challenge_type => $challenge) {

		$challenge_parent = $challenge['parent']; //assign archetype
		$challenge_parent = get_term_by('id', $challenge_parent, 'kwml_challenge_type');

		//return $challenge_parent->slug;
		//$challenge_parent_slug = $challenge_parent['slug'];
		if ($challenge_parent->slug == "magician") {
			$icon = "ra-crystal-wand";
		}
		else if ($challenge_parent->slug == "warrior") {
			$icon = "ra-sword";
		}
		else if ($challenge_parent->slug == "sovereign") {
			$icon = "ra-crown";
		}
		else if ($challenge_parent->slug == "lover") {
			$icon = "ra-hearts";
		}

		$complete_label = "Yes, I did it";

		switch ($filter) {
			case "fight-redfang":
				$complete_label = "Strike Redfang";
			break;
			case "fight-beira":
				$complete_label = "Strike Beira";
			break;
		}

		$cat_echo = "";

		if (!empty($filter)) {
			$tag_filter_match = false;
			$tag_filter_matches = 0;
		}
		foreach ($challenge['posts'] as $post) {
			$tags = wp_get_post_terms($post->ID, 'kwml_benefit');
			if ($tags) {
				$tags_echo = '<ul class="tags">';
				$tags_classes = "";
				foreach ($tags as $tag) {
					if (!empty($filter) && $tag->slug == $filter) {
						$tag_filter_match = true;
						$tag_filter_matches++;
					}
					if (strpos($tag->name, 'fight-') !== false) {
						continue; //challenge has filter related to dragon encounter. skip this tag from listing
						
					}
					$tags_echo .= "<li class='" . $tag->slug . "' tag_name='" . $tag->name . "'>" . $tag->name . "</li>";
					$tags_classes .= " " . $tag->slug;
				}
				$tags_echo .= "</ul>";
			}
			else {
				$tags_echo = "";
			}
			if (!empty($filter) && !$tag_filter_match) {
				continue;
				$tag_filter_match = false;
			}
			else {
				$tag_filter_match = false;
			}

			$intensity = get_field('kwml_challenge_enable_intensity', $post->ID);
			$intensity_values = get_field('kwml_challenge_intensity_values', $post->ID);
			$intensity_text = ($intensity) ? $intensity_values['intensity_medium'] : "";
			$description = get_field('kwml_challenge_description', $post->ID);

			if ($intensity) {
				$description = str_replace('[intensity_text]', '<strong>' . $intensity_text . '</strong>', $description);
			}

			$cat_echo .= '<li class="challenge' . $tags_classes . '" value="' . get_field('kwml_challenge_reward', $post->ID) . '">';
			$cat_echo .= '<div class="header"><h2>' . get_the_title($post->ID) . '</h2><span class="reward">' . get_field('kwml_challenge_reward', $post->ID) . '</span><span class="toggle">Click for Info</span><span class="archetype ra ' . $icon . '"></span></div>';
			$cat_echo .= '<div class="info">' . $description . '</div>';
			$cat_echo .= '<div class="meta">' . $tags_echo . '<a href="#" id="kwml_complete_challenge">' . $complete_label . '</a></div>';
			$cat_echo .= '</li>';
		}

		if ($prev_challenge_type != $challenge_type) {
			$term = get_term_by('slug', $challenge_type, 'kwml_challenge_type', 'ARRAY_A');
			$challenge_type_name = $term['name'];
			if (empty($filter)) {
				$output .= '<h3>' . $challenge_type_name . '</h3><ul class="challenge_cat">' . $cat_echo;
			}
			else {
				if ($tag_filter_matches > 0) {
					$output .= '<h3>' . $challenge_type_name . '</h3><ul class="challenge_cat">' . $cat_echo;
				}
			}
			$cat_echo = "";
			$output .= '</ul>'; // end challenge cat
			
		}

		$prev_challenge_type = $challenge_type;
	}

	$output .= '</div></div></div>';
	return $output;
}

add_action('wp_ajax_update_reward_balance', 'ryit_update_reward_balance_ajax');
add_action('wp_ajax_nopriv_update_reward_balance', 'ryit_update_reward_balance_ajax');

function ryit_update_reward_balance_ajax() {
	if (!empty($_GET['user_id'])) {
		$user_id = $_GET['user_id'];
	}
	if (!empty($_GET['value'])) {
		$challenge_value = $_GET['value'];
	}
	$curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
	$curr_balance = empty($curr_balance) ? 0 : $curr_balance;
	$new_balance = intval($curr_balance) + intval($challenge_value);
	if ($new_balance < 0) $new_balance = 0;
	update_field('ryit_user_currency_balance', $new_balance, 'user_' . $user_id);
	echo $new_balance;
	die();
}

function ryit_update_reward_balance($balance, $user_id = false) {
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		//Not AJAX
		$user_id = get_current_user_id();
	}

	if (substr($balance, 0, 1) == "+") {
		$val_increment = intval(substr($balance, 1));
		$curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
		update_field('ryit_user_currency_balance', $curr_balance + $val_increment, 'user_' . $user_id);
	}
	elseif (substr($balance, 0, 1) == "-") {
		$val_increment = intval(substr($balance, 1));
		$curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
		if (($curr_balance - $val_increment) < 0) {
			update_field('ryit_user_currency_balance', 0, 'user_' . $user_id);
		}
		else {
			update_field('ryit_user_currency_balance', $new_balance - $val_increment, 'user_' . $user_id);
		}
	}
	else {
		update_field('ryit_user_currency_balance', $balance, 'user_' . $user_id);
	}
}


//Ajax update of Fragments form
add_action('wp_ajax_display_coaching_system', 'jgs_display_coaching_system');
add_action('wp_ajax_nopriv_display_coaching_system', 'jgs_display_coaching_system');

function jgs_display_coaching_system() {
	$filter = $_GET['filter'];
	$user_id = get_current_user_id();
	$output = ryit_list_kwml_challenges($user_id, $filter);
	if ($output) {
		$ajax_output = array();
		$ajax_output[] = $output;
		echo json_encode($ajax_output);
	}
	else {
		echo "No output";
	}
	die();
}


?>