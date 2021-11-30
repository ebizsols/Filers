<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Menu_Card_Shortcode' ) ) {
	
    class UT_Menu_Card_Shortcode {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_menu_card_shortcode';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Menu Card Module', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Load your desired menu card directly into the site.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/portfolio.png',
                    'category'        => 'Plugins',
                    'class'           => 'ut-vc-icon-module ut-plugin-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Select UT Menu Card', 'ut_shortcodes' ),
                            'param_name'        => 'menu_id',
                            'group'             => 'General',
                            'admin_label'       => true,
                            'value'             => ut_get_menu_cards()
                        ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Animate Menu Card?', 'ut_shortcodes' ),
		                    'param_name'        => 'animate_menu_card',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
			                    esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'on'
		                    ),
	                    ),

                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        )

                    )

                )

            ); // end mapping
        
        }
        
        function ut_create_shortcode( $atts ) {
                
            extract( shortcode_atts( array (
                'menu_id'           => '',
                'animate_menu_card' => '',
                'css'               => ''
            ), $atts ) ); 
            
            if( !$menu_id ) {                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . esc_html__( 'No Menu Card selected!', 'ut_shortcodes' ) . '</div>';                
            }
            
            if( defined( 'WPB_VC_VERSION' ) ) {                 
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . do_shortcode( '[ut_menu id="' . esc_attr( $menu_id ) . '" animate_menu_card="' . esc_attr( $animate_menu_card ) . '"]' ) . '</div>';
            }  
            
            /* start output */
            return do_shortcode( $content );
        
        }
            
    }

}

new UT_Menu_Card_Shortcode;