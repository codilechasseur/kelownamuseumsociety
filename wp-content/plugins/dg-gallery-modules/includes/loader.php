<?php

if ( ! class_exists( 'ET_Builder_Element' ) ) {
	return;
}
require_once( DGGM_MAIN_DIR . '/includes/utils/dggm_utls.php' );  

$module_files = glob( __DIR__ . '/modules/*/*.php' );

// Load custom Divi modules
foreach ( (array) $module_files as $module_file ) {
	if ( $module_file && preg_match( "/\/modules\/\b([^\/]+)\/\\1\.php$/", $module_file ) ) {
		require_once $module_file;
	}
}
