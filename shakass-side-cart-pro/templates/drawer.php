<?php /** Template: Drawer. Version: 1.0.0-beta.2 */ defined( 'ABSPATH' ) || exit; ?>
<div id="ssc-root" class="ssc-root" data-ssc-root hidden>
	<div class="ssc-overlay" data-ssc-close aria-hidden="true"></div>
	<aside class="ssc-drawer" role="dialog" aria-modal="true" aria-labelledby="ssc-title" tabindex="-1">
		<?php do_action( 'ssc_before_header' ); ?><?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'header.php' ); ?><?php do_action( 'ssc_after_header' ); ?>
		<?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'notifications.php' ); ?>
		<?php if ( ! empty( $settings['rewards_enabled'] ) ) : ?>
			<?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'progress-bar.php' ); ?>
		<?php endif; ?>
		<?php if ( ! empty( $settings['coupons_enabled'] ) ) : ?>
			<?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'coupons.php' ); ?>
		<?php endif; ?>
		<main class="ssc-body" data-ssc-body><?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'cart-items.php' ); ?></main>
		<?php if ( ! empty( $settings['recommendations_enabled'] ) ) : ?>
			<?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'recommendations.php' ); ?>
		<?php endif; ?>
		<?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'totals.php' ); ?>
		<?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'express-payments.php' ); ?>
		<?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'footer.php' ); ?>
	</aside>
</div>
