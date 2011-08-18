<?php if (isset($options['checkboxes'])): ?>
	<ul>
		<?php foreach ($options['checkboxes'] as $key => $checkbox): ?>
			<li>
				<label>
					<?php if ( isset($data['selections'][ $key ]) ): ?>
						<input checked="checked" type="checkbox" name="vwm_surveys_questions[<?php echo $id; ?>][data][checkboxes][<?php echo $key; ?>][option]" value="<?php echo $key; ?>" />
					<?php else: ?>
						<input type="checkbox" name="vwm_surveys_questions[<?php echo $id; ?>][data][checkboxes][<?php echo $key; ?>][option]" value="<?php echo $key; ?>" />
					<?php endif; ?>
					<span><?php echo $checkbox['text']; ?></span>
				</label>
				<?php if ($checkbox['type'] == 'other'): ?>
					<input type="text" name="vwm_surveys_questions[<?php echo $id; ?>][data][checkboxes][<?php echo $key; ?>][other]" value="<?php echo isset($data['selections'][ $key ]['other']) ? legit_encode($data['selections'][ $key ]['other']) : NULL; ?>" maxlength="128" />
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

