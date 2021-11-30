<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Client_Group' ) ) {
	
    class UT_Client_Group {
        
        private $shortcode;
        private $inner_shortcode;
        
        private $client_carousel;
        private $client_carousel_id;

        private $client_animation;
        private $client_display;
        private $client_count;

        private $atts;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode       = 'ut_client_group';
            $this->inner_shortcode = 'ut_client';
            
            $this->client_carousel    = FALSE;

            add_action( 'init', array( $this, 'ut_map_shortcode' ) );

            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );
            add_shortcode( $this->inner_shortcode, array( $this, 'ut_create_inner_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Brand Module', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Put your clients testimonials into a nice carousel or inside a grid.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'category'        => 'Community',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/client-carousel.png',
                    'class'           => 'ut-vc-icon-module ut-community-module',
                    'as_parent'       => array( 'only' => $this->inner_shortcode ),
                    'content_element' => true,
                    'js_view'         => 'VcColumnView',
                    'params'          => array(

                        array(
                            'type'              => 'checkbox',
                            'heading'           => esc_html__( 'Activate Carousel?', 'ut_shortcodes' ),
                            'param_name'        => 'carousel',
                            'group'             => 'General',
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Column Count', 'ut_shortcodes' ),
                            'param_name'        => 'columns',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( '5 Columns', 'ut_shortcodes' )  => '5',
                                esc_html__( '6 Columns', 'ut_shortcodes' )  => '6',
                                esc_html__( '4 Columns', 'ut_shortcodes' )  => '4',
                                esc_html__( '3 Columns', 'ut_shortcodes' )  => '3',
                                esc_html__( '2 Columns', 'ut_shortcodes' )  => '2',
                            ),

                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Display as', 'ut_shortcodes' ),
                            'param_name'        => 'display',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Plain Images', 'ut_shortcodes' ) => 'plain',
                                esc_html__( 'Images In Cells', 'ut_shortcodes' ) => 'cells',

                            ),
                            'dependency'    => array(
                                'element' => 'carousel',
                                'value_not_equal_to' => array( 'true' ),
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Cell Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'Left', 'ut_shortcodes' ) => 'flex-start',
                                esc_html__( 'Right', 'ut_shortcodes' ) => 'flex-end',
                                esc_html__( 'Space Around', 'ut_shortcodes' ) => 'space-around',
                                esc_html__( 'Space Between', 'ut_shortcodes' ) => 'space-between',

                            ),
                            'dependency'    => array(
                                'element' => 'display',
                                'value' => array( 'plain' ),
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Cell Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'cell_align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Left', 'ut_shortcodes' ) => 'flex-start',
                                esc_html__( 'Center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'Right', 'ut_shortcodes' ) => 'flex-end',
                            ),
                            'dependency'    => array(
                                'element' => 'display',
                                'value' => array( 'cells' ),
                            )
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                        /* logo settings */
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Overall Logo Opacity', 'ut_shortcodes' ),
                            'param_name'        => 'logo_opacity',
                            'group'             => 'Logo Settings',
                            'value'             => array(
                                'def'   => '1',
                                'min'   => '0.1',
                                'max'   => '1',
                                'step'  => '0.1',
                                'unit'  => ''
                            ),

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Overall Logo Opacity Hover', 'ut_shortcodes' ),
                            'param_name'        => 'logo_opacity_hover',
                            'group'             => 'Logo Settings',
                            'value'             => array(
                                'def'   => '1',
                                'min'   => '0.1',
                                'max'   => '1',
                                'step'  => '0.1',
                                'unit'  => ''
                            ),

                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Force Logo Height?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'By default logos display with their own height. With this option, all logos will be forced to the same height.', 'ut_shortcodes' ),
                            'param_name'        => 'force_logo_height',
                            'group'             => 'Logo Settings',
                            'edit_field_class'  => 'vc_col-sm-12',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Overall Logo Height', 'ut_shortcodes' ),
                            'param_name'        => 'logo_height',
                            'group'             => 'Logo Settings',
                            'value'             => array(
                                'def'   => '20',
                                'min'   => '1',
                                'max'   => '100',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'dependency'    => array(
                                'element' => 'force_logo_height',
                                'value' => array( 'true' ),
                            )

                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Display Logo with original height on mobiles?', 'ut_shortcodes' ),
                            'param_name'        => 'reset_mobile_logo_height',
                            'group'             => 'Logo Settings',
                            'edit_field_class'  => 'vc_col-sm-12',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                            'dependency'    => array(
                                'element' => 'force_logo_height',
                                'value' => array( 'true' ),
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Logos?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Animate each logo inside your client area with an awesome animation effect.', 'ut_shortcodes' ),
                            'param_name'        => 'animate',
                            'group'             => 'Logo Animation',
                            'edit_field_class'  => 'vc_col-sm-12',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
                            ),
                            'dependency'    => array(
                                'element' => 'carousel',
                                'value_not_equal_to' => array( 'true' ),
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Logos on Tablet?', 'ut_shortcodes' ),
                            'param_name'        => 'animate_tablet',
                            'group'             => 'Logo Animation',
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
                            'heading'           => esc_html__( 'Animate Logos on Mobile?', 'ut_shortcodes' ),
                            'param_name'        => 'animate_mobile',
                            'group'             => 'Logo Animation',
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
                            'heading'           => __( 'Animation Effect', 'ut_shortcodes' ),
                            'description'       => __( 'Select initial loading animation for logos.', 'ut_shortcodes' ),
                            'group'             => 'Logo Animation',
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

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Animation Delay Timer', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Time in milliseconds until the next image appears. e.g. 100', 'ut_shortcodes' ),
                            'param_name'        => 'effect_delay',
                            'group'             => 'Logo Animation',
                            'dependency'        => array(
                                'element' => 'animate',
                                'value'   => 'true',
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Set delay until Brand Animation starts?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'This timer allows you to delay the entire animation process of brands.', 'ut_shortcodes' ),
                            'param_name'        => 'global_effect_delay',
                            'group'             => 'Logo Animation',
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
                            'description'       => esc_html__( 'Time in milliseconds until the brand animation should start. e.g. 200', 'ut_shortcodes' ),
                            'param_name'        => 'global_effect_delay_timer',
                            'group'             => 'Logo Animation',
                            'dependency'        => array(
                                'element' => 'global_effect_delay',
                                'value'   => 'true',
                            )
                        ),

                        /* carousel settings */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Carousel Border Top', 'ut_shortcodes' ),
                            'param_name'        => 'carousel_border_top',
                            'group'             => 'Carousel Settings',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'off', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'on', 'ut_shortcodes' ) => 'on'
                            ),
                            'dependency'    => array(
                                'element' => 'carousel',
                                'value' => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Carousel Border Bottom', 'ut_shortcodes' ),
                            'param_name'        => 'carousel_border_bottom',
                            'group'             => 'Carousel Settings',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'off', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'on', 'ut_shortcodes' ) => 'on'
                            ),
                            'dependency'    => array(
                                'element' => 'carousel',
                                'value' => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Prev Next Buttons', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Creates and enables previous & next buttons.', 'ut_shortcodes' ),
                            'param_name'        => 'prev_next_buttons',
                            'group'             => 'Carousel Settings',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'off', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'on', 'ut_shortcodes' ) => 'true'
                            ),
                            'dependency'    => array(
                                'element' => 'carousel',
                                'value' => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Page Dots', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Creates and enables page dots.', 'ut_shortcodes' ),
                            'param_name'        => 'page_dots',
                            'group'             => 'Carousel Settings',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'off', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'on', 'ut_shortcodes' ) => 'true',

                            ),
                            'dependency'    => array(
                                'element' => 'carousel',
                                'value' => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Autoplay', 'ut_shortcodes' ),
                            'param_name'        => 'autoplay',
                            'group'             => 'Carousel Settings',
                            'value'             => array(
                                esc_html__( 'on', 'ut_shortcodes' ) => 'true',
                                esc_html__( 'off', 'ut_shortcodes' ) => 'false'
                            ),
                            'dependency'    => array(
                                'element' => 'carousel',
                                'value' => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Autoplay Timer', 'ut_shortcodes' ),
                            'param_name'        => 'autoplay_timer',
                            'group'             => 'Carousel Settings',
                            'value'             => array(
                                'def'   => '3000',
                                'min'   => '1000',
                                'max'   => '10000',
                                'step'  => '1000',
                                'unit'  => 'ms'
                            ),
                            'dependency' => array(
                                'element' => 'autoplay',
                                'value'   => array( 'true' ),
                            ),


                        ),


                        /* colors */
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Cell Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'cell_border_color',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'display',
                                'value'   => array( 'cells' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Cell Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'cell_background_color',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'display',
                                'value'   => array( 'cells' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Cell Hover Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'cell_background_color_hover',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'display',
                                'value'   => array( 'cells' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Decoration Line Top Color', 'ut_shortcodes' ),
                            'param_name'        => 'deco_line_color_top',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'carousel_border_top',
                                'value'   => array( 'on' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Decoration Line Bottom Color', 'ut_shortcodes' ),
                            'param_name'        => 'deco_line_color_bottom',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'carousel_border_bottom',
                                'value'   => array( 'on' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Arrow Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'prev_next_buttons',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Arrow Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_color_hover',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'prev_next_buttons',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Arrow Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_background',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'prev_next_buttons',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Arrow Hover Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_background_hover',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'prev_next_buttons',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Arrow Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_border_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'prev_next_buttons',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Arrow Hover Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_border_color_hover',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'prev_next_buttons',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Dot Color', 'ut_shortcodes' ),
                            'param_name'        => 'dot_color',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'page_dots',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Dot Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'dot_color_hover',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'page_dots',
                                'value'   => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Dot Active Color', 'ut_shortcodes' ),
                            'param_name'        => 'dot_color_active',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'group'             => 'Colors',
                            'dependency'    => array(
                                'element' => 'page_dots',
                                'value'   => array( 'true' ),
                            )
                        ),

                        /* css editor */
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        )

                    )

                )

            ); /* end mapping */

            vc_map(
                array(
                    'name'            => esc_html__( 'Single Brand', 'ut_shortcodes' ),
                    'base'            => $this->inner_shortcode,
                    'as_child'        => array( 'only' => $this->shortcode ),
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/client.png',
                    'class'           => 'ut-vc-icon-module ut-community-module',
                    'category'        => 'Community',
                    'content_element' => true,
                    'params'          => array(
                        array(
                            'type'              => 'attach_image',
                            'heading'           => esc_html__( 'Brand Logo', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Logos within this Client Group should have the same size for best results. Also try to avoid whitespace. This can cause unwanted spacing.', 'ut_shortcodes' ),
                            'param_name'        => 'logo',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Brand Name', 'ut_shortcodes' ),
                            'param_name'        => 'name',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'vc_link',
                            'heading'           => esc_html__( 'Brand Website', 'ut_shortcodes' ),
                            'param_name'        => 'url',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                    )

                )

            ); /* end mapping */
        
        }


        function vendor_prefix( $align ) {

            switch( $align ) {

                case 'flex-start':
                    return '-webkit-box-pack: start; -ms-flex-pack: start; justify-content: flex-start;';
                    break;

                case 'flex-end':
                    return '-webkit-box-pack: end; -ms-flex-pack: end; justify-content: flex-end;';
                    break;

                case 'center':
                    return '-webkit-box-pack: center; -ms-flex-pack: center; justify-content: center;';
                    break;

                case 'space-between':
                    return '-webkit-box-pack: justify; -ms-flex-pack: justify; justify-content: space-between;';
                    break;

                case 'space-around':
                    return '-ms-flex-pack: distribute; justify-content: space-around;';
                    break;

                default:
                    echo '';

            }

        }


        /*
         * Carousel JSON
         */

        function carousel_settings_json() {

            /**
             * @var $columns
             * @var $autoplay
             * @var $autoplay_timer
             * @var $page_dots
             * @var $prev_next_buttons
             *
             */

            extract( shortcode_atts( array (

                // Carousel Settings
                'columns'                  => '5',
                'autoplay'                 => 'true',
                'autoplay_timer'           => '3000',
                'page_dots'                => 'false',
                'prev_next_buttons'        => 'false',

            ), $this->atts ) );

            $autoplay = $autoplay == 'true' ? $autoplay_timer : false;

            $json = array(
                'cellAlign'             => 'left',
                'wrapAround'            => true,
                'groupCells'            => 1,
                'lazyLoad'              => $columns + 1,
                'imagesLoaded'          => true,
                'pageDots'              => filter_var( $page_dots, FILTER_VALIDATE_BOOLEAN ),
                'autoPlay'              => $autoplay,
                'pauseAutoPlayOnHover'  => false,
                'prevNextButtons'       => filter_var( $prev_next_buttons, FILTER_VALIDATE_BOOLEAN ),
                'arrowShape'            => '',


            );

            return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

        }


        function ut_create_shortcode( $atts, $content = NULL ) {

            $this->atts = $atts;

            /**
             * @var $class
             * @var $columns
             * @var $autoplay
             * @var $autoplay_timer
             * @var $page_dots
             * @var $prev_next_buttons
             * @var $cell_border_color
             * @var $cell_background_color
             * @var $cell_background_color_hover
             * @var $display
             * @var $align
             * @var $cell_align
             * @var $logo_opacity
             * @var $logo_opacity_hover
             * @var $force_logo_height
             * @var $reset_mobile_logo_height
             * @var $logo_height
             *
             * @var $animate
             * @var $animate_tablet
             * @var $animate_mobile
             * @var $effect
             * @var $effect_delay
             * @var $global_effect_delay
             * @var $global_effect_delay_timer
             * @var $carousel_border_top
             * @var $carousel_border_bottom
             *
             * @var $deco_line_color_top
             * @var $deco_line_color_bottom
             * @var $arrow_color
             * @var $arrow_color_hover
             * @var $arrow_background
             * @var $arrow_background_hover
             * @var $arrow_border_color
             * @var $arrow_border_color_hover
             * @var $dot_color
             * @var $dot_color_hover
             * @var $dot_color_active
             *
             *
             */

            extract( shortcode_atts( array (
                'class'                    => '',
                'carousel'                 => 'off',

                // Carousel Settings
                'autoplay'                 => 'true',
                'autoplay_timer'           => '3000',
                'page_dots'                => 'false',
                'prev_next_buttons'        => 'false',
                'carousel_border_top'      => 'off',
                'carousel_border_bottom'   => 'off',

                'columns'                       => '5',
                'display'                       => 'plain',
                'align'                         => 'center',
                'cell_align'                    => 'flex-start',
                'logo_opacity'                  => '1',
                'logo_opacity_hover'            => '1',
                'force_logo_height'             => 'false',
                'reset_mobile_logo_height'      => 'false',
                'logo_height'                   => '20',

                'animate'                       => '',
                'animate_tablet'                => '',
                'animate_mobile'                => '',
                'effect'                        => '',
                'effect_delay'                  => '100',
                'global_effect_delay'           => 'false',
                'global_effect_delay_timer'     => '',

                'cell_border_color'             => '#DDD',
                'cell_background_color'         => '',
                'cell_background_color_hover'   => '',

                'deco_line_color_top'      => '',
                'deco_line_color_bottom'   => '',
                'arrow_color'              => '',
                'arrow_color_hover'        => '',
                'arrow_background'         => '',
                'arrow_background_hover'   => '',
                'arrow_border_color'       => '',
                'arrow_border_color_hover' => '',
                'dot_color'                => '',
                'dot_color_hover'          => '',
                'dot_color_active'         => '',

                'css'                      => ''
            ), $this->atts ) );

            $this->client_display = $display;
            $this->client_count = substr_count( $content, '[ut_client' );

            // maybe we have an unbalanced grid
            $rest = (( $this->client_count % $columns ) + $columns) % $columns;
            $rest = $rest == 0 ? $columns : $rest;

            $tablet_count = $columns >= 4 ? 4 : $columns;

            $rest_tablet = (( $this->client_count % $tablet_count) + $tablet_count) % $tablet_count;
            $rest_tablet = $rest_tablet == 0 ? $tablet_count : $rest_tablet;

            $rest_mobile = (( $this->client_count % 2) + 2) % 2;
            $rest_mobile = $rest_mobile == 0 ? 2 : $rest_mobile;

            $classes = array();
            $animation_settings = array();
            
            /* extra element class */
            $classes[] = $class;

            /* start output */
            $output = '';
            
            $this->client_carousel_id = $id = uniqid("ut_cc_");
            $outer_id = uniqid("ut_cc_outer_");

            $selector  = '#' . $id;

            ob_start(); ?>

            <style type="text/css">

                <?php if( $display == 'plain' ) : ?>

                    <?php echo $selector; ?>.ut-brands:not(.ut-brands-carousel) {
                        <?php echo $this->vendor_prefix( $align ); ?>
                    }

                <?php endif; ?>

                <?php if( $display == 'cells' ) : ?>

                    <?php echo $selector; ?>.ut-brands:not(.ut-brands-carousel) {
                        <?php echo $this->vendor_prefix( $cell_align ); ?>
                    }

                <?php endif; ?>

                <?php if( $deco_line_color_top ) : ?>

                    <?php echo $selector; ?>.ut-brands-carousel {
                        border-top-color: <?php echo $deco_line_color_top; ?>
                    }

                <?php endif; ?>

                <?php if( $deco_line_color_bottom ) : ?>

                    <?php echo $selector; ?>.ut-brands-carousel {
                        border-bottom-color: <?php echo $deco_line_color_bottom; ?>
                    }

                <?php endif; ?>

                <?php if( $arrow_color ) : ?>

                    <?php echo $selector; ?> .flickity-button::before {
                        color: <?php echo $arrow_color; ?>
                    }

                <?php endif; ?>

                <?php if( $arrow_color_hover ) : ?>

                    <?php echo $selector; ?> .flickity-button:hover::before {
                         color: <?php echo $arrow_color_hover; ?>
                     }

                <?php endif; ?>

                <?php if( $arrow_background ) : ?>

                    <?php echo $selector; ?> .flickity-button {
                         background: <?php echo $arrow_background; ?>
                     }

                <?php endif; ?>

                <?php if( $arrow_background_hover ) : ?>

                    <?php echo $selector; ?> .flickity-button:hover {
                         background: <?php echo $arrow_background_hover; ?>
                     }

                <?php endif; ?>

                <?php if( $arrow_border_color ) : ?>

                    <?php echo $selector; ?> .flickity-button {
                        border-color: <?php echo $arrow_border_color; ?> !important;
                    }

                <?php endif; ?>

                <?php if( $arrow_border_color_hover ) : ?>

                    <?php echo $selector; ?> .flickity-button:hover {
                         border-color: <?php echo $arrow_border_color_hover; ?> !important;
                    }

                <?php endif; ?>

                <?php if( $cell_background_color ) : ?>

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand {
                        background: <?php echo $cell_background_color; ?>;
                    }

                <?php endif; ?>

                <?php if( $cell_background_color_hover ) : ?>

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:hover {
                        background: <?php echo $cell_background_color_hover; ?>;
                    }

                <?php endif; ?>

                <?php if( $logo_opacity ) : ?>

                    <?php echo $selector; ?> .ut-single-brand-logo  {
                        opacity: <?php echo $logo_opacity; ?>;
                    }

                <?php endif; ?>

                <?php if( $logo_opacity_hover ) : ?>

                    <?php echo $selector; ?> .ut-single-brand.ut-single-brand-link:hover .ut-single-brand-logo,
                    <?php echo $selector; ?> .ut-single-brand.ut-single-brand-cells:hover .ut-single-brand-logo,
                    <?php echo $selector; ?> .ut-single-brand:not(.ut-single-brand-link):not(.ut-single-brand-cells) .ut-single-brand-logo:hover {
                        opacity: <?php echo $logo_opacity_hover; ?>;
                    }

                <?php endif; ?>

                <?php if( $force_logo_height == 'true' && $logo_height ) : ?>

                    <?php if( $reset_mobile_logo_height == 'true' ) : ?>

                        @media (min-width: 768px) {

                            <?php echo $selector; ?> .ut-single-brand-logo img {
                                max-height: <?php echo $logo_height; ?>px;
                            }

                        }

                    <?php else : ?>

                        <?php echo $selector; ?> .ut-single-brand-logo img  {
                            max-height: <?php echo $logo_height; ?>px;
                        }

                    <?php endif; ?>

                <?php endif; ?>

                <?php if( $dot_color ) : ?>

                    <?php echo $selector; ?>_dots .flickity-page-dots .dot {
                        background: <?php echo $dot_color; ?>;
                    }

                <?php endif; ?>

                <?php if( $dot_color_hover ) : ?>

                    <?php echo $selector; ?>_dots .flickity-page-dots .dot:not(.is-selected):hover {
                        background: <?php echo $dot_color_hover; ?>;
                    }

                <?php endif; ?>

                <?php if( $dot_color_active ) : ?>

                    <?php echo $selector; ?>_dots .flickity-page-dots .dot.is-selected {
                        background: <?php echo $dot_color_active; ?>;
                    }

                <?php endif; ?>

                <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand {
                    border-right: 1px solid <?php echo $cell_border_color; ?>;
                    border-bottom: 1px solid <?php echo $cell_border_color; ?>;
                }

                <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:first-child {
                    border-left: 1px solid <?php echo $cell_border_color; ?>;
                }

                @media (min-width: 1025px) {

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:nth-child(<?php echo $columns; ?>n+1) {
                        border-left: 1px solid <?php echo $cell_border_color; ?>;
                    }

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:nth-child(-n+<?php echo $columns; ?>) {
                        border-top: 1px solid <?php echo $cell_border_color; ?>;
                    }

                    <?php echo $selector; ?>.ut-brands-display-plain:not(.ut-brands-carousel) .ut-single-brand:nth-last-child(-n+<?php echo $rest; ?>) {
                        padding-bottom: 0;
                    }

                }

                @media (min-width: 768px) and (max-width: 1024px) {

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:nth-child(<?php echo $tablet_count; ?>n+1) {
                        border-left: 1px solid <?php echo $cell_border_color; ?>;
                    }

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:nth-child(-n+<?php echo $tablet_count; ?>) {
                        border-top: 1px solid <?php echo $cell_border_color; ?>;
                    }

                    <?php echo $selector; ?>.ut-brands-display-plain:not(.ut-brands-carousel) .ut-single-brand:nth-last-child(-n+<?php echo $rest_tablet; ?>) {
                        padding-bottom: 0;
                    }

                }

                @media (max-width: 767px) {

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:nth-child(2n+1) {
                        border-left: 1px solid <?php echo $cell_border_color; ?>;
                    }

                    <?php echo $selector; ?>.ut-brands-display-cells .ut-single-brand:nth-child(-n+2) {
                        border-top: 1px solid <?php echo $cell_border_color; ?>;
                    }

                    <?php echo $selector; ?>.ut-brands-display-plain:not(.ut-brands-carousel) .ut-single-brand:nth-last-child(-n+<?php echo $rest_mobile; ?>) {
                        padding-bottom: 0;
                    }

                }

            </style>

            <?php $css_style = ob_get_clean();

            // extra carousel settings
            if( !empty( $carousel ) && ( $carousel == 'on' || $carousel == 'true' ) ) {

                $this->client_carousel = true;

                $classes[] = 'ut-brands-carousel';

                if( $prev_next_buttons == 'false' ) {

                    $classes[] = 'ut-brands-carousel-no-arrows';

                }

                // will be turned into data attributes
                $animation_settings['data-settings'] = $this->carousel_settings_json();
            
            }

            // additional classes
            $classes[] = 'ut-brands-columns-' . $columns;
            $classes[] = 'ut-brands-display-' . $display;

            if( $carousel_border_top == 'off' ) {
                $classes[] = 'ut-brands-no-border-top';
            }

            if( $carousel_border_bottom == 'off' ) {
                $classes[] = 'ut-brands-no-border-bottom';
            }

            // add css
            $output .= ut_minify_inline_css( $css_style );



            if( $animate == 'true' && $effect ) {

                $classes[] = 'ut-animate-brand-logos';

                if( $global_effect_delay == true && !empty( $global_effect_delay_timer ) ) {

                    $animation_settings['data-ut-wait'] = 1;
                    $animation_settings['data-ut-auto-remove-wait'] = $global_effect_delay_timer;

                }

                $animation_settings['data-effect'] = esc_attr($effect);
                $animation_settings['data-delay'] = esc_attr($effect_delay);
                $animation_settings['data-appear-top-offset'] = 'almost';

                if( !$animate_tablet ) {
                    $animation_settings['data-animate-tablet'] = 'true';
                }

                if( !$animate_mobile ) {
                    $animation_settings['data-animate-mobile'] = 'true';
                }

                $this->client_animation = true;

            }

            /* attributes string */
            $animation_settings = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $animation_settings,
                array_keys( $animation_settings )
            ) );


            $output  .= '<div id="' . esc_attr( $id ) . '" class="ut-brands ' . esc_attr(  implode(' ', $classes ) ) . '" ' . $animation_settings .'>';

                $output .= do_shortcode( $content );

                if( !empty( $carousel ) && ( $carousel == 'on' || $carousel == 'true' ) && $this->client_count <= $columns ) {

                    $output .= do_shortcode($content);

                }

            $output .= '</div>';

            if( $page_dots == 'true' ) {

                $output  .= '<div id="' . esc_attr( $id ) . '_dots" class="ut-brands-dots"></div>';

            }

            /* done - let's reset */
            $this->client_carousel  = false;
            $this->client_animation = false;
            $this->client_display   = '';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div id="' . $outer_id .'" class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
                
            return $output;
        
        }
        
        function ut_create_inner_shortcode( $atts, $content = NULL ) {

            /**
             * @var $class
             * @array $classes
             */
            extract( shortcode_atts( array(
                'name'        => '',
                'logo'        => '',
                'url'         => '',        
                'target'      => '_blank',  /* deprecated */
                'class'       => ''        
            ), $atts ) );

            $classes = array( $class );

            $animation = '';

            if( $this->client_animation ) {
                $animation = 'animated';
            }

            /* prepare link setting */
            if( function_exists('vc_build_link') && !empty( $url ) && strpos( $url, 'url:') !== false ) {
                
                $link = vc_build_link( $url );
                
                $url    = !empty( $link['url'] )    ? $link['url'] : '#';
                $target = !empty( $link['target'] ) ? $link['target'] : '_self';
                $title  = !empty( $link['title'] )  ? $link['title'] : '';
                $rel    = !empty( $link['rel'] )    ? 'rel="' . esc_attr( trim( $link['rel'] ) ) . '"' : '';
                
            } else {
                
                $title  = !empty( $name )  ? $name : 'Image';
                $rel    = '';
                
            }
            
            /* start output */
            $output = '';
            
            if( $this->client_carousel ) {
                
                $output .= '<div class="ut-single-brand ' . esc_attr( implode( " ", $classes ) ) . '">';

                    if( $this->client_display == 'plain' || $this->client_display == 'cells' ) {

                        $output .= '<div class="ut-single-brand-logo">';

                    }

                    if( !empty( $url ) ) {
                        
                        $output .= '<a target="' . esc_attr( $target ) . '" href="' . esc_attr( $url ) . '" ' . $rel . '>';
                        
                    }
                    
                    /* fallback */
                    if( empty( $logo ) ) {
                        
                        $output .= '<img alt="' . esc_attr( $title )  . '" data-flickity-lazyload="' . esc_url( vc_asset_url( 'vc/no_image.png' ) ) . '">';
                        
                    }                    
                    
                    if( strpos( $logo, '.png' ) !== false || strpos( $logo, '.jpg' ) !== false || strpos( $logo, '.gif' ) !== false || strpos( $logo, '.ico' ) !== false || strpos( $logo, '.svg' ) !== false ) {
                        
                        $output .= '<img alt="' . esc_attr( $title )  . '" data-flickity-lazyload="' . esc_url( $logo ) . '">';
                    
                    } elseif( !empty( $logo ) ) {

                        $output .= '<img alt="' . esc_attr( $title )  . '" data-flickity-lazyload="' . wp_get_attachment_url( $logo ) . '">';
                    
                    }                    
                    
                    if( !empty( $url ) ) {    
                        
                        $output .= '</a>';
                        
                    }

                    if( $this->client_display == 'plain' || $this->client_display == 'cells' ) {

                    $output .= '</div>';

                    }
                
                $output .= '</div>';
            
            } else {

                $classes[] = 'ut-single-brand-' . $this->client_display;

                if( !empty( $url ) && $this->client_display == 'cells' ) {

                    $output .= '<a class="ut-single-brand ut-single-brand-link ' . esc_attr( implode( " ", $classes ) ) . '" target="' . esc_attr( $target ) . '" href="' . esc_attr( $url ) . '" ' . $rel . '>';

                } else {

                    $output  = '<div class="ut-single-brand ' . esc_attr( implode( " ", $classes ) ) . '">';

                }


                    if( $this->client_display == 'plain' || $this->client_display == 'cells' ) {

                        $output .= '<div class="ut-single-brand-logo">';

                    }

                    if( !empty( $url ) && $this->client_display == 'plain' ) {

                        $output .= '<a target="' . esc_attr( $target ) . '" href="' . esc_attr( $url ) . '" ' . $rel . '>';

                    }


                    if( strpos( $logo, '.png' ) !== false || strpos( $logo, '.jpg' ) !== false || strpos( $logo, '.gif' ) !== false || strpos( $logo, '.ico' ) !== false ) {
                        
                        $output .= '<img class="' . $animation . ' ut-lozad" alt="' . esc_attr( $title )  . '" data-src="' . esc_url( $logo ) . '">';
                    
                    } else {
                        
                        $output .= '<img class="' . $animation . ' ut-lozad" alt="' . esc_attr( $title )  . '" data-src="' . wp_get_attachment_url( $logo ) . '">';
                    
                    }

                    if( !empty( $url ) && $this->client_display == 'plain' ) {

                        $output .= '</a>';

                    }


                    if( $this->client_display == 'plain' || $this->client_display == 'cells' ) {

                    $output .= '</div>';

                    }

                if( !empty($url) && $this->client_display == 'cells' ) {

                    $output .= '</a>';

                } else {

                    $output .= '</div>';

                }
            
            }
            
            return $output;        
        
        }
        
    }

}

new UT_Client_Group;

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    
    class WPBakeryShortCode_ut_client_group extends WPBakeryShortCodesContainer {
    
    }
    
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_client extends WPBakeryShortCode {


        public function singleParamHtmlHolder( $param, $value ) {

            $output = '';

            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
            $type = isset( $param['type'] ) ? $param['type'] : '';
            $class = isset( $param['class'] ) ? $param['class'] : '';

            if ('attach_image' === $param['type'] && 'logo' === $param_name) {

                $output .= '<input type="hidden" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
                $element_icon = $this->settings('icon');
                $img = wpb_getImageBySize(array(
                    'attach_id' => (int)preg_replace('/[^\d]/', '', $value),
                    'thumb_size' => 'thumbnail',
                ));
                $this->setSettings('logo', ($img ? $img['thumbnail'] : '<img width="150" height="150" src="' . vc_asset_url('vc/blank.gif') . '" class="attachment-thumbnail vc_general vc_element-icon"  data-name="' . $param_name . '" alt="" title="" style="display: none;" />') . '<span class="no_image_image vc_element-icon' . (!empty($element_icon) ? ' ' . $element_icon : '') . ($img && !empty($img['p_img_large'][0]) ? ' image-exists' : '') . '" /><a href="#" class="column_edit_trigger' . ($img && !empty($img['p_img_large'][0]) ? ' image-exists' : '') . '">' . __('Add image', 'js_composer') . '</a>');
                $output .= $this->outputTitleTrue($this->settings['name']);

            } elseif (!empty($param['holder'])) {

                if ('input' === $param['holder']) {
                    $output .= '<' . $param['holder'] . ' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '">';
                } elseif (in_array($param['holder'], array('img', 'iframe'))) {
                    $output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="' . $value . '">';
                } elseif ('hidden' !== $param['holder']) {
                    $output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
                }

            }

            if ( ! empty( $param['admin_label'] ) && true === $param['admin_label'] ) {
                $output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . $param['heading'] . '</label>: ' . $value . '</span>';
            }

            return $output;
        }

        protected function outputTitle( $title ) {
            return '';
        }

        protected function outputTitleTrue( $title ) {
            return '<h4 class="wpb_element_title">' . $title . ' ' . $this->settings( 'logo' ) . '</h4>';
        }


    }
    
}