<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Compatibility {
	public function init() { add_action( 'before_woocommerce_init', array( $this, 'declare_hpos' ) ); }
	public static function is_woocommerce_active() { return class_exists( 'WooCommerce' ); }
	public function declare_hpos() { if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) { \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', SSC_PLUGIN_FILE, true ); } }
}
