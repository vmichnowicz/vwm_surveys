<?php if ($results): ?>
	<div class="toggle">
		<div class="toggle_content">
			<ul>
				<?php foreach ($results as $submission_id => $submission): ?>
					<li><?php echo legit_encode($submission); ?> <a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submission' . AMP . 'submission_id=' . $submission_id; ?>" class="view_survey">View Individual Survey (<?php echo $submission_id; ?>)</a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<a href="javascript:void(0);"><strong>Show</strong> <em>(<?php echo count($results); ?>) <span>&darr;</span></em></a>
	</div>
<?php else: ?>
	<p class="no_data">No data</p>
<?php endif; ?>