<label>Checkbox Inputs:</label>
<ul class="all_checkboxes">
	<?php if (isset($options['checkboxes'])): ?>
		<?php foreach($options['checkboxes'] as $key => $checkbox): ?>
			<li>
				<input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][checkboxes][<?php echo $key; ?>][text]" value="<?php echo htmlspecialchars($checkbox['text'], ENT_QUOTES, 'UTF-8'); ?>"/>
				<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][checkboxes][<?php echo $key; ?>][type]">
					<?php if ($checkbox['type'] == 'defined'): ?>
						<option value="defined" selected="selected">Defined</option>
						<option value="other">Other</option>
					<?php else: ?>
						<option value="defined">Defined</option>
						<option value="other" selected="selected">Other</option>
					<?php endif; ?>
				</select>
				<input type="button" class="remove" value="Remove" />
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>

<div class="new_checkbox">
	<label>New Checkbox:
		<input type="text" name="" value="" class="no_submit" />
	</label>

	<select name="" class="no_submit">
		<option value="defined">Defined</option>
		<option value="other">Other</option>
	</select>
	<input type="button" name="new_checkbox" value="Add Checkbox" />
</div>

<label>Order:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][order]">
		<option value="custom" <?php echo ( isset($options['order']) AND $options['order'] == 'custom' ) ? 'selected="selected"' : NULL; ?>>Custom</option>
		<option value="random" <?php echo ( isset($options['order']) AND $options['order'] == 'random' ) ? 'selected="selected"' : NULL; ?>>Random</option>
	</select>
</label>

<label>Minimum Number of Options: <input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][min_options]" value="<?php echo isset($options['min_options']) ? $options['min_options'] : NULL; ?>" /></label>
<label>Maximum Number of Options: <input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][max_options]" value="<?php echo isset($options['max_options']) ? $options['max_options'] : NULL; ?>" /></label>