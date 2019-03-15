;(function($) {

    var $formEditor = $('ul#edd-checkout-fields-editor');

    var Editor = {
        init: function() {

            // make it sortable
            this.makeSortable();

            this.tooltip();
            this.tabber();
            this.showHideHelp();

            // collapse all
            $('button.cfm-collapse').on('click', this.collpaseEditFields);

            // add field click
            $('.edd-checkout-fields-buttons').on('click', 'button', this.addNewField);

            // remove form field
            $('#edd-checkout-fields-editor').on('click', '.cfm-remove', this.removeFormField);

            // on change event: meta key
            $('#edd-checkout-fields-editor').on('change', 'li.custom-field input[data-type="label"]', this.setMetaKey);

            // on change event: checkbox|radio fields
            $('#edd-checkout-fields-editor').on('change', '.edd-checkout-fields-sub-fields input[type=text]', function() {
                $(this).prev('input[type=checkbox], input[type=radio]').val($(this).val());
            });

            // on change event: checkbox|radio fields
            $('#edd-checkout-fields-editor').on('click', 'input[type=checkbox].multicolumn', function() {
                // $(this).prev('input[type=checkbox], input[type=radio]').val($(this).val());
                var $self = $(this),
                    $parent = $self.closest('.edd-checkout-fields-rows');

                if ($self.is(':checked')) {
                    $parent.next().hide().next().hide();
                    $parent.siblings('.column-names').show();
                } else {
                    $parent.next().show().next().show();
                    $parent.siblings('.column-names').hide();
                }
            });

            // on change event: checkbox|radio fields
            $('#edd-checkout-fields-editor').on('click', 'input[type=checkbox].retype-pass', function() {
                // $(this).prev('input[type=checkbox], input[type=radio]').val($(this).val());
                var $self = $(this),
                    $parent = $self.closest('.edd-checkout-fields-rows');

                if ($self.is(':checked')) {
                    $parent.next().show().next().show();
                } else {
                    $parent.next().hide().next().hide();
                }
            });

            // toggle form field
            $('#edd-checkout-fields-editor').on('click', '.cfm-toggle', this.toggleFormField);

            // clone and remove repeated field
            $('#edd-checkout-fields-editor').on('click', 'img.cfm-clone-field', this.cloneField);
            $('#edd-checkout-fields-editor').on('click', 'img.cfm-remove-field', this.removeField);
        },

        showHideHelp: function() {
            var childs = $('ul#edd-checkout-fields-editor').children('li');

            if ( !childs.length) {
                $('.cfm-updated').show();
            } else {
                $('.cfm-updated').hide();
            }
        },

        makeSortable: function() {
            $formEditor = $('ul#edd-checkout-fields-editor');

            if ($formEditor) {
                $formEditor.sortable({
                    placeholder: "ui-state-highlight",
                    handle: '> .cfm-legend',
                    distance: 5
                });
            }
        },

        addNewField: function(e) {
            e.preventDefault();

            var $self = $(this),
                $formEditor = $('ul#edd-checkout-fields-editor'),
                name = $self.data('name'),
                type = $self.data('type'),
                data = {
                    name: name,
                    type: type,
                    order: $formEditor.find('li').length + 1,
                    action: 'edd-checkout-fields_add_el'
                };

            // console.log($self, data);

            // check if these are already inserted
            var oneInstance = ['edd_first', 'edd_last', 'edd_email'];

            if ($.inArray(name, oneInstance) >= 0) {
                if( $formEditor.find('li.' + name).length ) {
                    alert('You already have this field in the form');
                    return false;
                }
            }

            $('.cfm-loading').removeClass('hide');
            $.post(ajaxurl, data, function(res) {
                $formEditor.append(res);

                // re-call sortable
                Editor.makeSortable();

                // enable tooltip
                Editor.tooltip();

                $('.cfm-loading').addClass('hide');
                Editor.showHideHelp();
            });
        },

        removeFormField: function(e) {
            e.preventDefault();

            if (confirm('Are you sure?')) {

                $(this).closest('li').fadeOut(function() {
                    $(this).remove();

                    Editor.showHideHelp();
                });
            }
        },

        toggleFormField: function(e) {
            e.preventDefault();

            $(this).closest('li').find('.edd-checkout-fields-holder').slideToggle('fast');
        },

        cloneField: function(e) {
            e.preventDefault();

            var $div = $(this).closest('div');
            var $clone = $div.clone();
            // console.log($clone);

            //clear the inputs
            $clone.find('input').val('');
            $clone.find(':checked').attr('checked', '');
            $div.after($clone);
        },

        removeField: function() {
            //check if it's the only item
            var $parent = $(this).closest('div');
            var items = $parent.siblings().andSelf().length;

            if( items > 1 ) {
                $parent.remove();
            }
        },

        setMetaKey: function() {
            var $self = $(this),
                val = $self.val().toLowerCase().split(' ').join('_').split('\'').join(''),
                $metaKey = $(this).closest('.edd-checkout-fields-rows').next().find('input[type=text]');

            if ($metaKey.length) {
                $metaKey.val(val);
            }
        },

        tooltip: function() {
            $('.smallipopInput').smallipop({
                preferredPosition: 'right',
                theme: 'black',
                popupOffset: 0,
                triggerOnClick: true
            });
        },

        collpaseEditFields: function(e) {
            e.preventDefault();

            $('ul#edd-checkout-fields-editor').children('li').find('.edd-checkout-fields-holder').slideToggle();
        },

        tabber: function() {
            // Switches option sections
            $('.group').hide();
            $('.group:first').fadeIn();

            $('.group .collapsed').each(function(){
                $(this).find('input:checked').parent().parent().parent().nextAll().each(
                function(){
                    if ($(this).hasClass('last')) {
                        $(this).removeClass('hidden');
                        return false;
                    }
                    $(this).filter('.hidden').removeClass('hidden');
                });
            });

            $('.nav-tab-wrapper a:first').addClass('nav-tab-active');

            $('.nav-tab-wrapper a').click(function(evt) {
                $('.nav-tab-wrapper a').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active').blur();
                var clicked_group = $(this).attr('href');
                $('.group').hide();
                $(clicked_group).fadeIn();
                evt.preventDefault();
            });
        }
    };

    // on DOM ready
    $(function() {
        Editor.init();
    });

})(jQuery);