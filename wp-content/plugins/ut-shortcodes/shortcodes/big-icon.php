<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Big_Icon' ) ) {
	
    class UT_Big_Icon {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_big_icon';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Big Icon', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Big Icons for better and greater user attention.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    //'icon'            => 'fa fa-star ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/big-icon.png',
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
                                esc_html__( 'Orion Icons (with animated draw)', 'ut_shortcodes' ) => 'orionicons'
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
                            'description'       => esc_html__( 'recommended size 128x128', 'ut_shortcodes' ),
                            'param_name'        => 'imageicon',
                            'group'             => 'General',
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Icon Size', 'ut_shortcodes' ),
                            'param_name'        => 'icon_size',
                            'value'             => array(
                                'default' => '60',
                                'min'     => '40',
                                'max'     => '100',
                                'step'    => '10',
                                'unit'    => 'px'
                            ),
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Icon Shape', 'ut_shortcodes' ),
                            'param_name'        => 'shape',
                            'group'             => 'General',
                            'value'             => array(
	                            esc_html__( 'round', 'ut_shortcodes' ) => 'round',
	                            esc_html__( 'square', 'ut_shortcodes' ) => 'square',
	                            esc_html__( 'normal', 'ut_shortcodes' ) => 'normal',
	                            esc_html__( 'outline', 'ut_shortcodes' ) => 'outline'
                            ),
                        ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Border Radius', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_border_radius',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'General',
		                    'value'             => array(
			                    'default'   => '0',
			                    'min'       => '0',
			                    'max'       => '60',
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
		                    'group'             => 'General',
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
                            'heading'           => esc_html__( 'Icon Spacing Bottom', 'ut_shortcodes' ),
                            'param_name'        => 'icon_margin_bottom',
                            'group'             => 'General',
                            'value'             => array(
                                'default' => '20',
                                'min'     => '0',
                                'max'     => '80',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),
                        ),

	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'General',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'Icon Link', 'ut_shortcodes' ),
	                    ),

                        array(
                            'type'              => 'vc_link',
                            'heading'           => esc_html__( 'Link', 'ut_shortcodes' ),
                            'param_name'        => 'link',
                            'group'             => 'General'
                        ),

	                    array(
		                    'param_name'        => 'link_class',
	                    	'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Link CSS Class', 'ut_shortcodes' ),
		                    'group'             => 'General'
	                    ),

	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'General',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'Title and Slogan', 'ut_shortcodes' ),
	                    ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Title', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'param_name'        => 'title',
                            'group'             => 'General'
                        ),
	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Slogan', 'ut_shortcodes' ),
		                    'param_name'        => 'slogan',
		                    'group'             => 'General'
	                    ),

	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'Font Settings',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'Title', 'ut_shortcodes' ),
	                    ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Title Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'title_text_transform',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' )            => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' )              => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' )              => 'lowercase'
                            ),
                        ),

	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'Font Settings',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'Slogan', 'ut_shortcodes' ),
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Slogan Text Transform', 'ut_shortcodes' ),
		                    'param_name'        => 'slogan_text_transform',
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
                            'heading'           => esc_html__( 'Slogan Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'slogan_font_weight',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' )             => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )               => 'bold',
                                esc_html__( '100' , 'ut_shortcodes' )                => '100',
                                esc_html__( '200' , 'ut_shortcodes' )                => '200',
                                esc_html__( '300' , 'ut_shortcodes' )                => '300',
                                esc_html__( '400' , 'ut_shortcodes' )                => '400',
                                esc_html__( '500' , 'ut_shortcodes' )                => '500',
                                esc_html__( '600' , 'ut_shortcodes' )                => '600',
                                esc_html__( '700' , 'ut_shortcodes' )                => '700',
                                esc_html__( '800' , 'ut_shortcodes' )                => '800',
                                esc_html__( '900' , 'ut_shortcodes' )                => '900'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Slogan Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'slogan_letter_spacing',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),
                        ),

                        /*array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Text Alignment for Title and Slogan', 'ut_shortcodes' ),
                            'param_name'        => 'text_align',
                            'group'             => 'General',
                            'value'             => array(
                                'center'    => esc_html__( 'center', 'ut_shortcodes' ),
                                'left'      => esc_html__( 'left'  , 'ut_shortcodes' ),
                                'right'     => esc_html__( 'right' , 'ut_shortcodes' ),
                            ),
                        ),*/

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
                            'param_name'        => 'icon_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',
                            'dependency' => array(
                                'element'   => 'icon_type',
                                'value'     => array('bklynicons','fontawesome'),
                            ),
                        ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Icon Hover Color', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_hover_color',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Colors',
		                    'dependency' => array(
			                    'element'   => 'icon_type',
			                    'value'     => array('bklynicons','fontawesome'),
		                    ),
	                    ),

	                    // Animated Icons
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

	                    // Background
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Icon Background / Outline Color', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_background',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Colors',
		                    'dependency'        => array(
			                    'element' => 'shape',
			                    'value'   => array( 'round', 'square', 'outline' ),
		                    )
	                    ),

	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Icon Background / Outline Hover Color', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_hover_background',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Colors',
		                    'dependency'        => array(
			                    'element' => 'shape',
			                    'value'   => array( 'round', 'square', 'outline' ),
		                    )
	                    ),

						// Icon Hover Color
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
                            'heading'           => esc_html__( 'Title Color', 'ut_shortcodes' ),
                            'param_name'        => 'title_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors',

                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Slogan Color', 'ut_shortcodes' ),
                            'param_name'        => 'slogan_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Colors'
                        ),



                    )

                )

            ); /* end mapping */
        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'icon_type'         	=> 'fontawesome',
				'icon'                  => '',
                'icon_bklyn'        	=> '',
				'icon_linea'			=> '',
				'icon_orion'			=> '',
				'imageicon'             => '',
				'icon_size'             => '',
                'icon_margin_bottom'    => '',
                'shape'                 => 'round',
                'icon_border_radius'    => '0',
                'icon_border_width'     => '2',
                'title'                 => '',
                'title_color'           => '',
                'title_text_transform'  => '',
                'slogan'                => '',
                'slogan_color'          => '',
                'slogan_font_weight'    => '',
                'slogan_letter_spacing' => '',
                'slogan_text_transform' => '',
                'text_align'            => 'center',
                'link'                  => '#',
                'icon_color'            => '',  
                'icon_hover_color'      => '',
                'icon_background'       => '',
                'icon_hover_background' => '',
	            'link_class'            => ''
            ), $atts ) ); 
            
            if( !empty( $link ) ) {
            
                /* attract link settings */
                $link = vc_build_link( $link );
                
                /* set link attributes */
                $link['target'] = empty( $link['target'] ) ? '_self' : $link['target'];
                $link['title']  = empty( $link['title'] )  ? ''      : 'title="' . esc_attr( $link['title'] ) . '"';
                $link['url']    = empty( $link['url'] )    ? ''      : $link['url'];
                $rel            = empty( $link['rel'] )    ? ''      : 'rel="' . $link['rel'] . '"';
            
            }
            
            /* icon setting */
            if( !empty( $imageicon ) && is_numeric( $imageicon ) ) {
                $imageicon = wp_get_attachment_url( $imageicon );        
            }            
            
            /* overwrite default icon */
            $icon = empty( $imageicon ) ? $icon : $imageicon;
            
            /* check if icon is an image */
            $image_icon = strpos( $icon, '.png' ) !== false || strpos( $icon, '.jpg' ) !== false || strpos( $icon, '.gif' ) !== false || strpos( $icon, '.ico' ) !== false || strpos( $icon, '.svg' ) !== false ? true : false;
            
            /* inline css */
            $id = uniqid("ut_bi_");
            
            $css_style = '<style type="text/css">';
                
                if( !empty( $icon_color ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-link i { color: ' . $icon_color . '; }';    
                }
                
                if( !empty( $icon_background ) && $shape != 'normal' ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-link .bklyn-big-icon { background: ' . $icon_background . '; }';    
                }
                
                if( !empty( $icon_hover_color ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-link:hover i { color: ' . $icon_hover_color . '; }';    
                }
                
                if( !empty( $icon_hover_background )  && $shape != 'normal' ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-link:hover .bklyn-big-icon { background: ' . $icon_hover_background . '; }';    
                }

		        if( $shape == 'outline' ) {

			        $css_style .= '#' . $id . ' .bklyn-big-icon-link .bklyn-big-icon { background: transparent !important; }';

			        if( $icon_border_radius ) {
				        $css_style .= '#' . $id . ' .bklyn-big-icon-link .bklyn-big-icon { border-radius: ' . $icon_border_radius . 'px; }';
			        }

			        $icon_background = !empty( $icon_background ) ? $icon_background : '#FFF';

			        if( $icon_border_width ) {
				        $css_style .= '#' . $id . ' .bklyn-big-icon-link .bklyn-big-icon { border: ' . $icon_border_width . 'px solid ' . $icon_background . '; }';
			        }

			        if( !empty( $icon_hover_background ) ) {
				        $css_style .= '#' . $id . ' .bklyn-big-icon-link:hover .bklyn-big-icon { border-color: ' . $icon_hover_background . '; }';
			        }


		        }

                if( $icon_margin_bottom != '' ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon { margin-bottom: ' . $icon_margin_bottom . 'px; }';
                }
            
                if( !empty( $slogan_color ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-slogan { color: ' . $slogan_color . '; }';    
                }
                
                if( !empty( $slogan_font_weight ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-slogan { font-weight: ' . $slogan_font_weight . '; }';    
                }
            
                if( !empty( $slogan_letter_spacing ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-slogan { letter-spacing: ' . $slogan_letter_spacing . 'em; }';    
                }
                
                if( !empty( $title_text_transform ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-title { text-transform: ' . $title_text_transform . '; }';
                }
                    
                if( !empty( $slogan_text_transform ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-slogan { text-transform: ' . $slogan_text_transform . '; }';                    
                }                
                
                if( !empty( $title_color ) ) {
                    $css_style .= '#' . $id . ' .bklyn-big-icon-title { color: ' . $title_color . '; }';    
                }
                
                if( !empty( $icon_size ) ) {
                    
                    $css_style .= '
                    #' . $id . ' .bklyn-big-icon-round.bklyn-big-icon .fa, 
                    #' . $id . ' .bklyn-big-icon-square.bklyn-big-icon .fa { 
                        line-height: ' . ( $icon_size ) . 'px;
                    }                    
                    #' . $id . ' .bklyn-big-icon img {
                        max-height: ' . ( $icon_size ) . 'px;
                    }                     
                    #' . $id . ' .bklyn-big-icon { 
                        height: ' . ( $icon_size * 2 ) . 'px;
                        line-height: ' . ( $icon_size * 2 ) . 'px;
                        width: ' . ( $icon_size * 2 ) . 'px;
                        font-size: ' . ( $icon_size ) . 'px;
                    }					
					#' . $id . ' .bklyn-big-icon svg {
						transform: scale(' . ( $icon_size / 100 ) * 2 . ');
					}';
                    
                    
                }
                
            $css_style .= '</style>';
            
            /* start output */
            $output = '';
            
            /* add css */ 
            $output .= ut_minify_inline_css( $css_style );            
            
            $output .= '<div id="' . $id . '" class="bklyn-big-icon-wrap">';
            
				if( !empty( $link['url'] ) ) {
			
                	$output .= '<a ' . $link['title'] . ' class="bklyn-big-icon-link ' . $link_class . '" target="' . esc_attr( $link['target'] ) . '" href="' . esc_url( $link['url'] ) . '" ' . $rel . '>';
                
				} else {
					
					$output .= '<div class="bklyn-big-icon-link ' . $link_class . '">';
					
				}
					
                    if( !$image_icon ) {
                    
                        $output .= '<div class="bklyn-big-icon bklyn-big-icon-' . esc_attr( $shape ) . '"><div class="bklyn-big-icon-inner">';
						
							if( $icon_type == 'lineaicons' || $icon_type == 'orionicons' ) {
								
								$svg = new UT_Draw_SVG( uniqid("ut-svg-"), $atts, $id );
								$output .= $svg->draw_svg_icon();
								
							} else {
								
								if( $icon_type == 'bklynicons' && !empty( $icon_bklyn ) ) {
                    
									$icon = $icon_bklyn;

								}								
								
								$output .= '<i class="' . esc_attr( $icon ) . '"></i>';
						
							}
						
						$output .= '</div></div>';
                    
                    } else {
                        
                        $output .= '<div class="bklyn-big-icon bklyn-big-icon-' . esc_attr( $shape ) . '"><div class="bklyn-big-icon-inner"><img alt="' . ( !empty( $title ) ? esc_attr( $title ) : 'icon' ) . '" src="' . esc_url( $icon ) . '"></div></div>'; 
                    
                    }
                    
                    
                    if( !empty( $title ) ) {
                    
                        $output .= '<h3 class="bklyn-big-icon-title">' . $title . '</h3>';
                    
                    }
                    
                    if( !empty( $slogan ) ) {
                        
                        $output .= '<div class="bklyn-big-icon-slogan">' . $slogan . '</div>';
                    
                    }
            
				if( !empty( $link['url'] ) ) {
			
                	$output .= '</a>';
                
				} else {
					
					$output .= '</div>';
					
				}
					
            $output .= '</div>';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element">' . $output . '</div>'; 
            
            }
                            
            return $output;
        
        }
            
    }

}

new UT_Big_Icon;