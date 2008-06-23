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
 * EventList Component Categories Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCategories extends JModel
{
	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Categorie id
	 *
	 * @var int
	 */
	var $_id = null;

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
	 * Method to set the category identifier
	 *
	 * @access	public
	 * @param	int Category identifier
	 */
	function setId($id)
	{
		// Set id
		$this->_id	 = $id;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		global $mainframe;
		
		static $items;

		if (isset($items)) {
			return $items;
		}
		
		$limit				= $mainframe->getUserStateFromRequest( 'com_eventlist.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart 		= $mainframe->getUserStateFromRequest( 'com_eventlist.limitstart', 'limitstart', 0, 'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( 'com_eventlist.categories.filter_order', 		'filter_order', 	'c.ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_eventlist.categories.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_eventlist.categories.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_eventlist.categories.search', 'search', '', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
		
		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', c.ordering';
		
		$where = array();
		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'c.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'c.published = 0';
			}
		}
		
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		//select the records
		//note, since this is a tree we have to do the limits code-side
		if ($search) {			
			
			
			$query = 'SELECT c.id'
					. ' FROM #__eventlist_categories AS c'
					. ' WHERE LOWER(c.catname) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false )
					. $where
					;
			$this->_db->setQuery( $query );
			$search_rows = $this->_db->loadResultArray();					
		}
		
		$query = 'SELECT c.*, c.catname AS name, c.parent_id AS parent, u.name AS editor, g.name AS groupname, gr.name AS catgroup'
					. ' FROM #__eventlist_categories AS c'
					. ' LEFT JOIN #__groups AS g ON g.id = c.access'
					. ' LEFT JOIN #__users AS u ON u.id = c.checked_out'
					. ' LEFT JOIN #__eventlist_groups AS gr ON gr.id = c.groupid'
					. $where
					. $orderby
					;
		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();
				
		//establish the hierarchy of the categories
		$children = array();
    //set depth limit
    $levellimit = 10;
    
    /*
		// first pass - collect children
    foreach ($rows as $v )
    {
      $pt = $v->parent;
      $list = @$children[$pt] ? $children[$pt] : array();
      array_push( $list, $v );
      $children[$pt] = $list;
    }
    
    // second pass - get an indent list of the items
    $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ) );
    /*/
		//first pass - collect children

    	foreach ($rows as $child) {
        	$parent = $child->parent_id;
       		$list 	= @$children[$parent] ? $children[$parent] : array();
        	array_push($list, $child);
        	$children[$parent] = $list;
    	}
    	
    	//second pass - get an indent list of the items
    	$list = eventlist_cats::treerecurse(0, '', array(), $children, false, max(0, $levellimit-1));
    
    	//eventually only pick out the searched items.
		if ($search) {
			$list1 = array();

			foreach ($search_rows as $sid )
			{
				foreach ($list as $item)
				{
					if ($item->id == $sid) {
						$list1[] = $item;
					}
				}
			}
			// replace full list with found items
			$list = $list1;
		}
		
    	$total = count( $list );

		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination( $total, $limitstart, $limit );

		// slice out elements based on limits
		$list = array_slice( $list, $this->_pagination->limitstart, $this->_pagination->limit );
    	
		foreach ($list as $category)	{
			$category->assignedevents = $this->_countcatevents( $category->id );
		}

		return $list;
	}
	
	function &getPagination()
	{
		if ($this->_pagination == null) {
			$this->getItems();
		}
		return $this->_pagination;
	}

	/**
	 * Method to (un)publish a category
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__eventlist_categories'
				. ' SET published = ' . (int) $publish
				. ' WHERE id IN ('. $cids .')'
				. ' AND ( checked_out = 0 OR ( checked_out = ' . (int) $user->get('id'). ' ) )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to move a category
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function move($direction)
	{
		$row =& JTable::getInstance('eventlist_categories', '');

		if (!$row->load( $this->_id ) ) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction, 'parent_id = '.$row->parent_id )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	/**

	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	/**
	 * Method to order categories
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.0
	 */
	function saveorder($cid = array(), $order)
	{
		$row =& JTable::getInstance('eventlist_categories', '');
		
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			
			// track categories
			$groupings[] = $row->parent_id;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('parent_id = '.$group);
		}

		return true;
	}
	
	/**
	 * Method to count the nr of assigned events to the category
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function _countcatevents($id)
	{
		$query = 'SELECT COUNT(DISTINCT e.id )'
				.' FROM #__eventlist_events AS e'
				.' LEFT JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = e.id'
				.' LEFT JOIN #__eventlist_categories AS c ON c.id = rel.catid'
				.' WHERE rel.catid = ' . (int)$id
				;
					
		$this->_db->setQuery($query);
		$number = $this->_db->loadResult();
    	
    	return $number;
	}
	

	/**
	 * Method to remove a event
	 *
	 * @access	public
	 * @return	string $msg
	 * @since	0.9
	 */
	function delete($cid)
	{
		$cids = implode( ',', $cid );

		$query = 'SELECT c.id, c.catname, COUNT( e.catid ) AS numcat'
				. ' FROM #__eventlist_categories AS c'
				. ' LEFT JOIN #__eventlist_cats_event_relations AS e ON e.catid = c.id'
				. ' WHERE c.id IN ('. $cids .')'
				. ' GROUP BY c.id'
				;
		$this->_db->setQuery( $query );

		if (!($rows = $this->_db->loadObjectList())) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		$err = array();
		$cid = array();
		foreach ($rows as $row) {
			if ($row->numcat == 0) {
				$cid[] = $row->id;
			} else {
				$err[] = $row->catname;
			}
		}

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__eventlist_categories'
					. ' WHERE id IN ('. $cids .')';

			$this->_db->setQuery( $query );

			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		if (count( $err )) {
			$cids 	= implode( ', ', $err );
    		$msg 	= JText::sprintf( 'EVENT ASSIGNED CATEGORY', $cids );
    		return $msg;
		} else {
			$total 	= count( $cid );
			$msg 	= $total.' '.JText::_('CATEGORIES DELETED');
			return $msg;
		}
	}
}
?>