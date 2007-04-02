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
 * EventList Component Events Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerEvents extends EventListController
{
	/**
	 * Constructor
	 * 
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask( 'apply', 		'save' );
		$this->registerTask( 'copy',	 	'edit' );
	}

	/**
	 * Logic to publish events
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function publish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('". JText::_( 'Select an item to publish' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}

		$model = $this->getModel('events');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('EVENT PUBLISHED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=events', $msg );
	}

	/**
	 * Logic to unpublish events
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function unpublish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('". JText::_( 'Select an item to unpublish' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}

		$model = $this->getModel('events');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('EVENT UNPUBLISHED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=events', $msg );
	}

	/**
	 * Logic to archive events
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function archive()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('". JText::_( 'Select an item to unpublish' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}

		$model = $this->getModel('events');
		if(!$model->publish($cid, -1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('EVENT ARCHIVED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=events', $msg );
	}

	/**
	 * logic for cancel an action
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function cancel()
	{
		global $option;

		$model = $this->getModel('event');

		$model->checkin();

		$this->setRedirect( 'index.php?option='.$option.'&view=events' );
	}

	/**
	 * logic to create the new event screen
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function add( )
	{
		global $option;

		$this->setRedirect( 'index.php?option='. $option .'&view=event' );
	}

	/**
	 * logic to create the edit event screen
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function edit( )
	{
		global $option;

		$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
		$id		= $cid[0];
		
		JRequest::setVar( 'view', 'event' );
		JRequest::setVar( 'hidemainmenu', 1 );
			
		$model = $this->getModel('event');
		
		/* Error if checkedout by another administrator
		if ($model->isCheckedOut( $user->get('id') )) {
			$this->setRedirect( 'index.php?option='.$option.'&task=categories', JText::_( 'THE CATEGORY' ).' '.$row->catname.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ) );
		}
		
		*/
		
		$task 	= JRequest::getVar( 'task' );

		if ($task == 'copy') {
			JRequest::setVar( 'task', $task );
		} else {
			$model->checkout();
		}
		parent::display();	
	}

	/**
	 * logic to save an event
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function save()
	{
		global $option;

		$task		= JRequest::getVar('task');

		$post = JRequest::get( 'post' );
		$post['datdescription'] = JRequest::getVar( 'datdescription', '', 'post','string', JREQUEST_ALLOWRAW );

		$model = $this->getModel('event');
		
		if ($returnid = $model->store($post)) {
			
			switch ($task)
			{
				case 'apply' :
					$link = 'index.php?option='.$option.'&controller=events&view=event&hidemainmenu=1&cid[]='.$returnid;
					break;
				
				default :
					$link = 'index.php?option='.$option.'&view=events';
					break;
			}
			$msg	= JText::_( 'EVENT SAVED');
			
		} else {
			
			$link = 'index.php?option='.$option.'&view=events';
			$msg 	= $model->getError();
		}

		$model->checkin();
		
		$this->setRedirect( $link, $msg );
 	}

	/**
	 * logic to remove an event
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
			echo "<script> alert('". JText::_( 'Select an item to delete' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}

		$model = $this->getModel('events');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$msg = $total.' '.JText::_( 'EVENTS DELETED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=events', $msg );
	}
}
?>