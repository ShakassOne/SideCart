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
		$dynamic_css = ( new Style_Generator() )->generate( $this->settings );
		if ( '' !== $dynamic_css ) {
			wp_add_inline_style( 'ssc-frontend', $dynamic_css );
		}

		$modules = array(
			'ssc-api'        => array(),
			'ssc-state'      => array( 'ssc-api' ),
			'ssc-drawer'     => array( 'ssc-state' ),
			'ssc-cart-items' => array( 'ssc-drawer' ),
			'ssc-coupons'    => array( 'ssc-cart-items' ),
			'ssc-rewards'    => array( 'ssc-cart-items' ),
			'ssc-recommendations' => array( 'ssc-cart-items' ),
			'ssc-app'        => array( 'ssc-coupons', 'ssc-rewards', 'ssc-recommendations' ),
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
					'error'    => __( 'Impossible de mettre à jour le panier.', 'shakass-side-cart-pro' ),
					'subtotal' => __( 'Sous-total', 'shakass-side-cart-pro' ),
					'total'    => __( 'Total', 'shakass-side-cart-pro' ),
					'decrease' => __( 'Diminuer la quantité', 'shakass-side-cart-pro' ),
					'increase' => __( 'Augmenter la quantité', 'shakass-side-cart-pro' ),
					'remove'   => __( 'Retirer l’article', 'shakass-side-cart-pro' ),
					'couponPlaceholder' => __( 'Code promo', 'shakass-side-cart-pro' ),
					'applyCoupon' => __( 'Appliquer', 'shakass-side-cart-pro' ),
					'removeCoupon' => __( 'Retirer le code promo', 'shakass-side-cart-pro' ),
				),
			)
		);
	}
}
