jQuery.each(param_obj, function (index, value) {
    if (!isNaN(value)) {
        param_obj[index] = parseInt(value);
    }
});
function Gallery_Img_Lightbox_Gallery(id) {
    var _this = this;
    _this.body = jQuery('body');
    _this.container = jQuery('#' + id + '.view-lightbox-gallery');
    _this.content = _this.container.parent();
    _this.element = _this.container.find('.element');
    _this.defaultBlockWidth = param_obj.gallery_img_ht_view6_width;
    _this.isCentered = _this.container.data("show-center") == "on";
    _this.ratingType = _this.content.data('rating-type');
    _this.likeContent = jQuery('.huge_it_gallery_like_cont');
    _this.likeCountContainer = jQuery('.huge_it_like_count');
    _this.loadMoreBtn = _this.content.find('.load_more_button4');
    _this.loadingIcon = _this.content.find('.loading4');
    _this.documentReady = function () {
        var options = {
            itemSelector: _this.element,
            masonry: {
                columnWidth: _this.defaultBlockWidth + 10 + param_obj.gallery_img_ht_view6_border_width * 2,
            },
            masonryHorizontal: {
                rowHeight: 300 + 20
            },
            cellsByRow: {
                columnWidth: 300 + 20,
                rowHeight: 'auto'
            },
            cellsByColumn: {
                columnWidth: 300 + 20,
                rowHeight: 'auto'
            },
            getSortData: {
                symbol: function ($elem) {
                    return $elem.attr('data-symbol');
                },
                category: function ($elem) {
                    return $elem.attr('data-category');
                },
                number: function ($elem) {
                    return parseInt($elem.find('.number').text(), 10);
                },
                weight: function ($elem) {
                    return parseFloat($elem.find('.weight').text().replace(/[\(\)]/g, ''));
                },
                id: function ($elem) {
                    return $elem.find('.id').text();
                }
            }
        };
        galleryImgIsotope(_this.container.children().first());
		var loadInterval = setInterval(function(){
			galleryImgIsotope(_this.container.children().first(),options);
			},100);
		setTimeout(function(){clearInterval(loadInterval);},7000);
		
		
        galleryImgRatingCountsOptimize(_this.container,_this.ratingType);
    };
    _this.showCenter = function () {
        if (_this.isCentered) {
            var count = _this.element.length;
            var elementWidth = _this.defaultBlockWidth + 10 + param_obj.gallery_img_ht_view6_border_width * 2;
            var enteryContent = _this.content.width();
            var whole = ~~(enteryContent / (elementWidth));
            if (whole > count) whole = count;
            if (whole == 0) {
                return false;
            }
            else {
                var sectionWidth = whole * elementWidth + (whole - 1) * 20;
            }
            _this.container.children().first().css({
                "width": sectionWidth,
                "max-width": "100%",
                "margin": "0px auto",
                "overflow": "hidden"
            });
            setInterval(function () {
                galleryImgIsotope(_this.container.children().first(),'layout');
            });
        }
    };


    _this.addEventListeners = function () {
        _this.loadMoreBtn.on('click', _this.loadMoreBtnClick);
        jQuery(window).resize(_this.resizeEvent);
    };
    _this.resizeEvent = function () {
        galleryImgIsotope(_this.container.children().first(),'layout');
        _this.showCenter();

    };
    _this.loadMoreBtnClick = function () {
        var lightboxLoadNonce = jQuery(this).attr('data-lightbox-nonce-value');
        if (parseInt(_this.content.find(".pagenum:last").val()) < parseInt(_this.container.find("#total").val())) {
            var pagenum = parseInt(_this.content.find(".pagenum:last").val()) + 1;
            var perpage = _this.content.attr('data-content-per-page');
            var galleryid = _this.content.attr('data-gallery-id');
            var pID = postID;
            var likeStyle = _this.ratingType;
            var ratingCount = param_obj.gallery_img_ht_lightbox_rating_count;
            _this.getResult(pagenum, perpage, galleryid, pID, likeStyle, ratingCount, lightboxLoadNonce);
        } else {
            _this.loadMoreBtn.hide();
        }
        return false;
    };
    _this.getResult = function (pagenum, perpage, galleryid, pID, likeStyle, ratingCount, lightboxLoadNonce) {
        var data = {
            action: "huge_it_gallery_ajax",
            task: 'load_images_lightbox',
            page: pagenum,
            perpage: perpage,
            galleryid: galleryid,
            pID: pID,
            likeStyle: likeStyle,
            ratingCount: ratingCount,
            galleryImgLightboxLoadNonce:lightboxLoadNonce
        };
        _this.loadingIcon.show();
        _this.loadMoreBtn.hide();
        jQuery.post(adminUrl, data, function (response) {
                if (response.success) {
                    var $objnewitems = jQuery(response.success);
                    _this.container.children().first().append($objnewitems);
                    _this.container.children().find('img').on('load', function () {
                        setTimeout(function(){
							var options2 = {
								itemSelector: '.element',
								masonry: {
									columnWidth: _this.defaultBlockWidth + 10 + param_obj.gallery_img_ht_view6_border_width * 2,
								},
								masonryHorizontal: {
									rowHeight: 300 + 20 +  + param_obj.gallery_img_ht_view6_border_width * 2
								},
								cellsByRow: {
									columnWidth: 300 + 20,
									rowHeight: 'auto'
								},
								cellsByColumn: {
									columnWidth: 300 + 20,
									rowHeight: 'auto'
								},
								getSortData: {
									symbol: function ($elem) {
										return $elem.attr('data-symbol');
									},
									category: function ($elem) {
										return $elem.attr('data-category');
									},
									number: function ($elem) {
										return parseInt($elem.find('.number').text(), 10);
									},
									weight: function ($elem) {
										return parseFloat($elem.find('.weight').text().replace(/[\(\)]/g, ''));
									},
									id: function ($elem) {
										return $elem.find('.id').text();
									}
								}
							};
                            galleryImgIsotope(_this.container.children().first());
							galleryImgIsotope(_this.container.children().first(),options2);
							galleryImgIsotope(_this.container.children().first(),'reloadItems');
							galleryImgIsotope(_this.container.children().first(),{sortBy: 'original-order'});
							galleryImgIsotope(_this.container.children().first(),'layout');
						},50);
                        if (_this.isCentered) {
                            _this.showCenter();
                        }
                    });
                    _this.loadMoreBtn.show();
                    _this.loadingIcon.hide();
                    if (_this.content.find(".pagenum:last").val() == _this.content.find("#total").val()) {
                        _this.loadMoreBtn.hide();
                    }
                    galleryImglightboxInit();
                    galleryImgRatingCountsOptimize(_this.container,_this.ratingType);
                } else {
                    alert("no");
                }
            }
            , "json");
    };
    _this.init = function () {
        _this.showCenter();
        _this.documentReady();
        _this.addEventListeners();
    };

    this.init();
}
var galleries = [];
jQuery(document).ready(function () {
    jQuery(".huge_it_gallery_container.view-lightbox-gallery").each(function (i) {
        var id = jQuery(this).attr('id');
        galleries[i] = new Gallery_Img_Lightbox_Gallery(id);
    });
});

