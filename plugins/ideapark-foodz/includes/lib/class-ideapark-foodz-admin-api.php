<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ideapark_foodz_Admin_API {

	/**
	 * Constructor function
	 */
	public function __construct() {
		add_action( 'save_post', [ $this, 'save_meta_boxes' ], 10, 1 );
	}

	/**
	 * Generate HTML for displaying fields
	 *
	 * @param array   $field Field data
	 * @param boolean $echo  Whether to echo the field HTML or return it
	 *
	 * @return void
	 */
	public function display_field( $data = [], $post = false, $echo = true ) {

		// Get field info
		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		// Check for prefix on option name
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		// Get saved data
		$data = '';

		if ( $post ) {
			// Get saved field data
			$option_name .= $field['id'];
			$option      = get_post_meta( $post->ID, $field['id'], false );

			if ( empty( $option ) ) {
				$option = null;
			} else {
				$option = $option[0];
			}

			// Get data to display in field
			if ( isset( $option ) && $option !== null ) {
				$data = $option;
			} else {
				if ( isset( $field['default'] ) ) {
					$data = $field['default'];
				} else {
					$data = '';
				}
			}
		} else {
			// Get saved option
			$option_name .= $field['id'];
			$option      = get_option( $option_name, null );

			// Get data to display in field
			if ( isset( $option ) && $option !== null ) {
				$data = $option;
			} else {
				if ( isset( $field['default'] ) ) {
					$data = $field['default'];
				} else {
					$data = '';
				}
			}
		}

		$html = '';

		switch ( $field['type'] ) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . ( isset( $field['placeholder'] ) ? '" placeholder="' . esc_attr( $field['placeholder'] ) : '' ) . '" value="' . esc_attr( $data ) . '" />' . "\n";
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
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . ( isset( $field['placeholder'] ) ? '" placeholder="' . esc_attr( $field['placeholder'] ) : '' ) . '" value="' . esc_attr( $data ) . '"' . $min . '' . $max . '/>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . ( isset( $field['placeholder'] ) ? '" placeholder="' . esc_attr( $field['placeholder'] ) : '' ) . '" value="' . esc_attr( $data ) . '" />' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . ( isset( $field['placeholder'] ) ? '" placeholder="' . esc_attr( $field['placeholder'] ) : '' ) . '">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' == $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
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
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image', 'ideapark-foodz' ) . '" data-uploader_button_text="' . __( 'Use image', 'ideapark-foodz' ) . '" class="image_upload_button button" value="' . __( 'Upload new image', 'ideapark-foodz' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="' . __( 'Remove image', 'ideapark-foodz' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
				break;

			case 'color':
				$html .= '<div class="color-picker-wrap"><input type="text" name="' . esc_attr( $option_name ) . '" class="color-picker" value="' . esc_attr( $data ) . '" /></div>';
				break;

		}

		if ( ! empty( $field['description'] ) ) {
			switch ( $field['type'] ) {

				case 'checkbox_multi':
				case 'radio':
				case 'select_multi':
					$html .= '<br/><span class="description">' . $field['description'] . '</span>';
					break;

				default:
					if ( ! $post ) {
						$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
					}

					$html .= '<span class="description">' . $field['description'] . '</span>' . "\n";

					if ( ! $post ) {
						$html .= '</label>' . "\n";
					}
					break;
			}
		}

		if ( ! $echo ) {
			return $html;
		}

		echo ideapark_wrap( $html );

	}

	/**
	 * Validate form field
	 *
	 * @param string $data Submitted value
	 * @param string $type Type of field to validate
	 *
	 * @return string       Validated value
	 */
	public function validate_field( $data = '', $type = 'text' ) {

		switch ( $type ) {
			case 'text':
				$data = esc_attr( $data );
				break;
			case 'url':
				$data = esc_url( $data );
				break;
			case 'email':
				$data = is_email( $data );
				break;
		}

		return $data;
	}

	/**
	 * Add meta box to the dashboard
	 *
	 * @param string $id            Unique ID for metabox
	 * @param string $title         Display title of metabox
	 * @param array  $post_types    Post types to which this metabox applies
	 * @param string $context       Context in which to display this metabox ('advanced' or 'side')
	 * @param string $priority      Priority of this metabox ('default', 'low' or 'high')
	 * @param array  $callback_args Any axtra arguments that will be passed to the display function for this metabox
	 *
	 * @return void
	 */
	public function add_meta_box( $id = '', $title = '', $post_types = [], $context = 'advanced', $priority = 'default', $callback_args = null ) {

		// Get post type(s)
		if ( ! is_array( $post_types ) ) {
			$post_types = [ $post_types ];
		}

		// Generate each metabox
		foreach ( $post_types as $post_type ) {
			add_meta_box( $id, $title, [
				$this,
				'meta_box_content'
			], $post_type, $context, $priority, $callback_args );
		}
	}

	/**
	 * Display metabox content
	 *
	 * @param object $post Post object
	 * @param array  $args Arguments unique to this metabox
	 *
	 * @return void
	 */
	public function meta_box_content( $post, $args ) {

		$fields = apply_filters( $post->post_type . '_custom_fields', [], $post->post_type );

		if ( ! is_array( $fields ) || 0 == count( $fields ) ) {
			return;
		}

		echo '<div class="ideapark-custom-field-panel">' . "\n";

		foreach ( $fields as $field ) {

			if ( ! isset( $field['metabox'] ) ) {
				continue;
			}

			if ( ! is_array( $field['metabox'] ) ) {
				$field['metabox'] = [ $field['metabox'] ];
			}

			if ( in_array( $args['id'], $field['metabox'] ) ) {
				$this->display_meta_box_field( $field, $post );
			}

		}

		echo '</div>' . "\n";

	}

	/**
	 * Dispay field in metabox
	 *
	 * @param array  $field Field data
	 * @param object $post  Post object
	 *
	 * @return void
	 */
	public function display_meta_box_field( $field = [], $post ) {

		if ( ! is_array( $field ) || 0 == count( $field ) ) {
			return;
		}

		$field = '<div class="form-field"><label class="form-label" for="' . $field['id'] . '">' . $field['label'] . '</label>' . $this->display_field( $field, $post, false ) . '</div>' . "\n";

		echo ideapark_wrap( $field );
	}

	/**
	 * Save metabox fields
	 *
	 * @param integer $post_id Post ID
	 *
	 * @return void
	 */
	public function save_meta_boxes( $post_id = 0 ) {

		if ( ! $post_id ) {
			return;
		}

		if ( isset( $_POST['_inline_edit'] ) && wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce' ) ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		$fields = apply_filters( $post_type . '_custom_fields', [], $post_type );

		if ( ! is_array( $fields ) || 0 == count( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			if ( isset( $_REQUEST[ $field['id'] ] ) ) {
				update_post_meta( $post_id, $field['id'], $this->validate_field( $_REQUEST[ $field['id'] ], $field['type'] ) );
			} else {
				update_post_meta( $post_id, $field['id'], '' );
			}
		}
	}
}

require_once( ABSPATH . 'wp-includes/class-wp-image-editor.php' );
require_once( ABSPATH . 'wp-includes/class-wp-image-editor-gd.php' );
require_once( ABSPATH . 'wp-includes/class-wp-image-editor-imagick.php' );

$ip_wp_image_editor_choose = _wp_image_editor_choose();
if ( $ip_wp_image_editor_choose == 'WP_Image_Editor_Imagick' ) {

	class Ideapark_Slider_Image_Editor extends WP_Image_Editor_Imagick {
		public function get_luminance() {
			if ( $this->image ) {
				$x     = round( imagesx( $this->image ) * 0.15 );
				$y     = round( imagesy( $this->image ) * 0.4 );
				$pixel = $this->image->getImagePixelColor( $x, $y );
				$color = $pixel->getColor();
				$y     = round( ( 0.2126 * $color['r'] + 0.7152 * $color['g'] + 0.0722 * $color['b'] ) * ( 127 - $color['a'] ) / 127 );

				return $y;
			}

			return false;
		}

		public function get_hex_color( $x = 0, $y = 0 ) {
			if ( $this->image ) {
				$pixel = $this->image->getImagePixelColor( $x, $y );
				$color = $pixel->getColor();

				return sprintf( '#%s%s%s',
					dechex( $color['r'] ),
					dechex( $color['g'] ),
					dechex( $color['b'] )
				);
			}
		}

		public function has_alfa() {
			if ( $this->image ) {

				$pixel = $this->image->getImagePixelColor( 0, 0 );
				$color = $pixel->getColor( true );

				return $color['a'] < 1;
			}

			return false;
		}
	}

} else { // WP_Image_Editor_GD

	class Ideapark_Slider_Image_Editor extends WP_Image_Editor_GD {
		public function get_luminance() {
			if ( $this->image ) {
				$picker_x = round( imagesx( $this->image ) * 0.15 );
				$picker_y = round( imagesy( $this->image ) * 0.4 );
				$rgba     = imagecolorat( $this->image, $picker_x, $picker_y );
				$color    = imagecolorsforindex( $this->image, $rgba );
				$y        = round( ( 0.2126 * $color['red'] + 0.7152 * $color['green'] + 0.0722 * $color['blue'] ) * ( 127 - $color['alpha'] ) / 127 );

				return $y;
			}

			return false;
		}

		public function get_hex_color( $x = 0, $y = 0 ) {
			if ( $this->image ) {
				$rgba  = imagecolorat( $this->image, $x, $y );
				$color = imagecolorsforindex( $this->image, $rgba );

				return sprintf( '#%s%s%s',
					dechex( $color['red'] ),
					dechex( $color['green'] ),
					dechex( $color['blue'] )
				);
			}
		}

		public function has_alfa() {
			if ( $this->image ) {

				if ( imagecolortransparent( $this->image ) != - 1 ) {
					return true;
				}
				$rgba  = imagecolorat( $this->image, 0, 0 );
				$alpha = ( $rgba & 0x7F000000 ) >> 24;

				return $alpha > 0;
			}

			return false;
		}
	}

}