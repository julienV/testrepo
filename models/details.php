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
 * EventList Component Details Model
 *
 * @package Joomla 
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelDetails extends JModel
{
	/**
	 * Details data in details array
	 *
	 * @var array
	 */
	var $_details = null;
	
	/**
	 * Pics in array
	 *
	 * @var array
	 */
	var $_pics = null;
	
	
	/**
	 * registeres in array
	 *
	 * @var array
	 */
	var $_registers = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('did', 0, '', 'int');
		$this->setId($id);
	}

	/**
	 * Method to set the details id
	 *
	 * @access	public
	 * @param	int	details ID number
	 */
	
	function setId($id)
	{
		// Set new details ID and wipe data
		$this->_id			= $id;
	}
	
	/**
	 * Method to get event data for the Detailsview
	 *
	 * @access public
	 * @return array
	 * @since 0.9
	 */
	function &getDetails( )
	{
		/*
		 * Load the Category data
		 */
		if ($this->_loadDetails())
		{
			$user	= & JFactory::getUser();
			
			// Is the category published?
			if (!$this->_details->published && $this->_details->catsid)
			{
				JError::raiseError( 404, JText::_("CATEGORY NOT PUBLISHED") );
			}
			
			// Do we have access to the category?
			if (($this->_details->access > $user->get('aid')) && $this->_details->catsid)
			{
				JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
			}
			
		}
		
		return $this->_details;
	}
	
	/**
	 * Method to load required data
	 *
	 * @access	private
	 * @return	array
	 * @since	0.9
	 */
	function _loadDetails()
	{		
		if (empty($this->_details))
		{
			// Get the WHERE clause
			$where	= $this->_buildDetailsWhere();
		
			$query = 'SELECT a.id AS did, a.dates, a.enddates, a.title, a.times, a.endtimes, a.datdescription, a.meta_keywords, a.meta_description, a.datimage, a.registra, a.unregistra, a.locid, a.catsid, a.uid,'
					. ' l.id AS locid, l.club, l.city, l.state, l.url, l.locdescription, l.locimage, l.city, l.plz, l.street, l.country, l.uid AS venueowner,' 
					. ' c.catname, c.published, c.access'
					. ' FROM #__eventlist_events AS a' 
					. ' LEFT JOIN #__eventlist_venues AS l ON a.locid = l.id'
					. ' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
					. $where
					;
    		$this->_db->setQuery($query);
			$this->_details = $this->_db->loadObject();
			return (boolean) $this->_details;
		}
		return true;
	}
	
	/**
	 * Method to build the WHERE clause of the query to select the details
	 *
	 * @access	private
	 * @return	string	WHERE clause
	 * @since	0.9
	 */
	function _buildDetailsWhere()
	{
		$where = ' WHERE a.id = '.$this->_id;

		return $where;
	}
	
	/**
	 * Method to check if the user is allready registered
	 *
	 * @access	public
	 * @return	array
	 * @since	0.9
	 */
	function getUsercheck()
	{
		// Initialize variables
		$user 		= & JFactory::getUser();
		$userid		= (int) $user->get('id', 0);
		
		//usercheck
		$query = 'SELECT urname'
				. ' FROM #__eventlist_register'
				. ' WHERE uid = '.$userid
				. ' AND rdid = '.$this->_id
				;
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Method to get the registered users
	 *
	 * @access	public
	 * @return	array
	 * @since	0.9
	 */
	function getRegisters()
	{						
		//Register holen
		$query = 'SELECT urname, uid'
				. ' FROM #__eventlist_register'
				. ' WHERE rdid = '.$this->_id
				;
		$this->_db->setQuery( $query );
		
		$_registers = $this->_db->loadObjectList();
		
		return $_registers;
	}
	
	/**
	 * Method to get the avatars of the registered users
	 *
	 * @access	public
	 * @return array
	 * @since	0.9
	 */
	function getAvatars()
	{
		// Initialize variables
		$_registers	= & $this->getRegisters();
		
		//get avatars

		foreach ($_registers as $register) {
			$query_avatar = 'SELECT avatar FROM #__comprofiler WHERE user_id= '. $register->uid .' AND avatarapproved = 1';
			$this->_db->setQuery( $query_avatar );
			$_pics = $this->_db->loadObjectList();
		}
		return $_pics;
	}
	
	/**
	 * Saves the registration to the database
	 *
	 * @access public
	 * @return true on success
	 * @since 0.7
	 */
	function userregister()
	{
		$user 	= & JFactory::getUser();
		
		$rdid 		= (int) $this->_id;
		$uid 		= (int) $user->get('id');
		$urname 	= $user->get('username');
		
		// Must be logged in
		if ($uid < 1) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
	
		//IP+time of registration
		$uregdate	= time();
		$uip 		= getenv('REMOTE_ADDR');
		
		$query = "INSERT INTO #__eventlist_register ( rdid, uid, urname, uregdate, uip )" .
					"\n VALUES ( $rdid, $uid, '$urname', $uregdate, '$uip' )";
		$this->_db->setQuery($query);
	
		if (!$this->_db->query()) {
				JError::raiseError( 500, $this->_db->stderr());
		}

		return true;
	}
	/**
	 * Deletes a registered user
	 *
	 */
	function delreguser()
	{		
		$user 	= & JFactory::getUser();
	
		$rdid 	= (int) $this->_id;
		$userid = $user->get('id');
		
		// Must be logged in
		if ($userid < 1) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
	
		$query = 'DELETE FROM #__eventlist_register WHERE rdid = '.$rdid.' AND uid= '.$userid;
		$this->_db->SetQuery( $query );
	
		if (!$this->_db->query()) {
				JError::raiseError( 500, $this->_db->getErrorMsg() );
		}
		
		return true;
	}
}
?>