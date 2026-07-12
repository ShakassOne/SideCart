<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Coupon operations through WooCommerce APIs.
 */
class Coupons {
	/** Apply a coupon code. */
	public function apply( $code ) {
		$code = wc_format_coupon_code( wp_unslash( $code ) );
		if ( '' === $code ) {
			return new \WP_Error( 'ssc_coupon_empty', __( 'Please enter a coupon code.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		if ( ! WC()->cart ) {
			return new \WP_Error( 'ssc_cart_unavailable', __( 'Cart is unavailable.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		if ( WC()->cart->has_discount( $code ) ) {
			return new \WP_Error( 'ssc_coupon_exists', __( 'This coupon is already applied.', 'shakass-side-cart-pro' ), array( 'status' => 409 ) );
		}

		$result = WC()->cart->apply_coupon( $code );
		WC()->cart->calculate_totals();

		if ( ! $result ) {
			return new \WP_Error( 'ssc_coupon_invalid', __( 'Coupon could not be applied.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		return true;
	}

	/** Remove a coupon code. */
	public function remove( $code ) {
		$code = wc_format_coupon_code( wp_unslash( $code ) );
		if ( '' === $code || ! WC()->cart ) {
			return new \WP_Error( 'ssc_coupon_missing', __( 'Coupon could not be removed.', 'shakass-side-cart-pro' ), array( 'status' => 400 ) );
		}

		WC()->cart->remove_coupon( $code );
		WC()->cart->calculate_totals();

		return true;
	}
}
