<?php

/*
 * Portfolio Management by United Themes
 * http://unitedthemes.com/
 */
 

if ( !function_exists( 'ut_get_image_id' ) ) {
    
    function ut_get_image_id($image_url) {
        
        global $wpdb;
        
        if( empty($image_url) ) {
            return;
        }
        
        $prefix = $wpdb->prefix;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $image_url )); 
        
        return isset($attachment[0]) ? $attachment[0] : ''; 
            
            
    }

}

if( !function_exists('ut_portfolio_get_image_id') ) {
 
    function ut_portfolio_get_image_id( $attachment_url ) {
        
        global $wpdb;
        $attachment_id = false;
    
        if ( '' == $attachment_url ) {
            return;
        }
        
        $upload_dir_paths = wp_upload_dir();
     
        /* Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image */
        if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
     
            /* If this is the URL of an auto-generated thumbnail, get the URL of the original image */
            $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
     
            /* Remove the upload path base directory from the attachment URL */
            $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
     
            /* Finally, run a custom database query to get the attachment ID from the modified attachment URL */
            $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
     
        }
     
        return $attachment_id;
        
    }

}

if( !function_exists('ut_escape_javascript') ) {

    function ut_escape_javascript($string) {
                
        return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
    }

} 
 
/*
|--------------------------------------------------------------------------
| Gallery Portfolio Post
|--------------------------------------------------------------------------
*/
if( !function_exists('ut_portfolio_flex_slider') ) {

    function ut_portfolio_flex_slider( $postID , $singular = false , $image_style = "ut-square" , $ut_page_hero_type = '' ) {
        
        // switch option key
        switch( $ut_page_hero_type ) {
        
            case 'slider':
            $optionkey = 'ut_page_hero_slider';
            break;
            
            case 'transition':
            $optionkey = 'ut_page_hero_fancy_slider';
            break;
            
            case 'tabs':
            $optionkey = 'ut_page_hero_tabs';
            break;
            
            default:  
            $optionkey = '';  
        
        }  
        
		/* new portfolio settings */
		$has_simple_gallery = false;
		$onpage_media_type  = get_post_meta( $postID , 'ut_onpage_portfolio_media_type' , true);
		
		if( $onpage_media_type == 'gallery' ) {
			
			$has_simple_gallery = true;
			$onpage_gallery = get_post_meta( $postID , 'ut_onpage_portfolio_gallery' , true);
			
			// put images into array
			$ut_gallery_images = explode( ",", $onpage_gallery);
			
		}
		/* end new gallery */
		
		
        $ut_background_slider_slides = get_post_meta( $postID , $optionkey , true );                                                                
        
        if( empty( $ut_page_hero_type ) || empty( $ut_background_slider_slides ) || $has_simple_gallery ) :
            
            // get all necessary image ID's from content gallery
            if( !$has_simple_gallery )
			$ut_gallery_images = ut_portfolio_extract_gallery_images_ids( $postID );
            
            // start output
            if ( !empty( $ut_gallery_images ) && is_array( $ut_gallery_images )  ) : 
                    
            $script = "<script type='text/javascript'>
            /* <![CDATA[ */
            
            (function($){
                
                'use strict';
        
                $(window).on('load', function () {
                    
                    $('#portfolio-gallery-slider-$postID').ut_require_js({
                        plugin: 'flexslider',
                        source: 'flexslider',
                        callback : function ( element ) {
                            
                            element.flexslider({
                        
                                animation: 'fade',
                                controlNav: false,
                                animationLoop: true,
                                slideshow: false,
                                smoothHeight: true,
                                startAt: 0
                                                    
                            });                            
                            
                        }      
                        
                    });
                    
                });
            
            })(jQuery);
            
            /* ]]> */
            </script>";
        
            
        
            $slider ='<div class="ut-portfolio-gallery-slider flexslider" id="portfolio-gallery-slider-' . $postID . '">';
                
                $slider .='<ul class="slides">';
                    
                    foreach ( $ut_gallery_images as $ID => $imagedata ) : 
                    
                        if( isset( $imagedata->guid ) && !empty($imagedata->guid) ) {
                                    
                            $image = $imagedata->guid; // fallback to older wp versions
                            
                        } else {
                            
                            $image = wp_get_attachment_image_src( $imagedata, 'full' );					
                            
                        }
                        
                        if( !empty( $image[0] ) ) :
        
                        // ratio
                        $ratio = !empty( $image[1] ) && !empty( $image[2] ) ? $image[1] / $image[2] : false;        

                        if( $ratio ) {

                            $targetWidth  = 1160;
                            $targetHeight = round( $targetWidth / $ratio );

                            $image = ut_resize( $image[0], $targetWidth, $targetHeight, true, true, true );

                        } else {
                            
                            $image = $image[0];
                            $targetWidth = $image[1];
                            $targetHeight = $image[2];
                            
                        }
                    
                        $caption = !empty( get_post( $imagedata )->post_excerpt ) ? get_post( $imagedata )->post_excerpt : '';
        
                        $slider .='<li>';
                                
                                /* output for single pages */
                                if( !$singular ) {
                                    
                                    if( !empty( $caption ) ) {
                                    
                                        $slider .= '<figure class="ut-post-thumbnail-caption-wrap" data-caption="' . esc_attr( $caption ) . '">';
                                        
                                            $slider .='<img width="'.$targetWidth.'" height="'.$targetHeight.'" src="" class="ut-load-me ' . $image_style . '" alt="' . get_the_title( $postID ) . '" data-original="' . $image . '" />';
                                        
                                            $slider .='<figcaption class="ut-post-thumbnail-caption">' . $caption . '</figcaption>';
                                        
                                        $slider .= '</figure>';
                                        
                                    } else {
                                        
                                        $slider .='<img width="'.$targetWidth.'" height="'.$targetHeight.'" src="" class="ut-load-me ' . $image_style . '" alt="' . get_the_title( $postID ) . '" data-original="' . $image . '" />';
                                        
                                    }
                                    
                                } else {
                                    
                                    $slider .='<img src="' . $image . '" class="ut-load-me ' . $image_style . '" alt="' . get_the_title( $postID ) . '" />';
                                    
                                }
                                                            
                            $slider .='</li>';
                            
                        endif;
                                        
                  endforeach;
        
                $slider .='</ul>';
            $slider .='</div>';
            
            if( $singular ) {
                $slider = ut_compress_java($script) . $slider;
            }
            
            return $slider;
            
            endif; 
        
        endif;
		
        if( !empty($ut_page_hero_type) && !empty($ut_background_slider_slides) && is_array($ut_background_slider_slides) ) {
            
                $script = "<script type='text/javascript'>
                /* <![CDATA[ */
                
                (function($){
                    
                    'use strict';
            
                    $(window).on('load', function () {
                        
                        $('#portfolio-gallery-slider-$postID').ut_require_js({
                            plugin: 'flexslider',
                            source: 'flexslider',
                            callback : function ( element ) {
                                
                                element.flexslider({
                            
                                    animation: 'fade',
                                    controlNav: false,
                                    animationLoop: true,
                                    slideshow: false,
                                    smoothHeight: true,
                                    startAt: 0
                                                        
                                });                            
                                
                            }      
                        
                        });                        
                        
                    });
                
                })(jQuery);
                
                /* ]]> */
                </script>";
                
                $slider ='<div class="ut-portfolio-gallery-slider flexslider" id="portfolio-gallery-slider-' . $postID . '">';
                    $slider .='<ul class="slides">';
                    
                        foreach ($ut_background_slider_slides as $slide) :
                                                                                    
                            $slider .='<li>';
                                
                                if( !$singular ) {
                                    
                                    $image = array("1920","1080"); 
                                    
                                    if( function_exists('ut_get_image_id') ) {
                                        
                                        $imageID = ut_get_image_id($slide['image']);
                                        $image   = wp_get_attachment_image_src($imageID , 'single-post-thumbnail');
                                        $caption = !empty( get_post( $imageID )->post_excerpt ) ? get_post( $imageID )->post_excerpt : '';
                                        
                                    }                                    
                                    
                                    // ratio
                                    $ratio = !empty( $image[1] ) && !empty( $image[2] ) ? $image[1] / $image[2] : false;        

                                    if( $ratio ) {

                                        $targetWidth  = 1160;
                                        $targetHeight = round( $targetWidth / $ratio );

                                        $image = ut_resize( $image[0], $targetWidth, $targetHeight, true, true, true );

                                    } else {
                                        
                                        $image = !empty( $image[0] ) ? $image[0] : $slide['image'];
                                        $targetWidth = $image[1];
                                        $targetHeight = $image[2];

                                    }
                                    
                                    if( !empty( $caption ) ) {
                                    
                                        $slider .= '<figure class="ut-post-thumbnail-caption-wrap" data-caption="' . esc_attr( $caption ) . '">';
                                        
                                            $slider .='<img width="'.$targetWidth.'" height="'.$targetHeight.'" src="" class="ut-load-me ' . $image_style . '" alt="' . get_the_title( $postID ) . '" data-original="' . $image . '" />';
                                        
                                            $slider .='<figcaption class="ut-post-thumbnail-caption">' . $caption . '</figcaption>';
                                        
                                        $slider .= '</figure>';
                                        
                                    } else {
                                        
                                        $slider .='<img width="'.$targetWidth.'" height="'.$targetHeight.'" src="" class="ut-load-me ' . $image_style . '" alt="' . get_the_title( $postID ) . '" data-original="' . $image . '" />';
                                        
                                    }                                    
                                    
                                } else {
                                    
                                    $slider .='<img src="' . $image . '" class="ut-load-me ' . $image_style . '" alt="' . get_the_title( $postID ) . '" />';
                                    
                                }
                            
                            $slider .='</li>';
                    
                        endforeach;
                    
                    $slider .='</ul>';
                $slider .='</div>';                
                        
            if( $singular ) {
                $slider = ut_compress_java($script) . $slider;
            }
            
            return $slider;
              
        }
        
    }
}

/*
|--------------------------------------------------------------------------
| Lightgallery for Portfolio Post
|--------------------------------------------------------------------------
*/

$GLOBALS['lightgallery_count'] = 1;

if( !function_exists('ut_portfolio_lightgallery') ) {

    function ut_portfolio_lightgallery( $postID , $token , $ut_page_hero_type = '' ) {
        
        /* switch option key */
        switch ($ut_page_hero_type) {
        
            case 'slider':
            $optionkey = 'ut_page_hero_slider';
            break;
            
            case 'transition':
            $optionkey = 'ut_page_hero_fancy_slider';
            break;
            
            case 'tabs':
            $optionkey = 'ut_page_hero_tabs';
            break;
            
            default:  
            $optionkey = '';  
        
        }  
        
        $ut_background_slider_slides = get_post_meta($postID , $optionkey , true);        
		
		/* new portfolio settings */
		$has_simple_gallery = false;
		$onpage_media_type  = get_post_meta( $postID , 'ut_onpage_portfolio_media_type' , true );
		
		if( $onpage_media_type == 'gallery' ) {
			
			$has_simple_gallery = true;
			$onpage_gallery = get_post_meta( $postID , 'ut_onpage_portfolio_gallery' , true );
			
			// put images into array
			$ut_gallery_images = explode( ",", $onpage_gallery );
			
		}
		/* end new gallery */
		
		
        if( empty( $ut_page_hero_type ) || empty( $ut_background_slider_slides ) || $has_simple_gallery ) :                
                                                                    
            /* get all necessary image ID's */
            if( !$has_simple_gallery )
			$ut_gallery_images = ut_portfolio_extract_gallery_images_ids( $postID );            
            
            /* start output */
            if ( !empty( $ut_gallery_images ) && is_array( $ut_gallery_images )  ) : 
            
            /* needed vars */
            $api_images = NULL;
                                    
            $counter = 1;				
            foreach ( $ut_gallery_images as $ID => $imagedata ) : 
                    
                if( isset( $imagedata->guid ) && !empty($imagedata->guid) ) {
                            
                    $image = $imagedata->guid; // fallback to older wp versions
                    
                } else {
                    
                    $image = wp_get_attachment_image_src( $imagedata , 'ut-lightbox' );
                    $image = $image[0];
                    
                    $thumb = wp_get_attachment_image_src( $imagedata , 'thumbnail' );
                    $thumb = !empty( $thumb[0] ) ? $thumb[0] : $image[0];
                    
                    
                }
                
                if( !empty( $image[0] ) ) : 
                    
                    $api_images .= '{';
                        $api_images .= '"src"  : "' . $image .'",';
                        $api_images .= '"thumb": "' . $thumb .'",';
                        $api_images .= '"subHtml": "<h4>' . addslashes( get_the_title( $imagedata ) ) . '</h4><p>' . ut_escape_javascript( get_post( $imagedata )->post_excerpt ) .'</p>",';                        
                    $api_images .= '}';                         
                                                
                endif;                   
                
                if( $counter != count( $ut_gallery_images ) ) { $api_images .= ','; }
                    
            $counter++;
            endforeach;
            
            
            $script = "<script type='text/javascript'>
            /* <![CDATA[ */
            
            (function($){
                
                'use strict';
                
                $(document).ready(function(){
                    
                    if( site_settings !== undefined && site_settings.lg_type === 'lightgallery' ) {
                    
	                    $('.ut-portfolio-popup-" . esc_attr( $postID ) . "').on('click', function(event) {
	                        
	                        $(this).ut_require_js({
	                            plugin: 'lightGallery',
	                            source: 'lightGallery',
	                            callback: function (element) {
	
	                                element.lightGallery({
	                                    dynamic: true, 
	                                    hash: false,
	                                    dynamicEl: [" . $api_images . "],
	                                    galleryId: " . $GLOBALS['lightgallery_count'] ."
	                                });
	
	                            }
	
	                        });
	                        
	                        event.preventDefault();                        
	                        
	                    });
                    
                    } else {
                        
                        $('.ut-portfolio-popup-" . esc_attr( $postID ) . "').on('click', function(event) {
                            
                            UT_Morph_Box_APP.create_morph_gallery();
                            
                        }); 
                        
                    }
                                    
                });
                        
            })(jQuery);
            
            /* ]]> */
            </script>";				
            
            $GLOBALS['lightgallery_count']++;
            
            return $script;
            
            endif;
            
		endif;
        
        if( !empty( $ut_page_hero_type ) && !empty( $ut_background_slider_slides ) && is_array( $ut_background_slider_slides ) ) {
        
            /* needed vars */
            $api_images = NULL;
                        
            $counter = 1;
            foreach ( $ut_background_slider_slides as $key => $slide ) :                
                
                $image_id = NULL;
                                
                if( !empty( $slide['image'] ) ) : 
                    
                    $image_id = ut_portfolio_get_image_id( $slide['image'] );
                    
                    /* thumb */
                    $thumb = wp_get_attachment_image_src( $image_id , 'thumbnail' );
                    $thumb = !empty( $thumb[0] ) ? $thumb[0] : $slide['image'];
                    
                    $api_images .= '{';
                    
                    $api_images .= '"src"  : "' . $slide['image'] .'",';
                    $api_images .= '"thumb": "' . $thumb .'",';                                                    
                                        
                    if( empty( $slide['description'] ) && $image_id ) {
                    
                        $api_titles = addslashes( get_the_title( $image_id ) );
                        
                    } else {
                        
                        $api_titles = addslashes( $slide['description'] );
                    
                    }
                    
                    if( empty( $slide['catchphrase'] ) && $image_id ) {
                        
                        $api_descriptions = addslashes( get_post( $image_id )->post_excerpt );
                        
                    } else {
                        
                        $api_descriptions = addslashes( $slide['catchphrase'] );
                    
                    }
                    
                    $api_images .= '"subHtml": "<h4>' . $api_titles . '<h4><p>' . ut_escape_javascript( $api_descriptions ) . '</p>"';
                    
                    $api_images .= '}';
                                                    
                endif;
                
                if( $counter != count( $ut_background_slider_slides ) ) { $api_images .= ','; }
                
                
            $counter++;
            endforeach;
            
            $script = "<script type='text/javascript'>
            /* <![CDATA[ */
            
            (function($){
                
                'use strict';
                
                $(document).ready(function(){
                    
                    if( site_settings !== undefined && site_settings.lg_type === 'lightgallery' ) {
                    
	                    $('.ut-portfolio-popup-" . esc_attr( $postID ) . "').on('click', function(event) {                       
	                       
	                       $(this).ut_require_js({
	                            plugin: 'lightGallery',
	                            source: 'lightGallery',
	                            callback: function (element) {
	
	                                element.lightGallery({
	                                    dynamic: true,
	                                    hash: false,
	                                    dynamicEl: [" . $api_images . "],
	                                    galleryId: " . $GLOBALS['lightgallery_count'] . "
	                                });
	
	                            }
	
	                        });
	                       
	                        event.preventDefault();                        
	                        
	                    });
                    
                    } else {
                        
                        $('.ut-portfolio-popup-" . esc_attr( $postID ) . "').on('click', function(event) {
                            
                            UT_Morph_Box_APP.create_morph_gallery();
                            
                        });
                        
                    }
                                    
                });
                        
            })(jQuery);
            
            /* ]]> */
            </script>";				
            
            $GLOBALS['lightgallery_count']++;
            
            return $script;
            
        
        }
		
    }
}

/*
|--------------------------------------------------------------------------
| Video Portfolio Post
|--------------------------------------------------------------------------
*/

if( !function_exists('ut_get_portfolio_format_video_content') ) {

    function ut_get_portfolio_format_video_content( $content ) {
        
        /* needed variables */
        $videofound = false;        
		$value = ut_portfolio_url_grabber( $content );
		
        /* check for vimeo */
        $value = ut_check_for_vimeo($value);           
                        
        if( !empty( $value ) ) {
            
            /* set video found */
            $videofound = true;
            
        }
                
        /* we have a meta value , lets check its syntax and return it */
        if( $videofound ) {
        
            if ( is_numeric( $value ) ) {
                
                $video = wp_get_attachment_url( $value );
                return do_shortcode( sprintf( '[video src="%s"]', $video ) );
                
            } elseif ( preg_match( '/' . get_shortcode_regex() . '/s', $value ) ) {

                return do_shortcode( $value );
                
            } else {
                
                return $value;
                
            }
			
        }
		
    }
	
}


if( !function_exists('ut_get_portfolio_format_video_media') ) {

    function ut_get_portfolio_format_video_media( $postID ) {
        
		$content = false;		
		
		// hero type settings
		$ut_page_hero_type    = get_post_meta( $postID, 'ut_page_hero_type' , true );
        $ut_page_video_source = get_post_meta( $postID, 'ut_page_video_source' , true );
		
		
		/* new portfolio settings since 4.7.4 */
		$has_media_type 	= false;
		$onpage_media_type  = get_post_meta( $postID , 'ut_onpage_portfolio_media_type' , true);
		
		if( $onpage_media_type == 'video' ) {
			
			// set flag for new media
			$has_media_type = true;
			
			// assign video source
			$ut_page_hero_type    = 'video';
			$ut_page_video_source = get_post_meta( $postID , 'ut_onpage_portfolio_video_source' , true);
			
		}
		/* end new portfolio settings */
		
		
	   	if( !empty( $ut_page_hero_type ) && $ut_page_hero_type == 'video' ) { 
			
			// youtube video 
            if( isset( $ut_page_video_source ) && $ut_page_video_source == 'youtube' )  {                                                
            
				if( !$has_media_type )
                $content = get_post_meta($postID , 'ut_page_video', true);
				
				if( $has_media_type )
                $content = get_post_meta($postID , 'ut_onpage_portfolio_video_youtube', true); 			
            
            }
			
			// vimeo video
            if( isset( $ut_page_video_source ) && $ut_page_video_source == 'vimeo' )  {                                                
            
				if( !$has_media_type )
                $content = get_post_meta($postID , 'ut_page_video_vimeo', true);
				
				if( $has_media_type )
                $content = get_post_meta($postID , 'ut_onpage_portfolio_video_vimeo', true);                 
            
            }
			
			// selfhosted video - current not support for lightbox
            if( isset( $ut_page_video_source ) && $ut_page_video_source == 'selfhosted' ) {
				
				$video_player = '<div style="display:none;" id="ut-selfhosted-lightbox-player-' . $postID . '">';	
				
					$video_player .= '<video class="lg-video-object lg-html5" controls disablePictureInPicture  preload="none">';
				
						$mp4 = get_post_meta( $postID , 'ut_onpage_portfolio_video_mp4', true );
						if( $mp4 ) {							
							$video_player .= '<source src="' . esc_url( $mp4 ) . '" type="video/mp4">';							
						}

						$ogg = get_post_meta( $postID , 'ut_onpage_portfolio_video_ogg', true );
						if( $ogg ) {							
							$video_player .= '<source src="' . esc_url( $ogg ) . '" type="video/ogg">';							
						}
				
						$webm = get_post_meta( $postID , 'ut_onpage_portfolio_video_webm', true );
						if( $webm ) {							
							$video_player .= '<source src="' . esc_url( $webm ) . '" type="video/webm">';							
						}

                        // hero fallback
                        if( !$mp4 && !$ogg && !$webm ) {

                            $mp4 = get_post_meta($postID, 'ut_page_video_mp4', true);
                            if ($mp4) {
                                $video_player .= '<source src="' . esc_url($mp4) . '" type="video/mp4">';
                            }

                            $ogg = get_post_meta( $postID , 'ut_page_video_ogg', true );
                            if( $ogg ) {
                                $video_player .= '<source src="' . esc_url( $ogg ) . '" type="video/ogg">';
                            }

                            $webm = get_post_meta( $postID , 'ut_page_video_webm', true );
                            if( $webm ) {
                                $video_player .= '<source src="' . esc_url( $webm ) . '" type="video/webm">';
                            }

                        }

					$video_player .= '</video>';				
				
				$video_player .= '</div>';
				
				$content = array(
					'selfhosted' => $video_player				
				);				
				
			}  
			
			// custom video
            if( isset($ut_page_video_source) && $ut_page_video_source == 'custom' ) {                                                
                
				if( !$has_media_type )
                $content = get_post_meta($postID , 'ut_page_video_custom', true);
				
				if( $has_media_type )
                $content = get_post_meta($postID , 'ut_onpage_portfolio_video_custom', true);
				
				$content = array(
					'iframe' => $content				
				);
				
				
                
            }

		}		
		
		return $content;
		
    }
	
}

function ut_get_portfolio_preview_video( $postID, $post_format, $showcase = '', $in_slider = false ) {

    if( !class_exists('UT_Background_Video_Player') ) {
        return false;
    }

    if( get_post_meta( $postID, 'ut_portfolio_showcase_video_preview', true ) != 'on' ) {
        return false;
    }

    $exclude_from = get_post_meta( $postID, 'ut_portfolio_showcase_video_exclude', true );

    if( is_array( $exclude_from ) && array_key_exists( $showcase, $exclude_from ) ) {
    	return false;
    }

    // if original video plays we only play 10 seconds
    $trailer = false;

    // get preview videos
    $mp4  = get_post_meta( $postID, 'ut_portfolio_showcase_video_mp4', true );
    $ogg  = get_post_meta( $postID, 'ut_portfolio_showcase_video_ogg', true );
    $webm = get_post_meta( $postID, 'ut_portfolio_showcase_video_webm', true );

    // fallback to selfhosted videos
    if( $post_format == 'video' && get_post_meta( $postID, 'ut_onpage_portfolio_video_source', true ) == 'selfhosted' && empty( $mp4 ) ) {

        $mp4  = get_post_meta( $postID, 'ut_onpage_portfolio_video_mp4', true );
        $ogg  = get_post_meta( $postID, 'ut_onpage_portfolio_video_ogg', true );
        $webm = get_post_meta( $postID, 'ut_onpage_portfolio_video_webm', true );

        $trailer = true;

    }

    // other player configs
    $play_event = get_post_meta( $postID, 'ut_portfolio_showcase_video_play_event', true );
    $appear_offset = 'auto';

    if( $in_slider ) {

        $play_event    = 'on_appear';
        $appear_offset = 'auto';

    }

    // Background Player
    $video = new UT_Background_Video_Player();

    return $video->handle_shortcode( array(
        'id'                    => $postID,
        'containment'           => '#ut-portfolio-article-' . $postID . ' .ut-portfolio-item > a',
        'source'                => 'selfhosted',
        'play_event'            => empty( $play_event ) ? 'on_hover' : $play_event,
        'appear_offset'         => $appear_offset,
        'lazy_load'             => true,
        'preload'               => 'auto',
        'mp4'                   => $mp4,
        'ogg'                   => $ogg,
        'webm'                  => $webm,
        'video_trailer'         => $trailer,
        'force_aspect_ratio'    => 'no',
        'volume'                => '0',
        'glitch_effect'         => get_post_meta( $postID, 'ut_portfolio_showcase_video_glitch', true ),
        'video_filter'          => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter', true ),
        'video_filter_action'   => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter_action', true ),
        'contrast'              => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter_contrast', true ),
        'brightness'            => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter_brightness', true ),
        'saturate'              => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter_saturate', true ),
        'grayscale'             => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter_grayscale', true ),
        'blur'                  => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter_blur', true ),
        'sepia'                 => get_post_meta( $postID, 'ut_portfolio_showcase_video_filter_sepia', true ),
    ) );

}

if( !function_exists('ut_portfolio_url_grabber') ) {
 
    function ut_portfolio_url_grabber( $string ) {
    
        $imageurl = !empty( $string ) ? preg_match_all('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', $string , $match) : '';
        return isset($match[0][0]) ? ut_portfolio_add_http($match[0][0]) : '';
    
    }
    
}

if( !function_exists('ut_portfolio_add_http') ) {

	function ut_portfolio_add_http( $url ) {
		
		if ( !preg_match("~^(?:f|ht)tps?://~i", $url ) ) {
			
            $url = is_ssl() ? "https://" . $url : "http://" . $url;
            
		}
        
		return esc_url_raw($url);
        
	}
	
}

if( !function_exists('ut_check_for_vimeo') ) {

	function ut_check_for_vimeo($url) {
		            
        if( preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo.com\/([a-z]*\/)*([0-9]{6,11})[?]?\.*/", trim($url) , $matches) ){
                                                
            if( !empty($matches[5]) ) {
                
                return ut_portfolio_add_http( 'vimeo.com/' . $matches[5] );
                              
            } else {
                
                return $url;
                
            }
                    
        } else {
                        
            return $url;
            
        }
		
	}
	
}

/*
|--------------------------------------------------------------------------
| Helper Function : Extract Attachment ID's from gallery shortcode
|--------------------------------------------------------------------------
*/
if ( !function_exists( 'ut_portfolio_extract_gallery_images_ids' ) ) {
    
    function ut_portfolio_extract_gallery_images_ids($postID = '') {
        
        if( empty( $postID ) ) {
            return false;    
        }
        
        $the_content = get_the_content( $postID );
        
        // search for gallery shortcode
        preg_match_all( "/(.?)\[(ut_image_gallery)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s", $the_content, $matches );
        
        if( isset( $matches[0][0] ) && strpos($matches[0][0], 'ut_image_gallery') !== false ) {
            
            preg_match_all('!\d+!', $matches[0][0], $images);
            
            return isset( $images[0] ) ? $images[0] : false;
            
        } 
        
        // search for gallery shortcode
        preg_match_all( "/(.?)\[(gallery)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s", $the_content, $matches );
        
        if( isset( $matches[0][0] ) && strpos($matches[0][0], 'gallery') !== false ) {
            
            preg_match_all('!\d+!', $matches[0][0], $images);
            
            return isset( $images[0] ) ? $images[0] : false;
            
        }        
    
    }

}


/*
|--------------------------------------------------------------------------
| Hex to RGB Changer
|--------------------------------------------------------------------------
*/
if( !function_exists('ut_hex_to_rgb') ) :

	function ut_hex_to_rgb($hex) {
				
		$hex = preg_replace("/#/", "", $hex);
		$color = array();
	 
		if(strlen($hex) == 3) {
			$color['r'] = hexdec(substr($hex, 0, 1) . $r);
			$color['g'] = hexdec(substr($hex, 1, 1) . $g);
			$color['b'] = hexdec(substr($hex, 2, 1) . $b);
		}
		else if(strlen($hex) == 6) {
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
		}
		
		$color = implode(',', $color);
		
		return $color;
	}

endif;


/*
|--------------------------------------------------------------------------
| Custom JS Minifier
|--------------------------------------------------------------------------
*/

if ( !function_exists( 'ut_compress_java' ) ) {

	function ut_compress_java($buffer) {
		
		// remove comments
		$buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);
		
		// remove tabs, spaces, newlines, etc.
		$buffer = str_replace(array("\r\n","\r","\t","\n",'  ','    ','     '), '', $buffer);
		
		// remove other spaces before/after )
		$buffer = preg_replace(array('(( )+\))','(\)( )+)'), ')', $buffer);
	
		return $buffer;
		
	}
	
}


/*
|--------------------------------------------------------------------------
| Generate Category List - Into Class
|--------------------------------------------------------------------------
*/
if( !function_exists('ut_generate_cat_list') ) :

	function ut_generate_cat_list( $categories , $separator = "," ) {
		
		if(!is_array($categories)) {
			return;
		}
		
		$return = '';
		$cats = count( $categories );
		$counter = 1;
		
		foreach( $categories as $category ) {
			
			$return .= $category->name;
			
			if( $counter < $cats) {
				$return .= $separator.' ';
			}
			
			$counter++;
			
		}
		
		return $return;
		
	}

endif;


/*
|--------------------------------------------------------------------------
| Generate Packery Caption
|--------------------------------------------------------------------------
*/
if( !function_exists('ut_generate_packery_caption') ) :

	function ut_generate_packery_caption( $ID ) {
		
		if( empty( $ID ) ) {
			return false;
		}
		
        $return = false;
        
        /* get theme option settings */
        if( ot_get_option('ut_portfolio_showcase_icon_type') == 'custom' && ot_get_option('ut_portfolio_showcase_custom_icon') )  {
        
            $return = '<div class="ut-portfolio-custom-icon"><img src="' . esc_url( ot_get_option('ut_portfolio_showcase_custom_icon') ) . '"></div>';
        
        } elseif( ot_get_option('ut_portfolio_showcase_font_icon')  ) {
            
            $return = '<div class="ut-portfolio-custom-icon"><i class="' . esc_attr( ot_get_option('ut_portfolio_showcase_font_icon') ) . '" aria-hidden="true"></i></div>';
            
        }        
		
		return $return;
		
	}

endif;


/*
|--------------------------------------------------------------------------
| Add WMode
|--------------------------------------------------------------------------
*/
if( !function_exists('ut_add_video_wmode_transparent') ) :
	
		function ut_add_video_wmode_transparent( $html, $url, $attr ) {
			
		if ( strpos( $html, "<embed src=" ) !== false ) { 
			
			return str_replace('</param><embed', '</param><param name="wmode" value="opaque"></param><embed wmode="opaque" ', $html); 
		
		} elseif ( strpos ( $html, 'feature=oembed' ) !== false ) { 
			
			return str_replace( 'feature=oembed', 'feature=oembed&wmode=opaque', $html ); 
		
		} else { 
			
			return $html; 
		
		}
	}
	
	add_filter( 'oembed_result', 'ut_add_video_wmode_transparent', 10, 3);

endif;