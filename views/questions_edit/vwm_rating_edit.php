<label>Maximum Value: <input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][max]" value="<?php echo isset($options['max']) ? $options['max'] : null; ?>" /></label>
<label>
	Rating Type:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][type]">
		<option value="dropdown">Dropdown</option>
		<option value="radios">Radios</option>
		<option value="text">Text</option>
	</select>
</label>