<label>Minimum Value: <input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][min]" value="<?php echo isset($options['min']) ? (int)$options['min'] : 0; ?>" /></label>
<label>Maximum Value: <input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][max]" value="<?php echo isset($options['max']) ? (int)$options['max'] : NULL; ?>" /></label>
<label>
	Rating Type:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][type]">
		<option value="dropdown" <?php echo ( isset($options['type']) AND $options['type'] === 'dropdown' ) ? 'selected="selected"' : NULL; ?>>Dropdown</option>
		<option value="radios" <?php echo ( isset($options['type']) AND $options['type'] === 'radios' ) ? 'selected="selected"' : NULL; ?>>Radios</option>
		<option value="text" <?php echo ( isset($options['type']) AND $options['type'] === 'text' ) ? 'selected="selected"' : NULL; ?>>Text</option>
	</select>
</label>