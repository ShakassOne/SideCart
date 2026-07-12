<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Server-side cart access. Prices and totals always come from WooCommerce.
 */
class Cart_Service {
	/**
	 * Get current WooCommerce cart.
	 *
	 * @return \WC_Cart|null
	 */
	private function cart() {
		return function_exists( 'WC' ) && WC()->cart ? WC()->cart : null;
	}

	/**
	 * Get item count.
	 *
	 * @return int
	 */
	public function get_count() {
		$cart = $this->cart();
		return $cart ? (int) $cart->get_cart_contents_count() : 0;
	}

	/**
	 * Get formatted total.
	 *
	 * @return string
	 */
	public function get_total_html() {
		$cart = $this->cart();
		return $cart ? wp_kses_post( $cart->get_total() ) : '';
	}

	/**
	 * Get normalized cart data for the drawer.
	 *
	 * @return array<string,mixed>
	 */
	public function get_cart_data() {
		$cart = $this->cart();

		if ( ! $cart ) {
			return array(
				'items'         => array(),
				'count'         => 0,
				'total_html'    => '',
				'subtotal_html' => '',
				'cart_url'      => '',
				'checkout_url'  => '',
			);
		}

		$cart->calculate_totals();

		$items = array();
		foreach ( $cart->get_cart() as $key => $item ) {
			$product = isset( $item['data'] ) ? $item['data'] : null;

			if ( ! $product || ! $product->exists() ) {
				continue;
			}

			$items[] = apply_filters(
				'ssc_cart_item_data',
				array(
					'key'               => $key,
					'name'              => wp_strip_all_tags( $product->get_name() ),
					'permalink'         => $product->is_visible() ? esc_url_raw( $product->get_permalink( $item ) ) : '',
					'image'             => wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ),
					'quantity'          => (int) $item['quantity'],
					'price'             => wp_kses_post( wc_price( wc_get_price_to_display( $product ) ) ),
					'line_total'        => wp_kses_post( wc_price( $item['line_total'] + $item['line_tax'] ) ),
					'min'               => 0,
					'max'               => (int) $product->get_max_purchase_quantity(),
					'sold_individually' => (bool) $product->is_sold_individually(),
				),
				$item
			);
		}

		return array(
			'items'         => $items,
			'count'         => $this->get_count(),
			'total_html'    => wp_kses_post( $cart->get_total() ),
			'subtotal_html' => wp_kses_post( $cart->get_cart_subtotal() ),
			'cart_url'      => esc_url_raw( wc_get_cart_url() ),
			'checkout_url'  => esc_url_raw( apply_filters( 'ssc_checkout_url', wc_get_checkout_url() ) ),
		);
	}

	/**
	 * Update item quantity using WooCommerce validation and calculations.
	 *
	 * @param string $key      Cart item key.
	 * @param int    $quantity Requested quantity.
	 * @return array<string,mixed>|\WP_Error
	 */
	public function set_quantity( $key, $quantity ) {
		$cart = $this->cart();

		if ( ! $cart || ! is_string( $key ) || '' === $key ) {
			return new \WP_Error( 'ssc_cart_unavailable', __( 'Cart is unavailable.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		$quantity = wc_stock_amount( $quantity );
		if ( $quantity < 0 ) {
			return new \WP_Error( 'ssc_invalid_quantity', __( 'Invalid quantity.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		$item = $cart->get_cart_item( $key );
		if ( ! $item ) {
			return new \WP_Error( 'ssc_missing_item', __( 'Cart item not found.', 'shakass-side-cart-pro' ), array( 'status' => 404 ) );
		}

		$product = isset( $item['data'] ) ? $item['data'] : null;
		if ( $product && $quantity > 0 ) {
			$max = (int) $product->get_max_purchase_quantity();
			if ( $max > 0 && $quantity > $max ) {
				return new \WP_Error( 'ssc_quantity_too_high', __( 'Requested quantity is not available.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
			}
		}

		$cart->set_quantity( $key, $quantity, true );
		return $this->get_cart_data();
	}

	/**
	 * Remove a cart item.
	 *
	 * @param string $key Cart item key.
	 * @return array<string,mixed>|\WP_Error
	 */
	public function remove_item( $key ) {
		return $this->set_quantity( $key, 0 );
	}
}
