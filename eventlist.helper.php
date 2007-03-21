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

		$sql = "SELECT * FROM #__eventlist_settings WHERE id = 1";
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