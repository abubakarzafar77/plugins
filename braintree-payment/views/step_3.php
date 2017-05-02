<?php 
$user_info = $responce['data']['user_info'];
$cse_key = $responce['data']['cse_key'];
?>
<?php 
    if($responce['status'] == 'error'){
    ?>
        <div class="error">
            <?php echo $responce['message']; ?>
        </div>
    <?php 
    }
?>
<div id="payment_box">
	<div class="confirm_information">
		<h2>Payment Information</h2>
        <form method="POST" action="" id="braintree-payment-form">
            <input type="hidden" name="first_name" value="<?php echo $user_info->first_name; ?>">
            <input type="hidden" name="last_name" value="<?php echo $user_info->last_name; ?>">
            <input type="hidden" name="user_id" value="<?php echo $user_info->id; ?>">
            <input type="hidden" name="step" value="4">
            <input type="hidden" name="save" value="subscribe">
        <table>
            <tr>
                <td>Card Number</td>
                <td colspan="2"><input type="text" size="20" autocomplete="off" data-encrypted-name="number" name="number" /></td>
            </tr>
            <tr>
                <td>CVV</td>
                <td colspan="2"><input type="text" size="4" autocomplete="off" data-encrypted-name="cvv" name="cvv" /></td>
            </tr>
            <tr>
                <td>Expiration (MM / YYYY)</td>
                <td>
                    <select data-encrypted-name="month" name="month">
                        <?php 
                        $range = array(1,12);
                        for($i=$range[0]; $i<=$range[1]; $i++){ 
                        ?>
                        <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <select data-encrypted-name="year" name="year">
                        <?php
                        $max = date('Y') + 25;
                        $min = date('Y');
                        $range= array($min, $max);
                        for($i=$range[0]; $i<=$range[1]; $i++){ 
                        ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>
                     <input type="button" name="tilbake" value="Tilbake" onclick="history.go(-1)">
                </td>
                <td colspan="2">
                    <input type="submit" value="Yes, everything is right" style="width: 120px;">
                </td>
            </tr>
        </table>
		</form>	
        <script src="https://js.braintreegateway.com/v1/braintree.js"></script>
    <script>
      var braintree = Braintree.create("<?php echo $cse_key ?>");
      braintree.onSubmitEncryptForm('braintree-payment-form');
    </script>
	</div>
</div>