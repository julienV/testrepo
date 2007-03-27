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
 * EventList events Model class
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_events extends JTable
{
	/**
	 * Primary Key
	 * @var int 
	 */
	var $id 				= null;
	/** @var int */
	var $locid 				= null;
	/** @var int */
	var $catsid 			= null;
	/** @var date */
	var $dates 				= null;
	/** @var date */
	var $enddates 			= null;
	/** @var date */
	var $times 				= null;
	/** @var date */
	var $endtimes 			= null;
	/** @var string */
	var $title 				= '';
	/** @var int */
	var $created_by			= null;
	/** @var int */
	var $modified 			= 0;
	/** @var int */
	var $modified_by 		= null;
	/** @var string */
	var $datdescription 	= null;
	/** @var string */
	var $meta_description 	= null;
	/** @var string */
	var $meta_keywords		= null;
	/** @var string */
	var $datimage 			= '';
	/** @var string */
	var $author_ip 			= null;
	/** @var date */
	var $created	 		= null;
	/** @var int */
	var $published 			= null;
	/** @var int */
	var $registra 			= null;
	/** @var int */
	var $unregistra 		= null;
	/** @var int */
	var $checked_out 		= null;
	/** @var date */
	var $checked_out_time 	= 0;

	function eventlist_events(& $db) {
		parent::__construct('#__eventlist_events', 'id', $db);
	}
}
?>