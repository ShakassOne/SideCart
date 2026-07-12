<?php
namespace Shakass\SideCartPro\Admin;
defined( 'ABSPATH' ) || exit;
class Admin { public function init(){ add_action('admin_menu',array($this,'menu')); } public function menu(){ add_submenu_page('woocommerce',__('Shakass Side Cart','shakass-side-cart-pro'),__('Shakass Side Cart','shakass-side-cart-pro'),'manage_woocommerce','shakass-side-cart-pro',array($this,'page')); } public function page(){ echo '<div class="wrap"><h1>'.esc_html__('Shakass Side Cart Pro','shakass-side-cart-pro').'</h1><p>'.esc_html__('Phase 1 foundation is installed. Advanced settings arrive in Phase 2.','shakass-side-cart-pro').'</p></div>'; } }
