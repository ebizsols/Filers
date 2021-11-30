/* <![CDATA[ */
(function($){
	
	"use strict";
	
    $(document).ready(function(){
        
        $('.ut-range-slider').each(function() {
            
			$(this).slider({
                range: "max",
                min: $(this).data('min'),
                max: $(this).data('max'),
                step: $(this).data('step'),
                value: $(this).data('value'),
                slide: function( event, ui ) {
                    
					if( $(this).attr('data-global') && ui.value === $(this).data('global') ) {
					   
						$(this).parent().find('.wpb-textinput').val( ui.value ).trigger("change");
						$(this).parent().find('.ut-range-value').text("global").addClass("ut-range-value-global");
					   
					} else {
						
						$(this).parent().find('.wpb-textinput').val( ui.value ).trigger("change");
                    	$(this).parent().find('.ut-range-value').text( ui.value ).removeClass("ut-range-value-global");						
						
					}
                    
                }
				
            });
            
        });
        
        if( $('.vc_param_group-list').length ) {
        
            $('.vc_param_group-list').bind('DOMNodeInserted', function() {
                
                $(this).find(".ut-range-slider").each(function() {
                    
                    // check if rangeslider has been initinalized already
                    if( !$(this).hasClass("ui-widget") ) {
                        
                        $(this).addClass("ui-widget").slider({
                            range: "max",
                            min: $(this).data('min'),
                            max: $(this).data('max'),
                            step: $(this).data('step'),
                            value: $(this).data('value'),
                            slide: function( event, ui ) {

                                if( $(this).attr('data-global') && ui.value === $(this).data('global') ) {
					   
									$(this).parent().find('.wpb-textinput').val( ui.value ).trigger("change");
									$(this).parent().find('.ut-range-value').text("global").addClass("ut-range-value-global");

								} else {

									$(this).parent().find('.wpb-textinput').val( ui.value ).trigger("change");
									$(this).parent().find('.ut-range-value').text( ui.value ).removeClass("ut-range-value-global");						

								}

                            }
							
                        });
                        
                    }
                    
                });

            });
        
        }

        function show_custom_background_size( $element ) {

            if( $element.val() === 'custom' ) {

                $element.parent().next('.vc_background-style-custom').show();

            } else {

                $element.parent().next('.vc_background-style-custom').hide();

            }

        }

        $('select.vc_background-style').on('change', function () {

            show_custom_background_size( $(this) );

        });

        if( $('.vc_css-editor').length ) {

            $('.vc_css-editor').bind('DOMNodeInserted', function() {

                $('select.vc_background-style').each( function () {

                    show_custom_background_size( $(this) );

                });

            });

            $('select.vc_background-style').each( function () {

                show_custom_background_size( $(this) );

            });

        }
        
    });
        
})(window.jQuery);
 /* ]]> */	