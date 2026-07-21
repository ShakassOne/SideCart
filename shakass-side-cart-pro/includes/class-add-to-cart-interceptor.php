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

	/** Register WooCommerce and TSL add-to-cart hooks. */
	public function init() {
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'redirect_to_drawer' ), 20, 2 );
		add_action( 'woocommerce_add_to_cart', array( $this, 'remember_tsl_rest_addition' ), 20 );
	}

	/**
	 * Mark a cart addition made through TSL's REST endpoint for the next page
	 * load. TSL owns its redirect, so this marker lets the drawer open when its
	 * redirect lands on the cart page.
	 */
	public function remember_tsl_rest_addition() {
		if ( ! $this->settings->get( 'enabled', true ) || ! $this->settings->get( 'open_after_add', true ) || ! defined( 'REST_REQUEST' ) || ! REST_REQUEST || ! function_exists( 'WC' ) || ! WC()->session ) {
			return;
		}

		$rest_route  = isset( $_REQUEST['rest_route'] ) ? (string) wp_unslash( $_REQUEST['rest_route'] ) : '';
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$is_tsl_route = 0 === strpos( $rest_route, '/tsl2/v1/cart' ) || false !== strpos( $request_uri, '/wp-json/tsl2/v1/cart' );
		if ( ! $is_tsl_route ) {
			return;
		}

		WC()->session->set( 'ssc_open_after_tsl_add', true );
	}

	/**
	 * Consume the one-time marker set when TSL adds a customized product.
	 *
	 * @return bool
	 */
	public static function consume_tsl_rest_addition() {
		if ( ! function_exists( 'WC' ) || ! WC()->session || ! WC()->session->get( 'ssc_open_after_tsl_add' ) ) {
			return false;
		}

		WC()->session->set( 'ssc_open_after_tsl_add', false );
		return true;
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
