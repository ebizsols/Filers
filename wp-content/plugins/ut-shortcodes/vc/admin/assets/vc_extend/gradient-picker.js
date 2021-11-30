;(function($){
	
	"use strict";
    
    $(document).ready(function(){
    
        $(".ut-gradient-picker").each(function() {

            // check if colorpicker has been initinalized already
            if( !$(this).hasClass("asColorPicker-input") ) {

                $(this).addClass("asColorPicker-input").asColorPicker();

            }
            
        });
        
        if( $('.vc_param_group-list').length ) {
        
            $('.vc_param_group-list').bind('DOMNodeInserted', function() {
                
                $(this).find(".ut-gradient-picker").each(function() {
                    
                    // check if colorpicker has been initinalized already
                    if( !$(this).hasClass("asColorPicker-input") ) {
                        
                        $(this).addClass("asColorPicker-input").asColorPicker();
                        
                    }
                    
                });

            });
        
        }
    
    });        
	
})(jQuery);  