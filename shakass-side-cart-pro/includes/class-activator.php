<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Activator { public static function activate() { add_option( 'ssc_settings_version', SSC_VERSION ); add_option( 'ssc_settings', Settings_Schema::defaults() ); } }
