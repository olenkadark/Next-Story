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
 * @package  U_Theme/Classes
 * @category Class
 * @author   uCAT
 */
class U_AJAX {

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
		// bslm_EVENT => nopriv
		$ajax_events = array(
			'product_import'                           => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_utheme_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_utheme_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	public static function product_import(){
        global $wpdb;

        check_ajax_referer( 'utheme-product-import', 'security' );

        if ( ! current_user_can( 'edit_posts' ) || ! isset( $_POST['file'] ) ) {
            wp_die( -1 );
        }

        #include_once( WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php' );
        #include_once( WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php' );

        $file = isset( $_POST['file'] ) ? get_attached_file( $_POST['file'] ) : '';
        $params = array(
            'delimiter'       => ! empty( $_POST['delimiter'] ) ? u_clean( $_POST['delimiter'] ) : ',',
            'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0,
            'mapping'         => isset( $_POST['mapping'] ) ? (array) $_POST['mapping'] : array(),
            'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false,
            'lines'           => 30,
            'parse'           => true,
        );

        // Log failures.
        if ( 0 !== $params['start_pos'] ) {
            $error_log = array_filter( (array) get_user_option( 'utheme_product_import_error_log' ) );
        } else {
            $error_log = array();
        }

        $importer         = WC_Product_CSV_Importer_Controller::get_importer( $file, $params );
        $results          = $importer->import();
        $percent_complete = $importer->get_percent_complete();
        $error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

        update_user_option( get_current_user_id(), 'utheme_product_import_error_log', $error_log );

        if ( 100 === $percent_complete ) {
            // Clear temp meta.
            $wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
            $wpdb->query( "
				DELETE {$wpdb->posts}, {$wpdb->postmeta}, {$wpdb->term_relationships}
				FROM {$wpdb->posts}
				LEFT JOIN {$wpdb->term_relationships} ON ( {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id )
				LEFT JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
				LEFT JOIN {$wpdb->term_taxonomy} ON ( {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id )
				LEFT JOIN {$wpdb->terms} ON ( {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id )
				WHERE {$wpdb->posts}.post_type IN ( 'strategy' )
				AND {$wpdb->posts}.post_status = 'importing'
			" );

            // Send success.
            wp_send_json_success( array(
                'position'   => 'done',
                'percentage' => 100,
                'url'        => add_query_arg( array( 'nonce' => wp_create_nonce( 'product-csv' ) ), admin_url( 'admin.php?import=u-strategies&step=done' ) ),
                'imported'   => count( $results['imported'] ),
                'failed'     => count( $results['failed'] ),
                'updated'    => count( $results['updated'] ),
                'skipped'    => count( $results['skipped'] ),
            ) );
        } else {
            wp_send_json_success( array(
                'position'   => $importer->get_file_position(),
                'percentage' => $percent_complete,
                'imported'   => count( $results['imported'] ),
                'failed'     => count( $results['failed'] ),
                'updated'    => count( $results['updated'] ),
                'skipped'    => count( $results['skipped'] ),
            ) );
        }
    }
	
}

U_AJAX::init();
