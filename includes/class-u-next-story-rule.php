<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class U_Next_Story_Rule
 *
 * @property string $menu
 * @property string $loop_menu
 * @property string $submenu
 */
class U_Next_Story_Rule {

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $object_id = null;
	public $title = '';
	public $post_types = [];
	public $same_term = [];
	public $exclude = [];
	public $effects_navigation = 'slide';
	public $background_color = '';
	public $text_color = '';
	public $hover_background_color = '';
	public $hover_text_color = '';
	public $top_position = '';
	public $scroll_position = '';
	public $apply_styles = 'off';
	public $priority = 0;

	public function __construct( $data = [] ) {
		foreach ( $data as $key => $val ) {
			$this->$key = $val;
		}
		if( is_array($this->same_term )){
			foreach ( $this->same_term as $type => $val ) {
				$key = 'same_term_' . $type;
				$this->$key = $val;
			}
		}

		if( is_array($this->exclude) ){
			foreach ( $this->exclude as $type => $val ) {
				$key = 'exclude_' . $type;
				$this->$key = $val;
			}
		}

	}

	/**
	 * @return string
	 */
	public function get_post_types_html(): string {
		$result = [];
		foreach ( $this->post_types as $post_type ) {
			if( empty($post_type)) continue;
			$post_type_obj = get_post_type_object( $post_type );
			$result[]      = $post_type_obj->labels->singular_name;
		}

		return implode( ', ', $result );
	}

	/**
	 * @return string
	 */
	public function get_same_term_html(): string {
		$result = [];
		foreach ( $this->same_term as $tax ) {
			if( empty($tax)) continue;
			$result[]   = get_taxonomy($tax)->labels->name;
		}

		return implode( ', ', $result );
	}


}
