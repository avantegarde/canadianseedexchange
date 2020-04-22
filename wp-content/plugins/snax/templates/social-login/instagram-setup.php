<?php
/**
 * Facebook setup
 *
 * @package Snax
 */

$config = snax_slog_get_config();
?>
<br />
<?php echo wp_kses_post( _x( 'During the application creation, you will be asked to provide this <strong>Valid redirect URIs</strong>:', 'Social Login Settings', 'snax' ) ); ?>
<br />
<code>
	<?php echo esc_url( $config[ 'Instagram' ]['callback'] ); ?>
</code>
