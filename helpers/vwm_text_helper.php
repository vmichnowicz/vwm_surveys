<?php

/**
 * Validate text input
 *
 * @param int				Question ID
 * @param text				User-provided question data (in this case it is the text from the text input)
 * @param array				Question options
 * @return array
 */
function vwm_text_validate($id, $input, $options)
{
	// Min & max length options
	$min_length = empty($options['min_length']) ? NULL : (int)$options['min_length'];
	$max_length = empty($options['max_length']) ? NULL : (int)$options['max_length'];

	// The only user input is from the sole text input
	$data['text'] = trim($input);

	// Make sure our string is not too long
	if ( isset($max_length) AND strlen($data['text']) > $max_length )
	{
		$data['errors'][] = 'Text may not exceede ' . $max_length . ' characters';
	}

	// Make sure our string is not too short
	if ( isset($min_length) AND strlen($data['text']) < $min_length )
	{
		$data['errors'][] = 'Text must be at least ' . $min_length . ' characters';
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
	if ( isset($question_data['text']) )
	{
		$compiled_data[ $submission_id ] = $question_data['text'];
	}

	return $compiled_data;
}

// EOF