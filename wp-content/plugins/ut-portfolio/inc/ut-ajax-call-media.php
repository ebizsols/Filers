<?php

/*
 * Portfolio Management by United Themes
 * http://unitedthemes.com/
 */

/*
|--------------------------------------------------------------------------
| returns protfolio video
|--------------------------------------------------------------------------
*/
if ( ! function_exists( 'ut_get_portfolio_post' ) ) :

	function ut_get_portfolio_post() {
		
        /* needed variable */
        $content = $video_url = NULL;
        
		/* get portfolio id */
		$portfolio_id = (int)$_POST[ 'portfolio_id' ];
		
		/* get post object */
		$portfolio = get_post($portfolio_id , OBJECT);
		
		/* get post format */
		$post_format = get_post_format($portfolio_id);	
		
        /* iframe flag */
        $is_iframe = false;
        
        /* hero type setting - new with 2.6 */
        $ut_page_hero_type = get_post_meta($portfolio_id , 'ut_page_hero_type' , true);
        $ut_page_video_source = get_post_meta($portfolio_id, 'ut_page_video_source' , true);
        
		/* new portfolio settings since 4.7.4 */
		$has_media_type = false;
		$onpage_media_type  = get_post_meta( $portfolio_id , 'ut_onpage_portfolio_media_type' , true);
		
		if( $onpage_media_type == 'video' ) {
			
			// set flag for new media
			$has_media_type = true;
			
			// assign video source
			$ut_page_hero_type    = 'video';
			$ut_page_video_source = get_post_meta( $portfolio_id , 'ut_onpage_portfolio_video_source' , true);
			
		}
		/* end new portfolio settings */
		
		
		
		
        if( !empty( $ut_page_hero_type ) && $ut_page_hero_type == 'video' ) {
            
            // youtube video 
            if( isset( $ut_page_video_source ) && $ut_page_video_source == 'youtube' )  {                                                
            
				if( !$has_media_type )
                $content = get_post_meta($portfolio_id , 'ut_page_video', true);
				
				if( $has_media_type )
                $content = get_post_meta($portfolio_id , 'ut_onpage_portfolio_video_youtube', true); 			
                
                // try to catch video url
		        $video_url = ut_get_portfolio_format_video_content($content);
            
            }
            
            // vimeo video
            if( isset( $ut_page_video_source ) && $ut_page_video_source == 'vimeo' )  {                                                
            
				if( !$has_media_type )
                $content = get_post_meta($portfolio_id , 'ut_page_video_vimeo', true);
				
				if( $has_media_type )
                $content = get_post_meta($portfolio_id , 'ut_onpage_portfolio_video_vimeo', true); 
                
                /* try to catch video url */
		        $video_url = ut_get_portfolio_format_video_content($content);
            
            }
            
            // selfhosted video
            if( isset( $ut_page_video_source ) && $ut_page_video_source == 'selfhosted' ) {                                                
                
                $mp4 = $ogg = $webm = NULL;
                
				if( !$has_media_type ) {
					$mp4  = get_post_meta($portfolio_id ,'ut_page_video_mp4', true);
					$ogg  = get_post_meta($portfolio_id ,'ut_page_video_ogg', true);
					$webm = get_post_meta($portfolio_id ,'ut_page_video_webm', true);
				}
				
				if( $has_media_type ) {
					$mp4  = get_post_meta($portfolio_id ,'ut_onpage_portfolio_video_mp4', true);
					$ogg  = get_post_meta($portfolio_id ,'ut_onpage_portfolio_video_ogg', true);
					$webm = get_post_meta($portfolio_id ,'ut_onpage_portfolio_video_webm', true);
				}					
					
                if( !empty($mp4) || !empty($ogg) || !empty($webm) ) {
                    
                    $mp4 = !empty($mp4) ? 'mp4="'.$mp4.'"' : '';
                    $ogg = !empty($ogg) ? 'ogv="'.$ogg.'"' : '';
                    $webm = !empty($webm) ? 'mov="'.$webm.'"' : '';
                    
                    /* built wordpress shortcode */                        
                    $content = '[video '.$mp4.' '.$ogg.' '.$webm.']';
                
                }
                
            }
            
            /* custom video */
            if( isset($ut_page_video_source) && $ut_page_video_source == 'custom' ) {                                                
                
				if( !$has_media_type )
                $content = get_post_meta($portfolio_id , 'ut_page_video_custom', true);
				
				if( $has_media_type )
                $content = get_post_meta($portfolio_id , 'ut_onpage_portfolio_video_custom', true);
                
                if( strpos( $content, '</iframe>' ) !== false ) {
                    
                    $video_url = $content;
                    $is_iframe = true;
                    
                } else {
                    
                    /* try to catch video url */
		            $video_url = ut_get_portfolio_format_video_content($content);
                    
                }
                
            }                                             
            
            // content is still empty - let's assign the regular content
            if(empty($content)) {
                
                $content = $portfolio->post_content;
                
                // try to catch video url
		        $video_url = ut_get_portfolio_format_video_content($content);
                
            }            
        
        } else {
        
            $content = $portfolio->post_content;
            
            // try to catch video url
		    $video_url = ut_get_portfolio_format_video_content($content);            
        
        }
        	
		/* get embed code */ 
		if( !empty( $video_url ) ) {
            
            if( !$is_iframe ) {
                
                $embed_code = wp_oembed_get( $video_url );
                
            } else {
                
                $embed_code = $video_url;
                
            }
            
        } else {
             
             /*no video URL found, maybe we can display the embedded code? */
             if(isset($ut_page_video_source) && $ut_page_video_source == 'custom') {
                
                $embed_code = get_post_meta($portfolio_id , 'ut_page_video_custom', true);
                
             } 
             
             elseif(isset($ut_page_video_source) && $ut_page_video_source == 'selfhosted') {
                
                $embed_code = $content;
                
             } 
             
             else {
                
                /* set empty value */ 
                $embed_code = '';
                
             }
        
        }        
        
        /* add high quality */
        if(strpos($embed_code, 'youtu.be') !== false || strpos($embed_code, 'youtube.com') !== false){
            $embed_code = preg_replace("@src=(['\"])?([^'\">\s]*)@", "src=$1$2&vq=hd720", $embed_code);
        }
        
		echo do_shortcode($embed_code);
		
        die(1);
		
	}

endif;

add_action( 'wp_ajax_nopriv_ut_get_portfolio_post', 'ut_get_portfolio_post' );
add_action( 'wp_ajax_ut_get_portfolio_post', 'ut_get_portfolio_post' );