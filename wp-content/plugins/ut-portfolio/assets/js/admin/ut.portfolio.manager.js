/* <![CDATA[ */
(function($){
	
	"use strict";
	
    $(document).ready(function(){

        $("#ut-metabox-tabs").addClass('ut-visual-composer-ready');



		$( "#ut-sortable-tax" ).sortable({ 
			handle: '.ut-handle',
			placeholder: "ut-handle-highlight"
		});
			
		/* ------------------------------------------------
		Color Picker 
        ------------------------------------------------ */
		// $('.ut_color_picker').wpColorPicker();
		$('.ut_color_picker').each(function() {
            
			var $this = $(this),
				mode  = $this.data('mode');

			if( mode === 'rgb' ) {

				$this.minicolors({
					format : mode,
					opacity: true
				});

			} else {

				$this.minicolors({
					format: mode,
					letterCase: 'uppercase'
				});

			}

		});
		
		/* ------------------------------------------------
		Opacity Range Slider
        ------------------------------------------------ */
		$( ".ut-opacity-slider" ).css("visibility","hidden").each(function() {
            
            var sliderdefault = $(this).data('state');
            
            var $slider = $(this).slider({
			
                min: 0,
                max: 1,
                step: 0.01,
                value: sliderdefault ,
                slide: function( event, ui ) {
                    
                    $(this).parent().find('.ut-hidden-slider-input').val( ui.value );
                    $(this).parent().find('.ut-opacity-value').text( ui.value );
                    
                }
            
            });
            
            setTimeout(function(){ $slider.css("visibility","visible").fadeIn().slider( "option", "value", sliderdefault ); }, 500 );
            
        });
        
        /* ------------------------------------------------
		Letter Spacing Slider
        ------------------------------------------------ */
		$( ".ut-letter-spacing-slider" ).css("visibility","hidden").each(function() {
            
            var sliderdefault = $(this).data('state');
            
            var $slider = $(this).slider({
			
                min: -0.2,
                max: 0.2,
                step: 0.01,
                value: sliderdefault ,
                slide: function( event, ui ) {
                    
                    $(this).parent().find('.ut-hidden-slider-input').val( ui.value );
                    $(this).parent().find('.ut-letter-spacing-value').text( ui.value );
                    
                }
            
            });
            
            setTimeout(function(){ $slider.css("visibility","visible").fadeIn().slider( "option", "value", sliderdefault ); }, 500 );
            
        });
        
        /* ------------------------------------------------
		Border Radius Range Slider
        ------------------------------------------------ */
		$( ".ut-border-radius-slider" ).css("visibility","hidden").each(function() {
            
            var sliderdefault = $(this).data('state');
            
            var $slider = $(this).slider({
			
                min: 0,
                max: 100,
                step: 1,
                value: sliderdefault ,
                slide: function( event, ui ) {
                                        
                    $(this).parent().find('.ut-hidden-slider-input').val( ui.value );
                    $(this).parent().find('.ut-border-radius-value').text( ui.value );
                    
                }
                
            
            });
            
            setTimeout(function(){ $slider.css("visibility","visible").fadeIn().slider( "option", "value", sliderdefault ); }, 500 );
            
        });
        
        /* ------------------------------------------------
		Gallery Styles
        ------------------------------------------------ */
        $("#ut_gallery_options_style").change(function(){
            
            if( $(this).find(":selected").val() === 'style_one' || $(this).find(":selected").val() === 'style_three' ) {
                
                $("#ut_gallery_options_style_1_caption_content").show();
                $("#ut_gallery_options_style_2_caption_content").hide();

            } else {
                
                $("#ut_gallery_options_style_1_caption_content").hide();
                $("#ut_gallery_options_style_2_caption_content").show();
                
            }


            if( $('#ut_portfolio_type').val() === 'ut_gallery' && $(this).find(":selected").val() === 'style_three' ) {

                $("#ut-general-image-style").hide();
                $("#ut-general-border-radius").hide();

                $("#ut-general-title-background").hide();
                $("#ut-general-title-background-color").hide();

            } else {

                $("#ut-general-image-style").show();
                $("#ut-general-border-radius").show();

                $("#ut-general-title-background").show();
                $("#ut-general-title-background-color").show();

            }


            
            $("#ut_gallery_options_style_1_caption_content").trigger("change");
            $("#ut_gallery_options_style_2_caption_content").trigger("change");
            
        });
        
        $("#ut_gallery_options_style").trigger("change");
        
        
        $("#ut_gallery_options_style_1_caption_content").change(function(){
            
            if( $(this).find(":selected").val() === 'custom_text' && $(this).is(":visible") === true ) {
                
                $("#ut_gallery_options_style_1_caption_custom_text").show();
                
            } else {
                
                $("#ut_gallery_options_style_1_caption_custom_text").hide();
                
            }
            
        });
        
        $("#ut_gallery_options_style_1_caption_content").trigger("change");
        
        $("#ut_gallery_options_style_2_caption_content").change(function(){
            
            if( $(this).find(":selected").val() === 'custom_text' && $(this).is(":visible") === true ) {
                
                $("#ut_gallery_options_style_2_caption_custom_text").show();
                
            } else {
                
                $("#ut_gallery_options_style_2_caption_custom_text").hide();
                
            }
            
        });
        
        $("#ut_gallery_options_style_2_caption_content").trigger("change");



        /**
         * Hide some settings not available react slider
         */

        var unrelated_settings_for_react_slider = [
            'ut-general-title-color',
            'ut-general-title-background',
            'ut-general-title-background-color',
            'ut-general-title-alignment',
            'ut-general-caption-content-color',
            'ut-general-hover-color',
            'ut-general-hover-color-opacity',
            'ut-general-image-style',
            'ut-general-border-radius',
            'ut-general-title-slideup',
            'ut-general-title-slideup-content',
            'ut-general-slideup-color',
            'ut-general-caption-position',
            'ut-general-portfolio-style',
            'ut-general-portfolio-details'
        ];


        function react_slider_dependency( type ) {

            if( type === 'ut_react_carousel' ) {

                $.each( unrelated_settings_for_react_slider, function(index, value) {

                    $('#' + value).hide();

                });

                // limit detail type
                $('#ut-general-detail-style').find('[value="slideup"]').hide();
                $('#ut-general-detail-style').find('[value="popup"]').hide();

                // make a reset if slideup was selected
                if( $('#ut-general-detail-style').val() === 'slideup' ) {

                    $('#ut-general-detail-style').prop('selectedIndex', 2);

                }

            } else {

                $.each( unrelated_settings_for_react_slider, function(index, value) {

                    $('#' + value).show();

                });

                // remove limitation detail type
                $('#ut-general-detail-style').find('[value="slideup"]').show();
                $('#ut-general-detail-style').find('[value="popup"]').show();

            }

        }

        var $ut_portfolio_type = $("#ut_portfolio_type");

        /**
         * display chosen portfolio settings type after load
         */
        $ut_portfolio_type.each(function(){

            var type = $(this).find(":selected").val();
            $('.ut-option-section').hide();

            if( type ) {
                $( '#' +  type + '_options' ).show();
            }

            react_slider_dependency( type );

        });

        /**
         * display chosen portfolio settings type on change
         */

        $ut_portfolio_type.change(function() {

            var type = $(this).find(":selected").val();
            $('.ut-option-section').hide();

            if( type ) {
                $( '#' +  type + '_options' ).show();
            }

            react_slider_dependency( type );

        });
        
        
        
        
    });
    
})(jQuery);
 /* ]]> */	