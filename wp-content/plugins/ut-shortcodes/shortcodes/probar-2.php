<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Probar_Thin' ) ) {
    
    class UT_Probar_Thin {
        
        private $shortcode;
            
        function __construct() {
            
            /* shortcode base */
            $this->shortcode = 'ut_probar_thin';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );    
            
        }
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Progress Bar Thin', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Visualize the progression of an operation or skill level.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-tasks ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/probar-thin.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Bar Description', 'ut_shortcodes' ),
                            'param_name'        => 'info',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Bar Description Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'info_align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'left' , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right', 'ut_shortcodes' ) => 'right',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Bar Description Position', 'ut_shortcodes' ),
                            'param_name'        => 'info_position',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'above' , 'ut_shortcodes' ) => 'above',
                                esc_html__( 'below', 'ut_shortcodes' ) => 'below',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Bar Description Spacing', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'The spacing between Bar Description and the bar itself.', 'ut_shortcodes' ),
                            'param_name'        => 'info_spacing',
                            'value'             => array(
                                'default'   => '10',
                                'min'       => '6',
                                'max'       => '40',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Percentage', 'ut_shortcodes' ),
                            'param_name'        => 'width',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '100',
                                'step'  => '1',
                                'unit'  => '%'
                            ),
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Fill Up Speed', 'ut_shortcodes' ),
                            'param_name'        => 'speed',
                            'group'             => 'General',
                            'value'             => array(
                                'default'   => '1000',
                                'min'       => '100',
                                'max'       => '3000',
                                'step'      => '50',
                                'unit'      => 'ms'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Bar Once?', 'ut_shortcodes' ),
                            'param_name'        => 'animate_once',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no' , 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            ),
                          ),

                        /* Bar Colors */
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Bar Description Color', 'ut_shortcodes' ),
                            'param_name'        => 'info_color',
                            'group'             => 'Bar Styling'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Bar Color', 'ut_shortcodes' ),
                            'param_name'        => 'color',
                            'group'             => 'Bar Styling'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Bar Percentage Color', 'ut_shortcodes' ),
                            'param_name'        => 'percentage_color',
                            'group'             => 'Bar Styling'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Bar Percentage Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'percentage_bg_color',
                            'group'             => 'Bar Styling'
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Bar Percentage Background Color Opacity', 'ut_shortcodes' ),
                            'param_name'        => 'percentage_bg_color_opacity',
                            'value'             => array(
                                'default' => '1',
                                'min'     => '0',
                                'max'     => '1',
                                'step'    => '0.1',
                                'unit'    => ''
                            ),
                            'group'             => 'Bar Styling'
                        ),

                        /* Font Settings */
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Description Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'info_font_size',
                            'value'             => array(
                                'min'   => '12',
                                'min'   => '0',
                                'max'   => '100',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'group'             => 'Font'
                          ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Description Font Weight', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'You can additionally style H3 tags inside the theme options panel.', 'ut_shortcodes' ),
                            'param_name'        => 'info_font_weight',
                            'group'             => 'Font',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ),
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
                            'heading'           => esc_html__( 'Description Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'info_letter_spacing',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),
                            'group'             => 'Font'
                          ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Description Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'info_text_transform',
                            'group'             => 'Font',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                          ),

                        /* css */
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
                          )


                    )

                )

            ); /* end mapping */
                

        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'color'                       => '',
                'info'                        => '',
                'info_align'                  => '',
                'info_position'               => 'above',
                'info_spacing'                => '',
                'info_color'                  => '',
                'info_font_size'              => '',
                'info_font_weight'            => '',
                'info_letter_spacing'         => '',
                'info_text_transform'         => '',
                'width'                       => '50',
                'speed'                       => '1000',
                'percentage_color'            => '',
                'percentage_bg_color'         => '',
                'percentage_bg_color_opacity' => '1',
                'animate_once'                => 'no',
                'css'                         => '',
                'class'                       => '',  
            ), $atts ) ); 
            
            // classes
            $classes   = array();
            $classes[] = $class;            
            $classes[] = 'ut-progress-bar-module-2';
            $classes[] = 'ut-progress-bar-percentage-digits-' . strlen((string)$width);
            
            if( $info_position == 'below' ) {
                $classes[] = 'ut-skill-name-below';    
            }
            
            // strip % if user has entered it
            $width = str_replace('%' , '' , $width); /* deprecated */
            
            /* inline css */
            $id = uniqid("ut_pb_");
            
            $css_style = '<style type="text/css">';
                
                if( $info_color ) {
                    $css_style .= '#' . $id . ' .ut-skill-name { color: ' . $info_color . '; }';  
                } 
            
                if( $info_align ) {
                    $css_style .= '#' . $id . ' .ut-skill-name { text-align: ' . $info_align . '; }';    
                }
            
                if( ( $info_spacing || $info_spacing == 0 ) && $info_position == 'above' ){
                    $css_style .= '#' . $id . ' .ut-skill-name { margin-bottom: ' . $info_spacing . 'px; }';
                }
            
                if( ( $info_spacing || $info_spacing == 0 ) && $info_position == 'below' ){
                    $css_style .= '#' . $id . ' .ut-skill-name { margin-top: ' . $info_spacing . 'px; }';
                }
            
                if( $info_font_size ) {
                    $css_style .= '#' . $id . ' .ut-skill-name { font-size: ' . $info_font_size . 'px; }';  
                } 
                
                if( $info_font_weight ) {
                    $css_style .= '#' . $id . ' .ut-skill-name { font-weight: ' . $info_font_weight . '; }';  
                } 
                
                if( $info_letter_spacing ) {
					
					// fallback letter spacing
					if( $info_letter_spacing >= 1 || $info_letter_spacing <= -1 ) {
						$info_letter_spacing = $info_letter_spacing / 100;	
					}
					
                    $css_style .= '#' . $id . ' .ut-skill-name { letter-spacing: ' . $info_letter_spacing . 'em; }';
					
                } 
                
                if( $info_text_transform ) {
                    $css_style .= '#' . $id . ' .ut-skill-name { text-transform: ' . $info_text_transform . '; }';  
                } 
                
                if( $color && function_exists("ut_create_gradient_css") && ut_create_gradient_css( $color ) ) {
            
                    // add background image
                    $css_style .= ut_create_gradient_css( $color, '#' . $id . ' .ut-skill-progress-thin' ); 

                } elseif( $color )  {
                    
                    $css_style .= '#' . $id . ' .ut-skill-progress-thin { background: ' . $color . '; }';       
                    
                }
            
                if( $percentage_color ) {
                    $css_style .= '#' . $id . ' .ut-skill-tooltip { color: ' . $percentage_color . '; }';  
                }
            
                if( $percentage_bg_color )  {
                    
                    $css_style .= '#' . $id . ' .ut-skill-tooltip { background: rgba(' . ut_hex_to_rgb( $percentage_bg_color ) . ', ' . $percentage_bg_color_opacity . '); }';
                    $css_style .= '#' . $id . ' .ut-skill-tooltip::after { border-color: rgba(' . ut_hex_to_rgb( $percentage_bg_color ) . ', ' . $percentage_bg_color_opacity . ') transparent; }';       
                    
                }
                
            $css_style .= '</style>'; 
            
            /* start output */
            $output = '';
            
            /* add css */ 
            $output .= ut_minify_inline_css( $css_style );            
            
            $output .= '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
            
                if( !empty( $info ) && $info_position == 'above' ) {
                    $output .= '<h3 class="ut-skill-name">' . $info . '</h3>';
                }
                
                $output .= '<div class="ut-skill-bar-thin">';
                    
                    $output .= '<div class="ut-skill-progress-thin ut-skill-active" data-speed="' . esc_attr( $speed ) . '" data-animateonce="' . esc_attr( $animate_once ) . '" data-effecttype="skillbar" data-width="' . esc_attr( $width ) . '">';
                        
                        $output .= '<span class="ut-skill-tooltip">' . $width . '%</span>';
            
                    $output .= '</div>';
            
                $output .= '</div>';
            
                if( !empty( $info ) && $info_position == 'below' ) {
                    $output .= '<h3 class="ut-skill-name">' . $info . '</h3>';
                }
            
            $output .= '</div>';
                
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
            
            return $output;
        
        }
            
    }

}

new UT_Probar_Thin;