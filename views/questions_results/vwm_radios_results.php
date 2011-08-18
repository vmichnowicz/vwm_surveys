<?php if ($results): ?>
	<div class="bar_chart">
		<ul>
			<?php foreach ($question['options']['radios'] as $id => $radio): ?>
				<li>
					<p><?php echo $radio['text']; ?> (<?php echo isset($results[ $id ]['count']) ? $results[ $id ]['count'] : 0; ?> votes)</p>

					<?php $percent = isset($results[ $id ]['count']) ? $results[ $id ]['count'] / $num_submissions * 100 : 0; ?>
					
					<div class="bar" style="width: <?php echo $percent; ?>%">
						<span><?php echo round($percent, 1)?>%</span>
					</div>

					<?php if ( isset($results[ $id ]['other']) ): ?>
						<div class="toggle">
							<div class="toggle_content">
								<ul>
									<?php foreach ($results[ $id ]['other'] as  $submission_id => $other): ?>
										<li><?php echo legit_encode($other); ?> <a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submission' . AMP . 'submission_id=' . $submission_id; ?>" class="view_survey">View Individual Survey (<?php echo $submission_id; ?>)</a></li>
									<?php endforeach; ?>
								</ul>
							</div>
							<a href="javascript:void(0);"><strong>Show</strong> <em>(<?php echo count($results[ $id ]['other']); ?>) <span>&darr;</span></em></a>
						</div>
					<?php endif; ?>

			<?php endforeach; ?>
		</ul>
	</div>
<?php else: ?>
	<p class="no_data">No data</p>
<?php endif; ?>