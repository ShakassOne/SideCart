<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Defines defaults and validation for the versioned settings option.
 */
class Settings_Schema {
	/**
	 * Default settings for Phase 1.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		return array(
			'enabled'           => true,
			'open_after_add'    => true,
			'floating_icon'     => true,
			'close_on_overlay'  => true,
			'close_on_escape'   => true,
			'position'          => 'right',
			'width'             => '420px',
			'tablet_width'      => '70vw',
			'mobile_fullscreen' => true,
			'z_index'           => 999999,
			'quantity_debounce' => 450,
			'generated_css'     => true,
			'delete_on_uninstall' => false,
		);
	}

	/**
	 * Sanitize settings before storage.
	 *
	 * @param array<string,mixed> $input Raw settings.
	 * @return array<string,mixed>
	 */
	public static function sanitize( $input ) {
		$input    = is_array( $input ) ? $input : array();
		$defaults = self::defaults();
		$output   = array();

		$booleans = array( 'enabled', 'open_after_add', 'floating_icon', 'close_on_overlay', 'close_on_escape', 'mobile_fullscreen', 'generated_css', 'delete_on_uninstall' );
		foreach ( $booleans as $key ) {
			$output[ $key ] = self::to_bool( $input[ $key ] ?? $defaults[ $key ] );
		}

		$allowed_positions   = array( 'right', 'left', 'bottom', 'fullscreen', 'modal' );
		$output['position']  = in_array( $input['position'] ?? '', $allowed_positions, true ) ? $input['position'] : $defaults['position'];
		$output['width']     = self::sanitize_css_size( $input['width'] ?? $defaults['width'], $defaults['width'] );
		$output['tablet_width'] = self::sanitize_css_size( $input['tablet_width'] ?? $defaults['tablet_width'], $defaults['tablet_width'] );
		$output['z_index']   = min( 2147483647, max( 1, absint( $input['z_index'] ?? $defaults['z_index'] ) ) );
		$output['quantity_debounce'] = min( 3000, max( 0, absint( $input['quantity_debounce'] ?? $defaults['quantity_debounce'] ) ) );

		return wp_parse_args( $output, $defaults );
	}

	/**
	 * Sanitize a CSS size supporting px, %, vw, vh, rem and em.
	 *
	 * @param mixed  $value    Raw value.
	 * @param string $fallback Fallback value.
	 * @return string
	 */
	private static function sanitize_css_size( $value, $fallback ) {
		$value = is_scalar( $value ) ? trim( (string) $value ) : '';
		if ( preg_match( '/^\d+(?:\.\d+)?(px|%|vw|vh|rem|em)$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}

	/**
	 * Convert common form values to bool.
	 *
	 * @param mixed $value Value.
	 * @return bool
	 */
	private static function to_bool( $value ) {
		return in_array( $value, array( true, 1, '1', 'yes', 'on' ), true );
	}
}
