!function(t) {
    function s(s) {
        s.carouFredSel({
            responsive: !0,
            width: "variable",
            height: "variable",
            prev: {
                button: function() {
                    return t(this).parents().children(".tmls_next_prev").children(".tmls_prev");
                }
            },
            next: {
                button: function() {
                    return t(this).parents().children(".tmls_next_prev").children(".tmls_next");
                }
            },
            pagination: {
                container: function() {
                    return "after" == t(this).attr("data-imagesposition") ? t(this).parents().next().children(".tmls_paginationContainer") : t(this).parents().prev().children(".tmls_paginationContainer");
                },
                anchorBuilder: function(s) {
                    return "avatars" == t(this).parent().attr("data-usedimages") ? "<div class='tmls_image_container " + t(this).attr("data-imageradius") + "'><div class='tmls_image' style='" + t(this).attr("data-bgimg") + "'></div><div class='tmls_image_overlay' style='background-color:" + t(this).parent().attr("data-slider2unselectedoverlaybgcolor") + "'></div></div>" : "<div class='tmls_image_container'><div class='tmls_image tmls_logo_image'><img alt='' src='" + t(this).attr("data-logoimage") + "'/></div><div class='tmls_image_overlay' style='background-color:" + t(this).parent().attr("data-slider2unselectedoverlaybgcolor") + "'></div></div>";
                }
            },
            scroll: {
                items: 1,
                duration: s.data("scrollduration"),
                fx: s.data("transitioneffect")
            },
            auto: {
                play: s.data("autoplay"),
                timeoutDuration: s.data("pauseduration"),
                pauseOnHover: s.data("pauseonhover")
            },
            items: {
                width: 700
            },
            swipe: {
                onMouse: !1,
                onTouch: !0
            }
        });
    }
    t(document).ready(function() {
        var s = t(".tmls_form.tmls_notready"), e = t(".tmls.tmls_notready .tmls_slider, .tmls.tmls_notready .tmls_slider2"), i = t(".style3.tmls_style3_notready");
        self != top ? t.tmls_findNotReadyInserted() : (s.length > 0 && s.each(function() {
            t(this).removeClass("tmls_notready"), t.tmls_runFormsScripts(t(this));
        }), e.length > 0 && e.each(function() {
            t(this).parent().removeClass("tmls_notready"), t.tmls_runSlidersScripts(t(this));
        }), i.length > 0 && i.each(function() {
            t(this).removeClass("tmls_style3_notready"), t.tmls_runStyle3Scripts(t(this));
        })), t.tmls_runReadMoreScripts(), t.tmls_runLogosImagesScripts();
    }), t.tmls_runLogosImagesScripts = function() {
        var s = t(".tmls.tmls_use_avatars_and_logos .tmls_item");
        s.mouseenter(function() {
            t(this).find(".tmls_image").stop().fadeIn("slow"), t(this).find(".tmls_image.tmls_logo_image").stop().hide(0);
        }), s.mouseleave(function() {
            t(this).find(".tmls_image").stop().hide(0), t(this).find(".tmls_image.tmls_logo_image").stop().fadeIn("slow");
        });
    }, t.tmls_runReadMoreScripts = function() {
        var s = t(".tmls_morelink"), e = t(".tmls_fulltext").parents(".tmls_item");
        s.click(function() {
            var s = t(this).parents(".tmls_item"), e = s.find(".tmls_fulltext"), i = s.find(".tmls_excerpttext"), n = t(this).parents(".tmls_container"), r = t(this).parents(".tmls_slider"), l = t(this).parents(".caroufredsel_wrapper"), a = t(this).parents(".tmls_slider2");
            i.stop().hide(0, function() {
                e.stop().fadeIn("slow"), n.hasClass("tmls_slider") ? (r.css("min-height", s.height()), 
                l.css("min-height", s.height())) : n.hasClass("tmls_slider2") && (a.css("min-height", s.height()), 
                l.css("min-height", s.height()));
            });
        }), e.mouseleave(function() {
            var s = t(this).find(".tmls_fulltext"), e = t(this).find(".tmls_excerpttext"), i = t(this).parents(".tmls_container"), n = t(this).parents(".tmls_slider"), r = t(this).parents(".caroufredsel_wrapper"), l = t(this).parents(".tmls_slider2");
            s.stop().hide(0, function() {
                e.stop().fadeIn("slow"), i.hasClass("tmls_slider") ? (n.css("min-height", 0), r.css("min-height", 0)) : i.hasClass("tmls_slider2") && (l.css("min-height", 0), 
                r.css("min-height", 0));
            });
        });
    }, t.tmls_findNotReadyInserted = function() {
        var s = t(".tmls_form.tmls_notready"), e = t(".tmls.tmls_notready .tmls_slider, .tmls.tmls_notready .tmls_slider2"), i = t(".style3.tmls_style3_notready");
        s.length > 0 && s.each(function() {
            t(this).removeClass("tmls_notready"), t.tmls_runFormsScripts(t(this));
        }), e.length > 0 && e.each(function() {
            t.tmls_runSlidersScripts(t(this)), t(this).parent().parent().removeClass("tmls_notready");
        }), i.length > 0 && i.each(function() {
            t(this).removeClass("tmls_style3_notready"), t.tmls_runStyle3Scripts(t(this));
        }), setTimeout(function() {
            t.tmls_findNotReadyInserted();
        }, 1e3);
    }, t.tmls_runFormsScripts = function(s) {
        var e = s.find(".tmls_form_submit");
        e.length > 0 && (e.mouseover(function() {
            t(this).css("color", t(this).attr("data-hoverfontcolor")), t(this).css("border-color", t(this).attr("data-hoverbordercolor")), 
            t(this).css("background-color", t(this).attr("data-hoverbgcolor"));
        }), e.mouseleave(function() {
            t(this).css("color", t(this).attr("data-fontcolor")), t(this).css("border-color", t(this).attr("data-bordercolor")), 
            t(this).css("background-color", t(this).attr("data-bgcolor"));
        }));
    }, t.tmls_runSlidersScripts = function(e) {
        var i = e.parent().parent().find(".tmls_next_prev.tmls_visible");
        s(e), e.parent().parent().mouseenter(function() {
            t(this).children(".tmls_show_on_hover").slideToggle();
        }), e.parent().parent().mouseleave(function() {
            t(this).children(".tmls_show_on_hover").slideToggle();
        }), i.fadeIn();
    }, t.tmls_runStyle3Scripts = function(s) {
        var e = s.find(".tmls_name");
        e.length > 0 && e.each(function() {
            var s = t(this).height() + 2.5 + t(this).parent().children(".tmls_position").height() + t(this).parent().children(".tmls_rating").height() + 5, e = t(this).parent().children(".tmls_image").last().height();
            e > s && "none" != t(this).parent().children(".tmls_image").last().css("display") ? t(this).css("padding-top", e / 2 - s / 2) : t(this).css("padding-top", 0);
        });
    }, t(window).load(function() {
        var e = t(".tmls_slider, .tmls_slider2");
        e.length > 0 && e.each(function() {
            s(t(this));
        });
    }), t(window).resize(function() {
        var e = t(".tmls_slider, .tmls_slider2"), i = t(".style3");
        e.length > 0 && s(e), i.length > 0 && i.each(function() {
            t.tmls_runStyle3Scripts(t(this));
        });
    });
}(jQuery);