<?php
/**
 * Initialization Class for CF7 cloud database
 * Company 		: contactus.com
 * Programmer	: Estuardo Bengoechea
 * Updated  	: 20131018
 **/
require_once(dirname(__FILE__).'/models/interfaces/icf7_cloud_interface.php');
//require_once(dirname(__FILE__).'/../contact-form-7/includes/shortcodes.php');

class CF7_cloud_loader extends CF7_cloud_interface {

	// Don't change this private values unless you know what you are doing
	private $cf7_cloud_db_version		= 	'0.1'; // cf7 cloud current DB version.
	private $cf7_cloud_version			= 	'0.3.11 Alpha';
	
	// create here the list of possible fields for contactUs.com API calls
	private $CU_API_fields	=	array(
	  'Message' 				=> 	'Message',
	  'First_Name'				=> 	'First Name',
	  'Last_Name'				=> 	'Last Name',
	  'Full_Name'				=> 	'Full Name',
	  'IP_Address'				=> 	'IP address',
	  'Company_Name'			=>	'Company Name',
	  'Seconday_Phone'			=>	'Secondary Phone',
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
		add_action('admin_print_scripts', array(&$this, 'Load_scripts'));
		add_action('admin_print_scripts', array(&$this, 'Load_styles'));

		// contact form 7 hooks/actions binding
		add_action("wpcf7_before_send_mail", array(&$this, 'wpcf7_cloud_send_all'));
		add_action( 'wpcf7_admin_after_form', array(&$this, 'show_cf7cloud_metabox') );
		
		$cf7_cloud_activated = get_option('cf7_cloud_database_active');

		add_action( 'wpcf7_after_save', array(&$this, 'cf7cloud_save_form' ));

		// if user already signed/logged to ContactUs then show CF7 extension.
		if( $cf7_cloud_activated == 1){
		  add_action( 'wpcf7_admin_before_subsubsub', array(&$this, 'add_cf7cloud_meta') );
		}

		add_filter( "plugin_action_links", array($this, 'cf7cloud_plugin_action_links'), 10, 4 );
		
		//$this->cf7class = new WPCF7_ShortcodeManager();
	}

 		/**
		 * This should create the setting button in plugin CF7 cloud database
		 **/
		function cf7cloud_plugin_action_links( $links, $file ) {
			$plugin_file = 'contact-form-7-cloud-database/cf7-cloud-database.php';
			//make sure it is our plugin we are modifying
			if ( $file == $plugin_file ) {
				$settings_link = '<a href="' .
					admin_url( 'admin.php?page=cf7-cloud-database' ) . '">' .
					__( 'Settings', 'contact-form-7-cloud-database' ) . '</a>';
				array_unshift( $links, $settings_link );
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

			// This call needs to be made to deactivate this app within WP MVC
			// $this->deactivate_app(__FILE__);
			// Perform any databases modifications related to plugin deactivation here, if necessary
		}
		
		/*
		* create main menu and its options for CF7 Extension
		* @params none
		* @since 0.1
		* @return html that conforms the menus for the sidebar
		*/ 
		public function cf7_cloud_database_menu() {
			add_menu_page('CF7 Cloud DB', 'CF7 Cloud DB', 0, 'cf7-cloud-database', array($this, 'cf7_cloud_settings'), WP_PLUGIN_URL.'/contact-form-7-cloud-database/assets/images/logo-cu.png', 3 );
			//add_submenu_page('menusuperior', __('Settings'), __('Settings'), 'edit_themes', 'settings', array($this, 'cf7_cloud_settings'));
		}
		
		/***************************************/
		/* THE REST OF PLUGIN RELATED METHODS */
		
		public function Load_scripts(){
			wp_register_script('my-scripts', WP_PLUGIN_URL.'/contact-form-7-cloud-database/assets/js/scripts.js');
			
			// now registered , enqueue.
			wp_enqueue_script('my-scripts');
		}
			
		
		public function Load_styles(){
			wp_enqueue_style('cf7_cloud-styles', WP_PLUGIN_URL.'/contact-form-7-cloud-database/assets/css/styles.css' );
			//wp_enqueue_style('thickbox');
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
		function add_cf7cloud_meta (){

			if ( wpcf7_admin_has_edit_cap() ) {
				add_meta_box( 'cf7cf7clouddiv', __( 'ContactUs.com Analytics', 'wpcf7' ),
					array($this, 'wpcf7_cf7cloud_add_contactus_analytics'), 'cf7clouddatabase', 'cf7_cf7cloud', 'core',
					array(
						'id' => 'wpcf7-cf7-cloud-database',
						'name' => 'cf7_cf7cloud',
						'use' => __( 'Use ContactUs.com Analytics', 'wpcf7' ) ) );
			}
		}
		
		function show_cf7cloud_metabox($cf){
			do_meta_boxes( 'cf7clouddatabase', 'cf7_cf7cloud', $cf );
		}
		
					
		function wpcf7_cf7cloud_add_contactus_analytics($args)
		{
						//print_r($args); exit;
		?>
			
		<script type="text/javascript">
		//<![CDATA[
			jQuery(document).ready(function(){
				
				var current_options = ''; // global variable
				var current_options_raw;

				jQuery('#wpcf7-cf7cloud-active').on('click', function(){
					if( jQuery('#wpcf7-cf7cloud-active').is(':checked') ){
						jQuery('#cf7cloud-formdata').show('fast');
					}else{
						jQuery('#cf7cloud-formdata').hide('fast');
					}
				});
	
				// data to be used to add new custom fields.
				function new_tr(tr_num){
					var new_row ='<tr id="row_'+tr_num+'">'+
				      	  	  '<td>'+
				      	  	  	'Specify CF7 custom field name:<br />';
				      	  	  	new_row += current_options; 
				      	  	  '</td>'+
				      	  	  '<td>'+
				      	  	  	'<select name="cf7cloud_custom_field_select[]">';
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
			    
			    // check checkbox for CF7 cloud is checked and see required fields are met
			    jQuery('form#wpcf7-admin-form-element').on('submit', function( event ){
			    	
			    	if( jQuery('#wpcf7-cf7cloud-active').is(':checked') ){
			    		// get three main and required values
			    		var name	=	jQuery('#cf7cloud_name').val();
			    		var email	=	jQuery('#cf7cloud_email').val();
			    		var phone	=	jQuery('#cf7cloud_phone').val();
			    		
			    		// validate fields on this JS side.
			    		if( name.length == 0 ){
			    			alert('You must relate the NAME field with your Contact Form 7 field name');
			    			return false;
			    		}else if( email.length == 0 ){
			    			alert('You must relate the EMAIL field with your Contact Form 7 field email');
			    			return false;
			    		}else if( phone.length == 0 ){
			    			alert('You must relate the PHONE field with your Contact Form 7 field phone');
			    			return false;
			    		}
			    	}
			    });
			    
			    
			    // *********************************************************************
			    // check checkbox for CF7 cloud is checked and see required fields are met
			    jQuery('#map_button').on('click', function(event){
			    	// alert(jQuery('#wpcf7-form').val());
			    	// postData = {action: '_get_cf7_inputs', cf7data:str_clean()};
			    	jQuery('#loading_mapper').css({'display':'block'});
			    	jQuery.ajax({
                    type: "POST",
                    dataType : 'json',
                    url: "<?php echo ADMIN_AJAX_URL; ?>admin-ajax.php",
                    data: {action: '_get_cf7_inputs' , cf7_form : jQuery('#wpcf7-form').val()},
                    success: function(data) {
                    //var theJson = jQuery.getJSON(data);
                    	current_options_raw = data;
                    	
                        current_options += '<select name="cf7cloud_custom_field_select[]">';
                        // below try to register the new user cause email
                        jQuery.each(data, function( i, item ){
                        	current_options += '<option value="'+item+'">'+item+'</option>';
                        });
                        current_options += '</select>';
                        
						    if(jQuery('#mapped_once').attr('value') == 0){                    
		                        // inject the fields into the td
		                        jQuery('#for_name').html( current_options );
		                        jQuery('#for_email').html( current_options );
		                        jQuery('#for_phone').html( current_options );
		                        jQuery('#mapped_once').attr('value', 1);
	                       }
                        // show the extra fields.
                        jQuery('#cf7_cloud_table').css({'display':'inline-block'});
                        jQuery('#loading_mapper').css({'display':'none'});

                    },
                    fail: function(){
                    	alert('Mapping Error......');
                       
                    }
                });
	
			    	
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
				$the_data 	= get_option('CU_cf7cloud_database_data_'.$_GET['post']);
				$cred 		= get_option('cUsCloud_settings_userCredentials');
				$cf7_cloud_activated = get_option('cf7_cloud_database_active');
				$fkey 		= get_option('cUsCloud_settings_form_key');
				//echo $fkey;
			?>
				
			<input type="hidden" name="trcount" id="trcount" value="<?php echo (is_array($the_data['customs'])?count($the_data['customs']):1 ); ?>" />
			<div class="mail-field">
				<input type="checkbox" id="wpcf7-cf7cloud-active" name="wpcf7-cf7cloud-active" value="1" <?php echo (is_array($the_data))?"checked":""; ?> />
				<label for="wpcf7-cf7cloud-active"><?php echo esc_html( __( 'Use contactUs Analytics', 'wpcf7' ) ); ?></label>
			<div class="pseudo-hr"></div>
			
			<input type="button" name="map_button" id="map_button" value="Map Contact Form 7 Fields" style="padding:5px 10px 5px; cursor:pointer" />
			<inpput type="hidden" name="mapped_once" id="mapped_once" value="0" />
			<img id="loading_mapper" src="<?php echo plugins_url(); ?>/contact-form-7-cloud-database/assets/images/ajax-loader.gif" width="16" height="16" alt="Loading....." />
		
			<div id="cf7cloud-formdata" <?php echo (is_array($the_data))?'style="display:block"':""; ?>>
				
				<!-- insert video tutorial -->
				<div id="cf7cloud_video">
				  <iframe width="350" height="300" src="<?php echo CU_VIDEO; ?>" frameborder="0" allowfullscreen></iframe>
				</div>
				<!-- / insert video tutorial -->
				<table id="cf7_cloud_table">
				  <tbody>
				    <tr><td colspan="2"><h4>To integrate analytics with your ContactUs.com account you must provide the Contact Form 7 field names to the following inputs:</h4></td></tr>
				    <tr>
				      <td>Specify input name for NAME field:</td>
				      <td id="for_name">
				      	
				      </td>
				    </tr>
		
				    <tr>
				      <td>Input name for EMAIL field:</td>
				      <td id="for_email">
				      	
				      </td>
				    </tr>
				    <tr>
				      <td>Specify input name for PHONE field:</td>
				      <td id="for_phone">
				      	
				      	<a name="new_customs"></a>
				      </td>
				    </tr>
				    <tr>
				      <td colspan="2"><br /><h2>CONTACTUS.COM CUSTOM FIELDS</h2>
				      	<script type="text/javascript">
		      	  	  	  //<![CDATA[
		      	  	  	  
		      	  	  	    
		      	  	  	  jQuery(document).ready(function(){
		      	  	  	  	alert(current_options_raw);
		      	  	  	  	jQuery.each(current_options_raw, function( i, item ){
		      	  	  	    	alert(item);
	                        	current_options += '<option value="'+item+'">'+item+'</option>';
	                        });
		      	  	  	  });
		      	  	  	    
		      	  	  	    
		      	  	  	  
		      	  	  	  
		      	  	  	  //]]>
		      	  	  	</script>
				      </td>
				    </tr>
				    
				    <tr>
				      <td colspan="2">&nbsp;[<a id="cf7cloud_custom_fields_link" href="#new_customs">add custom field</a>]<br /><br />
				      </td>
					</tr>
					    
				    <?php
				    // check if custom forms fields available to show or not
				    if(is_array($the_data['customs'])){
				      
				      $counter = 1; // counter to create row ids
				      foreach($the_data['customs'] as $key => $value){
				    ?>
						<tr id="row_<?php echo $counter; ?>">
				      	  	  <td>
				      	  	  	Specify CF7 custom field:<br />
				      	  	  	<select name="cf7cloud_custom_field_name[]">

				      	  	  	  <script type="text/javascript">
				      	  	  	  //<![CDATA[
				      	  	  	  
				      	  	  	    jQuery.each(current_options_raw, function( i, item ){
				      	  	  	    	alert(item);
			                        	current_options += '<option value="'+item+'">'+item+'</option>';
			                        });
				      	  	  	  
				      	  	  	  
				      	  	  	  //]]>
				      	  	  	  </script>
	
				      	  	  		<?php
				      	  	  		// capture values of current custom fields.
				      	  		/*
					      	  	  	  // list and select current select value
					      	  	  	  foreach($this->CU_API_fields as $skey => $svalue){
					      	  	  	 	if( $key == $skey ){
					      	  	  	      echo('<option value="'.$skey.'" selected>'.$svalue.'</option>');
					      	  	  	    }else{
					      	  	  	      echo('<option value="'.$skey.'">'.$svalue.'</option>');
					      	  	  	    }
					      	  	  	  }
				      	  	  	  */
				      	  	  	
				      	  	  	?>
				      	  	  </select>

				      	  	  <!-- <input type="text" name="cf7cloud_custom_field_name[]" id="cf7cloud_custom_field_name" value="<?php echo (count($the_data) > 0)?$the_data['customs'][$key]:""; ?>" placeholder="paste CF7  field name" />-->
				      	  	  </td>
				      	  	  <td>
				      	  	  	Select ContactUs.com field to associate:<br />
				      	  	  	<?php
				      	  	  	//print_r($this->CU_API_fields);
				      	  	  	?>
				      	  	  	
				      	  	  	<select name="cf7cloud_custom_field_select[]" id="cf7cloud_customfields">
				      	  	  	  <?php
				      	  	  	  // list and select current select value
				      	  	  	  foreach($this->CU_API_fields as $skey => $svalue){
				      	  	  	 	if( $key == $skey ){
				      	  	  	      echo('<option value="'.$skey.'" selected>'.$svalue.'</option>');
				      	  	  	    }else{
				      	  	  	      echo('<option value="'.$skey.'">'.$svalue.'</option>');
				      	  	  	    }
				      	  	  	  }
				      	  	  	  ?>
				      	  	  	</select>
				      	  	  </td>
				      	  	  <td>
				      	  	    <span class="tr_delete" id="<?php echo $counter; ?>">[ X ]</span>
				      	  	  </td>
				      	    </tr>
	
					<?php
					  $counter++; // increment the counter for row identification
					  } // end foreach
					
					}
				    ?>
				    <tr>
				      <td colspan="2">
				      	<table class="cf7cloud_custom_fields_table">
				      	  <tbody>
				      	    <tr>
				      	  	  <td colspan="3">&nbsp;</td>
				      	    </tr>
				      	  </tbody>
				      	</table> 
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
			</div>
			</div>
			<br class="clear" />
		
		<?php

		}
	
	
		/*
		* Method in charge to save the relationships between contact form 7 and CU cloud Database
		* @params Array all the actual editing form data being submitted
		* @since 0.1
		* @returns Null
		*/
		function cf7cloud_save_form($args)
		{

			// loop through the fields to get the field name
			//$this->_get_cf7_inputs($args->form);
			
			if( (int)$_POST['wpcf7-cf7cloud-active'] == 1 && 
			trim($_POST['cf7cloud_name']) != '' && 
			trim($_POST['cf7cloud_email']) != '' && 
			trim($_POST['cf7cloud_phone']) != '' ){
				
					$the_data = array(); // array to store data as option for each form.
					
					$the_data['Full_Name'] = esc_sql($_POST['cf7cloud_name']);
					$the_data['Email'] = esc_sql($_POST['cf7cloud_email']);
					$the_data['Primary_Phone'] = esc_sql($_POST['cf7cloud_phone']);
					
					// check if custom fields and store them as key = value
					if(count($_POST['cf7cloud_custom_field_select']) > 0){
						//$counter = 0;	
						foreach( $_POST['cf7cloud_custom_field_select'] as $key => $value ){
							$the_data['customs'][$value] =  esc_sql($_POST['cf7cloud_custom_field_name'][$key]);	
							//$counter++;
						}
					}
		
					//print_r($the_data); exit;
					update_option( 'CU_cf7cloud_database_data_'.$_POST['post_ID'], $the_data );
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
		  //print_r($wpcf7->posted_data); exit;
		  
		  // get the option for this specific form and see which fields to send to CU API
		  $cf7cloud_data = get_option('CU_cf7cloud_database_data_'.$wpcf7->posted_data['_wpcf7']);
			
		  	// first check if this form has any Analytics associated
		  	if( $cf7cloud_data && is_array($cf7cloud_data) ){
				  
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
							if( array_key_exists($value, $wpcf7->posted_data) ){
								$CU_string .= $key."=".urlencode($wpcf7->posted_data[$value]).'&';
							}
					}
					
					// remove last character
					$CU_string = substr_replace($CU_string ,"",-1);
					//$CU_string = urlencode($CU_string);
			
			        $ch = curl_init();
			
			        $strCURLOPT  = 'http://test.contactus.com/api2.php?';
			        // $strCURLOPT  = 'http://admin.contactus.com/api2.php?';
			        /*$strCURLOPT .= '?API_Account=AC11111f363ae737fb7c60b75dfdcbb306';
			        $strCURLOPT .= '&API_Key=d2f581d0423326195488eb91e6aba907d1e88719';
			        $strCURLOPT .= '&API_Action=getAPICredentials';*/
					
					//$strCURLOPT .= 'API_Account='.CU_API_Account; // constants defined in config.php
			        //$strCURLOPT .= '&API_Key='.CU_API_Key; // constants defined in config.php
			        //$strCURLOPT .= '&API_Action=postSubmitLead';
					//$strCURLOPT .= '&Form_Key='.CU_Form_Key.'&'; // constants defined in config.php
			        
			        $thekey 		= get_option('cUsCloud_settings_form_key');
			        $credentials 	= get_option('cUsCloud_settings_userCredentials');

					$strCURLOPT .= 'API_Account='.$credentials['API_Account']; // constants defined in config.php
			        $strCURLOPT .= '&API_Key='.$credentials['API_Key']; // constants defined in config.php
			        $strCURLOPT .= '&API_Action=postSubmitLead';
					$strCURLOPT .= '&Form_Key='.$thekey.'&'; // constants defined in config.php

					$strCURLOPT = trim($strCURLOPT.$CU_string);
					
					//echo $strCURLOPT; exit;
					
					//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			        curl_setopt($ch, CURLOPT_URL, $strCURLOPT );
			        curl_setopt($ch, CURLOPT_HEADER, 0);
			        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			        $content = curl_exec($ch);
			        curl_close($ch);

			}
			        // return $content;
					// echo $content;
					// exit;

		}

		
} // end class definition

/* CF7 Cloud Database loader  */
$CF7_cloud_loader	= new CF7_cloud_loader();