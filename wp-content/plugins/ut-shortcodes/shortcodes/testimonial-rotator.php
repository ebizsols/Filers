<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Testimonial_Rotator' ) ) {
	
    class UT_Testimonial_Rotator {
		
		private $shortcode;

		function __construct() {
			
			$this->shortcode = 'ut_testimonial_rotator';
			
			add_action( 'init', array( $this, 'ut_map_shortcode' ) );
			add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );
			
		}		
				
		function ut_map_shortcode() {
						
			vc_map(
				array(
					'name'                  => esc_html__( 'Testimonial Rotator', 'ut_shortcodes' ),
					'description'     		=> esc_html__( 'Please this element inside a fullwidth row.', 'ut_shortcodes' ),
					'base'                  => $this->shortcode,
					'icon'                  => UT_SHORTCODES_URL . '/admin/img/vc_icons/quote-rotator.png',
					'category'              => 'Community',
					'class'                 => 'ut-vc-icon-module ut-community-module', 
					'as_parent'             => array( 'only' => 'ut_single_quote' ),
					'is_container'          => true,
					'params'                => array(

						array(
							'type'          => 'dropdown',
							'heading'       => esc_html__( 'Autoplay Slider?', 'ut_shortcodes' ),
							'param_name'    => 'autoplay',
							'group'         => 'Slider Settings',
							'value'         => array(
								esc_html__( 'no', 'ut_shortcodes' ) => 'false',
								esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
							),
						),
						array(
							'type'          => 'textfield',
							'heading'       => esc_html__( 'Autoplay Timeout', 'ut_shortcodes' ),
							'description'   => esc_html__( 'Autoplay interval timeout in milliseconds. Default: 5000' , 'ut_shortcodes' ),
							'param_name'    => 'autoplay_timeout',
							'group'         => 'Slider Settings',
							'dependency'    => array(
								'element' => 'autoplay',
								'value'   => array( 'true' ),
							)

						),
						array(
							'type'          => 'dropdown',
							'heading'       => esc_html__( 'Loop Slider?', 'ut_shortcodes' ),
							'param_name'    => 'loop',
							'group'         => 'Slider Settings',
							'value'         => array(
								esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
								esc_html__( 'no', 'ut_shortcodes' ) => 'false'
							),
							'dependency'    => array(
								'element' => 'type',
								'value'   => array( 'single' ),
							) 
						),
						array(
							'type'          => 'dropdown',
							'heading'       => esc_html__( 'Show Next / Prev Navigation?', 'ut_shortcodes' ),
							'param_name'    => 'nav',
							'group'         => 'Slider Settings',
							'value'         => array(
								esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
								esc_html__( 'no', 'ut_shortcodes' ) => 'false'
							),
						),
						array(
							'type'              => 'dropdown',
							'heading'           => esc_html__( 'Animation Effect', 'ut_shortcodes' ),
							'param_name'        => 'effect',
							'group'             => 'Slider Settings',
							'value'         => array(
								esc_html__( 'Slide'  , 'ut_shortcodes' ) => 'slide',
								esc_html__( 'Fade', 'ut_shortcodes' ) => 'fade'
							),                                
						),
						array(
							'type'              => 'textfield',
							'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
							'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
							'param_name'        => 'class',
							'group'             => 'Slider Settings'
						),                            

						// css editor
						array(
							'type'              => 'css_editor',
							'param_name'        => 'css',
							'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
						)                            

					),
					'js_view'         => 'VcColumnView'                        

				)

			);
			
		}
		
		
		function ut_create_shortcode( $atts, $content = NULL ) {

            extract( shortcode_atts( array (
                'nav'               => 'true',
                'effect'            => '',       
                'animate_once'      => 'no',
                'animate_mobile'    => false,
                'animate_tablet'    => false,
                'delay'             => 'false',
                'delay_timer'       => '100',
                'animation_duration'=> '',   
                'css'               => '',                
                'class'             => ''
            ), $atts ) ); 
            
            /* class array */
            $classes    = array();
            $attributes = array();
            
            /* extra element class */
            $classes[] = $class;
            
            /* animation effect */
            $dataeffect = NULL;
            
            if( !empty( $effect ) ) {
                
                $attributes['data-effect']      = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );
                $attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;
                
                if( !empty( $animation_duration ) ) {
                    $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );    
                }
                
                $classes[]  = 'ut-animate-element';
                $classes[]  = 'animated';
                
                if( !$animate_tablet ) {
                    $classes[]  = 'ut-no-animation-tablet';
                }
                
                if( !$animate_mobile ) {
                    $classes[]  = 'ut-no-animation-mobile';
                }
                
                if( $animate_once == 'infinite' ) {
                    $classes[]  = 'infinite';
                }
                
            }     
            
            /* attributes string */
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) ); 
            
            /* set unique ID for this rotator */
            $id = uniqid("qtSlider_");
            $outer_id = uniqid("qtSliderOuter");
            
            /* start output */
            $output = '';            

            /* attach css */
            // $output .= ut_minify_inline_css( $this->ut_create_inline_css( $outer_id, $atts ) );
            
            $output .= '<div ' . $attributes . ' class="ut-bkly-testimonial-rotator ' . implode( ' ', $classes ) . '">';
            
                $output .= '<div id="' . esc_attr( $id ) . '" class="owl-carousel owl-theme" data-settings="' . $this->slider_settings_json( $atts, substr_count( $content, '[ut_qt') ) . '">';
                    
                    $output .= do_shortcode( $content );
                         
                $output .= '</div>';
                
                if( $nav == 'true' ) {
                        
                    // $output .= '<a href="#" data-for="' . esc_attr( $id ) . '" class="ut-prev-gallery-slide"><i class="Bklyn-Core-Left-2"></i></a>';
                    // $output .= '<a href="#" data-for="' . esc_attr( $id ) . '" class="ut-next-gallery-slide"><i class="Bklyn-Core-Right-2"></i></a>';                 
                
                }
            
            $output .= '</div>';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div id="'. esc_attr( $outer_id ).'" class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
                
            return $output;
        
		}

	    function slider_settings_json( $atts, $slides ) {

		    $loop = $slides > 1 ? 'true' : 'false';

		    /**
		     * @var $slide_effect
		     * @var $quote_width
		     * @var $dot_nav
		     * @var $autoplay
		     * @var $autoplay_timeout
		     * @var $loop
		     */
		    extract( shortcode_atts( array (
			    'effect'           => 'slide',
			    'autoplay'         => 'false',
			    'autoplay_timeout' => 5000,
			    'loop'             => $loop,
		    ), $atts ) );

		    $json = array(

			    'items'              => 3,
			    'smartSpeed'         => 600,
			    'lazyLoad'           => true,
			    'center'             => true,
			    'autoplay'           => filter_var( $autoplay, FILTER_VALIDATE_BOOLEAN ),
			    'autoplayTimeout'    => $autoplay_timeout,
			    'autoplayHoverPause' => true,
			    'loop'               => filter_var( $loop, FILTER_VALIDATE_BOOLEAN ),
			    'nav'                => false,
			    'dots'               => true,
                'responsiveClass'    => true,
                'responsive'         => array(
                    0 => array(
                       'items' => 1
                    ),
                    768 => array(
	                    'items' => 1
                    ),
                    1025 => array(
	                    'items' => 3
                    ),
                )

		    );

		    if( $quote_width == 'fullwidth' ) {
			    $json['margin'] = 80;
		    }

		    if( $slide_effect == 'fade' ) {
			    $json['animateIn']  = "fadeIn";
			    $json['animateOut'] = "fadeOut";
		    }

		    return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

	    }
		
	}
	
}

new UT_Testimonial_Rotator();


if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    
    class WPBakeryShortCode_ut_testimonial_rotator extends WPBakeryShortCodesContainer {
    
    }
    
}