<?php echo form_open($action_url, array('id' => 'vwm_survey_submissions_search')) ?>
	<fieldset>
		<legend>Filter</legend>

		<div class="input">
			<label for="filter_survey_id">Survey:</label>
			<select name="filter_survey_id" id="filter_survey_id">
				<option value="">Any</option>
				<?php if ($surveys): ?>
					<?php foreach ($surveys as $survey): ?>
						<option value="<?php echo $survey['id']; ?>" <?php echo $filters['survey_id'] == $survey['id'] ? 'selected="selected"' : NULL; ?>><?php echo $survey['id']; ?> &mdash; <?php echo $survey['title']; ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>

		<div class="input">
			<label for="filter_member_id">Member:</label>
			<select name="filter_member_id" id="filter_member_id">
				<option value="">Any</option>
					<?php foreach ($members as $member): ?>
						<option value="<?php echo $member['id']; ?>" <?php echo $filters['member_id'] == $member['id'] ? 'selected="selected"' : NULL; ?>><?php echo $member['id']; ?> &mdash; <?php echo $member['screen_name']; ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="input">
			<label for="filter_group_id">Group:</label>
			<select name="filter_group_id" id="filter_group_id">
				<option value="">Any</option>
					<?php foreach ($groups as $group_id => $group): ?>
						<option value="<?php echo $group_id; ?>" <?php echo $filters['group_id'] == $group_id ? 'selected="selected"' : NULL; ?>><?php echo $group_id; ?> &mdash; <?php echo $group; ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="input">
			<label for="filter_created_from">Created from:</label>
			<input type="text" name="filter_created_from" id="filter_created_from" class="datepicker" value="<?php echo $filters['created_from'] ? htmlspecialchars($filters['created_from'], ENT_QUOTES, 'UTF-8') : NULL; ?>" />
			to <input type="text" name="filter_created_to" id="filter_created_to" class="datepicker" value="<?php echo $filters['created_to'] ? htmlspecialchars($filters['created_to'], ENT_QUOTES, 'UTF-8') : NULL; ?>" />
		</div>

		<div class="input">
			<label for="filter_updated_from">Updated from:</label>
			<input type="text" name="filter_updated_from" id="filter_updated_from" class="datepicker" value="<?php echo $filters['updated_from'] ? htmlspecialchars($filters['updated_from'], ENT_QUOTES, 'UTF-8') : NULL; ?>" />
			to <input type="text" name="filter_updated_to" id="filter_updated_to" class="datepicker" value="<?php echo $filters['updated_to'] ? htmlspecialchars($filters['updated_to'], ENT_QUOTES, 'UTF-8') : NULL; ?>" />
		</div>

		<div class="input">
			<label for="filter_complete">Complete:</label>
			<select name="filter_complete" id="filter_complete">
				<option value="" <?php echo $filters['complete'] === NULL ? 'selected="selected"' : NULL; ?>>Both</option>
				<option value="1" <?php echo $filters['complete'] === TRUE ? 'selected="selected"' : NULL; ?>>Yes</option>
				<option value="0" <?php echo $filters['complete'] === FALSE ? 'selected="selected"' : NULL; ?>>No</option>
			</select>
		</div>

	</fieldset>

	<fieldset>
		<legend>Sort Order</legend>

		<div class="input">
			<label for="order_by">Order by:</label>
			<select name="order_by" id="order_by">
				<option value="id" <?php echo $order_by == 'id' ? 'selected="selected"' : NULL; ?>>ID</option>
				<option value="member_id" <?php echo $order_by == 'member_id' ? 'selected="selected"' : NULL; ?>>Member ID</option>
				<option value="survey_id" <?php echo $order_by == 'survey_id' ? 'selected="selected"' : NULL; ?>>Survey ID</option>
				<option value="created" <?php echo $order_by == 'created' ? 'selected="selected"' : NULL; ?>>Created date</option>
				<option value="updated" <?php echo $order_by == 'updated' ? 'selected="selected"' : NULL; ?>>Update date</option>
				<option value="completed" <?php echo $order_by == 'completed' ? 'selected="selected"' : NULL; ?>>Completed date</option>
				<option value="complete" <?php echo $order_by == 'complete' ? 'selected="selected"' : NULL; ?>>Complete</option>
			</select>
			<select name="order_by_order" id="order_by_order">
				<option value="ASC" <?php echo $order_by_order == 'ASC' ? 'selected="selected"' : NULL; ?>>Ascending</option>
				<option value="DESC" <?php echo $order_by_order == 'DESC' ? 'selected="selected"' : NULL; ?>>Descending</option>
				<option value="RANDOM" <?php echo $order_by_order == 'RANDOM' ? 'selected="selected"' : NULL; ?>>Random</option>
			</select>
		</div>

	</fieldset>

	<fieldset>
		<legend>Search</legend>

		<div class="input">
			<input class="submit" type="submit" name="submit" value="Search" />
		</div>

	</fieldset>

<?php echo form_close(); ?>

<br />

<table class="mainTable" border="0px" cellpadding="0px" cellspacing="0px">
	<thead>
		<tr>
			<th>ID</th>
			<th>Hash</th>
			<th>Member ID</th>
			<th>Group ID</th>
			<th>Survey ID</th>
			<th>Created</th>
			<th>Updated</th>
			<th>Completed</th>
			<th>Complete</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php if ( ! empty($submissions) ): ?>
			<?php foreach ($submissions as $submission): ?>
				<tr>
					<td><?php echo $submission['id']; ?></td>
					<td><?php echo $submission['hash']; ?></td>
					<td><?php echo $submission['member_id']; ?></td>
					<td><?php echo $members[ $submission['member_id'] ]['group_id']; ?></td>
					<td><?php echo $submission['survey_id']; ?></td>
					<td><?php echo date('l F jS Y @ g:ia', $submission['created']); ?></td>
					<td><?php echo $submission['updated'] ? date('l F jS Y @ g:ia', $submission['updated']) : NULL; ?></td>
					<td><?php echo $submission['completed'] ? date('l F jS Y @ g:ia', $submission['completed']) : NULL; ?></td>
					<td><?php echo $submission['complete'] ? '<span class="yes">Yes</span>' : '<span class="no">No</span>'; ?></td>
					<td style="text-align: center;">
						<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=survey_submission' . AMP . 'hash=' . $submission['hash']; ?>" class="submit">View Individual Survey</a>
						<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=delete_survey_submission' . AMP . 'id=' . $submission['id']; ?>" class="submit">Delete Submission</a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
				<tr>
					<td colspan="10">No survey submissions.</td>
				</tr>
		<?php endif; ?>
	</tbody>
</table>