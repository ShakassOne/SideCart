<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Generates scoped CSS variables from sanitized settings.
 */
class Style_Generator {
	/**
	 * Build frontend CSS variables.
	 *
	 * @param Settings $settings Settings service.
	 * @return string
	 */
	public function generate( Settings $settings ) {
		if ( ! $settings->get( 'generated_css', true ) ) {
			return '';
		}

		$vars = array(
			'--ssc-width'         => $settings->get( 'width', '455px' ),
			'--ssc-tablet-width'  => $settings->get( 'tablet_width', '70vw' ),
			'--ssc-bg'            => $settings->get( 'drawer_background', '#090c12' ),
			'--ssc-text'          => $settings->get( 'text_color', '#f7f7f7' ),
			'--ssc-accent'        => $settings->get( 'accent_color', '#ff6a00' ),
			'--ssc-overlay'       => $settings->get( 'overlay_color', 'rgba(0,0,0,.70)' ),
			'--ssc-radius'        => $settings->get( 'drawer_radius', '12px' ),
			'--ssc-z'             => (string) $settings->get( 'z_index', 999999 ),
		);

		$declarations = array();
		foreach ( $vars as $name => $value ) {
			$declarations[] = $name . ':' . esc_html( (string) $value );
		}

		return '.ssc-root{' . implode( ';', $declarations ) . ';}';
	}
}
