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
 * EventList Component Categories Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerCategories extends EventListController
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
		$this->registerTask( 'add'  ,		 	'edit' );
		$this->registerTask( 'apply', 			'save' );
		$this->registerTask( 'accesspublic', 	'access' );
		$this->registerTask( 'accessregistered','access' );
		$this->registerTask( 'accessspecial', 	'access' );
	}

	/**
	 * Logic to save a category
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function save()
	{
		global $option, $mainframe;
		
		$task		= JRequest::getVar('task');

		//Sanitize
		$post = JRequest::get( 'post' );
		$post['catdescription'] = JRequest::getVar( 'catdescription', '', 'post', 'string', JREQUEST_ALLOWRAW );

		$model = $this->getModel('category');

		if ($returnid = $model->store($post)) {

			switch ($task)
			{
				case 'apply' :
					$link = 'index.php?option='.$option.'&view=category&cid[]='.$returnid;
					break;

				default :
					$link = 'index.php?option='.$option.'&view=categories';
					break;
			}
			$msg = JText::_( 'CATEGORY SAVED' );
			
		} else {

			$msg 	= $model->getError();
			$link 	= 'index.php?option='.$option.'&view=category';
		}
		$this->setRedirect($link, $msg );
	}

	/**
	 * Logic to publish categories
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

		$model = $this->getModel('categories');
		
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}
		
		$total = count( $cid );
		$msg 	= $total.' '.JText::_( 'CATEGORY PUBLISHED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=categories', $msg );
	}

	/**
	 * Logic to unpublish categories
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

		$model = $this->getModel('categories');
		
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_( 'CATEGORY UNPUBLISHED');

		$this->setRedirect( 'index.php?option=com_eventlist&view=categories', $msg );
	}

	/**
	 * Logic to orderup a category
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function orderup()
	{
		$model = $this->getModel('categories');
		$model->move(-1);

		$this->setRedirect( 'index.php?option=com_eventlist&view=categories');
	}

	/**
	 * Logic to orderdown a category
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function orderdown()
	{
		$model = $this->getModel('categories');
		$model->move(1);

		$this->setRedirect( 'index.php?option=com_eventlist&view=categories');
	}

	/**
	 * Logic to mass ordering categories
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function saveorder()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(0), 'post', 'array' );

		$model = $this->getModel('categories');
		$model->saveorder($cid, $order);

		$msg = 'New ordering saved';
		$this->setRedirect( 'index.php?option=com_com_eventlist&view=categories', $msg );
	}

	/**
	 * Logic to delete categories
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function remove()
	{
		global $option;

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('". JText::_( 'Select an item to delete' ) ."'); window.history.go(-1);</script>\n";
			exit;
		}
		
		$model = $this->getModel('categories');
		
		$msg = $model->delete($cid);

		$this->setRedirect( 'index.php?option='. $option .'&view=categories', $msg );
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

		$model = $this->getModel('category');

		$model->checkin();

		$this->setRedirect( 'index.php?option='.$option.'&view=categories' );
	}

	/**
	 * Logic to set the category access level
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function access( )
	{
		global $option;

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$id			= $cid[0];
		$task		= JRequest::getVar( 'task' );

		if ($task == 'accesspublic') {
			$access = 0;
		} elseif ($task == 'accessregistered') {
			$access = 1;
		} else {
			$access = 2;
		}
		
		$model = $this->getModel('category');
		$model->access( $id, $access );

		$this->setRedirect('index.php?option='. $option .'&view=categories' );
	}

	/**
	 * Logic to create the view for the edit categoryscreen
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function edit( )
	{		
		JRequest::setVar( 'view', 'category' );
		JRequest::setVar( 'hidemainmenu', 1 );
		
		parent::display();	
	
		$model = $this->getModel('category');
		
		/* Error if checkedout by another administrator
		if ($model->isCheckedOut( $user->get('id') )) {
			$this->setRedirect( 'index.php?option='.$option.'&task=categories', JText::_( 'THE CATEGORY' ).' '.$row->catname.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ) );
		}
		
		*/
		
		$model->checkout();
	}
}