<?php
/**
 * Snapcode widget
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'widgets_init', 'g1_socials_register_youtube_widget' );
/**
 * Snapcode widget register function
 */
function g1_socials_register_youtube_widget() {
	register_widget( 'G1_Socials_YouTube_Widget' );
}

/**
 * Snapcode widget class
 */
class G1_Socials_YouTube_Widget extends WP_widget {
	/**
	 * Widget contruct.
	 */
	function __construct() {
		parent::__construct(
			'g1_socials_youtube_widget',
			esc_html__( 'G1 Socials YouTube', 'g1_socials' ),
			array(
				'classname'     => 'widget_g1_socials_youtube',
				'description'   => esc_html__( 'Displays YouTube profile overview.', 'g1_socials' ),
			)
		);
	}

	/**
	 * Get default arguments
	 *
	 * @return array
	 */
	public function get_default_args() {
		return apply_filters( 'adace_get_my_widget_defaults', array(
			'title'        => esc_html__( 'Find me on YouTube', 'g1_socials' ),
			'channel_name' => '',
			'channel_id'   => '',
		) );
	}

	/**
	 * Widget contruct.
	 *
	 * @param array $instance Current widget settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->get_default_args() );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'channel_name' ) ); ?>"><?php esc_html_e( 'Channel Name:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'channel_name' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'channel_name' ) ); ?>" type="text" value="<?php echo esc_html( $instance['channel_name'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'channel_id' ) ); ?>"><?php esc_html_e( 'Channel Id:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'channel_id' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'channel_id' ) ); ?>" type="text" value="<?php echo esc_html( $instance['channel_id'] ); ?>" />
		</p>
		<?php
	}

	/**
	 * Widget saving.
	 *
	 * @param array $new_instance Current widget settings form output.
	 * @param array $old_instance Old widget settings.
	 */
	public function update( $new_instance, $old_instance ) {
		// Sanitize input.
		$instance = array();
		$instance['title']        = filter_var( $new_instance['title'], FILTER_SANITIZE_STRING );
		$instance['channel_name'] = filter_var( $new_instance['channel_name'], FILTER_SANITIZE_STRING );
		$instance['channel_id']   = filter_var( $new_instance['channel_id'], FILTER_SANITIZE_STRING );
		return $instance;
	}

	/**
	 * Widget output.
	 *
	 * @param array $args Widget args from registration point.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->get_default_args() );
		// Get settings.
		$title        = apply_filters( 'widget_title', $instance['title'] );
		$channel_name = apply_filters( 'channel_name', $instance['channel_name'] );
		$channel_id   = apply_filters( 'channel_id', $instance['channel_id'] );
		if ( empty( $channel_id ) ) {
			return;
		}
		// Check if all is provided.
		// Echo all widget elements.
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		set_query_var( 'g1_socials_yt_channel_id', $channel_id );
		set_query_var( 'g1_socials_yt_channel_name', $channel_name );

		g1_socials_get_template_part( 'youtube-channel' );

		echo wp_kses_post( $args['after_widget'] );
	}
}
