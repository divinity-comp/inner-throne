jQuery(document).ready( // DOM ready.
    function($) // Depends on Stripe lib.
    {
        var stripeCheck = function()
        {
            if(window.StripeCheckout) // Stripe available?
                clearInterval(stripeCheckInterval), setupProForms();
        }, stripeCheckInterval = setInterval(stripeCheck, 100);

        $.ajax({cache: true, dataType: 'script', url: 'https://checkout.stripe.com/checkout.js'});

        var setupProForms = function()
        {
            /*
             Initializations.
             */
            var preloadAjaxLoader, // Loading image.
                $clForm, $upForm, $rgForm, $spForm, $coForm,
                ariaTrue = {'aria-required': 'true'}, ariaFalse = {'aria-required': 'false'},
                ariaFalseDis = {'aria-required': 'false', 'disabled': 'disabled'},
                disabled = {'disabled': 'disabled'},
                ariaFalseReadonly = {'aria-required': 'false', 'readonly': 'readonly'},

                taxMayApply = true, calculateTax, cTaxDelay, cTaxTimeout, cTaxReq, cTaxLocation, ajaxTaxDiv,
                optionsSection, optionsSelect, descSection, couponSection, couponApplyButton, registrationSection, customFieldsSection,
                billingMethodSection, handleBillingMethod, sourceTokenButton, sourceTokenSummary, sourceTokenInput, sourceTokenSummaryInput, billingAddressSection, captchaSection,
                submissionSection, submissionButton, submissionNonceVerification;

            preloadAjaxLoader = new Image(), preloadAjaxLoader.src = '<?php echo $vars["i"]; ?>/ajax-loader.gif';

            /*
             Check for more than a single form on this page.
             */
            if($('form.optimizemember-pro-stripe-cancellation-form').length > 1
                || $('form.optimizemember-pro-stripe-registration-form').length > 1 || $('form.optimizemember-pro-stripe-update-form').length > 1
                || $('form.optimizemember-pro-stripe-sp-checkout-form').length > 1 || $('form.optimizemember-pro-stripe-checkout-form').length > 1)
            {
                return alert('Detected more than one OptimizeMember Pro Form.\n\nPlease use only ONE OptimizeMember Pro Form Shortcode on each Post/Page.' +
                    ' Attempting to serve more than one Pro Form on each Post/Page (even w/ DHTML) may result in unexpected/broken functionality.');
            }
            /*
             Cancellation form handler.
             */
            if(($clForm = $('form#optimizemember-pro-stripe-cancellation-form')).length === 1)
            {
                captchaSection = 'div#optimizemember-pro-stripe-cancellation-form-captcha-section',
                    submissionSection = 'div#optimizemember-pro-stripe-cancellation-form-submission-section',
                    submissionButton = submissionSection + ' button#optimizemember-pro-stripe-cancellation-submit';

                $(submissionButton).removeAttr('disabled'),
                    ws_plugin__optimizemember_animateProcessing($(submissionButton), 'reset');

                $clForm.on('submit', function(/* Form validation. */)
                {
                    var context = this, label = '', error = '', errors = '',
                        $recaptchaResponse = $(captchaSection + ' input#recaptcha_response_field');

                    $(':input', context)
                        .each(function(/* Go through them all together. */)
                        {
                            var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);

                            if(id && (label = $.trim($('label[for="' + id + '"]', context).first().children('span').first().text().replace(/[\r\n\t]+/g, ' '))))
                            {
                                if(error = ws_plugin__optimizemember_validationErrors(label, this, context))
                                    errors += error + '\n\n'/* Collect errors. */;
                            }
                        });
                    if((errors = $.trim(errors)))
                    {
                        alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
                        return false;
                    }
                    else if($recaptchaResponse.length && !$recaptchaResponse.val())
                    {
                        alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');
                        return false;
                    }

                    $(submissionButton).attr(disabled),
                        ws_plugin__optimizemember_animateProcessing($(submissionButton));
                    return true;
                });
            }
            /*
             Registration form handler.
             */
            else if(($rgForm = $('form#optimizemember-pro-stripe-registration-form')).length === 1)
            {
                optionsSection = 'div#optimizemember-pro-stripe-registration-form-options-section',
                    optionsSelect = optionsSection + ' select#optimizemember-pro-stripe-registration-options',

                    descSection = 'div#optimizemember-pro-stripe-registration-form-description-section',

                    registrationSection = 'div#optimizemember-pro-stripe-registration-form-registration-section',
                    captchaSection = 'div#optimizemember-pro-stripe-registration-form-captcha-section',
                    submissionSection = 'div#optimizemember-pro-stripe-registration-form-submission-section',
                    submissionButton = submissionSection + ' button#optimizemember-pro-stripe-registration-submit',
                    submissionNonceVerification = submissionSection + ' input#optimizemember-pro-stripe-registration-nonce';

                $(submissionButton).removeAttr('disabled'),
                    ws_plugin__optimizemember_animateProcessing($(submissionButton), 'reset');

                if(!$(optionsSelect + ' option').length)
                    $(optionsSection).hide(), $(descSection).show();

                else $(optionsSection).show(), $(descSection).hide(),
                    $(optionsSelect).on('change', function(/* Handle checkout option changes. */)
                    {
                        $(submissionNonceVerification).val('option'),
                            $rgForm.attr('action', $rgForm.attr('action').replace(/#.*$/, '') + '#s2p-option'),
                            $rgForm.submit(); // Submit form with a new checkout option.
                    });
                if($(submissionSection + ' input#optimizemember-pro-stripe-registration-names-not-required-or-not-possible').length)
                {
                    $(registrationSection + ' > div#optimizemember-pro-stripe-registration-form-first-name-div').hide(),
                        $(registrationSection + ' > div#optimizemember-pro-stripe-registration-form-first-name-div :input').attr(ariaFalseReadonly);

                    $(registrationSection + ' > div#optimizemember-pro-stripe-registration-form-last-name-div').hide(),
                        $(registrationSection + ' > div#optimizemember-pro-stripe-registration-form-last-name-div :input').attr(ariaFalseReadonly);
                }
                if($(submissionSection + ' input#optimizemember-pro-stripe-registration-password-not-required-or-not-possible').length)
                {
                    $(registrationSection + ' > div#optimizemember-pro-stripe-registration-form-password-div').hide(),
                        $(registrationSection + ' > div#optimizemember-pro-stripe-registration-form-password-div :input').attr(ariaFalseReadonly);
                }
                $(registrationSection + ' > div#optimizemember-pro-stripe-registration-form-password-div :input').on('keyup', function()
                {
                    ws_plugin__optimizemember_passwordStrength(
                        $(registrationSection + ' input#optimizemember-pro-stripe-registration-username'),
                        $(registrationSection + ' input#optimizemember-pro-stripe-registration-password1'),
                        $(registrationSection + ' input#optimizemember-pro-stripe-registration-password2'),
                        $(registrationSection + ' div#optimizemember-pro-stripe-registration-form-password-strength')
                    );
                });
                $rgForm.on('submit', function(/* Form validation. */)
                {
                    if($.inArray($(submissionNonceVerification).val(), ['option']) === -1)
                    {
                        var context = this, label = '', error = '', errors = '',
                            $recaptchaResponse = $(captchaSection + ' input#recaptcha_response_field'),
                            $password1 = $(registrationSection + ' input#optimizemember-pro-stripe-registration-password1[aria-required="true"]'),
                            $password2 = $(registrationSection + ' input#optimizemember-pro-stripe-registration-password2');

                        $(':input', context)
                            .each(function(/* Go through them all together. */)
                            {
                                var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);

                                if(id && (label = $.trim($('label[for="' + id + '"]', context).first().children('span').first().text().replace(/[\r\n\t]+/g, ' '))))
                                {
                                    if(error = ws_plugin__optimizemember_validationErrors(label, this, context))
                                        errors += error + '\n\n'/* Collect errors. */;
                                }
                            });
                        if((errors = $.trim(errors)))
                        {
                            alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
                            return false;
                        }
                        else if($password1.length && $.trim($password1.val()) !== $.trim($password2.val()))
                        {
                            alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Passwords do not match up. Please try again.", "s2member-front", "s2member")); ?>');
                            return false;
                        }
                        else if($password1.length && $.trim($password1.val()).length < 6/* Enforce minimum length requirement here. */)
                        {
                            alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Password MUST be at least 6 characters. Please try again.", "s2member-front", "s2member")); ?>');
                            return false;
                        }
                        else if($recaptchaResponse.length && !$recaptchaResponse.val())
                        {
                            alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');
                            return false;
                        }
                        //$(optionsSelect).attr(disabled);
                    }
                    $(submissionButton).attr(disabled),
                        ws_plugin__optimizemember_animateProcessing($(submissionButton));
                    return true;
                });
            }
            /*
             Update form handler.
             */
            else if(($upForm = $('form#optimizemember-pro-stripe-update-form')).length === 1)
            {
                billingMethodSection = 'div#optimizemember-pro-stripe-update-form-billing-method-section',
                    sourceTokenButton = billingMethodSection + ' button#optimizemember-pro-stripe-update-form-source-token-button',
                    sourceTokenSummary = billingMethodSection + ' div#optimizemember-pro-stripe-update-form-source-token-summary',

                    billingAddressSection = 'div#optimizemember-pro-stripe-update-form-billing-address-section',

                    captchaSection = 'div#optimizemember-pro-stripe-update-form-captcha-section',

                    submissionSection = 'div#optimizemember-pro-stripe-update-form-submission-section',
                    sourceTokenInput = submissionSection + ' input[name="' + ws_plugin__optimizemember_escjQAttr('optimizemember_pro_stripe_update[source_token]') + '"]',
                    sourceTokenSummaryInput = submissionSection + ' input[name="' + ws_plugin__optimizemember_escjQAttr('optimizemember_pro_stripe_update[source_token_summary]') + '"]',
                    submissionButton = submissionSection + ' button#optimizemember-pro-stripe-update-submit';

                $(submissionButton).removeAttr('disabled'),
                    ws_plugin__optimizemember_animateProcessing($(submissionButton), 'reset');

                handleBillingMethod = function(eventTrigger /* eventTrigger is passed by jQuery for DOM events. */)
                {
                    var cardToken = $(sourceTokenInput).val(/* Card token from Stripe. */);

                    if(cardToken/* They have now supplied a credit card? */)
                    {
                        $(billingMethodSection).show(), // Show billing method section.
                            $(billingMethodSection + ' > div.optimizemember-pro-stripe-update-form-div').show(),
                            $(billingMethodSection + ' > div.optimizemember-pro-stripe-update-form-div :input').attr(ariaTrue);

                        if(taxMayApply/* If tax may apply, we need to collect a tax location. */)
                        {
                            $(billingAddressSection).show(), // Show billing address section.
                                $(billingAddressSection + ' > div.optimizemember-pro-stripe-update-form-div').show(),
                                $(billingAddressSection + ' > div.optimizemember-pro-stripe-update-form-div :input').attr(ariaTrue);
                        }
                        else // There is no reason to collect tax information in this case.
                        {
                            $(billingAddressSection).hide(), // Hide billing address section.
                                $(billingAddressSection + ' > div.optimizemember-pro-stripe-update-form-div').hide(),
                                $(billingAddressSection + ' > div.optimizemember-pro-stripe-update-form-div :input').attr(ariaFalse);
                        }
                        if(eventTrigger) $(submissionSection + ' button#optimizemember-pro-stripe-update-submit').focus();
                    }
                    else if(!cardToken/* Else there is no Billing Method supplied. */)
                    {
                        $(billingMethodSection).show(), // Show billing method section.
                            $(billingMethodSection + ' > div.optimizemember-pro-stripe-update-form-div').show(),
                            $(billingMethodSection + ' > div.optimizemember-pro-stripe-update-form-div :input').attr(ariaTrue);

                        $(billingAddressSection).hide(), // Hide billing address section.
                            $(billingAddressSection + ' > div.optimizemember-pro-stripe-update-form-div').hide(),
                            $(billingAddressSection + ' > div.optimizemember-pro-stripe-update-form-div :input').attr(ariaFalse);
                    }
                };
                handleBillingMethod(); // Handle billing method immediately to deal with fields already filled in.

                $(sourceTokenButton).on('click', function() // Stripe integration.
                {
                    var validateZipCode = $(submissionSection + ' input#optimizemember-pro-stripe-update-should-validate-zipcode').val() == '1',
                        collectBillingAddress = $(submissionSection + ' input#optimizemember-pro-stripe-update-should-collect-billing-address').val() == '1',
                        collectShippingAddress = $(submissionSection + ' input#optimizemember-pro-stripe-update-should-collect-shipping-address').val() == '1',
                        stripeImage = '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_stripe_api_image"]); ?>';

                    var getCardToken = StripeCheckout.configure
                    ({
                        image          : stripeImage ? stripeImage : undefined,
                        key            : '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_stripe_api_publishable_key"]); ?>',
                        zipCode        : validateZipCode, billingAddress : collectBillingAddress, shippingAddress: collectShippingAddress,
                        panelLabel     : '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Add", "s2member-front", "s2member")); ?>',
                        email          : typeof OPTIMIZEMEMBER_CURRENT_USER_EMAIL === 'string' ? OPTIMIZEMEMBER_CURRENT_USER_EMAIL : '',
                        allowRememberMe: true, // Allow Stripe to remember the customer.
                        token          : function(token)
                        {
                            $(sourceTokenInput).val(token.id), $(sourceTokenSummaryInput).val(buildCardTokenTextSummary(token)),
                                $(sourceTokenSummary).html(ws_plugin__optimizemember_escHtml(buildCardTokenTextSummary(token))),
                                handleBillingMethod(); // Adjust billing methods fields now also.
                        }
                    });
                    getCardToken.open(); // Open Stripe overlay.
                });
                $upForm.on('submit', function(/* Form validation. */)
                {
                    var context = this, label = '', error = '', errors = '',
                        $recaptchaResponse = $(captchaSection + ' input#recaptcha_response_field');

                    if(!$(sourceTokenInput).val())
                    {
                        alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("No Billing Method; please try again.", "s2member-front", "s2member")); ?>');
                        return false;
                    }
                    $(':input', context)
                        .each(function(/* Go through them all together. */)
                        {
                            var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);

                            if(id && (label = $.trim($('label[for="' + id.replace(/-(month|year)/, '') + '"]', context).first().children('span').first().text().replace(/[\r\n\t]+/g, ' '))))
                            {
                                if(error = ws_plugin__optimizemember_validationErrors(label, this, context))
                                    errors += error + '\n\n'/* Collect errors. */;
                            }
                        });
                    if((errors = $.trim(errors)))
                    {
                        alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
                        return false;
                    }
                    else if($recaptchaResponse.length && !$recaptchaResponse.val())
                    {
                        alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');
                        return false;
                    }
                    $(submissionButton).attr(disabled),
                        ws_plugin__optimizemember_animateProcessing($(submissionButton));
                    return true;
                });
            }
            /*
             Handles both types of checkout forms.
             */
            else if(($coForm = $('form#optimizemember-pro-stripe-sp-checkout-form')).length === 1 || ($coForm = $('form#optimizemember-pro-stripe-checkout-form')).length === 1)
            {
                (function($coForm)// Handles both types of checkout forms; i.e. Specific Post/Page and also Checkout/Modification forms.
                {
                    var coTypeWithDashes = $coForm[0].id.replace(/^optimizemember\-pro\-stripe\-/, '').replace(/\-form$/, ''),
                        coTypeWithUnderscores = coTypeWithDashes.replace(/[^a-z0-9]/gi, '_'); // e.g. `sp_checkout`.

                    optionsSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-options-section',
                        optionsSelect = optionsSection + ' select#optimizemember-pro-stripe-' + coTypeWithDashes + '-options',

                        descSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-description-section',

                        couponSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-coupon-section',
                        couponApplyButton = couponSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-coupon-apply',

                        registrationSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-registration-section',
                        customFieldsSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-custom-fields-section',

                        billingMethodSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-billing-method-section',
                        sourceTokenButton = billingMethodSection + ' button#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-source-token-button',
                        sourceTokenSummary = billingMethodSection + ' div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-source-token-summary',

                        billingAddressSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-billing-address-section',
                        ajaxTaxDiv = billingAddressSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-ajax-tax-div',

                        captchaSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-captcha-section',

                        submissionSection = 'div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-submission-section',
                        sourceTokenInput = submissionSection + ' input[name="' + ws_plugin__optimizemember_escjQAttr('optimizemember_pro_stripe_' + coTypeWithUnderscores + '[source_token]') + '"]',
                        sourceTokenSummaryInput = submissionSection + ' input[name="' + ws_plugin__optimizemember_escjQAttr('optimizemember_pro_stripe_' + coTypeWithUnderscores + '[source_token_summary]') + '"]',
                        submissionNonceVerification = submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-nonce',
                        submissionButton = submissionSection + ' button#optimizemember-pro-stripe-' + coTypeWithDashes + '-submit';
                    /*
                     Reset button states; in case of a back button.
                     */
                    $(optionsSelect).removeAttr('disabled'), $(couponApplyButton).removeAttr('disabled'),
                        $(submissionButton).removeAttr('disabled'), ws_plugin__optimizemember_animateProcessing($(submissionButton), 'reset');
                    /*
                     Handle checkout options. Does this form have checkout options?
                     */
                    if(!$(optionsSelect + ' option').length)
                        $(optionsSection).hide(), $(descSection).show();

                    else $(optionsSection).show(), $(descSection).hide(),
                        $(optionsSelect).on('change', function(/* Handle checkout option changes. */)
                        {
                            $(submissionNonceVerification).val('option'),
                                $coForm.attr('action', $coForm.attr('action').replace(/#.*$/, '') + '#s2p-option'),
                                $coForm.submit(); // Submit form with a new checkout option.
                        });
                    /*
                     Handle the coupon code section. Enabled on this form?
                     */
                    if($(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-coupons-not-required-or-not-possible').length)
                        $(couponSection).hide(); // Not accepting coupons on this particular form.

                    else $(couponSection).show(), $(couponApplyButton).on('click', function(/* Submit coupon code upon clicking apply button. */)
                    {
                        $(submissionNonceVerification).val('apply-coupon'),
                            $coForm.attr('action', $coForm.attr('action').replace(/#.*$/, '') + '#s2p-option'),
                            $coForm.submit(); // Submit form with hash positioning.
                    });
                    /*
                     Handle a user that is already logged into their account.
                     */
                    if(OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN/* User is already logged in? */)
                    {
                        $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-first-name')
                            .each(function()
                            {
                                var $this = $(this), val = $this.val();
                                if(!val) $this.val(OPTIMIZEMEMBER_CURRENT_USER_FIRST_NAME);
                            });
                        $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-last-name')
                            .each(function()
                            {
                                var $this = $(this), val = $this.val();
                                if(!val) $this.val(OPTIMIZEMEMBER_CURRENT_USER_LAST_NAME);
                            });
                        $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-email').val(OPTIMIZEMEMBER_CURRENT_USER_EMAIL).attr(ariaFalseReadonly),
                            $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-username').val(OPTIMIZEMEMBER_CURRENT_USER_LOGIN).attr(ariaFalseReadonly);

                        $(registrationSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-password-div').hide(),
                            $(registrationSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-password-div :input').attr(ariaFalseReadonly);

                        if($.trim($(registrationSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-registration-section-title').html()) === '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Create Profile", "s2member-front", "s2member")); ?>')
                            $(registrationSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-registration-section-title').html('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Your Profile", "s2member-front", "s2member")); ?>');

                        $(customFieldsSection).hide(), $(customFieldsSection + ' :input').attr(ariaFalseReadonly);
                    }
                    /*
                     Handle the password input field in various scenarios.
                     */
                    if($(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-password-not-required-or-not-possible').length)
                    {
                        $(registrationSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-password-div').hide(),
                            $(registrationSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-password-div :input').attr(ariaFalseReadonly);
                    }
                    else $(registrationSection + ' > div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-password-div :input').on('keyup', function()
                    {
                        ws_plugin__optimizemember_passwordStrength(
                            $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-username'),
                            $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-password1'),
                            $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-password2'),
                            $(registrationSection + ' div#optimizemember-pro-stripe-' + coTypeWithDashes + '-form-password-strength')
                        );
                    });
                    /*
                     Handle tax calulations via tax-related input fields.
                     */
                    if($(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-tax-not-required-or-not-possible').length)
                        $(ajaxTaxDiv).hide(), taxMayApply = false; // Tax does NOT even apply.

                    else // We need to setup a few handlers.
                    {
                        cTaxDelay = function(eventTrigger)
                        {
                            setTimeout(function(){ calculateTax(eventTrigger); }, 10);
                        };
                        calculateTax = function(eventTrigger) // Calculates tax.
                        {
                            if(!taxMayApply) return; // Not applicable.

                            if(eventTrigger && eventTrigger.interval && document.activeElement
                                && document.activeElement.id === 'optimizemember-pro-stripe-' + coTypeWithDashes + '-country')
                                return; // Nothing to do in this special case.

                            var attr = $(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-attr').val(),
                                state = $.trim($(billingAddressSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-state').val()),
                                country = $.trim($(billingAddressSection + ' select#optimizemember-pro-stripe-' + coTypeWithDashes + '-country').val()),
                                zip = $.trim($(billingAddressSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-zip').val()),
                                thisTaxLocation = state + '|' + country + '|' + zip; // Three part location.

                            if(state && country && zip && thisTaxLocation && (!cTaxLocation || cTaxLocation !== thisTaxLocation))
                            {
                                clearTimeout(cTaxTimeout), cTaxTimeout = 0,
                                    cTaxLocation = thisTaxLocation; // Set current location.
                                if(cTaxReq) cTaxReq.abort(); // Abort any existing connections.

                                var verifier = '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(c_ws_plugin__optimizemember_utils_encryption::encrypt("ws-plugin--optimizemember-pro-stripe-ajax-tax")); ?>',
                                    calculating = '<div><img src="' + ws_plugin__optimizemember_escAttr(preloadAjaxLoader.src) + '" alt="" /> <?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("calculating sales tax...", "s2member-front", "s2member")); ?></div>',
                                    ajaxTaxHandler = function(/* Create a new cTaxTimeout with a one second delay. */)
                                    {
                                        cTaxReq = $.post('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(admin_url("/admin-ajax.php")); ?>',
                                            {
                                                'action'                                               : 'ws_plugin__optimizemember_pro_stripe_ajax_tax',
                                                'ws_plugin__optimizemember_pro_stripe_ajax_tax'              : verifier,
                                                'ws_plugin__optimizemember_pro_stripe_ajax_tax_vars[attr]'   : attr,
                                                'ws_plugin__optimizemember_pro_stripe_ajax_tax_vars[state]'  : state,
                                                'ws_plugin__optimizemember_pro_stripe_ajax_tax_vars[country]': country,
                                                'ws_plugin__optimizemember_pro_stripe_ajax_tax_vars[zip]'    : zip
                                            },
                                            function(response, textStatus)
                                            {
                                                clearTimeout(cTaxTimeout), cTaxTimeout = 0;
                                                if(typeof response === 'object' && response.hasOwnProperty('tax'))
                                                /* translators: `Sales Tax (Today)` and `Total (Today)`. The word `Today` is displayed when/if a trial period is offered. The word `Today` is translated elsewhere. */
                                                    $(ajaxTaxDiv).html('<div>' + $.sprintf('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("<strong>Sales Tax%s:</strong> %s<br /><strong>— Total%s:</strong> %s", "s2member-front", "s2member")); ?>', ((response.trial) ? ' ' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Today", "s2member-front", "s2member")); ?>' : ''), ((response.tax_per) ? '<em>' + response.tax_per + '</em> ( ' + response.cur_symbol + '' + response.tax + ' )' : response.cur_symbol + '' + response.tax), ((response.trial) ? ' ' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Today", "s2member-front", "s2member")); ?>' : ''), response.cur_symbol + '' + response.total) + '</div>');
                                            }, 'json');
                                    };
                                $(ajaxTaxDiv).html(calculating), cTaxTimeout = setTimeout(ajaxTaxHandler, ((eventTrigger && eventTrigger.keyCode) ? 1000 : 100));
                            }
                            else if(!state || !country || !zip || !thisTaxLocation)
                            {
                                clearTimeout(cTaxTimeout), cTaxTimeout = 0,
                                    cTaxLocation = ''; // Reset the current location.
                                if(cTaxReq) cTaxReq.abort(); // Abort any existing connections.
                                $(ajaxTaxDiv).html(''); // Empty the tax calculation div here also.
                            }
                        };
                        setInterval(function(){ calculateTax({interval: true}); }, 1000), // Helps with things like Google's Autofill feature.
                            $(billingAddressSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-state').on('keyup blur', calculateTax).on('cut paste', cTaxDelay),
                            $(billingAddressSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-zip').on('keyup blur', calculateTax).on('cut paste', cTaxDelay),
                            $(billingAddressSection + ' select#optimizemember-pro-stripe-' + coTypeWithDashes + '-country').on('change', calculateTax),
                            calculateTax(); // Calculate immediately to deal with fields already filled in.
                    }
                    handleBillingMethod = function(eventTrigger /* eventTrigger is passed by jQuery for DOM events. */)
                    {
                        if($(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-payment-not-required-or-not-possible').length)
                            $(sourceTokenInput).val('free'); // No payment required in this VERY special case.

                        var cardToken = $(sourceTokenInput).val(/* Card token from Stripe. */);

                        if(cardToken/* They have now supplied a credit card? */)
                        {
                            if(cardToken === 'free' /* Special card token value. */)
                            {
                                $(billingMethodSection).hide(), // Hide billing method section.
                                    $(billingMethodSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div').hide(),
                                    $(billingMethodSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div :input').attr(ariaFalse);
                            }
                            else // We need to display the billing method section in all other cases.
                            {
                                $(billingMethodSection).show(), // Show billing method section.
                                    $(billingMethodSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div').show(),
                                    $(billingMethodSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div :input').attr(ariaTrue);
                            }
                            if(cardToken !== 'free' && taxMayApply/* If tax may apply, we need to collect a tax location. */)
                            {
                                $(billingAddressSection).show(), // Show billing address section.
                                    $(billingAddressSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div').show(),
                                    $(billingAddressSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div :input').attr(ariaTrue);
                            }
                            else // There is no reason to collect tax information in this case.
                            {
                                $(billingAddressSection).hide(), // Hide billing address section.
                                    $(billingAddressSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div').hide(),
                                    $(billingAddressSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div :input').attr(ariaFalse);
                            }
                            if(eventTrigger) $(submissionSection + ' button#optimizemember-pro-stripe-' + coTypeWithDashes + '-submit').focus();
                        }
                        else if(!cardToken/* Else there is no Billing Method supplied. */)
                        {
                            $(billingMethodSection).show(), // Show billing method section.
                                $(billingMethodSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div').show(),
                                $(billingMethodSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div :input').attr(ariaTrue);

                            $(billingAddressSection).hide(), // Hide billing address section.
                                $(billingAddressSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div').hide(),
                                $(billingAddressSection + ' > div.optimizemember-pro-stripe-' + coTypeWithDashes + '-form-div :input').attr(ariaFalse);
                        }
                    };
                    handleBillingMethod(); // Handle billing method immediately to deal with fields already filled in.

                    $(sourceTokenButton).on('click', function() // Stripe integration.
                    {
                        var isBuyNow = $(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-is-buy-now-amount-in-cents').length > 0,
                            validateZipCode = $(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-should-validate-zipcode').val() == '1',
                            collectBillingAddress = $(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-should-collect-billing-address').val() == '1',
                            collectShippingAddress = $(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-should-collect-shipping-address').val() == '1',
                            acceptBitcoin = isBuyNow && $(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-is-buy-now-bitcoin-accepted').length > 0,
                            isBuyNowAmountInCents = isBuyNow ? parseInt($.trim($(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-is-buy-now-amount-in-cents').val())) : 0,
                            isBuyNowCurrency = isBuyNow ? $.trim($(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-is-buy-now-currency').val()).toUpperCase() : '',
                            isBuyNowDesc = isBuyNow ? $.trim($(submissionSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-is-buy-now-desc').val()) : '',
                            stripeImage = '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_stripe_api_image"]); ?>';

                        var getCardToken = StripeCheckout.configure
                        ({
                            image      : stripeImage ? stripeImage : undefined,
                            locale     : 'auto', // Based on visitor's country.

                            amount     : isBuyNow ? isBuyNowAmountInCents : undefined,
                            currency   : isBuyNow ? isBuyNowCurrency : undefined,
                            description: isBuyNow ? isBuyNowDesc : undefined,
                            bitcoin    : isBuyNow && acceptBitcoin,

                            key            : '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_stripe_api_publishable_key"]); ?>',
                            allowRememberMe: '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_stripe_api_allow_remember_me"]); ?>' == '1',
                            panelLabel     : '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Add", "s2member-front", "s2member")); ?>',
                            zipCode        : validateZipCode, billingAddress : collectBillingAddress, shippingAddress: collectShippingAddress,
                            email          : $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-email').val(),
                            token          : function(token)
                            {
                                $(sourceTokenInput).val(token.id), $(sourceTokenSummaryInput).val(buildCardTokenTextSummary(token)),
                                    $(sourceTokenSummary).html(ws_plugin__optimizemember_escHtml(buildCardTokenTextSummary(token))),
                                    handleBillingMethod(); // Adjust billing methods fields now also.
                            }
                        });
                        getCardToken.open(); // Open Stripe overlay.
                    });
                    $coForm.on('submit', function(/* Form validation. */)
                    {
                        if($.inArray($(submissionNonceVerification).val(), ['option', 'apply-coupon']) === -1)
                        {
                            var context = this, label = '', error = '', errors = '',
                                $recaptchaResponse = $(captchaSection + ' input#recaptcha_response_field'),
                                $password1 = $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-password1[aria-required="true"]'),
                                $password2 = $(registrationSection + ' input#optimizemember-pro-stripe-' + coTypeWithDashes + '-password2');

                            if(!$(sourceTokenInput).val())
                            {
                                alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("No Billing Method; please try again.", "s2member-front", "s2member")); ?>');
                                return false;
                            }
                            $(':input', context)
                                .each(function(/* Go through them all together. */)
                                {
                                    var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);
                                    if(id && (label = $.trim($('label[for="' + id.replace(/-(month|year)/, '') + '"]', context).first().children('span').first().text().replace(/[\r\n\t]+/g, ' '))))
                                    {
                                        if(error = ws_plugin__optimizemember_validationErrors(label, this, context))
                                            errors += error + '\n\n'/* Collect errors. */;
                                    }
                                });
                            if((errors = $.trim(errors)))
                            {
                                alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
                                return false;
                            }
                            else if($password1.length && $.trim($password1.val()) !== $.trim($password2.val()))
                            {
                                alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Passwords do not match up. Please try again.", "s2member-front", "s2member")); ?>');
                                return false;
                            }
                            else if($password1.length && $.trim($password1.val()).length < 6/* Enforce minimum length requirement here. */)
                            {
                                alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Password MUST be at least 6 characters. Please try again.", "s2member-front", "s2member")); ?>');
                                return false;
                            }
                            else if($recaptchaResponse.length && !$recaptchaResponse.val())
                            {
                                alert('<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__optimizemember_utils_strings::esc_js_sq(_x("Security Code missing. Please try again.", "s2member-front", "s2member")); ?>');
                                return false;
                            }
                            //$(optionsSelect).attr(disabled);
                        }
                        $(couponApplyButton).attr(disabled),
                            $(submissionButton).attr(disabled), ws_plugin__optimizemember_animateProcessing($(submissionButton));
                        return true;
                    });
                })($coForm);
            }
            var buildCardTokenTextSummary = function(token)
            {
                if(typeof token !== 'object') return '';

                if(token.type === 'bank_account' && token.bank_account)
                    return token.bank_account.bank_name + ': xxxx...' + token.bank_account.last4;

                if(token.type === 'card' && token.card)
                    return token.card.brand + ': xxxx-xxxx-xxxx-' + token.card.last4;

                return 'Token: ' + token.id;
            };
            /*
             Jump to responses.
             */
            $('div#optimizemember-pro-stripe-form-response')
                .each(function()
                {
                    scrollTo(0, $(this).offset().top - 100);
                });
        }
    });