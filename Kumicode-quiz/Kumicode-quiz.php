<?php
/*
  Plugin Name: Kumicode-quiz
  Plugin URI: http://kumicode.com/Kumicode-quiz
  Description: Quiz plug-in.
  Version: 1.0
  Author: Øyvind Dahl
  Author URI: http://kumicode.com
  License: GPL
 */

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);




if (!class_exists("KumicodeQuiz"))
{

    class KumicodeQuiz
    {

        var $quizTableName    = "kumicodeQuiz";
        var $quizTableStatics = 'kumicodeQuiz_statics';
        var $metaNameForQuiz  = 'kumicodeQuizTaken';

        /*
         * 	install quiz
         */

        function installQuiz()
        {
            //echo "<h2>installQuiz</h2>";
            global $wpdb;

            $kumicodeQuizVersion = "1.0";
            add_option("kumicodeQuizVersion", $kumicodeQuizVersion);
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $tableName = $wpdb->prefix . $this->quizTableName;


            // insert db tables
            $sql = "CREATE TABLE " . $tableName . "(
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
        function uninstallQuiz()
        {
            global $wpdb;

            //$tableName = $wpdb->prefix."kumicodeQuiz";
            //$wpdb->query("DROP TABLE IF EXISTS $tableName");
        }

        /*
         * 	deleteQuestion
         *
         */

        function deleteQuestion($questionid)
        {
            global $wpdb;

            $tableName = $wpdb->prefix . $this->quizTableName;

            $result = $wpdb->query($wpdb->prepare("DELETE FROM $tableName WHERE id = '%d'", $questionid));
        }

        /*
         * 	print_main_quiz_menu
         *
         */

        function print_main_quiz_menu()
        {
            global $wpdb;

            // save quiz

            if ($_POST['todo'] == "saveQuiz")
            {

                $questionContent = array(
                    'id'                => $_POST['questionid'],
                    'question'          => stripslashes($_POST['quiz_question']),
                    'answer1'           => stripslashes($_POST['quiz_answer1']),
                    'answer2'           => stripslashes($_POST['quiz_answer2']),
                    'answer3'           => stripslashes($_POST['quiz_answer3']),
                    'answer4'           => stripslashes($_POST['quiz_answer4']),
                    'answer5'           => stripslashes($_POST['quiz_answer5']),
                    'quiz_right_answer' => $_POST['quiz_right_answer'],
                    'category'          => $_POST['quiz_category']
                );

                if ($_POST['questionid'] > 0)
                {
                    $this->updateQuiz($questionContent);
                }
                else
                {

                    $this->saveQuiz($questionContent);
                }
            }
            else if ($_GET['todo'] == "getquestion")
            {
                $this->drawQuizTable(4);
                exit();


                //	edit question	
            }
            else if ($_GET['todo'] == "edit")
            {
                $oldQuestion = $this->getQuestion($_GET['questionid']);


                //	delete question	
            }
            else if ($_GET['todo'] == "deleteQuestion")
            {

                if (current_user_can('administrator') && $_GET['questionid'] > 0)
                {
                    $this->deleteQuestion($_GET['questionid']);
                }
            }

            echo '<div class="wrap">
					<h2>Insert new quiz</h2>';

            //	add quiz
            echo '		<form action="admin.php?page=kumicodeQuizOptions" method="post">
						<input type="hidden" name="todo" count="saveQuiz">
						<input type="hidden" name="questionid" count="' . $oldQuestion->id . '">
						<div class="addQuestion">
							<h3>Spørsmål:</h3>
							<textarea name="quiz_question" id="question_input">' . $oldQuestion->question . '</textarea>
						<br/>
						<div class="admin_answer_box">
							<h3>Svar 1:</h3>
							<textarea name="quiz_answer1">' . $oldQuestion->answer1 . '</textarea>
						</div>
						<div class="admin_answer_box">
							<h3>Svar 2:</h3>
							<textarea name="quiz_answer2">' . $oldQuestion->answer2 . '</textarea>
						</div>
						<div class="admin_answer_box">
							<h3>Svar 3:</h3>
							<textarea name="quiz_answer3">' . $oldQuestion->answer3 . '</textarea>
						</div>
						<!--<div class="admin_answer_box">
							<h3>Svar 4:</h3>
							<textarea name="quiz_answer4">' . $oldQuestion->answer4 . '</textarea>
						</div>
						<div class="admin_answer_box">
							<h3>Svar 5:</h3>
							<textarea name="quiz_answer5">' . $oldQuestion->answer5 . '</textarea>
						</div>-->';


            echo '	<div class="correct_answer">
							<label>' . __('Riktig svar:', 'Kumicode_mattevideo') . '</label>
							<select name="quiz_right_answer">
								<option></option>';
            for ($a = 1; $a <= 3; $a++)
            {
                $selected = ($a == $oldQuestion->correct_answer) ? " SELECTED" : "";
                echo '<option count="' . $a . '"' . $selected . '>' . $a . '</option>';
            }
            echo '		</select>
						</div>';


            //	get Chapters/categories
            echo '		<div class="category_picker">
							<select name="quiz_category">';


            /* Adding categories for S1, 1PY, and 2P */

            $idForS1         = get_cat_ID('s1');
            $categoriesForS1 = get_categories(array('parent' => $idForS1, 'hide_empty' => false));

            echo '<option></option>
					<option>---   S1   ---</option>		
					<option></option>		
			';

            foreach ($categoriesForS1 as $category)
            {
                echo '<option count="' . $category->cat_ID . '">' . $category->name . '</option>';


                $subCategories = get_categories(array('parent' => $category->cat_ID, 'hide_empty' => false));
                foreach ($subCategories as $subCategory)
                {
                    $selected = ($oldQuestion->categoryID == $subCategory->cat_ID) ? ' SELECTED' : "";
                    echo '<option count="' . $subCategory->cat_ID . '"' . $selected . '>- ' . $subCategory->name . '</option>';
                }
                echo '<option></option>';
            }


            $idFor1Py         = get_cat_ID('1py');
            $categoriesFor1PY = get_categories(array('parent' => $idFor1Py, 'hide_empty' => false));

            echo '<option></option>
					<option>---   1PY   ---</option>		
					<option></option>		
			';

            foreach ($categoriesFor1PY as $category)
            {
                echo '<option count="' . $category->cat_ID . '">' . $category->name . '</option>';


                $subCategories = get_categories(array('parent' => $category->cat_ID, 'hide_empty' => false));
                foreach ($subCategories as $subCategory)
                {
                    $selected = ($oldQuestion->categoryID == $subCategory->cat_ID) ? ' SELECTED' : "";
                    echo '<option count="' . $subCategory->cat_ID . '"' . $selected . '>- ' . $subCategory->name . '</option>';
                }
                echo '<option></option>';
            }


            $idFor2P         = get_cat_ID('2p');
            $categoriesFor2P = get_categories(array('parent' => $idFor2P, 'hide_empty' => false));

            echo '<option></option>
					<option>---   2P   ---</option>		
					<option></option>		
			';

            foreach ($categoriesFor2P as $category)
            {
                echo '<option count="' . $category->cat_ID . '">' . $category->name . '</option>';


                $subCategories = get_categories(array('parent' => $category->cat_ID, 'hide_empty' => false));
                foreach ($subCategories as $subCategory)
                {
                    $selected = ($oldQuestion->categoryID == $subCategory->cat_ID) ? ' SELECTED' : "";
                    echo '<option count="' . $subCategory->cat_ID . '"' . $selected . '>- ' . $subCategory->name . '</option>';
                }
                echo '<option></option>';
            }


            $idForR2         = get_cat_ID('r2');
            $categoriesForR2 = get_categories(array('parent' => $idForR2, 'hide_empty' => false));

            echo '<option></option>
					<option>---   R2   ---</option>		
					<option></option>		
			';

            foreach ($categoriesForR2 as $category)
            {
                echo '<option count="' . $category->cat_ID . '">' . $category->name . '</option>';


                $subCategories = get_categories(array('parent' => $category->cat_ID, 'hide_empty' => false));
                foreach ($subCategories as $subCategory)
                {
                    $selected = ($oldQuestion->categoryID == $subCategory->cat_ID) ? ' SELECTED' : "";
                    echo '<option count="' . $subCategory->cat_ID . '"' . $selected . '>- ' . $subCategory->name . '</option>';
                }
                echo '<option></option>';
            }

            $idForR1         = get_cat_ID('r1');
            $categoriesFor1P = get_categories(array('parent' => $idForR1, 'hide_empty' => false));

            echo '<option></option>
					<option>---   R1   ---</option>		
					<option></option>		
			';

            foreach ($categoriesFor1P as $category)
            {
                echo '<option count="' . $category->cat_ID . '">' . $category->name . '</option>';


                $subCategories = get_categories(array('parent' => $category->cat_ID, 'hide_empty' => false));
                foreach ($subCategories as $subCategory)
                {
                    $selected = ($oldQuestion->categoryID == $subCategory->cat_ID) ? ' SELECTED' : "";
                    echo '<option count="' . $subCategory->cat_ID . '"' . $selected . '>- ' . $subCategory->name . '</option>';
                }
                echo '<option></option>';
            }

            $idFor1T         = get_cat_ID('1t');
            $categoriesFor1T = get_categories(array('parent' => $idFor1T, 'hide_empty' => false, 'orderby' => 'description', 'order' => 'ASC'));



            echo '			<option></option>
							<option>---   1T   ---</option>
							<option></option>
							';

            foreach ($categoriesFor1T as $category)
            {
                echo '<option count="' . $category->cat_ID . '">' . $category->name . '</option>';


                $subCategories = get_categories(array('parent' => $category->cat_ID, 'hide_empty' => false, 'orderby' => 'description', 'order' => 'ASC'));
                foreach ($subCategories as $subCategory)
                {
                    $selected = ($oldQuestion->categoryID == $subCategory->cat_ID) ? ' SELECTED' : "";
                    echo '<option count="' . $subCategory->cat_ID . '"' . $selected . '>- ' . $subCategory->name . '</option>';
                }
                echo '<option></option>';
            }


            $idFor1P         = get_cat_ID('1p');
            $categoriesFor1P = get_categories(array('parent' => $idFor1P, 'hide_empty' => false));

            echo '<option></option>
					<option>---   1P   ---</option>		
					<option></option>		
			';

            foreach ($categoriesFor1P as $category)
            {
                echo '<option count="' . $category->cat_ID . '">' . $category->name . '</option>';


                $subCategories = get_categories(array('parent' => $category->cat_ID, 'hide_empty' => false));
                foreach ($subCategories as $subCategory)
                {
                    $selected = ($oldQuestion->categoryID == $subCategory->cat_ID) ? ' SELECTED' : "";
                    echo '<option count="' . $subCategory->cat_ID . '"' . $selected . '>- ' . $subCategory->name . '</option>';
                }
                echo '<option></option>';
            }


            echo '		</select>
						</div>
						<h3>&nbsp;</h3>
						<input type="submit" count="Save">
						</form>
						</div>
						';


            //	edit / delete quiz
            $tableName = $wpdb->prefix . $this->quizTableName;
            $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableName ORDER BY categoryID ASC", ''));

            echo '<h2>Edit quiz</h2>
						<div class="editQuestion">';
            foreach ($questions as $question)
            {

                $category = get_category($question->categoryID);
                echo '<div class="question">';
                echo '<h3>' . $category->name . "</h3>";
                echo '<label>' . do_shortcode($question->question) . "</label><br/>";
                echo '<label class="answer"><strong>Svar 1:</strong> ' . do_shortcode($question->answer1) . "</label><br/>";
                echo '<label class="answer"><strong>Svar 2:</strong> ' . do_shortcode($question->answer2) . "</label><br/>";
                echo '<label class="answer"><strong>Svar 3:</strong> ' . do_shortcode($question->answer3) . "</label><br/>";

                echo '<br/><label class="answer">Riktig svar: ' . $question->correct_answer . "</label><br/>";
                echo '<a href="?page=kumicodeQuizOptions&todo=edit&questionid=' . $question->id . '">edit</a>
						<a hreF="?page=kumicodeQuizOptions&todo=deleteQuestion&questionid=' . $question->id . '">delete</a>
						</div>';
            }
            echo '</div>';
        }

        /*
         *
         *
         */

        function getQuestion($questionID)
        {

            global $wpdb;
            $tableName = $wpdb->prefix . $this->quizTableName;
            $row       = $wpdb->get_row("SELECT * FROM $tableName WHERE id = '$questionID'");

            return $row;
        }

        /*
         * 	
         *
         */

        function get_questions($category_id)
        {
            global $wpdb;
            if ($category_id > 0)
            {
                $tableName = $wpdb->prefix . $this->quizTableName;
                $result    = $wpdb->get_results("SELECT * FROM $tableName WHERE categoryID = '$category_id'");
                return $result;
            }
            else
            {
                return false;
            }
        }

        function checkIfCategoryHasQuestions($category_id)
        {
            global $wpdb;
            if ($category_id > 0)
            {
                $tableName = $wpdb->prefix . $this->quizTableName;
                $result    = $wpdb->get_results("SELECT count(id) AS numberOfQuestions, complexity as average FROM $tableName WHERE categoryID = '$category_id' GROUP BY categoryID");
                //$numberOfQuestions = $result[0]->numberOfQuestions;
                //return $numberOfQuestions;
                return $result[0];
            }
            else
            {
                return false;
            }
        }

        function tooltip_results($category_id)
        {
            $array=[0,0];
            global $wpdb;
            if ($category_id > 0)
            {
                $tableName = $wpdb->prefix . $this->quizTableName;
                $results   = $wpdb->get_results("SELECT id , (SELECT COUNT(*)FROM kumicodeQuiz_statics WHERE quiz_id=k.quiz_id AND uid=k.uid GROUP BY uid) AS total_count FROM  `kumicodeQuiz_statics` AS k WHERE  `quiz_id` ={$category_id} AND answer =100 GROUP BY uid");
                if ($results)
                {
                    $count=0;
                    $user=count($results);
                    foreach ($results as $result)
                    {
                        $count += $result->total_count;
                    }
                    $array[0] =  round($count/$user, 1);
                    $array[1] = $user;
                }
            }
            return $array;
        }

        function userAttemptedQuizes($user_id, $category_id)
        {
            global $wpdb;
            // echo "SELECT quiz_id FROM kumicodeQuiz_statics WHERE quiz_id='".$category_id."' AND uid = '$user_id'";
            if ($category_id > 0)
            {
                $result = $wpdb->get_results("SELECT quiz_id FROM kumicodeQuiz_statics WHERE quiz_id='" . $category_id . "' AND uid = '$user_id'");
                return $result;
            }
            else
            {
                return false;
            }
        }

        function drawQuizTable($category_id)
        {
            echo "Quiz $category_id is here!";
        }

        /*
         * 	printQuiz
         *
         *
         */

        function printQuiz($atts)
        {
            global $wpdb;
            $checkAnswers = ($_POST['todo'] == "send_answers");

            if ($checkAnswers)
            {

                $category_id = $_POST['catid'];
                $questions   = $this->get_questions($category_id);

                $answer1            = ($questions[0]->correct_answer == $_POST[$questions[0]->id]);
                $answer2            = ($questions[1]->correct_answer == $_POST[$questions[1]->id]);
                $answer3            = ($questions[2]->correct_answer == $_POST[$questions[2]->id]);
                $answer4            = ($questions[3]->correct_answer == $_POST[$questions[3]->id]);
                $answer5            = ($questions[4]->correct_answer == $_POST[$questions[4]->id]);
                $percentage         = 100 / count($questions);
                $corrent_percentage = 0;
                foreach ($questions as $question)
                {
                    if ($question->correct_answer == $_POST[$question->id])
                    {
                        $corrent_percentage+=$percentage;
                    }
                }

                $user_id        = $this->get_user_id();
                $insertAttempts = array(
                    'uid'     => $user_id,
                    'quiz_id' => $category_id,
                    'answer'  => $corrent_percentage
                );
                $_sql = "SELECT * FROM {$this->quizTableStatics} WHERE answer = 100 AND uid={$user_id} AND quiz_id={$category_id}";
                $_result = $wpdb->get_row($_sql);
                if($_result){
                    $wpdb->delete($this->quizTableStatics, array('uid'=>$user_id, 'quiz_id'=>$category_id));
                }
                $wpdb->insert($this->quizTableStatics, $insertAttempts);
                $sql            = 'SELECT AVG( answer ) as complexity FROM  `' . $this->quizTableStatics . '` WHERE quiz_id =' . $category_id;
                $result         = $wpdb->get_results($sql);
                $wpdb->update($wpdb->prefix . $this->quizTableName, array('complexity' => $result[0]->complexity), array('categoryID' => $category_id));
            }

            if ($answer1 && $answer2 && $answer3 && $answer4 && $answer5)
            {

                $quizContent = '<h1>' . __('Gratulerer!', 'Kumicode_mattevideo') . '</h1>
									<div>' . __('Du svarte riktig på alle spørsmålene. <br/>
												For å se hvilken quiz du har bestått, refresh ditt pensum på mattevideo.no siden<br/> (kun for betalende brukere).', 'Kumicode_mattevideo') . '</div>';

                //	Saving result to user

                if ($current_user->ID != 0)
                {
                    $prevQuizTaken = get_user_meta($current_user->ID, $this->metaNameForQuiz, true);

                    if (isset($prevQuizTaken) && !in_array($_POST['catid'], explode('|', $prevQuizTaken)))
                    {
                        $quizTaken = $prevQuizTaken . '|' . $_POST['catid'];
                    }
                    else
                    {
                        $quizTaken = $_POST['catid'];
                    }
                    update_user_meta($current_user->ID, $this->metaNameForQuiz, $quizTaken);
                }
            }
            else
            {


                $questions = $this->get_questions($_GET['catid']);

                $category = get_the_category($questions[0]->categoryID);

                $directory = 'wp-content/uploads/quiz_popup_bg/';
                $filecount = 0;
                if (glob($directory . "*.*") != false)
                {
                    $filecount = count(glob($directory . "*.*"));
                }
                // echo $filecount;
                $random_n               = rand(1, $filecount);
                $popup_background_image = 'quiz_' . $random_n . '.jpg';
                $image_url              = WP_SITEURL . '/wp-content/uploads/quiz_popup_bg/' . $popup_background_image;
                #background-image: url(' . $image_url . ');background-size: 680px auto;

                $quizContent .= '<div class="quiz2" style="">
                                        <!--<h2 class="quiz" id="quizCategory">' . $category[0]->name . '</h2>-->
                                        <form id="quiz_submit_form" action="" method="POST">
                                                <input type="hidden" name="todo" value="send_answers">
                                                <input id="catid_input" type="hidden" name="catid" value="' . $_GET['catid'] . '">';

                foreach ($questions as $question)
                {

                    if ($checkAnswers)
                    {

                        $answerResultText = ($question->correct_answer == $_POST[$question->id]) ? 'Riktig' : 'Feil';
                        $correctAnswer1   = ( $_POST[$question->id] == 1) ? 1 : 0;
                        $correctAnswer2   = ( $_POST[$question->id] == 2) ? 1 : 0;
                        $correctAnswer3   = ( $_POST[$question->id] == 3) ? 1 : 0;
                    }


                    $quizContent .= '<div class="koib ' . $answerResultText . '"> 
									<label class="question">' . do_shortcode($question->question) . '</label>';
                    $quizContent .= '<div class="answer_box" >
									<label>Svar 1: </label>
									<input type="radio" name="' . $question->id . '" value="1" ' . ($correctAnswer1 ? 'checked="checked"' : '') . '>
									<label>' . do_shortcode($question->answer1) . '</label>
								</div>';
                    $quizContent .= '<div class="answer_box">
									<label>Svar 2: </label>
									<input type="radio" name="' . $question->id . '" value="2" ' . ($correctAnswer2 ? 'checked="checked"' : '') . '>
									<label>' . do_shortcode($question->answer2) . ' </label>
								</div>';
                    $quizContent .= '<div class="answer_box">
									<label>Svar 3: </label>
									<input type="radio" name="' . $question->id . '" value="3" ' . ($correctAnswer3 ? 'checked="checked"' : '') . '>
									<label>' . do_shortcode($question->answer3) . '</label>
								</div>';
                    /* if(isset($question->answer4)){
                      $quizContent .= '<div class="answer_box">
                      <label>'.do_shortcode($question->answer4).' </label>
                      <input type="radio" name="'.$question->id.'" count="4" '.$correctAnswer4.'>
                      </div>';
                      }
                      if(isset($question->answer5)){
                      $quizContent .= '<div class="answer_box">
                      <label>'.do_shortcode($question->answer5).' </label>
                      <input type="radio" name="'.$question->id.'" count="5" '.$correctAnswer5.'>
                      </div>';
                      } */
                    $quizContent .= '<hr/><br/></div>';
                }

                //$quizContent .= '<input type="submit" count="' . __('Lever svar', 'Kumicode_mattevideo') . '">';
                $quizContent .= '<input type="submit" value="' . __('Lever svar', 'Kumicode_mattevideo') . '">';
                $quizContent .= '</div>';
            }
            return $quizContent;
        }

        private function get_user_id()
        {
            $user_id      = 0;
            $current_user = wp_get_current_user();
            if ($current_user->ID != 0)
            {
                $user_id = $current_user->ID;
                unset($_SESSION['anonymus_user']);
            }
            else
            {
                if (isset($_SESSION['anonymus_user']))
                {
                    $user_id = $_SESSION['anonymus_user'];
                }
                else
                {
                    $user_id                   = $this->RandomStringGenerator();
                    $_SESSION['anonymus_user'] = $user_id;
                }
            }
            return $user_id;
        }

        private function RandomStringGenerator($letters = 6, $digits = 2)
        {
            $result  = null;
            $charset = array
                (
                0 => array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'),
                1 => array('a', 'e', 'i', 'o', 'u'),
            );

            for ($i = 0; $i < $letters; $i++)
            {
                $result .= $charset[$i % 2][array_rand($charset[$i % 2])];
            }

            for ($i = 0; $i < $digits; $i++)
            {
                $result .= mt_rand(0, 9999);
            }

            return $result;
        }

        /*
         * 	saveQuiz
         *
         */

        function saveQuiz($questionContent)
        {
            global $wpdb;
            $tableName = $wpdb->prefix . $this->quizTableName;

            //	check data
            //	save into quizdb
            $elementsAndValues = array('question'       => $questionContent['question'],
                'answer1'        => $questionContent['answer1'],
                'answer2'        => $questionContent['answer2'],
                'answer3'        => $questionContent['answer3'],
                'correct_answer' => $questionContent['quiz_right_answer'],
                'categoryID'     => $questionContent['category'],
            );

            $result = $wpdb->insert($tableName, $elementsAndValues);

            if ($result)
            {
                echo '<h2>Arkiverte spørsmål ' . $questionContent['question'] . '</h2>';
            }
            else
            {
                echo '<h2>En feil oppstod med arkivering av spørsmål</h2>';
            }
        }

        /*
         * 	updateQuiz
         *
         */

        function updateQuiz($questionContent)
        {

            global $wpdb;
            $tableName = $wpdb->prefix . $this->quizTableName;

            $result = $wpdb->update($tableName, array('question'       => $questionContent['question'],
                'answer1'        => $questionContent['answer1'],
                'answer2'        => $questionContent['answer2'],
                'answer3'        => $questionContent['answer3'],
                'answer4'        => $questionContent['answer4'],
                'answer5'        => $questionContent['answer5'],
                'correct_answer' => $questionContent['quiz_right_answer'],
                'categoryID'     => $questionContent['category']
                    ), array('id' => $questionContent['id'])
            );

            if ($result)
            {
                echo '<h2>Oppdaterte spørsmål ' . $questionContent['question'] . '</h2>';
            }
            else
            {
                echo '<h2>En feil oppstod med oppdatering av spørsmål</h2>';
            }
        }

    }

}
/**
 * Mattevideo_chapter_widget Class
 */
class Mattevideo_quiz_table extends WP_Widget
{
    

    /** constructor */
    function Mattevideo_quiz_table()
    {
        parent::WP_Widget(false, $name = 'Mattevideo quiz table');
    }
    
    /** @see WP_Widget::widget */
    function widget($args, $instance)
    {
        $average_ratio=array('0'=>0,'1'=>0,'2'=>0,'3'=>0,'4'=>0);
        //if (!in_category('r2')) { //remove this line for R2 quiz, by Sofiane
        //global $wpdb;
        
        $kumicodeQuiz = new KumicodeQuiz();
        $category_info = get_category_parent(true);
        $parent_id     = $category_info['parent_id'];
        $cat           = $category_info['slug'];

        /* if(in_category(get_cat_ID('1p'))){
          $parent_id = get_cat_ID('1p');
          $cat = "1p";
          }else if(in_category(get_cat_ID('r1'))){
          $parent_id = get_cat_ID('r1');
          $cat = "r1";
          }else if(in_category(get_cat_ID('1py'))){
          $parent_id = get_cat_ID('1py');
          $cat = "1py";
          }//else if(in_category(get_cat_ID('s1'))){
          //$parent_id = get_cat_ID('s1');
          //$cat = "s1";
          //}
          else if(in_category(get_cat_ID('2p'))){
          $parent_id = get_cat_ID('2p');
          $cat = "2p";
          } else if(in_category(get_cat_ID('r2'))){
          $parent_id = get_cat_ID('r2');
          $cat = "r2";
          }else {
          $parent_id = get_cat_ID('1t');
          $cat = "1t";
          } */


        //get chapters
        //$categoryIdFor1T = get_cat_ID('1T');
        $chapters = get_categories(array(
            'parent'     => $parent_id,
            'hide_empty' => 0,
        ));
//        echo "<pre>";
//        print_r($chapters);
        extract($args);
        $attemps_sql="SELECT uid , COUNT( quiz_id ) FROM  `kumicodeQuiz_statics` GROUP BY uid";
        
        $title    = apply_filters('widget_title', $instance['title']);

        global $course_tab;
        $category_info = get_category_parent(true);
        $category = $category_info['slug'];
        $tab_text = '';
        if(!in_array($category, $course_tab) || is_super_admin()){
            $tab_text = '<span class=\'tab_smal_text\'>Quiz</span>';
        }

        echo '<div data-pws-tab="tab5" data-pws-tab-name="'.$tab_text.'">';
        echo $before_widget;
        /* if ( $title ) echo $before_title . $title . $after_title; */

        // echo '<div>the area for graph</div>';
        echo '<div class="quiz_' . $cat . '" id="quiz"><div class="row max_width">';

        $current_user = wp_get_current_user();

        /*

          if ($current_user->ID != 0)
          {
          echo "here in";
          $quizResult = explode('|', get_user_meta($current_user->ID, $kumicodeQuiz->metaNameForQuiz, true));
          }
         */



        //feed queestions
        $quizHeight = 850;
        $boxHeight  = 20;


        //	TOP RIGHT #2
        $tempChapters = get_categories(array('parent' => $chapters[0]->cat_ID, 'hide_empty' => 0));
        $tempHeight   = (($quizHeight / 2) - ($boxHeight * count($tempChapters))) / 2;

        echo '<div class="col-md-2"><h3 >' . $chapters[0]->name . '</h3>';

        echo'	<ul class="bottomleft">';

        $this->drawQuestionBoxes($tempChapters, $tempHeight, $quizResult, 1);
        echo '	</ul></div>';


        //	RIGHT	#3
        $tempChapters = get_categories(array('parent' => $chapters[1]->cat_ID, 'hide_empty' => 0));
        $tempHeight   = ($quizHeight - ($boxHeight * count($tempChapters))) / 2;

        echo '<div class="col-md-2"><h3 >' . $chapters[1]->name . '</h3>';

        echo '	<ul class="topright" >';
        $this->drawQuestionBoxes($tempChapters, $tempHeight, $quizResult, 2);
        echo '	</ul></div>';


        //	BOTTOM RIGHT
        $tempChapters = get_categories(array('parent' => $chapters[2]->cat_ID, 'hide_empty' => 0));
        $tempHeight   = (($quizHeight / 2) - ($boxHeight * count($tempChapters))) / 2;

        echo '<div class="col-md-2"><h3 >' . $chapters[2]->name . '</h3>';

        echo'	<ul class="left" >';
        $this->drawQuestionBoxes($tempChapters, $tempHeight, $quizResult, 3);
        echo '	</ul></div>';
        echo '<div class="clearfix"></div><div class="clearfix"></div>';

        //	BOTTOM LEFT
        $tempChapters = get_categories(array('parent' => $chapters[3]->cat_ID, 'hide_empty' => 0));
        $tempHeight   = (($quizHeight / 2) - ($boxHeight * count($tempChapters))) / 2;

        echo '<div class="col-md-2"><h3 >' . $chapters[3]->name . '</h3>';

        echo '<ul class="topleft">';
        $this->drawQuestionBoxes($tempChapters, $tempHeight, $quizResult, 4);
        echo '	</ul></div>';




        //	LEFT #6
        $tempChapters = get_categories(array('parent' => $chapters[4]->cat_ID, 'hide_empty' => 0));
        $tempHeight   = ($quizHeight - ($boxHeight * count($tempChapters))) / 2;

        echo '<div class="col-md-2"><h3 >' . $chapters[4]->name . '</h3>';

        echo '	<ul class="bottomright">';
        $this->drawQuestionBoxes($tempChapters, $tempHeight, $quizResult, 5);
        echo '	</ul></div>';




        //	TOP LEFT #1
        $tempChapters = get_categories(array('parent' => $chapters[5]->cat_ID, 'hide_empty' => 0));
        $tempHeight   = (($quizHeight / 2) - ($boxHeight * count($tempChapters))) / 2;

        echo '<div class="col-md-2"><h3>' . $chapters[5]->name . '</h3>';

        echo '	<ul class="right">';
        $this->drawQuestionBoxes($tempChapters, $tempHeight, $quizResult, 6);
        echo '	</ul></div></div>';
        echo $after_widget;
        global $average_ratio;
        echo '<div class="col-md-12" style="text-align:center; margin: 60px 0px; font-size: 12px;">
        <div class="b_bullet"><label>Lett quiz</label> <span class="bullet-0"></span> <label class="last" style="padding: 0 0 0 7px;">'.(isset($average_ratio[0])?mmmr($average_ratio[0], 'median'):'0').' forsøk</label></div>
        <div class="b_bullet"><label>&nbsp;</label><span class="bullet-1"></span> <label class="last">'.(isset($average_ratio[1])?mmmr($average_ratio[1], 'median'):'0').' forsøk</label></div>
        <div class="b_bullet"><label>Middles quiz </label><span class="bullet-2"></span> <label class="last">'.(isset($average_ratio[2])?mmmr($average_ratio[2], 'median'):'0').' forsøk</label></div>
        <div class="b_bullet"><label>&nbsp;</label><span class="bullet-3"></span> <label class="last">'.(isset($average_ratio[3])?mmmr($average_ratio[3],'median'):'0').' forsøk</label></div>
        <div class="b_bullet"><label>Vansklig quiz</label><span class="bullet-4"></span> <label class="last">'.(isset($average_ratio[4])?mmmr($average_ratio[4], 'median'):'0').' forsøk</label></div>
        <div class="bullet_txt1">
        Farge indikerer hvor vansklig quizen er
        </div>
        <div class="bullet_txt2">utifra </div>
        <div class="bullet_txt3">
        hvor mange forsøk deltakere trenger for de svarer riktig
        </div>
        </div>';
        echo '<div class="col-md-6" style="display: none;">';
        echo '<div id="percentile_graph" style=" width:460px; height: 175px; padding-left: 0px !important;margin-left: -21px !important;;"></div>';
        echo '<div class="bullet_txt4">Du broker I gjennomsnitt 1.7 forsook for du svarer riktig, du svarer bedre enn 70% av quizdeltakerne</div>';
        echo '</div></div>';
        echo '</div>';




        //echo '<div data-pws-tab="tab3" data-pws-tab-name="Eksamens gjennomgang ">Eksamens gjennomgang';
    }

    /* //remove the next lines untill //end for R2 quiz, by Sofiane
      else {
      extract( $args );
      $title = apply_filters('widget_title', $instance['title']);
      echo $before_widget;

      if ( $title ) echo $before_title . $title . $after_title;
      echo "R2 quiz er under produksjon og vil publiseres høst 2013";
      echo $after_widget;
      }
      }
      //end */

    /*
     * 	drawQuestionBoxes
     * 	
     *
     */

    function drawQuestionBoxes($chapters, $height, $userQuizTaken = array(), $side = 0)
    {
        $kumicodeQuiz = new KumicodeQuiz();
        global $current_user;
        if ($current_user->ID != 0)
        {
            $user_id = $current_user->ID;
        }
        else
        {
            $user_id = $_SESSION['anonymus_user'];
        }

        //echo '<div class="vertical-center" style="position:absolut; top:50%; margin-top:'.$height.'px;">';

        foreach ($chapters as $chapter)
        {

            $numberOfQuestions = $kumicodeQuiz->checkIfCategoryHasQuestions($chapter->cat_ID);
            $tooltip_result    = $kumicodeQuiz->tooltip_results($chapter->cat_ID);
            global $average_ratio;
            $taken       = $kumicodeQuiz->userAttemptedQuizes($user_id, $chapter->cat_ID);
            $taken_class = '';
            if (count($taken) > 0 && is_array($taken))
            {
                $taken_class = 'active fa fa-check';
            }

            if (count($userQuizTaken))
            {

                $done = (in_array($chapter->cat_ID, $userQuizTaken)) ? " done done_" . $side : "";
            }
            $class = round($tooltip_result[0] - 1);
            if($class < 0){
                $class = 0;
            }else if($class > 4){
                $class = 4;
            }
            echo '<li class="question_box' . $done . '">';
            // also have complexity here
            if ($numberOfQuestions->numberOfQuestions > 0)
            {
                $box_heigh = 650;
                if ($numberOfQuestions->numberOfQuestions <= 2)
                {
                    $box_heigh = $numberOfQuestions->numberOfQuestions * 330;
                }
                $average_ratio[$class][] = $tooltip_result[0];
                //$average_ratio[$class]['count']+=$tooltip_result[1];
                echo '<a title="" href="' . get_page_link(150) . '?catid=' . $chapter->cat_ID . '&amp;height=' . $box_heigh . '&amp;width=700&amp;TB_iframe=true" data-users=' . $tooltip_result[1] . ' data-ratio=' . round($tooltip_result[0], 2) . ' class="thickbox quiz_box_tooltip">' . str_replace('\n', '<br />', $chapter->name) . '</a><span id="quiz_cat_id_bullet_' . $chapter->cat_ID . '"  class="bullet-' . $class . " " . $taken_class . ' " ></span>';
            }
            else
            {
                echo str_replace('\n', '<br />', $chapter->name);
            }
            echo '</li>';
        }

        //echo '</div>';	
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance)
    {
        $instance          = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance)
    {
        $title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" count="<?php echo $title; ?>" />
        </p>
        <?php
    }

}

// class Mattevideo CHapter widget


/* 	1.	 */
if (class_exists("KumicodeQuiz"))
{
    $kumicodeQuiz = new KumicodeQuiz();
}


/* 	2	 */
if (!function_exists("kumicodeQuizOptions"))
{

    function kumicodeQuizDelegate()
    {

        //echo "kumicodeQuizDelegate<br/>";

        global $kumicodeQuiz;
        if (!isset($kumicodeQuiz))
        {
            return;
        }

        //	Add main menu page	
        add_menu_page('Quiz', 'Quiz', "moderate_comments", "kumicodeQuizOptions", array(&$kumicodeQuiz, 'print_main_quiz_menu'));
    }

}


/* 	3	 */
if ($kumicodeQuiz)
{


    /*
     * 	Actions
     */
    add_action('admin_menu', 'kumicodeQuizDelegate');
    add_action('widgets_init', create_function('', 'return register_widget("Mattevideo_quiz_table");'));

    /*
     * 	Shortcode
     */
    add_shortcode('quiz', array(&$kumicodeQuiz, 'printQuiz'));

    /*
     * 	Activation code
     */
    register_activation_hook(__FILE__, array(&$kumicodeQuiz, 'installQuiz'));

    /*
     * 	Deactivation code
     */
    register_deactivation_hook(__FILE__, array(&$kumicodeQuiz, 'uninstallQuiz'));

    /*
     * 	css Style
     */
    $pathToStyleFile = WP_PLUGIN_URL . "/Kumicode-quiz/quiz_style.css";
    wp_register_style('kumicode_quiz_style', $pathToStyleFile);
    wp_enqueue_style('kumicode_quiz_style');
}
?>