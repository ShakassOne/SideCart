<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Phase 1 REST endpoints for cart reads and item mutations.
 */
class Rest_API {
	/** @var Cart_Service */
	private $cart;

	/**
	 * Constructor.
	 *
	 * @param Cart_Service $cart Cart service.
	 */
	public function __construct( Cart_Service $cart ) {
		$this->cart = $cart;
	}

	/** Register REST hook. */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/** Register routes. */
	public function routes() {
		register_rest_route(
			'ssc/v1',
			'/cart',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_cart' ),
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			'ssc/v1',
			'/cart/item',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'check_nonce' ),
					'args'                => $this->item_args( true ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'check_nonce' ),
					'args'                => $this->item_args( false ),
				),
			)
		);


		register_rest_route(
			'ssc/v1',
			'/cart/coupon',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'apply_coupon' ),
					'permission_callback' => array( $this, 'check_nonce' ),
					'args'                => $this->coupon_args(),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'remove_coupon' ),
					'permission_callback' => array( $this, 'check_nonce' ),
					'args'                => $this->coupon_args(),
				),
			)
		);
	}

	/**
	 * Item endpoint argument schema.
	 *
	 * @param bool $with_quantity Include quantity arg.
	 * @return array<string,array<string,mixed>>
	 */
	private function item_args( $with_quantity ) {
		$args = array(
			'key' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);

		if ( $with_quantity ) {
			$args['quantity'] = array(
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => static function ( $value ) {
					return is_numeric( $value ) && (int) $value >= 0;
				},
			);
		}

		return $args;
	}

	/**
	 * Coupon endpoint argument schema.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private function coupon_args() {
		return array(
			'code' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Validate REST nonce for writes.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return bool|\WP_Error
	 */
	public function check_nonce( $request ) {
		if ( wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return true;
		}

		return new \WP_Error( 'ssc_nonce_expired', __( 'Your cart session expired. Please refresh and try again.', 'shakass-side-cart-pro' ), array( 'status' => 403 ) );
	}

	/** @return \WP_REST_Response */
	public function get_cart() {
		return rest_ensure_response( $this->cart->get_cart_data() );
	}

	/**
	 * Update quantity.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {
		$result = $this->cart->set_quantity( $request->get_param( 'key' ), $request->get_param( 'quantity' ) );
		return is_wp_error( $result ) ? $result : rest_ensure_response( $result );
	}

	/**
	 * Remove item.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_item( $request ) {
		$result = $this->cart->remove_item( $request->get_param( 'key' ) );
		return is_wp_error( $result ) ? $result : rest_ensure_response( $result );
	}

	/** Apply a coupon and return the refreshed cart. */
	public function apply_coupon( $request ) {
		$result = ( new Coupons() )->apply( $request->get_param( 'code' ) );
		return is_wp_error( $result ) ? $result : rest_ensure_response( $this->cart->get_cart_data() );
	}

	/** Remove a coupon and return the refreshed cart. */
	public function remove_coupon( $request ) {
		$result = ( new Coupons() )->remove( $request->get_param( 'code' ) );
		return is_wp_error( $result ) ? $result : rest_ensure_response( $this->cart->get_cart_data() );
	}
}
