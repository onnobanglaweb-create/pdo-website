<?php
/**
 * Drag-and-drop ordering control with per-item visibility checkboxes.
 *
 * The value is stored as a comma separated list of keys. A key prefixed with
 * an underscore is disabled, e.g. "hero,stats,_gallery,cta".
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'PDO_Sortable_Control' ) ) {

	/**
	 * Sortable control.
	 */
	class PDO_Sortable_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'pdo-sortable';

		/**
		 * Render the control.
		 *
		 * @return void
		 */
		public function render_content() {
			if ( empty( $this->choices ) ) {
				return;
			}

			$value = (string) $this->value();
			$order = array_filter( array_map( 'trim', explode( ',', $value ) ) );

			// Make sure every known choice is present.
			foreach ( array_keys( $this->choices ) as $slug ) {
				if ( ! in_array( $slug, $order, true ) && ! in_array( '_' . $slug, $order, true ) ) {
					$order[] = $slug;
				}
			}
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>

			<ul class="pdo-sortable" data-pdo-sortable>
				<?php
				foreach ( $order as $slug ) {
					$enabled = 0 !== strpos( $slug, '_' );
					$bare    = ltrim( $slug, '_' );
					if ( ! isset( $this->choices[ $bare ] ) ) {
						continue;
					}
					?>
					<li class="pdo-sortable__item<?php echo $enabled ? '' : ' is-disabled'; ?>" data-slug="<?php echo esc_attr( $bare ); ?>">
						<span class="pdo-sortable__handle dashicons dashicons-menu" aria-hidden="true"></span>
						<label class="pdo-sortable__label">
							<input type="checkbox" class="pdo-sortable__toggle" <?php checked( $enabled ); ?> />
							<span><?php echo esc_html( $this->choices[ $bare ] ); ?></span>
						</label>
					</li>
					<?php
				}
				?>
			</ul>

			<input type="hidden" class="pdo-sortable__value" value="<?php echo esc_attr( implode( ',', $order ) ); ?>" <?php $this->link(); ?> />
			<?php
		}
	}
}
