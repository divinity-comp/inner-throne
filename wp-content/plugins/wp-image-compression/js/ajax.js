jQuery(document).ready(function ($) {

    var errors = [
        {
            code: 401,
            msg: 'Unnknown API Key. Please check your API key and try again'
        },
        {
            code: 403,
            msg: 'Your account has been temporarily suspended'
        },
        {
            code: 413,
            msg: 'File size too large. The maximum file size for your plan is 1048576 bytes'
        },
        {
            code: 415,
            msg: 'File type not supported'
        },
        {
            code: 415,
            msg: 'WebP compression is non available for SVG images'
        },
        {
            code: 422,
            msg: 'You need to specify either callback_url or wait flag'
        },
        {
            code: 422,
            msg: 'This image can not be optimized any further'
        },
        {
            code: 500,
            msg: 'Wpimage has encountered an unexpected error and cannot fulfill your request'
        },
        {
            code: 502,
            msg: 'Couldn\'t get this file'
        }
    ];

    $('a.wpimageError').tipsy({
        fade: true,
        gravity: 'e'
    });

    var data = { action: 'wpimage_request' },
        errorTpl = '<div class="wpimageErrorWrap"><a class="wpimageError">Failed! Hover here</a></div>',
        $btnApplyBulkAction = $("#doaction"),
        $btnApplyBulkAction2 = $("#doaction2"),
        $topBulkActionDropdown = $(".tablenav.top .bulkactions select[name='action']"),
        $bottomBulkActionDropdown = $(".tablenav.bottom .bulkactions select[name='action2']");


    var requestSuccess = function (data, textStatus, jqXHR) {
        var $button = $(this);
        var $parent = $button.parent(), $cell = $button.closest("td");

        console.log( data );

        if (data.success && 'undefined' === typeof data.error) {

            $button.text("Image optimized");

            var type = data.type,
                compressedSize = data.compressed_size,
                originalSize = data.original_size,
                savingsPercent = data.savings_percent,
                $originalSizeColumn = $(this).parent().prev("td.original_size"),
                compressedData = '',
                withoutLastChunk = savingsPercent.slice(0, savingsPercent.lastIndexOf("%"));

            $parent.fadeOut("fast", function () {
                $cell.find(".noSavings, .wpimageErrorWrap").remove();
                if (0 < withoutLastChunk) {
                    compressedData = '<strong>' + compressedSize + '</strong><br /><small>Savings: ' + savingsPercent + '</small>';
                    if ('undefined' !== typeof data.thumbs_data ) {
                        compressedData += '<br /><small>' + data.thumbs_data.length + ' thumbs optimized</small>';
                    }
                } else {
                    compressedData = '<br/><small>No further optimization required</small>';
                }
                $(this).parent('td').html(compressedData);
                $originalSizeColumn.html(originalSize);
                $parent.remove();
            });

        }
        else if (data.error) {
            var $error = $(errorTpl).attr("title", data.error);
            $parent.closest("td").find(".wpimageErrorWrap").remove();
            $parent.after($error);
            $error.tipsy({ fade: true, gravity: 'e' });
            $button.text("Retry request").removeAttr("disabled").css({ opacity: 1 });
        }else if (data.reload) {
            window.location.reload();
        }
        else{
            $parent.fadeOut("fast", function () {
                $cell.find(".noSavings, .wpimageErrorWrap").remove();
                $(this).parent('td').html('<small>Savings: 0</small>');
                $parent.remove();
            });
        }
    };

    var requestFail = function (jqXHR, textStatus, errorThrown) {
        $(this).removeAttr("disabled");
    };

    var requestComplete = function (jqXHR, textStatus, errorThrown) {
        $(this).removeAttr("disabled");
        $(this).parent().find(".wpimageSpinner").css("display", "none");
    };

    var opts = '<option value="wpimage-bulk-lossy">' + "Compress all" + '</option>';

    $topBulkActionDropdown.find("option:last-child").before(opts);
    $bottomBulkActionDropdown.find("option:last-child").before(opts);


    var getBulkImageData = function () {
        var $rows = $("tr[id^='post-']"),
                $row = null,
                postId = 0,
                imageDateItem = {},
                $krakBtn = null,
                btnData = {},
                originalSize = '',
                rv = [];
        $rows.each(function () {
            $row = $(this);
            postId = this.id.replace(/^\D+/g, '');
            if ($row.find("input[type='checkbox'][value='" + postId + "']:checked").length) {
                $krakBtn = $row.find(".wpimage_req");
                if ($krakBtn.length) {
                    btnData = $krakBtn.data();
                    originalSize = $.trim($row.find('td.original_size').text());
                    btnData.originalSize = originalSize;
                    rv.push(btnData);
                }
            }
        });
        return rv;
    };

    var renderBulkImageSummary = function (bulkImageData) {
        var modalOptions = {
            zIndex: 4,
            escapeClose: true,
            clickClose: false,
            closeText: 'close',
            showClose: false
        },
        setting = $("button.wpimage_req").eq(0).data("setting"),
                nImages = bulkImageData.length,
                header = '<p class="wpimageBulkHeader">Wpimage Bulk Image Optimization</p>',
                krakEmAll = '<button class="wpimage_req_bulk">Compress all</button>',
                $modal = $('<div id="wpimage-bulk-modal" class="modal"></div>')
                .html(header)
                .append('<br /><small class="wpimage-bulk-small">The following <strong>' + nImages + '</strong> images will be optimized by wp-image.co.uk<br />')
                .appendTo("body")
                .modal(modalOptions)
                .bind($.modal.BEFORE_CLOSE, function (event, modal) {

                })
                .bind($.modal.OPEN, function (event, modal) {

                })
                .bind($.modal.CLOSE, function (event, modal) {
                    $("#wpimage-bulk-modal").remove();
                })
                .css({
                    top: "10px",
                    marginTop: "40px"
                });

        if (setting === 'lossy') {
            $("#wpimage-bulk-type-lossy").attr("checked", true);
        } else {
            $("#wpimage-bulk-type-lossless").attr("checked", true);
        }

        $bulkSettingSpan = $(".bulkSetting");
        $("input[name='wpimage-bulk-type']").change(function () {
            var text = this.id === "wpimage-bulk-type-lossy" ? "lossy" : "lossless";
            $bulkSettingSpan.text(text);
        });

        // to prevent close on clicking overlay div
        $(".jquery-modal.blocker").click(function (e) {
            return false;
        });

        // otherwise media submenu shows through modal overlay
        $("#menu-media ul.wp-submenu").css({
            "z-index": 1
        });

        var $table = $('<table id="wpimage-bulk"></table>'),
                $headerRow = $('<tr class="wpimage-bulk-header"><td>File</td><td style="width:120px">Original Size</td><td style="width:120px">compressed Size</td><td style="width:120px">Savings</td><td style="width:120px">% Savings</td></tr>');

        $table.append($headerRow);
        $.each(bulkImageData, function (index, element) {
            $table.append('<tr class="wpimage-item-row" data-wpimagebulkid="' + element.id + '"><td class="wpimage-filename">' + element.filename + '</td><td class="wpimage-originalsize">' + element.originalSize + '</td><td class="wpimage-compressedsize"><span class="wpimageBulkSpinner hidden"></span></td><td class="wpimage-savings"></td><td class="wpimage-savingsPercent"></td></tr>');
        });

        $modal
                .append($table)
                .append(krakEmAll)
                .append('<span class="close-wpimage-bulk">Close Window</span>');

        $(".close-wpimage-bulk").click(function () {
            $.modal.close();
        });

        if (!nImages) {
            $(".wpimage_req_bulk")
                    .attr("disabled", true)
                    .css({
                        opacity: 0.5
                    });
        }
    };

    var bulkAction = function (bulkImageData) {

        $bulkTable = $("#wpimage-bulk");
        var jqxhr = null;

        var q = async.queue(function (task, callback) {
            var id = task.id,
                    filename = task.filename;

            var $row = $bulkTable.find("tr[data-wpimagebulkid='" + id + "']"),
                    $compressedSizeColumn = $row.find(".wpimage-compressedsize"),
                    $spinner = $compressedSizeColumn
                    .find(".wpimageBulkSpinner")
                    .css({
                        display: "inline-block"
                    }),
                    $savingsPercentColumn = $row.find(".wpimage-savingsPercent"),
                    $savingsBytesColumn = $row.find(".wpimage-savings");

            jqxhr = $.ajax({
                url: ajax_object.ajax_url,
                data: {
                    'action': 'wpimage_request',
                    'id': id
                },
                type: "post",
                dataType: "json",
                timeout: 360000
            })
                    .done(function (data, textStatus, jqXHR) {
                        //console.log('test');
                        //console.log(data);
                        if (data.success && typeof data.error === 'undefined') {
                            var type = data.type,
                                    originalSize = data.original_size,
                                    compressedSize = data.compressed_size,
                                    savingsPercent = data.savings_percent,
                                    savingsBytes = data.saved_bytes;
                            var withoutLastChunk = savingsPercent.slice(0, savingsPercent.lastIndexOf("%"));
                            if (withoutLastChunk > 0) {
                                $compressedSizeColumn.text(compressedSize);
                                $savingsPercentColumn.text(savingsPercent);
                                $savingsBytesColumn.text(savingsBytes);
                            } else {
                                $compressedSizeColumn.text('No more compression');
                                $savingsPercentColumn.text('0%');
                                $savingsBytesColumn.text('0 kb');
                            }

                            var $button = $("button[id='wpimageid-" + id + "']"),
                                    $parent = $button.parent(),
                                    $cell = $button.closest("td"),
                                    $originalSizeColumn = $button.parent().prev("td.original_size")


                            $parent.fadeOut("fast", function () {
                                $cell.find(".noSavings, .wpimageErrorWrap").remove();
                                if (withoutLastChunk > 0) {
                                    compressedData = '<strong>' + compressedSize + '</strong><br /><small><small>Savings: ' + savingsPercent + '</small>';
                                    if (typeof data.thumbs_data !== 'undefined') {
                                        compressedData += '<br /><small>' + data.thumbs_data.length + ' thumbs optimized</small>';
                                    }
                                } else {
                                    compressedData = '<small>No furhter optimization required</small>';
                                }
                                $(this).parent('td').html(compressedData);
                                $originalSizeColumn.html(originalSize);
                                $parent.remove();
                            });

                        } else if (data.error) {
                            if (data.error === 'This image can not be optimized any further') {
                                $compressedSizeColumn.text('No savings found.');
                            } else {

                            }
                        }

                    })

                    .fail(function () {

                    })

                    .always(function () {
                        $spinner.css({
                            display: "none"
                        });
                        callback();
                    });
        }, 5);

        q.drain = function () {
            $(".wpimage_req_bulk")
                    .removeAttr("disabled")
                    .css({
                        opacity: 1
                    })
                    .text("Done")
                    .unbind("click")
                    .click(function () {
                        $.modal.close();
                    });
        }

        // add some items to the queue (batch-wise)
        q.push(bulkImageData, function (err) {

        });
    };

    $btnApplyBulkAction.add($btnApplyBulkAction2).click(function (e) {
        if ($(this).prev("select").val() === 'wpimage-bulk-lossy') {
            e.preventDefault();
            var bulkImageData = getBulkImageData();
            renderBulkImageSummary(bulkImageData);

            $('.wpimage_req_bulk').click(function (e) {
                e.preventDefault();
                $(this)
                        .attr("disabled", true)
                        .css({
                            opacity: 0.5
                        });
                bulkAction(bulkImageData);
            });
        }
    });

    $(".wpimage_req").click( function(e){
        e.preventDefault();
        var $parent, $button = $(this);
        $parent = $button.parent();
        $parent.find(".wpimageSpinner").css("display", "inline");
        $button.text( "Optimizing image..." ).attr( "disabled", true ).css({ opacity: 0.5 });        
        $.ajax({
            url: ajax_object.ajax_url,
            data: {
                action: 'wpimage_request',
                id: $button.data("id"),
                optimised: $button.data("optimised")
            },
            type: "post",
            dataType: "json",
            timeout: 360000,
            context: $button
        }).done( requestSuccess ).fail( requestFail ).always( requestComplete );
    });
});