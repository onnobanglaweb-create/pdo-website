<?php
/**
 * Component – OpenStreetMap embed (lazy loaded, no third-party JS).
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( ! pdo_setting( 'org', 'map_enabled', true ) ) {
	return;
}

$pdo_embed = trim( (string) pdo_setting( 'org', 'map_embed', '' ) );
$pdo_lat   = trim( (string) pdo_setting( 'org', 'map_lat', '' ) );
$pdo_lng   = trim( (string) pdo_setting( 'org', 'map_lng', '' ) );
$pdo_bbox  = trim( (string) pdo_setting( 'org', 'map_zoom_bbox', '' ) );
$pdo_link  = (string) pdo_setting( 'org', 'map_link', '' );
$pdo_pin   = pdo_text( 'org', 'map_pin_label', '' );
$pdo_note  = pdo_text( 'org', 'map_caption', '' );

$pdo_src = '';

if ( $pdo_embed && false !== strpos( $pdo_embed, 'http' ) ) {
	// The administrator pasted a full iframe or a bare URL – keep only the src.
	if ( preg_match( '/src=["\']([^"\']+)["\']/', $pdo_embed, $pdo_match ) ) {
		$pdo_src = $pdo_match[1];
	} elseif ( 0 === strpos( $pdo_embed, 'http' ) ) {
		$pdo_src = $pdo_embed;
	}
} elseif ( $pdo_bbox ) {
	$pdo_src = 'https://www.openstreetmap.org/export/embed.html?bbox=' . rawurlencode( $pdo_bbox ) . '&layer=mapnik';
	if ( $pdo_lat && $pdo_lng ) {
		$pdo_src .= '&marker=' . rawurlencode( $pdo_lat . ',' . $pdo_lng );
	}
}

if ( ! $pdo_src ) {
	return;
}

if ( ! $pdo_link && $pdo_lat && $pdo_lng ) {
	$pdo_link = 'https://www.openstreetmap.org/?mlat=' . rawurlencode( $pdo_lat ) . '&mlon=' . rawurlencode( $pdo_lng ) . '#map=13/' . rawurlencode( $pdo_lat ) . '/' . rawurlencode( $pdo_lng );
}
?>
<div class="pdo-map">
	<div
		class="pdo-map__frame"
		data-pdo-map-src="<?php echo esc_url( $pdo_src ); ?>"
		data-pdo-map-title="<?php echo esc_attr( $pdo_pin ? $pdo_pin : __( 'Office location', 'pdo-modern' ) ); ?>"
	>
		<noscript>
			<iframe src="<?php echo esc_url( $pdo_src ); ?>" title="<?php echo esc_attr( $pdo_pin ? $pdo_pin : __( 'Office location', 'pdo-modern' ) ); ?>" loading="lazy"></iframe>
		</noscript>
	</div>

	<?php if ( $pdo_pin || $pdo_note || $pdo_link ) : ?>
		<p class="pdo-map__caption">
			<span>
				<?php if ( $pdo_pin ) : ?>
					<strong><?php echo esc_html( $pdo_pin ); ?></strong>
				<?php endif; ?>
				<?php echo esc_html( $pdo_note ); ?>
			</span>
			<?php if ( $pdo_link ) : ?>
				<a class="pdo-btn pdo-btn--outline pdo-btn--sm" href="<?php echo esc_url( $pdo_link ); ?>" target="_blank" rel="noopener">
					<?php pdo_icon( 'external-link', array( 'size' => 14 ) ); ?>
					<span><?php esc_html_e( 'Open in OpenStreetMap', 'pdo-modern' ); ?></span>
				</a>
			<?php endif; ?>
		</p>
	<?php endif; ?>
</div>
