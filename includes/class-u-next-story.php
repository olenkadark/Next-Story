<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class U_Next_Story {

	/**
	 * The single instance of U_Next_Story.
	 * @var    object
	 * @access   private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'u_next_story';

		// Load plugin environment variables
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->script_suffix = '';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new U_Next_Story_Admin_API();
		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Add arrow navigation to footer
		add_filter( 'previous_post_link', array( $this, 'parse_post_link' ), 999, 5 );
		add_filter( 'next_post_link', array( $this, 'parse_post_link' ), 999, 5 );

		add_action( 'wp_footer', array( $this, 'display_arrow_navigation' ) );
	} // End __construct ()


	/**
	 * Load frontend CSS.
	 * @access  public
	 * @return void
	 * @since   1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(),
			$this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );

		$bg_color     = get_option( 'u_next_story_background_color', '#ffffff' );
		$tx_color     = get_option( 'u_next_story_text_color', '#34495e' );
		$h_bg_color   = get_option( 'u_next_story_hover_background_color', '#34495e' );
		$h_tx_color   = get_option( 'u_next_story_hover_text_color', '#ffffff' );
		$top_position = get_option( 'u_next_story_top_position', '50' );


		$rgb_bg_color = $this->hex2rgb( $bg_color );
		$rgb_bg_color = implode( ',', $rgb_bg_color );

		$rgb_h_tx_color = $this->hex2rgb( $h_tx_color );
		$rgb_h_tx_color = implode( ',', $rgb_h_tx_color );

		$custom_css = "
        		.u_next_story.nav-reveal div{
					background-color: rgba({$rgb_bg_color},0.6);
				}
				.u_next_story.nav-growpop div p{
					color: rgba({$rgb_h_tx_color},0.6);
				}
				.u_next_story.nav-fillpath a::before,
				.u_next_story.nav-fillpath a::after,
				.u_next_story.nav-fillpath .icon-wrap::before,
				.u_next_story.nav-fillpath .icon-wrap::after,

				.u_next_story.nav-fillslide .icon-wrap,
				.u_next_story.nav-diamond > div div,
				.u_next_story.nav-growpop .icon-wrap,
				.u_next_story.nav-circleslide > div div,
				.u_next_story.nav-doubleflip h3,
				.u_next_story.nav-doubleflip .icon-wrap,
				.u_next_story.nav-thumbflip .icon-wrap,
    			.u_next_story.nav-split .icon-wrap,
        		.u_next_story.nav-roundslide > div,
                .u_next_story.nav-slide .icon-wrap{
                    background-color: {$bg_color};
                }
                .u_next_story.nav-growpop .icon-wrap{
                	border-color: {$bg_color};
                }
                .u_next_story.nav-diamond svg.icon{
                	 stroke: {$tx_color};
                }
                .u_next_story.nav-fillslide svg.icon,
                .u_next_story.nav-diamond svg.icon,
                .u_next_story.nav-growpop svg.icon,
                .u_next_story.nav-circleslide svg.icon,
                .u_next_story.nav-multithumb svg.icon,
                .u_next_story.nav-doubleflip svg.icon,
                .u_next_story.nav-thumbflip svg.icon,
                .u_next_story.nav-reveal svg.icon,
                .u_next_story.nav-split svg.icon,
                .u_next_story.nav-roundslide svg.icon,
                .u_next_story.nav-imgbar svg.icon,
                .u_next_story.nav-slide svg.icon{
                    fill: {$tx_color};
                }
                .u_next_story.nav-diamond > div:hover svg.icon{
                	 stroke: {$h_tx_color};
                }
                .u_next_story.nav-fillslide > div:hover svg.icon,
                .u_next_story.nav-diamond > div:hover svg.icon,
                .u_next_story.nav-circleslide > div:hover svg.icon,
                .u_next_story.nav-doubleflip > div:hover svg.icon,
                .u_next_story.nav-thumbflip > div:hover svg.icon,
                .u_next_story.nav-reveal > div:hover svg.icon,
                .u_next_story.nav-roundslide > div:hover svg.icon{
                	fill: {$h_tx_color};
                }

                .u_next_story.nav-fillpath .icon-wrap::before,
				.u_next_story.nav-fillpath .icon-wrap::after,

                .u_next_story.nav-fillslide > div div,
                .u_next_story.nav-fillslide .icon-wrap:before,
                .u_next_story.nav-growpop > div div,
                .u_next_story.nav-split h3,
                .u_next_story.nav-doubleflip > div:hover .icon-wrap,
                .u_next_story.nav-thumbflip > div:hover .icon-wrap,
                .u_next_story.nav-reveal > div:hover,
                .u_next_story.nav-roundslide > div:hover,
                .u_next_story.nav-circlepop div:before,
                .u_next_story.nav-slide > div div{
                	background-color: {$h_bg_color};
                }
                .u_next_story.nav-fillpath h3,
                .u_next_story.nav-growpop span,
                .u_next_story.nav-growpop h3,
                .u_next_story.nav-fillslide div span,
                .u_next_story.nav-fillslide h3,
                .u_next_story.nav-growpop > div div,
                .u_next_story.nav-split h3,
                .u_next_story.nav-slide h3,
                .u_next_story.nav-roundslide a h3
                {
                	color: {$h_tx_color};
                }
                .u_next_story.nav-multithumb .icon-wrap::after,
                .u_next_story.nav-circlepop .icon-wrap:before, .u_next_story.nav-circlepop .icon-wrap:after{
                	background-color: {$tx_color};
                }
                .u_next_story.nav-multithumb > div div::after{
                	color: {$tx_color};
                }                
                .u_next_story.nav-circlepop > div:hover .icon-wrap:before,
				.u_next_story.nav-circlepop > div:hover .icon-wrap:after{
					background-color: {$h_tx_color};
				}
				.u_next_story.nav-growpop span,
				.u_next_story.nav-fillslide img,
				.u_next_story.nav-fillslide div span{
					border-color: {$h_tx_color};
				}
				
				nav.u_next_story > div{
					top: {$top_position}%;
				}

                ";
		wp_add_inline_style( $this->_token . '-frontend', $custom_css );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend',
			esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ),
			$this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );

		$args_array = array(
			'scroll_position'      => get_option( 'u_next_story_scroll_position', 0 ),
			'scroll_position_unit' => get_option( 'u_next_story_scroll_position_unit', 0 )
		);
		wp_localize_script( $this->_token . '-frontend', 'ucat_ns', $args_array );


	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		wp_register_style( 'select2css', esc_url( $this->assets_url ) . 'css/select2.min.css', false, '4.0.6', 'all' );
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(
			'wp-color-picker',
			'select2css'
		), $this->_version );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
		wp_register_script( 'select2', $this->assets_url . 'js/select2.min.js', [ 'jquery' ], '4.0.6' );
		wp_register_script( 'jquery-block', $this->assets_url . 'js/jquery.blockUI.min.js', [ 'jquery' ], '1.7.0 ' );

		$deph = [ 'wp-color-picker', 'jquery', 'jquery-block', 'select2' ];
		wp_register_script( $this->_token . '-settings-js',
			$this->assets_url . 'js/settings' . $this->script_suffix . '.js', $deph, $this->_version );
		wp_localize_script( $this->_token . '-settings-js', 'uns_settings_params', array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'default_nonce' => wp_create_nonce( 'u_next_story_nonce' ),
		) );

		wp_register_script( $this->_token . '-admin',
			esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ),
			$this->_version );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'u-next-story', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = 'u-next-story';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Convert Hex Color to RGB
	 *
	 * @access  public
	 *
	 * @param  string  $hex  The hex code.
	 *
	 * @return  array
	 */
	public function hex2rgb( $hex ) {
		$hex = str_replace( "#", "", $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );

		return $rgb; // returns an array with the rgb values
	}

	/**
	 * Check if post is in a menu
	 *
	 * @param string|null $menu      menu name, id, or slug
	 * @param int|null $object_id  post object id of page
	 *
	 * @return bool true if object is in menu
	 */
	public function object_is_in_menu( $menu = null, $object_id = null ): bool {

		// get menu object
		$menu_object = wp_get_nav_menu_items( esc_attr( $menu ) );

		// stop if there isn't a menu
		if ( ! $menu_object ) {
			return false;
		}

		// get the object_id field out of the menu object
		$menu_items = wp_list_pluck( $menu_object, 'object_id' );

		// use the current post if object_id is not specified
		if ( ! $object_id ) {
			$object_id = get_queried_object_id();
		}

		// test if the specified page is in the menu or not. return true or false.
		return in_array( (int) $object_id, $menu_items );

	}

	/**
	 * @return array
	 */
	public function get_rule_settings(): array {
		$loop_menu = get_option( 'u_next_story_loop_menu', 'off' );
		$_post     = get_post();
		$settings  = [
			'object_id'  => $_post->ID,
			'post_types' => get_option( 'u_next_story_post_types', [] ),
			'same_term'  => get_option( 'u_next_story_same_term', [] ),
			'exclude'    => get_option( 'u_next_story_exclude', [] ),
			'menu'       => get_option( 'u_next_story_menu', '' ),
			'submenu'    => get_option( 'u_next_story_submenu', 'include' ),
			'loop_menu'  => $loop_menu === 'on',
		];

		$rules = get_option( U_Next_Story()->settings->base . 'rules', [] );
		foreach ( $rules as $rule ) {
			if ( isset( $rule['post_types'] ) && ! empty( $rule['post_types'] ) && is_array( $rule['post_types'] ) && is_singular( $rule['post_types'] ) ) {
				$settings['post_types'] = [ $_post->post_type ];
				$settings['exclude']    = isset( $rule['exclude'] ) ? $rule['exclude'] : [];
				$settings['same_term']  = isset( $rule['same_term'] ) ? $rule['same_term'] : [];
				break;
			}
		}
		$settings['same_term'] = isset( $settings['same_term'][ $_post->post_type ] ) ? $settings['same_term'][ $_post->post_type ] : '';
		$exclude               = [];
		if ( $settings['exclude'] ) {
			$taxonomy_objects = array_keys( get_object_taxonomies( $_post->post_type, 'objects' ) );
			foreach ( $settings['exclude'] as $tax => $terms ) {
				if ( ! in_array( $tax, $taxonomy_objects ) ) {
					continue;
				}
				$exclude = array_merge( $exclude, $terms );
			}
		}
		$settings['exclude'] = array_map( 'absint', $exclude );

		return apply_filters( $this->_token . '_rule_settings', $settings );
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
	 * @param  WP_Post  $post  The adjacent post.
	 * @param  string  $adjacent  Whether the post is previous or next.
	 */
	public function parse_post_link( $output, $format, $link, $post, $adjacent ) {
		$post     = false;
		$settings = $this->get_rule_settings();
		if ( $settings['post_types'] && is_array( $settings['post_types'] ) && is_singular( $settings['post_types'] ) ) {
			$result = $this->get_adjacent_post_link( $format, $link, $adjacent, $settings );
			if ( ! $result ) {
				return false;
			}
			$output = $result[0];
			$post   = $result[1];
		} elseif ( ! empty( $settings['menu'] ) ) {
			$result = $this->get_adjacent_menu_link( $format, $link, $adjacent, $settings );
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
					$url = $this->get_attachment_image( $im->ID, $size );
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
			$url = $this->get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 130, 100 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb130', $thumb, $output );
		} elseif ( strpos( $output, '%thumb135' ) ) {
			$url = $this->get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 135, 800 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb135', $thumb, $output );
		} elseif ( strpos( $output, '%thumb100' ) ) {
			$url = $this->get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 100, 100 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb100', $thumb, $output );
		} elseif ( strpos( $output, '%thumb200' ) ) {
			$url = $this->get_attachment_image( get_post_thumbnail_id( $post->ID ), array( 200, 112 ) );
			if ( $url ) {
				$thumb = '<img src="' . $url . '" />';
			}
			$output = str_replace( '%thumb200', $thumb, $output );
		} else {
			$url = $this->get_attachment_image( get_post_thumbnail_id( $post->ID ) );
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
	 * Get adjacent item menu link.
	 *
	 * @param  string  $format  Link anchor format.
	 * @param  string  $link  Link permalink format.
	 * @param  bool  $adjacent  Whether to display link to previous or next post. Default next.
	 * @param  array  $settings  Settings
	 *
	 * @return array|boolean The link URL of the previous or next post in relation to the current menu item.
	 * @since 1.0.1
	 *
	 */
	public function get_adjacent_menu_link( $format, $link, $adjacent, $settings ) {
		$location = $settings['menu'];
		$menu     = false;
		if ( ! is_numeric( $location ) ) {
			$theme_locations = get_nav_menu_locations();
			if ( $theme_locations && isset( $theme_locations[ $location ] ) ) {
				$menu_obj = get_term( $theme_locations[ $location ], 'nav_menu' );
				if ( $menu_obj ) {
					$menu = $menu_obj->term_id;
				}
			}
		}

		if ( ! $menu ) {
			return false;
		}
		$previous = $adjacent === 'previous';

		$menu_items        = (array) wp_get_nav_menu_items( $menu );
		$loop_menu         = $settings['loop_menu'];
		$submenu           = $settings['submenu'];
		$object_id         = $settings['object_id'];
		$current_menu_item = null;

		switch ( $submenu ) {
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
			if ( $menu_object_id === $object_id ) {
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
	 *
	 * @param  string  $format
	 * @param  string  $link
	 * @param  string  $adjacent
	 * @param  array  $settings
	 *
	 * @return array|boolean The link URL of the previous or next post in relation to the current menu item.
	 */
	public function get_adjacent_post_link( $format, $link, $adjacent, $settings ) {
		$is_previous  = $adjacent === 'previous';
		$taxonomy     = $settings['same_term'];
		$exclude      = $settings['exclude'];
		$in_same_term = ! ! $taxonomy;
		$taxonomy     = !empty($settings['same_term']) ? $settings['same_term'] : 'category';
		$post = get_adjacent_post( $in_same_term, $exclude, $is_previous, $taxonomy );
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

	// Process a single image ID (this is an AJAX handler)
	public function get_attachment_image( $id, $size = array( 90, 90 ) ) {

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
	 * Add arrow navigation to footer
	 * @access  public
	 */
	public function display_arrow_navigation() {
		$effects    = get_option( 'u_next_story_effects_navigation', 'slide' );

		$this->get_template( 'arrow_icons.php' );
		$this->get_template( $effects . '.php' );
	}

	function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = $this->locate_template( $template_name, $template_path, $default_path );

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

	function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = $this->template_path();
		}

		if ( ! $default_path ) {
			$default_path = $this->plugin_path() . '/templates/';
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

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( plugin_dir_path( __FILE__ ) ) );
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'u_next_story_template_path', 'u-next-story/' );
	}

	/**
	 * Main U_Next_Story Instance
	 *
	 * Ensures only one instance of U_Next_Story is loaded or can be loaded.
	 *
	 * @return U_Next_Story instance
	 * @see   U_Next_Story()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function install() {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
