<?php
/**
 * Plugin Name: Shakass Side Cart Pro
 * Description: Panier latéral Ajax WooCommerce par Shakass Communication.
 * Version: 1.0.0-beta.11
 * Author: Shakass Communication
 * Text Domain: shakass-side-cart-pro
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 * WC requires at least: 8.0
 * WC tested up to: 10.0
 * License: Proprietary
 */

defined( 'ABSPATH' ) || exit;

define( 'SSC_VERSION', '1.0.0-beta.11' );
define( 'SSC_PLUGIN_FILE', __FILE__ );
define( 'SSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SSC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SSC_TEXT_DOMAIN', 'shakass-side-cart-pro' );

spl_autoload_register(
	static function ( $class ) {
		$prefix = 'Shakass\\SideCartPro\\';
		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}
		$relative = substr( $class, strlen( $prefix ) );
		$parts    = explode( '\\', $relative );
		$base     = 'includes';
		if ( 'Admin' === $parts[0] ) {
			$base = 'admin';
			array_shift( $parts );
		} elseif ( 'Frontend' === $parts[0] ) {
			$base = 'public';
			array_shift( $parts );
		}
		$file = SSC_PLUGIN_DIR . $base . '/class-' . strtolower( str_replace( '_', '-', implode( '-', $parts ) ) ) . '.php';
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
);

register_activation_hook( __FILE__, array( 'Shakass\\SideCartPro\\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Shakass\\SideCartPro\\Deactivator', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		load_plugin_textdomain( 'shakass-side-cart-pro', false, dirname( SSC_PLUGIN_BASENAME ) . '/languages' );
		Shakass\SideCartPro\Plugin::instance()->init();
	}
);

function ssc_render_cart_icon() { Shakass\SideCartPro\Plugin::instance()->shortcodes()->render_cart_icon(); }
function ssc_get_cart_count() { return Shakass\SideCartPro\Plugin::instance()->cart_service()->get_count(); }
function ssc_get_cart_total() { return Shakass\SideCartPro\Plugin::instance()->cart_service()->get_total_html(); }
