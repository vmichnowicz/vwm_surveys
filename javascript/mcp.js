/**
 * Generate a random string
 *
 * @param int				Length of random string
 * @return string
 */
function random_string(length) {
    var text = '';
    var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (var i = 0; i < length; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
    return text;
}

/**
 * Add a question to the edit survey page
 */
$('#vwm_surveys_pages .add_question a').live('click', function() {
	
	// Get the UL with all the questions for this page
	var page_questions = $(this).closest('div').siblings('.vwm_surveys_questions');
	
	// Question type (ie: "radios", "checkboxes", etc...)
	var type = $(this).closest('div').find('select').val();

	// Add a new question
	$.get(EE.BASE, {
			C: 'addons_modules',
			M: 'show_module_cp',
			module: 'vwm_surveys',
			method: 'add_question',
			survey_id: $(this).closest('form').find('input[name="vwm_surveys_id"]').val(),
			type: type,
			custom_order: $(page_questions).children().length * 1,
			page_number: $(this).closest('li[id^="vwm_surveys_page_"]').attr('id').substring(17) * 1
		}, function(data) {
			// If question data was returned
			if (data) {
				var el = $('<li />').addClass('vwm_' + type).addClass('vwm_surveys_question').append(data);
				$(page_questions).append(el);
			}
	});
});

/**
 * Delete a question
 */
$('#vwm_surveys_pages .delete_question a').live('click', function() {

	// Make sure the user is sure he wants to delete this question
	var answer = confirm('Are you sure you want to delete this question?');

	// If the user does not want to delete this question
	if ( ! answer) { return; }

	// Get the UL with all the questions for this page
	var question = $(this).closest('li');
	var hidden_inputs = $(question).find('.hidden_inputs');

	// Delete the question
	$.get(EE.BASE, {
			C: 'addons_modules',
			M: 'show_module_cp',
			module: 'vwm_surveys',
			method: 'delete_question',
			question_id: $(hidden_inputs).find('input.id').val(),
			page_number: $(this).closest('li[id^="vwm_surveys_page_"]').attr('id').substring(17) * 1
		}, function(data) {
			if (data.result == 'success') {
				$(question).remove();
				$.ee_notice('Delete successful', {open: true, type: 'success'});
			}
			else {
				$.ee_notice('Delete failed', {open: true, type: 'error'});
			}
	}, 'json');
});

/**
 * Move a question up or down the page
 */
$('.move_question a').live('click', function() {

	// Get the UL with all the questions for this page
	var question = $(this).closest('li');
	var hidden_inputs = $(question).find('.hidden_inputs');
	var move = $(this).attr('class');

	$.get(EE.BASE, {
			C: 'addons_modules',
			M: 'show_module_cp',
			module: 'vwm_surveys',
			method: 'move_question',
			question_id: $(hidden_inputs).find('input.id').val(),
			survey_id: $(this).closest('form').find('input[name="vwm_surveys_id"]').val(),
			move: move
		}, function(data) {
			var pages = $('#vwm_surveys_pages');
			var new_pages = $(data).find('#vwm_surveys_pages > li');
			$(pages).empty().html(new_pages);
	});	
});

/**
 * Delete a page from this survey
 */
$('.delete_page a').live('click', function() {

	// Make sure the user is sure he wants to delete this page
	var answer = confirm('Are you sure you want to delete this page?');

	// If the user does not want to delete this page
	if ( ! answer) { return; }

	var page = $(this).closest('li');
	var page_number = $(page).attr('id').substring(17);
	var survey_id = $(this).closest('form').find('input[name="vwm_surveys_id"]').val();

	$.get(EE.BASE, {
			C: 'addons_modules',
			M: 'show_module_cp',
			module: 'vwm_surveys',
			method: 'delete_page',
			survey_id: survey_id,
			page: page_number
		}, function(data) {
			// Remove page from DOM
			$(page).remove();
	});	
});

/**
 * Confirm an action
 *
 * @return bool|void
 */
$('a.confirm').click(function(e) {
	if ( confirm( $(this).attr('title') ) ) {
		return true;
	}
	else {
		e.preventDefault();
	}
});

/**
 * Add a page to the edit survey page
 */
$('.add_page a').live('click', function() {

	var pages = $(this).closest('form').find('#vwm_surveys_pages');
	var survey_id = $(this).closest('form').find('input[name="vwm_surveys_id"]').val();
	var title = $(this).siblings('input').val();

	// Add a new page
	$.get(EE.BASE, {
			C: 'addons_modules',
			M: 'show_module_cp',
			module: 'vwm_surveys',
			method: 'add_page',
			survey_id: survey_id,
			title: title
		}, function(data) {
			var page = $(data).find('#vwm_surveys_pages li:last');
			$(pages).append(page)
	});	
});

/**
 * Whenever a user registers a kek up event on the add page text input we want
 * to see if it was the "Enter" key. If it was then we want to trigger a click
 * event for the "Add Page" link.
 */
$('form.edit_survey .add_page :input').live('keyup', function(e) {
	// If the use pressed the "enter" key
	if (e.which == 13) {
		$(this).siblings('a').trigger('click');
	}
});

/**
 * Toggle
 */
$('.toggle > a').click(function() {
	var link_text = $(this).find('strong');
	var arrow = $(this).find('span')
	var content = $(this).prev('.toggle_content');
	$(content).slideToggle('fast', function() {
		if ($(this).is(':visible')) {
			$(link_text).text('Hide');
			$(arrow).html('&uarr;'); // Up arrow
		}
		else {
			$(link_text).text('Show');
			$(arrow).html('&darr;'); // Down arrow
		}
	});
});

/**
 * jQuery UI datepicker for date question type
 */
var datepicker = function datepicker() {
	var datepickers = $('li.vwm_date .datepicker'); // All date question type datepickers
	var date_format_selects = $('li.vwm_date select.vwm_date_format'); // All date question type date format selectors

	// Update all datepickers for date question type
	var update_date_format = function update_date_format() {
		$(datepickers).each(function() {

			var li = $(this).closest('li');
			var date_format_select = $(li).find('select.vwm_date_format').val();
			var date_format = '';

			switch (date_format_select) {
				// YYYY-MM-DD
				case 'YYYY-MM-DD':
					date_format = 'yy-mm-dd'
					break;
				// DD-MM-YYYY
				case 'DD-MM-YYYY':
					date_format = 'dd-mm-yy'
					break;
				// Default to MM-DD-YYYY
				default:
					date_format = 'mm-dd-yy'
					break;
			}

			try { $(this).datepicker('destroy'); } catch (e) { /* Prevent JS error if cannot destroy */ }

			$(this).datepicker({
				dateFormat: date_format
			});

		});

		return update_date_format;
	}();

	// Update datepicker whenever format changes
	$(date_format_selects).live('change', function() {
		update_date_format();
	});

	return datepicker;
}();

/**
 * Toggle select groups multiselect
 */
(function() {
	var radios = $('input[name="vwm_surveys_allowed_groups"]');
	var multiselect = $('select[name^="vwm_surveys_select_allowed_groups"]');

	// On page load if group radio input is equal to "SELECT"
	if ( $(radios).filter(':checked').val() === 'SELECT' ) {
		$(multiselect).show();
	}
	// On page load if the group radio input is equal to either "A" or "NULL"
	else {
		$(multiselect).hide();
	}

	// Whenever the group radio input changes
	$(radios).change(function() {
		if ( $(this).val() === 'SELECT' ) {
			$(multiselect).show();
		}
		else {
			$(multiselect).hide();
		}
	});
})();

/**
 * Whenever our survey edit form is submitted check to see if the current
 * element with focus has the class "no_submit". If it does we will not submit
 * the form.
 */
$('form.edit_survey').live('submit', function(e) {
	if ( $(':focus').hasClass('no_submit') ) {
		e.preventDefault();
	}
});

/**
 * Run cleanup function on Ajax complete!
 */
$(document).ajaxComplete(function() {
	ajaxCleanup();
});

/**
 * Run Ajax cleanup functions!
 */
var ajaxCleanup = function ajaxCleanup() {
	datepicker();

	return ajaxCleanup;
}
