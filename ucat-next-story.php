<?php
/*
 * Plugin Name: uCAT - Next Story
 * Version: 2.0.1
 * Plugin URI: http://ucat.tech/products/next-story/
 * Description: The lateral navigation with interesting hover effects that in some cases enhance the element, or show a preview of the content to come.
 * Author: Olena Zhyvohliad
 * Author URI: http://ucat.tech/my-story
 * Requires at least: 4.9
 * Tested up to: 6.0.2
 * Requires PHP: 7.0
 * Donate link: https://www.patreon.com/olenkadark
 *
 * Text Domain: u-next-story
 * Domain Path: /lang/
 *
 * @author Olena Zhyvohliad
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'U_NEXT_STORY_PLUGIN_FILE' ) ) {
	define( 'U_NEXT_STORY_PLUGIN_FILE', __FILE__ );
}
require_once( 'includes/class-u-next-story.php' );


/**
 * Returns the main instance of U_Next_Story to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object U_Next_Story
 */
function U_Next_Story () {
	return U_Next_Story::instance();
}

U_Next_Story();
$GLOBALS['U_Next_Story'] = U_Next_Story();
