<?php
/**
 * Notification for a new post publication
 */

$snax_data = get_query_var( 'snax_notification_data' );
?>
<div>
	<p>
		<?php _ex( 'Hello, admin', 'Mail notification', 'snax' ); ?>
	</p>
	<p>
		<?php printf( _x( 'New post (%1$s) was published: %2$s', 'Mail notification', 'snax' ), $snax_data['format'], $snax_data['link'] ); ?>
	</p>
</div>
