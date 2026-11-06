<?php
/**
 * Homepage – message from the President.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_quote = pdo_text( 'home', 'president_quote', '' );
$pdo_name  = pdo_text( 'home', 'president_name', '' );

if ( ! $pdo_quote && ! $pdo_name ) {
	return;
}

$pdo_photo = pdo_image(
	(int) pdo_setting( 'home', 'president_photo', 0 ),
	pdo_setting( 'home', 'president_photo_url', '' ),
	array(
		'size' => 'pdo-portrait',
		'alt'  => $pdo_name,
	)
);

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-president',
		'class'      => 'pdo-section-president',
		'background' => 'dark',
		'visibility' => pdo_modern_visibility_class( 'president' ),
		'label'      => 'pdo-president-title',
	)
);
?>
<div class="pdo-split">
	<?php if ( $pdo_photo ) : ?>
		<figure class="pdo-president__photo">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_photo;
			?>
		</figure>
	<?php endif; ?>

	<div class="pdo-president__body">
		<?php if ( pdo_text( 'home', 'president_badge', '' ) ) : ?>
			<p class="pdo-eyebrow">
				<?php pdo_icon( 'quote', array( 'size' => 15 ) ); ?>
				<?php echo esc_html( pdo_text( 'home', 'president_badge', '' ) ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $pdo_quote ) : ?>
			<blockquote class="pdo-president__quote" id="pdo-president-title">
				<p><?php echo esc_html( $pdo_quote ); ?></p>
				<?php if ( $pdo_name ) : ?>
					<cite>
						<strong><?php echo esc_html( $pdo_name ); ?></strong>
						<?php if ( pdo_text( 'home', 'president_role', '' ) ) : ?>
							<span> — <?php echo esc_html( pdo_text( 'home', 'president_role', '' ) ); ?></span>
						<?php endif; ?>
					</cite>
				<?php endif; ?>
			</blockquote>
		<?php endif; ?>

		<?php
		pdo_modern_button(
			pdo_text( 'home', 'president_btn_label', '' ),
			pdo_setting( 'home', 'president_btn_url', '' ),
			array(
				'style' => 'light',
				'icon'  => 'arrow-right',
			)
		);
		?>
	</div>
</div>
<?php
pdo_modern_section_close();
