<?php
/*
 * Plugin Name: uCAT - Next Story
 * Version: 2.0.0
 * Plugin URI: http://ucat.biz/next-story/
 * Description: The lateral navigation with interesting hover effects that in some cases enhance the element, or show a preview of the content to come.
 * Author: Elena Zhyvohliad
 * Author URI: http://ucat.biz/
 * Requires at least: 4.0
 * Tested up to: 4.9
 *
 * Text Domain: u-next-story
 * Domain Path: /lang/
 *
 * @author Elena Zhyvohliad
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-u-next-story.php' );
require_once( 'includes/class-u-next-story-settings.php' );
require_once( 'includes/class-u-next-story-rule.php' );
require_once( 'includes/class-u-next-story-ajax.php' );

// Load plugin libraries
require_once( 'includes/lib/class-u-next-story-admin-api.php' );


/**
 * Returns the main instance of U_Next_Story to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object U_Next_Story
 */
function U_Next_Story () {
	$instance = U_Next_Story::instance( __FILE__, '2.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = U_Next_Story_Settings::instance( $instance );
	}

	return $instance;
}

U_Next_Story();
add_image_size( 'u_next_story-thumb', 90, 90, true );
add_image_size( 'u_next_story-62', 62, 64, true );
add_image_size( 'u_next_story-46', 46, 48, true );
add_image_size( 'u_next_story-30', 30, 32, true );
add_image_size( 'u_next_story-135', 135, 800, true );
add_image_size( 'u_next_story-130', 130, 100, true );
add_image_size( 'u_next_story-100', 100, 100, true );
add_image_size( 'u_next_story-middle', 200, 112, true );
