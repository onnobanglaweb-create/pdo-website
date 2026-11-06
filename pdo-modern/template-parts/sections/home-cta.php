<?php
/**
 * Homepage – closing call to action.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_title = pdo_text( 'home', 'cta_title', '' );
$pdo_desc  = pdo_text( 'home', 'cta_description', '' );

if ( ! $pdo_title && ! $pdo_desc ) {
	return;
}

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-cta',
		'class'      => 'pdo-cta',
		'visibility' => pdo_modern_visibility_class( 'cta' ),
		'label'      => 'pdo-cta-title',
	)
);
?>
<div class="pdo-cta__inner" data-pdo-reveal>
	<?php if ( $pdo_title ) : ?>
		<h2 id="pdo-cta-title"><?php echo esc_html( $pdo_title ); ?></h2>
	<?php endif; ?>

	<?php if ( $pdo_desc ) : ?>
		<p><?php echo esc_html( $pdo_desc ); ?></p>
	<?php endif; ?>

	<div class="pdo-btn-group">
		<?php
		pdo_modern_button(
			pdo_text( 'home', 'cta_btn1_label', '' ),
			pdo_setting( 'home', 'cta_btn1_url', '' ),
			array(
				'style' => 'light',
				'icon'  => 'hand-heart',
			)
		);
		pdo_modern_button(
			pdo_text( 'home', 'cta_btn2_label', '' ),
			pdo_setting( 'home', 'cta_btn2_url', '' ),
			array(
				'style' => 'onDark',
				'icon'  => 'mail',
			)
		);
		?>
	</div>
</div>
<?php
pdo_modern_section_close();
