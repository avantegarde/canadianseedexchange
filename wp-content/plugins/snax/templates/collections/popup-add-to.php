<?php
/**
 * Add to collection popup
 *
 * @package snax 1.19
 * @subpackage Collections
 */
$snax_count = snax_get_user_custom_collection_count();
$snax_placeholder = $snax_count ? __( 'Add new or search&hellip;', 'snax') : __( 'Add new&hellip;', 'snax' );
?>
<div id="snax-popup-add-to-collection" class="snax white-popup mfp-hide">
	<h2><?php esc_html_e( 'Add to Collection', 'snax' ); ?></h2>

	<div class="snax-add-to-collection"><!--  .snax-add-to-collection-loading -->
		<form class="snax-form-collection-search">
			<label>
				<?php esc_html_e( 'Add new or search', 'snax' ); ?>
				<input name="snax-collection-search" type="search" placeholder="<?php echo esc_attr( $snax_placeholder ); ?>" autocomplete="off" />
			</label>
			<input name="snax-collection-save" type="submit" value="<?php esc_attr_e( 'Save', 'snax' ); ?>" disabled="disabled" />
		</form>
		<div class="snax-collections snax-collections-tpl-listxs">
			<ul class="snax-collections-items">
				<li class="snax-collections-item">
					<div class="snax-collection snax-collection-tpl-listxs snax-collection-public">
						<p class="snax-collection-title"><a>Public collection title</a></p>
					</div>
				</li>

				<li class="snax-collections-item">
					<div class="snax-collection snax-collection-tpl-listxs snax-collection-private">
						<p class="snax-collection-title"><a>Private collection title</a></p>
					</div>
				</li>
			</ul>
		</div>
		<div class="snax-collections-leading">
			<div class="snax-collections-leading-icon"></div>
			<h3 class="snax-collections-leading-title"><?php esc_html_e( 'No Collections', 'snax' ); ?></h3>
			<p><?php esc_html_e( 'Here you\'ll find all collections you\'ve created before.', 'snax' ); ?></p>
		</div>
	</div>
</div>
