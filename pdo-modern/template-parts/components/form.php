<?php
/**
 * Component – a PDO Site Manager form (contact / volunteer / partner).
 *
 * Progressive enhancement only: the form posts to admin-post.php and works
 * with JavaScript disabled.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_type = isset( $args['type'] ) ? sanitize_key( $args['type'] ) : 'contact';
$pdo_cfg  = pdo_form_config( $pdo_type );

if ( ! $pdo_cfg || empty( $pdo_cfg['fields'] ) ) {
	return;
}

$pdo_result = pdo_form_result( $pdo_type );
$pdo_errors = is_array( $pdo_result ) && ! empty( $pdo_result['errors'] ) ? (array) $pdo_result['errors'] : array();
$pdo_values = is_array( $pdo_result ) && ! empty( $pdo_result['values'] ) ? (array) $pdo_result['values'] : array();
$pdo_done   = is_array( $pdo_result ) && ! empty( $pdo_result['success'] );
?>
<div class="pdo-form-wrap" id="pdo-form-<?php echo esc_attr( $pdo_type ); ?>">
	<?php if ( is_array( $pdo_result ) && ! empty( $pdo_result['message'] ) ) : ?>
		<div class="pdo-alert pdo-alert--<?php echo $pdo_done ? 'success' : 'error'; ?>" data-pdo-form-result role="status">
			<?php pdo_icon( $pdo_done ? 'check-circle' : 'alert-circle', array( 'size' => 18 ) ); ?>
			<span><?php echo esc_html( $pdo_result['message'] ); ?></span>
		</div>
	<?php endif; ?>

	<?php if ( $pdo_done ) : ?>
		<p class="pdo-form__note">
			<?php echo esc_html( pdo_text( 'org', 'response_time', __( 'We usually reply within a few working days.', 'pdo-modern' ) ) ); ?>
		</p>
	<?php else : ?>
		<form
			class="pdo-form"
			method="post"
			action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			data-pdo-form="<?php echo esc_attr( $pdo_type ); ?>"
			novalidate
		>
			<?php if ( ! empty( $pdo_cfg['title'] ) ) : ?>
				<h3 class="pdo-form__title"><?php echo esc_html( $pdo_cfg['title'] ); ?></h3>
			<?php endif; ?>

			<div class="pdo-form__grid">
				<?php foreach ( $pdo_cfg['fields'] as $pdo_index => $pdo_field ) : ?>
					<?php
					$pdo_name     = pdo_form_field_name( $pdo_field, $pdo_index );
					$pdo_id       = 'pdo-' . $pdo_type . '-' . $pdo_name;
					$pdo_label    = pdo_row_text( $pdo_field, 'label', $pdo_name );
					$pdo_hint     = pdo_row_text( $pdo_field, 'placeholder', '' );
					$pdo_required = ! empty( $pdo_field['required'] );
					$pdo_kind     = isset( $pdo_field['type'] ) ? (string) $pdo_field['type'] : 'text';
					$pdo_width    = isset( $pdo_field['width'] ) && 'half' === $pdo_field['width'] ? 'half' : 'full';
					$pdo_value    = isset( $pdo_values[ $pdo_name ] ) ? (string) $pdo_values[ $pdo_name ] : '';
					$pdo_error    = isset( $pdo_errors[ $pdo_name ] ) ? (string) $pdo_errors[ $pdo_name ] : '';
					$pdo_choices  = pdo_form_field_choices( $pdo_field );
					?>
					<div class="pdo-field pdo-field--<?php echo esc_attr( $pdo_width ); ?><?php echo $pdo_error ? ' has-error' : ''; ?><?php echo 'checkbox' === $pdo_kind ? ' pdo-field--checkbox' : ''; ?>">
						<?php if ( 'checkbox' !== $pdo_kind ) : ?>
							<label class="pdo-field__label" for="<?php echo esc_attr( $pdo_id ); ?>">
								<?php echo esc_html( $pdo_label ); ?>
								<?php if ( $pdo_required ) : ?>
									<span class="pdo-field__required" aria-hidden="true">*</span>
								<?php endif; ?>
							</label>
						<?php endif; ?>

						<?php if ( 'textarea' === $pdo_kind ) : ?>
							<textarea
								id="<?php echo esc_attr( $pdo_id ); ?>"
								name="pdo_field[<?php echo esc_attr( $pdo_name ); ?>]"
								placeholder="<?php echo esc_attr( $pdo_hint ); ?>"
								<?php echo $pdo_required ? 'required' : ''; ?>
								<?php echo $pdo_error ? 'aria-describedby="' . esc_attr( $pdo_id ) . '-error"' : ''; ?>
								<?php echo 'message' === $pdo_name ? 'data-pdo-prefill-template="' . esc_attr__( 'I would like to know more about: %s', 'pdo-modern' ) . '"' : ''; ?>
							><?php echo esc_textarea( $pdo_value ); ?></textarea>

						<?php elseif ( in_array( $pdo_kind, array( 'select', 'radio' ), true ) ) : ?>
							<select
								id="<?php echo esc_attr( $pdo_id ); ?>"
								name="pdo_field[<?php echo esc_attr( $pdo_name ); ?>]"
								<?php echo $pdo_required ? 'required' : ''; ?>
							>
								<option value=""><?php echo esc_html( $pdo_hint ? $pdo_hint : __( 'Please choose…', 'pdo-modern' ) ); ?></option>
								<?php foreach ( $pdo_choices as $pdo_choice ) : ?>
									<option value="<?php echo esc_attr( $pdo_choice ); ?>" <?php selected( $pdo_value, $pdo_choice ); ?>>
										<?php echo esc_html( $pdo_choice ); ?>
									</option>
								<?php endforeach; ?>
							</select>

						<?php elseif ( 'checkbox' === $pdo_kind ) : ?>
							<input
								type="checkbox"
								id="<?php echo esc_attr( $pdo_id ); ?>"
								name="pdo_field[<?php echo esc_attr( $pdo_name ); ?>]"
								value="1"
								<?php checked( $pdo_value, '1' ); ?>
								<?php echo $pdo_required ? 'required' : ''; ?>
							/>
							<label class="pdo-field__label" for="<?php echo esc_attr( $pdo_id ); ?>">
								<?php echo esc_html( $pdo_label ); ?>
								<?php if ( $pdo_required ) : ?>
									<span class="pdo-field__required" aria-hidden="true">*</span>
								<?php endif; ?>
							</label>

						<?php else : ?>
							<input
								type="<?php echo esc_attr( in_array( $pdo_kind, array( 'email', 'url', 'tel', 'number', 'date' ), true ) ? $pdo_kind : 'text' ); ?>"
								id="<?php echo esc_attr( $pdo_id ); ?>"
								name="pdo_field[<?php echo esc_attr( $pdo_name ); ?>]"
								value="<?php echo esc_attr( $pdo_value ); ?>"
								placeholder="<?php echo esc_attr( $pdo_hint ); ?>"
								<?php echo $pdo_required ? 'required' : ''; ?>
								<?php echo $pdo_error ? 'aria-describedby="' . esc_attr( $pdo_id ) . '-error"' : ''; ?>
							/>
						<?php endif; ?>

						<?php if ( $pdo_error ) : ?>
							<span class="pdo-field__error" id="<?php echo esc_attr( $pdo_id ); ?>-error"><?php echo esc_html( $pdo_error ); ?></span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php /* Spam traps. */ ?>
			<div class="pdo-honeypot" aria-hidden="true">
				<label for="pdo-website-<?php echo esc_attr( $pdo_type ); ?>"><?php esc_html_e( 'Leave this field empty', 'pdo-modern' ); ?></label>
				<input type="text" id="pdo-website-<?php echo esc_attr( $pdo_type ); ?>" name="pdo_website" value="" tabindex="-1" autocomplete="off" />
			</div>

			<p class="pdo-form__note" data-pdo-prefill-notice hidden data-template="<?php esc_attr_e( 'Selected project: %s', 'pdo-modern' ); ?>"></p>

			<input type="hidden" name="action" value="pdo_sm_submit" />
			<input type="hidden" name="pdo_form" value="<?php echo esc_attr( $pdo_type ); ?>" />
			<input type="hidden" name="pdo_project" value="" />
			<input type="hidden" name="pdo_ts" value="<?php echo esc_attr( (string) time() ); ?>" />
			<input type="hidden" name="_pdo_redirect" value="<?php echo esc_url( pdo_modern_current_url() ); ?>" />
			<?php wp_nonce_field( 'pdo_sm_form_' . $pdo_type, '_pdo_nonce' ); ?>

			<div class="pdo-form__footer">
				<button type="submit" class="pdo-btn pdo-btn--primary pdo-btn--lg">
					<?php pdo_icon( 'send', array( 'size' => 16 ) ); ?>
					<span><?php echo esc_html( $pdo_cfg['submit'] ); ?></span>
				</button>
				<p class="pdo-form__note">
					<?php pdo_icon( 'lock', array( 'size' => 14 ) ); ?>
					<?php esc_html_e( 'Your details are stored securely and never shared with third parties.', 'pdo-modern' ); ?>
				</p>
			</div>
		</form>
	<?php endif; ?>
</div>
