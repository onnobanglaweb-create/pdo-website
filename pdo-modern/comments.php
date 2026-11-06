<?php
/**
 * Comments.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="pdo-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="pdo-comments__title">
			<?php
			$pdo_count = (int) get_comments_number();
			printf(
				/* translators: %s: comment count. */
				esc_html( _n( '%s comment', '%s comments', $pdo_count, 'pdo-modern' ) ),
				esc_html( number_format_i18n( $pdo_count ) )
			);
			?>
		</h2>

		<ol class="pdo-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'class'     => 'pdo-pagination',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="pdo-form__note"><?php esc_html_e( 'Comments are closed.', 'pdo-modern' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'         => 'pdo-form',
			'class_submit'       => 'pdo-btn pdo-btn--primary',
			'title_reply_before' => '<h2 class="pdo-comments__title">',
			'title_reply_after'  => '</h2>',
		)
	);
	?>
</div>
