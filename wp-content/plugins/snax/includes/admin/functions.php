<?php
/**
 * Snax Admin Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Check whether we are in autosave state
 *
 * @return bool
 */
function snax_is_doing_autosave() {
	return defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ? true : false;
}

/**
 * Check whether we are during inline edition
 *
 * @return bool
 */
function snax_is_inline_edit() {
	return ! is_null( filter_input( INPUT_POST, '_inline_edit' ) );
}

/**
 * Check whether we are in preview mode
 *
 * @return bool
 */
function snax_is_doing_preview() {
	$preview = filter_input( INPUT_POST, 'wp-preview' );

	return ! empty( $preview );
}

/**
 * Check whether a string is a valid date
 *
 * @param string $date_string       Input date string.
 *
 * @return string                   Correct date or empty if not a valid date.
 */
function snax_sanitize_datetime( $date_string ) {
	// Return empty if it's not a valid date?
	if ( false === strtotime( $date_string ) ) {
		return '';
	}

	return $date_string;
}

/**
 * Checkbox sanitization callback
 *
 * @param string $string            Input.
 *
 * @return string                   Output.
 */
function snax_sanitize_checkbox( $string ) {
	if ( ! in_array( $string, array( 'none', 'standard' ), true ) ) {
		return 'none';
	}

	return $string;
}

/**
 * Post sanitization callback
 *
 * @param int $post_id              Post id.
 *
 * @return int
 */
function snax_sanitize_published_post( $post_id ) {
	$post_id = absint( $post_id );

	// Only a published post is valid.
	if ( 'publish' !== get_post_status( $post_id ) ) {
		$post_id = false;
	}

	return $post_id;
}

/**
 * Sanitize array text values (1 level deep only)
 *
 * @param array $input_array        Input.
 *
 * @return array                    Output.
 */
function snax_sanitize_text_array( $input_array ) {
	if ( ! is_array( $input_array ) ) {
		return array();
	}

	foreach ( $input_array as $key => $value ) {
		if ( is_array( $value ) ) {
			$input_array[ $key ] = array_map( 'sanitize_text_field', $input_array );
		} else {
			$input_array[ $key ] = sanitize_text_field( $value );
		}
	}

	return $input_array;
}

/**
 * Sanitize multi select category white list
 *
 * @param array $input_array        Input.
 *
 * @return array                    Output.
 */
function snax_sanitize_category_whitelist( $input_array ) {
	// The "Allow all" options can't be used with others.
	if ( count( $input_array ) > 1 && in_array( '', $input_array, true ) ) {
		$empty_value_key = array_search( '', $input_array );

		unset( $input_array[ $empty_value_key ] );
	}

	foreach ( $input_array as $key => $value ) {
		$input_array[ $key ] = sanitize_text_field( $value );
	}

	return $input_array;
}

/**
 * Sanitize array int values
 *
 * @param array $input_array        Input.
 *
 * @return array
 */
function snax_sanitize_int_array( $input_array ) {
	foreach ( $input_array as $key => $value ) {
		$input_array[ $key ] = intval( $value );
	}

	return $input_array;
}

/**
 * Sanitize array of new item available form types
 *
 * @param array $input_array        Input.
 *
 * @return array
 */
function snax_sanitize_new_item_forms( $input_array ) {
	$forms = snax_get_registered_item_forms();

	foreach ( $input_array as $key => $value ) {
		if ( ! in_array( $value, $forms, true ) ) {
			unset( $key );
		}
	}

	return $input_array;
}

/**
 * Register custom columns to the columns shown on the manage posts screen
 *
 * @param array $columns            An array of column name => label.
 *
 * @return array
 */
function snax_register_custom_columns( $columns ) {
	global $post_type, $pagenow;

	if ( 'edit.php' !== $pagenow ) {
		return $columns;
	}

	if ( in_array( $post_type, snax_get_post_supported_post_types(), true ) ) {
		$columns['snax_format'] = __( 'Snax Format', 'snax' );
	}

	if ( snax_get_item_post_type() === $post_type ) {
		$columns['snax_item_parent'] = __( 'Submitted to', 'snax' );
	}

	return $columns;
}

/**
 * Render content of registered custom columns.
 *
 * @param string $column           Column name.
 * @param int    $post_id          Post ID.
 */
function snax_render_custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'snax_format':
			if ( ! snax_is_format( null, $post_id ) ) {
				echo '-';
				break;
			}

			?>

			<?php if ( snax_is_format( 'list', $post_id ) ) : ?>

				<p><?php esc_html_e( 'List', 'snax' ); ?></p>

				<?php if ( snax_is_post_open_for_submission( $post_id ) ) : ?>
					<div class="snax-admin-status snax-admin-status-submissions-open"><?php esc_html_e( 'Submissions', 'snax' ) ?></div>
				<?php else : ?>
					<div class="snax-admin-status snax-admin-status-submissions-closed"><?php esc_html_e( 'Submissions', 'snax' ); ?></div>
				<?php endif; ?>

				<?php if ( snax_is_post_open_for_voting( $post_id ) ) : ?>
					<div class="snax-admin-status snax-admin-status-voting-open"><?php esc_html_e( 'Voting', 'snax' ) ?></div>
				<?php else : ?>
					<div class="snax-admin-status snax-admin-status-voting-closed"><?php esc_html_e( 'Voting', 'snax' ); ?></div>
				<?php endif; ?>

			<?php else: ?>
				<?php
				$formats = snax_get_formats();
				$format = snax_get_format( $post_id );

				if ( isset( $formats[ $format ] ) ) {
					printf( '<p>%s</p>', esc_html( $formats[$format]['labels']['name'] ) );
				}
				?>
			<?php endif; ?>

			<?php
			break;

		case 'snax_item_parent':
			$item = get_post( $post_id );

			edit_post_link( get_the_title( $item->post_parent ), '', '', $item->post_parent );
			break;
	}
}

/**
 * Render custom filters
 */
function snax_render_custom_columns_filters() {
	// Execute only on the supported post types.
	global $post_type;

	if ( in_array( $post_type, snax_get_post_supported_post_types(), true ) ) {
		$selected_filter = filter_input( INPUT_GET, 'snax_filter' );

		?>
		<label for="snax-filter"></label>
		<select id="snax-filter" name="<?php echo esc_attr( 'snax_filter' ); ?>">
			<option value=""<?php selected( $selected_filter, '' ); ?>><?php esc_html_e( 'All posts', 'snax' ); ?></option>';
			<option value="all_formats"<?php selected( $selected_filter, 'all_formats' ); ?>><?php esc_html_e( 'Snax: All formats', 'snax' ); ?></option>';
			<?php foreach ( snax_get_formats() as $snax_format => $snax_format_data ) : ?>
				<?php
				if ( in_array( $snax_format, array( 'ranked_list', 'classic_list', 'trivia_quiz', 'personality_quiz' ), true ) ) {
					continue;
				}

				if ( 'list' === $snax_format ) {
					$label = __( 'List', 'snax' );
				} else {
					$label = $snax_format_data['labels']['name'];
				}
				?>

				<option value="<?php echo esc_attr( $snax_format ); ?>"<?php selected( $selected_filter, $snax_format ); ?>><?php esc_html_e( 'Snax:', 'snax' ) ?> <?php echo esc_html( $label ); ?></option>';
			<?php endforeach; ?>
		</select>
		<?php
	}

	if ( snax_get_item_post_type() === $post_type ) {
		$selected_filter = filter_input( INPUT_GET, 'snax_filter' );

		?>
		<label for="snax-filter"></label>
		<select id="snax-filter" name="<?php echo esc_attr( 'snax_filter' ); ?>">
			<option value=""<?php selected( $selected_filter, '' ); ?>><?php esc_html_e( 'All items', 'snax' ); ?></option>';
			<?php foreach ( snax_get_formats() as $snax_format => $snax_format_data ) : ?>
				<?php
				if ( ! in_array( $snax_format, array( 'list', 'gallery' ), true ) ) {
					continue;
				}

				if ( 'list' === $snax_format ) {
					$label = __( 'List', 'snax' );
				} else {
					$label = $snax_format_data['labels']['name'];
				}
				?>

				<option value="<?php echo esc_attr( $snax_format ); ?>"<?php selected( $selected_filter, $snax_format ); ?>><?php echo esc_html( $label ); ?> <?php esc_html_e( 'items', 'snax' ) ?></option>';
			<?php endforeach; ?>
		</select>
		<?php
	}
}

/**
 * Apply custom filters
 *
 * @param WP_Query $query        Current query object.
 */
function snax_filter_by_custom_columns( $query ) {
	global $post_type, $pagenow;

	if ( 'edit.php' !== $pagenow ) {
		return;
	}

	// Filter post by snax formats.
	if ( in_array( $post_type, snax_get_post_supported_post_types(), true ) ) {
		$selected_filter = filter_input( INPUT_GET, 'snax_filter' );

		$all_formats = snax_get_formats();

		if ( isset( $all_formats[ $selected_filter ] ) ) {
			$query->set( 'tax_query', array(
				array(
					'taxonomy' 	=> snax_get_snax_format_taxonomy_slug(),
					'field' 	=> 'slug',
					'terms'   	=> $selected_filter,
				),
			) );
		}

		if ( 'all_formats' === $selected_filter ) {
			$query->set( 'tax_query', array(
				array(
				'taxonomy' 	=> snax_get_snax_format_taxonomy_slug(),
				'field' 	=> 'slug',
				'compare'   => 'EXISTS',
				),
			) );
		}
	}

	// Filter snax items.
	if ( snax_get_item_post_type() === $post_type ) {
		$selected_filter = filter_input( INPUT_GET, 'snax_filter' );

		if ( in_array( $selected_filter, array( 'list', 'gallery' ), true ) ) {
			$query->set( 'meta_key', '_snax_parent_format' );
			$query->set( 'meta_value', $selected_filter );
		}
	}
}

/**
 * Redirect to welcome page
 */
function snax_do_welcome_redirect() {
	if ( get_transient( '_snax_do_activation_redirect' ) ) {
		delete_transient( '_snax_do_activation_redirect' );

		if ( ! filter_input( INPUT_GET, 'activate-multi' ) ) {
			$query_args = array( 'page' => 'snax-about' );

			wp_safe_redirect( add_query_arg( $query_args, admin_url( 'index.php' ) ) );
		}
	}
}

/**
 * Customize admin settings fields
 *
 * @param array $settings_fields        Fields config.
 *
 * @return array
 */
function snax_customize_admin_settings_fields( $settings_fields ) {
	// WP login.
	$can_disable_wp_login = apply_filters( 'snax_disable_wp_login_option_active', false );

	if ( ! $can_disable_wp_login ) {
		unset( $settings_fields['snax_settings_general']['snax_disable_wp_login'] );
	}

	return $settings_fields;
}

/**
 * Save custom permalinks structure
 */
function snax_save_permalinks() {
	global $pagenow;

	if ( 'options-permalink.php' === $pagenow ) {
		// Item slug.
		$item_slug = filter_input( INPUT_POST, 'snax_item_slug', FILTER_SANITIZE_STRING );

		if ( null !== $item_slug ) {
			update_option( 'snax_item_slug', sanitize_title_with_dashes( $item_slug ) );
		}

		// Prefix.
		$url_var_prefix = filter_input( INPUT_POST, 'snax_url_var_prefix', FILTER_SANITIZE_STRING );

		if ( null !== $url_var_prefix ) {
			update_option( 'snax_url_var_prefix', sanitize_title_with_dashes( $url_var_prefix ) );
		}
	}
}

/**
 * Handle post actions
 */
function snax_admin_handle_post_actions() {
	$post_id = filter_input( INPUT_GET, 'snax_post', FILTER_SANITIZE_NUMBER_INT );
	$action  = filter_input( INPUT_GET, 'snax_action', FILTER_SANITIZE_STRING );

	if ( ! empty( $post_id ) && ! empty( $action ) ) {
		switch ( $action ) {
			case 'convert_to_list':
				snax_conver_to_list_format( $post_id );
				break;
		}
	}
}

/**
 * Conver not Snax post to the "Open List" format.
 *
 * @param int|WP_Post $post_id              Optional. Post ID or WP_Post object. Default is global `$post`.
 */
function snax_conver_to_list_format( $post_id = 0 ) {
	$post = get_post( $post_id );

	if ( snax_is_format( 'list', $post ) ) {
		return;
	}

	// Format.
	snax_set_post_format( $post->ID, 'list' );

}

/**
 * Add input for changing Snax format
 *
 * @param str $column_name  Name of the column.
 * @param str $post_type    Post type.
 * @return void
 */
function snax_add_set_format_to_bulk_edit( $column_name, $post_type ) {
	$snax_formats = snax_get_formats();
	$excluded_formats = snax_get_excluded_formats_from_bulk_settings();
	if ( 'snax_format' === $column_name ) {?>
		<fieldset class="inline-edit-col-right" >
		<label class="alignleft">
		<span class="title">Snax Format</span>
			<select name="snax_format">
				<option value="-1">— No Change —</option>
				<option value="remove">— No Snax Format —</option>
					<?php
					foreach ( $snax_formats as $snax_format => $snax_format_data ) :
						if ( ! in_array( $snax_format, $excluded_formats, true ) ) :?>
						<option value="<?php echo esc_attr( $snax_format ); ?>"><?php echo esc_attr( $snax_format_data['labels']['name'] ); ?></option>
					<?php
						endif;
					endforeach;?>
			</select>
		</label>
		</fieldset>
	<?php }
}

/**
 * Get formats to be excluded from bulk setting
 *
 * @return array
 */
function snax_get_excluded_formats_from_bulk_settings() {
	$excluded_formats = array( 'gallery', 'list', 'ranked_list', 'classic_list', 'trivia_quiz', 'personality_quiz' );
	return apply_filters( 'snax_get_excluded_formats_from_bulk_settings',$excluded_formats );
}

/**
 * Save bulk edit
 *
 * @param int $post_id  Post id.
 */
function snax_save_bulk_edit( $post_id ) {
	// We have to do this to avoid infinite loop.
	remove_filter( 'save_post', 'snax_save_bulk_edit', 10, 1 );

	if ( empty( $_REQUEST['snax_format'] ) ) {
		add_filter( 'save_post', 'snax_save_bulk_edit', 10, 1 );
		return;
	}

	$snax_format = filter_var( $_REQUEST['snax_format'], FILTER_SANITIZE_STRING );
	$current_format = snax_get_post_format( $post_id );
	$excluded_formats = snax_get_excluded_formats_from_bulk_settings();
	if ( 'remove' === $snax_format ) {
		$snax_format = '';
	}

	if ( '-1' === $snax_format ) {
		add_filter( 'save_post', 'snax_save_bulk_edit', 10, 1 );
		return;
	}
	if ( in_array( $current_format, $excluded_formats, true ) || in_array( $snax_format, $excluded_formats, true ) ) {
		add_filter( 'save_post', 'snax_save_bulk_edit', 10, 1 );
		return;
	}

	snax_set_post_format( $post_id, $snax_format );

	add_filter( 'save_post', 'snax_save_bulk_edit', 10, 1 );
}

/**
 * Add Snax row actions
 *
 * @param array $actions        Row actions.
 *
 * @return array
 */
function snax_post_row_actions( $actions ) {
	$post = get_post();

	if ( apply_filters( 'snax_show_post_row_action_edit', true, $post->ID ) ) {
		if ( current_user_can( 'snax_edit_posts', $post->ID ) ) {
			$actions['edit_snax'] = sprintf(
				'<a href="%s">%s</a>',
				snax_get_post_edit_url( $post->ID ),
				_x( 'Edit with Snax', 'Post row actions', 'snax' )
			);
		}
	}

    $duplicate_post_types = snax_allow_to_duplicate_post_types();

	if ( in_array( get_post_type( $post ), $duplicate_post_types ) ) {
        if ( current_user_can( 'edit_posts', $post->ID ) && 'trash' !== get_post_status( $post->ID ) ) {
            $actions['snax_duplicate'] = sprintf(
                '<a href="%s">%s</a>',
                snax_get_post_duplicate_url( $post->ID ),
                _x( 'Duplicate', 'Post row actions', 'snax' )
            );
        }
    }

	return $actions;
}

function snax_allow_to_duplicate_post_types() {
    return apply_filters( 'snax_allow_to_duplicate_post_types', array() );
}

function snax_get_post_duplicate_url( $post_id = 0 ) {
    $post = get_post( $post_id );

    $url = add_query_arg( array(
        'post' => $post->ID,
        'action' => 'snax_duplicate',

    ), admin_url( 'post.php' ) );

    return $url;
}

function snax_post_action_duplicate( $post_id ) {
    $post = get_post( $post_id );
    $post_type = get_post_type( $post );

    $allowed_post_types = snax_allow_to_duplicate_post_types();

    if ( ! in_array( $post_type, $allowed_post_types ) ) {
        return;
    }

    $callback = sprintf( '%s_duplicate', $post_type );

    if ( is_callable( $callback ) ) {
        $duplicate_id = call_user_func( $callback, $post->ID );

        $redirect_url = add_query_arg( array(
            'post' => $duplicate_id,
            'action' => 'edit',
            'snax_duplicated' => $post_id
        ), admin_url( 'post.php' )  );

        wp_safe_redirect( $redirect_url );
        exit;
    }
}

function snax_post_duplicated_notice() {
    $duplicate_origin_id = filter_input( INPUT_GET, 'snax_duplicated', FILTER_SANITIZE_NUMBER_INT );

    if ( $duplicate_origin_id > 0 ) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>'. sprintf( esc_html__( 'Duplicate of the %s successfully saved as draft', 'snax' ), '<a href="'. esc_url( get_edit_post_link( $duplicate_origin_id ) ) .'">'. esc_html__( 'post', 'snax' ) .'</a>' ) .'</p>';
        echo '</div>';
    }
}

/**
 * Duplicate post with meta
 *
 * @param int   $post_id        Post id.
 * @param array $new_post_arr   Optional. Post data.
 *
 * @return int|WP_Error         Post id on success. WP_Error on error.
 */
function snax_post_duplicate( $post_id, $new_post_arr = array() ) {
    $post = get_post( $post_id );

    $new_post_defaults = array(
        'post_author'           => $post->post_author,
        'post_content'          => $post->post_content,
        'post_title'            => $post->post_title,
        'post_excerpt'          => $post->post_excerpt,
        'post_status'           => 'draft',
        'post_type'             => $post->post_type,
        'comment_status'        => $post->comment_status,
        'ping_status'           => $post->ping_status,
        'post_password'         => $post->post_password,
        'to_ping'               => $post->to_ping,
        'pinged'                => $post->pinged,
        'post_parent'           => $post->post_parent,
        'menu_order'            => $post->menu_order,
        'guid'                  => $post->guid,
    );

    $new_post_arr = wp_parse_args( $new_post_arr, $new_post_defaults );

    $new_post_id = wp_insert_post( $new_post_arr, true );

    if ( is_wp_error( $new_post_id ) ) {
        return $new_post_id;
    }

    $post_meta = get_post_meta( $post->ID );

    foreach ( $post_meta as $meta_key => $meta_values ) {
        add_post_meta( $new_post_id, $meta_key, $meta_values[0] );
    }

    return $new_post_id;
}
