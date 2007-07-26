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
	/** @var string */
	var $alias	 			= '';
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
	/** @var int */
	var $recurrence_number	= 0;
	/** @var int */
	var $recurrence_type	= 0;
	/** @var date */
	var $recurrence_counter = null;
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

	// overloaded check function
	function check($elsettings)
	{
		// Check fields
		if (empty($this->enddates)) {
			$this->enddates = NULL;
		}

		if (empty($this->times)) {
			$this->times = NULL;
		}

		if (empty($this->endtimes)) {
			$this->endtimes = NULL;
		}

		jimport('joomla.filter.output');
		$alias = JFilterOutput::stringURLSafe($this->title);

		if(empty($this->alias) || $this->alias === $alias ) {
			$this->alias = $alias;
		}

		//if (isset($this->dates)) {
			if (!preg_match("/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/", $this->dates)) {
	 	     	$this->_error = JText::_( 'DATE WRONG' );
	 	     	JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
	 	     	return false;
			}
		//}

		if ($row->enddates != 0) {
			if (!preg_match("/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/", $row->enddates)) {
				$this->_error = JText::_( 'ENDDATE WRONG FORMAT');
				JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
				return false;
			}
		}

		if (($elsettings->showtime == 1) || (!empty($this->times))) {
			if (isset($this->times)) {
   				if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $this->times)) {
      				$this->_error = JText::_( 'TIME WRONG' );
      				JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
      				return false;
	  			}
			}
		}

		if (!preg_match("/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/", $this->recurrence_counter)) {
	 	     	$this->_error = JText::_( 'DATE WRONG' );
	 	     	JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
	 	     	return false;
			}

		if ($this->endtimes != 0) {
   			if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $this->endtimes)) {
      			$this->_error = JText::_( 'TIME WRONG' );
      			JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
      			return false;
	  		}
		}

		$this->title = strip_tags($this->title);
		$titlelength = JString::strlen($this->title);

		if ($titlelength > 100 || $this->title =='') {
      		$this->_error = JText::_( 'ERROR TITLE LONG' );
      		JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
      		return false;
		}

		//No venue or category choosen?
		if($this->locid == '') {
			$this->_error = JText::_( 'VENUE EMPTY');
			JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
			return false;
		}

		if($this->catsid == 0) {
			$this->_error = JText::_( 'CATEGORY EMPTY');
			JError::raiseWarning('SOME_ERROR_CODE', $this->_error );
			return false;
		}

		return true;
	}
}
?>