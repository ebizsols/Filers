<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Divider' ) ) {
	
    class UT_Divider {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_divider';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Fancy Divider Module', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/title-divider.png',
                    'category'        => 'Structual',
                    'class'           => 'ut-vc-icon-module ut-structual-module',
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Divider Style', 'ut_shortcodes' ),
                            'group'             => 'General',
                            'param_name'        => 'divider',
                            'admin_label'       => true,
                            'value'             => array(
                                esc_html__( 'Style 1' , 'ut_shortcodes' ) => 'bklyn-fancy-divider-style-1',
                                esc_html__( 'Style 2' , 'ut_shortcodes' ) => 'bklyn-fancy-divider-style-2'
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
                            'group' => 'General',
                            'dependency'    => array(
                                'element' => 'divider',
                                'value'     => 'bklyn-fancy-divider-style-1'
                            )
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Divider Color', 'ut_shortcodes' ),
                            'param_name'        => 'primary_color',
                            'group'             => 'General'
                        ),

                        array(
                            'type'             => 'dropdown',
                            'heading'          => esc_html__( 'Animate Divider Line', 'ut_shortcodes' ),
                            'param_name'       => 'animated',
                            'group'            => 'Line Animation',
                            'value'            => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' )   => 'off',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on',
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Delay Divider Line Animation?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'value in milliseconds, eg "800"', 'ut_shortcodes' ),
                            'param_name'        => 'animated_delay',
                            'group'             => 'Line Animation',
                            'dependency'       => array(
                                'element' => 'animated',
                                'value'   => 'on',
                            ),
                        ),

                        array(
                            'type'             => 'dropdown',
                            'heading'          => esc_html__( 'Alignment', 'ut_shortcodes' ),
                            'param_name'       => 'align',
                            'group'            => 'General',
                            'edit_field_class' => 'vc_col-sm-4',
                            'value'            => array(
                                esc_html__( 'left', 'ut_shortcodes' )   => 'left',
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right', 'ut_shortcodes' )  => 'right',
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
                                esc_html__( 'center', 'ut_shortcodes' )              => 'center',
                                esc_html__( 'left', 'ut_shortcodes' )                => 'left',
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

                        /* Icon
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Icon to Divider?', 'ut_shortcodes' ),
                            'param_name'        => 'add_icon',
                            'group'             => 'Icon',
                            'value'             => array(
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false'
                            ),
                        ),
                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'icon',
                            'group'             => 'Icon',
                            'dependency'        => array(
                                'element' => 'add_icon',
                                'value'   => 'true',
                            )
                        ),
                        array(
                            'type'              => 'attach_image',
                            'heading'           => esc_html__( 'or upload an own Icon', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'recommended size 48x48', 'ut_shortcodes' ),
                            'param_name'        => 'imageicon',
                            'group'             => 'Icon',
                            'dependency'        => array(
                                'element' => 'add_icon',
                                'value'   => 'true',
                            )
                        ),  */

                        /* divider spacing */
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
        
        function ut_create_shortcode( $atts, $content = NULL ) {

            /**
             * @var $divider
             * @var $divider_width
             * @var $animated
             * @var $animated_delay
             *
             * @var $primary_color
             *
             * @var $class
             */

            extract( shortcode_atts( array (
                
                'divider'            => 'bklyn-fancy-divider-style-1',
                'divider_width'      => '100',
                'align'              => 'left',
                'align_tablet'       => 'inherit',
                'align_mobile'       => 'center',

                // animated
                'animated'           => 'off',
                'animated_delay'     => '0',
                'spacing'            => '',

                // Colors
                'primary_color'      => '',
                'css'                => '',
                'class'              => ''
                
            ), $atts ) );
            
            /* classes */
            $classes    = array( $class );
            $classes[]  = $divider;

            /* animation effect */
            $attributes = array();

            if( $animated == 'on' ) {

                $classes[] = 'bklyn-fancy-divider-animated';
                $attributes['data-animated-delay'] = $animated_delay;

            }

            // attributes string
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );


            // unique ID
            $id = uniqid("ut_fancy_divider_");
            $outer_id = uniqid("ut_fancy_divider_wrap");


            $css_style = '';

            if( $divider == 'bklyn-fancy-divider-style-1' )  {

                $css_style .= '#' . $id . ':not(.bklyn-fancy-divider-animated) { width: ' . $divider_width . '%; }';

            }

            if( $primary_color ) {

                $css_style .= '#' . $id . ' { background: ' . $primary_color . '; }';
                $css_style .= '#' . $id . '::after { background: ' . $primary_color . '; }';
                $css_style .= '#' . $id . '::before { background: ' . $primary_color . '; }';

            }

            $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
            $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

            $margin = array(
                'left'   => '0 auto 0 0',
                'center' => '0 auto',
                'right'  => '0 0 0 auto',
            );

            if( $align_mobile ) {

                $css_style.= '@media (max-width: 767px) { #' . $id . ' { margin:' . $margin[$align_mobile] . '; } }';

            }

            if( $align_tablet ) {

                $css_style.= '@media (min-width: 768px) and (max-width: 1024px) { #' . $id . ' { margin:' . $margin[$align_tablet] . '; } }';

            }

            if( $align ) {

                $css_style.= '@media (min-width: 1025px) { #' . $id . ' { margin:' . $margin[$align] . '; } }';

            }

            /* spacing css */
            if( !empty( $spacing ) ) {

                $css_style .= '#' . $outer_id . ' { '. $spacing .' }';

            }

            // start output
            $output = '';

            // attach css
            if( !empty( $css_style ) ) {

                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );

            }

            $output .= '<div id="' . esc_attr__( $id ) . '" class="bklyn-fancy-divider ' . implode(' ', $classes ) . '" ' . $attributes . '></div>';

            if( defined( 'WPB_VC_VERSION' ) ) {

                return '<div id="' . esc_attr( $outer_id ) . '" class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>';

            }


            return $output;            
            
        }        
            
    }

}

new UT_Divider;