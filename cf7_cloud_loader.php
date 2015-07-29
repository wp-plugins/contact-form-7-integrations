<?php
/**
 * Initialization Class for CF7 Integrations 
 * Company 		: ContactUs.com
 * Programmer	: ContactUs.com
 * Updated  	: 20142407
 * */
require_once(dirname(__FILE__) . '/models/interfaces/icf7_cloud_interface.php');
require_once(dirname(__FILE__) . '/includes/class-tgm-plugin-activation.php');
require_once(dirname(__FILE__) . '/includes/cusAPI.class.php');

class CF7_cloud_loader extends CF7_cloud_interface {

    // Don't change this private values unless you know what you are doing
    private $cf7_cloud_db_version = '1.4.1'; // cf7 cloud current DB version.
    private $cf7_cloud_version = '1.4.1';
    private $API_url = 'https://api.contactus.com/api2.php?';

    /*
      'First_Name'				=> 	'First Name',
      'Last_Name'				=> 	'Last Name',
      'Full_Name'				=> 	'Full Name',
     */
    // create here the list of possible fields for contactUs.com API calls
    private $CU_API_fields = array(
        'Message' => 'Message',
        'IP_Address' => 'IP address',
        'Company_Name' => 'Company Name',
        'Primary_Phone' => 'Primary Phone',
        'Secondary_Phone' => 'Secondary Phone',
        'Address' => 'Address',
        'Address2' => 'Address 2',
        'City' => 'City',
        'State' => 'State',
        'Zip' => 'Zip',
        'Country' => 'Country',
        'Best_Time_To_Contact' => 'Best time to contact',
        'Relationship' => 'Relationship',
        'Landing_Page' => 'Landing Page',
        'HTTP_Referer' => 'HTTP Referer',
        'HTTP_User_Agent' => 'HTTP User Agent',
        'UTM_Content' => 'UTM Content',
        'UTM_Source' => 'UTM Source',
        'UTM_Medium' => 'UTM Medium',
        'UTM_Campaign' => 'UTM Campaign',
        'UTM_Term' => 'UTM Term',
        'Generic_Field_1' => 'Generic Field 1',
        'Generic_Field_2' => 'Generic Field 2',
        'Generic_Field_3' => 'Generic Field 3',
        'Generic_Field_4' => 'Generic Field 4',
        'Generic_Field_5' => 'Generic Field 5',
        'Generic_Field_6' => 'Generic Field 6',
        'Generic_Field_7' => 'Generic Field 7',
        'Generic_Field_8' => 'Generic Field 8',
        'Generic_Field_9' => 'Generic Field 9',
        'Generic_Field_10' => 'Generic Field 10'
    );

    // just the constructor for the action settings
    public function __construct() {

        // initialize something here :)
        add_action('admin_menu', array(&$this, 'cf7_cloud_database_menu'));

        // contact form 7 hooks/actions binding
        add_action("wpcf7_before_send_mail", array(&$this, 'wpcf7_cloud_send_all'));
        add_action("wpcf7_admin_after_mail", array(&$this, 'show_cf7cloud_metabox'));

        $cf7_cloud_activated = get_option('cf7_cloud_database_active');

        add_action('wpcf7_after_save', array(&$this, 'cf7cloud_save_form'));

        // if user already signed/logged to ContactUs then show CF7 extension.
        if ($cf7_cloud_activated == 1) {
            //add_action('wpcf7_admin_notices', array(&$this, 'add_cf7cloud_meta'));
            add_filter( 'wpcf7_editor_panels', array( &$this, 'cf7cloud_editor_panels') );
        }

        add_filter("plugin_action_links", array(&$this, 'cf7cloud_plugin_action_links'), 10, 4);
        add_filter("plugin_row_meta", array(&$this, 'cf7cloud_plugin_links'), 10, 2);

        add_action('admin_print_scripts', array(&$this, 'Load_scripts'));
        add_action('admin_print_scripts', array(&$this, 'Load_styles'));

        //add_action( 'tgmpa_register', array(&$this, 'my_plugin_register_required_plugins' ));
    }
    
    /**
	 * Add panels in Contact Form 7 4.2+
	 *
	 * @since 2.1
	 *
	 * @param array $panels registered tabs in Form Editor
	 *
	 * @return array tabs with CTCTCF7 tab added
	 */
	function cf7cloud_editor_panels( $panels = array() ) {

		if ( wpcf7_admin_has_edit_cap() ) {
			$panels['cf7cloud'] = array(
				'title'    => __( 'CF7 Integrations', 'cf7-integrations' ),
				'callback' => array( &$this, 'wpcf7_cf7cloud_add_contactus_analytics' )
			);
		}

		return $panels;
	}
        
        

    function my_plugin_register_required_plugins() {

        /**
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = array(
            /*
              // This is an example of how to include a plugin pre-packaged with a theme
              array(
              'name'     				=> 'TGM Example Plugin', // The plugin name
              'slug'     				=> 'tgm-example-plugin', // The plugin slug (typically the folder name)
              'source'   				=> get_stylesheet_directory() . '/lib/plugins/tgm-example-plugin.zip', // The plugin source
              'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
              'version' 				=> '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
              'force_activation' 		=> true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
              'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
              'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
              ), */

            // This is an example of how to include a plugin from the WordPress Plugin Repository
            array(
                'name' => 'Contact Form 7',
                'slug' => 'Contact-Form-7',
                'force_activation' => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                'required' => true,
            ),
        );

        // Change this to your theme text domain, used for internationalising strings
        $theme_text_domain = 'cf7-integrations';

        /**
         * Array of configuration settings. Amend each line as needed.
         * If you want the default strings to be available under your own theme domain,
         * leave the strings uncommented.
         * Some of the strings are added into a sprintf, so see the comments at the
         * end of each line for what each argument will be.
         */
        $config = array(
            'domain' => $theme_text_domain, // Text domain - likely want to be the same as your theme.
            'default_path' => '', // Default absolute path to pre-packaged plugins
            'parent_menu_slug' => 'plugins.php', // Default parent menu slug
            'parent_url_slug' => 'plugins.php', // Default parent URL slug
            'menu' => 'install-required-plugins', // Menu slug
            'has_notices' => true, // Show admin notices or not
            'is_automatic' => true, // Automatically activate plugins after installation or not
            'message' => '', // Message to output right before the plugins table
            'strings' => array(
                'page_title' => __('Install Required Plugins', $theme_text_domain),
                'menu_title' => __('Install Plugins', $theme_text_domain),
                'installing' => __('Installing Plugin: %s', $theme_text_domain), // %1$s = plugin name
                'oops' => __('Something went wrong with the plugin API.', $theme_text_domain),
                'notice_can_install_required' => _n_noop('The Contact Form 7 Integrations plugin requires %1$s plugin.  If you already have Contact Form 7 installed, please dismiss this notice.', 'This Contact Form 7 Integrations plugin requires %1$s plugin.  If you already have Contact Form 7 installed, please dismiss this notice.'), // %1$s = plugin name(s)
                'notice_can_install_recommended' => _n_noop('This plugin recommends the following plugin: %1$s.', 'This plugin recommends the following plugin: %1$s.'), // %1$s = plugin name(s)
                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.'), // %1$s = plugin name(s)
                'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.'), // %1$s = plugin name(s)
                'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.'), // %1$s = plugin name(s)
                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.'), // %1$s = plugin name(s)
                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.'), // %1$s = plugin name(s)
                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.'), // %1$s = plugin name(s)
                'install_link' => _n_noop('Begin installing Contact Form 7', 'Begin installing plugins'),
                'activate_link' => _n_noop('Activate installed plugin', 'Activate installed plugins'),
                'return' => __('Return to Required Plugins Installer', $theme_text_domain),
                'plugin_activated' => __('Plugin activated successfully.', $theme_text_domain),
                'complete' => __('All plugins installed and activated successfully. %s', $theme_text_domain), // %1$s = dashboard link
                'nag_type' => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
            )
        );

        tgmpa($plugins, $config);
    }

    /**
     * This should create the setting button in plugin CF7 cloud database
     * */
    function cf7cloud_plugin_action_links($links, $file) {
        $plugin_file = 'contact-form-7-integrations/cf7-cloud-database.php';
        //make sure it is our plugin we are modifying
        if ($file == $plugin_file) {
            $settings_link = '<a href="' .
                    admin_url('admin.php?page=cf7-integrations') . '">' .
                    __('Settings', 'contact-form-7-integrations') . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    // *********************
    // create the support link in plugins
    function cf7cloud_plugin_links($links, $file) {
        $plugin_file = 'contact-form-7-integrations/cf7-cloud-database.php';
        if ($file == $plugin_file) {
            $links[] = '<a target="_blank" style="color: #42a851; font-weight: bold;" href="http://help.contactus.com/">' . __("Get Support", "cus_plugin") . '</a>';
        }
        return $links;
    }

    /**
     * Private method to create the required options in database
     * @params none
     * @return none
     * @since 0.1
     * */
    private function create_cf7_cloud_options() {
        // set options to be used along the system
        update_option('cf7_cloud_db_version', $this->cf7_cloud_db_version);
        update_option('cf7_cloud_version', $this->cf7_cloud_version);
        update_option('cf7_cloud_database_active', 0); // this is to know if user has signup/login to CU API system
    }

    /**
     * Method en charge to create DB tables and version control options
     * @params none
     * @return none
     * @since 0.1
     * */
    public function activate() {
        // Perform any databases modifications related to plugin activation here, if necessary
        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;

        /*         * ****************************** START PLUGIN SQL ************************************ */
        /* $sql= "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."cf7_cloud_database` (
          `field_id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
          `field_name` varchar(100) NOT NULL,
          PRIMARY KEY (`id_field`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
          dbDelta($sql);

          $sql = "INSERT INTO `".$wpdb->prefix."cf7_cloud_database` (`field_id`, `field_name`) VALUES
          (1, 'Message'),
          (1, 'First'),
          ";
          dbDelta($sql); */
        /*         * ****************************** END PLUGIN SQL ************************************ */

        // create plugin in options table.
        $this->create_cf7_cloud_options();
    }

    /**
     * Method to deactive the plugin, we will not delete DB tables nor reset options.
     * @params none;
     * @return none;
     * @since 0.1
     */
    public function deactivate() {

        //$the_data = get_option('CU_cf7cloud_database_data_'.$_GET['post']);
        delete_option('cUsCloud_settings_userCredentials');
        delete_option('cUsCloud_settings_form_key');
        delete_option('cf7_cloud_database_active');
        delete_option('cUsCloud_settings_userData');
        //delete_option('cUsCloud_FORM_settings');
        delete_option('cUsCloud_settings_form_keys');
        delete_option('cf7_cloud_db_version');
        delete_option('cf7_cloud_version');

        delete_option('cf7_cloud_db_version');
        delete_option('cf7_cloud_version');
        delete_option('cf7_cloud_database_active');  // this is to know if user has signup/login to the system
        // delete dependant plugins flag when deactivate so it is shown again on activate
        delete_user_meta(get_current_user_id(), 'tgmpa_dismissed_notice');
    }

    /*
     * create main menu and its options for CF7 Extension
     * @params none
     * @since 0.1
     * @return html that conforms the menus for the sidebar
     */

    public function cf7_cloud_database_menu() {
        if (current_user_can('level_10')) {
            add_menu_page('CF7 Integrations', 'CF7 Integrations', 0, 'cf7-integrations', array($this, 'cf7_cloud_settings'), WP_PLUGIN_URL . '/contact-form-7-integrations/assets/images/favicon.gif');
        }
    }

    /*     * ************************************ */
    /* THE REST OF PLUGIN RELATED METHODS */

    public function Load_scripts() {

        global $current_screen; // check we are in our CF7 integrations plugin page
        if ($current_screen->id == 'toplevel_page_cf7-integrations' || $current_screen->id == 'toplevel_page_wpcf7') {

            wp_register_script('my-scripts', WP_PLUGIN_URL . '/contact-form-7-integrations/assets/js/scripts.js');

            wp_enqueue_style('colorbox', plugins_url('includes/colorbox/colorbox.css', __FILE__), false, '1');
            wp_enqueue_style('other_info_styles', plugins_url('assets/css/styles2.css', __FILE__), false, '1');
            wp_enqueue_style('thickbox');

            wp_register_script('other_info_scripts', plugins_url('assets/js/main.js?pluginurl=' . dirname(__FILE__), __FILE__), array('jquery'), '1.0', true);
            wp_register_script('colorbox', plugins_url('includes/colorbox/jquery.colorbox-min.js', __FILE__), array('jquery'), '1.4.1.33', true);

            wp_enqueue_script('my-scripts');
            wp_enqueue_script('other_info_scripts');
            wp_enqueue_script('colorbox');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('CU_chat', '//cdn.contactus.com/cdn/forms/MDBmMjhjMTNiZDE,/contactus.js');
        }
    }

    /**
     * Method in charge to load plugin specific styles
     * @since version 1
     * @params none
     * @return none 
     * */
    public function Load_styles() {
        global $current_screen; // check we are in our CF7 integrations plugin page
        if ($current_screen->id == 'toplevel_page_cf7-integrations' || $current_screen->id == 'toplevel_page_wpcf7') {
            wp_enqueue_style('cf7_cloud-styles', WP_PLUGIN_URL . '/contact-form-7-integrations/assets/css/styles.css');
        }
    }

    /*
     * display admin page to manage requisitions
     * @params none
     * @since 0.1
     */

    public function cf7_cloud_settings() {
        require_once('controllers/settings.php');
    }

    /**
     * This is the method in charge to create the metabox for integration with Contact Form 7
     * @params none
     * @since 0.1 
     * return null
     * DEPRECATED SINCE 1.4.1
     * */
    public function add_cf7cloud_meta() {

        global $wpcf7;

        if (wpcf7_admin_has_edit_cap()) {

            add_meta_box('cf7cf7clouddiv', __('Contact Form 7 Integrations by ContactUs.com', 'wpcf7'), array($this, 'wpcf7_cf7cloud_add_contactus_analytics'), 'cf7clouddatabase', 'cf7_cf7cloud', 'core', array(
                'id' => 'wpcf7-cf7-integrations',
                'name' => 'cf7_cf7cloud',
                'use' => __('Turn On Contact Form 7 Integrations', 'wpcf7')));
        }
    }

    public function show_cf7cloud_metabox($cf) {
        do_meta_boxes('cf7clouddatabase', 'cf7_cf7cloud', $cf);
    }

    public function wpcf7_cf7cloud_add_contactus_analytics($args) {
        $cUsComAPI_Cloud = new cUsComAPI_Cloud();
        ?>
        <script>
            //<![CDATA[
            jQuery(document).ready(function() {

                jQuery('#wpcf7-cf7cloud-active').on('click', function() {

                    if (jQuery('#wpcf7-cf7cloud-active').is(':checked')) {
                        //jQuery('#wpcf7-admin-form-element').submit();
                        jQuery('#cf7cloud-formdata').show('fast');
                    } else {
                        jQuery('#cf7cloud-formdata').hide('fast');
                    }
                });

                // function to validate email address
                function validate_email(email) {
                    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return re.test(email);
                }

            });

            //]]>
        </script>

        <?php
        // get the custom data for this contact form
        $the_data = get_option('CU_cf7cloud_database_data_' . $_GET['post']);
        $the_cf7_fields = get_option('CU_cf7_cloud_mapped_fields_' . $_GET['post']);
        $is_active_form = get_option('CU_cf7cloud_database_form_' . $_GET['post'] . '_active');

        //print_r($the_data);

        $cred = get_option('cUsCloud_settings_userCredentials');
        $cf7_cloud_activated = get_option('cf7_cloud_database_active');
        $fkey = get_option('cUsCloud_settings_form_key');
        //echo $fkey;
        ?>

        <input type="hidden" name="trcount" id="trcount" value="<?php echo (is_array($the_data['customs']) ? count($the_data['customs']) : 1 ); ?>" />
        <div class="mail-field">

            <div class="cf7cloud-active">
                <input type="checkbox" id="wpcf7-cf7cloud-active" name="wpcf7-cf7cloud-active" value="1" <?php echo ( $is_active_form ) ? "checked" : ""; ?> />

                <label for="wpcf7-cf7cloud-active"><?php echo esc_html(__('Turn On Contact Form 7 Integrations', 'wpcf7')); ?></label>
                <a name="cf7cloud_errors"></a>
                <?php
                // CF7 cloud errors in fields
                if (isset($_GET['cf7cloud_errors'])) {
                    echo('<div class="cf7_cloud_errors">' . $_GET['cf7cloud_errors'] . '</div>');
                }
                ?>
            </div>

            <div id="cf7cloud-formdata" <?php echo ($is_active_form) ? 'style="display:block"' : ""; ?>>
                <input type="submit" name="map_button" id="map_button" value="Map Contact Form 7 Fields" style="padding:5px 10px 5px; cursor:pointer" /> 
                <br/><strong>Click here before mapping or editing your mapped fields (Required).</strong>
                <hr/>
                <table id="cf7_cloud_table" <?php echo ($is_active_form) ? 'style="display:block"' : 'style="display:none"'; ?>>
                    <tbody>
                        <tr><td colspan="2"><h4>To integrate your ContactUs.com account with your form, you must map fields by matching Contact Form 7 fields with ContactUs.com form fields. We have set the default form fields, but please make sure they are correct.</h4></td></tr>
                        <tr>
                            <td>Input name for EMAIL field:</td>
                            <td>
                                <select name="cf7cloud_email">
                                    <?php
                                    // list the CF7 fields names
                                    foreach ($the_cf7_fields as $key => $value) {
                                        if ($the_data['Email'] == $value)
                                            echo('<option value="' . $value . '" selected="selected">' . $value . '</option>');
                                        else {

                                            // check if email name of CF7 is the default, your email
                                            if ($value == 'your-email')
                                                echo('<option value="' . $value . '" selected="selected">' . $value . '</option>');
                                            else
                                                echo('<option value="' . $value . '">' . $value . '</option>');
                                        }
                                    }
                                    ?>
                                </select>

                            </td>
                        </tr>

                        <tr>
                            <td>Select input name for NAME field:</td>
                            <td>
                                <select name="cf7cloud_name">
                                    <?php
                                    // list the CF7 fields names
                                    foreach ($the_cf7_fields as $key => $value) {
                                        if ($the_data['Full_Name'] == $value)
                                            echo('<option value="' . $value . '" selected="selected">' . $value . '</option>');
                                        else {

                                            // check if email name of CF7 is the default, your name
                                            if ($value == 'your-name')
                                                echo('<option value="' . $value . '" selected="selected">' . $value . '</option>');
                                            else
                                                echo('<option value="' . $value . '">' . $value . '</option>');
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"><br />&nbsp;</h2>
                            </td>
                        </tr>
                        <?php
                        // check if custom forms fields available to show or not
                        $count_mapper = 0; // variable to compare how many custom fields are being displayed already and not allow more than $total-2

                        if (isset($the_data['customs']) && is_array($the_data['customs'])) {
                            $counter = 1; // counter to create row ids
                            foreach ($the_data['customs'] as $key => $value) {

                                // check and compare amount of fields with mapper
                                if ((count($the_cf7_fields) - 2) == $count_mapper) {
                                    break;
                                }
                                ?>
                                <tr id="row_<?php echo $counter; ?>">
                                    <td>
                                        Select Contact Form 7 field:<br />
                                        <select name="cf7cloud_custom_field_name[]">
                                            <?php
                                            // list the CF7 fields names
                                            foreach ($the_cf7_fields as $xkey => $xvalue) {
                                                if ($xvalue == $key)
                                                    echo('<option value="' . $xvalue . '" selected="selected">' . $xvalue . '</option>');
                                                else {
                                                    // avoid including here CF7 defaults, your-name and your-mail
                                                    if ($xvalue != 'your-name' && $xvalue != 'your-email')
                                                        if ($value == 'your-subject') {
                                                            echo('<option value="' . $xvalue . '">' . $xvalue . '</option>');
                                                            $subject_present = TRUE;
                                                        } elseif ($value == 'your-message') {
                                                            echo('<option value="' . $xvalue . '">' . $xvalue . '</option>');
                                                            $message_present = TRUE;
                                                        } else
                                                            echo('<option value="' . $xvalue . '">' . $xvalue . '</option>');
                                                }
                                            }
                                            ?>
                                        </select>

                                    </td>
                                    <td>
                                        Select ContactUs.com field to associate:<br />
                                        <?php
                                        //print_r($this->CU_API_fields); exit;
                                        ?>

                                        <select name="cf7cloud_custom_field_select[]">
                                            <option value="unmapped">-- Unmapped --</option>
                                            <?php
                                            // list and select current select value
                                            foreach ($this->CU_API_fields as $skey => $svalue) {
                                                if ($value == $skey) {
                                                    echo('<option value="' . $skey . '" selected="selected">' . $svalue . '</option>');
                                                } else {

                                                    // check if subject_present gives TRUE to select Generic_Field_10
                                                    if ($subject_present == TRUE && $skey == 'Generic_Field_10') {
                                                        echo('<option value="' . $skey . '" selected="selected">' . $svalue . '</option>');
                                                        $subject_present = FALSE;
                                                    } elseif ($message_present == TRUE && $skey == 'Message') {
                                                        echo('<option value="' . $skey . '" selected="selected">' . $svalue . '</option>');
                                                        $message_present = FALSE;
                                                    } else
                                                        echo('<option value="' . $skey . '">' . $svalue . '</option>');
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>&nbsp;
                                    </td>
                                </tr>

                                <?php
                                $counter++; // increment the counter for row identification
                                $count_mapper++;
                            } // end foreach
                        }
                        ?>
                        <tr>
                            <td colspan="2">

                            </td>
                        </tr>
                        <tr><td colspan="2" style="text-align:center;">&nbsp;</td></tr>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                              <!-- <input type="submit" name="save_cf7cloud" id="save_cf7cloud" value="Save Analytics Data" /><br /><br /> -->
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="cf7cloud_custom_fields_table" <?php echo ($is_active_form) ? 'style="display:block"' : 'style="display:none"'; ?>>

                    <?php
                    // get api account and api key
                    $data = get_option('cUsCloud_settings_userCredentials');
                    $credentials = get_option('cUsCloud_settings_userCredentials');
                    $cUs_API_Account = $credentials['API_Account'];
                    $cUs_API_Key = $credentials['API_Key'];
                    $cus_par_url = 'https://admin.contactus.com/partners';
                    $default_deep_link = get_option('cUsCloud_settings_default_deep_link_view');
                    $partnerID = $cUsComAPI_Cloud->get_partner_id($default_deep_link);
                    $cus_CRED_url = $cus_par_url . '/index.php?loginName=' . $cUs_API_Account . '&userPsswd=' . urlencode($cUs_API_Key);
                    
                    
                    ?>

                    <tbody>
                        <tr>
                            <td colspan="3">When you’re finish mapping, remember to hit <strong>Save</strong> your Contact Form 7 settings! (Save button is located on upper right)<br /><br /></td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>What’s Next?</strong>
                                <p>Once saved, your ContactUs.com account is now connected with your Contact Form 7 form. Visit your ContactUs.com admin panel to:   </p>
                                <ul>	
                                    <li><a href="<?php echo $cus_CRED_url; ?>&confirmed=1" target="_blank" rel="toDash" class="deep_link_action btn action_orange_button_2">View Your Stats</a></li>
                                </ul>			
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
            <!-- insert video tutorial -->
                <div id="cf7cloud_video">
                    

                    <?php if (strlen($credentials['API_Key']) && strlen($credentials['API_Account'])) { ?>

                        <p>
                            <a href="<?php echo $cus_CRED_url; ?>&confirmed=1" target="_blank" rel="toDash" class="action_orange_button_2 btn">Form Control Panel</a>
                            <a href="<?php echo $cus_CRED_url; ?>&confirmed=1&redir_url=<?php echo urlencode(trim($default_deep_link)); ?>%26expand=103" target="_blank" rel="toDash" class="action_orange_button_2 btn">Software Integrations</a>
                        </p>
                        <hr/>
                        <br/>
                    <?php } ?>  

                    <div class="cf7integrations_support">
                        <h2>Contact Form 7 Integrations Support</h2>
                        <ul>
                            <li><a href="http://help.contactus.com/hc/en-us/articles/200918046-Installing-the-CF7-Integrations-Plugin" target="_blank">Installing the CF7 Integrations Plugin</a></li>
                            <li><a href="http://help.contactus.com/hc/en-us/articles/200919166-Setting-up-your-Contact-Form-7-Integrations-Plugin" target="_blank">Setting up your "Contact Form 7 Integrations" Plugin</a></li>
                            <li><a href="http://help.contactus.com/hc/en-us/articles/201083933-Creating-a-POST-form-type" target="_blank">Creating a POST form type</a></li>
                            <li><a href="http://help.contactus.com/hc/en-us/articles/200927346-Integrating-your-Contact-Form-7-with-Third-party-applications" target="_blank">Integrating your Contact Form 7 with Third party applications</a></li>
                            <li><a href="http://help.contactus.com/hc/en-us/requests/new" target="_blank"><strong>Submit support ticket</strong></a></li>
                        </ul>
                        
                        <hr/>
                        
                        <h2>Step by Step Instructions</h2>
                        <iframe src="//www.youtube.com/embed/videoseries?list=PL0S7fxBYpaTEB-GJtkE0lgDe0XHhzEXrG" width="100%" height="220" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        
                    </div>          

                </div>
                <!-- / insert video tutorial -->
        </div>
        <br class="clear" />

        <?php
    }

    /*
     * Method in charge to get the inputs from the CF7 textarea
     * @params string
     * @since 0.1
     * @returns Array
     */

    private function _get_cf7_inputs($cf7_form) {

        $cf7_shortcodes = preg_match_all('#\[[text|select|checkbox|radio|tel|email|url|number|textarea]\s*.*?\]#s', $cf7_form, $matches);
        $the_values = Array();

        // loop the fields found in CF7 textarea
        foreach ($matches[0] as $key => $value) {
            $the_values[] = explode(" ", $value);
            $the_names[$key] = str_replace(']', '', $the_values[$key][1]);
        }

        // delete the submit button of the end, TODO: hope always is in the end xD otherwise we must change this procedure.
        array_pop($the_names);
        return $the_names;
    }

    /*
     * Method in charge to save the relationships between contact form 7 and CU cloud Database
     * @params Array all the actual editing form data being submitted
     * @since 0.1
     * @returns Null
     */

    public function cf7cloud_save_form($args) {

        // create an option for the custom_cf7_fields that come
        $cf7_customs = $this->_get_cf7_inputs($args->form);

        $prev_url = $_SERVER["HTTP_REFERER"];

        $error_main = 'The Email and Name cannot have the same CF7  values. Please change one and try again.';
        $error_customs = 'The following CF7 fields were detected duplicates in your selects: ';
        $error_CUapi = 'The following ContactUs.com fields were detected as duplicates in your selection: ';
        $string_error = '';

        if ((int) $_POST['post_ID']) {

            // save if this form is active as an option
            update_option('CU_cf7cloud_database_form_' . $_POST['post_ID'] . '_active', 1);

            $the_data['Full_Name'] = esc_sql($_POST['cf7cloud_name']);
            $the_data['Email'] = esc_sql($_POST['cf7cloud_email']);

            // *********************************
            // check email and name dont have equal select values
            if (trim($_POST['cf7cloud_name']) != '' && trim($_POST['cf7cloud_email']) != '') {
                if ((string) $_POST['cf7cloud_name'] == (string) $_POST['cf7cloud_email']) {
                    header('Location:' . $prev_url . '&cf7cloud_errors=' . urlencode($error_main) . '#cf7cloud_errors');
                    exit;
                }
            }

            // check if any of the above fields is already selected in custom fields. This is when user changes default field names for name and email.
            if (isset($_POST['cf7cloud_custom_field_name']) && in_array($_POST['cf7cloud_name'], $_POST['cf7cloud_custom_field_name']) ||
                    in_array($_POST['cf7cloud_email'], $_POST['cf7cloud_custom_field_name'])) {
                header('Location:' . $prev_url . '&cf7cloud_errors=' . urlencode($error_main) . '#cf7cloud_errors');
                exit;
            }

            // ***********************************
            // prefilter here to see which ones are to be unmapped
            foreach ($_POST['cf7cloud_custom_field_select'] as $key => $value) {
                if ($value == 'unmapped')
                    $_POST['cf7cloud_custom_field_select'][$key] = 'unmappedCUAPI_' . $key;
                //echo $value . "\n";
            }

            // **************************
            // THIS IS TO AVOID DUPLICATES IN CF7 FIELDS.
            //$counts = array_count_values( $_POST['cf7cloud_custom_field_name'] );
            $cf7_customs_duplicate = array_flip(array_filter(array_count_values($_POST['cf7cloud_custom_field_name']), create_function('$x', 'return $x > 1; ')));

            // check if duplicates for cf7 customs
            if (!empty($cf7_customs_duplicate)) {
                foreach ($cf7_customs_duplicate as $key => $value)
                    $string_error .= urlencode($value . ', ');
                header('Location:' . $prev_url . '&cf7cloud_errors=' . urlencode($error_customs) . urlencode($string_error) . urlencode(' only one field for relationship allowed') . "#cf7cloud_errors");
                exit;
            }

            // **************************
            // THIS IS TO AVOID DUPLICATES IN CUAPI FIELDS.
            //$counts = array_count_values( $_POST['cf7cloud_custom_field_name'] );
            $cf7_CUapi_duplicate = array_flip(array_filter(array_count_values($_POST['cf7cloud_custom_field_select']), create_function('$x', 'return $x > 1; ')));

            // check if duplicates for cf7 customs
            if (!empty($cf7_CUapi_duplicate)) {
                foreach ($cf7_CUapi_duplicate as $key => $value)
                    $string_error .= urlencode($value . ', ');
                header('Location:' . $prev_url . '&cf7cloud_errors=' . $error_CUapi . $string_error . urlencode(' Please try again.') . "#cf7cloud_errors");
                exit;
            }

            $the_data = array(); // array to store data as option for each form.

            $the_data['Full_Name'] = esc_sql($_POST['cf7cloud_name']);
            $the_data['Email'] = esc_sql($_POST['cf7cloud_email']);

            // ***********************************************
            // check first if custom fields have been created
            if (isset($_POST['cf7cloud_custom_field_name']) && isset($_POST['cf7cloud_custom_field_select'])) {

                //print_r( get_option('CU_cf7_cloud_mapped_fields_'.$_POST['post_ID'] ) ); exit;
                foreach ($_POST['cf7cloud_custom_field_name'] as $xkey => $xvalue) {
                    //if( $xvalue != 'your-name' && $xvalue != 'your-email' ){
                    $the_data['customs'][$xvalue] = ( isset($_POST['cf7cloud_custom_field_select'][$xkey]) ? esc_sql($_POST['cf7cloud_custom_field_select'][$xkey]) : '' );
                    //$field_count++;
                    //}
                }

                // check to see if no other fields have been added to CF7 textarea
                $simplify = array();
                $p = get_option('CU_cf7cloud_database_data_' . $_POST['post_ID']);
                $pc = $p['customs'];

                // current data stored in database array
                foreach ($p as $item_id => $item_value) {
                    if (!is_array($item_value))
                        $simplify[] = $item_value;
                }

                // current customs stored in database array
                foreach ($pc as $item_id => $item_value) {
                    $simplify[] = $item_id;
                }

                // get the number of fields in CF7 textarea
                $cf7_fields_quantity = count($cf7_customs);
                $actual_custom_amount = count($simplify);

                // ***************************************
                // if actual quantity of stored custom fields is not the same as the ones comming, some where added or deleted.
                if ((int) $cf7_fields_quantity != (int) $actual_custom_amount) {

                    // check for fields to be deleted in stack
                    $to_delete = array();
                    foreach ($simplify as $key => $value) {
                        if (!in_array($value, $cf7_customs))
                            $to_delete[] = $value;
                    }

                    // check for fields to be added to stack
                    $to_add = array();
                    foreach ($cf7_customs as $key => $value) {
                        if (!in_array($value, $simplify))
                            $to_add[] = $value;
                    }

                    // delete the erased fields
                    foreach ($to_delete as $key => $value) {
                        if (array_key_exists($value, $the_data['customs']))
                            unset($the_data['customs'][$value]);
                    }

                    // add the new fields
                    foreach ($to_add as $key => $value) {
                        if (!array_key_exists($value, $the_data['customs']) && !in_array($value, array('your-name', 'your-email')))
                            $the_data['customs'][$value] = '';
                    }
                }
            }else { // ***** this else will create the customs fields for the first time ********
                foreach ($cf7_customs as $xkey => $xvalue) {
                    if ($xvalue != 'your-name' && $xvalue != 'your-email') {
                        $the_data['customs'][$xvalue] = $xvalue;
                        //$field_count++;
                    }
                }
            }


            update_option('CU_cf7cloud_database_data_' . $_POST['post_ID'], $the_data);
            update_option('CU_cf7_cloud_mapped_fields_' . $_POST['post_ID'], $cf7_customs);
            update_option('CU_cf7cloud_database_data_' . $_POST['post_ID'] . '_amount', count($cf7_customs)); // the number of actual stored custom fields.
        } else {

            // update this form to inactive
            update_option('CU_cf7cloud_database_form_' . $_POST['post_ID'] . '_active', 0);
            delete_option('CU_cf7cloud_database_data_' . $_POST['post_ID']); // deleted fields to avoid any conflict when mapping
        }
    }

    /*
     * This is the method in charge to pre-process CF7 submitted data
     * @params none
     * @since 0.1
     * @returns void
     */

    public function wpcf7_cloud_send_all($wpcf7) {


        /* Use WPCF7_Submission object's get_posted_data() method to get it. */
        $submission = WPCF7_Submission::get_instance();

        if ($submission) {
            $posted_data = $submission->get_posted_data();
        }

        $data = '';

        $cuapi = new cUsComAPI_Cloud();

        // get the option for this specific form and see which fields to send to CU API
        $cf7cloud_data = get_option('CU_cf7cloud_database_data_' . $posted_data['_wpcf7']);
        // get if this form is active to send data to admin.contactus.com
        $is_active = get_option('CU_cf7cloud_database_form_' . $posted_data['_wpcf7'] . '_active');

        // **************************
        // check for unmapped fields and delete from array that is used to send to CU API
        $cf7cloud_data['customs'] = $this->_clear_unmapped($cf7cloud_data['customs']);

        // **************************************
        // first check if this form has any Analytics associated
        if ($cf7cloud_data && is_array($cf7cloud_data) && $is_active) {

            $CU_string = '';

            // check if this form has any CF7 Cloud database analytics associated to it
            foreach ($cf7cloud_data as $key => $value) {
                if (!is_array($value)) // avoid using arrays customs
                    if (array_key_exists($value, $posted_data)) {
                        $CU_string .= $key . "=" . urlencode($posted_data[$value]) . '&';
                    }
            }

            // now check for custom fields.
            foreach ($cf7cloud_data['customs'] as $key => $value) {
                // check if array of values comming or any other array type
                if (is_array($posted_data[$key])) {

                    // as array unify it as string and associate it to custom field
                    foreach ($posted_data[$key] as $akey => $avalue)
                        $CU_array .= $avalue . ' / ';

                    $CU_string .= $value . "=" . urlencode($CU_array) . '&';

                    $CU_array = NULL; // clear array to avoid duplicates in foreach association
                } elseif (array_key_exists($key, $posted_data)) {
                    $CU_string .= $value . "=" . urlencode($posted_data[$key]) . '&';
                }
            }

            // remove last character
            $CU_string = substr_replace($CU_string, "", -1);

            $ch = curl_init();

            $strCURLOPT = $this->API_url;
            $thekey = get_option('cUsCloud_settings_form_key');
            $credentials = get_option('cUsCloud_settings_userCredentials');

            $strCURLOPT .= 'API_Account=' . $credentials['API_Account']; // constants defined in config.php
            $strCURLOPT .= '&API_Key=' . $credentials['API_Key']; // constants defined in config.php
            $strCURLOPT .= '&API_Action=postSubmitLead';
            $strCURLOPT .= '&Form_Key=' . $thekey . '&'; // constants defined in config.php

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-ContactUs-Request-URL: ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                'X-ContactUs-Signature: CF7i|1.4.1|' . $cuapi->getIP(),
            ));

            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

            $strCURLOPT = trim($strCURLOPT . $CU_string);

            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_URL, $strCURLOPT);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($ch);
            curl_close($ch);
        }
    }

    /*
     * This method is in charge to clear the unmapped fields from the array
     * @params Array customs fields to check for unmapped
     * @since 0.1
     * @return Array with unmapped field unset
     */

    private function _clear_unmapped($customs) {

        foreach ($customs as $key => $value) {
            if (strpos($value, 'unmappedCUAPI') !== FALSE)
                unset($customs[$key]); // delete element from array to avoid sending it to CU Api.
        }

        return $customs;
    }

}

// end class definition

/* CF7 Cloud Database loader  */
$CF7_cloud_loader = new CF7_cloud_loader();
