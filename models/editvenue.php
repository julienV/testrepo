<?php 
/**
 * @version 0.9 $Id$
 * @package Joomla 
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * EventList Component Editvenue Model
 *
 * @package Joomla 
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelEditvenue extends JModel
{
	/**
	 * Venue data in Venue array
	 *
	 * @var array
	 */
	var $_venue = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id);
	}

	/**
	 * Method to set the Venue id
	 *
	 * @access	public
	 * @param	int	Venue ID number
	 */
	function setId($id)
	{
		// Set new venue ID
		$this->_id			= $id;
	}
	
	/**
	 * Logic to get the venue
	 *
	 * @return array
	 */
	function &getVenue(  )
	{
		global $mainframe, $option, $Itemid;

		// Initialize variables
		$user		= & JFactory::getUser();
		$elsettings = ELHelper::config();
		
		$view		= JRequest::getVar('view', '', '', 'string');

		if ($this->_id) {
		
			// Load the Event data
			$this->_loadVenue();

			/*
			* TODO Lang strings
			* Error if allready checked out
			*/
			if ($this->_venue->isCheckedOut( $user->get('id') )) {
				$mainframe->redirect( 'index.php?option='.$option.'&Itemid='.$Itemid.'&view='.$view, JText::_( 'THE VENUE' ).' '.$this->_venue->venue.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ) );
			} else {
				$this->_venue->checkout( $user->get('id') );
			}
			
			/*
			* access check
			*/
			$owner = $this->getOwner();
			
			$allowedtoeditvenue = ELUser::editaccess($elsettings->venueowner, $owner->created_by, $user->get('id'), $elsettings->venueeditrec, $elsettings->venueedit);
			
			if ($allowedtoeditvenue == 0) {
				
				JError::raiseError( 403, JText::_( 'NO ACCESS' ) );
				
			}
			

		} else {
			
			/*
			* access checks
			*/
			$delloclink = ELUser::validate_user( $elsettings->locdelrec, $elsettings->deliverlocsyes );
			
			if ($delloclink == 0) {
				
				JError::raiseError( 403, JText::_( 'NO ACCESS' ) );
				
			}

			//prepare output
			$this->_venue->id				= '';
			$this->_venue->venue			= '';
			$this->_venue->url				= '';
			$this->_venue->street			= '';
			$this->_venue->plz				= '';
			$this->_venue->locdescription	= '';
			$this->_venue->city				= '';
			$this->_venue->state			= '';
			$this->_venue->country			= '';
			$this->_venue->sendernameloc	= '';
			$this->_venue->sendermailloc	= '';
			$this->_venue->locimage			= '';

		}

		return $this->_venue;

	}
	
	/**
	 * logic to get the venue
	 *
	 * @access private
	 * @return array
	 */
	function _loadVenue( )
	{
		
		if (empty($this->_venue)) {

			$this->_venue =& JTable::getInstance('eventlist_venues', '');

			$this->_venue->load( $this->_id );

			return $this->_venue;

		} else {

			return true;

		}
	}
	
	/**
	 * Logic to get the owner
	 *
	 * @return integer
	 */
	function getOwner( )
	{		
		$query = 'SELECT l.created_by'
				. ' FROM #__eventlist_venues AS l' 
				. ' WHERE l.id = '.$this->_id
				;
		$this->_db->setQuery( $query );
				
    	return $this->_db->loadObject();
	}
	
	/**
	 * Method to checkin/unlock the item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function checkin()
	{
		if ($this->_id)
		{
			$item = & $this->getTable('eventlist_venues', '');
			if(! $item->checkin($this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Method to store the venue
	 *
	 * @access	public
	 * @return	id
	 * @since	0.9
	 */
	function store($data, $file)
	{
		global $mainframe, $option;
		
		jimport('joomla.utilities.date');

		$user 		= & JFactory::getUser();
		$elsettings = ELHelper::config();

		//Get mailinformation
		$SiteName 		= $mainframe->getCfg('sitename');
		$MailFrom	 	= $mainframe->getCfg('mailfrom');
		$FromName 		= $mainframe->getCfg('fromname');
		
		$sizelimit 	= $elsettings->sizelimit*1024; //size limit in kb
		$base_Dir = JPATH_SITE.'/images/eventlist/venues/';

		$row =& JTable::getInstance('eventlist_venues', '');

		//bind it to the table
		if (!$row->bind($data)) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		$datenow = new JDate();

		//Are we saving from an item edit?
		if ($row->id) {

			$owner = ELUser::isOwner($row->id, 'venues');

			//check if user is allowed to edit venues
			$allowedtoeditvenue = ELUser::editaccess($elsettings->venueowner, $owner, $elsettings->venueeditrec, $elsettings->venueedit);

			if ($allowedtoeditvenue == 0) {
				$row->checkin();
				$mainframe->enqueueMessage( JText::_( 'NO ACCESS' ) );
				return false;
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
				$mainframe->enqueueMessage( JText::_( 'NO ACCESS' ) );
				return false;
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
				$this->setError( JText::_( 'IMAGE EMPTY' ) );
				return false;
			}

			if ($imagesize > $sizelimit) {
				$this->setError( JText::_( 'IMAGE FILE SIZE' ) );
				return false;
			}

			if (file_exists($base_Dir.$file['name'])) {
				$this->setError( JText::_( 'IMAGE EXISTS' ) );
				return false;
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
				$this->setError( JText::_( 'WRONG IMAGE FILE TYPE' ) );
				return false;
			}

			if (!JFile::upload($file['tmp_name'], $base_Dir.strtolower($file['name']))) {
				$this->setError( JText::_( 'UPLOAD FAILED' ) );
				return false;
			} else {
				$row->locimage = strtolower($file['name']);
			}
		} else {
			//keep image if edited and left blank
			$row->locimage = $row->curimage;
		}//end image upload if

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
				//if shortened add ...
				$row->locdescription = $row->locdescription.'...';
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

		//Make sure the data is valid
		if (!$row->check($elsettings)) {
			$this->setError($row->getError());
			return false;
		}

		//store it in the db
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		//update item order
		$row->reorder();
		
		//create mail
		if (($elsettings->mailinform == 2) || ($elsettings->mailinform == 3)) {

			$this->_db->setQuery("SELECT username, email FROM #__users"
						. "\nWHERE id = ".$user->get('id')
						);

			$rowuser = $this->_db->loadObject();
			
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
			$mailbody .= JText::_( 'VENUE' ).': '.$row->venue.' \n';
			$mailbody .= JText::_( 'WEBSITE' ).': '.$row->url.' \n';
			$mailbody .= JText::_( 'STREET' ).': '.$row->street.' \n';
			$mailbody .= JText::_( 'ZIP' ).': '.$row->plz.' \n';
			$mailbody .= JText::_( 'CITY' ).': '.$row->city.' \n';
			$mailbody .= JText::_( 'COUNTRY' ).': '.$row->country.' \n';
			$mailbody .= ' \n';
			$mailbody .= JText::_( 'DESCRIPTION' ).': '.$row->locdescription.' \n';

			jimport('joomla.utilities.mail');

			$mail = new JMail();

			//$mail->addRecipient( $elsettings->mailinformrec );
			$mail->addRecipient( array( $elsettings->mailinformrec, $elsettings->mailinformrec2 )  );
			$mail->setSender( array( $MailFrom, $FromName ) );
			$mail->setSubject( $SiteName.JText::_( 'NEW VENUE MAIL' ) );
			$mail->setBody( $mailbody );

			$sent = $mail->Send();
		}
		
		return $row->id;
	}
}
?>