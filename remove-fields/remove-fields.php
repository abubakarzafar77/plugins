<?php
/*
Plugin Name: Remove Fields
Plugin URI: http://www.kadimi.com/
Description: Remove Fields.
Version: 0.2
Author: Nabil Kadimi
Author URI: http://www.kadimi.com/en/
Author Email: nabil@kadimi.com
*/

/*
 * Remove Website Field from the comment form
 */
add_action('wp_head','remove_website');
function remove_website() {
	?><script>
		jQuery(document).ready(function($){
			$p = $('#url').parent();
			$('#url, label[for=url]').remove();
			$p.filter(function(){return $(this).text().trim().length;}).remove();
		});
	</script><?php
}

/**
 * Hide Personal Options section:
 * - Visual Editor
 * - Admin Color Scheme
 * - Keyboard Shortcuts
 * - Toolbar 
 * - Admin Language
 */
add_action('admin_head','hide_personal_options');
function hide_personal_options(){
	?><script>
		jQuery(document).ready(function($) {
			$('form#your-profile > h3:first').remove(); 
			$('form#your-profile > table:first').remove(); 
			$('form#your-profile').show(); 
		});
	</script><?php
}

/*
 * Remove AIM, Yahoo IM and Jabber / Google Talk
 */
add_filter('user_contactmethods', 'remove_contactmethods');
function remove_contactmethods($contactmethods) {
	unset($contactmethods['aim']);
	unset($contactmethods['yim']);
	unset($contactmethods['jabber']);
	return $contactmethods;
}

/**
 * Remove About Yourself section (method 1)
 * - Biographical Info 
 */
add_action('admin_head', 'profile_admin_buffer_start');
add_action('admin_footer', 'profile_admin_buffer_end');
function remove_plain_bio($buffer){
	$titles = array('#<h3>'.__('About Yourself').'</h3>#','#<h3>'.__('About the user').'</h3>#');
	$buffer=preg_replace($titles,'<h3>'.__('Password').'</h3>',$buffer,1);
	$biotable='#<h3>'.__('Password').'</h3>.+?<table.+?/tr>#s';
	$buffer=preg_replace($biotable,'<h3>'.__('Password').'</h3> <table class="form-table">',$buffer,1);
	return $buffer;
}
function profile_admin_buffer_start() {ob_start('remove_plain_bio');}
function profile_admin_buffer_end() {ob_end_flush();}

/**
 * Remove About Yourself section (method 2)
 * - Biographical Info 
 */
add_action('admin_head','hide_bio');
function hide_bio(){
	?><script>
		jQuery(document).ready(function($) {
			$('h3').filter(function(){return $(this).text()==='<?php _e('About Yourself')?>' || $(this).text()==='<?php _e('About the user')?>'}).text('<?php _e('Password')?>');
			jQuery('#password').parent().find('tr:first:not(#password)').remove();
		});
	</script><?php
}
