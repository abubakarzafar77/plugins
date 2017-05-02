<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/

echo <<<EOE
   <script type="text/javascript">
     if (navigator.cookieEnabled)
       document.cookie = "tzo="+ (- new Date().getTimezoneOffset());
   </script>
EOE;
if (!isset($_COOKIE['tzo'])) {
    echo <<<EOE
      <script type="text/javascript">
        if (navigator.cookieEnabled) document.reload();
        else alert("Cookies must be enabled!");
      </script>
EOE;
}

ob_clean();
ob_start();
@session_start();
$GLOBALS['webinar_config'] = null;
$GLOBALS['ts'] = null;

/**
 * Plugin Name: Mattevideo - Webinar
 * Plugin URI: http://www.purelogics.net
 * Description: This plugin adds a Webinar feature where students can book teacher.
 * Version: 1.0.0
 * Author: Purelogics
 * Author URI: http://www.purelogics.net
 * License: GPL2
 */
require_once('class/webinar_admin.php');
require_once('model/model.php');
require_once('common_function.php');

if(!class_exists('TT_List_Table')){
    require_once( 'class/tt-list-table.php' );
}

class Webinar{
    public $pluginPath;
    public $pluginUrl;
    public $config;
    protected $model;

    public function __construct()
    {
        global $wpdb,$webinar_config, $ts;
        $this->pluginPath = dirname(__FILE__);
        $webinar_config = $this->config = json_decode(file_get_contents($this->pluginPath.'/config.json'));
        $this->pluginUrl = WP_PLUGIN_URL . '/'.$this->config->plugin_name.'/';
        $this->model = new Model();

        $webinar_admin = new Webinar_admin();
        add_action('admin_menu', array($webinar_admin, 'create_admin_menu'));
        add_shortcode('WEBINAR', array($this, 'shortcode_webinar'));
        register_activation_hook(__FILE__, array($this, 'plugin_activate'));
        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivate'));

        $ts = new DateTime('now', new DateTimeZone('GMT'));
        $ts->add(DateInterval::createFromDateString($_COOKIE['tzo'].' minutes'));
    }

    public function plugin_activate()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $categories_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_teacher_categories_table);
        $q1 = "CREATE TABLE `".$categories_table."` (
                `ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `name` VARCHAR( 255 ) NULL
                ) ENGINE = MYISAM";
        dbDelta($q1);

        $jobs_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_jobs_table);
        $q1 = "CREATE TABLE `".$jobs_table."` (
            `ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `webinar_date_time` DATETIME NULL ,
            `webinar_duration` VARCHAR( 10 ) NULL ,
            `webinar_description` TEXT NULL ,
            `webinar_education_level` VARCHAR( 15 ) NULL ,
            `webinar_teacher` VARCHAR( 15 ) NULL ,
            `webinar_budget` VARCHAR( 15 ) NULL,
            `webinar_user_id` INT( 11 ) NULL,
            `webinar_status` ENUM( 'posted', 'accepted', 'scheduled', 'in-progress', 'completed' ) NOT NULL DEFAULT 'posted'
            ) ENGINE = MYISAM";
        dbDelta($q1);

        $hearts_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_hearts_table);
        $q1 = "CREATE TABLE `".$hearts_table."` (
            `ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `webinar_id` INT( 11 ) NOT NULL ,
            `teacher_id` INT( 11 ) NOT NULL
            ) ENGINE = MYISAM";
        dbDelta($hearts_table);

        $offers_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_job_offers_table);
        $q1 = "CREATE TABLE `".$offers_table."` (
            `ID` INT( 11 ) NOT NULL AUTO_INCREMENT ,
            `job_id` INT( 11 ) NOT NULL ,
            `offer_amount` VARCHAR( 20 ) NOT NULL ,
            `offer_description` TEXT NOT NULL ,
            `offer_teacher_id` INT( 11 ) NOT NULL ,
            `offered_at` DATETIME NOT NULL ,
            `is_accepted` ENUM('0','1') NOT NULL ,
            `is_deleted` ENUM('0','1') NOT NULL ,
            PRIMARY KEY ( `ID` )
            ) ENGINE = MYISAM";
        dbDelta($q1);

        $rating_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_job_ratings_table);
        $q1 = "CREATE TABLE `".$rating_table."` (
            `ID` INT NOT NULL AUTO_INCREMENT ,
            `user_id` INT(11) NULL DEFAULT NULL ,
            `teacher_id` INT(11) NULL DEFAULT NULL ,
            `rating` DOUBLE(11,1) NULL DEFAULT NULL ,
            `job_id` INT NULL DEFAULT NULL ,
            `created_at` INT(11) NULL DEFAULT NULL , PRIMARY KEY (`ID`)
            ) ENGINE = MyISAM;";
        dbDelta($q1);
    }

    public function plugin_deactivate(){

    }

    public function load_teachers(){
        $education_level = $_REQUEST['education_level'];
        $teachers = $this->model->get_all_teachers($education_level);
        $html = '';
        foreach($teachers as $teacher):
            $html .= '<div class="col-md-3 w-profile-box" data-teacher-id="'.$teacher['ID'].'">
                <div class="teacher-image">
                    <img src="'.(get_user_meta($teacher['ID'], 'webinar_photo', true)?get_user_meta($teacher['ID'], 'webinar_photo', true):plugins_url('webinar/images/no_image.png', 'webinar')).'" style="" />
                </div>
                <div class="w-profile-caption">
                    <div class="w-profile-commets">
                        <span><i class="fa fa-comment" aria-hidden="true"></i>'.$this->model->get_completed_webinars_count($teacher['ID']).'</span>
                        <span><i class="fa fa-heart" aria-hidden="true"></i>'.round($this->model->get_average_rating($teacher['ID'])).'</span>
                    </div>
                    <p class="w-name">
                        <a href="'.home_url('studiekamerat?page=teacher&id='.$teacher['ID']).'" target="_blank">
                            '.(get_user_meta($teacher['ID'], 'first_name', true)?get_user_meta($teacher['ID'], 'first_name', true):$teacher['display_name']).' '. get_user_meta($teacher['ID'], 'last_name', true).'
                        </a>
                    </p>
                    <p>Se min video</p>
                    <p class="w-hidden"><a href="'.home_url('studiekamerat?page=teacher&id='.$teacher['ID']).'" target="_blank">See full profile</a></p>
                </div>
            </div>';
         endforeach;
        echo json_encode(array('teachers'=>$teachers, 'html'=>$html));
        exit;
    }

    public function rateTeacher(){
        $userID = get_current_user_id();
        if($userID){
            $teacherID = $_POST['teacherID'];
            $value = $_POST['value'];
            $job_id = $_POST['job_id'];
            $this->model->save_rating($userID, $teacherID, $value, $job_id);
            echo json_encode(array('success'=>'yes'));
        }else{
            echo json_encode(array('error'=>'Please login to rate.'));
        }
        exit;
    }

    public function shortcode_webinar(){
        $this->wp_plugin_style();
        $data = array();
        $is_user_teacher = get_user_meta(get_current_user_id(), 'webinar_role', true);
        if(!is_user_logged_in()/* || $is_user_teacher*/){
            wp_redirect(home_url());
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_job'){
            $verify_is_authorized = wp_verify_nonce( $_REQUEST['delete_job'], 'delete_job' );
            $job_details = $this->model->get_job_by_id($_REQUEST['job_id']);
            if($job_details) {
                $job_details = $job_details[0];
                if ($verify_is_authorized && $job_details->webinar_user_id == get_current_user_id()) {
                    $this->model->delete_job_by_id($_REQUEST['job_id']);
                    $data['success'] = "<strong>Well done!</strong> Job deleted successfully.";
                }else{
                    $data['error'] = "<strong>Oh snap!</strong> Owner of this job can only delete.";
                }
            }else{
                $data['error'] = "<strong>Oh snap!</strong> No job data found against your job_id.";
            }
        }
        $view = $_REQUEST['page'];
        if($view == 'pay') {
            $this->load_payment_form();
        }elseif($view == 'setup'){
            $this->setup();
        }else {
            $this->load_view($view, $data);
        }
    }

    public function setup(){
        ob_clean();
        ob_start();
        if(!empty($_POST)){
            global $allowedposttags;
            $allowedposttags['div'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'data-teacherid'=>array(), 'data-jobid'=>array(), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
            $allowedposttags['iframe'] = array('src' => array () , 'width'=>array(), 'height'=>array(), 'frameborder'=>array());
            $allowedposttags['script'] = array('src' => array () , 'type'=>array());
            $allowedposttags['p'] = array('class' => array () );
            $allowedposttags['style'] = array('type' => array () );
            $content = '<style>#teacher_rating{width: 100%;height: 20px;}</style>'.$_POST['teacher_email'].' - mob '.$_POST['mobile'].' - skype '.$_POST['skype_name'].' (evt skype knapp)<script type="text/javascript" src="https://secure.skypeassets.com/i/scom/js/skype-uri.js"></script><div id="SkypeButton_Call_button_1"><script type="text/javascript">Skype.ui({"name": "call","element": "SkypeButton_Call_button_1","participants": ["'.$_POST['skype_name'].'"]});</script></div><div id="teacher_rating"><div data-teacherid="'.get_current_user_id().'" data-jobid="'.$_REQUEST['job_id'].'" class="rateit"></div></div><p>&nbsp;</p><iframe width="890" height="420" frameborder="0" src="'.$_POST['drawing_url'].'"></iframe><p>&nbsp;</p><iframe width="890" height="800" frameborder="0" src="'.$_POST['text_url'].'"></iframe><p>&nbsp;</p><iframe width="890" height="500" frameborder="0" src="'.$_POST['meeting_url'].'"></iframe><p>&nbsp;</p><script type="text/javascript">/*we bind only to the rateit controls within the products div*/jQuery(document).ready(function(){jQuery("#teacher_rating .rateit").bind("rated reset", function (e) {var ri = $(this);/*if the use pressed reset, it will get value: 0 (to be compatible with the HTML range control), we could check if e.type == "reset", and then set the value to  null .*/var value = ri.rateit("value");var teacherID = ri.data("teacherid");var job_id = ri.data("jobid");/*maybe we want to disable voting?*/ri.rateit("readonly", true);jQuery.ajax({url: "'.admin_url("admin-ajax.php").'", data: { teacherID: teacherID, value: value, job_id: job_id, action: "rateit"},type: "POST",success: function (data) {},error: function (jxhr, msg, err) {}});});});</script>';
            $post_id = $_POST['post_id'];
            $my_post = new stdClass();
            $my_post->ID = $post_id;
            $my_post->post_content = $content;

            // Update the post into the database
            wp_update_post( $my_post , true );
            if (is_wp_error($post_id)) {
                $errors = $post_id->get_error_messages();
                foreach ($errors as $error) {
                    echo $error;
                }
                exit;
            }
            wp_redirect(home_url('studiekamerat?page=t_scheduled_sessions&scheduled=yes'));
            $offer_details = $this->model->get_scheduled_offer_by_job_id($_REQUEST['job_id']);
            $job_details = $this->model->get_job_by_id($_REQUEST['job_id']);
            $this->model->mark_setup($_REQUEST['job_id']);
            sendEmail($_REQUEST['job_id'], $offer_details->ID, $job_details[0]->webinar_user_id, get_current_user_id(), 'webinar_setup_complete');
            wp_redirect(home_url('studiekamerat?page=t_scheduled_sessions&scheduled=yes'));
        }else{
            $view = $_REQUEST['page'];
            $job_id = $_REQUEST['job_id'];
            $scheduled_sessions = $this->model->get_job_by_id($_REQUEST['job_id']);
            include $this->pluginPath.'/views/front/setup.php';
        }
        exit;
    }


    public function pay(){
        ob_clean();
        ob_start();
        if(isset($_POST) && !empty($_POST)){
            /**
             * Detect plugin. For use on Front End only.
             */
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            // check for plugin using plugin name
            if ( is_plugin_active( 'braintree-payment/index.php' ) ) {
                $status = $this->model->pay_using_braintree($_POST);
                if(isset($status['success'])) {
                    $offer_details = $this->model->get_offer_details_by_id($_POST['offer_id']);
                    sendEmail($_POST['job'], $_POST['offer_id'], get_current_user_id(), $offer_details->offer_teacher_id, 'offer_accepted');
                    sendEmail($_POST['job'], $_POST['offer_id'], get_current_user_id(), $offer_details->offer_teacher_id, 'payment_success');
                    $this->model->create_webinar_page($_POST['job'], $_POST['offer_id'], get_current_user_id(), $offer_details->offer_teacher_id);
                }
                echo json_encode($status);
                exit;
            }
        }else{
            $message['error'] = "<strong>Oh snap!</strong> Wrong request.";
            echo json_encode($message);
        }
        exit;
    }

    private function load_payment_form(){
        ob_clean();
        ob_start();
        $view = $_REQUEST['page'];
        $offer = $_REQUEST['offer'];
        $job = $_REQUEST['job'];
        $job_details = $this->model->get_job_by_id($job);
        $data = array();
        if($job_details){
            $job_details = $job_details[0];
            $offer_details = $this->model->get_offer_details_by_id($offer);
            if(!$offer_details->is_accepted) {
                $current_user_id = get_current_user_id();
                if ($current_user_id == $job_details->webinar_user_id) {
                    $user_info = get_userdata($current_user_id);
                    $profile_id = get_user_meta($current_user_id, 'braintree_id', true);
                    $model = $this->model;
                    include $this->pluginPath . '/views/front/' . $view . '.php';
                    exit;
                } else {
                    $data['error'] = '<strong>Oh snap!</strong> Only owner of the job can accept and pay.';
                }
            }else{
                $data['error'] = '<strong>Oh snap!</strong> Job is already paid.';
            }
        }else{
            $data['error'] = '<strong>Oh snap!</strong> Wrong job id.';
        }
        extract($data);
        include $this->pluginPath.'/views/front/error.php';
        exit;
    }

    private function load_view($view, $data=array()) {
        global $wpdb;
        if($view == null OR $view == "") {
            $view = 'post_webinar';
        }
        
        if($view == 'post_webinar' && !$_POST) {
            $webinar_durations = (array)$this->config->plugin_webinar_durations;
            $webinar_budget = (array)$this->config->plugin_webinar_budget;
            $education_levels = $this->model->get_all_categories();
        }else if($view == 'teacher' && !$_POST) {
            $teacher = get_userdata($_REQUEST['id']);
            if(!$teacher){
                wp_redirect(home_url());
            }
            $is_user_teacher = get_user_meta($teacher->ID, 'webinar_role', true);
            if(!$is_user_teacher){
                wp_redirect(home_url());
            }
        }else if(isset($_POST['action']) && $_POST['action'] == 'addwebinar'){
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            $files = $_FILES['webinar_files'];
            $_POST['webinar_files'] = array();
            
            $delArray = array();
            $deleteFiles = explode("<=>", $_POST['deleteFile']);
            
            foreach($deleteFiles AS $value) {
                $delArray[] = $value; 
            }
            
            $upload_overrides = array( 'test_form' => false );
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    if(!in_array($files['name'][$key], $delArray)) {
                        $uploadedfile = array(
                            'name' => $files['name'][$key],
                            'type' => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error' => $files['error'][$key],
                            'size' => $files['size'][$key]
                        );
                        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                        if ($movefile && !isset($movefile['error'])) {
                            $_POST['webinar_files'][] = $movefile['url'];
                        }
                    }    
                }
            }
            $_POST['webinar_files'] = json_encode($_POST['webinar_files']);
            $saved = $this->model->save_webinar_request($_POST);
            sendEmail($saved, 0, get_current_user_id(), $_POST['teacher'], 'job_posted');
            if($saved){
                wp_redirect('/studiekamerat?page='.$this->config->webinar_posted_jobs_page.'&add=yes');
            }else{
                wp_redirect('/studiekamerat?page='.$this->config->webinar_posted_jobs_page.'&add=no');
            }
        }else if($view == 'my_jobs'){
            $my_jobs = $this->model->get_my_jobs();
            if(is_super_admin() && isset($_REQUEST['job_id'])){
                $my_jobs = $this->model->get_job_by_id($_REQUEST['job_id']);
            }
        }else if($view == 'past_due'){
            $past_due = $this->model->get_past_due_jobs();
            if(is_super_admin() && isset($_REQUEST['job_id'])){
                $past_due = $this->model->get_job_by_id($_REQUEST['job_id']);
            }
        }else if($view == 'finished_sessions'){
            $finished_sessions = $this->model->get_finished_sessions();
            if(is_super_admin() && isset($_REQUEST['job_id'])){
                $finished_sessions = $this->model->get_job_by_id($_REQUEST['job_id']);
            }
        }else if($view == 'scheduled_sessions'){
            $scheduled_sessions = $this->model->get_scheduled_sessions();
            if(is_super_admin() && isset($_REQUEST['job_id'])){
                $scheduled_sessions = $this->model->get_job_by_id($_REQUEST['job_id']);
            }
        }else if($view == 't_scheduled_sessions'){
            $scheduled_sessions = $this->model->get_teacher_scheduled_sessions();
            if(is_super_admin() && isset($_REQUEST['job_id'])){
                $scheduled_sessions = $this->model->get_job_by_id($_REQUEST['job_id']);
            }
        }else if($view == 't_finished_sessions'){
            $finished_sessions = $this->model->get_teacher_finished_sessions();
            if(is_super_admin() && isset($_REQUEST['job_id'])){
                $finished_sessions = $this->model->get_job_by_id($_REQUEST['job_id']);
            }
        }else if($view == 'available_jobs' && !isset($_REQUEST['action'])){
            $available_jobs = $this->model->get_available_jobs();
        }elseif(isset($_POST['action']) && $_POST['action'] == 'add_offer'){
            $saved = $this->model->save_offer($_POST);
            $job_details = $this->model->get_job_by_id($_REQUEST['job_id']);
            sendEmail($_REQUEST['job_id'], $saved, $job_details[0]->webinar_user_id, get_current_user_id(), 'offer_recieved');
            if($saved){
                $data['success'] = "<strong>Well done!</strong> Offer created successfully.";
            }else{
                $data['error'] = '<strong>Oh snap!</strong> Unable to save offer.';
            }
            $available_jobs = $this->model->get_available_jobs();
        }elseif(isset($_POST['action']) && $_POST['action'] == 'update_offer'){
            $saved = $this->model->update_offer_by_id($_POST);
            if($saved){
                $data['success'] = "<strong>Well done!</strong> Offer updated successfully.";
            }else{
                $data['error'] = '<strong>Oh snap!</strong> Unable to update offer.';
            }
            $available_jobs = $this->model->get_available_jobs();
        }elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_offer') {
            $deleted = $this->model->delete_offer_by_id($_REQUEST);
            if($deleted){
                $data['success'] = "<strong>Well done!</strong> Offer deleted successfully.";
            }else{
                $data['error'] = '<strong>Oh snap!</strong> Unable to delete offer, offer may be accepted by student.';
            }
            $available_jobs = $this->model->get_available_jobs();
        } else if($view == 'group_webinar' && $_POST) {
            $saved = $this->model->save_exercise_group($_POST);
            $user = WP_User::get_data_by('email', $data['email_address']);
            if ($saved) {
                sendEmail($saved, 0, $user->ID, get_current_user_id(), 'job_posted', 'group');
                $data['success'] = "<strong>Great!</strong> Your package deal was successfully sent";
            } else {
                $data['error'] = '<strong>Oh snap!</strong> Unable to send offer.';
            }
        } else if($view == 'group_webinars'){
            $teacher_groups = $this->model->get_teacher_groups();
            global $webinar_config;
            $teacher_budget = $webinar_config->teacher_budget;
        } else {
            $meta_value = trim($view);
            
            $result = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE meta_value='{$meta_value}' ");
            if(count($result) > 0 && $result !=null) {
                $view = "teacher";
                $teacher = get_userdata($result->user_id);
                if(!$teacher) {
                    wp_redirect(home_url());
                }
                $is_user_teacher = get_user_meta($teacher->ID, 'webinar_role', true);
                if(!$is_user_teacher){
                    wp_redirect(home_url());
                }
            }
        }
        
        $model = $this->model;
        extract($data);
        $view = $view;
        $is_user_teacher = get_user_meta(get_current_user_id(), 'webinar_role', true);
        if(!$is_user_teacher) {
            include $this->pluginPath . '/views/front/header-top.php';
        }else{
            include $this->pluginPath . '/views/front/header-teacher.php';
        }
        include $this->pluginPath.'/views/front/'.$view.'.php';
    }

    public function wp_plugin_style() {
        wp_enqueue_style( 'webinar-style', $this->pluginUrl.'css/style.css' );
        
        wp_enqueue_script( 'webinar-script', $this->pluginUrl.'js/script.js', array(), null, true );
        //wp_enqueue_style( 'jquery-datetimepicker-style', $this->pluginUrl.'js/datetimepicker/src/jquery-ui-timepicker-addon.css' );
        wp_enqueue_style( 'jquery-datetimepicker-style', $this->pluginUrl.'js/new-bootstrap-datetimepicker/src/css/bootstrap-datetimepicker.css', array());
        
        wp_enqueue_style( 'jquery-validationEngine-style', $this->pluginUrl.'js/jQuery-Validation-Engine/css/validationEngine.jquery.css' );
        wp_enqueue_style( 'jquery-rateit-style', $this->pluginUrl.'js/gjunge-rateit/scripts/rateit.css' );

        wp_enqueue_script( 'jquery-moment-script', $this->pluginUrl.'js/new-bootstrap-datetimepicker/moment.min.js' , array());
        wp_enqueue_script( 'jquery-datetimepicker-script', $this->pluginUrl.'js/new-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js' , array());
        wp_enqueue_script( 'jquery-validationEngine-en-script', $this->pluginUrl.'js/jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js' );
        wp_enqueue_script( 'jquery-validationEngine-script', $this->pluginUrl.'js/jQuery-Validation-Engine/js/jquery.validationEngine.js' );
        
# Source of Rateit Library https://github.com/gjunge/rateit.js Examples here http://gjunge.github.io/rateit.js/examples/
        wp_enqueue_script( 'jquery-rateit-script', $this->pluginUrl.'js/gjunge-rateit/scripts/jquery.rateit.min.js' );
        //wp_enqueue_script( 'jquery-fancybox-script', $this->pluginUrl.'js/fancyapps-fancyBox/source/jquery.fancybox.pack.js?v=2.1.5' );
    }

}

if(!function_exists('webinar_ajaxurl')){
    function webinar_ajaxurl() {?>
        <script type="text/javascript">
            var WebinarAjaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    }
}

$webinar = new Webinar();
$GLOBALS['webinar'] = $webinar;

add_action('init', 'custom_rewrite_basic', 10, 0);
if(!function_exists('custom_rewrite_basic')){
    function custom_rewrite_basic() {
        //Ensure the $wp_rewrite global is loaded
        global $wp_rewrite;
        add_rewrite_tag('%page%','([^&]+)');
        add_rewrite_rule('^studiekamerat/(.*)/?$', 'index.php/studiekamerat?page=$1', 'top');
        //Call flush_rules() as a method of the $wp_rewrite object
        $wp_rewrite->flush_rules( true );
    }
}

add_action('wp_head', 'wp_rating_style', 10, 0);
if(!function_exists('wp_rating_style')) {
    function wp_rating_style() {
        global $webinar;
        wp_enqueue_style( 'jquery-rateit-style', $webinar->pluginUrl.'js/gjunge-rateit/scripts/rateit.css' );
        wp_enqueue_script( 'jquery-rateit-script', $webinar->pluginUrl.'js/gjunge-rateit/scripts/jquery.rateit.min.js' );
    }
}

apply_filters( 'show_password_fields', true );
add_action( 'user_new_form', 'webinar_add_registration_fields' );
add_action( 'show_user_profile', 'webinar_add_registration_fields' );
add_action( 'edit_user_profile', 'webinar_add_registration_fields' );
add_filter( 'user_register', 'webinar_registration_save', 10, 1 );
add_filter( 'edit_user_profile_update', 'webinar_registration_save', 10, 1 );
add_action('wp_head','webinar_ajaxurl');
# Hooks for the Loading Teachers
add_action( 'wp_ajax_nopriv_loadteachers', array($webinar, 'load_teachers') );
add_action( 'wp_ajax_loadteachers', array($webinar, 'load_teachers') );

add_action( 'wp_ajax_nopriv_rateit', array($webinar, 'rateTeacher') );
add_action( 'wp_ajax_rateit', array($webinar, 'rateTeacher') );

add_action( 'wp_ajax_pay', array($webinar, 'pay') );