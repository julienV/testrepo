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
 * EventList groups Model class
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_groups extends JTable 
{
	/**
	 * Primary Key
	 * @var int 
	 */
	var $id 				= null;
	/** @var int */
	var $name				= '';
	/** @var string */
	var $description 		= null;
	/** @var int */
	var $checked_out 		= 0;
	/** @var date */
	var $checked_out_time	= 0;

	function eventlist_groups(& $db) {
		parent::__construct('#__eventlist_groups', 'id', $db);
	}
}
?>