var  name_changeRight = function(e) {
    document.getElementById("name").value = e.value;
}
var  name_changeTop = function(e) {
    document.getElementById("huge_it_gallery_name").value = e.value;
};

jQuery(document).ready(function () {

    if(jQuery('#rating').val()=='off'){
        jQuery('.like_dislike_wrapper').css('display','none');
        jQuery('.heart_wrapper').css('display','none');
    }else if (jQuery('#rating').val()=='dislike'){
        jQuery('.like_dislike_wrapper').css('display','block');
        jQuery('.heart_wrapper').css('display','none');
        jQuery('.heart_wrapper').find('input').removeAttr('name');
    }else if (jQuery('#rating').val()=='heart'){
        jQuery('.heart_wrapper').css('display','block');
    }

    jQuery('#lightbox_type input').change(function () {
        jQuery('#lightbox_type input').parent().removeClass('active');
        jQuery(this).parent().addClass('active');
        if(jQuery(this).val() == 'old_type'){
            jQuery('#lightbox-options-list').addClass('active');
            jQuery('#new-lightbox-options-list').removeClass('active');
        }
        else{
            jQuery('#lightbox-options-list').removeClass('active');
            jQuery('#new-lightbox-options-list').addClass('active');
        }
        jQuery('#lightbox_type input').prop('checked',false);
        if(!jQuery(this).prop('checked')){
            jQuery(this).prop('checked',true);
        }
    });

    jQuery('#rating').on('change',function(){
        if(jQuery(this).val()=='off'){
            jQuery('.like_dislike_wrapper').css('display','none');
            jQuery('.heart_wrapper').css('display','none');
        }else if (jQuery(this).val()=='dislike'){
            jQuery('.like_dislike_wrapper').css('display','block');
            jQuery('.heart_wrapper').css('display','none');
            jQuery('.heart_wrapper').find('input').removeAttr('name');
        }else if (jQuery(this).val()=='heart'){
            jQuery('.heart_wrapper').css('display','block');
            jQuery('.like_dislike_wrapper').css('display','none');
            jQuery('.heart_wrapper').each(function(){
                var num=jQuery(this).find('input').attr('num');
                jQuery(this).find('input').attr('name','like_'+num);
            });
        }
    });
    jQuery('.close-christmas').on('click',function (e) {
        e.preventDefault();
        jQuery(".backend-christmas-banner").css("display","none");
        galleryImgSetCookie( 'galleryImgChristmasShow', 'no', {expires:345600} );
    });
    galleryImgPopupSizes(jQuery('#light_box_size_fix'));
    jQuery('#light_box_size_fix').change(function(){
        galleryImgPopupSizes(jQuery(this));
    });
    var setTimeoutConst;
    jQuery('ul#images-list > li > .image-container img').on('mouseenter',function () {
        var onHoverPreview = jQuery('#img_hover_preview').prop('checked');
        if(onHoverPreview == true) {
            var imgSrc = jQuery(this).attr('src');
            jQuery('#gallery-image-zoom img').attr('src', imgSrc);
            setTimeoutConst = setTimeout(function () {
                jQuery('#gallery-image-zoom').fadeIn('3000');
            }, 500);
        }
    });
    jQuery('ul#images-list > li > .image-container img').on('mouseout',function () {
        clearTimeout(setTimeoutConst);
        jQuery('#gallery-image-zoom').fadeOut('3000');
    });
    jQuery('.huge-it-editnewuploader .button-edit').click(function(e) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = jQuery(this);
        var id = button.attr('id').replace('_button', '');
        _custom_media = true;
        wp.media.editor.send.attachment = function(props, attachment){
            if ( _custom_media ) {
                jQuery("#"+id).val(attachment.url);
                jQuery("#save-buttom").click();
            } else {
                return _orig_send_attachment.apply( this, [props, attachment] );
            }
        };

        wp.media.editor.open(button);
        return false;
    });
    jQuery(".huge-it-editnewuploader").click();
    jQuery('.remove-image-container a').on('click',function () {
        var galleryId = jQuery(this).data('gallery-id');
        var imageId = jQuery(this).data('image-id');
        var removeNonce = jQuery(this).data('nonce-value');
        jQuery('#adminForm').attr('action', 'admin.php?page=galleries_huge_it_gallery&id='+galleryId+'&removeslide='+imageId+'&save_data_nonce='+removeNonce);
        galleryImgSubmitbutton('apply');
    });
    jQuery(".wp-media-buttons-icon").click(function() {
        jQuery(".attachment-filters").css("display","none");
    });
    var _custom_media = true,custom_uploader,
        _orig_send_attachment = wp.media.editor.send.attachment;


    jQuery('.huge-it-newuploader .button').click(function(e) {
        e.preventDefault();
        var button = jQuery(this);
        var id = button.attr('id').replace('_button', '');
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Insert Into Gallery',
            button: {
                text: 'Insert Into Gallery'
            },
            multiple: true
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachments = custom_uploader.state().get('selection').toJSON();
            for(var key in attachments){
                jQuery("#"+id).val(attachments[key].url+';;;'+jQuery("#"+id).val());
            }
            jQuery("#save-buttom").click();
        });
        custom_uploader.open();
    });

    jQuery('.add_media').on('click', function(){
        _custom_media = false;

    });
    jQuery(".wp-media-buttons-icon").click(function() {
        jQuery(".media-menu .media-menu-item").css("display","none");
        jQuery(".media-menu-item:first").css("display","block");
        jQuery(".separator").next().css("display","none");
        jQuery('.attachment-filters').val('image').trigger('change');
        jQuery(".attachment-filters").css("display","none");
    });
    if(jQuery('select[name="display_type"]').val() == 2){
        jQuery('#content_per_page_li').hide();
    }else{
        jQuery('#content_per_page_li').show();
    }
    jQuery('select[name="display_type"]').on('change' ,function(){
        if(jQuery(this).val() == 2){
            jQuery('#content_per_page_li').hide();
        }else{
            jQuery('#content_per_page_li').show();
        }
    });


    jQuery('#gallery-unique-options').on('change',function(){
        jQuery( 'div[id^="gallery-current-options"]').each(function(){
            if(jQuery('#gallery-current-options-1').hasClass('active')  || jQuery('#gallery-current-options-3').hasClass('active'))
                jQuery('li.for_slider').show();
            else
                jQuery('li.for_slider').hide();
        });
    });
    jQuery('#gallery-unique-options').change();

    jQuery('#gallery-unique-options').on('change',function(){
        jQuery( 'div[id^="gallery-current-options"]').each(function(){
            if(!jQuery(this).hasClass( "active" )){
                jQuery(this).find('ul li input[name="sl_pausetime"]').attr('name', '');
            }
        });
    });

    jQuery('#gallery-unique-options').on('change',function(){
        jQuery( 'div[id^="gallery-current-options"]').each(function(){
            if(!jQuery(this).hasClass( "active" )){
                jQuery(this).find('ul li input[name="sl_changespeed"]').attr('name', '');
            }
        });
    });

    jQuery( "#images-list > li input" ).on('keyup',function(){
        jQuery(this).parents("#images-list > li").addClass('submit-post');
    });
    jQuery( "#images-list > li textarea" ).on('keyup',function(){
        jQuery(this).parents("#images-list > li").addClass('submit-post');
    });
    jQuery( "#images-list > li input" ).on('change',function(){
        jQuery(this).parents("#images-list > li").addClass('submit-post');
    });
    jQuery('.editimageicon').click(function(){
        jQuery(this).parents("#images-list > li").addClass('submit-post');
    })
    /*** </posted only submit classes> ***/

    jQuery( "#images-list" ).sortable({
        start: function(event, ui) {
            ui.item.data('start_pos', ui.item.index());
        },
        stop: function(event, ui) {
            jQuery("#images-list > li").removeClass('has-background');
            count=jQuery("#images-list > li").length;
            for(var i=0;i<=count;i+=2){
                jQuery("#images-list > li").eq(i).addClass("has-background");
            }
            jQuery("#images-list > li").each(function(){
                jQuery(this).find('.order_by').val(jQuery(this).index());
            });
            var start = Math.min(ui.item.data('start_pos'),ui.item.index());
            var end = Math.max(ui.item.data('start_pos'),ui.item.index());
            for(var i1=start; i1<=end; i1++){
                jQuery(document.querySelectorAll("#images-list > li")[i1]).addClass('highlights');
            }
        },
        change: function(event, ui) {
            var start_pos = ui.item.data('start_pos');
            var index = ui.placeholder.index();
            if (start_pos < index + 2) {
                jQuery('#images-list > li:nth-child(' + index + ')').addClass('highlights');
            } else {
                jQuery('#images-list > li:eq(' + (index + 1) + ')').addClass('highlights');
            }
        },
        update: function(event, ui) {
            jQuery('#sortable li').removeClass('highlights');
        },
        revert: true
    });
    var strliID = jQuery(location).attr('hash');
    jQuery('#gallery-view-tabs li').removeClass('active');
    if (jQuery('#gallery-view-tabs li a[href="' + strliID + '"]').length > 0) {
        jQuery('#gallery-view-tabs li a[href="' + strliID + '"]').parent().addClass('active');
    } else {
        jQuery('a[href="#gallery-view-options-0"]').parent().addClass('active');
    }
    strliID = strliID.split('#').join('.');
    jQuery('#gallery-view-tabs-contents > li').removeClass('active');
    if (jQuery(strliID).length > 0) {
        jQuery(strliID).addClass('active');
    } else {
        jQuery('.gallery-view-options-0').addClass('active');
    }
    jQuery('input[data-slider="true"]').bind("slider:changed", function (event, data) {
        jQuery(this).parent().find('span').html(parseInt(data.value) + "%");
        jQuery(this).val(parseInt(data.value));
    });

    jQuery('#arrows-type input[name="params[slider_navigation_type]"]').change(function(){
        jQuery(this).parents('ul').find('li.active').removeClass('active');
        jQuery(this).parents('li').addClass('active');
    });
    jQuery('input[data-gallery="true"]').bind("gallery:changed", function (event, data) {
        jQuery(this).parent().find('span').html(parseInt(data.value)+"%");
        jQuery(this).val(parseInt(data.value));
    });

    jQuery('#gallery-view-tabs li a').click(function(){
        jQuery('#gallery-view-tabs > li').removeClass('active');
        jQuery(this).parent().addClass('active');
        jQuery('#gallery-view-tabs-contents > li').removeClass('active');
        var liID=jQuery(this).attr('href').split('#').join('.');//alert(liID);
        jQuery(liID).addClass('active');
        liID=liID.replace('.','');
        jQuery('#adminForm').attr('action',"admin.php?page=Options_gallery_styles&task=save#"+liID);
    });

    jQuery('#huge_it_sl_effects').change(function(){

        jQuery('.gallery-current-options').removeClass('active');
        jQuery('#gallery-current-options-'+jQuery(this).val()).addClass('active');
        if(jQuery(this).val() == 10){
            jQuery('#rating').parent().hide();
            jQuery("#display_type option[value=1]").hide();
            if(jQuery("#display_type").val() == 1){
                jQuery("#display_type").val(0);
            }
        }
        else{
            jQuery('#rating').parent().show();
            jQuery("#display_type option[value=1]").show();
        }

        if( jQuery(this).val() == 1 || jQuery(this).val() == 3 ){
            jQuery('#gallery-current-options-0').hide();
        }
        else{
            jQuery('#gallery-current-options-0').show();
        }

        if( jQuery(this).val() == 10 || jQuery(this).val() == 3 ){
            jQuery('#rating_inp').hide();
        }
        else{
            jQuery('#rating_inp').show();
        }

    });
    jQuery('#huge_it_sl_effects').change();
    jQuery(".close_gallery_free_banner").on("click",function(){
        jQuery(".gallery_free_version_banner").css("display","none");
        galleryImgSetCookie( 'hgGalleryFreeBannerShow', 'no', {expires:86400} );
    });

    jQuery('.huge-it-insert-video-button').click(function(){
        alert("Image Gallery Settings are disabled in free version. If you need those functionalityes, you need to buy the commercial version.");
        return false;
    });
    jQuery('a[href*="remove_gallery"]').click(function(){
        if(!confirm('Are you sure you want to delete this item?'))
            return false;
    });

});

jQuery(window).resize(function () {
    galleryImgResizeAdminImages();
});

jQuery(window).load(function(){
    if(galleryImgGetCookie('gallery_deleted')){
        galleryImgSetCookie( 'gallery_deleted', 'success', {expires:-1} );
    }
    galleryImgResizeAdminImages();
});

function galleryImgSetCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }


    if(typeof value == "object"){
        value = JSON.stringify(value);
    }
    value = encodeURIComponent(value);
    var updatedCookie = name + "=" + value;

    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}
function galleryImgFilterInputs() {
    var mainInputs = "";
    jQuery("#images-list > li.highlights").each(function(){
        jQuery(this).next().addClass('submit-post');
        jQuery(this).prev().addClass('submit-post');
        jQuery(this).addClass('submit-post');
        jQuery(this).removeClass('highlights');
    });
    if(jQuery("#images-list > li.submit-post").length) {
        jQuery("#images-list > li.submit-post").each(function(){
            var inputs = jQuery(this).find('.order_by').attr("name");
            var n = inputs.lastIndexOf('_');
            var res = inputs.substring(n+1, inputs.length);
            res +=',';
            mainInputs += res;
        });
        mainInputs = mainInputs.substring(0,mainInputs.length-1);
        jQuery(".changedvalues").val(mainInputs);
        jQuery("#images-list > li").not('.submit-post').each(function(){
            jQuery(this).find('input').removeAttr('name');
            jQuery(this).find('textarea').removeAttr('name');
        });
        return mainInputs;

    };
    jQuery("#images-list > li").each(function(){
        jQuery(this).find('input').removeAttr('name');
        jQuery(this).find('textarea').removeAttr('name');
        jQuery(this).find('select').removeAttr('name');
    });
};
function galleryImgSubmitbutton(pressbutton) {
    if (!document.getElementById('name').value) {
        alert("Name is required.");
        return;
    }
        if (jQuery('#content_per_page').val() < 1) {
            alert("Images Per Page must be greater than 0.");
            return;
        }


    galleryImgFilterInputs();
    document.getElementById("adminForm").action = document.getElementById("adminForm").action + "&task=" + pressbutton;
    document.getElementById("adminForm").submit();
}
function galleryImgListItemTask(this_id,replace_id){
    document.getElementById('oreder_move').value=this_id+","+replace_id;
    document.getElementById('admin_form').submit();
}
function galleryImgDoNothing() {
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
    if( keyCode == 13 ) {
        if(!e) var e = window.event;
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
            e.stopPropagation();
            e.preventDefault();
        }
    }
}
function galleryImgResizeAdminImages(){
    jQuery('ul#images-list > li > .image-container .list-img-wrapper img').each(function () {
        var imhHeight = jQuery(this).prop('naturalHeight');
        var imhWidth = jQuery(this).prop('naturalWidth');
        var parentWidth = jQuery(this).parent().width();
        var parentHeight = jQuery(this).parent().height();
        var imgRatio = imhWidth/imhHeight;
        var parentRatio = parentWidth/parentHeight;
        if (imgRatio <= parentRatio) {
            jQuery(this).css({
                position: "relative",
                width: '100%',
                top: '50%',
                transform: 'translateY(-50%)',
                height: 'auto',
                left: '0'
            });
        } else {
            jQuery(this).css({
                position: "relative",
                height: '100%',
                left: '50%',
                transform: 'translateX(-50%)',
                width: 'auto',
                top: '0'
            });
        }
    });
}
function galleryImgPopupSizes(checkbox){
    if(checkbox.is(':checked')){
        jQuery('.lightbox-options-block .not-fixed-size').css({'display':'none'});
        jQuery('.lightbox-options-block .fixed-size').css({'display':'block'});
    }else {
        jQuery('.lightbox-options-block .fixed-size').css({'display':'none'});
        jQuery('.lightbox-options-block .not-fixed-size').css({'display':'block'});
    }
}
function galleryImgGetCookie (name) {
    var cookie, allcookie = document.cookie.split(';');
    for (var i = 0; i < allcookie.length; i++) {
        cookie = allcookie[i].split('=');
        cookie[0] = cookie[0].replace(/ +/g,'');
        if (cookie[0] == name) {
            return decodeURIComponent(cookie[1]);
        }
    }
    return false;
}

