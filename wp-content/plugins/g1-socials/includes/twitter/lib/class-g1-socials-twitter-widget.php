<?php
/**
 * Twitter widget
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

add_action( 'widgets_init', 'g1_socials_register_twitter_widget' );

/**
 * Widget register function
 */
function g1_socials_register_twitter_widget() {
	register_widget( 'G1_Twitter_Widget' );
}

if ( ! class_exists( 'G1_Twitter_Widget' ) ) :

	/**
	 * Class G1_Twitter_Widget
	 */
	class G1_Twitter_Widget extends WP_Widget {

		/**
		 * The total number of displayed widgets
		 *
		 * @var int
		 */
		protected static $counter = 0;

		private $access_keys_loaded = false;
		private $customer_key;
		private $customer_secret;
		private $access_token;
		private $access_token_secret;

		/**
		 * G1_Twitter_Widget constructor.
		 */
		public function __construct() {
			parent::__construct(
				'g1_twitter',                            // Base ID.
				esc_html__( 'G1 Socials Twitter', 'g1_socials' ),       // Name.
				array(                                          // Args.
					'description' => esc_html__( 'Display recent tweets.', 'g1_socials' ),
				)
			);

			self::$counter ++;
		}

		/**
		 * Get default arguments
		 *
		 * @return array
		 */
		public function get_default_args() {
			return apply_filters( 'g1_twitter_widget_defaults', array(
				'title'               => esc_html__( 'Recent Tweets', 'g1_socials' ),
				'username'            => '',
				'tweets_to_show'      => 3,
				'exclude_replies'     => 'standard',
				'cache_time'          => 120, // minutes.
			) );
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

			// Translate title.
			if ( function_exists( 'icl_translate' ) ) {
				$title = icl_translate( 'G1 Socials Twitter', 'title', $title );
			}

			// HTML id attribute.
			$html_id = 'g1-twitter-widget-' . self::$counter;
			set_query_var( 'g1_twitter_html_id', $html_id );
			set_query_var( 'g1_twitter_this', $this );
			set_query_var( 'g1_twitter_instance', $instance );

			echo wp_kses_post( $args['before_widget'] );

			if ( ! empty( $title ) ) {
				echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
			}

			g1_socials_get_template_part( 'twitter/index' );

			echo wp_kses_post( $args['after_widget'] );
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

			if ( function_exists( 'icl_register_string' ) ) {
				icl_register_string( 'G1 Socials Twitter', 'title', $instance['title'] );
			}

			$this->load_access_keys();

			?>
			<div class="g1-widget-twitter">
				<?php if ( ! $this->access_keys_set() ) : ?>
				<p style="color: #ff0000;">
					<?php printf( esc_html__( 'Your Twitter access keys and tokens are not set. Please enter them on the %s', 'g1_socials' ), '<a href="'. esc_url( $this->get_twitter_settings_url() ) .'" target="_blank">'. esc_html__( 'Twitter settings page', 'g1_socials' ) .'</a>' ); ?>
				</p>
				<?php endif; ?>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Widget title', 'g1_socials' ); ?>:</label>
					<input
						type="text"
						class="widefat"
						id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
						value="<?php echo esc_attr( $instance['title'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e( 'Username', 'g1_socials' ); ?>:</label>
					<input
						type="text"
						class="widefat"
						id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>"
						value="<?php echo esc_attr( $instance['username'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'tweets_to_show' ) ); ?>"><?php esc_html_e( 'Number of Tweets to Show', 'g1_socials' ); ?>:</label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'tweets_to_show' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'tweets_to_show' ) ); ?>">
						<option value="1"<?php selected( '1', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '1' ); ?></option>
						<option value="2"<?php selected( '2', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '2' ); ?></option>
						<option value="3"<?php selected( '3', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '3' ); ?></option>
						<option value="4"<?php selected( '4', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '4' ); ?></option>
						<option value="5"<?php selected( '5', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '5' ); ?></option>
						<option value="6"<?php selected( '6', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '6' ); ?></option>
						<option value="7"<?php selected( '7', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '7' ); ?></option>
						<option value="8"<?php selected( '8', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '8' ); ?></option>
						<option value="9"<?php selected( '9', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '9' ); ?></option>
						<option value="10"<?php selected( '10', $instance['tweets_to_show'] ); ?>><?php echo esc_html( '10' ); ?></option>
					</select>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'cache_time' ) ); ?>"><?php esc_html_e( 'Cache time (in minutes)', 'g1_socials' ); ?>:</label>
					<input
						type="number"
						class="widefat"
						id="<?php echo esc_attr( $this->get_field_id( 'cache_time' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'cache_time' ) ); ?>"
						value="<?php echo esc_attr( $instance['cache_time'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_replies' ) ); ?>"><?php esc_html_e( 'Exclude Replies', 'g1_socials' ); ?>:</label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'exclude_replies' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'exclude_replies' ) ); ?>">
						<option value="none"<?php selected( 'none', $instance['exclude_replies'] ); ?>><?php esc_html_e( 'no', 'g1_socials' ); ?></option>
						<option value="standard"<?php selected( 'standard', $instance['exclude_replies'] ); ?>><?php esc_html_e( 'yes', 'g1_socials' ); ?></option>
					</select>
				</p>
			</div>
			<?php
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

			$instance['title']               = strip_tags( $new_instance['title'] );
			$instance['username']            = strip_tags( $new_instance['username'] );
			$instance['tweets_to_show']      = strip_tags( $new_instance['tweets_to_show'] );
			$instance['exclude_replies']     = strip_tags( $new_instance['exclude_replies'] );
			$instance['cache_time']          = strip_tags( $new_instance['cache_time'] );

			// Reload cache.
			if ( $old_instance['username'] !== $new_instance['username'] ) {
				$this->purge_cache();
			}

			return $instance;
		}

		/**
		 * Fetch tweets
		 *
		 * @param array $instance       Widget data.
		 *
		 * @return array|WP_Error
		 */
		public function get_tweets( $instance ) {
			// Fetch fresh tweets.
			if ( $this->is_cache_expired( $instance['cache_time'] ) ) {
				require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'twitteroauth.php';

				$connection = new TwitterOAuth(
					$this->customer_key,
					$this->customer_secret,
					$this->access_token,
					$this->access_token_secret
				);

				$twitter_api_url = apply_filters(
					'g1_twitter_widget_api_user_timeline_url',
					'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=%s&count=%d&exclude_replies=%d&include_rts=1'
				);

				// We have to set higher value because replies (event if the exclude_replies flag is set to true) are counted.
				$number_of_tweets_to_fetch = apply_filters( 'g1_twitter_widget_number_of_tweets_to_fetch', 100 );

				$fetch_url = sprintf( $twitter_api_url, $instance['username'], $number_of_tweets_to_fetch, 'standard' === $instance['exclude_replies'] ? 1 : 0 );

				$tweets = $connection->get( $fetch_url );

				if ( empty( $tweets->errors ) ) {
					$tweets_array = array();
					$tweets_count = count( $tweets );

					for ( $i = 0; $i <= $tweets_count; $i++ ) {
						if ( ! empty( $tweets[ $i ] ) ) {
							$tweet = $tweets[ $i ];

							$tweets_array[ $i ]['id']            = $tweet->id;
							$tweets_array[ $i ]['id_str']        = $tweet->id_str;
							$tweets_array[ $i ]['created_at']    = $tweet->created_at;
							$tweets_array[ $i ]['text']          = preg_replace( '/[\x{10000}-\x{10FFFF}]/u', '', $tweet->text );
							$tweets_array[ $i ]['retweet_count'] = ! empty( $tweet->retweet_count ) ? $tweet->retweet_count: 0;

							if ( ! empty( $tweet->user ) ) {
								$user = $tweet->user;

								$tweets_array[ $i ]['user'] = array(
									'id'                => ! empty( $user->id ) ? $user->id : '',
									'name'              => ! empty( $user->name ) ? $user->name : '',
									'screen_name'       => ! empty( $user->screen_name ) ? $user->screen_name : '',
									'profile_image_url' => ! empty( $user->profile_image_url ) ? $user->profile_image_url : '',
									'profile_image_url_https' => ! empty( $user->profile_image_url_https ) ? $user->profile_image_url_https : '',
								);
							}
						}
					}

					$this->update_tweets( $tweets_array );
				} else {
					return new WP_Error( 'g1_twitter_widget_fetch_error', $tweets->errors[0]->message );
				}
			}

			// Get cached tweets.
			$tweets = get_option( 'g1_twitter_widget_tweets' );

			return $tweets;
		}

		/**
		 * Check whether the tweets cache is expired
		 *
		 * @param string $cache_time        Cache time in hours.
		 *
		 * @return bool
		 */
		protected function is_cache_expired( $cache_time ) {
			$last_cache_time = get_option( 'g1_twitter_widget_last_cache_time' );

			// Flag was not set or was removed to force cache reload.
			if ( empty( $last_cache_time ) ) {
				return true;
			}

			$diff_ms       = time() - $last_cache_time;
			$cache_time_ms = $cache_time * 60; // Cache time is in minutes.

			// Cache expired.
			return $diff_ms >= $cache_time_ms;
		}

		/**
		 * Update tweets in database
		 *
		 * @param array $tweets_array       Array of tweets.
		 */
		protected function update_tweets( $tweets_array ) {
			update_option( 'g1_twitter_widget_tweets', $tweets_array );
			update_option( 'g1_twitter_widget_last_cache_time', time() );
		}

		/**
		 * Return time of tweets last update
		 *
		 * @return string
		 */
		public function get_last_update_time() {
			return get_option( 'g1_twitter_widget_last_cache_time' );
		}

		/**
		 * Purge tweets cache
		 */
		protected function purge_cache() {
			delete_option( 'g1_twitter_widget_last_cache_time' );
		}

		/**
		 * Check if all required fields are set
		 *
		 * @param array $instance   Widget data.
		 *
		 * @return bool
		 */
		public function is_widget_configured( $instance ) {
			return ! empty( $instance['username'] ) && $this->access_keys_set();

		}

		protected function load_access_keys() {
			$this->customer_key        = get_option( 'g1_socials_twitter_consumer_key', '' );
			$this->customer_secret     = get_option( 'g1_socials_twitter_consumer_secret', '' );
			$this->access_token        = get_option( 'g1_socials_twitter_access_token', '' );
			$this->access_token_secret = get_option( 'g1_socials_twitter_access_token_secret', '' );

			$this->access_keys_loaded = true;
		}

		protected function access_keys_set() {
			if ( ! $this->access_keys_loaded ) {
				$this->load_access_keys();
			}

			return
				! empty( $this->customer_key ) &&
				! empty( $this->customer_secret ) &&
				! empty( $this->access_token ) &&
				! empty( $this->access_token_secret );
		}

		public function get_twitter_settings_url() {
			return add_query_arg(
				array(
					'page' => g1_socials_options_page_slug(),
					'tab'  => 'g1_socials_twitter',
				),
				admin_url( 'options-general.php' )
			);
		}
	}

endif;
