<?php

namespace Buma;

use Buma\FilterInput;
use function Patchwork\Redefinitions\LanguageConstructs\_print;

/**
 * Class MetaBox
 * @package KToolbox
 *
 * This class will help you managing CPT meta fields.
 */
class MetaBox {

	/**
	 * Configuration container
	 *
	 * @var array
	 */
	protected $_config = [];

	/**
	 * Singleton
	 *
	 * @return Metabox
	 *
	 * @codeCoverageIgnore
	 */
	public static function create() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new static();

			add_action( 'save_post', [ $instance, 'save_data' ] );
		}

		return $instance;
	}

	/**
	 * Returns true if config for this post type exists
	 *
	 * @param $post_type
	 *
	 * @return bool
	 */
	public function has_config( $post_type ) {
		return isset( $this->_config[ $post_type ] );
	}

	/**
	 * Adds config
	 *
	 * @param array $config
	 */
	public function add_config( array $config ) {
		foreach ( $config as $key => $box ) {
			if ( ! $this->has_config( $key ) ) {
				$this->_config[ $key ] = $box;
				add_meta_box( $box['id'], $box['title'], [
					$this,
					'show_box'
				], $box['page'], $box['context'], $box['priority'] );
			} else {
				throw new \InvalidArgumentException( __( 'Duplicate Key in Metabox configuration', 'dkwp' ) );
			}
		}
	}

	/**
	 * Filter - Shows box
	 */
	public function show_box() {
		global $post;

		if ( $this->has_config( $post->post_type ) ) {
			printf( '<input type="hidden" name="cont_theme_meta_box_nonce" value="%s" />', wp_create_nonce( __CLASS__ ) );

			echo '<table class="form-table">';
			foreach ( $this->_config[ $post->post_type ]['fields'] as $field ) {
				if ( ! empty( $field['condition'] ) ) {
					if ( ! call_user_func_array( $field['condition'], [ $field, $post ] ) ) {
						continue;
					}
				}

				$fid  = get_post_meta( $post->ID, $field['id'], true );
				$meta = ( is_array( $fid ) || is_object( $fid ) ) ? '[object]' : $fid;

				echo '<tr>', '<th style="width:25%"><h4 style="font-size:1.2em; margin:0; padding:0;" for="', $field['id'], '">', $field['name'], '</h4>', '</th>', '<td>';

				switch ( $field['type'] ) {
					case 'text':
						printf(
							'<input type="text" name="%1$s" id="%1$s" value="%2$s" size="30" style="width:97%%" maxlength="%3$s"%4$s%5$s><span style="display:block; padding:0.3em 0 0;">%6$s</span>',
							$field['id'],
							$meta ?? $field['std'],
							$field['maxlength'] ?? '',
							! empty( $field['disabled'] ) ? ' disabled' : '',
							! empty( $field['readonly'] ) ? ' readonly' : '',
							$field['desc']
						);
						break;
					case 'textarea':
						echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="30" rows="2" style="width:98%">', $meta ? $meta : $field['std'], '</textarea>
							<span style="display:block; padding:0.5em 0 0;">', $field['desc'], '</span>';
						break;
					case 'select':
						$name     = $field['id'];
						$multiple = '';
						if ( isset( $field['multiple'] ) && ( true === $field['multiple'] ) ) {
							$name     = $field['id'] . '[]';
							$multiple = 'multiple';
							$meta     = unserialize( $meta );
						}
						printf( '<select name="%1$s" id="%2$s" %3$s>',
							$name,
							$field['id'],
							$multiple
						);
						foreach ( $field['options'] as $option ) {
							printf( '<option %1$s>%2$s</option>',
								selected( $meta == $option || ( is_array( $meta ) && in_array( $option, $meta ) ) ),
								$option
							);
						}
						echo '</select>
							<span style="display:block; padding:0.3em 0 0;">', $field['desc'], '</span>';
						break;
					case 'radio':
						foreach ( $field['options'] as $option ) {
							echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'], "\n";
						}
						break;
					case 'checkbox':
						echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' /> ', '<span style="display:block; padding:0.5em 0 0;">', $field['desc'], '</span>';
						break;
					case 'checkbox_list':
						foreach ( $field['options'] as $option ) {
							$checked = $meta == $option['value'] ? 'checked="checked"' : '';
							printf( '<input type="checkbox"  value="%1$s" name="%2$s" id="%2$s" %3$s  />%s ', $option['value'], $field['id'], $checked );
						}
						break;
					case 'number':
						printf( '<input type="number" min="0" value="%1$s" name="%2$s" id="%2$s" /></input>', $meta, $field['id'] );
						break;
					case 'currency':
						printf( '<input type="number" min="0" value="%1$s" name="%2$s" id="%2$s" /> €</input>', $meta, $field['id'] );
						break;
					case 'wp_editor':
						wp_editor( ( $meta ? $meta : $field['std'] ), $field['id'] );
						echo '<span style="display:block; padding:0.5em 0 0;">', $field['desc'], '</span>';
				}
				echo '</td>', '</tr>';
			}
			echo '</table>';
		}
	}

	/**
	 * Filter - Saves data
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function save_data( $post_id ) {
		// verifies nonce
		$input = new FilterInput( INPUT_POST, 'cont_theme_meta_box_nonce' );
		if ( ! $input->has_var() || ! wp_verify_nonce( $input->get(), __CLASS__ ) ) {
			return false;
		}

		// checks autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		$post_type = ( new FilterInput( INPUT_POST, 'post_type' ) )->get( '' );
		if ( $this->has_config( $post_type ) ) {
			// Checks permissions
			if ( 'page' === $post_type && ! current_user_can( 'edit_page', $post_id ) ) {
				return false;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return false;
			}

			foreach ( $this->_config[ $post_type ]['fields'] as $field ) {
				$mid = get_post_meta( $post_id, $field['id'], true );
				$fid = ( isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : '' ); //@todo DK-2767

				if ( ! empty( $field['disabled'] ) ) {
					continue;
				}

				if ( ! empty( $field['validate'] ) ) {
					if ( ! call_user_func_array( $field['validate'], [ $field['id'], $fid, $this->_config[ $post_type ]['fields'] ] ) ) {
						$fid = '';
					}
				}

				if ( is_array( $fid ) ) {
					$fid = serialize( $fid );
				}

				if ( $fid && $fid != $mid ) {
					update_post_meta( $post_id, $field['id'], $fid );
				} elseif ( '' == $fid && $mid ) {
					delete_post_meta( $post_id, $field['id'], $mid );
				}
			}
		}

		return true;
	}

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_fields( int $post_id ): array {
		$meta   = get_post_meta( $post_id );
		$values = [];
		foreach ( $meta as $key => $value ) {
			$values[ $key ] = implode( PHP_EOL, $value );
		}

		return $values;
	}

}
