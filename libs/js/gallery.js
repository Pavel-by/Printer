/**
 * LC Lightbox - LITE
 * yet.. another jQuery lightbox.. or not?
 *
 * @version    :    1.2.3
 * @copyright    :    Luca Montanari aka LCweb
 * @website    :    https://lcweb.it
 * @requires    :    jQuery v1.7 or later

 * Released under the MIT license
 */

/*
 :	Luca Montanari aka LCweb
 @website	:	https://lcweb.it
 @requires	:	jQuery v1.7 or later

 Released under the MIT license
 */
(function (b) {
    lcl_objs = [];
    lcl_is_active = lcl_shown = !1;
    lcl_slideshow = void 0;
    lcl_on_mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent);
    lcl_hashless_url = lcl_deeplink_tracked = lcl_curr_vars = lcl_curr_opts = lcl_curr_obj = !1;
    lcl_url_hash = "";
    Gallery = function (k, C) {
        if ("string" != typeof k && ("object" != typeof k || !k.length)) return !1;
        var v = !1;
        b.each(lcl_objs, function (b, h) {if (JSON.stringify(h) == JSON.stringify(k)) return v = h, !1});
        if (!1 === v) {
            var u = new D(k, C);
            lcl_objs.push(u);
            return u
        }
        return v
    };
    lcl_destroy = function (k) {
        k = b.inArray(k, lcl_objs);
        -1 !== k && lcl_objs.splice(k, 1)
    };
    var D = function (k, C) {
        var v = b.extend({
            gallery: !0,
            gallery_hook: "rel",
            live_elements: !0,
            preload_all: !1,
            global_type: "image",
            src_attr: "href",
            title_attr: "title",
            txt_attr: "data-lcl-txt",
            author_attr: "data-lcl-author",
            slideshow: !0,
            open_close_time: 400,
            ol_time_diff: 100,
            fading_time: 80,
            animation_time: 250,
            slideshow_time: 6E3,
            autoplay: !1,
            counter: !1,
            progressbar: !0,
            carousel: !0,
            max_width: "93%",
            max_height: "93%",
            wrap_padding: !1,
            ol_opacity: .7,
            ol_color: "#111",
            ol_pattern: !1,
            border_w: 0,
            border_col: "#ddd",
            padding: 0,
            radius: 0,
            shadow: !0,
            remove_scrollbar: !0,
            wrap_class: "",
            skin: "light",
            data_position: "over",
            cmd_position: "inner",
            ins_close_pos: "normal",
            nav_btn_pos: "normal",
            txt_hidden: 500,
            show_title: !0,
            show_descr: !0,
            show_author: !0,
            thumbs_nav: !0,
            tn_icons: !0,
            tn_hidden: 500,
            thumbs_w: 110,
            thumbs_h: 110,
            thumb_attr: !1,
            thumbs_maker_url: !1,
            fullscreen: !1,
            fs_img_behavior: "fit",
            fs_only: 500,
            browser_fs_mode: !0,
            socials: !1,
            fb_direct_share: !1,
            txt_toggle_cmd: !0,
            download: !1,
            touchswipe: !0,
            mousewheel: !0,
            modal: !1,
            rclick_prevent: !1,
            elems_parsed: function () {},
            html_is_ready: function () {},
            on_open: function () {},
            on_elem_switch: function () {},
            slideshow_start: function () {},
            slideshow_end: function () {},
            on_fs_enter: function () {},
            on_fs_exit: function () {},
            on_close: function () {}
        }, C), u = {
            elems: [],
            is_arr_instance: "string" != typeof k && "undefined" == typeof k[0].childNodes ? !0 : !1,
            elems_count: "string" != typeof k && "undefined" == typeof k[0].childNodes ? k.length : b(k).length,
            elems_selector: "string" == typeof k ? k : !1,
            elem_index: !1,
            gallery_hook_val: !1,
            preload_all_used: !1,
            img_sizes_cache: [],
            inner_cmd_w: !1,
            txt_exists: !1,
            txt_und_sizes: !1,
            force_fullscreen: !1,
            html_style: "",
            body_style: ""
        };
        "string" == typeof k &&
        (k = b(k));
        var l = b.data(k, "lcl_settings", v), h = b.data(k, "lcl_vars", u), z = function (b) {
            if ("string" != typeof b) return b;
            for (var c = 0, e = 0, f = b.toString().length; e < f;) c = (c << 5) - c + b.charCodeAt(e++) << 0;
            return 0 > c ? -1 * c : c
        }, D = function (c) {
            var d = !1;
            b.each(h.elems, function (b, f) {if (f.hash == c) return d = f, !1});
            return d
        }, B = function (c) {
            if (!c) return c;
            c = c.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&amp;/g, "&").replace(/&quot;/g, '"').replace(/&#039;/g, "'");
            return b.trim(c)
        }, E = function (c, d) {
            var e = l[d];
            return -1 !== e.indexOf("> ") ?
                c.find(e.replace("> ", "")).length ? b.trim(c.find(e.replace("> ", "")).html()) : "" : "undefined" != typeof c.attr(e) ? B(c.attr(e)) : ""
        }, X = function (c) {
            var d = l, e = [];
            c.each(function () {
                var c = b(this), g = c.attr(d.src_attr), k = z(g);
                if (h.gallery_hook_val && c.attr(d.gallery_hook) != h.gallery_hook_val) return !0;
                k = D(k);
                if (!k) {
                    k = g;
                    var m = c.data("lcl-type");
                    "undefined" == typeof m ? m = l.global_type : (k = k.toLowerCase(), m = /^https?:\/\/(?:[a-z\-]+\.)+[a-z]{2,6}(?:\/[^\/#?]+)+\.(?:jpe?g|gif|png)$/.test(k) ? "image" : "unknown");
                    "unknown" !=
                    m ? (k = {
                        src: g,
                        type: m,
                        hash: d.deeplink ? z(g) : !1,
                        title: d.show_title ? E(c, "title_attr") : "",
                        txt: d.show_descr ? E(c, "txt_attr") : "",
                        author: d.show_author ? E(c, "author_attr") : "",
                        thumb: d.thumb_attr && "undefined" != typeof d.thumb_attr ? c.attr(d.thumb_attr) : "",
                        width: "image" != m && "undefined" != typeof c.data("lcl-w") ? c.data("lcl-w") : !1,
                        height: "image" != m && "undefined" != typeof c.data("lcl-h") ? c.data("lcl-h") : !1,
                        force_over_data: "undefined" != typeof c.data("lcl-force-over-data") ? parseInt(c.data("lcl-force-over-data"), 10) : "",
                        force_outer_cmd: "undefined" !=
                        typeof c.data("lcl-outer-cmd") ? c.data("lcl-outer-cmd") : ""
                    }, k.download = "image" == m ? "undefined" != typeof c.data("lcl-path") ? c.data("lcl-path") : g : !1) : k = {
                        src: g,
                        type: m,
                        hash: d.deeplink ? z(g) : !1
                    }
                }
                e.push(k)
            });
            2 > e.length && b(".lcl_prev, .lcl_next, #lcl_thumb_nav").remove();
            if (!e.length) return !1;
            h.elems = e;
            return !0
        }, K = function () {
            if (2 > h.elems.length || !l.gallery) return !1;
            0 < h.elem_index && x(!1, h.elem_index - 1);
            h.elem_index != h.elems.length - 1 && x(!1, h.elem_index + 1)
        }, x = function (c, d, e) {
            var f = h;
            "undefined" == typeof d && (d = f.elem_index);
            if ("undefined" == typeof d) return !1;
            var g = "image" == f.elems[d].type ? "image" == f.elems[d].type ? f.elems[d].src : f.elems[d].poster : "";
            g && "undefined" == typeof f.img_sizes_cache[g] ? b("<img/>").bind("load", function () {
                f.img_sizes_cache[g] = {
                    w: this.width,
                    h: this.height
                };
                c && d == f.elem_index && L()
            }).attr("src", g) : ((c || "undefined" != typeof e) && b("#lcl_loader").addClass("no_loader"), c && L())
        }, M = function (c, d) {
            var e = b.data(c, "lcl_settings"), f = b.data(c, "lcl_vars");
            if (f.is_arr_instance) {
                var g = [];
                b.each(c, function (c, d) {
                    var f =
                        {}, h = "undefined" == typeof d.type && e.global_type ? e.global_type : !1;
                    "undefined" != typeof d.type && (h = d.type);
                    h && -1 !== b.inArray(h, ["image"]) ? "undefined" != typeof d.src && d.src && (f.src = d.src, f.type = h, f.hash = z(d.src), f.title = "undefined" == typeof d.title ? "" : B(d.title), f.txt = "undefined" == typeof d.txt ? "" : B(d.txt), f.author = "undefined" == typeof d.author ? "" : B(d.author), f.width = "undefined" == typeof d.width ? !1 : d.width, f.height = "undefined" == typeof d.height ? !1 : d.height, f.force_over_data = "undefined" == typeof d.force_over_data ?
                        !1 : parseInt(d.force_over_data, 10), f.force_outer_cmd = "undefined" == typeof d.force_outer_cmd ? !1 : d.force_outer_cmd, f.thumb = "undefined" == typeof d.thumb ? !1 : d.thumb, f.download = "image" == h ? "undefined" != typeof d.download ? d.download : d.src : !1, g.push(f)) : (f = {
                        src: f.src,
                        type: "unknown",
                        hash: e.deeplink ? z(f.src) : !1
                    }, g.push(f))
                });
                f.elems = g
            } else {
                var y = c;
                e.live_elements && f.elems_selector && (y = d && e.gallery && e.gallery_hook && "undefined" != typeof b(k[0]).attr(e.gallery_hook) ? f.elems_selector + "[" + e.gallery_hook + "=" + d.attr(e.gallery_hook) +
                    "]" : f.elems_selector, y = b(y));
                if (!X(y)) return (!e.live_elements || e.live_elements && !f.elems_selector) && console.error("LC Lightbox - no valid elements found"), !1
            }
            e.preload_all && !f.preload_all_used && (f.preload_all_used = !0, b(document).ready(function (c) {b.each(f.elems, function (b, c) {x(!1, b)})}));
            "function" == typeof e.elems_parsed && e.elems_parsed.call({opts: l, vars: h});
            f.is_arr_instance || (y = f.elems_selector ? b(f.elems_selector) : c, y.first().trigger("lcl_elems_parsed", [f.elems]));
            return !0
        };
        M(k);
        var G = function (c,
                          d) {
                if (lcl_shown || lcl_is_active) return !1;
                lcl_is_active = lcl_shown = !0;
                lcl_curr_obj = c;
                l = b.data(c, "lcl_settings");
                h = b.data(c, "lcl_vars");
                lcl_curr_opts = l;
                lcl_curr_vars = h;
                var e = l, f = h, g = "undefined" != typeof d ? d : !1;
                if (!h) return console.error("LC Lightbox - cannot open. Object not initialized"), !1;
                f.gallery_hook_val = g && e.gallery && e.gallery_hook && "undefined" != typeof g.attr(e.gallery_hook) ? g.attr(e.gallery_hook) : !1;
                if (!M(c, d)) return !1;
                if (g) b.each(f.elems, function (b, c) {
                    if (c.src == g.attr(e.src_attr)) return f.elem_index =
                        b, !1
                }); else if (parseInt(f.elem_index, 10) >= f.elems_count) return console.error("LC Lightbox - selected index does not exist"), !1;
                x(!1);
                Y();
                Z();
                f.force_fullscreen && F(!0, !0);
                b("#lcl_thumbs_nav").length && aa();
                x(!0);
                K()
            }, N = function () {
                b("#lcl_wrap").removeClass("lcl_pre_show").addClass("lcl_shown");
                b("#lcl_loader").removeClass("lcl_loader_pre_first_el")
            }, Y = function () {
                var c = l, d = h, e = [], f = "";
                "number" == typeof document.documentMode && (b("body").addClass("lcl_old_ie"), "outer" != c.cmd_position && (c.nav_btn_pos = "normal"));
                b("#lcl_wrap").length && b("#lcl_wrap").remove();
                b("body").append('<div id="lcl_wrap" class="lcl_pre_show lcl_pre_first_el lcl_first_sizing lcl_is_resizing"><div id="lcl_window"><div id="lcl_corner_close" title="close"></div><div id="lcl_loader" class="lcl_loader_pre_first_el"><span id="lcll_1"></span><span id="lcll_2"></span></div><div id="lcl_nav_cmd"><div class="lcl_icon lcl_prev" title="previous"></div><div class="lcl_icon lcl_play"></div><div class="lcl_icon lcl_next" title="next"></div><div class="lcl_icon lcl_counter"></div><div class="lcl_icon lcl_right_icon lcl_close" title="close"></div><div class="lcl_icon lcl_right_icon lcl_fullscreen" title="toggle fullscreen"></div><div class="lcl_icon lcl_right_icon lcl_txt_toggle" title="toggle text"></div><div class="lcl_icon lcl_right_icon lcl_download" title="download"></div><div class="lcl_icon lcl_right_icon lcl_thumbs_toggle" title="toggle thumbnails"></div><div class="lcl_icon lcl_right_icon lcl_socials" title="toggle socials"></div></div><div id="lcl_contents_wrap"><div id="lcl_subj"><div id="lcl_elem_wrap"></div></div><div id="lcl_txt"></div></div></div><div id="lcl_thumbs_nav"></div><div id="lcl_overlay"></div></div>');
                b("#lcl_wrap").attr("data-lcl-max-w", c.max_width).attr("data-lcl-max-h", c.max_height);
                e.push("lcl_" + c.ins_close_pos + "_close lcl_nav_btn_" + c.nav_btn_pos + " lcl_" + c.ins_close_pos + "_close lcl_nav_btn_" + c.nav_btn_pos);
                (!0 === c.tn_hidden || "number" == typeof c.tn_hidden && (b(window).width() < c.tn_hidden || b(window).height() < c.tn_hidden)) && e.push("lcl_tn_hidden");
                (!0 === c.txt_hidden || "number" == typeof c.txt_hidden && (b(window).width() < c.txt_hidden || b(window).height() < c.txt_hidden)) && e.push("lcl_hidden_txt");
                c.carousel ||
                e.push("lcl_no_carousel");
                lcl_on_mobile && e.push("lcl_on_mobile");
                c.wrap_class && e.push(c.wrap_class);
                e.push("lcl_" + c.cmd_position + "_cmd");
                if ("inner" != c.cmd_position) {
                    var g = b("#lcl_nav_cmd").detach();
                    b("#lcl_wrap").prepend(g)
                }
                c.slideshow || b(".lcl_play").remove();
                c.txt_toggle_cmd || b(".lcl_txt_toggle").remove();
                c.socials || b(".lcl_socials").remove();
                c.download || b(".lcl_download").remove();
                (!c.counter || 2 > d.elems.length || !c.gallery) && b(".lcl_counter").remove();
                d.force_fullscreen = !1;
                if (!c.fullscreen) b(".lcl_fullscreen").remove();
                else if (!0 === c.fs_only || "number" == typeof c.fs_only && (b(window).width() < c.fs_only || b(window).height() < c.fs_only)) b(".lcl_fullscreen").remove(), h.force_fullscreen = !0;
                2 > d.elems.length || !c.gallery ? b(".lcl_prev, .lcl_play, .lcl_next").remove() : "middle" == c.nav_btn_pos && (f += ".lcl_prev, .lcl_next {margin: " + c.padding + "px;}");
                !c.thumbs_nav || 2 > h.elems.length || !c.gallery ? b("#lcl_thumbs_nav, .lcl_thumbs_toggle").remove() : (b("#lcl_thumbs_nav").css("height", c.thumbs_h), g = b("#lcl_thumbs_nav").outerHeight(!0) - c.thumbs_h,
                    f += "#lcl_window {margin-top: " + -1 * (c.thumbs_h - g) + "px;}", f += ".lcl_tn_hidden.lcl_outer_cmd:not(.lcl_fullscreen_mode) #lcl_window {margin-bottom: " + -1 * b(".lcl_close").outerHeight(!0) + "px;}");
                e.push("lcl_txt_" + c.data_position + " lcl_" + c.skin);
                f += "#lcl_overlay {background-color: " + c.thumbs_h + "px; opacity: " + c.ol_opacity + ";}";
                c.ol_pattern && b("#lcl_overlay").addClass("lcl_pattern_" + c.ol_pattern);
                c.modal && b("#lcl_overlay").addClass("lcl_modal");
                c.wrap_padding && (f += "#lcl_wrap {padding: " + c.wrap_padding + ";}");
                c.border_w && (f += "#lcl_window {border: " + c.border_w + "px solid " + c.border_col + ";}");
                c.padding && (f += "#lcl_subj, #lcl_txt, #lcl_nav_cmd {margin: " + c.padding + "px;}");
                c.radius && (f += "#lcl_window, #lcl_contents_wrap {border-radius: " + c.radius + "px;}");
                c.shadow && (f += "#lcl_window {box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);}");
                "inner" == c.cmd_position && "corner" == c.ins_close_pos && (f += "#lcl_corner_close {top: " + -1 * (c.border_w + Math.ceil(b("#lcl_corner_close").outerWidth() / 2)) + "px;right: " + -1 * (c.border_w + Math.ceil(b("#lcl_corner_close").outerHeight() /
                    2)) + ";}", b("#lcl_nav_cmd > *:not(.lcl_close)").length || (f += "#lcl_wrap:not(.lcl_fullscreen_mode):not(.lcl_forced_outer_cmd) #lcl_nav_cmd {display: none;}"));
                b("#lcl_inline_style").length && b("#lcl_inline_style").remove();
                b("head").append('<style type="text/css" id="lcl_inline_style">' + f + "#lcl_overlay {background-color: " + c.ol_color + ";opacity: " + c.ol_opacity + ";}#lcl_window, #lcl_txt, #lcl_subj {-webkit-transition-duration: " + c.animation_time + "ms; transition-duration: " + c.animation_time + "ms;}#lcl_overlay {-webkit-transition-duration: " +
                    c.open_close_time + "ms; transition-duration: " + c.open_close_time + "ms;}.lcl_first_sizing #lcl_window, .lcl_is_closing #lcl_window {-webkit-transition-duration: " + (c.open_close_time - c.ol_time_diff) + "ms; transition-duration: " + (c.open_close_time - c.ol_time_diff) + "ms;}.lcl_first_sizing #lcl_window {-webkit-transition-delay: " + c.ol_time_diff + "ms; transition-delay: " + c.ol_time_diff + "ms;}#lcl_loader, #lcl_contents_wrap, #lcl_corner_close {-webkit-transition-duration: " + c.fading_time + "ms; transition-duration: " +
                    c.fading_time + "ms;}.lcl_toggling_txt #lcl_subj {-webkit-transition-delay: " + (c.fading_time + 200) + "ms !important;  transition-delay: " + (c.fading_time + 200) + "ms !important;}.lcl_fullscreen_mode.lcl_txt_over:not(.lcl_tn_hidden) #lcl_txt, .lcl_fullscreen_mode.lcl_force_txt_over:not(.lcl_tn_hidden) #lcl_txt {max-height: calc(100% - 42px - " + c.thumbs_h + "px);}.lcl_fullscreen_mode.lcl_playing_video.lcl_txt_over:not(.lcl_tn_hidden) #lcl_txt,.lcl_fullscreen_mode.lcl_playing_video.lcl_force_txt_over:not(.lcl_tn_hidden) #lcl_txt {max-height: calc(100% - 42px - 45px - " +
                    c.thumbs_h + "px);}</style>");
                c.remove_scrollbar && (h.html_style = "undefined" != typeof jQuery("html").attr("style") ? jQuery("html").attr("style") : "", h.body_style = "undefined" != typeof jQuery("body").attr("style") ? jQuery("body").attr("style") : "", f = b(window).width(), b("html").css("overflow", "hidden"), b("html").css({
                    "margin-right": b(window).width() - f,
                    "touch-action": "none"
                }), b("body").css({overflow: "visible", "touch-action": "none"}));
                f = h.elems[d.elem_index];
                "image" != f.type || "image" == f.type && "undefined" != typeof d.img_sizes_cache[f.src] ?
                    e.push("lcl_show_already_shaped") : N();
                b("#lcl_wrap").addClass(e.join(" "));
                "function" == typeof c.html_is_ready && c.html_is_ready.call({opts: l, vars: h});
                h.is_arr_instance || (h.elems_selector ? b(h.elems_selector) : lcl_curr_obj).first().trigger("lcl_html_is_ready", [l, h])
            }, ba = function (c) {
                var d = b(c)[0], e = null;
                d.addEventListener("touchstart", function (b) {1 === b.targetTouches.length && (e = b.targetTouches[0].clientY)}, !1);
                d.addEventListener("touchmove", function (b) {
                    if (1 === b.targetTouches.length) {
                        var c = b.targetTouches[0].clientY -
                            e;
                        0 === d.scrollTop && 0 < c && b.preventDefault();
                        d.scrollHeight - d.scrollTop <= d.clientHeight && 0 > c && b.preventDefault()
                    }
                }, !1)
            }, L = function () {
                if (!lcl_shown) return !1;
                var c = h, d = c.elems[c.elem_index];
                b("#lcl_wrap").attr("lc-lelem", c.elem_index);
                l.carousel || (b("#lcl_wrap").removeClass("lcl_first_elem lcl_last_elem"), c.elem_index ? c.elem_index == c.elems.length - 1 && b("#lcl_wrap").addClass("lcl_last_elem") : b("#lcl_wrap").addClass("lcl_first_elem"));
                b(document).trigger("lcl_before_populate_global", [d, c.elem_index]);
                var e =
                    h.elem_index;
                b("#lcl_elem_wrap").removeAttr("style").removeAttr("class").empty();
                b("#lcl_wrap").attr("lcl-type", d.type);
                b("#lcl_elem_wrap").addClass("lcl_" + d.type + "_elem");
                switch (d.type) {
                    case "image":
                        b("#lcl_elem_wrap").css("background-image", "url('" + d.src + "')");
                        break;
                    default:
                        b("#lcl_elem_wrap").html('<div id="lcl_inline" class="lcl_elem"><br/>Error loading the resource .. </div>')
                }
                if (lcl_curr_opts.download) if (d.download) {
                    b(".lcl_download").show();
                    var f = d.download.split("/");
                    f = f[f.length - 1];
                    b(".lcl_download").html('<a href="' +
                        d.download + '" target="_blank" download="' + f + '"></a>')
                } else b(".lcl_download").hide();
                b(".lcl_counter").html(e + 1 + " / " + h.elems.length);
                H(d) && "unknown" != d.type ? (b("#lcl_wrap").removeClass("lcl_no_txt"), b(".lcl_txt_toggle").show(), d.title && b("#lcl_txt").append('<h3 id="lcl_title">' + d.title + "</h3>"), d.author && b("#lcl_txt").append('<h5 id="lcl_author">by ' + d.author + "</h5>"), d.txt && b("#lcl_txt").append('<section id="lcl_descr">' + d.txt + "</section>"), d.txt && (d.title && d.author ? b("#lcl_txt h5").addClass("lcl_txt_border") :
                    b("#lcl_txt h3").length ? b("#lcl_txt h3").addClass("lcl_txt_border") : b("#lcl_txt h5").addClass("lcl_txt_border"))) : (b(".lcl_txt_toggle").hide(), b("#lcl_wrap").addClass("lcl_no_txt"));
                ba("#lcl_txt");
                c.is_arr_instance || (e = c.elems_selector ? b(c.elems_selector) : lcl_curr_obj, e.first().trigger("lcl_before_show", [d, c.elem_index]));
                b(document).trigger("lcl_before_show_global", [d, c.elem_index]);
                b("#lcl_wrap").hasClass("lcl_pre_first_el") && ("function" == typeof l.on_open && l.on_open.call({
                    opts: l,
                    vars: h
                }), c.is_arr_instance ||
                (e = c.elems_selector ? b(c.elems_selector) : lcl_curr_obj, e.first().trigger("lcl_on_open", [d, c.elem_index])));
                w(d);
                b("#lcl_subj").removeClass("lcl_switching_el")
            }, H = function (b) {return b.title || b.txt || b.author ? !0 : !1},
            O = function (c, d, e) {
                var f = 0, g = b("#lcl_wrap"),
                    h = b(window).width() - parseInt(g.css("padding-left"), 10) - parseInt(g.css("padding-right"), 10);
                g = b(window).height() - parseInt(g.css("padding-top"), 10) - parseInt(g.css("padding-bottom"), 10);
                !isNaN(parseFloat(c)) && isFinite(c) ? f = parseInt(c, 10) : -1 !== c.toString().indexOf("%") ?
                    f = ("w" == d ? h : g) * (parseInt(c, 10) / 100) : -1 !== c.toString().indexOf("vw") ? f = h * (parseInt(c, 10) / 100) : -1 !== c.toString().indexOf("vh") && (f = g * (parseInt(c, 10) / 100));
                "undefined" == typeof e && ("w" == d && f > h && (f = h), "h" == d && f > g && (f = g));
                return f
            }, w = function (c, d, e) {
                var f = l, g = h;
                "undefined" == typeof d && (d = {});
                var k = (e = b(".lcl_fullscreen_mode").length ? !0 : !1) ? 0 : 2 * parseInt(f.border_w, 10) + 2 * parseInt(f.padding, 10);
                "undefined" != typeof d.side_txt_checked || "undefined" != typeof d.no_txt_under && d.no_txt_under || b("#lcl_wrap").removeClass("lcl_force_txt_over");
                var m = b(".lcl_force_txt_over").length || b(".lcl_hidden_txt").length || -1 === b.inArray(f.data_position, ["rside", "lside"]) || !H(c) ? 0 : b("#lcl_txt").outerWidth();
                var n = e || !b("#lcl_thumbs_nav").length || b(".lcl_tn_hidden").length ? 0 : b("#lcl_thumbs_nav").outerHeight(!0) - parseInt(b("#lcl_wrap").css("padding-bottom"), 10);
                var p = !e && b(".lcl_outer_cmd").length ? b(".lcl_close").outerHeight(!0) + parseInt(b("#lcl_nav_cmd").css("padding-top"), 10) + parseInt(b("#lcl_nav_cmd").css("padding-bottom"), 10) : 0;
                var q = k + m;
                n = k + n + p;
                var r = b("#lcl_wrap").attr("data-lcl-max-w");
                p = b("#lcl_wrap").attr("data-lcl-max-h");
                q = e ? b(window).width() : Math.floor(O(r, "w")) - q;
                p = e ? b(window).height() : Math.floor(O(p, "h")) - n;
                if ("object" == typeof g.txt_und_sizes) {
                    if (q = g.txt_und_sizes.w, n = g.txt_und_sizes.h, "image" == c.type) var t = g.img_sizes_cache[c.src]
                } else switch (c.type) {
                    case "image":
                        b("#lcl_elem_wrap").css("bottom", 0);
                        if ("undefined" == typeof g.img_sizes_cache[c.src]) return !1;
                        t = g.img_sizes_cache[c.src];
                        t.w <= q ? (q = t.w, n = t.h) : n = Math.floor(t.h / t.w * q);
                        n >
                        p && (n = p, q = Math.floor(t.w / t.h * n));
                        if (H(c) && !b(".lcl_hidden_txt").length && "under" == f.data_position && "undefined" == typeof d.no_txt_under) return P(q, n, p), b(document).off("lcl_txt_und_calc").on("lcl_txt_und_calc", function () {if (g.txt_und_sizes) return "no_under" == g.txt_und_sizes && (d.no_txt_under = !0), w(g.elems[g.elem_index], d)}), !1;
                        b("#lcl_subj").css("maxHeight", "none");
                        break;
                    default:
                        q = 280, n = 125
                }
                if (("rside" == f.data_position || "lside" == f.data_position) && !b(".lcl_no_txt").length && "undefined" == typeof d.side_txt_checked &&
                    (t = "image" == c.type ? g.img_sizes_cache[c.src] : "", (p = c.force_over_data) || (p = 400), "image" == c.type && t.w > p && t.h > p && !ca(c, p, q + k, n + k, m))) return d.side_txt_checked = !0, w(c, d);
                g.txt_und_sizes = !1;
                if ("undefined" == typeof d.inner_cmd_checked && ("inner" == f.cmd_position || c.force_outer_cmd) && da(c, q)) return d.inner_cmd_checked = !0, w(c, d);
                b("#lcl_wrap").removeClass("lcl_pre_first_el");
                b("#lcl_window").css({width: e ? "100%" : q + k + m, height: e ? "100%" : n + k});
                b(".lcl_show_already_shaped").length && setTimeout(function () {
                    b("#lcl_wrap").removeClass("lcl_show_already_shaped");
                    N()
                }, 10);
                Q();
                "undefined" != typeof lcl_size_n_show_timeout && clearTimeout(lcl_size_n_show_timeout);
                k = b(".lcl_first_sizing").length ? f.open_close_time + 20 : f.animation_time;
                if (b(".lcl_browser_resize").length || b(".lcl_toggling_fs").length || e) k = 0;
                lcl_size_n_show_timeout = setTimeout(function () {
                    lcl_is_active && (lcl_is_active = !1);
                    b(".lcl_first_sizing").length && f.autoplay && 1 < g.elems.length && (f.carousel || g.elem_index < g.elems.length - 1) && lcl_start_slideshow();
                    if ("image" == c.type) if (b(".lcl_fullscreen_mode").length) {
                        var d =
                            t, e = l.fs_img_behavior;
                        if (b(".lcl_fullscreen_mode").length && d.w <= b("#lcl_subj").width() && d.h <= b("#lcl_subj").height()) b(".lcl_image_elem").css("background-size", "auto"); else if ("fit" == e) b(".lcl_image_elem").css("background-size", "contain"); else if ("fill" == e) b(".lcl_image_elem").css("background-size", "cover"); else if ("undefined" == typeof d) b(".lcl_image_elem").css("background-size", "cover"); else {
                            e = b(window).width() / b(window).height() - d.w / d.h;
                            var h = b(window).width() - d.w;
                            d = b(window).height() - d.h;
                            1.15 >=
                            e && -1.15 <= e && 350 >= h && 350 >= d ? b(".lcl_image_elem").css("background-size", "cover") : b(".lcl_image_elem").css("background-size", "contain")
                        }
                    } else b(".lcl_image_elem").css("background-size", "cover");
                    b("#lcl_wrap").removeClass("lcl_first_sizing lcl_switching_elem lcl_is_resizing lcl_browser_resize");
                    b("#lcl_loader").removeClass("no_loader");
                    b(document).trigger("lcl_resized_window")
                }, k)
            };
        b(window).resize(function () {
            if (!lcl_shown || k != lcl_curr_obj || b(".lcl_toggling_fs").length) return !1;
            b("#lcl_wrap").addClass("lcl_browser_resize");
            "undefined" != typeof lcl_rs_defer && clearTimeout(lcl_rs_defer);
            lcl_rs_defer = setTimeout(function () {lcl_resize()}, 50)
        });
        var P = function (c, d, e, f) {
                var g = "undefined" == typeof f ? 1 : f, k = b(".lcl_fullscreen_mode").length;
                b("#lcl_txt").outerHeight();
                var m = c / d;
                if (k && b("#lcl_thumbs_nav").length) return b("#lcl_wrap").addClass("lcl_force_txt_over"), b("#lcl_subj").css("maxHeight", "none"), b("#lcl_txt").css({
                    right: 0,
                    width: "auto"
                }), h.txt_und_sizes = "no_under", b(document).trigger("lcl_txt_und_calc"), !1;
                b("#lcl_wrap").removeClass("lcl_force_txt_over").addClass("lcl_txt_under_calc");
                k ? b("#lcl_txt").css({right: 0, width: "auto"}) : b("#lcl_txt").css({
                    right: "auto",
                    width: c
                });
                "undefined" != typeof lcl_txt_under_calc && clearInterval(lcl_txt_under_calc);
                lcl_txt_under_calc = setTimeout(function () {
                    var n = Math.ceil(b("#lcl_txt").outerHeight()), p = d + n - e;
                    if (k) return b("#lcl_wrap").removeClass("lcl_txt_under_calc"), b("#lcl_subj").css("maxHeight", "calc(100% - " + n + "px)"), h.txt_und_sizes = {
                        w: c,
                        h: d
                    }, b(document).trigger("lcl_txt_und_calc"), !1;
                    if (0 < p && ("undefined" == typeof f || 10 > f)) {
                        n = d - p;
                        p = Math.floor(n * m);
                        var q = h.elems[h.elem_index].force_over_data;
                        q || (q = 400);
                        return p < q || n < q ? (b("#lcl_wrap").removeClass("lcl_txt_under_calc").addClass("lcl_force_txt_over"), b("#lcl_subj").css("maxHeight", "none"), b("#lcl_txt").css({
                            right: 0,
                            width: "auto"
                        }), h.txt_und_sizes = "no_under", b(document).trigger("lcl_txt_und_calc"), !0) : P(p, n, e, g + 1)
                    }
                    b("#lcl_wrap").removeClass("lcl_txt_under_calc");
                    b("#lcl_subj").css("maxHeight", d + l.padding);
                    h.txt_und_sizes = {w: c, h: d + n};
                    b(document).trigger("lcl_txt_und_calc");
                    return !0
                }, 120)
            }, ca = function (c,
                              d, e, f, g) {
                g = b(".lcl_force_txt_over").length;
                if (e < d || "html" != c.type && f < d) {
                    if (g) return !0;
                    b("#lcl_wrap").addClass("lcl_force_txt_over")
                } else {
                    if (!g) return !0;
                    b("#lcl_wrap").removeClass("lcl_force_txt_over")
                }
                return !1
            }, da = function (c, d) {
                var e = l, f = b(".lcl_fullscreen_mode").length ? !0 : !1;
                if (b(".lcl_forced_outer_cmd").length) {
                    b("#lcl_wrap").removeClass("lcl_forced_outer_cmd");
                    b("#lcl_wrap").removeClass("lcl_outer_cmd").addClass("lcl_inner_cmd");
                    var g = b("#lcl_nav_cmd").detach();
                    b("#lcl_window").prepend(g)
                }
                f || !1 !==
                h.inner_cmd_w || (h.inner_cmd_w = 0, jQuery("#lcl_nav_cmd .lcl_icon").each(function () {
                    if ((b(this).hasClass("lcl_prev") || b(this).hasClass("lcl_next")) && "middle" == e.nav_btn_pos) return !0;
                    h.inner_cmd_w += b(this).outerWidth(!0)
                }));
                return f || c.force_outer_cmd || d <= h.inner_cmd_w ? (b("#lcl_wrap").addClass("lcl_forced_outer_cmd"), b("#lcl_wrap").removeClass("lcl_inner_cmd").addClass("lcl_outer_cmd"), g = b("#lcl_nav_cmd").detach(), b("#lcl_wrap").prepend(g), !0) : !1
            }, r = function (c, d) {
                var e = h, f = l.carousel;
                if (lcl_is_active ||
                    2 > e.elems.length || !l.gallery || b(".lcl_switching_elem").length) return !1;
                if ("next" == c) if (e.elem_index == e.elems.length - 1) {
                    if (!f) return !1;
                    c = 0
                } else c = e.elem_index + 1; else if ("prev" == c) if (e.elem_index) c = e.elem_index - 1; else {
                    if (!f) return !1;
                    c = e.elems.length - 1
                } else if (c = parseInt(c, 10), 0 > c || c >= e.elems.length || c == e.elem_index) return !1;
                "undefined" != typeof lcl_slideshow && ("undefined" == typeof d || !f && c == e.elems.length - 1) && lcl_stop_slideshow();
                lcl_is_active = !0;
                R(c);
                x(!1, c, !0);
                b("#lcl_wrap").addClass("lcl_switching_elem");
                setTimeout(function () {
                    b("#lcl_wrap").removeClass("lcl_playing_video");
                    "html" == e.elems[e.elem_index].type && (b("#lcl_window").css("height", b("#lcl_contents_wrap").outerHeight()), b("#lcl_contents_wrap").css("maxHeight", "none"));
                    "function" == typeof l.on_elem_switch && l.on_elem_switch.call({
                        opts: l,
                        vars: h,
                        new_el: c
                    });
                    !e.is_arr_instance && lcl_curr_obj && (e.elems_selector ? b(e.elems_selector) : lcl_curr_obj).first().trigger("lcl_on_elem_switch", [e.elem_index, c]);
                    b("#lcl_wrap").removeClass("lcl_no_txt lcl_loading_iframe");
                    b("#lcl_txt").empty();
                    e.elem_index = c;
                    x(!0);
                    K()
                }, l.fading_time)
            }, S = function (c) {
                var d = l;
                if (!d.progressbar) return !1;
                c = c ? 0 : d.animation_time + d.fading_time;
                var e = d.slideshow_time + d.animation_time - c;
                b("#lcl_progressbar").length || b("#lcl_wrap").append('<div id="lcl_progressbar"></div>');
                "undefined" != typeof lcl_pb_timeout && clearTimeout(lcl_pb_timeout);
                lcl_pb_timeout = setTimeout(function () {
                    b("#lcl_progressbar").stop(!0).removeAttr("style").css("width", 0).animate({width: "100%"}, e, "linear", function () {
                        b("#lcl_progressbar").fadeTo(0,
                            0)
                    })
                }, c)
            }, I = function () {
                if (!lcl_shown) return !1;
                "function" == typeof l.on_close && l.on_close.call({opts: l, vars: h});
                h.is_arr_instance || (h.elems_selector ? b(h.elems_selector) : lcl_curr_obj).first().trigger("lcl_on_close");
                b(document).trigger("lcl_on_close_global");
                b("#lcl_wrap").removeClass("lcl_shown").addClass("lcl_is_closing lcl_tn_hidden");
                lcl_stop_slideshow();
                b(".lcl_fullscreen_mode").length && T();
                setTimeout(function () {
                    b("#lcl_wrap, #lcl_inline_style").remove();
                    l.remove_scrollbar && (jQuery("html").attr("style",
                        h.html_style), jQuery("body").attr("style", h.body_style));
                    b(document).trigger("lcl_closed_global");
                    lcl_is_active = lcl_shown = lcl_curr_vars = lcl_curr_opts = lcl_curr_obj = !1
                }, l.open_close_time + 80);
                "undefined" != typeof lcl_size_check && clearTimeout(lcl_size_check)
            }, F = function (c, d) {
                "undefined" == typeof d && (d = !1);
                if (!lcl_shown || !l.fullscreen || !d && lcl_is_active) return !1;
                var e = l, f = h;
                b("#lcl_wrap").addClass("lcl_toggling_fs");
                e.browser_fs_mode && "undefined" != typeof c && (document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() :
                    document.documentElement.msRequestFullscreen ? document.documentElement.msRequestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT));
                setTimeout(function () {
                    b("#lcl_wrap").addClass("lcl_fullscreen_mode");
                    w(f.elems[f.elem_index]);
                    b(document).on("lcl_resized_window", function () {
                        b(document).off("lcl_resized_window");
                        (d || "under" ==
                            lcl_curr_opts.data_position && !b(".lcl_force_txt_over").length) && w(lcl_curr_vars.elems[lcl_curr_vars.elem_index]);
                        setTimeout(function () {b("#lcl_wrap").removeClass("lcl_toggling_fs")}, 150)
                    })
                }, d ? e.open_close_time : e.fading_time);
                "function" == typeof e.on_fs_enter && e.on_fs_enter.call({opts: e, vars: f});
                h.is_arr_instance || lcl_curr_obj.first().trigger("lcl_on_fs_enter")
            }, U = function (c) {
                if (!lcl_shown || !l.fullscreen || lcl_is_active) return !1;
                var d = l;
                b("#lcl_wrap").addClass("lcl_toggling_fs");
                b("#lcl_window").fadeTo(70,
                    0);
                setTimeout(function () {
                    if (d.browser_fs_mode && "undefined" != typeof c) {
                        T();
                        var e = 250
                    } else e = 0;
                    b("#lcl_wrap").removeClass("lcl_fullscreen_mode");
                    setTimeout(function () {
                        w(h.elems[h.elem_index]);
                        var c = c || navigator.userAgent;
                        c = -1 < c.indexOf("MSIE ") || -1 < c.indexOf("Trident/") ? 100 : 0;
                        setTimeout(function () {
                            b("#lcl_window").fadeTo(30, 1);
                            b("#lcl_wrap").removeClass("lcl_toggling_fs")
                        }, 300 + c)
                    }, e)
                }, 70);
                "function" == typeof d.on_fs_exit && d.on_fs_exit.call({opts: l, vars: h});
                h.is_arr_instance || (h.elems_selector ? b(h.elems_selector) :
                    lcl_curr_obj).first().trigger("lcl_on_fs_exit")
            },
            T = function () {document.exitFullscreen ? document.exitFullscreen() : document.msExitFullscreen ? document.msExitFullscreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitExitFullscreen && document.webkitExitFullscreen()},
            aa = function () {
                var c = !1, d = !1, e = Date.now();
                b("#lcl_thumbs_nav").append('<span class="lcl_tn_prev"></span><ul class="lcl_tn_inner"></ul><span class="lcl_tn_next"></span>');
                b("#lcl_thumbs_nav").attr("rel", e);
                b.each(h.elems,
                    function (f, g) {
                        if ("unknown" != g.type) {
                            c || (d && d != g.type ? c = !0 : d = g.type);
                            var k = "", m = "";
                            tpc = "";
                            if (g.thumb) m = g.thumb, k = "style=\"background-image: url('" + g.thumb + "');\""; else {
                                switch (g.type) {
                                    case "image":
                                        m = g.src;
                                        break;
                                    case "youtube":
                                        m = g.poster ? g.poster : "https://img.youtube.com/vi/" + g.video_id + "/maxresdefault.jpg";
                                        break;
                                    case "vimeo":
                                        g.poster ? m = g.poster : "undefined" == typeof h.vimeo_thumb_cache[g.src] ? (tpc = "lcl_tn_preload", b.getJSON("https://www.vimeo.com/api/v2/video/" + g.video_id + ".json?callback=?", {format: "json"},
                                            function (c) {
                                                V(c[0].thumbnail_large, f, e);
                                                h.vimeo_thumb_cache[g.src] = c[0].thumbnail_large;
                                                b(".lcl_tn_inner li[rel=" + f + "]").attr("style", b(".lcl_tn_inner li[rel=" + f + "]").attr("style") + " background-image: url('" + c[0].thumbnail_large + "');")
                                            })) : m = h.vimeo_thumb_cache[g.src];
                                        break;
                                    case "video":
                                    case "iframe":
                                    case "html":
                                        g.poster && (m = g.poster);
                                        break;
                                    case "dailymotion":
                                        m = g.poster ? g.poster : "http://www.dailymotion.com/thumbnail/video/" + g.video_id
                                }
                                m && (l.thumbs_maker_url && (g.poster || -1 === b.inArray(g.type, ["youtube",
                                    "vimeo", "dailymotion"])) && (m = l.thumbs_maker_url.replace("%URL%", encodeURIComponent(m)).replace("%W%", l.thumbs_w).replace("%H%", l.thumbs_h)), k = "style=\"background-image: url('" + m + "');\"", -1 === b.inArray(g.type, ["youtube", "vimeo", "dailymotion"]) || g.poster || (h.elems[f].vid_poster = m))
                            }
                            if (("html" == g.type || "iframe" == g.type) && !k) return !0;
                            var n = "video" != g.type || k ? "" : '<video src="' + g.src + '"></video>';
                            tpc = "lcl_tn_preload";
                            b(".lcl_tn_inner").append('<li class="lcl_tn_' + g.type + " " + tpc + '" title="' + g.title + '" rel="' +
                                f + '" ' + k + ">" + n + "</li>");
                            tpc && V(m, f, e)
                        }
                    });
                if (2 > b(".lcl_tn_inner > li").length) return b("#lcl_thumbs_nav").remove(), !1;
                b(".lcl_tn_inner > li").css("width", l.thumbs_w);
                lcl_on_mobile || b(".lcl_tn_inner").lcl_smoothscroll(.3, 400, !1, !0);
                c && l.tn_icons && b(".lcl_tn_inner").addClass("lcl_tn_mixed_types");
                R(h.elem_index)
            }, V = function (c, d, e) {
                b("<img/>").bind("load", function () {
                    if (!h) return !1;
                    h.img_sizes_cache[c] = {w: this.width, h: this.height};
                    b("#lcl_thumbs_nav[rel=" + e + "] li[rel=" + d + "]").removeClass("lcl_tn_preload");
                    setTimeout(function () {
                        Q();
                        J()
                    }, 500)
                }).attr("src", c)
            }, W = function () {
                var c = 0;
                b(".lcl_tn_inner > li").each(function () {c += b(this).outerWidth(!0)});
                return c
            }, Q = function () {
                if (!b("#lcl_thumbs_nav").length) return !1;
                W() > b(".lcl_tn_inner").width() ? b("#lcl_thumbs_nav").addClass("lcl_tn_has_arr") : b("#lcl_thumbs_nav").removeClass("lcl_tn_has_arr")
            }, J = function () {
                var c = b(".lcl_tn_inner").scrollLeft();
                c ? b(".lcl_tn_prev").removeClass("lcl_tn_disabled_arr").stop(!0).fadeTo(150, 1) : b(".lcl_tn_prev").addClass("lcl_tn_disabled_arr").stop(!0).fadeTo(150,
                    .5);
                c >= W() - b(".lcl_tn_inner").width() ? b(".lcl_tn_next").addClass("lcl_tn_disabled_arr").stop(!0).fadeTo(150, .5) : b(".lcl_tn_next").removeClass("lcl_tn_disabled_arr").stop(!0).fadeTo(150, 1)
            };
        b(document).on("lcl_smoothscroll_end", ".lcl_tn_inner", function (b) {
            if (k != lcl_curr_obj) return !0;
            J()
        });
        var R = function (c) {
            var d = b(".lcl_tn_inner > li[rel=" + c + "]");
            if (!d.length) return !1;
            var e = 0;
            b(".lcl_tn_inner > li").each(function (d, f) {if (b(this).attr("rel") == c) return e = d, !1});
            var f = b(".lcl_tn_inner > li").last().outerWidth(),
                g = parseInt(b(".lcl_tn_inner > li").last().css("margin-left"), 10);
            b(".lcl_tn_inner").width();
            var h = Math.floor((b(".lcl_tn_inner").width() - f - g) / 2);
            f = f * e + g * (e - 1) + Math.floor(g / 2) - h;
            b(".lcl_tn_inner").stop(!0).animate({scrollLeft: f}, 500, function () {b(".lcl_tn_inner").trigger("lcl_smoothscroll_end")});
            b(".lcl_tn_inner > li").removeClass("lcl_sel_thumb");
            d.addClass("lcl_sel_thumb")
        };
        b.fn.lcl_smoothscroll = function (c, d, e, f) {
            if (lcl_on_mobile) return !1;
            this.off("mousemove mousedown mouseup mouseenter mouseleave");
            var g = this, h = "undefined" != typeof e && e ? !1 : !0,
                k = "undefined" != typeof f && f ? !1 : !0, l = !1, p = !1, q = 0, r = 0, t = 0,
                u = 0;
            g.mousemove(function (b) {!0 === p && (g.stop(!0), h && g.scrollLeft(u + (r - b.pageX)), k && g.scrollTop(t + (q - b.pageY)))});
            g.mouseover(function () {l && clearTimeout(l)});
            g.mouseout(function () {l = setTimeout(function () {l = p = !1}, 500)});
            g.mousedown(function (b) {
                "undefined" != typeof lc_sms_timeout && clearTimeout(lc_sms_timeout);
                p = !0;
                t = g.scrollTop();
                u = g.scrollLeft();
                q = b.pageY;
                r = b.pageX
            });
            g.mouseup(function (e) {
                p = !1;
                var f = g.scrollTop(),
                    l = -1 * (t - f);
                f += l * c;
                var m = g.scrollLeft(), n = -1 * (u - m);
                m += n * c;
                if (3 > l && -3 < l && 3 > n && -3 < n) return b(e.target).trigger("lcl_tn_elem_click"), !1;
                if (20 < l || 20 < n) e = {}, k && (e.scrollTop = f), h && (e.scrollLeft = m), g.stop(!0).animate(e, d, "linear", function () {g.trigger("lcl_smoothscroll_end")})
            })
        };
        if (!u.is_arr_instance) if (v.live_elements && u.elems_selector) b(document).off("click", u.elems_selector).on("click", u.elems_selector, function (c) {
            c.preventDefault();
            b.data(k, "lcl_vars").elems_count = b(u.elems_selector).length;
            G(k, b(this));
            k.first().trigger("lcl_clicked_elem", [b(this)])
        }); else k.off("click"), k.on("click", function (c) {
            c.preventDefault();
            G(k, b(this));
            k.first().trigger("lcl_clicked_elem", [b(this)])
        });
        b(document).on("click", "#lcl_overlay:not(.lcl_modal), .lcl_close, #lcl_corner_close", function (b) {
            if (k != lcl_curr_obj) return !0;
            I()
        });
        b(document).on("click", ".lcl_prev", function (b) {
            if (k != lcl_curr_obj) return !0;
            r("prev")
        });
        b(document).on("click", ".lcl_next", function (b) {
            if (k != lcl_curr_obj) return !0;
            r("next")
        });
        b(document).bind("keydown",
            function (c) {
                if (lcl_shown) {
                    if (k != lcl_curr_obj) return !0;
                    39 == c.keyCode ? (c.preventDefault(), r("next")) : 37 == c.keyCode ? (c.preventDefault(), r("prev")) : 27 == c.keyCode ? (c.preventDefault(), I()) : 122 == c.keyCode && l.fullscreen && ("undefined" != typeof lcl_fs_key_timeout && clearTimeout(lcl_fs_key_timeout), lcl_fs_key_timeout = setTimeout(function () {b(".lcl_fullscreen_mode").length ? U() : F()}, 50))
                }
            });
        b(document).on("wheel", "#lcl_overlay, #lcl_window, #lcl_thumbs_nav:not(.lcl_tn_has_arr)", function (c) {
            if (k != lcl_curr_obj || !lcl_curr_opts.mousewheel) return !0;
            var d = b(c.target);
            if (d.is("#lcl_window") || d.parents("#lcl_window").length) {
                var e = !0;
                for (a = 0; 20 > a && !d.is("#lcl_window"); a++) if (d[0].scrollHeight > d.outerHeight()) {
                    e = !1;
                    break
                } else d = d.parent();
                e && (c.preventDefault(), c = c.originalEvent.deltaY, 0 < c ? r("next") : r("prev"))
            } else c.preventDefault(), c = c.originalEvent.deltaY, 0 < c ? r("next") : r("prev")
        });
        b(document).on("click", ".lcl_image_elem", function (c) {
            if (k != lcl_curr_obj) return !0;
            lcl_img_click_track = setTimeout(function () {b(".lcl_zoom_wrap").length || r("next")}, 250)
        });
        b(document).on("dblclick", ".lcl_image_elem", function (c) {
            if (k != lcl_curr_obj || !lcl_curr_opts.img_zoom || !b(".lcl_zoom_icon").length) return !0;
            "undefined" != typeof lcl_img_click_track && (clearTimeout(lcl_img_click_track), zoom(!0))
        });
        b(document).on("click", ".lcl_txt_toggle", function (c) {
            if (k != lcl_curr_obj) return !0;
            c = l;
            if (!lcl_is_active && !b(".lcl_no_txt").length && !b(".lcl_toggling_txt").length) if ("over" != c.data_position) {
                var d = "rside" == c.data_position || "lside" == c.data_position ? !0 : !1,
                    e = b(".lcl_force_txt_over").length,
                    f = 150 > c.animation_time ? c.animation_time : 150, g = 0;
                d && !e ? b("#lcl_subj").fadeTo(f, 0) : e || (b("#lcl_contents_wrap").fadeTo(f, 0), g = f);
                setTimeout(function () {b("#lcl_wrap").toggleClass("lcl_hidden_txt")}, g);
                e || (lcl_is_active = !0, b("#lcl_wrap").addClass("lcl_toggling_txt"), setTimeout(function () {
                    lcl_is_active = !1;
                    lcl_resize()
                }, c.animation_time), setTimeout(function () {
                    b("#lcl_wrap").removeClass("lcl_toggling_txt");
                    d && !e ? b("#lcl_subj").fadeTo(f, 1) : e || b("#lcl_contents_wrap").fadeTo(f, 1)
                }, 2 * c.animation_time + 50))
            } else b("#lcl_wrap").toggleClass("lcl_hidden_txt")
        });
        b(document).on("click", ".lcl_play", function (c) {
            if (k != lcl_curr_obj) return !0;
            b(".lcl_is_playing").length ? lcl_stop_slideshow() : lcl_start_slideshow()
        });
        b(document).on("click", ".lcl_elem", function (c) {
            if (k != lcl_curr_obj) return !0;
            b(".lcl_playing_video").length || -1 === b.inArray(b("#lcl_wrap").attr("lcl-type"), ["video"]) || (lcl_stop_slideshow(), b("#lcl_wrap").addClass("lcl_playing_video"))
        });
        b(document).on("click", ".lcl_socials", function (c) {
            if (k != lcl_curr_obj) return !0;
            if (b(".lcl_socials > div").length) b(".lcl_socials_tt").removeClass("lcl_show_tt"),
                setTimeout(function () {b(".lcl_socials").removeClass("lcl_socials_shown").empty()}, 260); else {
                var d = lcl_curr_vars.elems[lcl_curr_vars.elem_index];
                c = encodeURIComponent(window.location.href);
                var e = encodeURIComponent(d.title).replace(/'/g, "\\'");
                encodeURIComponent(d.txt).replace(/'/g, "\\'");
                if ("image" == d.type) var f = d.src; else f = d.poster ? d.poster : !1, f || "undefined" == typeof d.vid_poster || (f = d.vid_poster);
                var g = '<div class="lcl_socials_tt lcl_tooltip lcl_tt_bottom">';
                g = lcl_curr_opts.fb_direct_share ? g + '<a class="lcl_icon lcl_fb" href="javascript: void(0)"></a>' :
                    g + ('<a class="lcl_icon lcl_fb" onClick="window.open(\'https://www.facebook.com/sharer?u=' + c + "&display=popup','sharer','toolbar=0,status=0,width=590,height=325');\" href=\"javascript: void(0)\"></a>");
                g += '<a class="lcl_icon lcl_twit" onClick="window.open(\'https://twitter.com/share?text=Check%20out%20%22' + e + "%22%20@&url=" + c + "','sharer','toolbar=0,status=0,width=548,height=325');\" href=\"javascript: void(0)\"></a>";
                lcl_on_mobile && (g += '<br/><a class="lcl_icon lcl_wa" href="whatsapp://send?text=' + c +
                    '" data-action="share/whatsapp/share"></a>');
                f && (g += '<a class="lcl_icon lcl_pint" onClick="window.open(\'http://pinterest.com/pin/create/button/?url=' + c + "&media=" + encodeURIComponent(f) + "&description=" + e + "','sharer','toolbar=0,status=0,width=575,height=330');\" href=\"javascript: void(0)\"></a>");
                g += "</div>";
                b(".lcl_socials").addClass("lcl_socials_shown").html(g);
                setTimeout(function () {b(".lcl_socials_tt").addClass("lcl_show_tt")}, 20);
                if (lcl_curr_opts.fb_direct_share) b(document).off("click", ".lcl_fb").on("click",
                    ".lcl_fb", function (b) {
                        FB.ui({
                            method: "share_open_graph",
                            action_type: "og.shares",
                            action_properties: JSON.stringify({
                                object: {
                                    "og:url": window.location.href,
                                    "og:title": d.title,
                                    "og:description": d.txt,
                                    "og:image": f
                                }
                            })
                        }, function (b) {window.close()})
                    })
            }
        });
        b(document).on("click", ".lcl_fullscreen", function (c) {
            if (k != lcl_curr_obj) return !0;
            b(".lcl_fullscreen_mode").length ? U(!0) : F(!0)
        });
        b(document).on("click", ".lcl_thumbs_toggle", function (c) {
            if (k != lcl_curr_obj) return !0;
            c = b(".lcl_fullscreen_mode").length;
            b("#lcl_wrap").addClass("lcl_toggling_tn").toggleClass("lcl_tn_hidden");
            c || setTimeout(function () {lcl_resize()}, 160);
            setTimeout(function () {b("#lcl_wrap").removeClass("lcl_toggling_tn")}, lcl_curr_opts.animation_time + 50)
        });
        v = lcl_on_mobile ? " click" : "";
        b(document).on("lcl_tn_elem_click" + v, ".lcl_tn_inner > li", function (c) {
            if (k != lcl_curr_obj) return !0;
            c = b(this).attr("rel");
            r(c)
        });
        b(document).on("click", ".lcl_tn_prev:not(.lcl_tn_disabled_arr)", function (c) {
            if (k != lcl_curr_obj) return !0;
            b(".lcl_tn_inner").stop(!0).animate({
                scrollLeft: b(".lcl_tn_inner").scrollLeft() - lcl_curr_opts.thumbs_w -
                10
            }, 300, "linear", function () {b(".lcl_tn_inner").trigger("lcl_smoothscroll_end")})
        });
        b(document).on("click", ".lcl_tn_next:not(.lcl_tn_disabled_arr)", function (c) {
            if (k != lcl_curr_obj) return !0;
            b(".lcl_tn_inner").stop(!0).animate({scrollLeft: b(".lcl_tn_inner").scrollLeft() + lcl_curr_opts.thumbs_w + 10}, 300, "linear", function () {b(".lcl_tn_inner").trigger("lcl_smoothscroll_end")})
        });
        b(document).on("wheel", "#lcl_thumbs_nav.lcl_tn_has_arr", function (c) {
            if (k != lcl_curr_obj) return !0;
            c.preventDefault();
            0 < c.originalEvent.deltaY ?
                b(".lcl_tn_prev:not(.lcl_tn_disabled_arr)").trigger("click") : b(".lcl_tn_next:not(.lcl_tn_disabled_arr)").trigger("click")
        });
        b(document).on("contextmenu", "#lcl_wrap *", function () {
            if (k != lcl_curr_obj) return !0;
            if (l.rclick_prevent) return !1
        });
        b(window).on("touchmove", function (c) {
            b(c.target);
            if (!lcl_shown || !lcl_on_mobile || k != lcl_curr_obj) return !0;
            b(c.target).parents("#lcl_window").length || b(c.target).parents("#lcl_thumbs_nav").length || c.preventDefault()
        });
        var Z = function () {
            if ("function" != typeof AlloyFinger) return !1;
            lcl_is_pinching = !1;
            var c = document.querySelector("#lcl_wrap");
            new AlloyFinger(c, {
                singleTap: function (c) {"lcl_overlay" != b(c.target).attr("id") || l.modal || lcl_close()},
                doubleTap: function (b) {
                    b.preventDefault();
                    zoom(!0)
                },
                pinch: function (b) {
                    b.preventDefault();
                    lcl_is_pinching = !0;
                    "undefined" != typeof lcl_swipe_delay && clearTimeout(lcl_swipe_delay);
                    "undefined" != typeof lcl_pinch_delay && clearTimeout(lcl_pinch_delay);
                    lcl_pinch_delay = setTimeout(function () {
                        1.2 < b.scale ? zoom(!0) : .8 > b.scale && zoom(!1);
                        setTimeout(function () {
                            lcl_is_pinching =
                                !1
                        }, 300)
                    }, 20)
                },
                touchStart: function (b) {lcl_touchstartX = b.changedTouches[0].clientX},
                touchEnd: function (c) {
                    var d = lcl_touchstartX - c.changedTouches[0].clientX;
                    if ((-50 > d || 50 < d) && !lcl_is_pinching) {
                        if (b(c.target).parents("#lcl_thumbs_nav").length || b(c.target).parents(".lcl_zoom_wrap").length) return !1;
                        c = b(c.target).parents(".lcl_zoomable").length ? 250 : 0;
                        "undefined" != typeof lcl_swipe_delay && clearTimeout(lcl_swipe_delay);
                        lcl_swipe_delay = setTimeout(function () {-50 > d ? r("prev") : r("next")}, c)
                    }
                }
            })
        }, A = function () {
            if (!lcl_curr_obj) return !1;
            h = b.data(lcl_curr_obj, "lcl_vars");
            l = b.data(lcl_curr_obj, "lcl_settings");
            return h ? !0 : (console.error("LC Lightbox. Object not initialized"), !1)
        };
        lcl_open = function (c, d) {
            var e = h = b.data(c, "lcl_vars");
            if (e) {
                if ("undefined" == typeof e.elems[d]) return console.error("LC Lightbox - cannot open. Unexisting index"), !1;
                e.elem_index = d;
                $clicked_obj = e.is_arr_instance ? !1 : b(c[d]);
                return G(c, $clicked_obj)
            }
            console.error("LC Lightbox - cannot open. Object not initialized");
            return !1
        };
        lcl_resize = function () {
            if (!lcl_shown ||
                lcl_is_active || !A()) return !1;
            var c = h;
            "undefined" != typeof lcl_size_check && clearTimeout(lcl_size_check);
            lcl_size_check = setTimeout(function () {
                b("#lcl_wrap").addClass("lcl_is_resizing");
                J();
                return w(c.elems[c.elem_index])
            }, 20)
        };
        lcl_close = function () {return lcl_shown && !lcl_is_active && A() ? I() : !1};
        lcl_switch = function (b) {return lcl_shown && !lcl_is_active && A() ? r(b) : !1};
        lcl_start_slideshow = function (c) {
            if (!lcl_shown || "undefined" == typeof c && "undefined" != typeof lcl_slideshow || !A()) return !1;
            var d = l;
            if (!d.carousel &&
                h.elem_index == h.elems.length - 1) return !1;
            "undefined" != typeof lcl_slideshow && clearInterval(lcl_slideshow);
            b("#lcl_wrap").addClass("lcl_is_playing");
            var e = d.animation_time + d.slideshow_time;
            S(!0);
            lcl_slideshow = setInterval(function () {
                S(!1);
                r("next", !0)
            }, e);
            "undefined" == typeof c && ("function" == typeof d.slideshow_start && d.slideshow_start.call({
                opts: d,
                vars: h
            }), h.is_arr_instance || (h.elems_selector ? b(h.elems_selector) : lcl_curr_obj).first().trigger("lcl_slideshow_start", [e]));
            return !0
        };
        lcl_stop_slideshow = function () {
            if (!lcl_shown ||
                "undefined" == typeof lcl_slideshow || !A()) return !1;
            var c = l;
            if (!c) return console.error("LC Lightbox. Object not initialized"), !1;
            clearInterval(lcl_slideshow);
            lcl_slideshow = void 0;
            b("#lcl_wrap").removeClass("lcl_is_playing");
            b("#lcl_progressbar").stop(!0).animate({marginTop: -3 * b("#lcl_progressbar").height()}, 300, function () {b(this).remove()});
            "function" == typeof c.slideshow_end && c.slideshow_end.call({opts: l, vars: h});
            h.is_arr_instance || (h.elems_selector ? b(h.elems_selector) : lcl_curr_obj).first().trigger("lcl_slideshow_end",
                []);
            return !0
        };
        return k
    }
})(jQuery);