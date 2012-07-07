<h3>Add a Survey</h3>

<?php echo form_open($action_url) ?>
	<fieldset>
		<legend>Survey Information</legend>
		<label for="vwm_surveys_survey_title">Title:</label>
		<input type="text" name="title" id="vwm_surveys_survey_title" />
	</fieldset>
	<fieldset>
		<legend>Submit</legend>
		<input type="submit" class="submit" name="submit" value="Submit" />
	</fieldset>
<?php echo form_close(); ?>