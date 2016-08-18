(function ($) {
	"use strict";
jQuery(document).ready(function(){
    jQuery(window).scroll(function(){
        if (jQuery(this).scrollTop() > 100) {
            jQuery('.return-top').fadeIn();
        } else {
            jQuery('.return-top').fadeOut();
    }
});

jQuery('.return-top').click(function(){
    jQuery("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});

var Barometer = new Object ( function() {

        var $barometers = $('.barometer'), 

        isInView = function($el) {
            var docViewTop = $(window).scrollTop(), 
                docViewBottom = docViewTop + $(window).height(), 
                elemTop = $el.offset().top,
                elemBottom = elemTop + $el.height();

            return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom)
                && (elemBottom <= docViewBottom) &&  (elemTop >= docViewTop) );
        }, 

        customArc = function (xloc, yloc, value, total, R) {
            var alpha = 360 / total * value,
                a = (90 - alpha) * Math.PI / 180,
                x = xloc + R * Math.cos(a),
                y = yloc - R * Math.sin(a),
                path;

            if (total == value) {
                path = [
                    ["M", xloc, yloc - R],
                    ["A", R, R, 0, 1, 1, xloc - 0.01, yloc - R]
                ];
            } else {
                path = [
                    ["M", xloc, yloc - R],
                    ["A", R, R, 0, +(alpha > 180), 1, x, y]
                ];
            }
            return {
                path: path
            };
        },

        // Draws a barometer
        drawBarometer = function($barometer, r, width, height, progress_val) {          
            var progress;

            // Draw the percentage filled arc
            if ( progress_val > 0 ) {
                progress = r.path().attr({ 
                    stroke: $barometer.data('progress-stroke'), 
                    'stroke-width' : $barometer.data('strokewidth')+1, 
                    arc: [width/2, height/2, 0, 100, (width/2)-8]
                });

                // Animate it
                progress.animate({
                    arc: [width/2, height/2, progress_val, 100, (width/2)-8]
                }, 1500, "easeInOut", function() {
                    $barometer.find('span').animate( { opacity: 1}, 300, 'linear');
                });
            }           
        }, 

        // Init barometer
        initBarometer = function($barometer) {
            var width = $barometer.data('width'), 
                height = $barometer.data('height'),                 
                r = Raphael( $barometer[0], width, height),
                drawn = false,                          
                progress_val = $barometer.data('progress') > 100 ? 100 : $barometer.data('progress'),
                circle;

            // @see http://stackoverflow.com/questions/5061318/drawing-centered-arcs-in-raphael-js
            r.customAttributes.arc = customArc;

            // Draw the main circle
            circle = r.path().attr({
                stroke: $barometer.data('stroke'), 
                'stroke-width' : $barometer.data('strokewidth'), 
                arc: [width/2, height/2, 0, 100, (width/2)-8]
            });

            // Fill the main circle
            $barometer.parent().addClass('barometer-added');
            circle.animate({ arc: [width/2, height/2, 100, 100, (width/2)-8] }, 1000, function() {
                if ( progress_val === 0 ) {
                    $barometer.find('span').animate( { opacity: 1}, 500, 'linear' );
                }                   
            });

            if ( isInView($barometer) ) {
                drawBarometer($barometer, r, width, height, progress_val);

                drawn = true;
            }
            else {
                $(window).scroll( function() {
                    if ( drawn === false && isInView($barometer) ) {
                        drawBarometer($barometer, r, width, height, progress_val);

                        drawn = true;
                    }
                });
            }
        };

        return {

            init : function() {
                $barometers.each( function() {
                    initBarometer( $(this) );
                });                 
            },

            getBarometers : function() {
                return $barometers;
            },

            _drawBarometer:function(barometer, r, width, height, progress_val){
                drawBarometer(barometer, r, width, height, progress_val);
            }
        }

    })();

    $(window).load(function() {
        Barometer.init();
    })


    //
    $("[data-progress-animation]").each(function() {
        var $this = $(this);
        $this.appear(function() {
          var delay = ($this.attr("data-appear-animation-delay") ? $this.attr("data-appear-animation-delay") : 1);
          if(delay > 1) $this.css("animation-delay", delay + "ms");
          setTimeout(function() { $this.animate({width: $this.attr("data-progress-animation")}, 800);}, delay);
        }, {accX: 0, accY: -50});
      });

    /*
    **  script call event change custom price
    */
    $('.edd_download_purchase_form').on('click', '.pledge-level', function() {
            $(this).parents('.edd_price_options').find('input[name=atcf_custom_price]').val( $(this).data('pri') );
            $(this).parents('.edd_price_options').find('.pledge-level').removeClass('active');
            $(this).addClass('active');
        })
        .on('change', 'input[name=atcf_custom_price]', function() {
            var pledge = $(this).val(), 
                $minpledge = $('.edd_download_purchase_form .pledge-level').first(),                
                $maxpledge;

            if ( $minpledge.length === 0 ) {
                return;
            }
            if ( $minpledge.data('pri') > pledge ) {
                alert( 'Your pledge must be at least the minimum pledge amount.' );
                
                $minpledge.find('input').prop('checked', true);
                $minpledge.change();

                return;
            }           

            $('.edd_download_purchase_form .pledge-level').each( function() {

                if ( $(this).data('pri') <= pledge && $(this).hasClass('not-available') === false ) {
                    $maxpledge = $(this);
                } 
                else {                                      
                    return false;
                }
            });

            $maxpledge.find('input').prop('checked', true);
        });

    $(document).ready(function(){
        $('.edd_download_purchase_form').on('change', '.edd_price_options_input', function() {
        })   
        $('.single-download .edd_price_options.expired').parent().addClass('hidden');
    }) 

    $(document).ready(function(){
        if($('.blog-masonry').length > 0){
            jQuery('.blog-masonry').isotope({
                layoutMode: 'masonry',
                itemSelector: '.isotope-item',
            });
        }
        if($('.blog-post-detail > .entry-thumb').length > 0){
            $('.blog-post-detail > .entry-thumb').each(function(){
                if($(this).children().size() == 1 )
                    $(this).addClass('hidden');
            });
        }

        //mobile click
        $('.mobile-toggle').click(function () {
            if( jQuery(this).parent().children().hasClass('show') ){
                jQuery(this).parent().children().removeClass('show');
            }else
                jQuery(this).parent().children().addClass('show');
        });

            
    })    

      

    jQuery(document).ready(function() {
        if($('.isotope').length > 0){
            //portfolio isotope filter
            var container = jQuery('.isotope');
            var filter = jQuery('.isotope-filter');
            var $duration = container.data('isotope-duration');
            container.isotope({
                filter : '*',
                animationOptions : {duration: $duration}
            });
            filter.find('a').click(function() {
                var selector = jQuery(this).attr('data-filter');
                filter.find('a').removeClass('active');
                jQuery(this).addClass('active');
                container.isotope({
                    filter: selector,
                    animationOptions:{
                        animationDuration: $duration,
                        queue: false
                    }
                });
                return false;
            });

            jQuery(window).load(function(){
                container.isotope("layout");
            });
        }
    });    
    
    $.fn.wrapStart = function(numWords){
        return this.each(function(){
            var $this = $(this);
            var node = $this.contents().filter(function(){
                return this.nodeType == 3;
            }).first(),
            text = node.text().trim(),
            first = text.split(' ', 1).join(" ");
            if (!node.length) return;
            node[0].nodeValue = text.slice(first.length);
            node.before('<b>' + first + '</b>');
        });
    }; 

    jQuery(document).ready(function() {
        $('.mod-heading .widget-title > span').wrapStart(1);

        $('.panel-default').on('show.bs.collapse', function () {
            $(this).find('.panel-heading').addClass('active');
        });

        $('.panel-default').on('hide.bs.collapse', function () {
            $(this).find('.panel-heading').removeClass('active');
        });
    })    

})(jQuery)