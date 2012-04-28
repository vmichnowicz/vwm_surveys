<!-- Question title -->
<label class="question_title">
	<span>Title:</span>
	<textarea name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][title]" cols="64" rows="3"><?php echo htmlspecialchars($question['title'], ENT_QUOTES, 'UTF-8'); ?></textarea>
</label>

<div class="question_controls">
	<!-- Delete question -->
	<div class="delete_question">
		<a href="javascript:void(0);" title="Delete Question">&otimes;</a>
	</div>

	<!-- Move question -->
	<div class="move_question">
		<a href="javascript:void(0);" class="up" title="Move question up">&uArr;</a>
		<a href="javascript:void(0);" class="down" title="Move question down">&dArr;</a>
	</div>
</div>

<!-- Question type -->
<label class="question_type">
	Question Type:
	<select name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][type]">
		<?php foreach ($question_types as $type => $name): ?>
			<?php if ($question['type'] == $type): ?>
				<option value="<?php echo $type; ?>" selected="selected"><?php echo $name; ?></option>
			<?php else: ?>
				<option value="<?php echo $type; ?>"><?php echo $name; ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
</label>

<!-- Hidden inputs -->
<div class="hidden_inputs">
	<input type="hidden" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][id]" value="<?php echo $question['id']; ?>" class="id" />
	<input type="hidden" name="vwm_surveys_pages[<?php echo $page_number; ?>][questions][<?php echo $question_number; ?>][custom_order]" value="<?php echo $question['custom_order']; ?>" class="custom_order"/>
	<input type="hidden" name="question_number" value="<?php echo $question_number; ?>" class="custom_order" />
</div>

<!-- Question-specific options -->
<?php $this->load->view('questions_edit/vwm_' . $question['type'] . '_edit', array('page_number' => $page_number, 'question_number' => $question_number, 'options' => $question['options'])); ?>
