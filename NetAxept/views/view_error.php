<?php

$fileName = explode( ".",  basename(__FILE__) );
$currentFileData = "data_".$fileName[0];
$currentViewData = $$currentFileData;

?>
<div class="error">
	<h2><?php echo $currentViewData['heading'] ?></h2>
	<p><?php echo $currentViewData['text'] ?></p>
</div>