<?php
/**
 * @version 1.1 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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

		$id = JRequest::getInt('id');
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
		$app = & JFactory::getApplication();

		// Initialize variables
		$user		= & JFactory::getUser();
		$elsettings = & ELHelper::config();

		$view		= JRequest::getWord('view');

		if ($this->_id) {

			// Load the Event data
			$this->_loadVenue();

			/*
			* Error if allready checked out
			*/
			if ($this->_venue->isCheckedOut( $user->get('id') )) {
				$app->redirect( 'index.php?option=&view='.$view, JText::_( 'THE VENUE' ).' '.$this->_venue->venue.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ) );
			} else {
				$this->_venue->checkout( $user->get('id') );
			}

			//access check
			$allowedtoeditvenue = ELUser::editaccess($elsettings->venueowner, $this->_venue->created_by, $elsettings->venueeditrec, $elsettings->venueedit);

			if ($allowedtoeditvenue == 0) {

				JError::raiseError( 403, JText::_( 'NO ACCESS' ) );

			}


		} else {

			//access checks
			$delloclink = ELUser::validate_user( $elsettings->locdelrec, $elsettings->deliverlocsyes );

			if ($delloclink == 0) {
				JError::raiseError( 403, JText::_( 'NO ACCESS' ) );
			}
			
			//sticky forms
			$session = &JFactory::getSession();
			if ($session->has('venueform', 'com_eventlist')) {
				
				$venueform 		= $session->get('venueform', 0, 'com_eventlist');
				$this->_venue 	= & JTable::getInstance('eventlist_venues', '');
								
				if (!$this->_venue->bind($venueform)) {
					JError::raiseError( 500, $this->_db->stderr() );
					return false;
				}
			} else {
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
      			$this->_venue->latitude      	= '';
      			$this->_venue->longitude      	= '';
				$this->_venue->map				= $elsettings->showmapserv ? 1 : 0;
				$this->_venue->created			= '';
				$this->_venue->created_by		= '';
				$this->_venue->version			= 0;
				$this->_venue->author_ip		= '';
				$this->_venue->locimage			= '';
				$this->_venue->meta_keywords	= '';
				$this->_venue->meta_description	= '';
			}

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
		}
		return true;
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
		$app = & JFactory::getApplication();

		$user 		= & JFactory::getUser();
		$elsettings = & ELHelper::config();

		$tzoffset 		= $app->getCfg('offset');

		$row 		= & JTable::getInstance('eventlist_venues', '');
	
		//bind it to the table
		if (!$row->bind($data)) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		//Are we saving from an item edit?
		if ($row->id) {

			//check if user is allowed to edit venues
			$allowedtoeditvenue = ELUser::editaccess($elsettings->venueowner, $row->created_by, $elsettings->venueeditrec, $elsettings->venueedit);

			if ($allowedtoeditvenue == 0) {
				$row->checkin();
				$app->enqueueMessage( JText::_( 'NO ACCESS' ) );
				return false;
			}

			$row->modified 		= gmdate('Y-m-d H:i:s');

			$row->modified_by 	= $user->get('id');

			//Is editor the owner of the venue
			//This extra Check is needed to make it possible
			//that the venue is published after an edit from an owner
			if ($elsettings->venueowner == 1 && $row->created_by == $user->get('id')) {
				$owneredit = 1;
			} else {
				$owneredit = 0;
			}

		} else {

			//check if user is allowed to submit new venues
			$delloclink = ELUser::validate_user( $elsettings->locdelrec, $elsettings->deliverlocsyes );

			if ($delloclink == 0){
				$app->enqueueMessage( JText::_( 'NO ACCESS' ) );
				return false;
			}

			//get IP, time and userid
			$row->created 			= gmdate('Y-m-d H:i:s');

			$row->author_ip 		= $elsettings->storeip ? getenv('REMOTE_ADDR') : 'DISABLED';
			$row->created_by		= $user->get('id');

			//set owneredit to false
			$owneredit = 0;
		}

		//Image upload

		//If image upload is required we will stop here if no file was attached
		if ( empty($file['name']) && $elsettings->imageenabled == 2 ) {
			$this->setError( JText::_( 'IMAGE EMPTY' ) );
			return false;
		}

		if ( ( $elsettings->imageenabled == 2 || $elsettings->imageenabled == 1 ) && ( !empty($file['name'])  ) )  {

			jimport('joomla.filesystem.file');

			$base_Dir 	= JPATH_SITE.'/images/eventlist/venues/';

			//check the image
			$check = ELImage::check($file, $elsettings);

			if ($check === false) {
				$app->redirect($_SERVER['HTTP_REFERER']);
			}

			//sanitize the image filename
			$filename = ELImage::sanitize($base_Dir, $file['name']);
			$filepath = $base_Dir . $filename;

			if (!JFile::upload( $file['tmp_name'], $filepath )) {
				$this->setError( JText::_( 'UPLOAD FAILED' ) );
				return false;
			} else {
				$row->locimage = $filename;
			}
		} else {
			//keep image if edited and left blank
			$row->locimage = $row->curimage;
		}//end image upload if

		//Check description
		$editoruser = ELUser::editoruser();

		if (!$editoruser) {
			//check description --> wipe out code
			$row->locdescription = strip_tags($row->locdescription, '<br><br/>');

			//convert the linux \n (Mac \r, Win \r\n) to <br /> linebreaks
			$row->locdescription = str_replace(array("\r\n", "\r", "\n"), "<br />", $row->locdescription);

			//cut too long words
			$row->locdescription = wordwrap($row->locdescription, 75, " ", 1);

			//check length
			$length = JString::strlen($row->locdescription);
			if ($length > $elsettings->datdesclimit) {

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

		$row->version++;
		
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

		return $row->id;
	}
}
?>