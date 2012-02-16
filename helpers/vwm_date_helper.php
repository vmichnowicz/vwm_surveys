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
	// Date format
	$format = isset($options['format']) ? $options['format'] : NULL;

	// Determine format conversion
	switch ($format)
	{
		// Lots of people
		case 'DD-MM-YYYY':
			$string_format = 'd#m#Y';
			$display_format = 'd-n-Y';
			break;
		// 'merica!
		case 'MM-DD-YYYY':
			$string_format = 'm#d#Y';
			$display_format = 'n-d-Y';
			break;
		// Default to YYYY-MM-DD format cuz it is the most logical
		default:
			$string_format = 'Y#m#d';
			$display_format = 'Y-n-d';
			$format = 'YYYY-MM-DD'; // Make damn sure format is YYYY-MM-DD
			break;
	}

	// Options
	$later_than = ( isset($options['later_than']) AND $options['later_than'] != '' ) ? DateTime::createFromFormat($string_format, $options['later_than'] ) : NULL;
	$earlier_than = ( isset($options['earlier_than']) AND $options['earlier_than'] != '' ) ? DateTime::createFromFormat($string_format, $options['earlier_than'] ) : NULL;

	// The only user input is from the sole date input
	$data['date'] = trim($input);

	/**
	 * Attempt to create date object from user provided input (createFromFormat
	 * returns FALSE on error)
	 *
	 * @todo PHP is being a sneaky bastard here. It considers a date of
	 * 2001-01-50 to be a valid date. It takes the extra days and moves it to
	 * the next month. Figure out a *clean* way of reporting this as an invalid
	 * date...
	 */
	if ( ! $date_obj = DateTime::createFromFormat($string_format, $data['date']) )
	{
		$data['errors'][] = "Invalid $format date provided.";
	}

	// If later_than date is set
	if ($later_than)
	{
		// If date is less than later_than date
		if( $date_obj < $later_than )
		{
			$data['errors'][] = 'Date must be later than ' . $later_than->format($display_format) . '.';
		}
	}

	// If earlier_than date is set
	if ($earlier_than)
	{
		// If date is less than later_than date
		if( $date_obj > $earlier_than )
		{
			$data['errors'][] = 'Date must be earlier than ' . $earlier_than->format($display_format) . '.';
		}
	}

	// If no error were set
	if ( ! isset($data['errors']) )
	{
		// Store date in YYYY-MM-DD format
		$data['date'] = $date_obj->format('Y-m-d');
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