<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Scripts.
 *
 * @class        U_Next_Story_Scripts
 * @version        1.0.0
 * @since        2.0.0
 * @package        U_Next_Story/Classes
 * @category    Class
 * @author        Elena Zhyvohliad
 */
class U_Next_Story_Scripts {

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
	 * Suffix for Javascript.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	public function __construct() {
		$this->init();

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );
	}

	public function init() {
		$this->dir        = dirname( U_NEXT_STORY_PLUGIN_FILE );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', U_NEXT_STORY_PLUGIN_FILE ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * @param  string  $file_name
	 * @param bool $force
	 *
	 * @return string
	 */
	public function scripts_url( string $file_name, bool $force = false ): string {
		$suffix = $force ? '.min' : $this->script_suffix;
		return esc_url( $this->assets_url . 'js/' . $file_name . $suffix . '.js' );
	}

	/**
	 * @param  string  $file_name
	 * @param  bool  $force
	 *
	 * @return string
	 */
	public function styles_url( string $file_name, $force = false ): string {
		$suffix = $force ? '.min' : $this->script_suffix;
		return esc_url( $this->assets_url . 'css/' . $file_name . $suffix . '.css' );
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @return void
	 * @since   1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( U_NEXT_STORY_TOKEN . '-frontend', $this->styles_url( 'frontend' ), array(),
			U_NEXT_STORY_PLUGIN_VERSION );
		wp_enqueue_style( U_NEXT_STORY_TOKEN . '-frontend' );

		$settings = (new U_Next_Story_Settings())->find_rules(get_post());

		$rgb_bg_color = $this->hex2rgb( $settings->background_color );
		$rgb_bg_color = implode( ',', $rgb_bg_color );

		$rgb_h_tx_color = $this->hex2rgb( $settings->hover_text_color );
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
                    background-color: {$settings->background_color};
                }
                .u_next_story.nav-growpop .icon-wrap{
                	border-color: {$settings->background_color};
                }
                .u_next_story.nav-diamond svg.icon{
                	 stroke: {$settings->text_color};
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
                    fill: {$settings->text_color};
                }
                .u_next_story.nav-diamond > div:hover svg.icon{
                	 stroke: {$settings->hover_text_color};
                }
                .u_next_story.nav-fillslide > div:hover svg.icon,
                .u_next_story.nav-diamond > div:hover svg.icon,
                .u_next_story.nav-circleslide > div:hover svg.icon,
                .u_next_story.nav-doubleflip > div:hover svg.icon,
                .u_next_story.nav-thumbflip > div:hover svg.icon,
                .u_next_story.nav-reveal > div:hover svg.icon,
                .u_next_story.nav-roundslide > div:hover svg.icon{
                	fill: {$settings->hover_text_color};
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
                	background-color: {$settings->hover_background_color};
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
                	color: {$settings->hover_text_color};
                }
                .u_next_story.nav-multithumb .icon-wrap::after,
                .u_next_story.nav-circlepop .icon-wrap:before, .u_next_story.nav-circlepop .icon-wrap:after{
                	background-color: {$settings->text_color};
                }
                .u_next_story.nav-multithumb > div div::after{
                	color: {$settings->text_color};
                }                
                .u_next_story.nav-circlepop > div:hover .icon-wrap:before,
				.u_next_story.nav-circlepop > div:hover .icon-wrap:after{
					background-color: {$settings->hover_text_color};
				}
				.u_next_story.nav-growpop span,
				.u_next_story.nav-fillslide img,
				.u_next_story.nav-fillslide div span{
					border-color: {$settings->hover_text_color};
				}
				
				nav.u_next_story > div{
					top: {$settings->top_position}%;
				}

                ";
		wp_add_inline_style( U_NEXT_STORY_TOKEN . '-frontend', $custom_css );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( U_NEXT_STORY_TOKEN . '-frontend', $this->scripts_url( 'frontend' ), array( 'jquery' ),
			U_NEXT_STORY_PLUGIN_VERSION );
		wp_enqueue_script( U_NEXT_STORY_TOKEN . '-frontend' );
		$settings = (new U_Next_Story_Settings())->find_rules(get_post());

		$args_array = array(
			'scroll_position'      => $settings->scroll_position,
			'scroll_position_unit' => $settings->scroll_position_unit
		);
		wp_localize_script( U_NEXT_STORY_TOKEN . '-frontend', 'ucat_ns', $args_array );


	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function admin_enqueue_styles() {

		wp_register_style( 'select2css', $this->styles_url( 'select2', true ), false, '4.0.6', 'all' );
		wp_register_style( U_NEXT_STORY_TOKEN . '-admin', $this->styles_url( 'admin' ), array(
			'wp-color-picker',
			'select2css'
		), U_NEXT_STORY_PLUGIN_VERSION );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function admin_enqueue_scripts() {
		wp_register_script( 'select2', $this->scripts_url( 'select2', true ), [ 'jquery' ], '4.0.6' );
		wp_register_script( 'jquery-block', $this->scripts_url( 'jquery.blockUI', true ), [ 'jquery' ], '1.7.0 ' );

		$deph = [ 'wp-color-picker', 'jquery', 'jquery-block', 'select2' ];
		wp_register_script( U_NEXT_STORY_TOKEN . '-settings-js',
			$this->scripts_url( 'settings' ), $deph, U_NEXT_STORY_PLUGIN_VERSION );

		wp_localize_script( U_NEXT_STORY_TOKEN . '-settings-js', 'uns_settings_params', array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'default_nonce' => wp_create_nonce( 'u_next_story_nonce' ),
		) );

		wp_register_script( U_NEXT_STORY_TOKEN . '-admin',
			$this->scripts_url( 'admin' ), array( 'jquery' ),
			U_NEXT_STORY_PLUGIN_VERSION );
	} // End admin_enqueue_scripts ()

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
		return array( $r, $g, $b ); // returns an array with the rgb values
	}

}

new U_Next_Story_Scripts();
