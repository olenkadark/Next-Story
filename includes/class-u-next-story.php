<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'U_Next_Story' ) ) :
	class U_Next_Story {

		/**
		 * The version number.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $_version = '2.0.0';

		/**
		 * The single instance of U_Next_Story.
		 * @var    U_Next_Story
		 * @access   private
		 * @since    1.0.0
		 */
		private static $_instance = null;

		/**
		 * The token.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $_token = 'u_next_story';

		/**
		 * The main plugin file.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $file;

		/**
		 * Settings class object
		 * @var     U_Next_Story_Admin_Settings
		 * @access  public
		 * @since   1.0.0
		 */
		public $settings = null;


		/**
		 * Constructor function.
		 * @access  public
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {
			do_action( 'before_load_' . $this->_token );

			$this->define_constants();
			$this->includes();
			$this->init();

			do_action( $this->_token . '_loaded' );
		} // End __construct ()

		public function init() {
			// Load plugin environment variables
			$this->file = U_NEXT_STORY_PLUGIN_FILE;
			$this->settings = new U_Next_Story_Admin_Settings();
			register_activation_hook( U_NEXT_STORY_PLUGIN_FILE, array( $this, 'install' ) );

			// Handle localisation
			$this->load_plugin_textdomain();
			add_action( 'init', array( $this, 'load_localisation' ), 0 );
		}

		/**
		 * Define Constants.
		 */
		private function define_constants() {
			$this->define( 'U_NEXT_STORY_TOKEN', $this->_token );
			$this->define( 'U_NEXT_STORY_PLUGIN_PATH', $this->plugin_path() );
			$this->define( 'U_NEXT_STORY_TEMPLATE_PATH', $this->template_path() );
			$this->define( 'U_NEXT_STORY_PLUGIN_VERSION', $this->_version );
			$this->define( 'U_NEXT_STORY_PLUGIN_BASENAME', plugin_basename( U_NEXT_STORY_PLUGIN_FILE ) );
		}

		private function includes() {
			// Load plugin class files
			require_once( 'class-u-next-story-autoloader.php' );
			require_once( 'u-next-story-functions.php' );
			require_once( 'class-u-next-story-scripts.php' );
			require_once( 'class-u-next-story-ajax.php' );
			require_once( 'class-u-next-story-hooks.php' );
			#require_once( 'class-u-next-story-rule.php' );

			// Load plugin admin class files
			//require_once( 'admin/class-u-next-story-admin-settings.php' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string  $name
		 * @param  string|bool  $value
		 */
		private function define( string $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string  $type  admin, ajax, cron or frontend.
		 *
		 * @return bool
		 */
		private function is_request( string $type ): bool {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}


		/**
		 * Load plugin localisation
		 * @access  public
		 * @return  void
		 * @since   1.0.0
		 */
		public function load_localisation() {
			load_plugin_textdomain( 'u-next-story', false, dirname( plugin_basename( U_NEXT_STORY_PLUGIN_FILE ) ) . '/lang/' );
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
			load_plugin_textdomain( $domain, false, dirname( plugin_basename( U_NEXT_STORY_PLUGIN_FILE ) ) . '/lang/' );
		} // End load_plugin_textdomain ()



		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path(): string {
			return untrailingslashit( plugin_dir_path( U_NEXT_STORY_PLUGIN_FILE ) );
		}

		/**
		 * Get the template path.
		 * @return string
		 */
		public function template_path(): string {
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
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
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
endif;
