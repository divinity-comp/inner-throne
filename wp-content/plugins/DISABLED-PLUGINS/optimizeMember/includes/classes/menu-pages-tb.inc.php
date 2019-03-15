<?php
/**
* Toolbox for Menu Pages.
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__optimizemember_menu_pages_tb"))
	{
		/**
		* Toolbox for Menu Pages.
		*
		* @package s2Member\Menu_Pages
		* @since 131108
		*/
		class c_ws_plugin__optimizemember_menu_pages_tb
			{
				/**
				* Toolbox for Menu Pages.
				*
				* @package s2Member\Menu_Pages
				* @since 131108
				*
				* @return null
				*/
				public static function display ()
					{
						do_action("ws_plugin__optimizemember_during_menu_pages_before_toolbox_sections", get_defined_vars ());

						ob_start(); // output buffer these so we can display a toggler conditionally.

						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["menu_pages"]["updates"])
							{
								echo '<a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Newsletter")) . '" target="_blank"><i class="fa fa-envelope"></i> s2 Updates (via Email)</a>';
							}
						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["menu_pages"]["upsell-pro"])
							{
								echo '<a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Pro Module / Prices")) . '" target="_blank" style="font-size:120%; font-weight:bold;"><i class="fa fa-money"></i> s2Member® Pro (Upgrade)</a>' . "\n";
							}
						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["menu_pages"]["installation"])
							{
								echo '<a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Professional Installation URI")) . '" target="_blank"><i class="fa fa-wrench"></i> Professional Installation Service</a>' . "\n";
							}
						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["menu_pages"]["kb"])
							{
								echo '<a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Knowledge Base")) . '" target="_blank"><i class="fa fa-lightbulb-o"></i> Knowledge Base</a>' . "\n";
							}
						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["menu_pages"]["videos"])
							{
								echo '<a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Video Tutorials")) . '" target="_blank"><i class="fa fa-film"></i> Video Tutorials</a>' . "\n";
							}
						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["menu_pages"]["support"])
							{
								echo '<a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Forum URI")) . '" target="_blank"><i class="fa fa-comments-o"></i> Community</a>' . "\n";
							}
						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["menu_pages"]["donations"])
							{
								echo '<a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Donate link")) . '" target="_blank"><i class="fa fa-heart-o"></i> Contribute</a>' . "\n";
							}
						if (($links = ob_get_clean()))
							{
								$links = '<div class="links">'.$links.'</div>';
								echo $links; // output content now; w/ possible toggler.
							}
						do_action("ws_plugin__optimizemember_during_menu_pages_after_toolbox_sections", get_defined_vars ());

						return /* return for uniformity. */;
					}
			}
	}
?>