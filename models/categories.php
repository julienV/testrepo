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
	 * Categories data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
   * Top category
   *
   * @var array
   */
  var $_id = null;
  
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
    */
		
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
	 */
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
	 */
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
   */
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
   */
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
*/
		return $query;
	}
}
?>