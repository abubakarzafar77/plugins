<?php 
function sre2t_get_categories_checkboxes( $taxonomy = 'category', $selected_cats = null ) {
	$args = array (
		'taxonomy' => $taxonomy
	);
	$all_categories = get_categories($args);
	
	$o = '<div class="checkbox_box checkbox_with_all"><ul><li class="e2t_all"><label><input class="e2t_all_input" type="checkbox" name="taxonomy['.$taxonomy.'][]" value="e2t_all" checked="yes" /> All</label></li>';
	foreach($all_categories as $key => $cat) {
		if($cat->parent == "0") $o .= sre2t_show_category($cat, $taxonomy, $selected_cats);
	}
	return $o . '</ul></div>';
}
function sre2t_show_category($cat_object, $taxonomy = 'category', $selected_cats = null) {
	$checked = "";
	if(!is_null($selected_cats) && is_array($selected_cats)) {
		$checked = (in_array($cat_object->cat_ID, $selected_cats)) ? 'checked="checked"' : "";
	}
	$ou = '<li><label><input class="e2t_input" ' . $checked .' type="checkbox" name="taxonomy['.$taxonomy.'][]" value="'. $cat_object->cat_ID .'" /> ' . $cat_object->cat_name . '</label>';
	$childs = get_categories('parent=' . $cat_object->cat_ID);
	foreach($childs as $key => $cat) {
		$ou .= '<ul class="children">' . sre2t_show_category($cat, $taxonomy, $selected_cats) . '</ul>';
	}
	$ou .= '</li>';
	return $ou;
}
// get taxonomies terms links
function sre2t_custom_taxonomies_terms_links() {
	global $post, $post_id;
	// get post by post id
	$post = &get_post($post->ID);
	// get post type by post
	$post_type = $post->post_type;
	// get post type taxonomies
	$taxonomies = get_object_taxonomies($post_type);
	$return = '';
	foreach ($taxonomies as $taxonomy) {
		if( $taxonomy !=  'category' && $taxonomy != 'post_tag' ) {
			// get the terms related to post
			$terms = get_the_terms( $post->ID, $taxonomy );
			if ( !empty( $terms ) ) {
				$return .= $taxonomy.' => ';
				$first = 1;
				foreach ( $terms as $term )
					if($first = 1) {
						$return .= $term->slug;
						$first = 0;
					}
					else {
						$return .= ','.$term->slug;
					}
				$return .= '. ';
			}
		}
	}
	return $return;
}

// Code used to get start and end dates with posts
function sre2t_the_post_dates() {
	global $wpdb, $wp_locale;
	
	$dateoptions = '';
	$types = "'" . implode("', '", get_post_types( array( 'public' => true, 'can_export' => true ), 'names' )) . "'";
	if ( function_exists( get_post_stati ) ) {
		$stati = "'" . implode("', '", get_post_stati( array( 'internal' => false ), 'names' )) . "'";
	}
	else {
		$stati = "'" . implode("', '", get_post_statuses( array( 'internal' => false ), 'names' )) . "'";
	}
	if ( $monthyears = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, YEAR(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `eyear`, MONTH(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `emonth` FROM $wpdb->posts WHERE post_type IN ($types) AND post_status IN ($stati) ORDER BY post_date ASC ") ) {
		foreach ( $monthyears as $k => $monthyear )
			$monthyears[$k]->lmonth = $wp_locale->get_month( $monthyear->month, 2 );
		for( $s = 0, $e = count( $monthyears ) - 1; $e >= 0; $s++, $e-- ) {
			//$dateoptions .= "\t<option value=\"" . $monthyears[$s]->year . '-' . zeroise( $monthyears[$s]->month, 2 ) . '">' . $monthyears[$s]->lmonth . ' ' . $monthyears[$s]->year . "</option>\n";
			$dateoptions .= "\t<option value=\"" . $monthyears[$e]->eyear . '-' . zeroise( $monthyears[$e]->emonth, 2 ) . '">' . $monthyears[$e]->lmonth . ' ' . $monthyears[$e]->year . "</option>\n";
		}
	}
	
	return $dateoptions;
}

function sre2t_implode_wrapped($before, $after, $array, $glue = '') {
    return $before . implode($after . $glue . $before, $array) . $after;
}