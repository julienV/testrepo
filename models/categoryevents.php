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
 * EventList Component Categoryevents Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCategoryevents extends JModel
{
	/**
	 * Events data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * category data array
	 *
	 * @var array
	 */
	var $_category = null;

	/**
	 * Events total
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

		global $mainframe, $option;

		$id = JRequest::getVar('categid', 0, '', 'int');
		$this->setId($id);

		// Get the paramaters of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		//get the number of events from database
		$limit       	= $mainframe->getUserStateFromRequest('com_eventlist.eventlist.limit', 'limit', $params->def('display_num', 0));
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to set the category id
	 *
	 * @access	public
	 * @param	int	category ID number
	 */
	function setId($id)
	{
		// Set new category ID and wipe data
		$this->_id			= $id;
		$this->_data		= null;
	}

	/**
	 * Method to get the events
	 *
	 * @access public
	 * @return array
	 */
	function &getData( )
	{
		$pop	= JRequest::getVar('pop', 0, '', 'int');

		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();

			if ($pop) {
				$this->_data = $this->_getList( $query );
			} else {
				$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
			}
		}

		return $this->_data;
	}

	/**
	 * Total nr of events
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
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Build the query
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		//Get Events from Database
		$query = 'SELECT a.id, a.dates, a.enddates, a.times, a.endtimes, a.title, a.locid, a.datdescription, l.venue, l.city, l.state, l.url, c.catname, c.id AS catid, '
				. ' CASE WHEN CHAR_LENGTH(a.title) THEN CONCAT_WS(\':\', a.id, a.title) ELSE a.id END as slug,'
				. ' CASE WHEN CHAR_LENGTH(l.venue) THEN CONCAT_WS(\':\', a.locid, l.venue) ELSE a.locid END as venueslug,'
				. ' CASE WHEN CHAR_LENGTH(c.catname) THEN CONCAT_WS(\':\', c.id, c.catname) ELSE c.id END as categoryslug'
				. ' FROM #__eventlist_events AS a'
				. ' LEFT JOIN #__eventlist_venues AS l ON l.id = a.locid'
				. ' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
				. $where
				. $orderby
				;

		return $query;
	}

	/**
	 * Build the order clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.venueevents.filter_order', 		'filter_order', 	'a.dates' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.venueevents.filter_order_Dir',	'filter_order_Dir',	'' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', a.dates, a.times';

		return $orderby;
	}

	/**
	 * Method to build the WHERE clause
	 *
	 * @access private
	 * @return array
	 */
	function _buildContentWhere( )
	{
		$user		=& JFactory::getUser();
		$gid		= (int) $user->get('aid');

		// Get the paramaters of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		$task 		= JRequest::getVar('task', '', '', 'string');

		// First thing we need to do is to select only the requested events
		if ($task == 'catarchive') {
			$where = ' WHERE a.published = -1 && a.catsid = '.$this->_id;
		} else {
			$where = ' WHERE a.published = 1 && a.catsid = '.$this->_id;
		}

		// Second is to only select events assigned to category the user has access to
		$where .= ' AND c.access <= '.$gid;

		/*
		 * If we have a filter, and this is enabled... lets tack the AND clause
		 * for the filter onto the WHERE clause of the content item query.
		 */
		if ($params->get('filter'))
		{
			$filter 		= JRequest::getVar('filter', '', 'request');
			$filter_type 	= JRequest::getVar('filter_type', '', 'request');

			if ($filter)
			{
				// clean filter variables
				$filter 		= JString::strtolower($filter);
				$filter_type 	= JString::strtolower($filter_type);

				switch ($filter_type)
				{
					case 'title' :
						$where .= ' AND LOWER( a.title ) LIKE "%'.$filter.'%"';
						break;

					case 'venue' :
						$where .= ' AND LOWER( l.venue ) LIKE "%'.$filter.'%"';
						break;

					case 'city' :
						$where .= ' AND LOWER( l.city ) LIKE "%'.$filter.'%"';
						break;
				}
			}
		}
		return $where;
	}

	/**
	 * Method to get the Category
	 *
	 * @access public
	 * @return integer
	 */
	function getCategory( )
	{
		$query = 'SELECT * FROM #__eventlist_categories WHERE id = '.$this->_id;

		$this->_db->setQuery( $query );

		$_category = $this->_db->loadObject();

		return $_category;
	}
}
?>