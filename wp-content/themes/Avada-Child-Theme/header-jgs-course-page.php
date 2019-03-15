<?php
/**
 * Header template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<!DOCTYPE html>
<html class="<?php echo ( Avada()->settings->get( 'smooth_scrolling' ) ) ? 'no-overflow-y' : ''; ?>" <?php language_attributes(); ?>>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<style type="text/css">
		/* Preloader hiding */
		div.fusion-fullwidth, div.fusion-column-wrapper, div[elem-type="popup"] {
			visibility: hidden;
			height: 1px;
		}

		#jgs_loader {
			display: block;
			margin: 0 auto;
			font-size: 28px;
			text-align: center;
		}
	</style>


	<?php wp_enqueue_script("jquery-cookie", get_stylesheet_directory_uri().'/js/jquery-cookie/jquery_cookie.js', array(), '0'); ?>
	<?php Avada()->head->the_viewport(); ?>
	<?php wp_head(); ?>

	<?php $object_id = get_queried_object_id(); ?>
	<?php $c_page_id = Avada()->fusion_library->get_page_id(); ?>

	<?php
		//important PHP variables
		$user_id = get_current_user_id();
	?>
	<script type="text/javascript">
		var doc = document.documentElement;
		doc.setAttribute('data-useragent', navigator.userAgent);
		
		//Set up AJAX 
		var jgs = {};
		jgs.ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
		
		jQuery('document').ready(function($j){

			//Initialize variables
			var curr_week = $j('body').attr('week_id');
			var progress_week = $j('body').attr('progress_week');
			var progress_step = $j('body').attr('progress_step');

			function update_step(step) {
				progress_step = step; //assign the correct value to variable
				$j('body').attr('progress_step', step);
			}

			//re-enable content sections after load is complete
			$j('div.fusion-fullwidth').css('visibility', 'visible');
			$j('div.fusion-fullwidth').css('height', 'auto');
			$j('div.fusion-column-wrapper').css('visibility', 'visible');
			$j('div.fusion-column-wrapper').css('height', 'auto');
			$j('#jgs_loader').hide();

			//Initialize visibility
			if(progress_week > curr_week) { //Participant is further than open week
				$j(".fusion-fullwidth[class*=step]").each(function() {
					$j(this).addClass('show');
					$j('.continue-button').remove();
				});
			}
			else if (progress_week === curr_week) { //Participant progress matches with this week
				i = 0;
				if(progress_step !== 'undefined') { //User has already started this week
					while(i <= progress_step) {
						$j('.step-' + i).addClass('show'); //show completed steps
						$j('.continue-button[step=' + i + ']').parents('.fusion-fullwidth').remove(); //remove irrelevant buttons from HTML altogether (though they were already hidden)
						i++;
					}
					$j('.continue-button[step=' + i + ']').addClass('show'); //show proceed button
				}
				else {
					$j('.continue-button[step=1]').addClass('show');
				}
			}
			else { //Participant has not come this far
				$j('.post-content .fusion-fullwidth:first-of-type').before('<h3 style="text-align: center; margin: 0 0 15px 0;">Complete the previous week first</h3><p style="text-align: center; max-width: 350px; margin: 0 auto 150px;">This week opens when you click through to the end of the previous one.</p>');
				$j('.post-content .fusion-fullwidth:first-of-type').hide();
			}


			//Initialize UI elements
			$j('#account').removeClass('hide');
			$j('#account .toggle').fadeIn(2000);
			if(curr_week <= progress_week) {
				if($j('.step-final').css('display') == 'block') {
					setup_wolf('Continue to the next Week', true);
				}
				else {
					var label = $j('.step-' + progress_step).find('.button_label_placeholder').text();
					if(label === null || label === undefined || label === '') {
						var curr_step = $j('body').attr('progress_step');
						var next_step = $j('.step-' + (parseInt(curr_step) + 1));
						if(next_step.length > 0 && ! next_step.hasClass('admin')) {
							label = "Continue your Journey"; 	
						}
						else {
							label = "Next step not yet ready...";
						}
					}
					setup_wolf(label);
				}
			}

			
			//Place wolf icon
			function setup_wolf(label,is_last) {
				var curr_step = $j('body').attr('progress_step');
				if(label === undefined || label === '') { //Set up label
					if($j('.step-' + (parseInt(curr_step) + 1)).length > 0) {
						label = "Contine your Journey"; 	
					}
					else {
						label = "Content not ready";
					}
				}

				//Check if button should have callback function (this is implemented in a sloppy way; better improve later by renaming shortcode from jgs_btn_label to jgs_init_button)
				var callback_function = $j('.step-' + (parseInt(curr_step)) + ' .button_label_placeholder').attr('data-function');

				var baseurl = "/courses/jgs/";
		        var slug = "";
		        var html_string = "";
		        var url = "";
				if($j('#wolf_button').length <= 0) { //create button
         			if(is_last) { slug = $j('body').attr('next_slug'); }
					url = (slug !== "") ? (baseurl + slug) : '#';
					html_string = '<div id="wolf_button"';
					html_string += (callback_function === undefined || callback_function === "") ? '>' : ' data-callback="' + callback_function + '">';
					html_string += '<a href="' + url + '"><span>' + label + '</span><img src="/wp-content/uploads/2017/11/wolf-symbol.png" id="wolf" /></a></div>';
					$j('section').append(html_string);	
				}
				else { //update button
					console.log("update button");
					if(is_last) { slug = $j('body').attr('next_slug'); }
					url = (slug !== "") ? (baseurl + slug) : '#';
					html_string = '<a href="' + url + '"><span>' + label + '</span><img src="/wp-content/uploads/2017/11/wolf-symbol.png" id="wolf" /></a>';
					$j('#wolf_button').html(html_string);
					if(callback_function !== undefined && callback_function !== "") {
						$j('#wolf_button').attr('data-callback',callback_function);
					}
					else {
						$j('#wolf_button').removeAttr('data-callback');
					}
				}
			}


			//Interface events

			$j('#account .toggle').on('click', function() {
				if($j(this).parent().hasClass('closed')) {
					$j(this).parent().removeClass('closed');
				} else {
					$j(this).parent().addClass('closed');
				}
			});

			var echo_system_msg = "The ECHO-system awaits you...";

			$j('#commentform #jgs_echo_comment').on('focus', function() {
				$j(this).text(''); 
			});

			$j('#commentform #jgs_echo_comment').on('blur', function() {
				if( $j(this).text() === "" ) {
					$j(this).text(echo_system_msg);
				}
			});


			//Set up form pointers
			var commentform = $j('#commentform');
			commentform.prepend('<div id="comment-status" ></div>');


			//Post form
			commentform.submit(function(e) {
				e.preventDefault();
				var formdata = commentform.serialize();	
				var formurl = commentform.attr('action');
				var field_value = $j('#jgs_echo_comment').val();
				var minimum_length = 700;

				if(field_value !== "" && field_value !== echo_system_msg) { 
					if(field_value.length > minimum_length) {
						jgs_status_popup('<p>Reviewing your submission to the ECHO-system</p>','show',0,true);
						$j.ajax({			   
							type: 'post', // the kind of data we are sending
							url: formurl, // this is the file that processes the form data
							data: formdata, // this is our serialized data from the form
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								statusdiv.html('<p class="wdpajax-error" >You might have left one of the fields blank, or be posting too quickly</p>');
							},
							success: function(data, textStatus) {
								if(data == "success") {
									jgs_status_popup("<p>Your comment has been accepted to the ECHO-system. Now, review the character's response.</p>",'show',0,false);
									$j('body #respond').fadeOut(500);
									getResponse();
								}
								else {
									jgs_status_popup('<p>Your comment was not approved</p>','show',0,false);
									commentform.find('textarea[name=comment]').val('');
								}
							}
						});
					}
					else {
						console.log(field_value.length);
						if(field_value.length < (minimum_length/2)) {
							console.log("short");
							jgs_status_popup($j('#echo_length_responses').attr('response_short'),"show",0,false);
						}
						else if (field_value.length < (minimum_length*0.75)){
							console.log("medium");
							jgs_status_popup($j('#echo_length_responses').attr('response_medium'),"show",0,false);
						}
						else {
							console.log("large");
							jgs_status_popup($j('#echo_length_responses').attr('response_long'),"show",0,false);
						}
					}
				}
				else {
					jgs_status_popup($j('#echo_length_responses').attr('response_none'),"show",0,false);
				}
			});
			//End commentform AJAX


			//JGS Status popup
			function jgs_status_popup(message,toggle,delay,modal) {
				if(modal === "" || modal === undefined) modal = true; //modal is default

				//console.log("Modal is " + modal);

				if(message !== 'undefined' && message !== 'hide' && toggle !== 'hide') { //Open the popup
					console.log("open popup");
					if($j('#jgs_status_popup').length <= 0) { //Create new element
						if(modal) { 
							$j('body').prepend('<div id="jgs_status_popup" class="modal"><div class="wrap"></div></div>');
						}
						else {
							$j('body').prepend('<div id="jgs_status_popup"><div class="wrap"></div></div>');
						}
					}
					else { //update existing element
						if(modal) { 
							$j('#jgs_status_popup').addClass('modal');
						}
						else {
							$j('#jgs_status_popup').removeClass('modal');
						}
					}

					var jgs_popup = $j('#jgs_status_popup');
					jgs_popup.children('.wrap').html(message);

					if(delay !== 'undefined') {
						if(jgs_popup.css('display') !== 'block') {						
							jgs_popup.delay(delay).fadeIn(500);
						}
					}
					else {
						if(jgs_popup.css('display') !== 'block') {
							jgs_popup.fadeIn(500);
						}
					}
				}
				else { //Close the popup
					//console.log("close popup");
					var jgs_popup = $j('#jgs_status_popup');

					//interrupt any delayed fadeout and trigger immediately
					jgs_popup.clearQueue();
					jgs_popup.stop();

					if(modal) {
						jgs_popup.addClass('modal');
					}
					else {
						jgs_popup.removeClass('modal');		
					}

					if(message !== 'undefined') {
						jgs_popup.children('.wrap').html(message);	
					}

					if(delay !== 'undefined') {
						if(jgs_popup.css('display') === 'block') {
							jgs_popup.delay(delay).fadeOut(500);
						}
					}
					else {
						if(jgs_popup.css('display') === 'block') {
							jgs_popup.delay(delay).fadeOut(500);
						}
					}
				}
			}


			$j("#wolf_button").on("click", function(e) {
				if($j('div.step-' + progress_step).find('.button_label_placeholder').length > 0)	 {
					var callback_function = $j('div.step-' + progress_step).find('.button_label_placeholder').attr('data-function');
					if(callback_function != null) {
						jgs_funcs[callback_function]();
						return false;
					}
				}
				e.preventDefault();
				jgs_shift_page_section($j(this));	
			});


			function jgs_shift_page_section(trigger) {
				console.log("shift page section");
				if(trigger != null) {
					if(trigger.children('a').attr('href') === "#") { //Link to new section on same page
						//console.log("default");
						step = progress_step;
						week_id = curr_week;
						step++;
						if($j('div.step-' + step).first().hasClass('step-final')) { //Last section is active
							setup_wolf('Continue to next Week', true);
							update_step(step);
							$j(".step-" + step).fadeIn(1000);
							updateProgress(week_id,step);
						} //Any section which is not the last
						else {
							if($j('div.step-' + step).length <= 0) {
								setup_wolf('Sorry, no dice!');		
							}
							else {
								//console.log("should be here");
								if($j('.step-' + step).length > 0) {
									$j(".step-" + step).fadeIn(1000);
									var label = $j('.step-' + step).find('.button_label_placeholder').text();
									if(label === null || label === undefined || label === '') label = "Continue your Journey";
									update_step(step);
									updateProgress(week_id,step);
									setup_wolf(label);
								}
							}
						}
					}
					else { //Link to next week
						e.preventDefault();
						var url = trigger.children('a').attr('href');
						if(url !== "#") {
							week_id = curr_week;
							setup_wolf('Please wait...',true);
							if(curr_week < progress_week) { //Just shift to next week, don't update progress
								window.location = url;
							}
							else {			
								updateProgress(++week_id,0,url); //Next week has not yet been visited; update progress
							}
						}
						else {
							setup_wolf('Week not yet launched...', true);
						}
					}		
				}	
				else {
					week_id = curr_week;
					step = progress_step;
					step++;
					var label = $j('.step-' + step).find('.button_label_placeholder').text();
					if(label === null || label === undefined || label === '') label = "Continue your Journey";
					update_step(step);
					updateProgress(week_id,step);
					setup_wolf(label);
					$j('#choice-sequence').removeClass();
					$j('#choice-sequence-bg').removeClass();
				}	
			}


			$j(document).on("click", '.tip-wrap', function(e) {
				console.log("click");
				$j(this).addClass('show');
				$j(this).find('.tip-bg').addClass('show');
				$j(this).find('.content').addClass('show');
			});

			$j(document).on("click", '.tip-wrap.show', function(e) {
				$j(this).removeClass('show');
				$j(this).find('.tip-bg').removeClass('show');	
				$j(this).find('.content').removeClass('show');
			});

			$j(document).on("click", '#jgs_status_popup', function(e) {
				e.preventDefault();	
				if($j(this).hasClass('modal')) {
					//do nothing
				}
				else {
					$j('#jgs_status_popup').clearQueue();
					$j('#jgs_status_popup').fadeOut(500);
				}
			});


			$j(window).scroll(function(){
				if(scrolledBelowElement($j('.fusion-title-sc-wrapper'))){
					$j('#jgs_gaming_elements').addClass('show');
				}
				else {
					$j('#jgs_gaming_elements').removeClass('show');
				}
			});


			<?php 
				$profile = get_field('jgs_user_profile', 'user_' . $user_id);
				$autoplay = $profile['autoplay_audio']; //_field('field_5a5a27159affc', 'user_' . $user_id);

				$music = get_field('jgs_background_music'); 
				if($music) :
			?>

		    $j("#jquery_jplayer_1").jPlayer({
		        ready: function(event) {
		            $j(this).jPlayer("setMedia", {
						//title: "Bubble",
						m4a: "https://jgs-resources.s3.amazonaws.com/<?php echo $music; ?>.mp3",
						oga: "https://jgs-resources.s3.amazonaws.com/<?php echo $music; ?>.ogg"
		            })<?php echo ( $autoplay == true ) ? ".jPlayer('play')" : ""; ?>
		        },
		        swfPath: "http://jplayer.org/latest/dist/jplayer",
		        supplied: "mp3, oga",
				wmode: "window",
				useStateClassSkin: true,
				autoBlur: false,
				smoothPlayBar: true,
				keyEnabled: true,
				remainingDuration: true,
				toggleDuration: true,				
		    });
		
			<?php endif; ?>

			//fade in user interface
			$j('#jgs_interface').removeClass('hide');
			$j('#jgs_interface').fadeIn(3000);

			//AJAX saving of Fragments form
			
			$j('form.acf-form#acf_fragments_form :submit').click(function(event){
				event.preventDefault();
				var form_data = {'action' : 'acf/validate_save_post'};

				var required_fragments = 4;
				var i = 1;
				var form_valid = false;
				$j('form.acf-form#acf_fragments_form :input').each(function(){
					val = $j(this).val();
					form_data[$j(this).attr('name')] = val;
					if($j(this).attr('type') === 'text') {
						if(val !== 'undefined' && val !== '') {
							form_valid = true;
						}
						else {
							if(i<=required_fragments) {
								form_valid = false;
								jgs_status_popup('<p>Please fill in the first ' + required_fragments + ' fields minimum.</p>');
								return false;
							}
						}
						i++;
					}
				});

				if(form_valid) {
					jgs_status_popup("<p>We're reviewing your fragments.</p>");
					form_data.action = 'save_my_data';
					$j.post(ajaxurl, form_data)
					.done(function(save_data){
						jgs_status_popup('<p>Thank you for sharing your fragments with us. If you want to edit them, find them in the profile settings. (page must be reloaded first)</p>');
						$j('#acf_fragments_form').before('<p style="max-width: 500px; margin: 0 auto; color: white; text-align: center;">Your fragments have been received. You can find them in the profile settings (press gear icon in lower right corner).</p>');
						$j('#acf_fragments_form').fadeOut(500).remove();
					});
				}
			});


			//AJAX: JGS USER PROGRESS

			//Define AJAX DATA
			function updateProgress(week_id, step, url) {
				//console.log("week id " + week_id + "progress_week" + progress_week + " step: " + progress_step);
				if(curr_week >= progress_week) { //only update progress for new weeks
					//console.log("new week is:" + week_id);
					var data = {
						action: 'update_progress',
						week_id: week_id,
						step: step
					};

					$j.ajax({
						url: jgs.ajaxurl,
						type: 'GET',
						data: data,
						dataType: 'json',
						success: function(response) {
							if(response !== "") {         
								$j('#jgs_system_feedback').animate({bottom: "20"}, 500).delay(1500).animate({bottom: "-50"}, 500);
								if(url !== undefined) { //Switch to next week
									window.location = url;
								} 
							}
						else {
								jgs_status_popup('An error occurred. Please contact support.');
							}
						}
					}); 
				}
			}

			//Retrieve ECHO-system
			$j('#jgs_interface #jgs_echo_system').on('click', function(e) {
				if($j('#jgs_echo_system_comments').length > 0 ) {
					if($j('#jgs_echo_system_comments').is(':visible')) {
						$j('#jgs_echo_system_bg').fadeOut(500);
						$j('#jgs_echo_system_comments').fadeOut(500);
						$j('#wrapper').css('position','static');
					}
					else {
						$j('#wrapper').css('position','fixed');
						$j('#jgs_echo_system_bg').fadeIn(500);
						$j('#jgs_echo_system_comments').fadeIn(500);
					}
				}
				else {
					e.preventDefault();
					//console.log($j('body').attr('post_id'));
					var data = {
						action: 'display_echo_system',
						post_id: $j('body').attr('post_id')
					}

					jgs_status_popup('<p>Opening ECHO-system.</p>','show',0,false);

					$j.ajax({
						url: jgs.ajaxurl,
						type: 'GET',
						data: data,
						dataType: 'json',
						success: function(response) {
							if(response !== "") {         
								jgs_status_popup('','hide');
								$j('body').prepend('<div id="jgs_echo_system_bg" style="display: none;"></div><div id="jgs_echo_system_comments" style="display: none;"><h2>ECHO-System<span class="close fa fa-times-circle"></span></h2><div class="wrap"></div></div>');
								$j('#jgs_echo_system_bg').fadeIn(500);
								$j('#jgs_echo_system_comments').fadeIn(500);
								$j('#wrapper').css('position','fixed');
								$j('#jgs_echo_system_comments .wrap').html(response);
							}
						else {
								console.log("error");	 
								jgs_status_popup('An error occurred. Please contact support.', 'show',0,false);
							}
						}
					}); 
				}
			});

			$j(document).on('click', '#jgs_echo_system_comments .close', function(e) {
				console.log("close comments");
				$j('#jgs_echo_system_bg').fadeOut(500);
				$j('#jgs_echo_system_comments').fadeOut(500);		
				$j('#wrapper').css('position','static');	
			});

			$j(document).on('click', '#choice-sequence .close', function(e) {
				$j('#choice-sequence').removeClass('show').addClass('hide');
				$j('#choice-sequence-bg').removeClass('show').addClass('hide');	
				$j("#jquery_jplayer_1").jPlayer("pause");		
			});


			//AJAX saving of acf-form
			$j('#acf_input_form :submit').click(function(event){
				event.preventDefault();
				var form_data = {'action' : 'acf/validate_save_post'};
				var require = $j('#acf_input_form').attr('require');
				var form_complete = true;

				$j('#acf_input_form :input').each(function(){
					form_data[$j(this).attr('name')] = $j(this).val();

					//Test for empty values in actual input fields
					var field = $j(this);
					if(field.parents('.acf-field-group').length > 0) {
						
						if(field.val() === "") {
							form_complete = false;
							console.log("problem is here");
						}
						console.log("input field:  " + $j(this).val());
					}
				});

				if(require === "all" && !form_complete) {
					jgs_status_popup('<p>You must fill in all fields</p>','show',0,false);
				}
				else {
					jgs_status_popup("<p>Saving form.</p>",'show',0,true);
					form_data.action = 'save_my_data';
					$j.post(jgs.ajaxurl, form_data)		
					.done(function(save_data){
						console.log("form was saved");
						$j('#acf_input_form input.acf-button').attr('value', 'Update form');
						jgs_status_popup('<p>Your data has been saved!</p>','hide', 1000);
					});
				}
			});

			function getResponse() {
				//Define AJAX DATA
				var post_id = $j('body').attr('post_id');
				var data = {
					action: 'dialogue_request',
					post_id: post_id
				}

				$j.ajax({
					url: jgs.ajaxurl,
					type: 'GET',
					data: data,
					dataType: 'json',
					success: function(response) {
						if(response != "") {     
							console.log("success");  
							$j('.jgs_character_interview .greeting').html(response);
							var commentform = $j('#commentform')
							/*
							$j('body .hide').fadeIn("slow", function() {
								$j('body .hide').removeClass('hide');
							}); */
						}
					else {
							window.alert("Ajax doesn't work :(");
						}
					}
				}); 
			}


			//Initialize dragon encounter
			var dragon_music_started = false;
			$j(document).on('click', 'a[data-callback="init_dragon"]', function(e) {
				e.preventDefault();
				jgs_funcs['start_dragon_sequence']();
			});


			//function called in this manner jgs_funcs[funcname]()
			var jgs_funcs = {
				start_dragon_sequence: function() {
					if($j('#choice-sequence').hasClass('hide')) {
						$j('#choice-sequence').removeClass('hide');
						$j('#choice-sequence-bg').removeClass('hide');
						$j('#choice-sequence').addClass('show');
						$j('#choice-sequence-bg').addClass('show');
						$j('#choice-sequence').show();
						$j('#choice-sequence-bg').show();
						if(dragon_music_started === false) {
							$j("#jquery_jplayer_1").jPlayer("stop");
							$j("#jquery_jplayer_1").jPlayer("setMedia", {
								m4a: "https://jgs-resources.s3.amazonaws.com/dragoncliff.mp3",
								oga: "https://jgs-resources.s3.amazonaws.com/dragoncliff.ogg"
							});
							dragon_music_started = true;
						}
						$j("#jquery_jplayer_1").jPlayer("playHead",0);
						$j("#jquery_jplayer_1").jPlayer("play");
					}
				},
				complete_dragon_sequence: function() {
					$j("#jquery_jplayer_1").jPlayer("stop");
					$j("#jquery_jplayer_1").jPlayer("setMedia", {
						m4a: "https://jgs-resources.s3.amazonaws.com/dragon-victory.mp3",
						oga: "https://jgs-resources.s3.amazonaws.com/dragon-victory.ogg"
					});
					$j("#jquery_jplayer_1").jPlayer("playHead",0);
					$j("#jquery_jplayer_1").jPlayer("play");
				},
				exit_dragon_sequence: function() {
					console.log("exit");
					$j('.dragon-pendant-glow').remove();
					$j('#choice-sequence').removeClass('show');
					$j('#choice-sequence-bg').removeClass('show');
					$j('#choice-sequence').addClass('hide');
					$j('#choice-sequence-bg').addClass('hide');
					$j('div.pendant-section h3 p').text('Master Cirrus’s dragon pendant lays dormant');
					$j('div.pendant-section p').text('Redfang and Beira have fled');
					jgs_shift_page_section();
				},
				update_draco_balance: function(balance,callback) {
					oldvalue = $j('body').attr('user_draco_balance'); //save for later
					
					var data = {
						action: 'update_reward_balance',
						value: balance,
						user_id: $j('body').attr('user_id')
					};

					$j.ajax({
						url: jgs.ajaxurl,
						type: 'GET',
						data: data,
						dataType: 'json',
						success: function(response) {
							if(callback == "redfang-money") {
								if(oldvalue <= 0) {
									jgs_funcs['jgs_update_heatindex']("fire", "+3"); //no money when paying Redfang
								}
								else if(oldvalue > 0 && oldvalue < 10) {
									jgs_funcs['jgs_update_heatindex']("fire", "+2"); //some money when paying Redfang
								}
							}
	                    }
					}); 
				},
	            jgs_update_heatindex: function(type,amount,shift_to_sequence_id) {
	                var data = {
	                    action: 'update_heatindex',
	                    type: type,
	                    user_id: $j('body').attr('user_id'),
	                    amount: amount
	                }
	                $j.ajax({
	                    url: jgs.ajaxurl,
		                type: 'GET',
	                    data: data,
	                    dataType: 'json',
	                    success: function(response) {                 	
	                    	var data = response.data;                    	
	                    	if(data.type === "fire") {
	                    		console.log("FIRE");
	                    		if(!$j('#heatmap-redfang').hasClass('status-defeated')) {
									$j('#heatmap-redfang').removeClass().addClass('heatmap level-' + data.amount);
								}
	                    	}
	                    	else if(data.type === "ice") {
	                    		console.log("ICE");
	                    		if(!$j('#heatmap-beira').hasClass('status-defeated')) {
									$j('#heatmap-beira').removeClass().addClass('heatmap level-' + data.amount);	 
								}
	                    	}
	                    	if(typeof shift_to_sequence_id != "undefined") {
								jgs_shift_sequence_ajax(shift_to_sequence_id);
			            	}
	                    }
	                }); 
	            },
	            jgs_defeat_dragon: function(dragon,shift_to_sequence_id) {
	            	console.log("dragon: " + dragon, " new sequence: " + shift_to_sequence_id);
	            	$j.ajax({
	            		url: jgs.ajaxurl,
		                type: 'GET',
	                    data: {action:'defeat_dragon',dragon:dragon},
	                    dataType: 'json',
	                    success: function(response) {
	                    	if(dragon === "redfang") {
	                    		$j('#heatmap-redfang').addClass('defeated');
	                    		if($j('#heatmap-beira').hasClass('status-defeated')) {
	                    			//console.log("end dragon sequence");
	                    			jgs_funcs['complete_dragon_sequence']();
	                    		}
	                    	}
	                    	if(dragon === "beira") {
	                    		$j('#heatmap-beira').addClass('defeated');
	                    		if($j('#heatmap-redfang').hasClass('status-defeated')) {
	                    			//console.log("end dragon sequence");
	                    			jgs_funcs['complete_dragon_sequence']();
	                    		}
	                    	}
			            	if(typeof shift_to_sequence_id != "undefined") {
								jgs_shift_sequence_ajax(shift_to_sequence_id);
			            	}                    	
	                    }
	            	});
	            }
			}



			/****************** DRAGON ENCOUNTER FUNCTIONS ***********************/

			var sequence_ID = "";

 			//Button triggering sequence shift         
            $j(document).on('click','.sequence-button a', function(e) {     
                e.preventDefault();  
                console.log("button is: " + $j(this).attr('data-target'));
                if($j(this).attr('data-target') != "") {
                	console.log("is defined")
	                $j('#dragon-loader').fadeIn(300); 
	                $j('body').attr('last_choice_id','1');
	                var this_elem = $j(this); //store for use inside ajax call
					$j.ajax({ //store seleted index to database for use in PHP
		                url: jgs.ajaxurl,
		                type: 'GET',
		                data: {action: 'update_lastid', last_id: 1},
		                dataType: 'json',
		                success: function(response) {
		                	//console.log("completed. next sequence is: " +  this_elem.attr('data-target'));
		                    jgs_shift_sequence(this_elem, this_elem.attr('data-target')); 
		                }
		            }); 
				}
				else {
					jgs_funcs['exit_dragon_sequence']();
				}
            });

            //Form triggering sequence shift
			$j(document).on('change','#choice-respond select', function(e) {
				var selected_elem = $j('#choice-respond option:selected');
            	var selected_index = $j('#choice-respond select').prop('selectedIndex');

                $j('#dragon-loader').fadeIn(300);
            	
            	if(selected_index == 0) {
            		return false;
            	}
            	else {
	                $j('body').attr('last_choice_id', selected_index);
	                $j.ajax({ //store seleted index to database for use in PHP
	                    url: jgs.ajaxurl,
	                    type: 'GET',
	                    data: {action: 'update_lastid', last_id: selected_index},
	                    dataType: 'json',
	                    success: function(response) {
	                    	jgs_shift_sequence(selected_elem, selected_elem.attr('data-target')); 
	                    }
	                }); 
            	}
            });

            //sequence shift
			function jgs_shift_sequence(trigger,sequence_id) {
				
            	if(sequence_id == null) { //sequence_id sequence shift comes from button click
					return false; //can't shift sequence - new sequence not defined
				}

				var callback = trigger.attr('data-js-callback');

                if(callback != "") {
                	console.log("callback is not null:" + callback);
	                var funcname = callback.substring(0,callback.indexOf('('));
	                var args = callback.substring(callback.indexOf('(')+1,callback.indexOf(')'));
					             
	                if(args.indexOf(',') !== '-1') {
	                    var args = args.split(',');
	                }

	                //Dynamically call jgs_funcs (special javascript functions that can be called through a variable name)                
	                if(funcname != null && funcname != "") {
	                	if(args != null  && args != "") {
	                		var dynamic_func = jgs_funcs[funcname]; //determine javascript function defined by shortcode
	                		console.log("dynamic function " + funcname);
	                		if(funcname == "jgs_update_heatindex" || funcname == "jgs_defeat_dragon") { //functions guaranteed to complete before sequence shifts
	                			args.push(sequence_id); //ensure sequence is shifted after dynamic function is run
	                			dynamic_func.apply(null,args); //call function with arguments
	                		}
	                		else {
	                			dynamic_func.apply(null,args); //call function with arguments
	                			jgs_shift_sequence_ajax(sequence_id);
	                		}
	                	}       	
	                }
                }
                else {
                	jgs_shift_sequence_ajax(sequence_id);
                }
            }

            function jgs_shift_sequence_ajax(sequence_id) {
                var data = {
                    action: 'update_sequence',
                    post_id: $j('body').attr('post_id'),
                    user_id: $j('body').attr('user_id'),
                    sequence_ID: sequence_id
                }

                $j.ajax({
                    url: jgs.ajaxurl,
                    type: 'GET',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if(response.data !== "") {    
                            $j('#choice-sequence .wrap').html(response.data['content']);
                            var buttons = response.data['buttons'];
                            if(buttons.indexOf('option') !== -1) { //Dropdown answers
                            	//console.log("This sequence has multiple answers");
                            	$j('#choice-respond').replaceWith("");
                            	$j('.sequence-buttons').replaceWith(response.data['buttons']);
                            }
                            else { //One button
                            	//console.log("This sequence has one answers");
                            	$j('.sequence-buttons').replaceWith(""); //empty out button container
                            	$j('#choice-respond').replaceWith(response.data['buttons']);
                            }
                            $j('#dragon-loader').fadeOut(300);
                            if(response.data['dragon'] == "redfang") {
								$j('#heatmaps').removeClass().addClass('redfang');
							}
							else if (response.data['dragon'] == "beira") {
								$j('#heatmaps').removeClass().addClass('beira');
							}
                            $j('#jgs_system_feedback').animate({bottom: "20"}, 500).delay(1500).animate({bottom: "-50"}, 500);
                        } 
                        else {
							jgs_status_popup('<p>Oops! Something went wrong. Please reload and try again.</p><p>If that does not work, please contact support!</p>','hide',5000,false);
						}
                    }
                }); 
            }



            /****************** MARION'S BOX FUNCTIONS ***********************/


			//Retrieve Challenge system from menu box
			$j('#jgs_challenge_box_menuitem').on('click', function(event) {
				event.preventDefault();
				open_challenge_box($j(this));
			});

			//Retrieve Challenge system from sequence box
			$j(document).on('click', 'a.jgs_challenge_box', function(event) {
				event.preventDefault();
				var filter_name = $j(this).attr('filter_name');
				open_challenge_box($j(this),filter_name);
			});

			function open_challenge_box(trigger, filter) {
				console.log("trigger is : " + trigger.attr('id'));
				if($j('#jgs_challenge_box').length > 0) { //challenges are open
					if(trigger.attr('id') == "jgs_challenge_box_menuitem") { //triggered from menu
						console.log("menu item");
						if($j('#jgs_challenge_box').is(':visible')) {
							$j('#jgs_challenge_box').fadeOut(500);
						}
						else {
							$j('#jgs_challenge_box').fadeIn(500);
						}
					}
					else { //triggered from sequence
						console.log("sequence");
						var challenge_class = $j('#challenges').attr('class'); //check whether part of dragon-encounter or not
						if(typeof(challenge_class) == "undefined") {
							console.log("remove box");
							$j('#jgs_challenge_box').remove();
							load_challenge_box_contents(filter);
						}
						else {
							$j('#jgs_challenge_box').fadeIn(500);
						}
					}
				}
				else {
					console.log("open box");
					load_challenge_box_contents(filter);
				}
			}

			function load_challenge_box_contents(filter) {
				$j('#jgs_challenge_box').remove(); //remove box before loading
				var data = {
					action: 'display_coaching_system',
					filter: filter
				}

				jgs_status_popup("<h3>Opening Marion's Box...</h3>",'show',0,true);

				$j.ajax({
					url: jgs.ajaxurl,
					type: 'GET',
					data: data,
					dataType: 'json',
					success: function(response) {
						if(response !== "") {         
							jgs_status_popup('','hide');
							$j('body').prepend('<div id="jgs_challenge_box" style="display: none;"></div>');
							$j('#jgs_challenge_box').html(response).fadeIn(500);
						}
						else {
							jgs_status_popup('An error occurred. Please contact support.', 'show',0,false);
						}
					}
				});				
			}

			$j(document).on('click', '#challenges .challenge h2', function(e) {
				e.preventDefault();
				if($j(this).parents('.challenge').hasClass('showall')) {
					$j('#challenges .challenge.showall').find('.toggle').text('Click for Info');
					$j(this).parents('.challenge').removeClass('showall');
					$j(this).parents('.challenge').find('.complete').text('Click for Info');
				}
				else {
					$j('#challenges .challenge').find('.toggle').text('Click Title to Collapse');
					$j('#challenges .challenge').removeClass('showall');
					$j(this).parents('.challenge').addClass('showall');
					$j(this).parent().find('.complete').text('Click Title to collapse');
				}
			});


			$j(document).on('click', '.challenge .meta ul li', function(e) {
				var element_class = $j(this).attr('class');
				$j('.challenge').each(function(index) {
					if(! $j(this).hasClass(element_class)) {
						$j(this).fadeOut(500);
					}
				});
				if($j('#tag_filter').length > 0 ) {
					$j('#tag_filter').html('<div><p>Filter on <em>' + $j(this).attr('tag_name') + '</em><span class="fa fa-times-circle close"></span></p></div></div>');
					toggle_tag_titles(element_class);
				}
				else {
					$j('#challenges_overlay').prepend('<div id="tag_filter"><div><p>Filter on <em>' + $j(this).attr('tag_name') + '</em><span class="fa fa-times-circle close"></span></p></div></div>');
					toggle_tag_titles(element_class);
				}
			});

			$j(document).on('click', '#tag_filter .close', function(e) {
				$j('#tag_filter').fadeOut(500).remove();
				$j('.challenge').fadeIn(500);
				$j('#challenges h3').fadeIn(500);
			});

			function toggle_tag_titles(elem_class) {
				$j('#challenges ul').each(function(index) {
					var title = $j(this).prev(); //h3 title
					$j(this).children('li').each(function(index) {
						if($j(this).hasClass(elem_class)) {
							cat_empty = false;
							return false;
						}
					});
					if(cat_empty === undefined || cat_empty === "") cat_empty = true;

					if(cat_empty) {
						title.fadeOut(500);
					}
					else {
						title.fadeIn(500);
					}
					cat_empty = "";
				});
			}

			//AJAX update after completing challenges

			$j(document).on('click', '#challenges_overlay a.close', function(e) {
				e.preventDefault();
				$j('#jgs_challenge_box').fadeOut(500);
			});

			// Challenges popup confirmation buttons
			$j(document).on('click', '#challenges .meta a', function(e) {
				e.preventDefault();
				$j('#challenges').attr('active_value', $j(this).parents('li.challenge').attr('value'));
				var challenge_class = $j('#challenges').attr('class');

				if(challenge_class.indexOf('fight-') !== -1) {
					var claim_text = "Strike the Dragon?";
				}
				else {
					var claim_text = "Are you sure you want to claim this reward?";
				}
				jgs_status_popup('<div class="challenge_check"><h3>' + claim_text + '</h3><p>Make sure you have actually completed the challenge before clicking yes.</p><ul><li class="yes">Yes</li><li class="no">No</li></ul></di>','show',0,true);
			});		

			$j(document).on('click', '#jgs_status_popup .challenge_check li', function() {
				if($j(this).hasClass('yes')) {
					complete_challenge();
				}
				else {
					jgs_status_popup("<p>Thanks for your honesty. Your integrity and self-worth will surely benefit.</p>",'hide',3000,false);
				}
			});



			function complete_challenge() {	
				var challenge_class = $j('#challenges').attr('class');
				if(challenge_class.indexOf('fight-') === -1) { //Normal use
					jgs_status_popup("<p>Claiming your Reward...</p>",'show',0,true);

					var challenge_value = $j('#challenges').attr('active_value');
					var data = {
						action: 'ryit_update_reward_balance',
						value: challenge_value,
						user_id: $j('body').attr('user_id')
					};

					$j.ajax({
						url: jgs.ajaxurl,
						type: 'GET',
						data: data,
						dataType: 'json',
						success: function(response) {
							if(response !== "") {     
								console.log(response);
								jgs_status_popup('<p>Congratulations.<br/>You have received your reward!</p>','hide',3000,false);
								$j('#reward_balance').html('Draco balance: <strong>' + response + '</strong>');
							}
							else {
								jgs_status_popup('<p>Oops! Something went wrong :(<br>Please contact support!</p>','hide',2000,false);
							}
						}
					}); 
				}
				else { //Dragon encounter use
					jgs_status_popup("<h3 style='text-align: center;'>Striking the Dragon</h2><p style='color: #999'>You're practicing how to regulate archetypal energy.",'show',0,true);	

					var challenge_value = $j('#challenges').attr('active_value');
					var challenge_class = $j('#challenges').attr('class');

					if(challenge_class.indexOf('fight-redfang') !== -1) {
						var type = "fire";
					}
					else if(challenge_class.indexOf('fight-beira') !== -1) {
						var type = "ice";
					}

					var data = {
						action: 'update_heatindex',
						amount: '-' + challenge_value,
						type: type,
						user_id: $j('body').attr('user_id')
					};

					$j.ajax({
						url: jgs.ajaxurl,
						type: 'GET',
						data: data,
						dataType: 'json',
						success: function(response) {
							if(response !== "") {     
								jgs_status_popup("<h3 style='text-align: center;'>Congratulations!</h2><p style='color: #999'>You have successfully regulated archetypal energy and weakened the force of the dragon!",'hide',4000,false);	
								$j('#reward_balance').html('Dragon power: <strong>' + response.data.amount + '</strong>');

								if(response.data.amount <= 0) {
									$j('#jgs_challenge_box').fadeOut(500);
									if(type == "fire") {
										$j('#heatmap-redfang').removeClass().addClass('heatmap level-defeated');
										jgs_shift_sequence_ajax('redfang-victory');
									}
									else if(type == "ice") {
										$j('#heatmap-beira').removeClass().addClass('heatmap level-defeated');
										jgs_shift_sequence_ajax('beira-victory');
									}
								}
								else {
									//update heatbars
									if(type == "fire") {
										$j('#heatmap-redfang').removeClass().addClass('heatmap level-' + response.data.amount);
									}
									else if(type == "ice") {
										$j('#heatmap-beira').removeClass().addClass('heatmap level-' + response.data.amount);
									}
								}
							}
						else {
								jgs_status_popup('<p>Oops! Something went wrong :(<br>Please contact support!</p>','hide',2000,false);
							}
						}
					}); 
				}


			}



			//Preload images
			$j.preloadImages = function() {
			  for (var i = 0; i < arguments.length; i++) {
			    $("<img />").attr("src", arguments[i]);
			  }
			}

			$j.preloadImages("/wp-content/themes/Avada-Child-Theme/images/wooden-box-inside.jpg");

			//dev_work();
		}); // end jquery document.ready


		function dev_work() {
			$j('body').prepend('<div id="dev_work"><p>Dev work in progress. Some irregularities may occur.</p></div>');
		}


		//Check if element is scrolled into view
		function scrolledBelowElement(elem){
		    var $elem = $j(elem);
		    var $window = $j(window);
		    var docViewTop = $window.scrollTop();
		    var elemBottom = $elem.offset().top + $elem.height();
		    return (elemBottom <= docViewTop);
		}
	</script>


	<?php
	/**
	 *
	 * The settings below are not sanitized.
	 * In order to be able to take advantage of this,
	 * a user would have to gain access to the database
	 * in which case this is the least on your worries.
	 */
	echo Avada()->settings->get( 'google_analytics' ); // WPCS: XSS ok.
	echo Avada()->settings->get( 'space_head' ); // WPCS: XSS ok.
	?>
</head>


<?php
$wrapper_class = ( is_page_template( 'blank.php' ) ) ? 'wrapper_blank' : '';

if ( 'modern' === Avada()->settings->get( 'mobile_menu_design' ) ) {
	$mobile_logo_pos = strtolower( Avada()->settings->get( 'logo_alignment' ) );
	if ( 'center' === strtolower( Avada()->settings->get( 'logo_alignment' ) ) ) {
		$mobile_logo_pos = 'left';
	}
}

//Set up week ID for course progress tracking
global $post;

if(get_field('jgs_week_id', $post->ID)) {
	$week_id = get_field('jgs_week_id', $post->ID);
}

$user_id = get_current_user_id();

//Read progress and apply relevant classes
if(get_field('jgs_user_data_progress', 'user_' . $user_id)) {
	$progress = get_field('jgs_user_data_progress', 'user_' . $user_id);
	$progress = explode(",", $progress);

	if(count($progress) == 2) {
		$progress_week = $progress[0];
		$progress_step = $progress[1];
	}
	else {
		$progress_week = 0;
		$progress_step = 0;
	}
}
else {
	$progress_week = 0;
	$progress_step = 0;
}

//Read progress and apply relevant classes
if(get_field('jgs_last_week_slug', $post->ID)) {
	$prev_slug = get_field('jgs_last_week_slug', $post->ID);
}

if(get_field('jgs_next_week_slug', $post->ID)) {
	$next_slug = get_field('jgs_next_week_slug', $post->ID);
}


?>

<body <?php body_class(); ?> post_id="<?php echo $post->ID; ?>" week_id="<?php echo $week_id; ?>" prev_slug="<?php echo $prev_slug; ?>" next_slug="<?php echo $next_slug; ?>" progress_week="<?php echo $progress_week; ?>" progress_step="<?php echo $progress_step; ?>" user_id="<?php echo $user_id; ?>" user_draco_balance="<?php echo ryit_get_reward_balance();?>">
	<?php
	do_action( 'avada_before_body_content' );

	$boxed_side_header_right = false;
	$page_bg_layout = ( $c_page_id ) ? get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) : 'default';
	?>
	<?php if ( ( ( 'Boxed' === Avada()->settings->get( 'layout' ) && ( 'default' === $page_bg_layout || '' == $page_bg_layout ) ) || 'boxed' === $page_bg_layout ) && 'Top' != Avada()->settings->get( 'header_position' ) ) : ?>
		<div id="boxed-wrapper">
	<?php endif; ?>
	<?php if ( ( ( 'Boxed' === Avada()->settings->get( 'layout' ) && 'default' === $page_bg_layout ) || 'boxed' === $page_bg_layout ) && 'framed' === Avada()->settings->get( 'scroll_offset' ) ) : ?>
		<div class="fusion-sides-frame"></div>
	<?php endif; ?>
	<div id="wrapper" class="<?php echo esc_attr( $wrapper_class ); ?>">
		<div id="home" style="position:relative;top:-1px;"></div>
		<?php avada_header_template( 'Below', is_archive() || Avada_Helper::bbp_is_topic_tag() ); ?>
		<?php if ( 'Left' === Avada()->settings->get( 'header_position' ) || 'Right' === Avada()->settings->get( 'header_position' ) ) : ?>
			<?php avada_side_header(); ?>
		<?php endif; ?>

		<?php avada_header_template( 'Above', is_archive() || Avada_Helper::bbp_is_topic_tag() ); ?>

		<?php if ( has_action( 'avada_override_current_page_title_bar' ) ) : ?>
			<?php do_action( 'avada_override_current_page_title_bar', $c_page_id ); ?>
		<?php else : ?>
			<?php avada_current_page_title_bar( $c_page_id ); ?>
		<?php endif; ?>

		<?php if ( is_page_template( 'contact.php' ) && Avada()->settings->get( 'recaptcha_public' ) && Avada()->settings->get( 'recaptcha_private' ) ) : ?>
			<script type="text/javascript">var RecaptchaOptions = { theme : '<?php echo esc_attr( Avada()->settings->get( 'recaptcha_color_scheme' ) ); ?>' };</script>
		<?php endif; ?>

		<?php if ( is_page_template( 'contact.php' ) && Avada()->settings->get( 'gmap_address' ) && Avada()->settings->get( 'status_gmap' ) ) : ?>
			<?php
			$map_popup             = ( ! Avada()->settings->get( 'map_popup' ) ) ? 'yes' : 'no';
			$map_scrollwheel       = ( Avada()->settings->get( 'map_scrollwheel' ) ) ? 'yes' : 'no';
			$map_scale             = ( Avada()->settings->get( 'map_scale' ) ) ? 'yes' : 'no';
			$map_zoomcontrol       = ( Avada()->settings->get( 'map_zoomcontrol' ) ) ? 'yes' : 'no';
			$address_pin           = ( Avada()->settings->get( 'map_pin' ) ) ? 'yes' : 'no';
			$address_pin_animation = ( Avada()->settings->get( 'gmap_pin_animation' ) ) ? 'yes' : 'no';
			?>
			<div id="fusion-gmap-container">
				<?php // @codingStandardsIgnoreLine
				echo Avada()->google_map->render_map(
					array(
						'address'                  => esc_html( Avada()->settings->get( 'gmap_address' ) ),
						'type'                     => esc_attr( Avada()->settings->get( 'gmap_type' ) ),
						'address_pin'              => esc_attr( $address_pin ),
						'animation'                => esc_attr( $address_pin_animation ),
						'map_style'                => esc_attr( Avada()->settings->get( 'map_styling' ) ),
						'overlay_color'            => esc_attr( Avada()->settings->get( 'map_overlay_color' ) ),
						'infobox'                  => esc_attr( Avada()->settings->get( 'map_infobox_styling' ) ),
						'infobox_background_color' => esc_attr( Avada()->settings->get( 'map_infobox_bg_color' ) ),
						'infobox_text_color'       => esc_attr( Avada()->settings->get( 'map_infobox_text_color' ) ),
						// @codingStandardsIgnoreLine
						'infobox_content'          => htmlentities( Avada()->settings->get( 'map_infobox_content' ) ),
						'icon'                     => esc_attr( Avada()->settings->get( 'map_custom_marker_icon' ) ),
						'width'                    => esc_attr( Avada()->settings->get( 'gmap_dimensions', 'width' ) ),
						'height'                   => esc_attr( Avada()->settings->get( 'gmap_dimensions', 'height' ) ),
						'zoom'                     => esc_attr( Avada()->settings->get( 'map_zoom_level' ) ),
						'scrollwheel'              => esc_attr( $map_scrollwheel ),
						'scale'                    => esc_attr( $map_scale ),
						'zoom_pancontrol'          => esc_attr( $map_zoomcontrol ),
						'popup'                    => esc_attr( $map_popup ),
					)
				);
				?>
			</div>
		<?php endif; ?>
		<?php
		$main_css   = '';
		$row_css    = '';
		$main_class = '';

		if ( apply_filters( 'fusion_is_hundred_percent_template', $c_page_id, false ) ) {
			$main_css = 'padding-left:0px;padding-right:0px;';
			$hundredp_padding = get_post_meta( $c_page_id, 'pyre_hundredp_padding', true );
			if ( Avada()->settings->get( 'hundredp_padding' ) && ! $hundredp_padding ) {
				$main_css = 'padding-left:' . Avada()->settings->get( 'hundredp_padding' ) . ';padding-right:' . Avada()->settings->get( 'hundredp_padding' );
			}
			if ( $hundredp_padding ) {
				$main_css = 'padding-left:' . $hundredp_padding . ';padding-right:' . $hundredp_padding;
			}
			$row_css    = 'max-width:100%;';
			$main_class = 'width-100';
		}
		do_action( 'avada_before_main_container' );
		?>
		<main id="main" role="main" class="clearfix <?php echo esc_attr( $main_class ); ?>" style="<?php echo esc_attr( $main_css ); ?>">
			<div id="jgs_loader">Loading page. Please wait...</div>
			<div class="fusion-row" style="<?php echo esc_attr( $row_css ); ?>">