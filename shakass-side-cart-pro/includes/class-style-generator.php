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
			'--ssc-width'         => $settings->get( 'width', '420px' ),
			'--ssc-tablet-width'  => $settings->get( 'tablet_width', '70vw' ),
			'--ssc-bg'            => $settings->get( 'drawer_background', '#ffffff' ),
			'--ssc-text'          => $settings->get( 'text_color', '#1f2937' ),
			'--ssc-accent'        => $settings->get( 'accent_color', '#f97316' ),
			'--ssc-overlay'       => $settings->get( 'overlay_color', 'rgba(15,23,42,0.48)' ),
			'--ssc-radius'        => $settings->get( 'drawer_radius', '0px' ),
			'--ssc-z'             => (string) $settings->get( 'z_index', 999999 ),
		);

		$declarations = array();
		foreach ( $vars as $name => $value ) {
			$declarations[] = $name . ':' . esc_html( (string) $value );
		}

		return '.ssc-root{' . implode( ';', $declarations ) . ';}';
	}
}
