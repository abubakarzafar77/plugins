<?php $plugin_url = WP_PLUGIN_URL.'/chat-groups/'; ?>
<link type="text/css" href="<?php echo $plugin_url; ?>css/style-admin.css" rel="stylesheet" />
<?php 
?>




<?php 

$email = get_email_by_id($_GET['email_id']);

// var_dump($setting); 

?>


<style>
    #custom_form .form-field input {
    width: 25em;
}
    #custom_form .form-field textarea {
    width: 25em;
}
#ui-datepicker-div{
    z-index: 1000000 !important;
}
#slider-range-max{
    width: 22.7em;
}
</style>
<div class="wrap">
    
    <div  style="background-position: -310px -5px !important;" class="icon32" id="icon-users"><br></div>
    <h2>Email Details</h2>
    
    <p>Edit your email.</p>
    
    <table class="form-table">
        <tbody>
            
            <tr class="form-field form-required">
                <th scope="row"><label for="user_login">Time <span class="description"></span></label></th>
                <td>
                    <div style="clear:both;"></div>
                    <?php echo $email->subject; ?>
                </td>
            </tr>
            
            
            <tr class="form-field form-required">
                <th scope="row"><label for="user_login">Description <span class="description"></span></label></th>
                <td>
                    <div style="width: 500px; height: 200px;"><?php echo $email->body; ?></div>
                </td>
            </tr>
            
            
        </tbody>
    </table>
    
    
</div>
