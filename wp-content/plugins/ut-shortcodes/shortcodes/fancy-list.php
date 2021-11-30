<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Fancy_list' ) ) {
	
    class UT_Fancy_list {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_fancy_list';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Fancy List', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Create lists of all kinds and for every purpose.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-list-ol ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/fancy-list.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

	                    array(
		                    'type'        => 'textfield',
		                    'heading'     => esc_html__( 'Description', 'ut_shortcodes' ),
		                    'description' => esc_html__( 'Only for internal use. This adds a label to Visual Composer for an easier element identification.', 'ut_shortcodes' ),
		                    'param_name'  => 'list_description',
		                    'admin_label' => true,
		                    'group'       => 'General'
	                    ),

	                    array(
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'General',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'List Style', 'ut_shortcodes' ),
	                    ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'List Style', 'ut_shortcodes' ),
                            'param_name'        => 'list_style_type',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'none'   , 'ut_shortcodes' )  => 'none',
                                esc_html__( 'Icons'  , 'ut_shortcodes' )  => 'icons',
                                esc_html__( 'disc'   , 'ut_shortcodes' )  => 'disc',
                                esc_html__( 'circle' , 'ut_shortcodes' )  => 'circle',
                                esc_html__( 'square' , 'ut_shortcodes' )  => 'square',
                                esc_html__( 'decimal', 'ut_shortcodes' )  => 'decimal',
                                esc_html__( 'decimal-leading-zero'  , 'ut_shortcodes' ) => 'decimal-leading-zero',
                                esc_html__( 'lower-roman', 'ut_shortcodes' ) => 'lower-roman',
                                esc_html__( 'upper-roman', 'ut_shortcodes' ) => 'upper-roman',
                                esc_html__( 'lower-greek', 'ut_shortcodes' ) => 'lower-greek',
                                esc_html__( 'lower-latin', 'ut_shortcodes' ) => 'lower-latin',
                                esc_html__( 'upper-latin', 'ut_shortcodes' ) => 'upper-latin',
                                esc_html__( 'armenian'   , 'ut_shortcodes' ) => 'armenian',
                                esc_html__( 'georgian'   , 'ut_shortcodes' ) => 'georgian',
                                esc_html__( 'lower-alpha', 'ut_shortcodes' ) => 'lower-alpha',
                                esc_html__( 'upper-alpha', 'ut_shortcodes' ) => 'upper-alpha'
                            )
                        ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'List Icon Font Size', 'ut_shortcodes' ),
		                    'param_name'        => 'list_icon_font_size',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'General',
		                    'value'             => array(
			                    'default'   => ut_get_theme_options_font_setting( 'body_font', 'font-size', "14" ),
			                    'min'       => '8',
			                    'max'       => '60',
			                    'step'      => '1',
			                    'unit'      => 'px'
		                    ),
		                    'dependency'        => array(
			                    'element' => 'list_style_type',
			                    'value'   => 'icons',
		                    )
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'List Icon Vertical Align', 'ut_shortcodes' ),
		                    'param_name'        => 'list_icon_vertical_align',
		                    'edit_field_class'  => 'vc_col-sm-6',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'middle', 'ut_shortcodes' ) => '',
			                    esc_html__( 'top'  , 'ut_shortcodes' ) => 'top',
			                    esc_html__( 'bottom' , 'ut_shortcodes' ) => 'bottom',
		                    ),
		                    'dependency'        => array(
			                    'element' => 'list_style_type',
			                    'value'   => 'icons',
		                    )
	                    ),
	                    array(
		                    'type'             => 'dropdown',
		                    'heading'          => esc_html__( 'Space between List Style and Text', 'ut_shortcodes' ),
		                    'param_name'       => 'list_icon_text_space',
		                    'group'            => 'General',
		                    'value'            => array(
			                    esc_html__( 'X Small', 'ut_shortcodes' )=> 'xsmall',
			                    esc_html__( 'Medium', 'ut_shortcodes' ) => 'medium',
			                    esc_html__( 'Small', 'ut_shortcodes' )  => 'small',
			                    esc_html__( 'Large', 'ut_shortcodes' )  => 'large',
			                    esc_html__( 'X Large', 'ut_shortcodes' )=> 'xlarge'
		                    ),
		                    'dependency'       => array(
			                    'element'            => 'list_style_type',
			                    'value_not_equal_to' => 'none',
		                    )
	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'List Font Size', 'ut_shortcodes' ),
		                    'param_name'        => 'list_font_size',
		                    'group'             => 'General',
		                    'value'             => array(
			                    'default'   => ut_get_theme_options_font_setting( 'body_font', 'font-size', "14" ),
			                    'min'       => '8',
			                    'max'       => '40',
			                    'step'      => '1',
			                    'unit'      => 'px'
		                    ),
	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'List Line height', 'ut_shortcodes' ),
		                    'param_name'        => 'list_line_height',
		                    'group'             => 'General',
		                    'value'             => array(
			                    'default'   =>  ut_get_theme_options_font_setting( 'body_font', 'line-height', "28" ),
			                    'min'       => '10',
			                    'max'       => '60',
			                    'step'      => '1',
			                    'unit'      => 'px'
		                    ),

	                    ),
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'List Spacing Bottom', 'ut_shortcodes' ),
		                    'param_name'        => 'list_margin_bottom',
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
		                    'type'       => 'ut_option_separator',
		                    'group'      => 'General',
		                    'param_name' => 'meta_info',
		                    'info'       => esc_html__( 'List Alignment', 'ut_shortcodes' ),
	                    ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'List Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'list_align',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'List Alignment Tablet', 'ut_shortcodes' ),
		                    'param_name'        => 'list_align_tablet',
		                    'group'             => 'General',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'left', 'ut_shortcodes' )                => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' )              => 'center',
			                    esc_html__( 'right', 'ut_shortcodes' )               => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'List Alignment Mobile', 'ut_shortcodes' ),
		                    'param_name'        => 'list_align_mobile',
		                    'group'             => 'General',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'value'             => array(
			                    esc_html__( 'center', 'ut_shortcodes' )              => 'center',
		                    	esc_html__( 'left', 'ut_shortcodes' )                => 'left',
			                    esc_html__( 'right', 'ut_shortcodes' )               => 'right',
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Animate List Items?', 'ut_shortcodes' ),
		                    'param_name'        => 'animate_list',
		                    'group'             => 'List Items',
		                    'value'             => array(
			                    esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
			                    esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
		                    ),
	                    ),
                        array(
                        	'type'          => 'ut_info_box',
							'heading'       => esc_html__( 'Animation Timings Info:', 'ut_shortcodes' ),
	                        'param_name'    => 'info',
	                        'group'         => 'List Items',
	                        'info'          => esc_html__( 'Simultaneously with a delay of 100ms between First and Second Line. Each following list item has a delay of 150ms.', 'ut_shortcodes' ),
	                        'dependency' => array(
		                        'element'   => 'animate_list',
		                        'value'     => 'on',
	                        )
                        ),

                        array(
                            'type'          => 'param_group',
                            'heading'       => esc_html__( 'List Items', 'ut_shortcodes' ),
                            'group'         => 'List Items',
                            'param_name'    => 'values',
                            'params' => array(
	                            array(
		                            'type'          => 'iconpicker',
		                            'heading'       => esc_html__( 'Icon', 'ut_shortcodes' ),
		                            'description'   => esc_html__( 'Only applies when List Style has been set to Icons.', 'ut_shortcodes' ),
		                            'param_name'    => 'icon'
	                            ),
	                            array(
		                            'type'             => 'textfield',
		                            'heading'          => esc_html__( 'First Line', 'ut_shortcodes' ),
		                            'edit_field_class' => 'vc_col-sm-9',
		                            'admin_label'      => true,
		                            'param_name'       => 'title',
	                            ),
	                            array(
		                            'type'             => 'dropdown',
		                            'heading'          => esc_html__( 'First Line Bold?', 'ut_shortcodes' ),
		                            'edit_field_class' => 'vc_col-sm-3',
		                            'param_name'       => 'title_is_bold',
		                            'value'             => array(
			                            esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false',
		                            	esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
		                            ),
	                            ),
	                            array(
		                            'type'             => 'textfield',
		                            'heading'          => esc_html__( 'Second Line', 'ut_shortcodes' ),
		                            'edit_field_class' => 'vc_col-sm-9',
		                            'admin_label'      => true,
		                            'param_name'       => 'title_extend',
	                            ),
	                            array(
		                            'type'             => 'dropdown',
		                            'heading'          => esc_html__( 'Second Line Bold?', 'ut_shortcodes' ),
		                            'edit_field_class' => 'vc_col-sm-3',
		                            'param_name'       => 'title_extend_is_bold',
		                            'value'             => array(
			                            esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false',
			                            esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
		                            ),
	                            ),
	                            array(
		                            'type'          => 'dropdown',
		                            'heading'       => esc_html__( 'List Item is a link?', 'ut_shortcodes' ),
		                            'param_name'    => 'is_link',
		                            'value'             => array(
			                            esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false',
			                            esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'true',
		                            ),
	                            ),
	                            array(
		                            'type'          => 'vc_link',
		                            'heading'       => esc_html__( 'Link', 'ut_shortcodes' ),
		                            'param_name'    => 'link',
		                            'dependency'    => array(
			                            'element' => 'is_link',
			                            'value'   => array( 'true' ),
		                            )
	                            ),

                            ),

                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'icon_color',
                            'group'             => 'List Colors',
                            'edit_field_class'  => 'vc_col-sm-6',
                        ),
	                    array(
		                    'type'              => 'colorpicker',
		                    'heading'           => esc_html__( 'Icon Hover Color', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Only applies if list item is link.', 'ut_shortcodes' ),
		                    'param_name'        => 'icon_color_hover',
		                    'group'             => 'List Colors',
		                    'edit_field_class'  => 'vc_col-sm-6',
	                    ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Color', 'ut_shortcodes' ),
                            'param_name'        => 'text_color',
                            'group'             => 'List Colors',
                            'edit_field_class'  => 'vc_col-sm-6',
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Text Hover Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Only applies if list item is link.', 'ut_shortcodes' ),
                            'param_name'        => 'text_color_hover',
                            'group'             => 'List Colors',
                            'edit_field_class'  => 'vc_col-sm-6',
                        ),
                        array(
                            'type'          => 'css_editor',
                            'param_name'    => 'css',
                            'group'         => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),

                    )

                )

            ); /* end mapping */
        
        }

	    function ut_create_shortcode( $atts, $content = NULL ) {

            /**
             * @var $list_align
             * @var $list_align_tablet
             * @var $list_align_mobile
             *
             * @var $class
             */
		    extract( shortcode_atts( array (
			    'list_style_type'  			=> 'none',
			    'list_align'       			=> 'left',
			    'list_align_tablet'         => 'inherit',
			    'list_align_mobile'         => 'center',
			    'list_font_size'   			=> ut_get_theme_options_font_setting( 'body_font', 'font-size', "14" ),
			    'list_icon_text_space'      => 'xsmall',
			    'list_line_height' 			=> ut_get_theme_options_font_setting( 'body_font', 'line-height' ),
			    'list_margin_bottom'		=> '',
			    'list_icon_font_size'		=> '',
			    'list_icon_vertical_align'  => 'middle',
			    'animate_list'              => '',
			    'values'           			=> '',
			    'icon_color'       			=> '',
			    'icon_color_hover' 			=> '',
			    'text_color'       			=> '',
			    'text_color_hover' 			=> '',
			    'css'              			=> '',
			    'class'                     => '',
		    ), $atts ) );

		    /* extract list items */
		    if( function_exists('vc_param_group_parse_atts') && !empty( $values ) ) {

			    $values = vc_param_group_parse_atts( $values );

		    }

		    $classes = array( $class );

		    /* list alignment */
		    $list_align_tablet = $list_align_tablet == 'inherit' ? $list_align : $list_align_tablet;
		    $list_align_mobile = $list_align_mobile == 'inherit' ? $list_align_tablet : $list_align_mobile;

			$classes[] = 'bklyn-fancy-list-' . $list_align;
			$classes[] = 'bklyn-fancy-list-tablet-' . $list_align_tablet;
			$classes[] = 'bklyn-fancy-list-mobile-' . $list_align_mobile;

		    /* unique listz ID */
		    $id = ut_get_unique_id("ut_fl_", true);

		    $css_style = '<style class="bklyn-inline-styles" type="text/css">';

		    if( !empty( $list_font_size ) ) {
			    $css_style .= '#' . $id . '.bklyn-fancy-list { font-size: ' . $list_font_size . 'px !important; }';
		    }

		    if( !empty( $list_line_height ) ) {
			    $css_style .= '#' . $id . '.bklyn-fancy-list { line-height: ' . $list_line_height . 'px; }';
		    }

		    if( !empty( $list_margin_bottom ) ) {
			    $css_style .= '#' . $id . ' .bklyn-list li:not(:last-child) { margin-bottom: ' . $list_margin_bottom . 'px; }';
		    }

		    if( !empty( $list_icon_font_size ) ) {
			    $css_style .= '#' . $id . '.bklyn-fancy-list li .fa { font-size: ' . $list_icon_font_size . 'px; }';
			    // $css_style .= '#' . $id . '.bklyn-fancy-list li .fa { width: ' . $list_icon_font_size * 2 . 'px; }';
		    }

		    if( $list_style_type == 'icons' ) {

			    if( !empty( $icon_color ) ) {
				    $css_style .= '#' . $id . ' .fa { color: ' . $icon_color . '; }';
				    $css_style .= '#' . $id . ' a .fa { color: ' . $icon_color . '; }';
				    $css_style .= '#' . $id . ' a:visited .fa { color: ' . $icon_color . '; }';
			    }

			    if( !empty( $text_color ) ) {
				    $css_style .= '#' . $id . ' { color: ' . $text_color . '; }';
				    $css_style .= '#' . $id . ' a { color: ' . $text_color . '; }';
				    $css_style .= '#' . $id . ' a:visited { color: ' . $text_color . '; }';
			    }

			    if( !empty( $icon_color_hover ) ) {
				    $css_style .= '#' . $id . ' a:hover .fa { color: ' . $icon_color_hover . '; }';
				    $css_style .= '#' . $id . ' a:focus .fa { color: ' . $icon_color_hover . '; }';
				    $css_style .= '#' . $id . ' a:active .fa { color: ' . $icon_color_hover . '; }';

			    }

			    if( !empty( $text_color_hover ) ) {
				    $css_style .= '#' . $id . ' a:hover { color: ' . $text_color_hover . '; }';
				    $css_style .= '#' . $id . ' a:focus { color: ' . $text_color_hover . '; }';
				    $css_style .= '#' . $id . ' a:active { color: ' . $text_color_hover . '; }';
			    }

		    } else {

			    if( !empty( $icon_color ) ) {
				    $css_style .= '#' . $id . ' li { color: ' . $icon_color . '; }';
			    }

			    $body_font_color = function_exists('ot_get_option') && ot_get_option('ut_body_font_color') ? ot_get_option('ut_body_font_color') : '';
			    $text_color = !empty( $text_color ) ? $text_color : $body_font_color;

			    if( !empty( $text_color ) ) {
				    $css_style .= '#' . $id . ' li span { color: ' . $text_color . '; }';
				    $css_style .= '#' . $id . ' a { color: ' . $text_color . '; }';
				    $css_style .= '#' . $id . ' a:visited { color: ' . $text_color . '; }';
			    }

			    if( !empty( $text_color_hover ) ) {
				    $css_style .= '#' . $id . ' a:hover span { color: ' . $text_color_hover . '; }';
			    	$css_style .= '#' . $id . ' a:hover { color: ' . $text_color_hover . '; }';
				    $css_style .= '#' . $id . ' a:focus { color: ' . $text_color_hover . '; }';
				    $css_style .= '#' . $id . ' a:active { color: ' . $text_color_hover . '; }';
			    }

		    }

		    if( !empty( $list_style_type ) && $list_style_type != 'icons' ) {

			    $css_style .= '#' . $id . ' ul { list-style-type: ' . $list_style_type . '; }';

		    }

		    $css_style.= '</style>';


		    // List Classes
		    $list_classes   = array();
		    $list_classes[] = $list_style_type == 'none' || $list_style_type == 'icons' ? 'bklyn-list-style-none' : '';

		    if( $animate_list == 'on' ) {

			    $list_classes[] = 'bklyn-fancy-list-animated';

		    }

		    /* start output */
		    $output = '';

		    $output .= ut_minify_inline_css( $css_style );

		    if( !empty( $values ) && is_array( $values ) ) {

			    $output .= '<div id="' . esc_attr( $id) . '" class="bklyn-fancy-list ' . implode( ' ', $classes ) . '">';

			    $output .= '<ul class="bklyn-list ' . implode( ' ', $list_classes ) . '" data-appear-top-offset="almost">';

			    foreach( $values as $value ) {

				    $flex_align = array(
					    'top'    => 'top',
					    'middle' => 'center',
					    'bottom' => 'bottom'
				    );

				    $li_classes = array( 'bklyn-list-item-space-' . $list_icon_text_space );

				    if( $list_style_type == 'icons' && !empty( $value['icon'] ) ) {

					    $li_classes[] = 'bklyn-fancy-list-item-has-icon';
					    $li_classes[] = 'bklyn-fancy-list-item-has-icon-' . $flex_align[$list_icon_vertical_align];

				    }

				    $output .= '<li class="' . implode(" ", $li_classes ) . '">';

				    if( $list_style_type != 'icons' ) {

					    $output .= '<span>';

				    }

				    if( isset( $value['is_link'] ) && $value['is_link'] && !empty( $value['link'] ) ) {

					    $link = vc_build_link( $value['link'] );

					    $url    = !empty( $link['url'] )    ? $link['url'] : '';
					    $target = !empty( $link['target'] ) ? $link['target'] : '_self';
					    $title  = !empty( $link['title'] )  ? $link['title'] : '';
					    $rel    = !empty( $link['rel'] )    ? 'rel="' . esc_attr( trim( $link['rel'] ) ) . '"' : '';

					    $output .= '<a title="' . esc_attr( $title ) . '" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '" ' . $rel . '>';

				    }

				    if( $list_style_type == 'icons' && !empty( $value['icon'] ) ) {
					    $output .= '<i class="' . esc_attr( $value['icon'] ) . '"></i>';
				    }

				    if( $list_style_type == 'icons' && !empty( $value['icon'] ) ) {
					    $output .= '<div class="bklyn-fancy-list-content">';
				    }

				    if( !empty( $value['title'] ) ) {

					    if( !empty( $value['title_is_bold'] ) && filter_var( $value['title_is_bold'], FILTER_VALIDATE_BOOLEAN ) ) {

						    $output .= '<span style="font-weight: bold">' . $value['title'] . '</span>';

					    } else {

						    $output .= '<span>' . $value['title'] . '</span>';

					    }

				    }

				    if( !empty( $value['title_extend'] ) ) {

					    $output .= '<br />';

					    if( !empty( $value['title_extend_is_bold'] ) && filter_var( $value['title_extend_is_bold'], FILTER_VALIDATE_BOOLEAN ) ) {

						    $output .= '<span style="font-weight: bold">' . $value['title_extend'] . '</span>';

					    } else {

						    $output .= '<span>' . $value['title_extend'] . '</span>';

					    }

				    }

				    if( $list_style_type == 'icons' && !empty( $value['icon'] ) ) {
					    $output .= '</div>';
				    }

				    if( isset( $value['is_link'] ) && $value['is_link'] ) {

					    $output .= '</a>';

				    }

				    if( $list_style_type != 'icons' ) {

					    $output .= '</span>';

				    }

				    $output .= '</li>';

			    }

			    $output .= '</ul>';

			    $output .= '</div>';

		    }

		    return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>';

	    }
            
    }

}

new UT_Fancy_list;