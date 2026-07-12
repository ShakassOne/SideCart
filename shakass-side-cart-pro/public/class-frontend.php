<?php
namespace Shakass\SideCartPro\Frontend;

use Shakass\SideCartPro\Assets;
use Shakass\SideCartPro\Settings;
use Shakass\SideCartPro\Template_Loader;

defined( 'ABSPATH' ) || exit;

/**
 * Frontend integration for rendering and assets.
 */
class Frontend {
	/** @var Settings */
	private $settings;

	/** @var Template_Loader */
	private $templates;

	/**
	 * Constructor.
	 *
	 * @param Settings        $settings  Settings service.
	 * @param Template_Loader $templates Template loader.
	 */
	public function __construct( Settings $settings, Template_Loader $templates ) {
		$this->settings  = $settings;
		$this->templates = $templates;
	}

	/**
	 * Register frontend hooks.
	 */
	public function init() {
		if ( ! $this->settings->get( 'enabled', true ) ) {
			return;
		}

		$assets = new Assets( $this->settings );
		add_action( 'wp_enqueue_scripts', array( $assets, 'enqueue_frontend' ) );
		add_action( 'wp_footer', array( $this, 'render' ) );
	}

	/**
	 * Render side cart shell and floating icon.
	 */
	public function render() {
		do_action( 'ssc_before_drawer' );
		$this->templates->render( 'drawer.php', array( 'settings' => $this->settings->all() ) );

		if ( $this->settings->get( 'floating_icon', true ) ) {
			$this->templates->render(
				'floating-cart.php',
				array(
					'count'  => function_exists( 'ssc_get_cart_count' ) ? ssc_get_cart_count() : 0,
					'total'  => function_exists( 'ssc_get_cart_total' ) ? ssc_get_cart_total() : '',
					'inline' => false,
				)
			);
		}

		do_action( 'ssc_after_drawer' );
	}
}
