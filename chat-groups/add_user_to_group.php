<?php

include('../../../wp-load.php');
$msg = "";
$name = "";
$type = "";

$user_id = $_POST['uid'];
$group_id = $_POST['gid'];
$nickname = $_POST['nickname'];


if($user_id == 0){ // user not logged in 
    $type = "error";
    $msg = "<span style='color:red;' >You are not logged in Please login or register first</span>";
}
else{
    
    $r = add_user_to_chat_group_with_nickname($group_id,$user_id,$nickname);
    if($r === 3){
        $type = "error";
        $msg = "<span style='color:red;' >Oops there are no more seats available !</span>";
    }
    else if($r === 2){
        $type = "error";
        $msg = "<span style='color:red;' >User was not found! Please try again with diffrent login</span>";
    }
    else if($r === 4){
        $type = "error";
        $msg = "<span style='color:red;' >You have already been register</span>";
    }
    else{
        $type = "ok";
        $msg = "<div> Hi (".$nickname.")! <br> Your are registered to participate in this group room:) Unregister? <a href='javascript: void(0)' id='unreg_user_for_chat'> Click here</a>  </div>";
        
        
    }
}

$array = array("type" => $type, "name" => $name, "msg" => $msg);
echo json_encode($array);
die;
?>
