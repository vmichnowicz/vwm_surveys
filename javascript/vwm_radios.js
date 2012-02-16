/**
 * VWM Radios
 *
 * This question type is cool!
 */

/**
 * Add a radio option
 */
$('.vwm_radios .new_radio input[type="button"]').live('click', function() {
	var all_radios = $(this).closest('li.vwm_surveys_question').find('ul.all_radios');
	var question_number = $(this).closest('li.vwm_surveys_question').find('input[name="question_number"]').val();
	var page_number = $(this).closest('li.vwm_surveys_page').attr('id').substring(17) * 1;
	var option_key = random_string(16); // Most likely a unique string...

	var text_name = 'vwm_surveys_pages[' + page_number + '][questions][' + question_number + '][options][radios][' + option_key + '][text]';
	var type_name = 'vwm_surveys_pages[' + page_number + '][questions][' + question_number + '][options][radios][' + option_key + '][type]';
	var option_text = $(this).closest('.new_radio').find('input[type="text"]').val();
	var option_type = $(this).closest('.new_radio').find('select').val(); // "defined" or "other"

	var text_input = $('<input />').attr('type', 'text').attr('name', text_name).val(option_text);
	var select_input = $(this).siblings('select').clone().attr('name', type_name);
	var remove_button = $('<input />').attr('type', 'button').addClass('remove').attr('value', 'Remove');
	var el = $('<li />');

	$(el).append(text_input).append(select_input).append(remove_button);
	$(all_radios).append(el);

	// Update value of our select input
	$(all_radios).find('select:last').val( option_type );

});

/**
 * Remove a radio option
 */
$('.vwm_radios input.remove').live('click', function() {
	$(this).closest('li').remove();
});

/**
 * Make radios sortable
 */
$('.vwm_radios .all_radios').sortable({
	axis: 'y',
	containment: 'parent'
});

/**
 * On keyup inside new radio input
 */
$('.vwm_radios .new_radio :input').live('keyup', function(e) {
	// If the use pressed the "enter" key
	if (e.which == 13) {
		// Trigger a click event on the "Add Radio" button
		$(this).closest('div').find('input[type="button"]').trigger('click');
	}
});