<?php   
date_default_timezone_set('CET');  
/* 
Plugin Name: chat-groups 
Plugin URI: http://purelogics.net
Description: Chat Groups plugin. 
Version: 1.0 
Author: Purelogics.net
Author URI: http://purelogics.net
*/

require('function.php');

function show_managment_page(){
    include('chat_groups_main.php');  
}
function show_configration_page(){
    include('configration_page.php');  
}
function show_group_managment_page(){
    include('group_managment.php');  
}
function show_email_managment_page(){
    include('email_managment.php');  
}
function show_new_group_page(){
    include('new_group.php');  
}

function show_email_detail_page(){
    include('email_detail.php');  
}
function show_email_edit_page(){
    include('email_edit.php');  
}
function show_chat_histroy(){
    include('chat_history.php');  
}

//menu items
add_action('admin_menu','chat_groups_menu');
function chat_groups_menu() {
	
	//this is the main item for the menu
	add_menu_page(
    'Chat Group', //page title
	'Chat Group', //menu title
	'manage_options', //capabilities
	'chat_groups_menu', //menu slug
	'show_managment_page' //function
	);
    
    //this is a submenu
	add_submenu_page('chat_groups_menu', //parent slug
	'Configrations', //page title
	'Configrations', //menu title
	'manage_options', //capability
	'manage_setting', //menu slug
	'show_configration_page'); //function
    //
    //this is a submenu
	add_submenu_page('chat_groups_menu', //parent slug
	'Group Managment', //page title
	'Group Managment', //menu title
	'manage_options', //capability
	'manage_groups', //menu slug
	'show_group_managment_page'); //function
    //
    //this is a submenu
	add_submenu_page('chat_groups_menu', //parent slug
	'Email Managment', //page title
	'Email Managment', //menu title
	'manage_options', //capability
	'manage_emails', //menu slug
	'show_email_managment_page'); //function
    
    //this is a hidden menu item used to create page
	add_submenu_page(null, //parent slug
	'Create New Group', //page title
	'Create New Group', //menu title
	'manage_options', //capability
	'new_group', //menu slug
	'show_new_group_page'); //function
    
    //this is a hidden menu item used to create page
	add_submenu_page(null, //parent slug
	'Email Template Detail', //page title
	'Email Template Detail', //menu title
	'manage_options', //capability
	'email_detail', //menu slug
	'show_email_detail_page'); //function
    
    //this is a hidden menu item used to create page
	add_submenu_page(null, //parent slug
	'Edit Email Template', //page title
	'Edit Email Template', //menu title
	'manage_options', //capability
	'email_edit', //menu slug
	'show_email_edit_page'); //function
    
    //this is a hidden menu item used to create page
	add_submenu_page(null, //parent slug
	'Chat History', //page title
	'Chat History', //menu title
	'manage_options', //capability
	'chat_history', //menu slug
	'show_chat_histroy'); //function
    
    //this is a hidden menu item used to create page
	add_submenu_page(null, //parent slug
	'Chat History', //page title
	'Chat History', //menu title
	'manage_options', //capability
	'chat_history', //menu slug
	'show_chat_histroy'); //function
    
    
    
	
}

function load_chat_list(){
    
    if(isset($_GET['action']) == "group_detail"){
        $chat_html = "";
        
        $group_id = ( isset($_GET['group']) && $_GET['group'] != "") ? $_GET['group'] : 0;
        
        if ($group_id != 0) {
            
            global $current_user;
            get_currentuserinfo();
            
            $user = get_user_nickname_by_id($current_user->ID,$group_id);
            $group = get_gorup_detail_id($group_id);
            $register_seats = get_group_register_seats($group_id);
            $remaining_seats = $group->available_seats - $register_seats;
            

            
            // disable chat according to its time
            $start_time = $group->start_time;
            $end_time = $group->end_time;
            $now_time = time();
            
            
            //##
            //$now_time = 1382515477; // 8:04
            //$now_time = 1382515537; // 8:05
            //$now_time = 1382515837; // 8:10
            //$now_time = 1382602539; // 8:15
            //$now_time = 1382602599; // 8:16
            //##
            
            //##
            // ALL ACTIVE CHAT
           /* $start_time = 1200000000;
            $end_time = 1400000000;
            $now_time = 1300000000;*/
            //##
            $setting = get_chat_group_setting();
            $chat_before_m = $setting->time_to_enable_chat_before;
            $start_time = strtotime("-$chat_before_m minutes", $start_time);
            
             //var_dump($now_time,$start_time,$end_time); 
             //var_dump(formate_date($now_time),formate_date($start_time),formate_date($end_time)); 
                        
            $chat_disable = 0;
            
            if($now_time >= $start_time && $now_time <= $end_time){
                $chat_disable = 0;
            }
            else{
                $chat_disable = 1;
            }
            
            $chat_start_flag = ($now_time < $start_time) ? 0 : 1 ;
            $chat_end_flag = ($now_time > $end_time) ? 1 : 0 ;
            
            //var_dump($chat_disable);
            $user_id = $current_user->ID;
            $nickname = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
            $user_exist_in_group = 0;
            $user_exist_in_group = (user_exist_group($group_id,$user_id)) ? 1 : 0 ;
                
            
            
            include 'chat_html.php';
            
        }
        else{
            $chat_html = "Chat not found";
        }
        return $chat_html;
    }
    else {
        global $base_url;
        $base_url = get_permalink(@$post->ID);

//        $sort_by = (isset($_GET['sort_by'])) ? $_GET['sort_by'] : "";
//        $order_by = (isset($_GET['order_by'])) ? $_GET['order_by'] : "";
//        //var_dump($_GET);
//        $time_order = ( ( isset($_GET['sort_by']) && $_GET['sort_by'] == "time" ) && ( isset($_GET['order_by']) && $_GET['order_by'] == "asc") ) ? "desc" : "asc";
//        $topic_order = ( ( isset($_GET['sort_by']) && $_GET['sort_by'] == "topic" ) && ( isset($_GET['order_by']) && $_GET['order_by'] == "asc") ) ? "desc" : "asc";

        global $current_user;
        get_currentuserinfo();
        $user_id = ($current_user->ID != 0) ? $current_user->ID : 0;
        
        
        
        $total_groups = get_total_groups("");
        $total_rows = count($total_groups);
        $per_page = 10;
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        //var_dump($total_rows,$per_page,$page);

        include 'group_list.php';

    }
    
}
add_shortcode('CHAT_GROUPS_LIST', 'load_chat_list');


// rewriting content
function rewrite_content(){
    $chat_html = "";
    
    $group_id = ( isset($_GET['group']) && $_GET['group'] != "") ? $_GET['group'] : 0 ;
    
    if ($group_id != 0) {
        global $current_user;
        get_currentuserinfo();
        // User ID
        //var_dump($current_user->ID);    
        // check user logged in if not show message
        $top_html = "";
        if ($current_user->ID == 0) {
            $top_html = file_get_contents(WP_PLUGIN_URL . '/chat-groups/loggin_html.php');
        } else {
            $top_html = file_get_contents(WP_PLUGIN_URL . '/chat-groups/registered_html.php');
        }

        $user = get_user_nickname_by_id($current_user->ID,$group_id);
        //var_dump($user);
        $nickname = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
        $custom_fields = "";
        $custom_fields .= ' <input id="uid" type="hidden" size="63" value="' . $current_user->ID . '" /> ';
        $custom_fields .= ' <input id="admin_nickname" type="hidden" size="63" value="' . $nickname . '" /> ';

        $chat_html = $top_html . $custom_fields;
        $chat_html .= file_get_contents(WP_PLUGIN_URL . '/chat-groups/chat_html.php');
    }



    return $chat_html; 
}
add_shortcode('CHATROOM', 'rewrite_content');






function chat_groups_plugin_activate() {

    run_active_plugin_query();
}
register_activation_hook( __FILE__, 'chat_groups_plugin_activate' );

function chat_groups_plugin_deactivate() {

    run_deactive_plugin_query();
}
register_deactivation_hook( __FILE__, 'chat_groups_plugin_deactivate' );

