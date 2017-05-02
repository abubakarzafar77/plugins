<?php
/*
  todo:
  this belongs in a function - not here
  make the strings translatable the right way
 */

echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">';
echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
echo '<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>';
echo '<script type="text/javascript" src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>';
global $post;
$results               = get_post_meta($post->ID, '_response', false);
$logged_in_questionare = get_post_meta($post->ID, '_respondents', false);
$elements              = json_decode(get_post_meta($post->ID, 'existing_elements', true), true);
$response_counts       = array();
echo '<p>';
printf(__('This questionare has received a total of %s%d%s responses', 'quiz-plugin'), '<b>', count($results), '</b>');
echo '</p>';
$counter               = 1;
$totalArray            = array();
$response_per_question = array();
$answer_book           = array();
$user_name             = $user_id               = 'all';
$user_display_name2    = 'All';

if ($logged_in_questionare)
{
    if (isset($_GET['user_id']))
    {
        $user_id            = $_GET['user_id'];
        $user_first         = get_user_meta($user_id, 'first_name', true);
        $user_last          = get_user_meta($user_id, 'last_name', true);
        $user_display_name2 = $user_first . " " . $user_last;
    }
    echo '<a style="margin-bottom:50px;" href="' . get_edit_post_link($post->ID) . '&view=results">All</a><br/>';
    echo '
<table id="example" class="display wp-list-table widefat fixed posts" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="header" style="text-align:center;">ID</th>
                <th class="header" style="text-align:center;">User Name</th>
                <th class="header" style="text-align:center;">Email</th>
            </tr>
        </thead>
        <tbody>';
    foreach ($results as $key => $result)
    {
        if (is_array($result))
        {
            foreach ($result as $answer_key => $count)
            {
                $user_info         = get_userdata($answer_key);
                $user_first        = get_user_meta($answer_key, 'first_name', true);
                $user_last         = get_user_meta($answer_key, 'last_name', true);
                $user_display_name = $user_first . " " . $user_last;
                $user_email        = $user_info->data->user_email;
                $user_ID           = $user_info->data->ID;
//                echo '<div class="postbox" id="respondent-' . $counter . '"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle ui-sortable-handle"><span>Results for respondent ' . $counter . '</span></h3>';
                $class             = ($user_id == $answer_key) ? ' background:lightblue;' : '';
                echo '<tr>
                                <td style="text-align:center; ' . $class . '"><a href="http://' . $_SERVER['SERVER_NAME'] . '/wp-admin/user-edit.php?user_id=' . $answer_key . '&wp_http_referer=%2Fwp-admin%2Fusers.php' . $answer_key . '">' . $user_ID . '</a></td>';
                echo '<td style="text-align:center;' . $class . '"><a href="' . get_edit_post_link($post->ID) . '&view=results&user_id=' . $answer_key . '">' . $user_display_name . '</a></td>';
                echo "<td style='text-align:center; $class'>$user_email</td>
                              </tr>";
                if ($user_id == 'all' || $user_id == $answer_key)
                {
                    if (is_array($count))
                    {

//                        echo '<br/>User Name : <a href="' . get_edit_post_link($post->ID) . '&view=results&user_id=' . $answer_key . '">' . $user_display_name . '</a><br/><br/>';
                        foreach ($count as $k => $v)
                        {
                            $ans = '';
                            if ($v == 0)
                            {
                                $totalArray['v']                     = isset($totalArray['v']) ? ($totalArray['v'] + 1) : 1;
                                $response_per_user[$answer_key]['v'] = isset($response_per_user[$answer_key]['v']) ? ($response_per_user[$answer_key]['v'] + 1) : 1;
                                $ans                                 = 'v';
                            }
                            else if ($v == 1)
                            {
                                $totalArray['k']                     = isset($totalArray['k']) ? ($totalArray['k'] + 1) : 1;
                                $response_per_user[$answer_key]['k'] = isset($response_per_user[$answer_key]['k']) ? ($response_per_user[$answer_key]['k'] + 1) : 1;
                                $ans                                 = 'k';
                            }
                            else if ($v == 2)
                            {
                                $totalArray['a']                     = isset($totalArray['a']) ? ($totalArray['a'] + 1) : 1;
                                $response_per_user[$answer_key]['a'] = isset($response_per_user[$answer_key]['a']) ? ($response_per_user[$answer_key]['a'] + 1) : 1;
                                $ans                                 = 'a';
                            }
                            $answer_book[$k][$ans] = isset($answer_book[$k][$ans]) ? $answer_book[$k][$ans] + 1 : 1;
                        }
                        $counter++;
                        $response_per_user[$answer_key]['name'] = isset($elements[$answer_key]['name']) ? $elements[$answer_key]['name'] : ' ';
                    }
                }
            }
        }
    }
}
else
{
    $results = get_post_meta($post->ID);
    if (isset($_GET['user_id']))
    {
        $user_id            = $_GET['user_id'];
        $user_display_name2 = $user_id;
        if (strpos($user_display_name2, 'QQ_user_login_') !== false)
        {
            $user_arr            = explode('_', $user_display_name2);
            $user_id_login_info  = $user_arr[count($user_arr) - 1];
//            $user_info_for_login = get_userdata($user_id_login_info);
            $user_first          = get_user_meta($user_id_login_info, 'first_name', true);
            $user_last           = get_user_meta($user_id_login_info, 'last_name', true);
            $user_display_name2  = $user_first . " " . $user_last;
        }
    }
    echo '<a style="margin-bottom:50px;" href="' . get_edit_post_link($post->ID) . '&view=results">All</a><br/>';
    echo '
<table id="example" class="display wp-list-table widefat fixed posts" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="header" style="text-align:center;">ID</th>
                <th class="header" style="text-align:center;">User Name</th>
                <th class="header" style="text-align:center;">Email</th>
            </tr>
        </thead>
        <tbody>';



    foreach ($results as $answer_key => $count)
    {
        $login_user = 0;
        if (strpos($answer_key, "Anonymous_") !== false || strpos($answer_key, 'QQ_user_login_') !== false)
        {
            if (strpos($answer_key, 'QQ_user_login_') !== false)
            {
                $user_arr          = explode('_', $answer_key);
                $user_id_login     = $user_arr[count($user_arr) - 1];
                $user_info2        = get_userdata($user_id_login);
                $user_display_name_table = $user_info2->data->display_name;
                $login_user        = 1;
            }
            else
            {
                $user_display_name_table = $answer_key;
            }
            $count = get_post_meta($post->ID, $answer_key, false);
            $count = isset($count[0]) ? $count[0] : array();
            foreach ($count as $k_ans => $val_ans)
            {
                $count = $val_ans;
            }

//            $user_ID           = $answer_key;
            $class = ($user_id == $answer_key) ? ' background:lightblue;' : '';
            echo '<tr>';
            if ($login_user)
            {
                $user_info          = get_userdata($user_id_login);
                $user_first         = get_user_meta($user_id_login, 'first_name', true);
                $user_last          = get_user_meta($user_id_login, 'last_name', true);
                $user_display_name_table = $user_first . " " . $user_last;
                $user_email         = $user_info->data->user_email;
                echo '<td style="text-align:center; ' . $class . '"><a href="http://' . $_SERVER['SERVER_NAME'] . '/wp-admin/user-edit.php?user_id=' . $user_id_login . '&wp_http_referer=%2Fwp-admin%2Fusers.php' . $user_id_login . '">' . $user_id_login . '</a></td>';
            }
            else
            {
                echo '<td style="text-align:center;' . $class . '">---</td>';
            }
            echo '<td style="text-align:center;' . $class . '"><a href="' . get_edit_post_link($post->ID) . '&view=results&user_id=' . $answer_key . '">' . $user_display_name_table . '</a></td>';
            if ($login_user)
            {
                echo '<td style="text-align:center;' . $class . '">'.$user_email.'</td>';
            }
            else
            {
                echo '<td style="text-align:center;' . $class . '">---</td>';
            }
            echo "</tr>";
            if ($user_id == 'all' || $user_id == $answer_key)
            {
                if (is_array($count))
                {
//                        echo '<br/>User Name : <a href="' . get_edit_post_link($post->ID) . '&view=results&user_id=' . $answer_key . '">' . $user_display_name . '</a><br/><br/>';
                    foreach ($count as $k => $v)
                    {
                        $ans = '';
                        if ($v == 0)
                        {
                            $totalArray['v']                     = isset($totalArray['v']) ? ($totalArray['v'] + 1) : 1;
                            $response_per_user[$answer_key]['v'] = isset($response_per_user[$answer_key]['v']) ? ($response_per_user[$answer_key]['v'] + 1) : 1;
                            $ans                                 = 'v';
                        }
                        else if ($v == 1)
                        {
                            $totalArray['k']                     = isset($totalArray['k']) ? ($totalArray['k'] + 1) : 1;
                            $response_per_user[$answer_key]['k'] = isset($response_per_user[$answer_key]['k']) ? ($response_per_user[$answer_key]['k'] + 1) : 1;
                            $ans                                 = 'k';
                        }
                        else if ($v == 2)
                        {
                            $totalArray['a']                     = isset($totalArray['a']) ? ($totalArray['a'] + 1) : 1;
                            $response_per_user[$answer_key]['a'] = isset($response_per_user[$answer_key]['a']) ? ($response_per_user[$answer_key]['a'] + 1) : 1;
                            $ans                                 = 'a';
                        }
                        $answer_book[$k][$ans] = isset($answer_book[$k][$ans]) ? $answer_book[$k][$ans] + 1 : 1;
                    }
                    $counter++;
                    $response_per_user[$answer_key]['name'] = isset($elements[$answer_key]['name']) ? $elements[$answer_key]['name'] : ' ';
                }
            }
        }
    }
}
?>

</tbody>
</table>
<div class="clear"></div>
<h4 style="text-align: center;background: #ffffff;padding: 26px;font-size: 18px;margin-bottom: -34px;padding-right: 319px;text-align:center;"><?php echo ucfirst($user_display_name2); ?></h4>
<div id="piechart_3d" style="width: 100%; height: 500px; margin-top:20px;"></div>



<script type = "text/javascript">
    $(document).ready(function() {
        $('#example').DataTable();
    });
    $("#wpseo_meta").remove();
    google.load("visualization", "1", {packages: ["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Questions quick result'],
            ['K', <?php echo $totalArray['k'] != '' ? $totalArray['k'] : 0; ?>],
            ['A', <?php echo $totalArray['a'] != '' ? $totalArray['a'] : 0; ?>],
            ['V', <?php echo $totalArray['v'] != '' ? $totalArray['v'] : 0; ?>],
        ]);
        var options = {
            //            title: '<?php // echo ucfirst($user_name);                        ?>',
//            is3D: true,
//            pieStartAngle: 10,
            slices: {1: {offset: 0.11},
                2: {offset: 0.13},
                3: {offset: 0.15},
            },
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
    }
</script>
<style>
    #example_wrapper{
        margin-top:37px;
    }
</style>

