<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Video_Shortcode' ) ) {
	
    class UT_Video_Shortcode {
        
        private $shortcode;

        /**
         * Attributes
         */

        private $atts;

        /**
         * Image ID
         */

        private $image_id;


        function __construct() {
			
            // shortcode base
            $this->shortcode = 'ut_video_player';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
            // ajax requests
            add_action( 'wp_ajax_nopriv_unite_get_video_player', array( $this, 'get_video_player' ) );
            add_action( 'wp_ajax_unite_get_video_player', array( $this, 'get_video_player' ) );            
            
		}


        /**
         * Return the type based on the URL.
         *
         * @param $url
         *
         * @return array
         */

        protected function get_twitch_video_type( $url ) {

            if( stristr($url, 'clips.twitch.tv') ) {

                return array(
                    'type'  => 'clip',
                    'index' => 1,
                    'regex' => '/http[s]?:\/\/(?:www\.|clips\.)twitch\.tv\/([0-9a-zA-Z\-\_]+)\/?(chat\/?$|[0-9a-z\-\_]*)?/'
                );

            }

            if( stristr($url, '/clip/') ) {

                return array(
                    'type'  => 'clip',
                    'index' => 3,
                    'regex' => '/http[s]?:\/\/(?:www\.|clips\.)twitch\.tv\/([0-9a-zA-Z\-\_]+)\/([0-9a-zA-Z\-\_]+)\/([0-9a-zA-Z\-\_]+)?/'
                );

            }

            if( stristr($url, '/videos/') ) {

                return array(
                    'type'  => 'video',
                    'regex' => '/http[s]?:\/\/(?:www\.|clips\.)twitch\.tv\/([0-9a-zA-Z\-\_]+)\/?(chat\/?$|[0-9a-z\-\_]*)?/'
                );

            }

            return array();

        }


        /**
         * Render Video Player.
         *
         * @since    1.0.0
         */

        public function get_video_player() {
        
            // get video to check
            $video = esc_url( $_POST['video'] );
            
            // needed variables 
            $embed_code = NULL;
            
            // check if youtube has been used 
            preg_match('~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i', trim($video) , $matches );        
                
            if( !empty( $matches[1] ) ) {
                $embed_code = '<iframe height="315" width="560" src="//www.youtube.com/embed/'.trim($matches[1]).'?wmode=transparent&vq=hd720&autoplay=1" wmode="Opaque" allowfullscreen="" frameborder="0"></iframe>';          
            }

            // check for twitch
            $twitch_type = $this->get_twitch_video_type( trim($video) );

            if( !empty( $twitch_type ) ) {

                preg_match( $twitch_type['regex'], trim($video), $matches );

                if( !empty( $matches[$twitch_type['index']] ) ) {

                    $channelName = esc_attr( $matches[$twitch_type['index']] );

                    switch( $twitch_type['type'] ) {

                        case 'clip':
                            $src         = 'https://clips.twitch.tv/embed?clip=' . $channelName . '&autoplay=false';
                            $attr        = 'scrolling="no" frameborder="0" allowfullscreen="true"';
                            break;

                        case 'video':
                            $channelName = $matches[2];
                            $src         = 'https://player.twitch.tv/?video=' . $channelName;
                            $attr        = 'scrolling="no" frameborder="0" allowfullscreen="true"';
                            break;

                    }

                    if( !empty( $src ) ) {

                        $embed_code = '<iframe src="' . $src . '" ' . $attr . '></iframe>';

                    }

                }

            }

            // no video found so far, try to create a player 
            if( empty($embed_code) ) {
                
                $video_embed = wp_oembed_get(trim($video));
                if( !empty($video_embed) ) {
                    $embed_code = $video_embed;            
                }
                
            }
            
            // still no video found , let's try to apply a shortcode
            if( empty( $embed_code ) ) {
                $embed_code = do_shortcode(stripslashes($video));
            
            }
             
            echo $embed_code;
            
            die(1);
        
        }
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Video Player', 'ut_shortcodes' ),
                    'description' 	  => esc_html__( 'An ajax powered video player for a faster website loading and with some eyecandy features.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-play ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/video-player.png',
                    'category'        => 'Media',
                    'class'           => 'ut-vc-icon-module ut-media-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Video URL', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'only the video URL eg "http://vimeo.com/*", "https://youtu.be/*" or "https://clips.twitch.tv/*" or path to *.mp4 file.', 'ut_shortcodes' ),
                            'param_name'        => 'url',
                            'group'             => 'General',
                            'admin_label'       => true
                        ),
	                    array(
		                    'type'             => 'filepicker',
		                    'heading'          => __( 'or select Video', 'ut_shortcodes' ),
		                    'param_name'       => 'file_url',
		                    'group'            => 'General',

	                    ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Max. Width', 'ut_shortcodes' ),
                            'param_name'        => 'maxwidth',
                            'group'             => 'General',
                            'value'             => array(
                                'default' => '100',
                                'min'     => '50',
                                'max'     => '100',
                                'step'    => '1',
                                'unit'    => '%'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Video Alignment', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-4',
                            'param_name'        => 'align',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'none', 'ut_shortcodes' )   => '',
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Video Alignment Tablet', 'ut_shortcodes' ),
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'param_name'        => 'align_tablet',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Video Alignment Mobile', 'ut_shortcodes' ),
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'param_name'        => 'align_mobile',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger', 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'center', 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Video Play Location', 'ut_shortcodes' ),
		                    'param_name'        => 'location',
		                    'group'             => 'General',
		                    'value'             => array(
			                    esc_html__( 'play video inline', 'ut_shortcodes' )   => 'inline',
			                    esc_html__( 'play video in lightbox', 'ut_shortcodes' ) => 'lightbox'
		                    ),
	                    ),


                        /* Video Poster */
                        array(
                            'type'              => 'attach_image',
                            'heading'           => esc_html__( 'Poster Image', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(required) A poster image will be displayed until the user decides to play the video. This saves bandwidth and increases the page speed! If you use Youtube or Vimeo this field is optional since the script will use the broadcaster thumbnail as fallback.', 'ut_shortcodes' ),
                            'param_name'        => 'poster',
                            'group'             => 'Poster',
                        ),

                        /* Video Caption */
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Video Caption', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional) Will be displayed next to the video play icon or below.', 'ut_shortcodes' ),
                            'param_name'        => 'caption',
                            'group'             => 'Caption',
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Video Caption Font Weight', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'caption_font_weight',
                            'group'             => 'Caption',
                            'value'             => array(
                                esc_html__( 'bold' , 'ut_shortcodes' )   => 'bold',
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
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
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Video Caption Letter Spacing', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'caption_letter_spacing',
                            'group'             => 'Caption',
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
                            'heading'           => esc_html__( 'Video Caption Position', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'caption_position',
                            'group'             => 'Caption',
                            'value'             => array(
                                esc_html__( 'default (next to playicon)' , 'ut_shortcodes' ) => 'default',
                                esc_html__( 'bottom (below playicon)' , 'ut_shortcodes' ) => 'bottom',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Video Caption Spacing', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'caption_spacing',
                            'group'             => 'Caption',
                            'value'             => array(
                                'default'   => '20',
                                'min'       => '10',
                                'max'       => '60',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                        ),

                        // Appearance
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Video Caption Text Color', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'caption_color',
                            'group'             => 'Appearance',
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Overlay Color', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'overlay_color',
                            'group'             => 'Appearance',
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Video Box Shadow?', 'ut_shortcodes' ),
                            'param_name'        => 'video_shadow',
                            'group'             => 'Appearance',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' )   => 'off',
                                esc_html__( 'yes, please!', 'ut_shortcodes' )   => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Video Box Shadow Style', 'ut_shortcodes' ),
                            'param_name'        => 'video_shadow_style',
                            'group'             => 'Appearance',
                            'value'             => array(
                                esc_html__( 'Default', 'ut_shortcodes' )   => 'default',
                                esc_html__( 'Box Shadow Style 1', 'ut_shortcodes' )   => 'ut-box-shadow-1',
                                esc_html__( 'Box Shadow Style 2', 'ut_shortcodes' )   => 'ut-box-shadow-2',
                                esc_html__( 'Box Shadow Style 3', 'ut_shortcodes' )   => 'ut-box-shadow-3',
                                esc_html__( 'Box Shadow Style 4', 'ut_shortcodes' )   => 'ut-box-shadow-4',
                                esc_html__( 'Box Shadow Style 5', 'ut_shortcodes' )   => 'ut-box-shadow-5',
                                esc_html__( 'Box Shadow Style 6', 'ut_shortcodes' )   => 'ut-box-shadow-6',
                                esc_html__( 'Box Shadow Style 7', 'ut_shortcodes' )   => 'ut-box-shadow-7',
                                esc_html__( 'Box Shadow Style 8', 'ut_shortcodes' )   => 'ut-box-shadow-8'
                            ),
                            'dependency'        => array(
                                'element' => 'video_shadow',
                                'value'   => 'on',
                            ),
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Shadow Canvas Color', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Should be the same as the background color the shadows displays on.', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'shadow_canvas_color',
                            'group'             => 'Appearance',
                            'dependency'        => array(
                                'element'   => 'video_shadow',
                                'value'     => array('on'),
                            )
                        ),

                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Video Box Shadow Color', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'shadow_color',
                            'group'             => 'Appearance',
                            'dependency'        => array(
                                'element' => 'video_shadow',
                                'value'   => 'on',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Video Box Shadow Blur Radius', 'ut_shortcodes' ),
                            'param_name'        => 'shadow_blur_radius',
                            'group'             => 'Appearance',
                            'value'             => array(
                                'default'   => '20',
                                'min'       => '1',
                                'max'       => '40',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'video_shadow_style',
                                'value'   => 'default',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Show Video Border?', 'ut_shortcodes' ),
                            'param_name'        => 'video_border',
                            'group'             => 'Appearance',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Border Color', 'ut_shortcodes' ),
                            'param_name'        => 'border_color',
                            'group'             => 'Appearance',
                            'dependency'        => array(
                                'element' => 'video_border',
                                'value'   => 'on',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Border Width', 'ut_shortcodes' ),
                            'param_name'        => 'border_width',
                            'group'             => 'Appearance',
                            'value'             => array(
                                'default'  	=> '1',
                                'min'   	=> '1',
                                'max'  	 	=> '10',
                                'step'  	=> '1',
                                'unit'  	=> ''
                            ),
                            'dependency'        => array(
                                'element' => 'video_border',
                                'value'   => 'on',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Video Box Padding', 'ut_shortcodes' ),
                            'param_name'        => 'video_padding',
                            'group'             => 'Appearance',
                            'value'             => array(
                                'default'   => '20',
                                'min'       => '0',
                                'max'       => '40',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'video_border',
                                'value'   => 'on',
                            ),
                        ),

	                    /* Rotation */
	                    array(
		                    'type'              => 'range_slider',
		                    'heading'           => esc_html__( 'Rotate Video?', 'ut_shortcodes' ),
		                    'description'       => esc_html__( 'Note: Depending on selected rotation, the video might cut off at the edges by row or section overflow. You can deactivate the overflow hidden option directly in row or section settings.', 'ut_shortcodes' ),
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
		                    'description'       => esc_html__( 'Note: Depending on selected rotation, the video might cut off at the edges by row or section overflow. You can deactivate the overflow hidden option directly in row or section settings.', 'ut_shortcodes' ),
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
		                    'description'       => esc_html__( 'Note: Depending on selected rotation, the video might cut off at the edges by row or section overflow. You can deactivate the overflow hidden option directly in row or section settings.', 'ut_shortcodes' ),
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


                        /* Play Icon */
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Display Play Icon?', 'ut_shortcodes' ),
		                    'param_name'        => 'play_icon',
		                    'group'             => 'Play Icon',
		                    'value'             => array(
			                    esc_html__( 'yes, please', 'ut_shortcodes' ) => 'on',
			                    esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off'
		                    ),
	                    ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Video Play Shape', 'ut_shortcodes' ),
                            'param_name'        => 'play_shape',
                            'group'             => 'Play Icon',
                            'value'             => array(
                                esc_html__( 'round', 'ut_shortcodes' )      => 'round',
                                esc_html__( 'square', 'ut_shortcodes' )     => 'square',
                                esc_html__( 'rectangle', 'ut_shortcodes' )  => 'rectangle'
                            ),
                            'dependency'        => array(
	                            'element' => 'play_icon',
	                            'value'   => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Video Play Icon Style', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'play_icon_style',
                            'group'             => 'Play Icon',
                            'value'             => array(
                                esc_html__( 'line', 'ut_shortcodes' )  => 'line',
                                esc_html__( 'solid', 'ut_shortcodes' ) => 'solid'
                            ),
                            'dependency'        => array(
	                            'element' => 'play_icon',
	                            'value'   => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Video Play Icon Size', 'ut_shortcodes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'play_icon_size',
                            'group'             => 'Play Icon',
                            'value'             => array(
                                esc_html__( 'normal', 'ut_shortcodes' ) => '',
                                esc_html__( 'large', 'ut_shortcodes' )  => 'large'
                            ),
                            'dependency'        => array(
	                            'element' => 'play_icon',
	                            'value'   => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Video Play Icon Background Color', 'ut_shortcodes' ),
                            'param_name'        => 'play_bg_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Play Icon',
                            'dependency'        => array(
	                            'element' => 'play_icon',
	                            'value'   => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'gradient_picker',
                            'heading'           => esc_html__( 'Video Play Icon Background Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'play_bg_color_hover',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Play Icon',
                            'dependency'        => array(
	                            'element' => 'play_icon',
	                            'value'   => 'on'
                            ),

                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Video Play Icon Color', 'ut_shortcodes' ),
                            'param_name'        => 'play_color',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Play Icon',
                            'dependency'        => array(
	                            'element' => 'play_icon',
	                            'value'   => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Video Play Icon Hover Color', 'ut_shortcodes' ),
                            'param_name'        => 'play_color_hover',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Play Icon',
                            'dependency'        => array(
	                            'element' => 'play_icon',
	                            'value'   => 'on'
                            ),
                        ),

                        // Pulse Effect
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Add Pulse Effect to Play Icon?', 'ut_shortcodes' ),
                            'param_name'        => 'icon_pulsate',
                            'group'             => 'Pulse Effect',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off',
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on'
                            ),
                            'dependency'        => array(
                                'element' => 'play_shape',
                                'value'   => 'round'
                            ),
                        ),

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Use Quick Options?', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Deactivate the Quick Options in order to be able to style each pulse individually.', 'ut_shortcodes' ),
                            'param_name'        => 'icon_pulsate_quick_options',
                            'group'             => 'Pulse Effect',
                            'value'             => array(
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on',
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'off'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate',
                                'value'   => 'on'
                            ),
                        ),

                        // Simple Mode
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line Default Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color',
                            'group'             => 'Pulse Effect',
                            'dependency'        => array(
                                'element' => 'icon_pulsate',
                                'value'   => 'on',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line Width', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Drag the handle to set the border width.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_quick_options',
                                'value'   => 'on',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( '"Double" requires a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_quick_options',
                                'value'   => 'on',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Amount', 'ut_shortcodes' ),
                            'param_name'        => 'icon_pulsate_amount_simple',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '4',
                                'step'      => '1',
                                'unit'      => ''
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_quick_options',
                                'value'   => 'on'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Amount', 'ut_shortcodes' ),
                            'param_name'        => 'icon_pulsate_amount_advanced',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '4',
                                'step'      => '1',
                                'unit'      => ''
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_quick_options',
                                'value'   => 'off'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Intensity', 'ut_shortcodes' ),
                            'param_name'        => 'icon_pulsate_intensity',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '25',
                                'min'       => '15',
                                'max'       => '50',
                                'step'      => '5',
                                'unit'      => ''
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate',
                                'value'   => 'on'
                            ),
                        ),

                        // Pulse 1
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 1 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_1',
                            'group'             => 'Pulse Effect',
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("1","2","3","4"),
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 1 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_1',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("1","2","3","4"),
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 1 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_1',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("1","2","3","4"),
                            ),
                        ),

                        // Pulse 2
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 2 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_2',
                            'group'             => 'Pulse Effect',
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("2","3","4"),
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 2 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_2',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("2","3","4"),
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 2 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_2',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("2","3","4"),
                            ),
                        ),

                        // Pulse 3
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 3 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_3',
                            'group'             => 'Pulse Effect',
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("3","4"),
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 3 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_3',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("3","4"),
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 3 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_3',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("3","4"),
                            ),
                        ),

                        // Pulse 4
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Pulse Line 4 Color', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_color_4',
                            'group'             => 'Pulse Effect',
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("4"),
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Pulse Line 4 Width', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_width_4',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                'default'   => '1',
                                'min'       => '1',
                                'max'       => '10',
                                'step'      => '1',
                                'unit'      => 'px'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("4"),
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Pulse Line 4 Style', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style "double" requires at least a line width of 3px.', 'ut_shortcodes' ),
                            'param_name'        => 'pulse_style_4',
                            'group'             => 'Pulse Effect',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'value'             => array(
                                esc_html__( 'solid' , 'ut_shortcodes' ) => 'solid',
                                esc_html__( 'dotted', 'ut_shortcodes' ) => 'dotted',
                                esc_html__( 'dashed', 'ut_shortcodes' ) => 'dashed',
                                esc_html__( 'double', 'ut_shortcodes' ) => 'double'
                            ),
                            'dependency'        => array(
                                'element' => 'icon_pulsate_amount_advanced',
                                'value'   => array("4"),
                            ),
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

	                    // CSS Editor
	                    array(
		                    'type'              => 'css_editor',
		                    'param_name'        => 'css',
		                    'group'             => 'Design Options'
	                    ),

                        // CSS
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'Design Options'
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



                    )

                )

            ); // end mapping
                

        
        }
        
        function create_keyframes( $keyframe_name, $scale ) {
            
            ob_start(); ?>
            
            @keyframes <?php echo $keyframe_name; ?> {
    
                0% {
                    -webkit-transform: scale(1);
                        -moz-transofrom: scale(1);
                            transform: scale(1);
                }
                50% {
                    opacity: 0.7;
                }
                75% {
                    opacity: 0.1;
                }
                100% {
                    opacity: 0;
                    -webkit-transform: scale(<?php echo $scale/10; ?>);
                        -moz-transofrom: scale(<?php echo $scale/10; ?>);
                            transform: scale(<?php echo $scale/10; ?>);
                }

            }

            <?php
            
            return ob_get_clean();
            
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

        public function create_glitch_effect( $poster_url ) {

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

            if( $glitch_effect == 'none' ) {
                return false;
            }

            $glitch_effect = new UT_Glitch_Effect( array(
                'image_id'                  => $this->image_id,
                'glitch_effect'             => $glitch_effect,
                'permanent_glitch'          => $permanent_glitch,
                'image_desktop'             => $poster_url,
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

            $this->atts = $atts;

            extract( shortcode_atts( array (
                'url'                   => '',
                'file_url'              => '',
                'video_bg'              => '',
                'caption'               => '',
                'caption_position'      => 'default',
                'caption_spacing'       => '20',
                'caption_font_weight'   => 'bold',
                'caption_letter_spacing'=> '',
                'caption_color'         => '',
                'overlay_color'         => '',
                'location'              => 'inline',
                'poster'                => '',
                'maxwidth'              => '',
                'align'                 => '',
                'align_tablet'          => 'inherit',
                'align_mobile'          => 'inherit',
                'video_border'          => 'off',
                'video_shadow'          => '',
                'shadow_canvas_color'   => '',
                'video_shadow_style'    => '',
                'shadow_color'          => '',
                'shadow_blur_radius'    => '20',
                'video_padding'         => '20',
                'rotation'              => '',
                'rotation_tablet'       => '0',
                'rotation_tablet_change'=> '',
                'rotation_mobile'       => '0',
                'rotation_mobile_change'=> '',
                'border_color'          => '',
				'border_width'			=> '',
                'play_icon'             => 'on',
                'play_shape'            => 'round',
                'play_color'            => '',
                'play_color_hover'      => '',
                'play_bg_color'         => '',
                'play_bg_color_hover'   => '',
                'play_icon_style'       => 'line',
				'play_icon_size'		=> '',
                'preview_only'          => '',

                // icon pulsate
                'icon_pulsate'                => 'off',
                'icon_pulsate_quick_options'  => 'on',
                'icon_pulsate_amount_simple'  => '1',
                'icon_pulsate_amount_advanced'=> '1',
                'icon_pulsate_intensity'      => '25',
                
                // simple mode colors
                'pulse_color'           => '#FFF',
                'pulse_style_width'     => '',
                'pulse_style'           => 'solid',                
                
                // icon 1 pulsate
                'pulse_color_1'         => '',
                'pulse_style_width_1'   => '',
                'pulse_style_1'         => 'solid',
                
                // icon 2 pulsate
                'pulse_color_2'         => '',
                'pulse_style_width_2'   => '',
                'pulse_style_2'         => 'solid',
                
                // icon 3 pulsate
                'pulse_color_3'         => '',
                'pulse_style_width_3'   => '',
                'pulse_style_3'         => 'solid',
                
                // icon 4 pulsate
                'pulse_color_4'         => '',
                'pulse_style_width_4'   => '',
                'pulse_style_4'         => 'solid',

                // gltich
                'glitch_effect'         => 'none',

                // nested
                'blog_layout'           => '',

	            // cursor
                'cursor_skin'         => 'inherit',

	            // Animation
                'effect'              => '',
                'animate_once'        => 'yes',
                'animate_mobile'      => false,
                'animate_tablet'      => false,
                'delay'               => 'no',
                'delay_timer'         => '100',
                'animation_duration'  => '',

                'class'                 => '' ,
                'css'                   => '',
                
            ), $atts ) ); 
            
            // extract url as fallback
            $url = ut_extract_url_from_string( $url );

            if( !empty( $file_url ) ) {

                $url = wp_get_attachment_url( $file_url );

            }

            $this->image_id = !empty( $poster ) ? $poster : rand(1, 99);

            // set unique ID for this video
            $id       = uniqid("ut_vs_");
            $keyframe = uniqid("ut-video-play-border-animation-");

            // settings
            $css_style = '';
            $image_icon = false;
            $classes = array();
            $classes[] = $class;
            
            $classes2 = array(); // module
            $classes3 = array(); // caption
            $video_play_icon_classes = array();

            // play icon
            if( $play_shape != 'round' ) {

                // no pulse effect
                $icon_pulsate = 'off';

                $video_play_icon_classes[] = 'ut-video-module-play-icon-' .$play_shape ;

            }

	        if( $preview_only == 'on'  ) {

		        $video_play_icon_classes[] = 'ut-new-hide';

            }

	        $video_play_icon_hidden = '';

            if( $play_icon == 'off' ) {

	            $video_play_icon_hidden = 'ut-video-module-play-icon-hidden';

            }

            // video border
            if( $video_border == 'on' ) {
                $classes3[] = 'ut-video-module-border';
            }

            // video shadow
            $video_shadow_class = array('ut-box-shadow-container');

            if( $video_shadow == 'on' ) {

                // $classes[] = $shadow . '-spacing';

                if( empty( $video_shadow_style ) ) {

                    $video_shadow_class[] = 'ut-video-module-shadow';

                } else {

                    $video_shadow_class[] = $video_shadow_style;

                }

            }

            /**
             * Video Settings
             */

            $video_background = '';

            if( $video_bg == 'yes' ) {

                $video = new UT_Background_Video_Player();

                // Add Containment
                $atts['containment'] = '#' . $id;
                $atts['source']      = 'selfhosted';
                $atts['lazy_load']   = true;

                // Poster provided by Module
                unset( $atts['poster'] );

                $video_background = $video->handle_shortcode( $atts );

                if( $video_background ) {

                    $classes[] = 'ut-has-background-video';

                }

            }
            
			// icon size
			if( $play_icon_size == 'large' ) {
				$classes3[] = 'ut-video-module-play-icon-large';
			}
			
            // video align
	        $align_tablet = $align_tablet == 'inherit' ? $align : $align_tablet;
	        $align_mobile = $align_mobile == 'inherit' ? $align_tablet : $align_mobile;

            if( $align ) {

                $classes[] = 'ut-shortcode-video-wrap-' . $align ;

            }

	        $classes[] = 'ut-shortcode-video-wrap-tablet-' . $align_tablet ;
	        $classes[] = 'ut-shortcode-video-wrap-mobile-' . $align_mobile ;


	        /* fill animation classes */
	        $attributes = array();

	        if( !empty( $effect ) && $effect != 'none' ) {

		        $classes[] = 'ut-animate-element';
		        $classes[] = 'animated';

		        if( !$animate_tablet ) {
			        $classes[]  = 'ut-no-animation-tablet';
		        }

		        if( !$animate_mobile ) {
			        $classes[]  = 'ut-no-animation-mobile';
		        }

		        if( $animate_once == 'infinite' ) {
			        $classes[]  = 'infinite';
		        }

		        $attributes['data-effect'] = esc_attr( $effect );
		        $attributes['data-animateonce'] = esc_attr( $animate_once );
		        $attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;

		        if( !empty( $animation_duration ) ) {
			        $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );
		        }

	        }

	        /* custom cursor */
	        if( $cursor_skin !== 'inherit' ) {
		        $attributes['data-cursor-skin'] = esc_attr( $cursor_skin );
	        }

	        /* attributes string */
	        $attributes = implode(' ', array_map(
		        function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
		        $attributes,
		        array_keys( $attributes )
	        ) );

            // glitch class
            if( $glitch_effect != 'none' ) {
                $classes[] = 'ut-has-element-glitch';
            }

            // assign poster image id
            $poster_id = $poster;

            // load poster image
            $poster = wp_get_attachment_image_src( $poster_id, 'full' );

            if( !empty( $poster ) ) {

	            $dimension = !empty( $blog_layout ) ? $blog_layout : 'default';

	            $sizes = array(
                    'default'    => array( $poster[1], $poster[2] ),
                    'mixed-grid' => array( '720' , '600' ),
                    'grid'       => array( '720' , '600' ),
		            'list-grid'  => array( '756' , '700' ),
                    'classic'    => array( '1280' , '720' )
                );

                $adaptive_poster = UT_Adaptive_Image::create( $poster_id, $sizes[$dimension] , true);

                $poster_url      = !empty( $poster[0] ) ? $poster[0] : '';
                $poster          = $adaptive_poster;

            }

            // poster is empty - use fallback ( currently only for youtube and vimeo )
            if( empty( $poster ) ) {
                
                // youtube
                if( ut_video_is_youtube( $url ) ) {
                
                    preg_match('~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i', trim( $url ) , $matches );

                    if( !empty( $matches[1] ) ) {

                        $poster     = '<img class="ut-lozad" src="' . $this->create_placeholder_svg( 1280, 720 ) . '" data-src="//i.ytimg.com/vi/' . $matches[1] . '/maxresdefault.jpg" width="1280" height="720">';
                        $poster_url = '//i.ytimg.com/vi/' . $matches[1] . '/maxresdefault.jpg';

                    }
                
                }                   
                    
                // vimeo
                if( ut_video_is_vimeo( $url ) ) {
                    
                    $vimeo_id = extract_vimeo_id( $url );
                    
                    if( $vimeo_id ) {
                    
                        $data = file_get_contents("http://vimeo.com/api/v2/video/$vimeo_id.json");
                        $data = json_decode( $data );
                        
                        if( !empty( $data[0]->thumbnail_large ) ) {
                        
                            $poster     = '<img src="' . esc_url( $data[0]->thumbnail_large ) . '">';
                            $poster_url = $data[0]->thumbnail_large;
                            
                        }
                    
                    }                    
                    
                }                
                
            }
            
            // still empty, let's use a final fallback
            if( empty( $poster ) ) {
                $poster     = '<img class="ut-lozad skip-lazy" src="' . $this->create_placeholder_svg( 770, 440 ) . '" data-src="' . UT_SHORTCODES_URL . '/img/placeholder/video-poster.jpg">';
                $poster_url = UT_SHORTCODES_URL . '/img/placeholder/video-poster.jpg';
            }

            // start output
            $output = '';
            
            // custom CSS
            if( !empty( $rotation ) ) {
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap { -webkit-transform: rotate(' . $rotation . 'deg); transform: rotate(' . $rotation . 'deg); }';
            }

            if( $rotation_tablet_change == 'on' ) {
                $css_style .= '@media (min-width: 768px) and (max-width: 1024px) { #' . $id . '.ut-shortcode-video-wrap { -webkit-transform: rotate(' . $rotation_tablet . 'deg); transform: rotate(' . $rotation_tablet . 'deg); } }';
            }

	        if( $rotation_mobile_change == 'on' ) {
		        $css_style .= '@media (max-width: 767px) { #' . $id . '.ut-shortcode-video-wrap { -webkit-transform: rotate(' . $rotation_mobile . 'deg); transform: rotate(' . $rotation_mobile . 'deg); } }';
	        }

            if( !empty( $maxwidth ) ) {
                // custom max width
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module { max-width: ' . $maxwidth . '% ; }';
            }
            
            if( $video_border == 'on' && !empty( $border_color ) ) {
                // border color
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-border { border-color: ' . $border_color . '; }';    
            }
            
            if( $video_border == 'on' && !empty( $video_padding ) ) {
                // border spacing
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption { padding: ' . $video_padding . 'px; }';    
            }
            
			if( $video_border == 'on' && !empty( $border_width ) ) {
				// border width
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption { border-width: ' . $border_width . 'px; }';
			}
						
            if( $video_shadow == 'on' && !empty( $shadow_color ) ) {
                
                // shadow color
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-shadow { color: ' . $shadow_color . '; }';
                
                if( !empty( $video_shadow_style ) ) {
                    
                    $css_style .= ut_box_shadow( $video_shadow_style, $shadow_color );
                    
                }
                
            }
            
            if( $video_shadow == 'on' && !empty( $shadow_blur_radius ) && empty( $video_shadow_style ) ) {
                
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-shadow { -webkit-box-shadow: 0 0 ' . $shadow_blur_radius . 'px; box-shadow: 0 0 ' . $shadow_blur_radius . 'px; }';    
                
            }

            if( $video_shadow == 'on' && $shadow_canvas_color ) {
                $css_style .= '#' . esc_attr( $id ) . ' .ut-box-shadow-container-inner { background: ' . $shadow_canvas_color . '; }';
            }

            if( ut_is_gradient( $overlay_color ) ) {
                
                $css_style .= ut_create_gradient_css( $overlay_color, '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption .ut-load-video::before', false, 'background' );

            } elseif( $overlay_color ) {

                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption .ut-load-video::before { background: ' . $overlay_color . '; }';

            }
            
            if( $caption_font_weight ) {
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption-text span { font-weight: ' . $caption_font_weight . '; }';
            }
            
            if( $caption_letter_spacing ) {
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption-text span { letter-spacing: ' . $caption_letter_spacing . 'em; }';
            }
            
            if( $caption_color ) {
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption-text span { color: ' . $caption_color . '; }';                
            }
            
            if( $play_color ) {
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-caption-text i { color: ' . $play_color . '; }';
            }

            if( $play_color_hover ) {
                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-load-video:hover .ut-video-module-caption-text i { color: ' . $play_color_hover . '; }';
            }
            
			
			if( ut_is_gradient( $play_bg_color ) ) {
                
                $css_style .= ut_create_gradient_css( $play_bg_color, '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-play-icon', false, 'background' );

            } elseif( $play_bg_color ) {

                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-play-icon { background: ' . $play_bg_color . '; }';

            }

            if( ut_is_gradient( $play_bg_color_hover ) ) {

                $css_style .= ut_create_gradient_css( $play_bg_color_hover, '#' . $id . '.ut-shortcode-video-wrap .ut-load-video:hover .ut-video-module-play-icon', false, 'background' );

            } elseif( $play_bg_color_hover ) {

                $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-load-video:hover .ut-video-module-play-icon { background: ' . $play_bg_color_hover . '; }';

            }

            if( $caption_spacing && !empty( $caption ) ) {
                
                if( $caption_position == 'default' ) {
                    
                    $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-play-icon { margin-right: ' . $caption_spacing . 'px; }';
                    
                } else {
                    
                    $css_style .= '#' . $id . '.ut-shortcode-video-wrap .ut-video-module-play-icon { margin-bottom: ' . $caption_spacing . 'px; }';
                    
                }
                
            }
            
            // Pulse CSS
            if( $icon_pulsate == 'on' ) {
                
                $css_style .= $this->create_keyframes( $keyframe, $icon_pulsate_intensity );
                
                if( $icon_pulsate_quick_options == 'on'  ) {

                    $pulsate_amount = $icon_pulsate_amount_simple;
					
					$pulse_style_width = !empty( $pulse_style_width ) ? $pulse_style_width :  1;
					
                    $css_style .= '#' . $id . ' .ut-video-play-animation-1 { border: ' . $pulse_style_width . 'px ' . $pulse_style . ' ' . $pulse_color . ' }';
                    $css_style .= '#' . $id . ' .ut-video-play-animation-2 { border: ' . $pulse_style_width . 'px ' . $pulse_style . ' ' . $pulse_color . ' }';
                    $css_style .= '#' . $id . ' .ut-video-play-animation-3 { border: ' . $pulse_style_width . 'px ' . $pulse_style . ' ' . $pulse_color . ' }';
                    $css_style .= '#' . $id . ' .ut-video-play-animation-4 { border: ' . $pulse_style_width . 'px ' . $pulse_style . ' ' . $pulse_color . ' }';
					
					for( $x = 1; $x <= $pulsate_amount; $x++ ) {
						
						$css_style .= '#' . $id . ' .ut-video-play-animation-' . $x . '.ut-video-play-animation-on {
                            -webkit-animation: 3s ease-in-out ' . ( 0.75 * $x ) . 's infinite normal none running ' . $keyframe . ';
                                -moz-animation: 3s ease-in-out ' . ( 0.75 * $x ) . 's infinite normal none running ' . $keyframe . ';
                                        animation: 3s ease-in-out ' . ( 0.75 * $x ) . 's infinite normal none running ' . $keyframe . ';   
                        }';						
						
					}
					
                } else {

                    $pulsate_amount = $icon_pulsate_amount_advanced;

                    for( $x = 1; $x <= $pulsate_amount; $x++ ) {
                        
                        $current_pulse_color = ${"pulse_color_" . $x} ? ${"pulse_color_" . $x} : $pulse_color;
                        
                        if( $current_pulse_color ) {
                            $css_style .= '#' . $id . ' .ut-video-play-animation-' . $x . ' { border-color: ' . $current_pulse_color . '; }';
                        }

                        if( ${"pulse_style_width_" . $x} ) {
                            $css_style .= '#' . $id . ' .ut-video-play-animation-' . $x . ' { border-width: ' . ${"pulse_style_width_" . $x} . 'px; }';
                        }

                        if( ${"pulse_style_" . $x} ) {
                            $css_style .= '#' . $id . ' .ut-video-play-animation-' . $x . ' { border-style: ' . ${"pulse_style_" . $x} . '; }';
                        }
                        
                        $css_style .= '#' . $id . ' .ut-video-play-animation-' . $x . '.ut-video-play-animation-on {
                            -webkit-animation: 3s ease-in-out ' . ( 0.75 * $x ) . 's infinite normal none running ' . $keyframe . ';
                                -moz-animation: 3s ease-in-out ' . ( 0.75 * $x ) . 's infinite normal none running ' . $keyframe . ';
                                        animation: 3s ease-in-out ' . ( 0.75 * $x ) . 's infinite normal none running ' . $keyframe . ';   
                        }';

                    }

                }
            
            }                
                
            if( !empty( $css_style ) ) {
            
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css_style . '</style>' );
            
            }

            $output .= '<div id="' . $id . '" class="ut-shortcode-video-wrap ' . esc_attr( implode( " ", $classes ) ) . ' clearfix" ' . $attributes  . '>';
            
                $output .= '<div class="ut-video-module ' . esc_attr( implode( " ", $classes2 ) ) . '">';

                    if( $video_shadow == 'on' ) {

                        $output .= '<div class="' . implode( ' ', $video_shadow_class ) . '"><div class="ut-box-shadow-container-inner"></div></div>';

                    }

                    $output .= '<div class="ut-video-module-caption ' . esc_attr( implode( " ", $classes3 ) ) . '">';

	                    $video_id = uniqid("ut_vid_");
	                    $html_flag = '';

                        // HTML5 Player for Lightbox
                        if( $location == 'lightbox' && strpos( $url, '.mp4' ) !== false ) {

                            $output .= '<div style="display:none;" id="' . $video_id . '">';

                                $output .= '<video poster="' . $poster_url . '" class="lg-video-object lg-html5" controls preload="none">';

                                    $output .= '<source src="' . trim( $url ) . '" type="video/mp4">';

                                $output .= '</video>';

                            $output .= '</div>';

	                        $html_flag = 'data-html5-video="true"';

                        }

                        if( $preview_only == 'on'  ) {

                            $output .= '<a href="#" class="ut-load-video ut-deactivated-link ' . $video_play_icon_hidden . '">';

                        } else {

	                        $output .= '<a class="ut-load-video ' . $video_play_icon_hidden . '" data-location="' . esc_attr( $location ) . '" ' . $html_flag . ' data-video="' . trim( $url ) . '" data-html="#' . $video_id . '" href="#">';

                        }

                            $output .= $poster;

                                $output .= $this->create_glitch_effect( $poster_url );

                            $output .= $video_background;
                        
                            $output .= '<div class="ut-video-module-caption-text">';

                                $output .= '<div class="ut-video-module-inner-caption-text ' . ( empty( $caption ) ? 'ut-video-module-empty-caption' : '' ) . ' ut-video-module-caption-position-' . $caption_position . '">'; 

                                    $output .= '<div class="ut-video-module-play-icon ' . implode( " ", $video_play_icon_classes ) . '">';

                                        if( $icon_pulsate == 'on' ) {

                                            for( $x = 1; $x <= $pulsate_amount; $x++ ) {

                                                $output .= '<div class="ut-video-play-animation-' . $x . ' ut-video-play-animation-on"></div>';

                                            }

                                        }

                                        if( $play_icon_style == 'line' ) {

                                            $output .= '<i class="Bklyn-Core-Right-6" aria-hidden="true"></i>';      

                                        } else {

                                            $output .= '<i class="fa fa-play" aria-hidden="true"></i>'; 

                                        }

                                    $output .= '</div>';

                                    if( !empty( $caption ) ) {

                                        $output .= '<span>' . $caption . '</span>';

                                    }    

                                $output .= '</div>';

                            $output .= '</div>';
            
                        $output .= '</a>';
            
                        $output .= '</div>';
                    
                    $output .= '<div class="ut-video-module-loading">';
                        
                        $output .= '<div class="sk-fading-circle">';        
                            
                            for ($x = 1; $x <= 12; $x++) {
                                
                                $output .= '<div class="sk-circle'.$x.' sk-circle"></div>';
                                
                            }
                        
                        $output .= '</div>';
                    
                    $output .= '</div>';
            
                $output .= '</div>';
            
            $output .= '</div>';
            
            /* return player */
            if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }
           
            return $output;
        
        }
            
    }

}

new UT_Video_Shortcode;


if( !function_exists('ut_simple_html5_video') ) {

    function ut_simple_html5_video( $atts = array() ) {

        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $wporg_atts = shortcode_atts([
            'mp4'       => $atts['mp4'],
            'style'     => null,
            'controls'  => false,
            'muted'     => false,
            'volume'    => '',
            'width'     => '',
            'height'    => ''
        ], $atts);

        // build output
        $o = '';
        $o .= '<video width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" autoplay loop playsinline webkit-playsinline preload="auto" ';
        if ($wporg_atts['controls']) $o .= 'controls ';
        if ($wporg_atts['muted']) $o .= 'muted defaultMuted ';
        $o .= 'class="ut-simple-html-5"';
        if (!is_null($wporg_atts['style'])) $o .= 'style="' . $wporg_atts['style'] . '" ';
        $o .= '><source src="' . $wporg_atts['mp4'] . '" type="video/mp4" />';
        $o .= '<p>Your browser does not support the video element.</p></video>';

        // return output
        return $o;

    }

    add_shortcode('ut_simple_html5_video', 'ut_simple_html5_video');

}