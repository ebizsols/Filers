<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Custom_Shortcode' ) ) {
	
    class UT_Custom_Shortcode {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_custom_shortcode';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Custom Shortcode', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Place a custom shortcode from a third party plugin.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'         => 'fa fa-code ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/custom-shortcode.png',
                    'category'        => 'Structual',
                    'class'           => 'ut-vc-icon-module ut-structual-module',
                    'content_element' => true,
                    'params'          => array(
                         array(
                            'type'        => 'textfield',
                            'heading'     => __( 'Insert Shortcode', 'ut_shortcodes' ),
                            'admin_label' => true,
                            'param_name'  => 'content',
                            'group'       => 'General'
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),
                        array(
                            'type'        => 'css_editor',
                            'param_name'  => 'css',
                            'group'       => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),


                    )

                )

            ); /* end mapping */
        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'class'     => '' ,
                'css'       => ''
            ), $atts ) ); 
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . $class . ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . do_shortcode( $content ) . '</div>';
            
            }  
            
            /* start output */
            return do_shortcode( $content );
        
        }
            
    }

}

new UT_Custom_Shortcode;