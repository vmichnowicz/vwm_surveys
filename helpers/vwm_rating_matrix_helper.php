<?php

/**
 * Validate rating matrix input
 *
 * @param int				Question ID
 * @param text				User-provided question data
 * @param array				Question options
 * @return array
 */
function vwm_rating_matrix_validate($id, $input, $options)
{
	// Rating options
	$min = isset($options['min']) ? (int)$options['min'] : 0;
	$max = isset($options['max']) ? (int)$options['max'] : NULL;
	$type = $options['type']; // Dropdown, radios, or text

	$data = array();

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
function vwm_rating_matrix_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	return $compiled_data;
}

// EOF