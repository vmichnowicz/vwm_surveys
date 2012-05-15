<div class="custom_rating_headers">
	<label>Custom Rating Headers:</label>
	<ul class="all_ratings">
		<table>
			<thead>
				<tr>
					<th>Rating</th>
					<th>Header</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( isset($options['ratings']) AND is_array($options['ratings']) ): ?>
					<?php foreach($options['ratings'] as $key => $rating): ?>
						<tr>
							<td><?php echo $key; ?></td>
							<td>
								<input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][ratings][<?php echo $key; ?>]" value="<?php echo htmlspecialchars($rating, ENT_QUOTES, 'UTF-8'); ?>"/>
							</td>
						</tr>
					<?php endforeach; ?>'
				<?php else: ?>
					<tr>
						<td></td>
						<td></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</ul>
</div>

<label>Minimum Value: <input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][min]" value="<?php echo isset($options['min']) ? (int)$options['min'] : 0; ?>" /></label>
<label>Maximum Value: <input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][max]" value="<?php echo isset($options['max']) ? (int)$options['max'] : NULL; ?>" /></label>
<label>
	Rating Type:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][options][type]" class="rating_type">
		<option value="dropdown" <?php echo ( isset($options['type']) AND $options['type'] === 'dropdown' ) ? 'selected="selected"' : NULL; ?>>Dropdown</option>
		<option value="radios" <?php echo ( isset($options['type']) AND $options['type'] === 'radios' ) ? 'selected="selected"' : NULL; ?>>Radios</option>
		<option value="text" <?php echo ( isset($options['type']) AND $options['type'] === 'text' ) ? 'selected="selected"' : NULL; ?>>Text</option>
	</select>
</label>