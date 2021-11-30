<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin JS Scripts
 *
 * @since     4.0
 */

if ( ! function_exists( 'load_ut_sc_admin_scripts' ) ) :

	function load_ut_sc_admin_scripts() {
	    
        wp_enqueue_style(
            'ut-datetimepicker', 
            UT_SHORTCODES_URL .'/vc/admin/assets/css/jquery.datetimepicker.min.css'
        ); 
        
        wp_enqueue_style(
            'ut-vc-composer-css',
            UT_SHORTCODES_URL . '/vc/admin/assets/css/param-styles.css'
        );
        
        wp_enqueue_style(
            'ut-mceskin', 
            UT_SHORTCODES_URL . 'admin/css/ut.mceskin.css'
        );
        
        wp_enqueue_style(
            'ut-vc-styles', 
            UT_SHORTCODES_URL . 'admin/css/ut.vc.styles.css'
        );	
        			
		wp_register_script(
            'ut-vc-composer-js', 
            UT_SHORTCODES_URL .'/vc/admin/assets/js/vc-composer-functions.js', 
            array( 
				'jquery',
				'jquery-ui-slider' 
			)
        );
        
        wp_enqueue_script(
            'ut-datetimepicker-js', 
            UT_SHORTCODES_URL .'/vc/admin/assets/js/datetimerpicker/jquery.datetimepicker.full.min.js', 
            array( 'jquery' )
        );
        
		wp_enqueue_script( 'ut-vc-composer-js' );		
		
	}

endif;


/**
 * Admin Init
 *
 * @since     4.0
 */
if ( ! function_exists( 'ut_sc_admin_init' ) ) :

    function ut_sc_admin_init() {
        
        
        add_action('admin_print_styles-post.php', 'load_ut_sc_admin_scripts');
        add_action('admin_print_styles-post-new.php', 'load_ut_sc_admin_scripts');
            
    }
    
    add_action( 'admin_init' , 'ut_sc_admin_init' );
    
endif;


/*
 * Overlay for Visual Composer
 *
 * @since 4.5
 */
 
if ( !function_exists( 'ut_vc_overlay' ) ) {
    
    function ut_vc_overlay() {
        
        echo '<div id="ut-vc-overlay"></div>';
        
    }
    
    add_action('admin_footer', 'ut_vc_overlay');

}

/**
 * Inline Admin CSS
 *
 * @access    public
 * @since     1.1.0
 * @version   1.0.0
 */

function ut_sc_inline_admin_css() {

	global $post_ID; ?>

	<style type="text/css">

		#gallery-settings-link-to.link-to > option[value="post"] {
			display: none;
		}

	</style><?php


}

add_action( 'admin_head', 'ut_sc_inline_admin_css' );

