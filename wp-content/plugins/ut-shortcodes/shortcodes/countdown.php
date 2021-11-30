<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Countdown' ) ) {
	
    class UT_Countdown {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_countdown';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Countdown Timer Module', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'What are you looking forward to?', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'         => 'fa fa-clock-o ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/countdown.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'datepicker',
                            'heading'           => esc_html__( 'Pick Date and Time', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'When selecting a date please make sure, you select a day or time last, otherwise the date is not updating correctly.' , 'ut_shortcodes' ),
                            'param_name'        => 'date',
                            'group'             => 'General',
                            'admin_label'       => true,
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Countdown counts up or down?', 'unitedthemes' ),
                            'param_name'        => 'type',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'down', 'ut_shortcodes' ) => 'until',
                                esc_html__( 'up', 'ut_shortcodes' ) => 'since'
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Format', 'unitedthemes' ),
                            'param_name'        => 'format',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'Hours-Minutes', 'ut_shortcodes' ) => 'HM',
                                esc_html__( 'Hours-Minutes-Seconds', 'ut_shortcodes' ) => 'HMS',
                                esc_html__( 'Days-Hours-Minutes', 'ut_shortcodes' ) => 'dHM',
                                esc_html__( 'Days-Hours-Minutes-Seconds', 'ut_shortcodes' ) => 'dHMS',
                                esc_html__( 'Weeks-Days-Hours-Minutes', 'ut_shortcodes' ) => 'wdHM',
                                esc_html__( 'Weeks-Days-Hours-Minutes-Seconds', 'ut_shortcodes' ) => 'wdHMS',
                                esc_html__( 'Month-Days-Hous-Minutes', 'ut_shortcodes' ) => 'odHM',
                                esc_html__( 'Month-Weeks-Days-Hous-Minutes', 'ut_shortcodes' ) => 'owdHM',
                                esc_html__( 'Year-Months-Weeks-Days-Hours-Minutes-Seconds', 'ut_shortcodes' ) => 'YOWDHMS',
                                esc_html__( 'Custom Format', 'ut_shortcodes' ) => 'custom',
                            )
                        ),
                        /*array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Compact Mode?', 'unitedthemes' ),
                            'param_name'        => 'compact',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true'
                            )
                        ),*/
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Custom Format', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Use the following characters (in order) to indicate which periods you want to display: "Y" for years, "O" for months, "W" for weeks, "D" for days, "H" for hours, "M" for minutes, "S" for seconds.' , 'ut_shortcodes' ),
                            'param_name'        => 'custom_format',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'format',
                                'value'   => 'custom',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Language', 'unitedthemes' ),
                            'param_name'        => 'lang',
                            'group'             => 'General',
                            'value'             => ut_recognized_coutdown_lang()
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Block Layout', 'unitedthemes' ),
                            'description'       => esc_html__( 'A single block usually contains a number and its period. ' , 'ut_shortcodes' ),
                            'param_name'        => 'layout',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'Display number and its period next to each other', 'ut_shortcodes' ) => 'default',
                                esc_html__( 'Display numbers only', 'ut_shortcodes' ) => 'number',
                                esc_html__( 'Display number and its period below', 'ut_shortcodes' ) => 'stacked',
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Block Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'align',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Separator?', 'unitedthemes' ),
                            'param_name'        => 'separator',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' )  => '',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
                            ),
                            'dependency'        => array(
                                'element' => 'layout',
                                'value'   => array('number','stacked'),
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                        // Counter Font Settings
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Countdown Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'cd_font_color',
                            'group'             => 'Counter Font'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'cd_font_source',
                            'group'             => 'Counter Font',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => '',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google',
                                esc_html__( 'Custom Font', 'ut_shortcodes' ) => 'custom',
                            ),
                        ),
                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'cd_google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Counter Font',
                            'settings'          => array(
                                'fields' => array(
                                    'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                                    'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                                ),
                            ),
                            'dependency'        => array(
                                'element'           => 'cd_font_source',
                                'value'             => 'google',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Websafe Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'cd_websafe_fonts',
                            'group'             => 'Counter Font',
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
                                'element'           => 'cd_font_source',
                                'value'             => 'websafe',
                            ),

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Custom Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'cd_custom_fonts',
                            'group'             => 'Counter Font',
                            'value'             => ut_get_custom_fonts(),
                            'dependency'        => array(
                                'element'           => 'cd_font_source',
                                'value'             => 'custom',
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'cd_font_size',
                            'group'             => 'Counter Font',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                'default'   => '17',
                                'min'       => '0',
                                'max'       => '200',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'cd_line_height',
                            'group'             => 'Counter Font',
                            'edit_field_class'  => 'vc_col-sm-4',
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
                            'param_name'        => 'cd_letter_spacing',
                            'group'             => 'Counter Font',
                            'edit_field_class'  => 'vc_col-sm-4',
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
                            'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Please keep in mind, that the selected font needs to support the font weight.', 'ut_shortcodes' ),
                            'param_name'        => 'cd_font_weight',
                            'group'             => 'Counter Font',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'lighter' , 'ut_shortcodes' )  => 'lighter',
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )   => 'bold',
                                esc_html__( 'bolder' , 'ut_shortcodes' ) => 'bolder',
                                esc_html__( '100' , 'ut_shortcodes' )    => '100',
                                esc_html__( '200' , 'ut_shortcodes' )    => '200',
                                esc_html__( '300' , 'ut_shortcodes' )    => '300',
                                esc_html__( '400' , 'ut_shortcodes' )    => '400',
                                esc_html__( '500' , 'ut_shortcodes' )    => '500',
                                esc_html__( '600' , 'ut_shortcodes' )    => '600',
                                esc_html__( '700' , 'ut_shortcodes' )    => '700',
                                esc_html__( '800' , 'ut_shortcodes' )    => '800',
                                esc_html__( '900' , 'ut_shortcodes' )    => '900',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'cd_text_transform',
                            'group'             => 'Counter Font',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),

                        // Period Font Settings
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'cp_font_color',
                            'group'             => 'Period Font',
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'cp_font_source',
                            'group'             => 'Period Font',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => '',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google',
                                esc_html__( 'Custom Font', 'ut_shortcodes' ) => 'custom',
                            ),
                        ),
                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'cp_google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Period Font',
                            'settings'          => array(
                                'fields' => array(
                                    'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                                    'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                                ),
                            ),
                            'dependency'        => array(
                                'element'           => 'cp_font_source',
                                'value'             => 'google',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Websafe Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'cp_websafe_fonts',
                            'group'             => 'Period Font',
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
                                'element'           => 'cp_font_source',
                                'value'             => 'websafe',
                            ),

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Custom Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'cp_custom_fonts',
                            'group'             => 'Period Font',
                            'value'             => ut_get_custom_fonts(),
                            'dependency'        => array(
                                'element'           => 'cp_font_source',
                                'value'             => 'custom',
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'cp_font_size',
                            'group'             => 'Period Font',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                'default'   => '17',
                                'min'       => '0',
                                'max'       => '200',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'cp_line_height',
                            'group'             => 'Period Font',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                'default'   => '150',
                                'min'       => '80',
                                'max'       => '300',
                                'step'      => '5',
                                'unit'      => '%'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'cp_letter_spacing',
                            'group'             => 'Period Font',
                            'edit_field_class'  => 'vc_col-sm-4',
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
                            'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Please keep in mind, that the selected font needs to support the font weight.', 'ut_shortcodes' ),
                            'param_name'        => 'cp_font_weight',
                            'group'             => 'Period Font',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'lighter' , 'ut_shortcodes' )  => 'lighter',
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )   => 'bold',
                                esc_html__( 'bolder' , 'ut_shortcodes' ) => 'bolder',
                                esc_html__( '100' , 'ut_shortcodes' )    => '100',
                                esc_html__( '200' , 'ut_shortcodes' )    => '200',
                                esc_html__( '300' , 'ut_shortcodes' )    => '300',
                                esc_html__( '400' , 'ut_shortcodes' )    => '400',
                                esc_html__( '500' , 'ut_shortcodes' )    => '500',
                                esc_html__( '600' , 'ut_shortcodes' )    => '600',
                                esc_html__( '700' , 'ut_shortcodes' )    => '700',
                                esc_html__( '800' , 'ut_shortcodes' )    => '800',
                                esc_html__( '900' , 'ut_shortcodes' )    => '900',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'cp_text_transform',
                            'group'             => 'Period Font',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),

                        // Block Design
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Block Border?', 'unitedthemes' ),
                            'param_name'        => 'section_border',
                            'group'             => 'Block Design',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Border Width', 'ut_shortcodes' ),
                            'param_name'        => 'section_border_width',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '50',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'group'             => 'Block Design',
                            'dependency'        => array(
                                'element' => 'section_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Border Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'double requires at least 4px border size.', 'ut_shortcodes' ),
                            'param_name'        => 'section_border_style',
                            'group'             => 'Block Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'section_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'section_border_color',
                            'group'             => 'Block Design',
                            'dependency'        => array(
                                'element' => 'section_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Block Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'section_background_color',
                            'group'             => 'Block Design',
                        ),


                        // Animation
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

new UT_Countdown;

if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_countdown extends WPBakeryShortCode {
        
        private $lang;
        
        function __construct( $settings ) {
            
            parent::__construct( $settings );
            $this->shortcodeScripts();    
            
        }
        
        public function shortcodeScripts() {
            
            $min = NULL;
        
            if( !WP_DEBUG ){
                $min = '.min';
            }
            
            wp_register_script( 
                'ut-jquery-plugin', 
                plugins_url('../js/plugins/jquery-countdown/jquery.plugin' . $min . '.js', __FILE__), 
                array('jquery'), 
                '2.1.0', 
                true
            ); 
            
            wp_register_script( 
                'ut-jquery-countdown', 
                plugins_url('../js/plugins/jquery-countdown/jquery.countdown' . $min . '.js', __FILE__), 
                array('jquery','ut-jquery-plugin'), 
                '2.1.0', 
                true
            );
            
            
            foreach( ut_recognized_coutdown_lang() as $language_code ) {
            
                // no language file
                if( $language_code == 'en' ) {
                    continue;
                }
                
                wp_register_script( 
                    'ut-jquery-lang-' . $language_code, 
                    plugins_url('../js/plugins/jquery-countdown/jquery.countdown-' . $language_code . '.js', __FILE__), 
                    array(), 
                    '1.0', 
                    true
                ); 
            
            }
            
            wp_register_script( 
                'ut-countdown', 
                plugins_url('../js/ut.countdown' . $min . '.js', __FILE__), 
                array(), 
                '1.0', 
                true
            );            
            
        }
        
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
        public function getStyles( $cd_google_fonts_data, $cp_google_fonts_data, $atts ) {
            
            $cd_styles = array();
            $cd_font_source = empty( $atts['cd_font_source'] ) ? '' : $atts['cd_font_source'];
                        
            if ( 'google' === $cd_font_source && ! empty( $cd_google_fonts_data ) && isset( $cd_google_fonts_data['values'], $cd_google_fonts_data['values']['font_family'], $cd_google_fonts_data['values']['font_style'] ) ) {
                $google_fonts_family = explode( ':', $cd_google_fonts_data['values']['font_family'] );
                $cd_styles[] = 'font-family:' . $google_fonts_family[0];
                $google_fonts_styles = explode( ':', $cd_google_fonts_data['values']['font_style'] );
                $cd_styles[] = 'font-weight:' . $google_fonts_styles[1];
                $cd_styles[] = 'font-style:' . $google_fonts_styles[2];
            }
            
            $cp_styles = array();
            $cp_font_source = empty( $atts['cp_font_source'] ) ? '' : $atts['cp_font_source'];
                        
            if ( 'google' === $cp_font_source && ! empty( $cp_google_fonts_data ) && isset( $cp_google_fonts_data['values'], $cp_google_fonts_data['values']['font_family'], $cp_google_fonts_data['values']['font_style'] ) ) {
                $google_fonts_family = explode( ':', $cp_google_fonts_data['values']['font_family'] );
                $cp_styles[] = 'font-family:' . $google_fonts_family[0];
                $google_fonts_styles = explode( ':', $cp_google_fonts_data['values']['font_style'] );
                $cp_styles[] = 'font-weight:' . $google_fonts_styles[1];
                $cp_styles[] = 'font-style:' . $google_fonts_styles[2];
            }
            
            return array(
                'cd_inline_styles' => $cd_styles,
                'cp_inline_styles' => $cp_styles,
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
                     
            $cd_google_fonts_field  = $this->getParamData( 'cd_google_fonts' );
    
            $cd_google_fonts_field_settings = isset( $cd_google_fonts_field['settings'], $cd_google_fonts_field['settings']['fields'] ) ? $cd_google_fonts_field['settings']['fields'] : array();
            $cd_google_fonts_data = strlen( $cd_google_fonts ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $cd_google_fonts_field_settings, $cd_google_fonts ) : '';
            
            
            $cp_google_fonts_field  = $this->getParamData( 'cp_google_fonts' );
    
            $cp_google_fonts_field_settings = isset( $cp_google_fonts_field['settings'], $cp_google_fonts_field['settings']['fields'] ) ? $cp_google_fonts_field['settings']['fields'] : array();
            $cp_google_fonts_data = strlen( $cp_google_fonts ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $cp_google_fonts_field_settings, $cp_google_fonts ) : '';
            
            
            return array(
                'cd_google_fonts'      => $cd_google_fonts,
                'cd_google_fonts_data' => $cd_google_fonts_data,
                'cp_google_fonts'      => $cp_google_fonts,
                'cp_google_fonts_data' => $cp_google_fonts_data,
            );
            
        }
        
        protected function content( $atts, $content = null ) {
            
            extract( shortcode_atts( array (
                'type'              => 'until',
                'date'              => '',
                'format'            => 'HM',
                'separator'         => '',
                'compact'           => 'false',
                'custom_format'     => '',
                'layout'            => 'default',
                'align'             => 'left',
                'lang'              => 'en',
                'size'              => '',
                'width'             => '',
                
                // Counter Font Settings
                'cd_font_source'    => '',
                'cd_websafe_fonts'  => '',
				'cd_custom_fonts'   => '',
                'cd_google_fonts'   => '',
                'cd_font_size'      => '',
                'cd_font_weight'    => '',
                'cd_font_color'     => '',
                'cd_letter_spacing' => '',
                'cd_text_transform' => '',
                'cd_line_height'    => '100',
                
                // Period Font Settings
                'cp_font_source'    => '',
                'cp_websafe_fonts'  => '',
				'cp_custom_fonts'   => '',
                'cp_google_fonts'   => '',
                'cp_font_size'      => '',
                'cp_font_weight'    => '',
                'cp_font_color'     => '',
                'cp_letter_spacing' => '',
                'cp_text_transform' => '',
                'cp_line_height'    => '',                
                
                 // Block Design
                'section_border'            => '',
                'section_border_width'      => '',
                'section_border_style'      => 'solid',
                'section_border_color'      => '',
                'section_background_color'  => '',
                
                // Animation
                'effect'            => '',     
                'animate_once'      => 'yes',
                'animate_mobile'    => false,
                'animate_tablet'    => false,
                'delay'             => 'false',
                'delay_timer'       => '100',
                'animation_duration'=> '',
                
                'css'               => '',
                'class'             => ''
                
            ), $atts ) ); 
            
            if( empty( $date ) ) {
                return esc_html__( 'Please select a date!', 'ut_shortcodes' );
            }
                        
            // enqueue scripts            
            $this->lang = $lang; 
            $this->enqueueScripts(); 
                        
            // optional classes
            $classes    = array('ut-countdown-module');
            $classes[]  = $class;
            $classes[]  = 'ut-countdown-module-' . $align;
            
            if( $layout == 'default' ) {
                $classes[] = 'ut-countdown-module-inline';
            }
            
            if( $layout == 'stacked' || $layout == 'number' ) {
                $classes[] = 'ut-countdown-module-compact';
            }
            
            if( $separator == 'on' && ( $layout == 'stacked' || $layout == 'number' ) ) {
                $classes[]  = 'ut-countdown-module-with-separator';
            }
            
            // animation effect 
            $attributes = array();            
            
            if( !empty( $effect ) && $effect != 'none' ) {
                
                $attributes['data-effect']      = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );
                $attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;
                
                if( !empty( $animation_duration ) ) {
                    $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );    
                }                
                
                $classes[]  = 'ut-animate-element';
                $classes[]  = 'animated';
                
                if( !$animate_tablet ) {
                    $classes[]  = 'ut-no-animation-tablet';
                }
                
                if( !$animate_mobile ) {
                    $classes[]  = 'ut-no-animation-mobile';
                }
                
                if( $animate_once == 'infinite' ) {
                    $classes[]  = 'infinite';
                }
                                
            }
            
            // attributes string
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );
                        
            // need variables
            $output              = NULL;
            $css_style           = NULL;
            $cd_font_settings    = array();
            $cp_font_settings    = array();
            $cd_section_settings = array();
            
            
            // Google Fonts
            if( ( $cd_font_source && $cd_font_source == 'google' ) || ( $cp_font_source && $cp_font_source == 'google' ) ) {
                
                // google font settings
                extract( $this->getAttributes( $atts ) );
                extract( $this->getStyles( $cd_google_fonts_data, $cp_google_fonts_data, $atts ) );
                
                // subsets
                $settings = get_option( 'wpb_js_google_fonts_subsets' );
                if ( is_array( $settings ) && ! empty( $settings ) ) {
                    $subsets = '&subset=' . implode( ',', $settings );
                } else {
                    $subsets = '';
                }
                
                // counter font
                if ( $cd_font_source && isset( $cd_google_fonts_data['values']['font_family'] ) ) {
                    wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $cd_google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $cd_google_fonts_data['values']['font_family'] . $subsets. '&display=swap' );
                }
                
                // period  font
                if ( $cp_font_source && isset( $cp_google_fonts_data['values']['font_family'] ) ) {
                    wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $cp_google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $cp_google_fonts_data['values']['font_family'] . $subsets. '&display=swap' );
                }
            
            }
            
            $id = uniqid('bklyn_cd_');
            
            if( $size && $size == 'custom' ) {
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-section { width:' . $width . 'px; }';                                
            }
            
            $css_style .= '#' . esc_attr( $id ) . ' .countdown-section { display:inline-block; }';
            
            if( $align ) {
                $css_style .= '#' . esc_attr( $id ) . ' { text-align: ' . $align . ' }';
            }
            
            // layouts
            if( $layout == 'default' ) {
                
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-amount { display:inline-block; }';
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-period { display:inline-block; }';
                
            }
            
            if( $layout == 'number' ) {
                
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-amount { display:inline-block; }';
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-period { display:none; }';
                
            }
            
            if( $layout == 'stacked' ) {
                
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-amount { display:block; }';
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-period { display:block; }';
                
            }
            
            // countdown font styles 
            if ( !empty( $cd_inline_styles ) ) {
                $cd_font_settings = array_merge($cd_font_settings, $cd_inline_styles);
            } 
            
			if( $cd_font_source && $cd_font_source == 'websafe' && $cd_websafe_fonts ) {
				$cd_font_settings[] = 'font-family:' . get_websafe_font_css_family( $cd_websafe_fonts ) . '';
			}
			
			if( $cd_font_source && $cd_font_source == 'custom' && $cd_custom_fonts ) {
				
				$font_family = get_term($cd_custom_fonts,'unite_custom_fonts');
				
				if( isset( $font_family->name ) )
				$cd_font_settings[] = 'font-family:"' . $font_family->name . '"';
				
			}
			
			
            if ( !empty( $cd_font_size ) ) {
                $cd_font_settings[] = 'font-size:' . $cd_font_size . 'px';
            }
            
            if ( !empty( $cd_line_height ) ) {
                
                if( $cd_line_height < 80 ) {
                    $cd_line_height = 100;
                }
                
                $cd_font_settings[] = 'line-height:' . $cd_line_height . '%';
                
            }
            
            if ( !empty( $cd_font_weight ) ) {
                $cd_font_settings[] = 'font-weight:' . $cd_font_weight;
            }
            
            if ( !empty( $cd_letter_spacing ) ) {
                
				// fallback letter spacing
				if( $cd_letter_spacing >= 1 || $cd_letter_spacing <= -1 ) {
					$cd_letter_spacing = $cd_letter_spacing / 100;	
				}
				
				$cd_font_settings[] = 'letter-spacing:' . $cd_letter_spacing . 'em';
				
            }
            
            if ( !empty( $cd_text_transform ) ) {
                $cd_font_settings[] = 'text-transform:' . $cd_text_transform;
            }
            
			if( !empty( $cd_font_color ) && ut_is_gradient( $cd_font_color ) ) {
				
				$classes[] = 'ut-countdown-module-with-gradient-amount';
				
				$css_style .= ut_create_gradient_css( $cd_font_color, '#' . $id . ' .countdown-amount', false, 'background' );
				$css_style .= ut_create_gradient_css( $cd_font_color, '#' . $id . '.ut-countdown-module-compact.ut-countdown-module-with-separator .countdown-section::after', false, 'background' );
				
			} elseif ( !empty( $cd_font_color ) ) {
                
				$cd_font_settings[] = 'color:' . $cd_font_color . '';
				
            }
            
                // add to css
                if( !empty( $cd_font_settings ) ) {
                    
                    $css_style .= '#' . esc_attr( $id ) . ' .countdown-amount {' . esc_attr( implode( ';', $cd_font_settings ) ) . '}';
                    $css_style .= '#' . esc_attr( $id ) . '.ut-countdown-module-compact.ut-countdown-module-with-separator .countdown-section::after {' . esc_attr( implode( ';', $cd_font_settings ) ) . '}';
                    
                }            
            
            // countdown period font styles
            if ( !empty( $cp_inline_styles ) ) {
                $cp_font_settings = array_merge($cp_font_settings, $cp_inline_styles);
            }
            
			if( $cp_font_source && $cp_font_source == 'websafe' && $cp_websafe_fonts ) {
				$cp_font_settings[] = 'font-family:' . get_websafe_font_css_family( $cp_websafe_fonts ) . '';
			}
			
			if( $cp_font_source && $cp_font_source == 'custom' && $cp_custom_fonts ) {
				
				$font_family = get_term($cp_custom_fonts,'unite_custom_fonts');
				$cp_font_settings[] = 'font-family:"' . $font_family . '"';
				
			}
			
            if ( !empty( $cp_font_size ) ) {
                $cp_font_settings[] = 'font-size:' . $cp_font_size . 'px';
            }
            
            if ( !empty( $cp_line_height ) ) {
                
                if( $cp_line_height < 80 ) {
                    $cp_line_height = 150;
                }
                
                $cp_font_settings[] = 'line-height:' . $cp_line_height . '%';
                
            }
            
            if ( !empty( $cp_font_weight ) ) {
                $cp_font_settings[] = 'font-weight:' . $cp_font_weight;
            }
            
            if ( !empty( $cp_letter_spacing ) ) {
				
				// fallback letter spacing
				if( $cp_letter_spacing >= 1 || $cp_letter_spacing <= -1 ) {
					$cp_letter_spacing = $cp_letter_spacing / 100;	
				}
				
                $cp_font_settings[] = 'letter-spacing:' . $cp_letter_spacing . 'em';
            }
            
            if ( !empty( $cp_text_transform ) ) {
                $cp_font_settings[] = 'text-transform:' . $cp_text_transform;
            }
            
			
			if( !empty( $cp_font_color ) && ut_is_gradient( $cp_font_color ) ) {
				
				$classes[] = 'ut-countdown-module-with-gradient-period';
				$css_style .= ut_create_gradient_css( $cp_font_color, '#' . $id . ' .countdown-period', false, 'background' );
				
			} elseif ( !empty( $cp_font_color ) ) {
                
				$cp_font_settings[] = 'color:' . $cp_font_color . '';
				
            }
			
                // add to css
                if( !empty( $cp_font_settings ) ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .countdown-period {' . esc_attr( implode( ';', $cp_font_settings ) ) . '}';
                }
            
            // Block Settings
            if( $section_border ) {
                
                if( $section_border_width ) {
                    $cd_section_settings[] = 'border-width:' . $section_border_width . 'px';
                }
                
                if( $section_border_style ) {
                    $cd_section_settings[] = 'border-style:' . $section_border_style;
                }
                
                if( $section_border_color ) {
                    $cd_section_settings[] = 'border-color:' . $section_border_color;
                }
                
            }
            
            if( $section_border || !empty( $section_background_color ) ) {
                
                $css_style .= '#' . esc_attr( $id ) . ' .countdown-section { background: ' . $section_background_color . '}';
                $classes[]  = 'ut-countdown-module-with-border';
                
            }
                
                // add to css
                if( !empty( $cd_section_settings ) ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .countdown-section {' . esc_attr( implode( ';', $cd_section_settings ) ) . '}';
                }
                        
            
            if( $css_style ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );
            }
            
            if( $format == 'custom' && !empty( $custom_format ) ) {
                $format = $custom_format;
            }
            
            // start shortcode
            $output .= '<div id="' . esc_attr( $id ) . '" ' . $attributes . ' class="' . implode(" ", $classes ) . '">';
            
                $output .= '<div class="ut-countdown" data-compact="' . esc_attr( $compact ) . '" data-separator="' . esc_attr( $separator )  . '" data-type="' . esc_attr( $type ) . '" data-lang="' . esc_attr( $this->lang ) . '" data-format="' . esc_attr( $format ) . '" data-date="' . esc_attr( $date ) . '"></div>';
            
            $output .= '</div>';
            
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }            
            
            return $output;
        
        }
        
        public function enqueueScripts() {
            
            wp_enqueue_script('ut-jquery-countdown');
            wp_enqueue_script('ut-jquery-lang-' . $this->lang);
            wp_enqueue_script('ut-countdown');
            
        }
            
    }

}