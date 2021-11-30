<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Skewed_Image_Border' ) ) {

    class UT_Skewed_Image_Border {

        private $shortcode;

        function __construct() {

            /* shortcode base */
            $this->shortcode = 'ut_skewed_image_border';

            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );

        }

        function ut_map_shortcode() {

            vc_map(
                array(
                    'name' => esc_html__('Skewed Images', 'ut_shortcodes'),
                    'description' => esc_html__('A visual border consisting of up to 8 images.', 'ut_shortcodes'),
                    'base' => $this->shortcode,
                    'category' => 'Media',
                    'icon' => UT_SHORTCODES_URL . '/admin/img/vc_icons/image-gallery.png',
                    'class' => 'ut-vc-icon-module ut-media-module',
                    'content_element' => true,
                    'params' => array(

                        array(
                            'type' => 'attach_images',
                            'heading' => esc_html__('Border Images', 'ut_shortcodes'),
                            'description' => esc_html__('Only supports a maximum of 8 Images.', 'ut_shortcodes'),
                            'param_name' => 'gallery',
                            'admin_label' => true
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Separate Images with Border?', 'ut_shortcodes' ),
                            'param_name'        => 'activate_border',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
                            ),
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'border_color',
                            'dependency'        => array(
                                'element' => 'activate_border',
                                'value'   => 'on',
                            )
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Border Width', 'ut_shortcodes' ),
                            'param_name'        => 'border_width',
                            'value'             => array(
                                'default' => '1',
                                'min'     => '1',
                                'max'     => '10',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'activate_border',
                                'value'   => 'on',
                            )
                        ),

                        array(
                            'type' => 'textfield',
                            'heading' => __('Margin Bottom', 'ut_shortcodes'),
                            'param_name' => 'margin_bottom',
                        ),

                    )
                )
            );



        }

        function create_placeholder_svg( $width , $height ){

            // fallback values
            $width = empty( $width ) ? '800' : $width;
            $height = empty( $height ) ? '600' : $height;

            return 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\' viewBox%3D\'0 0 ' . esc_attr( $width ) . ' ' . esc_attr( $height ) . '\'%2F%3E';

        }


        function ut_create_shortcode( $atts, $content = NULL ) {

            /**
             * @var $gallery
             * @var $margin_bottom
             * @var $activate_border
             * @var $border_color
             * @var $border_width
             * @var $class
             * @var $css
             */
            extract( shortcode_atts( array (
                'gallery'           => '',
                'activate_border'   => '',
                'border_color'      => '',
                'border_width'      => '1',
                'class'             => '',
                'margin_bottom'     => '',
                'css'               => '',
            ), $atts ) );

            $classes = array( $class );

            $gallery = explode( ',' , $gallery );

            $image_sizes = count( $gallery ) > 4 ? array( 800, 600 ) : array( 1600, 900 );

            // CSS
            $id = uniqid("ut_si_");

            $css_style = '';

            if( $margin_bottom != '' ) {

                $css_style .= '#' . $id . ' { margin-bottom: ' . $margin_bottom . ' }';

            }

            if( $activate_border && $border_color ) {

                $css_style .= '#' . $id . '.ut-skewed-image-border-separated .ut-skewed-image-border-slice { border-color: ' . $border_color . '; border-width: ' . $border_width . 'px }';
                $classes[] = 'ut-skewed-image-border-separated';

            }

            // start output
            $output = '';

            // attach CSS
            if( !empty( $css_style ) ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );
            }

            if( !empty( $gallery ) && is_array( $gallery ) ) {

                $gallery = array_slice( $gallery, 0, 8 );

                $output .= '<div id="' . esc_attr( $id ) . '" class="ut-skewed-image-border ' . implode( " ", $classes ) . '">';

                foreach( $gallery as $image ) {

                    $thumbnail = wp_get_attachment_image_src( $image, 'full' );
                    $thumbnail = ut_resize( $thumbnail[0], $image_sizes[0], $image_sizes[1], true, true, true );

                    $output .= '<div class="ut-skewed-image-border-slice">';

                        $output .= '<img class="ut-lazy" src="' . $this->create_placeholder_svg( $image_sizes[0], $image_sizes[1] ) . '" data-src="' . $thumbnail . '">';

                    $output .= '</div>';

                }

                $output .= '</div>';

            }

            return $output;


        }

    }

}

new UT_Skewed_Image_Border;


if ( class_exists( 'WPBakeryShortCode' ) ) {

    class WPBakeryShortCode_ut_skewed_image_border extends WPBakeryShortCode {

        /* add images to visual composer */
        public function singleParamHtmlHolder( $param, $value ) {

            $output = '';

            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';

            if ( 'gallery' === $param_name ) {

                $images_ids = empty( $value ) ? array() : explode( ',', trim( $value ) );
                $output .= '<ul class="attachment-thumbnails' . ( empty( $images_ids ) ? ' image-exists' : '' ) . '" data-name="' . $param_name . '">';
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