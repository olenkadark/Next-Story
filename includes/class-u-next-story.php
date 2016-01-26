<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class U_Next_Story {

	/**
	 * The single instance of U_Next_Story.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
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
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'u_next_story';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

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
		add_filter( 'previous_post_link', array( $this, 'parse_post_link' ), 150, 5 );
		add_filter( 'next_post_link', array( $this, 'parse_post_link' ), 150, 5 );
		add_action( 'wp_footer', array( $this, 'display_arrow_navigation' ) );
	} // End __construct ()

	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new U_Next_Story_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy
	 * @param  string $taxonomy   Taxonomy name
	 * @param  string $plural     Taxonomy single name
	 * @param  string $single     Taxonomy plural name
	 * @param  array  $post_types Post types to which this taxonomy applies
	 * @return object             Taxonomy class object
	 */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new U_Next_Story_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );

		$bg_color   = get_option( 'u_next_story_background_color', '#ffffff' );
		$tx_color   = get_option( 'u_next_story_text_color', '#34495e' );
		$h_bg_color = get_option( 'u_next_story_hover_background_color', '#34495e' );
		$h_tx_color = get_option( 'u_next_story_hover_text_color', '#ffffff' );

		$rgb_bg_color = $this->hex2rgb($bg_color);
		$rgb_bg_color = implode(',', $rgb_bg_color);

		$rgb_h_tx_color = $this->hex2rgb($h_tx_color);
		$rgb_h_tx_color = implode(',', $rgb_h_tx_color);
		
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

                ";
        wp_add_inline_style( $this->_token . '-frontend', $custom_css );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'u-next-story', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'u-next-story';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	* Convert Hex Color to RGB
	*
	* @access  public
	* @param string  $hex   The hex code.
	* @return  array
	*/
	public function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);

	   return $rgb; // returns an array with the rgb values
	}

	/**
	 * Filter the adjacent post link.
	 *
	 * The dynamic portion of the hook name, `$adjacent`, refers to the type
	 * of adjacency, 'next' or 'previous'.
	 *
	 *
	 * @param string  $output   The adjacent post link.
	 * @param string  $format   Link anchor format.
	 * @param string  $link     Link permalink format.
	 * @param WP_Post $post     The adjacent post.
	 * @param string  $adjacent Whether the post is previous or next.
	 */
	public function parse_post_link( $output, $format, $link, $post, $adjacent )
	{
		$multithumb = '';

		if ( !$post ){
			return;
		}
		if( strpos($output, '%multithumb')){

			$media = get_attached_media( 'image', $post->ID );
			if( $media ){
				$size = array(62, 64);
				$i = 0;
				foreach ($media as $im) { $i++;
					$url   = $this->get_attachment_image( $im->ID, $size );
					if( $url ){
						$multithumb .= '<img src="'.$url .'" />';			
					}
					$size[0] -= 16;
					$size[1] -= 16;
					if( $i == 3) break;
				}
			}
			$output = str_replace( '%multithumb', $multithumb, $output );

		}
		else if( strpos($output, '%thumb130')){
			$url   = $this->get_attachment_image( get_post_thumbnail_id($post->ID), array(130, 100) );
			if( $url ){
				$thumb  = '<img src="'.$url .'" />';			
			}
			$output = str_replace( '%thumb130', $thumb, $output );
		}
		else if( strpos($output, '%thumb135')){
			$url   = $this->get_attachment_image( get_post_thumbnail_id($post->ID), array(135, 800) );
			if( $url ){
				$thumb  = '<img src="'.$url .'" />';			
			}
			$output = str_replace( '%thumb135', $thumb, $output );
		}
		else if( strpos($output, '%thumb100')){
			$url   = $this->get_attachment_image( get_post_thumbnail_id($post->ID), array(100, 100) );
			if( $url ){
				$thumb  = '<img src="'.$url .'" />';			
			}
			$output = str_replace( '%thumb100', $thumb, $output );
		}
		else if( strpos($output, '%thumb200')){
			$url   = $this->get_attachment_image( get_post_thumbnail_id($post->ID), array(200, 112) );
			if( $url ){
				$thumb  = '<img src="'.$url .'" />';			
			}
			$output = str_replace( '%thumb200', $thumb, $output );
		} else {
			$url   = $this->get_attachment_image( get_post_thumbnail_id($post->ID) );
			if( $url ){
				$thumb  = '<img src="'.$url .'" />';			
			}
			$output = str_replace( '%thumb', $thumb, $output );
		}


		$author = get_the_author_meta( 'display_name', $post->post_author );
		$output = str_replace( '%author', $author, $output );
		return $output;
	}

	// Process a single image ID (this is an AJAX handler)
	function get_attachment_image($id, $size = array(90, 90) ) {

		$image_src = wp_get_attachment_image_src( $id, $size );
		if( !$image_src )
			return false;

		if( $image_src[1] == $size[0] && $image_src[2] == $size[1] ){
			return $image_src[0];
		}

		$image = get_post( $id );

		if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) )
			return false;

		$fullsizepath = get_attached_file( $image->ID );

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) )
			return false;

		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY
		include_once ABSPATH. '/wp-admin/includes/image.php';

		$metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

		if ( is_wp_error( $metadata ) )
			return false;
		if ( empty( $metadata ) )
			return false;

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $image->ID, $metadata );

		$image_src = wp_get_attachment_image_src( $id, $size );
		if( $image_src ){
			return $image_src[0];
		}
		else{
			return false;
		}
	}

	/**
	 * Add arrow navigation to footer
	 * @access  public
	 */
	public function display_arrow_navigation()
	{
		$post_types = get_option('u_next_story_post_types', array('post'));
		
		if( $post_types && is_array($post_types) && is_singular( $post_types ) ){
			$effects = get_option('u_next_story_effects_navigation', 'slide');
			
			if( !$effects ){
				$effects = 'slide';
			}
			
			$this->get_template('arrow_icons.php');
			$this->get_template($effects . '.php');
			
		}
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
		$located = apply_filters( 'u_next_story_get_template', $located, $template_name, $args, $template_path, $default_path );

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
		return untrailingslashit( plugin_dir_path(plugin_dir_path( __FILE__ )) );
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
	 * @since 1.0.0
	 * @static
	 * @see U_Next_Story()
	 * @return Main U_Next_Story instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
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
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
