<?php @include_once("../../../wp-config.php"); ?>
<?php
$setting = get_chat_group_setting();

?>
<?php

$msg = '';
$type = '';
$setting = get_chat_group_setting();
if (isset($_POST['submit'])) {

    
    
    // calculating start and end time 
    $time = $_POST['time'];
    $time = str_replace("/", "-", $time);
    $time = strtotime($time);
    $start_time = $time;
    $h = $setting->hours_duration;
    $end_time = strtotime("+$h hours", $time);
    $seats = $_POST['seats'];
    $topic = $_POST['topic'];
    $descn = $_POST['descn'];
    $nickname = $_POST['nickname'];
    $invites = $_POST['invites'];
    $user_id = $_POST['user_id'];




    create_new_group($user_id, $topic, $start_time, $end_time, $seats, $descn, $nickname, $invites, $time, $setting);


    $return = $_POST['redirect'];
    
    header('Location: '.$return);


    
}

?>
<?php $plugin_url = WP_PLUGIN_URL . '/chat-groups/'; ?>
<!-- JQ UI -->
<script src="<?php echo $plugin_url.'js/jquery-1.9.1.js'?>"></script>
<link rel="stylesheet" href="<?php echo $plugin_url.'css/jquery-ui.css' ?>" />
<script src="<?php echo $plugin_url.'js/jquery-ui.js' ?>"></script>
<script src="<?php echo $plugin_url.'js/timepicker-ui.js' ?>"></script>
    
<!--JQ UI end -->
<link type="text/css" rel="stylesheet" href="<?php echo WP_PLUGIN_URL . "/chat-groups/css/style_group_new.css" ?>" />  



<script>
    $(document).ready(function() {
        
        
        
        
    
    });
</script>





<div id="new_group_div">
    <form id="create_group_form" action="<?php echo $plugin_url."new_group.php"; ?>" method="post" >
        <input type="hidden" name="redirect" value="<?php echo $_POST['base_url']; ?>" >
        <input type="hidden" name="user_id" value="<?php echo $_POST['user_id']; ?>" >
        <h2 class="new_group_hd">New study group</h2>
        <table class="new_group_tbl">
            <tbody>

                <tr class="">
                    <td>
                        <div style="clear:both;"></div>
                        <input type="text" aria-required="true" value="" class="timepicker" id="time" name="time">
                    </td>
                </tr>


                <tr class="">
                    <td>
                        <div style="clear:both;"></div>
                        <div>Maximum Seats : <span id="seat_num">1</span></div>
                        <div id="slider-range-max"></div>
                        <input type="hidden" aria-required="true" value="" id="seats" name="seats">
                    </td>
                </tr>

                <tr class="">
                    <td>
                        <div style="clear:both;"></div>
                        <?php if (count($topics) > 0) { ?>
                            <select id="topic" name="topic">
                                <?php foreach ($topics as $topic) { ?>
                                    <option value="<?php echo $topic->id; ?>" ><?php echo $topic->title; ?></option>
                                <?php } ?>
                            </select>
                            <?php
                        } else {
                            echo "NO topics exist";
                        }
                        ?>
                    </td>
                </tr>

                <tr class="">
                    <td>
                        <textarea name="descn" id="descn" class="group_descn" maxlength="<?php echo $setting->descn_length; ?>"></textarea>
                    </td>
                </tr>


                <tr class="">
                    <td>
                        <input type="text" value="" name="nickname">
                    </td>
                </tr>



            </tbody>
        </table>

        <div class="invite_div">
            <span>Want to give someone an early invite, <?php echo $setting->time_to_email_before; ?> min before the others?</span>
            <input type="text" value="" id="invites" placeholder="type email address of user here">
            <input type="button" value="Add Address" id="add_invites" class="orange_btn" >
        </div>
        <div style="clear: both;"></div>
        <div id="invites_list"></div>

        <input type="submit" value="Create Group" class="button button-primary orange_btn" id="create_group" name="submit">

    </form>
</div>
