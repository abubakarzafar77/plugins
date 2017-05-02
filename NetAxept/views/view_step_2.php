<?php

$fileName = explode( ".",  basename(__FILE__) );
$currentFileData = "data_".$fileName[0];
$currentViewData = $$currentFileData;

?>

<div id="payment_box">
	<div class="confirm_information">
		<h2>Har vi lagret rett informasjon?</h2>
		<dl>
			<dt>Navn:</dt>
			<dd><?php echo $mattevideo_customer->FirstName.' '.$mattevideo_customer->LastName ?></dd>
			<dt>Mobilnummer:</dt>
			<dd><?php echo $mattevideo_customer->PhoneNumber ?></dd>
			<dt>E-post:</dt>
			<dd><?php echo $mattevideo_customer->Email ?></dd>
			<dt>Pris:</dt>
			<dd><?php echo $currentViewData['amount'] ?>,-</dd>
		</dl>
	
		<p>
		<?php echo $currentViewData['pre_text'] ?>
		</p>
		<?php /*?><?php echo $currentViewData['terminal'].'?merchantId='.$currentViewData['merchantId'].'&transactionId='.$currentViewData['transactionId'] ?><?php */?>
		<form method="POST" action="">
        	<input type="hidden" name="todo" value="payment_redirect" />
			<input type="hidden" name="merchantId" value="<?php echo $currentViewData['merchantId'];?>" />
			<input type="hidden" name="transactionId" value="<?php echo $currentViewData['transactionId'];?>" />
            <input type="hidden" name="terminal" value="<?php echo $currentViewData['terminal']; ?>" />
            <input type="hidden" name="user_id" value="<?php echo $currentViewData['user_id']; ?>" />
			<input type="button" name="tilbake" value="Tilbake" onclick="history.go(-1)" />
			<input type="submit" value="<?php echo $currentViewData['submit_text'] ?>" style="width: 120px;" />
		</form>	
	</div>
</div>
