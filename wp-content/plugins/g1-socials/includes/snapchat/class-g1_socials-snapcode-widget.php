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

add_action( 'widgets_init', 'g1_socials_register_snapcode_widget' );
/**
 * Snapcode widget register function
 */
function g1_socials_register_snapcode_widget() {
	register_widget( 'G1_Socials_Snapcode_Widget' );
}

/**
 * Snapcode widget class
 */
class G1_Socials_Snapcode_Widget extends WP_widget {
	/**
	 * Widget contruct.
	 */
	function __construct() {
		parent::__construct(
			'g1_socials_snapcode_widget',
			esc_html__( 'G1 Socials Snapchat', 'g1_socials' ),
			array(
				'description' => esc_html__( 'Display Snapcode, an easy way to gain followers on Snapchat', 'g1_socials' ),
			)
		);
	}

	/**
	 * Widget contruct.
	 *
	 * @param array $instance Current widget settings.
	 */
	public function form( $instance ) {
		$instance_default = array(
			'title'      => esc_html__( 'Find me on Snapchat', 'g1_socials' ),
			'username'   => '',
			'userid'     => '',
			'useravatar' => '',
			'useravatar_hdpi' => '',
		);
		$instance = wp_parse_args( $instance, $instance_default );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo  esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e( 'Display name:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_html( $instance['username'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo  esc_attr( $this->get_field_id( 'userid' ) ); ?>"><?php esc_html_e( 'Username:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'userid' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'userid' ) ); ?>" type="text" value="<?php echo esc_html( $instance['userid'] ); ?>" />
		</p>
		<p class="g1-socials-image-picker">
			<label for="<?php echo  esc_attr( $this->get_field_id( 'useravatar' ) ); ?>"><?php esc_html_e( 'Avatar:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'useravatar' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'useravatar' ) ); ?>" type="text" value="<?php echo esc_url( $instance['useravatar'] ); ?>" />
			<a class="button button-secondary g1-add-image" href="#"><?php esc_html_e( 'Add image', 'bimber' ); ?></a>
			<a class="button button-secondary g1-delete-image" href="#"><?php esc_html_e( 'Remove image', 'bimber' ); ?></a>
		</p>
		<p class="g1-socials-image-picker">
			<label for="<?php echo  esc_attr( $this->get_field_id( 'useravatar_hdpi' ) ); ?>"><?php esc_html_e( 'Avatar HDPI:', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo  esc_attr( $this->get_field_id( 'useravatar_hdpi' ) ); ?>" name="<?php echo  esc_attr( $this->get_field_name( 'useravatar_hdpi' ) ); ?>" type="text" value="<?php echo esc_url( $instance['useravatar_hdpi'] ); ?>" />
			<a class="button button-secondary g1-add-image" href="#"><?php esc_html_e( 'Add image', 'bimber' ); ?></a>
			<a class="button button-secondary g1-delete-image" href="#"><?php esc_html_e( 'Remove image', 'bimber' ); ?></a>
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
		$instance['title'] = filter_var( $new_instance['title'], FILTER_SANITIZE_STRING );
		$instance['username'] = filter_var( $new_instance['username'], FILTER_SANITIZE_STRING );
		$instance['userid'] = filter_var( $new_instance['userid'], FILTER_SANITIZE_STRING );
		$instance['useravatar'] = filter_var( $new_instance['useravatar'], FILTER_SANITIZE_URL );
		$instance['useravatar_hdpi'] = filter_var( $new_instance['useravatar_hdpi'], FILTER_SANITIZE_URL );
		return $instance;
	}

	/**
	 * Widget output.
	 *
	 * @param array $args Widget args from registration point.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		// Get settings.
		$title = apply_filters( 'widget_title', $instance['title'] );
		$username = $instance['username'];
		$userid = $instance['userid'];
		$useravatar = $instance['useravatar'];
		$useravatar_hdpi = isset( $instance['useravatar_hdpi'] ) ? $instance['useravatar_hdpi'] : '';
		// Check if all is provided.
		if ( empty( $username ) || empty( $userid ) || empty( $useravatar ) ) {
			return;
		}
		// Get snapcode.
		$user_snapcode = g1_socials_get_snapcode( $username, $userid, $useravatar, $useravatar_hdpi );
		if ( ! $user_snapcode ) {
			return;
		}
		// Echo all widget elements.
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}
		// @TODO wp_kses_post removes srcset, how do we escape this? (user input is escaped already within function)
		echo( $user_snapcode );
		echo wp_kses_post( $args['after_widget'] );
	}
}
