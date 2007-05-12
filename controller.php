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
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		//register extratasks
		$this->registerTask( 'ical', 		'vcal' );
	}

	/**
	 * Display the view
	 */
	function display()
	{
		$viewName 		= JRequest::getVar( 'view' );

		parent::display();
	}

	/**
	 * Logic for canceling an event edit task
	 *
	 */
	function cancelevent()
	{
		$db 	= & JFactory::getDBO();
		$user	= & JFactory::getUser();

		$view	= JRequest::getVar('returnview', '', 'post', 'string');
		$id		= JRequest::getVar( 'id', 0, 'post', 'int' );
		$Itemid = JRequest::getVar( 'Itemid', 0, 'post', 'int' );

		// Must be logged in
		if ($user->get('id') < 1) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		if ($id) {
			// Create and load a events table
			$row =& JTable::getInstance('eventlist_events', '');

			$row->load($id);
			$row->checkin();

			$this->setRedirect( JRoute::_('index.php?view=details&did='.$id.'&Itemid='.$Itemid ) );

		} else {
			$this->setRedirect( JRoute::_('index.php?view='.$view.'&Itemid='.$Itemid ) );
		}
	}

	/**
	 * Logic for canceling a venue edit task
	 *
	 * @param string $option
	 */
	function cancelvenue()
	{
		$db 	= & JFactory::getDBO();
		$user	= & JFactory::getUser();

		$view	= JRequest::getVar('returnview', '', 'request', 'string');
		$id		= JRequest::getVar( 'id', 0, 'post', 'int' );
		$Itemid = JRequest::getVar( 'Itemid', 0, 'post', 'int' );

		// Must be logged in
		if ($user->get('id') < 1) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		if ($id) {
			// Create and load a venues table
			$row =& JTable::getInstance('eventlist_venues', '');

			$row->load($id);
			$row->checkin();

			$this->setRedirect( JRoute::_('index.php?view=venueevents&locatid='.$id.'&Itemid='.$Itemid) );

		} else {
		//	$view != '' ? $target = '&view='.$view : $target = '';
			$this->setRedirect( JRoute::_('index.php?view='.$view.'&Itemid='.$Itemid) );
		//	$this->setRedirect( JRoute::_('index.php?'.$target) );
		}
	}

	/**
	 * Saves the submitted venue to the database
	 *
	 * @since 0.5
	 */
	function savevenue()
	{
		global $mainframe, $option;

		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}

		//Sanitize
		$post = JRequest::get( 'post' );
		$post['locdescription'] = JRequest::getVar( 'locdescription', '', 'post', 'string', JREQUEST_ALLOWRAW );

		$file 		= JRequest::getVar( 'userfile', '', 'files', 'array' );
		$Itemid 	= JRequest::getVar( 'Itemid', 0, 'post', 'int' );


		$model = $this->getModel('editvenue');

		if ($returnid = $model->store($post, $file)) {

			$msg 	= JText::_( 'VENUE SAVED' );
			$link 	= 'index.php?option='.$option.'&Itemid='.$Itemid.'&view=venueevents&locatid='.$returnid ;

		} else {

			$msg 		= '';
			$returnview	= JRequest::getVar('returnview', '', '', 'string');
			$link 		= 'index.php?option='.$option.'&Itemid='.$Itemid.'&view='.$returnview ;

			JError::raiseWarning('SOME_ERROR_CODE', $model->getError() );
		}

		$model->checkin();

		$this->setRedirect($link, $msg );
	}

	/**
	 * Cleanes and saves the submitted event to the database
	 *
	 * TODO: Check if the user is allowed to post events assigned to this category/venue
	 *
	 * @since 0.4
	 */
	function saveevent()
	{
		global $option;

		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}

		//get image
		$file 		= JRequest::getVar( 'userfile', '', 'files', 'array' );
		$Itemid 	= JRequest::getVar( 'Itemid', 0, 'post', 'int' );
		$post 		= JRequest::get( 'post' );

		$model = $this->getModel('editevent');

		if ($returnid = $model->store($post, $file)) {

			$msg 	= JText::_( 'EVENT SAVED' );
			$link 	= 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view=details&did='.$returnid ;

		} else {

			$msg 		= '';
			$returnview	= JRequest::getVar('returnview', '', '', 'string');
			$link 		= 'index.php?option='.$option.'&Itemid='.$Itemid.'&view='.$returnview ;

			JError::raiseWarning('SOME_ERROR_CODE', $model->getError() );
		}

		$model->checkin();

		$this->setRedirect($link, $msg );
	}

	/**
	 * Saves the registration to the database
	 *
	 * @since 0.7
	 */
	function userregister()
	{
		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}

		$id 	= JRequest::getVar( 'rdid', 0, 'post', 'int' );
		$Itemid = JRequest::getVar( 'Itemid', 0, 'post', 'int' );

		// Get the model
		$model = & $this->getModel('Details', 'EventListModel');

		$model->setId($id);
		$model->userregister();

		$this->setRedirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view=details&did='.$id, JText::_( 'REGISTERED SUCCESSFULL' ) );
	}

	/**
	 * Deletes a registered user
	 *
	 * @since 0.7
	 */
	function delreguser()
	{
		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}

		$id 	= JRequest::getVar( 'rdid', 0, 'post', 'int' );
		$Itemid = JRequest::getVar( 'Itemid', 0, 'post', 'int' );

		// Get/Create the model
		$model = & $this->getModel('Details', 'EventListModel');

		$model->setId($id);
		$model->delreguser();

		$msg = JText::_( 'UNREGISTERED SUCCESSFULL' );
		$this->setRedirect( 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view=details&did='.$id, $msg );
	}

	/**
	 * shows a venue select listing
	 *
	 * @since 0.9
	 */
	function selectvenue( )
	{
		//TODO: Implement Access check
		//JRequest::setVar( 'view', 'editevent' );
		JRequest::setVar( 'layout', 'selectvenue'  );

		parent::display();
	}

	/**
	 * offers the vcal/ical functonality
	 *
	 * @author Lybegard Karl-Olof
	 * @since 0.9
	 */
	function vcal()
	{
		global $mainframe;

		$task 			= JRequest::getVar( 'task' );
		$did 			= (int) JRequest::getVar( 'did', 0, 'request', 'int' );
		$user_offset 	= $mainframe->getCfg( 'offset_user' );

		//get Data from model
		$model = & $this->getModel('Details', 'EventListModel');
		$model->setId($did);

		$row = $model->getDetails();

		$Start = mktime(strftime('%H', strtotime($row->times)),
				strftime('%M', strtotime($row->times)),
				strftime('%S', strtotime($row->times)),
				strftime('%m', strtotime($row->dates)),
				strftime('%d', strtotime($row->dates)),
				strftime('%Y', strtotime($row->dates)),0);

		$End   = mktime(strftime('%H', strtotime($row->endtimes)),
				strftime('%M', strtotime($row->endtimes)),
				strftime('%S', strtotime($row->endtimes)),
				strftime('%m', strtotime($row->enddates)),
				strftime('%d', strtotime($row->enddates)),
				strftime('%Y', strtotime($row->enddates)),0);

		require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'vcal.class.php');

		$v = new vCal();

		$v->setTimeZone($user_offset);
		$v->setSummary($row->venue.'-'.$row->catname.'-'.$row->title);
		$v->setDescription($row->datdescription);
		$v->setStartDate($Start);
		$v->setEndDate($End);
		$v->setLocation($row->street.', '.$row->plz.', '.$row->city.', '.$row->country);
		$v->setFilename($row->did);

		if ($task == 'vcal') {
			$v->generateHTMLvCal();
		} else {
			$v->generateHTMLiCal();
		}

	}
}
?>