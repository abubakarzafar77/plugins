<?php
/*
Plugin Name: Export to Text
Plugin URI: http://www.hypedtext.com
Description: A simple plugin to export WordPress post data into a tab-separated text file format (TSV)
Version: 2.4
Author: Hyped Text
Author URI: http://www.hypedtext.com
*/
?>
<?php
register_activation_hook( __FILE__, 'sre2t_install' ); // executes funcition upon plugin activation

function sre2t_install() { // function executed at plugin activation - checks if WP version is compatible with the plugin - NOT WORKING
	if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
	}
}

add_action( 'admin_menu', 'sre2t_admin_page' );

function sre2t_admin_page() { // adds menu to "Tools" section linked to sre2t_manage function	
	$plugin_page = add_management_page('Export to Text', 'Export to Text', 'export', basename(__FILE__), 'sre2t_manage'); //Sets and saves plugin page in WP admin
	add_action( 'load-'.$plugin_page, 'sre2t_add_js' ); //triggers sre2t_add_js function just on plugin page
}

function sre2t_add_js() { //properly adds JS file to page
	wp_enqueue_style( 'export_to_text_css', plugins_url( 'export-to-text.css' , __FILE__ ) ); 
	
	wp_enqueue_script( 'export_to_text_js', plugins_url( 'export-to-text.js' , __FILE__ ) );
	
	$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://'; //This is used to set correct adress if secure protocol is used so ajax calls are working
	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol )
	);
	wp_localize_script( 'export_to_text_js', 'export_to_text_js', $params );
	
	wp_enqueue_script( 'jquery-ui-sortable' );
}

require_once( 'export-to-text_helpers.php' ); //loads file with fuctions for help

if(isset($_POST['download'])) {
	
	add_action('wp_loaded', 'export_to_text_download', 1);
	
	function export_to_text_download() {
		//$core = $_POST['download'].'wp-load.php';
		//include( $core );
		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty($sitename) ) $sitename .= '.';
		$filename = $sitename . 'wordpress.' . date( 'Y-m-d' ) . '.txt';
	
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );
		
		sre2t_ajax();
		die();
	}
	
	//export_to_text_download();
}

function sre2t_manage() { // Sre2t_manage function used to display Export To Text page
		if ( !current_user_can('export') )
			wp_die(__('You do not have sufficient permissions to export the content of this site.'));
	
		global $wpdb;
		
		// Displays Export To Text Menu
		?>
		<div class="export-to-text">
        <div class="wrap">
        
            <?php screen_icon(); //function used to get correct icon ?>
            <h2>Export to Text</h2>
            
            <div id="main">
                
	            <p>A simple plugin to export WordPress post data into a tab-separated text file format (TSV). When you click the button below Export to Text will render a text box from which you can copy and paste your data into a text editor or Excel. If you need to re-import posts from a text file, please consider using CSV Importer plugin.</p>
	            
	            <form id="export-to-text-form" action="" method="post"><!--Form posts to "export-to-text_dl_txt.php" responsible for file download-->
	                <h3>Filters</h3>
	                <div id="options_holder">
	                	<div class="option_box option_box_short">
	                		<label id="sdate" class="short_label" for="sdate">Start Date</label>
	                        <select name="sdate" id="sdate">
	                        	<option value="all">All Dates</option>
	                        	<?php
								$dateoptions = sre2t_the_post_dates();
								
								echo $dateoptions;
	                        	?>
	                        </select>                 		
	                	</div>
	                	<div class="option_box option_box_short">
	                		<label for="edate" class="short_label">End Date</label>
	                        <select name="edate" id="edate">
	                             <option value="all">All Dates</option>
								 <?php
	                             echo $dateoptions;
	                             ?>
	                        </select>
	                	</div>
	                	<div class="option_box option_box_submit submit">
			                <input type="hidden" name="download" value="<?php echo get_home_path(); ?>" />
			                   
							<a href="#" class="button-secondary">Generete preview (max 10)</a> <!--link connected to js responsible for AJAX call-->
							<input class="button button-primary" type="submit" value="Download as TXT file" name="submit"> <!--Posts data to "export-to-text_dl_txt.php" file-->
	                	</div>               	
	                	<div class="clearboth"></div>
	                	<div class="option_box">
	                		<label for="author" class="full_label">Authors:</label>
							
							<label title="Include"><input type="radio" name="author_inex" value="" checked="checked"> <span>Include</span></label>
							<label title="Exclude"><input type="radio" name="author_inex" value="-"> <span>Exclude</span></label>
							
	                    	<div class="checkbox_box">
	                            <ul>
	                                <li class="e2t_all"><label><input type="checkbox" name="author[]" value="e2t_all" checked="yes" /> All</label></li>
		                            <?php
		                            $authors = $wpdb->get_results( "SELECT DISTINCT u.id, u.display_name FROM $wpdb->users u INNER JOIN $wpdb->posts p WHERE u.id = p.post_author ORDER BY u.display_name" );
		                            foreach ( (array) $authors as $author ) {
		                            ?>
		                                <li><label><input type="checkbox" name="author[]" value="<?php echo $author->id; ?>" /> <?php echo $author->display_name; ?></label></li>
		                            <?php
		                            }
		                            ?>
	                            </ul>
	                    	</div>                                  		
	                	</div>
	                	<div class="option_box">
	                		<label for="ptype" class="full_label">Post Types:</label>
	                    	<div class="checkbox_box">
	                            <ul>
	                                <li><label><input type="checkbox" name="ptype[]" value="post" checked="yes" /> Posts</label></li>
	                                <li><label><input type="checkbox" name="ptype[]" value="page" /> Pages</label></li>
	                                <?php 
	                                foreach ( get_post_types( array( 'public' => true,'_builtin' => false ), 'objects' ) as $post_type_obj ) { ?>
	                                    <li><label><input type="checkbox" name="ptype[]" value="<?php echo $post_type_obj->name; ?>" /> <?php echo $post_type_obj->labels->name; ?></label></li>
	                                <?php 
	                                } 
	                                ?>
	                        	</ul>
	                        </div>
	                	</div>                	
	                	<div class="option_box">
	                		<label for="ptype" class="full_label">Statuses:</label>
	                    	<div class="checkbox_box">
	                            <ul>
	                            	<li><label><input type="checkbox" name="post_status[]" value="publish" checked="yes"/> Publish</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="pending" /> Pending</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="draft" /> Draft</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="future" /> Future</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="private" /> Private</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="trash" /> Trash</label></li>        
								</ul>
							</div>
	                	</div>
	                	
						<?php
						$taxonomies = array_merge(array('category', 'post_tag'), get_taxonomies(array('_builtin' => false),'names'));
						foreach ($taxonomies as $taxonomy ) { ?>
							<div class="option_box">
								<label for="ptype" class="full_label"><?php echo str_replace('_', ' ', $taxonomy); ?>: </label>
								
								<label title="Include"><input type="radio" name="taxonomy[<?php echo $taxonomy; ?>][inex]" value="IN" checked="checked"> <span>Include</span></label>
								<label title="Exclude"><input type="radio" name="taxonomy[<?php echo $taxonomy; ?>][inex]" value="NOT IN"> <span>Exclude</span></label>
								
								<?php echo sre2t_get_categories_checkboxes($taxonomy);?>
							</div>
						<?php
						}
						?>
						
						<div class="option_box">
	                        <label for="cf" class="full_label">Custom field:</label>
								<label title="Equal"><input type="radio" name="cfcompare" value="=" checked="checked"> <span>Equal</span></label>
								<label title="Not Equal"><input type="radio" name="cfcompare" value="!="> <span>Not Equal</span></label>
								</br>
	                    	<label class="short_label">Name: </label><input type="text" name="cfname"></br>
	                   		<label class="short_label">Value: </label><input type="text" name="cfvalue" >
						</div>
						
	                	<div class="option_box">
	                		<label for="ptype" class="full_label">Select and reorder data to generate:</label>
	                    	<div class="checkbox_box">
	                            <ul class="sortable">
	                            	<li><label><input type="checkbox" name="data_filter[]" value="ID" checked="yes"/> ID</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Title" checked="yes"/> Title</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Date" checked="yes"/> Date</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Post Type" checked="yes"/> Post Type</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Categories" checked="yes"/> Categories</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Tags" checked="yes"/> Tags</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Custom Taxonomies" checked="yes"/> Custom Taxonomies</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Permlink" checked="yes"/> Permlink</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Content" checked="yes"/> Content</label></li>
									<li><label><input type="checkbox" name="data_filter[]" value="Excerpt" checked="yes"/> Excerpt</label></li>          
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Author" checked="yes"/> Author</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Author Email" checked="yes"/> Author Email</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Custom Fields" checked="yes"/> Custom Fields</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Comments" checked="yes"/> Comments</label></li>
								</ul>
							</div>
	                	</div>					                	
	                	
	                </div>
	                	
	            </form>
	            <div class="clearboth"></div>
	            
	            <div id="export-to-text-results-holder">
					<div id="export-to-text-results-close-holder"><a href="#" id="export-to-text-results-close">Close</a></div>
	            	<div id="export-to-text-results" ><strong>Just click on "Generete for quick copying" and then click on this box to select and copy the text.<br/>Then paste it (Paste Special works best) into a new Excel document.</strong></div>
	            </div>
	                
				<div class="info-box" id="donate_box">
					<div class="sidebar-name">
						<h3>Donate!</h3>
					</div>
					<div class="sidebar-description">
	                    <p>Please make a donation to aid the development of plugins and support open source software.</p>
	                    <form id="donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
	                        <input type="hidden" name="cmd" value="_s-xclick">
	                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	                        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	                        <img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
	                    </form>					
                    </div>
				</div>

				<div class="info-box">
					<div class="sidebar-name">
						<h3>Need support?</h3>
					</div>
					<div class="sidebar-description">
	                    <p>Please visit the <a href="http://wordpress.org/tags/export-to-text">WordPress.org forums</a> if you are having any issues with this plugin.</p>				
                    </div>
				</div>

				<div class="info-box">
					<div class="sidebar-name">
						<h3>Rate me!</h3>
					</div>
					<div class="sidebar-description">
	                    <p><a href="http://wordpress.org/extend/plugins/export-to-text/">Click here</a> and rate this plugin on WordPress.org</p>					
                    </div>
				</div>
                
                <div class="clearboth"></div>
                            
            </div>
        </div>
        </div>
<?php	
}

add_action( 'wp_ajax_sre2t_ajax', 'sre2t_ajax' ); //adds function to WP ajax
function sre2t_ajax() { //Function used for generating results for display in PRE tag and saving as TXT
	
	//prepere query and post data
	
	// sets correct values for start and end date + adds WP "post_where" filter
	if ( ($_POST['sdate'] != 'all' || $_POST['edate'] != 'all') && !empty($_POST['sdate']) ) {
		add_filter('posts_where', 'filter_where');
		function filter_where($where = '') {
			if($_POST['sdate'] == $_POST['edate'] ) {
				$edate_explode = explode('-', $_POST['edate']);
				if($edate_explode[1] == 12) {
					$edate_explode[1] = 01;
					$edate_explode[0] ++;
				}
				else
					$edate_explode[1]  ++;
					
				$edate_explode[1] = sprintf("%02s", $edate_explode[1]);
					
				$_POST['edate'] = implode('-', $edate_explode);
			}
			if( $_POST['sdate'] != 'all' && $_POST['edate'] == 'all') {
				$sdate =  "AND post_date >= '".$_POST['sdate']."'";
			}
			elseif( $_POST['edate'] != 'all' && $_POST['sdate'] == 'all') {
				$edate = "AND post_date < '".$_POST['edate']."'";
			}
			else{
				$sdate =  " AND post_date >= '".$_POST['sdate']."'";
				$edate = " AND post_date < '".$_POST['edate']."'";
			}
			$where .= $sdate;
			$where .= $edate;
			
			return $where;
		}
	}
	
	if( $_POST['download'] == '0' )
		$posts_per_page = 10;
	else
		$posts_per_page = -1;
	
	$args = array( // arguments used for WP_Query
		'posts_per_page' => $posts_per_page,
		'post_type' => $_POST['ptype'],
		'post_status' => $_POST['post_status'],
		'order' => ASC,
		'meta_key' => $_POST['cfname'],
		'meta_value' => $_POST['cfvalue'],
		'meta_compare' => $_POST['cfcompare']
	);
	//creates arrays for taxonomies
	$is_tax_query = 0;
	foreach ($_POST['taxonomy'] as $key => $value) {
		if( !in_array('e2t_all', $value) ) {
			if($is_tax_query == 0) {
				$args['tax_query'] = array('relation' => 'OR');
				$is_tax_query = 1;
			}
			$operator = $value['inex'];
			unset($value['inex']);
			$temp = array( 'taxonomy' => $key, 'field' => 'id', 'terms' => $value, 'operator' => $operator );
			array_push($args['tax_query'], $temp);
		}
	}
	//adds argument for authors
	if( !in_array('e2t_all', $_POST['author']) ) {
		$args['author'] = $_POST['author_inex'].implode(','.$_POST['author_inex'], $_POST['author']);
	}
	$export_to_text = new WP_Query( $args ); // new custom loop to get desired results
	
	//prepere all the post data...if there are any posts!	
	if ( $export_to_text->have_posts() ) :
	
		$ett_posts = array();
		$count = 0;
		 
		if( !is_array($_POST['data_filter']) ) {
			$_POST['data_filter'] = array();
		}
		
		//echo ( $_POST['download'] == '0' ) ? '<strong>'.$labels.'</strong><br />' : $labels."\r\n";// echoes labels differently for Pre and TXT file
	
	while ( $export_to_text->have_posts() ) : $export_to_text->the_post();
	
		if(in_array('ID', $_POST['data_filter'])) $ett_posts[$count]['ID'] = get_the_ID();
		if(in_array('Title', $_POST['data_filter'])) $ett_posts[$count]['Title'] = get_the_title();
		if(in_array('Date', $_POST['data_filter'])) $ett_posts[$count]['Date'] = get_the_date();
		if(in_array('Post Type', $_POST['data_filter'])) $ett_posts[$count]['Post Type'] = get_post_type();
		
		if(in_array('Categories', $_POST['data_filter'])) {
			$categories_names = array();
			foreach ( get_the_category () as $category) {
				$categories_names[] = $category -> cat_name;
			}
			$ett_posts[$count]['Categories'] = implode(', ', $categories_names);
		}

		if(in_array('Tags', $_POST['data_filter'])) {		
			if (has_tag()){
				$tags_names = array();
				foreach ( get_the_tags () as $tag) {
					$tags_names[] = $tag -> name; 
				}
				$ett_posts[$count]['Tags'] = implode(', ', $tags_names);
			}
			else {
				$ett_posts[$count]['Tags'] = "";
			}
		}
		
		if(in_array('Custom Taxonomies', $_POST['data_filter'])) $ett_posts[$count]['Custom Taxonomies'] = sre2t_custom_taxonomies_terms_links();
		
		if(in_array('Permlink', $_POST['data_filter'])) $ett_posts[$count]['Permlink'] = get_post_permalink(get_the_ID());
		
		if(in_array('Content', $_POST['data_filter'])) {
			global $more;
			$more = 1;
			$thepostcontent = apply_filters('the_content', get_the_content());
			//$thepostcontent = get_the_content();
			$thepostcontent = htmlentities($thepostcontent, ENT_QUOTES | ENT_IGNORE,"UTF-8");
				$thepostcontent = preg_replace('/[\t\r\n]*/', '', $thepostcontent);
			$ett_posts[$count]['Content'] = $thepostcontent;
		}
		
		if(in_array('Excerpt', $_POST['data_filter'])) {
			$thepostexcerpt = htmlentities(get_the_excerpt(),ENT_QUOTES | ENT_IGNORE,"UTF-8");
			$thepostexcerpt = preg_replace('/[\t\r\n]*/', '', $thepostexcerpt);
			$ett_posts[$count]['Excerpt'] = $thepostexcerpt;
		}
	
		if(in_array('Author', $_POST['data_filter'])) $ett_posts[$count]['Author'] = get_the_author();
		if(in_array('Author Email', $_POST['data_filter'])) $ett_posts[$count]['Author Email'] = get_the_author_email();
		
		if(in_array('Custom Fields', $_POST['data_filter'])) {
			$custom_field_keys = get_post_custom_keys();
			if (!empty($custom_field_keys)) {
				sort($custom_field_keys);
				$custom_field_keys_ready = array();
				
				foreach ( $custom_field_keys as $key => $value ) {
					$valuet = trim($value);
					if ( '_' != $valuet{0} ) {
						$mykey_values = get_post_custom_values($value);
						sort($mykey_values);
						foreach ( $mykey_values as $key2 => $value2 ) {
							$custom_field_keys_ready[] = htmlentities(preg_replace('/[\t\r\n]*/', '', "$value => $value2"),ENT_QUOTES | ENT_IGNORE,"UTF-8");
						}
					}
				}
				
				$ett_posts[$count]['Custom Fields'] = implode('. ', $custom_field_keys_ready);
			}
			else {
				$ett_posts[$count]['Custom Fields'] = "";
			}
		}

		if(in_array('Comments', $_POST['data_filter'])) {		
			$args = array(
				'status' => 'approve',
				'post_id' => get_the_ID()
			);
			$comments = get_comments($args);
			
			if (!empty($comments)) {
				$comments_ready = array();
				
				foreach($comments as $comment) {
					$comment_content = htmlentities($comment->comment_content,ENT_QUOTES | ENT_IGNORE,"UTF-8");
					$comment_content = preg_replace('/[\t\r\n]*/', '', $comment_content);
					
					$comments_ready[] = $comment->comment_author.' => '.$comment_content;
				}
				$ett_posts[$count]['Comments'] = implode('. ', $comments_ready);
			}
			else {
				$ett_posts[$count]['Comments'] = "";
			}
		}
		
		$count ++;
		
	endwhile; else:
		$ett_posts = 0;
	endif;
	
	//after data is ready - now its time for show!
	if(count($ett_posts) > 0) {
		
		if( $_POST['download'] == '0' ) {
			$begin = '<table class="wp-list-table widefat fixed"><thead><tr>'.sre2t_implode_wrapped('<th>', '</th>', $_POST['data_filter']).'</tr></thead><tbody>';
			$begin_row = '<tr>';
			$end_row = '</tr>';
			$end = '</tbody></table>';
		}
		else {
			$begin = implode("\t", $_POST['data_filter'])."\r\n";
			$begin_row = '';
			$end_row = "\r\n";
			$end = '';
		}
		
		echo $begin;
			
		foreach($ett_posts as $ett_post) {
			echo $begin_row;
					
			foreach($_POST['data_filter'] as $data) {
				if( $_POST['download'] == '0' )
					echo '<td>'.$ett_post[$data].'</td>';
				else {
					$data = html_entity_decode($ett_post[$data]);
					if(strlen($data) > 32752)
						$data = substr($data, 0, 32752).'[limit]'."\t";
					echo $data."\t";
				}
			}
			
			echo $end_row;
		}
		
		echo $end;
	}
					
	if ( ($_POST['sdate'] != 'all' || $_POST['edate'] != 'all') && !empty($_POST['sdate']) ) { remove_filter('posts_where', 'filter_where'); }
	
	die(); //Functions echoing for AJAX must die
}
?>
<?php
/*  Copyright 2010  Sky Rocket Inc.  (email : jonathan.clarke@skyrocketonlinemarketing.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
