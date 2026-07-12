<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Coupon operations through WooCommerce APIs.
 */
class Coupons {
	/** Appliquer un code promo. */
	public function apply( $code ) {
		$code = wc_format_coupon_code( wp_unslash( $code ) );
		if ( '' === $code ) {
			return new \WP_Error( 'ssc_coupon_empty', __( 'Veuillez saisir un code promo.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		if ( ! WC()->cart ) {
			return new \WP_Error( 'ssc_cart_unavailable', __( 'Le panier est indisponible.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		if ( WC()->cart->has_discount( $code ) ) {
			return new \WP_Error( 'ssc_coupon_exists', __( 'Ce code promo est déjà appliqué.', 'shakass-side-cart-pro' ), array( 'status' => 409 ) );
		}

		$result = WC()->cart->apply_coupon( $code );
		WC()->cart->calculate_totals();

		if ( ! $result ) {
			return new \WP_Error( 'ssc_coupon_invalid', __( 'Le code promo n’a pas pu être appliqué.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		return true;
	}

	/** Remove a coupon code. */
	public function remove( $code ) {
		$code = wc_format_coupon_code( wp_unslash( $code ) );
		if ( '' === $code || ! WC()->cart ) {
			return new \WP_Error( 'ssc_coupon_missing', __( 'Le code promo n’a pas pu être retiré.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		WC()->cart->remove_coupon( $code );
		WC()->cart->calculate_totals();

		return true;
	}
}
