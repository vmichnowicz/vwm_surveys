<h3>All Surveys</h3>

<table class="mainTable" border="0px" cellpadding="0px" cellspacing="0px">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Questions</th>
			<th>Created</th>
			<th>Updated</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php if ($surveys): ?>
			<?php foreach ($surveys as $survey): ?>
				<tr>
					<td><?php echo $survey['id']; ?></td>
					<td><?php echo $survey['title']; ?></td>
					<td><?php echo $survey['num_questions']; ?></td>
					<td><?php echo date('l F jS Y @ g:ia', $survey['created']); ?></td>
					<td><?php echo $survey['updated'] ? date('l F jS Y @ g:ia', $survey['updated']) : NULL; ?></td>
					<td>
						<a href="<?php echo $compile_survey_results_url . $survey['id']; ?>" class="submit">Compile Results</a>
						<a href="<?php echo $survey_results_url . $survey['id']; ?>" class="submit">Results</a>
						<a href="<?php echo $edit_url . $survey['id']; ?>" class="submit">Modify</a>
						<a href="<?php echo $delete_url . $survey['id']; ?>" class="submit">Delete</a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr><td colspan="6">No surveys have been created yet. </td></tr>
		<?php endif; ?>
	</tbody>
</table>