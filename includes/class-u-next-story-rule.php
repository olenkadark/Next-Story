<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class U_Next_Story_Rule {

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	public $title = '';
	public $post_types = [];
	public $same_term = [];
	public $exclude = [];
	public $menu = '';
	public $submenu = 'include';
	public $loop_menu = 'off';
	public $effects_navigation = '';
	public $background_color = '';
	public $text_color = '';
	public $hover_background_color = '';
	public $hover_text_color = '';
	public $top_position = '';
	public $scroll_position = '';
	public $apply_styles = 'off';
	public $priority = 0;

	public function __construct( $data = [] ) {
		$this->base = U_Next_Story()->settings->base;
		$styles_k   = [
			'effects_navigation',
			'background_color',
			'text_color',
			'hover_background_color',
			'hover_text_color',
			'top_position',
			'scroll_position'
		];
		foreach ( $styles_k as $k ) {
			$this->$k = get_option( $this->base . $k );
		}

		foreach ( $data as $key => $val ) {
			$this->$key = $val;
		}
		foreach ( $this->same_term as $type => $val ) {
			$key = 'same_term_' . $type;
			$this->$key = $val;
		}
		foreach ( $this->exclude as $type => $val ) {
			$key = 'exclude_' . $type;
			$this->$key = $val;
		}
	}

	public function get_post_types_html() {
		$result = [];
		foreach ( $this->post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			$result[]      = $post_type_obj->labels->singular_name;
		}

		return implode( ', ', $result );
	}


}
