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
 * EventList Component Categoriesdetailed Model
 *
 * @package Joomla 
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelCategoriesdetailed extends JModel
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
		
		// Get the paramaters of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);
		
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
		global $mainframe;
		
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		// Lets load the content if it doesn't already exist
		if (empty($this->_categories))
		{
			$query = $this->_buildQuery();
			$this->_categories = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		
			/* Only php5 compatible
			foreach ($this->_categories as $category) {

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
			
				//Get total of assigned events of each venue
				$category->assignedevents = $this->_assignedevents( $category->id );
			}
			*/
			
			$k = 0;
			for($i = 0; $i <  count($this->_categories); $i++)
			{
				$category =& $this->_categories[$i];

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
			
				//Get total of assigned events of each venue
				$category->assignedevents = $this->_assignedevents( $category->id );

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
	 * Method to get the Categories events
	 *
	 * @access public
	 * @return array
	 */
	function &getEventdata( $id )
	{
		global $mainframe;
		
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);

		// Lets load the content
		$query = $this->_buildDataQuery( $id );
		$this->_data = $this->_getList( $query, 0, $params->get('detcat_nr') );
				
		return $this->_data;
	}
	
	/**
	 * Method get the event query
	 *
	 * @access private
	 * @return array
	 */
	function _buildDataQuery( $id )
	{		
		$user		= & JFactory::getUser();
		$aid		= (int) $user->get('aid');
		$id			= (int) $id;
		
		//Get Events from Category
		$query = 'SELECT a.*, l.club, l.city, l.state, l.url, c.catname, c.id AS catid,'
				. ' CASE WHEN CHAR_LENGTH(a.title) THEN CONCAT_WS(\':\', a.id, a.title) ELSE a.id END as slug,'
				. ' CASE WHEN CHAR_LENGTH(l.club) THEN CONCAT_WS(\':\', a.locid, l.club) ELSE a.locid END as venueslug,'
				. ' CASE WHEN CHAR_LENGTH(c.catname) THEN CONCAT_WS(\':\', c.id, c.catname) ELSE c.id END as categoryslug'
				. ' FROM #__eventlist_events AS a'
				. ' LEFT JOIN #__eventlist_venues AS l ON l.id = a.locid'
				. ' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
				. ' WHERE a.published = 1 && a.catsid = '.$id
				. ' AND c.access <= '.$aid
				. ' ORDER BY a.dates, a.times'
				;

		return $query;
	}
	
	/**
	 * Method get the categories query
	 *
	 * @access private
	 * @return array
	 */
	function _buildQuery( )
	{
		$user		= & JFactory::getUser();
		$gid 		= (int) $user->get('aid');
		
		// Get the paramaters of the active menu item
		$menu		=& JMenu::getInstance();
		$item    	= $menu->getActive();
		$params		=& $menu->getParams($item->id);
		
		// show/hide empty categories
		$empty 	= null;
		$publ	= null;
		if (!$params->get('empty_cat'))
		{
			$empty 	= ' HAVING COUNT( a.id ) > 0';
			$publ	= ' AND a.published = 1';
		}
				
		//Get Categories
		$query = 'SELECT c.*'
				. ' FROM #__eventlist_categories AS c'
				. ' LEFT JOIN #__eventlist_events AS a ON a.catsid = c.id'
				. ' WHERE c.published = 1'
				. ' AND c.access <= '.$gid
				. $publ
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
	function _assignedevents( $id )
	{	
		$user		= & JFactory::getUser();
		$gid 		= (int) $user->get('aid');
		$id			= (int) $id;
		
		//Count Events
		$query = 'SELECT COUNT(a.id)'
				. ' FROM #__eventlist_events AS a' 
				. ' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
				. ' WHERE a.published = 1 && a.catsid = '.$id
				. ' AND c.access <= '.$gid
				;
		$this->_db->setQuery( $query );
		
		return $this->_db->loadResult();
	}
}
?>