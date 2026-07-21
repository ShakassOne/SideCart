<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	private static $instance;
	private $settings; private $templates; private $cart_service; private $shortcodes;
	public static function instance() { return self::$instance ?: self::$instance = new self(); }
	private function __construct() {}
	public function init() {
		$this->settings     = new Settings();
		$this->templates    = new Template_Loader();
		$this->cart_service = new Cart_Service();
		$this->shortcodes   = new Shortcodes( $this->cart_service, $this->templates );
		( new Compatibility() )->init();
		if ( is_admin() ) { ( new Admin\Admin() )->init(); }
		if ( ! Compatibility::is_woocommerce_active() ) { add_action( 'admin_notices', array( $this, 'woocommerce_notice' ) ); return; }
		( new Rest_API( $this->cart_service ) )->init();
		( new Add_To_Cart_Interceptor( $this->settings ) )->init();
		( new Frontend\Frontend( $this->settings, $this->templates ) )->init();
		$this->shortcodes->init();
	}
	public function woocommerce_notice() { echo '<div class="notice notice-error"><p>' . esc_html__( 'Shakass Side Cart Pro nécessite que WooCommerce soit actif.', 'shakass-side-cart-pro' ) . '</p></div>'; }
	public function settings() { return $this->settings; }
	public function templates() { return $this->templates; }
	public function cart_service() { return $this->cart_service; }
	public function shortcodes() { return $this->shortcodes; }
}
