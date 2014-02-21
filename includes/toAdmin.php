<?php
/**
 To Admin
 */
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<title>Loading your ContactUs.com Admin Panel</title>
</head>

<body>
    
    <p>Loading your ContactUs.com Admin Dashboard. . .</p>
    
<?php
    
    if (isset($_REQUEST['uE']) && isset($_REQUEST['uC']) ) {
        ?>
            
        <script>
            var form = jQuery('<form action="https://admin.contactus.com/partners/" method="post" style="display:none;">' +
                '<input type="hidden" name="confirmed" value="1">' +
                '<input type="hidden" name="loginName" value="<?php echo $_REQUEST['uE'] ?>">' +
                '<input type="hidden" name="userPsswd" value="<?php echo $_REQUEST['uC'] ?>">' +
                '<input value="Login" type="submit" /> ' + 
                '</form>');
            jQuery('body').append(form);
            jQuery(form).delay(8000).submit();
        </script>
            
            
        <?php
    }
?>
    
</body>

</html>

