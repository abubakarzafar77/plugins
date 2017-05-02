<?php
if(!session_id()){
    session_start();
}
/*

  Plugin Name: Mattevideo Exercise

  Plugin URI: http://purelogics.net

  Description: Purpose of this plugins is provide exercise.

  Version: 1.0

  Author: Purelogics.net| Muhammad Saleem

  Author URI: http://purelogics.net

 */

/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/

$GLOBALS['course_name_tabs'] = array(
    1 => '1P',
    2 => '1PY',
    3 => 'IT',
    4 => '2P',
    5 => 'S1',
    6 => 'R1',
    7 => 'S2',
    8 => 'R2'
);

include_once 'class/Ex_admin_menu.php';
$pluginPath = dirname(__FILE__);

if(!class_exists('Exercise_List_Table')){
    require_once( dirname(__FILE__).'/class/exercise-list-table.php' );
}

if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php");
}

class Exercise
{

    protected $pluginPath;
    protected $pluginUrl;
    var $operation = '';
    protected $ex_admin = null;
    protected $Exercise;

    public function __construct()
    {
// Set Plugin Path

        $this->pluginPath = dirname(__FILE__);

        $this->pluginUrl = WP_PLUGIN_URL . '/exercise';

        $this->load_style_scripts();
        $this->Exercise  = new Exercise_model();
        $this->ex_admin = new Ex_admin_menu();
        add_action('admin_menu', array($this->ex_admin, 'create_admin_menu'));
    }

    function load_style_scripts()
    {

        function tr_enqueue_custom_admin_style()
        {
            wp_register_style('select2_css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css', false, '1.0.0');
            wp_register_style('custom_ex_admin_css', WP_PLUGIN_URL . '/exercise/css/ex_admin_css.css', false, '1.0.1');
            wp_enqueue_style('custom_ex_admin_css');
            wp_enqueue_style('select2_css');
        }

        function tr_enqueue_custom_admin_scripts()
        {
            //wp_enqueue_script('jquery', 'jquery', false);
            wp_enqueue_script('select2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js', array('jquery'), '4.0.2');
            wp_enqueue_script('custom_ex_admin_script', WP_PLUGIN_URL . '/exercise/js/ex_admin_js.js', array('jquery'), '1.0');
            wp_enqueue_script('jquery_validation_script', WP_PLUGIN_URL . '/exercise/plugins/jquery-validation-1.15.1/dist/jquery.validate.js', array('jquery'), '1.0');
        }

        wp_localize_script( 'ajax-script-ajax_object', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );

        add_action('admin_enqueue_scripts', 'tr_enqueue_custom_admin_style');
        add_action('admin_enqueue_scripts', 'tr_enqueue_custom_admin_scripts');
    }

    public function load_subChapters(){
        $categories = array();
        foreach ($_POST['courses'] as $course){
            $_categories = get_categories(array('parent' => $course));
            foreach ($_categories as $_category){
                $___categories = get_categories(array('parent' => $_category->term_id));
                $categories = array_merge($categories, $___categories);
            }
            //$__categories = get_categories(array('parent' => $course));
        }
        $html = "";
        foreach ($categories as $category){
            $html .= "<option value='".$category->term_id."'>".$category->name."</option>";
        }
        $html .= "<option value='99999999'>Other</option>";
        echo json_encode(array('html'=>$html));
        exit();
    }

    public function deleteRecords($type){
        $this->ex_admin->deleteRecords($type);
    }

    public function shortcode_exercise(){
        include $this->pluginPath . "/views/questions.php";
    }

    public function plugin_activate(){

        /*global $wpdb;

        $ExerciseVersion = "1.0";
        add_option("ExerciseVersion", $ExerciseVersion);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."exercise` (
              `id` int(11) NOT NULL,
              `course_id` varchar(255) DEFAULT NULL,
              `year` varchar(4) DEFAULT NULL,
              `term` varchar(7) DEFAULT NULL,
              `exercise_name` varchar(30) DEFAULT NULL,
              `sub_chapter_id` int(11) DEFAULT NULL,
              `relevant_video` varchar(100) DEFAULT NULL,
              `duration` enum('1-min','2-min','3-min','3|5-min','5|8-min','8|12-min','15-min') DEFAULT NULL,
              `tools` enum('yes','no') DEFAULT NULL,
              `exercise_type` enum('calculation','text') DEFAULT NULL,
              `corr_alternative` enum('alt1','alt2','alt3') DEFAULT NULL,
              `context` text,
              `alt_1` text,
              `alt_1_exp` text,
              `alt_2` text,
              `alt_2_exp` text,
              `alt_3` text,
              `alt_3_exp` text,
              `publish` enum('yes','no') DEFAULT 'no',
              `created_at` int(11) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        dbDelta($sql);

        $sql = "ALTER TABLE `".$wpdb->prefix."exercise` ADD PRIMARY KEY (`id`);";

        dbDelta($sql);


        $sql = "ALTER TABLE `".$wpdb->prefix."exercise` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";

        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."exercise_attempts` (
              `id` int(11) NOT NULL,
              `exercise_id` int(11) DEFAULT NULL,
              `user_id` int(11) DEFAULT NULL,
              `correct` tinyint(1) DEFAULT '0',
              `alt_choose` tinyint(1) DEFAULT '0',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        dbDelta($sql);

        $sql = "ALTER TABLE `".$wpdb->prefix."exercise_attempts` ADD PRIMARY KEY (`id`);";

        dbDelta($sql);

        $sql = "ALTER TABLE `".$wpdb->prefix."exercise_attempts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."exercise_course` (
              `id` int(11) NOT NULL,
              `exercise_id` int(11) DEFAULT NULL,
              `course_id` int(11) DEFAULT NULL,
              `created_at` int(11) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        dbDelta($sql);

        $sql = "ALTER TABLE `".$wpdb->prefix."exercise_course` ADD PRIMARY KEY (`id`);";

        dbDelta($sql);

        $sql = "ALTER TABLE `".$wpdb->prefix."exercise_course` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";

        dbDelta($sql);*/
    }

    public function plugin_deactivate(){

    }

    private function diagram_counts($course, $terms){
        return $this->Exercise->get_counts($course, $terms);
    }

    private function get_category_solution($where, $course, $terms, $term_id, $color){
        $exercises = $this->Exercise->get_category_solution($where, $course, $terms, $term_id);
        $html ='';
        if(count($exercises) > 0) {
            $html .= '<p>';
        }
        if($color == 'i_completed'){
            for ($i=0;$i<count($exercises);$i++){
                $date = $this->Exercise->get_exercise_solution_date($exercises[$i]);
                $current = strtotime('now');
                $days = ($current - $date) / (60*60*24);
                $user_id = get_current_user_id();
                if ($user_id != "") {
                    if ($days) {
                        if ($days > 0 && $days < 31) {
                            $html .= '<span class="a-green"></span>';
                        } else if ($days > 30 && $days < 91) {
                            $html .= '<span class="a-blue"></span>';
                        } elseif ($days > 90) {
                            $html .= '<span class=""></span>';
                        }
                    } else {
                        $html .= '<span class=""></span>';
                    }
                } else {
                    $html .= '<span class=""></span>';
                }
            }
        } else {
            for ($i=0;$i<count($exercises);$i++){
                $percent = $this->Exercise->get_exercise_solution_percent($exercises[$i]);
                if(round($percent) >= 80){
                    $html .= '<span class="a-green"></span>';
                }elseif(round($percent) < 80 && round($percent) >= 60){
                    $html .= '<span class="a-yellow"></span>';
                }elseif (round($percent) < 60 && round($percent) >= 1){
                    $html .= '<span class="a-orange"></span>';
                }else{
                    $html .= '<span class=""></span>';
                }
            }
        }
        if(count($exercises) > 0) {
            $html .= '</p>';
        }
        return $html;
    }

    public function loadExercises(){
        global $current_user;
        $sort_by = isset($_POST['sort_by'])?$_POST['sort_by']:'times_completed';
        $sort_type = isset($_POST['sort_type'])?$_POST['sort_type']:'ASC';
        $course = $_POST['course'];
        $terms = $_POST['terms'];
        $term_id = $_POST['term'];
        $parent_id = $_POST['parent'];
        $question = isset($_POST['question'])?$_POST['question']:0;
        $_selected = $selected = (isset($_POST['selected'])?$_POST['selected']:'');
        $where = '';
        $where_tools = array();
        $where_type = array();
        if($selected){
            $selected = explode('|', $selected);
            foreach($selected as $key=>$value){
                if($value == 'yes' || $value == 'no') {
                    $where_tools[] = 'tools="'.$value.'"';
                } else if($value == 'calculation' || $value == 'text') {
                    $where_type[] = 'exercise_type="'.$value.'"';
                }
            }
        }
        $final_where = array();
        if(!empty($where_tools)){
            $final_where[] = '('.implode(' OR ', $where_tools).')';
        }
        if(!empty($where_type)){
            $final_where[] = '('.implode(' OR ', $where_type).')';
        }
        $where = implode(' AND ', $final_where);

        if($where){
            $where = ' AND '.$where;
        }


        if($question){
            $where .= ' AND we.id='.$question;
        }

        if($term_id == 'all' && !isset($_POST['sort_by'])){
            $sort_by = 'year';
            $sort_type = 'ASC';
        }

        $exercises = $this->Exercise->get_category_exercises($where, $course, $terms, $term_id, $sort_by, $sort_type);

        if($parent_id) {
            $parent_category_name = get_cat_name($parent_id);
        }else{
            $parent_category_name = 'All exercises for '.($terms[0]['spring']?'spring ':'autumn ').$terms[0]['year'];
        }
        $category_name = get_cat_name($term_id);

        ob_start();

        include $this->pluginPath . "/views/modal.php";
        $detail = ob_get_contents();
        ob_end_clean();
        echo json_encode(array('detail'=>$detail));
        exit;
    }

    public function loadDiagram(){
        global $_durations, $_tools, $_question_type;
        $course = $_POST['course'];
        $__terms = $_POST['terms'];
        $showOpen = false;
        if(count($__terms) == 1) {
            foreach ($__terms as $__term) {
                if (isset($__term['spring']) && !isset($__term['autumn'])){
                    $showOpen = true;
                }
                else if (!isset($__term['spring']) && isset($__term['autumn'])){
                    $showOpen = true;
                }
            }
        }
        $color = $_POST['color'];
        //$data = $this->Exercise->get_data($_POST);
        if(!isset($_POST['selected'])) {
            $counts = $this->diagram_counts($course, $__terms);

            extract($counts);
            ob_start();
            include $this->pluginPath . "/views/diagram_section.php";
            $diagram = ob_get_contents();
            ob_end_clean();
        }

        $categories = get_categories(array('parent' => $course));
        $selected = (isset($_POST['selected'])?$_POST['selected']:'');
        $where = '';
        $where_tools = array();
        $where_type = array();
        if($selected){
            $selected = explode('|', $selected);
            foreach($selected as $key=>$value){
                if($value == 'yes' || $value == 'no') {
                    $where_tools[] = 'tools="'.$value.'"';
                } else if($value == 'calculation' || $value == 'text') {
                    $where_type[] = 'exercise_type="'.$value.'"';
                }
            }
        }
        $final_where = array();
        if(!empty($where_tools)){
            $final_where[] = '('.implode(' OR ', $where_tools).')';
        }
        if(!empty($where_type)){
            $final_where[] = '('.implode(' OR ', $where_type).')';
        }
        $where = implode(' AND ', $final_where);

        ob_start();

        include $this->pluginPath . "/views/categories.php";
        $categories = ob_get_contents();
        ob_end_clean();

        $term = '';
        ob_start();

        if($showOpen === TRUE) {
            include $this->pluginPath . "/views/term.php";
            $term = ob_get_contents();
            ob_end_clean();
        }

        echo json_encode(array('diagram'=>$diagram, 'categories'=>$categories, 'terms'=>$term));
        exit;
    }

    public function saveAttempt(){
        $this->Exercise->saveAttempt($_POST);
        echo json_encode(array('success'=>'yes'));
        exit;
    }

    public function get_last_attempt_date($exercise_id){
        $ret = $this->Exercise->get_last_attempt_date($exercise_id);
        if($ret){
            return $ret.' last answered';
        }
        return 'not answered';
    }

    public function count_completed($exercise_id){
        return $this->Exercise->count_completed($exercise_id);
    }

    public function show_exercises($terms, $term_id, $course){
        return '<span class=""></span><span class=""></span>';
    }

    public function print_csv()
    {
        if ( ! current_user_can( 'manage_options' ) )
            return;

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=exercises.csv');
        header('Pragma: no-cache');

        $data = $this->Exercise->get_exercises_csv();

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Exercise Id', 'Course', 'Exercise Name', 'Year', 'Term', 'Sub-Chapter', 'Relevant video', 'Duration', 'Tools', 'Exercise Type', 'Correct Alt', 'Publish'));
        foreach ($data as $_data) {
            fputcsv($output, $_data);
        }

    }

    public function my_function( $login ) {
        $user = get_user_by('login',$login);
        $_SESSION['your_current_user_id'] = $user->ID;
    }

    public function wp_logout_function(){
        unset($_SESSION['your_current_user_id']);
    }

}


class Mattevideo_exercise_table extends WP_Widget
{

    protected $pluginPath;
    /** constructor */
    function Mattevideo_exercise_table()
    {
        parent::WP_Widget(false, $name = 'Mattevideo exercise table');
        $this->pluginPath = dirname(__FILE__);
        wp_register_style('custom_ex_style_css', WP_PLUGIN_URL . '/exercise/css/style.css', false, '1.0.0');
        wp_enqueue_style('custom_ex_style_css');
        wp_enqueue_script('custom_ex_script', WP_PLUGIN_URL . '/exercise/js/script.js', array('jquery'), '1.0');
        wp_localize_script( 'custom_ex_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance)
    {
        global $_tools, $_question_type;
        global $course_tab;
        $category_info = get_category_parent(true);
        $category = $category_info['slug'];
        if(in_array($category, $course_tab) || is_super_admin()){
        echo '<div data-pws-tab="tab2" data-pws-tab-name="<div class=\'btn-tabs\'>Eksamensquiz</div>">';
        echo $before_widget;
        
        include $this->pluginPath . "/views/questions.php";
        echo $after_widget;
        echo '</div>';
        }
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

$GLOBALS['ex'] = $ex = new Exercise();
$GLOBALS['_durations'] = array('1-min', '2-min', '3-min', '3|5-min', '5|8-min', '8|12-min', '15-min');
$GLOBALS['_tools'] = array('no', 'yes');
$GLOBALS['_question_type'] = array('calculation', 'text');

if(!function_exists('deleteRecord')){
    function deleteRecord($type){
        global $ex;
        $ex->deleteRecords($type);
    }
}

if($ex){
    # Initializing Exercise Widget
    add_action('widgets_init', create_function('', 'return register_widget("Mattevideo_exercise_table");'));

    # Register Activation/De-Activation Hooks
    register_activation_hook(__FILE__, array($ex, 'plugin_activate'));
    register_deactivation_hook(__FILE__, array($ex, 'plugin_deactivate'));

    # Hooks for the Loading SubChapters
    add_action( 'wp_ajax_nopriv_load_subChapters', array($ex, 'load_subChapters') );
    add_action( 'wp_ajax_load_subChapters', array($ex, 'load_subChapters') );

    # Hooks for the Loading of Diagram
    add_action( 'wp_ajax_nopriv_loadDiagram', array($ex, 'loadDiagram') );
    add_action( 'wp_ajax_loadDiagram', array($ex, 'loadDiagram') );

    # Hooks for the Loading of Exercises
    add_action( 'wp_ajax_nopriv_loadExercises', array($ex, 'loadExercises') );
    add_action( 'wp_ajax_loadExercises', array($ex, 'loadExercises') );


    # Hooks for the saving the exercise attempt
    add_action( 'wp_ajax_nopriv_saveAttempt', array($ex, 'saveAttempt') );
    add_action( 'wp_ajax_saveAttempt', array($ex, 'saveAttempt') );
    if($_REQUEST['page'] == 'create_exercise') {
        add_filter('wp_default_editor', create_function(null, 'return "html";'));
    }

    add_action( 'admin_post_exercises.csv', array($ex, 'print_csv') );
    add_action( 'wp_login', array($ex, 'my_function') );
    add_action( 'wp_logout', array($ex, 'wp_logout_function') );


}
?>