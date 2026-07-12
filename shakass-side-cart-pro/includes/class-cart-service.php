<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Cart_Service {
	private function cart() { return function_exists( 'WC' ) ? WC()->cart : null; }
	public function get_count() { $cart = $this->cart(); return $cart ? (int) $cart->get_cart_contents_count() : 0; }
	public function get_total_html() { $cart = $this->cart(); return $cart ? wp_kses_post( $cart->get_total() ) : ''; }
	public function get_cart_data() { $cart = $this->cart(); if ( ! $cart ) { return array( 'items'=>array(), 'count'=>0, 'totals'=>array(), 'total_html'=>'' ); } $cart->calculate_totals(); $items = array(); foreach ( $cart->get_cart() as $key => $item ) { $product = $item['data']; if ( ! $product || ! $product->exists() ) { continue; } $items[] = apply_filters( 'ssc_cart_item_data', array( 'key'=>$key,'name'=>$product->get_name(),'permalink'=>$product->is_visible() ? $product->get_permalink( $item ) : '','image'=>$product->get_image( 'woocommerce_thumbnail', array( 'loading'=>'lazy' ) ),'quantity'=>(int)$item['quantity'],'price'=>wc_price( wc_get_price_to_display( $product ) ),'line_total'=>wc_price( $item['line_total'] + $item['line_tax'] ),'min'=>0,'max'=>$product->get_max_purchase_quantity(),'sold_individually'=>$product->is_sold_individually() ), $item ); }
		return array( 'items'=>$items,'count'=>$this->get_count(),'total_html'=>$cart->get_total(),'subtotal_html'=>$cart->get_cart_subtotal(),'cart_url'=>wc_get_cart_url(),'checkout_url'=>apply_filters( 'ssc_checkout_url', wc_get_checkout_url() ) ); }
	public function set_quantity( $key, $quantity ) { $cart = $this->cart(); if ( ! $cart || ! is_string( $key ) ) { return new \WP_Error( 'ssc_cart_unavailable', __( 'Cart is unavailable.', 'shakass-side-cart-pro' ) ); } $quantity = wc_stock_amount( $quantity ); if ( $quantity < 0 ) { return new \WP_Error( 'ssc_invalid_quantity', __( 'Invalid quantity.', 'shakass-side-cart-pro' ) ); } $item = $cart->get_cart_item( $key ); if ( ! $item ) { return new \WP_Error( 'ssc_missing_item', __( 'Cart item not found.', 'shakass-side-cart-pro' ) ); } $cart->set_quantity( $key, $quantity, true ); return $this->get_cart_data(); }
	public function remove_item( $key ) { return $this->set_quantity( $key, 0 ); }
}
