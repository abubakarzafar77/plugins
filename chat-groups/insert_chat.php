<?php

include('../../../wp-load.php');
$msg = "";
$time = "";
$name = "";
$type = "";

$user_id = $_POST['uid'];
$group_id = $_POST['gid'];
$msg = $_POST['msg'];

if ($user_id != 0) {
    $user = check_logged_in_user_invited_to_group($user_id, $group_id);
    if ($user) {
        if ($msg != "") {
            $r = insert_chat_message($user_id, $group_id, $msg);
            if ($r) {
                $time = $r['time'];
                $name = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
                $msg = stripslashes($r['msg']);
                $type = "ok";
            }
        }
    } else {
        $type = "error";
        $msg = "logged in User not invited to this group";
    }
} else {
    $type = "error";
    $msg = "User Not logged in";
}
$time = formate_date($time, "g:i A");
$array = array("type" => $type, "time" => $time, "name" => $name, "msg" => $msg);
echo json_encode($array);
die;
?>
