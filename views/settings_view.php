
<?php 
/**
 * File to display the view in CF7
 * Created: 2013-11-12
 * Company: ContactUs.com
 * Updated: 20131114
 **/
extract($data);
// check if this plugin has been activated and validated with CU , otherwise don't show this error message
$cf7_cloud_activated = get_option('cf7_cloud_database_active');
?>
<script type="text/javascript">
//<![CDATA[	
	var ADMIN_AJAX_URL 	= 	"<?php echo ADMIN_AJAX_URL; ?>";
	var template_url 	= 	"<?php echo get_bloginfo('template_url'); ?>";
//]]>
</script>

<!-- plugin admin header -->
<div class="cf7cloud_logo">
	
	<h1>Contact Form 7 Integrations</h1>
	
  <?php
  // get option if plugin is already activated
  
  if( $cf7_cloud_activated == 1 ){
  	// get the credentials
  	$credentials = get_option('cUsCloud_settings_userCredentials');
  ?>
  	
  	<?php if ( strlen($credentials['API_Key']) && strlen($credentials['API_Account']) ){ ?><a href="<?php echo plugins_url('contact-form-7-integrations/includes/toAdmin.php?iframe&uE='.$credentials['API_Account'].'&uC='.$credentials['API_Key']) ?>" target="_blank" rel="toDash" class="action_orange_button btn">Form Control Panel</a><?php } ?>
  	
  	<!-- <input type="button" value="Go to my Dashboard Analytics" id="gotodashboard" name="gotodashboard" class="action_orange_button " onclick="javascript:window.open('http://admin.contactus.com/')"> -->

  <?php
  }
  ?>

	<a href="http://on.fb.me/HqI1hd" target="_blank" title="Follow Us on Facebook for new product updates"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/facebook_icon.png" width="32" height="34" alt="Facebook" /></a>
	<a href="http://bit.ly/18DW6O4" target="_blank" title="Follow Us on Google+"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/googeplus_icon.png" width="32" height="34" alt="Google+" /></a>
	<a href="http://linkd.in/1ivr9kK" target="_blank" title="Follow Us on LinkedIn"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/linkedin_icon.png" width="32" height="34" alt="Linked In" /></a>
	<!-- <a href="#"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/pinterest_icon.png" width="32" height="34" alt="Pinterest" /></a> -->
	<a href="http://bit.ly/16NxNh5" target="_blank" title="Follow Us on Twitter"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/twitter_icon.png" width="32" height="34" alt="Twitter" /></a>
	<a href="http://bit.ly/1dPwnub" target="_blank" title="Find tutorials on our Youtube channel"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/youtube_icon.png" width="32" height="34" alt="Youtube" /></a>
</div>
<!-- / plugin admin header -->

<div id="container_cf7clouddatabase">

<!-- left side container -->
<div id="CUintegrations_toleft">
	
	
	<?php
	  // get current user logged in info.
	  // global $current_user;
      // get_currentuserinfo();
	?>

<?php
// if this user has ContactUs.com data in DB don't show buttons.

    // get the custom data for this contact form
    // $credentials = get_option('cUsCloud_settings_userCredentials');
    $ukey = get_option('cUsCloud_settings_form_key');
    // print_r( $credentials );
    // echo('<br />');
    // print_r(get_option('cUsCloud_settings_form_key'));
    // print ( $cred['API_Account'] );


	if( !$ukey ){	
	    //echo $cf7_cloud_activated; 
	?>
	<div class="first_step_clouddb">
		
	  <div id="cf7cloud_welcome">
	    <h1>Welcome to Contact Form 7 Integrations</h1>
		<h2>By ContactUs.com</h2>
	    
	    <p class="CU_integrations">
	      <img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/software_integrations_icon.png" width="25" height="24" alt="Software Integrations" /><span>Software Integrations</span>
	      <img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/advanced_analytics_icon.png" width="25" height="24" alt="Software Analytics" /><span>Advanced Analytics</span>
	      <img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/contact_management_icon.png" width="25" height="24" alt="Contact Management" /><span>Contact Management</span>
	    </p>
	    
	    <p>
	Contact Form 7 Integrations by ContactUs.com is an add-on solution for Contact Form 7 users to enhance their contact form capabilities with professional contact management built by ContactUs.com.  Once integrated, you can manage contact form submissions, track analytics and post data to 3rd party software providers.
	</p><br />
	
	<h3>Create your contactus.com account here! <br /> or login and start tracking your results.</h3><br />
	  
	    <button id="cUsCloud_yes" class="btn_clouddb_yes" type="button" ><span>Yes</span> Set Up My Form</button>
	    <button id="cUsCloud_no" class="btn_clouddb_no"><span>No</span> Signup Free Now</button>
	    <br /><br />

	</div>
	    
	</div>
<?php
}else{
  echo('<div class="notice_visible"> <h1>Welcome to Contact Form 7 Integrations</h1>
	<h2>By ContactUs.com</h2>
  <!-- <p>Welcome,Contact Form 7 users!<br /><br />
Contact Form 7 Integrations by ContactUs.com is an add-on solution for ContactForm7 users to enhance their contact form capabilities with professional contact management built by ContactUs.com.  Once integrated, you can manage contact form submissions, track analytics and post data to 3rd party software providers. -->
</p>');
  
 
  
  
  echo('<p>You’re almost done. To continue the setup process, click the “Continue” button below to take you to your Contact Form 7 installation</p>');
  
  
  echo('</p>
  <img src="'.plugins_url().'/contact-form-7-integrations/assets/images/after_linking_account_final.png" width="100%" height="auto" 
  alt="Continue to Configure your Contact Form 7 extension" style="float:left; margin:0px 0px 25px 0px" />
  
   <!-- <div class="CF7cloud_introduction_video">
    <iframe width="350" height="300" src="//www.youtube.com/embed/SEQJZqUT-Hk" frameborder="0" allowfullscreen></iframe>
  </div> -->
  
  
  </div>
  
  <input type="button" class="action_orange_button button_redirect" name="buttoncontinue" id="buttoncontinue" value="Click here to continue" style="float:right;" />
  
  ');
   //echo('');
}
?>

	<div class="loadingMessage"></div>
	<div class="advice_notice">Advices....</div>
	<div class="notice">Ok....</div>

	<!-- this is the login form -->
	<form method="post" action="admin.php?page=cf7-integrations" id="cUsCloud_loginform" name="cUsCloud_loginform" class="steps login_form" onsubmit="return false;">
	    <h3>ContactUs.com Login</h3>
	
	    <table class="form-table">
	        <tr>
	            <th><label class="labelform" for="login_email">Email</label><br>
	            <td><input class="inputform" name="cUsCloud_settings[login_email]" id="login_email" type="text"></td>
	        </tr>
	        <tr>
	            <th><label class="labelform" for="user_pass">Password</label></th>
	            <td><input class="inputform" name="cUsCloud_settings[user_pass]" id="user_pass" type="password"></td>
	        </tr>
	        <tr><th></th>
	            <td>
	                <input id="loginbtn" class="action_orange_button cUsCloud_LoginUser" value="Login" type="submit">
	            </td>
	        </tr>
	        <tr>
	            <th></th>
	            <td>
	                <a href="https://www.contactus.com/client-login.php" target="_blank">I forgot my password</a>
	            </td>
	        </tr>
	    </table>
	</form>
	<!-- / this is the login form -->

	<!-- this is the register form -->
	<form method="post" action="admin.php?page=cUs_form_plugin" id="cUsCloud_userdata" name="cUsCloud_userdata" class="steps step1" onsubmit="return false;">
	    <h3 class="step_title">Register for your ContactUs.com Account</h3>
	
	    <table class="form-table">
	        <tr>
	            <th><label class="labelform" for="cUsCloud_first_name">* First Name</label></th>
	            <td><input type="text" class="inputform text" placeholder="First Name" name="cUsCloud_first_name" id="cUsCloud_first_name" value="<?php echo (isset($_POST['cUsCloud_first_name']) && strlen($_POST['cUsCloud_first_name'])) ? $_POST['cUsCloud_first_name'] : $current_user->user_firstname; ?>" /></td>
	        </tr>
	        <tr>
	            <th><label class="labelform" for="lname">* Last Name</label></th>
	            <td><input type="text" class="inputform text" placeholder="Last Name" name="cUsCloud_last_name" id="cUsCloud_last_name" value="<?php echo (isset($_POST['cUsCloud_last_name']) && strlen($_POST['cUsCloud_last_name'])) ? $_POST['cUsCloud_last_name'] : $current_user->user_lastname; ?>"/></td>
	        </tr>
	        <tr>
	            <th><label class="labelform" for="remail">* Email</label></th>
	            <td><input type="text" class="inputform text" placeholder="Email" name="cUsCloud_email" id="cUsCloud_email" value="<?php echo (isset($_POST['cUsCloud_email']) && strlen($_POST['cUsCloud_email'])) ? $_POST['cUsCloud_email'] : $current_user->user_email; ?>"/></td>
	        </tr>
	        <tr>
	            <th><label class="labelform" for="cUsCloud_web">* Website</label></th>
	            <td><input type="text" class="inputform text" placeholder="Website (http://www.example.com)" name="cUsCloud_web" id="cUsCloud_web" value="http://<?php echo $_SERVER['HTTP_HOST']; ?>"/></td>
	        </tr>
	
	
	        <tr>
	            <th><label class="labelform" for="cUsCloud_pass1">* Password</label></th>
	            <td><input type="password" class="inputform text" placeholder="" name="cUsCloud_pass1" id="cUsCloud_pass1" value="" /></td>
	        </tr>
	
	        <tr>
	            <th><label class="labelform" for="cUsCloud_pass2">* Retype password</label></th>
	            <td><input type="password" class="inputform text" placeholder="" name="cUsCloud_pass2" id="cUsCloud_pass2" value="" /></td>
	        </tr>
	
	
	        <tr>
	            <th></th><td><input id="cUsCloud_CreateCustomer" class="action_orange_button" value="Create Account" type="submit" /></td>
	        </tr>
	        <tr>
	            <td colspan="2" style="text-align:left;"><br />
	            	<strong>By creating a ContactUs.com account, you agree that:</strong> <br />
	            	a) You have read and accepted our <a href="http://www.contactus.com/terms-of-service/" target="_blank">Terms</a> and our <a href="http://www.contactus.com/dmca-policy/" target="_blank">Privacy Policy</a> and<br /> 
	            	b) You may receive communications from ContactUs.com, including new submission notifications.
	            	</td>
	        </tr>
	        
	        <tr>
	            <th></th><td>&nbsp;</td>
	        </tr>
	    </table>
	</form>
	<!-- / this is the register form -->

	<div id="createpostform">
		
		<?php
		$credentials = get_option('cUsCloud_settings_userCredentials');
		?>

	    <table>
	    	<tr>
	    	  <td colspan="2">
	    	  	<div class="no_post_form">
	    	  	  We just checked your <a href="http://admin.contactus.com" target="_blank">ContactUs.com</a> Settings and you do not have a POST form created.<br /> <?php if ( strlen($credentials['API_Key']) && strlen($credentials['API_Account']) ){ ?><a href="<?php echo plugins_url('contact-form-7-integrations/includes/toAdmin.php?iframe&uE='.$credentials['API_Account'].'&uC='.$credentials['API_Key']) ?>" target="_blank" rel="toDash" class=" btn"><strong>Click here</strong></a><?php } ?> to create your post form following the instructions below.<br /><br />
	    	    </div>
	    	  </td>
	    	</tr>
	        <tr>
	          <td valign="top"><img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/create_post_form_instructions.jpg" width="283" height="268" alt="How to Create a POST form in admin.contactus.com" /></td>
	          <td><!-- <iframe width="350" height="300" src="//www.youtube.com/embed/FSO8Jq5n2F0" frameborder="0" allowfullscreen></iframe> -->&nbsp;</td>
	        </tr>
	    </table>
	 
	</div>

</div>
<!-- / left side container -->

<!-- right side container -->
<div id="CUintegrations_toright">

<img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/arrow_5_steps.png" width="39" height="48" alt="5 easy steps" />
<span class="five_easy_steps">5 Steps to Supercharge <br />Contact Form 7</span>


<table class="table_steps">
  <tr>
    <td width="45"><img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/step_one.png" width="31" height="32" alt="Step One" /></td>
  <td>Create a free ContactUs.com account (or login if you already have one)
Once this is done, the plugin will automatically create a new form in your ContactUs.com that will be matched against your first CF7 form
</td>
</tr>

<tr>
 <td><img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/step_two.png" width="31" height="32" alt="Step Two" /></td>
  <td>Open Your Contact Form 7 Installation (which you can do from Contact Form 7 Integrations)
</td>
</tr>

<tr>
 <td><img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/step_three.png" width="31" height="32" alt="Step Three" /></td>
  <td>Choose your Contact Form 7 Form (if you have more than one)
</td>
</tr>

<tr>
 <td><img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/step_four.png" width="31" height="32" alt="Step Four" /></td>
  <td>Map Your Fields between Contact Form 7 and ContactUs.com
</td>
</tr>

<tr>
 <td><img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/step_five.png" width="31" height="32" alt="Step Five" /></td>
  <td>Track Your Results inside <a href="http://admin.contactus.com" target="_blank">ContactUs.com</a>
</td>
</tr>

</table>

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

</div>
<!-- / right side container -->

<!--
<table class="table_steps">
  <tr>
  <td width="45"><strong>Step 1</strong> </td>
  <td>Create a free ContactUs.com account below (or login if you already have one)<br />
          <em>Once this is down, the plugin will automatically create a new form in your ContactUs.com that will be matched against your first CF7 form</em>
</td>
</tr>

<tr>
 <td><strong>Step 2 </strong></td>
  <td>Open Your Contact Form 7 Installation (which you can do from Contact Form 7 Integrations)
</td>
</tr>

<tr>
 <td><strong>Step 3 </strong></td>
  <td>Choose your Contact Form 7 Form (if you have more than one)
</td>
</tr>

<tr>
 <td><strong>Step 4</strong> </td>
  <td>Map Your Fields between Contact Form 7 and ContactUs.com
</td>
</tr>

<tr>
 <td><strong>Step 5 </strong></td>
  <td>Track Your Results inside ContactUs.com
</td>
</tr>

</table>
<br />
-->





