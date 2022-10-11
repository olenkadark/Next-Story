<?php

if( !function_exists('u_ns_get_template')) {
	/**
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 */
	function u_ns_get_template($template_name, array $args = array(), string $template_path = '', string $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = u_ns_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0.0' );

			return;
		}

		// Allow 3rd party plugin filter template file from their plugin
		$located = apply_filters( 'u_next_story_get_template', $located, $template_name, $args, $template_path,
			$default_path );

		do_action( 'u_next_story_before_template_part', $template_name, $template_path, $located, $args );

		include( $located );

		do_action( 'u_next_story_after_template_part', $template_name, $template_path, $located, $args );
	}
}
if( !function_exists('u_ns_locate_template')) {
	/**
	 * @param $template_name
	 * @param  string  $template_path
	 * @param  string  $default_path
	 *
	 * @return mixed|void
	 */
	function u_ns_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = U_NEXT_STORY_TEMPLATE_PATH;
		}

		if ( ! $default_path ) {
			$default_path = U_NEXT_STORY_PLUGIN_PATH . '/templates/';
		}

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);

		// Get default template
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters( 'u_next_story_locate_template', $template, $template_name, $template_path );
	}
}


/**
 * @param  string|array  $var
 *
 * @return string|array
 */
function u_ns_form_clean($var) {
	if ( is_array( $var ) ) {
		return array_map( 'u_ns_form_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

function u_ns_get_effects_navigation(): array
{
    return apply_filters('u_next_story_effects_navigation', array(
        'slide'        => 'Slide',
        'image_bar'    => 'Image Bar',
        'circle_pop'   => 'Circle Pop',
        'round_slide'  => 'Round Slide',
        'split'        => 'Split',
        'reveal'       => 'Reveal',
        'thumb_flip'   => 'Thumb Flip',
        'double_flip'  => 'Double Flip',
        'multi_thumb'  => 'Multi Thumb',
        'circle_slide' => 'Circle Slide',
        'grow_pop'     => 'Grow Pop',
        'diamond'      => 'Diamond',
        'fill_slide'   => 'Fill Slide',
        'fill_path'    => 'Fill Path'
    ));
}

add_image_size( 'u_next_story-thumb', 90, 90, true );
add_image_size( 'u_next_story-62', 62, 64, true );
add_image_size( 'u_next_story-46', 46, 48, true );
add_image_size( 'u_next_story-30', 30, 32, true );
add_image_size( 'u_next_story-135', 135, 800, true );
add_image_size( 'u_next_story-130', 130, 100, true );
add_image_size( 'u_next_story-100', 100, 100, true );
add_image_size( 'u_next_story-middle', 200, 112, true );
