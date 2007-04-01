<?php
/**
* @version 0.9 $Id$
* @package Joomla
* @subpackage EventList
* @copyright (C) 2005 - 2007 Christoph Lukes
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * EventList Component Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListController extends JController
{
	function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask( 'applycss', 	'savecss' );
	}

	/**
	 * Display the view
	 */
	function display()
	{
		parent::display();

	}

	/**
	 * Saves the settings
	 *
	 */
	function savesettings()
	{
		global $mainframe, $option;

		$db 		= & JFactory::getDBO();
		$post 		= JRequest::get( 'post' );

		$settings 	= & JTable::getInstance('eventlist_settings', '');

		// Bind the form fields to the table
		if (!$settings->bind($post)) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		$meta_key="";
		foreach ($settings->meta_keywords as $meta_keyword) {
			if ($meta_key != "") {
				$meta_key .= ", ";
			}
			$meta_key .= $meta_keyword;
		}

		$settings->id = 1;

		if (!$settings->store()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

    	$mainframe->redirect('index.php?option='.$option, JText::_( 'SETTINGS SAVE') );
	}
	
	/**
	 * Saves the css
	 *
	 */
	function savecss()
	{
		global $mainframe;

		// Initialize some variables
		$option			= JRequest::getVar('option');
		$filename		= JRequest::getVar('filename');
		$path			= JRequest::getVar('path');
		$filecontent	= JRequest::getVar('filecontent', '', '', '', JREQUEST_ALLOWRAW);

		if (!$filecontent) {
			$mainframe->redirect('index.php?option='.$option, JText::_('Operation Failed').': '.JText::_('Content empty.'));
		}

		jimport('joomla.filesystem.file');
		if (JFile::write($path.$filename, $filecontent))
		{
			$task = JRequest::getVar('task');
			switch($task)
			{
				case 'applycss' :
					$mainframe->redirect('index.php?option='.$option.'&view=editcss', JText::_('CSS FILE SUCCESSFULLY ALTERED'));
					break;

				case 'savecss'  :
				default          :
					$mainframe->redirect('index.php?option='.$option, JText::_('CSS FILE SUCCESSFULLY ALTERED') );
					break;
			}
		} else {
			$mainframe->redirect('index.php?option='.$option, JText::_('Operation Failed').': '.JText::_('Failed to open file for writing.'));
		}
	}
}
?>