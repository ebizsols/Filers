<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Hover_Box' ) ) {
	
    class UT_Hover_Box {
        
		private $css;
        private $shortcode;
		private $shortcode_front;
		private $shortcode_back;
            
        function __construct() {
			
            // shortcode base
            $this->shortcode 	   = 'ut_hover_box';
			$this->shortcode_front = 'ut_hover_box_front';
			$this->shortcode_back  = 'ut_hover_box_back';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
			
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );
			add_shortcode( $this->shortcode_front, array( $this, 'ut_create_shortcode_front' ) );
			add_shortcode( $this->shortcode_back, array( $this, 'ut_create_shortcode_back' ) );
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Flip Box', 'ut_shortcodes' ),
                    'description' 	  => esc_html__( 'Mouse Over Me! And reveal what is behind. A nice hover / flip box.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'category'        => 'Media',
                    'class'           => 'ut-vc-icon-module ut-media-module',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/hover-box.png',
                    'is_container' 	  => true,
                    'js_view' 		  => 'VcColumnView',
                    'as_parent'       => array( 'only' => 'ut_hover_box_front,ut_hover_box_back' ),
                    'weight'          => 10,
                    'params'          => array(

                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Hover Box Flip Direction', 'ut_shortcodes' ),
                            'param_name' => 'direction',
                            'group' => 'General',
                            'value' => array(
                                esc_html__( 'flip to top', 'ut_shortcodes' )    => 'top',
                                esc_html__( 'flip to right', 'ut_shortcodes' )  => 'right',
                                esc_html__( 'flip to bottom', 'ut_shortcodes' ) => 'bottom',
                                esc_html__( 'flip to left', 'ut_shortcodes' )   => 'left',
                            )
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Minimum Height of this Hover Box.', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional) value in px or em, eg "350px. Default value 350px."' , 'ut_shortcodes' ),
                            'param_name'        => 'min_height',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'hover',
                                'value'   => 'yes',
                            )
                        ),

                        // custom css
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                    ),
                    'default_content' => '
                        [ut_hover_box_front title="' . __( 'Front', 'js_composer' ) . '"][/ut_hover_box_front]
                        [ut_hover_box_back title="' . __( 'Back', 'js_composer' ) . '"][/ut_hover_box_back]
                    ',

                )

            );

            vc_map(
                array(
                    'name'            => esc_html__( 'Hover Box Front', 'ut_shortcodes' ),
                    'base'            => $this->shortcode_front,
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'as_child'        => array( 'only' => $this->shortcode ),
                    'is_container' 	  => true,
                    'js_view' 		  => 'VcColumnView',
                    'as_parent'       => array( 'only' => 'vc_icon,ut_btn,vc_column_text,ut_custom_heading,ut_header,ut_btn_group,ut_label,ut_animated_image,ut_custom_link' ),
                    'params'          => array(

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Default Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'text_color',
                            'group'             => 'General',
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Box Shadow.', 'ut_shortcodes' ),
                            'param_name'        => 'box_shadow',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!' , 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!' , 'ut_shortcodes' ) => 'on'
                            ),
                        ),

                        // custom css
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                    ),

                )

            );

            vc_map(
                array(
                    'name'            => esc_html__( 'Hover Box Back', 'ut_shortcodes' ),
                    'base'            => $this->shortcode_back,
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'as_child'        => array( 'only' => $this->shortcode ),
                    'is_container' 	  => true,
                    'js_view' 		  => 'VcColumnView',
                    'as_parent'       => array( 'only' => 'vc_icon,ut_btn,vc_column_text,ut_custom_heading,ut_header,ut_btn_group,ut_label,ut_animated_image,ut_custom_link' ),
                    'params'          => array(

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Default Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'text_color',
                            'group'             => 'General',
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Box Shadow.', 'ut_shortcodes' ),
                            'param_name'        => 'box_shadow',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!' , 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!' , 'ut_shortcodes' ) => 'on'
                            ),
                        ),

                        // custom css
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                    ),

                )

            );
        
        }

        /*
		 * Create Glitch Tag
		 */

        public function create_glitch_effect( $atts, $image, $id ) {

            /**
             * @var string $glitch_effect
             * @var string $permanent_glitch
             * @var string $accent_1
             * @var string $accent_2
             * @var string $accent_3
             * @var string $glitch_transparent
             * @var string $glitch_effect_transparent
             */

            extract( shortcode_atts( array (
                'glitch_effect'             => 'none',
                'permanent_glitch'          => 'on',
                'accent_1'                  => '',
                'accent_2'                  => '',
                'accent_3'                  => '',
                'glitch_transparent'        => '',
                'glitch_effect_transparent' => ''
            ), $atts ) );

            if( $glitch_effect == 'none' || !$image ) {
                return false;
            }

            $glitch_effect = new UT_Glitch_Effect( array(
                'image_id'                  => $id,
                'glitch_effect'             => $glitch_effect,
                'permanent_glitch'          => $permanent_glitch,
                'image_desktop'             => $image,
                'accent_1'                  => $accent_1,
                'accent_2'                  => $accent_2,
                'accent_3'                  => $accent_3,
                'glitch_transparent'        => $glitch_transparent,
                'glitch_effect_transparent' => $glitch_effect_transparent,
            ));

            return $glitch_effect->render();

        }

        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
				
				'direction'			=> 'top',
				'min_height'		=> '',
				'css'               => '',
				'class'				=> ''
				
            ), $atts ) ); 
            
			$this->css = '';
			
			// class array
			$classes   = array('ut-hover-box');
			$classes[] = 'ut-hover-box-flip-' . $direction;
			$classes[] = $class;
			
			// attribute array
			$attributes = array();
			
			// inline css
            $id = uniqid("ut_hb_");
			$outer_id = uniqid("ut_hb_");
			
			if( $min_height ) {
				
				$this->css .= '#' . esc_attr( $id ) . ' .ut-hover-box-back { min-height: ' . ut_add_px_value( $min_height ) . '; }';
				$this->css .= '#' . esc_attr( $id ) . ' .ut-hover-box-front { min-height: ' . ut_add_px_value( $min_height ) . '; }';
				
				if( $direction == 'left' || $direction == 'right' ) {
				
					$this->css .= '#' . esc_attr( $id ) . '.ut-hover-box { margin-top: calc(' . ut_add_px_value( $min_height ) . ' / 3); }';
					$this->css .= '#' . esc_attr( $id ) . '.ut-hover-box { margin-bottom: calc(' . ut_add_px_value( $min_height ) . ' / 3); }';
					
				}
				
			}
			
			if( !empty( $atts['css'] ) && ut_vc_css_to_array( $atts['css'] ) ) {
    
				$vc_css = ut_vc_css_to_array( $atts['css'] );
				
				if( isset( $vc_css["background-color"] ) ) {

					if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $vc_css["background-color"] ) ) {

						// add background image
						$this->css .= ut_create_gradient_css( $vc_css["background-color"], '#' . esc_attr( $outer_id ) ); 

						// remove vc background color
						$vc_css = ut_clean_up_vc_css_array( $vc_css, 'background-color' );

					}         

				}

				// background with gradient and background image
				if( isset( $vc_css["background"] ) ) {

					if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $vc_css["background"] ) ) {

						// add background image
						$this->css .= ut_create_gradient_css( $vc_css["background"], '#' . esc_attr( $outer_id ), false, 'background' ); 

						// remove vc background
						unset( $vc_css['background'] );

					}         

				}    
				
				// re-assemble custom css
				$this->css .= '#' . $outer_id . '{' . implode_with_key( $vc_css ) . '}';

			}
			
            // start output
            $output = '';
        	
			$output .= '<div id="' . $id . '" class="' . implode(' ', $classes ) . '">';
				
				$output .= do_shortcode( $content );			
			
			$output .= '</div>';				
            
			// attach all CSS
			if( $this->css ) {
				$output .= ut_minify_inline_css( '<style type="text/css">' . $this->css . '</style>' );
			}			
			
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div id="' . $outer_id . '" class="wpb_content_element">' . $output . '</div>'; 
            
            }
            
            return $output;
        
        }

        public function extract_url( $css ) {

            preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $css, $match );

            if( isset( $match[0][0] ) ) {

                return isset( $match[0][0] ) ? $match[0][0] : false;

            } else {

                return false;

            }

        }

        public function extract_url_id( $css ) {

            preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $css, $match );

            if( isset( $match[0][0] ) ) {

                parse_str( parse_url( $match[0][0], PHP_URL_QUERY ), $array );

                return isset( $array['id'] ) ? $array['id'] : false;

            } else {

                return false;

            }

        }

		function ut_create_shortcode_front( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
				
				'text_color'					=> '',
				'box_shadow'					=> '',
				'bklyn_overlay'					=> '',
				'bklyn_overlay_color'			=> '',
				'bklyn_overlay_pattern' 		=> '',
				'bklyn_overlay_pattern_style'	=> '',
				'css'               			=> '',
                'glitch_effect'                 => 'none',
				'class'							=> ''
				
            ), $atts ) ); 
            
			// class array
			$classes   = array('ut-hover-box-front');
			$classes[] = $class;

            $background_image = false;
            $background_image_id = false;
			
			// box shadow
			if( $box_shadow == 'on' ) {
				$classes[] = 'ut-hover-box-front-with-shadow';
			}

            // glitch class
            if( $glitch_effect != 'none' ) {
                $classes[] = 'ut-has-element-glitch';
            }

			$id = uniqid("ut_hb_front_");
			$inner_id = uniqid("ut_hb_front_inner_");
			
			// text color
			if( $text_color ) {
				$this->css .= '#' . esc_attr( $id ) . ' { color: ' . $text_color . '; }';
			}
			
			if( !empty( $atts['css'] ) && ut_vc_css_to_array( $atts['css'] ) ) {
    
				$vc_css = ut_vc_css_to_array( $atts['css'] );

                if( isset( $vc_css["background-image"] ) ) {

                    $background_image    = $this->extract_url( $vc_css["background-image"] );
                    $background_image_id = $this->extract_url_id( $vc_css["background-image"] );

                }

				if( isset( $vc_css["background-color"] ) ) {

					if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $vc_css["background-color"] ) ) {

						// add background image
						$this->css .= ut_create_gradient_css( $vc_css["background-color"], '#' . esc_attr( $id ) ); 

						// remove vc background color
						$vc_css = ut_clean_up_vc_css_array( $vc_css, 'background-color' );

					}         

				}

				// background with gradient and background image
				if( isset( $vc_css["background"] ) ) {

					if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $vc_css["background"] ) ) {

						// add background image
						$this->css .= ut_create_gradient_css( $vc_css["background"], '#' . esc_attr( $id ), false, 'background' ); 

						// remove vc background
						unset( $vc_css['background'] );

					}         

				}    
				
				if( !empty( $vc_css['padding'] ) ) {
					
					$this->css .= '#' . $inner_id . '{ padding:' . $vc_css['padding'] . ';}';
					unset( $vc_css['padding'] );
					
				}
				
				// re-assemble custom css
				$this->css .= '#' . $id . '{' . implode_with_key( $vc_css ) . '}';

			}
			
			/**
			 * Overlay Settings
			 */

			$overlay_style_id = uniqid("ut_hover_box_overlay_");
			$gradient_overlay_background = false;
			
			if( $bklyn_overlay && $bklyn_overlay_color ) {
    
				if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $bklyn_overlay_color ) ) {

					$this->css .= ut_create_gradient_css( $bklyn_overlay_color, '#' . $overlay_style_id, ( $bklyn_overlay_pattern ? $bklyn_overlay_pattern_style : false ) );   
					$gradient_overlay_background = true;

				} else {

					$this->css .= '#' . $overlay_style_id . '{ background-color: ' . $bklyn_overlay_color . ';}';

				}

			}
			
			// start output
            $output = '';
			
			$output .= '<div id="' . esc_attr( $id ) . '" class="' . implode(' ', $classes ) . '">';

                $output .= $this->create_glitch_effect( $atts, $background_image, $background_image_id );

				$output .= '<div id="' . esc_attr( $inner_id ) . '" class="ut-hover-box-inner">';	

					$output .= do_shortcode( $content );

				$output .= '</div>';
			
				if( $bklyn_overlay ) {
    
					$output .= '<div id="' . $overlay_style_id . '" class="bklyn-overlay ' . ( $bklyn_overlay_pattern ? $bklyn_overlay_pattern_style : '' ) . '">';


					$output .= '</div>';

				}

			$output .= '</div>';
			
			return $output;				
		
		}
		
		
		
		function ut_create_shortcode_back( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
				
				'text_color'					=> '',
				'box_shadow'					=> '',
				'bklyn_overlay'					=> '',
				'bklyn_overlay_color'			=> '',
				'bklyn_overlay_pattern' 		=> '',
				'bklyn_overlay_pattern_style'	=> '',
				'css'               			=> '',
                'glitch_effect'                 => 'none',
				'class'							=> ''
				
            ), $atts ) ); 
            
			// class array
			$classes   = array('ut-hover-box-back');
			$classes[] = $class;

            $background_image    = false;
            $background_image_id = false;

			// box shadow
			if( $box_shadow == 'on' ) {
				$classes[] = 'ut-hover-box-back-with-shadow';
			}

            // glitch class
            if( $glitch_effect != 'none' ) {
                $classes[] = 'ut-has-element-glitch';
            }

			$id = uniqid("ut_hb_back_");
			$inner_id = uniqid("ut_hb_back_inner_");
			
			// text color
			if( $text_color ) {
				$this->css .= '#' . esc_attr( $id ) . ' { color: ' . $text_color . '; }';
			}
			
			if( !empty( $atts['css'] ) && ut_vc_css_to_array( $atts['css'] ) ) {
    
				$vc_css = ut_vc_css_to_array( $atts['css'] );

                if( isset( $vc_css["background-image"] ) ) {

                    $background_image    = $this->extract_url( $vc_css["background-image"] );
                    $background_image_id = $this->extract_url_id( $vc_css["background-image"] );

                }

				if( isset( $vc_css["background-color"] ) ) {

					if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $vc_css["background-color"] ) ) {

						// add background image
						$this->css .= ut_create_gradient_css( $vc_css["background-color"], '#' . esc_attr( $id ) ); 

						// remove vc background color
						$vc_css = ut_clean_up_vc_css_array( $vc_css, 'background-color' );

					}         

				}

				// background with gradient and background image
				if( isset( $vc_css["background"] ) ) {

					if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $vc_css["background"] ) ) {

						// add background image
						$this->css .= ut_create_gradient_css( $vc_css["background"], '#' . esc_attr( $id ), false, 'background' ); 

						// remove vc background
						unset( $vc_css['background'] );

					}         

				}    
				
				if( !empty( $vc_css['padding'] ) ) {
					
					$this->css .= '#' . $inner_id . '{ padding:' . $vc_css['padding'] . ';}';
					unset( $vc_css['padding'] );
					
				}
				
				// re-assemble custom css
				$this->css .= '#' . $id . '{' . implode_with_key( $vc_css ) . '}';

			}
			
			/**
			 * Overlay Settings
			 */

			$overlay_style_id = uniqid("ut_hover_box_overlay_");
			$gradient_overlay_background = false;
			
			if( $bklyn_overlay && $bklyn_overlay_color ) {
    
				if( function_exists("ut_create_gradient_css") && ut_create_gradient_css( $bklyn_overlay_color ) ) {

					$this->css .= ut_create_gradient_css( $bklyn_overlay_color, '#' . $overlay_style_id, ( $bklyn_overlay_pattern ? $bklyn_overlay_pattern_style : false ) );   
					$gradient_overlay_background = true;

				} else {

					$this->css .= '#' . $overlay_style_id . '{ background-color: ' . $bklyn_overlay_color . ';}';

				}

			}
			
			// start output
            $output = '';
			
			$output .= '<div id="' . esc_attr( $id ) . '" class="' . implode(' ', $classes ) . '">';

                $output .= $this->create_glitch_effect( $atts, $background_image, $background_image_id );

				$output .= '<div id="' . esc_attr( $inner_id ) . '" class="ut-hover-box-inner">';	

					$output .= do_shortcode( $content );

				$output .= '</div>';			
				
				if( $bklyn_overlay ) {
    
					$output .= '<div id="' . $overlay_style_id . '" class="bklyn-overlay ' . ( $bklyn_overlay_pattern ? $bklyn_overlay_pattern_style : '' ) . '">';


					$output .= '</div>';

				}			
			
			$output .= '</div>';
			
			return $output;			
		
		}
            
    }

}

new UT_Hover_Box;


if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    
    class WPBakeryShortCode_ut_hover_box extends WPBakeryShortCodesContainer {
        
        protected function outputTitle( $title ) {

			return '<h4 class="wpb_element_title"> ' . $title . '</h4>';
		}     
            
    }
    
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    
    class WPBakeryShortCode_ut_hover_box_front extends WPBakeryShortCodesContainer {
        
		protected function outputTitle( $title ) {

			return '<h4 class="wpb_element_title"> ' . $title . '</h4>';
		}    
            
    }
    
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    
    class WPBakeryShortCode_ut_hover_box_back extends WPBakeryShortCodesContainer {
        
         protected function outputTitle( $title ) {

			return '<h4 class="wpb_element_title"> ' . $title . '</h4>';
		}      
            
    }
    
}