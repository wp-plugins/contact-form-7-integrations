<?php
global $wpdb;

// define a list of constants used along.
define('CF7_CLOUD_PATH', realpath(dirname(__FILE__) ) );
define('CF7_CLOUD_PLUGIN_VERSION', '1.4.1' );
define('CF7_CLOUD_DATA', $wpdb->prefix.'cf7_cloud_data');

define('ADMIN_AJAX_URL',  get_admin_url());

// define the constant for the video to show in plugin Cloud Database
define('CU_VIDEO', '//www.youtube.com/embed/SEQJZqUT-Hk');

/* end configuration class */