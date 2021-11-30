<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Service_Icon_Box' ) ) {
	
    class UT_Service_Icon_Box {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_service_icon_box';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Service Box Vertical', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Promote and demonstrate services, features or qualifications in a single box with an advanced linking features on the icon itself.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/service-box-vertical.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Icon library', 'ut_shortcodes' ),
                            'description'   => esc_html__( 'Select icon library.', 'ut_shortcodes' ),
                            'param_name'    => 'icon_type',
                            'group'         => 'General',
                            'value'         => array(
                                esc_html__( 'Font Awesome', 'ut_shortcodes' ) => 'fontawesome',
                                esc_html__( 'Brooklyn Icons', 'ut_shortcodes' ) => 'bklynicons',
                                esc_html__( 'Linea Icons (with animated draw)', 'ut_shortcodes' ) => 'lineaicons',
                                esc_html__( 'Orion Icons (with animated draw)', 'ut_shortcodes' ) => 'orionicons',
                            ),

                        ),
                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'icon',
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
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'icon_linea',
                            'group'             => 'General',
                            'settings' => array(
                                'emptyIcon'     => true,
                                'type'          => 'lineaicons',
                            ),
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => 'lineaicons',
                            ),

                        ),
                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'icon_orion',
                            'group'             => 'General',
                            'settings' => array(
                                'emptyIcon'     => true,
                                'type'          => 'orionicons',
                            ),
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => 'orionicons',
                            ),

                        ),
                        array(
                            'type'              => 'attach_image',
                            'heading'           => esc_html__( 'or upload an own Icon', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'recommended size 48x48', 'ut_shortcodes' ),
                            'param_name'        => 'imageicon',
                            'group'             => 'General',
                        ),

	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Spacing Bottom', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_margin_bottom',
		                    'group'             => 'General',
		                    'value'             => array(
			                    'default' => '30',
			                    'min'     => '20',
			                    'max'     => '80',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
	                    ),



                        /* SVG Animation */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Draw SVG Icons?', 'unitedthemes' ),
                            'param_name'        => 'draw_svg_icons',
                            'group'             => 'Draw Settings',
                            'value'             => array(
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                            ),
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('lineaicons','orionicons'),
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Draw Type', 'unitedthemes' ),
                            'description'		=> esc_html__( 'Defines what kind of animation will be used.', 'unitedthemes' ),
                            'param_name'        => 'draw_svg_type',
                            'group'             => 'Draw Settings',
                            'value'             => array(
                                esc_html__( 'oneByOne', 'ut_shortcodes' ) 		=> 'oneByOne',
                                esc_html__( 'delayed', 'ut_shortcodes' ) 		=> 'delayed',
                                esc_html__( 'sync', 'ut_shortcodes' ) 			=> 'sync',
                                esc_html__( 'scenario', 'ut_shortcodes' ) 		=> 'scenario',
                                esc_html__( 'scenario-sync', 'ut_shortcodes' ) 	=> 'scenario-sync'
                            ),
                            'dependency' => array(
                                'element'   => 'draw_svg_icons',
                                'value'     => array('yes'),
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Animation duration, in frames.', 'ut_shortcodes' ),
                            'param_name'        => 'draw_svg_duration',
                            'group'             => 'Draw Settings',
                            'value'             => array(
                                'default' => '100',
                                'min'     => '10',
                                'max'     => '600',
                                'step'    => '10',
                                'unit'    => ''
                            ),
                            'dependency' => array(
                                'element'   => 'draw_svg_icons',
                                'value'     => array('yes'),
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Delay Draw Animation.', 'ut_shortcodes' ),
                            'param_name'        => 'draw_svg_delay',
                            'group'             => 'Draw Settings',
                            'value'             => array(
                                'default' => '0',
                                'min'     => '0',
                                'max'     => '2000',
                                'step'    => '10',
                                'unit'    => ''
                            ),
                            'dependency' => array(
                                'element'   => 'draw_svg_icons',
                                'value'     => array('yes'),
                            ),
                        ),

                        /* Colors */
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'icon_color',
                            'group'             => 'Colors',
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('bklynicons','fontawesome'),
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'svg_color',
                            'group'             => 'Colors',
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('lineaicons', 'orionicons'),
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Color 2', 'ut_shortcodes' ),
                            'description'       => __( 'Most Orion Icons do support a second color.', 'ut_shortcodes' ),
                            'param_name'        => 'svg_color_2',
                            'group'             => 'Colors',
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('orionicons'),
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'icon_hover_color',
                            'group'             => 'Colors',
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('bklynicons','fontawesome'),
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'svg_color_hover',
                            'group'             => 'Colors',
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('lineaicons', 'orionicons'),
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Hover Color 2', 'ut_shortcodes' ),
                            'param_name'        => 'svg_color_2_hover',
                            'group'             => 'Colors',
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('orionicons'),
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Background Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'hovercolor',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design options', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Headline', 'ut_shortcodes' ),
                            'param_name'        => 'headline',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),
	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Headline Margin Bottom', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'value in px , eg "20px" (optional)' , 'ut_shortcodes' ),
		                    'param_name'        => 'headline_margin_bottom',
		                    'group'             => 'General'
	                    ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Headline Color', 'ut_shortcodes' ),
                            'param_name'        => 'headline_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'textarea',
                            'heading'           => esc_html__( 'Text', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'param_name'        => 'content',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'text_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Link', 'ut_shortcodes' ),
                            'param_name'        => 'link',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Link Target', 'ut_shortcodes' ),
                            'param_name'        => 'target',
                            'group'             => 'General',
                            'value'             => array(
                                '_blank'  => esc_html__( '_blank', 'ut_shortcodes' ),
                                '_self'   => esc_html__( '_self', 'ut_shortcodes' ),
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'class'             => '',
                            'heading'           => esc_html__( 'Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'align',
                            'value'             => '',
                            'description'       => '',
                            'group'             => 'General',
                            'value'             => array(
                                'center'    => esc_html__( 'center', 'ut_shortcodes' ),
                                'left'      => esc_html__( 'left', 'ut_shortcodes' ),
                                'right'     => esc_html__( 'right', 'ut_shortcodes' ),
                            ),
                        ),

                        /* animation */
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

                        /* css */
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                    )

                )

            ); /* end mapping */
        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
	            'icon_type'              => 'fontawesome',
	            'icon'                   => '',
	            'icon_bklyn'             => '',
	            'icon_linea'             => '',
	            'icon_orion'             => '',
	            'imageicon'              => '',
	            'icon_margin_bottom'     => '',
	            'icon_color'             => '',
	            'icon_hover_color'       => '',
	            'color'                  => '#CCC',
	            'hovercolor'             => get_option( 'ut_accentcolor', '#F1C40F' ),
	            'url'                    => '',
	            'link'                   => '#',
	            'headline'               => '',
	            'headline_color'         => '',
	            'headline_margin_bottom' => '',
	            'text_color'             => '',
	            'align'                  => 'center',
	            'effect'                 => '',
	            'animate_once'           => 'yes',
	            'animate_mobile'         => false,
	            'animate_tablet'         => false,
	            'delay'                  => 'false',
	            'delay_timer'            => '100',
	            'animation_duration'     => '',
	            'width'                  => '',    /* deprecated */
	            'last'                   => '',    /* deprecated */
	            'target'                 => '_blank',
	            'css'                    => '',
	            'class'                  => ''
            ), $atts ) ); 
            
            $classes    = array();
            $classes_2  = array();
            $attributes = array();
            
            
            /* deprecated - will be removed one day - start block */
            
                $grid = array( 
                    'third'   => 'ut-one-third',
                    'fourth'  => 'ut-one-fourth',
                    'half'    => 'ut-one-half'
                );  
                
                $classes[] = ( $last == 'true' ) ? 'ut-column-last' : '';
                $classes[] = !empty( $grid[$width] ) ? $grid[$width] : 'clearfix';
                $classes[] = $class;
                
            /* deprecated - will be removed one day - end block */
            
            /* animation effect */
            if( !empty( $effect ) && $effect != 'none' ) {
                
                $attributes['data-effect']      = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );
                $attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;

                // draw icons delay
                $atts['draw_svg_delay'] = $attributes['data-delay'];
                
                if( !empty( $animation_duration ) ) {

                    $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );
                    $atts['draw_svg_delay'] = $atts['draw_svg_delay'] + ( ut_delete_timer_unit( $animation_duration, 's' ) * 1000 / 3 );

                } else {

                    $atts['draw_svg_delay'] = $atts['draw_svg_delay'] + ( ut_delete_timer_unit( '1s', 's' ) * 1000 / 3 );

                }
                
                $classes_2[]  = 'ut-animate-element';
                $classes_2[]  = 'animated';
                
                if( !$animate_tablet ) {

                    $classes_2[]  = 'ut-no-animation-tablet';

                    if( function_exists('unite_mobile_detection') && unite_mobile_detection()->isTablet() ) {

                        $atts['draw_svg_delay'] = 0;

                    }

                }
                
                if( !$animate_mobile ) {

                    $classes_2[]  = 'ut-no-animation-mobile';

                    if( function_exists('unite_mobile_detection') && unite_mobile_detection()->isMobile() && !unite_mobile_detection()->isTablet() ) {

                        $atts['draw_svg_delay'] = 0;

                    }

                }
                
                if( $animate_once == 'infinite' ) {
                    $classes_2[]  = 'infinite';
                }
                
            }  
            
            
            /* align */
            $classes[] = 'ut-service-icon-box-' . $align;
            
            /* icon setting */
            if( !empty( $imageicon ) && is_numeric( $imageicon ) ) {
                $imageicon = wp_get_attachment_url( $imageicon );        
            }            
            
            /* overwrite default icon */
            $icon = empty( $imageicon ) ? $icon : $imageicon;
            
            /* check if icon is an image */
            $image_icon = strpos( $icon, '.png' ) !== false || strpos( $icon, '.jpg' ) !== false || strpos( $icon, '.gif' ) !== false || strpos( $icon, '.ico' ) !== false || strpos( $icon, '.svg' ) !== false ? true : false;
                        
            /* font awesome icon */
            if( !$image_icon ) {
                
                if( $icon_type == 'bklynicons' && !empty( $icon_bklyn ) ) {
                    
                    $icon = $classes_2[] = $icon_bklyn;
                    
                } elseif( $icon_type == 'lineaicons' && !empty( $icon_linea ) ) {
					
					$icon = $icon_linea;
					$classes_2[] = 'ut-service-icon-with-svg';					
					
                } elseif( $icon_type == 'orionicons' && !empty( $icon_orion ) ) {
					
					$icon = $icon_orion;
					$classes_2[] = 'ut-service-icon-with-svg';					
					
                } else {
                    
                    /* fallback */
                    if( strpos( $icon, 'fa fa-' ) === false ) {
                        
                        $icon = $classes_2[] = str_replace('fa-', 'fa fa-', $icon );     
                        
                    } else {
                        
                        $icon = $classes_2[] = $icon;
                        
                    }
                                    
                }     
                
            }
            
            /* link alt */
            if( empty( $url ) ) {
                $url = $link;
            }
            
            $id       = uniqid("utbx_");
            $outer_id = uniqid("utbx_o_");
            
            $css_style = '<style type="text/css">';
            
                // Design Options Gradient
                $vc_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, '.' ), $this->shortcode, $atts );
                $css_style .= ut_add_gradient_css( $vc_class, $atts );            
            
                $css_style.= '#' . $id . ' { background:' . $color . '; }';
                $css_style.= '#' . $id . ':hover { background: ' . $hovercolor . '; } ';
                $css_style.= '#' . $id . ':after { box-shadow: 0 0 0 4px ' . $hovercolor . '; } ';

		        if( !empty( $icon ) && $icon_margin_bottom ) {
			        $css_style .= '#' . $outer_id . ' .ut-service-icon-box-content { margin-top: ' . $icon_margin_bottom . 'px; }';
		        }

                if( $headline_color ) {
                    $css_style.= '#' . $outer_id . ' h3 { color:' . $headline_color . ' !important; }';
                }

		        if( $headline_margin_bottom ) {

			        if( empty( $content ) ) {

				        $css_style .= '#' . $outer_id . ' h3 { margin-bottom: ' . $headline_margin_bottom  . '; }';

			        } else {

				        $css_style .= '#' . $outer_id . ' h3 + p { margin-top: ' . $headline_margin_bottom  . '; }';

			        }

		        }

                if( $text_color ) {
                    $css_style.= '#' . $outer_id . ' p { color:' . $text_color . ' !important; }';
                }
                
                if( $icon_color ) {
                    $css_style.= '#' . $id . ' { color:' . $icon_color . '; }';
                }
                
                if( $icon_hover_color ) {
                    $css_style.= '#' . $id . ':hover { color: ' . $icon_hover_color . '; } ';
                }

                if( empty( $icon ) ) {
                    $css_style.= '#' . $outer_id . ' .ut-service-icon-box-content { margin-top:0; } ';
                }

            $css_style.= '</style>';
            
            /* attributes string */
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );
            
            /* start output */
            $output = '';  
            
            $output .= '<div id="' . $outer_id . '" class="ut-service-icon-box ' . implode(' ', $classes ) . '">';
                
                /* attach CSS */
                $output .= ut_minify_inline_css( $css_style );
                
                if( !empty( $icon ) ) {
                    
                    $output .= '<div class="ut-highlight-icon-wrap ut-highlight-icon-effect">';        
                        
                        if( $image_icon ) {
                               
                            $output .= '<a id="' . esc_attr( $id ) . '" data-id="' . esc_attr( $id ) . '" data-hovercolor="' . esc_attr( $hovercolor ) . '"  ' . $attributes . ' class="ut-highlight-icon ' . implode(' ', $classes_2 ) . '" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '">' . UT_Adaptive_Image::create_lazy( $icon, array( 'alt' => ( !empty( $headline ) ? esc_attr( $headline ) : 'icon' ) ) ) . '</a>';
                            
                        } elseif( $icon_type == 'lineaicons' || $icon_type == 'orionicons' ) {
							
							$output .= '<a id="' . esc_attr( $id ) . '" data-id="' . esc_attr( $id ) . '" data-hovercolor="' . esc_attr( $hovercolor ) . '"  ' . $attributes . ' class="ut-highlight-icon ' . implode(' ', $classes_2 ) . '" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '">';
							
								// fallback colors
								if ( empty( $atts['svg_color'] ) ) {
									$atts['svg_color'] = '#FFF';
								}
							
								$svg = new UT_Draw_SVG( uniqid("ut-svg-"), $atts, $id );
								$output .= $svg->draw_svg_icon();
							
							$output .= '</a>';
							
						} else { 
                        
                            $output .= '<a id="' . esc_attr( $id ) . '" data-id="' . esc_attr( $id ) . '" data-hovercolor="' . esc_attr( $hovercolor ) . '"  ' . $attributes . ' class="ut-highlight-icon ' . implode(' ', $classes_2 ) . '" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '"></a>';
                            
                        }                            
                        
                    $output .= '</div>';
                
                }
                
                if( !empty( $headline ) || !empty( $headline ) ) {
                
                    $output .= '<div class="ut-service-icon-box-content">';
                        
                        if( !empty( $headline ) ) {
                            
                            $output .= '<h3>' . $headline . '</h3>';
                            
                        }
                        
                        $output .= '<p>' . do_shortcode( $content ) . '</p>';
                                    
                    $output .= '</div>';
                
                }
                                    
            $output .= '</div>';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
            
            return $output;
        
        }
            
    }

}

new UT_Service_Icon_Box;

if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_service_icon_box extends WPBakeryShortCode {
        
        /*protected function outputTitle( $title ) {
            
            return $title;
            
        }*/
        
    }
    
}