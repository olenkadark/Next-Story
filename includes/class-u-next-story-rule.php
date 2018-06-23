<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class U_Next_Story_Rule {

    /**
     * Prefix for plugin settings.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    protected $title              = '';
    protected $post_types         = [];
    protected $menu               = [];
    protected $submenu            = 'include';
    protected $loop_menu          = 'off';
    protected $exclude            = [];
    protected $effects_navigation = '';
    protected $background_color   = '';
    protected $text_color         = '';
    protected $hover_background_color  = '';
    protected $hover_text_color   = '';
    protected $top_position       = '';
    protected $scroll_position    = '';
    protected $apply_styles       = false;

    public function __construct($data=[])
    {
        $this->base = U_Next_Story()->settings->base;
        $styles_k = ['effects_navigation', 'background_color', 'text_color', 'hover_background_color', 'hover_text_color', 'top_position', 'scroll_position'];
        foreach ($styles_k as $k){
            $this->$k = get_option( $this->base . 'rules');
        }

        foreach ($data as $key => $val){
            $this->$key = $val;
        }
    }

    public function __call($name, $arguments)
    {
        if(strpos('get_', $name) === 0 ){
            $property = str_replace('get_', '', $name);
            return isset($this->$property) ? $this->$property : '';
        }
        return '';
    }

}
