<?php if ( isset($options['type']) ): ?>
	<?php if ( $options['type'] === 'dropdown' ): ?>
		<select id="vwm_surveys_question_<?php echo $id; ?>" name="vwm_surveys_questions[<?php echo $id; ?>][data][rating]" value="<?php echo isset($data['rating']) ? $data['rating'] : null; ?>">
			<?php for ( $i = 0; $i < intval($options['max']); $i++ ): ?>
				<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
	<?php elseif ( $options['type'] === 'radios' ): ?>
		<?php for ( $i = 0; $i < intval($options['max']); $i++ ): ?>
			<label><input type="radio" name="vwm_surveys_questions[<?php echo $id; ?>][data][rating]" value="<?php echo $i; ?>" /> <?php echo $i; ?></label>
		<?php endfor; ?>
	<?php elseif ( $options['type'] === 'text' ): ?>
		<input id="vwm_surveys_question_<?php echo $id; ?>" type="text" name="vwm_surveys_questions[<?php echo $id; ?>][data][rating]" value="" maxlength="<?php echo strlen($options['max']); ?>" />
	<?php endif; ?>
<?php endif; ?>