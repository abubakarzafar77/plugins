

<?php $plugin_url = WP_PLUGIN_URL.'/chat-groups/'; ?>
<link type="text/css" href="<?php echo $plugin_url; ?>css/style-admin.css" rel="stylesheet" />
<?php 
$setting = get_chat_group_setting();

?>




<?php 


$msg = '';
$type = '';
if(isset($_POST['submit'])){
    
    
    
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $id = (int) $_GET['email_id'];
    
    if( $subject == ""){
        $msg = 'Subject cannot be empty';
        $type = 'error';
    }
    else if( $body == "" ){
        $msg = 'Body cannet be empty';
        $type = 'error';
    }
    
    
    if($type == ''){
        $r = update_email($id,$body,$subject);
        if($r){
            $msg = 'Settings saved';
            $type = 'updated';
        }
    }
}
$email = get_email_by_id($_GET['email_id']);

// var_dump($setting); 

?>



<?php

// response message
if($type != ''){

    echo '<div class=" '.$type.' settings-error" id="setting-error-settings_updated"><p><strong>'.$msg.'</strong></p></div>';



}
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
    
    <form id="custom_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" >
    
    
    <div  style="background-position: -310px -5px !important;" class="icon32" id="icon-users"><br></div>
    <h2> Edit Email</h2>
    
    <p>Edit your email.</p>
    
    <table class="form-table">
        <tbody>
            
            <tr class="form-field form-required">
                <th scope="row"><label for="user_login">Subject <span class="description">(required)</span></label></th>
                <td>
                    <div style="clear:both;"></div>
                    <input type="text" aria-required="true" value="<?php echo $email->subject; ?>" class="" id="subject" name="subject" style="width: 25em;">
                </td>
            </tr>
            
            
            <tr class="form-field form-required">
                <th scope="row"><label for="user_login">Description <span class="description">(required)</span></label></th>
                <td>
                    <style>#custom_form .form-field input {
                        width: auto;
                    }</style>
                    <div style="width: 600px; ">
                     <?php 
                     $content = $email->body; 
                     $editor_id = 'body';
                     wp_editor( $content, $editor_id, $settings = array('textarea_name'=>'body','textarea_rows'=> 20) );
                    ?>
                    </div>
                    
                </td>
            </tr>
            
            
        </tbody>
    </table>
    <br>
    <p class="submit"><input type="submit" value="Save" class="button button-primary" id="createusersub" name="submit"></p>
    
    </form>
</div>
