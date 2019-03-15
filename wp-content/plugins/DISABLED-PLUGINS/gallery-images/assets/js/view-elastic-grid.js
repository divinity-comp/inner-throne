"use strict";
jQuery.each(param_obj, function (index, value) {
    if (!isNaN(value)) {
        param_obj[index] = parseInt(value);
    }
});

function Gallery_Img_Elastic_Grid(id) {
    var _this = this;
    _this.container = jQuery('#' + id + '.view-elastic-grid');
    _this.content = _this.container.find('#og-grid');
    _this.content = _this.container.parent();
    _this.imageBehaviour = _this.container.data('image-behaviour') == 'crop';
    _this.defaultBlockWidth = param_obj.gallery_img_ht_view10_element_width;
    _this.defaultBlockHeight = param_obj.gallery_img_ht_view10_element_height;
    _this.hoverEffect = param_obj.gallery_img_ht_view10_element_hover_effect == 'true';
    _this.hoverEffectInverse = param_obj.gallery_img_ht_view10_hover_effect_inverse == 'true';
    // receive gallery object index from container data
    // it solves in the one page 2 or more Galleries object localize problem
    _this.index = _this.content.attr('data-image-object-name');
    _this.documentReady = function () {
        jQuery(window).on("elastic-grid:ready",function(){
            _this.container.elastic_grid({
                'showAllText' : param_obj.gallery_img_ht_view10_filter_all_text,
                'filterEffect': param_obj.gallery_img_ht_view10_filter_effect, // moveup, scaleup, fallperspective, fly, flip, helix , popup
                'hoverDirection': _this.hoverEffect,
                'hoverDelay': param_obj.gallery_img_ht_view10_hover_effect_delay,
                'hoverInverse': _this.hoverEffectInverse,
                'expandingSpeed': param_obj.gallery_img_ht_view10_expanding_speed,
                'expandingHeight': param_obj.gallery_img_ht_view10_expand_block_height,
                'items' : window[_this.index]
            });
        });
    };

    _this.manageLoading = function () {
        if (_this.hasLoading) {
            _this.container.css({'opacity': 1});
            _this.optionsBlock.css({'opacity': 1});
            _this.filtersBlock.css({'opacity': 1});
            _this.content.find('div[id^="huge-it-container-loading-overlay_"]').css('display', 'none');
        }
    };

    _this.imageBehaiour = function(){
        _this.content.find('ul#og-grid > li > a > img').each(function(i, img) {
            var naturalRatio = jQuery(this).prop('naturalWidth')/jQuery(this).prop('naturalHeight');
            var defaultRatio = _this.defaultBlockWidth/_this.defaultBlockHeight;
            if(naturalRatio<=defaultRatio){
                jQuery(img).css({
                    position: "relative",
                    width: '100%',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    height: 'auto'
                });
            }else {
                jQuery(img).css({
                    position: "relative",
                    height:'100%',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    width: 'auto'
                });
            }
        });
    };

    _this.addEventListeners = function () {

    };

    _this.init = function () {
        _this.documentReady();
        _this.addEventListeners();
        jQuery(window).load(function () {
            if(_this.imageBehaviour){
                _this.imageBehaiour();
            }
            _this.manageLoading();
            _this.container.find('ul#og-grid > li > a figure > span').each(function () {
                if(!jQuery(this).text()){
                    jQuery(this).css('border','none');
                }
            });
        });
    };

    this.init();
}
var galleries = [];
jQuery(document).ready(function () {
    jQuery(".gallery-img-content.view-elastic-grid").each(function (i) {
        var id = jQuery(this).attr('id');
        galleries[i] = new Gallery_Img_Elastic_Grid(id);
    });
});
