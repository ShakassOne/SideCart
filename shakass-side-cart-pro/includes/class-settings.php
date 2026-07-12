<?php
namespace Shakass\SideCartPro;
defined( 'ABSPATH' ) || exit;
class Settings { private $settings; public function all() { if ( null === $this->settings ) { $this->settings = wp_parse_args( (array) get_option( 'ssc_settings', array() ), Settings_Schema::defaults() ); } return apply_filters( 'ssc_drawer_settings', $this->settings ); } public function get( $key, $default = null ) { $all = $this->all(); return array_key_exists( $key, $all ) ? $all[ $key ] : $default; } }
