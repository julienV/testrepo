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
 * EventList Component Category Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCategory extends JModel
{
	/**
	 * Category id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Groups data array
	 *
	 * @var array
	 */
	var $_groups = null;

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
	 * @param	int category identifier
	 */
	function setId($id)
	{
		// Set category id and wipe data
		$this->_id	    = $id;
		$this->_data	= null;
	}

	/**
	 * Method to get content category data
	 *
	 * @access	public
	 * @return	array
	 * @since	0.9
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
	 * Method to get the group data
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function &getGroups()
	{
		$query = 'SELECT id AS value, name AS text'
			. ' FROM #__eventlist_groups'
			. ' ORDER BY name'
			;
		$this->_db->setQuery( $query );

		$this->_groups = $this->_db->loadObjectList();

		return $this->_groups;
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
					. ' FROM #__eventlist_categories'
					. ' WHERE id = '.$this->_id
					;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the category data
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
			$category = new stdClass();
			$category->id					= 0;
			$category->parent_id			= 0;
			$category->groupid				= 0;
			$category->catname				= null;
			$category->catdescription		= null;
			$category->meta_description		= null;
			$category->meta_keywords		= null;
			$category->published			= 1;
			$category->image				= JText::_('SELECTIMAGE');
			$category->access				= 0;
			$this->_data					= $category;
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
			$item = & $this->getTable('eventlist_categories', '');
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
			$item = & $this->getTable('eventlist_categories', '');
			if(!$item->checkout($uid, $this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}

	/**
	 * Tests if the category is checked out
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
	 * Method to store the category
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		global $mainframe, $option;

		$row  =& $this->getTable('eventlist_categories', '');

		// bind it to the table
		if (!$row->bind($data)) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		// Not typed in a category name?
		if(empty($row->catname)) {
			$row->checkin();
			$mainframe->redirect('index.php?option='.$option.'&view=categories', JText::_( 'ADD NAME CATEGORY' ) );
		}

		// Make sure the data is valid
		if (!$row->check()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		// Store it in the db
		if (!$row->store()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		$row->checkin();
		$row->reorder();

		return true;
	}

	/**
	 * Method to set the access level of the category
	 *
	 * @access	public
	 * @param integer id of the category
	 * @param integer access level
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function access($id, $access)
	{
		$row  =& $this->getTable('eventlist_categories', '');

		$row->load( $id );
		$row->access = $access;

		if ( !$row->check() ) {
			return $row->getError();
		}
		if ( !$row->store() ) {
			return $row->getError();
		}

		return true;
	}
}
?>