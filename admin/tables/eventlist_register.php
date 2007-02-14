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
 * EventList registration Model class
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_register extends JTable
{
	/**
	 * Primary Key
	 * @var int 
	 */
	var $rid 		= null;
	/** @var int */
	var $rdid 		= null;
	/** @var int */
	var $uid 		= null;
	/** @var string */
	var $urname 	= null;
	/** @var date */
	var $uregdate 	= null;
	/** @var string */
	var $uip 		= null;

	function eventlist_register(& $db) {
		parent::__construct('#__eventlist_register', 'rid', $db);
	}
}
?>