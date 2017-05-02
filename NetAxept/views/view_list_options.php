<div class="wrap">
	<h2>NetAxept settings</h2>
	<form id="your-profile" method="post" action="admin.php?page=netaxept">
		<input type="hidden" name="action" value="save_settings">
		<div class="parameterbox">
			<label class="standar">Merchant id: </label>
			<input type="text" class="regular-text" name="netaxept_merchantid" value="<?php echo $values['merchantid']; ?>" />
		</div>
		<div class="parameterbox">
			<label class="standar">Subscription price</label>
			<input type="text" class="regular-text" name="netaxept_subscription_price" value="<?php echo $values['subscription_price']; ?>" />
		</div>
		<div class="parameterbox">
			<label class="standar">Recurring</label>
			<input type="checkbox" class="regular-text" name="netaxept_recurring" value="true" <?php echo $recurring_true; ?>/>
		</div>
		<div class="parameterbox">
			<label class="standar">Service type</label>
			<input type="text" class="regular-text" name="netaxept_servicetype" value="<?php echo $values['service_type']; ?>"/>
		</div>
				
		<h3>Utvikling</h3>
		<div class="parameterbox">
			<label class="standar">Token: </label>
			<input type="password" class="regular-text" name="netaxept_token_dev" value="<?php echo $values['dev_token'];?>" />
		</div>
		<div class="parameterbox">
			<label class="standar">Wsdl: </label>
			<input type="text" class="regular-text" name="netaxept_wsdl_dev" value="<?php echo $values['dev_wsdl']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Redirection from NetAxept: <?php echo $siteUrl; ?>/</label>
			<input type="text" class="regular-text" name="netaxept_redirection_dev" value="<?php echo $values['dev_redirection']; ?>">
		</div>
		<div class="parameterbox">
			<label>Redirection url: <?php echo $siteUrl; ?>/</label>
			<input type="text" class="regular-text" name="netaxept_redirection_url_dev" value="<?php echo $values['dev_redirection_url']; ?>">
		</div>
		<div class="parameterbox">
			<label>Redirect on error url: <?php echo $siteUrl; ?>/</label>
			<input type="text" class="regular-text" name="netaxept_redirect_on_error_dev" value="<?php echo $values['dev_redirect_on_error']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Netaxept Terminal location: </label>
			<input type="text" class="regular-text" name="netaxept_terminal_dev" value="<?php echo $values['dev_terminal']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Netaxept Terminal mobil location: </label>
			<input type="text" class="regular-text" name="netaxept_terminal_dev_mobile" value="<?php echo $values['dev_terminal_mobile']; ?>">
		</div>
		
		
		<h3>Produksjon</h3>
		<div class="parameterbox">
			<label class="standar">Token: </label>
			<input type="password" class="regular-text" name="netaxept_token_prod" value="<?php echo $values['prod_token']; ?>">
		</div>
		<div class="parameterbox">
			<label>Wsdl: </label><input type="text" class="regular-text" name="netaxept_wsdl_prod" value="<?php echo $values['prod_wsdl']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Redirection from NetAxept: </label>
			<input type="text" class="regular-text" name="netaxept_redirection_prod" value="<?php echo $values['prod_redirection']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Redirection url: </label>
			<input type="text" class="regular-text" name="netaxept_redirection_url_prod" value="<?php echo $values['prod_redirection_url']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Redirect on error: </label>
			<input type="text" class="regular-text" name="netaxept_redirect_on_error_prod" value="<?php echo $values['prod_redirect_on_error']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Netaxept Terminal location: </label>
			<input type="text" class="regular-text" name="netaxept_terminal_prod" value="<?php echo $values['prod_terminal']; ?>">
		</div>
		<div class="parameterbox">
			<label class="standar">Netaxept Terminal mobile location: </label>
			<input type="text" class="regular-text" name="netaxept_terminal_prod_mobile" value="<?php echo $values['prod_terminal_mobile']; ?>">
		</div>
		
		<div class="parameterbox">
			<label class="standar">Go: </label>
			<select name="netaxept_go">
				<option value="dev" <?php echo $dev_selected; ?>>Development</option>
				<option value="prod" <?php echo $prod_selected; ?>>Produktion</option>
			</select>
		</div>
		<input id="submit" class="button-primary" type="submit" value="Oppdater instillinger" name="submit">
	</form>
</div>