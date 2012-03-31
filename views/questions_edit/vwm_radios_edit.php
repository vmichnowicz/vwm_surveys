<label>Radio Inputs:</label>
<ul class="all_radios">
	<?php if (isset($options['radios'])): ?>
		<?php foreach($options['radios'] as $key => $radio): ?>
			<li>
				<input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][radios][<?php echo $key; ?>][text]" value="<?php echo htmlspecialchars($radio['text'], ENT_QUOTES, 'UTF-8'); ?>"/>
				<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][radios][<?php echo $key; ?>][type]">
					<?php if ($radio['type'] == 'defined'): ?>
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

<div class="new_radio">
	<label>New Radio:
		<input type="text" name="" value="" class="no_submit" />
	</label>

	<select name="" class="no_submit">
		<option value="defined">Defined</option>
		<option value="other">Other</option>
	</select>
	<input type="button" name="new_radio" value="Add Radio" />
</div>

<label>Order:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][order]">
		<option value="custom" <?php echo ( isset($options['order']) AND $options['order'] == 'custom' ) ? 'selected="selected"' : NULL; ?>>Custom</option>
		<option value="random" <?php echo ( isset($options['order']) AND $options['order'] == 'random' ) ? 'selected="selected"' : NULL; ?>>Random</option>
	</select>
</label>