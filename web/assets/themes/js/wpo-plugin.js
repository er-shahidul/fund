/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Opal  Team <opalwordpressl@gmail.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
 
 ;(function($){
    'use strict';

    var WPOMenu = function(elm, options){
        this.$menu = $(elm);
        if (!this.$menu.length){
            return;
        }

        this.options = $.extend({}, $.fn.wpomenu.defaults, options);
        this.child_open = [];
        this.loaded = false;

        this.start();
    };

    WPOMenu.prototype = {
        constructor: WPOMenu,

        start: function () {
            //init once
            if (this.loaded){
                return;
            }
            this.loaded = true;

            //start
            var self = this,
                options = this.options,
                $menu = this.$menu;

            this.$items = $menu.find('li');
            this.$items.each(function (idx, li) {

                var $item = $(this),
                    $child = $item.children('.dropdown-menu'),
                    $link = $item.children('a'),
                    item = {
                        $item: $item,
                        child: $child.length,
                        link: $link.length,
                        clickable: !($link.length && $child.length),
                        mega: $item.hasClass('mega'),
                        status: 'close',
                        timer: null,
                        atimer: null
                    };

                //store
                $item.data('wpomenu.item', item);

                //click action
                if ($child.length && !options.hover) {
                    $item.on('click', function (e) {
                        e.stopPropagation();

                        if ($item.hasClass('group')) {
                            return;
                        }

                        if (item.status == 'close') {
                            e.preventDefault();
                            self.show(item);
                        }
                    });
                } else {

                    //stop if click on menu item - prevent bubble event
                    $item.on('click', function (e) {
                        e.stopPropagation()
                    });
                }

                if (options.hover) {
                    $item.on('mouseover', function (e) {
                        if ($item.hasClass('group')) {
                            return;
                        }

                        e.stopPropagation();
                        self.show(item);

                    }).on('mouseleave', function (e) {
                        if ($item.hasClass('group')) {
                            return;
                        }
                        e.stopPropagation();
                        self.hide(item);
                    });

                    //if has child, don't goto link before open child - fix for touch screen
                    if ($link.length && $child.length) {
                        $link.on('click', function (e) {
                            return item.clickable;
                        });
                    }
                }

            });

            $(document.body).on('tap hideall.wpomenu', function(e){
                clearTimeout(self.timer);
                self.timer = setTimeout($.proxy(self.hide_alls, self), e.type == 'tap' ? 500 : self.options.hidedelay);
            });
        },

        show: function (item) {
            // hide all others menu of this instance
            if($.inArray(item, this.child_open) < this.child_open.length -1){
                this.hide_others(item);
            }

            // hide all for other instances as well
            $(document.body).trigger('hideall.wpomenu', [this]);

            clearTimeout(this.timer);       //hide alls
            clearTimeout(item.timer);       //hide this item
            clearTimeout(item.ftimer);  //on hidden
            clearTimeout(item.ctimer);  //on hidden

            if(item.status != 'open' || !item.$item.hasClass('open') || !this.child_open.length){
                if(item.mega){
                    //remove timer
                    clearTimeout(item.astimer); //animate
                    clearTimeout(item.atimer);  //animate

                    //place menu
                    this.position(item.$item);

                    // add class animate
                    item.astimer = setTimeout(function(){item.$item.addClass('animating')}, 10);
                    item.atimer = setTimeout(function(){item.$item.removeClass('animating')}, this.options.duration + 50);
                    item.timer = setTimeout(function(){item.$item.addClass('open')}, 100);

                } else {
                    item.$item.addClass('open');
                }

                item.status = 'open';
                if (item.child && $.inArray(item, this.child_open) == -1) {
                    this.child_open.push(item);
                }
            }

            item.ctimer = setTimeout($.proxy(this.clickable, this, item), 300);
        },

        hide: function (item) {
            clearTimeout(this.timer);       //hide alls
            clearTimeout(item.timer);       //hide this item
            clearTimeout(item.astimer); //animate timer
            clearTimeout(item.atimer);  //animate timer
            clearTimeout(item.ftimer);  //on hidden

            if(item.mega){
                //animate out
                item.$item.addClass('animating');
                item.atimer = setTimeout(function(){item.$item.removeClass('animating')}, this.options.duration);
                item.timer = setTimeout(function(){item.$item.removeClass('open')}, 100);
            } else {
                item.$item.removeClass('open');
            }

            item.status = 'close';
            for (var i = this.child_open.length; i--;){
                if (this.child_open[i] === item){
                    this.child_open.splice(i, 1);
                }
            }

            item.ftimer = setTimeout($.proxy(this.hidden, this, item), this.options.duration);
            this.timer = setTimeout($.proxy(this.hide_alls, this), this.options.hidedelay);
        },

        hidden: function (item) {
            //hide done
            if (item.status == 'close') {
                item.clickable = false;
            }
        },

        hide_others: function (item) {
            var self = this;
            $.each(this.child_open.slice(), function (idx, open) {
                if (!item || (open != item && !open.$item.has(item.$item).length)) {
                    self.hide(open);
                }
            });
        },

        hide_alls: function (e, inst) {
            if(!e || e.type == 'tap' || (e.type == 'hideall' && this != inst)){
                var self = this;
                $.each(this.child_open.slice(), function (idx, item) {
                    item && self.hide(item);
                });
            }
        },

        clickable: function (item) {
            item.clickable = true;
        },

        position: function ($item) {
            var sub = $item.find('>.dropdown-menu'),
                is_show = sub.is(':visible');

            if(!is_show){
                sub.show();
            }
            var offset = $item.offset(),
                width = $item.outerWidth(),
                screen_width = $(window).width() - this.options.sb_width,
                sub_width = sub.outerWidth(),
                level = $item.data('level');

            if(!is_show){
                sub.css('display', '');
            }

            //reset custom align
            sub.css({left : '', right : ''});

            if(level == 1){
                var align = $item.data('alignsub'),
                    align_offset = 0,
                    align_delta = 0,
                    align_trans = 0;
                if(align == 'fullwidth'){
                    return; //do nothing
                }

                if(!align){
                    align = 'left';
                }

                if(align == 'center'){
                    align_offset = offset.left + (width /2);

                    if(!$.support.t3transform){
                        align_trans = -sub_width /2;
                        sub.css(this.options.rtl ? 'right' : 'left', align_trans + width /2);
                    }

                } else {
                    align_offset = offset.left + ((align == 'left' && this.options.rtl || align == 'right' && !this.options.rtl) ? width : 0);
                }
                
                if (this.options.rtl) {
                    if(align == 'right'){
                        if(align_offset + sub_width > screen_width){
                            align_delta = screen_width - align_offset - sub_width;
                            sub.css('left', align_delta);

                            if(screen_width < sub_width){
                                sub.css('left', align_delta + sub_width - screen_width);
                            }
                        }
                    } else {
                        if(align_offset < (align == 'center' ? sub_width /2 : sub_width)){
                            align_delta = align_offset - (align == 'center' ? sub_width /2 : sub_width);
                            sub.css('right', align_delta + align_trans);
                        }

                        if(align_offset + (align == 'center' ? sub_width /2 : 0) - align_delta > screen_width){
                            sub.css('right', align_offset + (align == 'center' ? (sub_width + width) /2 : 0) + align_trans - screen_width);
                        }
                    }

                } else {
                    if(align == 'right'){
                        if(align_offset < sub_width){
                            align_delta = align_offset - sub_width;
                            sub.css('right', align_delta);

                            if(sub_width > screen_width){
                                sub.css('right', sub_width - screen_width + align_delta);
                            }
                        }
                    } else {
                        if(align_offset + (align == 'center' ? sub_width /2 : sub_width) > screen_width){

                            align_delta = screen_width - align_offset -(align == 'center' ? sub_width /2 : sub_width);
                            sub.css('left', align_delta + align_trans);
                        }

                        if(align_offset - (align == 'center' ? sub_width /2 : 0) + align_delta < 0){
                            sub.css('left', (align == 'center' ? (sub_width + width) /2 : 0) + align_trans - align_offset);
                        }
                    }
                }
            } else {

                if (this.options.rtl) {
                    if ($item.closest('.mega-dropdown-menu').parent().hasClass('mega-align-right')) {

                        //should be align to the right as parent
                        $item.removeClass('mega-align-left').addClass('mega-align-right');

                        // check if not able => revert the direction
                        if (offset.left + width + sub_width > screen_width) {
                            $item.removeClass('mega-align-right'); //should we add align left ? it is th default now

                            if(offset.left - sub_width < 0){
                                sub.css('right', offset.left + width - sub_width);
                            }
                        }
                    } else {
                        if (offset.left - sub_width < 0) {
                            $item.removeClass('mega-align-left').addClass('mega-align-right');

                            if(offset.left + width + sub_width > screen_width){
                                sub.css('left', screen_width - offset.left - sub_width);
                            }
                        }
                    }
                } else {

                    if ($item.closest('.mega-dropdown-menu').parent().hasClass('mega-align-right')) {
                        //should be align to the right as parent
                        $item.removeClass('mega-align-left').addClass('mega-align-right');

                        // check if not able => revert the direction
                        if (offset.left - sub_width < 0) {
                            $item.removeClass('mega-align-right'); //should we add align left ? it is th default now

                            if(offset.left + width + sub_width > screen_width){
                                sub.css('left', screen_width - offset.left - sub_width);
                            }
                        }
                    } else {

                        if (offset.left + width + sub_width > screen_width) {
                            $item.removeClass('mega-align-left').addClass('mega-align-right');

                            if(offset.left - sub_width < 0){
                                sub.css('right', offset.left + width - sub_width);
                            }
                        }
                    }
                }
            }
        }
    };

    $.fn.wpomenu = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('megamenu'),
                options = typeof option == 'object' && option;

            if (!data) {
                $this.data('megamenu', (data = new WPOMenu(this, options)));

            } else {
                if (typeof option == 'string' && data[option]){
                    data[option]()
                }
            }
        })
    };

    $.fn.wpomenu.defaults = {
        duration: 400,
        timeout: 100,
        hidedelay: 200,
        hover: true,
        sb_width: 20
    };


    //apply script
    $(document).ready(function(){

        //detect settings
        var mm_duration = $('#wpo-mainnav').data('duration') || 0;

        var mm_timeout = mm_duration ? 100 + mm_duration : 500,
            mm_rtl = $(document.documentElement).attr('dir') == 'rtl',
            mm_trigger = true,
            sb_width = (function () {
            var parent = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body'),
                child = parent.children(),
                width = child.innerWidth() - child.height(100).innerWidth();

            parent.remove();

            return width;
        })();

        //lt IE 10
        // if(!$.support.transition){
        //     //it is not support animate
        //     $('.t3-megamenu').removeClass('animate');

        //     mm_timeout = 100;
        // }

        //get ready
        $('.nav').has('.dropdown-menu').wpomenu({
            duration: mm_duration,
            timeout: mm_timeout,
            rtl: mm_rtl,
            sb_width: sb_width,
            hover: mm_trigger
        });


        $(window).load(function(){

            //check we miss any nav
            $('.nav').has('.dropdown-menu').wpomenu({
                duration: mm_duration,
                timeout: mm_timeout,
                rtl: mm_rtl,
                sb_width: sb_width,
                hover: mm_trigger
            });

        });
    });

})(jQuery);



;(function ($, window, undefined) {
    // outside the scope of the jQuery plugin to
    // keep track of all dropdowns
    var $allDropdowns = $();

    // if instantlyCloseOthers is true, then it will instantly
    // shut other nav items when a new one is hovered over
    $.fn.dropdownHover = function (options) {
        // don't do anything if touch is supported
        // (plugin causes some issues on mobile)
        if('ontouchstart' in document) return this; // don't want to affect chaining

        // the element we really care about
        // is the dropdown-toggle's parent
        $allDropdowns = $allDropdowns.add(this.parent());

        return this.each(function () {
            var $this = $(this),
                $parent = $this.parent(),
                defaults = {
                    delay: 500,
                    instantlyCloseOthers: true
                },
                data = {
                    delay: $(this).data('delay'),
                    instantlyCloseOthers: $(this).data('close-others')
                },
                showEvent   = 'show.bs.dropdown',
                hideEvent   = 'hide.bs.dropdown',
                // shownEvent  = 'shown.bs.dropdown',
                // hiddenEvent = 'hidden.bs.dropdown',
                settings = $.extend(true, {}, defaults, options, data),
                timeout;

            $parent.hover(function (event) {
                // so a neighbor can't open the dropdown
                if(!$parent.hasClass('open') && !$this.is(event.target)) {
                    // stop this event, stop executing any code
                    // in this callback but continue to propagate
                    return true;
                }

                openDropdown(event);
            }, function () {
                timeout = window.setTimeout(function () {
                    $parent.removeClass('open');
                    $this.trigger(hideEvent);
                }, settings.delay);
            });

            // this helps with button groups!
            $this.hover(function (event) {
                // this helps prevent a double event from firing.
                // see https://github.com/CWSpear/bootstrap-hover-dropdown/issues/55
                if(!$parent.hasClass('open') && !$parent.is(event.target)) {
                    // stop this event, stop executing any code
                    // in this callback but continue to propagate
                    return true;
                }

                openDropdown(event);
            });

            // handle submenus
            $parent.find('.dropdown-submenu').each(function (){
                var $this = $(this);
                var subTimeout;
                $this.hover(function () {
                    window.clearTimeout(subTimeout);
                    $this.children('.dropdown-menu').show();
                    // always close submenu siblings instantly
                    $this.siblings().children('.dropdown-menu').hide();
                }, function () {
                    var $submenu = $this.children('.dropdown-menu');
                    subTimeout = window.setTimeout(function () {
                        $submenu.hide();
                    }, settings.delay);
                });
            });

            function openDropdown(event) {
                $allDropdowns.find(':focus').blur();

                if(settings.instantlyCloseOthers === true)
                    $allDropdowns.removeClass('open');

                window.clearTimeout(timeout);
                $parent.addClass('open');
                $this.trigger(showEvent);
            }
        });
    };

    $(document).ready(function () {
        // apply dropdownHover to all elements with the data-hover="dropdown" attribute
        $('[data-hover="dropdown"]').dropdownHover();
    });
})(jQuery, this);

var WPO_Plugin = window.WPO_Plugin || {};

!function ($) {
	"use strict";
	$.extend(WPO_Plugin, {
		mm_Duration: $('#wpo-mainnav').data('duration') || 0,
		mm_Animation:$('#wpo-mainnav').data('animation') || '',
        oc_Variables:{
            wrapper : $('body'),
            inner : $('.wpo-wrapper'),
            toggles : $('.off-canvas-toggle'),
            offcanvas : $('.wpo-off-canvas'),
            close : $('.wpo-off-canvas .close'),
            btn:null,
            nav:null,
            direction : 'left',
            fixed : null
        },
		init: function(){
			// Tooltip Bootstrap
			$("[data-toggle='tooltip']").tooltip();

			// Blog Tabs Element Builder
			WPO_Plugin.BlogTab();

			//Megamenu
			//WPO_Plugin.MegamenuHover();
			WPO_Plugin.MegamenuDuration();
            WPO_Plugin.ocInit();
		},
        // Offcanvas
        // 
        ocInit : function(){
            // no wrapper, just exit
            if (!WPO_Plugin.oc_Variables.wrapper.length) return ;

            // add effect class for nav
            WPO_Plugin.oc_Variables.toggles.each (function () {
                var $this = $(this);
                WPO_Plugin.oc_Variables.nav = $($this.data('nav'));
                var effect = $this.data('effect');
                WPO_Plugin.oc_Variables.direction = ($('html').attr('dir') == 'rtl' && $this.data('pos')!='right') || ($('html').attr('dir') != 'rtl' && $this.data('pos')=='right')  ? 'right':'left';
                WPO_Plugin.oc_Variables.nav.addClass (effect).addClass ('off-canvas-'+WPO_Plugin.oc_Variables.direction);

                // move to outside wrapper-content
                var inside_effect = ['off-canvas-effect-3','off-canvas-effect-16','off-canvas-effect-7','off-canvas-effect-8','off-canvas-effect-14'];
                if ($.inArray(effect, inside_effect) == -1) {
                    WPO_Plugin.oc_Variables.inner.before(WPO_Plugin.oc_Variables.nav);
                } else {
                    WPO_Plugin.oc_Variables.inner.prepend(WPO_Plugin.oc_Variables.nav);
                }
            });

            WPO_Plugin.oc_Variables.toggles.click (function(e){
                // detect direction

                WPO_Plugin.stopBubble (e);
                if (WPO_Plugin.oc_Variables.wrapper.hasClass ('off-canvas-open')) {
                    WPO_Plugin.oc_Variables.oc_hide (e);
                    return;
                }

                WPO_Plugin.oc_Variables.btn = $(this);
                WPO_Plugin.oc_Variables.nav = $(WPO_Plugin.oc_Variables.btn.data('nav'));
                WPO_Plugin.oc_Variables.fixed = WPO_Plugin.oc_Variables.inner.find('*').filter (function() {return $(this).css("position") === 'fixed';});

                WPO_Plugin.oc_Variables.nav.addClass ('off-canvas-current');

                WPO_Plugin.oc_Variables.direction = ($('html').attr('dir') == 'rtl' && WPO_Plugin.oc_Variables.btn.data('pos')!='right') || ($('html').attr('dir') != 'rtl' && WPO_Plugin.oc_Variables.btn.data('pos')=='right')  ? 'right':'left';

                // add direction class to body
                // $('html').removeClass ('off-canvas-left off-canvas-right').addClass ('off-canvas-' + direction);

                WPO_Plugin.oc_Variables.offcanvas.height($(window).height());


                // disable scroll on page
                var scrollTop = ($('html').scrollTop()) ? $('html').scrollTop() : $('body').scrollTop(); // Works for Chrome, Firefox, IE...
                $('html').addClass('noscroll').css('top',-scrollTop).data('top', scrollTop);
                $('.wpo-off-canvas').css('top',scrollTop);

                // make the fixed element become absolute
                WPO_Plugin.oc_Variables.fixed.each (function () {
                    var $this = $(this),
                        $parent = $this.parent(),
                        mtop = 0;
                    // find none static parent
                    while (!$parent.is(WPO_Plugin.oc_Variables.inner) && $parent.css("position") === 'static') $parent = $parent.parent();
                    mtop = -$parent.offset().top;
                    $this.css ({'position': 'absolute', 'margin-top': mtop});
                });

                WPO_Plugin.oc_Variables.wrapper.scrollTop (scrollTop);
                // update effect class
                WPO_Plugin.oc_Variables.wrapper[0].className = WPO_Plugin.oc_Variables.wrapper[0].className.replace (/\s*off\-canvas\-effect\-\d+\s*/g, ' ').trim() +
                    ' ' + WPO_Plugin.oc_Variables.btn.data('effect') + ' ' + 'off-canvas-' + WPO_Plugin.oc_Variables.direction;

                setTimeout(WPO_Plugin.oc_show, 50);

                return;
            });
        },
        //WPO_Plugin.oc_Variables.
        oc_show : function () {
            WPO_Plugin.oc_Variables.wrapper.addClass ('off-canvas-open');
            WPO_Plugin.oc_Variables.wrapper.on ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.close.on ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.offcanvas.on ('click', WPO_Plugin.stopBubble);

            // fix for old ie
            if ($.browser.msie && $.browser.version < 10) {
                var p1 = {}, p2 = {};
                p1['padding-'+direction] = $('.wpo-off-canvas').width();
                p2[direction] = 0;
                WPO_Plugin.oc_Variables.inner.animate (p1);
                WPO_Plugin.oc_Variables.nav.animate (p2);
            }
        },
        oc_hide : function () {
            WPO_Plugin.oc_Variables.wrapper.removeClass ('off-canvas-open');
            WPO_Plugin.oc_Variables.wrapper.off ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.close.off ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.offcanvas.off ('click', WPO_Plugin.stopBubble);
            setTimeout (function (){
                WPO_Plugin.oc_Variables.wrapper.removeClass (WPO_Plugin.oc_Variables.btn.data('effect')).removeClass ('off-canvas-'+WPO_Plugin.oc_Variables.direction);
                WPO_Plugin.oc_Variables.wrapper.scrollTop (0);
                // enable scroll
                $('html').removeClass ('noscroll').css('top', '');
                $('html,body').scrollTop ($('html').data('top'));
                WPO_Plugin.oc_Variables.nav.removeClass ('off-canvas-current');
                // restore fixed elements
                WPO_Plugin.oc_Variables.fixed.css ({'position': '', 'margin-top': ''});
            }, 550);

            // fix for old ie
            if ($.browser.msie && $.browser.version < 10) {
                var p1 = {}, p2 = {};
                p1['padding-'+direction] = 0;
                p2[direction] = -$('.wpo-off-canvas').width();
                WPO_Plugin.oc_Variables.inner.animate (p1);
                WPO_Plugin.oc_Variables.nav.animate (p2);
            }
        },

        stopBubble : function (e) {
            if($(e.target).hasClass('dropdown-toggle')){
                
            }else{
                e.stopPropagation();
            }
            
        },
		BlogTab : function(){
			$('[data-toggle="blog-tab"]').each(function(index, el) {
				var contain = $(this);
				var recent = contain.find('.recent-item');
				var child = contain.find('.category-child a');
				recent.hide();
				contain.find('.recent-item:first-child').show();
				child.hover(function(){
					var id = $(this).attr('data-item');
					recent.hide();
					contain.find(id).show();
				});
			});
		},
		MegamenuDuration: function(){
			if(WPO_Plugin.mm_Duration){
				$('<style type="text/css">' +
					'.wpo-megamenu.animate .animating > .dropdown-menu,' +
					'.wpo-megamenu.animate.slide .animating > .dropdown-menu > div {' +
						'transition-duration: ' + WPO_Plugin.mm_Duration + 'ms;' +
						'-webkit-transition-duration: ' + WPO_Plugin.mm_Duration + 'ms;' +
                        '-moz-transition-duration: ' + WPO_Plugin.mm_Duration + 'ms;' +
					'}' +
				'</style>').appendTo ('head');
			}
		}
	});

	$(document).ready(function(){  
		WPO_Plugin.init();
	});

    $(window).resize(function(){
        WPO_Plugin.oc_Variables.offcanvas.height($(window).height());
    });

}(jQuery);
