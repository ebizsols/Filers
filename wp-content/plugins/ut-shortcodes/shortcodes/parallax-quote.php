<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Parallax_Quote' ) ) {

    class UT_Parallax_Quote {

        private $shortcode;

        function __construct() {

            /* shortcode base */
            $this->shortcode = 'ut_parallax_quote';

            add_action( 'init', array( $this, 'ut_map_shortcode' ) );

        }

        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Parallax Quote', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Please this element inside a fullwidth row.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'category'        => 'Community',
                    // 'icon'            => 'fa fa-quote-left ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/single-quote.png',
                    'class'           => 'ut-vc-icon-module ut-community-module',
                    'content_element' => true,
                    'params'          => array(

                        /* General Settings */
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
			                    esc_html__( 'Custom Icon', 'ut_shortcodes' ) => 'custom',
		                    ),

	                    ),
	                    array(
		                    'type'              => 'iconpicker',
		                    'heading'           => esc_html__( 'Choose Icon ( optional )', 'ut_shortcodes' ),
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
		                    'heading'           => esc_html__( 'Upload an own Icon', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'recommended size 64x64', 'ut_shortcodes' ),
		                    'param_name'        => 'imageicon',
		                    'group'             => 'General',
		                    'dependency' => array(
			                    'element'   => 'icon_type',
			                    'value'     => 'custom',
		                    ),
	                    ),

                        array(
                            'type'              => 'textarea',
                            'heading'           => esc_html__( 'Quote', 'ut_shortcodes' ),
                            'param_name'        => 'content',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'checkbox',
                            'heading'           => esc_html__( 'Hide Linebreaks on Tablet', 'ut_shortcodes' ),
                            'param_name'        => 'quote_linebreak_tablet',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value' 			=> array('yes, please!' => 'on' ),
                            'std'				=> ''
                        ),

                        array(
                            'type'              => 'checkbox',
                            'heading'           => esc_html__( 'Hide Linebreaks on Mobile', 'ut_shortcodes' ),
                            'param_name'        => 'quote_linebreak_mobile',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value' 			=> array('yes, please!' => 'on' ),
                            'std'				=> 'on',
                            'save_always' 		=> true
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Cite', 'ut_shortcodes' ),
                            'param_name'        => 'cite',
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Quotation Marks?', 'ut_shortcodes' ),
                            'param_name'        => 'quotation_marks',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'yes' , 'ut_shortcodes' ) => 'yes',
                                esc_html__( 'no' , 'ut_shortcodes' )  => 'no'
                            )
                        ),

	                    /* Responsive Settings */
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Spacing Bottom.', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_spacing_bottom',
		                    'group'             => 'Responsive',
		                    'value'             => array(
			                    'default' => '20',
			                    'min'     => '0',
			                    'max'     => '160',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
	                    ),

	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Tablet Icon Spacing Bottom.', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_tablet_spacing_bottom',
		                    'group'             => 'Responsive',
		                    'value'             => array(
			                    'default' => '20',
			                    'min'     => '0',
			                    'max'     => '160',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
	                    ),

	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Mobile Icon Spacing Bottom.', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_mobile_spacing_bottom',
		                    'group'             => 'Responsive',
		                    'value'             => array(
			                    'default' => '20',
			                    'min'     => '0',
			                    'max'     => '160',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Alignment', 'ut_shortcodes' ),
		                    'param_name'        => 'align',
		                    'group'             => 'Responsive',
		                    'value'             => array(
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Alignment Tablet', 'ut_shortcodes' ),
		                    'param_name'        => 'align_tablet',
		                    'group'             => 'Responsive',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'center', 'ut_shortcodes' )  => 'center',
			                    esc_html__( 'left'  , 'ut_shortcodes' )  => 'left',
			                    esc_html__( 'right' , 'ut_shortcodes' )  => 'right',
		                    ),
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Alignment Mobile', 'ut_shortcodes' ),
		                    'param_name'        => 'align_mobile',
		                    'group'             => 'Responsive',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'center', 'ut_shortcodes' )  => 'center',
			                    esc_html__( 'left'  , 'ut_shortcodes' )  => 'left',
			                    esc_html__( 'right' , 'ut_shortcodes' )  => 'right',
		                    ),
	                    ),

	                    /* SVG Animation */
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Draw SVG Icons?', 'ut_shortcodes' ),
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
		                    'heading'           => esc_html__( 'Draw Type', 'ut_shortcodes' ),
		                    'description'		=> esc_html__( 'Defines what kind of animation will be used.', 'ut_shortcodes' ),
		                    'param_name'        => 'draw_svg_type',
		                    'group'             => 'Draw Settings',
		                    'value'             => array(
			                    esc_html__( 'oneByOne', 'ut_shortcodes' ) 		=> 'oneByOne',
			                    esc_html__( 'delayed', 'ut_shortcodes' ) 		    => 'delayed',
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

                        /* Color Settings */
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_color',
		                    'group'             => 'Quote Styling',
		                    'dependency' => array(
			                    'element'   => 'icon_type',
			                    'value'     => array('bklynicons','fontawesome'),
		                    ),
	                    ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
		                    'param_name'        => 'svg_color',
		                    'group'             => 'Quote Styling',
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
		                    'group'             => 'Quote Styling',
		                    'dependency' => array(
			                    'element'   => 'icon_type',
			                    'value'     => array('orionicons'),
		                    ),
	                    ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Icon Background Color', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_background_color',
		                    'group'             => 'Quote Styling',
		                    'dependency' => array(
			                    'element'   => 'icon_type',
			                    'value'     => array('lineaicons', 'orionicons', 'bklynicons', 'fontawesome'),
		                    ),
	                    ),

	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Border Radius', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_border_radius',
		                    'value'             => array(
			                    'min'   => '0',
			                    'max'   => '50',
			                    'step'  => '1',
			                    'unit'  => '%'
		                    ),
		                    'group'             => 'Quote Styling',
		                    'dependency' => array(
			                    'element'   => 'icon_type',
			                    'value'     => array('lineaicons', 'orionicons', 'bklynicons', 'fontawesome'),
		                    ),
	                    ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Quote Color', 'ut_shortcodes' ),
                            'param_name'        => 'quote_color',
                            'group'             => 'Quote Styling'
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Quotation Marks Color', 'ut_shortcodes' ),
                            'param_name'        => 'quotation_marks_color',
                            'group'             => 'Quote Styling',
                            'dependency'        => array(
                                'element'           => 'quotation_marks',
                                'value'             => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Quote Highlight Color', 'ut_shortcodes' ),
                            'description'       => sprintf( esc_html__( '(optional) - use: %s inside your quote to apply this color.', 'ut_shortcodes' ), '<xmp class="ut-code-usage"><ins>Word</ins></xmp>' ),
                            'param_name'        => 'quote_ins_color',
                            'group'             => 'Quote Styling'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Cite Color', 'ut_shortcodes' ),
                            'param_name'        => 'cite_color',
                            'group'             => 'Quote Styling'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Cite Border Top?', 'unitedthemes' ),
                            'param_name'        => 'cite_custom_border',
                            'group'             => 'Quote Styling',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'cite_border_color',
                            'group'             => 'Quote Styling',
                            'dependency'        => array(
                                'element' => 'cite_custom_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Border Style', 'ut_shortcodes' ),
                            'param_name'        => 'cite_border_style',
                            'group'             => 'Quote Styling',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'cite_custom_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Border Width', 'ut_shortcodes' ),
                            'param_name'        => 'cite_border_width',
                            'value'             => array(
                                'min'   => '1',
                                'max'   => '10',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'group'             => 'Quote Styling',
                            'dependency'        => array(
                                'element' => 'cite_custom_border',
                                'value'   => 'yes',
                            ),
                        ),

                        /* Quote Font Settings */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'quote_font_source',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => '',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google',
                                esc_html__( 'Custom Font', 'ut_shortcodes' )    => 'custom',
                            ),
                            'group'             => 'Quote Font'
                        ),

                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'quote_google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Quote Font',
                            'settings'          => array(
                                'fields' => array(
                                    'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                                    'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                                ),
                            ),
                            'dependency'        => array(
                                'element'           => 'quote_font_source',
                                'value'             => 'google',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Websafe Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'quote_websafe_fonts',
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
                            'group'             => 'Quote Font',
                            'dependency'        => array(
                                'element'           => 'quote_font_source',
                                'value'             => 'websafe',
                            ),

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Custom Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'quote_custom_fonts',
                            'group'             => 'Quote Font',
                            'value'             => ut_get_custom_fonts(),
                            'dependency'        => array(
                                'element'           => 'quote_font_source',
                                'value'             => 'custom',
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Quote Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'quote_font_size',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '100',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'group'             => 'Quote Font'
                        ),

                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Unit', 'unitedthemes' ),
                            'param_name' => 'quote_line_height_unit',
                            'edit_field_class' => 'vc_col-sm-1',
                            'std' => 'px',
                            'group' => 'Quote Font',
                            'value' => array(
                                esc_html__( 'px', 'unitedthemes' ) => 'px',
                                esc_html__( '%', 'unitedthemes' ) => 'percent',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Quote Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'quote_line_height',
                            'group'             => 'Quote Font',
                            'edit_field_class'  => 'vc_col-sm-11',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '100',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'dependency'        => array(
                                'element'           => 'quote_line_height_unit',
                                'value'             => 'px',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Quote Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'quote_line_height_percent',
                            'group'             => 'Quote Font',
                            'edit_field_class'  => 'vc_col-sm-11',
                            'value'             => array(
                                'default' => '100',
                                'min'     => '0',
                                'max'     => '200',
                                'step'    => '1',
                                'unit'    => '%'
                            ),
                            'dependency'        => array(
                                'element'           => 'quote_line_height_unit',
                                'value'             => 'percent',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Quote Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'quote_letter_spacing',
                            'group'             => 'Quote Font',
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
                            'heading'           => esc_html__( 'Quote Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'quote_font_weight',
                            'group'             => 'Quote Font',
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
                                esc_html__('900', 'ut_shortcodes') => '900',
                            ),
                            'dependency'        => array(
                                'element'           => 'quote_font_source',
                                'value'             => 'websafe',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Quote Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'quote_text_transform',
                            'group'             => 'Quote Font',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),


                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Quote Highlight Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'quote_ins_font_weight',
                            'group'             => 'Quote Font',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' ) => 'bold',
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


                        /* Cite Font Settings */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'cite_font_source',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => '',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google'
                            ),
                            'group'             => 'Cite Font'
                        ),

                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'cite_google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Cite Font',
                            'settings'          => array(
                                'fields'        => array(
                                    'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                                    'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                                ),
                            ),
                            'dependency'        => array(
                                'element'           => 'cite_font_source',
                                'value'             => 'google',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'cite_websafe_fonts',
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
                            'group'             => 'Cite Font',
                            'dependency'        => array(
                                'element'           => 'cite_font_source',
                                'value'             => 'websafe',
                            ),

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Cite Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'cite_font_size',
                            'group'             => 'Cite Font',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '30',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),

                        ),

                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Unit', 'unitedthemes' ),
                            'param_name' => 'cite_line_height_unit',
                            'edit_field_class' => 'vc_col-sm-1',
                            'std' => 'px',
                            'group' => 'Cite Font',
                            'value' => array(
                                esc_html__( 'px', 'unitedthemes' ) => 'px',
                                esc_html__( '%', 'unitedthemes' ) => 'percent',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Cite Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'cite_line_height',
                            'edit_field_class' => 'vc_col-sm-11',
                            'group'             => 'Cite Font',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '60',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'dependency'        => array(
                                'element'           => 'cite_line_height_unit',
                                'value'             => 'px',
                            ),

                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Cite Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'cite_line_height_percent',
                            'group'             => 'Cite Font',
                            'edit_field_class' => 'vc_col-sm-11',
                            'value'             => array(
                                'default' => '100',
                                'min'     => '0',
                                'max'     => '200',
                                'step'    => '1',
                                'unit'    => '%'
                            ),
                            'dependency'        => array(
                                'element'           => 'cite_line_height_unit',
                                'value'             => 'percent',
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Cite Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'cite_letter_spacing',
                            'group'             => 'Cite Font',
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
                            'heading'           => esc_html__( 'Cite Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'cite_font_weight',
                            'group'             => 'Cite Font',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' ) => 'bold',
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
                            'heading'           => esc_html__( 'Cite Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'cite_text_transform',
                            'group'             => 'Cite Font',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),

                        /* reveal fx */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Reveal Effect?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'The reveal duration for this effect is 500ms.', 'ut_shortcodes' ),
                            'param_name'        => 'revealfx',
                            'group'             => 'Reveal Effect',
                            'value'             => array(
                                esc_html__( 'no, thanks!' , 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!' , 'ut_shortcodes' ) => 'on'
                            ),

                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Reveal Effect Color', 'ut_shortcodes' ),
                            'param_name'        => 'revealfx_color',
                            'group'             => 'Reveal Effect',
                            'dependency'        => array(
                                'element'           => 'revealfx',
                                'value'             => 'on',
                            ),
                        ),

                        /* glow effect */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Quote Glow Effect?', 'ut_shortcodes' ),
                            'param_name'        => 'glow_effect_quote',
                            'group'             => 'Glow & Stroke',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'yes'
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Quote Glow Color', 'ut_shortcodes' ),
                            'param_name'        => 'glow_color_quote',
                            'group'             => 'Glow & Stroke',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'glow_effect_quote',
                                'value'   => array('yes'),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Quote Glow Text Shadow Color', 'ut_shortcodes' ),
                            'param_name'        => 'glow_shadow_color_quote',
                            'group'             => 'Glow & Stroke',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'glow_effect_quote',
                                'value'   => array('yes'),
                            )
                        ),

                        array(
                            'type'       => 'dropdown',
                            'heading'    => esc_html__( 'Activate Quote Text Stroke?', 'ut_shortcodes' ),
                            'param_name' => 'stroke_effect_quote',
                            'group'      => 'Glow & Stroke',
                            'value'      => array(
                                esc_html__( 'no', 'ut_shortcodes' )  => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'        => 'colorpicker',
                            'heading'     => esc_html__( 'Stroke Color', 'ut_shortcodes' ),
                            'param_name'  => 'stroke_color_quote',
                            'group'       => 'Glow & Stroke',
                            'edit_field_class' => 'vc_col-sm-6',
                            'dependency'  => array(
                                'element' => 'stroke_effect_quote',
                                'value'   => array('yes'),
                            )
                        ),
                        array(
                            'type'       => 'range_slider',
                            'heading'    => esc_html__( 'Stroke Width', 'ut_shortcodes' ),
                            'param_name' => 'stroke_width_quote',
                            'group'      => 'Glow & Stroke',
                            'edit_field_class' => 'vc_col-sm-6',
                            'value'      => array(
                                'default' => '1',
                                'min'     => '1',
                                'max'     => '3',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'stroke_effect_quote',
                                'value'   => array('yes'),
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Cite Glow Effect?', 'ut_shortcodes' ),
                            'param_name'        => 'glow_effect_cite',
                            'group'             => 'Glow & Stroke',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'yes'
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Cite Glow Color', 'ut_shortcodes' ),
                            'param_name'        => 'glow_color_cite',
                            'group'             => 'Glow & Stroke',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'glow_effect_cite',
                                'value'   => array('yes'),
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Cite Glow text Shadow Color', 'ut_shortcodes' ),
                            'param_name'        => 'glow_shadow_color_cite',
                            'group'             => 'Glow & Stroke',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'glow_effect_cite',
                                'value'   => array('yes'),
                            )
                        ),

                        /* css */
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
                        )

                    )

                )

            ); /* end mapping */



        }

    }

}

new UT_Parallax_Quote;


if ( class_exists( 'WPBakeryShortCode' ) ) {

    class WPBakeryShortCode_ut_parallax_quote extends WPBakeryShortCode {

        /**
         * Used to get field name in vc_map function for google_fonts, font_container and etc..
         *
         * @param $key
         *
         * @since 4.4
         * @return bool
         */
        protected function getField( $key ) {
            return isset( $this->fields[ $key ] ) ? $this->fields[ $key ] : false;
        }

        /**
         * Get param value by providing key
         *
         * @param $key
         *
         * @since 4.4
         * @return array|bool
         */
        protected function getParamData( $key ) {
            return WPBMap::getParam( $this->shortcode, $this->getField( $key ) );
        }


        /**
         * Parses google_fonts_data to get needed css styles to markup
         *
         * @param $el_class
         * @param $css
         * @param $google_fonts_data
         * @param $font_container_data
         * @param $atts
         *
         * @since 4.3
         * @return array
         */
        public function getStyles( $quote_google_fonts_data, $cite_google_fonts_data,  $atts ) {

            $quote_styles = array();
            $quote_font_source = empty( $atts['quote_font_source'] ) ? '' : $atts['quote_font_source'];

            if ( 'google' === $quote_font_source && ! empty( $quote_google_fonts_data ) && isset( $quote_google_fonts_data['values'], $quote_google_fonts_data['values']['font_family'], $quote_google_fonts_data['values']['font_style'] ) ) {
                $google_fonts_family = explode( ':', $quote_google_fonts_data['values']['font_family'] );
                $quote_styles[] = 'font-family:' . $google_fonts_family[0];
                $google_fonts_styles = explode( ':', $quote_google_fonts_data['values']['font_style'] );
                $quote_styles[] = 'font-weight:' . $google_fonts_styles[1];
                $quote_styles[] = 'font-style:' . $google_fonts_styles[2];
            }

            $cite_styles = array();
            $cite_font_source = empty( $atts['cite_font_source'] ) ? '' : $atts['cite_font_source'];

            if ( 'google' === $cite_font_source && ! empty( $cite_google_fonts_data ) && isset( $cite_google_fonts_data['values'], $cite_google_fonts_data['values']['font_family'], $cite_google_fonts_data['values']['font_style'] ) ) {
                $google_fonts_family = explode( ':', $cite_google_fonts_data['values']['font_family'] );
                $cite_styles[] = 'font-family:' . $google_fonts_family[0];
                $google_fonts_styles = explode( ':', $cite_google_fonts_data['values']['font_style'] );
                $cite_styles[] = 'font-weight:' . $google_fonts_styles[1];
                $cite_styles[] = 'font-style:' . $google_fonts_styles[2];
            }

            return array(
                'quote_inline_styles' => $quote_styles,
                'cite_inline_styles'  => $cite_styles
            );

        }


        /**
         * Parses shortcode attributes and set defaults based on vc_map function relative to shortcode and fields names
         *
         * @param $atts
         *
         * @since 4.3
         * @return array
         */
        public function getAttributes( $atts ) {
            /**
             * Shortcode attributes
             * @var $google_fonts
             * @var $font_container
             * @var $link
             * @var $css
             */
            $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
            extract( $atts );

            /**
             * Get default values from VC_MAP.
             */
            $google_fonts_obj = new Vc_Google_Fonts();

            $quote_google_fonts_field  = $this->getParamData( 'quote_google_fonts' );
            $cite_google_fonts_field   = $this->getParamData( 'cite_google_fonts' );

            $quote_google_fonts_field_settings = isset( $quote_google_fonts_field['settings'], $quote_google_fonts_field['settings']['fields'] ) ? $quote_google_fonts_field['settings']['fields'] : array();
            $quote_google_fonts_data = strlen( $quote_google_fonts ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $quote_google_fonts_field_settings, $quote_google_fonts ) : '';

            $cite_google_fonts_field_settings = isset( $cite_google_fonts_field['settings'], $cite__google_fonts_field['settings']['fields'] ) ? $cite_google_fonts_field['settings']['fields'] : array();
            $cite_google_fonts_data = strlen( $cite_google_fonts ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $cite_google_fonts_field_settings, $cite_google_fonts ) : '';

            return array(
                'quote_google_fonts'      => $quote_google_fonts,
                'quote_google_fonts_data' => $quote_google_fonts_data,
                'cite_google_fonts'       => $cite_google_fonts,
                'cite_google_fonts_data'  => $cite_google_fonts_data,
            );

        }

        protected function content( $atts, $content = null ) {

            extract( shortcode_atts( array (

                'quote_linebreak_tablet' => '',
                'quote_linebreak_mobile' => 'on',

                /* icon settings */
                'icon_type'                  => 'fontawesome',
                'icon'                       => '',
                'icon_bklyn'                 => '',
                'icon_linea'                 => '',
                'icon_orion'                 => '',
                'imageicon'                  => '',
                'icon_spacing_bottom'        => '',
                'icon_tablet_spacing_bottom' => '',
                'icon_mobile_spacing_bottom' => '',
                'align'                      => 'center',
                'align_tablet'               => 'inherit',
                'align_mobile'               => 'inherit',

                'icon_color'            => '',
                'icon_border_radius'    => '',
                'icon_background_color' => '',
                'quotation_marks'       => 'yes',

                /* reveal effect */
                'revealfx'              => 'off',
                'revealfx_color'        => get_option('ut_accentcolor'),

                /* quote settings */
                'quote_color'                => '',
                'quote_font_size'            => '',
                'quote_line_height'          => '',
                'quote_line_height_percent'  => '100',
                'quote_line_height_unit'     => 'px',
                'quote_font_weight'          => '',
                'quote_letter_spacing'       => '0',
                'quote_text_transform'       => '',
                'quotation_marks_color'      => '',
                'quote_font_source'          => '',
                'quote_google_fonts'         => '',
                'quote_websafe_fonts'        => '',
                'quote_custom_fonts'         => '',
                'quote_ins_color'            => '',
                'quote_ins_font_weight'      => '',

                /* cite settings */
                'cite'                      => '',
                'cite_color'                => '',
                'cite_font_size'            => '',
                'cite_line_height'          => '',
                'cite_line_height_percent'  => '100',
                'cite_line_height_unit'     => 'px',
                'cite_font_weight'          => '',
                'cite_letter_spacing'       => '0',
                'cite_text_transform'       => '',
                'cite_font_source'          => '',
                'cite_google_fonts'         => '',
                'cite_websafe_fonts'        => '',

                /* cite border */
                'cite_custom_border'    => 'no',
                'cite_border_color'     => '#FFF',
                'cite_border_style'     => 'solid',
                'cite_border_width'     => '1',

                /* glow colors */
                'glow_effect_quote'          => '',
                'glow_color_quote'           => get_option('ut_accentcolor' , '#F1C40F'),
                'glow_shadow_color_quote'    => 'black',
                'glow_effect_cite'           => '',
                'glow_color_cite'            => get_option('ut_accentcolor' , '#F1C40F'),
                'glow_shadow_color_cite'     => 'black',
                'stroke_effect_quote'        => '',
                'stroke_color_quote'         => get_option('ut_accentcolor' , '#F1C40F'),
                'stroke_width_quote'         => '1',

                /* misc */
                'fontsize'              => '', /* deprecated */
                'el_class'              => '',
                'css'                   => '',
                'class'                 => ''

            ), $atts ) );

            if( ( $cite_font_source && $cite_font_source == 'google' ) || ( $quote_font_source && $quote_font_source == 'google' ) ) {

                /* google font settings */
                extract( $this->getAttributes( $atts ) );
                extract( $this->getStyles( $quote_google_fonts_data, $cite_google_fonts_data, $atts ) );

                /* subsets */
                $settings = get_option( 'wpb_js_google_fonts_subsets' );
                if ( is_array( $settings ) && ! empty( $settings ) ) {
                    $subsets = '&subset=' . implode( ',', $settings );
                } else {
                    $subsets = '';
                }

                /* quote font */
                if ( $quote_font_source && isset( $quote_google_fonts_data['values']['font_family'] ) ) {
                    wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $quote_google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $quote_google_fonts_data['values']['font_family'] . $subsets . '&display=swap' );
                }

                /* cite font */
                if ( $cite_font_source && isset( $cite_google_fonts_data['values']['font_family'] ) ) {
                    wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $cite_google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $cite_google_fonts_data['values']['font_family'] . $subsets . '&display=swap' );
                }

            }

            /* deprecated - fallback to older versions */
            if( $fontsize && !$quote_font_size ) {
                $quote_font_size = $fontsize;
            }

            // unique ID for blockquote reveal effects
            $reveal_id = uniqid("ut_reveal_");

            /* start output */
            $output = '';
            $reveal_classes = array();
            $reveal_wrap_classes = array();
	        $icon_wrap_classes = array();
	        $icon_classes = array();

            // reveal fx
            if( $revealfx == 'on' ) {

                // extra classes
                $reveal_classes[] = 'ut-block-reveal-hide';
                $reveal_wrap_classes[] = 'ut-reveal-fx ut-element-with-block-reveal';

            }

            // hide linebreak on tablet
            if( $quote_linebreak_tablet ) {
                $reveal_classes[] = 'ut-no-parallax-quote-linebreak-tablet';
            }

            // hide linebreak on mobile
            if( $quote_linebreak_mobile ) {
                $reveal_classes[] = 'ut-no-parallax-quote-linebreak-mobile';
            }

	        $reveal_wrap_classes[] = 'ut-parallax-quote-align-' . $align;
	        $icon_wrap_classes[] = 'ut-parallax-icon-wrap-align-' . $align;

	        // Responsive
	        $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
	        $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

	        $icon_wrap_classes[] = 'ut-parallax-icon-wrap-tablet-align-' . $align_tablet;
	        $icon_wrap_classes[] = 'ut-parallax-icon-wrap-mobile-align-' . $align_mobile;

	        $reveal_wrap_classes[] = 'ut-parallax-quote-tablet-align-' . $align_tablet;
	        $reveal_wrap_classes[] = 'ut-parallax-quote-mobile-align-' . $align_mobile;

            /* unique ID */
            $id = uniqid("ut_p_quote_");

            $css_style = '<style type="text/css">';

	        $icon_spacing_bottom        = $icon_spacing_bottom != '' ? $icon_spacing_bottom : 20;
	        $icon_tablet_spacing_bottom = $icon_tablet_spacing_bottom != '' ? $icon_tablet_spacing_bottom : 20;
	        $icon_mobile_spacing_bottom = $icon_mobile_spacing_bottom != '' ? $icon_mobile_spacing_bottom : 20;

	        if( $align == 'left' ) {

		        $css_style .= '#' . $id . ' { text-align: left; }';
		        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 auto ' . $icon_spacing_bottom . 'px 0; }';

	        }

	        if( $align == 'right' ) {

		        $css_style .= '#' . $id . ' { text-align: right; }';
		        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 0 ' . $icon_spacing_bottom . 'px auto; }';

	        }

	        if( $align == 'center' ) {

		        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 auto ' . $icon_spacing_bottom . 'px auto; }';

	        }


	        if( $align_tablet != 'inherit' ) {

		        $css_style .= '@media (min-width: 768px) and (max-width: 1024px) {';

		        $css_style .= '#' . $id . ' { text-align: ' . $align_tablet . '; }';

		        if( $align_tablet == 'left' )
			        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 auto ' . $icon_tablet_spacing_bottom . 'px 0; }';

		        if( $align_tablet == 'right' )
			        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 0 ' . $icon_tablet_spacing_bottom . 'px auto; }';

		        if( $align_tablet == 'center' )
			        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 auto ' . $icon_tablet_spacing_bottom . 'px auto; }';

		        $css_style .= '}';

	        }

	        if( $align_mobile != 'inherit' ) {

		        $css_style .= '@media (max-width: 767px) {';

		        $css_style .= '#' . $id . ' { text-align: ' . $align_mobile . '; }';

		        if( $align_mobile == 'left' )
			        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 auto ' . $icon_mobile_spacing_bottom . 'px 0; }';

		        if( $align_mobile == 'right' )
			        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 0 ' . $icon_mobile_spacing_bottom . 'px auto; }';

		        if( $align_mobile == 'center' )
			        $css_style .= '#' . $id . ' .ut-parallax-icon-wrap { margin: 0 auto ' . $icon_mobile_spacing_bottom . 'px auto; }';

		        $css_style .= '}';

	        }

            if( $icon_color ) {
                $css_style .= '#' . $id . ' .ut-parallax-icon { color: ' . $icon_color . '; }';
            }

            if( $icon_background_color ) {
                $css_style .= '#' . $id . ' .ut-parallax-icon { background: ' . $icon_background_color . '; }';
            }
            if( $icon_border_radius ) {
                $css_style .= '#' . $id . ' .ut-parallax-icon { border-radius: ' . $icon_border_radius . '%; -moz-border-radius: ' . $icon_border_radius . '%; -webkit-border-radius: ' . $icon_border_radius . '%; }';
            }

            /* quote font settings */
            if ( ! empty( $quote_inline_styles ) ) {

                $css_style .= '#' . $id . ' blockquote { '. esc_attr( implode( ';', $quote_inline_styles ) ). ' }';

            }

            if( $quote_color ) {
                $css_style .= '#' . $id . ' blockquote { color: ' . $quote_color . '; }';
            }
            if( $quotation_marks_color ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-title .fa-quote-left { color: ' . $quotation_marks_color . '; }';
                $css_style .= '#' . $id . ' .ut-parallax-quote-title .fa-quote-right { color: ' . $quotation_marks_color . '; }';
            }
            if( $quote_font_size ) {
                $fontsize = str_replace('px', '', $quote_font_size );
                $css_style .= '#' . $id . ' blockquote { font-size: ' . $quote_font_size . 'px; }';
            }
            if( $quote_font_source && $quote_font_source == 'websafe' ) {
                $css_style .= '#' . $id . ' blockquote { font-family: ' . get_websafe_font_css_family( $quote_websafe_fonts ) . '; }';
            }

            if( $quote_font_source && $quote_font_source == 'custom' && $quote_custom_fonts ) {

                $font_family = get_term($quote_custom_fonts,'unite_custom_fonts');

                if( isset( $font_family->name ) )
                    $css_style .= '#' . $id . ' blockquote { font-family: "' . $font_family->name . '"; }';

            }

            if( $quote_line_height_unit == 'px' && $quote_line_height ) {
                $css_style .= '#' . $id . ' blockquote { line-height: ' . $quote_line_height . 'px; }';
            }
            if( $quote_line_height_unit == 'percent' && $quote_line_height_percent ) {
                $css_style .= '#' . $id . ' blockquote { line-height: ' . $quote_line_height_percent . '%; }';
            }
            if( $quote_font_weight ) {
                $css_style .= '#' . $id . ' blockquote { font-weight: ' . $quote_font_weight . '; }';
            }
            if( $quote_letter_spacing ) {

                // fallback letter spacing
                if( $quote_letter_spacing >= 1 || $quote_letter_spacing <= -1 ) {
                    $quote_letter_spacing = $quote_letter_spacing / 100;
                }

                $css_style .= '#' . $id . ' blockquote { letter-spacing: ' . $quote_letter_spacing . 'em; }';

            }
            if( $quote_text_transform ) {
                $css_style .= '#' . $id . ' blockquote { text-transform: ' . $quote_text_transform . '; }';
            }
            if( $quote_ins_color ) {
                $css_style .= '#' . $id . ' blockquote ins { background-color: transparent; color: ' . $quote_ins_color . '; }';
            }
            if( $quote_ins_font_weight ) {
                $css_style .= '#' . $id . ' blockquote ins { font-weight: ' . $quote_ins_font_weight . '; }';
            }

            /* cite font settings */
            if ( ! empty( $cite_inline_styles ) ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { ' . esc_attr( implode( ';', $cite_inline_styles ) ) . ' }';
            }

            if( $cite_font_size ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { font-size: ' . $cite_font_size . 'px; }';
            }
            if( $cite_font_source && $cite_font_source == 'websafe' ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { font-family: ' . get_websafe_font_css_family( $cite_websafe_fonts ) . '; }';
            }
            if( $cite_line_height_unit == 'px' && $cite_line_height ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { line-height: ' . $cite_line_height . 'px; }';
            }
            if( $cite_line_height_unit == 'percent' && $cite_line_height_percent ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { line-height: ' . $cite_line_height_percent . '%; }';
            }
            if( $cite_font_weight ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { font-weight: ' . $cite_font_weight . '; }';
            }
            if( $cite_letter_spacing ) {

                // fallback letter spacing
                if( $cite_letter_spacing >= 1 || $cite_letter_spacing <= -1 ) {
                    $cite_letter_spacing = $cite_letter_spacing / 100;
                }

                $css_style .= '#' . $id . ' .ut-parallax-quote-name { letter-spacing: ' . $cite_letter_spacing . 'em; }';

            }
            if( $cite_text_transform ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { text-transform: ' . $cite_text_transform . '; }';
            }
            if( $cite_color ) {
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { color: ' . $cite_color . '; }';
            }
            if( $cite_custom_border == 'yes' ) {

                $css_style .= '#' . $id . ' .ut-parallax-quote-name-wrap { margin-top: 20px; }';
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { display: inline-block; border-top: ' . $cite_border_width . 'px ' . $cite_border_style . ' ' . $cite_border_color . '  }';

            }

            //if( $revealfx == 'on' ) {

                $css_style .= '#' . $id . ' .ut-parallax-quote-name-wrap { margin-top: 20px; }';
                $css_style .= '#' . $id . ' .ut-parallax-quote-name { margin-top: 0; }';

            // }

            if( $glow_effect_quote == 'yes'  ) {

                $css_style.= '#' . $id . ' blockquote { 
                                -webkit-text-shadow: 0 0 20px ' . $glow_color_quote . ',  2px 2px 3px ' . $glow_shadow_color_quote . ';
                                   -moz-text-shadow: 0 0 20px ' . $glow_color_quote . ',  2px 2px 3px ' . $glow_shadow_color_quote . ';
                                        text-shadow: 0 0 20px ' . $glow_color_quote . ',  2px 2px 3px ' . $glow_shadow_color_quote . '; }';

            }

            if( $stroke_effect_quote == 'yes' ) {

                $css_style.= '#' . $id . ' blockquote {
                        -moz-text-stroke-color: ' . $stroke_color_quote .';
                        -webkit-text-stroke-color: ' . $stroke_color_quote .';
                        -moz-text-stroke-width: ' . $stroke_width_quote .'px;  
                        -webkit-text-stroke-width: ' . $stroke_width_quote .'px;	            
                    }';

            }

            if( $glow_effect_cite == 'yes' ) {

                $css_style.= '#' . $id . ' .ut-parallax-quote-name { 
                                -webkit-text-shadow: 0 0 20px ' . $glow_color_cite . ',  2px 2px 3px ' . $glow_shadow_color_cite . ';
                                   -moz-text-shadow: 0 0 20px ' . $glow_color_cite . ',  2px 2px 3px ' . $glow_shadow_color_cite . ';
                                        text-shadow: 0 0 20px ' . $glow_color_cite . ',  2px 2px 3px ' . $glow_shadow_color_cite . '; 
                            }';

            }

            $css_style .= '</style>';

	        /* icon setting */
	        if( !empty( $imageicon ) && is_numeric( $imageicon ) ) {

		        $imageicon = wp_get_attachment_url( $imageicon );

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
			        $icon_classes[] = 'ut-parallax-icon-with-svg';


		        } elseif( $icon_type == 'orionicons' && !empty( $icon_orion ) ) {

			        $icon = $icon_orion;
			        $icon_classes[] = 'ut-parallax-icon-with-svg';

		        } else {

			        /* fallback */
			        if( strpos( $icon, 'fa fa-' ) === false ) {
				        $icon = str_replace('fa-', 'fa fa-', $icon );
			        }

		        }

		        if( $icon_background_color ) {

			        $icon_classes[] = 'ut-parallax-icon-with-background';

		        }

	        }

            /* attach css */
            $output .= ut_minify_inline_css( $css_style );

            $output .= '<div id="' . $id . '" data-revealfx-color="' . esc_attr( $revealfx_color ) . '" class="ut-parallax-quote ' . implode( " ", $reveal_wrap_classes ) . '">';

	        if( $image_icon ) {

		        $output .= '<div class="ut-parallax-icon-wrap ' . implode( " ", $icon_wrap_classes ) . '">';

			        $output .= '<div class="ut-parallax-icon">';

			            $output .= '<img class="ut-lozad" alt="' . ( !empty( $headline ) ? esc_attr( $headline ) : 'icon' ) . '" data-src="' . esc_url( $icon ) . '">';

			        $output .= '</div>';

		        $output .= '</div>';

	        } elseif( !empty( $icon ) ) {

		        $output .= '<div class="ut-parallax-icon-wrap ' . implode( " ", $icon_wrap_classes ) . '">';

			        $output .= '<div class="ut-parallax-icon ' . implode( " ", $icon_classes ) . '">';

			        if ( $icon_type == 'bklynicons' ) {

				        $output .= '<i class="' . $icon . '"></i>';

			        } elseif ( $icon_type == 'lineaicons' || $icon_type == 'orionicons' ) {

				        $svg    = new UT_Draw_SVG( ut_get_unique_id( "ut-svg-", true ), $atts );
				        $output .= $svg->draw_svg_icon();

			        } else {

				        $output .= '<i class="fa ' . $icon . '"></i>';

			        }

			        $output .= '</div>';

		        $output .= '</div>';

	        }

            $output .= '<blockquote data-appear-top-offset="-200" id="' . esc_attr( $reveal_id ) . '" class="ut-parallax-quote-title ' . implode( " ", $reveal_classes ) . ' element-with-custom-line-height ' . esc_attr( $class ) . '">';

            if( $quotation_marks == 'yes' ) {
                $output .=  '<span class="quote-left">"</span>';
            }

            $output .=  do_shortcode( $content );

            if( $quotation_marks == 'yes' ) {
                $output .=  '<span class="quote-right">"</span>';
            }

            $output .= '</blockquote><br />';

            if( !empty( $cite ) ) {

                if( $revealfx == 'on' ) { $output .= '<div>'; }

                $output .= '<div class="ut-parallax-quote-name-wrap">';

                    $output .= '<span class="ut-parallax-quote-name">' . $cite . '</span>';

                $output .= '</div>';

                if( $revealfx == 'on' ) { $output .= '</div>'; }

            }

            $output .= '</div>';

            if( defined( 'WPB_VC_VERSION' ) ) {

                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>';

            }

            return $output;


        }

    }

}