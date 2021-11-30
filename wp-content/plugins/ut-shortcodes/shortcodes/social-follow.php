<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Social_Follow' ) ) {
	
    class UT_Social_Follow {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_social_follow';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Social Follow Module', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Create your own list of your social media profiles.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-thumbs-up ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/social-follow.png',
                    'category'        => 'Community',
                    'class'           => 'ut-vc-icon-module ut-community-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Description', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Only for internal use. This adds a label to Visual Composer for an easier element identification.', 'ut_shortcodes' ),
                            'param_name'        => 'social_description',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),

                        array(
                            'type'          => 'param_group',
                            'heading'       => esc_html__( 'Social Items', 'ut_shortcodes' ),
                            'group'         => 'General',
                            'param_name'    => 'socials',
                            'params'        => array(

                                array(
                                    'type'          => 'iconpicker',
                                    'heading'       => esc_html__( 'Icon', 'ut_shortcodes' ),
                                    'admin_label'   => true,
                                    'param_name'    => 'icon',
                                ),
                                array(
                                    'type'              => 'dropdown',
                                    'heading'           => esc_html__( 'Icon Colors', 'ut_shortcodes' ),
                                    'param_name'        => 'colors',
                                    'group'             => 'General',
                                    'value'             => array(
                                        esc_html__( 'Global Colors', 'ut_shortcodes' )     => 'global',
                                        esc_html__( 'Custom Colors', 'ut_shortcodes' )     => 'custom',
                                    )

                                ),
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Background /Outline Color', 'ut_shortcodes' ),
                                    'description'       => esc_html__( 'Supported shapes: round, square and round corners.', 'ut_shortcodes' ),
                                    'edit_field_class'  => 'vc_col-sm-6',
                                    'param_name'        => 'background',
                                    'dependency' => array(
                                        'element' => 'colors',
                                        'value'   => array( 'custom' ),
                                    ),
                                ),
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Background / Outline Hover Color', 'ut_shortcodes' ),
                                    'description'       => esc_html__( 'Supported shapes: round, square and round corners.', 'ut_shortcodes' ),
                                    'edit_field_class'  => 'vc_col-sm-6 vc-clear-left',
                                    'param_name'        => 'background_hover',
                                    'dependency' => array(
                                        'element' => 'colors',
                                        'value'   => array( 'custom' ),
                                    ),
                                ),
	                            array(
		                            'type'              => 'colorpicker',
		                            'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
		                            'edit_field_class'  => 'vc_col-sm-6',
		                            'param_name'        => 'icon_color',
		                            'dependency' => array(
			                            'element' => 'colors',
			                            'value'   => array( 'custom' ),
		                            ),
	                            ),
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Icon Hover Color', 'ut_shortcodes' ),
                                    'edit_field_class'  => 'vc_col-sm-6',
                                    'param_name'        => 'icon_color_hover',
                                    'dependency' => array(
                                        'element' => 'colors',
                                        'value'   => array( 'custom' ),
                                    ),
                                ),

                                array(
                                    'type'          => 'vc_link',
                                    'heading'       => esc_html__( 'Link', 'ut_shortcodes' ),
                                    'param_name'    => 'link',
                                ),

                            ),

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'align',
                            'group'             => 'General',
                            'edit_field_class' => 'vc_col-sm-4',
                            'value'             => array(
                                'left'      => esc_html__( 'left', 'ut_shortcodes' ),
                                'center'    => esc_html__( 'center', 'ut_shortcodes' ),
                                'right'     => esc_html__( 'right', 'ut_shortcodes' ),
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
			                    esc_html__( 'center (default)', 'ut_shortcodes' )              => 'center',
		                    	esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
		                    	esc_html__( 'left', 'ut_shortcodes' )                => 'left',
			                    esc_html__( 'right', 'ut_shortcodes' )               => 'right',
		                    ),
	                    ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Gap Size between Items', 'ut_shortcodes' ),
                            'param_name'        => 'gap',
                            'group'             => 'General',
                            'value'             => array(
                                '20'    => esc_html__( 'default', 'ut_shortcodes' ),
                                '40'    => esc_html__( '40 Pixel', 'ut_shortcodes' ),
                            ),
                        ),

                        // Animation
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Icons?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Animate each element inside your icon list with an awesome animation effect.', 'ut_shortcodes' ),
                            'param_name'        => 'animate',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-12',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',

                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Icons on Tablet?', 'ut_shortcodes' ),
                            'param_name'        => 'animate_tablet',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                            'dependency' => array(
                                'element' => 'animate',
                                'value'   => array( 'true' ),
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Icons on Mobile?', 'ut_shortcodes' ),
                            'param_name'        => 'animate_mobile',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                            'dependency' => array(
                                'element' => 'animate',
                                'value'   => array( 'true' ),
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Set delay until Icon Animation starts?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'This timer allows you to delay the entire animation process of the icon list.', 'ut_shortcodes' ),
                            'param_name'        => 'global_delay_animation',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                            'dependency'        => array(
                                'element' => 'animate',
                                'value'   => 'true'
                            )
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Delay Timer', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Time in milliseconds until the icon list animation should start. e.g. 200', 'ut_shortcodes' ),
                            'param_name'        => 'global_delay_timer',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'global_delay_animation',
                                'value'   => 'true',
                            )
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
                            ),
                            'dependency'        => array(
                                'element' => 'animate',
                                'value'   => 'true',
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Delay Animation?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Animate Icons inside the Gallery one by one.', 'ut_shortcodes' ),
                            'param_name'        => 'delay_animation',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                            'dependency'        => array(
                                'element' => 'animate',
                                'value'   => 'true'
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
                                'element' => 'delay_animation',
                                'value'   => 'true',
                            )
                        ),

                        array(
                            'type'              => 'animation_style',
                            'heading'           => __( 'Animation Effect', 'ut_shortcodes' ),
                            'description'       => __( 'Select initial loading animation for icons.', 'ut_shortcodes' ),
                            'group'             => 'Animation',
                            'param_name'        => 'effect',
                            'settings' => array(
                                'type' => array(
                                    'in',
                                    'other',
                                ),
                            ),
                            'dependency'        => array(
                                'element' => 'animate',
                                'value'   => 'true',
                            )

                        ),

                        // Global Colors
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Icon Shape', 'ut_shortcodes' ),
                            'param_name'        => 'shape',
                            'group'             => 'Global Colors',
                            'value'             => array(
                                esc_html__( 'no shape', 'ut_shortcodes' )      => 'icon-only',
                                esc_html__( 'round', 'ut_shortcodes' )         => 'round',
                                esc_html__( 'square', 'ut_shortcodes' )        => 'square',
                                esc_html__( 'round corners', 'ut_shortcodes' ) => 'round-corners',
                                esc_html__( 'outline', 'ut_shortcodes' )       => 'outline',
                            )

                        ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Border Radius', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_border_radius',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Global Colors',
		                    'value'             => array(
			                    'default'   => '0',
			                    'min'       => '0',
			                    'max'       => '50',
			                    'step'      => '1',
			                    'unit'      => 'px'
		                    ),
		                    'dependency' => array(
			                    'element'   => 'shape',
			                    'value'     => 'outline',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Border Width', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_border_width',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Global Colors',
		                    'value'             => array(
			                    'default'   => '2',
			                    'min'       => '1',
			                    'max'       => '5',
			                    'step'      => '1',
			                    'unit'      => 'px'
		                    ),
		                    'dependency' => array(
			                    'element'   => 'shape',
			                    'value'     => 'outline',
		                    ),
	                    ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Icon Size', 'ut_shortcodes' ),
                            'param_name'        => 'size',
                            'value'             => array(
                                'default'   => '16',
                                'min'       => '16',
                                'max'       => '50',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'group'             => 'Global Colors',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Background / Outline Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Supported shapes: round, square, outline and round corners.', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Global Colors',
                            'param_name'        => 'background',
                            'dependency' => array(
                                'element' => 'shape',
                                'value'   => array( 'round', 'square', 'round-corners', 'outline' ),
                            ),
                        ),

	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Background /Outline Hover Color', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Supported shapes: round, square and round corners.', 'ut_shortcodes' ),
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Global Colors',
		                    'param_name'        => 'background_hover',
		                    'dependency' => array(
			                    'element' => 'shape',
			                    'value'   => array( 'round', 'square', 'round-corners', 'outline' ),
		                    ),
	                    ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Global Colors',
                            'param_name'        => 'icon_color',
                            'dependency' => array(
                                'element' => 'shape',
                                'value'   => array( 'round', 'square', 'round-corners', 'icon-only', 'outline' ),
                            ),
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Hover Color', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Global Colors',
                            'param_name'        => 'icon_color_hover',
                            'dependency' => array(
                                'element' => 'shape',
                                'value'   => array( 'round', 'square', 'round-corners', 'icon-only', 'outline' ),
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                        /* css editor */
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        )

                    )

                )

            ); // end mapping
        
        }

	    function social_follow_settings_json( $atts ) {

		    /**
		     * @var $animate
		     * @var $effect
		     * @var $delay_animation
		     * @var $global_delay_animation
		     * @var $delay_timer
		     * @var $global_delay_timer
		     */

		    extract( shortcode_atts( array (
			    'animate'                => 'false',
			    'effect'                 => '',
			    'delay_animation'        => 'false',
			    'global_delay_animation' => 'false',
			    'delay_timer'            => 100,
			    'global_delay_timer'     => 100,
		    ), $atts ) );

		    $json = array(

			    'animate'                => filter_var( $animate, FILTER_VALIDATE_BOOLEAN ),
			    'effect'                 => $effect,
			    'delay_animation'        => filter_var( $delay_animation, FILTER_VALIDATE_BOOLEAN ),
			    'global_delay_animation' => filter_var( $global_delay_animation, FILTER_VALIDATE_BOOLEAN ),
			    'delay_timer'            => $delay_timer,
			    'global_delay_timer'     => $global_delay_timer

		    );

		    return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

	    }


        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'socials'               => '',
                'shape'                 => 'icon-only',
                'icon_border_radius'    => '',
                'icon_border_width'     => '2',
                'align'                 => 'left',
                'align_tablet'          => 'inherit',
                'align_mobile'          => 'center',
                'gap'                   => '20',
                'size'                  => '',
                'background'            => '',
                'background_hover'      => '',
                'icon_color'            => '',
                'icon_color_hover'      => '',
                'animate'               => 'false',
                'effect'                => '',
                'animate_once'          => 'yes',
                'animate_mobile'        => false,
                'animate_tablet'        => false,
                'class'                 => '',
                'css'                   => ''
            ), $atts ) ); 
            
            // variables and arrays
            $classes    = array(
                'ut-social-follow-module',
            );
            $classes[]  = $class;
            $css_style  = '';

	        // Setup Align
	        $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
	        $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

            if( $align ) {
                $classes[] = 'ut-social-follow-module-' . $align;
            }

	        if( $align_tablet ) {
		        $classes[] = 'ut-social-follow-module-tablet-' . $align_tablet;
	        }

	        if( $align_mobile ) {
		        $classes[] = 'ut-social-follow-module-mobile-' . $align_mobile;
	        }

            if( $gap ) {
                $classes[] = 'ut-social-follow-module-' . $gap;
            }
            
            $animation_classes = array();
            $attributes = array();
            
            if( $animate == 'true' && $effect ) {
                
                $attributes['data-effect']      = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );
                
                $animation_classes[]  = 'ut-animate-social-follow-element';
                $animation_classes[]  = 'animated';
                
                if( !$animate_tablet ) {
                    $animation_classes[]  = 'ut-no-animation-tablet';
                }
                
                if( !$animate_mobile ) {
                    $animation_classes[]  = 'ut-no-animation-mobile';
                }
                
                if( $animate_once == 'infinite' ) {
                    $animation_classes[]  = 'infinite';
                }
                
            }
            
            /* attributes string */
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) ); 
            
            // extract social items
            if( function_exists('vc_param_group_parse_atts') && !empty( $socials ) ) {
                
                $socials = vc_param_group_parse_atts( $socials );    
                            
            }
                        
            // unique list ID 
            $id = uniqid("ut_sf_");
            
            if( $animate == 'true' && $effect ) {
                $css_style .= '#' . esc_attr( $id ) . ' .ut-animate-social-follow-element { opacity: 0; }';
            }
            
            if( !empty( $icon_color ) ) {
                $css_style .= '#' . esc_attr( $id ) . ' li a i { color: ' . $icon_color .'; }';
            }
            
            if( !empty( $icon_color_hover ) ) {
                $css_style .= '#' . esc_attr( $id ) . ' li a:hover i { color: ' . $icon_color_hover .'; }';
                $css_style .= '#' . esc_attr( $id ) . ' li a:focus i { color: ' . $icon_color_hover .'; }';     
            }
            
            if( !empty( $background ) && $shape != 'icon-only' ) {
                $css_style .= '#' . esc_attr( $id ) . ' li a { background: ' . $background .'; }';    
            }
            
            if( !empty( $background_hover ) && $shape != 'icon-only' ) {
                $css_style .= '#' . esc_attr( $id ) . ' li a:hover { background: ' . $background_hover .'; }';
                $css_style .= '#' . esc_attr( $id ) . ' li a:focus { background: ' . $background_hover .'; }';  
            }

	        if( $shape == 'outline' ) {

		        $css_style .= '#' . esc_attr( $id ) . ' li a { background: transparent !important }';

		        if( $icon_border_radius ) {
			        $css_style .= '#' . esc_attr( $id ) . ' li a { border-radius: ' . $icon_border_radius . 'px; }';
		        }

		        $background = !empty( $background ) ? $background : '#FFF';
				$css_style .= '#' . esc_attr( $id ) . ' li a { border: ' . $icon_border_width . 'px solid ' . $background . '; }';

		        if( !empty( $background_hover ) && $shape != 'icon-only' ) {
			        $css_style .= '#' . esc_attr( $id ) . ' li a:hover { border-color: ' . $background_hover .'; }';
		        }

	        }




            // size 
            if( !empty( $size ) ) {
                $css_style .= '#' . esc_attr( $id ) . ' { font-size: ' . $size .'px; }';
            }
            
            if( !empty( $size ) && $shape != 'icon-only' ) {
                
                $css_style .= '#' . esc_attr( $id ) . ' li a { 
                    height: ' . ( $size * 2.5 ) . 'px;
                    line-height: ' . ( $size * 2.5 ) . 'px;
                    width: ' . ( $size * 2.5 ) . 'px;
                }';
                
                $css_style .= '#' . esc_attr( $id ) . ' li a .fa { 
                    line-height: ' . ( $size * 2.5 ) . 'px;
                }';
                
            }
            
            if( !empty( $socials ) && is_array( $socials ) ) {
                
                foreach( $socials as $key => $social ) {
                    
                    if( !empty( $social['colors'] ) && $social['colors'] != 'custom' ) {
                        continue;
                    }
                    
                    if( !empty( $social['icon_color'] ) ) {
                        $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a i { color: ' . $social['icon_color'] .'; }';
                    }
                    
                    if( !empty( $social['icon_color_hover'] ) ) {
                        $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a:hover i { color: ' . $social['icon_color_hover'] .'; }';
                        $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a:focus i { color: ' . $social['icon_color_hover'] .'; }';     
                    }
                    
                    if( !empty( $social['background'] ) && $shape != 'icon-only' ) {
                        $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a { background: ' . $social['background'] .'; }';    
                    }
                    
                    if( !empty( $social['background_hover'] ) && $shape != 'icon-only' ) {
                        $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a:hover { background: ' . $social['background_hover'] .'; }';
                        $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a:focus { background: ' . $social['background_hover'] .'; }';  
                    }

	                if( $shape == 'outline' ) {

		                if( !empty( $social['background'] ) && $shape != 'icon-only' ) {
			                $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a { border-color: ' . $social['background'] .'; }';
		                }

		                if( !empty( $social['background_hover'] ) && $shape != 'icon-only' ) {
			                $css_style .= '#' . esc_attr( $id ) . ' .ut-social-follow-' . $key . ' a:hover { border-color: ' . $social['background_hover'] .'; }';
		                }

	                }
                
                }
            
            }
            
            // start output 
            $output = '';

            // add css
            if( !empty( $css_style ) ) {
                $output .= ut_minify_inline_css( '<style class="bklyn-inline-styles" type="text/css">' . $css_style . '</style>' );
            }
            
            if( !empty( $socials ) && is_array( $socials ) ) {

                $output .= '<ul id="' . esc_attr( $id ) . '" class="' . esc_attr( implode(' ', $classes ) ) . '" data-settings="' . $this->social_follow_settings_json( $atts ) . '">';
                
                foreach( $socials as $key => $social ) {
                    
                    $li_class = 'ut-social-follow-' . ( ( $shape == 'outline' ) ? 'square ut-social-follow-outline' : $shape );
                    
                    $output .= '<li ' . $attributes . ' class="ut-social-follow-' . esc_attr( $key ) . ' ' . $li_class . ' ' . implode( ' ', $animation_classes ) . '">';
                        
                        if( !empty( $social['link'] ) ) {
                                
                            $link = vc_build_link( $social['link'] );
                            
                            $url    = !empty( $link['url'] )    ? $link['url'] : '';
                            $target = !empty( $link['target'] ) ? $link['target'] : '_self';
                            $title  = !empty( $link['title'] )  ? $link['title'] : '';
                            $rel    = !empty( $link['rel'] )    ? 'rel="' . esc_attr( trim( $link['rel'] ) ) . '"' : '';
                            
                            $output .= '<a class="ut-social-follow-link" title="' . esc_attr( $title ) . '" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '" ' . $rel . '>';
                            
                        } else {
                            
                            $output .= '<a class="ut-social-follow-link" href="#" target="_blank">';
                            
                        }  
                    
                        if( !empty( $social['icon'] ) ) {
                    
                            $output .= '<i class="' . esc_attr( $social['icon'] ) . '"></i>';
                        
                        }
                            
                        $output .= '</a>';
                    
                    $output .= '</li>';                        
                    
                }
                
                $output .= '</ul>';
            
            }
                
            return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>';
            
        
        }
            
    }

}

new UT_Social_Follow;