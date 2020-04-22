<?php
/**
 * Options Page
 *
 * @package G1 Socials
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<p><?php esc_html_e( 'Fill a social channel row to make it active. Drag and drop to reorder.', 'g1_socials' ); ?></p>

<table id="g1-social-icons" class="g1-social-icons-table">
	<thead>
		<tr>
			<th colspan="2"><?php esc_html_e( 'Name', 'g1_socials' ); ?></th>
			<th><?php esc_html_e( 'Label', 'g1_socials' ); ?></th>
			<th><?php esc_html_e( 'Caption', 'g1_socials' ); ?></th>
			<th><?php esc_html_e( 'Link', 'g1_socials' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( G1_Socials()->get_items() as $name => $data ) :
			$value            = G1_Socials_Admin()->get_item_value( $name );
			$base_option_name = sprintf( '%s[%s]', G1_Socials()->get_option_name(), $name );
		?>
			<tr>
			<td>
				<span class="g1-socials-item-icon g1-socials-item-icon-<?php echo sanitize_html_class( $name ); ?>" title="<?php esc_attr( $name ); ?>"></span>
			</td>
			<td>
				<?php echo esc_html( $name ); ?>
			</td>
			<td>
				<input type="text" name="<?php echo esc_attr( $base_option_name . '[label]' ); ?>" value="<?php echo esc_attr( $value['label'] ); ?>" placeholder="<?php esc_attr_e( 'label&hellip;', 'g1_socials' ); ?>" />
			</td>
			<td>
				<input type="text" name="<?php echo esc_attr( $base_option_name . '[caption]' ); ?>" value="<?php echo esc_attr( $value['caption'] ); ?>" placeholder="<?php esc_attr_e( 'caption&hellip;', 'g1_socials' ); ?>" />
			</td>
			<td>
				<input type="text" name="<?php echo esc_attr( $base_option_name . '[link]' ); ?>" value="<?php echo esc_attr( $value['link'] ); ?>" placeholder="<?php echo esc_attr_e( 'link&hellip;', 'g1_socials' ); ?>" />
			</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script type="text/javascript">
(function($) {
	$(document).ready(function() {
		$( '#g1-social-icons tbody' ).sortable();
		$( '#g1-social-icons tbody tr' ).each(function () {
			var $this = $(this);

			$this.mouseover(function () {
				$this.addClass('g1-hover');
			});

			$this.mouseout(function () {
				$this.removeClass('g1-hover');
			});
		});
	});
})(jQuery);
</script>
