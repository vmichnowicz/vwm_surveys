<?php if ($results): ?>
	<table style="width: 100%;">
		<thead>
			<tr>
				<th style="width: 7%;">&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($question['options']['y'] as $y_key => $y_value): ?>
				<tr>
					<th><?php echo $y_value['text']; ?></th>
					<td>
						<div class="bar_chart">
							<ul>
								<?php foreach ($question['options']['x'] as $x_key => $x_value): ?>
									<li
										<?php $percent = $results[ $y_key ][ $x_key ]['count'] > 0 ? $results[ $y_key ][ $x_key ]['count'] / $num_submissions * 100 : 0; ?>

										<p><?php echo $x_value['text']; ?> (<?php echo $results[ $y_key ][ $x_key ]['count']; ?> Votes)</p>
										<div class="bar" style="width: <?php echo $percent; ?>%">
											<span><?php echo round($percent, 1); ?>%</span>
										</div>
									</li>
								<?php endforeach; ?>	
							</ul>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
