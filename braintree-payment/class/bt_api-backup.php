<?php

function get_settings_2(){
    global $wpdb;
    $table = $wpdb->prefix . "braintree_setting";
    $q = "SELECT * FROM ".$table." LIMIT 1 ";
    return $wpdb->get_row($q);
}
/**
 * Description of braintree_api
 *
 * @author
 */
class braintree_api {
    public $env = 'sandbox';
    public $merchent_id = '';
    public $public_key = '';
    public $private_key = '';
    public $cse_key = '';

    private $plan_name = 'mattevideo';
    public function __construct()
    {

        $setting = get_settings_2();
        if($setting){
            $this->merchent_id = $setting->merchant_id;
            $this->public_key = $setting->public_key;
            $this->private_key = $setting->private_key;
            $this->cse_key = $setting->cse_key;
            if($setting->sandbox == '1'){
                $this->env = 'sandbox';
                $this
            }
            else{
                $this->env = 'production';
            }
        }



        Braintree_Configuration::environment($this->env);
        Braintree_Configuration::merchantId($this->merchent_id);
        Braintree_Configuration::publicKey($this->public_key);
        Braintree_Configuration::privateKey($this->private_key);
    }

    public function create_user_profile($data){
        $result = Braintree_Customer::create(array(
            "firstName" => $data["first_name"],
            "lastName" => $data["last_name"],
            "creditCard" => array(
                "number" => $data["number"],
                "expirationMonth" => $data["month"],
                "expirationYear" => $data["year"],
                "cvv" => $data["cvv"]

            )
        ));

        if ($result->success) {
            $status = 'ok';
            $data = $result->customer->id;
        } else {
            $message = '';
            foreach (($result->errors->deepAll()) as $error) {
                $message .= "<p>". $error->message . "</p>";
            }
            $status = 'error';
            $data = $message;
        }
        return array('status' => $status , 'data' => $data);
    }

    public function update_user_profile($braintree_id, $data){
        $customer = Braintree_Customer::find($braintree_id);
        $token = $customer->creditCards[0]->token;
        $result = Braintree_Customer::update($braintree_id ,
            array(
                "firstName" => $data["first_name"],
                "lastName" => $data["last_name"],
                "creditCard" => array(
                    "number" => $data["number"],
                    "expirationMonth" => $data["month"],
                    "expirationYear" => $data["year"],
                    "cvv" => $data["cvv"],
                    'options' => array(
                        'updateExistingToken' => $token
                    )

                )
            ));


        if ($result->success) {
            $status = 'ok';
            $data = $result->customer->id;
        } else {
            $message = '';
            foreach (($result->errors->deepAll()) as $error) {
                $message .= "<p>". $error->message . "</p>";
            }
            $status = 'error';
            $data = $message;
        }
        return array('status' => $status , 'data' => $data);
    }

    public function retryCharge($subscription_id=0){
        $status = 'error';
        $message = '';
        $data = array();
        if($subscription_id){
            $result = Braintree_Subscription::retryCharge($subscription_id);
            if ($result->success & $result->success != ''){
                $status = 'ok';
                $message = 'Charged successfully';
            }else{
                $message = $result->message;
            }
        }else{
            $status = 'error';
        }
        return array('status' => $status, 'message'=>$message);
    }

    public function subscribe_user_to_plan($customer_id){
        try {
            $customer = Braintree_Customer::find($customer_id);
            $payment_method_token = $customer->creditCards[0]->token;

            $result = Braintree_Subscription::create(array(
                'paymentMethodToken' => $payment_method_token,
                'planId' => $this->plan_name,
                'options' => array('startImmediately' => true)
            ));

            if ($result->success) {
                //echo("Success! Subscription " . $result->subscription->id . " is " . $result->subscription->status);
                $status = 'ok';
                $data['subscription_id'] = $result->subscription->id;
                $data['subscription_status'] = $result->subscription->status;
                $data['result'] = $result;
            }
            else {

                $message = '';
                if($result->errors->deepAll()){
                    foreach (($result->errors->deepAll()) as $error) {
                        $message .= "<p>". $error->message . "</p>";
                    }
                }
                if($result->transaction->status != ""){
                    $message .= "<p> Transaction is declined ". $result->transaction->status . "</p>";
                }
                $status = 'error';
                $data = $message;
            }
        } catch (Braintree_Exception_NotFound $e) {
            // echo("Failure: no customer found with ID " . $customer_id);
            $status = 'error';
            $data = "Failure: no customer found with ID " . $customer_id;
        }
        return array('status' => $status , 'data' => $data);
    }

    public function cancel_user_subscription($subscription_id){

        $result = Braintree_Subscription::cancel($subscription_id);
        if ($result->success) {
            $status = 'ok';
            $data['subscription_id'] = $subscription_id;
            $data['subscription_status'] = $result->subscription->status;
        }
        else{
            $message = '';
            foreach (($result->errors->deepAll()) as $error) {
                $message .= "<p>". $error->message . "</p>";
            }
            $status = 'error';
            $data = $message;
        }
        return array('status' => $status , 'data' => $data);

    }

    public function handle_webhooks(){
        if(isset($_GET["bt_challenge"])) {
            echo(Braintree_WebhookNotification::verify($_GET["bt_challenge"]));
        }


    }

}

?>
