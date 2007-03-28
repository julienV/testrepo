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
 * EventList Component Editevent Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelEditevent extends JModel
{
	/**
	 * Event data in Event array
	 *
	 * @var array
	 */
	var $_event = null;

	/**
	 * Category data in category array
	 *
	 * @var array
	 */
	var $_categories = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id);
	}

	/**
	 * Method to set the event id
	 *
	 * @access	public Event
	 */
	function setId($id)
	{
		// Set new event ID
		$this->_id			= $id;
	}

	/**
	 * logic to get the event
	 *
	 * @access public
	 * @since	0.9
	 * @return array
	 */
	function &getEvent(  )
	{
		global $mainframe, $option, $Itemid;

		// Initialize variables
		$user		= & JFactory::getUser();
		$elsettings = ELHelper::config();

		$view		= JRequest::getVar('view', '', '', 'string');

		/*
		* If Id exists we will do the edit stuff
		*/
		if ($this->_id) {

			/*
			* Load the Event data
			*/
			$this->_loadEvent();

			/*
			* Error if allready checked out otherwise check event out
			*/
			if ($this->_event->isCheckedOut( $user->get('id') )) {
				$mainframe->redirect( 'index.php?option='.$option.'&Itemid='.$Itemid.'&view='.$view, JText::_( 'THE EVENT' ).': '.$this->_event->title.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ) );
			} else {
				$this->_event->checkout( $user->get('id') );
			}

			/*
			* access check
			*/
			$owner = $this->getOwner();
			$editaccess	= ELUser::editaccess($elsettings->eventowner, $owner->created_by, $user->get('id'), $elsettings->eventeditrec);
			$maintainer = ELUser::ismaintainer();

			if ($maintainer || $editaccess ) $allowedtoeditevent = 1;

			if ($allowedtoeditevent == 0) {

				JError::raiseError( 403, JText::_( 'NO ACCESS' ) );

			}

			/*
			* If no Id exists we will do the add event stuff
			*/
		} else {

			//Check if the user has access to the form
			$maintainer = ELUser::ismaintainer();
			$genaccess 	= ELUser::validate_user( $elsettings->evdelrec, $elsettings->delivereventsyes );

			if ($maintainer || $genaccess ) $dellink = 1;

			if ($dellink == 0) {

				JError::raiseError( 403, JText::_( 'NO ACCESS' ) );

			}

			//prepare output
			$this->_event->id				= 0;
			$this->_event->locid			= '';
			$this->_event->catsid			= 0;
			$this->_event->dates			= '';
			$this->_event->enddates			= null;
			$this->_event->title			= '';
			$this->_event->times			= null;
			$this->_event->endtimes			= null;
			$this->_event->created			= null;
			$this->_event->author_ip		= null;
			$this->_event->created_by		= null;
			$this->_event->datdescription	= '';
			$this->_event->registra			= 0;
			$this->_event->unregistra		= 0;
			$this->_event->sendername		= '';
			$this->_event->sendermail		= '';
			$this->_event->datimage			= '';

		}

		return $this->_event;

	}

	/**
	 * logic to get the event
	 *
	 * @access private
	 * @return array
	 */
	function _loadEvent(  )
	{

		if (empty($this->_event)) {

			$this->_event =& JTable::getInstance('eventlist_events', '');

			$this->_event->load( $this->_id );

			return $this->_event;

		} else {

			return true;

		}
	}

	/**
	 * logic to get the categories
	 *
	 * @access public
	 * @return array
	 */
	function getCategories( )
	{
		$user		= & JFactory::getUser();
		$elsettings = ELHelper::config();
		$userid		= (int) $user->get('id');

		//get the ids of the categories the user maintaines
		$query = 'SELECT g.group_id'
				. ' FROM #__eventlist_groupmembers AS g'
				. ' WHERE g.member = '.$userid
				;
		$this->_db->setQuery( $query );
		$catids = $this->_db->loadResultArray();

		$categories = implode(' OR c.groupid = ', $catids);

		//build ids query
		if ($categories) {
			if (ELUser::validate_user($elsettings->evdelrec, $elsettings->delivereventsyes)) {
				$where = ' AND c.groupid = 0 OR c.groupid = '.$categories;
			} else {
				$where = ' AND c.groupid = '.$categories;
			}
		} else {
			$where = ' AND c.groupid = 0';
		}

		//get the maintained categories and the categories whithout any group
		$query = 'SELECT c.id AS value, c.catname AS text, c.groupid'
				. ' FROM #__eventlist_categories AS c'
				. ' WHERE c.published = 1'
				. $where
				. ' ORDER BY c.ordering'
				;
		$this->_db->setQuery( $query );

		$this->_category = array();
		$this->_category[] = JHTMLSelect::option( '0', JText::_( 'SELECT CATEGORY' ) );
		$this->_categories = array_merge( $this->_category, $this->_db->loadObjectList() );

		return $this->_categories;
	}

	/**
	 * logic to get the owner
	 *
	 * @access public
	 * @return integer
	 */
	function getOwner( )
	{
		$query = 'SELECT a.created_by'
				. ' FROM #__eventlist_events AS a'
				. ' WHERE a.id = '.$this->_id
				;
		$this->_db->setQuery( $query );

		return $this->_db->loadObject();
	}

	/**
	 * logic to get the venueslist
	 *
	 * @access public
	 * @return array
	 */
	function getVenues( )
	{
		$where		= $this->_buildVenuesWhere(  );
		$orderby	= $this->_buildVenuesOrderBy(  );

		$limit			= JRequest::getVar('limit', 0, '', 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$query = 'SELECT l.id, l.venue, l.city, l.country, l.published'
				.' FROM #__eventlist_venues AS l'
				. $where
				. $orderby
				;

		$this->_db->setQuery( $query, $limitstart, $limit );
		$rows = $this->_db->loadObjectList();

		return $rows;
	}

	/**
	 * Method to build the ordering
	 *
	 * @access private
	 * @return array
	 */
	function _buildVenuesOrderBy( )
	{

		$filter_order		= JRequest::getVar('filter_order');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir');

		$orderby = ' ORDER BY ';

		if ($filter_order && $filter_order_Dir)
		{
			$orderby .= $filter_order.' '.$filter_order_Dir.', ';
		}

		$orderby .= 'l.ordering';

		return $orderby;
	}

	/**
	 * Method to build the WHERE clause
	 *
	 * @access private
	 * @return array
	 */
	function _buildVenuesWhere(  )
	{

		$filter_type		= JRequest::getVar('filter_type', '', 'request');
		$filter 			= JRequest::getVar('filter');
		$filter 			= $this->_db->getEscaped( trim(JString::strtolower( $filter ) ) );

		$where = array();

		if ($filter && $filter_type == 1) {
			$where[] = 'LOWER(l.venue) LIKE "%'.$filter.'%"';
		}

		if ($filter && $filter_type == 2) {
			$where[] = 'LOWER(l.city) LIKE "%'.$filter.'%"';
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '');

		return $where;
	}

	/**
	 * Method to get the total number
	 *
	 * @access public
	 * @return integer
	 */
	function getCountitems ()
	{
		// Initialize variables
		$where		= $this->_buildVenuesWhere(  );

		$query = 'SELECT count(*)'
				. ' FROM #__eventlist_venues AS l'
				. $where
				;
		$this->_db->SetQuery($query);

  		return $this->_db->loadResult();
	}
}
?>