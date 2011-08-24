<label>
	Date Format:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][format]">
		<?php foreach($this->config->item('vwm_surveys_date_formats') as $key => $value): ?>
			<?php if ( isset($options['format']) AND $options['format'] == $key ): ?>
				<option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
			<?php else: ?>
				<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
</label>
<label>Later than: <input type="text" class="datepicker" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][later_than]" value="<?php echo isset($options['later_than']) ? $options['later_than'] : NULL; ?>" /></label>
<label>Earlier than: <input type="text" class="datepicker" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][earlier_than]" value="<?php echo isset($options['later_than']) ? $options['later_than'] : NULL; ?>" /></label>