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
}
?>