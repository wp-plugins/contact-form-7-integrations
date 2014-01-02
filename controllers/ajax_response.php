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
    
    if($cUsCloud_API_credentials){
        $cUs_json = json_decode($cUsCloud_API_credentials);
        
        switch ( $cUs_json->status  ) :
            case 'success':

                $cUs_API_Account    = $cUs_json->api_account;
                $cUs_API_Key        = $cUs_json->api_key;
                
                if(strlen(trim($cUs_API_Account)) && strlen(trim($cUs_API_Key))):
                    
                    $aryUserCredentials = array(
                        'API_Account' => $cUs_API_Account,
                        'API_Key'     => $cUs_API_Key
                    );
                    update_option('cUsCloud_settings_userCredentials', $aryUserCredentials);
                    
                    $cUsCF_API_getKeysResult = $cUsCF_api->getFormKeysAPI($cUs_API_Account, $cUs_API_Key); //api hook;

                    $cUs_jsonKeys = json_decode($cUsCF_API_getKeysResult);
                
                    //print_r( $cUs_jsonKeys ); exit;

                    if($cUs_jsonKeys->status == 'success' ):
                        
                        $postData = array( 'email' => $cUs_email, 'credential'    => $cUs_pass);
                        update_option('cUsCloud_settings_userData', $postData);
                        
                        foreach ($cUs_jsonKeys->data as $oForms => $oForm) {
                            if ( $oForm->form_type == 'post' && $oForm->default == 1 ){ //GET DEFAULT POST FORM KEY
                               $defaultFormKey = $oForm->form_key;
                            }
                        }
                            
                        // check if form with Type 7 is available
                        if( !isset($defaultFormKey) || !strlen($defaultFormKey) ){
                            echo 2; // no form of type POST/7 is available
                        }else{
                            
                            $aryFormOptions = array('tab_user' => 1,'cus_version' => 'tab'); //DEFAULT SETTINGS / FIRST TIME
                            
                            //update_option('cUsCloud_FORM_settings', $aryFormOptions );//UPDATE FORM SETTINGS
                            update_option('cUsCloud_settings_form_key', $defaultFormKey);//DEFAULT FORM KEYS
                            update_option('cUsCloud_settings_form_keys', $cUs_jsonKeys); // ALL FORM KEYS
                            update_option('cf7_cloud_database_active', 1);
                            
                            echo 1; 
                            
                        }

                            //echo 1;
                        
                    else:
                        echo 'Error. . . ';
                    endif;
                    
                else:
                    echo 'Error. . . ';
                endif;
                
                break;

            case 'error':
                echo $cUs_json->error;
                //$cUsCF_api->resetData(); //RESET DATA
                break;
        endswitch;
    }
    
    die();
}


// cUsCloud_verifyCustomerEmail handler function...
add_action('wp_ajax_cUsCloud_verifyCustomerEmail', 'cUsCloud_verifyCustomerEmail_callback');
function cUsCloud_verifyCustomerEmail_callback() {
    
    if      ( !strlen($_REQUEST['fName']) ):      echo 'Missing First Name, is required fieldsss!';      die();
    elseif  ( !strlen($_REQUEST['lName']) ):      echo 'Missing Last Name, is required field!';       die();
    elseif  ( !strlen($_REQUEST['Email']) ):      echo 'Missing/Invalid Email, is required field!';   die();
    elseif  ( !strlen($_REQUEST['website']) ):    echo 'Missing Website, is required field!';         die();
    else:
        
        $cUsCloud_api = new cUsComAPI_Cloud(); //CONTACTUS.COM API
        
        $postData = array(
            'fname' => $_REQUEST['fName'],
            'lname' => $_REQUEST['lName'],
            'email' => $_REQUEST['Email'],
            'website' => $_REQUEST['website'],
            'password' => $_REQUEST['password']
        );

        $cUsCloud_API_EmailResult = $cUsCloud_api->verifyCustomerEmail($_REQUEST['Email']); //EMAIL VERIFICATION
        if($cUsCloud_API_EmailResult) :
            $cUsCloud_jsonEmail = json_decode($cUsCloud_API_EmailResult);
            
            switch ($cUsCloud_jsonEmail->result):
                case 0 :
                    //echo 'No Existe';
                    
                    update_option('cUsCloud_settings_userData', $postData);
                    
                    // if one register was successful
                    if( cUsCloud_createCustomer_callback() == 1){
                        update_option('cf7_cloud_database_active', 1);
                        echo 1;
                    }
                         
                    break;

                case 1 :
                    //echo 'Existe';
                    echo 2;//ALREDY CUS USER
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
    
    if      ( !strlen($cUsCloud_userData['fname']) ):      echo 'Missing First Name, is required fieldsss!';      die();
    elseif  ( !strlen($cUsCloud_userData['lname']) ):      echo 'Missing Last Name, is required field!';       die();
    elseif  ( !strlen($cUsCloud_userData['email']) ):      echo 'Missing/Invalid Email, is required field!';   die();
    elseif  ( !strlen($cUsCloud_userData['website']) ):    echo 'Missing Website, is required field!';         die();
    /*elseif  ( !strlen($_REQUEST['Template_Desktop_Form']) ):    echo 'Missing Form Template!';         die();
    elseif  ( !strlen($_REQUEST['Template_Desktop_Tab']) ):    echo 'Missing Tab Template!';         die();*/
    else:
        
        $cUsCF_api = new cUsComAPI_Cloud(); //CONTACTUS.COM API
        
        $postData = array(
            'fname' => $cUsCloud_userData['fname'],
            'lname' => $cUsCloud_userData['lname'],
            'email' => $cUsCloud_userData['email'],
            'website' => $cUsCloud_userData['website'],
            'password' => $cUsCloud_userData['password']  // EBE modified
            /*'Template_Desktop_Form' => $_REQUEST['Template_Desktop_Form'],
            'Template_Desktop_Tab' => $_REQUEST['Template_Desktop_Tab']*/
        );
        
        $cUsCloud_API_result = $cUsCF_api->createCustomer($postData);

        if($cUsCloud_API_result) :

            $cUs_json = json_decode($cUsCloud_API_result);

            switch ( $cUs_json->status  ) :

                case 'success':
                    //echo 1;//GREAT
                    update_option('cUsCloud_settings_form_key', $cUs_json->form_key ); //finally get form key form contactus.com // SESSION IN
                    $aryFormOptions = array( //DEFAULT SETTINGS / FIRST TIME
                        'tab_user'          => 1,
                        'cus_version'       => 'post'
                    ); 
                    update_option('cUsCloud_FORM_settings', $aryFormOptions );//UPDATE FORM SETTINGS
                    update_option('cUsCloud_settings_userData', $postData);
                    
                    $cUs_API_Account    = $cUs_json->api_account;
                    $cUs_API_Key        = $cUs_json->api_key;
                    
                    $aryUserCredentials = array(
                        'API_Account' => $cUs_API_Account,
                        'API_Key'     => $cUs_API_Key
                    );
                    update_option('cUsCloud_settings_userCredentials', $aryUserCredentials);
                    return 1;

                break;

                case 'error':

                    if($cUs_json->error[0] == 'Email exists'):
                        echo 2;//ALREDY CUS USER
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




/* end file ajax_response.php */