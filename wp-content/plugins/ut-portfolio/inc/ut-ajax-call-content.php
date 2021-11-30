<?php
 
if( !function_exists('ut_fix_vc_section_empty_p_tags') ) {
    
    function ut_fix_vc_section_empty_p_tags( $content ){   
    
        
        $block = join("|",array( "vc_section" ));
         
        $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
        $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
         
        return $rep;

    }
        
}

if ( ! function_exists( 'ut_get_portfolio_post_content' ) ) :

	function ut_get_portfolio_post_content() {
		
        global $post;
                
		/* get portfolio id */
		$portfolio_id = (int)$_POST[ 'portfolio_id' ];
		
        $the_content = NULL;
        
        if( isset( $_POST[ 'show_title' ] ) && $_POST[ 'show_title' ] == 'off' ) {
            
            $show_title = false;
            
        } else {
            
            $show_title = true;
            
        }
        
        
		/* get post object */
		$post = get_post( $portfolio_id , OBJECT );
        
        setup_postdata( $post );
        
		/* get post format */
		$post_format = get_post_format( $portfolio_id );	
		
        /* try to catch video url */
		$video_url = ut_get_portfolio_format_video_content( get_the_content() );	
        
        /* load VC Shortcodes */ 
        if( class_exists('WPBMap') ) {
            WPBMap::addAllMappedShortcodes();
        }
        
        // check hero type
        $ut_page_hero_type = get_post_meta($portfolio_id , 'ut_page_hero_type' , true);
        $ut_page_hero_slider = get_post_meta($portfolio_id , 'ut_page_hero_slider' , true);
        
        if( $ut_page_hero_type == 'slider' && empty( $ut_page_hero_slider ) ) {
            $post_format = 'gallery';                
        }
        
        // check if visual composer has been used 
        if( preg_match( '/vc_section/', get_the_content() ) || preg_match( '/vc_row/', get_the_content() ) ) {
            
            $the_content = ut_fix_vc_section_empty_p_tags( get_the_content() );
            
            /* modify content ( fallback to old version ) */
            if( $post_format == 'video') {

                $the_content = str_replace( $video_url , "" , $the_content );
                $the_content = do_shortcode( $the_content );

            } elseif( $post_format == 'gallery') {

                $the_content = preg_replace( '/(.?)\[(gallery)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', '$1$6', $the_content );
                $the_content = do_shortcode( $the_content );        

            } else {

                $the_content = do_shortcode( $the_content );            

            }
            
            
        } else {
            
            /* modify content ( fallback to old version ) */
            if( $post_format == 'video') {

                $the_content = str_replace( $video_url , "" , get_the_content() );
                $the_content = do_shortcode( wpautop( $the_content ) );

            } elseif( $post_format == 'gallery') {

                $the_content = preg_replace( '/(.?)\[(gallery)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', '$1$6', get_the_content() );
                $the_content = do_shortcode( wpautop( $the_content ) );        

            } else {

                $the_content = do_shortcode( wpautop( get_the_content() ) );
                //$the_content = apply_filters( 'the_content' , get_the_content() );

            }    
            
        }
        
        
        $post_content = '';
        
        if( $show_title ) {
        
            $post_content = '<h2 class="ut-portfolio-title">' . get_the_title($portfolio_id) . '</h2>';
        
        }
            
        /* try to fetch VC Styles */
        $shortcodes_custom_css = get_post_meta( $portfolio_id, '_wpb_shortcodes_custom_css', true );
        
        if( $shortcodes_custom_css ) {
            $post_content .= '<style type="text/css">' . $shortcodes_custom_css . '</style>';
        }
        
        $post_content .= $the_content;

		$post_content = str_replace( array( 'ut-vc-200', 'ut-vc-160', 'ut-vc-120' ), 'ut-vc-80', $post_content );

		echo $post_content;

        wp_die();
		
	}

endif;

add_action( 'wp_ajax_nopriv_ut_get_portfolio_post_content', 'ut_get_portfolio_post_content' );
add_action( 'wp_ajax_ut_get_portfolio_post_content', 'ut_get_portfolio_post_content' );