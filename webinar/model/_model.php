<?php

class Model{

    private $db;
    private $config;
    private $jobs_table,$categories_table,$hearts_table,$offers_table,$ratings_table;
    private $env = 'sandbox';
    private $merchent_id = '';
    private $public_key = '';
    private $private_key = '';
    private $cse_key = '';

    function Model(){
        global $wpdb, $webinar_config;
        $this->config = $webinar_config;
        $this->db = $wpdb;
        $env = $this->env;
        $this->categories_table = str_replace('[WPDBPREFIX]', $this->db->prefix, $this->config->plugin_teacher_categories_table);
        $this->jobs_table = str_replace('[WPDBPREFIX]', $this->db->prefix, $this->config->plugin_jobs_table);
        $this->hearts_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_hearts_table);
        $this->offers_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_job_offers_table);
        $this->ratings_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_job_ratings_table);
        $settings = (array)$this->config->$env;
        $this->merchent_id = $settings['merchant_id'];
        $this->public_key = $settings['public_key'];
        $this->private_key = $settings['private_key'];
        $this->cse_key = $settings['cse_key'];
    }

    public function save_category($post){
        $verify_is_authorized = wp_verify_nonce( $_REQUEST['createcategory_nounce'], 'createcategory_nounce' );
        if($verify_is_authorized){
            $this->db->query($this->db->prepare(
                "INSERT INTO ".$this->categories_table." SET name=%s",
                $post['name']
            ));
            return true;
        }else{
            return false;
        }
    }

    public function update_category($post){
        $verify_is_authorized = wp_verify_nonce( $_REQUEST['updatecategory_nounce'], 'updatecategory_nounce' );
        if($verify_is_authorized){
            $this->db->query(
                $this->db->prepare(
                    "UPDATE ".$this->categories_table." SET name=%s WHERE ID=%d",
                    $post['name'],
                    $post['ID']
                )
            );
            return true;
        }else{
            return false;
        }
    }

    public function get_category_date_by_id($id, $object=false){
        $result = $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->categories_table WHERE ID=%d",
                $id
            )
        );
        if($object){
            if($result)
                return $result[0];
            else
                return null;
        }else{
            if($result)
                return (array)$result[0];
            else
                return null;
        }
    }

    public function delete_category_by_id($id){
        $verify_is_authorized = wp_verify_nonce( $_REQUEST['deletecategory_nounce'], 'deletecategory_nounce' );
        if($verify_is_authorized) {
            $this->db->delete( $this->categories_table, array( 'ID' => $id ) );
            return true;
        }else{
            return false;
        }
    }

    public function delete_category_by_ids($ids){
        $verify_is_authorized = true;
        if($verify_is_authorized) {
            $this->db->query("DELETE FROM $this->categories_table WHERE ID IN(".implode(',', $ids).")");
            //$this->db->delete( $this->categories_table, array( 'ID' => $ids ) );
            return true;
        }else{
            return false;
        }
    }

    public function get_all_categories($object=false){
        $result = $this->db->get_results("SELECT * FROM $this->categories_table",
            $object?OBJECT:ARRAY_A
        );
        return $result;
    }

    public function get_all_teachers($education_level='', $id=''){
        if($education_level == '' OR $education_level == 'all'){
            extract( array(
                'query_id' => 'webinar_teacher_listing',
                'role' => 'Teacher',
                'include' => '',
                'exclude' => '',
                'blog_id' => '',
                'number' => true,
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_key' => '',
                'meta_value' => '',
                'meta_compare' => '=',
                'meta_type' => 'CHAR',
                'count_total' => true,
                'ID' => $id
            ));
            $number = intval( $number );
            ob_start();
            if($id == '') {
                $args = array(
                    'orderby' => $orderby,
                    'order' => $order,
                    'role' => $role
                );
            }else{
                $args = array(
                    'orderby' => $orderby,
                    'order' => $order,
                    'role' => $role,
                    'include' => $ID
                );
            }
        }else{
            extract( array(
                'query_id' => 'webinar_teacher_listing',
                'role' => 'Teacher',
                'include' => '',
                'exclude' => '',
                'blog_id' => '',
                'number' => true,
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_key' => 'webinar_education',
                'meta_value' => $education_level,
                'meta_compare' => 'like',
                'meta_type' => 'CHAR',
                'count_total' => true,
            ));
            $number = intval( $number );
            ob_start();
            $args = array(
                'orderby' => $orderby,
                'order' => $order,
                'role' => $role,
                'meta_key'=>$meta_key,
                'meta_value'=>$meta_value,
                'meta_compare'=>'like'
            );
        }
        $number = intval( $number );
        ob_start();

        wp_reset_query();
        $webinar_users = new WP_User_Query( $args );
        /*echo "<pre>";
        //.' AND (meta_key = "webinar_education" AND FIND_IN_SET("'.$education_level.'", met_value))'
        print_r($webinar_users->query_where);
        echo "</pre>";
        exit;*/
        $data = array();
        $count = 0;
        foreach($webinar_users->get_results() as $user){
            $data[$count] = (array)$user->data;
            $data[$count]['first_name'] = (get_user_meta($data[$count]['ID'], 'first_name', true)?get_user_meta($data[$count]['ID'], 'first_name', true):$data[$count]['display_name']);
            $data[$count]['last_name'] = (get_user_meta($data[$count]['ID'], 'last_name', true)?get_user_meta($data[$count]['ID'], 'last_name', true):'');
            $count++;
        }
        wp_reset_query();
        return $data;
    }

    public function get_webinars_list(){
        return $this->db->get_results( 'SELECT * FROM '.$this->jobs_table, ARRAY_A );
    }

    public function save_webinar_request($post){
        $verify_is_authorized = wp_verify_nonce( $_REQUEST['add_webinar'], 'add_webinar' );
        if($verify_is_authorized){
            $data = array(
                'webinar_date_time'=>$post['date_time'],
                'webinar_duration'=>$post['duration'],
                'webinar_description'=>nl2br($post['description']),
                'webinar_education_level'=>$post['webinar_education_level'],
                'webinar_teacher'=>$post['teacher'],
                'webinar_budget'=>$post['budget'],
                'webinar_user_id'=>get_current_user_id(),
                'webinar_files'=>$post['webinar_files'],
                'webinar_status'=>'posted'
            );
            if($this->db->insert($this->jobs_table, $data))
                return $this->db->insert_id;
            else
                return false;
        }else{
            return false;
        }
    }

    public function delete_user_by_id($id){
        return wp_delete_user($id);
    }

    private function delete_webinar_teacher_id($teacher_id){
        $this->db->delete( $this->jobs_table, array('webinar_teacher'=>$teacher_id) );
    }

    public function completed_webinars($teacher_id){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." WHERE webinar_status='completed' AND (webinar_teacher = 'all' OR webinar_teacher = '".$teacher_id."')");
    }

    public function count_user_completed_webinars(){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." WHERE webinar_status='completed' AND webinar_user_id = '".get_current_user_id()."'");
    }

    public function count_user_pastdue_webinars(){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." WHERE webinar_status='posted' AND webinar_user_id = '".get_current_user_id()."' AND UNIX_TIMESTAMP(webinar_date_time) < ".strtotime('NOW'));
    }

    public function count_user_posted_webinars(){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." WHERE webinar_status='posted' AND webinar_user_id = '".get_current_user_id()."' AND UNIX_TIMESTAMP(webinar_date_time) > ".strtotime('NOW'));
    }

    public function count_user_scheduled_webinars(){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." WHERE webinar_status='scheduled' AND webinar_user_id = '".get_current_user_id()."' AND UNIX_TIMESTAMP(webinar_date_time) >= ".strtotime('NOW'));
    }

    public function hearts($teacher_id){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->hearts_table." WHERE teacher_id='".$teacher_id."'");
    }

    public function get_my_jobs(){
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->jobs_table WHERE webinar_user_id=%d AND webinar_status=%s AND UNIX_TIMESTAMP(webinar_date_time) >=%d ORDER BY ID DESC",
                get_current_user_id(),
                'posted',
                strtotime('NOW')
            )
        );
    }

    public function get_past_due_jobs(){
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->jobs_table WHERE webinar_user_id=%d AND webinar_status=%s AND UNIX_TIMESTAMP(webinar_date_time) < %d ORDER BY ID DESC",
                get_current_user_id(),
                'posted',
                strtotime('NOW')
            )
        );
    }

    public function get_finished_sessions(){
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->jobs_table WHERE webinar_user_id=%d AND webinar_status=%s ORDER BY ID DESC",
                get_current_user_id(),
                'completed'
            )
        );
    }

    public function get_scheduled_sessions(){
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->jobs_table WHERE webinar_user_id=%d AND webinar_status=%s ORDER BY ID DESC",
                get_current_user_id(),
                'scheduled'
            )
        );
    }

    public function get_job_by_id($job_id){
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->jobs_table WHERE ID=%d ORDER BY ID DESC",
                $job_id
            )
        );
    }

    public function get_offers_by_id($job_id){
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->offers_table WHERE job_id=%d ORDER BY ID,is_accepted DESC",
                $job_id
            )
        );
    }

    public function get_teacher_avatar($teacher_id){
        return get_user_meta($teacher_id, 'webinar_photo', true);
    }

    public function delete_job_by_id($job_id){
        $this->db->delete( $this->jobs_table, array( 'ID' => $job_id ) );
        $this->delete_offers_by_job_id($job_id);
    }

    public function delete_offers_by_job_id($job_id){
        $this->db->delete( $this->offers_table, array( 'job_id' => $job_id ) );
    }

    public function get_teacher_name($teacher_id){
        return get_user_meta($teacher_id, 'first_name', true).' '.get_user_meta($teacher_id, 'last_name', true);
    }

    public function get_offer_details_by_id($offer_id){
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM $this->offers_table WHERE ID=%d",
                $offer_id
            )
        );
    }

    public function pay_using_braintree($post){
        require_once 'braintree-php/vendor/autoload.php';
        Braintree_Configuration::environment($this->env);
        Braintree_Configuration::merchantId($this->merchant_id);
        Braintree_Configuration::publicKey($this->public_key);
        Braintree_Configuration::privateKey($this->private_key);
        //$post['existing'] = 58798868;
        $customer_id = 0;
        $token = '';
        if($post['existing']) {
            $customer_id = $post['existing'];
            $card = Braintree_CreditCard::create([
                'customerId' => $customer_id,
                'cardholderName' => $post['first_name'].' '.$post['last_name'],
                'number' => $post['number'],
                'expirationDate' => $post['month'].'/'.$post['exp-year'],
                'cvv' => $post['cvv']
            ]);
            if($card->success) {
                $token = $card->creditCard->token;
            }
        }else{
            $card = Braintree_Customer::create(array(
                "firstName" => $post["first_name"],
                "lastName" => $post["last_name"],
                "creditCard" => array(
                    "number" => $post["number"],
                    "expirationMonth" => $post["month"],
                    "expirationYear" => $post["exp-year"],
                    "cvv" => $post["cvv"]

                )
            ));
            if ($card->success) {
                $customer_id = $card->customer->id;
                $token = $card->customer->creditCards[0]->token;
                update_user_meta(get_current_user_id(), 'braintree_id', $customer_id);
            }
        }
        $message = array();
        if($customer_id){
            $result = Braintree_Transaction::sale([
                'amount'=>$post['amount'],
                'paymentMethodToken'=>$token
            ]);
            if($result->success){
                $message['success'] = "<strong>Well done!</strong> Payment is successfully made.";
                $this->mark_paid($post['offer_id'], $post['job']);
            }else{
                $message['error'] = "";
                if($result->errors->deepAll()){
                    foreach (($result->errors->deepAll()) as $error) {
                        $message['error'] .= "<p>". $error->message . "</p>";
                    }
                }
            }
        }else{
            $message['error'] = "<strong>Oh snap!</strong> Braintree customer id is wrong or null.";
        }
        return $message;
    }

    private function mark_paid($offer_id, $job_id){
        $this->db->query(
            $this->db->prepare(
                "UPDATE ".$this->offers_table." SET is_accepted=%s WHERE ID=%d",
                '1',
                $offer_id
            )
        );
        $this->db->query(
            $this->db->prepare(
                "UPDATE ".$this->jobs_table." SET webinar_status=%s WHERE ID=%d",
                'scheduled',
                $job_id
            )
        );
    }


    public function count_available_jobs(){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." WHERE webinar_status='posted' AND (webinar_teacher = '".get_current_user_id()."' OR webinar_teacher='all') AND UNIX_TIMESTAMP(webinar_date_time) > ".strtotime('NOW'));
    }

    public function get_available_jobs(){
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM $this->jobs_table WHERE (webinar_teacher=%d OR webinar_teacher=%s) AND webinar_status=%s AND UNIX_TIMESTAMP(webinar_date_time) > %d ORDER BY ID DESC",
                get_current_user_id(),
                'all',
                'posted',
                strtotime('NOW')
            )
        );
    }

    public function save_offer($post){
        $verify_is_authorized = wp_verify_nonce( $_REQUEST['add_offer'], 'add_offer' );
        if($verify_is_authorized) {
            $data = array(
                'offer_amount' => $post['offer_amount'],
                'offer_description' => nl2br($post['offer_description']),
                'job_id' => $post['job_id'],
                'offer_teacher_id' => get_current_user_id(),
                'offered_at' => date('Y-m-d H:i:s'),
                'is_accepted' => '0',
                'is_deleted' => '0'
            );
            $this->db->insert($this->offers_table, $data);
            return $this->db->insert_id;
        }else{
            return false;
        }
    }

    public function get_offer_details_by_teacher_id($job_id){
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM $this->offers_table WHERE job_id=%d AND offer_teacher_id=%d AND is_deleted=%d",
                $job_id,
                get_current_user_id(),
                1
            )
        );
    }

    public function update_offer_by_id($post){
        $verify_is_authorized = wp_verify_nonce( $_POST['update_offer'], 'update_offer' );
        if($verify_is_authorized){
            return $this->db->query(
                $this->db->prepare(
                    "UPDATE ".$this->offers_table." SET offer_amount=%s, offer_description=%s WHERE ID=%d",
                    $post['offer_amount'],
                    $post['offer_description'],
                    $post['offer_id']
                )
            );
        }else{
            return false;
        }
    }

    public function delete_offer_by_id($post){
        $verify_is_authorized = wp_verify_nonce( $post['delete_offer'], 'delete_offer' );
        if($verify_is_authorized){
            $offer_details = $this->get_offer_details_by_id($post['offer_id']);
            if($offer_details && !$offer_details->is_accepted) {
                return $this->db->query(
                    $this->db->prepare(
                        "UPDATE " . $this->offers_table . " SET is_deleted=%s WHERE ID=%d",
                        '1',
                        $post['offer_id']
                    )
                );
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    public function getAllTeachers(){
        extract( array(
            'query_id' => 'webinar_teacher_listing',
            'role' => 'Teacher',
            'include' => '',
            'exclude' => '',
            'blog_id' => '',
            'number' => get_option( 'posts_per_page', 10 ),
            'order' => 'ASC',
            'orderby' => 'login',
            'meta_key' => '',
            'meta_value' => '',
            'meta_compare' => '=',
            'meta_type' => 'CHAR',
            'count_total' => true,
        ));

        $number = intval( $number );

        // We're outputting a lot of HTML, and the easiest way
        // to do it is with output buffering from PHP.
        ob_start();

        // Get the Search Term
        $search = ( isset( $_GET['as'] ) ) ? sanitize_text_field( $_GET['as'] ) : false ;

        // Get Query Var for pagination. This already exists in WordPress
        $page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' )  : 1;

        // Calculate the offset (i.e. how many users we should skip)
        $offset = ( $page - 1 ) * $number;

        // args
        $args = array(
            'query_id' => $query_id,
            'offset' => $offset,
            'number' => $number,
            'orderby' => $orderby,
            'order' => $order,
            'count_total' => $count_total,
            'role' => $role
        );
        $webinar_users = new WP_User_Query( $args );
        $data = array();
        foreach($webinar_users->get_results() as $user){
            $data[] = (array)$user->data;
        }
        return $data;
    }

    public function create_webinar_page($job_id, $offer_id, $user_id, $teacher_id){
        $user_info = get_userdata($user_id);
        $teacher_info = get_userdata($teacher_id);
        $offer_details = $this->get_offer_details_by_id($offer_id);
        $t_first_name = get_user_meta($teacher_id, 'first_name', true);
        $t_last_name = get_user_meta($teacher_id, 'last_name', true);
        $first_name = get_user_meta($user_id, 'first_name', true);
        $last_name = get_user_meta($user_id, 'last_name', true);
        $post = array(
            'post_author' => 2,
            'post_content' => '<div class="col-md-12"></div>',
            'post_name' =>  "Webinar setup, Teacher: ".($t_first_name?$t_first_name.' '.$t_last_name:$teacher_info->display_name)." Student: ".$first_name.' '.$last_name,
            'post_status' => 'publish',
            'post_title' => "Webinar setup, Teacher: ".($t_first_name?$t_first_name.' '.$t_last_name:$teacher_info->display_name)." Student: ".$first_name.' '.$last_name,
            'post_type' => 'page',
            'post_parent' => 0,
            'menu_order' => 0,
            'to_ping' =>  '',
            'pinged' => '',
            'page_template'=>'standar-notitle-page.php'
        );
        $post_id = wp_insert_post($post);
        $webinar_url = get_permalink($post_id);
        $this->update_webinar_url($job_id, $webinar_url);
        sendEmail($job_id, $offer_id, $user_id, $teacher_id, 'webinar_setup_teacher');
        sendEmail($job_id, $offer_id, $user_id, $teacher_id, 'webinar_setup_student');
    }

    public function update_webinar_url($job_id, $webinar_url){
        return $this->db->query(
            $this->db->prepare(
                "UPDATE ".$this->jobs_table." SET webinar_url=%s WHERE ID=%d",
                $webinar_url,
                $job_id
            )
        );
    }


    public function count_teacher_scheduled_webinars(){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." j JOIN ".$this->offers_table." o ON j.ID=o.job_id WHERE j.webinar_status='scheduled' AND offer_teacher_id= '".get_current_user_id()."' AND is_accepted='1'");
    }

    public function count_teacher_completed_webinars(){
        return $this->db->get_var("SELECT COUNT(*) AS CNT FROM ".$this->jobs_table." j JOIN ".$this->offers_table." o ON j.ID=o.job_id WHERE j.webinar_status='completed' AND offer_teacher_id= '".get_current_user_id()."' AND is_accepted='1'");
    }

    public function get_teacher_scheduled_sessions(){
        return $this->db->get_results("SELECT j.* FROM ".$this->jobs_table." j JOIN ".$this->offers_table." o ON j.ID=o.job_id WHERE j.webinar_status='scheduled' AND offer_teacher_id= '".get_current_user_id()."' AND is_accepted='1'");
    }

    public function get_teacher_finished_sessions(){
        return $this->db->get_results("SELECT j.* FROM ".$this->jobs_table." j JOIN ".$this->offers_table." o ON j.ID=o.job_id WHERE j.webinar_status='completed' AND offer_teacher_id= '".get_current_user_id()."' AND is_accepted='1'");
    }

    public function get_scheduled_offer_by_job_id($job_id){
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM $this->offers_table WHERE job_id=%d AND is_accepted=%s AND offer_teacher_id=%d",
                $job_id,
                '1',
                get_current_user_id()
            )
        );
    }

    public function mark_setup($job_id){
        return $this->db->query(
            $this->db->prepare(
                "UPDATE ".$this->jobs_table." SET is_setup=%s WHERE ID=%d",
                "1",
                $job_id
            )
        );
    }

    public function save_rating($userID, $teacherID, $value, $job_id){
        $exists = $this->db->get_row($this->db->prepare("SELECT * FROM $this->ratings_table WHERE job_id=%d AND user_id=%d AND teacher_id=%d", $job_id, $userID, $teacherID));
        if($exists){
            $this->db->query(
                $this->db->prepare(
                    "UPDATE ".$this->ratings_table." SET rating=%s WHERE ID=%d",
                    $value,
                    $exists->ID
                )
            );
        }else {
            $data = array('user_id' => $userID, 'teacher_id' => $teacherID, 'rating' => $value, 'job_id' => $job_id, 'created_at' => time());
            $this->db->insert($this->ratings_table, $data);
        }
        return $this->db->query(
            $this->db->prepare(
                "UPDATE ".$this->jobs_table." SET webinar_status=%s WHERE ID=%d",
                "completed",
                $job_id
            )
        );
    }

    public function get_average_rating($teacher_id){
        return $this->db->get_var($this->db->prepare("SELECT AVG(rating) as avg_rating FROM `".$this->ratings_table."` WHERE teacher_id=%d GROUP BY teacher_id", $teacher_id));
    }

    public function get_completed_webinars_count($teacher_id){
        $prepare = $this->db->prepare("SELECT COUNT(*) FROM $this->jobs_table as j JOIN $this->offers_table o ON j.ID=o.job_id WHERE o.is_accepted='1' AND o.offer_teacher_id=%d", $teacher_id);
        return $this->db->get_var($prepare);
    }

    public function get_teacher_by_id(){

    }

}