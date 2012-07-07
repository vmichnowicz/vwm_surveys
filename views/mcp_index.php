<h3>All Surveys</h3>

<table class="mainTable" border="0px" cellpadding="0px" cellspacing="0px">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Questions</th>
			<th>Created</th>
			<th>Updated</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( isset($surveys) AND is_array($surveys) ): ?>
			<?php foreach ($surveys as $survey): ?>
				<tr>
					<td><?php echo $survey['id']; ?></td>
					<td><?php echo $survey['title']; ?></td>
					<td><?php echo $survey['num_questions']; ?></td>
					<td><?php echo date('l F jS Y @ g:ia', $survey['created']); ?></td>
					<td><?php echo $survey['updated'] ? date('l F jS Y @ g:ia', $survey['updated']) : NULL; ?></td>
					<td style="text-align: center;">
						<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=compile_survey_results' . AMP . 'survey_id=' . $survey['id']; ?>" class="submit">Compile Results</a>
						<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=survey_results' . AMP . 'survey_id=' . $survey['id']; ?>" class="submit">Results</a>
						<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=edit_survey' . AMP . 'survey_id=' . $survey['id']; ?>" class="submit">Modify</a>
						<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=delete_survey_submissions' . AMP . 'survey_id=' . $survey['id'];?>" class="submit confirm" title="Remove all survey submissions for this survey">Remove All Submissions</a>
						<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=delete_survey' . AMP . 'survey_id=' . $survey['id']; ?>" class="submit confirm" title="Delete survey and all associated survey submissions">Delete</a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr><td colspan="6">No surveys have been created yet.</td></tr>
		<?php endif; ?>
	</tbody>
</table>