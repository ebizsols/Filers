<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $type
 * @var $icon_fontawesome
 * @var $icon_openiconic
 * @var $icon_typicons
 * @var $icon_entypo
 * @var $icon_linecons
 * @var $color
 * @var $custom_color
 * @var $background_style
 * @var $background_color
 * @var $custom_background_color
 * @var $size
 * @var $align
 * @var $el_class
 * @var $el_id
 * @var $link
 * @var $css_animation
 * @var $css
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Icon
 */

$type = $icon_fontawesome = $icon_openiconic = $icon_typicons = $icon_entypo = $icon_linecons = $icon_linea = $icon_orion = $icon_bklyn = $color = $custom_color = $background_style = $background_color = $custom_background_color = $size = $align = $el_class = $el_id = $link = $css_animation = $css = $rel = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

// wrapper classes
$classes   = array();
$classes[] = 'vc_icon_element-align-' . esc_attr( $align );

$class_to_filter = vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$classes[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

// icon classes
$icon_classes = array();
$icon_classes[] = 'vc_icon_element-color-' .$color;
$icon_classes[] = 'vc_icon_element-size-' .$size;
$icon_classes[] = 'vc_icon_element-background-color-' .$background_color;

// enqueue needed icon font.
if( $type != 'bklyn' && $type != 'linea' && $type != 'orion' )
vc_icon_element_fonts_enqueue( $type );

// url for the icon
$url = vc_build_link( $link );







$has_style = false;

if ( strlen( $background_style ) > 0 ) {
	
	$has_style = true;
	
	if ( false !== strpos( $background_style, 'outline' ) ) {
		
		$background_style .= ' vc_icon_element-outline'; // if we use outline style it is border in css
		
	} else {
		
		$background_style .= ' vc_icon_element-background';
		
	}
	
	$classes[] 		= 'vc_icon_element-have-style';
	$icon_classes[] = 'vc_icon_element-have-style-inner';
}


$iconClass = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';










$style = '';
if ( 'custom' === $background_color ) {
	if ( false !== strpos( $background_style, 'outline' ) ) {
		$style = 'border-color:' . $custom_background_color;
	} else {
		$style = 'background-color:' . $custom_background_color;
	}
}
$style = $style ? ' style="' . esc_attr( $style ) . '"' : '';
$rel = '';
if ( ! empty( $url['rel'] ) ) {
	$rel = ' rel="' . esc_attr( $url['rel'] ) . '"';
}
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
} ?>

<div <?php echo implode( ' ', $wrapper_attributes ); ?>	class="vc_icon_element vc_icon_element-outer <?php echo implode( ' ', $classes ); ?>">

	<div class="vc_icon_element-inner vc_icon_element-style-<?php echo esc_attr( $background_style ); ?> <?php echo implode( ' ', $icon_classes ); ?>" <?php echo $style ?>>
		
		<?php if( $type == 'linea' || $type == 'orion' ) :

            if( !empty( $atts['custom_color'] ) ) {

	            $atts['svg_color']   = $atts['custom_color'];
	            $atts['svg_color_2'] = $atts['custom_color'];

            }

			if( !empty( $atts['color'] ) && $atts['color'] != 'custom' ) {

				$atts['svg_color']   = $atts['color'];
				$atts['svg_color_2'] = $atts['color'];

			} ?>
		
			<span class="vc_icon_element-icon <?php echo $iconClass; ?>" <?php echo( 'custom' === $color ? 'style="color:' . esc_attr( $custom_color ) . ' !important"' : '' ); ?>>
			
				<?php 
				
				if( class_exists("UT_Draw_SVG") ) {

					// draw svg
					$svg = new UT_Draw_SVG( uniqid("ut-svg-"), $atts );
					echo $svg->draw_svg_icon();
				
				}
					
				?>
		
			</span>
		
		<?php else : ?>
		
			<span class="vc_icon_element-icon <?php echo $iconClass; ?>" <?php echo( 'custom' === $color ? 'style="color:' . esc_attr( $custom_color ) . ' !important"' : '' ); ?>></span>
		
		<?php endif; ?>
		
		<?php if ( strlen( $link ) > 0 && strlen( $url['url'] ) > 0 ) {
			echo '<a class="vc_icon_element-link" href="' . esc_attr( $url['url'] ) . '" ' . $rel . ' title="' . esc_attr( $url['title'] ) . '" target="' . ( strlen( $url['target'] ) > 0 ? esc_attr( $url['target'] ) : '_self' ) . '"></a>';
		} ?>
		
	</div>
	
</div>
