<?php

/**
 * Preprocess radio options
 *
 * @param array				Radio options
 * @return array			Radio options
 */
function vwm_radios_preprocess($options)
{
	// If the options are to be displayed randomally
	if ( $options['order'] == 'random')
	{
		$options['radios'] = shuffle_assoc($options['radios']);
	}

	return $options;
}

/**
 * Validate radios input
 *
 * @param int				Question ID
 * @param array				User-provided question data
 * @param array				Question options
 * @return array
 */
function vwm_radios_validate($id, $input, $options)
{
	// If no option was submitted
	if ( ! isset($input['option']))
	{
		$data['errors'][] = 'No data submitted.';
	}
	else
	{
		$option = $input['option'];
		$other = isset($input['other']) ? substr(trim($input['other']), 0, 127) : NULL; // Max 128 characters

		// Make sure the user-submitted option is valid
		if ( array_key_exists($option, $options['radios']) )
		{
			$data['option'] = $option;

			// If "other" text was submitted make sure this option is of the type "other"
			if ($other != NULL AND $options['radios'][ $option ]['type'] == 'other')
			{
				$data['other'] = $other;
			}
		}
		else
		{
			$data['errors'][] = 'Invalid option selected.';
		}	
	}

	return $data;
}

/**
 * Compile radios data for insertion into the `exp_vwm_surveys_results` table
 *
 * @param int				Survey ID
 * @param int				Survye submission ID
 * @param array				Question options
 * @param array				User-submitted question data
 * @param array				Current compiled question data
 * @return array
 */
function vwm_radios_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	if ($question_options['radios'])
	{
		/**
		 * Loop through each of our radio options specified in our question options
		 *
		 * This is really only needed on the first go around when no compiled
		 * data has been set.
		 *
		 * @todo Figure out a better way to do this...
		 */
		foreach ($question_options['radios'] as $key => $radio)
		{
			$count = isset( $compiled_data[ $key ]['count'] ) ? $compiled_data[ $key ]['count'] : 0;
			$compiled_data[ $key ]['count'] = $count++;
		}

		// Make sure this is a current option (removed options will be ignored)
		if ( isset($compiled_data[ $question_data['option'] ]['count']) )
		{
			$compiled_data[ $question_data['option'] ]['count']++;

			// If this selection has "other" text
			if ( isset($question_data['other']) )
			{
				// Add this "other" text to the compiled data
				$compiled_data[ $question_data['option'] ]['other'][ $submission_id ] = $question_data['other'];
			}
		}

	}
	
	return $compiled_data;
}

// EOF