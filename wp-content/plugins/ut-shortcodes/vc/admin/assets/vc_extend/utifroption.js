/* <![CDATA[ */
(function($){
	
	"use strict";
        
    var uri_pattern = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/ig;

    function update_iframe_textarea( $obj ) {
        
        setTimeout(function() {
            
            var val    = $obj.val(),
                result = val.match(uri_pattern);
            
            if( result !== null && result[0].length ) {
            
                $obj.val( result[0] );
                
            } 
            
        }, 100 );
    
    }
    
    $(document).on('paste', '.ut-iframe-input', function() {
            
        var $this = $(this);
        update_iframe_textarea( $this );
        
    });                 
            
})(jQuery);
 /* ]]> */	