/**
 * VWM Radio Matrix
 *
 * Possibly the most annoying question type (from both a development & user
 * perspective). But hey, that's what makes it interesting!
 */

/**
 * Add a matrix option
 */
$('.vwm_radio_matrix .new_matrix_option input[type="button"]').live('click', function() {
	var question = $(this).closest('li');
	
	var x_axis = $(question).find('ul.x_axis');
	var y_axis = $(question).find('ul.y_axis');


	var question_number = $(question).find('input[name="question_number"]').val();
	var page_number = $(this).closest('li.vwm_surveys_page').attr('id').substring(17) * 1;
	
	var x_option_key = random_string(16);
	var y_option_key = random_string(16);

	var x_text_name = 'vwm_surveys_pages[' + page_number + '][questions][' + question_number + '][options][x][' + x_option_key + '][text]';
	var y_text_name = 'vwm_surveys_pages[' + page_number + '][questions][' + question_number + '][options][y][' + y_option_key + '][text]';

	var x_option_text = ''; // Let's just leave this blank for now and let the user add this in later
	var y_option_text = $(this).closest('.new_matrix_option').find('input[type="text"]').val();

	var x_text_input = $('<input />').attr('type', 'text').attr('name', x_text_name).val(x_option_text);
	var y_text_input = $('<input />').attr('type', 'text').attr('name', y_text_name).val(y_option_text);

	var x_remove_button = $('<input />').attr('type', 'button').addClass('remove').attr('value', 'Remove');
	var y_remove_button = $(x_remove_button).clone();
	
	var x_el = $('<li />');
	$(x_el).append(x_text_input).append(x_remove_button);
	$(x_axis).append(x_el);

	var y_el = $('<li />');
	$(y_el).append(y_text_input).append(y_remove_button);
	$(y_axis).append(y_el);
});

/**
 * Remove a radio matrix option
 *
 * This will just remove one row. A radio matrix must have an equal number of
 * rows on both the x and y axis. The enforcement of this rule will occur later.
 */
$('.vwm_radio_matrix ul.options input.remove').live('click', function() {
	$(this).closest('li').remove();
});

/**
 * Make radio matrix options sortable
 */
$('.vwm_radio_matrix ul.options').sortable({
	axis: 'y',
	containment: 'parent'
});

/**
 * On keyup inside new radio input
 */
$('.vwm_radio_matrix .new_matrix_option :input').live('keyup', function(e) {
	// If the use pressed the "enter" key
	if (e.which == 13) {
		// Trigger a click event on the "Add Radio" button
		$(this).closest('div').find('input[type="button"]').trigger('click');
	}
});