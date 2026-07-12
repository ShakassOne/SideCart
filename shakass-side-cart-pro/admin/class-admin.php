<?php
namespace Shakass\SideCartPro\Admin;

use Shakass\SideCartPro\Settings;
use Shakass\SideCartPro\Settings_Schema;

defined( 'ABSPATH' ) || exit;

/**
 * Minimal Phase 1 admin page under WooCommerce.
 */
class Admin {
	/** Register admin hooks. */
	public function init() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/** Register submenu. */
	public function menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Shakass Side Cart', 'shakass-side-cart-pro' ),
			__( 'Shakass Side Cart', 'shakass-side-cart-pro' ),
			'manage_woocommerce',
			'shakass-side-cart-pro',
			array( $this, 'page' )
		);
	}

	/** Register the versioned settings option. */
	public function register_settings() {
		register_setting(
			'ssc_settings_group',
			'ssc_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => Settings_Schema::defaults(),
			)
		);
	}

	/**
	 * Sanitize admin-posted settings and synchronize companion options.
	 *
	 * @param array<string,mixed> $input Raw option value.
	 * @return array<string,mixed>
	 */
	public function sanitize_settings( $input ) {
		$sanitized = Settings_Schema::sanitize( $input );
		update_option( 'ssc_settings_version', SSC_VERSION );
		update_option( 'ssc_delete_data_on_uninstall', $sanitized['delete_on_uninstall'] ? 'yes' : 'no' );

		return $sanitized;
	}

	/** Render admin page. */
	public function page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage this side cart.', 'shakass-side-cart-pro' ) );
		}

		$settings = new Settings();
		$values   = $settings->all();
		?>
		<div class="wrap ssc-admin">
			<h1><?php esc_html_e( 'Shakass Side Cart Pro', 'shakass-side-cart-pro' ); ?></h1>
			<p><?php esc_html_e( 'Phase 1 foundation settings. Advanced visual controls arrive in Phase 2.', 'shakass-side-cart-pro' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'ssc_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<?php $this->checkbox_row( 'enabled', __( 'Enable side cart', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'open_after_add', __( 'Open after add to cart', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'floating_icon', __( 'Show floating cart icon', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->text_row( 'width', __( 'Desktop width', 'shakass-side-cart-pro' ), $values, '420px' ); ?>
					<?php $this->text_row( 'tablet_width', __( 'Tablet width', 'shakass-side-cart-pro' ), $values, '70vw' ); ?>
					<?php $this->text_row( 'drawer_background', __( 'Drawer background', 'shakass-side-cart-pro' ), $values, '#ffffff' ); ?>
					<?php $this->text_row( 'text_color', __( 'Text color', 'shakass-side-cart-pro' ), $values, '#1f2937' ); ?>
					<?php $this->text_row( 'accent_color', __( 'Accent color', 'shakass-side-cart-pro' ), $values, '#f97316' ); ?>
					<?php $this->text_row( 'overlay_color', __( 'Overlay color', 'shakass-side-cart-pro' ), $values, 'rgba(15,23,42,0.48)' ); ?>
					<?php $this->text_row( 'drawer_radius', __( 'Drawer radius', 'shakass-side-cart-pro' ), $values, '0px' ); ?>
					<?php $this->number_row( 'free_shipping_threshold', __( 'Reward threshold', 'shakass-side-cart-pro' ), $values, 0, 1000000 ); ?>
					<?php $this->checkbox_row( 'coupons_enabled', __( 'Enable coupons block', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'rewards_enabled', __( 'Enable rewards progress', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'recommendations_enabled', __( 'Enable recommendations', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->number_row( 'quantity_debounce', __( 'Quantity debounce (ms)', 'shakass-side-cart-pro' ), $values, 0, 3000 ); ?>
					<?php $this->number_row( 'z_index', __( 'Z-index', 'shakass-side-cart-pro' ), $values, 1, 2147483647 ); ?>
					<?php $this->checkbox_row( 'delete_on_uninstall', __( 'Delete plugin data on uninstall', 'shakass-side-cart-pro' ), $values ); ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/** Render checkbox row. */
	private function checkbox_row( $key, $label, $values ) {
		?>
		<tr>
			<th scope="row"><?php echo esc_html( $label ); ?></th>
			<td><label><input type="checkbox" name="ssc_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $values[ $key ] ) ); ?>> <?php esc_html_e( 'Enabled', 'shakass-side-cart-pro' ); ?></label></td>
		</tr>
		<?php
	}

	/** Render text row. */
	private function text_row( $key, $label, $values, $placeholder ) {
		?>
		<tr>
			<th scope="row"><label for="ssc-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td><input id="ssc-<?php echo esc_attr( $key ); ?>" class="regular-text" type="text" name="ssc_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"></td>
		</tr>
		<?php
	}

	/** Render number row. */
	private function number_row( $key, $label, $values, $min, $max ) {
		?>
		<tr>
			<th scope="row"><label for="ssc-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td><input id="ssc-<?php echo esc_attr( $key ); ?>" type="number" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>" name="ssc_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( (string) ( $values[ $key ] ?? '' ) ); ?>"></td>
		</tr>
		<?php
	}
}
