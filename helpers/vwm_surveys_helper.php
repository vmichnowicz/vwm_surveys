<?php if ( ! defined('EXT')) { exit('Invalid file request'); }

/**
 * VWM Surveys
 *
 * @package		VWM Surveys
 * @author		Victor Michnowicz
 * @copyright	Copyright (c) 2011 Victor Michnowicz
 * @license		http://www.apache.org/licenses/LICENSE-2.0.html
 * @link		http://github.com/vmichnowicz/vwm_surveys
 */

// -----------------------------------------------------------------------------


/**
 * Shuffle an array preserving key with value
 * 
 * @param array
 * @return array 
 */
function shuffle_assoc($array)
{
   $keys = array_keys($array);
   shuffle($keys);
   return array_merge(array_flip($keys), $array);
}

/**
 * Encode all special characters & quotes and convert new lines to page breaks
 *
 * @param string				The non-legit string we want to make legit
 * @return string				A legit string
 */
function legit_encode($string)
{
	return nl2br(htmlentities($string, ENT_QUOTES, 'UTF-8'));
}

// EOF