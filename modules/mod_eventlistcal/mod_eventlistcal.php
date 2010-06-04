<?php
/**
* @version 0.9 $Id$
* @package Eventlist CalModul
* @copyright (C) 2005 - 2007 Christoph Lukes / Keith Devens
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL

* this modified version for EventList is based on PHP Calendar 2.3 originally from Keith Devens 

* PHP Calendar (version 2.3), written by Keith Devens
* http://keithdevens.com/software/php_calendar
* see example at http://keithdevens.com/weblog
* License: http://keithdevens.com/software/license
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_SITE.DS.'components'.DS.'com_eventlist'.DS.'helpers'.DS.'route.php');

JHTML::_('behavior.tooltip', '.hasTipCal');

$db			=& JFactory::getDBO();
$user		=& JFactory::getUser();

//get switch trigger
$req_month 		= (int)JRequest::getVar( 'el_mcal_month', '', 'request');
$req_year       = (int)JRequest::getVar( 'el_mcal_year', '', 'request');

// Parameters
$day_name_length	= $params->get( 'day_name_length', '2' );
$first_day			= $params->get( 'first_day', '1' );
$todayback			= trim( $params->get( 'todayback' ));
$todayfontcolor		= trim( $params->get( 'todayfontcolor' ));
$Year_length		= $params->get( 'Year_length', '1' );
$Month_length		= $params->get( 'Month_length', '0' );

//Requested URL
$request_link =  strstr ($_SERVER['REQUEST_URI'], 'index.php?');

if ($request_link == '') {
	$request_link = 'index.php?';
}

//set now
$time 			= time();
$today_month 	= date( 'm', $time);
$today_year 	= date( 'Y', $time);
$today          = date( 'j',$time);

if ($req_month == 0) $req_month = $today_month;
if ($req_year == 0) $req_year = $today_year;

//Setting the previous an next month numbers
$prev_month_year = $req_year;
$next_month_year = $req_year;

$prev_month = $req_month-1;
if($prev_month < 1){
	$prev_month = 12;
	$prev_month_year = $prev_month_year-1;
}

$next_month = $req_month+1;
if($next_month > 12){
	$next_month = 1;
	$next_month_year = $next_month_year+1;
}

$monthstart = mktime(0, 0, 1, $req_month, 1, $req_year);
$monthend = mktime(0, 0, -1, $req_month+1, 1, $req_year);
        
//Create Links
$prev_link =  JURI::base(true).'/'.$request_link.'&el_mcal_month='.$prev_month.'&el_mcal_year='.$prev_month_year;
$next_link =  JURI::base(true).'/'.$request_link.'&el_mcal_month='.$next_month.'&el_mcal_year='.$next_month_year;

//Get eventdates
				$filter_date_from = $db->Quote(strftime('%Y-%m-%d', $monthstart));
				$date_where = ' AND DATEDIFF(IF (a.enddates IS NOT NULL AND a.enddates <> '. $db->Quote('0000-00-00') .', a.enddates, a.dates), '. $filter_date_from .') >= 0';
				$filter_date_to = $db->Quote(strftime('%Y-%m-%d', $monthend));
				$date_where .= ' AND DATEDIFF(a.dates, '. $filter_date_to .') <= 0';

$query = 'SELECT DATEDIFF(a.enddates, a.dates) AS datediff, a.title, a.dates, a.times, DAYOFMONTH(a.dates) AS day, YEAR(a.dates) AS year, MONTH(a.dates) AS month'
. ' FROM #__eventlist_events AS a'
. ' INNER JOIN #__eventlist_cats_event_relations AS rel ON rel.itemid = a.id'
. ' INNER JOIN #__eventlist_categories AS c ON c.id = rel.catid'
. ' WHERE a.published = 1'
. $date_where
. ' AND c.access <= '.(int)$user->aid
. ' GROUP BY a.id'
;

$db->setQuery( $query );
$events = $db->loadObjectList();

$days = array();
foreach ( $events as $event )
{
		$day = $event->day;
		
		for ($counter = 0; $counter <= $event->datediff+1; $counter++)
		{
			$thisday = mktime(0, 0, 0, $event->month, $day, $event->year);
			if ($thisday < $monthend) // still same month
			{
				if (isset($days[$day])) 
				{
					$days[$day]['events'][] = htmlspecialchars($event->title);
				}
				else 
				{
					$link	= JRoute::_( EventListHelperRoute::getRoute(strftime('%Y%m%d', $thisday), 'day') );
					$days[$day] = array();
					$days[$day]['events'] = array(htmlspecialchars($event->title));		
					$days[$day]['link'] = $link;
				}				
				$day++;
			}
			else {
				break;
			}
		}						
}

//Month Names
$first_of_month = gmmktime(0, 0, 0, $prev_month, 1, $year);
list($tmp, $year, $prev_month, $weekday) = explode(',', gmstrftime('%m,%Y,%b,%w', $first_of_month));

$first_of_month = gmmktime(0, 0, 0, $next_month, 1, $year);
list($tmp, $year, $next_month, $weekday) = explode(',', gmstrftime('%m,%Y,%b,%w', $first_of_month));

//Creating switching links
$pn = array( $prev_month=>$prev_link, $next_month=>$next_link);

//Output
echo "<div align=center>";
echo generate_calendar($req_year, $req_month, $days, $day_name_length, NULL, $first_day, $pn, $todayback, $todayfontcolor, $Year_length, $Month_length);
echo "</div>";

// calendar function
function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array(), $todayback, $todayfontcolor, $Year_length, $Month_length)
{
	$first_of_month = gmmktime(0, 0, 0, $month, 1, $year);
	#remember that mktime will automatically correct if invalid dates are entered
	# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	# this provides a built in "rounding" feature to generate_calendar()

	$day_names = array(); #generate all the day names according to the current locale
	for( $n = 0, $t = ( 3 + $first_day ) *24 *60 *60; $n < 7; ++$n, $t += 24 *60 *60) #January 4, 1970 was a Sunday
	$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

	list($month, $year, $month_name, $weekday) = explode(',', gmstrftime('%m,%Y,%B,%w', $first_of_month));
	$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
	$year_length = $Year_length ? $year : substr($year, 2, 3);
	$month_length = $Month_length ?  substr($month_name,0,3) : $month_name;
	$title   = htmlentities(ucfirst($month_length)).'&nbsp;'.$year_length;  #note that some locales don't capitalize month and day names

	#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
	if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
	if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';

	$calendar = '<table class="calendar">'."\n".
	'<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

	if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
		#if day_name_length is >3, the full name of the day will be printed
		foreach($day_names as $d)
		$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
		$calendar .= "</tr>\n<tr>";
	}

	// Today
	$time 		= time();
	$today 		= date( 'j', $time);
	$currmonth 	= date( 'm', $time);
	$curryear 	= date( 'Y', $time);

	if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days

	for($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++) {

		if($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}

		if (($day == $today) & ($currmonth == $month) & ($curryear == $year)) {
			$istoday = 1;
		} else {
			$istoday = 0;
		}

		//space in front of daynumber when day < 10
		($day < 10) ? $space = '&nbsp;&nbsp;': $space = '';

		if(isset($days[$day])) {
			$link = $days[$day]['link'];
			$count_evs = count($days[$day]['events']);
			
			$ev_tooltip = ($count_evs > 10) ? implode('<br>', array_slice($days[$day]['events'], 0, 10)).'<br>...' : implode('<br>', $days[$day]['events']);
			$calendar .= '<td style="text-align: right;">'.($link ? '<a href="'.$link.'" class="hasTipCal" title="'. $count_evs . ' ' . JText::_('Events') .'::'.$ev_tooltip.'">'.$space.$day.'</a>' : $space.$day).'</td>';
		} else {
			$calendar .= '<td style="text-align: right;">'.($istoday && $todayback ? '<span style="background-color:'. $todayback. '; font-weight:bold; color:'. $todayfontcolor. '; ">'.$space.$day.'</span>' : $space.$day).'</td>';
		}
	}
	if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

	return $calendar."</tr>\n</table>\n";
}
?>