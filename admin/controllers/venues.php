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
 * EventList Component Venues Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerVenues extends EventListController
{
	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask( 'add', 		'edit' );
		$this->registerTask( 'apply', 		'save' );
	}

	/**
	 * Logic to publish venues
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

		$model = $this->getModel('venues');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('VENUE PUBLISHED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=venues', $msg );
	}

	/**
	 * Logic to unpublish venues
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

		$model = $this->getModel('venues');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('VENUE UNPUBLISHED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=venues', $msg );
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

		$model = $this->getModel('venue');

		$model->checkin();

		$this->setRedirect( 'index.php?option='.$option.'&view=venues' );
	}

	/**
	 * logic for remove venues
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function remove()
	{
		global $option;

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('". JText::_( 'Select an item to delete' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}

		$model = $this->getModel('venues');

		$msg = $model->delete($cid);

		$this->setRedirect( 'index.php?option=com_eventlist&view=venues', $msg );
	}

	/**
	 * logic to orderup a venue
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function orderup()
	{
		$model = $this->getModel('venues');
		$model->move(-1);

		$this->setRedirect( 'index.php?option=com_eventlist&view=venues');
	}

	/**
	 * logic to orderdown a venue
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function orderdown()
	{
		$model = $this->getModel('venues');
		$model->move(1);

		$this->setRedirect( 'index.php?option=com_eventlist&view=venues');
	}

	/**
	 * logic to create the edit venue view
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function edit( )
	{
		JRequest::setVar( 'view', 'venue' );
		JRequest::setVar( 'hidemainmenu', 1 );

		parent::display();

		$model = $this->getModel('venue');

		$model->checkout();
	}

	/**
	 * saves the venue in the database
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function save()
	{
		global $option, $mainframe;

		$task		= JRequest::getVar('task');

		// Sanitize
		$post = JRequest::get( 'post' );
		$post['locdescription'] = JRequest::getVar( 'locdescription', '', 'post', 'string', JREQUEST_ALLOWRAW );


		$model = $this->getModel('venue');

		if ($returnid = $model->store($post)) {
			
			switch ($task)
			{
				case 'apply':
					$link = 'index.php?option='.$option.'&view=venue&hidemainmenu=1&cid[]='.$returnid;
					break;

				default:
					$link = 'index.php?option='.$option.'&view=venues';
					break;
			}
			$msg	= JText::_( 'VENUE SAVED');
			
		} else {
			
			$link 	= 'index.php?option='.$option.'&view=venue';
			$msg 	= $model->getError();
		}
		
		$model->checkin();
		
		$this->setRedirect( $link, $msg );
	}
}
?>