<?php
/**
 * The Template for displaying Teacher page navigation
 *
 * Override this template by copying it to yourtheme/webinar/navigation-teacher.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $simple_user_listing, $sul_users;

if( function_exists( 'wp_pagenavi' ) ){

		wp_pagenavi(array('query' => $sul_users, 'type' => 'users' ) );
} else {

	// Only show the navigation if needed
	if ( $simple_user_listing->get_total_user_pages() > 1 ) { ?>

	<nav id="nav-single">
		<h3 class="assistive-text"><?php _e('Teacher navigation', 'webinar');?></h3>

	<?php		if ( $previous_url = $simple_user_listing->get_previous_users_url() ) { ?>
				<span class="nav-previous"><a rel="prev" href="<?php esc_attr_e( $previous_url ); ?>"><span class="meta-nav">&larr;</span> <?php _e('Previous', 'webinar');?></a></span>
			<?php } ?>

			<?php if ( $next_url = $simple_user_listing->get_next_users_url() ) { ?>
				<span class="nav-next"><a rel="next" href="<?php esc_attr_e( $next_url ); ?>"><?php _e('Next', 'webinar');?> <span class="meta-nav">&rarr;</span></a></span>
			<?php } ?>

	</nav>

	<?php } ?>

<?php } ?>