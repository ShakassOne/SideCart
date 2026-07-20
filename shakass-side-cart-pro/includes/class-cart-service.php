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
		if ( ! function_exists( 'WC' ) ) {
			return null;
		}

		if ( null === WC()->cart && function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}

		return WC()->cart ? WC()->cart : null;
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
					'permalink'         => $this->get_item_permalink( $product, $item ),
					'image'             => $this->get_item_image_html( $product, $item, $key ),
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

		$settings = new Settings();
		return array(
			'items'         => $items,
			'coupons'       => $this->get_coupons(),
			'rewards'       => ( new Rewards() )->progress( $settings ),
			'recommendations' => ( new Recommendations() )->get_items(),
			'count'         => $this->get_count(),
			'total_html'    => wp_kses_post( $cart->get_total() ),
			'subtotal_html' => wp_kses_post( $cart->get_cart_subtotal() ),
			'cart_url'      => esc_url_raw( wc_get_cart_url() ),
			'checkout_url'  => esc_url_raw( apply_filters( 'ssc_checkout_url', wc_get_checkout_url() ) ),
		);
	}

	/**
	 * Get the best cart item link, using a personalization edit URL when available.
	 *
	 * @param \WC_Product $product Product instance.
	 * @param array       $item    WooCommerce cart item.
	 * @return string
	 */
	private function get_item_permalink( $product, $item ) {
		if ( function_exists( 'tsl2_cart_item_edit_url' ) ) {
			$edit_url = tsl2_cart_item_edit_url( $item );
			if ( $edit_url ) {
				return esc_url_raw( $edit_url );
			}
		}

		return $product->is_visible() ? esc_url_raw( $product->get_permalink( $item ) ) : '';
	}

	/**
	 * Get cart item thumbnail HTML, preferring T-Shirt Studio mockups when present.
	 *
	 * The T-Shirt Studio plugin exposes public helpers for custom mockups and also
	 * injects its image through WooCommerce's standard cart thumbnail filter. This
	 * method supports both paths while keeping the normal product image fallback.
	 *
	 * @param \WC_Product $product Product instance.
	 * @param array       $item    WooCommerce cart item.
	 * @param string      $key     Cart item key.
	 * @return string
	 */
	private function get_item_image_html( $product, $item, $key ) {
		$image = $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) );

		if ( function_exists( 'tsl2_cart_item_mockup' ) ) {
			$mockup = tsl2_cart_item_mockup( $item );
			if ( $mockup ) {
				return wp_kses_post(
					sprintf(
						'<img src="%1$s" alt="%2$s" loading="lazy" />',
						esc_url( $mockup ),
						esc_attr( $product->get_name() )
					)
				);
			}
		}

		return wp_kses_post( apply_filters( 'woocommerce_cart_item_thumbnail', $image, $item, $key ) );
	}

	/**
	 * Get applied coupons.
	 *
	 * @return array<int,array<string,string>>
	 */
	private function get_coupons() {
		$cart = $this->cart();
		if ( ! $cart ) {
			return array();
		}

		$coupons = array();
		foreach ( $cart->get_coupons() as $code => $coupon ) {
			$coupons[] = array(
				'code' => wc_format_coupon_code( $code ),
				'label' => sprintf( __( 'Code promo : %s', 'shakass-side-cart-pro' ), wc_format_coupon_code( $code ) ),
			);
		}

		return $coupons;
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
			return new \WP_Error( 'ssc_cart_unavailable', __( 'Le panier est indisponible.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		$quantity = wc_stock_amount( $quantity );
		if ( $quantity < 0 ) {
			return new \WP_Error( 'ssc_invalid_quantity', __( 'Quantité invalide.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		// Work from the cart array, which is the canonical source of the line-item
		// key for both standard WooCommerce products and personalized products.
		// Some product extensions replace the product object after it has been added
		// to the cart, but never alter this array key.
		$cart_items = $cart->get_cart();
		$item       = isset( $cart_items[ $key ] ) ? $cart_items[ $key ] : null;
		if ( ! $item ) {
			return new \WP_Error( 'ssc_missing_item', __( 'Article introuvable dans le panier.', 'shakass-side-cart-pro' ), array( 'status' => 404 ) );
		}

		$product = isset( $item['data'] ) ? $item['data'] : null;
		if ( $product && $quantity > 0 ) {
			$max = (int) $product->get_max_purchase_quantity();
			if ( $max > 0 && $quantity > $max ) {
				return new \WP_Error( 'ssc_quantity_too_high', __( 'La quantité demandée n’est pas disponible.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
			}
		}

		$updated = $cart->set_quantity( $key, $quantity, true );
		if ( false === $updated ) {
			return new \WP_Error( 'ssc_update_failed', __( 'Impossible de mettre à jour cet article du panier.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

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
