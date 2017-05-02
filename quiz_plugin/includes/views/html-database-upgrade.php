<div class="update-nag">
	<p><?php _e( 'Press the button below to migrate your surveys to the newest version', 'quiz-plugin' ); ?></p>
	<p>
		<form method="post" action="<?php echo admin_url( 'edit.php?post_type=quiz-plugin&noheader=true' ); ?>">
			<input class="button-primary" name="wwm_do_db_upgrade" type="submit" value="<?php _e( 'upgrade database', 'quiz-plugin' ) ?>">
			<?php wp_nonce_field( 'wwm-as-database-upgrade', 'wwm_as_db_upgrade', false, true ); ?>
		</form>
	</p>
</div>