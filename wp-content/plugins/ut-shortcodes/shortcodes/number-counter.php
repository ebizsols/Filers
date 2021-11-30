<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Number_Counter' ) ) {
	
    class UT_Number_Counter {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_number_counter';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Number Counter Module', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Animated number counters are a fun and effective way to display stats to your visitors.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'         => 'fa fa-sort-numeric-asc ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/number-counter.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

	                    /* array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Style', 'unitedthemes' ),
		                    'param_name'        => 'type',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'default (swap numbers)', 'ut_shortcodes' )  => 'countup',
			                    esc_html__( 'slot machine effect', 'ut_shortcodes' ) => 'slot'
		                    )
	                    ),*/

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Counter Prefix', 'ut_shortcodes' ),
                            'param_name'        => 'prefix',
                            'edit_field_class'  => 'vc_col-sm-2',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Count Up to', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'edit_field_class'  => 'vc_col-sm-8',
                            'param_name'        => 'to',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Counter Suffix', 'ut_shortcodes' ),
                            'param_name'        => 'suffix',
                            'edit_field_class'  => 'vc_col-sm-2',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Separator?', 'unitedthemes' ),
                            'description'       => esc_html__( 'Example 3856 becomes 3.856 or 40000 becomes 40.000', 'ut_shortcodes' ),
                            'param_name'        => 'sep',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true'
                            )
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Separator', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional). By default its a . (dot) as separator.', 'ut_shortcodes' ),
                            'param_name'        => 'sep_sign',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'dependency' => array(
                                'element'   => 'sep',
                                'value'     => 'true',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Count Up Speed', 'ut_shortcodes' ),
                            'param_name'        => 'speed',
                            'group'             => 'General',
                            'value'             => array(
                                'default'   => '2000',
                                'min'       => '100',
                                'max'       => '5000',
                                'step'      => '50',
                                'unit'      => 'ms'
                            ),
                        ),
	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'General',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'Alignments', 'ut_shortcodes' ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Counter Alignment', 'ut_shortcodes' ),
		                    'param_name'        => 'counter_align',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Prefix Vertical Align', 'ut_shortcodes' ),
		                    'param_name'        => 'prefix_vertical_align',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'middle', 'ut_shortcodes' ) => 'middle',
			                    esc_html__( 'top'  , 'ut_shortcodes' ) => 'top',
			                    esc_html__( 'bottom' , 'ut_shortcodes' ) => 'bottom',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Suffix Vertical Align', 'ut_shortcodes' ),
		                    'param_name'        => 'suffix_vertical_align',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'middle', 'ut_shortcodes' ) => 'middle',
			                    esc_html__( 'top'  , 'ut_shortcodes' ) => 'top',
			                    esc_html__( 'bottom' , 'ut_shortcodes' ) => 'bottom',
		                    ),
	                    ),


                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),


	                    // Counter Icon
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Icon Position', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_position',
		                    'group'             => 'Icon',
		                    'value'             => array(
			                    esc_html__( 'top', 'ut_shortcodes' ) => 'top',
			                    esc_html__( 'left', 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'right', 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'          => 'dropdown',
		                    'heading'       => esc_html__( 'Icon library', 'ut_shortcodes' ),
		                    'description'   => esc_html__( 'Select icon library.', 'ut_shortcodes' ),
		                    'param_name'    => 'icon_type',
		                    'group'         => 'Icon',
		                    'value'         => array(
			                    esc_html__( 'Brooklyn Icons', 'ut_shortcodes' ) => 'bklynicons',
			                    esc_html__( 'Font Awesome', 'ut_shortcodes' ) => 'fontawesome',
			                    esc_html__( 'Linea Icons (with animated draw)', 'ut_shortcodes' ) => 'lineaicons',
			                    esc_html__( 'Orion Icons (with animated draw)', 'ut_shortcodes' ) => 'orionicons',
		                    ),

	                    ),
	                    array(
		                    'type'              => 'iconpicker',
		                    'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
		                    'param_name'        => 'icon',
		                    'group'             => 'Icon',
		                    'dependency' => array(
			                    'element'   => 'icon_type',
			                    'value'     => 'fontawesome',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'iconpicker',
		                    'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_bklyn',
		                    'group'             => 'Icon',
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
		                    'group'             => 'Icon',
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
		                    'group'             => 'Icon',
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
		                    'group'             => 'Icon',
	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Size', 'ut_shortcodes' ),
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'param_name'        => 'icon_font_size',
		                    'group'             => 'Icon',
		                    'value'             => array(
			                    'default' => '60',
			                    'min'     => '20',
			                    'max'     => '200',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Spacing Bottom', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_spacing',
		                    'group'             => 'Icon',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'value'             => array(
			                    'default'   => '10',
			                    'min'       => '0',
			                    'max'       => '100',
			                    'step'      => '1',
			                    'unit'      => 'px'
		                    ),
		                    'dependency' => array(
			                    'element'   => 'icon_position',
			                    'value'     => 'top',
		                    ),

	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Icon Spacing', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_spacing_lr',
		                    'group'             => 'Icon',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'value'             => array(
			                    'default'   => '20',
			                    'min'       => '0',
			                    'max'       => '20',
			                    'step'      => '1',
			                    'unit'      => 'px'
		                    ),
		                    'dependency' => array(
			                    'element'   => 'icon_position',
			                    'value'     => array('left', 'right'),
		                    ),

	                    ),

                        // Counter Colors
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Counter Caption', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'param_name'        => 'content',
                            'group'             => 'Caption'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Counter Caption Text Transform', 'unitedthemes' ),
                            'param_name'        => 'caption_text_transform',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Caption',
                            'value'             => array(
                                esc_html__( 'None', 'ut_shortcodes' )        => 'none',
                                esc_html__( 'Capitalize', 'ut_shortcodes' )  => 'capitalize',
                                esc_html__( 'Inherit', 'ut_shortcodes' )     => 'inherit',
                                esc_html__( 'Lowercase', 'ut_shortcodes' )   => 'lowercase',
                                esc_html__( 'Uppercase', 'ut_shortcodes' )   => 'uppercase'
                            ),

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Counter Caption Font Weight', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'caption_font_weight',
                            'group'             => 'Caption',
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
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Counter Caption Font Size', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'caption_font_size',
                            'group'             => 'Caption',
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
                            'heading'           => esc_html__( 'Counter Caption Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'caption_letter_spacing',
                            'group'             => 'Caption',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Counter Caption Margin Top', 'ut_shortcodes' ),
                            'param_name'        => 'caption_margin_top',
                            'group'             => 'Caption',
                            'value'             => array(
                                'default' => '10',
                                'min'     => '0',
                                'max'     => '100',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),

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

                        // Counter Colors
                        array(
                            'type'              => 'gradient_picker',
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
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Counter Color', 'ut_shortcodes' ),
                            'param_name'        => 'counter_color',
                            'group'             => 'Colors',
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Caption Color', 'ut_shortcodes' ),
                            'param_name'        => 'desccolor',
                            'group'             => 'Colors',
                        ),


                        // Font Settings
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'counter_font_source',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => '',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google',
                                esc_html__( 'Custom Font', 'ut_shortcodes' ) => 'custom'
                            ),
                            'group'             => 'Counter Font'
                        ),

                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'counter_google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Counter Font',
                            'settings'          => array(
                                'fields' => array(
                                    'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                                    'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                                ),
                            ),
                            'dependency'        => array(
                                'element'           => 'counter_font_source',
                                'value'             => 'google',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Websafe Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'counter_websafe_fonts',
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
                            'group'             => 'Counter Font',
                            'dependency'        => array(
                                'element'           => 'counter_font_source',
                                'value'             => 'websafe',
                            ),

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Custom Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'counter_custom_fonts',
                            'group'             => 'Counter Font',
                            'value'             => ut_get_custom_fonts(),
                            'dependency'        => array(
                                'element'           => 'counter_font_source',
                                'value'             => 'custom',
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Counter Font Size', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'counter_font_size',
                            'group'             => 'Counter Font',
                            'value'             => array(
                                'default' => '60',
                                'min'     => '0',
                                'max'     => '200',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),

                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Counter Prefix Font Weight', 'ut_shortcodes' ),
		                    'param_name'        => 'counter_prefix_font_weight',
		                    'group'             => 'Counter Font',
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
			                    esc_html__( '900' , 'ut_shortcodes' )                => '900',

		                    ),
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Counter Suffix Font Weight', 'ut_shortcodes' ),
		                    'param_name'        => 'counter_suffix_font_weight',
		                    'group'             => 'Counter Font',
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
			                    esc_html__( '900' , 'ut_shortcodes' )                => '900',

		                    ),
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Activate Glow Effect?', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Not supported by Title Style 1.', 'ut_shortcodes' ),
		                    'param_name'        => 'glow_effect',
		                    'group'             => 'Glow & Stroke',
		                    'value'             => array(
			                    esc_html__( 'no', 'ut_shortcodes' ) => 'no',
			                    esc_html__( 'yes'  , 'ut_shortcodes' ) => 'yes'
		                    ),
		                    'dependency'        => array(
			                    'element' => 'style',
			                    'value_not_equal_to' => array('pt-style-1'),
		                    )
	                    ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Glow Color', 'ut_shortcodes' ),
		                    'param_name'        => 'glow_color',
		                    'group'             => 'Glow & Stroke',
		                    'dependency'        => array(
			                    'element' => 'glow_effect',
			                    'value'   => array('yes'),
		                    )
	                    ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Glow Text Shadow Color', 'ut_shortcodes' ),
		                    'param_name'        => 'glow_shadow_color',
		                    'group'             => 'Glow & Stroke',
		                    'dependency'        => array(
			                    'element' => 'glow_effect',
			                    'value'   => array('yes'),
		                    )
	                    ),
	                    array(
		                    'type'       => 'dropdown',
		                    'heading'    => esc_html__( 'Activate Text Stroke?', 'ut_shortcodes' ),
		                    'param_name' => 'stroke_effect',
		                    'group'      => 'Glow & Stroke',
		                    'value'      => array(
			                    esc_html__( 'no', 'ut_shortcodes' )  => 'no',
			                    esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
		                    )
	                    ),
	                    array(
		                    'type'       => 'colorpicker',
		                    'heading'    => esc_html__( 'Stroke Color', 'ut_shortcodes' ),
		                    'param_name' => 'stroke_color',
		                    'group'      => 'Glow & Stroke',
		                    'dependency'        => array(
			                    'element' => 'stroke_effect',
			                    'value'   => array('yes'),
		                    )
	                    ),
	                    array(
		                    'type'       => 'range_slider',
		                    'heading'    => esc_html__( 'Stroke Width', 'ut_shortcodes' ),
		                    'param_name' => 'stroke_width',
		                    'group'      => 'Glow & Stroke',
		                    'value'      => array(
			                    'default' => '1',
			                    'min'     => '1',
			                    'max'     => '3',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
		                    'dependency'        => array(
			                    'element' => 'stroke_effect',
			                    'value'   => array('yes'),
		                    )
	                    ),

	                    // Design Options
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),



                    )

                )

            ); // end mapping
                

        
        }
            
    }

}

new UT_Number_Counter;


if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_number_counter extends WPBakeryShortCode {
        
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
        public function getStyles( $counter_google_fonts_data, $atts ) {
            
            $counter_styles = array();
            $counter_font_source = empty( $atts['counter_font_source'] ) ? '' : $atts['counter_font_source'];
                        
            if ( 'google' === $counter_font_source && ! empty( $counter_google_fonts_data ) && isset( $counter_google_fonts_data['values'], $counter_google_fonts_data['values']['font_family'], $counter_google_fonts_data['values']['font_style'] ) ) {
                $google_fonts_family = explode( ':', $counter_google_fonts_data['values']['font_family'] );
                $counter_styles[] = 'font-family:"' . $google_fonts_family[0]. '" !important';
                $google_fonts_styles = explode( ':', $counter_google_fonts_data['values']['font_style'] );
                $counter_styles[] = 'font-weight:' . $google_fonts_styles[1];
                $counter_styles[] = 'font-style:' . $google_fonts_styles[2];
            }
            
            return array(
                'counter_inline_styles' => $counter_styles,
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
                     
            $counter_google_fonts_field  = $this->getParamData( 'counter_google_fonts' );
    
            $counter_google_fonts_field_settings = isset( $counter_google_fonts_field['settings'], $counter_google_fonts_field['settings']['fields'] ) ? $counter_google_fonts_field['settings']['fields'] : array();
            $counter_google_fonts_data = strlen( $counter_google_fonts ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $counter_google_fonts_field_settings, $counter_google_fonts ) : '';
            
            return array(
                'counter_google_fonts'      => $counter_google_fonts,
                'counter_google_fonts_data' => $counter_google_fonts_data,
            );
            
        }


	    /**
	     * Create Slot Machine HTML Markup
	     *
	     * @param string $number
	     * @param string $filter_id
	     *
	     * @return string $html
	     *
	     * @since Brooklyn 4.9.5
	     */

        public function create_slot_markup( $number, $filter_id ) {

        	$html = array();

	        $single_numbers = str_split( $number );

	        foreach( $single_numbers as $number ) {

	        	$html[] = '<span class="ut-slot-machine-number" data-filter="#' . $filter_id . '" data-value="' . esc_attr( $number ) . '"><span class="ut-slot-machine-number-value">' . $number . '</span></span>';

	        }

			return implode( '', $html );

        }


	    public function numberOfDecimals( $value ) {

		    if ( (int) $value == $value ) {

		    	return 0;

		    } else if ( ! is_numeric( $value ) ) {

		    	// throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
			    return false;

		    }

		    return strlen( $value ) - strrpos( $value, '.' ) - 1;

	    }
        
        protected function content( $atts, $content = null ) {
            
            extract( shortcode_atts( array (
                'type'                  => 'countup',
            	'icon_type'             => 'bklynicons',
                'icon_position'         => 'top',
                'icon'                  => '',
                'icon_bklyn'            => '',
				'icon_linea'			=> '',
				'icon_orion'			=> '',
                'imageicon'             => '',
				
				// SVG 
				'svg_color'				=> '',
				'draw_svg_icons'		=> 'yes',
				'draw_svg_type'			=> 'oneByOne',
				'draw_svg_duration' 	=> '100',
				
                'icon_font_size'        => '',
                'icon_spacing'          => '',
                'icon_spacing_lr'       => '',
                'color'                 => '',
                'counter_color'         => '',
                'desccolor'             => '',
                'caption_text_transform'=> '',
                'caption_letter_spacing'=> '',
                'caption_font_size'     => '12',
                'caption_font_weight'   => '',
                'caption_margin_top'    => '10',
                'counter_font_size'     => '',
                'counter_font_source'   => '',
                'counter_google_fonts'  => '',
                'counter_websafe_fonts' => '',
				'counter_custom_fonts'	=> '',
                'counter_prefix_font_size'   => '',
                'counter_prefix_font_weight' => '',
                'counter_suffix_font_size'   => '',
                'counter_suffix_font_weight' => '',
                'counter_align'         => 'center',
                'prefix_vertical_align' => 'middle',
                'suffix_vertical_align' => 'middle',
                'speed'                 => '2000',
                'to'                    => '1250',
                'suffix'                => '',
                'prefix'                => '',
                'sep'                   => 'false',
                'sep_sign'              => '',

                'glow_effect'           => 'global',
                'glow_color'            => get_option('ut_accentcolor' , '#F1C40F'),
                'glow_shadow_color'     => 'black',
                'stroke_effect'         => 'global',
                'stroke_color'          => '',
                'stroke_width'          => '1',

                'opacity'               => '0.8',      /* deprecated */
                'width'                 => '',         /* deprecated */
                'last'                  => 'false',    /* deprecated */
                'animate_once'          => 'no',       /* deprecated */
                'background'            => '',         /* deprecated */
                'css'                   => '',
                'class'                 => ''
            ), $atts ) ); 

            if( $counter_font_source && $counter_font_source == 'google' ) {
                
                /* google font settings */
                extract( $this->getAttributes( $atts ) );
                extract( $this->getStyles( $counter_google_fonts_data, $atts ) );
                
                /* subsets */            
                $settings = get_option( 'wpb_js_google_fonts_subsets' );
                if ( is_array( $settings ) && ! empty( $settings ) ) {
                    $subsets = '&subset=' . implode( ',', $settings );
                } else {
                    $subsets = '';
                }
                
                /* quote font */
                if ( $counter_font_source && isset( $counter_google_fonts_data['values']['font_family'] ) ) {
                    
                    wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $counter_google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $counter_google_fonts_data['values']['font_family'] . $subsets . '&display=swap' );
                    
                }
            
            }

            // id's
	        $id         = uniqid("ut_sc_");
            $counter_id = uniqid("ut_sc_count_");
            $filter_id  = uniqid("ut_sc_filter_");

            // classes
	        $classes = array();
	        $count_classes = array();

            // suffix / prefix tags
            $tags = array(
            	'middle' => 'span',
            	'top'    => 'sup',
            	'bottom' => 'sub',
            );

            $prefix_tag = $tags[$prefix_vertical_align];
            $suffix_tag = $tags[$suffix_vertical_align];

	        $classes[] = 'ut-count-prefix-' . $prefix_vertical_align;
	        $classes[] = 'ut-count-suffix-' . $suffix_vertical_align;
	        $classes[] = 'ut-count-icon-position-' . $icon_position;
	        $classes[] = 'ut-count-align-' . $counter_align;

	        if( $glow_effect == 'yes' ) {

		        $count_classes[] = 'ut-glow';

	        }

            /* counter inline styles */
            if ( ! empty( $counter_inline_styles ) ) {
                $counter_inline_styles = 'style="' . esc_attr( implode( ';', $counter_inline_styles ) ) . '"';
            } else {
                $counter_inline_styles = '';
            }
            
            
            /* deprecated - will be removed one day - start block */
            
                $grid = array( 
                    'third'   => 'ut-one-third',
                    'fourth'  => 'ut-one-fourth',
                    'half'    => 'ut-one-half'
                );  
                
                $classes[] = ( $last == 'true' ) ? 'ut-column-last' : '';
                $classes[] = !empty( $grid[$width] ) ? $grid[$width] : 'clearfix';
                $classes[] = $class;                             
                    
            /* deprecated - will be removed one day - end block */
            
            
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
					
                } elseif( $icon_type == 'orionicons' && !empty( $icon_orion ) ) {
					
					$icon = $icon_orion;
					
                } else {
                    
                    if( strpos( $icon, 'fa fa-' ) === false ) {
                        $icon = str_replace('fa-', 'fa fa-', $icon );     
                    }
                    
                    $icon = str_replace('fa fa-', 'fa-4x fa fa-', $icon );                
                                    
                }     
                
            }            
            
            /* inline css */
            $css_style = '';
			
			if( $color && ut_is_gradient( $color ) ) {
                    
				$classes[] = 'ut-element-with-gradient-icon';
				$css_style.= ut_create_gradient_css( $color, '#' . $id . ' .ut-counter-box i', false, 'background' );

			} elseif( $color ) {

				$css_style .= '#' . $id . ' .ut-counter-box i { color: ' . $color . '; }'; 

			}
			
            if( $icon_font_size ) {
                $css_style .= '#' . $id . ' .ut-counter-box i { font-size: ' . $icon_font_size . 'px; }';
				$css_style .= '#' . $id . ' .ut-counter-box svg { width: ' . $icon_font_size . 'px; }';
                $css_style .= '#' . $id . ' .ut-counter-box .ut-custom-icon { width: ' . $icon_font_size . 'px; }';  
            }
            
            if( $icon_spacing ) {
                $css_style .= '#' . $id . ' .ut-counter-box i { margin-bottom: ' . $icon_spacing . 'px; }';
				$css_style .= '#' . $id . ' .ut-counter-box svg { margin-bottom: ' . $icon_spacing . 'px; }';
                $css_style .= '#' . $id . ' .ut-counter-box .ut-custom-icon { margin-bottom: ' . $icon_spacing . 'px; }';  
            }

            if( $icon_spacing_lr &&  $icon_position == 'left' ) {

	            $css_style .= '#' . $id . ' .ut-counter-box i { margin-right: ' . $icon_spacing_lr . 'px; }';
	            $css_style .= '#' . $id . ' .ut-counter-box svg { margin-right: ' . $icon_spacing_lr . 'px; }';
	            $css_style .= '#' . $id . ' .ut-counter-box .ut-custom-icon { margin-right: ' . $icon_spacing_lr . 'px; }';

            }

	        if( $icon_spacing_lr &&  $icon_position == 'right' ) {

		        $css_style .= '#' . $id . ' .ut-counter-box i { margin-left: ' . $icon_spacing_lr . 'px; }';
		        $css_style .= '#' . $id . ' .ut-counter-box svg { margin-left: ' . $icon_spacing_lr . 'px; }';
		        $css_style .= '#' . $id . ' .ut-counter-box .ut-custom-icon { margin-left: ' . $icon_spacing_lr . 'px; }';

	        }

			if( $counter_color && ut_is_gradient( $counter_color ) ) {
                    
				$classes[] = 'ut-element-with-gradient-text';
				$css_style.= ut_create_gradient_css( $counter_color, '#' . $id . ' .ut-count', false, 'background' );

			} elseif( $counter_color ) {

				$css_style .= '#' . $id . ' .ut-count { color: ' . $counter_color . '; fill: ' . $counter_color . '; }';

			}
			
			if( $desccolor && ut_is_gradient( $desccolor ) ) {
                    
				$classes[] = 'ut-element-with-gradient-headline';
				$css_style.= ut_create_gradient_css( $desccolor, '#' . $id . ' h3.ut-counter-details', false, 'background' );

			} elseif( $desccolor ) {

				$css_style .= '#' . $id . ' h3.ut-counter-details { color: ' . $desccolor . '; }'; 

			}
			
            if( $counter_align ) {

            	$css_style .= '#' . $id . ' .ut-counter-box { text-align: ' . $counter_align . '; }';

            	if( $counter_align == 'left' ) {

		            //$css_style .= '#' . $id . ' .ut-count { -webkit-box-pack: start; -ms-flex-pack: start; justify-content: flex-start; }';

	            }

	            if( $counter_align == 'right' ) {

		            //$css_style .= '#' . $id . ' .ut-count { -webkit-box-pack: end; -ms-flex-pack: end; justify-content: flex-end; }';

	            }

            }

            $css_style .= '#' . $id . ' h3.ut-counter-details { margin-top: ' . $caption_margin_top . 'px; }';
                            
            if( $caption_text_transform ) {
                $css_style .= '#' . $id . ' h3.ut-counter-details { text-transform: ' . $caption_text_transform . '; }';     
            }
            
            if( $caption_font_weight ) {
                $css_style .= '#' . $id . ' h3.ut-counter-details { font-weight: ' . $caption_font_weight . '; }';     
            }
            
            if( $caption_letter_spacing ) {
				
				// fallback letter spacing
				if( $caption_letter_spacing >= 1 || $caption_letter_spacing <= -1 ) {
					$caption_letter_spacing = $caption_letter_spacing / 100;	
				}
				
                $css_style .= '#' . $id . ' h3.ut-counter-details { letter-spacing:' . $caption_letter_spacing . 'em; }';
            }
            
            if( $caption_font_size ) {
                
                $caption_font_size = str_replace( 'px', '', $caption_font_size );
                $css_style .= '#' . $id . ' h3.ut-counter-details { font-size: ' . $caption_font_size . 'px; }';
                
            }
            
            if( $background ) {
                $css_style .= '#' . $id . ' .ut-counter-box { background: rgba(' .  ut_hex_to_rgb( $background )  . ',' . $opacity . '); }';     
            }
            
            if( $counter_font_size ) {
                
                $caption_font_size = str_replace( 'px', '', $counter_font_size );
                $css_style .= '#' . $id . ' .ut-count { font-size: ' . $counter_font_size . 'px; }';
                
            }

	        if( $counter_prefix_font_weight ) {

		        $css_style .= '#' . $id . ' .ut-count .ut-count-prefix { font-weight: ' . $counter_prefix_font_weight . '; }';

	        }

	        if( $counter_suffix_font_weight ) {

		        $css_style .= '#' . $id . ' .ut-count .ut-count-suffix { font-weight: ' . $counter_suffix_font_weight . '; }';

	        }

			if( $counter_font_source && $counter_font_source == 'websafe' && $counter_websafe_fonts ) {
				
				$css_style .= '#' . $id . ' .ut-count { font-family: ' . get_websafe_font_css_family( $counter_websafe_fonts ) . '; }';
				
			}

			if( $counter_font_source && $counter_font_source == 'custom' && $counter_custom_fonts ) {

				$font_family = get_term($counter_custom_fonts,'unite_custom_fonts');

				if( isset( $font_family->name ) )
				$css_style .= '#' . $id . ' .ut-count { font-family: "' . $font_family->name . '"; }';

			}

	        if( $glow_color || $glow_shadow_color ) {

	        	if( $type == 'countup' ) {


			        $css_style .= '#' . $id . ' .ut-glow { 
	                    -webkit-text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px ' . $glow_shadow_color . ';
	                       -moz-text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px ' . $glow_shadow_color . ';
	                            text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px ' . $glow_shadow_color . '; 
                    }';

		        } else {

			        $css_style.= '#' . $id . ' .ut-glow { 
	                    -webkit-text-shadow: 0 0 8px ' . $glow_color . ',  2px 2px 4px ' . $glow_shadow_color . ';
	                       -moz-text-shadow: 0 0 8px ' . $glow_color . ',  2px 2px 4px ' . $glow_shadow_color . ';
	                            text-shadow: 0 0 8px ' . $glow_color . ',  2px 2px 4px ' . $glow_shadow_color . '; 
                    }';

		        }

	        }

	        if( $stroke_effect == 'yes' ) {

		        if( $type == 'countup' ) {

			        $css_style.= '#' . $id . ' .ut-count {
	                    -moz-text-stroke-color: ' . $stroke_color .';
	                    -webkit-text-stroke-color: ' . $stroke_color .';
	                    -moz-text-stroke-width: ' . $stroke_width .'px;  
	                    -webkit-text-stroke-width: ' . $stroke_width .'px;	            
	                }';

		        } else {

			        $css_style.= '#' . $id . ' .ut-count {
	                    -moz-text-stroke-color: ' . $stroke_color .';
	                    -webkit-text-stroke-color: ' . $stroke_color .';
	                    -moz-text-stroke-width: ' . $stroke_width .'px;  
	                    -webkit-text-stroke-width: ' . $stroke_width .'px;
	                    paint-order: stroke;
					    stroke: ' . $stroke_color .';
					    stroke-width: ' . $stroke_width .'px;
					    stroke-linecap: butt;
					    stroke-linejoin: miter;	            
	                }';

		        }

	        }

            /* start output */
            $output = '';
            
            /* add css */ 
            if( !empty( $css_style ) ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );
            }
            
            $output .= '<div id="' . $id . '" class="' . implode( ' ', array_unique( $classes ) ) . '">';
            
				$sep_sign = $sep == 'true' && empty( $sep_sign ) ? '.' : $sep_sign;
			
                $output .= '<div data-animateonce="' . $animate_once . '" data-effecttype="counter" class="ut-counter-box ut-counter-box-' . $counter_align. ' ut-counter" data-type="' . esc_attr( $type ) . '" data-decimal-places="' . $this->numberOfDecimals( $to ) . '" data-sep="' . esc_attr( $sep ) . '" data-sep-sign="' . esc_attr( $sep_sign ) . '" data-speed="' . esc_attr( $speed ) . '" data-suffix="' . esc_attr( $suffix ) . '" data-suffix-tag="' . esc_attr( $suffix_tag ) . '" data-prefix="' . esc_attr( $prefix ) . '" data-prefix-tag="' . esc_attr( $prefix_tag ) . '" data-counter="' . esc_attr( $to ) . '">';
                    
                    if( !empty( $icon ) ) {
                        
                        if( $image_icon ) {
                            
                            $output .= '<figure class="ut-custom-icon"><img alt="' . esc_html__( 'Count Up to', 'ut_shortcodes' ) . ' ' . $to . '" src="' . $icon . '"></figure>';
                            
                        } else {
                            
							
							if( $icon_type == 'lineaicons' || $icon_type == 'orionicons' ) {
							
								$svg = new UT_Draw_SVG( uniqid("ut-svg-"), $atts );
								$output .= $svg->draw_svg_icon();

							} else {
								
								$output .= '<i class="' . $icon . '"></i>';
								
							}
                            
                        }
                        
                    }

                    if( $type == 'slot' ) {

                    	// && filter_var( $sep, FILTER_VALIDATE_BOOLEAN )

	                    $sep_sign = empty( $sep_sign ) ? '.' : $sep_sign;

	                    $_to = number_format( $to, 0, '', $sep_sign );
	                    $_from = str_replace( array(1,2,3,4,5,6,7,8,9), 0, $_to );


	                    $output .= '<span id="' . $counter_id . '" data-to="' . $_to . '" class="ut-count ' . implode( ' ', $count_classes ) . '" ' . $counter_inline_styles . '>';

	                        $output .= $this->create_slot_markup( $_to, $filter_id );

	                    $output .= '</span>';

	                    $output .= '<svg class="ut-counter-blur" xmlns="http://www.w3.org/2000/svg" version="1.1" width="0" height="0"><defs><filter id="' . $filter_id . '"><feGaussianBlur in="SourceGraphic" stdDeviation="0,120"/></filter></defs></svg>';


                    } else {

	                    $_to = $to;
	                    $_from = str_replace( array(1,2,3,4,5,6,7,8,9), 0, $to );

	                    $output .= '<span id="' . $counter_id . '" data-to="' . $_to . '" class="ut-count ' . implode( ' ', $count_classes ) . '" ' . $counter_inline_styles . '>' . $_from . '</span>';

                    }

                    if( $content ) {                                            
                        $output .= '<h3 class="ut-counter-details">' . $content . '</h3>';                    
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