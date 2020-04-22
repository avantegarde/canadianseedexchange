<?php
/**
 * BuddyPress - Groups Cover Image Header.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_group_header' ); ?>

<div id="cover-image-container" class="g1-row-layout-page bp-layout-standard">
	<span id="header-cover-image"></span>

	<?php if ( bimber_bp_show_group_cover_image_change_link() ) : ?>
		<?php bimber_bp_render_group_cover_image_change_link(); ?>
	<?php endif; ?>

	<div id="item-header-cover-image">
	</div><!-- #item-header-cover-image -->

	<?php bp_get_template_part( 'groups/single/group-header' ); ?>
</div><!-- #cover-image-container -->

<?php

/**
 * Fires after the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_group_header' );
