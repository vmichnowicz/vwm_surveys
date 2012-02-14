<?php

/**
 * Validate rating input
 *
 * @param int				Question ID
 * @param text				User-provided question data (in this case it is the date from the text input)
 * @param array				Question options
 * @return array
 */
function vwm_rating_validate($id, $input, $options)
{
	// Rating options
	$max = isset($options['max']) ? (int)$options['max'] : 0;
	$type = $options['type'];

	// The only user input is from the sole date input
	$data['rating'] = trim($input);

	// Check to make sure this is a number
	if ( is_numeric($data['rating']) )
	{
		// If this is not an integer
		if ( $data['rating'] !== (string)(int)$data['rating'] )
		{
			$data['errors'][] = 'Rating must be a whole number (integer).';
		}
	}
	else
	{
		$data['errors'][] = 'Rating must be a number.';
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

	if ( isset( $question_data['rating'] ) )
	{
		$compiled_data['total'] = $total + (int)$question_data['rating'];

	}

	return $compiled_data;
}

// EOF