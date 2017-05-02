<?php
include('../../../wp-load.php');
$type = (isset($_POST['type'])) ? $_POST['type'] : "" ;
$id = (isset($_POST['id'])) ? $_POST['id'] : 0 ;
$topic_drop_down_html = "";

if($type == "main"){
    
    $html = "<select class='sub_cat1' name='sub_cat1' id='sub_cat1' >";
    $html .= "<option value=''>Hovedtema</option>";
    $childs_cat1 = get_categories('parent=' . $id);
    foreach ($childs_cat1 as $child_cat1) {
           $html .= '<option value="'.$child_cat1->term_id.'"> '.$child_cat1->name.'</option>'; 
    }
    $html .= "</select>";
    $topic_drop_down_html = $html;
}
else if($type == "sub_cat1") {
    $html = "<select class='sub_cat2' name='sub_cat2' id='sub_cat2' >";
    $html .= "<option value=''>Tema</option>";
    $childs_cat1 = get_categories('parent=' . $id);
    foreach ($childs_cat1 as $child_cat1) {
           $html .= '<option value="'.$child_cat1->term_id.'"> '.$child_cat1->name.'</option>'; 
    }
    $html .= "</select>";
    $topic_drop_down_html = $html;
}
echo trim($topic_drop_down_html);
?>
