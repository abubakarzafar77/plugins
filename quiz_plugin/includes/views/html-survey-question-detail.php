<?php

global $post;
global $awesome_surveys;
$auth_method = get_post_meta($post->ID, 'survey_auth_method', true);
if (empty($auth_method))
{
    $auth_method = 0;
}
$auth_type  = $awesome_surveys->auth_methods[$auth_method]['name'];
global $post;
$post_id    = $post->ID;
$meta_key   = 'quiz_details_' . $post_id;
//$meta_exist = get_post_meta($post_id, $meta_key);
//$desc       = isset($meta_exist[0]) ? $meta_exist[0] : '';
//echo '<div style="width:100%; height:450px;">';
////echo '<h1>Hwllo here</h1>';
//wp_editor($desc != '' ? $desc : "Enter quiz description here", "quiz_details");
//echo '</div>';
$meta_exist = get_post_meta($post_id);
$desc       = "";
if ($meta_exist)
{
    foreach ($meta_exist as $key => $val)
    {
        $pos = strpos($key, "quiz_details_");
        if ($pos !== false)
        {
            $desc = isset($val[0]) ? $val[0] : '';
        }
    }
}
echo '<div style="width:100%; height:450px;">';
//echo '<h1>Hwllo here</h1>';
//wp_editor($desc != '' ? $desc : "Enter quiz description here", "quiz_details");
//the_editor($desc != '' ? $desc : "Enter quiz description here", "quiz_details");
wp_editor($desc != '' ? $desc : "Enter quiz description here", "quiz_details", array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 360) );
echo '</div>';
?>