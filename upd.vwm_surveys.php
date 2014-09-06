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
 * Lets install, uninstall, or update this bad boy
 */
class Vwm_surveys_upd {

	private $EE;
	public $version = '0.5.2';
	const MIN_PHP_VERSION = '5.3.0';

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
	}

	/**
	 * Module Installer
	 *
	 * @access public
	 * @return bool
	 */	
	public function install()
	{
		$this->check_php_version();

		// VWM Polls module information
		$data = array(
			'module_name' => 'Vwm_surveys',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);

		// Add submit_survey action to exp_actions
		$submit_survey = array('class' => 'Vwm_surveys', 'method' => 'submit_survey');
		$this->EE->db->insert('actions', $submit_survey);

		// Get database prefix
		$prefix = $this->EE->db->dbprefix;

		// Table to store survey questions
		$this->EE->db->query("	
			CREATE TABLE IF NOT EXISTS `{$prefix}vwm_surveys_questions` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`title` mediumtext NULL DEFAULT NULL,
				`type` varchar(32) NOT NULL,
				`options` mediumtext NULL DEFAULT NULL,
				`custom_order` tinyint(3) unsigned NOT NULL DEFAULT  '0',
				`page` tinyint(4) unsigned NOT NULL DEFAULT '0',
				`survey_id` mediumint(8) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `survey_id_2` (`survey_id`,`page`,`custom_order`),
				KEY `survey_id` (`survey_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		");

		// Table to store surveys
		$this->EE->db->query("
			CREATE TABLE IF NOT EXISTS `{$prefix}vwm_surveys_surveys` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`site_id` INT(4) UNSIGNED NOT NULL DEFAULT '1',
				`hash` varchar(32) NOT NULL,
				`title` varchar(128) NOT NULL,
				`allowed_groups` varchar(128) DEFAULT NULL,
				`created` int(10) unsigned NOT NULL,
				`updated` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `hash` (`hash`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		");

		// Table to store survey pages
		$this->EE->db->query("
			CREATE TABLE IF NOT EXISTS `{$prefix}vwm_surveys_pages` (
				`survey_id` mediumint(9) NOT NULL,
				`page` tinyint(4) NOT NULL DEFAULT '0',
				`title` varchar(128) NOT NULL DEFAULT '',
				`description` mediumtext NOT NULL,
				UNIQUE KEY `survey_id` (`survey_id`,`page`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

		// Table to store survey submissions
		$this->EE->db->query("
			CREATE TABLE IF NOT EXISTS `{$prefix}vwm_surveys_submissions` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`hash` varchar(32) NOT NULL,
				`member_id` int(10) unsigned DEFAULT NULL,
				`survey_id` int(10) unsigned NOT NULL,
				`data` text NOT NULL,
				`created` int(10) unsigned NOT NULL,
				`updated` int(10) unsigned DEFAULT NULL,
				`completed` int(10) unsigned DEFAULT NULL,
				`current_page` tinyint(4) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `hash` (`hash`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		");

		// Table to store survey results
		$this->EE->db->query("
			CREATE TABLE IF NOT EXISTS `{$prefix}vwm_surveys_results` (
				`survey_id` int(10) unsigned NOT NULL,
				`data` text NOT NULL,
				`num_submissions` int(10) unsigned NOT NULL DEFAULT '0',
				`compiled` int(10) unsigned NOT NULL,
				PRIMARY KEY (`survey_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		");

		return TRUE;
	}

	/**
	 * Uninstall
	 *
	 * @access public
	 * @return bool
	 */	
	public function uninstall()
	{
		// Get database prefix
		$prefix = $this->EE->db->dbprefix;

		// Get module ID
		$module_id = $this->EE->db
			->select('module_id')
			->where('module_name', 'Vwm_surveys')
			->limit(1)
			->get('modules')
			->row('module_id');

		// Delete from modules
		$this->EE->db
			->where('module_id', $module_id)
			->delete('modules');

		// Delete from module_member_groups
		$this->EE->db
			->where('module_id', $module_id)
			->delete('module_member_groups');

		// Delete from actions
		$this->EE->db
			->where('class', 'Vwm_surveys')
			->delete('actions');

		// Delete all extra tables
		$this->EE->db->query("DROP TABLE {$prefix}vwm_surveys_questions");
		$this->EE->db->query("DROP TABLE {$prefix}vwm_surveys_surveys");
		$this->EE->db->query("DROP TABLE {$prefix}vwm_surveys_pages");
		$this->EE->db->query("DROP TABLE {$prefix}vwm_surveys_submissions");
		$this->EE->db->query("DROP TABLE {$prefix}vwm_surveys_results");

		return TRUE;
	}

	/**
	 * Update
	 *
	 * @access	public
	 * @return	bool
	 */	
	public function update($current = '')
	{
		$this->check_php_version();

		// Get database prefix
		$prefix = $this->EE->db->dbprefix;

		// Version 0.2
		if ($current == '0.2')
		{
			// Make allowed groups NULLable
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_surveys`
				MODIFY `allowed_groups` VARCHAR(128) CHARACTER SET utf8 NULL DEFAULT NULL
			");

			// Make question options NULLable
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_questions`
				MODIFY `options` MEDIUMTEXT CHARACTER SET utf8 NULL DEFAULT NULL
			");
		}

		if ($current < '0.3.3')
		{
			// Make default value for page title an empty string
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_pages`
				CHANGE `title` `title` VARCHAR(128) CHARACTER SET utf8 NOT NULL DEFAULT ''
			");

			// Make default page 0
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_pages`
				CHANGE `page` `page` TINYINT(4) NOT NULL DEFAULT '0'
			");
		}

		if ($current < '0.3.4')
		{
			// Make default value for page options NULL and default value for custom_order 0
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_questions`
				CHANGE `options` `options` MEDIUMTEXT CHARACTER SET utf8 NULL DEFAULT NULL ,
				CHANGE `custom_order` `custom_order` TINYINT(3) UNSIGNED NOT NULL DEFAULT  '0'
			");
		}
		
		if ($current < '0.4.1')
		{
			// Update question title to be MEDIUMTEXT
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_questions`
				CHANGE `title` `title` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
			");
		}

		if ($current < '0.5')
		{
			// Add site ID
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_surveys`
				ADD `site_id` INT(4) UNSIGNED NOT NULL DEFAULT '1' AFTER `id`
			");

			// Add page description
			$this->EE->db->query("
				ALTER TABLE `{$prefix}vwm_surveys_pages` ADD `description` MEDIUMTEXT NOT NULL DEFAULT ''
			");
		}

		return TRUE;
	}

	/**
	 * Check the current version of PHP and thow an error if it's not good enough
	 *
	 * @access private
	 * @return boolean
	 */
	private function check_php_version()
	{
		// If current version of PHP is not up snuff
		if ( version_compare(PHP_VERSION, self::MIN_PHP_VERSION) < 0 )
		{
			show_error('VWM Surveys requires PHP version ' . self::MIN_PHP_VERSION . ' or higher.');
			return FALSE;
		}
	}

}

// EOF