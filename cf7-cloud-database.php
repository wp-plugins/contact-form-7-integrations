<?php

/**
 * Plugin Name: Contact Form 7 Integrations
 * Plugin URI: http://www.contactus.com/
 * Description:Database, analytics and software integrations for Contact Form 7
 * Author: http://www.contactus.com
 * Author URI: http://www.contactus.com/
 * Version: 1.4.1
 * Stable tag: 1.4.1
 * License: GPLv2 or later
 * */
/*
  Copyright 2014  ContactUs.com  ( help.contacus.com )
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

//DEBUG MODE OFF
error_reporting(0);
error_reporting(E_ERROR);

// configuration directives
require_once(dirname(__FILE__) . '/config.php');

// file with the activation functions for hooks and helpers.
require_once(dirname(__FILE__) . '/helpers/cf7_cloud_helper.php');

// Ajax controllers
require_once(dirname(__FILE__) . '/controllers/ajax_response.php');

// require initialization file
require_once (dirname(__FILE__) . '/cf7_cloud_loader.php');

// require CU API
require_once (dirname(__FILE__) . '/includes/cusAPI.class.php');

// register the hooks
register_activation_hook(__FILE__, 'cf7_cloud_activate');
register_deactivation_hook(__FILE__, 'cf7_cloud_deactivate');

/**
 * Activate Initialization function, called in plugin file
 * */
function cf7_cloud_activate() {
    $loader = new CF7_cloud_loader();
    $loader->activate();
}

/**
 * Deactivate Initialization function, called in plugin file
 * */
function cf7_cloud_deactivate() {
    $loader = new CF7_cloud_loader();
    $loader->deactivate();
}

// -----------------------------> END CF7 Cloud Database plugin DEFINITIONS -------------------------------//