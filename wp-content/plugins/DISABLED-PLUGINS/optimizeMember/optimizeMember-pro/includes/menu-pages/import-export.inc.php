<?php
/**
* Menu page for optimizeMember Pro ( Import/Export page ).
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* This WordPress plugin ( optimizeMember Pro ) is comprised of two parts:
*
* o (1) Its PHP code is licensed under the GPL license, as is WordPress.
* 	You should have received a copy of the GNU General Public License,
* 	along with this software. In the main directory, see: /licensing/
* 	If not, see: {@link http://www.gnu.org/licenses/}.
*
* o (2) All other parts of ( optimizeMember Pro ); including, but not limited to:
* 	the CSS code, some JavaScript code, images, and design;
* 	are licensed according to the license purchased.
* 	See: {@link http://www.optimizepress.com/prices/}
*
* Unless you have our prior written consent, you must NOT directly or indirectly license,
* sub-license, sell, resell, or provide for free; part (2) of the optimizeMember Pro Module;
* or make an offer to do any of these things. All of these things are strictly
* prohibited with part (2) of the optimizeMember Pro Module.
*
* Your purchase of optimizeMember Pro includes free lifetime upgrades via optimizeMember.com
* ( i.e. new features, bug fixes, updates, improvements ); along with full access
* to our video tutorial library: {@link http://www.optimizepress.com/videos/}
*
* @package optimizeMember\Menu_Pages
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_menu_page_import_export"))
	{
		/**
		* Menu page for optimizeMember Pro ( Import/Export page ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__optimizemember_pro_menu_page_import_export
			{
				public function __construct ()
					{
						echo '<div class="wrap ws-menu-page op-bsw-wizard op-bsw-content">' . "\n";
						/**/
						echo '<div class="op-bsw-header">';
							echo '<div class="op-logo"><img src="' . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images/" . 'logo-optimizepress.png" alt="OptimizePress" height="50" class="animated flipInY"></div>';
						echo '</div>';
						echo '<div class="op-bsw-main-content">';
						echo '<h2>OptimizeMember Pro ( Import/Export Tools )</h2>' . "\n";
						/**/
						echo '<table class="ws-menu-page-table">' . "\n";
						echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
						echo '<tr class="ws-menu-page-table-tr">' . "\n";
						echo '<td class="ws-menu-page-table-l">' . "\n";
						/**/
						if (is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ())
							{
								echo '<div class="ws-menu-page-group" title="User/Member CSV Importation"' . (($_POST["ws_plugin__optimizemember_pro_import_users"]) ? ' default-state="open"' : '') . '>' . "\n";
								/**/
								echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-user-importation-section">' . "\n";
								echo '<h3>User/Member Importation ( upload file / or direct input )</h3>' . "\n";
								echo '<p>Import files can be uploaded in CSV format, or you can copy/paste data into the form field provided. In either case, you MUST follow the examples given below. Please double-check your data before clicking the Import button. Make sure that all data fields are properly encapsulated by double-quotes, and separated by commas. You\'ll also need to make sure that all of your data fields are in the proper order, based on the examples given below. After importing Users/Members, you can inspect your work by going to: <code>WordPress -> Users</code>.</p>' . "\n";
								echo '<p><em><strong>*No Email Notification*</strong> This import routine works silently. Users/Members will NOT be contacted by optimizeMember; that is, unless you have another plugin installed that conflicts with optimizeMember\'s ability to perform the Import properly. You should always test one or two accounts before importing a large number of Users all at once. If you want Users/Members to be contacted, you can add them manually, by going to <code>WordPress -> Users -> Add New</code>, and selecting one of the optimizeMember Roles from the drop-down menu.</em></p>' . "\n";
								echo (version_compare(PHP_VERSION, "5.3", "<")) ? '<p><em><strong>*PHP v5.3+ recommended*</strong> In order for optimizeMember to properly import CSV files containing escape sequences, PHP v5.3 or higher is required. While optimizeMember may be able to parse import files in most cases, PHP v5.3 provides the best stability.</em></p>' . "\n" : '';
								//echo '<p><em><strong class="ws-menu-page-hilite">*IMPORTANT NOTICE*</strong> optimizeMember\'s import format changed in optimizeMember Pro v130203+ ( with respect to Custom Registration/Profile Fields only ). If you are importing Custom Fields, please review <a href="http://www.optimizepress.com/kb/importing-updating-users/#custom-registration-profile-fields" target="_blank" rel="external nofollow x-link">this article</a> before you import new Users/Members or mass update any existing Users/Members. <strong>VERY important!</strong></em></p>' . "\n";
								/**/
								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";
								/**/
								echo '<td>' . "\n";
								echo '<form method="post" enctype="multipart/form-data" name="ws_plugin__optimizemember_pro_import_users_form" id="ws-plugin--optimizemember-pro-import-users-form">' . "\n";
								echo '<input type="hidden" name="ws_plugin__optimizemember_pro_import_users" id="ws-plugin--optimizemember-pro-import-users" value="' . esc_attr (wp_create_nonce ("ws-plugin--optimizemember-pro-import-users")) . '" />' . "\n";
								/**/
								echo '<input type="file" name="ws_plugin__optimizemember_pro_import_users_file" id="ws-plugin--optimizemember-pro-import-users-file" />&nbsp;&nbsp;&nbsp;(up to 1000 lines per file)&nbsp;&nbsp;&nbsp;<input type="submit" class="button-primary" value="Import Now" /><br /><br />' . "\n";
								echo '<textarea name="ws_plugin__optimizemember_pro_import_users_direct_input" id="ws-plugin--optimizemember-pro-import-users-direct-input" rows="10" wrap="off" spellcheck="false" style="width:99%;">' . format_to_edit (trim (stripslashes ($_POST["ws_plugin__optimizemember_pro_import_users_direct_input"]))) . '</textarea><br />' . "\n";
								/**/
								echo 'One User/Member per line please. Here is a quick example:<br />' . "\n";
								echo '<code>"ID","Username","First Name","Last Name","Display Name","Email"</code>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo 'If you fill the ID field, the Import routine will update an account matching the ID you specify ( so long as the account ID does NOT belong to an Administrator, this is for security ). When importing new Users/Members, you can leave the ID field empty Don\'t remove it, just leave it empty ( i.e. <code>""</code> ).<br /><br />'."\n";
								/**/
								echo 'Example: <code>"","Username","First Name","Last Name","Display Name","Email"</code><br /><br />' . "\n";
								/**/
								echo 'Additional extended information can also be included; even Custom Registration/Profile Fields:<br />' . "\n";
								echo '<code>"ID","Username","First Name","Last Name","Display Name","Email","Website","Level[0-9]+ or Role ID","Custom Capabilities","Registration Date ( mm/dd/yyyy )","First Payment Date ( mm/dd/yyyy )","Last Payment Date ( mm/dd/yyyy )","Auto-EOT Date ( mm/dd/yyyy )","Custom Value ( starts w/domain )","Paid Subscr. ID","Paid Subscr. Gateway","Custom Field Value","Another Custom Field Value", ...</code><br /><br />' . "\n";
								/**/
								echo 'Here is a full example with all fields filled in, including extended details; and two Custom Field values:<br />' . "\n";
								echo '<code>"","johnsmith22","John","Smith","John Smith","john.smith@example.com","http://www.example.com/","2","music,videos","12/31/2000","01/10/2001","12/31/2020","12/31/2021","www.example.com|123.357.125.654","I-2342934SSER243","paypal","fishing,biking,computers","xx-large"</code><br /><br />' . "\n";
								/**/
								echo 'Here is a full example with some fields left empty:<br />' . "\n";
								echo '<code>"","johnsmith22","John","Smith","John Smith","john.smith@example.com","","optimizemember_level2","","","","","12/31/2021"</code>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo '<em>* If you supply a Paid Subscr. Gateway, you must use one of these values: <code>paypal</code>, <code>alipay</code>, <code>authnet</code>, <code>ccbill</code>, <code>clickbank</code>, <code>google</code>.</em><br /><br />' . "\n";
								/**/
								echo '<em>* If you supply Custom Fields, your Custom Field values should be listed in alphabetical order, based on your Custom Field IDs (i.e. values listed in the order of your alphabetized Custom Field IDs). This is based on the Custom Field IDs you\'ve configured with optimizeMember. See: <code>optimizeMember -> General Options -> Registration/Profile Fields</code>. In the example above, you will see two Custom Field values (<code>... "fishing,biking,computers","xx-large"</code>). The first value comes first, because it maps to the Custom Field ID <code>interests</code>, and the second one comes last, because it maps to the Custom Field ID <code>t_shirt_size</code> (e.g. alphabetical order — based on the underlying Custom Field IDs that you\'re mapping this data to).</em><br /><br />'."\n";
								echo '<em>* If you have a Custom Field that contains an array of multiple values, you can import the array using PHP\'s <a href="http://php.net/manual/en/function.serialize.php" target="_blank" rel="external">serialize()</a> function. This allows you to convert the array into a string representation. optimizeMember will automatically unserialize the value during importation. If you have any trouble, please perform an export first. optimizeMember\'s export files are already formatted for easy re-importation. In other words, you can use them as a guideline for building your own import files.</em><br /><br />' . "\n";
								/**/
								echo '<em>* If you supply "First Payment Date", you have two options available. You can either supply a simple date in this format ( mm/dd/yyyy ), or you can import an array of First Payment Dates, in the form of Unix timestamps. optimizeMember has the ability to record and monitor First Payment Dates at each specific Membership Level. The array it expects, consists of the following: <code>array("level" => [timestamp of first payment date regardless of level], "level1" => [timestamp of first payment date at level #1], "level2" => [timestamp], "level3" => [timestamp], "level4" => [timestamp])</code>. Of course, if you decide to import an array with some of these timestamps, you will need to use PHP\'s <a href="http://php.net/manual/en/function.serialize.php" target="_blank" rel="external">serialize()</a> function to convert the array into a string representation. If you have any trouble, please perform an export first. optimizeMember\'s export files are already formatted for easy re-importation. In other words, you can use them as a guideline for building your own import files. By default, optimizeMember exports an array of timestamps.</em>' . "\n";
								/**/
								echo '</form>' . "\n";
								echo '</td>' . "\n";
								/**/
								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";
								/**/
								echo '</div>' . "\n";
							}
						else /* Otherwise, we use the standardized format for Importation.*/
							{
								echo '<div class="ws-menu-page-group" title="User/Member CSV Importation"' . ((isset ($_POST["ws_plugin__optimizemember_pro_import_users"])) ? ' default-state="open"' : '') . '>' . "\n";
								/**/
								echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-user-importation-section">' . "\n";
								echo '<h3>User/Member Importation ( upload file / or direct input )</h3>' . "\n";
								echo '<p>Import files can be uploaded in CSV format, or you can copy/paste data into the form field provided. In either case, you MUST follow the examples given below. Please double-check your data before clicking the Import button. Make sure that all data fields are properly encapsulated by double-quotes, and separated by commas. You\'ll also need to make sure that all of your data fields are in the proper order, based on the examples given below. After importing Users/Members, you can inspect your work by going to: <code>WordPress -> Users</code>.</p>' . "\n";
								echo '<p><em><strong>*No Email Notification*</strong> This import routine works silently. Users/Members will NOT be contacted by optimizeMember; that is, unless you have another plugin installed that conflicts with optimizeMember\'s ability to perform the Import properly. You should always test one or two accounts before importing a large number of Users all at once. If you want Users/Members to be contacted, you can add them manually, by going to <code>WordPress -> Users -> Add New</code>, and selecting one of the optimizeMember Roles from the drop-down menu.</em></p>' . "\n";
								echo (version_compare(PHP_VERSION, "5.3", "<")) ? '<p><em><strong>*PHP v5.3+ recommended*</strong> In order for optimizeMember to properly import CSV files containing escape sequences, PHP v5.3 or higher is required. While optimizeMember may be able to parse import files in most cases, PHP v5.3 provides the best stability.</em></p>' . "\n" : '';
								//echo '<p><em><strong class="ws-menu-page-hilite">*IMPORTANT NOTICE*</strong> optimizeMember\'s import format changed in optimizeMember Pro v130203+ ( with respect to Custom Registration/Profile Fields only ). If you are importing Custom Fields, please review <a href="http://www.optimizepress.com/kb/importing-updating-users/#custom-registration-profile-fields" target="_blank" rel="external nofollow x-link">this article</a> before you import new Users/Members or mass update any existing Users/Members. <strong>VERY important!</strong></em></p>' . "\n";
								/**/
								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";
								/**/
								echo '<td>' . "\n";
								echo '<form method="post" enctype="multipart/form-data" name="ws_plugin__optimizemember_pro_import_users_form" id="ws-plugin--optimizemember-pro-import-users-form">' . "\n";
								echo '<input type="hidden" name="ws_plugin__optimizemember_pro_import_users" id="ws-plugin--optimizemember-pro-import-users" value="' . esc_attr (wp_create_nonce ("ws-plugin--optimizemember-pro-import-users")) . '" />' . "\n";
								/**/
								echo '<input type="file" name="ws_plugin__optimizemember_pro_import_users_file" id="ws-plugin--optimizemember-pro-import-users-file" />&nbsp;&nbsp;&nbsp;(up to 1000 lines per file)&nbsp;&nbsp;&nbsp;<input type="submit" class="button-primary" value="Import Now" /><br /><br />' . "\n";
								echo '<textarea name="ws_plugin__optimizemember_pro_import_users_direct_input" id="ws-plugin--optimizemember-pro-import-users-direct-input" rows="10" wrap="off" spellcheck="false" style="width:99%;">' . format_to_edit (trim (stripslashes (@$_POST["ws_plugin__optimizemember_pro_import_users_direct_input"]))) . '</textarea><br />' . "\n";
								/**/
								echo 'One User/Member per line please. Here is a quick example:<br />' . "\n";
								echo '<code>"ID","Username","Password","First Name","Last Name","Display Name","Email"</code><br /><br />' . "\n";
								/**/
								echo 'If you fill the ID field, the Import routine will update an account matching the ID you specify ( so long as the account ID does NOT belong to an Administrator, this is for security ). When importing new Users/Members, you can leave the ID field empty Don\'t remove it, just leave it empty ( i.e. <code>""</code> ).<br /><br />'."\n";
								/**/
								echo 'Example: <code>"","Username","First Name","Last Name","Display Name","Email"</code>' . "\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>' . "\n";
								/**/
								echo 'Additional extended information can also be included; even Custom Registration/Profile Fields:<br />' . "\n";
								//echo 'Please see this KB article for more detailed instructions: <a href="http://www.optimizepress.com/kb/importing-updating-users/" target="_blank" rel="external nofollow x-link">Import (Or Mass Update Users)</a>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";
								/**/
								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";
								/**/
								echo '</div>' . "\n";
							}
						/**/
						echo '<div class="ws-menu-page-group" title="User/Member CSV Exportation">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-user-exportation-section">' . "\n";
						echo '<h3>User/Member Exportation ( download CSV export files )</h3>' . "\n";
						/**/
						echo '<form method="post" name="ws_plugin__optimizemember_pro_export_users_form" id="ws-plugin--optimizemember-pro-export-users-form">' . "\n";
						echo '<input type="hidden" name="ws_plugin__optimizemember_pro_export_users" id="ws-plugin--optimizemember-pro-export-users" value="' . esc_attr (wp_create_nonce ("ws-plugin--optimizemember-pro-export-users")) . '" />' . "\n";
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<th>' . "\n";
						echo '<label for="ws-plugin--optimizemember-pro-export-users-format">' . "\n";
						echo 'CSV File Preference:' . "\n";
						echo '</label>' . "\n";
						echo '</th>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<select name="ws_plugin__optimizemember_pro_export_users_format" id="ws-plugin--optimizemember-pro-export-users-format">' . "\n";
						echo '<option value="" selected="selected">Default ( CSV, perfectly formatted for easy re-importation )</option>' . "\n";
						echo '<option value="readable">Easy-Read ( CSV w/ improved readability; CANNOT be re-imported )</option>' . "\n";
						echo '</select><br />' . "\n";
						echo '<em>Open CSV files with Notepad, MS Excel, or <a href="http://www.openoffice.org/" target="_blank" rel="external">OpenOffice</a> ( recommended ).</em>';
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<th>' . "\n";
						echo '<label for="ws-plugin--optimizemember-pro-export-users-start">' . "\n";
						echo 'CSV File Exportation:' . "\n";
						echo '</label>' . "\n";
						echo '</th>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo 'You have a total of ' . number_format (c_ws_plugin__optimizemember_utils_users::users_in_database ()) . ' User/Member rows in the database' . ((is_multisite ()) ? ' for this site' : '') . '.<br />' . "\n";
						echo 'You can export up to 1000 database rows in each file; starting from a particular row that you specify.<br />' . "\n";
						echo 'Export, starting with row#: <input type="text" autocomplete="off" name="ws_plugin__optimizemember_pro_export_users_start" id="ws-plugin--optimizemember-pro-export-users-start" style="width:100px;" value="1" class="input-inline" /> <input type="submit" class="button-primary" value="Export Now" />' . "\n";
						/**/
						echo '<div class="ws-menu-page-hr"></div>' . "\n";
						/**/
						echo '<em>Please note. Export files do NOT contain Passwords. Passwords are stored by WordPress with one-way encryption. In other words, it\'s not possible for optimizeMember to include them in the export file. However, this does NOT create a problem, because when/if you re-import existing Users/Members with the Password field empty, optimizeMember will simply keep the existing Password that is already on file. For further information, please read all Import instructions, regarding Passwords.</em>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						/**/
						echo '</form>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '<div class="ws-menu-page-group" title="optimizeMember Options ( Import/Export )"' . ((isset ($_POST["ws_plugin__optimizemember_pro_import_ops"])) ? ' default-state="open"' : '') . '>' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-ops-importation-exportation-section">' . "\n";
						/**/
						echo '<h3 style="margin-bottom:5px;">optimizeMember Options Export <small>( <a href="' . esc_attr (site_url ("/?ws_plugin__optimizemember_pro_export_ops=" . urlencode (wp_create_nonce ("ws-plugin--optimizemember-pro-export-ops")))) . '">download serialized export file</a> )</small></h3>' . "\n";
						echo '<p style="margin-top:5px;">This allows you to export your current optimizeMember configuration, and then import it into another instance of WordPress.' . "\n";
						/**/
						echo '<div class="ws-menu-page-hr"></div>' . "\n";
						/**/
						echo '<h3 style="margin-bottom:5px;">optimizeMember Options Import <small>( upload your serialized export file )</small></h3>' . "\n";
						echo '<p style="margin-top:5px;">This allows you to import your optimizeMember configuration export file, from another instance of WordPress.' . "\n";
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<form method="post" enctype="multipart/form-data" name="ws_plugin__optimizemember_pro_import_ops_form" id="ws-plugin--optimizemember-pro-import-ops-form">' . "\n";
						echo '<input type="hidden" name="ws_plugin__optimizemember_pro_import_ops" id="ws-plugin--optimizemember-pro-import-ops" value="' . esc_attr (wp_create_nonce ("ws-plugin--optimizemember-pro-import-ops")) . '" />' . "\n";
						echo '<input type="file" name="ws_plugin__optimizemember_pro_import_ops_file" id="ws-plugin--optimizemember-pro-import-ops-file" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-primary" value="Import Now" />' . "\n";
						echo '</form>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '</td>' . "\n";
						/**/
						echo '<td class="ws-menu-page-table-r">' . "\n";
						c_ws_plugin__optimizemember_menu_pages_rs::display ();
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						/**/
						echo '</div>' . "\n";
						echo '</div>' . "\n";
					}
			}
	}
/**/
new c_ws_plugin__optimizemember_pro_menu_page_import_export ();
?>