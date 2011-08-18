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
 * Model for survey results
 */
class Vwm_surveys_results_m extends CI_Model {

	/**
	 * Get survey results for a given survey
	 *
	 * @access public
	 * @param int				Survey ID
	 * @return array
	 */
	public function get_survey_results($id)
	{
		$data = array();

		$query = $this->db->where('survey_id', $id)->limit(1)->get('vwm_surveys_results');

		if ($query->num_rows() > 0)
		{
			$row = $query->row();

			$data = array(
				'data' => json_decode($row->data, TRUE),
				'num_submissions' => (int)$row->num_submissions,
				'compiled' => (int)$row->compiled
			);
		}

		return $data;
	}

	/**
	 * Insert survey results
	 *
	 * @access public
	 * @param int				Survey ID
	 * @param array				Survey results
	 * @param int				Total number of completed survey submissions for this survey
	 * @return bool
	 */
	public function insert_survey_results($id, $data, $num_submissions)
	{
		// See if we already have results for this survey
		$results = $this->db
			->where('survey_id', $id)
			->limit(1)
			->get('vwm_surveys_results')
			->row();

		// If we do not have any results for this survey
		if ( ! $results )
		{
			$data = array(
				'survey_id' => $id,
				'data' => json_encode($data),
				'num_submissions' => $num_submissions,
				'compiled' => time()
			);

			$this->db->insert('vwm_surveys_results', $data);

			return $this->db->affected_rows() > 0 ? TRUE : FALSE;
		}
		// If we already have results
		else
		{
			return $this->update_survey_results($id, $data, $num_submissions); // Returns either TRUE or FALSE
		}
	}

	/**
	 * Update survey results
	 *
	 * @access private
	 * @param int				Survey ID
	 * @param array				Survey results
	 * @param int				Total number of completed survey submissions for this survey
	 * @return bool
	 */
	private function update_survey_results($id, $data, $num_submissions)
	{
		$data = array(
			'data' => json_encode($data),
			'num_submissions' => $num_submissions,
			'compiled' => time()
		);

		$this->db->where('survey_id', $id)->update('vwm_surveys_results', $data);

		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

}

// EOF