/**
 * Contains all the script to be used in the CF7 cloud Database plugin
 * Updated: 20130927
 * Version : 0.1.1
 **/

	jQuery(document).ready(function(){
		
		var cUsCloud_myjq = jQuery.noConflict();
		
		cUsCloud_myjq('.button_redirect').live('click', function(){
			window.location.href = ADMIN_AJAX_URL+"admin.php?page=wpcf7";
		});

		cUsCloud_myjq('#cUsCloud_yes').click(function() {
	        cUsCloud_myjq('#cUsCloud_userdata').delay(100).fadeOut();
	        cUsCloud_myjq('#cUsCloud_yes').attr('disabled', false );
	        cUsCloud_myjq('#cUsCloud_loginform').slideDown('slow');
	    	    
	    });

	    cUsCloud_myjq('#cUsCloud_no').click(function() {
	    	cUsCloud_myjq('.advice_notice').delay(100).fadeOut();
	    	cUsCloud_myjq('#createpostform').delay(100).fadeOut();
	        cUsCloud_myjq('#cUsCloud_loginform').delay(100).fadeOut();
	        cUsCloud_myjq('#cUsCloud_userdata').slideDown('slow');
	    });

	    function checkRegexp( o, regexp, n ) {
	        if ( !( regexp.test( o ) ) ) {
	            return false;
	        } else {
	            return true;
	        }
	    }

        function checkURL(url) {
            return /^(ht|f)tps?:\/\/[a-z0-9-\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?$/.test(url);
        }

        function str_clean(str){
           
            str = str.replace("'" , " ");
            str = str.replace("," , "");
            str = str.replace("\"" , "");
            str = str.replace("/" , "");

            return str;
        }

	
	cUsCloud_myjq("#cUsCloud_CreateCustomer").on('click', function(){
	
    	 var postData = {};

            var cUsCloud_first_name = cUsCloud_myjq('#cUsCloud_first_name').val();
            var cUsCloud_last_name = cUsCloud_myjq('#cUsCloud_last_name').val();
            var cUsCloud_email = cUsCloud_myjq('#cUsCloud_email').val();
            var cUsCloud_emailValid = checkRegexp( cUsCloud_email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. sergio@jquery.com" );
            var cUsCloud_web = cUsCloud_myjq('#cUsCloud_web').val();
            var cUsCloud_webValid = checkURL(cUsCloud_web);

            // get the passwords
            var pass1 = cUsCloud_myjq('#cUsCloud_pass1').val();
            var pass2 = cUsCloud_myjq('#cUsCloud_pass2').val();
            
            
            cUsCloud_myjq('.loadingMessage').show();
           
           if( String(pass1) != String(pass2) ){
               cUsCloud_myjq('.advice_notice').html('Check your password must be equal in both fields.').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_pass1').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(pass1.length < 8){
               cUsCloud_myjq('.advice_notice').html('Password must be 8 characters or more!').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_pass1').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if( !cUsCloud_first_name.length){
               cUsCloud_myjq('.advice_notice').html('Your First Name is a required field').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_first_name').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if( !cUsCloud_last_name.length){
               cUsCloud_myjq('.advice_notice').html('Your Last Name is a required field').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_last_name').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_email.length){
               cUsCloud_myjq('.advice_notice').html('Email is a required field!').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#apikey').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_emailValid){
               cUsCloud_myjq('.advice_notice').html('Please, enter a valid Email').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_email').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_web.length){
               cUsCloud_myjq('.advice_notice').html('Your Website is a required field').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_web').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_webValid){
               cUsCloud_myjq('.advice_notice').html('Please, enter one valid website URL').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_web').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else{
			   cUsCloud_myjq("#cUsCloud_CreateCustomer").colorbox({inline:true, maxWidth:'100%', minHeight:'430px', scrolling:false });	
		   }
    });
	
	
	
	
	
    //Try to register this new user.
    try{
        cUsCloud_myjq('.btn-skip').click(function() {
            
            var postData = {};

            var cUsCloud_first_name = cUsCloud_myjq('#cUsCloud_first_name').val();
            var cUsCloud_last_name = cUsCloud_myjq('#cUsCloud_last_name').val();
            var cUsCloud_email = cUsCloud_myjq('#cUsCloud_email').val();
            var cUsCloud_emailValid = checkRegexp( cUsCloud_email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. sergio@jquery.com" );
            var cUsCloud_web = cUsCloud_myjq('#cUsCloud_web').val();
            var cUsCloud_webValid = checkURL(cUsCloud_web);

            // get the passwords
            var pass1 = cUsCloud_myjq('#cUsCloud_pass1').val();
            var pass2 = cUsCloud_myjq('#cUsCloud_pass2').val();
            
            
			
			/* Main Categories, Sub Categories and goals below */
			cUsCloud_myjq(".img_loader").css({display:'inline-block'});
           
		   // this are optional so do not passcheck
		   var CU_category 		= 	cUsCloud_myjq('#CU_category').val();
		   var CU_subcategory 	= 	cUsCloud_myjq('#CU_subcategory').val();
		   
		   // check if other goal is set
		   /*if( cUsCloud_myjq('#other_goal').val() != '' ){
				var CU_goals = cUsCloud_myjq('#other_goal').val();
		   }else{*/
				
				var new_goals = '';
				var CU_goals = cUsCloud_myjq('input[name="the_goals[]"]').each(function(){
					new_goals += cUsCloud_myjq(this).val()+',';	
				});
				
				if( jQuery('#other_goal').val() )
					new_goals += jQuery('#other_goal').val()+',';	
				
				
		   /*}*/
			
			
            cUsCloud_myjq('.loadingMessage').show();
           
           if( String(pass1) != String(pass2) ){
               cUsCloud_myjq('.advice_notice').html('Check your password must be equal in both fields.').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_pass1').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(pass1.length < 8){
               cUsCloud_myjq('.advice_notice').html('Password must be 8 characters or more!').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_pass1').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if( !cUsCloud_first_name.length){
               cUsCloud_myjq('.advice_notice').html('Your First Name is a required field').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_first_name').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if( !cUsCloud_last_name.length){
               cUsCloud_myjq('.advice_notice').html('Your Last Name is a required field').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_last_name').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_email.length){
               cUsCloud_myjq('.advice_notice').html('Email is a required field!').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#apikey').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_emailValid){
               cUsCloud_myjq('.advice_notice').html('Please, enter a valid Email').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_email').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_web.length){
               cUsCloud_myjq('.advice_notice').html('Your Website is a required field').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_web').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else if(!cUsCloud_webValid){
               cUsCloud_myjq('.advice_notice').html('Please, enter one valid website URL').slideToggle().delay(2000).fadeOut(2000);
               cUsCloud_myjq('#cUsCloud_web').focus();
               cUsCloud_myjq('.loadingMessage').fadeOut();
           }else{
                cUsCloud_myjq('#cUsCloud_CreateCustomer').val('Loading . . .').attr({disabled:'disabled'});
                
                postData = {action: 'cUsCloud_verifyCustomerEmail', password:str_clean(pass1),fName:str_clean(cUsCloud_first_name),lName:str_clean(cUsCloud_last_name),Email:cUsCloud_email,website:cUsCloud_web,CU_category:CU_category,CU_subcategory:CU_subcategory,CU_goals:new_goals};
                
                cUsCloud_myjq.ajax({ 
                    type: "POST", 
                    url: ADMIN_AJAX_URL+'admin-ajax.php',
                    data: postData,
                    success: function(data) {
                        // below try to register the new user cause email
                       
                        switch(data){
                            case '1':
                                // hide the buttons
                                cUsCloud_myjq('#cUsCloud_userdata').slideUp().fadeOut();
                                cUsCloud_myjq('#cf7cloud_welcome').slideUp().fadeOut();
                                location.reload();

                            break;
                            case '2':
                            	
                            	cUsCloud_myjq("#cUsCloud_CreateCustomer").colorbox.close();
                            	
                            	// alert(data);
                                message = 'Seems like you already have one Contactus.com Account, Please Login below!';
                                 
                                setTimeout(function(){
                                    cUsCloud_myjq('#login_email').val(cUsCloud_email).focus();
                                    cUsCloud_myjq('#cUsCloud_userdata').fadeOut();
                                    cUsCloud_myjq('#cUsCloud_settings').slideDown('slow');
                                    cUsCloud_myjq('#cUsCloud_loginform').delay(1000).fadeIn();
                                    cUsCloud_myjq('#cUsCloud_CreateCustomer').val('Create Account').removeAttr('disabled');
                                },2000);
                                cUsCloud_myjq('.advice_notice').html(message).show().delay(4000).fadeOut(2000);
                            break;  
                            default: 
                                message = '<p>Ouch! Unfortunately, there has being an error during the application: <b>' + data + '</b>. Please try again or <a href="http://help.contactus.com" target="_blank">Contact Support</a></p>';
                                cUsCloud_myjq('#cUsCloud_CreateCustomer').val('Create Account').removeAttr('disabled');
                            break;
                        }
                        
                        cUsCloud_myjq('.loadingMessage').fadeOut();
                        //cUsCloud_myjq('.advice_notice').html(message).show().delay(4000).fadeOut(2000);

                    },
                    fail: function(){
                       message = '<p>Ouch! Unfortunately, there has being an error during the application. Please try again!</a></p>';
                       cUsCloud_myjq('#cUsCloud_CreateCustomer').val('Continue to Step 2').removeAttr('disabled'); 
                    }
                });
           }
           
            
        });
    }catch(err){
        cUsCloud_myjq('.advice_notice').html('Oops, something wrong happened, please try again later!').slideToggle().delay(2000).fadeOut(2000);
        cUsCloud_myjq('#cUsCloud_CreateCustomer').val('Continue to Step 2').removeAttr('disabled');
    }

	    cUsCloud_myjq('.cUsCloud_LoginUser').click(function(){ //LOGIN ALREADY USERS
        var email = cUsCloud_myjq('#login_email').val();
        var pass = cUsCloud_myjq('#user_pass').val();
        cUsCloud_myjq('.loadingMessage').show();
        
        if(!email.length){
            cUsCloud_myjq('.advice_notice').html('User Email is a required and valid field!').slideToggle().delay(2000).fadeOut(2000);
            cUsCloud_myjq('#login_email').focus();
            cUsCloud_myjq('.loadingMessage').fadeOut();
        }else if(!pass.length){
            cUsCloud_myjq('.advice_notice').html('User password is a required field!').slideToggle().delay(2000).fadeOut(2000);
            cUsCloud_myjq('#user_pass').focus();
            cUsCloud_myjq('.loadingMessage').fadeOut();
        }else{
            var bValid = checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. sergio@jquery.com" );  
            if(!bValid){
                cUsCloud_myjq('.advice_notice').html('Please enter a valid User Email!').slideToggle().delay(2000).fadeOut(2000);
                cUsCloud_myjq('.loadingMessage').fadeOut();
            }else{
                
                cUsCloud_myjq('.cUsCloud_LoginUser').val('Loading . . .').attr({disabled:'disabled'});
                cUsCloud_myjq.ajax({ type: "POST", url: ADMIN_AJAX_URL+'admin-ajax.php', data: {action:'cUsCloud_loginAlreadyUser',email:email,pass:pass},
                    success: function(data) {
                        
                        switch(data){
                            case '1':
                                
                                cUsCloud_myjq('.cUsCloud_LoginUser').val('Success . . .');
                                cUsCloud_myjq('#cUsCloud_loginform').slideUp().fadeOut();
                                
                                // hide the buttons
                                cUsCloud_myjq('#cf7cloud_welcome').hide();
                                location.reload();
                                

                            break;
                            case '2':
                                cUsCloud_myjq('.cUsCloud_LoginUser').val('Error . . .');
                                cUsCloud_myjq('#loginbtn').attr('disabled', false );
                                cUsCloud_myjq('#loginbtn').attr('value', 'Login');
                                
                                message = '<p>Seems like you don\'t have one Default Contact Forms added in your ContactUs.com account!.</p>';
                                message += '<p>Please login into your admin panel at ContactUs.com and add at least one to continue...</p>';
                                
                                // cUsCloud_loginform
                                cUsCloud_myjq('#cUsCloud_loginform').slideUp().fadeOut();
                                cUsCloud_myjq('#createpostform').fadeIn();
                                //cUsCloud_myjq('.advice_notice').html(message).show();
                                
                            break;

                            case '3':
                                
                                cUsCloud_myjq('.cUsCloud_LoginUser').val('Error . . .');
                                cUsCloud_myjq('.cUsCloud_Loginform').slideUp().fadeOut();
                                message = '<p>Seems like you don\'t have one Default Contact Forms added in your ContactUs.com account!.</p>';
                                message += '<p>Please login into your admin panel at ContactUs.com and add at least one to continue...</p>';
                                cUsCloud_myjq('.cUsCloud_LoginUser').val('Login').removeAttr('disabled');
                                cUsCloud_myjq('.advice_notice').html(message).show();
                                cUsCloud_myjq('#createpostform').fadeIn();
                                
                            break;

                            default:
                                cUsCloud_myjq('.cUsCloud_LoginUser').val('Login').removeAttr('disabled');
                                message = '<p>Ouch! Unfortunately, there has being an error during the application: <b>' + data + '</b>. Please try again or <a href="http://help.contactus.com" target="_blank">Contact Support</a></p>';
                                cUsCloud_myjq('.advice_notice').html(message).show();
                                break;
                        }
                        
                        cUsCloud_myjq('.loadingMessage').fadeOut();
                        

                    },
                    async: false
                });
            }
        }
    });
    
    
    /*
    cUsCloud_myjq("#cf7_cloud_table select").live('load change', function(){
        cUsCloud_myjq("select option").attr("disabled",""); //enable everything
        DisableOptions(); //disable selected values
    });
	*/

		function DisableOptions(){
		    var arr=[];
		      cUsCloud_myjq("#cf7_cloud_table select option:selected").each(function(){
		        arr.push(cUsCloud_myjq(this).val());
		      });
		
		    cUsCloud_myjq("#cf7_cloud_table select option").filter(function(){
		       return cUsCloud_myjq.inArray(cUsCloud_myjq(this).val(),arr)>-1;
		   }).attr("disabled","disabled");   
		
		}
		
		
		
	
		
		
		
		
		
		
		
		
		
		


	});
		


  
  