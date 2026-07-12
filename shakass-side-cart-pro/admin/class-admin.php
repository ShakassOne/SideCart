<?php
namespace Shakass\SideCartPro\Admin;

use Shakass\SideCartPro\Settings;
use Shakass\SideCartPro\Settings_Schema;

defined( 'ABSPATH' ) || exit;

/**
 * Page d’administration principale du module.
 */
class Admin {
	/** Enregistrer les hooks d’administration. */
	public function init() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/** Enregistrer le menu principal. */
	public function menu() {
		add_menu_page(
			__( 'Shakass Side Cart', 'shakass-side-cart-pro' ),
			__( 'Shakass Side Cart', 'shakass-side-cart-pro' ),
			'manage_woocommerce',
			'shakass-side-cart-pro',
			array( $this, 'page' ),
			'dashicons-cart',
			56
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

	/** Afficher la page d’administration. */
	public function page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Vous n’avez pas les droits nécessaires pour gérer ce panier latéral.', 'shakass-side-cart-pro' ) );
		}

		$settings = new Settings();
		$values   = $settings->all();
		?>
		<div class="wrap ssc-admin">
			<h1><?php esc_html_e( 'Shakass Side Cart Pro', 'shakass-side-cart-pro' ); ?></h1>
			<p><?php esc_html_e( 'Réglages du module de panier latéral. Configurez l’affichage, le design et les fonctionnalités WooCommerce.', 'shakass-side-cart-pro' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'ssc_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<?php $this->checkbox_row( 'enabled', __( 'Activer le panier latéral', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'open_after_add', __( 'Ouvrir après l’ajout au panier', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'floating_icon', __( 'Afficher l’icône flottante du panier', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->text_row( 'width', __( 'Largeur ordinateur', 'shakass-side-cart-pro' ), $values, '420px' ); ?>
					<?php $this->text_row( 'tablet_width', __( 'Largeur tablette', 'shakass-side-cart-pro' ), $values, '70vw' ); ?>
					<?php $this->text_row( 'drawer_background', __( 'Arrière-plan du tiroir', 'shakass-side-cart-pro' ), $values, '#ffffff' ); ?>
					<?php $this->text_row( 'text_color', __( 'Couleur du texte', 'shakass-side-cart-pro' ), $values, '#1f2937' ); ?>
					<?php $this->text_row( 'accent_color', __( 'Couleur d’accent', 'shakass-side-cart-pro' ), $values, '#f97316' ); ?>
					<?php $this->text_row( 'overlay_color', __( 'Couleur du voile', 'shakass-side-cart-pro' ), $values, 'rgba(15,23,42,0.48)' ); ?>
					<?php $this->text_row( 'drawer_radius', __( 'Arrondi du tiroir', 'shakass-side-cart-pro' ), $values, '0px' ); ?>
					<?php $this->number_row( 'free_shipping_threshold', __( 'Seuil de récompense', 'shakass-side-cart-pro' ), $values, 0, 1000000 ); ?>
					<?php $this->checkbox_row( 'coupons_enabled', __( 'Activer le bloc codes promo', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'rewards_enabled', __( 'Activer la progression des récompenses', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->checkbox_row( 'recommendations_enabled', __( 'Activer les recommandations', 'shakass-side-cart-pro' ), $values ); ?>
					<?php $this->number_row( 'quantity_debounce', __( 'Délai de quantité (ms)', 'shakass-side-cart-pro' ), $values, 0, 3000 ); ?>
					<?php $this->number_row( 'z_index', __( 'Z-index', 'shakass-side-cart-pro' ), $values, 1, 2147483647 ); ?>
					<?php $this->checkbox_row( 'delete_on_uninstall', __( 'Supprimer les données du module à la désinstallation', 'shakass-side-cart-pro' ), $values ); ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/** Afficher une ligne de case à cocher. */
	private function checkbox_row( $key, $label, $values ) {
		?>
		<tr>
			<th scope="row"><?php echo esc_html( $label ); ?></th>
			<td><label><input type="hidden" name="ssc_settings[<?php echo esc_attr( $key ); ?>]" value="0"><input type="checkbox" name="ssc_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $values[ $key ] ) ); ?>> <?php esc_html_e( 'Activé', 'shakass-side-cart-pro' ); ?></label></td>
		</tr>
		<?php
	}

	/** Afficher une ligne de texte. */
	private function text_row( $key, $label, $values, $placeholder ) {
		?>
		<tr>
			<th scope="row"><label for="ssc-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td><input id="ssc-<?php echo esc_attr( $key ); ?>" class="regular-text" type="text" name="ssc_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"></td>
		</tr>
		<?php
	}

	/** Afficher une ligne numérique. */
	private function number_row( $key, $label, $values, $min, $max ) {
		?>
		<tr>
			<th scope="row"><label for="ssc-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td><input id="ssc-<?php echo esc_attr( $key ); ?>" type="number" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>" name="ssc_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( (string) ( $values[ $key ] ?? '' ) ); ?>"></td>
		</tr>
		<?php
	}
}
