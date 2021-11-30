<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$GLOBALS['ut_google_maps_callback'] = array();

if( !class_exists( 'UT_Google_Maps' ) ) {
	
    class UT_Google_Maps {
        
        private $shortcode;
        private $map_id;
		private $wrap_id;
		private $callback;
		private $api_key;		
		
        function __construct() {
			
            /* shortcode base */
            $this->shortcode = 'ut_google_maps';
            
            add_action( 'init', array( $this, 'ut_map_shortcode' ) );
            add_shortcode( $this->shortcode, array( $this, 'ut_create_shortcode' ) );	
            
		}
        
        function ut_map_shortcode() {

            vc_map(
                array(
                    'name'            => esc_html__( 'Advanced Google Maps', 'ut_shortcodes' ),
                    'description'     => esc_html__( 'Requires Google Maps API Key!', 'unitedthemes' ),
                    'base'            => $this->shortcode,
                    // 'icon'            => 'fa fa-map-marker ut-vc-module-icon',
                    'icon'            => UT_SHORTCODES_URL . '/admin/img/vc_icons/google-maps.png',
                    'category'        => 'Community',
                    'class'           => 'ut-vc-icon-module ut-community-module',
                    'content_element' => true,
                    'params'          => array(

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'API Key', 'ut_shortcodes' ),
                            'description'       => sprintf( esc_html__( 'By default this module uses the API Key entered in Theme Options > Advanced > Google Maps. You can overwrite this key here. To use the Google Maps JavaScript API, you must register your app project on the Google API Console and get a Google API key which you can add to your app. Get %s now!', 'unitedthemes' ), '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key#key">Get API Key</a>' ),
                            'param_name'        => 'api_key',
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'textarea',
                            'heading'           => esc_html__( 'Address', 'ut_shortcodes' ),
                            'admin_label'       => true,
                            'param_name'        => 'content',
                            'group'             => 'General'
                        ),

                        /* array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Latitude', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional) will overwrite the address field. But needs Longitude as well.', 'unitedthemes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'lat',
                            'group'             => 'General'
                        ),

                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'Longitude', 'ut_shortcodes' ),
                            'description'       => esc_html__( '(optional) will overwrite the address field. But needs Latitude as well.', 'unitedthemes' ),
                            'edit_field_class'  => 'vc_col-sm-6',
                            'param_name'        => 'long',
                            'group'             => 'General'
                        ), */

                        // Map Styling
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Map Theme', 'ut_shortcodes' ),
                            'description'       => sprintf( esc_html__( 'Need a different theme? Browse %s to discover more styles and submit a request on our forum for a future integration.', 'unitedthemes' ), '<a target="_blank" href="https://snazzymaps.com/explore">Snazzymaps</a>' ),
                            'param_name'        => 'theme',
                            'group'             => 'Map Styling',
                            'value'             => array(
                                esc_html__( 'Default' , 'ut_shortcodes' )  => '',
                                esc_html__( 'Aubergine', 'ut_shortcodes' ) => 'aubergine',
                                esc_html__( 'Blue Light' , 'ut_shortcodes' )   => 'bluelight',
                                esc_html__( 'Dark', 'ut_shortcodes' ) 	   => 'dark',
                                esc_html__( 'Dark Electric' , 'ut_shortcodes' )   => 'darkelectric',
                                esc_html__( 'Forest' , 'ut_shortcodes' )   => 'forest',
                                esc_html__( 'Grayscale' , 'ut_shortcodes' )   => 'grayscale',
                                esc_html__( 'Light Monochrome' , 'ut_shortcodes' )   => 'lightmonochrome',
                                esc_html__( 'Night', 'ut_shortcodes' ) 	   => 'night',
                                esc_html__( 'Papuportal' , 'ut_shortcodes' )   => 'papuportal',
                                esc_html__( 'Retro', 'ut_shortcodes' ) 	   => 'retro',
                                esc_html__( 'Shades of Gray' , 'ut_shortcodes' )   => 'shadesofgray',
                                esc_html__( 'Silver' , 'ut_shortcodes' )   => 'silver',
                                esc_html__( 'Sin City' , 'ut_shortcodes' )   => 'sincity',
                                esc_html__( 'Ultra Light' , 'ut_shortcodes' )   => 'ultralight',
                                esc_html__( 'Light Purple' , 'ut_shortcodes' )   => 'lightpurple',

                            ),
                        ),

                        array(
                            'type'              => 'attach_image',
                            'heading'           => esc_html__( 'Custom Marker', 'ut_shortcodes' ),
                            'param_name'        => 'marker',
                            'group'             => 'Map Styling',
                        ),

                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Map Zoom', 'ut_shortcodes' ),
                            'description'       => esc_html__( '', 'unitedthemes' ),
                            'param_name'        => 'zoom',
                            'group'             => 'Map Styling',
                            'value'             => array(
                                'default'  	=> '12',
                                'min'   	=> '1',
                                'max'  	 	=> '20',
                                'step'  	=> '1',
                                'unit'  	=> ''
                            ),
                        ),

                        // Map Controls

                        /*array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Scale Control', 'ut_shortcodes' ),
                            'param_name'        => 'scalecontrol',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Map Styling',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'off'
                            ),
                        ),*/

                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Zoom Control', 'ut_shortcodes' ),
                            'param_name'        => 'zoomcontrol',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Map Styling',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'off'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Map Type Control', 'ut_shortcodes' ),
                            'param_name'        => 'maptypecontrol',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Map Styling',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'off'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Street View Control', 'ut_shortcodes' ),
                            'param_name'        => 'streetviewcontrol',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Map Styling',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'off'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Disable Full Screen Control', 'ut_shortcodes' ),
                            'param_name'        => 'fullscreencontrol',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Map Styling',
                            'value'             => array(
                                esc_html__( 'no, thanks!', 'ut_shortcodes' ) => '',
                                esc_html__( 'yes, please!'  , 'ut_shortcodes' ) => 'off'
                            ),
                        ),

                        // Responsive Settings
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Map Width', 'ut_shortcodes' ),
                            'param_name'        => 'map_width',
                            'group'             => 'Responsive',
                            'value'             => array(
                                'default'  	=> '100',
                                'min'   	=> '1',
                                'max'  	 	=> '100',
                                'step'  	=> '1',
                                'unit'  	=> '%'
                            ),
                        ),
                        array(
                            'type'              => 'dropdown',
                            'heading'           => esc_html__( 'Map Alignment', 'ut_shortcodes' ),
                            'param_name'        => 'map_align',
                            'group'             => 'Responsive',
                            'value'             => array(
                                esc_html__( 'center', 'ut_shortcodes' ) => 'center',
                                esc_html__( 'left'  , 'ut_shortcodes' ) => 'left',
                                esc_html__( 'right' , 'ut_shortcodes' ) => 'right',
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Map Height Desktop', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'based on Screen Height', 'unitedthemes' ),
                            'param_name'        => 'map_height',
                            'group'             => 'Responsive',
                            'value'             => array(
                                'default'  	=> '60',
                                'min'   	=> '1',
                                'max'  	 	=> '100',
                                'step'  	=> '1',
                                'unit'  	=> '%'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Map Height Tablet', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'based on Screen Height', 'unitedthemes' ),
                            'param_name'        => 'map_height_tablet',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Responsive',
                            'value'             => array(
                                'default'  	=> '60',
                                'min'   	=> '1',
                                'max'  	 	=> '100',
                                'step'  	=> '1',
                                'unit'  	=> '%'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Map Width Tablet', 'ut_shortcodes' ),
                            'param_name'        => 'map_width_tablet',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Responsive',
                            'value'             => array(
                                'default'  	=> '100',
                                'min'   	=> '1',
                                'max'  	 	=> '100',
                                'step'  	=> '1',
                                'unit'  	=> '%'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Map Height Mobile', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'based on Screen Height', 'unitedthemes' ),
                            'param_name'        => 'map_height_mobile',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Responsive',
                            'value'             => array(
                                'default'  	=> '60',
                                'min'   	=> '1',
                                'max'  	 	=> '100',
                                'step'  	=> '1',
                                'unit'  	=> '%'
                            ),
                        ),
                        array(
                            'type'              => 'range_slider',
                            'heading'           => esc_html__( 'Map Width Mobile', 'ut_shortcodes' ),
                            'param_name'        => 'map_width_mobile',
                            'edit_field_class'  => 'vc_col-sm-6',
                            'group'             => 'Responsive',
                            'value'             => array(
                                'default'  	=> '100',
                                'min'   	=> '1',
                                'max'  	 	=> '100',
                                'step'  	=> '1',
                                'unit'  	=> '%'
                            ),
                        ),

                        // CSS
                        array(
                            'type'              => 'css_editor',
                            'param_name'        => 'css',
                            'group'             => esc_html__( 'Design Options', 'ut_shortcodes' ),
                        ),
                        array(
                            'type'              => 'textfield',
                            'heading'           => esc_html__( 'CSS Class', 'ut_shortcodes' ),
                            'description'       => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'ut_shortcodes' ),
                            'param_name'        => 'class',
                            'group'             => 'General'
                        ),

                    )

                )

            ); /* end mapping */
                

        
        }
        
		function google_maps_theme( $key ) {
			
			$themes = array(
				
				'papuportal'	  => '[{"featureType":"all","elementType":"geometry","stylers":[{"visibility":"simplified"},{"hue":"#ff7700"}]},{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#1d1d1d"}]},{"featureType":"administrative.province","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"},{"visibility":"on"}]},{"featureType":"administrative.province","elementType":"labels.text.stroke","stylers":[{"color":"#ed5929"},{"weight":"5.00"},{"visibility":"on"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#787878"},{"visibility":"on"}]},{"featureType":"administrative.locality","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"},{"visibility":"on"},{"weight":"5.00"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#2d2d2d"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"},{"visibility":"on"},{"weight":"5.00"}]},{"featureType":"administrative.land_parcel","elementType":"geometry.fill","stylers":[{"saturation":"64"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#fafafa"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#2c2c2c"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#d5d5d5"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"on"},{"color":"#ff0000"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#ed5929"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"weight":"5.00"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ed5929"},{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#ed5929"}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ed5929"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#d9d9d9"},{"visibility":"on"}]},{"featureType":"transit.station","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"visibility":"simplified"},{"lightness":"4"},{"saturation":"-100"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#e1e1e1"},{"visibility":"on"}]}]',
				'forest'		  => '[{"featureType":"all","elementType":"geometry.fill","stylers":[{"weight":"2.00"}]},{"featureType":"all","elementType":"geometry.stroke","stylers":[{"color":"#9c9c9c"}]},{"featureType":"all","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#68a46d"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#cfe4cc"}]},{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"color":"#fbfbfb"}]},{"featureType":"landscape.natural.landcover","elementType":"geometry.fill","stylers":[{"color":"#d3edc1"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry.fill","stylers":[{"color":"#c6dbb8"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#e6f3d6"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#eeeeee"},{"weight":"0.5"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#c1c1c1"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffde52"},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"#ffe05e"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#ffde52"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#b0ffdd"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#e0f6f7"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#8f8f8f"}]}]',	
				'lightmonochrome' => '[{"featureType":"administrative.locality","elementType":"all","stylers":[{"hue":"#2c2e33"},{"saturation":7},{"lightness":19},{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":-2},{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#e9ebed"},{"saturation":-90},{"lightness":-8},{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":10},{"lightness":69},{"visibility":"on"}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":-78},{"lightness":67},{"visibility":"simplified"}]}]',
				'grayscale'		  => '[{"featureType":"administrative","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"saturation":-100},{"lightness":"50"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"lightness":"40"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]},{"featureType":"water","elementType":"labels","stylers":[{"lightness":-25},{"saturation":-100}]}]',
				'darkelectric'	  => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"administrative.locality","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"administrative.neighborhood","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"lightness":"17"}]},{"featureType":"administrative.land_parcel","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"landscape","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"landscape.man_made","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"landscape.natural","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#ff4700"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"invert_lightness":true},{"visibility":"off"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry.fill","stylers":[{"color":"#3b3b3b"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ff4700"},{"lightness":"39"},{"gamma":"0.43"},{"saturation":"-47"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"color":"#555555"}]},{"featureType":"road.local","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]',
				'bluelight'		  => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"gamma":"0.00"},{"lightness":"-81"},{"saturation":"-100"}]},{"featureType":"administrative","elementType":"labels.text","stylers":[{"weight":"4.00"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"},{"visibility":"on"}]},{"featureType":"administrative","elementType":"labels.icon","stylers":[{"gamma":"6.45"}]},{"featureType":"administrative.land_parcel","elementType":"labels","stylers":[{"saturation":"-13"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"saturation":"-27"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape.natural.terrain","elementType":"labels.text.fill","stylers":[{"weight":"9.81"}]},{"featureType":"landscape.natural.terrain","elementType":"labels.icon","stylers":[{"color":"#ff0000"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"geometry.stroke","stylers":[{"weight":"3.27"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"weight":"3.00"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#143e6c"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"saturation":"-100"},{"lightness":"-100"},{"color":"#354590"}]}]',
				'shadesofgray'	  => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]',
				'sincity'		  => '[{"featureType":"all","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#c4c4c4"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"color":"#707070"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21},{"visibility":"on"}]},{"featureType":"poi.business","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#be2026"},{"lightness":"0"},{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"visibility":"off"},{"hue":"#ff000a"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#575757"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.stroke","stylers":[{"color":"#2c2c2c"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#999999"}]},{"featureType":"road.local","elementType":"labels.text.stroke","stylers":[{"saturation":"-52"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]',
				'ultralight'	  => '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]',
				'silver'		  => '[{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}]',
				'retro'			  => '[{"elementType":"geometry","stylers":[{"color":"#ebe3cd"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#523735"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f1e6"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#c9b2a6"}]},{"featureType":"administrative.land_parcel","elementType":"geometry.stroke","stylers":[{"color":"#dcd2be"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#ae9e90"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#93817c"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#a5b076"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#447530"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#f5f1e6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#fdfcf8"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#f8c967"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#e9bc62"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#e98d58"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry.stroke","stylers":[{"color":"#db8555"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#806b63"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"transit.line","elementType":"labels.text.fill","stylers":[{"color":"#8f7d77"}]},{"featureType":"transit.line","elementType":"labels.text.stroke","stylers":[{"color":"#ebe3cd"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#b9d3c2"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#92998d"}]}]',
				'dark'			  => '[{"elementType":"geometry","stylers":[{"color":"#212121"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#212121"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#757575"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"administrative.land_parcel","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#181818"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"poi.park","elementType":"labels.text.stroke","stylers":[{"color":"#1b1b1b"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#2c2c2c"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#8a8a8a"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#373737"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#3c3c3c"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#3d3d3d"}]}]',
				'night'			  => '[{"elementType":"geometry","stylers":[{"color":"#242f3e"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#746855"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#242f3e"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#263c3f"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#6b9a76"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#38414e"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#212a37"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#9ca5b3"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#746855"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#1f2835"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#f3d19c"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#2f3948"}]},{"featureType":"transit.station","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#17263c"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#515c6d"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#17263c"}]}]',
				'aubergine' 	  => '[{"elementType":"geometry","stylers":[{"color":"#1d2c4d"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#8ec3b9"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#1a3646"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#64779e"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"landscape.man_made","elementType":"geometry.stroke","stylers":[{"color":"#334e87"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#023e58"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#283d6a"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#6f9ba5"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#023e58"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#3C7680"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#304a7d"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#2c6675"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#255763"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#b0d5ce"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"color":"#023e58"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#283d6a"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#3a4762"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#0e1626"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#4e6d70"}]}]',
				'lightpurple'	  => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#928ec3"}]},{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#f0f4f6"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#fcfcfc"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#fffbfb"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#dddddd"}]}]'
			
			);
			
			return isset( $themes[$key] ) ? $themes[$key] : '';
			
		}		
		
		
		function init_map( $atts ) {

            /**
             * @var $zoom
             */
			extract( shortcode_atts( array (
				
				// Base Config
				'lat' 	  		 	=> '',
				'long' 	  			=> '',
				'theme'   		 	=> '',
				'zoom'	  		 	=> 12,
				'marker'  		 	=> '',
				'disableDefaultUI'	=> '',
				
				// Advanced Controls
				'zoomcontrol'	 	=> '',
				'maptypecontrol' 	=> '',
				'scalecontrol'	 	=> '',
				'streetviewcontrol' => '',
				'rotatecontrol'		=> '',
				'fullscreencontrol' => ''
				
            ), $atts ) );			

			$this->callback =  uniqid("map_callback_");
			
			// Map Config
			$map_config = array();
			
			// Custom Map Marker
			$marker = !empty( $marker ) && is_numeric( $marker ) ? $marker = wp_get_attachment_url( $marker ) : false;
			
			// Map Theme
			if( !empty( $theme ) ) {
				$map_config['styles'] = json_decode( $this->google_maps_theme( $theme ) );
			}
			
			// Map Zoom
			$map_config['zoom'] = intval( $zoom );
			
			// Advanced Settings
			if( $maptypecontrol )
			$map_config['mapTypeControl'] = ( $maptypecontrol == 'off' );
			
			if( $zoomcontrol )
			$map_config['zoomControl'] = ( $zoomcontrol == 'off' );
			
			if( $scalecontrol )
			$map_config['scaleControl'] = ( $scalecontrol == 'off' );
			
			if( $fullscreencontrol )
			$map_config['fullscreenControl'] = ( $fullscreencontrol == 'off' );
			
			if( $streetviewcontrol )
			$map_config['streetViewControl'] = ( $streetviewcontrol == 'off' );

			if( $marker )
            $map_config['marker'] = $marker;

            return htmlentities( json_encode( $map_config ), ENT_QUOTES, 'utf-8' );
			
		}
		
		function create_css( $atts ) {
			
			extract( shortcode_atts( array (
				
				'map_width'			=> '',
				'map_height'		=> '60',
				'map_height_tablet' => '60',
				'map_width_tablet'  => '100',
				'map_height_mobile' => '60',
				'map_width_mobile'  => '100',
				
            ), $atts ) ); 
			
			ob_start() ?>
			
			<style type="text/css">
				
				<?php if( $map_height_mobile ) : ?>
				
					@media (max-width: 767px) {

						#<?php echo $this->wrap_id; ?> {
							height: <?php echo $map_height_mobile; ?>vh;
						}

					}
				
				<?php endif; ?>
				
				<?php if( $map_width_mobile ) : ?>
				
					@media (max-width: 767px) {

						#<?php echo $this->wrap_id; ?> {
							width: <?php echo $map_width_mobile; ?>%;
						}

					}
				
				<?php endif; ?>
				
				<?php if( $map_height_tablet ) : ?>
				
					@media (min-width: 768px) and (max-width: 1024px) {

						#<?php echo $this->wrap_id; ?> {
							
							height: <?php echo $map_height_tablet; ?>vh;
							
						}					

					}
				
				<?php endif; ?>
				
				<?php if( $map_width_tablet ) : ?>
				
					@media (min-width: 768px) and (max-width: 1024px) {

						#<?php echo $this->wrap_id; ?> {
							width: <?php echo $map_width_tablet; ?>%;
						}					

					}
				
				<?php endif; ?>
				
				<?php if( $map_height ) : ?>
				
					@media (min-width: 1025px) {

						#<?php echo $this->wrap_id; ?> {
							height: <?php echo $map_height; ?>vh;
						}

					}
				
				<?php endif; ?>
				
				<?php if( $map_width ) : ?>
				
					@media (min-width: 1025px) {
				
						#<?php echo $this->wrap_id; ?> {
							width: <?php echo $map_width; ?>%;
						}
				
					}
						
				<?php endif; ?>
				
			</style>		
					
			<?php return ut_minify_inline_css( ob_get_clean() );
			
		}
		
        function ut_create_shortcode( $atts, $content = NULL ) {
            
            extract( shortcode_atts( array (
				
				'api_key' 	=> ot_get_option('ut_google_maps_key'),
				'map_align' => 'center',
				'class'		=> '',
				'css'		=> ''
				
            ), $atts ) ); 
            
			if( empty( $api_key ) ) {
								
				return '<div class="ut-alert grey" style="text-align:center">' . sprintf( esc_html__( 'No Google API Key entered! To use the Google Maps JavaScript API, you must register your website project on the Google API Console and get a Google API key which you can add to your website. Get %s now!', 'unitedthemes' ), '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key#key">Get API Key</a>' ) . '</div>';
				
			}
			
			// Classes
			$classes   = array();
			$classes[] = $class;
			$classes[] = 'ut-advanced-google-map-wrap-' . $map_align;
			
			// Start Output
			$output = '';
			
			$this->map_id  = uniqid("ut_google_map_");
			$this->wrap_id = uniqid("ut_google_map_wrap");
			
			$this->api_key = $api_key;


	        $query_args = array(
		        'key' => trim($this->api_key)
	        );

			// inline script and map container
            $output .= $this->create_css( $atts );
			
			// map container
			$output .= '<div id="' . esc_attr( $this->wrap_id ) . '" class="ut-advanced-google-map-wrap ' . implode( ' ', $classes ) . '">';
				
				$output .= '<div id="' . esc_attr( $this->map_id ) . '" class="ut-advanced-google-map" data-address="' . esc_attr( preg_replace( "/\r|\n/", "", strip_tags(html_entity_decode($content) ) ) ) . '" data-api="' . esc_url( 'https://maps.googleapis.com/maps/api/js?' . http_build_query( $query_args, '', '&amp;' ) ) . '" data-settings="' . $this->init_map( $atts ) . ' "></div>';
			
			$output .= '</div>';
						
			if( defined( 'WPB_VC_VERSION' ) ) { 
                
                return '<div class="wpb_content_element ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->shortcode, $atts ) . '">' . $output . '</div>'; 
            
            }			
			
            return $output;
        
        }
            
    }

}

new UT_Google_Maps;