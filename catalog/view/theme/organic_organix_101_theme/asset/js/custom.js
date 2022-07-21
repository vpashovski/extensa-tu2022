/*custom.js*/
var mobileView = 991;

function datetimeToTimestamp(e) {
    if (!1 === /^[0-9]{4}-(?:[0]?[0-9]{1}|10|11|12)-(?:[012]?[0-9]{1}|30|31)(?: (?:[01]?[0-9]{1}|20|21|22|23)(?::[0-5]?[0-9]{1})?(?::[0-5]?[0-9]{1})?)?$/.test(e)) throw "Wrong format for the param. `Y-m-d H:i:s` expected.";
    var t = e.split(" "),
        o = t[0].split("-"),
        n = t[1].split(":"),
        l = new Date;
    return l.setUTCFullYear(o[0], o[1] - 1, o[2]), l.setUTCHours(n[0], n[1], n[2], 0), l.getTime()
}
$(window).load(function() {
    $(".badpage-loader-wrapper").fadeOut(600)
}), setInterval(function() {
    $(".badtimer-wrapper .badtimer-content-box").each(function() {
        $time = $(this).attr("data-datetime"), $time += " 00:00:00";
        var e = datetimeToTimestamp($time) - (new Date).getTime(),
            t = Math.floor(e / 864e5),
            o = Math.floor(e % 864e5 / 36e5),
            n = Math.floor(e % 36e5 / 6e4),
            l = Math.floor(e % 6e4 / 1e3);
        $(this).find(".badtimer-content-days-time").html(t), $(this).find(".badtimer-content-hours-time").html(o), $(this).find(".badtimer-content-minutes-time").html(n), $(this).find(".badtimer-content-secounds-time").html(l), e < 0 && (clearInterval(x), document.getElementById("demo").innerHTML = "EXPIRED")
    })
}, 1e3), $(window).load(function() {
    $(".badpage-loader-wrapper").fadeOut(600)
}), $(document).ready(function() {
    for (var e = [
            [".badnew-products-block", ".badnew-product-list-inner", ".badnew-product-prev-btn", ".badnew-product-next-btn"]
        ], t = 0; t < e.length; t++) {
        var o = e[t][0].toString() + " " + e[t][1].toString();
        $(o).owlCarousel({
            loop: !1,
            nav: !1,
            responsive: {
                0: {
                    items: 1,
                    slideBy: 1
                },
                320: {
                    items: 1,
                    slideBy: 1
                },
                575: {
                    items: 1,
                    slideBy: 1
                },
                600: {
                    items: 2,
                    slideBy: 1
                },
                767: {
                    items: 2,
                    slideBy: 1
                },
                991: {
                    items: 2,
                    slideBy: 1
                },
                1024: {
                    items: 2,
                    slideBy: 1
                },
                1200: {
                    items: 2,
                    slideBy: 1
                },
                1540: {
                    items: 2,
                    slideBy: 1
                },
                1920: {
                    items: 2,
                    slideBy: 1
                },
                2240: {
                    items: 2,
                    slideBy: 1
                }
            }
        });
        var n = e[t][0] + " " + e[t][2];
        $(document).on("click", n, function(e) {
            $("." + $(this).attr("data-parent") + " .owl-nav .owl-prev").trigger("click")
        });
        var l = e[t][0] + " " + e[t][3];
        $(document).on("click", l, function(e) {
            $("." + $(this).attr("data-parent") + " .owl-nav .owl-next").trigger("click")
        })
    }
    for (e = [
            [".badfeatured-products-block", ".badfeatured-product-list-inner", ".badfeatured-product-prev-btn", ".badfeatured-product-next-btn"],
            [".badbestseller-products-block", ".badbestseller-product-list-inner", ".badbestseller-product-prev-btn", ".badbestseller-product-next-btn"]
        ], t = 0; t < e.length; t++) {
        o = e[t][0].toString() + " " + e[t][1].toString();
        $(o).owlCarousel({
            loop: !1,
            nav: !1,
            responsive: {
                0: {
                    items: 1,
                    slideBy: 1
                },
                320: {
                    items: 1,
                    slideBy: 1
                },
                575: {
                    items: 2,
                    slideBy: 1
                },
                767: {
                    items: 3,
                    slideBy: 1
                },
                991: {
                    items: 3,
                    slideBy: 1
                },
                1024: {
                    items: 4,
                    slideBy: 1
                },
                1200: {
                    items: 4,
                    slideBy: 1
                },
                1540: {
                    items: 4,
                    slideBy: 1
                },
                1920: {
                    items: 4,
                    slideBy: 1
                },
                2240: {
                    items: 4,
                    slideBy: 1
                }
            }
        });
        n = e[t][0] + " " + e[t][2];
        $(document).on("click", n, function(e) {
            $("." + $(this).attr("data-parent") + " .owl-nav .owl-prev").trigger("click")
        });
        l = e[t][0] + " " + e[t][3];
        $(document).on("click", l, function(e) {
            $("." + $(this).attr("data-parent") + " .owl-nav .owl-next").trigger("click")
        })
    }

    function i() {/*
        $(window).width() <= mobileView && ($(".badmobile-menu-icon").removeClass("open"), $(".badmain-menu-block .badmain-menu-dropdown").removeClass("open"))
    */}

    function a() {
    }
    $(window).on("resize", function() {
        $(window).width() > mobileView && $(".badmain-menu-dropdown .badmain-menu-get-child, .badmain-menu-dropdown .badsub-menu-get-child").removeClass("open").removeAttr("style")
    }), $(document).on("click", ".badmain-menu-icon", function(e) {
        e.preventDefault();
        var t = $(this).attr("data-dropdown");
        $(".badmain-menu-dropdown " + t).hasClass("open") ? ($(this).removeClass("open"), console.log($(".badmain-menu-dropdown " + t)), $(".badmain-menu-dropdown " + t).removeClass("open").slideUp(500)) : ($(this).addClass("open"), $(".badmain-menu-dropdown " + t).addClass("open").slideDown(500)), e.stopPropagation()
    }), $(document).click(function() {
        i()
    }), $(".badmobile-menu-icon").click(function(e) {
        $(window).width() <= mobileView && (e.preventDefault(), $(".badmain-menu-content").hasClass("open") ? ($(this).removeClass("open"), $(".badmain-menu-content").removeClass("open")) : ($(this).addClass("open"), $(".badmain-menu-content").addClass("open"),
            e.stopPropagation()))
    }), a(), $(window).resize(function() {
        a()
    }), $(document).on("click", ".badfooter-top-to-bottom-block.badfooter-top-to-bottom-block a", function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600)
    }), $(window).on("scroll load", function() {
        $(document).scrollTop() <= 400 ? $(".badfooter-top-to-bottom-block").hide(400) : $(".badfooter-top-to-bottom-block").show(400)
    }), $(".badshopping-cart-block .badshopping-cart-content a").click(function(e) {
        $(window).width() <= mobileView && (e.preventDefault(), i(), $(".badshopping-cart-block .badshopping-cart-content").hasClass("open") ? $(".badshopping-cart-block .badshopping-cart-content").removeClass("open") : ($(".badshopping-cart-block .badshopping-cart-content").addClass("open"), e.stopPropagation()))
    }), $(document).on("mouseenter", ".badshopping-cart-block .badshopping-cart-content", function() {
        $(window).width() > mobileView && $(this).addClass("open")
    }), $(document).on("mouseleave", ".badshopping-cart-block .badshopping-cart-content", function() {
        $(window).width() > mobileView && $(this).removeClass("open")
    });
    var m = [
        [".badlike-products-block", ".badlike-product-list-inner", ".badlike-product-prev-btn", ".badlike-product-next-btn"],
        [".badcross-selling-products-block", ".badcross-selling-product-list-inner", ".badcross-selling-product-prev-btn", ".badcross-selling-product-next-btn"],
        [".badcategory-products-block", ".badcategory-product-list-inner", ".badcategory-product-prev-btn", ".badcategory-product-next-btn"],
        [".badviewed-products-block", ".badviewed-product-list-inner", ".badviewed-product-prev-btn", ".badviewed-product-next-btn"]
    ];
    for (t = 0; t < m.length; t++) {
        var d = m[t][0].toString() + " " + m[t][1].toString();
        $(d).owlCarousel({
            loop: !1,
            nav: !1,
            responsive: {
                0: {
                    items: 1,
                    slideBy: 1
                },
                320: {
                    items: 1,
                    slideBy: 1
                },
                575: {
                    items: 2,
                    slideBy: 1
                },
                767: {
                    items: 3,
                    slideBy: 1
                },
                991: {
                    items: 3,
                    slideBy: 1
                },
                1024: {
                    items: 4,
                    slideBy: 1
                },
                1200: {
                    items: 4,
                    slideBy: 1
                },
                1540: {
                    items: 4,
                    slideBy: 1
                },
                1920: {
                    items: 4,
                    slideBy: 1
                },
                2240: {
                    items: 4,
                    slideBy: 1
                }
            }
        });
        n = m[t][0] + " " + m[t][2];
        $(document).on("click", n, function(e) {
            $("." + $(this).attr("data-parent") + " .owl-nav .owl-prev").trigger("click")
        });
        l = m[t][0] + " " + m[t][3];
        $(document).on("click", l, function(e) {
            $("." + $(this).attr("data-parent") + " .owl-nav .owl-next").trigger("click")
        })
    }

    function s() {
        document.body.clientWidth > 1199 ? ($("#column-left").insertBefore("#content"), $("#column-left").insertBefore("#content-wrapper")) : ($("#column-left").insertAfter("#content"), $("#column-left").insertAfter("#content-wrapper"))
    }

    function c() {
         if (document.body.clientWidth > 575) {
            $("#badmobile-header-center").insertAfter("#badmobile-header-left");
         }else{
            console.log("mobile");
            $("#badmobile-header-left").insertAfter("#badmobile-header-center");
         }
        // if (document.body.clientWidth > 991) {
        //     var e = $(".badmain-menu-content ul#top-menu .badmain-menu-item").length,
        //         t = e / 2,
        //         o = (parseInt(t), 0);
        //     null != $(".badmain-menu-content ul#top-menu .badmain-menu-left-items").html() && null != $(".badmain-menu-content ul#top-menu .badmain-menu-right-items").html() || ($(".badmain-menu-content ul#top-menu").append("<div class='badmain-menu-left-items '></div><div class='badmain-menu-right-items'></div>"), $(".badmain-menu-content ul#top-menu > li.badmain-menu-item").each(function() {
        //         o < t ? $(".badmain-menu-content ul#top-menu .badmain-menu-left-items").append($(this)) : $(".badmain-menu-content ul#top-menu .badmain-menu-right-items").append($(this)), o++
        //     }), $("#_desktop_logo").insertAfter($(".badmain-menu-content ul#top-menu .badmain-menu-left-items")))
        // } else $(".badmain-menu-content ul#top-menu .badmain-menu-left-items > li.badmain-menu-item, .badmain-menu-content ul#top-menu .badmain-menu-right-items > li.badmain-menu-item").unwrap()
    }
    $(window).on("scroll", function() {
        if ($(document).scrollTop() >= 400) {
            var e = $(".badmenu-has-sticky").height();
            $("#wrapper").css("margin-top", e + "px"), $(".badmenu-has-sticky").addClass("sticky")
        } else $("#wrapper").css("margin-top", "0px"), $(".badmenu-has-sticky").removeClass("sticky")
    }), $(document).on("click", ".badquickview", function() {
        var e = $(this).attr("data-productid"),
            t = $("#badquickviewhtml");
        t.html(""), 0 != e.length && $.ajax({
            type: "POST",
            url: "index.php?route=common/tntsocialicon/productquickview&product_id=" + encodeURIComponent(e),
            cache: !1,
            beforeSend: function() {
                $(".mainlloader").show()
            },
            complete: function() {
                $(".mainlloader").hide()
            },
            contentType: !1,
            processData: !1,
            success: function(e) {
                t.html(e), $("#badquickview").modal("show")
            },
            error: function(e, t, o) {
                console.log(t, o)
            }
        })
    }), s(), $(window).resize(function() {
        s()
    }), c(), $(window).resize(function() {
        c()
    })



    /* Header Dropdown */

    /* Language Dropdown */
    var btnClickClass = '.badheaderlanguage-block .badheaderlanguage-btn';
    var dropdownClass = '.badheaderlanguage-block .badheaderlanguage-dropdown';
    setCustomDropdown(btnClickClass, dropdownClass, true);
    /* Language Dropdown */

    /* currency Dropdown */
    var btnClickClass = '.badheadercurrency-block .badheadercurrency-btn';
    var dropdownClass = '.badheadercurrency-block .badheadercurrency-dropdown';
    setCustomDropdown(btnClickClass, dropdownClass, true);
    /* currency Dropdown */

    function setCustomDropdown(btnClickClass, dropdownClass, defaultDropdownClose = false) {
        var speed = 400;
        $(document).on('click', btnClickClass,function(){
            
            if ($(dropdownClass).hasClass('open')) {
                $(btnClickClass).removeClass('open');
                $(dropdownClass).removeClass('open').slideUp(speed);
            } else {
                if (defaultDropdownClose == true){
                    $('.dropdown .dropdown-menu').removeClass('active').slideUp(speed);
                }
                $(btnClickClass).addClass('open');
                $(dropdownClass).addClass('open').slideDown(speed);
            }
        }); 
    }

    /* Header Dropdowns */



});

/*************************************start_menu_wrapper**********************************/

$(document).ready(function(){
    getDesktopMobileClassInfo();
    $(window).resize(function(){
        getDesktopMobileClassInfo();
    });
})

/* @Params parentClass :- DesktopClass */
/* @Params childClass :- MobileClass */
/* Must Use Id */
function getDesktopMobileClassInfo(){
    moreMobileViewJs('.badmain-menu-content-box', '#badmobile-horizontal-menu');
    moreMobileViewJs('.badheader-top-center', '#badmobile-header-center');
    moreMobileViewJs('.badheader-top-right', '#badmobile-header-right'); 

    moreMobileViewcallemailJs('.badheader-left-right-contant', '#badmobile-header-call');
}

function moreMobileViewcallemailJs(parentClass, childClass){
    if ($(window).width() <768) {
        changeDivIntoMobile(parentClass, childClass);
    }  else {
        changeDivIntoDesktop(parentClass, childClass);
    }
}
function moreMobileViewJs(parentClass, childClass)
{
    if ($(window).width() <992) {
        changeDivIntoMobile(parentClass, childClass);
    }  else {
        changeDivIntoDesktop(parentClass, childClass);
    }
}

function changeDivIntoMobile(parentClass, childClass) {
    if ($(childClass).html() == "" || $(childClass).html() == null || $(childClass).html() == undefined) {
        $(childClass).html($(parentClass).html());
        $(parentClass).html('');
    }
}

function changeDivIntoDesktop(parentClass, childClass) {
    if ($(parentClass).html() == "" || $(parentClass).html() == null || $(parentClass).html() == undefined) {
        $(parentClass).html($(childClass).html());
        $(childClass).html('');
    }
}


/**************************************end_menu_wrapper**********************************/