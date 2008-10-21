<?php
/**
 * @version 1.1 $Id: categoriesview.php 447 2007-10-13 16:36:15Z schlu $
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
	 * Event data array
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
	 * Categories data array
	 *
	 * @var integer
	 */
	var $_categories = null;

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
		$params 	= & $mainframe->getParams('com_eventlist');

		//get the number of events from database
		$limit			= JRequest::getInt('limit', $params->get('cat_num'));
		$limitstart		= JRequest::getInt('limitstart');

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
		global $mainframe;

		$params 	= & $mainframe->getParams();
		$elsettings = & ELHelper::config();

		// Lets load the content if it doesn't already exist
		if (empty($this->_categories))
		{
			$query = $this->_buildQuery();
			$this->_categories = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );

			$k = 0;
			$count = count($this->_categories);
			for($i = 0; $i < $count; $i++)
			{
				$category =& $this->_categories[$i];
				
				//child categories
				$query	= $this->_buildQuery( $category->id );
				$this->_db->setQuery($query);
				$category->subcats = $this->_db->loadObjectList();

				//Generate description
				if (empty ($category->catdescription)) {
					$category->catdescription = JText::_( 'NO DESCRIPTION' );
				} else {
					//execute plugins
					$category->text		= $category->catdescription;
					$category->title 	= $category->catname;
					JPluginHelper::importPlugin('content');
					$results = $mainframe->triggerEvent( 'onPrepareContent', array( &$category, &$params, 0 ));
					$category->catdescription = $category->text;
				}

				if ($category->image != '') {

					$attribs['width'] = $elsettings->imagewidth;
					$attribs['height'] = $elsettings->imagehight;

					$category->image = JHTML::image('images/stories/'.$category->image, $category->catname, $attribs);
				} else {
					$category->image = JHTML::image('components/com_eventlist/assets/images/noimage.png', $category->catname);
				}
				
				//create target link
				$task 	= JRequest::getWord('task');
				
				$category->linktext = $task == 'archive' ? JText::_( 'SHOW ARCHIVE' ) : JText::_( 'SHOW EVENTS' );

				if ($task == 'archive') {
					$category->linktarget = JRoute::_('index.php?view=categoryevents&id='.$category->slug.'&task=archive');
				} else {
					$category->linktarget = JRoute::_('index.php?view=categoryevents&id='.$category->slug);
				}
				
				$k = 1 - $k;
			}

		}

		return $this->_categories;
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
			$query = $this->_buildQueryTotal();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method get the categories query
	 *
	 * @access private
	 * @return array
	 */
	function _buildQuery( $parent_id = 0 )
	{
		$user 		= &JFactory::getUser();
		$gid		= (int) $user->get('aid');
		$ordering	= 'c.ordering ASC';

		//build where clause
		$where = ' WHERE cc.published = 1';
		$where .= ' AND cc.parent_id = 0';
		$where .= ' AND cc.access <= '.$gid;
		
		//TODO: Make option for categories without events to be invisible in list
		//check archive task and ensure that only categories get selected if they contain a published/archived event
		$task 	= JRequest::getWord('task');
		if($task == 'archive') {
			$where .= ' AND i.published = -1';
		} else {
			$where .= ' AND i.published = 1';
		}
		$where .= ' AND c.id = cc.id';
		
		$query = 'SELECT c.*,'
				. ' CASE WHEN CHAR_LENGTH( c.alias ) THEN CONCAT_WS( \':\', c.id, c.alias ) ELSE c.id END AS slug,'
					. ' ('
					. ' SELECT COUNT( DISTINCT i.id )'
					. ' FROM #__eventlist_events AS i'
					. ' LEFT JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = i.id'
					. ' LEFT JOIN #__eventlist_categories AS cc ON cc.id = rel.catid'
					. $where
					. ' GROUP BY cc.id'
					. ')' 
					. ' AS assignedevents'
				. ' FROM #__eventlist_categories AS c'
				. ' WHERE c.published = 1'
				. ' AND c.parent_id = '.(int)$parent_id
				. ' AND c.access <= '.$gid
				. ' ORDER BY '.$ordering
				;

		return $query;
	}
	
	/**
	 * Method to build the Categories query without subselect
	 * That's enough to get the total value.
	 *
	 * @access private
	 * @return string
	 */
	function _buildQueryTotal()
	{
		$user 		= &JFactory::getUser();
		$gid		= (int) $user->get('aid');
				
		$query = 'SELECT c.id'
				. ' FROM #__eventlist_categories AS c'
				. ' WHERE c.published = 1'
				. ' AND c.parent_id = 0'
				. ' AND c.access <= '.$gid
				;

		return $query;
	}
		
	/**
	 * Method to build the Categories query
	 *
	 * @access private
	 * @return array
	 */
	function _getsubs($id)
	{
		$user 		= &JFactory::getUser();
		$gid		= (int) $user->get('aid');
		$ordering	= 'c.ordering ASC';
		
		//build where clause
		$where = ' WHERE cc.published = 1';
		$where .= ' AND cc.parent_id = 0';
		$where .= ' AND cc.access <= '.$gid;
		
		//TODO: Make option for categories without events to be invisible in list
		//check archive task and ensure that only categories get selected if they contain a published/archived event
		$task 	= JRequest::getWord('task');
		if($task == 'archive') {
			$where .= ' AND i.published = -1';
		} else {
			$where .= ' AND i.published = 1';
		}
		$where .= ' AND c.id = cc.id';

		$query = 'SELECT c.*,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug,'
					. ' ('
					. ' SELECT COUNT( DISTINCT i.id )'
					. ' FROM #__eventlist_events AS i'
					. ' LEFT JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = i.id'
					. ' LEFT JOIN #__eventlist_categories AS cc ON cc.id = rel.catid'
					. $where
					. ' GROUP BY cc.id'
					. ')' 
					. ' AS assignedevents'
				. ' FROM #__eventlist_categories AS c'
				. ' WHERE c.published = 1'
				. ' AND c.parent_id = '. (int)$id
				. ' AND c.access <= '.$gid
				. ' ORDER BY '.$ordering
				;

		$this->_db->setQuery($query);
		$this->_subs = $this->_db->loadObjectList();

		return $this->_subs;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Categories data array
	 *
	 * @var array
	 *
	var $_data = null;
	
	/**
	 * Childs
	 *
	 * @var mixed
	 *
	var $_childs = null;

	/**
     * Top category
     *
     * @var array
     *
  	var $_id = null;
  
	/**
	 * Categories total
	 *
	 * @var integer
	 *
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 *
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 *
	function __construct()
	{
		parent::__construct();

		global $mainframe;

		// commenting out Christoph use of cid...
		// $cid			= JRequest::getInt('cid', 0);
		
		// Get the paramaters of the active menu item
		$params = & $mainframe->getParams();

		// if usecat is set, use category value specified in menu item parameters
		if ( $params->get('usecat') ) {
			$cid = JRequest::getInt('cid', intval( $params->get('catid') ) );
		}
		else {
			$cid     = JRequest::getInt('cid', 0);
		}
		
		/*
    if ( intval( JRequest::getInt('catid') ) ) {
      $this->setId(intval( JRequest::getInt('catid') ));    	
    }
    *
		
		//get the number of events from database
		$limit			= JRequest::getInt('limit', $params->get('cat_num'));
		$limitstart		= JRequest::getInt('limitstart');

		$this->setId((int)$cid);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	/**
	 * Method to set the category id
	 *
	 * @access	public
	 * @param	int	category ID number
	 *
	function setId($cid)
	{
		// Set new category ID and wipe data
		$this->_id			= $cid;
		//$this->_data		= null;
	}

	/**
	 * Method to get the Categories
	 *
	 * @access public
	 * @return array
	 *
	function &getData( )
	{
		$elsettings = & ELHelper::config();

		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );

			$k = 0;
			$count = count($this->_data);
			for($i = 0; $i < $count; $i++)
			{
				$category =& $this->_data[$i];

				if ($category->image != '') {

					$attribs['width'] = $elsettings->imagewidth;
					$attribs['height'] = $elsettings->imagehight;

					$category->image = JHTML::image('images/stories/'.$category->image, $category->catname, $attribs);
				} else {
					$category->image = JHTML::image('components/com_eventlist/assets/images/noimage.png', $category->catname);
				}
				
				//create target link
				$task 	= JRequest::getWord('task');
				
				$category->linktext = $task == 'archive' ? JText::_( 'SHOW ARCHIVE' ) : JText::_( 'SHOW EVENTS' );

				if ($task == 'archive') {
					$category->linktarget = JRoute::_('index.php?view=categoryevents&id='.$category->slug.'&task=archive');
				} else {
					$category->linktarget = JRoute::_('index.php?view=categoryevents&id='.$category->slug);
				}

				$k = 1 - $k;
			}

		}

		return $this->_data;
	}

  /**
   * Returns the category list, filtering the results from the request?
   *
   * @param string The query
   * @param int Offset
   * @param int The number of records
   * @return  array
   * @access  protected
   * @since 1.5
   *
  function &_getList( $query, $limitstart=0, $limit=0 )
  {
    $this->_db->setQuery( $query );
    $rows = $this->_db->loadObjectList();

    // if no category selected, print them all
    if (! $this->_id) {
    	$this->_total = count($rows);
    	return ( array_slice($rows, $limitstart, $limit) );
    }
    
    // filter categories if a top category was specified    
    // establish the hierarchy of the categories
    $children = array();
    
    // first pass - collect direct children
    foreach ($rows as $v )
    {
      $pt = $v->parent_id;
      $list = @$children[$pt] ? $children[$pt] : array();
      array_push( $list, $v );
      $children[$pt] = $list;
    }
    $list = $this->getAllChildren( $children, $this->_id);
    // update the total number of rows.
    $this->_total = count($list);
    
    return ( array_slice($list, $limitstart, $limit) );
  }
  
  /**
   * Recursive function to get all categories belonging to subtree of a category.
   *
   * @param unknown_type $children
   * @param unknown_type $catid
   * @return unknown
   *
  function getAllChildren( &$children, $catid ) 
  {
  	$childs = array();
  	if (count($children[$catid])) 
  	{
  		//$childs = $children[$catid];
  		foreach ($children[$catid] AS $child)
  		{
  			$childs[] = $child;
  			$childs = array_merge($childs, $this->getAllChildren($children, $child->id));
  		}
  	}
  	return $childs;
  }
  
	/**
	 * Total nr of Venues
	 *
	 * @access public
	 * @return integer
	 *
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
	 *
	function _buildQuery()
	{
		//initialize some vars
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');

		//check archive task and ensure that only categories get selected if they contain a published/archived event
		$task 	= JRequest::getVar('task', '', '', 'string');
		if($task == 'archive') {
			$eventstate = ' AND e.published = -1';
		} else {
			$eventstate = ' AND e.published = 1';
		}
						
		//get categories
		$query = 'SELECT c.*, c.id AS catid, COUNT(*) AS assignedevents,'
					. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug'
					. ' FROM #__eventlist_categories AS c'
					. ' LEFT JOIN #__eventlist_cats_event_relations AS a ON a.catid = c.id'
	        . ' LEFT JOIN #__eventlist_events AS e ON e.id = a.itemid'
					. ' WHERE c.published = 1'
					. ' AND c.access <= '.$gid
					. $eventstate
					. ' GROUP BY c.id'
					. ' ORDER BY c.ordering'
					;
		/*	
		$query = 'SELECT DISTINCT c.id AS catid, c.*, COUNT( a.id ) AS assignedevents,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug'
				. ' FROM #__eventlist_events AS a'
		//		. ' LEFT JOIN #__eventlist_events AS a ON a.catsid = c.id'
				. ' LEFT JOIN #__eventlist_categories AS c ON c.parent_id = '. $this->_id
				. ' LEFT JOIN #__eventlist_cats_event_relations AS rel ON rel.catid = c.id'
		//		. ' WHERE rel.itemid = '.(int)$id
		//		. ' AND c.published = 1'
				. ' WHERE c.published = 1'
				. $eventstate
				. ' AND c.access  <= '.$gid
				. ' GROUP BY c.id'
				. ' ORDER BY c.ordering'
				;
*
		return $query;
	}
}

	/**
	 * Method to build the Childcategories query
	 *
	 * @access private
	 * @return string
	 *
	function _buildChildsquery()
	{
		$user 		= &JFactory::getUser();
		$gid		= (int) $user->get('aid');
		$ordering	= 'ordering ASC';

		$query = 'SELECT *,'
				. ' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug'
				. ' FROM #__eventlist_categories'
				. ' WHERE published = 1'
				. ' AND parent_id = '. $this->_id
				. ' AND access <= '.$gid
				. ' ORDER BY '.$ordering
				;

		return $query;
	}
	/**
	 * Method to get the childs of a category
	 *
	 * @access public
	 * @return array
	 *
	function getChilds()
	{
		$query = $this->_buildChildsquery();
		$this->_childs = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		var_dump($this->_childs);
		$k = 0;
		$count = count($this->_childs);
		for($i = 0; $i < $count; $i++)
		{
			$category =& $this->_childs[$i];

			$category->assignedfaqs = $this->_countcatevents( $category->id );
			$category->subcats		= $this->_getsubs( $category->id );

			$k = 1 - $k;
		}

		return $this->_childs;
	}
	
	/**
	 * Method to build the Categories query
	 * todo: see above and merge
	 *
	 * @access private
	 * @return array
	 *
	function _getsubs($id)
	{
		$user 		= &JFactory::getUser();
		$gid		= (int) $user->get('aid');
		$ordering	= 'ordering ASC';

		$query = 'SELECT *,'
				. ' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug'
				. ' FROM #__eventlist_categories'
				. ' WHERE published = 1'
				. ' AND parent_id = '. (int)$id
				. ' AND access <= '.$gid
				. ' ORDER BY '.$ordering
				;

		$this->_db->setQuery($query);
		$this->_subs = $this->_db->loadObjectList();

		return $this->_subs;
	}
	
	/**
	 * Method to get the total number of assigned items to a category
	 *
	 * @access private
	 * @return integer
	 *
	function _countcatevents( $id )
	{
		//initialize some vars
		$user		= & JFactory::getUser();
		$gid		= (int) $user->get('aid');

		$where 	= ' WHERE rel.catid = '.(int)$id;
		$where .= ' AND c.access <= '.$gid;

		$where .= ' AND e.published = 1';
		
		$query = 'SELECT COUNT(DISTINCT e.id)'
				. ' FROM #__eventlist_events AS e'
				. ' LEFT JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = e.id'
				. ' LEFT JOIN #__eventlist_categories AS c ON c.id = rel.catid'
				.$where;
		;
		$this->_db->setQuery( $query );

		return $this->_db->loadResult();
	}
*/
}
?>