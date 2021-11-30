/* <![CDATA[ */
!(function($){
	
	"use strict";
	
    $(document).ready(function(){

        if ( $().countdown ) {
            
            $.countdown.setDefaults($.countdown.regionalOptions['']);
            
            $('.ut-countdown').each(function() {
                
                if( $(this).data("type") === 'until' ) {
                
                    $(this).countdown($.extend({
                        until: new Date($(this).data("date")), 
                        format: $(this).data("format"),
                        compact: $(this).data("compact"),
                    }, $.countdown.regionalOptions[$(this).data("lang")]) );
                
                } else {
                
                    $(this).countdown($.extend({
                        since: new Date($(this).data("date")), 
                        format: $(this).data("format"),
                        compact: $(this).data("compact"),
                    }, $.countdown.regionalOptions[$(this).data("lang")] ) );
                
                }                
            
            });
        
        } 
    
    });    
        
})(window.jQuery);
 /* ]]> */	