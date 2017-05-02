<?php
include_once('../../../wp-load.php');
$group_id = $_REQUEST['gid'];
$last_id = $_REQUEST['last_id'];


$chats = get_chat_by_group_id($group_id,$last_id);

$html = "";
$last_id = 0;
if (count($chats) > 0){
    foreach($chats as $chat){
        $time = formate_date($chat->time, "g:i A");
        $name = ($chat->nickname != "") ? $chat->nickname : $chat->user_nicename;
        $html .= '<div class="chat_msg"><span class="time">'.$time.'</span><span class="userName" >'.$name.'</span><span class="message">'.$chat->msg.'</span></div>';
        $last_id = $chat->id;
    }
}else{
    $last_id = $_POST['last_id'];
}

$array =  array("html"=> $html , "last_id"=>$last_id);
echo json_encode($array); die;
?>