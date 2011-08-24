<?php if ($results): ?>
	<div class="toggle">
		<div class="toggle_content">
			<ul>
				<?php foreach ($results['dates'] as $date): ?>
					<li><?php echo $date; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<a href="javascript:void(0);"><strong>Show</strong> <em>(<?php echo count($results); ?>) <span>&darr;</span></em></a>
	</div>
<?php else: ?>
	<p class="no_data">No data</p>
<?php endif; ?>