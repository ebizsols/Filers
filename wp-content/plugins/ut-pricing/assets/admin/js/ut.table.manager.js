/* <![CDATA[ */
(function($){
	
	"use strict";
	
    $(document).ready(function(){
		
        $(".add-feature").click(function(e) {
            
			var featuregroup = $(this).data("featuregroup"),
				column		 = $(this).data("column"),
				count 		 = $(this).data("featurecount")	;
			
			/* increase count */
			count = count + 1;
			
			/* update count */
			$(this).data("featurecount" , count)
			
			/* add new feature */
			$('#ut-repeat-'+featuregroup+'-'+column).append('<p><input type="text" class="regular-text" name="' + featuregroup + '['+column+'][features]['+count+'][title]" value="" /><span class="ut-admin-remove remove-feature">X</span></p>');			
			
			e.preventDefault();
			
        });
		
		$(document).on("click", ".remove-feature", function(e) { 
										
			$(this).parent().remove();
			e.preventDefault();
			
		});
        
        $(document).on("click", ".add-food", function(e) { 
                    
			var foodgroup = $(this).data("foodgroup"),
				card	  = $(this).data("card"),
				count 	  = $(this).data("foodcount");
			
			/* increase count */
			count = count + 1;
			
			/* update count */
			$(this).data("foodcount" , count)
			
			/* add new feature */
			$('#ut-repeat-'+foodgroup+'-'+card).append('<div class="ut-menu-card-item"><p><label>' + ut_menu_manager.title + '</label><input type="text" class="regular-text" name="' + foodgroup + '['+card+'][foods]['+count+'][title]" value="" /></p><p><label>' + ut_menu_manager.ingredients + '</label><textarea class="large-text code" rows="7" name="' + foodgroup + '['+card+'][foods]['+count+'][ingredients]" /></textarea></p><p><label>' + ut_menu_manager.price + '</label><input type="text" class="regular-text" name="' + foodgroup + '['+card+'][foods]['+count+'][price]" value="" /></p><span class="ut-admin-remove remove-food">X</span></div>');			
			
			e.preventDefault();
			
        });
		
		$(document).on("click", ".remove-food", function(e) { 
										
			$(this).parent().remove();
			e.preventDefault();
			
		});
				
		$( "#ut-table-tabs" ).tabs();
        
        
        
        /* move copy */
        $('.ut-column-to-copy').insertAfter('#post');
        
        $(document).on("click", ".ut-add-card", function(e) {
            
            e.preventDefault();
            
            var count = $(this).data('count');
            
            /* increase count */
            count++;
            
            /* copy dummy card */
            var $new_card = $('.ut-column-to-copy').clone().removeClass('ut-column-to-copy').attr('id','table-column-'+count );
            
            /* increase ID */
            $new_card.html($new_card.html().replace(/__id__/gi, count));
            
            /* append new card */
            $('#ut-table-tabs').append( $new_card );
                        
            var $new_tab = $('<li></li>').append('<a id="tab-for-table-column-'+ count +'" href="#table-column-'+ count +'">Card '+ count +'</a>');
            
            $('.ut-table-tabs-nav').append( $new_tab );            
            
            /* update count */
            $(this).data('count', count );
            
            $( "#ut-table-tabs" ).tabs( "refresh" );
            $( "#tab-for-table-column-"+ count).trigger('click');
        
        });
        
        $(document).on("click", ".ut-delete-card", function(e) {
            
            $('#table-column-' + $(this).data('card') ).remove();
            $('#tab-for-table-column-' + $(this).data('card') ).remove();
            
            e.preventDefault();
            
        });
        
        
        $('.ut-color-picker').minicolors();
        
        
        
	
	});
    
})(jQuery);
 /* ]]> */	