<?php
/*
Plugin Name: PayWithATweet
Plugin URI: http://kumicode.com/Pay With A tweet
Description: Checking if incoming request is from xxx, and create a user user for x min.
Version: 1.0
Author: Øyvind Dahl
Author URI: http://kumicode.no
License: GPL
*/


if(!class_exists("PayWithATweet")){
	
	class PayWithATweet{
		
		// options variables and names
		var $minutesOptionName = "pwat_option_minutes";
		var $legalUrlOptionName = "pwat_option_legal_url";
		var $versionOptionName = "pwat_version";
		
		var $pwat_version = "1";
		
		
		//	session varibales and names
		var $sessionsArrayName = 'pwat_sessions';
		var $sessionvalue = 'OK_COMPUTER';
		
		
		
		
		function PayWithATweet(){
			add_action('admin_menu', array($this ,'register_settings_page'));
			add_action('init', array($this, 'PWAT_session'), 1);
		
			
			add_option($this->versionOptionName, $this->pwat_version, "", "no");
		}
		

		
		
		
		/*
		*	
		*
		*/
		function endSessionFromPWAT() {
		    session_destroy ();
		}
				
		
		
		
		/*
		*
		*
		*
		*/
		function print_admin_page(){
			?>
			<div class="wrap">
					<div id="icon-options-general" class="icon32"></div>
					<h2>Pay With A Tweet settings</h2>
			<?php
			if(!empty($_POST['todo']) && $_POST['todo'] == "update_settings"){
				echo '<h2>input saved</h2>';
				
				if(!empty($_POST['pwat_ant_min'])) update_option($this->minutesOptionName, $_POST['pwat_ant_min'], "", "yes");	
				if(!empty($_POST['pwat_legal_url'])) update_option($this->legalUrlOptionName, $_POST['pwat_legal_url'], "", "yes");	
			}
			
			?>
				
					<div class="form-table">
						<form action="" method="post">
							<p>
								<label>Hvor mange minutter skal brukeren være logget inn i:</label>
								<input name="pwat_ant_min" class="regular-text" value="<?php echo get_option($this->minutesOptionName); ?>">
							</p>
							<p>
								<label>Hvilken url er tilat</label>
								<input name="pwat_legal_url" class="regular-text" value="<?php echo get_option($this->legalUrlOptionName); ?>">
							</p>
							<p>
								<input type="submit" value="Oppdater instillinger" class="button-primary">
							</p>
							<input type="hidden" name="todo" value="update_settings">
						</form>
					</div>
				</div>
			
			<?php
		}
		
		
		
		
		
		
		
		
		/*
		*	
		*
		*/
		function PWAT_button_small($atts){
			$content = '<iframe src="http://www.paywithatweet.com/dlbutton01.php?id=05a8e21ca986893cb96d6801b399fb61" name="paytweet_button" scrolling="auto" frameborder="no" ></iframe>';
			return $content;
		}
		
		
		
		
		/*
		*	
		*
		*/
		function PWAT_button_medium($atts){
			$content = '<iframe src="http://www.paywithatweet.com/dlbutton02.php?id=05a8e21ca986893cb96d6801b399fb61" name="paytweet_button2" width = "240px" height = "24px" scrolling="No" frameborder="no" id="paytweet_button2"></iframe>
                  ';
			return $content;
		}
		
		
		
		
		
		
		/*
		*	=PWAT_button_big =big
		*
		*/
		function PWAT_button_big($atts){
			$conten = '<iframe src="http://www.paywithatweet.com/dlbutton03.php?id=05a8e21ca986893cb96d6801b399fb61" name="paytweet_button3" width = "292px" height = "48px" scrolling="auto" frameborder="no" id="paytweet_button3"></iframe>';
			return $content;
		}
		
		
		
		
		
		
		/*
		*	=PWAT_landingpage =landing	
		*
		*/
		function PWAT_landingpage($atts){
			
			//if(($_SERVER['HTTP_REFERER'] == get_option($this->legalUrlOptionName))){
				$content = '<h2>Takk for at du betalte med en tweet! Du har nå 15 minutter til å hygge deg med norges mest geniale mattelærer. </h2>';
				$content .= '<p>Om du vil kan du lukke dette vinduet, og se videoen fra det vanlige nettleser vinduet ditt. Du har automatisk tilgang derifra også :)</p>';
				
				//	save user 
				$this->PWAT_session();
				$session_id = $this->PWAT_session_start();
	//			echo '<p>got: '.$session_id.' session: '.$_SESSION['pwat_session_id'].' and transient: '.get_transient($_SESSION['pwat_session_id']).' test: '.get_transient('test').'</p>';
			/*	
			}else if(get_transient($_SESSION['pwat_session_id'])){
				$content .= '<p>Du har fortsatt noen minutter igjen av din test periode</p>';
				
			}else{
				$content .= '<p>Om du vil betale med en tweet trykk her</p>';
				$content .= do_shortcode('<p>[PWAT_button_small]</p>');
			}
			*/
			return $content;
		}
	
	
		
		
		
		
		/*
		*	=register_settings_page =settings
		*
		*
		*/
		function register_settings_page(){
			add_submenu_page('options-general.php', 'Pay With A Tweet Settings', 'PWAT', 'add_users', 'pwat', array($this, 'print_admin_page'));
		}
		
		
		
		
		
		/*
		*
		*
		*
		*/
		function PWAT_session() {
		    if(!session_id()) {
		        session_start();    
		     }
		}
		
		
		
		
		function PWAT_session_start(){
			$session_id = "pwat_".wp_generate_password(10, false);
		    $_SESSION['pwat_session_id'] = $session_id;
		    $session_expires = (60 * get_option($this->minutesOptionName));
		    
		    set_transient($session_id, true, $session_expires);
		    
		    return $session_id;
		}
		
		
		
	
	}
	

}




/*	1.	*/
if(class_exists("PayWithATweet")){
	$payWithATweet = new PayWithATweet();
}


/*	2	*/
if($payWithATweet){

	
	/*
	*	Shortcode
	*/
	add_shortcode('PWAT_button_small', array(&$payWithATweet, 'PWAT_button_small'));
	add_shortcode('PWAT_button_medium', array(&$payWithATweet, 'PWAT_button_medium'));
	add_shortcode('PWAT_button_big', array(&$payWithATweet, 'PWAT_button_big'));
	add_shortcode('PWAT_landingpage', array(&$payWithATweet, 'PWAT_landingpage'));
	

			
	
			
	
}
?>