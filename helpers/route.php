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

defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * EventList Component Route Helper
 * based on Joomla ContentHelperRoute
 *
 * @static
 * @package		Joomla
 * @subpackage	EventList
 * @since 1.5
 */
class EventListHelperRoute
{
	/**
	 * TODO: Rework if we haven't the time to cleanup the id mess
	 *
	 * @param	int	The route of the Event item
	 */
	function getEventRoute($id)
	{
		$needles = array(
			'details'  => (int) $id
		);

		//Create the link
		$link = 'index.php?option=com_eventlist&view=details&id='. $id;

		if($item = EventListHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}

	/**
	 * @param	int	The route of the Venue item
	 */
	function getVenueRoute($id)
	{
		$needles = array(
			'venueevents' => (int) $id
		);

		//Create the link
		$link = 'index.php?option=com_eventlist&view=venueevents&id='.$id;

		if($item = EventListHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}

	/**
	 * @param	int	The route of the Category item
	 */
	function getCategoryRoute($id)
	{
		$needles = array(
			'categoryevents' => (int) $id,
		);

		//Create the link
		$link = 'index.php?option=com_eventlist&view=categoryevents&id='.$id;

		if($item = EventListHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}

	//TODO: Wait till router is fixed and can handle links without any itemid, than cleanup
	function _findItem($needles)
	{
		$component =& JComponentHelper::getComponent('com_eventlist');

		$menus	=& JSite::getMenu();
		$items	= $menus->getItems('componentid', $component->id);

		foreach($needles as $needle => $id)
		{
			foreach($items as $item)
			{
				if ((@$item->query['view'] == $needle) && (@$item->query['id'] == $id)) {
					return $item;
				}
			}
		}

		//return first match
		return $items[0]->id;
	}
}
?>