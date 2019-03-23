$j = jQuery.noConflict();

	   $j('document').ready(function() {
			if(!$j('body').hasClass('logged-in')) {
			 $j('.logged-in-show').remove();
			}
	   });

   	function show_popup(content,classes='',close_btn=true,fadeout_delay=false,load_url=false) {
			$j('body').addClass('has-popup');
			$j('#ryit-popup').remove();
			$j('body').append('<div id="ryit-popup-overlay"></div>');
			if(classes !== '') classes = ' class="' + classes + '"';
			var close_btn = close_btn == true ? '<a href="#" class="close"><span class="fa fa-times"></span></a>' : '';

			if(content == 'loading') {
				content = '<span class="spinner"></span><p>Refreshing View ...</p>';
			}
			$j('body').append('<div id="ryit-popup"' + classes + '><div class="innerwrap">' + content + close_btn + '</div></div>');
			var scrollTop = $j(document).scrollTop();
			if(classes.indexOf('fixed') == -1) {
				$j("#ryit-popup").css("top", scrollTop + 150 + "px");
			}

			if(fadeout_delay !== false) {
				setTimeout(function() { 
					hide_popup(true,500); 
					if(load_url !== false) {
						location.reload(load_url);
					}
				}, fadeout_delay);
			}
			else {
				if(load_url !== false) {
					location.reload(load_url);
				}
			}
		}

		function hide_popup(fadeout=false,fadeout_time=false) {
			if(fadeout) {
				if(fadeout_time === false) {
					fadeout_time = 500;
				}
				$j('#ryit-popup').fadeOut(fadeout_time,function() {
					$j('#ryit-popup').remove();
				});
				$j('#ryit-popup-overlay').fadeOut(fadeout_time,function() {
					$j('#ryit-popup-overlay').remove();
				});
			}
			else {
				$j('#ryit-popup').remove();
				$j('#ryit-popup-overlay').remove();
			}
		}

		$j('document').on('click','body.has-popup #ryit-popup', function(e) {
			e.stopPropagation();
			console.log("clicking one");
		});

		$j('document').on('click','body.has-popup', function(e) {
			console.log("clicking body");
			$j("#ryit-popup").fadeOut(350);
		});