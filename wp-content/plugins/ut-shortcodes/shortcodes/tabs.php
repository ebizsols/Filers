<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Default_Tabs' ) ) {
	
    class UT_Default_Tabs {
        
        private $shortcode;
        private $inner_shortcode;
        
        private $navigation;
        private $count;
        
        private $css;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_default_tabs';
            $this->inner_shortcode = 'ut_default_tab';
                
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );
            add_shortcode( $this->inner_shortcode, array( $this, 'ut_create_inner_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Standard Tabs Module', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'A single content area with multiple panels. Click tabs to swap between content that is broken into logical sections.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-folder ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/default.png',
                    'category'        => 'Information',
                    'class'           => 'ut-information-module',
                    'as_parent'       => array( 'only' => $this->inner_shortcode ),
                    'content_element' => true,
                    'is_container'    => true,
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Select Alignment', 'ut_shortcodes' ) => '',
                                esc_html__( 'center', 'ut_shortcodes' )           => 'center',
                                esc_html__( 'left'  , 'ut_shortcodes' )           => 'left',
                                esc_html__( 'right' , 'ut_shortcodes' )           => 'right',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Label Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'label_font_weight',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
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
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Label Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'label_text_transform',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Custom Label Font Size?', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'label_custom_font_size',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' )    => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Label Font Size', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'label_font_size',
                            'value'             => array(
                                'default'   => ut_get_theme_options_font_setting( 'h3', 'font-size', "17" ),
                                'min'       => '8',
                                'max'       => '50',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'label_custom_font_size',
                                'value'   => 'true',
                            )
                        ),


                        /* colors */
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Label Color', 'ut_shortcodes' ),
                            'param_name'        => 'label_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Label Design'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Label Hover and Active Color', 'ut_shortcodes' ),
                            'param_name'        => 'label_hover_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Label Design'
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Labels with Background and Border', 'ut_shortcodes' ),
                            'param_name'        => 'label_border_background',
                            'group'             => 'Label Design',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' )    => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'label_border_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Label Design',
                            'dependency'        => array(
                                'element' => 'label_border_background',
                                'value'   => 'true',
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Border Hover and Active Color', 'ut_shortcodes' ),
                            'param_name'        => 'label_border_hover_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Label Design',
                            'dependency'        => array(
                                'element' => 'label_border_background',
                                'value'   => 'true',
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Border Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'double requires at least 4px border size.', 'ut_shortcodes' ),
                            'param_name'        => 'label_border_style',
                            'group'             => 'Label Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' )  => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' )  => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' )  => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' )  => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'label_border_background',
                                'value'   => 'true',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Border Width', 'ut_shortcodes' ),
                            'param_name'        => 'label_border_width',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '50',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'group'             => 'Label Design',
                            'dependency'        => array(
                                'element' => 'label_border_background',
                                'value'   => 'true',
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'label_background_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Label Design',
                            'dependency'        => array(
                                'element' => 'label_border_background',
                                'value'   => 'true',
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Background Hover and Active Color', 'ut_shortcodes' ),
                            'param_name'        => 'label_background_hover_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Label Design',
                            'dependency'        => array(
                                'element' => 'label_border_background',
                                'value'   => 'true',
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Content Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'content_text_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Label Design'
                        ),


                    ),
                    'js_view'         => 'VcColumnView'

                )

            ); // end mapping

            vc_map(
                array(
                    'name'            => esc_html__( 'Standard Tab', 'ut_shortcodes' ),
                    'base'            => $this->inner_shortcode,
                    // 'icon'            => 'fa fa-folder ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/default.png',
                    'as_child'        => array( 'only' => $this->shortcode ),
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Tab Label', 'ut_shortcodes' ),
                            'param_name'        => 'label',
                            'edit_field_class'  => 'vc_col-sm-12 vc_column_no_padding_top',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'textarea_html',
                            'heading'           => esc_html__( 'Tab Content', 'ut_shortcodes' ),
                            'param_name'        => 'content',
                            'group'             => 'Content'
                        ),

                    )

                )

            ); // end mapping
        
        }
        
        function ut_create_inline_css() {
            
            $css_style = '';
                
            if( !empty( $this->css['label_font_weight'] ) ) {
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a .bklyn-tab-label {  font-weight: ' . $this->css['label_font_weight'] . '; }';
            }
            
            if( !empty( $this->css['label_text_transform'] ) ) {
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a .bklyn-tab-label {  text-transform: ' . $this->css['label_text_transform'] . '; }';
            }
            
            if( $this->css['label_custom_font_size'] == 'true' && !empty( $this->css['label_font_size'] ) ) {
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a .bklyn-tab-label {  font-size: ' . $this->css['label_font_size'] . 'px; }';
            }
            
            if( !empty( $this->css['content_text_color'] ) ) {
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .tab-pane {  color: ' . $this->css['content_text_color'] . '; }';
            }              
            
            if( !empty( $this->css['label_color'] ) ) {
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a .bklyn-tab-label {  color: ' . $this->css['label_color'] . '; }';
            }
            
            if( !empty( $this->css['label_hover_color'] ) ) {
                
                // hover state
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a:hover .bklyn-tab-label {  color: ' . $this->css['label_hover_color'] . '; }';
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a:focus .bklyn-tab-label {  color: ' . $this->css['label_hover_color'] . '; }';
                
                // active hover state
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a .bklyn-tab-label {  color: ' . $this->css['label_hover_color'] . '; }';
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a:hover .bklyn-tab-label {  color: ' . $this->css['label_hover_color'] . '; }';
                $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a:focus .bklyn-tab-label {  color: ' . $this->css['label_hover_color'] . '; }';
                
            }
                            
            if( $this->css['label_border_background'] == 'true' ) {
                
                if( !empty( $this->css['label_border_color'] ) ) {
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a { border-color: ' . $this->css['label_border_color'] . '; }';
                }
                
                if( !empty( $this->css['label_border_hover_color'] ) ) {
                    
                    // hover state
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a:hover { border-color: ' . $this->css['label_border_hover_color'] . '; }';
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a:focus { border-color: ' . $this->css['label_border_hover_color'] . '; }';
                    
                    // active hover state
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a { border-color: ' . $this->css['label_border_hover_color'] . '; }';
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a:hover { border-color: ' . $this->css['label_border_hover_color'] . '; }';
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a:focus { border-color: ' . $this->css['label_border_hover_color'] . '; }';
                    
                }
                
                if( !empty( $this->css['label_background_color'] ) ) {
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a { background: ' . $this->css['label_background_color'] . '; }';
                }
                
                if( !empty( $this->css['label_background_hover_color'] ) ) {
                    
                    // hover state
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a:hover { background: ' . $this->css['label_background_hover_color'] . '; }';
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a:focus { background: ' . $this->css['label_background_hover_color'] . '; }';
                    
                    // active hover state
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a { background: ' . $this->css['label_background_hover_color'] . '; }';
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a:hover { background: ' . $this->css['label_background_hover_color'] . '; }';
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li.active a:focus { background: ' . $this->css['label_background_hover_color'] . '; }';
                    
                }
                
                if( !empty( $this->css['label_border_style'] ) ) {
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a { border-style: ' . $this->css['label_border_style'] . '; }';
                }
                
                if( !empty( $this->css['label_border_width'] ) ) {
                    $css_style .= '#' . esc_attr( $this->css['globaL_style_id'] ) . ' .bklyn-tabs li a { border-width: ' . $this->css['label_border_width'] . 'px; }';
                }
            
            }
            
            return !empty( $css_style ) ? ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' ) : '';
            
        }        
        
        function ut_create_tabs_navigation( $label_border_background ) {
            
            $navigation = '';
            
            if( !empty( $this->navigation ) ) {
                
                $navigation .= '<ul class="bklyn-tabs ' . ( $label_border_background == 'true' ? 'bklyn-tabs-bb' : ''  ) . '">';
                
                $first = true;
                
                foreach( $this->navigation as $item ) {
                    
                    $navigation .= '<li id="' . esc_attr( $item['style_id'] ) . '" ' . ( $first ? 'class="active"' : '' ) . '>';
                        
                        $navigation .= '<a href="#' . esc_attr( $item['id'] ) . '" data-toggle="tab">';
                            
                            $navigation .= '<h3 class="bklyn-tab-label">' . $item['label'] . '</h3>';
                            
                        $navigation .= '</a>';
                    
                    $navigation .= '</li>';
                    
                    $first = false;
                    
                }
                
                $navigation .= '</ul>';
            
            }
            
            return $navigation;
        
        }       
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'align'                         => 'center',
                'label_font_weight'             => '',
                'label_text_transform'          => '',
                'label_custom_font_size'        => 'false',
                'label_font_size'               => '',
                'label_color'                   => '',
                'label_hover_color'             => '',
                'label_border_background'       => 'false',
                'label_border_color'            => '',
                'label_border_hover_color'      => '',
                'label_border_style'            => 'solid',
                'label_border_width'            => '',
                'label_background_color'        => '',
                'label_background_hover_color'  => '',
                'content_text_color'    => ''
            ), $atts ) ); 
            
            $this->navigation = array();
            $this->count = 1;
            $this->css = array();
            
            /* excute inner already shortcodes */
            $tab = do_shortcode( $content );
            
            /* global css */
            $this->css['globaL_style_id']              = uniqid("ut_tabs_style_");
            $this->css['label_font_weight']            = $label_font_weight;
            $this->css['label_text_transform']         = $label_text_transform;
            $this->css['label_custom_font_size']       = $label_custom_font_size;
            $this->css['label_font_size']              = $label_font_size;
            $this->css['label_color']                  = $label_color;
            $this->css['label_hover_color']            = $label_hover_color;
            $this->css['label_border_background']      = $label_border_background;
            $this->css['label_border_color']           = $label_border_color;
            $this->css['label_border_color']           = $label_border_color;
            $this->css['label_border_hover_color']     = $label_border_hover_color;
            $this->css['label_border_style']           = $label_border_style;
            $this->css['label_border_width']           = $label_border_width;
            $this->css['label_background_color']       = $label_background_color;
            $this->css['label_background_hover_color'] = $label_background_hover_color;                        
            $this->css['content_text_color']           = $content_text_color;
            
            /* start output */
            $output = '';
            
            $output .= $this->ut_create_inline_css();
            
            $output .= '<div id="' . esc_attr( $this->css['globaL_style_id'] ) . '" class="bklyn-tabs-wrap bklyn-tabs-' . esc_attr( $align ) . '">';
                
                /* create navigation */
                $output .= $this->ut_create_tabs_navigation( $label_border_background );
                
                $output .= '<div class="tab-content">';
                    
                    $output .= $tab;
                
                $output .= '</div>';
                
            $output .= '</div>';
            
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element">' . $output . '</div>'; 
            
            }
                
            return $output;
            
        
        }
        
        function ut_create_inner_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'label'             => '',
            ), $atts ) );
            
            /* enhance array for tab navigation */
            $this->navigation[$this->count]['id']                = uniqid("ut_tab_");
            $this->navigation[$this->count]['label']             = $label;
            $this->navigation[$this->count]['style_id']          = uniqid("ut_tab_style_");
            
            /* start output */
            $output = '';
            
            $output .= '<div data-size="auto" class="tab-pane fade ' . ( $this->count == 1 ? 'active' : '' ) . '" id="' . esc_attr( $this->navigation[$this->count]['id'] ) . '"> ';
            
                $output .= do_shortcode( $content );
            
            $output .= '</div>';
            
            /* increase internal counter */
            $this->count++;
                        
            return $output;
        
        }
            
    }

}

new UT_Default_Tabs;

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    
    class WPBakeryShortCode_ut_default_tabs extends WPBakeryShortCodesContainer {
        
              
            
    }
    
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_default_tab extends WPBakeryShortCode {
        
        
        
    }
    
}