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
	 * Categories data array
	 *
	 * @var array
	 */
	var $_categories = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
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

	//	$this->_loadData();
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
			$query = 'SELECT *'
					. ' FROM #__eventlist_events'
					. ' WHERE id = '.$this->_id
					;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to get the category data
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function &getCategories()
	{
		$query = 'SELECT id AS value, catname AS text'
				. ' FROM #__eventlist_categories'
				. ' WHERE published = 1'
				. ' ORDER BY ordering'
				;
		$this->_db->setQuery( $query );

		$this->_categories = $this->_db->loadObjectList();

		return $this->_categories;
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
			$event = new stdClass();
			$event->id					= 0;
			$event->locid				= 0;
			$event->catsid				= 0;
			$event->dates				= '0000-00-00';
			$event->enddates			= '0000-00-00';
			$event->times				= '00:00:00';
			$event->endtimes			= '00:00:00';
			$event->title				= null;
			$event->published			= 1;
			$event->registra			= 0;
			$event->unregistra			= 0;
			$event->datdescription		= null;
			$event->meta_keywords		= null;
			$event->meta_description	= null;
			$event->datimage			= JText::_('SELECTIMAGE');
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
			$item = & $this->getTable('eventlist_events', '');
			if(! $item->checkin($this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
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
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$item = & $this->getTable('eventlist_events', '');
			if(!$item->checkout($uid, $this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
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
		global $mainframe, $option;

		$elsettings = ELAdmin::config();

		$row =& JTable::getInstance('eventlist_events', '');

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		/*
		* Check date format
		* TODO: move to table
		*/
		if (isset($row->dates)) {
			if (!preg_match("/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/", $row->dates)) {
				$row->checkin();
				$mainframe->redirect( 'index.php?option='.$option.'&view=events', JText::_( 'DATE WRONG FORMAT') );
			}
		}

		if ($row->enddates != 0) {
			if (!preg_match("/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/", $row->enddates)) {
				$row->checkin();
				$mainframe->redirect( 'index.php?option='.$option.'&view=events', JText::_( 'ENDDATE WRONG FORMAT') );
			}
		}


		// Check/sanitize the metatags
		$row->meta_description = htmlspecialchars(trim(addslashes($row->meta_description)));
		if (JString::strlen($row->meta_description) > 255) {
			$row->meta_description = JString::substr($row->meta_description, 0, 254);
		}

		$row->meta_keywords = htmlspecialchars(trim(addslashes($row->meta_keywords)));
		if (JString::strlen($row->meta_keywords) > 200) {
			$row->meta_keywords = JString::substr($row->meta_keywords, 0, 199);
		}

		/*
		* Check time format
		*/
		if ( $elsettings->showtime == 1 ) {
			if (isset($row->times)) {
   				if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $row->times)) {
     		 		$row->checkin();
   					$mainframe->redirect( 'index.php?option='.$option.'&view=events', JText::_( 'TIME WRONG FORMAT') );
			  	}
			}
			if ($row->endtimes != 0) {
   				if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $row->endtimes)) {
     		 		$row->checkin();
   					$mainframe->redirect( 'index.php?option='.$option.'&view=events', JText::_( 'TIME WRONG FORMAT') );
	 		 	}
			}
		}

		/*
		* No venue or category choosen?
		*/
		if($row->locid == '') {
  	      	$row->checkin();
			$mainframe->redirect( 'index.php?option='.$option.'&view=venue', JText::_( 'VENUE EMPTY') );
		}

		if($row->catsid == 0) {
 	       	$row->checkin();
			$mainframe->redirect( 'index.php?option='.$option.'&view=categories', JText::_( 'CATEGORY EMPTY') );
		}

		/*
		* Check if image was selected
		*/
		jimport('joomla.filesystem.file');
		$format 	= JFile::getExt('JPATH_SITE/images/eventlist/events/'.$row->datimage);

		$allowable 	= array ('gif', 'jpg', 'png');
		if (in_array($format, $allowable)) {
			$row->datimage = $row->datimage;
		} else {
			$row->datimage = '';
		}

		// Store the table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$row->checkin();

		return true;
	}
}
?>