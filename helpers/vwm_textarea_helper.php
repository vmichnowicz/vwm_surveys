<?php

/**
 * Validate textarea input
 *
 * @param int				Question ID
 * @param string			User-provided question data (in this case it is the text from the textarea)
 * @param array				Question options
 * @return array
 */
function vwm_textarea_validate($id, $input, $options)
{
	// Min & max length options
	$min_length = empty($options['min_length']) ? NULL : (int)$options['min_length'];
	$max_length = empty($options['max_length']) ? NULL : (int)$options['max_length'];

	// The only user input is from the sole text input
	$data['textarea'] = trim($input);

	// Make sure our text is not too long
	if ( isset($max_length) AND strlen($data['textarea']) > $max_length )
	{
		$data['errors'][] = 'Textarea may not exceede ' . $max_length . ' characters';
	}

	// Make sure our text is not too short
	if ( isset($min_length) AND strlen($data['textarea']) < $min_length )
	{
		$data['errors'][] = 'Textarea must have at least ' . $min_length . ' characters';
	}

	return $data;
}

/**
 * Compile text data
 *
 * @param int				Survey ID
 * @param int				Survye submission ID
 * @param array				Question options
 * @param array				User-submitted question data
 * @param array				Current compiled question data
 * @return array
 */
function vwm_textarea_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	if ( isset($question_data['textarea']) )
	{
		$compiled_data[ $submission_id ] = $question_data['textarea'];
	}

	return $compiled_data;
}

// EOF