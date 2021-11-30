<?php if (!defined('ABSPATH')) {
    exit; // exit if accessed directly
}

/**
 * Orion Icon Font
 * @return array
 * since 4.7.1
 */

function ut_iconpicker_type_orionicons( $icons ) {
		
	$orion_icons = array(
		array( "icon-orion-photos" => "orion-photos" ),
		array( "icon-orion-cart-settings" => "orion-cart-settings" ),
		array( "icon-orion-smartphone-1" => "orion-smartphone-1" ),
		array( "icon-orion-umbrella" => "orion-umbrella" ),
		array( "icon-orion-upload-to-cloud" => "orion-upload-to-cloud" ),
		array( "icon-orion-paper-stack" => "orion-paper-stack" ),
		array( "icon-orion-menu" => "orion-menu" ),
		array( "icon-orion-sync-files" => "orion-sync-files" ),
		array( "icon-orion-missile" => "orion-missile" ),
		array( "icon-orion-play" => "orion-play" ),
		array( "icon-orion-wifi" => "orion-wifi" ),
		array( "icon-orion-storage-box" => "orion-storage-box" ),
		array( "icon-orion-ssl-ecommerce" => "orion-ssl-ecommerce" ),
		array( "icon-orion-truck" => "orion-truck" ),
		array( "icon-orion-messaging" => "orion-messaging" ),
		array( "icon-orion-responsive" => "orion-responsive" ),
		array( "icon-orion-undo" => "orion-undo" ),
		array( "icon-orion-stack" => "orion-stack" ),
		array( "icon-orion-pinterest" => "orion-pinterest" ),
		array( "icon-orion-star" => "orion-star" ),
		array( "icon-orion-presentation" => "orion-presentation" ),
		array( "icon-orion-router" => "orion-router" ),
		array( "icon-orion-pictures" => "orion-pictures" ),
		array( "icon-orion-multiple-windows" => "orion-multiple-windows" ),
		array( "icon-orion-map-marker" => "orion-map-marker" ),
		array( "icon-orion-pin" => "orion-pin" ),
		array( "icon-orion-remote-control" => "orion-remote-control" ),
		array( "icon-orion-stopwatch" => "orion-stopwatch" ),
		array( "icon-orion-minus" => "orion-minus" ),
		array( "icon-orion-shield" => "orion-shield" ),
		array( "icon-orion-restore-window" => "orion-restore-window" ),
		array( "icon-orion-reset-window" => "orion-reset-window" ),
		array( "icon-orion-server" => "orion-server" ),
		array( "icon-orion-network" => "orion-network" ),
		array( "icon-orion-text-message" => "orion-text-message" ),
		array( "icon-orion-tv-screen" => "orion-tv-screen" ),
		array( "icon-orion-wireless" => "orion-wireless" ),
		array( "icon-orion-smartphone" => "orion-smartphone" ),
		array( "icon-orion-settings-database" => "orion-settings-database" ),
		array( "icon-orion-software-service" => "orion-software-service" ),
		array( "icon-orion-settings" => "orion-settings" ),
		array( "icon-orion-angle-down" => "orion-angle-down" ),
		array( "icon-orion-behance" => "orion-behance" ),
		array( "icon-orion-cutlery" => "orion-cutlery" ),
		array( "icon-orion-data-content" => "orion-data-content" ),
		array( "icon-orion-burst-mode" => "orion-burst-mode" ),
		array( "icon-orion-hide-document" => "orion-hide-document" ),
		array( "icon-orion-champion" => "orion-champion" ),
		array( "icon-orion-landline" => "orion-landline" ),
		array( "icon-orion-open-box" => "orion-open-box" ),
		array( "icon-orion-schedule-database" => "orion-schedule-database" ),
		array( "icon-orion-mouse-1" => "orion-mouse-1" ),
		array( "icon-orion-portfolio-grid" => "orion-portfolio-grid" ),
		array( "icon-orion-photo-gallery" => "orion-photo-gallery" ),
		//array( "icon-orion-angle" => "orion-angle" ),
		array( "icon-orion-coins" => "orion-coins" ),
		array( "icon-orion-items-list" => "orion-items-list" ),
		array( "icon-orion-connect" => "orion-connect" ),
		array( "icon-orion-image" => "orion-image" ),
		array( "icon-orion-linkedin" => "orion-linkedin" ),
		array( "icon-orion-map" => "orion-map" ),
		array( "icon-orion-equalizer" => "orion-equalizer" ),
		array( "icon-orion-in-love" => "orion-in-love" ),
		array( "icon-orion-download-cloud" => "orion-download-cloud" ),
		array( "icon-orion-facebook" => "orion-facebook" ),
		array( "icon-orion-puzzle-2" => "orion-puzzle-2" ),
		array( "icon-orion-settings-mixer" => "orion-settings-mixer" ),
		array( "icon-orion-mouse-2" => "orion-mouse-2" ),
		array( "icon-orion-paint-bucket" => "orion-paint-bucket" ),
		array( "icon-orion-bluetooth" => "orion-bluetooth" ),
		array( "icon-orion-imac-screen" => "orion-imac-screen" ),
		array( "icon-orion-flame" => "orion-flame" ),
		array( "icon-orion-focus" => "orion-focus" ),
		array( "icon-orion-camera-shutter" => "orion-camera-shutter" ),
		array( "icon-orion-cd" => "orion-cd" ),
		array( "icon-orion-document-stack" => "orion-document-stack" ),
		array( "icon-orion-download-1" => "orion-download-1" ),
		array( "icon-orion-amazon" => "orion-amazon" ),
		array( "icon-orion-twitter" => "orion-twitter" ),
		array( "icon-orion-mouse-3" => "orion-mouse-3" ),
		array( "icon-orion-pie-chart" => "orion-pie-chart" ),
		array( "icon-orion-vintage-camera" => "orion-vintage-camera" ),
		array( "icon-orion-image-gallery" => "orion-image-gallery" ),
		array( "icon-orion-instagram" => "orion-instagram" ),
		array( "icon-orion-flickr" => "orion-flickr" ),
		array( "icon-orion-iphone" => "orion-iphone" ),
		array( "icon-orion-archive-box" => "orion-archive-box" ),
		array( "icon-orion-add" => "orion-add" ),
		array( "icon-orion-cart" => "orion-cart" ),
		array( "icon-orion-color-wheel" => "orion-color-wheel" ),
		array( "icon-orion-paypal" => "orion-paypal" ),
		array( "icon-orion-reload-database" => "orion-reload-database" ),
		array( "icon-orion-menu-hamburger" => "orion-menu-hamburger" ),
		array( "icon-orion-vimeo" => "orion-vimeo" ),
		array( "icon-orion-tv-display" => "orion-tv-display" ),
		array( "icon-orion-sailing" => "orion-sailing" ),
		array( "icon-orion-play-movie" => "orion-play-movie" ),
		array( "icon-orion-share" => "orion-share" ),
		array( "icon-orion-sliders" => "orion-sliders" ),
		array( "icon-orion-vk" => "orion-vk" ),
		array( "icon-orion-sedan" => "orion-sedan" ),
		array( "icon-orion-skype" => "orion-skype" ),
		array( "icon-orion-set-square" => "orion-set-square" ),
		array( "icon-orion-github" => "orion-github" ),
		array( "icon-orion-full-screen" => "orion-full-screen" ),
		array( "icon-orion-dribbble" => "orion-dribbble" ),
		array( "icon-orion-files" => "orion-files" ),
		array( "icon-orion-direction-arrow-gps" => "orion-direction-arrow-gps" ),
		array( "icon-orion-computer-network" => "orion-computer-network" ),
		array( "icon-orion-data-app" => "orion-data-app" ),
		array( "icon-orion-ipad" => "orion-ipad" ),
		array( "icon-orion-apple" => "orion-apple" ),
		array( "icon-orion-camera" => "orion-camera" ),
		array( "icon-orion-compact-disc" => "orion-compact-disc" ),
		array( "icon-orion-control" => "orion-control" ),
		array( "icon-orion-luxury" => "orion-luxury" ),
		array( "icon-orion-crown" => "orion-crown" ),
		array( "icon-orion-lifebelt" => "orion-lifebelt" ),
		array( "icon-orion-headphones" => "orion-headphones" ),
		array( "icon-orion-clouds" => "orion-clouds" ),
		array( "icon-orion-hamburger" => "orion-hamburger" ),
		array( "icon-orion-database" => "orion-database" ),
		array( "icon-orion-donut" => "orion-donut" ),
		array( "icon-orion-documents" => "orion-documents" ),
		array( "icon-orion-brush" => "orion-brush" ),
		array( "icon-orion-statistics" => "orion-statistics" ),
		array( "icon-orion-multiple-files" => "orion-multiple-files" ),
		array( "icon-orion-cinema-reel" => "orion-cinema-reel" ),
		array( "icon-orion-file-cabinet" => "orion-file-cabinet" ),
		array( "icon-orion-sorting" => "orion-sorting" ),
		array( "icon-orion-images" => "orion-images" ),
		array( "icon-orion-hand-truck" => "orion-hand-truck" ),
		array( "icon-orion-wifi-router" => "orion-wifi-router" ),
		array( "icon-orion-grid-layout" => "orion-grid-layout" ),
		array( "icon-orion-hourglass" => "orion-hourglass" ),
		array( "icon-orion-google-drive" => "orion-google-drive" ),
		array( "icon-orion-shopping-trolley" => "orion-shopping-trolley" ),
		array( "icon-orion-twitch" => "orion-twitch" ),
		array( "icon-orion-lock-window" => "orion-lock-window" ),
		array( "icon-orion-airplane-mode" => "orion-airplane-mode" ),
		array( "icon-orion-hot-coffee" => "orion-hot-coffee" ),
		array( "icon-orion-keyboard" => "orion-keyboard" ),
		array( "icon-orion-search-glass" => "orion-search-glass" ),
		array( "icon-orion-wind-turbine" => "orion-wind-turbine" ),
		array( "icon-orion-angle-right" => "orion-angle-right" ),
		array( "icon-orion-delivery-truck" => "orion-delivery-truck" ),
		array( "icon-orion-angle-left" => "orion-angle-left" ),
		array( "icon-orion-hide-layer" => "orion-hide-layer" ),
		array( "icon-orion-upload" => "orion-upload" ),
		array( "icon-orion-shopping-bag" => "orion-shopping-bag" ),
		array( "icon-orion-envelope" => "orion-envelope" ),
		array( "icon-orion-display" => "orion-display" ),
		array( "icon-orion-download-2" => "orion-download-2" ),
		array( "icon-orion-instagram-camera" => "orion-instagram-camera" ),
		array( "icon-orion-signal-bars" => "orion-signal-bars" )	
	);
	
	return array_merge( $icons, $orion_icons );
		
}

add_filter( 'vc_iconpicker-type-orionicons', 'ut_iconpicker_type_orionicons' );

function ut_orion_icon_register_css() {
	
	wp_register_style( 
        'ut-orion-icons', 
        UT_SHORTCODES_URL . 'css/orionicons/orion.css'
    );
	
}

add_action( 'vc_base_register_admin_css', 'ut_orion_icon_register_css' );

function ut_enqueue_orion_icon_font() {

	wp_enqueue_style( 'ut-orion-icons' );

}

add_action( 'vc_backend_editor_enqueue_js_css', 'ut_enqueue_orion_icon_font' );
add_action( 'vc_frontend_editor_enqueue_js_css', 'ut_enqueue_orion_icon_font' );
add_action( 'vc_enqueue_font_icon_element', 'ut_enqueue_orion_icon_font' );