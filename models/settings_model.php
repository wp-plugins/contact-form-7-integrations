<?php

/**
 * Handles settings processes.
 * Company : contactUs.com
 * Version : 0.1
 * */
//require_once(dirname(__FILE__).'/interfaces/icbc_interface.php');

class Settings {

    private $wpdb; // we can use the global wpdb, but why calling global wpdb in every method?, here just once.

    public function __construct($wpdb) {
        // initialize something here.
        $this->wpdb = $wpdb;
    }

    public function get_cf7_pages() {

        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );

        // get the list of pages
        $pages = get_pages($args);
        return $pages;
    }

}
