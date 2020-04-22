<?php
/**
 * AdAce Functions
 *
 * @package Media Ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'after_setup_theme', 'adace_add_mace_section', 20 );
add_action( 'after_setup_theme', 	'adace_register_ad_slots' );
/**
 * Add mace section
 */
function adace_add_mace_section() {
	adace_register_ad_section( 'mace', __( 'Media Ace', 'mace' ) );
}

/**
 * Add ad slots.
 *
 * @return void
 */
function adace_register_ad_slots() {
	/**
	 * Register Gallery Slot
	 */
	adace_register_ad_slot(
		array(
			'id' => 'adace-mace-gallery',
			'name' => esc_html__( 'Gallery Sidebar', 'adace' ),
			'section' => 'mace',
		)
	);

	/**
	 * Register Inside Gallery Slot
	 */
	adace_register_ad_slot(
		array(
			'id' => 'adace-mace-inside-gallery',
			'name' => esc_html__( 'Inside Gallery', 'adace' ),
			'section' => 'mace',
			'custom_options' => array(
				'inject_gallery_items' => 3,
				'inject_gallery_items_editable' => true,
				'inject_gallery_items_repeat' => 5,
				'inject_gallery_items_repeat_editable' => true,
			),
		)
	);
}


add_filter( 'adace_options_slot_fields_filter', 'mace_gallery_inject_option', 10, 2 );
/**
 * Add option for Inside Gallery Slot
 *
 * @param array $slot_fields Slot fields.
 * @param array $adace_ad_slot Slot ad.
 */
function mace_gallery_inject_option( $slot_fields, $adace_ad_slot ) {
	if ( 'adace-mace-inside-gallery' !== $adace_ad_slot['id'] ) {
		return $slot_fields;
	}
	$slot_fields['inject_gallery_items'] = esc_html__( 'Start at position', 'adace' );
	$slot_fields['inject_gallery_items_repeat'] = esc_html__( 'Repeat after each X positions', 'adace' );
	return $slot_fields;
}

add_action( 'adace_options_slots_field_renderer_action', 'mace_gallery_inject_option_renderer', 10, 2 );
/**
 * Add renderer for After X Snax Items Slot
 *
 * @param array $args Slot args.
 * @param array $slot_options Slot options.
 */
function mace_gallery_inject_option_renderer( $args, $slot_options ) {
	if ( 'adace-mace-inside-gallery' !== $args['slot']['id'] ) {
		return;
	}

	$inject_gallery_items_editable = $args['slot']['custom_options']['inject_gallery_items_editable'];
	if ( $inject_gallery_items_editable ) {
		$inject_gallery_items_current = isset( $slot_options['inject_gallery_items'] ) ? $slot_options['inject_gallery_items'] : $args['slot']['custom_options']['inject_gallery_items'];
	} else {
		$inject_gallery_items_current = $args['slot']['custom_options']['inject_gallery_items'];
	}

	if ( 'inject_gallery_items' === $args['field_for'] ) :
		?>
		<input
			class="small-text"
			type="number"
			id="<?php echo( 'adace_slot_' . esc_html( $args['slot']['id'] ) . '_options' ); ?>[inject_gallery_items]"
			name="<?php echo( 'adace_slot_' . esc_html( $args['slot']['id'] ) . '_options' ); ?>[inject_gallery_items]"
			min="1"
			max="10000"
			step="1"
			value="<?php echo( esc_html( $inject_gallery_items_current ) ); ?>"
			<?php echo( $inject_gallery_items_editable ? '' : ' disabled' );  ?>
			/>
		<label for="<?php echo( 'adace_slot_' . esc_html( $args['slot']['id'] ) . '_options' ); ?>[inject_gallery_items]"></label>
		<?php
	endif;

	$inject_gallery_items_repeat_editable = $args['slot']['custom_options']['inject_gallery_items_repeat_editable'];
	if ( $inject_gallery_items_repeat_editable ) {
		$inject_gallery_items_repeat_current = isset( $slot_options['inject_gallery_items_repeat'] ) ? $slot_options['inject_gallery_items_repeat'] : $args['slot']['custom_options']['inject_gallery_items_repeat'];
	} else {
		$inject_gallery_items_repeat_current = $args['slot']['custom_options']['inject_gallery_items_repeat'];
	}

	if ( 'inject_gallery_items_repeat' === $args['field_for'] ) :
		?>
		<input
			class="small-text"
			type="number"
			id="<?php echo( 'adace_slot_' . esc_html( $args['slot']['id'] ) . '_options' ); ?>[inject_gallery_items_repeat]"
			name="<?php echo( 'adace_slot_' . esc_html( $args['slot']['id'] ) . '_options' ); ?>[inject_gallery_items_repeat]"
			min="0"
			max="10000"
			step="1"
			value="<?php echo( esc_html( $inject_gallery_items_repeat_current ) ); ?>"
			<?php echo( $inject_gallery_items_repeat_editable ? '' : ' disabled' );  ?>
			/>
		<label for="<?php echo( 'adace_slot_' . esc_html( $args['slot']['id'] ) . '_options' ); ?>[inject_gallery_items_repeat]"><?php esc_html_e( '0 to not repeat', 'mace' ); ?></label>
		<?php
	endif;
}

add_filter( 'adace_slots_options_save_validator_filter', 'mace_gallery_inject_option_save_validator', 10, 2 );
/**
 * Add option saving validator for After X Snax Items slot
 *
 * @param array $input_sanitized Sanitized.
 * @param array $input Original.
 */
function mace_gallery_inject_option_save_validator( $input_sanitized, $input ) {
	if ( isset( $input['inject_gallery_items'] ) ) {
		$input_sanitized['inject_gallery_items'] = intval( filter_var( $input['inject_gallery_items'], FILTER_SANITIZE_NUMBER_INT ) );
	}
	if ( isset( $input['inject_gallery_items_repeat'] ) ) {
		$input_sanitized['inject_gallery_items_repeat'] = intval( filter_var( $input['inject_gallery_items_repeat'], FILTER_SANITIZE_NUMBER_INT ) );
	}
	return $input_sanitized;
}

/**
 * Get data for injected gallery ad.
 */
function mace_adace_gallery_get_ad_inside() {

	$data = array();

	$adace_ad_slots = adace_access_ad_slots();
	// get slot register data.
	$slot_register = $adace_ad_slots['adace-mace-inside-gallery'];
	// Get slot options.
	$slot_options = get_option( 'adace_slot_adace-mace-inside-gallery_options' );

	$data['position'] = $slot_register['custom_options']['inject_gallery_items_editable'] ? $slot_options['inject_gallery_items'] : $slot_register['custom_options']['inject_gallery_items'];
	$data['repeat'] = $slot_register['custom_options']['inject_gallery_items_repeat_editable'] ? $slot_options['inject_gallery_items_repeat'] : $slot_register['custom_options']['inject_gallery_items_repeat'];

	// AdSense ad inside gallery can't be loaded asynchronously.
	remove_filter( 'adace_adsense_output', 'adace_wrap_in_loader', 10 );

	ob_start();
	echo adace_get_ad_slot( 'adace-mace-inside-gallery' );
	$data['html'] = ob_get_clean();

	add_filter( 'adace_adsense_output', 'adace_wrap_in_loader', 10, 2 );

	return $data;
}

/**
 * Get data for injected gallery ad.
 */
function mace_adace_render_sidebar_ad() {
	// AdSense ad in gallery sidebar can't be loaded asynchronously.
	remove_filter( 'adace_adsense_output', 'adace_wrap_in_loader', 10 );

	echo adace_get_ad_slot( 'adace-mace-gallery' );

	add_filter( 'adace_adsense_output', 'adace_wrap_in_loader', 10, 2 );
}