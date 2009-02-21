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
 * EventList Component Event Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelEvent extends JModel
{
	/**
	 * Event id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Event data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$this->setId($cid[0]);
	}

	/**
	 * Method to set the identifier
	 *
	 * @access	public
	 * @param	int event identifier
	 */
	function setId($id)
	{
		// Set event id and wipe data
		$this->_id	    = $id;
		$this->_data	= null;
	}

	/**
	 * Logic for the event edit screen
	 *
	 */
	function &getData()
	{

		if ($this->_loadData())
		{

		}
		else  $this->_initData();

		return $this->_data;
	}

	/**
	 * Method to load content event data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT e.*, v.venue'
					. ' FROM #__eventlist_events AS e'
					. ' LEFT JOIN #__eventlist_venues AS v ON v.id = e.locid'
					. ' WHERE e.id = '.$this->_id
					;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the event data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$createdate = & JFactory::getDate();
			
			$event = new stdClass();
			$event->id					= 0;
			$event->locid				= 0;
			$event->catsid				= 0;
			$event->dates				= null;
			$event->enddates			= null;
			$event->times				= null;
			$event->endtimes			= null;
			$event->title				= null;
			$event->alias				= null;
			$event->created				= $createdate->toUnix();
			$event->author_ip			= null;
			$event->created_by			= null;
			$event->published			= 1;
			$event->registra			= 0;
			$event->unregistra			= 0;
			$event->datdescription		= null;
			$event->meta_keywords		= null;
			$event->meta_description	= null;
			$event->recurrence_number	= 0;
			$event->recurrence_type		= 0;
			$event->recurrence_limit_date	= '0000-00-00';
      $event->recurrence_limit = 0;
      $event->recurrence_counter = 0;
      $event->recurrence_byday = '';
			$event->datimage			= JText::_('SELECTIMAGE');
			$event->venue				= JText::_('SELECTVENUE');
			$event->hits				= 0;
			$event->version				= 0;
			$event->modified			= $this->_db->getNullDate();
			$this->_data				= $event;
			return (boolean) $this->_data;
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
			$event = & JTable::getInstance('eventlist_events', '');
			return $event->checkin($this->_id);
		}
		return false;
	}

	/**
	 * Method to checkout/lock the item
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the item out
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function checkout($uid = null)
	{
		if ($this->_id)
		{
			// Make sure we have a user id to checkout the event with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$event = & JTable::getInstance('eventlist_events', '');
			return $event->checkout($uid, $this->_id);
		}
		return false;
	}

	/**
	 * Tests if the event is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	0.9
	 */
	function isCheckedOut( $uid=0 )
	{
		if ($this->_loadData())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		} elseif ($this->_id < 1) {
			return false;
		} else {
			JError::raiseWarning( 0, 'Unable to Load Data');
			return false;
		}
	}

	/**
	 * Method to store the event
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		//$app = & JFactory::getApplication();

		$elsettings = ELAdmin::config();
		$user		= & JFactory::getUser();

		$cats 		= JRequest::getVar( 'cid', array(), 'post', 'array');
		
		$row =& JTable::getInstance('eventlist_events', '');

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		//get values from time selectlist and concatenate them accordingly
		$starthours		= JRequest::getCmd( 'starthours');
		$startminutes	= JRequest::getCmd( 'startminutes');
		$endhours		= JRequest::getCmd( 'endhours');
		$endminutes		= JRequest::getCmd( 'endminutes');
		
		$row->times		= $starthours.':'.$startminutes;
		$row->endtimes	= $endhours.':'.$endminutes;
		
		// Check/sanitize the metatags
		$row->meta_description = htmlspecialchars(trim(addslashes($row->meta_description)));
		if (JString::strlen($row->meta_description) > 255) {
			$row->meta_description = JString::substr($row->meta_description, 0, 254);
		}

		$row->meta_keywords = htmlspecialchars(trim(addslashes($row->meta_keywords)));
		if (JString::strlen($row->meta_keywords) > 200) {
			$row->meta_keywords = JString::substr($row->meta_keywords, 0, 199);
		}

		//Check if image was selected
		jimport('joomla.filesystem.file');
		$format 	= JFile::getExt('JPATH_SITE/images/eventlist/events/'.$row->datimage);

		$allowable 	= array ('gif', 'jpg', 'png');
		if (in_array($format, $allowable)) {
			$row->datimage = $row->datimage;
		} else {
			$row->datimage = '';
		}

		// sanitise id field
		$row->id = (int) $row->id;

		$nullDate	= $this->_db->getNullDate();

		// Are we saving from an item edit?
		if ($row->id) {
			$row->modified 		= gmdate('Y-m-d H:i:s');
			$row->modified_by 	= $user->get('id');
		} else {
			$row->modified 		= $nullDate;
			$row->modified_by 	= '';

			//get IP, time and userid
			$row->created 			= gmdate('Y-m-d H:i:s');

			$row->author_ip 		= $elsettings->storeip ? getenv('REMOTE_ADDR') : 'DISABLED';
			$row->created_by		= $user->get('id');
		}
		
		$row->version++;

		// Make sure the data is valid
		if (!$row->check($elsettings)) {
			$this->setError($row->getError());
			return false;
		}

		// Store the table to the database
		if (!$row->store(true)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		//store cat relation
		$query = 'DELETE FROM #__eventlist_cats_event_relations WHERE itemid = '.$row->id;
		$this->_db->setQuery($query);
		$this->_db->query();
			
		foreach($cats as $cat)
		{
			$query = 'INSERT INTO #__eventlist_cats_event_relations (`catid`, `itemid`) VALUES(' . $cat . ',' . $row->id . ')';
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		return $row->id;
	}
	
	/**
	 * Fetch event hits
	 *
	 * @param int $id
	 * @return int
	 */
	function gethits($id)
	{
		$query = 'SELECT hits FROM #__eventlist_events WHERE id = '.(int)$id;
		$this->_db->setQuery($query);
		$hits = $this->_db->loadResult();
		
		return $hits;
	}
	
	/**
	 * Reset hitcount
	 *
	 * @param int $id
	 * @return int
	 */
	function resetHits($id)
	{
		$row  =& $this->getTable('eventlist_events', '');
		$row->load($id);
		$row->hits = 0;
		$row->store();
		$row->checkin();
		return $row->id;
	}
	
	/**
	 * Get assigned cats
	 *
	 * @return array
	 */
	function getCatsselected()
	{
		$query = 'SELECT DISTINCT catid FROM #__eventlist_cats_event_relations WHERE itemid = ' . (int)$this->_id;
		$this->_db->setQuery($query);
		$used = $this->_db->loadResultArray();
		return $used;
	}
}
?>