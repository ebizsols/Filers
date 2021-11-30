<?php

$GLOBALS['ut_video_tooltip'] = false;

if( !class_exists( 'UT_Background_Video_Player' ) ) :

    class UT_Background_Video_Player {

        /**
         * Unique Player ID
         */

        public $id;


        /**
         * Parent ID
         */

        public $parent_id;


        /**
         * Youtube Script
         */

        public $youtube;


        /**
         * Vimeo Script
         */

        public $vimeo;


        /**
         * Selfhosted Script
         */

        public $selfhosted;


        /**
         * Footer Add Script
         */

        protected $add_script;


        /**
         * Attributes
         */

        public $atts;


        /**
         * Instantiates the class
         */

        function __construct() {

            // add shortcode
            add_shortcode( 'ut_background_video', array( $this, 'handle_shortcode') );

            // print scripts by use
            add_action( 'wp_footer', array(  $this , 'print_script') );

            // print tooltip for user interaction
            // add_action( 'ut_after_footer_hook', array(  $this , 'print_tooltip') );

        }


        /**
         * Inline CSS
         */

        public function inline_css( $id ) {

            extract( shortcode_atts( array(

                'controls_style'                    => 'default',
                'controls_color'                    => '',
                'controls_border_color'             => '',
                'controls_border_animation_color'   => ''

            ), $this->atts ) );

            ob_start(); ?>

                <style type="text/css">

                    <?php if( $controls_color ) : ?>

                        #<?php echo $id; ?>.ut-video-controls a,
                        #<?php echo $id; ?>.ut-video-controls .ut-video-audio-control-speaker::before {
                            color: <?php echo $controls_color; ?>;
                        }

                        #<?php echo $id; ?>.ut-video-controls .ut-video-play-control-icon {
                            border-color: transparent transparent transparent <?php echo $controls_color; ?>;
                        }

                        #<?php echo $id; ?>.ut-video-controls .ut-audio-bar ul li {
                            background: <?php echo $controls_color; ?>;
                        }

                    <?php endif; ?>

                    <?php if( $controls_border_color ) : ?>

                        #<?php echo $id; ?> .ut-video-control-bordered {
                            box-shadow: inset 0 0 0 1px <?php echo $controls_border_color; ?>;
                        }

                    <?php endif; ?>

                    <?php if( $controls_border_animation_color ) : ?>

                        #<?php echo $id; ?> .ut-box-draw:hover::before {
                            border-top-color: <?php echo $controls_border_animation_color; ?>;
                            border-right-color: <?php echo $controls_border_animation_color; ?>;
                        }

                        #<?php echo $id; ?> .ut-box-draw:hover::after {
                            border-bottom-color: <?php echo $controls_border_animation_color; ?>;
                            border-left-color: <?php echo $controls_border_animation_color; ?>;
                        }

                    <?php endif; ?>



                </style>

            <?php

            return str_replace("\r\n", '', trim( ob_get_clean() ) );


        }



        /**
         * CSS Filter
         *
         * @return string A JSON Object with set Filters
         */

        public function css_filter() {

            /**
             * @var string $video_filter
             * @var string $video_filter_action
             * @var number $contrast
             * @var number $brightness
             * @var number $saturate
             * @var number $grayscale
             * @var number $blur
             * @var number $sepia
             */
            extract( shortcode_atts( array(

                'video_filter'          => '',
                'video_filter_action'   => 'none',
                'contrast'              => 100,
                'brightness'            => 100,
                'saturate'              => 100,
                'grayscale'             => 0,
                'blur'                  => 0,
                'sepia'                 => 0,

            ), $this->atts ) );

            if( $video_filter != 'yes' ) {
                return false;
            }

            $filter = array();

            // contrast 0-200
            if( $contrast && $contrast != 100 ) {
                $filter['contrast'] = $contrast;
            }

            // brightness 0-200
            if( $brightness && $brightness != 100 ) {
                $filter['brightness'] = $brightness;
            }

            // saturate 0-200
            if( $saturate && $saturate != 100 ) {
                $filter['saturate'] = $saturate;
            }

            // grayscale 0-100
            if( $grayscale ) {
                $filter['grayscale'] = $grayscale;
            }

	        // sepia 0-100
	        if( $sepia ) {
		        $filter['sepia'] = $sepia;
	        }

            // blur 0-100            
            if( $blur ) {
                $filter['blur'] = $blur;
            }

            // filter action
            if( $video_filter_action ) {
                $filter['action'] = $video_filter_action;
            }

            return json_encode( $filter, JSON_HEX_APOS );

        }


        /**
         * Youtube Check
         */

        public function is_youtube( $video ) {

            return preg_match('~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x', trim($video) , $matches);

        }

        /**
         * Youtube Player
         */

        public function youtube_player( $video ) {

	        /**
	         * @var string $play_event
	         * @var string $containment
	         * @var string $sound
	         * @var number $volume
             * @var number $abundance
             * @var number $startAt
             * @var number $stopAt
             * @var string $aspect_ratio
             * @var string $force_aspect_ratio
	         * @var string $loop
	         * @var string $video_filter
	         * @var string $play_button
	         * @var string $mute_button
	         * @var string $controls_position
             * @var string $controls_style
	         * @var string $play_button_action
	         */
            extract( shortcode_atts( array(

	            // Containment
	            'containment'        => '',

	            // Play Config
	            'play_event'         => 'on_load',
	            'sound'              => 'off',
	            'volume'             => '5',
	            'loop'               => 'on',
	            'startAt'            => '0',
	            'stopAt'             => '0',

	            // Buttons
	            'mute_button'        => 'off',
	            'mute_button_style'  => '',
	            'play_button'        => 'off',
	            'play_button_action' => 'play_pause',

	            // Buttons Position
	            'controls_position'  => 'bottom-right',
	            'controls_style'     => 'default',

	            // Video
	            'video_filter'       => '',
	            'aspect_ratio'       => 'wide',
	            'force_aspect_ratio' => 'yes'

            ), $this->atts ) );

	        global $reveal_fx_active;

            // player options
            $volume = empty( $volume ) ? '0' : $volume;

            // player json
            $player_config = array(
                'videoURL'          => $video,
                'containment'       => $containment,
                'showControls'      => false,
                'container_class'   => 'ut-youtube-video-container mbYTP_wrapper',
                'ratio'             => 'auto',
                'quality'           => 'hd1080',
                'autoPlay'          => false,
                'startAt'           => $startAt,
                'stopAt'            => $stopAt,
                'loop'              => ( $loop == 'on' ),
                'mute'              => ( $sound == 'off' ),
                'vol'               => $volume,
                'opacity'           => '1',
                'abundance'         => 0
            );

	        if( $video_filter == 'yes' ) {

		        $player_config['filters_enabled'] = true;

            }

	        if( $force_aspect_ratio == 'yes' ) {

		        $player_config['container_class'] = 'ut-youtube-video-container mbYTP_wrapper ut-force-video-aspect-ratio ut-video-section-format-' . $aspect_ratio;

	        }

            $player = '<a id="ut-background-video-' . esc_attr( $this->id ) . '" data-id="' . esc_attr( $this->id ) . '" data-ut-wait="' . esc_attr( $reveal_fx_active ) . '" data-controls-play-event="' . esc_attr( $play_button_action ) . '" data-play-event="' . esc_attr( $play_event ) . '" data-filters=\'' . $this->css_filter() . '\' class="ut-video-section-player ut-video-section-player-youtube" data-property=\'' . json_encode( $player_config, JSON_HEX_APOS ) . '\'></a>';

            if( ( $mute_button == 'on' ) || $play_button == 'on' ) {

                // controls ID
                $id = ut_get_unique_id("ut_vc_", true);

                // attach controls css
                $player .= $this->inline_css( $id );

                // start controls
                $player .= '<div id="' . esc_attr( $id ) . '" data-for="' . esc_attr( $this->id ) . '" class="ut-video-controls ut-video-controls-' . str_replace( "_", "-", $controls_style ) . ' ut-video-controls-' . $controls_position . '">';

	            if( $play_button == 'on' ) {

		            $player .= $this->create_video_play_control();

	            }

                if( $mute_button == 'on' ) {

                    $player .= $this->create_video_audio_controls( 'youtube' );

                }

                $player .= '</div>';

            }

            return $player;

        }


        /**
         * Vimeo Check
         */

        function is_vimeo( $video ) {

            return preg_match('/\/\/(www\.)?vimeo.com\/(\d+)($|\/)/', trim($video) , $matches);

        }


        /**
         * Extract Vimeo ID
         *
         * @var string Video URL
         * @return mixed Vimeo Video ID or false
         */

        function vimeo_id( $url ) {

            if( preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo.com\/([a-z]*\/)*([0-9]{6,11})[?]?\.*/", trim($url) , $matches) ){

                if( !empty($matches[5]) ) {

                    return $matches[5];

                } else {

                    return false;

                }

            } else {

                return false;

            }

        }


        /**
         * Vimeo Player
         *
         * @var number Vimeo Video ID
         * @return string Vimeo Video String for JavaScript
         */

        public function vimeo_player( $video ) {

	        /**
	         * @var string $play_event
             * @var string $containment
             * @var string $sound
             * @var number $volume
             * @var number $abundance
             * @var string $aspect_ratio
             * @var string $force_aspect_ratio
             * @var string $loop
             * @var string $video_filter
             * @var string $play_button
             * @var string $mute_button
             * @var string $controls_position
             * @var string $controls_style
             * @var string $play_button_action
             */

            extract( shortcode_atts( array(
	            'play_event'         => 'on_load',

	            // Containment
	            'containment'        => '',

	            // Play Config
	            'sound'              => 'off',
	            'volume'             => '5',
	            'loop'               => 'on',
	            'startAt'            => '0',
	            'stopAt'             => '0',
	            'dnt'                => false,

	            // Mute Button
	            'mute_button'        => 'off',
	            'mute_button_style'  => '',

	            // Play Button
	            'play_button'        => '',
	            'play_button_action' => 'play_pause',

	            // Buttons Position
	            'controls_position'  => 'bottom-right',
	            'controls_style'     => 'default',

	            // Video
	            'video_filter'       => '',
	            'aspect_ratio'       => 'wide',
	            'force_aspect_ratio' => 'yes',
	            'abundance'          => 0

            ), $this->atts ) );

            global $reveal_fx_active;

            $ratio = array(
	            'wide'           => 16 / 9,
	            'cinematic'      => 21 / 9,
	            'cinematic-wide' => 24 / 10,
	            'normal'         => 4 / 3
            );

            // player json
            $player_config = array(
                'ratio'             => $ratio[$aspect_ratio],
                'videoId'           => $this->vimeo_id( $video ),
                'containment'       => $containment,
                'container_class'   => 'ut-video-section-format-' . $aspect_ratio,
                'playerId'          => $this->id,
                'sound'             => $sound,
                'class'             => '',
                'parameters'        => array(

                    'autopause'  => true,
                    'autoplay'   => false,
                    'badge'      => true,
                    'byline'     => true,
                    'color'      => "000",
                    'loop'       => ( $loop == 'on' ),
                    'player_id'  => ut_get_unique_id("ut_vimeo_", true),
                    'portrait'   => true,
                    'title'      => true,
                    'background' => 1,
                    'dnt'        => $dnt,
                    'volume'     => $volume ? $volume : 0

                )

            );

            if( $force_aspect_ratio == 'yes' ) {

	            $player_config['container_class'] = 'ut-vimelar-container ut-force-video-aspect-ratio ut-video-section-format-' . $aspect_ratio;

            }

            if( $video_filter == 'yes' ) {

                // extra class with transition
                $player_config['class'][] = 'ut-video-section-player-with-filter';

                // merge classes
                $player_config['class'] = implode(" ", $player_config['class'] );

            }

            // add player html
            $player = '<a id="ut-background-video-' . $this->id . '" data-id="' . esc_attr( $this->id ) . '" data-ut-wait="' . esc_attr( $reveal_fx_active ) . '" data-controls-play-event="' . esc_attr( $play_button_action ) . '" data-play-event="' . esc_attr( $play_event ) . '" class="ut-video-section-player ut-video-section-player-vimeo" data-filters=\'' . $this->css_filter() . '\' data-property=\'' . json_encode( $player_config, JSON_HEX_APOS ) . '\'></a>';

            if( $mute_button == 'on' || $play_button == 'on' ) {

                // controls ID
                $id = ut_get_unique_id("ut_vc_", true);

                // attach controls css
                $player .= $this->inline_css( $id );

                // start controls
                $player .= '<div id="' . esc_attr( $id ) . '" data-for="' . esc_attr( $this->id ) . '" class="ut-video-controls ut-video-controls-' . str_replace( "_", "-", $controls_style ) . ' ut-video-controls-' . $controls_position . '">';

	            if( $play_button == 'on' ) {

		            $player .= $this->create_video_play_control( 'vimeo' );

	            }

                if( $mute_button == 'on' ) {

                    $player .= $this->create_video_audio_controls( 'vimeo' );

                }

                $player .= '</div>';

            }

            return $player;

        }


        /**
         *  Selfhosted Video Glitch Effect
         */

	    public function selfhosted_player_glitch() {

	        /**
	         * @var string $mp4
             */
		    extract( shortcode_atts( array(

			    'play_event'            => 'on_load',

                // Sounds
			    'volume'                => '5',
			    'sound'                 => 'off',

			    // Media Files
			    'mp4'                   => '',
			    'ogg'                   => '',
			    'webm'                  => ''

		    ), $this->atts ) );

		    $container_class = array(
		        'ut-video-container'
            );

	        // Glitch Classes
		    $glitch_classes = array(
			    'ut-selfhosted-glitch-video'
		    );

		    // HTML5 Player Default Attributes
		    $player_attributes = array();

		    // Play Event
		    $player_attributes[] = 'data-play-event="' . $play_event . '"';

		    // Muted
		    $player_attributes[] = ( $sound == 'off' ) ? 'data-muted="true"' : 'data-muted=""';
		    $player_attributes[] = ( $sound == 'off' ) ? 'data-play-sound="off"' : 'data-play-sound="on"';

            // Volume
		    $player_attributes[] = ( empty( $volume ) ) ? 'data-volume="5"' : 'data-volume="' . $volume . '"';

		    // video play event
		    if( $play_event == 'on_hover' ) {

		        $container_class[] = 'ut-video-is-paused';
			    $player_attributes[] = ( empty( $volume ) ) ? 'data-autoplay="true"' : 'data-autoplay=""';

		    }

		    // Get URL
		    if( is_numeric( $mp4 ) ) {

			    $mp4 = wp_get_attachment_url( $mp4 );

		    }

		    $video_glitch = '<div class="' . implode( " ", $container_class ) . '">';

		        $video_glitch .= '<div data-mp4="' . esc_attr( $mp4 ) . '" data-id="' .  $this->id . '" class="' . implode( " ", $glitch_classes ) . '" ' . implode( " ", $player_attributes ) . '></div>';
		        $video_glitch .= '<canvas width="1920" height="1080" id="ut-glitch-canvas-' .  $this->id . '" class="ut-glitch-canvas"></canvas>';

		    $video_glitch .= '</div>';
		    $video_glitch .= $this->selfhosted_player_controls();

		    return $video_glitch;

        }


        /**
         * Selfhosted Player
         *
         * @param string $silentplayer
         *
         * @return string
         */

        public function selfhosted_player() {

	        /**
	         * @var string $play_event
	         * @var string $appear_offset
	         * @var boolean $lazy_load
	         * @var string $sound
	         * @var number $volume
             * @var string $poster
             * @var string $preload
	         * @var string $loop
             * @var string $aspect_ratio
             * @var string $force_aspect_ratio
	         * @var string $video_filter
	         * @var string $play_button
	         * @var string $mute_button
	         * @var string $controls_position
             * @var string $controls_style
	         * @var string $play_button_action
             * @var string $cover_method
             * @var string $video_glitch
             * @var string $class
	         */

            extract( shortcode_atts( array(
                'play_event'            => 'on_load',
                'appear_offset'         => 'full',
                'lazy_load'             => false,
                'hover_out_event'       => 'reset',

                // Buttons
                'mute_button'           => 'off',
                'mute_button_style'     => 'speakers',
                'play_button'           => 'off',
                'play_button_action'    => 'play_pause',

                // Video Aspect Ratio and Cover
                'cover_method'          => 'ut-video-object-fit',
                'aspect_ratio'          => 'wide',
                'force_aspect_ratio'    => 'yes',

                // Video Controls
                'controls_position'     => 'bottom-right',
                'controls_style'        => 'default',

                // Player Settings
                'volume'                => '5',
                'sound'                 => 'off',
                'loop'                  => 'on',
                'controls'              => 'off',
                'preload'               => 'metadata',

                // Poster Image
                'poster'                => '',

                // Video Filter
                'video_filter'          => '',
                'blur'                  => 0,
                'grayscale'             => 0,

                // Media Files
                'mp4'                   => '',
                'ogg'                   => '',
                'webm'                  => '',

                // Lightbox Media Files
                'lightbox_mp4'          => '',
                'lightbox_ogg'          => '',
                'lightbox_webm'         => '',

                // Glitch Effect
                'video_glitch'          => '',

                // optional class
                'class'                 => '',

            ), $this->atts ) );

            if( !empty( $mp4 ) || !empty( $ogg ) || !empty( $webm ) ) {

                global $reveal_fx_active;

                $this->selfhosted = true;

                // HTML5 Player Default Attributes
                $player_attributes = array();

                // Preload
                $player_attributes[] = ( $preload ) ? 'preload="' . $preload . '"' : 'preload="none"';

	            // Plays Inline
	            $player_attributes[] = 'playsinline'; // required for IOS

                // Loop
                $player_attributes[] = ( $loop == 'on' ) ? 'loop' : '';

                // Muted
                $player_attributes[] = ( $sound == 'off' ) ? 'muted' : '';
                $player_attributes[] = ( $sound == 'off' ) ? 'data-play-sound="off"' : 'data-play-sound="on"';

                // Set Volume
                $player_attributes[] = ( $volume == '' ) ? 'volume="5"' : 'volume="' . $volume . '"';

                // Set Autoplay
                if( $play_event == 'on_load' && $volume == '0' ) {
                    $player_attributes[] = 'autoplay';
                }

                // Play Event
                $player_attributes[] = 'data-play-event="' . $play_event . '"';
                $player_attributes[] = 'data-appear-top-offset="' . $appear_offset . '"';

                // Play Controls Event
                $player_attributes[] = 'data-controls-play-event="' . $play_button_action . '"';

                // Play Button Action
                $player_attributes[] = 'data-play-action="' . $play_button_action . '"';

	            // Wait for possible parent animations
	            $player_attributes[] = 'data-ut-wait="' . $reveal_fx_active . '"';

                // HTML Player Classes
                $player_classes = array( $class );

                if( $video_filter == 'yes' && !$video_glitch ) {

                    $player_attributes[] = 'data-video-filter="' . $this->css_filter() . '"';

                    // extra class with transition
                    $player_classes[] = 'ut-video-section-player-with-filter';

                }

                // Media Files
                $media_files = array();

                $media_files['mp4']  = !empty( $mp4 )  ? $mp4  : '';
                $media_files['ogg']  = !empty( $ogg )  ? $ogg  : '';
                $media_files['webm'] = !empty( $webm ) ? $webm : '';

                // video display
                $container_class    = array( $cover_method, $class );
                $container_class[]  = 'ut-video-format-' . $aspect_ratio;

	            if( $force_aspect_ratio == 'yes' ) {
		            $container_class[] = 'ut-force-video-aspect-ratio';
	            }

	            // video play event
                if( $play_event == 'on_hover' ) {
	                $container_class[] = 'ut-video-is-paused';
                }

                ob_start(); ?>

                <div id="<?php echo ut_get_unique_id('ut_vc_', true); ?>" class="ut-video-container <?php echo implode(" ", $container_class ); ?>">

                    <?php echo $this->html5_video_poster( $poster ); ?>
                    <?php echo $this->html5_video( $media_files, $player_attributes, $player_classes, $lazy_load ); ?>

                </div>

                <?php

                $player = str_replace("\r\n", '', trim( ob_get_clean() ) );

                if( !$video_glitch ) {

	                $player .= $this->selfhosted_player_controls();

                }

                return $player;

            }

            return false;

        }

	    /**
	     *
	     */

	    public function selfhosted_player_controls() {

		    /**
		     * @var string $play_button
		     * @var string $mute_button
		     * @var string $controls_position
		     * @var string $controls_style
		     * @var string $video_glitch
		     */

		    extract( shortcode_atts( array(

			    // Buttons
			    'mute_button'           => 'off',
			    'play_button'           => 'off',

			    // Video Controls
			    'controls_position'     => 'bottom-right',
			    'controls_style'        => 'default',

			    // Glitch Effect
			    'video_glitch'          => '',

		    ), $this->atts ) );

		    $player = '';

		    if ( $mute_button == 'on' || $play_button == 'on' ) {

			    // controls ID
			    $id = ut_get_unique_id( "ut_vc_", true );

			    // attach controls css
			    $player .= $this->inline_css( $id );

			    // start controls
			    $player .= '<div id="' . esc_attr( $id ) . '" data-for="' . esc_attr( $this->id ) . '" class="ut-video-controls ut-video-controls-visible ut-video-controls-' . str_replace( "_", "-", $controls_style ) . ' ut-video-controls-' . $controls_position . '">';

			    if ( $play_button == 'on' ) {

				    $player .= $this->create_video_play_control( 'selfhosted' );

			    }

			    if ( $mute_button == 'on' ) {

				    $player .= $this->create_video_audio_controls( 'selfhosted' );

			    }

			    $player .= '</div>';

		    }

		    return $player;

	    }

        /**
         * Selfhosted Player Lightbox
         */

        public function lightbox_selfhosted_player() {

            extract( shortcode_atts( array(
                'volume'                => '',
                'lightbox_mp4'          => '',
                'lightbox_ogg'          => '',
                'lightbox_webm'         => ''
            ), $this->atts ) );

            // Media Files
            $media_files = array();

            $media_files['mp4']  = !empty( $lightbox_mp4 )  ? $lightbox_mp4  : '';
            $media_files['ogg']  = !empty( $lightbox_ogg )  ? $lightbox_ogg  : '';
            $media_files['webm'] = !empty( $lightbox_webm ) ? $lightbox_webm : '';

            // No Videos available
            if( empty( $media_files ) ) {
                return false;
            }

            // Player Attributes
            $player_attributes   = array('controls', 'preload="none"');
            $player_attributes[] = ( $volume == '' ) ? 'volume="5"' : 'volume="' . $volume . '"';

            // Classes for Lightgallery
            $classes = array( 'lg-video-object', 'lg-html5' );

            // Lightbox Player
            $player = '<div id="ut-lightbox-selfhosted-video-' . $this->id . '" class="ut-lightbox-selfhosted-video">';

                $player .= $this->html5_video( $media_files, $player_attributes, $classes );

            $player .= '</div>';

            return $player;

        }


        /**
         * HTML Video Player
         *
         * @var array $media_files An Array containing all media files
         * @var array $player_attributes
         * @var array $classes Optional Classes for the Player
         * @var boolean $lazy
         *
         * @return string Final HTML5 Player
         */

        public function html5_video( $media_files = array(), $player_attributes = array(), $classes = array(), $lazy = false ) {

            // lazy load support
            if( $lazy ) {

                $classes[] = 'ut-lazy-video';

            }

            // HTML5 Player Start
            $player = '<video id="ut-selfvideo-player-' . esc_attr( $this->id ) . '" data-id="' . esc_attr( $this->id ) . '" data-filters=\'' . $this->css_filter() . '\' class="ut-selfvideo-player disablePictureInPicture  ' . implode(" ", $classes) . '" ' . implode( " ", $player_attributes ) . '>';

	        if( !empty( $media_files['webm'] ) ) :

		        if( is_numeric( $media_files['webm'] ) ) {

			        $media_files['webm'] = wp_get_attachment_url( $media_files['webm'] );

		        }

		        if( $lazy ) {

			        $player .= '<source data-src="' . esc_url($media_files['webm']) . '" type="video/webm"> ';

		        } else {

			        $player .= '<source src="' . esc_url($media_files['webm']) . '" type="video/webm"> ';

		        }

	        endif;

            if( !empty( $media_files['mp4'] ) ) :

                if( is_numeric( $media_files['mp4'] ) ) {

                    $media_files['mp4'] = wp_get_attachment_url( $media_files['mp4'] );

                }

                if( $lazy ) {

                    $player .= '<source data-src="' . esc_url( $media_files['mp4'] ) . '" type="video/mp4"> ';

                } else {

                    $player .= '<source src="' . esc_url( $media_files['mp4'] ) . '" type="video/mp4"> ';

                }

            endif;

            if( !empty( $media_files['ogg'] ) ) :

                if( is_numeric( $media_files['ogg'] ) ) {

                    $media_files['mp4'] = wp_get_attachment_url( $media_files['ogg'] );

                }

                if( $lazy ) {

                    $player .= ' <source data-src="' . esc_url($media_files['ogg']) . '" type="video/ogg ogv">';

                } else {

                    $player .= ' <source src="' . esc_url( $media_files['ogg'] ) . '" type="video/ogg ogv">';

                }

            endif;

            $player .= '</video>';

            return $player;

        }


        /**
         * HTML Video Player
         */

        public function html5_video_poster( $poster ) {

            if( empty( $poster ) || !is_numeric( $poster ) ) {
                return false;
            }

            $poster = wp_get_attachment_url( $poster );

            return '<img class="ut-video-poster" src="' . esc_url( $poster ) . '">';

        }


        /**
         * Video Play Button Controls
         */

        public function create_video_play_control( $source = 'youtube' ) {

            extract( shortcode_atts( array(

                // autoplay
                'play_event'            => 'on_load',

                // visual appearance
                'controls_style'        => 'default',
                'controls_animated'     => '',

                // play button
                'play_button_action'    => 'play_pause',

                // lightbox
                'lightbox_source'       => 'default',
                'lightbox_caption'      => '',
                'lightbox_url'          => '', // vimeo or youtube

                // Poster Image ( Selfhosted )
                'poster'                => '',

            ), $this->atts ) );



            $classes = array();


            if( $play_button_action == 'play_pause' ) {

                $target = '#';

            }

            if( $play_button_action == 'play_lightbox' ) {

                // selfhosted lightbox video
                if( $lightbox_source == 'selfhosted' ) {

                    // target for hidden player
                    $target = '#ut-lightbox-selfhosted-video-' . $this->id;

                    // poster image
                    if( !empty( $poster ) ) {

                        $poster = 'data-poster="' . wp_get_attachment_url( $poster ) . '"';

                    }

                // youtube vimeo
                } else {

                    $target = $lightbox_url;

                }

                $classes[] = 'ut-video-play-control-lightbox';


            }

            // bordered controls
            if( $controls_style == 'bordered' ) {

                $classes[] = 'ut-video-control-bordered';

            }

            // animated border
            if( $controls_style == 'bordered_animated' ) {

                $classes[] = 'ut-video-control-bordered';
                $classes[] = 'ut-box-draw ut-box-meet';

            }

            ob_start(); ?>

            <?php if( $lightbox_source == 'selfhosted' ) : ?>

                <a <?php echo esc_url( $poster ); ?> data-sub-html="<?php echo esc_attr( $lightbox_caption ); ?>" data-html="<?php echo esc_url( $target ); ?>" data-event-type="<?php echo esc_attr( $play_button_action ); ?>" data-for="<?php echo esc_attr( $this->id ); ?>" class="ut-video-play-control <?php echo implode( " ", $classes ); ?>">

                    <i class="ut-video-play-control-icon"></i>

                </a>

                <?php echo $this->lightbox_selfhosted_player(); ?>

            <?php else : ?>

                <a data-source="<?php echo esc_attr( $source ); ?>" data-for="<?php echo $this->id; ?>" data-source="<?php echo esc_attr( $source ); ?>" href="<?php echo esc_url( $target ); ?>" data-event-type="<?php echo esc_attr( $play_button_action ); ?>" data-for="<?php echo esc_attr( $this->id ); ?>" class="ut-video-play-control <?php echo implode( " ", $classes ); ?>">

                    <i class="ut-video-play-control-icon"></i>

                </a>

            <?php endif; ?>

            <?php return str_replace("\r\n", '', trim( ob_get_clean() ) );

        }


        /**
         * Video Audio Controls
         */

        public function create_video_audio_controls( $source = 'youtube' ) {

            extract( shortcode_atts( array(

                // sound and volumne
                'sound'                 => 'off',
                'volume'                => '5',

                // overall controls
                'controls_style'        => 'default',
                'controls_animated'     => '',

                // mute button
                'mute_button_style'     => 'speakers',

            ), $this->atts ) );

            $mute      = ( $sound == "on" ) ? 'ut-video-audio-control-on' : 'ut-video-audio-control-off';
            $mute_text = ( $sound == "on" ) ? esc_attr__('on','ut_shortcodes') : esc_attr__('off','ut_shortcodes');

            // extra classes
            $mute_classes = array( $mute );

            // bordered controls
            if( $controls_style == 'bordered' ) {

                $mute_classes[] = 'ut-video-control-bordered';

            }

            // animated border
            if( $controls_style == 'bordered_animated' ) {

                $mute_classes[] = 'ut-video-control-bordered';
                $mute_classes[] = 'ut-box-draw ut-box-meet';

            }

            // simple mute button
            if( $mute_button_style == 'speakers' ) {

                // video muted on load or not?
                $mute = ( $sound == "on" ) ? 'ut-mute' : 'ut-unmute';

                ob_start(); ?>

                <a data-volume="<?php echo $volume; ?>" data-source="<?php echo esc_attr( $source ); ?>" data-for="<?php echo $this->id; ?>" href="#" class="ut-video-audio-control <?php echo implode(" ", $mute_classes); ?>">

                    <i class="ut-video-audio-control-speaker"></i>

                </a>

                <?php return str_replace("\r\n", '', trim( ob_get_clean() ) );

            }

            // soundbars
            if( $mute_button_style == 'soundbars' ) {

                ob_start(); ?>

                <a data-volume="<?php echo $volume; ?>" data-source="<?php echo esc_attr( $source ); ?>" data-for="<?php echo $this->id; ?>" href="#" class="ut-video-audio-control ut-video-audio-control-soundbar <?php echo implode(" ", $mute_classes); ?>">

                    <div class="ut-video-audio-control-soundbar-wrap">

                        <span data-on="<?php echo esc_attr__( 'on','ut_shortcodes' ); ?>" data-off="<?php echo esc_attr__( 'off','ut_shortcodes' ); ?>">
                            <?php echo $mute_text; ?>
                        </span>
                        <div class="ut-audio-bar">
                            <ul><li></li><li></li><li></li><li></li><li></li><li></li></ul>
                        </div>

                    </div>

                </a>

                <?php return str_replace("\r\n", '', trim( ob_get_clean() ) );

            }

        }


        public function handle_shortcode( $atts ) {

            // add script to footer
            $this->add_script = true;

            // assign attributes
            $this->atts       = $atts;


            /**
             * @var boolean $glitch_effect
             *
             */

            extract( shortcode_atts( array(

                // Container ID
                'id'            => '',

                // Containment
                'section'       => '',

                // Video Source
                'source'        => 'default',

                // Hosted
                'video_bg_url'  => '',
                'video'         => '',
                'video_vimeo'   => '',

                // Selfhosted
                'mp4'           => '',
                'ogg'           => '',
                'webm'          => '',

                // Play Config
                'sound'         => 'off',
                'volume'        => '5',
                'loop'          => 'on',
                'startAt'       => '0',
                'stopAt'        => '0',
                'preload'       => '',

                // Mute Button
                'mute_button'           => '',
                'mute_button_style'     => '',

                // Play Button
                'play_button'           => '',
                'play_button_action'    => '',

                // Lightbox
                'lightbox_source'       => '',
                'lightbox_url'          => '',
                'lightbox_mp4'          => '',
                'lightbox_webm'         => '',
                'lightbox_ogg'          => '',

	            // Video Glitch Effect
                'glitch_effect'         => ''

            ), $this->atts ) );

            if( empty( $id ) || empty( $section ) ) {
                // return;
            }

            // assign ID
            $this->id = esc_attr( ut_get_unique_id('', true) );

            // check video source
            if( $source == 'default' ) {

                if( empty( $video_bg_url ) ) {

                    return;

                } else {

                    if( $this->is_youtube( $video_bg_url ) ) {

                        // assign variables
                        $source = 'youtube';
                        $video  = $video_bg_url;

                    }


                    if( $this->is_vimeo( $video_bg_url ) ) {

                        // assign variables
                        $source = 'vimeo';
                        $video  = $video_bg_url;

                    }

                }

            }

            // youtube
            if( $source == 'youtube' && !empty( $video ) ) {

	            // enqueue script
	            $this->youtube = true;

                return $this->youtube_player( $video );

            }

            // vimeo
            if( $source == 'vimeo' && !empty( $video ) ) {

	            // enqueue script
	            $this->vimeo = true;

                return $this->vimeo_player( $video );

            }

            // selfhosted
            if( $source == 'selfhosted' ) {

                if( $glitch_effect == 'yes' || $glitch_effect == 'on' ) {

	                return $this->selfhosted_player_glitch();

                } else {

	                return $this->selfhosted_player();

                }

            }

            return false;

        }

        public function print_script() {

            if ( ! $this->add_script ) {
                return;
            }

            if ( $this->vimeo ) {
                wp_enqueue_script('ut-vimeo-api');
                wp_enqueue_script('ut-bgvid-vimeo');
            }

            wp_enqueue_script('ut-video-lib');

        }

        public function print_tooltip() {

            global $ut_video_tooltip;

            if( !$ut_video_tooltip ) {

                echo '<div id="ut-video-play-tooltip" class="ut-video-play-tooltip">';

                    echo '<p>' . esc_html__( 'This Website is playing a video with an active Sound.', 'ut_shortcodes' ) . '</p>';
                    echo '<a id="ut-confirm-video-play" href="#" class="bklyn-btn  bklyn-btn-mini">OK</a>';

                echo '</div>';

                $ut_video_tooltip = true;

            }

        }

    }

endif;