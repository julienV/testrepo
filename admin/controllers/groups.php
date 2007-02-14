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
 * EventList Component Groups Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerGroups extends EventListController
{
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
	 * logic for cancel an action
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function cancel()
	{
		global $option;

		$model = $this->getModel('group');

		$model->checkin();

		$this->setRedirect( 'index.php?option='.$option.'&view=groups' );
	}

	/**
	 * logic to create the new event screen
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function newgroup( )
	{
		global $option;

		$this->setRedirect( 'index.php?option='. $option .'&view=group' );
	}

	/**
	 * logic to create the edit event screen
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function editgroup( )
	{
		global $option;

		$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
		$id		= $cid[0];
		
		JRequest::setVar( 'view', 'group' );
		JRequest::setVar( 'hidemainmenu', 1 );
			
		$model = $this->getModel('group');
		
		/* Error if checkedout by another administrator
		if ($model->isCheckedOut( $user->get('id') )) {
			$this->setRedirect( 'index.php?option='.$option.'&task=categories', JText::_( 'THE CATEGORY' ).' '.$row->catname.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ) );
		}
		
		*/

		$model->checkout();

		parent::display();	
	}

	/**
	 * logic to save an event
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function savegroup()
	{
		global $option;

		$post = JRequest::get( 'post' );

		$model = $this->getModel('group');

		$model->store($post);

		$link = 'index.php?option='.$option.'&view=groups';

		$this->setRedirect( $link, JText::_( 'GROUP SAVED') );
 	}

	/**
	 * logic to remove a group
	 * 
	 * @access public
	 * @return void
	 * @since 0.9
	 */
 	function removegroup()
	{
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$total = count( $cid );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('". JText::_( 'Select an item to delete' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}

		$model = $this->getModel('groups');
		
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$msg = $total.' '.JText::_( 'GROUPS DELETED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=groups', $msg );
	}
}
?>