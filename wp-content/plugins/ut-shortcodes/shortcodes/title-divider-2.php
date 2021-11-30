<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Title_Divider_2' ) ) {
	
    class UT_Title_Divider_2 {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_title_divider_2';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            
            if( function_exists('vc_add_params') ) {
                vc_add_params( $this->shortcode, _vc_add_animation_settings() );
            }
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Title Divider Module', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'A collection of separator styles for horizontally dividing sections on a website.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-arrows-h ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/title-divider.png',
                    'category'        => 'Structual',
                    'class'           => 'ut-vc-icon-module ut-structual-module',
                    'params'          => array(
                        array(
                            'type'              => 'textfield',
                            'heading'           => __( 'Title', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'param_name'        => 'content',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Divider Style', 'ut_shortcodes' ),
                            'group'             => 'General',
                            'param_name'        => 'divider',
                            'value'             => array(
                                esc_html__( 'Style 1'  , 'ut_shortcodes' ) => 'bklyn-divider-style-1',
                                esc_html__( 'Style 2'  , 'ut_shortcodes' ) => 'bklyn-divider-style-2',
                                esc_html__( 'Style 3'  , 'ut_shortcodes' ) => 'bklyn-divider-style-3',
                                esc_html__( 'Style 4'  , 'ut_shortcodes' ) => 'bklyn-divider-style-4',
                                esc_html__( 'Style 5'  , 'ut_shortcodes' ) => 'bklyn-divider-style-5',
                                esc_html__( 'Style 6'  , 'ut_shortcodes' ) => 'bklyn-divider-style-6',
                                esc_html__( 'Style 7'  , 'ut_shortcodes' ) => 'bklyn-divider-style-7',
                                esc_html__( 'Style 8'  , 'ut_shortcodes' ) => 'bklyn-divider-style-8',
                                esc_html__( 'Style 9'  , 'ut_shortcodes' ) => 'bklyn-divider-style-9',
                                esc_html__( 'Style 10' , 'ut_shortcodes' ) => 'bklyn-divider-style-10',
                                esc_html__( 'Style 11' , 'ut_shortcodes' ) => 'bklyn-divider-style-11',
                                esc_html__( 'Style 12' , 'ut_shortcodes' ) => 'bklyn-divider-style-12',
                                esc_html__( 'Style 13' , 'ut_shortcodes' ) => 'bklyn-divider-style-13',
                                esc_html__( 'Style 14' , 'ut_shortcodes' ) => 'bklyn-divider-style-14',
                                esc_html__( 'Style 15' , 'ut_shortcodes' ) => 'bklyn-divider-style-15',
                                esc_html__( 'Style 16' , 'ut_shortcodes' ) => 'bklyn-divider-style-16',
                                esc_html__( 'Style 17' , 'ut_shortcodes' ) => 'bklyn-divider-style-17',
                                esc_html__( 'Style 18' , 'ut_shortcodes' ) => 'bklyn-divider-style-18',
                                esc_html__( 'Style 19' , 'ut_shortcodes' ) => 'bklyn-divider-style-19',
                                esc_html__( 'Style 20' , 'ut_shortcodes' ) => 'bklyn-divider-style-20',
                            ),
                        ),
	                    array(
		                    'type' => 'range_slider',
		                    'heading' => esc_html__( 'Divider Width', 'ut_shortcodes' ),
		                    'param_name' => 'divider_width',
		                    'value' => array(
			                    'default'   => '100',
			                    'min'       => '1',
			                    'max'       => '100',
			                    'step'      => '1',
			                    'unit'      => '%'
		                    ),
		                    'group' => 'General'
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Title Alignment', 'ut_shortcodes' ),
		                    'group'             => 'General',
		                    'param_name'        => 'align',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'value'             => array(
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'             => 'dropdown',
		                    'heading'          => esc_html__( 'Alignment Tablet', 'ut_shortcodes' ),
		                    'param_name'       => 'align_tablet',
		                    'group'            => 'General',
		                    'edit_field_class' => 'vc_col-sm-4',
		                    'value'            => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'left', 'ut_shortcodes' )                => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' )              => 'center',
			                    esc_html__( 'right', 'ut_shortcodes' )               => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'             => 'dropdown',
		                    'heading'          => esc_html__( 'Alignment Mobile', 'ut_shortcodes' ),
		                    'param_name'       => 'align_mobile',
		                    'group'            => 'General',
		                    'edit_field_class' => 'vc_col-sm-4',
		                    'value'            => array(
			                    esc_html__( 'left', 'ut_shortcodes' )                => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' )              => 'center',
			                    esc_html__( 'right', 'ut_shortcodes' )               => 'right',
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
		                    ),
	                    ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),
                        /* Colors */
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'color',
                            'group'             => 'Colors'
                        ),
                        /* Font Settings */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'font_source',
                            'group'             => 'Font',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => '',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google'
                            ),
                        ),
                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Font',
                            'settings'          => array(
                                'fields' => array(
                                    'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                                    'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                                ),
                            ),
                            'dependency'        => array(
                                'element'           => 'font_source',
                                'value'             => 'google',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Websafe Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'websafe_fonts',
                            'group'             => 'Font',
                            'value'             => array(
                                esc_html__( 'Arial', 'unite' )              => 'arial',
                                esc_html__( 'Comic Sans', 'unite' )         => 'comic',
                                esc_html__( 'Georgia', 'unite' )            => 'georgia',
                                esc_html__( 'Helvetica', 'unite' )          => 'helvetica',
                                esc_html__( 'Impact', 'unite' )             => 'impact',
                                esc_html__( 'Lucida Sans', 'unite' )        => 'lucida_sans',
                                esc_html__( 'Lucida Console', 'unite' )     => 'lucida_console',
                                esc_html__( 'Palatino', 'unite' )           => 'palatino',
                                esc_html__( 'Tahoma', 'unite' )             => 'tahoma',
                                esc_html__( 'Times New Roman', 'unite' )    => 'times',
                                esc_html__( 'Trebuchet', 'unite' )          => 'trebuchet',
                                esc_html__( 'Verdana', 'unite' )            => 'verdana'
                            ),
                            'dependency'        => array(
                                'element'           => 'font_source',
                                'value'             => 'websafe',
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'font_size',
                            'group'             => 'Font',
                            'value'             => array(
                                'default'   => ut_get_theme_options_font_setting( 'h3', 'font-size', "17" ),
                                'min'       => '0',
                                'max'       => '100',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'line_height',
                            'group'             => 'Font',
                            'value'             => array(
                                'default'   => '69',
                                'min'       => '69',
                                'max'       => '300',
                                'step'      => '1',
                                'unit'      => '%',
                                'global'	=> '69', // according to "min"
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'letter_spacing',
                            'group'             => 'Font',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'font_weight',
                            'group'             => 'Font',
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
                            'dependency'        => array(
                                'element'           => 'font_source',
                                'value'             => 'websafe',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'text_transform',
                            'group'             => 'Font',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),
                        /* title Spacing*/
                        array(
                            'type'              => 'ut_css_editor',
                            'heading'           => esc_html__( 'Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'spacing',
                            'group'             => 'Spacing',
                        ),

                    )

                )

            ); // end mapping
                

        
        }
            
    }

}

new UT_Title_Divider_2;


if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_title_divider_2 extends WPBakeryShortCode {
        
        /**
         * Used to get field name in vc_map function for google_fonts, font_container and etc..
         *
         * @param $key
         *
         * @since 4.4
         * @return bool
         */
        protected function getField( $key ) {
            return isset( $this->fields[ $key ] ) ? $this->fields[ $key ] : false;
        }
        
        /**
         * Get param value by providing key
         *
         * @param $key
         *
         * @since 4.4
         * @return array|bool
         */
        protected function getParamData( $key ) {
            return WPBMap::getParam( $this->shortcode, $this->getField( $key ) );
        }
        
        
        /**
         * Parses google_fonts_data to get needed css styles to markup
         *
         * @param $el_class
         * @param $css
         * @param $google_fonts_data
         * @param $font_container_data
         * @param $atts
         *
         * @since 4.3
         * @return array
         */
        public function getStyles( $google_fonts_data, $atts ) {
            
            $styles = array();
            $font_source = empty( $atts['font_source'] ) ? '' : $atts['font_source'];
                        
            if ( 'google' === $font_source && ! empty( $google_fonts_data ) && isset( $google_fonts_data['values'], $google_fonts_data['values']['font_family'], $google_fonts_data['values']['font_style'] ) ) {
                $google_fonts_family = explode( ':', $google_fonts_data['values']['font_family'] );
                $styles[] = 'font-family:' . $google_fonts_family[0];
                $google_fonts_styles = explode( ':', $google_fonts_data['values']['font_style'] );
                $styles[] = 'font-weight:' . $google_fonts_styles[1];
                $styles[] = 'font-style:' . $google_fonts_styles[2];
            }
            
            return array(
                'inline_styles' => $styles,
            );
            
        }
        
        /**
         * Parses shortcode attributes and set defaults based on vc_map function relative to shortcode and fields names
         *
         * @param $atts
         *
         * @since 4.3
         * @return array
         */
        public function getAttributes( $atts ) {
            
            /**
             * Shortcode attributes
             * @var $google_fonts
             * @var $font_container
             * @var $link
             * @var $css
             */
            $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
            extract( $atts );
    
            /**
             * Get default values from VC_MAP.
             */            
            $google_fonts_obj = new Vc_Google_Fonts();
                     
            $google_fonts_field  = $this->getParamData( 'google_fonts' );
    
            $google_fonts_field_settings = isset( $google_fonts_field['settings'], $google_fonts_field['settings']['fields'] ) ? $google_fonts_field['settings']['fields'] : array();
            $google_fonts_data = strlen( $google_fonts ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $google_fonts_field_settings, $google_fonts ) : '';
            
            return array(
                'google_fonts'      => $google_fonts,
                'google_fonts_data' => $google_fonts_data,
            );
            
        }
                
        protected function content( $atts, $content = null ) {
        
            extract( shortcode_atts( array (
                'color'              => '',
                'divider'            => 'bklyn-divider-style-1',
                'divider_width'      => '100',
                'align'              => 'left',
                'align_tablet'       => 'inherit',
                'align_mobile'       => 'left',
                'spacing'            => '',
                
                // Font Settings
                'font_size'          => '',
                'line_height'        => '',
                'font_weight'        => '',
                'letter_spacing'     => '0',
                'text_transform'     => '',
                'font_source'        => '',
                'google_fonts'       => '',
                'websafe_fonts'      => '',
                
                // Animation
                'effect'             => '',       
                'animate_once'       => 'no',
                'animate_tablet'     => 'no',
                'animate_mobile'     => 'no',
                'delay'              => 'no',
                'delay_timer'        => '100',
                'animation_duration' => '',
                'animation_between'  => '',
                
                'css'                => '',
                'class'              => ''
            ), $atts ) ); 
			
            if( ( $font_source && $font_source == 'google' ) ) {
                
                /* google font settings */
                extract( $this->getAttributes( $atts ) );
                extract( $this->getStyles( $google_fonts_data, $atts ) );
                
                /* subsets */            
                $settings = get_option( 'wpb_js_google_fonts_subsets' );
                if ( is_array( $settings ) && ! empty( $settings ) ) {
                    $subsets = '&subset=' . implode( ',', $settings );
                } else {
                    $subsets = '';
                }
                
                /* font */
                if ( $font_source && isset( $google_fonts_data['values']['font_family'] ) ) {
                    wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets . '&display=swap' );
                }
            
            }
            
            /* inline styles */
            if ( ! empty( $inline_styles ) ) {
                
                $inline_styles = 'style="' . esc_attr( implode( ';', $inline_styles ) ) . '"';
                
            } else {
                
                $inline_styles = '';
                
            }
            
            /* classes */
	        $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
	        $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

            $classes    = array( $class );
            $classes[]  = $divider;

	        $classes[]  = 'bklyn-title-divider-' . $align;
	        $classes[]  = 'bklyn-title-divider-tablet-' . $align_tablet;
	        $classes[]  = 'bklyn-title-divider-mobile-' . $align_mobile;
            
            /* animation effect */
            $attributes = array();
            
            if( !empty( $effect ) && $effect != 'none' ) {
                
                $attributes['data-effect']      = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );
                $attributes['data-delay']       = esc_attr( $delay_timer );
                
                if( $animate_once == 'infinite' && !empty( $animation_between ) ) {
                    
                    if( strpos($animation_between, 's') === true ) {
                        $animation_between = str_replace('s' , '', $animation_between);                        
                    }
                    
                    $attributes['data-animation-between'] = esc_attr( $animation_between );
                    
                }
                
                if( !empty( $animation_duration ) ) {

                    $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );    
                    
                }
                
                $classes[]  = 'ut-animate-element';
                $classes[]  = 'animated';
                
                if( $animate_tablet ) {
                    $classes[]  = 'ut-no-animation-tablet';
                }
                
                if( $animate_mobile ) {
                    $classes[]  = 'ut-no-animation-mobile';
                }
                
                if( $animate_once == 'infinite' && empty( $animation_between ) ) {
                    $classes[]  = 'infinite';
                }
                
            }            
            
            /* unique ID */
            $id = uniqid("ut_title_divider_");
            
            $css_style = '';

	        if( $divider_width !== '100'  )  {

		        $css_style .= '#' . $id . '.bklyn-title-divider span::before { width: ' . $divider_width . '%; }';
		        $css_style .= '#' . $id . '.bklyn-title-divider span::after { width: ' . $divider_width . '%; }';

	        }

            if( $color ) {
                $css_style .= '#' . $id . ' { color: ' . $color . '; }';
            }
            
            if( $font_size ) {
                $fontsize = str_replace('px', '', $font_size );                    
                $css_style .= '#' . $id . ' { font-size: ' . $font_size . 'px; }';
            }
            
            if( $font_source && $font_source == 'websafe' ) {
                $css_style .= '#' . $id . ' { font-family: ' . get_websafe_font_css_family( $websafe_fonts ) . '; }';
            }
            
            if( $line_height ) {
                $css_style .= '#' . $id . ' { line-height: ' . $line_height . '%; }';
            }
            
            if( $font_weight ) {
                $css_style .= '#' . $id . ' { font-weight: ' . $font_weight . '; }';
            }
            
            if( isset ($letter_spacing ) && $letter_spacing != '' ) {
				
				// fallback letter spacing
				if( $letter_spacing >= 1 || $letter_spacing <= -1 ) {
					$letter_spacing = $letter_spacing / 100;	
				}
				
                $css_style .= '#' . $id . ' { letter-spacing: ' . $letter_spacing . 'em; }';
				
            }
            
            if( $text_transform ) {
                $css_style .= '#' . $id . ' { text-transform: ' . $text_transform . '; }';
            }
            
            /* spacing css */
            if( !empty( $spacing ) ) {
                
                $padding = $margin = NULL;
                $spacing = explode(';', $spacing );
                
                foreach( $spacing as $space ) {
                    
                    if( strpos( $space, 'padding') !== false ) {
                        
                        $padding .= $space . ';';            
                        
                    } else {
                    
                        $margin .= $space . ';';
                    
                    }
                    
                }
                    
                /* margin for h3 */
                if( $margin ) {
                    
                    $css_style .= '#' . $id . ' { '. $margin .' }';
                    
                }
                
                /* padding for span */
                if( $padding ) {
                    
                    $css_style .= '#' . $id . ' span { '. $padding .' }';
                    
                }
                
            }                 
            
            /* attributes string */
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );
            
            /* start output */
            $output = '';
            
            /* attach css */
            if( !empty( $css_style ) ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );
            }
            
            $output .= '<h3 id="' . esc_attr( $id ) . '" class="bklyn-title-divider ' . esc_attr( implode(' ', $classes ) ) . '" ' . $inline_styles . ' ' . $attributes . '><span>' . do_shortcode( $content ) . '</span></h3>';
                        
            return $output;
        
        
        }        
        
    }

}


