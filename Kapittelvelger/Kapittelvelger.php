<?php
/*
Plugin Name: Kapittelvelger
Plugin URI: http://kumicode.com/kapittelvelger
Description: kapittelvelger plug-in. Write "[Kapittelvelger]" into any post to show the chapterchooser  
Version: 1.0
Author: Øyvind Dahl
Author URI: http://kumicode.com
License: GPL
*/



if(!class_exists("Kapittelvelger")){
	
	class Kapittelvelger{
	
		
		
		
		

	
	}//	end of kapittelvelger
}



/**
 * Mattevideo_chapter_chooser Class
 */
class Mattevideo_chapter_chooser_widget extends WP_Widget {

    /** constructor */
    function Mattevideo_chapter_chooser_widget() {
        parent::WP_Widget(false, $name = 'Mattevideo Veiviser');
    	
    }
	

    /*
    *	Listing chapters and subchapters (categories and subcategories)
    *
    */
    function widget($args, $instance) {
    	
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        
        $this->includeKapittelVelgerStyle();
        
        
        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;
        
        
        echo '
        		<div class="veiviser">
       				<h1 id="veiviser_header">Videoveiviser</h1>
       				<select class="veiviser" name="pensum" id="pensum">
       					<option value="">Velg mattebok:</option>
       					<option value="matematikk1t">Matematikk 1T</option>
       					<option value="sinus1t">Sinus 1T</option>
       					<option value="matematikk1p">Matematikk 1P</option>
       					<option value="sinus1p">Sinus 1P</option>
                        <option value="sinusS1">Sinus S1</option>
                        <option value="sinusR1">Sinus R1</option>
                        <option value="matematikkR1">Matematikk R1</option>
       					<option value="sinusR2">Sinus R2</option>
                        <option value="matematikkR2">Matematikk R2</option>
						
						
                        
       				</select>
       				<select class="veiviser" id="chapter"><option></option></select>
       				<select class="veiviser" id="subchapter"><option></option></select>
        				
       				<div id="chapterlinks"></div>
        				
   				</div>';
        
        echo $after_widget; 
        
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }


    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
    }
	
	
	
	/*
		*
		*	=includeKapittelVelgerStyle
		*
		*/
		function includeKapittelVelgerStyle(){
			$src = WP_PLUGIN_URL."/Kapittelvelger/kapittelvelgerstyle.css";
			wp_register_style( "KapittelvelgerStyle2000", $src);
			wp_enqueue_style( "KapittelvelgerStyle2000" ); 
		}
	
	
	
	function print_kapittelvelger(){
			
			
			$this->includeKapittelVelgerStyle();
        
        
       		
        	return '
        			<div>
       				<h3>Videoveiviser</h3>
       				<select class="veiviser" name="pensum" id="pensum">
       					<option value="">Velg mattebok:</option>
       					<option value="matematikk1t">Matematikk 1T</option>
       					<option value="sinus1t">Sinus 1T</option>
       					<option value="matematikk1p">Matematikk 1P</option>
       					<option value="sinus1p">Sinus 1P</option>
                        <option value="sinusS1">Sinus S1</option>
                        <option value="sinusR1">Sinus R1</option>
                        <option value="matematikkR1">Matematikk R1</option>
       					<option value="sinusR2">Sinus R2</option>
                        <option value="matematikkR2">Matematikk R2</option>
						
                        
       				</select>
       				<select class="veiviser" id="chapter"><option></option></select>
       				<select class="veiviser" id="subchapter"><option></option></select>
        				
       				<div id="chapterlinks"></div>
        				
   				</div>';
        
        	
        }
	
} // class chapterchooser 





/*	1.	*/
if(class_exists("Mattevideo_chapter_chooser_widget")){
	$kapittelvelger = new Mattevideo_chapter_chooser_widget();
}


/*	3	*/
if($kapittelvelger){

	add_action('widgets_init', create_function('', 'return register_widget("Mattevideo_chapter_chooser_widget");'));
	
	/*
	*	Shortcode
	*/
	add_shortcode('Kapittelvelger', array(&$kapittelvelger, 'print_kapittelvelger'));
	
	
	/*
	*	Ajax
	*/
	// this hook is fired if the current viewer is not logged in
	if(!empty($_REQUEST['action'])){
		do_action( 'wp_ajax_nopriv_' . $_REQUEST['action'] );
 	}
	// if logged in:
	if(!empty($_POST['action'])){
		do_action( 'wp_ajax_' . $_POST['action'] );
	}
	
	add_action( 'wp_ajax_nopriv_myajax-submit', 'getChaptersForPensum' );
	add_action( 'wp_ajax_myajax-submit', 'getChaptersForPensum' );
	
						
    function kapittelVelgerScripts(){
	    wp_enqueue_script( 'json-form' );
		// embed the javascript file that makes the AJAX request
		wp_enqueue_script( 'Mattevideo-veiviser-request', plugin_dir_url( __FILE__ ) . 'ajax.js', array( 'jquery' ) );
	 
		// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
		wp_localize_script( 'Mattevideo-veiviser-request', 'VeiviserAjax', array( 
								'ajaxurl' => admin_url( 'admin-ajax.php' ),
								'redirecturl' => home_url('/logg-inn'),
								'homeurl' => home_url('/'),
								'loadingmessage' => __('Sending user info, please wait...')
	   							//'postCommentNonce' =>  wp_create_nonce('Mattevideo_veiviser'),
	    					 	) 
	    					);
	    
    }
	add_action('wp_enqueue_scripts', 'kapittelVelgerScripts');    
    	
    					
	function getChaptersForPensum() {
	    // get the submitted parameters
	    
	    $nonce = $_POST['postCommentNonce'];
 
    	// check to see if the submitted nonce matches with the
    	// generated nonce we created earlier
   		/*if ( ! wp_verify_nonce( $nonce, 'myajax-post-comment-nonce' ) )
        	die ( 'Busted!');
	    */
	    if ( current_user_can( 'edit_posts' ) ) {
		    $postID = $_POST['postID'];
		 	$test = $_POST['test'];
		 	
		 	echo $test." ".$postID;
		 	
		    // generate the response
		    $response = json_encode( array( 'success' => true ) );
		 
		    // response output
		    header( "Content-Type: application/json" );
		    echo $response;
		}	 
	    
	    exit;
	}

}
?>