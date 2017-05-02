<?php

include('../../../wp-load.php');
$msg = "";
$name = "";
$type = "";

$user_id = $_POST['uid'];
$group_id = $_POST['gid'];


if($user_id == 0){ // user not logged in 
    $type = "error";
    $msg = "You are not logged in Please login or register first";
}
else{
    
    $r = remove_user_from_chat_group($group_id,$user_id);
    if($r){
        $type = "ok";
        $msg = "Unregister successfully";
    }
    else{
        $type = "error";
        $msg = "Problem unregistering";
    }
}

$array = array("type" => $type, "name" => $name, "msg" => $msg);
echo json_encode($array);
die;
?>
