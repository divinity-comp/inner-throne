jQuery(document).ready(function(x){var e,o,h,k,n,G,v,u={"aria-required":"true"},b={"aria-required":"false"},y={disabled:"disabled"},g={"aria-required":"false",disabled:"disabled"},a={"aria-required":"false",readonly:"readonly"};v=new Image(),v.src='<?php echo $vars["i"]; ?>/ajax-loader.gif';if((e=x("form#s2member-pro-authnet-cancellation-form")).length===1){var J="div#s2member-pro-authnet-cancellation-form-captcha-section",l="div#s2member-pro-authnet-cancellation-form-submission-section",t=x(l+" input#s2member-pro-authnet-cancellation-submit");ws_plugin__optimizemember_animateProcessing(t,"reset"),t.removeAttr("disabled");e.submit(function(){var P=this,N="",M="",Q="";var O=x(J+" input#recaptcha_response_field");x(":input",P).each(function(){var R=x.trim(x(this).attr("id")).replace(/-[0-9]+$/g,"");if(R&&(N=x.trim(x('label[for="'+R+'"]',P).first().children("span").first().text().replace(/[\r\n\t]+/g," ")))){if(M=ws_plugin__optimizemember_validationErrors(N,this,P)){Q+=M+"\n\n"}}});if(Q=x.trim(Q)){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n'+Q);return false}else{if(O.length&&!O.val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');return false}}t.attr(y),ws_plugin__optimizemember_animateProcessing(t);return true})}else{if((o=x("form#s2member-pro-authnet-update-form")).length===1){var r,s="div#s2member-pro-authnet-update-form-billing-method-section",D="div#s2member-pro-authnet-update-form-billing-address-section",j=s+' input[name="optimizemember_pro_authnet_update[card_type]"]',J="div#s2member-pro-authnet-update-form-captcha-section",l="div#s2member-pro-authnet-update-form-submission-section",t=x(l+" input#s2member-pro-authnet-update-submit");ws_plugin__optimizemember_animateProcessing(t,"reset"),t.removeAttr("disabled");(r=function(M){var N=x(j+":checked").val();if(x.inArray(N,["Visa","MasterCard","Amex","Discover"])!==-1){x(s+" > div.s2member-pro-authnet-update-form-div").show();x(s+" > div.s2member-pro-authnet-update-form-div :input").attr(u);x(s+" > div#s2member-pro-authnet-update-form-card-start-date-issue-number-div").hide();x(s+" > div#s2member-pro-authnet-update-form-card-start-date-issue-number-div :input").attr(b);x(D+" > div.s2member-pro-authnet-update-form-div").show();x(D+" > div.s2member-pro-authnet-update-form-div :input").attr(u);x(D).show(),(M)?x(s+" input#s2member-pro-authnet-update-card-number").focus():null}else{if(x.inArray(N,["Maestro","Solo"])!==-1){x(s+" > div.s2member-pro-authnet-update-form-div").show();x(s+" > div.s2member-pro-authnet-update-form-div :input").attr(u);x(D+" > div.s2member-pro-authnet-update-form-div").show();x(D+" > div.s2member-pro-authnet-update-form-div :input").attr(u);x(D).show(),(M)?x(s+" input#s2member-pro-authnet-update-card-number").focus():null}else{if(!N){x(s+" > div.s2member-pro-authnet-update-form-div").hide();x(s+" > div.s2member-pro-authnet-update-form-div :input").attr(b);x(s+" > div#s2member-pro-authnet-update-form-card-type-div").show();x(s+" > div#s2member-pro-authnet-update-form-card-type-div :input").attr(u);x(D+" > div.s2member-pro-authnet-update-form-div").hide();x(D+" > div.s2member-pro-authnet-update-form-div :input").attr(b);x(D).hide(),(M)?x(l+" input#s2member-pro-authnet-update-submit").focus():null}}}})();x(j).click(r).change(r);o.submit(function(){var P=this,N="",M="",Q="";var O=x(J+" input#recaptcha_response_field");if(!x(j+":checked").val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Please choose a Billing Method.", "s2member-front", "s2member")); ?>');return false}x(":input",P).each(function(){var R=x.trim(x(this).attr("id")).replace(/-[0-9]+$/g,"");if(R&&(N=x.trim(x('label[for="'+R+'"]',P).first().children("span").first().text().replace(/[\r\n\t]+/g," ")))){if(M=ws_plugin__optimizemember_validationErrors(N,this,P)){Q+=M+"\n\n"}}});if(Q=x.trim(Q)){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n'+Q);return false}else{if(O.length&&!O.val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');return false}}t.attr(y),ws_plugin__optimizemember_animateProcessing(t);return true})}else{if((h=x("form#s2member-pro-authnet-registration-form")).length===1){var H,p,I="div#s2member-pro-authnet-registration-form-registration-section",J="div#s2member-pro-authnet-registration-form-captcha-section",l="div#s2member-pro-authnet-registration-form-submission-section",t=x(l+" input#s2member-pro-authnet-registration-submit");ws_plugin__optimizemember_animateProcessing(t,"reset"),t.removeAttr("disabled");(H=function(M){if(x(l+" input#s2member-pro-authnet-registration-names-not-required-or-not-possible").length){x(I+" > div#s2member-pro-authnet-registration-form-first-name-div").hide();x(I+" > div#s2member-pro-authnet-registration-form-first-name-div :input").attr(a);x(I+" > div#s2member-pro-authnet-registration-form-last-name-div").hide();x(I+" > div#s2member-pro-authnet-registration-form-last-name-div :input").attr(a)}})();(p=function(M){if(x(l+" input#s2member-pro-authnet-registration-password-not-required-or-not-possible").length){x(I+" > div#s2member-pro-authnet-registration-form-password-div").hide();x(I+" > div#s2member-pro-authnet-registration-form-password-div :input").attr(a)}})();x(I+" > div#s2member-pro-authnet-registration-form-password-div :input").keyup(function(){ws_plugin__optimizemember_passwordStrength(x(I+" input#s2member-pro-authnet-registration-username"),x(I+" input#s2member-pro-authnet-registration-password1"),x(I+" input#s2member-pro-authnet-registration-password2"),x(I+" div#s2member-pro-authnet-registration-form-password-strength"))});h.submit(function(){var P=this,N="",M="",S="";var O=x(J+" input#recaptcha_response_field");var R=x(I+' input#s2member-pro-authnet-registration-password1[aria-required="true"]');var Q=x(I+" input#s2member-pro-authnet-registration-password2");x(":input",P).each(function(){var T=x.trim(x(this).attr("id")).replace(/-[0-9]+$/g,"");if(T&&(N=x.trim(x('label[for="'+T+'"]',P).first().children("span").first().text().replace(/[\r\n\t]+/g," ")))){if(M=ws_plugin__optimizemember_validationErrors(N,this,P)){S+=M+"\n\n"}}});if(S=x.trim(S)){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n'+S);return false}else{if(R.length&&x.trim(R.val())!==x.trim(Q.val())){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Passwords do not match up. Please try again.", "s2member-front", "s2member")); ?>');return false}else{if(R.length&&x.trim(R.val()).length<6){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Password MUST be at least 6 characters. Please try again.", "s2member-front", "s2member")); ?>');return false}else{if(O.length&&!O.val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');return false}}}}t.attr(y),ws_plugin__optimizemember_animateProcessing(t);return true})}else{if((k=x("form#s2member-pro-authnet-sp-checkout-form")).length===1){var B,F,q=true,E,m,f,z,c,A,r,C="div#s2member-pro-authnet-sp-checkout-form-coupon-section",K=C+" input#s2member-pro-authnet-sp-checkout-coupon-apply",I="div#s2member-pro-authnet-sp-checkout-form-registration-section",s="div#s2member-pro-authnet-sp-checkout-form-billing-method-section",j=s+' input[name="optimizemember_pro_authnet_sp_checkout[card_type]"]',D="div#s2member-pro-authnet-sp-checkout-form-billing-address-section",w=x(D+" > div#s2member-pro-authnet-sp-checkout-form-ajax-tax-div"),J="div#s2member-pro-authnet-sp-checkout-form-captcha-section",l="div#s2member-pro-authnet-sp-checkout-form-submission-section",i=l+" input#s2member-pro-authnet-sp-checkout-nonce",d=l+" input#s2member-pro-authnet-sp-checkout-submit";ws_plugin__optimizemember_animateProcessing(x(d),"reset"),x(d).removeAttr("disabled"),x(K).removeAttr("disabled");(B=function(M){if(x(l+" input#s2member-pro-authnet-sp-checkout-coupons-not-required-or-not-possible").length){x(C).hide()}else{x(C).show()}})();(F=function(M){if(x(l+" input#s2member-pro-authnet-sp-checkout-tax-not-required-or-not-possible").length){w.hide(),q=false}})();(E=function(N){if(q&&!(N&&N.interval&&document.activeElement.id==="s2member-pro-authnet-sp-checkout-country")){var M=x(l+" input#s2member-pro-authnet-sp-checkout-attr").val();var Q=x.trim(x(D+" input#s2member-pro-authnet-sp-checkout-state").val());var R=x(D+" select#s2member-pro-authnet-sp-checkout-country").val();var P=x.trim(x(D+" input#s2member-pro-authnet-sp-checkout-zip").val());var O=Q+"|"+R+"|"+P;if(Q&&R&&P&&O&&(!c||c!==O)&&(c=O)){(z)?z.abort():null,clearTimeout(f),f=null;w.html('<div><img src="<?php echo $vars["i"]; ?>/ajax-loader.gif" alt="<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (esc_attr (_x ("Calculating Sales Tax...", "s2member-front", "s2member"))); ?>" /> <?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("calculating sales tax...", "s2member-front", "s2member")); ?></div>');f=setTimeout(function(){z=x.post('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (admin_url ("/admin-ajax.php")); ?>',{action:"ws_plugin__optimizemember_pro_authnet_ajax_tax",ws_plugin__optimizemember_pro_authnet_ajax_tax:'<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_encryption::encrypt ("ws-plugin--optimizemember-pro-authnet-ajax-tax")); ?>',"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[attr]":M,"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[state]":Q,"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[country]":R,"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[zip]":P},function(S){clearTimeout(f),f=null;try{w.html("<div>"+x.sprintf('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("<strong>Sales Tax%s:</strong> %s<br /><strong>— Total%s:</strong> %s", "s2member-front", "s2member")); ?>',((S.trial)?' <?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Today", "s2member-front", "s2member")); ?>':""),((S.tax_per)?"<em>"+S.tax_per+"</em> ( "+S.cur_symbol+""+S.tax+" )":S.cur_symbol+""+S.tax),((S.trial)?' <?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Today", "s2member-front", "s2member")); ?>':""),S.cur_symbol+""+S.total)+"</div>")}catch(T){}},"json")},((N&&N.keyCode)?1000:100))}else{if(!Q||!R||!P||!O){w.html(""),c=null}}}})();m=function(M){setTimeout(function(){E(M)},10)};x(D+" input#s2member-pro-authnet-sp-checkout-state").bind("keyup blur",E).bind("cut paste",m);x(D+" input#s2member-pro-authnet-sp-checkout-zip").bind("keyup blur",E).bind("cut paste",m);x(D+" select#s2member-pro-authnet-sp-checkout-country").bind("change",E);setInterval(function(){E({interval:true})},1000);(A=function(M){if(OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN){x(I+" input#s2member-pro-authnet-sp-checkout-first-name").each(function(){var N=x(this),O=N.val();(!O)?N.val(OPTIMIZEMEMBER_CURRENT_USER_FIRST_NAME):null});x(I+" input#s2member-pro-authnet-sp-checkout-last-name").each(function(){var N=x(this),O=N.val();(!O)?N.val(OPTIMIZEMEMBER_CURRENT_USER_LAST_NAME):null});x(I+" input#s2member-pro-authnet-sp-checkout-email").each(function(){var N=x(this),O=N.val();(!O)?N.val(OPTIMIZEMEMBER_CURRENT_USER_EMAIL):null})}})();(r=function(M){var N=x(j+":checked").val();if(x.inArray(N,["Visa","MasterCard","Amex","Discover"])!==-1){x(s+" > div.s2member-pro-authnet-sp-checkout-form-div").show();x(s+" > div.s2member-pro-authnet-sp-checkout-form-div :input").attr(u);x(s+" > div#s2member-pro-authnet-sp-checkout-form-card-start-date-issue-number-div").hide();x(s+" > div#s2member-pro-authnet-sp-checkout-form-card-start-date-issue-number-div :input").attr(b);x(D+" > div.s2member-pro-authnet-sp-checkout-form-div").show();x(D+" > div.s2member-pro-authnet-sp-checkout-form-div :input").attr(u);(!q)?w.hide():null;x(D).show(),(M)?x(s+" input#s2member-pro-authnet-sp-checkout-card-number").focus():null}else{if(x.inArray(N,["Maestro","Solo"])!==-1){x(s+" > div.s2member-pro-authnet-sp-checkout-form-div").show();x(s+" > div.s2member-pro-authnet-sp-checkout-form-div :input").attr(u);x(D+" > div.s2member-pro-authnet-sp-checkout-form-div").show();x(D+" > div.s2member-pro-authnet-sp-checkout-form-div :input").attr(u);(!q)?w.hide():null;x(D).show(),(M)?x(s+" input#s2member-pro-authnet-sp-checkout-card-number").focus():null}else{if(!N){x(s+" > div.s2member-pro-authnet-sp-checkout-form-div").hide();x(s+" > div.s2member-pro-authnet-sp-checkout-form-div :input").attr(b);x(s+" > div#s2member-pro-authnet-sp-checkout-form-card-type-div").show();x(s+" > div#s2member-pro-authnet-sp-checkout-form-card-type-div :input").attr(u);x(D+" > div.s2member-pro-authnet-sp-checkout-form-div").hide();x(D+" > div.s2member-pro-authnet-sp-checkout-form-div :input").attr(b);(!q)?w.hide():null;x(D).hide(),(M)?x(l+" input#s2member-pro-authnet-sp-checkout-submit").focus():null}}}F()})();x(j).click(r).change(r);x(K).click(function(){x(i).val("apply-coupon"),k.submit()});k.submit(function(){if(x(i).val()!=="apply-coupon"){var P=this,N="",M="",Q="";var O=x(J+" input#recaptcha_response_field");if(!x(j+":checked").val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Please choose a Billing Method.", "s2member-front", "s2member")); ?>');return false}x(":input",P).each(function(){var R=x.trim(x(this).attr("id")).replace(/-[0-9]+$/g,"");if(R&&(N=x.trim(x('label[for="'+R+'"]',P).first().children("span").first().text().replace(/[\r\n\t]+/g," ")))){if(M=ws_plugin__optimizemember_validationErrors(N,this,P)){Q+=M+"\n\n"}}});if(Q=x.trim(Q)){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n'+Q);return false}else{if(O.length&&!O.val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');return false}}}x(d).attr(y),ws_plugin__optimizemember_animateProcessing(x(d)),x(K).attr(y);return true})}else{if((n=x("form#s2member-pro-authnet-checkout-form")).length===1){var B,F,q=true,E,m,f,z,c,p,r,A,C="div#s2member-pro-authnet-checkout-form-coupon-section",K=C+" input#s2member-pro-authnet-checkout-coupon-apply",I="div#s2member-pro-authnet-checkout-form-registration-section",L="div#s2member-pro-authnet-checkout-form-custom-fields-section",s="div#s2member-pro-authnet-checkout-form-billing-method-section",j=s+' input[name="optimizemember_pro_authnet_checkout[card_type]"]',D="div#s2member-pro-authnet-checkout-form-billing-address-section",w=x(D+" > div#s2member-pro-authnet-checkout-form-ajax-tax-div"),J="div#s2member-pro-authnet-checkout-form-captcha-section",l="div#s2member-pro-authnet-checkout-form-submission-section",i=l+" input#s2member-pro-authnet-checkout-nonce",d=l+" input#s2member-pro-authnet-checkout-submit";ws_plugin__optimizemember_animateProcessing(x(d),"reset"),x(d).removeAttr("disabled"),x(K).removeAttr("disabled");(B=function(M){if(x(l+" input#s2member-pro-authnet-checkout-coupons-not-required-or-not-possible").length){x(C).hide()}else{x(C).show()}})();(F=function(M){if(x(l+" input#s2member-pro-authnet-checkout-tax-not-required-or-not-possible").length){w.hide(),q=false}})();(E=function(N){if(q&&!(N&&N.interval&&document.activeElement.id==="s2member-pro-authnet-checkout-country")){var M=x(l+" input#s2member-pro-authnet-checkout-attr").val();var Q=x.trim(x(D+" input#s2member-pro-authnet-checkout-state").val());var R=x(D+" select#s2member-pro-authnet-checkout-country").val();var P=x.trim(x(D+" input#s2member-pro-authnet-checkout-zip").val());var O=Q+"|"+R+"|"+P;if(Q&&R&&P&&O&&(!c||c!==O)&&(c=O)){(z)?z.abort():null,clearTimeout(f),f=null;w.html('<div><img src="<?php echo $vars["i"]; ?>/ajax-loader.gif" alt="<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (esc_attr (_x ("Calculating Sales Tax...", "s2member-front", "s2member"))); ?>" /> <?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("calculating sales tax...", "s2member-front", "s2member")); ?></div>');f=setTimeout(function(){z=x.post('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (admin_url ("/admin-ajax.php")); ?>',{action:"ws_plugin__optimizemember_pro_authnet_ajax_tax",ws_plugin__optimizemember_pro_authnet_ajax_tax:'<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_encryption::encrypt ("ws-plugin--optimizemember-pro-authnet-ajax-tax")); ?>',"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[attr]":M,"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[state]":Q,"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[country]":R,"ws_plugin__optimizemember_pro_authnet_ajax_tax_vars[zip]":P},function(S,U){clearTimeout(f),f=null;try{w.html("<div>"+x.sprintf('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("<strong>Sales Tax%s:</strong> %s<br /><strong>— Total%s:</strong> %s", "s2member-front", "s2member")); ?>',((S.trial)?' <?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Today", "s2member-front", "s2member")); ?>':""),((S.tax_per)?"<em>"+S.tax_per+"</em> ( "+S.cur_symbol+""+S.tax+" )":S.cur_symbol+""+S.tax),((S.trial)?' <?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Today", "s2member-front", "s2member")); ?>':""),S.cur_symbol+""+S.total)+"</div>")}catch(T){}},"json")},((N&&N.keyCode)?1000:100))}else{if(!Q||!R||!P||!O){w.html(""),c=null}}}})();m=function(M){setTimeout(function(){E(M)},10)};x(D+" input#s2member-pro-authnet-checkout-state").bind("keyup blur",E).bind("cut paste",m);x(D+" input#s2member-pro-authnet-checkout-zip").bind("keyup blur",E).bind("cut paste",m);x(D+" select#s2member-pro-authnet-checkout-country").bind("change",E);setInterval(function(){E({interval:true})},1000);(p=function(M){if(x(l+" input#s2member-pro-authnet-checkout-password-not-required-or-not-possible").length){x(I+" > div#s2member-pro-authnet-checkout-form-password-div").hide();x(I+" > div#s2member-pro-authnet-checkout-form-password-div :input").attr(a)}})();(A=function(M){if(OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN){x(I+" input#s2member-pro-authnet-checkout-first-name").each(function(){var N=x(this),O=N.val();(!O)?N.val(OPTIMIZEMEMBER_CURRENT_USER_FIRST_NAME):null});x(I+" input#s2member-pro-authnet-checkout-last-name").each(function(){var N=x(this),O=N.val();(!O)?N.val(OPTIMIZEMEMBER_CURRENT_USER_LAST_NAME):null});x(I+" input#s2member-pro-authnet-checkout-email").val(OPTIMIZEMEMBER_CURRENT_USER_EMAIL).attr(a);x(I+" input#s2member-pro-authnet-checkout-username").val(OPTIMIZEMEMBER_CURRENT_USER_LOGIN).attr(a);x(I+" > div#s2member-pro-authnet-checkout-form-password-div").hide();x(I+" > div#s2member-pro-authnet-checkout-form-password-div :input").attr(a);if(x.trim(x(I+" > div#s2member-pro-authnet-checkout-form-registration-section-title").html())==='<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Create Profile", "s2member-front", "s2member")); ?>'){x(I+" > div#s2member-pro-authnet-checkout-form-registration-section-title").html('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Your Profile", "s2member-front", "s2member")); ?>')}x(L).hide(),x(L+" :input").attr(a)}})();(r=function(M){var N=x(j+":checked").val();if(x.inArray(N,["Visa","MasterCard","Amex","Discover"])!==-1){x(s+" > div.s2member-pro-authnet-checkout-form-div").show();x(s+" > div.s2member-pro-authnet-checkout-form-div :input").attr(u);x(s+" > div#s2member-pro-authnet-checkout-form-card-start-date-issue-number-div").hide();x(s+" > div#s2member-pro-authnet-checkout-form-card-start-date-issue-number-div :input").attr(b);x(D+" > div.s2member-pro-authnet-checkout-form-div").show();x(D+" > div.s2member-pro-authnet-checkout-form-div :input").attr(u);(!q)?w.hide():null;x(D).show(),(M)?x(s+" input#s2member-pro-authnet-checkout-card-number").focus():null}else{if(x.inArray(N,["Maestro","Solo"])!==-1){x(s+" > div.s2member-pro-authnet-checkout-form-div").show();x(s+" > div.s2member-pro-authnet-checkout-form-div :input").attr(u);x(D+" > div.s2member-pro-authnet-checkout-form-div").show();x(D+" > div.s2member-pro-authnet-checkout-form-div :input").attr(u);(!q)?w.hide():null;x(D).show(),(M)?x(s+" input#s2member-pro-authnet-checkout-card-number").focus():null}else{if(!N){x(s+" > div.s2member-pro-authnet-checkout-form-div").hide();x(s+" > div.s2member-pro-authnet-checkout-form-div :input").attr(b);x(s+" > div#s2member-pro-authnet-checkout-form-card-type-div").show();x(s+" > div#s2member-pro-authnet-checkout-form-card-type-div :input").attr(u);x(D+" > div.s2member-pro-authnet-checkout-form-div").hide();x(D+" > div.s2member-pro-authnet-checkout-form-div :input").attr(b);(!q)?w.hide():null;x(D).hide(),(M)?x(l+" input#s2member-pro-authnet-checkout-submit").focus():null}}}})();x(j).click(r).change(r);x(K).click(function(){x(i).val("apply-coupon"),n.submit()});x(I+" > div#s2member-pro-authnet-checkout-form-password-div :input").keyup(function(){ws_plugin__optimizemember_passwordStrength(x(I+" input#s2member-pro-authnet-checkout-username"),x(I+" input#s2member-pro-authnet-checkout-password1"),x(I+" input#s2member-pro-authnet-checkout-password2"),x(I+" div#s2member-pro-authnet-checkout-form-password-strength"))});n.submit(function(){if(x(i).val()!=="apply-coupon"){var P=this,N="",M="",S="";var O=x(J+" input#recaptcha_response_field");var R=x(I+' input#s2member-pro-authnet-checkout-password1[aria-required="true"]');var Q=x(I+" input#s2member-pro-authnet-checkout-password2");if(!x(j+":checked").val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Please choose a Billing Method.", "s2member-front", "s2member")); ?>');return false}x(":input",P).each(function(){var T=x.trim(x(this).attr("id")).replace(/-[0-9]+$/g,"");if(T&&(N=x.trim(x('label[for="'+T+'"]',P).first().children("span").first().text().replace(/[\r\n\t]+/g," ")))){if(M=ws_plugin__optimizemember_validationErrors(N,this,P)){S+=M+"\n\n"}}});if(S=x.trim(S)){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n'+S);return false}else{if(R.length&&x.trim(R.val())!==x.trim(Q.val())){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Passwords do not match up. Please try again.", "s2member-front", "s2member")); ?>');return false}else{if(R.length&&x.trim(R.val()).length<6){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Password MUST be at least 6 characters. Please try again.", "s2member-front", "s2member")); ?>');return false}else{if(O.length&&!O.val()){alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("— Oops, you missed something: —", "s2member-front", "s2member")); ?>\n\n<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq (_x ("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');return false}}}}}x(d).attr(y),ws_plugin__optimizemember_animateProcessing(x(d)),x(K).attr(y);return true})}}}}}(G=function(){x("div#s2member-pro-authnet-form-response").each(function(){var M=x(this).offset();window.scrollTo(0,M.top-100)})})()});