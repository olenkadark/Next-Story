<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class U_Next_Story_Settings
 *
 * @property array $post_types
 * @property array $same_term
 * @property array $exclude
 * @property string $menu
 * @property string $submenu
 * @property string $loop_menu
 * @property string $effects_navigation
 * @property string $background_color
 * @property string $text_color
 * @property string $hover_background_color
 * @property string $hover_text_color
 * @property string $top_position
 * @property string $scroll_position
 * @property string $scroll_position_unit
 * @property array $rules
 */
class U_Next_Story_Settings {

	/**
	 * @var string
	 */
	private $base = '';

	/**
	 * @var array
	 */
	public $rules = [];

	public function __construct() {
		$this->base = U_NEXT_STORY_TOKEN . '_';

		$this->init();
	}

	private function init() {
		$options = $this->get_options();
		foreach ( $options as $key => $options_value ) {
			$this->$key = $options_value;
		}

		unset( $options['rules'] );
		$this->rules = array_map( function ( $rule ) use ( $options ) {
			//$rule = array_merge( $options, $rule );
			return new U_Next_Story_Rule( $rule );
		}, $this->rules );
	}

	/**
	 * @param  bool  $with_rules
	 * @param  bool  $only_styles
	 *
	 * @return array
	 */
		public function get_options( bool $with_rules = true, bool $only_styles = false ): array {
		$options = get_transient( $this->base . 'options' );
		if ( ! $options ) {
			$defaults = [
				'post_types'             => [],
				'same_term'              => [],
				'exclude'                => [],
				'menu'                   => '',
				'submenu'                => 'include',
				'loop_menu'              => 'off',
				'effects_navigation'     => 'slide',
				'background_color'       => '',
				'text_color'             => '',
				'hover_background_color' => '',
				'hover_text_color'       => '',
				'top_position'           => 50,
				'scroll_position'        => 0,
				'scroll_position_unit'   => 'px',
				'rules'                  => []
			];

			foreach ( $defaults as $key => $default_value ) {
				$options[ $key ] = get_option( $this->base . $key, $default_value );
			}
			set_transient( $this->base . 'options', $options );
		}

		if ( !$with_rules || $only_styles ) {
			 unset($options['rules']);
		}
		if ( $only_styles ) {
			 unset($options['post_types']);
			 unset($options['same_term']);
			 unset($options['exclude']);
			 unset($options['menu']);
			 unset($options['submenu']);
			 unset($options['loop_menu']);
		}

		return $options;
	}

	/**
	 * @param  WP_Post  $post
	 *
	 * @return U_Next_Story_Rule
	 */
	public function find_rules( WP_Post $post ): U_Next_Story_Rule {
		$rules = wp_cache_get( $post->ID, $this->base . 'post_rules' );
		if ( ! $rules ) {
			foreach ( $this->rules as $rule ) {
				if ( ! empty( $rule->post_types ) && is_array( $rule->post_types ) && is_singular( $rule->post_types ) ) {
					$rules = $rule;
					break;
				}
			}
			if ( ! $rules ) {
				$rules = new U_Next_Story_Rule( $this->get_options( false ) );
			}
			$rules->same_term = isset( $rules->same_term[ $post->post_type ] ) ? $rules->same_term[ $post->post_type ] : '';
			$exclude          = [];
			if ( $rules->exclude ) {
				$taxonomy_objects = array_keys( get_object_taxonomies( $post->post_type, 'objects' ) );
				foreach ( $rules->exclude as $tax => $terms ) {
					if ( ! in_array( $tax, $taxonomy_objects ) ) {
						continue;
					}
					$exclude = array_merge( $exclude, $terms );
				}
			}
			$rules->exclude   = array_map( 'absint', $exclude );
			$rules->object_id = $post->ID;
			wp_cache_set( $post->ID, $rules, $this->base . 'post_rules' );
		}

		return apply_filters( $this->base . 'post_rules', $rules, $post );
	}

	/**
	 * @return U_Next_Story_Rule
	 */
	public function get_new_rule(): U_Next_Story_Rule {
		$defaults       = $this->get_options( false, true);
		$rule           = new U_Next_Story_Rule( $defaults );
		$rule->priority = count( $this->rules ) + 1;

		return $rule;
	}

	/**
	 * @param  string  $rule_id
	 *
	 * @return U_Next_Story_Rule
	 */
	public function get_rule( string $rule_id ): U_Next_Story_Rule {
		if ( isset( $this->rules[ $rule_id ] ) ) {
			return $this->rules[ $rule_id ];
		}

		return $this->get_new_rule();
	}
}
