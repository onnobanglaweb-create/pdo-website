<?php
/**
 * Non-interactive separator / heading inside a Customizer section.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'PDO_Heading_Control' ) ) {

	/**
	 * Heading control.
	 */
	class PDO_Heading_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'pdo-heading';

		/**
		 * Render the control.
		 *
		 * @return void
		 */
		public function render_content() {
			?>
			<div class="pdo-customize-heading">
				<h3><?php echo esc_html( $this->label ); ?></h3>
				<?php if ( $this->description ) : ?>
					<p class="description"><?php echo wp_kses_post( $this->description ); ?></p>
				<?php endif; ?>
			</div>
			<?php
		}
	}
}
