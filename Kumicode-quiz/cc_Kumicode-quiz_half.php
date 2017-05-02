<?php
/*
xxxPlugin Name: Kumicode-quiz
xxxPlugin URI: http://kumicode.com/Kumicode-quizx
xxxDescription: Quiz plug-in.  
xxxVersion: 1.0
xxxAuthor: Øyvind Dahl
xxxxAuthor URI: http://kumicode.com
xxxLicense: GPL
*/

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );


if(!class_exists("KumicodeQuiz")) {
	
	class KumicodeQuiz{
		
		
		var $quizTableName = "kumicodeQuiz";
		var $metaNameForQuiz = 'kumicodeQuizTaken';
		
		
		/*
		*	install quiz
		*/
		function installQuiz(){
			//echo "<h2>installQuiz</h2>";
			global $wpdb;
			
			$kumicodeQuizVersion = "1.0";
			add_option("kumicodeQuizVersion", $kumicodeQuizVersion);
			require_once(ABSPATH.'wp-admin/includes/upgrade.php');
			
			$tableName = $wpdb->prefix.$this->quizTableName;
			
			
			// insert db tables
			$sql = "CREATE TABLE ".$tableName."(
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					modified datetime DEFAULT '0000-00-00 0:00' NOT NULL,
					question text NOT NULL,
					answer1 text,
					answer2 text,
					answer3 text,
					answer4 text,
					answer5 text,
					correct_answer int,
					categoryID int,
					UNIQUE  KEY id (id)
					);";
			
		
			//echo "<h2>sql</h2>$sql<br/>";		
			dbDelta($sql);
		}
		
		//	uninstall db-tables
		function uninstallQuiz(){
			global $wpdb;
			
			$tableName = $wpdb->prefix."kumicodeQuiz";
			$wpdb->query("DROP TABLE IF EXISTS $tableName");
		}
		
		
		
		
		/*
		*	print_main_quiz_menu
		*
		*/
		function print_main_quiz_menu(){
			global $wpdb;
			
			
			if($_POST['todo'] == "saveQuiz"){
				
				$questionContent = array(
						'question' => $_POST['quiz_question'],
						'answer1' => $_POST['quiz_answer1'],
						'answer2' => $_POST['quiz_answer2'],
						'answer3' => $_POST['quiz_answer3'],
						'answer4' => $_POST['quiz_answer4'],
						'answer5' => $_POST['quiz_answer5'],
						'quiz_right_answer' => $_POST['quiz_right_answer'],
						'category' => $_POST['quiz_category'],
						'quizid' => $_POST['quizid']
						);
				
				if($_POST['quizid'] > 0){
					echo "<h2>Updated question</h2>";
					$this->updateQuiz($questionContent);
				
				}else{
					echo "<h2>saved question</h2>";						
					$this->saveQuiz($questionContent);
				}
				
				
			
			}else if($_GET['todo'] == "getquestion"){
				$this->drawQuizTable(4);
				exit();
				
			}else if($_GET['todo'] == "deleteQuestion" && $_GET['quizid'] > 0){
				$this->deleteQuestion($_GET['quizid']);
				
			}else if( $_GET['quizid'] > 0 ){
				$oldQuestion = $this->getQuestion($_GET['quizid']);
			}
			
			
			
			echo '<div class="wrap">
					
					<h2>Insert new quiz</h2>';
			
			//	add quiz
			echo '		<form action="admin.php?page=kumicodeQuizOptions" method="post">
						<input type="hidden" name="todo" value="saveQuiz">
						<input type="hidden" name="quizid" value="'.$_GET['quizid'].'">
						<div class="addQuestion">
							<h3>Spørsmål:</h3>
							<textarea name="quiz_question">'.$oldQuestion->question.'</textarea>
							<br/>
							<div class="answer_box">
								<h3>Svar 1:</h3>
								<textarea name="quiz_answer1">'.$oldQuestion->answer1.'</textarea>
							</div>
							<div class="answer_box">
								<h3>Svar 2:</h3>
								<textarea name="quiz_answer2">'.$oldQuestion->answer2.'</textarea>
							</div>
							<div class="answer_box">
								<h3>Svar 3:</h3>
								<textarea name="quiz_answer3">'.$oldQuestion->answer3.'</textarea>
							</div>
							<div class="answer_box">
								<h3>Svar 4:</h3>
								<textarea name="quiz_answer4">'.$oldQuestion->answer4.'</textarea>
							</div>
							<div class="answer_box">
								<h3>Svar 5:</h3>
								<textarea name="quiz_answer5">'.$oldQuestion->answer5.'</textarea>
							</div>
							<div class="correct_answer">
								<h3>'.__('Riktig svar:', 'Kumicode_mattevideo').'</h3>
								<select name="quiz_right_answer">
									<option>Riktig svar</option>';
							for($a = 1; $a <= 5; $a++ ){
									$chosen = ($a == $oldQuestion->correct_answer) ? " SELECTED": "";
									echo '<option value="'.$a.'"'.$chosen.'>'.$a.'</option>';
							}
							
				echo'			</select>
							</div>';
			
			//	get Chapters/categories
			$idFor1T = get_cat_ID('1t');
			$categoriesFor1T = get_categories( array('parent' => $idFor1T, 'hide_empty' => false, 'orderby' => 'description', 'order' => 'ASC'));
			
			
			echo '			<div class="category_picker">
							<h3>Velg kapittel:</h3>
							<select name="quiz_category">
								<option></option>
								<option>---   1T   ---</option>
								<option></option>';
			
			foreach($categoriesFor1T as $category){
				echo '<option value="'.$category->cat_ID.'">'.$category->name.'</option>';
				
				$subCategories = get_categories( array('parent' => $category->cat_ID, 'hide_empty' => false, 'orderby' => 'description', 'order' => 'ASC'));
				foreach($subCategories as $subCategory){
					$chosen = ($subCategory->cat_ID == $oldQuestion->categoryID) ? ' SELECTED': "";
					echo '<option value="'.$subCategory->cat_ID.'"'.$chosen.'>- '.$subCategory->name.' </option>';
				}
				echo '<option></option>';
				
				
			}
			
			
			$idFor2P = get_cat_ID('2P');
			$categoriesFor2P = get_categories( array('parent' => $idFor2P, 'hide_empty' => false));
			
			echo '<option></option>
					<option>---   2P   ---</option>		
					<option></option>		
			';
			
			foreach($categoriesFor2P as $category){
				echo '<option value="'.$category->cat_ID.'">'.$category->name.'</option>';
				
				$subCategories = get_categories( array('parent' => $category->cat_ID, 'hide_empty' => false));
				foreach($subCategories as $subCategory){
					echo '<option value="'.$subCategory->cat_ID.'">- '.$subCategory->name.'</option>';
				}
				echo '<option></option>';
				
				
			}	
			
			echo '			</select>
						</div>
						<h3> &nbsp;</h3>
						<input type="submit" value="Save">
					</div>';
			
			
			//	edit / delete quiz
			$tableName = $wpdb->prefix.$this->quizTableName;
			$questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableName"));
			
			echo '<h2>Edit quiz</h2>
						<div class="editQuestion">';
			foreach($questions as $question){
				$category = get_category($question->categoryID);
				echo '<div class="question">
						<label></label> ';
				echo '<label>'.$category->name."</label>";
				echo '<label> '.do_shortcode($question->question)."</label><br/>  ";
				echo '<label class="answer">Svar 1: '.do_shortcode($question->answer1)." </label><br/>  ";
				echo '<label class="answer">Svar 2: '.do_shortcode($question->answer2)." </label><br/>  ";
				echo '<label class="answer">Svar 3:'.do_shortcode($question->answer3)." </label><br/>  ";
				//echo '<label>'.do_shortcode($question->answer4)." </label>  ";
				//echo '<label>'.do_shortcode($question->answer5)."</label><br/>  ";
				echo '<label class="answer">Riktig svar: '.$question->correct_answer." </label><br/> ";
				echo '<label>
							<a href="?page=kumicodeQuizOptions&quizid='.$question->id.'">[edit]</a> 
							<a href="?page=kumicodeQuizOptions&todo=deleteQuestion&quizid='.$question->id.'"> [delete] </a><br/></label> </div>';
			}				
			echo '		</div>
					</div>';
		}
		
		
		/*
		*	deleteQuestion
		*
		*/
		function deleteQuestion($questionId){
			
			if(current_user_can('administrator')){
			
				global $wpdb;
				$tableName = $wpdb->prefix.$this->quizTableName;
				
				$result = $wpdb->query($wpdb->prepare("DELETE FROM $tableName WHERE id = '%d' ", $questionId));
			}else{
				echo "<h2>Error</h2><p>Kunne ikke slette spørsmål fordi du ikke har tilgang til denne funksjonen</p>";
			}
			
		}
		
		
		
		
		/*
		*	getQuestion
		*
		*
		*/
		function getQuestion($questionId){
			
			global $wpdb;
			$tableName = $wpdb->prefix.$this->quizTableName;
			
			$result = $wpdb->get_results(	$wpdb->prepare( "SELECT id, question, answer1, answer2, answer3, answer4, answer5, categoryID, correct_answer  
														FROM $tableName 
														WHERE id = %s", $questionId) );
			
			return $result[0];
		}
		
		
		
		
	
		
		
		function get_questions($category_id){
			global $wpdb;
			if($category_id > 0){
				$tableName = $wpdb->prefix.$this->quizTableName;
				$result = $wpdb->get_results( "SELECT * FROM $tableName WHERE categoryID = '$category_id'");
				return $result;
			}else{
				return false;
			}
		}
		
		
		
		function checkIfCategoryHasQuestions($category_id){
			global $wpdb;
			if($category_id > 0){
				$tableName = $wpdb->prefix.$this->quizTableName;
				$result = $wpdb->get_results( "SELECT count(id) AS numberOfQuestions FROM $tableName WHERE categoryID = '$category_id'");
				$numberOfQuestions = $result[0]->numberOfQuestions;
				return $numberOfQuestions;
			}else{
				return false;
			}
		}
		
		function drawQuizTable($category_id){
			echo "Quiz $category_id is here!";
		}
		
		
		
		/*
		*	printQuiz
		*
		*	
		*/
		function printQuiz($atts){
			
			$checkAnswers = ($_POST['todo'] == "send_answers");
			if($_POST['todo'] == "send_answers"){
				$category_id = $_POST['catid'];
				$questions = $this->get_questions($category_id);
				
				$answer1 = ($questions[0]->correct_answer == $_POST[$questions[0]->id]);
				$answer2 = ($questions[1]->correct_answer == $_POST[$questions[1]->id]);
				$answer3 = ($questions[2]->correct_answer == $_POST[$questions[2]->id]);
				$answer4 = ($questions[3]->correct_answer == $_POST[$questions[3]->id]);
				$answer5 = ($questions[4]->correct_answer == $_POST[$questions[4]->id]);
	
			}
				
				
			if($answer1 && $answer2 && $answer3 && $answer4 && $answer5){
		
					$quizContent = '<h1>'.__('Gratulerer!', 'Kumicode_mattevideo').'</h1>
									<div>'.__('Du svarte riktig på alle spørsmålene', 'Kumicode_mattevideo').'</div>';
					
					$current_user = wp_get_current_user();
					
					if($current_user->ID > 0){
						
						echo "saving result to user ".$current_user->ID."<br/>";
						
						$prevQuizTaken = get_user_meta($current_user->ID, 'kumicodeQuizTaken', true);
						echo "prevQuizTaken <br/>";
						print_r($prevQuizTaken);
						echo "<br/>";
						
						if(isset($prevQuizTaken) && in_array($_POST['catid'], explode('|', $prevQuizTaken))){
							$quizTaken = $prevQuizTaken.'|'.$_POST['catid'];
						}else{
							$quizTaken = $_POST['catid'];
						}
						echo "quizTaken  $quizTaken<br/>";
						$result = update_user_meta($current_user->ID, 'kumicodeQuizTaken', $quizTaken);
						echo "result $result<br/>";
					}
					
			}else{
			
			
				$questions = $this->get_questions($_GET['catid']);
		
				$quizContent .= '<div class="quiz">
								<form action="" method="POST">
									<input type="hidden" name="todo" value="send_answers">
									<input type="hidden" name="catid" value="'.$_GET['catid'].'">
									';
			
				foreach($questions as $question){
				
				if($checkAnswers){
					$answerResultText = ($question->correct_answer == $_POST[$question->id]) ? 'Riktig' : 'Feil';
					$correctAnswer1 = ( $_POST[$question->id] == 1) ? 'checked="checked"':'';
					$correctAnswer2 = ( $_POST[$question->id] == 2) ? 'checked="checked"':'';
					$correctAnswer3 = ( $_POST[$question->id] == 3) ? 'checked="checked"':'';
					$correctAnswer4 = ( $_POST[$question->id] == 4) ? 'checked="checked"':'';
					$correctAnswer5 = ( $_POST[$question->id] == 5) ? 'checked="checked"':'';
				}					
			
				
				$quizContent .= '<label class="question">'.do_shortcode($question->question).' '.$answerResultText.'</label>';
				$quizContent .= '<div class="answer_box">
									<label>Svar 1: </label>
									<input type="radio" name="'.$question->id.'" value="1" '.$correctAnswer1.'>
									<label>'.do_shortcode($question->answer1).' </label>
								</div>';				
				$quizContent .= '<div class="answer_box">
									<label>Svar 2: </label>
									<input type="radio" name="'.$question->id.'" value="2" '.$correctAnswer2.'>
									<label>'.do_shortcode($question->answer2).' </label>
								</div>';
				$quizContent .= '<div class="answer_box">
									<label>Svar 3: </label>
									<input type="radio" name="'.$question->id.'" value="3" '.$correctAnswer3.'>
									<label>'.do_shortcode($question->answer3).' </label>
				
								</div>';
				if($question->answer4 != ""){
					
					$quizContent .= '<div class="answer_box">
									<label>Svar 4: </label>
									<label>'.do_shortcode($question->answer4).' </label>
									<input type="radio" name="'.$question->id.'" value="4" '.$correctAnswer4.'>
								</div>';
				}
				if($question->answer5 != ""){
					$quizContent .= '<div class="answer_box">
										<label>Svar 5: </label>
										<input type="radio" name="'.$question->id.'" value="5" '.$correctAnswer5.'>
										<label>'.do_shortcode($question->answer5).' </label>
									</div>';
				}
				$quizContent .= '<hr/><br/>';
				}
			
				$quizContent .= '<input type="submit" value="'.__('Lever svar', 'Kumicode_mattevideo').'">';
				$quizContent .= '</div>';
    	
    	
    	//	TOP LEFT #1
    	$tempChapters = get_categories( array('parent' => $chapters[0]->cat_ID, 'hide_empty' => 0, 'orderby' => 'description', 'order' => 'ASC'));
    	$tempHeight = (($quizHeight/2) - ($boxHeight * count($tempChapters))) / 2;
    			
    	echo '	<div class="top" >
    				<div class="left" style="position:relative;">';
		    		$this->drawQuestionBoxes($tempChapters, $tempHeight);
	    echo '	</div>';
    	
    	//	TOP RIGHT #2
    	$tempChapters = get_categories( array('parent' => $chapters[2]->cat_ID, 'hide_empty' => 0, 'orderby' => 'description', 'order' => 'ASC'));
    	$tempHeight = (($quizHeight/2) - ($boxHeight * count($tempChapters))) / 2;
    	
    	echo'	<div class="right" style="position:relative;">';
		    		$this->drawQuestionBoxes($tempChapters, $tempHeight);
	    echo '	</div>
    			</div>';
    	
    	
    	//	BOTTOM LEFT
    	$tempChapters = get_categories( array('parent' => $chapters[4]->cat_ID, 'hide_empty' => 0, 'orderby' => 'description', 'order' => 'ASC'));
    	$tempHeight = (($quizHeight/2) - ($boxHeight * count($tempChapters))) / 2;
    			
    	echo '<div class="bottom">
    				<div class="left">';
		    		$this->drawQuestionBoxes($tempChapters, $tempHeight);
	    echo '	</div>';
		
		//	BOTTOM RIGHT
		$tempChapters = get_categories( array('parent' => $chapters[3]->cat_ID, 'hide_empty' => 0, 'orderby' => 'description', 'order' => 'ASC'));
    	$tempHeight = (($quizHeight/2) - ($boxHeight * count($tempChapters))) / 2;
    	
					
    	echo'	<div class="right"  style="position:relative;">';
		    		$this->drawQuestionBoxes($tempChapters, $tempHeight);
	    echo '	</div>
    		<div>';

    }
	
	/*
	*	drawQuestionBoxes
	*	
	*
	*/
	function drawQuestionBoxes($chapters, $height, $userQuizTaken = array()){
		$kumicodeQuiz = new KumicodeQuiz();
		
		echo '<div class="vertical-center" style="position:absolut; top:50%; margin-top:'.$height.'px;">';
    				
    				foreach($chapters as $chapter){
    					$numberOfQuestions = $kumicodeQuiz->checkIfCategoryHasQuestions($chapter->cat_ID);
    					
    					echo '	<div  class="question_box" '.(in_array($chapter->cat_ID, $userQuizTaken) ? 'style="background-color:orange;"': '').'>';
    					if($numberOfQuestions > 0){
    						echo '<a href="'.get_page_link(150).'?catid='.$chapter->cat_ID.'&amp;height=650&amp;width=700&amp;TB_iframe=true" class="thickbox">'.$chapter->name.'</a>';
    					}else{
							echo $chapter->name;
						}
						echo '</div>';
					}
	 
	    echo '</div>';	
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

} // class Mattevideo Chapter widget


/*	1.	*/
if(class_exists("KumicodeQuiz")){
	$kumicodeQuiz = new KumicodeQuiz();
}


/*	2	*/
if(!function_exists("kumicodeQuizOptions")){
	
	function kumicodeQuizDelegate(){
		
		//echo "kumicodeQuizDelegate<br/>";
		
		global $kumicodeQuiz;
		if(!isset($kumicodeQuiz)){
			return;
		}
		
		//	Add main menu page	
		add_menu_page('Quiz', 'Quiz', "moderate_comments", "kumicodeQuizOptions", array(&$kumicodeQuiz, 'print_main_quiz_menu'));
	}
	
}


/*	3	*/
if($kumicodeQuiz){


	/*
	*	Actions
	*/
	add_action('admin_menu', 'kumicodeQuizDelegate');
	add_action('widgets_init', create_function('', 'return register_widget("Mattevideo_quiz_table");'));
	
	/*
	*	Shortcode
	*/
	add_shortcode('quiz', array(&$kumicodeQuiz, 'printQuiz'));
	
	/*
	*	Activation code
	*/
	register_activation_hook(__FILE__, array(&$kumicodeQuiz, 'installQuiz'));
	
	/*
	*	Deactivation code
	*/
	register_deactivation_hook( __FILE__, array(&$kumicodeQuiz, 'uninstallQuiz'));
	
	/*
	*	
	*/
	$pathToStyleFile = WP_PLUGIN_URL."/Kumicode-quiz/style.css";
	wp_register_style('kumicode_quiz_style', $pathToStyleFile);
	wp_enqueue_style('kumicode_quiz_style');
}




?>