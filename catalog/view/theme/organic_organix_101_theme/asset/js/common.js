function getURLVar(t) {
    var e = [],
        o = String(document.location).split("?");
    if (o[1]) {
        var a = o[1].split("&");
        for (i = 0; i < a.length; i++) {
            var n = a[i].split("=");
            n[0] && n[1] && (e[n[0]] = n[1])
        }
        return e[t] ? e[t] : ""
    }
}
$(document).ready(function() {
    function t() {
        $(".product-layout").hasClass("product-list") ? $(".product-layout .badproduct-wrapper").each(function() {
            $(this).find(".badproduct-star-icon").insertAfter($(this).find(".badproduct-title"))
        }) : $(".product-layout").hasClass("product-grid") && $(".product-layout .badproduct-wrapper").each(function() {
            $(this).find(".badproduct-star-icon").insertAfter($(this).find(".badproduct-all-btn"))
        })
    }
    $(".text-danger").each(function() {
        var t = $(this).parent().parent();
        t.hasClass("form-group") && t.addClass("has-error")
    }), $("#form-currency .currency-select").on("click", function(t) {
        t.preventDefault(), $("#form-currency input[name='code']").val($(this).attr("data-name")), $("#form-currency").submit()
    }), $("#form-language .language-select").on("click", function(t) {
        t.preventDefault(), $("#form-language input[name='code']").val($(this).attr("data-name")), $("#form-language").submit()
    }), $("#search input[name='search']").parent().find("button").on("click", function() {
        var t = $("base").attr("href") + "index.php?route=product/search",
            e = $("header #search input[name='search']").val();
        e && (t += "&search=" + encodeURIComponent(e)), location = t
    }), $("#search input[name='search']").on("keydown", function(t) {
        13 == t.keyCode && $("header #search input[name='search']").parent().find("button").trigger("click")
    }), $("#menu .dropdown-menu").each(function() {
        var t = $("#menu").offset(),
            e = $(this).parent().offset().left + $(this).outerWidth() - (t.left + $("#menu").outerWidth());
        e > 0 && $(this).css("margin-left", "-" + (e + 10) + "px")
    }), $("#list-view").click(function() {
        $("#content .product-grid > .clearfix").remove(), $("#content .row > .product-grid").attr("class", "product-layout product-list col-xs-12"), $("#grid-view").removeClass("active"), $("#list-view").addClass("active"), localStorage.setItem("display", "list"), t()
    }), $("#grid-view").click(function() {
        var e = $("#column-right, #column-left").length;
        2 == e ? $("#content .product-list").attr("class", "product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12") : 1 == e ? $("#content .product-list").attr("class", "product-layout product-grid col-lg-4 col-md-4 col-xl-3 col-sm-6 col-xs-12") : $("#content .product-list").attr("class", "product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12"), $("#list-view").removeClass("active"), $("#grid-view").addClass("active"), localStorage.setItem("display", "grid"), t()
    }), "list" == localStorage.getItem("display") ? ($("#list-view").trigger("click"), $("#list-view").addClass("active"), t()) : ($("#grid-view").trigger("click"), $("#grid-view").addClass("active"), t()), $(document).on("keydown", "#collapse-checkout-option input[name='email'], #collapse-checkout-option input[name='password']", function(t) {
        13 == t.keyCode && $("#collapse-checkout-option #button-login").trigger("click")
    }), $("[data-toggle='tooltip']").tooltip({
        container: "body"
    }), $(document).ajaxStop(function() {
        $("[data-toggle='tooltip']").tooltip({
            container: "body"
        })
    })
});
var voucher = {
        add: function() {},
        remove: function(t) {
            $.ajax({
                url: "index.php?route=checkout/cart/remove",
                type: "post",
                data: "key=" + t,
                dataType: "json",
                beforeSend: function() {
                    $(".mainlloader").show()
                },
                complete: function() {
                    $(".mainlloader").hide()
                },
                success: function(t) {
                    var e = new RegExp(/^\d+/).exec(t.total);
                    setTimeout(function() {
                        $("#cartviewid .badshopping-cart-count").html(e)
                    }, 100), "checkout/cart" == getURLVar("route") || "checkout/checkout" == getURLVar("route") ? location = "index.php?route=checkout/cart" : $("#cartviewid > .badul").load("index.php?route=common/cart/info .badul .badli")
                },
                error: function(t, e, o) {
                    alert(o + "\r\n" + t.statusText + "\r\n" + t.responseText)
                }
            })
        }
    },
    cart = {
        add: function(t, e) {
            e = $("#cart_quantity" + t).val(), $.ajax({
                url: "index.php?route=checkout/cart/add",
                type: "post",
                data: "product_id=" + t + "&quantity=" + (void 0 !== e ? e : 1),
                dataType: "json",
                beforeSend: function() {
                    $(".mainlloader").show()
                },
                complete: function() {
                    $(".mainlloader").hide()
                },
                success: function(t) {
                    if ($(".alert-dismissible, .text-danger").remove(), t.redirect && (location = t.redirect), t.success) {
                        var e = "<div class='badcategory-add-to-cart-inner'><div class='badcategory-add-to-cart-img-block'><img src='" + t.image + "'></div><div class='badcategory-add-to-cart-img-content'><h1><a href='" + t.link + "'>" + t.name + "</a></h1><div class='badproduct-price-and-shipping'><span class='price'>" + t.special + "</span><span class='regular-price'>" + t.price + "</span></div></br><a href='" + t.cartlink + "'><div class='badcategory-add-to-cart-button'>View cart</div></a></div></div>";
                        $.fancybox.open([{
                            type: "inline",
                            autoScale: !0,
                            minHeight: 30,
                            minWidth: "40%",
                            content: e
                        }], {
                            padding: 0
                        });
                        var o = new RegExp(/^\d+/).exec(t.total);
                        setTimeout(function() {
                            $("#cartviewid .badshopping-cart-count").html(o)
                        }, 100), $("#cartviewid > .badul").load("index.php?route=common/cart/info .badul .badli")
                    }
                },
                error: function(t, e, o) {
                    alert(o + "\r\n" + t.statusText + "\r\n" + t.responseText)
                }
            })
        },
        update: function(t, e) {
            $.ajax({
                url: "index.php?route=checkout/cart/edit",
                type: "post",
                data: "key=" + t + "&quantity=" + (void 0 !== e ? e : 1),
                dataType: "json",
                beforeSend: function() {
                    $(".mainlloader").show()
                },
                complete: function() {
                    $(".mainlloader").hide()
                },
                success: function(t) {
                    var e = new RegExp(/^\d+/).exec(t.total);
                    setTimeout(function() {
                        $("#cartviewid .badshopping-cart-count").html(e)
                    }, 100), "checkout/cart" == getURLVar("route") || "checkout/checkout" == getURLVar("route") ? location = "index.php?route=checkout/cart" : $("#cartviewid > .badul").load("index.php?route=common/cart/info .badul .badli")
                },
                error: function(t, e, o) {
                    alert(o + "\r\n" + t.statusText + "\r\n" + t.responseText)
                }
            })
        },
        remove: function(t) {
            $.ajax({
                url: "index.php?route=checkout/cart/remove",
                type: "post",
                data: "key=" + t,
                dataType: "json",
                beforeSend: function() {
                    $(".mainlloader").show()
                },
                complete: function() {
                    $(".mainlloader").hide()
                },
                success: function(t) {
                    var e = new RegExp(/^\d+/).exec(t.total);
                    setTimeout(function() {
                        $("#cartviewid .badshopping-cart-count").html(e)
                    }, 100), "checkout/cart" == getURLVar("route") || "checkout/checkout" == getURLVar("route") ? location = "index.php?route=checkout/cart" : $("#cartviewid > .badul").load("index.php?route=common/cart/info .badul .badli")
                },
                error: function(t, e, o) {
                    alert(o + "\r\n" + t.statusText + "\r\n" + t.responseText)
                }
            })
        }
    },
    wishlist = {
        add: function(t) {
            $.ajax({
                url: "index.php?route=account/wishlist/add",
                type: "post",
                data: "product_id=" + t,
                dataType: "json",
                beforeSend: function() {
                    $(".mainlloader").show()
                },
                complete: function() {
                    $(".mainlloader").hide()
                },
                success: function(t) {
                    console.log(t), $(".alert-dismissible").remove(), t.redirect && (location = t.redirect), t.success && $.fancybox.open([{
                        type: "inline",
                        autoScale: !0,
                        minHeight: 30,
                        minWidth: "40%",
                        content: '<p class="fancybox-success badcategory-product-wishlist"> ' + t.success + " </p>"
                    }], {
                        padding: 0
                    }), thenum = t.total.match(/\d+/)[0], $(".cart-wishlist-number").html(thenum), $("#wishlist-total").attr("title", t.total)
                },
                error: function(t, e, o) {
                    alert(o + "\r\n" + t.statusText + "\r\n" + t.responseText)
                }
            })
        },
        remove: function() {}
    },
    compare = {
        add: function(t) {
            $.ajax({
                url: "index.php?route=product/compare/add",
                type: "post",
                data: "product_id=" + t,
                dataType: "json",
                beforeSend: function() {
                    $(".mainlloader").show()
                },
                complete: function() {
                    $(".mainlloader").hide()
                },
                success: function(t) {
                    $(".alert-dismissible").remove(), t.success && ($.fancybox.open([{
                        type: "inline",
                        autoScale: !0,
                        minHeight: 30,
                        minWidth: "40%",
                        content: '<p class="fancybox-success badcategory-product-compare"> ' + t.success + " </p>"
                    }], {
                        padding: 0
                    }), thenum = t.total.match(/\d+/)[0], $(".count-product").html(thenum))
                },
                error: function(t, e, o) {
                    alert(o + "\r\n" + t.statusText + "\r\n" + t.responseText)
                }
            })
        },
        remove: function() {}
    };
$(document).delegate(".agree", "click", function(t) {
        t.preventDefault(), $("#modal-agree").remove();
        var e = this;
        $.ajax({
            url: $(e).attr("href"),
            type: "get",
            dataType: "html",
            success: function(t) {
                html = '<div id="modal-agree" class="modal">', html += '  <div class="modal-dialog">', html += '    <div class="modal-content">', html += '      <div class="modal-header">', html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>', html += '        <h4 class="modal-title">' + $(e).text() + "</h4>", html += "      </div>", html += '      <div class="modal-body">' + t + "</div>", html += "    </div>", html += "  </div>", html += "</div>", $("body").append(html), $("#modal-agree").modal("show")
            }
        })
    }),
    function(t) {
        t.fn.autocomplete = function(e) {
            return this.each(function() {
                this.timer = null, this.items = new Array, t.extend(this, e), t(this).attr("autocomplete", "off"), t(this).on("focus", function() {
                    this.request()
                }), t(this).on("blur", function() {
                    setTimeout(function(t) {
                        t.hide()
                    }, 200, this)
                }), t(this).on("keydown", function(t) {
                    switch (t.keyCode) {
                        case 27:
                            this.hide();
                            break;
                        default:
                            this.request()
                    }
                }), this.click = function(e) {
                    e.preventDefault(), value = t(e.target).parent().attr("data-value"), value && this.items[value] && this.select(this.items[value])
                }, this.show = function() {
                    var e = t(this).position();
                    t(this).siblings("ul.dropdown-menu").css({
                        top: e.top + t(this).outerHeight(),
                        left: e.left
                    }), t(this).siblings("ul.dropdown-menu").show()
                }, this.hide = function() {
                    t(this).siblings("ul.dropdown-menu").hide()
                }, this.request = function() {
                    clearTimeout(this.timer), this.timer = setTimeout(function(e) {
                        e.source(t(e).val(), t.proxy(e.response, e))
                    }, 200, this)
                }, this.response = function(e) {
                    if (html = "", e.length) {
                        for (i = 0; i < e.length; i++) this.items[e[i].value] = e[i];
                        for (i = 0; i < e.length; i++) e[i].category || (html += '<li data-value="' + e[i].value + '"><a href="#">' + e[i].label + "</a></li>");
                        var o = new Array;
                        for (i = 0; i < e.length; i++) e[i].category && (o[e[i].category] || (o[e[i].category] = new Array, o[e[i].category].name = e[i].category, o[e[i].category].item = new Array), o[e[i].category].item.push(e[i]));
                        for (i in o)
                            for (html += '<li class="dropdown-header">' + o[i].name + "</li>", j = 0; j < o[i].item.length; j++) html += '<li data-value="' + o[i].item[j].value + '"><a href="#">&nbsp;&nbsp;&nbsp;' + o[i].item[j].label + "</a></li>"
                    }
                    html ? this.show() : this.hide(), t(this).siblings("ul.dropdown-menu").html(html)
                }, t(this).after('<ul class="dropdown-menu"></ul>'), t(this).siblings("ul.dropdown-menu").delegate("a", "click", t.proxy(this.click, this))
            })
        }
    }(window.jQuery);