<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Animated_Image' ) ) {
	
    class UT_Animated_Image {

        /**
         * Shortcode
         */

        private $shortcode;

        /**
         * Unique ID
         */

        private $unique_id;

        /**
         * Image ID
         */

        private $image_id;


        /**
         * Reveal ID
         */

        private $reveal_id;


        /**
         * Attributes
         */

        private $atts;

        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_animated_image';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            $base_option = array(
                'name'            => esc_html__( 'Single Image', 'ut_shortcodes' ),
                'description'     => esc_html__( 'With a bunch of cool, fun, and cross-browser animations and lightbox support.', 'ut_shortcodes' ),
                'base'            => $this->shortcode,
                'category'        => 'Media',
                //'icon'            => 'fa fa-image ut-vc-module-icon',
                'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/animated-single-image.png',
                'class'           => 'ut-vc-icon-module ut-media-module',
                'content_element' => true

            );

            $base_params = array(

                array(
                    'type'              => 'attach_image',
                    'heading'           => esc_html__( 'Image', 'ut_shortcodes' ),
                    'param_name'        => 'image',
                    'group'             => 'General'
                ),

                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Image Size', 'ut_shortcodes' ),
                    'param_name'        => 'size',
                    'group'             => 'General',
                    'value'             => array(
                        esc_html__( 'Thumbnail (cropped)' , 'ut_shortcodes' ) => 'thumbnail',
                        esc_html__( 'Medium (cropped)' , 'ut_shortcodes' )    => 'medium',
                        esc_html__( 'Large (cropped)' , 'ut_shortcodes' )     => 'large',
                        esc_html__( 'Original' , 'ut_shortcodes' )  		  => 'full',
                        esc_html__( 'Custom Size' , 'ut_shortcodes' )         => 'custom'

                    )
                ),
                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Custom Size Width', 'ut_shortcodes' ),
                    'description'       => esc_html__( 'Value in px. e.g. 800', 'ut_shortcodes' ),
                    'param_name'        => 'custom_width',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'group'             => 'General',
                    'dependency'        => array(
                        'element' => 'size',
                        'value'   => 'custom',
                    )
                ),
                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Custom Size Height', 'ut_shortcodes' ),
                    'description'       => esc_html__( 'Value in px. e.g. 600', 'ut_shortcodes' ),
                    'param_name'        => 'custom_height',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'group'             => 'General',
                    'dependency'        => array(
                        'element' => 'size',
                        'value'   => 'custom',
                    )
                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Crop Images?', 'ut_shortcodes' ),
                    'description'		=> __('What does Soft Crop mean? A soft crop will never cut off any of the image, it will scale the image down until it fits within the dimensions specified, maintaining its original aspect ratio. What does Hard Crop mean? The image will be scaled and then cropped to the exact dimensions you have specified. Depending on the aspect ratio of the image in relation to the crop size, it might happen that the image will be cut off.', 'ut_shortcodes'),
                    'param_name'        => 'custom_crop',
                    'group'             => 'General',
                    'value'             => array(
                        esc_html__( 'yes, please! (Hard Crop)' , 'ut_shortcodes' ) => 'on',
                        esc_html__( 'no, thanks! (Soft Crop)' , 'ut_shortcodes' )  => 'off',
                    ),
                    'dependency'        => array(
                        'element' => 'size',
                        'value'   => 'custom',
                    )
                ),
                array(
                    'type'              => 'range_slider',
                    'heading'           => esc_html__( 'Image Opacity', 'ut_shortcodes' ),
                    'param_name'        => 'image_opacity',
                    'group'             => 'General',
                    'value'             => array(
                        'default' => '100',
                        'min'     => '0',
                        'max'     => '100',
                        'step'    => '1',
                        'unit'    => '%'
                    ),

                ),

                array(
                    'type'              => 'range_slider',
                    'heading'           => esc_html__( 'Image Border Radius', 'ut_shortcodes' ),
                    'param_name'        => 'image_border_radius',
                    'group'             => 'General',
                    'value'             => array(
                        'default'   => '0',
                        'min'       => '0',
                        'max'       => '100',
                        'step'      => '1',
                        'unit'      => 'px'
                    ),
                ),

                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Image Alignment', 'ut_shortcodes' ),
                    'edit_field_class'  => 'vc_col-sm-4',
                    'param_name'        => 'align',
                    'group'             => 'General',
                    'value'             => array(
                        esc_html__( 'left'   , 'ut_shortcodes' ) => 'left',
                        esc_html__( 'center' , 'ut_shortcodes' ) => 'center',
                        esc_html__( 'right'  , 'ut_shortcodes' ) => 'right',
                    )
                ),
	            array(
		            'type'              => 'dropdown',
		            'heading'           => esc_html__( 'Tablet Image Alignment', 'ut_shortcodes' ),
		            'edit_field_class'  => 'vc_col-sm-4',
		            'param_name'        => 'align_tablet',
		            'group'             => 'General',
		            'value'             => array(
			            esc_html__( 'inherit from larger'   , 'ut_shortcodes' ) => 'inherit',
			            esc_html__( 'left'   , 'ut_shortcodes' ) => 'left',
			            esc_html__( 'center' , 'ut_shortcodes' ) => 'center',
			            esc_html__( 'right'  , 'ut_shortcodes' ) => 'right',
		            )
	            ),
	            array(
		            'type'              => 'dropdown',
		            'heading'           => esc_html__( 'Mobile Image Alignment', 'ut_shortcodes' ),
		            'edit_field_class'  => 'vc_col-sm-4',
		            'param_name'        => 'align_mobile',
		            'group'             => 'General',
		            'value'             => array(
			            esc_html__( 'center' , 'ut_shortcodes' ) => 'center',
		            	esc_html__( 'left'   , 'ut_shortcodes' ) => 'left',
			            esc_html__( 'right'  , 'ut_shortcodes' ) => 'right',
			            esc_html__( 'inherit from larger'   , 'ut_shortcodes' ) => 'inherit',
		            )
	            ),

                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Link Image? (Open in Lightbox)', 'ut_shortcodes' ),
                    'param_name'        => 'link_type',
                    'group'             => 'General',
                    'value'             => array(
                        esc_html__( 'No Link' , 'ut_shortcodes' ) => 'none',
                        esc_html__( 'Custom Link' , 'ut_shortcodes' ) => 'custom',
                        esc_html__( 'Open in Lightbox' , 'ut_shortcodes' ) => 'image',
                        esc_html__( 'Open Iframe in Lightbox', 'ut_shortcodes' ) => 'iframe',
                    )
                ),

                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Image Size in Lightbox', 'ut_shortcodes' ),
                    'param_name'        => 'lightbox_size',
                    'description'       => esc_html__( 'Only applies to "Lightgallery" Lightbox. What does Soft Crop mean? A soft crop will never cut off any of the image, it will scale the image down until it fits within the dimensions specified, maintaining its original aspect ratio. Also keep in mind, that using sizes these sizes except "HD Ready" and "Full" will force an image re-calculation the first time the setting is used. Means if your max_execution time is low, you might have to reload your website a few times until your server was able to process all images.', 'ut_shortcodes' ),
                    'group'             => 'General',
                    'value'             => array(
                        esc_html__( 'HD Ready (1280x720 soft cropped)', 'ut_shortcodes' ) => 'hd',
                        esc_html__( 'Full HD (1920x1280 soft cropped)', 'ut_shortcodes' ) => 'full_hd',
                        esc_html__( 'WQHD (2560x1440 soft cropped)', 'ut_shortcodes' ) => 'wqhd',
                        esc_html__( 'Retina 4k (4096x2304 soft cropped)', 'ut_shortcodes' ) => 'retina_4k',
                        esc_html__( 'Retina 5k (5120x2880 soft cropped)', 'ut_shortcodes' ) => 'retina_5k',
                        esc_html__( 'Original (Full Size no cropping)', 'ut_shortcodes' ) => 'full',
                    ),
                    'dependency'        => array(
                        'element' => 'link_type',
                        'value'   => 'image',
                    )
                ),

                array(
                    'type'              => 'vc_link',
                    'heading'           => esc_html__( 'Link', 'ut_shortcodes' ),
                    'param_name'        => 'link',
                    'group'             => 'General',
                    'dependency'  => array(
                        'element' => 'link_type',
                        'value'   => 'custom',
                    )
                ),
	            array(
		            'type'              => 'iframe',
		            'heading'           => esc_html__( 'Iframe SRC', 'ut_shortcodes' ),
		            'description'       => esc_html__( 'Only insert the iframe src not the entire iframe code! Make sure, that your iframe source allow embedding, otherwise the lightbox stays empty.' , 'ut_shortcodes' ),
		            'param_name'        => 'lightbox_iframe',
		            'group'             => 'General',
		            'dependency'        => array(
			            'element' => 'link_type',
			            'value'   => 'iframe',
		            ),
	            ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Hide Image Title on Mouse Over (Browser Tooltip)?', 'ut_shortcodes' ),
                    'param_name'        => 'hide_image_title',
                    'group'             => 'General',
                    'value'             => array(
                        esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'yes',
                        esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'no'
                    ),

                ),

                // Caption Colors
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Activate Hover Caption?', 'ut_shortcodes' ),
                    'description'       => esc_html__( 'The hover captions contains the image caption or a "plus sign".', 'ut_shortcodes' ),
                    'param_name'        => 'hover',
                    'group'             => 'Caption Settings',
                    'value'             => array(
                        esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                        esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                    ),

                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Hover Caption Style', 'ut_shortcodes' ),
                    'param_name'        => 'caption_style',
                    'group'             => 'Caption Settings',
                    'value'             => array(
                        esc_html__( 'Default Caption' , 'ut_shortcodes' ) => '',
                        esc_html__( 'Caption Style 2' , 'ut_shortcodes' ) => '-style-2',

                    ),
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Hover Caption Content', 'ut_shortcodes' ),
                    'param_name'        => 'caption_content',
                    'group'             => 'Caption Settings',
                    'value'             => array(
                        esc_html__( 'Caption Text (Media Library)', 'ut_shortcodes' ) => 'caption',
                        esc_html__( 'Image Title (Media Library)', 'ut_shortcodes' ) => 'title',
                        esc_html__( 'Plus Sign', 'ut_shortcodes' ) => 'plus',
                        esc_html__( 'Custom Caption', 'ut_shortcodes' ) => 'custom',
                    ),
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),
                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Caption Title', 'ut_shortcodes' ),
                    'param_name'        => 'custom_caption',
                    'group'             => 'Caption Settings',
                    'dependency'        => array(
                        'element' => 'caption_content',
                        'value'   => 'custom',
                    )
                ),
                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Caption Description', 'ut_shortcodes' ),
                    'description'       => esc_html__( '(optional) automatically display with a small font right below the regular custom text.' , 'ut_shortcodes' ),
                    'param_name'        => 'custom_caption_small',
                    'group'             => 'Caption Settings',
                    'dependency'        => array(
                        'element' => 'caption_content',
                        'value'   => 'custom',
                    )
                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Caption Text Transform', 'ut_shortcodes' ),
                    'param_name'        => 'caption_transform',
                    'group'             => 'Caption Settings',
                    'value'             => array(
                        esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                        esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                        esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                        esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                    ),
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Caption Font Weight', 'ut_shortcodes' ),
                    'param_name'        => 'caption_font_weight',
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
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),
                array(
                    'type'              => 'range_slider',
                    'heading'           => esc_html__( 'Caption Letter Spacing', 'ut_shortcodes' ),
                    'param_name'        => 'caption_letter_spacing',
                    'group'             => 'Caption Settings',
                    'value'             => array(
                        'default'   => ut_get_theme_options_font_setting( 'h3', 'letter-spacing', "0" ),
                        'min'   	=> '-0.2',
                        'max'   	=> '0.2',
                        'step'  	=> '0.01',
                        'unit'  	=> 'em'
                    ),
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )

                ),

                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Caption Font Size', 'ut_shortcodes' ),
                    'description'       => esc_html__( '(optional) value in px or em, eg "20px"' , 'ut_shortcodes' ),
                    'param_name'        => 'caption_font_size',
                    'group'             => 'Caption Settings',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),

                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Caption Line height', 'ut_shortcodes' ),
                    'description'       => esc_html__( '(optional)' , 'ut_shortcodes' ),
                    'param_name'        => 'caption_line_height',
                    'group'             => 'Caption Settings',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),

                array(
                    'type'              => 'colorpicker',
                    'heading'           => esc_html__( 'Caption Text Color', 'ut_shortcodes' ),
                    'param_name'        => 'caption_color',
                    'group'             => 'Caption Settings',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),

                array(
                    'type'              => 'gradient_picker',
                    'heading'           => esc_html__( 'Caption Hover Color', 'ut_shortcodes' ),
                    'param_name'        => 'caption_background',
                    'group'             => 'Caption Settings',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'dependency'        => array(
                        'element' => 'hover',
                        'value'   => 'yes',
                    )
                ),

                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Show Image Caption Below?', 'ut_shortcodes' ),
                    'param_name'        => 'caption_below',
                    'group'             => 'Caption Settings',
                    'value' => array(
                        esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                        esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                    ),
                ),

                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Caption Below Text Transform', 'ut_shortcodes' ),
                    'param_name'        => 'caption_below_transform',
                    'group'             => 'Caption Settings',
                    'value'             => array(
                        esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                        esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                        esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                        esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                    ),
                    'dependency'        => array(
                        'element' => 'caption_below',
                        'value'   => 'yes',
                    )
                ),

                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Caption Below Font Weight', 'ut_shortcodes' ),
                    'param_name'        => 'caption_below_font_weight',
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
                    'dependency'        => array(
                        'element' => 'caption_below',
                        'value'   => 'yes',
                    )
                ),



                array(
                    'type'              => 'range_slider',
                    'heading'           => esc_html__( 'Caption Below Letter Spacing', 'ut_shortcodes' ),
                    'param_name'        => 'caption_below_letter_spacing',
                    'group'             => 'Caption Settings',
                    'value'             => array(
                        'def'   => '0',
                        'min'   => '-0.2',
                        'max'   => '0.2',
                        'step'  => '0.01',
                        'unit'  => 'em'
                    ),
                    'dependency'        => array(
                        'element' => 'caption_below',
                        'value'   => 'yes',
                    )

                ),

                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Caption Below Font Size', 'ut_shortcodes' ),
                    'description'       => esc_html__( '(optional) value in px or em, eg "20px"' , 'ut_shortcodes' ),
                    'param_name'        => 'caption_below_font_size',
                    'group'             => 'Caption Settings',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'dependency'        => array(
                        'element' => 'caption_below',
                        'value'   => 'yes',
                    )
                ),

                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'Caption Below Line height', 'ut_shortcodes' ),
                    'description'       => esc_html__( '(optional)' , 'ut_shortcodes' ),
                    'param_name'        => 'caption_below_line_height',
                    'group'             => 'Caption Settings',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'dependency'        => array(
                        'element' => 'caption_below',
                        'value'   => 'yes',
                    )
                ),

                array(
                    'type'              => 'colorpicker',
                    'heading'           => esc_html__( 'Caption Below Text Color', 'ut_shortcodes' ),
                    'param_name'        => 'caption_below_color',
                    'group'             => 'Caption Settings',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'dependency'        => array(
                        'element' => 'caption_below',
                        'value'   => 'yes',
                    )
                ),

                // Caption Extra Settings
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Deactivate Image Offset?', 'ut_shortcodes' ),
                    'param_name'        => 'image_offset',
                    'group'             => 'Extra Settings',
                    'value'             => array(
                        esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'no',
                        esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'yes',
                    ),
                    'dependency'        => array(
                        'element' => 'caption_style',
                        'value'   => '-style-2',
                    )
                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Activate Mouse Image Zoom?', 'ut_shortcodes' ),
                    'param_name'        => 'image_zoom',
                    'group'             => 'Extra Settings',
                    'value'             => array(
                        esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'yes',
                        esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'no'
                    ),
                    'dependency'        => array(
                        'element' => 'caption_style',
                        'value'   => '-style-2',
                    )
                ),

                // Shadow
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Activate Image Shadow?', 'ut_shortcodes' ),
                    'description'       => esc_html__( 'Note, image shadows require additional space. This required space reduces the image display size.', 'ut_shortcodes' ),
                    'param_name'        => 'shadow',
                    'group'             => 'Shadow',
                    'value'             => array(
                        esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'no',
                        esc_html__( 'Style 1', 'ut_shortcodes' )     => 'ut-box-shadow-1',
                        esc_html__( 'Style 2', 'ut_shortcodes' )     => 'ut-box-shadow-2',
                        esc_html__( 'Style 3', 'ut_shortcodes' )     => 'ut-box-shadow-3',
                        esc_html__( 'Style 4', 'ut_shortcodes' )     => 'ut-box-shadow-4',
                        esc_html__( 'Style 5', 'ut_shortcodes' )     => 'ut-box-shadow-5',
                        esc_html__( 'Style 6', 'ut_shortcodes' )     => 'ut-box-shadow-6',
                        esc_html__( 'Style 7', 'ut_shortcodes' )     => 'ut-box-shadow-7',
                        esc_html__( 'Style 8', 'ut_shortcodes' )     => 'ut-box-shadow-8',
                    ),
                    'dependency' => array(
                        'element'              => 'gap',
                        'value_not_equal_to'   => array( '0', '1', '5', '10' ),
                    ),
                ),

                array(
                    'type'              => 'colorpicker',
                    'heading'           => esc_html__( 'Shadow Canvas Color', 'ut_shortcodes' ),
                    'description'       => esc_html__( 'Should be the same as the background color the shadows displays on.', 'ut_shortcodes' ),
                    'param_name'        => 'shadow_canvas_color',
                    'group'             => 'Shadow',
                    'dependency'        => array(
                        'element'              => 'shadow',
                        'value_not_equal_to'   => array('no'),
                    )
                ),

	            array(
		            'type'              => 'colorpicker',
		            'heading'           => esc_html__( 'Shadow Color', 'ut_shortcodes' ),
		            'param_name'        => 'shadow_color',
		            'group'             => 'Shadow',
		            'dependency'        => array(
			            'element'              => 'shadow',
			            'value_not_equal_to'   => array('no'),
		            ),
	            ),



                /* reveal fx */
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Activate Reveal Effect?', 'ut_shortcodes' ),
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
                    'dependency' => array(
                        'element' => 'revealfx',
                        'value' => 'on',
                    ),
                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Activate Reveal Direction', 'ut_shortcodes' ),
                    'param_name'        => 'revealfx_direction',
                    'group'             => 'Reveal Effect',
                    'value'             => array(
                        esc_html__( 'left to right' , 'ut_shortcodes' ) => 'lr',
                        esc_html__( 'right to left' , 'ut_shortcodes' ) => 'rl',
                        esc_html__( 'top to bottom' , 'ut_shortcodes' ) => 'tb',
                        esc_html__( 'bottom to top' , 'ut_shortcodes' ) => 'bt',
                    ),
                    'dependency' => array(
                        'element' => 'revealfx',
                        'value' => 'on',
                    ),

                ),
                array(
                    'type'              => 'range_slider',
                    'heading'           => esc_html__( 'Reveal Delay', 'ut_shortcodes' ),
                    'param_name'        => 'revealfx_delay',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'group'             => 'Reveal Effect',
                    'value'             => array(
                        'default'   => '0',
                        'min'   	=> '0',
                        'max'   	=> '3000',
                        'step'  	=> '50',
                        'unit'  	=> 'ms'
                    ),
                    'dependency' => array(
                        'element' => 'revealfx',
                        'value' => 'on',
                    ),
                ),
                array(
                    'type'              => 'range_slider',
                    'heading'           => esc_html__( 'Reveal Duration', 'ut_shortcodes' ),
                    'param_name'        => 'revealfx_duration',
                    'edit_field_class'  => 'vc_col-sm-6',
                    'group'             => 'Reveal Effect',
                    'value'             => array(
                        'default'   => '750',
                        'min'   	=> '200',
                        'max'   	=> '5000',
                        'step'  	=> '50',
                        'unit'  	=> 'ms'
                    ),
                    'dependency' => array(
                        'element' => 'revealfx',
                        'value' => 'on',
                    ),
                ),
                array(
                    'type'              => 'dropdown',
                    'heading'           => esc_html__( 'Animation Offset', 'ut_shortcodes' ),
                    'description'       => esc_html__( '', 'ut_shortcodes' ),
                    'param_name'        => 'appear_offset',
                    'group'             => 'Reveal Effect',
                    'value'             => array(
                        esc_html__( 'auto (image needs to be 50% in viewport)' , 'ut_shortcodes' ) => 'auto',
                        esc_html__( 'almost (image needs to be 75% in viewport)' , 'ut_shortcodes' ) => 'almost',
                        esc_html__( 'full (image needs to be fully in viewport)' , 'ut_shortcodes' ) => 'full',
                        esc_html__( 'partial (image needs to be 33% in viewport)' , 'ut_shortcodes' ) => 'partial',
                        esc_html__( 'direct (image needs hits viewport)' , 'ut_shortcodes' ) => 'none',
                    ),
                    'dependency'        => array(
                        'element'           => 'revealfx',
                        'value'             => 'on',
                    ),

                ),

	            /* Rotation */
	            array(
		            'type'              => 'range_slider',
		            'heading'           => esc_html__( 'Rotate Video?', 'ut_shortcodes' ),
		            'description'       => esc_html__( 'Note: Depending on selected rotation, the image might cut off at the edges by row or section overflow. You can deactivate the overflow hidden option directly in row or section settings.', 'ut_shortcodes' ),
		            'param_name'        => 'rotation',
		            'group'             => 'Rotation',
		            'value'             => array(
			            'default' => '0',
			            'min'     => '-180',
			            'max'     => '180',
			            'step'    => '1',
			            'unit'    => 'deg'
		            ),
	            ),

	            array(
		            'type'              => 'dropdown',
		            'heading'           => esc_html__( 'Apply different rotation on tablet?', 'ut_shortcodes' ),
		            'param_name'        => 'rotation_tablet_change',
		            'group'             => 'Rotation',
		            'value'             => array(
			            esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
			            esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
		            ),
	            ),

	            array(
		            'type'              => 'range_slider',
		            'heading'           => esc_html__( 'Tablet Rotation', 'ut_shortcodes' ),
		            'description'       => esc_html__( 'Note: Depending on selected rotation, the image might cut off at the edges by row or section overflow. You can deactivate the overflow hidden option directly in row or section settings.', 'ut_shortcodes' ),
		            'param_name'        => 'rotation_tablet',
		            'group'             => 'Rotation',
		            'value'             => array(
			            'default' => '0',
			            'min'     => '-180',
			            'max'     => '180',
			            'step'    => '1',
			            'unit'    => 'deg'
		            ),
		            'dependency'        => array(
			            'element' => 'rotation_tablet_change',
			            'value'   => 'on',
		            ),
	            ),

	            array(
		            'type'              => 'dropdown',
		            'heading'           => esc_html__( 'Apply different rotation on mobile?', 'ut_shortcodes' ),
		            'param_name'        => 'rotation_mobile_change',
		            'group'             => 'Rotation',
		            'value'             => array(
			            esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
			            esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
		            ),
	            ),

	            array(
		            'type'              => 'range_slider',
		            'heading'           => esc_html__( 'Mobile Rotation', 'ut_shortcodes' ),
		            'description'       => esc_html__( 'Note: Depending on selected rotation, the image might cut off at the edges by row or section overflow. You can deactivate the overflow hidden option directly in row or section settings.', 'ut_shortcodes' ),
		            'param_name'        => 'rotation_mobile',
		            'group'             => 'Rotation',
		            'value'             => array(
			            'default' => '0',
			            'min'     => '-180',
			            'max'     => '180',
			            'step'    => '1',
			            'unit'    => 'deg'
		            ),
		            'dependency'        => array(
			            'element' => 'rotation_mobile_change',
			            'value'   => 'on',
		            ),
	            ),


                /* Animation Effect */
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

                /* css editor */
                array(
                    'type'              => 'textfield',
                    'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                    'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                    'param_name'        => 'class',
                    'group'             => 'General'
                ),

                /* custom cursor */
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Custom Cursor Skin', 'ut_shortcodes' ),
                    'description' => __( 'Only applies when custom cursor is active. Check Theme Options > Advanced > Custom Cursor.', 'ut_shortcodes' ),
                    'param_name' => 'cursor_skin',
                    'value' => _vc_get_cursor_skins( true ),
                    'group' => 'Custom Cursor',

                ),

                array(
                    'type'              => 'css_editor',
                    'param_name'        => 'css',
                    'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                ),

            );

            // add new params
            $params = ut_update_map_shortcode( $base_params, _vc_add_glitch_settings_to_element( 'Glitch' ), 'appear_offset' );

            // add all params
            $base_option['params'] = $params;

            // map shortcode
            vc_map( $base_option );
        
        }

        function create_placeholder_svg( $width , $height ){

            // fallback values
            $width = empty( $width ) ? '800' : $width;
            $height = empty( $height ) ? '600' : $height;

            return 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\' viewBox%3D\'0 0 ' . esc_attr( $width ) . ' ' . esc_attr( $height ) . '\'%2F%3E';

        }

        /*
		 * Create Glitch Tag
		 */

        public function create_glitch_effect( $image ) {

            /**
             * @var string $glitch_effect
             * @var string $permanent_glitch
             * @var string $accent_1
             * @var string $accent_2
             * @var string $accent_3
             * @var string $glitch_transparent
             * @var string $glitch_effect_transparent
             */

            extract( shortcode_atts( array (
                'glitch_effect'             => 'none',
                'permanent_glitch'          => 'on',
                'accent_1'                  => '',
                'accent_2'                  => '',
                'accent_3'                  => '',
                'glitch_transparent'        => '',
                'glitch_effect_transparent' => ''
            ), $this->atts ) );

            if( ( $glitch_transparent == 'on' && $glitch_effect_transparent == 'none' ) ||
                ( $glitch_transparent == 'off' && $glitch_effect == 'none' ) ||
                !$this->image_id ) {

                    return false;

            }

            $glitch_effect = new UT_Glitch_Effect( array(
                'image_id'                  => $this->image_id,
                'glitch_effect'             => $glitch_effect,
                'permanent_glitch'          => $permanent_glitch,
                'image_desktop'             => $image,
                'accent_1'                  => $accent_1,
                'accent_2'                  => $accent_2,
                'accent_3'                  => $accent_3,
                'glitch_transparent'        => $glitch_transparent,
                'glitch_effect_transparent' => $glitch_effect_transparent,
	            'lozad'                     => 'on'
            ));

            return $glitch_effect->render();

        }

        function ut_create_shortcode( $atts, $content = NULL ) {

            // Assign Attributes
            $this->atts = $atts;

            /**
             * @var $caption_content
             * @var $caption_style
             *
             */

            extract( shortcode_atts( array (
                'image'               => '',
                'image_opacity'       => '100',
                'image_border_radius' => '',
                'size'                => 'thumbnail',
                'align'               => 'left',
                'align_tablet'        => 'inherit',
                'align_mobile'        => 'center',
				'hide_image_title'	  => 'yes',
                'custom_width'		  => '',
				'custom_height'		  => '',
				'custom_crop'		  => 'on',
				
                // Caption
                'hover'                     => '',
                'caption_content'           => 'caption',
                'custom_caption'            => '',
                'custom_caption_small'      => '',
                'caption_below'             => '',

                // Extra Settings
                'image_offset'              => 'no',
                'image_zoom'                => 'yes',

                // Caption and Hover Colors
                'caption_style'                => '',
                'caption_color'                => '',
                'caption_font_size'            => '',
                'caption_background'           => '',
                'caption_transform'            => '',
				'caption_font_weight'		   => '',	
                'caption_letter_spacing'       => '',
                'caption_line_height'          => '',
                'caption_below_font_weight'    => '',
                'caption_below_font_size'      => '',
                'caption_below_line_height'    => '',
                'caption_below_color'          => '',
                'caption_below_transform'      => '',
                'caption_below_letter_spacing' => '',

                // Rotation
                'rotation'              => '',
                'rotation_tablet'       => '0',
                'rotation_tablet_change'=> '',
                'rotation_mobile'       => '0',
                'rotation_mobile_change'=> '',

                // Animation
                'effect'              => '',
                'animate_once'        => 'yes',
                'animate_mobile'      => false,
                'animate_tablet'      => false,
                'delay'               => 'no',
                'delay_timer'         => '100',
                'animation_duration'  => '',

                // reveal effect
                'revealfx'              => 'off',
                'appear_offset'         => 'auto',
                'revealfx_color'        => get_option('ut_accentcolor'),
                'revealfx_direction'    => 'lr',
                'revealfx_delay'        => '0',
                'revealfx_duration'     => '750',

                // glitch
                'glitch_effect'         => 'none',
                'glitch_transparent'        => '',
                'glitch_effect_transparent' => '',

                // shadow
                'shadow'              => '',
                'shadow_color'        => '',
                'shadow_canvas_color' => '',

                // cursor
                'cursor_skin'         => 'inherit',

                // link
                'link_type'           => 'none',
				'lightbox_size'       => 'hd',
                'link'                => '',
                'lightbox_iframe'     => '',

                'class'               => '',
                'css'                 => ''

            ), $atts ) ); 

            // store image id
            $this->image_id = $image;

            /* get image */
            if( $size == 'custom' ) {

				$new_image = array();
				$thumbnail = wp_get_attachment_image_src( $image, $size );
				
				if( isset( $thumbnail[0] ) && strpos( $thumbnail[0], '.svg' ) !== false ) {
					
					$new_image[0] = $thumbnail[0];
					$new_image[1] = $custom_width;
					$new_image[2] = $custom_height;					
					
				} elseif( isset( $thumbnail[0] ) ) {
				
					if( $custom_crop == 'on' ) {

						$new_image[0] = ut_resize( $thumbnail[0], $custom_width, $custom_height, true, true, true );
						$new_image[1] = $custom_width;
						$new_image[2] = $custom_height;

					} else {

						$new_image = ut_resize( $thumbnail[0], $custom_width, $custom_height, true, false, true );

					}
				
				}
				
				// assign new thumb
				$image_array = $new_image;

			} else {
				
				$image_array = wp_get_attachment_image_src( $image, $size );							
								
				// check for SVG
				if( isset( $image_array[0] ) && strpos( $image_array[0], '.svg' ) !== false ) {

					if( $size == 'thumbnail' ) {

						$image_array[1] = get_option('thumbnail_size_w');
						$image_array[2] = get_option('thumbnail_size_h');	

					}

					if( $size == 'medium' ) {

						$image_array[1] = get_option('medium_size_w');
						$image_array[2] = get_option('medium_size_h');

					}

					if( $size == 'large' || $size == 'full' ) {

						$image_array[1] = get_option('large_size_w');
						$image_array[2] = get_option('large_size_h');					

					}
					

				}
				
				
			}
			
            /* attachment meta */
            $attachment_meta = get_post( $image );

	        $morphbox_title   = '';
	        $morphbox_caption = '';
	        $force_caption    = false;

            /* caption text */
            if( !empty( $attachment_meta->post_excerpt ) && $caption_content == 'caption' ) {

                $caption_text = '<h3>' . $attachment_meta->post_excerpt . '</h3>';

            } elseif( !empty( $attachment_meta->post_title ) && $caption_content == 'title' ) {

                $caption_text = '<h3>' . $attachment_meta->post_title . '</h3>';

            } elseif( $caption_content == 'custom' ) {

                if( !empty( $custom_caption ) && !empty( $custom_caption_small ) ) {

	                $caption_text = '<h3>' . $custom_caption . '<br /><small>' . $custom_caption_small . '</small></h3>';

	                // morphbox
	                $morphbox_title = $custom_caption;
	                $morphbox_caption = $custom_caption_small;

                } elseif( empty( $custom_caption ) && !empty( $custom_caption_small ) ) {

	                $caption_text = '<h3><small>' . $custom_caption_small . '</small></h3>';

	                // morphbox
	                $morphbox_caption = $custom_caption_small;

                } elseif( !empty( $custom_caption ) && empty( $custom_caption_small ) ) {

                    $caption_text = '<h3>' . $custom_caption . '</h3>';

	                // morphbox
	                $morphbox_title = $custom_caption;

                }

	            $force_caption = true;

            } else {

                if( $caption_style != '-style-2' ) {

                    $caption_text = '<h3 class="ut-image-gallery-empty-title">+</h3>';

                } else {

                    $caption_text = false;

                }

            }

            /* image alt */
            $alt = get_post_meta( $image, '_wp_attachment_image_alt', true ) ? get_post_meta( $image, '_wp_attachment_image_alt', true ) : '';
            
            if( empty( $alt ) && !empty( $attachment_meta->post_excerpt ) ) {
                
                $alt = esc_attr( $attachment_meta->post_excerpt );
                
            }

            /* image title */
			$image_title = get_the_title( $image );
            $image_title = 'title="' . esc_attr( $image_title ) . '"';
            
			if( $hide_image_title == 'yes' ) {
				$image_title = '';
			}

            if( empty( $image_array ) ) {
                
                $image_array   = array();
                $image_array[] = ut_img_asset_url( 'replace-normal.jpg' );
                $image_array[] = "800";
                $image_array[] = "500";
                
            }

            if( empty( $image_array[0] ) ) {

                $image_array   = array();
                $image_array[] = ut_img_asset_url( 'replace-normal.jpg' );
                $image_array[] = "800";
                $image_array[] = "500";

            }

            /* class array */
            $classes = array("ut-image-gallery-image");
            $animation_classes = array();            
            $image_classes = array();

            /* extra element class */
            $classes[] = $class;
            
            /* attributes array */
            $attributes = array();

            /* custom cursor */
	        if( $cursor_skin !== 'inherit' ) {
		        $attributes['data-cursor-skin'] = esc_attr( $cursor_skin );
	        }

	        /* transparent glitch */
	        if( $glitch_transparent == 'on' && $glitch_effect_transparent != 'none' ) {

		        $image_classes[] = 'ut-element-glitch-transparent';
		        $image_classes[] = 'ut-simple-glitch-text-permanent';
		        $image_classes[] = 'ut-simple-glitch-text-' . $glitch_effect_transparent . '-permanent';

	        }


            $link_attributes = '';

            /* fill animation classes */
            if( !empty( $effect ) && $effect != 'none' ) {
                
                $animation_classes[] = 'ut-animate-image';
                $animation_classes[] = 'animated';
                            
                if( !$animate_tablet ) {
                    $animation_classes[]  = 'ut-no-animation-tablet';
                }
                
                if( !$animate_mobile ) {
                    $animation_classes[]  = 'ut-no-animation-mobile';
                }
                
                if( $animate_once == 'infinite' ) {
                    $animation_classes[]  = 'infinite';
                }
                
                $attributes['data-effect'] = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );
                $attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;
                
                if( !empty( $animation_duration ) ) {
                    $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );    
                }    
                
            }
            
            /* attributes string */
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );

            // image glitch
            if( $glitch_effect != 'none' ) {
                $classes[] = 'ut-has-element-glitch';
            }

            // inline custom css 
            $id = uniqid("ut_am_");
            $this->unique_id = $wrap_id = uniqid("ut_am_wrap_");

            // reveal fx
            $reveal_id = uniqid("ut_reveal_");
            
            $css_style = '';
            
            if( $image_opacity ) {
                $css_style .= '#' . $id . ' { opacity: ' . ( $image_opacity / 100 ) . '; }';
            }
            
            if( $image_border_radius ) {

                $css_style .= '#' . esc_attr( $wrap_id ) . ' img,';
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-image-caption,';
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-box-shadow-container,';
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-box-shadow-container-inner,';
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-block-revealer-element-container,';
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-animated-image-zoom { -webkit-border-radius: ' . $image_border_radius . 'px; -moz-border-radius: ' . $image_border_radius . 'px; border-radius: ' . $image_border_radius . 'px; }';

            }
            
            if( $caption_color ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item-caption-title h3 { color: '. $caption_color . '; }';
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item-caption-title-style-2 h3 { color: '. $caption_color . '; }';
            }

            if( $caption_background && ut_is_gradient( $caption_background ) ) {
                
				$css_style .= ut_create_gradient_css( $caption_background, '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item.ut-animation-done:hover .ut-image-gallery-image-caption, #' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item.ut-element-is-animating:hover .ut-image-gallery-image-caption', false, 'background' );
				$css_style .= ut_create_gradient_css( $caption_background, '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item .ut-image-gallery-image-caption-style-2', false, 'background' );

            } elseif( $caption_background ) {
				
				$css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item.ut-animation-done:hover .ut-image-gallery-image-caption, #' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item.ut-element-is-animating:hover .ut-image-gallery-image-caption { background: ' . $caption_background . '; }';
				$css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item .ut-image-gallery-image-caption-style-2 { background: ' . $caption_background . '; }';

			}

            if( $caption_text && $caption_style == '-style-2' && $image_offset == 'no' ) {

                $css_style .= '@media (min-width: 768px) { #' . esc_attr( $wrap_id ) . ' .ut-animated-image-item a { padding-right: 40px; } }';
                $css_style .= '@media (min-width: 768px) { #' . esc_attr( $wrap_id ) . ' .ut-box-shadow-container { margin-right: 40px; } }';


                // $css_style .= '@media (min-width: 768px) { #' . esc_attr( $wrap_id ) . ' .ut-element-glitch-wrap { --offset: 40px; } }'; //@todo check offset

            }

            if( $caption_style == '-style-2' && $image_zoom == 'yes' ) {

                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-animated-image-zoom::after { background: url(' . esc_url( $image_array[0] ) . '); }';

            }

            if( $caption_transform ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item-caption-title' . $caption_style . ' h3 { text-transform: '. $caption_transform . '; }';
            }
            
			if( $caption_font_weight ){
				$css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item-caption-title' . $caption_style . ' h3 { font-weight: '. $caption_font_weight . '; }';
			}

			if( $caption_line_height ){
				$css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item-caption-title' . $caption_style . ' h3 { line-height: '. $caption_line_height . '; }';
			}
			
            if( isset( $caption_letter_spacing ) && $caption_letter_spacing != '' ) {
				
				// fallback letter spacing
				if( $caption_letter_spacing >= 1 || $caption_letter_spacing <= -1 ) {
					$caption_letter_spacing = $caption_letter_spacing / 100;	
				}
				
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item-caption-title' . $caption_style . ' h3 { letter-spacing: '. $caption_letter_spacing . 'em; }';
            }

            if( $caption_font_size ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-image-gallery-item-caption-title' . $caption_style . ' h3 { font-size: '. $caption_font_size . '; }';
            }

            if( $caption_below_color ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-gallery-slider-caption { color: '. $caption_below_color . '; }';
            }

            if( $caption_below_font_size ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-gallery-slider-caption { font-size: '. $caption_below_font_size . '; }';
            }

            if( $caption_below_line_height ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-gallery-slider-caption { line-height: '. $caption_below_line_height . '; }';
            }

            if( $caption_below_transform ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-gallery-slider-caption { text-transform: '. $caption_below_transform . '; }';
            }
            
            if( $caption_below_letter_spacing ) {
				
				// fallback letter spacing
				if( $caption_below_letter_spacing >= 1 || $caption_below_letter_spacing <= -1 ) {
					$caption_below_letter_spacing = $caption_below_letter_spacing / 100;	
				}				
				
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-gallery-slider-caption { letter-spacing: '. $caption_below_letter_spacing . 'em; }';
            }

            if( $caption_below_font_weight ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-gallery-slider-caption { font-weight: '. $caption_below_font_weight . '; }';
            }

	        if( !empty( $shadow_color ) ) {

		        // shadow color
		        $css_style .= '#' . $id . ' .ut-box-shadow-container-inner { color: ' . $shadow_color . '; }';

		        if( $shadow && $shadow != 'no' ) {

			        $css_style .= ut_box_shadow( $shadow, $shadow_color );

		        }

	        }

            if( $shadow_canvas_color ) {
                $css_style .= '#' . esc_attr( $wrap_id ) . ' .ut-box-shadow-container-inner { background: ' . $shadow_canvas_color . '; }';
            }

	        /* image align */
	        $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
	        $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

	        if( $align ) {
		        $css_style .= '#' . $wrap_id . ' { text-align: ' . $align . '; }';
	        }
	        if( $align_tablet ) {
		        $css_style .= '@media (min-width: 768px) and (max-width: 1024px) { #' . $wrap_id . ' { text-align: ' . $align_tablet . ' !important; } }';
	        }
	        if( $align_mobile ) {
		        $css_style .= '@media (max-width: 767px) { #' . $wrap_id . ' { text-align: ' . $align_mobile . ' !important; } }';
	        }

	        if( !empty( $rotation ) ) {
		        $css_style .= '#' . $wrap_id . ' { -webkit-transform: rotate(' . $rotation . 'deg); transform: rotate(' . $rotation . 'deg); }';
	        }

	        if( $rotation_tablet_change == 'on' ) {
		        $css_style .= '@media (min-width: 768px) and (max-width: 1024px) { #' . $wrap_id . '.ut-shortcode-video-wrap { -webkit-transform: rotate(' . $rotation_tablet . 'deg); transform: rotate(' . $rotation_tablet . 'deg); } }';
	        }

	        if( $rotation_mobile_change == 'on' ) {
		        $css_style .= '@media (max-width: 767px) { #' . $wrap_id . ' { -webkit-transform: rotate(' . $rotation_mobile . 'deg); transform: rotate(' . $rotation_mobile . 'deg); } }';
	        }

            /* start output */
            $output = '';

            $reveal_classes    = array();
            $reveal_attributes = array();

            if( $revealfx == 'on' ) {

                $this->reveal_id = $reveal_id;

                // extra classes
                $classes[] = 'ut-reveal-fx ut-element-with-block-reveal';
                $reveal_classes[] = 'ut-reveal-fx-element ut-block-reveal-hide';

                // attributes
                $reveal_attributes['data-reveal-bgcolor']   = esc_attr( $revealfx_color );
                $reveal_attributes['data-reveal-direction'] = esc_attr( $revealfx_direction );
                $reveal_attributes['data-reveal-duration']  = esc_attr( $revealfx_duration );
                $reveal_attributes['data-reveal-delay']     = esc_attr( $revealfx_delay );

            }

            /* reveal attributes string */
            $reveal_attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $reveal_attributes,
                array_keys( $reveal_attributes )
            ) );


            // shadow spacing
            $img_classes = array();
            $img_shadow_class = array('ut-box-shadow-container');

            if( $shadow && $shadow != 'no' ) {

                // $classes[] = $shadow . '-spacing';

                if( $shadow == 'yes' ) {

                    $img_shadow_class[] = 'gutter-shadow';

                } else {

                    $img_shadow_class[] = $shadow;

                }

            }
            // add css to output
            if( !empty( $css_style ) ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );
            }
            
            $output .= '<div id="' . $wrap_id . '" class="' . implode( ' ', $classes ) . '">';
            
                $output .= '<div id="' . esc_attr( $reveal_id ) . '" ' . $reveal_attributes . ' data-appear-top-offset="' . esc_attr( $appear_offset ) . '" class="ut-animated-image-item ut-image-gallery-item ut-animation-done ' . implode( ' ', $reveal_classes ) . '">';
            
                    if( $link_type == 'image' ) {

                        if( function_exists('ot_get_option') && ot_get_option( 'ut_lightgallery_type', 'lightgallery' ) == 'lightgallery' ) {

                            // lightgallery zoom image
                            if ($lightbox_size == 'hd') {

                                $lightgallery = wp_get_attachment_image_src($image, 'ut-lightbox');

                            } elseif ($lightbox_size == 'full') {

                                $lightgallery = wp_get_attachment_image_src($image, 'full');

                            } elseif ($lightbox_size == 'full_hd') {

                                $lightgallery = wp_get_attachment_image_src($image, 'full');
                                $lightgallery = ut_resize($lightgallery[0], 1920, 1080, false, false, false);

                            } elseif ($lightbox_size == 'wqhd') {

                                $lightgallery = wp_get_attachment_image_src($image, 'full');
                                $lightgallery = ut_resize($lightgallery[0], 2560, 1440, false, false, false);

                            } elseif ($lightbox_size == 'retina_4k') {

                                $lightgallery = wp_get_attachment_image_src($image, 'full');
                                $lightgallery = ut_resize($lightgallery[0], 4096, 2304, false, false, false);

                            } elseif ($lightbox_size == 'retina_5k') {

                                $lightgallery = wp_get_attachment_image_src($image, 'full');
                                $lightgallery = ut_resize($lightgallery[0], 5120, 2880, false, false, false);

                            }

                        } elseif( function_exists('ut_get_morphbox_fullscreen') ) {

                            $link_attributes = ut_get_morphbox_meta( $image, false, $morphbox_title, $morphbox_caption, $force_caption );
                            $lightgallery = ut_get_morphbox_fullscreen( $image, 'full' );

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

                        $mini = wp_get_attachment_image_src( $image, 'full' );

                        if( isset( $mini[0] ) ) {
	                        $mini = ut_resize( $mini[0], 200, 200, true, false, false );
                        }

                        // fallback image
                        if( empty( $mini ) ) {

                            $mini   = array();
                            $mini[] = ut_img_asset_url( 'replace-normal.jpg' );
                            $mini[] = "";
                            $mini[] = "";

                        }
						
                        /* set link */
                        $output .= '<a data-exthumbimage="' . esc_url( $mini[0] ) . '" href="' . esc_url( $lightgallery[0] ) . '" class="ut-vc-images-lightbox ut-wait-for-plugin" ' . $link_attributes . '>';

                    }

                    if( $link_type == 'custom' ) {

                        if( !empty( $link ) ) {

                            /* attract link settings */
                            $link = vc_build_link($link);

                            /* set link attributes */
                            $link['target'] = empty($link['target']) ? '_self' : $link['target'];
                            $link['url'] = empty($link['url']) ? '#' : $link['url'];
                            $rel = empty($link['rel']) ? '' : 'rel="' . $link['rel'] . '"';

                            $output .= '<a target="' . esc_attr($link['target']) . '" href="' . esc_url($link['url']) . '" ' . $rel . '>';

                        } else {

                            $output .= '<a class="ut-deactivated-link" href="#">';

                        }

                    }

			        if( $link_type == 'iframe' ) {

				        if( !empty( $lightbox_iframe ) ) {

					        $output .= '<a href="' . esc_url($lightbox_iframe ) . '" class="ut-vc-images-lightbox" data-iframe="true">';

				        } else {

					        $output .= '<a class="ut-deactivated-link" href="#">';

				        }

			        }
                    
                    if( $link_type == 'none' ) {
                        
                        $output .= '<a class="ut-deactivated-link" href="#">';
                        
                    }

                    if( $shadow && $shadow != 'no' ) {

                        $output .= '<div class="' . implode( ' ', $img_shadow_class ) . '"><div class="ut-box-shadow-container-inner"></div></div>';

                    }

			        if( $shadow ) {

				        $img_shadow_class[] = 'ut-box-shadow-lazy';

			        }

                    if( $image_zoom == 'yes' ) {

                        $output .= '<div class="ut-animated-image-zoom">';

                            $output .= $this->create_glitch_effect($image_array[0]);
                            $output .= '<img class="ut-lazy skip-lazy ut-animated-image ' . implode(" ", $image_classes ) . '" ' . $image_title . ' id="' . $id . '" src="' . $this->create_placeholder_svg( $image_array[1], $image_array[2] ) . '" data-src="' . esc_url( $image_array[0] ) . '" width="' . esc_attr( ( $image_array[1] > 1 ? $image_array[1] : '' ) ) . '" height="' . esc_attr( ( $image_array[2] > 1 ? $image_array[2] : '' ) ) . '" alt="' . esc_attr( $alt ) . '"/>';

                        $output .= '</div>';

                    } else {

                        $output .= $this->create_glitch_effect($image_array[0]);
						$output .= '<img class="ut-lazy skip-lazy ut-animated-image ' . implode(" ", $image_classes ) . '" ' . $image_title . ' id="' . $id . '" src="' . $this->create_placeholder_svg( $image_array[1], $image_array[2] ) . '" data-src="' . esc_url( $image_array[0] ) . '" width="' . esc_attr( ( $image_array[1] > 1 ? $image_array[1] : '' ) ) . '" height="' . esc_attr( ( $image_array[2] > 1 ? $image_array[2] : '' ) ) . '" alt="' . esc_attr( $alt ) . '"/>';

                    }

                    if( $hover == 'yes' ) {

                        if( $caption_text ) {

                            $output .= '<div data-image-caption class="ut-image-gallery-image-caption' . $caption_style . '">';

                                $output .= '<div class="ut-image-gallery-item-caption-title' . $caption_style . '">';

                                    $output .= $caption_text;

                                $output .= '</div>';

                            $output .= '</div>';

                        }

                    }
            
                    $output .= '</a>';

                    if( $caption_below == 'yes' && !empty( $attachment_meta->post_excerpt ) ) {

                        $output .= '<div class="ut-gallery-slider-caption">' . $attachment_meta->post_excerpt . '</div>';

                    }
            
                $output .= '</div>';
            
            $output .= '</div>';
                
            return '<div ' . $attributes . ' class="wpb_content_element ' . implode( ' ', $animation_classes ) . ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . ' clearfix">' . $output . '</div>'; 
        
        }
            
    }

}

new UT_Animated_Image;

if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_animated_image extends WPBakeryShortCode {
        
        function __construct( $settings ) {
            
            parent::__construct( $settings );
            $this->jsScripts();
            
        }
    
        public function jsScripts() {
            
            wp_register_script( 'zoom', vc_asset_url( 'lib/bower/zoom/jquery.zoom.min.js' ), array(), WPB_VC_VERSION );
            wp_register_script( 'vc_image_zoom', vc_asset_url( 'lib/vc_image_zoom/vc_image_zoom.min.js' ), array(
                'jquery',
                'zoom',
            ), WPB_VC_VERSION, true );
            
        }
    
        public function singleParamHtmlHolder( $param, $value ) {
            
            $output = '';
    
            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
            $type = isset( $param['type'] ) ? $param['type'] : '';
            $class = isset( $param['class'] ) ? $param['class'] : '';
            
            if ( 'attach_image' === $param['type'] && 'image' === $param_name ) {
                $output .= '<input type="hidden" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
                $element_icon = $this->settings( 'icon' );
                $img = wpb_getImageBySize( array(
                    'attach_id' => (int) preg_replace( '/[^\d]/', '', $value ),
                    'thumb_size' => 'thumbnail',
                ) );
                $this->setSettings( 'logo', ( $img ? $img['thumbnail'] : '<img width="150" height="150" src="' . vc_asset_url( 'vc/blank.gif' ) . '" class="attachment-thumbnail vc_general vc_element-icon"  data-name="' . $param_name . '" alt="" title="" style="display: none;" />' ) . '<span class="no_image_image vc_element-icon' . ( ! empty( $element_icon ) ? ' ' . $element_icon : '' ) . ( $img && ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '" /><a href="#" class="column_edit_trigger' . ( $img && ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '">' . __( 'Add image', 'js_composer' ) . '</a>' );
                $output .= $this->outputTitleTrue( $this->settings['name'] );
            } elseif ( ! empty( $param['holder'] ) ) {
                if ( 'input' === $param['holder'] ) {
                    $output .= '<' . $param['holder'] . ' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '">';
                } elseif ( in_array( $param['holder'], array( 'img', 'iframe' ) ) ) {
                    $output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="' . $value . '">';
                } elseif ( 'hidden' !== $param['holder'] ) {
                    $output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
                }
            }
    
            if ( ! empty( $param['admin_label'] ) && true === $param['admin_label'] ) {
                $output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . $param['heading'] . '</label>: ' . $value . '</span>';
            }
    
            return $output;
        }
    
        public function getImageSquareSize( $img_id, $img_size ) {
            if ( preg_match_all( '/(\d+)x(\d+)/', $img_size, $sizes ) ) {
                $exact_size = array(
                    'width' => isset( $sizes[1][0] ) ? $sizes[1][0] : '0',
                    'height' => isset( $sizes[2][0] ) ? $sizes[2][0] : '0',
                );
            } else {
                $image_downsize = image_downsize( $img_id, $img_size );
                $exact_size = array(
                    'width' => $image_downsize[1],
                    'height' => $image_downsize[2],
                );
            }
            $exact_size_int_w = (int) $exact_size['width'];
            $exact_size_int_h = (int) $exact_size['height'];
            if ( isset( $exact_size['width'] ) && $exact_size_int_w !== $exact_size_int_h ) {
                $img_size = $exact_size_int_w > $exact_size_int_h
                    ? $exact_size['height'] . 'x' . $exact_size['height']
                    : $exact_size['width'] . 'x' . $exact_size['width'];
            }
    
            return $img_size;
        }
    
        protected function outputTitle( $title ) {
            return '';
        }
    
        protected function outputTitleTrue( $title ) {
            return '<h4 class="wpb_element_title">' . $title . ' ' . $this->settings( 'logo' ) . '</h4>';
        }                
        
    }
    
}