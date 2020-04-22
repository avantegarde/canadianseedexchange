<?php
/**
 * Snax Overview Dashboard Widget
 *
 * @package snax
 * @subpackage admin
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Snax_Overview_Dashboard_Widget' ) ) :

	final class Snax_Overview_Dashboard_Widget {

		private static $instance;

		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new Snax_Overview_Dashboard_Widget();
			}

			return self::$instance;
		}

		/**
		 * Private constructor to prevent creating a new instance
		 * via the 'new' operator from outside of this class.
		 */
		private function __construct() {

			$this->setup_hooks();
		}

		private function setup_hooks() {
			add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		}


		public function wp_dashboard_setup() {
			wp_add_dashboard_widget(
				'snax_dashboard_widget_overview',
				esc_html__( 'Snax Overview', 'snax' ),
				array( $this, 'render' )
			);
		}

		public function enqueue_styles( $hook ) {
			$screen = get_current_screen();
			if ( 'dashboard' === $screen->id ) {
				wp_enqueue_style( 'dw_style', snax_admin()->assets_url . 'css/dashboard.widget.overview.css', array(), '1.0' );
			}
		}


		public function render() {
			?>
			<h3><?php esc_html_e( 'Frontend Submission', 'snax' ); ?></h3>
			<?php
			$query = new WP_Query( array(
				'fields'                => 'ids',
				'post_type'             => 'any',
				'post_status'           => 'pending',
				'ignore_sticky_posts'   => true,
				'tax_query'             => array(
					array(
						'taxonomy' 	=> snax_get_snax_format_taxonomy_slug(),
						'field' 	=> 'slug',
						'terms'     => snax_get_active_formats_ids(),
					),
				),
			) );
			?>

			<?php if ( $query->have_posts() ) : ?>
				<?php
				echo esc_html(
					sprintf ( _nx(
						'There is %d pending submission awaiting your approval:',
						'There are %d pending submissions awaiting your approval:',
						$query->found_posts,
						'snax'
					),
						number_format_i18n( $query->found_posts )
					) );
				?>

				<ul>
					<?php foreach( snax_get_active_formats() as $format_id => $format_args ) : ?>
						<?php
						switch( $format_id ) {
							case 'trivia_quiz':
							case 'personality_quiz':
								$post_type = snax_get_quiz_post_type();
								break;

							case 'classic_poll':
							case 'binary_poll':
							case 'versus_poll':
								$post_type = snax_get_poll_post_type();
								break;

							case 'extproduct':
								$post_type = 'product';
								break;

							default:
								$post_type = 'post';
						}

						$query = new WP_Query( array(
							'fields'                => 'ids',
							'post_type'             => $post_type,
							'post_status'           => 'pending',
							'ignore_sticky_posts'   => true,
							'tax_query'             => array(
								array(
									'taxonomy' 	=> snax_get_snax_format_taxonomy_slug(),
									'field' 	=> 'slug',
									'terms'   	=> $format_id,
								),
							),
						) );
						?>
						<?php if ( $query->have_posts() ) : ?>
							<?php
							$url_args = array(
								'post_type'     => $post_type,
								'post_status'   => 'pending',
								'snax_filter'   => $format_id,
							);
							?>
							<li>
								<a href="<?php echo admin_url( add_query_arg( $url_args, 'edit.php' ) ); ?>"><?php echo esc_html( $format_args['labels']['name'] ); ?> <span class="count"><?php echo $query->found_posts; ?></span></a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<?php esc_html_e( 'There are no pending submissions.', 'snax' ); ?>
			<?php endif;?>
			<?php
		}
	}

	// Fire in a hole!
	Snax_Overview_Dashboard_Widget::get_instance();
endif;
