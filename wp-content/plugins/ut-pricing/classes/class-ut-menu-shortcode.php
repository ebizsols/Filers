<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ut_menu_shortcode {

	static $token;
	static $atts;
	static $add_pricing_markup;
	
	/* init */
	static function init() {
		
		add_shortcode( 'ut_menu' , array(__CLASS__, 'handle_shortcode') );		
		
	}
	
	static function handle_shortcode( $atts ) {
		
		extract( shortcode_atts( array( "id" => '' ) , $atts ) );
		
		/* no id has been set , nothing more to do here */
		if( empty($id) ) {
			return false;
		}

		/* set token */
		self::$token = $id;		
		self::$atts = $atts;

		/* initiate shortcode markup */
		self::$add_pricing_markup = true;
		return self::create_pricing_table();
	
	}
	
	static function create_pricing_table() {

		extract( shortcode_atts( array(
			'id'                => '',
			'animate_menu_card' => 'off'
		) , self::$atts ) );

        $menu = $css = '';
        
        /* menu card styles */		
        $menu_card_style = get_post_meta( self::$token , 'ut_menu_style' , true );
        
        /* currency */
        $currency = !empty( $menu_card_style['currency'] ) ? $menu_card_style['currency'] : '';
        
        /* skin */
        $css_skin = uniqid("ut_menu_skin_");
        
        $css .= '<style type="text/css" scoped>';
            
            if( !empty ( $menu_card_style['card_title_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu-title span { color: ' . $menu_card_style['card_title_color'] . '; }';            
            }
            
            if( !empty ( $menu_card_style['card_title_background_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu-title span { background: ' . $menu_card_style['card_title_background_color'] . '; }';            
            }
            
            if( !empty ( $menu_card_style['food_title_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu h6 { color: ' . $menu_card_style['food_title_color'] . '; }';            
            }
			if( !empty ( $menu_card_style['food_title_size'] ) ) {
				$css .= '.' . $css_skin . ' .ut-table-menu h6 { font-size: ' . $menu_card_style['food_title_size'] . 'px; }';
			}
            if( !empty ( $menu_card_style['food_title_background_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu h6 { background: ' . $menu_card_style['food_title_background_color'] . '; }';            
            }
                        
            if( !empty ( $menu_card_style['decoration_line_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu .ut-table-menu-top { border-color: ' . $menu_card_style['decoration_line_color'] . '; }';            
            }
            
            if( !empty ( $menu_card_style['ingredients_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu-bottom { color: ' . $menu_card_style['ingredients_color'] . '; }';            
            }

			if( !empty ( $menu_card_style['ingredients_text_transform'] ) && $menu_card_style['ingredients_text_transform'] == 'on' ) {
				$css .= '.' . $css_skin . ' .ut-table-menu-bottom { text-transform: inherit; }';
			}
            
            if( !empty ( $menu_card_style['currency_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu em, .ut-table-menu sup { color: ' . $menu_card_style['currency_color'] . '; }';
                $css .= '.' . $css_skin . ' .ut-table-menu sup { color: ' . $menu_card_style['currency_color'] . '; }';            
            }
            if( !empty ( $menu_card_style['currency_background_color'] ) ) {
                $css .= '.' . $css_skin . ' .ut-table-menu em { background: ' . $menu_card_style['currency_background_color'] . '; }';            
            }
			if( !empty ( $menu_card_style['food_item_spacing'] ) && $menu_card_style['food_item_spacing'] == 'small' ) {
                $css .= '.' . $css_skin . ' .ut-table-menu-bottom { margin-bottom: 20px; }';            
            }
            
        $css .= '</style>';
                
		/* menu card content */
		$menu_data = get_post_meta( self::$token , 'ut_menu_data', true );

        if( $menu_data && is_array( $menu_data ) ) {
            
            /* attach CSS */
            $menu .= $css;
                
            /* global card counter */
            $card_count    = count( $menu_data );
            $odd_or_even   = $card_count % 2 == 0 ? true : false;
            $current_count = 1;
            
            foreach( $menu_data as $card ) {
                                
                $grid = ( $current_count % 2 == 0 ) ? 'ut-one-half ut-column-last' : 'ut-one-half';
                
                if( !$odd_or_even &&  $card_count == 1 ) {
                    
                    $grid = '';    
                    
                }

                $menu .= '<div class="ut-table-menu-wrap ' . $css_skin . ' ' . $grid . ' ' . ( $animate_menu_card == 'on' ? 'ut-table-menu-animated' : '' ) . '" data-appear-top-offset="almost">';
                
                    if( !empty( $card['headline'] ) ) {
                    
                        $menu .= '<h3 class="ut-table-menu-title"><span>' . $card['headline'] . '</span></h3>';
                    
                    }
                    
                    if( !empty( $card['header'] ) ) {
                    
                        $menu .= do_shortcode( $card['header'] );
                    
                    }
                    
                    $menu .= '<div class="ut-table-menu">';
                        
                        if( !empty( $card['foods'] ) && is_array( $card['foods'] ) ) {
                            
                            foreach( $card['foods'] as $food ) {
                            
                                $menu .= '<div class="ut-table-menu-top">';
                                    
                                    if( !empty( $food['title'] ) ) {
                                        
                                        $menu .= '<h6>' . $food['title'] . '</h6>';
                                            
                                    }
                                    
                                    $menu .= '<p>';
                                        
                                        if( !empty( $food['price'] ) ) {
                                            
                                            $menu .= '<em>' . $currency . '' . $food['price'] . '</em>';    
                                            
                                        }
                                    
                                    $menu .= '</p>';
                                            
                                $menu .= '</div>';
                                
                                $menu .= '<div class="ut-table-menu-bottom">';
                                    
                                    $menu .= do_shortcode( $food['ingredients'] );
                                        
                                $menu .= '</div>';
                            
                            }
                        
                        }
                        
                        if( !empty( $card['footer'] ) ) {
                    
                            $menu .= do_shortcode( $card['footer'] );
                        
                        }
                        
                    $menu .= '</div>';                    
                
                $menu .= '</div>';
                
                if( $current_count % 2 == 0 ) {
                    $menu .= '<div class="clear"></div>';    
                }
                
                $current_count++;
                $card_count--;
                
            }
            
        }   
        
		return $menu;		
	
	}

}

ut_menu_shortcode::init();