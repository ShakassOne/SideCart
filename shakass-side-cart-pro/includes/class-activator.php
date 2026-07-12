<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin activation tasks.
 */
class Activator {
	/** Activate plugin defaults and version marker. */
	public static function activate() {
		$current = (array) get_option( 'ssc_settings', array() );
		add_option( 'ssc_settings', Settings_Schema::sanitize( $current ) );
		update_option( 'ssc_settings_version', SSC_VERSION );
	}
}
