<?php

class MattevideoPaymentController{

	private $pmt_id;
	private $pmt_status;
	private $pmt_netaxept_id;
	private $pmt_sbr_id;
	private $pmt_period_from;
	private $pmt_period_to;
	private $pmt_payment_id;
	private $pmt_amount;
	
	private $config;
	private $table_name;
	
	
	function MattevideoPaymentController( $sbr_id = null, $transactionId = null, $amount = null, $pan_hash = null, $order_number = null, $pmt_id = null){
		
		global $wpdb;
			
		//	set variables
		$this->config = json_decode( file_get_contents(plugin_dir_path( __FILE__ ).'../config.json' ) );
		$this->table_name = $wpdb->prefix.$this->config->plugin_parameters->tn_payment;
		
		
		//	createing a new payment with sbr id and transaction id
		if( !empty($sbr_id) && !empty($transactionId) ){
			
			$status = 'Initiated';
			$now = date('Y-m-d H:i:s');
			$period_to = date('Y-m-d H:m:s', strtotime('30 days'));
			$order_number = md5(uniqid('', true));
			
			$insert_result_id = $wpdb->insert( $this->table_name, 	
												array(	'pmt_status' => $status,
														'pmt_netaxept_id' => $transactionId,
														'pmt_sbr_id' => $sbr_id,
														'pmt_period_from' => $now,
														'pmt_period_to' => $period_to,
														'pmt_payment_id' => $order_number,
														'pmt_amount' => $amount));
			

			if( $wpdb->insert_id ){
				
				$this->pmt_id = $wpdb->insert_id;
				$this->pmt_status = $status;
				$this->pmt_netaxept_id = $transactionId;
				$this->pmt_period_from = $now;
				$this->pmt_period_to = $period_to;
				$this->pmt_payment_id = $order_number;
				$this->pmt_amount = $amount;
											
			}else{
				// todo: write error to log. creating subscripton failed
				_log( 'Error initiating class: ' . $insert_result_id );
			}
		
		
		//	getting payment based on transaction id	
		} 
        else if ( empty( $sbr_id ) && !empty( $transactionId) ){
			
			$select_query = "SELECT * FROM ".$this->table_name." WHERE pmt_netaxept_id = '".$transactionId."'";
			$select_result = $wpdb->get_results( $select_query );
			
			if($wpdb->num_rows == 1){
				$this->pmt_id = $select_result[0]->pmt_id;
				$this->pmt_sbr_id = $select_result[0]->pmt_sbr_id;
				$this->pmt_status = $select_result[0]->pmt_status;
				$this->pmt_netaxept_id = $select_result[0]->pmt_netaxept_id;
				$this->pmt_period_from = $select_result[0]->pmt_period_from;
				$this->pmt_period_to = $select_result[0]->pmt_period_to;
				$this->pmt_payment_id = $select_result[0]->pmt_payment_id;
				$this->pmt_amount = $select_result[0]->pmt_amount;
				
			}else{
				_log( 'ClassMattevideoPaymentController.php: multiple results for unique transactionId' );
			}
			
		} 
        else if ( !empty( $pmt_id ) ){
			
			$select_query = "SELECT * FROM ".$this->table_name." WHERE pmt_id = '" . $pmt_id . "'";
			$select_result = $wpdb->get_results( $select_query );
			
			if($wpdb->num_rows == 1){
				$this->pmt_id = $select_result[0]->pmt_id;
				$this->pmt_sbr_id = $select_result[0]->pmt_sbr_id;
				$this->pmt_status = $select_result[0]->pmt_status;
				$this->pmt_netaxept_id = $select_result[0]->pmt_netaxept_id;
				$this->pmt_period_from = $select_result[0]->pmt_period_from;
				$this->pmt_period_to = $select_result[0]->pmt_period_to;
				$this->pmt_payment_id = $select_result[0]->pmt_payment_id;
				$this->pmt_amount = $select_result[0]->pmt_amount;
			
			} else {
				_log ( 'ClassMattevideoPaymentController.php: numrows of query were not one' );
			}
		
		} 
        elseif( !empty( $sbr_id ) && empty( $transactionId ) ) {
			$select_query = "SELECT * FROM ".$this->table_name." WHERE pmt_sbr_id = '" . $sbr_id . "' AND pmt_status='Collected' ORDER BY pmt_id DESC LIMIT 1;";
			$select_result = $wpdb->get_results( $select_query );
			if($wpdb->num_rows == 1){
				$this->pmt_id = $select_result[0]->pmt_id;
				$this->pmt_sbr_id = $select_result[0]->pmt_sbr_id;
				$this->pmt_status = $select_result[0]->pmt_status;
				$this->pmt_netaxept_id = $select_result[0]->pmt_netaxept_id;
				$this->pmt_period_from = $select_result[0]->pmt_period_from;
				$this->pmt_period_to = $select_result[0]->pmt_period_to;
				$this->pmt_payment_id = $select_result[0]->pmt_payment_id;
				$this->pmt_amount = $select_result[0]->pmt_amount;
			
			} else {
				_log ( 'ClassMattevideoPaymentController.php: numrows of query were not one' );
			}
		} 
        else {
			return false;
		} 
	}
	
	
	
	public static function withId( $pmt_id ){
		$instance = new self();
		
	}
	
	// EDIT PL - 15-May-2014
	public function create_new_payment_on_sbr_with_from_date( $sbr_id, $period_from, $amount , $change_date = FALSE){
		
		global $wpdb; 
		
		$status = 'Initiated';
        // EDIT PL - 15-May-2014
        if($change_date == TRUE){
            $check_date = date( 'Y-m-d',time() );
            if($check_date < $period_from){
                $period_to = date('Y-m-d', strtotime('30 days', strtotime($period_from)));
            }
            else{
                $period_from = date( 'Y-m-d',time() );
                $period_to = date('Y-m-d', strtotime('30 days'));
            } 
        }
        else{
            $period_to = date('Y-m-d', strtotime('30 days', strtotime($period_from)));
        }
        // EDIT PL - 15-May-2014
		$order_number = md5(uniqid('', true));
		$transactionId = '';
		
		$insert_result_id = $wpdb->insert( $this->table_name, 	
											array(	'pmt_status' => $status,
													'pmt_netaxept_id' => $transactionId,
													'pmt_sbr_id' => $sbr_id,
													'pmt_period_from' => $period_from,
													'pmt_period_to' => $period_to,
													'pmt_payment_id' => $order_number,
													'pmt_amount' => $amount
													));
		

		if( $wpdb->insert_id ){
			
			$this->pmt_id = $wpdb->insert_id;
			$this->pmt_status = $status;
			$this->pmt_netaxept_id = $transactionId;
			$this->pmt_period_from = $period_from;
			$this->pmt_period_to = $period_to;
			$this->pmt_payment_id = $order_number;
			$this->pmt_amount = $amount;
			
			return $wpdb->insert_id;
									
		}else{
			// todo: write error to log. creating subscripton failed
			echo 'Error initiating class<br/>'.$insert_result_id;
			return false;
		}
		
	}
	
	
	
	public function get_current_payment_for_sbr( $sbr_id ){
		global $wpdb;

		$select_query = 'select * from wptest_mna_payment where pmt_sbr_id = ' . $sbr_id . ' and pmt_status LIKE "Collected" order by pmt_period_to desc limit 1';
		$select_result = $wpdb->get_results( $select_query );
		$return_payment = new self();
		$return_payment->setAllValues($select_result);
		return $return_payment;	
	}
	
	
	
	public function get_initiated_payments_for_sbr( $sbr_id){
		global $wpdb;
		
		$select_query = 'select * from wptest_mna_payment where pmt_sbr_id = ' . $sbr_id . ' and pmt_period_to > now() and pmt_status LIKE "Initiated"';
		$select_result = $wpdb->get_results( $select_query );
		
		if( $wpdb->num_rows > 0 ){
			return $select_result;
			
		} else {
			return false;
		}
		
	}
	
	
	public function getSbrId(){
		return $this->pmt_sbr_id;
	}
	
	
	public function getPeriod($array=false){
		if($array)
			return array('pmt_period_from'=>$this->pmt_period_from, 'pmt_period_to'=>$this->pmt_period_to);
		return $this->pmt_period_from . ' - ' . $this->pmt_period_to;
	}
		
	public function get_to_date(){
		return $this->pmt_period_to;
	}
	
	public function get_pmt_payment_id(){
		return $this->pmt_payment_id;
	}
	
	
	//	get 
	public static function withTransactionId($transactionId){
		$instance = new self(0, $transactionId);
		return $instance;
	}
	
	
	public static function getNumberOfPaymentsWithSbrId ( $sbr_id ) {
		
		$payments_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE pmt_sbr_id = '$sbr_id'" );
		return $payments_count;
	}
	
	
	private function setAllValues($values){
	
		$this->pmt_id = $values[0]->pmt_id;
		$this->pmt_sbr_id = $values[0]->pmt_sbr_id;
		$this->pmt_status = $values[0]->pmt_status;
		$this->pmt_netaxept_id = $values[0]->pmt_netaxept_id;
		$this->pmt_period_from = $values[0]->pmt_period_from;
		$this->pmt_period_to = $values[0]->pmt_period_to;
		$this->pmt_payment_id = $values[0]->pmt_payment_id;
		$this->pmt_amount = $values[0]->pmt_amount;
	
	}
	
	
	
	public function setStatus($status){
		if($this->updatePaymentField('pmt_status', $status)){
			$this->pmt_status = $status;
		}else{
			return false;
		}
		
	}
	
	
	public function setTransactionId( $transactionId ){
		
		if($this->updatePaymentField( 'pmt_netaxept_id', $transactionId)){
			$this->pmt_netaxept_id = $transactionId;
		} else {
			return false;
		}
	}
	
	private function updatePaymentField($key, $value){
		
		global $wpdb;
		
		$result = $wpdb->update( $this->table_name, array($key => $value), array('pmt_id' => $this->pmt_id));
		return $result;
	}
	
	
}

?>