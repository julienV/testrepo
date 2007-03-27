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
				$mainframe->redirect( 'index.php?option='.$option.'&Itemid='.$Itemid.'&view='.$view, JText::_( 'THE VENUE' ).' '.$this->_venue->club.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ) );
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
			$this->_venue->club				= '';
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
}
?>