<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $height
 * @var $el_class
 * @var $el_id
 * @var $css
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Empty_space
 */
$height = $tablet_height = $mobile_height = $el_class = $el_id = $css = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$classes = array();

if ( empty( $el_id ) ) {

    $el_id = uniqid("ut-empty-space-");

}

// Mobile Settings
$tablet_height = !empty( $tablet_height ) || $tablet_height == '0' ? $tablet_height : $height;
$mobile_height = !empty( $mobile_height ) || $mobile_height == '0' ? $mobile_height : $tablet_height;

// allowed metrics: http://www.w3schools.com/cssref/css_units.asp
$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';

// Desktop Height
$regexr = preg_match( $pattern, $height, $matches );
$value = isset( $matches[1] ) ? (float) $matches[1] : (float) WPBMap::getParam( 'vc_empty_space', 'height' );
$unit = isset( $matches[2] ) ? $matches[2] : 'px';
$height = $value . $unit;

// Tablet Height

$regexr = preg_match( $pattern, $tablet_height, $matches );
$value = isset( $matches[1] ) ? (float) $matches[1] : (float) WPBMap::getParam( 'vc_empty_space', 'tablet_height' );
$unit = isset( $matches[2] ) ? $matches[2] : 'px';
$tablet_height = $value . $unit;

// Mobile Height
$regexr = preg_match( $pattern, $mobile_height, $matches );
$value = isset( $matches[1] ) ? (float) $matches[1] : (float) WPBMap::getParam( 'vc_empty_space', 'mobile_height' );
$unit = isset( $matches[2] ) ? $matches[2] : 'px';
$mobile_height = $value . $unit;

// CSS Classes
$class     = 'vc_empty_space ' . $this->getExtraClass( $el_class ) . vc_shortcode_custom_css_class( $css, ' ' );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );

$classes[] = $css_class;

// Custom CSS
$wrapper_attributes = array();
$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"'; ?>

<style type="text/css">

    <?php if( (float) $height > 0.0 ) : ?>

        #<?php echo $el_id; ?> {
            height: <?php echo esc_attr( $height ); ?>
        }

    <?php else : ?>

        #<?php echo $el_id; ?> {
            display: none;
        }

    <?php endif; ?>

    <?php if( (float) $tablet_height > 0.0 ) : ?>
        @media (min-width: 768px) and (max-width: 1200px) {
            #<?php echo $el_id; ?> {
                height: <?php echo esc_attr( $tablet_height ); ?>;
                display: block;
            }
        }
    <?php else : ?>

        @media (min-width: 768px) and (max-width: 1200px) {
            #<?php echo $el_id; ?> {
                display: none;
            }
        }

    <?php endif; ?>

    <?php if( (float) $mobile_height > 0.0 ) : ?>

        @media (max-width: 767px) {
            #<?php echo $el_id; ?> {
                height: <?php echo esc_attr( $mobile_height ); ?>;
                display: block;
            }
        }

    <?php else : ?>

        @media (max-width: 767px) {
            #<?php echo $el_id; ?> {
                display: none;
            }
        }

    <?php endif; ?>

</style>

<div class="<?php echo esc_attr( trim( $css_class ) ); ?>" <?php echo implode( ' ', $wrapper_attributes ); ?>><span class="vc_empty_space_inner"></span></div>