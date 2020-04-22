<?php
/**
 * New post form
 *
 * @package snax 1.11
 * @subpackage Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<?php if ( 1 < snax_get_format_count() ) : ?>
	<?php
	$snax_class = array(
		'snax-formats',
		'snax-formats-' . snax_get_format_count(),
	);
	?>
	<ul class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_class ) ); ?>">
		<?php foreach ( snax_get_active_formats( 'object' ) as $snax_format_id => $snax_format ) : ?>
			<li>
				<a <?php snax_render_format_class( array(), $snax_format_id ); ?> href="<?php echo esc_url( $snax_format->get_url() ); ?>">
					<i class="snax-format-icon"></i>
					<h3 class="snax-format-label"><?php echo esc_html( $snax_format->get_labels()['name'] ); ?></h3>
					<p class="snax-format-desc"><?php echo esc_html( $snax_format->get_description() ); ?></p>

					<?php do_action( 'snax_end_format', $snax_format ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
