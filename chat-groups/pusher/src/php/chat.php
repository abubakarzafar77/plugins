<?php

require_once('lib/squeeks-Pusher-PHP/lib/Pusher.php');
require_once('Activity.php');
require_once('config_example.php');
require_once('../../../../../../wp-load.php');

date_default_timezone_set('UTC');



$channel_name = null;
$data = "";
$response = "";
$trigger_type = "";

if (isset($_POST['chat_info'])) {
    $trigger_type = "chat_message";
} else if (isset($_POST['user_list'])) {
    $trigger_type = "user_list";
} else if (isset($_POST['user_list_remove'])) {
    $trigger_type = "user_list_remove";
} else {
    header("HTTP/1.0 400 Bad Request");
    echo('chat_info must be provided');
}

if (!isset($_SERVER['HTTP_REFERER'])) {
    header("HTTP/1.0 400 Bad Request");
    echo('channel name could not be determined from HTTP_REFERER');
}

$channel_name = get_channel_name($_SERVER['HTTP_REFERER']);
$pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);

// if trigger is chat message
if ($trigger_type == "chat_message") {
    
    $options = $_POST['chat_info'];

    $activity = new Activity('chat-message', $options['msg'], $options);

    
    $data = $activity->getMessage();

    $data = array(
        "msg" => stripslashes($options['msg']),
        "uid" => $options['uid'],
        "gid" => $options['gid'],
        "msg_time" => $options['msg_time'],
        "msg_name" => $options['msg_name'],
    );
    $response = $pusher->trigger($channel_name, 'chat_message', $data, null, true);

    header('Cache-Control: no-cache, must-revalidate');
    header('Content-type: application/json');

// if response is success then insert chat in DB

    if ($response) {


        $user_id = $options['uid'];
        $group_id = $options['gid'];
        $msg = $options['msg'];

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
            }
        }
    }
// 
} // if trigger is chat message END 
else if ($trigger_type == "user_list") { // if trigger is user_list
    
    $msg = "";
    $name = "";
    $type = "";
    $options = $_POST['user_list'];
    $user_id = $options['uid'];
    $group_id = $options['gid'];
    $nickname = $options['nickname'];
    

    if ($user_id == 0) { // user not logged in 
        $type = "error";
        $msg = "<span style='color:red;' >Du må være innlogget for å melde deg på kollokviegruppen</span>";
    } else {



        $r = add_user_to_chat_group_with_nickname($group_id, $user_id, $nickname);
        if ($r === 3) {
            $type = "error";
            $msg = "<span style='color:red;' >Oops there are no more seats available !</span>";
        } else if ($r === 2) {
            $type = "error";
            $msg = "<span style='color:red;' >User was not found! Please try again with diffrent login</span>";
        } else if ($r === 4) {
            $type = "error";
            $msg = "<span style='color:red;' >You have already been register</span>";
        } else {
            $type = "ok";
            $msg = "<div> Hi (" . $nickname . ")! <br> Your are registered to participate in this group room:) Unregister? <a href='javascript: void(0)' id='unreg_user_for_chat'> Click here</a>  </div>";
        }
    }

    $array = array("type" => $type, "name" => $name, "msg" => $msg);

    $nicknames = array();
    $users = get_chat_group_users($group_id);
    if (count($users) > 0) {
        foreach ($users as $i => $user) {
            $nicknames[] = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
        }
    }
    $data['nicknames'] = $nicknames;
    $data['reg_rsp'] = $array;
    
    $response = $pusher->trigger($channel_name, 'user_list', $data, null, true);
    
}
else if ($trigger_type == "user_list_remove") { // if trigger is remove_user_list
    
    $msg = "";
    $name = "";
    $type = "";

    $options = $_POST['user_list_remove'];
    $user_id = $options['uid'];
    $group_id = $options['gid'];
    
    if ($user_id == 0) { // user not logged in 
        $type = "error";
        $msg = "You are not logged in Please login or register first";
    } else {

        $r = remove_user_from_chat_group($group_id, $user_id);
        if ($r) {
            $type = "ok";
            $msg = "Unregister successfully";
        } else {
            $type = "error";
            $msg = "Problem unregistering";
        }
    }

    $array = array("type" => $type, "name" => $name, "msg" => $msg);

    $nicknames = array();
    $users = get_chat_group_users($group_id);
    if (count($users) > 0) {
        foreach ($users as $i => $user) {
            $nicknames[] = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
        }
    }
    $data['nicknames'] = $nicknames;
    $data['reg_rsp'] = $array;
    
    $response = $pusher->trigger($channel_name, 'user_list', $data, null, true);
    
}


$result = array('activity' => $data, 'pusherResponse' => $response);
echo(json_encode($result));

function get_channel_name($http_referer) {
    // not allowed :, / % #
    $pattern = "/(\W)+/";
    $channel_name = preg_replace($pattern, '-', $http_referer);
    return $channel_name;
}

function sanitise_input($chat_info) {
    $email = isset($chat_info['email']) ? $chat_info['email'] : '';

    $options = array();
    $options['displayName'] = substr(htmlspecialchars($chat_info['nickname']), 0, 30);
    $options['text'] = substr(htmlspecialchars($chat_info['text']), 0, 300);
    $options['email'] = substr(htmlspecialchars($email), 0, 100);
    $options['get_gravatar'] = true;
    return $options;
}

?>