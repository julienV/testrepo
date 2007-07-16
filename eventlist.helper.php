<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
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

  function config()
  {
    $db =& JFactory::getDBO();

    $sql = 'SELECT * FROM #__eventlist_settings WHERE id = 1';
    $db->setQuery($sql);
    $config = $db->loadObject();

    return $config;
  }

  /**
   * Moves old events in the archive or delete them
   *
   * @since 0.9
   */
  function cleanevents($lastupdate)
  {
    $now = time();

    //last update later then 24h?
    $difference = $now - $lastupdate;
    if ( $difference > 86400 ) {
      $db			= & JFactory::getDBO();
      $elsettings = ELHelper::config();

      $nulldate = '0000-00-00';
      $query = 'SELECT * FROM #__eventlist_events WHERE DATE_SUB(NOW(), INTERVAL '.$elsettings->minus.' DAY) > (IF (enddates <> '.$nulldate.', enddates, dates)) AND recurrence_number <> "0" AND recurrence_type <> "0" AND (recurrence_counter = "'.$nulldate.'" OR recurrence_counter > "'.date("Y-m-d").'")';
      $db->SetQuery( $query );
      $recurrence_array = $db->loadAssocList();
      foreach($recurrence_array as $recurrence_row) {
        $insert_keys = '';
        $insert_values = '';
        $wherequery = '';
        // get the recurrence information
        $recurrence_number = $recurrence_row['recurrence_number'];
        $recurrence_type = $recurrence_row['recurrence_type'];
        switch ($recurrence_type) {
          case "1": // dayly
            $recurrence_name = "days";
            break;
          case "2": //weekly
            $recurrence_name = "weeks";
            break;
          case "3": // monthly
            $recurrence_name = "months";
            break;
           // weekday
          case "4":
            $recurrence_weekday = "Monday";
            break;
          case "5":
            $recurrence_weekday = "Tuesday";
            break;
          case "6":
            $recurrence_weekday = "Wednesday";
            break;
          case "7":
            $recurrence_weekday = "Thursday";
            break;
          case "8":
            $recurrence_weekday = "Friday";
            break;
          case "9":
            $recurrence_weekday = "Saturday";
            break;
          case "10":
            $recurrence_weekday = "Sunday";
            break;
        }
        // dayly, weekly and monthly recurrences have the same solution
        if ($recurrence_type < 4) {
          $recurrence_row['dates'] =gmdate("Y-m-d", strtotime($recurrence_row['dates']." +".$recurrence_number." ".$recurrence_name." +1 day"));
          if ($recurrence_row['enddates']) {
            $recurrence_row['enddates'] = gmdate("Y-m-d", strtotime($recurrence_row['enddates']." +".$recurrence_number." ".$recurrence_name." +1 day"));
          } else {
            $recurrence_row['enddates'] = "null";
          }
        } else {
          $dates = getdate(strtotime($recurrence_row['dates']));
          $year = $dates['year'];
          $month = $dates['mon'];
          if ($month == 12) {
            $month = "01";
            $year++;
          } else {
            if (++$month < 10) {
              $month = "0".$month;
            }
          }
          $new_date = gmdate("Y-m-d", strtotime($year."-".$month."-01 -1 day"));
          $new_date =gmdate("Y-m-d", strtotime($new_date." next ".$recurrence_weekday));
          echo $recurrence_row['enddates']." Hallo";
          if ($recurrence_row['enddates']) {
            $timediff = (strtotime($recurrence_row['enddates']) - strtotime($recurrence_row['dates']));
            $days = strftime("%j",$timediff) - 1;
            $recurrence_row['enddates'] = gmdate("Y-m-d", strtotime($new_date." +".((7 * ($recurrence_number - 1)) + $days + 2)." days"));
          } else {
            $recurrence_row['enddates'] = "null";
          }
          $recurrence_row['dates'] =gmdate("Y-m-d", strtotime($new_date." +".((7 * ($recurrence_number - 1)) + 2)." days"));

        }
        // create the INSERT query
        foreach ($recurrence_row as $key => $result) {
          if ($key != 'id') {
            if ($insert_keys != '') {
              if (!(($result == "" || $result == "null") && $key == "enddates") && !(($result == "" || $result == "null") && $key == "endtimes")) {
                $wherequery .= ' AND ';
                $insert_keys .= ', ';
              	$insert_values .= ', ';
              }
            }
            if (!(($result == "" || $result == "null") && $key == "enddates") && !(($result == "" || $result == "null") && $key == "endtimes")) {
            	$insert_keys .= $key;
            	$insert_values .= "'".$result."'";
              	$wherequery .= '`'.$key.'` = "'.$result.'"';
            }
          }
        }
        $query = 'SELECT id FROM #__eventlist_events WHERE '.$wherequery.';';
        $db->SetQuery( $query );
        if (count($db->loadAssocList()) == 0) {
          $query = 'INSERT INTO #__eventlist_events ('.$insert_keys.') VALUES ('.$insert_values.');';
          $db->SetQuery( $query );
          $db->Query();
        }
      }
      if ($elsettings->oldevent == 1) {
        $query = 'DELETE FROM #__eventlist_events WHERE DATE_SUB(NOW(), INTERVAL '.$elsettings->minus.' DAY) > (IF (enddates <> '.$nulldate.', enddates, dates))';
        $db->SetQuery( $query );
        $db->Query();
      }

      if ($elsettings->oldevent == 2) {
        $query = 'UPDATE #__eventlist_events SET published = -1 WHERE DATE_SUB(NOW(), INTERVAL '.$elsettings->minus.' DAY) > (IF (enddates <> '.$nulldate.', enddates, dates))';
        $db->SetQuery( $query );
        $db->Query();
      }

      $query = 'UPDATE #__eventlist_settings SET lastupdate = '.time().' WHERE id = 1';
      $db->SetQuery( $query );
      $db->Query();
    }
  }
}
?>