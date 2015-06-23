
<?php
/**
 * File to display the view in CF7
 * Created: 2013-11-12
 * Company: ContactUs.com
 * Updated: 20140206
 * */
extract($data);
// check if this plugin has been activated and validated with CU , otherwise don't show this error message
$cf7_cloud_activated = get_option('cf7_cloud_database_active');
?>
<script>
//<![CDATA[	
    var ADMIN_AJAX_URL = "<?php echo ADMIN_AJAX_URL; ?>";
    var template_url = "<?php echo get_bloginfo('template_url'); ?>";
//]]>
</script>

<!-- plugin admin header -->
<div class="cf7cloud_logo">

    <h1>Contact Form 7 Integrations</h1>

    <?php
    // get option if plugin is already activated
    
    $cUsComAPI_Cloud = new cUsComAPI_Cloud();

    if ($cf7_cloud_activated == 1) {
        // get the credentials
        $credentials = get_option('cUsCloud_settings_userCredentials');
        $cUs_API_Account    = $credentials['API_Account'];
        $cUs_API_Key        = $credentials['API_Key'];
        $cus_par_url = 'https://admin.contactus.com/partners';
        $default_deep_link  = get_option('cUsCloud_settings_default_deep_link_view');
        $defaultFormId  = get_option('cUsCloud_settings_form_id');
        
        if(!strlen($default_deep_link)){
            $cUsAPI_getFormKeys = $cUsComAPI_Cloud->getFormKeysAPI($cUs_API_Account, $cUs_API_Key); //api hook;
            $default_deep_link = $cUsComAPI_Cloud->getDefaultDeepLink($cUsAPI_getFormKeys);
            update_option('cUsCloud_settings_default_deep_link_view', $default_deep_link); // DEFAULT FORM KEYS
        }
        
        if(!strlen($defaultFormId)){
            $cUsAPI_getFormKeys = $cUsComAPI_Cloud->getFormKeysAPI($cUs_API_Account, $cUs_API_Key); //api hook;
            $defaultFormId = $cUsComAPI_Cloud->getDefaultFormID($cUsAPI_getFormKeys);
            update_option('cUsCloud_settings_form_id', $defaultFormId); // DEFAULT FORM KEYS
        }
        
        $partnerID = $cUsComAPI_Cloud->get_partner_id($default_deep_link);
        $cus_CRED_url = $cus_par_url . '/index.php?loginName='.$cUs_API_Account.'&userPsswd='.urlencode($cUs_API_Key);
        ?>

        <?php if (strlen($credentials['API_Key']) && strlen($credentials['API_Account'])) { ?><a href="<?php echo $cus_CRED_url; ?>&confirmed=1" target="_blank" rel="toDash" class="action_orange_button btn">Form Control Panel</a><?php } ?>

        <?php
    }
    ?>

    <a href="https://www.facebook.com/ContactUscom" target="_blank" title="Follow Us on Facebook for new product updates"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/facebook_icon.png" width="32" height="34" alt="Facebook" /></a>
    <a href="https://plus.google.com/117416697174145120376" target="_blank" title="Follow Us on Google+"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/googeplus_icon.png" width="32" height="34" alt="Google+" /></a>
    <a href="http://www.linkedin.com/company/2882043" target="_blank" title="Follow Us on LinkedIn"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/linkedin_icon.png" width="32" height="34" alt="Linked In" /></a>
    <a href="https://twitter.com/ContactUsCom" target="_blank" title="Follow Us on Twitter"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/twitter_icon.png" width="32" height="34" alt="Twitter" /></a>
    <a href="http://www.youtube.com/user/ContactUsCom" target="_blank" title="Find tutorials on our Youtube channel"><img src="<?php echo plugins_url() ?>/contact-form-7-integrations/assets/images/youtube_icon.png" width="32" height="34" alt="Youtube" /></a>
</div>
<!-- / plugin admin header -->

<div id="container_cf7clouddatabase">

    <?php
    if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        //echo('Contact Form 7 is installed and active!');	  
        ?>

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

            if (!$ukey) {
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
            } else {
                echo('<div class="notice_visible"> <h1>Welcome to Contact Form 7 Integrations</h1>
	<h2>By ContactUs.com</h2>
  <!-- <p>Welcome,Contact Form 7 users!<br /><br />
Contact Form 7 Integrations by ContactUs.com is an add-on solution for ContactForm7 users to enhance their contact form capabilities with professional contact management built by ContactUs.com.  Once integrated, you can manage contact form submissions, track analytics and post data to 3rd party software providers. -->
</p>');




                echo('<p>You’re almost done. To continue the setup process, click the “Continue” button below to take you to your Contact Form 7 installation</p>
  <ul class="steps"><li><p><strong>1)</strong> Go to Contact Form 7&#39;s admin panel and click on the forms you want to integrate to ContactUs.com</p></li>
  <li><p><strong>2)</strong> Once you are editing your form, check the Turn On Contact Form 7 Integrations tab and click on the Map Fields button</p></li>
  </ul>
  ');


                echo('</p>
  <img src="' . plugins_url() . '/contact-form-7-integrations/assets/images/after_linking_account_final.png" width="100%" height="auto" 
  alt="Continue to Configure your Contact Form 7 extension" style="float:left; margin:0px 0px 25px 0px" />
  
   <!-- <div class="CF7cloud_introduction_video">
    <iframe width="350" height="300" src="//www.youtube.com/embed/SEQJZqUT-Hk" frameborder="0" allowfullscreen></iframe>
  </div> -->
  
  
  </div>
  
  <input type="button" class="action_orange_button_spe button_redirect" name="buttoncontinue" id="buttoncontinue" value="Click here to continue" style="float:right;" />
  
  ');
            }
            ?>
            
             <?php
            global $current_user;
            get_currentuserinfo();
            ?>	

            <div class="loadingMessage"></div><div class="advice_notice">Advices....</div><div class="notice">Ok....</div>

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
                        <td colspan="2">
                            <p class="advice">
                                If you created an account by signing up with Facebook, you probably don’t know your password. Please click here to request a new one. <br/>
                                <a href="http://www.contactus.com/login/#forgottenbox" target="_blank">I forgot my password</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </form>
            <!-- / this is the login form -->

            <!-- this is the register form -->
            <form method="post" action="admin.php?page=cUs_form_plugin" id="cUsCloud_userdata" name="cUsCloud_userdata" class="steps step1" onsubmit="return false;">
                <a name="signupform"></a>
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
                        <th><label class="labelform" for="remail"> Phone</label></th>
                        <td><input type="text" class="inputform text" placeholder="Phone (optional)" name="cUsCloud_phone" id="cUsCloud_phone" value=""/></td>
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
                        <th></th><td><input id="cUsCloud_CreateCustomer" href="#cats_selection" class="action_orange_button" value="Create Account" type="submit" /></td>
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

            <!-- CATS SUBCATS AND GOALS -->
            <div id="cats_container" style="display:none;">

                <div id="cats_selection">
                    <div class="loadingMessage"></div><div class="advice_notice">Advices....</div><div class="notice">Ok....</div>
                    <form action="/">

                        <div id="customer-categories-box" class="questions-box">

                            <div class="cc-headline">Hi <?php echo $current_user->user_login; ?></div>
                            <img src="<?php echo plugins_url('../assets/images/contactus-users.png', __FILE__); ?>" class="user-graphic">
                            <div class="cc-message">We’re working on new ways to personalize your account</div>
                            <div class="cc-message-small">Please take 7 seconds to tell us about your website, which helps us identify the best tools for your needs:</div>

                            <h4 class="cc-title" id="category-message">Select the Category of Your Website:</h4>


                            <?php
                            /*
                             * GET CATEGORIES AND SUBCATEGORIES
                             */

                            

                            $aryCategoriesAndSub = $cUsComAPI_Cloud->get_categories();

                            if (is_array($aryCategoriesAndSub)) {
                                ?>
                                <ul id="customer-categories">
                                    <?php foreach ($aryCategoriesAndSub as $category => $arySubs) { ?>

                                        <li class="parent-category"><span data-maincat="<?php echo $category; ?>" id="<?php echo str_replace(' ', '-', $category); ?>" class="parent-title"><?php echo trim($category); ?></span>
                                            <?php if (is_array($arySubs)) { ?>
                                                <ul class="sub-category">
                                                    <?php foreach ($arySubs as $Sub) { ?>
                                                        <li data-subcat="<?php echo $Sub; ?>"><span><?php echo trim($Sub); ?></span></li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </li>

                                    <?php } ?>
                                </ul>
                            <?php } ?>

                            </ul>

                            <div class="int-navigation">
                                <div id="skip-button" class="next btn btn-skip skip-button">Skip</div>
                                <img src="<?php echo plugins_url('../assets/images/ajax-loader.gif', __FILE__); ?>" width="16" height="16" alt="Loading . . ." style="display:none; vertical-align:middle;" class="img_loader" />
                                <div class="next btn unactive" id="open-intestes">Next Question</div>
                            </div>

                        </div>	

                        <div id="user-interests-box" class="questions-box">
                            <div class="cc-headline">Hi <?php echo $current_user->user_login; ?></div>
                            <div class="cc-message">What are your goals for your ContactUs.com form?</div>

                            <?php
                            /*
                             * GET GOALS
                             */
                            $aryGoals = $cUsComAPI_Cloud->get_goals();

                            if (is_array($aryGoals)) {
                                ?>
                                <ul id="user-interests">
                                    <?php foreach ($aryGoals as $Goal) { ?>
                                        <li data-goals="<?php echo trim($Goal); ?>" <?php if ($Goal === 'Other') { ?>id="other"<?php } ?>><span <?php if (strpos($Goal, 'to my email') !== false) { ?> class="grey" <?php } ?>><?php echo trim($Goal); ?></span></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>



                            <div id="other-interest">Please tell us <input type="text" name="other" id="other_goal" value="" /></div>

                            <div class="int-navigation">
                                <div class="next btn btn-skip skip-button">Skip</div>
                                <div class="next btn unactive btn-skip" id="save">Save Preferences</div>
                                <img src="<?php echo plugins_url('../assets/images/ajax-loader.gif', __FILE__); ?>" width="16" height="16" alt="Loading . . ." style="display:none; vertical-align:middle;" class="img_loader" />
                            </div>

                        </div>

                        <!-- input the category and subcategory data -->
                        <input type="hidden" value="" name="CU_category" id="CU_category" />
                        <input type="hidden" value="" name="CU_subcategory" id="CU_subcategory" />

                        <div id="goals_added">

                        </div>

                    </form>
                    <br /><br /><br />
                </div>


            </div>
            <!-- / CATS SUBCATS AND GOALS -->

            <div id="createpostform">

                <?php
                $credentials = get_option('cUsCloud_settings_userCredentials');
                $cUs_API_Account = $credentials['API_Account'];
                $cUs_API_Key = $credentials['API_Key'];
                $cus_CRED_url = $cus_par_url . '/index.php?loginName=' . $cUs_API_Account . '&userPsswd=' . urlencode($cUs_API_Key);

                ?>

                <table>
                    <tr>
                        <td colspan="2">
                            <div class="no_post_form">
                                We just checked your <a href="http://admin.contactus.com" target="_blank">ContactUs.com</a> Settings and you do not have a POST form created.<br /> <?php if (strlen($credentials['API_Key']) && strlen($credentials['API_Account'])) { ?><a href="<?php echo $cus_CRED_url; ?>&confirmed=1" target="_blank" rel="toDash" class=" btn"><strong>Click here</strong></a><?php } ?> to create your post form following the instructions below.<br /><br />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><img src="<?php echo plugins_url(); ?>/contact-form-7-integrations/assets/images/create_post_form_instructions.jpg" width="283" height="268" alt="How to Create a POST form in admin.contactus.com" /></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>

            </div>
            
            <?php if (empty($cUs_API_Account)) { ?>
            <div class="contaus_features">
                <div class="head_title"><h2>What Do You Get With a ContactUs.com Account?</h2></div>
                <div class="road_features">
                    <div class="_row r1">
                        <div class="_col"><a class="feature arrow_start setLabels" href="http://www.contactus.com/product-tour/" target="_blank" title="Benefit from our customer acquistion tools. Start by creating a custom Contact Form."></a></div>
                        <div class="_col"><a class="feature contacts setLabels" href="http://www.contactus.com/contact-management/" target="_blank" title="Manage and email leads, sync data, track analytics, and much more."></a></div>
                        <div class="_col"><a class="feature contactform setLabels" href="http://www.contactus.com/custom-form-builder/" target="_blank" title="Drag and Drop Editor: Easily customize fields, layout, colors and calls to action."></a></div>
                        <div class="_col"><a class="feature wp setLabels" href="http://www.contactus.com/wordpress-plugins/" target="_blank" title="The ContactUs.com plugins are the best way to add contact forms, newsletter opt-ins, and PayPal forms on your WordPress powered website."></a></div>
                    </div>
                    <div class="_row r2">
                        <div class="_col"></div>
                        <div class="_col"></div>
                        <div class="_col"></div>
                        <div class="_col"><a class="feature tracking setLabels" href="http://www.contactus.com/phone-call-tracking/" target="_blank" title="Get a unique phone number, track inbound calls & optimize your sales process."></a></div>
                    </div>
                    <div class="_row r3">
                        <div class="_col"><a class="feature chat setLabels" href="http://www.contactus.com/contactus-chat/" target="_blank" title="Enagage website visitors, increase sales, and provide better customer support."></a></div>
                        <div class="_col"><a class="feature _3rd setLabels" href="http://www.contactus.com/3rd-party-software-integrations/" target="_blank" title="Tons of 3rd-Party Integrations to sync your data."></a></div>
                        <div class="_col"><a class="feature ab setLabels" href="http://www.contactus.com/conversion-optimization-tools/" target="_blank" title="We've made it easy to optimize your forms and test new variations."></a></div>
                        <div class="_col"><a class="feature leadalerts setLabels" href="http://www.contactus.com/returning-lead-alerts" target="_blank" title="Create Time-Aware opportunities with our tracking cookie."></a></div>
                    </div>
                    <div class="_row r4">
                        <div class="_col"><a class="feature loadforms setLabels" href="http://www.contactus.com/smart-triggers/" target="_blank" title="Page load triggers, exit intent triggers, and hyperlink triggers are cutting edge."></a></div>
                        <div class="_col"></div>
                        <div class="_col"></div>
                        <div class="_col"></div>
                    </div>
                    <div class="_row r5">
                        <div class="_col"><a class="feature analytics setLabels" href="http://www.contactus.com/reports-analytics/" target="_blank" title="Track conversions, conduct A/B tests, and sync with 3rd party tools."></a></div>
                        <div class="_col"><a class="feature customizable setLabels" href="http://www.contactus.com/product-tour/#five-types" target="_blank" title="Traditional Contact Forms, an Appointment Scheduler with calendar sync, Newsletter & Opt-in Forms, Payments & Donations, and Custom Field Forms"></a></div>
                        <div class="_col"></div>
                        <div class="_col"></div>
                    </div>
                    <div class="_row r6">
                        <div class="_col"></div>
                        <div class="_col"></div>
                        <div class="_col"></div>
                        <div class="_col"><a id="cUsCf7i_signup_cloud" class="feature cloud setLabels" href="#signupform"></a></div>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>
        <!-- / left side container -->

        <!-- right side container -->
        <div id="CUintegrations_toright">

            <?php
            $aryAds = array( 'very-cool.png', 'very-cool-2.jpg');
            shuffle($aryAds);
            ?>

            <?php if (!empty($cUs_API_Account)) { ?>
                <div class="premium_chat">
                    <a href="<?php echo $cus_CRED_url; ?>&confirmed=1&redir_url=<?php echo urlencode($cus_par_url . '/'.$partnerID); ?> /en/plans/confirm/premium-monthly-14-day-trial" target="_blank">
                        <img src="<?php echo plugins_url('assets/images/' . $aryAds[0], dirname(__FILE__) ) ;  ?>" width="100%" height="auto">
                    </a>
                </div>
            <?php } ?>


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
                <?php if (!empty($cUs_API_Account)) { ?>
                <hr/>
                <br/>
                <a href="javascript:;" class="LogoutUser action_orange_button"><strong>Unlink Account</strong></a>
                <?php } ?>
            </div>

        </div>
        <!-- / right side container -->
        <?php
    } else {
        ?>

        <div id="CUintegrations_toleft_wide">
            <h1><img src="<?php echo plugins_url('../assets/images/engranaje.png', __FILE__); ?>" width="42" height="55" alt="ContactUs.com" />It seems you don’t have Contact Form 7 (CF7) installed</h1>
            <p style="margin:-20px 0px 10px 52px;">Contact Form 7 Integrations by ContactUs.com is an extension for the CF7 plugin to integrate with ContactUs.com and other supported third-party software. It requires an installation of CF7 to work. 
            </p>

            <p><h2>If you are looking for a standalone contact form solution, we recommend the all-in-one Contact Form by ContactUs.com</h2></p>

            <div id="buttons_container">

                <div class="cf7i_buttons">
                    <a id="install_cf" href="http://wordpress.org/plugins/contactuscom/" target="_blank">Learn More about Contact Form by ContactUs.com</a>
                </div>

                <div class="cf7i_buttons">
                    <a id="install_cf7" class="thickbox" href="<?php echo get_bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=contactuscom&TB_iframe=true&width=640&height=565">Install Contact Form by ContactUs.com </a>
                    <p style="font-size:0.9em; display:inline-block; float:left; clear:right; margin:0px; width:100%;">*This will install the ContactUs Contact Form plugin from wordpress.org</p>
                </div>

            </div>

            <p>Contact Form by ContactUs.com is a popular, hosted contact form solution that inserts forms that you create within the ContactUs.com form builder onto your WordPress website.  In addition to the form, it features callout tabs to increase form submissions, has 20+ built-in software integrations, offers professionally designed templates, and brings with it lots of customization options.  With the WordPress plugin, it supports easy, code-free implementation, as well as placement of short-codes.
            </p>

            <p class="cf7_download_link">To download <strong>"Contact Form 7"</strong> instead, <a class="thickbox" href="<?php echo get_bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=Contact-Form-7&TB_iframe=true&width=640&height=565">click here</a></p>

            <p class="cf7_download_link">(Once you install "Contact form 7", you can click on <strong>"Contact Form 7 integrations"</strong> settings and continue Contact Form 7 integrations setup).</p>

        </div>

        <!-- <div id="CUintegrations_toright2"></div> -->

        <?php
    } // end else that check if Contact Form 7 is active.
    ?>
</div>
