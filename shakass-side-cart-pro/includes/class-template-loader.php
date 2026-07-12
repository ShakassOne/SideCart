<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Template_Loader {
	public function locate( $template ) { $template = ltrim( sanitize_file_name( $template ), '/' ); $theme = trailingslashit( get_stylesheet_directory() ) . 'shakass-side-cart/' . $template; $path = is_readable( $theme ) ? $theme : SSC_PLUGIN_DIR . 'templates/' . $template; return apply_filters( 'ssc_template_path', $path, $template ); }
	public function render( $template, $args = array() ) { $path = $this->locate( $template ); if ( is_readable( $path ) ) { extract( $args, EXTR_SKIP ); include $path; } }
}
