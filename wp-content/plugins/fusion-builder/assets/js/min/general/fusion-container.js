function setCorrectResizeValuesForScrollSections(){var e=jQuery("#content").find(".fusion-scroll-section"),n=0,i=(n=0,0),t=0;e.length&&(jQuery(".fusion-scroll-section.active").find(".fusion-scroll-section-element").css({left:jQuery("#content").offset().left}),jQuery(".fusion-scroll-section").find(".fusion-scroll-section-element").css({width:jQuery("#content").width()}),0==fusionContainerVars.container_hundred_percent_height_mobile&&(Modernizr.mq("only screen and (max-width: "+fusionContainerVars.content_break_point+"px)")?(jQuery(".fusion-scroll-section").removeClass("active").addClass("fusion-scroll-section-mobile-disabled"),jQuery(".fusion-scroll-section").attr("style",""),jQuery(".fusion-scroll-section").find(".fusion-scroll-section-element").attr("style",""),jQuery(".fusion-scroll-section").find(".hundred-percent-height-scrolling").css("height","auto"),jQuery(".fusion-scroll-section").find(".fusion-fullwidth-center-content").css("height","auto")):jQuery(".fusion-scroll-section").hasClass("fusion-scroll-section-mobile-disabled")&&(jQuery(".fusion-scroll-section").find(".fusion-fullwidth-center-content").css("height",""),Boolean(Number(fusionContainerVars.is_sticky_header_transparent))||"function"!=typeof getStickyHeaderHeight||(n=getStickyHeaderHeight(!0)),jQuery("#wpadminbar").length&&(t=parseInt(jQuery("#wpadminbar").height(),10)),i=n+t,e.each(function(){1<jQuery(this).children("div").length&&(jQuery(this).css("height",100*jQuery(this).children("div").size()+50+"vh"),jQuery(this).find(".hundred-percent-height-scrolling").css("height","calc(100vh - "+i+"px)"))}),scrollToCurrentScrollSection()))),jQuery(".hundred-percent-height.non-hundred-percent-height-scrolling").length&&(Boolean(Number(fusionContainerVars.is_sticky_header_transparent))||"function"!=typeof getStickyHeaderHeight||(n=getStickyHeaderHeight(!0)),jQuery("#wpadminbar").length&&(t=parseInt(jQuery("#wpadminbar").height(),10)),i=n+t,0==fusionContainerVars.container_hundred_percent_height_mobile&&(Modernizr.mq("only screen and (max-width: "+fusionContainerVars.content_break_point+"px)")?(jQuery(".hundred-percent-height.non-hundred-percent-height-scrolling").css("height","auto"),jQuery(".hundred-percent-height.non-hundred-percent-height-scrolling").find(".fusion-fullwidth-center-content").css("height","auto")):(jQuery(".hundred-percent-height.non-hundred-percent-height-scrolling").css("height","calc(100vh - "+i+"px)"),jQuery(".hundred-percent-height.non-hundred-percent-height-scrolling").find(".fusion-fullwidth-center-content").css("height",""))))}function scrollToCurrentScrollSection(){jQuery(window).scrollTop();var s=Math.ceil(jQuery(window).scrollTop()),e=jQuery(window).height(),o=Math.floor(s+e),n=Boolean(Number(fusionContainerVars.is_sticky_header_transparent))||"function"!=typeof getStickyHeaderHeight?0:getStickyHeaderHeight(!0),i=jQuery("#wpadminbar").length?parseInt(jQuery("#wpadminbar").height(),10):0;s+=n+i,jQuery(".fusion-page-load-link").hasClass("fusion-page.load-scroll-section-link")||jQuery(".fusion-scroll-section").each(function(){var e=jQuery(this),n=Math.ceil(e.offset().top),i=Math.ceil(e.outerHeight()),t=Math.floor(n+i);n<=s&&o<=t&&(e.addClass("active"),jQuery("html, body").animate({scrollTop:n-50},{duration:50,easing:"easeInExpo",complete:function(){jQuery("html, body").animate({scrollTop:n},{duration:50,easing:"easeOutExpo",complete:function(){Modernizr.mq("only screen and (max-width: "+fusionContainerVars.content_break_point+"px)")||jQuery(".fusion-scroll-section").removeClass("fusion-scroll-section-mobile-disabled")}})}}))})}jQuery(window).load(function(){jQuery(".fullwidth-faded").fusionScroller({type:"fading_blur"})}),jQuery(document).ready(function(){Modernizr.mq("only screen and (max-width: "+fusionContainerVars.content_break_point+"px)")&&jQuery(".fullwidth-faded").each(function(){var e=jQuery(this).css("background-image"),n=jQuery(this).css("background-color");jQuery(this).parent().css("background-image",e),jQuery(this).parent().css("background-color",n),jQuery(this).remove()})}),jQuery(window).load(function(){jQuery("#content").find(".fusion-scroll-section").length&&void 0===jQuery(".fusion-page-load-link").attr("href")&&setTimeout(function(){scrollToCurrentScrollSection()},400)}),jQuery(document).ready(function(){var n,i,e,t=jQuery("#content").find(".fusion-scroll-section"),s=(Boolean(Number(fusionContainerVars.is_sticky_header_transparent))||"function"!=typeof getStickyHeaderHeight?0:getStickyHeaderHeight(!0))+(jQuery("#wpadminbar").length?parseInt(jQuery("#wpadminbar").height(),10):0);t.length&&(jQuery("#content").find(".non-hundred-percent-height-scrolling").length||1!==t.length||jQuery.trim(jQuery("#sliders-container").html())||(t.addClass("active"),t.find(".fusion-scroll-section-nav li:first a").addClass("active"),i=!0),t.each(function(){1<jQuery(this).children("div").length&&(e=s?"calc("+(100*jQuery(this).children("div").size()+50)+"vh - "+s+"px)":100*jQuery(this).children("div").size()+50+"vh",jQuery(this).css("height",e),s&&(jQuery(this).find(".hundred-percent-height-scrolling").css("height","calc(100vh - "+s+"px)"),jQuery(this).find(".fusion-scroll-section-nav").css("top","calc(50% + "+s/2+"px)")))}),n=jQuery(window).scrollTop(),jQuery(window).scroll(function(){var e=jQuery(window).scrollTop();jQuery(".fusion-scroll-section").each(function(){1<jQuery(this).children("div").length&&!jQuery(this).hasClass("fusion-scroll-section-mobile-disabled")&&jQuery(this).fusionPositionScrollSectionElements(n,e,i)}),n=e}),jQuery(".fusion-scroll-section-link").on("click",function(e){var n=jQuery(this).parents(".fusion-scroll-section"),i=parseInt(jQuery(this).parents(".fusion-scroll-section-nav").find(".fusion-scroll-section-link.active").data("element"),10),t=parseInt(jQuery(this).data("element"),10),s=Math.abs(t-i),o=(350+30*(s-1))*s;e.preventDefault(),0!==s&&(20<s&&(o=950*s),jQuery(this).parents(".fusion-scroll-section").find(".fusion-scroll-section-element").removeClass("active"),jQuery("html, body").animate({scrollTop:Math.ceil(n.offset().top)+jQuery(window).height()*(jQuery(this).data("element")-1)},o,"linear"))})),jQuery(".hundred-percent-height").length&&(setCorrectResizeValuesForScrollSections(),jQuery(window).on("resize",function(){setCorrectResizeValuesForScrollSections()}))}),function(g){"use strict";g.fn.fusionPositionScrollSectionElements=function(e,n,i){var t,s,o,l,r=g(this),c=Math.ceil(r.offset().top),a=Math.ceil(r.outerHeight()),d=Math.floor(c+a),u=Math.ceil(g(window).scrollTop()),h=g(window).height(),f=Math.floor(u+h),y=r.find(".fusion-scroll-section-element").length,p=0;if(i=i||!1,s=g("#wpadminbar").length?parseInt(g("#wpadminbar").height(),10):0,u+=s+=Boolean(Number(fusionContainerVars.is_sticky_header_transparent))||"function"!=typeof getStickyHeaderHeight?0:getStickyHeaderHeight(!0),t=g("#content").width(),o=g("#content").offset().left,"0",i||(c<=u&&f<=d?r.addClass("active"):r.removeClass("active")),e<n){for(l=1;l<y;l++)c+h*l<=u&&u<c+h*(l+1)&&(p=l+1);c<=u&&u<c+h?(r.find(".fusion-scroll-section-element").removeClass("active"),r.children(":nth-child(1)").addClass("active"),r.find(".fusion-scroll-section-nav a").removeClass("active"),r.find('.fusion-scroll-section-nav a[data-element="'+r.children(":nth-child(1)").data("element")+'"] ').addClass("active"),r.find(".fusion-scroll-section-element").css({position:"fixed",top:s,left:o,padding:"0 0",width:t}),r.children(":nth-child(1)").css("display","block")):d<=f&&"absolute"!==r.find(".fusion-scroll-section-element").last().css("position")?(r.find(".fusion-scroll-section-element").removeClass("active"),r.find(".fusion-scroll-section-element").last().addClass("active"),r.find(".fusion-scroll-section-element").css("position","absolute"),r.find(".fusion-scroll-section-element").last().css({top:"auto",left:"0",bottom:"0",padding:""})):0<p&&!r.children(":nth-child("+p+")").hasClass("active")&&(r.find(".fusion-scroll-section-element").removeClass("active"),r.children(":nth-child("+p+")").addClass("active"),r.find(".fusion-scroll-section-nav a").removeClass("active"),r.find('.fusion-scroll-section-nav a[data-element="'+r.children(":nth-child("+p+")").data("element")+'"] ').addClass("active"))}else if(n<e){for(l=1;l<y;l++)u<c+h*l&&c+h*(l-1)<u&&(p=l);f<=d&&c+h*(y-1)<u&&"fixed"!==r.find(".fusion-scroll-section-element").last().css("position")?(r.find(".fusion-scroll-section-element").removeClass("active"),r.find(".fusion-scroll-section-element").last().addClass("active"),r.find(".fusion-scroll-section-nav a").removeClass("active"),r.find('.fusion-scroll-section-nav a[data-element="'+r.find(".fusion-scroll-section-element").last().data("element")+'"] ').addClass("active"),r.find(".fusion-scroll-section-element").css({position:"fixed",top:s,left:o,padding:"0 0",width:t}),r.find(".fusion-scroll-section-element").last().css("display","block")):(u<=c||0===g(window).scrollTop()&&r.find(".fusion-scroll-section-element").first().hasClass("active"))&&""!==r.find(".fusion-scroll-section-element").first().css("position")?(r.find(".fusion-scroll-section-element").removeClass("active"),r.find(".fusion-scroll-section-element").first().addClass("active"),r.find(".fusion-scroll-section-element").css("position",""),r.find(".fusion-scroll-section-element").first().css("padding","")):0<p&&!r.children(":nth-child("+p+")").hasClass("active")&&(r.find(".fusion-scroll-section-element").removeClass("active"),r.children(":nth-child("+p+")").addClass("active"),r.find(".fusion-scroll-section-nav a").removeClass("active"),r.find('.fusion-scroll-section-nav a[data-element="'+r.children(":nth-child("+p+")").data("element")+'"] ').addClass("active"))}}}(jQuery);