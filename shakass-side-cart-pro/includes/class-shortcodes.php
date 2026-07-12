<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Public shortcodes and PHP rendering helpers.
 */
class Shortcodes {
	/** @var Cart_Service */
	private $cart;

	/** @var Template_Loader */
	private $templates;

	/** Constructor. */
	public function __construct( Cart_Service $cart, Template_Loader $templates ) {
		$this->cart      = $cart;
		$this->templates = $templates;
	}

	/** Register shortcodes. */
	public function init() {
		add_shortcode( 'ssc_cart_icon', array( $this, 'cart_icon' ) );
		add_shortcode( 'ssc_menu_cart', array( $this, 'menu_cart' ) );
		add_shortcode( 'ssc_cart_count', array( $this, 'cart_count' ) );
		add_shortcode( 'ssc_cart_total', array( $this, 'cart_total' ) );
		add_shortcode( 'ssc_open_cart', array( $this, 'open_cart' ) );
		add_shortcode( 'ssc_cart', array( $this, 'cart_icon' ) );
		add_filter( 'nav_menu_item_title', 'do_shortcode' );
	}

	/** Render cart icon shortcode. */
	public function cart_icon() {
		ob_start();
		$this->render_cart_icon();
		return ob_get_clean();
	}

	/** Echo cart icon helper output. */
	public function render_cart_icon() {
		ob_start();
		$this->templates->render(
			'floating-cart.php',
			array(
				'count'  => $this->cart->get_count(),
				'total'  => $this->cart->get_total_html(),
				'inline' => true,
			)
		);
		$html = ob_get_clean();

		echo apply_filters( 'ssc_cart_icon_html', $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template output is escaped and filter is developer-facing.
	}

	/** Cart count shortcode. */
	public function cart_count() {
		return esc_html( (string) $this->cart->get_count() );
	}

	/** Cart total shortcode. */
	public function cart_total() {
		return wp_kses_post( $this->cart->get_total_html() );
	}

	/** Menu-friendly cart trigger shortcode. */
	public function menu_cart( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'label' => __( 'Panier', 'shakass-side-cart-pro' ),
			),
			$atts,
			'ssc_menu_cart'
		);

		return sprintf(
			'<button type="button" class="ssc-menu-cart ssc-open-cart"><span class="ssc-menu-cart__label">%1$s</span> <span class="ssc-menu-cart__count" data-ssc-count>%2$s</span> <span class="ssc-menu-cart__total" data-ssc-total>%3$s</span></button>',
			esc_html( $atts['label'] ),
			esc_html( (string) $this->cart->get_count() ),
			wp_kses_post( $this->cart->get_total_html() )
		);
	}

	/** Ouvrir le panier button shortcode. */
	public function open_cart( $atts = array(), $content = '' ) {
		$label = '' !== $content ? $content : __( 'Ouvrir le panier', 'shakass-side-cart-pro' );
		return '<button type="button" class="ssc-open-cart">' . esc_html( $label ) . '</button>';
	}
}
