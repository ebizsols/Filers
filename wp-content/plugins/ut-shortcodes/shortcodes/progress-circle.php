<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Progress_Cirle' ) ) {
	
    class UT_Progress_Cirle {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_progress_circle';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Progress Circle', 'ut_shortcodes' ),
                    'description' 	  => esc_html__( 'Circle counters are an ultimate way for displaying varying types of data in a beautiful manner.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-circle-o-notch ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/circle-counter.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Circle Style', 'ut_shortcodes' ),
                            'param_name'        => 'circle_style',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Cirle with title and percent', 'ut_shortcodes' ) => 'one',
                                esc_html__( 'Cirle with icon and title', 'ut_shortcodes' )    => 'two',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Percentage', 'ut_shortcodes' ),
                            'param_name'        => 'percent',
                            'group'             => 'General',
                            'value'             => array(
                                'default' => '80',
                                'min'     => '1',
                                'max'     => '100',
                                'step'    => '1',
                                'unit'    => '%'
                            ),

                        ),
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
                                esc_html__( 'Orion Icons (with animated draw)', 'ut_shortcodes' ) => 'orionicons'
                            ),
                            'dependency'        => array(
                                'element' => 'circle_style',
                                'value'   => array( 'two' ),
                            )

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
                            'description'       => esc_html__( 'recommended size 120x120', 'ut_shortcodes' ),
                            'param_name'        => 'imageicon',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'circle_style',
                                'value'   => array( 'two' ),
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Percent Value in Circle?', 'ut_shortcodes' ),
                            'param_name'        => 'show_percent',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'yes' , 'ut_shortcodes' )   => 'yes',
                                esc_html__( 'no' , 'ut_shortcodes' )    => 'no'
                            ),
                            'dependency'        => array(
                                'element' => 'circle_style',
                                'value'   => array( 'one' ),
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Percent Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'percent_font_weight',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' )             => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )               => 'bold'
                            ),
                            'dependency'        => array(
                                'element' => 'show_percent',
                                'value'   => array( 'yes' ),
                            )
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Title', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'param_name'        => 'title',
                            'group'             => 'General',
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Apply Theme Font Face', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'By using this option, the title will use the global section headline font.', 'ut_shortcodes' ),
                            'param_name'        => 'title_text_font',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Title Transform', 'ut_shortcodes' ),
                            'param_name'        => 'title_text_transform',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' )            => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' )              => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' )              => 'lowercase'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Title Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'title_font_weight',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' )             => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )               => 'bold'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Stroke Width', 'ut_shortcodes' ),
                            'param_name'        => 'stroke_width',
                            'group'             => 'General',
                            'value'             => array(
                                'default' => '10',
                                'min'     => '1',
                                'max'     => '20',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Once?', 'unitedthemes' ),
                            'description'       => esc_html__( 'Animate only once when reaching the viewport, animate everytime when reaching the viewport? By default the animation executes everytime when the element becomes visible in viewport, means when leaving the viewport the animation will be reseted and starts again when reaching the viewport again. By setting this option to yes, the animation executes exactly once.', 'unitedthemes' ),
                            'param_name'        => 'animate_once',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no' , 'unitedthemes' )      => 'no',
                                esc_html__( 'yes', 'unitedthemes' )      => 'yes',
                            )
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
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

                        // Colors
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Circle Color', 'ut_shortcodes' ),
                            'param_name'        => 'circle_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Circle Stroke Color', 'ut_shortcodes' ),
                            'param_name'        => 'stroke_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Title Color', 'ut_shortcodes' ),
                            'param_name'        => 'title_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Percentage Color', 'ut_shortcodes' ),
                            'param_name'        => 'percent_color',
                            'group'             => 'Colors',
                            'dependency'        => array(
                                'element' => 'circle_style',
                                'value'   => array( 'one' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
                            'description'       => 'Does not apply to custom icons!',
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
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),

                    )

                )

            ); /* end mapping */
                

        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'circle_style'          => 'one',
                'title'                 => '',
                'title_text_font'       => 'off',
                'title_font_weight'     => '',
                'title_text_transform'  => '',
                'percent'               => '80',
                'show_percent'          => 'yes',
                'title_color'           => '',
                'percent_color'         => '',
                'percent_font_weight'   => '',
                'animate_once'          => 'no',
                'circle_color'          => '#e5e5e5',
                'stroke_color'          => get_option('ut_accentcolor' , '#F1C40F'),
                'stroke_width'          => '',
                'icon_type'         	=> 'fontawesome',
				'icon'                  => '',
				'icon_bklyn'        	=> '',
				'icon_linea'			=> '',
				'icon_orion'			=> '',
				'icon_color'            => get_option('ut_accentcolor' , '#F1C40F'),
                'imageicon'             => '',
                'class'                 => '',
                'css'                   => ''
            ), $atts ) ); 
            
            
            /* class array */
            $classes = array();
            
            /* extra element class */
            $classes[] = $class;
            
            if( empty( $title ) ) {
                $classes[] = 'bkly-progress-circle-no-title';    
            }
            
            if( $show_percent == 'no' ) {
                $classes[] = 'bkly-progress-circle-no-percent';
            }
            
            /* icon setting */
            if( !empty( $imageicon ) && is_numeric( $imageicon ) ) {
                $imageicon = wp_get_attachment_url( $imageicon );                
            }            
            
            if( !empty( $title_text_font ) && $title_text_font == 'on' ) {
                $classes[] = 'bkly-progress-circle-theme-font';    
            }
            			
            /* overwrite default icon */
            $icon = empty( $imageicon ) ? $icon : $imageicon;
            
            /* check if icon is an image */
            $image_icon = strpos( $icon, '.png' ) !== false || strpos( $icon, '.jpg' ) !== false || strpos( $icon, '.gif' ) !== false || strpos( $icon, '.ico' ) !== false || strpos( $icon, '.svg' ) !== false ? true : false;
            
			/* font icon */
            if( !$image_icon ) {
            	
                if( $icon_type == 'bklynicons' && !empty( $icon_bklyn ) ) {
                    
                    $icon = $icon_bklyn;
                
				} elseif( $icon_type == 'lineaicons' && !empty( $icon_linea ) ) {
					
					$icon = $icon_linea;
					$classes[] = 'bkly-progress-circle-with-svg-icon';					
					
                } elseif( $icon_type == 'orionicons' && !empty( $icon_orion ) ) {
					
					$icon = $icon_orion;
					$classes[] = 'bkly-progress-circle-with-svg-icon';					
					
                } else {
					
                    /* fallback */
                    if( strpos( $icon, 'fa fa-' ) === false ) {
                        $icon = str_replace('fa-', 'fa fa-', $icon );     
                    } 
                                    
                }                 
                
            }
			
            /* inline css */
            $id = uniqid("ut_pc_");
            
            $css_style = '<style type="text/css">';
                    
                $css_style .= '#' . $id . ' circle.circle { stroke: ' . $circle_color . '; }';
                $css_style .= '#' . $id . ' circle.stroke { stroke: ' . $stroke_color . '; }';
                
                if( $stroke_width ) {
                    $css_style .= '#' . $id . ' circle.circle { stroke-width: ' . $stroke_width . 'px; }';
                    $css_style .= '#' . $id . ' circle.stroke { stroke-width: ' . ( $stroke_width + 1 ) . 'px; }';                    
                }    
            
                // percentage
                if( $percent_color ) {
                    $css_style .= '#' . $id . ' .bkly-progress-circle::after { color: ' . $percent_color . '; }';
                }
            
                if( $percent_font_weight ) {
                    $css_style .= '#' . $id . ' .bkly-progress-circle::after { font-weight: ' . $percent_font_weight . '; }';
                }
            
                // title
                if( $title_color ) {

                    $css_style .= '#' . $id . ' h3 { color: ' . $title_color . '; }';
                    $css_style .= '#' . $id . ' .bkly-progress-circle::before { color: ' . $title_color . '; }';

                }
            
                if( $title_text_transform ) {
                
                    $css_style .= '#' . $id . ' h3 { text-transform: ' . $title_text_transform . '; }';
                    $css_style .= '#' . $id . ' .bkly-progress-circle::before { text-transform: ' . $title_text_transform . '; }';

                }
            
                if( $title_font_weight ) {
                
                    $css_style .= '#' . $id . ' h3 { font-weight: ' . $title_font_weight . '; }';
                    $css_style .= '#' . $id . ' .bkly-progress-circle::before { font-weight: ' . $title_font_weight . '; }';

                }
            
                if( $circle_style == 'two' && !$image_icon ) {
                    
                    /* extra class */
                    if( $icon_type == 'fontawesome' || $icon_type == 'bklynicons' ) {
						$classes[] = 'bkly-progress-circle-with-fa-icon';
					}
						
                    /* icon color */
                    $css_style .= '#' . $id . ' .bkly-progress-circle-style-two.bkly-progress-circle-with-fa-icon i { color: ' . esc_attr( $icon_color ) . '; }';
                    
                } elseif( $circle_style == 'two' && $image_icon ) {
                    
                    /* extra class */
                    $classes[] = 'bkly-progress-circle-with-custom-icon';
                    
                    /* add image icon */
                    $css_style .= '#' . $id . ' .bkly-progress-circle-with-custom-icon::after { background-image: url(' . esc_url( $icon ) . '); }';
                
                }
                
            $css_style .= '</style>';
                
            /* start output */
            $output = '';
            
            /* add css */ 
            $output .= ut_minify_inline_css( $css_style );            
            
            $output .= '<div id="' . esc_attr( $id ) . '" class="bkly-progress-circle-wrap">';
            
                $output .= '<div class="bkly-progress-circle bkly-progress-circle-style-' . $circle_style . ' ' . implode(' ', $classes ) . '"  data-circle-percent="' . esc_attr( $percent )  . '" data-circle-text="' . esc_attr( $title ) . '">';

                    if( $circle_style == 'two' && !$image_icon ) {
                        
						if( $icon_type == 'lineaicons' || $icon_type == 'orionicons' ) {
								
							$svg = new UT_Draw_SVG( uniqid("ut-svg-"), $atts, $id );
							$output .= $svg->draw_svg_icon();
								
						} else {
						
							$output .= '<i class="' . esc_attr( $icon ) . '"></i>';
						
						}
						
                    }

                    $output .= '<svg class="bkly-progress-svg" data-animateonce="' . esc_attr( $animate_once ) . '">';
            
                        $output .= '<circle class="stroke" r="80" cx="90" cy="90" fill="transparent" stroke-dasharray="502.4" stroke-dashoffset="0"></circle>';
                        $output .= '<circle class="circle" r="80" cx="90" cy="90" fill="transparent" stroke-dasharray="502.4" stroke-dashoffset="0"></circle>';
                        
                    $output .= '</svg>';

                $output .= '</div>';
                
                if( $circle_style != 'one' && !empty( $title ) ) {

                    $output .= '<h3>' . $title . '</h3>';

                }
            
            $output .= '</div>';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
            
            return $output;
        
        }
            
    }

}

new UT_Progress_Cirle;