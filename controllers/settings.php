<?php

// SETTINGS CONTROLLER
global $wpdb;

require_once(CF7_CLOUD_PATH . "/models/settings_model.php");

$settings = new Settings($wpdb);

// list of services
$services = (object) array('mailchimp' => 'MailChimp', 'aweber' => 'aWeber', 'constantcontact' => 'Constant Contact');
$lists = (object) array('1' => 'list one', '2' => 'List two', '3' => 'List three');

$pages = $settings->get_cf7_pages();
$data['pages'] = $pages;
$data['services'] = $services;

// call the view and insert controller data
include_once(CF7_CLOUD_PATH . '/views/settings_view.php');

/*   end controller - settings_view.php */