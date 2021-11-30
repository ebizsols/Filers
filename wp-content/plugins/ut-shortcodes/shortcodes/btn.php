<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_BTN' ) ) {
    
    class UT_BTN {
        
        private $shortcode;

        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_btn';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );

            if( function_exists('vc_add_params') ) {
                vc_add_params( $this->shortcode, _vc_add_animation_settings() );
            }
            
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Button', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Buttons for every purpose. Fully packed with unique effects and smooth transitions. .', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    //'icon'            => 'fa fa-external-link ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/button.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        /* General */
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Button Text', 'ut_shortcodes' ),
                            'param_name'        => 'button_text',
                            'admin_label'       => true,
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Icon?', 'unitedthemes' ),
                            'param_name'        => 'button_add_icon',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),

                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Icon library', 'ut_shortcodes' ),
                            'description'   => esc_html__( 'Select icon library.', 'ut_shortcodes' ),
                            'param_name'    => 'button_icon_type',
                            'group'         => 'General',
                            'value'         => array(
                                esc_html__( 'Brooklyn Icons', 'ut_shortcodes' ) => 'bklynicons',
                                esc_html__( 'Font Awesome', 'ut_shortcodes' ) => 'fontawesome',
                            ),
                            'dependency'        => array(
                                'element' => 'button_add_icon',
                                'value'   => 'yes',
                            ),
                        ),

                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'button_icon',
                            'group'             => 'General',
                            'dependency' => array(
                                'element'   => 'button_icon_type',
                                'value'     => 'fontawesome',
                            ),
                        ),

                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'button_icon_bklyn',
                            'group'             => 'General',
                            'settings' => array(
                                'emptyIcon'     => true,
                                'type'          => 'bklynicons',
                            ),
                            'dependency' => array(
                                'element'   => 'button_icon_type',
                                'value'     => 'bklynicons',
                            ),

                        ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Font Awesome Vertical Alignment Correction (top)', 'ut_shortcodes' ),
		                    'description'		=> esc_html__( 'Font Awesome are not always vertically aligned correctly. In case there is a misalignment, use this option to correct it.', 'unitedthemes' ),
		                    'param_name'        => 'fontawesome_v_correction_t',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'General',
		                    'value'             => array(
			                    'default' => '0',
			                    'min'     => '0',
			                    'max'     => '10',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
		                    'dependency' => array(
			                    'element'   => 'button_icon_type',
			                    'value'     => 'fontawesome',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Font Awesome Vertical Alignment Correction (bottom)', 'ut_shortcodes' ),
		                    'description'		=> esc_html__( 'Font Awesome are not always vertically aligned correctly. In case there is a misalignment, use this option to correct it.', 'unitedthemes' ),
		                    'param_name'        => 'fontawesome_v_correction_b',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'General',
		                    'value'             => array(
			                    'default' => '0',
			                    'min'     => '0',
			                    'max'     => '10',
			                    'step'    => '1',
			                    'unit'    => 'px'
		                    ),
		                    'dependency' => array(
			                    'element'   => 'button_icon_type',
			                    'value'     => 'fontawesome',
		                    ),
	                    ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Icon Size', 'ut_shortcodes' ),
                            'param_name'        => 'button_icon_size',
                            'group'             => 'General',
                            'value'             => array(
                                'default'   => '14',
                                'min'   	=> '10',
                                'max'   	=> '30',
                                'step'  	=> '1',
                                'unit'  	=> 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'button_add_icon',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Icon Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'button_icon_align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                            'dependency'        => array(
                                'element' => 'button_add_icon',
                                'value'   => 'yes',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Lightbox?', 'unitedthemes' ),
                            'param_name'        => 'button_add_lightbox',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'What do you like to show inside your lightbox?', 'ut_shortcodes' ),
                            'param_name'        => 'button_lightbox_content',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Image', 'ut_shortcodes' ) => 'image',
                                esc_html__( 'Youtube or Vimeo', 'ut_shortcodes' ) => 'video',
                                esc_html__( 'Iframe', 'ut_shortcodes' ) => 'iframe',
                            ),
                            'dependency'        => array(
                                'element' => 'button_add_lightbox',
                                'value'   => 'yes',
                            ),
                        ),

                        array(
                            'type'              => 'attach_image',
                            'heading'           => esc_html__( 'Image for Lightbox', 'ut_shortcodes' ),
                            'param_name'        => 'button_lightbox_image',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'button_lightbox_content',
                                'value'   => 'image',
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Video for Lightbox', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Needs to be Vimeo or Youtube! If you are using vimeo please use the following link markup: https://vimeo.com/XXXXXXX' , 'ut_shortcodes' ),
                            'param_name'        => 'button_lightbox_video',
                            'group'             => 'General',
                            'admin_label'       => true,
                            'dependency'        => array(
                                'element' => 'button_lightbox_content',
                                'value'   => 'video',
                            ),
                        ),

                        array(
                            'type'              => 'iframe',
                            'heading'           => esc_html__( 'Iframe SRC', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Only insert the iframe src not the entire iframe code! Make sure, that your iframe source allow embedding, otherwise the lightbox stays empty.' , 'ut_shortcodes' ),
                            'param_name'        => 'button_lightbox_iframe',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'button_lightbox_content',
                                'value'   => 'iframe',
                            ),
                        ),

                        array(
                            'type'              => 'vc_link',
                            'heading'           => esc_html__( 'Button Link', 'ut_shortcodes' ),
                            'param_name'        => 'button_link',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'button_add_lightbox',
                                'value'   => 'no',
                            ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                        /* Button Colors */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Button Effect', 'unitedthemes' ),
                            'param_name'        => 'button_effect',
                            'group'             => 'Button Colors',
                            'value'             => array(
                                esc_html__( 'No Effect', 'ut_shortcodes' ) => 'none',
                                esc_html__( 'Aylen', 'ut_shortcodes' ) => 'aylen',
                                esc_html__( 'Winona', 'ut_shortcodes' ) => 'winona'
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_text_color',
                            'group'             => 'Button Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_background',
                            'group'             => 'Button Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_text_color_hover',
                            'group'             => 'Button Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Background Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_background_hover',
                            'group'             => 'Button Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Second Background Hover Color for Button Effect', 'ut_shortcodes' ),
                            'param_name'        => 'button_background_hover_2',
                            'group'             => 'Button Colors',
                            'dependency'        => array(
                                'element' => 'button_effect',
                                'value'   => array('aylen'),
                            ),
                        ),

                        /* Button Effects */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Button Particle Effect?', 'unitedthemes' ),
                            'description'       => esc_html__( 'Note, activating this effect will create extra spacing. You can adjust it down below.', 'ut_shortcodes' ),
                            'param_name'        => 'button_particle',
                            'group'             => 'Button Effects',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'yes'
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Select Button Particle Effect', 'unitedthemes' ),
                            'param_name'        => 'button_particle_effect',
                            'group'             => 'Button Effects',
                            'value'             => ut_get_button_particle_effects(),
                            'dependency'        => array(
                                'element' => 'button_particle',
                                'value'   => array('yes'),
                            ),
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Particle Effect Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_particle_effect_color',
                            'group'             => 'Button Effects',
                            'dependency'        => array(
                                'element' => 'button_particle',
                                'value'   => array('yes'),
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Button Particle Effect Direction', 'unitedthemes' ),
                            'param_name'        => 'button_particle_effect_direction',
                            'group'             => 'Button Effects',
                            'value'             => array(
                                esc_html__( 'left', 'ut_shortcodes' )   => 'left',
                                esc_html__( 'top', 'ut_shortcodes' )    => 'top',
                                esc_html__( 'right', 'ut_shortcodes' )  => 'right',
                                esc_html__( 'bottom', 'ut_shortcodes' ) => 'bottom'
                            ),
                            'dependency'        => array(
                                'element' => 'button_particle',
                                'value'   => array('yes'),
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Let Button reappear?', 'unitedthemes' ),
                            'param_name'        => 'button_particle_reappear',
                            'group'             => 'Button Effects',
                            'value'             => array(
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes, please!',
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no, thanks!'
                            ),
                            'dependency'        => array(
                                'element' => 'button_particle',
                                'value'   => array('yes'),
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Particle Button Spacing Top', 'ut_shortcodes' ),
                            'param_name'        => 'button_particle_spacing_top',
                            'group'             => 'Button Effects',
                            'dependency'        => array(
                                'element' => 'button_particle',
                                'value'   => array('yes'),
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Particle Button Spacing Bottom', 'ut_shortcodes' ),
                            'param_name'        => 'button_particle_spacing_bottom',
                            'group'             => 'Button Effects',
                            'dependency'        => array(
                                'element' => 'button_particle',
                                'value'   => array('yes'),
                            ),
                        ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Add Glitch Distortion?', 'ut_shortcodes' ),
		                    'param_name'        => 'glitch_distortion_effect',
		                    'group'             => 'Button Effects',
		                    'value'             => array(
			                    esc_html__( 'no, thanks!', 'ut_shortcodes' )                => 'off',
			                    esc_html__( 'yes, apply on appear!' , 'ut_shortcodes' )     => 'on_appear',
			                    esc_html__( 'yes, apply on hover!' , 'ut_shortcodes' )      => 'on_hover',
			                    esc_html__( 'yes, apply permanently!' , 'ut_shortcodes' )   => 'permanent',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Glitch Distortion Style', 'ut_shortcodes' ),
		                    'param_name'        => 'glitch_distortion_effect_style',
		                    'group'             => 'Button Effects',
		                    'value'             => array(
			                    esc_html__( 'Style 1', 'ut_shortcodes' )                => 'style-1',
			                    esc_html__( 'Style 2', 'ut_shortcodes' )                => 'style-2',
			                    esc_html__( 'Style 3', 'ut_shortcodes' )                => 'style-3',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'glitch_distortion_effect',
			                    'value'   => array('on_appear','permanent'),
		                    )
	                    ),


                        /* Button Design */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Button Size', 'ut_shortcodes' ),
                            'param_name'        => 'button_size',
                            'group'             => 'Button Design',
                            'value'             => array(
                                esc_html__( 'Choose Button Size', 'ut_shortcodes' ) => '',
                                esc_html__( 'mini'   , 'ut_shortcodes' ) => 'bklyn-btn-mini',
                                esc_html__( 'small'  , 'ut_shortcodes' ) => 'bklyn-btn-small',
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'bklyn-btn-normal',
                                esc_html__( 'large'  , 'ut_shortcodes' ) => 'bklyn-btn-large',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Button Alignment', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'If this button belongs to a button group, the alignment is controlled by the button group setting.', 'ut_shortcodes' ),
                            'param_name'        => 'button_align',
                            'group'             => 'Button Design',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                esc_html__( 'center', 'ut_shortcodes' ) => 'bklyn-btn-center',
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'bklyn-btn-left',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'bklyn-btn-right',
                            ),
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Alignment Tablet', 'ut_shortcodes' ),
		                    'param_name'        => 'button_align_tablet',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'group'             => 'Button Design',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger'  , 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Alignment Mobile', 'ut_shortcodes' ),
		                    'param_name'        => 'button_align_mobile',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'group'             => 'Button Design',
		                    'value'             => array(
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
			                    esc_html__( 'inherit from larger'  , 'ut_shortcodes' ) => 'inherit',
		                    ),
	                    ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Justify Button?', 'unitedthemes' ),
                            'param_name'        => 'button_fluid',
                            'group'             => 'Button Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Button Shadow?', 'unitedthemes' ),
                            'param_name'        => 'button_hover_shadow',
                            'group'             => 'Button Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes, display shadow on mouse over!', 'ut_shortcodes' ) => 'yes',
                                esc_html__( 'yes, display shadow by default and on mouse over!', 'ut_shortcodes' ) => 'yes_default'
                            )
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Border Radius', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Border Radius is not supported if you are using gradient border colors.', 'unitedthemes' ),
                            'param_name'        => 'button_border_radius',
                            'group'             => 'Button Design',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '50',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Button Border?', 'unitedthemes' ),
                            'param_name'        => 'button_custom_border',
                            'group'             => 'Button Design',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_border_color',
                            'group'             => 'Button Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'button_custom_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Border Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_border_color_hover',
                            'group'             => 'Button Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'button_custom_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Border Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Border Style is not supported if you are using gradient border colors.', 'unitedthemes' ),
                            'param_name'        => 'button_border_style',
                            'group'             => 'Button Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'button_custom_border',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Border Width', 'ut_shortcodes' ),
                            'param_name'        => 'button_border_width',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'min'   => '0',
                                'max'   => '50',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'group'             => 'Button Design',
                            'dependency'        => array(
                                'element' => 'button_custom_border',
                                'value'   => 'yes',
                            ),
                        ),

                        /*
                        array(
                            'type'              => 'checkbox',
                            'heading'           => esc_html__( 'Use custom font for this button?', 'unitedthemes' ),
                            'param_name'        => 'button_custom_font',
                            'group'             => 'Button Fonts',
                        ),
                        array(
                            'type'              => 'google_fonts',
                            'heading'           => esc_html__( 'Google Font', 'ut_shortcodes' ),
                            'param_name'        => 'button_font',
                            'group'             => 'Button Fonts',
                            'dependency'    => array(
                                'element' => 'button_custom_font',
                                'value'   => 'true',
                            ),
                        ),
                        */

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Font Family', 'ut_shortcodes' ),
                            'param_name'        => 'font_family',
                            'group'             => 'Button Font',
                            'value'             => array(

                                esc_html__( 'Sans Serif (default)' , 'ut_shortcodes' ) => '',
                                esc_html__( 'Body Font (inherit)' , 'ut_shortcodes' ) => 'inherit',

                            ),
                        ),

                        /* Font Settings */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'font_weight',
                            'group'             => 'Button Font',
                            /*'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' )             => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )               => 'bold'
                            ),*/
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
                            'heading'           => esc_html__( 'Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'text_transform',
                            'group'             => 'Button Font',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'none' , 'ut_shortcodes' ) => 'none',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'letter_spacing',
                            'group'             => 'Button Font',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),
                        ),

                        /* CSS Editor */
                        array(
                            'type'              => 'ut_css_editor',
                            'heading'           => esc_html__( 'Button Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'spacing',
                            'group'             => 'Button Spacing',
                        )

                    )

                )

            ); /* end mapping */
                

        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                
                'button_text'           => '',
                'button_link'           => '',
                'button_align'          => 'bklyn-btn-center',
                'button_align_tablet'   => 'inherit',
                'button_align_mobile'   => 'center',
                'button_size'           => 'bklyn-btn-normal',
                'button_fluid'          => '',
                'button_hover_shadow'   => 'no',
                
				/* particle */
				'button_particle'					=> 'no',
				'button_particle_effect'			=> 'send',
				'button_particle_effect_color'     	=> '',
				'button_particle_effect_direction'	=> 'left',
				'button_particle_reappear'			=> 'yes',
				'button_particle_spacing_top'       => '',
                'button_particle_spacing_bottom'    => '',

                /* glitch effect */
	            'glitch_distortion_effect'          => '',
                'glitch_distortion_effect_style'    => 'style-1',

                /* icons */
                'button_add_icon'           => '',
                'button_icon_type'          => 'bklynicons',
                'button_icon'               => '',
                'button_icon_bklyn'         => '',
				'button_icon_size'			=> '',
                'button_icon_align'         => 'left',
                'fontawesome_v_correction_t' => '',
                'fontawesome_v_correction_b' => '',

                /* lightbox */
                'button_add_lightbox'       => '',                
                'button_lightbox_content'   => 'image',
                'button_lightbox_video'     => '',
                'button_lightbox_image'     => '',
                'button_lightbox_iframe'    => '',                
                
                /* color settings */
                'button_effect'             => 'none',
                'button_text_color'         => '',
                'button_text_color_hover'   => '',
                'button_background'         => '',
                'button_background_hover'   => '',
                'button_background_hover_2' => '',
                
                /* border settings */
                'button_custom_border'      => '',
                'button_border_color'       => '',
                'button_border_color_hover' => '',
                'button_border_style'       => '',
                'button_border_width'       => '',
                'button_border_radius'      => '',
                
                /* font */
                'font_family'               => '',
                'font_size'               	=> '',
				'font_weight'               => '',
                'letter_spacing'            => '',
                'text_transform'            => '',
                
                /* css */
                'spacing'                   => '',
                
                /* animation */
                'effect'                    => '',     
                'animate_once'              => 'yes',
                'animate_mobile'            => false,
                'animate_tablet'            => false,
                'delay'                     => 'no',
                'delay_timer'               => '100',
                'animation_duration'        => '',                
                
                /* class */
                'class'                     => '',
                
                /* simple  atts */
                'button_plain_link'        => '',
                'button_plain_target'      => '',
                
            ), $atts ) ); 
            
            global $ut_btn_group_count, $ut_btn_group_total_count;
            
            $classes    = array();
            $classes[]  = $class;
            
            if( $ut_btn_group_count && $ut_btn_group_count == 1 ) {
                $classes[] = 'bklyn-btn-first';                
            }
            
            if( $ut_btn_group_count && $ut_btn_group_count == $ut_btn_group_total_count ) {
                $classes[] = 'bklyn-btn-last';                
            }
            
            if( $ut_btn_group_count ) {
                $ut_btn_group_count++;
            }
            
            // button animation    
            $attributes     = array();
			$p_attributes   = array(); 
            $button_classes = array();
			$p_classes 		= array();
            
            // button effect
            if( $button_effect != 'none' ) {
                
                $button_classes[] = 'bklyn-btn-with-effect';
                $button_classes[] = 'bklyn-btn-effect-' . $button_effect;    
                
            }
            
			// button particle effect
			if( $button_particle == 'yes' ) {

				// needed body class
				$button_classes[] = 'ut-btn-disintegrate';
				
				if( $button_particle_reappear && $button_particle_reappear == 'yes' )
				$button_classes[] = 'ut-btn-integrate';				
				
				// particle effect
				$attributes['data-particle-effect'] = $button_particle_effect;

				// particle effect direction
				$attributes['data-particle-direction'] = $button_particle_effect_direction;

				// particle effect color
				$button_particle_effect_color = !empty( $button_particle_effect_color ) ? $button_particle_effect_color : '';
				
				if( empty( $button_particle_effect_color ) ) 
				$button_particle_effect_color = !empty( $button_background ) ? $button_background : get_option('ut_accentcolor' , '#F1C40F');
				
				$attributes['data-particle-color'] = $button_particle_effect_color;
				
			}
			
			
            // button link
            if( function_exists('vc_build_link') && $button_link ) {
                
                $button_link = vc_build_link( $button_link );
                
                /* assign link */
                $link = !empty( $button_link['url'] ) ? $button_link['url'] : '#';
                
            } else {
                
                $link = !empty( $button_link ) ? $button_link : '#';
            
            }
            
            $data_iframe = '';
            
            if( $button_add_lightbox == 'yes' ) {
                
                if( $button_lightbox_content == 'image' ) {
                      
                    $link = wp_get_attachment_url( $button_lightbox_image );
                    
                }
                
                if( $button_lightbox_content == 'video' ) {
                    
                    $link = esc_url( $button_lightbox_video );
                        
                }
                
                if( $button_lightbox_content == 'iframe' ) {
                    
                    $link = esc_url( $button_lightbox_iframe );        
                    $data_iframe = 'data-iframe="true"';
                    
                }                                
                
            }
            
            $target = !empty( $button_link['target'] ) ? $button_link['target'] : '_self';
            $title  = !empty( $button_link['title'] )  ? $button_link['title'] : '';
            $rel    = !empty( $button_link['rel'] )    ? 'rel="' . esc_attr( trim( $button_link['rel'] ) ) . '"' : '';
            
            
            // temporary conditional for menu buttons
            if( !empty( $button_plain_link ) ) {
                $link = $button_plain_link;
            }
            
            if( !empty( $button_plain_target ) ) {
                $target = $button_plain_target;
            }
            
            if( $class != 'bklyn-btn-menu' && $class != 'bklyn-btn-header' ) {

	            $button_aligns = array(
		            'bklyn-btn-left'   => 'left',
		            'bklyn-btn-center' => 'center',
		            'bklyn-btn-right'  => 'right'
	            );

	            $button_align_tablet = $button_align_tablet == 'inherit' ? $button_aligns[$button_align] : $button_align_tablet;
	            $button_align_mobile = $button_align_mobile == 'inherit' ? $button_align_tablet : $button_align_mobile;

	            $classes[]  = $button_align;
	            $classes[]  = 'bklyn-btn-tablet-' . $button_align_tablet;
	            $classes[]  = 'bklyn-btn-mobile-' . $button_align_mobile;

            }
            
            if( $class == 'bklyn-btn-menu' || $class == 'bklyn-btn-header' ) {
                $button_size = '';    
            }
            
            if( $button_hover_shadow == 'yes' && $button_particle == 'no' ) {
                $button_classes[] = 'bklyn-btn-shadow';
            }
            
			if( $button_hover_shadow == 'yes_default' && $button_particle == 'no' ) {
				$button_classes[] = 'bklyn-btn-shadow-active';
			}
			
			// Button with Paritcle and Shadow
			if( $button_hover_shadow == 'yes' && $button_particle == 'yes' ) {
                $p_classes[] = 'bklyn-btn-shadow';
            }
            
			if( $button_hover_shadow == 'yes_default' && $button_particle == 'yes' ) {
				$p_classes[] = 'bklyn-btn-shadow-active';
			}
			
            if( $button_custom_border == 'yes' ) {
                $button_classes[] = 'bklyn-btn-outline';
            }
            
            if( $button_fluid == 'yes' ) {
                $button_classes[] = 'bklyn-btn-full';
				$classes[] = 'bklyn-btn-holder-contains-fuild-button';
            }
            
            if( $button_add_lightbox == 'yes' ) {
                $button_classes[] = 'ut-lightbox';
            }
            
            if( $button_add_icon == 'yes' ) {
                $button_classes[] = 'bklyn-btn-icon-' . $button_icon_align;
            }
            
            
            /* inline css */
            $button_id = uniqid('bklyn_btn_');
            
            $css_style = '<style type="text/css">';
                
                /* button design */
                $button_design_array = array();                
                $button_design_array['effect']                       = $button_effect;
                $button_design_array['button_custom_border']         = $button_custom_border;
                
                $button_design_array['default']['color']             = $button_text_color;
                
				if( ut_is_gradient( $button_border_color ) ) {
					
					$button_design_array['default']['border-image']      = $button_border_color;
					
				} else {
					
					$button_design_array['default']['border-color']      = $button_border_color;
					
					if( $button_border_color == 'transparent' ) {
						
						// reset possible background image
						$button_design_array['default']['background-repeat'] = 'no-repeat !important';
						
					}
					
				}
			
			
                $button_design_array['default']['border-style']      = $button_border_style;
                
                if( !empty( $button_border_width ) ) {
                    $button_design_array['default']['border-width']  = $button_border_width  . 'px';
                }
                
                if( !empty( $button_border_radius ) ) {
                    $button_design_array['default']['border-radius'] = $button_border_radius . 'px';
                }
                
                $button_design_array['default']['background-color']  = $button_background;
                
                $button_design_array['hover']['color']               = $button_text_color_hover;
                
				if( ut_is_gradient( $button_border_color_hover ) ) {
					
					$button_design_array['hover']['border-image'] = $button_border_color_hover;
					
				} else {
					
					$button_design_array['hover']['border-color'] = $button_border_color_hover;
					
					if( $button_border_color_hover == 'transparent' ) {
						
						// reset possible background image
						$button_design_array['hover']['background-repeat'] = 'no-repeat !important';
						
					}					
					
				}
			
                $button_design_array['hover']['background-color']    = $button_background_hover;
			
				if( $button_background_hover == 'transparent' ) {
					
					// reset possible background image
					$button_design_array['hover']['background-image'] = 'none';
					
				}
				
                // alyen effect
                if( $button_effect == 'aylen' ) {
            
                    $button_design_array['before']['background-color']   = $button_background_hover;
                    $button_design_array['after']['background-color']    = $button_background_hover_2;
            
                }
            
                // winona effect
                if( $button_effect == 'winona' ) {
            
                    $button_design_array['after']['color'] = $button_text_color_hover;
            
                }
            
                // nina effect    
                if( $button_effect == 'nina' ) {
            
                    $button_design_array['before']['color'] = $button_text_color;
            
                }
            
            
                /* button styles */
                $css_style .= ut_create_button_css( $button_id, $button_design_array );                
                
				if( !empty( $button_border_radius ) && ( $button_hover_shadow == 'yes' && $button_particle == 'yes' || $button_hover_shadow == 'yes_default' && $button_particle == 'yes' ) ) {
					
					$css_style .= '#' . $button_id . ' .ut-particles-wrapper { border-radius:' . $button_border_radius . 'px; }';
					
				}
			
                /* font css */ 
                if( !empty( $font_size ) && $class == 'bklyn-btn-header' ) {
                    
					if( strpos( $font_size, 'px' ) !== false ) {
						
						$css_style .= '#' . $button_id . ' a.bklyn-btn { font-size:' . $font_size . ' !important; }';
						
					} else {
						
						$css_style .= '#' . $button_id . ' a.bklyn-btn { font-size:' . $font_size . 'px !important; }';
						
					}					
					
                }
						
				if( !empty( $font_weight ) ) {
                    $css_style .= '#' . $button_id . ' a.bklyn-btn { font-weight:' . $font_weight . ' !important; }';

                    $css_style .= '#' . $button_id . '.bklyn-btn-header a.bklyn-btn { font-weight:' . $font_weight . ' !important; }';
                    $css_style .= '#' . $button_id . '.bklyn-btn-header a.bklyn-btn span { font-weight:' . $font_weight . ' !important; }';

                }
            
                if( !empty( $font_family ) ) {
                    $css_style .= '#' . $button_id . ' a.bklyn-btn { font-family:' . $font_family . ' !important; }';
                }
                
                if( !empty( $letter_spacing ) ) {
					
					// fallback letter spacing
					if( $letter_spacing >= 1 || $letter_spacing <= -1 ) {
						$letter_spacing = $letter_spacing / 100;	
					}					
					
					if( strpos( $letter_spacing, 'em' ) !== false ) {
						
						$css_style .= '#' . $button_id . ' a.bklyn-btn { letter-spacing:' . $letter_spacing . ' !important; }';
						
					} else {
						
						$css_style .= '#' . $button_id . ' a.bklyn-btn { letter-spacing:' . $letter_spacing . 'em !important; }';

					}
					
                }
                
                if( !empty( $text_transform ) ) {
                    $css_style .= '#' . $button_id . ' a.bklyn-btn { text-transform:' . $text_transform . ' !important; }';
                }

		        if( $button_icon_type == 'fontawesome' && $fontawesome_v_correction_t  ) {
			        $css_style .= '#' . $button_id . ' a i.fa { margin-top: ' . $fontawesome_v_correction_t . 'px; }';
		        }

		        if( $button_icon_type == 'fontawesome' && $fontawesome_v_correction_b  ) {
			        $css_style .= '#' . $button_id . ' a i.fa { margin-bottom: ' . $fontawesome_v_correction_b . 'px; }';
		        }

				if( !empty( $button_icon_size ) ) {
					$css_style .= '#' . $button_id . ' a i:before { font-size:' . $button_icon_size . 'px; }';					
				}
			
                /* spacing css */
                if( !empty( $spacing ) ) {
                    
                    $css_style .= '#' . $button_id . '.bklyn-btn-holder a { '. $spacing .' }';
                    
                    if( $button_effect == 'winona' ) {
                        
                        $css_style .= '#' . $button_id . ' a.bklyn-btn-effect-winona::after { '. $spacing .' }';
                        
                    }
                    
                }

                if( !empty( $button_particle_spacing_top ) ) {

                    $css_style .= '#' . $button_id . ' .ut-particles  { margin-top:' . $button_particle_spacing_top . '; }';

                }

                if( !empty( $button_particle_spacing_bottom ) ) {

                    $css_style .= '#' . $button_id . ' .ut-particles  { margin-bottom:' . $button_particle_spacing_bottom . '; }';

                }
                
            $css_style .= '</style>';
            
            // animation effect
            $dataeffect = NULL;
            
            if( !empty( $effect ) && $effect != 'none' ) {
                
				if( $button_particle == 'yes' ) {
				
					$p_attributes['data-effect']      = esc_attr( $effect );
					$p_attributes['data-animateonce'] = esc_attr( $animate_once );

					$p_attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;

					if( !empty( $animation_duration ) ) {
						$p_attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );    
					}                

					$p_classes[]  = 'ut-animate-element';
					$p_classes[]  = 'animated';

					if( !$animate_tablet ) {
						$p_classes[]  = 'ut-no-animation-tablet';
					}

					if( !$animate_mobile ) {
						$p_classes[]  = 'ut-no-animation-mobile';
					}

					if( $animate_once == 'infinite' ) {
						$p_classes[]  = 'infinite';
					}
					
				} else {
					
					$attributes['data-effect']      = esc_attr( $effect );
					$attributes['data-animateonce'] = esc_attr( $animate_once );

					$attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;

					if( !empty( $animation_duration ) ) {
						$attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );    
					}                

					$button_classes[]  = 'ut-animate-element';
					$button_classes[]  = 'animated';

					if( !$animate_tablet ) {
						$button_classes[]  = 'ut-no-animation-tablet';
					}

					if( !$animate_mobile ) {
						$button_classes[]  = 'ut-no-animation-mobile';
					}

					if( $animate_once == 'infinite' ) {
						$button_classes[]  = 'infinite';
					}
					
				}
                                
            }
            
            // button effect winona
            if( $button_effect == 'winona' ) {

	            $attributes['data-text'] = $button_text;
                $button_text = '<span>' . $button_text . '</span>';
                
            }

	        // Glitch on Appear
	        if( $glitch_distortion_effect == 'on_appear' ) {

		        $button_classes[] = 'ut-glitch-on-appear';
		        $attributes['data-ut-glitch-class'] = 'ut-simple-glitch-text-' . $glitch_distortion_effect_style;

	        }

	        // Glitch on Hover
	        if( $glitch_distortion_effect == 'on_hover' ) {

		        $button_classes[] = 'ut-simple-glitch-text-hover';
		        $button_classes[] = 'ut-simple-glitch-text-' . $glitch_distortion_effect_style . '-hover';

	        }

	        // Glitch Permanent
	        if( $glitch_distortion_effect == 'permanent' ) {

		        $button_classes[] = 'ut-simple-glitch-text-permanent';
		        $button_classes[] = 'ut-simple-glitch-text-' . $glitch_distortion_effect_style . '-permanent';

	        }

            // attributes string
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );
			
			$p_attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $p_attributes,
                array_keys( $p_attributes )
            ) );
            
            /* start output */
            $output = '';
            
            /* custom css */
            $output .= ut_minify_inline_css( $css_style );
                        
            $output .= '<div id="' . esc_attr( $button_id ) . '" class="bklyn-btn-holder ' . implode(' ', $classes ) . '">';
                
				if( $button_particle == 'yes' ) {
					
					$output .= '<div ' . $p_attributes . ' class="ut-particles-with-animation ' . implode(" ", $p_classes ) . '">';
					
				}			
			
                $output .= '<a title="' . esc_attr( $title ) . '" ' . $attributes . ' ' . $data_iframe . ' href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '" class="bklyn-btn ' . implode(" ", $button_classes ) . ' '  . $button_size . '" ' . $rel . '>';
                
                if( $button_add_icon == 'yes' && $button_icon_align == 'left' ) {
                    
                    if( $button_icon_type == 'bklynicons' && !empty( $button_icon_bklyn ) ) {
                    
                        $button_icon = $button_icon_bklyn;
                        
                    }                     
                    
                    $output .= '<i class="' . $button_icon . '"></i>';    
                    
                }
            
                $output .= '<span class="ut-btn-text">' . $button_text . '</span>';
                
                if( $button_add_icon == 'yes' && $button_icon_align == 'right' ) {
                    
                    if( $button_icon_type == 'bklynicons' && !empty( $button_icon_bklyn ) ) {
                    
                        $button_icon = $button_icon_bklyn;
                        
                    }                     
                    
                    $output .= '<i class="' . $button_icon . '"></i>';    
                    
                }
            
                $output .= '</a>';
			
				if( $button_particle == 'yes' ) {
					
					$output .= '</div>';
					
				}
            
            $output .= '</div>';
                
            return $output;
        
        }
            
    }

}

new UT_BTN;

if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_btn extends WPBakeryShortCode {
        
    }
    
}