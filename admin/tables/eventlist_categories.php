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
 * EventList categories Model class
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class eventlist_categories extends JTable 
{
	/**
	 * Primary Key
	 * @var int 
	 */
	var $id 				= null;
	/** @var int */
	var $parent_id			= 0;
	/** @var string */
	var $catname 			= '';
	/** @var string */
	var $catdescription 	= null;
	/** @var string */
	var $meta_description 	= null;
	/** @var string */
	var $meta_keywords		= null;
	/** @var string */
	var $image 				= '';
	/** @var int */
	var $published			= null;
	/** @var int */
	var $checked_out 		= 0;
	/** @var date */
	var $checked_out_time	= 0;
	/** @var int */
	var $access 			= 0;
	/** @var int */
	var $groupid 			= 0;
	/** @var string */
	var $maintainers		= null;
	/** @var int */
	var $ordering 			= null;

	function eventlist_categories(& $db) {
		parent::__construct('#__eventlist_categories', 'id', $db);
	}
}
?>