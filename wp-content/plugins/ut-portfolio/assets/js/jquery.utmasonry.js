/* requires istope and images loaded plugin */

(function($) {
	
	$.fn.utmasonry = function( options ) {
				
		var settings = $.extend({
            columns		: 4 ,
			tcolumns	: 3 ,
			mcolumns	: 2 ,
			unitHeight  : '',
			itemClass 	: 'isotope-item'
        }, options);

		return this.each(function(options){
									
			var $win = $(window),
				$container = $(this),
				unitHeight = '';
			
			function getUnitWidth() {
                    
				var width,
					containersize = $container.parent().width(),
					columns = settings.columns;
				
				if(containersize <= 1024) {
                    columns = settings.tcolumns;
                } 
				
				if(containersize <= 768) {
                    columns = settings.mcolumns;
                }
                	
				width = containersize / columns;
				return width;
				
			}
			
			
			function setWidths() {
                    
				var unitWidth = getUnitWidth();
				
				/* set item width */
				$container.children().width( unitWidth ).addClass('show');
				
			}
			
			
			function centerImages() {
                
				$container.children().each(function() {
					
					var $this		 = $(this),
						imagewidth   = $this.find('figure').find('img').attr("width"),
						parentwidth  = $this.width();
					
					if( imagewidth > parentwidth ) {
												
						$this.find('figure').find('img').css({ "left" : -( imagewidth - parentwidth ) / 2 });				
					
					} else if( imagewidth < parentwidth ) {
											
						$this.find('figure').find('img').css({ "left" : ( parentwidth - imagewidth  ) / 2 });
					}
					
				});
				
				// trigger scroll for lazy load
                $win.trigger("scroll");
				
			}

			setWidths();

			$container.ut_require_js({
				plugin: 'isotope',
				source: 'isotope',
				callback: function (element) {

					$container.isotope({

						animationEngine : 'jquery',
						itemSelector 	: '.ut-masonry',
						transformsEnabled: false,
						layoutMode: 'packery',
						itemClass : settings.itemClass

					});

				}
			});

			centerImages();

			$(window).utresize(function(){
                    
				setWidths();

				$container.ut_require_js({
					plugin: 'isotope',
					source: 'isotope',
					callback: function (element) {

						$container.isotope({

							animationEngine : 'jquery',
							itemSelector 	: '.ut-masonry',
							transformsEnabled: false,
							layoutMode: 'packery',
							itemClass : settings.itemClass

						});

					}
				});
				
				centerImages();                    
														
			});
			
		});

    }

}(jQuery));