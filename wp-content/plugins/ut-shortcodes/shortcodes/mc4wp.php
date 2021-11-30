<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'UT_MC4WP_Shortcode' ) ) {
	
    class UT_MC4WP_Shortcode {
        
        private $shortcode;
            
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_mail_chimp_shortcode';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Mailchimp Plugin', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'MailChimp is the world’s largest marketing automation platform.', 'ut_shortcodes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/mail-chimp.png',
                    'category'        => 'Plugins',
                    'class'           => 'ut-vc-icon-module ut-plugin-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Select Mail Chimp Form', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'If you are looking for color options, please check "Theme Options" > "Advanced" > "Mailchimp Colors". Once you created a skin, please edit your mailchimp form, switch to the "Appearance" tab and select your skin.', 'ut_shortcodes' ),
                            'param_name'        => 'form_id',
                            'group'             => 'General',
                            'value'             => ut_get_mailchimp_forms()
                        ),


                        // Animation
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
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        )

                    )

                )

            ); // end mapping
                

        
        }
        
        function ut_create_shortcode( $atts ) {
                
            extract( shortcode_atts( array (
                'form_id'            => '',
                'effect'             => '',     
                'animate_once'       => 'yes',
                'animate_mobile'     => false,
                'animate_tablet'     => false,
                'delay'              => 'false',
                'delay_timer'        => '100',
                'animation_duration' => '',                
                'css'                => ''
            ), $atts ) ); 
            
            if( !$form_id ) {                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . esc_html__( 'No Mail Chimp Form selected!', 'ut_shortcodes' ) . '</div>';                
            }
            
            if( defined( 'WPB_VC_VERSION' ) ) {                 
                
                // animation effect 
                $classes    = array();
                $attributes = array();            

                if( !empty( $effect ) && $effect != 'none' ) {

                    $attributes['data-effect']      = esc_attr( $effect );
                    $attributes['data-animateonce'] = esc_attr( $animate_once );
                    $attributes['data-delay'] = $delay == 'true' ? esc_attr( $delay_timer ) : 0;

                    if( !empty( $animation_duration ) ) {
                        $attributes['data-animation-duration'] = esc_attr( ut_add_timer_unit( $animation_duration, 's' ) );    
                    }                

                    $classes[]  = 'ut-animate-element';
                    $classes[]  = 'animated';

                    if( !$animate_tablet ) {
                        $classes[]  = 'ut-no-animation-tablet';
                    }

                    if( !$animate_mobile ) {
                        $classes[]  = 'ut-no-animation-mobile';
                    }

                    if( $animate_once == 'infinite' ) {
                        $classes[]  = 'infinite';
                    }

                }

                // attributes string
                $attributes = implode(' ', array_map(
                    function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                    $attributes,
                    array_keys( $attributes )
                ) );
                                
                return '<div ' . $attributes . ' class="' . implode(" ", $classes ) . ' wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . do_shortcode( '[mc4wp_form id="' . esc_attr( $form_id ) . '"]' ) . '</div>';
                
            }  
            
            /* start output */
            return do_shortcode( $content );
        
        }
            
    }

}

new UT_MC4WP_Shortcode;