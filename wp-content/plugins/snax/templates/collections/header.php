<?php
/**
 * Snax Collection Header Template.
 *
 * @package snax
 * @subpackage Collections
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$snax_class = array(
	'snax-collections-title',
);
?>
<header class="snax-collections-header">
	<?php if ( strlen( $snax_collections_title ) ) : ?>
		<h2 class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_class ) ); ?>"><?php echo esc_html( $snax_collections_title ); ?></h2>
	<?php endif; ?>
</header>
