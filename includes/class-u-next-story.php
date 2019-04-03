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
		add_filter( 'previous_post_link', array( $this, 'parse_post_link' ), 150, 5 );
		add_filter( 'next_post_link', array( $this, 'parse_post_link' ), 150, 5 );
		add_action( 'wp_footer', array( $this, 'display_arrow_navigation' ) );
	} // End __construct ()


	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );

		$post_types = get_option('u_next_story_post_types', array('post'));

		$bg_color   = get_option( 'u_next_story_background_color', '#ffffff' );
		$tx_color   = get_option( 'u_next_story_text_color', '#34495e' );
		$h_bg_color = get_option( 'u_next_story_hover_background_color', '#34495e' );
		$h_tx_color = get_option( 'u_next_story_hover_text_color', '#ffffff' );
		$top_position = get_option( 'u_next_story_top_position', '50' );


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
				
				nav.u_next_story > div{
					top: {$top_position}%;
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

		$args_array = array(
			'scroll_position' => get_option( 'u_next_story_scroll_position', 0 ),
			'scroll_position_unit' => get_option( 'u_next_story_scroll_position_unit', 0 )
		);
		wp_localize_script( $this->_token . '-frontend', 'ucat_ns', $args_array );


    } // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
        wp_register_style( 'select2css', esc_url( $this->assets_url ) . 'css/select2.min.css', false, '4.0.6', 'all' );
        wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array('wp-color-picker', 'select2css'), $this->_version );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
        wp_register_script( 'select2', $this->assets_url . 'js/select2.min.js', ['jquery'], '4.0.6' );
        wp_register_script( 'jquery-block', $this->assets_url . 'js/jquery.blockUI.min.js', ['jquery'], '1.7.0 ');

        $deph = ['wp-color-picker', 'jquery' , 'jquery-block', 'select2'];
        wp_register_script( $this->_token . '-settings-js', $this->assets_url . 'js/settings' . $this->script_suffix . '.js', $deph, $this->_version);
        wp_localize_script( $this->_token . '-settings-js', 'uns_settings_params', array(
            'ajax_url'                  => admin_url( 'admin-ajax.php' ),
            'default_nonce'             => wp_create_nonce( 'u_next_story_nonce' ),
        ) );

        wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
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
		$post_types = get_option('u_next_story_post_types', array('post'));
		$menu       = get_option('u_next_story_menu', '');

		if( $post_types && is_array($post_types) && is_singular( $post_types ) ){			
		}else if( $menu && !empty($menu) ){			
			$result = $this->get_adjacent_post_link($format, $link, $menu, $adjacent);
			if( $result && is_array($result) ){
				$output = $result[0];
				$post   = $result[1];				
			}else{
				return;	
			}
		}
		if ( !$post ){
			return;
		}
		
		$multithumb = $thumb = '';

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


		$author = isset($post->post_author) ? get_the_author_meta( 'display_name', $post->post_author ) : '';
		$output = str_replace( '%author', $author, $output );
		return $output;
	}

	/**
	 * Get adjacent item menu link.
	 *
	 * @since 1.0.1
	 *
	 * @param string       $format         Link anchor format.
	 * @param string       $link           Link permalink format.
	 * @param array|string $post           Object of menu item.
	 * @param bool         $adjacent       Optional. Whether to display link to previous or next post. Default next.
	 * @return string The link URL of the previous or next post in relation to the current menu item.
	 */
	public function get_adjacent_post_link($format, $link, $menu, $adjacent)
	{
		global $wp_query, $wp_rewrite;
		if( !is_numeric($menu) ){
			$location = $menu;
			$menu = false;
			$theme_locations = get_nav_menu_locations();
			if( $theme_locations && isset($theme_locations[$location])){
				$menu_obj = get_term( $theme_locations[$location], 'nav_menu' );				
				if($menu_obj){
					$menu = $menu_obj->term_id;
				}
			}
		}
		if( !$menu || empty($menu) ){
			return;
		}
		$previous = $adjacent == 'previous' ? true : false;

		$output = '';

		$menu_items        = (array) wp_get_nav_menu_items($menu);
		$loop_menu         = get_option('u_next_story_loop_menu', 'off');
		$loop_menu         = $loop_menu == 'on' ? true : false;
		$submenu           = get_option('u_next_story_submenu', 'include');
		$current_menu_item = null;

		_wp_menu_item_classes_by_context( $menu_items );

		$sorted_menu_items = $menu_items_with_children = array();
		foreach ( (array) $menu_items as $menu_item ) {
			$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
			if ( $menu_item->menu_item_parent )
				$menu_items_with_children[ $menu_item->menu_item_parent ] = true;
		}

		// Add the menu-item-has-children class where applicable
		if ( $menu_items_with_children ) {
			foreach ( $sorted_menu_items as &$menu_item ) {
				if ( isset( $menu_items_with_children[ $menu_item->ID ] ) )
					$menu_item->classes[] = 'menu-item-has-children';
			}
		}

		unset( $menu_items, $menu_item );

		$menu_items = apply_filters( 'wp_nav_menu_objects', $sorted_menu_items, array() );
		
		$parent_ = 0;
		foreach ( $menu_items as $key => $menu_item ) {
			
			if( isset($menu_item->classes) && is_array($menu_item->classes) && in_array('current-menu-item', $menu_item->classes)){
				$current_menu_item = $key;
				$parent_ = (int) $menu_item->menu_item_parent;
				break;
			}
		}
		if( is_null($current_menu_item) ){
			return;
		}
		if( $parent_ == 0 && $submenu == 'only_submenu' ){
			return;
		}
		$parents = array($parent_);
		if( $parent_ > 0 && $submenu == 'only_submenu' ){
			
			$exit = false;
			while (!$exit) {
				foreach ( $menu_items as $key => $menu_item ) {
					if( $menu_item->ID == $parent_ ){
						$par = (int) $menu_item->menu_item_parent;
						if( $par > 0 ){
							$parents[] = $parent_  = $par;
						}else{
							$exit = true;
						}
						break;
					}
				}
			}
		}
		
		$post = false;
		$i = $current_menu_item;
		$d = -1;
		$j = 0;
		$count = count($menu_items);
		while ( !$post && $d != $current_menu_item) {
			
			if( $adjacent == 'next' ){
				$i++;
				$j = 0;
			}else if( $adjacent == 'previous' ){
				$i--;
				$j = $count;
			}
			if( $i > $count || $i < 0){
				if( !$loop_menu ) break;
				$i = $j;
			}
			$d = $i;

			if(isset( $menu_items[$i] ) && strpos( $menu_items[$i]->url, '#' ) === false ){
				$parent = (int) $menu_items[$i]->menu_item_parent;
				if( $submenu == 'exclude' && $parent > 0){
					continue;
				}
				if( $submenu == 'only_submenu' && ($parent == 0 || !in_array($parent, $parents)) ){
					continue;
				}
				$post = $menu_items[$i];					
			}
		}
		
		if ( ! $post ) {
			$output = '';
		} else {
			$title = $post->title;

			if ( empty( $post->title ) )
				$title = $previous ? __( 'Previous Post' ) : __( 'Next Post' );


			//$date = mysql2date( get_option( 'date_format' ), $post->post_date );
			$rel = $previous ? 'prev' : 'next';

			$string = '<a href="' . $post->url . '" rel="'.$rel.'">';
			$inlink = str_replace( '%title', $title, $link );
			$inlink = str_replace( '%date', '', $inlink );
			$inlink = $string . $inlink . '</a>';

			$output = str_replace( '%link', $inlink, $format );
			if( $post->type == 'post_type'){
				$post = get_post($post->object_id);
			}
		}
		return array($output, $post); 
	}

	// Process a single image ID (this is an AJAX handler)
	public function get_attachment_image($id, $size = array(90, 90) ) {

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
		$menu       = get_option('u_next_story_menu', '');		
				
		if( $post_types && is_array($post_types) && is_singular( $post_types ) ){
			
			$effects = get_option('u_next_story_effects_navigation', 'slide');
			
			if( !$effects ){
				$effects = 'slide';
			}

			$this->get_template('arrow_icons.php');
			$this->get_template($effects . '.php');
			
		}elseif( $menu && !empty($menu) ){
			
			$effects = get_option('u_next_story_effects_navigation_menu', 'slide');
			
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
