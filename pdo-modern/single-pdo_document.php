<?php
/**
 * Single document / report.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pdo_id   = (int) get_the_ID();
	$pdo_file = pdo_document_file( $pdo_id );

	pdo_modern_section_open( array( 'class' => 'pdo-single pdo-single--document' ) );
	?>
	<article id="post-<?php echo esc_attr( (string) $pdo_id ); ?>" <?php post_class( 'pdo-entry pdo-container--narrow' ); ?>>
		<header class="pdo-entry__header">
			<p class="pdo-eyebrow">
				<?php pdo_icon( pdo_raw_meta( $pdo_id, 'icon', 'file-text' ), array( 'size' => 15 ) ); ?>
				<?php esc_html_e( 'Document', 'pdo-modern' ); ?>
			</p>
			<h1 class="pdo-section__title"><?php echo esc_html( pdo_post_title( $pdo_id ) ); ?></h1>

			<p class="pdo-meta">
				<?php if ( pdo_raw_meta( $pdo_id, 'year', '' ) ) : ?>
					<span><?php pdo_icon( 'calendar', array( 'size' => 14 ) ); ?><?php echo esc_html( pdo_raw_meta( $pdo_id, 'year', '' ) ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $pdo_file['ext'] ) ) : ?>
					<span><?php echo esc_html( $pdo_file['ext'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $pdo_file['size'] ) ) : ?>
					<span><?php echo esc_html( $pdo_file['size'] ); ?></span>
				<?php endif; ?>
			</p>
		</header>

		<?php if ( pdo_meta( $pdo_id, 'description', '' ) ) : ?>
			<p class="pdo-section__lede"><?php echo esc_html( pdo_meta( $pdo_id, 'description', '' ) ); ?></p>
		<?php endif; ?>

		<div class="pdo-prose">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
			echo pdo_post_content( $pdo_id );
			?>
		</div>

		<?php if ( ! empty( $pdo_file['url'] ) ) : ?>
			<div class="pdo-btn-group pdo-doc__actions">
				<a class="pdo-btn pdo-btn--primary pdo-btn--lg" href="<?php echo esc_url( $pdo_file['url'] ); ?>" download>
					<?php pdo_icon( 'download', array( 'size' => 16 ) ); ?>
					<span><?php echo esc_html( pdo_meta( $pdo_id, 'download_label', __( 'Download the document', 'pdo-modern' ) ) ); ?></span>
				</a>
				<a class="pdo-btn pdo-btn--outline pdo-btn--lg" href="<?php echo esc_url( $pdo_file['url'] ); ?>" target="_blank" rel="noopener">
					<?php pdo_icon( 'eye', array( 'size' => 16 ) ); ?>
					<span><?php esc_html_e( 'Open in a new tab', 'pdo-modern' ); ?></span>
				</a>
			</div>

			<?php if ( pdo_raw_meta( $pdo_id, 'preview_enabled', 1 ) && ! empty( $pdo_file['mime'] ) && 'application/pdf' === $pdo_file['mime'] ) : ?>
				<div class="pdo-map">
					<iframe
						src="<?php echo esc_url( $pdo_file['url'] ); ?>#view=FitH"
						title="<?php echo esc_attr( pdo_post_title( $pdo_id ) ); ?>"
						loading="lazy"
						style="min-height:700px"
					></iframe>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<p class="pdo-alert pdo-alert--info">
				<?php pdo_icon( 'info', array( 'size' => 18 ) ); ?>
				<span><?php esc_html_e( 'This document is available on request. Please contact us for a copy.', 'pdo-modern' ); ?></span>
			</p>
		<?php endif; ?>
	</article>
	<?php
	pdo_modern_section_close();

endwhile;

get_footer();
