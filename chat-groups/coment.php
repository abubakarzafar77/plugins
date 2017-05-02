 <?php
 
include('../../../wp-load.php');
$msg = "";
$time = "";
$name = "";
$type = "";

$user_id = $_POST['uid'];
$group_id = $_POST['gid'];
$msg = $_POST['msg'];


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
    $time = "";
    $name = "";
    $msg = "logged in User not invited to this group";
}
$time = formate_date($time, "g:i A");
$array = array("type" => $type, "time" => $time, "name" => $name, "msg" => $msg);
echo json_encode($array);
die;

 
 
 
 
//  $filename  = dirname(__FILE__).'/data.txt';
// 
//  // store new message in the file
//  $msg = isset($_GET['msg']) ? $_GET['msg'] : '';
//  if ($msg != '')
//  {
//    
//  }
// 
//  // infinite loop until the data file is not modified
//  $lastmodif    = isset($_GET['timestamp']) ? $_GET['timestamp'] : 0;
//  $currentmodif = filemtime($filename);
//  while ($currentmodif <= $lastmodif) // check if the data file has been modified
//  {
//    usleep(10000); // sleep 10ms to unload the CPU
//    clearstatcache();
//    $currentmodif = filemtime($filename);
//  }
 
  // return a json array
  $response = array();
  $response['msg']       = file_get_contents($filename);
  $response['timestamp'] = $currentmodif;
  echo json_encode($response);
  flush();
 
  ?>