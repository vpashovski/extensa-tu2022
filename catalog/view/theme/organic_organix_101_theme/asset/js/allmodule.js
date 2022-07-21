/*allmodulejs*/
$(document).ready(function() {
       
    $("#_desktop_search input[name='search']").bind("keydown", function(e) {
        13==e.keyCode&&$("#tntlivesearch").trigger("click")
    }
    ), $(document).on("click", "#tntlivesearchmore ,#tntlivesearch", function() {
        var e=$("base").attr("href")+"index.php?route=product/search", t=$("#_desktop_search input[name='search']").val();
        t&&(e+="&search="+encodeURIComponent(t)), location=e
    }
    ), $(document).keyup(function(e) {
        "Escape"===e.key&&$(".badsearch-content-box-wrapper").removeClass("open")
    }
    ), $(".livesearchloader").hide(), $("input[name='search']").keyup(function() {
        var e=$(".search_popup");
        e.html(""), e.show();
        var t=$(this).val();
        0!=t.length&&$.ajax( {
            type:"POST", url:"index.php?route=common/tntsocialicon/autocomplete&filter_name="+encodeURIComponent(t), cache:!1, beforeSend:function() {
                $(".livesearchloader").show()
            }
            , complete:function() {
                $(".livesearchloader").hide()
            }
            , success:function(t) {
                e.html(""), e.append(t)
            }
            , error:function(e, t, i) {
                console.log(t, i)
            }
        }
        )
    }
    )
}
),
$(document).ready(function() {
    var e=$(".badhome-slider-block").attr("data-speed"), t=$(".badhome-slider-block").attr("data-pause"), i=$(".badhome-slider-block").attr("data-wrap"), o=$(".badhome-slider-block").attr("data-navigation"), n=$(".badhome-slider-block").attr("data-pagination");
    $(".badhome-slider-block .badhome-slider-content-wrapper").owlCarousel( {
        loop:i,dots: true,dotsData: true, pagination:n, navigation:o, slideSpeed:e, autoplayHoverPause:t, paginationSpeed:400, responsive: {
            0: {
                items: 1
            }
            , 320: {
                items: 1, slideBy: 1
            }
            , 576: {
                items: 1, slideBy: 1
            }
            , 768: {
                items: 1, slideBy: 1
            }
            , 992: {
                items: 1, slideBy: 1
            }
            , 1200: {
                items: 1, slideBy: 1
            }
            , 1400: {
                items: 1, slideBy: 1
            }
            , 1600: {
                items: 1, slideBy: 1
            }
            , 1800: {
                items: 1, slideBy: 1
            }
        }
    }
    ), $(document).on("click", ".badhome-slider-block .badhome-slider-prev-btn", function() {
        $(".badhome-slider-block").find(".owl-prev").trigger("click")
    }
    ), $(document).on("click", ".badhome-slider-block .badhome-slider-next-btn", function() {
        $(".badhome-slider-block").find(".owl-next").trigger("click")
    }
    )
}
),
$(document).ready(function() {
    $(".fancybox").fancybox( {
        openEffect: "none", closeEffect: "none"
    }
    ), $(".badimagegallery-block .badimagegallery-content-inner").owlCarousel( {
        loop:!0, pagination:!1, navigation:!1, autoplay:!0, autoplayTimeout:5e3, autoplayHoverPause:!0, dots:!0, responsive: {
            0: {
                items: 1
            }
            , 320: {
                items: 1
            }
            , 576: {
                items: 1
            }
            , 768: {
                items: 1
            }
            , 992: {
                items: 1
            }
            , 1200: {
                items: 1
            }
            , 1400: {
                items: 1
            }
            , 1600: {
                items: 1
            }
            , 1800: {
                items: 1
            }
        }
    }
    ), $(document).on("click", ".badimagegallery-block .badimagegallery-prev-btn", function() {
        $(".badimagegallery-block").find(".owl-prev").trigger("click")
    }
    ), $(document).on("click", ".badimagegallery-block .badimagegallery-next-btn", function() {
        $(".badimagegallery-block").find(".owl-next").trigger("click")
    }
    )
for(var homePageProductSlider=[[".badspecial-products-block", ".badspecial-product-list-inner", ".badspecial-product-prev-btn", ".badspecial-product-next-btn"]], i=0;
i<homePageProductSlider.length;
i++) {
    var makehomePageProductSlider=homePageProductSlider[i][0].toString()+" "+homePageProductSlider[i][1].toString();
    $(makehomePageProductSlider).owlCarousel( {
        loop:!1, nav:!1, responsive: {
            0: {
                items: 1, slideBy: 1
            }
            , 320: {
                items: 1, slideBy: 1
            }
            , 575: {
                items: 1, slideBy: 1
            }
            , 767: {
                items: 1, slideBy: 1
            }
            , 991: {
                items: 1, slideBy: 1
            }
            , 1024: {
                items: 1, slideBy: 1
            }
            , 1200: {
                items: 1, slideBy: 1
            }
            , 1540: {
                items: 1, slideBy: 1
            }
            , 1920: {
                items: 1, slideBy: 1
            }
            , 2240: {
                items: 1, slideBy: 1
            }
        }
    }
    );
    var prevBtn=homePageProductSlider[i][0]+" "+homePageProductSlider[i][2];
    $(document).on("click", prevBtn, function(e) {
        $("."+$(this).attr("data-parent")+" .owl-nav .owl-prev").trigger("click")
    }
    );
    var nextBtn=homePageProductSlider[i][0]+" "+homePageProductSlider[i][3];
    $(document).on("click", nextBtn, function(e) {
        $("."+$(this).attr("data-parent")+" .owl-nav .owl-next").trigger("click")
    }
    )
}
}
);
function getCookie(e) {
    for(var t=e+"=", i=decodeURIComponent(document.cookie).split(";"), o=0;
    o<i.length;
    o++) {
        for(var n=i[o];
        " "==n.charAt(0);
        )n=n.substring(1);
        if(0==n.indexOf(t))return n.substring(t.length, n.length)
    }
    return""
}
function countChecked() {
    var e=new Date;
    e.setTime(e.getTime()+864e5);
    var t="expires="+e.toUTCString();
    document.cookie="subscribe=true;"+t+"/"
}
$(document).ready(function() {
    $(".badtestimonials-block .badtestimonials-content-inner").owlCarousel( {
        loop:!0, pagination:!0, navigation:!0,dots: true, responsive: {
            0: {
                items: 1
            }
            , 320: {
                items: 1, slideBy: 1
            }
            , 576: {
                items: 1, slideBy: 1
            }
            , 768: {
                items: 1, slideBy: 1
            }
            , 992: {
                items: 1, slideBy: 1
            }
            , 1200: {
                items: 1, slideBy: 1
            }
            , 1400: {
                items: 1, slideBy: 1
            }
            , 1600: {
                items: 1, slideBy: 1
            }
            , 1800: {
                items: 1, slideBy: 1
            }
        }
    }
    ), $(document).on("click", ".badtestimonials-block .badtestimonials-prev-btn", function() {
        $(".badtestimonials-block").find(".owl-prev").trigger("click")
    }
    ), $(document).on("click", ".badtestimonials-block .badtestimonials-next-btn", function() {
        $(".badtestimonials-block").find(".owl-next").trigger("click")
    }
    )
}
),
$("#bad-newsletter-dont-show-again").on("click", countChecked),
$(document).ready(function() {
    "true"!=getCookie("subscribe")&&$("#badPopupnewsletter").modal( {
        show: !0
    }
    )
}
),
$(document).ready(function() {
$("#badclick").click(function() {
    
    var e=$("#badnewslettr-inpute").val();
   
    $.ajax( {
        url:"index.php?route=extension/module/tntnewsletterpopup/adddata&email="+encodeURIComponent(e), type:"post", dataType:"json", cache:!1, contentType:!1, processData:!1, beforeSend:function() {
            $("#badnewslettr-inpute").button("loading")
        }
        , complete:function() {
            $("#badnewslettr-inpute").button("reset")
        }
        , success:function(e) {
           
            e.text_error_email&&$("#badhtmldiv").html(e.text_error_email), e.text_repeat_email&&$("#badhtmldiv").html(e.text_repeat_email), e.text_enter_email&&$("#badhtmldiv").html(e.text_enter_email), e.text_success_email&&$("#badhtmldiv").html(e.text_success_email)
        }
        , error:function(e, t, i) {
            alert(i+"\r\n"+e.statusText+"\r\n"+e.responseText)
        }
    }
    )
}
)
}
),
$(document).on("click", "#closenewsletter", function() {
    $(this).parent().remove()
}
),
$(".submitNewsletter").click(function() {
    var e=$("#newsletterid").val();
    $.ajax( {
        url:"index.php?route=extension/module/tntnewsletterpopup/adddata&email="+encodeURIComponent(e), type:"post", dataType:"json", cache:!1, contentType:!1, processData:!1, beforeSend:function() {
            $(".homettvnewsletter-email-subscrib").button("loading")
        }
        , complete:function() {
            $(".homettvnewsletter-email-subscrib").button("reset")
        }
        , success:function(e) {
            e.text_error_email&&$("#homemsg").html(e.text_error_email), e.text_repeat_email&&$("#homemsg").html(e.text_repeat_email), e.text_enter_email&&$("#homemsg").html(e.text_enter_email), e.text_success_email&&$("#homemsg").html(e.text_success_email)
        }
        , error:function(e, t, i) {
            alert(i+"\r\n"+e.statusText+"\r\n"+e.responseText)
        }
    }
    )
}
);
 var status_lazy = $("#lazysetting").text();
 if(status_lazy == 1){
    $("img").each(function() {
        "lazynot"!=$(this).attr("class")&&($(this).attr("data-src", $(this).attr("src")), $(this).removeAttr("src"))
    }),
    $(function() {
        $("img").lazy({
            effect: "fadeIn", effectTime: 1e3, threshold: 0
        })
    });
}
$(document).ready(function() {
    /*$(".badcategoryslider-block .badcategoryslider-content-inner").owlCarousel( {
        loop:!1, pagination:!1, navigation:!1, autoplay:!0, autoplayTimeout:5e3, autoplayHoverPause:!0, dots:!1, responsive: {
            0: { items: 1 },
            320: { items: 1, slideBy: 1 },
            576: { items: 2, slideBy: 1 },
            768: { items: 3, slideBy: 1 },
            992: { items: 3, slideBy: 1 },
            1200: { items: 4, slideBy: 1 },
            1400: { items: 5, slideBy: 1 },
            1600: { items: 6, slideBy: 1 },
            1800: { items: 6, slideBy: 1 }
        }
    }
    ), $(document).on("click", ".badcategoryslider-block .badcategoryslider-prev-btn", function() {
        $(".badcategoryslider-block").find(".owl-prev").trigger("click")
    }
    ), $(document).on("click", ".badcategoryslider-block .badcategoryslider-next-btn", function() {
        $(".badcategoryslider-block").find(".owl-next").trigger("click")
    }
    )*/
}
);
$(document).ready(function() {
    $(".badbrandlist-block .badbrandlist-content-inner").owlCarousel( {
        loop:!1, pagination:!1, navigation:!1, autoplay:!1, autoplayTimeout:5e3, dots:!1, responsive: {
            0: {
                items: 1
            }
            , 320: {
                items: 1, slideBy: 1
            }
            , 576: {
                items: 3, slideBy: 1
            }
            , 768: {
                items: 3, slideBy: 1
            }
            , 992: {
                items: 4, slideBy: 1
            }
            , 1200: {
                items: 5, slideBy: 1
            }
            , 1400: {
                items: 5, slideBy: 1
            }
            , 1600: {
                items: 5, slideBy: 1
            }
            , 1800: {
                items: 5, slideBy: 1
            }
        }
    }
    ), $(document).on("click", ".badbrandlist-block .badbrandlist-prev-btn", function() {
        $(".badbrandlist-block").find(".owl-prev").trigger("click")
    }
    ), $(document).on("click", ".badbrandlist-block .badbrandlist-next-btn", function() {
        $(".badbrandlist-block").find(".owl-next").trigger("click")
    }
    )
}
);
$(document).ready(function() {
    $(".badtab-prdoducts-block .badtab-product-content-wrapper").hide(), $(".badtab-prdoducts-block .badtab-product-pagination-dots").hide(), $(".badtab-prdoducts-block .badtab-product-pagination-wrapper").hide(), $(".badtab-prdoducts-block #"+$(".badtab-prdoducts-block ul li a").eq(0).attr("data-product")).show(), $(".badtab-prdoducts-block ."+$(".badtab-prdoducts-block ul li a").eq(0).attr("data-pagin")+"-wrapper").show(), $(".badtab-prdoducts-block ."+$(".badtab-prdoducts-block ul li a").eq(0).attr("data-dots")).show(), $(document).on("click", ".badtab-prdoducts-block ul li a", function() {
        $(".badtab-prdoducts-block ul li").removeClass("active"), $(".badtab-prdoducts-block .badtab-product-content-wrapper").hide(), $(".badtab-prdoducts-block .badtab-product-pagination-dots").hide(), $(".badtab-prdoducts-block .badtab-product-pagination-wrapper").hide(), $(this).parent().addClass("active"), $(".badtab-prdoducts-block #"+$(this).attr("data-product")).show(), $(".badtab-prdoducts-block ."+$(this).attr("data-pagin")+"-wrapper").show(), $(".badtab-prdoducts-block ."+$(this).attr("data-dots")).show()
    }
    );
    for(var t=[
        [".badtab-prdoducts-block", ".badtab-featured-product-list-content", ".badtab-featured-product-pagin-prev-btn", ".badtab-featured-product-pagin-next-btn"], 
        [".badtab-prdoducts-block", ".badtab-new-product-list-content", ".badtab-new-product-pagin-prev-btn", ".badtab-new-product-pagin-next-btn"],
        [".badtab-prdoducts-block", ".badtab-best-product-list-content", ".badtab-best-product-pagin-prev-btn", ".badtab-best-product-pagin-next-btn"],
        [".badtab-prdoducts-block", ".badtab-special-product-list-content", ".badtab-special-product-pagin-prev-btn", ".badtab-special-product-pagin-next-btn"]], a=0;
    a<t.length;
    a++) {
        var d=t[a][0].toString()+" "+t[a][1].toString();
        $(d).owlCarousel( {
            loop:!1, nav:!1, responsive: {
                0:{items:1, slideBy: 1},
                320:{items:1, slideBy: 1},
                575:{items:2, slideBy: 1},
                767:{items:3, slideBy: 1},
                991:{items:3, slideBy: 1},
                1024:{items:5, slideBy: 1},
                1200:{items:5, slideBy: 1},
                1540:{items:5, slideBy: 1},
                1920:{items:5, slideBy: 1},
                2240:{items:6, slideBy: 1},
            }
        }
        );
        var b=t[a][0]+" "+t[a][2];
        $(document).on("click", b, function(t) {
            $("."+$(this).attr("data-parent")+" .owl-nav .owl-prev").trigger("click")
        }
        );
        var o=t[a][0]+" "+t[a][3];
        $(document).on("click", o, function(t) {
            $("."+$(this).attr("data-parent")+" .owl-nav .owl-next").trigger("click")
        }
        )
    }
}
);
$(document).ready(function() {
/*leftmodule*/
$(".badleftfeatureproduct").owlCarousel( {
    loop:!1, dots:!1, nav:!1, autoplay:!1, autoplayTimeout:3e3, responsive: {
        0: {
            items: 1
        }
        , 320: {
            items: 1, slideBy: 1
        }
        , 640: {
            items: 1, slideBy: 1
        }
        , 992: {
            items: 1, slideBy: 1
        }
        , 1200: {
            items: 1, slideBy: 1
        }
        , 1399: {
            items: 1, slideBy: 1
        }
    }
}
),
$(".badleftnewproduct").owlCarousel( {
    loop:!1, dots:!1, nav:!1, autoplay:!1, autoplayTimeout:3e3, responsive: {
        0: {
            items: 1
        }
        , 320: {
            items: 1, slideBy: 1
        }
        , 640: {
            items: 1, slideBy: 1
        }
        , 992: {
            items: 1, slideBy: 1
        }
        , 1200: {
            items: 1, slideBy: 1
        }
        , 1399: {
            items: 1, slideBy: 1
        }
    }
}
),
$(".badleftbestproduct").owlCarousel( {
    loop:!1, dots:!1, nav:!1, autoplay:!1, autoplayTimeout:3e3, responsive: {
        0: {
            items: 1
        }
        , 320: {
            items: 1, slideBy: 1
        }
        , 640: {
            items: 1, slideBy: 1
        }
        , 992: {
            items: 1, slideBy: 1
        }
        , 1200: {
            items: 1, slideBy: 1
        }
        , 1399: {
            items: 1, slideBy: 1
        }
    }
}
),
$(".badleftspecialproduct").owlCarousel( {
    loop:!1, dots:!1, nav:!1, autoplay:!1, autoplayTimeout:3e3, responsive: {
        0: {
            items: 1
        }
        , 320: {
            items: 1, slideBy: 1
        }
        , 640: {
            items: 1, slideBy: 1
        }
        , 992: {
            items: 1, slideBy: 1
        }
        , 1200: {
            items: 1, slideBy: 1
        }
        , 1399: {
            items: 1, slideBy: 1
        }
    }
}
);
    $(".badlefttestimonials-block .badlefttestimonials-content-inner").owlCarousel( {
        loop:!0, pagination:!0, navigation:!0, autoplay:!0, autoplayTimeout:2e3, responsive: {
            0: {
                items: 1
            }
            , 320: {
                items: 1, slideBy: 1
            }
            , 576: {
                items: 1, slideBy: 1
            }
            , 768: {
                items: 1, slideBy: 1
            }
            , 992: {
                items: 1, slideBy: 1
            }
            , 1200: {
                items: 1, slideBy: 1
            }
            , 1400: {
                items: 1, slideBy: 1
            }
            , 1600: {
                items: 1, slideBy: 1
            }
            , 1800: {
                items: 1, slideBy: 1
            }
        }
    }
    ), $(document).on("click", ".badlefttestimonials-block .badlefttestimonials-prev-btn", function() {
        $(".badlefttestimonials-block").find(".owl-prev").trigger("click")
    }
    ), $(document).on("click", ".badlefttestimonials-block .badlefttestimonials-next-btn", function() {
        $(".badlefttestimonials-block").find(".owl-next").trigger("click")
    }
    )
}
);

/*allblog*/
/* $(document).ready(function(){
    $('.badhomeblog-block .badhomeblog-content-inner').owlCarousel({
        loop : true,
        pagination : false,
        navigation : false,
        responsive: {
            0: { items: 1 },
            320: { items: 1, slideBy: 1 },
            576: { items: 2, slideBy: 1 },
            768: { items: 2, slideBy: 1 },
            992: { items: 2, slideBy: 1 },
            1200: { items: 2, slideBy: 1 },
            1400: { items: 3, slideBy: 1 },
            1600: { items: 3, slideBy: 1 },
            1800: { items: 3, slideBy: 1 },

        },
    });

    $(document).on('click','.badhomeblog-block .badhomeblog-prev-btn', function(){
        $('.badhomeblog-block').find('.owl-prev').trigger('click');
    });

    $(document).on('click','.badhomeblog-block .badhomeblog-next-btn', function(){
        $('.badhomeblog-block').find('.owl-next').trigger('click');
    });
});*/ 
/*allblog*/