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
 * EventList venues Model class
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_venues extends JTable 
{
	/**
	 * Primary Key
	 * @var int 
	 */
	var $id 				= null;
	/** @var string */
	var $venue 				= null;
	/** @var string */
	var $url 				= null;
	/** @var string */
	var $street 			= null;
	/** @var string */
	var $plz 				= null;
	/** @var string */
	var $city 				= null;
	/** @var string */
	var $state				= null;
	/** @var string */
	var $country			= null;
	/** @var string */
	var $locdescription 	= null;
	/** @var string */
	var $meta_description 	= null;
	/** @var string */
	var $meta_keywords		= null;
	/** @var string */
	var $locimage 			= null;
	/** @var int */
	var $created_by			= null;
	/** @var string */
	var $author_ip	 		= null;
	/** @var date */
	var $created		 	= null;
	/** @var date */
	var $modified 			= null;
	/** @var int */
	var $modified_by 		= null;
	/** @var int */
	var $published	 		= null;
	/** @var int */
	var $checked_out 		= null;
	/** @var date */
	var $checked_out_time 	= null;
	/** @var int */
	var $ordering 			= null;

	function eventlist_venues(& $db) {
		parent::__construct('#__eventlist_venues', 'id', $db);
	}
}
?>