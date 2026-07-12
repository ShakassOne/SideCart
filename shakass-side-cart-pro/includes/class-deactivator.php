<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Deactivator { public static function deactivate() { delete_transient( 'ssc_compiled_css' ); } }
