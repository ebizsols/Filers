<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_Word_rotator' ) ) {
	
    class UT_Word_rotator {
        
        private $shortcode;

        private $rotator_words;

        private $rotator_id;

        private $rotator_type;

        private $atts;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_rotate_words';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );

		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Word Rotator', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Super simple rotating text.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    'category'        => 'Information',
                    // 'icon'            => 'ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/word-rotator.png',
                    'class'           => 'ut-vc-icon-module ut-information-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Effect', 'ut_shortcodes' ),
                            'param_name'        => 'word_effect',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Fade' , 'ut_shortcodes' ) => 'fade',
                                esc_html__( 'Swap (no transition)', 'ut_shortcodes' ) => 'swap',
                                esc_html__( 'Typewriter'  , 'ut_shortcodes' ) => 'typewriter',
                                // esc_html__( 'Reveal'  , 'ut_shortcodes' ) => 'reveal',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Display Typewriter Cursor?', 'ut_shortcodes' ),
                            'param_name'        => 'display_cursor',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'yes, please!', 'ut_shortcodes' ) => 'on',
                                esc_html__( 'no, thanks!', 'ut_shortcodes' )  => 'off',
                            ),
                            'dependency' => array(
                                'element' => 'word_effect',
                                'value'   => array( 'typewriter' ),
                            ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Cursor', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'String value to use as the cursor. Default Pipe Character.', 'ut_shortcodes'),
                            'param_name'        => 'cursor',
                            'group'             => 'General',
                            'dependency' => array(
                                'element' => 'display_cursor',
                                'value'   => array( 'on' ),
                            ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Rotation Time', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'value in miliseconds, eg "3000"', 'ut_shortcodes'),
                            'param_name'        => 'timer',
                            'group'             => 'General',
                            'dependency' => array(
                                'element' => 'word_effect',
                                'value'   => array( 'fade', 'swap' ),
                            ),
                        ),
                        array(
                            'type'              => 'textarea',
                            'heading'           => esc_html__( 'Words', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Each new line will be a separate Word.', 'ut_shortcodes'),
                            'admin_label'       => true,
                            'param_name'        => 'content',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Display', 'ut_shortcodes' ),
                            'param_name'        => 'display',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'Inline' , 'ut_shortcodes' ) => 'inline',
                                esc_html__( 'Block'  , 'ut_shortcodes' ) => 'block',
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Wait for Preloader', 'ut_shortcodes' ),
                            'param_name'        => 'wait_for_preloader',
                            'group'             => 'General',
                            'value'             => array(
                                esc_html__( 'yes, please!' , 'ut_shortcodes' ) => 'true',
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => 'false',
                            ),
                        ),

                        // Font Settings
                        array(
                            'type'       => 'dropdown',
                            'heading'    => esc_html__( 'Element Tag', 'ut_shortcodes' ),
                            'param_name' => 'tag',
                            'group'      => 'Font Settings',
                            'value'      => array(
                                esc_html__( 'span', 'ut_shortcodes' ) => 'span',
                                esc_html__( 'h1', 'ut_shortcodes' )   => 'h1',
                                esc_html__( 'h2', 'ut_shortcodes' )   => 'h2',
                                esc_html__( 'h3', 'ut_shortcodes' )   => 'h3',
                                esc_html__( 'h4', 'ut_shortcodes' )   => 'h4',
                                esc_html__( 'h5', 'ut_shortcodes' )   => 'h5',
                                esc_html__( 'h6', 'ut_shortcodes' )   => 'h6',
                                esc_html__( 'Bold Large Text', 'ut_shortcodes' ) => 'div',
                            )
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Font Size', 'ut_shortcodes' ),
                            'param_name'        => 'font_size',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-12',
                            'value'             => array(
                                'min'   => '8',
                                'max'   => '200',
                                'step'  => '1',
                                'unit'  => 'px'
                            ),
                            'dependency' => array(
                                'element' => 'tag',
                                'value_not_equal_to' => array('div')
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Large Desktop Font Size', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Viewport min width 1201px. Also applies to tablet and mobile landscape mode.', 'ut_shortcodes' ),
                            'param_name'        => 'font_size_vw',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                'default'   => '0',
                                'min'       => '0',
                                'max'       => '20',
                                'step'      => '0.1',
                                'unit'      => 'vw',
                            ),
                            'dependency' => array(
                                'element' => 'tag',
                                'value' => 'div',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Tablet and small Desktop Font Size', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Viewport 768px to 1200px. Only applies to portrait mode.', 'ut_shortcodes' ),
                            'param_name'        => 'font_size_tablet_vh',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                'default'   => '4',
                                'min'       => '0',
                                'max'       => '20',
                                'step'      => '0.1',
                                'unit'      => 'vh',
                            ),
                            'dependency' => array(
                                'element' => 'tag',
                                'value' => 'div',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Mobile Font Size', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Viewport max width 767px. Only applies to portrait mode.', 'ut_shortcodes' ),
                            'param_name'        => 'font_size_mobile_vh',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                'default'   => '4',
                                'min'       => '0',
                                'max'       => '20',
                                'step'      => '0.1',
                                'unit'      => 'vh',
                            ),
                            'dependency' => array(
                                'element' => 'tag',
                                'value' => 'div',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Line Height', 'ut_shortcodes' ),
                            'param_name'        => 'line_height',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'value'             => array(
                                'default'   => '100',
                                'min'       => '80',
                                'max'       => '300',
                                'step'      => '5',
                                'unit'      => '%'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Letter Spacing', 'ut_shortcodes' ),
                            'param_name'        => 'letter_spacing',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-4',
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
                            'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                            'param_name'        => 'font_weight',
                            'group'             => 'Font Settings',
                            'edit_field_class'  => 'vc_col-sm-4',
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
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Font Style', 'ut_shortcodes' ),
                            'param_name'        => 'font_style',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'Select Font Style' , 'ut_shortcodes' ) => '',
                                esc_html__( 'normal' , 'ut_shortcodes' ) => 'normal',
                                esc_html__( 'italic' , 'ut_shortcodes' ) => 'italic',
                            ),
                        ),
                        array(
                            'type'       => 'dropdown',
                            'heading'    => esc_html__( 'Title Text Transform', 'ut_shortcodes' ),
                            'param_name' => 'text_transform',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'      => 'Font Settings',
                            'value'      => array(
                                esc_html__( 'Select Text Transform', 'ut_shortcodes' ) => '',
                                esc_html__( 'none', 'ut_shortcodes' )                  => 'none',
                                esc_html__( 'capitalize', 'ut_shortcodes' )            => 'capitalize',
                                esc_html__( 'uppercase', 'ut_shortcodes' )             => 'uppercase',
                                esc_html__( 'lowercase', 'ut_shortcodes' )             => 'lowercase'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Text Align', 'ut_shortcodes' ),
                            'param_name'        => 'text_align',
                            'edit_field_class'  => 'vc_col-sm-4',
                            'group'             => 'Font Settings',
                            'value'             => array(
                                esc_html__( 'left' , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'center' , 'ut_shortcodes' ) => 'center',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                        ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Tablet Text Align', 'ut_shortcodes' ),
		                    'param_name'        => 'text_align_tablet',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'group'             => 'Font Settings',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger' , 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'left' , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'center' , 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
	                    array(
		                    'type'              => 'dropdown',
		                    'heading'           => esc_html__( 'Mobile Text Align', 'ut_shortcodes' ),
		                    'param_name'        => 'text_align_mobile',
		                    'edit_field_class'  => 'vc_col-sm-4',
		                    'group'             => 'Font Settings',
		                    'value'             => array(
			                    esc_html__( 'inherit from larger' , 'ut_shortcodes' ) => 'inherit',
			                    esc_html__( 'left' , 'ut_shortcodes' ) => 'left',
			                    esc_html__( 'center' , 'ut_shortcodes' ) => 'center',
			                    esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
		                    ),
	                    ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Font Color', 'ut_shortcodes' ),
                            'param_name'        => 'font_color',
                            'group'             => 'Font Settings'
                        ),

                        // Text Effects
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Activate Glow Effect?', 'ut_shortcodes' ),
                            'param_name'        => 'glow_effect',
                            'group'             => 'Glow & Stroke',
                            'value'             => array(
                                esc_html__( 'no', 'ut_shortcodes' )  => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'              => 'colorpicker',
                            'heading'           => esc_html__( 'Glow Color', 'ut_shortcodes' ),
                            'param_name'        => 'glow_color',
                            'group'             => 'Glow & Stroke',
                            'dependency'        => array(
                                'element' => 'glow_effect',
                                'value'   => array('yes'),
                            )
                        ),
                        array(
                            'type'       => 'dropdown',
                            'heading'    => esc_html__( 'Activate Text Stroke?', 'ut_shortcodes' ),
                            'param_name' => 'stroke_effect',
                            'group'      => 'Glow & Stroke',
                            'value'      => array(
                                esc_html__( 'no', 'ut_shortcodes' )  => 'no',
                                esc_html__( 'yes', 'ut_shortcodes' ) => 'yes'
                            )
                        ),
                        array(
                            'type'       => 'colorpicker',
                            'heading'    => esc_html__( 'Stroke Color', 'ut_shortcodes' ),
                            'param_name' => 'stroke_color',
                            'group'      => 'Glow & Stroke',
                            'dependency' => array(
                                'element' => 'stroke_effect',
                                'value'   => array( 'yes' ),
                            )
                        ),
                        array(
                            'type'       => 'range_slider',
                            'heading'    => esc_html__( 'Stroke Width', 'ut_shortcodes' ),
                            'param_name' => 'stroke_width',
                            'group'      => 'Glow & Stroke',
                            'value'      => array(
                                'default' => '1',
                                'min'     => '1',
                                'max'     => '3',
                                'step'    => '1',
                                'unit'    => 'px'
                            ),
                            'dependency' => array(
                                'element' => 'stroke_effect',
                                'value'   => array( 'yes' ),
                            )
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

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),


                    )

                )

            ); /* end mapping */
        
        }


        function rotator_settings_json() {

            /**
             * @var string $word_effect
             * @var string $cursor
             * @var string $timer
             * @var string $glitch
             * @var string $wait_for_preloader
             */

            extract( shortcode_atts( array (

                'word_effect'        => 'fade',
                'cursor'             => '|',
                'timer'              => 2000,
                'glitch'             => 'off',
                'wait_for_preloader' => 'true'

            ), $this->atts ) );


            $effect_timer = ( $timer <= 400 ) ? $timer / 2 : 400;
            $effect_timer = $word_effect == 'fade' ? $effect_timer : 0;

            $json = array(

                'word_effect'        => $word_effect,
                'timer'              => $timer,
                'effect_timer'       => $effect_timer,
                'glitch'             => $glitch,
                'cursor'             => $cursor,
                'wait_for_preloader' => filter_var( $wait_for_preloader, FILTER_VALIDATE_BOOLEAN )

            );

            return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

        }

        // @todo compare 5 move to sc.plugin
        function javascript_for_reveal_effect() {

            /**
             * @var string $glitch
             * @var number $timer
             */

            extract( shortcode_atts( array (
                'timer'          => 2000,
            ), $this->atts ) );

            $classes = array();

            if( $this->rotator_type != 'reveal' ) {
                return '';
            }

            ob_start(); ?>

            <script type="text/javascript">

                /* <![CDATA[ */
                (function($){

                    "use strict";

                    var ut_rotator_words = [<?php echo $this->rotator_words; ?>],
                        counter = 1,
                        block_count = true;

                    // check if word rotator is located inside 3image fader
                    if( $("#<?php echo $this->rotator_id; ?>").closest("#ut-hero").length && $("#<?php echo $this->rotator_id; ?>").closest("#ut-hero").hasClass("ut-hero-imagefader-background") ) {

                        $("ul.ut-image-fader li", "#ut-hero").on("webkitAnimationStart mozAnimationStart MSAnimationStart oanimationstart animationstart", function(){

                            var indx = $(this).index();

                            if( counter > 0 ) {

                                var word = ut_rotator_words[indx],
                                    data_word = word.replace(/<(?:.|\n)*?>/gm, '');

                                $("#<?php echo $this->rotator_id; ?> .ut-word-rotator").fadeOut(726, function(){

                                    $("#<?php echo $this->rotator_id; ?> .ut-word-rotator").html('<div class="<?php echo implode( " ", $classes ); ?>" data-title="'+ data_word +'">' + word + '</div>').fadeIn(726);

                                });


                            }

                            counter++;

                        });

                        $("ul.ut-image-fader li", "#ut-hero").on("animationiteration", function(){

                            var indx = $(this).index();

                            var word = ut_rotator_words[indx],
                                data_word = word.replace(/<(?:.|\n)*?>/gm, '');

                            $("#<?php echo $this->rotator_id; ?> .ut-word-rotator").fadeOut(726,function(){

                                $("#<?php echo $this->rotator_id; ?> .ut-word-rotator").html('<div class="<?php echo implode( " ", $classes ); ?>" data-title="'+ data_word +'">' + word + '</div>').fadeIn(726);

                            });

                        });

                    } else {

                        var $word_rotator_revealer = $(".ut-element-revealer", "#<?php echo $this->rotator_id; ?>");

                        $word_rotator_revealer.on("webkitAnimationStart mozAnimationStart MSAnimationStart oanimationstart animationstart", function( event, target ){

                            if( !block_count ) {

                                var word = ut_rotator_words[counter = (counter + 1) % ut_rotator_words.length];
                                block_count = true;

                            }

                            $word_rotator_revealer.delay( 900 ).queue(function () {

                                $("#<?php echo $this->rotator_id; ?> .ut-word-rotator > div:first-child").html(word);
                                $word_rotator_revealer.dequeue();

                            });

                        });

                        $word_rotator_revealer.on('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {

                            $word_rotator_revealer.removeClass('inView').delay( 4000 ).queue(function () {

                                $word_rotator_revealer.data('inview', false).dequeue();
                                block_count = false;

                            });

                        });

                    }

                })(jQuery);

                /* ]]> */
            </script>

            <?php

            return ob_get_clean();


        }

        function ut_create_shortcode( $atts, $content = NULL ) {

            global $_ut_hero_active;

            $this->atts = $atts;

            /**
             * @var string $word_effect
             * @var string $timer
             * @var string $display_cursor
             * @var string $font_source
             * @var string $google_fonts
             * @var string $websafe_fonts
             * @var string $custom_fonts
             * @var string $tag
             * @var string $font_size
             * @var string $font_size_vw
             * @var string $font_size_tablet_vh
             * @var string $font_size_mobile_vh
             * @var string $text_align
             * @var string $text_align_tablet
             * @var string $text_align_mobile
             * @var string $font_color
             * @var string $text_transform
             * @var number $line_height
             * @var number $letter_spacing
             * @var string $glow_color
             * @var string $stroke_effect
             * @var string $stroke_color
             * @var string $stroke_width
             * @var string $display
             * @var string $wait_for_preloader
             * @var string $css
             * @var string $class
             */

            extract( shortcode_atts( array (
                'word_effect'         => 'fade',
                'timer'               => 2000,
                'display_cursor'      => 'on',
                'tag'                 => 'span',
                'font_size'           => '',
                'font_size_vw'        => '',
                'font_size_tablet_vh' => '4',
                'font_size_mobile_vh' => '4',
                'line_height'         => '100',
                'letter_spacing'      => '',
                'font_weight'         => '',
                'text_transform'      => '',
                'text_align'          => '',
                'text_align_tablet'   => 'inherit',
                'text_align_mobile'   => 'inherit',
                'font_style'          => '',
                'font_color'          => '',
                'glow_effect'         => '',
                'glow_color'          => '',
                'stroke_effect'       => '',
                'stroke_color'        => '#000',
                'stroke_width'        => '1',
                'display'             => '',

                // Animation
                'effect'              => '',
                'animate_once'        => 'yes',
                'animate_tablet'      => 'no',
                'animate_mobile'      => 'no',
                'delay'               => 'no',
                'delay_timer'         => '100',
                'animation_duration'  => '',
                'animation_between'   => '',

                // Wait for Preloader
                'wait_for_preloader'  => 'true',

                'css'               => '',
                'class'             => ''

            ), $this->atts ) );
            
            $classes    = array();
            $classes[]  = $class;

            if( $tag == 'div' ) {

                $classes[] = 'ut-skip-flowtype';

            }

            // hide cursor for typewriter
            if( $display_cursor == 'off' ) {

                $classes[] = 'ut-word-rotator-no-cursor';

            }


            // animation attributes
            $attributes = array();

            // animation effect
            $dataeffect = NULL;
            $outer_classes = array( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, '' ), $this->shortcode, $atts ) );

            if( !empty( $effect ) && $effect != 'none' ) {

                $attributes['data-effect']      = esc_attr( $effect );
                $attributes['data-animateonce'] = esc_attr( $animate_once );
                $attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;

                if( $animate_once == 'infinite' && !empty( $animation_between ) ) {

                    if( strpos($animation_between, 's') === true ) {
                        $animation_between = str_replace('s' , '', $animation_between);
                    }

                    $attributes['data-animation-between'] = esc_attr( $animation_between );

                }

                if( !empty( $animation_duration ) ) {

                    $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );

                }

                $outer_classes[]  = 'ut-animate-element';
                $outer_classes[]  = 'animated';

                if( $animate_tablet ) {
                    $outer_classes[]  = 'ut-no-animation-tablet';
                }

                if( $animate_mobile ) {
                    $outer_classes[]  = 'ut-no-animation-mobile';
                }

                if( $animate_once == 'infinite' && empty( $animation_between ) ) {
                    $outer_classes[]  = 'infinite';
                }

            }

            $attributes = implode(' ', array_map(
                function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                $attributes,
                array_keys( $attributes )
            ) );

            /* split up words */
            if( $_ut_hero_active ) {

                $words = explode( ',' , str_replace(',<br />' , ',' , $content) );

            } else {

                if( strpos( $content, '<br />' ) ) {

	                $words = explode( '<br />', $content );

                } else if( strpos( $content, "\n" ) ) {

	                $words = explode( "\n", $content );

                } else {

                    $words = explode( ',' , str_replace(',<br />' , ',' , $content) );

                }

            }

            /* final rotator word variable*/
            $rotator_words = array();

            /* loop through word array and concatenate final string*/
            foreach( $words as $key => $word ) {                
                
				$rotator_words[] = json_encode( str_replace(array("\r", "\n"), '', $word ) );
				
            }

	        $rotator_words = implode( ',', $rotator_words );

            /* unique ID */
            $id     = uniqid("ut_word_rotator_");
            $tag_id = uniqid("ut_word_rotator_");

            // Design Options Gradient
            $vc_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, '.' ), $this->shortcode, $atts );

	        $css = '';
            $css .= ut_add_gradient_css( $vc_class, $atts );

            if( $tag != 'ut-bold' && $font_size ) {

                if( is_numeric( $font_size ) ) {

                    $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { font-size: ' . $font_size . 'px; }';

                } else {

                    $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { font-size: ' . $font_size . '; }';

                }

            }

            if( $tag == 'div' && $font_size_vw ) {

                $css .= '@media (min-width: 1025px) { #' . $id . ' ' . $tag . '.ut-word-rotator { font-size: ' . $font_size_vw . 'vw; } }';
                $css .= '@media (max-width: 1200px) and (orientation: landscape) { #' . $id . ' ' . $tag . '.ut-word-rotator { font-size: ' . $font_size_vw . 'vw; } }';

            }

            if( $tag == 'div' && $font_size_tablet_vh ) {

                $css .= '@media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) { #' . $id . ' ' . $tag . '.ut-word-rotator { font-size: ' . $font_size_tablet_vh . 'vh; } }';

            }

            if( $tag == 'div' && $font_size_mobile_vh ) {

                $css .= '@media (max-width: 767px) and (orientation: portrait) { #' . $id . ' ' . $tag . '.ut-word-rotator { font-size: ' . $font_size_mobile_vh . 'vh; } }';

            }

            if( $line_height ) {
                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { line-height: ' . $line_height . '%; }';
            }

            if( $letter_spacing ) {
                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { letter-spacing: ' . $letter_spacing . 'em; }';
            }

            if( $font_weight ) {
                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { font-weight: ' . $font_weight . '; }';
            }

            if( $font_style ) {
                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { font-style: ' . $font_style . '; }';
            }

            if( $text_transform ) {
                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { text-transform: ' . $text_transform . '; }';
            }

	        $text_align_tablet = $text_align_tablet == 'inherit' ? $text_align : $text_align_tablet;
	        $text_align_mobile = $text_align_mobile == 'inherit' ? $text_align_tablet : $text_align_mobile;

            if( $text_align ) {
                $css .= '#' . $id . ' { text-align: ' . $text_align . '; }';
            }
            if( $text_align_tablet ) {
                $css .= '@media (min-width: 768px) and (max-width: 1024px) { #' . $id . ' { text-align: ' . $text_align_tablet . '; } }';
            }
            if( $text_align_mobile ) {
                $css .= '@media (max-width: 767px) { #' . $id . ' { text-align: ' . $text_align_mobile . '; } }';
            }

            if( $font_color ) {
                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { color: ' . $font_color . ' !important; }';
            }
            
            if( $display ) {
                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { display: ' . $display . '; }';
            }

            if( $glow_color ) {

                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator { 
                        -webkit-text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px black;
                           -moz-text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px black;
                                text-shadow: 0 0 20px ' . $glow_color . ',  2px 2px 3px black; 
                }';

            }

            if( $stroke_effect == 'yes' ) {

                $css .= '#' . $id . ' ' . $tag . '.ut-word-rotator {
                    -moz-text-stroke-color: ' . $stroke_color .';
                    -webkit-text-stroke-color: ' . $stroke_color .';
                    -moz-text-stroke-width: ' . $stroke_width .'px;  
                    -webkit-text-stroke-width: ' . $stroke_width .'px;	            
	            }';

            }

            // Typewriter Effect
            if( $word_effect == 'typewriter' ) {

                $this->rotator_type = 'typewriter';
                $this->rotator_id   = $tag_id;

	            $classes[] = 'ut-word-rotator-typewriter';

            } elseif( $word_effect == 'reveal' ) {

                $this->rotator_type = 'reveal';
                $this->rotator_id = $id;

                $classes[] = 'ut-element-revealer-parent';

            // Fade and Swap
            } else {

                $classes[] = 'ut-word-rotator-classic';

                $this->rotator_type = 'default';
                $this->rotator_id = $id;

            }

            /* start output */

	        ob_start(); ?>

            <script type="text/javascript">

                if( window.ut_rotator_words === undefined  ) {

                    window.ut_rotator_words = [];

                }

                window.ut_rotator_words['<?php echo $this->rotator_id; ?>'] = [<?php echo $rotator_words; ?>];

            </script>

	        <?php

	        $output = ob_get_clean();

            /* attach css */
            if( !empty( $css ) ) {
                $output .= ut_minify_inline_css( '<style type="text/css">' . $css . '</style>' );
            }

            $content = '<' . $tag . ' id="' . $tag_id . '" class="' . implode( ' ', $classes ) . ' ut-word-rotator" data-id="' . $this->rotator_id . '" data-settings="' . $this->rotator_settings_json() . '">';

            $content .= '<div data-title="' . esc_attr( $words[0] ) . '">' . $words[0] . '</div>';

            if( $word_effect == 'reveal' ) {

                $content .= '<div class="ut-element-revealer ut-reveal-in-ltor ut-reveal-in-front ut-element-revealer-default ut-element-revealer-quantic ut-reveal-in-before ut-reveal-in-after"></div>';

            }

            $content .= '</' . $tag . '>';
                
            return $output . '<div id="' . $id . '" class="ut-word-rotator-wrap wpb_content_element ' . implode( ' ', $outer_classes ) . '" ' . $attributes . '>' . $content . '</div>';
            
        
        }
            
    }

}

new UT_Word_rotator;