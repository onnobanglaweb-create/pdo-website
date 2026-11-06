<?php
/**
 * Drag-and-drop ordering for every PDO content type.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

$pdo_types = array();
foreach ( PDO_SM_Content_Types::post_types() as $pdo_slug ) {
	$pdo_object = get_post_type_object( $pdo_slug );
	if ( $pdo_object ) {
		$pdo_types[ $pdo_slug ] = $pdo_object->labels->name;
	}
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$pdo_current = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : 'pdo_program';
if ( ! isset( $pdo_types[ $pdo_current ] ) ) {
	$pdo_current = (string) key( $pdo_types );
}

$pdo_posts = get_posts(
	array(
		'post_type'      => $pdo_current,
		'posts_per_page' => 200,
		'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	)
);
?>
<div class="wrap pdo-wrap">
	<h1 class="pdo-page-title"><?php esc_html_e( 'Reorder Content', 'pdo-site-manager' ); ?></h1>
	<p class="pdo-lede"><?php esc_html_e( 'Drag the rows to set the order used on the website. Changes save automatically.', 'pdo-site-manager' ); ?></p>

	<nav class="nav-tab-wrapper pdo-tabs">
		<?php foreach ( $pdo_types as $pdo_slug => $pdo_label ) : ?>
			<a class="nav-tab <?php echo $pdo_slug === $pdo_current ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=pdo-reorder&post_type=' . $pdo_slug ) ); ?>">
				<?php echo esc_html( $pdo_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<div class="pdo-panel">
		<div class="pdo-panel__body">
			<?php if ( ! $pdo_posts ) : ?>
				<p class="description"><?php esc_html_e( 'Nothing to reorder yet.', 'pdo-site-manager' ); ?></p>
			<?php else : ?>
				<ul class="pdo-reorder" data-pdo-reorder>
					<?php foreach ( $pdo_posts as $pdo_post ) : ?>
						<li class="pdo-reorder__item" data-id="<?php echo esc_attr( $pdo_post->ID ); ?>">
							<span class="pdo-reorder__handle" aria-hidden="true"><?php echo pdo_get_icon( 'grip' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="pdo-reorder__title"><?php echo esc_html( get_the_title( $pdo_post ) ); ?></span>
							<?php if ( 'publish' !== $pdo_post->post_status ) : ?>
								<span class="pdo-badge pdo-badge--draft"><?php echo esc_html( $pdo_post->post_status ); ?></span>
							<?php endif; ?>
							<a class="pdo-reorder__edit" href="<?php echo esc_url( (string) get_edit_post_link( $pdo_post->ID ) ); ?>"><?php esc_html_e( 'Edit', 'pdo-site-manager' ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="pdo-reorder__status" data-pdo-reorder-status aria-live="polite"></p>
			<?php endif; ?>
		</div>
	</div>
</div>
