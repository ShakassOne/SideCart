<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Frontend asset registration and localization.
 */
class Assets {
	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings service.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register and enqueue the Phase 1 frontend assets.
	 */
	public function enqueue_frontend() {
		if ( ! $this->settings->get( 'enabled', true ) ) {
			return;
		}

		wp_enqueue_style( 'ssc-frontend', SSC_PLUGIN_URL . 'public/assets/css/frontend.css', array(), SSC_VERSION );

		$modules = array(
			'ssc-api'        => array(),
			'ssc-state'      => array( 'ssc-api' ),
			'ssc-drawer'     => array( 'ssc-state' ),
			'ssc-cart-items' => array( 'ssc-drawer' ),
			'ssc-app'        => array( 'ssc-cart-items' ),
		);

		foreach ( $modules as $handle => $dependencies ) {
			$file = str_replace( 'ssc-', '', $handle );
			wp_enqueue_script( $handle, SSC_PLUGIN_URL . 'public/assets/js/' . $file . '.js', $dependencies, SSC_VERSION, true );
		}

		wp_localize_script(
			'ssc-api',
			'sscConfig',
			array(
				'restUrl'      => esc_url_raw( rest_url( 'ssc/v1/' ) ),
				'nonce'        => wp_create_nonce( 'wp_rest' ),
				'openAfterAdd' => (bool) $this->settings->get( 'open_after_add', true ),
				'debounce'     => (int) $this->settings->get( 'quantity_debounce', 450 ),
				'i18n'         => array(
					'error'    => __( 'Unable to update the cart.', 'shakass-side-cart-pro' ),
					'subtotal' => __( 'Subtotal', 'shakass-side-cart-pro' ),
					'total'    => __( 'Total', 'shakass-side-cart-pro' ),
					'decrease' => __( 'Decrease quantity', 'shakass-side-cart-pro' ),
					'increase' => __( 'Increase quantity', 'shakass-side-cart-pro' ),
					'remove'   => __( 'Remove item', 'shakass-side-cart-pro' ),
				),
			)
		);
	}
}
