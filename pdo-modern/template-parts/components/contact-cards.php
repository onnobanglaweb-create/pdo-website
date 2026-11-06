<?php
/**
 * Component – contact information cards.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_address  = pdo_text( 'org', 'address', '' );
$pdo_office   = pdo_text( 'org', 'office_name', '' );
$pdo_phone    = pdo_setting( 'org', 'phone', '' );
$pdo_whatsapp = pdo_setting( 'org', 'whatsapp', '' );
$pdo_email    = pdo_setting( 'org', 'email', '' );
$pdo_hours    = pdo_text( 'org', 'office_hours', '' );

$pdo_cards = array();

if ( $pdo_address ) {
	$pdo_cards[] = array(
		'icon'  => 'map-pin',
		'title' => $pdo_office ? $pdo_office : __( 'Head office', 'pdo-modern' ),
		'lines' => array( array( 'text' => $pdo_address ) ),
	);
}

if ( $pdo_phone || $pdo_whatsapp ) {
	$pdo_lines = array();
	if ( $pdo_phone ) {
		$pdo_lines[] = array(
			'text' => $pdo_phone,
			'url'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $pdo_phone ),
		);
	}
	if ( $pdo_whatsapp ) {
		$pdo_lines[] = array(
			'text' => __( 'WhatsApp', 'pdo-modern' ) . ' · ' . $pdo_whatsapp,
			'url'  => 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $pdo_whatsapp ),
		);
	}
	$pdo_cards[] = array(
		'icon'  => 'phone',
		'title' => __( 'Call or message', 'pdo-modern' ),
		'lines' => $pdo_lines,
	);
}

if ( $pdo_email ) {
	$pdo_cards[] = array(
		'icon'  => 'mail',
		'title' => __( 'Email', 'pdo-modern' ),
		'lines' => array(
			array(
				'text' => $pdo_email,
				'url'  => 'mailto:' . $pdo_email,
			),
		),
	);
}

if ( $pdo_hours ) {
	$pdo_cards[] = array(
		'icon'  => 'clock',
		'title' => __( 'Office hours', 'pdo-modern' ),
		'lines' => array( array( 'text' => $pdo_hours ) ),
	);
}

if ( ! $pdo_cards ) {
	return;
}
?>
<div class="pdo-grid pdo-grid--auto">
	<?php foreach ( $pdo_cards as $pdo_card ) : ?>
		<div class="pdo-card pdo-card--icon" data-pdo-reveal>
			<div class="pdo-card__body">
				<span class="pdo-card__icon"><?php pdo_icon( $pdo_card['icon'], array( 'size' => 22 ) ); ?></span>
				<h3 class="pdo-card__title"><?php echo esc_html( $pdo_card['title'] ); ?></h3>
				<?php foreach ( $pdo_card['lines'] as $pdo_line ) : ?>
					<p class="pdo-card__text">
						<?php if ( ! empty( $pdo_line['url'] ) ) : ?>
							<a href="<?php echo esc_url( $pdo_line['url'] ); ?>"><?php echo nl2br( esc_html( $pdo_line['text'] ) ); ?></a>
						<?php else : ?>
							<?php echo nl2br( esc_html( $pdo_line['text'] ) ); ?>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
