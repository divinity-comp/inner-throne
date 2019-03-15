jQuery(document).on('ready', function () {

    jQuery('#apiKey').on('submit', function (e) {
        e.preventDefault();
        var reconnect_value = jQuery('.reconnect').val();
        jQuery.post(
            ajaxurl,
            {
                'action': 'add_api_key',
                'data': {
                    'key': jQuery('.api_key').val()
                },
                'reconnect': reconnect_value
            },
            function (response) {
                // var is_zero = false;
                // try {
                //     if (response.substr(-1) == 0) {
                //         console.debug('it is 0');
                //         is_zero = true;
                //     }
                // }
                // catch (m) {
                //
                // }
                //
                // if (is_zero) {
                // response = JSON.parse(response.substr(0, response.length - 1));
                response = JSON.parse(response);
                if (response.status) {
                    alert(response.message);
                    window.location.reload();
                }
                else {
                    alert(response.message);
                }
                // }

            }
        );
    });


});
