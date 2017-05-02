<?php
/**
 * The Template for displaying Teacher Search
 *
 * Override this template by copying it to yourtheme/webinar/search-teacher.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$search = ( get_query_var( 'as' ) ) ? get_query_var( 'as' )  : '';

?>

<div class="author-search">
	<h2><?php _e('Search teachers by name' ,'webinar');?></h2>
		<form method="get" id="webinar-searchform" action="<?php the_permalink() ?>">
			<label for="as" class="assistive-text"><?php _e('Search' ,'webinar');?></label>
			<input type="text" class="field" name="as" id="webinal-s" placeholder="<?php _e('Search Teachers' ,'webinar');?>" value="<?php echo $search; ?>"/>
			<input type="submit" class="button" id="webinar-searchsubmit" value="<?php _e('Search Teachers' ,'webinar');?>" />
		</form>
	<h1>Teachers <a href="user-new.php?role=teacher" class="button button-primary button-large">Add New</a></h1>
	<?php
	if( $search ){ ?>
		<h2 ><?php printf( __('Search Results for: %s' ,'webinar'), '<em>' . $search .'</em>' );?></h2>
		<a href="<?php the_permalink(); ?>"><?php _e('Back To Teacher Listing' ,'webinar');?></a>
	<?php } ?>
</div><!-- .author-search -->