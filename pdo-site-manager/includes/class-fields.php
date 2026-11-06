<?php
/**
 * Admin field renderer.
 *
 * Renders every field type described by PDO_SM_Schema, including the
 * sortable repeater UI, Media Library pickers and the icon picker.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field renderer.
 */
class PDO_SM_Fields {

	/**
	 * Render a list of fields.
	 *
	 * @param array  $fields    Field definitions.
	 * @param array  $values    Current values.
	 * @param string $name_base Base input name.
	 * @return void
	 */
	public static function render_fields( $fields, $values, $name_base = 'pdo' ) {
		foreach ( $fields as $field ) {
			self::render_field( $field, $values, $name_base );
		}
	}

	/**
	 * Render one field (and its Bengali twin when bilingual).
	 *
	 * @param array  $field     Field definition.
	 * @param array  $values    Current values.
	 * @param string $name_base Base input name.
	 * @return void
	 */
	public static function render_field( $field, $values, $name_base = 'pdo' ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';
		$key  = isset( $field['key'] ) ? $field['key'] : '';

		if ( 'heading' === $type ) {
			echo '<h4 class="pdo-field-heading">' . esc_html( $field['label'] ) . '</h4>';
			return;
		}

		if ( ! $key ) {
			return;
		}

		if ( 'order' === $type ) {
			printf(
				'<input type="hidden" class="pdo-row-order" name="%1$s[%2$s]" value="%3$s" />',
				esc_attr( $name_base ),
				esc_attr( $key ),
				esc_attr( isset( $values[ $key ] ) ? $values[ $key ] : 0 )
			);
			return;
		}

		if ( 'repeater' === $type ) {
			self::render_repeater( $field, $values, $name_base );
			return;
		}

		$id        = sanitize_html_class( str_replace( array( '[', ']' ), '-', $name_base . '-' . $key ) );
		$bilingual = ! empty( $field['bilingual'] );
		$classes   = array( 'pdo-field', 'pdo-field--' . $type );
		if ( $bilingual ) {
			$classes[] = 'pdo-field--bilingual';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<label class="pdo-field__label" for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( isset( $field['label'] ) ? $field['label'] : $key ); ?>
				<?php if ( ! empty( $field['required'] ) ) : ?>
					<span class="pdo-req" aria-hidden="true">*</span>
				<?php endif; ?>
			</label>

			<?php if ( $bilingual ) : ?>
				<div class="pdo-lang-tabs" data-pdo-langtabs>
					<button type="button" class="pdo-lang-tab is-active" data-lang="en"><?php esc_html_e( 'English', 'pdo-site-manager' ); ?></button>
					<button type="button" class="pdo-lang-tab" data-lang="bn"><?php esc_html_e( 'বাংলা', 'pdo-site-manager' ); ?></button>
				</div>
				<div class="pdo-lang-pane is-active" data-lang="en">
					<?php self::render_control( $field, $key, $values, $name_base, $id ); ?>
				</div>
				<div class="pdo-lang-pane" data-lang="bn">
					<?php self::render_control( $field, $key . '_bn', $values, $name_base, $id . '-bn', true ); ?>
				</div>
			<?php else : ?>
				<?php self::render_control( $field, $key, $values, $name_base, $id ); ?>
			<?php endif; ?>

			<?php if ( ! empty( $field['desc'] ) ) : ?>
				<p class="pdo-field__desc"><?php echo wp_kses_post( $field['desc'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the actual input control.
	 *
	 * @param array  $field     Field definition.
	 * @param string $key       Value key (may be the `_bn` variant).
	 * @param array  $values    Values.
	 * @param string $name_base Base name.
	 * @param string $id        Element id.
	 * @param bool   $is_bn     Whether this is the Bengali twin.
	 * @return void
	 */
	private static function render_control( $field, $key, $values, $name_base, $id, $is_bn = false ) {
		$type  = isset( $field['type'] ) ? $field['type'] : 'text';
		$name  = $name_base . '[' . $key . ']';
		$value = array_key_exists( $key, (array) $values ) ? $values[ $key ] : ( isset( $field['default'] ) && ! $is_bn ? $field['default'] : '' );

		switch ( $type ) {
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="%3$d" class="pdo-input pdo-textarea" placeholder="%4$s">%5$s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					(int) ( isset( $field['rows'] ) ? $field['rows'] : 3 ),
					esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ),
					esc_textarea( (string) $value )
				);
				break;

			case 'richtext':
				$editor_id = preg_replace( '/[^a-z0-9]/', '', strtolower( $id ) );
				wp_editor(
					(string) $value,
					$editor_id,
					array(
						'textarea_name' => $name,
						'textarea_rows' => isset( $field['rows'] ) ? (int) $field['rows'] : 10,
						'media_buttons' => true,
						'teeny'         => false,
						'quicktags'     => true,
					)
				);
				break;

			case 'checkbox':
				printf(
					'<label class="pdo-switch"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /><span class="pdo-switch__track"><span class="pdo-switch__thumb"></span></span><span class="pdo-switch__text">%4$s</span></label>',
					esc_attr( $id ),
					esc_attr( $name ),
					checked( PDO_SM_Helpers::to_bool( $value ), true, false ),
					esc_html( isset( $field['checkbox_label'] ) ? $field['checkbox_label'] : __( 'Enabled', 'pdo-site-manager' ) )
				);
				break;

			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="pdo-input pdo-select">';
				foreach ( (array) $field['options'] as $opt_value => $opt_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $opt_value ),
						selected( (string) $value, (string) $opt_value, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'number':
				printf(
					'<input type="number" id="%1$s" name="%2$s" value="%3$s" class="pdo-input pdo-input--number" min="%4$s" max="%5$s" step="%6$s" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( isset( $field['min'] ) ? $field['min'] : '' ),
					esc_attr( isset( $field['max'] ) ? $field['max'] : '' ),
					esc_attr( isset( $field['step'] ) ? $field['step'] : '1' )
				);
				break;

			case 'color':
				printf(
					'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="pdo-color-field" data-default-color="%4$s" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( isset( $field['default'] ) ? $field['default'] : '' )
				);
				break;

			case 'icon':
				self::render_icon_picker( $id, $name, (string) $value );
				break;

			case 'image':
				self::render_media_picker( $id, $name, (int) $value, 'image' );
				break;

			case 'file':
				self::render_media_picker( $id, $name, (int) $value, 'file' );
				break;

			case 'url':
			case 'email':
			case 'text':
			default:
				$input_type = 'email' === $type ? 'email' : 'text';
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="pdo-input" placeholder="%5$s" />',
					esc_attr( $input_type ),
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' )
				);
				break;
		}
	}

	/**
	 * Media Library picker.
	 *
	 * @param string $id    Element id.
	 * @param string $name  Input name.
	 * @param int    $value Attachment ID.
	 * @param string $mode  `image` or `file`.
	 * @return void
	 */
	public static function render_media_picker( $id, $name, $value, $mode = 'image' ) {
		$value   = (int) $value;
		$preview = '';
		$label   = '';
		if ( $value > 0 ) {
			if ( 'image' === $mode ) {
				$src = wp_get_attachment_image_url( $value, 'medium' );
				if ( $src ) {
					$preview = '<img src="' . esc_url( $src ) . '" alt="" />';
				}
			}
			$label = get_the_title( $value );
			if ( ! $preview ) {
				$url     = wp_get_attachment_url( $value );
				$preview = '<span class="pdo-media__file">' . esc_html( $label ) . '</span>';
				if ( $url ) {
					$preview .= '<a class="pdo-media__link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Open file', 'pdo-site-manager' ) . '</a>';
				}
			}
		}
		?>
		<div class="pdo-media" data-pdo-media data-mode="<?php echo esc_attr( $mode ); ?>">
			<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="pdo-media__input" />
			<div class="pdo-media__preview" <?php echo $value ? '' : 'hidden'; ?>><?php echo wp_kses_post( $preview ); ?></div>
			<div class="pdo-media__actions">
				<button type="button" class="button pdo-media__select">
					<?php echo 'image' === $mode ? esc_html__( 'Select image', 'pdo-site-manager' ) : esc_html__( 'Select file', 'pdo-site-manager' ); ?>
				</button>
				<button type="button" class="button-link pdo-media__remove" <?php echo $value ? '' : 'hidden'; ?>>
					<?php esc_html_e( 'Remove', 'pdo-site-manager' ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Icon picker.
	 *
	 * @param string $id    Element id.
	 * @param string $name  Input name.
	 * @param string $value Current icon.
	 * @return void
	 */
	public static function render_icon_picker( $id, $name, $value ) {
		$value = (string) $value;
		$known = $value && PDO_SM_Icons::exists( $value );
		?>
		<div class="pdo-iconpicker" data-pdo-iconpicker>
			<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="pdo-iconpicker__input" />
			<button type="button" class="button pdo-iconpicker__toggle" aria-haspopup="dialog" aria-expanded="false">
				<span class="pdo-iconpicker__current">
					<?php
					if ( $known ) {
						pdo_icon( $value, array( 'size' => 20 ) );
					} else {
						esc_html_e( 'Choose icon', 'pdo-site-manager' );
					}
					?>
				</span>
				<span class="pdo-iconpicker__name"><?php echo esc_html( $value ); ?></span>
			</button>
			<button type="button" class="button-link pdo-iconpicker__clear"<?php echo $value ? '' : ' hidden'; ?>><?php esc_html_e( 'Clear', 'pdo-site-manager' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Render a sortable repeater.
	 *
	 * @param array  $field     Field definition.
	 * @param array  $values    Values.
	 * @param string $name_base Base name.
	 * @return void
	 */
	public static function render_repeater( $field, $values, $name_base = 'pdo' ) {
		$key        = $field['key'];
		$rows       = isset( $values[ $key ] ) && is_array( $values[ $key ] ) ? $values[ $key ] : array();
		$rows       = PDO_SM_Helpers::sort_rows( $rows );
		$sub_fields = array_merge( $field['fields'], PDO_SM_Schema::row_meta_fields() );
		$item_label = isset( $field['item_label'] ) ? $field['item_label'] : '';
		$uid        = sanitize_html_class( str_replace( array( '[', ']' ), '-', $name_base . '-' . $key ) );
		?>
		<div class="pdo-repeater" data-pdo-repeater data-name="<?php echo esc_attr( $name_base . '[' . $key . ']' ); ?>" data-label-field="<?php echo esc_attr( $item_label ); ?>">
			<div class="pdo-repeater__head">
				<h4><?php echo esc_html( $field['label'] ); ?></h4>
				<span class="pdo-repeater__count"></span>
			</div>
			<?php if ( ! empty( $field['desc'] ) ) : ?>
				<p class="pdo-field__desc"><?php echo wp_kses_post( $field['desc'] ); ?></p>
			<?php endif; ?>

			<div class="pdo-repeater__rows" id="<?php echo esc_attr( $uid ); ?>-rows">
				<?php foreach ( $rows as $index => $row ) : ?>
					<?php self::render_repeater_row( $sub_fields, $row, $name_base . '[' . $key . '][' . $index . ']', $item_label, $index ); ?>
				<?php endforeach; ?>
			</div>

			<script type="text/html" class="pdo-repeater__template">
				<?php self::render_repeater_row( $sub_fields, array(), $name_base . '[' . $key . '][__INDEX__]', $item_label, '__INDEX__', true ); ?>
			</script>

			<p class="pdo-repeater__actions">
				<button type="button" class="button button-secondary pdo-repeater__add">
					<span aria-hidden="true">+</span>
					<?php echo esc_html( isset( $field['add_label'] ) ? $field['add_label'] : __( 'Add item', 'pdo-site-manager' ) ); ?>
				</button>
			</p>
		</div>
		<?php
	}

	/**
	 * Render a single repeater row.
	 *
	 * @param array  $sub_fields Sub field definitions.
	 * @param array  $row        Row values.
	 * @param string $name_base  Base name for this row.
	 * @param string $item_label Field key used as the row title.
	 * @param mixed  $index      Row index.
	 * @param bool   $is_tpl     Whether this is the JS template.
	 * @return void
	 */
	private static function render_repeater_row( $sub_fields, $row, $name_base, $item_label, $index, $is_tpl = false ) {
		$title = '';
		if ( $item_label && ! empty( $row[ $item_label ] ) ) {
			$title = wp_strip_all_tags( (string) $row[ $item_label ] );
		}
		$visible = ! array_key_exists( 'visible', $row ) || PDO_SM_Helpers::to_bool( $row['visible'] );
		?>
		<div class="pdo-repeater__row<?php echo $visible ? '' : ' is-hidden-row'; ?>" data-pdo-row>
			<div class="pdo-repeater__row-head">
				<span class="pdo-repeater__handle" title="<?php esc_attr_e( 'Drag to reorder', 'pdo-site-manager' ); ?>" aria-hidden="true">⠿</span>
				<button type="button" class="pdo-repeater__toggle" aria-expanded="false">
					<span class="pdo-repeater__title"><?php echo esc_html( $title ? $title : __( '(untitled item)', 'pdo-site-manager' ) ); ?></span>
				</button>
				<span class="pdo-repeater__flag"><?php esc_html_e( 'Hidden', 'pdo-site-manager' ); ?></span>
				<span class="pdo-repeater__row-actions">
					<button type="button" class="button-link pdo-repeater__duplicate" title="<?php esc_attr_e( 'Duplicate', 'pdo-site-manager' ); ?>"><?php esc_html_e( 'Duplicate', 'pdo-site-manager' ); ?></button>
					<button type="button" class="button-link pdo-repeater__remove" title="<?php esc_attr_e( 'Delete', 'pdo-site-manager' ); ?>"><?php esc_html_e( 'Delete', 'pdo-site-manager' ); ?></button>
				</span>
			</div>
			<div class="pdo-repeater__row-body" hidden>
				<?php
				if ( $is_tpl ) {
					// Templates must not run wp_editor(): downgrade rich text to a textarea.
					$sub_fields = array_map(
						static function ( $sub ) {
							if ( isset( $sub['type'] ) && 'richtext' === $sub['type'] ) {
								$sub['type'] = 'textarea';
								$sub['rows'] = 6;
							}
							return $sub;
						},
						$sub_fields
					);
				}
				self::render_fields( $sub_fields, $row, $name_base );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Hidden inputs listing every top-level field submitted by this form.
	 *
	 * Allows the save handler to reset unchecked checkboxes and emptied repeaters.
	 *
	 * @param array $fields Field definitions.
	 * @return void
	 */
	public static function render_field_registry( $fields ) {
		foreach ( $fields as $field ) {
			if ( empty( $field['key'] ) || 'heading' === $field['type'] ) {
				continue;
			}
			printf( '<input type="hidden" name="pdo_fields[]" value="%s" />', esc_attr( $field['key'] ) );
			if ( ! empty( $field['bilingual'] ) ) {
				printf( '<input type="hidden" name="pdo_fields[]" value="%s" />', esc_attr( $field['key'] . '_bn' ) );
			}
		}
	}
}
