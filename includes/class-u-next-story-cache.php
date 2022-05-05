<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Cache.
 *
 * @class        U_Next_Story_Cache
 * @version      1.0.0
 * @since        2.0.0
 * @package      U_Next_Story/Classes
 * @category     Class
 * @author       Elena Zhyvohliad
 */
class U_Next_Story_Cache {

	public static function clean_cache() {
		delete_transient( U_NEXT_STORY_TOKEN . '_settings_fields' );
		delete_transient( U_NEXT_STORY_TOKEN . '_settings' );
		delete_transient( U_NEXT_STORY_TOKEN . '_options' );

		do_action(U_NEXT_STORY_TOKEN . '_cache_cleared');
	}
}
