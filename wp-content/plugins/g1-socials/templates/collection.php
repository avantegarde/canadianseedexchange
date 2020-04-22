<?php
global $g1_socials_items;
global $g1_socials_shortcode;
?>
<?php if ( is_array( $g1_socials_items ) ) : ?>
<ul id="<?php echo esc_attr( $g1_socials_shortcode['final_id'] ); ?>" class="g1-socials-items g1-socials-items-tpl-grid">
	<?php foreach ( $g1_socials_items as $g1_socials_item ) : ?>
	<?php
		$g1_icon_class = array(
			'g1-socials-item-icon',
			'g1-socials-item-icon-' . $g1_socials_shortcode['icon_size'],
			'g1-socials-item-icon-' . $g1_socials_shortcode['icon_color'],
			'g1-socials-item-icon-' . $g1_socials_item->id,
		);
	?>
	<li class="g1-socials-item g1-socials-item-<?php echo sanitize_html_class( $g1_socials_item->id ); ?>">
	   <a class="g1-socials-item-link" href="<?php echo esc_url( $g1_socials_item->url ); ?>" target="_blank" rel="noopener">
		   <span class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $g1_icon_class ) ); ?>"></span>
		   <span class="g1-socials-item-tooltip">
			   <span class="g1-socials-item-tooltip-inner"><?php echo esc_html( $g1_socials_item->name ); ?></span>
		   </span>
	   </a>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
