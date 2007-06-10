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
				default         :
					$mainframe->redirect('index.php?option='.$option, JText::_('CSS FILE SUCCESSFULLY ALTERED') );
					break;
			}
		} else {
			$mainframe->redirect('index.php?option='.$option, JText::_('Operation Failed').': '.JText::_('Failed to open file for writing.'));
		}
	}

	/**
	 * displays the fast addvenue screen
	 *
	 * @since 0.9
	 */
	function addvenue( )
	{
		//TODO: Implement Access check
		JRequest::setVar( 'view', 'event' );
		JRequest::setVar( 'layout', 'addvenue'  );

		parent::display();
	}
}
?>