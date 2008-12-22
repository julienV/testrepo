<?php
/**
 * @version 1.1 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2008 Christoph Lukes
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
 * EventList Component search Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelSearch extends JModel
{
	/**
	 * Events data array
	 *
	 * @var array
	 */
	var $_data = null;

	var $_total = null;
	
	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;
	
	/**
	 * the query
	 */
	var $_query = null;

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
		$params 	= & $mainframe->getParams('com_eventlist');

		//get the number of events from database
		$limit       	= $mainframe->getUserStateFromRequest('com_eventlist.search.limit', 'limit', $params->def('display_num', 0), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');
			
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// Get the filter request variables
		$this->setState('filter_order', JRequest::getCmd('filter_order', 'a.dates'));
		$this->setState('filter_order_dir', JRequest::getCmd('filter_order_Dir', 'ASC'));
	}

	/**
	 * Method to get the Events
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
				$pagination = $this->getPagination();
				$this->_data = $this->_getList( $query, $pagination->limitstart, $pagination->limit );
			}

			$k = 0;
			$count = count($this->_data);
			for($i = 0; $i < $count; $i++)
			{
				$item =& $this->_data[$i];
				$item->categories = $this->getCategories($item->id);
				
				//remove events without categories (users have no access to them)
				if (empty($item->categories)) {
					unset($this->_data[$i]);
				} 
				
				$k = 1 - $k;
			}
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
	 * Build the query
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery()
	{
		if (empty($this->_query))
		{
			// Get the WHERE and ORDER BY clauses for the query
			$where		= $this->_buildEventListWhere();
			$orderby	= $this->_buildEventListOrderBy();
	
			//Get Events from Database
			$this->_query = 'SELECT DISTINCT a.id, a.dates, a.enddates, a.times, a.endtimes, a.title, a.created, a.locid, a.datdescription,'
					. ' l.venue, l.city, l.state, l.url,'
					. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
					. ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', a.locid, l.alias) ELSE a.locid END as venueslug'
					. ' FROM #__eventlist_events AS a'
	        . ' INNER JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = a.id '
					. ' LEFT JOIN #__eventlist_venues AS l ON l.id = a.locid'
          . ' LEFT JOIN #__eventlist_countries AS c ON c.iso2 = l.country'
					. $where
					. $orderby
					;
		}
		return $this->_query;
	}

	/**
	 * Build the order clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildEventListOrderBy()
	{
		$filter_order		= $this->getState('filter_order');
		$filter_order_dir	= $this->getState('filter_order_dir');

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_dir.', a.dates, a.times';

		return $orderby;
	}

	/**
	 * Build the where clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildEventListWhere()
	{
		global $mainframe;

		// Get the paramaters of the active menu item
		$params 	= & $mainframe->getParams();

		$task 		= JRequest::getWord('task');
		
		// First thing we need to do is to select only needed events
		if ($task == 'archive') {
			$where = ' WHERE a.published = -1';
		} else {
			$where = ' WHERE a.published = 1';
		}

		$filter 		= JRequest::getString('filter', '', 'request');
		$filter_type 	= JRequest::getWord('filter_type', '', 'request');
    $filter_continent = $mainframe->getUserStateFromRequest('com_eventlist.search.filter_continent', 'filter_continent', '', 'string');
    $filter_country = $mainframe->getUserStateFromRequest('com_eventlist.search.filter_country', 'filter_country', '', 'string');
    $filter_city = $mainframe->getUserStateFromRequest('com_eventlist.search.filter_city', 'filter_city', '', 'string');
    $filter_date = $mainframe->getUserStateFromRequest('com_eventlist.search.filter_date', 'filter_date', '', 'string');
    $filter_category = $mainframe->getUserStateFromRequest('com_eventlist.search.filter_category', 'filter_category', 0, 'int');

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
    // filter date
    if ($filter_date) {
    	if (strtotime($filter_date)) {
    		$where .= ' AND (\''.$filter_date.'\' BETWEEN (a.dates) AND (a.enddates) OR \''.$filter_date.'\' = a.dates)';
    	}
    }
    // filter country
    if ($filter_continent) {
      $where .= ' AND c.continent = ' . $this->_db->Quote($filter_continent);
    }
    // filter country
    if ($filter_country) {
    	$where .= ' AND l.country = ' . $this->_db->Quote($filter_country);
    }
    // filter city
    if ($filter_country && $filter_city) {
    	$where .= ' AND l.city = ' . $this->_db->Quote($filter_city);
    }
    // filter category
    if ($filter_category) {
    	$cats = eventlist_cats::getChilds((int) $filter_category);
    	$where .= ' AND rel.catid IN (' . implode(', ', $cats) .')';
    }
		return $where;
	}
	
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
  
  function getCountryOptions()
  {
    global $mainframe;
  	$filter_continent = $mainframe->getUserStateFromRequest('com_eventlist.search.filter_continent', 'filter_continent', '', 'string');
  	
    $query = ' SELECT DISTINCT c.iso2 as value, c.name as text '
           . ' FROM #__eventlist_events AS a'
           . ' INNER JOIN #__eventlist_venues AS l ON l.id = a.locid'
           . ' INNER JOIN #__eventlist_countries as c ON c.iso2 = l.country '
           ;
    if ($filter_continent) {
      $query .= ' WHERE c.continent = ' . $this->_db->Quote($filter_continent);
    }
    $query .= ' ORDER BY c.name ';
    $this->_db->setQuery($query);
    return $this->_db->loadObjectList();
  }
	
	function getCityOptions()
	{
		if (!$country = JRequest::getString('filter_country', '', 'request')) {
			return array();
		}
		$query = ' SELECT DISTINCT l.city as value, l.city as text '
		       . ' FROM #__eventlist_events AS a'
           . ' INNER JOIN #__eventlist_venues AS l ON l.id = a.locid'
           . ' INNER JOIN #__eventlist_countries as c ON c.iso2 = l.country '
           . ' WHERE l.country = ' . $this->_db->Quote($country)
           . ' ORDER BY l.city ';           
    $this->_db->setQuery($query);
    return $this->_db->loadObjectList();
	}
	

  /**
   * logic to get the categories
   *
   * @access public
   * @return void
   */
  function getCategoryTree( )
  {
    $user   = & JFactory::getUser();
    $elsettings = & ELHelper::config();
    $userid   = (int) $user->get('id');
    $gid    = (int) $user->get('aid');
    $superuser  = ELUser::superuser();

    $where = ' WHERE c.published = 1 AND c.access <= '.$gid;

    //get the maintained categories and the categories whithout any group
    //or just get all if somebody have edit rights
    $query = 'SELECT c.*'
        . ' FROM #__eventlist_categories AS c'
        . $where
        . ' ORDER BY c.ordering'
        ;
    $this->_db->setQuery( $query );  
    $rows = $this->_db->loadObjectList();
    
    //set depth limit
    $levellimit = 10;
    
    //get children
    $children = array();
    foreach ($rows as $child) {
    	$parent = $child->parent_id;
    	$list = @$children[$parent] ? $children[$parent] : array();
    	array_push($list, $child);
    	$children[$parent] = $list;
    }
    //get list of the items
    return eventlist_cats::treerecurse(0, '', array(), $children, true, max(0, $levellimit-1));
  }
}
?>