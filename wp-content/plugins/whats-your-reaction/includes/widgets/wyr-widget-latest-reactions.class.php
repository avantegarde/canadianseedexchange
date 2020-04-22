<?php
/**
 * Class to display latest reactions in a sidebar
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package whats-your-reaction
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Wyr_Widget_Latest_Reactions' ) ) :

	/**
	 * Class Bimber_Widget_Posts
	 */
	class Wyr_Widget_Latest_Reactions extends WP_Widget {

		/**
		 * The total number of displayed widgets
		 *
		 * @var int
		 */
		static $counter = 0;

		/**
		 * Bimber_Widget_Latest_Reactions constructor.
		 */
		public function __construct() {
			parent::__construct(
				'wyr_widget_latest_reactions',                   // Base ID.
				esc_html__( 'Latest Reactions', 'wyr' ),         // Name.
				array(                                           // Args.
					'description' => esc_html__( 'A list of latest reactions.', 'wyr' ),
				)
			);

			self::$counter ++;
		}

		/**
		 * Render widget
		 *
		 * @param array $args Arguments.
		 * @param array $instance Instance of widget.
		 */
		public function widget( $args, $instance ) {
			$instance = wp_parse_args( $instance, $this->get_default_args() );

			$title = apply_filters( 'widget_title', $instance['title'] );

			// HTML id.
			if ( empty( $instance['id'] ) ) {
				$instance['id'] = 'g1-widget-latest-reactions-' . self::$counter;
			}

			// HTML class.
			$classes   = explode( ' ', $instance['class'] );
			$classes[] = 'g1-widget-latest-reactions';

			$collection_class = array(
				'g1-collection',
			);

			echo wp_kses_post( $args['before_widget'] );

			if ( ! empty( $title ) ) {
				echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
			}


			$user_id = $this->get_user_id();
			$view_all_url = $this->get_view_all_url( $user_id );
			$votes = wyr_get_user_latest_votes( $user_id, $instance['max'] );

			set_query_var( 'wyr_latest_reactions', $votes );
			set_query_var( 'wyr_latest_reactions_type', $user_id ? 'for_displayed_user' : 'global' );
			set_query_var( 'wyr_latest_reactions_id', $instance['id'] );
			set_query_var( 'wyr_latest_reactions_classes', $classes );
			set_query_var( 'wyr_latest_reactions_view_all_url', $view_all_url );

			wyr_get_template_part( 'widget-latest-reactions' );

			echo wp_kses_post( $args['after_widget'] );
		}

		protected function get_user_id() {
			// On BP profile page?
			if ( function_exists( 'bp_get_displayed_user' ) && $user = bp_get_displayed_user() ) {
				return $user->id;
			}

			return 0;
		}

		protected function get_view_all_url( $user_id ) {
			if ( ! $user_id ) {
				return '';
			}
			return bp_core_get_user_domain( $user_id ) . wyr_reactions_bp_component_id();
		}

		/**
		 * Render form
		 *
		 * @param array $instance Instance of widget.
		 *
		 * @return void
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( $instance, $this->get_default_args() );
			?>
			<div class="g1-widget-latest-reactions">
				<p>
					<label
						for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Widget title', 'wyr' ); ?>
						:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
					       value="<?php echo esc_attr( $instance['title'] ); ?>">
				</p>

				<p>
					<label
						for="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>"><?php esc_html_e( 'The max. number of entries to show', 'wyr' ); ?>
						:</label>
					<input size="5" type="text" name="<?php echo esc_attr( $this->get_field_name( 'max' ) ); ?>"
					       id="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>"
					       value="<?php echo esc_attr( $instance['max'] ) ?>"/>
				</p>

				<p>
					<label
						for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'HTML id attribute (optional)', 'wyr' ); ?>
						:</label>
					<input class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>"
					       id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"
					       value="<?php echo esc_attr( $instance['id'] ) ?>"/>
				</p>

				<p>
					<label
						for="<?php echo esc_attr( $this->get_field_id( 'class' ) ); ?>"><?php esc_html_e( 'HTML class(es) attribute (optional)', 'wyr' ); ?>
						:</label>
					<input class="widefat" type="text"
					       name="<?php echo esc_attr( $this->get_field_name( 'class' ) ); ?>"
					       id="<?php echo esc_attr( $this->get_field_id( 'class' ) ); ?>"
					       value="<?php echo esc_attr( $instance['class'] ) ?>"/>
				</p>
			</div>
			<?php
		}

		/**
		 * Get default arguments
		 *
		 * @return array
		 */
		public function get_default_args() {
			return apply_filters( 'wyr_widget_latest_reactions_defaults', array(
				'title'                => esc_html__( 'Latest Reactions', 'wyr' ),
				'max'                  => 3,
				'id'                   => '',
				'class'                => '',
			) );
		}

		/**
		 * Update widget
		 *
		 * @param array $new_instance New instance.
		 * @param array $old_instance Old instance.
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title']                = strip_tags( $new_instance['title'] );
			$instance['max']                  = absint( $new_instance['max'] );
			$instance['id']                   = sanitize_html_class( $new_instance['id'] );
			$instance['class']                = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $new_instance['class'] ) ) );

			return $instance;
		}
	}

endif;
