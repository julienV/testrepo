<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
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
 * EventList Component Home Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelEventList extends JModel
{
	/**
	 * Events data in array
	 *
	 * @var array
	 */
	var $_events = null;

	/**
	 * Venues data in array
	 *
	 * @var array
	 */
	var $_venue = null;

	/**
	 * Categories data in array
	 *
	 * @var array
	 */
	var $_category = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to get event item data
	 *
	 * @access public
	 * @return array
	 */
	function getEventsdata()
	{
		/*
		* Get nr of all published events
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_events'
					. ' WHERE published = 1'
					;

		$this->_db->SetQuery($query);
  		$nrevpubl = $this->_db->loadResult();

		/*
		* Get nr of all unpublished events
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_events'
					. ' WHERE published = 0'
					;

		$this->_db->SetQuery($query);
  		$nrevunpubl = $this->_db->loadResult();

		/*
		* Get nr of all archived events
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_events'
					. ' WHERE published = -1'
					;

		$this->_db->SetQuery($query);
  		$nrevarchived = $this->_db->loadResult();

		/*
		* Get totalnr of events
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_events'
					;

		$this->_db->SetQuery($query);
  		$nrevtotal = $this->_db->loadResult();

		$_events = array();
		$_events[] = $nrevpubl;
		$_events[] = $nrevunpubl;
		$_events[] = $nrevarchived;
		$_events[] = $nrevtotal;

		return $_events;
	}

	/**
	 * Method to get venue item data
	 *
	 * @access public
	 * @return array
	 */
	function getVenuesdata()
	{
		/*
		* Get nr of all published venues
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_venues'
					. ' WHERE published = 1'
					;

		$this->_db->SetQuery($query);
  		$nrvenpubl = $this->_db->loadResult();

		/*
		* Get nr of all unpublished venues
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_venues'
					. ' WHERE published = 0'
					;

		$this->_db->SetQuery($query);
  		$nrvenunpubl = $this->_db->loadResult();

		/*
		* Get totalnr of venues
		*/
		$query = 'SELECT count(*)'
				. ' FROM #__eventlist_venues'
					;

		$this->_db->SetQuery($query);
  		$nrventotal = $this->_db->loadResult();

		$_venue = array();
		$_venue[] = $nrvenpubl;
		$_venue[] = $nrvenunpubl;
		$_venue[] = $nrventotal;

		return $_venue;
	}

		/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getCategoriesdata()
	{
		/*
		* Get nr of all published categories
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_categories'
					. ' WHERE published = 1'
					;

		$this->_db->SetQuery($query);
  		$nrcatpubl = $this->_db->loadResult();

		/*
		* Get nr of all unpublished categories
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_categories'
					. ' WHERE published = 0'
					;

		$this->_db->SetQuery($query);
  		$nrcatunpubl = $this->_db->loadResult();

		/*
		* Get totalnr. of categories
		*/
		$query = 'SELECT count(*)'
					. ' FROM #__eventlist_categories'
					;

		$this->_db->SetQuery($query);
  		$nrcattotal = $this->_db->loadResult();

		$_category = array();
		$_category[] = $nrcatpubl;
		$_category[] = $nrcatunpubl;
		$_category[] = $nrcattotal;

		return $_category;
	}
}
?>