<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_C7_Shortcode' ) ) {
	
    class UT_C7_Shortcode {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_c7_shortcode';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Contact Form 7', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Load your desired contact form directly into the site.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    //'icon'            => 'fa fa-envelope ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/contact-form-7.png',
                    'category'        => 'Plugins',
                    'class'           => 'ut-vc-icon-module ut-plugin-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Select Contact Form 7', 'ut_shortcodes' ),
                            'param_name'        => 'form_id',
                            'group'             => 'General',
                            'value'             => ut_get_c7_forms()
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Form Skin', 'ut_shortcodes' ),
                            'param_name'        => 'form_skin',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Select Skin', 'ut_shortcodes' )     => '',
                                esc_html__( 'Dark Form Skin (for light backgrounds)', 'ut_shortcodes' ) => 'dark',
                                esc_html__( 'Light Form Skin (for dark backgrounds)', 'ut_shortcodes' ) => 'light'
                            ),
                        ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Label Colors', 'ut_shortcodes' ),
		                    'param_name'        => 'label_color',
		                    'group'             => 'General',
	                    ),
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => 'General'
                        )
                    )
                )

            ); // end mapping
        
        }
        
        function ut_create_shortcode( $atts ) {

            /**
             * @var $form_skin
             */

            extract( shortcode_atts( array (
                'form_id'     => '',
                'form_skin'   => '',
                'label_color' => '',
                'css'         => ''
            ), $atts ) ); 

            $skin = '';

            if( $form_skin ) {

                $skin = "ut-cf7-$form_skin-skin";

            }

            $custom_css = '';

	        /* unique ID */
	        $id = uniqid("ut_cf7_colors_");

	        if( !empty( $label_color ) ) {

		        $custom_css .= '#' . $id . ' { color:' . $label_color . '; }';

	        }

	        // attach css
	        if( !empty( $custom_css ) ) {
		        $custom_css = '<style type="text/css">' . ut_minify_inline_css( $custom_css ) . '</style>';
	        }

            if( !$form_id ) {                
                return $custom_css .'<div id="' . $id . '" class="wpb_content_element ' . $skin . ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . esc_html__( 'No Contact Form 7 selected!', 'ut_shortcodes' ) . '</div>';
            }
            
            if( defined( 'WPB_VC_VERSION' ) ) {                 
                return $custom_css. '<div id="' . $id . '" class="wpb_content_element ' . $skin . ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . do_shortcode( '[contact-form-7 id="' . esc_attr( $form_id ) . '"]' ) . '</div>';
            }  
            
            /* start output */
            return do_shortcode( $content );
        
        }
            
    }

}

new UT_C7_Shortcode;