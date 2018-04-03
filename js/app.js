/*!
 * Gear HTML5 Audio Player
 * http://flashedge.net
 *
 * Version: 1.2.2
 * Updated: 07/09/2014 15.25.41
 *
 * @license Copyright (c) 2014. All rights reserved.
 * @author: Emanuele Manco, hello@flashedge.net
 */

(function ($, window, document, undefined) {
    'use strict';

    /**
     * Tries to show browser's promt for enabling flash
     *
     * Chrome starting from 56 version and Edge from 15 are disabling flash
     * by default. To promt user to enable flash, they suggest to send user to
     * flash player download page. Then this browser will catch such request
     * and show a promt to user:
     * https://www.chromium.org/flash-roadmap#TOC-Developer-Recommendations
     * In this method we are forcing such promt by navigating user to adobe
     * site in iframe, instead of top window
     */
    function requestFlashPermission() {
        var iframe = document.createElement('iframe');
        iframe.src = 'https://get.adobe.com/flashplayer';
        iframe.sandbox = '';
        document.body.appendChild(iframe);
        document.body.removeChild(iframe);
    }


    var isNewEdge = (navigator.userAgent.match(/Edge\/(\d+)/) || [])[1] > 14;
    var isNewSafari = (navigator.userAgent.match(/OS X (\d+)/) || [])[1] > 9;
    var isNewChrome = (navigator.userAgent.match(/Chrom(e|ium)\/(\d+)/) || [])[2] > 56
        && !/Mobile/i.test(navigator.userAgent);
    var canRequestPermission = isNewEdge || isNewSafari || isNewChrome;

    if (canRequestPermission) {
        requestFlashPermission();
        // Chrome requires user's click in order to allow iframe embeding
        $(window).one('click', requestFlashPermission);
    }

    $(document).ready(function () {

        init();

        function init() {

            var f = $(document).foundation(),
                g = $('.gearWrap').gearPlayer();

            g.ready(function () {

                // player is ready and we can apply our events
                $('.intro>a').click(function (e) {
                    e.preventDefault();
                    g.show(); // this shows the player interface
                });

            });

            // offcanvas function
            function toggle() {

                if ($('.stage').hasClass('side') || close == true) {

                    TweenMax.to($('.stage>.wrap, .stage>.overlay>span'), 0.5, {
                        x: 0,
                        ease: Cubic.easeInOut,
                        clearProps: "transform"
                    });
                    TweenMax.to($('.stage>.overlay'), 0.5, {
                        autoAlpha: 0, onComplete: function () {
                            $('body').css({'overflow-y': 'inherit'});
                            $('.offcanvas-menu').css({'display': 'none', 'visibility': 'hidden'})
                        }
                    });

                    $('.stage').removeClass('side');

                } else {

                    TweenMax.to($('.stage>.wrap, .stage>.overlay>span'), 0.5, {
                        x: -$('.offcanvas-menu').width(),
                        ease: Cubic.easeInOut
                    });
                    TweenMax.to($('.stage>.overlay'), 0.5, {
                        autoAlpha: 1, onComplete: function () {
                            $('body').css({'overflow-y': 'hidden'})
                        }
                    });

                    window.scrollTo(0, 0);

                    $('.stage').addClass('side');
                    $('.offcanvas-menu').css({'display': 'block', 'visibility': 'visible'});
                }
            }

            // offcanvas event
            $('.offcanvas-toggle, .stage>.overlay').click(function () {
                toggle();
            });


            // rollover effects
            $('.over i').each(function () {
                TweenMax.set($(this), {scaleX: 0, scaleY: 0});
            });

            $('.cover').hover(function () {
                TweenMax.to($(this).children('.over'), 0.5, {alpha: 1});
                TweenMax.to($(this).children('.over').children('i'), 0.5, {scaleX: 1, scaleY: 1});
            }, function () {
                TweenMax.to($(this).children('.over'), 0.5, {alpha: 0});
                TweenMax.to($(this).children('.over').children('i'), 0.5, {scaleX: 0, scaleY: 0});
            });


            // this scrolls each anchor tag to the desired location
            $("a[href*=#]").click(function (e) {
                e.preventDefault();
                var a = $(this).attr('href'),
                    offset = $(window).width() > 640 ? 100 : 50;  // offset for mobile or desktop

                TweenMax.to(window, 2, {
                    scrollTo: {y: $(a).position().top - offset, autoKill: false},
                    ease: Cubic.easeInOut
                });
                if ($(this).parent().parent().parent().hasClass('offcanvas-menu')) {
                    toggle();
                }
                ; // for offcanvas
            });

            // put here your custom scripts, if you want to use this file for your stuff

        }

    });

    $.noConflict();

}(jQuery, this, this.document));






