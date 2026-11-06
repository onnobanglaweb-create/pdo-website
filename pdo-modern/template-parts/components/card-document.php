<?php
/**
 * Component – document / report card.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $pdo_post ) {
	return;
}

$pdo_id    = (int) ( is_object( $pdo_post ) ? $pdo_post->ID : $pdo_post );
$pdo_title = pdo_post_title( $pdo_id );
$pdo_file  = pdo_document_file( $pdo_id );
$pdo_desc  = pdo_meta( $pdo_id, 'description', '' );
$pdo_year  = (string) pdo_raw_meta( $pdo_id, 'year', '' );
?>
<article class="pdo-doc" data-pdo-reveal>
	<span class="pdo-doc__icon">
		<?php pdo_icon( pdo_raw_meta( $pdo_id, 'icon', 'file-text' ), array( 'size' => 22 ) ); ?>
	</span>

	<div class="pdo-doc__body">
		<h3 class="pdo-doc__title"><?php echo esc_html( $pdo_title ); ?></h3>

		<?php if ( $pdo_desc ) : ?>
			<p class="pdo-doc__text"><?php echo esc_html( $pdo_desc ); ?></p>
		<?php endif; ?>

		<p class="pdo-meta">
			<?php if ( $pdo_year ) : ?>
				<span><?php pdo_icon( 'calendar', array( 'size' => 13 ) ); ?><?php echo esc_html( $pdo_year ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $pdo_file['ext'] ) ) : ?>
				<span><?php echo esc_html( $pdo_file['ext'] ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $pdo_file['size'] ) ) : ?>
				<span><?php echo esc_html( $pdo_file['size'] ); ?></span>
			<?php endif; ?>
		</p>

		<div class="pdo-doc__actions">
			<?php if ( ! empty( $pdo_file['url'] ) ) : ?>
				<a class="pdo-btn pdo-btn--primary pdo-btn--sm" href="<?php echo esc_url( $pdo_file['url'] ); ?>" download>
					<?php pdo_icon( 'download', array( 'size' => 15 ) ); ?>
					<span><?php echo esc_html( pdo_meta( $pdo_id, 'download_label', pdo_t( 'download', __( 'Download', 'pdo-modern' ) ) ) ); ?></span>
				</a>
				<?php if ( pdo_raw_meta( $pdo_id, 'preview_enabled', 1 ) ) : ?>
					<a class="pdo-btn pdo-btn--outline pdo-btn--sm" href="<?php echo esc_url( $pdo_file['url'] ); ?>" target="_blank" rel="noopener">
						<?php pdo_icon( 'eye', array( 'size' => 15 ) ); ?>
						<span><?php esc_html_e( 'Preview', 'pdo-modern' ); ?></span>
					</a>
				<?php endif; ?>
			<?php else : ?>
				<span class="pdo-doc__missing"><?php esc_html_e( 'Document available on request.', 'pdo-modern' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</article>
