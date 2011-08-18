<h2><?php echo $survey['title']; ?></h2>

<ol id="survey_submission">
	<?php foreach ($survey['pages'] as $page): ?>
		<li>
			<h3><?php echo $page['title']; ?></h3>
			<?php if ( isset($page['questions']) ): ?>
				<?php foreach ($page['questions'] as $question): ?>
					<h4><?php echo $question['title']; ?></h4>
					<?php $this->load->view('questions_view/vwm_' . $question['type'] . '_view', array('id' => $question['id'], 'options' => $question['options'], 'data' => isset($submission['data'][ $question['id'] ]) ? $submission['data'][ $question['id'] ] : NULL )); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>

<br />

<ul>
	<li><strong>Submission ID</strong>: <?php echo $submission['id']; ?></li>
	<li><strong>Submission hash</strong>: <?php echo $submission['hash']; ?></li>
	<li><strong>Survey ID</strong>: <?php echo $submission['survey_id']; ?></li></li>
	<li><strong>Member ID</strong>: <?php echo $submission['member_id']; ?></li></li>
	<li><strong>Submission created</strong>: <?php echo date('l F jS Y @ g:ia', $submission['created']); ?></li></li>
	<li><strong>Submission last updated</strong>: <?php echo $submission['updated'] ? date('l F jS Y @ g:ia', $submission['updated']) : NULL; ?></li></li>
	<li><strong>Submission completed</strong>: <?php echo $submission['completed'] ? date('l F jS Y @ g:ia', $submission['completed']) : NULL; ?></li></li>
	<li><strong>Complete?</strong>: <?php echo $submission['complete'] ? 'Yes' : 'No'; ?></li></li>
</ul>