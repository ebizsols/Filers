<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Content_Block' ) ) {
	
    class UT_Content_Block {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_content_block';

            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Content Block', 'ut_shortcodes' ),
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/section.png',
                    'base'            => $this->shortcode,
                    'category'        => 'Structual',
                    'class' 		  => 'ut-content-block vc_main-sortable-element ut-structual-module',
                    'is_container' 	  => true,
                    'js_view' 		  => 'VcSectionView',
                    'as_parent' => array(
                        'only' => '', // no childs
                    ),
                    'as_child' => array(
                        'only' => '', // only root
                    ),
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Content Block', 'ut_shortcodes' ),
                            'param_name'        => 'id',
                            'admin_label'       => true,
                            'value'             => ut_get_content_blocks()
                        ),

                    )

                )

            );
        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
				'id'	=>	''	
            ), $atts ) ); 

            if( empty( $id ) ) {
            	return false;
            }

            // get block content
	        $cblock = get_post( $id );

            // get block css
	        $cblock_custom_css      = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
            $cblock_custom_post_css = get_post_meta( $id, '_wpb_post_custom_css', true );

            $custom_css = '';

	        if( $cblock_custom_css || $cblock_custom_post_css ) {

	        	// echo before content to avoid p tag wrapping by filter
                $custom_css = '<style>';

		            if( $cblock_custom_css ) {

                        $custom_css .= $cblock_custom_css;

                    }

                    if( $cblock_custom_post_css ) {

                        $custom_css .= $cblock_custom_post_css;

                    }

                $custom_css .= '</style>';

	        }

	        echo $custom_css;

	        if( isset( $cblock->post_content ) ) {

		        return apply_filters( 'the_content', $cblock->post_content );

	        } else {

	        	return false;

	        }
        
        }
            
    }

}

new UT_Content_Block;


/**
 * WPBakery WPBakery Page Builder content block
 *
 * @package WPBakeryPageBuilder
 *
 */
class WPBakeryShortCode_UT_Content_Block extends WPBakeryShortCodesContainer {

	public function containerHtmlBlockParams( $width, $i ) {

		return 'class="vc_section_container"';

	}

	/**
	 * @param $settings
	 */
	public function __construct( $settings ) {

		parent::__construct( $settings );

	}

	public function cssAdminClass() {

		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? ' wpb_sortable' : ' ' . $this->nonDraggableClass );
		return 'wpb_' . $this->settings['base'] . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' );

	}

	public function getColumnControls( $controls = 'full', $extended_css = '' ) {
		$controls_start = '<div class="vc_controls vc_controls-visible controls_column' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';

		$output = '<div class="vc_controls vc_controls-row controls_row vc_clearfix">';
		$controls_end = '</div>';
		//Create columns
		$controls_move = ' <a class="vc_control column_move vc_column-move" href="#" title="' . __( 'Drag row to reorder', 'js_composer' ) . '" data-vc-control="move"><i class="vc-composer-icon vc-c-icon-dragndrop"></i></a>';

		// @United
		$controls_move .= ' <a class="vc_control column_move_up vc_column-move-up" href="#" title="' . __( 'Move Section Up', 'js_composer' ) . '" data-vc-control="move-up"><i class="fa fa-caret-up" aria-hidden="true"></i></a>';
		$controls_move .= ' <a class="vc_control column_move_down vc_column-move-down" href="#" title="' . __( 'Move Section Down', 'js_composer' ) . '" data-vc-control="move-down"><i class="fa fa-caret-down" aria-hidden="true"></i></a>';
		$controls_move .= ' <a class="vc_control column_move_up vc_column-move-top" href="#" title="' . __( 'Move Section To Top', 'js_composer' ) . '" data-vc-control="move-up"><i class="fa fa-angle-double-up" aria-hidden="true"></i></a>';
		$controls_move .= ' <a class="vc_control column_move_down vc_column-move-bottom" href="#" title="' . __( 'Move Section To Bottom', 'js_composer' ) . '" data-vc-control="move-down"><i class="fa fa-angle-double-down" aria-hidden="true"></i></a>';

		$moveAccess = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();

		if ( ! $moveAccess ) {
			$controls_move = '';
		}

		$controls_add = ' <a class="vc_control column_add vc_column-add" href="#" title="' . __( 'Add column', 'js_composer' ) . '" data-vc-control="add"><i class="vc-composer-icon vc-c-icon-add"></i></a>';
		$controls_delete = '<a class="vc_control column_delete vc_column-delete" href="#" title="' . __( 'Delete this content block', 'js_composer' ) . '" data-vc-control="delete"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></a>';
		$controls_edit = ' <a class="vc_control column_edit vc_column-edit" href="#" title="' . __( 'Edit this content block', 'js_composer' ) . '" data-vc-control="edit"><i class="vc-composer-icon vc-c-icon-mode_edit"></i></a>';
		$controls_clone = ' <a class="vc_control column_clone vc_column-clone" href="#" title="' . __( 'Clone this content block', 'js_composer' ) . '" data-vc-control="clone"><i class="vc-composer-icon vc-c-icon-content_copy"></i></a>';
		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );
		$row_edit_clone_delete = '<span class="vc_row_edit_clone_delete">';

		if ( 'add' === $controls ) {
			return $controls_start . $controls_add . $controls_end;
		}
		if ( $allAccess ) {

			$row_edit_clone_delete .= $controls_delete . $controls_clone . $controls_edit;

		} elseif ( $editAccess ) {

			$row_edit_clone_delete .= $controls_edit;

		}

		$row_edit_clone_delete .= '</span>';

		if ( $allAccess ) {
			$output .= $controls_move . $controls_add . $row_edit_clone_delete . $controls_end;
		} elseif ( $editAccess ) {
			$output .= $row_edit_clone_delete . $controls_end;
		} else {
			$output .= $row_edit_clone_delete . $controls_end;
		}

		return $output;
	}

	public function contentAdmin( $atts, $content = null ) {

		$width = '';
		$atts = shortcode_atts( $this->predefined_atts, $atts );

		$output = '';

		$column_controls = $this->getColumnControls();

		$output .= '<div data-element_type="' . $this->settings['base'] . '" class="' . $this->cssAdminClass() . ' ut_vc_section_move">';
		$output .= str_replace( '%column_size%', 1, $column_controls );
		$output .= '<div class="wpb_element_wrapper">';

		if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {

			$markup = $this->settings['custom_markup'];
			$output .= $this->customMarkup( $markup );

		} else {

			$output .= '<div ' . $this->containerHtmlBlockParams( $width, 1 ) . '></div>';

		}

		if ( isset( $this->settings['params'] ) ) {

			$inner = '';

			foreach ( $this->settings['params'] as $param ) {

				if ( ! isset( $param['param_name'] ) ) {
					continue;
				}

				$param_value = isset( $atts[ $param['param_name'] ] ) ? $atts[ $param['param_name'] ] : '';
				if ( is_array( $param_value ) ) {
					// Get first element from the array
					reset( $param_value );
					$first_key = key( $param_value );
					$param_value = $param_value[ $first_key ];
				}
				$inner .= $this->singleParamHtmlHolder( $param, $param_value );
			}

			$output .= $inner;

		}

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
}