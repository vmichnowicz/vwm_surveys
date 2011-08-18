<?php if (isset($options['radios'])): ?>
	<ul>
		<?php foreach ($options['radios'] as $key => $radio): ?>
			<li>
				<label>
					<?php if ( isset($data['option']) AND $data['option'] == $key ): ?>
						<input type="radio" checked="checked" name="vwm_surveys_questions[<?php echo $id; ?>][data][radios][option]" value="<?php echo $key; ?>" />
					<?php else: ?>
						<input type="radio" name="vwm_surveys_questions[<?php echo $id; ?>][data][radios][option]" value="<?php echo $key; ?>" />
					<?php endif; ?>
					<span><?php echo $radio['text']; ?></span>
				</label>
				<?php if ($radio['type'] == 'other'): ?>
					<input type="text" name="vwm_surveys_questions[<?php echo $id; ?>][data][radios][other]" value="<?php echo isset($data['other']) ? legit_encode($data['other']) : NULL; ?>" maxlength="128" />
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>