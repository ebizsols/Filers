<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Grid_Blog' ) ) {
	
    class UT_Grid_Blog {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_grid_blog';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Latest Blog Posts', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'A grid blog containing the latest posts or posts of a chosen category.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-th ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/blog-excerpt.png',
                    'category'        => 'Community',
                    'class'           => 'ut-vc-icon-module ut-community-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Posts to Display', 'ut_shortcodes' ),
                            'description' => esc_html__( 'Enter desired amount of posts to display. Default 3.', 'ut_shortcodes' ),
                            'param_name'  => 'numberposts',
                            'group'       => 'General'
                        ),

                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Excerpt Length', 'ut_shortcodes' ),
                            'description' => esc_html__( '', 'ut_shortcodes' ),
                            'param_name'  => 'excerpt',
                            'group'       => 'General'
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Hide Categories', 'ut_shortcodes' ),
                            'param_name'        => 'hide_meta_categories',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'on'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Hide Author and Comments', 'ut_shortcodes' ),
                            'param_name'        => 'hide_meta_footer',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'on'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Hide Date', 'ut_shortcodes' ),
                            'param_name'        => 'hide_date',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'on'
                            ),
                        ),





                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Post Excerpt Content Padding', 'ut_shortcodes' ),
                            'description' 		=> esc_html__( 'Once Border and Shadow are deactivated, the post excerpt still has its default spacing. With the help of this option, you can remove this spacing.', 'ut_shortcodes' ),
                            'param_name'        => 'disable_content_padding',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'on'
                            ),
                        ),

                        // Query Settings
                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Show Posts by Category ID', 'ut_shortcodes' ),
                            'description' => esc_html__( 'Enter category ID, separate multiple category IDs with comma.', 'ut_shortcodes' ),
                            'param_name'  => 'cat',
                            'group'       => 'Query Settings'
                        ),
                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Show Posts by Category Name', 'ut_shortcodes' ),
                            'description' => esc_html__( 'Enter category slug, separate multiple category slugs with comma.', 'ut_shortcodes' ),
                            'param_name'  => 'category_name',
                            'group'       => 'Query Settings'
                        ),

                        // Blog Colors
                        array(
                            'type'       => 'ut_option_separator',
                            'group'      => 'Blog Colors',
                            'param_name' => 'meta_info',
                            'info'       => esc_html__( 'Article Meta Colors', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Meta Background Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Background of Categories, Comments and Author.', 'ut_shortcodes' ),
                            'param_name'        => 'article_meta_background_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Meta Icon Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Icon Color in front of Categories, Comments and Author.', 'ut_shortcodes' ),
                            'param_name'        => 'article_meta_icon_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Meta Link Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Link Color for Categories, Comments and Author.', 'ut_shortcodes' ),
                            'param_name'        => 'article_meta_link_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Meta Link Hover Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Link Mouse Over Color for Categories, Comments and Author.', 'ut_shortcodes' ),
                            'param_name'        => 'article_meta_link_color_hover',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Background Color', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'article_background_color',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Title Color', 'ut_shortcodes' ),
                            'param_name'        => 'article_title_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Title Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'article_title_color_hover',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Excerpt Color', 'ut_shortcodes' ),
                            'param_name'        => 'article_excerpt_color',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'       => 'ut_option_separator',
                            'group'      => 'Blog Colors',
                            'param_name' => 'meta_info',
                            'info'       => esc_html__( 'Post Format Colors', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Slider Controls Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'slider_controls_background_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Slider Controls Arrow Color', 'ut_shortcodes' ),
                            'param_name'        => 'slider_controls_arrow_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Post Format Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'pformat_icon_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Post Format Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'pformat_background_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Blog Colors'
                        ),

                        array(
                            'type'       => 'ut_option_separator',
                            'group'      => 'Blog Colors',
                            'param_name' => 'meta_info',
                            'info'       => esc_html__( 'Article Border and Shadow', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Box Border', 'ut_shortcodes' ),
                            'param_name'        => 'disable_border',
                            'group'             => 'Blog Colors',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'article_border_color',
                            'group'             => 'Blog Colors',
                            'edit_field_class'  => 'vc_col-sm-6 ut-force-option-dependency-placeholder',
                            'dependency'        => array(
                                'element' => 'disable_border',
                                'value'   => 'off',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Box Shadow', 'ut_shortcodes' ),
                            'param_name'        => 'disable_shadow',
                            'group'             => 'Blog Colors',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Article Shadow Color', 'ut_shortcodes' ),
                            'param_name'        => 'article_shadow_color',
                            'group'             => 'Blog Colors',
                            'edit_field_class'  => 'vc_col-sm-6 ut-force-option-dependency-placeholder',
                            'dependency'        => array(
                                'element' => 'disable_shadow',
                                'value'   => 'off',
                            ),
                        ),

	                    // Blog Date
	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'Blog Date',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'Article Colors', 'ut_shortcodes' ),
	                    ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Article Date Color', 'ut_shortcodes' ),
		                    'param_name'        => 'article_date_color',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Blog Date',
		                    'dependency'    => array(
			                    'element'   => 'hide_date',
			                    'value_not_equal_to' => array('on')
		                    ),
	                    ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Article Date Background Color', 'ut_shortcodes' ),
		                    'param_name'        => 'article_date_background_color',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'Blog Date',
		                    'dependency'    => array(
			                    'element'   => 'hide_date',
			                    'value_not_equal_to' => array('on')
		                    ),
	                    ),
	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'Blog Date',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'Date Position', 'ut_shortcodes' ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Date Position', 'unitedthemes' ),
		                    'param_name'        => 'article_date_position',
		                    'group'             => 'Blog Date',
		                    'value'             => array(
			                    esc_html__( 'top left', 'ut_shortcodes' ) => 'top_left',
			                    esc_html__( 'top left no spacing', 'ut_shortcodes' ) => 'top_left_no_spacing',
			                    esc_html__( 'top left offset', 'ut_shortcodes' ) => 'top_left_offset',
			                    esc_html__( 'top left offset overlap', 'ut_shortcodes' ) => 'top_left_offset_overlap'
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Date Size', 'unitedthemes' ),
		                    'param_name'        => 'article_date_size',
		                    'group'             => 'Blog Date',
		                    'value'             => array(
			                    esc_html__( 'default', 'ut_shortcodes' ) => 'default',
			                    esc_html__( 'small', 'ut_shortcodes' ) => 'small'
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Add Shadow to Date?', 'unitedthemes' ),
		                    'param_name'        => 'article_date_shadow',
		                    'group'             => 'Blog Date',
		                    'value'             => array(
			                    esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
			                    esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
		                    )
	                    ),

                        // Button Settings
                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Button Text leading to Main Blog.', 'ut_shortcodes' ),
                            'param_name'  => 'button_text',
                            'group'       => 'Blog Button'
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Icon?', 'unitedthemes' ),
                            'param_name'        => 'button_add_icon',
                            'group'             => 'Blog Button',
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
                            'group'         => 'Blog Button',
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
                            'group'             => 'Blog Button',
                            'dependency' => array(
                                'element'   => 'button_icon_type',
                                'value'     => 'fontawesome',
                            ),
                        ),

                        array(
                            'type'              => 'iconpicker',
                            'heading'           => esc_html__( 'Choose Icon', 'ut_shortcodes' ),
                            'param_name'        => 'button_icon_bklyn',
                            'group'             => 'Blog Button',
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
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Icon Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'button_icon_align',
                            'group'             => 'Blog Button',
                            'value'             => array(
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                            'dependency'        => array(
                                'element' => 'button_add_icon',
                                'value'   => 'yes',
                            ),
                        ),

                        /* Button Colors */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Button Effect', 'unitedthemes' ),
                            'param_name'        => 'button_effect',
                            'group'             => 'Blog Button Colors',
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
                            'group'             => 'Blog Button Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_background',
                            'group'             => 'Blog Button Colors'
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_text_color_hover',
                            'group'             => 'Blog Button Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Background Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_background_hover',
                            'group'             => 'Blog Button Colors'
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Second Background Hover Color for Button Effect', 'ut_shortcodes' ),
                            'param_name'        => 'button_background_hover_2',
                            'group'             => 'Blog Button Colors',
                            'dependency'        => array(
                                'element' => 'button_effect',
                                'value'   => array('aylen'),
                            ),
                        ),

                        /* Button Design */
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Button Size', 'ut_shortcodes' ),
                            'param_name'        => 'button_size',
                            'group'             => 'Blog Button Design',
                            'edit_field_class'  => 'vc_col-sm-6',
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
                            'group'             => 'Blog Button Design',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'center', 'ut_shortcodes' ) => 'bklyn-btn-center',
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'bklyn-btn-left',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'bklyn-btn-right',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Justify Button?', 'unitedthemes' ),
                            'param_name'        => 'button_fluid',
                            'group'             => 'Blog Button Design',
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
                            'group'             => 'Blog Button Design',
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
                            'group'             => 'Blog Button Design',
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
                            'group'             => 'Blog Button Design',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'button_border_color',
                            'group'             => 'Blog Button Design',
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
                            'group'             => 'Blog Button Design',
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
                            'group'             => 'Blog Button Design',
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
                            'group'             => 'Blog Button Design',
                            'dependency'        => array(
                                'element' => 'button_custom_border',
                                'value'   => 'yes',
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Font Family', 'ut_shortcodes' ),
                            'param_name'        => 'font_family',
                            'group'             => 'Blog Button Font',
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
                            'group'             => 'Blog Button Font',
                            'value'             => array(
                                esc_html__( 'Select Font Weight' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' )             => 'normal',
                                esc_html__( 'bold' , 'ut_shortcodes' )               => 'bold'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Text Transform', 'ut_shortcodes' ),
                            'param_name'        => 'text_transform',
                            'group'             => 'Blog Button Font',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' ) => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' ) => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' ) => 'lowercase'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'letter_spacing',
                            'group'             => 'Blog Button Font',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-10',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
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
            
            $class = '';
            
            extract( shortcode_atts( array (
                
				'numberposts'     		    => '3',
                'excerpt'         		    => '40',
				'hide_meta_categories'	    => '',
				'hide_meta_footer'	    	=> '',
				'hide_date'				    => '',
				'disable_shadow'		    => '',
				'disable_border'		    => '',
				'disable_content_padding'   => '',
                'cat'             		    => '',
                'category_name'   		    => '',
                'button_text'      		    => '',
                'class'           		    => '',

				// Blog Colors
				'article_date_color'                => '',
                'article_date_background_color'     => '',
                'article_date_position'             => 'top_left',
                'article_date_size'                 => 'default',
                'article_date_shadow'               => '',
                'article_title_color'               => '',
                'article_title_color_hover'         => '',
                'article_excerpt_color'             => '',
                'article_background_color'          => '',
				'article_border_color'              => '',
				'article_shadow_color'              => '',
				'pformat_icon_color'                => '',
                'pformat_background_color'          => '',
				'article_meta_background_color'     => '',
				'article_meta_icon_color'           => '',
				'article_meta_link_color'           => '',
				'article_meta_link_color_hover'     => '',
				'slider_controls_arrow_color'       => '',
				'slider_controls_background_color'  => '',

				// Blog Button
				'button_align'              => 'bklyn-btn-center',
                'button_size'               => 'bklyn-btn-normal',
                'button_fluid'              => '',
                'button_hover_shadow'       => 'no',
                
                /* button icons */
                'button_add_icon'           => '',
                'button_icon_type'          => 'bklynicons',
                'button_icon'               => '',
                'button_icon_bklyn'         => '',
                'button_icon_align'         => 'left',
				
				/* button olor settings */
                'button_effect'             => 'none',
                'button_text_color'         => '',
                'button_text_color_hover'   => '',
                'button_background'         => '',
                'button_background_hover'   => '',
                'button_background_hover_2' => '',
                
                /* button border settings */
                'button_custom_border'      => '',
                'button_border_color'       => '',
                'button_border_color_hover' => '',
                'button_border_style'       => '',
                'button_border_width'       => '',
                'button_border_radius'      => '',
                
                /* font */
                'font_family'               => '',
                'font_weight'               => '',
                'letter_spacing'            => '',
                'text_transform'            => '',
                
                /* css */
                'spacing'                   => '',
				
            ), $atts ) ); 
            
			// avoid error with early hooks
			if( !function_exists("unite_get_excerpt_by_id") ) {
				return;
			}			
			
            $args = array(
                'post_type'      => 'post',
                'cat'            => $cat,
                'category_name'  => $category_name,
                'posts_per_page' => $numberposts,    
            );
			
			$id = uniqid("ut_blog_grid_");
			
			$css_style = '<style type="text/css">';
			
				if( $hide_date ) {
					$css_style .= '#' . esc_attr( $id ) . ' .date-format { display:none; }';	
				}

				if( $article_meta_background_color ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .entry-meta { background: ' . $article_meta_background_color . '; }';
                }

                if( $article_meta_icon_color ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .entry-meta .fa { color: ' . $article_meta_icon_color . '; }';
                }

                if( $article_meta_link_color ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .entry-meta a { color: ' . $article_meta_link_color . '; }';
                }

                if( $article_meta_link_color_hover ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .entry-meta a:hover { color: ' . $article_meta_link_color_hover . '; }';
                    $css_style .= '#' . esc_attr( $id ) . ' .entry-meta a:active { color: ' . $article_meta_link_color_hover . '; }';
                }

				if( $article_date_color ) {
					$css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .date-format > span { color: ' . $article_date_color . '; }';
				}

		        if( $article_date_background_color ) {

		        	$css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .date-format { background-color: ' . $article_date_background_color . '; padding: 0.2em; }';
		        	$css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid.ut-blog-grid-date-small .date-format { background-color: ' . $article_date_background_color . '; padding: 0.4em; }';

		        }

		        if( $article_date_position == 'top_left_no_spacing' ) {
			        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .date-format { top: 0; left: 0; }';
		        }

		        if( $article_date_position == 'top_left_offset' ) {

		        	if( $hide_meta_categories == 'on' ) {

				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .date-format { top: -20px; left: 0; }';
				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .ut-blog-grid-article-inner { overflow: visible; }';

			        } else {

				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .date-format { top: -45px; left: 0; }';
				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .entry-meta.entry-meta-top { padding-left: 120px; }';

			        }

			        $css_style .= '#' . esc_attr( $id ) . ' { padding-top: 20px; }';

		        }

		        if( $article_date_position == 'top_left_offset_overlap' ) {

			        if( $hide_meta_categories == 'on' ) {

				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .date-format { top: -20px; left: 20px; }';
				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .ut-blog-grid-article-inner { overflow: visible; }';

			        } else {

				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .date-format { top: -45px; left: 20px; }';
				        $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid .entry-meta.entry-meta-top { padding-left: 120px; }';

			        }

			        $css_style .= '#' . esc_attr( $id ) . ' { padding-top: 20px; }';

		        }

				if( $article_title_color ) {
					$css_style .= '#' . esc_attr( $id ) . ' h2.entry-title { color: ' . $article_title_color . '; }';
				}

                if( $article_title_color_hover ) {
                    $css_style .= '#' . esc_attr( $id ) . ' h2.entry-title:hover { color: ' . $article_title_color_hover . '; }';
                }

                if( $article_excerpt_color ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .entry-content { color: ' . $article_excerpt_color . '; }';
                }

				if( $disable_shadow ) {
					$css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid-article-inner { -webkit-box-shadow:none; -moz-box-shadow:none; box-shadow:none; }';
				} else {
                    $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid-article { margin-top: 10px; }';
                }
			
				if( $disable_border ) {
					$css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid-article-inner { border:none; }';
				}

				if( $article_background_color ) {
					$css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid-article-inner { background: ' . $article_background_color . '; }';
				}

                if( $article_border_color ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid-article-inner { border-color: ' . $article_border_color . '; }';
                }

                if( $article_shadow_color ) {
                    $css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid-article-inner {  -webkit-box-shadow: 0 0 10px ' . $article_shadow_color . '; box-shadow: 0 0 10px ' . $article_shadow_color . '; }';
                }

				if( $disable_content_padding ) {
					$css_style .= '#' . esc_attr( $id ) . ' .ut-blog-grid-content-wrap { padding:40px 0 0 0; }';
				}

				if( $pformat_icon_color ) {
					$css_style .= '#' . esc_attr( $id ) . ' .ut-meta-post-icon i { color: ' . $pformat_icon_color . '; }';
				}

				if( $pformat_background_color ) {
					$css_style .= '#' . esc_attr( $id ) . ' .ut-meta-post-icon { background-color: ' . $pformat_background_color . '; }';
				}

				if( $slider_controls_arrow_color ) {
					$css_style .= '#' . esc_attr( $id ) . ' .flex-direction-nav a { color: ' . $slider_controls_arrow_color . '; }';
				}

				if( $slider_controls_background_color ) {
					$css_style .= '#' . esc_attr( $id ) . ' .flex-direction-nav a { background-color: ' . $slider_controls_background_color . '; }';
				}
			
			$css_style .= '</style>';

			ob_start();

	        $template_args = array(
		        'hide_meta_categories' => $hide_meta_categories,
		        'hide_meta_footer' => $hide_meta_footer
	        );

			// classes
			$classes = array( $class, 'clearfix' );
			$classes[] = 'ut-blog-grid-date-' . $article_date_size;

			if( $article_date_shadow == 'on' ) {
				$classes[] = 'ut-blog-grid-date-shadow';
			}

			if( defined('UT_THEME_VERSION') ) {
				
				echo $css_style;
				
				echo '<div id="' . esc_attr( $id ) . '" class="ut-blog-grid-module blog">';
				
					echo '<div class="ut-blog-grid ' . implode( " ", $classes ) . '">';

						// initiate query
						$blog_query = new WP_Query( $args );

						// start loop
						if ( $blog_query->have_posts() ) : 

							global $post;
                            global $is_grid_blog;
                            global $grid_blog_count;

                            $is_grid_blog    = true;
                            $grid_blog_count = $blog_query->post_count;

							while ( $blog_query->have_posts() ) : $blog_query->the_post();

								setup_postdata( $post );

								if( get_post_format() ) {

									// include( get_template_directory() . '/partials/blog-grid/content-' . get_post_format() . '.php' );
									get_template_part( 'partials/blog-grid/content', get_post_format(), $template_args );

								} else {

									// include( get_template_directory() . '/partials/blog-grid/content.php' );
									get_template_part( 'partials/blog-grid/content', '',$template_args );

								}

							endwhile; 

						endif;        

						// restore original post data
						wp_reset_postdata();

					echo '</div>';
				
				echo '</div>';
				
				if( $button_text ) {
					
					// set blog link
					$blog_id = get_option('page_for_posts');  
					$atts['button_plain_link'] = get_permalink( $blog_id );
					
					// Blog Button
					$blog_button = new UT_BTN();
					echo $blog_button->ut_create_shortcode( $atts );
					
				}
            
			} else {
				
				echo esc_html__( 'Brooklyn Theme not active!', 'ut_shortcodes' );
				
			}

            return ob_get_clean();
        
        }
            
    }

}

new UT_Grid_Blog;