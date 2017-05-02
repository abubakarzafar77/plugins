<?php

function update_chat_group_setting($days_in_calender,$hours_duration,$descn_length,$max_seat,$per_page,$time_to_email_before, $time_to_email_reminder, $time_to_enable_chat_before) {
    global $wpdb;

    $q = "UPDATE  chat_group_setting 
            SET 
            days_in_calender = '$days_in_calender',
            hours_duration = '$hours_duration',
            descn_length = '$descn_length',
            max_seat = '$max_seat',
            per_page = '$per_page',
            time_to_email_before = '$time_to_email_before',
            time_to_email_reminder = '$time_to_email_reminder',
            time_to_enable_chat_before = '$time_to_enable_chat_before'
            WHERE id = 1 ";

    $r = $wpdb->query($q);

    return true;
}

function update_email($id, $body, $subject) {
    global $wpdb;
    $id = $wpdb->escape($id);
    $q = "UPDATE  email_templates 
            SET 
            body = '$body',
            subject = '$subject'
            WHERE id = '$id' ";

    $r = $wpdb->query($q);
    return true;
}

function get_chat_group_setting() {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM chat_group_setting WHERE id = 1 ");
}

function get_email_by_id($id) {
    global $wpdb;
    $id = $wpdb->escape($id);
    return $wpdb->get_row("SELECT * FROM  email_templates WHERE id = '$id' ");
}

function get_group_by_id($id) {
    global $wpdb;
    $id = $wpdb->escape($id);
    return $wpdb->get_row("SELECT * FROM  groups WHERE id = '$id' ");
}

function get_gorup_detail_id($id) {
    global $wpdb;
    $id = $wpdb->escape($id);

    $query = "SELECT g.topic AS title, 
        g.start_time,
        g.end_time,
        g.available_seats,
        g.description,
        g.nickname,
        g.created_at,
        g.updated_at,
        g.id,
        is_active
        FROM groups g
        WHERE g.id = '$id' ";
    return $wpdb->get_row($query);
}

function get_all_chat_groups($is_active = "") {

    // current time is between start and time then group is active
    $where = "";
    if ($is_active != "") {
        $time = time();
        if ($is_active) {
            $where = " AND ( '$time' >= g.start_time AND '$time' <= g.end_time ) ";
        } else {
            $where = " AND ( '$time' <= g.start_time OR  '$time' >= g.end_time ) ";
        }
    }

    global $wpdb;
    $query = "SELECT g.topic AS title, 
        g.start_time,
        g.end_time,
        g.available_seats,
        g.description,
        g.nickname,
        g.created_at,
        g.updated_at,
        g.id,
        is_active
        FROM groups g
        WHERE g.is_active = 1
        " . $where;
    //die;



    $r = $wpdb->get_results($query);
    return $r;
}

function get_total_groups($is_active = "") {

    // current time is between start and time then group is active
    $where = "";
    if ($is_active != "") {
        $time = time();
        if ($is_active) {
            $where = " AND ( '$time' >= g.start_time AND '$time' <= g.end_time ) ";
        } else {
            $where = " AND ( '$time' <= g.start_time OR  '$time' >= g.end_time ) ";
        }
    }

    global $wpdb;
    $query = "SELECT 
        g.id
        FROM groups g
        WHERE g.is_active = 1
        " . $where;
    $r = $wpdb->get_results($query);
    return $r;
}

function get_all_chat_groups_frontend($is_active = "" , $list_type = "" , $user_id = 0) {

    // current time is between start and end time then group is active
    $where = "";
    if ($is_active != "") {
        $time = time();
        if ($is_active) {
            $where = " AND ( '$time' >= g.start_time AND '$time' <= g.end_time ) ";
        } else {
            $where = " AND ( '$time' <= g.start_time OR  '$time' >= g.end_time ) ";
        }
    }
    if($list_type != ""){
        $where = " AND  g.user_id = '$user_id' ";
    }




    global $wpdb;
    $user_table = $wpdb->prefix . "users";
    //g.available_seats - COUNT(gu.group_id) AS seats,
    $query = "SELECT 
        g.topic AS title, 
        g.start_time,
        g.end_time,
        g.available_seats AS total_seats,
        g.description,
        g.nickname,
        g.created_at,
        g.updated_at,
        g.id,
        u.user_nicename,
        is_active
        FROM groups g
        JOIN " . $user_table . " u ON g.user_id = u.ID 
        WHERE g.is_active = 1
        " . $where . " ORDER BY ID DESC";
    //var_dump($query);die;
    //die;



    $r = $wpdb->get_results($query);
    return $r;
}

function get_all_email_templates() {
    global $wpdb;
    $query = "SELECT * FROM email_templates";
    $r = $wpdb->get_results($query);
    return $r;
}

function get_email_by_type($type) { // $type = 'mail_1' , 'mail_2' 
    global $wpdb;
    $query = "SELECT * FROM email_templates where type = '$type' ";
    $r = $wpdb->get_row($query);
    return $r;
}

function get_all_topics() {
    global $wpdb;
    $query = "SELECT * FROM groups_topics";
    $r = $wpdb->get_results($query);
    return $r;
}

// chats function 
function insert_chat_message($user_id, $group_id, $msg) {
    global $wpdb;
    $created_at = time();


    $q = " INSERT INTO groups_chats (user_id, group_id, message, created_at) VALUES (
        '$user_id',
        '$group_id',
        '$msg',
        '$created_at'  
        ) ";

    $r = $wpdb->query($q);
    if ($r) {
        return array("time" => $created_at, "msg" => $msg);
    }
    return false;
}

function check_logged_in_user_invited_to_group($user_id = 0, $group_id = 0) {
    global $wpdb;
    $user_table = $wpdb->prefix . "users";
    $q = "SELECT gu.id,u.user_nicename,gu.nickname FROM groups_users gu 
        JOIN " . $user_table . " u ON gu.user_id = u.ID 
        WHERE gu.user_id = '$user_id' AND gu.group_id = '$group_id'  ";

    return $wpdb->get_row($q);
}

function get_chat_by_group_id($group_id = 0, $last_id = "") {
    global $wpdb;
    $group_id = $wpdb->escape($group_id);
    $last_id_query = "";
    if ($last_id != "") {
        $last_id_query = " AND gc.id > '$last_id' ";
    }
    $user_table = $wpdb->prefix . "users";
    $q = "SELECT u.user_nicename ,gu.nickname, gc.message AS msg,gc.created_at AS time ,gc.id FROM groups_chats gc
        JOIN " . $user_table . " u on gc.user_id = u.ID
        LEFT JOIN  groups_users gu ON gc.user_id = gu.user_id
        WHERE gc.group_id = '$group_id' AND gu.group_id = '$group_id' " . $last_id_query . "
        ORDER BY id ASC ";
    return $wpdb->get_results($q);
}

function get_chat_by_id($id) {
    global $wpdb;
    $id = $wpdb->escape($id);
    return $wpdb->get_row("SELECT * FROM  groups_chats WHERE group_id = '$id' ");
}

function get_chat_group_users($group_id) {
    global $wpdb;
    $id = $wpdb->escape($id);
    $user_table = $wpdb->prefix . "users";
    $q = "SELECT 
        gu.group_id,
        gu.user_id,
        gu.nickname,
        u.user_nicename
        FROM  groups_users gu  JOIN " . $user_table . " u on gu.user_id = u.ID WHERE group_id = '$group_id'";

    return $wpdb->get_results($q);
}

function get_chat_group_users_who_participated($group_id) {
    global $wpdb;
    $id = $wpdb->escape($id);
    $user_table = $wpdb->prefix . "users";
    $q = "SELECT 
        gc.user_id,
        gu.nickname,
        u.user_nicename
        FROM  groups_chats gc  
        JOIN " . $user_table . " u ON gc.user_id = u.ID 
        JOIN groups_users gu ON gu.user_id = gc.user_id 
        WHERE gc.group_id = '$group_id'
        GROUP BY gc.user_id ";

    return $wpdb->get_results($q);
}

function get_user_nickname_by_id($user_id, $group_id) {
    global $wpdb;

    $user_table = $wpdb->prefix . "users";


//        $q = "SELECT 
//        g.nickname,
//        u.user_nicename
//        FROM " . $user_table . " u 
//        JOIN groups g ON g.user_id = u.ID
//        WHERE g.id = '$group_id'
//        AND g.user_id = '$user_id'
//        ";
//        $user = $wpdb->get_row($q);
//        
//        if(!$user){

    $q2 = "SELECT 
            gu.nickname,
            u.user_nicename
            FROM " . $user_table . " u 
            JOIN groups_users gu ON gu.user_id = u.ID
            WHERE gu.group_id = '$group_id'
            AND gu.user_id = '$user_id'
            ";
    $user = $wpdb->get_row($q2);
    //}
    return $user;
}

function create_new_group($user_id, $topic, $start_time, $end_time, $seats, $descn, $nickname, $invites, $time, $setting) {
    global $wpdb;
    $q = "INSERT INTO groups 
        ( user_id, topic, start_time, end_time, available_seats,description,nickname, created_at, updated_at, is_active ) 
        VALUES (
            '$user_id',
            '$topic',
            '$start_time',
            '$end_time',
            '$seats',
            '$descn',
            '$nickname',
            '$time',
            '$time',
            '1') ";
    $r = $wpdb->query($q);

    if ($r) {
        $group_id = mysql_insert_id();
        $q4 = "INSERT INTO groups_users (group_id,user_id,nickname) VALUES ('$group_id','$user_id', '$nickname') ";
        $wpdb->query($q4);
        $invites = array_unique($invites);
        
        foreach ($invites as $i => $email) {

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user_table = $wpdb->prefix . "users";
                $q2 = "SELECT ID,user_email FROM " . $user_table . " WHERE user_email = '$email' ";
                $user = $wpdb->get_row($q2);
               
                if ($user) {
                    if ($i < $seats) {
                        $user_id = $user->ID;
                        $q3 = "INSERT INTO groups_users (group_id,user_id,nickname) VALUES ('$group_id','$user_id', '') ";
                        $wpdb->query($q3);
                        $i++;

                        // send email to the invited user
                        $base_url = $GLOBALS['base_url'];
                        $link = "<a href=' " . $base_url . "?action=group_detail&group=" . $group_id . " ' >" . $base_url . "?action=group_detail&group=" . $group_id . "</a>";
                        $replace_patterns = array(
                            '{_NICKNAME_}' => $nickname,
                            '{_TIME_}' => formate_date($start_time),
                            '{_TOPIC_}' => $topic,
                            '{_DESCRIPTION_}' => $descn,
                            '{_CHATROOM_}' => $link,
                            '{_YOUR_USER_SETTING_}' => "Your email setting",
                        );
                        
                        email_send($email, 'mail_1', $replace_patterns);
                        
                    }
                }
            }
        }
        return $group_id;
    }
    return false;
}

function add_user_to_chat_group_with_nickname($group_id, $user_id, $nickname) {
    global $wpdb;
    // if email is valid 
    if ($nickname != "") {

        // check if groups seats are avalible
        $group = get_group_by_id($group_id);
        $ava_seats = $group->available_seats;
        $q = "SELECT group_id from groups_users WHERE group_id = '$group_id' ";
        $wpdb->query($q);

        if ($wpdb->num_rows < $ava_seats) {

            $user_table = $wpdb->prefix . "users";
            $q2 = "SELECT ID,user_email FROM " . $user_table . " WHERE ID = '$user_id' ";
            $user = $wpdb->get_row($q2);
            if ($user) {
                $user_table = $wpdb->prefix . "users";
                $q4 = "SELECT user_id FROM groups_users WHERE group_id = '$group_id' AND user_id = '$user_id' ";
                $user_exist = $wpdb->get_row($q4);
                if (!$user_exist) {
                    $q3 = "INSERT INTO groups_users (group_id,user_id,nickname) VALUES ('$group_id','$user_id', '$nickname') ";
                    $wpdb->query($q3);
                    return true;
                }
                return 4;
            }
            return 2;
        }
        return 3;
    }
}

function get_group_register_seats($group_id) {
    global $wpdb;
    $q = "SELECT COUNT(group_id) AS registered_seats FROM groups_users WHERE group_id =  '$group_id' ";
    $group = $wpdb->get_row($q);
    return (int) $group->registered_seats;
}

function user_exist_group($group_id, $user_id) {
    global $wpdb;
    $q = "SELECT user_id FROM groups_users WHERE group_id = '$group_id' AND user_id = '$user_id' AND is_delete = 0 ";
    $user_exist = $wpdb->get_row($q);
    if (!$user_exist) {
        return false;
    }
    return true;
}

function remove_user_from_chat_group($group_id, $user_id) {
    global $wpdb;
    $q = "UPDATE `groups_users` SET is_delete = '1' WHERE group_id = '$group_id' AND user_id = '$user_id' ";
    $r = $wpdb->query($q);
    if ($r) {
        return true;
    }
    return false;
}

// general 
if (!function_exists('formate_date')) {

    function formate_date($datetime, $formate = "") {
        $default_formate = ($formate == "") ? "F j, Y, g:i a" : $formate;
        $formated_datetime = date($default_formate, $datetime);
        return $formated_datetime;
    }

}
if (!function_exists('trim_string')) {

    function trim_string($str, $display_str = 100, $strPost = "...", $strPre = "") {
        $str = (string) $str;
        $output = "";
        $output .= ($strPre != "") ? $strPre : "";
        $output .= substr($str, 0, $display_str);
        $output .= (strlen($str) > $display_str) ? $strPost : "";
        return $output;
    }

}

function email_send($email, $email_type, $replace_patterns) {

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    $email_temp = get_email_by_type($email_type);

    $body = $email_temp->body;
    $subject = $email_temp->subject;
    $from = $email_temp->from;
    foreach ($replace_patterns as $pattern => $val) {
        $body = str_replace($pattern, $val, $body);
        $subject = str_replace($pattern, $val, $subject);
    }
    $to = $email; // note the comma
    
    $headers .= "From:" . $from;
    
    $r =  (@mail($to, $subject, $body, $headers)) ? true : false ;
    return $r;
}

//
function run_active_plugin_query() {
//        $q = "CREATE TABLE IF NOT EXISTS `active` (`id` int(11) NOT NULL AUTO_INCREMENT,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
//        global $wpdb;
//        $wpdb->query($q);
}

function run_deactive_plugin_query() {
//        $q = "DROP TABLE `active` ;";
//        global $wpdb;
//        $wpdb->query($q);
}

function paginate_links_custom($total_rows, $per_page, $page) {
    $current_page = $page;
    $total_pages = ceil($total_rows / $per_page);
    if ($current_page >= 1) {

        $output = '';

//        if($options['page'] > 1) {
//            $markup .= '<a href="?page=' . ($options['page'] - 1) . ((isset($options['order_by'])) ? "&sort=" . $options['order_by'] : "") . '">< prev</a>';
//        }       

        for ($i = 1; $i <= $total_pages; $i++) {

//            if($options['page'] != $i) {
//                $markup .= '<a href="?page='. $i . ((isset($options['order_by'])) ? "&sort=" . $options['order_by'] : "") . '">' . $i . '</a>';
//            }
//            else {
//                $markup .= '<span class="current">' . $i . '</span>';
//            }

            $output .= "<li><a href='#' >" . $i . "</a></li>";
        }

//        if($options['page'] < $options['total_pages']) {
//            $markup .= '<a href="?page=' . ($options['page'] + 1) . ((isset($options['order_by'])) ? "&sort=" . $options['order_by'] : "") . '">next ></a>';
//        }



        return $output;
    } else {
        return false;
    }
}