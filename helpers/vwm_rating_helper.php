<?php

/**
 * Validate rating input
 *
 * @param int				Question ID
 * @param text				User-provided question data
 * @param array				Question options
 * @return array
 */
function vwm_rating_validate($id, $input, $options)
{
	// Rating options
	$min = isset($options['min']) ? (int)$options['min'] : 0;
	$max = isset($options['max']) ? (int)$options['max'] : NULL;
	$type = $options['type']; // Dropdown, radios, or text

	$data = array();

	// The only user input is from the sole date input
	$data['rating'] = trim($input);

	// Check to make sure this is an integer
	if ( ctype_digit($data['rating']) )
	{
		// If value is less than min
		if ( isset($min) AND $data['rating'] < $min )
		{
			$data['errors'][] = sprintf( lang('vwm_surveys_rating_min'), $min);
		}

		// If value is greater than max
		if ( isset($max) AND $data['rating'] > $max )
		{
			$data['errors'][] = sprintf( lang('vwm_surveys_rating_max'), $max);
		}
	}
	else
	{
		$data['errors'][] = lang('vwm_surveys_rating_number');
	}

	return $data;
}

/**
 * Compile date data
 *
 * @param int				Survey ID
 * @param int				Survey submission ID
 * @param array				Question options
 * @param array				User-submitted question data
 * @param array				Current compiled question data
 * @return array
 */
function vwm_rating_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	

	$total = isset($compiled_data['total']) ? (int)$compiled_data['total']  : 0;
	$min = array_key_exists('min', $compiled_data) ? $compiled_data['min']  : NULL;
	$max = array_key_exists('max', $compiled_data) ? $compiled_data['max']  : NULL;


	if ( isset( $question_data['rating'] ) )
	{
		// Convert to integer
		$rating = (int)$question_data['rating'];

		// Total value of all ratings added together
		$compiled_data['total'] = $total + $rating;

		// Min rating submitted
		$compiled_data['min'] = ( $min === NULL OR $rating < $min )  ? $rating : $min;

		// Max rating submitted
		$compiled_data['max'] = ( $max === NULL OR $rating > $max )  ? $rating : $max;

		// If this our first survey submission we don't have any ratings yet in our compiled data
		if ( ! isset($compiled_data['ratings']) ) {
			$compiled_data['ratings'] = array();
		}

		// Get all ratings and put them into an array
		$compiled_data['ratings'][] = $rating;

		// Total number of ratings we have so far
		$count = count($compiled_data['ratings']);
		
		// Middle element
		$middle = floor($count / 2);

		// Sort all ratings from lowest to highest
		sort($compiled_data['ratings'], SORT_NUMERIC);

		// Median (if we have an odd number of submissions)
		$median = $compiled_data['ratings'][$middle];

		// Median (if we have an even number of submissions)
		if ($count % 2 == 0) {
			$median = ($median + $compiled_data['ratings'][$middle - 1]) / 2;
		}

		$compiled_data['median'] = $median;
	}

	return $compiled_data;
}

// EOF