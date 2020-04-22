<?php
/**
 * Patreon Widget
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package g1_socials_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'widgets_init', 'g1_socials_register_instagram_widget' );
/**
 * About me widget register function.
 */
function g1_socials_register_instagram_widget() {
	register_widget( 'G1_Socials_Instagram_Widget' );
}

/**
 * Patreon widget class.
 */
class G1_Socials_Instagram_Widget extends WP_widget {
	/**
	 * Widget contruct.
	 */
	function __construct() {
		parent::__construct(
			'g1_socials_instagram_widget',
			esc_html__( 'G1 Socials Instagram', 'g1_socials' ),
			array(
				'classname'     => 'widget_g1_socials_instagram',
				'description'   => esc_html__( 'Promote your Instagram.', 'g1_socials' ),
			)
		);
	}

	/**
	 * Get default arguments
	 *
	 * @return array
	 */
	public function get_default_args() {
		return apply_filters( 'g1_socials_widget_instagram_defaults', array(
			'title'               => esc_html__( 'Instagram', 'g1_socials' ),
			'limit'               => 9,
			'size'                => 'large',
			'afterwidget_details' => true,
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
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of photos', 'g1_socials' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_html_e( 'Photo size', 'g1_socials' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" class="widefat">
				<option value="thumbnail" <?php selected( 'thumbnail', $instance['size'] ); ?>><?php esc_html_e( 'Thumbnail', 'g1_socials' ); ?></option>
				<option value="small" <?php selected( 'small', $instance['size'] ); ?>><?php esc_html_e( 'Small', 'g1_socials' ); ?></option>
				<option value="large" <?php selected( 'large', $instance['size'] ); ?>><?php esc_html_e( 'Large', 'g1_socials' ); ?></option>
				<option value="original" <?php selected( 'original', $instance['size'] ); ?>><?php esc_html_e( 'Original', 'g1_socials' ); ?></option>
			</select>
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
		$instance               = array();
		$default_args           = $this->get_default_args();
		$instance['title']      = filter_var( $new_instance['title'], FILTER_SANITIZE_STRING );
		$instance['limit']      = empty( $new_instance['limit'] ) ? $default_args['limit'] : intval( $new_instance['limit'] );
		$instance['size']       = empty( $new_instance['size'] ) ? $default_args['size'] : $new_instance['size'];

		return $instance;
	}

	/**
	 * Widget output.
	 *
	 * @param array $args Widget args from registration point.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$instance            = wp_parse_args( $instance, $this->get_default_args() );
		$title               = apply_filters( 'widget_title', $instance['title'] );
		$limit               = $instance['limit'];
		//$size                = $instance['size'];
		$size                = 'original';
		$afterwidget_details = $instance['afterwidget_details'];

		// Echo all widget elements.
		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		$link       = get_option( 'g1_socials_instagram_follow_text', '' );
		$target     = get_option( 'g1_socials_instagram_target', '_blank' );
		$cache_time = (int) get_option( 'g1_socials_instagram_cache_time', 120 );

		// Filters the feed before it is retrieved.
		$instagram_feed = apply_filters( 'pre_g1_socials_instagram_feed', false, $limit );

		if ( ! $instagram_feed ) {
			$instagram_feed = apply_filters( 'g1_socials_instagram_feed', g1_socials_get_instagram_feed( $cache_time ), $limit );
		}

		if ( 0 === $cache_time && current_user_can( 'administrator' ) ) {
			echo '<p style="color: #ff0000;">';
			echo esc_html_x( 'The Instagram cache is disabled (cache time = 0). Please enable the cache before going live. This info is only visible for administrator', 'Instagram widget', 'g1_socials' );
			echo '</p>';
		}

		if ( is_wp_error( $instagram_feed ) ) {
			echo wp_kses_post( $instagram_feed->get_error_message() );
		} else if ( empty( $instagram_feed ) ) {
			printf( esc_html__( 'Feed is empty. Please check if the provided token is associated with correct account.', 'g1_socials' ) );
		} else {
			// Slice list down to required limit.
			$instagram_feed = array_slice( $instagram_feed, 0, $limit );

			$user_data = get_option( 'g1_socials_instagram_token_owner' );
			$username = $user_data['user']['username'];

			$final_class = array(
				'g1-instagram',
				'g1-instagram-size-' . $size,
			);
			?>
			<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $final_class ) ); ?>">
				<ul class="g1-instagram-items">
					<?php foreach ( $instagram_feed as $item ) : ?>
						<?php
						$item_class = array(
							'g1-instagram-item',
							'g1-instagram-item-' . $item['type'],
						);
						?>
						<li class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $item_class ) ); ?>">
							<a href="<?php echo( esc_url( $item['link'] ) ); ?>" target="<?php echo( esc_html( $target ) ); ?>" >
								<?php
								$img = sprintf(
									'<img src="%1$s" srcset="%1$s 1x" alt="%2$s" title="%2$s" />',
									esc_url( $item['original'] ),
									esc_attr( $item['description'] )
								);

								echo apply_filters( 'g1_socials_instagram_item_image', $img );
								?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php

			if ( $afterwidget_details ) {
				$url = sprintf( 'https://www.instagram.com/%s/', $username );
				?>

				<div class="g1-instagram-overview">
					<p class="g1-instagram-profile">
						<a class="g1-delta g1-delta-1st g1-instagram-username" href="<?php echo trailingslashit( esc_url( $url ) ); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>">
							&#64;<?php echo wp_kses_post( $username ); ?>
						</a>
					</p>

					<?php if ( '' !== $link ) : ?>
						<p class="g1-instagram-follow">
							<a class="g1-button g1-button-s g1-button-simple" href="<?php echo trailingslashit( esc_url( $url ) ); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>">
								<?php echo wp_kses_post( $link ); ?>
							</a>
						</p>
					<?php endif; ?>
				</div>
				<?php
			}
		}

		echo wp_kses_post( $args['after_widget'] );
	}
}
