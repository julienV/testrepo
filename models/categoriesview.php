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
 * EventList Component Categoriesview Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCategoriesview extends JModel
{
	/**
	 * Categories data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Categories total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe;

		// Get the paramaters of the active menu item
		$params = & $mainframe->getPageParameters('com_eventlist');

		//get the number of events from database
		$limit			= JRequest::getVar('limit', $params->get('cat_num'), '', 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the Categories
	 *
	 * @access public
	 * @return array
	 */
	function &getData( )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );

			$k = 0;
			for($i = 0; $i <  count($this->_data); $i++)
			{
				$category =& $this->_data[$i];

				$category->assignedevents = $this->_countcatevents( $category->catid );

				$k = 1 - $k;
			}

		}

		return $this->_data;
	}

	/**
	 * Total nr of Venues
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to load the Categories
	 *
	 * @access private
	 * @return array
	 */
	function _buildQuery()
	{
		//initialize some vars
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');

		// Get the paramaters of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		// show/hide empty categories
		$empty = null;
		if (!$params->get('empty_cat'))
		{
			$empty = "\n HAVING COUNT( a.id ) > 0";
		}

		//check archive task and ensure that only categories get selected if they contain a publishes/arcived event
		$task 		= JRequest::getVar('task', '', '', 'string');
		if($task == 'archive') {
			$eventstate = ' AND a.published = -1';
		} else {
			$eventstate = ' AND a.published = 1';
		}

		//get categories
		$query = 'SELECT c.*, c.id AS catid,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug'
				. ' FROM #__eventlist_categories AS c'
				. ' LEFT JOIN #__eventlist_events AS a ON a.catsid = c.id'
				. ' WHERE c.published = 1'
				. ' AND c.access <= '.$gid
				. $eventstate
				. ' GROUP BY c.id '.$empty
				. ' ORDER BY c.ordering'
				;

		return $query;
	}

	/**
	 * Method to get the total number
	 *
	 * @access private
	 * @return integer
	 */
	function _countcatevents( $id )
	{
		//initialize some vars
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');
		$id			= (int) $id;

		$task 		= JRequest::getVar('task', '', '', 'string');

		if($task == 'archive') {
			$where = ' WHERE a.published = -1 && a.catsid = '.$id;
		} else {
			$where = ' WHERE a.published = 1 && a.catsid = '.$id;
		}

		$query = 'SELECT COUNT(a.id)'
				. ' FROM #__eventlist_events AS a'
				. ' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
				. $where
				. ' AND c.access <= '.$gid
				;
		$this->_db->setQuery( $query );

  		return $this->_db->loadResult();
	}
}
?>