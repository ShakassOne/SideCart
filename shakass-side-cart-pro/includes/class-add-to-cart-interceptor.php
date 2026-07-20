<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Returns shoppers to the originating page after a non-Ajax WooCommerce add.
 */
class Add_To_Cart_Interceptor {
	/** @var Settings */
	private $settings;

	/**
	 * @param Settings $settings Settings service.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/** Register the WooCommerce redirect filter. */
	public function init() {
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'redirect_to_drawer' ), 20, 2 );
	}

	/**
	 * Replace WooCommerce's cart-page redirect with the originating page and a
	 * one-time flag consumed by the drawer JavaScript.
	 *
	 * Ajax additions do not use this redirect; they are handled by the
	 * `added_to_cart` event in the frontend application.
	 *
	 * @param string      $redirect_url WooCommerce's proposed redirect URL.
	 * @param \WC_Product $product      Product being added to the cart.
	 * @return string
	 */
	public function redirect_to_drawer( $redirect_url, $product ) {
		if ( ! $this->settings->get( 'enabled', true ) || ! $this->settings->get( 'open_after_add', true ) || wp_doing_ajax() ) {
			return $redirect_url;
		}

		$referer = wp_get_referer();
		if ( $referer ) {
			$redirect_url = wp_validate_redirect( $referer, $redirect_url );
		} elseif ( $product instanceof \WC_Product ) {
			$redirect_url = $product->get_permalink();
		}

		return add_query_arg( 'ssc-open-cart', '1', $redirect_url );
	}
}
