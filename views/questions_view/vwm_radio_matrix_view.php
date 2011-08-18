<?php if ( (isset($options['x']) AND isset($options['y'])) AND ( count($options['x']) == count($options['y']) ) ): ?>
	<table>
		<thead>
			<tr>
				<th>&nbsp;</th>
				<?php foreach ($options['x'] as $x): ?>
					<th><?php echo $x['text']; ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($options['y'] as $y_key => $y_value): ?>
				<tr>
					<th><?php echo $y_value['text']; ?></th>
					<?php foreach ($options['x'] as $x_key => $x_value): ?>
						<td>
							<input type="radio" name="vwm_surveys_questions[<?php echo $id; ?>][data][radio_matrix][<?php echo $y_key; ?>][option]" value="<?php echo $x_key; ?>" <?php echo ( isset($data['selections'][ $y_key ]) AND $data['selections'][ $y_key ] == $x_key) ? 'checked="checked"' : NULL;?> />
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>