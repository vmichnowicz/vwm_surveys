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
		var question_number = $(question).find('input[name="question_number"]').val();
		var page_number = $(question).closest('li.vwm_surveys_page').attr('id').substring(17) * 1;
		var min = parseInt( $(question).find('input.min').val() );
		var max = parseInt( $(question).find('input.max').val() );
		var num_levels = ( ( ! isNaN(min) && ! isNaN(max) ) && (max > min) ) ? max - min : null;
		var rating_type_select = $(question).find('select.rating_type');
		var custom_rating_headers_div = $(question).find('div.custom_rating_headers');
		var custom_rating_headers_select = $(question).find('select.custom_rating_headers');
		var custom_rating_headers_label = $(custom_rating_headers_select).closest('label');
		var tbody = $('<tbody />');

		for (var i = min; i <= max; i++) {

			var tr = $('<tr />');
			var key_td = $('<td />');
			var key_label = $('<label />').attr('for', 'vwm_rating_matrix_' + page_number + '_' + question_number + '_' + i ).text(i);
			var header_td = $('<td />');
			var header_input = $('<input />').attr('type', 'text').attr('id', 'vwm_rating_matrix_' + page_number + '_' + question_number + '_' + i );

			$(key_td).append(key_label);
			$(header_td).append(header_input);

			$(tr).append(key_td, header_td);
			$(tbody).append(tr);

			console.log(tr);
		}
5
		console.log(tbody);

		$(custom_rating_headers_div).find('tbody').replaceWith(tbody);

		// Hide both "Custom Rating Headers" select input and div
		$(custom_rating_headers_label).hide();
		$(custom_rating_headers_div).hide();

		// If "Rating Type" is "Radios"
		if ( $(rating_type_select).val() === 'radios' ) {
			// Show "Custom Rating Headers" select input
			$(custom_rating_headers_label).show();

			// If "Custom Rating Headers" select is "Yes"
			if ( $(custom_rating_headers_select).val() === 'TRUE' ) {
				$(custom_rating_headers_div).show();
			}
		}
	});

	return toggle_custom_radio_matrix_headers;
}();

$('.vwm_rating_matrix select.rating_type, .vwm_rating_matrix select.custom_rating_headers').live('change', function() {
	toggle_custom_radio_matrix_headers();
});