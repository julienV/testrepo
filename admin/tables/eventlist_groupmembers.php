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
 * EventList groupmembers Model class
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_groupmembers extends JTable 
{
	/**
	 * Primary Key
	 * @var int 
	 */
	var $id 				= null;
	/** @var int */
	var $member				= null;

	function eventlist_groupmembers(& $db) {
		parent::__construct('#__eventlist_groupmembers', '', $db);
	}
}
?>