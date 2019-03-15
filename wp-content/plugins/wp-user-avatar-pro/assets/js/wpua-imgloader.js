(function($, window, document, undefined) {
    function CI_Webcam(elements, successCallback, errorCallback) {
        this.element = elements.video;
        this.stream = null;
    }
    CI_Webcam.prototype = {
    };
    function ci_modal() {
        this.target_image = null;
        this.stream = null;
        this.webcam_on = false;
        this.webcam = null;
        this.archive_on = false;
        this.is_getusermedia = true;
        
        this.iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        this.init();
        this.controls_event();
    }
    ci_modal.prototype = {
        init: function() {
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
            window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;
            this.is_getusermedia = this.is_getusermedia && !!navigator.getUserMedia && !!window.URL;
            $("body").append('<div id="ci-modal-bg"></div><div id="ci-modal"><div class="ci-modal-body"></div></div>');
            if (typeof Webcam == 'object')
                this.webcam = Webcam;
        },
        controls_event: function() {
            var imgobj = this;
            $("body").on("click", ".delete_avatar", function() {
                delete_btn = $(this);
                var avatar_url = delete_btn.parent().find('.ci_choose_image').attr('src');
                delete_btn.parent().find('input[name="wp-user-avatar-deleted-url[]"]').val(avatar_url);
                $(this).hide();
                delete_btn.parent().find('input[name="wpua_avatar"]').val(delete_btn.attr('data-nonce'));
                delete_btn.parent().find('.ci_choose_image').attr('src', $('#default_avatar').val());
                delete_btn.parent().find('.reset_avatar').show().click(function() {
                    delete_btn.parent().find('.ci_choose_image').attr('src', avatar_url);
                    $(this).hide();
                    delete_btn.show();
                    delete_btn.parent().find('.wp-user-avatar-url').val('');
                    delete_btn.parent().find('input[name="wp-user-avatar-deleted-url[]"]').val('');
                    delete_btn.parent().find('input[name="wpua_avatar"]').val('');
                });
            });
            $("body").on("click", ".ci_controls .icon-close", function(e) {
                e.preventDefault();
                imgobj.close();
            });
            $("body").on("click", ".ci_controls .icon-disk", function(e) {
                e.preventDefault();
                $(".ci_editor form").submit();
            });
            $("body").on("click", ".ci_controls .wpua_capture", function(e) {
                e.preventDefault();
                $('.ci_img_container').trigger('click');
                $(this).hide();
            });
            $("body").on("click", ".ci_controls .icon-image", function(e) {
                e.preventDefault();
            });
            $("body").on("click", ".ci_editor .wpua_capture", function(e) {
                if (imgobj.is_getusermedia == true) {
                    return;
                }
                imgobj.showLoader();
                if (imgobj.webcam) {
                    var Webcam = imgobj.webcam;
                    Webcam.snap(function(dataURL) {
                        if (imgobj.stream) {
                            imgobj.stream.getTracks().forEach(function(track) {
                                track.stop();
                            });
                        }
                        var blob = imgobj.dataURItoBlob(dataURL);
                        var reader = new FileReader();
                        var img = new Image();
                        reader.onload = function(e) {
                            img.src = reader.result;
                            img.onload = function() {
                                $(".ci_editor .ci_img_container").css('transform','unset');
                                $(".ci_editor .ci_img_container").html(img);
                                imgobj.loadCropper($(".ci_editor .ci_img_container img"));
                            }
                        }
                        reader.readAsDataURL(blob);
                    })
                } else {
                    var video = $("#ci_webcam")[0];
                    //Capture it now..
                    var canvas = document.createElement('canvas');
                    var canvas_context = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas_context.drawImage(video, 0, 0);
                    imgobj.stream.getTracks().forEach(function(track) {
                        track.stop();
                    });
                    canvas.toDataURL('image/jpeg');
                    var dataURL = canvas.toDataURL('image/jpeg', 0.5);
                    var blob = imgobj.dataURItoBlob(dataURL);
                    var reader = new FileReader();
                    var img = new Image();
                    reader.onload = function(e) {
                        img.src = reader.result;
                        img.onload = function() {
                            $(".ci_editor .ci_img_container").html(img);
                            imgobj.loadCropper($(".ci_editor .ci_img_container img"));
                        }
                    }
                    reader.readAsDataURL(blob);
                }
            });
            $("body").on("click", ".ci_editor .ci_img_container, .ci_editor  .ci_placeholder", function(e) {
                e.preventDefault();
                if (imgobj.archive_on == true) {
                    var image = wp.media({
                            title: wpua_imgloader_vars.wpua_string.upload_image,
                            multiple: false
                        }).open()
                        .on('select', function(e) {
                            imgobj.showLoader();
                            var uploaded_image = image.state().get('selection').first();
                            var image_url = uploaded_image.toJSON().url;
                            var image_type = uploaded_image.toJSON().mime;
                            var allowed_file_size = uploaded_image.toJSON().filesizeInBytes;
                            var img = new Image();
                            img.src = image_url;
                            img.onload = function() {
                                imgobj.set_image(img);
                            };
                        });
                } else if (imgobj.webcam_on == true ) {
                    if (imgobj.is_getusermedia == false) {
                        return;
                    }
                    imgobj.showLoader();
                    if (imgobj.webcam) {
                        var Webcam = imgobj.webcam;
                        Webcam.snap(function(dataURL) {
                            if (imgobj.stream) {
                                imgobj.stream.getTracks().forEach(function(track) {
                                    track.stop();
                                });
                            }
                            var blob = imgobj.dataURItoBlob(dataURL);
                            var reader = new FileReader();
                            var img = new Image();
                            reader.onload = function(e) {
                                img.src = reader.result;
                                img.onload = function() {
                                    $(".ci_editor .ci_img_container").css('transform','none');
                                    $(".ci_editor .ci_img_container").html(img);
                                    imgobj.loadCropper($(".ci_editor .ci_img_container img"));
                                }
                            }
                            reader.readAsDataURL(blob);
                        })
                    } else {
                        var video = $("#ci_webcam")[0];
                        //Capture it now..
                        var canvas = document.createElement('canvas');
                        var canvas_context = canvas.getContext('2d');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas_context.drawImage(video, 0, 0);
                        imgobj.stream.getTracks().forEach(function(track) {
                            track.stop();
                        });
                        canvas.toDataURL('image/jpeg');
                        var dataURL = canvas.toDataURL('image/jpeg', 0.5);
                        var blob = imgobj.dataURItoBlob(dataURL);
                        var reader = new FileReader();
                        var img = new Image();
                        reader.onload = function(e) {
                            img.src = reader.result;
                            img.onload = function() {
                                $(".ci_editor .ci_img_container").html(img);
                                imgobj.loadCropper($(".ci_editor .ci_img_container img"));
                            }
                        }
                        reader.readAsDataURL(blob);
                    }
                } else {
                    if (!imgobj.stream)
                        $('.ci_editor .ci_file_control').trigger('click');
                }
            });
            
            $("body").on("click", ".ci_editor .ci_img_container .jcrop-holder ", function(e) {
                e.stopPropagation();
            });
            $("body").on("click", ".ci_editor .ci_controls .icon-image ", function(e) {
                e.preventDefault();
                if (imgobj.webcam_on == true) {
                    imgobj.webcam_on = false;
                    imgobj.stopWebcam();
                }
                if (imgobj.mediamanager_on == true) {
                    imgobj.mediamanager_on = false;
                    imgobj.disableMediaManager();
                } else {
                    imgobj.mediamanager_on = true;
                    imgobj.enableMediaManager();
                }
            });
            $("body").on("click", ".ci_controls a.icon-camera", function(e) {
                e.preventDefault();
                imgobj.disableMediaManager();
                if (imgobj.webcam_on != true && !$(this).hasClass('webcam_on')) {
                    imgobj.webcam_on = true;
                    imgobj.onWebcam();
                } else {
                    imgobj.webcam_on = false;
                    imgobj.stopWebcam();
                }
            });
            $("body").on("change", ".ci_file_control", function(event) {
                if (event.target.files && event.target.files[0]) {
                    var size = event.target.files[0].size;
                    var type = event.target.files[0].type;
                    var show_image = true;
                    if (imgobj.fileSizeCompare(size, type) == true) {
                        imgobj.showLoader();
                        var reader = new FileReader();
                        var img = new Image();
                        reader.onload = function(e) {
                            img.src = reader.result;
                            img.onload = function() {
                                imgobj.set_image(img);
                            }
                        }
                        reader.readAsDataURL(event.target.files[0]);
                    }
                }
            });
            $("body").on('dragenter', ".ci_editor .ci_img_container", function(e) {
                e.stopPropagation();
                e.preventDefault();
            });
            $("body").on('dragover', ".ci_editor .ci_img_container", function(e) {
                e.stopPropagation();
                e.preventDefault();
            });
            $("body").on('drop', ".ci_editor .ci_img_container", function(e) {
                e.preventDefault();
                var files = e.originalEvent.dataTransfer.files;
                if (files && files[0]) {
                    imgobj.showLoader();
                    var reader = new FileReader();
                    var img = new Image();
                    reader.onload = function(e) {
                        img.src = reader.result;
                        img.onload = function() {
                            imgobj.set_image(img);
                        }
                    }
                    reader.readAsDataURL(files[0]);
                }
            });
            $(document).on('dragenter', function(e) {
                e.stopPropagation();
                e.preventDefault();
            });
            $(document).on('dragover', function(e) {
                e.stopPropagation();
                e.preventDefault();
            });
            $(document).on('drop', function(e) {
                e.stopPropagation();
                e.preventDefault();
            });
            $("body").on('submit', ".ci_editor form", function(e) {
                e.preventDefault();
                form_data = new FormData(this);
                if (typeof $(".ci_editor .ci_img_container img").attr('src') == 'undefined') {
                    imgobj.setPlaceholder(wpua_imgloader_vars.wpua_string.no_image, wpua_imgloader_vars.wpua_string.no_image_instruction)
                    return false;
                }
                var image_type = $(".ci_editor .ci_img_container img").attr('src').match(/^data\:image\/(\w+)/);
                if (typeof image_type[1] != "undefined" && (image_type[1] == 'jpeg' || image_type[1] == 'png' || image_type[1] == 'gif')) {
                    form_data.append("ci_blob", $(".ci_editor .ci_img_container img").attr('src'));
                    form_data.append("file_type", image_type[1]);
                    form_data.append("action", 'wpua_save_avatar_action');
                } else {
                    return false;
                }
                imgobj.showLoader();
                //check if no cache save
                is_wp_user_avatar = $(imgobj.target_image).data('source');
                if (is_wp_user_avatar == "wp-user-avatar") {
                    form_data.append("no_cache", 'true');
                }else if (is_wp_user_avatar == "role-based-avatar") {
                    form_data.append("no_cache", 'true');
                    form_data.append("avatar_for_role", $(imgobj.target_image).data('role'));
                }
                //end
                //now send all data to server and close the editor.
                $.ajax({
                    url: wpua_imgloader_vars.url,
                    type: "POST",
                    data: form_data,
                    contentType: false,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    success: function(response) {
                        imgobj.hideLoader();
                        if (typeof response.error != 'undefined') {
                        } else {
                            is_custom_target = $(imgobj.target_image).data('target');
                            if ( $(imgobj.target_image).hasClass('role_based_avatar') ) {  
                                $("#" + is_custom_target).attr("src", response.img);
                                $(imgobj.target_image).parent().parent().find('.role-based-avatar').val(response.filename);
                            }else{  
                                $(imgobj.target_image).parent().find('input[name="wp-user-avatar-deleted-url[]"]').val($(imgobj.target_image).attr("src"));
                                if (typeof is_custom_target != 'undefined') {
                                    $("#" + is_custom_target).attr("src", response.img);
                                    $('#wp-user-avatar-url').val(response.filename);
                                } else {
                                    $(imgobj.target_image).attr("src", response.img);
                                    $(imgobj.target_image).parent().find('.wp-user-avatar-url').val(response.filename);
                                }
                            } 
                            imgobj.close();
                        }
                    }
                });
            });
        },
        set_image: function(img) {
            var imgobj = this;
            var width_height_valid = true;
            var canvas = document.createElement('canvas');
            var canvas_context = canvas.getContext('2d');
            var max_width = wpua_imgloader_vars.restriction.max_file_width;
            var max_height = wpua_imgloader_vars.restriction.max_file_height;
            if (max_width > 0 && img.width > max_width) {
                width_height_valid = false;
            }
            if (max_height > 0 && img.height > max_height) {
                width_height_valid = false;
            }
            if (width_height_valid == true) {
                var ratio = 1;
                if (img.width > 509 || img.height > 330) {
                    ratio = 470 / img.width;
                } else {
                    ratio = 1;
                }
                canvas.width = img.width * ratio;
                canvas.height = img.height * ratio;
                canvas_context.scale(ratio, ratio); // scale by 1/4
                canvas_context.drawImage(img, 0, 0);
                var final_img = new Image();
                final_img.src = canvas.toDataURL();
                final_img.onload = function() {
                    $(".ci_editor .ci_img_container").html(final_img);
                    imgobj.loadCropper($(".ci_editor .ci_img_container img"));
                }
            } else {
                $(".ci_editor .ci_img_container").html('');
                $(".ci_editor .ci_placeholder label").text(wpua_imgloader_vars.wpua_string.file_size_error);
                $(".ci_editor .ci_placeholder p").html(wpua_imgloader_vars.wpua_string.file_size_error_description);
                imgobj.placeholder('show');
                imgobj.hideLoader();
            }
        },
        showLoader: function() {
            var imgobj = this;
            $('.ci_editor .ci_placeholder .wpua-loader').addClass('wpua_loader');
            imgobj.setPlaceholder("", "");
            imgobj.placeholder("show");
        },
        hideLoader: function() {
            $('.ci_editor .ci_placeholder .wpua-loader').removeClass('wpua_loader');
        },
        loadCropper: function(obj) {
            var interval, imgobj = this;
            interval = setInterval(function() {
                $(obj).Jcrop({
                    aspectRatio: 1,
                    bgColor: '',
                    onSelect: imgobj.updateCoords
                });
                imgobj.placeholder('hide');
                clearInterval(interval);
                imgobj.hideLoader();
            }, 1000);
        },
        fileSizeCompare: function(file_size, file_type) {
            var imgobj = this;
            if (wpua_imgloader_vars.restriction.max_file_size < file_size) {
                $(".ci_editor .ci_placeholder label").text(wpua_imgloader_vars.wpua_string.file_size_error);
                $(".ci_editor .ci_placeholder p").html(wpua_imgloader_vars.wpua_string.file_size_error_description);
                imgobj.placeholder('show');
                return false;
            } else if (file_type.search('image') == -1) {
                $(".ci_editor .ci_placeholder label").text(wpua_imgloader_vars.wpua_string.file_type_error);
                $(".ci_editor .ci_placeholder p").html(wpua_imgloader_vars.wpua_string.file_type_error_description);
                imgobj.placeholder('show');
                return false;
            }
            return true;
        },
        disableMediaManager: function() {
            var imgobj = this;
            $(".ci_editor .ci_controls .icon-image").removeClass('archive_on');
            imgobj.archive_on = false;
            imgobj.resetPlaceholder();
        },
        enableMediaManager: function() {
            var imgobj = this;
            $(".ci_editor .ci_controls .icon-image").addClass('archive_on');
            imgobj.archive_on = true;
            imgobj.setPlaceholder(wpua_imgloader_vars.wpua_string.mediamanager_on_title, wpua_imgloader_vars.wpua_string.mediamanager_on_instruction);
        },
        onWebcam: function() {
            var imgobj = this;
            imgobj.placeholder("hide");
            if (imgobj.is_getusermedia == false && !imgobj.iOS) {
                $('.wpua_capture').show();
            }
            var container = $(".ci_editor .ci_img_container")[0];
            
            if(imgobj.iOS){
            this.webcam.set({
						iosPlaceholderText: '',
						user_callback: function(data_uri) {
							// display results in page
							container.innerHTML = 
								'<img src="'+data_uri+'"/>';
						}
					});
				}
            if (!imgobj.stream) {
                if (this.webcam) {
                    var webcam = this.webcam;
                    
                    if(screen.width < screen.height) {
						webcam.set( 'constraints', {
							width: container.offsetHeight,
							height: container.offsetWidth
						} );
						webcam.set({flip_horiz: true});
					}else{
						webcam.set( 'constraints', {
							width: container.offsetWidth,
							height: container.offsetHeight
						} );
						webcam.set({flip_horiz: true});
					}
                    webcam.attach(container);
                    webcam.on('live', function() {
                        imgobj.placeholder("hide");
                        imgobj.stream = webcam.stream;
                        console.log(imgobj.stream);
                        $(".ci_editor .ci_img_container").addClass('webcam_on');
                    });
                    webcam.on('error', function() {
                        imgobj.html5_webcam();
                    });
                } else {
                    imgobj.placeholder("hide");
                    this.html5_webcam();
                }
            }
        },
        html5_webcam: function() {
            var imgobj = this;
            this.webcam = null;
            var video = document.createElement("video");
            video.setAttribute("id", "ci_webcam");
            $(".ci_editor .ci_img_container").html(video);
            var webcam = imgobj.play($("#ci_webcam")[0]);
            imgobj.placeholder("hide");
            $(".ci_editor .ci_controls .icon-camera").addClass('webcam_on');
        },
        stopWebcam: function() {
            var imgobj = this;
            $('.wpua_capture').hide();
            if (imgobj.stream) {
                imgobj.stream.getTracks().forEach(function(track) {
                    track.stop();
                });
                imgobj.stream = null;
                imgobj.webcam_on = false;
                $("#ci_webcam").remove();
                $(".ci_editor .ci_img_container").find('video, object').remove();
            }
            imgobj.resetPlaceholder();
            imgobj.placeholder("show");
            $(".ci_editor .ci_controls .icon-camera").removeClass('webcam_on');
        },
        placeholder: function(type) {
            var placeholder = $(".ci_editor .ci_placeholder");
            if (type == 'hide') placeholder.hide();
            if (type == 'show') placeholder.show();
        },
        setPlaceholder: function(title, message) {
            var placeholder = $(".ci_editor .ci_placeholder");
            placeholder.find('label').html(title);
            placeholder.find('p').text(message);
        },
        resetPlaceholder: function() {
            var placeholder = $(".ci_editor .ci_placeholder");
            placeholder.find('label').text(wpua_imgloader_vars.wpua_string.drop_instruction);
            placeholder.find('p').text(wpua_imgloader_vars.wpua_string.control_instruction);
            
            if(this.iOS){
				$('body').find('.webcamjs-ios-placeholder').remove();
			}
        },
        dataURItoBlob: function(dataURI) {
            // convert base64/URLEncoded data component to raw binary data held in a string
            var byteString;
            if (dataURI.split(',')[0].indexOf('base64') >= 0)
                byteString = atob(dataURI.split(',')[1]);
            else
                byteString = unescape(dataURI.split(',')[1]);
            // separate out the mime component
            var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
            // write the bytes of the string to a typed array
            var ia = new Uint8Array(byteString.length);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            return new Blob([ia], {
                type: mimeString
            });
        },
        show: function() {
            this.content(this.editor());
            $("#ci-modal, #ci-modal-bg").toggleClass("active");
        },
        content: function(obj) {
            $("#ci-modal .ci-modal-body").html("");
            $("#ci-modal .ci-modal-body").append(obj);
        },
        updateCoords: function(c) {
            var imgobj = this;
            $('.ci_editor form input[name="ci_crop_x"]').val(c.x);
            $('.ci_editor form input[name="ci_crop_y"]').val(c.y);
            $('.ci_editor form input[name="ci_crop_w"]').val(c.w);
            $('.ci_editor form input[name="ci_crop_h"]').val(c.h);
        },
        editor: function() {
            var media_uploader = "",
                webcam_uploader = "";
            if (wpua_imgloader_vars.WPUA_MEDIA == 'true')
                media_uploader = '<a title="' + wpua_imgloader_vars.wpua_string.media_uploader + '" href="" class="fa icon-image"></a>';
            if (wpua_imgloader_vars.wpua_webcam == 'true')
                webcam_uploader += '<a title="' + wpua_imgloader_vars.wpua_string.webcam_on + '" href="" class="fa icon-camera"></a>';
            $editor = '<div class="ci_editor"><form class="wpua_image_form" enctype="multipart/form-data" action="ajax_file.php" method="post"><input type="hidden"  name="ci_crop_x" /><input type="hidden"  name="ci_crop_y" /><input type="hidden"  name="ci_crop_w" /><input type="hidden"  name="ci_crop_h" /><div class="ci_img_container"></div><div class="ci_controls">' + media_uploader + '<input name="ci_file_control" class="ci_file_control" type="file"  />' + webcam_uploader + '<a title="' + wpua_imgloader_vars.wpua_string.close + '" href="" class="fa icon-close"></a><a title="' + wpua_imgloader_vars.wpua_string.save + '" href="" class="fa icon-disk"></a><a title="Title" href="" class="wpua_capture">' + wpua_imgloader_vars.wpua_string.capture_image + '</a></div><div class="ci_placeholder"><label>' + wpua_imgloader_vars.wpua_string.drop_instruction + ' </label><p>' + wpua_imgloader_vars.wpua_string.control_instruction + '</p><div class="wpua-loader"></div></div></form></div>';
            return $editor;
        },
        close: function() {
            var imgobj = this;
            imgobj.stopWebcam();
            $("#ci-modal, #ci-modal-bg").removeClass("active");
        },
        play: function(video) {
            var imgobj = this;
            function successCallback(stream) {
                imgobj.stream = stream;
                if (video.mozSrcObject !== undefined) {
                    video.mozSrcObject = imgobj.stream;
                } else {
                    video.src = (window.URL && window.URL.createObjectURL(imgobj.stream)) || imgobj.stream;
                }
                video.play();
            }
            function errorCallback(error) {
                imgobj.stopWebcam();
            }
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
            window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;
            if (navigator.getUserMedia) {
                navigator.getUserMedia({
                    video: true
                }, successCallback, errorCallback);
            } else {
                console.log('Native web camera streaming (getUserMedia) not supported in this browser.');
            }
        }
    }
    function ci_choose_image(element, options) {
        this.element = element;
        this.config = options;
        this.modal = options['modal'];
        this.init();
    }
    ci_choose_image.prototype = {
        init: function() {
            var imgobj = this;
            $(imgobj.element).wrap("<div class='avatar_container'></div>");
            elem_tag = $(imgobj.element).prop('tagName').toLowerCase();
            if (elem_tag == 'img') {
                $(imgobj.element).parent().append("<input type='hidden' name='wp-user-avatar-deleted-url[]' value=''><input type='hidden' name='wp-user-avatar-url[]' class='wp-user-avatar-url' value=''><i class='icon-edit'></i><i class='delete_avatar icon-trash'></i><i style='display:none;' class='reset_avatar icon-undo'></i>");
            }
            //register all events
            imgobj.controls_event();
        },
        controls_event: function() {
            var imgobj = this;
            elem_tag = $(imgobj.element).prop('tagName').toLowerCase();
            if (elem_tag != 'img') {
                $(imgobj.element).on("click", function() {
                    imgobj.modal.target_image = imgobj.element;
                    imgobj.modal.show();
                });
            } else {
                $(imgobj.element).parent().on("click", ".icon-edit", function() {
                    imgobj.modal.target_image = imgobj.element;
                    imgobj.modal.show();
                });
            }
        }
    }
    $.fn.ci_choose_image = function(options) {
        var modal = new ci_modal();
        options["modal"] = modal;
        this.each(function() {
            if (!$.data(this, "ci_choose_image")) {
                $.data(this, "ci_choose_image", new ci_choose_image(this, options));
            }
        });
    };
    $(document).ready(function() {
        $(".ci_choose_image").ci_choose_image({});
        //Control Avatar Storage Options
        $("input[name='avatar_storage_option']").change(function() {
            $(".wpua_media_storage").hide();
            $(".wpua_aws_storage").hide();
            $(".wpua_dropbox_storage").hide();
            $(".wpua_directory_storage").hide();
            $(".wpua_" + $("input[name='avatar_storage_option']:checked").val() + "_storage").show();
        });
        $("input[name='avatar_storage_option']").trigger("change");
    });
}(jQuery, window, document));
