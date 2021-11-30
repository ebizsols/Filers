<?php
/*
 * Plugin Name: Pricing Tables by United Themes
 * Version: 3.3.2
 * Plugin URI: http://unitedthemes.com
 * Description: Plugin for creating nice pricing tables
 * Author: United Themes
 * Author URI: http://unitedthemes.com
 * Requires at least: 3.0
 * Tested up to: 4.0
 *
 * @package WordPress
 * @author United Themes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/*
|--------------------------------------------------------------------------
| Basic Constants 
|--------------------------------------------------------------------------
*/

define('UT_PRICING_DIR', plugin_dir_path(__FILE__));
define('UT_PRICING_URL', plugin_dir_url(__FILE__));
define('UT_PRICING_ASSETS_URL' , UT_PRICING_URL . 'assets');
define('UT_PRICING_VERSION', '3.3.2');

/*
|--------------------------------------------------------------------------
| Include plugin class files
|--------------------------------------------------------------------------
*/

/* settings */
require_once( 'classes/class-table-template.php' );

/* post types */
require_once( 'classes/post-types/class-table-manager.php' );
require_once( 'classes/post-types/class-menu-manager.php' );

/* shortcode */
require_once( 'classes/class-ut-table-shortcode.php' );
require_once( 'classes/class-ut-menu-shortcode.php' );  

/*
|--------------------------------------------------------------------------
| Instantiate necessary classes
|--------------------------------------------------------------------------
*/

new UT_Table_Template( __FILE__ );
new UT_Table_Manager( __FILE__ ); 
new UT_Menu_Manager( __FILE__ );