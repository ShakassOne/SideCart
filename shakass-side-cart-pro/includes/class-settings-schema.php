<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Settings_Schema { public static function defaults() { return array( 'enabled'=>true,'open_after_add'=>true,'floating_icon'=>true,'close_on_overlay'=>true,'close_on_escape'=>true,'position'=>'right','width'=>'420px','tablet_width'=>'70vw','mobile_fullscreen'=>true,'z_index'=>999999,'quantity_debounce'=>450,'generated_css'=>true ); } }
