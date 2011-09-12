<?php

/**
 * Validate date input
 *
 * @param int				Question ID
 * @param text				User-provided question data (in this case it is the date from the text input)
 * @param array				Question options
 * @return array
 */
function vwm_date_validate($id, $input, $options)
{	
	// Options
	$format = $options['format'];
	$later_than = $options['later_than'] != '' ? new DateTime( $options['later_than'] ) : NULL;
	$earlier_than = $options['earlier_than'] != '' ? new DateTime( $options['earlier_than'] ) : NULL;

	// The only user input is from the sole date input
	$data['date'] = trim($input);
	
	// Split date string by slashes, dots, & hyphens
	$date_array = preg_split('/[.,\/ -]/', $data['date']);
	
	// We must have three segments for a valid date
	if ( count($date_array) != 3 )
	{
		$data['errors'][] = 'Invalid date provided.';
	}
	// We have at least three segments
	else
	{
		$date = $month = $year = NULL;

		switch ($format)
		{
			// Lots of people
			case 'DD/MM/YYYY':
				$year = $date_array[2];
				$month = $date_array[1];
				$day = $date_array[0];
				$format_conversion = 'j-n-Y';
				break;
			// 'merica!
			case 'MM/DD/YYYY':
				$year = $date_array[2];
				$month = $date_array[0];
				$day = $date_array[1];
				$format_conversion = 'n-j-Y';
				break;
			// Default to YYYY/MM/DD format cuz it is the most logical
			default:
				$year = $date_array[0];
				$month = $date_array[1];
				$day = $date_array[2];
				$format_conversion = 'Y-j-n';
				break;
		}

		// If this is a valid date
		if ( $date_timestamp = mktime(0, 0, 0, $month, $day, $year) )
		{
			// If later_than date is set
			if ($later_than)
			{
				// If date is less than later_than date
				if( $date_timestamp < $later_than->getTimestamp() )
				{
					$data['errors'][] = 'Date must be later than ' . $later_than->format($format_conversion) . '.';
				}
			}
			
			// If earlier_than date is set
			if ($earlier_than)
			{
				// If date is less than later_than date
				if( $date_timestamp > $earlier_than->getTimestamp() )
				{
					$data['errors'][] = 'Date must be earlier than ' . $earlier_than->format($format_conversion) . '.';
				}
			}
			
			// If no error were encountered
			if ( ! $data['errors'] )
			{
				// Store date in YYYY/MM/DD format
				$data['date'] = $year . '-' . $month . '-' . $day;
			}
		}
		// If this is not a valid date
		else
		{
			$data['errors'][] = 'Invalid date provided.';
		}
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
function vwm_date_compile_results($survey_id, $submission_id, $question_options, $question_data, $compiled_data)
{
	/**
	 * If this submission has date data set
	 * 
	 * The only reason this should not be the case is if someone submits a
	 * survey and then an admin adds a date question to the survey - bad admin!
	 */
	if ( isset( $question_data['date'] ) )
	{
		$compiled_data['dates'][ $submission_id ] = $question_data['date'];
	}

	return $compiled_data;
}

// EOF