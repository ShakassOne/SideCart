<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Lightweight recommendations based on WooCommerce cross-sells.
 */
class Recommendations {
	/**
	 * Get recommendation cards for the current cart.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function get_items() {
		if ( ! WC()->cart ) {
			return array();
		}

		$ids     = array();
		$in_cart = array();
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'] ?? null;
			if ( $product ) {
				$in_cart[] = $product->get_id();
				$ids       = array_merge( $ids, $product->get_cross_sell_ids() );
			}
		}

		$ids = array_values( array_diff( array_unique( array_map( 'absint', $ids ) ), $in_cart ) );
		$ids = array_slice( $ids, 0, 4 );
		$items = array();

		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
				continue;
			}

			$items[] = array(
				'id'        => $id,
				'name'      => wp_strip_all_tags( $product->get_name() ),
				'price'     => wp_kses_post( $product->get_price_html() ),
				'permalink' => esc_url_raw( $product->get_permalink() ),
				'image'     => wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ),
			);
		}

		return apply_filters( 'ssc_recommended_products', $items, WC()->cart );
	}
}
