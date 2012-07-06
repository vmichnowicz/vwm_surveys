<h3>Add a Survey</h3>

<?php echo form_open($action_url) ?>
	<fieldset>
		<legend>Survey Information</legend>
		<label for="vwm_surveys_survey_title">Title:</label>
		<input type="text" name="title" id="vwm_surveys_survey_title" />

		<!-- Only show site dropdown if there is more than one site -->
		<?php if ( isset($sites) AND is_array($sites) AND count($sites) > 0 ): ?>
			<label for="site_id">Site</label>
			<select name="site_id" id="site_id">
				<?php foreach ($sites as $site): ?>
					<option value="<?php echo $site['id']; ?>" <?php echo $site['id'] === $site_id ? 'selected="selected"' : NULL; ?>>
						<?php echo $site['label']; ?>
					</option>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>

	</fieldset>
	<fieldset>
		<legend>Submit</legend>
		<input type="submit" class="submit" name="submit" value="Submit" />
	</fieldset>
<?php echo form_close(); ?>