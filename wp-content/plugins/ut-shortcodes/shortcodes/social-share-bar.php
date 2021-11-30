<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Social_Share_Bar' ) ) {
	
    class UT_Social_Share_Bar {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_social_share_bar';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Social Share Bar', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Easy-to-use share buttons for your website or blog.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'category'        => 'Information',
                    // 'icon'            => 'fa fa-share-alt ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/social-share-bar.png',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        /* General */
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Share Text', 'ut_shortcodes' ),
                            'description'       => __( '<strong>(optional)</strong> default: Share', 'ut_shortcodes' ),
                            'param_name'        => 'share_text',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Alignment Tablet', 'ut_shortcodes' ),
		                    'param_name'        => 'align_tablet',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Alignment Mobile', 'ut_shortcodes' ),
		                    'param_name'        => 'align_mobile',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
		                    	esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
			                    esc_html__( 'inherit from larger'  , 'ut_shortcodes' ) => 'inherit',
		                    ),
	                    ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Share Text Font Weight', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Please keep in mind, that the selected font needs to support the font weight.', 'ut_shortcodes' ),
                            'param_name'        => 'share_text_font_weight',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ),
                                'lighter',
                                'normal',
                                'bold',
                                'bolder',
                                100,
                                200,
                                300,
                                400,
                                500,
                                600,
                                700,
                                800,
                                900,
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Share Text Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'share_text_letter_spacing',
                            'group'             => 'General',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),

                        ),
                        array(
                            'type'              => 'checkbox',
                            'heading'           => esc_html__( 'Activate Border?', 'unitedthemes' ),
                            'param_name'        => 'border',
                            'group'             => 'General',
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                        /* colors */
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Share Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'share_text_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Colors', 'ut_shortcodes' ),
                            'param_name'        => 'icon_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Colors Hover', 'ut_shortcodes' ),
                            'param_name'        => 'icon_color_hover',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'border_color',
                            'group'             => 'Colors',
                            'dependency'        => array(
                                'element' => 'border',
                                'value'   => array( 'true' ),
                            )
                        ),

                        /* Design Options */
                        array(
                            'type'          => 'css_editor',
                            'param_name'    => 'css',
                            'group'         => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),

                    )

                )

            ); /* end mapping */

        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'align'                     => 'left',
                'align_tablet'              => 'inherit',
                'align_mobile'              => 'center',
                'share_text'                => esc_html__( 'Share:', 'ut_shortcodes' ),
                'share_text_color'          => '',
                'share_text_font_weight'    => '',
                'share_text_letter_spacing' => '',
                'icon_color'                => '',
                'icon_color_hover'          => get_option('ut_accentcolor' , '#F1C40F'),
                'border'                    => '',
                'border_color'              => '',
                'class'                     => '',
                'css'                       => ''        
            ), $atts ) ); 
            
            $classes    = array();
            $classes[]  = $class;

            // Alignment
	        $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
	        $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

            $classes[]  = 'ut-project-sc-' . $align;
            $classes[]  = 'ut-project-sc-tablet-' . $align_tablet;
            $classes[]  = 'ut-project-sc-mobile-' . $align_mobile;

            /* deactivate border */
            if( !$border ) {
                $classes[]  = 'no-border';    
            }            
            
            /* unique listz ID */
            $id = uniqid("ut_scb_");
            
            $css_style  = '<style class="bklyn-inline-styles" type="text/css">';
                
                if( $border && !empty( $border_color ) ) {
                    $css_style .= '#' . $id . '.ut-project-sc { border-color: ' . $border_color . '; }';
                }
                
                if( !empty( $share_text_color ) ) {
                    $css_style .= '#' . $id . '.ut-project-sc .widget-title { color: ' . $share_text_color . '; }';
                }
                
                if( $share_text_font_weight ) {
                    $css_style .= '#' . $id . '.ut-project-sc .widget-title { font-weight: ' . $share_text_font_weight . '; }';  
                } 
                
                if( $share_text_letter_spacing ) {
					
					// fallback letter spacing
					if( $share_text_letter_spacing >= 1 || $share_text_letter_spacing <= -1 ) {
						$share_text_letter_spacing = $share_text_letter_spacing / 100;	
					}
					
                    $css_style .= '#' . $id . '.ut-project-sc .widget-title { letter-spacing: ' . $share_text_letter_spacing . 'em; }';  
					
                } 
            
                if( !empty( $icon_color ) ) {
                    $css_style .= '#' . $id . ' .ut-share-link { color: ' . $icon_color . '; }';
                    $css_style .= '#' . $id . ' .ut-share-link:visited { color: ' . $icon_color . '; }';
                }
                
                if( !empty( $icon_color_hover ) ) {
                    $css_style .= '#' . $id . ' .ut-share-link:hover { color: ' . $icon_color_hover . '; }';
                    $css_style .= '#' . $id . ' .ut-share-link:focus { color: ' . $icon_color_hover . '; }';
                    $css_style .= '#' . $id . ' .ut-share-link:active { color: ' . $icon_color_hover . '; }';
                }                
             
            $css_style .= '</style>';
                        
            /* start output */
            $output = '';
            
            /* attach CSS */
            $output .= ut_minify_inline_css( $css_style );            
                
            $output .= '<ul id="' . esc_attr( $id ) . '" class="ut-project-sc clearfix ' . implode(' ', $classes ) . '">';
                
                $output .= '<li><h6 class="widget-title"><span>' . $share_text . '</span></h6></li>';
                $output .= '<li><a class="ut-share-link sc-facebook" data-social="utsharefacebook"><i class="fa fa-facebook"></i></a></li>';
                $output .= '<li><a class="ut-share-link sc-twitter" data-social="utsharetwitter"><i class="fa fa-twitter"></i></a></li>';
                $output .= '<li><a class="ut-share-link sc-linkedin" data-social="utsharelinkedin"><i class="fa fa-linkedin"></i></a></li>';
                $output .= '<li><a class="ut-share-link sc-pinterest" data-social="utsharepinterest"><i class="fa fa-pinterest"></i></a></li>';
                $output .= '<li><a class="ut-share-link sc-xing" data-social="utsharexing"><i class="fa fa-xing"></i></a></li>';                
            
            $output .= '</ul>';
                
            return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>';             
        
        }
            
    }

}

new UT_Social_Share_Bar;