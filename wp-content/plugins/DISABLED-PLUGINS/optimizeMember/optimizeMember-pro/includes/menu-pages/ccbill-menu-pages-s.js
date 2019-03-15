/**
* Core JavaScript routines for ccBill menu pages.
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
jQuery(document).ready (function($)
	{
		var esc_attr = esc_html = /* Convert special characters. */ function(str)
			{
				return String(str).replace (/"/g, '&quot;').replace (/\</g, '&lt;').replace (/\>/g, '&gt;');
			};
		/**/
		if (location.href.match (/page\=ws-plugin--optimizemember-pro-ccbill-ops/))
			{
				$('select#ws-plugin--optimizemember-auto-eot-system-enabled').change (function()
					{
						var $this = $(this), val = $this.val ();
						var $viaCron = $('p#ws-plugin--optimizemember-auto-eot-system-enabled-via-cron');
						/**/
						if /* Display Cron instructions. */ (val == 2)
							$viaCron.show ()
						else /* Hide instructions. */
							$viaCron.hide ();
					});
			}
		/**/
		else if (location.href.match (/page\=ws-plugin--optimizemember-pro-ccbill-buttons/))
			{
				$('div.ws-menu-page select[id]').filter ( /* Filter all select elements with an id. */function()
					{
						return this.id.match (/^ws-plugin--optimizemember-pro-level[1-9][0-9]*-term$/);
					}).change (function()
					{
						var button = this.id.replace (/^ws-plugin--optimizemember-pro-(.+?)-term$/g, '$1');
						/**/
						var trialDisabled = ($(this).val ().split ('-')[2].replace (/[^0-1]/g, '') === '0') ? 1 : 0;
						/**/
						$('p#ws-plugin--optimizemember-pro-' + button + '-trial-line').css ('display', (trialDisabled ? 'none' : ''));
						$('span#ws-plugin--optimizemember-pro-' + button + '-trial-then').css ('display', (trialDisabled ? 'none' : ''));
						/**/
						(trialDisabled) ? $('input#ws-plugin--optimizemember-pro-' + button + '-trial-period').val (0) : null;
						(trialDisabled) ? $('input#ws-plugin--optimizemember-pro-' + button + '-trial-amount').val ('0.00') : null;
					});
				/**/
				$('div.ws-menu-page input[id]').filter ( /* Filter all input elements with an id. */function()
					{
						return this.id.match (/^ws-plugin--optimizemember-pro-(level[1-9][0-9]*|ccap)-ccaps$/);
					}).keyup (function()
					{
						var value = this.value.replace (/^(-all|-al|-a|-)[;,]*/gi, ''), _all = (this.value.match (/^(-all|-al|-a|-)[;,]*/i)) ? '-all,' : '';
						if /* Only if there is a problem with the actual values; because this causes interruptions. */ (value.match (/[^a-z_0-9,]/))
							this.value = _all + $.trim ($.trim (value).replace (/[ \-]/g, '_').replace (/[^a-z_0-9,]/gi, '').toLowerCase ());
					});
				/**/
				ws_plugin__optimizemember_pro_ccbillButtonGenerate = /* Handles ccBill Button Generation. */ function(button)
					{
						var shortCodeTemplate = '[optimizeMember-Pro-ccBill-Button %%attrs%% image="default" output="anchor" /]', shortCodeTemplateAttrs = '', labels = {};
						/**/
						eval("<?php echo c_ws_plugin__optimizemember_utils_strings::esc_dq($vars['labels']); ?>");
						/**/
						var shortCode = $('input#ws-plugin--optimizemember-pro-' + button + '-shortcode');
						/**/
						var level = /* Just strip the button name to get the Level number. */ button.replace (/^level/, '');
						var label = /* Labels may NOT contain double-quotes. */ labels['level' + level].replace (/"/g, "");
						var desc = $.trim ($('input#ws-plugin--optimizemember-pro-' + button + '-desc').val ().replace (/"/g, ""));
						/**/
						var trialAmount = $('input#ws-plugin--optimizemember-pro-' + button + '-trial-amount').val ().replace (/[^0-9\.]/g, '');
						var trialPeriod = $('input#ws-plugin--optimizemember-pro-' + button + '-trial-period').val ().replace (/[^0-9]/g, '');
						var trialTerm = $('select#ws-plugin--optimizemember-pro-' + button + '-trial-term').val ().replace (/[^A-Z]/g, '');
						/**/
						var regAmount = $('input#ws-plugin--optimizemember-pro-' + button + '-amount').val ().replace (/[^0-9\.]/g, '');
						var regPeriod = $('select#ws-plugin--optimizemember-pro-' + button + '-term').val ().split ('-')[0].replace (/[^0-9]/g, '');
						var regTerm = $('select#ws-plugin--optimizemember-pro-' + button + '-term').val ().split ('-')[1].replace (/[^A-Z]/g, '');
						var regRecur = $('select#ws-plugin--optimizemember-pro-' + button + '-term').val ().split ('-')[2].replace (/[^0-1]/g, '');
						/**/
						var currencyCode = $('select#ws-plugin--optimizemember-pro-' + button + '-currency').val ().replace (/[^A-Z]/g, '');
						/**/
						var cCaps = $.trim ($.trim ($('input#ws-plugin--optimizemember-pro-' + button + '-ccaps').val ()).replace (/^(-all|-al|-a|-)[;,]*/gi, '').replace (/[ \-]/g, '_').replace (/[^a-z_0-9,]/gi, '').toLowerCase ());
						cCaps = ($.trim ($('input#ws-plugin--optimizemember-pro-' + button + '-ccaps').val ()).match (/^(-all|-al|-a|-)[;,]*/i)) ? ((cCaps) ? '-all,' : '-all') + cCaps.toLowerCase () : cCaps.toLowerCase ();
						/**/
						trialPeriod = /* Buy Now / Non-Recurring is absolutely NOT compatible w/ Trial Periods. */ (!regRecur || regRecur === '0') ? '0' : trialPeriod;
						trialAmount = /* Check Trial Amount. */ (!trialAmount || isNaN(trialAmount) || trialAmount < 0.01 || trialPeriod <= 0) ? '0' : trialAmount;
						/**/
						var levelCcapsPer = (!regRecur || regRecur === '0') ? level + ':' + cCaps + ':' + regPeriod + ' ' + regTerm : level + ':' + cCaps;
						levelCcapsPer = /* Clean any trailing separators from this string. */ levelCcapsPer.replace (/\:+$/g, '');
						/**/
						if (trialAmount !== '0' && (isNaN(trialAmount) || trialAmount < 2.95))
							{
								alert('— Oops, a slight problem: —\n\nWhen provided, Trial Amount must be >= 2.95');
								return false;
							}
						else if (trialTerm === 'D' && trialPeriod > 0 && /* Min: 2. */ trialPeriod < 2)
							{
								alert('— Oops, a slight problem: —\n\nMinimum Trial Days is: 2.');
								return false;
							}
						else if (trialTerm === 'D' && trialPeriod > /* Max: 365 days. */ 365)
							{
								alert('— Oops, a slight problem: —\n\nMaximum Trial Days is: 365.');
								return false;
							}
						else if (!regAmount || isNaN(regAmount) || regAmount < 2.95)
							{
								alert('— Oops, a slight problem: —\n\nAmount must be >= 2.95');
								return false;
							}
						else if /* Each Button should have a Description. */ (!desc)
							{
								alert('— Oops, a slight problem: —\n\nPlease type a Description for this Button.');
								return false;
							}
						/**/
						shortCodeTemplateAttrs += 'level="' + esc_attr(level) + '" ccaps="' + esc_attr(cCaps) + '" desc="' + esc_attr(desc) + '" cc="' + esc_attr(currencyCode) + '" custom="<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>"';
						shortCodeTemplateAttrs += ' ta="' + esc_attr(trialAmount) + '" tp="' + esc_attr(trialPeriod) + '" tt="' + esc_attr(trialTerm) + '" ra="' + esc_attr(regAmount) + '" rp="' + esc_attr(regPeriod) + '" rt="' + esc_attr(regTerm) + '" rr="' + esc_attr(regRecur) + '"';
						shortCode.val (shortCodeTemplate.replace (/%%attrs%%/, shortCodeTemplateAttrs));
						/**/
						alert('Your Button has been generated.\nPlease copy/paste the Shortcode into your Membership Options Page.');
						/**/
						shortCode.each ( /* Focus and select the Shortcode. */function()
							{
								this.focus (), this.select ();
							});
						/**/
						return false;
					};
				/**/
				ws_plugin__optimizemember_pro_ccbillCcapButtonGenerate = /* Handles ccBill Button Generation. */ function()
					{
						var shortCodeTemplate = '[optimizeMember-Pro-ccBill-Button %%attrs%% image="default" output="anchor" /]', shortCodeTemplateAttrs = '';
						/**/
						var shortCode = $('input#ws-plugin--optimizemember-pro-ccap-shortcode');
						/**/
						var desc = $.trim ($('input#ws-plugin--optimizemember-pro-ccap-desc').val ().replace (/"/g, ""));
						/**/
						var regAmount = $('input#ws-plugin--optimizemember-pro-ccap-amount').val ().replace (/[^0-9\.]/g, '');
						var regPeriod = $('select#ws-plugin--optimizemember-pro-ccap-term').val ().split ('-')[0].replace (/[^0-9]/g, '');
						var regTerm = $('select#ws-plugin--optimizemember-pro-ccap-term').val ().split ('-')[1].replace (/[^A-Z]/g, '');
						var regRecur = $('select#ws-plugin--optimizemember-pro-ccap-term').val ().split ('-')[2].replace (/[^0-1]/g, '');
						/**/
						var currencyCode = $('select#ws-plugin--optimizemember-pro-ccap-currency').val ().replace (/[^A-Z]/g, '');
						/**/
						var cCaps = $.trim ($.trim ($('input#ws-plugin--optimizemember-pro-ccap-ccaps').val ()).replace (/^(-all|-al|-a|-)[;,]*/gi, '').replace (/[ \-]/g, '_').replace (/[^a-z_0-9,]/gi, '').toLowerCase ());
						cCaps = ($.trim ($('input#ws-plugin--optimizemember-pro-ccap-ccaps').val ()).match (/^(-all|-al|-a|-)[;,]*/i)) ? ((cCaps) ? '-all,' : '-all') + cCaps.toLowerCase () : cCaps.toLowerCase ();
						/**/
						var levelCcapsPer = ((!regRecur || regRecur === '0') && regTerm !== 'L') ? '*:' + cCaps + ':' + regPeriod + ' ' + regTerm : '*:' + cCaps;
						levelCcapsPer = /* Clean any trailing separators from this string. */ levelCcapsPer.replace (/\:+$/g, '');
						/**/
						if (!regAmount || isNaN(regAmount) || regAmount < 2.95)
							{
								alert('— Oops, a slight problem: —\n\nAmount must be >= 2.95');
								return false;
							}
						else if /* Must have some Independent Custom Capabilities. */ (!cCaps || cCaps === '-all')
							{
								alert('— Oops, a slight problem: —\n\nPlease provide at least one Custom Capability.');
								return false;
							}
						else if /* Each Button should have a Description. */ (!desc)
							{
								alert('— Oops, a slight problem: —\n\nPlease type a Description for this Button.');
								return false;
							}
						/**/
						shortCodeTemplateAttrs += 'level="*" ccaps="' + esc_attr(cCaps) + '" desc="' + esc_attr(desc) + '" cc="' + esc_attr(currencyCode) + '" custom="<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>"';
						shortCodeTemplateAttrs += ' ra="' + esc_attr(regAmount) + '" rp="' + esc_attr(regPeriod) + '" rt="' + esc_attr(regTerm) + '" rr="' + esc_attr(regRecur) + '"';
						shortCode.val (shortCodeTemplate.replace (/%%attrs%%/, shortCodeTemplateAttrs));
						/**/
						alert('Your Button has been generated.\nPlease copy/paste the Shortcode into your WordPress Editor.');
						/**/
						shortCode.each ( /* Focus and select the Shortcode. */function()
							{
								this.focus (), this.select ();
							});
						/**/
						return false;
					};
				/**/
				ws_plugin__optimizemember_pro_ccbillSpButtonGenerate = /* Handles ccBill Button Generation for Specific Post/Page Access. */ function()
					{
						var shortCodeTemplate = '[optimizeMember-Pro-ccBill-Button %%attrs%% image="default" output="button" /]', shortCodeTemplateAttrs = '';
						/**/
						var shortCode = $('input#ws-plugin--optimizemember-pro-sp-shortcode');
						/**/
						var leading = $('select#ws-plugin--optimizemember-pro-sp-leading-id').val ().replace (/[^0-9]/g, '');
						var additionals = $('select#ws-plugin--optimizemember-pro-sp-additional-ids').val () || [];
						var hours = $('select#ws-plugin--optimizemember-pro-sp-hours').val ().replace (/[^0-9]/g, '');
						var regAmount = $('input#ws-plugin--optimizemember-pro-sp-amount').val ().replace (/[^0-9\.]/g, '');
						var desc = $.trim ($('input#ws-plugin--optimizemember-pro-sp-desc').val ().replace (/"/g, ''));
						var currencyCode = $('select#ws-plugin--optimizemember-pro-sp-currency').val ().replace (/[^A-Z]/g, '');
						/**/
						if /* Must have a Leading Post/Page ID to work with. Otherwise, Link generation will fail. */ (!leading)
							{
								alert('— Oops, a slight problem: —\n\nPlease select a Leading Post/Page.\n\n*Tip* If there are no Posts/Pages in the menu, it\'s because you\'ve not configured optimizeMember for Specific Post/Page Access yet. See: optimizeMember -> Restriction Options -> Specific Post/Page Access.');
								return false;
							}
						else if (!regAmount || isNaN(regAmount) || regAmount < 2.95)
							{
								alert('— Oops, a slight problem: —\n\nAmount must be >= 2.95');
								return false;
							}
						else if /* Each Button should have a Description. */ (!desc)
							{
								alert('— Oops, a slight problem: —\n\nPlease type a Description for this Button.');
								return false;
							}
						/**/
						for (var i = 0, ids = leading; i < additionals.length; i++)
							if (additionals[i] && additionals[i] !== leading)
								ids += ',' + additionals[i];
						/**/
						var spIdsHours = /* Combined sp:ids:expiration hours. */ 'sp:' + ids + ':' + hours;
						/**/
						shortCodeTemplateAttrs += 'sp="1" ids="' + esc_attr(ids) + '" exp="' + esc_attr(hours) + '" desc="' + esc_attr(desc) + '" cc="' + esc_attr(currencyCode) + '"';
						shortCodeTemplateAttrs += ' custom="<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>" ra="' + esc_attr(regAmount) + '"';
						shortCode.val (shortCodeTemplate.replace (/%%attrs%%/, shortCodeTemplateAttrs));
						/**/
						alert('Your Button has been generated.\nPlease copy/paste the Shortcode into your WordPress Editor.');
						/**/
						shortCode.each ( /* Focus and select the Shortcode. */function()
							{
								this.focus (), this.select ();
							});
						/**/
						return false;
					};
				/**/
				ws_plugin__optimizemember_pro_ccbillRegLinkGenerate = /* Handles ccBill Link Generation. */ function()
					{
						var level = $('select#ws-plugin--optimizemember-pro-reg-link-level').val ().replace (/[^0-9]/g, '');
						var subscrID = $.trim ($('input#ws-plugin--optimizemember-pro-reg-link-subscr-id').val ());
						var custom = $.trim ($('input#ws-plugin--optimizemember-pro-reg-link-custom').val ());
						var cCaps = $.trim ($.trim ($('input#ws-plugin--optimizemember-pro-reg-link-ccaps').val ()).replace (/[ \-]/g, '_').replace (/[^a-z_0-9,]/gi, '').toLowerCase ());
						var fixedTerm = $.trim ($('input#ws-plugin--optimizemember-pro-reg-link-fixed-term').val ().replace (/[^A-Z 0-9]/gi, '').toUpperCase ());
						var $link = $('p#ws-plugin--optimizemember-pro-reg-link'), $loading = $('img#ws-plugin--optimizemember-pro-reg-link-loading');
						/**/
						var levelCcapsPer = (fixedTerm && !fixedTerm.match (/L$/)) ? level + ':' + cCaps + ':' + fixedTerm : level + ':' + cCaps;
						levelCcapsPer = /* Clean any trailing separators from this string. */ levelCcapsPer.replace (/\:+$/g, '');
						/**/
						if /* We must have a Paid Subscr. ID value. */ (!subscrID)
							{
								alert('— Oops, a slight problem: —\n\nPaid Subscr. ID is a required value.');
								return false;
							}
						else if (!custom || custom.indexOf ('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq ($_SERVER["HTTP_HOST"]); ?>') !== 0)
							{
								alert('— Oops, a slight problem: —\n\nThe Custom Value MUST start with your domain name.');
								return false;
							}
						else if /* Check format. */ (fixedTerm && !fixedTerm.match (/^[1-9]+ (D|W|M|Y|L)$/))
							{
								alert('— Oops, a slight problem: —\n\nThe Fixed Term Length is not formatted properly.');
								return false;
							}
						/**/
						$link.hide (), $loading.show (), $.post (ajaxurl, {action: 'ws_plugin__optimizemember_reg_access_link_via_ajax', ws_plugin__optimizemember_reg_access_link_via_ajax: '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (wp_create_nonce ("ws-plugin--optimizemember-reg-access-link-via-ajax")); ?>', optimizemember_reg_access_link_subscr_gateway: 'ccbill', optimizemember_reg_access_link_subscr_id: subscrID, optimizemember_reg_access_link_custom: custom, optimizemember_reg_access_link_item_number: levelCcapsPer}, function(response)
							{
								$link.show ().html ('<a href="' + esc_attr(response) + '" target="_blank" rel="external">' + esc_html(response) + '</a>'), $loading.hide ();
							});
						/**/
						return false;
					};
				/**/
				ws_plugin__optimizemember_pro_ccbillSpLinkGenerate = /* Handles ccBill Link Generation. */ function()
					{
						var leading = $('select#ws-plugin--optimizemember-pro-sp-link-leading-id').val ().replace (/[^0-9]/g, '');
						var additionals = $('select#ws-plugin--optimizemember-pro-sp-link-additional-ids').val () || [];
						var hours = $('select#ws-plugin--optimizemember-pro-sp-link-hours').val ().replace (/[^0-9]/g, '');
						var $link = $('p#ws-plugin--optimizemember-pro-sp-link'), $loading = $('img#ws-plugin--optimizemember-pro-sp-link-loading');
						/**/
						if /* Must have a Leading Post/Page ID to work with. Otherwise, Link generation will fail. */ (!leading)
							{
								alert('— Oops, a slight problem: —\n\nPlease select a Leading Post/Page.\n\n*Tip* If there are no Posts/Pages in the menu, it\'s because you\'ve not configured optimizeMember for Specific Post/Page Access yet. See: optimizeMember -> Restriction Options -> Specific Post/Page Access.');
								return false;
							}
						/**/
						for (var i = 0, ids = leading; i < additionals.length; i++)
							if (additionals[i] && additionals[i] !== leading)
								ids += ',' + additionals[i];
						/**/
						$link.hide (), $loading.show (), $.post (ajaxurl, {action: 'ws_plugin__optimizemember_sp_access_link_via_ajax', ws_plugin__optimizemember_sp_access_link_via_ajax: '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (wp_create_nonce ("ws-plugin--optimizemember-sp-access-link-via-ajax")); ?>', optimizemember_sp_access_link_ids: ids, optimizemember_sp_access_link_hours: hours}, function(response)
							{
								$link.show ().html ('<a href="' + esc_attr(response) + '" target="_blank" rel="external">' + esc_html(response) + '</a>'), $loading.hide ();
							});
						/**/
						return false;
					};
			}
	});