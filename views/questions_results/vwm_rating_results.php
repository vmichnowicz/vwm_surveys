<?php if ($results): ?>
	<p>
		Average: <?php echo isset($results['total']) ? ( round($results['total'] / $num_submissions, 3) ) : NULL; ?><br />
		Median: <?php echo isset($results['median']) ? round($results['median'], 3) : NULL; ?><br />
		Min: <?php echo isset($results['min']) ? $results['min'] : NULL; ?><br />
		Max: <?php echo isset($results['max']) ? $results['max'] : NULL; ?>
	</p>
<?php else: ?>
	<p class="no_data">No data</p>
<?php endif; ?>