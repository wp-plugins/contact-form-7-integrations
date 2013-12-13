<?php
global $wpdb;

// define a list of constants used along.
define('CF7_CLOUD_PATH', realpath(dirname(__FILE__) ) );
define('CF7_CLOUD_PLUGIN_VERSION', '1.1' );
define('CF7_CLOUD_DATA', $wpdb->prefix.'cf7_cloud_data');

define('ADMIN_AJAX_URL',  get_admin_url());

// define the constant for the video to show in plugin Cloud Database
define('CU_VIDEO', '//www.youtube.com/embed/SEQJZqUT-Hk');

// below constants for testing purposes, change as will and are configured in cf7_cloud_loader.php

/*
define('CU_API_Account', 'BC6e93f6add475d91ba6e6c0ea51ff8f839545cb82');
define('CU_API_Key', 'd2f581d0423326195488eb91e6aba907d1e88719');
define('CU_Form_Key', 'YmE0Yzg3NWJlMQ,,');
*/

/* end configuration class */