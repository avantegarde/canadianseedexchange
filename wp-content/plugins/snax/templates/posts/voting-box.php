<?php
/**
 * Snax Post Voting Box Template Part
 *
 * @package snax 1.11
 * @subpackage Theme
 */

?>

<?php if ( snax_show_item_voting_box() ) : ?>
<div class="snax-voting-container">
	<h2 class="snax-voting-container-title"><?php esc_html_e( 'Leave your vote', 'snax' ); ?></h2>
	<?php
		snax_render_voting_box( null, 0, array(
			'class'                         => 'snax-voting-large',
			'show_member_profile_page_link' => true,
		) );
	?>
</div>
<?php endif; ?>
