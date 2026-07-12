<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Locates and renders overrideable templates.
 */
class Template_Loader {
	/**
	 * Locate a template in the child/theme override first, then plugin templates.
	 *
	 * @param string $template Template file name.
	 * @return string
	 */
	public function locate( $template ) {
		$template = $this->normalize_template_name( $template );
		$theme    = trailingslashit( get_stylesheet_directory() ) . 'shakass-side-cart/' . $template;
		$path     = is_readable( $theme ) ? $theme : SSC_PLUGIN_DIR . 'templates/' . $template;

		return apply_filters( 'ssc_template_path', $path, $template );
	}

	/**
	 * Render a template with isolated arguments.
	 *
	 * @param string              $template Template file name.
	 * @param array<string,mixed> $args     Template args.
	 */
	public function render( $template, $args = array() ) {
		$path = $this->locate( $template );

		if ( ! is_readable( $path ) ) {
			return;
		}

		if ( ! empty( $args ) ) {
			extract( $args, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		include $path;
	}

	/**
	 * Avoid arbitrary paths while allowing known template file names.
	 *
	 * @param string $template Template file name.
	 * @return string
	 */
	private function normalize_template_name( $template ) {
		$template = ltrim( str_replace( array( '../', '..\\', '\\' ), '', (string) $template ), '/' );
		$template = sanitize_file_name( $template );

		return $template;
	}
}
