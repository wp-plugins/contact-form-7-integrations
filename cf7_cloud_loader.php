<?php
/**
 * Initialization Class for CF7 Integrations 
 * Company 		: ContactUs.com
 * Programmer	: Estuardo Bengoechea
 * Updated  	: 20131114
 **/
require_once(dirname(__FILE__).'/models/interfaces/icf7_cloud_interface.php');

class CF7_cloud_loader extends CF7_cloud_interface {

	// Don't change this private values unless you know what you are doing
	private $cf7_cloud_db_version		= 	'0.1'; // cf7 cloud current DB version.
	private $cf7_cloud_version			= 	'1.0 Beta';
	
	// create here the list of possible fields for contactUs.com API calls
	private $CU_API_fields	=	array(
	  'Message' 				=> 	'Message',
	  'First_Name'				=> 	'First Name',
	  'Last_Name'				=> 	'Last Name',
	  'Full_Name'				=> 	'Full Name',
	  'IP_Address'				=> 	'IP address',
	  'Company_Name'			=>	'Company Name',
      'Secondary_Phone'			=>	'Secondary Phone',
	  'Address'					=>	'Address',
	  'Address2'				=>	'Address 2',
	  'City'					=>	'City',
	  'State'					=>	'State',
	  'Zip'						=>	'Zip',
	  'Country'					=>	'Country',
	  'Best_Time_To_Contact'	=>	'Best time to contact',
	  'Relationship'			=>	'Relationship',
	  'Landing_Page'			=>	'Landing Page',
	  'HTTP_Referer'			=>	'HTTP Referer',
	  'HTTP_User_Agent'			=>	'HTTP User Agent',
	  'UTM_Content'				=>	'UTM Content',
	  'UTM_Source'				=>	'UTM Source',
	  'UTM_Medium'				=>	'UTM Medium',
	  'UTM_Campaign'			=>	'UTM Campaign',
	  'UTM_Term'				=>	'UTM Term',
	  'Generic_Field_1'			=>	'Generic Field 1',
	  'Generic_Field_2'			=>	'Generic Field 2',
	  'Generic_Field_3'			=>	'Generic Field 3',
	  'Generic_Field_4'			=>	'Generic Field 4',
	  'Generic_Field_5'			=>	'Generic Field 5',
	  'Generic_Field_6'			=>	'Generic Field 6',
	  'Generic_Field_7'			=>	'Generic Field 7',
	  'Generic_Field_8'			=>	'Generic Field 8',
	  'Generic_Field_9'			=>	'Generic Field 9',
	  'Generic_Field_10'		=>	'Generic Field 10'
	);
	
	private $cf7class;
	
	// just the constructor for the action settings
	public function __construct(){
	
		// initialize something here :)
		add_action('admin_menu', array(&$this, 'cf7_cloud_database_menu'));
		
		// contact form 7 hooks/actions binding
		add_action("wpcf7_before_send_mail", array(&$this, 'wpcf7_cloud_send_all'));
		add_action( 'wpcf7_admin_after_mail', array(&$this, 'show_cf7cloud_metabox'));
		
		$cf7_cloud_activated = get_option('cf7_cloud_database_active');

		add_action( 'wpcf7_after_save', array(&$this, 'cf7cloud_save_form' ));

		// if user already signed/logged to ContactUs then show CF7 extension.
		if( $cf7_cloud_activated == 1){
		  add_action( 'wpcf7_admin_before_subsubsub', array(&$this, 'add_cf7cloud_meta') );
		}

			add_filter( "plugin_action_links", array(&$this, 'cf7cloud_plugin_action_links'), 10, 4);
			add_filter("plugin_row_meta", array(&$this, 'cf7cloud_plugin_links'), 10, 2);
			
			add_action('admin_print_scripts', array(&$this, 'Load_scripts'));
			add_action('admin_print_scripts', array(&$this, 'Load_styles'));
	}

	/**
	 * This should create the setting button in plugin CF7 cloud database
	 **/
	function cf7cloud_plugin_action_links( $links, $file ) {
		$plugin_file = 'contact-form-7-integrations/cf7-cloud-database.php';
		//make sure it is our plugin we are modifying
		if ( $file == $plugin_file ) {
			$settings_link = '<a href="' .
				admin_url( 'admin.php?page=cf7-integrations' ) . '">' .
				__( 'Settings', 'contact-form-7-integrations' ) . '</a>';
			array_unshift( $links, $settings_link );
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
	 **/
	private function create_cf7_cloud_options(){
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
	 **/
	public function activate() 
	{
		// Perform any databases modifications related to plugin activation here, if necessary
		require_once (ABSPATH.'wp-admin/includes/upgrade.php');
		global $wpdb;
		
	  /******************************** START PLUGIN SQL *************************************/

		/*$sql= "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."cf7_cloud_database` (
			  `field_id` bigint(22) unsigned NOT NULL AUTO_INCREMENT,
			  `field_name` varchar(100) NOT NULL,
			  PRIMARY KEY (`id_field`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		dbDelta($sql);

		$sql = "INSERT INTO `".$wpdb->prefix."cf7_cloud_database` (`field_id`, `field_name`) VALUES
				(1, 'Message'),
				(1, 'First'),
				";
		dbDelta($sql);*/
	  /******************************** END PLUGIN SQL *************************************/
	  
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

		}
		
		/*
		* create main menu and its options for CF7 Extension
		* @params none
		* @since 0.1
		* @return html that conforms the menus for the sidebar
		*/ 
		public function cf7_cloud_database_menu() {
			add_menu_page('CF7 Integrations', 'CF7 Integrations', 0, 'cf7-integrations', array($this, 'cf7_cloud_settings'), WP_PLUGIN_URL.'/contact-form-7-integrations/assets/images/favicon.gif');
			//add_submenu_page('menusuperior', __('Settings'), __('Settings'), 'edit_themes', 'settings', array($this, 'cf7_cloud_settings'));
		}
		
		/***************************************/
		/* THE REST OF PLUGIN RELATED METHODS */
		
		public function Load_scripts(){
			wp_register_script('my-scripts', WP_PLUGIN_URL.'/contact-form-7-integrations/assets/js/scripts.js');
			global $current_screen; // check we are in our CF7 integrations plugin page
			if( $current_screen->id == 'toplevel_page_cf7-integrations' || $current_screen->id == 'toplevel_page_wpcf7'){
				wp_enqueue_script('my-scripts');
			}
		}
			
		
		public function Load_styles(){
			global $current_screen;	// check we are in our CF7 integrations plugin page
			if( $current_screen->id == 'toplevel_page_cf7-integrations' || $current_screen->id == 'toplevel_page_wpcf7' ){
				wp_enqueue_style('cf7_cloud-styles', WP_PLUGIN_URL.'/contact-form-7-integrations/assets/css/styles.css' );
			}
		}
		
		
		/*
		* display admin page to manage requisitions
		* @params none
		* @since 0.1
		*/ 
		public function cf7_cloud_settings(){
			require_once('controllers/settings.php');
		}

		/**
		 * This is the method in charge to create the metabox for integration with Contact Form 7
		 * @params none
		 * @since 0.1
		 * return null
		 **/
		public function add_cf7cloud_meta (){

			if ( wpcf7_admin_has_edit_cap() ) {
			
				add_meta_box( 'cf7cf7clouddiv', __( 'Contact Form 7 Integrations by ContactUs.com', 'wpcf7' ),
					array($this, 'wpcf7_cf7cloud_add_contactus_analytics'), 'cf7clouddatabase', 'cf7_cf7cloud', 'core',
					array(
						'id' => 'wpcf7-cf7-integrations',
						'name' => 'cf7_cf7cloud',
						'use' => __( 'Turn On Contact Form 7 Integrations', 'wpcf7' ) ) );
			}
		}

		
		
		public function show_cf7cloud_metabox($cf){
			do_meta_boxes( 'cf7clouddatabase', 'cf7_cf7cloud', $cf );
		}
		
					
		public function wpcf7_cf7cloud_add_contactus_analytics($args)
		{
						//print_r($args); exit;
		?>
			
		<script type="text/javascript">
		//<![CDATA[
			jQuery(document).ready(function(){
				
				jQuery('#wpcf7-cf7cloud-active').on('click', function(){
					
					if( jQuery('#wpcf7-cf7cloud-active').is(':checked') ){
						//jQuery('#wpcf7-admin-form-element').submit();
						jQuery('#cf7cloud-formdata').show('fast');
					}else{
						jQuery('#cf7cloud-formdata').hide('fast');
					}
				});
	
				// data to be used to add new custom fields.
				function new_tr(tr_num){
					var new_row ='<tr id="row_'+tr_num+'">'+
				      	  	  '<td>'+
				      	  	  	'Select Contact Form 7 field:<br />'+
				      	  	  	'<select name="cf7cloud_custom_field_name[]">';
				      	  	  	<?php
				      	  	  	  $the_cf7_fields = get_option('CU_cf7_cloud_mapped_fields_'.$_GET['post']);
				      	  	  	  foreach( $the_cf7_fields as $key => $value ){
				      	  	  	  	echo "new_row += '<option value=\"$value\">$value</option>'; \n";
				      	  	  	  }
				      	  	  	?>
				      	  	  	
				      	  	  new_row += '</select>'+
				      	  	  '</td>'+
				      	  	  '<td>'+
				      	  	  	'Select ContactUs.com field to associate:<br />'+
				      	  	  	'<select name="cf7cloud_custom_field_select[]" id="cf7cloud_customfields">';
				      	  	  	  <?php
				      	  	  	  foreach( $this->CU_API_fields as $key => $value ){
				      	  	  	  	echo "new_row += '<option value=\"$key\">$value</option>'; \n";
				      	  	  	  }
				      	  	  	  ?>
				      	  	  	new_row += '</select>'+
				      	  	  '</td>'+
				      	  	  '<td>'+
				      	  	  '<span class="tr_delete" id="'+tr_num+'">[ X ]</span>'+
				      	  	  	//'<!-- <input type="button" id="" name="" value="Add Relation" /> -->'+
				      	  	  '</td>'+
				      	    '</tr>';
				      	    
				      	    return new_row;
					}
								
				// function to delete selected row
				jQuery('span.tr_delete').live('click', function(){
					if (!confirm("Do you want to delete this custom field")){
      					return false;
    				}
					var attribute = jQuery(this).attr('id');
					jQuery("#row_"+attribute).remove();
					
				});
	
				// add rows to custom fields
				jQuery('#cf7cloud_custom_fields_link').on('click', function() {
				  var actual_value = parseInt(jQuery('#trcount').val());
				  jQuery('#trcount').val(parseInt(actual_value+1));
			      jQuery('.cf7cloud_custom_fields_table tr:last').after(new_tr(jQuery('#trcount').val()));
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
				$the_data 		= get_option('CU_cf7cloud_database_data_'.$_GET['post']);
				$the_cf7_fields = get_option('CU_cf7_cloud_mapped_fields_'.$_GET['post']);
				$is_active_form = get_option('CU_cf7cloud_database_form_'.$_GET['post'].'_active');
				
				//print_r($the_cf7_fields);
				
				$cred = get_option('cUsCloud_settings_userCredentials');
				$cf7_cloud_activated = get_option('cf7_cloud_database_active');
				$fkey = get_option('cUsCloud_settings_form_key');
				//echo $fkey;

			?>
				
			<input type="hidden" name="trcount" id="trcount" value="<?php echo (is_array($the_data['customs'])?count($the_data['customs']):1 ); ?>" />
			<div class="mail-field">
				
				<input type="checkbox" id="wpcf7-cf7cloud-active" name="wpcf7-cf7cloud-active" value="1" <?php echo ( $is_active_form )?"checked":""; ?> />
				
				<label for="wpcf7-cf7cloud-active"><?php echo esc_html( __( 'Turn On Contact Form 7 Integrations', 'wpcf7' ) ); ?></label>
				<a name="cf7cloud_errors"></a>
				<?php
				// CF7 cloud errors in fields
				if( isset($_GET['cf7cloud_errors']) )
				{
					echo('<div class="cf7_cloud_errors">'.$_GET['cf7cloud_errors'].'</div>');
				}
				
				?>
				
				
			<div class="pseudo-hr"></div>
		
			<div id="cf7cloud-formdata" <?php echo ($is_active_form)?'style="display:block"':""; ?>>
				<input type="submit" name="map_button" id="map_button" value="Map Contact Form 7 Fields" style="padding:5px 10px 5px; cursor:pointer" /> <strong>Click here before mapping or editing your mapped fields (Required).</strong>
				
				<!-- insert video tutorial -->
				<div id="cf7cloud_video">
				  
				  <div class="cf7integrations_support">
					<h2>Contact Form 7 Integrations Support</h2>
					  	<ul>
					  	  <li><a href="http://help.contactus.com/hc/en-us/articles/200918046-Installing-the-CF7-Integrations-Plugin" target="_blank">Installing the CF7 Integrations Plugin</a></li>
					  	  <li><a href="http://help.contactus.com/hc/en-us/articles/200919166-Setting-up-your-Contact-Form-7-Integrations-Plugin" target="_blank">Setting up your "Contact Form 7 Integrations" Plugin</a></li>
					  	  <li><a href="http://help.contactus.com/hc/en-us/articles/201083933-Creating-a-POST-form-type" target="_blank">Creating a POST form type</a></li>
					  	  <li><a href="http://help.contactus.com/hc/en-us/articles/200927346-Integrating-your-Contact-Form-7-with-Third-party-applications" target="_blank">Integrating your Contact Form 7 with Third party applications</a></li>
					  	  <li><a href="http://help.contactus.com/hc/en-us/requests/new" target="_blank"><strong>Submit support ticket</strong></a></li>
					  	</ul>		  	
				</div>

				  <?php
				    $credentials = get_option('cUsCloud_settings_userCredentials');
				  ?>

  				  <?php if ( strlen($credentials['API_Key']) && strlen($credentials['API_Account']) ){ ?><a href="<?php echo plugins_url('contact-form-7-integrations/includes/toAdmin.php?iframe&uE='.$credentials['API_Account'].'&uC='.$credentials['API_Key']) ?>" target="_blank" rel="toDash" class="action_orange_button btn">Form Control Panel</a><?php } ?>
					
				<br /><br />
				 
				 <iframe src="//player.vimeo.com/video/79802852" width="356" height="195" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe> 
				 
				<!--<iframe width="350" height="300" src="https://vimeo.com/79802852" frameborder="0" allowfullscreen></iframe>--><br />
				  
				  <!--
				  <input type="button" value="View your Contacts" id="viewyourcontacts" name="viewyourcontacts" class="action_orange_button button_redirect"><br />
				  <input type="button" value="View Your Stats" id="viewyourstats" name="viewyourstats" class="action_orange_button button_redirect"><br />
				  <input type="button" value="Manage Your Deliveries" id="managedeliveries" name="managedeliveries" class="action_orange_button button_redirect"><br />
				  -->
				  
				</div>
				<!-- / insert video tutorial -->
				<table id="cf7_cloud_table" <?php echo ($is_active_form)?'style="display:block"':'style="display:none"'; ?>>
				  <tbody>
				    <tr><td colspan="2"><h4>To integrate your ContactUs.com account with your form, you must map fields by matching Contact Form 7 fields with ContactUs.com form fields. We have set the default form fields, but please make sure they are correct.</h4></td></tr>
				    <tr>
				      <td>Input name for EMAIL field:</td>
				      <td>
				      	<select name="cf7cloud_email">
				      		<?php 
				      		// list the CF7 fields names
				      		foreach( $the_cf7_fields as $key => $value ){
				      		  if( $the_data['Email'] == $value )	
				      			 echo('<option value="'.$value.'" selected="selected">'.$value.'</option>');
							  else{
							  		
							  	// check if email name of CF7 is the default, your email
							  	if( $value == 'your-email' )
							  	   echo('<option value="'.$value.'" selected="selected">'.$value.'</option>');
								else
								   echo('<option value="'.$value.'">'.$value.'</option>');
								
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
				      		foreach( $the_cf7_fields as $key => $value ){
				      		  if( $the_data['Full_Name'] == $value )	
				      			 echo('<option value="'.$value.'" selected="selected">'.$value.'</option>');
							  else{
							  	
							  	// check if email name of CF7 is the default, your name
							  	if( $value == 'your-name' )
							  	   echo('<option value="'.$value.'" selected="selected">'.$value.'</option>');
								else
								   echo('<option value="'.$value.'">'.$value.'</option>');
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
				    // print_r($the_data['customs']); //exit;
				    // echo count( $the_cf7_fields )-2;
				    $count_mapper = 0; // variable to compare how many custom fields are being displayed already and not allow more than $total-2
				    
				    if(isset($the_data['customs']) && is_array($the_data['customs'])){
				      $counter = 1; // counter to create row ids
				      foreach( $the_data['customs'] as $key => $value ){
				      		
				      // check and compare amount of fields with mapper
				      if((count( $the_cf7_fields )-2) == $count_mapper){
				        break;
				      }	
				      	
				    // echo $key;
				    ?>
						<tr id="row_<?php echo $counter; ?>">
				      	  	  <td>
				      	  	  	Select Contact Form 7 field:<br />
				      	  	  	<select name="cf7cloud_custom_field_name[]">
						      		<?php 
						      		// list the CF7 fields names
						      		foreach( $the_cf7_fields as $xkey => $xvalue ){
						      		  if( $xvalue == $key )	
						      			 echo('<option value="'.$xvalue.'" selected="selected">'.$xvalue.'</option>');
									  else{
									  	// avoid including here CF7 defaults, your-name and your-mail
										if($xvalue != 'your-name' && $xvalue != 'your-email')
										  if($value == 'your-subject'){
									  	    echo('<option value="'.$xvalue.'">'.$xvalue.'</option>');
											$subject_present = TRUE;
										  }elseif( $value == 'your-message' ){
										  	echo('<option value="'.$xvalue.'">'.$xvalue.'</option>');
											$message_present = TRUE;
										  }else
										  	echo('<option value="'.$xvalue.'">'.$xvalue.'</option>');
										    
									  
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
				      	  	  	  foreach($this->CU_API_fields as $skey => $svalue){
				      	  	  	 	if( $value == $skey ){
				      	  	  	      echo('<option value="'.$skey.'" selected="selected">'.$svalue.'</option>');
				      	  	  	    }else{
				      	  	  	      	
				      	  	  	      // check if subject_present gives TRUE to select Generic_Field_10
									  if($subject_present == TRUE && $skey == 'Generic_Field_10'){
				      	  	  	        echo('<option value="'.$skey.'" selected="selected">'.$svalue.'</option>');
										$subject_present = FALSE;
									  }elseif($message_present == TRUE && $skey == 'Message'){
									  	echo('<option value="'.$skey.'" selected="selected">'.$svalue.'</option>');
										$message_present = FALSE;
									  }else
									  	echo('<option value="'.$skey.'">'.$svalue.'</option>');
 
				      	  	  	    }
				      	  	  	  }
				      	  	  	  ?>
				      	  	  	</select>
				      	  	  </td>
				      	  	  <td>&nbsp;
				      	  	    <!--<span class="tr_delete" id="<?php echo $counter; ?>">[ X ]</span>-->
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
				
				<table class="cf7cloud_custom_fields_table" <?php echo ($is_active_form)?'style="display:block"':'style="display:none"'; ?>>
				      	  <tbody>
				      	    <tr>
				      	  	  <td colspan="3">When you’re finish mapping, remember to hit <img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/cf7_save_button.png" width="51" height="26" alt="Save button on upper right" title="Save button on upper right" style="display:inline-block; vertical-align:middle;" /> your Contact Form 7 settings! (Save button is located on upper right)<br /><br /></td>
				      	    </tr>
							 <tr>
				      	  	  <td colspan="3"><strong>What’s Next?</strong>
								<p>Once saved, your ContactUs.com account is new connected with your Contact Form 7 form. Visit your ContactUs.com admin panel to:   </p>
								<ul>
								  <li><a href="#">Track Your Leads</a></li>
								  <li><a href="#">View Your States</a></li>
								  <li><a href="#">Integration 3rd Party Software</a></li>
								</ul>			
							  </td>
				      	    </tr>
				      	  </tbody>
				      	</table>
			</div>
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
		private function _get_cf7_inputs($cf7_form){

			$cf7_shortcodes = preg_match_all( '#\[[text|select|checkbox|radio|tel|email|url|number|textarea]\s*.*?\]#s', $cf7_form, $matches );
			$the_values = Array();
			
			// loop the fields found in CF7 textarea
			foreach( $matches[0] as $key => $value ){
				$the_values[] = explode(" ", $value);
				$the_names[$key] = str_replace(']', '', $the_values[$key][1]);
			}
			
			// delete the submit button of the end, TODO: hope always is in the end xD otherwise we must change this procedure.
			array_pop( $the_names );
			return $the_names;

		}
	
		/*
		* Method in charge to save the relationships between contact form 7 and CU cloud Database
		* @params Array all the actual editing form data being submitted
		* @since 0.1
		* @returns Null
		*/
		public function cf7cloud_save_form($args)
		{
			// create an option for the custom_cf7_fields that come
			$cf7_customs = $this->_get_cf7_inputs($args->form);
			
			//print_r( $_POST ); exit;
			// print_r(get_option( 'CU_cf7cloud_database_data_'.$_POST['post_ID'] )); exit;
						
			$prev_url = $_SERVER["HTTP_REFERER"];
			
			$error_main 	= 'The Email and Name cannot have same CF7 select values';
			$error_customs 	= 'The following CF7 fields were detected duplicates in your selects: ';
			$error_CUapi 	= 'The following ContactUs.com fields were detected duplicate in your selects: ';
			$string_error 	= '';
			
			//print_r( $cf7_customs ); exit;

			//print_r( $_POST['cf7cloud_custom_field_name'] ); exit;
			
			if( (int)$_POST['wpcf7-cf7cloud-active'] == 1 ){
				
					// save if this form is active as an option
					update_option( 'CU_cf7cloud_database_form_'.$_POST['post_ID'].'_active', 1 );
						
					$the_data['Full_Name'] = esc_sql($_POST['cf7cloud_name']);
					$the_data['Email'] = esc_sql($_POST['cf7cloud_email']);
						
					// *********************************
					// check email and name dont have equal select values
					if( trim($_POST['cf7cloud_name']) != '' && trim($_POST['cf7cloud_email']) != '' ){
						if( (string)$_POST['cf7cloud_name'] == (string)$_POST['cf7cloud_email'] ){
							header('Location:'.$prev_url.'&cf7cloud_errors='.urlencode($error_main).'#cf7cloud_errors');
						 	exit;
						}		
					}
					
					// check if any of the above fields is already selected in custom fields. This is when user changes default field names for name and email.
					if( isset( $_POST['cf7cloud_custom_field_name'] ) && in_array($_POST['cf7cloud_name'], $_POST['cf7cloud_custom_field_name']) || 
					in_array($_POST['cf7cloud_email'], $_POST['cf7cloud_custom_field_name']) ){
						header('Location:'.$prev_url.'&cf7cloud_errors='.urlencode($error_main).'#cf7cloud_errors');
						exit;
					}
					
					// ***********************************
					// prefilter here to see which ones are to be unmapped
					foreach( $_POST['cf7cloud_custom_field_select'] as $key => $value ){	
						if( $value == 'unmapped' )
						  $_POST['cf7cloud_custom_field_select'][$key] = 'unmappedCUAPI_'.$key;
						//echo $value . "\n";
					}
					
					//exit;
					
					//print_r($_POST); exit;
					
					// **************************
					// THIS IS TO AVOID DUPLICATES IN CF7 FIELDS.
					//$counts = array_count_values( $_POST['cf7cloud_custom_field_name'] );
					$cf7_customs_duplicate = array_flip(array_filter( array_count_values($_POST['cf7cloud_custom_field_name']), create_function('$x', 'return $x > 1; ')));
					
					// check if duplicates for cf7 customs
					if( !empty($cf7_customs_duplicate) ){
					  foreach($cf7_customs_duplicate as $key => $value)
					    $string_error .= urlencode($value.', '); 
					 header('Location:'.$prev_url.'&cf7cloud_errors='.urlencode($error_customs).urlencode($string_error).urlencode(' only one field for relationship allowed')."#cf7cloud_errors");
					 exit;
					}
					
					// **************************
					// THIS IS TO AVOID DUPLICATES IN CUAPI FIELDS.
					//$counts = array_count_values( $_POST['cf7cloud_custom_field_name'] );
					$cf7_CUapi_duplicate = array_flip(array_filter( array_count_values($_POST['cf7cloud_custom_field_select']), create_function('$x', 'return $x > 1; ')));
		
					//print_r( $cf7_CUapi_duplicate ); exit;
					
					// check if duplicates for cf7 customs
					if( !empty($cf7_CUapi_duplicate) ){
					  foreach($cf7_CUapi_duplicate as $key => $value)
					    $string_error .= urlencode($value.', ');
					 header('Location:'.$prev_url.'&cf7cloud_errors='.$error_CUapi.$string_error.urlencode(' only one field for relationship allowed')."#cf7cloud_errors");
					 exit;
					}
				
					$the_data = array(); // array to store data as option for each form.

					$the_data['Full_Name'] = esc_sql($_POST['cf7cloud_name']);
					$the_data['Email'] = esc_sql($_POST['cf7cloud_email']);
					//$the_data['Primary_Phone'] = esc_sql($_POST['cf7cloud_phone']);
					
					// echo( $cf7_fields_quantity ); exit;	
					// echo $cf7_customs[2]; exit;
					// $field_count = 0;
					
					//echo count($cf7_customs); exit;
					
					// ***********************************************
					// check first if custom fields have been created
					if( isset($_POST['cf7cloud_custom_field_name']) && isset($_POST['cf7cloud_custom_field_select']) ){
								
						//print_r( get_option('CU_cf7_cloud_mapped_fields_'.$_POST['post_ID'] ) ); exit;
						foreach( $_POST['cf7cloud_custom_field_name'] as $xkey => $xvalue ){
							//if( $xvalue != 'your-name' && $xvalue != 'your-email' ){
								$the_data['customs'][$xvalue] = (isset($_POST['cf7cloud_custom_field_select'][$xkey])?esc_sql($_POST['cf7cloud_custom_field_select'][$xkey]):'' );
								//$field_count++;
							//}
						}
						
						// check to see if no other fields have been added to CF7 textarea
						
						$simplify 	= array();
						$p 			= get_option('CU_cf7cloud_database_data_'.$_POST['post_ID']);
						$pc 		= $p['customs'];
						
						// $pv = array_values($p);
						// print_r($p); exit;

						// current data stored in database array
						foreach($p as $item_id => $item_value){
							if(!is_array($item_value))
								$simplify[] = $item_value;
						}
						
						// current customs stored in database array
						foreach($pc as $item_id => $item_value){		
								$simplify[] = $item_id;
						}
							
						// get the number of fields in CF7 textarea
						$cf7_fields_quantity = count( $cf7_customs );
						$actual_custom_amount = count($simplify);
						
						// ***************************************
						// if actual quantity of stored custom fields is not the same as the ones comming, some where added or deleted.
						if( (int)$cf7_fields_quantity != (int)$actual_custom_amount ){
								
							// print_r( $simplify );
							// print_r( $cf7_customs );
							// exit;
							
							// check for fields to be deleted in stack
							$to_delete = array();
							foreach( $simplify as $key => $value ){
								if( !in_array($value, $cf7_customs) )
									$to_delete[] = $value;
							}
							
							//print_r($to_delete); exit;

							// check for fields to be added to stack
							$to_add = array();
							foreach( $cf7_customs as $key => $value ){
								if( !in_array($value, $simplify) )
									$to_add[] = $value;
							}
							
							// delete the erased fields
							foreach($to_delete as $key => $value){
								if( array_key_exists($value, $the_data['customs']) )
									unset($the_data['customs'][$value]);
							}
							
							//print_r($to_add); exit;
							
							// add the new fields
							foreach($to_add as $key => $value){
								if( !array_key_exists($value, $the_data['customs']) && !in_array($value, array('your-name','your-email')) )
									$the_data['customs'][$value] = '';
							}
							
						}


					}else{ // ***** this else will create the customs fields for the first time ********
						
						foreach( $cf7_customs as $xkey => $xvalue ){
							if( $xvalue != 'your-name' && $xvalue != 'your-email' ){
								$the_data['customs'][$xvalue] = $xvalue;
								//$field_count++;
							}
						}
					}

					update_option( 'CU_cf7cloud_database_data_'.$_POST['post_ID'], $the_data );
					update_option( 'CU_cf7_cloud_mapped_fields_'.$_POST['post_ID'], $cf7_customs );
					update_option( 'CU_cf7cloud_database_data_'.$_POST['post_ID'].'_amount', count($cf7_customs) ); // the number of actual stored custom fields.
					// print_r( get_option('CU_cf7_cloud_mapped_fields_'.$_POST['post_ID']) ); exit;
			}else{
				
				// update this form to inactive
				update_option( 'CU_cf7cloud_database_form_'.$_POST['post_ID'].'_active', 0 );
				
			}
				
		}

		/*
		* This is the method in charge to pre-process CF7 submitted data
		* @params none
		* @since 0.1
		* @returns void
		*/
		public function wpcf7_cloud_send_all(&$wpcf7) {
		  	  
		  $data = '';
		  
		  // get the option for this specific form and see which fields to send to CU API
		  $cf7cloud_data = get_option('CU_cf7cloud_database_data_'.$wpcf7->posted_data['_wpcf7']);
		  //print( $wpcf7->posted_data );
		  //exit;
		  
		  // get if this form is active to send data to admin.contactus.com
		  $is_active = get_option('CU_cf7cloud_database_form_'.$wpcf7->posted_data['_wpcf7'].'_active');
		   
		  // **************************
		  // check for unmapped fields and delete from array that is used to send to CU API
		  $cf7cloud_data['customs'] = $this->_clear_unmapped($cf7cloud_data['customs']);
		  

		  	// **************************************
		  	// first check if this form has any Analytics associated
		  	if( $cf7cloud_data && is_array($cf7cloud_data) && $is_active ){
				  
				  // print_r( $cf7cloud_data); exit;
				  $CU_string = '';
					
					// check if this form has any CF7 Cloud database analytics associated to it
					foreach( $cf7cloud_data as $key => $value ){
						if( !is_array($value) ) // avoid using arrays customs
							if( array_key_exists($value, $wpcf7->posted_data) ){
								$CU_string .= $key."=".urlencode($wpcf7->posted_data[$value]).'&';
							}
					}

					// now check for custom fields.
					foreach( $cf7cloud_data['customs'] as $key => $value ){
							if( array_key_exists($key, $wpcf7->posted_data) ){
								$CU_string .= $value."=".urlencode($wpcf7->posted_data[$key]).'&';
							}
					}
					
					//echo $CU_string; exit;
					
					// remove last character
					$CU_string = substr_replace($CU_string ,"",-1);
					//$CU_string = urlencode($CU_string);
			
			        $ch = curl_init();
			
			        $strCURLOPT  = 'https://api.contactus.com/api2.php?';
			        //$strCURLOPT  = 'http://test.contactus.com/api2.php?';
			        
			        $thekey 		= get_option('cUsCloud_settings_form_key');
			        $credentials 	= get_option('cUsCloud_settings_userCredentials');
                   
					$strCURLOPT .= 'API_Account='.$credentials['API_Account']; // constants defined in config.php
			        $strCURLOPT .= '&API_Key='.$credentials['API_Key']; // constants defined in config.php
			        $strCURLOPT .= '&API_Action=postSubmitLead';
					$strCURLOPT .= '&Form_Key='.$thekey.'&'; // constants defined in config.php

					$strCURLOPT = trim($strCURLOPT.$CU_string);
					//echo $strCURLOPT; exit;
					
					// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			        curl_setopt($ch, CURLOPT_URL, $strCURLOPT );
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
		* @returns Array with unmapped field unset
		*/
		private function _clear_unmapped($customs){
			
			foreach( $customs as $key => $value ){
		  	if( strpos($value, 'unmappedCUAPI') !== FALSE )
			  unset($customs[$key]); // delete element from array to avoid sending it to CU Api.
		    }
			
			return $customs;
			
		}
		
} // end class definition

/* CF7 Cloud Database loader  */
$CF7_cloud_loader	= new CF7_cloud_loader();