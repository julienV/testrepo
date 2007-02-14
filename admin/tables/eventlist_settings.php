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
 * EventList settings table class
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_settings extends JTable
{
	/**
	 * Unique Key
	 * @var int 
	 */
	var $id					= "1";
	var $oldevent 			= "2";
	var $minus 				= "1";
	var $showtime 			= "0";
	var $showtitle 			= "1";
	var $showlocate 		= "1";
	var $showcity 			= "1";
	var $showmapserv 		= "0";
	var $map24id 			= "";
	var $tablewidth 		= null;
	var $datewidth 			= null;
	var $titlewidth 		= null;
	var $infobuttonwidth 	= null;
	var $locationwidth 		= null;
	var $citywidth 			= null;
	var $datename 			= null;
	var $titlename 			= null;
	var $infobuttonname 	= null;
	var $locationname 		= null;
	var $cityname 			= null;
	var $formatdate 		= null;
	var $formattime 		= null;
	var $timename 			= null;
	var $showdetails 		= "1";
	var $showtimedetails 	= "1";
	var $showevdescription 	= "1";
	var $showdetailstitle 	= "1";
	var $showdetailsadress 	= "1";
	var $showlocdescription = "1";
	var $showlinkclub 		= "1";
	var $showdetlinkclub 	= "1";
	var $delivereventsyes 	= "-2";
	var $mailinform 		= "0";
	var $mailinformrec 		= null;
	var $mailinformrec2 	= null;
	var $datdesclimit 		= "1000";
	var $autopubl 			= "-2";
	var $deliverlocsyes 	= "-2";
	var $autopublocate 		= "-2";
	var $showcat 			= "0";
	var $catfrowidth 		= "";
	var $catfroname 		= null;
	var $evfrontview 		= "";
	var $evdelrec 			= "1";
	var $evpubrec 			= "1";
	var $locdelrec 			= "1";
	var $locpubrec 			= "1";
	var $sizelimit 			= "100";
	var $imagehight 		= "100";
	var $imagewidth 		= "100";
	var $imageprob 			= "1";
	var $gddisabled 		= "0";
	var $imageenabled 		= "1";
	var $comunsolution 		= "0";
	var $comunoption 		= "0";
	var $catlinklist 		= "0";
	var $showfroregistra 	= "0";
	var $showfrounregistra 	= "0";
	var $eventedit 			= "-2";
	var $eventeditrec 		= "1";
	var $eventowner 		= "0";
	var $venueedit 			= "-2";
	var $venueeditrec 		= "1";
	var $venueowner 		= "0";
	var $lightbox 			= "0";
	var $meta_keywords 		= null;
	var $meta_description 	= null;
	var $showstate 			= "0";
	var $statename 			= null;
	var $statewidth 		= null;
	var $lastupdate 		= null;

	function eventlist_settings(& $db) {
		parent::__construct('#__eventlist_settings', 'id', $db);
	}
}
?>