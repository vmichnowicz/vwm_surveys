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
 * VWM Surveys module
 */
class Vwm_surveys {

	private $EE, $survey_id, $hash;
	private static $question_types = array();
	private static $submission_hashes = NULL;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

		// Make damn sure module path is defined
		$this->EE->load->add_package_path(PATH_THIRD . 'vwm_surveys/');

		// Load lang, models, config, and hepler
		$this->EE->lang->loadfile('vwm_surveys');
		$this->EE->config->load('vwm_surveys');
		$this->EE->load->helper('vwm_surveys');
		$this->EE->load->model(array('vwm_surveys_m', 'vwm_surveys_submissions_m'));

		// Make sure we are able to grab our question types from the config file
		if ( is_array($this->get_question_types()) AND count($this->get_question_types() > 0) )
		{
			// Load config files for all question types
			foreach ( $this->get_question_types() as $slug => $name)
			{
				$this->EE->load->helper('vwm_' . $slug);
			}
		}
		else
		{
			throw new Exception('Unable to load VWM Surveys question types from config file.');
		}
	}

	/**
	 * Get the hash
	 *
	 * If the hash is not yet set, attempt to set it
	 *
	 * @access private
	 * @return null | string
	 */
	private function get_hash()
	{
		if ( ! isset($this->hash) )
		{
			$this->set_hash();
		}

		return $this->hash;
	}

	/**
	 * Set question types from our config file if they have not already been set
	 * and then return them all
	 *
	 * @access private
	 * @return array
	 */
	private function get_question_types()
	{
		// If our question types have not yet been loaded
		if ( ! self::$question_types )
		{
			// Get question types from config file
			self::$question_types = $this->EE->config->item('vwm_surveys_question_types');
		}

		return self::$question_types;
	}

	/**
	 * Get the survey ID
	 *
	 * @access private
	 * @return null | int
	 */
	private function get_survey_id()
	{
		if ( ! isset($this->survey_id) )
		{
			$this->set_survey_id();
		}

		return $this->survey_id;
	}

	/**
	 * Get submission hashes from users cookies and then return them all
	 *
	 * @access private
	 * @return array
	 */
	private function get_submission_hashes()
	{
		// If our submission hashes have not yet been gathered
		if ( self::$submission_hashes == NULL)
		{
			$hashes = array();

			// If the user has this cookie set
			if ( isset($_COOKIE['vwm_surveys_survey_submissions']) )
			{
				if ( $cookie = $_COOKIE['vwm_surveys_survey_submissions'] )
				{
					$hashes = explode(',', $cookie);
				}
			}

			self::$submission_hashes = $hashes;
			
		}

		return self::$submission_hashes;
	}

	/*
	 * Set hash
	 *
	 * @access private
	 * @param string
	 * @return object
	 */
	private function set_hash($param_hash = NULL)
	{
		if ( isset($param_hash) )
		{
			$this->hash = $param_hash;
		}
		else {
			$survey_id_or_hash = $this->EE->TMPL->fetch_param('survey_id_or_hash');
			$hash = strlen( $this->EE->TMPL->fetch_param('hash') );
			$uri_array = $this->EE->uri->segment_array();

			if ( strlen($hash) === 32 && ctype_alnum($hash) )
			{
				$this->hash = $hash;

				goto set_hash;
			}
			elseif ( strlen($survey_id_or_hash) === 32 && ctype_alnum($survey_id_or_hash) )
			{
				$this->hash = $survey_id_or_hash;

				goto set_hash;
			}
			elseif ( ( ! empty($uri_array) ) AND is_array($uri_array) )
			{
				foreach ($uri_array as $segment)
				{
					if ( strlen($segment) === 32 && ctype_alnum($segment) )
					{
						$this->hash = $segment;

						goto set_hash;
					}
				}
			}
		}

		set_hash:

		return $this;
	}

	/*
	 * Set survey ID
	 *
	 * @access private
	 * @param int
	 * @return object
	 */
	private function set_survey_id($param_survey_id = NULL)
	{
		if ( isset($param_survey_id) )
		{
			$this->survey_id = $param_survey_id;
		}
		else
		{
			$survey_id_or_hash = $this->EE->TMPL->fetch_param('survey_id_or_hash');
			$survey_id = $this->EE->TMPL->fetch_param('survey_id');

			if ( ( ! empty($survey_id) AND ctype_digit($survey_id)) )
			{
				$this->survey_id = (int)$survey_id;

				goto set_survey_id;
			}
			elseif ( ( ! empty($survey_id_or_hash) ) AND ctype_digit($survey_id_or_hash) )
			{
				$this->survey_id = (int)$survey_id_or_hash;

				goto set_survey_id;
			}
		}

		// O crap, I think I hear a dino...
		set_survey_id:

		return $this;
	}

	/**
	 * Display all surveys with EE template code
	 *
	 * EE template code {exp:vwm_surveys:surveys}{title}{id}{/exp:vwm:surveys:surveys}
	 * 
	 * @access public
	 * @return string
	 */
	public function surveys()
	{
		$surveys = array();
		$site_id = $this->EE->TMPL->fetch_param('site_id') ? (int)$this->EE->TMPL->fetch_param('site_id') : (int)$this->EE->config->item('site_id');
		$user_progress = $this->EE->vwm_surveys_submissions_m->user_submissions_progress( $this->get_submission_hashes() );
		$user_complete = $this->EE->vwm_surveys_submissions_m->user_submissions_complete( $this->get_submission_hashes() );

		// Loop through all surveys (for this particular site)
		foreach ($this->EE->vwm_surveys_m->get_surveys($site_id) as $survey)
		{
			$surveys[] = array(
				'id' => $survey['id'],
				'site_id' => $survey['site_id'],
				'hash' => $survey['hash'],
				'title' => $survey['title'],
				'num_questions' => $survey['num_questions'],
				'complete' => isset($user_complete[ $survey['id'] ]) ? TRUE : FALSE,
				'progress' => isset($user_progress[ $survey['id'] ]) ? TRUE : FALSE,
				'submission_hash' => isset($user_progress[ $survey['id'] ]) ? $user_progress[ $survey['id'] ] : NULL,
				'created' => $survey['created'],
				'updated' => $survey['updated']
			);
		}

		$variables[0] = array(
			'surveys' => $surveys
		);

		// Make the magic happen
		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
	}

	/**
	 * Build a survey and parse EE template code
	 *
	 * @access public
	 * @return string
	 */
	public function survey()
	{
		$site_id = $this->EE->TMPL->fetch_param('site_id') ? (int)$this->EE->TMPL->fetch_param('site_id') : (int)$this->EE->config->item('site_id');
		$redirect = $this->EE->TMPL->fetch_param('redirect') ? $this->EE->functions->create_url( $this->EE->TMPL->fetch_param('redirect') ) : NULL;

		// The page of the survey we want to display (default to the first page - 0)
		$current_page = 0;

		// See if we have a hash indicating this user has progress for this survey
		if ( $this->get_hash() )
		{
			// If the hash is valid
			if ( $submission = $this->EE->vwm_surveys_submissions_m->get_survey_submission( $this->get_hash() ) )
			{
				$this->set_survey_id( $submission['survey_id'] );
				
				// Make sure this user has not already completed this survey
				if ( $submission['complete'] ) { show_error('You have already completed this survey.'); }

				// If this submission has a member ID we need to make sure that that user is logged in
				if ($submission['member_id'] != 0)
				{
					// If the member ID in the submission data does not match the current logged in member's ID
					if ( $submission['member_id'] != $this->EE->session->userdata('member_id') )
					{
						show_error('You must be logged in to take this survey.');
					}
				}

				// Get the survey details for this survey
				$survey = $this->EE->vwm_surveys_m->get_survey($submission['survey_id'], $site_id);
				$total_pages = count($survey['pages']);
				$current_page = $submission['current_page'];

				// Add in previously submitted data for the current page
				foreach ($survey['pages'][ $current_page ]['questions'] as &$question)
				{
					if ( isset($submission['data'][ $question['id'] ]) )
					{
						$question['data'] = $submission['data'][ $question['id'] ];
					}
				}

				unset($question); // Unset $question as we will be using the same variable later
			}
			// If the user provided an invalid hash string
			else
			{
				show_error('This is not a valid survey.');
			}
		}
		// Since we do not have a user-provided hash we will have to rely on the supplied survey ID
		else
		{
			// Make sure this survey exists
			if ( $survey = $this->EE->vwm_surveys_m->get_survey( $this->get_survey_id(), $site_id ) )
			{
				$total_pages = count($survey['pages']);
			}
			else
			{
				show_error('This is survey does not exist');
			}
		}

		// Prepare question data for our EE template
		$questions = array();

		// If this survey exists and it has at least one page
		if ( isset($survey) AND isset($survey['pages']) AND count($survey['pages']) > 0 )
		{
			// If the current survey page is set and has an array of questions
			if ( isset($survey['pages'][ $current_page ]['questions']) AND is_array($survey['pages'][ $current_page ]['questions']) )
			{
				// Loop through all questions on the current page
				foreach ($survey['pages'][ $current_page ]['questions'] as $question)
				{
					// If a question-specific function exists to preprocess our question options
					if ( function_exists($preprocess_function = 'vwm_' . $question['type'] . '_preprocess') )
					{
						$question['options'] = $preprocess_function($question['options']);
					}

					// Data to be passed to our question view file
					$data = array(
						'id' => $question['id'],
						'page_number' => $current_page,
						'question_number' => $question['custom_order'],
						'options' => $question['options'],
						'data' => isset($question['data']) ? $question['data'] : NULL
					);

					/**
					* EE 2.13 uses an older version of CI that does not allow us to
					* easily load third party views from within our module.
					*
					* '../third_party/vwm_surveys/views/questions_view/vwm_' . $question['type'] . '_view' // EE 2.1
					* 'questions_view/vwm_' . $question['type'] . '_view' // EE 2.2 onwards
					*
					* @todo There really should be a better way to do this...
					*/
					$view_file = $this->EE->config->item('app_version') <= 213 ? '../third_party/vwm_surveys/views/' : '';
					$view_file .= 'questions_view/vwm_' . $question['type'] . '_view';

					$questions[] = array(
						'question_id' => $question['id'],
						'question_title' => $question['title'],
						'question_number' => $question['custom_order'],
						'question' => $this->EE->load->view($view_file, $data, TRUE) // EE 2.1
					);
				}
			}
			else
			{
				// This page contains no questions...
			}

			$variables[0] = array(
				'id' => $survey['id'],
				'title' => $survey['title'],
				'in_allowed_group' => $this->is_allowed_group($survey['allowed_groups']),
				'complete' => $this->is_complete( $this->get_survey_id() ),
				'progress' => $this->is_progress( $this->get_survey_id() ), // Returns a submission hash if there is progress with this survey
				'total_pages' => $total_pages,
				'current_page' => $current_page + 1, // $current_page is zero-index
				'page_title' => $survey['pages'][ $current_page ]['title'],
				'questions' => $questions
			);

			// Set hidden fields, class, and ID for our form
			$form_data = array(
				'id' => 'vwm_surveys_survey_' .  $this->get_survey_id() ,
				'class' => 'vwm_surveys_survey',
				'hidden_fields' => array(
					'ACT' => $this->EE->functions->fetch_action_id('Vwm_surveys', 'submit_survey'),
					'RET' => $this->EE->TMPL->fetch_param('return') ? $this->EE->TMPL->fetch_param('return') : NULL,
					'URI' => $this->EE->uri->uri_string ? $this->EE->uri->uri_string : 'index',
					'save_survey' => $this->EE->functions->fetch_action_id('Vwm_surveys', 'save_survey'),
					'survey_id' =>  $this->get_survey_id() ,
					'current_page' => $current_page,
					'hash' => $this->get_hash(),
					'redirect' => $redirect
				)
			);
		}
		else
		{
			show_error('This survey contains no data.');
		}

		$variables[0] = array(
			'id' => $survey['id'],
			'title' => $survey['title'],
			'in_allowed_group' => $this->is_allowed_group($survey['allowed_groups']),
			'complete' => $this->is_complete( $this->get_survey_id() ),
			'progress' => $this->is_progress( $this->get_survey_id() ), // Returns a submission hash if there is progress with this survey
			'total_pages' => $total_pages,
			'current_page' => $current_page + 1, // $current_page is zero-index
			'page_title' => $survey['pages'][ $current_page ]['title'],
			'page_description' => $survey['pages'][ $current_page ]['description'],
			'questions' => $questions
		);
		
		// Set hidden fields, class, and ID for our form
		$form_data = array(
			'id' => 'vwm_surveys_survey_' . $this->get_survey_id(),
			'class' => 'vwm_surveys_survey',
			'hidden_fields' => array(
				'ACT' => $this->EE->functions->fetch_action_id('Vwm_surveys', 'submit_survey'),
				'RET' => $this->EE->TMPL->fetch_param('return') ? $this->EE->TMPL->fetch_param('return') : NULL,
				'URI' => $this->EE->uri->uri_string ? $this->EE->uri->uri_string : 'index',
				'save_survey' => $this->EE->functions->fetch_action_id('Vwm_surveys', 'save_survey'),
				'survey_id' => $this->get_survey_id(),
				'current_page' => $current_page,
				'hash' => $this->get_hash(),
				'redirect' => $redirect
			)
		);

		// Make the magic happen
		return $this->EE->functions->form_declaration($form_data) . $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables) . '</form>';
	}

	/**
	 * Submit a survey
	 *
	 * @access public
	 * @return mixed
	 */
	public function submit_survey()
	{
		// Get the ID of the survey
		$survey_id = (int)$this->EE->input->post('survey_id');

		// Hash
		$hash = $this->EE->input->post('hash');

		// Redirect
		$redirect = $this->EE->input->post('redirect');

		// Current page
		$current_page = (int)$this->EE->input->post('current_page');

		// The submitted data (grouped by question ID)
		$submitted_data = $this->EE->input->post('vwm_surveys_questions');
		
		// Is the user attempting to save this survey?
		$save_survey = $this->EE->input->post('save') ? TRUE : FALSE;

		$data = array();
		$errors = array();
		$complete = FALSE;
		$total_pages = 0;

		// Make sure this survey exists
		if ( $survey = $this->EE->vwm_surveys_m->get_survey($survey_id) )
		{
			// If the current user groups is not allowed to take this survey
			if ( ! $this->is_allowed_group($survey['allowed_groups']) )
			{
				show_error('You are not allowed to take this survey');
			}

			// Calculate the total number of pages in this survey (one-based index)
			$total_pages = count($survey['pages']);

			// Loop through all questions in the current page
			foreach ($survey['pages'][$current_page]['questions'] as $question)
			{
				// Make sure user submitted this question and it is of the correct type
				if ( isset($submitted_data[ $question['id'] ]['data'][ $question['type'] ]) )
				{
					// Validate the question data using the custom helper functions for this question type
					$validate_function = 'vwm_' . $question['type'] . '_validate';
					$validate = $validate_function($question['id'], $submitted_data[ $question['id'] ]['data'][ $question['type'] ], $question['options']); // I <3 variable variables & variable functions

					$data[ $question['id'] ] = $validate;

					// If the question did not pass validation
					if (isset($data[ $question['id'] ]['errors']))
					{
						// Add the error(s) to our error array
						$errors[ $question['id'] ] = $data[ $question['id'] ]['errors'];

						// Remove errors from our data array
						unset($data[ $question['id'] ]['errors']);
					}
				}
				// If the user did not submit any data for this question
				else
				{
					$errors[ $question['id'] ][] = 'Question not completed.';
				}
			}

			// If no errors were encountered
			if ( count($errors) == 0 )
			{
				// If we are on the last page we can "complete" this survey
				$complete = ( $current_page == ($total_pages - 1) ) ? TRUE : FALSE;

				// If we are not on the last page we can advance a page
				$current_page = ( ($current_page < ($total_pages - 1)) ) ? $current_page + 1 : $current_page;
			}

			// If we have a hash then we want to update an existing survey submission
			if ($hash)
			{
				$submission = $this->EE->vwm_surveys_submissions_m->update_submission($hash, $data, $current_page, $complete);
			}
			/**
			 * If we do not have a hash then we want to create a new survey submission
			 *
			 * Even if there were errors we will still submit the data. This
			 * allows us to populate the form when the user goes back to that
			 * survey page to fix the subission errors
			 */
			else
			{
				$submission = $this->EE->vwm_surveys_submissions_m->insert_submission($survey_id, $data, $current_page, $complete);
				$hash = $submission; // The submit_survey method returns a hash if successful
			}

			// Add this hash to our cookie data
			$this->add_submission_cookie($hash);

			// If the survey creation or update was successful
			if ($submission)
			{
				// Generate a redirect URL
				$hash_redirect_url = $this->hash_redirect_url($survey_id, $hash, $current_page);

				// If the user just wants to save the survey, return the hash redirect url
				if ( $save_survey == TRUE )
				{
					// If this was an AJAX request
					if ($this->EE->input->is_ajax_request())
					{
						$data = array(
							'saved' => TRUE,
							'hash' => $hash,
							'hash_redirect' => $hash_redirect_url,
							'xid' => $this->refresh_xid()
						);

						$this->EE->output->send_ajax_response($data);
					}
					// No AJAX
					else
					{
						// Where is the user being directed after completing this survey
						$completion_redirect = $redirect ? $redirect : $this->EE->config->item('site_url');
								
						$data = array(
							'redirect' => $completion_redirect,
							'link' => array($completion_redirect, 'Continue'),
							'title' => 'Survey Saved',
							'heading' => 'Survey Saved',
							'content' => 'This survey has been successfully saved.'
						);

						$this->EE->output->show_message($data);
					}
				}
				// If user is attempting a survey submission
				else
				{
					// If there were errors
					if ($errors)
					{
						// If this was an AJAX request
						if ($this->EE->input->is_ajax_request())
						{
							$data = array(
								'errors' => $errors,
								'hash' => $hash,
								'hash_redirect' => $hash_redirect_url,
								'xid' => $this->refresh_xid()
							);

							$this->EE->output->send_ajax_response($data, TRUE);
						}
						// No AJAX
						else
						{
							// Get an HTML unordered list of all our errors grouped by question
							$error_list = $this->error_list($errors, $survey['pages'][$current_page]['questions']);

							$data = array(
								'content' => $error_list,
								'hash_redirect' => $hash_redirect_url,
								'heading' => 'Survey Errors',
								'link' => array( $hash_redirect_url, 'Return to previous page' ),
								'redirect' => NULL,
								'title' => 'Survey Errors'
							);

							$this->EE->output->show_message($data, FALSE);
						}
					}
					// If there were no errors
					else
					{
						// If this was an AJAX request
						if ($this->EE->input->is_ajax_request())
						{
							$data = array(
								'complete' => $complete,
								'hash' => $hash,
								'hash_redirect' => $hash_redirect_url,
								'redirect' => $redirect
							);

							$this->EE->output->send_ajax_response($data);
						}
						// No AJAX
						else
						{
							// There are no errors, however, this survey is not complete
							if ( ! $complete)
							{
								// Redirect to next page
								$this->EE->functions->redirect($hash_redirect_url);
							}
							// There are no errors and this survey is complete, GREAT SUCCESS!
							else
							{
								// Where is the user being directed after completing this survey
								$completion_redirect = $redirect ? $redirect : $this->EE->config->item('site_url');

								$data = array(
									'redirect' => $completion_redirect,
									'link' => array($completion_redirect, 'Continue'),
									'title' => 'Survey Complete',
									'heading' => 'Survey Complete',
									'content' => 'This survey has been successfully completed.'
								);

								$this->EE->output->show_message($data);
							}
						}
					}
				}
			}
			else
			{
				show_error('There was a problem submitting this survey.');
			}
		}
		else
		{
			show_error('This survey does not exist.');
		}
	}

	/**
	 * Generate a URL to a particular survey page
	 *
	 * The URL returned will be something like http://example.com/index.php/surveys/survey/2b60536f17565ecca9fa8ca78956ef77/P3
	 *
	 * @access private
	 * @param int				Survey ID
	 * @param string			Submission hash
	 * @param int				Current survey page
	 * @return string
	 */
	private function hash_redirect_url($survey_id, $hash, $current_page)
	{
		// Get all URI segments
		$uri = explode('/', $this->EE->input->post('URI'));

		/**
		 * @todo This *should* work, look into it later
		 * 
		 * $uri = $this->EE->uri->segment_array();
		 */

		// Loop through each URI segment
		foreach ($uri as $key => $segment)
		{
			/**
			 * If the current URI segment is either our hash or our survey ID
			 * then we want to slice our URI array to remove everything
			 * proceeding, for example:
			 *
			 * array("surveys", "survey", "2adbf22c9a7178fc0fa9cac756a0815c", "P3")
			 * array("surveys", "survey")
			 */
			if ( in_array($segment, array($survey_id, $hash)) )
			{
				$uri = array_slice($uri, 0, $key);
				break;
			}
		}

		// Add in hash & current page to the end of our URI array
		$uri[] = $hash;
		$uri[] = 'P' . ($current_page + 1);

		$redirect = implode('/', $uri);

		// Return URL
		return $this->EE->functions->create_url($redirect);
	}

	/**
	 * Generate an HTML unordered list of all our errors
	 *
	 * Only used in non-ajax situations to display survey errors
	 *
	 * @access private
	 * @param array				Errors array grouped by question ID
	 * @param array				Array of all questions
	 * @return string
	 */
	private function error_list($errors, $questions)
	{
		$return = '';

		if ( ! empty($errors) AND is_array($errors) )
		{
			foreach ($errors as $question_id => $question_errors)
			{
				// List question title
				$return .= '<h4>' . htmlentities($questions[ $question_id ]['title'], ENT_QUOTES, 'UTF-8') . '</h4><ul>';

				if ( ! empty($question_errors) AND is_array($question_errors) )
				{
					// List error(s) for this particular question
					foreach ($question_errors as $error)
					{
						$return .= '<li>' . htmlentities($error, ENT_QUOTES, 'UTF-8') . '</li>';
					}
				}

				$return .= '</ul>';

			}
		}
		return $return;
	}

	/**
	 * Add a hash to our cookie
	 *
	 * @access private
	 * @param string			Submission hash
	 * @return void
	 */
	private function add_submission_cookie($hash)
	{
		// If cookie is set, grab it
		$cookie = isset($_COOKIE['vwm_surveys_survey_submissions']) ? $_COOKIE['vwm_surveys_survey_submissions'] : NULL;

		$hashes = $cookie ? explode(',', $cookie) : array();

		// If the current hash is not already in the cookie data
		if ( ! in_array($hash, $hashes) )
		{
			$hashes[] = $hash;

			$hashes = implode(',', $hashes);

			// Add submission hash to our cookie data
			$this->EE->input->set_cookie('vwm_surveys_survey_submissions', $hashes, 31536000);
		}
	}

	/**
	 * See if the current user has progress in the provided survey
	 *
	 * @access private
	 * @param int				Survey ID
	 * @return mixed			FALSE if no progress, submission hash if progress
	 */
	private function is_progress($survey_id)
	{
		// If we have submission hashes and one of those submission hashes shows survey progress
		if ( $this->get_submission_hashes() AND $hash = $this->EE->vwm_surveys_submissions_m->is_progress_by_hashes($survey_id, $this->get_submission_hashes()) )
		{
			return $hash;
		}

		// If the current user is not a guest AND this user has progress in this survey
		if ( $this->EE->session->userdata('member_id') AND $hash = $this->EE->vwm_surveys_submissions_m->is_progress($survey_id) )
		{
			return $hash;
		}

		// This user most likely does not have any progress in this survey
		return FALSE;
	}

	/**
	 * See if the current user has completed the provided survey
	 *
	 * @access private
	 * @param int
	 * @return bool
	 */
	private function is_complete($survey_id)
	{
		// If we have submission hashes and one of those submission hashes shows a complete survey
		if ( $this->get_submission_hashes() AND $this->EE->vwm_surveys_submissions_m->is_complete_by_hashes($survey_id, $this->get_submission_hashes()) )
		{
			return TRUE;
		}

		// If the current user is not a guest AND this user has completed this survey
		if ( $this->EE->session->userdata('member_id') AND $this->EE->vwm_surveys_submissions_m->is_complete($survey_id) )
		{
			return TRUE;
		}

		// This user has most likely not completed this survey
		return FALSE;
	}

	/**
	 * Determine if the current group is allowed to take the current survey
	 *
	 * @access private
	 * @param mixed				"A" (all), NULL (none), or array of allowed groups
	 * @return bool
	 */
	private function is_allowed_group($allowed_groups)
	{
		// By default this group will not be allowed to take this survey
		$all_good_in_da_hood = FALSE;

		// Determine if the current group is allowed to take this survey
		switch($allowed_groups)
		{
			// All groups are allowed to take this survey
			case 'A':
				$all_good_in_da_hood = TRUE;
				break;
			// No groups are allowed to take this survey
			case NULL:
				$all_good_in_da_hood = FALSE;
				break;
			// Select groups are allowed to take this sruvey
			default:
				$all_good_in_da_hood = in_array( $this->EE->session->userdata('group_id'), $allowed_groups );
				break;
		}

		return $all_good_in_da_hood;
	}

	/**
	 * Refresh the XID
	 *
	 * After a user submits a survey that has errors the XID is destroyed. We
	 * must create a new one so the user can successfully submit the survey
	 * again.
	 *
	 * @access private
	 * @return string
	 */
	private function refresh_xid()
	{
		$hash = NULL;

		// If secure forms are enabled
		if ($this->EE->config->item('secure_forms') == 'y')
		{
			$hash = $this->EE->functions->random('encrypt');

			$data = array(
				'date' => $this->EE->localize->now,
				'session_id' => $this->EE->session->userdata('session_id'),
				'hash' => $hash
			);

			$this->EE->db->insert('security_hashes', $data);
		}

		return $hash;
	}

}

// EOF