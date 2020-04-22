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

add_action( 'mycred_load_hooks', 'mycred_load_wyr_hook', 65 );
add_filter( 'mycred_setup_hooks', 'mycred_register_wyr_hook', 65 );
add_filter( 'mycred_all_references', 'wyr_mycred_add_reference', 10, 1 );
/**
 * Add reference
 *
 * @param array $references References.
 * @return array
 */
function wyr_mycred_add_reference( $references ) {
	$references['whats-your-reaction'] = _x( "React to a post", 'myCRED reference', 'wyr' );
	return $references;
}

/**
 * Register hook
 *
 * @param array $installed Installed hooks.
 * @return array
 */
function mycred_register_wyr_hook( $installed ) {
	$installed['whats-your-reaction'] = array(
		'title'         => _x( 'React to a post', 'myCRED hook name', 'wyr' ),
		'description'   => _x( 'Awards for reactions.', 'myCRED hook description', 'wyr' ),
		'callback'      => array( 'WyrMyCredHook' ),
	);

	return $installed;

}

/**
 * Wyr Hook
 */
function mycred_load_wyr_hook() {
	/**
	 * Wyr MyCred Hook class
	 */
	class WyrMyCredHook extends myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {
			parent::__construct( array(
				'id'       => 'whats-your-reaction',
				'defaults' => array(
					'creds' => 1,
					'log'   => _x( 'Reacted to %post_title% with: %reaction%', 'myCRED hook log', 'wyr' ),
				),
			), $hook_prefs, $type );

		}

		/**
		 * Run.
		 */
		public function run() {
			add_action( 'wyr_vote_added', array( $this, 'vote_added' ), 10, 2 );
			add_filter( 'mycred_parse_tags_wyr_reaction', array( $this, 'parse_custom_tags' ), 10, 2 );
		}

		/**
		 * Parse Custom Tags in Log
		 */
		public function parse_custom_tags( $content, $log_entry ) {
			$data    = maybe_unserialize( $log_entry->data );
			$post_title = get_the_title( $data['post_id'] );
			$content = str_replace( '%post_title%', $post_title, $content );
			$content = str_replace( '%reaction%',  $data['reaction'], $content );
			return $content;
		}

		/**
		 * Handle added vote.
		 *
		 * @param array $vote_arr  Vote array.
		 * @param array $meta		Post meta.
		 */
		public function vote_added( $vote_arr, $meta ) {
			$user_id = $vote_arr['author_id'];
			$amount = $this->prefs['creds'];
			$entry = $this->prefs['log'];
			$reaction = wyr_get_reaction( $vote_arr['type'] );
			$data = array(
				'ref_type'   => 'wyr_reaction',
				'reaction' => $reaction->name,
				'post_id' => $vote_arr['post_id'],
			);
			$this->core->add_creds(
				'whats-your-reaction',
				$user_id,
				$amount,
				$entry,
				'',
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
			<h3><?php _ex( 'Reacting to a post', 'myCRED hook instance', 'wyr' ); ?></h3>
			<div class="row">
				<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $this->field_id( 'creds' ); ?>"><?php echo $this->core->plural(); ?></label>
						<input type="text" name="<?php echo $this->field_name( 'creds' ); ?>" id="<?php echo $this->field_id( 'creds' ); ?>"
						value="<?php echo $this->core->number( $prefs['creds'] ); ?>" class="form-control" />
					</div>
				</div>
				<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="<?php echo $this->field_id( 'log' ); ?>"><?php _e( 'Log template', 'wyr' ); ?></label>
						<input type="text" name="<?php echo $this->field_name( 'log' ); ?>" id="<?php echo $this->field_id( 'log' ); ?>" placeholder="<?php _e( 'required', 'wyr' ); ?>" value="<?php echo esc_attr( $prefs['log'] ); ?>" class="form-control" />
						<span class="description"><?php echo $this->available_template_tags( array( 'general', 'user' ), '%post_title%, %reaction%' ); ?></span>
					</div>
				</div>
			</div>
			</div>
			<?php
		}

	}

}
