<?php
/**
 * Snax Post Row Regular Price
 *
 * @package snax
 */

$wc_decimals = wc_get_price_decimals();
$number_steps = $wc_decimals > 0 ? 1 / pow( 10, $wc_decimals ) : 1;
?>

<div class="snax-edit-post-row-sale-price<?php echo snax_has_field_errors( 'sale-price' ) ? ' snax-validation-error' : ''; ?>">
	<label for="snax-post-price"><?php printf( esc_html__( 'Sale Price (%s)', 'snax' ), esc_html( get_woocommerce_currency_symbol() )); ?></label>

	<?php if ( snax_has_field_errors( 'sale-price' ) ) : ?>
		<span class="snax-validation-tip"><?php echo esc_html( snax_get_field_errors( 'sale-price' ) ); ?></span>
	<?php endif; ?>

	<input name="snax-post-sale-price"
	       id="snax-post-sale-price"
	       type="number"
	       min="0"
	       step="<?php echo esc_attr( $number_steps ); ?>"
	       value="<?php echo esc_attr( snax_get_field_values( 'sale-price' ) ); ?>"
	       placeholder="<?php echo esc_attr_x( '9.99', 'Snax External Product', 'snax' ); ?>"
	       autocomplete="off"
	       maxlength="20"
		/>
</div>
