<?php 

/*
 * Portfolio Management by United Themes
 * http://unitedthemes.com/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class ut_portfolio_shortcode {
	
	static $add_showcase_script;
	static $add_masonry_script;
    static $add_gallery_script;
    static $add_lightbox_script;
    
    static $vc_active;
	static $token;
    static $lightbox;
    static $portfolio_settings;
    static $detailstyle;
    static $slide_up_title;
    static $slide_up_width;
	
	/* init */
	static function init() {
		
		add_shortcode( 'ut_showcase' , array(__CLASS__, 'handle_shortcode') );		
		
	}
	
	/* start shortcode */
	static function handle_shortcode( $atts ) {
		
		extract( shortcode_atts( array( 
            'id' => '' 
        ) , $atts ) );
		
		/* no id has been set , nothing more to do here */
		if( empty( $id ) ) return '';
		
		/* set token */
		self::$token = $id;
		self::$vc_active = preg_match( '/vc_row/', get_the_content( null, false, get_the_ID() ) );  //@todo check core ticket 47824
        
        /* set lightbox */
        self::$lightbox = 'ut-lightgallery';
		
        /* portfolio settings */
        self::$portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings', true );
        
        if( !self::$portfolio_settings ) {
            
            echo esc_html__( 'Portfolio Showcase does not exist!', 'unitedthemes' ); 
            return '';
            
        }
                                
        /* detail style */
        self::$detailstyle = isset( self::$portfolio_settings['detail_style'] ) ? self::$portfolio_settings['detail_style'] : 'slideup';
        
        /* slideup with title or not */
        self::$slide_up_title = isset( self::$portfolio_settings['portfolio_title'] ) && self::$portfolio_settings['portfolio_title'] == 'off' ? 'data-slideup-title="off"' : 'data-slideup-title="on"';
        
        /* slideup width */
        self::$slide_up_width = isset( self::$portfolio_settings['slideup_width'] ) && self::$portfolio_settings['slideup_width'] == 'fullwidth' ? 'data-slideup-width="fullwidth"' : 'data-slideup-width="centered"';
        
        
        /* get portfolio type */
		$ut_portfolio_type = get_post_meta( self::$token , 'ut_portfolio_type' , true );
                
        /*
        |--------------------------------------------------------------------------
        | Showcase Gallery
        |--------------------------------------------------------------------------
        */
		if( $ut_portfolio_type == 'ut_showcase' ) {
			
			self::$add_showcase_script = true;
			add_action( 'wp_footer' , array(__CLASS__, 'enqueue_showcase_scripts') );
			
			/* create showcase gallery */
			return self::create_showcase_gallery();
            
		}
        
        /*
        |--------------------------------------------------------------------------
        | Portfolio Carousel
        |--------------------------------------------------------------------------
        */
		if( $ut_portfolio_type == 'ut_carousel' ) {
			
			self::$add_showcase_script = true;
			add_action( 'wp_footer' , array(__CLASS__, 'enqueue_showcase_scripts') );
			
			/* create showcase gallery */
			return self::create_portfolio_carousel();
            
		}


       /*
       |--------------------------------------------------------------------------
       | Portfolio React Carousel
       |--------------------------------------------------------------------------
       */
        if( $ut_portfolio_type == 'ut_react_carousel' ) {

            self::$add_showcase_script = true;
            add_action( 'wp_footer' , array(__CLASS__, 'enqueue_react_carousel_scripts') );

            /* create showcase gallery */
            return self::create_portfolio_react_carousel();

        }
		
        /*
        |--------------------------------------------------------------------------
        | Grid / Masonry Gallery
        |--------------------------------------------------------------------------
        */
		if( $ut_portfolio_type == 'ut_masonry' ) {
			
			self::$add_masonry_script = true;
			add_action( 'wp_footer' , array(__CLASS__, 'enqueue_masonry_scripts') );
			
			/* create masonry gallery */
			return self::create_masonry_gallery();
            
		}
        
        /*
        |--------------------------------------------------------------------------
        | Portfolio Filterable Gallery
        |--------------------------------------------------------------------------
        */
        if( $ut_portfolio_type == 'ut_gallery' ) {
			
			self::$add_gallery_script = true;
			add_action( 'wp_footer' , array(__CLASS__, 'enqueue_gallery_scripts') );
			
			/* create masonry gallery */
			return self::create_portfolio_gallery();
            
		}
        
        /*
        |--------------------------------------------------------------------------
        | Portfolio Filterable Gallery Packery
        |--------------------------------------------------------------------------
        */
        if( $ut_portfolio_type == 'ut_packery' ) {
			
			self::$add_gallery_script = true;
			add_action( 'wp_footer' , array(__CLASS__, 'enqueue_packery_scripts') );
			
			/* create masonry gallery */
			return self::create_portfolio_packery_gallery();
            
		}
        		

	}
    
    /*
    |--------------------------------------------------------------------------
    | Placeholder SVG
    |--------------------------------------------------------------------------
    */
    static function create_placeholder_svg( $width , $height ){
            
        // fallback values
        $width = empty( $width ) ? '800' : $width;
        $height = empty( $height ) ? '600' : $height;            

        return 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\' viewBox%3D\'0 0 ' . esc_attr( $width ) . ' ' . esc_attr( $height ) . '\'%2F%3E';            

    }

    /*
    |--------------------------------------------------------------------------
    | Minify Inline CSS
    |--------------------------------------------------------------------------
    */

    static function minify_inline_css( $buffer ) {

        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

        return $buffer;

    }


    /*
    |--------------------------------------------------------------------------
    | Get Portfolio Setting
    |--------------------------------------------------------------------------
    */
    static function get_portfolio_meta( $id, $key, $fallback = false ){

        $meta = get_post_meta( $id, $key, true );

        if( $meta ) {

            return $meta;

        } else {


            return $fallback;

        }


    }


    /*
    |--------------------------------------------------------------------------
    | Custom CSS
    |--------------------------------------------------------------------------
    */
    static function create_custom_css( $custom_settings = array() ) {
        
		$gutter      = !empty( $custom_settings['gutter'] )      ? 'on' : '';
        $gutter_size = !empty( $custom_settings['gutter_size'] ) ? $custom_settings['gutter_size'] : '1';
		
        $css  = '<style id="ut-portfolio-custom-css-' . self::$token . '" type="text/css">';
            
			/* title color */
            $background_color = !empty( self::$portfolio_settings['background_color'] ) ? self::$portfolio_settings['background_color'] : '';
            
			if( $background_color ) {
				
				// filterable portfolio
				$css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-item-container { background: ' . $background_color . ' ; }';
				
				if( $gutter == 'on' ) {
					$css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-item-container { padding-top: ' . $gutter_size * 20 . 'px; }';	
				}

                // carousel portfolio
                $css .= '.ut-portfolio-' . self::$token . '.ut-carousel { background: ' . $background_color . ' !important; }';

				// packery portfolio
				$css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-item-packery-container { background: ' . $background_color . ' ; }';
				
				// masonry portfolio
				$css .= '#ut-masonry-' . self::$token . ' { background: ' . $background_color . ' ; }';

                // react carousel
                $css .= '#ut-react-carousel-container-' . self::$token . ' { background: ' . $background_color . ' ; }';

				
			}				
				
            if( function_exists('ot_get_option') && function_exists('ut_recognized_google_fonts') ) {
                
                if( class_exists( 'UT_Custom_CSS' ) ) {
                    
                    $css_class = new UT_Custom_CSS;
                    
                    $css .= $css_class->font_style_css( array(
                        'selector'           => '.ut-portfolio-' . self::$token . ' .ut-portfolio-info-c h3',
                        'font-type'          => ot_get_option('ut_global_portfolio_title_font_type', 'ut-font' ),   
                        'font-style'         => ot_get_option('ut_portfolio_title_font_style', 'regular' ),
                        'google-font-style'  => ot_get_option('ut_google_portfolio_title_font_style'),
                        'websafe-font-style' => ot_get_option('ut_websafe_portfolio_title_font_style'),
						'custom-font-style'  => ot_get_option('ut_custom_portfolio_title_font_style')
                    ) );
                    
                    $css .= $css_class->font_style_css( array(
                        'selector'           => '.ut-portfolio-' . self::$token . ' .portfolio-title',
                        'font-type'          => ot_get_option('ut_global_portfolio_title_below_font_type', 'ut-font' ),   
                        'font-style'         => ot_get_option('ut_portfolio_title_below_font_style', 'regular' ),
                        'google-font-style'  => ot_get_option('ut_google_portfolio_title_below_font_style'),
                        'websafe-font-style' => ot_get_option('ut_websafe_portfolio_title_below_font_style'),
						'custom-font-style'  => ot_get_option('ut_custom_portfolio_title_below_font_style')
                    ) );
                    
                    $css .= $css_class->font_style_css( array(
                        'selector'           => '.ut-portfolio-' . self::$token . ' .ut-portfolio-info-c span',
                        'font-type'          => ot_get_option('ut_global_portfolio_category_font_type', 'ut-font' ),   
                        'font-style'         => ot_get_option('ut_portfolio_category_font_style', 'regular' ),
                        'google-font-style'  => ot_get_option('ut_google_portfolio_category_font_style'),
                        'websafe-font-style' => ot_get_option('ut_websafe_portfolio_category_font_style'),
						'custom-font-style'  => ot_get_option('ut_custom_portfolio_category_font_style')
                    ) );
                
                } 
                
                /* title color */
                $text_color = !empty( self::$portfolio_settings['text_color'] ) ? self::$portfolio_settings['text_color'] : ot_get_option( 'ut_global_portfolio_title_color', '#FFF' );
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-info-c h3 { color: ' . $text_color . ' ; }';
                
                $text_color = !empty( self::$portfolio_settings['text_color'] ) ? self::$portfolio_settings['text_color'] : ot_get_option( 'ut_global_portfolio_title_below_color', '#151515' );
                $css .= '.ut-portfolio-' . self::$token . ' .portfolio-title { color: ' . $text_color . '; }';

                if( isset( $custom_settings['style'] ) && $custom_settings['style'] == 'style_two' ) {

                    if( isset( self::$portfolio_settings['title_background'] ) && self::$portfolio_settings['title_background'] == 'off' ) {

                        $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-title-wrap, .ut-portfolio-' . self::$token . ' .portfolio-title { background: transparent; padding: 0; margin-top:20px }';

                    } else {

                        /* title background */
                        $title_background_color = !empty( self::$portfolio_settings['title_background_color'] ) ? self::$portfolio_settings['title_background_color'] : ot_get_option( 'ut_global_portfolio_title_background_color', '#FFF' );

                        $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-title-wrap, .ut-portfolio-' . self::$token . ' .portfolio-title { background: ' . $title_background_color . ' ; }';

                    }

                    if( isset( self::$portfolio_settings['title_align'] ) && self::$portfolio_settings['title_align'] != 'center' ) {

                        $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-title-wrap, .ut-portfolio-' . self::$token . ' .portfolio-title { text-align: ' . self::$portfolio_settings['title_align'] . '; }';

                    }
                
                }
                
                /* category category */
                $category_color = !empty( self::$portfolio_settings['category_color'] ) ? self::$portfolio_settings['category_color'] : ot_get_option( 'ut_global_portfolio_category_color', '#FFF' );
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-info-c, .ut-portfolio-' . self::$token . ' .ut-portfolio-info-c span { color: ' . $category_color . ' ; }';
                               
                
            }
            
            if( !empty( self::$portfolio_settings['caption_position'] ) ) {
                
                $positions = explode( '-', self::$portfolio_settings['caption_position'] );
                
                $flex = array(                    
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end'                
                );                
                
                $css .= '.ut-portfolio-' . self::$token . ' .ut-hover-layer .ut-portfolio-info { -webkit-justify-content: ' . $positions[1] . ' !important; justify-content: ' . $positions[1] . ' !important; }';
                $css .= '.ut-portfolio-' . self::$token . ' .ut-hover-layer .ut-portfolio-info-c { align-self: ' . $flex[$positions[0]] . ' !important; text-align: ' . $positions[1] . '; }';
                
                $css .= '.ut-portfolio-' . self::$token . ' .portfolio-style-two .ut-portfolio-info i { text-align: ' . $positions[1] . ' ;}';
            
            }
        
            if( !empty( self::$portfolio_settings['border_radius'] ) && self::$portfolio_settings['image_style'] == 'ut-rounded' ) {
                
                $css .= '.ut-portfolio-' . self::$token . ' .ut-rounded { -webkit-border-radius: ' . self::$portfolio_settings['border_radius'] . 'px; -moz-border-radius: ' . self::$portfolio_settings['border_radius'] . 'px; border-radius: ' . self::$portfolio_settings['border_radius'] . 'px; }';
                
            }
        
            // Filter Colors                
			if( !empty( $custom_settings['filter_deactivate_border'] ) && $custom_settings['filter_deactivate_border'] == 'yes' ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { border:none; padding:0; }';
            }
		
			if( !empty( $custom_settings['filter_font_size'] ) && $custom_settings['filter_font_size'] == 'inherit' ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { font-size: inherit ; }';
            }
		
			if( !empty( $custom_settings['filter_font_weight'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { font-weight: ' . $custom_settings['filter_font_weight'] . ' !important; }';
            }
		
		 	if( !empty( $custom_settings['filter_text_transform'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { text-transform: ' . $custom_settings['filter_text_transform'] . ' !important; }';
            } else {
				$css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { text-transform: uppercase; }';
			}

            if( !empty( $custom_settings['filter_font_style'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { font-style: ' . $custom_settings['filter_font_style'] . ' !important; }';
            }

            if( isset( $custom_settings['filter_letter_spacing'] ) && $custom_settings['filter_letter_spacing'] != '' ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { letter-spacing: ' . $custom_settings['filter_letter_spacing'] . 'em !important; }';
            }
        
            if( !empty( $custom_settings['filter_text_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { color: ' . $custom_settings['filter_text_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_background_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { background: ' . $custom_settings['filter_background_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_border_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a { border-color: ' . $custom_settings['filter_border_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_text_hover_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a:hover { color: ' . $custom_settings['filter_text_hover_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_background_hover_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a:hover { background: ' . $custom_settings['filter_background_hover_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_border_hover_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a:hover { border-color: ' . $custom_settings['filter_border_hover_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_text_selected_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a.selected { color: ' . $custom_settings['filter_text_selected_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_background_selected_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a.selected { background: ' . $custom_settings['filter_background_selected_color'] . ' !important; }';
            }
            
            if( !empty( $custom_settings['filter_border_selected_color'] ) ) {
                $css .= '.ut-portfolio-' . self::$token . ' .ut-portfolio-menu li a.selected { border-color: ' . $custom_settings['filter_border_selected_color'] . ' !important; }';
            }
            
            // SlideUp Colors 
            $portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings', true );    
            
			// Hover Gradient Color
			if( !empty( $portfolio_settings['hover_color'] ) && function_exists('ut_is_gradient') && ut_is_gradient( $portfolio_settings['hover_color'] ) ) {
				
				$css .= ut_create_gradient_css( $portfolio_settings['hover_color'], '.ut-portfolio-' . self::$token . ' .ut-hover-layer', false, 'background', true );
				
			} elseif( !empty( $portfolio_settings['hover_color'] ) && function_exists('ut_is_hex') && ut_is_hex( $portfolio_settings['hover_color'] ) ) {
				
				$css .= '.ut-portfolio-' . self::$token . ' .ut-hover-layer { background: rgba(' . ut_hex_to_rgb( $portfolio_settings["hover_color"] ) . ', ' . $portfolio_settings["hover_opacity"] . '); }';
				
			} elseif( !empty( $portfolio_settings['hover_color'] ) ) {
				
				$css .= '.ut-portfolio-' . self::$token . ' .ut-hover-layer { background: ' . $portfolio_settings['hover_color'] . '; }';
				
			}
				
			// SlideUp Colors 
            if( !empty( $portfolio_settings['slideup_loader_color'] ) ) {
                $css .= '#ut-loader-' . self::$token . ' { color: ' . $portfolio_settings['slideup_loader_color'] . ' ; }';
            }
            
            if( !empty( $portfolio_settings['slideup_loader_background_color'] ) ) {
                $css .= '#ut-loader-' . self::$token . ' { background: ' . $portfolio_settings['slideup_loader_background_color'] . ' ; }';
            }
        
            if( !empty( $portfolio_settings['slideup_arrow_color'] ) ) {
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.next-portfolio-details { color: ' . $portfolio_settings['slideup_arrow_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.prev-portfolio-details { color: ' . $portfolio_settings['slideup_arrow_color'] . ' ; }';
            }
        
            if( !empty( $portfolio_settings['slideup_arrow_hover_color'] ) ) {
                
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.next-portfolio-details:hover { color: ' . $portfolio_settings['slideup_arrow_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.next-portfolio-details:active { color: ' . $portfolio_settings['slideup_arrow_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.next-portfolio-details:focus { color: ' . $portfolio_settings['slideup_arrow_hover_color'] . ' ; }';
                
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.prev-portfolio-details:hover { color: ' . $portfolio_settings['slideup_arrow_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.prev-portfolio-details:active { color: ' . $portfolio_settings['slideup_arrow_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.prev-portfolio-details:focus { color: ' . $portfolio_settings['slideup_arrow_hover_color'] . ' ; }';
            }    
        
            if( !empty( $portfolio_settings['slideup_close_icon_color'] ) ) {
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details { color: ' . $portfolio_settings['slideup_close_icon_color'] . ' ; }';
            }
        
            if( !empty( $portfolio_settings['slideup_close_icon_hover_color'] ) ) {
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details:hover { color: ' . $portfolio_settings['slideup_close_icon_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details:active { color: ' . $portfolio_settings['slideup_close_icon_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details:focus { color: ' . $portfolio_settings['slideup_close_icon_hover_color'] . ' ; }';
            }
            
            if( !empty( $portfolio_settings['slideup_close_background_color'] ) ) {
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details { background: ' . $portfolio_settings['slideup_close_background_color'] . ' ; }';
            }
        
            if( !empty( $portfolio_settings['slideup_close_background_hover_color'] ) ) {
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details:hover { background: ' . $portfolio_settings['slideup_close_background_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details:active { background: ' . $portfolio_settings['slideup_close_background_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-navigation-' . self::$token . ' a.close-portfolio-details:focus { background: ' . $portfolio_settings['slideup_close_background_hover_color'] . ' ; }';
            }
            
            if( !empty( $portfolio_settings['slideup_gallery_icon_color'] ) ) {
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next { color: ' . $portfolio_settings['slideup_gallery_icon_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev { color: ' . $portfolio_settings['slideup_gallery_icon_color'] . ' ; }';
            }
        
            if( !empty( $portfolio_settings['slideup_gallery_icon_hover_color'] ) ) {
                
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next:hover { color: ' . $portfolio_settings['slideup_gallery_icon_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next:active { color: ' . $portfolio_settings['slideup_gallery_icon_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next:focus { color: ' . $portfolio_settings['slideup_gallery_icon_hover_color'] . ' ; }';
                
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev:hover { color: ' . $portfolio_settings['slideup_gallery_icon_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev:active { color: ' . $portfolio_settings['slideup_gallery_icon_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev:focus { color: ' . $portfolio_settings['slideup_gallery_icon_hover_color'] . ' ; }';
            } 
        
            if( !empty( $portfolio_settings['slideup_gallery_icon_background_color'] ) ) {
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next { background: ' . $portfolio_settings['slideup_gallery_icon_background_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev { background: ' . $portfolio_settings['slideup_gallery_icon_background_color'] . ' ; }';
            }
        
            if( !empty( $portfolio_settings['slideup_gallery_icon_background_hover_color'] ) ) {
                
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next:hover { background: ' . $portfolio_settings['slideup_gallery_icon_background_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next:active { background: ' . $portfolio_settings['slideup_gallery_icon_background_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-next:focus { background: ' . $portfolio_settings['slideup_gallery_icon_background_hover_color'] . ' ; }';
                
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev:hover { background: ' . $portfolio_settings['slideup_gallery_icon_background_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev:active { background: ' . $portfolio_settings['slideup_gallery_icon_background_hover_color'] . ' ; }';
                $css .= '#ut-portfolio-details-wrap-' . self::$token . ' .ut-portfolio-media a.flex-prev:focus { background: ' . $portfolio_settings['slideup_gallery_icon_background_hover_color'] . ' ; }';
            } 
        
        $css .= '</style>';
        
        return self::minify_inline_css( $css );
            
    }

    /*
    |--------------------------------------------------------------------------
    | Custom CSS
    |--------------------------------------------------------------------------
    */
    static function create_single_custom_css( $portfolio_args ) {

        /* start query */
        $portfolio_query = new WP_Query( $portfolio_args );

        if( $portfolio_query->have_posts() ) :

            $css  = '<style id="ut-portfolio-single-custom-css" type="text/css">';

            while( $portfolio_query->have_posts() ) : $portfolio_query->the_post();

                // Hover Colors
                $hover_color = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_hover_color' , true );

                if( !empty( $hover_color ) && function_exists('ut_is_gradient') && ut_is_gradient( $hover_color ) ) {

                    $css .= ut_create_gradient_css( $hover_color, '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . ' .ut-hover-layer', false, 'background', true );

                } elseif( !empty( $hover_color ) && function_exists('ut_is_hex') && ut_is_hex( $hover_color ) ) {

                    $css .= '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . ' .ut-hover-layer { background: rgba(' . ut_hex_to_rgb( $hover_color ) . ', 1) !important; }';

                } elseif( !empty( $hover_color ) ) {

                    $css .= '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . ' .ut-hover-layer { background: ' . $hover_color . ' !important; }';

                }

                // Title Color
                $title_color = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_title_color' , true );

                if( !empty( $title_color ) ) {

                    $css .= '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . ' .ut-portfolio-info-c h3 { color: ' . $title_color . ' !important; }';

                }

                // Category Colors
                $category_color = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_category_color' , true );

                if( !empty( $category_color ) ) {

                    $css .= '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . ' .ut-portfolio-info-c span { color: ' . $category_color . ' !important; }';

                }

                // Caption Position
                $caption_position = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_caption_position' , true );

                if( !empty( $caption_position ) && $caption_position != 'showcase' ) {

                    $positions = explode( '-', $caption_position );

                    $flex = array(
                        'top' => 'flex-start',
                        'middle' => 'center',
                        'bottom' => 'flex-end'
                    );

                    $css .= '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . ' .ut-hover-layer .ut-portfolio-info { -webkit-justify-content: ' . $positions[1] . ' !important; justify-content: ' . $positions[1] . ' !important; }';
                    $css .= '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . ' .ut-hover-layer .ut-portfolio-info-c { align-self: ' . $flex[$positions[0]] . ' !important; text-align: ' . $positions[1] . '; }';

                    $css .= '.ut-portfolio-' . self::$token . ' #ut-portfolio-article-' . $portfolio_query->post->ID . '.portfolio-style-two .ut-portfolio-info i { text-align: ' . $positions[1] . ' ;}';

                }

            endwhile;

            $css .= '</style>';

            return $css;

        endif;

        /* reset query */
        wp_reset_postdata();

    }

    /*
    |--------------------------------------------------------------------------
    | Responsive CSS
    |--------------------------------------------------------------------------
    */
    static function create_responsive_css( $gallery_options ) {
        
        // desktop base height
        $desktop_base_height = !empty( $gallery_options['desktop_base_height'] ) ? $gallery_options['desktop_base_height'] : '600';
		$desktop_medium_base_height = !empty( $gallery_options['desktop_medium_base_height'] ) ? $gallery_options['desktop_medium_base_height'] : '450';
		$desktop_small_base_height = !empty( $gallery_options['desktop_small_base_height'] ) ? $gallery_options['desktop_small_base_height'] : '350';
		
		// mobiles base height
        $tablet_base_height  = !empty( $gallery_options['tablet_base_height'] )  ? $gallery_options['tablet_base_height']  : '450';
        $mobile_base_height  = !empty( $gallery_options['mobile_base_height'] )  ? $gallery_options['mobile_base_height']  : '400';
        
        ob_start();
        
        ?>
        
        <style type="text/css">
            
            <?php if ( isset( $gallery_options['style'] ) && $gallery_options['style'] == 'style_one' ) : ?>
                
				@media screen and (min-width: 1921px) {
			
					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default {
						 height:<?php echo $desktop_base_height; ?>px; 
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait {
						height:<?php echo $desktop_base_height * 2; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl {
						height:<?php echo $desktop_base_height * 2; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama {
						height:<?php echo $desktop_base_height; ?>px;    
					}
                
				}
				
				@media screen and (min-width: 1537px) and (max-width: 1920px) {
					
					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default {
						 height:<?php echo $desktop_medium_base_height; ?>px; 
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait {
						height:<?php echo $desktop_medium_base_height * 2; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl {
						height:<?php echo $desktop_medium_base_height * 2; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama {
						height:<?php echo $desktop_medium_base_height; ?>px;    
					}
					
				}
			
				@media screen and (min-width: 1025px) and (max-width: 1536px) {
					
					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default {
						 height:<?php echo $desktop_small_base_height; ?>px; 
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait {
						height:<?php echo $desktop_small_base_height * 2; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl {
						height:<?php echo $desktop_small_base_height * 2; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama {
						height:<?php echo $desktop_small_base_height; ?>px;    
					}
					
				}
				
                @media screen and (max-width: 767px) {
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default {
                         height:<?php echo $mobile_base_height; ?>px; 
                    }
                     
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait {
                        height:<?php echo $mobile_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl {
                        height:<?php echo $mobile_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama {
                        height:<?php echo $mobile_base_height; ?>px;    
                    }                
                
                }
                
                @media screen and (min-width: 768px) and (max-width: 1024px) {
                
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default {
                         height:<?php echo $tablet_base_height; ?>px; 
                    }
                     
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait {
                        height:<?php echo $tablet_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl {
                        height:<?php echo $tablet_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama {
                        height:<?php echo $tablet_base_height; ?>px;    
                    }
                
                }
            
            <?php else :?>
            	
				@media screen and (min-width: 1921px) {			
			
					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default .ut-portfolio-item {
						 height:<?php echo $desktop_base_height; ?>px; 
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait .ut-portfolio-item {
						height:<?php echo ( $desktop_base_height * 2 ) + 80; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl .ut-portfolio-item {
						height:<?php echo ( $desktop_base_height * 2 ) + 80; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama .ut-portfolio-item {
						height:<?php echo $desktop_base_height; ?>px;    
					}
                
				}
			
				@media screen and (min-width: 1537px) and (max-width: 1920px) {		
			
					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default .ut-portfolio-item {
						 height:<?php echo $desktop_medium_base_height; ?>px; 
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait .ut-portfolio-item {
						height:<?php echo ( $desktop_medium_base_height * 2 ) + 80; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl .ut-portfolio-item {
						height:<?php echo ( $desktop_medium_base_height * 2 ) + 80; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama .ut-portfolio-item {
						height:<?php echo $desktop_medium_base_height; ?>px;    
					}
                
				}
			
				@media screen and (min-width: 1025px) and (max-width: 1536px) {		
			
					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default .ut-portfolio-item {
						 height:<?php echo $desktop_small_base_height; ?>px; 
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait .ut-portfolio-item {
						height:<?php echo ( $desktop_small_base_height * 2 ) + 80; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl .ut-portfolio-item {
						height:<?php echo ( $desktop_small_base_height * 2 ) + 80; ?>px;    
					}

					.ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama .ut-portfolio-item {
						height:<?php echo $desktop_small_base_height; ?>px;    
					}
                
				}
					
                @media screen and (max-width: 767px) {
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default .ut-portfolio-item {
                         height:<?php echo $mobile_base_height; ?>px; 
                    }
                     
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait .ut-portfolio-item {
                        height:<?php echo $mobile_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl .ut-portfolio-item {
                        height:<?php echo $mobile_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama .ut-portfolio-item {
                        height:<?php echo $mobile_base_height; ?>px;    
                    }                
                
                }
                
                @media screen and (min-width: 768px) and (max-width: 1024px) {
                
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-default .ut-portfolio-item {
                         height:<?php echo $tablet_base_height; ?>px; 
                    }
                     
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-portrait .ut-portfolio-item {
                        height:<?php echo $tablet_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-xxl .ut-portfolio-item {
                        height:<?php echo $tablet_base_height; ?>px;    
                    }
                    
                    .ut-portfolio-<?php echo self::$token; ?> .ut-masonry-panorama .ut-portfolio-item {
                        height:<?php echo $tablet_base_height; ?>px;    
                    }
                
                }            
            
            <?php endif; ?>
            
        </style>
        
        <?php
        
        return self::minify_inline_css( ob_get_clean() );
    
    }


	/*
    |--------------------------------------------------------------------------
    | Create hidden slide / popup gallery for all gallery types
    |--------------------------------------------------------------------------
    */
	static function create_hidden_popup_portfolio( $portfolio_args = array() , $image_style = "" ) {
		
		/* no query args - we leave here */
		if( empty( $portfolio_args ) ) {
			return;
		}        
		
		/* create hidden portfolio */		
		$hidden_portfolio  = '<div id="ut-loader-' . self::$token . '" class="ut-portfolio-detail-loader"><i class="Bklyn-Core-Rotate-2"></i></div>';
		
        
        /*
        |--------------------------------------------------------------------------
        | Portfolio Navigation
        |--------------------------------------------------------------------------
        */

        $hidden_portfolio .= '<a id="ut-portfolio-details-anchor-' . self::$token . '" href="#" class="ut-vc-offset-anchor-top"></a>';
        $hidden_portfolio .= '<div id="ut-portfolio-details-navigation-' . self::$token . '" class="ut-portfolio-details-navigation grid-container" ' . self::$slide_up_width . '>';
        
            $hidden_portfolio .= '<a class="prev-portfolio-details" data-wrap="' . self::$token . '" href="#"></a>';
            $hidden_portfolio .= '<a class="close-portfolio-details" data-wrap="' . self::$token . '" href="#"></a>';
            $hidden_portfolio .= '<a class="next-portfolio-details" data-wrap="' . self::$token . '" href="#"></a>';
        
        $hidden_portfolio .= '</div>';

        $hidden_portfolio .= '<div id="ut-portfolio-details-wrap-' . self::$token . '" class="ut-portfolio-details-wrap clearfix">';

        $hidden_portfolio .= '<div id="ut-portfolio-details-' . self::$token . '" class="inner ut-portfolio-details">';
						
			/* start query */
			$portfolio_query = new WP_Query( $portfolio_args );	
			
			/* loop trough portfolio items */
			if ($portfolio_query->have_posts()) : while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
				
				global $more; 
				
				/* needed variables */
				$more = $portfolio_details = $optionkey = NULL;
				$post_format = get_post_format();
        
                $ut_portfolio_link_type = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true);
                $ut_portfolio_link_type = empty( $ut_portfolio_link_type ) || !empty( $ut_portfolio_link_type ) && $ut_portfolio_link_type == 'global' ? self::$detailstyle : $ut_portfolio_link_type;
        
                /* hero type setting - new with 2.6 */
                $ut_page_hero_type = get_post_meta($portfolio_query->post->ID , 'ut_page_hero_type' , true);
				
                if( !empty($ut_page_hero_type) ) {
                    
                    if($ut_page_hero_type == 'image' || $ut_page_hero_type == 'animatedimage' || $ut_page_hero_type == 'splithero' || $ut_page_hero_type == 'custom' || $ut_page_hero_type == 'dynamic') {
                        $post_format = '';
                    }
                    
                    if($ut_page_hero_type == 'slider' || $ut_page_hero_type == 'transition' || $ut_page_hero_type == 'tabs') {
                        $post_format = 'gallery';
                    }
                    
                    if($ut_page_hero_type == 'video') {
                        $post_format = 'video';
                        $ut_page_video_source = get_post_meta($portfolio_query->post->ID, 'ut_page_video_source' , true);
                    }
                    
                    /* switch option key */
                    switch ($ut_page_hero_type) {
                    
                        case 'animatedimage':
                        $optionkey = 'ut_page_hero_animated_image';
                        break;
                        
                        default:  
                        $optionkey = 'ut_page_hero_image';  
                    
                    }                  
                
                }  
				
		
				/* new portfolio settings since 4.7.4 */
				$has_media_type = false;
				$onpage_media_type = get_post_meta( $portfolio_query->post->ID , 'ut_onpage_portfolio_media_type' , true);
		
				if( !empty( $onpage_media_type ) ) {
					
					if( $onpage_media_type == 'image' ) {
						$ut_page_hero_type = 'image';
						$optionkey = 'ut_onpage_portfolio_image';
						$post_format = '';	
					}
					
					if( $onpage_media_type == 'gallery' ) {
						$ut_page_hero_type = 'gallery';
						$post_format = 'gallery';	
					}
					
					if( $onpage_media_type == 'video' ) {
						$ut_page_hero_type = 'video';
						$post_format = 'video';	
					}
					
					$has_media_type = true;
					
				}
				/* end new portfolio settings */
		
				
				/* meta data*/
				$ut_portfolio_details = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_details', true );
				
				/* grab up the featured image url */
				$fullsize = $thumbnail = $portfolio_detail_image = wp_get_attachment_url( get_post_thumbnail_id( $portfolio_query->post->ID ) );				
				
                /* check if there is a detail image available */
                $portfolio_detail_image_data = get_post_meta( $portfolio_query->post->ID , $optionkey , true );                
                
                if( is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data['background-image']) ) {
                        
                    $portfolio_detail_image = $portfolio_detail_image_data['background-image'];
                    
                } elseif( !is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data) ) {
                    
                    $portfolio_detail_image = $portfolio_detail_image_data;
                    
                }
						
                // get image id 
                $image_ID  = ut_get_image_id( $portfolio_detail_image );
                $full_size = wp_get_attachment_image_src( $image_ID, 'full' );
                
                // ratio
                $ratio = !empty( $full_size[1] ) && !empty( $full_size[2] ) ? $full_size[1] / $full_size[2] : false;        
        
                if( $ratio ) {
                    
					if( function_exists('ot_get_option') ) {
						
						$targetWidth  = ot_get_option( 'ut_site_custom_width', '1200' );
						
					} else {
						
						$targetWidth  = 1160;
						
					}					
					
					$targetHeight = round( $targetWidth / $ratio );
                    
                    $portfolio_detail_image = ut_resize( $full_size[0], $targetWidth, $targetHeight, true, true, true );
                    
                }        
        
                /* check if portfolio leads to internal page - so we don't need to show the details anymore */
                if( $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'external' || $ut_portfolio_link_type == 'popup' ) {
                    continue;
                }
                                
				/* create hidden content div first */
				$hidden_portfolio .= '<div id="ut-portfolio-detail-' . $portfolio_query->post->ID . '" class="animated ut-portfolio-detail clearfix" data-post="' . $portfolio_query->post->ID . '" data-format="' . $post_format . '">';
										
					/* portfolio details */
					if( is_array( $ut_portfolio_details ) && !empty( $ut_portfolio_details ) && ( count($ut_portfolio_details) > 1 ) ) {
						
						$portfolio_details .= '<ul class="ut-portfolio-list clearfix">';
						
							foreach( $ut_portfolio_details as $key => $detail ) {
								
                                if( !empty($detail['title']) && !empty($detail['value']) ) {   
								    $portfolio_details .= '<li><strong>' . $detail['title'] . ': </strong>' . $detail['value'] . '</li>';
                                }
                                
							}
						
						$portfolio_details .= '</ul>';
						
					}		            
		
					// new since 4.7.4
					if( get_post_meta( $portfolio_query->post->ID , 'ut_slide_up_portfolio_hide_media' , true ) != 'on' ) {
						
						if( self::$vc_active ) {

						   /* start markup */
						   $hidden_portfolio .= '<div class="ut-portfolio-media">'; 

						} else {

						   /* start markup */
						   $hidden_portfolio .= '<div class="grid-80 prefix-10 mobile-grid-100 tablet-grid-80 tablet-prefix-10 ut-portfolio-media">';    

						}

						if( ! post_password_required() ) {

								// fallback to old system prioer or 4.7.4 new media type
								if( empty( $ut_page_hero_type ) ) {

									/*
									|--------------------------------------------------------------------------
									| Standard Post Format
									|--------------------------------------------------------------------------
									*/
									if( empty( $post_format ) ) {

										/* featured image */
										$hidden_portfolio .= '<img class="ut-portfolio-image ut-load-me ' . $image_style . '" alt="' . get_the_title() . '" data-original="' . $portfolio_detail_image . '">';								

									}							

									/*
									|--------------------------------------------------------------------------
									| Video Post Format 
									|--------------------------------------------------------------------------
									*/
									if( $post_format == 'video' ) {

										/* add video to hidden portfolio detail*/
										$hidden_portfolio .= '<div id="ut-video-call-'.$portfolio_query->post->ID.'" class="ut-video-call"></div>';								

									}							

									/*
									|--------------------------------------------------------------------------
									| Gallery Post Format 
									|--------------------------------------------------------------------------
									*/
									if( $post_format == 'gallery' ) {

										$hidden_portfolio .= ut_portfolio_flex_slider( $portfolio_query->post->ID , false , $image_style );								

									}


								// new markup for 2.6
								} else {

									if( $ut_page_hero_type == 'image' || $ut_page_hero_type == 'animatedimage' || $ut_page_hero_type == 'splithero' || $ut_page_hero_type == 'custom' || $ut_page_hero_type == 'dynamic' ) {

										$caption = !empty( get_post( get_post_thumbnail_id( $portfolio_query->post->ID ) )->post_excerpt ) ? get_post( get_post_thumbnail_id( $portfolio_query->post->ID ) )->post_excerpt : '';

										if( $caption ) {

											$hidden_portfolio .= '<figure class="ut-post-thumbnail-caption-wrap" data-caption="' . esc_attr( $caption ) . '">';

												/* featured image */
												$hidden_portfolio .= '<img class="ut-portfolio-image ut-load-me ' . $image_style . '" alt="' . get_the_title() . '" data-original="' . $portfolio_detail_image . '">';

												$hidden_portfolio .='<figcaption class="ut-post-thumbnail-caption">' . $caption . '</figcaption>';

											$hidden_portfolio .= '</figure>';

										} else {

											// featured image
											$hidden_portfolio .= '<img class="ut-portfolio-image ut-load-me ' . $image_style . '" alt="' . get_the_title() . '" data-original="' . $portfolio_detail_image . '">';

										}


									}

									if( $ut_page_hero_type == 'gallery' || $ut_page_hero_type == 'slider' || $ut_page_hero_type == 'transition' || $ut_page_hero_type == 'tabs' ) {

										// create flex slider list
										$hidden_portfolio .= ut_portfolio_flex_slider( $portfolio_query->post->ID , false , $image_style, $ut_page_hero_type );

									}

									if( $ut_page_hero_type == 'video' ) {

										// create video placeholder div - ut-ajax call media will fill it with content on request
										$hidden_portfolio .= '<div id="ut-video-call-'.$portfolio_query->post->ID.'" class="ut-video-call"></div>';	

									}

								}

							} else {

								$hidden_portfolio .= '<div class="ut-password-protected">' . __('Password Protected Portfolio' , 'ut_portfolio_lang') . '</div>';
								$the_content = get_the_password_form();

							}
					
						$hidden_portfolio .= '</div>';
					
					}
						
                    if( self::$vc_active ) {
        
                        $hidden_portfolio .= '<div class="entry-content clearfix">';
				    
                    } else {
                        
                        $hidden_portfolio .= '<div class="grid-70 prefix-15 mobile-grid-100 tablet-grid-70 tablet-prefix-15 entry-content clearfix">';    
                        
                    }                        
                        
					$hidden_portfolio .= '</div>';					
					
				$hidden_portfolio .= '</div>';
				
			/* end loop */
			endwhile; endif;
			
			/* reset query */
			wp_reset_postdata();
			
			$hidden_portfolio .= '</div>';
			
		$hidden_portfolio .= '</div>';
		
		$hidden_portfolio .= '<div class="clear"></div>';
		
		return $hidden_portfolio;
		
	}
	
    /*
    |--------------------------------------------------------------------------
    | Showcase Gallery
    |--------------------------------------------------------------------------
    */
	static function create_showcase_gallery() {
		
		global $paged;
				
		/* settings */
		$portfolio_categories = get_post_meta( self::$token , 'ut_portfolio_categories' );
        
		/* global portfolio settings */
        $portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings', true );
        $portfolio_settings = $portfolio_settings[0];
		
        /* showcase options */
        $showcase_options = get_post_meta( self::$token , 'ut_showcase_options' );
        $showcase_options = $showcase_options[0];
        
		/* fallback if no meta has been set */
		$portfolio_per_page   = !empty($portfolio_settings['posts_per_page']) ? $portfolio_settings['posts_per_page'] : 4 ;
		$portfolio_categories = !empty($portfolio_categories) ? $portfolio_categories : array();
						
        /* portfolio query terms */
		$portfolio_terms = array();
		if( is_array($portfolio_categories[0])  && !empty($portfolio_categories[0]) ) {

			foreach( $portfolio_categories[0] as $key => $value) {                
                array_push($portfolio_terms , $key);
			}
			
		}
		
        /* query args */
        $portfolio_args = array(
            
            'post_type'      => 'portfolio',
            'posts_per_page' => $portfolio_per_page,
            'paged'          => $paged,
            'tax_query'      => array( array(
                    'taxonomy' => 'portfolio-category',
                    'terms'    => $portfolio_terms,
                    'field'    => 'term_id'  
            ) )
            
        
        ); 
        
        /* start query */
        $portfolio_query = new WP_Query( $portfolio_args );
		        
        /* needed javascript */
		$showcase = self::generate_showcase_script( $showcase_options );		
		
        /* needed css */
        $showcase .= self::create_custom_css();
        
        /* text color */
        if( function_exists('ot_get_option') ) {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : ot_get_option('ut_global_portfolio_title_color','#FFF');
            
        } else {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : '#FFF';
            
        }
        
        /* start showcase */
		$showcase .= '<div class="ut-portfolio-wrap ut-portfolio-' . self::$token . ' ' . $portfolio_settings["optional_class"] . '" ' . self::$slide_up_title . ' ' . self::$slide_up_width . ' data-textcolor="' . esc_attr( $textcolor ) . '" data-opacity="' . $portfolio_settings["hover_opacity"] . '" data-hovercolor="' . ut_hex_to_rgb($portfolio_settings["hover_color"]) . '">';
		
		$showcase .= '<div id="slider_' . self::$token . '" class="flexslider ut-showcase">';
		$showcase .= '<ul class="slides">';				
		
        /* create thumbnail navigation */
        if( isset($showcase_options['display_thumbnail_navigation'] ) ) {
            $showcase_navigation  = '<div id="carousel_' . self::$token . '" class="flexslider ut-showcase-navigation">';
            $showcase_navigation .= '<ul class="slides">';
        }
        				
					/* loop trough portfolio items */
					if ($portfolio_query->have_posts() ) : while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
												
						if ( has_post_thumbnail() && ! post_password_required() ) {		
            
							$fullsize   = wp_get_attachment_url( get_post_thumbnail_id( $portfolio_query->post->ID ) );
						
							$showcase .= '<li>';
								$showcase .= '<img alt="' . get_the_title() . '" src="' . $fullsize . '" />';
							$showcase .= '</li>';
							
							/* create showcase navigation items */
							if( isset($showcase_options['display_thumbnail_navigation'] ) ) {
								
								$thumbnail = ut_resize( $fullsize , 210 , 140 , true , true , true );
								
									$showcase_navigation .= '<li class="ut-hover">';
										
										$showcase_navigation .= '<a href="#">';
										
										$showcase_navigation .= '<figure>';
											$showcase_navigation .= '<img src="' . $thumbnail . '" />';
										$showcase_navigation .= '</figure>';
										
										$showcase_navigation .= '<div class="ut-hover-layer">';
											
											$showcase_navigation .= '<div class="ut-portfolio-info">';
                                                
                                                $showcase_navigation .= '<div class="ut-portfolio-info-c">';
											
                                                    $showcase_navigation .= '<h3>' . get_the_title() . '</h3>';											
                                                    $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                                                    $showcase_navigation .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';
										    
                                            $showcase_navigation .= '</div>';
                                            	
										$showcase_navigation .= '</div>';
										
									$showcase_navigation .= '</div>';
									
									$showcase_navigation .= '</a>';
									
								$showcase_navigation .= '</li>';                           
								
							}
						
						} else {
							
							
										
						}                       
						
					endwhile; endif;
				
				/* reset query */
				 wp_reset_postdata();
	    
        /* end showcase navigation */
        if( isset($showcase_options['display_thumbnail_navigation'] ) ) {
            $showcase_navigation .= '</ul>';
            $showcase_navigation .= '</div>';
        }
        
        /* end showcase */		
		$showcase .= '</ul>';
		$showcase .= '</div>';
		        
        /* return final showcase */
        if( isset($showcase_options['display_thumbnail_navigation'] ) ) {
            return $showcase . $showcase_navigation . '</div>';
        } else {
            return $showcase . '</div>';
        }
        
	}
	
	static function generate_showcase_script( $showcase_options ) {
		
		/* settings */
		$container = self::$token;
		$thumbnail_navigation = '';
		$sync = '';
		
		/* showcase navigation script */
		if( isset( $showcase_options['display_thumbnail_navigation'] ) ) :
			
			$thumbnail_navigation = "
			$('#carousel_$container').flexslider({
            	animation: 'slide',
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 210,
                itemMargin: 5,
                asNavFor: '#slider_$container'
            });";
			
			// sync for slider function
			$sync = "sync: '#carousel_$container'";

		endif;
		/* end showcase navigation */ 
        
                
        /* showcase options */
        $animation      = !empty($showcase_options['animation']) ? $showcase_options['animation'] : 'slide';
        $slideshowSpeed = !empty($showcase_options['slideshowSpeed']) ? $showcase_options['slideshowSpeed'] : '7000'; 
        $animationSpeed = !empty($showcase_options['animationSpeed']) ? $showcase_options['animationSpeed'] : '600'; 
        $directionNav   = !empty($showcase_options['directionNav']) ? 'true' : 'false';
        $smoothHeight   = !empty($showcase_options['smoothHeight']) ? 'true' : 'false';
        
        /* main showcase script */
        $script = "
		<script type='text/javascript'>		
        
			(function($){
			
				$(window).on('load', function () {
                    
				    $('#slider_$container').ut_require_js({
                         plugin: 'flexslider',
                         source: 'flexslider',
                         callback : function ( element ) {
                             
                            $thumbnail_navigation
				
                            element.flexslider({
                                
                                animation: '$animation',
                                controlNav: false,
                                animationLoop: false,
                                slideshow: true,
                                slideshowSpeed: $slideshowSpeed,
                                animationSpeed: $animationSpeed,
                                directionNav: $directionNav,
                                smoothHeight: $smoothHeight,                                
                                $sync                                
                            });
                             
                         }
                     });
					
				});
			
			})(jQuery);		
		 
		</script>";
		/* end main showcase script */       
               
		
        /* output javascript */
		return ut_compress_java($script);
		
	}
	
    /*
    |--------------------------------------------------------------------------
    | Portfolio Carousel
    |--------------------------------------------------------------------------
    */
    static function create_portfolio_carousel() { 
        				
		/* settings */
		$portfolio_categories = get_post_meta( self::$token , 'ut_portfolio_categories' );
        
		/* global portfolio settings */
        $portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings', true );
		
		/* showcase options */
        $carousel_options = get_post_meta( self::$token , 'ut_carousel_options', true );
        		        
		/* fallback if no meta has been set */
		$portfolio_per_page   = !empty($portfolio_settings['posts_per_page']) ? $portfolio_settings['posts_per_page'] : 4 ;		
		$portfolio_categories = !empty($portfolio_categories) ? $portfolio_categories : array();
						
        /* portfolio query terms */
		$portfolio_terms = array();
		if( is_array($portfolio_categories[0])  && !empty($portfolio_categories[0]) ) {

			foreach( $portfolio_categories[0] as $key => $value) {                
                array_push($portfolio_terms , $key);
			}
			
		}
		        
		/* query args */
        $portfolio_args = array(
            
            'post_type'      => 'portfolio',
            'posts_per_page' => $portfolio_per_page,
            'tax_query'      => array( array(
                    'taxonomy' => 'portfolio-category',
                    'terms'    => $portfolio_terms,
                    'field'    => 'term_id'  
            ) )
            
        
        );

        /* start query */
        $portfolio_query = new WP_Query( $portfolio_args );

		// carousel columns
		$columns   = !empty($carousel_options['columns']) ? $carousel_options['columns'] : 4;

        /* needed css */
        $carousel = self::create_custom_css( $carousel_options );
        
		/* gallery style */
		$gallery_style      = !empty($carousel_options['style']) ? $carousel_options['style'] : 'style_one';

        $carousel_columns   = !empty($carousel_options['columns']) ? $carousel_options['columns'] : 4;
        
		$gallery_style_class = NULL;
		
		switch ( $gallery_style ) {
			
			case 'style_one':
				$gallery_style_class = 'portfolio-style-one';
			break;
				
			case 'style_two':
				$gallery_style_class = 'portfolio-style-two';
			break;

		}
		
		/* create hidden gallery */
		$carousel .= self::create_hidden_popup_portfolio( $portfolio_args , $portfolio_settings['image_style'] );
        
        if( function_exists('ot_get_option') ) {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : ot_get_option('ut_global_portfolio_title_color','#FFF');
            
        } else {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : '#FFF';
            
        }
        
        /* create carousel */
        $carousel .= '<div id="carousel_' . self::$token . '" class="ut-portfolio-carousel ut-portfolio-wrap ut-portfolio-' . self::$token . ' flexslider ut-carousel ' . $gallery_style_class . ' ut-carousel-' . $carousel_columns . '-col" ' . self::$slide_up_title . ' ' . self::$slide_up_width . ' data-textcolor="' . esc_attr( $textcolor ) . '" data-opacity="' . $portfolio_settings["hover_opacity"] . '" data-hovercolor="' . ut_hex_to_rgb($portfolio_settings["hover_color"]) . '" data-columns="' . $columns . '">';
        $carousel .= '<ul class="slides">';
        				
            /* loop trough portfolio items */
            if ($portfolio_query->have_posts()) : while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
                                
                if ( has_post_thumbnail() ) {

                    $article_classes = array();

                    $fullsize = $thumbnail = $portfolio_detail_image = wp_get_attachment_url( get_post_thumbnail_id( $portfolio_query->post->ID ) );
                    $caption = get_post( get_post_thumbnail_id( $portfolio_query->post->ID ) )->post_excerpt;					
                    
                    /* hero type setting - new with 2.6 */
                    $ut_page_hero_type = get_post_meta($portfolio_query->post->ID , 'ut_page_hero_type' , true);
                                        
                    $optionkey = 'ut_page_hero_image';
                    $post_format = '';
                    
                    if( !empty($ut_page_hero_type) ) {
                        
                        if($ut_page_hero_type == 'image' || $ut_page_hero_type == 'animatedimage' || $ut_page_hero_type == 'splithero' || $ut_page_hero_type == 'custom' || $ut_page_hero_type == 'dynamic') {
                            
                            
                        }
                        
                        if($ut_page_hero_type == 'slider' || $ut_page_hero_type == 'transition' || $ut_page_hero_type == 'tabs') {
                            $post_format = 'gallery';
                        }
                        
                        if($ut_page_hero_type == 'video') {
                            
                            $post_format = 'video';
                            $ut_page_video_source = get_post_meta($portfolio_query->post->ID, 'ut_page_video_source' , true);
                            
                        }
                        
                        /* switch option key */
                        switch ($ut_page_hero_type) {
                        
                            case 'animatedimage':
                            $optionkey = 'ut_page_hero_animated_image';
                            break;
                            
                            default:  
                            $optionkey = 'ut_page_hero_image';  
                        
                        }                  
                    
                    } /* end ut_page_hero_type */
                    
                                      
					/* new portfolio settings since 4.7.4 */
					$has_media_type = false;
					$onpage_media_type = get_post_meta( $portfolio_query->post->ID , 'ut_onpage_portfolio_media_type' , true);

					if( !empty( $onpage_media_type ) ) {

						if( $onpage_media_type == 'image' ) {
							$ut_page_hero_type = 'image';
							$optionkey = 'ut_onpage_portfolio_image';
							$post_format = '';	
						}

						if( $onpage_media_type == 'gallery' ) {
							$ut_page_hero_type = 'gallery';
							$post_format = 'gallery';	
						}

						if( $onpage_media_type == 'video' ) {
							$ut_page_hero_type = 'video';
							$post_format = 'video';	
						}

						$has_media_type = true;

					}
					/* end new portfolio settings */
										
					
					/* check if there is a detail image available */
                    $portfolio_detail_image_data = get_post_meta( $portfolio_query->post->ID , $optionkey , true );                
                    
                    if( is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data['background-image']) ) {
                            
                        $portfolio_detail_image = $portfolio_detail_image_data['background-image'];
                        
                    } elseif( !is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data) ) {
                        
                        $portfolio_detail_image = $portfolio_detail_image_data;
                        
                    }

					/* cropping dimensions */
					$width  = !empty($carousel_options['crop_size_x']) ? $carousel_options['crop_size_x'] : '1000';
					$height = !empty($carousel_options['crop_size_y']) ? $carousel_options['crop_size_y'] : '800';
					// $thumbnail = ut_resize( $fullsize , $width , $height , true , true , true );
                    
					/* link settings */
					$ut_portfolio_link_type = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true);
                    $ut_portfolio_link_type = empty( $ut_portfolio_link_type ) || !empty( $ut_portfolio_link_type ) && $ut_portfolio_link_type == 'global' ? self::$detailstyle : $ut_portfolio_link_type;                    
                    $external = false;
                    $target = '_blank';
                    
                    if( $ut_portfolio_link_type == 'external' ) {
                       
                       /* get link user haas to set */ 
                       $external = get_post_meta( $portfolio_query->post->ID , 'ut_external_link' , true);

	                   $external_target = get_post_meta( $portfolio_query->post->ID , 'ut_external_link_target' , true);
	                   $target = !empty( $external_target ) ? $external_target : $target;
                        
                    } elseif( $ut_portfolio_link_type == 'internal' ) {
                       
                       /* get permalink to single portfolio */
                       $external = get_permalink( $portfolio_query->post->ID );
                       $target = '_self';
                       
                    }

                    $a_tag = false;

                    // preview video
                    $preview_video = ut_get_portfolio_preview_video( $portfolio_query->post->ID, $post_format,self::$token );

                    if( $preview_video ) {

                        $article_classes[] = 'ut-has-background-video';

                        $play_event = get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_video_play_event', true );

                        if( $play_event == 'on_load' ) {

                            $article_classes[] = 'ut-video-is-playing';

                        }

                    }

                    // tilt effect
                    if( $gallery_style == 'style_three' ) {
                        $article_classes[] = 'ut-portfolio-article-tilt';
                    }

                    // hover visibility
                    if( isset( $carousel_options['caption_visibility'] ) && $carousel_options['caption_visibility'] == 'permanent' ) {
                        $article_classes[] = 'ut-portfolio-article-permanent-caption';
                    }

	                // custom cursor
	                $data_cursor_skin = '';

	                if( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) {

		                $data_cursor_skin = 'data-cursor-skin="' . esc_attr( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) . '"';

	                }

					if( $gallery_style == 'style_one' || $gallery_style == 'style_three' ) {

						$carousel .= '<li id="ut-portfolio-article-' . $portfolio_query->post->ID . '" class="ut-carousel-item ut-hover ' . implode( " ", $article_classes ) . '">';

							$title= str_ireplace('"', '', trim(get_the_title()));
							
								/* link markup for detail slideup */
								if( $ut_portfolio_link_type == 'slideup' || $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'onepage' || $ut_portfolio_link_type == 'external' ) {
									
									/* has external link */
									if($external) {
									
										$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
									
									} else {
									
										$carousel .= '<a class="ut-portfolio-link ' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" data-wrap="' . self::$token . '" data-post="' . $portfolio_query->post->ID . '" href="#" ' . $data_cursor_skin . '>';
									
									}

                                    $a_tag = true;
									
								}
								
								/* link markup for image popup */
								if( $ut_portfolio_link_type == 'popup' && !$a_tag ) {
									
									$popuplink = NULL;
                                    
                                    $mini = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-mini' );
                                    $mini = !empty( $mini[0] ) ? $mini[0] : "";

                                    $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                                    $portfolio_cats = ut_generate_cat_list( $portfolio_cats );

                                    if( $has_media_type ) {

                                        $link_attributes = ut_get_morphbox_meta( ut_portfolio_get_image_id( $portfolio_detail_image ), $portfolio_query->post->ID, '', $portfolio_cats );
                                        $lightgallery = ut_get_morphbox_fullscreen( ut_portfolio_get_image_id( $portfolio_detail_image ) , 'ut-lightbox');

									} else {

                                        $link_attributes = ut_get_morphbox_meta( get_post_thumbnail_id( $portfolio_query->post->ID ), $portfolio_query->post->ID, '', $portfolio_cats );
                                        $lightgallery = ut_get_morphbox_fullscreen( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-lightbox' );

									}

                                    /* attributes string */
                                    $link_attributes = implode(' ', array_map(
                                        function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                                        $link_attributes,
                                        array_keys( $link_attributes )
                                    ) );

									$lightgallery = !empty( $lightgallery[0] ) ? $lightgallery[0] : $portfolio_detail_image;
                                    
									/* image post or audio post */
									if( empty( $post_format ) || $post_format == 'audio' ) {
										
										/* has external link */
										if( $external ) {
										
											$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
										
										} else {
										    
                                            $carousel .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$carousel  .= '<a data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" class="' . $portfolio_settings['image_style'] . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="'. esc_url( $lightgallery ) .'" ' . $link_attributes . ' ' . $data_cursor_skin . '>';
										
										}
										
									}
									
									/* video post */
									if( $post_format == 'video' ) {
										
                                        /* has external link */
										if($external) {
										
											$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
										
										} else {                                           
                                            
                                            // try to get video media
											$video_media = ut_get_portfolio_format_video_media( $portfolio_query->post->ID );

											if( !$video_media )
											$video_media = get_the_content();

											// check for possible Iframe
											$video_data = '';

											if( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['iframe'] ) ) {

												$video_data = 'data-iframe="true"';
												$popuplink = ut_get_portfolio_format_video_content( $video_media['iframe'] );

											} elseif( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['selfhosted'] ) ) {

												$carousel .= $video_media['selfhosted'];
												$video_data = 'data-html="#ut-selfhosted-lightbox-player-' . $portfolio_query->post->ID . '"';

											} else {

												// try to catch video url
												$popuplink = ut_get_portfolio_format_video_content( $video_media );

											}
                                            
                                            $carousel .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
                                            $carousel .= '<a data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" ' . $video_data . ' class="' . $portfolio_settings['image_style'] . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="' . esc_url( $popuplink ) . '" ' . $data_cursor_skin . '>';
                                            
                                        }
                                        
									}
									
									/* gallery post */
									if( $post_format == 'gallery' ) {
										
                                        /* has external link */
										if($external) {
										
											$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
										
										} else {
                                        
                                            $carousel .= ut_portfolio_lightgallery( $portfolio_query->post->ID , self::$token , $ut_page_hero_type );                                            
                                            $carousel .= '<a data-exThumbImage="' . $mini . '" class="ut-portfolio-popup-'.$portfolio_query->post->ID.' '. $portfolio_settings['image_style'] . '" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . esc_url( $lightgallery ) . '" ' . $data_cursor_skin . '>';
									    
                                        }
                                        
									}								
									
								}
								
								$carousel .= '<figure>';
									$carousel .= UT_Adaptive_Image::create( get_post_thumbnail_id( $portfolio_query->post->ID ), array( $width , $height ), true, 'landscape', false, 'skip-lazy ut-lazy-wait' );
									// $carousel .= '<img alt="' . get_the_title() . '" src="' . $thumbnail . '" />';
								$carousel .= '</figure>';

                                $carousel .= $preview_video;

								$carousel .= '<div class="ut-hover-layer">';
									$carousel .= '<div class="ut-portfolio-info">';
									    $carousel .= '<div class="ut-portfolio-info-c">';                                        
                                        	
                                            /* Portfolio Slogan */										
                                            $carousel .= '<h3 class="portfolio-title">' . get_the_title() . '</h3>';
                                            
                                            $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                                            $carousel .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';
                                            
										$carousel .= '</div>';
									$carousel .= '</div>';
								$carousel  .= '</div>';
								
							$carousel  .= '</a>';						
							
						$carousel  .= '</li>';
					
					}
					
					if( $gallery_style == 'style_two' ) {    
						
                        $link_trigger_id = 'ut-portfolio-trigger-link-' . self::$token . '-' . esc_attr( $portfolio_query->post->ID );
                        
						$carousel  .= '<li id="ut-portfolio-article-' . $portfolio_query->post->ID . '" class="ut-carousel-item ut-hover ' . implode( " ", $article_classes ) . '">';

                            $carousel .= $preview_video;

							$title= str_ireplace('"', '', trim(get_the_title()));
							
								/* link markup for detail slideup */
								if( $ut_portfolio_link_type == 'slideup' || $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'onepage' || $ut_portfolio_link_type == 'external' ) {
									
									/* has external link */
									if($external) {
									
										$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
									
									} else {
									
										$carousel  .= '<a id="' . $link_trigger_id . '" class="ut-portfolio-link ' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . self::$token . '" data-post="' . $portfolio_query->post->ID . '" href="#" ' . $data_cursor_skin . '>';
									
									}

                                    $a_tag = true;
									
								}
								
								/* link markup for image popup */
								if( $ut_portfolio_link_type == 'popup' && !$a_tag ) {
									
									$popuplink = NULL;
									
                                    $mini = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-mini' );
                                    $mini = !empty( $mini[0] ) ? $mini[0] : "";

                                    $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                                    $portfolio_cats = ut_generate_cat_list( $portfolio_cats );

                                    if( $has_media_type ) {

                                        $link_attributes = ut_get_morphbox_meta( ut_portfolio_get_image_id( $portfolio_detail_image ), $portfolio_query->post->ID, '', $portfolio_cats );
                                        $lightgallery = ut_get_morphbox_fullscreen( ut_portfolio_get_image_id( $portfolio_detail_image ) , 'ut-lightbox');

									} else {

                                        $link_attributes = ut_get_morphbox_meta( get_post_thumbnail_id( $portfolio_query->post->ID ), $portfolio_query->post->ID, '', $portfolio_cats );
                                        $lightgallery = ut_get_morphbox_fullscreen( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-lightbox' );

									}

                                    /* attributes string */
                                    $link_attributes = implode(' ', array_map(
                                        function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                                        $link_attributes,
                                        array_keys( $link_attributes )
                                    ) );

									$lightgallery = !empty( $lightgallery[0] ) ? $lightgallery[0] : $portfolio_detail_image;                                  
                                    
									/* image post or audio post */
									if( empty( $post_format ) || $post_format == 'audio' ) {
										
										/* has external link */
										if($external) {
										
											$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
										
										} else {										




                                            $carousel .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$carousel .= '<a id="' . $link_trigger_id . '" data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" class="' . $portfolio_settings['image_style'] . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="'. esc_url( $lightgallery ) .'" ' . $link_attributes . ' ' . $data_cursor_skin . '>';
											
										}
										
									}
									
									/* video post */
									if( $post_format == 'video' ) {
										
                                        /* has external link */
										if($external) {
										
											$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
										
										} else {
                                                                                        
                                           	// try to get video media
											$video_media = ut_get_portfolio_format_video_media( $portfolio_query->post->ID );

											if( !$video_media )
											$video_media = get_the_content();

											// check for possible Iframe
											$video_data = '';

											if( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['iframe'] ) ) {

												$video_data = 'data-iframe="true"';
												$popuplink = ut_get_portfolio_format_video_content( $video_media['iframe'] );

											} elseif( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['selfhosted'] ) ) {

												$carousel .= $video_media['selfhosted'];
												$video_data = 'data-html="#ut-selfhosted-lightbox-player-' . $portfolio_query->post->ID . '"';

											} else {

												// try to catch video url
												$popuplink = ut_get_portfolio_format_video_content( $video_media );

											}
                                            
                                            $carousel .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
                                            $carousel  .= '<a id="' . $link_trigger_id . '" data-exThumbImage="' . $mini . '" ' . $video_data . ' data-rel="' . self::$lightbox . '" class="' . $portfolio_settings['image_style'] . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="' . esc_url( $popuplink ) . '" ' . $data_cursor_skin . '>';
										
                                        }
                                        
									}
									
									/* gallery post */
									if( $post_format == 'gallery' ) {
										
                                        /* has external link */
										if( $external ) {
										
											$carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';
										
										} else {
                                            
                                            $carousel .= ut_portfolio_lightgallery( $portfolio_query->post->ID , self::$token , $ut_page_hero_type );
										    $carousel .= '<a id="' . $link_trigger_id . '" data-exThumbImage="' . $mini . '" class="ut-portfolio-popup-'.$portfolio_query->post->ID.' '. $portfolio_settings['image_style'] . '" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . esc_url( $lightgallery ) . '" ' . $data_cursor_skin . '>';
									    
                                        }
                                        
									}								
									
								}
								
								$carousel .= '<figure>';
						            $carousel .= UT_Adaptive_Image::create( get_post_thumbnail_id( $portfolio_query->post->ID ), array( $width , $height ), true, 'landscape', false, 'skip-lazy ut-lazy-wait' );
									// $carousel .= '<img alt="' . get_the_title() . '" src="' . $thumbnail . '" />';
								$carousel .= '</figure>';
								
								$carousel .= '<div class="ut-hover-layer">';
									$carousel .= '<div class="ut-portfolio-info">';
                                        $carousel .= '<div class="ut-portfolio-info-c">';

						                    $caption_content = isset( $carousel_options['style_2_caption_content'] ) ? $carousel_options['style_2_caption_content'] : 'category_icon';

						                    if( $caption_content == 'category_icon' ) {

							                    if ( $post_format == 'video' ) {
								                    $carousel .= '<i class="fa fa-film fa-lg"></i>';
							                    }

							                    if ( $post_format == 'audio' ) {
								                    $carousel .= '<i class="fa fa-headphones fa-lg"></i>';
							                    }

							                    if ( $post_format == 'gallery' ) {
								                    $carousel .= '<i class="fa fa-camera-retro fa-lg"></i>';
							                    }

							                    if ( empty( $post_format ) ) {
								                    $carousel .= '<i class="fa fa-picture-o fa-lg"></i>';
							                    }

						                    }

                                            if( $caption_content == 'category_only' || $caption_content == 'category_icon' ) {

                                                $gallery .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';

                                            }


                                            
                                            /* Portfolio Slogan */
                                            $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                                            $carousel .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';
                                            
									    $carousel .= '</div>';	
									$carousel .= '</div>';
								$carousel  .= '</div>';
								
							$carousel  .= '</a>';
							
							$carousel .= '<div>';
                                    
                                    $a_tag = false;								    

									/* link markup for detail slideup */
									if( $ut_portfolio_link_type == 'external' || $ut_portfolio_link_type == 'internal' ) {

                                        /* has external link */
                                        if($external) {

                                            $carousel .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'">';

                                        } else {

                                            $carousel .= '<a class="ut-portfolio-link ' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . esc_attr( self::$token ) . '" data-post="' . esc_attr( $portfolio_query->post->ID ) . '" href="#ut-portfolio-details-wrap-' . esc_attr( self::$token ) . '">';

                                        }

									} else {
                                        
                                        $carousel .= '<a data-trigger="#ut-portfolio-trigger-link-' . self::$token . '-' . esc_attr( $portfolio_query->post->ID ) . '" class="ut-portfolio-trigger-link" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="#">';
                                        
                                    }
								
									$carousel .= '<h3 class="portfolio-title">' . $title . '</h3>';
								
								$carousel  .= '</a>';
								
							$carousel .= '</div>';
							
						$carousel  .= '</li>';
					
					}
					
                }                     
                
            endwhile; endif;
				
        /* reset query */
        wp_reset_postdata();
	    
        /* end showcase navigation */
        $carousel .= '</ul>';
        $carousel .= '</div>';
                
        /* return final carousel */
        return $carousel;

    
    }

    /*
    |--------------------------------------------------------------------------
    | Custom CSS
    |--------------------------------------------------------------------------
    */
    static function create_portfolio_react_carousel_css( $custom_settings = array(), $portfolio_query = array() ) {

        ob_start(); ?>

        <style type="text/css">

            <?php

            // Number Counter Color
            if( !empty( $custom_settings['number_counter_color'] ) ) : ?>

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-react-carousel-number {
                -webkit-text-fill-color: <?php echo $custom_settings['number_counter_color']; ?>;
                text-fill-color: <?php echo $custom_settings['number_counter_color']; ?>;
                color: <?php echo $custom_settings['number_counter_color']; ?>;
            }

            <?php endif; ?>

            <?php

            // Number Counter Stroke Color
            if( $custom_settings['number_counter_stroke'] == 'on' && !empty( $custom_settings['number_counter_stroke_color'] ) ) : ?>

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-react-carousel-number {
                -webkit-text-stroke: 1px <?php echo $custom_settings['number_counter_stroke_color']; ?>;
                text-stroke: 1px <?php echo $custom_settings['number_counter_stroke_color']; ?>;
            }

            <?php endif; ?>

            <?php

            // Title Active Color
            if( !empty( $custom_settings['title_active_color'] ) ) : ?>

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-react-carousel-title.ut-react-carousel-item-center.active .ut-stroke-offset-line {
                fill: <?php echo $custom_settings['title_active_color']; ?>;
            }

            <?php endif; ?>

            <?php

            // Category Color
            if( !empty( $custom_settings['category_color'] ) ) : ?>

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-react-carousel-caption  {
                color: <?php echo $custom_settings['category_color']; ?>;
            }

            <?php endif; ?>

            <?php

            // Shadow Color
            if( !empty( $custom_settings['shadow_color'] ) ) : ?>

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-react-carousel-item-with-shadow .ut-react-carousel-img img  {
                box-shadow: <?php echo $custom_settings['shadow_color']; ?> 0px 20px 20px -10px;
            }

            <?php endif; ?>

            <?php

            // Preloader Color
            if( !empty( $custom_settings['preloader_color'] ) ) : ?>

            #ut-react-carousel-container-<?php echo esc_attr( self::$token ); ?> .ut-react-carousel-preloader {
                color: <?php echo $custom_settings['preloader_color']; ?>
            }

            <?php endif; ?>

            <?php

            // Shadow Color
            if( !empty( $custom_settings['title_shadow_color'] ) ) : ?>

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-text-svg.ut-text-svg-with-shadow {
                -webkit-filter: drop-shadow( 0px 3px 1px <?php echo $custom_settings['title_shadow_color']; ?> );
                filter: drop-shadow( 0px 3px 1px <?php echo $custom_settings['title_shadow_color']; ?> );
            }

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-text-svg.ut-text-svg-with-shadow.ut-text-svg-with-blur {
                -webkit-filter: drop-shadow( 0px 3px 1px <?php echo $custom_settings['title_shadow_color']; ?> ) blur(2px);
                filter: drop-shadow( 0px 3px 1px <?php echo $custom_settings['title_shadow_color']; ?> ) blur(2px);
            }

            #ut-react-carousel-<?php echo esc_attr( self::$token ); ?> .ut-react-carousel-item-center .ut-text-svg.ut-text-svg-with-shadow {
                -webkit-filter: drop-shadow( 0px 3px 1px <?php echo $custom_settings['title_shadow_color']; ?> );
                filter: drop-shadow( 0px 3px 1px <?php echo $custom_settings['title_shadow_color']; ?> );
            }

            <?php endif; ?>


            <?php

            // Navigation Below Arrow Color
            if( !empty( $custom_settings['nav_below_arrow_color'] ) ) : ?>

                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a {
                    color: <?php echo $custom_settings['nav_below_arrow_color']; ?>
                }

            <?php endif;

            if( !empty( $custom_settings['nav_below_arrow_hover_color'] ) ) : ?>

                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a:hover,
                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a:active{
                    color: <?php echo $custom_settings['nav_below_arrow_hover_color']; ?>
                }

            <?php endif; ?>

            <?php

            // Navigation Below Arrow Background Color
            if( !empty( $custom_settings['nav_below_arrow_background_color'] ) ) : ?>

                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a {
                    background: <?php echo $custom_settings['nav_below_arrow_background_color']; ?>
                }

            <?php endif;

            if( !empty( $custom_settings['nav_below_arrow_background_hover_color'] ) ) : ?>

                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a:hover,
                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a:active{
                    background: <?php echo $custom_settings['nav_below_arrow_background_hover_color']; ?>
                }

            <?php endif; ?>

            <?php

            // Navigation Below Arrow Background Color
            if( !empty( $custom_settings['nav_below_arrow_border_color'] ) ) : ?>

                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a {
                    border: 1px solid <?php echo $custom_settings['nav_below_arrow_border_color']; ?>
                }

            <?php endif;

            if( !empty( $custom_settings['nav_below_arrow_border_hover_color'] ) ) : ?>

                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a:hover,
                #ut-react-carousel-<?php echo esc_attr( self::$token ); ?>-navigation a:active{
                    border: 1px solid <?php echo $custom_settings['nav_below_arrow_border_hover_color']; ?>
                }

            <?php endif; ?>

            <?php if( !empty( $portfolio_query ) ) :

                if( $portfolio_query->have_posts() ) : while( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); ?>

                    <?php

                    // Number Color
                    if( !empty( get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_number_color', true ) ) ) : ?>

                    #ut-portfolio-article-<?php echo esc_attr( $portfolio_query->post->ID ); ?> .ut-react-carousel-number {
                        -webkit-text-fill-color: <?php echo get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_number_color', true ); ?>;
                        text-fill-color: <?php echo get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_number_color', true ); ?>;
                        color: <?php echo get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_number_color', true ); ?>
                    }

                    <?php endif; ?>


                    <?php

                    // Number Stroke Color
                    if( $custom_settings['number_counter_stroke'] == 'on' && !empty( get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_number_stroke_color', true ) ) ) : ?>

                    #ut-portfolio-article-<?php echo esc_attr( $portfolio_query->post->ID ); ?> .ut-react-carousel-number {
                        -webkit-text-stroke: 1px <?php echo get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_number_stroke_color', true ); ?>;
                        text-stroke: 1px <?php echo get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_number_stroke_color', true ); ?>;
                    }

                    <?php endif; ?>


                    <?php

                    // Title Active Color
                    if( !empty( self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_active_color', self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_color' ) ) ) ) : ?>

                    #ut-react-carousel-title-<?php echo esc_attr( $portfolio_query->post->ID ); ?>.ut-react-carousel-title.ut-react-carousel-item-center.active .ut-stroke-offset-line {
                        fill: <?php echo self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_active_color', self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_color' ) ); ?> !important;
                    }

                    <?php endif; ?>

                    <?php

                    // Category Color
                    if( !empty( self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_category_color' ) ) ) : ?>

                    #ut-portfolio-article-<?php echo esc_attr( $portfolio_query->post->ID ); ?> .ut-react-carousel-caption  {
                        color: <?php echo self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_category_color' ); ?>;
                    }

                    <?php endif; ?>


                <?php endwhile; endif; ?>

            <?php endif; ?>

        </style>

        <?php

        return self::minify_inline_css( ob_get_clean() );

    }



    /*
    |--------------------------------------------------------------------------
    | React Carousel Font Settings
    |--------------------------------------------------------------------------
    */
    static function get_global_font_settings() {

        if( !function_exists('ot_get_option') || !function_exists('ut_search_sub_array') ) {
            return array();
        }

        $title_settings = array();

        // Front Titles
        if( ot_get_option( 'ut_react_portfolio_title_font_type' ) == 'ut-google' ) {

            $font = array_filter( ot_get_option( 'ut_google_react_portfolio_title_font_style' ) );

            if( !empty( $font['font-family'] ) ) {

                $google_font = ut_search_sub_array( ut_recognized_google_fonts(), $font['font-family'] );
                $font['font-family'] = $google_font['family'];

            }

            $title_settings['title'] = $font;


        } elseif( ot_get_option( 'ut_react_portfolio_title_font_type' ) == 'ut-websafe' ) {

            $title_settings['title'] = array_filter( ot_get_option( 'ut_websafe_react_portfolio_title_font_style' ) );


        } elseif( ot_get_option( 'ut_react_portfolio_title_font_type' ) == 'ut-custom' ) {

            $title_settings['title'] = array_filter( ot_get_option( 'ut_custom_react_portfolio_title_font_style' ) );

        } elseif( ot_get_option( 'ut_react_portfolio_title_font_type' ) == 'ut-font' && is_array( ot_get_option( 'ut_react_portfolio_title_font_style' ) ) ) {

            $title_settings['title'] = array_filter( ot_get_option( 'ut_react_portfolio_title_font_style' ) );

        } elseif( ot_get_option( 'ut_react_portfolio_title_font_type' ) == 'ut-inherit' ) {

            $title_settings['title'] = array_filter( ot_get_option( 'ut_global_react_portfolio_title_font_style' ) );

        }

        // Fallback Values
        $title_settings['title']['font-size'] = !empty( $title_settings['title']['font-size'] ) ? $title_settings['title']['font-size'] : '80px';

        // Back Titles
        if( ot_get_option( 'ut_react_portfolio_background_title_font_type' ) == 'ut-google' ) {

            $font = array_filter( ot_get_option( 'ut_google_react_portfolio_background_title_font_style' ) );

            if( !empty( $font['font-family'] ) ) {

                $google_font = ut_search_sub_array( ut_recognized_google_fonts(), $font['font-family'] );
                $font['font-family'] = $google_font['family'];

            }

            $title_settings['title_background'] = $font;

        } elseif( ot_get_option( 'ut_react_portfolio_background_title_font_type' ) == 'ut-websafe' ) {

            $title_settings['title_background'] = array_filter( ot_get_option( 'ut_websafe_react_portfolio_background_title_font_style' ) );

        } elseif( ot_get_option( 'ut_react_portfolio_background_title_font_type' ) == 'ut-custom' ) {

            $title_settings['title_background'] = array_filter( ot_get_option( 'ut_custom_react_portfolio_background_title_font_style' ) );

        } elseif( ot_get_option( 'ut_react_portfolio_background_title_font_type' ) == 'ut-font' && is_array( ot_get_option( 'ut_react_portfolio_background_title_font_style' ) ) ) {

            $title_settings['title_background'] = array_filter( ot_get_option( 'ut_react_portfolio_background_title_font_style' ) );

        } elseif( ot_get_option( 'ut_react_portfolio_background_title_font_type' ) == 'ut-inherit' ) {

            $title_settings['title_background'] = array_filter( ot_get_option( 'ut_global_react_portfolio_background_title_font_style' ) );

        }

        return $title_settings;

    }

    /*
    |--------------------------------------------------------------------------
    | Portfolio React Carousel
    |--------------------------------------------------------------------------
    */
	static function create_portfolio_react_carousel() {

        // settings
        $portfolio_categories = get_post_meta( self::$token , 'ut_portfolio_categories', true );

        // global portfolio settings
        $portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings', true );

        // gallery options
        $gallery_options = get_post_meta( self::$token , 'ut_react_carousel_options', true );

        // fallback if no meta has been set
        $portfolio_per_page   = !empty( $portfolio_settings['posts_per_page'] ) ? $portfolio_settings['posts_per_page'] : 2;
        $portfolio_categories = !empty( $portfolio_categories ) ? $portfolio_categories : array();

        // portfolio query terms
        $portfolio_terms = array();

        if( !empty($portfolio_categories ) ) {

            foreach( $portfolio_categories as $key => $value) {

                array_push($portfolio_terms , $key);

            }

        }

        // carousel slider settings
        $carousel_attributes = array();
        $carousel_classes = array('ut-react-carousel', 'ut-react-grid');
        $carousel_wrap_classes = array();

        // rotate carousel
        $carousel_attributes['data-rotate'] = $gallery_options['rotate'];

        // autoplay
        $carousel_attributes['data-autoplay'] = $gallery_options['autoplay'];
        $carousel_attributes['data-autoplay-direction'] = 'right';
        $carousel_attributes['data-autoplay-timer'] = !empty( $gallery_options['autoplay_timer'] ) ? $gallery_options['autoplay_timer'] : '3000';

        // navigation below
        $carousel_attributes['data-navigation-below'] = !empty( $gallery_options['navigation'] ) ? $gallery_options['navigation'] : 'off';

        $carousel_attributes = implode(' ', array_map(
            function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
            $carousel_attributes,
            array_keys( $carousel_attributes )
        ) );

        // inner carousel elements
        $display_number_counter = $gallery_options['number_counter'] == 'on' ? '' : 'ut-new-hide';
        $display_categories = $gallery_options['category'] == 'on' ? '' : 'ut-new-hide';


        if( $gallery_options['number_counter'] == 'on' ) {

            $carousel_classes[] = 'ut-react-carousel-with-number-counter';

        }

        if( $gallery_options['category'] == 'on' ) {

            $carousel_classes[] = 'ut-react-carousel-with-categories';

        }

        if( $gallery_options['shadow'] == 'on' ) {

            $carousel_wrap_classes[] = 'ut-react-carousel-negative-margin';
            $carousel_classes[] = 'ut-react-carousel-with-shadows';

        }

        if( isset( $gallery_options['preloader'] ) && $gallery_options['preloader'] == 'on' ) {

            $carousel_wrap_classes[] = 'ut-react-carousel-with-preloader';

        }

        // query args
        $portfolio_args = array(

            'post_type'      => 'portfolio',
            'posts_per_page' => $portfolio_per_page,
            'tax_query'      => array( array(
                'taxonomy' => 'portfolio-category',
                'terms'    => $portfolio_terms,
                'field'    => 'term_id'
            ) )

        );

        // start query
        $portfolio_query = new WP_Query( $portfolio_args );

        ob_start();

        // needed javascript
        echo self::generate_react_carousel_script();

        // needed css
        echo self::create_custom_css();

        // needed css
        echo self::create_portfolio_react_carousel_css( $gallery_options, $portfolio_query );

        // custom fonts
        $react_font_settings = self::get_global_font_settings();

        // this carousel needs 4 items at least
        if( $portfolio_query->post_count < 4 ) {

            $count = ( 4 - $portfolio_query->post_count );

            $current_portfolio = $portfolio_query->posts;

            for( $i = 1; $i <= $count; $i++ ) {

                foreach( $current_portfolio as $single_portfolio ) {

                    $portfolio_query->posts[] = $single_portfolio;
                    $portfolio_query->post_count++;

                }

            }

        } ?>

        <div id="ut-react-carousel-container-<?php echo esc_attr( self::$token ); ?>" class="ut-react-carousel-container <?php echo implode( " ", $carousel_wrap_classes ); ?>">

        <div id="ut-react-carousel-preloader-<?php echo esc_attr( self::$token ); ?>" class="ut-react-carousel-preloader"></div>

        <div id="ut-react-carousel-<?php echo esc_attr( self::$token ); ?>" data-id="<?php echo esc_attr( self::$token ); ?>" class="ut-portfolio-wrap ut-portfolio-<?php echo self::$token; ?> <?php echo implode( " ", $carousel_classes ); ?>" <?php echo $carousel_attributes; ?>>

            <?php

            $num     = 1;
            $numbers = array();

            if( $portfolio_query->have_posts() ) : while( $portfolio_query->have_posts() ) :

            $portfolio_query->the_post();

            // attach number
            $numbers[$portfolio_query->post->ID] = !isset( $numbers[$portfolio_query->post->ID] ) ? sprintf("%02d", $num ) : $numbers[$portfolio_query->post->ID];

            $article_classes = array();

            // Portfolio Image
            $fullsize = $thumbnail = $portfolio_detail_image = wp_get_attachment_url( get_post_thumbnail_id( $portfolio_query->post->ID ) );

            // Portfolio Caption
            $caption = get_post( get_post_thumbnail_id( $portfolio_query->post->ID ) )->post_excerpt;

            // Portfolio Hero Type
            $ut_page_hero_type = get_post_meta($portfolio_query->post->ID , 'ut_page_hero_type' , true);

            // Default Options
            $option_key  = 'ut_page_hero_image';
            $post_format = '';

            /* start ut_page_hero_type */
            if( !empty($ut_page_hero_type) ) {

                // Animated Images do have a different option key
                if( $ut_page_hero_type == 'animatedimage' ) {
                    $option_key = 'ut_page_hero_animated_image';
                }

                // Gallery Format
                if( $ut_page_hero_type == 'slider' || $ut_page_hero_type == 'transition' || $ut_page_hero_type == 'tabs' ) {
                    $post_format = 'gallery';
                }

                // Video Format
                if( $ut_page_hero_type == 'video' ) {
                    $post_format = 'video';
                    $ut_page_video_source = get_post_meta( $portfolio_query->post->ID, 'ut_page_video_source' , true );
                }


            }
            /* end ut_page_hero_type */


            /* new portfolio settings since 4.7.4 */
            $has_media_type = false;
            $one_page_media_type = get_post_meta( $portfolio_query->post->ID , 'ut_onpage_portfolio_media_type' , true);

            if( !empty( $one_page_media_type ) ) {

                if( $one_page_media_type == 'image' ) {
                    $ut_page_hero_type = 'image';
                    $option_key = 'ut_onpage_portfolio_image';
                    $post_format = '';
                }

                if( $one_page_media_type == 'gallery' ) {
                    $ut_page_hero_type = 'gallery';
                    $post_format = 'gallery';
                }

                if( $one_page_media_type == 'video' ) {
                    $ut_page_hero_type = 'video';
                    $post_format = 'video';
                }

                $has_media_type = true;

            }
            /* end new portfolio settings */


            /* check if there is a detail image available */
            $portfolio_detail_image_data = get_post_meta( $portfolio_query->post->ID , $option_key , true );

            if( is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data['background-image']) ) {

                $portfolio_detail_image = $portfolio_detail_image_data['background-image'];

            } elseif( !is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data) ) {

                $portfolio_detail_image = $portfolio_detail_image_data;

            }

            /* cropping dimensions */
            $width  = !empty($carousel_options['crop_size_x']) ? $carousel_options['crop_size_x'] : '1000';
            $height = !empty($carousel_options['crop_size_y']) ? $carousel_options['crop_size_y'] : '1500';

            $featured_image_id = get_post_thumbnail_id( $portfolio_query->post->ID );

            if( get_post_meta( $portfolio_query->post->ID , 'ut_portrait_featured_image' , true ) ) {

                $featured_image_id = get_post_meta( $portfolio_query->post->ID , 'ut_portrait_featured_image' , true );
                $fullsize = wp_get_attachment_url( get_post_meta( $portfolio_query->post->ID , 'ut_portrait_featured_image' , true ) );

            }

            // thumbnail
            // $thumbnail = ut_resize( $fullsize , $width , $height , true , true , true );

            // preview video
            $preview_video = ut_get_portfolio_preview_video( $portfolio_query->post->ID, $post_format, self::$token, true );

            if( $preview_video ) {

                $article_classes[] = 'ut-has-background-video';

                $play_event = get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_video_play_event', true );

                if( $play_event == 'on_load' ) {

                    $article_classes[] = 'ut-video-is-playing';

                }

            }

            // global portfolio settings
            if( $gallery_options['number_counter_stroke'] == 'on' ) {

                $article_classes[] = 'ut-react-carousel-item-with-stroke-number';

            }

            if( $gallery_options['shadow'] == 'on' ) {

                $article_classes[] = 'ut-react-carousel-item-with-shadow';

            }

            // custom cursor
            $data_cursor_skin = '';

            if( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) {

                $data_cursor_skin = 'data-cursor-skin="' . esc_attr( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) . '"';

            }

            $title = str_ireplace( '"', '', trim( get_the_title() ) ); ?>

            <figure id="ut-portfolio-article-<?php echo esc_attr( $portfolio_query->post->ID ); ?>" data-ut-wait="1" data-appear-top-offset="auto" data-id="<?php echo esc_attr( $portfolio_query->post->ID ); ?>" class="ut-react-carousel-item ut-react-carousel-slide <?php echo esc_attr( implode(" ", $article_classes ) ); ?>" <?php echo $data_cursor_skin; ?>>

                <span class="ut-react-carousel-number <?php echo esc_attr( $display_number_counter ); ?>"><?php echo $numbers[$portfolio_query->post->ID]; ?></span>

                    <a href="<?php echo get_permalink( $portfolio_query->post->ID ); ?>" rel="bookmark" title="<?php echo esc_attr( strip_tags( $title ) ); ?>">

                    <div class="ut-react-carousel-img-wrap">

                        <div class="ut-react-carousel-img">

                            <?php echo UT_Adaptive_Image::create( $featured_image_id, array( $width, $height ), true, 'landscape', false, 'skip-lazy ut-portfolio-featured-image' ); ?>

                            <?php /* <img width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" alt="<?php echo esc_attr( strip_tags( $title ) ); ?>" src="<?php echo self::create_placeholder_svg( $width, $height ); ?>" data-src="<?php echo $thumbnail; ?>"> */ ?>

                            <?php echo $preview_video; ?>

                        </div>

                    </div>

                    <?php // Category List
                    $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' ); ?>

                    <figcaption class="ut-react-carousel-caption <?php echo esc_attr( $display_categories ); ?>"><?php echo ut_generate_cat_list( $portfolio_cats ); ?></figcaption>

                </a>

            </figure>

            <?php $num++; endwhile; endif; ?>

            <div class="ut-react-carousel-titles-wrap">

                <div class="ut-react-grid ut-react-carousel-titles">

                <?php if( $portfolio_query->have_posts() ) : while( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); ?>

                    <?php

                    $title_color = self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_color', $gallery_options['title_color'] );
                    $draw_color  = self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_draw_color', $gallery_options['title_line_draw_color'] );
                    $draw_color  = empty( $draw_color ) ? get_option( 'ut_accentcolor', '#F1C40F' ) : $draw_color;

                    $svg_classes = array();

                    // SVG blur
                    if( $gallery_options['blur'] == 'on' ) {
                        $svg_classes[] = 'ut-text-svg-with-blur';
                    }

                    // SVG title
                    if( $gallery_options['title_shadow'] == 'on' ) {
                        $svg_classes[] = 'ut-text-svg-with-shadow';
                    }

                    // SVG Text
                    if( class_exists('UT_Text_SVG') ) :

                    $svg = new UT_Text_SVG( 'ut-text-svg-for-' . $portfolio_query->post->ID, implode( " ", $svg_classes ) );

                    if( !empty( $react_font_settings['title']['font-family'] ) ) {
                        $svg->setFontFamily( $react_font_settings['title']['font-family'] );
                    }

                    if( !empty( $react_font_settings['title']['font-size'] ) ) {
                        $svg->setFontSize( $react_font_settings['title']['font-size'] );
                    }

                    if( !empty( $react_font_settings['title']['font-weight'] ) ) {
                        $svg->setFontWeight( $react_font_settings['title']['font-weight'] );
                    }

                    if( !empty( $react_font_settings['title']['text-transform'] ) ) {
                        $svg->setTextTransform( $react_font_settings['title']['text-transform'] );
                    }

                    if( !empty( $react_font_settings['title']['letter-spacing'] ) ) {
                        $svg->setLetterSpacing( $react_font_settings['title']['letter-spacing'] );
                    }

                    $svg->setTextAlign('left');


                    // Stroke
                    $line_draw_class = '';

                    if( $gallery_options['line_draw'] == 'on' ) {

                        $svg->setStroke(true);
                        $svg->setStrokeWidth(0);
                        $svg->setStrokeColor( $draw_color );

                        if( $gallery_options['line_draw_width'] == 'thicker' ) {

                            $line_draw_class = 'ut-react-carousel-item-with-title-line-draw ut-stroke-offset-line-thicker';

                        } else {

                            $line_draw_class = 'ut-react-carousel-item-with-title-line-draw';

                        }

                    }

                    // Fill
                    $svg->setFill( $title_color );

                    // Alternate Title
                    $svg->addText(  strip_tags( get_the_title() ) ); ?>

                    <div id="ut-react-carousel-title-<?php echo esc_attr( $portfolio_query->post->ID ); ?>" class="ut-react-carousel-item ut-react-carousel-title <?php echo $line_draw_class; ?>">
                        <?php echo $svg->dynamicCSS(); ?>
                        <?php echo $svg->asXML(); ?>
                    </div>

                    <?php endif; ?>

                <?php endwhile; endif; ?>

                </div>

            </div>

            <?php if( $gallery_options['background_titles'] == 'on' ) : ?>

            <div class="ut-react-carousel-titles-background-wrap">

                <div class="ut-react-grid ut-react-carousel-background-titles">

                    <?php if( $portfolio_query->have_posts() ) : while( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); ?>

                        <?php

                        // default colors
                        $title_color = $gallery_options['title_background_color'];
                        $draw_color  = $gallery_options['title_background_line_draw_color'];

                        // custom colors
                        $title_color = self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_background_color', $title_color );
                        $draw_color =  self::get_portfolio_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_react_title_background_draw_color', $draw_color );

                        // custom classes
                        $svg_classes = array();

                        // SVG Text
                        if( class_exists('UT_Text_SVG') ) :

                            $svg = new UT_Text_SVG( 'ut-text-svg-for-' . $portfolio_query->post->ID, implode( " ", $svg_classes ) );

                            if( !empty( $react_font_settings['title_background']['font-family'] ) ) {
                                $svg->setFontFamily( $react_font_settings['title_background']['font-family'] );
                            }

                            if( !empty( $react_font_settings['title_background']['font-weight'] ) ) {
                                $svg->setFontWeight( $react_font_settings['title_background']['font-weight'] );
                            }

                            if( !empty( $react_font_settings['title_background']['text-transform'] ) ) {
                                $svg->setTextTransform( $react_font_settings['title_background']['text-transform'] );
                            }

                            if( !empty( $react_font_settings['title_background']['letter-spacing'] ) ) {
                                $svg->setLetterSpacing( $react_font_settings['title_background']['letter-spacing'] );
                            }

                            $svg->setFontSize('80');

                            // Stroke
                            $svg->setStroke(true);
                            $svg->setStrokeWidth(0);
                            $svg->setStrokeColor( $draw_color );

                            // Fill
                            $svg->setFill( $title_color );
                            $svg->addText(  strip_tags( get_the_title() ) ); ?>

                            <div class="ut-react-carousel-item ut-react-carousel-background-title">
                                <?php echo $svg->dynamicCSS(); ?>
                                <?php echo $svg->asXML(); ?>
                            </div>

                        <?php endif; ?>

                    <?php endwhile; endif; ?>

                </div>

            </div>

            <?php endif; ?>

            <div class="ut-react-grid ut-react-carousel-interaction">
                <div class="ut-react-carousel-item ut-react-carousel-item-cursor ut-react-carousel-item-left" data-custom-cursor="arrow-left"></div>
                <div class="ut-react-carousel-item ut-react-carousel-item-cursor ut-react-carousel-item-center" data-custom-cursor="image"></div>
                <div class="ut-react-carousel-item ut-react-carousel-item-cursor ut-react-carousel-item-right" data-custom-cursor="arrow-right"></div>
            </div>

        </div>

        <?php if( $gallery_options['navigation'] == 'on' ) : ?>

            <div id="ut-react-carousel-<?php echo self::$token; ?>-navigation" class="ut-react-carousel-navigation">

                <a class="ut-react-carousel-button ut-react-carousel-button-prev" href="#"></a>
                <a class="ut-react-carousel-button ut-react-carousel-button-next" href="#"></a>

            </div>

        <?php endif; ?>

        </div>

        <?php

        // reset query
        wp_reset_postdata();

        $carousel = ob_get_clean();

        /* return final carousel */
        return $carousel;

    }

    static function generate_react_carousel_script( $carousel_options = array() ) {

        ob_start(); ?>

        <script type="text/javascript">

            (function($){



            })(jQuery);

        </script>

        <?php

        /* return javascript */
        return ut_compress_java( ob_get_clean() );


    }


    /*
    |--------------------------------------------------------------------------
    | Grid / Masonry Gallery
    |--------------------------------------------------------------------------
    */
	static function create_masonry_gallery() {
		
		global $paged;
		
		// settings
		$portfolio_categories = get_post_meta( self::$token , 'ut_portfolio_categories' );
		
		// global portfolio settings
        $portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings' );
        $portfolio_settings = $portfolio_settings[0];
				
        // masonry options
        $masonry_options = get_post_meta( self::$token , 'ut_masonry_options', true );
        
		// fallback if no meta has been set
		$portfolio_per_page   = !empty($portfolio_settings['posts_per_page']) ? $portfolio_settings['posts_per_page'] : 4 ;
		$portfolio_categories = !empty($portfolio_categories) ? $portfolio_categories : array();
		
		// effect settings
		$effect = !empty( $masonry_options['animation_in'] ) ? $masonry_options['animation_in'] : 'portfolioFadeIn';
		
		// portfolio query terms
		$portfolio_terms = array();
		if( !empty($portfolio_categories[0]) && is_array($portfolio_categories[0]) ) {

			foreach( $portfolio_categories[0] as $key => $value) {                
                array_push($portfolio_terms , $key);
			}
			
		}
		
        /* query args */
        $portfolio_args = array(
            
            'post_type'      => 'portfolio',
            'posts_per_page' => $portfolio_per_page,
            'paged'          => $paged,
            'tax_query'      => array( array(
                    'taxonomy' => 'portfolio-category',
                    'terms'    => $portfolio_terms,
                    'field'    => 'term_id'  
            ) )
            
        
        );
        
        /* start query */
        $portfolio_query = new WP_Query( $portfolio_args );		
		
		/* needed javascript vars */
		$columns  = !empty($masonry_options['columns']) ? $masonry_options['columns'] : 5;
		$tcolumns = !empty($masonry_options['tcolumns']) ? $masonry_options['tcolumns'] : 3;
		$mcolumns = !empty($masonry_options['mcolumns']) ? $masonry_options['mcolumns'] : 2;
		$height   = !empty($masonry_options['crop_size_y']) ? $masonry_options['crop_size_y'] : '400';

        /* needed css */
        $masonry = self::create_custom_css();

        /* needed css */
        $masonry .= self::create_single_custom_css( $portfolio_args );
        
		/* create hidden gallery */
		$masonry .= self::create_hidden_popup_portfolio( $portfolio_args , $portfolio_settings['image_style'] );
		
        if( function_exists('ot_get_option') ) {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : ot_get_option('ut_global_portfolio_title_color','#FFF');
            
        } else {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : '#FFF';
            
        }
        
        /* portfolio wrapper */		
		$masonry .= '<div id="ut-masonry-' . self::$token . '" class="ut-masonry-grid ut-portfolio-wrap ut-portfolio-' . self::$token . '" ' . self::$slide_up_title . ' ' . self::$slide_up_width . ' data-textcolor="' . esc_attr( $textcolor ) .'" data-opacity="' . $portfolio_settings["hover_opacity"] . '" data-hovercolor="' . ut_hex_to_rgb($portfolio_settings["hover_color"]) . '" data-columns="' . $columns . '" data-tcolumns="' . $tcolumns . '" data-mcolumns="' . $mcolumns . '" data-column-height="' . $height . '">';
			
			/* loop trough portfolio items */
			if ($portfolio_query->have_posts()) : while ($portfolio_query->have_posts()) : $portfolio_query->the_post();

                $article_classes = array();

				$fullsize = $thumbnail = $portfolio_detail_image = wp_get_attachment_url( get_post_thumbnail_id( $portfolio_query->post->ID ) );
				$caption = get_post( get_post_thumbnail_id( $portfolio_query->post->ID ) )->post_excerpt;
				
				/* image dimensions */
				$imageinfo = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'full' );
				$width  = $imageinfo[1];
				$height = $imageinfo[2];
				
                /* hero type setting - new with 2.6 */
                $ut_page_hero_type = get_post_meta($portfolio_query->post->ID , 'ut_page_hero_type' , true);
                
                $post_format = '';
                $optionkey   = 'ut_page_hero_image';
                
                if( !empty($ut_page_hero_type) ) {
                    
                    if($ut_page_hero_type == 'image' || $ut_page_hero_type == 'animatedimage' || $ut_page_hero_type == 'splithero' || $ut_page_hero_type == 'custom' || $ut_page_hero_type == 'dynamic') {
                        $post_format = '';
                    }
                    
                    if($ut_page_hero_type == 'slider' || $ut_page_hero_type == 'transition' || $ut_page_hero_type == 'tabs') {
                        $post_format = 'gallery';
                    }
                    
                    if($ut_page_hero_type == 'video') {
                        $post_format = 'video';
                        $ut_page_video_source = get_post_meta($portfolio_query->post->ID, 'ut_page_video_source' , true);  
                    }
                    
                    /* switch option key */
                    switch ( $ut_page_hero_type ) {
                    
                        case 'animatedimage':
                        $optionkey = 'ut_page_hero_animated_image';
                        break;
                        
                        default:  
                        $optionkey = 'ut_page_hero_image';  
                    
                    }                  
                
                } /* end ut_page_hero_type */
               
		
		
				/* new portfolio settings since 4.7.4 */
				$has_media_type = false;
				$onpage_media_type = get_post_meta( $portfolio_query->post->ID , 'ut_onpage_portfolio_media_type' , true);
		
				if( !empty( $onpage_media_type ) ) {
					
					if( $onpage_media_type == 'image' ) {
						$ut_page_hero_type = 'image';
						$optionkey = 'ut_onpage_portfolio_image';
						$post_format = '';	
					}
					
					if( $onpage_media_type == 'gallery' ) {
						$ut_page_hero_type = 'gallery';
						$post_format = 'gallery';	
					}
					
					if( $onpage_media_type == 'video' ) {
						$ut_page_hero_type = 'video';
						$post_format = 'video';	
					}
					
					$has_media_type = true;
					
				}
				/* end new portfolio settings */		
		
		
                                    			
                /* check if there is a detail image available */
                $portfolio_detail_image_data = get_post_meta( $portfolio_query->post->ID , $optionkey , true );                
                
                if( is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data['background-image']) ) {
                        
                    $portfolio_detail_image = $portfolio_detail_image_data['background-image'];
                    
                } elseif( !is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data) ) {
                    
                    $portfolio_detail_image = $portfolio_detail_image_data;
                    
                }               
                	
				/* cropping dimensions */
				$width  = !empty($masonry_options['crop_size_x']) ? $masonry_options['crop_size_x'] : '1000';
				$height = !empty($masonry_options['crop_size_y']) ? $masonry_options['crop_size_y'] : '800';
				// $thumbnail = ut_resize( $fullsize , $width , $height , true , true , true );
				
				/* link settings */
                $ut_portfolio_link_type = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true);
                $ut_portfolio_link_type = empty( $ut_portfolio_link_type ) || !empty( $ut_portfolio_link_type ) && $ut_portfolio_link_type == 'global' ? self::$detailstyle : $ut_portfolio_link_type;
                $external = false;
                $target = '_blank';
                
                if( $ut_portfolio_link_type == 'external' ) {
                   
                   /* get link user haas to set */ 
                   $external = get_post_meta( $portfolio_query->post->ID , 'ut_external_link' , true);

	                $external_target = get_post_meta( $portfolio_query->post->ID , 'ut_external_link_target' , true);
	                $target = !empty( $external_target ) ? $external_target : $target;
                    
                } elseif( $ut_portfolio_link_type == 'internal' ) {
                   
                   /* get permalink to single portfolio */
                   $external = get_permalink( $portfolio_query->post->ID );
                   $target = '_self';
                   
                }
                
                $a_tag = false;

                // preview video
                $preview_video = ut_get_portfolio_preview_video( $portfolio_query->post->ID, $post_format, self::$token );

                if( $preview_video ) {

                    $article_classes[] = 'ut-has-background-video';

                    $play_event = get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_video_play_event', true );

                    if( $play_event == 'on_load' ) {

                        $article_classes[] = 'ut-video-is-playing';

                    }

                }

				// custom cursor
				$data_cursor_skin = '';

				if( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) {

					$data_cursor_skin = 'data-cursor-skin="' . esc_attr( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) . '"';

				}

				/* create single content item */				
				$masonry .= '<div id="ut-portfolio-article-' . $portfolio_query->post->ID . '" class="ut-portfolio-article ut-masonry show ut-hover ' . implode(" ", $article_classes ) . '">';
					
					$masonry .= '<div class="ut-portfolio-article-animation-box">';					
		
						$masonry .= '<div data-effect="' . esc_attr( $effect ) . '" class="ut-portfolio-item">';

                            $masonry .= $preview_video;

							$title= str_ireplace('"', '', trim(get_the_title()));

							/* link markup for detail slideup */
							if( $ut_portfolio_link_type == 'slideup' || $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'onepage' || $ut_portfolio_link_type == 'external' ) {

								/* has external link */
								if( $external ) {

									$masonry .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

								} else {

									$masonry .= '<a class="ut-portfolio-link ' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . self::$token . '" data-post="' . $portfolio_query->post->ID . '" href="#" ' . $data_cursor_skin . '>';

								}

								$a_tag = true;							


							}

							/* link markup for image popup */
							if( $ut_portfolio_link_type == 'popup' && !$a_tag ) {

								$popuplink = NULL;

								$mini = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-mini' );
								$mini = !empty( $mini[0] ) ? $mini[0] : "";

                                $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                                $portfolio_cats = ut_generate_cat_list( $portfolio_cats );

								if( $has_media_type ) {

                                    $link_attributes = ut_get_morphbox_meta( ut_portfolio_get_image_id( $portfolio_detail_image ), $portfolio_query->post->ID, '', $portfolio_cats );
                                    $lightgallery = ut_get_morphbox_fullscreen( ut_portfolio_get_image_id( $portfolio_detail_image ) , 'ut-lightbox');

								} else {

                                    $link_attributes = ut_get_morphbox_meta( get_post_thumbnail_id( $portfolio_query->post->ID ), $portfolio_query->post->ID, '', $portfolio_cats );
								    $lightgallery = ut_get_morphbox_fullscreen( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-lightbox' );

								}

                                /* attributes string */
                                $link_attributes = implode(' ', array_map(
                                    function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                                    $link_attributes,
                                    array_keys( $link_attributes )
                                ) );

								$lightgallery = !empty( $lightgallery[0] ) ? $lightgallery[0] : $portfolio_detail_image;

								/* image post or audio post */
								if( empty( $post_format ) || $post_format == 'audio' ) {

									/* has external link */
									if( $external ) {

										$masonry .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

									} else {

										$masonry .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
										$masonry .= '<a data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" class="' . $portfolio_settings['image_style'] . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="'. esc_url( $lightgallery) .'" ' . $link_attributes . ' ' . $data_cursor_skin . '>';

									}

								}

								/* video post */
								if( $post_format == 'video' ) {

									/* has external link */
									if( $external ) {

										$masonry .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

									} else {

										// try to get video media
										$video_media = ut_get_portfolio_format_video_media( $portfolio_query->post->ID );

										if( !$video_media )
										$video_media = get_the_content();

										// check for possible Iframe
										$video_data = '';

										if( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['iframe'] ) ) {

											$video_data = 'data-iframe="true"';
											$popuplink = ut_get_portfolio_format_video_content( $video_media['iframe'] );

										} elseif( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['selfhosted'] ) ) {

											$masonry .= $video_media['selfhosted'];
											$video_data = 'data-html="#ut-selfhosted-lightbox-player-' . $portfolio_query->post->ID . '"';

										} else {

											// try to catch video url
											$popuplink = ut_get_portfolio_format_video_content( $video_media );

										}                                  

										$masonry .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
										$masonry .= '<a data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" ' . $video_data . ' class="' . $portfolio_settings['image_style'] . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="'.$popuplink.'" ' . $data_cursor_skin . '>';

									}

								}

								/* gallery post */
								if( $post_format == 'gallery' ) {

									/* has external link */
									if($external) {

										$masonry .= '<a class="' . $portfolio_settings['image_style'] . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

									} else {

										$masonry .= ut_portfolio_lightgallery( $portfolio_query->post->ID , self::$token , $ut_page_hero_type );
										$masonry .= '<a data-exThumbImage="' . $mini . '" class="ut-portfolio-popup-'.$portfolio_query->post->ID.' '. $portfolio_settings['image_style'] . '" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . esc_url( $lightgallery ) . '" ' . $data_cursor_skin . '>';

									}

								}								

							}

								$masonry .= '<figure>';

				                    $masonry .= UT_Adaptive_Image::create( get_post_thumbnail_id( $portfolio_query->post->ID ), array( $width, $height ), true, 'landscape', false, 'skip-lazy ut-portfolio-featured-image' );
									// $masonry .= '<img alt="' . esc_attr( get_the_title() ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" src="' . self::create_placeholder_svg( $width, $height ) . '" class="lozad skip-lazy ut-portfolio-featured-image" data-src="' . esc_url( $thumbnail )  . '" />';

								$masonry .= '</figure>';

								$masonry .= '<div class="ut-hover-layer">';

									$masonry .= '<div class="ut-portfolio-info">';

										$masonry .= '<div class="ut-portfolio-info-c">';

											/* Portfolio Title and Categories */
											$masonry .= '<h3 class="portfolio-title">' . get_the_title() . '</h3>';								

											/* get all portfolio-categories for this item */
											$portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
											$masonry .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';

										$masonry .= '</div>';	

									$masonry .= '</div>';

								$masonry .= '</div>';

							$masonry .= '</a>';

						$masonry .= '</div>';
					
					$masonry .= '</div>';
		
				$masonry .= '</div>';
				
			endwhile; endif;
		
		$masonry .= '</div>';
				
		/* reset query */
		wp_reset_postdata();
		
		return  $masonry;
		
	}

    /*
    |--------------------------------------------------------------------------
    | Portfolio Filterable Gallery
    |--------------------------------------------------------------------------
    */
    static function create_portfolio_gallery() {
		
		global $paged, $wp_query;

        /* pagination */		
        if  ( empty($paged) ) {
                
                if ( !empty( $_GET['paged'] ) ) {
                        $paged = $_GET['paged'];
                } elseif ( !empty($wp->matched_query) && $args = wp_parse_args($wp->matched_query) ) {
                        if ( !empty( $args['paged'] ) ) {
                                $paged = $args['paged'];
                        }
                }
                if ( !empty($paged) ) {
                    $wp_query->set('paged', $paged);
                }
                
        }           
         
		// settings
		$portfolio_categories = get_post_meta( self::$token , 'ut_portfolio_categories' );
		$term = '';
		
		// global portfolio settings
        $portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings', true );
				
        // gallery options
        $gallery_options = get_post_meta( self::$token , 'ut_gallery_options', true );

		// gutter settings
        $container_classes = array();

        $gutter         		= !empty( $gallery_options['gutter'] ) ? 'gutter' : '';
        $gutter_size    		= !empty( $gallery_options['gutter_size'] ) && $gutter == 'gutter' ? $gallery_options['gutter_size'] : '1';
        $gutter_shadow  		= !empty( $gallery_options['gutter_shadow'] ) ? 'gutter-shadow' : '';
        $gutter_class_item      = !empty( $gutter ) ? 'gutter-' . $gutter_size : '' ;

        $container_classes[]    = !empty( $gutter ) ? 'has-gutter guttersize-' . $gutter_size : '' ;
        $container_classes[]    = !empty( $portfolio_settings['background_color'] ) ? 'ut-portfolio-item-container-with-background' : '' ;

		// effect settings
		$effect = !empty( $gallery_options['animation_in'] ) ? $gallery_options['animation_in'] : 'portfolioFadeIn';
		
		// image style
		$image_style_class = $portfolio_settings['image_style'];
		
		// fallback if no meta has been set
		$portfolio_per_page   = !empty($portfolio_settings['posts_per_page']) ? $portfolio_settings['posts_per_page'] : 4 ;
		$portfolio_categories = !empty($portfolio_categories) ? $portfolio_categories : array();
		
		/* portfolio query terms */
		$portfolio_terms = array();
        $portfolio_terms_query = '';
        
		if( !empty( $portfolio_categories[0] ) && is_array($portfolio_categories[0]) ) {

			foreach( $portfolio_categories[0] as $key => $value) {                
                array_push($portfolio_terms , $key);
			}
			
			$portfolio_terms_query = $portfolio_terms;
			
		}
		
		
		/* only change term if browser is browsing this portfolio */
		if( ( !empty( $_GET['portfolioID'] ) && $_GET['portfolioID'] == self::$token ) ) :
		
			if( $gallery_options['filter_type'] == 'static' && !empty($_GET['termID']) ) {
				
				/* sanitize term */
				$term = absint( $_GET['termID'] );
				
				/* reset terms */
				$portfolio_terms_query = array();
				array_push($portfolio_terms_query , $term);
			
			}
			
		endif;
		
		
		/* user is not browsing this portfolio , so we reset the pagination */
		if( ( !empty( $_GET['portfolioID'] ) && $_GET['portfolioID'] != self::$token ) ) :
			
			$paged = 0;
		
		endif;
		
				
        /* query args */
        $portfolio_args = array(
            
            'post_type'      => 'portfolio',
            'posts_per_page' => $portfolio_per_page,
            'paged'          => $paged,
            'tax_query'      => array( array(
                    'taxonomy' => 'portfolio-category',
                    'terms'    => $portfolio_terms_query,
                    'field'    => 'term_id'  
            ) )
            
        
        );
        
        /* start query */
        $portfolio_query = new WP_Query( $portfolio_args );
        
        /* portfolio filter */                
        if( !empty($gallery_options['filter']) ) :
        		
        $filter  = '<div class="ut-portfolio-menu-wrap ut-portfolio-menu-wrap-' . ( !empty( $gallery_options['filter_align'] ) ? $gallery_options['filter_align'] : '' ) . '" ' . ( !empty( $gallery_options['filter_align'] ) ? 'style="text-align:' . $gallery_options['filter_align'] .';"' : '' ) . '><ul id="ut-portfolio-menu-' . self::$token . '" class="ut-portfolio-menu ' . ( !empty( $gallery_options['filter_style'] ) ? $gallery_options['filter_style'] : 'style_one' ) . ' ' . ( !empty( $gallery_options['filter_spacing'] ) ? 'ut-portfolio-menu-' . $gallery_options['filter_spacing'] : '' ) . '">';
            
            /* reset button */
			$reset = !empty($gallery_options['reset_text']) ? $gallery_options['reset_text'] : __('All' , 'ut_portfolio_lang');
			
            if( $gallery_options['filter_type'] == 'static' ) {
				
				$selected = (empty($term)) ? 'class="selected"' : '';
				$filter .= '<li><a href="?portfolioID='.self::$token.'#ut-portfolio-items-' . self::$token . '-anchor" data-filter="*" ' . $selected . '>' . $reset . '</a></li>';
			
			} else {
			
				$filter .= '<li><a href="#" data-filter="*" class="selected">' . $reset . '</a></li>';
				
			}
            
            /* get taxonomies */
            $taxonomies = get_terms('portfolio-category');
			$taxonomiesarray =  json_decode(json_encode($taxonomies), true);
					    
            if( is_array($portfolio_terms) && is_array($taxonomies) ) {
                
                foreach ($portfolio_terms as $single_term ) {
										
                    $key = self::search_tax_key( $single_term , $taxonomiesarray );
										                    
					if( $gallery_options['filter_type'] == 'static' && isset($key) ) {
						
						$selected = ( $taxonomies[$key]->term_id == $term ) ? 'class="selected"' : '';
						
						$filter .= '<li><a ' . $selected . ' href="?termID=' . $taxonomies[$key]->term_id . '&amp;portfolioID='.self::$token.'#ut-portfolio-items-' . self::$token . '-anchor" data-filter=".'.$taxonomies[$key]->slug.'-filt">'.$taxonomies[$key]->name.'</a></li>';
						
					} else {
						
                        if( isset($taxonomies[$key]->slug) ) {
						    $filter .= '<li><a href="#" data-filter=".'.$taxonomies[$key]->slug.'-filt">'.$taxonomies[$key]->name.'</a></li>';
                        }
                        
					}
					
					
                }
            
			}      

            /*
             * @todo later website demo
             * if( !empty( $gallery_options['column_switch'] )  ) {

                $columns = isset( $gallery_options['columns'] ) ? $gallery_options['columns'] : 4;
                $columns_alternate = isset( $gallery_options['columns_alternate'] ) ? $gallery_options['columns_alternate'] : $columns - 1;

                $filter .= '<li class="ut-portfolio-layout-change"><a data-default-columns="' . $columns . '" data-alternate-columns="' . $columns_alternate . '" href="#" ><i class="fa fa-columns" aria-hidden="true"></i></a></li>';

            }*/

        $filter .= '</ul></div>';
        
        endif;
        
        /* needed javascript */
		$gallery = self::generate_gallery_script( $gallery_options );
        
        /* needed css */
        $gallery .= self::create_custom_css( $gallery_options );

        /* needed css */
        $gallery .= self::create_single_custom_css( $portfolio_args );
        
		/* create hidden gallery */
		$gallery .= self::create_hidden_popup_portfolio( $portfolio_args , $portfolio_settings['image_style'] );
		
        if( function_exists('ot_get_option') ) {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : ot_get_option('ut_global_portfolio_title_color','#FFF');
            
        } else {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : '#FFF';
            
        }
        
        /* output portfolio wrap */
        $gallery .= '<div id="ut-portfolio-wrap" class="ut-portfolio-wrap ut-portfolio-' . self::$token . '" ' . self::$slide_up_title . ' ' . self::$slide_up_width . ' data-textcolor="' . esc_attr( $textcolor ) .'" data-opacity="' . $portfolio_settings["hover_opacity"] . '" data-hovercolor="' . ut_hex_to_rgb($portfolio_settings["hover_color"]) . '">';
		$gallery .= '<a class="ut-portfolio-offset-anchor" style="top:-120px;" id="ut-portfolio-items-' . self::$token . '-anchor"></a>';
		
        /* add filter */
        if( isset($gallery_options['filter']) ) {
            $gallery .= $filter;
        }
        
        /* output portfolio container */
        $gallery .= '<div id="ut-portfolio-items-' . self::$token . '" class="ut-portfolio-item-container ' . implode(" ", $container_classes ) . '">';
        	
            /* loop trough portfolio items */
			if( $portfolio_query->have_posts() ) : while( $portfolio_query->have_posts() ) : $portfolio_query->the_post();

                // classes on article
                $article_classes = array( $gutter, $gutter_class_item );

                /* needed variables */
                $title = str_ireplace('"', '', trim(get_the_title()));
                $post_format = get_post_format();
				
                /* link settings */
                $ut_portfolio_link_type = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true);
                $ut_portfolio_link_type = empty( $ut_portfolio_link_type ) || !empty( $ut_portfolio_link_type ) && $ut_portfolio_link_type == 'global' ? self::$detailstyle : $ut_portfolio_link_type;
        
                $external = false;
                $target = '_blank';
                
                if( $ut_portfolio_link_type == 'external' ) {
                   
                   /* get link user has to set */ 
                   $external = get_post_meta( $portfolio_query->post->ID , 'ut_external_link' , true);

                   $external_target = get_post_meta( $portfolio_query->post->ID , 'ut_external_link_target' , true);
	               $target = !empty( $external_target ) ? $external_target : $target;

                } elseif( $ut_portfolio_link_type == 'internal' ) {
                   
                   /* get permalink to single portfolio */
                   $external = get_permalink( $portfolio_query->post->ID );
                   $target = '_self';
                   
                }
        
                /* get all portfolio-categories for this item ( needed for filter ) */
                $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                
                /* set filter attributes */               
                if( is_array($portfolio_cats) ) {

                    foreach($portfolio_cats as $single_cat) {

                        $article_classes[] =  $single_cat->slug."-filt ";

                    }

                }
                
				/* gallery style */
				$gallery_style = !empty($gallery_options['style']) ? $gallery_options['style'] : 'style_one';

				if( $gallery_style == 'style_three' ) {

                    $article_classes[] = 'ut-portfolio-article-tilt';

                }


				/* portfolio featured image */
				$fullsize = $thumbnail = $portfolio_detail_image = wp_get_attachment_url( get_post_thumbnail_id($portfolio_query->post->ID) );
				$caption = get_post( get_post_thumbnail_id( $portfolio_query->post->ID ) )->post_excerpt;
				
                /* hero type setting - new with 2.6 */
                $ut_page_hero_type = get_post_meta($portfolio_query->post->ID , 'ut_page_hero_type' , true);
                $optionkey = 'ut_page_hero_image';
                
                if( !empty($ut_page_hero_type) ) {
                    
                    if($ut_page_hero_type == 'image' || $ut_page_hero_type == 'animatedimage' || $ut_page_hero_type == 'splithero' || $ut_page_hero_type == 'custom' || $ut_page_hero_type == 'dynamic') {
                        $post_format = '';
                    }
                    
                    if($ut_page_hero_type == 'slider' || $ut_page_hero_type == 'transition' || $ut_page_hero_type == 'tabs') {
                        $post_format = 'gallery';
                    }
                    
                    if($ut_page_hero_type == 'video') {
                        $post_format = 'video';
                        $ut_page_video_source = get_post_meta($portfolio_query->post->ID, 'ut_page_video_source' , true);
                    }
                    
                    /* switch option key */
                    switch( $ut_page_hero_type ) {
                    
                        case 'animatedimage':
                        $optionkey = 'ut_page_hero_animated_image';
                        break;
                        
                        default:  
                        $optionkey = 'ut_page_hero_image';  
                    
                    }                  
                
                } /* end ut_page_hero_type */
                
		
				/* new portfolio settings since 4.7.4 */
				$has_media_type = false;
				$onpage_media_type = get_post_meta( $portfolio_query->post->ID , 'ut_onpage_portfolio_media_type' , true);
		
				if( !empty( $onpage_media_type ) ) {
					
					if( $onpage_media_type == 'image' ) {
						$ut_page_hero_type = 'image';
						$optionkey = 'ut_onpage_portfolio_image';
						$post_format = '';	
					}
					
					if( $onpage_media_type == 'gallery' ) {
						$ut_page_hero_type = 'gallery';
						$post_format = 'gallery';	
					}
					
					if( $onpage_media_type == 'video' ) {
						$ut_page_hero_type = 'video';
						$post_format = 'video';	
					}
					
					$has_media_type = true;
					
				}
				/* end new portfolio settings */
			
                /* check if there is a detail image available */
                $portfolio_detail_image_data = get_post_meta( $portfolio_query->post->ID , $optionkey , true );                
                
                if( is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data['background-image']) ) {
                        
                    $portfolio_detail_image = $portfolio_detail_image_data['background-image'];
                    
                } elseif( !is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data) ) {
                    
                    $portfolio_detail_image = $portfolio_detail_image_data;
                    
                }
			
				/* cropping dimensions */
				$width  = !empty($gallery_options['crop_size_x']) ? $gallery_options['crop_size_x'] : '1000';
				$height = !empty($gallery_options['crop_size_y']) ? $gallery_options['crop_size_y'] : '800';

                // check if hardcropping is activated
                $hardcrop = !empty( $gallery_options['hardcrop'] ) ? false : true;
				// $thumbnail  =  ut_resize( $fullsize , $width , $height , $hardcrop , true , true );

				$a_tag = false;

                // preview video
                $preview_video = ut_get_portfolio_preview_video( $portfolio_query->post->ID, $post_format, self::$token );

                if( $preview_video ) {

                    $article_classes[] = 'ut-has-background-video';

                    $play_event = get_post_meta( $portfolio_query->post->ID, 'ut_portfolio_showcase_video_play_event', true );

                    if( $play_event == 'on_load' ) {

                        $article_classes[] = 'ut-video-is-playing';

                    }

                }

				// hover visibility
				if( isset( $gallery_options['caption_visibility'] ) && $gallery_options['caption_visibility'] == 'permanent' ) {
					$article_classes[] = 'ut-portfolio-article-permanent-caption';
				}

				// custom cursor
				$data_cursor_skin = '';

				if( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) {

					$data_cursor_skin = 'data-cursor-skin="' . esc_attr( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) . '"';

				}

				/* style one images with title */        
				if( $gallery_style == 'style_one' || $gallery_style == 'style_three' ) {
				    
					/* create single portfolio item */
					$gallery .= '<article id="ut-portfolio-article-' . $portfolio_query->post->ID . '" class="ut-portfolio-article ut-masonry portfolio-style-one ' . implode( " ", $article_classes ) . '">';
					
						$gallery .= '<div class="ut-portfolio-article-animation-box">';

							$gallery .= '<div data-effect="' . esc_attr( $effect ) . '" class="ut-portfolio-item ut-hover">';

								/* link markup for slideup details */
								if( $ut_portfolio_link_type == 'onepage' || $ut_portfolio_link_type == 'slideup' || $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'external' ) {							

									/* has external link */
									if( !empty( $external ) ) {

										$gallery .= '<a class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . $external . '" target="'.$target.'" ' . $data_cursor_skin . '>';

									} else {

										$gallery .= '<a class="ut-portfolio-link ' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . self::$token . '" data-post="' . $portfolio_query->post->ID . '" href="#ut-portfolio-details-wrap-' . self::$token . '" ' . $data_cursor_skin . '>';

									}

									$a_tag = true;


								}

								/* link markup for image popup */
								if( $ut_portfolio_link_type == 'popup' && !$a_tag ) {

									$popuplink = NULL;

									$mini = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-mini' );
									$mini = !empty( $mini[0] ) ? $mini[0] : "";

                                    $_portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
									$_portfolio_cats = ut_generate_cat_list( $_portfolio_cats );

									if( $has_media_type ) {

                                        $link_attributes = ut_get_morphbox_meta( ut_portfolio_get_image_id( $portfolio_detail_image ), $portfolio_query->post->ID, '', $_portfolio_cats );
                                        $lightgallery = ut_get_morphbox_fullscreen( ut_portfolio_get_image_id( $portfolio_detail_image ) , 'ut-lightbox');

									} else {

                                        $link_attributes = ut_get_morphbox_meta( get_post_thumbnail_id( $portfolio_query->post->ID ), $portfolio_query->post->ID, '', $_portfolio_cats );
									    $lightgallery = ut_get_morphbox_fullscreen( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-lightbox' );

									}

                                    /* attributes string */
                                    $link_attributes = implode(' ', array_map(
                                        function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                                        $link_attributes,
                                        array_keys( $link_attributes )
                                    ) );

									$lightgallery = !empty( $lightgallery[0] ) ? $lightgallery[0] : $portfolio_detail_image;

									/* image post or audio post */
									if( empty( $post_format ) || $post_format == 'audio' ) {

										/* has external link */ 
										if( !empty( $external ) ) {

											$gallery .= '<a class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . $external . '" target="' . $target . '" ' . $data_cursor_skin . '>';

										} else {

											if( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true ) == 'popup' ) {
												$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											}

											$gallery .= '<a data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" class="' . $image_style_class . ' ' . $gutter_shadow . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . $caption . '" href="'. esc_url( $lightgallery ) .'" ' . $link_attributes . ' ' . $data_cursor_skin . '>';

										}

									}

									/* video post */
									if( $post_format == 'video' ) {

										/* has external link */ 
										if( !empty( $external ) ) {

											$gallery .= '<a class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . $external . '" target="' . $target . '" ' . $data_cursor_skin . '>';

										} else {                                    

											// try to get video media
											$video_media = ut_get_portfolio_format_video_media( $portfolio_query->post->ID );

											if( !$video_media )
											$video_media = get_the_content();

											// check for possible Iframe
											$video_data = '';

											if( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['iframe'] ) ) {

												$video_data = 'data-iframe="true"';
												$popuplink = ut_get_portfolio_format_video_content( $video_media['iframe'] );

											} elseif( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['selfhosted'] ) ) {

												$gallery .= $video_media['selfhosted'];
												$video_data = 'data-html="#ut-selfhosted-lightbox-player-' . $portfolio_query->post->ID . '"';

											} else {

												// try to catch video url
												$popuplink = ut_get_portfolio_format_video_content( $video_media );

											}

											$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$gallery .= '<a data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" ' . $video_data . ' class="' . $image_style_class . ' ' . $gutter_shadow . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . $caption . '" href="' . esc_url( $popuplink ) . '" ' . $data_cursor_skin . '>';

										}

									}								

									/* gallery post */
									if( $post_format == 'gallery' ) {

										/* has external link */ 
										if( !empty( $external ) ) {

											$gallery .= '<a class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											$gallery .= ut_portfolio_lightgallery( $portfolio_query->post->ID , self::$token , $ut_page_hero_type );
											$gallery .= '<a data-exThumbImage="' . esc_url( $mini ) . '" class="ut-portfolio-popup-'.$portfolio_query->post->ID.' ' . $image_style_class . ' ' . $gutter_shadow . '" title="' . esc_attr( $caption ) . '" href="'. esc_url( $lightgallery ) .'" ' . $data_cursor_skin . '>';

										}

									}								

								}							

								if( has_post_thumbnail() ) :

                                    $gallery .= '<figure>' . UT_Adaptive_Image::create( get_post_thumbnail_id( $portfolio_query->post->ID ), array( $width, $height ), $hardcrop, 'landscape', false, 'skip-lazy ut-portfolio-featured-image ut-lazy-wait' ) . '</figure>';

                                elseif( !has_post_thumbnail() && $preview_video ) :

                                    $gallery .= '<figure><img alt="' . esc_attr( get_the_title() ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" src="' . self::create_placeholder_svg( $width, $height ) . '" class="lozad skip-lazy ut-portfolio-featured-image" /></figure>';

								endif;

                                    $gallery .= $preview_video; //@todo

									$gallery .= '<div class="ut-hover-layer">';

										$gallery .= '<div class="ut-portfolio-info">';

											$gallery .= '<div class="ut-portfolio-info-c">';

												$caption_content = isset( $gallery_options['style_1_caption_content'] ) ? $gallery_options['style_1_caption_content'] : 'title_category';

												if( $caption_content == 'title_category' ) {

													// item title + category 
													$gallery .= '<h3 class="portfolio-title">' . $title . '</h3>';								
													$gallery .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';

												}                            

												if( $caption_content == 'title_only' ) {

													// item title only
													$gallery .= '<h3 class="portfolio-title">' . $title . '</h3>';

												}

												if( $caption_content == 'category_only' ) {

													// categories only
													$gallery .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';								

												}

												if( $caption_content == 'plus_sign' ) {

													// plus sign only
													$gallery .= '<span class="ut-portfolio-info-plus">+</span>';								

												}

												if( $caption_content == 'custom_text' && !empty( $gallery_options['style_1_caption_custom_text'] ) ) {

													// categories only
													$gallery .= '<h3 class="portfolio-title">' . $gallery_options['style_1_caption_custom_text'] . '</h3>';								

												}

											$gallery .= '</div>';

										$gallery .= '</div>';								

									$gallery .= '</div>';								

								$gallery .= '</a>';

							$gallery .= '</div>';
					
						$gallery .= '</div>';
					
					$gallery .= '</article>';		

				}
								
				/* style two only images and title beneath */
				if( $gallery_style == 'style_two') {					
					
					/* create single portfolio item */
					$gallery .= '<article id="ut-portfolio-article-' . $portfolio_query->post->ID . '" class="ut-portfolio-article ut-masonry ' . $gutter_shadow . ' portfolio-style-two ' . implode( " ", $article_classes ) . '">';
					
						$gallery .= '<div class="ut-portfolio-article-animation-box">';

							$gallery .= '<div data-effect="' . esc_attr( $effect ) . '" class="ut-portfolio-item ut-hover">';
					
								$link_trigger_id = 'ut-portfolio-trigger-link-' . self::$token . '-' . esc_attr( $portfolio_query->post->ID );

								/* link markup for slideup details */
								if( $ut_portfolio_link_type == 'slideup' || $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'onepage' || $ut_portfolio_link_type == 'external' ) {							

									/* has external link */
									if($external) {

										$gallery .= '<a id="' . $link_trigger_id . '" class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

									} else {

										$gallery .= '<a id="' . $link_trigger_id . '" class="ut-portfolio-link ' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . self::$token . '" data-post="' . $portfolio_query->post->ID . '" href="#ut-portfolio-details-wrap-' . self::$token . '" ' . $data_cursor_skin . '>';

									}

								}

								/* link markup for image popup */
								if( $ut_portfolio_link_type == 'popup' ) {

									$popuplink = NULL;

									$mini = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-mini' );
									$mini = !empty( $mini[0] ) ? $mini[0] : "";

                                    $_portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
									$_portfolio_cats = ut_generate_cat_list( $_portfolio_cats );

									if( $has_media_type ) {

                                        $link_attributes = ut_get_morphbox_meta( ut_portfolio_get_image_id( $portfolio_detail_image ), $portfolio_query->post->ID, '', $_portfolio_cats );
									    $lightgallery = ut_get_morphbox_fullscreen( ut_portfolio_get_image_id( $portfolio_detail_image ) , 'ut-lightbox');

									} else {

                                        $link_attributes = ut_get_morphbox_meta( get_post_thumbnail_id( $portfolio_query->post->ID ), $portfolio_query->post->ID, '', $_portfolio_cats );
                                        $lightgallery = ut_get_morphbox_fullscreen( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'full' );

                                    }

                                    /* attributes string */
                                    $link_attributes = implode(' ', array_map(
                                        function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                                        $link_attributes,
                                        array_keys( $link_attributes )
                                    ) );

									$lightgallery = !empty( $lightgallery[0] ) ? $lightgallery[0] : $portfolio_detail_image;

									/* image post or audio post */
									if( empty( $post_format ) || $post_format == 'audio' ) {

										/* has external link */
										if( $external ) {

											$gallery .= '<a class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . esc_url( $external ) . '" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$gallery .= '<a id="' . $link_trigger_id . '" data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" class="' . $image_style_class . ' ' . $gutter_shadow . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="'. esc_url( $lightgallery ) .'" ' . $link_attributes . ' ' . $data_cursor_skin . '>';

										}

									}

									/* video post */
									if( $post_format == 'video' ) {

										/* has external link */ 
										if( !empty( $external ) ) {

											$gallery .= '<a id="' . $link_trigger_id . '" class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											// try to get video media
											$video_media = ut_get_portfolio_format_video_media( $portfolio_query->post->ID );

											if( !$video_media )
											$video_media = get_the_content();

											// check for possible Iframe
											$video_data = '';

											if( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['iframe'] ) ) {

												$video_data = 'data-iframe="true"';
												$popuplink = ut_get_portfolio_format_video_content( $video_media['iframe'] );

											} elseif( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['selfhosted'] ) ) {

												$gallery .= $video_media['selfhosted'];
												$video_data = 'data-html="#ut-selfhosted-lightbox-player-' . $portfolio_query->post->ID . '"';

											} else {

												// try to catch video url
												$popuplink = ut_get_portfolio_format_video_content( $video_media );

											}

											$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$gallery .= '<a id="' . $link_trigger_id . '" data-exThumbImage="' . $mini . '" data-rel="' . self::$lightbox . '" ' . $video_data . ' class="' . $image_style_class . ' ' . $gutter_shadow . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . esc_attr( $caption ) . '" href="' . esc_url( $popuplink ) . '" ' . $data_cursor_skin . '>';

										}

									}

									/* gallery post */
									if( $post_format == 'gallery' ) {

										/* has external link */
										if($external) {

											$gallery .= '<a id="' . $link_trigger_id . '" class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											$gallery .= ut_portfolio_lightgallery( $portfolio_query->post->ID , self::$token , $ut_page_hero_type );
											$gallery .= '<a id="' . $link_trigger_id . '" data-exThumbImage="' . $mini . '" class="ut-portfolio-popup-'.$portfolio_query->post->ID.' ' . $image_style_class . ' ' . $gutter_shadow . '" title="' . esc_attr( strip_tags( $title ) ) . '" href="#" ' . $data_cursor_skin . '>';

										}

									}								

								}

								if ( has_post_thumbnail() ) :

									$gallery .= '<figure>' . UT_Adaptive_Image::create( get_post_thumbnail_id( $portfolio_query->post->ID ), array( $width, $height ), $hardcrop, 'landscape', false, 'skip-lazy ut-portfolio-featured-image ut-lazy-wait' ) . '</figure>';

                                elseif( !has_post_thumbnail() && $preview_video ) :

                                    $gallery .= '<figure><img alt="' . esc_attr( get_the_title() ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" src="' . self::create_placeholder_svg( $width, $height ) . '" class="lozad skip-lazy ut-portfolio-featured-image" /></figure>';

								endif;

                                    $gallery .= $preview_video;

									$gallery .= '<div class="ut-hover-layer">';

										$gallery .= '<div class="ut-portfolio-info">';

											$gallery .= '<div class="ut-portfolio-info-c">';

												$caption_content = isset( $gallery_options['style_2_caption_content'] ) ? $gallery_options['style_2_caption_content'] : 'category_icon';

												if( $caption_content == 'category_icon' ) {

													// item title + category 
													if( $post_format == 'video' ) {
														$gallery .= '<i class="fa fa-film fa-lg"></i>';
													}

													if( $post_format == 'audio' ) {
														$gallery .= '<i class="fa fa-headphones fa-lg"></i>';
													}

													if(  $post_format == 'gallery' ) {
														$gallery .= '<i class="fa fa-camera-retro fa-lg"></i>';
													}

													if( empty($post_format) ) {
														$gallery .= '<i class="fa fa-picture-o fa-lg"></i>';
													}									

													$gallery .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';

												}

												if( $caption_content == 'category_only' ) {

													$gallery .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';

												}

												if( $caption_content == 'plus_sign' ) {

													// plus sign only
													$gallery .= '<span class="ut-portfolio-info-plus">+</span>';								

												}

												if( $caption_content == 'custom_text' && !empty( $gallery_options['style_2_caption_custom_text'] ) ) {

													// categories only
													$gallery .= '<span>' . $gallery_options['style_2_caption_custom_text'] . '</span>';								

												}

											$gallery .= '</div>';
										$gallery .= '</div>';
									$gallery .= '</div>';

								$gallery .= '</a>';

								$gallery .= '<div>';

									$a_tag = false;

									/* link markup for slideup details */
									if( $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'external' ) {							

										/* has external link */
										if($external) {

											$gallery .= '<a class="' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'">';

										} else {

											$gallery .= '<a class="ut-portfolio-link ' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . esc_attr( self::$token ) . '" data-post="' . esc_attr( $portfolio_query->post->ID ) . '" href="#ut-portfolio-details-wrap-' . esc_attr( self::$token ) . '">';

										}

									} else {

										$gallery .= '<a data-trigger="#ut-portfolio-trigger-link-' . self::$token . '-' . esc_attr( $portfolio_query->post->ID ) . '" class="ut-portfolio-trigger-link ' . $image_style_class . ' ' . $gutter_shadow . '" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="#">';    

									}

									/* item title */
									$gallery .= '<h3 class="portfolio-title">' . $title . '</h3>';
									$gallery .= '</a>';


								$gallery .= '</div>';

							$gallery .= '</div>';
					
						$gallery .= '</div>';
					
					$gallery .= '</article>';  
				
				}
            
            endwhile; endif;
            
        /* end portfolio container */
        $gallery .= '</div>';
		
		/* portfolio pagination */
		if( $portfolio_query->max_num_pages > 1 ){
									
			$gallery .= '<nav class="ut-portfolio-pagination clearfix '.(!empty($gallery_options['filter_style']) ? $gallery_options['filter_style'] : 'style_one').'">';
			
			if ( $paged > 1 ) { 
        		$gallery .= '<a href="?paged=' . ($paged -1) .'&amp;portfolioID='.self::$token.'&amp;termID='.$term.'#ut-portfolio-items-' . self::$token . '-anchor"><i class="fa fa-angle-double-left"></i></a>';
			}
			
			for( $i=1 ;$i<=$portfolio_query->max_num_pages; $i++ ){
				
				$selected = ($paged == $i || ( empty($paged) && $i == 1 ) ) ? 'selected' : '';
				$gallery .= '<a href="?paged=' . $i . '&amp;portfolioID='.self::$token.'&amp;termID='.$term.'#ut-portfolio-items-' . self::$token . '-anchor" class="' . $selected . '">' . $i . '</a>';

            }
			
			if($paged < $portfolio_query->max_num_pages){
                $paged_next = $paged == 0 ? $paged + 1 : $paged;
                $gallery .= '<a href="?paged=' . ($paged_next + 1) . '&amp;portfolioID='.self::$token.'&amp;termID='.$term.'#ut-portfolio-items-' . self::$token . '-anchor"><i class="fa fa-angle-double-right"></i></a>';
            } 
			
			$gallery .= '</nav>';
		
		}

				
        /* end portfolio wrap */
        $gallery .= '</div>';
        
        /* reset query */
		wp_reset_postdata();
        
        /* return final gallery */
        return $gallery;

    
    }
        
    static function generate_gallery_script( $gallery_options ) {
        
		$gutter      = !empty( $gallery_options['gutter'] )      	? 'on' : '';
        $gutter_size = !empty( $gallery_options['gutter_size'] ) 	? $gallery_options['gutter_size'] : '1';
		$animation   = !empty( $gallery_options['animation_out'] )  ? $gallery_options['animation_out'] : 'portfolioZoomOut';
		
		ob_start(); ?>
        
        <script type="text/javascript">
        (function($) {

            "use strict";

            /* check if is in vc front end
            ================================================== */
            function inIframe() {

                var field = 'vc_editable';
                var url = window.location.href ;

                if( url.indexOf('?' + field + '=') !== -1 ) {

                    return true;

                } else if( url.indexOf('&' + field + '=') !== -1 ) {

                    return true;

                } else {

                    return false;

                }

            }

            if( inIframe() ) {

                $(window).trigger("load");

            }

            $(window).on("load", function(){

                $(document).ready( function(){

                var inc = "0";

                function calculate_portfolio_item_width_with_gutter( $container, columns, guttersize, increament ) {
                    
                    var step = increament ? increament : 0.1,
                        extra_spacing = $container.hasClass('ut-portfolio-item-container-with-background') ? guttersize : 0,
                        size = ( $container.get(0).getBoundingClientRect().width - extra_spacing - guttersize ) / columns - ( guttersize * ( columns - 1 ) / columns ) - step;

                    if( ( ( size + guttersize ) * columns ) < $container.get(0).getBoundingClientRect().width ) {
                                                
                        inc = 0;                        
                        return size;                        
                        
                    } else {
                        
                        inc = inc + 0.2;
                        return calculate_portfolio_item_width_with_gutter( $container, columns, guttersize, inc );
                        
                    }                    
                
                }
				
                var $win = $(window),
                    $container = $("#ut-portfolio-items-<?php echo self::$token; ?>"),
					columns = <?php echo $gallery_options['columns']; ?>,
					gutter = "<?php echo $gutter; ?>",
					gutterwidth = "",
                    guttersize = "<?php echo $gutter_size; ?>";
                
				$container.on( "layoutComplete", function( event, filteredItems ){

					$(filteredItems).each(function( index, element ){

						$(element.element).addClass("sorted").css({
							<?php if( $animation == 'portfolioZoomOut' ) { ?>"opacity" : "1", "transform" : "scale(1)" <?php } ?>
							<?php if( $animation == 'portfolioFadeOut' ) { ?>"opacity" : "1" <?php } ?>
						});

					});

                    $container.addClass("layoutComplete");

                    $(window).trigger('scroll');
                    $.waypoints("refresh");
                    $.force_appear();

                    setTimeout(function(  ) {

                        $(window).trigger('scroll');
                        $.waypoints("refresh");
                        $.force_appear();

                    }, 401 );


				});
				
                var hide_timout;
                
				function hide_portfolio_articles( selector ) {

                    $container.removeClass("layoutComplete");
                    
					clearTimeout( hide_timout );
                    
					$("> .ut-portfolio-article", "#ut-portfolio-items-<?php echo self::$token; ?>").each(function(){


						if( $(window).width() <= 1024 ) {
                            
                            $(this).addClass("ut-portfolio-article-animation").removeClass("sorted").css({
                                "opacity" : "0"
                            }); 
                            
                        } else {
                        
                            $(this).addClass("ut-portfolio-article-animation").removeClass("sorted").css({
                                <?php if( $animation == 'portfolioZoomOut' ) { ?>"opacity" : "0", "transform" : "scale(0.001)" <?php } ?>
                                <?php if( $animation == 'portfolioFadeOut' ) { ?>"opacity" : "0" <?php } ?>
                            }); 
                            
                        } 

					});

					setTimeout( function() {

						$("> .ut-portfolio-article .ut-portfolio-article-animation-box", "#ut-portfolio-items-<?php echo self::$token; ?>").each(function(){
                            
                            var $this = $(this);
                            
							$this.clearQueue().css({
								"visibility" : "hidden",
								"opacity" : "0"
							}).removeClass( $this.children(".ut-portfolio-item").data("effect") ); 

						});

                        hide_timout = setTimeout(function(  ) {

                            $container.isotope({ filter: selector });
                        
                        }, 400 );
                            
					}, 400 );

				}

                function hide_and_relayout_portfolio_articles( callback ) {

                    $container.removeClass("layoutComplete");

                    $("> .ut-portfolio-article", "#ut-portfolio-items-<?php echo self::$token; ?>").each(function(){

                        if( $(window).width() <= 1024 ) {

                            $(this).addClass("ut-portfolio-article-animation").removeClass("sorted").css({
                                "opacity" : "0"
                            });

                        } else {

                            $(this).addClass("ut-portfolio-article-animation").removeClass("sorted").css({
                                <?php if( $animation == 'portfolioZoomOut' ) { ?>"opacity" : "0", "transform" : "scale(0.001)" <?php } ?>
                                <?php if( $animation == 'portfolioFadeOut' ) { ?>"opacity" : "0" <?php } ?>
                            });

                        }

                    });

                    setTimeout( function() {

                        if( callback && typeof(callback) === "function" ) {

                            callback();

                        }

                    }, 350 );

                }

				function ut_call_isotope( force_column, callback ) {

					if( $(window).width() > 1024) {
						columns = <?php echo $gallery_options['columns']; ?>;
                        guttersize = "<?php echo $gutter_size; ?>";
					} 

					if( force_column ) {
					    columns = force_column;
                    }

					if( $(window).width() <= 1024) {
						columns = 2;
                        guttersize = "<?php echo $gutter_size; ?>";
					} 
                    
					if( $(window).width() <= 767) {
                        columns = 1;
                        guttersize = 1.5;
					} 				
					
                    if( $(window).width() <= 400 ) {
                        guttersize = 1;
                    }

					if( gutter === "on" ) {
                        
                        $container.children().width( calculate_portfolio_item_width_with_gutter( $container, columns, ( 20 * guttersize ), false ) ).addClass("show");
						gutterwidth = 20 * guttersize;
                        
					} else {
						
                        $container.children().width( $container.get(0).getBoundingClientRect().width / columns - 0.05 ).addClass("show");
						gutterwidth = 0;
                        
					}

                    if( callback && typeof(callback) === "function" ) {

                        callback( columns, gutterwidth );

                    } else {

                        $container.ut_require_js({
                            plugin: 'isotope',
                            source: 'isotope',
                            callback: function (element) {

                                element.addClass("animated").isotope({

                                    itemSelector : ".ut-portfolio-article",
                                    layoutMode: "fitRows",
                                    masonry: { columnWidth: element.get(0).getBoundingClientRect().width / columns - gutterwidth },
                                    itemPositionDataEnabled: true,
                                    transitionDuration: 0

                                }).isotope("layout");

                                $('.ut-lazy-wait', $container ).each(function (index, element ) {

                                    $(element).removeClass('ut-lazy-wait');

                                });

                            }

                        });

                    }

				}

                $(window).utresize(function(){

                    /* update isotope */
                    ut_call_isotope();

                });

                /* create isotope */
                ut_call_isotope();

				/* additional call for ajax filter */
				<?php if( $gallery_options['filter_type'] == 'ajax' ) : ?>

                    /* Isotope Filtering
                    ================================================== */
                    $("#ut-portfolio-menu-<?php echo self::$token; ?> a").each(function( index ) {

                        if( $(this).parent().hasClass('ut-portfolio-layout-change') ) {
                            return true;
                        }

                        var searchforclass = $(this).attr("data-filter");

                        if( !$(searchforclass).length  ) {
                            // hide filter if we do not have any children to filter
                            $(this).hide();
                        }

                    });

                    $("#ut-portfolio-menu-<?php echo self::$token; ?> a").on('click', function(event) {

                        var $this = $(this);

                        if( $this.parent().hasClass('ut-portfolio-layout-change') ) {
                            return true;
                        }

                        if( $this.hasClass("selected") ) {

                            event.preventDefault();
                            return;

                        }

                        var selector = $this.attr("data-filter");

                        if ( !$this.hasClass("selected") ) {

                            $this.parents("#ut-portfolio-menu-<?php echo self::$token; ?>").find(".selected").removeClass("selected");
                            $this.addClass("selected");

                        }

                        hide_portfolio_articles( selector );

                        return false;

                    });

                    $("#ut-portfolio-menu-<?php echo self::$token; ?> .ut-portfolio-layout-change a").on('click', function(event) {

                        var $this = $(this),
                            $columns = $this.data('default-columns'),
                            $columns_alternate = $this.data('alternate-columns');

                        var selector = $("#ut-portfolio-menu-<?php echo self::$token; ?> a.selected").attr("data-filter");

                        // restore initial layout
                        if( $this.hasClass('ut-alternate-columns-active') ) {

                            hide_and_relayout_portfolio_articles( function () {

                                ut_call_isotope( $columns, function ( columns, gutterwidth ) {

                                    $("> .ut-portfolio-article .ut-portfolio-article-animation-box", "#ut-portfolio-items-<?php echo self::$token; ?>").css({
                                        "visibility" : "hidden",
                                        "opacity" : "0"
                                    });

                                    setTimeout(function() {

                                        $container.isotope({
                                            filter: selector
                                        });

                                    }, 760 );

                                });

                                $this.removeClass('ut-alternate-columns-active');

                            });

                        // switch to alternate layout
                        } else {

                            hide_and_relayout_portfolio_articles( function () {

                                ut_call_isotope( $columns_alternate, function ( columns, gutterwidth ) {

                                    $("> .ut-portfolio-article .ut-portfolio-article-animation-box", "#ut-portfolio-items-<?php echo self::$token; ?>").css({
                                        "visibility" : "hidden",
                                        "opacity" : "0"
                                    });

                                    setTimeout(function(  ) {

                                        $container.isotope({
                                            filter: selector
                                        });

                                    }, 760 );

                                });

                                $this.addClass('ut-alternate-columns-active');

                            });

                        }

                        event.preventDefault();

                    });

				<?php endif; ?>
				       
           		});
        
            });
			
        }(jQuery));

        </script>        
       	
		<?php return ob_get_clean();		
    
    }
    
    /*
    |--------------------------------------------------------------------------
    | Portfolio Filterable Packery Gallery
    |--------------------------------------------------------------------------
    */
    static function create_portfolio_packery_gallery() {
		
		global $paged, $wp_query;

        /* pagination */		
        if  ( empty($paged) ) {
                
                if ( !empty( $_GET['paged'] ) ) {
                        $paged = $_GET['paged'];
                } elseif ( !empty($wp->matched_query) && $args = wp_parse_args($wp->matched_query) ) {
                        if ( !empty( $args['paged'] ) ) {
                                $paged = $args['paged'];
                        }
                }
                if ( !empty($paged) ) {
                    $wp_query->set('paged', $paged);
                }
                
        }           
		
		/* settings */
		$portfolio_categories = get_post_meta( self::$token , 'ut_portfolio_categories' );
		$term = '';
		
		/* global portfolio settings */
        $portfolio_settings = get_post_meta( self::$token , 'ut_portfolio_settings', true );
				
        // gallery options
        $gallery_options = get_post_meta( self::$token , 'ut_packery_options', true );
        
		// columnset 
		$columnset = !empty($gallery_options['columns']) ? $gallery_options['columns'] : 4;

		// effect settings
		$effect = !empty( $gallery_options['animation_in'] ) ? $gallery_options['animation_in'] : 'portfolioFadeIn';		
		
		/* fallback if no meta has been set */
		$portfolio_per_page   = !empty($portfolio_settings['posts_per_page']) ? $portfolio_settings['posts_per_page'] : 4 ;
		$portfolio_categories = !empty($portfolio_categories) ? $portfolio_categories : array();
		
		/* portfolio query terms */
		$portfolio_terms = array();
		$portfolio_terms_query = '';
        
        if( !empty($portfolio_categories[0]) && is_array($portfolio_categories[0]) ) {

			foreach( $portfolio_categories[0] as $key => $value) {                
                array_push($portfolio_terms , $key);
			}
			
			$portfolio_terms_query = $portfolio_terms;
			
		}
				
        /* query args */
        $portfolio_args = array(
            
            'post_type'      => 'portfolio',
            'posts_per_page' => $portfolio_per_page,
            'paged'          => $paged,
            'tax_query'      => array( array(
                    'taxonomy' => 'portfolio-category',
                    'terms'    => $portfolio_terms_query,
                    'field'    => 'term_id'  
            ) )
            
        
        );
        
        /* start query */
        $portfolio_query = new WP_Query( $portfolio_args );
        
        /* portfolio filter */                
        if( !empty($gallery_options['filter']) ) :
        		
        $filter  = '<div class="ut-portfolio-menu-wrap" ' . ( !empty( $gallery_options['filter_align'] ) ? 'style="text-align:' . $gallery_options['filter_align'] .';"' : '' ) . '><ul id="ut-portfolio-menu-' . self::$token . '" class="ut-portfolio-menu ' . ( !empty( $gallery_options['filter_style'] ) ? $gallery_options['filter_style'] : 'style_one' ) . ' ' . ( !empty( $gallery_options['filter_spacing'] ) ? 'ut-portfolio-menu-' . $gallery_options['filter_spacing'] : '' ) . '">';
            
            /* reset button */
			$reset = !empty($gallery_options['reset_text']) ? $gallery_options['reset_text'] : __('All' , 'ut_portfolio_lang');
			
            $filter .= '<li><a href="#" data-filter="*" class="selected">' . $reset . '</a></li>';				
            
            /* get taxonomies */
            $taxonomies = get_terms('portfolio-category');
			$taxonomiesarray =  json_decode(json_encode($taxonomies), true);
					    
            if( is_array($portfolio_terms) && is_array($taxonomies) ) {
                
                foreach ($portfolio_terms as $single_term ) {
										
                    $key = self::search_tax_key( $single_term , $taxonomiesarray );
										                    
                    if( isset($taxonomies[$key]->slug) ) {
                        $filter .= '<li><a href="#" data-filter=".'.$taxonomies[$key]->slug.'-filt">'.$taxonomies[$key]->name.'</a></li>';
                    }
                        
					
					
                }
            
			}      
        
        $filter .= '</ul></div>';
        
        endif;
        
        /* needed javascript */
		$gallery = self::generate_gallery_packery_script( $gallery_options );
        
        /* needed css */
        $gallery .= self::create_custom_css( $gallery_options );

        /* needed css */
        $gallery .= self::create_single_custom_css( $portfolio_args );

        /* need responsive css */
        $gallery .= self::create_responsive_css( $gallery_options );
        
		/* create hidden gallery */
		$gallery .= self::create_hidden_popup_portfolio( $portfolio_args , $portfolio_settings['image_style'] );
		
        if( function_exists('ot_get_option') ) {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : ot_get_option('ut_global_portfolio_title_color','#FFF');
            
        } else {
            
            $textcolor = !empty( $portfolio_settings["text_color"] ) ? $portfolio_settings["text_color"] : '#FFF';
            
        }
        
        /* output portfolio wrap */
        $gallery .= '<div id="ut-portfolio-wrap" class="ut-portfolio-wrap ut-portfolio-packery-wrap ut-portfolio-' . self::$token . '" ' . self::$slide_up_title . ' ' . self::$slide_up_width . ' data-textcolor="' . esc_attr( $textcolor ) .'" data-opacity="' . $portfolio_settings["hover_opacity"] . '" data-hovercolor="' . ut_hex_to_rgb($portfolio_settings["hover_color"]) . '">';
		$gallery .= '<a class="ut-portfolio-offset-anchor" style="top:-120px;" id="ut-portfolio-items-' . self::$token . '-anchor"></a>';
		
        /* add filter */
        if( isset($gallery_options['filter']) ) {
            $gallery .= $filter;
        }
        
        /* output portfolio container */
        $gallery .= '<div id="ut-portfolio-items-' . self::$token . '" class="ut-portfolio-item-packery-container ut-portfolio-item-container-'. $columnset .'-columns">';
        	
            /* loop trough portfolio items */
			if ($portfolio_query->have_posts()) : while ($portfolio_query->have_posts()) : $portfolio_query->the_post();

                // classes on article
                $article_classes = array();

                // image style
                $article_classes[] = $portfolio_settings['image_style'];

                /* needed variables */
                $title = str_ireplace('"', '', trim(get_the_title()));
                $post_format = get_post_format();
				
                /* link settings */
                $ut_portfolio_link_type = get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true);
                $ut_portfolio_link_type = empty( $ut_portfolio_link_type ) || !empty( $ut_portfolio_link_type ) && $ut_portfolio_link_type == 'global' ? self::$detailstyle : $ut_portfolio_link_type;
        
                $external = false;
                $target = '_blank';
                
                if( $ut_portfolio_link_type == 'external' ) {
                   
                   /* get link user haas to set */ 
                   $external = get_post_meta( $portfolio_query->post->ID , 'ut_external_link' , true);

	                $external_target = get_post_meta( $portfolio_query->post->ID , 'ut_external_link_target' , true);
	                $target = !empty( $external_target ) ? $external_target : $target;
                    
                } elseif( $ut_portfolio_link_type == 'internal' ) {
                   
                   /* get permalink to single portfolio */
                   $external = get_permalink( $portfolio_query->post->ID );
                   $target = '_self';
                   
                }
                
                /* get all portfolio-categories for this item ( needed for filter ) */
                $portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
                
                /* set filter attributes */               
                if( is_array($portfolio_cats) ) {
                    foreach($portfolio_cats as $single_cat) {
                        $article_classes[] =  $single_cat->slug."-filt ";
                    }
                }
                
				/* gallery style */
				$gallery_style = !empty($gallery_options['style']) ? $gallery_options['style'] : 'style_one';

                if( $gallery_style == 'style_three' ) {

                    $article_classes[] = 'ut-portfolio-article-tilt';

                }
				
				/* portfolio featured image */
				$fullsize = $thumbnail = $portfolio_detail_image = wp_get_attachment_url( get_post_thumbnail_id($portfolio_query->post->ID) );
				$caption = get_post( get_post_thumbnail_id( $portfolio_query->post->ID ) )->post_excerpt;
				
                /* hero type setting - new with 2.6 */
                $ut_page_hero_type = get_post_meta($portfolio_query->post->ID , 'ut_page_hero_type' , true);
                $optionkey = 'ut_page_hero_image';
                
                if( !empty($ut_page_hero_type) ) {
                    
                    if($ut_page_hero_type == 'image' || $ut_page_hero_type == 'animatedimage' || $ut_page_hero_type == 'splithero' || $ut_page_hero_type == 'custom' || $ut_page_hero_type == 'dynamic') {
                        $post_format = '';
                    }
                    
                    if($ut_page_hero_type == 'slider' || $ut_page_hero_type == 'transition' || $ut_page_hero_type == 'tabs') {
                        $post_format = 'gallery';
                    }
                    
                    if($ut_page_hero_type == 'video') {
                        $post_format = 'video';
                        $ut_page_video_source = get_post_meta($portfolio_query->post->ID, 'ut_page_video_source' , true);
                    }
                    
                    /* switch option key */
                    switch ($ut_page_hero_type) {
                    
                        case 'animatedimage':
                        $optionkey = 'ut_page_hero_animated_image';
                        break;
                        
                        default:  
                        $optionkey = 'ut_page_hero_image';  
                    
                    }                  
                
                } /* end ut_page_hero_type */
                
		
		
				/* new portfolio settings since 4.7.4 */
				$has_media_type = false;
				$onpage_media_type = get_post_meta( $portfolio_query->post->ID , 'ut_onpage_portfolio_media_type' , true);
		
				if( !empty( $onpage_media_type ) ) {
					
					if( $onpage_media_type == 'image' ) {
						$ut_page_hero_type = 'image';
						$optionkey = 'ut_onpage_portfolio_image';
						$post_format = '';	
					}
					
					if( $onpage_media_type == 'gallery' ) {
						$ut_page_hero_type = 'gallery';
						$post_format = 'gallery';	
					}
					
					if( $onpage_media_type == 'video' ) {
						$ut_page_hero_type = 'video';
						$post_format = 'video';	
					}
					
					$has_media_type = true;
					
				}
				/* end new portfolio settings */
		
		
                                
                /* check if there is a detail image available */
                $portfolio_detail_image_data = get_post_meta( $portfolio_query->post->ID , $optionkey , true );                
                
                if( is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data['background-image']) ) {
                        
                    $portfolio_detail_image = $portfolio_detail_image_data['background-image'];
                    
                } elseif( !is_array($portfolio_detail_image_data) && !empty($portfolio_detail_image_data) ) {
                    
                    $portfolio_detail_image = $portfolio_detail_image_data;
                    
                }
		
				/* cropping dimensions */
				$width  = !empty($gallery_options['crop_size_x']) ? $gallery_options['crop_size_x'] : '1536';
				$height = !empty($gallery_options['crop_size_y']) ? $gallery_options['crop_size_y'] : '1024';
				
                /* mobile image */
                $mobile_image = array();
                $mobile_image['src']    = ut_resize( $fullsize , $width , $height , true , true , true );
                $mobile_image['height'] = $height;
                $mobile_image['width']  = $width;
                
                /* default size */                
                $size = 'default';
                
                /* portrait size */
                if( get_post_meta( $portfolio_query->post->ID , 'ut_showcase_image_size' , true ) == 'portrait' ) {
                    
                    $height = $height * 2;
                    $size = 'portrait';
                    
                }
                
                /* panorama size */
                if( get_post_meta( $portfolio_query->post->ID , 'ut_showcase_image_size' , true ) == 'panorama' ) {
                    
                    $width = $width * 2;
                    $size = 'panorama';    
                
                }
                
                /* xxl size */
                if( get_post_meta( $portfolio_query->post->ID , 'ut_showcase_image_size' , true ) == 'xxl' ) {
                    
                    $height = $height * 2;
                    $width = $width * 2;
                    $size = 'xxl';
                
                }

                // add size to article classes
                $article_classes[] = 'ut-masonry-' . $size;

                // check if hardcropping is activated
                $hardcrop = !empty( $gallery_options['hardcrop'] ) ? false : true;        
        
                /* create thumbnail */
				$thumbnail = ut_resize( $fullsize, $width, $height, $hardcrop, true, true );
				
                /* flag */
                $a_tag = false;

                // preview video
                $preview_video = ut_get_portfolio_preview_video( $portfolio_query->post->ID, $post_format, self::$token );

                if( $preview_video ) {
                    $article_classes[] = 'ut-has-background-video';
                }

				// hover visibility
				if( isset( $gallery_options['caption_visibility'] ) && $gallery_options['caption_visibility'] == 'permanent' ) {
					$article_classes[] = 'ut-portfolio-article-permanent-caption';
				}

				// custom cursor
				$data_cursor_skin = '';

				if( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) {

                    $data_cursor_skin = 'data-cursor-skin="' . esc_attr( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_showcase_custom_cursor_skin' , true ) ) . '"';

				}

				/* style one images with title */
				if( ( $gallery_style == 'style_one' || $gallery_style == 'style_three' ) && $thumbnail ) {
				    
					/* create single portfolio item */
					$gallery .= '<article id="ut-portfolio-article-' . $portfolio_query->post->ID . '" data-size="' . $size . '" class="ut-portfolio-article ut-masonry show portfolio-style-one ' . implode(" ", $article_classes ) . '">';
                    
						$gallery .= '<div class="ut-portfolio-article-animation-box">';

                            $gallery .= $preview_video;
					
							$gallery .= '<div data-effect="' . esc_attr( $effect ) . '" data-background-image="' . esc_url( $thumbnail ) . '" class="ut-portfolio-item ut-hover lozad skip-lazy">';

								// link markup for slideup details
								if( $ut_portfolio_link_type == 'slideup' || $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'onepage' || $ut_portfolio_link_type == 'external' ) {							

									/* has external link */
									if( !empty( $external ) ) {

										$gallery .= '<a rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . $external . '" target="'.$target.'" ' . $data_cursor_skin . '>';

									} else {

										$gallery .= '<a class="ut-portfolio-link" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . self::$token . '" data-post="' . $portfolio_query->post->ID . '" href="#ut-portfolio-details-wrap-' . self::$token . '" ' . $data_cursor_skin . '>';

									}

									$a_tag = true;


								}

								/* link markup for image popup */
								if( $ut_portfolio_link_type == 'popup' && !$a_tag ) {

									$popuplink = NULL;

									$mini = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-mini' );
									$mini = !empty( $mini[0] ) ? $mini[0] : "";

									$_portfolio_cats = wp_get_object_terms( $portfolio_query->post->ID , 'portfolio-category' );
									$_portfolio_cats = ut_generate_cat_list( $_portfolio_cats );

									if( $has_media_type ) {

                                        $link_attributes = ut_get_morphbox_meta( ut_portfolio_get_image_id( $portfolio_detail_image ), $portfolio_query->post->ID, '', $_portfolio_cats );
                                        $lightgallery = ut_get_morphbox_fullscreen( ut_portfolio_get_image_id( $portfolio_detail_image ) , 'ut-lightbox');

									} else {

                                        $link_attributes = ut_get_morphbox_meta( get_post_thumbnail_id( $portfolio_query->post->ID ), $portfolio_query->post->ID, '', $_portfolio_cats );
									    $lightgallery = ut_get_morphbox_fullscreen( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-lightbox' );

									}

                                    /* attributes string */
                                    $link_attributes = implode(' ', array_map(
                                        function ($v, $k) { return sprintf("%s=\"%s\"", $k, $v); },
                                        $link_attributes,
                                        array_keys( $link_attributes )
                                    ) );

									$lightgallery = !empty( $lightgallery[0] ) ? $lightgallery[0] : $portfolio_detail_image;

									/* image post or audio post */
									if( empty( $post_format ) || $post_format == 'audio' ) {

										/* has external link */ 
										if( !empty( $external ) ) {

											$gallery .= '<a rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . $external . '" target="' . $target . '" ' . $data_cursor_skin . '>';

										} else {

											if( get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true ) == 'popup' ) {

												$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';

											}

											$gallery .= '<a data-rel="' . self::$lightbox . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" data-exthumbimage="' .  esc_url( $mini ) . '" title="' . esc_attr( $caption ) . '" href="'. esc_url( $lightgallery ) .'" ' . $link_attributes . ' ' . $data_cursor_skin . '>';


										}

									}

									/* video post */
									if( $post_format == 'video' ) {

										/* has external link */ 
										if( !empty($external) ) {

											$gallery .= '<a rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="' . $external . '" target="' . $target . '" ' . $data_cursor_skin . '>';

										} else {                                    

											// try to get video media
											$video_media = ut_get_portfolio_format_video_media( $portfolio_query->post->ID );

											if( !$video_media )
											$video_media = get_the_content();

											// check for possible Iframe
											$video_data = '';

											if( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['iframe'] ) ) {

												$video_data = 'data-iframe="true"';
												$popuplink = ut_get_portfolio_format_video_content( $video_media['iframe'] );

											} elseif( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['selfhosted'] ) ) {

												$gallery .= $video_media['selfhosted'];
												$video_data = 'data-html="#ut-selfhosted-lightbox-player-' . $portfolio_query->post->ID . '"';

											} else {

												// try to catch video url
												$popuplink = ut_get_portfolio_format_video_content( $video_media );

											}

											$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$gallery .= '<a data-rel="' . self::$lightbox . '" ' . $video_data . ' data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" data-exthumbimage="' .  esc_url( $mini ) . '" title="' . esc_attr( $caption ) . '" href="' . esc_url( $popuplink ) . '" ' . $data_cursor_skin . '>';

										}

									}

									/* gallery post */
									if( $post_format == 'gallery' ) {

										/* has external link */ 
										if( !empty($external) ) {

											$gallery .= '<a rel="bookmark" title="' . $title . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											$gallery .= ut_portfolio_lightgallery( $portfolio_query->post->ID , self::$token , $ut_page_hero_type );                                        
											$gallery .= '<a data-exthumbimage="' .  esc_url( $mini ) . '" class="ut-portfolio-popup-'.$portfolio_query->post->ID.'" title="' . esc_attr( $caption ) . '" href="'. esc_url( $lightgallery ) .'" ' . $data_cursor_skin . '>';

										}

									}								

								}							

								if( post_password_required() ) :

									$gallery .= '<div class="ut-password-protected">' . __('Password Protected Portfolio' , 'ut_portfolio_lang') . '</div>';

								endif;

									$gallery .= '<div class="ut-hover-layer">';

										$gallery .= '<div class="ut-portfolio-info">';

											$gallery .= '<div class="ut-portfolio-info-c">';

												$gallery .= '<h3 class="portfolio-title">' . $title . '</h3>';

												if( get_post_meta($portfolio_query->post->ID , 'ut_showcase_custom_title', true ) ) {

													$gallery .= '<span>' . get_post_meta($portfolio_query->post->ID , 'ut_showcase_custom_title', true ) . '</span>';

												} else {

													$gallery .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';

												}							

											$gallery .= '</div>';

										$gallery .= '</div>';

									$gallery .= '</div>';								

									if( ut_generate_packery_caption( $portfolio_query->post->ID ) ) {

										$gallery .= ut_generate_packery_caption( $portfolio_query->post->ID );								

									}

								$gallery .= '</a>';

							$gallery .= '</div>';
					
						$gallery .= '</div>';
					
					$gallery .= '</article>';		

				}
				
				/* style two only images and title beneath */
				if( $gallery_style == 'style_two' && $thumbnail ) {					
					
                    $lightbox = 'ut-lightgallery-alternative';
                    
					/* create single portfolio item */
					$gallery .= '<article id="ut-portfolio-article-' . $portfolio_query->post->ID . '" data-size="' . $size . '" class="ut-portfolio-article ut-masonry show portfolio-style-two ' . implode(" ", $article_classes ) . '">';
					
						$gallery .= '<div class="ut-portfolio-article-animation-box">';

                            $gallery .= $preview_video;
					
							$gallery .= '<div data-effect="' . esc_attr( $effect ) . '" data-background-image="' . esc_url( $thumbnail ) . '" class="ut-portfolio-item ut-hover lozad skip-lazy">';

								/* link markup for slideup details */
								if( $ut_portfolio_link_type == 'slideup' || $ut_portfolio_link_type == 'internal' || $ut_portfolio_link_type == 'onepage' || $ut_portfolio_link_type == 'external' ) {							

									/* has external link */
									if( $external ) {

										$gallery .= '<a rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

									} else {

										$gallery .= '<a class="ut-portfolio-link" rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" data-wrap="' . self::$token . '" data-post="' . $portfolio_query->post->ID . '" href="#ut-portfolio-details-wrap-' . self::$token . '" ' . $data_cursor_skin . '>';

									}

								}

								/* link markup for image popup */
								if( $ut_portfolio_link_type == 'popup' || get_post_meta( $portfolio_query->post->ID , 'ut_portfolio_link_type' , true ) == 'popup' ) {

									$popuplink = NULL;

                                    $mini = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio_query->post->ID ) , 'ut-mini' );
                                    $mini = !empty( $mini[0] ) ? $mini[0] : "";

									/* image post or audio post */
									if( empty( $post_format ) || $post_format == 'audio' ) {

										/* has external link */
										if($external) {

											$gallery .= '<a rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$gallery .= '<a data-rel="' . self::$lightbox . '" data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . $caption . '" data-exthumbimage="' .  esc_url( $mini ) . '" href="'. $portfolio_detail_image .'" ' . $data_cursor_skin . '>';

										}

									}

									/* video post */
									if( $post_format == 'video' ) {

										/* has external link */ 
										if( !empty($external) ) {

											$gallery .= '<a rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											// try to get video media
											$video_media = ut_get_portfolio_format_video_media( $portfolio_query->post->ID );

											if( !$video_media )
											$video_media = get_the_content();

											// check for possible Iframe
											$video_data = '';

											if( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['iframe'] ) ) {

												$video_data = 'data-iframe="true"';
												$popuplink = ut_get_portfolio_format_video_content( $video_media['iframe'] );

											} elseif( !empty( $video_media ) && is_array( $video_media ) && isset( $video_media['selfhosted'] ) ) {

												$gallery .= $video_media['selfhosted'];
												$video_data = 'data-html="#ut-selfhosted-lightbox-player-' . $portfolio_query->post->ID . '"';

											} else {

												// try to catch video url
												$popuplink = ut_get_portfolio_format_video_content( $video_media );

											}

											$gallery .= '<div class="ut-new-hide" id="ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '"><h4>' . $title . '</h4><p>' . $caption . '</p></div>';
											$gallery .= '<a data-rel="' . self::$lightbox . '" ' . $video_data . ' data-sub-html="#ut-lightgallery-sub-html-' . $portfolio_query->post->ID . '" title="' . $caption . '" data-exthumbimage="' .  esc_url( $mini ) . '" href="'.$popuplink.'" ' . $data_cursor_skin . '>';

										}

									}

									/* gallery post */
									if( $post_format == 'gallery' ) {

										/* has external link */
										if($external) {

											$gallery .= '<a rel="bookmark" title="' . esc_attr( strip_tags( $title ) ) . '" href="'.$external.'" target="'.$target.'" ' . $data_cursor_skin . '>';

										} else {

											$gallery .= ut_portfolio_lightgallery( $portfolio_query->post->ID , self::$token , $ut_page_hero_type );
											$gallery .= '<a class="ut-portfolio-popup-'.$portfolio_query->post->ID.'" title="' . $title . '" href="#" ' . $data_cursor_skin . '>';

										}

									}								

								}

								if( post_password_required() ) :

									$gallery .= '<div class="ut-password-protected">' . __('Password Protected Portfolio' , 'ut_portfolio_lang') . '</div>';

								endif;

									$gallery .= '<div class="ut-hover-layer">';

										$gallery .= '<div class="ut-portfolio-info">';

											$gallery .= '<div class="ut-portfolio-info-c">';

												if( get_post_meta($portfolio_query->post->ID , 'ut_showcase_custom_title', true ) ) {

													$gallery .= '<span>' . get_post_meta($portfolio_query->post->ID , 'ut_showcase_custom_title', true ) . '</span>';

												} else {

													$gallery .= '<span>' . ut_generate_cat_list( $portfolio_cats ) . '</span>';

												}

											$gallery .= '</div>';

										$gallery .= '</div>';

									$gallery .= '</div>';

									if( ut_generate_packery_caption( $portfolio_query->post->ID ) ) {

										$gallery .= ut_generate_packery_caption( $portfolio_query->post->ID );								

									}

								$gallery .= '</a>';

							$gallery .= '</div>';

							$gallery .= '<div class="ut-portfolio-title-wrap">';

								$gallery .= '<div class="ut-portfolio-title-inner-wrap"><h3 class="portfolio-title">' . $title . '</h3></div>';

							$gallery .= '</div>';
                        					
						$gallery .= '</div>';
					
					$gallery .= '</article>';  
				
				}
            
            endwhile; endif;
            
        /* end portfolio container */
        $gallery .= '</div>';
		
		/* portfolio pagination */
		if( $portfolio_query->max_num_pages > 1 ){
									
			$gallery .= '<nav class="ut-portfolio-pagination clearfix '.(!empty($gallery_options['filter_style']) ? $gallery_options['filter_style'] : 'style_one').'">';
			
			if ( $paged > 1 ) { 
        		$gallery .= '<a href="?paged=' . ($paged -1) .'&amp;portfolioID='.self::$token.'&amp;termID='.$term.'#ut-portfolio-items-' . self::$token . '-anchor"><i class="fa fa-angle-double-left"></i></a>';
			}
			
			for( $i=1 ;$i<=$portfolio_query->max_num_pages; $i++ ){
				
				$selected = ($paged == $i || ( empty($paged) && $i == 1 ) ) ? 'selected' : '';
				$gallery .= '<a href="?paged=' . $i . '&amp;portfolioID='.self::$token.'&amp;termID='.$term.'#ut-portfolio-items-' . self::$token . '-anchor" class="' . $selected . '">' . $i . '</a>';

            }
			
			if($paged < $portfolio_query->max_num_pages){
                $paged_next = $paged == 0 ? $paged + 1 : $paged;
                $gallery .= '<a href="?paged=' . ($paged_next + 1) . '&amp;portfolioID='.self::$token.'&amp;termID='.$term.'#ut-portfolio-items-' . self::$token . '-anchor"><i class="fa fa-angle-double-right"></i></a>';
            } 
			
			$gallery .= '</nav>';
		
		}

				
        /* end portfolio wrap */
        $gallery .= '</div>';
		
        /* reset query */
		wp_reset_postdata();
        
        /* return final gallery */
        return $gallery;

    
    }
        
    static function generate_gallery_packery_script( $gallery_options ) {
        
		$animation = !empty( $gallery_options['animation_out'] )  ? $gallery_options['animation_out'] : 'portfolioZoomOut';
		
        ob_start();
            
        ?>
        				        
        <script type="text/javascript">
        (function($) {

            "use strict";

            /* check if is in vc front end
            ================================================== */
            function inIframe() {

                var field = 'vc_editable';
                var url = window.location.href ;

                if( url.indexOf('?' + field + '=') !== -1 ) {

                    return true;

                } else if( url.indexOf('&' + field + '=') !== -1 ) {

                    return true;

                } else {

                    return false;

                }

            }

            if( inIframe() ) {

                $(window).trigger("load");

            }

            $(window).on("load", function(){

                $(document).ready( function(){

                var $container = $("#ut-portfolio-items-<?php echo self::$token; ?>");

                $container.on( "layoutComplete", function( event, filteredItems ){

                    $(filteredItems).each(function( index, element ){

                        if( $(window).width() <= 1024 ) {

                            $(element.element).addClass("sorted").css({
                                "opacity" : "1"
                            });

                        } else {

                            $(element.element).addClass("sorted").css({
                                <?php if( $animation == 'portfolioZoomOut' ) { ?>"opacity" : "1", "transform" : "scale(1)" <?php } ?>
                                <?php if( $animation == 'portfolioFadeOut' ) { ?>"opacity" : "1" <?php } ?>
                            });

                        }

                    });

                    $container.addClass("layoutComplete");

                    setTimeout(function() {

                        $(window).trigger('scroll');
                        $.waypoints("refresh");

                    }, 100 );

                });

                let hide_timout;

                function hide_portfolio_articles( selector ) {

                    clearTimeout( hide_timout );

                    $("> .ut-portfolio-article", "#ut-portfolio-items-<?php echo self::$token; ?>").each(function( index ){

                        if( $(window).width() <= 1024 ) {

                            $(this).addClass("ut-portfolio-article-animation").removeClass("sorted").css({
                                "opacity" : "0"
                            });

                        } else {

                            $(this).addClass("ut-portfolio-article-animation").removeClass("sorted").css({
                                <?php if( $animation == 'portfolioZoomOut' ) { ?>"opacity" : "0", "transform" : "scale(0.001)" <?php } ?>
                                <?php if( $animation == 'portfolioFadeOut' ) { ?>"opacity" : "0" <?php } ?>
                            });

                        }

                    });

                    hide_timout = setTimeout(function() {

                        $("> .ut-portfolio-article .ut-portfolio-article-animation-box", "#ut-portfolio-items-<?php echo self::$token; ?>").each(function( index ){

                            var $this = $(this);

                            $this.clearQueue().css({
                                "visibility" : "hidden",
                                "opacity" : "0"
                            }).removeClass( $this.children(".ut-portfolio-item").data("effect") );

                        });

                        $container.isotope({ filter: selector });

                    }, 400 );

                }
					
                /* IsoTope
                ================================================== */
                $container.ut_require_js({
                    plugin: 'isotope',
                    source: 'isotope',
                    callback: function (element) {

                        element.addClass("animated").isotope({

                            itemSelector : '.ut-masonry',
                            layoutMode: 'packery',
                            transitionDuration: 0

                        });

                    }

                });



                    /* IsoTope Filtering
                    ================================================== */
                    $("#ut-portfolio-menu-<?php echo self::$token; ?> a").each(function( index ) {

                        var searchforclass = $(this).attr("data-filter");

                        if( !$(searchforclass).length  ) {

                            $(this).hide(); // hide filter if we do not have any children to filter

                        }

                    });

                    $("#ut-portfolio-menu-<?php echo self::$token; ?> a").click( function( event ) {

                        var $this = $(this);

                        if ( $this.hasClass("selected") ) {

                            event.preventDefault();
                            return;

                        }

                        var selector = $this.attr("data-filter");

                        if ( !$this.hasClass("selected") ) {

                            $this.parents("#ut-portfolio-menu-<?php echo self::$token; ?>").find(".selected").removeClass("selected");
                            $this.addClass("selected");

                            hide_portfolio_articles( selector );

                        }

                        return false;

                    });


                });
					
            });
			
        }(jQuery));

        </script>        

        <?php
        
		return ob_get_clean();
    
    }
    
    static function search_tax_key( $id, $array ) {
		        
        foreach ($array as $key => $val) {

           if($val['term_id'] == $id) {
               return $key;
           }
           
        }
        
        return null;
            
    }
    	  
	static function enqueue_showcase_scripts() {
		
		if ( ! self::$add_showcase_script )  {
            return;
        }
		
        $min = NULL;

		if( apply_filters( 'ut_minify_assets', true ) ){
			$min = '.min';
		}

		wp_enqueue_script(
            'ut-effect', 
            UT_PORTFOLIO_URL . 'assets/js/ut.effects' . $min . '.js',
            array(),
            UT_PORTFOLIO_VERSION
        );
        
		wp_localize_script('ut-effect', 'utPortfolio' , array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

		
	}

    static function enqueue_react_carousel_scripts() {

        if (!self::$add_showcase_script) {
            return;
        }

        $min = NULL;

	    if( apply_filters( 'ut_minify_assets', true ) ){
		    $min = '.min';
	    }

        wp_enqueue_script(
            'ut-effect',
            UT_PORTFOLIO_URL . 'assets/js/ut.effects' . $min . '.js',
            array(),
            UT_PORTFOLIO_VERSION
        );

        wp_localize_script( 'ut-effect', 'utPortfolio' , array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'reactslider'   => true
        ) );

    }

	static function enqueue_masonry_scripts() {
		
		if ( ! self::$add_masonry_script )  {
            return;
        }
		
        $min = NULL;

		if( apply_filters( 'ut_minify_assets', true ) ){
			$min = '.min';
		}

		wp_enqueue_script(
            'ut-masonry-js',
            UT_PORTFOLIO_URL . 'assets/js/jquery.utmasonry' . $min . '.js',
            array(),
            '1.8'
        );
        
		wp_enqueue_script(
            'ut-effect',
            UT_PORTFOLIO_URL . 'assets/js/ut.effects' . $min . '.js',
            array(),
            UT_PORTFOLIO_VERSION
        );
        
		wp_localize_script( 'ut-effect', 'utPortfolio' , array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
	
	}
    
    static function enqueue_gallery_scripts() {
		
		if ( ! self::$add_gallery_script )  {
            return;
        }
		
        $min = NULL;

	    if( apply_filters( 'ut_minify_assets', true ) ){
		    $min = '.min';
	    }

	    wp_enqueue_script(
            'ut-masonry-js',
            UT_PORTFOLIO_URL . 'assets/js/jquery.utmasonry' . $min . '.js',
            array(),
            UT_PORTFOLIO_VERSION
        );
        
		wp_enqueue_script(
            'ut-effect', 
            UT_PORTFOLIO_URL . 'assets/js/ut.effects' . $min . '.js',
            array(),
            UT_PORTFOLIO_VERSION
        );
        
		wp_localize_script( 'ut-effect', 'utPortfolio' , array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        ) );		
			
	}
    
    static function enqueue_packery_scripts() {
		
		if ( ! self::$add_gallery_script )  {
            return;
        }
		
        $min = NULL;

	    if( apply_filters( 'ut_minify_assets', true ) ){
		    $min = '.min';
	    }

	    wp_enqueue_script(
            'ut-masonry-js',
            UT_PORTFOLIO_URL . 'assets/js/jquery.utmasonry' . $min . '.js',
            array(),
            UT_PORTFOLIO_VERSION
        );
        
		wp_enqueue_script(
            'ut-effect', 
            UT_PORTFOLIO_URL . 'assets/js/ut.effects' . $min . '.js',
            array(),
            UT_PORTFOLIO_VERSION
        );
        
		wp_localize_script( 'ut-effect', 'utPortfolio' , array(
		    'ajaxurl' => admin_url( 'admin-ajax.php' )
        ) );
			
	}     
    
}

ut_portfolio_shortcode::init();