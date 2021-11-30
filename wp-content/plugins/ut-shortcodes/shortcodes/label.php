<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Label_Shortcode' ) ) {
	
    class UT_Label_Shortcode {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_label';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Labels', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Display a single label or a group of labels.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/label.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'          => 'param_group',
                            'heading'       => esc_html__( 'Labels', 'ut_shortcodes' ),
                            'group'         => 'General',
                            'param_name'    => 'labels',
                            'params' => array(

                                array(
                                    'type'              => 'textfield',
                                    'heading'           => esc_html__( 'Title Title', 'ut_shortcodes' ),
                                    'param_name'        => 'title',
                                    'admin_label'       => true,
                                ),
                                array(
                                    'type'              => 'dropdown',
                                    'heading'           => esc_html__( 'Add Link to Title?', 'ut_shortcodes' ),
                                    'param_name'        => 'link',
                                    'value'             => array(
                                        esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                        esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                                    ),
                                ),
                                array(
                                    'type'              => 'vc_link',
                                    'heading'           => esc_html__( 'Title Link', 'ut_shortcodes' ),
                                    'param_name'        => 'label_link',
                                    'dependency'    => array(
                                        'element' => 'link',
                                        'value'   => array( 'true' ),
                                    )
                                ),
                                array(
                                    'type'              => 'dropdown',
                                    'heading'           => esc_html__( 'Individually customize this label?', 'ut_shortcodes' ),
                                    'param_name'        => 'custom_design',
                                    'value'             => array(
                                        esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                        esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                                    ),
                                ),
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Label Title Color', 'ut_shortcodes' ),
                                    'param_name'        => 'title_color',
                                    'dependency'        => array(
                                        'element' => 'custom_design',
                                        'value'   => array( 'true' ),
                                    )
                                ),
                                array(
                                    'type'              => 'gradient_picker',
                                    'heading'           => esc_html__( 'Label Background Color', 'ut_shortcodes' ),
                                    'param_name'        => 'label_background_color',
                                    'group'             => 'Label Design',
                                    'dependency'        => array(
                                        'element' => 'custom_design',
                                        'value'   => array( 'true' ),
                                    )
                                ),
                                array(
                                    'type'              => 'colorpicker',
                                    'heading'           => esc_html__( 'Label Title Hover Color', 'ut_shortcodes' ),
                                    'description'       => esc_html__( 'only available for label with link.', 'ut_shortcodes' ),
                                    'param_name'        => 'title_color_hover',
                                    'dependency'        => array(
                                        'element' => 'custom_design',
                                        'value'   => array( 'true' ),
                                    )
                                ),
                                array(
                                    'type'              => 'gradient_picker',
                                    'heading'           => esc_html__( 'Label Background Hover Color', 'ut_shortcodes' ),
                                    'description'       => esc_html__( 'only available for label with link.', 'ut_shortcodes' ),
                                    'param_name'        => 'label_background_color_hover',
                                    'dependency'        => array(
                                        'element' => 'custom_design',
                                        'value'   => array( 'true' ),
                                    )
                                ),

                            ),

                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Label Display Style', 'ut_shortcodes' ),
                            'param_name'        => 'list_style',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'inline'  , 'ut_shortcodes' ) => 'inline',
                                esc_html__( 'stack'  , 'ut_shortcodes' ) => 'stack',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Label Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                        // Label Design
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Label Title Color', 'ut_shortcodes' ),
                            'param_name'        => 'title_color',
                            'group'             => 'Default Label Design'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Label Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'label_background_color',
                            'group'             => 'Default Label Design'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Label Title Hover Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'only available for labels with active links.', 'ut_shortcodes' ),
                            'param_name'        => 'title_color_hover',
                            'group'             => 'Default Label Design',
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Label Background Hover Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'only available for labels with active links.', 'ut_shortcodes' ),
                            'param_name'        => 'label_background_color_hover',
                            'group'             => 'Default Label Design',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Label Border Radius', 'ut_shortcodes' ),
                            'param_name'        => 'label_border_radius',
                            'value'             => array(
                                'default'   => '4',
                                'min'       => '0',
                                'max'       => '150',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'group'             => 'Default Label Design'
                        ),

                        // Label Spacing
                        array(
                            'type'              => 'ut_css_editor',
                            'heading'           => esc_html__( 'Label Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'label_spacing',
                            'group'             => 'Label Spacing'
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Label Group Spacing Bottom', 'ut_shortcodes' ),
                            'param_name'        => 'label_group_spacing',
                            'value'             => array(
                                'default'   => '35',
                                'min'       => '0',
                                'max'       => '100',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'group'             => 'Label Spacing'
                        ),

                        // Label Font Settings
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'font_source',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => 'default',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google'
                            ),
                        ),

                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Font Settings',
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
                            'group'             => 'Font Settings',
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
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default' => '12',
                                'min'     => '8',
                                'max'     => '50',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'line_height',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '100',
                                'min'       => '80',
                                'max'       => '300',
                                'step'      => '5',
                                'unit'      => '%'
                            ),

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'letter_spacing',
                            'group'             => 'Font Settings',
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
                            'heading'           => esc_html__( 'Title Text Transform', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional)' , 'ut_shortcodes' ),
                            'param_name'        => 'text_transform',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' )            => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' )              => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' )              => 'lowercase'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Please keep in mind, that the selected font needs to support the font weight.', 'ut_shortcodes' ),
                            'param_name'        => 'font_weight',
                            'group'             => 'Font Settings',
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
                                'value'             => array('websafe','default'),
                            ),
                        ),




                    )

                )

            ); /* end mapping */
                

        
        }
        
        function create_inline_css( $label, $single_id ) {
            
            $label_design = '';
            
            if( !empty( $label["custom_design"] ) && $label["custom_design"] == 'false' ) {
                return $label_design;
            }
            
            if( !empty( $label['title_color'] ) ) {
                $label_design .= '#' . $single_id . '.ut-label-module { color:' . $label['title_color'] . '; }';
            }

            if( !empty( $label['title_color_hover'] ) ) {
                $label_design .= '#' . $single_id . '.ut-label-module:hover { color:' . $label['title_color_hover'] . '; }';
                $label_design .= '#' . $single_id . '.ut-label-module:active { color:' . $label['title_color_hover'] . '; }';
                $label_design .= '#' . $single_id . '.ut-label-module:focus { color:' . $label['title_color_hover'] . '; }';
            }
            
            if( !empty( $label['label_background_color'] ) && ut_create_gradient_css( $label['label_background_color'] ) ) {
                    
                $label_design .= ut_create_gradient_css( $label['label_background_color'], '#' . $single_id . '.ut-label-module' );                    

            } elseif( !empty( $label['label_background_color'] ) ) {

                $label_design .= '#' . $single_id . '.ut-label-module { background:' . $label['label_background_color'] . '; }';                    

            } 
            
            if( !empty( $label['label_background_color_hover'] ) && ut_create_gradient_css( $label['label_background_color_hover'] ) ) {
                    
                $label_design.= ut_create_gradient_css( $label['label_background_color_hover'], '#' . $single_id . '.ut-label-module:hover' );
                $label_design.= ut_create_gradient_css( $label['label_background_color_hover'], '#' . $single_id . '.ut-label-module:active' );
                $label_design.= ut_create_gradient_css( $label['label_background_color_hover'], '#' . $single_id . '.ut-label-module:focus' );

            } elseif( !empty( $label['label_background_color_hover'] ) ) {

                $label_design .= '#' . $single_id . '.ut-label-module:hover { background:' . $label['label_background_color_hover'] . '; }';
                $label_design .= '#' . $single_id . '.ut-label-module:active { background:' . $label['label_background_color_hover'] . '; }';
                $label_design .= '#' . $single_id . '.ut-label-module:focus { background:' . $label['label_background_color_hover'] . '; }';

            }
            
            return $label_design;
            
        }        
        
        
        function create_label_link( $link ) {
            
            // option link settings
            $link_attributes = array();
            
            if( function_exists('vc_build_link') ) {
                
                $label_link = vc_build_link( $link );
                
                $link_attributes[] = !empty( $label_link['url'] )    ? 'href="' . esc_url( $label_link['url'] ) . '"' : '#';
                $link_attributes[] = !empty( $label_link['target'] ) ? 'target="' . esc_attr( $label_link['target'] ) . '"' : 'target="_self"';
                $link_attributes[] = !empty( $label_link['title'] )  ? 'title="' . esc_attr( $label_link['title'] ) . '"' : '';
                $link_attributes[] = !empty( $label_link['rel'] )    ? 'rel="' . esc_attr( trim( $label_link['rel'] ) ) . '"' : '';
                
            }
            
            return $link_attributes;
            
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                // Labels
                'labels'                        => '',
                'align'                         => 'left',
                'list_style'                    => 'inline',
                
                // Global Label Styling
                'title_color'                   => '',
                'title_color_hover'             => '',
                'label_background_color'        => '',
                'label_background_color_hover'  => '',
                'label_border_radius'           => '4',
                'label_spacing'                 => '',
				'label_group_spacing'			=> '',
                
                // Font Settings
                'font_size'      => '12',
                'font_weight'    => '',
                'line_height'    => '100',
                'letter_spacing' => '',
                'text_transform' => '',
                'font_source'    => 'theme',
                'google_fonts'   => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'websafe_fonts'  => '',
                
                // custom CSS
                'class'          => ''
                
            ), $atts ) ); 
            
            // CSS Classes 
            $classes   = array('ut-label-module-wrap');
            $classes[] = 'ut-label-module-wrap-' . $align;
            $classes[] = 'ut-label-module-wrap-' . $list_style;
            $classes[] = $class;
                        
            // unique label group ID
            $id = uniqid("ut_label_");                        
            
            // extract label items
            if( function_exists('vc_param_group_parse_atts') && !empty( $labels ) ) {
                
                $labels = vc_param_group_parse_atts( $labels );    
                            
            }           
            
            // google font 
            $ut_font_css = false;
            
            // initialize google font
            if( $font_source && $font_source == 'google' ) {
                
                 $ut_google_font = new UT_VC_Google_Fonts( $atts, 'google_fonts', $this->shortcode );
                 $ut_font_css    = $ut_google_font->get_google_fonts_css_styles();
                        
            }
            
            $ut_font_css = is_array( $ut_font_css ) ? implode( '', $ut_font_css ) : $ut_font_css;
            
            // Custom CSS
            $css_style = '<style type="text/css">';
            
                // Label Design Settings
                if( $title_color ) {
                    $css_style.= '#' . $id . ' .ut-label-module { color:' . $title_color . '; }';
                }
            
                if( $title_color_hover ) {
                    $css_style.= '#' . $id . ':hover { color:' . $title_color_hover . '; }';
                    $css_style.= '#' . $id . ':active { color:' . $title_color_hover . '; }';
                    $css_style.= '#' . $id . ':focus { color:' . $title_color_hover . '; }';
                }
            
                if( $label_background_color && ut_create_gradient_css( $label_background_color ) ) {
                    
                    $css_style.= ut_create_gradient_css( $label_background_color, '#' . $id . ' .ut-label-module' );                    
                    
                } elseif( $label_background_color ) {
                    
                    $css_style.= '#' . $id . ' .ut-label-module { background:' . $label_background_color . '; }';                    
                    
                }               
            
                if( $label_background_color_hover && ut_create_gradient_css( $label_background_color_hover ) ) {
                    
                    $css_style.= ut_create_gradient_css( $label_background_color_hover, '#' . $id . ' a:hover' );
                    $css_style.= ut_create_gradient_css( $label_background_color_hover, '#' . $id . ' a:active' );
                    $css_style.= ut_create_gradient_css( $label_background_color_hover, '#' . $id . ' a:focus' );
                    
                } elseif( $label_background_color_hover ) {
                    
                    $css_style.= '#' . $id . ' a:hover { background:' . $label_background_color_hover . '; }';
                    $css_style.= '#' . $id . ' a:active { background:' . $label_background_color_hover . '; }';
                    $css_style.= '#' . $id . ' a:focus { background:' . $label_background_color_hover . '; }';
                    
                }
            
                if( $label_border_radius ) {
                    $css_style.= '#' . $id . ' .ut-label-module { border-radius:' . $label_border_radius . 'px; }';
                }
                            
                // Label Font Settings
                $label_font_settings = array();
            
                if( $font_source && $font_source == 'google' && $ut_font_css ) {                    
                    $label_font_settings[] = $ut_font_css;
                }
            
                if( $font_source && $font_source == 'websafe' ) {
                    $label_font_settings[] = 'font-family: ' . get_websafe_font_css_family( $websafe_fonts ) . ';';
                }
            
            
                if( $font_size ) {
                    $label_font_settings[] = 'font-size:' . $font_size . 'px;';
                }
            
                if( $line_height ) {
                    $label_font_settings[] = 'line-height:' . $line_height . '%;';                   
                }
            
                if( $letter_spacing ) {
					
					// fallback letter spacing
					if( $letter_spacing >= 1 || $letter_spacing <= -1 ) {
						$letter_spacing = $letter_spacing / 100;	
					}
					
                    $label_font_settings[] = 'letter-spacing:' . $letter_spacing . 'em;';
					
                }
            
                if( $text_transform ) {
                    $label_font_settings[] = 'text-transform:' . $text_transform . ';';
                }
            
                if( $font_weight ) {
                    $label_font_settings[] = 'font-weight:' . $font_weight . ';';
                }
            
                if( !empty( $label_font_settings ) ) {
                    
                    $css_style .= '#' . $id . ' .ut-label-module { '. implode(" ", $label_font_settings ) .' }';
                    
                }    
                
                if( $label_spacing ) {
                    $css_style .= '#' . $id . ' .ut-label-module { '. $label_spacing .' }';
                }                 
            
				if( $label_group_spacing ) {
					$css_style .= '#' . $id . ' { margin-bottom: '. $label_group_spacing .'px; }';
				}
			
                // Label Custom Styles
                if( !empty( $labels ) && is_array( $labels ) ) {
            
                    foreach( $labels as $key => $label ) {
                        
                        $single_id     = uniqid("ut_single_label_");
                        $single_design = $this->create_inline_css( $label, $single_id );
                        
                        if( !empty( $single_design ) ) {
                            
                            $css_style .= $single_design;                            
                            
                        }                        
                        
                        $labels[$key]['id'] = $single_id;
                        
                    }
            
                }
                    
            $css_style.= '</style>';
            
            // start output
            $output = '';            
            
            if( !empty( $labels ) && is_array( $labels ) ) {
                                
                // attach CSS
                $output .= ut_minify_inline_css( $css_style );
                                
                $output .= '<ul id="' . esc_attr( $id ) . '" class="' . implode(" ", $classes ) . '">';
                
                foreach( $labels as $label ) {
                    
                    // no label title
                    if( empty( $label["title"] ) ) {
                        continue;
                    }
                    
                    // check if label has link or not
                    $tag  = isset( $label['link'] ) && $label['link'] == "true" ? 'a' : 'span';
                    $link = !empty( $label['label_link'] ) ? $this->create_label_link( $label['label_link'] ) : array() ;
                    
                    $output .= '<li>';
                    
                        $output .= '<' . $tag . ' id="' . $label["id"] . '" class="ut-label-module" ' . implode(" ", $link ) . '>';

                            $output .= $label["title"];

                        $output .= '</' . $tag . '>';
                    
                    $output .= '</li>';
                    
                    
                }
                
                $output .= '</ul>';
                
            }
            
                
            return $output;
        
        }
            
    }

}

new UT_Label_Shortcode;