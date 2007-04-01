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
 * EventList Component Editcss Model
 *
 * @package Joomla 
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelEditcss extends JModel
{
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		//TODO: Limit access to administrators and super administrators
		
		parent::__construct();
	}
}
?>