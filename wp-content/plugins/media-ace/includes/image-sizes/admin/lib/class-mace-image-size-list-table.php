<?php
/**
 * MediaAce Image Size List
 *
 * @package media-ace
 * @subpackage Classes
 */


if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * MediaAce Image Size List Class
 */
class Mace_Image_Size_List_Table extends WP_List_Table {

	protected $activated    = 0;
	protected $deactivated  = 0;

	public function get_columns() {
		$columns = array(
			'name'      => __( 'Name', 'mace' ),
			'width'     => __( 'Width (px)', 'mace' ),
			'height'    => __( 'Height (px)', 'mace' ),
			'crop'      => __( 'Crop', 'mace' ),
			'crop_x'    => __( 'Crop X', 'mace' ),
			'crop_y'    => __( 'Crop Y', 'mace' ),
			'group'     => __( 'Group', 'mace' ),
			'active'    => __( 'Status', 'mace' ),
			'modified'	=> __( 'Modified', 'mace' ),
			'in_use'	=> __( 'In use', 'mace' ),
		);

		return $columns;
	}

	protected function get_views() {
		$all = $this->activated + $this->deactivated;

		$links = array(
			'all'           => $this->get_edit_link( array( 'status' => 'all' ), sprintf( __( 'All (%d)', 'mace' ), $all ) ),
			'activated'     => $this->get_edit_link( array( 'status' => 'activated' ), sprintf( __( 'Activated (%d)', 'mace' ), $this->activated ) ),
			'deactivated'   => $this->get_edit_link( array( 'status' => 'deactivated' ), sprintf( __( 'Deactivated (%d)', 'mace' ), $this->deactivated ) ),
		);

		return $links;
	}

	public function column_default( $item, $column_name ) {
		$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';

		switch( $column_name ) {
			case 'name':
			case 'width':
			case 'height':
				$out = '<span class="mace-value">' . $value . '</span>';
				$out .= '<span class="mace-edit"><input type="text" size="5" value="' . esc_attr( $value ) .'" /></span>';

				return $out;

			case 'crop':
				$out = '';

				if ($item['active']) {
					$out .=  '<span class="mace-value' . ( $value ? ' mace-checked' : '' ) . '"></span>';
					$out .= '<span class="mace-edit"><input type="checkbox"' . checked( $value, true, false ) . ' /></span>';
				}

				return $out;

			case 'crop_x':
				$out = '';

				if ( ! $value ) {
					$value = 'center';
				}

				if ($item['active']) {
					$out = '<span class="mace-value">' . $value . '</span>';
					$out .= '<span class="mace-edit">'. mace_image_sizes_crop_x_select( $value ) .'</span>';
				}

				return $out;

			case 'crop_y':
				$out = '';

				if ( ! $value ) {
					$value = 'center';
				}

				if ($item['active']) {
					$out = '<span class="mace-value">' . $value . '</span>';
					$out .= '<span class="mace-edit">'. mace_image_sizes_crop_y_select( $value ) .'</span>';
				}

				return $out;

			case 'group':
				// Inactive sizes belong to theme_plugins group.
				$out = $this->group_nicename( 'theme_plugins' );

				if ($item['active']) {
					$out = $this->group_nicename( $value );
				}

				return $out;

			case 'active':
				return $value ? __( 'activated', 'mace' ) : __( 'deactivated', 'mace' );

			case 'modified':
				if ( $item['modified']['is_modified'] ) {
					return __( 'Yes', 'mace' ) . '<br>' . $item['modified']['description'];
				} else {
					return __( 'No', 'mace' );
				}
			case 'in_use':
			    $detection_run = filter_input( INPUT_GET, 'mace-unused-sizes-detection', FILTER_SANITIZE_STRING );

			    if ( $detection_run ) {
                    $ret = $value ? __( 'yes', 'mace' ) : __( 'unknown', 'mace' );
                } else {
			        $ret = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'mace-unused-sizes-detection' => 'run' ) ) ), esc_html_x( 'check', 'Image Sizes settings', 'mace' ) );
                }

				return $ret;

			default:
				return print_r( $item, true ) ;
		}
	}

	public function prepare_items() {
		$status = $this->get_selected_status();
		$group  = $this->get_selected_group();

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array(
			'name'      => array( 'name', false ),
			'group'     => array( 'group', false),
			'active'    => array( 'active', false),
		);

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$sizes = mace_get_image_sizes();

		$items = array();

		foreach( $sizes as $group_id => $group_sizes ) {
			foreach( $group_sizes as $size_name => $size ) {
				if ( $size['active'] ) {
					$this->activated++;
				} else {
					$this->deactivated++;
				}

				if ( 'activated' === $status && ! $size['active'] ) {
					continue;
				}

				if ( 'deactivated' === $status && $size['active'] ) {
					continue;
				}

				$theme_plugins_inactive_size = ( 'theme_plugins' === $group && 'inactive' === $group_id );

				if ( $group && $group !== $group_id && ! $theme_plugins_inactive_size ) {
					continue;
				}

				$is_modified = false;
				$modified_desc = '';

				if ( isset( $size['defaults'] ) ) {
					foreach ( $size['defaults'] as $key => $value ) {
						if ( $size['defaults'][ $key ] !== $size[ $key ] ) {
							$is_modified = true;
							if ( 'crop' === $key ) {
								$modified_desc .= $key .'<br>';
							} else {
								$modified_desc .= $key . ' (' . $value . ')<br>';
							}
						}
					}
				}

				$modified = array(
					'is_modified' => $is_modified,
					'description' => $modified_desc,
				);

				$items[] = array(
					'name'      => $size_name,
					'width'     => isset( $size['width'] ) ? $size['width'] : '',
					'height'    => isset( $size['height'] ) ? $size['height'] : '',
					'crop'      => isset( $size['crop'] ) ? (bool) $size['crop'] : '',
					'crop_x'    => isset( $size['crop_x'] ) ? $size['crop_x'] : '',
					'crop_y'    => isset( $size['crop_y'] ) ? $size['crop_y'] : '',
					'group'     => $group_id,
					'active'    => (bool) $size['active'],
					'modified'	=> $modified,
					'in_use'    => empty( $size['in_use'] ) ? false : true,
				);
			}
		}

		usort( $items, array( $this, 'usort_reorder' ) );

		$this->items = $items;
	}

	public function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'name';
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

		$result = strcmp( $a[$orderby], $b[$orderby] );

		return ( $order === 'asc' ) ? $result : -$result;
	}

	public function column_name( $item ) {
		$args = array(
			'edit'          => true,
			'delete'        => true,
			'activate'      => false,
		);

		$group = $item['group'];

		if ( 'wp' === $group ) {
			$args['delete']     = false;

		} elseif ( 'inactive' === $group ) {
			$args['delete']     = false;
			$args['edit']       = false;
			$args['activate']   = true;
		}

		$actions = array();

		if ( $args['edit'] ) {
			$actions['edit']    = sprintf('<a href="#" class="mace-image-size-action mace-image-size-%s" data-mace-image-size-name="%s">%s</a>', 'edit', $item['name'], __( 'Quick Edit', 'mace' ) );
		}

		if ( $args['delete'] ) {
			$actions['delete'] = sprintf('<a href="#" class="mace-image-size-action mace-image-size-%s" data-mace-image-size-name="%s">%s</a>', 'delete', $item['name'], __( 'Delete', 'mace' ) );

			if ( 'theme_plugins' === $group ) {
				$actions['delete'] = sprintf('<a href="#" class="mace-image-size-action mace-image-size-%s" data-mace-image-size-name="%s">%s</a>', 'deactivate', $item['name'], __( 'Deactivate', 'mace' ) );
			}
		}

		if ( $args['activate'] ) {
			$actions['activate'] = sprintf('<a href="#" class="mace-image-size-action mace-image-size-%s" data-mace-image-size-name="%s">%s</a>', 'activate', $item['name'], __( 'Activate', 'mace' ) );
		}

		return sprintf('%1$s %2$s', $item['name'], $this->row_actions( $actions ) );
	}

	protected function group_nicename( $group ) {
		$nicename = $group;

		switch ( $group ) {
			case 'wp':
				$nicename = 'WordPress';
				break;

			case 'theme_plugins':
				$nicename = 'Theme & Plugins';
				break;

			default:
				$nicename = ucfirst( $nicename );
				break;
		}

		return $nicename;
	}

	protected function get_edit_link( $args, $label, $class = '' ) {
		$url = add_query_arg( $args );

		$status = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_STRING );

		if ( ( empty( $status ) && 'all' === $args['status'] ) || $status === $args['status'] ) {
			$class = 'current';
		}

		$class_html = '';
		if ( ! empty( $class ) ) {
			$class_html = sprintf(
				' class="%s"',
				esc_attr( $class )
			);
		}

		return sprintf(
			'<a href="%s"%s>%s</a>',
			esc_url( $url ),
			$class_html,
			$label
		);
	}

	/**
	 * @param string $which
	 */
	public function extra_tablenav( $which ) {
		?>
		<div class="actions">
			<?php
				$this->groups_dropdown();

				/** This action is documented in wp-admin/includes/class-wp-posts-list-table.php */
				do_action( 'restrict_manage_posts', $this->screen->post_type, $which );

				submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			?>
		</div>
		<?php
	}

	protected function groups_dropdown() {
		$group = $this->get_selected_group();

		$groups = array(
			'wp'            => $this->group_nicename('wp'),
			'theme_plugins' => $this->group_nicename('theme_plugins'),
			'custom'        => $this->group_nicename('custom'),
		);
		?>
		<label for="filter-by-group" class="screen-reader-text"><?php _e( 'Filter by group', 'mace' ); ?></label>
		<select name="group" id="filter-by-group">
			<option<?php selected( $group, '' ); ?> value=""><?php _e( 'All groups', 'mace' ); ?></option>
			<?php
			foreach ( $groups as $group_id => $group_name ) {
				printf( "<option %s value='%s'>%s</option>\n",
					selected( $group, $group_id ),
					esc_attr( $group_id ),
					esc_html( $group_name )
				);
			}
			?>
		</select>
		<?php
	}

	protected function get_selected_group() {
		return filter_input( INPUT_GET, 'group', FILTER_SANITIZE_STRING );
	}

	protected function get_selected_status() {
		return filter_input( INPUT_GET, 'status', FILTER_SANITIZE_STRING );
	}
}
