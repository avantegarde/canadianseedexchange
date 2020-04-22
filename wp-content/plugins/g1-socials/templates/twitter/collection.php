<?php
/**
 * Twitter item template part
 *
 * @package g1-socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $g1_socials_twitter;

if ( empty( $g1_socials_twitter ) ) {
	return;
}

$g1_twitter_user     = $g1_socials_twitter['tweets'][0]['user'];
$g1_twitter_user_url = g1_socials_twitter_get_user_url( $g1_twitter_user );
?>



<ul class="g1-twitter-items" data-g1-tweets-updated-at="<?php echo esc_html( $g1_socials_twitter['updated_at'] ); ?>">
	<?php
	global $g1_socials_twitter_item;

	for ( $i = 0; $i < $g1_socials_twitter['max']; $i++ ) {
		$g1_socials_twitter_item = $g1_socials_twitter['tweets'][ $i ];

		g1_socials_get_template_part( 'twitter/item' );
	}
	?>
</ul>

<p class="g1-twitter-overview">
	<a class="g1-twitter-user" href="<?php echo esc_url( $g1_twitter_user_url ); ?>" target="_blank">
		<img class="g1-twitter-avatar" src="<?php echo esc_url( g1_socials_twitter_get_user_profile_image_url( $g1_twitter_user ) ); ?>" alt="avatar" />
		<span class="g1-twitter-username">@<?php echo esc_html( $g1_twitter_user['screen_name'] ); ?></span>
	</a>

	<a class="g1-twitter-follow g1-button g1-button-xs g1-button-simple" href="<?php echo esc_url( $g1_twitter_user_url ); ?>" target="_blank">
		<?php esc_html_e( 'Follow', 'g1_socials' ); ?>
	</a>
</p>