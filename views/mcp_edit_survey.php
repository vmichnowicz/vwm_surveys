<?php if ($has_submissions): ?>
	<p class="notice">WARNING: This survey already has some submissions. Editing this survey may result in survey errors and / or skewed survey results.</p>
<?php endif; ?>

<?php echo form_open($action_url, array('class' => 'edit_survey'), array('redirect_to' => $this->cp->get_safe_refresh()) ) ?>
	<fieldset>
		<legend>Survey Details</legend>
		<label>Title: <input type="text" name="vwm_surveys_title" value="<?php echo $title; ?>" /></label>
		<label>Creation Date: <input type="text" name="" value="<?php echo date('l F jS Y @ g:ia', $created); ?>" disabled="disabled"/></label>
		<label>Last Updated: <input type="text" name="" value="<?php echo $updated ? date('l F jS Y @ g:ia', $updated) : NULL; ?>" disabled="disabled"/></label>
	</fieldset>

	<fieldset>
		<legend>Allowed Groups</legend>
		<label>
			<input type="radio" name="vwm_surveys_allowed_groups" value="A" <?php echo $allowed_groups === 'A' ? 'checked="checked"' : NULL; ?> />
			All
		</label>
		<label>
			<input type="radio" name="vwm_surveys_allowed_groups" value="NULL" <?php echo $allowed_groups === NULL ? 'checked="checked"' : NULL; ?> />
			None
		</label>
		<label>
			<input type="radio" name="vwm_surveys_allowed_groups" value="SELECT" <?php echo is_array($allowed_groups) ? 'checked="checked"' : NULL; ?> />
			Select
		</label>
		<div>
			<?php echo form_multiselect('vwm_surveys_select_allowed_groups[]', $member_groups, $allowed_groups); ?>
		</div>
	</fieldset>

	<fieldset>
		<legend>Pages (<?php echo count($pages); ?>)</legend>
		<ol id="vwm_surveys_pages">
			<?php foreach ($pages as $page_number => $page): ?>
				<li id="vwm_surveys_page_<?php echo $page_number; ?>" class="vwm_surveys_page">

					<!-- Page title -->
					<label class="page_title">
						<span>Page Title:</span>
						<input type="text" name="vwm_surveys_pages[<?php echo $page_number; ?>][title]" value="<?php echo isset($page['description']) ? htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') : NULL; ?>" />
					</label>

					<!-- Page description -->
					<label class="page_description">
						<span>Page Description:</span>
						<textarea name="vwm_surveys_pages[<?php echo $page_number; ?>][description]" rows="3" cols="32"><?php echo isset($page['description']) ? htmlspecialchars($page['description'], ENT_QUOTES, 'UTF-8') : NULL; ?></textarea>
					</label>

					<!-- Questions -->
					<ul class="vwm_surveys_questions">
						<?php if ( isset($page['questions']) ): ?>
							<?php foreach ($page['questions'] as $question_number => $question): ?>
								<li class="vwm_surveys_question vwm_<?php echo $question['type']; ?>">						
									<?php echo $this->load->view('vwm_question_template', array('question' => $question, 'page_number' => $page_number, 'question_number' => $question_number), TRUE); ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					
					<!-- Add a question -->
					<div class="add_question">
						<label>
							Add a question to this page:
							<select name="">
								<?php foreach ($question_types as $type => $name): ?>
									<option value="<?php echo $type; ?>"><?php echo $name; ?></option>
								<?php endforeach; ?>
							</select>
							<input type="hidden" name="page_number" />
							<a href="javascript:void(0);" class="submit">Add Question</a>
						</label>
					</div>
					
					<!-- Delete this page -->
					<div class="delete_page">
						<a href="javascript:void(0);" class="submit">Delete Page</a>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>

		<div class="clear"></div>
		
		<!-- Add a page -->
		<div class="add_page">
			<label>
				Add a page to this survey:
				<input type="text" class="no_submit" />
				
				<a href="javascript:void(0);" class="submit">Add Page</a>
			</label>
		</div>
	</fieldset>
	
	<fieldset>
		<legend>Save Survey</legend>
		<input type="hidden" name="vwm_surveys_id" value="<?php echo $id; ?>" />
		<input type="submit" name="submit" class="submit" value="Submit" />
	</fieldset>
<?php echo form_close(); ?>