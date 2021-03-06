! function(b) {
    function k() {
        var a = this,
            c = setTimeout(function() {
                a.$element.off(b.support.transition.end);
                g.call(a)
            }, 500);
        this.$element.one(b.support.transition.end, function() {
            clearTimeout(c);
            g.call(a)
        })
    }

    function g() {
        this.$element.hide().trigger("hidden");
        h.call(this)
    }

    function h(a) {
        var c = this.$element.hasClass("fade") ? "fade" : "";
        if (this.isShown && this.options.backdrop) {
            var d = b.support.transition && c;
            this.$backdrop = b('<div id="sjekk" class="modal-backdrop ' + c + '" />').appendTo(document.body);
            "static" != this.options.backdrop &&
                this.$backdrop.click(b.proxy(this.hide, this));
            d && this.$backdrop[0].offsetWidth;
            this.$backdrop.addClass("in");
            d ? this.$backdrop.one(b.support.transition.end, a) : a()
        } else !this.isShown && this.$backdrop ? (this.$backdrop.removeClass("in"), b.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one(b.support.transition.end, b.proxy(i, this)) : i.call(this)) : a && a()
    }

    function i() {
        this.$backdrop.remove();
        this.$backdrop = null
    }

    function j() {
        var a = this;
        if (this.isShown && this.options.keyboard) b(document).on("keyup.dismiss.modal",
            function(b) {
                27 == b.which && a.hide()
            });
        else this.isShown || b(document).off("keyup.dismiss.modal")
    }
    var f = function(a, c) {
        this.options = c;
        this.$element = b(a).delegate('[data-dismiss="modal"]', "click.dismiss.modal", b.proxy(this.hide, this))
    };
    f.prototype = {
        constructor: f,
        toggle: function() {
            return this[!this.isShown ? "show" : "hide"]()
        },
        show: function() {
            var a = this;
            this.isShown || (b("body").addClass("modal-open"), this.isShown = !0, this.$element.trigger("show"), j.call(this), h.call(this, function() {
                var c = b.support.transition &&
                    a.$element.hasClass("fade");
                !a.$element.parent().length && a.$element.appendTo(document.body);
                a.$element.show();
                c && a.$element[0].offsetWidth;
                a.$element.addClass("in");
                c ? a.$element.one(b.support.transition.end, function() {
                    a.$element.trigger("shown")
                }) : a.$element.trigger("shown")
            }))
        },
        hide: function(a) {
            a && a.preventDefault();
            this.isShown && (this.isShown = !1, b("body").removeClass("modal-open"), j.call(this), this.$element.trigger("hide").removeClass("in"), b.support.transition && this.$element.hasClass("fade") ? k.call(this) :
                g.call(this))
        }
    };
    b.fn.modal = function(a) {
        return this.each(function() {
            var c = b(this),
                d = c.data("modal"),
                e = b.extend({}, b.fn.modal.defaults, c.data(), "object" == typeof a && a);
            d || c.data("modal", d = new f(this, e));
            if ("string" == typeof a) d[a]();
            else e.show && d.show()
        })
    };
    b.fn.modal.defaults = {
        backdrop: !0,
        keyboard: !0,
        show: !0
    };
    b.fn.modal.Constructor = f;
    b(function() {
        b("body").on("click.modal.data-api", '[data-toggle="modal"]', function(a) {
            var c = b(this),
                d, e = b(c.attr("data-target") || (d = c.attr("href")) && d.replace(/.*(?=#[^\s]+$)/,
                    "")),
                c = e.data("modal") ? "toggle" : b.extend({}, e.data(), c.data());
            a.preventDefault();
            e.modal(c)
        })
    })
}(window.jQuery);