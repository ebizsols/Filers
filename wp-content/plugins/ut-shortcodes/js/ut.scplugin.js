/* <![CDATA[ */
(function($){

	"use strict";

	/* detect IE */
	function is_ms_ie() {

		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("MSIE ");

		return msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./);

	}

	/* helper functions
    ================================================== */
	function create_id() {
		return '-' + Math.random().toString(36).substr(2, 9);
	}

	/* check if is in vc front end
    ================================================== */
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

	/* inView / outView Class
    ================================================== */
	function InViewObserver( event, isInView ) {

		if (isInView) {

			$(event.target).removeClass("outView").addClass("inView");

		} else {

			$(event.target).removeClass("inView").addClass("outView");

		}

	}

	/* Image Observer for Images
    ================================================== */
	var UTImageObserver = lozad('.ut-lazy', {
		rootMargin: '100%',
		loaded: function(el) {

			var $element = $(el);

			$element.delay( 600 ).queue( function(){

				$element.addClass("ut-image-loaded");
				$element.siblings('.ut-box-shadow-lazy').addClass('ut-box-shadow-ready');

				$element.closest(".ut-image-gallery-item").addClass("ut-image-loaded");
				$element.closest(".ut-animate-image").addClass("ut-animate-image-ready").trigger('inview', _isElementInViewport($element) );

				$.force_appear();

			});

		}

	});

	/* Image Observer for Single Images
    ================================================== */
	var UTSimpleImageObserver = lozad( '.ut-lozad',{
		rootMargin: '100%'
	});

	/* Image Observer for Background Images
    ================================================== */
	var UTBackgroundImageObserver = lozad('.ut-background-lozad', {
		rootMargin: '100%',
		load: function(element) {

			if( !$(element).hasClass('ut-pseudo-background') && element.getAttribute('data-background-image') ) {

				element.style.backgroundImage = 'url(\'' + element.getAttribute('data-background-image').split(',').join('\'),url(\'') + '\')';
				// UT_Adaptive_Images.load_responsive_background_image(element);

			} else if( $(element).hasClass('ut-pseudo-background') && element.getAttribute('data-background-image') ) {

				element.classList.add('ut-pseudo-background-loaded');

			}

		}

	});

	// run after page load
	$(window).on("load", function () {

		UTImageObserver.observe();
		UTSimpleImageObserver.observe();
		UTBackgroundImageObserver.observe();

	});

	if( inIframe() ) {

		setInterval(function () {

			$.force_appear();

		}, 100 )

	}

	/* Helper Function for Appearing Elements
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

	/* Letter Effects
    ================================================== */
	var ut_letter_effects = {

		'effect-1' : {
			scale: [0.3,1],
			opacity: [0,1],
			translateZ: 0,
			easing: "easeOutExpo",
			duration: 600,
			delay: function(el, i) {
				return 70 * (i+1);
			}
		},
		'effect-2' : {
			scale: [4,1],
			opacity: [0,1],
			translateZ: 0,
			easing: "easeOutExpo",
			duration: 950,
			delay: function(el, i) {
				return 70*i;
			}
		},
		'effect-3' : {
			opacity: [0,1],
			easing: "easeInOutQuad",
			duration: 2250,
			delay: function(el, i) {
				return 150 * (i+1);
			}
		},
		'effect-4' : {
			translateY: ["1.1em", 0],
			translateZ: 0,
			duration: 750,
			delay: function(el, i) {
				return 50 * i;
			}
		},
		'effect-5' : {
			translateY: ["1.1em", 0],
			translateX: ["0.55em", 0],
			translateZ: 0,
			rotateZ: [180, 0],
			duration: 750,
			easing: "easeOutExpo",
			delay: function(el, i) {
				return 50 * i;
			}
		},
		'effect-6' : {
			scale: [0, 1],
			duration: 1500,
			elasticity: 600,
			delay: function(el, i) {
				return 45 * (i+1);
			}
		},
		'effect-7' : {
			rotateY: [-90, 0],
			duration: 1300,
			delay: function(el, i) {
				return 45 * i;
			}
		},
		'effect-8' : {
			translateX: [40,0],
			translateZ: 0,
			opacity: [0,1],
			easing: "easeOutExpo",
			duration: 1200,
			delay: function(el, i) {
				return 50 + 30 * i;
			}
		},
		'effect-10' : {
			translateY: [100,0],
			translateZ: 0,
			opacity: [0,1],
			easing: "easeOutExpo",
			duration: 1400,
			delay: function(el, i) {
				return 50 + 30 * i;
			}
		},
		'effect-9' : {
			opacity: [0,1],
			translateX: [40,0],
			translateZ: 0,
			scaleX: [0.3, 1],
			easing: "easeOutExpo",
			duration: 800,
			delay: function(el, i) {
				return 150 + 25 * i;
			}
		},
		'effect-11' : {
			translateY: [-100,0],
			easing: "easeOutExpo",
			duration: 1400,
			delay: function(el, i) {
				return 30 * i;
			}
		}

	};

	// Shortcode Functions
	window.UT_Shortcodes = {

		delay_this 			: true,
		start_delay			: false,
		content    			: '',
		google_maps_loaded	: false,

		isJson: function (str) {

			try {

				JSON.parse(str);

			} catch (e) {

				return false;

			}

			return true;

		},

		isHTML: function(str) {

			// var doc = new DOMParser().parseFromString(str, "text/html");
			// return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);

		},

		init: function (content) {

			if (typeof content !== undefined && typeof content === 'string' && !this.isJson( content ) ) {

				this.content = content;

			}

			this.init_fitvids();
			this.init_accordion();
			this.init_tabs();
			this.init_revealfx();
			this.init_progress_circles();
			this.init_skill_bars();
			this.init_count_up();
			this.init_animated_image();
			this.init_owl_gallery_slider();
			this.init_owl_testimonials();
			this.init_brands();
			this.init_word_rotators();
			this.init_icon_draw();
			this.init_parallax_quote();
			this.init_pie_charts();
			this.init_appear_effects();
			this.init_list_animation();
			this.init_image_gallery();
			this.init_react_carousel();
			this.init_portfolio_carousel();
			this.init_timeline();
			this.init_social_follow();
			this.init_twitter_rotator();
			this.init_distortion();
			this.init_lightbox();
			this.init_video_lightbox();
			this.init_morphbox();
			this.init_yt_background_video();
			this.init_vimeo_background_video();
			this.init_google_maps();
			this.init_glitch();

		},

		/* Create Collection of DOM Elements
        ================================================== */
		create_collection: function( $elements ) {

			var $content_elements = $([]);

			$elements.each(function () {

				$content_elements = $content_elements.add( $('#' + $(this).attr('id') ) );

			});

			return $content_elements;

		},

		/* Init fitVids
        ================================================== */
		init_fitvids: function() {

			$(".ut-video:not(.ut-initialized), .entry-content:not(.ut-initialized), .ut-post-media:not(.ut-initialized), .entry-thumbnail:not(.ut-initialized)").ut_require_js({
				plugin: 'fitVids',
				source: 'fitVids',
				callback: function (element) {

					if( $(element).hasClass('ut-initialized') ) {
						return;
					}

					element.addClass('ut-initialized').fitVids();

				}
			});

		},

		/* Init Accordion
        ================================================== */
		init_accordion: function() {

			$('.ut-accordion-module').not('.ut-initialized').ut_require_js({
				plugin: 'accordion',
				source: 'accordion',
				callback: function( element ) {

					element.each( function( index, current ) {

						if( $(current).hasClass('ut-initialized') ) {
							return true;
						}

						$(current).addClass('ut-initialized').find('.ut-accordion-module-item').accordion({
							"transitionSpeed"  : $(current).find('.ut-accordion-module-item').data('transition'),
							"singleOpen"       : $(current).hasClass('ut-accordion-module-group'),
							"transitionEasing" : $(current).find('.ut-accordion-module-item').data('easing')
						});

					});

				}
			});

		},

		/* Init Tabs
        ================================================== */
		init_tabs: function() {

			$('.bklyn-icon-tabs').not('.ut-initialized').ut_require_js({
				plugin: 'boostrap',
				source: 'boostrap',
				callback: function( element ) {

					element.each( function( index, current ) {

						if( $(current).hasClass('ut-initialized') ) {
							return true;
						}



					});

				}
			});

		},

		/* Init revealFX
        ================================================== */
		init_revealfx: function() {

			$('.ut-reveal-fx-element').not('.ut-initialized').ut_require_js({
				plugin: 'RevealFx',
				source: 'revealfx',
				callback: function( element ) {

					element.each( function( index, element ) {

						if( $(element).hasClass('ut-initialized') ) {
							return;
						}

						$(element).addClass('ut-initialized');

						var current_reveal  = new RevealFx(element, {
							isContentHidden: true,
							revealSettings: {
								bgcolor: $(element).data('reveal-bgcolor'),
								direction: $(element).data('reveal-direction'),
								duration: $(element).data('reveal-duration'),
								delay: $(element).data('reveal-delay'),
								onCover: function( contentEl ) {

									contentEl.style.opacity = 1;
									$(element).addClass('ut-block-revealed');

									UTImageObserver.observe();

								}

							}

						});

						$(element).on('inview', function (event, isInView) {

							if (isInView) {

								var $this = $( event.target );

								if ($this.hasClass('ut-block-reveal-done')) {
									return;
								}

								// remove CSS Hide
								$this.parent().removeClass('ut-element-with-block-reveal');

								// execute reveal
								current_reveal.reveal();

							}

						});

					});

				}
			});

		},

		/* Init Progress Circles - No Plugin Dependency
        ================================================== */
		init_progress_circles: function() {

			var $progress_circles = $('.bkly-progress-circle');

			if( $progress_circles.hasClass('ut-initialized') ) {
				return;
			}

			$progress_circles.addClass('ut-initialized');

			// run inview
			$progress_circles.on('inview', function (event, isInView) {

				var $this = $(event.target),
					$circle = $this.children('.bkly-progress-svg');

				if( isInView ) {

					var totalProgress = $circle.find('.circle').attr('stroke-dasharray'),
						progress      = $circle.parent().data('circle-percent');

					$circle.find('.stroke').get(0).style['stroke-dashoffset'] = 502.4 + (totalProgress * progress / 100);
					$circle.find('.circle').get(0).style['stroke-dashoffset'] = totalProgress * progress / 100;

				} else {

					if( $circle.data('animateonce') === 'no' ) {

						$circle.find('.stroke').get(0).style['stroke-dashoffset'] = 502.4;
						$circle.find('.circle').get(0).style['stroke-dashoffset'] = 0;

					} else {

						// remove inview listener
						$this.off('inview');

					}

				}

			});

		},

		/* Init Skill Bars - No Plugin Dependency
        ================================================== */
		init_skill_bars: function() {

			var $skill_bars = $('.ut-skill-active');

			if( this.content ) {

				var $container = $(this.content).find('.ut-skill-active');

				if( $container.length ) {

					$skill_bars = this.create_collection( $container );

				}

			}

			if( $skill_bars.hasClass('ut-initialized') ) {
				return;
			}

			$skill_bars.addClass('ut-initialized');

			// run inview
			$skill_bars.on('inview', function( event, isInView ) {

				var $this     = $(event.target),
					bar_width = $this.data('width');

				if( isInView ) {

					if( $this.hasClass('ut-skill-progress-thin') ) {

						$this.addClass("ut-already-visible").width( bar_width + "%");

					} else {

						$this.addClass("ut-already-visible").stop(true, true).animate({width: bar_width + "%"}, $this.data("speed"));

					}

				} else {

					if( $this.data('animateonce') === 'no' ) {

						$this.css('width', 0);

					} else {

						// remove inview listener
						$this.off('inview');

					}

				}

			});

		},

		/* Milestone Count Up - Has Dependency - Deprecated
        ================================================== */
		init_count_up: function () {

			$('.ut-counter[data-type="slot"]').not('.ut-initialized').ut_require_js({
				plugin: 'utSlotMachine',
				source: 'slot',
				callback: function( element ) {

					if( element.hasClass('ut-initialized') ) {
						return;
					}

					element.addClass('ut-initialized');

					if( this.content ) {

						var $container = $(this.content).find('.ut-counter');

						if( $container.length ) {

							element = this.create_collection( $container );

						}

					}

					element.utSlotMachine();


					element.on('inview', function( event, isInView ) {

						if( isInView ) {

							var $this = $(event.target);

							$('<'+ $this.data('prefix-tag') +' class="ut-count-prefix">' + $this.data('prefix') + '</'+ $this.data('prefix-tag') +'>').prependTo($this.find('.ut-count'));
							$('<'+ $this.data('suffix-tag') +' class="ut-count-suffix">' + $this.data('suffix') + '</'+ $this.data('suffix-tag') +'>').appendTo($this.find('.ut-count'));

							// remove inview listener
							$(event.target).off('inview');

						}

					});

				}

			});

			$('.ut-counter[data-type="countup"]').not('.ut-initialized').ut_require_js({
				plugin: 'CountUp',
				source: 'countup',
				callback: function( element ) {

					if( element.hasClass('ut-initialized') ) {
						return;
					}

					element.addClass('ut-initialized');

					if( this.content ) {

						var $container = $(this.content).find('.ut-counter');

						if( $container.length ) {

							element = this.create_collection( $container );

						}

					}

					element.on('inview', function( event, isInView ) {

						if( isInView ) {

							var $this = $(event.target);

							var options = {
								useEasing: true,
								useGrouping: true,
								separator: $this.data('sep-sign'),
								decimal: '.',
								decimalPlaces: $this.data('decimal-places'),
								prefix: '<'+ $this.data('prefix-tag') +' class="ut-count-prefix">' + $this.data('prefix') + '</'+ $this.data('prefix-tag') +'>',
								suffix: '<'+ $this.data('suffix-tag') +' class="ut-count-suffix">' + $this.data('suffix') + '</'+ $this.data('suffix-tag') +'>'
							};

							// initialize counter
							var count = new CountUp( $this.find('.ut-count').attr("id"), 0, $this.data('counter'), $this.data('decimals'), $this.data('speed') / 1000, options );

							// run it
							count.start();

							// remove inview listener
							$this.off('inview');

						}

					});

				}

			});

		},

		/* Animated Image
        ================================================== */
		init_animated_image: function () {

			var $animated_image = $('.ut-animate-image');

			$animated_image.each(function( index, element ) {

				var _$animated_image = $(element);

				if( _$animated_image.hasClass('ut-initialized') ) {
					return;
				}

				_$animated_image.addClass('ut-initialized');

				// run inview
				_$animated_image.on('inview', function( event, isInView ) {

					var $this = $(event.target),
						effect = $this.data('effect');

					if ( isInView ) {

						if( $this.hasClass('ut-animation-complete') || $this.hasClass('ut-element-is-animating') || !$this.hasClass('ut-animate-image-ready') ) {
							return false;
						}

						if ($this.data('animation-duration')) {

							$this.css('animation-duration', $this.data('animation-duration'));

						}

						if ($this.data('animation-between')) {

							$this.css('animation-delay', $this.data('animation-between'));

						}

						$this.delay($this.data('delay')).queue(function () {

							$this.css('opacity', '1').addClass(effect).dequeue();

						});

					} else {

						if ($this.hasClass('ut-animation-complete') || $this.hasClass('ut-element-is-animating')) {
							return;
						}

						if ($this.data('animateonce') === 'no') {

							$this.clearQueue().removeClass(effect).css('opacity', '0').dequeue();

						} else {

							if ($this.hasClass(effect)) {

								$this.addClass('ut-animation-complete');
								$this.off('inview');

							}

						}

					}

				});

			});

		},

		/* Owl Carousel
        ================================================== */
		init_owl_gallery_slider: function () {

			$('.ut-owl-gallery-slider, .ut-bkly-qt-rotator > .owl-carousel').not('.ut-initialized').ut_require_js({
				plugin: 'owlCarousel',
				source: 'owl',
				callback: function( element ) {

					element.each( function( index, element ) {

						if ($(element).hasClass('ut-initialized')) {
							return;
						}

						$(element).addClass('ut-initialized');

						// get carousel settings
						var owl_settings = $(element).attr('data-settings');

						// could be escaped by ajax request
						if( owl_settings.includes('\"') ) {

							owl_settings = JSON.parse( owl_settings.replace(/\\/g, "") );

						} else {

							owl_settings = JSON.parse( owl_settings );

						}

						// run owl slider
						$(element).owlCarousel( owl_settings );

						// lightbox actions
						if( site_settings.lg_type === 'lightgallery' && owl_settings.lightbox !== 'no' || is_ms_ie() ) {

							$(element).ut_require_js({
								plugin: 'lightGallery',
								source: 'lightGallery',
								callback: function (element) {

									element.lightGallery({
										selector: ".for-lightbox",
										hash: false,
										thumbnail: false,
										exThumbImage: 'data-exthumbimage',
										getCaptionFromTitleOrAlt: "true",
										download: site_settings.lg_download,
										mode: site_settings.lg_mode,
									});

									// stop autoplay when lightgallery is active
									if( owl_settings.autoplay ) {

										element.on('onAfterOpen.lg', function () {
											element.trigger('stop.owl.autoplay');
										});

										element.on('onCloseAfter.lg', function () {
											element.trigger('play.owl.autoplay');
										});

									}

								}

							});

						}

						if( site_settings.lg_type === 'morphbox' && !is_ms_ie() ) {

							var $morphbox_app = $('#ut-morph-box-app');

							if( owl_settings.autoplay ) {

								$morphbox_app.on('onAfterOpen.utmorph', function () {

									$(element).trigger('stop.owl.autoplay');

								});

								$morphbox_app.on('onAfterClose.utmorph', function () {

									$(element).trigger('play.owl.autoplay');

								});

							}

						}

						if( $(element).hasClass('ut-owl-gallery-slider-with-media') ) {

							var selector = site_settings.lg_type === 'morphbox' && !is_ms_ie() ? '.ut-owl-video-link' : '.for-lightbox';

							$(element).ut_require_js({
								plugin: 'lightGallery',
								source: 'lightGallery',
								callback: function (element) {

									element.lightGallery({
										selector: selector,
										hash: false,
										thumbnail: false,
										exThumbImage: 'data-exthumbimage',
										getCaptionFromTitleOrAlt: "true",
										download: site_settings.lg_download,
										mode: site_settings.lg_mode,
									});

									// stop autoplay when lightgallery is active
									if( owl_settings.autoplay ) {

										element.on('onAfterOpen.lg', function () {
											element.trigger('stop.owl.autoplay');
										});

										element.on('onCloseAfter.lg', function () {
											element.trigger('play.owl.autoplay');
										});

									}

								}

							});

						}

					});

				}

			});

		},


		/* Owl Testimonials Carousel (in progress)
        ================================================== */
		init_owl_testimonials: function () {

			$('.ut-bkly-testimonial-rotator > .owl-carousel').not('.ut-initialized').ut_require_js({
				plugin: 'owlCarousel',
				source: 'owl',
				callback: function( element ) {

					element.each( function( index, element ) {

						if ($(element).hasClass('ut-initialized')) {
							return;
						}

						$(element).addClass('ut-initialized');

						// get carousel settings
						var owl_settings = $(element).attr('data-settings');

						// could be escaped by ajax request
						if (owl_settings.includes('\"')) {

							owl_settings = JSON.parse(owl_settings.replace(/\\/g, ""));

						} else {

							owl_settings = JSON.parse(owl_settings);

						}

						owl_settings.onRefresh = function () {

							$(element).find('div.owl-item').height('');

						};

						owl_settings.onRefreshed = function () {

							$(element).find('div.owl-item').height( $(element).height() );

						};

						// run owl slider
						$(element).owlCarousel(owl_settings);

						$(element).on('click', '.owl-item.active:not(.center)', function() {

							if( $(this).prev().hasClass("center") ) {

								$(element).trigger('next.owl.carousel');

							} else {

								$(element).trigger('prev.owl.carousel');

							}

						});

					});

				}

			});

		},


		/* Brands
        ================================================== */
		init_brands: function () {

			$('.ut-brands-carousel').not('.ut-initialized').ut_require_js({
				plugin: 'flickity',
				source: 'flickity',
				callback: function( element ) {

					element.each( function( index, element ) {

						if ($(element).hasClass('ut-initialized')) {
							return true;
						}

						$(element).addClass('ut-initialized');

						var flickity_settings = $(element).data('settings');

						$(element).flickity({
							cellAlign: "left",
							wrapAround: true,
							groupCells: 1,
							lazyLoad: flickity_settings.lazyLoad,
							imagesLoaded: true,
							pageDots : flickity_settings.pageDots,
							autoPlay : flickity_settings.autoPlay,
							pauseAutoPlayOnHover: false,
							prevNextButtons: flickity_settings.prevNextButtons,
							arrowShape : '',
							on: {
								ready: function() {

									if( flickity_settings.pageDots ) {

										// move page dots
										$('.flickity-page-dots').appendTo( $(element).parent().find('.ut-brands-dots') );

									}

								}

							}

						});

						if( flickity_settings.autoPlay ) {

							$(element).flickity('playPlayer');

							$(element).on( 'mouseleave', function () {

								$(element).flickity('playPlayer');

							});

						}

						$(element).imagesLoaded( function() {

							$(element).flickity("resize");

						});

					});

				}

			});

		},

		decodeHtml: function(html) {

			var txt = document.createElement("textarea");
			txt.innerHTML = html;
			return txt.value;

		},

		/* Init Word Rotators
        ================================================== */
		init_word_rotators: function() {

			$('.ut-word-rotator-classic').not('.ut-initialized').each( function() {

				var $this = $(this);

				if( $this.hasClass('ut-initialized') ) {
					return true;
				}

				$this.addClass('ut-initialized');

				var word_rotator_settings = $this.attr('data-settings'),
					ut_rotator_words = window.ut_rotator_words[$this.data('id')],
					counter = 0;

				// could be escaped by ajax request
				if( word_rotator_settings.includes('\"') ) {

					word_rotator_settings = JSON.parse( word_rotator_settings.replace(/\\/g, "") );

				} else {

					word_rotator_settings = JSON.parse( word_rotator_settings );

				}

				// check if word rotator is located inside 3image fader
				if( $this.closest("#ut-hero").length && $this.closest("#ut-hero").hasClass("ut-hero-imagefader-background") ) {

					$("ul.ut-image-fader li", "#ut-hero").on("webkitAnimationStart mozAnimationStart MSAnimationStart oanimationstart animationstart", function(){

						var indx = $(this).index();

						if( counter > 0 ) {

							$this.fadeOut(726, function(){

								var data_word = ut_rotator_words[indx].replace(/<(?:.|\n)*?>/gm, '');

								if( word_rotator_settings.glitch !== 'off' ) {

									$this.html('<div class="ut-glitch-on-appear ut-simple-glitch-text-'+ word_rotator_settings.glitch +'" data-title="' + data_word + '">' + ut_rotator_words[indx] + '</div>').fadeIn(726);

								} else {

									$this.html('<div data-title="' + data_word + '">' + ut_rotator_words[indx] + '</div>').fadeIn(726);

								}

							});

						}

						counter++;

					});

					$("ul.ut-image-fader li", "#ut-hero").on("animationiteration", function(){

						var indx = $(this).index();

						$this.fadeOut( 726, function(){

							var data_word = ut_rotator_words[indx].replace(/<(?:.|\n)*?>/gm, '');

							if( word_rotator_settings.glitch !== 'off' ) {

								$this.html('<div class="ut-glitch-on-appear ut-simple-glitch-text-'+ word_rotator_settings.glitch +'" data-title="'+ data_word +'">' + ut_rotator_words[indx] + '</div>').fadeIn(726);

							} else {

								$this.html('<div data-title="' + data_word + '">' + ut_rotator_words[indx] + '</div>').fadeIn(726);

							}

						});

					});


				} else {

					var ut_word_rotator = function() {

						setInterval( function() {

							var word = ut_rotator_words[counter=(counter+1)%ut_rotator_words.length],
								data_word = word.replace(/<(?:.|\n)*?>/gm, '');

							if( word_rotator_settings.glitch !== 'off' ) {

								$this.fadeOut( word_rotator_settings.effect_timer, function() {

									$(this).html('<div class="ut-glitch-on-appear ut-simple-glitch-text-' + word_rotator_settings.glitch + '" data-title="'+ data_word +'">' + word + '</div>').fadeIn( word_rotator_settings.effect_timer );

								});

							} else {

								$this.fadeOut( word_rotator_settings.effect_timer, function() {

									$(this).html('<div data-title="'+ data_word +'">' + word + '</div>').fadeIn( word_rotator_settings.effect_timer );

								});

							}


						}, word_rotator_settings.timer );

					};

					if( typeof preloader_settings !== "undefined" && word_rotator_settings.wait_for_preloader === 'true'  ) {

						var check_loader_status = setInterval( function() {

							if( !preloader_settings.loader_active ) {

								ut_word_rotator();
								clearInterval(check_loader_status);

							}

						}, 50 );

					} else {

						ut_word_rotator();

					}

				}

			});

			$('.ut-word-rotator-typewriter').not('.ut-initialized').ut_require_js({
				plugin: 'Typewriter',
				source: 'typewriter',
				callback: function (element) {

					element.each(function ( index, element ) {

						var $this = $(element);

						if( $this.hasClass('ut-initialized') ) {
							return true;
						}

						$this.addClass('ut-initialized');

						// settings
						var word_rotator_settings = $this.attr('data-settings'),
							ut_rotator_words = window.ut_rotator_words[$this.data('id')],
							counter = 0;

						// could be escaped by ajax request
						if( word_rotator_settings.includes('\"') ) {

							word_rotator_settings = JSON.parse( word_rotator_settings.replace(/\\/g, "") );

						} else {

							word_rotator_settings = JSON.parse( word_rotator_settings );

						}

						// initialize Typewriter
						var typewriter = new Typewriter( this, {
							loop: true,
							cursor: word_rotator_settings.cursor,
							wrapperClassName : 'ut-typewriter-word',
							cursorClassName : 'ut-typewriter-cursor'
						});

						// check if word rotator is located inside 3image fader
						if( $this.closest("#ut-hero").length && $this.closest("#ut-hero").hasClass("ut-hero-imagefader-background") ) {

							$("ul.ut-image-fader li", "#ut-hero").on("webkitAnimationStart mozAnimationStart MSAnimationStart oanimationstart animationstart", function(){

								var indx = $(this).index();

								if( counter > 0 ) {

									var word = ut_rotator_words[indx],
										data_word = word.replace(/<(?:.|\n)*?>/gm, '');

									if( word_rotator_settings.glitch !== 'off' ) {

										$(".ut-word-rotator", $this).fadeOut(726, function () {

											$(".ut-word-rotator", $this).html('<div class="ut-glitch-on-appear ut-simple-glitch-text-' + word_rotator_settings.glitch + '" data-title="' + data_word + '">' + word + '</div>').fadeIn(726);

										});

									} else {

										$(".ut-word-rotator", $this).fadeOut(726, function () {

											$(".ut-word-rotator", $this).html('<div data-title="' + data_word + '">' + word + '</div>').fadeIn(726);

										});

									}

								}

								counter++;

							});

							$("ul.ut-image-fader li", "#ut-hero").on("animationiteration", function(){

								var indx = $(this).index();

								var word = ut_rotator_words[indx],
									data_word = word.replace(/<(?:.|\n)*?>/gm, '');

								if( word_rotator_settings.glitch !== 'off' ) {

									$(".ut-word-rotator", $this ).fadeOut(726,function(){

										$(".ut-word-rotator", $this ).html('<div class="ut-glitch-on-appear ut-simple-glitch-text-' + word_rotator_settings.glitch + '" data-title="'+ data_word +'">' + word + '</div>').fadeIn(726);

									});

								} else {

									$(".ut-word-rotator", $this ).fadeOut(726, function () {

										$(".ut-word-rotator", $this ).html('<div data-title="' + data_word + '">' + word + '</div>').fadeIn(726);

									});

								}

							});

						} else {

							var ut_word_rotator = function() {

								for( var i = 0; i < ut_rotator_words.length; i++ ) {

									typewriter.typeString( UT_Shortcodes.decodeHtml( ut_rotator_words[i] ) ).callFunction( function () {

										if( word_rotator_settings.glitch !== 'off' ) {

											$this.addClass("ut-glitch-on-appear ut-simple-glitch-text-" + word_rotator_settings.glitch );

										}

									}).pauseFor(2500).deleteAll().callFunction( function () {

										if( word_rotator_settings.glitch !== 'off' ) {

											$this.removeClass("ut-glitch-on-appear ut-simple-glitch-text-" + word_rotator_settings.glitch );

										}

									});

								}

								typewriter.start();

							};

							if( typeof preloader_settings !== "undefined" && word_rotator_settings.wait_for_preloader === 'true'  ) {

								var check_loader_status = setInterval( function() {

									if( !preloader_settings.loader_active ) {

										ut_word_rotator();
										clearInterval( check_loader_status );

									}

								}, 50 );

							} else {

								ut_word_rotator();

							}

						}

					});

				}
			});

			// @todo upcoming update - integrated in 5
			$('.ut-word-rotator-reveal').each( function(){

				var $this = $(this);

				if( $this.hasClass('ut-initialized') ) {
					return true;
				}

				$this.addClass('ut-initialized');

			});

		},

		/* Init Icon Draw
        ================================================== */
		init_icon_draw: function() {

			$('.ut-vivus-draw').not('.ut-initialized').ut_require_js({
				plugin: 'Vivus',
				source: 'vivus',
				callback: function( element ) {

					element.each(function ( index, element ) {

						var $this = $(element);

						if( $this.hasClass('ut-initialized') ) {
							return true;
						}

						$this.addClass('ut-initialized');

						// settings
						var draw_settings = $this.attr('data-settings');

						// could be escaped by ajax request
						if( draw_settings.includes('\"') ) {

							draw_settings = JSON.parse( draw_settings.replace(/\\/g, "") );

						} else {

							draw_settings = JSON.parse( draw_settings );

						}

						// no draw activated
						if( draw_settings.draw_svg_icons === 'no' ) {
							return true;
						}

						$this.data('ut-vivus', new Vivus( element, {
							duration: draw_settings.draw_svg_duration,
							type: draw_settings.draw_svg_type,
							start: "inViewport",
							onReady: function (obj) {

								if( obj.el.classList ) {

									obj.el.classList.add('ut-svg-loaded');

								} else {

									obj.el.setAttribute('class', 'ut-svg-loaded');

								}

							}
						}) );

						$this.data('ut-vivus').stop();

						$this.parent().on('inview', function(event, isInView) {

							if( isInView ) {

								$(event.target).delay( $this.data('settings').draw_svg_delay ).queue( function() {

									$this.data('ut-vivus').play();
									$(event.target).dequeue();

								});

							}

						});


					});

				}

			});

		},

		/* Init Parallax Quote
        ================================================== */
		init_parallax_quote: function() {

			$('.ut-parallax-quote.ut-reveal-fx').not('.ut-initialized').ut_require_js({
				plugin: 'RevealFx',
				source: 'revealfx',
				callback: function (element) {

					element.each(function (index, element) {

						var $this = $(element);

						if( $this.hasClass('ut-initialized') ) {
							return true;
						}

						$this.addClass('ut-initialized');

						// reveal icon
						if( $('.ut-parallax-quote-title', $this ).prev('.ut-parallax-icon-wrap').length ) {

							var reveal_icon = new RevealFx( $('.ut-parallax-quote-title', $this ).prev('.ut-parallax-icon-wrap').get(0), {
								revealSettings: {
									bgcolor: $this.data('revealfx-color'),
									onCover: function( contentEl ) {

										contentEl.style.opacity = 1;

										// possible icon draw
										if( $(contentEl).find('.ut-vivus-draw').length ) {

											$(contentEl).find('.ut-vivus-draw').removeClass('ut-initialized');
											UT_Shortcodes.init_icon_draw();

										}

									}
								}
							});

						}

						// reveal quote
						var reveal_title = new RevealFx( $('.ut-parallax-quote-title', $this ).get(0), {
							revealSettings: {
								bgcolor: $this.data('revealfx-color'),
								onCover: function ( contentEl ) {
									contentEl.style.opacity = 1;
								}
							}
						});

						// cite
						if( $('.ut-parallax-quote-name-wrap', $this ).length ) {

							var reveal_cite = new RevealFx( $('.ut-parallax-quote-name-wrap', $this ).get(0), {
								revealSettings: {
									bgcolor: $this.data('revealfx-color'),
									onCover: function( contentEl ) {
										contentEl.style.opacity = 1;
									}
								}
							});

						}

						$this.on('inview', function( event, isInView ) {

							if( isInView ) {

								var $that = $( '.ut-parallax-quote-title', $(event.target) );

								// remove CSS Hide
								$that.parent().removeClass('ut-element-with-block-reveal');

								// execute reveal
								reveal_title.reveal();

								if( $('.ut-parallax-quote-name-wrap', $that.next().next('div') ).length ) {

									setTimeout(function () {

										reveal_cite.reveal();

									}, 200);

								}

								if( $that.prev('.ut-parallax-icon-wrap').length ) {

									setTimeout(function () {

										reveal_icon.reveal();

									}, 200);

								}

								// remove event listener
								$(event.target).off('inview');

							}

						});

					});

				}

			});

		},

		/* Init Pie Charts
        ================================================== */
		init_pie_charts: function() {

			$('.ut-pie-chart').not('.ut-initialized').ut_require_js({
				plugin: 'Chart',
				source: 'pie_chart',
				callback: function (element) {

					element.each(function (index, element) {

						var $this = $(element);

						if ($this.hasClass('ut-initialized')) {
							return true;
						}

						$this.addClass('ut-initialized');

						// get canvas context
						var ut_pie_canvas = element.getContext('2d');

						// pie chart settings
						var pie_settings = $this.attr('data-settings');

						// could be escaped by ajax request
						if( pie_settings.includes('\"') ) {

							pie_settings = JSON.parse( pie_settings.replace(/\\/g, "") );

						} else {

							pie_settings = JSON.parse( pie_settings );

						}

						// add callback
						pie_settings.options.tooltips.callbacks = {
							label : function(tooltipItem, chart) {
								return chart.datasets[0].data[tooltipItem.index] + ' ' + chart.labels[tooltipItem.index];
							}
						};

						var ut_pie_chart = new Chart( ut_pie_canvas, pie_settings );


					});

				}

			});

		},

		/* Init Custom Heading Animation
        ================================================== */
		init_appear_effects: function() {

			$('[data-appear-effect]').not('.ut-initialized').ut_require_js({
				plugin: 'anime',
				source: 'anime',
				callback: function (element) {

					element.each(function (index, element) {

						var $this = $(element),
							span_classes = $this.find('span').attr('class');

						if( $this.hasClass('ut-initialized') ) {
							return true;
						}

						$this.addClass('ut-initialized');

						$this.on('inview', function( event, isInView ) {

							if( isInView ) {

								var $that = $(event.target),
									effect = $that.data('appear-effect');

								var that = this;

								if( $that.find('a').length ) {

									that = $that.find('a').get(0);

								}

								if( !$that.hasClass('ut-effect-letter-split') ) {

									// wrap every letter in a span
									var all_words = that.textContent.split(" ");

									that.innerHTML = '';

									for( var i = 0; i < all_words.length; i++ ) {

										var word = all_words[i].replace(/\S/g, "<ut-letter class='ut-effect-letter'>$&</ut-letter>");
										that.innerHTML += '<span class="' + span_classes + '">' + word + '</span>';

										if( i + 1 < all_words.length ) {

											that.innerHTML += '\xa0';

										}

									}

									$that.addClass('ut-effect-letter-split');

								}

								// all letters
								var targets = that.querySelectorAll('.ut-effect-letter');

								// add effect settings
								ut_letter_effects[effect].targets = targets;
								ut_letter_effects[effect].begin = function() {

									$that.addClass('ut-effect-letter-animated');
									$this.off('inview');

								};

								anime.timeline({loop: false }).add(ut_letter_effects[effect]);

							}

						});

					});

				}

			});

		},

		/* List Animation
        ================================================== */
		init_list_animation: function() {

			$('.bklyn-fancy-list-animated').each(function(){

				var $this = $(this);

				$this.on('inview', function( event, isInView ) {

					if( isInView ) {

						$this.find('li').each(function (i) {

							var $that = $(this);

							setTimeout(function () {
								$that.addClass('appeared');
							}, i * 150);

						});

						$this.off('inview');

					}

				});

			});

			$('.ut-table-menu-animated').each(function(){

				var $this = $(this);

				$this.on('inview', function( event, isInView ) {

					if( isInView ) {

						$this.find('.ut-table-menu-top').each(function (i) {

							var $that = $(this);

							setTimeout(function () {
								$that.addClass('appeared');
							}, i * 150);

						});

						$this.off('inview');

					}

				});

			});

		},

		/* Init Isotope
        ================================================== */
		init_isotope: function( element ) {

			let isoOptions = {
				itemSelector: '.ut-image-gallery-item-wrap',
				masonry: {
					columnWidth: '.ut-image-gallery-sizer',
					gutter: element.data('mason-gal-gutter')
				}
			};

			if( $(window).width() > 767 && $(window).width() <= 1024 ) {

				isoOptions.masonry.gutter = element.data('mason-gal-gutter-tablet');

			}

			if( $(window).width() <= 767 ) {

				isoOptions.masonry.gutter = element.data('mason-gal-gutter-mobile');

			}

			if( element.data('isotope') ) {

				if( $(window).width() >= 1025 ) {

					element.data('isotope').options.masonry.gutter = element.data('mason-gal-gutter');

				}

				if( $(window).width() > 767 && $(window).width() <= 1024 ) {

					element.data('isotope').options.masonry.gutter = element.data('mason-gal-gutter-tablet');

				}

				if( $(window).width() <= 767 ) {

					element.data('isotope').options.masonry.gutter = element.data('mason-gal-gutter-mobile');

				}

				element.isotope('reloadItems');

			} else {

				element.isotope(isoOptions);

			}

		},

		/* Init Image Gallery
        ================================================== */
		init_image_gallery: function() {

			let $gallery_modules = $('.ut-image-gallery-module');

			if( site_settings.lg_type === 'lightgallery' ) {

				$gallery_modules.ut_require_js({
					plugin: 'lightGallery',
					source: 'lightGallery',
					callback: function ( element ) {

						element.lightGallery({
							selector: '.ut-vc-images-lightbox-group-image',
							exThumbImage: 'data-exthumbimage',
							download: site_settings.lg_download,
							mode: site_settings.lg_mode,
							hash: false
						});

					}

				});

			}

			$gallery_modules.each(function( index, element ){

				var $this = $(element);

				if( $this.hasClass('ut-initialized') ) {
					return true;
				}

				if( $this.data('mason-gal') ) {

					$this.ut_require_js({
						plugin: 'isotope',
						source: 'isotope',
						callback: function (element) {

							if( element.hasClass('ut-isotope-ready') ) {
								return;
							}

							// init isotope
							UT_Shortcodes.init_isotope( element );

							// attach listener
							$(window).utresize(function(){

								UT_Shortcodes.init_isotope( element );

							});

							element.addClass('ut-isotope-ready');

						}

					});

				}

				// gallery settings
				var gallery_settings = $this.attr('data-settings');

				// could be escaped by ajax request
				if( gallery_settings.includes('\"') ) {

					gallery_settings = JSON.parse( gallery_settings.replace(/\\/g, "") );

				} else {

					gallery_settings = JSON.parse( gallery_settings );

				}

				if( gallery_settings.lazy && ( !gallery_settings.animate || ( gallery_settings.animate && !gallery_settings.effect ) ) ) {

					if( $this.data('mason-gal') ) {

						$(".ut-image-gallery-item-wrap", $this ).appear();

						$this.on('appear', '.ut-image-gallery-item-wrap', function( event, $all_appeared_elements ) {

							var $that = $(this);

							if( !$that.children('.ut-image-gallery-item').hasClass('ut-image-loaded') ) {
								return;
							}

							$that.delay( 100 * ( $all_appeared_elements.index(this) - get_animated_objects( $all_appeared_elements, 'appeared' ) ) ).queue( function() {

								$that.children('.ut-image-gallery-item').addClass('appeared');

							});

						});

					} else {

						$(".ut-image-gallery-item", $this ).appear();

						$this.on('appear', '.ut-image-gallery-item', function( event, $all_appeared_elements ) {

							var $that = $(this);

							if( !$that.hasClass('ut-image-loaded') ) {
								return;
							}

							$that.delay( 100 * ( $all_appeared_elements.index(this) - get_animated_objects( $all_appeared_elements, 'appeared' ) ) ).queue( function() {

								$that.addClass('appeared');

							});

						});

					}

				}

				if( gallery_settings.animate && gallery_settings.effect ) {

					$(".ut-animate-gallery-element", $this ).appear();

					var delay_this  = true,
						start_delay = false;

					function function_check_for_delay() {

						if( delay_this ) {

							if( !start_delay ) {

								start_delay = true;

								setTimeout(function() {

									delay_this = false;
									$.force_appear();

								}, gallery_settings.global_delay_timer );

							}

							return true;

						} else {

							return false;

						}

					}

					$this.on('appear', '.ut-animate-gallery-element', function( event, $all_appeared_elements ) {

						if( gallery_settings.global_delay_animation ) {

							if( function_check_for_delay() ) {
								return false;
							}

						}

						var $that    = $(this),
							effect   = $that.data('effect');

						if( gallery_settings.lazy ) {

							if (!$that.hasClass('ut-image-loaded')) {

								return true;

							}

						}

						if( $that.hasClass('ut-animation-complete') || $that.hasClass('ut-element-is-animating') ) {
							return;
						}

						if( $that.data('animation-duration') ) {

							$that.css('animation-duration', $that.data('animation-duration') );

						}

						if( gallery_settings.delay_animation ) {

							$that.delay( gallery_settings.delay_timer * ( $all_appeared_elements.index(this) - get_animated_objects( $all_appeared_elements, effect ) ) ).queue( function() {

								$that.css('opacity','1').addClass( effect ).dequeue();
								$that.addClass('appeared');

							});

						} else {

							$that.delay( $that.data('delay') ).queue( function() {

								$that.css('opacity','1').addClass( effect ).dequeue();
								$that.addClass('appeared');

							});

						}

						$that.one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {

							$that.addClass("ut-animation-done");

						});

					});

					$this.on('disappear', '.ut-animate-gallery-element', function() {

						var $that  = $(this),
							effect = $that.data('effect');

						if( gallery_settings.lazy ) {

							if (!$that.hasClass('ut-image-loaded')) {
								return true;
							}

						}

						if( $that.hasClass('ut-animation-complete') || $that.hasClass('ut-element-is-animating') ) {
							return;
						}

						if( $that.data('animateonce') === 'no' ) {

							$that.parent().removeClass("ut-animation-done");
							$that.clearQueue().removeClass( effect ).css('opacity','0').dequeue();

						} else {

							if( $that.hasClass( effect ) ) {

								$that.addClass('ut-animation-complete');

							}

						}

					});

				}

				$this.addClass('ut-initialized');


			});



		},

		/* Init React Carousel
        ================================================== */
		init_react_carousel: function() {

			$('.ut-react-carousel').ut_require_js({
				plugin: 'reactslider',
				source: 'reactslider',
				ieblock: true, // no IE support
			});

		},

		/* Init Masonry Grid
        ================================================== */
		init_masonry_grid: function() {

			$('.ut-masonry-grid').ut_require_js({
				plugin: 'utmasonry',
				source: 'masonry',
				callback: function( element ) {

					element.each(function( index, current ){

						$(current).utmasonry({
							columns : $(current).data('columns'),
							tcolumns: $(current).data('tcolumns'),
							mcolumns : $(current).data('mcolumns'),
							itemClass : 'ut-grid-item',
							unitHeight : $(current).data('column-height'),
						});

						$(current).addClass('layoutComplete');
						$.force_appear();

					});

				}

			});

		},

		/* Init Portfolio Grid Size
        ================================================== */
		get_grid_size : function ( $container ) {

			var columns = '';

			if( $(window).width() <= 767) {

				columns = 1;

			} else if( $(window).width() >= 767 && $(window).width() <= 1024 ) {

				columns = 2;

			} else {

				columns = $container.data('columns');

			}

			return $container.width() / columns;

		},

		/* Init Portfolio Carousel
        ================================================== */
		init_portfolio_carousel: function() {

			$('.ut-portfolio-carousel').ut_require_js({
				plugin: 'flexslider',
				source: 'flexslider',
				callback : function ( element ) {

					element.each(function( index, current ){

						if( $(current).hasClass('ut-initialized') ) {
							return true;
						}

						$(current).flexslider({
							animation: 'slide',
							controlNav: false,
							animationLoop: false,
							slideshow: false,
							itemWidth: UT_Shortcodes.get_grid_size( $(current) ),
							itemMargin: 0,
							touch: true,
							start: function(){

								$('.ut-lazy-wait', element ).each(function (index, element ) {

									$(element).removeClass('ut-lazy-wait');

									if( inIframe() ) {

										UT_Adaptive_Images.load_responsive_image(element);

									}

								});

							}
						});

						$(window).utresize(function(){

							$(current).removeData('flexslider').flexslider({
								animation: 'slide',
								controlNav: false,
								animationLoop: false,
								slideshow: false,
								itemWidth: UT_Shortcodes.get_grid_size( $(current) ),
								itemMargin: 0,
								touch: true
							});

						});

						$(current).addClass("ut-initialized");

					});

				}

			});

		},

		/* Init Timeline
        ================================================== */
		init_timeline: function () {

			$('.ut-simple-time-line-wrap').not('.ut-initialized').each(function ( index, element ) {

				var $this = $(element);

				if( $this.hasClass('ut-initialized') ) {
					return true;
				}

				$this.addClass('ut-initialized');

				// settings
				var timeline_settings = $this.attr('data-settings');

				// could be escaped by ajax request
				if( timeline_settings.includes('\"') ) {

					timeline_settings = JSON.parse( timeline_settings.replace(/\\/g, "") );

				} else {

					timeline_settings = JSON.parse( timeline_settings );

				}

				if( timeline_settings.animate ) {

					$(".ut-simple-time-line-event-animation, .ut-simple-time-line-event-marker", $this ).appear();

					var delay_this  = true,
						start_delay = false;

					function function_check_for_delay() {

						if( delay_this ) {

							if( !start_delay ) {

								start_delay = true;

								setTimeout(function() {

									delay_this = false;
									$.force_appear();

								}, timeline_settings.global_delay_timer );

							}

							return true;

						} else {

							return false;

						}

					}

					$this.on('appear', '.ut-simple-time-line-event-animation', function( event, $all_appeared_elements ) {

						if( timeline_settings.global_delay_animation ) {

							if (function_check_for_delay() ) {
								return true;
							}

						}

						var $that    = $(this),
							effect   = $that.data('effect');

						if( $that.hasClass('ut-animation-complete') || $that.hasClass('ut-element-is-animating') ) {
							return;
						}

						if( $that.data('animation-duration') ) {

							$that.css('animation-duration', $that.data('animation-duration') );

						}

						if( timeline_settings.delay_animation ) {

							$that.delay( timeline_settings.delay_timer * ( $all_appeared_elements.index(this) - get_animated_objects( $all_appeared_elements, effect ) ) ).queue( function() {

								$that.css('opacity','1').addClass( effect ).dequeue();

							});

						} else {

							$that.delay( $that.data('delay') ).queue( function() {

								$that.css('opacity','1').addClass( effect ).dequeue();

							});

						}

						$that.one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {

							$that.addClass("ut-animation-done");

						});

					});

					if( timeline_settings.animation_style_marker !== 'none' ) {

						$this.on('appear', '.ut-simple-time-line-event-marker', function( event, $all_appeared_elements ) {

							if( timeline_settings.global_delay_animation ) {

								if (function_check_for_delay()) {
									return true;
								}

							}

							var $that    = $(this),
								effect   = $that.data('effect');

							if( $that.hasClass('ut-animation-complete') || $that.hasClass('ut-element-is-animating') ) {
								return;
							}

							if( $that.data('animation-duration') ) {

								$that.css('animation-duration', $that.data('animation-duration') );

							}

							if( timeline_settings.delay_animation ) {

								$that.delay( timeline_settings.delay_timer_marker * ( $all_appeared_elements.index(this) - get_animated_objects( $all_appeared_elements, effect ) ) ).queue( function() {

									$that.css('opacity','1').addClass( effect ).dequeue();

								});

							} else {

								$that.delay( $that.data('delay') ).queue( function() {

									$that.css('opacity','1').addClass( effect ).dequeue();

								});

							}

							$that.one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {

								$that.addClass("ut-animation-done");

							});

						});

					}

					$this.on('disappear', '.ut-simple-time-line-event-animation', function() {

						var $that  = $(this),
							effect = $that.data('effect');

						if( $that.hasClass('ut-animation-complete') || $that.hasClass('ut-element-is-animating') ) {
							return;
						}

						if( $that.data('animateonce') === 'no' ) {

							$that.parent().removeClass("ut-animation-done");
							$that.clearQueue().removeClass( effect ).css('opacity','0').dequeue();

						} else {

							if( $that.hasClass( effect ) ) {

								$that.addClass('ut-animation-complete');

							}

						}

					});

				}

			});

		},

		/* Init Social Follow
        ================================================== */
		init_social_follow: function() {

			$('.ut-social-follow-module').not('.ut-initialized').each(function ( index, element ) {

				var $this = $(element);

				if( $this.hasClass('ut-initialized') ) {
					return true;
				}

				$this.addClass('ut-initialized');

				// settings
				var social_settings = $this.attr('data-settings');

				// could be escaped by ajax request
				if( social_settings.includes('\"') ) {

					social_settings = JSON.parse( social_settings.replace(/\\/g, "") );

				} else {

					social_settings = JSON.parse( social_settings );

				}


				if( social_settings.animate ) {

					$(".ut-animate-social-follow-element", $this ).appear();

					$this.on('appear', '.ut-animate-social-follow-element', function( event, $all_appeared_elements ) {

						var $that    = $(this),
							effect   = $that.data('effect');

						function social_animation( current ) {

							if( $that.hasClass('ut-animation-complete') || $that.hasClass('ut-element-is-animating') ) {
								return;
							}

							if( $that.data('animation-duration') ) {

								$that.css('animation-duration', $that.data('animation-duration') );

							}

							if( social_settings.delay_animation ) {

								$that.delay( social_settings.delay_timer * ( $all_appeared_elements.index( current ) - get_animated_objects( $all_appeared_elements, effect ) ) ).queue( function() {

									$that.css('opacity','1').addClass( effect ).dequeue();

								});

							} else {

								$that.delay( $that.data('delay') ).queue( function() {

									$that.css('opacity','1').addClass( effect ).dequeue();

								});

							}

							$that.one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {

								$that.addClass("ut-animation-done");

							});

						}

						if( social_settings.global_delay_animation ) {

							$that.delay(social_settings.global_delay_timer).queue( function() {

								$that.dequeue();
								social_animation( this );

							});

						} else {

							social_animation( this );

						}

					});

					$this.on('disappear', '.ut-animate-social-follow-element', function () {

						var $that = $(this),
							effect = $that.data('effect');

						if ($that.hasClass('ut-animation-complete') || $that.hasClass('ut-element-is-animating')) {
							return;
						}

						if ($that.data('animateonce') === 'no') {

							$that.parent().removeClass("ut-animation-done");
							$that.clearQueue().removeClass(effect).css('opacity', '0').dequeue();

						} else {

							if ($that.hasClass(effect)) {

								$that.addClass('ut-animation-complete');

							}

						}

					});

				}

			});

		},

		/* Init Twitter Rotator
        ================================================== */
		init_twitter_rotator: function() {

			$('.ut-twitter-rotator').not('.ut-initialized').ut_require_js({
				plugin: 'flexslider',
				source: 'flexslider',
				callback: function (element) {

					element.each(function( index, element ) {

						var $this = $(element);

						if ($this.hasClass('ut-initialized')) {
							return true;
						}

						$this.addClass('ut-initialized');

						if( $this.data('avatar') === 'on' ) {

							$this.children(":first").flexslider({
								animation: "fade",
								directionNav:false,
								controlNav:false,
								smoothHeight: true,
								animationLoop:true,
								slideshow: $this.data('autoplay'),
								slideshowSpeed: $this.data('speed'),
								slideToStart: 0,
								prevText: "",
								nextText: ""
							});

							$this.children(":first").next().flexslider({
								animation: "slide",
								directionNav:true,
								controlNav:false,
								slideshow: $this.data('autoplay'),
								smoothHeight: true,
								animationLoop:true,
								sync: $this.children(":first"),
								slideshowSpeed: $this.data('speed'),
								slideToStart: 0,
								prevText: "",
								nextText: ""
							});

						} else {

							$this.children(":first").flexslider({
								useCSS: false,
								animation: "fade",
								directionNav:true,
								controlNav:false,
								smoothHeight: false,
								animationLoop:true,
								slideshow: $this.data('autoplay'),
								slideshowSpeed: $this.data('speed'),
								prevText: "",
								nextText: ""
							});


						}

					});

				}

			});

		},

		addParam : function(uri, key, value) {

			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";

			if (uri.match(re)) {

				return uri.replace(re, '$1' + key + "=" + value + '$2');

			} else {

				return uri + separator + key + "=" + value;

			}

		},

		/* Single Google Map
        ================================================== */
		init_google_map: function( element ) {

			// map_settings
			var map_settings = $(element).attr('data-settings');

			// could be escaped by ajax request
			if( map_settings.includes('\"') ) {

				map_settings = JSON.parse( map_settings.replace(/\\/g, "") );

			} else {

				map_settings = JSON.parse( map_settings );

			}

			var map = new google.maps.Map( element, map_settings );

			var geocoder = new google.maps.Geocoder();

			geocoder.geocode({
				'address': $(element).data('address')
			}, function( results, status ) {

				if( status === google.maps.GeocoderStatus.OK ) {

					new google.maps.Marker({
						position: results[0].geometry.location,
						icon: map_settings.marker,
						map: map
					});

					map.setCenter(results[0].geometry.location);

					$(element).addClass('ut-initialized');

				}

			});

		},

		/* Init Google Maps
        ================================================== */
		init_google_maps: function() {

			var $maps = $('.ut-advanced-google-map'),
				that = this;

			if( $maps.length ) {

				if( !this.google_maps_loaded ) {

					$.getScript($maps.data('api')).done(function () {

						$maps.each(function (index, element) {

							UT_Shortcodes.init_google_map(element);

						});

						that.google_maps_loaded = true;

					});

				} else if( this.google_maps_loaded ) {

					$maps.not('.ut-initialized').each(function (index, element) {

						UT_Shortcodes.init_google_map(element);

					});

				}

			}

		},

		/* Init Distortion
        ================================================== */
		init_distortion: function() {

			$('.ut-distortion-effect-container').not('.ut-initialized').ut_require_js({
				plugin: 'hoverEffect',
				source: 'distortion',
				callback: function (element) {

					element.each(function (index, element) {

						var $this = $(element);

						if ($this.hasClass('ut-initialized')) {
							return true;
						}

						$this.addClass('ut-initialized');

						var imgs = Array.from( element.querySelectorAll('img'));

						var dist_observer = lozad( element, {
							rootMargin: '100%',
							loaded: function(el) {

								new hoverEffect({
									parent: el,
									intensity: el.dataset.intensity || undefined,
									speedIn: el.dataset.speedin || undefined,
									speedOut: el.dataset.speedout || undefined,
									easing: el.dataset.easing || undefined,
									hover: el.dataset.hover || undefined,
									image1: imgs[0].getAttribute('data-src'),
									image1_w: imgs[0].getAttribute('width'),
									image1_h: imgs[0].getAttribute('height'),
									image2: imgs[1].getAttribute('data-src'),
									image2_w: imgs[1].getAttribute('width'),
									image2_h: imgs[1].getAttribute('height'),
									displacementImage: el.dataset.displacement
								});

							}
						});

						dist_observer.observe();

					});

				}

			});

		},

		/* Init Lightbox
        ================================================== */
		init_lightbox: function() {

			if( site_settings !== "undefined" && site_settings.lg_type === 'lightgallery' ) {

				$('.ut-vc-images-lightbox').ut_require_js({
					plugin: 'lightGallery',
					source: 'lightGallery',
					callback: function ( element ) {

						element.each(function () {

							$(this).removeClass('ut-wait-for-plugin');

						});

						$('#ut-custom-contact-section, #ut-custom-hero').lightGallery({
							selector: '.ut-vc-images-lightbox',
							exThumbImage: "data-exthumbimage",
							download: site_settings.lg_download,
							getCaptionFromTitleOrAlt: "true",
							mode: site_settings.lg_mode,
							hash: false
						});

						$('.entry-content').each(function () {

							if( $(this).data('lightGallery') ) {

								$(this).data('lightGallery').destroy(true);

							}

							$(this).lightGallery({
								selector: '.ut-vc-images-lightbox:not(.ut-vc-images-lightbox-group-image)',
								exThumbImage: "data-exthumbimage",
								download: site_settings.lg_download,
								getCaptionFromTitleOrAlt: "true",
								mode: site_settings.lg_mode,
								hash: false
							});

						});

					}

				});

				$('.ut-wp-gallery-lightbox-on').ut_require_js({
					plugin: 'lightGallery',
					source: 'lightGallery',
					callback: function ( element ) {

						element.each(function () {

							$(this).lightGallery({
								selector: '.gallery-item a',
								exThumbImage: 'data-exthumbimage',
								hash: false,
								download: site_settings.lg_download,
								mode: site_settings.lg_mode
							});

						});

					}

				});

			}

			$('.ut-wp-gallery-lightbox-off').each(function () {

				$(this).find('a').addClass('ut-deactivated-link');

			})


		},

		init_video_lightbox: function() {

			$('.ut-load-video[data-location="lightbox"]').ut_require_js({
				plugin: 'lightGallery',
				source: 'lightGallery',
				callback: function ( element ) {

					// add video as href for lightbox
					element.each( function () {

						if( $(this).data('html5-video')  ) {

							$(this).removeAttr("href");

							var player_params = {
								selector: '.ut-load-video',
								autoplayFirstVideo: true,
								controls: false,
								download: false,
								zoom: false,
								hash: false,
							};

						} else {

							$(this).attr('href', $(this).data('video') );

							var player_params = {
								selector: '.ut-load-video',
								youtubePlayerParams : { autoplay: 1},
								vimeoPlayerParams : { autoplay: 1},
								controls: false,
								download: false,
								zoom: false,
								hash: false,
							};

						}

						$(this).parent().lightGallery(player_params);

					});

				}

			});

		},

		/* Init Morphbox
        ================================================== */
		init_morphbox: function() {

			if( site_settings !== "undefined" && site_settings.lg_type === 'morphbox' ) {

				// load libraries first
				$("a[data-exthumbimage]").ut_require_js({
					plugin: 'morphbox_base',
					source: 'morphbox_base',
					callback: function (element) {

						$(element).ut_require_js({
							plugin: 'UT_Morph_Box_APP',
							source: 'morphbox',
							callback: function (element) {

								element.each(function () {

									$(this).removeClass('ut-wait-for-plugin');

								});

								UT_Morph_Box_APP.init();

							}
						});

					}
				});

			}

		},

		/* Init Youtube Background Video // replace with new video module
        ================================================== */
		init_yt_background_video: function() {

			$('.ut-video-section-player[data-source="youtube"]').ut_require_js({
				plugin: 'YTPlayer',
				source: 'ytplayer',
				callback: function (element) {

					element.YTPlayer();

					/* player mute control */
					$("#ut-video-control-" + element.data('id') ).click(function(event){

						event.preventDefault();

						if( $(this).hasClass("ut-unmute") ) {

							$(this).removeClass("ut-unmute").addClass("ut-mute");
							element.YTPUnmute();

						} else {

							$(this).removeClass("ut-mute").addClass("ut-unmute");
							element.YTPMute();

						}

					});

					// hide parallax slibling
					$("#ut-background-video-" + element.data('id') ).on("YTPReady",function(){

						$("#ut-background-video-" + element.data('id') ).siblings(".parallax-scroll-container").addClass("parallax-scroll-container-hide");

					});

				}

			});

		},

		/* Init Vimeo Background Video // replace with new video module
        ================================================== */
		init_vimeo_background_video: function() {

			$('.ut-video-section-player[data-source="vimeo"]').ut_require_js({
				plugin: 'vimelar',
				source: 'vimeo',
				callback: function (element) {

					element.each(function() {

						var $this = $(this);

						$this.vimelar( $this.data('settings') );

						var vimeo = document.querySelector("#vimelar-player-" + $this.data('id') ),
							vimeo_player = new Vimeo.Player(vimeo);

						vimeo_player.on("loaded", function() {

							vimeo_player.setVolume($this.data('volume'));
							vimeo_player.play();

							$("#vimelar-container-" + $this.data('id') ).delay(2000).queue(function() {

								$(this).addClass("ut-vimeo-loaded");
								$("#ut-video-hero-control").parent().addClass("ut-audio-control-visible");

								$(window).trigger("resize");

							});

						});

						vimeo_player.on("ended", function() {

							$("#vimelar-container-"+ $this.data('id')).removeClass("ut-vimeo-loaded");
							$("#ut-video-hero-control").fadeOut();

						});

						$("#ut-video-hero-control.vimeo").click(function(event){

							if( $(this).hasClass("ut-unmute") ) {

								$(this).removeClass("ut-unmute").addClass("ut-mute");
								vimeo_player.setVolume( $this.data('max-volume') );

							} else {

								$(this).removeClass("ut-mute").addClass("ut-unmute");
								vimeo_player.setVolume(0);

							}

							event.preventDefault();

						});


					});

				}

			});

		},

		/* Glitch
        ================================================== */
		init_glitch : function() {

			$('.ut-element-glitch-wrap:not(.ut-oberserver-initialized), .ut-simple-glitch-text-permanent:not(.ut-oberserver-initialized)').each(function () {

				var $this = $(this);

				if( $this.hasClass('ut-oberserver-initialized') ) {
					return true;
				}

				$this.addClass('ut-oberserver-initialized').on('inview', InViewObserver);

			});

			$('.ut-simple-glitch-text-hover').each(function () {

				var $this = $(this);

				if( $this.hasClass('bklyn-btn') || $this.parent().hasClass('ut-simple-glitch-text-hover-parent') ) {
					return true;
				}

				$this.parent().addClass('ut-simple-glitch-text-hover-parent');

			});

		}


	};

	// Initialize Shortcodes on document ready
	$(document).ready(function(){

		UT_Shortcodes.init();

	});

	$(window).on("load", function(){

		$(document).ready( function(){

			UT_Shortcodes.init_masonry_grid();

		});

	});

	$(window).on("load", function () {

		// Visual Composer
		if( inIframe() ) {

			setInterval(function () {

				UTImageObserver.observe();
				UTSimpleImageObserver.observe();
				UTBackgroundImageObserver.observe();

				UT_Shortcodes.init();

			}, 2000 );

		}

	});

	// Initialize Shortcodes after Ajax
	$(document).ajaxComplete(function () {

		// required for inview to register new elements
		$(window).scroll();

		// add observer to new elements
		UTImageObserver.observe();
		UTSimpleImageObserver.observe();
		UTBackgroundImageObserver.observe();

		if( inIframe() ) {

			UT_Shortcodes.init();

		}

		// add appear event to new elements
		$('.ut-animate-element:not(.ut-appear-initialized)').appear().addClass('ut-appear-initialized');

	});

	$(window).on("load", function () {

		/* United Video Player
        ================================================== */
		function ut_load_video_player(url, uniqueID, $parent, callback){

			if( !url ) {
				return;
			}

			var ajaxURL = utShortcode.ajaxurl,
				$video = $('<div id="ut-video'+uniqueID+'"></div>'),
				$caption = $parent.find('.ut-video-module-caption-text');

			$.ajax({

				type: 'POST',
				url: ajaxURL,
				data: {"action": "ut_get_video_player", "video" : url },
				success: function(response) {

					$video.html(response).ut_require_js({
						plugin: 'fitVids',
						source: 'fitVids',
						callback: function (element) {

							element.fitVids();

						}
					});

					$parent.html( $video.append($caption) );

					return false;

				},
				complete : function() {

					if (callback && typeof(callback) === "function") {
						callback();
					}

				}

			});

		}

		$(document).on('click', '.ut-load-video[data-location=inline]', function(event) {

			var $this = $(this),
				url = $this.data('video'),
				uniqueID = create_id(),
				$parent = $this.parent('.ut-video-module-caption'),
				$loader = $parent.next('.ut-video-module-loading');

			$parent.find(".ut-video-module-caption-text").fadeOut();
			$loader.fadeIn();

			ut_load_video_player(url, uniqueID, $parent, function() {

				$loader.fadeOut();

			});

			event.preventDefault();

		});

		// deprecated shortcode fallback
		function ut_load_video_united_player(url, uniqueID, $parent, callback){

			if( !url ) {
				return;
			}

			var ajaxURL = utShortcode.ajaxurl,
				$video = $('<div id="ut-video'+uniqueID+'"></div>'),
				$caption = $parent.find('.ut-video-caption-text');

			$.ajax({

				type: 'POST',
				url: ajaxURL,
				data: {"action": "ut_get_video_player", "video" : url },
				success: function(response) {

					$video.html(response).ut_require_js({
						plugin: 'fitVids',
						source: 'fitVids',
						callback: function (element) {

							element.fitVids();

						}
					});

					$parent.html( $video.append($caption) );

					return false;

				},
				complete : function() {

					if (callback && typeof(callback) === "function") {
						callback();
					}

				}

			});

		}

		$(document).on('click', '.ut-video-caption .ut-load-video[data-location="inline"]', function(event) {

			var url = $(this).data('video'),
				uniqueID = create_id(),
				$parent = $(this).parent('.ut-video-caption'),
				$loader = $parent.next('.ut-video-loading');

			$parent.find(".ut-video-caption-text").fadeOut();
			$loader.fadeIn();

			ut_load_video_united_player(url, uniqueID, $parent, function() {

				$loader.fadeOut();

			});

			event.preventDefault();

		});

		/* Element Effects
        ================================================== */
		$('.ut-animate-element').appear().addClass('ut-appear-initialized');

		$(document.body).on('webkitAnimationStart mozAnimationStart MSAnimationStart oanimationstart animationstart', '.ut-animate-element, .ut-animate-image', function() {

			var $this = $(this),
				effect = $this.data('effect');

			if( !$this.hasClass( effect ) ) {
				return;
			}

			// extra class
			$this.addClass('ut-element-is-animating');

		});

		$(document.body).on('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', '.ut-animate-element, .ut-animate-image', function() {

			var $this  = $(this),
				effect = $this.data('effect');

			if( !$this.hasClass( effect ) ) {
				return;
			}

			// extra class
			$this.removeClass('ut-element-is-animating');

			// start animation again
			if( $this.data('animation-between') ) {

				$this.removeClass(effect).delay( $this.data('animation-between') * 1000 ).queue( function() {

					$this.addClass( effect ).dequeue();

				});

			}

			// check if element is hidden and will be animated again
			if( $this.data('animateonce') === 'no' && !$this.isOnScreen() ) {

				$this.clearQueue().removeClass( effect ).css('opacity','0').dequeue();

			}

		});

		$(document.body).on('webkitAnimationStart mozAnimationStart MSAnimationStart oanimationstart animationstart', '.ut-animate-gallery-element', function() {

			var $this = $(this);

			if( !$this.hasClass( $this.data("effect") ) ) {
				return;
			}

			// extra class
			$this.addClass('ut-element-is-animating');

		});

		$(document.body).on('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', '.ut-animate-gallery-element', function() {

			var $this  = $(this);
			var effect = $this.data('effect');

			if( !$this.hasClass( effect ) ) {
				return;
			}

			// extra class
			$this.removeClass('ut-element-is-animating');

			// start animation again
			if( $this.data('animation-between') ) {

				$this.removeClass(effect).delay( $this.data('animation-between') ).queue( function() {

					$this.addClass( effect ).dequeue();

				});

			}

			// check if element is hidden and will be animated again
			if( $this.data('animateonce') === 'no' && !$this.isOnScreen() ) {

				$this.clearQueue().removeClass( effect ).css('opacity','0').dequeue();

			}

		});

		$(document.body).on('appear', '.ut-animate-element', function() {

			var $this = $(this),
				effect = $this.data('effect');

			if( $this.hasClass('ut-animation-complete') || $this.hasClass('ut-element-is-animating') ) {
				return;
			}

			if( $this.data('animation-duration') ) {

				$this.css('animation-duration', $this.data('animation-duration') );

			}

			$this.delay( $this.data('delay') ).queue( function() {

				if( effect.indexOf('fade') !== -1 ) {

					$this.addClass( effect );

				} else {

					$this.css('opacity','1').addClass( effect );

				}

			});


		});

		$(document.body).on('disappear', '.ut-animate-element', function() {

			var $this  = $(this),
				effect = $this.data('effect');


			if( $this.hasClass('ut-animation-complete') || $this.hasClass('ut-element-is-animating') ) {
				return;
			}

			if( $this.data('animateonce') === 'no' ) {

				$this.clearQueue().removeClass( effect ).css('opacity','0').dequeue();

			} else {

				if( $this.hasClass( effect ) ) {

					$this.addClass('ut-animation-complete');

				}

			}

		});



		$('.bklyn-fancy-divider-animated').on('inview', function( event, isInView ) {

			if( isInView ) {

				var $this = $(this);

				setTimeout( function(){

					$this.removeClass('bklyn-fancy-divider-animated');

				}, $this.data('animated-delay') );

			}

		});

		/* Client Groups
        ================================================== */
		$('[data-ut-auto-remove-wait]').parent().on('inview', function( event, isInView ) {

			if( isInView ) {

				var $this = $(this);

				$this.delay( $this.find('.ut-animate-brand-logos').data('ut-auto-remove-wait') ).queue( function () {

					$this.find('.ut-animate-brand-logos').data('ut-wait', 0).attr('data-ut-wait', 0).trigger('inview');

				});

			}

		});

		$('.ut-animate-brand-logos').on('inview', function( event, isInView ) {

			if( isInView ) {

				var $this = $(this),
					effect = $this.data('effect');

				$this.find('.ut-single-brand').each(function (index) {

					var $that = $(this).find('img');

					$that.delay($this.data('delay') * index).queue(function () {

						$that.css('opacity', '1').addClass(effect);

					});

				});

			}

		});

		$(document.body).on('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', '.ut-single-brand img.animated', function() {

			var $this = $(this);

			$this.attr('class', '').attr('style', '');

		});







		$(document).ajaxComplete(function() {

			$('.ut-animate-element:not(.ut-appear-initialized)').appear().addClass('ut-appear-initialized');

		});

	});


	/* Gallery Slider
    ================================================== */
	var user_can_click = true;

	function delay_click( timer ) {

		setTimeout(function() {

			user_can_click = true;

		}, timer );

	}

	$(document).on('click', '.ut-next-gallery-slide:not(.ut-single-slider-control)', function(event) {

		event.stopImmediatePropagation();
		event.preventDefault();

		if( !user_can_click ) {
			return;
		}

		var $owl = $('#' + $(this).data('for') );
		$owl.trigger('next.owl.carousel');

		user_can_click = false;
		delay_click( 200 ); // should be same as animation speed in css

	});

	$(document).on('click', '.ut-prev-gallery-slide:not(.ut-single-slider-control)', function(event) {

		event.stopImmediatePropagation();
		event.preventDefault();

		if( !user_can_click ) {
			return;
		}

		var $owl = $('#' + $(this).data('for') );
		$owl.trigger('prev.owl.carousel');

		user_can_click = false;
		delay_click( 200 ); // should be same as animation speed in css

	});


	$(document).on('click', '.ut-next-gallery-slide.ut-single-slider-control', function(event) {

		event.stopImmediatePropagation();
		event.preventDefault();

		if( !user_can_click ) {
			return;
		}

		var $owl = $('#' + $(this).data('for') );
		$owl.trigger('next.owl.carousel');

		user_can_click = false;
		delay_click( 200 ); // should be same as animation speed in css

	});

	$(document).on('click', '.ut-prev-gallery-slide.ut-single-slider-control', function(event) {

		event.stopImmediatePropagation();
		event.preventDefault();

		if( !user_can_click ) {
			return;
		}

		var $owl = $('#' + $(this).data('for') );
		$owl.trigger('prev.owl.carousel');

		user_can_click = false;
		delay_click( 200 ); // should be same as animation speed in css

	});

	/* Typewriter Tag
    ================================================== */
	$('ut-typewriter-1, ut-typewriter-2').ut_require_js({
		plugin: 'Typewriter',
		source: 'typewriter',
		callback: function (element) {

			element.each( function(){

				var $this = $(this),
					cursor = $this.is('ut-typewriter-1') ? '|' : '_',
					ut_rotator_words = $this.text().split(',');

				// counter for glitch
				$this.attr('current', 1);

				// initialize Typewriter
				var typewriter = new Typewriter( this, {
					loop: true,
					cursor: cursor,
					wrapperClassName : 'ut-typewriter-word',
					cursorClassName : 'ut-typewriter-cursor'
				});

				$this.addClass('ut-typewriter-ready');

				var ut_word_rotator = function() {

					for( var i = 0; i < ut_rotator_words.length; i++ ) {

						typewriter.typeString(ut_rotator_words[i]).callFunction( function () {

							$this.addClass( $this.data('ut-glitch-class') );

						}).pauseFor( 2500 ).deleteAll().callFunction( function () {

							if( parseInt( $this.attr( 'current' ) ) === ut_rotator_words.length ) {

								$this.attr( 'current', 1 );

							} else {

								$this.attr( 'current', parseInt( $this.attr('current') ) + 1 );

							}

							if( $this.closest('.ut-glitch').length ) {

								$this.closest('.ut-glitch').attr('data-title', ut_rotator_words[parseInt( $this.attr('current') )-1] + cursor );

							}

							$this.removeClass( $this.data('ut-glitch-class') );

						});

					}

					typewriter.start();

				};

				if( typeof preloader_settings != "undefined" ) {

					let check_loader_status = setInterval( function() {

						if( !preloader_settings.loader_active ) {

							ut_word_rotator();
							clearInterval( check_loader_status );

						}

					}, 50 );

				} else {

					ut_word_rotator();

				}

			});

		}
	});

	/* Glitch Inview Event
    ================================================== */
	$('.ut-glitch-on-appear').on('inview', function (event, isInView) {

		if (isInView) {

			$(event.target).addClass($(event.target).data('ut-glitch-class'));

		} else {

			$(event.target).removeClass($(event.target).data('ut-glitch-class'));

		}

	});

	/* Deactivated Link
    ================================================== */
	$(document).on('click', '.ut-deactivated-link, .ut-deactivated-link a', function(event) {

		event.preventDefault();

	});

	/* Blog Article Animation
    ================================================== */
	if( $("body").hasClass("ut-blog-has-animation") ) {

		var $posts = $('article'),
			$sidebar = $('#secondary');

		$posts.appear();
		$sidebar.appear();

		$posts.each(function(i){

			$(this).css('z-index', $('article').length+i);

		});

		$(document.body).on('appear', 'article', function() {

			if( !$(this).hasClass('BrooklynFadeInUp') ) {

				$(this).delay(150*$(this).index()).queue(function () {

					$(this).addClass('BrooklynFadeInUp');

				});

			}

		});

		$(document.body).on('appear', '#secondary', function() {

			if( !$(this).hasClass('BrooklynFadeInUp') ) {

				$(this).addClass('BrooklynFadeInUp');

			}

		});

	}

	// force visible elements to appear after load
	$(window).on("load", function () {

		$.force_appear();
		$('#ut-header-search').addClass('ut-header-search-ready');

	});

})(jQuery);
/* ]]> */