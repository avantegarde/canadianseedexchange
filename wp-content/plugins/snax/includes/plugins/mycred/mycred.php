<?php
/**
 * MyCred plugin functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once( trailingslashit( dirname( __FILE__ ) ) . 'lib/class-snax-mycred-hook.php' );

add_action( 'mycred_load_hooks', 'mycred_load_snax_votes_hook', 65 );
add_action( 'mycred_load_hooks', 'mycred_load_snax_format_hook', 65 );
add_filter( 'mycred_setup_hooks', 'mycred_register_snax_hooks', 65 );
add_filter( 'mycred_all_references', 'snax_mycred_add_references', 10, 1 );
/**
 * Add reference
 *
 * @param array $references References.
 * @return array
 */
function snax_mycred_add_references( $references ) {
	$references['snax_vote'] = __( 'Vote', 'snax' );
	$formats = snax_get_formats();
	$formats['quiz']['labels']['name'] = __( 'Quiz', 'snax' );
	$formats['list']['labels']['name'] = __( 'List', 'snax' );
	$formats['poll']['labels']['name'] = __( 'Poll', 'snax' );
	foreach ( $formats as $slug => $format ) {
		$slug = snax_mycred_override_format_slugs( $slug );
		$references[ 'snax_format_' . $slug ] = __( 'Publishing ', 'snax' ) . $formats[ $slug ]['labels']['name'];
	}
	return $references;
}

/**
 * Override format slugs to handle some formats as one
 *
 * @param 	string $slug  Format slug.
 * @return 	string
 */
function snax_mycred_override_format_slugs( $slug ) {
	if ( strpos( $slug, 'quiz' ) > -1 ) {
		$slug = 'quiz';
	}
	if ( strpos( $slug, 'list' ) > -1 ) {
		$slug = 'list';
	}
	if ( strpos( $slug, 'poll' ) > -1 ) {
		$slug = 'poll';
	}
	return $slug;
}

/**
 * Register hook
 *
 * @param array $installed Installed hooks.
 * @return array
 */
function mycred_register_snax_hooks( $installed ) {
	$installed['snax_vote'] = array(
		'title'         => __( 'Vote', 'snax' ),
		'description'   => __( 'Awards for voting.', 'snax' ),
		'callback'      => array( 'SnaxMyCredVoteHook' ),
	);
	$installed['snax_format'] = array(
		'title'         => __( 'Snax', 'snax' ),
		'description'   => __( 'Awards for adding Snax Formats.', 'snax' ),
		'callback'      => array( 'SnaxMyCredFormatHook' ),
	);
	return $installed;

}

/**
 * Snax Hook
 */
function mycred_load_snax_votes_hook() {
	/**
	 * Snax MyCred Hook class
	 */
	class SnaxMyCredVoteHook extends Snax_myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {
			parent::__construct( array(
				'id'       => 'snax_vote',
				'defaults' => array(
					'post_creds' => 1,
					'post_log'   => sprintf( __('Voted on "%s"', 'snax' ), '%post_title%' ),
					'item_creds' => 1,
					'item_log'   => sprintf( __('Voted on "%s"', 'snax' ), '%post_title%' ),
				),
			), $hook_prefs, $type );

		}

		/**
		 * Run.
		 */
		public function run() {
			add_action( 'snax_vote_added',   array( $this, 'vote_added' ), 10, 1 );
			add_action( 'snax_vote_removed', array( $this, 'vote_removed' ), 10, 1 );
			add_filter( 'mycred_parse_tags_snax_vote', array( $this, 'parse_custom_tags' ), 10, 2 );
		}

		/**
		 * Parse Custom Tags in Log
		 */
		public function parse_custom_tags( $content, $log_entry ) {
			$data    = maybe_unserialize( $log_entry->data );
			$post_title = get_the_title( $data['post_id'] );
			$content = str_replace( '%post_title%', $post_title, $content );
			return $content;
		}

		/**
		 * Handle added vote.
		 *
		 * @param array $vote_data      Vote data.
		 */
		public function vote_added( $vote_data ) {
			$item_id   = $vote_data['post_id'];
			$author_id = $vote_data['author_id'];

			$user_id = $author_id;

			$item = get_post( $item_id );

			// Don't assign points when voting on own posts.
			if ( (int) $item->post_author === (int) $author_id ) {
				return;
			}

			if ( snax_get_item_post_type() === get_post_type( $item_id ) ) {
				$amount = $this->prefs['item_creds'];
				$entry = $this->prefs['item_log'];
			} else {
				$amount = $this->prefs['post_creds'];
				$entry = $this->prefs['post_log'];
			}

			$ref = 'snax_vote';

			$data = array(
				'ref_type'  => $ref,
				'post_id'   => $item_id,
			);

			// Make sure this is unique.
			if ( $this->core->has_entry( $ref, $item_id, $user_id, $data, $this->mycred_type ) ) return;

			$this->core->add_creds(
				$ref,
				$user_id,
				$amount,
				$entry,
				$item_id,
				$data,
				$this->mycred_type
			);
		}

		/**
		 * Handle removed vote.
		 *
		 * @param array $vote_data      Vote data.
		 */
		public function vote_removed( $vote_data ) {
			$item_id = $vote_data['post_id'];
			$user_id = $vote_data['author_id'];

			$ref = 'snax_vote';

			$data = array(
				'ref_type'  => $ref,
				'post_id'   => $item_id,
			);

			$this->remove_creds(
				$ref,
				$item_id,
				$user_id,
				$data,
				$this->mycred_type
			);
		}

		/**
		 * Preferences.
		 */
		public function preferences() {
			$prefs = $this->prefs;
			?>
			<div class="hook-instance">
			<h3><?php _e( 'Voting for a post', 'snax' ); ?></h3>
			<div class="row">
				<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
				<label for="<?php echo $this->field_id( 'post_creds' ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'post_creds' ); ?>" id="<?php echo $this->field_id( 'post_creds' ); ?>"
				value="<?php echo $this->core->number( $prefs['post_creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'post_log' ); ?>"><?php _e( 'Log template', 'snax' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'post_log' ); ?>" id="<?php echo $this->field_id( 'post_log' ); ?>" placeholder="<?php _e( 'required', 'snax' ); ?>" value="<?php echo esc_attr( $prefs['post_log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'user' ), '%post_title%, %reaction%' ); ?></span>
			</div>
				</div>
			</div>
			<h3><?php _e( 'Voting for an item', 'snax' ); ?></h3>
			<div class="row">
				<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $this->field_id( 'item_creds' ); ?>"><?php echo $this->core->plural(); ?></label>
						<input type="text" name="<?php echo $this->field_name( 'item_creds' ); ?>" id="<?php echo $this->field_id( 'item_creds' ); ?>"
						value="<?php echo $this->core->number( $prefs['item_creds'] ); ?>" class="form-control" />
					</div>
				</div>
				<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $this->field_id( 'item_log' ); ?>"><?php _e( 'Log template', 'snax' ); ?></label>
						<input type="text" name="<?php echo $this->field_name( 'item_log' ); ?>" id="<?php echo $this->field_id( 'item_log' ); ?>" placeholder="<?php _e( 'required', 'snax' ); ?>" value="<?php echo esc_attr( $prefs['item_log'] ); ?>" class="form-control" />
						<span class="description"><?php echo $this->available_template_tags( array( 'general', 'user' ), '%post_title%' ); ?></span>
					</div>
				</div>
			</div>
			</div>
			<?php
		}

	}

}

/**
 * Snax Hook
 */
function mycred_load_snax_format_hook() {
	/**
	 * Snax MyCred Hook class
	 */
	class SnaxMyCredFormatHook extends Snax_myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {
			$defaults = array();
			$formats = snax_get_formats();

			// Posts.
			foreach ( $formats as $slug => $format ) {
				$creds 	= $slug . '_creds';
				$log 	= $slug . '_log';
				$defaults[ $creds ] = 5;
				$defaults[ $log ] = 'Published %snax_format%: %post_title%';
			}

			// Items.
			$creds 	= 'list_item_creds';
			$log 	= 'list_item_log';
			$defaults[ $creds ] = 5;
			$defaults[ $log ] = 'Published list item: %post_title%';

			parent::__construct( array(
				'id'       => 'snax_format',
				'defaults' => $defaults,
			), $hook_prefs, $type );

		}

		/**
		 * Run.
		 */
		public function run() {

			// Posts.
			add_action( 'snax_post_added',      array( $this, 'post_published' ), 10, 1 );
			add_action( 'snax_post_published',  array( $this, 'post_published' ), 10, 1 );
			add_action( 'snax_post_deleted',    array( $this, 'post_deleted' ), 10, 2 );

			// Items.
			add_action( 'snax_item_added',      array( $this, 'item_published' ), 10, 1 );
			add_action( 'snax_item_published',  array( $this, 'item_published' ), 10, 1 );
			add_action( 'snax_item_deleted',    array( $this, 'item_deleted' ), 10, 2 );

			add_filter( 'mycred_parse_tags_snax_format', array( $this, 'parse_custom_tags' ), 10, 2 );
			add_filter( 'mycred_parse_tags_snax_list_item', array( $this, 'parse_custom_tags' ), 10, 2 );
		}

		/**
		 * Parse Custom Tags in Log
		 */
		public function parse_custom_tags( $content, $log_entry ) {
			$data    = maybe_unserialize( $log_entry->data );
			$post_title = get_the_title( $data['post_id'] );

			if ( 'snax_format' === $data['ref_type'] ) {
				$formats = snax_get_formats();
				$snax_format = $formats[ $data['snax_format'] ]['labels']['name'];
				$content = str_replace( '%post_title%', $post_title, $content );
				$content = str_replace( '%snax_format%', $snax_format, $content );
			}

			if ( 'snax_list_item' === $data['ref_type'] ) {
				$content = str_replace( '%post_title%', $post_title, $content );
			}

			return $content;
		}

		/**
		 * Reward for publishing post
		 *
		 * @param int $post_id  	Post id.
		 */
		public function post_published( $post_id ) {

			$post = get_post( $post_id );

			if ( 'publish' !== get_post_status( $post ) ) {
				return;
			}

			$user_id = $post->post_author;
			$slug = snax_get_post_format( $post_id );

			if ( ! $slug ) {
				return;
			}

			$creds 	= $slug . '_creds';
			$log 	= $slug . '_log';
			$amount = $this->prefs[ $creds ];
			$entry = $this->prefs[ $log ];

			$data = array(
				'ref_type'      => 'snax_format',
				'post_id'       => $post_id,
				'snax_format'   => $slug,
			);

			$slug = snax_mycred_override_format_slugs( $slug );

			$ref = 'snax_format_' . $slug;

			// Make sure this is unique.
			if ( $this->core->has_entry( $ref, $post_id, $user_id, $data, $this->mycred_type ) ) return;

			$this->core->add_creds(
				$ref,
				$user_id,
				$amount,
				$entry,
				$post_id,
				$data,
				$this->mycred_type
			);
		}

		/**
		 * Update myCRED points
		 *
		 * @param WP_Post $post         Post object.
		 * @param string  $format       Snax post format.
		 */
		public function post_deleted( $post, $format ) {
			$this->remove_snax_format_creds( $post, $format );
			$this->remove_publishing_content_creds( $post );
		}

		protected function remove_snax_format_creds( $post, $format ) {
			$user_id = $post->post_author;

			$data = array(
				'ref_type'      => 'snax_format',
				'post_id'       => $post->ID,
				'snax_format'   => $format,
			);

			$slug = snax_mycred_override_format_slugs( $format );

			$ref = 'snax_format_' . $slug;

			$this->remove_creds(
				$ref,
				$post->ID,
				$user_id,
				$data,
				$this->mycred_type
			);
		}

		protected function remove_publishing_content_creds( $post ) {
			$user_id = $post->post_author;

			$ref = 'publishing_content';

			$this->remove_creds(
				$ref,
				$post->ID,
				$user_id,
				null,
				$this->mycred_type
			);
		}

		public function item_published( $item_id ) {
			$item = get_post( $item_id );

			if ( 'publish' !== get_post_status( $item ) ) {
				return;
			}

			$post_id = wp_get_post_parent_id( $item );
			$post    = get_post( $post_id );

			$post_author = (int) $post->post_author;
			$item_author = (int) $item->post_author;

			// Both have to be set.
			if ( ! $post_author || ! $item_author ) {
				return;
			}

			// Points are NOT granted if the item has been added by list's owner.
			if ( $post_author === $item_author ) {
				return;
			}

			$user_id = $item->post_author;
			$slug = 'list_item';
			$creds 	= $slug . '_creds';
			$log 	= $slug . '_log';
			$amount = $this->prefs[ $creds ];
			$entry = $this->prefs[ $log ];

			$data = array(
				'ref_type'      => 'snax_list_item',
				'post_id'       => $item->ID,
			);

			$ref = 'snax_list_item';

			// Make sure this is unique.
			if ( $this->core->has_entry( $ref, $item->ID, $user_id, $data, $this->mycred_type ) ) return;

			$this->core->add_creds(
				$ref,
				$user_id,
				$amount,
				$entry,
				$item->ID,
				$data,
				$this->mycred_type
			);
		}

		/**
		 * Update myCRED points
		 *
		 * @param WP_Post $item         Post object.
		 * @param bool    $directly     Optional. False means that item was removed as list dependency.
		 */
		public function item_deleted( $item, $directly = true ) {
			$user_id = $item->post_author;

			$take_points_back = apply_filters( 'snax_mycred_take_item_points_back_when_list_deleted', true );

			if ( ! $take_points_back && ! $directly ) {
				return;
			}

			$data = array(
				'ref_type'      => 'snax_list_item',
				'post_id'       => $item->ID,
			);

			$ref = 'snax_list_item';

			$this->remove_creds(
				$ref,
				$item->ID,
				$user_id,
				$data,
				$this->mycred_type
			);
		}

		/**
		 * Preferences.
		 */
		public function preferences() {
			$prefs = $this->prefs;
			$formats = snax_get_formats();
			?>
			<div class="hook-instance">
			<?php foreach ( $formats as $slug => $format ) :
				$creds 	= $slug . '_creds';
				$log 	= $slug . '_log';
				$title 	= __( 'Publishing', 'snax' ) . ' ' . $format['labels']['name'];
			?>
			<h3><?php echo esc_html( $title ); ?></h3>
			<div class="row">
				<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $this->field_id( $creds ); ?>"><?php echo $this->core->plural(); ?></label>
						<input type="text" name="<?php echo $this->field_name( $creds ); ?>" id="<?php echo $this->field_id( $creds ); ?>"
						value="<?php echo $this->core->number( $prefs[$creds] ); ?>" class="form-control" />
					</div>
				</div>
				<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $this->field_id( $log ); ?>"><?php _e( 'Log template', 'snax' ); ?></label>
						<input type="text" name="<?php echo $this->field_name( $log ); ?>" id="<?php echo $this->field_id( $log ); ?>" placeholder="<?php _e( 'required', 'snax' ); ?>" value="<?php echo esc_attr( $prefs[$log] ); ?>" class="form-control" />
						<span class="description"><?php echo $this->available_template_tags( array( 'general', 'user' ), '%post_title%, %snax_format%' ); ?></span>
					</div>
				</div>
			</div>
			<?php endforeach;?>

				<?php
				$creds 	= 'list_item_creds';
				$log 	= 'list_item_log';
				?>

				<h3><?php esc_html_e( 'Publishing new list item', 'snax' ); ?></h3>
				<div class="row">
					<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="<?php echo $this->field_id( $creds ); ?>"><?php echo $this->core->plural(); ?></label>
							<input type="text" name="<?php echo $this->field_name( $creds ); ?>" id="<?php echo $this->field_id( $creds ); ?>"
							       value="<?php echo $this->core->number( $prefs[$creds] ); ?>" class="form-control" />
						</div>
					</div>
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="<?php echo $this->field_id( $log ); ?>"><?php _e( 'Log template', 'snax' ); ?></label>
							<input type="text" name="<?php echo $this->field_name( $log ); ?>" id="<?php echo $this->field_id( $log ); ?>" placeholder="<?php _e( 'required', 'snax' ); ?>" value="<?php echo esc_attr( $prefs[$log] ); ?>" class="form-control" />
							<span class="description"><?php echo $this->available_template_tags( array( 'general', 'user' ), '%post_title%, %snax_format%' ); ?></span>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}


//add_action( 'snax_end_format', 'snax_mycred_end_format' );
function snax_mycred_end_format( $format ) {
	if ( ! apply_filters( 'snax_mycred_display_hint_points_for_publishing', true ) || ! $format->has_mycred_points_for_publishing() ) {
		return;
	}
	?>
	<p class="snax-format-gamification">
		<span class="snax-format-gamification-points">
			<?php
			$mycred = mycred();

			echo $mycred->template_tags_amount(
				_n(
					'Earn <strong>+%cred_f%</strong> %_singular% for publishing.',
					'Earn <strong>+%cred_f%</strong> %_plural% for publishing.',
					$format->get_mycred_points_for_publishing(),
					'snax'
				),
				$format->get_mycred_points_for_publishing() );
			?>
		</span>

		<?php if ( false ) : ?>
			<span class="snax-format-gamification-badges">
				<?php printf( __( 'Earn %s <strong>Meme Maker</strong> badge for publishing your first post.', 'snax' ), '<img src="http://bimber.snap/main/wp-content/uploads/sites/17/2017/12/badge-author-meme-0-5.svg" width="40" height="40" />' ); ?>
			</span>
		<?php endif; ?>

		<?php if ( false ) : ?>
			<?php if ( $format->get_learn_more_url() ) : ?>
				<span class="snax-format-gamification-more"><?php esc_html_e( 'Learn More', 'snax' ); ?></span>
			<?php endif; ?>
		<?php endif; ?>
	</p>
	<?php
}
