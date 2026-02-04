<?php

require_once (__DIR__ . '/config.php');
require_once (__DIR__ . '/functions.php');

add_filter('pre_set_site_transient_update_plugins', '\Dggm\Func\dggm_plugin_update');

// add_action( 'admin_notices', '\Dgdc\Func\dgcm_plugin_notice' );