<?php
/**
 * Instagram
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Return Instagram feed
 *
 * @param string $username      Username/Tag to be displayed.
 * @param int    $cache_time    How long the feed should be not updated.
 *
 * @return string|WP_Error
 */
function g1_socials_get_instagram_feed( $cache_time ) {
	$instagram_feed = get_transient( 'g1-socials-instagram-cache' );

	$cache_time = (int) $cache_time;

	// Fetch if cache disabled or cache expired.
	if ( 0 === $cache_time || false === $instagram_feed ) {
		$instagram_feed = g1_socials_fetch_instagram( $cache_time );
	}

	if ( is_wp_error( $instagram_feed ) ) {
		return new WP_Error( 'g1_instagram_fetch_failed', $instagram_feed->get_error_message() );
	}

	if ( empty( $instagram_feed ) || false === $instagram_feed ) {
		return new WP_Error( 'g1_instagram_empty_feed', esc_html__( 'Instagram did not return any data.', 'g1_socials' ) );
	}

	return unserialize( base64_decode( $instagram_feed ) );
}

/**
 * Fetch Instagram feed
 *
 * @param int    $cache_time    Cache time.
 *
 * @return string|WP_Error
 */
function g1_socials_fetch_instagram( $cache_time ) {
	$instagram = array();

	$images = g1_socials_instagram_get_images();

//	$images = array();
//	$images[] = array(
//        'id' => '17858172760268525',
//        'caption' => 'Some awesome photos for our Bimber - Viral Magazine Theme #viral #WordPress #magazine #theme',
//        'media_type' => 'IMAGE',
//        'media_url' => 'https://scontent.xx.fbcdn.net/v/t51.2885-15/35574496_2058370501156700_6285065256167276544_n.jpg?_nc_cat=105&_nc_oc=AQl7WRQEhm-ajfrAlBedU88bIwpMsEZ45coTB_hlsZH6oSvQUCTBFROSDC14J6bXDTY&_nc_ht=scontent.xx&oh=79ddf32bd2f62a7170800e052151e954&oe=5E5DEB18',
//        'permalink' => 'https://www.instagram.com/p/Bkwhpq_HRkE/',
//        'timestamp' => '2018-07-03T05:44:02+0000',
//        'username' => 'bringthepixel',
//	);

	if ( is_wp_error( $images ) ) {
		return $images;
	}

	foreach ( $images as $image ) {
		$caption = '';

		if ( ! empty( $image['caption'] ) ) {
			$caption = $image['caption'];
		}

		$feed_item = array(
			'description' => $caption,
			'link'        => $image['permalink'],
			'time'        => $image['timestamp'],
		);

		switch( $image['media_type'] ) {
			case 'IMAGE':
				$feed_item['type'] = 'image';
				$feed_item['thumbnail'] = $image['media_url'];
				$feed_item['small'] = $image['media_url'];
				$feed_item['large'] = $image['media_url'];
				$feed_item['original'] = $image['media_url'];
				break;

			case 'VIDEO':
				$feed_item['type'] = 'video';
				$feed_item['thumbnail'] = $image['thumbnail_url'];
				$feed_item['small'] = $image['thumbnail_url'];
				$feed_item['large'] = $image['thumbnail_url'];
				$feed_item['original'] = $image['thumbnail_url'];
				break;
		}

		$instagram[] = $feed_item;
	}



	if ( ! empty( $instagram ) ) {
		$instagram = base64_encode( serialize( $instagram ) );

		if ( $cache_time > 0 ) {
			// Cache data.
			set_transient( 'g1-instagram-cache', $instagram, $cache_time * 60 );
		} else {
			// Clear cache.
			delete_transient( 'g1-instagram-cache' );
		}

		return $instagram;
	} else {
		return '';
	}
}

function g1_socials_instagram_get_images() {
	$token = get_option( 'g1_socials_instagram_token', '' );

	if ( empty( $token ) ) {
		return new WP_Error( 'g1_socials_instagram_empty_token',__( 'Please fill in the Instagram Access Token in the plugin settings', 'g1_socials' ) );
	}

	/*
	 Media fields:

	    caption - The Media's caption text. Not returnable for Media in albums

		id - The Media's ID.

		media_type - The Media's type. Can be IMAGE, VIDEO, or CAROUSEL_ALBUM.

		media_url - The Media's URL.

		permalink - The Media's permanent URL.

		thumbnail_url - The Media's thumbnail image URL. Only available on VIDEO Media.

		timestamp - The Media's publish date in ISO 8601 format.

		username - The Media owner's username.
	 */

	$url = sprintf( 'https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=%s', $token );

	$ret = wp_remote_get( $url );

	if ( is_wp_error( $ret ) ) {
		return $ret;
	}

	$response = $ret['response'];

	if ( 200 !== (int) $response['code'] ) {
		$body = json_decode( $ret['body'], true );

		$error_msg = $response['message'];

		if ( isset( $body['error'] ) ) {
			$error_msg .= '. ' . $body['error']['message'];
		}

		return new WP_Error( $response['code'], $error_msg );
	}

	$body = json_decode( $ret['body'], true );

	$images = $body['data'];

	if ( ! is_array( $images ) ) {
		return new WP_Error( 'g1_instagram_bad_array', esc_html__( 'Instagram data is not an array.', 'g1_socials' ) );
	}

	return $images;
}
