<label>
	Date Format:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][format]" class="vwm_date_format">
		<?php foreach($this->config->item('vwm_surveys_date_formats') as $format): ?>
			<?php if ( isset($options['format']) AND $options['format'] == $format ): ?>
				<option value="<?php echo $format; ?>" selected="selected"><?php echo $format; ?></option>
			<?php else: ?>
				<option value="<?php echo $format; ?>"><?php echo $format; ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
</label>
<label>Later than: <input type="text" class="datepicker vwm_date_later_than" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][later_than]" value="<?php echo isset($options['later_than']) ? $options['later_than'] : NULL; ?>" maxlength="10" /></label>
<label>Earlier than: <input type="text" class="datepicker vwm_date_earlier_than" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][earlier_than]" value="<?php echo isset($options['earlier_than']) ? $options['earlier_than'] : NULL; ?>" maxlength="10" /></label>