<?php
/**
 * @version 1.1 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
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

defined('_JEXEC') or die('Restricted access');

/**
 *
 * Holds some usefull functions to keep the code a bit cleaner
 *
 * @package Joomla
 * @subpackage EventList
 */
class ELHelper {

	/**
	 * Pulls settings from database and stores in an static object
	 *
	 * @return object
	 * @since 0.9
	 */
	function &config()
	{
		static $config;

		if (!is_object($config))
		{
			$db 	= & JFactory::getDBO();
			$sql 	= 'SELECT * FROM #__eventlist_settings WHERE id = 1';
			$db->setQuery($sql);
			$config = $db->loadObject();
			
			$config->params = JComponentHelper::getParams('com_eventlist');
		}

		return $config;
	}

	/**
   	* Performs dayly scheduled cleanups
   	*
   	* Currently it archives and removes outdated events
   	* and takes care of the recurrence of events
   	*
 	* @since 0.9
   	*/
	function cleanup($forced = 0)
	{
		$elsettings = & ELHelper::config();
    	$params = &JComponentHelper::getParams('com_eventlist');   
    	$weekstart = $params->get('weekdaystart',0);
    	$anticipation = $params->get('recurrence_anticipation', 30);

		$now 		= time();
		$lastupdate = $elsettings->lastupdate;

		//last update later then 24h?
		//$difference = $now - $lastupdate;

		//if ( $difference > 86400 ) {

		//better: new day since last update?
		$nrdaysnow = floor($now / 86400);
		$nrdaysupdate = floor($lastupdate / 86400);

		if ( $nrdaysnow > $nrdaysupdate || $forced ) {

			$db			= & JFactory::getDBO();

			// get the last event occurence of each recurring published events, with unlimited repeat, or last date not passed.
			$nulldate = '0000-00-00';
			$query = ' SELECT id, CASE recurrence_first_id WHEN 0 THEN id ELSE recurrence_first_id END AS first_id, '
			       . ' recurrence_number, recurrence_type, recurrence_limit_date, recurrence_limit, recurrence_byday, '
			       . ' MAX(dates) as dates, MAX(enddates) as enddates, MAX(recurrence_counter) as counter '
			       . ' FROM #__eventlist_events '
			       . ' WHERE recurrence_type <> "0" '
			       . ' AND CASE recurrence_limit_date WHEN '.$nulldate.' THEN 1 ELSE NOW() < recurrence_limit_date END '
			       . ' AND recurrence_number <> "0" '
			       . ' AND `published` = 1 '
			       . ' GROUP BY first_id'
			       . ' ORDER BY dates DESC';
			$db->SetQuery( $query );
			$recurrence_array = $db->loadAssocList();
			
			foreach($recurrence_array as $recurrence_row) 
			{
				// get the info of reference event for the duplicates
				$ref_event = & JTable::getInstance('eventlist_events', '');
				$ref_event->load($recurrence_row['id']);
								
				// get the recurrence information
				$recurrence_number = $recurrence_row['recurrence_number'];
				$recurrence_type = $recurrence_row['recurrence_type'];
				
				// the first day of the week is used for certain rules
				$recurrence_row['weekstart'] = $weekstart;
				
				// calculate next occurence date
				$recurrence_row = ELHelper::calculate_recurrence($recurrence_row);
				
				// add events as long as we are under the interval and under the limit, if specified.				
				while (($recurrence_row['recurrence_limit_date'] == $nulldate || strtotime($recurrence_row['dates']) <= strtotime($recurrence_row['recurrence_limit_date'])) 
				     && strtotime($recurrence_row['dates']) <= time() + 86400*$anticipation) 
				{
					$new_event = & JTable::getInstance('eventlist_events', '');
					$new_event->bind($ref_event, array('id', 'hits', 'dates', 'enddates'));
					$new_event->recurrence_first_id = $recurrence_row['first_id'];
          			$new_event->recurrence_counter = $recurrence_row['counter'] + 1;
					$new_event->dates = $recurrence_row['dates'];
          			$new_event->enddates = $recurrence_row['enddates'];
          
          			if ($new_event->store())
          			{
          				$recurrence_row['counter']++;
	          			//duplicate categories event relationships
	          			$query = ' INSERT INTO #__eventlist_cats_event_relations (itemid, catid) '
	                 			. ' SELECT ' . $db->Quote($new_event->id) . ', catid FROM #__eventlist_cats_event_relations WHERE itemid = ' . $db->Quote($ref_event->id);
	          			$db->setQuery($query);
	          			if (!$db->query()) {
	          				echo JText::_('Error saving categories for event "' . $ref_event->title . '" new recurrences\n');
	          			}
          			}
          
          			$recurrence_row = ELHelper::calculate_recurrence($recurrence_row);
				}
			}

			//delete outdated events
			if ($elsettings->oldevent == 1) {
				$query = 'DELETE FROM #__eventlist_events WHERE DATE_SUB(NOW(), INTERVAL '.$elsettings->minus.' DAY) > (IF (enddates <> '.$nulldate.', enddates, dates))';
				$db->SetQuery( $query );
				$db->Query();
			}

			//Set state archived of outdated events
			if ($elsettings->oldevent == 2) {
				$query = 'UPDATE #__eventlist_events SET published = -1 WHERE DATE_SUB(NOW(), INTERVAL '.$elsettings->minus.' DAY) > (IF (enddates <> '.$nulldate.', enddates, dates)) AND published = 1';
				$db->SetQuery( $query );
				$db->Query();
			}
			
			//Set timestamp of last cleanup
			$query = 'UPDATE #__eventlist_settings SET lastupdate = '.time().' WHERE id = 1';
			$db->SetQuery( $query );
			$db->Query();
		}
	}

	/**
	 * this methode calculate the next date
	 */
	function calculate_recurrence($recurrence_row) 
	{
		// get the recurrence information
		$recurrence_number = $recurrence_row['recurrence_number'];
		$recurrence_type = $recurrence_row['recurrence_type'];

		$day_time = 86400;	// 60 sec. * 60 min. * 24 h
		$week_time = 604800;// $day_time * 7days
		$date_array = ELHelper::generate_date($recurrence_row['dates'], $recurrence_row['enddates']);

		switch($recurrence_type) {
			case "1":
				// +1 hour for the Summer to Winter clock change
				$start_day = mktime(1,0,0,$date_array["month"],$date_array["day"],$date_array["year"]);
				$start_day = $start_day + ($recurrence_number * $day_time);
				break;
			case "2":
				// +1 hour for the Summer to Winter clock change
				$start_day = mktime(1,0,0,$date_array["month"],$date_array["day"],$date_array["year"]);
				$start_day = $start_day + ($recurrence_number * $week_time);
				break;
			case "3": // month recurrence
			  /*
			   * warning here, we have to make sure the date exists: 31 of october + 1 month = 31 of november, which doesn't exists => skip the date!
			   */
				$start_day = mktime(1,0,0,($date_array["month"] + $recurrence_number),$date_array["day"],$date_array["year"]);

        $interval = $recurrence_number;        
        while ((date('m', $start_day) + 12 - $date_array["month"]) % 12 != $interval) {
          $interval += $recurrence_number;
          $start_day = mktime(1,0,0,($date_array["month"] + $interval),$date_array["day"],$date_array["year"]);
        }
				break;
			case "4":
        		$selected = ELHelper::convert2CharsDaysToInt(explode(',', $recurrence_row['recurrence_byday']), $recurrence_row['weekstart']);  // the selected weekdays
        		$current_weekday = (int) $date_array["weekday"];
				
        		if ($recurrence_row['weekstart'] == 1) {
        			// O for monday, not sunday
        			$current_weekday = ($current_weekday + 6) % 7;
        		}
        
        		if (count($selected) == 0) {
        			// this shouldn't happen, but if it does, to prevent problem use the current weekday for the repetition.
          			JError::raiseWarning(500, JText::_( 'Empty weekday recurrence' ) );
          			$selected = array($current_weekday);
        		}
		
        		sort($selected);
				
				if ($recurrence_number < 5) {
					// 1. - 4. week in a month
					// look for the position of current start date in 'selected' days.
					if ($current_weekday >= max($selected)) {
						
						// last selected day of the week => next occurence is first selected day of next (interval) week
            			$start_day = $date_array["unixtime"] - ($current_weekday - $selected[0]) * $day_time + $week_time * $recurrence_number;
					
					} else{
						
						// next weekday
						foreach($selected as $k => $day)
	          			{
	            			if ($current_weekday < $day) {
                				$next_weekday = $day;
                			break;
	            			}
	          			}
						$start_day = $date_array["unixtime"] + ($next_weekday - $current_weekday) * $day_time;
					}
				} else {					
         			if ($current_weekday >= max($selected)) {
            			// next occurence is next month
          				// the last or the before last week in a month
            			// the last day in the new month
	         			$start_day = mktime(1,0,0,($date_array["month"] + 2),1,$date_array["year"]) - $day_time;
	          			$weekday_is = date("w",$start_day);
	          
	          			// first selected day of the week
	          			$weekday_must = $selected[0];
	          
	          			// calculate the day difference between these days
	          			if ($weekday_is >= $weekday_must) {
	            			$day_diff = $weekday_is - $weekday_must;
	          			} else {
	            			$day_diff = ($weekday_is - $weekday_must) + 7;
	          			}
						
	          			$start_day = ($start_day - ($day_diff * $day_time));
						
	          			if ($recurrence_number == 6) {  // before last?
	            			$start_day = $start_day - $week_time;
	          			}
          			} else {
          				// next weekday
            			foreach($selected as $k => $day)
            			{
              				if ($current_weekday < $day) {
                				$next_weekday = $day;
                			break;
              				}
            			}
            			// next occurence is next selected weekday
            			$start_day = $date_array["unixtime"] + ($next_weekday - $current_weekday) * $day_time;          	
          			}
				}
				break;
		}
		
		$recurrence_row['dates'] = date("Y-m-d", $start_day);
		
		if ($recurrence_row['enddates']) {
			$recurrence_row['enddates'] = date("Y-m-d", $start_day + $date_array["day_diff"]);
		}
		
		if ($start_day < $date_array["unixtime"]) {
			JError::raiseError(500, JText::_( 'Recurrence date generation error' ) );
		}
		
		return $recurrence_row;
	}

	/**
	 * this method generate the date string to a date array
	 *
	 * @var string the date string
	 * @return array the date informations
	 * @access public
	 */
	function generate_date($startdate, $enddate) {
		$startdate = explode("-",$startdate);
		$date_array = array("year" => $startdate[0],
							"month" => $startdate[1],
							"day" => $startdate[2],
							"weekday" => date("w",mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0])),
		          "unixtime" => mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0]));
		if ($enddate) {
			$enddate = explode("-", $enddate);
			$day_diff = (mktime(1,0,0,$enddate[1],$enddate[2],$enddate[0]) - mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0]));
			$date_array["day_diff"] = $day_diff;
		}
		return $date_array;
	}
	
	/**
	 * return day number of the week starting with 0 for first weekday
	 *
	 * @param array of 2 letters day
	 * @return array of int
	 */
	function convert2CharsDaysToInt($days, $firstday = 0)
	{
		$result = array();
		foreach ($days as $day)
		{
			if ($firstday == 0) // sunday
			{
				switch (strtoupper($day))
				{
					case 'SU':
						$result[] = 0;
						break;
		      		case 'MO':
	          			$result[] = 1;
	          			break;
		      		case 'TU':
	         			$result[] = 2;
	          			break;
		      		case 'WE':
	          			$result[] = 3;
	         			break;
		      		case 'TH':
	          			$result[] = 4;
	          			break;
		      		case 'FR':
	          			$result[] = 5;
	          			break;
		      		case 'SA':
	          			$result[] = 6;
	          			break;
		      		default:
		        		JError::raiseWarning(500, JText::_( 'Wrong ical day string' ) );
				}
			} else {
				
				//monday
        		switch (strtoupper($day))
        		{
          			case 'MO':
            			$result[] = 0;
            			break;
          			case 'TU':
            			$result[] = 1;
            			break;
          			case 'WE':
            			$result[] = 2;
            			break;
          			case 'TH':
            			$result[] = 3;
            			break;
          			case 'FR':
            			$result[] = 4;
            			break;
          			case 'SA':
            			$result[] = 5;
            			break;
          			case 'SU':
            			$result[] = 6;
            			break;
          			default:
            			JError::raiseWarning(500, JText::_( 'Wrong ical day string' ) );
        		}
      		}
		}
		
		return $result;
	}
	
	/**
	 * transforms <br /> and <br> back to \r\n
	 *
	 * @param string $string
	 * @return string
	 */
	function br2break($string)
	{
		return preg_replace("=<br(>|([\s/][^>]*)>)\r?\n?=i", "\r\n", $string);
	}

	/**
	 * use only some importent keys of the eventlist_events - database table for the where query
	 *
	 * @param string $key
	 * @return boolean
	 */
	function where_table_rows($key) {
		if ($key == 'locid' ||
		//	$key == 'catsid' ||
			$key == 'dates' ||
			$key == 'enddates' ||
			$key == 'times' ||
			$key == 'endtimes' ||
			$key == 'alias' ||
			$key == 'created_by') {
			return true;
		} else {
			return false;
		}
	}
	
	function buildtimeselect($max, $name, $selected, $class = 'class="inputbox"')
	{
		$timelist 	= array();

		foreach(range(0, $max) as $wert) {
		    if(strlen($wert) == 2) {
				$timelist[] = JHTML::_( 'select.option', $wert, $wert);
    		}else{
      			$timelist[] = JHTML::_( 'select.option', '0'.$wert, '0'.$wert);
    		}
		}
		return JHTML::_('select.genericlist', $timelist, $name, $class, 'value', 'text', $selected );
	}
 
	/**
	 * return country options from the database
	 *
	 * @return unknown
	 */
	function getCountryOptions()
	{
		$db   = & JFactory::getDBO();
    	$sql  = 'SELECT iso2 AS value, name AS text FROM #__eventlist_countries ORDER BY name';
    	$db->setQuery($sql);
    	
		return $db->loadObjectList();
  	}
}
?>