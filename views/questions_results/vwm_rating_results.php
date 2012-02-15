<?php if ($results): ?>
	<?php echo $results['total'] . $num_submissions; ?>
<?php else: ?>
	<p class="no_data">No data</p>
<?php endif; ?>