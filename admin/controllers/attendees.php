<?php 
/**
* @version 0.9 $Id$
* @package Joomla 
* @subpackage EventList
* @copyright (C) 2005 - 2007 Christoph Lukes
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * EventList Component Attendees Controller
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerAttendees extends EventListController
{
	/**
	 * Constructor
	 * 
	 *@since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Delete attendees
	 *
	 * @return true on sucess
	 * @access private
	 * @since 0.9
	 */
	function remove()
	{
		global $option;

		$cid 	= JRequest::getVar('cid');
		$rcid 	= JRequest::getVar('rcid');
		$total 	= count( $cid );

		$model = $this->getModel('attendees');

		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$msg = $total.' '.JText::_( 'REGISTERED USERS DELETED');

		$this->setRedirect( 'index.php?option='. $option .'&view=attendees&rcid='.$rcid, $msg );
	}
}
?>