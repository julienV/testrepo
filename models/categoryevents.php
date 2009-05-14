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
 * EventList Component Categoryevents Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCategoryevents extends JModel
{
	/**
	 * Category id
	 *
	 * @var int
	 */
	var $_id = null;
	
	/**
	 * Categories items Data
	 *
	 * @var mixed
	 */
	var $_data = null;

	/**
	 * Childs
	 *
	 * @var mixed
	 */
	var $_childs = null;

	/**
	 * category data array
	 *
	 * @var array
	 */
	var $_category = null;

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

		$app = & JFactory::getApplication();

		$id = JRequest::getInt('id');
		$this->setId((int)$id);

		// Get the paramaters of the active menu item
		$params 	= & $app->getParams();

		//get the number of events from database
		$limit       	= $app->getUserStateFromRequest('com_eventlist.categoryevents.limit', 'limit', $params->def('display_num', 0), 'int');
		$limitstart		= JRequest::getInt('limitstart');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// Get the filter request variables
		$this->setState('filter_order', JRequest::getCmd('filter_order', 'a.dates'));
		$this->setState('filter_order_dir', JRequest::getWord('filter_order_Dir', 'ASC'));
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

			$k = 0;
			$count = count($this->_data);
			for($i = 0; $i < $count; $i++)
			{
				$item =& $this->_data[$i];
				$item->categories = $this->getCategories($item->id);
			
				//child categories
			//	$query	= $this->_buildChildsQuery( $item->id );
			//	$this->_db->setQuery($query);
			//	$item->categories = $this->_db->loadObjectList();
				
				//remove events without categories (users have no access to them)
				if (empty($item->categories)) {
					unset($this->_data[$i]);
				}
				
				$k = 1 - $k;
			}

		return $this->_data;
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
	 * Total nr of Categories
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
		$query = 'SELECT DISTINCT a.id, a.dates, a.enddates, a.times, a.endtimes, a.title, a.locid, a.datdescription, a.created, l.venue, l.city, l.state, l.url,'
				. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
				. ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', a.locid, l.alias) ELSE a.locid END as venueslug'
				. ' FROM #__eventlist_events AS a'
				. ' INNER JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = a.id'
				. ' INNER JOIN #__eventlist_categories AS c ON c.id = rel.catid'
        		. ' LEFT JOIN #__eventlist_venues AS l ON l.id = a.locid'
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
		$filter_order		= $this->getState('filter_order');
		$filter_order_dir	= $this->getState('filter_order_dir');
		
		$filter_order		= JFilterInput::clean($filter_order, 'cmd');
		$filter_order_dir	= JFilterInput::clean($filter_order_dir, 'word');

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_dir.', a.dates, a.times';

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
		$app = & JFactory::getApplication();

		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');

		// Get the paramaters of the active menu item
		$params 	= & $app->getParams();

		$task 		= JRequest::getWord('task');

		// First thing we need to do is to select only the requested events
		if ($task == 'archive') {
			$where = ' WHERE a.published = -1 ';
		} else {
			$where = ' WHERE a.published = 1 ';
		}
		
		// display event from direct childs ?
		if (!$params->get('displayChilds', 0)) {
			$where .= ' AND rel.catid = '.$this->_id;
		} else {
      		$where .= ' AND (rel.catid = '.$this->_id . ' OR c.parent_id = '.$this->_id . ')';			
		}
		
		// display all event of recurring serie ?
    	if ($params->get('only_first',0)) {
     		$where .= ' AND a.recurrence_first_id = 0 ';
    	}

		// only select events assigned to category the user has access to
		$where .= ' AND c.access <= '.$gid;

		/*
		 * If we have a filter, and this is enabled... lets tack the AND clause
		 * for the filter onto the WHERE clause of the content item query.
		 */
		if ($params->get('filter'))
		{
			$filter 		= JRequest::getString('filter', '', 'request');
			$filter_type 	= JRequest::getWord('filter_type', '', 'request');

			if ($filter)
			{
				// clean filter variables
				$filter 		= JString::strtolower($filter);
				$filter			= $this->_db->Quote( '%'.$this->_db->getEscaped( $filter, true ).'%', false );
				$filter_type 	= JString::strtolower($filter_type);

				switch ($filter_type)
				{
					case 'title' :
						$where .= ' AND LOWER( a.title ) LIKE '.$filter;
						break;

					case 'venue' :
						$where .= ' AND LOWER( l.venue ) LIKE '.$filter;
						break;

					case 'city' :
						$where .= ' AND LOWER( l.city ) LIKE '.$filter;
						break;
				}
			}
		}
		return $where;
	}

	/**
	 * Method get the count of direct sub categories events
	 *
	 * @access private
	 * @return array
	 */
	function getChilds()
	{
		$query = $this->_buildChildsquery();
		$this->_childs = $this->_getList($query);
		return $this->_childs;
	}
	
	/**
	 * build query for direct child categories event count
	 *
	 * @access private
	 * @return array
	 */
	function _buildChildsQuery()
	{
		$user 		= &JFactory::getUser();
		$gid		= (int) $user->get('aid');
		$ordering	= 'c.ordering ASC';

		//build where clause
		$where = ' WHERE cc.published = 1';
		$where .= ' AND cc.parent_id = '.(int)$this->_id;
		$where .= ' AND cc.access <= '.$gid;
		
		//TODO: Make option for categories without events to be invisible in list
		//check archive task and ensure that only categories get selected if they contain a published/archived event
		$task 	= JRequest::getWord('task');
		if($task == 'archive') {
			$where .= ' AND i.published = -1';
		} else {
			$where .= ' AND i.published = 1';
		}
		
		$query = 'SELECT c.*,'
				. ' CASE WHEN CHAR_LENGTH( c.alias ) THEN CONCAT_WS( \':\', c.id, c.alias ) ELSE c.id END AS slug,'
				. ' ec.assignedevents'
				. ' FROM #__eventlist_categories AS c'
				. ' INNER JOIN ('
	          	. ' SELECT COUNT( DISTINCT i.id ) AS assignedevents, cc.id'
	         	. ' FROM #__eventlist_events AS i'
	          	. ' INNER JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = i.id'
	          	. ' INNER JOIN #__eventlist_categories AS cc ON cc.id = rel.catid'
	          	. $where
	          	. ' GROUP BY cc.id'
	          	. ')' 
          		. ' AS ec ON ec.id = c.id'
				. ' ORDER BY '.$ordering
			 	;

		return $query;
	}
	
	/**
	 * Method to get the Category
	 *
	 * @access public
	 * @return integer
	 */
	function getCategory( )
	{
		//initialize some vars
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');
		
		$query = 'SELECT *,'
				.' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug'
				.' FROM #__eventlist_categories'
				.' WHERE id = '.$this->_id;

		$this->_db->setQuery( $query );

		$this->_category = $this->_db->loadObject();
		
		//Make sure the category is published
		if (!$this->_category->published)
		{
			JError::raiseError(404, JText::sprintf( 'CATEGORY #%d NOT FOUND', $this->_id ));
			return false;
		}
		
		//check whether category access level allows access
		//additional check
		if ($this->_category->access > $gid)
		{
			JError::raiseError(403, JText::_("ALERTNOTAUTH"));
			return false;
		}

		return $this->_category;
	}

	/**
	 * get event categories
	 *
	 * @param int event id
	 * @return array
	 */
	function getCategories($id)
	{
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');
		
		$query = 'SELECT DISTINCT c.id, c.catname, c.access, c.checked_out AS cchecked_out,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug'
				. ' FROM #__eventlist_categories AS c'
				. ' LEFT JOIN #__eventlist_cats_event_relations AS rel ON rel.catid = c.id'
				. ' WHERE rel.itemid = '.(int)$id
				. ' AND c.published = 1'
				. ' AND c.access  <= '.$gid;
				;
	
		$this->_db->setQuery( $query );

		$this->_cats = $this->_db->loadObjectList();
		
		return $this->_cats;
	}
}
?>