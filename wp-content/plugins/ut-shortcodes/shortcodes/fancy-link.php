<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Fancy_Link' ) ) {
    
    class UT_Fancy_Link {

        /**
         * Shortcode
         */

        private $shortcode;

        function __construct() {

            /* shortcode base */
            $this->shortcode = 'ut_fancy_link';

            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );
            
        }
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Fancy Link', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'More than just a plain link.', 'ut_shortcodes' ),
                    'base'            => 'ut_fancy_link',
                    // 'icon'         => 'fa fa-link ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/fancy-link.png',
                    'category'        => 'Information',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Link Text', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'group'             => 'General',
                            'param_name'        => 'content',
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Lightbox?', 'unitedthemes' ),
                            'param_name'        => 'add_lightbox',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' ) => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'What do you like to show inside your lightbox?', 'ut_shortcodes' ),
                            'param_name'        => 'lightbox_content',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Image', 'ut_shortcodes' ) => 'image',
                                esc_html__( 'Youtube or Vimeo', 'ut_shortcodes' ) => 'video',
                                esc_html__( 'Iframe', 'ut_shortcodes' ) => 'iframe',
                            ),
                            'dependency'        => array(
                                'element' => 'add_lightbox',
                                'value'   => 'yes',
                            ),
                        ),
                        array(
                            'type'              => 'attach_image',
                            'heading'           => esc_html__( 'Image for Lightbox', 'ut_shortcodes' ),
                            'param_name'        => 'lightbox_image',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'lightbox_content',
                                'value'   => 'image',
                            ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Video for Lightbox', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Needs to be Vimeo or Youtube! If you are using vimeo please use the following link markup: https://vimeo.com/XXXXXXX' , 'ut_shortcodes' ),
                            'param_name'        => 'lightbox_video',
                            'group'             => 'General',
                            'admin_label'       => true,
                            'dependency'        => array(
                                'element' => 'lightbox_content',
                                'value'   => 'video',
                            ),
                        ),
                        array(
                            'type'              => 'iframe',
                            'heading'           => esc_html__( 'Iframe SRC', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Only insert the iframe src not the entire iframe code! Make sure, that your iframe source allow embedding, otherwise the lightbox stays empty.' , 'ut_shortcodes' ),
                            'param_name'        => 'lightbox_iframe',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'lightbox_content',
                                'value'   => 'iframe',
                            ),
                        ),
                        array(
                            'type'              => 'vc_link',
                            'heading'           => esc_html__( 'Link', 'ut_shortcodes' ),
                            'param_name'        => 'link',
                            'group'             => 'General',
                            'dependency'        => array(
                                'element' => 'add_lightbox',
                                'value'   => 'no',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Link is a section on this page?', 'ut_shortcodes' ),
                            'param_name'        => 'section',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' )  => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            ),
                            'dependency'        => array(
                                'element' => 'add_lightbox',
                                'value'   => 'no',
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Link Color', 'ut_shortcodes' ),
                            'param_name'        => 'link_color',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-3',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Line Color', 'ut_shortcodes' ),
                            'param_name'        => 'line_color',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-3',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Link Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'link_hover_color',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-3',
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Line Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'line_hover_color',
                            'group'             => 'General',
                            'edit_field_class'  => 'vc_col-sm-3',
                        ),

                        // Title Font Settings
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                            'param_name'        => 'font_source',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'Theme Default', 'ut_shortcodes' )  => 'default',
                                esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                                esc_html__( 'Google Font', 'ut_shortcodes' )    => 'google'
                            ),
                        ),
                        array(
                            'type'              => 'google_fonts',
                            'param_name'        => 'google_fonts',
                            'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                            'group'             => 'Font Settings',
                            'settings'          => array(
                                'fields' => array(
                                    'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                                    'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                                ),
                            ),
                            'dependency'        => array(
                                'element'           => 'font_source',
                                'value'             => 'google',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Websafe Fonts', 'ut_shortcodes' ),
                            'param_name'        => 'websafe_fonts',
                            'group'             => 'Font Settings',
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
                                'element'           => 'font_source',
                                'value'             => 'websafe',
                            ),

                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Font Size', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional) value in px or em, eg "20px". default: 1.4em' , 'ut_shortcodes' ),
                            'param_name'        => 'font_size',
                            'group'             => 'Font Settings'
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Title Text Transform', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional)' , 'ut_shortcodes' ),
                            'param_name'        => 'text_transform',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                                esc_html__( 'capitalize' , 'ut_shortcodes' )            => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' )              => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' )              => 'lowercase'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Please keep in mind, that the selected font needs to support the font weight.', 'ut_shortcodes' ),
                            'param_name'        => 'font_weight',
                            'group'             => 'Font Settings',
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
                            'dependency'        => array(
                                'element'           => 'font_source',
                                'value'             => array('websafe','default'),
                            ),
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'letter_spacing',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '-0.2',
                                'max'       => '0.2',
                                'step'      => '0.01',
                                'unit'      => 'em'
                            ),
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'group'             => 'General',
                            'param_name'        => 'class',
                        ),

                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css_editor',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),


                    ),

                )

            ); // end mapping
        
        }
        
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
                'link_color'        => '',
                'link_hover_color'  => '',
                'line_color'        => '',
                'line_hover_color'  => '',
                'link'              => '',
                'url'               => '#',         // deprecated
                'target'            => '_self',    // deprecated
                'section'           => 'no',
                
                // lightbox
                'add_lightbox'       => 'no',
                'lightbox_content'   => 'image',
                'lightbox_video'     => '',
                'lightbox_image'     => '',
                'lightbox_iframe'    => '',
                
                // Font Settings
                'font_size'      => '',
                'font_weight'    => '',
                'text_transform' => '',
                'letter_spacing' => '',
                'font_source'    => 'theme',
                'google_fonts'   => '',
                'websafe_fonts'  => '',
                
                'class'            => '',
                'css_editor'       => ''

            ), $atts ) ); 
            
            $classes    = array();
            $classes[]  = $class;
            
            $output = '';
            
            
            $ut_font_css = false;
            
            /* initialize google font */
            if( $font_source && $font_source == 'google' ) {
                
                 $ut_google_font     = new UT_VC_Google_Fonts( $atts, 'google_fonts', 'ut_fancy_link' );
                 $ut_font_css        = $ut_google_font->get_google_fonts_css_styles();
                        
            }
            
            $ut_font_css = is_array( $ut_font_css ) ? implode( '', $ut_font_css ) : $ut_font_css;
            
            /* unique ID */
            $id = uniqid("ut_fancy_link_");
            
            /* custom css */
            $css = '';
            
            if( $ut_font_css ) {                
               $css .= '#' . $id . ' a { ' . $ut_font_css . ' }'; 
            }
            
            if( $font_source && $font_source == 'websafe' ) {
                $css .= '#' . $id . ' a { font-family: ' . get_websafe_font_css_family( $websafe_fonts ) . '; }';                
            }
            
            if( $font_weight ) {
                $css.= '#' . $id . ' a { font-weight:' . $font_weight . '; }';
            }

            if( $font_size ) {
                $css.= '#' . $id . ' a { font-size:' . $font_size . '; }';
            }
            
            if( $letter_spacing ) {
                $css.= '#' . $id . ' a { letter-spacing:' . $letter_spacing . 'em; }';
            }
            
            if( $text_transform ) {
                $css.= '#' . $id . ' a { text-transform:' . $text_transform . '; }';
            }
                
            if( $link_color ) {                
                
               $css .= '#' . $id . ' a { color: ' . $link_color . ' }'; 
                
            }
            
            if( $link_hover_color ) {                
                
                $css .= '#' . $id . ' a:hover { color: ' . $link_hover_color . ' }'; 
                
            }
            
            if( $line_color ) {
                
                $css .= '#' . $id . ' a::before { background: ' . $line_color . ' }';
                $css .= '#' . $id . ' a::after { background: ' . $line_color . ' }';
                
            }
            
            if( $line_hover_color ) {
                
                $css .= '#' . $id . ' a:hover::before { background: ' . $line_hover_color . ' }';
                $css .= '#' . $id . ' a:hover::after { background: ' . $line_hover_color . ' }';
                
            }
            
            // attach css
            if( !empty( $css ) ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css . '</style>' );
            }
            
            // link settings
            if( function_exists('vc_build_link') && $link ) {
                
                $fancy_link = vc_build_link( $link );
                
                // assign link
                $link = !empty( $fancy_link['url'] ) ? $fancy_link['url'] : '#';
                
            } else {
                
                $link = $url;
            
            }
            
            $target = !empty( $fancy_link['target'] ) ? $fancy_link['target'] : $target;
            $title  = !empty( $fancy_link['title'] )  ? $fancy_link['title'] : '';
            $rel    = !empty( $fancy_link['rel'] )    ? 'rel="' . esc_attr( trim( $fancy_link['rel'] ) ) . '"' : '';
            
            // lightbox settings
            $lightbox = NULL;
            
            if( $add_lightbox == 'yes' ) {
                $lightbox = 'ut-lightbox';
            }
            
            $data_iframe = '';
            
            if( $add_lightbox == 'yes' ) {
                
                if( $lightbox_content == 'image' ) {
                      
                    $link = wp_get_attachment_url( $lightbox_image );
                    
                }
                
                if( $lightbox_content == 'video' ) {
                    
                    $link = esc_url( $lightbox_video );
                        
                }
                
                if( $lightbox_content == 'iframe' ) {
                    
                    $link = esc_url( $lightbox_iframe );        
                    $data_iframe = 'data-iframe="true"';
                    
                }                                
                
            }
            
            // scroll to section
            if( $add_lightbox == 'no' && $section == 'yes' ) {
                $target     = '_self';  
                $classes[]  = 'ut-scroll-to-section';
            }
            
            $output .= '<span id="' . esc_attr( $id ) . '" class="cta-btn cl-effect-18 ' . implode(' ', $classes ) . '"><a ' . $data_iframe . ' class="cl-effect-18 ' . $lightbox . '" title="' . esc_attr( $title ) . '" target="' . esc_attr( $target ) . '" href="' . esc_url( $link ) . '" ' . $rel . '>' . do_shortcode( $content ) . '</a></span>';
            return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_editor, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>';


        }
            
    }

}

new UT_Fancy_Link;