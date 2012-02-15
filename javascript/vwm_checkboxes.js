/**
 * VWM Checkboxes
 *
 * This question type is really cool!
 */

/**
 * Add a checkbox
 */
$('.vwm_checkboxes .new_checkbox input[type="button"]').live('click', function() {
	var all_checkboxes = $(this).closest('li.vwm_surveys_question').find('ul.all_checkboxes');
	var question_number = $(this).closest('li.vwm_surveys_question').find('input[name="question_number"]').val();
	var page_number = $(this).closest('li.vwm_surveys_page').attr('id').substring(17) * 1;
	var option_key = random_string(16); // Most likely a unique string...

	var text_name = 'vwm_surveys_pages[' + page_number + '][questions][' + question_number + '][options][checkboxes][' + option_key + '][text]';
	var type_name = 'vwm_surveys_pages[' + page_number + '][questions][' + question_number + '][options][checkboxes][' + option_key + '][type]';
	var option_text = $(this).closest('.new_checkbox').find('input[type="text"]').val();
	var option_type = $(this).closest('.new_checkbox').find('select').val(); // "defined" or "other"

	var text_input = $('<input />').attr('type', 'text').attr('name', text_name).val(option_text);
	var select_input = $(this).siblings('select').clone().attr('name', type_name);
	var remove_button = $('<input />').attr('type', 'button').addClass('remove').attr('value', 'Remove');
	var el = $('<li />');

	$(el).append(text_input).append(select_input).append(remove_button);
	$(all_checkboxes).append(el);

	// Update value of our select input
	$(all_checkboxes).find('select:last').val( option_type );
});


/**
 * Remove a checkbox option
 */
$('.vwm_checkboxes input.remove').live('click', function() {
	$(this).closest('li').remove();
});

/**
 * Make checkboxes sortable
 */
$('.vwm_checkboxes .all_checkboxes').sortable({
	axis: 'y',
	containment: 'parent'
});

/**
 * On keyup inside new checkbox input
 */
$('.vwm_checkboxes .new_checkbox :input').live('keyup', function(e) {
	// If the use pressed the "enter" key
	if (e.which == 13) {
		// Trigger a click event on the "Add Checkbox" button
		$(this).closest('div').find('input[type="button"]').trigger('click');
	}
});