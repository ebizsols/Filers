<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Image_Gallery' ) ) {
	
    class UT_Image_Gallery {
        
        private $shortcode;

        private $image_count;
        private $image_total_count;
        
        private $mobile_image_count;
        private $mobile_total_count;

        private $gallery_id;
        private $atts;

        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_image_gallery';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );

		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Image Gallery', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'A responsive and mobile friendly Image Gallery with tons of features and lightbox support.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'category'        => 'Media',
                    // 'icon'            => 'fa fa-image ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/image-gallery.png',
                    'class'           => 'ut-vc-icon-module ut-media-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'attach_images',
                            'heading'           => esc_html__( 'Gallery', 'ut_shortcodes' ),
                            'group'             => 'Gallery',
                            'param_name'        => 'gallery',
                            'admin_label'       => true
                        ),
                        array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Activate Masonry?', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'no, thanks!' , 'ut_shortcodes' )  => 'off',
			                    esc_html__( 'yes, please!' , 'ut_shortcodes' ) => 'on',
		                    )
	                    ),

	                    // default image sizes
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Thumbnail Size', 'ut_shortcodes' ),
		                    'description'       => esc_html__( '', 'ut_shortcodes' ),
		                    'param_name'        => 'thumbnail_size',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'Thumbnail (cropped)' , 'ut_shortcodes' ) => 'thumbnail',
			                    esc_html__( 'Medium (cropped)' , 'ut_shortcodes' )    => 'medium',
			                    esc_html__( 'Large (cropped)' , 'ut_shortcodes' )     => 'large',
			                    esc_html__( 'Original' , 'ut_shortcodes' )            => 'full',
			                    esc_html__( 'Custom Size' , 'ut_shortcodes' )         => 'custom'
		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry',
			                    'value'   => 'off',
		                    )
	                    ),
	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Custom Size Width', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Value in px. e.g. 800', 'ut_shortcodes' ),
		                    'param_name'        => 'thumbnail_custom_width',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Gallery',
		                    'dependency'        => array(
			                    'element' => 'thumbnail_size',
			                    'value'   => 'custom',
		                    )
	                    ),
	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Custom Size Height', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Value in px. e.g. 600', 'ut_shortcodes' ),
		                    'param_name'        => 'thumbnail_custom_height',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Gallery',
		                    'dependency'        => array(
			                    'element' => 'thumbnail_size',
			                    'value'   => 'custom',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Crop Images?', 'ut_shortcodes' ),
		                    'description'		=> __('What does Soft Crop mean? A soft crop will never cut off any of the image, it will scale the image down until it fits within the dimensions specified, maintaining its original aspect ratio. What does Hard Crop mean? The image will be scaled and then cropped to the exact dimensions you have specified. Depending on the aspect ratio of the image in relation to the crop size, it might happen that the image will be cut off.', 'ut_shortcodes'),
		                    'param_name'        => 'thumbnail_custom_crop',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'yes, please! (Hard Crop)' , 'ut_shortcodes' ) => 'on',
			                    esc_html__( 'no, thanks! (Soft Crop)' , 'ut_shortcodes' )  => 'off',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'thumbnail_size',
			                    'value'   => 'custom',
		                    )
	                    ),

	                    // masonry image sizes
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Thumbnail Size', 'ut_shortcodes' ),
		                    'description'       => esc_html__( '', 'ut_shortcodes' ),
		                    'param_name'        => 'thumbnail_size_masonry',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    sprintf( __( 'Default - %sx%s soft crop based on Settings > Media > Large size' , 'ut_shortcodes' ), get_option( 'large_size_w' ), get_option( 'large_size_h' ) )   => 'masonry',
			                    esc_html__( 'Original' , 'ut_shortcodes' )  => 'full',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry',
			                    'value'   => 'on',
		                    )
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Masonry Layout Mode', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_mode',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'masonry (default)' , 'ut_shortcodes' )  => 'masonry',
			                    esc_html__( 'fitRows' , 'ut_shortcodes' )  => 'fitRows',

		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry',
			                    'value'   => 'on',
		                    )
	                    ),
	                    array(
		                    'type'          => 'ut_info_box',
		                    'heading'       => esc_html__( 'Masonry Information', 'ut_shortcodes' ),
		                    'param_name'    => 'info_masonry',
		                    'group'         => 'Gallery',
		                    'info'          => esc_html__( 'It works by placing elements in optimal position based on available vertical space, sort of like a mason fitting stones in a wall.', 'ut_shortcodes' ),
		                    'dependency' => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'masonry',
		                    )
	                    ),
	                    array(
		                    'type'          => 'ut_info_box',
		                    'heading'       => esc_html__( 'Mode Information', 'ut_shortcodes' ),
		                    'param_name'    => 'info_fitRows',
		                    'group'         => 'Gallery',
		                    'info'          => esc_html__( 'Items are arranged into rows. Rows progress vertically. Similar to what you would expect from a layout that uses CSS floats. fitRows is ideal for items that have the same height.', 'ut_shortcodes' ),
		                    'dependency' => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'fitRows',
		                    )
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Row Height Desktop', 'ut_shortcodes' ),
		                    'param_name'        => 'fit_rows_height',
		                    'group'             => 'Gallery',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'value'             => array(
			                    esc_html__( 'small (300px)' , 'ut_shortcodes' )     => 'small',
			                    esc_html__( 'medium (450px)' , 'ut_shortcodes' )    => 'medium',
			                    esc_html__( 'large (600px)' , 'ut_shortcodes' )    => 'large',
			                    esc_html__( 'custom' , 'ut_shortcodes' )    => 'custom',

		                    ),
		                    'dependency'        => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'fitRows',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Row Height Tablet', 'ut_shortcodes' ),
		                    'param_name'        => 'fit_rows_height_tablet',
		                    'group'             => 'Gallery',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'value'             => array(
			                    esc_html__( 'small (300px)' , 'ut_shortcodes' )     => 'small',
			                    esc_html__( 'medium (450px)' , 'ut_shortcodes' )    => 'medium',
			                    esc_html__( 'large (600px)' , 'ut_shortcodes' )    => 'large',
			                    esc_html__( 'custom' , 'ut_shortcodes' )    => 'custom',

		                    ),
		                    'dependency'        => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'fitRows',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Row Height Mobile', 'ut_shortcodes' ),
		                    'param_name'        => 'fit_rows_height_mobile',
		                    'group'             => 'Gallery',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'value'             => array(
			                    esc_html__( 'small (300px)' , 'ut_shortcodes' )     => 'small',
			                    esc_html__( 'medium (450px)' , 'ut_shortcodes' )    => 'medium',
			                    esc_html__( 'large (600px)' , 'ut_shortcodes' )    => 'large',
			                    esc_html__( 'custom' , 'ut_shortcodes' )    => 'custom',

		                    ),
		                    'dependency'        => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'fitRows',
		                    )
	                    ),
	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Row Height Desktop', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'value in px, eg "600px", default: 600px', 'ut_shortcodes' ),
		                    'param_name'        => 'fit_rows_height_custom',
		                    'group'             => 'Gallery',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'dependency'        => array(
			                    'element'   => 'fit_rows_height',
			                    'value'     => 'custom',
		                    )
	                    ),
	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Row Height Tablet', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'value in px, eg "600px", default inherit from larger.', 'ut_shortcodes' ),
		                    'param_name'        => 'fit_rows_height_tablet_custom',
		                    'group'             => 'Gallery',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'dependency'        => array(
			                    'element'   => 'fit_rows_height_tablet',
			                    'value'     => 'custom',
		                    )
	                    ),
	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Row Height Mobile', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'value in px, eg "600px", default inherit from larger.', 'ut_shortcodes' ),
		                    'param_name'        => 'fit_rows_height_mobile_custom',
		                    'group'             => 'Gallery',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'dependency'        => array(
			                    'element'   => 'fit_rows_height_mobile',
			                    'value'     => 'custom',
		                    )
	                    ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Gallery Items per row.', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Select your desired amount of images per row.', 'ut_shortcodes' ),
                            'param_name'        => 'grid',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( '1 - Image' , 'ut_shortcodes' ) => '1',
                                esc_html__( '2 - Images' , 'ut_shortcodes' ) => '2',
                                esc_html__( '3 - Images' , 'ut_shortcodes' ) => '3',
                                esc_html__( '4 - Images' , 'ut_shortcodes' ) => '4',
                                esc_html__( '5 - Images' , 'ut_shortcodes' ) => '5',
                            ),
                            'dependency' => array(
	                            'element'   => 'masonry',
	                            'value'     => 'off',
                            )
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Gallery Items per row.', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Select your desired amount of images per row.', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_grid',
		                    'group'             => 'Gallery',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'value'             => array(
			                    esc_html__( '5 - Images' , 'ut_shortcodes' ) => '5',
			                    esc_html__( '4 - Images' , 'ut_shortcodes' ) => '4',
			                    esc_html__( '3 - Images' , 'ut_shortcodes' ) => '3',
			                    esc_html__( '2 - Images' , 'ut_shortcodes' ) => '2',
                                esc_html__( '1 - Image' , 'ut_shortcodes' )  => '1',

		                    ),
		                    'dependency' => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'masonry',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Tablet Gallery Items per row.', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Select your desired amount of images per row showing on tablets.', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_grid_tablet',
		                    'edit_field_class'  => 'vc_col-sm-4 ut-dependent-hidden-style',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( '3 - Images' , 'ut_shortcodes' ) => '3',
			                    esc_html__( '5 - Images' , 'ut_shortcodes' ) => '5',
			                    esc_html__( '4 - Images' , 'ut_shortcodes' ) => '4',
                                esc_html__( '2 - Images' , 'ut_shortcodes' ) => '2',
			                    esc_html__( '1 - Image' , 'ut_shortcodes' )  => '1',
			                    esc_html__( 'inherit from larger' , 'ut_shortcodes' ) => 'inherit',
		                    ),
		                    'dependency' => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'masonry',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Mobile Gallery Items per row.', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Select your desired amount of images per row showing on mobiles.', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_grid_mobile',
		                    'edit_field_class'  => 'vc_col-sm-4 ut-dependent-hidden-style',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( '2 - Images' , 'ut_shortcodes' ) => '2',
                                esc_html__( '1 - Image' , 'ut_shortcodes' ) => '1',
			                    esc_html__( '3 - Images' , 'ut_shortcodes' ) => '3',
			                    esc_html__( '4 - Images' , 'ut_shortcodes' ) => '4',
			                    esc_html__( '5 - Images' , 'ut_shortcodes' ) => '5',
			                    esc_html__( 'inherit from larger' , 'ut_shortcodes' ) => 'inherit',
		                    ),
		                    'dependency' => array(
			                    'element'   => 'masonry_mode',
			                    'value'     => 'masonry',
		                    )
	                    ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Adjust last row?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'This Option will adjust the remaining items inside the last row to a higher grid if necessary.', 'ut_shortcodes' ),
                            'param_name'        => 'adjust_row',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            ),
                            'dependency'        => array(
	                            'element' => 'masonry',
	                            'value'   => 'off',
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Gallery Items Gap.', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Select gap between gallery images.', 'ut_shortcodes' ),
                            'param_name'        => 'gap',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( '0px'  , 'ut_shortcodes' ) => '0',
                                esc_html__( '1px'  , 'ut_shortcodes' ) => '1',
                                esc_html__( '5px'  , 'ut_shortcodes' ) => '5',
                                esc_html__( '10px' , 'ut_shortcodes' ) => '10',
                                esc_html__( '15px' , 'ut_shortcodes' ) => '15',
                                esc_html__( '20px' , 'ut_shortcodes' ) => '20',
                                esc_html__( '25px' , 'ut_shortcodes' ) => '25',
                                esc_html__( '30px' , 'ut_shortcodes' ) => '30',
                                esc_html__( '35px' , 'ut_shortcodes' ) => '35',
                                esc_html__( '40px' , 'ut_shortcodes' ) => '40',
                            )
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Gallery Items Tablet Gap.', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Select gap between gallery images.', 'ut_shortcodes' ),
		                    'param_name'        => 'gap_tablet',
		                    'edit_field_class'  => 'vc_col-sm-4 ut-dependent-hidden-style',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( '0px'  , 'ut_shortcodes' ) => '0',
			                    esc_html__( '1px'  , 'ut_shortcodes' ) => '1',
			                    esc_html__( '5px'  , 'ut_shortcodes' ) => '5',
			                    esc_html__( '10px' , 'ut_shortcodes' ) => '10',
			                    esc_html__( '15px' , 'ut_shortcodes' ) => '15',
			                    esc_html__( '20px' , 'ut_shortcodes' ) => '20',
			                    esc_html__( '25px' , 'ut_shortcodes' ) => '25',
			                    esc_html__( '30px' , 'ut_shortcodes' ) => '30',
			                    esc_html__( '35px' , 'ut_shortcodes' ) => '35',
			                    esc_html__( '40px' , 'ut_shortcodes' ) => '40',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry',
			                    'value'   => 'on',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Gallery Items Mobile Gap.', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Select gap between gallery images.', 'ut_shortcodes' ),
		                    'param_name'        => 'gap_mobile',
		                    'edit_field_class'  => 'vc_col-sm-4 ut-dependent-hidden-style',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( '1px'  , 'ut_shortcodes' ) => '1',
			                    esc_html__( '5px'  , 'ut_shortcodes' ) => '5',
			                    esc_html__( '10px' , 'ut_shortcodes' ) => '10',
			                    esc_html__( '15px' , 'ut_shortcodes' ) => '15',
			                    esc_html__( '20px' , 'ut_shortcodes' ) => '20',
			                    esc_html__( '25px' , 'ut_shortcodes' ) => '25',
			                    esc_html__( '30px' , 'ut_shortcodes' ) => '30',
			                    esc_html__( '35px' , 'ut_shortcodes' ) => '35',
			                    esc_html__( '40px' , 'ut_shortcodes' ) => '40',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry',
			                    'value'   => 'on',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Deactivate alt tag?', 'ut_shortcodes' ),
		                    'param_name'        => 'alt',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'yes, please!' , 'ut_shortcodes' ) => 'on',
			                    esc_html__( 'no, thanks!' , 'ut_shortcodes' )  => 'off',
		                    )
	                    ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Link to Image to Project URL?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'When editing your image you are able to enter an additional Project URL. When activating this option, the user gets forwarded to this URL on click.', 'ut_shortcodes' ),
                            'param_name'        => 'external_link',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Project URL Target', 'ut_shortcodes' ),
                            'param_name'        => 'external_link_target',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( 'blank', 'ut_shortcodes' ) => 'blank',
                                esc_html__( 'self', 'ut_shortcodes' ) => 'self',
                            ),
                            'dependency'        => array(
                                'element' => 'external_link',
                                'value'   => 'yes',
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Lightbox?', 'ut_shortcodes' ),
                            'param_name'        => 'lightbox',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                            ),
                            'dependency'        => array(
                                'element' => 'external_link',
                                'value'   => 'no',
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Image Size in Lightbox', 'ut_shortcodes' ),
                            'param_name'        => 'lightbox_size',
                            'description'       => esc_html__( 'Only applies to "Lightgallery" Lightbox. What does Soft Crop mean? A soft crop will never cut off any of the image, it will scale the image down until it fits within the dimensions specified, maintaining its original aspect ratio. Also keep in mind, that using sizes these sizes except "HD Ready" and "Full" will force an image re-calculation the first time the setting is used. Means if your max_execution time is low, you might have to reload your website a few times until your server was able to process all images.', 'ut_shortcodes' ),
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( 'HD Ready (1280x720 soft cropped)', 'ut_shortcodes' ) => 'hd',
                                esc_html__( 'Full HD (1920x1280 soft cropped)', 'ut_shortcodes' ) => 'full_hd',
                                esc_html__( 'WQHD (2560x1440 soft cropped)', 'ut_shortcodes' ) => 'wqhd',
                                esc_html__( 'Retina 4k (4096x2304 soft cropped)', 'ut_shortcodes' ) => 'retina_4k',
                                esc_html__( 'Retina 5k (5120x2880 soft cropped)', 'ut_shortcodes' ) => 'retina_5k',
                                esc_html__( 'Original (Full Size no cropping)', 'ut_shortcodes' ) => 'full',
                            ),
                            'dependency'        => array(
                                'element' => 'lightbox',
                                'value'   => 'yes',
                            )
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Display as group in Lightbox?', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Display images of this gallery as a group inside the lightbox or together with other galleries as long as the other galleries use the same option.', 'ut_shortcodes' ),
		                    'param_name'        => 'lightbox_group',
		                    'group'             => 'Gallery',
		                    'value'             => array(
			                    esc_html__( 'display as group', 'ut_shortcodes' ) => 'group',
			                    esc_html__( 'display together with other galleries', 'ut_shortcodes' ) => 'global',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'lightbox',
			                    'value'   => 'yes',
		                    )
	                    ),
                        array(
                            'type' => 'range_slider',
                            'heading' => esc_html__( 'Overall Image Opacity', 'ut_shortcodes' ),
                            'param_name' => 'image_opacity',
                            'group' => 'Gallery',
                            'value' => array(
                                'default' => '100',
                                'min' => '0',
                                'max' => '100',
                                'step' => '1',
                                'unit'=> '%'
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Overall Image Border Radius', 'ut_shortcodes' ),
                            'param_name'        => 'image_border_radius',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '0',
                                'max'       => '200',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'group'             => 'Gallery',
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate LazyLoad?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Speed up page loading times and decrease traffic to your users by only loading the images in view. We recommend activating an animation effect for a nicer user experience.', 'ut_shortcodes' ),
                            'param_name'        => 'lazy',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false',
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Image Loader?', 'ut_shortcodes' ),
                            'description'       => esc_html__( '', 'ut_shortcodes' ),
                            'param_name'        => 'loader',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Gallery',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
                            ),
                            'dependency'        => array(
                                'element' => 'lazy',
                                'value'   => 'true',
                            )
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Image Loader Color', 'ut_shortcodes' ),
                            'param_name'        => 'loader_color',
                            'group'             => 'Gallery',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'loader',
                                'value'   => 'true',
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Image Shadow?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Note: Gallery Gap 0/1/5 and 10 do not support Shadows. Image shadows require additional space. This required space reduces the image display size.', 'ut_shortcodes' ),
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

                        // Caption Settings
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Hover Caption?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'The hover caption contains the image caption or a "plus sign".', 'ut_shortcodes' ),
                            'param_name'        => 'caption',
                            'group'             => 'Caption Settings',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                            ),

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
                                'element' => 'caption',
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
	                    /*array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Caption Description', 'ut-core' ),
		                    'description'       => esc_html__( '(optional) automatically display with a small font right below the regular custom text.' , 'ut-core' ),
		                    'param_name'        => 'custom_caption_small',
		                    'group'             => 'Caption Settings',
		                    'dependency'        => array(
			                    'element' => 'caption_content',
			                    'value'   => 'custom',
		                    )
	                    ),*/
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
                                'element' => 'caption',
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
                                'element' => 'caption',
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
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),
                            'dependency'        => array(
                                'element' => 'caption',
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
                                'element' => 'caption',
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
                                'element' => 'caption',
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
                                'element' => 'caption',
                                'value'   => 'yes',
                            )
                        ),

                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Caption Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'caption_background',
                            'group'             => 'Caption Settings',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'caption',
                                'value'   => 'yes',
                            )
                        ),

                        // default gallery caption
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Image Caption Below?', 'ut_shortcodes' ),
                            'param_name'        => 'caption_below',
                            'group'             => 'Caption Settings',
                            'value' => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
                            ),
                            'dependency'        => array(
	                            'element' => 'masonry',
	                            'value'   => 'off',
                            )
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
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
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

                        // masonry gallery caption
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Show Image Caption Below?', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_caption_below',
		                    'group'             => 'Caption Settings',
		                    'value' => array(
			                    esc_html__( 'no', 'ut_shortcodes' ) => 'no',
			                    esc_html__( 'yes', 'ut_shortcodes' ) => 'yes',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry_mode',
			                    'value'   => 'masonry',
		                    )
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Caption Below Text Transform', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_caption_below_transform',
		                    'group'             => 'Caption Settings',
		                    'value'             => array(
			                    esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
			                    esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
			                    esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
			                    esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry_caption_below',
			                    'value'   => 'yes',
		                    )
	                    ),

	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Caption Below Font Weight', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_caption_below_font_weight',
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
			                    'element' => 'masonry_caption_below',
			                    'value'   => 'yes',
		                    )
	                    ),

	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Caption Below Letter Spacing', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_caption_below_letter_spacing',
		                    'group'             => 'Caption Settings',
		                    'value'             => array(
			                    'default'   => '0',
			                    'min'       => '-0.2',
			                    'max'       => '0.2',
			                    'step'      => '0.01',
			                    'unit'      => 'em'
		                    ),
		                    'dependency'        => array(
			                    'element' => 'masonry_caption_below',
			                    'value'   => 'yes',
		                    )

	                    ),

	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Caption Below Font Size', 'ut_shortcodes' ),
		                    'description'       => esc_html__( '(optional) value in px or em, eg "20px"' , 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_caption_below_font_size',
		                    'group'             => 'Caption Settings',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'dependency'        => array(
			                    'element' => 'masonry_caption_below',
			                    'value'   => 'yes',
		                    )
	                    ),

	                    array(
		                    'type'              => 'textfield',
		                    'heading'           => esc_html__( 'Caption Below Line height', 'ut_shortcodes' ),
		                    'description'       => esc_html__( '(optional)' , 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_caption_below_line_height',
		                    'group'             => 'Caption Settings',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'dependency'        => array(
			                    'element' => 'masonry_caption_below',
			                    'value'   => 'yes',
		                    )
	                    ),

	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Caption Below Text Color', 'ut_shortcodes' ),
		                    'param_name'        => 'masonry_caption_below_color',
		                    'group'             => 'Caption Settings',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'dependency'        => array(
			                    'element' => 'masonry_caption_below',
			                    'value'   => 'yes',
		                    )
	                    ),


                        // Image Animation
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Images?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Animate each element inside your gallery with an awesome animation effect.', 'ut_shortcodes' ),
                            'param_name'        => 'animate',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-12',
                            'value'             => array(
                                esc_html__( 'yes'  , 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no', 'ut_shortcodes' ) => 'false'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Animate Images on Tablet?', 'ut_shortcodes' ),
                            'param_name'        => 'animate_tablet',
                            'group'             => 'Animation',
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
                            'heading'           => esc_html__( 'Animate Images on Mobile?', 'ut_shortcodes' ),
                            'param_name'        => 'animate_mobile',
                            'group'             => 'Animation',
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
                            'heading'           => esc_html__( 'Set delay until Gallery Animation starts?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'This timer allows you to delay the entire animation process of the gallery.', 'ut_shortcodes' ),
                            'param_name'        => 'global_delay_animation',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
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
                            'description'       => esc_html__( 'Time in milliseconds until the gallery animation should start. e.g. 200', 'ut_shortcodes' ),
                            'param_name'        => 'global_delay_timer',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'global_delay_animation',
                                'value'   => 'true',
                            )
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
                            ),
                            'dependency'        => array(
                                'element' => 'animate',
                                'value'   => 'true',
                            )
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Delay Animation?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Animate Images inside the Gallery one by one.', 'ut_shortcodes' ),
                            'param_name'        => 'delay_animation',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
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
                            'description'       => esc_html__( 'Time in milliseconds until the next image appears. e.g. 200', 'ut_shortcodes' ),
                            'param_name'        => 'delay_timer',
                            'group'             => 'Animation',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'dependency'        => array(
                                'element' => 'delay_animation',
                                'value'   => 'true',
                            )
                        ),

                        array(
                            'type'              => 'animation_style',
                            'heading'           => __( 'Animation Effect', 'ut_shortcodes' ),
                            'description'       => __( 'Select initial loading animation for images.', 'ut_shortcodes' ),
                            'group'             => 'Animation',
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
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'Gallery'
                        ),

                        /* custom cursor */
                        array(
                            'type' => 'dropdown',
                            'heading' => __( 'Custom Cursor Skin', 'ut_shortcodes' ),
                            'description' => __( 'Only applies when custom cursor is active. Check Theme Options > Advanced > Custom Cursor.', 'ut_shortcodes' ),
                            'param_name' => 'cursor_skin',
                            'value' => _vc_get_cursor_skins(true),
                            'group' => 'Custom Cursor',

                        ),

                        /* css editor */
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),




                    )

                )

            ); /* end mapping */



        }

        function get_row_height( $row_height, $rows_height_custom ) {

	        $rows_height_custom = !empty( $rows_height_custom ) ? $rows_height_custom : '300';

	        $_row_height = array(
		        'small'  => '300',
		        'medium' => '450',
		        'large'  => '600',
		        'custom' => $rows_height_custom
	        );

	        return isset( $_row_height[$row_height] ) ? $_row_height[$row_height] : $rows_height_custom;

        }

        function ut_create_inline_css() {

            extract( shortcode_atts( array (
                'animate'                       => 'true',
                'effect'                        => '',
                'loader_color'                  => '',
                'caption_color'                 => '',
                'caption_font_size'             => '',
                'caption_line_height'           => '',
                'caption_background'            => '',
                'caption_transform'             => '',
				'caption_font_weight'			=> '',
                'caption_letter_spacing'        => '',
                'caption_below_font_weight'     => '',
                'caption_below_font_size'       => '',
                'caption_below_line_height'     => '',
                'caption_below_color'           => '',
                'caption_below_transform'       => '',
                'caption_below_letter_spacing'  => '',

                'masonry_caption_below'                 => 'no',
                'masonry_caption_below_font_weight'     => '',
                'masonry_caption_below_font_size'       => '',
                'masonry_caption_below_line_height'     => '',
                'masonry_caption_below_color'           => '',
                'masonry_caption_below_transform'       => '',
                'masonry_caption_below_letter_spacing'  => '',

                'shadow'                        => '',
                'shadow_canvas_color'           => '',
                'image_border_radius'           => '',
				'image_opacity'					=> '',

	            // masonry
	            'masonry'                       => 'off',
	            'masonry_mode'                  => 'masonry',
                'gap'                           => '0',
	            'gap_tablet'                    => 'inherit',
                'gap_mobile'                    => 'inherit',
	            'grid'                          => '1',
	            'masonry_grid'                  => '5',
	            'masonry_grid_tablet'           => '3',
	            'masonry_grid_mobile'           => '2',
	            'fit_rows_height'               => 'small',
                'fit_rows_height_tablet'        => 'small',
                'fit_rows_height_mobile'        => 'small',
                'fit_rows_height_custom'        => '600',
                'fit_rows_height_tablet_custom' => '',
                'fit_rows_height_mobile_custom' => '',

            ), $this->atts ) );

            if( $masonry == 'on' && $masonry_mode == 'masonry' && $masonry_caption_below == 'yes' ) {

	            $caption_below_font_weight = $masonry_caption_below_font_weight;
	            $caption_below_font_size = $masonry_caption_below_font_size;
	            $caption_below_line_height = $masonry_caption_below_line_height;
	            $caption_below_color = $masonry_caption_below_color;
	            $caption_below_transform = $masonry_caption_below_transform;
	            $caption_below_letter_spacing = $masonry_caption_below_letter_spacing;

            }

            $css = '<style type="text/css">';

                $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-lazy:not(.ut-image-loaded) + .ut-video-module-loading { display:block; }';
                $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-lazy.ut-image-loaded + .ut-video-module-loading { display:none; }';

                if( $animate == 'true' && $effect ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-animate-gallery-element { opacity: 0; }';
                }

				if( $image_opacity ) {
					$css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-image img { opacity: ' . ( $image_opacity / 100 ) . '; }';
			    }

                if( $caption_color ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item-caption-title h3 { color: '. $caption_color . '; }';
                }

				if( $caption_background && ut_is_gradient( $caption_background ) ) {

					$css .= ut_create_gradient_css( $caption_background, '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item.ut-animation-done:hover .ut-image-gallery-image-caption, #' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item.ut-element-is-animating:hover .ut-image-gallery-image-caption', false, 'background' );

				} elseif( $caption_background ) {

					$css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item.ut-animation-done:hover .ut-image-gallery-image-caption, #' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item.ut-element-is-animating:hover .ut-image-gallery-image-caption { background: ' . $caption_background . '; }';

				}

                if( $caption_transform ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item-caption-title h3 { text-transform: '. $caption_transform . '; }';
                }

				if( $caption_font_weight ) {
					$css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item-caption-title h3 { font-weight: '. $caption_font_weight . '; }';
				}

                if( isset( $caption_letter_spacing ) && $caption_letter_spacing != '' ) {

					// fallback letter spacing
					if( $caption_letter_spacing >= 1 || $caption_letter_spacing <= -1 ) {
						$caption_letter_spacing = $caption_letter_spacing / 100;
					}

                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item-caption-title h3 { letter-spacing: '. $caption_letter_spacing . 'em; }';
                }

                if( $caption_font_size ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item-caption-title h3 { font-size: '. $caption_font_size . '; }';
                }

                if( $caption_line_height ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-item-caption-title h3 { line-height: '. $caption_line_height . '; }';
                }

                if( $caption_below_color ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-gallery-slider-caption { color: '. $caption_below_color . '; }';
                }

                if( $caption_below_font_size ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-gallery-slider-caption { font-size: '. $caption_below_font_size . '; }';
                }

                if( $caption_below_line_height ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-gallery-slider-caption { line-height: '. $caption_below_line_height . '; }';
                }


                if( $caption_below_transform ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-gallery-slider-caption { text-transform: '. $caption_below_transform . '; }';
                }

                if( $caption_below_font_weight ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-gallery-slider-caption { font-weight: '. $caption_below_font_weight . '; }';
                }

                if( $caption_below_letter_spacing ) {

					// fallback letter spacing
					if( $caption_below_letter_spacing >= 1 || $caption_below_letter_spacing <= -1 ) {
						$caption_below_letter_spacing = $caption_below_letter_spacing / 100;
					}

					$css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-gallery-slider-caption { letter-spacing: '. $caption_below_letter_spacing . 'em; }';
                }

                if( $shadow ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-gallery-slider-caption { margin-top: 10px; }';
                }

                if( $shadow_canvas_color ) {
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-box-shadow-container-inner { background: ' . $shadow_canvas_color . '; }';
                }

                if( $loader_color ) {

                    if( ut_is_hex( $loader_color ) ) {

	                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-loader-inner { border-top-color: rgba('. ut_hex_to_rgba( $loader_color, '0.65' ) . '); border-left-color: rgba('. ut_hex_to_rgba( $loader_color, '0.65' ) . '); }';
	                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-loader-inner { border-bottom-color: rgba('. ut_hex_to_rgba( $loader_color, '0.15' ) . '); border-right-color: rgba('. ut_hex_to_rgba( $loader_color, '0.15' ) . '); }';

                    } else {

	                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-loader-inner { border-top-color: '. ut_rgb_to_rgba( $loader_color ) . '; border-left-color: '. ut_rgb_to_rgba( $loader_color ) . '; }';
	                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-loader-inner { border-bottom-color: '. ut_rgb_to_rgba( $loader_color, '0.15' ) . '; border-right-color: '. ut_rgb_to_rgba( $loader_color, '0.15' ) . '; }';

                    }

                }

                if( $image_border_radius ) {

                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-image img { -webkit-border-radius: ' . $image_border_radius . 'px; -moz-border-radius: ' . $image_border_radius . 'px; border-radius: ' . $image_border_radius . 'px; }';
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-image .ut-image-gallery-image-caption { -webkit-border-radius: ' . $image_border_radius . 'px; -moz-border-radius: ' . $image_border_radius . 'px; border-radius: ' . $image_border_radius . 'px; }';
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-image .ut-box-shadow-container { -webkit-border-radius: ' . $image_border_radius . 'px; -moz-border-radius: ' . $image_border_radius . 'px; border-radius: ' . $image_border_radius . 'px; }';
                    $css .= '#' . esc_attr( $this->gallery_id ) . ' .ut-image-gallery-image .ut-box-shadow-container-inner { -webkit-border-radius: ' . $image_border_radius . 'px; -moz-border-radius: ' . $image_border_radius . 'px; border-radius: ' . $image_border_radius . 'px; }';

                }

                if( $masonry == 'on' && $masonry_mode == 'masonry' ) {

	                if( $masonry == 'on' ) {
		                $grid = $masonry_grid;
	                }

	                // grid size for tablet and mobile
	                $masonry_grid_tablet = $masonry_grid_tablet == 'inherit' ? $grid : $masonry_grid_tablet;
	                $masonry_grid_mobile = $masonry_grid_mobile == 'inherit' ? $masonry_grid_tablet : $masonry_grid_mobile;

	                // grid gap for tablet and mobile
	                $gap_tablet = $gap_tablet == 'inherit' ? $gap : $gap_tablet;
	                $gap_mobile = $gap_mobile == 'inherit' ? $gap_tablet : $gap_mobile;

	                ob_start(); ?>

                    #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-item-wrap {
                        width: calc(<?php echo (100 / $grid ); ?>% - <?php echo ut_add_px_value($gap ); ?>);
                        margin-bottom: <?php echo ut_add_px_value($gap ); ?>;
                    }

                    #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-item,
                    #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-item-wrap {
                        padding: 0;

                    }

                    #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-sizer {
                        width: calc(<?php echo (100 / $grid ); ?>% - <?php echo ut_add_px_value($gap ); ?>);
                    }

                    @media (min-width: 768px) and (max-width: 1024px) {

                        #<?php echo esc_attr( $this->gallery_id ); ?> {
                            margin-right: -<?php echo ut_add_px_value($gap_tablet ); ?>;
                        }

                        #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-item-wrap {
                            width: calc(<?php echo (100 / $masonry_grid_tablet ); ?>% - <?php echo ut_add_px_value($gap_tablet ); ?>);
                            margin-bottom: <?php echo ut_add_px_value($gap_tablet ); ?>;
                        }

                        #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-sizer {
                            width: calc(<?php echo (100 / $masonry_grid_tablet ); ?>% - <?php echo ut_add_px_value($gap_tablet ); ?>);
                        }

                    }

                    @media (max-width: 767px) {

                        #<?php echo esc_attr( $this->gallery_id ); ?> {
                            margin-right: -<?php echo ut_add_px_value($gap_mobile ); ?>;
                        }

                        #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-item-wrap {
                            width: calc(<?php echo (100 / $masonry_grid_mobile ); ?>% - <?php echo ut_add_px_value($gap_mobile ); ?>);
                            margin-bottom: <?php echo ut_add_px_value($gap_mobile ); ?>;
                        }

                        #<?php echo esc_attr( $this->gallery_id ); ?> .ut-image-gallery-sizer {
                            width: calc(<?php echo (100 / $masonry_grid_mobile ); ?>% - <?php echo ut_add_px_value($gap_mobile ); ?>);
                        }

                    }

	                <?php

	                $css .= ob_get_clean();

                }

		        if( $masonry == 'on' && $masonry_mode == 'fitRows' ) {

			        $fit_rows_height        = $this->get_row_height( $fit_rows_height, $fit_rows_height_custom );
			        $fit_rows_height_tablet = $this->get_row_height( $fit_rows_height_tablet, $fit_rows_height_tablet_custom );
			        $fit_rows_height_mobile = $this->get_row_height( $fit_rows_height_mobile, $fit_rows_height_mobile_custom );

			        // grid gap for tablet and mobile
			        $gap_tablet = $gap_tablet == 'inherit' ? $gap : $gap_tablet;
			        $gap_mobile = $gap_mobile == 'inherit' ? $gap_tablet : $gap_mobile;

		        	ob_start(); ?>

					#<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows {
			            margin-left: -<?php echo $gap / 2; ?>px;
			            margin-right: -<?php echo $gap / 2; ?>px;
			        }

			        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-item,
			        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-image {
			            height: <?php echo ut_add_px_value($fit_rows_height ); ?>;
			        }

			        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-item {
			            margin: <?php echo $gap / 2; ?>px;
			            padding: 0;
			        }

			        @media (min-width: 768px) and (max-width: 1024px) {

                        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows {
                            margin-left: -<?php echo $gap_tablet / 2; ?>px;
                            margin-right: -<?php echo $gap_tablet / 2; ?>px;
                        }

				        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-item,
				        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-image {
				            height: <?php echo ut_add_px_value($fit_rows_height_tablet ); ?>;
				        }

                        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-item {
                            margin: <?php echo $gap_tablet / 2; ?>px;
                        }

			        }

			        @media (max-width: 767px) {

                        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows {
                            margin-left: -<?php echo $gap_mobile / 2; ?>px;
                            margin-right: -<?php echo $gap_mobile / 2; ?>px;
                        }

				        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-item,
				        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-image {
				            height: <?php echo ut_add_px_value($fit_rows_height_mobile ); ?>;
				        }

                        #<?php echo esc_attr( $this->gallery_id ); ?>.ut-image-gallery-fitRows .ut-image-gallery-item {
                            margin: <?php echo $gap_mobile / 2; ?>px;
                        }

			        }

		        	<?php

			        $css .= ob_get_clean();

		        }

           $css .= '</style>';

           return $css;

        }

	    function gallery_settings_json() {

		    /**
		     * @var $animate
		     * @var $effect
		     * @var $lazy
		     * @var $delay_animation
		     * @var $global_delay_animation
		     * @var $delay_timer
		     * @var $global_delay_timer
		     */

		    extract( shortcode_atts( array (
			    'animate'                => 'true',
			    'effect'                 => '',
			    'lazy'                   => 'true',
			    'delay_animation'        => 'false',
			    'global_delay_animation' => 'false',
			    'delay_timer'            => 100,
			    'global_delay_timer'     => 100,
		    ), $this->atts ) );

		    $json = array(
			    'animate'                   => filter_var( $animate, FILTER_VALIDATE_BOOLEAN ),
			    'effect'                    => $effect,
			    'lazy'                      => filter_var( $lazy, FILTER_VALIDATE_BOOLEAN ),
			    'delay_animation'           => filter_var( $delay_animation, FILTER_VALIDATE_BOOLEAN ),
			    'global_delay_animation'    => filter_var( $global_delay_animation, FILTER_VALIDATE_BOOLEAN ),
			    'delay_timer'               => $delay_timer,
			    'global_delay_timer'        => $global_delay_timer,
		    );

		    return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );


        }


        function create_placeholder_svg( $width , $height ){

            // fallback values
            $width = empty( $width ) ? '800' : $width;
            $height = empty( $height ) ? '600' : $height;

            return 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\' viewBox%3D\'0 0 ' . esc_attr( $width ) . ' ' . esc_attr( $height ) . '\'%2F%3E';

        }


        function ut_create_shortcode( $atts, $content = NULL ) {

            $this->atts = $atts;

            extract( shortcode_atts( array (
                'gallery'            		=> '',
                'thumbnail_size'     		=> 'thumbnail',
                'thumbnail_size_masonry'    => 'masonry',
				'thumbnail_custom_width'	=> '',
				'thumbnail_custom_height'	=> '',
				'thumbnail_custom_crop'		=> 'on',
				'image_opacity'				=> '',
                'external_link'      		=> '',
				'external_link_target'		=> 'blank',
                'lightbox'           		=> 'yes',
                'lightbox_size'      		=> 'hd',
                'lightbox_group'      		=> 'group',
                'alt'                		=> 'on',
                'caption'            		=> 'no',
                'caption_content'    		=> 'caption',
                'custom_caption'     		=> '',
                //'custom_caption_small'      => '',
                'caption_color'      		=> '',
                'caption_background' 		=> '',
                'caption_transform'  		=> '',
                'caption_below'      		=> 'no',
                'masonry_caption_below'     => 'no',
                'lazy'               		=> 'true',
                'loader'             		=> '',
                'loader_color'       		=> '',
                'animate'            		=> 'true',
                'effect'             		=> '',
                'animate_once'       		=> 'yes',
                'animate_mobile'     		=> false,
                'animate_tablet'     		=> false,
                'gap'                		=> '',
                'gap_tablet'		        => 'inherit',
                'gap_mobile'		        => 'inherit',
                'shadow'             		=> '',
                'grid'               		=> '',
                'masonry_grid'              => '5',
                'masonry_grid_tablet'       => '3',
                'masonry_grid_mobile'       => '2',
                'masonry'               	=> 'off',
                'masonry_mode'              => 'masonry',
                'adjust_row'         		=> '',
                'class'              		=> '',
                'css'                		=> '',
                'cursor_skin'               => 'inherit'
            ), $this->atts ) );

            $this->image_count = NULL;
            $this->image_total_count = NULL;

            $this->mobile_image_count = NULL;
            $this->mobile_total_count = NULL;

	        if( $masonry == 'on' && $masonry_mode == 'masonry' && $masonry_caption_below == 'yes' ) {
                $caption_below = $masonry_caption_below;
	        }

            /* fallback */
            $grid = !$grid ? '1' : $grid;

            if( $masonry == 'on' ) {
	            $grid = $masonry_grid;
            }

            /* available grid sizes */
            $theme_grid = array(
                1 => '100',
                2 => '50',
                3 => '33',
                4 => '25',
                5 => '20'
            );

            $tablet_grid = array(
                1 => '100',
                2 => '50',
                3 => '33',
                4 => '50',
                5 => '33'
            );

            /* class array */
            $classes = array();
            $animation_classes = array();
            $animation_active = false;

            $attributes = array();

            /* extra element class */
            $classes[] = $class;

            /* gap */
            if( $gap ) {
                $classes[] = 'ut-image-gallery-' .  $gap;
            }

	        if( $masonry == 'on' && $masonry_mode == 'masonry') {
		        $classes[] = 'ut-image-gallery-masonry';
	        }

            if( $masonry == 'on' && $masonry_mode == 'fitRows') {
	            $classes[] = 'ut-image-gallery-fitRows';
            }

            if( $animate == 'true' && $effect ) {

                $attributes['data-effect']      = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );

                $animation_classes[]  = 'ut-animate-gallery-element';
                $animation_classes[]  = 'animated';

                if( !$animate_tablet ) {
                    $animation_classes[]  = 'ut-no-animation-tablet';
                }

                if( !$animate_mobile ) {
                    $animation_classes[]  = 'ut-no-animation-mobile';
                }

                if( $animate_once == 'infinite' ) {
                    $animation_classes[]  = 'infinite';
                }

                $animation_active = true;

            }

            /* attributes string */
            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );

	        $gallery_attributes = array();

	        if( $masonry == 'on' && $masonry_mode == 'masonry' ) {

		        $gap_tablet = $gap_tablet == 'inherit' ? $gap : $gap_tablet;
		        $gap_mobile = $gap_mobile == 'inherit' ? $gap_tablet : $gap_mobile;

		        $gallery_attributes['data-mason-gal']  = esc_attr( $masonry );

		        $gallery_attributes['data-mason-gal-gutter'] = !empty( $gap ) ?  esc_attr( $gap ) : "0";
		        $gallery_attributes['data-mason-gal-gutter-tablet'] = !empty( $gap_tablet ) ?  esc_attr( $gap_tablet ) : "0";
		        $gallery_attributes['data-mason-gal-gutter-mobile'] = !empty( $gap_mobile ) ?  esc_attr( $gap_mobile ) : "0";

	        }

	        /* gallery attributes string */
	        $gallery_attributes = implode(' ', array_map(
		        function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
		        $gallery_attributes,
		        array_keys( $gallery_attributes )
	        ) );

	        /* unique ID */
            $this->gallery_id = uniqid("ut_ig_");
            
            /* start output */
            $output = '';

            /* attach css */
            $output .= ut_minify_inline_css( $this->ut_create_inline_css() );
                
            $gallery = explode( ',' , $gallery );

            if( !empty( $gallery ) && is_array( $gallery ) ) {
                
                $this->mobile_total_count = count( $gallery );
                $this->image_total_count  = count( $gallery );
                
                $output .= '<div id="' . esc_attr( $this->gallery_id ) . '" class="ut-image-gallery ut-image-gallery-module ' . implode( ' ', $classes ) . ' clearfix" data-settings="' . $this->gallery_settings_json() . '" ' . $gallery_attributes . '>';
                    
                    foreach( $gallery as $image ) {
                        
                        $image_classes = array();
                        $link_classes = array();
						$img_classes = array();
                        
                        $this->image_count++;
                        $this->mobile_image_count++;                        

                        /* grid settings */
                        if( $masonry == 'off' ) {

	                        if ( $adjust_row == 'yes' ) {

		                        $grid_items        = ( $this->image_total_count >= $grid ) ? $grid : $this->image_total_count;
		                        $grid_items_tablet = ( $this->image_total_count >= $grid ) ? $grid : $this->image_total_count;

		                        /* force grid 33 for tablets */
		                        if ( $grid == '5' ) {
			                        $grid_items_tablet = ( $this->mobile_total_count >= '3' ) ? '3' : $this->mobile_total_count;
		                        }

		                        /* element classes */
		                        $image_classes[] = 'grid-' . $theme_grid[ $grid_items ];
		                        $image_classes[] = 'tablet-grid-' . $tablet_grid[ $grid_items_tablet ];

	                        } else {

		                        $image_classes[] = 'grid-' . $theme_grid[ $grid ];
		                        $image_classes[] = 'tablet-grid-' . $tablet_grid[ $grid ];

	                        }

	                        $image_classes[] = 'mobile-grid-100';

                        }

                        $add_clear = '';
                        
                        /* if counter has reached the maximum of items per row, decrease the total counter */
                        if( $this->mobile_image_count == '3' && $this->mobile_total_count > '3') {
                            $this->mobile_total_count = $this->mobile_total_count - $this->mobile_image_count;
                            $this->mobile_image_count = 0;

	                        if( $masonry == 'off' ) {
		                        $image_classes[] = 'ut-tablet-last';
		                        $add_clear       = '<div class="clear hide-on-desktop hide-on-tablet"></div>';
	                        }

                        }
                        
                        /* if counter has reached the maximum of items per row, decrease the total counter */
                        if( $this->image_count ==  $grid && $this->image_total_count > $grid) {
                            $this->image_total_count = $this->image_total_count - $this->image_count;
                            $this->image_count = 0;

	                        if( $masonry == 'off' ) {
		                        $image_classes[] = 'ut-desktop-last';
		                        $add_clear = '<div class="clear hide-on-tablet"></div>';
	                        }

                        }

                        // get image by size
                        if( $masonry == 'off' ) {

	                        if ( $thumbnail_size == 'custom' ) {

		                        $thumbnail = wp_get_attachment_image_src( $image, 'full' );

	                        } else {

		                        $thumbnail = wp_get_attachment_image_src( $image, $thumbnail_size );

	                        }

	                        // check if upscale is necessary since WordPress does not upscale
	                        if ( get_option( 'large_crop' ) && $thumbnail_size == 'large' && ( isset( $thumbnail[1] ) && $thumbnail[1] < get_option( 'large_size_w' ) || isset( $thumbnail[2] ) && $thumbnail[2] < get_option( 'large_size_h' ) ) ) {

		                        // create new thumb
		                        $new_image    = array();
		                        $new_image[0] = ut_resize( $thumbnail[0], get_option( 'large_size_w' ), get_option( 'large_size_h' ), true, true, true );
		                        $new_image[1] = get_option( 'large_size_w' );
		                        $new_image[2] = get_option( 'large_size_h' );

		                        // assign new thumb
		                        $thumbnail = $new_image;

	                        }

	                        // check if upscale is necessary since WordPress does not upscale
	                        if ( get_option( 'medium_crop' ) && $thumbnail_size == 'medium' && ( isset( $thumbnail[1] ) && $thumbnail[1] < get_option( 'medium_size_w' ) || isset( $thumbnail[2] ) && $thumbnail[2] < get_option( 'medium_size_h' ) ) ) {

		                        // create new thumb
		                        $new_image    = array();
		                        $new_image[0] = ut_resize( $thumbnail[0], get_option( 'medium_size_w' ), get_option( 'medium_size_h' ), true, true, true );
		                        $new_image[1] = get_option( 'medium_size_w' );
		                        $new_image[2] = get_option( 'medium_size_h' );

		                        // assign new thumb
		                        $thumbnail = $new_image;

	                        }

	                        // check if upscale is necessary since WordPress does not upscale
	                        if ( get_option( 'thumbnail_crop' ) && $thumbnail_size == 'medium' && ( isset( $thumbnail[1] ) && $thumbnail[1] < get_option( 'thumbnail_size_w' ) || isset( $thumbnail[2] ) && $thumbnail[2] < get_option( 'thumbnail_size_h' ) ) ) {

		                        // create new thumb
		                        $new_image    = array();
		                        $new_image[0] = ut_resize( $thumbnail[0], get_option( 'thumbnail_size_w' ), get_option( 'thumbnail_size_h' ), true, true, true );
		                        $new_image[1] = get_option( 'thumbnail_size_w' );
		                        $new_image[2] = get_option( 'thumbnail_size_h' );

		                        // assign new thumb
		                        $thumbnail = $new_image;

	                        }

	                        // custom image size
	                        if ( $thumbnail_size == 'custom' ) {

		                        $new_image = array();

		                        $thumbnail_custom_width  = str_replace( "px", "", $thumbnail_custom_width );
		                        $thumbnail_custom_height = str_replace( "px", "", $thumbnail_custom_height );

		                        if ( $thumbnail_custom_crop == 'on' ) {

			                        $new_image[0] = ut_resize( $thumbnail[0], $thumbnail_custom_width, $thumbnail_custom_height, true, true, true );
			                        $new_image[1] = $thumbnail_custom_width;
			                        $new_image[2] = $thumbnail_custom_height;

		                        } else {

			                        $new_image = ut_resize( $thumbnail[0], $thumbnail_custom_width, $thumbnail_custom_height, false, false, true );

		                        }

		                        // assign new thumb
		                        $thumbnail = $new_image;

	                        }

                        } else {

	                        $thumbnail = wp_get_attachment_image_src( $image, 'full' );

	                        if( $thumbnail_size_masonry == 'masonry' ) {

		                        // assign new thumb
		                        $thumbnail = ut_resize( $thumbnail[0], get_option( 'large_size_w' ), get_option( 'large_size_h' ), false, false, true );

	                        }

                        }




						
                        // fallback image
                        if( empty( $thumbnail ) ) {
                
                            $thumbnail   = array();
                            $thumbnail[] = ut_img_asset_url( 'replace-normal.jpg' );
                            $thumbnail[] = "1958";
                            $thumbnail[] = "1224";
                            
                        }

                        if( empty( $thumbnail[0] ) ) {

                            $thumbnail   = array();
                            $thumbnail[] = ut_img_asset_url( 'replace-normal.jpg' );
                            $thumbnail[] = "1958";
                            $thumbnail[] = "1224";

                        }

                        // lightgallery zoom image
                        $link_attributes = '';

                        if( function_exists('ot_get_option') && ot_get_option( 'ut_lightgallery_type', 'lightgallery' ) == 'lightgallery' ) {

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

	                        $link_attributes = ut_get_morphbox_meta( $image );
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
                        
                        // lightgallery thumbnail image
                        $mini = wp_get_attachment_image_src( $image, 'ut-mini' );
                        // $mini = ut_resize( $mini[0], 200, 200, true, false, false );
                        
                        // fallback image
                        if( empty( $mini ) ) {
                
                            $mini   = array();
                            $mini[] = ut_img_asset_url( 'replace-normal.jpg' );
                            $mini[] = "";
                            $mini[] = "";
                            
                        }
                        
                        // attachment meta
                        $attachment_meta = get_post( $image );
                        
                        // needed for hover effect if animation is not active
                        $animation_classes[] = $animation_active ? '' : 'ut-animation-done';

                        // shadow spacing
                        if( $shadow ) {

                            $link_classes[] = $shadow . '-spacing';

                            if( $gap ) {
                                $animation_classes[] = $shadow . '-gap-' . $gap;
                            }

                        }

                        if( $masonry == 'on' && $masonry_mode == 'masonry' ) {

                            $output .= '<div class="ut-image-gallery-item-wrap">';

                        }

                        $output .= '<div ' . $attributes . ' data-count="' . $this->image_total_count . '" class="ut-image-gallery-item ' . implode( ' ', $animation_classes ) . ' ' . implode( ' ', $image_classes ) . '">';
                        
                            if( isset( $attachment_meta->ID ) ) {
                        
                                $alt_value = get_post_meta( $attachment_meta->ID, '_wp_attachment_image_alt', true);
                                $alt_value = empty( $attachment_meta->post_excerpt ) && !empty( $alt_value ) ? $alt_value : $attachment_meta->post_excerpt;

                                $alt_tag = ( $alt == 'off' ) ? 'alt="' . esc_attr( $alt_value ) . '"' : '';
                                $title = ( $alt == 'off' ) ? 'title="' . esc_attr( $attachment_meta->post_excerpt ) . '"' : '';

                                $sub_html = !empty( $attachment_meta->post_excerpt ) ? 'data-sub-html="#ut-image-caption-' . $image . '"' : '';
                            
                            } else {
                                
                                $sub_html = $alt_tag = $title = '';
                                
                            }
                                
                            // image with lightbox
                            if( $lightbox == 'yes' && !$external_link ) {

	                            if( $lightbox_group == 'group' ) {

		                            $link_classes[] = 'ut-vc-images-lightbox-group-image';

	                            }

                                $output .= '<a ' . $title . ' class="ut-vc-images-lightbox ' . implode( " ", $link_classes ) . '" ' . $sub_html . ' data-exthumbimage="' . esc_url( $mini[0] ) . '" href="' . esc_url( $lightgallery[0] ) . '" ' . $link_attributes . '>';
                            
                            }

                            if( $lightbox == 'no' && !$external_link ) {

                                $output .= '<a class="ut-deactivated-link ' . implode( " ", $link_classes ) . '" href="#">';

                            }

                        
                            $attachment_project_url = NULL;
                        
                            // image with external link
                            $attachment_project_url = get_post_meta($image, 'ut_attachment_url', true );
                        
                            if( $external_link == 'yes' && $attachment_project_url ) {
                                    
                                $output .= '<a ' . $title . ' href="' . esc_url( $attachment_project_url ) . '" target="_' . $external_link_target . '" class="' . implode( " ", $link_classes ) . '">';

                            }

                            $img_shadow_class = array('ut-box-shadow-container');

							if( $shadow == 'yes' ) {

                                $img_shadow_class[] = 'gutter-shadow';

							} else {

                                $img_shadow_class[] = $shadow;

                            }

							if( $shadow && $lazy == 'true' ) {

                                $img_shadow_class[] = 'ut-box-shadow-lazy';

                            }

		                    /* custom cursor */
		                    $cursor_skin_attribute = '';

		                    if( $cursor_skin !== 'inherit' ) {

			                    $cursor_skin_attribute = 'data-cursor-skin="'. esc_attr( $cursor_skin ) . '"';

		                    }

                            $output .= '<div class="ut-image-gallery-image" ' . $cursor_skin_attribute . '>';

							    if( $shadow ) {

							        $output .= '<div class="' . implode( ' ', $img_shadow_class ) . '"><div class="ut-box-shadow-container-inner"></div></div>';

                                }

                                if( $lazy == 'true' ) {

	                                $output .= '<img ' . $alt_tag . ' class="' . implode( ' ', $img_classes ) . ' ut-lazy skip-lazy" src="' . $this->create_placeholder_svg( $thumbnail[1], $thumbnail[2] ) . '" data-src="' . esc_url( $thumbnail[0] ) . '" width="' . esc_attr( $thumbnail[1] ) . '" height="' . esc_attr( $thumbnail[2] ) . '">';


                                } else {

	                                $output .= '<img ' . $alt_tag . ' class="' . implode( ' ', $img_classes ) . '" src="' . esc_url( $thumbnail[0] ) . '" width="' . esc_attr( $thumbnail[1] ) . '" height="' . esc_attr( $thumbnail[2] ) . '">';

                                }

                                if( $loader ) {

                                    $output .= '<div class="ut-video-module-loading">';

	                                    $output .= '<div class="ut-image-gallery-loader"><div class="ut-image-gallery-loader-inner"></div></div>';

                                    $output .= '</div>';

                                }                                
                                
                                if( $caption == 'yes' ) {

                                    $output .= '<div class="ut-image-gallery-image-caption">';

                                        $output .= '<div class="ut-image-gallery-item-caption-title">';

                                        if( !empty( $attachment_meta->post_excerpt ) && $caption_content == 'caption' ) {

                                            $output .= '<h3>' . $attachment_meta->post_excerpt . '</h3>';

                                        } else if( !empty( $attachment_meta->post_title ) && $caption_content == 'title' ) {

                                            $output .= '<h3>' . $attachment_meta->post_title . '</h3>';

                                        } elseif( $caption_content == 'custom' && ( !empty( $custom_caption ) || !empty( $custom_caption_small ) ) ) {

	                                        if( !empty( $custom_caption ) && !empty( $custom_caption_small ) ) {

		                                        $output .= '<h3>' . $custom_caption . '<br /><small>' . $custom_caption_small . '</small></h3>';

	                                        } elseif( empty( $custom_caption ) && !empty( $custom_caption_small ) ) {

		                                        $output .= '<h3><small>' . $custom_caption_small . '</small></h3>';

	                                        } elseif( !empty( $custom_caption ) && empty( $custom_caption_small ) ) {

		                                        $output .= '<h3>' . $custom_caption . '</h3>';

	                                        }
                                            
                                        } else {
                                            
                                            $output .= '<h3 class="ut-image-gallery-empty-title">+</h3>';

                                        }

                                         $output .= '</div>';

                                    $output .= '</div>';                                

                                }

                            $output .= '</div>';    

                            if ( !empty( $attachment_meta->post_excerpt ) ) {

                                $output .= '<div id="ut-image-caption-' . $image . '" class="ut-vc-images-lightbox-caption">' . $attachment_meta->post_excerpt . '</div>';            

                            }
                        
                        if( $lightbox == 'yes' && !$external_link  || $external_link == 'yes' && $attachment_project_url || $lightbox == 'no' && !$external_link ) {
                        
                            $output .= '</a>';

                        }
                            
                        if( $caption_below == 'yes' && !empty( $attachment_meta->post_excerpt ) ) {

                            $output .= '<div class="ut-gallery-slider-caption">' . $attachment_meta->post_excerpt . '</div>';

                        }

	                    if( $masonry == 'on' && $masonry_mode == 'masonry' ) {

		                    $output .= '</div>';

	                    }

                        $output .= '</div>';
                        $output .= $add_clear;
                            
                    }

                    $output .= '<div class="ut-image-gallery-sizer"></div>';

                    $output .= '<div class="clear"></div>';
                        
                $output .= '</div>';
            
            }
            
            $wpb = $gap ? array( 'ut-gallery-' .  $gap ) : array();
            
            return '<div class="wpb_content_element ' . implode( ' ', $wpb ) . ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . ' clearfix">' . $output . '</div>'; 
        
        }
            
    }

}

new UT_Image_Gallery;


if ( class_exists( 'WPBakeryShortCode' ) ) {
    
    class WPBakeryShortCode_ut_image_gallery extends WPBakeryShortCode {
        
        /* add images to visual composer */
        public function singleParamHtmlHolder( $param, $value ) {
            
            $output = '';
            
            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
            
            if ( 'gallery' === $param_name ) {
                
                $images_ids = empty( $value ) ? array() : explode( ',', trim( $value ) );

                $output .= '<ul class="attachment-thumbnails ut-attachment-list-builder' . ( empty( $images_ids ) ? ' image-exists' : '' ) . '" data-name="' . $param_name . '">';
                foreach ( $images_ids as $image ) {
                    $img = wpb_getImageBySize( array( 'attach_id' => (int) $image, 'thumb_size' => 'thumbnail' ) );
                    $output .= ( $img ? '<li>' . $img['thumbnail'] . '</li>' : '<li><img width="150" height="150" test="' . $image . '" src="' . vc_asset_url( 'vc/blank.gif' ) . '" class="attachment-thumbnail" alt="" title="" /></li>' );
                }
                $output .= '</ul>';
                $output .= '<a href="#" class="column_edit_trigger' . ( ! empty( $images_ids ) ? ' image-exists' : '' ) . '">' . __( 'Add images', 'js_composer' ) . '</a>';
    
            }
            
            return $output;
            
        }        
        
    }
    
}