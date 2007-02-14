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
 * EventList Component Eventelement Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelEventelement extends JModel
{
	/**
	 * Events data array
	 *
	 * @var array
	 */
	var $_data = null;

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

		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 0);
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0 );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
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
		// Lets load the content if it doesn't already exist
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

		$query = 'SELECT a.*, loc.club, loc.city, cat.catname'
					. ' FROM #__eventlist_events AS a'
					. ' LEFT JOIN #__eventlist_venues AS loc ON loc.id = a.locid'
					. ' LEFT JOIN #__eventlist_categories AS cat ON cat.id = a.catsid'
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

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.eventelement.filter_order', 		'filter_order', 	'a.dates' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.eventelement.filter_order_Dir',	'filter_order_Dir',	'' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', a.dates';

		return $orderby;
	}

	/**
	 * Build the where clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.eventelement.filter_state', 		'filter_state', 	'' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.eventelement.filter', 'filter', '' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.eventelement.search', 			'search', '' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'a.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'a.published = 0';
			}
		} else {
			$where[] = 'a.published >= 0';
		}

		if ($search && $filter == 1) {
			$where[] = ' LOWER(a.title) LIKE "%'.$search.'%"';
		}

		if ($search && $filter == 2) {
			$where[] = ' LOWER(loc.club) LIKE "%'.$search.'%"';
		}

		if ($search && $filter == 3) {
			$where[] = ' LOWER(loc.city) LIKE "%'.$search.'%"';
		}

		if ($search && $filter == 4) {
			$where[] = ' LOWER(cat.catname) LIKE "%'.$search.'%"';
		}


		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
}//Class end
?>