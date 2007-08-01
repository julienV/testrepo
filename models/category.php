<?php
/**
 * @version 0.9 $Id: category.php 64 2007-04-14 12:09:16Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

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
 * EventList Component Category Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCategory extends JModel
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
	 * categories data array
	 *
	 * @var array
	 */
	var $_categories = null;

	/**
	 * Events total
	 *
	 * @var integer
	 */
	var $_totalCategories = null;

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
		global $mainframe;
		parent::__construct();

		$id = JRequest::getInt('cid');
		$this->setId($id);

		// Get the paramaters of the active menu item
		$params 	= & $mainframe->getPageParameters('com_eventlist');

		//get the number of events from database
		$limit       	= $mainframe->getUserStateFromRequest('com_eventlist.eventlist.limit', 'limit', $params->def('display_num', 0), 'int');
		$limitstart		= JRequest::getInt('limitstart');

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
		$pop	= JRequest::getBool('pop');

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
		$where		= $this->_buildCategoryWhere();
		$orderby	= $this->_buildCategoryOrderBy();

		//Get Events from Database
		$query = 'SELECT a.id, a.dates, a.enddates, a.times, a.endtimes, a.title, a.locid, a.datdescription, a.created, l.venue, l.city, l.state, l.url, c.catname, c.id AS catid, '
				. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
				. ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', a.locid, l.alias) ELSE a.locid END as venueslug,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as categoryslug'
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
	function _buildCategoryOrderBy()
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
	function _buildCategoryWhere( )
	{
		global $mainframe;

		$user		=& JFactory::getUser();
		$gid		= (int) $user->get('aid');

		// Get the paramaters of the active menu item
		$params 	= & $mainframe->getPageParameters('com_eventlist');

		$task 		= JRequest::getWord('task');

		// First thing we need to do is to select only the requested events
		if ($task == 'catarchive') {
			$where = ' WHERE a.published = -1 && a.catsid = '.$this->_id;
		} else {
			if(JRequest::getVar('layout', 'default') == 'default') {
				$where = ' WHERE a.published = 1 && a.catsid = '.$this->_id;
			} else {
				$where = ' WHERE a.published = 1';
			}
		}

		// Second is to only select events assigned to category the user has access to
		$where .= ' AND c.access <= '.$gid;

		/*
		 * If we have a filter, and this is enabled... lets tack the AND clause
		 * for the filter onto the WHERE clause of the content item query.
		 */
		if ($params->get('filter'))
		{
			$filter 		= JRequest::getString('filter');
			$filter_type 	= JRequest::getWord('filter_type');

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
		$query = 'SELECT *, '
			.'CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug '
			.'FROM #__eventlist_categories WHERE id = '.$this->_id;
		$this->_db->setQuery( $query );

		$_category = $this->_db->loadObject();

		return $_category;
	}

	/**
	 * Method to get the Categories
	 *
	 * @access public
	 * @return array
	 */
	function &getCategories( )
	{
		$task = JRequest::getWord('task');

		// Lets load the content if it doesn't already exist
		if (empty($this->_categories))
		{
			$query = $this->_buildCategoriesQuery();
			$this->_categories = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );

			$k = 0;
			for($i = 0; $i <  count($this->_categories); $i++)
			{
				$category =& $this->_categories[$i];

				if( $task == 'archive' ) {

					$category->assignedevents = $this->_countarchiveevents( $category->catid );

				} else {

					$category->assignedevents = $this->_countcatevents( $category->catid );

				}

				$k = 1 - $k;
			}

		}

		return $this->_categories;
	}

	/**
	 * Total nr of Venues
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalCategories()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_totalCategories))
		{
			$query = $this->_buildCategoriesQuery();
			$this->_totalCategories = $this->_getListCount($query);
		}

		return $this->_totalCategories;
	}

	/**
	 * Method to load the Categories
	 *
	 * @access private
	 * @return array
	 */
	function _buildCategoriesQuery()
	{
		global $mainframe;
		//initialize some vars
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');

		// Get the paramaters of the active menu item
		$params 	= & $mainframe->getPageParameters('com_eventlist');

		// show/hide empty categories
		$empty = null;
		if (!$params->get('empty_cat'))
		{
			$empty = "\n HAVING COUNT( a.id ) > 0";
		}

		//check archive task and ensure that only categories get selected if they contain a publishes/arcived event
		$task 		= JRequest::getWord('task');
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

		$query = 'SELECT COUNT(a.id)'
				. ' FROM #__eventlist_events AS a'
				. ' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
				. ' WHERE a.published = 1 && a.catsid = '.$id
				. ' AND c.access <= '.$gid
				;
		$this->_db->setQuery( $query );

  		return $this->_db->loadResult();
	}

	/**
	 * Method to get the total number of archived events
	 *
	 * @access private
	 * @return integer
	 */
	function _countarchiveevents( $id )
	{
		//initialize some vars
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');
		$id			= (int) $id;

		$query = 'SELECT COUNT(a.id)'
				. ' FROM #__eventlist_events AS a'
				. ' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
				. ' WHERE a.published = -1 && a.catsid = '.$id
				. ' AND c.access <= '.$gid
				;
		$this->_db->setQuery( $query );

  		return $this->_db->loadResult();
	}
}
?>