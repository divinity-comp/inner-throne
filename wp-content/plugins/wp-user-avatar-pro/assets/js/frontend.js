jQuery(function($) {
    // Add enctype to form with JavaScript as backup
    $('#your-profile').attr('enctype', 'multipart/form-data');
    // Add enctype to registration form with JavaScript
    $('#registerform').attr('enctype', 'multipart/form-data');
    // Add enctype to registration form with JavaScript in multisite
    $('#setupform').attr('enctype', 'multipart/form-data');
});
