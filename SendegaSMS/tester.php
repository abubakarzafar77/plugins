<?php


echo 'yo<br/>';

$date = now();

echo 'date: '.$date.'<br/>';

$timestamp = now('timestamp');

echo 'timestamp: '.$timestamp.'<br/>';


function now($format = 'datetime'){
		
			if($format == 'timestamp'){
				return time() + 7200;
				
			}else if($format = 'datetime'){
			
				$timestampNow = time() + 7200;
				return date('Y-m-d H:i:s', $timestampNow );
			}
		
		}
?>