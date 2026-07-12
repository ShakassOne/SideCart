<?php /** Template: Cart items. Version: 1.0.0-alpha.3 */ defined( 'ABSPATH' ) || exit; ?>
<?php do_action( 'ssc_before_cart_items' ); ?><div class="ssc-items" data-ssc-items></div><?php Shakass\SideCartPro\Plugin::instance()->templates()->render( 'empty-cart.php' ); ?><?php do_action( 'ssc_after_cart_items' ); ?>
