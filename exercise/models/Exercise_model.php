<?php

class Exercise_model{

    private $db, $table = 'exercise', $course_table = 'exercise_course', $attempts_table = 'exercise_attempts', $publish='';

    public function Exercise_model(){
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $this->db->prefix . $this->table;
        $this->course_table = $this->db->prefix . $this->course_table;
        $this->attempts_table = $this->db->prefix . $this->attempts_table;
        if(is_super_admin()){
            $this->publish = "(publish='1' OR publish='2')";
        }else{
            $this->publish = "publish='2'";
        }
    }

    public function save_exercise($data, $call = ''){
        if ($call == 'local') {
            $verify_is_authorized = true;
        } else {
            $verify_is_authorized = wp_verify_nonce($_REQUEST['createexercise_nounce'], 'createexercise_nounce');
        }
        if ($verify_is_authorized) {
            if ($this->db->insert($this->table, $data)) {
                $insert_id = $this->db->insert_id;
                $_data['exercise_id'] = $insert_id;
                $courses = explode(",", $data['course_id']);
                foreach($courses as $course){
                    $_data['course_id'] = $course;
                    $_data['created_at'] = time();
                    $this->save_exercise_course($_data);
                }
                return $insert_id;
            }
            return false;
        } else {
            return false;
        }
    }


    public function delete_exercise_courses($exercise_id){
        $this->db->delete($this->course_table, array('exercise_id'=>$exercise_id));
    }

    public function save_exercise_course($data){
        return $this->db->insert($this->course_table, $data);
    }

    public function update_exercise($data, $id){
        $verify_is_authorized = wp_verify_nonce($_REQUEST['updateexercise_nounce'], 'updateexercise_nounce');
        if ($verify_is_authorized) {
            if ($this->db->update($this->table, $data, array('id' => $id))) {
                $insert_id = $id;
                $this->delete_exercise_courses($insert_id);
                $courses = explode(",", $data['course_id']);
                $_data['exercise_id'] = $insert_id;
                foreach($courses as $course){
                    $_data['course_id'] = $course;
                    $_data['created_at'] = time();
                    $this->save_exercise_course($_data, 'update');
                }
                return $insert_id;
            }
            return false;
        } else {
            return false;
        }
    }

    public function get_exercise($id){
        return $this->db->get_row($this->db->prepare(
                                "SELECT * FROM $this->table WHERE id = %d", $id
                        ), ARRAY_A);
    }

    public function get_exercises($category_id=null,$toolsby=null,$year=null, $term=null){
        $tool='yes';
        $exercise_type='text';
        if ($toolsby) {
            switch ($toolsby) {
                case 'wtools_calc':
                    $tool='no';
                    $exercise_type='calculation';
                    break;
                case 'tools_calc':
                    $tool='yes';
                    $exercise_type='calculation';
                    break;
                case 'wtools_text':
                    $tool='no';
                    $exercise_type='text';
                    break;
                case 'tools_text':
                    $tool='yes';
                    $exercise_type='text';
                    break;
                default:
                    break;
            }
        }
        $sql = '';
        $where = array();
        if($category_id && $category_id != 'All'){
            $where[] = 'wec.course_id='.$category_id;
        }
        if($toolsby && $toolsby != 'All'){
            $where[] = 'tools="'.$tool.'" AND exercise_type="'.$exercise_type.'"';
        }
        if($year && $year != 'All'){
            $where[] = 'year="'.$year.'"';
        }
        if($term && $term != 'All'){
            $where[] = 'term="'.$term.'"';
        }
        $_where = '';
        if(!empty($where) && count($where) >= 2){
            $_where = implode(' AND ', $where);
        }else if(!empty($where) && count($where) == 1){
            $_where = implode(' ', $where);
        }


        if(empty($_where)){
            $sql = "SELECT we.*, (SELECT COUNT(id) FROM $this->attempts_table WHERE correct=1 AND exercise_id=we.id) * 100 / (SELECT COUNT(id) FROM $this->attempts_table WHERE exercise_id=we.id) AS percent_corr FROM $this->table we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id ".($this->publish?'WHERE '.$this->publish:'')." ORDER BY we.id DESC";
        }else{
            $sql = 'SELECT we.*, (SELECT COUNT(ea.id) FROM '.$this->attempts_table.' ea JOIN '.$this->table.' e ON ea.exercise_id=e.id WHERE correct=1) * 100 / (SELECT COUNT(ea.id) FROM '.$this->attempts_table.' ea JOIN '.$this->table.' e ON ea.exercise_id=e.id) AS percent_corr FROM ' . $this->table . ' we INNER JOIN '.$this->course_table.' wec ON we.id=wec.exercise_id WHERE '.$_where.($this->publish?' AND '.$this->publish:'').' ORDER BY we.id DESC';
        }

        return $this->db->get_results($sql, ARRAY_A);
    }


    public function get_exercises_csv(){
        $sql = "SELECT we.id, we.course_id, we.exercise_name, we.year, we.term, we.sub_chapter_id, we.relevant_video, we.duration, we.tools, we.exercise_type, we.corr_alternative, we.publish FROM $this->table we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id ORDER BY we.id DESC";
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function dup_exercise($id){
        $_data = $this->get_exercise($id);
        $data['course_id'] = $_data['course_id'];
        $data['year'] = $_data['year'];
        $data['term'] = $_data['term'];
        $data['exercise_name'] = $_data['exercise_name'];
        $data['sub_chapter_id'] = $_data['sub_chapter_id'];
        $data['relevant_video'] = $_data['relevant_video'];
        $data['duration'] = $_data['duration'];
        $data['tools'] = $_data['tools'];
        $data['exercise_type'] = $_data['exercise_type'];
        $data['corr_alternative'] = $_data['corr_alternative'];
        $data['solution_setup'] = $_data['solution_setup'];
        $data['alt_1'] = $_data['alt_1'];
        $data['alt_1_exp'] = $_data['alt_1_exp'];
        $data['alt_2'] = $_data['alt_2'];
        $data['alt_2_exp'] = $_data['alt_2_exp'];
        $data['alt_3'] = $_data['alt_3'];
        $data['alt_3_exp'] = $_data['alt_3_exp'];
        $data['publish'] = $_data['publish'];
        $data['context'] = $_data['context'];
        $data['created_at'] = time();
        return $this->save_exercise($data, 'local');
    }

    private function get_exercise_count_by_category($ex_type=null){
        $tool='';
        $exercise_type='';
        if ($ex_type) {
            switch ($ex_type) {
                case 'wtools_calc':
                    $tool='no';
                    $exercise_type='calculation';
                    break;
                case 'tools_calc':
                    $tool='yes';
                    $exercise_type='calculation';
                    break;
                case 'wtools_text':
                    $tool='no';
                    $exercise_type='text';
                    break;
                case 'tools_text':
                    $tool='yes';
                    $exercise_type='text';
                    break;
                default:
                    break;
            }
        }


        $sql = 'SELECT count(*) as count,wec.course_id FROM ' . $this->table . ' we INNER JOIN '.$this->course_table.' wec ON we.id=wec.exercise_id';
        if($tool && $exercise_type){
            $sql .= ' AND tools="'.$tool.'" AND exercise_type="'.$exercise_type.'"';
        }
        $sql .= ' group by wec.course_id';
        //echo $sql."<br />";
        return $this->db->get_results($sql, ARRAY_A);
    }

    private function get_exercise_count_by_tools($tools, $type, $course=null, $year=null, $term=null){
        $sql = "SELECT COUNT(*) AS count FROM " . $this->table . " we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id WHERE tools='" . $tools . "' and exercise_type='" . $type . "'";
        if($course){
            $sql .= " AND wec.course_id=".$course;
        }
        if($year && $year != 'All'){
            $sql .= " AND year='$year'";
        }
        if($term && $term != 'All'){
            $sql .= " AND term='$term'";
        }
        //echo $sql."<br />";
        $result = $this->db->get_results($sql, ARRAY_A);
        if (!empty($result)) {
            return $result[0]['count'];
        }
        return 0;
    }


    private function get_exercise_count_by_year($year=null, $course=null, $ex_type=null, $term=null){
        $tool='';
        $exercise_type='';
        if ($ex_type) {
            switch ($ex_type) {
                case 'wtools_calc':
                    $tool='no';
                    $exercise_type='calculation';
                    break;
                case 'tools_calc':
                    $tool='yes';
                    $exercise_type='calculation';
                    break;
                case 'wtools_text':
                    $tool='no';
                    $exercise_type='text';
                    break;
                case 'tools_text':
                    $tool='yes';
                    $exercise_type='text';
                    break;
                default:
                    break;
            }
        }
        $sql = "SELECT COUNT(*) AS count FROM " . $this->table . " we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id ";
        $where = array();
        if($tool && $exercise_type){
            $where[] = ' tools="'.$tool.'" AND exercise_type="'.$exercise_type.'"';
        }
        if($course){
            $where[] = " we.course_id=".$course;
        }
        if($term && $term != 'All'){
            $where[] = " term='".$term."'";
        }
        if($year && $year != 'All'){
            $where[] = " year='".$year."'";
        }
        if(!empty($where) && count($where) >= 2){
            $sql .= 'WHERE'.implode(' AND ', $where);
        }else if(!empty($where) && count($where) == 1){
            $sql .= 'WHERE'.implode(' ', $where);
        }
        //echo $sql."<br />";
        $result = $this->db->get_results($sql, ARRAY_A);
        if (!empty($result)) {
            return $result[0]['count'];
        }
        return 0;
    }

    public function get_listing_count($course=null, $ex_type=null, $year=null, $term=null){
        $categories = get_categories(array('parent' => 0));
        $not_require = array('teori', 'oppgavevideo', 'frontpage-box');
        $filtered_categories = array();
        foreach ($categories as $cate) {
            if (!in_array($cate->slug, $not_require)) {
                $filtered_categories[$cate->term_id] = $cate->term_id;
            }
        }
        $final_count_array = [];
        $category_count    = $this->get_exercise_count_by_category($ex_type);
        $course_sum = 0;
        $sum = 0;
        foreach ($category_count as $cat_count) {
            $final_count_array['course'][] = array(
                'name'  => $this->get_cat_slug($cat_count['course_id']),
                'id'    => $cat_count['course_id'],
                'count' => $cat_count['count']
            );
            $sum += $cat_count['count'];
            unset($filtered_categories[$cat_count['course_id']]);
        }
        if(count($filtered_categories) > 0){
            foreach ($filtered_categories as $cat) {
                $final_count_array['course'][] = array(
                    'name' => $this->get_cat_slug($cat),
                    'id'   => $cat,
                    'count' => 0
                );
            }
        }
        $final_count_array['course'][] = array(
            'name'  => 'All',
            'id'    => 0,
            'count' => $sum
        );

        usort($final_count_array['course'], function ($item1, $item2) {
            if ($item1['id'] == $item2['id']) return 0;
            return $item1['id'] > $item2['id'] ? -1 : 1;
        });

        $final_count_array['tool_yes_cal'] = $this->get_exercise_count_by_tools('yes', 'calculation', $course, $year, $term);
        $final_count_array['tool_no_cal'] = $this->get_exercise_count_by_tools('no', 'calculation', $course, $year, $term);
        $final_count_array['tool_yes_text'] = $this->get_exercise_count_by_tools('yes', 'text', $course, $year, $term);
        $final_count_array['tool_no_text'] = $this->get_exercise_count_by_tools('no', 'text', $course, $year, $term);
        $final_count_array['tool_type_all'] = $final_count_array['tool_yes_cal'] + $final_count_array['tool_no_cal'] + $final_count_array['tool_yes_text'] + $final_count_array['tool_no_text'];

        $years_total = 0;
        for($i=date('Y')-15;$i<=date('Y');$i++){
            $final_count_array[$i] = $this->get_exercise_count_by_year($i, $course, $ex_type, $term);
            $years_total += $final_count_array[$i];
        }

        /*Count for the years*/
        $final_count_array['year'] = $years_total;

        $final_count_array['spring'] = $this->get_exercise_count_by_year($year, $course, $ex_type, 'spring');
        $final_count_array['autumn'] = $this->get_exercise_count_by_year($year, $course, $ex_type, 'autumn');
        /*Count for the Terms*/
        $final_count_array['term'] = $final_count_array['spring'] + $final_count_array['autumn'];

        return $final_count_array;
    }

    function get_cat_slug($cat_id){
        $cat_id = (int) $cat_id;
        $category = &get_category($cat_id);
        return $category->slug;
    }


    public function get_where($table, $where=''){
        return $this->db->get_results("SELECT * FROM $table WHERE $where ORDER BY id DESC", ARRAY_A);
    }

    public function get_data($data){
        $where = " AND wec.course_id='".$data['course']."'";
        foreach($data['terms'] as $term){
            $where .= " AND year='".$term['year']."'";
            $_term = array();
            if($term['spring']){
                $_term[] = 'term="spring"';
            }
            if($term['autumn']){
                $_term[] = 'term="autumn"';
            }
            if(count($_term) > 0){
                $where .= " AND (".implode(' OR ', $_term).")";
            }
            $sql = "SELECT * FROM " . $this->table . " we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id WHERE 1=1".$where;
        }
        json_encode($data);
    }
    
    
    public function delete_exercises($exercises){
        foreach ($exercises as $exercise) {
            $this->db->delete($this->table, array('id'=>$exercise));
            $this->delete_exercise_courses($exercise);
            $this->delete_exercise_attempts($exercise);
        }
        return true;
    }

    public function delete_exercise_attempts($exercise_id){
        $this->db->delete($this->attempts_table, array('exercise_id'=>$exercise_id));
    }

    public function get_category_solution($__where, $course, $terms, $term_id){
        $where = " AND wec.course_id='".$course."'";
        $exercises = array();
        $_where = array();
        foreach($terms as $term) {
            if ($term['spring']) {
                $_where[$term['year']][] = 'term="spring"';
            }
            if ($term['autumn']) {
                $_where[$term['year']][] = 'term="autumn"';
            }
        }
        $___where = array();
        foreach ($_where as $key=>$value){
            $___where[] = '(year="'.$key.'" AND ('.implode(' OR ', $value).'))';
        }
        if(!empty($___where)){
            $___where = '('.implode(' OR ', $___where).')';
        }
        if(!empty($___where)){
            $___where = ' AND '.$___where;
        }
        if(!empty($__where)){
            $__where = ' AND '.$__where;
        }
        $sql = "SELECT we.id FROM " . $this->table . " we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id WHERE 1=1" . $where.$__where.$___where.($this->publish?' AND '.$this->publish:'')." AND sub_chapter_id='".$term_id."'";
        //echo $sql;exit;
        $result = $this->db->get_results($sql, ARRAY_A);
        foreach($result as $row) {
            //$total += $row['_CNT'];
            $exercises[] = $row['id'];
        }
        return $exercises;
    }


    public function get_category_exercises($__where, $course, $terms, $term_id, $order_by = 'times_completed', $order_type = 'DESC'){
        $where = " AND wec.course_id='".$course."'";
        if($order_by == 'year'){
            $order_by = 'exercise_name';
        }
        $details = array();
        foreach($terms as $term) {
            $_where = '';
            $_where .= " AND year='" . $term['year'] . "'";
            $_term = array();
            if ($term['spring']) {
                $_term[] = 'term="spring"';
            }
            if ($term['autumn']) {
                $_term[] = 'term="autumn"';
            }
            if (count($_term) > 0) {
                $_where .= " AND (" . implode(' OR ', $_term) . ")";
            }
            $user_id = get_current_user_id();
            if($term_id != 'all') {
                $sql = "SELECT we.*, (SELECT COUNT(id) FROM $this->attempts_table WHERE correct=1 AND exercise_id=we.id AND user_id=$user_id) AS times_completed, (SELECT created_at FROM $this->attempts_table WHERE correct=1 AND exercise_id=we.id AND user_id=$user_id ORDER BY id DESC LIMIT 1) AS last_answered, (SELECT (UNIX_TIMESTAMP() - created_at) / (60*60*24) FROM $this->attempts_table WHERE correct=1 AND exercise_id=we.id AND user_id=$user_id ORDER BY id DESC LIMIT 1) AS days_since_completion FROM " . $this->table . " we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id WHERE 1=1" . $where . $_where . $__where.($this->publish?' AND '.$this->publish:'') . " AND sub_chapter_id='" . $term_id . "' ORDER BY $order_by $order_type";
            }else{
                $sql = "SELECT we.*, (SELECT COUNT(id) FROM $this->attempts_table WHERE correct=1 AND exercise_id=we.id AND user_id=$user_id) AS times_completed, (SELECT created_at FROM $this->attempts_table WHERE correct=1 AND exercise_id=we.id AND user_id=$user_id ORDER BY id DESC LIMIT 1) AS last_answered, (SELECT (UNIX_TIMESTAMP() - created_at) / (60*60*24) FROM $this->attempts_table WHERE correct=1 AND exercise_id=we.id AND user_id=$user_id ORDER BY id DESC LIMIT 1) AS days_since_completion FROM " . $this->table . " we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id WHERE 1=1" . $where . $_where . $__where.($this->publish?' AND '.$this->publish:'') . " ORDER BY $order_by $order_type";
            }
            $result = $this->db->get_results($sql, ARRAY_A);
            $details = array_merge($details, $result);
        }
        return $details;
    }

    public function get_counts($course, $terms){
        $where = " AND wec.course_id='".$course."'";
        $total = 0;
        $tools = array();
        $calculation = array();
        $duration = array();
        foreach($terms as $term) {
            $_where = '';
            $_where .= " AND year='" . $term['year'] . "'";
            $_term = array();
            if ($term['spring']) {
                $_term[] = 'term="spring"';
            }
            if ($term['autumn']) {
                $_term[] = 'term="autumn"';
            }
            if (count($_term) > 0) {
                $_where .= " AND (" . implode(' OR ', $_term) . ")";
            }

            $sql = "SELECT COUNT(we.id) as _CNT, tools, exercise_type, duration FROM " . $this->table . " we INNER JOIN $this->course_table wec ON we.id=wec.exercise_id WHERE 1=1" . $where.$_where.($this->publish?' AND '.$this->publish:'')." GROUP BY tools, exercise_type, duration";
            $result = $this->db->get_results($sql, ARRAY_A);
            foreach($result as $row){
                $total += $row['_CNT'];
                $duration[$row['tools']][$row['exercise_type']][$row['duration']] += $row['_CNT'];
                $calculation[$row['tools']][$row['exercise_type']] += $row['_CNT'];
                $tools[$row['tools']] += $row['_CNT'];
            }
        }
        return array('total'=>$total, 'tools'=>$tools, 'question_type'=>$calculation, 'duration'=>$duration);
    }

    public function saveAttempt($post){
        $data['exercise_id'] = $post['exercise_id'];
        $data['course'] = $post['course'];
        $data['correct'] = $post['correct'];
        $data['alt_choose'] = $post['alt_choose'];
        $data['created_at'] = time();
        $data['user_id'] = get_current_user_id();
        $this->db->insert($this->attempts_table, $data);
    }

    public function get_last_attempt_date($exercise_id){
        $user_id = get_current_user_id();
        $sql = "SELECT created_at FROM `".$this->attempts_table."` WHERE user_id=$user_id AND exercise_id=$exercise_id AND correct=1 ORDER BY id DESC LIMIT 1";
        $row = $this->db->get_row($sql, ARRAY_A);
        if($row){
            return date('j F', $row['created_at']);
        }
        return '';
    }

    public function count_completed($exercise_id){
        $user_id = get_current_user_id();
        $sql = "SELECT COUNT(id) AS _CNT FROM `".$this->attempts_table."` WHERE user_id=$user_id AND exercise_id=$exercise_id AND correct=1 GROUP BY user_id,exercise_id ORDER BY id DESC";
        $row = $this->db->get_row($sql, ARRAY_A);
        if($row){
            return $row['_CNT'];
        }
        return 0;
    }


    public function get_exercise_userdata($exercise_id = "", $user_id = ""){
        $user = $user_id ? "AND ea.user_id=$user_id" : ""; 
        $sql = "SELECT e.exercise_name as exercise_name_detail, ea.exercise_id, ea.user_id, ea.created_at, e.corr_alternative as correct_alt, ea.alt_choose as answered_alt FROM $this->attempts_table ea JOIN $this->table e ON ea.exercise_id=e.id WHERE ea.exercise_id=$exercise_id $user ORDER BY created_at DESC";
        return $this->db->get_results($sql, ARRAY_A);
    }


    public function get_exercise_solution_date($exercise_id){
        $user_id = get_current_user_id();
        $sql = "SELECT created_at FROM $this->attempts_table WHERE correct=1 AND exercise_id=$exercise_id AND user_id=$user_id ORDER BY created_at DESC LIMIT 1";
        return $this->db->get_var($sql);
    }

    public function get_exercise_solution_percent($exercise_id){
        $user_id = get_current_user_id();
//        $sql = "SELECT (SELECT COUNT(id) FROM $this->attempts_table  WHERE correct=1 AND exercise_id=$exercise_id AND user_id<>$user_id) * 100 / (SELECT COUNT(id) FROM $this->attempts_table WHERE exercise_id=$exercise_id AND user_id<>$user_id) AS percent";
        $sql = "SELECT (SELECT COUNT(id) FROM $this->attempts_table  WHERE correct=1 AND exercise_id=$exercise_id) * 100 / (SELECT COUNT(id) FROM $this->attempts_table WHERE exercise_id=$exercise_id) AS percent";
        return $this->db->get_var($sql);
    }
    
    public function get_alt1_count($exercise_id){
        $sql = "SELECT COUNT(alt_choose) FROM `wptest_exercise_attempts` WHERE alt_choose = 'alt1' AND exercise_id=$exercise_id";
        return $this->db->get_results($sql, ARRAY_A);
    }
    public function get_alt2_count($exercise_id){
        $sql = "SELECT COUNT(alt_choose) FROM `wptest_exercise_attempts` WHERE alt_choose = 'alt2' AND exercise_id=$exercise_id";
        return $this->db->get_results($sql, ARRAY_A);
    }
    public function get_alt3_count($exercise_id){
        $sql = "SELECT COUNT(alt_choose) FROM `wptest_exercise_attempts` WHERE alt_choose = 'alt3' AND exercise_id=$exercise_id";
        return $this->db->get_results($sql, ARRAY_A);
    }

}
