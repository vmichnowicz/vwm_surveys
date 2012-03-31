<label>X-axis</label>

<ul class="options x_axis">
	<?php if (isset($options['x'])): ?>
		<?php foreach ($options['x'] as $key => $option): ?>
			<li>
				<input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][x][<?php echo $key; ?>][text]" value="<?php echo htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8'); ?>" />
				<input type="button" class="remove" value="Remove" />
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>

<label>Y-axis</label>

<ul class="options y_axis">
	<?php if (isset($options['y'])): ?>
		<?php foreach ($options['y'] as $key => $option): ?>
			<li>
				<input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][y][<?php echo $key; ?>][text]" value="<?php echo htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8'); ?>" />
				<input type="button" class="remove" value="Remove" />
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>

<div class="new_matrix_option">
	<label>New matrix option: <input type="text" class="no_submit"/></label>
	<input type="button" name="new_matrix_option" value="Add Option">
</div>

<label>X Axis Order:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][x_order]">
		<option value="custom" <?php echo (isset($options['x_order']) AND $options['x_order'] == 'custom') ? 'selected="selected"' : NULL; ?>>Custom</option>
		<option value="random" <?php echo (isset($options['x_order']) AND $options['x_order'] == 'random') ? 'selected="selected"' : NULL; ?>>Random</option>
	</select>
</label>

<label>Y Axis Order:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][y_order]">
		<option value="custom" <?php echo (isset($options['y_order']) AND $options['y_order'] == 'custom') ? 'selected="selected"' : NULL; ?>>Custom</option>
		<option value="random" <?php echo (isset($options['y_order']) AND $options['y_order'] == 'random') ? 'selected="selected"' : NULL; ?>>Random</option>
	</select>
</label>