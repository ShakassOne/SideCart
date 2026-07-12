<?php
namespace Shakass\SideCartPro;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin deactivation tasks.
 */
class Deactivator {
	/** Clear generated runtime caches only. */
	public static function deactivate() {
		delete_transient( 'ssc_compiled_css' );
	}
}
