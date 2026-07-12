<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Versioned settings repository.
 */
class Settings {
	/** @var array<string,mixed>|null */
	private $settings;

	/**
	 * Get all settings merged with defaults.
	 *
	 * @return array<string,mixed>
	 */
	public function all() {
		if ( null === $this->settings ) {
			$this->settings = Settings_Schema::sanitize( (array) get_option( 'ssc_settings', array() ) );
		}

		return apply_filters( 'ssc_drawer_settings', $this->settings );
	}

	/**
	 * Get one setting.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$all = $this->all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
	}

	/**
	 * Save settings.
	 *
	 * @param array<string,mixed> $settings Raw settings.
	 * @return array<string,mixed>
	 */
	public function save( $settings ) {
		$sanitized      = Settings_Schema::sanitize( $settings );
		$this->settings = $sanitized;
		update_option( 'ssc_settings', $sanitized );
		update_option( 'ssc_settings_version', SSC_VERSION );
		update_option( 'ssc_delete_data_on_uninstall', $sanitized['delete_on_uninstall'] ? 'yes' : 'no' );

		return $sanitized;
	}
}
