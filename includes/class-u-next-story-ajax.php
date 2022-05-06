<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme AJAX.
 *
 * AJAX Event Handler.
 *
 * @class    U_AJAX
 * @version  1.0.0
 * @package  U_Next_Story/Classes
 * @category Class
 * @author   uCAT
 */
class U_Next_Story_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		self::add_ajax_events();
	}


	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		// EVENT => nopriv
		$ajax_events = array(
			'add_new_rule'                    => false,
			'edit_rule'                       => false,
			'save_rule'                       => false,
			'delete_rule'                     => false
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_u_next_story_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_u_next_story_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	public static function add_new_rule(){
        check_ajax_referer( 'u_next_story_nonce', 'security' );
	    $the_rule = (new U_Next_Story_Settings())->get_new_rule();
        $rule_id = 'u_ns_' . time();

        include "admin/views/html-edit-rule.php";
	    wp_die();
    }

    public static function edit_rule(){
        check_ajax_referer( 'u_next_story_nonce', 'security' );
        $rule_id  = $_REQUEST['rule_id'];
	    $the_rule = (new U_Next_Story_Settings())->get_rule($rule_id);
	    include "admin/views/html-edit-rule.php";
        wp_die();
    }

    public static function save_rule(){
        check_ajax_referer( 'u_next_story_nonce', 'security' );
        $rule_id  = $_REQUEST['rule_id'];
        $rules    = get_option(U_NEXT_STORY_TOKEN . '_rules', []);

        $rule     = $_REQUEST;

        unset($rule['rule_id']);
        unset($rule['security']);
        unset($rule['action']);

       $rules[$rule_id] = u_ns_form_clean($rule);

	    update_option(U_NEXT_STORY_TOKEN . '_rules', $rules, true);
	    U_Next_Story_Cache::clean_cache();
        $the_rule = new U_Next_Story_Rule($rule);
        include "admin/views/html-rule-row.php";
        wp_die();
    }

    public static function delete_rule(){
        check_ajax_referer( 'u_next_story_nonce', 'security' );
        $rule_id  = $_REQUEST['rule_id'];
        $rules    = get_option(U_NEXT_STORY_TOKEN . '_rules', []);

        unset($rules[$rule_id]);

        $i = 0; foreach ($rules as $id => &$rule){ $i++;
		    $rules[$id]['priority'] = $i;
        }
	    update_option(U_NEXT_STORY_TOKEN . '_rules', $rules);
	    U_Next_Story_Cache::clean_cache();
	    $return = array(
		    'message'  => 'OK'
	    );

	    wp_send_json($return);
    }

}

U_Next_Story_AJAX::init();
