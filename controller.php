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
		global $Itemid, $mainframe;

		$db 	= & JFactory::getDBO();
		$user	= & JFactory::getUser();

		$view	= JRequest::getVar('returnview', '', 'post', 'string');
		$id		= JRequest::getVar( 'id', 0, 'post', 'int' );

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

			$mainframe->redirect( 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view=details&did='.$id );

		} else {
			$mainframe->redirect( 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$view );
		}
	}

	/**
	 * Logic for canceling a venue edit task
	 *
	 * @param string $option
	 */
	function cancelvenue()
	{
		global $Itemid, $mainframe;

		$db 	= & JFactory::getDBO();
		$user	= & JFactory::getUser();

		$view	= JRequest::getVar('returnview', '', 'post', 'string');
		$id		= JRequest::getVar( 'id', 0, 'post', 'int' );

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

			$mainframe->redirect( 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view=venueevents&locatid='.$id);

		} else {
			$mainframe->redirect( 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$view );
		}
	}


	/**
	 * Saves the submitted venue to the database
	 *
	 * TODO move to model
	 *
	 * @since 0.5
	 */
	function savevenue()
	{
		global $mainframe, $option, $Itemid;

		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}
		
		jimport('joomla.utilities.date');

		$db 		= & JFactory::getDBO();
		$user 		= & JFactory::getUser();
		$elsettings = ELHelper::config();

		//Get mailinformation
		$SiteName 		= $mainframe->getCfg('sitename');
		$MailFrom	 	= $mainframe->getCfg('mailfrom');
		$FromName 		= $mainframe->getCfg('fromname');
		


		$file 		= JRequest::getVar( 'userfile', '', 'files', 'array' );
		$sizelimit 	= $elsettings->sizelimit*1024; //size limit in kb
		$base_Dir = JPATH_SITE.'/images/eventlist/venues/';

		//Sanitize
		$post = JRequest::get( 'post' );
		$post['locdescription'] = JRequest::getVar( 'locdescription', '', 'post', 'string', JREQUEST_ALLOWRAW );

		$row =& JTable::getInstance('eventlist_venues', '');


		//bind it to the table
		if (!$row->bind($post)) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		

		//Get view the user come from
		if ($row->id) {
			$returnview = 'venueevents&locatid='.$row->id;
		} else {
			$returnview	= JRequest::getVar('returnview', '', '', 'string');
		}

		$datenow = new JDate();

		//Are we saving from an item edit?
		if ($row->id) {

			$owner = ELUser::isOwner($row->id, 'venues');

			//check if user is allowed to edit venues
			$allowedtoeditvenue = ELUser::editaccess($elsettings->venueowner, $owner, $elsettings->venueeditrec, $elsettings->venueedit);

			if ($allowedtoeditvenue == 0) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'NO ACCESS' ));
			}

			$row->modified 		= $datenow->toFormat();
			$row->modified_by 	= $user->get('id');


			//Is editor the owner of the venue
			//This extra Check is needed to make it possible
			//that the venue is published after an edit from an owner
			if ($elsettings->venueowner == 1 && $owner == $user->get('id')) {
				$owneredit = 1;
			} else {
				$owneredit = 0;
			}

		} else {

			//check if user is allowed to submit new venues
			$delloclink = ELUser::validate_user( $elsettings->locdelrec, $elsettings->deliverlocsyes );

			if ($delloclink == 0){
				$mainframe->redirect( 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'NO ACCESS' ));
			}


			//get IP, time and userid
			$row->author_ip 		= getenv('REMOTE_ADDR');
			$row->created			= $datenow->toFormat();
			$row->created_by		= $user->get('id');

			//set owneredit to false
			$owneredit = 0;
		}

		//Image upload
		if ( ( $elsettings->imageenabled == 2 || $elsettings->imageenabled == 1 ) && ( !empty($file['name'])) )  {

			$imagesize 	= $file['size'];

			if (empty($file['name'])) {
				$row->checkin();
				$mainframe->redirect( 'index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'IMAGE EMPTY' ) );
			}

			if ($imagesize > $sizelimit) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'IMAGE FILE SIZE' ) );
			}

			if (file_exists($base_Dir.$file['name'])) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'IMAGE EXISTS' ) );
			}

			jimport('joomla.filesystem.file');
			$format 	= JFile::getExt($file['name']);

			$allowable 	= array ('bmp', 'gif', 'jpg', 'png');
			if (in_array($format, $allowable)) {
				$noMatch = true;
			} else {
				$noMatch = false;
			}

			if (!$noMatch) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'WRONG IMAGE FILE TYPE' ) );
			}

			if (!JFile::upload($file['tmp_name'], $base_Dir.strtolower($file['name']))) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_('UPLOAD FAILED') );
			} else {
				$row->locimage = strtolower($file['name']);
			}
		} else {
			//keep image if edited and left blank
			$row->locimage = $row->curimage;
		}//end image if

		//cleanup fields
		$row->club = strip_tags($row->club);
		$row->club = ampReplace($row->club);

		if(empty($row->club)) {
			$row->checkin();
			$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR ADD VENUE' ) );
		}

		if ($elsettings->showcity == 1) {
			if(empty($row->city)) {
				$row->checkin();
        		$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR ADD CITY' ) );
			}
		}

		if (($elsettings->showmap24 == 1) && ($elsettings->showdetailsadress == 1)){
			if ((empty($row->street)) || (empty($row->plz)) || (empty($row->city)) || (empty($row->country))) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR ADD ADDRESS' ) );
			}
		}

		if (!empty($row->url)) {
			$row->url = strip_tags($row->url);
			$urllength = strlen($row->url);

			if ($urllength > 150) {
				$row->checkin();
      			$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR URL LONG' ) );
			}
			if (!preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'
       		.'((:[0-9]{1,5})?\/.*)?$/i' , $row->url)) {
       			$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR URL WRONG FORMAT' ) );
			}
		}

		$row->street = strip_tags($row->street);
		$streetlength = JString::strlen($row->street);
		if ($streetlength > 50) {
			$row->checkin();
     	 	$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR STREET LONG' ) );
		}

		$row->plz = strip_tags($row->plz);
		$plzlength = JString::strlen($row->plz);
		if ($plzlength > 10) {
			$row->checkin();
      		$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR ZIP LONG' ) );
		}

		$row->city = strip_tags($row->city);
		$citylength = JString::strlen($row->city);
		if ($citylength > 50) {
			$row->checkin();
    	  	$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR CITY LONG' ) );
		}

		$row->state = strip_tags($row->state);
		$statelength = JString::strlen($row->state);
		if ($statelength > 50) {
			$row->checkin();
    	  	$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR STATE LONG' ) );
		}

		$row->country = strip_tags($row->country);
		$countrylength = JString::strlen($row->country);
		if ($countrylength > 3) {
			$row->checkin();
     	 	$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR COUNTRY LONG' ) );
		}

		//Check description
		$editoruser = & ELUser::editoruser();
		if (!$editoruser) {

			$row->locdescription = strip_tags($row->locdescription);

			//cut too long words
			$row->locdescription = wordwrap($row->locdescription, 75, " ", 1);

			//check length
			$beschnitten = JString::strlen($row->locdescription);
			if ($beschnitten > $elsettings->datdesclimit) {

				// if required shorten it
				$row->locdescription = JString::substr($row->locdescription, 0, $elsettings->datdesclimit);
				//if shortened add ...[striped]
				$row->locdescription = $row->locdescription.'...[striped]';
			}
		}

		//Autopublish
		//check if the user has the required rank for autopublish
		$autopublloc = ELUser::validate_user( $elsettings->locpubrec, $elsettings->autopublocate );

		//Check if user is the owner of the venue
		//If yes enable autopublish
		if ($autopublloc || $owneredit) {
			$row->published = 1 ;
		} else {
			$row->published = 0 ;
		}

		/*
		 * Make sure the data is valid
		 */
		if (!$row->check()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		/*
		 * store it in the db
		 */
		if (!$row->store()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		/*
		 * Check the venue item in and update item order
		 */
		$row->checkin();
		$row->reorder();

		/*
		* create mail
		*/
		if (($elsettings->mailinform == 2) || ($elsettings->mailinform == 3)) {

			$db->SetQuery("SELECT username, email FROM #__users"
						. "\nWHERE id = ".$user->get('id')
						);

			$rowuser = $db->loadObject();
			If ($row->id) {
				$mailbody = JText::_( 'GOT EDITING' ).' '.$rowuser->username.' \n';
				$mailbody .= ' \n';
				$mailbody .= JText::_( 'USERMAILADDRESS' ).' '.$rowuser->email.' \n';
				//$mailbody .= JText::_( 'USER IP' ).' '.$row->author_ip.' \n';
				$mailbody .= JText::_( 'SUBMISSION TIME' ).' '.strftime( '%c', $row->modified ).' \n';
			} else {
				$mailbody = JText::_( 'GOT SUBMISSION' ).' '.$rowuser->username.' \n';
				$mailbody .= ' \n';
				$mailbody .= JText::_( 'USERMAILADDRESS' ).' '.$rowuser->email.' \n';
				$mailbody .= JText::_( 'USER IP' ).' '.$row->author_ip.' \n';
				$mailbody .= JText::_( 'SUBMISSION TIME' ).' '.strftime( '%c', $row->created ).' \n';
			}
			$mailbody .= ' \n';
			$mailbody .= JText::_( 'VENUE' ).': '.$row->club.' \n';
			$mailbody .= JText::_( 'WEBSITE' ).': '.$row->url.' \n';
			$mailbody .= JText::_( 'STREET' ).': '.$row->street.' \n';
			$mailbody .= JText::_( 'ZIP' ).': '.$row->plz.' \n';
			$mailbody .= JText::_( 'CITY' ).': '.$row->city.' \n';
			$mailbody .= JText::_( 'COUNTRY' ).': '.$row->country.' \n';
			$mailbody .= ' \n';
			$mailbody .= JText::_( 'DESCRIPTION' ).': '.$row->locdescription.' \n';

			jimport('joomla.utilities.mail');

			$mail = new JMail();

			$mail->addRecipient( $elsettings->mailinformrec );
			//$mail->addRecipient( array( $mailinformrec, $mailinformrec2 )  );
			$mail->setSender( array( $MailFrom, $FromName ) );
			$mail->setSubject( $SiteName.JText::_( 'NEW VENUE MAIL' ) );
			$mail->setBody( $mailbody );

			$sent = $mail->Send();
		}

		$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view=venueevents&locatid='.$row->id, JText::_( 'VENUE SAVED' ) );

	}

	/**
	 * Cleanes and saves the submitted event to the database
	 *
	 * TODO Change the fields to more clear names, editor ip field?
	 * TODO: Check if the user is allowed to post events assigned to this category/venue
	 *
	 * @since 0.4
	 */
	function saveevent()
	{
		global $mainframe, $option, $Itemid;

		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}
		
		jimport('joomla.utilities.date');

		$db			= & JFactory::getDBO();
		$user 		= & JFactory::getUser();
		$acl		= & JFactory::getACL();
		$elsettings = ELHelper::config();

		//Get mailinformation
		$SiteName 		= $mainframe->getCfg('sitename');
		$MailFrom	 	= $mainframe->getCfg('mailfrom');
		$FromName 		= $mainframe->getCfg('fromname');

		//get image
		$file 		= JRequest::getVar( 'userfile', '', 'files', 'array' );
		$sizelimit 	= $elsettings->sizelimit*1024; //size limit in kb
		$base_Dir 	= JPATH_SITE.'/images/eventlist/events/';

		//Sanitize
		$post = JRequest::get( 'post' );
		$post['datdescription'] = JRequest::getVar( 'datdescription', '', 'post','string', JREQUEST_ALLOWRAW );

		//include the metatags
		$post['meta_description'] = addslashes(htmlspecialchars(trim($elsettings->meta_description)));
		if (strlen($post['meta_description']) > 255) {
			$post['meta_description'] = substr($post['meta_description'],0,254);
		}
		$post['meta_keywords'] = addslashes(htmlspecialchars(trim($elsettings->meta_keywords)));
		if (strlen($post['meta_keywords']) > 200) {
			$post['meta_keywords'] = substr($post['meta_keywords'],0,199);
		}

		$row =& JTable::getInstance('eventlist_events', '');

		/*
		* bind it to the table
		*/
		if (!$row->bind($post)) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		$datenow = new JDate();
		
		//Are we saving from an item edit?
		if ($row->id) {

			$returnview = 'details&did='.$row->id;

			//check if user is allowed to edit events
			$owner = ELUser::isOwner($row->id, 'events');
			$editaccess	= & ELUser::editaccess($elsettings->eventowner, $owner, $elsettings->eventeditrec, $elsettings->eventedit);
			$maintainer = ELUser::ismaintainer();

			if ($maintainer || $editaccess ) $allowedtoeditevent = 1;

			if ($allowedtoeditevent == 0) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'NO ACCESS' ));
			}

			$row->modified 		= $datenow->toFormat();
			$row->modified_by 	= $user->get('id');

			/*
			* Is editor the owner of the event
			* This extra Check is needed to make it possible
			* that the venue is published after an edit from an owner
			*/
			if ($elsettings->venueowner == 1 && $owner == $user->get('id')) {
				$owneredit = 1;
			} else {
				$owneredit = 0;
			}

		} else {

			$returnview	= JRequest::getVar('returnview', '', '', 'string');

			//check if user is allowed to submit new events
			$maintainer = ELUser::ismaintainer();
			$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

			if ($maintainer || $genaccess ) $dellink = 1;

			if ($dellink == 0){
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'NO ACCESS' ));
			}

			//get IP, time and userid
			$row->author_ip 	= getenv('REMOTE_ADDR');
			$row->created 		= $datenow->toFormat();
			$row->created_by 	= $user->get('id');

			//Set owneredit to false
			$owneredit = 0;
		}

		/*
		* Autopublish
		* check if the user has the required rank for autopublish
		*/
		$autopubev = ELUser::validate_user( $elsettings->evpubrec, $elsettings->autopubl );
		if ($autopubev || $owneredit) {
				$row->published = 1 ;
			} else {
				$row->published = 0 ;
		}


		//Image upload
		if ( ( $elsettings->imageenabled == 2 || $elsettings->imageenabled == 1 ) && ( !empty($file['name'])  ) )  {
			$imagesize 	= $file['size'];

			if ($imagesize > $sizelimit) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'IMAGE FILE SIZE' ) );
			}

			if (file_exists($base_Dir.$file['name'])) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'IMAGE EXISTS' ) );
			}

			jimport('joomla.filesystem.file');
			$format 	= JFile::getExt($file['name']);
			$allowable 	= array ('bmp', 'gif', 'jpg', 'png');

			if (in_array($format, $allowable)) {
				$noMatch = true;
			} else {
				$noMatch = false;
			}

			if (!$noMatch) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'WRONG IMAGE FILE TYPE' ) );
			}

			if (!JFile::upload($file['tmp_name'], $base_Dir.strtolower($file['name']))) {
				$row->checkin();
				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'UPLOAD FAILED' ) );
			} else {
				$row->datimage = strtolower($file['name']);
			}
		} else {
			//keep image if edited and left blank
			$row->datimage = $row->curimage;
		}//end image if


		// Check fields
		if (empty($row->enddates)) {
			$row->enddates = NULL;
		}
		
		if (empty($row->times)) {
			$row->times = NULL;
		}
		
		if (empty($row->endtimes)) {
			$row->endtimes = NULL;
		}
		
		if (isset($row->dates)) {
			$datum = $row->dates;
			if (!preg_match("/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/", $datum)) {
	 	   		$row->checkin();
	 	     	$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'DATE WRONG' ) );
			}
		}

		if (($elsettings->showtime == 1) || (!empty($row->times))) {
			if (isset($row->times)) {
   				$times = $row->times;
   				if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $times)) {
      				$row->checkin();
      				$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'TIME WRONG' ) );
	  			}
			}
		}

		if ($row->endtimes != 0) {
	   			$endtimes = $row->endtimes;
   			if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $endtimes)) {
      			$row->checkin();
      			$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'TIME WRONG' ) );
	  		}
		}

		$row->title = strip_tags($row->title);
		$titlelength = JString::strlen($row->title);

		if ($titlelength > 60 || $row->title =='') {
			$row->checkin();
      		$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view='.$returnview, JText::_( 'ERROR TITLE LONG' ) );
		}

		$editoruser = & ELUser::editoruser();
		if (!$editoruser) {
			//check datdescription --> wipe out code
			$row->datdescription = strip_tags($row->datdescription);

			// cut too long words
			$row->datdescription = wordwrap($row->datdescription, 75, ' ', 1);

			//check length
			$beschnitten = JString::strlen($row->datdescription);
			if ($beschnitten > $elsettings->datdesclimit) {
				//too long then shorten datdescription
				$row->datdescription = JString::substr($row->datdescription, 0, $elsettings->datdesclimit);
				//add ...[striped]
				$row->datdescription = $row->datdescription.'...[striped]';
			}
		}

		/*
		* set registration regarding the el settings
		*/
		switch ($elsettings->showfroregistra) {
			case 0:
				$row->registra = 0;
			break;

			case 1:
				$row->registra = 1;
			break;

			case 2:
				$row->registra =  $row->registra ;
			break;
		}

		switch ($elsettings->showfrounregistra) {
			case 0:
				$row->unregistra = 0;
			break;

			case 1:
				$row->unregistra = 1;
			break;

			case 2:
				if ($elsettings->showfroregistra >= 1) {
					$row->unregistra = $row->unregistra;
				} else {
					$row->unregistra = 0;
				}
			break;
		}


		/*
		 * Make sure the table is valid
		 */
		if (!$row->check()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		/*
		 * store it in the db
		 */
		if (!$row->store(true)) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		/*
		 * Check the event item in
		 */
		$row->checkin();

		/*
		* create mail
		*/
		if (($elsettings->mailinform == 1) || ($elsettings->mailinform == 3)) {
			$mailloc = intval($row->locid) ;
			$db->SetQuery("SELECT * FROM #__eventlist_venues"
						. "\nWHERE id = ".$mailloc
						);

			$rowloc = $db->loadObject();

			$db->SetQuery("SELECT username, email FROM #__users"
						. "\nWHERE id = ".$user->get('id')
						);

			$rowuser = $db->loadObject( );

			if ($elsettings->id) {
				$mailbody = JText::_( 'GOT EDITING' ).': '.$rowuser->username.' \n';
				$mailbody .= ' \n';
				$mailbody .= JText::_( 'USERMAILADDRESS' ).': '.$rowuser->email.' \n';
				//$mailbody .= JText::_( 'USER IP' ).': '.$row->author_ip.' \n';
				$mailbody .= JText::_( 'SUBMISSION TIME' ).': '.strftime( '%c', $row->modified ).' \n';
			} else {
				$mailbody = JText::_( 'GOT SUBMISSION' ).': '.$rowuser->username.' \n';
				$mailbody .= ' \n';
				$mailbody .= JText::_( 'USERMAILADDRESS' ).': '.$rowuser->email.' \n';
				$mailbody .= JText::_( 'USER IP' ).': '.$row->author_ip.' \n';
				$mailbody .= JText::_( 'SUBMISSION TIME' ).': '.strftime( '%c', $row->created ).' \n';
			}
			$mailbody .= ' \n';
			$mailbody .= JText::_( 'TITLE' ).': '.$row->title.' \n';
			$mailbody .= JText::_( 'DATE' ).': '.$row->dates.' \n';
			$mailbody .= JText::_( 'TIME' ).': '.$row->times.' \n';
			$mailbody .= JText::_( 'VENUE' ).': '.$rowloc->club.' / '.$rowloc->city.' \n';
			$mailbody .= JText::_( 'DESCRIPTION' ).': '.$row->datdescription.' \n';

			jimport('joomla.utilities.mail');

			$mail = new JMail();

			$mail->addRecipient( $elsettings->mailinformrec );
			//$mail->addRecipient( array( $mailinformrec, $mailinformrec2 )  );
			$mail->setSender( array( $MailFrom, $FromName ) );
			$mail->setSubject( $SiteName.JText::_( 'NEW EVENT MAIL' ) );
			$mail->setBody( $mailbody );

			$sent = $mail->Send();

		}//mail end

		$mainframe->redirect('index.php?option=com_eventlist&Itemid='.$Itemid.'&view=details&did='.$row->id, JText::_( 'EVENT SAVED' ) );

	//function saveevent end
	}

	/**
	 * Saves the registration to the database
	 *
	 * @since 0.7
	 */
	function userregister()
	{
		global $Itemid;

		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}

		$id = JRequest::getVar( 'rdid', 0, 'post', 'int' );

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
		global $Itemid;

		//check the token before we do anything else
		$token	= JUtility::getToken();
		if(!JRequest::getVar( $token, 0, 'post' )) {
			JError::raiseError(403, 'Request Forbidden');
		}

		$id = JRequest::getVar( 'rdid', 0, 'post', 'int' );

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
		JRequest::setVar( 'view', 'editevent' );
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

		require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'vCal.class.php');

		$v = new vCal();

		$v->setTimeZone($user_offset);
		$v->setSummary($row->club.'-'.$row->catname.'-'.$row->title);
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