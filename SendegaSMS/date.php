<?php

$today = date('Y-m-d H:i:s');

$nextMonth = date('Y-m-d H:i:s', strtotime("+1 month"));
$threeweeks = date('Y-m-d H:i:s', strtotime("+3 weeks"));

echo "today: $today<br/>";
echo "nextMonth: $nextMonth<br/>";
echo "three weeks: $threeweeks<br/>";


echo 'time: '.time().'</br/>';
echo 'strtotime: '.strtotime($today).'<br/>';
echo 'strtotime: '.strtotime($nextMonth).'<br/>';

?>