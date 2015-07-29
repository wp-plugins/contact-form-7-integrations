<?php

/*
 * Method to clean and extract CF7 inputs from textarea
 * @params String of data in cf7 form creation interface
 * @since 0.1
 * @returns Array of input names
 */

// loginAlreadyUser handler function...
add_action('wp_ajax_cUsCloud_loginAlreadyUser', 'cUsCloud_loginAlreadyUser_callback');

function cUsCloud_loginAlreadyUser_callback() {
    $cUsCF_api = new cUsComAPI_Cloud();
    $cUs_email = $_REQUEST['email'];
    $cUs_pass = $_REQUEST['pass'];

    $cUsCloud_API_credentials = $cUsCF_api->getAPICredentials($cUs_email, $cUs_pass); //api hook;
    //print_r($cUsCloud_API_credentials);

    if ($cUsCloud_API_credentials) {
        $cUs_json = json_decode($cUsCloud_API_credentials);

        switch ($cUs_json->status) :
            case 'success':

                $cUs_API_Account = $cUs_json->api_account;
                $cUs_API_Key = $cUs_json->api_key;

                if (strlen(trim($cUs_API_Account)) && strlen(trim($cUs_API_Key))) {

                    $aryUserCredentials = array(
                        'API_Account' => $cUs_API_Account,
                        'API_Key' => $cUs_API_Key
                    );
                    //update_option('cUsCloud_settings_userCredentials', $aryUserCredentials);

                    $cUsCF_API_getKeysResult = $cUsCF_api->getFormKeysAPI($cUs_API_Account, $cUs_API_Key); //api hook;

                    $cUs_jsonKeys = json_decode($cUsCF_API_getKeysResult);

                    //print_r( $cUs_jsonKeys ); exit;

                    if ($cUs_jsonKeys->status == 'success') {

                        $postData = array('email' => $cUs_email, 'credential' => $cUs_pass);
                        update_option('cUsCloud_settings_userData', $postData);
                        
                        //print_r($cUs_jsonKeys->data); 
                        //exit;

                        foreach ($cUs_jsonKeys->data as $oForms => $oForm) {
                            if ($oForm->form_type == 'post' && $oForm->default == 1) { //GET DEFAULT POST FORM KEY
                                $defaultFormKey = $oForm->form_key;
                                $form_type = $oForm->form_type;
                                $deeplinkview = $oForm->deep_link_view;
                                //exit;
                                $defaultFormId = $oForm->form_id;
                                update_option('cUsCloud_settings_default_deep_link_view', $deeplinkview); // DEFAULT FORM KEYS
                                //exit;
                            } else {
                                $deeplinkview = $oForm->deep_link_view;
                            }
                        }

                        // check if form with Type 7 is available
                        if (!isset($defaultFormKey) || !strlen($defaultFormKey)) {
                            //echo 2; // no form of type POST/7 is available
                            $aryResponse = array(
                                'status' => 2,
                                'cUs_API_Account' => $cUs_API_Account,
                                'cUs_API_Key' => $cUs_API_Key,
                                'deep_link_view' => $cUsCF_api->parse_deeplink($deeplinkview)
                            );
                        } else {

                            $aryFormOptions = array('tab_user' => 1, 'cus_version' => 'tab'); //DEFAULT SETTINGS / FIRST TIME
                            //update_option('cUsCloud_FORM_settings', $aryFormOptions );//UPDATE FORM SETTINGS
                            update_option('cUsCloud_settings_form_key', $defaultFormKey); //DEFAULT FORM KEYS
                            update_option('cUsCloud_settings_form_id', $defaultFormId); //DEFAULT FORM ID
                            update_option('cUsCloud_settings_form_keys', $cUs_jsonKeys); // ALL FORM KEYS
                            update_option('cf7_cloud_database_active', 1);
                            update_option('cUsCloud_settings_userCredentials', $aryUserCredentials);
                            //update_option('cUsCloud_settings_default_deep_link_view', $deeplinkview); // DEFAULT FORM KEYS

                            $aryResponse = array('status' => 1);
                        }

                        //echo 1;
                    } elseif ($cUs_jsonKeys->error === 'No valid form keys') {
                        $aryResponse = array('status' => 3, 'message' => 'No valid form keys');
                    } else {
                        echo 'Error. . . ';
                    }
                } else {
                    $aryResponse = array('status' => 3, 'message' => $cUs_json->error);
                }

                break;

            case 'error':
                $aryResponse = array('status' => 3, 'message' => $cUs_json->error);
                break;
        endswitch;
    }

    echo json_encode($aryResponse);

    die();
}

// cUsCloud_verifyCustomerEmail handler function...
add_action('wp_ajax_cUsCloud_verifyCustomerEmail', 'cUsCloud_verifyCustomerEmail_callback');

function cUsCloud_verifyCustomerEmail_callback() {

    if (!strlen($_REQUEST['fName'])): echo 'Missing First Name, is required fieldsss!';
        die();
    elseif (!strlen($_REQUEST['lName'])): echo 'Missing Last Name, is required field!';
        die();
    elseif (!strlen($_REQUEST['Email'])): echo 'Missing/Invalid Email, is required field!';
        die();
    elseif (!strlen($_REQUEST['website'])): echo 'Missing Website, is required field!';
        die();
    else:

        $cUsCloud_api = new cUsComAPI_Cloud(); //CONTACTUS.COM API

        $postData = array(
            'fname' => $_REQUEST['fName'],
            'lname' => $_REQUEST['lName'],
            'email' => $_REQUEST['Email'],
            'phone' => filter_input(INPUT_POST, 'Phone', FILTER_SANITIZE_NUMBER_INT),
            'website' => $_REQUEST['website'],
            'password' => $_REQUEST['password'],
            'Main_Category' => $_REQUEST['CU_category'],
            'Sub_Category' => $_REQUEST['CU_subcategory'],
            'Goals' => $_REQUEST['CU_goals']
        );

        $cUsCloud_API_EmailResult = $cUsCloud_api->verifyCustomerEmail($_REQUEST['Email']); //EMAIL VERIFICATION
        if ($cUsCloud_API_EmailResult) :
            $cUsCloud_jsonEmail = json_decode($cUsCloud_API_EmailResult);

            switch ($cUsCloud_jsonEmail->result):
                case 0 :
                    //echo 'No Existe';

                    update_option('cUsCloud_settings_userData', $postData);

                    // if one register was successful
                    if (cUsCloud_createCustomer_callback() == 1) {
                        update_option('cf7_cloud_database_active', 1);
                        echo 1;
                    }

                    break;

                case 1 :
                    //echo 'Existe';
                    echo 2; //ALREDY CUS USER
                    delete_option('cUsCloud_settings_userData');
                    break;
            endswitch;
        else:
            echo 'Ouch! unfortunately there has being an error during the application, please try again';
            exit();
        endif;

    endif;

    die();
}

// cUsCF_createCustomer handler function...
add_action('wp_ajax_cUsCloud_createCustomer', 'cUsCloud_createCustomer_callback');

function cUsCloud_createCustomer_callback() {

    $cUsCloud_userData = get_option('cUsCloud_settings_userData'); //get the saved user data

    if (!strlen($cUsCloud_userData['fname'])): echo 'Missing First Name, is required fieldsss!';
        die();
    elseif (!strlen($cUsCloud_userData['lname'])): echo 'Missing Last Name, is required field!';
        die();
    elseif (!strlen($cUsCloud_userData['email'])): echo 'Missing/Invalid Email, is required field!';
        die();
    elseif (!strlen($cUsCloud_userData['website'])): echo 'Missing Website, is required field!';
        die();
    /* elseif  ( !strlen($_REQUEST['Template_Desktop_Form']) ):    echo 'Missing Form Template!';         die();
      elseif  ( !strlen($_REQUEST['Template_Desktop_Tab']) ):    echo 'Missing Tab Template!';         die(); */
    else:

        $cUsCF_api = new cUsComAPI_Cloud(); //CONTACTUS.COM API

        $postData = array(
            'fname' => $cUsCloud_userData['fname'],
            'lname' => $cUsCloud_userData['lname'],
            'email' => $cUsCloud_userData['email'],
            'phone' => preg_replace('/[^0-9]+/i', '', $cUsCloud_userData['phone']),
            'website' => $cUsCloud_userData['website'],
            'password' => $cUsCloud_userData['password'],
            'Main_Category' => $cUsCloud_userData['Main_Category'],
            'Sub_Category' => $cUsCloud_userData['Sub_Category'],
            'Goals' => $cUsCloud_userData['Goals']
        );

        $cUsCloud_API_result = $cUsCF_api->createCustomer($postData);

        if ($cUsCloud_API_result) :

            $cUs_json = json_decode($cUsCloud_API_result);

            switch ($cUs_json->status) :

                case 'success':
                    //echo 1;//GREAT
                    update_option('cUsCloud_settings_form_key', $cUs_json->form_key); //finally get form key form contactus.com // SESSION IN
                    $aryFormOptions = array(//DEFAULT SETTINGS / FIRST TIME
                        'tab_user' => 1,
                        'cus_version' => 'post'
                    );
                    update_option('cUsCloud_FORM_settings', $aryFormOptions); //UPDATE FORM SETTINGS
                    update_option('cUsCloud_settings_userData', $postData);

                    $cUs_API_Account = $cUs_json->api_account;
                    $cUs_API_Key = $cUs_json->api_key;

                    $aryUserCredentials = array(
                        'API_Account' => $cUs_API_Account,
                        'API_Key' => $cUs_API_Key
                    );
                    update_option('cUsCloud_settings_userCredentials', $aryUserCredentials);

                    // ********************************
                    // get here the default deeplink after creating customer
                    $cUsAPI_getKeysResult = $cUsCF_api->getFormKeysData($cUs_API_Account, $cUs_API_Key); //api hook;

                    $cUs_jsonKeys = json_decode($cUsAPI_getKeysResult);
                    $cUs_deeplinkview = $cUsCF_api->get_deeplink($cUs_jsonKeys->data);
                    // get the default contact form deeplink
                    if (strlen($cUs_deeplinkview)) {
                        update_option('cUsCloud_settings_default_deep_link_view', $cUs_deeplinkview); // DEFAULT FORM KEYS
                    }


                    return 1;

                    break;

                case 'error':

                    if ($cUs_json->error[0] == 'Email exists'):
                        echo 2; //ALREDY CUS USER
                    //$cUsCF_api->resetData(); //RESET DATA
                    else:
                        //ANY ERROR
                        echo $cUs_json->error;
                    //$cUsCF_api->resetData(); //RESET DATA
                    endif;
                    break;


            endswitch;
        else:
            //echo 3;//API ERROR
            echo $cUs_json->error;
        // $cUsCF_api->resetData(); //RESET DATA
        endif;


    endif;

    die();
}

// logoutUser handler function...
/*
 * Method in charge to remove wp options saved with this plugin via ajax post request vars
 * @since 1.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsCloud_logoutUser', 'cUsCloud_logoutUser_callback');

function cUsCloud_logoutUser_callback() {

    $cUsCF_api = new cUsComAPI_Cloud();
    $cUsCF_api->resetData(); //RESET DATA

    delete_option('cUsCloud_settings_form_key');
    delete_option('cUsCloud_settings_form_keys');
    delete_option('cf7_cloud_database_active');
    delete_option('cUsCloud_settings_userCredentials');
    delete_option('cUsCloud_settings_default_deep_link_view');

    echo 'Deleted.... User data'; //none list

    die();
}

/* end file ajax_response.php */