<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Scripts.
 *
 * @class        U_Next_Story_Hooks
 * @version      1.0.0
 * @since        2.0.0
 * @package      U_Next_Story/Classes
 * @category     Class
 * @author       Elena Zhyvohliad
 */
class U_Next_Story_Hooks {
	public static function init() {
		// Add arrow navigation to footer
		add_filter( 'previous_post_link', array( __CLASS__, 'parse_post_link' ), 999, 5 );
		add_filter( 'next_post_link', array( __CLASS__, 'parse_post_link' ), 999, 5 );

		add_action( 'wp_footer', array( __CLASS__, 'display_arrow_navigation' ) );

		// Clean cache
		add_action( 'switch_theme', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'clean_taxonomy_cache', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'clean_site_cache', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'clean_object_term_cache', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'clean_term_cache', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'clean_taxonomy_cache_cache', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'clean_attachment_cache', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'update_option_u_next_story_post_types', 'U_Next_Story_Cache::clean_cache' );
		add_action( 'u_next_story_clean_cache', 'U_Next_Story_Cache::clean_cache' );

	}

	/**
	 * Filter the adjacent post link.
	 *
	 * The dynamic portion of the hook name, `$adjacent`, refers to the type
	 * of adjacency, 'next' or 'previous'.
	 *
	 *
	 * @param  string  $output  The adjacent post link.
	 * @param  string  $format  Link anchor format.
	 * @param  string  $link  Link permalink format.
	 * @param  WP_Post|null  $post  The adjacent post.
	 * @param  string  $adjacent  Whether the post is previous or next.
	 *
	 * @return false|string|string[]
	 */
	public static function parse_post_link(
		string $output,
		string $format,
		string $link,
		$post,
		string $adjacent
	) {
		$post = false;
		$rule = ( new U_Next_Story_Settings() )->find_rules( get_post() );
		if ( ! empty( $rule->post_types ) && is_array( $rule->post_types ) && is_singular( $rule->post_types ) ) {

			$result = self::get_adjacent_post_link( $format, $link, $adjacent, $rule );

			if ( ! $result ) {
				return false;
			}

			$output = $result[0];
			$post   = $result[1];
		} elseif ( ! empty( $rule->menu ) ) {
			$result = self::get_adjacent_menu_link( $format, $link, $adjacent, $rule );
			if ( ! $result ) {
				return false;
			}
			$output = $result[0];
			$post   = $result[1];
		}

		if ( ! $post ) {
			return false;
		}

		$multithumb = $thumb = '';

		if ( strpos( $output, '%multithumb' ) ) {

			$media = get_attached_media( 'image', $post->ID );
			if ( $media ) {
				$size = array( 62, 64 );
				$i    = 0;
				foreach ( $media as $im ) {
					$i ++;
					$url = self::get_attachment_image( $im->ID, $size );
					if ( $url ) {
						$multithumb .= '<img src="' . $url . '" />';
					}
					$size[0] -= 16;
					$size[1] -= 16;
					if ( $i == 3 ) {
						break;
					}
				}
			}
			$output = str_replace( '%multithumb', $multithumb, $output );

		} elseif ( strpos( $output, '%thumb130' ) ) {
			$url = self::get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 130, 100 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb130', $thumb, $output );
		} elseif ( strpos( $output, '%thumb135' ) ) {
			$url = self::get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 135, 800 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb135', $thumb, $output );
		} elseif ( strpos( $output, '%thumb100' ) ) {
			$url = self::get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 100, 100 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb100', $thumb, $output );
		} elseif ( strpos( $output, '%thumb200' ) ) {
			$url = self::get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 200, 112 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb200', $thumb, $output );
		} else {
			$url = self::get_attachment_image( get_post_thumbnail_id( $post->ID ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb', $thumb, $output );
		}


		$author = isset( $post->post_author ) ? get_the_author_meta( 'display_name', $post->post_author ) : '';
		$output = str_replace( '%author', $author, $output );

		return $output;
	}

	/**
	 *
	 * @param  string  $format
	 * @param  string  $link
	 * @param  string  $adjacent
	 * @param  U_Next_Story_Rule  $settings
	 *
	 * @return array|boolean The link URL of the previous or next post in relation to the current menu item.
	 */
	public static function get_adjacent_post_link(
		string $format,
		string $link,
		string $adjacent,
		U_Next_Story_Rule $settings
	) {
		$is_previous  = $adjacent === 'previous';
		$in_same_term = ! ! $settings->same_term;
		$taxonomy     = ! empty( $settings->same_term ) ? $settings->same_term : 'category';

		$post = get_adjacent_post( $in_same_term, $settings->exclude, $is_previous, $taxonomy );

		if ( empty( $post ) ) {
			return false;
		}

		$title = get_the_title( $post );

		if ( empty( $title ) ) {
			$title = $is_previous ? __( 'Previous Post' ) : __( 'Next Post' );
		}

		$rel = $is_previous ? 'prev' : 'next';

		$string = '<a href="' . get_permalink( $post ) . '" rel="' . $rel . '">';
		$inlink = str_replace( '%title', $title, $link );
		$inlink = str_replace( '%date', '', $inlink );
		$inlink = $string . $inlink . '</a>';

		$output = str_replace( '%link', $inlink, $format );

		return array( $output, $post );
	}

	/**
	 * @param $id
	 * @param  int[]  $size
	 *
	 * @return false|string
	 */
	public static function get_attachment_image( $id, $size = array( 90, 90 ) ) {

		$image_src = wp_get_attachment_image_src( $id, $size );
		if ( ! $image_src ) {
			return false;
		}

		if ( $image_src[1] == $size[0] && $image_src[2] == $size[1] ) {
			return $image_src[0];
		}

		$image = get_post( $id );

		if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) ) {
			return false;
		}

		$fullsizepath = get_attached_file( $image->ID );

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) {
			return false;
		}

		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY
		include_once ABSPATH . '/wp-admin/includes/image.php';

		$metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

		if ( is_wp_error( $metadata ) ) {
			return false;
		}
		if ( empty( $metadata ) ) {
			return false;
		}

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $image->ID, $metadata );

		$image_src = wp_get_attachment_image_src( $id, $size );
		if ( $image_src ) {
			return $image_src[0];
		} else {
			return false;
		}
	}


	/**
	 * Get adjacent item menu link.
	 *
	 * @param  string  $format  Link anchor format.
	 * @param  string  $link  Link permalink format.
	 * @param  bool  $adjacent  Whether to display link to previous or next post. Default next.
	 * @param  U_Next_Story_Rule  $settings  Settings
	 *
	 * @return array|boolean The link URL of the previous or next post in relation to the current menu item.
	 * @since 1.0.1
	 */
	public static function get_adjacent_menu_link(
		string $format,
		string $link,
		string $adjacent,
		U_Next_Story_Rule $settings
	) {
		$menu = $settings->menu;
		if ( ! is_numeric( $menu ) ) {
			$theme_locations = get_nav_menu_locations();
			if ( $theme_locations && isset( $theme_locations[ $menu ] ) ) {
				$menu_obj = get_term( $theme_locations[ $menu ], 'nav_menu' );
				if ( $menu_obj ) {
					$menu = $menu_obj->term_id;
				}
			}
		}

		if ( ! is_numeric( $menu ) ) {
			return false;
		}
		$previous = $adjacent === 'previous';

		$menu_items        = (array) wp_get_nav_menu_items( $menu );
		$loop_menu         = $settings->loop_menu === 'on';
		$current_menu_item = null;

		switch ( $settings->submenu ) {
			case 'exclude':
				foreach ( $menu_items as $key => $menu_item ) {
					if ( absint( $menu_item->menu_item_parent ) > 0 ) {
						unset( $menu_items[ $key ] );
						continue;
					}
				}
				break;
			case 'only_submenu':
				foreach ( $menu_items as $key => $menu_item ) {
					if ( absint( $menu_item->menu_item_parent ) === 0 ) {
						unset( $menu_items[ $key ] );
						continue;
					}
				}
				break;
		}

		$menu_items = array_values( $menu_items );

		foreach ( $menu_items as $key => $menu_item ) {
			$menu_object_id = isset( $menu_item->object_id ) ? absint( $menu_item->object_id ) : 0;
			if ( $menu_object_id === $settings->object_id ) {
				$current_menu_item = $key;
				break;
			}
		}

		if ( is_null( $current_menu_item ) ) {
			return false;
		}


		$need_key = $current_menu_item + 1;
		end( $menu_items );         // move the internal pointer to the end of the array
		$last_key = key( $menu_items );
		reset( $menu_items );

		switch ( $adjacent ) {
			case 'previous':
				$need_key = $current_menu_item - 1;
				if ( $current_menu_item === 0 && $loop_menu ) {
					$need_key = $last_key;
				}
				break;
			case 'next':
				if ( $current_menu_item === $last_key && $loop_menu ) {
					$need_key = 0;
				}
				break;
		}

		$post = isset( $menu_items[ $need_key ] ) ? $menu_items[ $need_key ] : false;

		$output = '';
		if ( $post ) {
			$title = $post->title;
			if ( ! $title ) {
				$title = get_the_title( $post->object_id );
			}

			if ( ! $title ) {
				$title = $previous ? __( 'Previous Post' ) : __( 'Next Post' );
			}


			//$date = mysql2date( get_option( 'date_format' ), $post->post_date );
			$rel = $previous ? 'prev' : 'next';

			$string = '<a href="' . $post->url . '" rel="' . $rel . '">';
			$inlink = str_replace( '%title', $title, $link );
			$inlink = str_replace( '%date', '', $inlink );
			$inlink = $string . $inlink . '</a>';

			$output = str_replace( '%link', $inlink, $format );
			if ( $post->type == 'post_type' ) {
				$post = get_post( $post->object_id );
			}
		}

		return array( $output, $post );
	}

	/**
	 * Add arrow navigation to footer
	 * @access  public
	 */
	public static function display_arrow_navigation() {
		$effects = get_option( 'u_next_story_effects_navigation', 'slide' );

		u_ns_get_template( 'arrow_icons.php' );
		u_ns_get_template( $effects . '.php' );
	}

}

U_Next_Story_Hooks::init();
