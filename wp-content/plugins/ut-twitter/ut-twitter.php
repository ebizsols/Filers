<?php
/*
 * Plugin Name: Twitter by United Themes 
 * Version: 3.2
 * Plugin URI: http://unitedthemes.com/
 * Description: Plugin to display simple Twitter tweets
 * Author: United Themes 
 * Author URI: http://unitedthemes.com/
 * Requires at least: 3.4
 * Tested up to: 5.3.1
 * 
 * @package WordPress
 * @author United Themes
 * @since 1.0.0
 */

define('UT_TWITTER_DIR', plugin_dir_path(__FILE__));
define('UT_TWITTER_URL', plugin_dir_url(__FILE__));
define('UT_TWITTER_VERSION', '3.2');
define('UT_TWITTER_LANG', 'ut_lang');

/*
 * required files
 */
require_once( UT_TWITTER_DIR . '/admin/admin.php' );
require_once( UT_TWITTER_DIR . '/inc/twitter.api.php' );
require_once( UT_TWITTER_DIR . '/ut.twitter.widget.php' );


/*
 * Activation, Deactivation and Uninstall Functions
 */
register_activation_hook(__FILE__, 'ut_twitter_activation');
register_deactivation_hook(__FILE__, 'ut_twitter_deactivation');

if ( ! function_exists( 'ut_twitter_activation' ) ) :
	
	function ut_twitter_activation() {
		
		//actions to perform once on plugin activation    
		add_option('ut_twitter_options');
		
		//register uninstaller
		register_uninstall_hook(__FILE__, 'ut_twitter_uninstall');
		
	}
	
endif;


if ( ! function_exists( 'ut_twitter_deactivation' ) ) :
	
	function ut_twitter_deactivation() {
		
		// actions to perform once on plugin deactivation
			
	}
	
endif;


if ( ! function_exists( 'ut_twitter_uninstall' ) ) :
	
	function ut_twitter_uninstall(){
		
		//actions to perform once on plugin uninstall
		delete_option('ut_twitter_options');
	}
	
endif;

/*
 * editor panel
 */
if ( ! function_exists( 'ut_twitter_init' ) ) : 

	function ut_twitter_init(){

		load_plugin_textdomain( 'ut_lang', false, dirname(plugin_basename(__FILE__)).'/lang/' );
		
	}
	
	ut_twitter_init();
	
endif;


/*
 * Widget functions 
 */
if ( ! function_exists( 'ut_twitterify' ) ) :
		
	function ut_twitterify($ret) {
		$ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
		$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
		$ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
		/* hashtags */
        $ret = preg_replace("/#(\w+)/", "<a href=\"https://twitter.com/search?q=%23\\1\" target=\"_blank\">#\\1</a>", $ret);
		return $ret;
	}

endif;		

if ( ! function_exists( 'ut_twitter_time_ago' ) ) :

	function ut_twitter_time_ago($oldTime, $newTime) {
		 
		$timeCalc = $newTime - $oldTime;
		
		if ( $timeCalc > (60*60*24) ) { $timeCalc = round($timeCalc/60/60/24) . __(" days ago" , 'ut_lang' ); }
		else if ( $timeCalc > (60*60) ) { $timeCalc = round($timeCalc/60/60) . __(" hours ago" , 'ut_lang' ); }
		else if ( $timeCalc > 60 ) { $timeCalc = round($timeCalc/60) . __(" minutes ago" , 'ut_lang' ); }
		else if ( $timeCalc > 0 ) { $timeCalc .= __(" seconds ago" , 'ut_lang' ); }
		
		return $timeCalc;
	}

endif;