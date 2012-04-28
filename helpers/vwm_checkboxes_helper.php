<?php

/**
 * Preprocess checkbox options
 *
 * @param array				Checkbox options
 * @return array			Checkbox options
 */
function vwm_checkboxes_preprocess($options)
{
	// If the options are to be displayed randomally
	if ( $options['order'] == 'random')
	{
		$options['checkboxes'] = shuffle_assoc($options['checkboxes']);
	}

	return $options;
}

/**
 * Validate checkbox input
 *
 * @param int				Question ID
 * @param array				User-provided question data
 * @param array				Question options
 * @return array
 */
function vwm_checkboxes_validate($id, $input, $options)
{
	// Min & max options
	$min_options = $options['min_options'] != '' ? (int)$options['min_options'] : NULL;
	$max_options = $options['max_options'] != '' ? (int)$options['max_options'] : NULL;

	// Get all selected checkboxes
	$selections = $input;

	/**
	 * Loop through each selection
	 *
	 * If an option is of the type "other" its corespoinding text input will get
	 * added to our POST data even if that option was not submitted and the text
	 * field was empty. If this happens, we must remove this from our selections
	 * array.
	 */
	foreach ($selections as $key => $value)
	{
		if ( isset($value['other']) AND !isset($value['option']) )
		{
			unset($selections[ $key ]);
		}
	}

	// Make sure user did not select too many options
	if ( $max_options != NULL AND count($selections) > $max_options )
	{
		$data['errors'][] = 'Max number of options is ' . $max_options;
	}

	// Make sure user did not select too few options
	if ( $min_options != NULL AND count($selections) < $min_options )
	{
		$data['errors'][] = 'Minimum number of options is ' . $min_options;
	}

	$data['selections'] = array();

	// Loop through each user-submitted selection
	foreach ($selections as $selection)
	{
		$option = $selection['option'];
		$other = isset($selection['other']) ? substr(trim($selection['other']), 0, 127) : NULL; // Max 128 characters

		// Make sure this checkbox option is valid
		if (array_key_exists($option, $options['checkboxes']) )
		{
			// Add this selected option to the data array
			$data['selections'][ $option ]['option'] = $option;

			// If the user submitted "other" text with this option, make sure that this option is of the type "other"
			if ($other !== NULL AND $options['checkboxes'][ $option ]['type'] == 'other')
			{
				// Add this other text to the data array
				$data['selections'][ $option ]['other'] = $other;
			}
		}
	}

	return $data;
}

/**
 * Compile checkbox data for insertion into the `exp_vwm_surveys_results` table
 *
 * @param int				Survey ID
 * @param int				Survye submission ID
 * @param array				Question options
 * @param array				User-submitted question data
 * @param array				Current compiled question data
 * @return array
 */
function vwm_checkboxes_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	// Make sure that this question has checkbox data specified
	if ($question_options['checkboxes'])
	{
		/**
		 * Loop through each of our checkbox options specified in our question options
		 *
		 * This is really only needed on the first go around when no compiled
		 * data has been set.
		 *
		 * @todo Figure out a better way to do this...
		 */
		foreach ($question_options['checkboxes'] as $key => $checkbox)
		{
			// Get the current count for this checkbox option (if no data, set count to 0)
			$count = isset( $compiled_data[ $key ]['count'] ) ? $compiled_data[ $key ]['count'] : 0;

			// Update the compiled data with the option count
			$compiled_data[ $key ]['count'] = $count;
		}

		// Loop through each of the checked checkboxes
		foreach ($question_data['selections'] as $selection)
		{
			// Make sure this is a current option (removed options will be ignored)
			if ( isset($compiled_data[ $selection['option'] ]['count']) )
			{
				// Add +1 to our count for this option
				$compiled_data[ $selection['option'] ]['count']++;

				// If this selection has "other" text
				if ( isset($selection['other']) )
				{
					// Add this "other" text to the compiled data
					$compiled_data[ $selection['option'] ]['other'][ $submission_id ] = $selection['other'];
				}
			}

			
		}
	}

	return $compiled_data;
}

// EOF