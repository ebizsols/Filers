/* <![CDATA[ */
!(function($){
	
	"use strict";
        
    var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;
    
    $('#vc_ui-panel-edit-element .ut-file-picker-button').click( function( e ) {
        
        var $this = $( this );
        
        var send_attachment     = wp.media.editor.send.attachment,
            file_picker_button  = $this,
            file_remove_button  = $this.parent().find( '.ut-file-remove-button' ),
            input               = $this.parent().find( '.filepicker_field' ),
            display             = $this.parent().find( '.filepicker-display' );
        
        _custom_media = true;
        
        wp.media.editor.send.attachment = function( props, attachment ) {
            
            if(_custom_media) {
                
                display.html( attachment.url );
                input.val( attachment.id );
                file_picker_button.addClass( 'hidden' );
                file_remove_button.removeClass( 'hidden' );
                
            } else {
            
                return _orig_send_attachment.apply( this, [props, attachment] );
                
            };
            
        };
        
        wp.media.editor.open( file_picker_button );
        return false;
         
    });
    
    $('#vc_ui-panel-edit-element .ut-file-remove-button').click( function( e ) {
        
        var $this = $( this );
        
        var file_picker_button  = $this.parent().find( '.ut-file-picker-button' ),
            file_remover_button  = $this,
            input               = $this.parent().find( '.filepicker_field' ),
            display             = $this.parent().find( '.filepicker-display' );

        display.html( '' );
        input.val( '' );
        
        file_picker_button.removeClass( 'hidden' );
        file_remover_button.addClass( 'hidden' );
         
         
    });    
    
    
    $('.add_media').on( 'click', function() {
        _custom_media = false;
    });
        
})(window.jQuery);
 /* ]]> */	