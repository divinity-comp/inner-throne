(function( $ ) {
	'use strict';
  var handler;

  jQuery(document).ready(function() {
    if( typeof StripeCheckout != 'undefined' ) { // hackish and needs a better fix.
      handler = StripeCheckout.configure({
        //key: 'pk_test_QvLB9XvZRvpFNyYBGurOqifH', TEST
        key: 'pk_live_uTqM4oCx5N9sOkg5WZZWUpFW',
        // image: '/img/documentation/checkout/marketplace.png',
        token: innerthrone_got_token
      });
    }

    jQuery('#innerthrone_payment_submit').click(function(event) {
      event.preventDefault();
      event.stopPropagation();

      var plan = jQuery('input[name=plan]:checked').val();
      if( !plan ) {
        return alert('select a plan');
      }

      handler.open({
        name: 'InnerThrone',
        description: 'Inner Throne 3 month training',
        amount: plans[plan].amount * 100,
        email: jQuery('#innerthrone_email').val()
      });
    });

    function innerthrone_got_token(token) {
      // Use the token to create the charge with a server-side script.
      // You can access the token ID with `token.id`
      jQuery('#token').val(token.id);
      jQuery('#token-string').val(JSON.stringify(token));

      var formData = new FormData();
      formData.append('action', 'innerthrone_payment');
      formData.append('token', token.id);
      formData.append('token-string', JSON.stringify(token));
      formData.append('name', jQuery('#innerthrone_name').val());
      formData.append('email', jQuery('#innerthrone_email').val());
      formData.append('plan', jQuery('input[name=plan]:checked').val());

      jQuery('#innerthrone_payment_submit').hide();

      jQuery.ajax({
        type: 'POST',
        processData: false,
        url: window.innerthrone_ajaxurl,
        success: function(response) {
          console.log(response);
          window.location.href = "http://www.inner-throne.com/payment-welcome";
          //alert('Payment Complete');
        },
        dataType: 'json',
        data: formData,
        contentType: false
      });
    }

    jQuery(window).on('popstate', function() {
      handler.close();
    });
  });


	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

})( jQuery );
