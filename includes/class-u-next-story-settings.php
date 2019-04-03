<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class U_Next_Story_Settings {

	/**
	 * The single instance of U_Next_Story_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'u_next_story_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );

	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_options_page( __( 'Next Story Settings', 'u-next-story' ) , __( 'Next Story', 'u-next-story' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {
        wp_enqueue_style( $this->parent->_token . '-admin' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'u-next-story' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {
		$post_type_objects = get_post_types(array('public' => true), 'objects');
		$post_types        = array();
		if( $post_type_objects ){
			foreach ($post_type_objects as $slug => $object ) {
				$post_types[$slug] = $object->label;
			}	
		}


		$menus  = array('' => __('None', 'u-next-story') );
		$nav_menus = get_registered_nav_menus();
		if( $nav_menus && is_array($nav_menus)){
			$menus['location'] = array('label' => __( 'Theme Location', 'u-next-story' ), 'options' => $nav_menus);
		}
		//var_dump();
		$_menus = wp_get_nav_menus();
		if( $_menus ){
			$menus['menus'] = array('label' => __( 'Menu', 'u-next-story' ), 'options' => array());
			foreach ($_menus as $m) {
				$menus['menus']['options'][$m->term_id] = $m->name;
			}
		}

        $post_type_objects = get_post_types(array('public' => true), 'objects');
        $exclude           = array();
        if( $post_type_objects ){
            foreach ($post_type_objects as $slug => $object ) {
                $taxonomy_objects = get_object_taxonomies( $slug, 'objects' );

                foreach ($taxonomy_objects as $taxonomy) {
                    if( !$taxonomy->public ) continue;

                    if( isset($exclude[$taxonomy->name])){
                        $exclude[$taxonomy->name]['class'][] = 'show_post_type_' . $slug;
                    }else {
                        $terms = $this->get_terms($taxonomy->name);
                        if(empty($terms)) continue;

                        $exclude[$taxonomy->name] = [
                            'id' => $slug . '_' . $taxonomy->name,
                            'label' => $taxonomy->label,
                            'name'  => 'exclude[' . $taxonomy->name . ']',
                            'class' => ['post_type_taxonomy', 'show_post_type_' . $slug],
                            'type' => 'select_multi',
                            'options' => $terms
                        ];
                    }
                }
            }
        }
        $settings['general'] = array(
            'title'    => __( 'General', 'u-next-story' ),
            'sections' => [
                'general' => [
                    'id'                    => 'general',
                    'title'					=> __( 'General', 'u-next-story' ),
                    'fields'				=> array(
                        array(
                            'id' 			=> 'post_types',
                            'label'			=> __( 'Post Types', 'u-next-story' ),
                            'description'	=> __( 'Choose post types where need display arrow navigation.', 'u-next-story' ),
                            'type'			=> 'select_multi',
                            'options'		=> $post_types,
                            'default'		=> array( 'post' )
                        ),
                        array(
                            'id' 			=> 'menu',
                            'label'			=> __( 'Menu', 'u-next-story' ),
                            'description'	=> __( 'Choose menu for displaying arrow navigation.', 'u-next-story' ),
                            'type'			=> 'select',
                            'options'		=> $menus,
                            'default'		=> ''
                        ),
                        array(
                            'id' 			=> 'submenu',
                            'label'			=> __( 'Sub-items', 'u-next-story' ),
                            'description'	=> __( 'Include/exclude sub-items to the arrow navigation (applicable only for hierarchical).', 'u-next-story' ),
                            'type'			=> 'select',
                            'options'		=> array(
                                'include'      => __( 'Include', 'u-next-story' ),
                                'exclude'      => __( 'Exclude', 'u-next-story' ),
                                'only_submenu' => __( 'Only sub-items', 'u-next-story' ),
                            ),
                            'default'		=> 'include'
                        ),
                        array(
                            'id' 			=> 'loop_menu',
                            'label'			=> __( 'Loop', 'u-next-story' ),
                            'description'	=> __( 'Loop menu items in the arrow navigation.', 'u-next-story' ),
                            'type'			=> 'checkbox',
                            'default'		=> 'off'
                        ),
                    )

                ],
                'exclude' => [
                    'id'                    => 'exclude',
                    'title'					=> __( 'Exclude', 'u-next-story' ),
                    'fields'				=> $exclude
                ],
                'styles' => [
                    'id'                    => 'styles',
                    'title'					=> __( 'Styles', 'u-next-story' ),
                    'fields'				=> array(
                        array(
                            'id' 			=> 'effects_navigation',
                            'label'			=> __( 'Effects' , 'u-next-story' ),
                            'description'	=> __( 'Effects and styles for arrow navigation', 'u-next-story' ),
                            'type'			=> 'select',
                            'options'		=> array(
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
                            ),
                            'default'		=> 'slide'
                        ),
                        array(
                            'id' 			=> 'background_color',
                            'label'			=> __( 'Background color' , 'u-next-story' ),
                            'type'			=> 'color',
                            'default'		=> '#ffffff'
                        ),
                        array(
                            'id' 			=> 'text_color',
                            'label'			=> __( 'Text color' , 'u-next-story' ),
                            'type'			=> 'color',
                            'default'		=> '#34495e'
                        ),
                        array(
                            'id' 			=> 'hover_background_color',
                            'label'			=> __( 'Hover Background color' , 'u-next-story' ),
                            'type'			=> 'color',
                            'default'		=> '#34495e'
                        ),
                        array(
                            'id' 			=> 'hover_text_color',
                            'label'			=> __( 'Hover Text color' , 'u-next-story' ),
                            'type'			=> 'color',
                            'default'		=> '#ffffff'
                        ),
                        array(
                            'id' 			=> 'top_position',
                            'label'			=> __( 'Top position' , 'u-next-story' ),
                            'description'	=> '%',
                            'type'			=> 'number',
                            'default'		=> '50'
                        ),
                        array(
                            'id' 			=> 'scroll_position',
                            'label'			=> __( 'Visible on scroll position' , 'u-next-story' ),
                            'description'	=> __( 'Don\'t show navigation until you scroll down to the specific position' , 'u-next-story' ),
                            'type'			=> 'number',
                            'default'		=> '0'
                        ),
	                    array(
		                    'id' 			=> 'scroll_position_unit',
		                    'label'			=> __( 'Scroll position unit' , 'u-next-story' ),
		                    'type'			=> 'select',
		                    'options'		=> array(
			                    'px'        => 'px',
			                    '%'         => '%',
		                    ),
		                    'default'		=> 'px'
	                    ),
                    )

                ]
            ]

        );

		$settings['rules'] = array(
			'title'					=> __( 'Rules', 'u-next-story' ),
			'callback'				=> array( $this, 'rules_section' )
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				if (!isset($data['sections']) ){
                    $data['sections'] = [$data];
                }

				foreach ($data['sections'] as $section_data ){

                    $section_id = isset($section_data['id']) ? $section_data['id'] : $section;
                    $callback   = isset($section_data['callback']) ? $section_data['callback'] : array( $this, 'settings_section' );

                    // Add section to page
                    add_settings_section( $section_id, $section_data['title'], $callback, $this->parent->_token . '_settings' );

                    if(!isset($section_data['fields'])) continue;

                    foreach ( $section_data['fields'] as $field ) {

                        // Validation callback for field
                        $validation = '';
                        if ( isset( $field['callback'] ) ) {
                            $validation = $field['callback'];
                        }

                        // Register field
                        $option_name = $this->base . $field['id'];
                        register_setting( $this->parent->_token . '_settings', $option_name, $validation );

                        // Add field to page
                        add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section_id, array( 'field' => $field, 'prefix' => $this->base ) );
                    }
                }



				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '';
		if( isset($this->settings[ $section['id'] ]['description']) )
			$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Next Story Settings' , 'u-next-story' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab = $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			if($tab !== 'rules') $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

            // Get settings fields
            ob_start();
            settings_fields( $this->parent->_token . '_settings' );
            do_settings_sections( $this->parent->_token . '_settings' );
            $html .= ob_get_clean();

            if($tab !== 'rules') {
                $html .= '<p class="submit">' . "\n";
                $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
                $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'u-next-story')) . '" />' . "\n";
                $html .= '</p>' . "\n";
                $html .= '</form>' . "\n";
            }

		$html .= '</div>' . "\n";

		echo $html;
	}

    public function rules_section($section){
        $sections = $this->settings[ 'general']['sections'];
        $rules = get_option( $this->base . 'rules');
        include "views/html-section-rules.php";
    }

    private function get_terms( $taxonomy='' ){
        $result = [];
        $args = array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'orderby'    => 'name',
        );
        $terms = get_terms( $args );

        if ( $terms && !is_wp_error($terms)){
            foreach ($terms as $term){
                $result[$term->term_id] = $term->name;
            }
        }

        return $result;
    }



	/**
	 * Main U_Next_Story_Settings Instance
	 *
	 * Ensures only one instance of U_Next_Story_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see U_Next_Story()
	 * @return Main U_Next_Story_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}
