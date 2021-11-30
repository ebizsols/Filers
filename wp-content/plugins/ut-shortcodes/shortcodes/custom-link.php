<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Custom_Link' ) ) {
	
    class UT_Custom_Link {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_custom_link';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode( $atts, $content = NULL ) {
            
            if( function_exists( 'vc_map' ) ) {
                                
                vc_map(
                    array(
                        'name'            => esc_html__( 'Custom Link', 'ut_shortcodes' ),
						'description'     => esc_html__( 'A single standalone link.', 'ut_shortcodes' ),
                        'base'            => $this->shortcode,
                        // 'icon'            => 'fa fa-link ut-vc-module-icon',
                        'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/fancy-link.png',
                        'category'        => 'Information',
                        'class'           => 'ut-vc-icon-module ut-information-module',
                        'content_element' => true,
                        'params'          => array(
                            
                            array(
                                'type'              => 'vc_link',
                                'heading'           => esc_html__( 'Link', 'ut_shortcodes' ),
                                'admin_label'       => true,
                                'param_name'        => 'link',
                                'group'             => 'General'
                            ),
                            array(
								'type'              => 'dropdown',
								'heading'           => esc_html__( 'Alignment', 'ut_shortcodes' ),
								'param_name'        => 'align',
								'group'             => 'General',
                                'edit_field_class' => 'vc_col-sm-4',
                                'value'             => array(
                                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
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
                                    esc_html__( 'center (default)', 'ut_shortcodes' )    => 'center',
                                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
                                    esc_html__( 'left', 'ut_shortcodes' )                => 'left',
                                    esc_html__( 'right', 'ut_shortcodes' )               => 'right',
                                ),
                            ),

                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Add Icon to Link?', 'unitedthemes' ),
                                'param_name'        => 'icon',
                                'group'             => 'General',
                                'value'             => array(
                                    esc_html__( 'no', 'ut_shortcodes' ) => '',
                                    esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'                                    
                                )                                
                            ),  
                            array(
                                'type'          => 'dropdown',
                                'heading'       => esc_html__( 'Icon library', 'ut_shortcodes' ),
                                'description'   => esc_html__( 'Select icon library.', 'ut_shortcodes' ),
                                'param_name'    => 'icon_type', 
                                'group'         => 'General', 
                                'value'         => array(
                                    esc_html__( 'Brooklyn Icons', 'ut_shortcodes' ) => 'bklynicons',
                                    esc_html__( 'Font Awesome', 'ut_shortcodes' ) => 'fontawesome',
                                ),
                                'dependency' => array(
                                    'element'   => 'icon',
                                    'value'     => 'yes',
                                ),
                                                              
                            ),
                            array(
								'type'              => 'iconpicker',
                                'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                                'param_name'        => 'icon_fontawesome',
                                'group'             => 'General',                                
                                'dependency' => array(
                                    'element'   => 'icon_type',
                                    'value'     => 'fontawesome',
                                ),
                            ),
                            array(
								'type'              => 'iconpicker',
                                'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                                'param_name'        => 'icon_bklyn',
                                'group'             => 'General',
                                'settings' => array(
                                    'emptyIcon'     => true,
                                    'type'          => 'bklynicons',
                                ),
                                'dependency' => array(
                                    'element'   => 'icon_type',
                                    'value'     => 'bklynicons',
                                ),
                                                                
                            ),
	                        array(
		                        'type'              => 'range_slider',
		                        'heading'           => esc_html__( 'Font Awesome Vertical Alignment Correction (top)', 'ut_shortcodes' ),
		                        'description'		=> esc_html__( 'Font Awesome are not always vertically aligned correctly. In case there is a misalignment, use this option to correct it.', 'unitedthemes' ),
		                        'param_name'        => 'fontawesome_v_correction_t',
		                        'edit_field_class'  => 'vc_col-sm-6',
		                        'group'             => 'General',
		                        'value'             => array(
			                        'default' => '0',
			                        'min'     => '0',
			                        'max'     => '10',
			                        'step'    => '1',
			                        'unit'    => 'px'
		                        ),
		                        'dependency' => array(
			                        'element'   => 'icon_type',
			                        'value'     => 'fontawesome',
		                        ),
	                        ),
	                        array(
		                        'type'              => 'range_slider',
		                        'heading'           => esc_html__( 'Font Awesome Vertical Alignment Correction (bottom)', 'ut_shortcodes' ),
		                        'description'		=> esc_html__( 'Font Awesome are not always vertically aligned correctly. In case there is a misalignment, use this option to correct it.', 'unitedthemes' ),
		                        'param_name'        => 'fontawesome_v_correction_b',
		                        'edit_field_class'  => 'vc_col-sm-6',
		                        'group'             => 'General',
		                        'value'             => array(
			                        'default' => '0',
			                        'min'     => '0',
			                        'max'     => '10',
			                        'step'    => '1',
			                        'unit'    => 'px'
		                        ),
		                        'dependency' => array(
			                        'element'   => 'icon_type',
			                        'value'     => 'fontawesome',
		                        ),
	                        ),
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Icon Position', 'unitedthemes' ),
                                'param_name'        => 'icon_position',
                                'group'             => 'General',
                                'value'             => array(
                                    esc_html__( 'before', 'ut_shortcodes' ) => 'before',
                                    esc_html__( 'after', 'ut_shortcodes' ) => 'after'                                    
                                ),
                                'dependency'        => array(
                                    'element' => 'icon',
                                    'value'   => 'yes',
                                ),
                            ),
                            array(
                                'type'              => 'range_slider',
                                'heading'           => esc_html__( 'Icon Size', 'ut_shortcodes' ),
                                'param_name'        => 'icon_size',
                                'group'             => 'General',
                                'value'             => array(
                                    'default' => ut_get_theme_options_font_setting( 'body_font', 'font-size', "12" ),
                                    'min'     => '8',
                                    'max'     => '100',
                                    'step'    => '1',
                                    'unit'    => 'px'
                                ),
                                'dependency'        => array(
                                    'element' => 'icon',
                                    'value'   => 'yes',
                                ),
                            ),
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Add Hover Animation to Icon?', 'unitedthemes' ),
                                'param_name'        => 'icon_animation',
                                'group'             => 'General',
                                'value'             => array(
                                    esc_html__( 'no', 'ut_shortcodes' ) => '',
                                    esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'                                    
                                ),
                                'dependency'        => array(
                                    'element' => 'icon',
                                    'value'   => 'yes',
                                ),
                            ),
                            array(
								'type'              => 'range_slider',
								'heading'           => esc_html__( 'Font Size', 'ut_shortcodes' ),
								'param_name'        => 'font_size',
                                'group'             => 'Link Font Settings',
                                'value'             => array(
                                    'default' => ut_get_theme_options_font_setting( 'body_font', 'font-size', "12" ),
                                    'min'     => '8',
                                    'max'     => '50',
                                    'step'    => '1',
                                    'unit'    => 'px'
                                ),								
						  	),
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Font Style', 'ut_shortcodes' ),
                                'param_name'        => 'font_style',
                                'group'             => 'Link Font Settings',
                                'value'             => array(
                                    esc_html__( 'Select Font Style' , 'ut_shortcodes' ) => '',
                                    esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                    esc_html__( 'italic' , 'ut_shortcodes' ) => 'italic',
                                ),
                            ),
                            array(
								'type'              => 'dropdown',
								'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
								'param_name'        => 'font_weight',
								'group'             => 'Link Font Settings',
                                'value'             => array(
                                    esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                    esc_html__( 'normal' , 'ut_shortcodes' )             => 'normal',
                                    esc_html__( 'bold' , 'ut_shortcodes' )               => 'bold',
                                    esc_html__( '100' , 'ut_shortcodes' )                => '100',
                                    esc_html__( '200' , 'ut_shortcodes' )                => '200',
                                    esc_html__( '300' , 'ut_shortcodes' )                => '300',
                                    esc_html__( '400' , 'ut_shortcodes' )                => '400',
                                    esc_html__( '500' , 'ut_shortcodes' )                => '500',
                                    esc_html__( '600' , 'ut_shortcodes' )                => '600',
                                    esc_html__( '700' , 'ut_shortcodes' )                => '700',
                                    esc_html__( '800' , 'ut_shortcodes' )                => '800',
                                    esc_html__( '900' , 'ut_shortcodes' )                => '900',
                                ),                              
						  	),
                            array(
								'type'              => 'dropdown',
								'heading'           => esc_html__( 'Text Transform', 'ut_shortcodes' ),
								'param_name'        => 'text_transform',
								'group'             => 'Link Font Settings',
                                'value'             => array(
                                    esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                    esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                    esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                    esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'                                    
                                ),
						  	),
                            array(
								'type'              => 'range_slider',
								'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
								'param_name'        => 'letter_spacing',
                                'group'             => 'Link Font Settings',
                                'value'             => array(
                                    'default'   => '0',
                                    'min'       => '-0.2',
                                    'max'       => '0.2',
                                    'step'      => '0.01',
                                    'unit'      => 'em'
                                ),								
						  	),
	                        array(
		                        'type'              => 'range_slider',
		                        'heading'           => esc_html__( 'Line Height', 'ut-core' ),
		                        'param_name'        => 'line_height',
		                        'group'             => 'Link Font Settings',
		                        'value'             => array(
			                        'default'   => '79',
			                        'min'       => '79',
			                        'max'       => '300',
			                        'step'      => '1',
			                        'unit'      => '%',
			                        'global'	=> '79'
		                        ),

	                        ),
                            array(
								'type'              => 'colorpicker',
								'heading'           => esc_html__( 'Link Color', 'ut_shortcodes' ),
								'param_name'        => 'link_color',
								'group'             => 'Link Colors'                                
						  	),
                            array(
								'type'              => 'colorpicker',
								'heading'           => esc_html__( 'Link Hover Color', 'ut_shortcodes' ),
								'param_name'        => 'link_color_hover',
								'group'             => 'Link Colors'                                
						  	),
                            array(
								'type'              => 'colorpicker',
								'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
								'param_name'        => 'icon_color',
								'group'             => 'Link Colors'                                
						  	),
                            array(
								'type'              => 'colorpicker',
								'heading'           => esc_html__( 'Icon Hover Color', 'ut_shortcodes' ),
								'param_name'        => 'icon_color_hover',
								'group'             => 'Link Colors'                                
						  	),

                            // Glow Effect
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Add Glitch Text Distortion?', 'ut_shortcodes' ),
                                'param_name'        => 'glitch_distortion_effect',
                                'group'             => 'Glitch & Glow',
                                'value'             => array(
                                    esc_html__( 'no, thanks!', 'ut_shortcodes' )                => 'off',
                                    esc_html__( 'yes, apply on appear!' , 'ut_shortcodes' )     => 'on_appear',
                                    esc_html__( 'yes, apply on hover!' , 'ut_shortcodes' )      => 'on_hover',
                                    esc_html__( 'yes, apply permanently!' , 'ut_shortcodes' )   => 'permanent',
                                )
                            ),
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Glitch Text Distortion Style', 'ut_shortcodes' ),
                                'param_name'        => 'glitch_distortion_effect_style',
                                'group'             => 'Glitch & Glow',
                                'value'             => array(
                                    esc_html__( 'Style 1', 'ut_shortcodes' )                => 'style-1',
                                    esc_html__( 'Style 2', 'ut_shortcodes' )                => 'style-2',
                                    esc_html__( 'Style 3', 'ut_shortcodes' )                => 'style-3',
                                ),
                                'dependency'        => array(
                                    'element' => 'glitch_distortion_effect',
                                    'value'   => array('on_appear', 'on_hover', 'permanent'),
                                )
                            ),
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Activate Glow Effect?', 'ut_shortcodes' ),
                                'param_name'        => 'glow_effect',
                                'group'             => 'Glitch & Glow',
                                'value'             => array(
                                    esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                    esc_html__( 'yes'  , 'ut_shortcodes' ) => 'yes'
                                ),
                            ),
                            array(
                                'type'              => 'colorpicker',
                                'heading'           => esc_html__( 'Glow Color', 'ut_shortcodes' ),
                                'param_name'        => 'glow_color',
                                'group'             => 'Glitch & Glow',
                                'dependency'        => array(
                                    'element' => 'glow_effect',
                                    'value'   => array('yes'),
                                )
                            ),
                            array(
                                'type'              => 'colorpicker',
                                'heading'           => esc_html__( 'Glow Text Shadow Color', 'ut_shortcodes' ),
                                'param_name'        => 'glow_shadow_color',
                                'group'             => 'Glitch & Glow',
                                'dependency'        => array(
                                    'element' => 'glow_effect',
                                    'value'   => array('yes'),
                                )
                            ),

                            // Animation
                            array(
                                'type'              => 'animation_style',
                                'heading'           => __( 'Animation Effect', 'ut_shortcodes' ),
                                'description'       => __( 'Select image animation effect.', 'ut_shortcodes' ),
                                'group'             => 'Animation',
                                'param_name'        => 'effect',
                                'settings' => array(
                                    'type' => array(
                                        'in',
                                        'out',
                                        'other',
                                    ),
                                )
                                
                            ),
                            array(
                                'type'              => 'textfield',
                                'heading'           => esc_html__( 'Animation Duration', 'unitedthemes' ),
                                'description'       => esc_html__( 'Animation time in seconds  e.g. 1s', 'unitedthemes' ),
                                'param_name'        => 'animation_duration',
                                'group'             => 'Animation',
                            ), 
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Animate Once?', 'unitedthemes' ),
                                'description'       => esc_html__( 'Animate only once when reaching the viewport, animate everytime when reaching the viewport or make the animation infinite? By default the animation executes everytime when the element becomes visible in viewport, means when leaving the viewport the animation will be reseted and starts again when reaching the viewport again. By setting this option to yes, the animation executes exactly once. By setting it to infinite, the animation loops all the time, no matter if the element is in viewport or not.', 'unitedthemes' ),
                                'param_name'        => 'animate_once',
                                'group'             => 'Animation',
                                'value'             => array(
                                    esc_html__( 'yes', 'unitedthemes' )      => 'yes',
                                    esc_html__( 'no' , 'unitedthemes' )      => 'no',
                                    esc_html__( 'infinite', 'unitedthemes' ) => 'infinite',
                                )
                            ),  
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Animate Image on Tablet?', 'ut_shortcodes' ),
                                'param_name'        => 'animate_tablet',
                                'group'             => 'Animation',
                                'edit_field_class'  => 'vc_col-sm-6',
                                'value'             => array(
                                    esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                    esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                                ),
                            ),
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Animate Image on Mobile?', 'ut_shortcodes' ),
                                'param_name'        => 'animate_mobile',
                                'group'             => 'Animation',
                                'edit_field_class'  => 'vc_col-sm-6',
                                'value'             => array(
                                    esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                    esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                                ),
                            ),                            
                            array(
                                'type'              => 'dropdown',
                                'heading'           => esc_html__( 'Delay Animation?', 'ut_shortcodes' ),
                                'description'       => esc_html__( 'Time in milliseconds until the image appears. e.g. 200', 'ut_shortcodes' ),
                                'param_name'        => 'delay',
                                'group'             => 'Animation',
                                'edit_field_class'  => 'vc_col-sm-6',
                                'value'             => array(
                                    esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                    esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'                                                                        
                                )
                            ),
                            array(
                                'type'              => 'textfield',
                                'heading'           => esc_html__( 'Delay Timer', 'ut_shortcodes' ),
                                'description'       => esc_html__( 'Time in milliseconds until the next image appears. e.g. 200', 'ut_shortcodes' ),
                                'param_name'        => 'delay_timer',
                                'group'             => 'Animation',
                                'edit_field_class'  => 'vc_col-sm-6',
                                'dependency'        => array(
                                    'element' => 'delay',
                                    'value'   => 'true',
                                )
                            ), 
                            
                            
                            
                            // custom CSS class    
                            array(
                                'type'              => 'textfield',
                                'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                                'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                                'param_name'        => 'class',
                                'group'             => 'General'
                            ),
                    
                    
                            // Design Options                          
                            array(
                                'type'              => 'css_editor',
                                'param_name'        => 'css',
                                'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                            ),
                    
                            
                        )                        
                        
                    )
                
                ); /* end mapping */
                
            } 
        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {

            /**
             * @var $align
             * @var $align_tablet
             * @var $align_mobile
             */

            extract( shortcode_atts( array (
	            'link'                           => '',
	            'align'                          => 'center',
	            'align_tablet'                   => 'inherit',
	            'align_mobile'                   => 'center',
	            'icon'                           => '',
	            'icon_type'                      => 'bklynicons',
	            'fontawesome_v_correction_t'     => '',
	            'fontawesome_v_correction_b'     => '',
	            'icon_fontawesome'               => '',
	            'icon_image'                     => '',
	            'icon_bklyn'                     => '',
	            'icon_position'                  => 'before',
	            'icon_size'                      => ut_get_theme_options_font_setting( 'body_font', 'font-size', "12" ),
	            'icon_animation'                 => '',
	            'font_size'                      => ut_get_theme_options_font_setting( 'body_font', 'font-size', "12" ),
	            'font_style'                     => '',
	            'font_weight'                    => '',
	            'text_transform'                 => '',
	            'letter_spacing'                 => '',
	            'line_height'                    => '',
	            'link_color'                     => '',
	            'link_color_hover'               => '',
	            'icon_color'                     => '',
	            'icon_color_hover'               => '',
	            'effect'                         => '',

	            // Glitch and Glow
	            'glow_effect'                    => '',
	            'glow_color'                     => get_option( 'ut_accentcolor', '#F1C40F' ),
	            'glow_shadow_color'              => 'black',
	            'stroke_effect'                  => 'global',
	            'glitch_distortion_effect'       => '',
	            'glitch_distortion_effect_style' => 'style-1',

	            'animate_once'       => 'yes',
	            'animate_mobile'     => false,
	            'animate_tablet'     => false,
	            'delay'              => 'false',
	            'delay_timer'        => '100',
	            'animation_duration' => '',
	            'css'                => '',
	            'class'              => ''
            ), $atts ) ); 
            
            $link_icon = false;
            $url = '#';
            
            // classes
            $attributes = array();
            $classes    = array();
            $classes[]  = $class;
            
            if( $icon_animation ) {
                $classes[] = 'ut-custom-link-module-with-animation';                 
            }
            
            $classes[] = 'ut-custom-link-module-icon-' . $icon_position;
            $classes[] = 'ut-custom-link-module-' . $align;
            
            // animation effect 
            if( !empty( $effect ) && $effect != 'none' ) {
                
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
            
            // attributes string
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );
            
            
            // link settings
            if( function_exists('vc_build_link') && $link ) {
                
                $link = vc_build_link( $link );
                
                // assign link
                $url = !empty( $link['url'] ) ? $link['url'] : '#';
                
            } 
            
            $target = !empty( $link['target'] ) ? $link['target'] : '_self';
            $title = !empty( $link['title'] )  ? $link['title'] : '';
            $rel = !empty( $link['rel'] ) ? 'rel="' . esc_attr( trim( $link['rel'] ) ) . '"' : '';
            
            // icon settings
            if( $icon_type == 'bklynicons' && !empty( $icon_bklyn ) ) {
                    
                $link_icon = $icon_bklyn;

            } else {

                if( strpos( $icon_fontawesome, 'fa fa-' ) === false ) {
                    
                    $link_icon = str_replace('fa-', 'fa fa-', $icon_fontawesome );     
                    
                } else {
                    
                    $link_icon = $icon_fontawesome;
                    
                }
                
            }             
            
            // inline custom css 
            $id = uniqid("ut_cl_");
            
            $css_style = '';

            // Setup Align
            $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
            $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

            if( $align_mobile ) {
                $css_style.= '@media (max-width: 767px) { #' . $id . '.ut-custom-link-module-holder { display:block; text-align:' . $align_mobile . ' !important; } }';
            }

            if( $align_tablet ) {
                $css_style.= '@media (min-width: 768px) and (max-width: 1024px) { #' . $id . '.ut-custom-link-module-holder { display:block; text-align:' . $align_tablet . ' !important; } }';
            }

            if( $align ) {
                $css_style.= '@media (min-width: 1025px) { #' . $id . '.ut-custom-link-module-holder { display:block; text-align:' . $align . '; } }';
            }
            
            if( !empty( $link_color ) ) {
                $css_style .= '#' . $id . ' a { color:' . $link_color . '; }';
            }
            
            if( !empty( $link_color_hover ) ) {
                $css_style .= '#' . $id . ' a:hover { color:' . $link_color_hover . '; }';
                $css_style .= '#' . $id . ' a:active { color:' . $link_color_hover . '; }';
                $css_style .= '#' . $id . ' a:focus { color:' . $link_color_hover . '; }';
            }
                        
            if( !empty( $icon_color ) ) {
                $css_style .= '#' . $id . ' a i { color:' . $icon_color . '; }';
            }
            
            if( !empty( $icon_color_hover ) ) {
                $css_style .= '#' . $id . ' a:hover i { color:' . $icon_color_hover . '; }';
                $css_style .= '#' . $id . ' a:active i { color:' . $icon_color_hover . '; }';
                $css_style .= '#' . $id . ' a:focus i { color:' . $icon_color_hover . '; }';
            }

            if( !empty( $font_style ) ) {
                $css_style .= '#' . $id . ' { font-style:' . $font_style . '; }';
            }

            if( !empty( $font_weight ) ) {
                $css_style .= '#' . $id . ' { font-weight:' . $font_weight . '; }';
            }
            
            if( !empty( $text_transform ) ) {
                $css_style .= '#' . $id . ' { text-transform:' . $text_transform . '; }';
            }
            
            if( !empty( $font_size ) ) {
                $css_style .= '#' . $id . ' { font-size:' . $font_size . 'px; }';
            }

	        if( $icon_type == 'fontawesome' && $fontawesome_v_correction_t  ) {
		        $css_style .= '#' . $id . ' i.fa { margin-top: ' . $fontawesome_v_correction_t . 'px; }';
	        }

	        if( $icon_type == 'fontawesome' && $fontawesome_v_correction_b  ) {
		        $css_style .= '#' . $id . ' i.fa { margin-bottom: ' . $fontawesome_v_correction_b . 'px; }';
	        }

            if( !empty( $icon_size ) ) {

                $css_style .= '#' . $id . ' i { font-size:' . $icon_size . 'px; }';

            }
            
            if( !empty( $letter_spacing ) ) {
                $css_style .= '#' . $id . ' { letter-spacing:' . $letter_spacing . 'em; }';
            }
            
            if( !empty( $line_height ) ) {
                $css_style .= '#' . $id . ' { line-height:' . $line_height . '%; }';
            }

            if( $glow_effect == 'yes' ) {

                $css_style.= '#' . $id . ' { 
                            -webkit-text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px ' . $glow_shadow_color . ';
                               -moz-text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px ' . $glow_shadow_color . ';
                                    text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px ' . $glow_shadow_color . '; 
                        }';

            }

            // Text Distortion
            $glitch_attributes = array();

            // Glitch on Appear
            if( $glitch_distortion_effect == 'on_appear' ) {

                $classes[] = 'ut-glitch-on-appear';
                $glitch_attributes['data-ut-glitch-class'] = 'ut-simple-glitch-text-' . $glitch_distortion_effect_style;

            }

            // Glitch on Hover
            if( $glitch_distortion_effect == 'on_hover' ) {

                $classes[] = 'ut-simple-glitch-text-hover';
                $classes[] = 'ut-simple-glitch-text-' . $glitch_distortion_effect_style . '-hover';

            }

            // Glitch Permanent
            if( $glitch_distortion_effect == 'permanent' ) {

                $classes[] = 'ut-simple-glitch-text-permanent';
                $classes[] = 'ut-simple-glitch-text-' . $glitch_distortion_effect_style . '-permanent';

            }

            // attributes string
            $glitch_attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $glitch_attributes,
                array_keys( $glitch_attributes )
            ) );


            /* start output */
            $output = '';
            
            // add css to output
            if( !empty( $css_style ) ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );
            }
            
            $output .= '<div id="' . esc_attr( $id ) . '" ' . $attributes . ' class="ut-custom-link-module-holder ' . implode(' ', $classes ) . '" ' . $glitch_attributes . '>';
            
                $output .= '<a title="' . esc_attr( $title ) . '" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '" class="ut-custom-link-module" ' . $rel . '>';

                    if( $icon && $link_icon && $icon_position == 'before' ) {

                        $output .= '<i class="' . $link_icon . '"></i>';    

                    }

                    $output .= $title;

                    if( $icon && $link_icon && $icon_position == 'after' ) {

                        $output .= '<i class="' . $link_icon . '"></i>';    

                    }

                $output .= '</a>';
            
            $output .= '</div>';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
            
            return $output;            
        
        }
            
    }

}

new UT_Custom_Link;