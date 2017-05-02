<?php

Class MattevideoSubscriptionController{

	private $sbr_id;
	private $sbr_wp_user_id;
	private $sbr_status;
	private $sbr_created;
	private $sbr_modified;
	private $sbr_pan_hash;
	
	private $config;
	private $table_name;
	
	private $subscription_status;
		
	
	function MattevideoSubscriptionController( $user_id = 0, $sbr_id = 0 ){
		
		global $wpdb;
		
		//	set variables
		$this->config = json_decode( file_get_contents(plugin_dir_path( __FILE__ ).'../config.json' ) );
		$this->table_name = $wpdb->prefix.$this->config->plugin_parameters->tn_subscription;
		
		$this->subscription_status = array('Active' => 'Active', 'Expired' => 'Expired', 'Deactivated' => 'Deactivated', 'Cancelled' => 'Cancelled', 'Started'=>'Started');
		
		//	create a new subscription for user
		if( $user_id > 0 ){
		
			// check if user has subscription
			$select_query = "SELECT sbr_id, sbr_wp_user_id, sbr_status, sbr_created, sbr_modified FROM ".$this->table_name." WHERE sbr_wp_user_id = ".$user_id;
			$select_result = $wpdb->get_results( $select_query );
			
			if( $wpdb->num_rows == 1 ){
				
				$this->sbr_id = $select_result[0]->sbr_id;
				$this->sbr_wp_user_id = $select_result[0]->sbr_wp_user_id;
				$this->sbr_status = $select_result[0]->sbr_status;
				$this->sbr_created = $select_result[0]->sbr_created;
				$this->sbr_modified = $select_result[0]->sbr_modified;
				$this->sbr_pan_hash = $select_result[0]->sbr_pan_hash;
			
			} else if( $wpdb->num_rows == 0 ){
				$status = 'Initiated';
				$now = date('Y-m-d H:i:s');
				$insert_result_id = $wpdb->insert( $this->table_name, 	
													array('sbr_status' => $status,
														'sbr_wp_user_id' => $user_id,
														'sbr_created' => $now,
														'sbr_modified' => $now));
				if($wpdb->insert_id){
					$this->sbr_id = $wpdb->insert_id;
					$this->sbr_status = $status;
					$this->sbr_wp_user_id = $user_id;
					$this->sbr_created = $now;
					$this->sbr_modified = $now;
					
				}else{
					// todo: write error to log. creating subscripton failed
	
				}
				
			} else if ( $sbr_id > 0 ) {
				//	todo write error to log, customer has meny subscriptions. thats not legal.
			}
		
		//	getsubscription for sbr id	
		} else if ( $sbr_id > 0 ){
			
			$select_query = "SELECT sbr_id, sbr_wp_user_id, sbr_status, sbr_created, sbr_modified, sbr_pan_hash FROM " . $this->table_name." WHERE sbr_id = " . $sbr_id;
			$select_result = $wpdb->get_results( $select_query );
			
			if ( $wpdb->num_rows == 1 ) {
			
				$this->sbr_id = $select_result[0]->sbr_id;
				$this->sbr_wp_user_id = $select_result[0]->sbr_wp_user_id;
				$this->sbr_status = $select_result[0]->sbr_status;
				$this->sbr_created = $select_result[0]->sbr_created;
				$this->sbr_modified = $select_result[0]->sbr_modified;
				$this->sbr_pan_hash = $select_result[0]->sbr_pan_hash;
			
			} else {
				echo 'Error on instance of type subscription<br/>';
			}
			
		} else {
			_log( 'ClassMattevideoSubscription.php: _constructor: could instantiace sbr isntance bacause of lack of ids' );
		}
	}
	
	
	public static function withId($sbr_id){
		$instance = new self(0, $sbr_id );
		return $instance;
	}
	
	
	
	public function get_pan_hash(){
		return ($this->sbr_pan_hash != '' ? $this->sbr_pan_hash : false);
	}
	
	public function getStatus(){
		return ($this->sbr_status != '' ? $this->sbr_status : false);
	}
	
	public function getSbrId(){
		return ($this->sbr_id > 0 ? $this->sbr_id : false);
	}
	
	public function getSubscriptionId(){
		return ($this->sbr_id > 0 ? $this->sbr_id : false);
	}
	
	
	
	public function getWpUserId () {
		return ($this->sbr_wp_user_id > 0 ? $this->sbr_wp_user_id : false);
	}
	
	
	
	
	
	
	public function is_active(){
		if($this->sbr_status == 'Active'){
			return true;
		}else{
			return false;
		}
	}
    // EDIT PL - 15-May-2014
	public function is_Cancel(){
		if($this->sbr_status == 'Cancelled'){
			return true;
		}else{
			return false;
		}
	}
	// EDIT PL - 15-May-2014
	
	public function setPanHash($panHash){
		
		if ( $this->updateSubscriptionField('sbr_pan_hash', $panHash )) {
			$this->sbr_pan_hash = $panHash;
			
		} else {
			
			return false;
			
		}
	}
	
	
	public function setStatus($status){
		
		if ( $this->updateSubscriptionField('sbr_status', $status )) {
			$this->sbr_status = $status;
			$result = update_user_meta( $this->sbr_wp_user_id, 'sbr_status', $status );
			
		} else {
			return false;
		}
	}
	
	
	
	private function updateSubscriptionField ( $key, $value ){
		
		global $wpdb;
		
		if($this->sbr_id != ''){
			$result = $wpdb->update( $this->table_name, array($key => $value), array('sbr_id' => $this->sbr_id));
		}else {
			$result = 'No sbr_id';
		}
		
		return $result;
	}
	
	
	
	/*
		When user is logging in, update subscription status by 
		checking if user has any valid payments.
	*/
	public function update_user_sbr_status ( ){ 
		
		global $wpdb;
		
		if( $this->sbr_wp_user_id > 0 ){
		
			$select_query = 'select * from wptest_mna_payment 
							where pmt_period_from < now() 
							and pmt_period_to > now() 
							and pmt_sbr_id = ' . $this->sbr_id . ' 
							and pmt_status LIKE "Collected"';
							
			$select_result = $wpdb->get_results( $select_query );
		
			$status = '';		
			
			if ( $wpdb->num_rows >= 1 ){
				$status = $this->subscription_status['Active'];
		
			} else {
				$status = $this->subscription_status['Expired'];
			}
		
			$result = $this->updateSubscriptionField ( 'sbr_status', $status );
			$result = update_user_meta( $this->sbr_wp_user_id, 'sbr_status', $status );		
		
		} else {
			_log( 'Could not update because user id is null' );
			
		}

	}
	
	


}



?>