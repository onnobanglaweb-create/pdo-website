<?php
/**
 * Range slider control with a live numeric readout.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'PDO_Range_Control' ) ) {

	/**
	 * Range control.
	 */
	class PDO_Range_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'pdo-range';

		/**
		 * Minimum value.
		 *
		 * @var float
		 */
		public $min = 0;

		/**
		 * Maximum value.
		 *
		 * @var float
		 */
		public $max = 100;

		/**
		 * Step.
		 *
		 * @var float
		 */
		public $step = 1;

		/**
		 * Unit suffix shown next to the number.
		 *
		 * @var string
		 */
		public $unit = '';

		/**
		 * Render the control.
		 *
		 * @return void
		 */
		public function render_content() {
			$id = '_customize-input-' . $this->id;
			?>
			<div class="pdo-range">
				<label class="pdo-range__label" for="<?php echo esc_attr( $id ); ?>">
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<output class="pdo-range__output" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $this->value() . $this->unit ); ?></output>
				</label>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
				<div class="pdo-range__row">
					<input
						type="range"
						id="<?php echo esc_attr( $id ); ?>"
						class="pdo-range__slider"
						min="<?php echo esc_attr( (string) $this->min ); ?>"
						max="<?php echo esc_attr( (string) $this->max ); ?>"
						step="<?php echo esc_attr( (string) $this->step ); ?>"
						value="<?php echo esc_attr( (string) $this->value() ); ?>"
						data-unit="<?php echo esc_attr( $this->unit ); ?>"
						<?php $this->link(); ?>
					/>
					<button type="button" class="button-link pdo-range__reset" title="<?php esc_attr_e( 'Reset to the default', 'pdo-modern' ); ?>" data-default="<?php echo esc_attr( (string) $this->setting->default ); ?>">
						<?php esc_html_e( 'Reset', 'pdo-modern' ); ?>
					</button>
				</div>
			</div>
			<?php
		}
	}
}
