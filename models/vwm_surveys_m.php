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
 * Model used for database interactions with surveys, questions, and pages tables
 */
class Vwm_surveys_m extends CI_Model {

	protected $EE;

	/**
	 * Model construct
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}

	/**
	 * Parse group data
	 *
	 * Group data comes in three different formats: "A", NULL, or something like
	 * "1,4,6".
	 *
	 * @access public
	 * @param mixed			Group data
	 * @param string		Delimiter (either a comma or a pipe)
	 * @return mixed
	 */
	protected function parse_groups($data, $delimiter = ',')
	{
		// If we are dealing with an array (from POST data on the MCP page)
		if ( is_array($data) )
		{
			// If this is an empty array
			if ( count($data) === 0 )
			{
				return NULL;
			}

			// Loop through array and make sure each element is an integer
			foreach($data as &$element) { intval($element); }

			// Sort array in ascending order
			asort($data);

			// Unique array
			$data = array_unique($data, SORT_NUMERIC);

			return implode($delimiter, $data);
		}
		// All
		elseif ( strtoupper($data) === 'A' || strtoupper($data) === 'ALL')
		{
			return 'A';
		}
		// None
		elseif ($data === NULL || $data === FALSE || strtoupper($data) === 'NULL' )
		{
			return NULL;
		}
		// Select groups
		else
		{
			// Explode into array using delimiter
			$array = explode($delimiter, $data);

			// Loop through array and make sure each element is an integer
			foreach($array as &$element) { intval($element); }

			// Sort array in ascending order
			asort($array);

			// Return unique array
			return array_unique($array, SORT_NUMERIC);
		}
	}

	/**
	 * Get all member groups
	 *
	 * @access public
	 * @return array
	 */
	public function get_groups()
	{
		$query = $this->db->select('group_id, group_title')->get('member_groups');

		foreach ($query->result() as $row)
		{
			$data[ (int)$row->group_id ] = $row->group_title;
		}

		return $data;
	}

	// -------------------------------------------------------------------------

	public function get_members()
	{
		$query = $this->db->select('member_id, group_id, screen_name')->get('members');

		foreach ($query->result() as $row)
		{
			$data[ (int)$row->member_id ] = array(
				'id' => (int)$row->member_id,
				'group_id' => (int)$row->group_id,
				'screen_name' => $row->screen_name
			);
		}

		return $data;
	}

	// -------------------------------------------------------------------------

	/**
	 * Get a complete survey (survey details and all related questions)
	 *
	 * @access public
	 * @param int				Survey ID
	 * @return array
	 */
	public function get_survey($survey_id)
	{
		$data = array();
		
		$survey_details = $this->get_survey_details($survey_id);
		
		if ($survey_details)
		{
			$data = $survey_details;
			$data['pages'] = $this->get_questions_by_page($survey_id);
		}
		
		return $data;
	}
	
	/**
	 * Get all surveys
	 * 
	 * @access public
	 * @return array
	 */
	public function get_surveys()
	{
		// Get all surveys
		$query = $this->db->get('vwm_surveys_surveys');
		
		$data = array();
		
		// If we have at least one survey
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[ (int)$row->id ] = array(
					'id' => (int)$row->id,
					'hash' => $row->hash,
					'title' => $row->title,
					'num_questions' => count($this->get_questions($row->id)),
					'created' => (int)$row->created,
					'updated' => (int)$row->updated
				);
			}
		}
		
		return $data;
	}
	
	/**
	 * Get details for a survey
	 * 
	 * @access public
	 * @param int				Survey ID
	 * @return array
	 */
	public function get_survey_details($survey_id)
	{
		$query = $this->db
			->where('id', $survey_id)
			->limit(1)
			->get('vwm_surveys_surveys');
		
		$data = array();

		if ($query->num_rows() > 0)
		{
			$row = $query->row(); 
			
			$data = array(
				'id' => $survey_id,
				'title' => $row->title,
				'allowed_groups' => explode(',', $row->allowed_groups),
				'hash' => $row->hash,
				'created' =>(int)$row->created,
				'updated' => (int)$row->updated
			);
		}
		
		return $data;
	}

	/**
	 * Update an existing survey
	 *
	 * @access public
	 * @param int				Survey ID
	 * @param string			Survey title
	 * @param array				Allowed member groups
	 * @return bool
	 */
	public function update_survey($id, $title, $allowed_groups)
	{
		$data = array(
			'title' => $title,
			'allowed_groups' => $this->parse_groups($allowed_groups),
			'updated' => $this->EE->localize->now
		);

		$this->db
			->where('id', $id)
			->update('vwm_surveys_surveys', $data);

		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

	/**
	 * Add a new survey
	 *
	 * @access public
	 * @param string			Survey title
	 * @return int				Survey ID
	 */
	public function insert_survey($title)
	{
		$data = array(
			'title' => $title,
			'hash' => md5( $title . microtime() ),
			'created' => time()
		);

		$this->db->insert('vwm_surveys_surveys', $data);

		return $this->db->insert_id();
	}

	/**
	 * Delete a survey and all corresponding survey questions, pages, results, and submissions
	 *
	 * @access public
	 * @param int				Survey ID
	 * @return bool
	 */
	public function delete_survey($id)
	{
		$this->db->delete('vwm_surveys_surveys', array('id' => $id));
		$this->db->delete('vwm_surveys_questions', array('survey_id' => $id));
		$this->db->delete('vwm_surveys_pages', array('survey_id' => $id));
		$this->db->delete('vwm_surveys_submissions', array('survey_id' => $id));
		$this->db->delete('vwm_surveys_results', array('survey_id' => $id));

		return TRUE; // Only if we could use InnoDB and transactions...
	}

	// -------------------------------------------------------------------------
	
	/**
	 * Get all questions in a given survey
	 * 
	 * @access public
	 * @param int				Survey ID
	 * @return array
	 */
	public function get_questions($survey_id)
	{
		// Get all questions for this survey
		$query = $this->db
			->where('survey_id', $survey_id)
			->get('vwm_surveys_questions');
		
		$data = array();
		
		// If this survey has some questions
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return $data;
	}
	
	/**
	 * Get all questions in a given survey and group by page
	 * 
	 * @access public
	 * @param int				Survey ID
	 * @return array
	 */
	public function get_questions_by_page($survey_id)
	{
		$data = array();
		
		// Get all questions for this survey
		$query = $this->db
			->where('survey_id', $survey_id)
			->get('vwm_surveys_questions');
		
		// Grab all page titles for this survey
		$page_titles = $this->get_survey_page_titles($survey_id);
		
		// If we have at least one page
		if ( count($page_titles) > 0 )
		{
			foreach ($page_titles as $page_number => $page_title)
			{
				// Add in page title
				$data[ $page_number ]['title'] = $page_title;
			}
		}
		
		// If this survey has some questions
		if ($query->num_rows() > 0)
		{
			// For each question
			foreach ($query->result_array() as $row)
			{
				// Decode the JSON question options
				$row['options'] = json_decode($row['options'], TRUE);
				
				$data[ (int)$row['page'] ]['questions'][ $row['id'] ] = $row;
			}
		}
		
		return $data;
	}
	
	/**
	 * Update an existing question
	 * 
	 * @access public
	 * @param int				Question ID
	 * @param array				Question data
	 * @return bool
	 */
	public function update_question($id, $data)
	{
		$this->db
			->where('id', $id)
			->update('vwm_surveys_questions', $data);
		
		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}
	
	/**
	 * Reorder all questions on a survey page
	 * 
	 * @access public
	 * @param int				Survey ID
	 * @param int				Page number
	 * @return bool
	 */
	public function update_questions_custom_order($survey_id, $page)
	{
		$query = $this->db
			->where('survey_id', $survey_id)
			->where('page', $page)
			->order_by('custom_order', 'ASC')
			->get('vwm_surveys_questions');
		
		if ($query->num_rows() > 0)
		{
			// Start our custom order at 0
			$i = 0;
			
			// Loop through all of our questions on this page
			foreach ($query->result() as $row)
			{
				// Update the question order (starting at 0 and progressing...)
				$update = $this->db
					->where('id', $row->id)
					->update('vwm_surveys_questions', array('custom_order' => $i));
				
				$i++;
			}
		}
		
		return TRUE;
	}

	/**
	 * Update a questions custom order (move it up or down the page)
	 *
	 * @access public
	 * @param int				Question ID
	 * @param string			Are we moving this question "UP" or "DOWN"?
	 * @return					bool
	 */
	public function update_question_order($id, $move = NULL)
	{
		// Get the data for this particular question
		$question = $this->db->where('id', $id)->get('vwm_surveys_questions')->row();

		// Make sure we are only moving up or down
		$move = in_array(strtoupper($move), array('UP', 'DOWN')) ? strtoupper($move) : 'UP';

		// Get all questions in this page
		$query = $this->db
			->where('survey_id', $question->survey_id)
			->where('page', $question->page)
			->order_by('custom_order', 'ASC')
			->get('vwm_surveys_questions');

		// Make sure this page has at leat two questions
		if ($query->num_rows() > 1)
		{
			// Fill out questions array
			$questions = $query->result_array();
		}
		else
		{
			return FALSE;
		}

		$first_question = $questions[ 0 ];
		$last_question = $questions[ count($questions) - 1 ];

		/**
		 * If we are on the first question and we want to move it "UP"
		 * This means we will place this question at the end of the page
		 */
		if ($first_question['id'] == $question->id AND $move == 'UP')
		{
			$this->db
				->where('id', $question->id)
				->update('vwm_surveys_questions', array('custom_order' => $last_question['custom_order'] + 1));

			// Update order of all questions on this page
			$this->update_questions_custom_order($question->survey_id, $question->page);
		}
		/**
		 * If we are on the last question and we want to move it "DOWN"
		 * This means we will place this question at the beginning of the page
		 */
		elseif ( $last_question['id'] == $question->id AND $move == 'DOWN')
		{
			// Add +1 to the custom order of each question on this page
			$this->db
				->where('survey_id', $question->survey_id)
				->where('page', $question->page)
				->set('custom_order', 'custom_order + 1', FALSE)
				->order_by('custom_order', 'DESC') // Maintain our composite key
				->update('vwm_surveys_questions');

			// Set the custom order of the last question to 0
			$this->db
				->where('id', $question->id)
				->update('vwm_surveys_questions', array('custom_order' => 0));
		}
		// Nothing special here...
		else
		{
			/**
			 * There is a composite key for survey_id, page, and custom_order.
			 * Because of this, no two questions in the same page can share the
			 * same custom_order. So when changing the order we will
			 * temporally set the order to a random integer between 100 and 255
			 */
			$random = rand(100, 255);

			// We are moving this question UP
			if ($move == 'UP')
			{
				// Set the custom order of previous question to a random number
				$this->db
					->where('survey_id', $question->survey_id)
					->where('page', $question->page)
					->where('custom_order', $question->custom_order - 1)
					->update('vwm_surveys_questions', array('custom_order' => $random));

				// Set the custom order of current question
				$this->db
					->where('id', $question->id)
					->update('vwm_surveys_questions', array('custom_order' => $question->custom_order - 1));

				// Reset custom order of previous question
				$this->db
					->where('survey_id', $question->survey_id)
					->where('page', $question->page)
					->where('custom_order', $random)
					->update('vwm_surveys_questions', array('custom_order' => $question->custom_order));
			}
			// We are moving this question down
			else
			{
				// Set the custom order of previous question to a random number
				$this->db
					->where('survey_id', $question->survey_id)
					->where('page', $question->page)
					->where('custom_order', $question->custom_order + 1)
					->update('vwm_surveys_questions', array('custom_order' => $random));

				// Set the custom order of current question
				$this->db
					->where('id', $question->id)
					->update('vwm_surveys_questions', array('custom_order' => $question->custom_order + 1));

				// Reset custom order of previous question
				$this->db
					->where('survey_id', $question->survey_id)
					->where('page', $question->page)
					->where('custom_order', $random)
					->update('vwm_surveys_questions', array('custom_order' => $question->custom_order));
			}
		}

		return TRUE;
	}
	
	/**
	 * Add a new question to a survey
	 * 
	 * @access public
	 * @param array				Question data
	 * @return int
	 */
	public function insert_question($data)
	{
		$page = (int)$data['page'];
		$survey_id = (int)$data['survey_id'];

		// Get number of questions on the current page
		$num_questions_on_page = $this->db
			->select('COUNT( `id`) AS num_questions_on_page', FALSE)
			->where('page', $page)
			->where('survey_id', $survey_id)
			->get('vwm_surveys_questions')
			->row()->num_questions_on_page;

		// We can only have 100 questions on a page
		if ($num_questions_on_page < 100)
		{
			// Insert new question
			$this->db->insert('vwm_surveys_questions', $data);

			// Return question ID
			return $this->db->insert_id();
		}

		return FALSE;
	}
	
	/**
	 * Delete a question
	 * 
	 * @access public
	 * @param int				Question ID
	 * @param int				Survey ID
	 * @param int				Page number
	 * @return bool
	 */
	public function delete_question($id, $survey_id = NULL, $page = NULL)
	{
		// Make sure we have a valid page number and survey ID
		if ( $page == NULL OR $survey_id == NULL )
		{
			$data = $this->db
				->where('id', $id)
				->limit(1)
				->get('vwm_surveys_questions')
				->row();
			
			$page = $data->page;
			$survey_id = $data->survey_id;
		}
		
		// Delete question
		$this->db->where('id', $id)->limit(1)->delete('vwm_surveys_questions');

		// Reorder all the questions in this survey page
		$this->update_questions_custom_order($survey_id, $page);
		
		return TRUE;
	}

	// -------------------------------------------------------------------------

	/**
	 * Get all page titles for a given survey
	 *
	 * @access public
	 * @param int				Survey ID
	 * @return array
	 */
	public function get_survey_page_titles($survey_id)
	{
		$data = array();

		$query = $this->db
			->where('survey_id', $survey_id)
			->get('vwm_surveys_pages');

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[ (int)$row->page ] = $row->title;
			}
		}

		return $data;
	}
	
	/**
	 * Insert a page into a survey
	 * 
	 * @access public
	 * @param int				Survey ID
	 * @param string			Page title
	 * @return mixed			If the page insertion was successful return the page number, else return FALSE
	 */
	public function insert_page($id, $title)
	{
		// Get the last page for this survey
		$query = $this->db
			->where('survey_id', $id)
			->order_by('page', 'DESC')
			->limit(1)
			->get('vwm_surveys_pages');
		
		// If this survey has at least one page
		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$page = $row->page + 1;
			
			$data = array(
				'survey_id' => $id,
				'title' => $title,
				'page' => $page
			);
		
			$this->db->insert('vwm_surveys_pages', $data);
			
			// If this insert was successful return the new page number, else return FALSE
			return $this->db->affected_rows() > 0 ? $page : FALSE;
		}
		// If this survey has no existing pages
		else
		{
			$data = array(
				'survey_id' => $id,
				'title' => $title
			);
			
			$this->db->insert('vwm_surveys_pages', $data);
			
			// If this insert was successful return 0, else return FALSE
			return $this->db->affected_rows() > 0 ? 0 : FALSE;
		}
	}
	
	/**
	 * Update a survey page
	 * 
	 * @access public
	 * @param int				Survey ID
	 * @param int				Page number
	 * @param string			Page title
	 * @return bool
	 */
	public function update_page($survey_id, $page, $title)
	{	
		$this->db
			->where('survey_id', $survey_id)
			->where('page', $page)
			->update( 'vwm_surveys_pages', array('title' => $title) );
		
		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}
	
	/**
	 * Delete a page (and all corresponding questions)
	 * 
	 * @access public
	 * @param int				Survey ID
	 * @param string			Page number
	 * @return bool
	 */
	public function delete_page($survey_id, $page)
	{
		$this->db->delete('vwm_surveys_pages', array('survey_id' => $survey_id, 'page' => $page));
		$this->db->delete('vwm_surveys_questions', array('survey_id' => $survey_id, 'page' => $page));
		
		return TRUE; // Only if we colud use InnoDB and transactions...
	}

}

// EOF