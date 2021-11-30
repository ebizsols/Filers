<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to create unique ID without sleep
 *
 * @param     string    $prefix
 * @param     boolean   $entropy
 *
 * @return    string
 *
 * @access    public
 * @since     5.0.0
 * @version   1.0.0
 */

if( !function_exists( 'ut_get_unique_id' ) ) {

    function ut_get_unique_id($prefix = '', $entropy = false)
    {

        $uniqueId = uniqid($prefix, $entropy);
        return str_replace(array('.', '+'), '', $uniqueId);

    }

}

/*
 * Helper Function: Removes multiple
 */

if ( !function_exists( 'ut_nl2br_special' ) ) {

	function ut_nl2br_special($string){

		$string = nl2br($string); 
		$string = preg_replace("/(<br\ ?\/?>)+/", '<br />', $string);

		return $string;

	}

}

/*
 * Helper Function: Add px to value in case someone has not entered it
 */

if ( !function_exists( 'ut_add_px_value' ) ) {

	function ut_add_px_value( $option ) {

		if ( strpos( $option, 'px' ) !== false ) {

			return $option;

		} else {

			return $option . 'px';

		}

	}

}

/*
 * Helper Function: Add timer unit to value in case someone has not entered it
 */

if ( !function_exists( 'ut_add_timer_unit' ) ) {

	function ut_add_timer_unit( $option, $unit ) {

		if ( strpos( $option, $unit ) !== false ) {

			return $option;

		} else {

			return $option . $unit;

		}

	}

}


/*
 * Helper Function: Delete Timer Unit and Turn into MS
 */

if ( !function_exists( 'ut_delete_timer_unit' ) ) {

    function ut_delete_timer_unit( $option, $unit ) {

        if ( strpos( $option, $unit ) !== false ) {

            return str_replace( $unit, '', $option );

        } else {

            return $option . $unit;

        }

    }

}


/*
 * Placeholder Images
 */

if ( !function_exists( 'ut_img_asset_url' ) ) {
    
    function ut_img_asset_url( $image ) {
        
        return UT_SHORTCODES_URL . '/img/placeholder/' . $image;
        
    }

}



/*
 * Box Shadow Styles
 */

if ( !function_exists( 'ut_box_shadow' ) ) {
    
    function ut_box_shadow( $style, $color ) {
        
        ob_start(); ?>
        
        <?php if( $style == 'ut-box-shadow-1' ) : ?>

        .ut-box-shadow-1 {
            -webkit-box-shadow: 0 10px 6px -6px <?php echo $color; ?>;
               -moz-box-shadow: 0 10px 6px -6px <?php echo $color; ?>;
                    box-shadow: 0 10px 6px -6px <?php echo $color; ?>;
        }

        <?php endif; ?>
        
        <?php if( $style == 'ut-box-shadow-2' ) : ?>

        .ut-box-shadow-2:before, 
        .ut-box-shadow-2:after {
            background: <?php echo $color; ?>;
            -webkit-box-shadow: 0 15px 10px <?php echo $color; ?>;
               -moz-box-shadow: 0 15px 10px <?php echo $color; ?>;
                    box-shadow: 0 15px 10px <?php echo $color; ?>;
        }
        
        <?php endif; ?>

        <?php if( $style == 'ut-box-shadow-3' ) : ?>
        
        .ut-box-shadow-3:before {
            background: <?php echo $color; ?>;
            -webkit-box-shadow: 0 15px 10px <?php echo $color; ?>;
               -moz-box-shadow: 0 15px 10px <?php echo $color; ?>;
                    box-shadow: 0 15px 10px <?php echo $color; ?>;
        }

        <?php endif; ?>

        <?php if( $style == 'ut-box-shadow-4' ) : ?>

        .ut-box-shadow-4:after {
            background: <?php echo $color; ?>;
            -webkit-box-shadow: 0 15px 10px <?php echo $color; ?>;
               -moz-box-shadow: 0 15px 10px <?php echo $color; ?>;
                    box-shadow: 0 15px 10px <?php echo $color; ?>;
        }

        <?php endif; ?>

        <?php if( $style == 'ut-box-shadow-5' ) : ?>

        .ut-box-shadow-5:before, 
        .ut-box-shadow-5:after {
            background: <?php echo $color; ?>;
            -webkit-box-shadow: 0 35px 20px <?php echo $color; ?>;
               -moz-box-shadow: 0 35px 20px <?php echo $color; ?>;
                    box-shadow: 0 35px 20px <?php echo $color; ?>;
        }

        <?php endif; ?>

        <?php if( $style == 'ut-box-shadow-6' ) : ?>

        .ut-box-shadow-6:before, 
        .ut-box-shadow-6:after {
            -webkit-box-shadow:0 0 20px <?php echo $color; ?>;
               -moz-box-shadow:0 0 20px <?php echo $color; ?>;
                    box-shadow:0 0 20px <?php echo $color; ?>;
        }
        
        <?php endif; ?>

        <?php if( $style == 'ut-box-shadow-7' ) : ?>

        .ut-box-shadow-7:before, 
        .ut-box-shadow-7:after {
            -webkit-box-shadow:0 0 20px <?php echo $color; ?>;
               -moz-box-shadow:0 0 20px <?php echo $color; ?>;
                    box-shadow:0 0 20px <?php echo $color; ?>;
        }

        <?php endif; ?>

        <?php if( $style == 'ut-box-shadow-8' ) : ?>

        .ut-box-shadow-8:before, 
        .ut-box-shadow-8:after {
            -webkit-box-shadow:0 0 20px <?php echo $color; ?>;
               -moz-box-shadow:0 0 20px <?php echo $color; ?>;
                    box-shadow:0 0 20px <?php echo $color; ?>;
        }

        <?php endif; ?>

        <?php return ob_get_clean();         
                
    }

}

/*
 * Extra Attachment Fields
 *
 * @param $form_fields array, fields to include in attachment form
 * @param $post object, attachment record in database
 * @return $form_fields, modified form fields
 *
 * @access    public
 * @version   1.0
 */

if ( !function_exists( 'ut_attachment_extra_fields' ) ) {

    function ut_attachment_extra_fields( $form_fields, $post ) {

        $form_fields['ut-attachment-url'] = array(
            'label' => 'Project URL',
            'input' => 'text',
            'value' => get_post_meta( $post->ID, 'ut_attachment_url', true ),
            'helps' => 'Add an option Project URL. Will be used for different theme features.',
        );

        if( function_exists('ot_type_upload_id') ) {

            // Alternate Image
            ob_start();

            ot_type_upload_id(array(

                'field_name' => "attachments[$post->ID][ut-morph-full-url]",
                'field_id' => "attachments-$post->ID-ut-morph-full-url",
                'field_value' => get_post_meta($post->ID, 'ut_morph_full_url', true),
                'field_class' => '',
                'post_id' => ''

            ));

            $html = ob_get_clean();

            $form_fields['ut-morph-full-url'] = array(
                'label' => 'Alternate Morphbox Image',
                'input' => 'html',
                'html' => $html,
                'helps' => 'Assign an alternate image for the Morphbox which display on zoom.',
            );

            // Caption Color
            ob_start();

            ot_type_colorpicker(array(

                'field_name' => "attachments[$post->ID][ut-morph-caption-color]",
                'field_id' => "attachments-$post->ID-ut-morph-caption-color",
                'field_value' => get_post_meta($post->ID, 'ut_morph_caption_color', true),
                'field_class' => '',
                'field_mode' => 'rgb',
                'post_id' => ''

            ));

            $html = ob_get_clean();

            $form_fields['ut-morph-caption-color'] = array(
                'label' => 'Morphbox Caption Color',
                'input' => 'html',
                'html' => $html,
                'helps' => 'Color for Caption and Close Button.',
            );

            // Caption Background Color
            ob_start();

            ot_type_colorpicker(array(

                'field_name' => "attachments[$post->ID][ut-morph-caption-background]",
                'field_id' => "attachments-$post->ID-ut-morph-caption-background",
                'field_value' => get_post_meta($post->ID, 'ut_morph_caption_background', true),
                'field_class' => '',
                'field_mode' => 'rgb',
                'field_position' => 'top left',
                'post_id' => ''

            ));

            $html = ob_get_clean();

            $form_fields['ut-morph-caption-background'] = array(
                'label' => 'Morphbox Caption Background Color',
                'input' => 'html',
                'html' => $html,
                'helps' => 'Backgroundcolor for Caption.',
            );

        }

        return $form_fields;

    }

    add_filter( 'attachment_fields_to_edit', 'ut_attachment_extra_fields', 10, 2 );

}

/**
 * Save values of Extra Attachment Fieldsin media uploader
 *
 * @param $post array, the post data for database
 * @param $attachment array, attachment fields from $_POST form
 * @return $post array, modified post data
 *
 * @access    public
 * @version   1.0
 */

if ( !function_exists( 'ut_attachment_extra_fields_save' ) ) {

    function ut_attachment_extra_fields_save( $post, $attachment ) {

        if( isset( $attachment['ut-attachment-url'] ) ) {
            update_post_meta( $post['ID'], 'ut_attachment_url', $attachment['ut-attachment-url'] );
        }

        if( isset( $attachment['ut-morph-full-url'] ) ) {
            update_post_meta( $post['ID'], 'ut_morph_full_url', $attachment['ut-morph-full-url'] );
        }

        if( isset( $attachment['ut-morph-caption-color'] ) ) {
            update_post_meta( $post['ID'], 'ut_morph_caption_color', $attachment['ut-morph-caption-color'] );
        }

        if( isset( $attachment['ut-morph-caption-background'] ) ) {
            update_post_meta( $post['ID'], 'ut_morph_caption_background', $attachment['ut-morph-caption-background'] );
        }

        return $post;
    }

    add_filter( 'attachment_fields_to_save', 'ut_attachment_extra_fields_save', 10, 2 );

}





/*
 * Add Autoplay for Ajax Video Request
 *
 * @access    public
 * @version   1.1
 */

if ( !function_exists( 'ut_ajax_player_autoplay' ) ) {

    function ut_ajax_player_autoplay( $provider, $url, $args ) {
        
        /* only execute for ajax action */
        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        
            if( strpos( $provider, 'vimeo' ) !== false ) {
                
                $provider = esc_url_raw( add_query_arg( 'autoplay', 1, $provider ) );
                
            }
            
            if( strpos( $provider, 'youtube' ) !== false || strpos($provider, 'youtu.be') !== false ) {
                
                $provider = esc_url_raw( add_query_arg( 'autoplay', 1, $provider ) );
                
            }            
        
        }
        
        return $provider;
        
    }
    
    add_filter('oembed_fetch_url', 'ut_ajax_player_autoplay', 90, 3);

}

/*
 * Add HTTP
 *
 * @access    public
 * @since     4.4.5
 */

if(!function_exists('ut_portfolio_add_http')) {

	function ut_portfolio_add_http( $url ) {
		
		if ( !preg_match("~^(?:f|ht)tps?://~i", $url ) ) {
			
            $url = is_ssl() ? "https://" . $url : "http://" . $url;
            
		}
        
		return esc_url_raw($url);
        
	}
	
}

/*
 * Extract URL from String
 *
 * @access    public
 * @since     4.4.5
 */

if( !function_exists('ut_extract_url_from_string') ) {
 
    function ut_extract_url_from_string( $string ) {
    
        $image_url = !empty( $string ) ? preg_match_all('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', $string , $match) : '';
        return isset($match[0][0]) ? ut_portfolio_add_http($match[0][0]) : '';
    
    }
    
}

/**
 * Check if given string is a youtube video
 *
 * @access    public 
 * @since     4.4.5
 * @version   1.0.0
 */
 
if ( ! function_exists( 'ut_video_is_youtube' ) ) :

    function ut_video_is_youtube( $video ) {
        
        return preg_match('~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x', trim($video) , $matches);

    }

endif;


/**
 * Extract Vimeo ID form URL
 *
 * @access    public 
 * @since     4.4.5
 * @version   1.0.0
 */

if ( ! function_exists( 'extract_vimeo_id' ) ) :

    function extract_vimeo_id( $url ) {

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

endif;


/**
 * Check if given string is a vimeo video
 *
 * @access    public 
 * @since     4.4,5
 * @version   1.0.0
 */
 
if ( ! function_exists( 'ut_video_is_vimeo' ) ) :

    function ut_video_is_vimeo( $video ) {
        
        return preg_match('/\/\/(www\.)?vimeo.com\/(\d+)($|\/)/', trim($video) , $matches);

    }

endif;


/*
 * CSS Minifier
 */

if ( !function_exists( 'ut_minify_inline_css' ) ) {
    
    function ut_minify_inline_css( $buffer ) { 
        
        $buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );
        $buffer = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer );
        
        return $buffer;
        
    }
        
}

/*
 * Filter Array Recursive
 */

if( !function_exists('array_filter_recursive') ) {

    function array_filter_recursive( $input ) { 
   
        foreach ($input as &$value) { 
            
            if( is_array( $value ) ) { 
                
                $value = array_filter_recursive( $value );
                 
            }
            
        }
            
        return array_filter( $input ); 
   
    } 

}

/*
 * Web Safe Font Markup
 */
if( !function_exists('get_websafe_font_css_family') ) {

    function get_websafe_font_css_family( $key ) {
        
        $websafe_fonts = array(
            'arial'         => 'Arial, Helvetica, sans-serif',
            'courier'       => '"Courier New", Courier, monospace',
            'comic'         => '"Comic Sans MS", cursive, sans-serif',
            'georgia'       => 'Georgia, serif',
            'helvetica'     => 'Helvetica, sans-serif',
            'impact'        => 'Impact, Charcoal, sans-serif',
            'lucida_sans'   => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
            'lucida_console'=> '"Lucida Console", Monaco, monospace',
            'palatino'      => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
            'tahoma'        => 'Tahoma, Geneva, sans-serif',
            'times'         => '"Times New Roman", Times, serif',
            'trebuchet'     => '"Trebuchet MS", Helvetica, sans-serif',
            'verdana'       => 'Verdana, Geneva, sans-serif'
        );
        
        return isset( $websafe_fonts[$key] ) ? $websafe_fonts[$key] : $websafe_fonts['arial'];
        
    }

}



/*
 * Array of available Font Settings
 *
 * since 4.4.6
 * version 1.0
 */

if ( !function_exists( '_vc_add_font_settings' ) ) {
    
    function _vc_add_font_settings( $prefix = NULL, $category = 'Font Settings', $defaults = array(), $exception = array() ) {
        
        $font_settings = array();
        
        if( !in_array( 'custom_fonts', $exception ) ) {
            
            $font_settings[] = array(
                'type'              => 'dropdown',
                'heading'           => esc_html__( 'Choose Font Source', 'ut_shortcodes' ),
                'param_name'        => $prefix . 'font_source',
                'group'             => $category,
                'value'             => array(
                    esc_html__( 'Theme Default', 'ut_shortcodes' ) => 'default',
                    esc_html__( 'Web Safe Fonts', 'ut_shortcodes' ) => 'websafe',
                    esc_html__( 'Google Font', 'ut_shortcodes' ) => 'google'                                            
                ),                                                                
            );
            
            $font_settings[] = array(
                'type'              => 'google_fonts',
                'param_name'        => $prefix . 'google_fonts',
                'value'             => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group'             => $category,
                'settings'          => array(
                    'fields' => array(
                        'font_family_description' => __( 'Select font family.', 'ut_shortcodes' ),
                        'font_style_description'  => __( 'Select font styling.', 'ut_shortcodes' ),
                    ),
                ),
                'dependency'        => array(
                    'element'           => $prefix . 'font_source',
                    'value'             => 'google',
                ),
            );
            
            $font_settings[] = array(
                'type'              => 'dropdown',
                'heading'           => esc_html__( 'Websafe Fonts', 'ut_shortcodes' ),
                'param_name'        => $prefix . 'websafe_fonts',
                'group'             => $category,
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
                    'element'           => $prefix . 'font_source',
                    'value'             => 'websafe',
                ),
                
            );
        
        }
        
        if( !in_array( 'font_color', $exception ) ) {
            
            $font_settings[] = array(
                'type'              => 'colorpicker',
                'heading'           => esc_html__( 'Font Color', 'ut_shortcodes' ),
                'param_name'        => $prefix . 'font_color',
                'group'             => $category,                								
            );
        
        }        
        
        if( !in_array( 'font_size', $exception ) ) {
            
            $default = !empty( $defaults['font_size'] ) ? $defaults['font_size'] : 10;
            $font_size_min = !empty( $defaults['font_size_min'] ) ? $defaults['font_size_min'] : 8;
            $font_size_max = !empty( $defaults['font_size_max'] ) ? $defaults['font_size_max'] : 200;
            
            $font_settings[] = array(
                'type'              => 'range_slider',
                'heading'           => esc_html__( 'Font Size', 'ut_shortcodes' ),
                'param_name'        => $prefix . 'font_size',
                'group'             => $category,
                'value'             => array(
                    'default'   => $default,
                    'min'       => $font_size_min,
                    'max'       => $font_size_max,
                    'step'      => '1',
                    'unit'      => 'px'
                ),								
            );
        
        }        
        
        if( !in_array( 'line_height', $exception ) ) {
            
            $default = !empty( $defaults['line_height'] ) ? $defaults['line_height'] : 150;
            
            $font_settings[] = array(
                'type'              => 'range_slider',
                'heading'           => esc_html__( 'Line_height', 'ut_shortcodes' ),
                'param_name'        => $prefix . 'line_height',
                'group'             => $category,
                'value'             => array(
                    'default'   => $default,
                    'min'       => '80',
                    'max'       => '300',
                    'step'      => '5',
                    'unit'      => '%'
                ),
                
            );
        
        }
        
        if( !in_array( 'text_transform', $exception ) ) {
            
            $font_settings[] = array(
                'type'              => 'dropdown',
                'heading'           => esc_html__( 'Title Text Transform', 'ut_shortcodes' ),
                'description'       => esc_html__( '(optional)' , 'ut_shortcodes' ),
                'param_name'        => $prefix . 'text_transform',
                'group'             => $category,
                'value'             => array(
                    esc_html__( 'Select Text Transform' , 'ut_shortcodes' ) => '',
                    esc_html__( 'capitalize' , 'ut_shortcodes' )            => 'capitalize',
                    esc_html__( 'uppercase', 'ut_shortcodes' )              => 'uppercase',
                    esc_html__( 'lowercase', 'ut_shortcodes' )              => 'lowercase'                                    
                ),
            );        
        
        }
        
        if( !in_array( 'font_weight', $exception ) ) {
            
            $font_settings[] = array(
                'type'              => 'dropdown',
                'heading'           => esc_html__( 'Font Weight', 'ut_shortcodes' ),
                'description'       => esc_html__( 'Please keep in mind, that the selected font needs to support the font weight.', 'ut_shortcodes' ),
                'param_name'        => $prefix . 'font_weight',
                'group'             => $category,
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
                    esc_html__( '900' , 'ut_shortcodes' )                => '900'
                ),
                'dependency'        => array(
                    'element'           => $prefix . 'font_source',
                    'value'             => array('websafe','default'),
                ),                                
            );
                
        }
        
        return $font_settings;
            
    }

}


/*
 * CSS3 Animation Effects
 */

if( !function_exists('ut_recognized_animation_effects') ) :

    function ut_recognized_animation_effects() {
     
      return apply_filters( 'ut_recognized_animation_effects', array(
        'flash'             => 'Flash',
        'bounce'            => 'Bounce',
        'shake'             => 'Shake',
        'tada'              => 'Tada',
        'swing'             => 'Swing',
        'wobble'            => 'Wobble',
        'wiggle'            => 'Wiggle',
        'pulse'             => 'Pulse',
        'slideInUp'         => 'Slide In Up',
        'slideInLeft'       => 'Slide In Left',
        'slideInRight'      => 'Slide In Right',
        'slideInDown'       => 'Slide In Down',
        'flip'              => 'Flip',
        'flipInX'           => 'Flip In X',
        'flipInY'           => 'Flip In Y',
        'fadeIn'            => 'Fade In',
        'fadeInUp'          => 'Fade In Up',
        'fadeInDown'        => 'Fade In Down',
        'fadeInLeft'        => 'Fade In Left',
        'fadeInRight'       => 'Fade In Right',
        'fadeInUpBig'       => 'Fade In Up Big',
        'fadeInDownBig'     => 'Fade In Down Big',
        'fadeInLeftBig'     => 'Fade In Left Big',
        'fadeInRightBig'    => 'Fade In Right Big',
        'bounceIn'          => 'Bounce In',
        'bounceInDown'      => 'Bounce In Down',
        'bounceInUp'        => 'Bounce In Up',
        'bounceInLeft'      => 'Bounce In Left',
        'bounceInRight'     => 'Bounce In Right',
        'rotateIn'          => 'Rotate In',
        'rotateInDownLeft'  => 'Rotate In Down Left',
        'rotateInDownRight' => 'Rotate In Down Right',
        'rotateInUpLeft'    => 'Rotate In Up Left',
        'rotateInUpRight'   => 'Rotate In Up Right',
        'lightSpeedIn'      => 'LightSpeed In',
        'hinge'             => 'Hinge',
        'rollIn'            => 'Roll In'
      ));
      
    }

endif;

/*
 * CSS3 Animation Effects
 */

if( !function_exists('ut_recognized_animation_effects_vc') ) :

    function ut_recognized_animation_effects_vc() {
     
      return apply_filters( 'ut_recognized_animation_effects_vc', array(
        'No Animation Effect'   => '',
        'Flash'                 => 'flash',
        'Bounce'                => 'bounce',
        'Shake'                 => 'shake',
        'Tada'                  => 'tada',
        'Swing'                 => 'swing',
        'Wobble'                => 'wobble',
        'Wiggle'                => 'wiggle',
        'Pulse'                 => 'pulse',
        'Slide In Up'           => 'slideInUp',
        'Slide In Left'         => 'slideInLeft',
        'Slide In Right'        => 'slideInRight',
        'Slide In Down'         => 'slideInDown',
        'Flip'                  => 'flip',
        'Flip In X'             => 'flipInX',
        'Flip In Y'             => 'flipInY',
        'Fade In'               => 'fadeIn',
        'Fade In Up'            => 'fadeInUp',
        'Fade In Down'          => 'fadeInDown',
        'Fade In Left'          => 'fadeInLeft',
        'Fade In Right'         => 'fadeInRight',
        'Fade In Up Big'        => 'fadeInUpBig',
        'Fade In Down Big'      => 'fadeInDownBig',
        'Fade In Left Big'      => 'fadeInLeftBig',
        'Fade In Right Big'     => 'fadeInRightBig',
        'Bounce In'             => 'bounceIn',
        'Bounce In Down'        => 'bounceInDown',
        'Bounce In Up'          => 'bounceInUp',
        'Bounce In Left'        => 'bounceInLeft',
        'Bounce In Right'       => 'bounceInRight',
        'Rotate In'             => 'rotateIn',
        'Rotate In Down Left'   => 'rotateInDownLeft',
        'Rotate In Down Right'  => 'rotateInDownRight',
        'Rotate In Up Left'     => 'rotateInUpLeft',
        'Rotate In Up Right'    => 'rotateInUpRight',
        'LightSpeed In'         => 'lightSpeedIn',
        'Hinge'                 => 'hinge',
        'Roll In'               => 'rollIn'
      ));
            
    }

endif;

/*
 * Custom "get"_comments_popup_link
 */
if( !function_exists('ut_get_comments_popup_link') ) {
    
    function ut_get_comments_popup_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
        
        global $wpcommentspopupfile, $wpcommentsjavascript;
     
        $id = get_the_ID();
     
        if ( false === $zero ) $zero = esc_html__( 'No Comments' , 'ut_shortcodes' );
        if ( false === $one ) $one = esc_html__( '1 Comment' , 'ut_shortcodes' );
        if ( false === $more ) $more = esc_html__( '% Comments' , 'ut_shortcodes' );
        if ( false === $none ) $none = esc_html__( 'Comments Off' , 'ut_shortcodes' );
     
        $number = get_comments_number( $id );
     
        $str = '';
     
        if ( 0 == $number && !comments_open() && !pings_open() ) {
            $str = '<span' . ((!empty($css_class)) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $none . '</span>';
            return $str;
        }
     
        if ( post_password_required() ) {
            $str = esc_html__('Enter your password to view comments . ' , 'ut_shortcodes' );
            return $str;
        }
     
        $str = '<a href="';
        if ( $wpcommentsjavascript ) {
            if ( empty( $wpcommentspopupfile ) )
                $home = home_url();
            else
                $home = get_option('siteurl');
            $str .= $home . '/' . $wpcommentspopupfile . '?comments_popup=' . $id;
            $str .= '" onclick="wpopen(this.href); return false"';
        } else { // if comments_popup_script() is not in the template, display simple comment link
            if ( 0 == $number )
                $str .= get_permalink() . '#respond';
            else
                $str .= get_comments_link();
            $str .= '"';
        }
     
        if ( !empty( $css_class ) ) {
            $str .= ' class="' . $css_class . '" ';
        }
        $title = the_title_attribute( array('echo' => 0 ) );
     
        $str .= apply_filters( 'comments_popup_link_attributes', '' );
     
        $str .= ' title="' . esc_attr( sprintf( esc_html__('Comment on %s' , 'ut_shortcodes'), $title ) ) . '">';
        $str .= ut_get_comments_number_str( $zero, $one, $more );
        $str .= '</a>';
         
        return $str;
    }
}

if( !function_exists('ut_get_comments_number_str') ) {
    
    function ut_get_comments_number_str( $zero = false, $one = false, $more = false, $deprecated = '' ) {
        if ( !empty( $deprecated ) )
            _deprecated_argument( __FUNCTION__, '1.3' );
     
        $number = get_comments_number();
     
        if ( $number > 1 )
            $output = str_replace('%', number_format_i18n($number), ( false === $more ) ? esc_html__('% Comments', 'ut_shortcodes') : $more);
        elseif ( $number == 0 )
            $output = ( false === $zero ) ? esc_html__('No Comments', 'ut_shortcodes') : $zero;
        else // must be one
            $output = ( false === $one ) ? esc_html__('1 Comment', 'ut_shortcodes') : $one;
     
        return apply_filters('comments_number', $output, $number);
    }
    
}

/*
 * Fix Shortcodes
 */
 
if( !function_exists('ut_fix_shortcodes') ) {
    
    function ut_fix_shortcodes( $content ){
    
        
        $block = join("|",array( "ut_custom_css", "ut_animate_image" , "ut_button" , "ut_alert" , "ut_one_sixth" , "ut_one_sixth_last" , "ut_one_fifth" , "ut_one_fifth_last" , "ut_one_fourth" , "ut_one_fourth_last" , "ut_one_third" , "ut_one_third_last" , "ut_one_half" , "ut_one_half_last" , "ut_two_thirds" ,
                                 "ut_two_thirds_last" , "ut_service_icon_box", "ut_three_fourth" , "ut_three_fourth_last" , "ut_probar" , "ut_highlight" , "ut_tabgroup" , "ut_tab" , "ut_togglegroup" , "ut_toggle" , "ut_rotate_words" , "ut_fw_section" , "ut_showcase" , "ut_service_box" , "ut_quote_rotator" , 
                                 "ut_quote" , "ut_quote_rotator_alt" , "ut_quote_alt" , "ut_service_column" , "ut_service_column_vertical" , "ut_count_up" , "ut_parallax_quote" , "ut_social" , "ut_social_media", "ut_client_group" , "ut_client" , "ut_single_quote" , "ut_highlight_section" , 'ut_video_testimonial', 'ut_video_testimonials',
                                 "video", "ut_custom_box", "contact-form-7", "ut_icon_tab", "ut_icon_tabs", "ut_pricing_table" ));
         
        $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
        $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
         
        return $rep;

    }
    
    add_filter( 'the_content', 'ut_fix_shortcodes' );
        
}

/*
 * Icons
 */

if( !function_exists('ut_create_icon') ) { 
 
    function ut_create_icon($atts, $content = null) {
                
        extract(shortcode_atts(array(
             
             'icon'     => 'fa-off',
             'size'     => 'fa-4',
             'color'    => '',
             'bgcolor'  => '#CCC',
             'border'   => 'none',
             'spin'     => '',
             'rotate'   => '',
             'link'     => '',
             'target'   => 'self',
             'align'    => 'alignnone',
             'class'    => ''    
             
        ), $atts));

            $finalicon = NULL;
            $iconclass = NULL;
            
            if( $border == 'none' ) {
                
                /* default icon */
                $classes    = array('icon' , 'size' , 'spin', 'rotate', 'class');
    
                $style = (!empty($color)) ? 'style="color:' . $color . '"' : '';
                
                $span_open  = '<span class="' . $align . ' ut-custom-icon">';
                $span_close = '</span>';
                
                foreach($atts as $key => $att) {
                    
                    if( !empty($att) && in_array($key, $classes) )
                    $iconclass .= $att . ' ';
                    
                }
                
                if(!empty($link)) {
                    
                    $finalicon .= $span_open  . '<a class="ut-custom-icon-link" target="_' . $target . '" href="' . $link . '"><i class="fa ' . trim($iconclass) . '" ' . $style . '></i></a>' .  $span_close;
                    
                } else {
                    
                    $finalicon .= $span_open  . '<i class="fa ' . trim($iconclass) . '" ' . $style . '></i>' .  $span_close;
                    
                }
                
            } else {
            
                
                /* stacked icon */
                $classes    = array('icon' , 'spin', 'rotate', 'class');
                $style      = (!empty($color)) ? 'style="color:' . $color . '"' : '';
                
                foreach($atts as $key => $att) {
                    
                    if( !empty($att) && in_array($key, $classes) )
                    $iconclass .= $att . ' ';
                    
                }
                
                /* start output */
                $finalicon .= '<span class="fa-stack ut-custom-icon ' . $size . ' ' . $align . '">';
                    
                    if(!empty($link)) { $finalicon .= '<a target="_' . $target . '" href="' . $link . '">'; }
                                                
                        $finalicon .= '<i class="fa fa-' . $border . ' fa-stack-2x" style="color:' . $bgcolor . '"></i>';
                        $finalicon .= '<i class="fa ' . trim($iconclass) . ' fa-stack-1x" ' . $style . '></i>';
                        
                    if(!empty($link)) { $finalicon .= '</a>'; }
                
                $finalicon .= '</span>';
            
            }
                    
        
        return $finalicon;
        
    }
    add_shortcode('ut_icon', 'ut_create_icon');

}

/*
 * Spacer
 */
 
if( !function_exists('ut_create_spacer') ) {  

    function ut_create_spacer( $atts ) {
        
        extract(shortcode_atts(array(            
                'class'  => '',
                'margin_bottom' => '',
                'margin_top' => ''        
            ), $atts));
            
            /* create style */
            $style = 'style="';
            
            if(!empty($margin_bottom)) {
                $margin_bottom = is_numeric($margin_bottom) ? $margin_bottom  . 'px' : $margin_bottom ; 
                $style .= 'margin-bottom: ' . $margin_bottom . ';';
            }
            
            if(!empty($margin_top)) {
                $margin_top = is_numeric($margin_top) ? $margin_top  . 'px' : $margin_top ; 
                $style .= 'margin-top: ' . $margin_top . ';';
            }
            
            $style .= '"';            
            
            return '<div class="ut-spacer ' . $class . '" ' . $style . '></div>';
            
    }
    
    add_shortcode('ut_spacer', 'ut_create_spacer');
    
}

/*
 * Alerts
 */
 
if( !function_exists('ut_create_alert') ) {  

    function ut_create_alert( $atts, $content = null ) {
        
        extract(shortcode_atts(array(
                'class'        => '',
                'color'        => 'white',
                'effect'       => '',
                'animate_once' => 'no',
            ), $atts));
            
            /* animation effect */
            $dataeffect = $animated = NULL;
            if( !empty( $effect ) ) {
    
                $dataeffect = 'data-effect="' . $effect . '" data-animateonce="' . $animate_once . '"';
                $animated   = 'ut-animate-element animated';
                
            }
            
            return '<div ' . $dataeffect . ' class="ut-alert ' . $color . ' ' . $class . ' ' . $animated . '">' . do_shortcode($content) . '</div>';
            
    }
    
    add_shortcode('ut_alert', 'ut_create_alert');
    
}




/*
 * Highlights
 */ 

if( !function_exists('ut_highlight') ) { 
 
    function ut_highlight($atts, $content = null) {
        
        extract(shortcode_atts(array(
             'class'     => '',
             'color'    => '',
             'bgcolor'    => ''
        ), $atts));
        
        $extraStyle = 'style="';
        
        if( !empty($color) ) {
            $extraStyle .= 'color: ' . $color . ' ;';
        }
        
        if( !empty($bgcolor) ) {
            $extraStyle .= 'background: ' . $bgcolor . ' ;';
        }
        
        $extraStyle .= '"';
        
        
        return '<span ' . $extraStyle . ' class="ut-highlight ' . $class . '">' . do_shortcode($content) . '</span>';
    }
    add_shortcode('ut_highlight', 'ut_highlight');
}

/*
 * Tabs
 */ 

if( !function_exists('ut_tabgroup') ) {

    function ut_tabgroup( $atts, $content ){
            
            extract(shortcode_atts(array(
                'width'        => '',
                'last'        => 'false',
                'class'     => ''
            ), $atts));    
            
            $grid = array(  'third'  => 'ut-one-third',
                            'fourth' => 'ut-one-fourth',
                               'half'     => 'ut-one-half' );
        
            $last = $last == 'true' ? 'ut-column-last' : '';
            $return = '';
            
            /* fallback */
            $gridwidth = !empty($grid[$width]) ? $grid[$width] : '';
            
            
            $GLOBALS['tab_count'] = 0;
            $GLOBALS['tabs'] = array();
            
            do_shortcode( $content );        
        
            if( is_array( $GLOBALS['tabs'] ) ){
                
                $tabcount = 1;
                
                foreach( $GLOBALS['tabs'] as $tab ){
                    
                    $active = ($tabcount == 1) ? 'class="active"' : '';
                    $tabs[]     = '<li ' . $active . '><a href="#' . $tab['id'] . 'Tab" data-toggle="tab">' . $tab['title'] . '</a></li>';
                    
                    $active = ($tabcount == 1) ? 'active' : '';
                    $panes[]    = '<div class="tab-pane ' . $active . ' ' . $tab['class'] . ' clearfix" id="' . $tab['id'] . 'Tab">' . do_shortcode($tab['content']) . '</div>';
                    
                    $tabcount++;
                    
                }
                
                if( !empty($width) || !empty($class) ) {
                    $return .= '<div class="' . $gridwidth . ' ' . $class . ' ' . $last . '">';
                }
                
                $return .= "\n" . '<!-- the tabs --><ul class="ut-nav-tabs clearfix">' . implode( "\n", $tabs ) . '</ul>' . "\n" . '<!-- tab "panes" --><div class="ut-tab-content entry-content clearfix">' . implode( "\n", $panes ) . '</div>' . "\n";
                
                if( !empty($width) ) {
                    $return .= '</div>';
                }
                
                
            }
        
        return $return;
    
    }
    add_shortcode( 'ut_tabgroup', 'ut_tabgroup' );

}

if( !function_exists('ut_tab') ) {

    function ut_tab( $atts, $content ){
        
        extract(shortcode_atts(array(
            'title' => '%d',
            'id' => '%d',
            'class' => ''
        ), $atts));
        
                
        $x = $GLOBALS['tab_count'];
        $GLOBALS['tabs'][$x] = array(
            'title' => sprintf( $title, $GLOBALS['tab_count'] ),
            'content' =>  $content,
            'id' =>  $id,        
            'class' => $class );
        
        $GLOBALS['tab_count']++;
    }
    add_shortcode( 'ut_tab', 'ut_tab' );
}

/*
 * Video Testimonials
 */

$GLOBALS['ut_video_testimonial_total_count'] = NULL;
$GLOBALS['ut_video_testimonial_count'] = NULL; 
  
if( !function_exists('ut_video_testimonials') ) {

    function ut_video_testimonials( $atts, $content ){
    
        extract(shortcode_atts(array(
                'class' => ''
        ), $atts));
        
        global $ut_video_testimonial_total_count , $ut_video_testimonial_count; 
        
        $ut_video_testimonial_total_count = substr_count($content, '[/ut_video_testimonial]');
        $ut_video_testimonial_count = 0;
        
        $testimonials = NULL;
        
        $testimonials.= '<div class="ut-video-testimonial-wrapper grid-100 tablet-grid-100 mobile-grid-100 grid-parent ' . $class . '">';
            
            $testimonials.= do_shortcode( $content );
            
        $testimonials.= '</div>';
        
        return $testimonials;
        
    }    
    
    add_shortcode( 'ut_video_testimonials', 'ut_video_testimonials' );
    
}

if( !function_exists('ut_video_testimonial') ) {

    function ut_video_testimonial( $atts, $content ){
        
        extract(shortcode_atts(array(
                'author'        => '',
                'company'       => '',
                'box'           => 'off',
                'poster'        => get_template_directory_uri() . '/images/video-default-thumbnail.jpg',
                'width'         => '800',
                'height'        => '450',
                'style'         => 'dark',
                'effect'        => '',
                'animate_once'  => 'no',
                'class'         => ''
        ), $atts));
        
        $grid = array(  1 => '100',
                        2 => '50',
                        3 => '33'                    
                );
        
        global $ut_video_testimonial_total_count , $ut_video_testimonial_count; 
        
        /* animation effect */
        $dataeffect = $animated = NULL;
        if( !empty( $effect ) ) {

            $dataeffect = 'data-effect="' . $effect . '" data-animateonce="' . $animate_once . '"';
            $animated      = 'ut-animate-element animated';
            
        }        
        
        /* fallback if there is no global value */
        $ut_video_testimonial_total_count = empty($ut_video_testimonial_total_count) ? 3 : $ut_video_testimonial_total_count;
        
        $grid_items = ( $ut_video_testimonial_total_count >= 3 ) ? 3 : $ut_video_testimonial_total_count;
                
        $testimonial = NULL;
        
        $style = ( $box == 'on' ) ? 'ut-video-box-' . $style : '';
        
        $testimonial.= '<div ' . $dataeffect . ' class="grid-' . $grid[$grid_items] . ' tablet-grid-' . $grid[$grid_items] . ' mobile-grid-100 ' .  $animated  . '">';
            
            $testimonial.= '<div class="ut-video-testimonial ' . ( ($box == 'on') ? 'ut-video-testimonial-boxed' : '' ) . ' ' . $style . '">';
                
                if( !empty($poster) ) {
                     
                $testimonial .= '<a data-exthumbimage="' . esc_url( $poster ) . '" class="ut-load-vtestimonial ut-vc-images-lightbox" href="' . strip_tags($content) . '">';                    
                    $testimonial .= '<img alt="' . ( !empty($author) ? $author : esc_html__('Video Testimonial','ut_shortcodes') ) . '" src="' . esc_url( $poster ) . '" />';                    
                $testimonial .= '</a>';
                
                }
                    
                if( !empty($author) ) {
                    $testimonial.= '<h3>' . $author . '</h3>';
                }
                
                if( !empty($company) ) {
                    $testimonial.= '<span>' . $company . '</span>';
                }                
                
            $testimonial.= '</div>';
            
        $testimonial.= '</div>';
        
        /* global counter */
        $ut_video_testimonial_count++;
        
        /* if counter has reached the maximum of 3 per row , decrease the total counter */
        if( $ut_video_testimonial_count ==  3 && $ut_video_testimonial_total_count > 3) {
            $ut_video_testimonial_total_count = $ut_video_testimonial_total_count - $ut_video_testimonial_count;
            $ut_video_testimonial_count = 0;
        }     
        
        return $testimonial;
        
    }
    
    add_shortcode( 'ut_video_testimonial', 'ut_video_testimonial' );
    
}


/*
 * Social Media List
 */ 
 
$GLOBALS['ut_social_total_count'] = NULL;
$GLOBALS['ut_social_count'] = NULL;

if( !function_exists('ut_social_media') ) {

    function ut_social_media( $atts, $content ){
            
        extract(shortcode_atts(array(
            'class' => ''
        ), $atts));        
        
        global $ut_social_total_count, $ut_social_count;
        
        $ut_social_total_count = substr_count($content, '[/ut_social]');
        $ut_social_count = 0;
        
        $social  = '<ul class="ut-social-network ' . $class . '">';
            $social .= do_shortcode( $content );
        $social .= '</ul>';
        
        return $social;
            
    }
    
    add_shortcode( 'ut_social_media', 'ut_social_media' );

}

if( !function_exists('ut_social') ) {

    function ut_social( $atts, $content ){
        
        extract(shortcode_atts(array(
            'title'        => '',
            'url'         => '#',
            'icon'        => 'fa-facebook',
            'target'    => '_blank',
            'avatar'     => '',
            'class'        => ''            
        ), $atts));        
        
        $grid = array(  1 => '100',
                        2 => '25',
                        3 => '33',
                        4 => '25',
                        5 => '20' );
        
        global $ut_social_total_count, $ut_social_count;
        
        $prefix = NULL;
        $suffix = NULL;        
                        
        //fallback if there is no global value
        $ut_social_total_count = empty($ut_social_total_count) ? 5 : $ut_social_total_count;
                
        // special case if we only have 2 items
        if( $ut_social_total_count == 2 && $ut_social_count == 0 ) {
            $prefix = "prefix-25 tablet-prefix-25";
        }
        
        if( $ut_social_total_count == 2 && $ut_social_count == 1 ) {
            $suffix = "suffix-25 tablet-suffix-25";
        }        
        
        $grid_items = ( $ut_social_total_count >= 5 ) ? 5 : $ut_social_total_count;
                
        $profile  = '<li class="grid-' . $grid[$grid_items] . ' tablet-grid-' . $grid[$grid_items] . ' mobile-grid-100 ' . $class . ' ' . $prefix . ' ' . $suffix . '">';
            $profile .= '<a target="' . $target . '" href="' . $url . '" class="ut-social-link">';
                $profile .= '<span class="ut-social-icon"><i class="fa ' . $icon . ' fa-4x"></i></span>';
                
                if( !empty($title) ) {
                    $profile .= '<h3 class="ut-social-title">' . $title . '</h3>';
                }
                
                if( !empty($content) ) {
                    $profile .= '<span class="ut-social-info">' . do_shortcode( $content ) . '</span>';
                }
                
            $profile .= '</a>';
        $profile .= '</li>';
        
        /* global counter */
        $ut_social_count++;        
        
        /* if counter has reached the maximum of 5 per row , decrease the total counter */
        if( $ut_social_count ==  5 && $ut_social_total_count > 5) {
            $ut_social_total_count = $ut_social_total_count - $ut_social_count;
            $ut_social_count = 1;
        }
                    
        return $profile;        
        
    }
    
    add_shortcode( 'ut_social', 'ut_social' );
    
}



/*
 * Toggles
 */ 
 
if( !function_exists('ut_togglegroup') ) {
    
    $togglegroupcount = 0;
    
    function ut_togglegroup( $atts, $content ){
        
        global $togglegroupcount;
        
        extract(shortcode_atts(array(
                'width'        => '',
                'last'        => 'false',
                'class'     => ''
        ), $atts));
        
        $grid = array( 'third'  => 'ut-one-third',
                       'fourth' => 'ut-one-fourth',
                       'half'    => 'ut-one-half');
        
        $last = $last == 'true' ? 'ut-column-last' : '';
        $return = '';
        
        /* fallback */
        $gridwidth = !empty($grid[$width]) ? $grid[$width] : '';        
        
        $togglegroupcount++;
        
        if( !empty($width) || !empty($class) ) {
            $return .= '<div class="' . $gridwidth . ' ' . $class . ' ' . $last . '">';
        }
           
        $return .= '<div id="ut-accordion-parent-' . $togglegroupcount . '" class="ut-accordion"><div class="ut-accordion-group">' . do_shortcode($content) . '</div></div>';
        
        if( !empty($width) || !empty($class) ) {
            $return .= '</div>';
        }
        
        
        return $return;
    
    }
    add_shortcode( 'ut_togglegroup', 'ut_togglegroup' );
}

if( !function_exists('ut_toggle') ) {

    $togglecount = 0;

    function ut_toggle( $atts, $content = null ) {
        
        global $togglecount , $togglegroupcount;    
            
        extract(shortcode_atts(array(
             'title'     => '',
             'state'     => 'closed',
             'class'    => ''
        ), $atts));
        
        $output = '';
        
        $hstate = ($state == 'closed') ? 'collapsed' : ''; 
        $state  = ($state == 'closed') ? 'collapse' : 'collapse in'; 
        $active = ($state == 'collapse') ? '' : 'active'; 
        
        $output .= '<div class="ut-accordion-heading ' . $class . '">';
            $output .= '<a data-parent="#ut-accordion-parent-' . $togglegroupcount . '" class="accordion-toggle ' . $hstate . ' ' . $active . '" data-toggle="collapse" data-target="#accordion' .$togglecount. '">';
                $output .= $title;
            $output .= '</a>';
        $output .= '</div>';
        $output .= '<div id="accordion' .$togglecount. '" class="ut-accordion-body ' . $state . '">';
            $output .= '<div class="ut-accordion-inner entry-content clearfix">';
                $output .= do_shortcode($content);
            $output .= '</div>';
        $output .= '</div>';
        
        $togglecount++;
        
        return $output;
        
    }
    add_shortcode('ut_toggle', 'ut_toggle');
}

/*
 * Blockquotes
 */ 

if( !function_exists('ut_blockquote_left') ) { 

    function ut_blockquote_left($atts, $content = null) {
        
        extract(shortcode_atts(array(
             'class'    => ''
        ), $atts));        
        
        return '<div class="ut-blockquote-left ' . $class . '"><blockquote><p>' . do_shortcode( $content ) . '</p></blockquote></div>';
    }
    add_shortcode('ut_blockquote_left', 'ut_blockquote_left');
}



if( !function_exists('ut_blockquote_right') ) { 

    function ut_blockquote_right($atts, $content = null) {
        
        extract(shortcode_atts(array(
             'class'    => ''
        ), $atts));    
        
        return '<div class="ut-blockquote-right ' . $class . '"><blockquote><p>' . do_shortcode( $content ) . '</p></blockquote></div>';
    }
    add_shortcode('ut_blockquote_right', 'ut_blockquote_right');
}

/*
 * Dropcaps
 */ 

if( !function_exists('ut_dropcap') ) { 

    function ut_dropcap($atts, $content = null) {
        
        extract(shortcode_atts(array(
             'class'     => '',
             'style'    => 'one'
        ), $atts)); 
        
        return '<span class="ut-dropcap-' . $style . ' ' . $class . '">' . do_shortcode($content) . '</span>';
    }
    
    add_shortcode('ut_dropcap', 'ut_dropcap');
    
}

/*
 * Highlight Section
 */

if( !function_exists('ut_highlight_section') ) { 
 
    function ut_highlight_section( $atts, $content = null ) {
        
        extract(shortcode_atts(array(
             'image'            => '',
             'imagetype'        => 'portrait',
             'alt'              => '',
             'imageeffect'      => '',
             'leftboxeffect'    => '',
             'rightboxeffect'   => '',
             'animate_once'     => 'no',
             'margin_bottom'    => '',
             'class'            => ''
        ), $atts));
        
        $highlight = NULL;
        
        /* margin bottom*/
        $margin_bottom = !empty($margin_bottom) ? 'style="margin-bottom:' . $margin_bottom . 'px"' : '';
        
        /* highlight wrapper */
        $highlight .= '<div class="ut-highlight-section-wrap clearfix" ' . $margin_bottom . '>';        
        
        /* animation effect */
        $dataeffect = $animated = NULL;
        if( !empty( $leftboxeffect ) ) {

            $dataeffect = 'data-effect="' . $leftboxeffect . '" data-animateonce="' . $animate_once . '"';
            $animated   = 'ut-animate-element animated';
            
        }
        
        /* left service columns */
        $highlight .= '<div ' . $dataeffect . ' class="grid-33 tablet-grid-33 mobile-grid-100 ut-highlight-section-right ' . $animated . '">';
            
            preg_match_all( '/' . get_shortcode_regex() . '/s', $content , $left_service_boxes );
                                    
            if (is_array($left_service_boxes[2]) && !empty($left_service_boxes[2]) ) {               
                
                foreach( $left_service_boxes[2] as $key => $box) {
                    
                    if( $box == 'ut_highlight_left' && !empty( $left_service_boxes[0][$key] ) ) {                    
                        $highlight .= do_shortcode( $left_service_boxes[0][$key] );
                    }
               
                }                
            
            }
            
        $highlight .= '</div><!-- grid-33 -->';
        
        
        /* image output */
        $image_class = ( $imagetype == 'portrait' ) ? 'ut-portrait' : 'ut-landscape';
                
        $highlight .= '<div class="grid-33 tablet-grid-33 mobile-grid-100">';
            $highlight .= '<div class="ut-highlight-section-image ' . $image_class . '">';

                if( !empty($image) ) {
                    
                    /* animation effect */
                    $dataeffect = $animated = NULL;
                    if( !empty( $imageeffect ) ) {
            
                        $dataeffect = 'data-effect="' . $imageeffect . '" data-animateonce="' . $animate_once . '"';
                        $animated   = 'ut-animate-element animated';
                        
                    }
                        
                    $highlight .= '<img ' . $dataeffect . ' class="' . $animated . '" alt="' . $alt . '" src="' . $image . '">';
                
                }
        
             $highlight .= '</div>';
        $highlight .= '</div><!-- grid-33 -->';
        
        
        /* animation effect */
        $dataeffect = $animated = NULL;
        if( !empty( $rightboxeffect ) ) {

            $dataeffect = 'data-effect="' . $rightboxeffect . '" data-animateonce="' . $animate_once . '"';
            $animated   = 'ut-animate-element animated';
            
        }
        
        /* right service columns */
        $highlight .= '<div ' . $dataeffect . ' class="grid-33 tablet-grid-33 mobile-grid-100 ' . $animated . '">';
            
            preg_match_all( '/' . get_shortcode_regex() . '/s', $content , $right_service_boxes );
                                    
            if (is_array($right_service_boxes[2]) && !empty($right_service_boxes[2]) ) {               
                
                foreach( $right_service_boxes[2] as $key => $box) {
                    
                    if( $box == 'ut_highlight_right' && !empty( $right_service_boxes[0][$key] ) ) {                    
                        $highlight .= do_shortcode( $right_service_boxes[0][$key] );
                    }
               
                }                
            
            }
            
        $highlight .= '</div><!-- grid-33 -->';
        
        /* end wrap */
        $highlight .= '</div><!-- ut-highlight-section-wrap -->';  
          
        return $highlight;
        
    }
    
    add_shortcode('ut_highlight_section', 'ut_highlight_section');

}


if( !function_exists('ut_highlight_section_box') ) { 
 
    function ut_highlight_section_box( $atts, $content = null ) {
    
        extract(shortcode_atts(array(
             'title'        => '',
             'icon'         => '',
             'color'        => '',
             'class'         => ''
        ), $atts));
        
        $ut_highlight_section_box = NULL;           
        
        /* set unique ID for this rotator */
        $id = uniqid("uthsbox_");
        
        if( !empty($color) ) {
            
            $ut_highlight_section_box .= '<style type="text/css">';
                $ut_highlight_section_box .= '#' . $id . ' . ut-highlight-section-box:hover h3 { color: ' . $color . '; }';
                $ut_highlight_section_box .= '#' . $id . ' .ut-highlight-section-icon { border-color: ' . $color . '; color: ' . $color . '; }';
            $ut_highlight_section_box .= '</style>';
        
        } elseif( empty($color) ) {
        
            $accentcolor = get_option('ut_accentcolor' , '#F1C40F');
            
            $ut_highlight_section_box .= '<style type="text/css">
                #' . $id . ' . ut-highlight-section-box:hover h3 { color: ' . $accentcolor . '; }
                #' . $id . ' .ut-highlight-section-icon { border-color: ' . $accentcolor . '; color: ' . $accentcolor . '; }                
            </style>';
        
        }
        
        $ut_highlight_section_box .= '<div id="' . $id . '" class="ut-highlight-section-box">';
            
            if( !empty($icon) ) {
                $ut_highlight_section_box .= '<div class="ut-highlight-section-icon hide-on-tablet"><i class="fa ' . $icon . '"></i></div>';
            }
            
            $ut_highlight_section_box .= '<div class="ut-highlight-section-content">';
                
                if( !empty($title) ) {
                    $ut_highlight_section_box .= '<h3>' . $title . '</h3>';
                }
                
                $ut_highlight_section_box .= do_shortcode( wpautop($content) );
                
            $ut_highlight_section_box .= '</div>';
            
        $ut_highlight_section_box .= '</div>';
        
        return $ut_highlight_section_box;
        
    }
    
    add_shortcode('ut_highlight_left', 'ut_highlight_section_box');
    add_shortcode('ut_highlight_right', 'ut_highlight_section_box');    
    
}


/*
 * Video Shortcode
 */ 

if( !function_exists('ut_video_player') ) {

    function ut_video_player( $atts, $content ){
        
        extract(shortcode_atts(array(
            'url'       => '',
            'caption'   => '',
            'poster'    => '',
            'maxwidth'  => '',
            'class'     => ''
        ), $atts));
        
        if( empty($url) ) {
            return;
        }
        
        $player = NULL;
        
        /* set unique ID for this video */
        $id = uniqid("utvs_");
        
        if( !empty( $maxwidth ) ) {
            
            $player .= '<style type="text/css">
                #' . $id . ' . ut-shortcode-video-wrap { max-width: ' . $maxwidth . '% ; }
            </style>';
        
        }
        
        $player .= '<div id="' . $id . '" class="ut-shortcode-video-wrap ' . $class . '">';
        
                $player .= '<figure class="ut-video-caption">';
                
                if( !empty($poster) ) {
                    
                    $player .= '<a class="ut-load-video" data-video="' . trim($url) . '">';
                        
                        $player .= '<img alt="' . ( !empty($caption) ? $caption : esc_html__('Video','ut_shortcodes') ) . '" src="' . $poster . '" />';
                        $player .= '<i class="fa fa-youtube-play fa-4x fa-border"></i>';
                    
                    $player .= '</a>';
                    
                } else {
                    
                    $player .= '<a class="ut-load-video" data-video="' . trim($url) . '" href="#?custom=true&amp;width=' . $width . '&amp;height=' . $height . '">';
                    
                        $player .= '<i class="fa fa-youtube-play fa-4x fa-border"></i>';
                    
                    $player .= '</a>';
                    
                }
                
                if(!empty($caption)) {
                    $player .= '<figcaption class="ut-video-caption-text">' . $caption . '</figcaption></figure>';
                }
                
                $player .= '</figure>';
                
                $player .= '<div class="ut-video-loading"><i class="fa fa-spinner fa-3x fa-spin"></i></div>';
                    
        $player .= '</div>';
        
        return $player;
        
    }
    
    add_shortcode( 'ut_united_video', 'ut_video_player' );
    
}



/*
|--------------------------------------------------------------------------
| custom box
|--------------------------------------------------------------------------
*/
if ( !function_exists( 'ut_custom_box' ) ) {
    
    function ut_custom_box( $atts, $content ) {
        
         extract(shortcode_atts(array(
            'title'             => '',
            'logo'              => '',
            'title_color'       => '',
            'text_color'        => '',
            'border_width'      => '1',
            'border_style'      => 'solid',            
            'border_color'      => '#FFF',
            'border_radius'     => '',
            'margin_top'        => '',
            'margin_right'      => '',
            'margin_bottom'     => '',
            'margin_left'       => '',
            'padding_top'       => '',
            'padding_right'     => '',
            'padding_bottom'    => '',
            'padding_left'      => '',
            'background'        => '',
            'effect'            => '',
            'animate_once'      => 'no',
            'class'             => ''                
        ), $atts));
        
        $box   = '<div class="clear"></div>';
        $style = '';
        
        /* set unique ID for this rotator */
        $id = uniqid("utcb_");
        
        /* animation effect */
        $dataeffect = $animated = NULL;
        if( !empty( $effect ) ) {

            $dataeffect = 'data-effect="' . $effect . '" data-animateonce="' . $animate_once . '"';
            $animated   = 'ut-animate-element animated';
            
        }
        
        /* box custom css */
        $style .= '<style type="text/css">';
            
            /* title color */
            if( !empty($text_color) ) {
                $style .= '#' . $id . ' p { color: ' . $text_color . '; }';
            }
            
            /* background color */
            if( !empty($background) ) {
                $style .= '#' . $id . ' { background: ' . $background . '; }';
            }
            
            /* border settings */
            if( $border_style != 'none' ) {
                
                $border_width = str_replace('px','',$border_width);
                $style .= '#' . $id . ' { border: ' . $border_width . 'px ' . $border_style . ' ' . $border_color . ' ; }'; 
                                           
            }
            
            /* border radius */
            if( !empty($border_radius) ) {
                
                $border_radius = str_replace('px','',$border_radius);
                $style .= '#' . $id . ' { border-radius: ' . $border_radius . 'px; }'; 
                
            }
            
            /* margin */
            if( !empty($margin_top) ) {
                
                $margin_top = str_replace('px','',$margin_top);                
                $style .= '#' . $id . ' { margin-top: ' . $margin_top . 'px; }';
                
            }
            
            if( !empty($margin_right) ) {
                
                $margin_right = str_replace('px','',$margin_right);
                $style .= '#' . $id . ' { margin-right: ' . $margin_right . 'px; }'; 
                
            }
            
            if( !empty($margin_bottom) ) {
                
                $margin_bottom = str_replace('px','',$margin_bottom);
                $style .= '#' . $id . ' { margin-bottom: ' . $margin_bottom . 'px; }'; 
                
            }
            
            if( !empty($margin_left) ) {
                
                $margin_left = str_replace('px','',$margin_left);
                $style .= '#' . $id . ' { margin-left: ' . $margin_left . 'px; }'; 
                
            }
            
            /* padding */
            if( !empty($padding_top) ) {
                
                $padding_top = str_replace('px','',$padding_top);
                $style .= '#' . $id . ' { padding-top: ' . $padding_top . 'px; }'; 
                
            }
            
            if( !empty($padding_right) ) {
                
                $padding_right = str_replace('px','',$padding_right);
                $style .= '#' . $id . ' { padding-right: ' . $padding_right . 'px; }'; 
                
            }
            
            if( !empty($padding_bottom) ) {
                
                $padding_bottom = str_replace('px','',$padding_bottom);
                $style .= '#' . $id . ' { padding-bottom: ' . $padding_bottom . 'px; }'; 
                
            }
            
            if( !empty($padding_left) ) {
                
                $padding_left = str_replace('px','',$padding_left);
                $style .= '#' . $id . ' { padding-left: ' . $padding_left . 'px; }'; 
                
            }
            
            
        $style .= '</style>';
        
        /* start box */
        $box .= '<div id="' . $id . '" class="ut-custom-box ' . $class . ' clearfix ' . $animated . '" ' . $dataeffect . '>';
            
            /* box image */
            if( !empty($logo) ) {
                
                $image_title = !empty($title) ? $title : 'Custom Image';
                
                $box .= '<div class="ut-custom-box-icon">';
                    
                    $box .= '<img alt="' . $image_title . '" src="' . $logo . '" />';
                    
                $box .= '</div>';
            
            }
            
            /* box content */
            $box .= '<div class="ut-custom-box-content">';
                
                /* box title */
                if( !empty($title) ) {
                    
                    /* title color */
                    if( !empty($title_color) ) {
                        $title_color = 'style="color: ' . $title_color . ' !important;"';
                    }
                    
                    $box .= '<h3 ' . $title_color . '>' . $title . '</h3>';
                    
                }
                
                $box .= '<p>' . do_shortcode($content) . '</p>';
            
            $box .='</div>';
        
        $box .= '</div>';
        
        return $style . $box;        
                
    }
    
    add_shortcode( 'ut_custom_box', 'ut_custom_box' );

}

/*
* Gets the excerpt of a specific post ID or object
* @param - $post - object/int - the ID or object of the post to get the excerpt of
* @param - $length - int - the length of the excerpt in words
* @param - $tags - string - the allowed HTML tags. These will not be stripped out
*/

if ( !function_exists( 'ut_get_excerpt_by_id' ) ) {
    
    function ut_get_excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>') {
        
        if( $post ) {
            
            $post = get_post($post);
            
        } elseif(!is_object($post)) {
            
            return false;
            
        }
         
        if( has_excerpt( $post->ID ) ) {
            
            $the_excerpt = $post->post_excerpt;
            $the_excerpt = apply_filters('the_content', $the_excerpt);
            $the_excerpt .= '<p><a class="more-link" href="' . get_permalink($post->ID) . '"><span class="more-link">' . esc_html__('Read More' , 'ut_shortcodes') . '<i class="fa fa-chevron-circle-right"></i></span></a></p>';
            return $the_excerpt;
            
        } else {
            
            $the_excerpt = $post->post_content;
            
        }
         
        $the_excerpt = strip_shortcodes(strip_tags($the_excerpt), $tags);
        $the_excerpt = preg_split('/\s+/', $the_excerpt, $length + 1);
        $excerpt_waste = array_pop($the_excerpt);
        $the_excerpt = implode(" ",$the_excerpt);
        
        if( isset($the_excerpt) && !empty($the_excerpt) ) {
            
            $the_excerpt  = '<p>' . $the_excerpt . '</p>';
            $the_excerpt .= '<p><a class="more-link" href="' . get_permalink($post->ID) . '"><span class="more-link">' . esc_html__('Read More' , 'ut_shortcodes') . '<i class="fa fa-chevron-circle-right"></i></span></a></p>';
            
        } else {
            
            $the_excerpt = '<p><a class="more-link" href="' . get_permalink($post->ID) . '"><span class="more-link">' . esc_html__('Read More' , 'ut_shortcodes') . '<i class="fa fa-chevron-circle-right"></i></span></a></p>';
        }

        return $the_excerpt;
        
    }
    
}

/*
 * List Icons ( Helper Shortcode )
 */ 
if( !function_exists('ut_list_icons') ) { 
 
    function ut_list_icons($content = null) {
        
        $output = '<div class="ut-icon-list">';
        $counter = 1;
            
        foreach( ut_recognized_icons() as $icon ) {
            
            $last = '';
            $clear = '';
            
            if( $counter == 5 ) { $last = 'ut-column-last'; $clear = '<div class="clear"></div>'; } 

            $output .= '<div class="ut-one-fifth ' . $last . '">
                            <p>
                                <i class="fa ' . $icon . ' icon-list-item" style="margin-right:5px;"></i> ' . $icon . '
                            </p>
                        </div>' . $clear;
            
            if( $counter == 5 ) { $counter = 1; } else { $counter++; }    
                        
                  
        }
        
        $output .= '</div>';    
            
        return $output;
        
    }
    add_shortcode('ut_list_icons', 'ut_list_icons');

}




/* remove default gallery shortcode */
remove_shortcode('gallery');

if( !function_exists('ut_gallery_shortcode') ) :

    function ut_gallery_shortcode( $attr ) {
        
        $post = get_post();
    
        static $instance = 0;
        $instance++;
    
        if ( ! empty( $attr['ids'] ) ) {
            // 'ids' is explicitly ordered, unless you specify otherwise.
            if ( empty( $attr['orderby'] ) )
                $attr['orderby'] = 'post__in';
            $attr['include'] = $attr['ids'];
        }
    
        /**
         * Filter the default gallery shortcode output.
         *
         * If the filtered output isn't empty, it will be used instead of generating
         * the default gallery template.
         *
         * @since 2.5.0
         *
         * @see gallery_shortcode()
         *
         * @param string $output The gallery output. Default empty.
         * @param array  $attr   Attributes of the gallery shortcode.
         */
        $output = apply_filters( 'post_gallery', '', $attr );
        if ( $output != '' )
            return $output;
    
        // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
        if ( isset( $attr['orderby'] ) ) {
            $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
            if ( !$attr['orderby'] )
                unset( $attr['orderby'] );
        }
    
        $html5 = current_theme_supports( 'html5', 'gallery' );
        extract(shortcode_atts(array(
            'order'                 => 'ASC',
            'orderby'               => 'menu_order ID',
            'id'                    => $post ? $post->ID : 0,
            'itemtag'               => $html5 ? 'figure'     : 'dl',
            'icontag'               => $html5 ? 'div'        : 'dt',
            'captiontag'            => $html5 ? 'figcaption' : 'dd',
            'columns'               => 3,
            'size'                  => 'thumbnail',
            'include'               => '',
            'exclude'               => '',
            'link'                  => 'file',
            'ut_gallery_lightbox' 	=> 'on',
            'ut_image_border'       => 'off',
            'ut_image_border_radius'=> '0px'            
        ), $attr, 'gallery'));

        $id = intval($id);
        if ( 'RAND' == $order )
            $orderby = 'none';
    
        if ( !empty($include) ) {

            $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    
            $attachments = array();
            foreach ( $_attachments as $key => $val ) {

                $attachments[$val->ID] = $_attachments[$key];


            }

        } elseif ( !empty($exclude) ) {

            $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        } else {

            $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        }
    
        if ( empty($attachments) )
            return '';
    
        if ( is_feed() ) {
            $output = "\n";
            foreach ( $attachments as $att_id => $attachment )
                $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
            return $output;
        }
    
        $itemtag = tag_escape($itemtag);
        $captiontag = tag_escape($captiontag);
        $icontag = tag_escape($icontag);
        $valid_tags = wp_kses_allowed_html( 'post' );
        if ( ! isset( $valid_tags[ $itemtag ] ) )
            $itemtag = 'dl';
        if ( ! isset( $valid_tags[ $captiontag ] ) )
            $captiontag = 'dd';
        if ( ! isset( $valid_tags[ $icontag ] ) )
            $icontag = 'dt';
    
        $columns = intval($columns);
        $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
        $float = is_rtl() ? 'right' : 'left';
    
        $selector = "gallery-{$instance}";
    
        $gallery_style = $gallery_div = '';
    
        /**
         * Filter whether to print default gallery styles.
         *
         * @since 3.1.0
         *
         * @param bool $print Whether to print default gallery styles.
         *                    Defaults to false if the theme supports HTML5 galleries.
         *                    Otherwise, defaults to true.
         */
        if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
            $gallery_style = "
            <style type='text/css'>
                #{$selector} {
                    margin: auto;
                }
                #{$selector} .gallery-item {
                    float: {$float};
                    margin-top: 10px;
                    text-align: center;
                    width: {$itemwidth}%;
                }
                #{$selector} img {
                    border: 2px solid #cfcfcf;
                }
                #{$selector} .gallery-caption {
                    margin-left: 0;
                }
                
                ".( $ut_image_border == 'on' ? "#{$selector} .gallery-item img { -webkit-border-radius:".$ut_image_border_radius."; -moz-border-radius:".$ut_image_border_radius."; border-radius:".$ut_image_border_radius."; }" : '' )."
                
                /* see gallery_shortcode() in wp-includes/media.php */
            </style>\n\t\t";
        }
    
        $size_class = sanitize_html_class( $size );

	    $lightbox_class = 'off ut-deactivated-link';

	    // no attachment pages
	    $link = $link == 'post' ? 'file' : $link;

	    if( $ut_gallery_lightbox == "on" && (! empty( $link ) && 'file' === $link) ) {
		    $lightbox_class = 'on';
        }

        $gallery_div = "<div id='$selector' class='ut-wp-gallery-lightbox-{$lightbox_class} gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
    
        /**
         * Filter the default gallery shortcode CSS styles.
         *
         * @since 2.5.0
         *
         * @param string $gallery_style Default gallery shortcode CSS styles.
         * @param string $gallery_div   Opening HTML div container for the gallery shortcode output.
         */
        $output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );
    
        $i = 0;
        foreach ( $attachments as $id => $attachment ) {

	        $mini = wp_get_attachment_image_src( $id , 'ut-mini' );
	        $mini = !empty( $mini[0] ) ? 'data-exthumbimage="' . $mini[0] . '"' : '';

            if ( ! empty( $link ) && 'file' === $link )
                $image_output = wp_get_attachment_link( $id, $size, false, false );
            elseif ( ! empty( $link ) && 'none' === $link )
                $image_output = wp_get_attachment_image( $id, $size, false );
            else
                $image_output = wp_get_attachment_link( $id, $size, true, false );
    
            $image_meta  = wp_get_attachment_metadata( $id );

            // inject data
            $image_output = str_replace('<a href', '<a ' . $mini . ' href', $image_output );

    
            $orientation = '';
            if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
                $orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
            }

            $lightbox_support = $ut_gallery_lightbox == "on" && (! empty( $attr['link'] ) && 'file' === $attr['link']) ? 'gallery-item-with-lightbox' : '';

            $output .= "<{$itemtag} class='gallery-item $lightbox_support'>";
            $output .= "
                <{$icontag} class='gallery-icon {$orientation}'>
                    $image_output
                </{$icontag}>";
            if ( $captiontag && trim($attachment->post_excerpt) ) {
                $output .= "
                    <{$captiontag} class='wp-caption-text gallery-caption'>
                    " . wptexturize($attachment->post_excerpt) . "
                    </{$captiontag}>";
            }
            $output .= "</{$itemtag}>";
            if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
                $output .= '<br style="clear: both" />';
            }
        }
    
        if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
            $output .= "
                <br style='clear: both' />";
        }

        $output .= "</div>\n";
    
        return $output;
    }
    
    add_shortcode('gallery', 'ut_gallery_shortcode');
    
endif;


if(!class_exists('UT_Section_Video_player' ) ) :

    class UT_Section_Video_player {
        
        static $add_script;
        static $youtube;
        static $vimeo;
        static $selfhosted;
    
        static function init() {
            
            add_shortcode('ut_section_video', array(__CLASS__, 'handle_shortcode'));
            
            add_action('init', array(__CLASS__, 'register_script'));
            add_action('wp_footer', array(__CLASS__, 'print_script'));
            
        }
        
        static function vimeo_id( $url ) {
                                
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
        
        static function handle_shortcode($atts) {
                    
            self::$add_script = true;
            
            extract(shortcode_atts(array(
                   'id'         => '',
                   'section'    => '',
                   'source'     => 'youtube',
                   'video'      => '',
                   'video_vimeo'=> '',
                   'volume'     => '5',
                   'mutebutton' => 'off',
                   'sound'      => 'off',
                   'loop'       => 'on',
                   'startAt'    => '0',
                   'stopAt'     => '0',
                   'mp4'        => '',
                   'ogg'        => '',
                   'webm'       => '',
                   'preload'    => '',
                   'dnt'        => false
            ), $atts));
            
            /* id or section is empty, nothing to do here */
	        if( empty( $id ) || empty( $section ) ) {
                return;
            }
            
            $script = NULL;
            $player = NULL;
            
            if( $source == 'youtube' && !empty( $video ) ) {
                
                self::$youtube = true;
                
                /* get player options */
                $sound_attr   = ($sound == 'off') ? 'mute : true' : 'mute : false';            
                $volume_attr  = (empty($volume)) ? 'vol : 0' : 'vol: ' . $volume;            
                $loop_attr    = ($loop == 'on') ? 'loop : true' : 'loop : false';

                $player .= '<a id="ut-background-video-' . $id . '" data-id="' . $id . '" class="ut-video-section-player" data-source="youtube" data-property="{ videoURL : \'' . $video . '\' , containment : \''.$section.'\' , showControls: false, ratio: \'auto\', quality: \'hd720\', autoPlay : true, '.$loop_attr.', '.$sound_attr.', '.$volume_attr.', startAt : ' . $startAt . ', stopAt : ' . $stopAt . ', opacity : 1}"></a>';
                
                $sound = ( $sound == "on" ) ? 'ut-mute' : 'ut-unmute';
                
                if( $mutebutton == 'on' ) {
                    $player .= '<a id="ut-video-control-' . $id . '" class="ut-video-control '.$sound.' youtube" data-source="youtube" data-for="ut-background-video-' . $id . '" href="#"></a>';
                }
                
            }
            
            if( $source == 'vimeo' && !empty( $video_vimeo ) ) {

                self::$vimeo = true;
                
                $player_volume = !$volume || $sound == 'off' ? 0 : $volume;
                $player_loop = $loop == 'on' ? "1" : "0";

                // player settings json
                $player_settings = array(
                    'videoId'       => self::vimeo_id( $video_vimeo ),
                    'containment'   => $section,
                    'parameters'    => array(
                        'autopause' => 0,
                        'autoplay'  => 0,
                        'badge'     => 1,
                        'byline'    => 1,
                        'color'     => "000",
                        'loop'      => $player_loop,
                        'player_id' => 'demo',
                        'portrait'  => 1,
                        'title'     => 1,
                        'muted'     => 1,
                        'dnt'       => $dnt,
                        'controls'  => false
                    )

                );

                $player_settings = htmlentities( json_encode( $player_settings ), ENT_QUOTES, 'utf-8' );

	            $script = '';
                
                $player = '<div id="ut-background-video-' . $id . '" data-id="' . str_replace( '#', '', $section ) . '" class="ut-video-section-player" data-source="vimeo" data-volume="' . ( $player_volume / 100 ) . '" data-max-volume="' . ( $volume / 100 ) . '" data-settings="' . $player_settings . '"></div>';
                
                if( $mutebutton == 'on' ) {
                    
                    $mute = ( $mutebutton == "on" && $player_volume != 0 ) ? 'ut-mute' : 'ut-unmute';
                    
                    $player .= '<a id="ut-video-hero-control" class="ut-video-control '.$sound.' ' . $mute . ' vimeo" data-source="vimeo" data-for="ut-video-hero" href="#"></a>';
                    
                }                
            
            }
            
            if( $source == 'selfhosted') {
                
                if( !empty($mp4) || !empty($ogg) || !empty($webm) ) {
                    
                    self::$selfhosted = true;
                        
                    /* build config */
                    $sound   = ($sound == 'off') ? 'muted' : '';            
                    $volume  = (empty($volume)) ? '5' : $volume;            
                    $loop    = ($loop == 'on') ? 'loop' : '';
                    $preload = ($preload == 'on') ? 'preload="auto"' : '';
                    
                    /* build player */
                    $player .= '<div class="ut-simple-video-container"><video id="ut-selfvideo-player-' . $id . '" class="ut-selfvideo-player" disablePictureInPicture autoplay '.$loop.' '.$sound.' '.$preload.' volume="'.$volume.'" autobuffer controls>';
                    
                    if( !empty( $mp4 ) ) :
                                
                        $player .= '<source src="' . $mp4 . '" type="video/mp4"> ';
                        
                    endif;
                    
                    if( !empty( $webm ) ) :
                        
                        $player .= '<source src="' . $webm . '" type="video/webm"> ';
                        
                    endif;    
                    
                    if( !empty( $ogg ) ) :
                        
                        $player .= ' <source src="' . $ogg . '" type="video/ogg ogv">';
                                        
                    endif;
                    
                    $player .= '</video></div><div class="ut-video-spacer"></div>';
                    
                    $sound = ( $sound == "on" ) ? 'ut-mute' : 'ut-unmute';
                    
                    if($mutebutton == 'on') {
                        $player .= '<a id="ut-video-control-' . $id . '" class="ut-video-control '.$sound.'" data-for="ut-selfvideo-player-' . $id . '" href="#"></a>';
                    }
                
                
                }
                
            
            }        
                    
            return $script . $player;
            
            
        }
    
        static function register_script() {
            
            $min = NULL;
                
            if( !WP_DEBUG ){
                $min = '.min';
            }

            wp_register_script(
                'ut-video',
                get_template_directory_uri() . '/js/ut-videoplayer' . $min . '.js', 
                array('jquery'),
                '1.0',
                true
            );
            
        }
    
        static function print_script() {
            
            if ( ! self::$add_script ) {
                return;
            }

            if ( self::$selfhosted ) {
                wp_enqueue_script('ut-video');
            }
            
        }
        
    }

    UT_Section_Video_player::init(); 

endif;


/*
 * Helper Shortcode : Displays a sidebar directly inside regular content
 */


if( !function_exists('ut_searchform') ) {

    function ut_searchform( $atts, $content = null ) {

        extract( shortcode_atts( array(
            'sidebar' => '',
        ), $atts ) );

        ob_start(); ?>

        <style>

            #ut-inline-search-area .search-form {
                position: relative;
                height: 70px;
            }

            #ut-inline-search-area .search-field {
                box-sizing: border-box;
                display: block;
                position: absolute;
                width: 100%;
                padding: 20px 220px 20px 20px;
                background: #191E23;
                color: #FAFAFA;
                border-color: #32373C;
                font-size: 1.2em;
                margin: 0;
                height: 70px;
            }

            #ut-inline-search-area .search-submit {
                display: block;
                position:absolute;
                top: 0;
                right: 0;
                width: 200px;
                border-radius: 0;
                margin: 0;
                height: 70px;
                background: rgba(5, 64, 242, 1);
            }

            #ut-inline-search-area .search-submit:hover {
                background: #FFF;
                color: #131416;
            }

        </style>

        <div id="ut-inline-search-area">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search the Knowledge Base', 'placeholder', 'ut_shortcodes' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php echo esc_html_x( 'Search for:', 'label', 'ut_shortcodes' ); ?>">
                <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'ut_shortcodes' ); ?>">
            </form>
        </div>

        <?php

        return ob_get_clean();

    }

    add_shortcode('ut_searchform', 'ut_searchform');

}


function _ut_update_image_post() {

	if( empty( $_REQUEST['post_ID'] ) ) {

		wp_die(esc_html__('Busted!', 'unitedthemes' ) );

	} else {

		$post_update = array(
			'ID' => (int) $_REQUEST['post_ID']
		);

	}

	if ( !current_user_can( 'edit_page', (int) $_REQUEST['post_ID'] ) ) {

		wp_die(esc_html__('Busted!', 'unitedthemes' ) );

	}

	if( get_post_type( (int) $_REQUEST['post_ID'] ) !== 'attachment' ) {

		wp_die(esc_html__('Busted!', 'unitedthemes' ) );

    }

	if( !empty( $_REQUEST['title'] ) ) {

		$post_update['post_title'] = sanitize_text_field( $_REQUEST['title'] );

	}

	if( !empty( $_REQUEST['excerpt'] ) ) {

		$post_update['post_excerpt'] = sanitize_text_field( $_REQUEST['excerpt'] );

	}

	wp_update_post( $post_update );

	wp_die();

}

// add_action( 'wp_ajax_update_image_meta', '_ut_update_image_post' );