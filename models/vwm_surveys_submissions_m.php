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
 * Model for survey submissions
 */
class Vwm_surveys_submissions_m extends CI_Model {

	private static $user_submissions = NULL;

	/**
	 * Submit a survey for the first time
	 *
	 * @access public
	 * @param int				Survey ID
	 * @param array				Survey data
	 * @param int				The current page
	 * @param bool				Is this survey complete?
	 * @return mixed			If successful return hash, else return FALSE
	 */
	public function insert_submission($survey_id, $data, $current_page = 0, $complete = FALSE)
	{
		$hash = md5( microtime() . $survey_id . $this->session->userdata('session_id') );

		$data = array(
			'hash' => $hash,
			'member_id' => $this->session->userdata('member_id'),
			'survey_id' => $survey_id,
			'data' => json_encode($data),
			'created' => time(),
			'completed' => $complete ? time() : NULL,
			'current_page' => $current_page
		);

		$this->db->insert('vwm_surveys_submissions', $data);

		return $this->db->affected_rows() > 0 ? $hash : FALSE;
	}

	/**
	 * Delete an individual survey submission
	 *
	 * @access public
	 * @param int				Survey submission ID
	 * @return bool
	 */
	public function delete_survey_submission($id)
	{
		if ( isset($id) )
		{
			$this->db->delete('vwm_surveys_submissions', array('id' => $id));
		}
		elseif ( isset($survey_id) )
		{
			$this->db->delete('vwm_surveys_submissions', array('survey_id' => $id));
		}

		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

	/**
	 * Delete all survey submissions for a particular survey
	 *
	 * @access public
	 * @param int				Survey ID
	 * @return bool
	 */
	public function delete_survey_submissions($survey_id)
	{
		$this->db->delete('vwm_surveys_submissions', array('survey_id' => $survey_id));

		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

	/**
	 * Update a previously created survey
	 *
	 * @access public
	 * @param string			Hash
	 * @param array				Survey data
	 * @param int				Current page
	 * @param bool				Is this survey complete?
	 * @return bool
	 */
	public function update_submission($hash, $data, $current_page = 0, $complete = FALSE)
	{
		// Get previously created survey submission
		$query = $this->db->where('hash', $hash)->limit(1)->get('vwm_surveys_submissions');

		// Make sure that this submission exists
		if ($query->num_rows() > 0)
		{
			$row = $query->row();

			// Combine old and new submission data
			$previous_data = json_decode($row->data, TRUE);
			$new_data = $previous_data;

			foreach($data as $question_id => $question_data)
			{
				$new_data[ $question_id ] = $question_data;
			}

			$new_data = json_encode($new_data);

			$data = array(
				'data' => $new_data,
				'updated' => time(),
				'completed' => $complete ? time() : NULL,
				'current_page' => $current_page,
			);

			$this->db
				->where('hash', $hash)
				->update('vwm_surveys_submissions', $data);

			return $this->db->affected_rows() > 0 ? TRUE : FALSE;
		}
		// If this submission does not exist
		else
		{
			return FALSE;
		}
	}

	/**
	 * Get all submissions for a given survey
	 *
	 * If completed_since is set than we will check for all surveys completed
	 * since that time. This will be used to see if the user wants to compile
	 * new results based on the new completed survey submissions.
	 *
	 * @access public
	 * @param int				Survey ID
	 * @param mixed				NULL by default, timestamp if set
	 * @return array
	 */
	public function get_completed_survey_submissions($id, $completed_since = NULL)
	{
		$data = array();

		$this->db
			->order_by('completed', 'ASC')
			->where('completed IS NOT NULL', NULL, TRUE)
			->where('survey_id', $id);

		// If completed_since is set, only grab completed submissions AFTER that date
		if ($completed_since != NULL)
		{
			$this->db->where('completed >=', $completed_since);
		}

		$query = $this->db->get('vwm_surveys_submissions');

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[ $row->id ] = array(
					'member_id' => (int)$row->member_id,
					'survey_id' => (int)$row->survey_id,
					'data' => json_decode($row->data, TRUE),
					'created' => (int)$row->created,
					'updated' => (int)$row->updated,
					'completed' => (int)$row->completed,
					'current_page' => (int)$row->current_page,
					'complete' => (bool)$row->completed
				);
			}

		}
		
		return $data;
	}

	/**
	 * Get all survey submissions (but allow for a search filter)
	 *
	 * @access public
	 * @param array				Array of filters (survey_id, member_id, group_id, created_from, created_to, updated_from, updated_to, complete)
	 * @param string			Order by column
	 * @param string			Order by order ("ASC" or "DESC")
	 * @return array
	 */
	public function get_survey_submissions($filters, $order_by = 'updated', $order_by_order = 'DESC')
	{
		$data = array();

		$order_by = in_array($order_by, array('id', 'member_id', 'survey_id', 'created', 'updated', 'completed', 'complete')) ? $order_by : 'updated';
		$order_by_order = in_array($order_by_order, array('ASC', 'DESC', 'RANDOM')) ? $order_by_order : 'DESC';

		$this->db->order_by($order_by, $order_by_order);

		// Filter survey ID
		if ( isset($filters['survey_id']) AND $filters['survey_id'])
		{
			$this->db->where('survey_id', $filters['survey_id']);
		}

		// Filter member ID
		if ( isset($filters['member_id']) AND $filters['member_id'])
		{
			$this->db->where('member_id', $filters['member_id']);
		}

		// Filter group ID
		if ( isset($filters['group_id']) AND $filters['group_id'])
		{
			$this->db->join('members', 'members.member_id = vwm_surveys_submissions.member_id');
			$this->db->where('group_id', $filters['group_id']);
		}

		// If a valid created from date was provided
		if ( isset($filters['created_from']) AND  strtotime($filters['created_from']) )
		{
			// If a valid created to date was provided as well
			if ( isset($filters['created_to']) AND  strtotime($filters['created_to']) )
			{
				/**
				 * Add one day (86400 seconds) to created_to date
				 *
				 * If user is searching for all surveys created from 1/1/2000 to
				 * 1/1/2000 it should show all surveys created on 1/1/2000.
				 */
				$this->db->where( '`created` BETWEEN ' . strtotime($filters['created_from']) . ' AND ' . (strtotime($filters['created_to']) + 86400), NULL, FALSE );
			}
			// Just a created from date was provided
			else
			{
				$this->db->where( 'created >=', strtotime($filters['created_from']) );
			}
		}

		// If a valid updated from date was provided
		if ( isset($filters['updated_from']) AND  strtotime($filters['updated_from']) )
		{
			// If a valid updated to date was provided as well
			if ( isset($filters['updated_to']) AND  strtotime($filters['updated_to']) )
			{
				/**
				 * Add one day (86400 seconds) to updated_to date
				 *
				 * If user is searching for all surveys updated from 1/1/2000 to
				 * 1/1/2000 it should show all surveys updated on 1/1/2000.
				 */
				$this->db->where( '`updated` BETWEEN ' . strtotime($filters['updated_from']) . ' AND ' . (strtotime($filters['updated_to']) + 86400), NULL, FALSE );
			}
			// Just a updated from date was provided
			else
			{
				$this->db->where( 'updated >=', strtotime($filters['updated_from']) );
			}
		}

		// Filter completed
		if ( isset($filters['complete']) AND $filters['complete'] !== NULL)
		{
			// Show completed subissions
			if ($filters['complete'])
			{
				$this->db->where('completed IS NOT NULL', NULL, TRUE);
			}
			// Show incomplete submissions
			else
			{
				$this->db->where('completed IS NULL', NULL, TRUE);
			}
		}
		
		$query =  $this->db->get('vwm_surveys_submissions');

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[ (int)$row->id ] = array(
					'id' => (int)$row->id,
					'hash' => $row->hash,
					'member_id' => (int)$row->member_id,
					'survey_id' => (int)$row->survey_id,
					'created' => (int)$row->created,
					'updated' => (int)$row->updated,
					'completed' => (int)$row->completed,
					'complete' => (bool)$row->completed
				);
			}
		}

		return $data;
	}

	/**
	 * Get details from a prevously submitted survey
	 *
	 * @access public
	 * @param string			Hash
	 * @param int				Submission ID
	 * @return array
	 */
	public function get_survey_submission($hash = NULL, $submission_id = NULL)
	{
		$data = array();

		// If we have a submission hash
		if ($hash)
		{
			$this->db->where('hash', $hash);
		}
		// If we do not have a submission hash we must have a submission ID
		else
		{
			$this->db->where('id', $submission_id);
		}

		$query = $this->db->limit(1)->get('vwm_surveys_submissions');

		if ($query->num_rows() > 0)
		{
			$row = $query->row();

			$data = array(
				'id' => (int)$row->id,
				'hash' => $row->hash,
				'member_id' => (int)$row->member_id,
				'survey_id' => (int)$row->survey_id,
				'data' => json_decode($row->data, TRUE),
				'created' => (int)$row->created,
				'updated' => (int)$row->updated,
				'completed' => (int)$row->completed,
				'current_page' => (int)$row->current_page,
				'complete' => (bool)$row->completed
			);
		}

		return $data;
	}

	/**
	 * See if a survey has been completed by the current user
	 *
	 * @access public
	 * @param int				Survey ID
	 * @return bool
	 */
	public function is_complete($survey_id)
	{
		$query = $this->db
			->where('survey_id', $survey_id)
			->where('completed IS NOT NULL', NULL, TRUE)
			->where('member_id', $this->session->userdata('member_id'))
			->order_by('updated', 'DESC')
			->limit(1)
			->get('vwm_surveys_submissions');

		if ($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Get all surveys completed or in progress by the current user
	 *
	 * If a member ID is set, use that. If submission hashes are found, use
	 * those. Save results to user_submissions property.
	 *
	 * @access private
	 * @param array				Array of submission hashes
	 * @return void
	 */
	private function get_user_submissions($submission_hahses)
	{
		self::$user_submissions = array();

		$member_id = $this->session->userdata('member_id');

		// Make sure we have a member ID or some submission hashes to check
		if ( $member_id OR $submission_hahses )
		{
			// If we have a member ID
			if ($member_id)
			{
				$this->db
					->where('member_id', $member_id)
					->or_where_in('hash', $submission_hahses ? $submission_hahses : ''); // CI does not like an empty array
			}
			// If we only have submission hashes
			else
			{
				$this->db->where_in('hash', $submission_hahses);
			}

			$query = $this->db
				->order_by('completed, updated, created', 'DESC')
				->get('vwm_surveys_submissions');

			if ($query->num_rows() > 0)
			{
				// Loop through each submission
				foreach ($query->result() as $row)
				{
					// First key is either "completed" or "progress", second is survey ID, and value is the submission hash
					self::$user_submissions[ $row->completed ? 'complete' : 'progress' ][ $row->survey_id ] = $row->hash;
				}
			}
		}
	}

	/**
	 * Get all surveys completed by the current user
	 *
	 * @access public
	 * @param array				Array of submission hashes
	 * @return array
	 */
	public function user_submissions_complete($submission_hahses)
	{
		// If user submissions have not yet been gathered
		if (self::$user_submissions == NULL) { $this->get_user_submissions($submission_hahses); }

		// If we have some completed surveys return an array of their hashes
		return isset(self::$user_submissions['complete']) ? self::$user_submissions['complete'] : array();
	}

	/**
	 * Get all surveys in progress by the current user
	 *
	 * @access public
	 * @param array				Array of submission hashes
	 * @return array
	 */
	public function user_submissions_progress($submission_hahses)
	{
		// If user submissions have not yet been gathered
		if (self::$user_submissions == NULL) { $this->get_user_submissions($submission_hahses); }

		// If we have some surveys in progress return an array of their hashes
		return isset(self::$user_submissions['progress']) ? self::$user_submissions['progress'] : array();
	}

	/**
	 * Check an array of submission hashes to see if it marks a completed survey
	 *
	 * @access public
	 * @param int				Survey ID
	 * @param array				Array of submission hashes
	 * @return bool
	 */
	public function is_complete_by_hashes($survey_id, $submission_hashes)
	{
		$query = $this->db
			->where('survey_id', $survey_id)
			->where('completed IS NOT NULL', NULL, TRUE)
			->where_in('hash', $submission_hashes)
			->limit(1)
			->get('vwm_surveys_submissions');

		if ($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * See if the current user has progress in the provided survey
	 *
	 * @access public
	 * @param int				Survey ID
	 * @return mixed			FALSE if no progress, hash if user has progress
	 */
	public function is_progress($survey_id)
	{
		$query = $this->db
			->where('survey_id', $survey_id)
			->where('completed IS NULL', NULL, TRUE)
			->where('member_id', $this->session->userdata('member_id'))
			->order_by('updated', 'DESC')
			->limit(1)
			->get('vwm_surveys_submissions');

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->hash;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Check an array of submission hashes to see if the current user has any progress in this survey
	 *
	 * @access public
	 * @param int				Survey ID
	 * @param array				Array of submission hashes
	 * @return bool
	 */
	public function is_progress_by_hashes($survey_id, $submission_hashes)
	{
		$query = $this->db
			->where('survey_id', $survey_id)
			->where('completed IS NULL', NULL, TRUE)
			->where_in('hash', $submission_hashes)
			->order_by('updated', 'DESC')
			->limit(1)
			->get('vwm_surveys_submissions');

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->hash;
		}
		else
		{
			return FALSE;
		}
	}

}

// EOF