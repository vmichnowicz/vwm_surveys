/**
 * VWM Rating Matrix
 *
 * This question type is super cool!
 */

/**
 * Toggle custom rating headers
 */
var toggle_custom_radio_matrix_headers = function toggle_custom_radio_matrix_headers() {

	var radio_matrix = $('li.vwm_rating_matrix');

	$(radio_matrix).each(function() {
		var question = $(this).closest('li');
		var rating_type = $(question).find('select.rating_type');
		var custom_rating_headers = $(question).find('.custom_rating_headers');

		if ( $(rating_type).val() === 'radios' ) {
			$(custom_rating_headers).show();
		}
		else {
			$(custom_rating_headers).hide();
		}
	});

	return toggle_custom_radio_matrix_headers;
}();

$('.vwm_rating_matrix select.rating_type').live('change', function() {
	toggle_custom_radio_matrix_headers();
});