<?php
	$options = get_option( 'wwm_awesome_surveys_options', array() );
	$include = ( isset( $options['general_options'] ) && isset( $options['general_options']['include_css'] ) ) ? absint( $options['general_options']['include_css'] ) : 1;
	?>
<h4>
	<?php __( 'Survey Styling Options', 'quiz-plugin' ); ?></h4>
<p>
	<?php _e( 'This plugin outputs some very basic structural css. You can enable/disable this by setting the option below', 'quiz-plugin' ); ?></p>
<div id="general-surveys-options">
	<fieldset style="border-radius: 7px;">
		<div class="overlay">
			<span class="preloader"></span>
		</div>
		<div class="control-group">
			<label class="control-label" for="styling-options-element-1">
				<?php _e( 'Use included css?', 'quiz-plugin' ); ?></label>
			<div class="controls">
				<label class="radio inline">
					<input id="styling-options-element-1-0" name="options[general_options][include_css]" value="1" <?php checked( 1 == $include ); ?> type="radio"><?php _e( 'Yes', 'quiz-plugin' ); ?></label>
				<label class="radio inline">
					<input id="styling-options-element-1-1" name="options[general_options][include_css]" value="0" <?php checked( 0 == $include ); ?> type="radio"><?php _e( 'No', 'quiz-plugin' ); ?></label>
			</div>
		</div>
		<input name="action" value="update_styling_options" id="styling-options-element-2" type="hidden">
		<?php wp_nonce_field( 'awesome-surveys-update-options', '_nonce', false, true ); ?>
		<div class="form-actions">
			<p>
				<input value="<?php _e( 'Save', 'quiz-plugin' ); ?>
				" name="" class="button-primary btn btn-primary" id="styling-options-element-4" type="submit">
			</p>
		</div>
	</fieldset>
</div>