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
 * VWM Surveys
 */
class Vwm_surveys_mcp {

	private $EE;
	private static $question_types = array();

	/**
	 * Load all of our models, helpers, config file, and add JS and CSS to page
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

		// Load models
		$this->EE->load->model(array('vwm_surveys_m', 'vwm_surveys_submissions_m', 'vwm_surveys_results_m'));

		// Load config
		$this->EE->config->load('vwm_surveys');

		// Add JavaScript & CSS
		$this->EE->cp->add_to_head('<script type="text/javascript">EE.CP_URL = "' . $this->EE->config->item('cp_url') . '";</script>');
		$this->EE->cp->load_package_js('mcp');
		$this->EE->cp->load_package_css('mcp');

		// Load main helper
		$this->EE->load->helper('vwm_surveys');

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
			// Get question types from config file (system/expressionengine/third_party/vwm_surveys/config/vwm_surveys.php)
			self::$question_types = $this->EE->config->item('vwm_surveys_question_types');
		}

		return self::$question_types;
	}

	/**
	 * Module CP page
	 * 
	 * @access public
	 * @return string
	 */
	public function index()
	{
		// Page title
		$this->EE->view->cp_page_title = $this->EE->lang->line('vwm_surveys_module_name');

		// Top-right navigation buttons
		$this->EE->cp->set_right_nav(array(
			'vwm_surveys_add_survey' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=add_survey',
			'vwm_surveys_survey_submissions' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submissions'
		));

		// Get current site ID
		$site_id = $this->EE->config->item('site_id');

		// Data to get passed to view
		$data = array(
			'surveys' => $this->EE->vwm_surveys_m->get_surveys($site_id), // All surveys for this current site
		);

		return $this->EE->load->view('mcp_index', $data, TRUE);
	}

	/**
	 * View an individual survey submission CP page
	 *
	 * @access public
	 * @return string
	 */
	public function survey_submission()
	{
		// Title
		$this->EE->view->cp_page_title = $this->EE->lang->line('vwm_surveys_survey_submission');

		// Top-right navigation buttons
		$this->EE->cp->set_right_nav(array(
			'vwm_surveys_add_survey' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=add_survey',
			'vwm_surveys_survey_submissions' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submissions'
		));

		// Grab submission hash and/or submission ID
		$hash = $this->EE->input->get('hash');
		$submission_id = $this->EE->input->get('submission_id');

		// If this is a valid survey submission
		if ( $submission = $this->EE->vwm_surveys_submissions_m->get_survey_submission($hash, $submission_id) )
		{
			$survey = $this->EE->vwm_surveys_m->get_survey( $submission['survey_id'] );

			// Breadcrumbs
			$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys', lang('vwm_surveys_module_name'));
			$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=edit_survey' . AMP . 'survey_id=' . $survey['id'], htmlspecialchars($survey['title'], ENT_QUOTES, 'UTF-8'));

			$data = array(
				'survey' => $survey,
				'submission' => $submission
			);

			return $this->EE->load->view('mcp_survey_submission', $data, TRUE);
		}
	}

	/**
	 * View all survey submissions CP page
	 *
	 * Optionally, we can filter survey submissions based on an array of filters
	 * from our POST data.
	 *
	 * @access public
	 * @return string
	 */
	public function survey_submissions()
	{
		// Title
		$this->EE->view->cp_page_title = $this->EE->lang->line('vwm_surveys_survey_submissions');

		// Breadcrumb
		$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys', lang('vwm_surveys_module_name'));

		// Top-right navigation buttons
		$this->EE->cp->set_right_nav(array(
			'vwm_surveys_add_survey' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=add_survey',
			'vwm_surveys_survey_submissions' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submissions'
		));

		// jQuery UI plugins
		$this->EE->cp->add_js_script( array('ui' => array('datepicker')) );

		// Filters
		$filters = array(
			'survey_id' => $this->EE->input->post('filter_survey_id') ? (int)$this->EE->input->post('filter_survey_id') : NULL,
			'member_id' => $this->EE->input->post('filter_member_id') ? (int)$this->EE->input->post('filter_member_id') : NULL,
			'group_id' => $this->EE->input->post('filter_group_id') ? (int)$this->EE->input->post('filter_group_id') : NULL,
			'created_from' => $this->EE->input->post('filter_created_from') ? $this->EE->input->post('filter_created_from') : NULL,
			'created_to' => $this->EE->input->post('filter_created_to') ? $this->EE->input->post('filter_created_to') : NULL,
			'updated_from' => $this->EE->input->post('filter_updated_from') ? $this->EE->input->post('filter_updated_from') : NULL,
			'updated_to' => $this->EE->input->post('filter_updated_to') ? $this->EE->input->post('filter_updated_to') : NULL,
			'complete' => $this->EE->input->post('filter_complete') == '' ? NULL : (bool)$this->EE->input->post('filter_complete')
		);

		// Sort order
		$order_by = $this->EE->input->post('order_by');
		$order_by_order = $this->EE->input->post('order_by_order');

		$data = array(
			'action_url'=> 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=survey_submissions',
			'submissions' => $this->EE->vwm_surveys_submissions_m->get_survey_submissions($filters, $order_by, $order_by_order),
			'surveys' => $this->EE->vwm_surveys_m->get_surveys(),
			'members' => $this->EE->vwm_surveys_m->get_members(),
			'groups' => $this->EE->vwm_surveys_m->get_groups(),
			'filters' => $filters,
			'order_by' => $order_by,
			'order_by_order' => $order_by_order
		);

		return $this->EE->load->view('mcp_survey_submissions', $data, TRUE);
	}
	
	/**
	 * Add a survey CP page
	 * 
	 * @access public
	 * @return string
	 */
	public function add_survey()
	{
		// Get current site ID
		$site_id = $this->EE->config->item('site_id');

		// If this page was POSTed to with a valid title
		if ( $title = trim($this->EE->input->post('title')) )
		{
			// Add survey to database and get its ID back
			$survey_id = $this->EE->vwm_surveys_m->insert_survey($title, $site_id);

			if ( $clone_id = $this->EE->input->post('clone_id') )
			{
				$this->EE->vwm_surveys_m->clone_survey($survey_id, $clone_id);
			}

			// Great success!
			$this->EE->session->set_flashdata('message_success', 'Survey added!');

			// Redirect to main module page where all surveys are visible
			$this->EE->functions->redirect(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys');
		}
		// If this page was not POSTed to
		else
		{
			// Page title
			$this->EE->view->cp_page_title = $this->EE->lang->line('vwm_surveys_add_survey');

			// Add breadcrumb
			$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys', lang('vwm_surveys_module_name'));

			// Top-right navigation buttons
			$this->EE->cp->set_right_nav(array(
				'vwm_surveys_add_survey' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=add_survey',
				'vwm_surveys_survey_submissions' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submissions'
			));

			$data = array(
				'action_url' => 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=add_survey',
				'surveys' => $this->EE->vwm_surveys_m->get_surveys($site_id)
			);

			return $this->EE->load->view('mcp_add_survey', $data, TRUE);
		}
	}

	/**
	 * Edit a survey CP page
	 * 
	 * @access public
	 * @return string
	 */
	public function edit_survey()
	{
		// Get survey ID and survey data
		$survey_id = $this->EE->input->get('survey_id');
		$survey = $this->EE->vwm_surveys_m->get_survey_details($survey_id);

		// Title
		$this->EE->view->cp_page_title = $this->EE->lang->line('vwm_surveys_edit_survey');

		// Breadcrumbs
		$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys', lang('vwm_surveys_module_name'));
		$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=edit_survey' . AMP . 'survey_id=' . $survey_id, htmlspecialchars($survey['title'], ENT_QUOTES, 'UTF-8'));

		// Top-right navigation buttons
		$this->EE->cp->set_right_nav(array(
			'vwm_surveys_add_survey' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=add_survey',
			'vwm_surveys_survey_submissions' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submissions'
		));

		// jQuery UI
		$this->EE->cp->add_js_script(
			array('ui' => array(
				'core', 'datepicker'
			)
		));

		// Load JS for all question types
		foreach ($this->get_question_types() as $slug => $name)
		{
			$this->EE->cp->load_package_js('vwm_' . $slug);
		}

		// See if this survey has any submissions
		$submissions = $this->EE->vwm_surveys_submissions_m->get_survey_submissions(array('survey_id' => $survey_id));

		$data = $survey;
		$data['action_url'] = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=update_survey';
		$data['pages'] = $this->EE->vwm_surveys_m->get_questions_by_page($survey_id);
		$data['question_types'] = $this->get_question_types();
		$data['member_groups'] = $this->EE->vwm_surveys_m->get_groups();
		$data['has_submissions'] = $this->EE->vwm_surveys_submissions_m->get_survey_submissions(array('survey_id' => $survey_id)) ? TRUE : FALSE; // See if this survey has any submissions

		return $this->EE->load->view('mcp_edit_survey', $data, TRUE);
	}

	/**
	 * Update a survey CP page
	 * 
	 * This page gets POSTed to and then updates our survey. For allowed groups
	 * we first want to see if the user is attempting to make the survey group
	 * setting "A" (all) or "NULL" (none). If the group setting is not either of
	 * those values then we will use the input from the select members input.
	 * 
	 * @access public
	 * @return void
	 */
	public function update_survey()
	{
		$survey_id = $this->EE->input->post('vwm_surveys_id');
		$title = trim($this->EE->input->post('vwm_surveys_title'));
		$allowed_groups = in_array( $this->EE->input->post('vwm_surveys_allowed_groups'), array('A', 'NULL') ) ? $this->EE->input->post('vwm_surveys_allowed_groups') : $this->EE->input->post('vwm_surveys_select_allowed_groups');
		$pages = $this->EE->input->post('vwm_surveys_pages');

		// If we have a title and page data
		if ($title AND $pages)
		{
			// Update the survey title and last updated date
			$this->EE->vwm_surveys_m->update_survey($survey_id, $title, $allowed_groups);

			// Loop through each page
			foreach ($pages as $page_number => $page)
			{
				// Get title for this page and attempt to update it
				$page_title = trim($page['title']);

				$page_description = trim($page['description']);

				$this->EE->vwm_surveys_m->update_page($survey_id, $page_number, $page_title, $page_description);

				// Make sure this page has at least one question
				if (isset($page['questions']))
				{
					// Loop through each question in this page
					foreach ($page['questions'] as $question)
					{
						// If this is an existing question
						if ($question['id'])
						{
							$data = array(
								'title' => trim($question['title']),
								'type' => $question['type'],
								'custom_order' => (int)$question['custom_order'],
								'options' => isset($question['options']) ? json_encode($question['options']) : NULL
							);

							$this->EE->vwm_surveys_m->update_question($question['id'], $data);
						}
						// If this is a new question
						else
						{
							// Set new question properties in model
							$this->EE->vwm_surveys_m
								->set_survey_id($survey_id)
								->set_page($page_number)
								->set_question_title( trim($question['title']) )
								->set_question_type( $question['type'] )
								->set_question_custom_order( $question['custom_order'] )
								->set_question_options( isset($question['options']) ? json_encode($question['options']) : NULL );

							$this->EE->vwm_surveys_m->insert_question($data);
						}
					}
				}
			}
		}
		
		// Redirect user back to previous page after adding file
		$this->EE->functions->redirect(BASE . AMP . $this->EE->input->post('redirect_to'));
	}
	
	/**
	 * Delete a survey
	 * 
	 * @access public
	 * @return void
	 */
	public function delete_survey()
	{
		$survey_id = (int)$this->EE->input->get('survey_id');

		if ($survey_id)
		{
			$this->EE->vwm_surveys_m->delete_survey($survey_id);

			// Great success!
			$this->EE->session->set_flashdata('message_success', 'Survey removed!');
		}
		else
		{
			// Survey not deleted
			$this->EE->session->set_flashdata('message_failure', 'Survey not removed - try passing a survey ID next time smartass.');
		}

		$this->EE->functions->redirect(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys');
	}

	/**
	 * Delete an individual survey submission
	 *
	 * @access public
	 * @return void
	 */
	public function delete_survey_submission()
	{
		$survey_id = (int)$this->EE->input->get('id');

		if ($survey_id)
		{
			if ( $this->EE->vwm_surveys_submissions_m->delete_survey_submission($survey_id) === TRUE )
			{
				// Great success!
				$this->EE->session->set_flashdata('message_success', 'Survey submission removed.');
			}
			else
			{
				// Survey submission not deleted
				$this->EE->session->set_flashdata('message_failure', 'Survey submission not removed.');
			}
		}

		$this->EE->functions->redirect(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=survey_submissions');
	}

	/**
	 * Delete all survey submissions associated with a given survey
	 *
	 * @access public
	 * @return void
	 */
	public function delete_survey_submissions()
	{
		$survey_id = (int)$this->EE->input->get('survey_id');

		if ($survey_id)
		{
			if ( $this->EE->vwm_surveys_submissions_m->delete_survey_submissions($survey_id) === TRUE )
			{
				// Great success!
				$this->EE->session->set_flashdata('message_success', 'Survey submissions removed.');
			}
			else
			{
				// Survey submission not deleted
				$this->EE->session->set_flashdata('message_failure', 'Survey submissions not removed.');
			}
		}

		$this->EE->functions->redirect(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys');
	}

	/**
	 * View results for a given survey CP page
	 *
	 * @access public
	 * @return string
	 */
	public function survey_results()
	{
		// Page title
		$this->EE->view->cp_page_title = $this->EE->lang->line('vwm_surveys_survey_results');

		// Top-right navigation buttons
		$this->EE->cp->set_right_nav(array(
			'vwm_surveys_add_survey' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=add_survey',
			'vwm_surveys_survey_submissions' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submissions'
		));

		// Get this surveys ID
		$survey_id = (int)$this->EE->input->get('survey_id');

		// If this survey exists and it has compiled results
		if ( $survey = $this->EE->vwm_surveys_m->get_survey($survey_id) AND $results = $this->EE->vwm_surveys_results_m->get_survey_results($survey_id) )
		{
			// Add breadcrumbs
			$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys', lang('vwm_surveys_module_name'));
			$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=edit_survey' . AMP . 'survey_id=' . $survey_id, htmlspecialchars($survey['title'], ENT_QUOTES, 'UTF-8'));

			// Check for any completed survey submissions since this survey was compiled
			$new_completed_submissions = count($this->EE->vwm_surveys_submissions_m->get_completed_survey_submissions($survey_id, $results['compiled']));

			$data = array(
				'survey' => $survey,
				'results' => $results,
				'new_completed_submissions' => $new_completed_submissions
			);

			return $this->EE->load->view('mcp_survey_results', $data, TRUE);
		}
	}

	/**
	 * Compile results for a given survey CP page
	 *
	 * @access public
	 * @return string
	 */
	public function compile_survey_results()
	{
		$survey_id = (int)$this->EE->input->get('survey_id');
		$survey = $this->EE->vwm_surveys_m->get_survey($survey_id);
		$submissions = $this->EE->vwm_surveys_submissions_m->get_completed_survey_submissions($survey_id);

		// Title
		$this->EE->view->cp_page_title = $this->EE->lang->line('vwm_surveys_compile_survey_results');

		// Breadcrumb
		$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys', lang('vwm_surveys_module_name'));
		$this->EE->cp->set_breadcrumb(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=edit_survey' . AMP . 'survey_id=' . $survey_id, htmlspecialchars($survey['title'], ENT_QUOTES, 'UTF-8'));

		// Top-right navigation buttons
		$this->EE->cp->set_right_nav(array(
			'vwm_surveys_add_survey' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=add_survey',
			'vwm_surveys_survey_submissions' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP .'method=survey_submissions'
		));

		$compiled_data = array();

		// Loop through each survey page
		foreach ($survey['pages'] as $page)
		{
			// Loop through each question on the page
			foreach ($page['questions'] as $question)
			{
				$compile_function = 'vwm_' . $question['type'] . '_compile_results';

				// Loop through each survey submission
				foreach ($submissions as $submission_id => $submission)
				{
					// If there is no compiled data for this question yet, set it to an empty array
					$compiled_data[ $question['id'] ] = isset($compiled_data[ $question['id'] ]) ? $compiled_data[ $question['id'] ] : array();

					// If there is no submission data for this question
					$submission['data'][ $question['id'] ] = isset($submission['data'][ $question['id'] ]) ? $submission['data'][ $question['id'] ] : array();

					// Run the compile function
					$compiled_data[ $question['id'] ] = $compile_function($survey_id, $submission_id, $question['options'], $submission['data'][ $question['id'] ], $compiled_data[ $question['id'] ]);
				}
			}
		}

		$this->EE->vwm_surveys_results_m->insert_survey_results($survey_id, $compiled_data, count($submissions));

		$data['survey_results_url'] = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=vwm_surveys' . AMP . 'method=survey_results' . AMP . 'survey_id=' . $survey['id'];

		return $this->EE->load->view('mcp_compile_survey_results', $data, TRUE);
	}
	
	/**
	 * Return HTML for question type
	 * 
	 * @access public
	 * @return string
	 */
	public function add_question()
	{
		// Get data for our new question
		$survey_id = (int)$this->EE->input->get('survey_id');
		$page = (int)$this->EE->input->get('page_number');
		$question_type = array_key_exists($this->EE->input->get('type'), $this->get_question_types()) ? $this->EE->input->get('type') : 'text';
		$question_custom_order = (int)$this->EE->input->get('custom_order');
		$question_title = 'Question ' . $question_custom_order;
		$question = ''; // Question view data

		// Set new question properties in model
		$this->EE->vwm_surveys_m
			->set_survey_id($survey_id)
			->set_page($page)
			->set_question_title($question_title)
			->set_question_type($question_type)
			->set_question_custom_order($question_custom_order);

		// Insert the new question into the database and get its insert ID back
		if ( $id = $this->EE->vwm_surveys_m->insert_question() )
		{
			// Prepare data array for our question view
			$data = array(
				'question' => array(
					'title' => $question_title,
					'id' => $id,
					'options' => array(),
					'type' => $question_type,
					'custom_order' => $question_custom_order
				),
				'question_type' => $question_type,
				'question_number' => $question_custom_order,
				'page_number' => $page,
				'question_types' => $this->get_question_types()
			);

			$question = $this->EE->load->view('vwm_question_template', $data, TRUE);
		}

		// Echo out our question
		die($question);
	}

	/**
	 * Delete a question
	 * 
	 * @access public
	 * @return void
	 */
	public function delete_question()
	{
		$question_id = (int)$this->EE->input->get('question_id');
		$page_number = (int)$this->EE->input->get('page_number');

		// If the question removal was successful
		if ( $this->EE->vwm_surveys_m->delete_question($question_id, $page_number) )
		{
			$this->EE->output->send_ajax_response(array('result' => 'success'));
		}
		// If deleting the question failed
		else
		{
			$this->EE->output->send_ajax_response(array('result' => 'failure'));
		}
	}

	/**
	 * Move a question up or down the page
	 * 
	 * @access public
	 * @return string
	 */
	public function move_question()
	{
		$question_id = (int)$this->EE->input->get('question_id');
		$move = $this->EE->input->get('move');

		$this->EE->vwm_surveys_m->update_question_order($question_id, $move);

		die( $this->edit_survey() );
	}

	/**
	 * Add a page to a survey
	 * 
	 * @access public
	 * @return string
	 */
	public function add_page()
	{
		$title = trim($this->EE->input->get('title'));
		$survey_id = (int)$this->EE->input->get('survey_id');

		// Only add a page if we have a title and a survey ID
		if ($title AND $survey_id)
		{
			// Insert a new page
			if ( $this->EE->vwm_surveys_m->insert_page($survey_id, $title) !== FALSE )
			{
				/**
				 * Return HTML containing survey edit page
				 * We will then use jQuery to parse this HTML and pull out the latest page
				 */
				die( $this->edit_survey() );
			}
		}
	}

	/**
	 * Delete a page from a survey
	 * 
	 * @access public
	 * @return bool
	 */
	public function delete_page()
	{
		$survey_id = (int)$this->EE->input->get('survey_id');
		$page = (int)$this->EE->input->get('page');

		return $this->EE->vwm_surveys_m->delete_page($survey_id, $page);
	}

}

// EOF