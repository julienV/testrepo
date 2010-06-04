<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList Wide Module
 * @copyright (C) 2005 - 2008 Christoph Lukes
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

/**
 * EventList Modulewide helper
 *
 * @package Joomla
 * @subpackage EventList Wide Module
 * @since		1.0
 */
class modEventListwideHelper
{

	/**
	 * Method to get the events
	 *
	 * @access public
	 * @return array
	 */
	function getList(&$params)
	{
		global $mainframe;

		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$user_gid	= (int) $user->get('aid');

		//all upcoming events
		if ($params->get( 'type' ) == 1) {
			$where = ' WHERE a.dates > CURDATE()';
		//	$where = ' WHERE a.published = 1';
			$order = ' ORDER BY a.dates, a.times';
		}
		
		//archived events only
		if ($params->get( 'type' ) == 2) {
			$where = ' WHERE a.published = -1';
			$order = ' ORDER BY a.dates DESC, a.times DESC';
		}
		
		//currently running events only
		if ($params->get( 'type' ) == 3) {
			$where = ' WHERE a.published = 1';			
			$where .= ' AND ( a.dates = CURDATE()';
			$where .= ' OR ( a.enddates >= CURDATE() AND a.dates <= CURDATE() ))';
			$order = ' ORDER BY a.dates, a.times';
		}

		//clean parameter data
		$catid 	= trim( $params->get('catid') );
		$venid 	= trim( $params->get('venid') );
		$state	= JString::strtolower(trim( $params->get('stateloc') ) );

		//Build category selection query statement
		if ($catid)
		{
			$ids = explode( ',', $catid );
			JArrayHelper::toInteger( $ids );
			$categories = ' AND (c.id=' . implode( ' OR c.id=', $ids ) . ')';
		}
		
		//Build venue selection query statement
		if ($venid)
		{
			$ids = explode( ',', $venid );
			JArrayHelper::toInteger( $ids );
			$venues = ' AND (l.id=' . implode( ' OR l.id=', $ids ) . ')';
		}
		
		//Build state selection query statement
		if ($state)
		{
			$rawstate = explode( ',', $state );
			
			foreach ($rawstate as $val)
			{
				if ($val) {
					$states[] = '"'.trim($db->getEscaped($val)).'"';
				}
			}
	
			JArrayHelper::toString( $states );
			$stat = ' AND (LOWER(l.state)='.implode(' OR LOWER(l.state)=',$states).')';
		}
		
		//perform select query
		$query = 'SELECT a.title, a.dates, a.enddates, a.times, a.endtimes, a.datdescription, a.datimage, l.venue, l.state, l.locimage, l.city, l.locdescription, c.catname,'
				.' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
				.' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', l.id, l.alias) ELSE l.id END as venueslug,'
				.' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as categoryslug'
				.' FROM #__eventlist_events AS a'
				.' LEFT JOIN #__eventlist_venues AS l ON l.id = a.locid'
				.' LEFT JOIN #__eventlist_categories AS c ON c.id = a.catsid'
				. $where
				.' AND c.access <= '.$user_gid
				.($catid ? $categories : '')
				.($venid ? $venues : '')
				.($state ? $stat : '')
				. $order
				.' LIMIT '.(int)$params->get( 'count', '2' )
				;

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		//assign datemethod value to jview
		jimport('joomla.application.component.view');
		JView::assignRef('datemethod', $params->get('datemethod', 1));
		JView::assignRef('use_modal', $params->get('use_modal', 0));
		
		if ($params->get('use_modal', 0)) {
			JHTML::_('behavior.modal');
		}

		//Loop through the result rows and prepare data
		$i		= 0;
		$lists	= array();
		foreach ( $rows as $row )
		{
			//create thumbnails if needed and receive imagedata
			$dimage = ELImage::flyercreator($row->datimage, 'event');
			$limage = ELImage::flyercreator($row->locimage);
						
			//cut titel
			$length = strlen(htmlspecialchars( $row->title ));
			
			if ($length > $params->get('cuttitle', '25')) {
				$row->title = substr($row->title, 0, $params->get('cuttitle', '18'));
				$row->title = $row->title.'...';
			}
			
			$lists[$i]->title			= htmlspecialchars( $row->title, ENT_COMPAT, 'UTF-8' );
			$lists[$i]->venue			= htmlspecialchars( $row->venue, ENT_COMPAT, 'UTF-8' );
			$lists[$i]->catname			= htmlspecialchars( $row->catname, ENT_COMPAT, 'UTF-8' );
			$lists[$i]->state			= htmlspecialchars( $row->state, ENT_COMPAT, 'UTF-8' );			
			$lists[$i]->eventlink		= $params->get('linkevent', 1) ? JRoute::_( EventListHelperRoute::getRoute($row->slug) ) : '';
			$lists[$i]->venuelink		= $params->get('linkvenue', 1) ? JRoute::_( EventListHelperRoute::getRoute($row->venueslug, 'venueevents') ) : '';
			$lists[$i]->categorylink	= $params->get('linkcategory', 1) ? JRoute::_( EventListHelperRoute::getRoute($row->categoryslug, 'categoryevents') ) : '';
			$lists[$i]->date 			= modEventListwideHelper::_format_date($row, $params);
			$lists[$i]->time 			= $row->times ? modEventListwideHelper::_format_time($row->dates, $row->times, $params) : '' ;
			$lists[$i]->eventimage		= JURI::base(true).'/'.$dimage['thumb'];
			$lists[$i]->eventimageorig	= JURI::base(true).'/'.$dimage['original'];
			$lists[$i]->venueimage		= JURI::base(true).'/'.$limage['thumb'];
			$lists[$i]->venueimageorig	= JURI::base(true).'/'.$limage['original'];
			$lists[$i]->eventdescription= strip_tags( $row->datdescription );
			$lists[$i]->venuedescription= strip_tags( $row->locdescription );
			$i++;
		}
		
		return $lists;
	}

	/**
	 * Method to format date information
	 *
	 * @access public
	 * @return string
	 */
	function _format_date($row, &$params)
	{
		//Get needed timestamps and format
		$yesterday_stamp	= mktime(0, 0, 0, date("m") , date("d")-1, date("Y"));
		$yesterday 			= strftime("%Y-%m-%d", $yesterday_stamp);
		$today_stamp		= mktime(0, 0, 0, date("m") , date("d"), date("Y"));
		$today 				= date('Y-m-d');
		$tomorrow_stamp 	= mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
		$tomorrow 			= strftime("%Y-%m-%d", $tomorrow_stamp);
		
		$dates_stamp		= strtotime($row->dates);
		$enddates_stamp		= $row->enddates ? strtotime($row->enddates) : null;

		//if datemethod show day difference
		if($params->get('datemethod', 1) == 2) {
			//check if today or tomorrow
			if($row->dates == $today) {
				$result = JText::_( 'TODAY' );
			} elseif($row->dates == $tomorrow) {
				$result = JText::_( 'TOMORROW' );
			} elseif($row->dates == $yesterday) {
				$result = JText::_( 'YESTERDAY' );
			
			//This one isn't very different from the DAYS AGO output but it seems 
			//adequate to use a different language string here.
			//
			//the event has an enddate and it's earlier than yesterday
			} elseif($row->enddates && $enddates_stamp < $yesterday_stamp) {
				$days = round( ($today_stamp - $enddates_stamp) / 86400 );
				$result = JText::sprintf( 'ENDED DAYS AGO', $days );
				
			//the event has an enddate and it's later than today but the startdate is earlier than today
			//means a currently running event 
			} elseif($row->enddates && $enddates_stamp > $today_stamp && $dates_stamp < $today_stamp) {
				$days = round( ($today_stamp - $dates_stamp) / 86400 );
				$result = JText::sprintf( 'STARTED DAYS AGO', $days );
				
			//the events date is earlier than yesterday
			} elseif($dates_stamp < $yesterday_stamp) {
				$days = round( ($today_stamp - $dates_stamp) / 86400 );
				$result = JText::sprintf( 'DAYS AGO', $days );
				
			//the events date is later than tomorrow
			} elseif($dates_stamp > $tomorrow_stamp) {
				$days = round( ($dates_stamp - $today_stamp) / 86400 );
				$result = JText::sprintf( 'DAYS AHEAD', $days );
			}
		} else {
			//single day event
			$date = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime( $row->dates.' '.$row->times ));
			$result = JText::sprintf('ON DATE', $date);
			
			//Upcoming multidayevent (From 16.10.2008 Until 18.08.2008)
			if($dates_stamp > $tomorrow_stamp && $enddates_stamp) {
				$startdate = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime( $row->dates.' '.$row->times ));
				$enddate = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime( $row->enddates.' '.$row->endtimes ));
				$result = JText::sprintf('FROM UNTIL', $startdate, $enddate);
			}
			
			//current multidayevent (Until 18.08.2008)
			if( $row->enddates && $enddates_stamp > $today_stamp && $dates_stamp < $today_stamp ) {
				//format date
				$result = strftime($params->get('formatdate', '%d.%m.%Y'), strtotime( $row->enddates.' '.$row->endtimes ));
				$result = JText::sprintf('UNTIL', $result);
			}
		}
				
		return $result;
	}
	/**
	 * Method to format time information
	 *
	 * @access public
	 * @return string
	 */
	function _format_time($date, $time, &$params)
	{
		$time = strftime( $params->get('formattime', '%H:%M'), strtotime( $date.' '.$time ));

		return $time;
	}
}