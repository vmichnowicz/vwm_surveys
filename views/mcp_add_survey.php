<h3>Add a Survey</h3>

<?php echo form_open($action_url) ?>
	<fieldset>
		<label for="vwm_surveys_survey_title">Title:</label>
		<input type="text" name="title" id="vwm_surveys_survey_title" />
		<input type="submit" class="submit" name="submit" value="Submit" />
	</fieldset>
<?php echo form_close(); ?>