<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Service_Box' ) ) {
	
    class UT_Service_Box {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_service_box';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Service Box Horizontal', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Promote and demonstrate services, features or qualifications in a single box.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/service-box.png',
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
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Icon Size', 'ut_shortcodes' ),
                            'param_name'        => 'size',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'large', 'ut_shortcodes' ) => 'large',
                                esc_html__( 'medium', 'ut_shortcodes' ) => 'medium',
                                esc_html__( 'small', 'ut_shortcodes' ) => 'small'
                            ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Headline', 'ut_shortcodes' ),
                            'description'       => esc_html__( '', 'ut_shortcodes' ),
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
                            'type'              => 'textarea',
                            'heading'           => esc_html__( 'Text', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'param_name'        => 'content',
                            'group'             => 'General'
                        ),

                        /* Link */
                        array(
                            'type'              => 'vc_link',
                            'heading'           => esc_html__( 'Custom Link', 'ut_shortcodes' ),
                            'param_name'        => 'link',
                            'group'             => 'Link',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Link Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'link_font_size',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Link',
                            'value'             => array(
                                'default'   => '12',
                                'min'       => '0',
                                'max'       => '20',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Link Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'link_letter_spacing',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Link',
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
                            'heading'           => esc_html__( 'Link Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'link_text_transform',
                            'group'             => 'Link',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Link Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'link_font_weight',
                            'group'             => 'Link',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__('Select Font Weight', 'ut_shortcodes') => '',
                                esc_html__('normal', 'ut_shortcodes') => 'normal',
                                esc_html__('bold', 'ut_shortcodes') => 'bold',
                                esc_html__('100', 'ut_shortcodes') => '100',
                                esc_html__('200', 'ut_shortcodes') => '200',
                                esc_html__('300', 'ut_shortcodes') => '300',
                                esc_html__('400', 'ut_shortcodes') => '400',
                                esc_html__('500', 'ut_shortcodes') => '500',
                                esc_html__('600', 'ut_shortcodes') => '600',
                                esc_html__('700', 'ut_shortcodes') => '700',
                                esc_html__('800', 'ut_shortcodes') => '800',
                                esc_html__('900', 'ut_shortcodes') => '900'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Icon to Link?', 'unitedthemes' ),
                            'param_name'        => 'link_icon',
                            'group'             => 'Link',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                            )
                        ),

                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Icon library', 'ut_shortcodes' ),
                            'description'   => esc_html__( 'Select icon library.', 'ut_shortcodes' ),
                            'param_name'    => 'link_icon_type',
                            'group'         => 'Link',
                            'value'         => array(
                                esc_html__( 'Font Awesome', 'ut_shortcodes' ) => 'fontawesome',
                                esc_html__( 'Brooklyn Icons', 'ut_shortcodes' ) => 'bklynicons',
                            ),
                            'dependency' => array(
                                'element'   => 'link_icon',
                                'value'     => 'yes',
                            ),

                        ),

                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'link_icon_fontawesome',
                            'group'             => 'Link',
                            'dependency' => array(
                                'element'   => 'link_icon_type',
                                'value'     => 'fontawesome',
                            ),
                        ),

                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'link_icon_bklyn',
                            'group'             => 'Link',
                            'settings' => array(
                                'emptyIcon'     => true,
                                'type'          => 'bklynicons',
                            ),
                            'dependency' => array(
                                'element'   => 'link_icon_type',
                                'value'     => 'bklynicons',
                            ),

                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Icon Position', 'unitedthemes' ),
                            'param_name'        => 'link_icon_position',
                            'group'             => 'Link',
                            'value'             => array(
                                esc_html__( 'before', 'ut_shortcodes' ) => 'before',
                                esc_html__( 'after', 'ut_shortcodes' ) => 'after'
                            ),
                            'dependency'        => array(
                                'element' => 'link_icon',
                                'value'   => 'yes',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Hover Animation to Icon?', 'unitedthemes' ),
                            'param_name'        => 'link_icon_animation',
                            'group'             => 'Link',
                            'value'             => array(
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                            )
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


                        /* colors */
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'color',
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
                            'heading'           => esc_html__( 'Icon Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'background',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Headline Color', 'ut_shortcodes' ),
                            'param_name'        => 'headline_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'text_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Link Color', 'ut_shortcodes' ),
                            'param_name'        => 'link_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Link Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'link_hover_color',
                            'group'             => 'Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Link Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'link_icon_color',
                            'group'             => 'Colors',
                            'dependency'        => array(
                                'element' => 'link_icon',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Link Icon Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'link_icon_hover_color',
                            'group'             => 'Colors',
                            'dependency'        => array(
                                'element' => 'link_icon',
                                'value'   => 'yes',
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
                            'heading'           => esc_html__( 'Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
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
                'icon_type'         => 'fontawesome',
                'icon'              => '',
                'icon_bklyn'        => '',
				'icon_linea'		=> '',
				'icon_orion'		=> '',
                'imageicon'         => '',
                'size'              => 'large',
                'color'             => '#FFF',
                'headline'          => '',
                'headline_color'    => '',
                'headline_margin_bottom'  => '',
                'text_color'        => '',
                'background'        => get_option('ut_accentcolor' , '#F1C40F'),
                
                // link
                'link'                => '',
                'link_font_size'      => '12',
                'link_letter_spacing' => '',
                'link_color'          => '',
                'link_hover_color'    => '',
                'link_text_transform' => '',
                'link_font_weight'    => '',
                
                // link icon
                'link_icon'             => 'no',
                'link_icon_type'        => 'fontawesome',
                'link_icon_fontawesome' => 'fa fa-arrow-circle-right',
                'link_icon_bklyn'       => '',
                'link_icon_position'    => 'before',
                'link_icon_animation'   => 'yes',
                'link_icon_color'       => '',
                'link_icon_hover_color' => '',
                
                // Animation
                'effect'            => '',       
                'animate_once'      => 'yes',
                'animate_mobile'    => false,
                'animate_tablet'    => false,
                'delay'             => 'false',
                'delay_timer'       => '100',
                'animation_duration'=> '',                 
                'opacity'           => '1',      /* deprecated */
                'width'             => '',       /* deprecated */
                'last'              => 'false',  /* deprecated */
                'css'               => '',
                'class'             => ''                
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
                
                /* check if if color is hex */
                if ( preg_match( '/^#[a-f0-9]{6}$/i', $background ) ) {
                    $background = 'rgba(' .  ut_hex_to_rgb( $background )  . ',' . $opacity . ');';
                }
                
                
            /* deprecated - will be removed one day - end block */

            /* animation effect */
            $dataeffect = NULL;
            
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
                    
                    $icon = $icon_bklyn;
                    
                } elseif( $icon_type == 'lineaicons' && !empty( $icon_linea ) ) {
					
					$icon = $icon_linea;
					$classes_2[] = 'ut-icon-box-with-svg';					
					
                } elseif( $icon_type == 'orionicons' && !empty( $icon_orion ) ) {
					
					$icon = $icon_orion;
					$classes_2[] = 'ut-icon-box-with-svg';					
					
                } else {
                    
                    /* fallback */
                    if( strpos( $icon, 'fa fa-' ) === false ) {
                        $icon = str_replace('fa-', 'fa fa-', $icon );     
                    }
                    
                    $icon = str_replace('fa fa-', 'fa-4x fa fa-', $icon );  
                                    
                }     
                
            }
            
            /* inline css */
            $id = uniqid("ut_sb_");
            
            $css_style = '<style type="text/css">';
            
                // Design Options Gradient
                $vc_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, '.' ), $this->shortcode, $atts );
                $css_style .= ut_add_gradient_css( $vc_class, $atts );
            
                $css_style .= '#' . $id . ' .ut-icon-box .ut-service-box-icon { color: ' . $color . '; }';
                
                if( !empty( $background ) ) {
                    
                    $css_style .= '#' . $id . ' .ut-arrow-right { border-left: 10px solid ' .  $background . '; }';
                    $css_style .= '#' . $id . ' .ut-icon-box { background: ' .  $background . '; }';    
                    
                }
                
                if( $headline_color ) {
                    $css_style .= '#' . $id . ' .ut-info h3 { color: ' . $headline_color . '; }';  
                }
                
                if( $text_color ) {
                    $css_style .= '#' . $id . ' .ut-info p { color: ' . $text_color . '; }';     
                }
            
                if( $headline_margin_bottom ) {
                    $css_style .= '#' . $id . ' .ut-info p { margin-top: ' . $headline_margin_bottom . '; }';     
                }
                
                if( $link_color ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link { color: ' . $link_color . '; }';    
                }

                if( $link_hover_color ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link:hover { color: ' . $link_hover_color . '; }';
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link:active { color: ' . $link_hover_color . '; }'; 
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link:focus { color: ' . $link_hover_color . '; }';    
                }
                
                if( $link_icon_color ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link i { color: ' . $link_icon_color . '; }'; 
                }
            
                if( $link_icon_hover_color ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link:hover i { color: ' . $link_icon_hover_color . '; }';
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link:active i { color: ' . $link_icon_hover_color . '; }'; 
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link:focus i { color: ' . $link_icon_hover_color . '; }'; 
                }
            
                if( $link_text_transform ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link { text-transform: ' . $link_text_transform . '; }';    
                }

                if( $link_font_weight ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link { font-weight: ' . $link_font_weight . '; }';    
                }

                if( $link_font_size ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link { font-size: ' . $link_font_size . 'px; }';
                }

                if( $link_letter_spacing ) {
                    $css_style .= '#' . $id . ' .ut-info a.ut-service-box-link { letter-spacing: ' . $link_letter_spacing . 'em; }';
                }
            
            $css_style .= '</style>';              
            
            /* attributes string */
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );            
            
            /* start output */
            $output = '';
            
            /* add css */ 
            $output .= ut_minify_inline_css( $css_style );

            $output .= '<div id="' . $id . '" class="' . implode( ' ', $classes ) . ' clearfix">';
            
                $output .= '<div ' . $attributes . ' class="ut-icon-box ut-icon-box-' . $size . ' ' . implode( ' ', $classes_2 ) . '">';
            
                    $output .= '<div class="ut-arrow-right"></div>';
            
                    if( !empty( $icon ) ) {
                        
                        if( $image_icon ) {
                            
                            $output .= '<figure class="ut-custom-icon">';
	                            $output .= UT_Adaptive_Image::create_lazy( $icon, array( 'alt' => ( !empty( $headline ) ? esc_attr( $headline ) : 'icon' ) ) );
                            $output .= '</figure>';

                        } else {
                            
							if( $icon_type == 'lineaicons' || $icon_type == 'orionicons' ) {
								
								// fallback colors
								if ( empty( $atts['svg_color'] ) ) {
									$atts['svg_color'] = '#FFF';
								}
								
								$svg = new UT_Draw_SVG( uniqid("ut-svg-"), $atts );
								$output .= $svg->draw_svg_icon();
							
							} else {
							
                            	$output .= '<i class="' . $icon . ' ut-service-box-icon"></i>';
                            
							}
								
                        }
                        
                    }
            
                $output .= '</div>';
                
                $output .= '<div class="ut-info">';
                    
                    if( !empty( $headline ) ) {
                        $output .= '<h3>' . $headline . '</h3>';
                    }
                    
                    if( !empty( $content ) ) {
                        $output .= '<p>' . do_shortcode($content) . '</p>';
                    }
                    
                    if( function_exists('vc_build_link') && $link ) {
                
                        $link = vc_build_link( $link );
                        
                        $target = !empty( $link['target'] ) ? $link['target'] : '_self';
                        $title  = !empty( $link['title'] )  ? $link['title'] : '';
                        $rel    = !empty( $link['rel'] )    ? 'rel="' . esc_attr( trim( $link['rel'] ) ) . '"' : '';
                        $link   = !empty( $link['url'] )    ? $link['url'] : '';
                        
                        $link_custom_icon = false;
                        $link_custom_classes = array();
                        
                        // icon settings
                        if( $link_icon_type == 'bklynicons' && !empty( $link_icon_bklyn ) ) {

                            $link_custom_icon = $link_icon_bklyn;

                        } else {

                            if( strpos( $link_icon_fontawesome, 'fa fa-' ) === false ) {

                                $link_custom_icon = str_replace('fa-', 'fa fa-', $link_icon_fontawesome );     

                            } else {

                                $link_custom_icon = $link_icon_fontawesome;

                            }

                        }
                        
                        if( $link_icon == 'yes' ) {
                            
                            $link_custom_classes[] = 'ut-service-box-link-icon-' . $link_icon_position;
                            
                            if( $link_icon_animation == 'yes' ) {
                                $link_custom_classes[] = 'ut-service-box-link-with-animation';
                            }
                                
                        }
                        
                        if( !empty( $link ) ) {                                                        
                            
                            $output .= '<a class="ut-service-box-link ' . implode(" ", $link_custom_classes ) . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '" ' . $rel . '>';
                            
                            if( $link_icon == 'yes' && $link_custom_icon && $link_icon_position == 'before' ) {

                                $output .= '<i class="' . $link_custom_icon . '"></i>';    

                            }
                            
                            $output .= $title;
                            
                            if( $link_icon == 'yes' && $link_custom_icon && $link_icon_position == 'after' ) {

                                $output .= '<i class="' . $link_custom_icon . '"></i>';    

                            }
                            
                            $output .= '</a>';
                            
                            
                        }
                        
                    }
            
                $output .= '</div>';
                
            $output .= '</div>';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
            
            return $output;
        
        }
            
    }

}

new UT_Service_Box;