<?php


class UT_Glitch_Effect {


    /**
     * Unique Glitch ID
     */

    private $unique_id;


    /**
     * Attributes
     */

    public $atts;


    /**
     * Images
     */

    public $images = array();


    /**
     * Image Background Positions
     */

    public $background_positions = array();


    /**
     * Classes
     */

    public $classes;


    /**
     * Current Image ID
     */

    public $image_id;


    /**
     * Instantiates the class
     *
     * @param array $atts
     */

    function __construct( $atts ) {

        $this->atts = $atts;

        if( empty( $this->atts ) ) {

            return;

        }

        /**
         * @var string $glitch_effect
         * @var string $glitch_type
         * @var string $permanent_glitch
         * @var string $image_desktop
         * @var string $image_tablet
         * @var string $image_mobile
         * @var string $accent_1
         * @var string $accent_2
         * @var string $accent_3
         */

        extract( shortcode_atts( array(

            'image_desktop'             => '',
            'image_tablet'              => '',
            'image_mobile'              => '',
            'image_desktop_position'    => '',
            'image_tablet_position'     => '',
            'image_mobile_position'     => ''

        ), $this->atts ) );

        // Main Desktop Image
        $this->set_image( 'desktop', $image_desktop );

        // Responsive Desktop Image Position
        $image_desktop_position  = !empty( $image_desktop_position ) ? $image_desktop_position : '';
        $this->set_image_position( 'desktop', $image_desktop_position );

        // Responsive Tablet Image
        $image_tablet  = !empty( $image_tablet ) ? $image_tablet : $image_desktop;
        $this->set_image( 'tablet', $image_tablet );

        // Responsive Tablet Image Position
        $image_tablet_position  = !empty( $image_tablet_position ) ? $image_tablet_position : '';
        $this->set_image_position( 'tablet', $image_tablet_position );

        // Responsive Mobile Image
        $image_mobile  = !empty( $image_mobile ) ? $image_mobile : $image_tablet;
        $this->set_image( 'mobile', $image_mobile );

        // Responsive Mobile Image Position
        $image_mobile_position  = !empty( $image_mobile_position ) ? $image_mobile_position : '';
        $this->set_image_position( 'mobile', $image_mobile_position );

    }


    /**
     * Inline Color CSS
     *
     * @param string $effect
     * @param array $colors
     */

    public function get_inline_color_css( $effect, $colors = array() ) {

        $glitch_colors = array();

        if( !empty( $colors ) ) {

            /* Colors */
            $accent_to_effect = array(
                'equal'    => array( 3, 5 ),
                'gifted'   => array( 3 ),
                'past'     => array( 2, 3, 4 ),
                'ground'   => array( 4, 5 ),
                'wide'     => array( 3 ),
                'haunted'  => array( 5 ),
                'ethereal' => array( 4, 5 )
            );

            if( isset( $accent_to_effect[$effect] ) ) {

                foreach( $colors as $color_key => $color ) {

                    if( empty( $color ) ) {
                        continue;
                    }

                    $glitch_colors[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element:nth-child(' . $accent_to_effect[$effect][$color_key] . ') {
				        background-color: ' . $color . ' !important;
                        background-blend-mode: ' . $color . ' !important;
				    }';

                }

            }

            echo implode( "\n", $glitch_colors );

        }

    }


    /**
     * Inline Background CSS
     *
     * @param array $background_settings
     * @param boolean $lozad
     *
     * @return string
     */

    public function create_css_background( $background_settings, $lozad ) {

        $final_css = array();

        foreach( $background_settings as $key => $value) {

            switch( $key ) {

                case 'background-image' :

                    if( !$lozad )
                        $final_css[] = $key . ':' . 'url("' . esc_url( $value ) . '");';

                    break;

                case 'background-position':
                case 'background-attachment':
                case 'background-repeat':
                case 'background-size' :

                    if( !empty( $value ) )
                        $final_css[] = $key . ':' . $value . ' !important;';

                    break;

            }

        }

        return !empty( $final_css ) ? implode( " ", $final_css ) : '';

    }


    /**
     * Inline Background CSS
     *
     * @param boolean $lozad
     */

    public function get_inline_background_css( $lozad = false ) {

        // Glitch Effect Images
        $desktop_queries = array();
        $tablet_queries  = array();
        $mobile_queries  = array();

        foreach( $this->images as $key => $image ) :

            // Desktop Default Image
            if( isset( $image['desktop'] ) && !$lozad ) {

                if( is_array( $image['desktop'] ) ) {

                    $desktop_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                        ' . $this->create_css_background( $image['desktop'], $lozad ) .  '                        
                    }';

                } else {

                    $desktop_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                        background-image: url(' . esc_url( $image['desktop'] ) . ');                        
                    }';

                }

            } elseif( isset( $image['desktop'] ) && is_array( $image['desktop'] ) ) {

                $desktop_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                    ' . $this->create_css_background( $image['desktop'], $lozad ) .  '                        
                }';

            }

            // Tablet Default Image
            if( isset( $image['tablet'] ) ) {

                if( is_array( $image['tablet'] ) ) {

                    $tablet_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                        ' . $this->create_css_background( $image['tablet'], $lozad ) .  '                        
                    }';

                } else {

                    $tablet_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                        background-image: url(' . esc_url( $image['tablet'] ) . ');                        
                    }';

                }

            } elseif( isset( $image['tablet'] ) && is_array( $image['tablet'] ) ) {

                $tablet_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                    ' . $this->create_css_background( $image['tablet'], $lozad ) .  '                        
                }';

            }

            // Mobile Default Image
            if( isset( $image['mobile'] ) ) {

                if( is_array( $image['mobile'] ) ) {

                    $mobile_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                        ' . $this->create_css_background( $image['mobile'], $lozad ) .  '                        
                    }';

                } else {

                    $mobile_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                        background-image: url(' . esc_url( $image['mobile'] ) . ');                        
                    }';

                }

            } elseif( isset( $image['mobile'] ) && is_array( $image['mobile'] ) ) {

                $mobile_queries[] = '#ut-element-glitch-' . $this->unique_id . '-' . $this->image_id . ' .ut-glitch-single-element {
                    ' . $this->create_css_background( $image['mobile'], $lozad ) .  '                        
                }';

            }


        endforeach; ?>

        @media (min-width: 1025px) {

        <?php echo implode( "\n", $desktop_queries ); ?>

        }

        @media (min-width: 768px) and (max-width: 1024px) {

        <?php echo implode( "\n", $tablet_queries ); ?>

        }

        @media (max-width: 767px) {

        <?php echo implode( "\n", $mobile_queries ); ?>

        }

        <?php

    }


    public function inline_css( $effect, $colors = array(), $lozad = false ) { ?>

        <style type="text/css">

            <?php $this->get_inline_color_css( $effect, $colors ); ?>
            <?php $this->get_inline_background_css( $lozad ); ?>

        </style>

        <?php

    }


    /**
     * @param string $device
     * @param string $image
     */

    public function set_image( $device, $image ) {

        $this->images[$this->image_id][$device]['background-image'] = $image;

    }

    /**
     * @param string $device
     * @param string $position
     */

    public function set_image_position( $device, $position ) {

        $this->images[$this->image_id][$device]['background-position'] = $position;

    }


    /**
     * @param number $image_id
     */

    public function set_image_id( $image_id ) {

        $this->image_id = $image_id;

    }



    /**
     * @param mixed $class
     */

    public function setClass( $class ) {

        $this->classes[] = $class;

    }

    /**
     * HTML Advanced Glitch Text
     *
     * @param string $text
     * @param string $tag
     *
     * @return string
     */

    public function render_inner_text( $text, $tag ) {

        $inner_text = "<$tag class=\"ut-advanced-glitch-text-inner\">";

        $inner_text .= "<span data-text=\"$text\"></span>";

        $inner_text .= "</$tag>";

        return $inner_text;

    }

    /**
     * HTML Advanced Glitch Text
     */

    public function wrap_glitch_text() {



    }


    /**
     * HTML Advanced Glitch Text
     *
     * @param string $text
     * @param string $tag
     *
     * @return string
     */

    public function render_text( $text, $tag = 'h1' ) {

        ob_start(); ?>

        <div class="">

            <?php echo $this->render_inner_text( $text, $tag ); ?>

        </div>

        <?php

        return ob_get_clean();

    }


    /**
     * HTML Markup
     */

    public function render() {

        /**
         * @var number $image_id
         * @var string $glitch_effect
         * @var string $glitch_type
         * @var string $glitch_shape
         * @var string $permanent_glitch
         * @var string $glitch_transparent
         * @var string $glitch_effect_transparent
         * @var mixed $image_desktop
         * @var string $image_desktop_position
         * @var mixed $image_tablet
         * @var string $image_tablet_position
         * @var mixed $image_mobile
         * @var string $image_mobile_position
         * @var string $accent_1
         * @var string $accent_2
         * @var string $accent_3
         * @var boolean $skip_first_layer
         * @var boolean $inline_css
         * @var string $class
         */

        extract( shortcode_atts( array(
            'custom_id'                 => '',
            'image_id'                  => '',
            'glitch_effect'             => '',
            'glitch_effect_transparent' => '',
            'glitch_shape'              => '',
            'glitch_transparent'        => '',
            'permanent_glitch'          => 'on',
            'image_desktop'             => '',
            'image_desktop_position'    => '',
            'image_tablet'              => '',
            'image_tablet_position'     => '',
            'image_mobile'              => '',
            'image_mobile_position'     => '',
            'accent_1'                  => '',
            'accent_2'                  => '',
            'accent_3'                  => '',
            'inline_css'                => true,
            'skip_first_layer'          => false,
            'lozad'                     => '',
            'class'                     => ''
        ), $this->atts ) );

        // No Glitch Effect
        if( empty( $glitch_effect ) && empty( $glitch_transparent ) ) {
            return false;
        }

        // Glitch ID
        $this->unique_id = !empty( $custom_id ) ? $custom_id : ut_get_unique_id( '', true );

        // Image ID
        $this->set_image_id( $image_id );

        // Glitch Effect Classes
        $this->setClass( $class );
        $this->setClass( 'ut-element-glitch' );
        $this->setClass( 'ut-glitch-' . esc_attr( $glitch_effect ) );

        // permanent glitch classes
        if( isset( $permanent_glitch ) && ( $permanent_glitch == 1 || $permanent_glitch == 'on' ) ) {

            $this->setClass( 'ut-element-glitch-permanent' );

        }

        // transparent image
        if( $glitch_transparent ) {

            $this->setClass( 'ut-element-glitch-transparent' );

            $this->setClass( 'ut-simple-glitch-text-permanent' );
            $this->setClass( 'ut-simple-glitch-text-' . $glitch_effect_transparent . '-permanent' );

        }

        // glitch shape
        if( $glitch_shape ) {
            $this->setClass( 'ut-' . $glitch_shape );
        }

        // Glitch Colors
        $colors = array( $accent_1, $accent_2, $accent_3 );

        // Lazy Load
        $lozad = !empty( $lozad ) ? 'ut-lozad skip-lazy' : false;

        ob_start(); ?>

        <div class="ut-element-glitch-wrap">

            <?php

            if( $inline_css ) {

                $this->inline_css( $glitch_effect, $colors, $lozad );

            }

            $image_desktop = is_array( $image_desktop ) && isset( $image_desktop['background-image'] ) ? $image_desktop['background-image'] : $image_desktop; ?>

            <div id="ut-element-glitch-<?php echo $this->unique_id; ?>-<?php echo $this->image_id; ?>" class="<?php echo implode( " ", $this->classes ); ?>">
                <div class="ut-glitch-single-element <?php echo $skip_first_layer ? 'ut-glitch-single-element-hide' : ''; ?><?php echo $lozad; ?>" data-background-image="<?php echo esc_url( $image_desktop ); ?>"></div>
                <div class="ut-glitch-single-element <?php echo $lozad; ?>" data-background-image="<?php echo esc_url( $image_desktop ); ?>"></div>
                <div class="ut-glitch-single-element <?php echo $lozad; ?>" data-background-image="<?php echo esc_url( $image_desktop ); ?>"></div>
                <div class="ut-glitch-single-element <?php echo $lozad; ?>" data-background-image="<?php echo esc_url( $image_desktop ); ?>"></div>
                <div class="ut-glitch-single-element <?php echo $lozad; ?>" data-background-image="<?php echo esc_url( $image_desktop ); ?>"></div>
            </div>

        </div>

        <?php

        return ob_get_clean();

    }

}