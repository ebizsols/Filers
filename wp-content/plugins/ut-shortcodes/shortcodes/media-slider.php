<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Media_Slider' ) ) {

	class UT_Media_Slider {

		private $shortcode;

        private $gallery_id;
        private $atts;

		function __construct() {

			/* shortcode base */
			$this->shortcode = 'ut_media_slider';

			add_action( 'init', array( $this, 'ut_map_shortcode' ) );
			add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );

		}

		function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Media Slider', 'ut_shortcodes' ),
                    'description' 	  => esc_html__( 'A touch enabled slider that lets you create a beautiful responsive carousels and single slides with video support.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'category'        => 'Media',
                    // 'icon'            => 'fa fa-sliders ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/media-slider.png',
                    'class'           => 'ut-vc-icon-module ut-media-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Description', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Only for internal use. This adds a label to Visual Composer for an easier element identification.', 'ut_shortcodes' ),
                            'param_name'        => 'slider_description',
                            'admin_label'       => true,
                            'group'             => 'Slides'
                        ),

	                    array(
		                    'type' => 'dropdown',
		                    'heading' => esc_html__( 'Gallery Slider Style', 'ut_shortcodes' ),
		                    'param_name' => 'slider_layout',
		                    'group' => 'Slides',
		                    'value' => array(
			                    esc_html__( 'Style 1 (default)', 'ut_shortcodes' ) => 'style-one',
			                    esc_html__( 'Style 2' , 'ut_shortcodes' ) => 'style-two',
		                    )
	                    ),

                        array(
                            'type'          => 'param_group',
                            'heading'       => esc_html__( 'Slides', 'ut_shortcodes' ),
                            'group'         => 'Slides',
                            'param_name'    => 'slides',
                            'params' => array(

                                array(
                                    'type'          => 'textfield',
                                    'heading'       => esc_html__( 'Title', 'ut_shortcodes' ),
                                    'param_name'    => 'title',
                                    'admin_label'   => true,
                                ),

                                array(
                                    'type'          => 'dropdown',
                                    'heading'       => esc_html__( 'Slide Type', 'ut_shortcodes' ),
                                    'param_name'    => 'type',
                                    'value'         => array(
                                        esc_html__( 'image'  , 'ut_shortcodes' ) => 'image',
                                        esc_html__( 'video', 'ut_shortcodes' )   => 'video'
                                    ),
                                ),

                                array(
                                    'type'        => 'attach_image',
                                    'heading'     => esc_html__( 'Image', 'ut_shortcodes' ),
                                    'param_name'  => 'image',
                                    'dependency'    => array(
                                        'element' => 'type',
                                        'value'   => array( 'image' ),
                                    )
                                ),

                                array(
                                    'type'              => 'dropdown',
                                    'heading'           => esc_html__( 'Link Image?', 'ut_shortcodes' ),
                                    'param_name'        => 'link_type',
                                    'value'             => array(
                                        esc_html__( 'No Link' , 'ut_shortcodes' ) => 'none',
                                        esc_html__( 'Custom Link' , 'ut_shortcodes' ) => 'custom',
                                        esc_html__( 'Open in Lightbox' , 'ut_shortcodes' ) => 'image',
                                    ),
                                    'dependency'    => array(
                                        'element' => 'type',
                                        'value'   => array( 'image' ),
                                    )
                                ),

                                array(
                                    'type'              => 'vc_link',
                                    'heading'           => esc_html__( 'Link', 'ut_shortcodes' ),
                                    'param_name'        => 'link',
                                    'dependency'  => array(
                                        'element' => 'link_type',
                                        'value'   => 'custom',
                                    )
                                ),

                                array(
                                    'type'          => 'textfield',
                                    'heading'       => esc_html__( 'Video URL', 'ut_shortcodes' ),
                                    'description'   => esc_html__( 'Needs to be Vimeo or Youtube! If you are using vimeo please use the following link markup: https://vimeo.com/XXXXXXX' , 'ut_shortcodes' ),
                                    'param_name'    => 'video',
                                    'dependency'    => array(
                                        'element' => 'type',
                                        'value'   => array( 'video' ),
                                    )

                                ),

                                array(
                                    'type'          => 'attach_image',
                                    'heading'       => esc_html__( 'Poster Image', 'ut_shortcodes' ),
                                    'description'   => sprintf( esc_html__( '%s. This images gets displayed until the video player loads.' , 'ut_shortcodes' ), '<strong>(required)</strong>' ),
                                    'param_name'    => 'poster',
                                    'dependency'    => array(
                                        'element' => 'type',
                                        'value'   => array( 'video' ),
                                    )
                                ),

                            ),

                        ),

                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Image Size', 'ut_shortcodes' ),
                            'description' => sprintf( esc_html__( 'Default Size %sx%s according to "Settings" > "Media" > "Large size".', 'ut_shortcodes' ), get_option('large_size_w'), get_option('large_size_h') ),
                            'param_name' => 'image_size',
                            'group' => 'Slides',
                            'value' => array(
                                esc_html__( 'Default Size', 'ut_shortcodes' ) => 'large',
                                esc_html__( 'Thumbnail (cropped)' , 'ut_shortcodes' ) => 'thumbnail',
                                esc_html__( 'Medium (cropped)' , 'ut_shortcodes' ) => 'medium',
                                esc_html__( 'Original' , 'ut_shortcodes' ) => 'full',
                                esc_html__( 'Custom Size', 'ut_shortcodes' ) => 'custom',
                            )
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Custom Size Width', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Value in px. e.g. 800', 'ut_shortcodes' ),
                            'param_name'        => 'image_custom_width',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Slides',
                            'dependency'        => array(
                                'element' => 'image_size',
                                'value'   => 'custom',
                            )
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Custom Size Height', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Value in px. e.g. 600', 'ut_shortcodes' ),
                            'param_name'        => 'image_custom_height',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Slides',
                            'dependency'        => array(
                                'element' => 'image_size',
                                'value'   => 'custom',
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Crop Images?', 'ut_shortcodes' ),
                            'description'		=> __('What does Soft Crop mean? A soft crop will never cut off any of the image, it will scale the image down until it fits within the dimensions specified, maintaining its original aspect ratio. What does Hard Crop mean? The image will be scaled and then cropped to the exact dimensions you have specified. Depending on the aspect ratio of the image in relation to the crop size, it might happen that the image will be cut off.', 'ut_shortcodes'),
                            'param_name'        => 'image_custom_crop',
                            'group'             => 'Slides',
                            'value'             => array(
                                esc_html__( 'yes, please! (Hard Crop)' , 'ut_shortcodes' ) => 'on',
                                esc_html__( 'no, thanks! (Soft Crop)' , 'ut_shortcodes' )  => 'off',
                            ),
                            'dependency'        => array(
                                'element' => 'image_size',
                                'value'   => 'custom',
                            )
                        ),
                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Slider Type', 'ut_shortcodes' ),
                            'param_name'    => 'slider_type',
                            'group'         => 'Slider Settings',
                            'value'         => array(
                                esc_html__( 'Single Slide'  , 'ut_shortcodes' ) => 'single',
                                esc_html__( 'Carousel', 'ut_shortcodes' ) => 'carousel'
                            ),
                        ),

                        array(
                            'type'          => 'range_slider',
                            'heading'       => esc_html__( 'Slides on Desktop', 'ut_shortcodes' ),
                            'description'   => esc_html__( 'The number of items you want to see on the screen.' , 'ut_shortcodes' ),
                            'param_name'    => 'number',
                            'group'         => 'Slider Settings',
                            'value'             => array(
                                'min'   => '1',
                                'max'   => '5',
                                'step'  => '1',
                                'unit'  => 'x'
                            ),
                            'dependency'    => array(
                                'element' => 'slider_type',
                                'value'   => array( 'carousel' ),
                            )
                        ),

                        array(
                            'type'          => 'range_slider',
                            'heading'       => esc_html__( 'Slides on Tablet', 'ut_shortcodes' ),
                            'description'   => esc_html__( 'The number of items you want to see on the screen.' , 'ut_shortcodes' ),
                            'param_name'    => 'number_tablet',
                            'group'         => 'Slider Settings',
                            'value'             => array(
                                'min'   => '1',
                                'max'   => '5',
                                'step'  => '1',
                                'unit'  => 'x'
                            ),
                            'dependency'    => array(
                                'element' => 'slider_type',
                                'value'   => array( 'carousel' ),
                            )
                        ),

                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Autoplay Slider?', 'ut_shortcodes' ),
                            'param_name'    => 'autoplay',
                            'group'         => 'Slider Settings',
                            'value'         => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true'
                            ),
                        ),

                        array(
                            'type'          => 'textfield',
                            'heading'       => esc_html__( 'Autoplay Timeout', 'ut_shortcodes' ),
                            'description'   => esc_html__( 'Autoplay interval timeout in milliseconds. Default: 5000' , 'ut_shortcodes' ),
                            'param_name'    => 'autoplay_timeout',
                            'group'         => 'Slider Settings',
                            'dependency'    => array(
                                'element' => 'autoplay',
                                'value'   => array( 'true' ),
                            )

                        ),

                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Loop Slider?', 'ut_shortcodes' ),
                            'param_name'    => 'loop',
                            'group'         => 'Slider Settings',
                            'value'         => array(
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no', 'ut_shortcodes' )    => 'false'
                            ),
                        ),

                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Show Next / Prev Navigation?', 'ut_shortcodes' ),
                            'param_name'    => 'nav',
                            'group'         => 'Slider Settings',
                            'value'         => array(
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false'
                            ),
                        ),

                        array(
                            'type'          => 'dropdown',
                            'heading'       => esc_html__( 'Show Dot Navigation?', 'ut_shortcodes' ),
                            'param_name'    => 'dots',
                            'group'         => 'Slider Settings',
                            'value'         => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false',
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',

                            ),
                            'dependency'    => array(
                                'element' => 'slider_type',
                                'value'   => array( 'single' ),
                            )
                        ),
                        array(
                            'type' => 'gradient_picker',
                            'heading' => esc_html__( 'Hover Color', 'ut_shortcodes' ),
                            'param_name' => 'hover_color',
                            'group' => 'Slider Settings',
                        ),
                        array(
                            'type' => 'range_slider',
                            'heading' => esc_html__( 'Hover Color Opacity', 'ut_shortcodes' ),
                            'param_name' => 'hover_color_opacity',
                            'group' => 'Slider Settings',
                            'value' => array(
                                'default' => '90',
                                'min' => '0',
                                'max' => '100',
                                'step' => '1',
                                'unit'=> '%'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animation Effect In', 'ut_shortcodes' ),
                            'param_name'        => 'effect_in',
                            'group'             => 'Slide Effects',
                            'value'             => ut_recognized_in_animation_effects(),
                            'dependency'    => array(
                                'element' => 'slider_type',
                                'value'   => array( 'single' ),
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animation Effect Out', 'ut_shortcodes' ),
                            'param_name'        => 'effect_out',
                            'group'             => 'Slide Effects',
                            'value'             => ut_recognized_out_animation_effects(),
                            'dependency'    => array(
                                'element' => 'slider_type',
                                'value'   => array( 'single' ),
                            )
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'Slides'
                        ),

                        /* caption */
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Show Image Caption Below?', 'ut_shortcodes' ),
                            'param_name' => 'caption',
                            'group' => 'Caption Settings',
                            'value' => array(
                                esc_html__( 'off', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'on', 'ut_shortcodes' ) => 'on',
                            ),
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Image Caption Below Text Transform', 'ut_shortcodes' ),
                            'param_name' => 'caption_text_transform',
                            'group' => 'Caption Settings',
                            'value' => array(
                                esc_html__( 'Select Text Transform', 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize', 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase',
                                esc_html__( 'none', 'ut_shortcodes' ) => 'none',
                            ),
                            'dependency' => array(
                                'element' => 'caption',
                                'value' => array( 'on' ),
                            )

                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Caption Below Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'caption_font_weight',
                            'group'             => 'Caption Settings',
                            'value'             => array(
                                esc_html__( 'bold' , 'ut_shortcodes' )               => 'bold',
                                esc_html__( 'normal' , 'ut_shortcodes' )             => 'normal',
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
                            'dependency' => array(
                                'element' => 'caption',
                                'value' => array( 'on' ),
                            )
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Image Caption Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'caption_letter_spacing',
                            'group'             => 'Caption Settings',
                            'value'             => array(
                                'default'   => '0',
                                'min'   	=> '-0.2',
                                'max'   	=> '0.2',
                                'step'  	=> '0.01',
                                'unit'  	=> 'em'
                            ),
                            'dependency'        => array(
                                'element' => 'hover_caption',
                                'value' => array( 'on' ),
                            )

                        ),
                        array(
                            'type' => 'colorpicker',
                            'heading' => esc_html__( 'Image Caption Below Color', 'ut_shortcodes' ),
                            'param_name' => 'caption_color',
                            'group' => 'Caption Settings',
                            'dependency' => array(
                                'element' => 'caption',
                                'value' => array( 'on' ),
                            )
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Show Image Caption On Hover?', 'ut_shortcodes' ),
                            'param_name' => 'hover_caption',
                            'group' => 'Caption Settings',
                            'value' => array(
                                esc_html__( 'off', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'on', 'ut_shortcodes' ) => 'on',
                            ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Image Caption On Hover Font Size', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional) value in px or em, eg "20px"' , 'ut_shortcodes' ),
                            'param_name'        => 'hover_caption_font_size',
                            'group'             => 'Caption Settings',
                            'dependency'        => array(
                                'element' => 'hover_caption',
                                'value' => array( 'on' ),
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Image Caption On Hover Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'hover_caption_font_weight',
                            'group'             => 'Caption Settings',
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
                            'dependency'        => array(
                                'element' => 'hover_caption',
                                'value' => array( 'on' ),
                            )
                        ),
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__( 'Image Caption On Hover Text Transform', 'ut_shortcodes' ),
                            'param_name' => 'hover_caption_text_transform',
                            'group' => 'Caption Settings',
                            'value' => array(
                                esc_html__( 'Select Text Transform', 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize', 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase',
                                esc_html__( 'none', 'ut_shortcodes' ) => 'none',
                            ),
                            'dependency' => array(
                                'element' => 'hover_caption',
                                'value' => array( 'on' ),
                            )

                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Image Caption On Hover Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'hover_caption_letter_spacing',
                            'group'             => 'Caption Settings',
                            'value'             => array(
                                'default'   => '0',
                                'min'   	=> '-0.2',
                                'max'   	=> '0.2',
                                'step'  	=> '0.01',
                                'unit'  	=> 'em'
                            ),
                            'dependency'        => array(
                                'element' => 'hover_caption',
                                'value' => array( 'on' ),
                            )

                        ),
                        array(
                            'type' => 'colorpicker',
                            'heading' => esc_html__( 'Image Caption On Hover Color', 'ut_shortcodes' ),
                            'param_name' => 'hover_caption_color',
                            'group' => 'Caption Settings',
                            'dependency' => array(
                                'element' => 'hover_caption',
                                'value' => array( 'on' ),
                            )
                        ),

                        /* colors */
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Arrow Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_color',
                            'group'             => 'Navigation Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Arrow Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_background_color',
                            'group'             => 'Navigation Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Arrow Color Hover', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_color_hover',
                            'group'             => 'Navigation Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Arrow Background Color Hover', 'ut_shortcodes' ),
                            'param_name'        => 'arrow_background_color_hover',
                            'group'             => 'Navigation Colors'
                        ),

                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Dot Color', 'ut_shortcodes' ),
                            'param_name'        => 'dot_color',
                            'group'             => 'Navigation Colors',
                            'dependency'        => array(
                                'element' => 'dots',
                                'value' => array( 'true' ),
                            )
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Dot Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'dot_color_hover',
                            'group'             => 'Navigation Colors',
                            'dependency'        => array(
                                'element' => 'dots',
                                'value' => array( 'true' ),
                            )
                        ),

                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Video Play and Maximize Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'video_icon_color',
                            'group'             => 'Navigation Colors'
                        ),

                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Video Play and Maximize Icon Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'video_icon_bg_color',
                            'group'             => 'Navigation Colors'
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



		}

		function ut_create_inline_css( $id ) {

			extract( shortcode_atts( array (
				'hover_color'                  => '',
				'hover_color_opacity'          => '',
				'arrow_color'                  => '',
				'arrow_color_hover'            => '',
				'arrow_background_color'       => '',
				'arrow_background_color_hover' => '',
				'dot_color'                    => '',
				'dot_color_hover'              => '',
				'video_icon_color'             => '',
				'video_icon_bg_color'          => '',
				'caption_color'                => '',
				'caption_text_transform'       => '',
				'caption_font_weight' 		   => '',
				'caption_letter_spacing' 	   => '',
				'hover_caption'                => '',
				'hover_caption_color'          => '',
				'hover_caption_text_transform' => '',
				'hover_caption_letter_spacing' => '',
				'hover_caption_font_weight'    => '',
				'hover_caption_font_size' 	   => '',
			), $this->atts ) );

			ob_start();

			?>

            <style type="text/css">

                <?php
			
				if( $arrow_color && ut_is_gradient( $arrow_color ) ) :

					echo ut_create_gradient_css( $arrow_color, '#' . $id . ' .ut-next-gallery-slide i', false, 'background' );
					echo ut_create_gradient_css( $arrow_color, '#' . $id . ' .ut-prev-gallery-slide i', false, 'background' );

				elseif( $arrow_color ) : ?>

                #<?php echo $id; ?> .ut-next-gallery-slide { color: <?php echo $arrow_color; ?>;}
                #<?php echo $id; ?> .ut-prev-gallery-slide { color: <?php echo $arrow_color; ?>;}

                <?php endif; ?>

                <?php
			
				if( $arrow_color_hover && ut_is_gradient( $arrow_color_hover ) ) :

					echo ut_create_gradient_css( $arrow_color_hover, '#' . $id . ' .ut-next-gallery-slide:hover i', false, 'background' );
					echo ut_create_gradient_css( $arrow_color_hover, '#' . $id . ' .ut-prev-gallery-slide:hover i', false, 'background' );

				elseif( $arrow_color_hover ) : ?>

                #<?php echo $id; ?> .ut-next-gallery-slide:hover { color: <?php echo $arrow_color_hover; ?>;}
                #<?php echo $id; ?> .ut-prev-gallery-slide:hover { color: <?php echo $arrow_color_hover; ?>;}

                <?php endif; ?>

                <?php 
			
				if( $arrow_background_color && ut_is_gradient( $arrow_background_color ) ) :
                    
                    echo ut_create_gradient_css( $arrow_background_color, '#' . $id . ' .ut-next-gallery-slide', false, 'background' );
					echo ut_create_gradient_css( $arrow_background_color, '#' . $id . ' .ut-prev-gallery-slide', false, 'background' );
                    
                elseif( $arrow_background_color ) : ?>

                #<?php echo $id; ?> .ut-next-gallery-slide { background: <?php echo $arrow_background_color; ?>;}
                #<?php echo $id; ?> .ut-prev-gallery-slide { background: <?php echo $arrow_background_color; ?>;}

                <?php endif; ?>

                <?php if( $arrow_background_color_hover && ut_is_gradient( $arrow_background_color_hover ) ) :
                    
                    echo ut_create_gradient_css( $arrow_background_color_hover, '#' . $id . ' .ut-next-gallery-slide:hover', false, 'background' );
					echo ut_create_gradient_css( $arrow_background_color_hover, '#' . $id . ' .ut-prev-gallery-slide:hover', false, 'background' );
                    
                elseif( $arrow_background_color_hover ) : ?>

                #<?php echo $id; ?> .ut-next-gallery-slide:hover { background: <?php echo $arrow_background_color_hover; ?>;}
                #<?php echo $id; ?> .ut-prev-gallery-slide:hover { background: <?php echo $arrow_background_color_hover; ?>;}

                <?php endif; ?>

                <?php if( $dot_color && ut_is_gradient( $dot_color ) ) :
                    
                    echo ut_create_gradient_css( $dot_color, '#' . $id . ' .owl-theme .owl-dots .owl-dot span', false, 'background' );
                    
                elseif( $dot_color ) : ?>

                #<?php echo $id; ?> .owl-theme .owl-dots .owl-dot span { background: <?php echo $dot_color; ?>;}

                <?php endif; ?>

                <?php if( $dot_color_hover && ut_is_gradient( $dot_color_hover ) ) :
                    
                    echo ut_create_gradient_css( $dot_color_hover, '#' . $id . ' .owl-theme .owl-dots .owl-dot.active span', false, 'background' );
					echo ut_create_gradient_css( $dot_color_hover, '#' . $id . ' .owl-theme .owl-dots .owl-dot:hover span', false, 'background' );
                    
                elseif( $dot_color_hover ) : ?>

                #<?php echo $id; ?> .owl-theme .owl-dots .owl-dot.active span,
                #<?php echo $id; ?> .owl-theme .owl-dots .owl-dot:hover span {
                                        background:<?php echo $dot_color_hover; ?>;
                                    }

                <?php endif; ?>

                <?php 
			
				if( $video_icon_color && ut_is_gradient( $video_icon_color ) ) :
                    
                    echo ut_create_gradient_css( $video_icon_color, '#' . $id . ' .ut-owl-video-play-icon i', false, 'background' );
					echo ut_create_gradient_css( $video_icon_color, '#' . $id . ' .ut-slider-maximize i', false, 'background' );
                    
                elseif( $video_icon_color ) : ?>

                #<?php echo $id; ?> .ut-owl-video-play-icon,
                #<?php echo $id; ?> .ut-slider-maximize {
                                        color:<?php echo $video_icon_color; ?>;
                                    }

                <?php endif; ?>

                <?php if( $video_icon_bg_color && ut_is_gradient( $video_icon_bg_color ) ) :
                    
                    echo ut_create_gradient_css( $video_icon_bg_color, '#' . $id . ' .ut-owl-video-play-icon', false, 'background' );
					echo ut_create_gradient_css( $video_icon_bg_color, '#' . $id . ' .ut-slider-maximize', false, 'background' );
                    
                elseif( $video_icon_bg_color ) : ?>

                #<?php echo $id; ?> .ut-owl-video-play-icon,
                #<?php echo $id; ?> .ut-slider-maximize {
                                        background:<?php echo $video_icon_bg_color; ?>;
                                    }

                <?php endif; ?>

                <?php if( $caption_color ): ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption {
                                        color: <?php echo $caption_color ?>;
                                    }

                <?php endif; ?>

                <?php if( $caption_text_transform ): ?>

                #<?php echo $id; ?>

                .ut-gallery-slider-caption {
                    text-transform: <?php echo $caption_text_transform; ?>;
                }

                <?php endif; ?>

                <?php if( $caption_font_weight ): ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption {
                                        font-weight: <?php echo $caption_font_weight; ?>;
                                    }

                <?php endif; ?>

                <?php if( $caption_letter_spacing ): ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption {
                                        letter-spacing: <?php echo $caption_letter_spacing; ?>em;
                                    }

                <?php endif; ?>

                <?php if( $hover_caption_color ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap::before {
                                        color: <?php echo $hover_caption_color; ?>;
                                    }

                <?php endif; ?>

                <?php if( $hover_caption_text_transform ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap::before {
                                        text-transform: <?php echo $hover_caption_text_transform; ?>;
                                    }

                <?php endif; ?>

                <?php if( $hover_caption_font_size ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap::before {
                                        font-size: <?php echo $hover_caption_font_size; ?>;
                                    }

                <?php endif; ?>

                <?php if( $hover_caption_font_weight ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap::before {
                                        font-weight: <?php echo $hover_caption_font_weight; ?>;
                                    }

                <?php endif; ?>

                <?php if( $hover_caption_letter_spacing ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap::before {
                                        letter-spacing: <?php echo $hover_caption_letter_spacing; ?>em;
                                    }

                <?php endif; ?>

                <?php if( $hover_caption != 'on' ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap::before { content: ""; }

                <?php endif; ?>

                <?php if( $hover_color && ut_is_gradient( $hover_color ) ) :
				
					echo ut_create_gradient_css( $hover_color, '#' . $id . ' .ut-gallery-slider-caption-wrap::after', false, 'background', true );	 
			
				elseif( $hover_color ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap::after {
                                        background-color: <?php echo $hover_color; ?>;
                                    }

                <?php endif; ?>

                <?php if( $hover_color_opacity != '' ) : ?>

                #<?php echo $id; ?> .ut-gallery-slider-caption-wrap:hover::after {
                                        opacity: <?php echo $hover_color_opacity / 100; ?>
                                    }

                <?php endif; ?>

            </style>

			<?php

			return ob_get_clean();

		}

        function slider_settings_json() {

            /* no custom js for search excerpts */
            if ( is_search() ) {
                return '';
            }

            /**
             * @var $slider_type
             * @var $effect_in
             * @var $effect_out
             * @var $autoplay
             * @var $autoplay_timeout
             * @var $loop
             * @var $dots
             * @var $number
             * @var $number_tablet
             * @var $lightbox
             *
             */
            extract( shortcode_atts( array(
                'slider_type'       => 'single',
                'caption'           => 'on',
                'effect_in'         => '',
                'effect_out'        => '',
                'autoplay'          => 'false',
                'autoplay_timeout'  => 5000,
                'loop'              => 'true',
                'nav'               => 'true',
                'zoom'              => 'true',
                'dots'              => 'false',
                'number'            => 1,
                'number_tablet'     => 1,
                'lightbox'          => 'yes',
            ), $this->atts ) );

            if( $slider_type == 'single' ) {

                $json = array(
                    'items'              => '1',
                    'lazyLoad'           => true,
                    'smartSpeed'         => 600,
                    'animateIn'          => filter_var( $effect_in, FILTER_VALIDATE_BOOLEAN ),
                    'animateOut'         => filter_var( $effect_out, FILTER_VALIDATE_BOOLEAN ),
                    'autoplay'           => filter_var( $autoplay, FILTER_VALIDATE_BOOLEAN ),
                    'autoplayHoverPause' => true,
                    'autoplayTimeout'    => $autoplay_timeout,
                    'loop'               => filter_var( $loop, FILTER_VALIDATE_BOOLEAN ),
                    'nav'                => false,
                    'dots'               => filter_var( $dots, FILTER_VALIDATE_BOOLEAN ),
                    'lightbox'           => $lightbox
                );

            } else {

                $json = array(
                    'items'              => $number,
                    'lazyLoad'           => true,
                    'smartSpeed'         => 600,
                    'animateIn'          => filter_var( $effect_in, FILTER_VALIDATE_BOOLEAN ),
                    'animateOut'         => filter_var( $effect_out, FILTER_VALIDATE_BOOLEAN ),
                    'autoplay'           => filter_var( $autoplay, FILTER_VALIDATE_BOOLEAN ),
                    'autoplayHoverPause' => true,
                    'autoplayTimeout'    => $autoplay_timeout,
                    'loop'               => true,
                    'nav'                => false,
                    'dots'               => filter_var( $dots, FILTER_VALIDATE_BOOLEAN ),
                    'responsiveClass'    => true,
                    'responsive'         => array(
                        0 => array(
                            'items' => 1
                        ),
                        768 => array(
                            'items' => $number_tablet
                        ),
                        1025 => array(
                            'items' => $number
                        )

                    ),
                    'lightbox'           => $lightbox
                );

            }

            return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

        }

		function create_retina_url( $image ) {

			$extension  = pathinfo( $image, PATHINFO_EXTENSION );

			/* retina url */
			$retina = str_replace('.' . $extension, '@2x.' . $extension, $image );
			return $retina;

		}

		function create_image_slide( $image, $caption ) {

			if( empty( $image['image'] ) ) {
				return '';
			}

            /**
             * @var $image_size
             * @var $image_custom_width
             * @var $image_custom_height
             * @var $image_custom_crop
             */

			extract( shortcode_atts( array(
				'image_size' 			=> 'large',
				'image_custom_width' 	=> '',
				'image_custom_height' 	=> '',
				'image_custom_crop' 	=> 'on',
			), $this->atts ) );

			$image_id = $image['image'];

			// get image
			if( $image_size == 'custom' ) {

				$new_image = array();
				$thumbnail = wp_get_attachment_image_src( $image_id, $image_size );

				if( isset( $thumbnail[0] ) && strpos( $thumbnail[0], '.svg' ) !== false ) {

					$new_image[0] = $thumbnail[0];
					$new_image[1] = $image_custom_width;
					$new_image[2] = $image_custom_height;

				} else {

					if( $image_custom_crop == 'on' ) {

						$new_image[0] = ut_resize( $thumbnail[0], $image_custom_width, $image_custom_height, true, true, true );
						$new_image[1] = $image_custom_width;
						$new_image[2] = $image_custom_height;

					} else {

						$new_image = ut_resize( $thumbnail[0], $image_custom_width, $image_custom_height, true, false, true );

					}

				}

				// assign new thumb
				$thumbnail = $new_image;

			} else {

				$thumbnail = wp_get_attachment_image_src( $image_id, $image_size );

				if( $image_size == 'large' && get_option('large_crop') && ( isset( $thumbnail[1] ) && $thumbnail[1] < get_option('large_size_w') || isset( $thumbnail[2] ) && $thumbnail[2] < get_option('large_size_h') ) ) {

					// create new thumb
					$new_image = array();
					$new_image[0] = ut_resize( $thumbnail[0], get_option('large_size_w'), get_option('large_size_h'), true, true, true );
					$new_image[1] = get_option('large_size_w');
					$new_image[2] = get_option('large_size_h');

					// assign new thumb
					$thumbnail = $new_image;

				}

				// check for SVG
				if( isset( $thumbnail[0] ) && strpos( $thumbnail[0], '.svg' ) !== false ) {

					if( $image_size == 'thumbnail' ) {

						$thumbnail[1] = get_option('thumbnail_size_w');
						$thumbnail[2] = get_option('thumbnail_size_h');

					}

					if( $image_size == 'medium' ) {

						$thumbnail[1] = get_option('medium_size_w');
						$thumbnail[2] = get_option('medium_size_h');

					}

					if( $image_size == 'large' || $image_size == 'full' ) {

						$thumbnail[1] = get_option('large_size_w');
						$thumbnail[2] = get_option('large_size_h');

					}


				}


			}

			// fallback image
			if( empty( $thumbnail ) ) {

				$thumbnail   = array();
				$thumbnail[] = ut_img_asset_url( 'replace-normal.jpg' );
				$thumbnail[] = "";
				$thumbnail[] = "";

			}

			// lightgallery zoom image
            $link_attributes = '';

            if( function_exists('ut_get_morphbox_fullscreen') ) {

                $link_attributes = ut_get_morphbox_meta( $image_id );
                $lightgallery = ut_get_morphbox_fullscreen( $image_id, 'full' );

                /* attributes string */
                $link_attributes = implode(' ', array_map(
                    function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                    $link_attributes,
                    array_keys( $link_attributes )
                ) );

            }

			// fallback image
			if( empty( $lightgallery ) ) {

				$lightgallery   = array();
				$lightgallery[] = ut_img_asset_url( 'replace-normal.jpg' );
				$lightgallery[] = "";
				$lightgallery[] = "";

			}

			// lightgallery thumbnail image
			$mini = wp_get_attachment_image_src( $image_id, 'full' );
			$mini = ut_resize( $mini[0], 200, 200, true, false, false );

			// mini needs to be an array
			if( !is_array( $mini ) && !empty( $mini ) ) {
				$mini = array( $mini );
			}

			// fallback image
			if( empty( $mini ) ) {

				$mini   = array();
				$mini[] = ut_img_asset_url( 'replace-normal.jpg' );
				$mini[] = "";
				$mini[] = "";

			}

			$link_icon = '';

			/* lightbox slide */
			if( isset( $image['link_type'] ) && $image['link_type'] == 'image' ) {

				$sub_html = '';

				if( !empty( get_post($image_id)->post_excerpt ) ) {

					$sub_html = 'data-sub-html="#ut-image-caption-' . $image_id . '"';

				}

				/* link to full size image */
				$link_icon = '<a ' . $sub_html . ' href="' . esc_url( $lightgallery[0] ) . '" data-exthumbimage="' . esc_url( $mini[0] ) . '" class="for-lightbox ut-slider-maximize" data-src="' . esc_url( $lightgallery[0] ) .'" ' . $link_attributes . '><i class="Bklyn-Core-Maximize-3"></i></a>';

			}

			/* custom link slide */
			if( isset( $image['link_type'] ) && $image['link_type'] == 'custom' && !empty( $image['link'] ) ) {

				/* attract link settings */
				$link = vc_build_link( $image['link'] );

				/* set link attributes */
				$link['target'] = empty( $link['target'] ) ? '_self' : $link['target'];
				$link['url']    = empty( $link['url'] )    ? '#'     : $link['url'];
				$rel            = empty( $link['rel'] )    ? ''      : 'rel="' . $link['rel'] . '"';

				$link_icon = '<a class="ut-slider-maximize" target="' . esc_attr( $link['target'] ) . '" href="' . esc_url( $link['url'] ) . '" ' . $rel . '><i class="Bklyn-Core-Maximize-3"></i></a>';

			}

			$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

			if( !empty( get_post( $image_id )->post_excerpt ) ) {

				// if no alt try to assign caption
				$image_alt = empty( $image_alt ) ? get_post( $image )->post_excerpt : $image_alt;

				$caption = $caption == 'on' ? '<div class="ut-gallery-slider-caption">' . get_post( $image_id )->post_excerpt . '</div>' : '';
				return '<div><figure data-caption="' . esc_attr( get_post( $image_id )->post_excerpt ) . '" class="ut-gallery-slider-caption-wrap">' . $link_icon . '<img class="item owl-lazy" alt="' . esc_attr( $image_alt ) . '" data-src="' . esc_url( $thumbnail[0] ) .'" /></figure>' . $caption . '<div id="ut-image-caption-' . $image_id . '" class="ut-vc-images-lightbox-caption">' . get_post( $image_id)->post_excerpt . '</div></div>';


			} else {

				// add alt tag if exist
				$image_alt = !empty( $image_alt ) ? 'alt="' . esc_attr( $image_alt ) . '"' : '';

				/* default slide */
				return '<div><figure class="ut-gallery-slider-caption-wrap">' . $link_icon . '<img class="item owl-lazy" ' . $image_alt . ' data-src="' . esc_url( $thumbnail[0] ) .'" /></figure></div>';

			}

		}

		function create_video_slide( $video, $caption ) {

			if( empty( $video['video'] ) ) {
				return '';
			}

			// get image by size
			if( isset( $video['poster'] ) ) {

			    /**
			     * @var $image_size
			     * @var $image_custom_width
			     * @var $image_custom_height
			     * @var $image_custom_crop
                 */

				extract( shortcode_atts( array(
					'image_size' 			=> 'large',
					'image_custom_width' 	=> '',
					'image_custom_height' 	=> '',
					'image_custom_crop' 	=> 'on',
				), $this->atts ) );

				$image = $video['poster'];

				// get image
				if( $image_size == 'custom' ) {

					$new_image = array();
					$thumbnail = wp_get_attachment_image_src( $image, $image_size );

					if( isset( $thumbnail[0] ) && strpos( $thumbnail[0], '.svg' ) !== false ) {

						$new_image[0] = $thumbnail[0];
						$new_image[1] = $image_custom_width;
						$new_image[2] = $image_custom_height;

					} else {

						if( $image_custom_crop == 'on' ) {

							$new_image[0] = ut_resize( $thumbnail[0], $image_custom_width, $image_custom_height, true, true, true );
							$new_image[1] = $image_custom_width;
							$new_image[2] = $image_custom_height;

						} else {

							$new_image = ut_resize( $thumbnail[0], $image_custom_width, $image_custom_height, true, false, true );

						}

					}

					// assign new thumb
					$thumbnail = $new_image;

				} else {

					$thumbnail = wp_get_attachment_image_src( $image, $image_size );

					if( $image_size == 'large' && get_option('large_crop') && ( isset( $thumbnail[1] ) && $thumbnail[1] < get_option('large_size_w') || isset( $thumbnail[2] ) && $thumbnail[2] < get_option('large_size_h') ) ) {

						// create new thumb
						$new_image = array();
						$new_image[0] = ut_resize( $thumbnail[0], get_option('large_size_w'), get_option('large_size_h'), true, true, true );
						$new_image[1] = get_option('large_size_w');
						$new_image[2] = get_option('large_size_h');

						// assign new thumb
						$thumbnail = $new_image;

					}

					// check for SVG
					if( isset( $thumbnail[0] ) && strpos( $thumbnail[0], '.svg' ) !== false ) {

						if( $image_size == 'thumbnail' ) {

							$thumbnail[1] = get_option('thumbnail_size_w');
							$thumbnail[2] = get_option('thumbnail_size_h');

						}

						if( $image_size == 'medium' ) {

							$thumbnail[1] = get_option('medium_size_w');
							$thumbnail[2] = get_option('medium_size_h');

						}

						if( $image_size == 'large' || $image_size == 'full' ) {

							$thumbnail[1] = get_option('large_size_w');
							$thumbnail[2] = get_option('large_size_h');

						}


					}


				}

			}

			// fallback image
			if( empty( $thumbnail ) ) {

				$thumbnail   = array();
				$thumbnail[] = ut_img_asset_url( 'replace-normal.jpg' );
				$thumbnail[] = "";
				$thumbnail[] = "";

			}

			$sub_html = $lightgallery_caption = '';

			if( isset( $video['poster'] ) && !empty( get_post($video['poster'])->post_excerpt ) ) {

				$sub_html = 'data-sub-html="#ut-image-caption-' . $video['poster'] . '"';
				$lightgallery_caption = '<div id="ut-image-caption-' . $video['poster'] . '" class="ut-vc-images-lightbox-caption">' . get_post($video['poster'])->post_excerpt . '</div>';

			}

			if( isset( $video['poster'] ) && !empty( get_post($video['poster'])->post_excerpt ) ) {

				$caption = $caption == 'on' ? '<div class="ut-gallery-slider-caption">' . get_post( $video['poster'] )->post_excerpt . '</div>' : '';
				return '<div><figure data-caption="' . esc_attr( get_post( $video['poster'] )->post_excerpt ) . '" class="ut-gallery-slider-caption-wrap"><a ' . $sub_html . ' href="' . esc_url( $video['video'] ) . '" class="for-lightbox ut-owl-video-link"><div class="ut-owl-video-play-icon"><i class="Bklyn-Core-Right-6"></i></div></a><img class="item owl-lazy" data-src="' . esc_url( $thumbnail[0] ) .'" />' . $lightgallery_caption . '</figure>' . $caption . '</div>';

			} else {

				return '<figure class="ut-gallery-slider-caption-wrap"><a ' . $sub_html . ' href="' . esc_url( $video['video'] ) . '" class="for-lightbox ut-owl-video-link"><div class="ut-owl-video-play-icon"><i class="Bklyn-Core-Right-6"></i></div></a><img class="item owl-lazy" data-src="' . esc_url( $thumbnail[0] ) .'" />' . $lightgallery_caption . '</figure>';

			}

		}

		function ut_create_shortcode( $atts, $content = NULL ) {

			/* enqueue scripts */
			$this->atts = $atts;

			/**
			 * @var $slides
             * @var $slider_type
             * @var $caption
             * @var $effect_in
             * @var $effect_out
             * @var $nav
             * @var $class
             * @var $css
             * @var $arrow_color
             * @var $arrow_color_hover
             * @var $video_icon_color
             *
             */

			extract( shortcode_atts( array (
				'slides'              => '',
				'slider_layout'       => 'style-one',
                'slider_type'         => 'single',
				'caption'             => 'off',
				'effect_in'           => 'fadeIn',
				'effect_out'          => 'fadeOut',
				'nav'                 => 'true',
				'class'               => '',
				'css'                 => '',
				'arrow_color'         => '',
				'arrow_color_hover'   => '',
				'video_icon_color'    => '',
			), $this->atts ) );

			/* class array */
			$classes = array();
			$el_classes= array();
			$control_classes = array();

			/* extra element class */
			$classes[] = $class;

			if( $arrow_color && ut_is_gradient( $arrow_color ) ) {
				$control_classes[] = 'ut-element-with-gradient-icon';
			}

			if( $arrow_color_hover && ut_is_gradient( $arrow_color_hover ) ) {
				$control_classes[] = 'ut-element-with-gradient-hover-icon';
			}

			if( $video_icon_color && ut_is_gradient( $video_icon_color ) ) {
				$classes[] = 'ut-owl-video-play-icon-with-gradient';
			}

			if( $slider_type == 'carousel' ) {
				$classes[] = 'ut-owl-carousel';
			}

			if( $slider_type == 'single' ) {
				$control_classes[] = 'ut-single-slider-control';
			}

			/* unique ID */
			$this->gallery_id = uniqid("ut_ms_");
			$css_id = uniqid("ut_ms_css_");

			/* slides */
			$slides = vc_param_group_parse_atts( $slides );

			/* start output */
			$output = '';

			/* attach css */
			$output .= ut_minify_inline_css( $this->ut_create_inline_css( $css_id ) );

			if( !empty( $slides ) && is_array( $slides ) ) {

				$output .= '<div id="' . esc_attr( $css_id ) . '" class="ut-owl-gallery-slider-wrap ut-owl-gallery-slider-' . $slider_layout . '">';

				$output .= '<div id="' . esc_attr( $this->gallery_id ) . '" class="ut-owl-gallery-slider ut-owl-gallery-slider-with-media owl-theme owl-carousel ' . implode( ' ', $classes ) . '" data-settings="' . $this->slider_settings_json() . '">';

				foreach( $slides as $slide ) {

					if( $slide['type'] == 'image' ) {
						$output .= $this->create_image_slide( $slide, $caption );
					}

					if( $slide['type'] == 'video' ) {
						$output .= $this->create_video_slide( $slide, $caption );
					}

				}

				$output .= '</div>';

				if( $nav == 'true' && count( $slides ) > 1 ) {
					$output .= '<a href="#" data-for="' . esc_attr( $this->gallery_id ) . '" class="ut-prev-gallery-slide ' . implode( ' ', $control_classes ) . '"><i class="Bklyn-Core-Left-2"></i></a>';
					$output .= '<a href="#" data-for="' . esc_attr( $this->gallery_id ) . '" class="ut-next-gallery-slide ' . implode( ' ', $control_classes ) . '"><i class="Bklyn-Core-Right-2"></i></a>';
				}

				$output .= '</div>';

			}

			return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $this->atts ) . '">' . $output . '</div>';

		}

	}

}

new UT_Media_Slider;