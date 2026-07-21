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

			$design_id  = $this->get_tsl2_design_id( $item );
			$image_data = $this->get_item_image_data( $product, $item, $key, $design_id );

			$items[] = apply_filters(
				'ssc_cart_item_data',
				array(
					'key'               => $key,
					'name'              => wp_strip_all_tags( $product->get_name() ),
					'permalink'         => $this->get_item_permalink( $product, $item, $design_id ),
					'image'             => $image_data['image'],
					'mockups'           => $image_data['mockups'],
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
	private function get_item_permalink( $product, $item, $design_id ) {
		if ( $design_id > 0 && function_exists( 'tsl2_cart_item_edit_url' ) ) {
			try {
				$edit_url = tsl2_cart_item_edit_url( $item );
				if ( is_string( $edit_url ) && filter_var( $edit_url, FILTER_VALIDATE_URL ) ) {
					return esc_url_raw( $edit_url );
				}
			} catch ( \Throwable $exception ) {
				// A personalization integration must never interrupt the cart REST response.
			}
		}

		return $product->is_visible() ? esc_url_raw( $product->get_permalink( $item ) ) : '';
	}

	/**
	 * Get the T-Shirt Studio design ID without allowing an integration failure to affect the cart.
	 *
	 * @param array $item WooCommerce cart item.
	 * @return int
	 */
	private function get_tsl2_design_id( $item ) {
		if ( ! function_exists( 'tsl2_cart_item_design_id' ) ) {
			return 0;
		}

		try {
			return max( 0, (int) tsl2_cart_item_design_id( $item ) );
		} catch ( \Throwable $exception ) {
			return 0;
		}
	}

	/**
	 * Get cart item thumbnail data, with the normal WooCommerce image as a complete fallback.
	 *
	 * Mockup helpers are deliberately called only after a positive TSL design ID is found.
	 * They only read URLs here: no mockup is generated during a side-cart request.
	 *
	 * @param \WC_Product $product   Product instance.
	 * @param array       $item      WooCommerce cart item.
	 * @param string      $key       Cart item key.
	 * @param int         $design_id TSL design ID.
	 * @return array{image:string,mockups:array<string,string>}
	 */
	private function get_item_image_data( $product, $item, $key, $design_id ) {
		$image = $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) );
		$fallback = wp_kses_post( apply_filters( 'woocommerce_cart_item_thumbnail', $image, $item, $key ) );
		$result   = array( 'image' => $fallback, 'mockups' => array() );

		if ( $design_id <= 0 || ! function_exists( 'tsl2_cart_item_mockups' ) ) {
			return $result;
		}

		try {
			$mockups = tsl2_cart_item_mockups( $item );
		} catch ( \Throwable $exception ) {
			return $result;
		}

		if ( ! is_array( $mockups ) ) {
			return $result;
		}

		$recto = isset( $mockups['recto'] ) && is_string( $mockups['recto'] ) ? $mockups['recto'] : '';
		$verso = isset( $mockups['verso'] ) && is_string( $mockups['verso'] ) ? $mockups['verso'] : '';
		$recto = filter_var( $recto, FILTER_VALIDATE_URL ) ? esc_url_raw( $recto ) : '';
		$verso = filter_var( $verso, FILTER_VALIDATE_URL ) ? esc_url_raw( $verso ) : '';

		if ( '' === $recto ) {
			return $result;
		}

		$result['image'] = wp_kses_post( sprintf( '<img src="%1$s" alt="%2$s" loading="lazy" />', esc_url( $recto ), esc_attr( $product->get_name() ) ) );
		if ( '' !== $verso ) {
			$result['mockups'] = array( 'recto' => $recto, 'verso' => $verso );
		}

		return $result;
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
