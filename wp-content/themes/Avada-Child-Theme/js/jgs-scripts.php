<?php // Script file
	
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

		$j('#account .toggle').fadeIn(2000);

		$j('#account .toggle').on('click', function() {
			if($j(this).parent().hasClass('closed')) {
				$j(this).parent().removeClass('closed');
			} else {
				$j(this).parent().addClass('closed');
			}
		});

		$j('#commentform #comment').on('focus', function() {
			$j(this).text(''); 
		});

		$j('#commentform #comment').on('blur', function() {
			if( $j(this).text() === "" ) {
				$j(this).text('The ECHO-archives await you...');
			}
		});

		//Set up form pointers
		var commentform = $j('#commentform');
		commentform.prepend('<div id="comment-status" ></div>');
		var statusdiv = $j('#comment-status');

		//post form
		commentform.submit(function(e) {
			e.preventDefault();
			var formdata = commentform.serialize();	
			statusdiv.html('<p>Processing...</p>');
			var formurl = commentform.attr('action');

			$j.ajax({			   
				type: 'post', // the kind of data we are sending
				url: formurl, // this is the file that processes the form data
				data: formdata, // this is our serialized data from the form
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					statusdiv.html('<p class="wdpajax-error" >You might have left one of the fields blank, or be posting too quickly</p>');
				},
				success: function(data, textStatus) {
					if(data == "success") {
						statusdiv.html('<p class="ajax-success">Thanks for your comment. We appreciate your response.</p>');
						$j('body #respond').fadeOut(500);
						getResponse();
					}
					else {
						statusdiv.html('<p class="ajax-error">Please wait a while before posting your next comment</p>');
						commentform.find('textarea[name=comment]').val('');
					}
				}
			});
		});
		//End commentform AJAX


		//JGS Status popup
		function jgs_status_popup(message,toggle,delay) {
			if(message !== 'undefined' && message !== 'hide' && toggle !== 'hide') {
				if($j('#jgs_modal').length <= 0) { //only create if it doesn't already exist
					$j('body').prepend('<div id="jgs_modal"><div class="wrap"></div></div>');
				}

				var jgs_popup = $j('#jgs_modal');
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
			else {
				var jgs_popup = $j('#jgs_modal');

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

		
		// Check progress and show completed steps
		var curr_week = $j('body').attr('week_id');
		var progress_week = $j('body').attr('progress_week');
		var progress_step = $j('body').attr('progress_step');

		if(progress_week > curr_week) { //Participant is further than open week
			$j("[class*=step]").each(function() {
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


		// Set up functionality for progress buttons
		$j(".continue-button").on("click", function(e) {
			e.preventDefault();
			week_id = $j("body").attr("week_id");
			step = $j(this).attr("step");	
			$j(this).removeClass('show');
			$j(".step-" + step).fadeIn(1000);
			$j(".step-" + step).find('.continue-button').addClass('show');
			$j(".step-" + (step-1)).find('.continue-button').removeClass('show'); //hide container for earlier class
			$j('body').attr('progress_step', ++progress_step);

			if($j(this).attr('finalize') == "true") { 
				week_id++; 
				step = 0;
			}

			updateProgress(week_id,step);
		});


		$j(document).on("click", '#jgs_modal', function(e) {
			e.preventDefault();	
			$j('#jgs_modal').fadeOut(500);
		});

		$j('#jgs_gaming_elements').on("click", function(e) {
			$j(this).before('<div id="jgs_modal"><div id="jgs_mystery_box"><div class="wrap"><p>The Box is empty</p><p><a href="#">Close it</a></p></div></div></div>');
			var statusdiv = $j('#jgs_modal');
			statusdiv.fadeIn(500);
		});

		$j(window).scroll(function(){
			if(scrolledBelowElement($j('.fusion-title-sc-wrapper'))){
				$j('#jgs_gaming_elements').addClass('show');
			}
			else {
				$j('#jgs_gaming_elements').removeClass('show');
			}
		});


		<?php $music = get_field('jgs_background_music'); ?>

		//JPlayer
		var myCirclePlayer = new CirclePlayer("#jquery_jplayer_1",
		{
			m4a: "https://jgs-resources.s3.amazonaws.com/<?php echo $music; ?>.mp3",
			oga: "https://jgs-resources.s3.amazonaws.com/<?php echo $music; ?>.ogg"
		}, {
			cssSelectorAncestor: "#cp_container_1",
			swfPath: "<?php echo get_stylesheet_directory_uri(); ?>/js/dist/jplayer",
		<?php
			$autoplay = get_field('field_5a5a27159affc', 'user_' . $user_id);
			if($autoplay) :
		?>
			canplay: function() {
		    	$j("#jquery_jplayer_1").jPlayer("play");
		    }
		<?php 
			endif; 
		?>
		});

		//fade in user interface
		$j('#jgs_interface').fadeIn(3000);

		//AJAX saving of Fragments form
		$j('form.acf-form#acf_fragments_form :submit').click(function(event){
			event.preventDefault();
			var form_data = {'action' : 'acf/validate_save_post'};

			required_fragments = 4;
			i = 1;
			form_valid = false;
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
				form_data.action = 'save_my_data';
				console.log(form_data);
				console.log(jgs.ajaxurl);
				$j.post(ajaxurl, form_data)
				.done(function(save_data){
					jgs_status_popup('<p>Thank you for sharing your fragments with us.</p>');
					$j('#acf_fragments_form').fadeOut(500);
					updateProgress($j('body').attr('week_id'),$j('body').attr('progress_step')+1);
				});
			}
		});
	});



/*
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
					$j('.jgs_character_interview .greeting').html(response);
					var commentform = $j('#commentform')
					$j('body .hide').fadeIn("slow", function() {
						$j('body .hide').removeClass('hide');
					});
				}
			else {
					window.alert("Ajax doesn't work :(");
				}
			}
		}); 
	}
*/

	//AJAX: JGS USER PROGRESS

	//Define AJAX DATA
	function updateProgress(week_id, step) {
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
				}
			else {
					console.log('There was an error');
				}
			}
		}); 
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