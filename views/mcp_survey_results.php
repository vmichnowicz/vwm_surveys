<?php if ($new_completed_submissions): ?>
	<p class="notice">
		There have been <?php echo $new_completed_submissions; ?> completed survey submissions since you last compiled these survey results <?php echo date('l F jS Y @ g:ia', $results['compiled']); ?>.
		<a href="<?php echo BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=compile_survey_results' . AMP . 'survey_id=' . $survey['id'];?>">Generate new survey results</a>.
	</p>
<?php endif; ?>

<h2>Survey Results</h2>

<?php if ( ! empty($results) AND isset($results['num_submissions']) AND $results['num_submissions'] > 0 ): ?>

	<p>Total number of survey submissions: <?php echo $results['num_submissions']; ?></p>
	<p>Survey results generated <?php echo date('l F jS Y @ g:ia', $results['compiled']); ?></p>

	<ol id="survey_results">
		<?php foreach ($survey['pages'] as $page): ?>
			<li>
				<h3><?php echo $page['title']; ?></h3>
				<?php if ( isset($page['questions'])  AND is_array($page['questions']) AND count($page['questions']) > 0 ): ?>
					<ul>
						<?php foreach ($page['questions'] as $question): ?>
							<?php if ( isset($results['data'][ $question['id'] ]) ): ?>
								<li>
									<h4><?php echo $question['title']; ?></h4>
									<?php $this->load->view('questions_results/vwm_' . $question['type'] . '_results', array('question' => $question, 'results' => $results['data'][ $question['id'] ], 'num_submissions' => $results['num_submissions'] )); ?>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
<?php else: ?>
	<p>Either no results have been compiled yet or no survey submissions have been recorded.</p>
<?php endif; ?>
