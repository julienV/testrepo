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
 * EventList Component Archive Controller
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerArchive extends EventListController
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
	 * unarchives an Event
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function unarchive()
	{		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('". JText::_( 'Select an item to unpublish' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}
		
		$model = $this->getModel('archive');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}
		
		$total = count( $cid );
		$msg 	= $total.' '.JText::_('EVENT UNARCHIVED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=archive', $msg );
	}
	
	/**
	 * removes an Event
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function remove()
	{
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$total = count( $cid );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('archive');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$msg = $total.' '.JText::_( 'EVENTS DELETED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=archive', $msg );
	}
}
?>