jQuery(document).ready (function($) {
    $("a[data-recur='click-recur']").on("click", function(event) {
        var elId = ($(this).attr('id'));

        var recurCheckbox = $(this).parent().next().find('.recurring');

        if (recurCheckbox.is(':checked')) {
            $(this).attr("href", function(i, href) {
                return href + '&cbrblaccpt=true';
            });
        } else {
            event.preventDefault();
            alert("Please agree to the payment terms of this recurring product.");
        }
    });
});