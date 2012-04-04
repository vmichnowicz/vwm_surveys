<?php

/**
 * Preprocess radio matrix options
 *
 * @param array				Radio matrix options
 * @return array			Radio matrix options
 */
function vwm_radio_matrix_preprocess($options)
{
	// If the x options are to be displayed randomally
	if ( $options['x_order'] === 'random')
	{
		$options['x'] = shuffle_assoc($options['x']);
	}

	// If the y options are to be displayed randomally
	if ( $options['y_order'] === 'random')
	{
		$options['y'] = shuffle_assoc($options['y']);
	}
	
	return $options;
}

/**
 * Validate radio matrix input
 *
 * @param int				Question ID
 * @param array				User-provided question data
 * @param array				Question options
 * @return array
 */
function vwm_radio_matrix_validate($id, $input, $options)
{
	$data = array();

	/**
	 * Make sure count of x & y options are the same (this would not be a user
	 * error, but we should still check for it.
	 */
	if ( count($options['x']) !== count($options['y']) )
	{
		$data['errors'][] = 'Invalid radio matrix question.';
	}

	// If the user did not select values for all options
	if ( count($input) !== count($options['x']) )
	{
		$data['errors'][] = 'Please select values for all radio matrix options.';
	}
	// If the user submitted values for all options (or at least he submitted the correct **amount** of options...)
	else
	{
		/**
		 * Make sure that all submitted options are valid (This should never
		 * happen unless some jerk-off is messing with the survey submission
		 * data)
		 */
		foreach ($input as $y_key => $x_value)
		{
			if ( ( ! array_key_exists($y_key, $options['y']) ) OR ( ! array_key_exists($x_value['option'], $options['x']) ) )
			{
				$data['errors'][] = 'Invalid radio matrix option.';
			}
		}

		/**
		 * Next, make sure that the user selected unique x-values for each data
		 * option. This should probably be handled with JavaScript on the
		 * font-end, but we can not count on that.
		 */
		$unique = array();

		foreach ($input as $option)
		{
			$unique[ $option['option'] ] = $option['option'];
		}

		if ( count($unique) !== count($options['x']) )
		{
			$data['errors'][] = 'Please select unique values for each radio matrix option.';
		}
	}

	// Loop through each submitted input
	foreach ($input as $selection_key => $selection_value)
	{
		// If these options are valid (yeah, we are kinda checking this twice...)
		if ( array_key_exists($selection_key, $options['y']) AND array_key_exists($selection_value['option'], $options['x']) )
		{
			// Add selections to data array
			$data['selections'][ $selection_key ] = $selection_value['option'];
		}
	}

	return $data;
}

/**
 * Compile radio matrix data for insertion into the `exp_vwm_surveys_results` table
 *
 * @param int				Survey ID
 * @param int				Survye submission ID
 * @param array				Question options
 * @param array				User-submitted question data
 * @param array				Current compiled question data
 * @return array
 */
function vwm_radio_matrix_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	// Make sure that this question x & y data
	if ( isset($question_options['x']) AND isset($question_options['y']) )
	{
		/**
		 * Loop through each of our radio matrix options specified in our question options
		 *
		 * This is really only needed on the first go around when no compiled
		 * data has been set.
		 *
		 * @todo Figure out a better way to do this...
		 */
		foreach ($question_options['y'] as $y_key => $y_option)
		{
			foreach ($question_options['x'] as $x_key => $x_value)
			{
				// Get the current count for this checkbox option (if no data, set count to 0)
				$count = isset( $compiled_data[ $y_key ][ $x_key ]['count'] ) ? $compiled_data[ $y_key ][ $x_key ]['count'] : 0;

				// Update the compiled data with the option count
				$compiled_data[ $y_key ][ $x_key ]['count'] = $count;
			}
		}

		// Loop through each of the radio matrix selections
		foreach ($question_data['selections'] as $selection_key => $selection_value)
		{
			if ( isset($compiled_data[ $selection_key ][ $selection_value ]['count']) )
			{
				$compiled_data[ $selection_key ][ $selection_value ]['count']++;
			}
		}
	}

	return $compiled_data;
}

// EOF