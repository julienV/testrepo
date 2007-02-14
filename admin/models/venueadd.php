<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * EventList Component Venueadd Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListModelVenueadd extends JModel
{
	/**
	 * Event data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Logic for the event edit screen
	 *
	 * @access public
	 * @return array
	 * @since 0.9
	 */
	function &getData()
	{
		$this->_initData();

		return $this->_data;
	}

	/**
	 * Method to initialise the venue data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$venue = new stdClass();
			$venue->id					= 0;
			$venue->club				= null;
			$venue->url					= null;
			$venue->street				= null;
			$venue->city				= null;
			$venue->plz					= null;
			$venue->state				= null;
			$venue->country				= null;
			$venue->locimage			= JText::_('SELECTIMAGE');
			$venue->published			= 1;
			$venue->locdescription		= null;
			$this->_data				= $venue;
			return (boolean) $this->_data;
		}
		return true;
	}
}
?>