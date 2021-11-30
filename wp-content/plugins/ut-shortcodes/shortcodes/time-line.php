<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Time_Line' ) ) {
	
    class UT_Time_Line {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_simple_time_line';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Time Line', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'A beautiful animated graphical representation of a period of time, on which important events are marked. ', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-th-list ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/simple-time-line.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'          => 'param_group',
                            'heading'       => esc_html__( 'Time Line Events', 'ut_shortcodes' ),
                            'group'         => 'General',
                            'param_name'    => 'events',
                            'params'        => array(

                                array(
                                    'type'              => 'dropdown',
                                    'heading'           => esc_html__( 'Event Marker Size', 'unitedthemes' ),
                                    'param_name'        => 'marker_size',
                                    'value'             => array(
                                        esc_html__( 'small', 'ut_shortcodes' ) => 'small',
                                        esc_html__( 'large', 'ut_shortcodes' ) => 'large'
                                    )
                                ),
                                array(
                                    'type'              => 'dropdown',
                                    'heading'           => esc_html__( 'Event Marker Pulse', 'unitedthemes' ),
                                    'param_name'        => 'marker_pulse',
                                    'value'             => array(
                                        esc_html__( 'off', 'ut_shortcodes' ) => 'off',
                                        esc_html__( 'on', 'ut_shortcodes' ) => 'on'
                                    ),
                                    'dependency' => array(
                                        'element' => 'marker_size',
                                        'value'   => array( 'large' ),
                                    ),
                                ),
                                array(
                                    'type'              => 'attach_image',
                                    'heading'           => esc_html__( 'Image', 'ut_shortcodes' ),
                                    'param_name'        => 'image'
                                ),
                                array(
                                    'type'              => 'textfield',
                                    'heading'           => esc_html__( 'Event Date (Main Headline)', 'ut_shortcodes' ),
                                    'param_name'        => 'title',
                                    'admin_label'       => true,
                                ),
                                array(
                                    'type'              => 'textfield',
                                    'heading'           => esc_html__( 'Event Subheadline', 'ut_shortcodes' ),
                                    'param_name'        => 'subtitle',
                                    'admin_label'       => true,
                                ),
                                array(
                                    'type'              => 'textarea',
                                    'heading'           => esc_html__( 'Event Text', 'ut_shortcodes' ),
                                    'param_name'        => 'text',
                                ),

                            )

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Event Subheadline Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'subtitle_margin_bottom',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '20',
                                'min'       => '0',
                                'max'       => '40',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                        ),

                        // Colors
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Time Line Color', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_color',
                            'group'             => 'Time Line Styling',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Time Line Dot Color (small)', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_dot_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Time Line Styling',
                        ),

                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Time Line Dot Inner Color (small)', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_dot_inner_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Time Line Styling',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Time Line Dot Color (large)', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_dot_large_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Time Line Styling',
                        ),

                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Time Line Dot Inner Color (large)', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_dot_large_inner_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Time Line Styling',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Time Line Event Date Color', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_event_date_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Time Line Styling',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Time Line Event Subheadline Color', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_event_subheadline_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Time Line Styling',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Time Line Event Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'time_line_event_text_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Time Line Styling',
                        ),

                        // Animation
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Time Line?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Animate each element inside your time line with an awesome animation effect.', 'ut_shortcodes' ),
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
                            'heading'           => esc_html__( 'Animate Timeline on Tablet?', 'ut_shortcodes' ),
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
                            'heading'           => esc_html__( 'Animate Timeline on Mobile?', 'ut_shortcodes' ),
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
                            'type'              => 'animation_style',
                            'heading'           => __( 'Left Animation Effect', 'ut_shortcodes' ),
                            'description'       => __( 'Select event animation effect.', 'ut_shortcodes' ),
                            'group'             => 'Animation',
                            'param_name'        => 'animation_style_left',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'settings' => array(
                                'type' => array(
                                    'in',
                                    'out',
                                    'other',
                                ),
                            ),
                            'dependency' => array(
                                'element' => 'animate',
                                'value'   => array( 'true' ),
                            ),

                        ),
                        array(
                            'type'              => 'animation_style',
                            'heading'           => __( 'Right Animation Effect', 'ut_shortcodes' ),
                            'description'       => __( 'Select event animation effect.', 'ut_shortcodes' ),
                            'group'             => 'Animation',
                            'param_name'        => 'animation_style_right',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'settings' => array(
                                'type' => array(
                                    'in',
                                    'out',
                                    'other',
                                ),
                            ),
                            'dependency' => array(
                                'element' => 'animate',
                                'value'   => array( 'true' ),
                            ),

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Event Markers?', 'ut_shortcodes' ),
                            'param_name'        => 'animation_style_marker',
                            'group'             => 'Animation',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'none',
                                esc_html__( 'Fade In'  , 'ut_shortcodes' ) => 'fadeIn',
                                //esc_html__( 'Zoom In'  , 'ut_shortcodes' ) => 'zoomIn',
                            ),
                            'dependency'        => array(
                                'element' => 'animate',
                                'value'   => 'true'
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Set delay until Event Animation starts?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'This timer allows you to delay the entire animation process of the timeline.', 'ut_shortcodes' ),
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
                            'description'       => esc_html__( 'Time in milliseconds until the timeline animation should start. e.g. 200', 'ut_shortcodes' ),
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
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Delay Timer Events', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Time in milliseconds until the next event appears. default 200', 'ut_shortcodes' ),
                            'param_name'        => 'delay_timer',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency' => array(
                                'element' => 'animate',
                                'value'   => array( 'true' ),
                            ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Delay Timer Markers', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Time in milliseconds until the next event appears. default 100', 'ut_shortcodes' ),
                            'param_name'        => 'delay_timer_marker',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency' => array(
                                'element' => 'animate',
                                'value'   => array( 'true' ),
                            ),
                        ),


                        // Pulse 1
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 1 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_1',
                            'group'             => 'Pulse Effect',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 1 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_1',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 1 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_1',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                        ),

                        // Pulse 2
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 2 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_2',
                            'group'             => 'Pulse Effect',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 2 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_2',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 2 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_2',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                        ),

                        // Pulse 3
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 3 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_3',
                            'group'             => 'Pulse Effect',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 3 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_3',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 3 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_3',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                        ),

                        // Pulse 4
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 4 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_4',
                            'group'             => 'Pulse Effect',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 4 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_4',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 4 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_4',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                        ),

                    )

                )

            ); /* end mapping */
                

        
        }

	    function timeline_settings_json( $atts ) {

		    /**
		     * @var $animate
		     * @var $delay_animation
		     * @var $global_delay_animation
		     * @var $delay_timer
		     * @var $delay_timer_marker
		     * @var $animation_style_marker
		     * @var $global_delay_timer
		     */

		    extract( shortcode_atts( array (
			    'animate'                => 'true',
			    'delay_animation'        => 'true',
			    'global_delay_animation' => 'false',
			    'delay_timer'            => 200,
			    'delay_timer_marker'     => 100,
			    'animation_style_marker' => 'none',
			    'global_delay_timer'     => 200,
		    ), $atts ) );

		    $json = array(

                'animate'                => filter_var( $animate, FILTER_VALIDATE_BOOLEAN ),
			    'delay_animation'        => filter_var( $delay_animation, FILTER_VALIDATE_BOOLEAN ),
			    'global_delay_animation' => filter_var( $global_delay_animation, FILTER_VALIDATE_BOOLEAN ),
			    'delay_timer'            => $delay_timer,
			    'delay_timer_marker'     => $delay_timer_marker,
			    'animation_style_marker' => $animation_style_marker,
			    'global_delay_timer'     => $global_delay_timer

		    );

		    return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

	    }

    
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (                
                'events'                            => '',                
                'time_line_color'                   => '',
                'time_line_dot_color'               => '',
                'time_line_dot_inner_color'         => '',
                'time_line_dot_large_color'         => '',
                'time_line_dot_large_inner_color'   => '',
                'time_line_event_date_color'        => '',
                'time_line_event_subheadline_color' => '',
                'time_line_event_text_color'        => '',
                
				// extra spacing
				'subtitle_margin_bottom'			=> '',
				
                // icon 1 pulsate
                'pulse_color_1'         => '',
                'pulse_style_width_1'   => '',
                'pulse_style_1'         => 'solid',
                
                // icon 2 pulsate
                'pulse_color_2'         => '',
                'pulse_style_width_2'   => '',
                'pulse_style_2'         => 'solid',
                
                // icon 3 pulsate
                'pulse_color_3'         => '',
                'pulse_style_width_3'   => '',
                'pulse_style_3'         => 'solid',
                
                // icon 4 pulsate
                'pulse_color_4'         => '',
                'pulse_style_width_4'   => '',
                'pulse_style_4'         => 'solid',
                
                'animate'                           => 'false',
                'animate_once'                      => 'yes',
                'animate_mobile'                    => false,
                'animate_tablet'                    => false,
                'animation_style_left'              => 'fadeInLeft',
                'animation_style_right'             => 'fadeInRight',
                'animation_style_marker'            => 'none',
                'class'                             => '',
                'css'                               => ''  
            ), $atts ) ); 
            
            // classes
            $classes   = array();
            $classes[] = $class;
            
            // Animation
            $animation_classes = array();
            $attributes = array();
            
            if( $animate == 'true' ) {
                
                $attributes['data-animateonce']     = esc_attr( $animate_once );
                
                $animation_classes[]     = 'ut-animate-simple-time-line-event';
                $animation_classes[]     = 'animated';
                
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
            
            // attributes string
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );
            
            
            $dot_animation_classes = array();
            $dot_attributes = array();
            
            if( $animation_style_marker != 'none' ) {
                
                $dot_attributes['data-animateonce'] = esc_attr( $animate_once );
                $dot_attributes['data-effect'] = $animation_style_marker;
                $dot_attributes['data-delay'] = '100';
                
                $dot_animation_classes[] = 'animated';
                
                if( !$animate_tablet ) {
                    $dot_animation_classes[] = 'ut-no-animation-tablet';
                }
                
                if( !$animate_mobile ) {
                    $dot_animation_classes[] = 'ut-no-animation-mobile';
                }
                
                if( $animate_once == 'infinite' ) {
                    $dot_animation_classes[] = 'infinite';
                }
                
            }
            
            // attributes string
            $dot_attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $dot_attributes,
                array_keys( $dot_attributes )
            ) );
            
            // unique ID
            $id = uniqid("ut_simple_time_line_");
            
            // css styles
            $css_style = '';

			// extra spacing
			if( $subtitle_margin_bottom != '' ) {
				$css_style .= '#' . $id . ' .ut-simple-time-line-event-subtitle { margin-bottom: ' . $subtitle_margin_bottom . 'px; }';
			}
			
			// colors
            if( $time_line_color ) {
                $css_style .= '#' . $id . '::before { border-color: ' . $time_line_color . '; }';
            }
            
            if( $time_line_dot_color ) {
                $css_style .= '#' . $id . ' .ut-simple-time-line-event-marker-small { border-color: ' . $time_line_dot_color . '; }';
            }
            
			if( ut_is_gradient( $time_line_dot_inner_color ) ) {
                
                $css_style .= ut_create_gradient_css( $time_line_dot_inner_color, '#' . $id . ' .ut-simple-time-line-event-marker-small', false, 'background' );

            } elseif( $time_line_dot_inner_color ) {

                $css_style .= '#' . $id . ' .ut-simple-time-line-event-marker-small { background: ' . $time_line_dot_inner_color . '; }';

            }
			
            if( $time_line_dot_large_color ) {
                $css_style .= '#' . $id . ' .ut-simple-time-line-event-marker-large { border-color: ' . $time_line_dot_large_color . '; }';
            }
            
			if( ut_is_gradient( $time_line_dot_large_inner_color ) ) {
                
                $css_style .= ut_create_gradient_css( $time_line_dot_large_inner_color, '#' . $id . ' .ut-simple-time-line-event-marker-large', false, 'background' );

            } elseif( $time_line_dot_large_inner_color ) {

                $css_style .= '#' . $id . ' .ut-simple-time-line-event-marker-large { background: ' . $time_line_dot_large_inner_color . '; }';

            }
			
            if( $time_line_event_date_color ) {
                $css_style .= '#' . $id . ' .ut-simple-time-line-event-title { color: ' . $time_line_event_date_color . '; }';
            }
            
            if( $time_line_event_subheadline_color ) {
                $css_style .= '#' . $id . ' .ut-simple-time-line-event-subtitle { color: ' . $time_line_event_subheadline_color . '; }';
            }
            
            if( $time_line_event_text_color ) {
                $css_style .= '#' . $id . ' .ut-simple-time-line-event-text { color: ' . $time_line_event_text_color . '; }';
            }
            
            if( $animate == 'true' ) {
                $css_style .= '#' . $id . ' .ut-simple-time-line-event-animation { opacity:0; }';                
            }
            
            if( $animate == 'true' && $animation_style_marker != 'none' ) {
                $css_style .= '#' . $id . ' .ut-simple-time-line-event-marker { opacity:0; }';                
            }
            
            for( $x = 1; $x <= 4; $x++ ) {
                        
                $current_pulse_color = ${"pulse_color_" . $x} ? ${"pulse_color_" . $x} : $time_line_dot_large_color;

                if( $current_pulse_color ) {

                    $css_style .= '#' . $id . ' .ut-marker-large-animation-' . $x . ' { border-color: ' . $current_pulse_color . '; }';

                }

                if( ${"pulse_style_width_" . $x} ) {

                    $css_style .= '#' . $id . ' .ut-marker-large-animation-' . $x . ' { border-width: ' . ${"pulse_style_width_" . $x} . 'px; }';

                }

                if( ${"pulse_style_" . $x} ) {

                    $css_style .= '#' . $id . ' .ut-marker-large-animation-' . $x . ' { border-style: ' . ${"pulse_style_" . $x} . '; }';

                }
               
            }
            
            // extract items
            if( function_exists('vc_param_group_parse_atts') && !empty( $events ) ) {
                $events = vc_param_group_parse_atts( $events );
            }
            
            // start output
            $output = '';
            
            // attach CSS
            if( !empty( $css_style ) ) {                
                $output .= '<style type="text/css">' . $css_style . '</style>';
            }

            $output .= '<div id="' . esc_attr( $id ) . '" class="ut-simple-time-line-wrap" data-settings="' . $this->timeline_settings_json( $atts ) . '">';
            
                $output .= '<div class="ut-simple-time-line ' . implode( " ", $classes ) . ' clearfix">';

                    foreach( $events as $key => $event ) {
                        
                        $data_effect = '';
                        
                        if( $animate == 'true' ) {
                        
                            if($key % 2 == 0) {
                                
                                $data_effect = 'data-effect="' . $animation_style_left . '"';

                            } else {
                                
                                $data_effect = 'data-effect="' . $animation_style_right . '"';
                                
                            }
                            
                        }
                        
                        $marker_size = !empty( $event['marker_size'] ) ? $event['marker_size'] : 'small';
                        
                        $output .= '<div class="ut-simple-time-line-event">';
                            
                            if( $marker_size == 'small' ) {
                                
                                $output .= '<div ' . $dot_attributes . ' class="ut-simple-time-line-event-marker ut-simple-time-line-event-marker-small ' . implode( " ", $dot_animation_classes ) . '"></div>';
                                
                            } else {
                                
                                $output .= '<div ' . $dot_attributes . ' class="ut-simple-time-line-event-marker ut-simple-time-line-event-marker-large ' . implode( " ", $dot_animation_classes ) . '">';
                                
                                    if( !empty( $event['marker_pulse'] ) && $event['marker_pulse'] == 'on' ) {
                                    
                                        $output .= '<div class="ut-marker-large-animation-1 ut-marker-animation-on"></div>';
                                        $output .= '<div class="ut-marker-large-animation-2 ut-marker-animation-on"></div>';
                                        $output .= '<div class="ut-marker-large-animation-3 ut-marker-animation-on"></div>';
                                        $output .= '<div class="ut-marker-large-animation-4 ut-marker-animation-on"></div>';
                                
                                    }
                                
                                $output .= '</div>';
                                
                            }
                        
                            if( $animate == 'true' ) {
                        
                                $output .= '<div ' . $attributes . ' ' . $data_effect . ' class="ut-simple-time-line-event-animation ' . implode( ' ', $animation_classes ) . '">';
                        
                            }

                            if( !empty( $event['image'] ) ) {

                                $output .= '<div class="ut-simple-time-line-event-image">';

                                    $thumbnail = wp_get_attachment_image_src( $event['image'], 'medium' );

                                    $output .= '<img class="ut-lazy" src="#" data-src="' . esc_url( $thumbnail[0] ) . '">';

                                $output .= '</div>';

                            }

                            if( !empty( $event['title'] ) ) {
                                
                                $output .= '<div class="ut-simple-time-line-event-title">';
                                
                                    $output .= $event['title'];
                                
                                $output .= '</div>';

                            }

                            if( !empty( $event['subtitle'] ) ) {

                                $output .= '<h3 class="ut-simple-time-line-event-subtitle">';
                                
                                    $output .= $event['subtitle'];
                                
                                $output .= '</h3>';

                            }

                            if( !empty( $event['text'] ) ) {

                                $output .= '<div class="ut-simple-time-line-event-text">';

                                    $output .= '<p>' . nl2br( $event['text'] ) . '</p>';
								
                                $output .= '</div>';

                            }
                            
                            if( $animate == 'true' ) {
                        
                                $output .= '</div>';
                        
                            }
                                
                        $output .= '</div>';

                    }

                $output .= '</div>';
                
                $output .= '<div class="clear"></div>';
            
            $output .= '</div>';
                
            return $output;
        
        }
            
    }

}

new UT_Time_line;