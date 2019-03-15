(function($){
    
    /**
     * Visability Options.
     */
    window.termsOptions = {
        
        init: function() {
            this.initOptions();
        },
        
        initOptions: function() {
            var self = this;
            
            $("#opanda_agreement_checkbox").change(function(){
                console.log('fff');
                if ( $(this).is(":checked") ) {
                    $("#onp-sl-agreement_checkbox-options").hide().removeClass('hide');
                    $("#onp-sl-agreement_checkbox-options").fadeIn();
                } else {
                    $("#onp-sl-agreement_checkbox-options").hide();
                }
            });
        }
    };

    $(function(){
        window.termsOptions.init();
    });
    
})(jQuery);