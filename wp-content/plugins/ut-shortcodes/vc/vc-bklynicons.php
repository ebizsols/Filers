<?php if (!defined('ABSPATH')) {
    exit; // exit if accessed directly
}

/**
 * Brooklyn Icon Font
 * @return array
 * since 4.5
 */

if( !function_exists('ut_vc_register_brooklyn_icon_font') ) {
    
    function ut_vc_register_brooklyn_icon_font( $icons ) {
        
        $bklyn_icons = array( array("BklynIcons-Right-6"=>"Right-6"),
            array("BklynIcons-Gallery-1"=>"Gallery-1"),
            array("BklynIcons-by4-Hour"=>"by4-Hour"),
            array("BklynIcons-Addon"=>"Addon"),
            array("BklynIcons-Addon-Setting"=>"Addon-Setting"),
            array("BklynIcons-Angry-Birds"=>"Angry-Birds"),
            array("BklynIcons-Application"=>"Application"),
            array("BklynIcons-Atom"=>"Atom"),
            array("BklynIcons-Bad-3"=>"Bad-3"),
            array("BklynIcons-Blend-Tool"=>"Blend-Tool"),
            array("BklynIcons-Block-Chart-2"=>"Block-Chart-2"),
            array("BklynIcons-Box-1"=>"Box-1"),
            array("BklynIcons-Busy-1"=>"Busy-1"),
            array("BklynIcons-Camera-Rear"=>"Camera-Rear"),
            array("BklynIcons-Cash-Pay"=>"Cash-Pay"),
            array("BklynIcons-Circle"=>"Circle"),
            array("BklynIcons-Clean-Code"=>"Clean-Code"),
            array("BklynIcons-Clipboard-Write"=>"Clipboard-Write"),
            array("BklynIcons-Clock-1"=>"Clock-1"),
            array("BklynIcons-Cloud-Database"=>"Cloud-Database"),
            array("BklynIcons-Cloud-Server-1"=>"Cloud-Server-1"),
            array("BklynIcons-Compas-Rose"=>"Compas-Rose"),
            array("BklynIcons-Compass-2"=>"Compass-2"),
            array("BklynIcons-Computer-Network-2"=>"Computer-Network-2"),
            array("BklynIcons-Computer-Sync"=>"Computer-Sync"),
            array("BklynIcons-Cube-3"=>"Cube-3"),
            array("BklynIcons-Cup-2"=>"Cup-2"),
            array("BklynIcons-Delivery"=>"Delivery"),
            array("BklynIcons-Diamond"=>"Diamond"),
            array("BklynIcons-Digital-Design"=>"Digital-Design"),
            array("BklynIcons-Disabled-man"=>"Disabled-man"),
            array("BklynIcons-Distance-1"=>"Distance-1"),
            array("BklynIcons-Down-2"=>"Down-2"),
            array("BklynIcons-Down-3"=>"Down-3"),
            array("BklynIcons-Download"=>"Download"),
            array("BklynIcons-Enlarge"=>"Enlarge"),
            array("BklynIcons-Ethernet"=>"Ethernet"),
            array("BklynIcons-Financial-Care-2"=>"Financial-Care-2"),
            array("BklynIcons-First-ad"=>"First-ad"),
            array("BklynIcons-Flying-Rocket"=>"Flying-Rocket"),
            array("BklynIcons-Free-Tag-2"=>"Free-Tag-2"),
            array("BklynIcons-French-Fries"=>"French-Fries"),
            array("BklynIcons-Gas-Pump"=>"Gas-Pump"),
            array("BklynIcons-Gear-1"=>"Gear-1"),
            array("BklynIcons-Hamburger"=>"Hamburger"),
            array("BklynIcons-Hand-cargo"=>"Hand-cargo"),
            array("BklynIcons-Handshake"=>"Handshake"),
            array("BklynIcons-Hat-3"=>"Hat-3"),
            array("BklynIcons-Hearts-Empty"=>"Hearts-Empty"),
            array("BklynIcons-Hotspot-Mobile"=>"Hotspot-Mobile"),
            array("BklynIcons-Increasing-Chart-1"=>"Increasing-Chart-1"),
            array("BklynIcons-Lamp-2"=>"Lamp-2"),
            array("BklynIcons-Lamp-3"=>"Lamp-3"),
            array("BklynIcons-Laptop-1"=>"Laptop-1"),
            array("BklynIcons-Left-2"=>"Left-2"),
            array("BklynIcons-Left-3"=>"Left-3"),
            array("BklynIcons-Lens-1"=>"Lens-1"),
            array("BklynIcons-Light-Bulb"=>"Light-Bulb"),
            array("BklynIcons-Line-Chart-1"=>"Line-Chart-1"),
            array("BklynIcons-Map-Pin-2"=>"Map-Pin-2"),
            array("BklynIcons-Map-pin-6"=>"Map-pin-6"),
            array("BklynIcons-Maximize-3"=>"Maximize-3"),
            array("BklynIcons-Medic"=>"Medic"),
            array("BklynIcons-Minimize-1"=>"Minimize-1"),
            array("BklynIcons-Monitor-1"=>"Monitor-1"),
            array("BklynIcons-Mustache-1"=>"Mustache-1"),
            array("BklynIcons-Navigation-1"=>"Navigation-1"),
            array("BklynIcons-Office-Chair"=>"Office-Chair"),
            array("BklynIcons-Office-Desk-2"=>"Office-Desk-2"),
            array("BklynIcons-Paint-Bucket"=>"Paint-Bucket"),
            array("BklynIcons-Paper-Clip-3"=>"Paper-Clip-3"),
            array("BklynIcons-Party-Glasses"=>"Party-Glasses"),
            array("BklynIcons-Pen-Holder"=>"Pen-Holder"),
            array("BklynIcons-Pie-Chart-1"=>"Pie-Chart-1"),
            array("BklynIcons-Pin"=>"Pin"),
            array("BklynIcons-Pizza-Slice"=>"Pizza-Slice"),
            array("BklynIcons-Plugin"=>"Plugin"),
            array("BklynIcons-Pokemon"=>"Pokemon"),
            array("BklynIcons-Reduce"=>"Reduce"),
            array("BklynIcons-Responsive-Design"=>"Responsive-Design"),
            array("BklynIcons-Right-2"=>"Right-2"),
            array("BklynIcons-Right-3"=>"Right-3"),
            array("BklynIcons-Rocket-Launch"=>"Rocket-Launch"),
            array("BklynIcons-Rotate-2"=>"Rotate-2"),
            array("BklynIcons-Ruler-Tool"=>"Ruler-Tool"),
            array("BklynIcons-Sailboat"=>"Sailboat"),
            array("BklynIcons-Sandwich"=>"Sandwich"),
            array("BklynIcons-Saturn"=>"Saturn"),
            array("BklynIcons-Scale-Tool"=>"Scale-Tool"),
            array("BklynIcons-Screen-Rotation"=>"Screen-Rotation"),
            array("BklynIcons-Search"=>"Search"),
            array("BklynIcons-Selection-Tool"=>"Selection-Tool"),
            array("BklynIcons-Share-File-1"=>"Share-File-1"),
            array("BklynIcons-Shoe-2"=>"Shoe-2"),
            array("BklynIcons-Smart-Devices"=>"Smart-Devices"),
            array("BklynIcons-Smartphone"=>"Smartphone"),
            array("BklynIcons-Smartwatch-EKG-1"=>"Smartwatch-EKG-1"),
            array("BklynIcons-Stormtrooper-2"=>"Stormtrooper-2"),
            array("BklynIcons-Tablet-1"=>"Tablet-1"),
            array("BklynIcons-Telescope"=>"Telescope"),
            array("BklynIcons-Tempometer"=>"Tempometer"),
            array("BklynIcons-Test-Flusk-1"=>"Test-Flusk-1"),
            array("BklynIcons-Text-box"=>"Text-box"),
            array("BklynIcons-Theme"=>"Theme"),
            array("BklynIcons-Umbrella"=>"Umbrella"),
            array("BklynIcons-Up-2"=>"Up-2"),
            array("BklynIcons-Up-3"=>"Up-3"),
            array("BklynIcons-Upload"=>"Upload"),
            array("BklynIcons-Waiting-room"=>"Waiting-room"),
            array("BklynIcons-Worms-Armagedon"=>"Worms-Armagedon"),
            );
        
        return array_merge( $icons, $bklyn_icons );
        
    }
    
    add_filter( 'vc_iconpicker-type-bklynicons', 'ut_vc_register_brooklyn_icon_font' );

}


function ut_brooklyn_icon_register_css() {
    
    wp_register_style( 
        'ut-bklynicons', 
        UT_SHORTCODES_URL . 'css/bklynicons/bklynicons.css'
    );

}

add_action( 'vc_base_register_admin_css', 'ut_brooklyn_icon_register_css' );
add_action( 'vc_base_register_front_css', 'ut_brooklyn_icon_register_css' );


if( !function_exists('ut_enqueue_brooklyn_icon_font') ) {
    
    function ut_enqueue_brooklyn_icon_font( $icons ) {
        
        wp_enqueue_style( 'ut-bklynicons' );
        
    }

    add_action( 'vc_backend_editor_enqueue_js_css', 'ut_enqueue_brooklyn_icon_font' );
    add_action( 'vc_frontend_editor_enqueue_js_css', 'ut_enqueue_brooklyn_icon_font' );
    add_action( 'vc_enqueue_font_icon_element', 'ut_enqueue_brooklyn_icon_font' );

}
