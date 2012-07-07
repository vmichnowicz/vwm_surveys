<h3>Add a Survey</h3>

<?php echo form_open($action_url) ?>
	<fieldset>
		<legend>Survey Information</legend>

		<div>
			<label for="vwm_surveys_survey_title">Title:</label>
			<input type="text" name="title" id="vwm_surveys_survey_title" />
		</div>

		<?php if ( isset($surveys) AND is_array($surveys) AND count($surveys) > 0): ?>
			<div>
				<label for="vwm_surveys_survey_clone_id">Clone existing survey (pages &amp; questions):</label>
				<select name="clone_id" id="vwm_surveys_survey_clone_id">
					<option value="">&mdash; None &mdash;</option>
					<?php foreach ($surveys as $survey): ?>
						<option value="<?php echo $survey['id']; ?>"><?php echo $survey['title']; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

	<fieldset>
		<legend>Submit</legend>
		<input type="submit" class="submit" name="submit" value="Submit" />
	</fieldset>
<?php echo form_close(); ?>