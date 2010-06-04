<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList Attendees CB plugin
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
 *
 * Tab to display registered events in EventList 0.9 in a Community Builder profile
 * Based on an idea of Michael Spredemann
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class getEventsTab extends cbTabHandler {

	function getEventsTab()
	{
		$this->cbTabHandler();
	}

	function getDisplayTab($tab, $user, $ui)
	{
		$db			=& JFactory::getDBO();
		$language 	=& JFactory::getLanguage();

		//Get languagefile
		$language->load('com_eventlist');

		//Get EventList Route helper
		require_once(JPATH_SITE.DS.'components'.DS.'com_eventlist'.DS.'helpers'.DS.'route.php');

        //Get params
        $limit		= $this->params->get('limit', '5');
		$dateformat	= $this->params->get('dateformat', '%d.%m.%Y');
		$timeformat	= $this->params->get('timeformat', '%H.%M');

		$query = 'SELECT r.event, r.uid, s.title, s.dates, s.times, s.locid, l.venue, l.city,'
				. ' CASE WHEN CHAR_LENGTH(s.alias) THEN CONCAT_WS(\':\', s.id, s.alias) ELSE s.id END as slug,'
				. ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', l.id, l.alias) ELSE l.id END as venueslug'
				. ' FROM #__eventlist_register AS r'
				. ' LEFT JOIN #__eventlist_events AS s ON r.event = s.id'
				. ' LEFT JOIN #__eventlist_venues AS l ON s.locid = l.id'
				. ' WHERE r.uid = '.(int)$user->id
				. ' AND r.event = s.id AND s.locid = l.id'
				. ' ORDER BY s.dates, s.times'
				. ' LIMIT '.(int)$limit;

		$db->setQuery( $query );
		$events = $db->loadObjectList();

		if(!count($events) > 0) {
			$return = JText::_('NO EVENTS');
			return $return;
		}

		$return = "<div style=\"padding:4px;\">"."</div>"
				. "<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"95%\">"
				. "<tr class=\"sectiontableheader\">"
				. "<td>".JText::_('TITLE')."</td><td>".JText::_('VENUE')."</td><td>".JText::_('CITY')."</td><td>".JText::_('DATE')."</td>"
				. "</tr>"
				;

		foreach($events AS $event) {

			$date = strftime( $dateformat, strtotime( $event->dates ));
			$time = $event->times ? ' '.strftime( $timeformat, strtotime( $event->times )) : '';

			$return .= "<tr><td>"
					."<a href=\"".JRoute::_( EventListHelperRoute::getRoute($event->slug) )."\" />".htmlspecialchars( $event->title, ENT_COMPAT, 'UTF-8' )."</a></td>"
    				."<td><a href=\"".JRoute::_( EventListHelperRoute::getRoute($event->venueslug, 'venueevents') )."\" />".htmlspecialchars( $event->venue, ENT_COMPAT, 'UTF-8' )."</a></td>"
					."<td>".htmlspecialchars( $event->city, ENT_COMPAT, 'UTF-8' )."</td>"
					."<td>".$date.$time."</td>"
					."</tr>"
					;

		}
		$return .= "</table>";

		return $return;
	}
}
?>