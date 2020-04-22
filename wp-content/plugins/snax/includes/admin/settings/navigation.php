<?php
/**
 * Snax Settings Navigation
 *
 * @package snax
 * @subpackage Settings
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Highlight the Settings > Snax main menu item regardless of which actual tab we are on.
 */
function snax_admin_settings_menu_highlight() {
	global $plugin_page, $submenu_file;

	$settings_pages = apply_filters( 'snax_settings_menu_highlight', array(
		'snax-general-settings',
		'snax-pages-settings',
		'snax-lists-settings',
		'snax-quizzes-settings',
		'snax-polls-settings',
		'snax-stories-settings',
		'snax-memes-settings',
		'snax-audios-settings',
		'snax-videos-settings',
		'snax-images-settings',
		'snax-galleries-settings',
		'snax-embeds-settings',
		'snax-voting-settings',
		'snax-auth-settings',
		'snax-moderation-settings',
		'snax-demo-settings'
	) );

	if ( in_array( $plugin_page, $settings_pages, true ) ) {
		// We want to map all subpages to one settings page (in main menu).
		$submenu_file = 'snax-general-settings';
	}
}

/**
 * Get tabs in the admin settings area.
 *
 * @param string $active_tab        Name of the tab that is active. Optional.
 *
 * @return string
 */
function snax_get_admin_settings_tabs( $active_tab = '' ) {
	$tabs = array();

	$tabs['general'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-general-settings' ), 'admin.php' ) ),
		'name'  => __( 'General', 'snax' ),
		'order' => 10,
	);

	$tabs['pages'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-pages-settings' ), 'admin.php' ) ),
		'name'  => __( 'Pages', 'snax' ),
		'order' => 20,
	);

	$tabs['formats'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-lists-settings' ), 'admin.php' ) ),
		'name'  => __( 'Formats', 'snax' ),
		'order' => 30,
		'subtabs'   => array(
			'lists' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-lists-settings' ), 'admin.php' ) ),
				'name'  => __( 'Lists', 'snax' ),
				'order' => 30,
			),
			'quizzes' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-quizzes-settings' ), 'admin.php' ) ),
				'name'  => __( 'Quizzes', 'snax' ),
				'order' => 40,
			),

			'polls' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-polls-settings' ), 'admin.php' ) ),
				'name'  => __( 'Polls', 'snax' ),
				'order' => 50,
			),

			'stories' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-stories-settings' ), 'admin.php' ) ),
				'name'  => __( 'Stories', 'snax' ),
				'order' => 60,
			),

			'memes' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-memes-settings' ), 'admin.php' ) ),
				'name'  => __( 'Memes', 'snax' ),
				'order' => 70,
			),

			'audios' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-audios-settings' ), 'admin.php' ) ),
				'name'  => __( 'Audios', 'snax' ),
				'order' => 80,
			),

			'videos' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-videos-settings' ), 'admin.php' ) ),
				'name'  => __( 'Videos', 'snax' ),
				'order' => 90,
			),

			'images' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-images-settings' ), 'admin.php' ) ),
				'name'  => __( 'Images', 'snax' ),
				'order' => 100,
			),

			'galleries' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-galleries-settings' ), 'admin.php' ) ),
				'name'  => __( 'Galleries', 'snax' ),
				'order' => 110,
			),

			'embeds' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-embeds-settings' ), 'admin.php' ) ),
				'name'  => __( 'Embeds', 'snax' ),
				'order' => 120,
			),

			'links' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-links-settings' ), 'admin.php' ) ),
				'name'  => __( 'Links', 'snax' ),
				'order' => 130,
			),
		),
	);


	$tabs['formats']['subtabs']['extproduct'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-extproduct-settings' ), 'admin.php' ) ),
		'name'  => __( 'External Product', 'snax' ),
		'order' => 140,
	);


	$tabs['voting'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-voting-settings' ), 'admin.php' ) ),
		'name'  => __( 'Voting', 'snax' ),
		'order' => 140,
	);

	$tabs['auth'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-auth-settings' ), 'admin.php' ) ),
		'name'  => __( 'Auth', 'snax' ),
		'order' => 150,
	);

	$tabs['moderation'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-moderation-settings' ), 'admin.php' ) ),
		'name'  => __( 'Moderation', 'snax' ),
		'order' => 160,
	);

	$tabs = apply_filters( 'snax_get_admin_settings_tabs', $tabs, $active_tab );

	// Order.
	uasort( $tabs, 'snax_func_sort_by_order_key' );

	return $tabs;
}

/**
 * Output the tabs in the admin area.
 *
 * @param string $active_tab        Name of the tab that is active. Optional.
 */
function snax_admin_settings_tabs( $active_tab = '' ) {
	$tabs_html    = '';
	$idle_class   = 'nav-tab';
	$active_class = 'nav-tab nav-tab-active';

	/**
	 * Filters the admin tabs to be displayed.
	 *
	 * @param array $value      Array of tabs to output to the admin area.
	 */
	$tabs = apply_filters( 'snax_admin_settings_tabs', snax_get_admin_settings_tabs( $active_tab ) );

	// Loop through tabs and build navigation.
	foreach ( array_values( $tabs ) as $tab_data ) {
		$is_current = (bool) ( $tab_data['name'] === $active_tab );
		$tab_class  = $is_current ? $active_class : $idle_class;

		$tabs_html .= '<a href="' . esc_url( $tab_data['href'] ) . '" class="' . esc_attr( $tab_class ) . '">' . esc_html( $tab_data['name'] ) . '</a>';
	}

	echo filter_var( $tabs_html );

	do_action( 'snax_admin_tabs' );
}

/**
 * Output the subtabs in the admin area.
 *
 * @param string $parent_slug       Slug of the parent tab.
 * @param string $active_tab        Name of the tab that is active. Optional.
 */
function snax_admin_settings_subtabs( $parent_slug, $active_tab = '' ) {
	/**
	 * Filters the admin tabs to be displayed.
	 *
	 * @param array $value      Array of tabs to output to the admin area.
	 */
	$tabs = apply_filters( 'snax_admin_settings_tabs', snax_get_admin_settings_tabs( $active_tab ) );

	if ( ! isset( $tabs[ $parent_slug ] ) ) {
		return;
	}

	$subtabs = $tabs[ $parent_slug ]['subtabs'];
	uasort( $subtabs, 'snax_func_sort_by_order_key' );
	$subtabs = array_values( $subtabs );
	?>
	<?php if ( count( $subtabs ) ) : ?>
		<div class="snax-admin-subtabs">
			<ul class="snax-admin-subtabs-items">
			<?php foreach ( $subtabs as $tab_data ) : ?>
				<?php
					$item_class = array( 'snax-admin-subtabs-item' );
					if ( $tab_data['name'] === $active_tab ) {
						$item_class[] = 'snax-admin-subtabs-item-current';
					}
				?>
				<li class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $item_class ) ); ?>">
					<a class="snax-admin-subtab" href="<?php echo esc_url( $tab_data['href'] ); ?>"><?php echo esc_html( $tab_data['name'] ); ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div><!-- .snax-admin-subtabs -->
	<?php endif; ?>
	<?php
	do_action( 'snax_admin_subtabs' );
}
