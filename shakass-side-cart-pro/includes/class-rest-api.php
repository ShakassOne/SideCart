<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Rest_API { private $cart; public function __construct( Cart_Service $cart ) { $this->cart = $cart; } public function init() { add_action( 'rest_api_init', array( $this, 'routes' ) ); }
	public function routes() { register_rest_route( 'ssc/v1', '/cart', array( array( 'methods'=>'GET','callback'=>array($this,'get_cart'),'permission_callback'=>'__return_true' ) ) ); register_rest_route( 'ssc/v1', '/cart/item', array( array( 'methods'=>'POST','callback'=>array($this,'update_item'),'permission_callback'=>array($this,'check_nonce') ), array( 'methods'=>'DELETE','callback'=>array($this,'delete_item'),'permission_callback'=>array($this,'check_nonce') ) ) ); }
	public function check_nonce( $request ) { return (bool) wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ); }
	public function get_cart() { return rest_ensure_response( $this->cart->get_cart_data() ); }
	public function update_item( $request ) { $result = $this->cart->set_quantity( sanitize_text_field( $request['key'] ), absint( $request['quantity'] ) ); return is_wp_error( $result ) ? $result : rest_ensure_response( $result ); }
	public function delete_item( $request ) { $result = $this->cart->remove_item( sanitize_text_field( $request['key'] ) ); return is_wp_error( $result ) ? $result : rest_ensure_response( $result ); }
}
