<?php

/* ini_set('display_errors', 1);
  error_reporting(E_ALL); */

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ex_admin_menu
 *
 * @author Hamid Ejaz
 */
require_once dirname(__FILE__) . '/../models/Exercise_model.php';

class Ex_admin_menu
{

    protected $pluginPath;
    protected $pluginUrl;
    protected $Exercise;
    var $operation = '';

    function __construct()
    {
        // Set Plugin Path
        $this->pluginPath = dirname(__FILE__);

        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/exercise';
        $this->Exercise  = new Exercise_model();
//        global $operation;
//        $operation       = new Operations();
    }

    function create_admin_menu()
    {
        //this is the main item for the menu
        add_menu_page(
                'Mattevideo Exercises', //page title
                'Mattevideo Exercises', //menu title
                'manage_options', //capabilities
                'mattevideo_exercise', //menu slug
                array($this, 'mattevideo_exercise') //function
        );
        add_submenu_page('mattevideo_exercise', //parent slug
                         'Create Exercise', //page title
                         'Create Exercise', //menu title
                         'manage_options', //capability
                         'create_exercise', //menu slug
                         array($this, 'create_exercise')); //function
    }

    function mattevideo_exercise()
    {
        if(isset($_REQUEST['id']) && isset($_REQUEST['detail'])){
            $data = $this->Exercise->get_exercise_userdata($_REQUEST['id'], $_REQUEST['user_id']);
            $alt1Data = $this->Exercise->get_alt1_count($_REQUEST['id']);
            $alt2Data = $this->Exercise->get_alt2_count($_REQUEST['id']);
            $alt3Data = $this->Exercise->get_alt3_count($_REQUEST['id']);
            exercise_render_detail_page($data, $alt1Data, $alt2Data, $alt3Data);
        }else {
            if (isset($_REQUEST['dup']) && $_REQUEST['dup'] == 'true' && isset($_REQUEST['id'])) {
                if ($dup = $this->Exercise->dup_exercise($_REQUEST['id'])) {
                    ob_start();
                    if(isset($_REQUEST['redirect'])){
                        wp_redirect('/wp-admin/admin.php?page=create_exercise&id=' . $dup);
                    } else {
                        wp_redirect('/wp-admin/admin.php?page=mattevideo_exercise&dup=' . $dup);
                    }
                    exit;
                } else {
                    ob_start();
                    wp_redirect('/wp-admin/admin.php?page=mattevideo_exercise&dup=error');
                    exit;
                }
            }
            $cat = null;
            $ex_type = isset($_GET['exercise_type'])?$_GET['exercise_type']:null;
            $year = isset($_GET['year'])?$_GET['year']:null;
            $term = isset($_GET['term'])?$_GET['term']:null;
            if(isset($_GET['course'])) {
                $category = get_category_by_slug($_GET['course']);
                $cat = $category->cat_ID;
            }
            $data['exercise'] = $this->Exercise->get_exercises($cat, $ex_type, $year, $term);

            $course = null;
            $ex_type = null;
            $year = null;
            $term = null;
            if (isset($_REQUEST['course']) && $_REQUEST['course'] != 'All') {
                $category = get_category_by_slug($_REQUEST['course']);
                $course = $category->cat_ID;
            }
            if (isset($_REQUEST['exercise_type'])) {
                $ex_type = $_REQUEST['exercise_type'];
            }
            if (isset($_REQUEST['year'])) {
                $year = $_REQUEST['year'];
            }
            if (isset($_REQUEST['term'])) {
                $term = $_REQUEST['term'];
            }
            $data['listing_count'] = $this->Exercise->get_listing_count($course, $ex_type, $year, $term);
            exercise_render_list_page($data);
        }
    }

    function create_exercise()
    {
        if (isset($_POST['action']) && $_POST['action'] == 'create')
        {
            $data['course_id'] = implode(',', $_POST['course']);
            $data['year'] = $_POST['year'];
            $data['term'] = $_POST['term'];
            $data['exercise_name'] = $_POST['exercise_name'];
            $data['sub_chapter_id'] = $_POST['sub_chapter'];
            $data['relevant_video'] = $_POST['relevant_video'];
            $data['duration'] = $_POST['duration'];
            $data['tools'] = $_POST['tool'];
            $data['exercise_type'] = $_POST['ex_type'];
            $data['corr_alternative'] = $_POST['corr_alternative'];
            $data['solution_setup'] = $_POST['solution_setup'];
            $data['context'] = $_POST['text_context_html'];
            $alt_1_exp = explode('----<alt_epx>----', $_POST['alt_1_exp']);
            $data['alt_1'] = trim($alt_1_exp[0]);
            $data['alt_1_exp'] = (isset($alt_1_exp[1]) ? trim($alt_1_exp[1]) : '');
            $alt_2_exp = explode('----<alt_epx>----', $_POST['alt_2_exp']);
            $data['alt_2'] = trim($alt_2_exp[0]);
            $data['alt_2_exp'] = (isset($alt_2_exp[1]) ? trim($alt_2_exp[1]) : '');
            $alt_3_exp = explode('----<alt_epx>----', $_POST['alt_3_exp']);
            $data['alt_3'] = trim($alt_3_exp[0]);
            $data['alt_3_exp'] = (isset($alt_3_exp[1]) ? trim($alt_3_exp[1]) : '');
            $data['publish'] = (isset($_POST['is_published']) ? $_POST['is_published'] : 0);
            $data['created_at'] = time();
            $message = '';
            if (isset($_POST['exercise_id'])) {
                $insert_id = $_POST['exercise_id'];
                if ($this->Exercise->update_exercise($data, $insert_id)) {
                    $message = 'success&id=' . $insert_id;
                } else {
                    $message = 'error&id=' . $insert_id;
                }
            } else {
                if ($insert_id = $this->Exercise->save_exercise($data)) {
                    $message = 'success&id=' . $insert_id;
                } else {
                    $message = 'error';
                }
            }
            wp_redirect('/wp-admin/admin.php?page=create_exercise&m=' . $message);
            exit;
        }
        global $pluginPath;
        $categories = get_categories(array('parent' => 0));
        $not_require = array('teori', 'oppgavevideo', 'frontpage-box');
        $filtered_categories = array();
        $chapters = array();
        $i = 1;
        foreach ($categories as $cate) {
            if (!in_array($cate->slug, $not_require)) {
                $filtered_categories[] = $cate;
            }
            $i++;
        }
        if (isset($_REQUEST['id'])) {
            $_data['exercise'] = $this->Exercise->get_exercise($_REQUEST['id']);
        }
        extract($_data);
        include $pluginPath . "/views/create_exercise.php";
    }

    public function deleteRecords($type){
        if($type == 'exercise'){
            if($this->Exercise->delete_exercises($_REQUEST['exercise'])){
                wp_redirect('/wp-admin/admin.php?page=mattevideo_exercise&m=deleted');
                exit;
            }
        }
    }
    
    public function searchUserid(){
        if($user_id != ''){
            wp_redirect('/wp-admin/admin.php?page=mattevideo_exercise&id=2&detail=true&orderby=created_at&order=desc&user_id=' . $user_id);
            exit;
        }
    }

}
