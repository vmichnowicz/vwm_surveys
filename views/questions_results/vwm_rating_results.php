<?php if ($results): ?>
	<p>
		Average: <?php echo round($results['total'] / $num_submissions, 3); ?><br />
		Median: <?php echo round($results['median']); ?><br />
		Min: <?php echo $results['min']; ?><br />
		Max: <?php echo $results['max']; ?>
	</p>
<?php else: ?>
	<p class="no_data">No data</p>
<?php endif; ?>