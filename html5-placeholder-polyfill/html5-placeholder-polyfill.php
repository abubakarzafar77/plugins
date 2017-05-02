<?php
/**
 * Plugin Name: HTML5 Placeholder Polyfill
 * Plugin URI:  https://wordpress.org/plugins/html5-placeholder-polyfill/
 * Description: Mimics HTML5 placeholder behavior in browsers that don't support it natively.
 * Version:     1.2.0
 * Author:      MIGHTYminnow & Mickey Kay
 * Author URI:  http://mightyminnow.com
 * License:     GPLv2+
 */

// Register the jQuery file - that's it!
function hpp_enqueue_jquery() {

	// Make sure jQuery is included
	wp_enqueue_script( 'jquery' );

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    
	// Include the polyfill jQuery in the footer
    wp_enqueue_script( 'html5-placeholder-polyfill', plugins_url( '/html5-placeholder-polyfill' . $suffix . '.js', __FILE__ ), 'jquery','', true );
}

// Enqueue on frontend
add_action( 'wp_enqueue_scripts', 'hpp_enqueue_jquery' );

// Enqueue on admin
add_action( 'admin_enqueue_scripts', 'hpp_enqueue_jquery' );

// Enqueue on login screen
add_action( 'login_enqueue_scripts', 'hpp_enqueue_jquery' );

?>