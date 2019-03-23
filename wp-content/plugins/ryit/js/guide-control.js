jQuery('document').ready(function($j) {
    $j('#setup-guide .toggle').on('click', function() {

        // */* optimized code, this makes for a smoother animation as well
        $j(this).parents('#setup-guide').toggleClass('maximized minimized');
        var mode = $j(this).parents('#setup-guide').className;

        var data = {
            action: 'update_setup_state',
            mode: mode,
            user_id: $j('body').attr('user_id')
        };

        $j.ajax({
            url: ajaxurl,
            type: 'GET', // the kind of data we are sending
            data: data,        
            dataType: 'json',
            success: function(response) {
// */* added console error
            }, error: function(response) {
                console.log("Error occured: " + response);
            }
        });
    });
});