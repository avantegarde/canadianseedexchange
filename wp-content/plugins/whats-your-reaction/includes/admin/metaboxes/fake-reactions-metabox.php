<?php
/**
 * Fake Votes Metabox
 *
 * @package wyr
 * @subpackage Metaboxes
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Register metabox
 *
 * @param string  $post_type    Post type.
 * @param WP_Post $post         Post object.
 */
function wyr_add_fake_reactions_metabox( $post_type, $post ) {
	if ( 'post' !== $post_type ) {
		return;
	}
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		return;
	}
	add_meta_box(
		'wyr_fake_reactions',
		__( 'Fake Reactions', 'wyr' ),
		'wyr_fake_reactions_metabox',
		$post_type,
		'normal'
	);

	do_action( 'wyr_register_fake_reactions_metabox' );
}

/**
 * Render metabox
 *
 * @param WP_Post $post         Post object.
 */
function wyr_fake_reactions_metabox( $post ) {
	// Secure the form with nonce field.
	wp_nonce_field(
		'wyr_fake_reactions',
		'wyr_fake_reactions_nonce'
	);

	$value = get_post_meta( $post->ID, '_wyr_fake_reaction_count', true );
	?>
	<div id="wyr-metabox">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="_wyr_fake_reaction_count">
						<?php esc_html_e( 'Fake reaction count', 'wyr' ); ?>
					</label>
				</th>
				<td>
					<input type="number" id="_wyr_fake_reaction_count" name="_wyr_fake_reaction_count" value="<?php echo esc_attr( $value ) ?>" size="5" />
					<span class="description"><?php esc_html_e( 'Leave empty to use global settings.', 'wyr' ); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="_wyr_fake_reaction_count">
						<?php esc_html_e( 'Disable fakes for:', 'wyr' ); ?>
					</label>
				</th>
			</tr>
			<?php
			$terms 	= wyr_get_reactions();
			$values = get_post_meta( $post->ID, '_wyr_disable_fakes_for_reactions', true );
			if ( ! is_array( $values )) {
				$values = array();
			}
			if ( ! empty( $terms ) ) :
				foreach ( $terms as $term ) :
					$name 	= $term->name;
					$slug 	= $term->slug;
			?>
			<tr>
				<th scope="row">
					<label>
						<?php echo esc_html( $name ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" id="_wyr_disable_fakes_for_<?php echo esc_html( $slug ); ?>" name="_wyr_disable_fakes_for_<?php echo esc_html( $slug ); ?>" 
					<?php
					$values[ $slug ] = ( isset( $values[ $slug ] ) ) ? $values[ $slug ] : false;
					if ( $values[ $slug ] ) {
						echo "checked='checked'";
					}
					?>/>
				</td>
			</tr>
			<?php
				endforeach;
			endif;
			?>
			</tbody>
		</table>
	</div>
<?php
}

/**
 * Save metabox data
 *
 * @param int $post_id      Post id.
 *
 * @return mixed
 */
function wyr_save_fake_reactions_metabox( $post_id ) {
	// Nonce sent?
	$nonce = filter_input( INPUT_POST, 'wyr_fake_reactions_nonce', FILTER_SANITIZE_STRING );

	if ( ! $nonce ) {
		return $post_id;
	}

	// Don't save data automatically via autosave feature.
	if ( wyr_is_doing_autosave() ) {
		return $post_id;
	}

	// Don't save data when doing preview.
	if ( wyr_is_doing_preview() ) {
		return $post_id;
	}

	// Don't save data when using Quick Edit.
	if ( wyr_is_inline_edit() ) {
		return $post_id;
	}

	$post_type = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_STRING );

	// Check permissions.
	$post_type_obj = get_post_type_object( $post_type );

	if ( ! current_user_can( $post_type_obj->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	// Verify nonce.
	if ( ! check_admin_referer( 'wyr_fake_reactions', 'wyr_fake_reactions_nonce' ) ) {
		wp_die( esc_html__( 'Nonce incorrect!', 'wyr' ) );
	}

	$reaction_count = filter_input( INPUT_POST, '_wyr_fake_reaction_count', FILTER_SANITIZE_STRING );

	// Sanitize if not empty.
	if ( ! empty( $reaction_count ) ) {
		$reaction_count = absint( $reaction_count );
	}

	update_post_meta( $post_id, '_wyr_fake_reaction_count', $reaction_count );

	$terms = wyr_get_reactions();
	$values = array();
	if ( ! empty( $terms ) ) {
		foreach ( $terms as $term ) {
			$slug 	= $term->slug;
			$value = isset( $_POST[ '_wyr_disable_fakes_for_' . $slug ] );
			$values[ $slug ] = $value;
		}
		update_post_meta( $post_id, '_wyr_disable_fakes_for_reactions', $values );
	}

	do_action( 'wyr_save_list_post_metabox', $post_id );

	return $post_id;
}
