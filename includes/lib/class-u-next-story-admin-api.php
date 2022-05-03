<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class U_Next_Story_Admin_API {

	/**
	 * Constructor function
	 */
	public function __construct () {
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array   $field Field data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function display_field ( $args = array(), $post = false, $echo = true ) {

		// Get field info
		if ( isset( $args['field'] ) ) {
			$field = $args['field'];
		} else {
			$field = $args;
		}
		$class = $field['class'] ?? [];
		$class = !is_array($class) ? [$class] : $class;

		// Check for prefix on option name
		$field_name  = $option_name = $args['prefix'] ?? '';

		if ( isset( $field['name'] ) ) {
            $field_name .= $field['name'];
        }else{
            $field_name .= $field['id'];
        }
		// Get saved data
		$data = '';
		if ( $post ) {

			// Get saved field data
			$option = get_post_meta( $post->ID, $field['id'], true );

			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		} else {

			// Get saved option
			$option_name .= $field['option_name'] ?? $field['id'];

			$option = get_option( $option_name );
			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		}

		// Show default data if no option saved and default is supplied
		if ( $data === false && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( $data === false ) {
			$data = '';
		}

		if( isset($field['value']) ){
			$data = $field['value'];
        }
		$html = '';

        $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';

		switch( $field['type'] ) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input type="text"
				                id="' . esc_attr( $field['id'] ) . '"				                 
				                name="' . esc_attr( $field_name ) . '" 
				                placeholder="' . esc_attr( $field['placeholder'] ) . '" 
				                value="' . esc_attr( $data ) . '" 
				                class="' . esc_attr( implode(' ', $class) ) . '" />' . "\n";
			break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" 
                                type="' . esc_attr( $field['type'] ) . '" 
                                name="' . esc_attr( $field_name ) . '" 
                                placeholder="' . esc_attr( $field['placeholder'] ) . '" 
                                class="' . esc_attr( implode(' ', $class) ) . '"
                                value="' . esc_attr( $data ) . '"' . $min . '' . $max . '/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input type="text" 
				                id="' . esc_attr( $field['id'] ) . '" 
				                name="' . esc_attr( $field_name ) . '" 
				                placeholder="' . esc_attr( $field['placeholder'] ) . '"
				                class="' . esc_attr( implode(' ', $class) ) . '" 
				                value="" />' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" 
				                    name="' . esc_attr( $field_name ) . '" 
				                    class="' . esc_attr( implode(' ', $class) ) . '"
				                    placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' == $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" 
				                type="' . esc_attr( $field['type'] ) . '"
				                class="' . esc_attr( implode(' ', $class) ) . '" 
				                name="' . esc_attr( $field_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( is_array($data) && in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $field_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $field_name ) . '" 
                                id="' . esc_attr( $field['id'] ) . '" 
                                class="' . esc_attr( implode(' ', $class) ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					if( is_array($v) ){
						$label = isset($v['label']) ? $v['label'] : '';
						$html .= '<optgroup label="'.$label.'">';
						foreach ( $v['options'] as $kk => $vv ) {
							$selected = false;
							if ( $kk == $data ) {
								$selected = true;
							}
							$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $kk ) . '">' . $vv . '</option>';
						}
						$html .= '</optgroup>';
					}else{
						$selected = false;
						if ( $k == $data ) {
							$selected = true;
						}
						$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
					}
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
                $data = is_array($data) ? $data : [];
				$class[] = 'u-init-select';
				$html .= '<select name="' . esc_attr( $field_name ) . '[]" 
				                id="' . esc_attr( $field['id'] ) . '"
				                 multiple="multiple" 
				                 class="' . esc_attr( implode(' ', $class) ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if ( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $field_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $field_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'u-next-story' ) . '" data-uploader_button_text="' . __( 'Use image' , 'u-next-story' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'u-next-story' ) . '" />' . "\n";
				$html .= '<input id="' . $field_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'u-next-story' ) . '" />' . "\n";
				$html .= '<input id="' . $field_name . '" class="image_data_field" type="hidden" name="' . $field_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $field_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				if(isset($field['description'])) $html .= '<span class="description">' . $field['description'] . '</span>';
			break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}
				if( isset($field['description']) ){
					$html .= '<span class="description">' . $field['description'] . '</span>' . "\n";
				}


				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
			break;
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html;

	}

	/**
	 * Validate form field
	 * @param  string $data Submitted value
	 * @param  string $type Type of field to validate
	 * @return string       Validated value
	 */
	public function validate_field ( $data = '', $type = 'text' ) {

		switch( $type ) {
			case 'text': $data = esc_attr( $data ); break;
			case 'url': $data = esc_url( $data ); break;
			case 'email': $data = is_email( $data ); break;
		}

		return $data;
	}

}
