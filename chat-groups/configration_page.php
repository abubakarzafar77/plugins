
<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/chat-groups/css/style-admin.css" rel="stylesheet" />

<?php 
$msg = '';
$type = '';
if(isset($_POST['submit'])){
    
    
    $days_in_calender = $_POST['days_in_calender'];
    $hours_duration = $_POST['hours_duration'];
    $descn_length = $_POST['descn_length'];
    $max_seat = $_POST['max_seat'];
    $per_page = $_POST['per_page'];
    $time_to_email_before = $_POST['time_to_email_before'];
    $time_to_email_reminder = $_POST['time_to_email_reminder'];
    $time_to_enable_chat_before = $_POST['time_to_enable_chat_before'];
    // var_dump($_POST); 
    
    if( !filter_var($days_in_calender, FILTER_VALIDATE_INT)){
        $msg = 'Number of days to be display in calendar must be numaric';
        $type = 'error';
    }
    else if( !filter_var($hours_duration, FILTER_VALIDATE_INT)){
        $msg = 'Hours duration must be numaric';
        $type = 'error';
    }
    else if( !filter_var($descn_length, FILTER_VALIDATE_INT)){
        $msg = 'Description Length must be numaric';
        $type = 'error';
    }
    else if( !filter_var($max_seat, FILTER_VALIDATE_INT)){
        $msg = 'Maximum number of seats must be numaric';
        $type = 'error';
    }
    else if( !filter_var($per_page, FILTER_VALIDATE_INT)){
        $msg = 'Number of Groups per page must be numaric';
        $type = 'error';
    }
    else if( !filter_var($time_to_email_before, FILTER_VALIDATE_INT)){
        $msg = 'Time for sending email before the group start must be numaric';
        $type = 'error';
    }
    else if( !filter_var($time_to_email_reminder, FILTER_VALIDATE_INT)){
        $msg = 'Time for sending reminder email must be numaric';
        $type = 'error';
    }
    else if( !filter_var($time_to_enable_chat_before, FILTER_VALIDATE_INT)){
        $msg = 'Time to enable chat before group start must be numaric';
        $type = 'error';
    }
    
    if($type == ''){
        $r = update_chat_group_setting($days_in_calender,$hours_duration,$descn_length,$max_seat,$per_page,$time_to_email_before, $time_to_email_reminder, $time_to_enable_chat_before);
        if($r){
            $msg = 'Settings saved';
            $type = 'updated';
        }
    }
}

$setting = get_chat_group_setting();
// var_dump($setting); 

?>




<div class="wrap">
    
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" >
    <h2>Configure Your Setting</h2>
    <?php
    
    // response message
    if($type != ''){
        
        echo '<div class=" '.$type.' settings-error" id="setting-error-settings_updated"><p><strong>'.$msg.'</strong></p></div>';
        
        
       
    }
    ?>
    <table class="form-table widefat">
        <tr valign="top">
            <th scope="row"><label for="">&nbsp;</label></th>
            <td>&nbsp;</td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="">Number of days to be display in calendar</label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->days_in_calender) ? $setting->days_in_calender : '14' ; ?> " id="days_in_calender" name="days_in_calender"></td>
        </tr>
        
        
        
        
        <tr valign="top">
            <th scope="row"><label for="">Description Length</label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->descn_length) ? $setting->descn_length : '170' ; ?>" id="descn_length" name="descn_length"></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="">Maximum number of seats</label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->max_seat) ? $setting->max_seat : '6' ; ?>" id="max_seat" name="max_seat"></td>
        </tr>
        
       
        
        <tr valign="top">
            <th scope="row"><label for="">Number of Groups per page</label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->per_page) ? $setting->per_page : '4' ; ?>" id="per_page" name="per_page"></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="">Time for sending email (email_1) after group is created</label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->time_to_email_before) ? $setting->time_to_email_before : '6' ; ?>" id="time_to_email_before" name="time_to_email_before"></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="">Time for sending reminder email (mail_2) before chat start </label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->time_to_email_reminder) ? $setting->time_to_email_reminder : '10' ; ?>" id="time_to_email_reminder" name="time_to_email_reminder"></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="">Groups duration ( in hours )</label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->hours_duration) ? $setting->hours_duration : '3' ; ?>" id="hours_duration" name="hours_duration"></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="">Time to enable chat before group start ( in minutes )</label></th>
            <td><input type="text" class="regular-text" value="<?php echo isset($setting->time_to_enable_chat_before) ? $setting->time_to_enable_chat_before : '6' ; ?>" id="time_to_enable_chat_before" name="time_to_enable_chat_before"></td>
        </tr>
        
        <tr valign="top">
            <th scope="row"><label for="">&nbsp;</label></th>
            <td>&nbsp;</td>
        </tr>
        
    </table>
    
    <p class="submit">
        <input type="submit" value="Save Setting" class="button button-primary" id="submit" name="submit">
    </p>
        
    </form>
</div>
