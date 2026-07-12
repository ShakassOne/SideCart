<?php
/**
 * Template: Coupons.
 *
 * Version: 1.0.0-beta.1
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="ssc-section ssc-coupons" data-ssc-section="coupons">
	<form class="ssc-coupon-form" data-ssc-coupon-form>
		<label class="screen-reader-text" for="ssc-coupon-code"><?php esc_html_e( 'Coupon code', 'shakass-side-cart-pro' ); ?></label>
		<input id="ssc-coupon-code" name="ssc_coupon_code" type="text" autocomplete="off" placeholder="<?php esc_attr_e( 'Coupon code', 'shakass-side-cart-pro' ); ?>">
		<button type="submit"><?php esc_html_e( 'Apply', 'shakass-side-cart-pro' ); ?></button>
	</form>
	<div class="ssc-coupon-list" data-ssc-coupon-list></div>
</section>
