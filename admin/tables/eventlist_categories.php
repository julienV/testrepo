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
	var $alias	 			= '';
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

	/**
	* @param database A database connector object
	*/
	function eventlist_categories(& $db) {
		parent::__construct('#__eventlist_categories', 'id', $db);
	}

	// overloaded check function
	function check()
	{
		global $mainframe;

		// Not typed in a category name?
		if (trim( $this->catname ) == '') {
			$this->_error = JText::_( 'ADD NAME CATEGORY' );
			return false;
		}

		jimport('joomla.filter.output');
		$alias = JOutputFilter::stringURLSafe($this->catname);

		if(empty($this->alias) || $this->alias === $alias ) {
			$this->alias = $alias;
		}
/*
		// check for existing name
		$query = 'SELECT id FROM #__eventlist_categories WHERE catname = "' .$this->catname. '"';
		$this->_db->setQuery($query);

		$xid = (int)$this->_db->loadResult();
		if ($xid && ($xid != (int)$this->id)) {
			$this->_error = JText::_( 'CATEGORY NAME ALREADY EXIST' );
			return false;
		}
*/
		return true;
	}
}
?>