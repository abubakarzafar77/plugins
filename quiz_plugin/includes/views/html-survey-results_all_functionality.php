<?php
/*
  todo:
  this belongs in a function - not here
  make the strings translatable the right way
 */
echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
global $post;
$results         = get_post_meta($post->ID, '_response', false);
$elements        = json_decode(get_post_meta($post->ID, 'existing_elements', true), true);
$response_counts = array();
echo '<p>';
printf(__('This questionare has received a total of %s%d%s responses', 'quiz-plugin'), '<b>', count($results), '</b>');
echo '</p>';
$counter = 1;
$totalArray            = array();
$response_per_question = array();
$answer_book           = array();
foreach ($results as $key => $result)
{
    if (is_array($result))
    {
        foreach ($result as $answer_key => $count)
        {
            if (is_array($count))
            {
                echo '<div class="postbox" id="respondent-' . $counter . '"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle ui-sortable-handle"><span>Results for respondent ' . $counter . '</span></h3>';
//                echo '<div class="inside">';
                foreach ($count as $k => $v)
                {
                    $ans = '';
                    if ($v == 0)
                    {
                        $totalArray['v']                         = isset($totalArray['v']) ? ($totalArray['v'] + 1) : 1;
                        $response_per_question[$answer_key]['v'] = isset($response_per_question[$answer_key]['v']) ? ($response_per_question[$answer_key]['v'] + 1) : 1;
                        $ans                                     = 'v';
                    }
                    else if ($v == 1)
                    {
                        $totalArray['k']                         = isset($totalArray['k']) ? ($totalArray['k'] + 1) : 1;
                        $response_per_question[$answer_key]['k'] = isset($response_per_question[$answer_key]['k']) ? ($response_per_question[$answer_key]['k'] + 1) : 1;
                        $ans                                     = 'k';
                    }
                    else if ($v == 2)
                    {
                        $totalArray['a']                         = isset($totalArray['a']) ? ($totalArray['a'] + 1) : 1;
                        $response_per_question[$answer_key]['a'] = isset($response_per_question[$answer_key]['a']) ? ($response_per_question[$answer_key]['a'] + 1) : 1;
                        $ans                                     = 'a';
                    }
                    $answer_book[$k][$ans] = isset($answer_book[$k][$ans]) ? $answer_book[$k][$ans] + 1 : 1;
//                    echo '<p><span class="italics">Question: ' . $elements[$k]['name'] . '</span><br><span class="answer">Answer: ' . $ans . '</span></p>';
                }
                $counter++;
                $response_per_question[$answer_key]['name'] = isset($elements[$answer_key]['name']) ? $elements[$answer_key]['name'] : ' ';
//                echo '</div>';
                echo "</div>";
            }
        }
    }
}





//?? PIE CHART END
//
//echo "<pre>";
//
//print_r($answer_book);
//print_r($results);
//print_r($elements);
//print_r($response_per_question);
//exit;
//
//foreach ($results as $response_key => $response_array)
//{
//    foreach ($response_array as $key => $array)
//    {
//        foreach ($array as $question_key => $responses)
//        {
//            $response_counts[$question_key]
//            original
//            $response_counts[$question_key]['totalcount'] = ( isset($response_counts[$question_key]['totalcount']) ) ? ( $response_counts[$question_key]['totalcount'] + 1 ) : 1;
//            edited
//            $response_counts[$question_key]['totalcount'] = ( isset($response_counts[$question_key]['totalcount']) ) ? ( $response_counts[$question_key]['totalcount'] + 1 ) : 1;
//            if (isset($elements[$question_key]['value']))
//            {
//                if (is_array($responses) && !empty($responses))
//                {
//                    foreach ($responses as $response)
//                    {
//                        $response_counts[$question_key]['count'][$elements[$question_key]['value'][$response]] = ( isset($response_counts[$question_key]['count'][$elements[$question_key]['value'][$response]]) ) ? $response_counts[$question_key]['count'][$elements[$question_key]['value'][$response]] + 1 : 1;
//                    }
//                }
//                elseif (isset($responses))
//                {
//                    $response_counts[$question_key]['count'][$elements[$question_key]['value'][$responses]] = ( isset($response_counts[$question_key]['count'][$elements[$question_key]['value'][$responses]]) ) ? $response_counts[$question_key]['count'][$elements[$question_key]['value'][$responses]] + 1 : 1;
//                }
//            }
//        }
//    }
//}
//echo "<pre>";
//print_r($response_counts);
//exit;
//$total = count($response_per_question);

//foreach ($answer_book as $question => $ans)
//{
//    echo '<div class="answers">';
//    echo '<p class="answers">';
//    printf(__('%s received %s%d%s responses', 'quiz-plugin'), $elements[$question]['name'], '<b>', $total, '</b>');
//    echo '</p>';
//// for k
//
//    $percentage = isset($ans['k']) ? ($ans['k'] / $total) : 0;
//    $total_q    = isset($ans['k']) ? ($ans['k']) : 0;
//    $percentage = $percentage * 100;
//    if (100 == intval($percentage))
//    {
//        echo '<div class="options-container">'
//        . '<div class="options" style="width:' . $percentage . '%;">'
//        .'K(<b>' . $total_q . '</b>)'
//        . '<p class="percentage">' . $percentage . '% (' . $total_q . '/' . $total . ')</span>'
//        . '</div>'
//        . '<p>&nbsp;</p>'
//        . '</div>';
//    }
//    else
//    {
//        echo '<div class="options-container">'
//        . '<span class="options" style="width:' . $percentage . '%;">'
//        . 'K(<b>' . $total_q . '</b>)'
//        . '</span>'
//        . '<p class="percentage">' . $percentage . '% (' . $total_q . '/' . $total . ')</p>'
//        . '</div>';
//    }
//    // a
//    $percentage = isset($ans['a']) ? ($ans['a'] / $total) : 0;
//    $total_q    = isset($ans['a']) ? ($ans['a']) : 0;
//    $percentage = $percentage * 100;
//    if (100 == intval($percentage))
//    {
//        echo '<div class="options-container">'
//        . '<div class="options" style="width:' . $percentage . '%;">'
//        . 'A(<b>' . $total_q . '</b>)'
//        . '<p class="percentage">' . $percentage . '% (' . $total_q . '/' . $total . ')</span>'
//        . '</div>'
//        . '<p>&nbsp;</p>'
//        . '</div>';
//    }
//    else
//    {
//        echo '<div class="options-container">'
//        . '<span class="options" style="width:' . $percentage . '%;">'
//        . 'A(<b>' . $total_q . '</b>)'
//        . '</span>'
//        . '<p class="percentage">' . $percentage . '% (' . $total_q . '/' . $total . ')</p>'
//        . '</div>';
//    }
//
//    // v
//
//    $percentage = isset($ans['v']) ? ($ans['v'] / $total) : 0;
//    $total_q    = isset($ans['v']) ? ($ans['v']) : 0;
//    $percentage = $percentage * 100;
//    if (100 == intval($percentage))
//    {
//        echo '<div class="options-container">'
//        . '<div class="options" style="width:' . $percentage . '%;">'
//        . 'V(<b>' . $total_q . '</b>)'
//        . '<p class="percentage">' . $percentage . '% (' . $total_q . '/' . $total . ')</span>'
//        . '</div>'
//        . '<p>&nbsp;</p>'
//        . '</div>';
//    }
//    else
//    {
//        echo '<div class="options-container">'
//        . '<span class="options" style="width:' . $percentage . '%;">'
//        . 'V(<b>' . $total_q . '</b>)'
//        . '</span>'
//        . '<p class="percentage">' . $percentage . '% (' . $total_q . '/' . $total . ')</p>'
//        . '</div>';
//    }
//    echo '</div>';
//}
echo '</div>';








//foreach ($response_per_question as $question_key => $value)
//{
//    echo '<div class="answers">';
//    if (isset($elements[$question_key]['value']))
//    {
//        echo '<p class="answers">';
//        printf(__('%s received %s%d%s responses', 'quiz-plugin'), $elements[$question_key]['name'], '<b>', $value['totalcount'], '</b>');
//        echo '</p>';
//        foreach ($value['count'] as $answer_key => $count)
//        {
////            echo $answer_key;
////            if ($answer_key == 0)
////            {
////                $totalArray['v'] +=$count;
////            }
////            else if ($answer_key == 1)
////            {
////                $totalArray['k'] +=$count;
////            }
////            else if ($answer_key == 2)
////            {
////                $totalArray['a'] +=$count;
////            }
//            // Commented by qudrat
//            $percentage  = number_format(( $count / $value['totalcount'] ) * 100, 2);
//            $total_count = $value['totalcount'];
//
//            if (100 == intval($percentage))
//            {
//                echo '<div class="options-container">'
//                . '<div class="options" style="width:' . $percentage . '%;">'
//                . $elements[$question_key]['label'][$answer_key] . ' (<b>' . $count . '</b>)'
//                . '<p class="percentage">' . $percentage . '% (' . $count . '/' . $total_count . ')</span>'
//                . '</div>'
//                . '<p>&nbsp;</p>'
//                . '</div>';
//            }
//            else
//            {
//                echo '<div class="options-container">'
//                . '<span class="options" style="width:' . $percentage . '%;">'
//                . $elements[$question_key]['label'][$answer_key] . ' (<b>' . $count . '</b>)'
//                . '</span>'
//                . '<p class="percentage">' . $percentage . '% (' . $count . '/' . $total_count . ')</p>'
//                . '</div>';
//            }
//        }
//    }
//    else
//    {
//        //future todo echo '<a href="#" data-question-key="' . $question_key . '">';
//        echo '<p class="totalcount survey">';
//        echo $elements[$question_key]['name'] . ' ';
//        printf(__('was answered %d times', 'quiz-plugin'), $value['totalcount']);
//        echo '</p>';
//        //future todo echo '</a>';
//    }
//
//
//
//    echo '<div class="clear"></div></div>';
//}
//                        exit;
//echo '<div class="clear"></div></div>';
?>

<div id="piechart_3d" style="width: 900px; height: 500px;"></div>
</div>
</div>

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
            title: 'Quick Result'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));

        chart.draw(data, options);
    }
</script>

