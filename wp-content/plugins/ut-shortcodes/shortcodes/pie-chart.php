<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Pie_Chart' ) ) {
	
    class UT_Pie_Chart {
        
        private $shortcode;

        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_pie_chart';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );

            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Pie Chart', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Striking Pir Charts for a perfect presentation.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-pie-chart ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/pie.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        // Pie Chart Type
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Type', 'unitedthemes' ),
                            'param_name'        => 'type',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Pie' , 'ut_shortcodes' )    => 'pie',
                                esc_html__( 'Doughnut' , 'ut_shortcodes' )   => 'doughnut',
                            ),

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Chart Box Maximum Width', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'The Chart Box includes the Chart itself as well as an optional legend.', 'ut_shortcodes' ),
                            'param_name'        => 'chart_max_width',
                            'group'             => 'General',
                            'value'             => array(
                                'default'   => '100',
                                'min'       => '1',
                                'max'       => '100',
                                'step'      => '1',
                                'unit'      => '%'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Chart Box Alignment', 'unitedthemes' ),
                            'description'       => esc_html__( 'The Chart Box includes the Chart itself as well as an optional legend.', 'ut_shortcodes' ),
                            'param_name'        => 'chart_align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'left' , 'ut_shortcodes' )   => 'left',
                                esc_html__( 'center' , 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right' , 'ut_shortcodes' )  => 'right'
                            ),
                        ),

                        // Pie Chart Slices
                        array(
                            'type'          => 'param_group',
                            'heading'       => esc_html__( 'Pie Chart Slices', 'ut_shortcodes' ),
                            'group'         => 'General',
                            'param_name'    => 'slices',
                            'params'        => array(

                                array(
                                    'type'              => 'textfield',
                                    'heading'           => esc_html__( 'Slice Label', 'ut_shortcodes' ),
                                    'description'       => esc_html__( '(required)', 'ut_shortcodes' ),
                                    'param_name'        => 'slice_label',
                                    'admin_label'       => true,
                                ),
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Slice Color', 'ut_shortcodes' ),
                                    'edit_field_class'  => 'vc_col-sm-6',
                                    'param_name'        => 'slice_color',
                                ),
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Slice Border Color', 'ut_shortcodes' ),
                                    'edit_field_class'  => 'vc_col-sm-6',
                                    'param_name'        => 'slice_border_color',
                                ),

                                // Label Settings
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Designation Label Color', 'ut_shortcodes' ),
                                    'param_name'        => 'slice_label_color',
                                ),

                                array(
                                    'type'              => 'range_slider',
                                    'heading'           => esc_html__( 'Slice Value (Share)', 'ut_shortcodes' ),
                                    'description'       => esc_html__( 'Based on the total value of all slices, the pie chart will automatically calculate the precentage value of this slice.', 'ut_shortcodes' ),
                                    'param_name'        => 'slice_size',
                                    'value'             => array(
                                        'default'   => '50',
                                        'min'       => '1',
                                        'max'       => '1000',
                                        'step'      => '1',
                                        'unit'      => ''
                                    ),
                                )
                            )

                        ),

                        // Legend Settings
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Display Legend?', 'unitedthemes' ),
                            'param_name'        => 'display_legend',
                            'group'             => 'Legend',
                            'value'             => array(
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false'
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Legend Position', 'unitedthemes' ),
                            'param_name'        => 'legend_position',
                            'group'             => 'Legend',
                            'value'             => array(
                                esc_html__( 'top left' , 'ut_shortcodes' )      => 'top-left',
                                esc_html__( 'top center' , 'ut_shortcodes' )    => 'top-center',
                                esc_html__( 'top right' , 'ut_shortcodes' )     => 'top-right',
                                esc_html__( 'left' , 'ut_shortcodes' )          => 'left',
                                esc_html__( 'right' , 'ut_shortcodes' )         => 'right',
                                esc_html__( 'bottom left' , 'ut_shortcodes' )   => 'bottom-left',
                                esc_html__( 'bottom center' , 'ut_shortcodes' ) => 'bottom-center',
                                esc_html__( 'bottom right' , 'ut_shortcodes' )  => 'bottom-right'
                            ),
                            'dependency' => array(
                                'element' => 'display_legend',
                                'value'   => array( 'true' ),
                            ),

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Legend to Chart Spacing', 'ut_shortcodes' ),
                            'description'       => esc_html__( '', 'ut_shortcodes' ),
                            'param_name'        => 'legend_spacing',
                            'group'             => 'Legend',
                            'value'             => array(
                                'default'   => '40',
                                'min'       => '0',
                                'max'       => '200',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency' => array(
                                'element' => 'display_legend',
                                'value'   => array( 'true' ),
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Legend Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'legend_font_size',
                            'group'             => 'Legend',
                            'value'             => array(
                                'default'   => '12',
                                'min'       => '10',
                                'max'       => '24',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency' => array(
                                'element' => 'display_legend',
                                'value'   => array( 'true' ),
                            ),

                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Legend Font Color', 'ut_shortcodes' ),
                            'param_name'        => 'legend_font_color',
                            'group'             => 'Legend',
                            'dependency' => array(
                                'element' => 'display_legend',
                                'value'   => array( 'true' ),
                            ),
                        ),

                        // Labelling
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Designation Label on Slice?', 'unitedthemes' ),
                            'param_name'        => 'designation',
                            'group'             => 'Labelling',
                            'value'             => array(
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false'
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Designation Label Type', 'unitedthemes' ),
                            'param_name'        => 'designation_type',
                            'group'             => 'Labelling',
                            'value'             => array(
                                esc_html__( 'Percentage' , 'ut_shortcodes' ) => 'percentage',
                                esc_html__( 'Value' , 'ut_shortcodes' ) => 'value',
                                esc_html__( 'Label' , 'ut_shortcodes' ) => 'label',
                            ),
                            'dependency' => array(
                                'element' => 'designation',
                                'value'   => array( 'true' ),
                            ),

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Designation Label Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'designation_label_font_size',
                            'group'             => 'Labelling',
                            'value'             => array(
                                'default'   => '12',
                                'min'       => '10',
                                'max'       => '24',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency' => array(
                                'element' => 'designation',
                                'value'   => array( 'true' ),
                            ),

                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Designation Label Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'designation_label_font_weight',
                            'group'             => 'Labelling',
                            'value'             => array(
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )   => 'bold'
                            ),
                            'dependency' => array(
                                'element' => 'designation',
                                'value'   => array( 'true' ),
                            ),
                        ),


                        // Tooltips
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Tooltips on Hover?', 'unitedthemes' ),
                            'param_name'        => 'tooltips',
                            'group'             => 'Tooltips',
                            'value'             => array(
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false'
                            )
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Tooltip Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'tooltip_text_color',
                            'group'             => 'Tooltips',
                            'dependency' => array(
                                'element' => 'tooltips',
                                'value'   => array( 'true' ),
                            ),
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Tooltip Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'tooltip_background_color',
                            'group'             => 'Tooltips',
                            'dependency' => array(
                                'element' => 'tooltips',
                                'value'   => array( 'true' ),
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Tooltip Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'tooltip_font_size',
                            'group'             => 'Tooltips',
                            'value'             => array(
                                'default'   => '12',
                                'min'       => '10',
                                'max'       => '24',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency' => array(
                                'element' => 'tooltips',
                                'value'   => array( 'true' ),
                            ),

                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Tooltip Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'tooltip_font_weight',
                            'group'             => 'Tooltips',
                            'value'             => array(
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )   => 'bold'
                            ),
                            'dependency' => array(
                                'element' => 'tooltips',
                                'value'   => array( 'true' ),
                            ),
                        ),

                        // Global Pie Chart Colors
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Default Slice Color', 'ut_shortcodes' ),
                            'param_name'        => 'slice_color',
                            'group'             => 'Default Slice Styling',
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Default Slice Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'slice_border_color',
                            'group'             => 'Default Slice Styling',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Slice Border Size', 'ut_shortcodes' ),
                            'param_name'        => 'slice_border_width',
                            'group'             => 'Default Slice Styling',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '0',
                                'max'       => '20',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),

                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Default Designation Label Color', 'ut_shortcodes' ),
                            'param_name'        => 'slice_label_color',
                            'group'             => 'Default Slice Styling',
                        ),





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

                    )

                )

            ); /* end mapping */
                

        
        }

	    function pie_settings_json( $id, $slice_series, $atts ) {

		    /**
		     * @var string $type
		     * @var string $designation
		     * @var string $designation_type
		     * @var string $designation_label_font_weight
		     * @var string $designation_label_font_size
		     * @var string $tooltips
		     * @var string $tooltip_text_color
		     * @var string $tooltip_background_color
		     * @var string $tooltip_font_size
		     * @var string $tooltip_font_weight
		     * @var string $display_legend
		     * @var string $legend_position
		     * @var string $legend_font_size
		     * @var string $legend_font_color
		     * @var string $slice_border_width
		     */

		    extract( shortcode_atts( array (
			    // Chart Type
			    'type'                          => 'pie',

			    // Slice Label
			    'designation'                   => 'true',
			    'designation_type'              => 'percentage',
			    'designation_label_font_weight' => 'normal',
			    'designation_label_font_size'   => '12',

			    // Tooltips
			    'tooltips'                      => 'true',
			    'tooltip_text_color'            => '#FFF',
			    'tooltip_background_color'      => 'rgba(0,0,0,0.8)',
			    'tooltip_font_size'             => '12',
			    'tooltip_font_weight'           => 'normal',

			    // Legend Settings
			    'display_legend'                => 'true',
			    'legend_position'               => 'top',
			    'legend_font_size'              => '12',
			    'legend_font_color'             => '',

			    // Slice Styling
			    'slice_border_width'            => '1'
		    ), $atts ) );

		    if( empty( $legend_font_color ) && function_exists('ot_get_option') ) {

			    $legend_font_color = ot_get_option('ut_body_font_color');

		    }

		    $json = array(
                'type' => $type,
                'data' => array(
                    'labels'   => $slice_series['labels'],
                    'datasets' => array( array(
                        'data'              => $slice_series['data'],
                        'backgroundColor'   => $slice_series['colors'],
                        'borderColor'       => $slice_series['borders'],
                        'borderWidth'       => $slice_border_width
                    ) ),
                ),
                'options' => array(
	                'legend' => array(
		                'display'  => false,
		                'position' => $legend_position,
		                'labels'   => array(
			                'padding'   => 20,
			                'boxWidth'  => 30,
			                'fontSize'  => $legend_font_size,
			                'fontColor' => $legend_font_color
		                )
	                ),
	                'tooltips' => array(
		                'enabled'           => filter_var( $tooltips, FILTER_VALIDATE_BOOLEAN ),
		                'xPadding'          => 8,
		                'yPadding'          => 8,
		                'caretPadding'      => 20,
		                'displayColors'     => false,
		                'bodyFontColor'     => $tooltip_text_color,
		                'backgroundColor'   => $tooltip_background_color,
		                'bodyFontSize'      => $tooltip_font_size,
		                'bodyFontStyle'     => $tooltip_font_weight,
	                ),
	                'plugins' => array(
		                'deferred' => array(
			                'yOffset' => '25%',
			                'delay'   => 500
		                )
	                )
                )

		    );

		    if( $designation == 'true' ) {

			    $json['options']['pieceLabel'] = array(
                    'render'    => $designation_type,
                    'fontColor' => $slice_series['label_colors'],
                    'fontStyle' => $designation_label_font_weight,
                    'fontSize'  => $designation_label_font_size
                );

            }

		    return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

	    }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'slices'                => '',
                'chart_max_width'       => '100',                
                'chart_align'           => 'left',
                
                // Colors 
                'slice_color'           => get_option('ut_accentcolor' , '#1867C1'),
                'slice_border_color'    => get_option('ut_accentcolor' , '#1867C1'),
                'slice_label_color'     => '#FFF',
                
                // Legend
                'display_legend'        => 'true',
                'legend_position'       => 'top-left',
                'legend_spacing'        => '',
                'legend_font_size'      => '12',
                'legend_font_color'     => '',
                
                // Design Options
                'class'                 => '',
                'css'                   => ''                
            ), $atts ) ); 

            // classes
            $classes   = array();
            $classes[] = $class;
            
            // Class Chart Position
            $classes[] = 'ut-pie-chart-wrap-align-' . $chart_align;            
            
            // Class Legend Position
            if( $display_legend == 'true' ) {
                
                if( strpos( $legend_position, 'top') !== false ) {
                    $classes[] = 'ut-pie-chart-with-legend-top';
                }
                
                if( strpos( $legend_position, 'bottom') !== false ) {
                    $classes[] = 'ut-pie-chart-with-legend-bottom';
                }
                
                $classes[] = 'ut-pie-chart-legend-' .  $legend_position;
                
            }
            
            
            // unique item id
            $wrap_id   = uniqid("ut_pie_chart_wrap_");
            $canvas_id = uniqid("ut_pie_chart_");           
            $legend_id = uniqid("ut_pie_chart_legend_");
            
            // extract slices
            if( function_exists('vc_param_group_parse_atts') && !empty( $slices ) ) {
                $slices = vc_param_group_parse_atts( $slices );
            }
            
            // Custom CSS
            $css_style = '';
            
            // Pie Chart Max Width
            if( $chart_max_width ) {
                
                if( $legend_position == 'left' || $legend_position == 'right' ) {
                    
                    $css_style .= '#' . $wrap_id . ' .ut-pie-chart-container { max-width: calc(' . $chart_max_width . '% - ' . ( $legend_spacing ? $legend_spacing : '40' ) . 'px); }';
                    $css_style .= '#' . $legend_id . ' { max-width: ' . ( 100 - $chart_max_width ) . '%; }';
                    
                } else {
                    
                    $css_style .= '#' . $wrap_id . ' .ut-pie-chart-container { max-width: ' . $chart_max_width . '%; }';
                    
                }
                
            }
            
            // Legend CSS
            if( $legend_font_size ) {
                $css_style .= '#' . $legend_id . '{ font-size: ' . $legend_font_size . 'px; }';
            }
            
            if( $legend_font_color ) {
                $css_style .= '#' . $legend_id . '{ color: ' . $legend_font_color . '; }';
            }

            if( $legend_spacing ) {
                
                if( strpos( $legend_position, 'top') !== false ) {
                    
                    $css_style .= '#' . $legend_id . '{ margin-bottom: ' . $legend_spacing . 'px; }';
                    
                } elseif( strpos( $legend_position, 'bottom') !== false ) {
                    
                    $css_style .= '#' . $legend_id . '{ margin-top: ' . $legend_spacing . 'px; }';
                    
                } elseif( $legend_position == 'left' ) {
                    
                    $css_style .= '#' . $legend_id . '{ margin-right: ' . $legend_spacing . 'px; }';
                    
                } elseif( $legend_position == 'right' ) {
                    
                    $css_style .= '#' . $legend_id . '{ margin-left: ' . $legend_spacing . 'px; }';
                    
                }
                
            }
            
            // final array for all slices
            $slice_series = array();
            
            // create slices
            foreach( $slices as $key => $slice ) {
                                
                // assign labels
				$slice_series['labels'][] = !empty( $slice["slice_label"] ) ? $slice["slice_label"] : '';
				
                // slice background colors
                if( !empty( $slice['slice_color'] ) ) {
                    
                    $slice_series['colors'][] = $slice['slice_color'];
                    
                } else {
                    
                    $slice_series['colors'][] = 'rgba(' . ut_hex_to_rgb( $slice_color ) . ', 0.8 )';
                    
                }
                
                // slice border colors
                if( !empty( $slice['slice_border_color'] ) ) {
                    
                    $slice_series['borders'][] = $slice['slice_border_color'];
                    
                } else {
                    
                    $slice_series['borders'][] = 'rgba(' . ut_hex_to_rgb( $slice_border_color ) . ', 1 )';
                    
                }
                
                // slice label color
                if( !empty( $slice['slice_label_color'] ) ) {
                    
                    $slice_series['label_colors'][] = $slice['slice_label_color'];
                    
                } else {
                    
                    $slice_series['label_colors'][] = $slice_label_color;
                    
                }
                
                // slice data
                $slice_series['data'][] = $slice['slice_size'];
                
            }
            
            // start output
            $output = '';

            // attach CSS
            if( !empty( $css_style ) ) {                
                $output .= '<style type="text/css">' . $css_style . '</style>';
            }

            // chart wrap
            $output .= '<div id="' . esc_attr( $wrap_id ) . '" class="ut-pie-chart-wrap ' . implode( " ", $classes ) . '">';
                
                if( $display_legend == 'true' ) {
            
                    // legend
                    $output .= '<div id="' . esc_attr( $legend_id ) . '" class="ut-pie-chart-legend">';
                    
                        $output .= '<ul>';
                    
                        foreach( $slices as $key => $slice ) {

                            $output .= '<li class="ut-pie-chart-legend-item ut-pie-chart-legend-item-' . $key . '">';

                                // legend indicator
                                $background_color = !empty( $slice['slice_color'] ) ? $slice['slice_color'] : 'rgba(' . ut_hex_to_rgb( $slice_color ) . ', 0.8 )';

                                $output .= '<span style="background-color: ' . $background_color . ';"></span>';
                                
								if( isset( $slice["slice_label"] ) ) {
									$output .= $slice["slice_label"];
								}
									
                            $output .= '</li>';                            

                        }
                        
                        $output .= '</ul>';
                    
                    $output .= '</div>';
                
                }
            
                // attach canvas
                $output .= '<div class="ut-pie-chart-container">';
                    $output .= '<canvas id="' . esc_attr( $canvas_id ) . '" class="ut-pie-chart" data-settings="' . $this->pie_settings_json( $canvas_id, $slice_series, $atts ) . '" width="400" height="400"></canvas>';
                $output .= '</div>';    
            
            $output .= '</div>';
            
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
           
            return $output;
        
        }
            
    }

}

new UT_Pie_Chart;