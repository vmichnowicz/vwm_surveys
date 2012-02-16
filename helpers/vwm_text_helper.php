<?php

/**
 * Validate text input
 *
 * @param int				Question ID
 * @param text				User-provided question data (in this case it is the text from the text input)
 * @param array				Question options
 * @return type
 */
function vwm_text_validate($id, $input, $options)
{
	// Min & max length options
	$min_length = $options['min_length'] != '' ? (int)$options['min_length'] : NULL;
	$max_length = $options['max_length'] != '' ? (int)$options['max_length'] : NULL;

	// The only user input is from the sole text input
	$data['text'] = trim($input);

	// If we need to check max length
	if ($max_length !== NULL)
	{
		// Make sure our string is not too long
		if ( strlen($data['text']) > $max_length )
		{
			$data['errors'][] = 'Text may not exceede ' . $max_length . ' characters';
		}
	}

	// If we need to check min length
	if ($min_length !== NULL)
	{
		// Make sure our string is not too short
		if ( strlen($data['text']) < $min_length )
		{
			$data['errors'][] = 'Text must be at least ' . $min_length . ' characters';
		}
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
function vwm_text_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	$compiled_data[ $submission_id ] = $question_data['text'];

	return $compiled_data;
}

// EOF