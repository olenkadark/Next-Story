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

	public ?int $object_id = null;
	public string $title = '';
	public array $post_types = [];
	public array $same_term = [];
	public array $exclude = [];
	public string $effects_navigation = 'slide';
	public string $background_color = '';
	public string $text_color = '';
	public string $hover_background_color = '';
	public string $hover_text_color = '';
	public string $top_position = '';
	public string $scroll_position = '';
	public string $scroll_position_unit = 'px';
	public string $apply_styles = 'off';
	public int $priority = 0;

    public array $_additional_values = [];

	public function __construct( $data = [] ) {

        foreach ( $data as $key => $val ) {
            $this->$key = $val;
		}
		if( $this->same_term){
			foreach ( $this->same_term as $type => $val ) {
				$key = 'same_term_' . $type;
				$this->$key = $val;
			}
		}

		if( $this->exclude ){
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
            if( $post_type_obj )
			    $result[] = $post_type_obj->labels->singular_name;
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
            $taxonomy_obj = get_taxonomy($tax);
            if( $taxonomy_obj )
			    $result[] = $taxonomy_obj->labels->name;
		}

		return implode( ', ', $result );
	}


}
