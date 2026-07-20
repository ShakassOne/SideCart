<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Phase 3 reward progress calculations.
 */
class Rewards {
	/**
	 * Get simple threshold progress data.
	 *
	 * @param Settings $settings Settings service.
	 * @return array<string,mixed>
	 */
	public function progress( Settings $settings ) {
		$threshold = (float) $settings->get( 'free_shipping_threshold', 0 );
		$subtotal  = WC()->cart ? (float) WC()->cart->get_subtotal() : 0.0;

		if ( $threshold <= 0 ) {
			return array( 'enabled' => false );
		}

		$remaining = max( 0, $threshold - $subtotal );
		$percent   = min( 100, ( $subtotal / $threshold ) * 100 );

		return array(
			'enabled'        => true,
			'threshold'      => $threshold,
			'subtotal'       => $subtotal,
			'remaining'      => $remaining,
			'percent'        => round( $percent, 2 ),
			'message'        => $remaining > 0 ? sprintf( __( 'Ajoutez encore %s pour profiter de la livraison gratuite.', 'shakass-side-cart-pro' ), wc_price( $remaining ) ) : __( 'Vous profitez de la livraison gratuite !', 'shakass-side-cart-pro' ),
			'unlocked'       => 0.0 === $remaining,
		);
	}
}
