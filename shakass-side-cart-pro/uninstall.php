<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
if ( 'yes' === get_option( 'ssc_delete_data_on_uninstall', 'no' ) ) { delete_option( 'ssc_settings' ); delete_option( 'ssc_settings_version' ); delete_option( 'ssc_delete_data_on_uninstall' ); }
delete_transient( 'ssc_compiled_css' );
