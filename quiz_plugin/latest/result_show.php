<?php
/**
 * Template Name: Result Page
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();
global $post;
//exit;
?>
<div id="container" class="default container">

    <div id="content" class="custom_margin" role="standar">
        <?php
        echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
        $post_id               = isset($_GET['post_id']) ? $_GET['post_id'] : '';
//        echo $_SESSION['response_user'] . ":::::tademi69932074305914";
        $results               = get_post_meta($post_id, '_response', false);
        $logged_in_questionare = get_post_meta($post_id, '_respondents', false);
        $post_result_title     = get_the_title($post_id);
        $response_counts       = array();
        $counter               = 1;
        $totalArray            = array();
        $response_per_question = array();
        $answer_book           = array();
        $user_id               = get_current_user_id();
        $user_display_name     = '';
        $response_per_user     = '';
        if ($logged_in_questionare)
        {
            foreach ($results as $key => $result)
            {
                if (is_array($result))
                {
                    foreach ($result as $answer_key => $count)
                    {
                        if ($user_id == 'all' || $user_id == $answer_key)
                        {
                            if (is_array($count))
                            {
                                $user_info         = get_userdata($answer_key);
                                $user_display_name = $user_info->data->display_name;
                                //                echo '<div class="postbox" id="respondent-' . $counter . '"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle ui-sortable-handle"><span>Results for respondent ' . $counter . '</span></h3>';
//                            echo '<br/>User Name : <a href="' . get_edit_post_link($post->ID) . '&view=results&user_id=' . $answer_key . '">' . $user_display_name . '</a><br/><br/>';
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
            $user_display_name = $_SESSION['response_user'];
            $results           = get_post_meta($post_id, $user_display_name, false);
            $result_filter     = isset($results[0]) ? $results[0] : array();
            foreach ($result_filter as $keyk => $valv)
            {
                foreach ($valv as $k => $v)
                {
                    $ans = '';
                    if ($v == 0)
                    {
                        $totalArray['v'] = isset($totalArray['v']) ? ($totalArray['v'] + 1) : 1;
                        $ans             = 'v';
                    }
                    else if ($v == 1)
                    {
                        $totalArray['k'] = isset($totalArray['k']) ? ($totalArray['k'] + 1) : 1;
                        $ans             = 'k';
                    }
                    else if ($v == 2)
                    {
                        $totalArray['a'] = isset($totalArray['a']) ? ($totalArray['a'] + 1) : 1;
                        $ans             = 'a';
                    }
                }
                break;
            }
        }
        ?>
        <div class="clear"></div>

        <h4 style="line-height: 1.2em;margin: 0 0 20px 0;background-color: #A5A5A5;color: white;padding: 21px;font-family: 'roboto_slabregular';font-weight: normal;border-radius: 7px;"><?php echo "Successfully submitted your answers for questionare : " . ucfirst($post_result_title); ?></h4>
        <h3 style="text-align: center;margin-top: 73px;margin-bottom:0px;background: #ffffff;font-size: 18px;padding-right: 200px;margin-bottom: 10px;text-align:center;"><?php echo ucfirst($user_display_name); ?></h3>
        <div id="piechart_3d" style="width: 100%; height: 500px;"></div>



        <script type = "text/javascript">
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
//                    title: 'Quick Result',
                    slices: {1: {offset: 0.11},
                        2: {offset: 0.13},
                        3: {offset: 0.15},
                    },
                };

                var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));

                chart.draw(data, options);
            }
        </script>


    </div><!-- #container -->

</div>
<?php
get_sidebar();

get_footer();
?>