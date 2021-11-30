<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if String is Hex Color
 *
 * @access    public
 * @since     4.4.5
 */
 
if( ! function_exists( 'ut_is_hex' ) ) {
  
    function ut_is_hex( $input ) {
        
        return preg_match( '/^#[a-f0-9]{6}$/i', $input );
                
    }
    
}

/*
 * Check if CSS String is Gradient Color
 *
 * @return    bolean
 * @access    public
 * since      4.6
 */

if ( !function_exists( 'ut_is_gradient' ) ) {
    
    function ut_is_gradient( $string ) {
        
        return strpos( $string, 'linear-gradient') === false ? false : true;
        
    }
    
}

/*
 * HEX to RGB
 */

if( !function_exists('ut_hex_to_rgb') ) :

    function ut_hex_to_rgb($hex) {
                
        $hex = preg_replace("/#/", "", $hex);
        $color = array();
     
        if(strlen($hex) == 3) {
            $color['r'] = hexdec(substr($hex, 0, 1) . $r);
            $color['g'] = hexdec(substr($hex, 1, 1) . $g);
            $color['b'] = hexdec(substr($hex, 2, 1) . $b);
        }
        else if(strlen($hex) == 6) {
            $color['r'] = hexdec(substr($hex, 0, 2));
            $color['g'] = hexdec(substr($hex, 2, 2));
            $color['b'] = hexdec(substr($hex, 4, 2));
        }
        
        $color = implode(',', $color);
        
        return $color;
    }

endif;


/*
 * HEX to RGBA
 */

if( !function_exists('ut_hex_to_rgba') ) :

	function ut_hex_to_rgba( $hex, $opacity ) {

		$hex = preg_replace("/#/", "", $hex);
		$color = array();

		if(strlen($hex) == 3) {
			$color['r'] = hexdec(substr($hex, 0, 1) . $r);
			$color['g'] = hexdec(substr($hex, 1, 1) . $g);
			$color['b'] = hexdec(substr($hex, 2, 1) . $b);
			$color['a'] = $opacity;
		}
		else if(strlen($hex) == 6) {
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
			$color['a'] = $opacity;
		}

		$color = implode(',', $color);

		return $color;
	}

endif;

/*
 * RGB to RGBA
 */

if( !function_exists('ut_rgb_to_rgba') ) :

	function ut_rgb_to_rgba( $rgb, $opacity = '' ) {

		if( empty( $rgb ) ) {
			return '';
		}

		/* check if already rgba */
		if( strpos( $rgb, 'rgba') !== false ) {

			if( !empty( $opacity ) ) {

				$rgb = preg_replace('/[^0-9,]/', '', $rgb);
				$rgb = explode(',', $rgb);

				if ($opacity != $rgb[3]) {

					$rgb[3] = $opacity;

				}
				$rgb = implode(',', $rgb);

				return 'rgb(' . $rgb . ')';

			} else {

				return $rgb;

			}

		}

		/* check if hex */
		if ( preg_match( '/^#[a-f0-9]{6}$/i', $rgb ) ) {
			$rgb = ut_hex_to_rgb( $rgb );
		}

		$rgb = preg_replace( '/[^0-9,]/', '', $rgb );
		$rgb = explode( ',', $rgb );

		$rgb[3] = !empty( $opacity ) ? $opacity : '1';

		$rgb = implode( ',', $rgb );

		return 'rgba(' . $rgb . ')';

	}

endif;

/*
 * RGB to HEX
 */

if( !function_exists('ut_rgb_to_hex') ) :

    function ut_rgb_to_hex( $rgb ) {
        
		$rgb = str_replace('rgba(', '', $rgb);
		$rgb = str_replace(')', '', $rgb);
		$rgb = explode(",", $rgb);
		
        $hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

		return $hex; // returns the hex value including the number sign (#)        
        
    }

endif;


/*
 * Get Opacity from RGB
 */

if( !function_exists('ut_get_rgba_opacity') ) :

    function ut_get_rgba_opacity( $rgb ) {
		
		$rgb = str_replace('rgba(', '', $rgb);
		$rgb = str_replace(')', '', $rgb);
		$rgb = explode(",", $rgb);
		
		return isset( $rgb[3] ) ? $rgb[3] : 1;
		
	}

endif;


/**
 * Turn RGBA String into RGB for browser fallback
 *
 * @access    public
 * @since     1.0
 */

if( ! function_exists( 'ut_rgba_to_rgb' ) ) {
    
    function ut_rgba_to_rgb( $rgba ) {
            
        if( empty( $rgba ) ) {
            return;
        }
        
        /* check if hex */
        if ( preg_match( '/^#[a-f0-9]{6}$/i', $rgba ) ) {
            $rgba = ut_hex_to_rgb( $rgba );
        }
        
        $rgb = preg_replace( '/[^0-9,]/', '', $rgba );
        $rgb = explode( ',', $rgb );
        
        if( count( $rgb ) === 4 ) {
            $stack = array_pop( $rgb );            
        }        
        
        $rgb = implode( ',', $rgb );
        
        return 'rgb(' . $rgb . ')';
    
    }

}




/**
 * Extract VC CSS Declaration from String
 *
 * @param $css
 *
 * @since 4.6
 * @return bool / array 
 */

if( ! function_exists( 'ut_vc_css_to_array' ) ) {

	function ut_vc_css_to_array( $css ) {

        preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);

		if( !empty( $matches[2][0] ) ) {

			$styles = array_filter( explode( ";", $matches[2][0] ) );

			$new_styles = array();

			foreach ( $styles as $val ) {

				$attr = explode( ':', $val, 2 );

				if( !empty( $attr[0] ) && !empty($attr[1]) ) {
					$new_styles[$attr[0]] = $attr[1];            
				}

			}

			return $new_styles;        

		}

		return false;    

	}

}

/*
 * CSS Gradient Background Support
 *
 * @return    string
 * @access    public
 * since      4.6
 */

if ( !function_exists( 'ut_add_gradient_css' ) ) {
    
    function ut_add_gradient_css( $vc_class, $atts ) {
        
        if( empty( $atts['css'] ) || empty( $vc_class ) ) {
            return;
        }
        
        $css_style = '';
        
        // Design Options Array
        $vc_css = ut_vc_css_to_array( $atts['css'] );

        // Linear Background
        if( isset( $vc_css["background-color"] ) ) {

            if( ut_create_gradient_css( $vc_css["background-color"] ) ) {

                // add background image
                $css_style = ut_create_gradient_css( $vc_css["background-color"], $vc_class ); 

            }         

        }

        // background with gradient and background image
        if( isset( $vc_css["background"] ) ) {

            if( ut_create_gradient_css( $vc_css["background"] ) ) {

                // add background image
                $css_style = ut_create_gradient_css( $vc_css["background"], $vc_class, false, 'background' ); 

            }         

        }
        
        return $css_style;        
        
    }
    
}


/*
 * Create CSS Gradient Colors
 *
 * @return    string
 * @access    public
 * since      4.5.3
 */

if ( !function_exists( 'ut_create_gradient_css' ) ) {
    
    function ut_create_gradient_css( $css, $tag = '', $pattern = false, $attribute = 'background-image', $important = false ) {
        
        if( !ut_is_gradient( $css ) ) {
            return false;
        }
        
        // optional pattern        
        $background_url_before = ''; // pattern
        $background_url_after = ''; // image
        
        $patterns = array(
            'bklyn-style-one'   => 'overlay-pattern.png',
            'bklyn-style-two'   => 'overlay-pattern2.png',
            'bklyn-style-three' => 'overlay-pattern3.png',
            'bklyn-style-four'   => 'circuit-board-pattern.svg'
        );
        
        if( $pattern && isset( $patterns[$pattern] ) ) {
            
            $background_url_before = 'url("' . THEME_WEB_ROOT . '/images/' . $patterns[$pattern] . '"),';
            
        }
        
        // check for Visual Composer has background with image
        if( strpos( $css, 'url(') !== false ) {
            
            // extract image
            $background_url = wp_extract_urls( $css );
            
            if( !empty( $background_url[0] )  ) {
                
                $background_url_after = ', url("' . esc_url( $background_url[0] ) . '")';
                
            } else {
                
                $background_url_after = '';
                
            }
            
            // extract linear gradient
            preg_match_all( '/linear-gradient\([^(]*(\([^)]*\)[^(]*)*[^)]*\)*url\(/', $css, $color );
                            
            if( !empty( $color[0][0] ) ) {

                $css = trim( str_replace('url(' , '', $color[0][0] ) );

            }
            
        }
        
        $important = $important ? '!important;' : '';
        
        $output = $tag . '{';
        
            $output .= "$attribute: $background_url_before -webkit-$css $background_url_after $important;";
            $output .= "$attribute: $background_url_before -moz-$css $background_url_after $important;";
            $output .= "$attribute: $background_url_before -o-$css $background_url_after $important;";
            $output .= "$attribute: $background_url_before $css $background_url_after $important;";
        
        $output .= '}';
        
        return $output;        
        
    }

}


/*
 * implode CSS Attributes
 *
 * @return    string
 * @access    public
 * since      4.6
 */

if ( !function_exists( 'ut_implode_css_attributes' ) ) {
    
    function ut_implode_css_attributes( $css ) {
        
        if( !empty( $css ) && is_array( $css ) ) {
            
            return implode(' ', array_map(

                function ($v, $k) { 

                    // add linear gradient prefixes
                    if( ut_is_gradient( $v ) && $k == 'background-color' ) {
                        
                        $k = 'background-image';
                        
                        $return  = 'background: none;'; // remove default backgroud color
                        $return .= "$k: -webkit-$v;";
                        $return .= "$k: -moz-$v;";
                        $return .= "$k: -o-$v;";
                        $return .= "$k: $v;";
                        
                        return $return;
                        
                    }                    
					
					if( ut_is_gradient( $v ) && $k == 'border-image' ) {
                        
                        $k = 'border-image';
                        
                        $return  = "-webkit-$k: -webkit-$v;";
                        $return .= "-moz-$k: -moz-$v;";
                        $return .= "-o-$k: -o-$v;";
                        $return .= "$k: $v;";
                        
						$k = 'border-image-slice';
						
						$return .= "-webkit-$k: 1;";
                        $return .= "-moz-$k: 1;";
                        $return .= "-o-$k: 1;";
                        $return .= "$k: 1;";
						
                        return $return;
                        
                    }  
					
                    return sprintf("%s:%s !important;", $k, $v);

                }, $css, array_keys( $css )

            ) );
        
        } else {
            
            return false;
            
        }
           
    }

}
    







/*
 * Create Button CSS 
 *
 * @return    string
 * @access    public
 * since      4.6
 */


if ( !function_exists( 'ut_create_button_css' ) ) {
    
    function ut_create_button_css( $id, $button_settings = array() ) {
        
        $button_settings = array_filter_recursive( $button_settings );
        
        if( empty( $button_settings ) ) {
            return;
        }
        
        $css = '';
        
        /*
         * Button Effects
         */
        
        // Aylen Effect
        if( isset( $button_settings['effect'] ) && $button_settings['effect'] == 'aylen' ) {
            
            // overwrite hover color with default color
            if( !empty( $button_settings['default']['background-color'] ) ) {
                
                $button_settings['hover']['background-color'] = $button_settings['default']['background-color'];
                
            } else {
                
                $button_settings['hover']['background-color'] = '';
                
            }
            
        }
        
        /*
         * Default State
         */
        
        if( !empty( $button_settings['default'] ) && is_array( $button_settings['default'] ) ) {
            
            $default = ut_implode_css_attributes( $button_settings['default'] );
            $css .= '#' . $id . ' .bklyn-btn { ' . $default . ' }';
            
        }
        
        /*
         * Hover State
         */
        
        if( !empty( $button_settings['hover'] ) && is_array( $button_settings['hover'] ) ) {
            
            $hover = ut_implode_css_attributes( $button_settings['hover'] );
            $css .= '#' . $id . ' .bklyn-btn:hover  { ' . $hover . ' }';
            $css .= '#' . $id . ' .bklyn-btn:focus  { ' . $hover . ' }';
            $css .= '#' . $id . ' .bklyn-btn:active { ' . $hover . ' }';   
            
        }
        
        /*
         * Hover After State
         */
        
        if( !empty( $button_settings['after'] ) && is_array( $button_settings['after'] ) ) {
            
            $after = ut_implode_css_attributes( $button_settings['after'] );
            $css .= '#' . $id . ' .bklyn-btn::after  { ' . $after . ' }';
            
        }
        
        /*
         * Hover Before State
         */
        
        if( !empty( $button_settings['before'] ) && is_array( $button_settings['before'] ) ) {
            
            $before = ut_implode_css_attributes( $button_settings['before'] );
            $css .= '#' . $id . ' .bklyn-btn::before  { ' . $before . ' }';
            
        }
        
        return $css;
        
    }    
    
}


/*
 * Create Reveal FX Classes
 *
 * @return    string
 * @access    public
 * since      4.9.5.2
 */


if ( !function_exists( 'ut_create_reveal_fx' ) ) {

	function ut_create_reveal_fx( $atts ) {

		/**
		 * @var string $reveal_direction
		 * @var string $reveal_position
		 * @var string $reveal_layers
		 * @var string $reveal_fx_animation
		 * @var string $reveal_speed
		 * @var string $reveal_fx_easing
		 */
		extract( shortcode_atts( array (

			'reveal_direction'    => 'ltor',
			'reveal_speed'        => 'default',
			'reveal_position'     => 'front',
			'reveal_layers'       => 'double',
			'reveal_fx_animation' => 'in_out',
			'reveal_fx_easing'    => 'quantic',

		), $atts ) );

		global $ut_tg_needs_to_wait;

		$reveal_fx_id = ut_get_unique_id('ut_rfx_');

		// FX Classes
		$classes = array( 'ut-element-revealer' );

		// Direction
		$classes[] = 'ut-reveal-in-' . $reveal_direction;

		// Position
		$classes[] = 'ut-reveal-in-' . $reveal_position;

		// Speed
		$classes[] = 'ut-element-revealer-' . $reveal_speed;

		// Easing
		if( $reveal_fx_easing ) {

			$classes[] = 'ut-element-revealer-' . $reveal_fx_easing;

		}

		// Layers
		if( $reveal_layers == 'double' ) {

			$classes[] = 'ut-reveal-in-before';
			$classes[] = 'ut-reveal-in-after';

		} else {

			$classes[] = 'ut-reveal-in-before';

		}

		// Animation in only
		if( $reveal_position == 'back' && $reveal_fx_animation == 'in' ) {

			$classes[] = 'ut-reveal-in-only';

		}

		$output = '<div id="' . $reveal_fx_id . '" class="' . implode( " ", $classes ) . '" data-ut-wait="' . $ut_tg_needs_to_wait . '">';

		$custom_css = ut_create_reveal_fx_css( $reveal_fx_id, $atts );

		if( !empty( $custom_css ) ) {

			$output .= '<style type="text/css">' . $custom_css . '</style>';

		}

		$output .= '</div>';

		return array(
			'id'      => $reveal_fx_id,
			'content' => $output
		);

	}

}




/*
 * Create Reveal FX CSS
 *
 * @return    string
 * @access    public
 * since      4.9.5.2
 */


if ( !function_exists( 'ut_create_reveal_fx_css' ) ) {

	function ut_create_reveal_fx_css( $reveal_fx_id, $atts ) {

		/**
		 * @var string $reveal_fx_color_1
		 * @var string $reveal_fx_color_2
		 * @var string $reveal_animation_width
		 * @var string $reveal_fx_easing
		 */
		extract( shortcode_atts( array (

			'reveal_direction'       => 'ltor',
			'reveal_fx_color_1'      => '',
			'reveal_fx_color_2'      => '',
			'reveal_animation_width' => 'default',
			'reveal_fx_easing'       => '',

			// only when in row
			'full_width'             => '',

		), $atts ) );

		$css_string = '';

		if( !empty( $reveal_fx_color_1 ) ) {

			$css_string .= "#$reveal_fx_id:before { background: $reveal_fx_color_1; }";

		}

		if( !empty( $reveal_fx_color_2 ) ) {

			$css_string .= "#$reveal_fx_id:after { background: $reveal_fx_color_2; }";

		}

		if ( ! empty( $full_width ) && 'stretch_row' === $full_width ) {

			$ut_site_custom_width = ot_get_option( 'ut_site_custom_width', '1200' ) - 40;

			if( $reveal_animation_width == 'edge' ) {

				$css_string .= "#$reveal_fx_id:before { max-width: calc(100vw - calc((100vw - {$ut_site_custom_width}px) / 2)); }";
				$css_string .= "#$reveal_fx_id:after { max-width: calc(100vw - calc((100vw - {$ut_site_custom_width}px) / 2)); }";

			}

		}

		return $css_string;

	}

}