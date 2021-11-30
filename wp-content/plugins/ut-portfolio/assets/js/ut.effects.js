/* <![CDATA[ */
(function($){

    "use strict";

    // check if is in vc front end
    function inIframe() {

        var field = 'vc_editable';
        var url = window.location.href ;

        if( url.indexOf('?' + field + '=') !== -1 ) {

            return true;

        } else if( url.indexOf('&' + field + '=') !== -1 ) {

            return true;

        } else {

            return false;

        }

    }

    /* New Lazy Loading
	================================================== */
    var portfolio_observer = lozad('.lozad', {

        rootMargin: '100%',
        loaded: function(el) {

            $(el).closest(".ut-portfolio-item").addClass('ut-portfolio-featured-image-loaded').parent().addClass("ut-hide-loader");
            $.force_appear();

        }

    });

    // run observer
    portfolio_observer.observe();

    $(window).on("load", function () {

        // Visual Composer
        if( inIframe() ) {

            portfolio_observer.observe();

        }

    });


    /* document ready
    ================================================== */
    $(document).ready(function(){

        // start preloading
        if( $('.ut-portfolio-featured-image:not(.ut-lazy-wait)').length ) {

            var preloadImages = document.querySelector('.ut-portfolio-featured-image:not(.ut-lazy-wait)');
            portfolio_observer.triggerLoad(preloadImages);

        }

        /* wait for images appear
        ================================================== */
        function get_animated_objects( $all_appeared_elements, effect ) {

            var counter = 0;

            $all_appeared_elements.each(function(){

                if( $(this).hasClass(effect) ) {

                    counter++;

                }

            });

            return counter;

        }

        $('.ut-portfolio-item').appear();

        $(document.body).on('appear', '.ut-portfolio-item', function( event, $all_appeared_elements ) {

            var $this  = $(this),
                effect = $this.data("effect");

            if( $(window).width() <= 1024 ) {

                effect = 'portfolioFadeIn';

            }

            if( !$this.hasClass('ut-portfolio-featured-image-loaded') || !$this.closest('.ut-masonry').hasClass('show') || !$this.closest('.ut-masonry').parent().hasClass('layoutComplete') ) {
                return;
            }

            $this.clearQueue().delay( 100 * ( $all_appeared_elements.index(this) - get_animated_objects( $all_appeared_elements, effect ) ) ).queue( function() {

                $this.closest(".ut-portfolio-article").removeClass("ut-portfolio-article-animation");

                $this.parent('.ut-portfolio-article-animation-box').queue( function() {

                    $(this).css({
                        "visibility" : "visible",
                        "opacity" : "1"
                    }).addClass( effect ).dequeue();

                });

                $this.dequeue();

            });

        });


        /* Lightbox LightGallery Effect
		================================================== */
        if ( site_settings !== "undefined" && site_settings.lg_type === 'lightgallery' ) {

            if( $('a[data-rel^="ut-lightgallery"]').length ) {

                $('.ut-portfolio-wrap').not('.ut-portfolio-packery-wrap').ut_require_js({
                    plugin: 'lightGallery',
                    source: 'lightGallery',
                    callback: function (element) {

                        element.lightGallery({
                            selector: 'a[data-rel^="ut-lightgallery"]',
                            exThumbImage: 'data-exThumbImage',
                            loadVimeoThumbnail: false,
                            loadYoutubeThumbnail: false,
                            youtubePlayerParams: {autoplay: 0},
                            hash: false,
                            download: site_settings.lg_download,
                            mode: site_settings.lg_mode,
                        });

                    }

                });

                $('.ut-portfolio-packery-wrap').ut_require_js({
                    plugin: 'lightGallery',
                    source: 'lightGallery',
                    callback : function ( element ) {

                        element.lightGallery({
                            selector: 'a[data-rel^="ut-lightgallery"]',
                            exThumbImage: 'data-exThumbImage',
                            loadVimeoThumbnail : false,
                            loadYoutubeThumbnail : false,
                            youtubePlayerParams: { autoplay: 0 },
                            hash: false,
                            download: site_settings.lg_download,
                            mode: site_settings.lg_mode,
                        });

                    }

                });

            }

        }

        /* Set Default Text Color for all elements */
        $(".ut-hover").each(function() {

            var text_color = $(this).closest('.ut-portfolio-wrap').data('textcolor');

            $(this).find(".ut-hover-layer").css({ "color" : text_color });
            $(this).find(".ut-hover-layer").find('.portfolio-title').attr('style', 'color: '+text_color );

        });

        $('.ut-hover').on('mouseenter touchstart', function() {

            $(this).find(".ut-hover-layer").css( "opacity" , 1 );

        }).on('mouseleave touchend', function() {

            $(this).find(".ut-hover-layer").css( "opacity" , 0 );

        });


        /* Portfolio Animation
        ================================================== */
        var ut_is_animating = false;

        function update_portfolio_height( wrap , direction ) {

            if( !wrap ) {
                return;
            }

            var height = null;

            if( direction === 'prev' ) {
                height = $('#ut-portfolio-details-'+wrap).find('.active').prev().height();
            }

            if( direction === 'current' ) {
                height = $('#ut-portfolio-details-'+wrap).find('.active').height();
            }

            if( direction === 'next' ) {
                height = $('#ut-portfolio-details-'+wrap).find('.active').next().height();
            }

            $('#ut-portfolio-details-wrap-'+wrap).height( height + 30 );

        }

        /* Update the Portfolio Detail Navigation */
        function update_portfolio_navigation( wrap ) {

            if( !wrap ) {
                return;
            }

            /* lets get the next and previous element */
            var prev = $('#ut-portfolio-details-'+wrap).find('.active').prev('.ut-portfolio-detail'),
                next = $('#ut-portfolio-details-'+wrap).find('.active').next('.ut-portfolio-detail');

            /* show or hide previous button */
            if( !prev.length ) {

                $('#ut-portfolio-details-navigation-'+wrap).find('.prev-portfolio-details').css("visibility" , "hidden");

            } else {

                $('#ut-portfolio-details-navigation-'+wrap).find('.prev-portfolio-details').css("visibility" , "visible");

            }

            /* show or hide next button */
            if( !next.length ) {

                $('#ut-portfolio-details-navigation-'+wrap).find('.next-portfolio-details').css("visibility" , "hidden");

            } else {

                $('#ut-portfolio-details-navigation-'+wrap).find('.next-portfolio-details').css("visibility" , "visible");

            }


        }

        function update_portfolio_height_on_resize() {

            $('.ut-portfolio-detail.active', '.ut-portfolio-details').closest('.ut-portfolio-details-wrap').height( $('.ut-portfolio-detail.active', '.ut-portfolio-details').height() );

        }

        function hide_portfolio_navigation_arrows() {

            $('.ut-portfolio-details-navigation').each(function() {

                var $this = $(this);

                $this.find('.next-portfolio-details').css("opacity", 0);
                $this.find('.prev-portfolio-details').css("opacity", 0);

            });

        }

        function update_portfolio_navigation_position() {

            $('.ut-portfolio-details-navigation').each(function() {

                var $this 			= $(this),
                    $parent 		= $this.next(),
                    $current 		= $parent.find(".active"),
                    media_height 	= $current.find(".ut-portfolio-media").height();

                if( media_height > 0 ) {

                    /* align arrows to media container */
                    $this.find('.next-portfolio-details').animate({top: media_height / 2 + 45 }, function() {

                        $(this).css("opacity", 1);

                    });

                    $this.find('.prev-portfolio-details').animate({top: media_height / 2 + 45 }, function() {

                        $(this).css("opacity", 1);

                    });

                } else {

                    setTimeout( function(){

                        /* align arrows to content container */
                        $this.find('.next-portfolio-details').animate({top: $parent.height() / 2 + 45 }, function() {

                            $(this).css("opacity", 1);

                        });

                        $this.find('.prev-portfolio-details').animate({top: $parent.height() / 2 + 45 }, function() {

                            $(this).css("opacity", 1);

                        });

                    }, 800 );

                }

            });

        }

        if ($.fn.utresize) {

            $(window).utresize(function () {

                update_portfolio_navigation_position();
                update_portfolio_height_on_resize();

            });

        }

        function update_portfolio_height_dynamic( wrap ) {

            if( !wrap ) {
                return;
            }

            setTimeout(function() {

                /* content is larger */
                if( wrap.parent().get(0).offsetHeight < wrap.parent().get(0).scrollHeight ){

                    wrap.parent().height( wrap.parent().get(0).scrollHeight );
                    return;

                    /* content is smaller */
                } else {

                    wrap.parent().height( wrap.height() );
                    return;

                }

            }, 200 );

        }


        function continue_slide_up_animations() {

            setTimeout( function () {

                window.ut_global_wait = false;
                $.force_appear();

            }, 800 );

        }

        /* trigger click on portfolio image since we cannot use direct links (lightbox double images) */
        $(document).on("click", ".ut-portfolio-trigger-link", function( event ) {

            $( $(this).data("trigger") ).trigger('click');

            event.preventDefault();

        });


        /* show portfolio detail */
        $(document).on("click", ".ut-portfolio-link", function(event) {

            if( ut_is_animating ) {
                return false;
            }

            ut_is_animating = true;

            var portfolio_single_id = $(this).data('post'),
                portfolio_wrap = $(this).data('wrap'),
                $portfolio_loader = $('#ut-loader-' + portfolio_wrap),
                $portfolio_wrap = $('#ut-portfolio-details-wrap-' + portfolio_wrap),
                $portfolio_details_anchor = $('#ut-portfolio-details-anchor-'+ portfolio_wrap),
                $portfolio_details = $('#ut-portfolio-details-' + portfolio_wrap),
                $portfolio_details_nav = $('#ut-portfolio-details-navigation-' + portfolio_wrap),
                $portfolio_detail = $portfolio_wrap.find('#ut-portfolio-detail-' + portfolio_single_id),
                current_portfolio_single_id = $portfolio_details.find('.active').data('post'),
                current_portfolio_pformat = $portfolio_details.find('.active').data('format'),
                section_width = $(".ut-portfolio-wrap.ut-portfolio-"+ portfolio_wrap).data("slideup-width"),
                pformat = $portfolio_detail.data("format"),
                grid = '',
                spacing = 60,
                scroll_offset = 0;

            // anchor to scroll to
            var $anchor = '';

            if( $portfolio_wrap.is(":visible") ) {

                $anchor = $portfolio_wrap;
                scroll_offset = $('#header-section').outerHeight();
                spacing = 40;

            } else {

                $anchor = $(".ut-portfolio-"+ portfolio_wrap);

            }

            $portfolio_loader.stop(true).fadeIn( 200 , function() {

                $.scrollTo( $anchor , 400 , {  easing: 'linear' , offset: -scroll_offset-$portfolio_details_nav.outerHeight()-spacing-120, 'axis':'y' , onAfter : function(){

                        /* reset video on current portfolio */
                        if( current_portfolio_pformat === 'video' ) {
                            utResetVideo( $portfolio_details, current_portfolio_single_id );
                        }

                        /* destroy slider */
                        if( current_portfolio_pformat === 'gallery' ) {
                            utResetGallery( $portfolio_details , current_portfolio_single_id );
                        }

                        /* we need some extra padding for fullwidth layouts / sections */
                        if( section_width === "centered" ) {

                            $portfolio_details.addClass('grid-container');
                            $('#ut-portfolio-details-navigation-' + portfolio_wrap).addClass('grid-container');
                            grid = "grid-100";

                        } else {

                            grid = 'ut-portfolio-detail-fullwidth';

                        }

                        /* reset content as well */
                        if( portfolio_single_id !== current_portfolio_single_id ) {
                            utResetContent( current_portfolio_single_id );
                        }

                        /* hide all portfolio items first */
                        $portfolio_details.find('.ut-portfolio-detail.active').attr("class", "animated ut-portfolio-detail clearfix").addClass(grid).fadeOut(200);

                        /* create single portfolio detail */
                        $portfolio_detail.attr("class", "animated ut-portfolio-detail clearfix").addClass(grid).addClass('active').css("visibility" , "hidden").show().slideDown( 400 , 'linear' , function() {

                            /* box holds a slider , so we need to "recall" it */
                            if( pformat === 'gallery' ) {

                                utInitFlexSlider( portfolio_single_id , $portfolio_details , function() {

                                    utInitPortfolioContent( portfolio_single_id, portfolio_wrap, function() {

                                        $portfolio_loader.fadeOut( 200 , function() {

                                            /* activate wrap */
                                            $portfolio_wrap.addClass('show');

                                            /* now make portfolio detaials visible and adjust the portfolio navigation */
                                            $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                                /* now show the portfolio navigation*/
                                                $portfolio_details_nav.slideDown().addClass('show').data( "single" , portfolio_single_id );

                                                $(window).trigger("resize");

                                                /* set details height */
                                                setTimeout(function() {

                                                    /* update portfolio detail navigation */
                                                    update_portfolio_navigation( portfolio_wrap );

                                                    $portfolio_wrap.height( $portfolio_details.height() + 40 ).addClass('overflow-visible');
                                                    $portfolio_detail.css("visibility" , "visible");

                                                    continue_slide_up_animations();

                                                }, 100);

                                                /* trigger scroll for lazy image load */
                                                $(window).trigger("scroll");

                                                /* reset animating global */
                                                ut_is_animating = false;

                                                setTimeout(function() {

                                                    $.scrollTo( $anchor , 400 , {  easing: 'linear' , offset: -scroll_offset-$portfolio_details_nav.outerHeight()-spacing-120, 'axis':'y' });

                                                }, 200 );

                                            });

                                        });

                                    });

                                });

                            } else if( pformat === 'video' ) {

                                utInitVideoPlayer( portfolio_single_id , function() {

                                    utInitPortfolioContent( portfolio_single_id, portfolio_wrap , function() {

                                        $portfolio_loader.fadeOut( 200 , function() {

                                            /* activate wrap */
                                            $portfolio_wrap.addClass('show');

                                            /* now make portfolio detaials visible and adjust the portfolio navigation */
                                            $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                                /* now show the portfolio navigation*/
                                                $portfolio_details_nav.slideDown().addClass('show').data( "single" , portfolio_single_id );

                                                $(window).trigger("resize");

                                                /* set details height */
                                                setTimeout(function() {

                                                    /* update portfolio detail navigation */
                                                    update_portfolio_navigation( portfolio_wrap );

                                                    $portfolio_wrap.height( $portfolio_details.height() + 40 ).addClass('overflow-visible');
                                                    $portfolio_detail.css("visibility" , "visible").addClass("animated zoomIn");

                                                    continue_slide_up_animations();

                                                }, 100);

                                                /* trigger scroll for lazy image load */
                                                $(window).trigger("scroll");

                                                /* reset animating global */
                                                ut_is_animating = false;

                                                setTimeout(function() {

                                                    $.scrollTo( $anchor , 400 , {  easing: 'linear' , offset: -scroll_offset-$portfolio_details_nav.outerHeight()-spacing-120, 'axis':'y' });

                                                }, 200 );

                                            });

                                        });

                                    });

                                });

                            } else {

                                utInitPortfolioImage( portfolio_single_id , $portfolio_details , function() {

                                    utInitPortfolioContent( portfolio_single_id, portfolio_wrap , function() {

                                        $portfolio_loader.fadeOut( 200 , function() {

                                            /* activate wrap */
                                            $portfolio_wrap.addClass('show');

                                            /* now make portfolio detaials visible and adjust the portfolio navigation */
                                            $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                                /* now show the portfolio navigation*/
                                                $portfolio_details_nav.slideDown().addClass('show').data( "single" , portfolio_single_id );

                                                $(window).trigger("resize");

                                                /* set details height */
                                                setTimeout(function() {

                                                    /* update portfolio detail navigation */
                                                    update_portfolio_navigation( portfolio_wrap );

                                                    $portfolio_wrap.height( $portfolio_details.height() + 40 ).addClass('overflow-visible');
                                                    $portfolio_detail.css("visibility" , "visible").addClass("animated zoomIn");

                                                    continue_slide_up_animations();

                                                }, 100 );

                                                /* trigger scroll for lazy image load */
                                                $(window).trigger("scroll");

                                                /* reset animating global */
                                                ut_is_animating = false;

                                                setTimeout(function() {

                                                    $.scrollTo( $anchor , 400 , {  easing: 'linear' , offset: -scroll_offset-$portfolio_details_nav.outerHeight()-spacing-120, 'axis':'y' });

                                                }, 200 );

                                            });

                                        });

                                    });

                                });

                            }

                        });

                    }});

            });

            event.preventDefault();

        });



        /* next portfolio item */
        $(document).on("click", ".next-portfolio-details", function(event) {

            event.preventDefault();

            if( ut_is_animating ) {
                return false;
            }

            ut_is_animating = true;

            var portfolio_wrap = $(this).data('wrap'),
                $portfolio_wrap = $('#ut-portfolio-details-wrap-' + portfolio_wrap),
                section_width = $(".ut-portfolio-wrap.ut-portfolio-"+ portfolio_wrap).data("slideup-width"),
                grid = '',
                $portfolio_details = $('#ut-portfolio-details-' + portfolio_wrap),
                $portfolio_loader = $('#ut-loader-' + portfolio_wrap),

                next_portfolio_single_id = $portfolio_details.find('.active').next().data('post'),
                next_portfolio_pformat = $portfolio_details.find('.active').next().data('format'),
                current_portfolio_single_id = $portfolio_details.find('.active').data('post'),
                current_portfolio_pformat = $portfolio_details.find('.active').data('format'),
                $portfolio_detail = $portfolio_details.find('#ut-portfolio-detail-' + next_portfolio_single_id);

            /* we need some extra padding for fullwidth layouts / sections */
            if( section_width === "centered" ) {

                $portfolio_details.addClass('grid-container');
                $('#ut-portfolio-details-navigation-' + portfolio_wrap).addClass('grid-container');
                grid = "grid-100";

            } else {

                grid = 'ut-portfolio-detail-fullwidth';

            }

            /* hide arrows */
            hide_portfolio_navigation_arrows();

            /* reset content as well */
            utResetContent( current_portfolio_single_id );

            /* hide all current portfolio first */
            $portfolio_details.find('#ut-portfolio-detail-'+current_portfolio_single_id).attr("class", "animated ut-portfolio-detail clearfix").addClass(grid).addClass("BrooklynFadeOutRightSlideUp").delay(1000).fadeOut( 400, function(){

                /* reset video on current portfolio */
                if( current_portfolio_pformat === 'video' ) {
                    utResetVideo( $portfolio_details, current_portfolio_single_id );
                }

                /* destroy slider */
                if( current_portfolio_pformat === 'gallery' ) {
                    utResetGallery( $portfolio_details , current_portfolio_single_id );
                }

                $(this).removeClass("BrooklynFadeOutRightSlideUp");

                $portfolio_loader.stop(true).fadeIn( 200 , function() {

                    /* create single portfolio detail */
                    $portfolio_detail.addClass('active').addClass(grid).css("visibility" , "hidden").show().slideDown( 400 , 'linear' , function() {

                        /* box holds a slider , so we need to "recall" it */
                        if( next_portfolio_pformat === 'gallery' ) {

                            utInitFlexSlider( next_portfolio_single_id , $portfolio_details , function() {

                                utInitPortfolioContent( next_portfolio_single_id, portfolio_wrap , function() {

                                    $portfolio_loader.fadeOut( 200 , function() {

                                        /* update portfolio navigation*/
                                        $portfolio_details.find('.ut-portfolio-details-navigation').data( "single" , next_portfolio_single_id );

                                        /* now make portfolio detaials visible and adjust the portfolio navigation */
                                        $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                            $(window).trigger("resize");

                                            /* set details height */
                                            setTimeout(function() {

                                                /* update portfolio detail navigation */
                                                update_portfolio_navigation( portfolio_wrap );

                                                $portfolio_wrap.height( $portfolio_details.height() + 40 );
                                                $portfolio_detail.css("visibility" , "visible").addClass("BrooklynFadeInLeftSlideUp");

                                                continue_slide_up_animations();

                                            }, 100);

                                            /* trigger scroll for lazy image load */
                                            $(window).trigger("scroll");

                                            ut_is_animating = false;

                                        });

                                    });

                                });

                            });

                        } else if( next_portfolio_pformat === 'video' ) {

                            utInitVideoPlayer( next_portfolio_single_id , function() {

                                utInitPortfolioContent( next_portfolio_single_id, portfolio_wrap , function() {

                                    $portfolio_loader.fadeOut( 200 , function() {

                                        /* update portfolio navigation*/
                                        $portfolio_details.find('.ut-portfolio-details-navigation').data( "single" , next_portfolio_single_id );

                                        /* now make portfolio detaials visible and adjust the portfolio navigation */
                                        $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                            $(window).trigger("resize");

                                            /* set details height */
                                            setTimeout(function() {

                                                /* update portfolio detail navigation */
                                                update_portfolio_navigation( portfolio_wrap );

                                                $portfolio_wrap.height( $portfolio_details.height() + 40 );
                                                $portfolio_detail.css("visibility" , "visible").addClass("BrooklynFadeInLeftSlideUp");

                                                continue_slide_up_animations();

                                            }, 100);

                                            ut_is_animating = false;

                                        });

                                    });

                                });

                            });

                        } else {

                            utInitPortfolioImage( next_portfolio_single_id , $portfolio_details , function() {

                                utInitPortfolioContent( next_portfolio_single_id, portfolio_wrap , function() {

                                    $portfolio_loader.fadeOut( 200 , function() {

                                        /* update portfolio navigation*/
                                        $portfolio_details.find('.ut-portfolio-details-navigation').data( "single" , next_portfolio_single_id );

                                        /* now make portfolio detaials visible and adjust the portfolio navigation */
                                        $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                            $(window).trigger("resize");

                                            /* set details height */
                                            setTimeout(function() {

                                                /* update portfolio detail navigation */
                                                update_portfolio_navigation( portfolio_wrap );

                                                $portfolio_wrap.height( $portfolio_details.height() + 40 );
                                                $portfolio_detail.css("visibility" , "visible").addClass("BrooklynFadeInLeftSlideUp");

                                                continue_slide_up_animations();

                                            }, 100);

                                            ut_is_animating = false;

                                        });

                                    });

                                });

                            });

                        } /* end if */

                    });

                });

            });

        });


        /* prev portfolio item */
        $(document).on("click", ".prev-portfolio-details", function(event) {

            event.preventDefault();

            if( ut_is_animating ) {
                return;
            }

            ut_is_animating = true;

            var portfolio_wrap = $(this).data('wrap'),
                $portfolio_wrap = $('#ut-portfolio-details-wrap-' + portfolio_wrap),
                section_width = $(".ut-portfolio-wrap.ut-portfolio-"+ portfolio_wrap).data("slideup-width"),
                grid = '',
                $portfolio_details = $('#ut-portfolio-details-' + portfolio_wrap),
                $portfolio_loader = $('#ut-loader-' + portfolio_wrap),
                prev_portfolio_single_id = $portfolio_details.find('.active').prev().data('post'),
                prev_portfolio_pformat = $portfolio_details.find('.active').prev().data('format'),
                current_portfolio_single_id = $portfolio_details.find('.active').data('post'),
                current_portfolio_pformat = $portfolio_details.find('.active').data('format'),
                $portfolio_detail = $portfolio_details.find('#ut-portfolio-detail-' + prev_portfolio_single_id);

            /* we need some extra padding for fullwidth layouts / sections */
            if( section_width === "centered" ) {

                $portfolio_details.addClass('grid-container');
                $('#ut-portfolio-details-navigation-' + portfolio_wrap).addClass('grid-container');
                grid = "grid-100";

            } else {

                grid = 'ut-portfolio-detail-fullwidth';

            }

            /* hide arrows */
            hide_portfolio_navigation_arrows();

            /* reset content as well */
            utResetContent( current_portfolio_single_id );

            /* hide all current portfolio first */
            $portfolio_details.find('#ut-portfolio-detail-'+ current_portfolio_single_id ).attr("class", "animated ut-portfolio-detail clearfix ").addClass(grid).addClass("BrooklynFadeOutLeftSlideUp").delay(1000).fadeOut( 400, function(){

                /* reset video on current portfolio */
                if( current_portfolio_pformat === 'video' ) {
                    utResetVideo( $portfolio_details , current_portfolio_single_id );
                }

                /* destroy slider */
                if( current_portfolio_pformat === 'gallery' ) {
                    utResetGallery( $portfolio_details , current_portfolio_single_id );
                }

                $(this).removeClass("BrooklynFadeOutLeftSlideUp");

                $portfolio_loader.stop(true).fadeIn( 200 , function() {

                    /* create single portfolio detail */
                    $portfolio_detail.addClass('active').addClass(grid).css("visibility" , "hidden").show().slideDown( 400 , 'linear' , function() {

                        /* box holds a slider , so we need to "recall" it */
                        if( prev_portfolio_pformat === 'gallery' ) {

                            utInitFlexSlider( prev_portfolio_single_id , $portfolio_details , function() {

                                utInitPortfolioContent( prev_portfolio_single_id, portfolio_wrap , function() {

                                    $portfolio_loader.fadeOut( 200 , function() {

                                        /* update portfolio navigation*/
                                        $portfolio_details.find('.ut-portfolio-details-navigation').data( "single" , prev_portfolio_single_id );

                                        /* now make portfolio detaials visible and adjust the portfolio navigation */
                                        $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                            $(window).trigger("resize");

                                            /* set details height */
                                            setTimeout(function() {

                                                /* update portfolio detail navigation */
                                                update_portfolio_navigation( portfolio_wrap );

                                                $portfolio_wrap.height( $portfolio_details.height() + 40 );
                                                $portfolio_detail.css("visibility" , "visible").addClass("BrooklynFadeInRightSlideUp");

                                                continue_slide_up_animations();

                                            }, 100);

                                            /* trigger scroll for lazy image load */
                                            $(window).trigger("scroll");

                                            ut_is_animating = false;

                                        });

                                    });

                                });

                            });

                        } else if( prev_portfolio_pformat === 'video' ) {

                            utInitVideoPlayer( prev_portfolio_single_id , function() {

                                utInitPortfolioContent( prev_portfolio_single_id, portfolio_wrap , function() {

                                    $portfolio_loader.fadeOut( 200 , function() {

                                        /* update portfolio navigation*/
                                        $portfolio_details.find('.ut-portfolio-details-navigation').data( "single" , prev_portfolio_single_id );

                                        /* now make portfolio detaials visible and adjust the portfolio navigation */
                                        $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                            $(window).trigger("resize");

                                            /* set details height */
                                            setTimeout(function() {

                                                /* update portfolio detail navigation */
                                                update_portfolio_navigation( portfolio_wrap );

                                                $portfolio_wrap.height( $portfolio_details.height() + 40 );
                                                $portfolio_detail.css("visibility" , "visible").addClass("BrooklynFadeInRightSlideUp");

                                                continue_slide_up_animations();

                                            }, 100);

                                            ut_is_animating = false;

                                        });

                                    });

                                });

                            });

                        } else {

                            utInitPortfolioImage( prev_portfolio_single_id , $portfolio_details , function() {

                                utInitPortfolioContent( prev_portfolio_single_id, portfolio_wrap , function() {

                                    $portfolio_loader.fadeOut( 200 , function() {

                                        /* update portfolio navigation*/
                                        $portfolio_details.find('.ut-portfolio-details-navigation').data( "single" , prev_portfolio_single_id );

                                        /* now make portfolio detaials visible and adjust the portfolio navigation */
                                        $portfolio_detail.animate({ opacity: 1 } , 100 , 'linear' , function() {

                                            $(window).trigger("resize");

                                            /* set details height */
                                            setTimeout(function() {

                                                /* update portfolio detail navigation */
                                                update_portfolio_navigation( portfolio_wrap );

                                                $portfolio_wrap.height( $portfolio_details.height() + 40 );
                                                $portfolio_detail.css("visibility" , "visible").addClass("BrooklynFadeInRightSlideUp");

                                                continue_slide_up_animations();

                                            }, 100);

                                            ut_is_animating = false;

                                        });

                                    });

                                });

                            });

                        } /* end if */

                    });

                });

            });



        });


        /* close portfolio detail */
        $(document).on("click", ".close-portfolio-details", function(event) {

            event.preventDefault();

            if( ut_is_animating ) {
                return false;
            }

            ut_is_animating = true;

            var portfolio_wrap = $(this).data('wrap'),
                portfolio_single_id = $(this).parent().data("single"),
                $portfolio_wrap = $('#ut-portfolio-details-wrap-'+ portfolio_wrap ),
                section_width = $(".ut-portfolio-wrap.ut-portfolio-"+ portfolio_wrap).data("slideup-width"),
                grid = '',
                $portfolio_details_nav = $('#ut-portfolio-details-navigation-'+ portfolio_wrap ),
                pformat	= $('#ut-portfolio-detail-'+portfolio_single_id).data("format");

            if( section_width === "centered" ) {

                grid = "grid-100";

            } else {

                grid = 'ut-portfolio-detail-fullwidth';

            }

            hide_portfolio_navigation_arrows();

            /* hide navigation */
            $portfolio_details_nav.removeClass('show').fadeOut( 400, function(){

                $(this).slideUp();

            });

            /* fade portfolio out */
            $portfolio_wrap.find('#ut-portfolio-detail-'+ portfolio_single_id ).attr("class", "animated ut-portfolio-detail clearfix").addClass(grid).addClass("zoomOut").animate({ opacity: 0 } , 200 , 'linear' , function(){

                $(this).removeClass("zoomOut").css("visibility" , "hidden").hide();

                /* collapse portfolio */
                $portfolio_wrap.height(0).delay(800).queue(function(){

                    $(this).removeClass('show').removeClass('overflow-visible').dequeue();

                    /* reset video if needed */
                    if( pformat === 'video' ) {
                        utResetVideo( $portfolio_wrap , portfolio_single_id );
                    }

                    if( pformat === 'gallery' ) {
                        utResetGallery( $portfolio_wrap , portfolio_single_id );
                    }

                    /* reset content as well */
                    utResetContent( portfolio_single_id );

                    ut_is_animating = false;

                });


            });


        });

        function utResetContent( portfolio_single_id ) {

            if( !portfolio_single_id ) {
                return;
            }

            var $portfolio_detail = $("#ut-portfolio-detail-"+portfolio_single_id);

            // destroy lightgallery
            if( $portfolio_detail.data('lightGallery') ) {

                $portfolio_detail.data('lightGallery').destroy(true);

            }

            $portfolio_detail.find(".entry-content").fadeOut( 600 , function() {

                /* remove video */
                $(this).html("");
                $(this).show();

            });

            // $portfolio_detail.hide();

        }

        function utResetVideo( $portfolio_wrap , portfolio_single_id ) {

            if( !portfolio_single_id ) {
                return;
            }

            $portfolio_wrap.find("#ut-video-call-"+portfolio_single_id).fadeOut( -600 , function() {

                /* remove video */
                $(this).html("");

            });

        }

        function utResetGallery( $portfolio_wrap , portfolio_single_id ) {

            if( !portfolio_single_id ) {
                return;
            }

            if( $portfolio_wrap.find('#portfolio-gallery-slider-'+portfolio_single_id).hasClass("ut-sliderimages-loaded") ) {
                // $portfolio_wrap.find('#portfolio-gallery-slider-'+portfolio_single_id).flexslider('destroy');
            }

        }

        /* load portfolio single content */
        function utInitPortfolioContent( postID, portfolioID , callback ) {

            if( !postID ) {
                return;
            }

            var $portfolio = $('#ut-portfolio-detail-' + postID);

            // block all animations
            window.ut_global_wait = true;

            $.ajax({
                type: 'POST',
                url: utPortfolio.ajaxurl,
                data: {
                    "portfolio_id": postID,
                    "action": "ut_get_portfolio_post_content",
                    "show_title" : $('.ut-portfolio-'+portfolioID).data("slideup-title"),
                },
                success: function(response) {

                    // attach new content
                    $portfolio.find(".entry-content").html( response );

                    // apply shortcodes
                    UT_Shortcodes.init();
                    UT_Adaptive_Images.init_images();

                    /* Custom Cursor
                    ================================================== */
                    $('#ut-hover-cursor').ut_require_js({
                        plugin: 'UT_Animated_Cursor',
                        source: 'customcursor',
                        ieblock: true, // no IE support
                        callback: function () {

                            new window.UT_Animated_Cursor( document.getElementById("ut-hover-cursor") );

                        }
                    });

                    return false;

                },
                complete : function() {

                    if (callback && typeof(callback) === "function") {
                        callback();
                    }

                }

            });

        }

        /* activate portfolio single player */
        function utInitVideoPlayer( postID , callback ) {

            if( !postID ) {
                return;
            }

            var $portfolio = $('#ut-portfolio-detail-' + postID ),
                ajaxURL = utPortfolio.ajaxurl;

            // no media has been set
            if( !$portfolio.find(".ut-video-call").length ) {

                callback();
                return;

            }

            $.ajax({
                type: 'POST',
                url: ajaxURL,
                data: {"action": "ut_get_portfolio_post", portfolio_id: postID },
                success: function(response) {

                    $portfolio.find(".ut-video-call").show().html(response).ut_require_js({
                        plugin: 'fitVids',
                        source: 'fitVids',
                        callback: function (element) {

                            element.fitVids();

                        }
                    });

                    return false;

                },
                complete : function() {

                    if (callback && typeof(callback) === "function") {
                        callback();
                    }

                }

            });

        }

        /* load portfolio single image */
        function utInitPortfolioImage( postID , $wrapOBJ , callback ) {

            if(!postID) {
                return;
            }

            // no media has been set
            if( !$wrapOBJ.find("#ut-portfolio-detail-"+postID).children(".ut-portfolio-media").length ) {
                callback();
            }

            var $img = $wrapOBJ.find("#ut-portfolio-detail-"+postID).find(".ut-load-me"),
                url  = $img.data("original");

            /* image has not been set yet */
            if( !$img.attr('src') ) {

                $img.attr('src', url ).one('load', function() {

                    if (callback && typeof(callback) === "function") {

                        callback();

                    }

                });

                /* image has been set, no need to load it again */
            } else {

                if (callback && typeof(callback) === "function") {

                    callback();

                }

            }

        }

        /* activate portfolio single slider */
        function utInitFlexSlider( postID , $wrapOBJ , callback ) {

            if(!postID) {
                return;
            }

            // no media has been set
            if( !$wrapOBJ.find("#ut-portfolio-detail-"+postID).children(".ut-portfolio-media").length ) {

                callback();
                return;

            }

            var $slider = $wrapOBJ.find('#portfolio-gallery-slider-'+postID);

            /* check if slider images were loaded previously */
            if( $slider.hasClass("ut-sliderimages-loaded") ) {

                $slider.ut_require_js({
                    plugin: 'flexslider',
                    source: 'flexslider',
                    callback : function ( element ) {

                        element.flexslider({

                            animation: 'fade',
                            controlNav: false,
                            animationLoop: true,
                            slideshow: false,
                            smoothHeight: true,
                            startAt: 0,
                            after: function(){

                                update_portfolio_height_dynamic( $wrapOBJ );
                                update_portfolio_navigation_position();

                            }

                        });

                    }

                });

                if (callback && typeof(callback) === "function") {

                    callback();
                    return;

                }

            }

            var $elems = $slider.find('.ut-load-me'), count = $elems.length;

            if( count ) {

                /* load images first */
                $elems.each(function() {

                    var $this = $(this),
                        url = $this.data("original");

                    $this.attr('src', url ).removeClass('ut-load-me').one('load', function() {

                        if ( !--count ) {

                            $slider.ut_require_js({
                                plugin: 'flexslider',
                                source: 'flexslider',
                                callback : function ( element ) {

                                    element.flexslider({

                                        animation: 'fade',
                                        controlNav: false,
                                        animationLoop: true,
                                        slideshow: false,
                                        smoothHeight: true,
                                        startAt: 0,
                                        after: function(){

                                            update_portfolio_height_dynamic( $wrapOBJ );
                                            update_portfolio_navigation_position();

                                        }

                                    }).addClass("ut-sliderimages-loaded");

                                }

                            });

                            if (callback && typeof(callback) === "function") {

                                callback();
                                return;

                            }

                        }

                    });

                });

                if (callback && typeof(callback) === "function") {

                    callback();
                    return;

                }

            } else {

                if (callback && typeof(callback) === "function") {

                    callback();
                    return;

                }

            }

        }

        $('.ut-portfolio-article-tilt:not(.ut-carousel-item) .ut-portfolio-item').ut_require_js({
            plugin: 'tilt',
            source: 'tilt',
            callback: function (element) {

                var tilt = element.tilt({
                    maxTilt : 10,
                    perspective: '1000'
                });

                element.on('tilt.mouseLeave', function(){

                    element.closest('.ut-portfolio-article').removeClass('ut-tilt-active');

                });

                element.on('tilt.mouseEnter', function(){

                    element.closest('.ut-portfolio-article').addClass('ut-tilt-active');

                });

                element.on('click', function(){

                    // tilt.tilt.reset.call(tilt);

                });

            }

        });

        $('.ut-portfolio-article-tilt.ut-carousel-item').ut_require_js({
            plugin: 'tilt',
            source: 'tilt',
            callback: function (element) {

                var tilt = element.tilt({
                    maxTilt : 10,
                    perspective: '1000'
                });

                element.on('tilt.mouseLeave', function(){

                    element.removeClass('ut-tilt-active');

                });

                element.on('tilt.mouseEnter', function(){

                    element.addClass('ut-tilt-active');

                });

                element.on('click', function(){

                    // tilt.tilt.reset.call(tilt);

                });

            }

        });

    });

})(jQuery);
/* ]]> */