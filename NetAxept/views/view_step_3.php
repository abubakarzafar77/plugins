<?php

$fileName = explode( ".",  basename(__FILE__) );
$currentFileData = "data_".$fileName[0];
$currentViewData = $$currentFileData;

?>
<div id="payment_box">
	<div class="confirm_payment">
		<?php echo $currentViewData['body']?>
	</div>
</div>