<?php


/**
 * Class to Draw SVG Inline
 *
 * @author     UnitedThemes <contact@unitedthemes.com>
 */

class UT_Draw_SVG {
	
	/**
	 * The ID of this SVG.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	
	private $id;
	
	
	/**
	 * The Attributes of this SVG.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array
	 */
	
	private $atts;
	
	
	/**
	 * Hover Support
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string 
	 */
	
	private $hover;
	
	
	/**
	 * Switch for Draw Animation JS File
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array
	 */
	
	private $add_script;
	
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $id      The ID of this SVG.
	 * @param    string    $atts    The Attributes of this SVG.
	 */
	
	function __construct( $id, $atts, $hover = false ) {
		
		$this->id    = $id;
		$this->atts  = $atts;
		$this->hover = $hover;

	}


	/**
	 * The JavaScript for running the draw animation
	 *
	 * @since    1.0.0
	 */

	function draw_settings_json() {

		/**
		 * @var string $draw_svg_icons
		 * @var string $draw_svg_type
		 * @var string $timer
		 * @var string $draw_svg_delay
		 * @var string $draw_svg_duration
		 */

		extract( shortcode_atts( array (

			// SVG
			'draw_svg_icons'	=> 'yes',
			'draw_svg_type'		=> 'oneByOne',
			'draw_svg_delay'    => '0',
			'draw_svg_duration' => '100',

		), $this->atts ) );

		$json = array(

			// SVG
			'draw_svg_icons'	=> $draw_svg_icons,
			'draw_svg_type'		=> $draw_svg_type,
			'draw_svg_delay'    => $draw_svg_delay,
			'draw_svg_duration' => $draw_svg_duration,

		);

		return htmlentities( json_encode( $json ), ENT_QUOTES, 'utf-8' );

	}

	/**
	 * The CSS Styling for this SVG
	 *
	 * @since    1.0.0
	 */	
	
	public function draw_svg_css() {
		
		extract( shortcode_atts( array (

			// SVG 
			'draw_svg_icons'	=> 'yes',
			'svg_color'			=> '',
			'svg_color_2'		=> '',
			'svg_color_hover'	=> '',
			'svg_color_2_hover'	=> ''

		), $this->atts ) );
		
		$svg_color = !empty( $svg_color ) ? $svg_color : get_option('ut_accentcolor' , '#F1C40F');
		
		ob_start(); ?>
			
			<style type="text/css">
				
				<?php 
				
				// required for draw animation
				if( $draw_svg_icons == 'yes' ) : ?>
				
					#<?php echo $this->id; ?> { display:none; }
					#<?php echo $this->id; ?>.ut-svg-loaded { display:block; }
				
				<?php endif; ?>

				#<?php echo $this->id; ?> path { stroke: <?php echo $svg_color; ?>; }
				#<?php echo $this->id; ?> line { stroke: <?php echo $svg_color; ?>; }
				#<?php echo $this->id; ?> circle { stroke: <?php echo $svg_color; ?>; }
				#<?php echo $this->id; ?> rect { stroke: <?php echo $svg_color; ?>; }
				#<?php echo $this->id; ?> ellipse { stroke: <?php echo $svg_color; ?>; }
				#<?php echo $this->id; ?> polyline { stroke: <?php echo $svg_color; ?>; }
				#<?php echo $this->id; ?> polygon { stroke: <?php echo $svg_color; ?>; }
				
				#<?php echo $this->id; ?> [data-name="layer1"] { stroke: <?php echo $svg_color; ?>; }
				#<?php echo $this->id; ?> [data-name="layer1"] path { stroke: <?php echo $svg_color; ?>; }
				
				<?php if( !empty( $svg_color_2 ) ) : ?> 
				
					#<?php echo $this->id; ?> [data-name="layer2"] { stroke: <?php echo $svg_color_2; ?>; }
					#<?php echo $this->id; ?> [data-name="layer2"] path { stroke: <?php echo $svg_color_2; ?>; }
				
				<?php endif; ?>
								
				<?php if( $this->hover ) : ?> 
					
					<?php if( !empty( $svg_color_hover ) ) : ?> 
					
						#<?php echo $this->hover; ?>.active path { stroke: <?php echo $svg_color_hover; ?>; }
						#<?php echo $this->hover; ?>.active line { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>.active circle { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>.active rect { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>.active ellipse { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>.active polyline { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>.active polygon { stroke: <?php echo $svg_color; ?>; }				
						
						#<?php echo $this->hover; ?>.active [data-name="layer1"] { stroke: <?php echo $svg_color_hover; ?>; }
						#<?php echo $this->hover; ?>.active [data-name="layer1"] path { stroke: <?php echo $svg_color_hover; ?>; }
				
						#<?php echo $this->hover; ?>:hover path { stroke: <?php echo $svg_color_hover; ?>; }
						#<?php echo $this->hover; ?>:hover line { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>:hover circle { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>:hover rect { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>:hover ellipse { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>:hover polyline { stroke: <?php echo $svg_color; ?>; }
						#<?php echo $this->hover; ?>:hover polygon { stroke: <?php echo $svg_color; ?>; }	
						
						#<?php echo $this->hover; ?>:hover [data-name="layer1"] { stroke: <?php echo $svg_color_hover; ?>; }
						#<?php echo $this->hover; ?>:hover [data-name="layer1"] path { stroke: <?php echo $svg_color_hover; ?>; }
				
					<?php endif; ?>
				
					<?php if( !empty( $svg_color_2_hover ) ) : ?> 
				
						#<?php echo $this->hover; ?>.active [data-name="layer2"] { stroke: <?php echo $svg_color_2_hover; ?>; }
						#<?php echo $this->hover; ?>.active [data-name="layer2"] path { stroke: <?php echo $svg_color_2_hover; ?>; }
				
						#<?php echo $this->hover; ?>:hover [data-name="layer2"] { stroke: <?php echo $svg_color_2_hover; ?>; }
						#<?php echo $this->hover; ?>:hover [data-name="layer2"] path { stroke: <?php echo $svg_color_2_hover; ?>; }
					
					<?php endif; ?>
				
				<?php endif; ?>				

			</style>
		
		<?php
		
		return ob_get_clean();
		
	}
	
	
	/**
	 * Linea Icon
	 *
	 * @since    1.0.0
	 */	
	
	public function linea_icon( $svg_icon ){
		
		$icon_path = '';
		
		if( strpos( $svg_icon, 'icon-basic-') !== false && strpos( $svg_icon, 'icon-basic-elaboration-') === false ) {
			$icon_path = 'lineaicons/basic';
		}
		
		if( strpos( $svg_icon, 'icon-basic-elaboration-') !== false ) {
			$icon_path = 'lineaicons/basic-elaboration';
		}
		
		if( strpos( $svg_icon, 'icon-ecommerce-') !== false ) {
			$icon_path = 'lineaicons/ecommerce';
		}
		
		if( strpos( $svg_icon, 'icon-music-') !== false ) {
			$icon_path = 'lineaicons/music';
		}
		
		if( strpos( $svg_icon, 'icon-software-') !== false ) {
			$icon_path = 'lineaicons/software';
		}
		
		if( strpos( $svg_icon, 'icon-weather-') !== false ) {
			$icon_path = 'lineaicons/weather';
		}
		
		if( strpos( $svg_icon, 'icon-arrows-') !== false ) {
			$icon_path = 'lineaicons/arrows';
		}
		
		// clean up icon file
		$svg_icon = str_replace( '-', '_', $svg_icon );
		$svg_icon = str_replace( 'icon_', '', $svg_icon );
		
		// get SVG content	
		$svg_string = @file_get_contents( UT_SHORTCODES_DIR . 'css/' . $icon_path . '/svg/' . $svg_icon . '.svg' );
		
		if( $svg_string ) {
			
			// inject SVG ID
            $svg_string = str_replace( "Layer_1", $this->id, $svg_string );
			$svg_string = str_replace( 'id="' . $this->id . '"', 'class="ut-vivus-draw" data-settings="' . $this->draw_settings_json() . '" id="' . $this->id . '"', $svg_string );

			// remove html comments
			$svg_string = preg_replace('/<!--(.*)-->/Uis', '', $svg_string );
			
			// remove html doctype
			$svg_string = preg_replace('~<(?:!DOCTYPE|/?(?:xml))[^>]*>\s*~i', '', $svg_string );
			
			// remove xml
			$svg_string = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $svg_string );
			
			return $svg_string;
			
		} else {
			
			return false;
			
		}
		
	}
	
	
	/**
	 * Orion Icon
	 *
	 * @since    1.0.0
	 */	
	
	public function orion_icon( $svg_icon ) {
		
		$svg_icon   = str_replace( 'icon-', '', $svg_icon );
		$svg_string = @file_get_contents( UT_SHORTCODES_DIR . 'css/orionicons/svg/' . $svg_icon . '.svg' );
		
		if( $svg_string  ) {
			
			// inject SVG ID and Class
			$svg_string = str_replace( "<svg", '<svg class="ut-vivus-draw" data-settings="' . $this->draw_settings_json() . '" id="' . $this->id . '"', $svg_string );
			
			// remove html comments
			$svg_string = preg_replace('/<!--(.*)-->/Uis', '', $svg_string);
			$svg_string = preg_replace("#\<title(.*)/title>#iUs", "", $svg_string);
			$svg_string = preg_replace("#\<desc(.*)/desc>#iUs", "", $svg_string);
						
			return $svg_string;
			
		} else {
			
			return false;
			
		}
		
	}	
	
	
	public function draw_svg_icon() {
		
		extract( shortcode_atts( array (
			
			'type'				=> '', // VC Icon Module
			'icon_type'			=> '', // United Themes Modules
			'icon_source'		=> '', // United Themes Tabs
			'icon_linea'		=> '',
			'icon_orion'		=> '',
		
		), $this->atts ) );
		
		$output = '';		
		
		if( $icon_type == 'lineaicons' || $type == 'linea' || $icon_source == 'lineaicons' ) {
			
			if( $this->linea_icon( $icon_linea ) ) {

				// attach css
				$output .= $this->draw_svg_css();
				
				// draw icon 
				$output .= $this->linea_icon( $icon_linea );
			
			}
			
		}
		
		if( $icon_type == 'orionicons' || $type == 'orion' || $icon_source == 'orionicons' ) {
			
			if( $this->orion_icon( $icon_orion ) ) {

				// attach css
				$output .= $this->draw_svg_css();
				
				// draw icon 
				$output .= $this->orion_icon( $icon_orion );
			
			}
			
		}
		
		return $output;
		

	}
	
}