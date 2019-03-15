<?php

function forums_toolkit_page() {
	// Check if options need to be saved, so if coming from form
	if ( isset($_POST['optssave']) ) {
		if( !empty($_POST["bbptoolkit-tick-notify"]) ) {
			add_option('bbptoolkit-tick-notify', 'activate');
		} else {
			delete_option('bbptoolkit-tick-notify');
		}
		if( !empty($_POST["bbptoolkit-rem-ohbother"]) ) {
			add_option('bbptoolkit-rem-ohbother', 'activate');
			delete_option('bbptoolkit-tick-ohbother');
			delete_option('bbptoolkit-tick-unreshtml');
		} else {
			delete_option('bbptoolkit-rem-ohbother');
			if( !empty($_POST["bbptoolkit-tick-ohbother"]) ) {
				add_option('bbptoolkit-tick-ohbother', 'activate');
				if ( empty ($_POST['bbptoolkit-new-ohbother'] ) ) {
					delete_option('bbptoolkit-new-ohbother');
				} else {
					update_option('bbptoolkit-new-ohbother', $_POST['bbptoolkit-new-ohbother']);
				}
			} else {
				delete_option('bbptoolkit-tick-ohbother');
			}
			if( !empty($_POST["bbptoolkit-tick-unreshtml"]) ) {
				add_option('bbptoolkit-tick-unreshtml', 'activate');
				if ( empty ($_POST['bbptoolkit-new-unres-html'] ) ) {
					delete_option('bbptoolkit-new-unres-html');
				} else {
					update_option('bbptoolkit-new-unres-html', $_POST['bbptoolkit-new-unres-html']);
				}
			} else {
				delete_option('bbptoolkit-tick-unreshtml');
			}
		}
		if( !empty($_POST["bbptoolkit-subscr-right"]) ) {
			add_option('bbptoolkit-subscr-right', 'activate');
		} else {
			delete_option('bbptoolkit-subscr-right');
		}
		if( !empty($_POST["bbptoolkit-closed-nogrey"]) ) {
			add_option('bbptoolkit-closed-nogrey', 'activate');
		} else {
			delete_option('bbptoolkit-closed-nogrey');
		}
		if( !empty($_POST["bbptoolkit-reply-inverse"]) ) {
			add_option('bbptoolkit-reply-inverse', 'activate');
			if ( !empty($_POST["bbptoolkit-reply-inverse-keep-lead"]) ) {
				add_option('bbptoolkit-reply-inverse-keep-lead', 'activate');
			} else {
				delete_option('bbptoolkit-reply-inverse-keep-lead');
			}
		} else {
			delete_option('bbptoolkit-reply-inverse');
			delete_option('bbptoolkit-reply-inverse-keep-lead');
		}
		
		if( !empty($_POST["bbptoolkit-rem-defstyle"]) ) {
			add_option('bbptoolkit-rem-defstyle', 'activate');
		} else {
			delete_option('bbptoolkit-rem-defstyle');
		}
		if( !empty($_POST["bbptoolkit-rem-subf"]) ) {
			add_option('bbptoolkit-rem-subf', 'activate');
		} else {
			delete_option('bbptoolkit-rem-subf');
		}

		// Remove info This forum contains 37 topics and 130 replies, and was last updated by  John Doe 2 days, 4 hours ago.
		if ( !empty($_POST["bbptoolkit-rem-forum-info"]) ) {
			add_option('bbptoolkit-rem-forum-info', 'activate');
			if ( !empty($_POST["bbptoolkit-tick-new-forum-info"]) ) {
				add_option('bbptoolkit-tick-new-forum-info', 'activate');
				if ( !empty($_POST["bbptoolkit-new-forum-info"]) ) {
					update_option('bbptoolkit-new-forum-info', $_POST["bbptoolkit-new-forum-info"]);
				}			
			} else {
				delete_option('bbptoolkit-tick-new-forum-info');
			}
			
		} else {
			delete_option('bbptoolkit-rem-forum-info');
			delete_option('bbptoolkit-tick-new-forum-info');
			delete_option('bbptoolkit-new-forum-info');
		}
		
		// Remove info This topic contains 8 replies, has 3 voices, and was last updated by  Jane Doe 1 day, 7 hours ago.
		if ( !empty($_POST["bbptoolkit-rem-topic-info"]) ) {
			add_option('bbptoolkit-rem-topic-info', 'activate');
			if ( !empty($_POST["bbptoolkit-tick-new-topic-info"]) ) {
				add_option('bbptoolkit-tick-new-topic-info', 'activate');
				if ( !empty($_POST["bbptoolkit-new-topic-info"]) ) {
					update_option('bbptoolkit-new-topic-info', $_POST["bbptoolkit-new-topic-info"]);
				}			
			} else {
				delete_option('bbptoolkit-tick-new-topic-info');
			}
			
		} else {
			delete_option('bbptoolkit-rem-topic-info');
			delete_option('bbptoolkit-tick-new-topic-info');
			delete_option('bbptoolkit-new-topic-info');
		}

		// Remove pagination info
		if( !empty($_POST["bbptoolkit-rem-pag-info"]) ) {
			add_option('bbptoolkit-rem-pag-info', 'activate');
		} else {
			delete_option('bbptoolkit-rem-pag-info');
		}
		
		// Shorten fresheness info
		if( !empty($_POST["bbptoolkit-short-fresh"]) ) {
			add_option('bbptoolkit-short-fresh', 'activate');
		} else {
			delete_option('bbptoolkit-short-fresh');
		}
		
		//Breadcrumbs
		if( !empty($_POST["bbptoolkit-rem-breadcrumb"]) ) {
			add_option('bbptoolkit-rem-breadcrumb', 'activate');
			delete_option('bbptoolkit-breadcrumb-before');
			delete_option('bbptoolkit-breadcrumb-sep');
			delete_option('bbptoolkit-breadcrumb-home');
			delete_option('bbptoolkit-breadcrumb-root');
			delete_option('bbptoolkit-breadcrumb-current');
		} else {
			delete_option('bbptoolkit-rem-breadcrumb');
			if( !empty($_POST["bbptoolkit-breadcrumb-before"]) ) {
				add_option('bbptoolkit-breadcrumb-before', 'activate');
				if( !empty($_POST["bbptoolkit-breadcrumb-before-text"]) ) {
					update_option('bbptoolkit-new-breadcrumbbefore', $_POST["bbptoolkit-breadcrumb-before-text"]);
				} else {
					delete_option('bbptoolkit-new-breadcrumbbefore');
				}
			} else {
				delete_option('bbptoolkit-breadcrumb-before');
			}
			if( !empty($_POST["bbptoolkit-breadcrumb-sep"]) ) {
				add_option('bbptoolkit-breadcrumb-sep', 'activate');
				if( !empty($_POST["bbptoolkit-breadcrumb-sep-text"]) ) {
					update_option('bbptoolkit-new-breadcrumbsep', trim($_POST["bbptoolkit-breadcrumb-sep-text"]));
				} else {
					delete_option('bbptoolkit-new-breadcrumbsep');
				}
			} else {
				delete_option('bbptoolkit-breadcrumb-sep');
			}
			if( !empty($_POST["bbptoolkit-breadcrumb-home"]) ) {
				add_option('bbptoolkit-breadcrumb-home', 'activate');
				if( !empty($_POST["bbptoolkit-breadcrumb-home-text"]) ) {
					update_option('bbptoolkit-new-breadcrumbhome', $_POST["bbptoolkit-breadcrumb-home-text"]);
				} else {
					delete_option('bbptoolkit-new-breadcrumbhome');
				}
			} else {
				delete_option('bbptoolkit-breadcrumb-home');
			}
			if( !empty($_POST["bbptoolkit-breadcrumb-root"]) ) {
				add_option('bbptoolkit-breadcrumb-root', 'activate');
				if( !empty($_POST["bbptoolkit-breadcrumb-root-text"]) ) {
					update_option('bbptoolkit-new-breadcrumbroot', $_POST["bbptoolkit-breadcrumb-root-text"]);
				} else {
					delete_option('bbptoolkit-new-breadcrumbroot');
				}
			} else {
				delete_option('bbptoolkit-breadcrumb-root');
			}
			if( !empty($_POST["bbptoolkit-breadcrumb-current"]) ) {
				add_option('bbptoolkit-breadcrumb-current', 'activate');
			} else {
				delete_option('bbptoolkit-breadcrumb-current');
			}
		}
		
		// Closed forums
		if (!empty($_POST['closedforum'])) {
			$optionArray = $_POST['closedforum'];
			$fullstr = '*';
			foreach ($optionArray as $optionitem) {
				$fullstr = $fullstr . $optionitem . '*';
			}
			update_option('bbptoolkit-closedforums', $fullstr);
		} else {
			delete_option('bbptoolkit-closedforums');
		}
		if (!empty($_POST['closedforumrolesok'])) {
			$optionArray = $_POST['closedforumrolesok'];
			$fullstr = '*';
			foreach ($optionArray as $optionitem) {
				$fullstr = $fullstr . $optionitem . '*';
			}
			update_option('bbptoolkit-closedforums-rolesok', $fullstr);
		} else {
			delete_option('bbptoolkit-closedforums-rolesok');
		}
		if( !empty($_POST["bbptoolkit-closedforums-text"]) ) {
			update_option('bbptoolkit-closedforums-text', $_POST["bbptoolkit-closedforums-text"]);
		} else {
			delete_option('bbptoolkit-closedforums-text');
		}

		// Secure Profiles
		if( !empty($_POST["bbptoolkit-sec-profile"]) ) {
			add_option('bbptoolkit-sec-profile', 'activate');
			if( !empty($_POST["bbptoolkit-sec-profile-path"]) ) {
				update_option('bbptoolkit-sec-profile-path', $_POST["bbptoolkit-sec-profile-path"]);
			} else {
				delete_option('bbptoolkit-sec-profile-path');
			}
		} else {
			delete_option('bbptoolkit-sec-profile');
		}

		// Limit title MAX
		if( !empty($_POST["bbptoolkit-title-maxchar"]) ) {
			add_option('bbptoolkit-title-maxchar', 'activate');
			if( !empty($_POST["bbptoolkit-title-maxchar-text"]) ) {
				update_option('bbptoolkit-title-maxchar-text', $_POST["bbptoolkit-title-maxchar-text"]);
			} else {
				delete_option('bbptoolkit-title-maxchar-text');
			}
		} else {
			delete_option('bbptoolkit-title-maxchar');
		}

		// Mentionning
		if( !empty($_POST["bbptoolkit-mentions"]) ) {
			add_option('bbptoolkit-mentions', 'activate');
			if( !empty($_POST["bbptoolkit-mentions-subject"]) ) {
				update_option('bbptoolkit-mentions-subject', $_POST["bbptoolkit-mentions-subject"]);
			} else {
				delete_option('bbptoolkit-mentions-subject');
			}
		} else {
			delete_option('bbptoolkit-mentions');
		}
		
		// Subforum separator
		if( !empty($_POST["bbptoolkit-subforum-separator"]) ) {
			update_option('bbptoolkit-subforum-separator', $_POST["bbptoolkit-subforum-separator"]);
		} else {
			delete_option('bbptoolkit-subforum-separator');
		}

		// Subforum hide counters
		if( !empty($_POST["bbptoolkit-subforum-hide-counters"]) ) {
			add_option('bbptoolkit-subforum-hide-counters', 'activate');
		} else {
			delete_option('bbptoolkit-subforum-hide-counters');
		}
		
		// Activate TinyMCE
		if( !empty($_POST["bbptoolkit-activate-tinymce"]) ) {
			add_option('bbptoolkit-activate-tinymce', 'activate');
		} else {
			delete_option('bbptoolkit-activate-tinymce');
		}

		// Admin links separator
		if( !empty($_POST["bbptoolkit-adm-links"]) ) {
			add_option('bbptoolkit-adm-links', 'activate');
			if( !empty($_POST["bbptoolkit-adm-links-sep"]) ) {
				update_option('bbptoolkit-adm-links-sep', $_POST["bbptoolkit-adm-links-sep"]);
			} else {
				delete_option('bbptoolkit-adm-links-sep');
			}
		} else {
			delete_option('bbptoolkit-adm-links');
		}

		// Show only last revision
		if( !empty($_POST["bbptoolkit-only-last-revision-log"]) ) {
			add_option('bbptoolkit-only-last-revision-log', 'activate');
		} else {
			delete_option('bbptoolkit-only-last-revision-log');
		}

		// Show featured icon in front of forum
		// Limit title MAX
		if( !empty($_POST["bbptoolkit-activate-forum-icon"]) ) {
			add_option('bbptoolkit-activate-forum-icon', 'activate');
			if( !empty($_POST["bbptoolkit-activate-forum-icon-width"]) ) {
				update_option('bbptoolkit-activate-forum-icon-width', $_POST["bbptoolkit-activate-forum-icon-width"]);
			} else {
				delete_option('bbptoolkit-activate-forum-icon-width');
			}
		} else {
			delete_option('bbptoolkit-activate-forum-icon');
		}

		// Remove Private in front of private forum
		if( !empty($_POST["bbptoolkit-rem-priv-prefix"]) ) {
			add_option('bbptoolkit-rem-priv-prefix', 'activate');
		} else {
			delete_option('bbptoolkit-rem-priv-prefix');
		}

		// Subscriptions default new users
		if (!empty($_POST['defsubscrforum'])) {
			$optionArray = $_POST['defsubscrforum'];
			$fullstr = '*';
			foreach ($optionArray as $optionitem) {
				$fullstr = $fullstr . $optionitem . '*';
			}
			update_option('bbptoolkit-def-subscr-forums', $fullstr);
		} else {
			delete_option('bbptoolkit-def-subscr-forums');
		}

		// Display bbPress search form above every single topic and forum
		if( !empty($_POST["bbptoolkit-show-search-everywhere"]) ) {
			add_option('bbptoolkit-show-search-everywhere', 'activate');
		} else {
			delete_option('bbptoolkit-show-search-everywhere');
		}

		// Fix issue in 2.5 when topic has are over 1000 replies
		if( !empty($_POST["bbptoolkit-fix-topic-over-thousand"]) ) {
			add_option('bbptoolkit-fix-topic-over-thousand', 'activate');
		} else {
			delete_option('bbptoolkit-fix-topic-over-thousand');
		}

		// Generate CSS needed
		bbptoolkit_generate_css();
		
	}

	$bbptoolkit_tick_notify = get_option('bbptoolkit-tick-notify', false);
	$bbptoolkit_rem_ohbother = get_option('bbptoolkit-rem-ohbother', false);
	$bbptoolkit_tick_ohbother = get_option('bbptoolkit-tick-ohbother', false);
	$bbptoolkit_tick_unreshtml = get_option('bbptoolkit-tick-unreshtml', false);
	$bbptoolkit_subscr_right = get_option('bbptoolkit-subscr-right', false);
	$bbptoolkit_closed_nogrey = get_option('bbptoolkit-closed-nogrey', false);
	$bbptoolkit_rem_defstyle = get_option('bbptoolkit-rem-defstyle', false);
	$NewOhBother = get_option('bbptoolkit-new-ohbother', false);
	$NewUnresHTML = get_option('bbptoolkit-new-unres-html', false);
	$bbptoolkit_reply_inverse = get_option('bbptoolkit-reply-inverse', false);
	$bbptoolkit_reply_inverse_keep_lead = get_option('bbptoolkit-reply-inverse-keep-lead', false);
	$bbptoolkit_rem_subf = get_option('bbptoolkit-rem-subf', false);
	$bbptoolkit_rem_forum_info = get_option('bbptoolkit-rem-forum-info', false);
	$bbptoolkit_new_forum_info = get_option('bbptoolkit-new-forum-info', false);
	$bbptoolkit_tick_new_forum_info = get_option('bbptoolkit-tick-new-forum-info', false);
	$bbptoolkit_rem_topic_info = get_option('bbptoolkit-rem-topic-info', false);
	$bbptoolkit_new_topic_info = get_option('bbptoolkit-new-topic-info', false);
	$bbptoolkit_tick_new_topic_info = get_option('bbptoolkit-tick-new-topic-info', false);
	$bbptoolkit_rem_pag_info = get_option('bbptoolkit-rem-pag-info', false);
	$bbptoolkit_rem_breadcrumb = get_option('bbptoolkit-rem-breadcrumb', false);
	$bbptoolkit_breadcrumb_before = get_option('bbptoolkit-breadcrumb-before', false);
	$newbreadcrumbbefore = get_option('bbptoolkit-new-breadcrumbbefore', false);
	$bbptoolkit_breadcrumb_sep = get_option('bbptoolkit-breadcrumb-sep', false);
	$newbreadcrumbsep = get_option('bbptoolkit-new-breadcrumbsep', false);
	$bbptoolkit_breadcrumb_home = get_option('bbptoolkit-breadcrumb-home', false);
	$newbreadcrumbhome = get_option('bbptoolkit-new-breadcrumbhome', false);
	$bbptoolkit_breadcrumb_root = get_option('bbptoolkit-breadcrumb-root', false);
	$newbreadcrumbroot = get_option('bbptoolkit-new-breadcrumbroot', false);
	$bbptoolkit_breadcrumb_current = get_option('bbptoolkit-breadcrumb-current', false);
	$bbptoolkit_sec_profile = get_option('bbptoolkit-sec-profile', false);
	$bbptoolkit_sec_profile_path = get_option('bbptoolkit-sec-profile-path', false);
	$bbptoolkit_title_maxchar = get_option('bbptoolkit-title-maxchar', false);	
	$bbptoolkit_title_maxchar_text = get_option('bbptoolkit-title-maxchar-text', false);
	$bbptoolkit_title_minchar = get_option('bbptoolkit-title-minchar', false);	
	$bbptoolkit_title_minchar_text = get_option('bbptoolkit-title-minchar-text', false);
	$bbptoolkit_short_fresh = get_option('bbptoolkit-short-fresh', false);
	$bbptoolkit_subforum_separator = get_option('bbptoolkit-subforum-separator', false);
	$bbptoolkit_subforum_hide_counters = get_option('bbptoolkit-subforum-hide-counters', false);
	$bbptoolkit_activate_tinymce = get_option('bbptoolkit-activate-tinymce', false);
	$bbptoolkit_adm_links = get_option('bbptoolkit-adm-links', false);
	$bbptoolkit_adm_links_sep = get_option('bbptoolkit-adm-links-sep', false);
	$bbptoolkit_only_last_revision_log = get_option('bbptoolkit-only-last-revision-log', false);
	$bbptoolkit_activate_forum_icon = get_option('bbptoolkit-activate-forum-icon', false);
	$bbptoolkit_activate_forum_icon_width = get_option('bbptoolkit-activate-forum-icon-width', false);
	$bbptoolkit_mentions = get_option('bbptoolkit-mentions', false);
	$bbptoolkit_mentions_subject = get_option('bbptoolkit-mentions-subject', 'You have been mentioned.');
	$bbptoolkit_rem_priv_prefix = get_option('bbptoolkit-rem-priv-prefix', false);
	$bbptoolkit_show_search_everywhere = get_option('bbptoolkit-show-search-everywhere', false);
	$bbptoolkit_fix_topic_over_thousand = get_option('bbptoolkit-fix-topic-over-thousand', false);
		
	// check new Oh Bother and Unrestricted HTML message
	if ( empty ( $NewOhBother ) ) {
		$NewOhBother = _x('Oh bother! No topics were found here!', 'Exact bbPress translation in your language!', 'bbp-toolkit');
	}
	if ( empty ( $NewUnresHTML ) ) {
		$NewUnresHTML = _x('Your account has the ability to post unrestricted HTML content.', 'Exact bbPress translation in your language!', 'bbp-toolkit');
	}

	echo '<div class="bbptoolkit-wrap">';
	echo '<h1>bbP Toolkit v'. BBPTOOLKIT_VERSION .'</h1>';

	echo '<table border="0"><tr><td style="vertical-align:top;">';

	echo '<button class="tab-buttons" id="button1">'; _e('Global', 'bbp-toolkit'); echo'</button>';
	echo '<button class="tab-buttons" id="button2">'; _e('Information', 'bbp-toolkit'); echo'</button>';
	echo '<button class="tab-buttons" id="button4">'; _e('Extras', 'bbp-toolkit'); echo'</button>';
	echo '<button class="tab-buttons" id="button5">'; _e('Close Forums', 'bbp-toolkit'); echo'</button>';
	echo '<button class="tab-buttons" id="button7">'; _e('Subscriptions', 'bbp-toolkit'); echo'</button>';
	echo '<button class="tab-buttons" id="button6">'; _e('Basic System Info', 'bbp-toolkit'); echo'</button>';
	

	echo '<div id="choosetab" style="margin:30px;">'; _e('Please choose your category ...', 'bbp-toolkit'); echo'</div>';
		
	echo '<form action="" method="post">';

	echo '<div id="tabbutton1" class="tabs">';
		echo '<h3>'; _e('bbPress Global settings', 'bbp-toolkit'); echo'</h3>';

	
		echo '<p><input type="checkbox" name="bbptoolkit-subscr-right" id="bbptoolkit-subscr-right" value="bbptoolkit-subscr-right" ';
		if ($bbptoolkit_subscr_right) { echo 'checked'; }
		echo '><label for="bbptoolkit-subscr-right">'; _e('Move the <b>Subscribe</b> option of a forum to the right, not next to breadcrums', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';

		echo '<u>'; _ex('Breadcrumbs', 'Title on options screen', 'bbp-toolkit'); echo '</u> '; _e('e.g. Home > Forums > myForum', 'bbp-toolkit');
		echo '<p style="text-indent:30px;"><input type="checkbox" name="bbptoolkit-rem-breadcrumb" id="bbptoolkit-rem-breadcrumb" value="bbptoolkit-rem-breadcrumb"  onclick="hidedivifchecked(\'bbptoolkit-div-breadcrumb\', \'bbptoolkit-rem-breadcrumb\')" ';
		if ($bbptoolkit_rem_breadcrumb) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-breadcrumb">'; _e('Remove breadcrumb completely', 'bbp-toolkit'); echo' ' . showversion('1.0.1') . '</label></p>';
		echo '<div id="bbptoolkit-div-breadcrumb" style="display:';
		if ($bbptoolkit_rem_breadcrumb) { echo 'none'; } else { echo 'block'; }
		echo '">';
			echo '<p style="text-indent:-20px;margin-left:80px;"><input type="checkbox" name="bbptoolkit-breadcrumb-before" id="bbptoolkit-breadcrumb-before" value="bbptoolkit-breadcrumb-before" ';
			if ($bbptoolkit_breadcrumb_before) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Add text before the breadcrumb :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-breadcrumb-before-text" id="bbptoolkit-breadcrumb-before-text" value="' . $newbreadcrumbbefore . '" size="60" />';
			echo '<label> '; _e('(e.g. You are here: )', 'bbp-toolkit'); echo' ' . showversion('1.0.2') . '</label></p>';

			echo '<p style="text-indent:-20px;margin-left:80px;"><input type="checkbox" name="bbptoolkit-breadcrumb-sep" id="bbptoolkit-breadcrumb-sep" value="bbptoolkit-breadcrumb-sep" ';
			if ($bbptoolkit_breadcrumb_sep) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Change separator <b>"&gt;"</b> to :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-breadcrumb-sep-text" id="bbptoolkit-breadcrumb-sep-text" value="' . $newbreadcrumbsep . '" size="3" />';
			echo '<label> ' . showversion('1.0.2') . '</label></p>';

			echo '<p style="text-indent:-20px;margin-left:80px;"><input type="checkbox" name="bbptoolkit-breadcrumb-home" id="bbptoolkit-breadcrumb-home" value="bbptoolkit-breadcrumb-home" ';
			if ($bbptoolkit_breadcrumb_home) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Replace text <b>Home</b> with :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-breadcrumb-home-text" id="bbptoolkit-breadcrumb-home-text" value="' . $newbreadcrumbhome . '" size="60" />';
			echo '<label> '; _e('(Ticked and empty field will remove <b>Home</b> completely)', 'bbp-toolkit'); echo' ' . showversion('1.0.2') . '</label></p>';

			echo '<p style="text-indent:-20px;margin-left:80px;"><input type="checkbox" name="bbptoolkit-breadcrumb-root" id="bbptoolkit-breadcrumb-root" value="bbptoolkit-breadcrumb-root" ';
			if ($bbptoolkit_breadcrumb_root) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Replace text <b>Forums</b> with :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-breadcrumb-root-text" id="bbptoolkit-breadcrumb-root-text" value="' . $newbreadcrumbroot . '" size="60" />';
			echo '<label> '; _e('(Ticked and empty field will remove <b>Forums</b> completely)', 'bbp-toolkit'); echo' ' . showversion('1.0.2') . '</label></p>';

			echo '<p style="text-indent:-20px;margin-left:80px;"><input type="checkbox" name="bbptoolkit-breadcrumb-current" id="bbptoolkit-breadcrumb-current" value="bbptoolkit-breadcrumb-current" ';
			if ($bbptoolkit_breadcrumb_current) { echo 'checked'; }
			echo '><label for="bbptoolkit-breadcrumb-current">'; _e('Remove name of current forum <b>myForum</b>', 'bbp-toolkit'); echo' ' . showversion('1.0.2') . '</label></p>';
		echo '</div>';
	
		echo '<u>'; _ex('Topics', 'Title on options screen', 'bbp-toolkit'); echo '</u>';
		echo '<p><input type="checkbox" name="bbptoolkit-closed-nogrey" id="bbptoolkit-closed-nogrey" value="bbptoolkit-closed-nogrey" ';
		if ($bbptoolkit_closed_nogrey) { echo 'checked'; }
		echo '><label for="bbptoolkit-closed-nogrey">'; _e('Do <b>not</b> grey out closed topics', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';
	
		echo '<p><input type="checkbox" name="bbptoolkit-tick-notify" id="bbptoolkit-tick-notify" value="bbptoolkit-tick-notify" ';
		if ($bbptoolkit_tick_notify) { echo 'checked'; }
		echo '><label for="bbptoolkit-tick-notify">'; _e('Auto tick the <b>Notify me of follow-up replies via email</b> (so activate subscription to every topic you reply to)', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';

		echo '<p style="text-indent:-20px;margin-left:20px;"><input type="checkbox" name="bbptoolkit-title-maxchar" id="bbptoolkit-title-maxchar" value="bbptoolkit-title-maxchar" ';
		if ($bbptoolkit_title_maxchar) { echo 'checked'; }
		echo '>';
		echo '<label>'; _e('Maximum topic title length :', 'bbp-toolkit'); echo' </label>';
		echo '<input type="text" name="bbptoolkit-title-maxchar-text" id="bbptoolkit-title-maxchar-text" value="' . $bbptoolkit_title_maxchar_text . '" size="3" />';
		echo '<label> '; _e('(Leave empty or untick for default (80) )', 'bbp-toolkit'); echo' ' . showversion('1.0.4') . '</label></p>';
		
		echo '<p style="text-indent:-20px;margin-left:20px;"><input type="checkbox" name="bbptoolkit-title-minchar" id="bbptoolkit-title-minchar" value="bbptoolkit-title-minchar" ';
		if ($bbptoolkit_title_minchar) { echo 'checked'; }
		echo '>';
		echo '<label><i>'; _e('Minimum topic title length :', 'bbp-toolkit'); echo' </i></label>';
		echo '<input type="text" name="bbptoolkit-title-minchar-text" id="bbptoolkit-title-minchar-text" value="' . $bbptoolkit_title_minchar_text . '" size="3" />';
		echo '<label><i> '; _e('( Not yet implemented !!! )', 'bbp-toolkit'); echo'</i> ' . showversion('1.0.x') . '</label></p>';

		echo '<u>'; _ex('Replies', 'Title on options screen', 'bbp-toolkit'); echo '</u>';
		echo '<p><input type="checkbox" name="bbptoolkit-reply-inverse" id="bbptoolkit-reply-inverse" value="bbptoolkit-reply-inverse" onclick="showdivifchecked(\'bbptoolkit-div-inverse-replies\', \'bbptoolkit-reply-inverse\')"  ';
		if ($bbptoolkit_reply_inverse) { echo 'checked'; }
		echo '><label for="bbptoolkit-reply-inverse">'; _e('Most recent reply on top (inverse the sorting of replies to a topic)', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';
		echo '<div id="bbptoolkit-div-inverse-replies" style="display:';
		if ($bbptoolkit_reply_inverse) { echo 'block'; } else { echo 'none'; }
		echo '">';
			echo '<p style="text-indent:-20px;margin-left:50px;"><input type="checkbox" name="bbptoolkit-reply-inverse-keep-lead" id="bbptoolkit-reply-inverse-keep-lead" value="bbptoolkit-reply-inverse-keep-lead" ';
			if ($bbptoolkit_reply_inverse_keep_lead) { echo 'checked'; }
			echo '><label for="bbptoolkit-reply-inverse-keep-lead">'; _e('Inverse replies <b>but keep lead topic on top</b>', 'bbp-toolkit'); echo' ' . showversion('1.0.1') . '</label></p>';
		echo '</div>';

		echo '<u>'; _ex('Topics and Replies', 'Title on options screen', 'bbp-toolkit'); echo '</u>';
		echo '<p><input type="checkbox" name="bbptoolkit-activate-tinymce" id="bbptoolkit-activate-tinymce" value="bbptoolkit-activate-tinymce" ';
		if ($bbptoolkit_activate_tinymce) { echo 'checked'; }
		echo '><label for="bbptoolkit-activate-tinymce">'; _e('Activate the basic TinyMCE editor to new topics and replies to add basic HTML to your input.', 'bbp-toolkit'); echo' ' . showversion('1.0.5') . '</label></p>';

		echo '<p style="text-indent:-20px;margin-left:20px;"><input type="checkbox" name="bbptoolkit-adm-links" id="bbptoolkit-adm-links" value="bbptoolkit-adm-links" ';
		if ($bbptoolkit_adm_links) { echo 'checked'; }
		echo '>';
		echo '<label>'; _e('Change the separator between the admin links', 'bbp-toolkit'); echo' : </label>';
		echo '<input type="text" name="bbptoolkit-adm-links-sep" id="bbptoolkit-adm-links-sep" value="' . $bbptoolkit_adm_links_sep . '" size="3" />';
		echo '<label> '; _e('(Empty the field and save to have the standard separator)', 'bbp-toolkit'); echo' ' . showversion('1.0.7') . '</label></p>';
		
		
		echo '<u>'; _ex('Profiles', 'Title on options screen', 'bbp-toolkit'); echo '</u>';
		echo '<p style="text-indent:-20px;margin-left:20px;"><input type="checkbox" name="bbptoolkit-sec-profile" id="bbptoolkit-sec-profile" value="bbptoolkit-sec-profile" ';
		if ($bbptoolkit_sec_profile) { echo 'checked'; }
		echo '>';
		echo '<label>'; _e('Secure user profiles so they cannot be seen by non logged-in users. Redirect page ( e.g. /login )', 'bbp-toolkit'); echo' : </label>';
		echo '<input type="text" name="bbptoolkit-sec-profile-path" id="bbptoolkit-sec-profile-path" value="' . $bbptoolkit_sec_profile_path . '" size="60" />';
		echo '<label> '; _e('(Empty the field and save to have non logged-in users redirect to previous page)', 'bbp-toolkit'); echo' ' . showversion('1.0.4') . '</label></p>';


	echo '</div>';
	echo '<div id="tabbutton2" class="tabs">';

	echo '<h3>'; _e('bbPress Information', 'bbp-toolkit'); echo'</h3>';
	
		echo '<p><input type="checkbox" name="bbptoolkit-rem-ohbother" id="bbptoolkit-rem-ohbother" value="bbptoolkit-rem-ohbother" onclick="showdivohbother(\'bbptoolkit-ohbother\')" '; 
		if ($bbptoolkit_rem_ohbother) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-ohbother">'; _e('Completely remove message and box for empty forum <b>Oh bother! No topics were found here!</b>, the message <b>You must be logged in to create new topics.</b> and for admins the <b>Your account has the ability to post unrestricted HTML content.</b>', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';
		echo '<div id="bbptoolkit-ohbother" style="display:';
		if ($bbptoolkit_rem_ohbother) { echo 'none'; } else { echo 'block'; }
		echo '">';
			echo '<p style="text-indent:-20px;margin-left:50px;"><input type="checkbox" name="bbptoolkit-tick-ohbother" id="bbptoolkit-tick-ohbother" value="bbptoolkit-tick-ohbother" ';
			if ($bbptoolkit_tick_ohbother) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Replace text <b>Oh bother! No topics were found here!</b> with :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-new-ohbother" id="bbptoolkit-new-ohbother" value="' . $NewOhBother . '" size="60" />';
			echo '<label> '; _e('(Activate, empty field and save to get the original message)', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';
	
			echo '<p style="text-indent:-20px;margin-left:50px;"><input type="checkbox" name="bbptoolkit-tick-unreshtml" id="bbptoolkit-tick-unreshtml" value="bbptoolkit-tick-unreshtml" ';
			if ($bbptoolkit_tick_unreshtml) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Replace text <b>Your account has the ability to post unrestricted HTML content.</b> with :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-new-unres-html" id="bbptoolkit-new-unres-html" value="' . $NewUnresHTML . '" size="60" />';
			echo '<label> '; _e('(Activate, empty field and save to get the original message)', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';
		echo '</div>';
	
	
		echo '<p><input type="checkbox" name="bbptoolkit-rem-subf" id="bbptoolkit-rem-subf" value="bbptoolkit-rem-subf" ';
		if ($bbptoolkit_rem_subf) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-subf">'; _e('Do not show the table with the list of subforums, only show the current forum and the topics', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';

		echo '<p><input type="checkbox" name="bbptoolkit-subforum-hide-counters" id="bbptoolkit-subforum-hide-counters" value="bbptoolkit-subforum-hide-counters" ';
		if ($bbptoolkit_subforum_hide_counters) { echo 'checked'; }
		echo '><label for="bbptoolkit-subforum-hide-counters">'; _e('Do not show the counters next to the subforums on the forum index page', 'bbp-toolkit'); echo' ' . showversion('1.0.5') . '</label></p>';

		echo '<p style="text-indent:-20px;margin-left:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>'; _e('Change the separator between the subforums on the forum index page to:', 'bbp-toolkit'); echo' </label>';
		if (!$bbptoolkit_subforum_separator) { echo 'checked'; }
		echo '<select name="bbptoolkit-subforum-separator" id="bbptoolkit-subforum-separator">';
		echo '<option value=" " ';
			if ((!$bbptoolkit_subforum_separator) || ($bbptoolkit_subforum_separator == ' ')) { echo 'selected'; }
			echo '>&lt;'; _e('space', 'bbp-toolkit'); echo'&gt;</option>';
		echo '<option value="<br>" ';
			if ($bbptoolkit_subforum_separator == '<br>') { echo 'selected'; }
			echo '>&lt;'; _e('newline', 'bbp-toolkit'); echo'&gt;</option></select>';
		echo '<label>' . showversion('1.0.5') . '</label></p>';
	
		echo '<p><input type="checkbox" name="bbptoolkit-rem-forum-info" id="bbptoolkit-rem-forum-info" value="bbptoolkit-rem-forum-info" onclick="showdivifchecked(\'bbptoolkit-div-forum-info\', \'bbptoolkit-rem-forum-info\')" ';
		if ($bbptoolkit_rem_forum_info) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-forum-info">'; _e('Remove forum info <b>This forum contains 37 topics and 130 replies, and was last updated by  John Doe 2 days, 4 hours ago</b>', 'bbp-toolkit'); echo' ' . showversion('1.0.1') . '</b></label></p>';
		echo '<div id="bbptoolkit-div-forum-info" style="display:';
		if ($bbptoolkit_rem_forum_info) { echo 'block'; } else { echo 'none'; }
		echo '">';
			echo '<p style="text-indent:-20px;margin-left:50px;"><input type="checkbox" name="bbptoolkit-tick-new-forum-info" id="bbptoolkit-tick-new-forum-info" value="bbptoolkit-tick-new-forum-info" ';
			if ($bbptoolkit_tick_new_forum_info) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Replace standard forum info text with :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-new-forum-info" id="bbptoolkit-new-forum-info" value="' . $bbptoolkit_new_forum_info . '" size="70" /> ' . showversion('1.0.1') . '</p>';
		echo '</div>';

		echo '<p><input type="checkbox" name="bbptoolkit-rem-topic-info" id="bbptoolkit-rem-topic-info" value="bbptoolkit-rem-topic-info" onclick="showdivifchecked(\'bbptoolkit-div-topic-info\', \'bbptoolkit-rem-topic-info\')" ';
		if ($bbptoolkit_rem_topic_info) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-topic-info">'; _e('Remove topic info <b>This topic contains 8 replies, has 3 voices, and was last updated by  Jane Doe 1 day, 7 hours ago</b>', 'bbp-toolkit'); echo' ' . showversion('1.0.1') . '</b></label></p>';
		echo '<div id="bbptoolkit-div-topic-info" style="display:';
		if ($bbptoolkit_rem_topic_info) { echo 'block'; } else { echo 'none'; }
		echo '">';
			echo '<p style="text-indent:-20px;margin-left:50px;"><input type="checkbox" name="bbptoolkit-tick-new-topic-info" id="bbptoolkit-tick-new-topic-info" value="bbptoolkit-tick-new-topic-info" ';
			if ($bbptoolkit_tick_new_topic_info) { echo 'checked'; }
			echo '>';
			echo '<label>'; _e('Replace standard topic info text with :', 'bbp-toolkit'); echo' </label>';
			echo '<input type="text" name="bbptoolkit-new-topic-info" id="bbptoolkit-new-topic-info" value="' . $bbptoolkit_new_topic_info . '" size="70" /> ' . showversion('1.0.1') . '</p>';
		echo '</div>';

		echo '<p><input type="checkbox" name="bbptoolkit-rem-pag-info" id="bbptoolkit-rem-pag-info" value="bbptoolkit-rem-pag-info" ';
		if ($bbptoolkit_rem_pag_info) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-pag-info">'; _e('Remove pagination info <b>Viewing 15 topics - 1 through 15 (of 32 total)</b>', 'bbp-toolkit'); echo' ' . showversion('1.0.3') . '</label></p>';

		echo '<p><input type="checkbox" name="bbptoolkit-short-fresh" id="bbptoolkit-short-fresh" value="bbptoolkit-short-fresh" ';
		if ($bbptoolkit_short_fresh) { echo 'checked'; }
		echo '><label for="bbptoolkit-short-fresh">'; _e('Shorten the display of the freshness. E.g. <b>1 month, 3 days ago</b> will show as <b>1 month ago</b>', 'bbp-toolkit'); echo ' ' . showversion('1.0.4') . ' <i>'; _e('(Only available in English!)'); echo'</i></label></p>';

		echo '<p><input type="checkbox" name="bbptoolkit-only-last-revision-log" id="bbptoolkit-only-last-revision-log" value="bbptoolkit-only-last-revision-log" ';
		if ($bbptoolkit_only_last_revision_log) { echo 'checked'; }
		echo '><label for="bbptoolkit-only-last-revision-log">'; _e('Only show the last revision of a topic or reply <b>(This topic was modified 2 months by John)</b>, not all of them', 'bbp-toolkit'); echo' ' . showversion('1.0.5') . '</label></p>';

		echo '<p style="text-indent:-20px;margin-left:20px;"><input type="checkbox" name="bbptoolkit-activate-forum-icon" id="bbptoolkit-activate-forum-icon" value="bbptoolkit-activate-forum-icon" ';
		if ($bbptoolkit_activate_forum_icon) { echo 'checked'; }
		echo '>';
		echo '<label>'; _e('Display featured image in front of forum name, image width :', 'bbp-toolkit'); echo' </label>';
		echo '<input type="text" name="bbptoolkit-activate-forum-icon-width" id="bbptoolkit-activate-forum-icon-width" value="' . $bbptoolkit_activate_forum_icon_width . '" size="3" />';
		echo '<label> '; _e('(Leave empty for default 30px )', 'bbp-toolkit'); echo' ' . showversion('1.0.6') . '</label></p>';

		echo '<p><input type="checkbox" name="bbptoolkit-rem-priv-prefix" id="bbptoolkit-rem-priv-prefix" value="bbptoolkit-rem-priv-prefix" ';
		if ($bbptoolkit_rem_priv_prefix) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-priv-prefix">'; _e('Remove the word Private: in front of private forums', 'bbp-toolkit'); echo' ' . showversion('1.0.8') . '</label></p>';

	echo '</div>';
	echo '<div id="tabbutton4" class="tabs">';	

	echo '<h3>'; _e('Extras', 'bbp-toolkit'); echo'</h3>';
		echo '<u>'; _ex('Performance', 'Title on options screen', 'bbp-toolkit'); echo '</u> ';
		echo '<p style="text-indent:30px;"><input type="checkbox" name="bbptoolkit-rem-defstyle" id="bbptoolkit-rem-defstyle" value="bbptoolkit-rem-defstyle" ';
		if ($bbptoolkit_rem_defstyle) { echo 'checked'; }
		echo '><label for="bbptoolkit-rem-defstyle">'; _e('Remove bbpress css style from all pages except forum pages', 'bbp-toolkit'); echo' ' . showversion('1.0.0') . '</label></p>';
		
		echo '<u>'; _ex('Mentions', 'Title on options screen', 'bbp-toolkit'); echo '</u> '; _e('(Email somebody by mentionning e.g. <b>@username</b> in your topic or reply)', 'bbp-toolkit');
		echo '<p style="text-indent:30px;"><input type="checkbox" name="bbptoolkit-mentions" id="bbptoolkit-mentions" value="bbptoolkit-mentions"  onclick="showdivifchecked(\'bbptoolkit-div-mentions\', \'bbptoolkit-mentions\')" ';
		if ($bbptoolkit_mentions) { echo 'checked'; }
		echo '><label for="bbptoolkit-mentions">'; _e('Activate mentions', 'bbp-toolkit'); echo' ' . showversion('1.0.7') . '</label></p>';
		echo '<div id="bbptoolkit-div-mentions" style="display:';
		if ($bbptoolkit_mentions) { echo 'block'; } else { echo 'none'; }
		echo '">';
			echo '<p style="text-indent:-20px;margin-left:80px;"><label>'; _e('Email subject :', 'bbp-toolkit'); echo ' </label>';
			echo '<input type="text" name="bbptoolkit-mentions-subject" id="bbptoolkit-mentions-subject" value="' . $bbptoolkit_mentions_subject . '" size="60" />';
			echo showversion('1.0.7') . '</p>';

		echo '<u>'; _ex('Search', 'Title on options screen', 'bbp-toolkit'); echo '</u> ';
		echo '<p style="text-indent:30px;"><input type="checkbox" name="bbptoolkit-show-search-everywhere" id="bbptoolkit-show-search-everywhere" value="bbptoolkit-show-search-everywhere" ';
		if ($bbptoolkit_show_search_everywhere) { echo 'checked'; }
		echo '><label for="bbptoolkit-show-search-everywhere">'; _e('Display bbPress search form above every single topic and forum', 'bbp-toolkit'); echo ' ' . showversion('1.0.10') . '</label></p>';

		echo '<u>'; _ex('Fixes v2.5', 'Title on options screen', 'bbp-toolkit'); echo '</u> ';
		echo '<p style="text-indent:30px;"><input type="checkbox" name="bbptoolkit-fix-topic-over-thousand" id="bbptoolkit-fix-topic-over-thousand" value="bbptoolkit-fix-topic-over-thousand" ';
		if ($bbptoolkit_fix_topic_over_thousand) { echo 'checked'; }
		echo '><label for="bbptoolkit-fix-topic-over-thousand">'; _e('Topic with over 1000 replies does not show pagination', 'bbp-toolkit'); echo ' ' . showversion('1.0.12') . '</label></p>';
		
		echo '</div>';
	
	echo '</div>';
	
	echo '<div id="tabbutton5" class="tabs">';

	echo '<h3>'; _e('Close Forums', 'bbp-toolkit'); echo'</h3>';
		echo '<p>'; _e('In the below ticked forums, <b>NO new topics</b> can be created. Only replies to current existing topics are accepted.', 'bbp-toolkit'); echo'<br>';
		echo ''; _e('Select one or more roles that could still create new topics, regardless of the protection/lock.', 'bbp-toolkit'); echo' ' . showversion('1.0.3') . '</p>';
		echo '<table><tr><td style="text-align: left;vertical-align: top;padding: 5px 35px;"><b>'; _e('Forums', 'bbp-toolkit'); echo'</b>';
		$closed_forum_ids = get_option('bbptoolkit-closedforums', false);
		$all_forums = bbptoolkit_forum_structure();
		foreach ($all_forums as $myforum) {
			echo '<p><input type="checkbox" name="closedforum[]" value="'.$myforum['id'].'" ';
			if (strpos($closed_forum_ids, '*'.strval($myforum['id']).'*') !== FALSE) { echo 'checked'; }
			echo '>' . $myforum['title'].'</p>';
		}
		echo '</td><td style="text-align: left;vertical-align: top;padding: 5px 35px;"><b>'; _e('Roles'); echo'</b>';
		$closed_forum_roles_ok = get_option('bbptoolkit-closedforums-rolesok', false);
		global $wp_roles;
		$all_roles = array_keys($wp_roles->roles);
		foreach ($all_roles as $myrole) {
			echo '<p><input type="checkbox" name="closedforumrolesok[]" value="'.$myrole.'" ';
			if (strpos($closed_forum_roles_ok, '*'.strval($myrole).'*') !== FALSE) { echo 'checked'; }
			echo '>' . $myrole.'</p>';
		}
		echo '</td></tr></table>';
		
		$closed_forum_text = get_option('bbptoolkit-closedforums-text', false);
		echo '<label>'; _e('Add this text instead of the New Topic box when a forum is closed', 'bbp-toolkit'); echo' : </label>';
		echo '<input type="text" name="bbptoolkit-closedforums-text" id="bbptoolkit-closedforums-text" value="' . $closed_forum_text . '" size="60" />';
		echo '<label> '; _e('(Leave empty for no text at all. HTML accepted.)', 'bbp-toolkit'); echo' ' . showversion('1.0.7') . '</label></p>';
		
	echo '</div>';
		
	echo '<div id="tabbutton7" class="tabs">';
	echo '<h3>'; _e('Subscriptions', 'bbp-toolkit'); echo'</h3>';
		$edit_url = site_url() . '/wp-admin/edit.php?post_type=forum';
		echo '<p>'; printf( __( 'To manage subscriptions of a forum, <a href="%s">edit the forums</a> and click on "Subscriptions" as an action of a forum, or edit the forum and find the "Manage Subscriptions" button (somewhere below the Forum Attributes).', 'bbp-toolkit' ), $edit_url ); echo '</p>';
		$edit_url = site_url() . '/wp-admin/edit.php?post_type=topic';
		echo '<p>'; printf( __( 'To manage subscriptions for topics, <a href="%s">edit the topics</a> and find "Subscriptions" as an action for each topic.', 'bbp-toolkit' ), $edit_url ); echo '</p>';
		$edit_url = site_url() . '/wp-admin/users.php';
		echo '<p>'; printf( __( 'To manage subscriptions for a user, <a href="%s">edit the users</a> and find "Subscriptions" as an action for each user.', 'bbp-toolkit' ), $edit_url ); echo '</p>';

		echo '<p><u>'; _e('Subscribe new users automatically to the below ticked forums:'); echo '</u>'; echo ' ' . showversion('1.0.10') . '<br>';
		$def_subscr_forum_ids = get_option('bbptoolkit-def-subscr-forums', false);
		$all_forums = bbptoolkit_forum_structure();
		foreach ($all_forums as $myforum) {
			echo '<input type="checkbox" name="defsubscrforum[]" value="'.$myforum['id'].'" ';
			if (strpos($def_subscr_forum_ids, '*'.strval($myforum['id']).'*') !== FALSE) { echo 'checked'; }
			echo '>' . $myforum['title'].'<br>';
		}
		echo '</p>';
		
	echo '</div>';
	
	echo '<p><input type="submit" name="optssave" value="'; _e('Save settings', 'bbp-toolkit'); echo'" /></p>';
	echo '</form></div>';

	echo '<div id="tabbutton6" class="tabs">';	
	
	echo '<h3>'; _e('Basic System Info', 'bbp-toolkit'); echo'</h3>';
	_e('Please provide the below info to support personnel on <a href="https://bbpress.org/forums">https://bbpress.org/forums/</a> if useful or requested', 'bbp-toolkit'); echo'<br><br>';
	$bbpt_sysinfo = bbptoolkit_basic_sysinfo();
	echo '<table>';
	array_walk($bbpt_sysinfo, create_function('$item1, $key', 'echo "<tr><td>$key</td><td>$item1</td></tr>";'));
	echo '</table>';
	echo '<br>';
	$update_url = wp_nonce_url(
                    self_admin_url( 'update.php?action=svn-update-plugin&plugin=' . BBPT_PLUGIN_NAME ),
                    'svn_update_plugin'
                );
	echo '<button><a href="' . $update_url . '">Upgrade to latest trunk (dev) version</a></button> (This will download and install the latest development version of this plugin from wordpress.org)';
	echo '</div>';

	echo '</td><td style="text-align: left;vertical-align: top;padding: 35px;">';
	echo '<table style="border: 1px solid green;">';
	echo '<tr><td style="vertical-align:top;text-align:center;padding:15px;">'; _e('Is this plugin helpful ?', 'bbp-toolkit'); echo'<br><br><a href="http://casier.eu/wp-dev/"><img src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_cc_147x47.png" height="40"></a></td></tr>';
	echo '<tr><td style="vertical-align:top;text-align:center;padding:15px;">'; _e('Just 1 or 2 EUR/USD for a coffee<br>is very much appreciated!', 'bbp-toolkit'); echo'</td></tr>';
	echo '<tr><td nowrap style="vertical-align:top;text-align:center;padding:15px;">'; _e('Consider also these great plugins:', 'bbp-toolkit'); echo'<br><a href="https://wordpress.org/plugins/bbp-move-topics/" target="_blank">bbP Move Topics</a><br><a href="https://wordpress.org/plugins/bbp-api/" target="_blank">bbP API</a><br><a href="https://wordpress.org/plugins/board-election/" target="_blank">Board Election</a></td></tr>';
	echo '</table>';
		
	echo '</td></tr></table>';
}
